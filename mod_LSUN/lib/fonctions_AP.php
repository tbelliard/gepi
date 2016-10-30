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
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 
*/

function getAPCommun() {
	
	global $mysqli;
	//global $selectionClasse;
	//$myData = implode(",", $selectionClasse);
	getEPIparClasse();
	$sqlGetEpi = "SELECT lac.* FROM lsun_ap_communs AS lac "
		. "ORDER BY periode , codeAP , id ";
	//echo $sqlGetEpi;
	$resultchargeDB = $mysqli->query($sqlGetEpi);
	return $resultchargeDB;
}

function getApAid() {
	global $mysqli;
	global $_AP;
	$in = implode(",",$_SESSION['afficheClasse']);
	if ($in) {$in = ','.$in;}
	$in = '0'.$in;
	
	$sqlAidAP = "SELECT indice_aid AS indice_aid, nom AS groupe , nom_complet AS description "
		. "FROM aid_config WHERE type_aid = $_AP ";
	echo '<br>'.$sqlAidAP.'<br>';
		
	$resultchargeDB = $mysqli->query($sqlAidAP);
	return $resultchargeDB;
}

