<?php
/**
 * Gestion des EDT ical
 * 
 * $_POST['activer'] activation/désactivation
 * $_POST['is_posted']
 * 
 *
 * @copyright Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage administration
 * @see checkAccess()
 * @see saveSetting()
 * @see suivi_ariane()
 */

/* This file is part of GEPI.
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
$titre_page = "Gestion module EDT ical";
$niveau_arbo = 1;
$gepiPathJava="./..";

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

$sql="SELECT 1=1 FROM droits WHERE id='/edt/index_admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/edt/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='EDT ICAL : Administration',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/******************************************************************
 *    Enregistrement des variables passées en $_POST si besoin
 ******************************************************************/
$msg = '';
$post_reussi=FALSE;

//debug_var();

if((isset($_POST['is_posted']))&&($_POST['is_posted']==1)) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_edt_ical", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	if(isset($_POST['EdtIcalProf'])) {
		if (!saveSetting("EdtIcalProf", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalProf !";
		}
	}
	else {
		if (!saveSetting("EdtIcalProf", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalProf !";
		}
	}

	if(isset($_POST['EdtIcalProfTous'])) {
		if (!saveSetting("EdtIcalProfTous", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalProfTous !";
		}
	}
	else {
		if (!saveSetting("EdtIcalProfTous", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalProfTous !";
		}
	}

	if(isset($_POST['EdtIcalEleve'])) {
		if (!saveSetting("EdtIcalEleve", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalEleve !";
		}
	}
	else {
		if (!saveSetting("EdtIcalEleve", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalEleve !";
		}
	}

	if(isset($_POST['EdtIcalResponsable'])) {
		if (!saveSetting("EdtIcalResponsable", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalResponsable !";
		}
	}
	else {
		if (!saveSetting("EdtIcalResponsable", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalResponsable !";
		}
	}
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==3)) {
	check_token();

	if(isset($_POST['EdtIcalUploadScolarite'])) {
		if (!saveSetting("EdtIcalUploadScolarite", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalUploadScolarite !";
		}
	}
	else {
		if (!saveSetting("EdtIcalUploadScolarite", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalUploadScolarite !";
		}
	}

	if(isset($_POST['EdtIcalUploadCpe'])) {
		if (!saveSetting("EdtIcalUploadCpe", "yes")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalUploadCpe !";
		}
	}
	else {
		if (!saveSetting("EdtIcalUploadCpe", "no")) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalUploadCpe !";
		}
	}
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==4)) {
	check_token();

	if(isset($_POST['EdtIcalFormatNomProf'])) {
		if (!saveSetting("EdtIcalFormatNomProf", $_POST['EdtIcalFormatNomProf'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalFormatNomProf !";
		}
	}

	if(isset($_POST['EdtIcalFormatNomMatière'])) {
		if (!saveSetting("EdtIcalFormatNomMatière", $_POST['EdtIcalFormatNomMatière'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre EdtIcalFormatNomMatière !";
		}
	}
}

if (isset($_POST['is_posted']) and ($msg=='')){
  $msg = "Les modifications ont été enregistrées !";
  $post_reussi=TRUE;
}

// on demande une validation si on quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?";
/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
/**
 * Entête de la page
 */
include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime="";
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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/edt/index_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
/**
 * Inclusion du gabarit
 */
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);

?>
