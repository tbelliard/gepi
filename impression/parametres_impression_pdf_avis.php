<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

if (!defined('FPDF_VERSION')) {
	require_once('../fpdf/fpdf.php');
}


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

	//INSERT INTO droits VALUES ('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; réglage des paramètres', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	//**************** EN-TETE **************************************
	//$titre_page = "Impression de listes au format PDF <br />Choix des paramètres".$periode;
	$titre_page = "Impression des avis (PDF) | Choix des paramètres";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE **********************************

	//debug_var();

	echo "<p class='bold'>";
	echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href='../saisie/impression_avis.php'>Impression des avis</a>";
	echo "</p>\n";
	
	echo "<h3>Choix des paramètres&nbsp;: </h3>\n";
	
	echo "<div>\n";
	echo "<fieldset style='background-image: url(\"../images/background/opacite50.png\");'>\n";
	echo "<legend style='border: 1px solid grey; background-color: white;'>Modifiez l'apparence du document PDF&nbsp;:</legend>\n";
	echo "<form method=\"post\" action=\"../impression/parametres_impression_pdf_avis.php\" name=\"choix_parametres\">\n";
	echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" /><br />\n";
	echo "<br />\n";

	echo "<b>Définition des marges du document&nbsp;:</b></p>\n";
	echo "<table style='margin-left: 1em;' border='0'>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_marge_gauche',10);
	echo "<tr><td>Marge à gauche&nbsp;:</td><td><input type=\"text\" name=\"marge_gauche\" id=\"marge_gauche\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" /></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_marge_droite',10);
	echo "<tr><td>Marge à droite&nbsp;:</td><td><input type=\"text\" name=\"marge_droite\" id=\"marge_droite\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" /></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_marge_haut',10);
	echo "<tr><td>Marge du haut&nbsp;:</td><td><input type=\"text\" name=\"marge_haut\" id=\"marge_haut\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,150);\" autocomplete=\"off\" /></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_marge_bas',10);
	echo "<tr><td>Marge du bas&nbsp;:</td><td><input type=\"text\" name=\"marge_bas\" id=\"marge_bas\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,150);\" autocomplete=\"off\" /></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_marge_reliure',1);
	echo "<tr><td>Option marge reliure ?</td><td style='width:8em'><input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_1\" value=\"1\" ";
	if("$valeur"!="0") {
		echo "checked ";
	}
	echo "/><label for='marge_reliure_1'> Oui</label> <input type=\"radio\" name=\"marge_reliure\" id=\"marge_reliure_0\" value=\"0\" ";
	if("$valeur"=="0") {
		echo "checked ";
	}
	echo "onchange=\"if(document.getElementById('marge_reliure_0').checked==true) {document.getElementById('avec_emplacement_trous_0').checked=true}\"";
	echo "/><label for='marge_reliure_0'> Non</label></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_avec_emplacement_trous',1);
	echo "<tr><td>Option emplacement des<br />perforations classeur&nbsp;?</td><td valign='top'><input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_1\" value=\"1\" ";
	if("$valeur"!="0") {
		echo "checked ";
	}
	echo "onchange=\"if(document.getElementById('avec_emplacement_trous_1').checked==true) {document.getElementById('marge_reliure_1').checked=true}\"";
	echo "/><label for='avec_emplacement_trous_1'> Oui</label> <input type=\"radio\" name=\"avec_emplacement_trous\" id=\"avec_emplacement_trous_0\" value=\"0\" ";
	if("$valeur"=="0") {
		echo "checked ";
	}
	echo "/><label for='avec_emplacement_trous_0'> Non</label></td><td valign='top'><p style='margin-left:1em;'><em>Sans effet, si on ne laisse pas de marge reliure</em></p></td></tr>\n";
	echo "</table>\n";
	echo "<br />\n";

	echo "<b>Informations à afficher sur le document&nbsp;:</b><br />\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_affiche_pp',1);
	echo "&nbsp;&nbsp;Afficher le professeur responsable de la classe ? <input type=\"radio\" name=\"affiche_pp\" id=\"affiche_pp_1\" value=\"1\" ";
	if("$valeur"!="0") {
		echo "checked ";
	}
	echo "/><label for='affiche_pp_1'> Oui</label> <input type=\"radio\" id=\"affiche_pp_0\" name=\"affiche_pp\" value=\"0\" ";
	if("$valeur"=="0") {
		echo "checked ";
	}
	echo "/><label for='affiche_pp_0'> Non</label><br />\n";
	echo "<br />\n";

	echo "<b>Styles du tableau&nbsp;: </b><br />\n";
	echo "<table style='margin-left: 1em;' border='0'>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_une_seule_page',1);
	echo "<tr><td>Tout sur une seule page&nbsp;?</td><td style='width:8em'><input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_1\" value=\"1\" ";
	if("$valeur"!="0") {
		echo "checked ";
	}
	echo "/><label for='une_seule_page_1'> Oui</label> <input type=\"radio\" name=\"une_seule_page\" id=\"une_seule_page_0\" value=\"0\" ";
	if("$valeur"=="0") {
		echo "checked ";
	}
	echo "/><label for='une_seule_page_0'> Non</label></td><td>&nbsp;</td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_h_ligne',8);
	echo "<tr><td valign='top'>Hauteur d'une ligne&nbsp;:</td><td valign='top'><input type=\"text\" name=\"h_ligne\" id=\"h_ligne\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" /> </td><td><em>La hauteur de ligne demandée n'est prise en compte que dans le cas<br />où on n'impose pas d'afficher tout sur une seule page</em></td></tr>\n";
	$valeur=getPref($_SESSION['login'],'avis_pdf_l_nomprenom',40);
	echo "<tr><td>Largeur colonne Nom / Prénom&nbsp;:</td><td><input type=\"text\" name=\"l_nomprenom\" id=\"l_nomprenom\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" /></td><td>&nbsp;</td></tr>\n";

	$valeur=getPref($_SESSION['login'],'avis_pdf_l_mentions',30);
	echo "<tr>
	<td>
		Largeur colonne ".getSettingValue('gepi_denom_mention')."&nbsp;:<br />
		(<em>sous réserve que des mentions aient été définies</em>)
	</td>
	<td>
		<input type=\"text\" name=\"l_mentions\" id=\"l_mentions\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" />
	</td>
	<td>&nbsp;</td>
