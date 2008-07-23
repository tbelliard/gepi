<?php
/*
 * Last modification  : 10/02/2007
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
	};

	//INSERT INTO droits VALUES ('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; réglage des paramètres', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

//**************** EN-TETE **************************************
//$titre_page = "Impression de listes au format PDF <br />Choix des paramètres".$periode;
$titre_page = "Impression des avis (PDF) | Choix des paramètres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE **********************************

echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href='../saisie/impression_avis.php'>Impression des avis</a>";
echo "</p>\n";

echo "<h3>Choix des paramètres : </h3>\n";

echo "<div>\n
   <fieldset>\n";
       echo "<legend>Modifiez l'apparence du document PDF :</legend>\n";
	   echo "<form method=\"post\" action=\"../impression/parametres_impression_pdf_avis.php\" name=\"choix_parametres\">\n";
	   echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" /><br />\n";
       echo "<br />\n";
	   echo "<b>Définition des marges du document :</b><br />\n";
	   echo "&nbsp;&nbsp;Marge à gauche : <input type=\"text\" name=\"marge_gauche\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge à droite : <input type=\"text\" name=\"marge_droite\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge du haut : <input type=\"text\" name=\"marge_haut\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Marge du bas : <input type=\"text\" name=\"marge_bas\" size=\"2\" maxlength=\"2\" value=\"10\" /> <br />\n";
	   echo "&nbsp;&nbsp;Option marge reliure ? <input type=\"radio\" name=\"marge_reliure\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"marge_reliure\" value=\"0\" /> Non<br />\n";
	   echo "&nbsp;&nbsp;Option emplacement des perforations classeur  ? <input type=\"radio\" name=\"avec_emplacement_trous\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"avec_emplacement_trous\" value=\"0\" /> Non<br />\n";
	   echo "<br />\n";
	   echo "<b>Informations à afficher sur le document :</b><br />\n";
	   echo "&nbsp;&nbsp;Afficher le professeur responsable de la classe ? <input type=\"radio\" name=\"affiche_pp\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"affiche_pp\" value=\"0\" /> Non<br />\n";
	   echo "<br />\n";
	   echo "<b>Styles du tableau : </b><br />\n";
	   echo "&nbsp;&nbsp;Tout sur une seule page ? <input type=\"radio\" name=\"une_seule_page\" value=\"1\" checked /> Oui <input type=\"radio\" name=\"une_seule_page\" value=\"0\" /> Non<br />\n";
	   echo "&nbsp;&nbsp;Hauteur d'une ligne : <input type=\"text\" name=\"h_ligne\" size=\"2\" maxlength=\"2\" value=\"8\" /> <br />\n";
	   echo "&nbsp;&nbsp;Largeur colonne Nom / Prénom : <input type=\"text\" name=\"l_nomprenom\" size=\"2\" maxlength=\"2\" value=\"40\" /> <br />\n";
	   echo "<input value=\"1\" name=\"ok\" type=\"hidden\" />\n";
	   echo "<br />\n";
	   echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" />\n";
       echo "<br />\n";
     echo "</form>\n";
   echo "</fieldset>\n
 </div>";
	require("../lib/footer.inc.php");

} else { // if OK
  // On enregistre dans la session et on redirige vers impression_serie.php
  $_SESSION['marge_gauche']=isset($_POST['marge_gauche']) ? $_POST["marge_gauche"] : 10;
  $_SESSION['marge_droite']=isset($_POST['marge_droite']) ? $_POST["marge_droite"] : 10;
  $_SESSION['marge_haut']=isset($_POST['marge_haut']) ? $_POST["marge_haut"] : 10;
  $_SESSION['marge_bas']=isset($_POST['marge_bas']) ? $_POST["marge_bas"] : 10;
  $_SESSION['marge_reliure']=isset($_POST['marge_reliure']) ? $_POST["marge_reliure"] : 1;
  $_SESSION['avec_emplacement_trous']=isset($_POST['avec_emplacement_trous']) ? $_POST["avec_emplacement_trous"] : 1;
  $_SESSION['affiche_pp']=isset($_POST['affiche_pp']) ? $_POST["affiche_pp"] : 1;
  $_SESSION['une_seule_page']=isset($_POST['une_seule_page']) ? $_POST["une_seule_page"] : 1;
  $_SESSION['h_ligne']=isset($_POST['h_ligne']) ? $_POST["h_ligne"] : 8;
  $_SESSION['l_nomprenom']=isset($_POST['l_nomprenom']) ? $_POST["l_nomprenom"] : 40;

  header("Location: ../saisie/impression_avis.php");
		die();
}
?>
