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

/*===== aid_config =====*/
function Get_famille_aid ($indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM aid_config WHERE indice_aid='".$indice_aid."' ;";
	$retour = mysqli_query($mysqli, $sql); 
	return $retour;
}

function Multiples_possible ($indice_aid) {
	$famille = Get_famille_aid ($indice_aid);
	$retour = FALSE;
	if ($famille && ($famille->fetch_object()->autoriser_inscript_multiples === 'y')) {
		$retour = TRUE;
	}
	return $retour;
}

/*===== aid =====*/

function Sauve_definition_aid ($aid_id , $aid_nom , $aid_num , $indice_aid , $sous_groupe, $inscrit_direct) {
	global $mysqli;
	$sql = "INSERT INTO aid "
	   . "SET id = '$aid_id', nom='$aid_nom', numero='$aid_num', indice_aid='$indice_aid', "
	   . "sous_groupe='$sous_groupe', inscrit_direct='$inscrit_direct' "
	   . "ON DUPLICATE KEY "
	   . "UPDATE nom='$aid_nom', numero='$aid_num', sous_groupe='$sous_groupe', inscrit_direct='$inscrit_direct'";
	//die($sql) ;
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
	$sql="SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY numero , nom;";
	//echo "$sql<br />";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

/**
 * 
 * @global type $mysqli
 * @param Const $ordre DESC → descendant (défaut) ASC→ascendant
 * @return integer
 */
function Dernier_id ($ordre = "DESC") {
	global $mysqli;
	$sql = "SELECT CAST( aid.id AS SIGNED INTEGER ) AS idAid FROM aid ORDER BY idAid ".$ordre;
	$return = mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($return)==0) {
		$retour=0;
	}
	else {
		$retour = $return->fetch_object()->idAid;
	}
	return $retour;
}

function a_parent ($aid_id, $indice_aid = NULL) {
	global $mysqli;
	$retour = FALSE;
	$filtre = $indice_aid ? " AND indice_aid='".$indice_aid."' " : "";
	$sql = "SELECT * FROM aid "
	   . "WHERE id=".$aid_id.$filtre." "
	   . "AND sous_groupe='y' ";
	$return = mysqli_query($mysqli, $sql);
	if ($return && $return->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function Categorie_a_enfants ($indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM aid "
	   . "WHERE indice_aid='".$indice_aid."' "
	   . "AND sous_groupe='y' ";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function Eleve_inscrit_direct($aid_id, $indice_aid = NULL) {
	global $mysqli;
	$retour = FALSE;
	$filtre = $indice_aid ? " AND indice_aid='".$indice_aid."' " : "";
	$sql = "SELECT * FROM aid "
	   . "WHERE id=".$aid_id.$filtre." "
	   . "AND inscrit_direct='y' ";
	$return = mysqli_query($mysqli, $sql);
	if ($return && $return->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

/*===== aid_sous_groupes =====*/

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

/*===== j_aid_utilisateurs =====*/
		
function Prof_deja_membre ($reg_prof_login, $aid_id, $indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM j_aid_utilisateurs "
	   . "WHERE (id_utilisateur = '$reg_prof_login' AND id_aid = '$aid_id' AND indice_aid='$indice_aid')";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Sauve_prof_membre ($reg_prof_login, $aid_id, $indice_aid) {
	global $mysqli;
	$sql = "INSERT INTO j_aid_utilisateurs "
	   . "SET id_utilisateur= '$reg_prof_login', id_aid = '$aid_id', indice_aid='$indice_aid'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

/* ===== j_aid_utilisateurs_gest =====*/

function Prof_deja_gestionnaire ($reg_prof_login, $aid_id, $indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM j_aid_utilisateurs_gest "
	   . "WHERE (id_utilisateur = '$reg_prof_login' and id_aid = '$aid_id' and indice_aid='$indice_aid')";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;
}

function Sauve_prof_gestionnaire ($reg_prof_login, $aid_id, $indice_aid) {
	global $mysqli;
	$sql = "INSERT INTO j_aid_utilisateurs_gest SET id_utilisateur= '$reg_prof_login', id_aid = '$aid_id', indice_aid='$indice_aid'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

/* ===== j_aid_eleves =====*/

function Eleve_est_deja_membre ($login, $indice_aid, $id_aid = NULL) {
	global $mysqli;
	$sql = "SELECT DISTINCT login "
	   . "FROM j_aid_eleves "
	   . "WHERE indice_aid = '".$indice_aid."' "
	   . "AND login = '".$login."' ";
	if ($id_aid !== NULL){
		$sql .= "AND id_aid = '".$id_aid."' ";
	}
	//echo $sql;
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Extrait_eleves_deja_membres ($aid_id, $indice_aid) {
	global $mysqli;
	$sql = "SELECT * FROM j_aid_eleves "
	   . "WHERE (indice_aid='$indice_aid' and id_aid='$aid_id')";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Extrait_eleves_sur_aid_id ($aid_id, $sous_groupe = NULL) {
	global $mysqli;
	if ($sous_groupe !== NULL) {
		$filtre =" INNER JOIN j_aid_eleves j2 ON ( e.login = j2.login "
		   . "AND j2.id_aid = '".$sous_groupe."' ) ";
	} else {
		$filtre ="";
	}
	$sql = "SELECT distinct e.login, e.nom, e.prenom, e.elenoet "
	   . "FROM eleves e "
	   . "LEFT JOIN j_aid_eleves j "
	   . "ON (e.login = j.login  AND j.id_aid = '".$aid_id."') ".$filtre;
	$sql .= "WHERE j.login is null "
	   . "ORDER by e.nom, e.prenom";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Extrait_eleves_sur_indice_aid ($indice_aid) {
	global $mysqli;
	$sql = "SELECT e.login, e.nom, e.prenom, e.elenoet "
	   . "FROM eleves e "
	   . "LEFT JOIN j_aid_eleves j "
	   . "ON (e.login = j.login  AND j.indice_aid = '".$indice_aid."') "
	   . "WHERE j.login is null "
	   . "ORDER by e.nom, e.prenom";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Sauve_eleve_membre($id_aid, $indice_aid, $login_eleve) {
	global $mysqli;
	$sql = "INSERT INTO j_aid_eleves "
	   . "SET login='".$login_eleve."', id_aid='".$id_aid."', indice_aid='".$indice_aid."'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

/*===== j_aid_eleves_resp =====*/

function Supprime_eleve_responsable($aid_id, $indice_aid) {
	global $mysqli;
	$sql = "DELETE FROM j_aid_eleves_resp WHERE id_aid='".$aid_id."' AND indice_aid='".$indice_aid."'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}

function Sauve_eleve_responsable($aid_id, $indice_aid, $login_eleve) {
	global $mysqli;
	$sql = "INSERT INTO j_aid_eleves_resp "
	   . "SET id_aid='$aid_id', login='$login_eleve', indice_aid='$indice_aid'";
	$retour = mysqli_query($mysqli, $sql);
	return $retour;	
}




