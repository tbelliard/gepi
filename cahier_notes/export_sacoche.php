<?php
/**
* @copyright Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

@set_time_limit(0);

/**
 * Fichiers d'initialisation
 */
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/export_sacoche.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_notes/export_sacoche.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export SACoche',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

//debug_var();

$msg="";

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
if ((!isset($id_groupe))||(!preg_match('/^[0-9]{1,}$/', $id_groupe))) {
	header("Location: ./index.php?msg=Numéro de groupe invalide");
	die();
}

if (is_numeric($id_groupe) && $id_groupe > 0) {

	$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='".$_SESSION['login']."';";
	$test_prof_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_prof_groupe)==0) {
		$mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
		unset($_SESSION['id_groupe_session']);
		tentative_intrusion(1, "Tentative d'accès à un carnet de notes qui ne lui appartient pas (id_groupe=$id_groupe)");
		// Sans le unset($_SESSION['id_groupe_session']) avec ces tentative_intrusion(), une modif d'id_groupe en barre d'adresse me provoquait 7 insertions... d'où un score à +7 et une déconnexion
		header("Location: index.php?msg=$mess");
		die();
	}

	$current_group = get_group($id_groupe);
}

if(!isset($current_group['name'])) {
	header("Location: ./index.php?msg=Groupe invalide");
	die();
}


$periode_num = isset($_POST["periode_num"]) ? $_POST["periode_num"] : (isset($_GET["periode_num"]) ? $_GET["periode_num"] : NULL);
if ((!isset($periode_num))||(!preg_match('/^[0-9]{1,}$/', $periode_num))) {
	header("Location: ./index.php?msg=Numéro de période invalide");
	die();
}


if ((isset($_GET['export']))&&($_GET['export']=='csv')) {

	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num');";
	//echo "$sql<br />";
	$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
	if ($nb_cahier_note == 0) {
		header("Location: ./index.php?msg=Le carnet de notes n'est pas initialisé pour cet enseignement sur cette période.");
		die();
	}

	$lig=mysqli_fetch_object($appel_cahier_notes);
	$id_racine = $lig->id_cahier_notes;

	// Récupérer les devoirs de la période
	$tab_devoirs=array();
	$sql="SELECT * FROM cn_devoirs WHERE id_racine='".$id_racine."' AND display_parents='1' ORDER BY id_conteneur, date, nom_court, nom_complet;";
	//echo "$sql<br />";
	$res=mysqli_query($mysqli, $sql);
	$nb_devoirs=mysqli_num_rows($res);
	if($nb_devoirs>0) {
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='".$lig->id."' AND statut!='v' ORDER BY login;";
			//echo "$sql<br />";
			$res2=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					$tab_devoirs[$lig2->login][$lig->id]['nom_court']=$lig->nom_court;
					$tab_devoirs[$lig2->login][$lig->id]['date']=formate_date($lig->date);
					$tab_devoirs[$lig2->login][$lig->id]['coef']=$lig->coef;
					$tab_devoirs[$lig2->login][$lig->id]['note_sur']=$lig->note_sur;
					if($lig2->statut=='') {
						$tab_devoirs[$lig2->login][$lig->id]['note_ou_statut']=$lig2->note;
					}
					else {
						$tab_devoirs[$lig2->login][$lig->id]['note_ou_statut']=$lig2->statut;
					}
				}
			}
		}
	}

	// Pour contourner un pb d'analyse SACoche du délimiteurs de champs du CSV:
	$chaine_separateur='';
	for($i=0;$i<$nb_devoirs;$i++) {
		$chaine_separateur.=';';
	}

	$csv='';
	foreach($current_group["classes"]["list"] as $current_id_classe) {
		$sql= "SELECT DISTINCT jeg.login, e.id_sacoche, e.nom, e.prenom FROM eleves e, 
					j_eleves_groupes jeg, 
					j_eleves_classes jec 
				WHERE (e.login=jeg.login AND 
					e.id_sacoche!='0' AND 
					jeg.login=jec.login AND 
					jec.periode=jeg.periode AND 
					jec.periode='".$periode_num."' AND 
					jeg.id_groupe='".$id_groupe."' AND 
					jec.id_classe='".$current_id_classe."') 
				ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$csv.='eleve_'.$lig->id_sacoche.';"'.$lig->prenom.' '.$lig->nom.'";;"';

				if(isset($tab_devoirs[$lig->login])) {
					$sql="SELECT * FROM cn_notes_conteneurs WHERE id_conteneur='".$id_racine."' AND login='".$lig->login."' AND statut='y';";
					//echo "$sql<br />";
					$res3=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res3)>0) {
						$lig3=mysqli_fetch_object($res3);
						$csv.="Moyenne : ".$lig3->note.", ";
						//$csv.="Moyenne  ".$lig3->note.", ";
					}

					$cpt=0;
					foreach($tab_devoirs[$lig->login] as $id_devoir => $current_devoir) {
						if($cpt>0) {
							$csv.=", ";
						}

						$csv.=$current_devoir['nom_court']." : ";
						$csv.=$current_devoir['note_ou_statut'];
						if($current_devoir['note_sur']!=20) {
							$csv.="/".$current_devoir['note_sur'];
						}
						if($current_devoir['coef']!=1) {
							$csv.=" (".($current_devoir['coef']+1-1).")";
						}

						$cpt++;
					}
				}

				$csv.=$chaine_separateur;
				$csv.="\"\n";
			}
		}

	}

	/*
	echo "<pre>";
	echo $csv;
	echo "</pre>";
	*/

	$nom_fic="export_notes_pour_appreciations_SACoche_".remplace_accents(get_info_grp($id_groupe, array('matieres', 'classes'), ''), 'all')."_periode_".$periode_num."_".strftime("%Y%m%d_%H%M%S").".csv";

	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);

	die();
}

