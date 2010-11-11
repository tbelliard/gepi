<?php
/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

//=============================
// REMONTé:
// Initialisations files
require_once("../lib/initialisations.inc.php");
//=============================

require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');

define('FPDF_FONTPATH','../fpdf/font/');
define('LargeurPage','210');
define('HauteurPage','297');

/*
// Initialisations files
require_once("../lib/initialisations.inc.php");
*/

require_once("./class_pdf.php");
require_once ("./liste.inc.php");

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();

$ok=isset($_POST['ok']) ? $_POST["ok"] : 0;

if ($ok==0) {
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	}

	//INSERT INTO droits VALUES ('/impression/parametres_impression_pdf.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes PDF; réglage des paramètres', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

//**************** EN-TETE **************************************
//$titre_page = "Impression de listes au format PDF <br />Choix des paramètres".$periode;
$titre_page = "Impression de listes au format PDF <br />Choix des paramètres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='./impression.php'>Impression rapide à l'unité</a>";
echo " | <a href='./impression_serie.php'>Impression en série</a>";
echo "</p>\n";

echo "<h3>Choix des paramètres&nbsp;: </h3>\n";

$tab_champs=array('marge_gauche', 'marge_droite', 'marge_haut', 'marge_bas', 'marge_reliure', 'avec_emplacement_trous', 'affiche_pp', 'avec_ligne_texte', 'ligne_texte', 'afficher_effectif', 'une_seule_page', 'h_ligne', 'l_colonne', 'l_nomprenom', 'nb_ligne_avant', 'h_ligne1_avant', 'nb_ligne_apres', 'encadrement_total_cellules', 'nb_cellules_quadrillees', 'zone_vide', 'hauteur_zone_finale');
$tab_val=array('10', '10', '10', '10', '1', '1', '1', '1', ' ', '1', '1', '8', '8', '40', '2', '25', '1', '1', '5', '1', '20');

for($loop=0;$loop<count($tab_champs);$loop++) {
	$nom=$tab_champs[$loop];
	$$nom=isset($_SESSION[$nom]) ? $_SESSION[$nom] : $tab_val[$loop];
}