</tr>\n";

	$valeur=getPref($_SESSION['login'],'avis_pdf_l_avertissements',20);
	echo "<tr>
	<td>
		Largeur colonne ".getSettingValue('mod_disc_terme_avertissement_fin_periode')."&nbsp;:<br />
		(<em>sous réserve que le module Discipline soit activé et que des ".getSettingValue('mod_disc_terme_avertissement_fin_periode')." y aient été définis</em>)
	</td>
	<td>
		<input type=\"text\" name=\"l_avertissements\" id=\"l_avertissements\" size=\"2\" maxlength=\"2\" value=\"$valeur\" onkeydown=\"clavier_2(this.id,event,0,100);\" autocomplete=\"off\" />
	</td>
	<td>&nbsp;</td>
</tr>\n";

	echo "</table>\n";
	echo "<input value=\"1\" name=\"ok\" type=\"hidden\" />\n";
	echo "<br />\n";
	echo "<input value=\"Valider les paramètres\" name=\"Valider\" type=\"submit\" />\n";
	echo "<br />\n";
	echo "</form>\n";
	echo "</fieldset>\n";
	echo "</div>\n";
	echo "<br />\n";
	require("../lib/footer.inc.php");

} else { // if OK
	// On enregistre dans la session et on redirige vers impression_serie.php
	$_SESSION['avis_pdf_marge_gauche']=isset($_POST['marge_gauche']) ? $_POST["marge_gauche"] : 10;
	savePref($_SESSION['login'],'avis_pdf_marge_gauche',$_SESSION['avis_pdf_marge_gauche']);

	$_SESSION['avis_pdf_marge_droite']=isset($_POST['marge_droite']) ? $_POST["marge_droite"] : 10;
	savePref($_SESSION['login'],'avis_pdf_marge_droite',$_SESSION['avis_pdf_marge_droite']);

	$_SESSION['avis_pdf_marge_haut']=isset($_POST['marge_haut']) ? $_POST["marge_haut"] : 10;
	savePref($_SESSION['login'],'avis_pdf_marge_haut',$_SESSION['avis_pdf_marge_haut']);

	$_SESSION['avis_pdf_marge_bas']=isset($_POST['marge_bas']) ? $_POST["marge_bas"] : 10;
	savePref($_SESSION['login'],'avis_pdf_marge_bas',$_SESSION['avis_pdf_marge_bas']);

	$_SESSION['avis_pdf_marge_reliure']=isset($_POST['marge_reliure']) ? $_POST["marge_reliure"] : 1;
	savePref($_SESSION['login'],'avis_pdf_marge_reliure',$_SESSION['avis_pdf_marge_reliure']);

	$_SESSION['avis_pdf_avec_emplacement_trous']=isset($_POST['avec_emplacement_trous']) ? $_POST["avec_emplacement_trous"] : 1;
	savePref($_SESSION['login'],'avis_pdf_avec_emplacement_trous',$_SESSION['avis_pdf_avec_emplacement_trous']);

	$_SESSION['avis_pdf_affiche_pp']=isset($_POST['affiche_pp']) ? $_POST["affiche_pp"] : 1;
	savePref($_SESSION['login'],'avis_pdf_affiche_pp',$_SESSION['avis_pdf_affiche_pp']);

	$_SESSION['avis_pdf_une_seule_page']=isset($_POST['une_seule_page']) ? $_POST["une_seule_page"] : 1;
	savePref($_SESSION['login'],'avis_pdf_une_seule_page',$_SESSION['avis_pdf_une_seule_page']);

	$_SESSION['avis_pdf_h_ligne']=isset($_POST['h_ligne']) ? $_POST["h_ligne"] : 8;
	savePref($_SESSION['login'],'avis_pdf_h_ligne',$_SESSION['avis_pdf_h_ligne']);

	$_SESSION['avis_pdf_l_nomprenom']=isset($_POST['l_nomprenom']) ? $_POST["l_nomprenom"] : 40;
	savePref($_SESSION['login'],'avis_pdf_l_nomprenom',$_SESSION['avis_pdf_l_nomprenom']);

	$_SESSION['avis_pdf_l_mentions']=isset($_POST['l_mentions']) ? $_POST["l_mentions"] : 30;
	savePref($_SESSION['login'],'avis_pdf_l_mentions',$_SESSION['avis_pdf_l_mentions']);

	$_SESSION['avis_pdf_l_avertissements']=isset($_POST['l_avertissements']) ? $_POST["l_avertissements"] : 20;
	savePref($_SESSION['login'],'avis_pdf_l_avertissements',$_SESSION['avis_pdf_l_avertissements']);

	header("Location: ../saisie/impression_avis.php");
	die();
}
?>
