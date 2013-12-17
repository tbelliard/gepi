<?php
/*
 * $Id$
 *
 * Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Begin standart header

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
include("../ckeditor/ckeditor.php") ;

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$reg_ok = 'yes';
$msg = '';
$bgcolor = "#DEDEDE";


if(getSettingAOui('active_bulletins')) {
	$titre_page = "Paramètres de configuration des bulletins scolaires HTML";
}
else {
	$titre_page = "Paramètres bloc adresse responsables";
}

//debug_var();

// Tableau des couleurs HTML:
$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");

// tableau des polices pour avis du CC de classe
$tab_polices_avis=Array("DejaVu","Arial","Helvetica","Serif","Times","Times New Roman","Verdana",);

//Style des caractères avis
// tableau des styles de polices pour avis du CC de classe
$tab_styles_avis=Array("Normal","Gras","Italique","Gras et Italique");

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if (isset($_POST['is_posted'])) {
	check_token();
	if (isset($_POST['textsize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['textsize'])) || $_POST['textsize'] < 1) {
			$_POST['textsize'] = 10;
		}
		if (!saveSetting("textsize", $_POST['textsize'])) {
			$msg .= "Erreur lors de l'enregistrement de textsize !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_cell_pp_textsize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_cell_pp_textsize'])) || $_POST['bull_cell_pp_textsize'] < 1) {
			$_POST['bull_cell_pp_textsize'] = 10;
		}
		if (!saveSetting("bull_cell_pp_textsize", $_POST['bull_cell_pp_textsize'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_cell_pp_textsize !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_cell_signature_textsize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_cell_signature_textsize'])) || $_POST['bull_cell_signature_textsize'] < 1) {
			$_POST['bull_cell_signature_textsize'] = 10;
		}
		if (!saveSetting("bull_cell_signature_textsize", $_POST['bull_cell_signature_textsize'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_cell_signature_textsize !";
			$reg_ok = 'no';
		}
	}

	//==================================
	// AJOUT: boireaus
	if (isset($_POST['p_bulletin_margin'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['p_bulletin_margin'])) || $_POST['p_bulletin_margin'] < 1) {
			$_POST['p_bulletin_margin'] = 5;
		}
		if (!saveSetting("p_bulletin_margin", $_POST['p_bulletin_margin'])) {
			$msg .= "Erreur lors de l'enregistrement de p_bulletin_margin !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_body_marginleft'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_body_marginleft']))) {
			$_POST['bull_body_marginleft'] = 1;
		}
		if (!saveSetting("bull_body_marginleft", $_POST['bull_body_marginleft'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_body_marginleft !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	
	
	if (isset($_POST['titlesize'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['titlesize'])) || $_POST['titlesize'] < 1) {
			$_POST['titlesize'] = 16;
		}
		if (!saveSetting("titlesize", $_POST['titlesize'])) {
			$msg .= "Erreur lors de l'enregistrement de titlesize !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['cellpadding'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['cellpadding'])) || $_POST['cellpadding'] < 0) {
			$_POST['cellpadding'] = 5;
		}
		if (!saveSetting("cellpadding", $_POST['cellpadding'])) {
			$msg .= "Erreur lors de l'enregistrement de cellpadding !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['cellspacing'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['cellspacing'])) || $_POST['cellspacing'] < 0) {
			$_POST['cellspacing'] = 2;
		}
		if (!saveSetting("cellspacing", $_POST['cellspacing'])) {
			$msg .= "Erreur lors de l'enregistrement de cellspacing !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['largeurtableau'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['largeurtableau'])) || $_POST['largeurtableau'] < 1) {
			$_POST['largeurtableau'] = 1440;
		}
		if (!saveSetting("largeurtableau", $_POST['largeurtableau'])) {
			$msg .= "Erreur lors de l'enregistrement de largeurtableau !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_matiere_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_matiere_largeur'])) || $_POST['col_matiere_largeur'] < 1) {
			$_POST['col_matiere_largeur'] = 300;
		}
		if (!saveSetting("col_matiere_largeur", $_POST['col_matiere_largeur'])) {
			$msg .= "Erreur lors de l'enregistrement de col_matiere_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_note_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_note_largeur'])) || $_POST['col_note_largeur'] < 1) {
			$_POST['col_note_largeur'] = 50;
		}
		if (!saveSetting("col_note_largeur", $_POST['col_note_largeur'])) {
			$msg .= "Erreur lors de l'enregistrement de col_note_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_boite_largeur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_boite_largeur'])) || $_POST['col_boite_largeur'] < 1) {
			$_POST['col_boite_largeur'] = 120;
		}
		if (!saveSetting("col_boite_largeur", $_POST['col_boite_largeur'])) {
			$msg .= "Erreur lors de l'enregistrement de col_boite_largeur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['col_hauteur'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['col_hauteur'])) || $_POST['col_hauteur'] < 1) {
			$_POST['col_hauteur'] = 0;
		}
		if (!saveSetting("col_hauteur", $_POST['col_hauteur'])) {
			$msg .= "Erreur lors de l'enregistrement de col_hauteur !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_ecart_entete'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_ecart_entete']))) {
			$_POST['bull_ecart_entete'] = 0;
		}
		if (!saveSetting("bull_ecart_entete", $_POST['bull_ecart_entete'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_ecart_entete !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_espace_avis'])) {
	
		if ((!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_espace_avis']))) or ($_POST['bull_espace_avis'] <= 0)) {
			$_POST['bull_espace_avis'] = 1;
		}
		if (!saveSetting("bull_espace_avis", $_POST['bull_espace_avis'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_espace_avis !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['addressblock_padding_right'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_right']))) {
			$_POST['addressblock_padding_right'] = 0;
		}
		if (!saveSetting("addressblock_padding_right", $_POST['addressblock_padding_right'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_padding_right !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_padding_top'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_top']))) {
			$_POST['addressblock_padding_top'] = 0;
		}
		if (!saveSetting("addressblock_padding_top", $_POST['addressblock_padding_top'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_padding_top !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_padding_text'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_padding_text']))) {
			$_POST['addressblock_padding_text'] = 0;
		}
		if (!saveSetting("addressblock_padding_text", $_POST['addressblock_padding_text'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_padding_text !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['addressblock_length'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_length']))) {
			$_POST['addressblock_length'] = 0;
		}
		if (!saveSetting("addressblock_length", $_POST['addressblock_length'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_length !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	// Ajout: boireaus
	if (isset($_POST['addressblock_font_size'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_font_size']))) {
			$_POST['addressblock_font_size'] = 12;
		}
		if (!saveSetting("addressblock_font_size", $_POST['addressblock_font_size'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_font_size !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['addressblock_logo_etab_prop'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_logo_etab_prop']))) {
				$addressblock_logo_etab_prop=50;
		}
		else{
				$addressblock_logo_etab_prop=$_POST['addressblock_logo_etab_prop'];
		}
	}
	else{
		if(getSettingValue("addressblock_logo_etab_prop")){
			$addressblock_logo_etab_prop=getSettingValue("addressblock_logo_etab_prop");
		}
		else{
			$addressblock_logo_etab_prop=50;
		}
	}
	
	if (isset($_POST['addressblock_classe_annee'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['addressblock_classe_annee']))) {
				$addressblock_classe_annee=35;
		}
		else{
				$addressblock_classe_annee=$_POST['addressblock_classe_annee'];
		}
	}
	else{
		if(getSettingValue("addressblock_classe_annee")){
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee");
		}
		else{
			$addressblock_classe_annee=30;
		}
	}
	
	if((isset($_POST['addressblock_classe_annee']))&&(isset($_POST['addressblock_logo_etab_prop']))){
		$valtest=$addressblock_logo_etab_prop+$addressblock_classe_annee;
		if($valtest>100){
			$msg.="Erreur! La somme addressblock_logo_etab_prop+addressblock_classe_annee dépasse 100% de la largeur de la page !";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("addressblock_logo_etab_prop", $addressblock_logo_etab_prop)) {
				$msg .= "Erreur lors de l'enregistrement de addressblock_logo_etab_prop !";
				$reg_ok = 'no';
			}
	
			if (!saveSetting("addressblock_classe_annee", $addressblock_classe_annee)) {
				$msg .= "Erreur lors de l'enregistrement de addressblock_classe_annee !";
				$reg_ok = 'no';
			}
		}
	}
	
	
	if (isset($_POST['bull_ecart_bloc_nom'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_ecart_bloc_nom']))) {
			$_POST['bull_ecart_bloc_nom'] = 0;
		}
		if (!saveSetting("bull_ecart_bloc_nom", $_POST['bull_ecart_bloc_nom'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_ecart_bloc_nom !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['addressblock_debug'])) {
		if (($_POST['addressblock_debug']!="y")&&($_POST['addressblock_debug']!="n")) {
			$_POST['addressblock_debug'] = "n";
		}
		if (!saveSetting("addressblock_debug", $_POST['addressblock_debug'])) {
			$msg .= "Erreur lors de l'enregistrement de addressblock_debug !";
			$reg_ok = 'no';
		}
	}
	//==================================
	
	
	if (isset($_POST['page_garde_padding_top'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_top']))) {
			$_POST['page_garde_padding_top'] = 0;
		}
		if (!saveSetting("page_garde_padding_top", $_POST['page_garde_padding_top'])) {
			$msg .= "Erreur lors de l'enregistrement de page_garde_padding_top !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['page_garde_padding_left'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_left']))) {
			$_POST['page_garde_padding_left'] = 0;
		}
		if (!saveSetting("page_garde_padding_left", $_POST['page_garde_padding_left'])) {
			$msg .= "Erreur lors de l'enregistrement de page_garde_padding_left !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['page_garde_padding_text'])) {
	
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['page_garde_padding_text']))) {
			$_POST['page_garde_padding_text'] = 0;
		}
		if (!saveSetting("page_garde_padding_text", $_POST['page_garde_padding_text'])) {
			$msg .= "Erreur lors de l'enregistrement de page_garde_padding_text !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['ok'])) {

		if(getSettingAOui('active_bulletins')) {
			if (isset($_POST['page_garde_imprime'])) {
				$temp = 'yes';
			} else {
				$temp = 'no';
			}
			if (!saveSetting("page_garde_imprime", $temp)) {
				$msg .= "Erreur lors de l'enregistrement de page_garde_imprime !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($NON_PROTECT['page_garde_texte'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['page_garde_texte']);
		if (!saveSetting("page_garde_texte", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de page_garde_texte !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($NON_PROTECT['bull_formule_bas'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['bull_formule_bas']);
		if (!saveSetting("bull_formule_bas", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de bull_formule_bas !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_mention_nom_court'])) {
	
		if (!saveSetting("bull_mention_nom_court", $_POST['bull_mention_nom_court'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_mention_nom_court !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_mention_doublant'])) {
	
		if (!saveSetting("bull_mention_doublant", $_POST['bull_mention_doublant'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_mention_doublant !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_eleve_une_ligne'])) {
	
		if (!saveSetting("bull_affiche_eleve_une_ligne", $_POST['bull_affiche_eleve_une_ligne'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_mention_nom_court !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_graphiques'])) {
	
		if (!saveSetting("bull_affiche_graphiques", $_POST['bull_affiche_graphiques'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_graphiques !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_appreciations'])) {
	
		if (!saveSetting("bull_affiche_appreciations", $_POST['bull_affiche_appreciations'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_appreciations !";
			$reg_ok = 'no';
		}
	}

	//20130215
	if (isset($_POST['bull_affiche_absences'])) {
		if (!saveSetting("bull_affiche_absences", $_POST['bull_affiche_absences'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_absences !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_abs_tot'])) {
		if (!saveSetting("bull_affiche_abs_tot", $_POST['bull_affiche_abs_tot'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_abs_tot !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_abs_nj'])) {
		if (!saveSetting("bull_affiche_abs_nj", $_POST['bull_affiche_abs_nj'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_abs_nj !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_abs_ret'])) {
		if (!saveSetting("bull_affiche_abs_ret", $_POST['bull_affiche_abs_ret'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_abs_ret !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_abs_cpe'])) {
		if (!saveSetting("bull_affiche_abs_cpe", $_POST['bull_affiche_abs_cpe'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_abs_cpe !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_avis'])) {
		
		if (!saveSetting("bull_affiche_avis", $_POST['bull_affiche_avis'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_avis !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_aid'])) {

		if (!saveSetting("bull_affiche_aid", $_POST['bull_affiche_aid'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_aid !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_formule'])) {

		if (!saveSetting("bull_affiche_formule", $_POST['bull_affiche_formule'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_formule !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['bull_affiche_signature'])) {
	
		if (!saveSetting("bull_affiche_signature", $_POST['bull_affiche_signature'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_signature !";
			$reg_ok = 'no';
		}
	}
	/*
	if (isset($_POST['bull_affiche_img_signature'])) {
	
		if (!saveSetting("bull_affiche_img_signature", $_POST['bull_affiche_img_signature'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_img_signature !";
			$reg_ok = 'no';
		}
	}
	*/

	if (isset($_POST['bull_hauteur_img_signature'])) {
		$bull_hauteur_img_signature=$_POST['bull_hauteur_img_signature'];

		if(($bull_hauteur_img_signature!='')&&(preg_match("/^[0-9]*$/", $bull_hauteur_img_signature))&&($bull_hauteur_img_signature>0)) {
			if (!saveSetting("bull_hauteur_img_signature", $_POST['bull_hauteur_img_signature'])) {
				$msg .= "Erreur lors de l'enregistrement de bull_hauteur_img_signature !";
				$reg_ok = 'no';
			}
		}
		else {
			$msg .= "Valeur incorrecte pour 'bull_hauteur_img_signature' !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_largeur_img_signature'])) {
		$bull_largeur_img_signature=$_POST['bull_largeur_img_signature'];

		if(($bull_largeur_img_signature!='')&&(preg_match("/^[0-9]*$/", $bull_largeur_img_signature))&&($bull_largeur_img_signature>0)) {
			if (!saveSetting("bull_largeur_img_signature", $_POST['bull_largeur_img_signature'])) {
				$msg .= "Erreur lors de l'enregistrement de bull_largeur_img_signature !";
				$reg_ok = 'no';
			}
		}
		else {
			$msg .= "Valeur incorrecte pour 'bull_largeur_img_signature' !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affiche_numero'])) {
	
		if (!saveSetting("bull_affiche_numero", $_POST['bull_affiche_numero'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_numero !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affiche_etab'])) {
		if (!saveSetting("bull_affiche_etab", $_POST['bull_affiche_etab'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_etab !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_bordure_classique'])) {
		if (!saveSetting("bull_bordure_classique", $_POST['bull_bordure_classique'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_bordure_classique !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['choix_bulletin'])) {

		if (!saveSetting("choix_bulletin", $_POST['choix_bulletin'])) {
			$msg .= "Erreur lors de l'enregistrement de choix_bulletin";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['min_max_moyclas'])) {
	
		if (!saveSetting("min_max_moyclas", $_POST['min_max_moyclas'])) {
			$msg .= "Erreur lors de l'enregistrement de min_max_moyclas !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['moyennes_periodes_precedentes'])) {
		if (!saveSetting("moyennes_periodes_precedentes", $_POST['moyennes_periodes_precedentes'])) {
			$msg .= "Erreur lors de l'enregistrement de moyennes_periodes_precedentes !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['moyennes_annee'])) {
		if (!saveSetting("moyennes_annee", $_POST['moyennes_annee'])) {
			$msg .= "Erreur lors de l'enregistrement de moyennes_annee !";
			$reg_ok = 'no';
		}
	}


	if(isset($_POST['activer_photo_bulletin'])) {
		if (!saveSetting("activer_photo_bulletin", $_POST['activer_photo_bulletin'])) {
			$msg .= "Erreur lors de l'enregistrement de activer_photo_bulletin !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_photo_hauteur_max'])) {
		if (!saveSetting("bull_photo_hauteur_max", $_POST['bull_photo_hauteur_max'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_photo_hauteur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_photo_largeur_max'])) {
		if (!saveSetting("bull_photo_largeur_max", $_POST['bull_photo_largeur_max'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_photo_largeur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_categ_font_size'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_categ_font_size']))) {
			$_POST['bull_categ_font_size'] = 10;
		}
		if (!saveSetting("bull_categ_font_size", $_POST['bull_categ_font_size'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_categ_font_size !";
			$reg_ok = 'no';
		}
	}
	
	
	if(isset($_POST['bull_intitule_app'])) {
		if (!saveSetting("bull_intitule_app", $_POST['bull_intitule_app'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_intitule_app !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_affiche_INE_eleve'])) {
		if (!saveSetting("bull_affiche_INE_eleve", $_POST['bull_affiche_INE_eleve'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_INE_eleve !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['bull_affiche_tel'])) {
		if (!saveSetting("bull_affiche_tel", $_POST['bull_affiche_tel'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_tel !";
			$reg_ok = 'no';
		}
	}

	if(isset($_POST['bull_affiche_fax'])) {
		if (!saveSetting("bull_affiche_fax", $_POST['bull_affiche_fax'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_fax !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_categ_bgcolor'])) {
		if((!in_array($_POST['bull_categ_bgcolor'],$tabcouleur))&&($_POST['bull_categ_bgcolor']!='')){
			$msg .= "Erreur lors de l'enregistrement de bull_categ_bgcolor ! (couleur invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_categ_bgcolor", $_POST['bull_categ_bgcolor'])) {
				$msg .= "Erreur lors de l'enregistrement de bull_categ_bgcolor !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($_POST['bull_police_avis'])) {
		if((!in_array($_POST['bull_police_avis'],$tab_polices_avis))&&($_POST['bull_police_avis']!='')){
			$msg .= "Erreur lors de l'enregistrement de bull_police_avis ! (police invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_police_avis", $_POST['bull_police_avis'])) {
				$msg .= "Erreur lors de l'enregistrement de bull_police_avis !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($_POST['bull_font_style_avis'])) {
		if((!in_array($_POST['bull_font_style_avis'],$tab_styles_avis))&&($_POST['bull_font_style_avis']!='')){
			$msg .= "Erreur lors de l'enregistrement de bull_font_style_avis ! (police invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("bull_font_style_avis", $_POST['bull_font_style_avis'])) {
				$msg .= "Erreur lors de l'enregistrement de bull_font_style_avis !";
				$reg_ok = 'no';
			}
		}
	}
	
	//taille de la police avis
	if(isset($_POST['bull_categ_font_size_avis'])) {
		if (!(preg_match ("/^[0-9]{1,}$/", $_POST['bull_categ_font_size_avis']))) {
			$_POST['bull_categ_font_size_avis'] = 10;
		}
		if (!saveSetting("bull_categ_font_size_avis", $_POST['bull_categ_font_size_avis'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_categ_font_size_avis !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['genre_periode'])) {
		if (!saveSetting("genre_periode", $_POST['genre_periode'])) {
			$msg .= "Erreur lors de l'enregistrement de genre_periode !";
			$reg_ok = 'no';
		}
	}
	
	
	
	if (isset($_POST['bull_affich_nom_etab'])) {
		if($_POST['bull_affich_nom_etab']=="n") {
			$bull_affich_nom_etab="n";
		}
		else{
			$bull_affich_nom_etab="y";
		}
		if (!saveSetting("bull_affich_nom_etab", $bull_affich_nom_etab)) {
			$msg .= "Erreur lors de l'enregistrement de bull_affich_nom_etab !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['bull_affich_adr_etab'])) {
		if($_POST['bull_affich_adr_etab']=="n") {
			$bull_affich_adr_etab="n";
		}
		else{
			$bull_affich_adr_etab="y";
		}
		if (!saveSetting("bull_affich_adr_etab", $bull_affich_adr_etab)) {
			$msg .= "Erreur lors de l'enregistrement de bull_affich_adr_etab !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affich_mentions'])) {
		if($_POST['bull_affich_mentions']=="n") {
			$bull_affich_mentions="n";
		}
		else{
			$bull_affich_mentions="y";
		}
		if (!saveSetting("bull_affich_mentions", $bull_affich_mentions)) {
			$msg .= "Erreur lors de l'enregistrement de bull_affich_mentions !";
			$reg_ok = 'no';
		}
	}

	if (isset($_POST['bull_affich_intitule_mentions'])) {
		if($_POST['bull_affich_intitule_mentions']=="n") {
			$bull_affich_intitule_mentions="n";
		}
		else{
			$bull_affich_intitule_mentions="y";
		}
		if (!saveSetting("bull_affich_intitule_mentions", $bull_affich_intitule_mentions)) {
			$msg .= "Erreur lors de l'enregistrement de bull_affich_intitule_mentions !";
			$reg_ok = 'no';
		}
	}

}

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
$msg = "Enregistrement réussi !";
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// End standart header
require_once("../lib/header.inc.php");
if (!loadSettings()) {
    die("Erreur chargement settings");
}
?>

<script type="text/javascript">
<!-- Debut
var nb='';
function SetDefaultValues(nb){
	if (nb=='A4V') {
		window.document.formulaire.titlesize.value = '14';
		window.document.formulaire.textsize.value = '8';
		window.document.formulaire.bull_cell_pp_textsize.value = '8';
		window.document.formulaire.bull_cell_signature_textsize.value = '8';
		window.document.formulaire.largeurtableau.value = '800';
		window.document.formulaire.col_matiere_largeur.value = '150';
		window.document.formulaire.col_note_largeur.value = '30';
		window.document.formulaire.col_boite_largeur.value = '120';
		window.document.formulaire.cellpadding.value = '3';
		window.document.formulaire.cellspacing.value = '1';
	}
	if(nb=='A3H'){
		window.document.formulaire.titlesize.value = '16';
		window.document.formulaire.textsize.value = '10';
		window.document.formulaire.bull_cell_pp_textsize.value = '10';
		window.document.formulaire.bull_cell_signature_textsize.value = '10';
		window.document.formulaire.largeurtableau.value = '1440';
		window.document.formulaire.col_matiere_largeur.value = '300';
		window.document.formulaire.col_note_largeur.value = '50';
		window.document.formulaire.col_boite_largeur.value = '150';
		window.document.formulaire.cellpadding.value = '5';
		window.document.formulaire.cellspacing.value = '2';
	}
	if(nb=='Adresse'){
		window.document.formulaire.addressblock_padding_right.value = '20';
		window.document.formulaire.addressblock_padding_top.value = '40';
		window.document.formulaire.addressblock_padding_text.value = '20';
		window.document.formulaire.addressblock_length.value = '60';
		window.document.formulaire.addressblock_font_size.value = '12';
		window.document.formulaire.addressblock_logo_etab_prop.value = '50';
		window.document.formulaire.addressblock_classe_annee.value = '35';
		window.document.formulaire.bull_ecart_bloc_nom.value = '1';

		//window.document.formulaire.addressblock_debug.value = 'n';
		window.document.getElementById('addressblock_debugn').checked='true';
	}
}
// fin du script -->
change='no';
</script>

<?php
if(!getSettingAOui('active_bulletins')) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil </a></p>\n";
}
else {
?>
<p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
| <!--a href="./index.php"> Imprimer les bulletins au format HTML</a-->
<a href="./bull_index.php"> Imprimer les bulletins</a>
| <a href="./param_bull_pdf.php"> Paramètres d'impression des bulletins PDF</a>
</p>
<?php
}

if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
    die("Droits insuffisants pour effectuer cette opération");
}
?>


<form name="formulaire" action="param_bull.php" method="post" style="width: 100%;">
<?php
echo add_token_field();

if(getSettingAOui('active_bulletins')) {
?>
<input type='hidden' name='is_posted' value='y' />
<H3>Mise en page du bulletin scolaire</H3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Mise en page'>

    <tr <?php $nb_ligne = 1; if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Rétablir les paramètres par défaut :<br />
        &nbsp;&nbsp;&nbsp;<A HREF="javascript:SetDefaultValues('A4V')">Impression sur A4 "portrait"</A><br />
        &nbsp;&nbsp;&nbsp;<A HREF="javascript:SetDefaultValues('A3H')">Impression sur A3 "paysage"</A>

        </td>
        <td>
        &nbsp;
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_body_marginleft' style='cursor: pointer;'>Marge gauche de la page (en pixels) :</label>
        </td>
        <td><input type="text" name="bull_body_marginleft" id="bull_body_marginleft" size="20" onchange="changement()" value="<?php
			if(getSettingValue("bull_body_marginleft")) {
				echo getSettingValue("bull_body_marginleft");
			}
			else{
				echo 1;
			}
		?>" onKeyDown="clavier_2(this.id,event,0,1000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='titlesize' style='cursor: pointer;'>Taille en points des gros titres :</label>
        </td>
        <td><input type="text" name="titlesize" id="titlesize" size="20" value="<?php echo(getSettingValue("titlesize")); ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='textsize' style='cursor: pointer;'>Taille en points du texte (hormis les titres) :</label>
        </td>
        <td><input type="text" name="textsize" id="textsize" size="20" value="<?php echo(getSettingValue("textsize")); ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php 
    	if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;$nb_ligne++;}
    	$bull_cell_pp_textsize=getSettingValue('bull_cell_pp_textsize');
    	if($bull_cell_pp_textsize=="") {
    		$bull_cell_pp_textsize=8;
    	}
    ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_cell_pp_textsize' style='cursor: pointer;'>Taille en points du texte de la cellule 'Avis du conseil de classe' :</label>
        </td>
        <td><input type="text" name="bull_cell_pp_textsize" id="bull_cell_pp_textsize" size="20" value="<?php echo $bull_cell_pp_textsize; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php 
    	if ($nb_ligne % 2) {echo "bgcolor=".$bgcolor;$nb_ligne++;}
    	$bull_cell_signature_textsize=getSettingValue('bull_cell_signature_textsize');
    	if($bull_cell_signature_textsize=="") {
    		$bull_cell_signature_textsize=8;
    	}
    ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_cell_signature_textsize' style='cursor: pointer;'>Taille en points du texte de la cellule Signature :</label>
        </td>
        <td><input type="text" name="bull_cell_signature_textsize" id="bull_cell_signature_textsize" size="20" value="<?php echo $bull_cell_signature_textsize; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='p_bulletin_margin' style='cursor: pointer;'>Marges hautes et basses des paragraphes en points du texte (hormis les titres) :</label>
        </td>
        <td><input type="text" name="p_bulletin_margin" id="p_bulletin_margin" size="20" value="<?php
		if(getSettingValue("p_bulletin_margin")!=""){
			echo(getSettingValue("p_bulletin_margin"));
		}
		else{
			echo "5";
		}?>" onKeyDown="clavier_2(this.id,event,0,40);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='largeurtableau' style='cursor: pointer;'>Largeur du tableau en pixels :</label>
        </td>
        <td><input type="text" name="largeurtableau" id="largeurtableau" size="20" value="<?php echo(getSettingValue("largeurtableau")); ?>" onKeyDown="clavier_2(this.id,event,0,5000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_matiere_largeur' style='cursor: pointer;'>Largeur de la première colonne (matières) en pixels :</label><br />
        <span class="small">(Si le contenu d'une cellule de la colonne est plus grand que la taille prévue, la mention ci-dessus devient caduque. La colonne sera dans ce cas dimensionnée par le navigateur lui-même.)</span>
        </td>
        <td><input type="text" name="col_matiere_largeur" id="col_matiere_largeur" size="20" value="<?php echo(getSettingValue("col_matiere_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_note_largeur' style='cursor: pointer;'>Largeur des colonnes min, max, classe et élève en pixels :</label><br />
        <span class="small">(Même remarque que ci-dessus)</span>
        </td>
        <td><input type="text" name="col_note_largeur" id="col_note_largeur" size="20" value="<?php echo(getSettingValue("col_note_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_boite_largeur' style='cursor: pointer;'>Largeur des cellules contenant les notes des carnets de notes à afficher sur les bulletins :</label><br />
        <span class="small">(Même remarque que ci-dessus)</span>
        </td>
        <td><input type="text" name="col_boite_largeur" id="col_boite_largeur" size="20" value="<?php echo(getSettingValue("col_boite_largeur")); ?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='col_hauteur' style='cursor: pointer;'>Hauteur minimale des lignes en pixels ("0" si automatique) :</label><br />
        <span class="small">(Si le contenu d'une cellule est telle que la hauteur fixée ci-dessus est insuffisante, la hauteur de la ligne sera dimensionnée par le navigateur lui-même.)</span>
        </td>
        <td><input type="text" name="col_hauteur" id="col_hauteur" size="20" value="<?php echo(getSettingValue("col_hauteur")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='cellpadding' style='cursor: pointer;'>Espace en pixels entre le bord d'une cellule du tableau et le contenu de la cellule :</label>
        </td>
        <td><input type="text" name="cellpadding" id="cellpadding" size="20" value="<?php echo(getSettingValue("cellpadding")); ?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='cellspacing' style='cursor: pointer;'>Espace en pixels entre les cellules du tableau :</label>
        </td>
        <td><input type="text" name="cellspacing" id="cellspacing" size="20" value="<?php echo(getSettingValue("cellspacing")); ?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_ecart_entete' style='cursor: pointer;'>Espace (nombre de lignes vides) entre l'en-tête du bulletin et le tableau des notes et appréciations :</label>
        </td>
        <td><input type="text" name="bull_ecart_entete" id="bull_ecart_entete" size="20" value="<?php echo(getSettingValue("bull_ecart_entete")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_espace_avis' style='cursor: pointer;'>Espace (nombre de lignes vides) pour une saisie à la main de l'avis du Conseil de classe, si celui-ci n'a pas été saisi dans GEPI :</label>
        </td>
        <td><input type="text" name="bull_espace_avis" id="bull_espace_avis" size="20" value="<?php echo(getSettingValue("bull_espace_avis")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Bordures des cellules du tableau des moyennes et appréciations :
        </td>
        <td>
		<?php
			if(getSettingValue("bull_bordure_classique")=='n'){
				$bull_bordure_classique="n";
			}
			else{
				$bull_bordure_classique="y";
			}

			echo "<input type=\"radio\" name=\"bull_bordure_classique\" id='bull_bordure_classiquey' value=\"y\" ";
			if ($bull_bordure_classique=='y') echo " checked";
			echo " /><label for='bull_bordure_classiquey' style='cursor: pointer;'>&nbsp;classique&nbsp;HTML</label><br />\n";
			echo "<input type=\"radio\" name=\"bull_bordure_classique\" id='bull_bordure_classiquen' value=\"n\" ";
			if ($bull_bordure_classique=='n') echo " checked";
			echo " /><label for='bull_bordure_classiquen' style='cursor: pointer;'>&nbsp;trait&nbsp;noir</label>\n";
		?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_font_size' style='cursor: pointer;'>Taille en points du texte des catégories de matières (<i>lorsqu'elles sont affichées</i>) :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_font_size")){
			$bull_categ_font_size=getSettingValue("bull_categ_font_size");
		}
		else{
			$bull_categ_font_size=10;
		}
	?>
        <td><input type="text" name="bull_categ_font_size" id="bull_categ_font_size" size="20" value="<?php echo $bull_categ_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_bgcolor' style='cursor: pointer;'>Couleur de fond des lignes de catégories de matières (<i>lorsqu'elles sont affichées</i>) :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_bgcolor")){
			$bull_categ_bgcolor=getSettingValue("bull_categ_bgcolor");
		}
		else{
			$bull_categ_bgcolor="";
		}
	?>
        <td>
	<?php
		//<input type="text" name="bull_categ_bgcolor" size="20" value="echo $bull_categ_bgcolor;" />
		echo "<select name='bull_categ_bgcolor' id='bull_categ_bgcolor'>\n";
		echo "<option value=''>Aucune</option>\n";
		for($i=0;$i<count($tabcouleur);$i++){
			if($tabcouleur[$i]=="$bull_categ_bgcolor"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value='$tabcouleur[$i]'$selected>$tabcouleur[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_categ_font_size_avis' style='cursor: pointer;'>Taille en points du texte de l'avis du conseil de classe :</label>
        </td>
	<?php
		if(getSettingValue("bull_categ_font_size_avis")){
			$bull_categ_font_size_avis=getSettingValue("bull_categ_font_size_avis");
		}
		else{
			$bull_categ_font_size_avis=10;
		}
	?>
        <td><input type="text" name="bull_categ_font_size_avis" id="bull_categ_font_size_avis" size="20" value="<?php echo $bull_categ_font_size_avis; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_police_avis' style='cursor: pointer;'>Police de caractères pour l'avis du conseil de classe :</label>
        </td>
	<?php
		if(getSettingValue("bull_police_avis")){
			$bull_police_avis=getSettingValue("bull_police_avis");
		}
		else{
			$bull_police_avis="";
		}
	?>
        <td>
	<?php
		echo "<select name='bull_police_avis' id='bull_police_avis'>\n";
		echo "<option value=''>Aucune</option>\n";
		for($i=0;$i<count($tab_polices_avis);$i++){
			if($tab_polices_avis[$i]=="$bull_police_avis"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value=\"$tab_polices_avis[$i]\" $selected>$tab_polices_avis[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_font_style_avis' style='cursor: pointer;'>Style de caractères pour l'avis du conseil de classe :</label>
        </td>
	<?php
		if(getSettingValue("bull_font_style_avis")){
			$bull_font_style_avis=getSettingValue("bull_font_style_avis");
		}
		else{
			$bull_font_style_avis="normal";
		}
	?>
        <td>
	<?php
		echo "<select name='bull_font_style_avis' id='bull_font_style_avis'>\n";
		for($i=0;$i<count($tab_styles_avis);$i++){
			if($tab_styles_avis[$i]=="$bull_font_style_avis"){
				$selected=" selected='true'";
			}
			else{
				$selected="";
			}
			echo "<option value=\"$tab_styles_avis[$i]\" $selected>$tab_styles_avis[$i]</option>\n";
		}
		echo "</select>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Genre des périodes :<br />(<i>'trimestre' ou 'semestre' est masculin; 'période' est féminin</i>)
        </td>
	<?php
		if(getSettingValue("genre_periode")){
			$genre_periode=getSettingValue("genre_periode");
		}
		else{
			$genre_periode="M";
		}
	?>
        <td>
	<?php
        echo "<label for='genre_periodeM' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"genre_periode\" id=\"genre_periodeM\" value=\"M\" ";
        if ($genre_periode == 'M') {echo " checked";}
        echo " />&nbsp;Masculin</label>\n";
		echo "<br />\n";
        echo "<label for='genre_periodeF' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"genre_periode\" id=\"genre_periodeF\" value=\"F\" ";
        if ($genre_periode == 'F') {echo " checked";}
        echo " />&nbsp;Féminin</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Faire apparaitre le nom de l'établissement sur le bulletin :<br />(<i>certains établissements ont le nom dans le Logo</i>)
        </td>
	<?php
		if(getSettingValue("bull_affich_nom_etab")){
			$bull_affich_nom_etab=getSettingValue("bull_affich_nom_etab");
		}
		else{
			$bull_affich_nom_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_nom_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_nom_etab\" id=\"bull_affich_nom_etab_y\" value=\"y\" ";
        if ($bull_affich_nom_etab == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_nom_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_nom_etab\" id=\"bull_affich_nom_etab_n\" value=\"n\" ";
        if ($bull_affich_nom_etab == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Faire apparaitre l'adresse de l'établissement sur le bulletin :<br />(<i>certains établissements ont l'adresse dans le Logo</i>)
        </td>
	<?php
		if(getSettingValue("bull_affich_adr_etab")){
			$bull_affich_adr_etab=getSettingValue("bull_affich_adr_etab");
		}
		else{
			$bull_affich_adr_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_adr_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_adr_etab\" id=\"bull_affich_adr_etab_y\" value=\"y\" ";
        if ($bull_affich_adr_etab == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_adr_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_adr_etab\" id=\"bull_affich_adr_etab_n\" value=\"n\" ";
        if ($bull_affich_adr_etab == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Faire apparaitre les <?php echo $gepi_denom_mention;?>s (<i>Félicitations, encouragements, avertissements,...</i>) avec l'avis du conseil de classe.
        </td>
	<?php
		if(getSettingValue("bull_affich_mentions")){
			$bull_affich_mentions=getSettingValue("bull_affich_mentions");
		}
		else{
			$bull_affich_mentions="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_mentions_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_mentions\" id=\"bull_affich_mentions_y\" value=\"y\" ";
        if ($bull_affich_mentions == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_mentions_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_mentions\" id=\"bull_affich_mentions_n\" value=\"n\" ";
        if ($bull_affich_mentions == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Faire apparaitre l'intitulé <?php echo $gepi_denom_mention;?>s en gras devant la <?php echo $gepi_denom_mention;?> choisie pour un élève avec l'avis du conseil de classe.
        </td>
	<?php
		if(getSettingValue("bull_affich_intitule_mentions")){
			$bull_affich_intitule_mentions=getSettingValue("bull_affich_intitule_mentions");
		}
		else{
			$bull_affich_intitule_mentions="y";
		}
	?>
        <td>
	<?php
        echo "<label for='bull_affich_intitule_mentions_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_intitule_mentions\" id=\"bull_affich_intitule_mentions_y\" value=\"y\" ";
        if ($bull_affich_intitule_mentions == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='bull_affich_intitule_mentions_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"bull_affich_intitule_mentions\" id=\"bull_affich_intitule_mentions_n\" value=\"n\" ";
        if ($bull_affich_intitule_mentions == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

</table>
<hr />


<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>


<hr />
<?php
//Informations devant figurer sur le bulletin scolaire</H3>
?>
<h3>Informations devant figurer sur le bulletin scolaire</h3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Informations'>
<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le nom court de la classe :
        </td>
        <!--td style='width:8em; text-align:right;'-->
        <td style='width:8em;'>
        <?php
        echo "<input type=\"radio\" name=\"bull_mention_nom_court\" id=\"bull_mention_nom_courty\" value=\"yes\" ";
        if (getSettingValue("bull_mention_nom_court") == 'yes') echo " checked";
        echo " /><label for='bull_mention_nom_courty' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_mention_nom_court\" id=\"bull_mention_nom_courtn\" value=\"no\" ";
        if (getSettingValue("bull_mention_nom_court") == 'no') echo " checked";
        echo " /><label for='bull_mention_nom_courtn' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher la mention "doublant" ou "doublante", le cas échéant :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_mention_doublant\" id=\"bull_mention_doublanty\" value=\"yes\" ";
        if (getSettingValue("bull_mention_doublant") == 'yes') echo " checked";
        echo " /><label for='bull_mention_doublanty' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_mention_doublant\" id=\"bull_mention_doublantn\" value=\"no\" ";
        if (getSettingValue("bull_mention_doublant") == 'no') echo " checked";
        echo " /><label for='bull_mention_doublantn' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les informations sur l'élève sur une seule ligne <i>(si non une information par ligne)</i> :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_eleve_une_ligne\" id=\"bull_affiche_eleve_une_ligney\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_eleve_une_ligne") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_eleve_une_ligney' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_eleve_une_ligne\" id=\"bull_affiche_eleve_une_lignen\" value=\"no\" ";
        if (getSettingValue("bull_affiche_eleve_une_ligne") == 'no') echo " checked";
        echo " /><label for='bull_affiche_eleve_une_lignen' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les appréciations des matières :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_appreciations\" id=\"bull_affiche_appreciationsy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_appreciations") == 'y') echo " checked";
        echo " /><label for='bull_affiche_appreciationsy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_appreciations\" id=\"bull_affiche_appreciationsn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_appreciations") != 'y') echo " checked";
        echo " /><label for='bull_affiche_appreciationsn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <!-- 20130215 -->

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <strong>Absences&nbsp;</strong><br />
        Afficher les données sur les absences&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_absences\" id=\"bull_affiche_absencesy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_absences") == 'y') echo " checked";
        echo " /><label for='bull_affiche_absencesy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_absences\" id=\"bull_affiche_absencesn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_absences") != 'y') echo " checked";
        echo " /><label for='bull_affiche_absencesn' style='cursor: pointer;'>&nbsp;Non</label>";

		// Si seule cette case est cochée, on affichera l'appréciation du CPE, mais pas les absences/retards.
        ?>
        </td>
    </tr>

    <?php
        // A mettre dans 162_to_163
        if((getSettingValue("bull_affiche_abs_tot")=="")&&(getSettingValue("bull_affiche_abs_nj")=="")&&(getSettingValue("bull_affiche_abs_ret")=="")&&(getSettingValue("bull_affiche_abs_cpe")=="")) {
            if(getSettingValue("bull_affiche_absences")=="y") {
                saveSetting("bull_affiche_abs_tot", "y");
                saveSetting("bull_affiche_abs_nj", "y");
                saveSetting("bull_affiche_abs_ret", "y");
                saveSetting("bull_affiche_abs_cpe", "y");
            }
            else {
                saveSetting("bull_affiche_abs_tot", "n");
                saveSetting("bull_affiche_abs_nj", "n");
                saveSetting("bull_affiche_abs_ret", "n");
                saveSetting("bull_affiche_abs_cpe", "n");
            }
        }
    ?>

    <tr <?php /*if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++;*/ ?>>
        <td style="font-variant: small-caps;">
        Afficher les totaux d'absences&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_abs_tot\" id=\"bull_affiche_abs_toty\" value=\"y\" ";
        if (getSettingValue("bull_affiche_abs_tot") == 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_toty' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_abs_tot\" id=\"bull_affiche_abs_totn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_abs_tot") != 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_totn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php /*if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++;*/ ?>>
        <td style="font-variant: small-caps;">
        Afficher le nombre d'absences non justifiées&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_abs_nj\" id=\"bull_affiche_abs_njy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_abs_nj") == 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_njy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_abs_nj\" id=\"bull_affiche_abs_njn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_abs_nj") != 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_njn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php /*if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++;*/ ?>>
        <td style="font-variant: small-caps;">
        Afficher le nombre de retards&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_abs_ret\" id=\"bull_affiche_abs_rety\" value=\"y\" ";
        if (getSettingValue("bull_affiche_abs_ret") == 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_rety' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_abs_ret\" id=\"bull_affiche_abs_retn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_abs_ret") != 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_retn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php /*if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++;*/ ?>>
        <td style="font-variant: small-caps;">
        Afficher le nom du C.P.E.&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_abs_cpe\" id=\"bull_affiche_abs_cpey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_abs_cpe") == 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_cpey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_abs_cpe\" id=\"bull_affiche_abs_cpen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_abs_cpe") != 'y') echo " checked";
        echo " /><label for='bull_affiche_abs_cpen' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les avis du conseil de classe :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_avis\" id=\"bull_affiche_avisy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_avis") == 'y') echo " checked";
        echo " /><label for='bull_affiche_avisy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_avis\" id=\"bull_affiche_avisn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_avis") != 'y') echo " checked";
        echo " /><label for='bull_affiche_avisn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les données sur les AID :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_aid") == 'y') echo " checked";
        echo " /><label for='bull_affiche_aidy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_aid") != 'y') echo " checked";
        echo " /><label for='bull_affiche_aidn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro du bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_numero\" id=\"bull_affiche_numeroy\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_numero") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_numeroy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_numero\" id=\"bull_affiche_numeron\" value=\"no\" ";
        if (getSettingValue("bull_affiche_numero") == 'no') echo " checked";
        echo " /><label for='bull_affiche_numeron' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les graphiques indiquant les niveaux (A, B, C+, C-, D ou E) :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_graphiques\" id=\"bull_affiche_graphiquesy\" value=\"yes\" ";
        if (getSettingValue("bull_affiche_graphiques") == 'yes') echo " checked";
        echo " /><label for='bull_affiche_graphiquesy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_graphiques\" id=\"bull_affiche_graphiquesn\" value=\"no\" ";
        if (getSettingValue("bull_affiche_graphiques") != 'yes') echo " checked";
        echo " /><label for='bull_affiche_graphiquesn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le nom du professeur principal et du chef d'établissement :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_signature\" id=\"bull_affiche_signaturey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_signature") == 'y') echo " checked";
        echo " /><label for='bull_affiche_signaturey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_signature\" id=\"bull_affiche_signaturen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_signature") != 'y') echo " checked";
        echo " /><label for='bull_affiche_signaturen' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>

    <!--tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;
    //$nb_ligne++;
    ?>>
        <td style="font-variant: small-caps;">
        Insérer la signature ou cachet de l'établissement&nbsp;:
        <?php
			echo "<br />\n(<em>sous réserve qu'une ";
			if($_SESSION['statut']=='administrateur') {
				echo "<a href='../gestion/gestion_signature.php'>image de signature</a>";
			}
			else {
				echo "image de signature";
			}
			echo " ait été uploadée en administrateur<br />et que vous soyez autorisé à utiliser cette signature</em>)\n";
        ?>
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_img_signature\" id=\"bull_affiche_img_signaturey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_img_signature") == 'y') echo " checked";
        echo " /><label for='bull_affiche_img_signaturey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_img_signature\" id=\"bull_affiche_img_signaturen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_img_signature") != 'y') echo " checked";
        echo " /><label for='bull_affiche_img_signaturen' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr-->

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps; vertical-align:top;">
        Dimensions maximales (<em>en pixels</em>) de l'image de la signature ou cachet de l'établissement&nbsp;:
        <?php
            if(acces('/gestion/gestion_signature.php', $_SESSION['statut'])) {
                echo "<br />Voir <a href='../gestion/gestion_signature.php' target='_blank'>Gestion du ou des fichiers de signature</a>";
            }
        ?>
       </td>
        <td>
        <?php
        $bull_largeur_img_signature=getSettingValue('bull_largeur_img_signature');
        if(($bull_largeur_img_signature=='')||(!preg_match("/^[0-9]*$/", $bull_largeur_img_signature))||($bull_largeur_img_signature==0)) {$bull_largeur_img_signature=200;}
        echo "Largeur&nbsp;: <input type=\"text\" name=\"bull_largeur_img_signature\" id=\"bull_largeur_img_signature\" value=\"$bull_largeur_img_signature\" size=\"3\" onKeyDown=\"clavier_2(this.id,event,1,500);\" autocomplete=\"off\" />\n";

        echo "<br />\n";

        $bull_hauteur_img_signature=getSettingValue('bull_hauteur_img_signature');
        if(($bull_hauteur_img_signature=='')||(!preg_match("/^[0-9]*$/", $bull_hauteur_img_signature))||($bull_hauteur_img_signature==0)) {$bull_hauteur_img_signature=200;}
        echo "Hauteur&nbsp;: <input type=\"text\" name=\"bull_hauteur_img_signature\" id=\"bull_hauteur_img_signature\" value=\"$bull_hauteur_img_signature\" size=\"3\" onKeyDown=\"clavier_2(this.id,event,1,500);\" autocomplete=\"off\" />\n";
        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher l'établissement d'origine sur le bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_etab\" id=\"bull_affiche_etaby\" value=\"y\" ";
        if (getSettingValue("bull_affiche_etab") == 'y') echo " checked";
        echo " /><label for='bull_affiche_etaby' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_etab\" id=\"bull_affiche_etabn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_etab") != 'y') echo " checked";
        echo " /><label for='bull_affiche_etabn' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>


<?php
if (getSettingValue("active_module_trombinoscopes")=='y') {
	echo "<tr ";
	if($nb_ligne % 2){echo "bgcolor=".$bgcolor;}
	$nb_ligne++;
	echo ">\n";
?>
        <td style="font-variant: small-caps;">
        Afficher la photo de l'élève sur le bulletin :
        </td>
        <td>
<?php
	echo "<input type='radio' name='activer_photo_bulletin' id='activer_photo_bulletiny' value='y'";
	if (getSettingValue("activer_photo_bulletin")=='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('afficher')\" /><label for='activer_photo_bulletiny' style='cursor: pointer;'>&nbsp;Oui</label>\n";
	echo "<input type='radio' name='activer_photo_bulletin' id='activer_photo_bulletinn' value='n'";
	if (getSettingValue("activer_photo_bulletin")!='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('cacher')\" /><label for='activer_photo_bulletinn' style='cursor: pointer;'>&nbsp;Non</label>\n";
?>
        </td>
    </tr>
<?php
	if(getSettingValue("bull_photo_hauteur_max")){
		$bull_photo_hauteur_max=getSettingValue("bull_photo_hauteur_max");
	}
	else{
		$bull_photo_hauteur_max=80;
	}

	if(getSettingValue("bull_photo_largeur_max")){
		$bull_photo_largeur_max=getSettingValue("bull_photo_largeur_max");
	}
	else{
		$bull_photo_largeur_max=80;
	}
?>
    <tr id='ligne_bull_photo_hauteur_max'>
	<td style="font-variant: small-caps;"><label for='bull_photo_hauteur_max' style='cursor: pointer;'>Hauteur maximale de la photo en pixels :</label></td>
	<td><input type="text" name="bull_photo_hauteur_max" id="bull_photo_hauteur_max" size='4' value="<?php echo $bull_photo_hauteur_max;?>" /></td>
    </tr>
    <tr id='ligne_bull_photo_largeur_max'>
	<td style="font-variant: small-caps;"><label for='bull_photo_largeur_max' style='cursor: pointer;'>Largeur maximale de la photo en pixels :</label></td>
	<td><input type="text" name="bull_photo_largeur_max" id="bull_photo_largeur_max" size='4' value="<?php echo $bull_photo_largeur_max;?>" />

	<script type='text/javascript'>
		function aff_lig_photo(mode){
			if(mode=='afficher'){
				document.getElementById('ligne_bull_photo_hauteur_max').style.display='';
				document.getElementById('ligne_bull_photo_largeur_max').style.display='';
			}
			else{
				document.getElementById('ligne_bull_photo_hauteur_max').style.display='none';
				document.getElementById('ligne_bull_photo_largeur_max').style.display='none';
			}
		}

		if(document.getElementById('activer_photo_bulletiny').checked==false){
			aff_lig_photo('cacher');
		}
	</script>
	</td>
    </tr>
<?php
}
?>




    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro de téléphone de l'établissement :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_tel\" id=\"bull_affiche_tely\" value=\"y\" ";
        if (getSettingValue("bull_affiche_tel") == 'y') echo " checked";
        echo " /><label for='bull_affiche_tely' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_tel\" id=\"bull_affiche_teln\" value=\"n\" ";
        if (getSettingValue("bull_affiche_tel") != 'y') echo " checked";
        echo " /><label for='bull_affiche_teln' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro de fax de l'établissement :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_fax\" id=\"bull_affiche_faxy\" value=\"y\" ";
        if (getSettingValue("bull_affiche_fax") == 'y') echo " checked";
        echo " /><label for='bull_affiche_faxy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_fax\" id=\"bull_affiche_faxn\" value=\"n\" ";
        if (getSettingValue("bull_affiche_fax") != 'y') echo " checked";
        echo " /><label for='bull_affiche_faxn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;" colspan='2'>
        <label for='bull_intitule_app' style='cursor: pointer;'>Intitulé de la colonne Appréciations :</label>
        <?php
		echo "<input type=\"text\" name=\"bull_intitule_app\" id=\"bull_intitule_app\" value=\"".getSettingValue('bull_intitule_app')."\" size='100' />";
        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro INE de l'élève :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_INE_eleve\" id=\"bull_affiche_INE_elevey\" value=\"y\" ";
        if (getSettingValue("bull_affiche_INE_eleve") == 'y') echo " checked";
        echo " /><label for='bull_affiche_INE_elevey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_INE_eleve\" id=\"bull_affiche_INE_eleven\" value=\"n\" ";
        if (getSettingValue("bull_affiche_INE_eleve") != 'y') echo " checked";
        echo " /><label for='bull_affiche_INE_eleven' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher la formule figurant en bas de chaque bulletin :
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"bull_affiche_formule\" id=\"bull_affiche_formuley\" value=\"y\" ";
        if (getSettingValue("bull_affiche_formule") == 'y') echo " checked";
        echo " /><label for='bull_affiche_formuley' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"bull_affiche_formule\" id=\"bull_affiche_formulen\" value=\"n\" ";
        if (getSettingValue("bull_affiche_formule") != 'y') echo " checked";
        echo " /><label for='bull_affiche_formulen' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;" colspan="2">
        <label for='no_anti_inject_bull_formule_bas' style='cursor: pointer;'>Formule figurant en bas de chaque bulletin :</label>
        <input type="text" name="no_anti_inject_bull_formule_bas" id="no_anti_inject_bull_formule_bas" size="100" value="<?php echo(getSettingValue("bull_formule_bas")); ?>" />
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Choix de l'apparence du bulletin (emplacement et regroupement des moyennes de la classe)
		<ul>
		<li><i><label for='choix_bulletin1' style='cursor: pointer;'>Toutes les informations chiffrées sur la classe et l'élève sont avant la colonne <?php echo getSettingValue('bull_intitule_app')?>.</label></i><br />
		<li><i><label for='choix_bulletin2' style='cursor: pointer;'>Idem choix 1. Les informations sur la classe sont regroupées en une catégorie "Pour la classe".</label></i><br />
		<li><i><label for='choix_bulletin3' style='cursor: pointer;'>Idem choix 2. Les informations pour la classe sont situées après la colonne <?php echo getSettingValue('bull_intitule_app')?>.</label></i><br />
        </ul>
		</td>
        <td> <br />
        <?php
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin1' value='1'";
		if (getSettingValue("choix_bulletin") == '1') echo " checked";
		echo " /> <label for='choix_bulletin1' style='cursor: pointer;'>Choix 1</label><br />";
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin2' value='2'";
		if (getSettingValue("choix_bulletin") == '2') echo " checked";
		echo " /> <label for='choix_bulletin2' style='cursor: pointer;'>Choix 2</label><br />";
		echo "<input type='radio' name='choix_bulletin' id='choix_bulletin3' value='3'";
		//echo "toto".getSettingValue("choix_bulletin");
		if (getSettingValue("choix_bulletin") == '3') echo " checked";
		echo " /> <label for='choix_bulletin3' style='cursor: pointer;'>Choix 3</label><br />";
        ?>
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">Afficher les moyennes minimale, classe et maximale dans une seule colonne pour gagner de la place pour l'appréciation : </td>
        <td>
	    <?php
        echo "<input type=\"radio\" name=\"min_max_moyclas\" id=\"min_max_moyclas1\" value='1' ";
        if (getSettingValue("min_max_moyclas") == '1') echo " checked";
        echo " /><label for='min_max_moyclas1' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"min_max_moyclas\" id=\"min_max_moyclas0\" value='0' ";
        if (getSettingValue("min_max_moyclas") != '1') echo " checked";
        echo " /><label for='min_max_moyclas0' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">Afficher les moyennes des périodes précédentes dans la cellule Moyenne de l'élève :<br />
            <!--(<em>choix incompatible avec l'affichage des moyennes minimale, classe et maximale dans une seule colonne</em>)-->
        </td>
        <td>
	    <?php
        echo "<input type=\"radio\" name=\"moyennes_periodes_precedentes\" id=\"moyennes_periodes_precedentes_y\" value='y' ";
        if (getSettingValue("moyennes_periodes_precedentes") == 'y') echo " checked";
        echo " /><label for='moyennes_periodes_precedentes_y' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"moyennes_periodes_precedentes\" id=\"moyennes_periodes_precedentes_n\" value='n' ";
        if (getSettingValue("moyennes_periodes_precedentes") != 'y') echo " checked";
        echo " /><label for='moyennes_periodes_precedentes_n' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">Afficher les moyennes annuelles des enseignements dans la cellule Moyenne de l'élève :<br />
            <!--(<em>choix incompatible avec l'affichage des moyennes minimale, classe et maximale dans une seule colonne</em>)-->
        </td>
        <td>
	    <?php
        echo "<input type=\"radio\" name=\"moyennes_annee\" id=\"moyennes_annee_y\" value='y' ";
        if (getSettingValue("moyennes_annee") == 'y') echo " checked";
        echo " /><label for='moyennes_annee_y' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"moyennes_annee\" id=\"moyennes_annee_n\" value='n' ";
        if (getSettingValue("moyennes_annee") != 'y') echo " checked";
        echo " /><label for='moyennes_annee_n' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>

</table>

<script type='text/javascript'>

</script>

<hr />

<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>

<hr />

<?php
} // Fin test getSettingAOui('active_bulletins')
?>

<a name='bloc_adresse'></a>
<H3>Bloc adresse</H3>
<center><table border="1" cellpadding="10" width="90%" summary='Bloc adresse'><tr><td>
Ces options contrôlent le positionnement du bloc adresse du responsable de l'élève directement sur le bulletin (<em>et non sur la page de garde - voir ci-dessous</em>). L'affichage ou non de ce bloc est contrôlé classe par classe, au niveau du paramétrage de la classe.<br />
<br />
<?php

	echo "Contrôler les paramétrages aberrants pour un format <a href='".$_SERVER['PHP_SELF']."?check_param_bloc_adresse_html=a4#bloc_adresse' onclick=\"return confirm_abandon (this, change, '$themessage')\">A4</a> ou un un format <a href='".$_SERVER['PHP_SELF']."?check_param_bloc_adresse_html=a3#bloc_adresse' onclick=\"return confirm_abandon (this, change, '$themessage')\">A3</a>";


	if(isset($_GET['check_param_bloc_adresse_html'])) {
		echo "<br />\n";
		if($_GET['check_param_bloc_adresse_html']=='a4') {
			echo "<p>Contrôle des paramètres pour la version A4&nbsp;:</p>\n";
			$retour_check=check_param_bloc_adresse_html('a4');
		}
		else {
			echo "<p>Contrôle des paramètres pour la version A3&nbsp;:</p>\n";
			$retour_check=check_param_bloc_adresse_html('a3');
		}

		if($retour_check=='') {
			echo "<p style='color:green'>";
			echo "Pas de valeur aberrante trouvée.";
		}
		else {
			echo "<p style='color:red'>";
			echo $retour_check;
		}
		echo "</p>\n";
	}

	echo "<br />\n<p style='text-indent: -4em; margin-left: 4em;'><em>NOTE&nbsp;:</em> Le bloc adresse des responsables d'un élève est positionné dans les bulletins HTML et Fiches Bienvenue avec les mêmes paramètres.</p>\n";
?>

</td></tr></table></center>

<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Bloc adresse'>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;;$nb_ligne++;?>>
        <td colspan='2' style="font-variant: small-caps;">
	<a href="javascript:SetDefaultValues('Adresse')">Rétablir les paramètres par défaut</a>
        </td>
     </tr>


    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_right' style='cursor: pointer;'>Espace en mm entre la marge droite de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="addressblock_padding_right" id="addressblock_padding_right" size="20" value="<?php echo(getSettingValue("addressblock_padding_right")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge droite d'impression pour calculer l'espace entre le bord droit de la feuille et le bloc adresse</i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_top' style='cursor: pointer;'>Espace en mm entre la marge haute de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="addressblock_padding_top" id="addressblock_padding_top" size="20" value="<?php echo(getSettingValue("addressblock_padding_top")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge haute d'impression pour calculer l'espace entre le bord haut de la feuille et le bloc adresse</i></td>
    </tr>

<?php
if(getSettingAOui('active_bulletins')) {
?>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_padding_text' style='cursor: pointer;'>Espace vertical en mm entre le bloc "adresse" et le bloc des résultats :</label>
        </td>
        <td><input type="text" name="addressblock_padding_text" id="addressblock_padding_text" size="20" value="<?php echo(getSettingValue("addressblock_padding_text")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>

<?php
} // Fin test getSettingAOui('active_bulletins')
?>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_length' style='cursor: pointer;'>Longueur en mm du bloc "adresse" :</label>
        </td>
        <td><input type="text" name="addressblock_length" id="addressblock_length" size="20" value="<?php echo(getSettingValue("addressblock_length")); ?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_font_size' style='cursor: pointer;'>Taille en points des textes du bloc "adresse" :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_font_size")){
			$addressblock_font_size=12;
		}
		else{
			$addressblock_font_size=getSettingValue("addressblock_font_size");
		}
	?>
        <td><input type="text" name="addressblock_font_size" id="addressblock_font_size" size="20" value="<?php echo $addressblock_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

<?php
if(getSettingAOui('active_bulletins')) {
?>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_logo_etab_prop' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au logo et à l'adresse de l'établissement :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_logo_etab_prop")){
			$addressblock_logo_etab_prop=50;
		}
		else{
			$addressblock_logo_etab_prop=getSettingValue("addressblock_logo_etab_prop");
		}
	?>
        <td><input type="text" name="addressblock_logo_etab_prop" id="addressblock_logo_etab_prop" size="20" value="<?php echo $addressblock_logo_etab_prop; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='addressblock_classe_annee' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au bloc "Classe, année, période" :</label>
        </td>
	<?php
		if(!getSettingValue("addressblock_classe_annee")){
			$addressblock_classe_annee=35;
		}
		else{
			$addressblock_classe_annee=getSettingValue("addressblock_classe_annee");
		}
	?>
        <td><input type="text" name="addressblock_classe_annee" id="addressblock_classe_annee" size="20" value="<?php echo $addressblock_classe_annee; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='bull_ecart_bloc_nom' style='cursor: pointer;'>Nombre de sauts de ligne entre le bloc Logo+Etablissement et le bloc Nom, prénom,... de l'élève :</label>
        </td>
	<?php
		if(!getSettingValue("bull_ecart_bloc_nom")){
			$bull_ecart_bloc_nom=0;
		}
		else{
			$bull_ecart_bloc_nom=getSettingValue("bull_ecart_bloc_nom");
		}
	?>
        <td><input type="text" name="bull_ecart_bloc_nom" id="bull_ecart_bloc_nom" size="20" value="<?php echo $bull_ecart_bloc_nom; ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>

<?php
} // Fin test getSettingAOui('active_bulletins')
?>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <font color='red'>Activer l'affichage des bordures pour comprendre la présentation avec bloc "adresse"</font> :<br />
		<span style='font-size:x-small;'>Il faut ajuster les paramètres des champs '<i>Espace en mm entre la marge haute de la feuille et le bloc "adresse"</i>', '<i>Longueur en mm du bloc "adresse"</i>' et '<i>Proportion (en % de la largeur de page) allouée au logo et à l'adresse de l'établissement</i>' pour que les cadres bleu et vert n'entrent pas en collision (<i>vous pouvez modifier la taille de la fenêtre du navigateur à l'aide de la souris pour comprendre les éventuelles abérrations de présentation pour certaines combinaisons de valeurs</i>).</span>
        </td>
	<?php
		if(!getSettingValue("addressblock_debug")){
			$addressblock_debug="n";
		}
		else{
			$addressblock_debug=getSettingValue("addressblock_debug");
		}
	?>
        <td valign='top'><input type="radio" id="addressblock_debugy" name="addressblock_debug" value="y" <?php if($addressblock_debug=="y"){echo "checked";}?> /><label for='addressblock_debugy' style='cursor: pointer;'> Oui</label> <input type="radio" id="addressblock_debugn" name="addressblock_debug" value="n" <?php if($addressblock_debug=="n"){echo "checked";}?> /><label for='addressblock_debugn' style='cursor: pointer;'> Non</label>
        </td>
    </tr>
</table>

<?php
if(getSettingAOui('active_bulletins')) {
	echo "<hr />";
}
?>

<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>

<?php
if(getSettingAOui('active_bulletins')) {
?>

<hr />
<H3>Page de garde</H3>
<center><table border="1" cellpadding="10" width="90%" summary='Page de garde'><tr><td>
La page de garde contient les informations suivantes :
<ul>
<li>l'adresse où envoyer le bulletin. Si vous utilisez des enveloppes à fenêtre, vous pouvez régler les paramètres ci-dessous pour qu'elle apparaisse dans le cadre prévu à cet effet,</li>
<li>un texte que vous pouvez personnaliser (voir plus bas).</li>
</ul>
<b><a href='javascript:centrerpopup("./modele_page_garde.php",600,600,"scrollbars=yes,statusbar=yes,menubar=yes,resizable=yes")'>Aperçu de la page de garde</a></b>
(Attention : la mise en page <!--des bulletins -->est très différente à l'écran et à l'impression.
Veillez à utiliser la fonction "aperçu avant impression" afin de vous rendre compte du résultat.
</td></tr></table></center>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary='Page de garde'>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;"><label for='page_garde_imprime' style='cursor: pointer;'>Imprimer les pages de garde : </label></td>
        <td><input type="checkbox" name="page_garde_imprime" id="page_garde_imprime" value="yes" <?php if (getSettingValue("page_garde_imprime")=='yes') echo "checked"; ?>/>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_left' style='cursor: pointer;'>Espace en cm entre la marge gauche de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_left" id="page_garde_padding_left" size="20" value="<?php echo(getSettingValue("page_garde_padding_left")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge gauche d'impression pour calculer l'espace entre le bord droit de la feuille et le bloc adresse</i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_top' style='cursor: pointer;'>Espace en cm entre la marge haute de la feuille et le bloc "adresse" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_top" id="page_garde_padding_top" size="20" value="<?php echo(getSettingValue("page_garde_padding_top")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge haute d'impression pour calculer l'espace entre le bord haut de la feuille et le bloc adresse</i></td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='page_garde_padding_text' style='cursor: pointer;'>Espace en cm entre le bloc "adresse" et le bloc "texte" :</label>
        </td>
        <td><input type="text" name="page_garde_padding_text" id="page_garde_padding_text" size="20" value="<?php echo(getSettingValue("page_garde_padding_text")); ?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
    <?php
    $impression = getSettingValue("page_garde_texte");
    echo "<td colspan=\"2\" valign=\"top\"  style=\"font-variant: small-caps;\">Texte de la page de garde apparaissant à la suite de l'adresse : </td>
	</tr>";
    // Modif : on utilise toute la largeur de la page pour afficher l'éditeur de textes
    echo "
	<tr><td colspan=\"2\" ><div class='small' style='width: 820px;'>
		<i>Mise en forme du message :</i>";

    $oCKeditor = new CKeditor('../ckeditor/');
    $oCKeditor->editor('no_anti_inject_page_garde_texte',$impression);
?>

		</div>
	</td></tr>

</table>

<hr />
<p style="text-align: center;"><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></p>

<?php
} // Fin test getSettingAOui('active_bulletins')
?>

</form>

<?php require("../lib/footer.inc.php");
