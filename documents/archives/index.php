<?php
/*
* @version: $Id$
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

$niveau_arbo=2;

// Initialisations files
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/documents/archives/index.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/documents/archives/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='V',
description='Archives des CDT',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../../logout.php?auto=1");
	die();
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;

$confirmer_ecrasement=isset($_POST['confirmer_ecrasement']) ? $_POST['confirmer_ecrasement'] : (isset($_GET['confirmer_ecrasement']) ? $_GET['confirmer_ecrasement'] : 'n');

include('../../cahier_texte_2/cdt_lib.php');

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Archives";
require_once("../../lib/header.inc");
//**************** FIN EN-TETE *************

//debug_var();

echo "<p class='bold'><a href='../../cahier_texte_2/index.php'><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

// Selon le statut, pointer vers la page annee/index.php ou vers annee/cdt_LOGIN.php

// Si multisite, changer le dossier à parcourir.
// Il faut aussi qu'un compte non prof ne puisse pas parcourir des dossier d'autres établissements
// Mettre une valeur à tester en entête

$extension="php";

// A MODIFIER:
$dossier_etab=get_dossier_etab_cdt_archives();

if($dossier_etab=="") {
	echo "<p style='color:red'>Le dossier d'archivage de l'établissement n'a pas pu être identifié.</p>\n";
	require("../../lib/footer.inc.php");
	die();
}

if(!file_exists($dossier_etab)) {
	echo "<p style='color:red'>Aucune année n'a été archivée.</p>\n";
	require("../../lib/footer.inc.php");
	die();
}

$handle=opendir($dossier_etab);
$tab_file = array();
$n=0;
while ($file = readdir($handle)) {
	if (($file != '.') and ($file != '..') and ($file != 'index.html') and ($file != '.test')) {
		$tab_file[] = $file;
		$n++;
	}
}
closedir($handle);
//arsort($tab_file);
rsort($tab_file);

if(count($tab_file)==0) {
	echo "<p style='color:red'>Aucune année n'a été archivée.</p>\n";
	require("../../lib/footer.inc.php");
	die();
}

echo "<p><b>Liste des années archivées</b>&nbsp;:<br />\n";
for($i=0;$i<count($tab_file);$i++) {
	echo "$tab_file[$i]&nbsp;: ";
	if($_SESSION['statut']!="professeur") {
		echo "<a href='$dossier_etab/".$tab_file[$i]."/cdt/index_classes.".$extension."'>Index des classes</a>";
		echo " - ";
		echo "<a href='$dossier_etab/".$tab_file[$i]."/cdt/index_professeurs.".$extension."'>Index des professeurs</a>";
	}
	else {
		echo "<a href='$dossier_etab/".$tab_file[$i]."/cdt/cdt_".$_SESSION['login'].".".$extension."'>Mon CDT</a>";
	}
	echo "<br />\n";
}
echo "</p>\n";

echo "<p><br /></p>\n";

//echo "<p style='color:red'>A FAIRE: Evaluer le nom du dossier établissement selon le cas multisite ou non</p>\n";

require("../../lib/footer.inc.php");
die();

?>
