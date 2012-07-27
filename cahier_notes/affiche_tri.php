<?php
/**
*
* @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
 * @package Carnet_de_notes
 * @subpackage Conteneur
 * @license GNU/GPL, 
 * @see Session::security_check()
 * @see checkAccess()
 * @see get_nom_prenom_eleve()
 */

/*
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

/**
 * Fichiers d'initialisation
 */
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


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/affiche_tri.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_notes/affiche_tri.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='V',
description='Tri',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$titre=isset($_POST["titre"]) ? $_POST["titre"] : (isset($_GET["titre"]) ? $_GET["titre"] : NULL);
$chaine1= isset($_POST["chaine1"]) ? $_POST["chaine1"] : (isset($_GET["chaine1"]) ? $_GET["chaine1"] : NULL);
$chaine2= isset($_POST["chaine2"]) ? $_POST["chaine2"] : (isset($_GET["chaine2"]) ? $_GET["chaine2"] : NULL);

$mode=isset($_GET['mode']) ? $_GET['mode'] : "";

header('Content-Type: text/xml; charset=utf-8');

if($mode!="ajax") {
	//**************** EN-TETE *****************
	//$titre_page = "Saisie des notes";
  /**
   * Entête de la page
   */
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************
}

//debug_var();

$tab1=explode('|',$chaine1);
$tab2=explode('|',$chaine2);

$tab3=array();
$rg=array();

echo "<table width='100%' class='boireaus'>\n";
echo "<tr>\n";
echo "<td>\n";
	$alt=1;
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Note</th>\n";
	echo "</tr>\n";
	for($i=0;$i<count($tab1);$i++) {
		$rg[$i]=$i;

		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
	
		echo "<td>\n";
		echo get_nom_prenom_eleve($tab1[$i]);
		echo "</td>\n";
	
		echo "<td>\n";
		if (isset ($tab2[$i])) {
		echo $tab2[$i];
		  if(($tab2[$i]!='')&&(preg_match("/^[0-9.]*$/",$tab2[$i]))&&($tab2[$i]>=0)) {
			  $tab3[$i]=$tab2[$i];
		  }
		  else {
			  $tab3[$i]=-1;
		  }
		}
		echo "</td>\n";
	
		echo "</tr>\n";
	}
	echo "</table>\n";
echo "</td>\n";

array_multisort ($tab3, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);

echo "<td>\n";
	$alt=1;
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th>Elève</th>\n";
	echo "<th>Note</th>\n";
	echo "</tr>\n";
	for($i=0;$i<count($tab1);$i++) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
	
		echo "<td>\n";
		echo get_nom_prenom_eleve($tab1[$rg[$i]]);
		echo "</td>\n";
	
		echo "<td>\n";
		if (isset ($tab2[$i])) {
		  echo $tab2[$rg[$i]];
		}
		echo "</td>\n";
	
		echo "</tr>\n";
	}
	echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

if($mode!="ajax") {
  /**
   * Pied de page
   */
	require("../lib/footer.inc.php");
}
?>
