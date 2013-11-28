<?php

/**
 * ajax_appreciations.php
 * Fichier qui permet la sauvegarde automatique des appréciations au fur et à mesure de leur saisie
 * Ajout du contrôle des lapsus sur les appréciations et avis des conseils de classe
 *
 * @copyright 2007-2013
 */

// ============== Initialisation ===================
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//echo "A";

function log_ajax_app($texte) {
	$debug="n";
	$fichier="/tmp/log_ajax_app.txt";
	if($debug=="y") {
		$ladate=strftime("%b %d %H:%M:%S");
		$f=fopen($fichier,"a+");
		fwrite($f,$ladate." : ".$texte."\n");
		fclose($f);
	}
}

// Sécurité
if (!checkAccess()) {
	log_ajax_app("Echec checkAccess().");
	header("Location: ../logout.php?auto=2");
	die();
}

//echo "B";

if(isset($_SESSION['login'])) {
	log_ajax_app($_SESSION['login']." (".$_SESSION['statut'].").");
}

// Le check_token doit être à false parce qu'il va se produire sans charger une nouvelle page, avec un header HTML déjà transmis
check_token(false);

log_ajax_app("Test check_token() depasse.");

//echo "C";

header('Content-Type: text/html; charset=utf-8');

// Initialisation des variables
$var1 = isset($_POST["var1"]) ? $_POST["var1"] : (isset($_GET["var1"]) ? $_GET["var1"] : NULL);
$var2 = isset($_POST["var2"]) ? $_POST["var2"] : (isset($_GET["var2"]) ? $_GET["var2"] : NULL);
$appreciation = isset($_POST["var3"]) ? $_POST["var3"] : (isset($_GET["var3"]) ? $_GET["var3"] : NULL);
$professeur = isset($_SESSION["statut"]) ? $_SESSION["statut"] : NULL;

$mode=isset($_POST['mode']) ? $_POST['mode'] : "";

// ========== Fin de l'initialisation de la page =============

// On détermine si les variables envoyées sont bonnes ou pas
$verif_var1 = explode("_t", $var1);

// On vérifie que le login de l'élève soit valable et qu'il corresponde à l'enseignement envoyé par var2
$temoin_eleve=0;
if($_SESSION['statut']=='professeur') {
	$sql="SELECT login FROM j_eleves_groupes
			WHERE login = '".$verif_var1[0]."'
			AND id_groupe = '".$var2."'
			AND periode = '".$verif_var1[1]."'";
	log_ajax_app("$sql");
	$verif_eleve = mysql_query($sql)
			or die('Erreur de verif_var1 : '.mysql_error());
	log_ajax_app("Test passe.");
	$temoin_eleve=mysql_num_rows($verif_eleve);

	// On vérifie que le prof logué peut saisir ces appréciations
	//$verif_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe = '".$var2."'");
	//if($mode!="verif") {
	if($mode!="verif_avis") {
		// On ne vient pas de la page de saisie d'avis du conseil de classe
		$verif_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe = '".$var2."' AND login='".$_SESSION['login']."'");
		if (mysql_num_rows($verif_prof) >= 1) {
			// On ne fait rien
			$temoin_prof=mysql_num_rows($verif_prof);
		} else {
			log_ajax_app("Vous ne pouvez pas saisir d'appreciations pour cet eleve");
			die('Vous ne pouvez pas saisir d\'appr&eacute;ciations pour cet &eacute;l&egrave;ve');
		}
	}
	else {
		// On vient de la page de saisie d'avis du conseil de classe
		$sql="SELECT login FROM j_eleves_professeurs WHERE login = '".$verif_var1[0]."' AND id_classe='".$var2."' AND professeur='".$_SESSION['login']."'";
		//echo "$sql<br />";
		$verif_prof = mysql_query($sql);
		if (mysql_num_rows($verif_prof) >= 1) {
			// On ne fait rien
			$temoin_prof=mysql_num_rows($verif_prof);
			$temoin_eleve=1;
		} else {
			log_ajax_app("Vous ne pouvez pas saisir d'avis pour cet eleve");
			die('Vous ne pouvez pas saisir d\'avis pour cet &eacute;l&egrave;ve');
		}
	}

	/*
	}
	else {
		// On économise une requête quand il s'agit juste de vérifier les lapsus?
		$temoin_prof=1;
	}
	*/
}

//echo "\$temoin_eleve=$temoin_eleve<br />";
//echo "\$temoin_prof=$temoin_prof<br />";