if(isset($_POST['envoi_csv'])) {
	check_token();

	$msg="ENVOI CSV effectué<br />";
	//debug_var();

	$csv_file = isset($_FILES["fichier_csv_sacoche"]) ? $_FILES["fichier_csv_sacoche"] : NULL;

	if(!isset($csv_file['tmp_name'])) {
		$msg="Le fichier n'a pas été uploadé.<br />";
	}
	elseif(!is_uploaded_file($csv_file['tmp_name'])) {
		$msg="L'upload du fichier a échoué.<br />";
	}
	elseif(!file_exists($csv_file['tmp_name'])){
		$msg="Le fichier aurait été uploadé mais pas conservé ???<br />";
	}
	else {
		//$motifs=array('bulletin_', 'rubrique_', 'groupe_');
		$csv='';
		$fich=fopen($csv_file['tmp_name'], 'r');
		while (!feof($fich)) {
			$ligne=fgets($fich, 4096);
			//if(trim($ligne)!="") {
				//echo $ligne."<br />\n";
				if($ligne=='') {
					//echo "1<br />";
					$csv.="\n";
				}
				//elseif(preg_match("/(^bulletin_)(^rubrique_)(^groupe_)/", $ligne)) {
				elseif((preg_match("/^bulletin_/", $ligne))||
				(preg_match("/^rubrique_/", $ligne))||
				(preg_match("/^groupe_/", $ligne))) {
					//echo "2<br />";
					//$csv.=$ligne."\n";
					$csv.=$ligne;
				}
			//}
		}
		fclose($fich);


		$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num');";
		//echo "$sql<br />";
		$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
		if ($nb_cahier_note == 0) {
			header("Location: ./index.php?msg=Le carnet de notes n'est pas initialisé pour cet enseignement sur cette période.");
			die();
		}

		$lig=mysqli_fetch_object($appel_cahier_notes);
		$id_racine = $lig->id_cahier_notes;

		// Récupérer les devoirs de la période
		$tab_devoirs=array();
		$sql="SELECT * FROM cn_devoirs WHERE id_racine='".$id_racine."' AND display_parents='1' ORDER BY id_conteneur, date, nom_court, nom_complet;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		$nb_devoirs=mysqli_num_rows($res);
		if($nb_devoirs>0) {
			while($lig=mysqli_fetch_object($res)) {
				$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='".$lig->id."' AND statut!='v' ORDER BY login;";
				//echo "$sql<br />";
				$res2=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res2)>0) {
					while($lig2=mysqli_fetch_object($res2)) {
						$tab_devoirs[$lig2->login][$lig->id]['nom_court']=$lig->nom_court;
						$tab_devoirs[$lig2->login][$lig->id]['date']=formate_date($lig->date);
						$tab_devoirs[$lig2->login][$lig->id]['coef']=$lig->coef;
						$tab_devoirs[$lig2->login][$lig->id]['note_sur']=$lig->note_sur;
						if($lig2->statut=='') {
							$tab_devoirs[$lig2->login][$lig->id]['note_ou_statut']=$lig2->note;
						}
						else {
							$tab_devoirs[$lig2->login][$lig->id]['note_ou_statut']=$lig2->statut;
						}
					}
				}
			}
		}

		// Pour contourner un pb d'analyse SACoche du délimiteurs de champs du CSV:
		$chaine_separateur='';
		for($i=0;$i<$nb_devoirs;$i++) {
			$chaine_separateur.=';';
		}

		//$csv='';
		foreach($current_group["classes"]["list"] as $current_id_classe) {
			$sql= "SELECT DISTINCT jeg.login, e.id_sacoche, e.nom, e.prenom FROM eleves e, 
						j_eleves_groupes jeg, 
						j_eleves_classes jec 
					WHERE (e.login=jeg.login AND 
						e.id_sacoche!='0' AND 
						jeg.login=jec.login AND 
						jec.periode=jeg.periode AND 
						jec.periode='".$periode_num."' AND 
						jeg.id_groupe='".$id_groupe."' AND 
						jec.id_classe='".$current_id_classe."') 
					ORDER BY e.nom, e.prenom;";
			//echo "$sql<br />";
			$res=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res)>0) {
				while($lig=mysqli_fetch_object($res)) {
					//$csv.='eleve_'.$lig->id_sacoche.';"'.$lig->prenom.' '.$lig->nom.'";;"';
					$ligne_csv_courante='eleve_'.$lig->id_sacoche.';"'.$lig->prenom.' '.$lig->nom.'";;"';

					if(isset($tab_devoirs[$lig->login])) {
						$sql="SELECT * FROM cn_notes_conteneurs WHERE id_conteneur='".$id_racine."' AND login='".$lig->login."' AND statut='y';";
						//echo "$sql<br />";
						$res3=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res3)>0) {
							$lig3=mysqli_fetch_object($res3);
							//$csv.="Moyenne : ".$lig3->note.", ";
							$ligne_csv_courante.="Moyenne : ".$lig3->note.", ";
							//$csv.="Moyenne  ".$lig3->note.", ";
						}

						$cpt=0;
						foreach($tab_devoirs[$lig->login] as $id_devoir => $current_devoir) {
							if($cpt>0) {
								//$csv.=", ";
								$ligne_csv_courante.=", ";
							}

							//$csv.=$current_devoir['nom_court']." : ";
							$ligne_csv_courante.=$current_devoir['nom_court']." : ";
							//$csv.=$current_devoir['note_ou_statut'];
							$ligne_csv_courante.=$current_devoir['note_ou_statut'];
							if($current_devoir['note_sur']!=20) {
								//$csv.="/".$current_devoir['note_sur'];
								$ligne_csv_courante.="/".$current_devoir['note_sur'];
							}
							if($current_devoir['coef']!=1) {
								//$csv.=" (".($current_devoir['coef']+1-1).")";
								$ligne_csv_courante.=" (".($current_devoir['coef']+1-1).")";
							}

							$cpt++;
						}
					}

					//$csv.=$ligne_csv_courante;
					$csv.=mb_convert_encoding($ligne_csv_courante, "ISO-8859-1", "utf-8");
					$csv.=$chaine_separateur;
					$csv.="\"\n";
				}
			}

		}

		/*
		echo "<pre>";
		echo $csv;
		echo "</pre>";
		*/

		$nom_fic="export_notes_pour_appreciations_SACoche_".remplace_accents(get_info_grp($id_groupe, array('matieres', 'classes'), ''), 'all')."_periode_".$periode_num."_".strftime("%Y%m%d_%H%M%S").".csv";

		send_file_download_headers('text/x-csv',$nom_fic);
		//echo echo_csv_encoded($csv);
		// Il y a un bazar entre le fichier ISO8859-1 de l'export SACoche et le fichier traité en UTF8 dans Gepi.
		echo $csv;

		die();

	}
}


