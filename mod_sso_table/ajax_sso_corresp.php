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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_sso_table/ajax_sso_corresp.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_sso_table/ajax_sso_corresp.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Table SSO: Ajax',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(isset($_GET['delete'])) {
	check_token();

	$sql="SELECT * FROM sso_table_correspondance WHERE login_gepi='".mysqli_real_escape_string($GLOBALS['mysqli'], $_GET['delete'])."';";
	$res=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<span style='color:red'>Aucune correspondance trouvée pour le login ".$_GET['delete'].".</span>";
	}
	else {
		$lig=mysqli_fetch_object($res);
		$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='".mysqli_real_escape_string($GLOBALS['mysqli'], $_GET['delete'])."';";
		$del=mysqli_query($GLOBALS['mysqli'], $sql);
		if($del) {
			echo "<span style='color:green'>Correspondance ".$lig->login_sso." supprimée pour le login ".$_GET['delete'].".</span>";
		}
		else {
			echo "<span style='color:red'>Echec de la suppression de la correspondance ".$lig->login_sso." pour le login ".$_GET['delete'].".</span>";
		}
	}
	die();
}

?>
