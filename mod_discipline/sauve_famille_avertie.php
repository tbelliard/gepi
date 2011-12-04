<?php

/*
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/sauve_famille_avertie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', '');;";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$id_incident=isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL;
$login=isset($_GET['login']) ? $_GET['login'] : NULL;
$avertie=isset($_GET['avertie']) ? $_GET['avertie'] : NULL;

if((isset($id_incident))&&(isset($login))&&(isset($avertie))) {
	check_token();

	/*
	echo "\$id_incident=$id_incident<br />";
	echo "\$login=$login<br />";
	echo "\$avertie=$avertie<br />";

	$sql="SELECT 1=1 FROM s_comm_incident WHERE id_incident='$id_incident' AND login='$login';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		$sql="INSERT INTO s_comm_incident SET avertie='$avertie', id_incident='$id_incident', login='$login';";
		$insert=mysql_query($sql);
		if($insert) {
			echo "Famille de $login avertie.";
		}
		else {
			echo "Echec de l'enregistrement pour la famille de $login.";
		}
	}
	else {
		$sql="UPDATE s_comm_incident SET avertie='$avertie' WHERE id_incident='$id_incident' AND login='$login';";
		$update=mysql_query($sql);
		if($update) {
			echo "Famille de $login avertie.";
		}
		else {
			echo "Echec de l'enregistrement pour la famille de $login.";
		}
	}
	*/
	$sql="UPDATE s_protagonistes SET avertie='$avertie' WHERE id_incident='$id_incident' AND login='$login';";
	$update=mysql_query($sql);
	if($update) {
		echo "Famille de $login avertie: ";
		if($avertie=="O") {echo "Oui";} else {echo "Non";}
	}
	else {
		echo "Echec de l'enregistrement pour la famille de $login.";
	}
}
?>