//**************** EN-TETE *****************
$titre_page = "Carnet de notes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

echo "<div class='norme'>\n";
echo "<form enctype=\"multipart/form-data\" id= \"form1\" name= \"form1\" action=\"".$_SERVER['PHP_SELF']."\" method=\"get\">\n";
echo "<p class='bold'>\n";
echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a> | \n";
echo "<a href='index.php?id_groupe=$id_groupe&periode_num=$periode_num'> ".$current_group['name']." (".$current_group['classlist_string'].")"." </a> | ";
echo "<a href='index.php?id_groupe=no_group'> Mes enseignements </a>";
//echo " | \n";

// Mettre de quoi passer au groupe suivant pour lequel on a des identifiants SACoche

echo "</form>
</div>

<h2>Export des notes pour SACoche</h2>\n";



$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], "SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
$nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
if ($nb_cahier_note == 0) {
	echo "<p>Le carnet de notes n'est pas initialisé pour cet enseignement sur cette période.</p>";
	require("../lib/footer.inc.php");
	die();
}

$lig=mysqli_fetch_object($appel_cahier_notes);
$id_racine = $lig->id_cahier_notes;


// Proposer l'export selon divers modes :
// - notes seules
// - avec intitulé
// - avec coefs
// - avec dates
// - avec moyenne CN
// Et placer la moyenne dans l'appréciation ou dans la colonne pourcentages

