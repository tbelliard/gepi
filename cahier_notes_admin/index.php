<?php
/**
 * Gestion des cahiers de textes
 * 
 * $_POST['activer'] activation/désactivation
 * $_POST['export_cn_ods'] autorisation de l'export au format OD
 * $_POST['referentiel_note'] referentiel de note
 * $_POST['note_autre_que_sur_referentiel'] note autre que sur referentiel
 * $_POST['is_posted']
 * 
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Gestion des carnets de notes";
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

if(isset($_POST['is_posted'])) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_carnets_notes", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}

	if (isset($_POST['export_cn_ods'])) {
		//if (!saveSetting("export_cn_ods", $_POST['export_cn_ods'])) {
		if (!saveSetting("export_cn_ods", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation de l'export au format ODS !";
		}
	}
	else{
		if (!saveSetting("export_cn_ods", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de l'export au format ODS !";
		}
	}

	if (isset($_POST['referentiel_note'])) {
		if (!saveSetting("referentiel_note", $_POST['referentiel_note'])) {
			$msg .= "Erreur lors de l'enregistrement du referentiel de note !";
		}
	}
	
	if (isset($_POST['note_autre_que_sur_referentiel'])) {
		if (!saveSetting("note_autre_que_sur_referentiel", $_POST['note_autre_que_sur_referentiel'])) {
			$msg .= "Erreur lors de l'enregistrement de note_autre_que_sur_referentiel !";
		}
	}

	if (isset($_POST['utiliser_sacoche'])) {
		if (!saveSetting("utiliser_sacoche", 'yes')) {
			$msg .= "Erreur lors de l'enregistrement de utiliser_sacoche !";
		}
	} else {
		if (!saveSetting("utiliser_sacoche", 'no')) {
			$msg .= "Erreur lors de l'enregistrement de utiliser_sacoche !";
		}
	}

	if (isset($_POST['sacocheUrl'])) {
		$sacocheUrl = $_POST['sacocheUrl'];
		if (mb_substr($sacocheUrl,mb_strlen($sacocheUrl)-1,1) == '/') {$sacocheUrl = mb_substr($sacocheUrl,0, mb_strlen($sacocheUrl)-1);} //on enleve le / a  la fin
	  	saveSetting("sacocheUrl", $_POST['sacocheUrl']);
	}

  	if (isset($_POST['sacoche_base'])) {
		saveSetting("sacoche_base", $_POST['sacoche_base']);
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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/cahier_notes_admin/index_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
/**
 * Inclusion du gabarit
 */
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);




?>
