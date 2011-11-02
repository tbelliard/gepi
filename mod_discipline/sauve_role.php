<?php

/*
 * $Id: sauve_role.php 5989 2010-11-25 11:51:39Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôles incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôles incident', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(strtolower(substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$id_incident=isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL;
$login=isset($_GET['login']) ? $_GET['login'] : NULL;
$qualite=isset($_GET['qualite']) ? $_GET['qualite'] : NULL;

if((isset($id_incident))&&(isset($login))&&(isset($qualite))) {
	check_token();

	/*
	echo "\$id_incident=$id_incident<br />";
	echo "\$login=$login<br />";
	echo "\$qualite=$qualite<br />";
	*/
	$sql="UPDATE s_protagonistes SET qualite='$qualite' WHERE id_incident='$id_incident' AND login='$login';";
	$update=mysql_query($sql);
	if($update) {
		//echo "Mise &agrave; jour de la qualit&eacute; \"$qualite\" effectu&eacute;e pour $login.";
		echo "Mise &agrave; jour du r&ocirc;le \"".htmlentities($qualite)."\" effectu&eacute; pour $login.";
	}
	else {
		//echo "Echec de la mise &agrave; jour de la qualit&eacute; \"$qualite\" pour $login.";
		echo "Echec de la mise &agrave; jour du r&ocirc;le \"".htmlentities($qualite)."\" pour $login.";
	}
}
?>