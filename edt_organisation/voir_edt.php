<?php

/**
 * Fichier voir_edt.php pour visionner les différents EdT (classes ou professeurs)
 *
 * @package		GEPI
 * @subpackage	EmploisDuTemps
 * @copyright	Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
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

// Définir dés le début le type d'EdT qu'on veut voir (prof, classe, salle)

//===========================
// AJOUT: boireaus
$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$login_edt=isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);
$classe=isset($_GET['classe']) ? $_GET['classe'] : (isset($_POST['classe']) ? $_POST['classe'] : NULL);
$salle=isset($_GET['salle']) ? $_GET['salle'] : (isset($_POST['salle']) ? $_POST['salle'] : NULL);
$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : NULL;
$identite = isset($_GET["identite"]) ? $_GET["identite"] : NULL;
$message = isset($_SESSION["message"]) ? $_SESSION["message"] : "";
$type_edt_2 = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
$period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
$bascule_edt=isset($_GET['bascule_edt']) ? $_GET['bascule_edt'] : (isset($_POST['bascule_edt']) ? $_POST['bascule_edt'] : NULL);
$week_min=isset($_GET['week_min']) ? $_GET['week_min'] : (isset($_POST['week_min']) ? $_POST['week_min'] : NULL);
$week_selected=isset($_GET['week_selected']) ? $_GET['week_selected'] : (isset($_POST['week_selected']) ? $_POST['week_selected'] : NULL);
//===========================

// =============================================================================
//
//                                  TRAITEMENT DES DONNEES
//		
// =============================================================================

if ($visioedt == 'prof1') {
    $type_edt = $login_edt;
}
elseif ($visioedt == 'classe1') {
    $type_edt = $classe;
}
elseif ($visioedt == 'salle1') {
    $type_edt = $salle;
}

if ($message != "") {
    $_SESSION["message"] = "";
}
// =================== Gérer la bascule entre emplois du temps périodes et emplois du temps semaines.

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
// =================== Forcer l'affichage d'un edt si l'utilisateur est un prof 
if (!isset($login_edt)) {
    if (($_SESSION['statut'] == "professeur") AND ($visioedt == "prof1")) {
        $login_edt = $_SESSION['login'];
        $_GET["login_edt"] = $login_edt;
        $_GET["type_edt_2"] = "prof";
        $type_edt_2 = "prof";
        $visioedt = "prof1";
    }
}
elseif (($_SESSION['statut'] == "professeur") AND ($visioedt == "prof1") AND (getSettingValue('AccesProf_EdtProfs')=='no')) {
    $login_edt = $_SESSION['login'];

    $_GET["login_edt"] = $login_edt;
    $_GET["type_edt_2"] = "prof";
    $type_edt_2 = "prof";
    $visioedt = "prof1";
}
// =================== Construire les emplois du temps

if(isset($login_edt)){
	//debug_var();

    $type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);
    if ($type_edt == "prof")
    {
		$sql="SELECT 1=1 FROM edt_cours WHERE duree='0' AND login_prof='$login_edt';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
	        $tab_data = ConstruireEDTProf($login_edt, $_SESSION['period_id']);
		}
		else {
			$tab_data=array();
			$msg="<b>ANOMALIE&nbsp;:</b> Un ou des cours de durée nulle perturbent l'affichage.<br />Contactez l'administrateur pour un ";
			if($_SESSION['statut']=='administrateur') {
				$msg.="<a href='verifier_edt.php'>Nettoyage des tables</a>";
			}
			else {
				$msg.="Nettoyage des tables";
			}
			$msg.=" portant sur l'EDT.";
		}
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
		FixColumnPositions($tab_data, $entetes);		// en cours de devel
        $DisplayEDT = true;
    }
    else if ($type_edt == "classe")
    {
		$sql="SELECT 1=1 FROM edt_cours WHERE duree='0';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$tab_data = ConstruireEDTClasse($login_edt, $_SESSION['period_id']);
		}
		else {
			$tab_data=array();
			$msg="<b>ANOMALIE&nbsp;:</b> Un ou des cours de durée nulle perturbent l'affichage.<br />Contactez l'administrateur pour un ";
			if($_SESSION['statut']=='administrateur') {
				$msg.="<a href='verifier_edt.php'>Nettoyage des tables</a>";
			}
			else {
				$msg.="Nettoyage des tables";
			}
			$msg.=" portant sur l'EDT.";
		}
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        $DisplayEDT = true;

    }
    else if ($type_edt == "salle")
    {
		$sql="SELECT 1=1 FROM edt_cours WHERE duree='0';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$tab_data = ConstruireEDTSalle($login_edt , $_SESSION['period_id']);
		}
		else {
			$tab_data=array();
			$msg="<b>ANOMALIE&nbsp;:</b> Un ou des cours de durée nulle perturbent l'affichage.<br />Contactez l'administrateur pour un ";
			if($_SESSION['statut']=='administrateur') {
				$msg.="<a href='verifier_edt.php'>Nettoyage des tables</a>";
			}
			else {
				$msg.="Nettoyage des tables";
			}
			$msg.=" portant sur l'EDT.";
		}
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
		//FixColumnPositions($tab_data, $entetes);		// en cours de devel
        $DisplayEDT = true;

    }
    else if ($type_edt == "eleve")
    {
		$sql="SELECT 1=1 FROM edt_cours WHERE duree='0';";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$tab_data = ConstruireEDTEleve($login_edt , $_SESSION['period_id']);
		}
		else {
			$tab_data=array();
			$msg="<b>ANOMALIE&nbsp;:</b> Un ou des cours de durée nulle perturbent l'affichage.<br />Contactez l'administrateur pour un ";
			if($_SESSION['statut']=='administrateur') {
				$msg.="<a href='verifier_edt.php'>Nettoyage des tables</a>";
			}
			else {
				$msg.="Nettoyage des tables";
			}
			$msg.=" portant sur l'EDT.";
		}
        $entetes = ConstruireEnteteEDT();
        $creneaux = ConstruireCreneauxEDT();
        $DisplayEDT = true;

    }
    else {
        $DisplayEDT = false;
    }

}
else {
    $DisplayEDT = false;
}
// =================== Tester la présence de IE6

$ua = getenv("HTTP_USER_AGENT");
if (strstr($ua, "MSIE 6.0")) {
	 $IE6 = true;
}
else {
    $IE6 = false;
}

// =============================================================================
//
//                                  VUE
//		
// =============================================================================
$no_entete=isset($_POST['no_entete']) ? $_POST['no_entete'] : (isset($_GET['no_entete']) ? $_GET['no_entete'] : "n");
if($no_entete=="y") {
	unset($titre_page);
}

//https://127.0.0.1/steph/gepi_git_trunk/edt_organisation/index_edt.php?login_edt=bejae&type_edt_2=eleve&no_entete=y&no_menu=y&lien_refermer=y
//https://127.0.0.1/steph/gepi_git_trunk/edt/index2.php?login_eleve=bejae&type_affichage=eleve&affichage_complementaire_sur_edt=absences2&num_semaine_annee=27|2015
$chaine_opt_edt2="";
$chaine_image_edt="<img src='$gepiPath/images/icons/edt2.png' class='icone16' alt='EDT2' />";
if((isset($login_edt))&&($login_edt!="")) {
	if((isset($type_edt_2))&&($type_edt_2=="eleve")) {
		$chaine_opt_edt2="?affichage=semaine&type_affichage=eleve&login_eleve=".$login_edt;
		if(isset($_GET['affichage_complementaire_sur_edt'])) {
			$chaine_opt_edt2.="&affichage_complementaire_sur_edt=".$_GET['affichage_complementaire_sur_edt'];
			if($_GET['affichage_complementaire_sur_edt']=="absences2") {
				$chaine_image_edt="<img src='$gepiPath/images/icons/edt2_abs2.png' width='24' height='24' alt='EDT2ABS2' />";
			}
		}
	}
	elseif((isset($type_edt_2))&&($type_edt_2=="prof")) {
		$chaine_opt_edt2="?affichage=semaine&type_affichage=prof&login_prof=".$login_edt;
		if(isset($_GET['affichage_complementaire_sur_edt'])) {
			$chaine_opt_edt2.="&affichage_complementaire_sur_edt=".$_GET['affichage_complementaire_sur_edt'];
		}
	}
}

$mode_infobulle=isset($_POST['mode_infobulle']) ? $_POST['mode_infobulle'] : (isset($_GET['mode_infobulle']) ? $_GET['mode_infobulle'] : "n");
//echo "\$mode_infobulle=$mode_infobulle<br />";
if($mode_infobulle=="n") {
	require_once("../lib/header.inc.php");

	if(acces("/edt/index2.php", $_SESSION['statut'])) {
		echo "<div style='float:right; width:16px; margin:5px;' title=\"Affichage EDT version 2\"><a href='$gepiPath/edt/index2.php".$chaine_opt_edt2."'>".$chaine_image_edt."</a></div>";
	}
}
else {
	if(acces("/edt/index2.php", $_SESSION['statut'])) {
		echo "<div style='float:right; width:16px; margin:5px;' title=\"Affichage EDT version 2\"><a href='$gepiPath/edt/index2.php".$chaine_opt_edt2."' target='_blank'>".$chaine_image_edt."</a></div>";
	}
}

require_once("./voir_edt_view.php");
if($mode_infobulle=="n") {
	require_once("../lib/footer.inc.php");
}

?>