echo "<div>\n
   <fieldset>\n";
		echo "<legend>Modifiez l'apparence du document PDF&nbsp;:</legend>\n";
		echo "<form method=\"post\" action=\"parametres_impression_pdf.php\" name=\"choix_parametres\">\n";
		echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" /><br />\n";
		echo "<br />\n";

		echo "<b>Définition des marges du document&nbsp;:</b></p>\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Marge à gauche&nbsp;:</td><td><input type=\"text\" name=\"marge_gauche\" size=\"2\" maxlength=\"2\" value=\"$marge_gauche\" /></td></tr>\n";
		echo "<tr><td>Marge à droite&nbsp;:</td><td><input type=\"text\" name=\"marge_droite\" size=\"2\" maxlength=\"2\" value=\"$marge_droite\" /></td></tr>\n";
		echo "<tr><td>Marge du haut&nbsp;:</td><td><input type=\"text\" name=\"marge_haut\" size=\"2\" maxlength=\"2\" value=\"$marge_haut\" /></td></tr>\n";
		echo "<tr><td>Marge du bas&nbsp;:</td><td><input type=\"text\" name=\"marge_bas\" size=\"2\" maxlength=\"2\" value=\"$marge_bas\" /></td></tr>\n";

		echo "<tr><td>Option marge reliure ?</td><td><input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_1\" value=\"1\" ";
		if($marge_reliure==1) {echo "checked ";}
		echo "/><label for='marge_reliure_1'> Oui</label> <input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_0\" value=\"0\" ";
		if($marge_reliure!=1) {echo "checked ";}
		echo "/><label for='marge_reliure_0'> Non</label></td></tr>\n";
		echo "<tr><td>Option emplacement des<br />perforations classeur  ?</td><td><input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_1\" value=\"1\" ";
		if($avec_emplacement_trous==1) {echo "checked ";}
		echo "/><label for='avec_emplacement_trous_1'> Oui</label> <input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_0\" value=\"0\" ";
		if($avec_emplacement_trous!=1) {echo "checked ";}
		echo "/><label for='avec_emplacement_trous_0'> Non</label></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";

		echo "<b>Informations à afficher sur le document&nbsp;:</b><br />\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Afficher le professeur responsable de la classe ?</td><td><input type=\"radio\" name=\"affiche_pp\" id=\"affiche_pp_1\" value=\"1\" ";
		if($affiche_pp==1) {echo "checked ";}
		echo "/><label for='affiche_pp_1'> Oui</label> <input type=\"radio\" name=\"affiche_pp\" id=\"affiche_pp_0\" value=\"0\" ";
		if($affiche_pp!=1) {echo "checked ";}
		echo "/><label for='affiche_pp_0'> Non</label></td></tr>\n";
		echo "<tr><td valign='top'>Afficher une ligne de texte avant le tableau  ?</td><td><input type=\"radio\" name=\"avec_ligne_texte\" id=\"avec_ligne_texte_1\" value=\"1\" ";
		if($avec_ligne_texte==1) {echo "checked ";}
		echo "/><label for='avec_ligne_texte_1'> Oui</label> <input type=\"radio\" name=\"avec_ligne_texte\" id=\"avec_ligne_texte_0\" value=\"0\" ";
		if($avec_ligne_texte!=1) {echo "checked ";}
		echo "/><label for='avec_ligne_texte_0'> Non</label><br />";
		echo "Texte&nbsp;: &nbsp;<input type=\"text\" name=\"ligne_texte\" size=\"50\" value=\"$ligne_texte\" /></td></tr>\n";
		echo "<tr><td>Afficher l'effectif de la classe ?</td><td><input type=\"radio\" name=\"afficher_effectif\" id=\"afficher_effectif_1\" value=\"1\" ";
		if($afficher_effectif==1) {echo "checked ";}
		echo "/><label for='afficher_effectif_1'> Oui</label> <input type=\"radio\" name=\"afficher_effectif\" id=\"afficher_effectif_0\" value=\"0\" ";
		if($afficher_effectif!=1) {echo "checked ";}
		echo "/><label for='afficher_effectif_0'> Non</label></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		echo "<b>Styles du tableau&nbsp;: </b><br />\n";
		echo "<table style='margin-left: 1em;' border='0'>\n";
		echo "<tr><td>Tout sur une seule page ?</td><td><input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_1\" value=\"1\" ";
		if($une_seule_page==1) {echo "checked ";}
		echo "/><label for='une_seule_page_1'> Oui</label> <input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_0\" value=\"0\" ";
		if($une_seule_page!=1) {echo "checked ";}
		echo "/><label for='une_seule_page_0'> Non</label></td></tr>\n";
		echo "<tr><td>Hauteur d'une ligne&nbsp;:</td><td><input type=\"text\" name=\"h_ligne\" size=\"2\" maxlength=\"2\" value=\"$h_ligne\" /> </td></tr>\n";
		echo "<tr><td>Largeur d'une colonne&nbsp;:</td><td><input type=\"text\" name=\"l_colonne\" size=\"2\" maxlength=\"2\" value=\"$l_colonne\" /> </td></tr>\n";
		echo "<tr><td>Largeur colonne Nom / Prénom&nbsp;:</td><td><input type=\"text\" name=\"l_nomprenom\" size=\"2\" maxlength=\"2\" value=\"$l_nomprenom\" /> </td></tr>\n";
		echo "<tr><td>Nombre ligne(s) avant&nbsp;:</td><td><input type=\"text\" name=\"nb_ligne_avant\" size=\"2\" maxlength=\"2\" value=\"$nb_ligne_avant\" /> \n";
		echo "<tr><td>Hauteur de la première ligne avant&nbsp;:</td><td><input type=\"text\" name=\"h_ligne1_avant\" size=\"2\" maxlength=\"$h_ligne1_avant\" value=\"25\" /> </td></tr>\n";
		echo "<tr><td>Nombre ligne(s) après&nbsp;:</td><td><input type=\"text\" name=\"nb_ligne_apres\" size=\"2\" maxlength=\"2\" value=\"$nb_ligne_apres\" /> </td></tr>\n";
		echo "<tr><td>Quadrillage total des cellules ?</td><td><input type=\"radio\" name=\"encadrement_total_cellules\" id=\"encadrement_total_cellules_1\" value=\"1\" ";
		if($encadrement_total_cellules==1) {echo "checked ";}
		echo "/><label for='encadrement_total_cellules_1'> Oui</label> [ <input type=\"radio\" name=\"encadrement_total_cellules\" id=\"encadrement_total_cellules_0\" value=\"0\" ";
		if($encadrement_total_cellules!=1) {echo "checked ";}
		echo "/><label for='encadrement_total_cellules_0'> Non</label> \n";
		echo "<tr><td>&nbsp;</td><td>Nombre de cellules quadrillées après le nom&nbsp;: <input type=\"text\" name=\"nb_cellules_quadrillees\" size=\"2\" maxlength=\"2\" value=\"$nb_cellules_quadrillees\" /> ] </td></tr>\n";
		echo "</table>\n";
		echo "<br />\n";

		echo "<b>Informations en bas du document&nbsp;: </b><br />\n";
		echo "&nbsp;&nbsp;Réserver une zone vide sous le tableau ? <input type=\"radio\" name=\"zone_vide\" id=\"zone_vide_1\" value=\"1\" ";
		if($zone_vide==1) {echo "checked ";}
		echo "/><label for='zone_vide_1'> Oui</label> <input type=\"radio\" name=\"zone_vide\" id=\"zone_vide_0\" value=\"0\" ";
		if($zone_vide!=1) {echo "checked ";}
		echo "/><label for='zone_vide_0'> Non</label><br />\n";
		echo "&nbsp;&nbsp;Hauteur de la zone&nbsp;: <input type=\"text\" name=\"hauteur_zone_finale\" size=\"2\" maxlength=\"2\" value=\"$hauteur_zone_finale\" /> (0 tout ce qui reste)<br />\n";

		echo "<input value=\"1\" name=\"ok\" type=\"hidden\" />\n";
		echo "<br />\n";
		echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" />\n";
		echo "<br />\n";
		echo "</form>\n";
   echo "</fieldset>\n
 </div>";
	require("../lib/footer.inc.php");

