<?php
/* $Id$ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// INSERT INTO droits VALUES('/mod_notanet/choix_generation_csv.php','V','F','F','F','F','F','F','F','Génération du CSV pour Notanet','');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================


//$extract_mode=isset($_POST['extract_mode']) ? $_POST['extract_mode'] : (isset($_GET['extract_mode']) ? $_GET['extract_mode'] : NULL);


//**************** EN-TETE *****************
$titre_page = "Notanet: Génération du CSV";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Bibliothèque pour Notanet et Fiches brevet
include("lib_brevets.php");

echo "<div class='noprint'>\n";
echo "<p class='bold'><a href='../accueil.php'>Accueil</a> | <a href='index.php'>Retour à l'accueil Notanet</a>";

$sql="SELECT DISTINCT type_brevet FROM notanet_ele_type ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association élève/type de brevet n'a encore été réalisée.<br />Commencez par <a href='select_eleves.php'>sélectionner les élèves</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT DISTINCT type_brevet FROM notanet_corresp WHERE $sql_indices_types_brevets ORDER BY type_brevet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_type_brevet=mysqli_num_rows($res);
//if(mysql_num_rows($res)==0) {
if($nb_type_brevet==0) {
	echo "</p>\n";
	echo "</div>\n";

	echo "<p>Aucune association matières/type de brevet n'a encore été réalisée.<br />Commencez par <a href='select_matieres.php'>sélectionner les matières</a></p>\n";

	require("../lib/footer.inc.php");
	die();
}

echo "</p>\n";
echo "</div>\n";

$lignes_export_complete="";
$lignes_export_complete2="";
echo "<h2>Export(s) notanet</h2>

<p>Voulez-vous: ";
//echo "<br />\n";
echo "</p>\n";
echo "<ul>\n";
if($nb_type_brevet>1) {
	echo "<li><a href='generer_csv.php?extract_mode=tous".add_token_in_url()."'>Générer le CSV Notanet pour tous les élèves associés à un type de brevet.</a></li>\n";
	$lignes_export_complete.="<li><a href='generer_csv.php?extract_mode=tous&amp;avec_nom_prenom=y".add_token_in_url()."'>Générer un export avec nom, prénom, classe pour tous les élèves associés à un type de brevet.</a></li>\n";
	$lignes_export_complete2.="<li><a href='generer_csv.php?extract_mode=tous&amp;avec_nom_prenom=y&amp;total_seul=y".add_token_in_url()."'>Générer un export avec nom, prénom, classe pour tous les élèves associés à un type de brevet.</a></li>\n";
}
//echo "<li><a href='".$_SERVER['PHP_SELF']."?extract_mode=select'></a>Extraire une sélection d'élèves</li>\n";
while($lig=mysqli_fetch_object($res)) {
	echo "<li><a href='generer_csv.php?extract_mode=".$lig->type_brevet.add_token_in_url()."'>Générer le CSV Notanet pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	$lignes_export_complete.="<li><a href='generer_csv.php?extract_mode=".$lig->type_brevet."&amp;avec_nom_prenom=y".add_token_in_url()."'>Générer un export avec nom, prénom, classe pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
	$lignes_export_complete2.="<li><a href='generer_csv.php?extract_mode=".$lig->type_brevet."&amp;avec_nom_prenom=y&amp;total_seul=y".add_token_in_url()."'>Générer un export avec nom, prénom, classe pour ".$tab_type_brevet[$lig->type_brevet]."</a></li>\n";
}
echo "</ul>\n";

echo "<p><br /></p>

<h2>Exports complémentaires non conformes</h2>

<p>Certains établissements demandent à pouvoir effectuer des exports destinés à récupérer le total notanet pour chaque élève avec un nom, prénom d'élève plutôt que l'export Notanet qui lui ne donne que l'INE de l'élève.<br />
<strong>ATTENTION&nbsp;:</strong> Ces exports ne conviennent pas pour notanet.</p>

<!--
<p>Export avec toutes les lignes de l'export Notanet&nbsp;:</p>
<ul>
$lignes_export_complete
</ul>
-->

<p>Export avec juste le total Notanet&nbsp;:</p>
<ul>
$lignes_export_complete2
</ul>
";


require("../lib/footer.inc.php");
?>
