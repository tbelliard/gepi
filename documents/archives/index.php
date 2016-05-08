<?php
/*
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
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
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
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../../logout.php?auto=1");
	die();
}

if(getSettingValue('acces_archives_cdt')=="") {
	$acces="y";
}
elseif(getSettingAOui('acces_archives_cdt')) {
	$acces="y";
}
else {
	header("Location: ../../accueil.php?msg=Accès aux archives CDT non autorisé.");
	die();
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : NULL;

$confirmer_ecrasement=isset($_POST['confirmer_ecrasement']) ? $_POST['confirmer_ecrasement'] : (isset($_GET['confirmer_ecrasement']) ? $_GET['confirmer_ecrasement'] : 'n');

include('../../cahier_texte_2/cdt_lib.php');

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Archives";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

echo "<p class='bold'>";
if($_SESSION['statut']=='professeur') {
	if((getSettingAOui("active_cahiers_texte"))&&(getSettingValue("GepiCahierTexteVersion")=='2')) {
		echo "<a href='../../cahier_texte_2/index.php'>";
	}
	elseif(getSettingAOui("active_cahiers_texte")) {
		echo "<a href='../../cahier_texte/index.php'>";
	}
	else {
		// Si le CDT Gepi n'est plus actif, mais que l'on a maintenu l'accès aux archives:
		echo "<a href='../../accueil.php'>";
	}
}
else {echo "<a href='../../accueil.php'>";}
echo "<img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($_SESSION['statut']=='administrateur') {
	echo " | <a href='../../cahier_texte_2/archivage_cdt.php'>Archivage des cahiers de textes</a>";
	echo " | <a href='../../cahier_texte_admin/index.php'>Administration du module Cahiers de textes</a>";
}
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


$suppr_arch_cdt=isset($_POST['suppr_arch_cdt']) ? $_POST['suppr_arch_cdt'] : (isset($_GET['suppr_arch_cdt']) ? $_GET['suppr_arch_cdt'] : NULL);
$confirmer_suppression=isset($_POST['confirmer_suppression']) ? $_POST['confirmer_suppression'] : "n";
if((isset($suppr_arch_cdt))&&($_SESSION['statut']=='administrateur')) {
	check_token(false);
	if($confirmer_suppression=='y') {
		echo "<p>Suppression de l'archivage de CDT <b>".$suppr_arch_cdt."</b>&nbsp;: \n";
		if(deltree($dossier_etab."/".$suppr_arch_cdt,true)) {
			echo "<span style='color:green;'>SUCCES</span>";
		}
		else {
			echo "<span style='color:red;'>ECHEC</span>";
		}
		echo "</p>\n";
	}
	else {
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
		echo add_token_field();
		echo "<p>Vous souhaitez supprimer l'archivage de CDT <b>".$suppr_arch_cdt."</b><br />\n";
		echo "<input type='hidden' name='suppr_arch_cdt' value='$suppr_arch_cdt' />\n";
		echo "<input type='hidden' name='confirmer_suppression' value='y' />\n";
		echo "<input type='submit' value='Confirmer la suppression' />\n";
		echo "</p>\n";
		echo "</form>\n";
	}

	echo "<br />\n";
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

$lignes="";
for($i=0;$i<count($tab_file);$i++) {
	if($_SESSION['statut']!="professeur") {
		$lignes.="$tab_file[$i]&nbsp;: ";
		$lignes.="<a href='$dossier_etab/".$tab_file[$i]."/cdt/index_classes.".$extension."'>Index des classes</a>";
		$lignes.=" - ";
		$lignes.="<a href='$dossier_etab/".$tab_file[$i]."/cdt/index_professeurs.".$extension."'>Index des professeurs</a>";

		if($_SESSION['statut']=='administrateur') {
			$lignes.=" - <a href='".$_SERVER['PHP_SELF']."?suppr_arch_cdt=".$tab_file[$i].add_token_in_url()."'><img src='../../images/delete16.png' width='16' height='16' /> Supprimer</a>";
		}
		$lignes.="<br />\n";
	}
	elseif(file_exists($dossier_etab."/".$tab_file[$i]."/cdt/cdt_".$_SESSION['login'].".".$extension)) {
		$lignes.="$tab_file[$i]&nbsp;: ";
		$lignes.="<a href='$dossier_etab/".$tab_file[$i]."/cdt/cdt_".$_SESSION['login'].".".$extension."'>Mon CDT</a>";
		$lignes.="<br />\n";
	}
}

if($lignes!="") {
	echo "<p><b>Liste des années archivées</b>&nbsp;:<br />\n";
	echo $lignes;
	echo "</p>\n";
}
else {
	echo "<p>Aucune donnée n'est archivée (<i>pour vous</i>).</p>\n";
}

echo "<p><br /></p>\n";

//echo "<p style='color:red'>A FAIRE: Evaluer le nom du dossier établissement selon le cas multisite ou non</p>\n";

require("../../lib/footer.inc.php");
die();

?>
