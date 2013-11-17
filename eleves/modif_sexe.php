<?php
/*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/eleves/modif_sexe.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/eleves/modif_sexe.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Eleves: Modification ajax du sexe d un eleve',
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

check_token();

$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : '');
$sexe=isset($_POST['sexe']) ? $_POST['sexe'] : (isset($_GET['sexe']) ? $_GET['sexe'] : '');
$mode_retour=isset($_POST['mode_retour']) ? $_POST['mode_retour'] : (isset($_GET['mode_retour']) ? $_GET['mode_retour'] : '');

$login_eleve_corr=preg_replace("/[^A-Za-z0-9_.-]/","",$login_eleve);
if($login_eleve_corr!=$login_eleve) {$login_eleve="";}

if(($login_eleve=='')||(($sexe!='M')&&($sexe!='F'))) {
	echo "<span style='color:red'>ERREUR</span>";
}
else {
	$sql="UPDATE eleves SET sexe='$sexe' WHERE login='$login_eleve';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
	if($update) {
		if($mode_retour=="image") {
			echo "<img src='../images/";
			if($sexe=='M') {
				echo "symbole_homme16.png";
			}
			else {
				echo "symbole_femme16.png";
			}
			echo "' width='16' height='16' title='$sexe' />";
		}
		else {
			echo $sexe;
		}
	}
	else {
		echo "<span style='color:red'>ERREUR</span>";
	}
}

?>
