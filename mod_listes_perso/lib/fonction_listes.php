<?php

/*
 *
 * Copyright 2015 Régis Bouguin
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

function verifieTableCree() {
	global $mysqli;
	global $dbDb;
	
	// echo "création de mod_listes_perso_definition"."<br />" ;
	$sql_def = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_definition` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`nom` varchar(50) NOT NULL default '', "
	   . "`sexe` BOOLEAN default true, "
	   . "`classe` BOOLEAN default true, "
	   . "`photo` BOOLEAN default true, "
	   . "PRIMARY KEY  (`id`) "
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_def = mysqli_query($mysqli, $sql_def);
	if (!$query_def) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_def."<br />" ;
	}
	
	// echo "création de mod_listes_perso_colonnes"."<br />" ;
	$sql_col = "CREATE TABLE IF NOT EXISTS `mod_listes_perso_colonnes` ("
	   . "`id` int(11) NOT NULL auto_increment, "
	   . "`id_def` int(11) NOT NULL, "
	   . "`titre` varchar(30) NOT NULL default '', "
	   . "PRIMARY KEY  (`id`) "
	   . ") ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
	$query_col = mysqli_query($mysqli, $sql_col);
	if (!$query_col) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql_col."<br />" ;
	}

	// echo "création de mod_listes_perso_contenu"."<br />" ;	
}

function EnregistreDroitListes($ouvre) {
	global $mysqli;
	$sql = "INSERT INTO `setting` (`NAME`, `VALUE`) VALUES ('GepiListePersonnelles', '".$ouvre."') "
	   . "ON DUPLICATE KEY UPDATE VALUE = '".$ouvre."' ";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function DroitSurListeOuvert() {
	global $mysqli;
	$retour = FALSE;
	$sql = "SELECT `VALUE` FROM `setting` WHERE `NAME`='GepiListePersonnelles' ";
	$query = mysqli_query($mysqli, $sql);
	$valeur = $query->fetch_object()->VALUE;
	if ($query->num_rows && $valeur === 'y') {
		$retour = TRUE;
	}
	return $retour;
}

function chargeListe($id) {
	if (!$id) {
		nouvelleListe($id);
	} else {
		litBase($id);
	}
}

function nouvelleListe($id) {
	$_SESSION['liste_perso']['id'] = $id;
	$_SESSION['liste_perso']['nom'] = '';
	$_SESSION['liste_perso']['sexe'] = FALSE;
	$_SESSION['liste_perso']['classe'] = FALSE;
	$_SESSION['liste_perso']['photo'] = FALSE;
	$_SESSION['liste_perso']['colonnes'] = array();
}

function litBase($id) {
	echo 'On lit la base pour '.$id;
}

function sauveDefListe($idListe,$nomListe, $sexeListe, $classeListe, $photoListe, $nbColonneListe) {
	global $mysqli;
	$sql = "INSERT INTO `mod_listes_perso_definition` "
	   . "SET  "
	   . "`nom`= '$nomListe', "
	   . "`sexe`= '$sexeListe', "
	   . "`classe`= '$classeListe', "
	   . "`photo`= '$photoListe' ";
	if (strlen((string)$idListe) !== 0) {$sql .= ", `id`=$idListe ";}
	$sql .= ";";
	
	$query = mysqli_query($mysqli, $sql);
	if (!$query) {
		echo "Erreur lors de la création de la base ".mysqli_error($mysqli)."<br />" ;
		echo $sql."<br />" ;
		return FALSE;
	}
	return TRUE;
}