/*
marge_gauche
marge_droite
marge_haut
marge_bas
marge_reliure
avec_emplacement_trous
affiche_pp
avec_ligne_texte
ligne_texte
afficher_effectif
une_seule_page
h_ligne
l_colonne
l_nomprenom
nb_ligne_avant
h_ligne1_avant
nb_ligne_apres
encadrement_total_cellules
nb_cellules_quadrillees
zone_vide
hauteur_zone_finale
*/

} else { // if OK

	// On enregistre dans la session et on redirige vers impression_serie.php
	$_SESSION['marge_gauche']=isset($_POST['marge_gauche']) ? $_POST["marge_gauche"] : 10;
	$_SESSION['marge_droite']=isset($_POST['marge_droite']) ? $_POST["marge_droite"] : 10;
	$_SESSION['marge_haut']=isset($_POST['marge_haut']) ? $_POST["marge_haut"] : 10;
	$_SESSION['marge_bas']=isset($_POST['marge_bas']) ? $_POST["marge_bas"] : 10;
	$_SESSION['marge_reliure']=isset($_POST['marge_reliure']) ? $_POST["marge_reliure"] : 1;
	$_SESSION['avec_emplacement_trous']=isset($_POST['avec_emplacement_trous']) ? $_POST["avec_emplacement_trous"] : 1;
	$_SESSION['affiche_pp']=isset($_POST['affiche_pp']) ? $_POST["affiche_pp"] : 1;
	$_SESSION['avec_ligne_texte']=isset($_POST['avec_ligne_texte']) ? $_POST["avec_ligne_texte"] : 1;
	$_SESSION['ligne_texte']=isset($_POST['ligne_texte']) ? $_POST["ligne_texte"] : " ";
	$_SESSION['afficher_effectif']=isset($_POST['afficher_effectif']) ? $_POST["afficher_effectif"] : 1;
	$_SESSION['une_seule_page']=isset($_POST['une_seule_page']) ? $_POST["une_seule_page"] : 1;
	$_SESSION['h_ligne']=isset($_POST['h_ligne']) ? $_POST["h_ligne"] : 8;
	$_SESSION['l_colonne']=isset($_POST['l_colonne']) ? $_POST["l_colonne"] : 8;
	$_SESSION['l_nomprenom']=isset($_POST['l_nomprenom']) ? $_POST["l_nomprenom"] : 40;
	$_SESSION['nb_ligne_avant']=isset($_POST['nb_ligne_avant']) ? $_POST["nb_ligne_avant"] : 2;
	$_SESSION['h_ligne1_avant']=isset($_POST['h_ligne1_avant']) ? $_POST["h_ligne1_avant"] : 25;
	$_SESSION['nb_ligne_apres']=isset($_POST['nb_ligne_apres']) ? $_POST["nb_ligne_apres"] : 1;
	$_SESSION['encadrement_total_cellules']=isset($_POST['encadrement_total_cellules']) ? $_POST["encadrement_total_cellules"] : 1;
	$_SESSION['nb_cellules_quadrillees']=isset($_POST['nb_cellules_quadrillees']) ? $_POST["nb_cellules_quadrillees"] : 5;
	$_SESSION['zone_vide']=isset($_POST['zone_vide']) ? $_POST["zone_vide"] : 1;
	$_SESSION['hauteur_zone_finale']=isset($_POST['hauteur_zone_finale']) ? $_POST["hauteur_zone_finale"] : 20;
	
	//echo $_SESSION['avec_emplacement_trous'];
	
	header("Location: ./impression_serie.php");
	die();
}
?>
