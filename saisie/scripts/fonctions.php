<?php

/*
*
* Copyright 2016 Bouguin Régis
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

function anneeScolaire() {
	//EdtHelper::getPremierJourAnneeScolaire()
	//$retour = EdtHelper::getPremierJourAnneeScolaire()->format("yy");
	//return $retour;
}

/**
 * Renvoie tous les éléments de programme
 * 
 * @global DB_connect $mysqli
 * @return mysqli_query
 */
function getToutElemProg() {
	global $mysqli;
	
	$sql = "SELECT * FROM matiere_element_programme";
	
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
	
}

function getGroupElemProg($groupe, $anneeScolaire, $periode) {
	global $mysqli;
	
	$sql = "SELECT * FROM matiere_element_programme AS mep "
		. "INNER JOIN j_mep_groupe AS jmp "
		. "ON mep.id = jmp.idEP "
		. "WHERE jmp.periode = '".$periode."' "
		. "AND jmp.annee = '".$anneeScolaire."' "
		. "AND jmp.idGroupe = '".$groupe."' ";
	
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}