if (($_SESSION['statut']=='scolarite') || ($_SESSION['statut']=='secours') || ($_SESSION['statut']=='cpe') || (($temoin_eleve !== 0 AND $temoin_prof !== 0))) {
	// Si on a passé mode=verif, c'est un test des lapsus.
	// Il ne faut pas mettre à jour matieres_appreciations_tempo sans quoi, au chargement de saisie_appreciations.php, en testant les lapsus, on va aussi remettre les anciennes valeurs (vide si on n'avait rien enregistré auparavant ou une appréciation antérieure)
	if(($mode!="verif_avis")&&($mode!="verif")) {
		// On ne vient pas de la page de saisie d'avis du conseil de classe
		// On va enregistrer les appréciations temporaires

		if($_SESSION['statut']=='professeur') {
			// Enregistrement ou pas de l'appréciation temporaire:
			$insertion_ou_maj_tempo="y";
			$sql="SELECT appreciation FROM matieres_appreciations WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."';";
			log_ajax_app($sql);
			$test_app_enregistree=mysql_query($sql);
			if(mysql_num_rows($test_app_enregistree)>0) {
				$lig_app_enregistree=mysql_fetch_object($test_app_enregistree);
				if($lig_app_enregistree->appreciation==$appreciation) {
					// On supprime l'enregistrement tempo pour éviter de conserver un tempo qui est déjà enregistré dans la table principale.
					$sql="DELETE FROM matieres_appreciations_tempo WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."';";
					log_ajax_app($sql);
					$menage=mysql_query($sql);
					$insertion_ou_maj_tempo="n";
				}
			}
	
			if($insertion_ou_maj_tempo=="y") {
				// On vérifie si cette appréciation existe déjà ou non
				$verif_appreciation = mysql_query("SELECT appreciation FROM matieres_appreciations_tempo WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'");
				// Si elle existe, on la met à jour
				if (mysql_num_rows($verif_appreciation) == 1) {
					$sql="UPDATE matieres_appreciations_tempo SET appreciation = '".$appreciation."' WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'";
					log_ajax_app($sql);
					$miseajour = mysql_query($sql);
				} else {
					//sinon on crée une nouvelle appréciation si l'appréciation n'est pas vide
					if ($appreciation != "") {
						$sql="INSERT INTO matieres_appreciations_tempo SET login = '".$verif_var1[0]."', id_groupe = '".$var2."', periode = '".$verif_var1[1]."', appreciation = '".$appreciation."'";
						log_ajax_app($sql);
						$sauvegarde = mysql_query($sql);
					}
				}
			}
			// et on renvoie une réponse valide
			//header("HTTP/1.0 200 OK");
			//echo ' ';
		}
	}

	if(getSettingValue('active_recherche_lapsus')=='n') {
		// on renvoie une réponse valide
		header("HTTP/1.0 200 OK");
		echo ' ';
	}
	else {
		// Vérification des fautes de frappe/lapsus que l'on saisisse une appréciation ou un avis du conseil de classe
		//if($mode=='verif') {
			$sql="CREATE TABLE IF NOT EXISTS vocabulaire (id INT(11) NOT NULL auto_increment,
				terme VARCHAR(255) NOT NULL DEFAULT '',
				terme_corrige VARCHAR(255) NOT NULL DEFAULT '',
				PRIMARY KEY (id)
				) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			//log_ajax_app($sql);
			$create_table=mysql_query($sql);
			if(!$create_table) {
				echo "<span style='color:red'>Erreur lors de la création de la table 'vocabulaire'.</span>";
			}
			else {
				$sql="SELECT * FROM vocabulaire;";
				//echo "$sql<br />";
				//log_ajax_app($sql);
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig_voc=mysql_fetch_object($res)) {
						$tab_voc[]=$lig_voc->terme;
						$tab_voc_corrige[]=$lig_voc->terme_corrige;
						//log_ajax_app("Tableau des corrections possibles : ".$lig_voc->terme." -> ".$lig_voc->terme_corrige);
					}

					/*
					$tab_tmp=explode(" ",preg_replace("//"," ",$appreciation);
					for($loop=0;$loop<count($tab_tmp);$loop++) {
					
					}
					*/
					$appreciation_test=" ".preg_replace("/[',;\.]/"," ",casse_mot($appreciation,'min'))." ";
					//echo "$appreciation_test<br />";
					$chaine_retour="";
					for($loop=0;$loop<count($tab_voc);$loop++) {
						if(preg_match("/ ".$tab_voc[$loop]." /i",$appreciation_test)) {
							if($chaine_retour=="") {$chaine_retour.="<span style='font-weight:bold'>Suspicion de faute de frappe&nbsp;: </span>";}
							$chaine_retour.=$tab_voc[$loop]." / ".$tab_voc_corrige[$loop]."<br />";
							//log_ajax_app("Suspicion de faute de frappe : ".$tab_voc[$loop]." / ".$tab_voc_corrige[$loop]);
						}
					}

					if($chaine_retour!="") {
						echo $chaine_retour;
						//log_ajax_app("\$chaine_retour=".$chaine_retour);
					}
					else {
						// et on renvoie une réponse valide
						header("HTTP/1.0 200 OK");
						echo ' ';
					}
				}
				else {
					// et on renvoie une réponse valide
					header("HTTP/1.0 200 OK");
					echo ' ';
				}
			}
		//}
	}
}
else {
	// et on renvoie une réponse valide
	header("HTTP/1.0 200 OK");
	echo ' ';
}
?>
