<?php
/*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$accessibilite="y";
$titre_page = "Orientation : Activation/désactivation";
$niveau_arbo = 1;
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';


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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Module Orientation : Administration du module',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//$msg = '';
if (isset($_POST['is_posted'])) {
	check_token();

	$nb_reg=0;
	$msg="";

	if ((isset($_POST['activer']))&&(!saveSetting("active_mod_orientation", $_POST['activer']))) {
		$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre activation/désactivation !</span><br />";
	} else {
		$nb_reg++;
	}

	saveSetting("OrientationSaisieVoeuxAdministrateur", 'y');

	$tab_statut=array('PP', 'Scolarite', 'Cpe');
	for($loop=0;$loop<count($tab_statut);$loop++) {
		if (isset($_POST['OrientationSaisieVoeux'.$tab_statut[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!saveSetting('OrientationSaisieVoeux'.$tab_statut[$loop], $valeur)) {
			$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre OrientationSaisieVoeux".$tab_statut[$loop]." !</span><br />";
		} else {
			$nb_reg++;
		}
	}

	if(isset($_POST['OrientationNbMaxVoeux'])) {
		if((!preg_match("/^[0-9]{1,}$/", $_POST['OrientationNbMaxVoeux']))||($_POST['OrientationNbMaxVoeux']==0)) {
			$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');
			if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxVoeux))||($OrientationNbMaxVoeux==0)) {
				$OrientationNbMaxVoeux=3;
				if(!saveSetting("OrientationNbMaxVoeux", $OrientationNbMaxVoeux)) {
					$msg.="Erreur lors de l'initialisation de 'OrientationNbMaxVoeux'.<br />";
				}
			}
		}
		else {
			if(!saveSetting("OrientationNbMaxVoeux", $_POST['OrientationNbMaxVoeux'])) {
				$msg.="Erreur lors de l'enregistrement de 'OrientationNbMaxVoeux'.<br />";
			}
		}
	}
	else {
		$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');
		if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxVoeux))||($OrientationNbMaxVoeux==0)) {
			$OrientationNbMaxVoeux=3;
			if(!saveSetting("OrientationNbMaxVoeux", $OrientationNbMaxVoeux)) {
				$msg.="Erreur lors de l'initialisation de 'OrientationNbMaxVoeux'.<br />";
			}
		}
	}

	saveSetting("OrientationSaisieOrientationAdministrateur", 'y');

	$tab_statut=array('PP', 'Scolarite', 'Cpe');
	for($loop=0;$loop<count($tab_statut);$loop++) {
		if (isset($_POST['OrientationSaisieOrientation'.$tab_statut[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!saveSetting('OrientationSaisieOrientation'.$tab_statut[$loop], $valeur)) {
			$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre OrientationSaisieOrientation".$tab_statut[$loop]." !</span><br />";
		} else {
			$nb_reg++;
		}
	}

	saveSetting("OrientationSaisieTypeAdministrateur", 'y');

	$tab_statut=array('PP', 'Scolarite', 'Cpe');
	for($loop=0;$loop<count($tab_statut);$loop++) {
		if (isset($_POST['OrientationSaisieType'.$tab_statut[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!saveSetting('OrientationSaisieType'.$tab_statut[$loop], $valeur)) {
			$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre OrientationSaisieType".$tab_statut[$loop]." !</span><br />";
		} else {
			$nb_reg++;
		}
	}

	if(isset($_POST['OrientationNbMaxOrientation'])) {
		if((!preg_match("/^[0-9]{1,}$/", $_POST['OrientationNbMaxOrientation']))||($_POST['OrientationNbMaxOrientation']==0)) {
			$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');
			if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxOrientation))||($OrientationNbMaxOrientation==0)) {
				$OrientationNbMaxOrientation=3;
				if(!saveSetting("OrientationNbMaxOrientation", $OrientationNbMaxOrientation)) {
					$msg.="Erreur lors de l'initialisation de 'OrientationNbMaxOrientation'.<br />";
				}
			}
		}
		else {
			if(!saveSetting("OrientationNbMaxOrientation", $_POST['OrientationNbMaxOrientation'])) {
				$msg.="Erreur lors de l'enregistrement de 'OrientationNbMaxOrientation'.<br />";
			}
		}
	}
	else {
		$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');
		if((!preg_match("/^[0-9]{1,}$/", $OrientationNbMaxOrientation))||($OrientationNbMaxOrientation==0)) {
			$OrientationNbMaxOrientation=3;
			if(!saveSetting("OrientationNbMaxOrientation", $OrientationNbMaxOrientation)) {
				$msg.="Erreur lors de l'initialisation de 'OrientationNbMaxOrientation'.<br />";
			}
		}
	}

	$tab_mef_deja_af=array();
	$mef_code_af=isset($_POST['mef_code_af']) ? $_POST['mef_code_af'] : array();
	$sql="SELECT * FROM o_mef om WHERE affichage='y';";
	$res_o_mef=mysqli_query($mysqli, $sql);
	while($lig_o_mef=mysqli_fetch_object($res_o_mef)) {
		if(!in_array($lig_o_mef->mef_code, $mef_code_af)) {
			$sql="DELETE FROM o_mef WHERE mef_code='".$lig_o_mef->mef_code."';";
			$del=mysqli_query($mysqli, $sql);
			if(!$del) {
				$msg.="Erreur lors de la suppression de l'affichage pour le MEF '".$lig_o_mef->mef_code."'.<br />";
			}
			else {
				$nb_reg++;
			}
		}
		else {
			$tab_mef_deja_af[]=$lig_o_mef->mef_code;
		}
	}

	for($loop=0;$loop<count($mef_code_af);$loop++) {
		if(!in_array($mef_code_af[$loop], $tab_mef_deja_af)) {
			$sql="INSERT INTO o_mef SET mef_code='".$mef_code_af[$loop]."', affichage='y';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'enregistrement de l'affichage pour le MEF '".$mef_code_af[$loop]."'.<br />";
			}
			else {
				$nb_reg++;
			}
		}
	}

	if($nb_reg>0) {
		$msg .= "Enregistrement effectué ($nb_reg valeurs).<br />";
		$post_reussi=TRUE;
	}
}

if (isset($_POST['is_posted2'])) {
	check_token();

	if(isset($_POST['suppr_orientation'])) {
		$sql="DELETE FROM o_orientations;";
		//echo "$sql<br />\n";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if($suppr) {
			$msg="Les orientations élèves saisies ont été supprimées.<br />\n";
			$post_reussi=TRUE;
		}
		else {
			$msg="<span style='color:red'>Erreur lors de la suppression des orientations élèves saisies.</span><br />\n";
		}
	}

	if(isset($_POST['suppr_voeux'])) {
		$sql="DELETE FROM o_voeux;";
		//echo "$sql<br />\n";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if($suppr) {
			$msg="Les voeux élèves saisis ont été supprimés.<br />\n";
			$post_reussi=TRUE;
		}
		else {
			$msg="<span style='color:red'>Erreur lors de la suppression des voeux élèves saisis.</span><br />\n";
		}
	}
}

$themessage = 'Des modifications n ont pas été validées. Voulez-vous vraiment quitter sans enregistrer ?';

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion Orientation"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

//include("../lib/calendrier/calendrier.class.php");
//$cal = new Calendrier("form2", "date_limite");

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_orientation/admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

?>
