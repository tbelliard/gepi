<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Initialisations files
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

$sql="SELECT 1=1 FROM droits WHERE id='/edt_organisation/saisie_edt.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/edt_organisation/saisie_edt.php',
	administrateur='V',
	professeur='V',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='EDT : Saisie',
	statut='';";
	$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;


function get_nom_salle($id_salle, $id_cours="") {
	$retour="";
	if($id_salle!="") {
		$sql="SELECT id_salle, numero_salle, nom_salle FROM salle_cours WHERE id_salle='$id_salle'";
	}
	else {
		$sql="SELECT sc.* FROM salle_cours sc, edt_cours ec WHERE sc.id_salle=ec.id_salle AND ec.id_cours='$id_cours'";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($lig->nom_salle!="") {
			$retour=$lig->nom_salle;
		}
		else {
			$retour=$lig->numero_salle;
		}
	}
	return $retour;
}

function get_infos_cours($id_cours) {
	$retour="";
	$sql="SELECT * FROM edt_cours WHERE id_cours='$id_cours';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		if($lig->id_groupe!="") {
			$info_group=get_info_grp($lig->id_groupe,array('classes'));
			if(($lig->id_semaine=="A")||($lig->id_semaine=="B")) {
				echo "<div style='border:1px solid black; width:45%; margin-right:2px; float:left;'><strong>".$lig->id_semaine."</strong> ";
				echo $info_group." (".get_nom_salle("", $lig->id_cours).")";
				echo "</div>";
			}
			else {
				echo $info_group." (".get_nom_salle("", $lig->id_cours).")";
			}
		}
		else {
			// AID : A faire
			echo "AID";
		}
	}
	return $retour;
}

//==============================================================

