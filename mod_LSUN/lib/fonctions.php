<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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

/**
 * Renvoie la première partie de l'année scolaire au format complet (2009/2010 ou 2009/10 ou 2009-2010 …)
 * 
 * @param String $_annee année scolaire
 * @return int début de l'année scolaire
 */
function LSUN_annee($_annee) {
	$expl = preg_split("/[^0-9]/", $_annee);
	$retour = intval($expl[0]);
	//$retour = ($expl[0]);
	return $retour;
}

/**
 * Récupère les données de la table utilisateurs
 *   
 * @global type $mysqli
 * @param String $login Login de l'utilisateur
 * @return Object mysqliQuery
 */
function getUtilisateur($login) {
	global $mysqli;
	$sql = "SELECT * FROM `utilisateurs` WHERE login = '".$login."' ";
	//echo "<br />".$sql."<br />";
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB->fetch_object();	
}

/**
 * Récupère les utilisateurs sur le statut
 * 
 * @global type $mysqli
 * @param string $statut statut recherché
 * @return type
 */
function getUtilisateurSurStatut($statut = "%") {
	global $mysqli;
	$sql = "SELECT login, nom, prenom FROM utilisateurs WHERE statut LIKE '".$statut."' ";
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

function getResponsables() {
	global $mysqli;
	$sql = "SELECT r.id, r.login, u.nom, u.prenom, u.civilite FROM lsun_responsables AS r "
		. "INNER JOIN utilisateurs AS u "
		. "ON u.login = r.login ";
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

function getPeriodes($myData = NULL) {
	global $mysqli;
	$sqlPeriodes = "SELECT DISTINCT num_periode FROM periodes ";
	if ($myData) {
		$sqlPeriodes .=  "WHERE id_classe IN (".$myData.") ";
	}
	$sqlPeriodes .=  "ORDER BY num_periode ";
	//echo $sqlPeriodes;
	$resultchargeDB = $mysqli->query($sqlPeriodes);	
	return $resultchargeDB;	
}

function getClasses() {
	global $mysqli;
	$sqlClasses = "SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe ";
	//echo $sqlClasses;
	$resultchargeDB = $mysqli->query($sqlClasses);	
	return $resultchargeDB;	
}


