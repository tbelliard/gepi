<?php

/*
 *
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/ajax_discipline.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajax', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/ajax_discipline.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajax', '');;";
$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/ajax_discipline.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_discipline/ajax_discipline.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Ajax',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_discipline')) {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

if((isset($_GET['modif_sanction']))&&($_GET['modif_sanction']=="etat_effectuee")&&(isset($_GET['id_sanction']))&&(preg_match("/^[0-9]{1,}$/", $_GET['id_sanction']))) {
	check_token();

	if((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))||
	(($_SESSION['statut']=='professeur')&&(sanction_saisie_par($_GET['id_sanction'], $_SESSION['login'])))) {
		$sql="SELECT effectuee FROM s_sanctions WHERE id_sanction='".$_GET['id_sanction']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<span style='color:red'>Identifiant de sanction inconnu (<em>".$_GET['id_sanction']."</em>)</span>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			if($lig->effectuee=="O") {
				$valeur_alt="N";
			}
			else {
				$valeur_alt="O";
			}

			$sql="UPDATE s_sanctions SET effectuee='".$valeur_alt."' WHERE id_sanction='".$_GET['id_sanction']."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$update) {
				echo "<span style='color:red'>Erreur</span>";
			}
			elseif($valeur_alt=="O") {
				echo "<span style='color:green'>O</span>";
			}
			elseif($valeur_alt=="N") {
				echo "<span style='color:red'>N</span>";
			}
		}
	}
	else {
		echo "<span style='color:red'>Modification non autorisée.</span>";
	}

	die();
}

?>