// fonctions edt
require_once('./choix_langue.php');
require_once("./fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
require_once("./fonctions_edt_prof.php");       // --- edt prof
require_once("./fonctions_edt_classe.php");     // --- edt classe
require_once("./fonctions_edt_salle.php");      // --- edt salle
require_once("./fonctions_edt_eleve.php");      // --- edt eleve
require_once("./fonctions_calendrier.php");
require_once("./fonctions_affichage.php");
require_once("./fonctions_cours.php");
require_once("./req_database.php");

//==============================================================
if(isset($_POST['modifier_cours'])) {
	// Extrait de modifier_cours_popup.php un peu bidouillé

	check_token();

	$id_cours = isset($_GET["id_cours"]) ? $_GET["id_cours"] : (isset($_POST["id_cours"]) ? $_POST["id_cours"] : NULL);
	$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : (isset($_POST["type_edt"]) ? $_POST["type_edt"] : NULL);
	$identite = isset($_GET["identite"]) ? $_GET["identite"] : (isset($_POST["identite"]) ? $_POST["identite"] : NULL);
	$modifier_cours = isset($_POST["modifier_cours"]) ? $_POST["modifier_cours"] : NULL;
	$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
	$ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
	$ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
	$heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
	$duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
	$choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
	$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
	$periode_calendrier = isset($_POST["periode_calendrier"]) ? $_POST["periode_calendrier"] : NULL;
	$aid = isset($_POST["aid"]) ? $_POST["aid"] : NULL;
	$horaire = isset($_GET["horaire"]) ? $_GET["horaire"] : (isset($_POST["horaire"]) ? $_POST["horaire"] : NULL);
	$cours = isset($_GET["cours"]) ? $_GET["cours"] : (isset($_POST["cours"]) ? $_POST["cours"] : NULL);

	//$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
	$period_id=$_SESSION['period_id'];

	$message = "";
	$id_aid = "";
	$analyse = explode("|", $enseignement);
	if ($analyse[0] == "AID") {
		$id_aid = $analyse[1];
		$enseignement = "";
	}
	// Dans le cas d'un professeur, on s'assure qu'il s'agit bien de son edt
	if (($_SESSION["statut"] == 'professeur') AND (getSettingValue("edt_remplir_prof") == 'y')){

		if (my_strtolower($identite) != my_strtolower($_SESSION["login"])){
			die("Vous ne pouvez pas cr&eacute;er un cours pour un coll&egrave;gue");
		}
	}

	if($_SESSION['statut']=='professeur') {
		$identite=$_SESSION['login'];
	}

	// Ajouter des tests
	if((isset($duree))&&($duree!='')&&($duree>0)) {
		if((!isset($choix_semaine))||($choix_semaine=='')) {
			$choix_semaine=0;
		}

		// Traitement des changements
		if (isset($modifier_cours) AND $modifier_cours == "ok") {
			if (ProfDisponible($identite, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
					if (SalleDisponible($login_salle, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
					if (GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, $id_cours, $message, $periode_calendrier)) {
						$sql="UPDATE edt_cours SET id_groupe = '$enseignement',
															id_aid = '$id_aid',
															id_salle = '$login_salle',
															jour_semaine = '$ch_jour_semaine',
															id_definie_periode = '$ch_heure',
															duree = '$duree',
															heuredeb_dec = '$heure_debut',
															id_semaine = '$choix_semaine',
															id_calendrier = '$periode_calendrier'
															WHERE id_cours = '".$id_cours."'";
						//echo "$sql<br />";
						$req_modif = mysql_query($sql);
						if(!$req_modif) {
							echo "Erreur: ".mysql_error();
							die();
						}
						else {
							// Afficher le cours modifié
							echo get_infos_cours($id_cours);

							$_SESSION['edt_prof_enseignement'] = $enseignement;
							$_SESSION['edt_prof_salle'] = $login_salle;
						}
					}
				}
			}
		}
		elseif (isset($modifier_cours) AND $modifier_cours == "non") {
			//echo "1";
			if (ProfDisponible($identite, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
				//echo "2";
				if (SalleDisponible($login_salle, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
					//echo "3";
					if (GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
						$sql="INSERT INTO edt_cours SET id_groupe = '$enseignement',
														id_aid = '$id_aid',
														id_salle = '$login_salle',
														jour_semaine = '$ch_jour_semaine',
														id_definie_periode = '$ch_heure',
														duree = '$duree',
														heuredeb_dec = '$heure_debut',
														id_semaine = '$choix_semaine',
														id_calendrier = '$periode_calendrier',
														login_prof = '".$identite."'";
						//echo "$sql<br />";
						$nouveau_cours = mysql_query($sql);
						if(!$nouveau_cours) {
							echo "Erreur: ".mysql_error();
							die();
						}
						else {
							// Afficher le nouveau cours
							$id_cours=mysql_insert_id();
							echo get_infos_cours($id_cours);

							$_SESSION['edt_prof_enseignement'] = $enseignement;
							$_SESSION['edt_prof_salle'] = $login_salle;
						}
					}
					else {
						//debug_var();
						//echo "GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)";
						echo "<span style='color:red' title='Groupe non disponible'>KO</span>";
						echo "<a href='#' onclick=\"ajout_cours('".$_POST['id_div']."', '$ch_jour_semaine', $ch_heure, ''); return false;\" target='_blank'><img src='../templates/DefaultEDT/images/ico_plus2.png' /></a>";
					}
				}
				else {
					echo "<span style='color:red' title='Salle non disponible'>KO</span>";
					echo "<a href='#' onclick=\"ajout_cours('".$_POST['id_div']."', '$ch_jour_semaine', $ch_heure, ''); return false;\" target='_blank'><img src='../templates/DefaultEDT/images/ico_plus2.png' /></a>";
				}
			}
			else {
				echo "<span style='color:red' title='Professeur non disponible'>KO</span>";
				echo "<a href='#' onclick=\"ajout_cours('".$_POST['id_div']."', '$ch_jour_semaine', $ch_heure, ''); return false;\" target='_blank'><img src='../templates/DefaultEDT/images/ico_plus2.png' /></a>";
			}


			//debug_var();
			//die();
		} 
	}
	else {
		// On ne fait rien
		echo "<span style='color:red' title='La durée choisie est nulle ".$duree.".'>KO</span>";
		echo "<a href='#' onclick=\"ajout_cours('".$_POST['id_div']."', '$ch_jour_semaine', $ch_heure, ''); return false;\" target='_blank'><img src='../templates/DefaultEDT/images/ico_plus2.png' /></a>";
	}

	// AJAX: on ne va pas plus loin
	die();
}
//==============================================================
// Extrait de voir_edt.php

$bascule_edt=isset($_GET['bascule_edt']) ? $_GET['bascule_edt'] : (isset($_POST['bascule_edt']) ? $_POST['bascule_edt'] : NULL);
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);

if ($bascule_edt != NULL) {
    $_SESSION['bascule_edt'] = $bascule_edt;
}
if (!isset($_SESSION['bascule_edt'])) {
    $_SESSION['bascule_edt'] = 'periode';
}
if ($_SESSION['bascule_edt'] == 'periode') {
    if (PeriodesExistent()) {
        if ($period_id != NULL) {
            $_SESSION['period_id'] = $period_id;
        }
        if (!isset($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnIdPeriod(date("U"));
        }
        if (!PeriodExistsInDB($_SESSION['period_id'])) {
            $_SESSION['period_id'] = ReturnFirstIdPeriod();    
        }
        $DisplayPeriodBar = true;
        $DisplayWeekBar = false;
    }
    else {
        $DisplayWeekBar = false;
        $DisplayPeriodBar = false;
        $_SESSION['period_id'] = 0;
    }
}
else {
    $DisplayPeriodBar = false;
    $DisplayWeekBar = true;
    if ($week_selected != NULL) {
        $_SESSION['week_selected'] = $week_selected;
    }
    if (!isset($_SESSION['week_selected'])) {
        $_SESSION['week_selected'] = date("W");
    }
}
//==============================================================

if($_SESSION['statut']=='professeur') {
	$login_prof=$_SESSION['login'];
}

//**************** EN-TETE *****************
$titre_page = "Emploi du temps : Saisie";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************

echo "<form name='form1' action='".$_SERVER['PHP_SELF']."' method='post'>
<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='index_edt.php'>Emplois du temps</a>";

$sql="SELECT login, nom, prenom, civilite, etat FROM utilisateurs WHERE statut='professeur' ORDER BY etat, nom, prenom;";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "</p>
</form>
<p style='color:red'>Il n'existe encore aucun professeur.</p>
<p><br /></p>\n";

	require("../lib/footer.inc.php");
	die();
}

$tab_prof=array();
while($lig=mysql_fetch_object($res)) {
	$tab_prof[$lig->login]['designation']=$lig->civilite." ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, "majf2");
	$tab_prof[$lig->login]['style_et_title']=(($lig->etat=="actif") ? "" : " style='color:grey' title='Compte inactif'");
}

if((!isset($login_prof))||($login_prof=="")) {
	echo "</p>
</form>

<h2>Saisie d'emploi du temps</h2>";

	echo "
<h3>Choix du professeur</h3>

<form name='form_choix_prof' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>
		<legend style='border: 1px solid grey; background-color: white;'>Choix du professeur</legend>

		<p>De quel professeur souhaitez-vous saisir/modifier l'emploi du temps&nbsp;:<br />
			<select name='login_prof'>
				<option value=''>--- Choisissez ---</option>";
	foreach($tab_prof as $current_login_prof => $current_prof) {
		echo "
				<option value='$current_login_prof'".$current_prof['style_et_title'].">".$current_prof['designation']."</option>";
	}
	echo "
			</select>
			<input type='submit' value='Valider' />
		</p>
	</fieldset>
</form>
<p><br /></p>\n";

	require("../lib/footer.inc.php");
	die();
}
//=========================================================
if($_SESSION['statut']!='professeur') {
	echo " | Autre professeur : <select name='login_prof' onchange='document.form1.submit()'>
	<option value=''>--- Choisissez ---</option>";
	foreach($tab_prof as $current_login_prof => $current_prof) {
		if($current_login_prof==$login_prof) {$selected=" selected";} else {$selected="";}
		echo "
	<option value='$current_login_prof'".$current_prof['style_et_title'].$selected.">".$current_prof['designation']."</option>";
	}
	echo "</select> | <a href='index_edt.php?login_edt=$login_prof&amp;type_edt_2=prof&amp;visioedt=prof1'>Voir EDT</a>";
}

echo "</p>
</form>

<h2>Saisie de l'emploi du temps de ".$tab_prof[$login_prof]['designation']."</h2>\n";

$groups=get_groups_for_prof($login_prof);
if(count($groups)==0) {
	echo "<p style='color:red'>Ce professeur n'assure aucun enseignement.</p>
<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo add_token_field(true);
echo "<div style='float:left; width:30%; margin-right:1em; border:1px solid grey; padding:0.5em; background-image: url(\"../images/background/opacite50.png\");'>
	<form name='form_edt'>
	<p class='bold'>Enseignements&nbsp;:</p>";

$chaine_radio_change="";
foreach($groups as $current_group) {
	$chaine_radio_change.="checkbox_change('id_groupe_".$current_group['id']."');\n";
	echo "
	<input type='radio' name='id_groupe' id='id_groupe_".$current_group['id']."' value='".$current_group['id']."' onchange=\"radio_change_graisse(); changement();\" /><label for='id_groupe_".$current_group['id']."' id='texte_id_groupe_".$current_group['id']."'>".$current_group['name']." (<em>".$current_group['description']."</em>) en ".$current_group['classlist_string']."</label><br />";
}

// Récupérer la liste des salles
$tab_select_salle = renvoie_liste("salle");
/*
echo "<pre>";
print_r($tab_select_salle);
echo "</pre>";
*/
echo "
	<p class='bold'>Salles&nbsp;:</p>
	<p>Choisissez une salle&nbsp;: 
	<select name='id_salle' id='id_salle'>
		<option value=''>---</option>";
for($c=0;$c<count($tab_select_salle);$c++) {
	echo "
		<option value='".$tab_select_salle[$c]['id_salle']."'>".(($tab_select_salle[$c]['nom_salle']!="") ? $tab_select_salle[$c]['nom_salle'] : $tab_select_salle[$c]['numero_salle'])."</option>";
}
echo "
	</select>
	</p>

	<p class='bold'>Semaines&nbsp;:</p>
	<p>Semaine&nbsp;: 
	<select name='semaine' id='semaine'>
		<option value='0'>Toutes</option>
		<option value='A'>A</option>
		<option value='B'>B</option>
	</select>
	</p>";

echo "<br />
<p>Choisissez un enseignement et une salle, puis cliquez dans le tableau ci-contre.</p>
</form>
</div>";

// Récupérer les jours

$sql="SELECT jour_horaire_etablissement FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1";
//echo "$sql<br />";
$req_jours = mysql_query($sql) or die(mysql_error());
$jour_sem_tab = array();
while($data_sem_tab = mysql_fetch_array($req_jours)) {
	$jour_sem_tab[] = $data_sem_tab["jour_horaire_etablissement"];
	//echo "\$jour_sem_tab[] = ".$data_sem_tab['jour_horaire_etablissement'].";<br />";
}

/*
mysql> SELECT * FROM horaires_etablissement WHERE ouvert_horaire_etablissement = 1;
+--------------------------+----------------------------+----------------------------+---------------------------------+---------------------------------+-----------------------------+------------------------------+
| id_horaire_etablissement | date_horaire_etablissement | jour_horaire_etablissement | ouverture_horaire_etablissement | fermeture_horaire_etablissement | pause_horaire_etablissement | ouvert_horaire_etablissement |
+--------------------------+----------------------------+----------------------------+---------------------------------+---------------------------------+-----------------------------+------------------------------+
|                        1 | 0000-00-00                 | lundi                      | 08:00:00                        | 16:30:00                        | 00:45:00                    |                            1 |
|                        2 | 0000-00-00                 | mardi                      | 08:00:00                        | 16:30:00                        | 00:45:00                    |                            1 |
|                        3 | 0000-00-00                 | mercredi                   | 08:00:00                        | 12:00:00                        | 00:00:00                    |                            1 |
|                        4 | 0000-00-00                 | jeudi                      | 08:00:00                        | 16:30:00                        | 00:45:00                    |                            1 |
|                        5 | 0000-00-00                 | vendredi                   | 08:00:00                        | 16:30:00                        | 00:45:00                    |                            1 |
+--------------------------+----------------------------+----------------------------+---------------------------------+---------------------------------+-----------------------------+------------------------------+
5 rows in set (0.00 sec)

mysql> 
*/


// Récupérer les créneaux
/*
mysql> SELECT * FROM edt_creneaux WHERE type_creneaux != 'pause';
+--------------------+---------------------+----------------------------+--------------------------+-----------------------+---------------+--------------+
| id_definie_periode | nom_definie_periode | heuredebut_definie_periode | heurefin_definie_periode | suivi_definie_periode | type_creneaux | jour_creneau |
+--------------------+---------------------+----------------------------+--------------------------+-----------------------+---------------+--------------+
|                  1 | M1                  | 08:00:00                   | 08:55:00                 |                     1 | cours         | NULL         |
|                  2 | M2                  | 08:55:00                   | 09:50:00                 |                     1 | cours         | NULL         |
|                  3 | M3                  | 10:05:00                   | 11:00:00                 |                     1 | cours         | NULL         |
|                  4 | M4                  | 11:00:00                   | 11:55:00                 |                     1 | cours         | NULL         |
|                  5 | S1                  | 13:30:00                   | 14:25:00                 |                     1 | cours         | NULL         |
|                  6 | S2                  | 14:25:00                   | 15:20:00                 |                     1 | cours         | NULL         |
|                  7 | S3                  | 15:35:00                   | 16:30:00                 |                     1 | cours         | NULL         |
|                 33 | R0                  | 12:00:00                   | 12:30:00                 |                     1 | repas         | NULL         |
|                 34 | R2                  | 13:00:00                   | 13:30:00                 |                     1 | cours         | NULL         |
|                 37 | R1                  | 12:30:00                   | 13:00:00                 |                     1 | repas         | NULL         |
+--------------------+---------------------+----------------------------+--------------------------+-----------------------+---------------+--------------+
10 rows in set (0.00 sec)

mysql> 
*/

/*
$tab_id_creneaux = retourne_id_creneaux();

echo "<pre>";
print_r($tab_id_creneaux);
echo "</pre>";
*/

$tab_creneau=array();
$sql="SELECT * FROM edt_creneaux
				WHERE type_creneaux != 'pause'
				ORDER BY heuredebut_definie_periode";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p style='color:red'>Aucun créneau horaire n'a été trouvé.</p>
<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

while($lig=mysql_fetch_object($res)) {
	$tab_creneau[$lig->id_definie_periode]['nom_definie_periode']=$lig->nom_definie_periode;
	$tab_creneau[$lig->id_definie_periode]['heuredebut_definie_periode']=$lig->heuredebut_definie_periode;
	$tab_creneau[$lig->id_definie_periode]['heurefin_definie_periode']=$lig->heurefin_definie_periode;
	$tab_creneau[$lig->id_definie_periode]['type_creneaux']=$lig->type_creneaux;
	$tab_creneau[$lig->id_definie_periode]['jour_creneau']=$lig->jour_creneau;
}

echo "<table class='boireaus boireaus_alt'>
	<tr>
		<th></th>";
for($loop=0;$loop<count($jour_sem_tab);$loop++) {
	echo "
		<th width='".floor(90/count($jour_sem_tab))."%'>".$jour_sem_tab[$loop]."</th>";
}
echo "
	</tr>";

foreach($tab_creneau as $id_definie_periode => $creneau) {
	$style_ligne="";
	if($creneau['type_creneaux']!='cours') {
		$style_ligne=" style='background-color:grey'";
	}
	echo "
	<tr".$style_ligne.">
		<th title='".$creneau['heuredebut_definie_periode']."-".$creneau['heurefin_definie_periode']."'>".$creneau['nom_definie_periode']."</th>";

	for($loop=0;$loop<count($jour_sem_tab);$loop++) {
		echo "
		<td>";
		// Voir s'il y a des cours sur ce créneau
		$req_creneau = LessonsFromDayTeacherSlotPeriod($jour_sem_tab[$loop], $login_prof, $id_definie_periode, $period_id);

		//$rep_creneau = mysql_fetch_array($req_creneau);
		//print_r($rep_creneau);
		$nb_rows = mysql_num_rows($req_creneau);

		/*
		echo "SELECT id_cours, id_aid, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
					jour_semaine = '".$jour_sem_tab[$loop]."' AND
					login_prof = '".$login_prof."' AND
					id_definie_periode = '".$id_definie_periode."' AND
					(id_calendrier = '".$period_id."' OR id_calendrier = '0');";
		*/

		if($nb_rows>0) {
			while($rep_creneau = mysql_fetch_array($req_creneau)) {
				/*
				echo "\$nb_rows=$nb_rows<br />";
				echo "<pre>";
				print_r($rep_creneau);
				echo "</pre>";
				*/
				$info_group=get_info_grp($rep_creneau['id_groupe'],array('classes'));
				if(($rep_creneau['id_semaine']=="A")||($rep_creneau['id_semaine']=="B")) {
					echo "<div style='border:1px solid black; width:45%; margin-right:2px; float:left;'><strong>".$rep_creneau['id_semaine']."</strong> ".$info_group." (".get_nom_salle("", $rep_creneau['id_cours']).")"."</div>";
					if($nb_rows==1) {
						if($rep_creneau['id_semaine']=="A") {
							// Proposer le lien d'ajout avec semaine forcée à B
							
						}
						else {
							// Proposer le lien d'ajout avec semaine forcée à B
							
						}
					}
				}
				else {
					echo $info_group." (".get_nom_salle("", $rep_creneau['id_cours']).")";
				}
			}
		}
		elseif($creneau['type_creneaux']=='cours') {
			// Proposer le lien d'ajout
			echo "<div id='div_".$loop."_".$id_definie_periode."'><a href='#' onclick=\"ajout_cours('div_".$loop."_".$id_definie_periode."', '".$jour_sem_tab[$loop]."', $id_definie_periode, 0); return false;\" target='_blank'><img src='../templates/DefaultEDT/images/ico_plus2.png' /></a></div>";
		}

		echo "
		</td>";
	}

	echo "
	</tr>";
}

echo "
</table>

<script type='text/javascript'>
	function ajout_cours(id_div, ch_jour_semaine, ch_heure, choix_semaine) {
		if((choix_semaine=='')||(choix_semaine==0)) {
			choix_semaine=document.getElementById('semaine').value;
		}

		// A forcer en réception du formulaire si on n'est pas admin
		identite='$login_prof';

		login_salle=document.getElementById('id_salle').value;

		heure_debut=0;
		duree='2';

		modifier_cours='non';

		// Pour le moment, on force l'année entière:
		// Voir ligne 554 de modifier_cours_popup.php
		periode_calendrier=0;

		enseignement='';
		//enseignement=document.getElementById('id_groupe').value;
		radio=document.form_edt.id_groupe;
		for (var i=0; i<radio.length;i++) {
			if (radio[i].checked) {
				enseignement=radio[i].value;
			}
		}

		csrf_alea=document.getElementById('csrf_alea').value;

		if(enseignement!='') {
			new Ajax.Updater($(id_div),'".$_SERVER['PHP_SELF']."',{method: 'post',
			parameters: {
				id_div: id_div,
				enseignement: enseignement,
				choix_semaine: choix_semaine,
				ch_jour_semaine: ch_jour_semaine,
				ch_heure: ch_heure,
				duree: duree,
				identite: identite,
				login_salle: login_salle,
				heure_debut: heure_debut,
				modifier_cours: modifier_cours,
				periode_calendrier: periode_calendrier,
				csrf_alea: csrf_alea
			}});
		}
		else {
			alert('Choisissez un enseignement.');
		}
	}

	".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."

	function radio_change_graisse() {
		$chaine_radio_change
	}
</script>

<p style='color:red'><em>NOTE&nbsp;:</em> Cette page est encore expérimentale.<br />
Elle est incomplète.<br />
Pas jolie.<br />
Pouvoir supprimer des enseignements.<br />
Pouvoir modifier des enseignements.<br />
Pouvoir ajouter un enseignement en semaine B quand on en a défini un en semaine A.<br />
Remplacer le SELECT de semaine en un champ RADIO<br />
Prendre les styles et couleurs définis ailleurs dans le module<br />
...</p>";

/*
Variables envoyées en POST: (*)

    $_POST['enseignement']=	2626
    $_POST['ch_jour_semaine']=	mercredi
    $_POST['ch_heure']=	2
    $_POST['heure_debut']=	0
    $_POST['duree']=	2
    $_POST['choix_semaine']=	0
    $_POST['login_salle']=	21
    $_POST['periode_calendrier']=	0
    $_POST['id_cours']=	
    $_POST['type_edt']=	
    $_POST['identite']=	TIMONJ
    $_POST['id_aid']=	
    $_POST['modifier_cours']=	non
    $_POST['Enregistre']=	Enregistrer
*/

// $req_creneau = LessonsFromDayTeacherSlotPeriod($jour_sem_tab[$jour], $login_edt, $tab_id_creneaux[$j], $period);

/*
function LessonsFromDayTeacherSlotPeriod($jour_sem, $login_edt, $id_creneau, $period) {

    $sql_request = "SELECT id_cours, id_aid, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                    jour_semaine = '".$jour_sem."' AND
                                    login_prof = '".$login_edt."' AND
                                    id_definie_periode = '".$id_creneau."' AND
                                    (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;
}

// =============================================================================
//
//
//          
//
//
// =============================================================================
function LessonsFromDayTeacherSlotWeekPeriod($jour_sem, $login_edt, $id_creneau, $id_semaine, $period) {

    $sql_request = "SELECT id_cours, id_aid, duree, id_groupe, heuredeb_dec, id_semaine FROM edt_cours WHERE 
                                            jour_semaine = '".$jour_sem."' AND
                                            login_prof = '".$login_edt."' AND
                                            id_definie_periode = '".$id_creneau."' AND
                                            id_semaine = '".$id_semaine."' AND
                                            (id_calendrier = '".$period."' OR id_calendrier = '0')";

    $req_creneau = mysql_query($sql_request) or die(mysql_error());
    return $req_creneau;
}
*/


//https://127.0.0.1/steph/gepi_git_trunk/edt_organisation/modifier_cours.php?period_id=1&cours=aucun&identite=TIMONJ&horaire=mercredi|3|debut

//https://127.0.0.1/steph/gepi_git_trunk/edt_organisation/modifier_cours.php?period_id=1&cours=aucun&identite=TIMONJ&horaire=jeudi|1|debut


echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>



