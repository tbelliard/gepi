<?php
/*
 * $Id: index.php 2356 2008-09-05 14:02:27Z jjocal $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) || isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

$niveau_arbo = "2";
// Initialisations files
include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");
include('UnitTestUtilisateurProfessionnel.php');
include('UnitTestEleve.php');
include('UnitTestAbsenceSaisie.php');
include("../propel/logger/STACKLogger.php");
$logger = new STACKLogger();
Propel::setLogger($logger);


// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************

//require_once("../../lib/header.inc");
//**************** FIN EN-TETE *************
purgeDonneesTest($logger);

//recuperation d'un creneau
$time = mktime(0, 0, 0);
$creneau = EdtCreneauPeer::getEdtCreneauActuel($time);
if ($creneau != null) {
	echo('test recuperation d\'un creneau à minuit a <font color="red">echoue</font> <br><br/> : ');
	echo('creneau retourné : '.$creneau->getNomDefiniePeriode());
	echo(' alors qu\'on ne devrait pas avoir de retour en toute logique');
} else {
	echo('test recuperation d\'un creneau à minuit a reussi : pas de retour <br><br/>');
}

$time = mktime(8, 40, 0);
$creneau = EdtCreneauPeer::getEdtCreneauActuel($time);
if ($creneau == null) {
	echo('test recuperation d\'un creneau à 8h40 a <font color="red">echoue</font> : ');
	echo('pas de retour <br><br/>');
} else {
	echo('test recuperation d\'un creneau à 8h40 a reussi.<br><br/>');
}

$creneau = EdtCreneauPeer::getEdtCreneauActuel('8:40');
if ($creneau == null) {
	echo('test recuperation d\'un creneau à 8h40 a <font color="red">echoue</font> : ');
	echo('pas de retour <br><br/>');
} else {
	echo('test recuperation d\'un creneau à 8h40 a reussi.<br><br/>');
}

purgeDonneesTest($logger);
Propel::setLogger(null);

function purgeDonneesTest($logger) {
	echo "Purge des données<br/><br/>";

	echo "<br/>Fin Purge des données<br/><br/>";
}

?>
