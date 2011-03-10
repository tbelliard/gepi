<?php

/**
 * ajax_appreciations.php
 * Fichier qui permet la sauvegarde automatique des appréciations au fur et à mesure de leur saisie
 *
 * @version $Id$
 * @copyright 2007-2011
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

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

check_token();

header('Content-Type: text/html; charset=ISO-8859-1');

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
$verif_eleve = mysql_query("SELECT login FROM j_eleves_groupes
		WHERE login = '".$verif_var1[0]."'
		AND id_groupe = '".$var2."'
		AND periode = '".$verif_var1[1]."'")
		or die('Erreur de verif_var1 : '.mysql_error());

// On vérifie que le prof logué peut saisir ces appréciations
//$verif_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe = '".$var2."'");
$verif_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe = '".$var2."' AND login='".$_SESSION['login']."'");
if (mysql_num_rows($verif_prof) >= 1) {
	// On ne fait rien
} else {
	die('Vous ne pouvez pas saisir d\'appr&eacute;ciations pour cet &eacute;l&eagrave;ve');
}

if (mysql_num_rows($verif_eleve) !== 0 AND mysql_num_rows($verif_prof) !== 0) {
	if($mode=='verif') {
		$sql="CREATE TABLE IF NOT EXISTS vocabulaire (id INT(11) NOT NULL auto_increment,
			terme VARCHAR(255) NOT NULL DEFAULT '',
			terme_corrige VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY (id)) ENGINE=MyISAM;";
		$create_table=mysql_query($sql);
		if(!$create_table) {
			echo "<span style='color:red'>Erreur lors de la création de la table 'vocabulaire'.</span>";
		}
		else {
			$sql="SELECT * FROM vocabulaire;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				while($lig_voc=mysql_fetch_object($res)) {
					$tab_voc[]=$lig_voc->terme;
					$tab_voc_corrige[]=$lig_voc->terme_corrige;
				}

				/*
				$tab_tmp=explode(" ",preg_replace("//"," ",$appreciation);
				for($loop=0;$loop<count($tab_tmp);$loop++) {
					
				}
				*/
				$appreciation_test=" ".preg_replace("/[;\.]/"," ",$appreciation)." ";
				$chaine_retour="";
				for($loop=0;$loop<count($tab_voc);$loop++) {
					if(preg_match("/".$tab_voc[$loop]."/i",$appreciation_test)) {
						if($chaine_retour=="") {$chaine_retour.="<span style='font-weight:bold'>Suspicion de faute de frappe&nbsp;: </span>";}
						$chaine_retour.=$tab_voc[$loop]." / ".$tab_voc_corrige[$loop]."<br />";
					}
				}
				if($chaine_retour!="") {
					echo $chaine_retour;
				}
			}
		}
	}
	else {
		$insertion_ou_maj_tempo="y";
		$sql="SELECT appreciation FROM matieres_appreciations WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."';";
		$test_app_enregistree=mysql_query($sql);
		if(mysql_num_rows($test_app_enregistree)>0) {
			$lig_app_enregistree=mysql_fetch_object($test_app_enregistree);
			if($lig_app_enregistree->appreciation==utf8_decode($appreciation)) {
				// On supprime l'enregistrement tempo pour éviter de conserver un tempo qui est déjà enregistré dans la table principale.
				$sql="DELETE FROM matieres_appreciations_tempo WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."';";
				$menage=mysql_query($sql);
				$insertion_ou_maj_tempo="n";
			}
		}
	
		if($insertion_ou_maj_tempo=="y") {
			// On vérifie si cette appréciation existe déjà ou non
			$verif_appreciation = mysql_query("SELECT appreciation FROM matieres_appreciations_tempo WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'");
			// Si elle existe, on la met à jour
			if (mysql_num_rows($verif_appreciation) == 1) {
				$miseajour = mysql_query("UPDATE matieres_appreciations_tempo SET appreciation = '".utf8_decode($appreciation)."' WHERE login = '".$verif_var1[0]."' AND id_groupe = '".$var2."' AND periode = '".$verif_var1[1]."'");
			} else {
				//sinon on crée une nouvelle appréciation si l'appréciation n'est pas vide
				if ($appreciation != "") {
					$sauvegarde = mysql_query("INSERT INTO matieres_appreciations_tempo SET login = '".$verif_var1[0]."', id_groupe = '".$var2."', periode = '".$verif_var1[1]."', appreciation = '".utf8_decode($appreciation)."'");
				}
			}
		}
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
?>