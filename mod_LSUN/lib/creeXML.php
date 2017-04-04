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


$niveau_arbo = "2";

// Initialisations files
include_once("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

$msgErreur = "";


require_once("chargeXML.php");

if ($msgErreur) {
	echo "<strong>Votre base contient des erreurs qui ne permettent pas de créer le fichier<br /><br /></strong>";
	echo "<span style='color:red'>".$msgErreur."</span>";
} else {
	header('Content-Type: application/xml');
	echo $xml->saveXML();
	
}