echo "<p>SACoche permet la saisie déportée des appréciations pour les bulletins trimestriels.<br />
La présente page permet de renseigner ces appréciations avec les notes saisies dans le carnet de notes de Gepi.</p>

<br />

<p><a href='".$_SERVER['PHP_SELF']."?id_groupe=".$id_groupe."&periode_num=".$periode_num."&export=csv' target='_blank' style='font-weight:bold'>Exporter simplement les notes et la moyenne</a><br />
<br />
Ou<br />
&nbsp;</p>

<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' id='form_envoi_csv' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<!--
		<p>Exporter les informations suivantes&nbsp;:<br />
		<input type='checkbox' name='moyenne' id='moyenne' value='y' onchange='checkbox_change(this.id)' /><label for='moyenne' id='texte_moyenne'>la moyenne</label><br />
		<input type='checkbox' name='nom_court_devoir' id='nom_court_devoir' value='y' onchange='checkbox_change(this.id)' /><label for='nom_court_devoir' id='texte_nom_court_devoir'>le nom court de l'évaluation</label><br />
		<input type='checkbox' name='note_devoir' id='note_devoir' value='y' onchange='checkbox_change(this.id)' /><label for='note_devoir' id='texte_note_devoir'>la note obtenue à chaque évaluation</label><br />
		<input type='checkbox' name='coef_devoir' id='coef_devoir' value='y' onchange='checkbox_change(this.id)' /><label for='coef_devoir' id='texte_coef_devoir'></label><br />
		</p>
		-->

		<p>Envoyer le fichier modèle CSV fourni par SACoche pour cet enseignement/période de façon à générer un fichier CSV à retourner vers SACoche sans intervention manuelle dans le fichier.<br />
		Fichier CSV SACoche&nbsp;: <input type='file' name='fichier_csv_sacoche' id='input_csv_file' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); padding:5px; margin:5px;' /><br />
		</p>

		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='periode_num' value='$periode_num' />
		<input type='hidden' name='envoi_csv' value='y' />

		<p>
		<input type='submit' id='input_submit' value='Valider' />
		<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" />
		</p>

		<script type='text/javascript'>
			document.getElementById('input_submit').style.display='none';
			document.getElementById('input_button').style.display='';

			function check_champ_file() {
				fichier=document.getElementById('input_csv_file').value;
				//alert(fichier);
				if(fichier=='') {
					alert('Vous n\'avez pas sélectionné de fichier CSV à envoyer.');
				}
				else {
					document.getElementById('form_envoi_csv').submit();
				}
			}
		</script>
	</fieldset>
</form>

<p style='margin-top:1em;'><em>NOTE&nbsp;:</em></p>
<div style='margin-left:3em;padding:1em;' class='fieldset_opacite50'>
	<p>Dans le cas où vous ne fournissez pas dans le formulaire ci-dessus le fichier modèle de SACoche, vous devrez incorporer manuellement les lignes extraites de Gepi dans le fichier de SACoche.<br />
	Voici quelques explications&nbsp;:<br />
	Le fichier attendu dans SACoche a le format suivant&nbsp;:</p>
	<pre class='fieldset_opacite50'>
	bulletin_3mixte_5_1_585;Saisie déportée - Bulletin scolaire - Trimestre 1 - 5 B - 5B2C1

	rubrique_613;\"Mathématiques\";Pourcentage;Appréciation
	groupe_585;\"Classe / Groupe\";;
	eleve_3333;\"Edgar DUNORD\";;
	eleve_3342;\"Alain TERIEUR\";;
	...
	</pre>
	<p>Le fichier produit par Gepi ne comporte que les lignes élèves <em>(celles qui commencent par <strong>eleve_</strong>)</em>.<br />
	Il faut remplacer les lignes élèves de l'export obtenu dans SACoche en cliquant dans <strong>MENU&nbsp;/&nbsp;Bilans officiels&nbsp;/&nbsp;Bulletin scolaire&nbsp;/&nbsp;<img src='../images/edit16.png' class='icone16'/> Saisie telle classe ou enseignement</strong> sur <strong>Saisie déportée</strong>, puis sur <strong>Récupérer un fichier vierge à compléter pour une saisie déportée (format csv).</strong></p>
	<p>Une fois votre fichier CSV correctement rempli, vous pourrez l'importer dans SACoche en cliquant sur <strong>Saisie déportée</strong>, puis sur <strong>Envoyer un fichier d'appréciations complété (format CSV)</strong>.</p>
</div>";



require("../lib/footer.inc.php");
?>
