<?php

/*
 * $Id: update_colonne_retenue.php 5989 2010-11-25 11:51:39Z crob $
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

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', '');;";
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

//require('sanctions_func_lib.php');

$id_incident=isset($_GET['id_incident']) ? $_GET['id_incident'] : NULL;
$login=isset($_GET['login']) ? $_GET['login'] : NULL;
$qualite=isset($_GET['qualite']) ? $_GET['qualite'] : NULL;

$csrf_alea=isset($_GET['csrf_alea']) ? $_GET['csrf_alea'] : NULL;


if((isset($id_incident))&&(isset($login))&&(isset($qualite))) {
	if($qualite=='Responsable') {
echo "<a href='../mod_ooo/retenue.php?mode=module_discipline&amp;id_incident=$id_incident&amp;ele_login=$login&amp;csrf_alea=$csrf_alea' title='Imprimer la retenue'><img src='../images/icons/print.png' width='16' height='16' alt='Imprimer Retenue' /></a>\n";
	}
	else {
		echo "&nbsp;";
	}
}
?>