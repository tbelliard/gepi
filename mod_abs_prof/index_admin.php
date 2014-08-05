<?php
/**
 * Gestion des absences et remlacements de professeurs
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
$titre_page = "Gestion module Absences/remplacements profs";
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/index_admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs_prof/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Absences/remplacements de professeurs : Administration',
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
		if (!saveSetting("active_mod_abs_prof", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	$tab=array('AbsProfSaisieAbsScol','AbsProfProposerRemplacementScol','AbsProfAttribuerRemplacementScol','AbsProfSaisieAbsCpe','AbsProfProposerRemplacementCpe','AbsProfAttribuerRemplacementCpe');

	for($loop=0;$loop<count($tab);$loop++) {
		if(isset($_POST[$tab[$loop]])) {
			if (!saveSetting($tab[$loop], "yes")) {
				$msg = "Erreur lors de l'enregistrement du paramètre ".$tab[$loop]." !";
			}
		}
		else {
			if (!saveSetting($tab[$loop], "no")) {
				$msg = "Erreur lors de l'enregistrement du paramètre ".$tab[$loop]." !";
			}
		}
	}
}


if((isset($_POST['is_posted']))&&($_POST['is_posted']==3)) {
	check_token();

	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();

	$tab_user_mae=array();

	$sql="SELECT value FROM abs_prof_divers WHERE name='login_exclus';";
	$res_mae=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mae)>0) {
		while($lig_mae=mysqli_fetch_object($res_mae)) {
			$tab_user_mae[]=$lig_mae->value;
		}
	}

	$cpt_comptes_exclus_ajoutes=0;
	for($loop=0;$loop<count($login_user);$loop++) {
		if(!in_array($login_user[$loop], $tab_user_mae)) {
			$sql="INSERT INTO abs_prof_divers SET name='login_exclus', value='".$login_user[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$cpt_comptes_exclus_ajoutes++;
			}
		}
	}
	$msg="$cpt_comptes_exclus_ajoutes compte(s) exclu(s) des propositions de remplacements pris en compte.<br />";

	$cpt_comptes_exclus_supprimes=0;
	for($loop=0;$loop<count($tab_user_mae);$loop++) {
		if(!in_array($tab_user_mae[$loop], $login_user)) {
			$sql="DELETE FROM abs_prof_divers WHERE name='login_exclus' AND value='".$login_user[$loop]."';";
			$delete=mysqli_query($GLOBALS["mysqli"], $sql);
			if($delete) {
				$cpt_comptes_exclus_supprimes++;
			}
		}
	}

	$msg.="$cpt_comptes_exclus_supprimes compte(s) précédemment exclu(s) des propositions de remplacements ne le sont plus.<br />";
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==4)) {
	check_token();

	$matiere_exclue=isset($_POST['matiere_exclue']) ? $_POST['matiere_exclue'] : array();

	$tab_mae=array();

	$sql="SELECT value FROM abs_prof_divers WHERE name='matiere_exclue';";
	$res_mae=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mae)>0) {
		while($lig_mae=mysqli_fetch_object($res_mae)) {
			$tab_mae[]=$lig_mae->value;
		}
	}

	$cpt_matieres_exclues_ajoutees=0;
	for($loop=0;$loop<count($matiere_exclue);$loop++) {
		if(!in_array($matiere_exclue[$loop], $tab_mae)) {
			$sql="INSERT INTO abs_prof_divers SET name='matiere_exclue', value='".$matiere_exclue[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$cpt_matieres_exclues_ajoutees++;
			}
		}
	}
	$msg="$cpt_matieres_exclues_ajoutees matière(s) exclue(s) des propositions de remplacements prises en compte.<br />";

	$cpt_matieres_exclues_supprimees=0;
	for($loop=0;$loop<count($tab_mae);$loop++) {
		if(!in_array($tab_mae[$loop], $matiere_exclue)) {
			$sql="DELETE FROM abs_prof_divers WHERE name='matiere_exclue' AND value='".$matiere_exclue[$loop]."';";
			$delete=mysqli_query($GLOBALS["mysqli"], $sql);
			if($delete) {
				$cpt_matieres_exclues_supprimees++;
			}
		}
	}

	$msg.="$cpt_matieres_exclues_supprimees matière(s) précédemment exclue(s) des propositions de remplacements ne le sont plus.<br />";
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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_abs_prof/index_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
/**
 * Inclusion du gabarit
 */
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);

?>
