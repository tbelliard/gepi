<?php
/*
*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
//$niveau_arbo = 1;
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

$sql="SELECT 1=1 FROM droits WHERE id='/utilisateurs/cherche_user.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/utilisateurs/cherche_user.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Chercher un utilisateur',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Initialisation des variables
$login_user = isset($_POST["login_user"]) ? $_POST["login_user"] : (isset($_GET["login_user"]) ? $_GET["login_user"] : NULL);

if(!isset($login_user)) {
	header("Location: ../accueil.php?msg=Utilisateur non choisi");
	die();
}

$tab=get_info_user($login_user);
if(!isset($tab["statut"])) {
	header("Location: ../accueil.php?msg=Utilisateur non trouvé");
	die();
}
else {
	if($_SESSION['statut']=="administrateur") {
		if($tab["statut"]=="eleve") {
			header("Location: ../eleves/modify_eleve.php?eleve_login=".$login_user);
			die();
		}
		elseif($tab["statut"]=="responsable") {
			if(!isset($tab["pers_id"])) {
				header("Location: ../accueil.php?msg=Utilisateur non trouvé");
				die();
			}

			header("Location: ../responsables/modify_resp.php?pers_id=".$tab["pers_id"]);
			die();
		}
		else {
			header("Location: ../utilisateurs/modify_user.php?user_login=$login_user");
			die();
		}
	}
	else {
		if(($tab["statut"]=="eleve")&&(acces("/eleves/visu_eleve.php", $_SESSION["login"]))) {
			header("Location: ../eleves/visu_eleve.php?ele_login=".$login_user);
			die();
		}
		elseif($tab["statut"]=="responsable") {
			if(!isset($tab["pers_id"])) {
				header("Location: ../accueil.php?msg=Utilisateur non trouvé");
				die();
			}

			if(acces("/responsables/modify_resp.php", $_SESSION["login"])) {
				header("Location: ../responsables/modify_resp.php?pers_id=".$tab["pers_id"]);
				die();
			}
			else {
				// Ajouter un test sur un élève et pointer sur visu_eleve dans l'onglet responsables.
				if((!isset($tab["enfants"][0]))&&(acces("/eleves/visu_eleve.php", $_SESSION["login"]))) {
					header("Location: ../eleves/visu_eleve.php?ele_login=".$login_user."&onglet=responsables");
					die();
				}
				else {
					header("Location: ../accueil.php?msg=Accès non autorisé");
					die();
				}
			}
		}
		else {
			header("Location: ../accueil.php?msg=Accès non autorisé");
			die();

			//header("Location: ../utilisateurs/modify_user.php?user_login=$login_user");
			//die();
		}
	}
}

header("Location: ../accueil.php?msg=Accès non autorisé");
die();
?>
