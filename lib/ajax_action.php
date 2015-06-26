<?php

/*
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

$sql="SELECT * FROM droits WHERE id='/lib/ajax_action.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/lib/ajax_action.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Action ajax',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//check_token();

header('Content-Type: text/html; charset=utf-8');

//debug_var();

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$target=isset($_POST['target']) ? $_POST['target'] : (isset($_GET['target']) ? $_GET['target'] : "");

if(($mode=="actions_conseil_classe")&&(isset($id_classe))&&(preg_match("/^[0-9]{1,}$/", $id_classe))&&(in_array($_SESSION['statut'], array('professeur', 'scolarite')))) {
	echo affiche_choix_action_conseil_de_classe($id_classe, $target);
	die();
}

$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
//$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);

//if(($mode=="notes_ele_grp_per")&&(isset($ele_login))&&(isset($id_groupe))&&(isset($periode))) {
if(($mode=="notes_ele_grp_per")&&(isset($ele_login))&&(isset($id_groupe))) {
	echo "<div align='center'>
	<p>".get_info_grp($id_groupe)."</p>
	".affiche_tableau_notes_ele($ele_login, $id_groupe)."
</div>";
	die();
}

if(($mode=="tab_avis_conseil")&&(isset($ele_login))) {
	//necessaire_bull_simple();
	//echo affiche_tab_avis_conseil($ele_login);
	echo affiche_tab_avis_conseil($ele_login, "n", "n");
	//include("../lib/footer_tab_infobulle.php");
	// Je n'arrive pas à obtenir l'infobulle depuis celle qui affiche le tableau des conseils.
	die();
}

$id_saisie=isset($_GET['id_saisie']) ? $_GET['id_saisie'] : NULL;
if(($mode=="visu_abs")&&(isset($id_saisie))&&(acces("/mod_abs2/visu_saisie.php", $_SESSION['statut']))) {

	if (getSettingValue("active_module_absence")!='2') {
		die("<p style='color:red'>Le module n'est pas activé.</p>");
	}

	// Ca ne fonctionne pas
	//include("../mod_abs2/visu_saisie.php?id_saisie=".$id_saisie);
	//require("../mod_abs2/visu_saisie.php?id_saisie=".$id_saisie);
	$_SESSION["id_saisie"]=$id_saisie;
	//$menu=true;
	$_SESSION['affichage_depuis_edt2']=true;
	include("../mod_abs2/visu_saisie.php");
	$_SESSION['affichage_depuis_edt2']=false;
	die();
}

?>
