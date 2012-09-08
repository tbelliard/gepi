<?php

/*
 *
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

$variables_non_protegees = 'yes';

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

$sql="SELECT 1=1 FROM droits WHERE id='/matieres/matiere_ajax_lib.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$sql="INSERT INTO droits VALUES ( '/matieres/matiere_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');";
	$insert=mysql_query($sql);
}

// SQL : INSERT INTO droits VALUES ( '/matieres/matiere_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/matieres/matiere_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


$matiere=isset($_GET['matiere']) ? $_GET['matiere'] : NULL;
$champ=isset($_GET['champ']) ? $_GET['champ'] : NULL;

$tab_champs=array('matiere', 'nom_complet', 'priority', 'categorie_id', 'matiere_aid', 'matiere_atelier');

if(!in_array($champ, $tab_champs)) {
	//echo "erreur";
	die();
}

$sql="SELECT $champ AS champ FROM matieres WHERE matiere='".mysql_real_escape_string($matiere)."';";
//echo "$sql";
$res=mysql_query($sql);
if(mysql_num_rows($res)>0) {
	$lig=mysql_fetch_object($res);
	echo $lig->champ;
}

?>
