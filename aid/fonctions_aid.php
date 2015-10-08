<?php

/*
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


function Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe) {
	global $mysqli;
	$sql = "INSERT INTO aid "
	   . "SET id = '$aid_id', nom='$aid_nom', numero='$aid_num', indice_aid='$indice_aid', sous_groupe='$sous_groupe'"
	   . "ON DUPLICATE KEY "
	   . "UPDATE nom='$aid_nom', numero='$aid_num', sous_groupe='$sous_groupe'";
	$retour = mysqli_query($mysqli, $sql); 
	return $retour;
}

function Extrait_aid_sur_nom ($aid_nom , $indice_aid = NULL) {
	global $mysqli;
	$critere = $indice_aid != NULL ? " AND indice_aid='".$indice_aid."' " : "";
	$sql = "SELECT * FROM aid WHERE (nom='".$aid_nom."'".$critere.")";
	$retour= mysqli_query($mysqli, $sql);
	return $retour;
}

function Extrait_aid_sur_id ($id, $indice_aid = NULL) {
	global $mysqli;
	$critere = $indice_aid != NULL ? " AND indice_aid='".$indice_aid."' " : "";
	$sql = "SELECT * FROM aid WHERE (id='".$id."'".$critere.")";
	$retour= mysqli_query($mysqli, $sql);
	return $retour;
}
	
function Extrait_aid_sur_indice_aid ($indice_aid) {
	global $mysqli;
	$sql="SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY numero , nom";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

/**
 * 
 * @global type $mysqli
 * @param Const $ordre DESC → descendant (défaut) ASC→ascendant
 * @return integer
 */
function Dernier_id ($ordre = DESC) {
	global $mysqli;
	$sql = "SELECT CAST( aid.id AS SIGNED INTEGER ) AS idAid FROM aid ORDER BY idAid ".$ordre;
	$return = mysqli_query($mysqli, $sql);
	$retour = $return->fetch_object()->idAid;
	return $retour;
}


function Sauve_sous_groupe ($aid_id, $parent) {
	global $mysqli;
	$sql = "INSERT INTO `aid_sous_groupes` (aid , parent) VALUES ('".$aid_id."','".$parent."')"
	   . "ON DUPLICATE KEY "
	   . "UPDATE parent='".$parent."' ;";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function Efface_sous_groupe($aid_id) {
	global $mysqli;
	$sql = "DELETE FROM `aid_sous_groupes` WHERE `aid` = '".$aid_id."' ;";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function a_parent ($aid_id, $indice_aid = NULL) {
	global $mysqli;	
	$retour = FALSE;
	$filtre = $indice_aid ? " AND indice_aid='".$indice_aid."' " : "";
	$sql = "SELECT * FROM aid WHERE id=".$aid_id.$filtre." AND sous_groupe='y' ";
	$return = mysqli_query($mysqli, $sql);
	if ($return && $return->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function Extrait_parent ($aid_id) {
	global $mysqli;
	$sql = "SELECT parent FROM `aid_sous_groupes` WHERE `aid` LIKE '".$aid_id."'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function Extrait_info_parent ($aid_id) {
	$retour = NULL;
	if (Extrait_parent ($aid_id) && Extrait_parent ($aid_id)->num_rows) {
		$id = Extrait_parent ($aid_id)->fetch_object()->parent;
		$return = Extrait_aid_sur_id ($id);
		if ($return)  {
			$retour = $return;
		}		
	}
	return $retour;
}
			
function Categorie_a_enfants ($indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM aid WHERE indice_aid='".$indice_aid."' AND sous_groupe='y' ";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}
		
			
			