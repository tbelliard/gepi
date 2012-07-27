<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Paramètres de configuration des relevés de notes HTML";

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


$sql="SELECT 1=1 FROM droits WHERE id='/cahier_notes/param_releve_html.php';";
$res_test=mysql_query($sql);
if (mysql_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/cahier_notes/param_releve_html.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F','F', 'Relevé de notes', '1');";
	$res_insert=mysql_query($sql);
}

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
$reg_ok = 'yes';
$msg = '';
$bgcolor = "#DEDEDE";


// Tableau des couleurs HTML:
$tabcouleur=Array("aliceblue","antiquewhite","aqua","aquamarine","azure","beige","bisque","black","blanchedalmond","blue","blueviolet","brown","burlywood","cadetblue","chartreuse","chocolate","coral","cornflowerblue","cornsilk","crimson","cyan","darkblue","darkcyan","darkgoldenrod","darkgray","darkgreen","darkkhaki","darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkseagreen","darkslateblue","darkslategray","darkturquoise","darkviolet","deeppink","deepskyblue","dimgray","dodgerblue","firebrick","floralwhite","forestgreen","fuchsia","gainsboro","ghostwhite","gold","goldenrod","gray","green","greenyellow","honeydew","hotpink","indianred","indigo","ivory","khaki","lavender","lavenderblush","lawngreen","lemonchiffon","lightblue","lightcoral","lightcyan","lightgoldenrodyellow","lightgreen","lightgrey","lightpink","lightsalmon","lightseagreen","lightskyblue","lightslategray","lightsteelblue","lightyellow","lime","limegreen","linen","magenta","maroon","mediumaquamarine","mediumblue","mediumorchid","mediumpurple","mediumseagreen","mediumslateblue","mediumspringgreen","mediumturquoise","mediumvioletred","midnightblue","mintcream","mistyrose","moccasin","navajowhite","navy","oldlace","olive","olivedrab","orange","orangered","orchid","palegoldenrod","palegreen","paleturquoise","palevioletred","papayawhip","peachpuff","peru","pink","plum","powderblue","purple","red","rosybrown","royalblue","saddlebrown","salmon","sandybrown","seagreen","seashell","sienna","silver","skyblue","slateblue","slategray","snow","springgreen","steelblue","tan","teal","thistle","tomato","turquoise","violet","wheat","white","whitesmoke","yellow","yellowgreen");


if (isset($_POST['ok'])) {
	check_token();

	if (isset($_POST['releve_textsize'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_textsize'])) || $_POST['releve_textsize'] < 1) {
			$_POST['releve_textsize'] = 10;
		}
		if (!saveSetting("releve_textsize", $_POST['releve_textsize'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_textsize !";
			$reg_ok = 'no';
		}
	}
	
	//==================================
	// AJOUT: boireaus
	if (isset($_POST['p_releve_margin'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['p_releve_margin'])) || $_POST['p_releve_margin'] < 1) {
			$_POST['p_releve_margin'] = 5;
		}
		if (!saveSetting("p_releve_margin", $_POST['p_releve_margin'])) {
			$msg .= "Erreur lors de l'enregistrement de p_releve_margin !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_body_marginleft'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_body_marginleft']))) {
			$_POST['releve_body_marginleft'] = 1;
		}
		if (!saveSetting("releve_body_marginleft", $_POST['releve_body_marginleft'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_body_marginleft !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	
	
	if (isset($_POST['releve_titlesize'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_titlesize'])) || $_POST['releve_titlesize'] < 1) {
			$_POST['releve_titlesize'] = 16;
		}
		if (!saveSetting("releve_titlesize", $_POST['releve_titlesize'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_titlesize !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_cellpadding'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_cellpadding'])) || $_POST['releve_cellpadding'] < 0) {
			$_POST['releve_cellpadding'] = 5;
		}
		if (!saveSetting("releve_cellpadding", $_POST['releve_cellpadding'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_cellpadding !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_cellspacing'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_cellspacing'])) || $_POST['releve_cellspacing'] < 0) {
			$_POST['releve_cellspacing'] = 2;
		}
		if (!saveSetting("releve_cellspacing", $_POST['releve_cellspacing'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_cellspacing !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_largeurtableau'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_largeurtableau'])) || $_POST['releve_largeurtableau'] < 1) {
			$_POST['largeurtableau'] = 800;
		}
		if (!saveSetting("releve_largeurtableau", $_POST['releve_largeurtableau'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_largeurtableau !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_col_matiere_largeur'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_col_matiere_largeur'])) || $_POST['releve_col_matiere_largeur'] < 1) {
			$_POST['releve_col_matiere_largeur'] = 150;
		}
		if (!saveSetting("releve_col_matiere_largeur", $_POST['releve_col_matiere_largeur'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_col_matiere_largeur !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_ecart_entete'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_ecart_entete']))) {
			$_POST['releve_ecart_entete'] = 0;
		}
		if (!saveSetting("releve_ecart_entete", $_POST['releve_ecart_entete'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_ecart_entete !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_addressblock_padding_right'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_padding_right']))) {
			$_POST['releve_addressblock_padding_right'] = 0;
		}
		if (!saveSetting("releve_addressblock_padding_right", $_POST['releve_addressblock_padding_right'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_padding_right !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_addressblock_padding_top'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_padding_top']))) {
			$_POST['releve_addressblock_padding_top'] = 0;
		}
		if (!saveSetting("releve_addressblock_padding_top", $_POST['releve_addressblock_padding_top'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_padding_top !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_addressblock_padding_text'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_padding_text']))) {
			$_POST['releve_addressblock_padding_text'] = 0;
		}
		if (!saveSetting("releve_addressblock_padding_text", $_POST['releve_addressblock_padding_text'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_padding_text !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_addressblock_length'])) {
	
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_length']))) {
			$_POST['releve_addressblock_length'] = 60;
		}
		if (!saveSetting("releve_addressblock_length", $_POST['releve_addressblock_length'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_length !";
			$reg_ok = 'no';
		}
	}
	
	
	//==================================
	// Ajout: boireaus
	if (isset($_POST['releve_addressblock_font_size'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_font_size']))) {
			$_POST['releve_addressblock_font_size'] = 12;
		}
		if (!saveSetting("releve_addressblock_font_size", $_POST['releve_addressblock_font_size'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_font_size !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_addressblock_logo_etab_prop'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_logo_etab_prop']))) {
				$releve_addressblock_logo_etab_prop=50;
		}
		else{
				$releve_addressblock_logo_etab_prop=$_POST['releve_addressblock_logo_etab_prop'];
		}
	}
	else{
		if(getSettingValue("releve_addressblock_logo_etab_prop")){
			$releve_addressblock_logo_etab_prop=getSettingValue("releve_addressblock_logo_etab_prop");
		}
		else{
			$releve_addressblock_logo_etab_prop=50;
		}
	}
	
	if (isset($_POST['releve_addressblock_classe_annee'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_addressblock_classe_annee']))) {
				$releve_addressblock_classe_annee=35;
		}
		else{
				$releve_addressblock_classe_annee=$_POST['releve_addressblock_classe_annee'];
		}
	}
	else{
		if(getSettingValue("releve_addressblock_classe_annee")){
			$releve_addressblock_classe_annee=getSettingValue("releve_addressblock_classe_annee");
		}
		else{
			$releve_addressblock_classe_annee=30;
		}
	}
	
	if((isset($_POST['releve_addressblock_classe_annee']))&&(isset($_POST['releve_addressblock_logo_etab_prop']))){
		$valtest=$releve_addressblock_logo_etab_prop+$releve_addressblock_classe_annee;
		if($valtest>100){
			$msg.="Erreur! La somme releve_addressblock_logo_etab_prop+releve_addressblock_classe_annee dépasse 100% de la largeur de la page !";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("releve_addressblock_logo_etab_prop", $releve_addressblock_logo_etab_prop)) {
				$msg .= "Erreur lors de l'enregistrement de releve_addressblock_logo_etab_prop !";
				$reg_ok = 'no';
			}
	
			if (!saveSetting("releve_addressblock_classe_annee", $releve_addressblock_classe_annee)) {
				$msg .= "Erreur lors de l'enregistrement de releve_addressblock_classe_annee !";
				$reg_ok = 'no';
			}
		}
	}
	
	
	if (isset($_POST['releve_ecart_bloc_nom'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_ecart_bloc_nom']))) {
			$_POST['releve_ecart_bloc_nom'] = 0;
		}
		if (!saveSetting("releve_ecart_bloc_nom", $_POST['releve_ecart_bloc_nom'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_ecart_bloc_nom !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($_POST['releve_addressblock_debug'])) {
		if (($_POST['releve_addressblock_debug']!="y")&&($_POST['releve_addressblock_debug']!="n")) {
			$_POST['releve_addressblock_debug'] = "n";
		}
		if (!saveSetting("releve_addressblock_debug", $_POST['releve_addressblock_debug'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_addressblock_debug !";
			$reg_ok = 'no';
		}
	}
	//==================================
	
	/*
	if (isset($NON_PROTECT['releve_formule_bas'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['releve_formule_bas']);
		if (!saveSetting("releve_formule_bas", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de releve_formule_bas !";
			$reg_ok = 'no';
		}
	}
	*/
	if (isset($_POST['releve_mention_nom_court'])) {
	
		if (!saveSetting("releve_mention_nom_court", $_POST['releve_mention_nom_court'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_mention_nom_court !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_mention_doublant'])) {
	
		if (!saveSetting("releve_mention_doublant", $_POST['releve_mention_doublant'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_mention_doublant !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affiche_eleve_une_ligne'])) {
	
		if (!saveSetting("releve_affiche_eleve_une_ligne", $_POST['releve_affiche_eleve_une_ligne'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_eleve_une_ligne !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affiche_formule'])) {
	
		if (!saveSetting("releve_affiche_formule", $_POST['releve_affiche_formule'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_formule !";
			$reg_ok = 'no';
		}
	}
	if (isset($_POST['releve_affiche_signature'])) {
	
		if (!saveSetting("releve_affiche_signature", $_POST['releve_affiche_signature'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_signature !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affiche_numero'])) {
	
		if (!saveSetting("releve_affiche_numero", $_POST['releve_affiche_numero'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_numero !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affiche_etab'])) {
		if (!saveSetting("releve_affiche_etab", $_POST['releve_affiche_etab'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_etab !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_bordure_classique'])) {
		if (!saveSetting("releve_bordure_classique", $_POST['releve_bordure_classique'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_bordure_classique !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['activer_photo_releve'])) {
		if (!saveSetting("activer_photo_releve", $_POST['activer_photo_releve'])) {
			$msg .= "Erreur lors de l'enregistrement de activer_photo_releve !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_photo_hauteur_max'])) {
		if (!saveSetting("releve_photo_hauteur_max", $_POST['releve_photo_hauteur_max'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_photo_hauteur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_photo_largeur_max'])) {
		if (!saveSetting("releve_photo_largeur_max", $_POST['releve_photo_largeur_max'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_photo_largeur_max !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_categ_font_size'])) {
		if (!(my_ereg ("^[0-9]{1,}$", $_POST['releve_categ_font_size']))) {
			$_POST['releve_categ_font_size'] = 10;
		}
		if (!saveSetting("releve_categ_font_size", $_POST['releve_categ_font_size'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_categ_font_size !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_affiche_INE_eleve'])) {
		if (!saveSetting("releve_affiche_INE_eleve", $_POST['releve_affiche_INE_eleve'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_INE_eleve !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_affiche_tel'])) {
		if (!saveSetting("releve_affiche_tel", $_POST['releve_affiche_tel'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_tel !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_affiche_fax'])) {
		if (!saveSetting("releve_affiche_fax", $_POST['releve_affiche_fax'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_fax !";
			$reg_ok = 'no';
		}
	}
	
	if(isset($_POST['releve_affiche_mail'])) {
		if (!saveSetting("releve_affiche_mail", $_POST['releve_affiche_mail'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_mail !";
			$reg_ok = 'no';
		}
	}
	
	
	if (isset($NON_PROTECT['releve_formule_bas'])) {
		$imp = traitement_magic_quotes($NON_PROTECT['releve_formule_bas']);
		if (!saveSetting("releve_formule_bas", $imp)) {
			$msg .= "Erreur lors de l'enregistrement de releve_formule_bas !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affiche_formule'])) {
	
		if (!saveSetting("releve_affiche_formule", $_POST['releve_affiche_formule'])) {
			$msg .= "Erreur lors de l'enregistrement de releve_affiche_formule !";
			$reg_ok = 'no';
		}
	}


	if (isset($_POST['releve_categ_bgcolor'])) {
		if((!in_array($_POST['releve_categ_bgcolor'],$tabcouleur))&&($_POST['releve_categ_bgcolor']!='')){
			$msg .= "Erreur lors de l'enregistrement de releve_categ_bgcolor ! (couleur invalide)";
			$reg_ok = 'no';
		}
		else{
			if (!saveSetting("releve_categ_bgcolor", $_POST['releve_categ_bgcolor'])) {
				$msg .= "Erreur lors de l'enregistrement de releve_categ_bgcolor !";
				$reg_ok = 'no';
			}
		}
	}
	
	if (isset($_POST['releve_affich_nom_etab'])) {
		if($_POST['releve_affich_nom_etab']=="n") {
			$releve_affich_nom_etab="n";
		}
		else{
			$releve_affich_nom_etab="y";
		}
		if (!saveSetting("releve_affich_nom_etab", $releve_affich_nom_etab)) {
			$msg .= "Erreur lors de l'enregistrement de releve_affich_nom_etab !";
			$reg_ok = 'no';
		}
	}
	
	if (isset($_POST['releve_affich_adr_etab'])) {
		if($_POST['releve_affich_adr_etab']=="n") {
			$releve_affich_adr_etab="n";
		}
		else{
			$releve_affich_adr_etab="y";
		}
		if (!saveSetting("releve_affich_adr_etab", $releve_affich_adr_etab)) {
			$msg .= "Erreur lors de l'enregistrement de releve_affich_adr_etab !";
			$reg_ok = 'no';
		}
	}
}

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
   $msg = "Enregistrement réussi !";
}


// End standart header
require_once("../lib/header.inc.php");
if (!loadSettings()) {
    die("Erreur chargement settings");
}
?>

<p class=bold><a href="visu_releve_notes_bis.php" onclick="self.close();return false;"><img src='../images/icons/back.png' alt='Refermer' class='back_link'/> Refermer </a></p>

<?php

// A FAIRE: Créer des droits

if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
    die("Droits insuffisants pour effectuer cette opération");
}

// Compteur pour alterner les couleurs de lignes
$nb_ligne=1;


$titre_infobulle="Paramètres communs HTML/PDF\n";
$texte_infobulle="Ce paramètre est commun aux relevés HTML et PDF.\n";
//$texte_infobulle.="\n";
$tabdiv_infobulle[]=creer_div_infobulle('parametres_communs_html_et_pdf',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');


?>


<form name="formulaire" action="param_releve_html.php" method="post" style="width: 100%;">
<?php
echo add_token_field();
?>
<H3>Mise en page du relevé de notes HTML</H3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary="Tableau des paramètres">

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_body_marginleft' style='cursor: pointer;'>Marge gauche de la page (en pixels)&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_body_marginleft" id="releve_body_marginleft" size="20" value="<?php
			if(getSettingValue("releve_body_marginleft")) {
				echo getSettingValue("releve_body_marginleft");
			}
			else{
				echo 1;
			}
		?>" onKeyDown="clavier_2(this.id,event,0,1000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_titlesize' style='cursor: pointer;'>Taille en points des gros titres&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_titlesize" id="releve_titlesize" size="20" value="<?php
			if(getSettingValue("releve_titlesize")) {
				echo getSettingValue("releve_titlesize");
			}
			else{
				echo 14;
			}
		?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_textsize' style='cursor: pointer;'>Taille en points du texte (hormis les titres)&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_textsize" id="releve_textsize" size="20" value="<?php
			if(getSettingValue("releve_textsize")) {
				echo getSettingValue("releve_textsize");
			}
			else{
				echo 8;
			}
		?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <!-- Début AJOUT: boireaus -->
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='p_releve_margin' style='cursor: pointer;'>Marges hautes et basses des paragraphes en points du texte (hormis les titres)&nbsp;:</label>
        </td>
        <td><input type="text" name="p_releve_margin" id="p_releve_margin" size="20" value="<?php
		if(getSettingValue("p_releve_margin")!=""){
			echo(getSettingValue("p_releve_margin"));
		}
		else{
			echo "5";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,40);" />
        </td>
    </tr>
    <!-- Fin AJOUT: boireaus -->
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_largeurtableau' style='cursor: pointer;'>Largeur du tableau en pixels&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_largeurtableau" id="releve_largeurtableau" size="20" value="<?php
		if(getSettingValue("releve_largeurtableau")!=""){
			echo(getSettingValue("releve_largeurtableau"));
		}
		else{
			echo "800";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,5000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_col_matiere_largeur' style='cursor: pointer;'>Largeur de la première colonne (matières) en pixels&nbsp;:</label><br />
        <span class="small">(Si le contenu d'une cellule de la colonne est plus grand que la taille prévue, la mention ci-dessus devient caduque. La colonne sera dans ce cas dimensionnée par le navigateur lui-même.)</span>
        </td>
        <td><input type="text" name="releve_col_matiere_largeur" id="releve_col_matiere_largeur" size="20" value="<?php
		if(getSettingValue("releve_col_matiere_largeur")!=""){
			echo(getSettingValue("releve_col_matiere_largeur"));
		}
		else{
			echo "150";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,2000);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_col_hauteur' style='cursor: pointer;'>Hauteur minimale des lignes en pixels ("0" si automatique)&nbsp;:</label><br />
        <span class="small">(Si le contenu d'une cellule est tel que la hauteur fixée ci-dessus est insuffisante, la hauteur de la ligne sera dimensionnée par le navigateur lui-même.)</span>
        </td>
        <td><input type="text" name="releve_col_hauteur" id="releve_col_hauteur" size="20" value="<?php
		if(getSettingValue("releve_col_hauteur")!=""){
			echo(getSettingValue("releve_col_hauteur"));
		}
		else{
			echo "0";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_cellpadding' style='cursor: pointer;'>Espace en pixels entre le bord d'une cellule du tableau et le contenu de la cellule&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_cellpadding" id="releve_cellpadding" size="20" value="<?php
		if(getSettingValue("releve_cellpadding")!=""){
			echo(getSettingValue("releve_cellpadding"));
		}
		else{
			echo "3";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_cellspacing' style='cursor: pointer;'>Espace en pixels entre les cellules du tableau&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_cellspacing" id="releve_cellspacing" size="20" value="<?php
		if(getSettingValue("releve_cellspacing")!=""){
			echo(getSettingValue("releve_cellspacing"));
		}
		else{
			echo "1";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,50);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_ecart_entete' style='cursor: pointer;'>Espace (nombre de lignes vides) entre l'en-tête du relevé et le tableau des notes et appréciations&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_ecart_entete" id="releve_ecart_entete" size="20" value="<?php
		if(getSettingValue("releve_ecart_entete")!=""){
			echo(getSettingValue("releve_ecart_entete"));
		}
		else{
			echo "0";
		}
		?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Bordures des cellules du tableau des moyennes et appréciations&nbsp;:
        </td>
        <td>
		<?php
			if(getSettingValue("releve_bordure_classique")=='n'){
				$releve_bordure_classique="n";
			}
			else{
				$releve_bordure_classique="y";
			}

			echo "<input type=\"radio\" name=\"releve_bordure_classique\" id='releve_bordure_classiquey' value=\"y\" ";
			if ($releve_bordure_classique=='y') echo " checked";
			echo " /><label for='releve_bordure_classiquey' style='cursor: pointer;'>&nbsp;classique&nbsp;HTML</label><br />\n";
			echo "<input type=\"radio\" name=\"releve_bordure_classique\" id='releve_bordure_classiquen' value=\"n\" ";
			if ($releve_bordure_classique=='n') echo " checked";
			echo " /><label for='releve_bordure_classiquen' style='cursor: pointer;'>&nbsp;trait&nbsp;noir</label>\n";
		?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_categ_font_size' style='cursor: pointer;'>Taille en points du texte des catégories de matières (<i>lorsqu'elles sont affichées</i>)&nbsp;:</label>
        </td>
	<?php
		if(getSettingValue("releve_categ_font_size")){
			$releve_categ_font_size=getSettingValue("releve_categ_font_size");
		}
		else{
			$releve_categ_font_size=10;
		}
	?>
        <td><input type="text" name="releve_categ_font_size" id="releve_categ_font_size" size="20" value="<?php echo $releve_categ_font_size; ?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_categ_bgcolor' style='cursor: pointer;'>Couleur de fond des lignes de catégories de matières (<i>lorsqu'elles sont affichées</i>)&nbsp;:</label>
        </td>
	<?php
		if(getSettingValue("releve_categ_bgcolor")){
			$releve_categ_bgcolor=getSettingValue("releve_categ_bgcolor");
		}
		else{
			$releve_categ_bgcolor="";
		}
	?>
        <td>
	<?php
		//<input type="text" name="releve_categ_bgcolor" size="20" value="echo $releve_categ_bgcolor;" />
		echo "<select name='releve_categ_bgcolor' id='releve_categ_bgcolor'>\n";
		echo "<option value=''>Aucune</option>\n";
		for($i=0;$i<count($tabcouleur);$i++){
			if($tabcouleur[$i]=="$releve_categ_bgcolor"){
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
        Faire apparaitre le nom de l'établissement sur le relevé&nbsp;:<br />(<i>certains établissements ont le nom dans le Logo</i>)
		<?php
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('parametres_communs_html_et_pdf','y',100,100);\"  onmouseout=\"cacher_div('parametres_communs_html_et_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		?>
        </td>
	<?php
		if(getSettingValue("releve_affich_nom_etab")){
			$releve_affich_nom_etab=getSettingValue("releve_affich_nom_etab");
		}
		else{
			$releve_affich_nom_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='releve_affich_nom_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"releve_affich_nom_etab\" id=\"releve_affich_nom_etab_y\" value=\"y\" ";
        if ($releve_affich_nom_etab == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='releve_affich_nom_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"releve_affich_nom_etab\" id=\"releve_affich_nom_etab_n\" value=\"n\" ";
        if ($releve_affich_nom_etab == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Faire apparaitre l'adresse de l'établissement sur le relevé&nbsp;:<br />(<i>certains établissements ont l'adresse dans le Logo</i>)
		<?php
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('parametres_communs_html_et_pdf','y',100,100);\"  onmouseout=\"cacher_div('parametres_communs_html_et_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		?>
        </td>
	<?php
		if(getSettingValue("releve_affich_adr_etab")){
			$releve_affich_adr_etab=getSettingValue("releve_affich_adr_etab");
		}
		else{
			$releve_affich_adr_etab="y";
		}
	?>
        <td>
	<?php
        echo "<label for='releve_affich_adr_etab_y' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"releve_affich_adr_etab\" id=\"releve_affich_adr_etab_y\" value=\"y\" ";
        if ($releve_affich_adr_etab == 'y') {echo " checked";}
        echo " />&nbsp;Oui</label>\n";
		echo "<br />\n";
        echo "<label for='releve_affich_adr_etab_n' style='cursor: pointer;'>\n";
		echo "<input type=\"radio\" name=\"releve_affich_adr_etab\" id=\"releve_affich_adr_etab_n\" value=\"n\" ";
        if ($releve_affich_adr_etab == 'n') {echo " checked";}
        echo " />&nbsp;Non</label>\n";
        ?>
	</td>
    </tr>

</table>
<hr />


<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>


<hr />


<?php
//Informations devant figurer sur le relevé de notes</H3>
?>
<h3>Informations devant figurer sur le relevé de notes</h3>
<table cellpadding="8" cellspacing="0" width="100%" border="0" summary="Tableau des informations devant figurer sur le relevé de notes">
<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le nom court de la classe&nbsp;:
        </td>
        <!--td style='width:8em; text-align:right;'-->
        <td style='width:8em;'>
        <?php
        echo "<input type=\"radio\" name=\"releve_mention_nom_court\" id=\"releve_mention_nom_courty\" value=\"yes\" ";
        if (getSettingValue("releve_mention_nom_court") == 'yes') echo " checked";
        echo " /><label for='releve_mention_nom_courty' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_mention_nom_court\" id=\"releve_mention_nom_courtn\" value=\"no\" ";
        if (getSettingValue("releve_mention_nom_court") == 'no') echo " checked";
        echo " /><label for='releve_mention_nom_courtn' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher la mention "doublant" ou "doublante", le cas échéant&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_mention_doublant\" id=\"releve_mention_doublanty\" value=\"yes\" ";
        if (getSettingValue("releve_mention_doublant") == 'yes') echo " checked";
        echo " /><label for='releve_mention_doublanty' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_mention_doublant\" id=\"releve_mention_doublantn\" value=\"no\" ";
        if (getSettingValue("releve_mention_doublant") == 'no') echo " checked";
        echo " /><label for='releve_mention_doublantn' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
	<tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher les informations sur l'élève sur une seule ligne <i>(si non une information par ligne)</i>&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_eleve_une_ligne\" id=\"releve_affiche_eleve_une_ligney\" value=\"yes\" ";
        if (getSettingValue("releve_affiche_eleve_une_ligne") == 'yes') echo " checked";
        echo " /><label for='releve_affiche_eleve_une_ligney' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_eleve_une_ligne\" id=\"releve_affiche_eleve_une_lignen\" value=\"no\" ";
        if (getSettingValue("releve_affiche_eleve_une_ligne") == 'no') echo " checked";
        echo " /><label for='releve_affiche_eleve_une_lignen' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro du relevé&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_numero\" id=\"releve_affiche_numeroy\" value=\"yes\" ";
        if (getSettingValue("releve_affiche_numero") == 'yes') echo " checked";
        echo " /><label for='releve_affiche_numeroy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_numero\" id=\"releve_affiche_numeron\" value=\"no\" ";
        if (getSettingValue("releve_affiche_numero") == 'no') echo " checked";
        echo " /><label for='releve_affiche_numeron' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le nom du professeur principal et du chef d'établissement&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_signature\" id=\"releve_affiche_signaturey\" value=\"y\" ";
        if (getSettingValue("releve_affiche_signature") == 'y') echo " checked";
        echo " /><label for='releve_affiche_signaturey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_signature\" id=\"releve_affiche_signaturen\" value=\"n\" ";
        if (getSettingValue("releve_affiche_signature") != 'y') echo " checked";
        echo " /><label for='releve_affiche_signaturen' style='cursor: pointer;'>&nbsp;Non</label>";
        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher l'établissement d'origine sur le relevé&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_etab\" id=\"releve_affiche_etaby\" value=\"y\" ";
        if (getSettingValue("releve_affiche_etab") == 'y') echo " checked";
        echo " /><label for='releve_affiche_etaby' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_etab\" id=\"releve_affiche_etabn\" value=\"n\" ";
        if (getSettingValue("releve_affiche_etab") != 'y') echo " checked";
        echo " /><label for='releve_affiche_etabn' style='cursor: pointer;'>&nbsp;Non</label>";
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
        Afficher la photo de l'élève sur le relevé&nbsp;:
        </td>
        <td>
<?php
	echo "<input type='radio' name='activer_photo_releve' id='activer_photo_relevey' value='y'";
	if (getSettingValue("activer_photo_releve")=='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('afficher')\" /><label for='activer_photo_relevey' style='cursor: pointer;'>&nbsp;Oui</label>\n";
	echo "<input type='radio' name='activer_photo_releve' id='activer_photo_releven' value='n'";
	if (getSettingValue("activer_photo_releve")!='y'){echo "checked";}
	echo " onchange=\"aff_lig_photo('cacher')\" /><label for='activer_photo_releven' style='cursor: pointer;'>&nbsp;Non</label>\n";
?>
        </td>
    </tr>
<?php
	if(getSettingValue("releve_photo_hauteur_max")){
		$releve_photo_hauteur_max=getSettingValue("releve_photo_hauteur_max");
	}
	else{
		$releve_photo_hauteur_max=80;
	}

	if(getSettingValue("releve_photo_largeur_max")){
		$releve_photo_largeur_max=getSettingValue("releve_photo_largeur_max");
	}
	else{
		$releve_photo_largeur_max=80;
	}
?>
    <tr id='ligne_releve_photo_hauteur_max'>
	<td style="font-variant: small-caps;"><label for='releve_photo_hauteur_max' style='cursor: pointer;'>Hauteur maximale de la photo en pixels&nbsp;:</label></td>
	<td><input type="text" name="releve_photo_hauteur_max" id="releve_photo_hauteur_max" size='4' value="<?php echo $releve_photo_hauteur_max;?>" /></td>
    </tr>
    <tr id='ligne_releve_photo_largeur_max'>
	<td style="font-variant: small-caps;"><label for='releve_photo_largeur_max' style='cursor: pointer;'>Largeur maximale de la photo en pixels&nbsp;:</label></td>
	<td><input type="text" name="releve_photo_largeur_max" id="releve_photo_largeur_max" size='4' value="<?php echo $releve_photo_largeur_max;?>" />

	<script type='text/javascript'>
		function aff_lig_photo(mode){
			if(mode=='afficher'){
				document.getElementById('ligne_releve_photo_hauteur_max').style.display='';
				document.getElementById('ligne_releve_photo_largeur_max').style.display='';
			}
			else{
				document.getElementById('ligne_releve_photo_hauteur_max').style.display='none';
				document.getElementById('ligne_releve_photo_largeur_max').style.display='none';
			}
		}

		if(document.getElementById('activer_photo_relevey').checked==false){
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
        Afficher le numéro de téléphone de l'établissement&nbsp;:
		<?php
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('parametres_communs_html_et_pdf','y',100,100);\"  onmouseout=\"cacher_div('parametres_communs_html_et_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		?>
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_tel\" id=\"releve_affiche_tely\" value=\"y\" ";
        if (getSettingValue("releve_affiche_tel") == 'y') echo " checked";
        echo " /><label for='releve_affiche_tely' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_tel\" id=\"releve_affiche_teln\" value=\"n\" ";
        if (getSettingValue("releve_affiche_tel") != 'y') echo " checked";
        echo " /><label for='releve_affiche_teln' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro de fax de l'établissement&nbsp;:
		<?php
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('parametres_communs_html_et_pdf','y',100,100);\"  onmouseout=\"cacher_div('parametres_communs_html_et_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		?>
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_fax\" id=\"releve_affiche_faxy\" value=\"y\" ";
        if (getSettingValue("releve_affiche_fax") == 'y') echo " checked";
        echo " /><label for='releve_affiche_faxy' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_fax\" id=\"releve_affiche_faxn\" value=\"n\" ";
        if (getSettingValue("releve_affiche_fax") != 'y') echo " checked";
        echo " /><label for='releve_affiche_faxn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher l'adresse email de l'établissement&nbsp;:
		<?php
			echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('parametres_communs_html_et_pdf','y',100,100);\"  onmouseout=\"cacher_div('parametres_communs_html_et_pdf');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		?>
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_mail\" id=\"releve_affiche_maily\" value=\"y\" ";
        if (getSettingValue("releve_affiche_mail") == 'y') echo " checked";
        echo " /><label for='releve_affiche_maily' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_mail\" id=\"releve_affiche_mailn\" value=\"n\" ";
        if (getSettingValue("releve_affiche_mail") != 'y') echo " checked";
        echo " /><label for='releve_affiche_mailn' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher le numéro INE de l'élève&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_INE_eleve\" id=\"releve_affiche_INE_elevey\" value=\"y\" ";
        if (getSettingValue("releve_affiche_INE_eleve") == 'y') echo " checked";
        echo " /><label for='releve_affiche_INE_elevey' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_INE_eleve\" id=\"releve_affiche_INE_eleven\" value=\"n\" ";
        if (getSettingValue("releve_affiche_INE_eleve") != 'y') echo " checked";
        echo " /><label for='releve_affiche_INE_eleven' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>


    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        Afficher la formule figurant en bas de chaque relevé de notes&nbsp;:
        </td>
        <td>
        <?php
        echo "<input type=\"radio\" name=\"releve_affiche_formule\" id=\"releve_affiche_formuley\" value=\"y\" ";
        if (getSettingValue("releve_affiche_formule") == 'y') echo " checked";
        echo " /><label for='releve_affiche_formuley' style='cursor: pointer;'>&nbsp;Oui</label>";
        echo "<input type=\"radio\" name=\"releve_affiche_formule\" id=\"releve_affiche_formulen\" value=\"n\" ";
        if (getSettingValue("releve_affiche_formule") != 'y') echo " checked";
        echo " /><label for='releve_affiche_formulen' style='cursor: pointer;'>&nbsp;Non</label>";

        ?>
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;" colspan="2">
        <label for='no_anti_inject_releve_formule_bas' style='cursor: pointer;'>Formule figurant en bas de chaque relevé de notes:</label>
        <input type="text" name="no_anti_inject_releve_formule_bas" id="no_anti_inject_releve_formule_bas" size="100" value="<?php echo(getSettingValue("releve_formule_bas")); ?>" />
        </td>
    </tr>


	<?php
	/*
    echo "<tr";
	if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++;
	echo ">\n";
    echo "<td style='font-variant: small-caps;' colspan='2'>\n";
	echo "<label for='no_anti_inject_releve_formule_bas' style='cursor: pointer;'>Formule figurant en bas de chaque relevé&nbsp;:</label>\n";
	echo "<input type='text' name='no_anti_inject_releve_formule_bas' id='no_anti_inject_releve_formule_bas' size='100' value=\"".getSettingValue("releve_formule_bas")."\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	*/
	?>

</table>
<hr />



<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>



<hr />
<H3>Bloc adresse</H3>
<center><table border="1" cellpadding="10" width="90%" summary="Tableau des paramètres bloc adresse"><tr><td>
Ces options contrôlent le positionnement du bloc adresse du responsable de l'élève directement sur le relevé (et non sur la page de garde - voir ci-dessous). L'affichage de ce bloc est contrôlé classe par classe, au niveau du paramétrage de la classe.
</td></tr></table></center>

<table cellpadding="8" cellspacing="0" width="100%" border="0" summary="Paramètres du bloc adresse">

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;;$nb_ligne++;?>>
        <td colspan='2' style="font-variant: small-caps;">
	<a href="javascript:SetDefaultValues('Adresse')">Rétablir les paramètres par défaut</a>
        </td>
     </tr>


    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_padding_right' style='cursor: pointer;'>Espace en mm entre la marge droite de la feuille et le bloc "adresse"&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_addressblock_padding_right" id="releve_addressblock_padding_right" size="20" value="<?php
		if(!getSettingValue("releve_addressblock_padding_right")){
			$releve_addressblock_padding_right=0;
		}
		else{
			$releve_addressblock_padding_right=getSettingValue("releve_addressblock_padding_right");
		}
		echo $releve_addressblock_padding_right;
		?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge droite d'impression pour calculer l'espace entre le bord droit de la feuille et le bloc adresse</i></td>
     </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_padding_top' style='cursor: pointer;'>Espace en mm entre la marge haute de la feuille et le bloc "adresse"&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_addressblock_padding_top" id="releve_addressblock_padding_top" size="20" value="<?php

		if(!getSettingValue("releve_addressblock_padding_top")){
			$releve_addressblock_padding_top=0;
		}
		else{
			$releve_addressblock_padding_top=getSettingValue("releve_addressblock_padding_top");
		}

		echo $releve_addressblock_padding_top;

		?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td colspan="2"><i>Tenez compte de la marge haute d'impression pour calculer l'espace entre le bord haut de la feuille et le bloc adresse</i></td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_padding_text' style='cursor: pointer;'>Espace vertical en mm entre le bloc "adresse" et le bloc des résultats&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_addressblock_padding_text" id="releve_addressblock_padding_text" size="20" value="<?php

		if(!getSettingValue("releve_addressblock_padding_text")){
			$releve_addressblock_padding_text=0;
		}
		else{
			$releve_addressblock_padding_text=getSettingValue("releve_addressblock_padding_text");
		}

		echo $releve_addressblock_padding_text;

		?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_length' style='cursor: pointer;'>Longueur en mm du bloc "adresse"&nbsp;:</label>
        </td>
        <td><input type="text" name="releve_addressblock_length" id="releve_addressblock_length" size="20" value="<?php

		if(!getSettingValue("releve_addressblock_length")){
			$releve_addressblock_length=60;
		}
		else{
			$releve_addressblock_length=getSettingValue("releve_addressblock_length");
		}

		echo $releve_addressblock_length;

		?>" onKeyDown="clavier_2(this.id,event,0,150);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_font_size' style='cursor: pointer;'>Taille en points des textes du bloc "adresse"&nbsp;:</label>
        </td>
	<?php
		if(!getSettingValue("releve_addressblock_font_size")){
			$releve_addressblock_font_size=12;
		}
		else{
			$releve_addressblock_font_size=getSettingValue("releve_addressblock_font_size");
		}
	?>
        <td><input type="text" name="releve_addressblock_font_size" id="releve_addressblock_font_size" size="20" value="<?php
		echo $releve_addressblock_font_size;
		?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_logo_etab_prop' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au logo et à l'adresse de l'établissement&nbsp;:</label>
        </td>
	<?php
		if(!getSettingValue("releve_addressblock_logo_etab_prop")){
			$releve_addressblock_logo_etab_prop=50;
		}
		else{
			$releve_addressblock_logo_etab_prop=getSettingValue("releve_addressblock_logo_etab_prop");
		}
	?>
        <td><input type="text" name="releve_addressblock_logo_etab_prop" id="releve_addressblock_logo_etab_prop" size="20" value="<?php
		echo $releve_addressblock_logo_etab_prop;
		?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_addressblock_classe_annee' style='cursor: pointer;'>Proportion (en % de la largeur de page) allouée au bloc "Classe, année, période"&nbsp;:</label>
        </td>
	<?php
		if(!getSettingValue("releve_addressblock_classe_annee")){
			$releve_addressblock_classe_annee=35;
		}
		else{
			$releve_addressblock_classe_annee=getSettingValue("releve_addressblock_classe_annee");
		}
	?>
        <td><input type="text" name="releve_addressblock_classe_annee" id="releve_addressblock_classe_annee" size="20" value="<?php
		echo $releve_addressblock_classe_annee;
		?>" onKeyDown="clavier_2(this.id,event,0,100);" />
        </td>
    </tr>

    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <label for='releve_ecart_bloc_nom' style='cursor: pointer;'>Nombre de sauts de ligne entre le bloc Logo+Etablissement et le bloc Nom, prénom,... de l'élève&nbsp;:</label>
        </td>
	<?php
		if(!getSettingValue("releve_ecart_bloc_nom")){
			$releve_ecart_bloc_nom=0;
		}
		else{
			$releve_ecart_bloc_nom=getSettingValue("releve_ecart_bloc_nom");
		}
	?>
        <td><input type="text" name="releve_ecart_bloc_nom" id="releve_ecart_bloc_nom" size="20" value="<?php
		echo $releve_ecart_bloc_nom;
		?>" onKeyDown="clavier_2(this.id,event,0,20);" />
        </td>
    </tr>
    <tr <?php if ($nb_ligne % 2) echo "bgcolor=".$bgcolor;$nb_ligne++; ?>>
        <td style="font-variant: small-caps;">
        <font color='red'>Activer l'affichage des bordures pour comprendre la présentation avec bloc "adresse"</font>&nbsp;:<br />
		<span style='font-size:x-small;'>Il faut ajuster les paramètres des champs '<i>Espace en mm entre la marge haute de la feuille et le bloc "adresse"</i>', '<i>Longueur en mm du bloc "adresse"</i>' et '<i>Proportion (en % de la largeur de page) allouée au logo et à l'adresse de l'établissement</i>' pour que les cadres bleu et vert n'entrent pas en collision (<i>vous pouvez modifier la taille de la fenêtre du navigateur à l'aide de la souris pour comprendre les éventuelles abérrations de présentation pour certaines combinaisons de valeurs</i>).</span>
        </td>
	<?php
		if(!getSettingValue("releve_addressblock_debug")){
			$releve_addressblock_debug="n";
		}
		else{
			$releve_addressblock_debug=getSettingValue("releve_addressblock_debug");
		}
	?>
        <td valign='top'><input type="radio" id="releve_addressblock_debugy" name="releve_addressblock_debug" value="y" <?php if($releve_addressblock_debug=="y"){echo "checked";}?> /><label for='releve_addressblock_debugy' style='cursor: pointer;'> Oui</label> <input type="radio" id="releve_addressblock_debugn" name="releve_addressblock_debug" value="n" <?php if($releve_addressblock_debug=="n"){echo "checked";}?> /><label for='releve_addressblock_debugn' style='cursor: pointer;'> Non</label>
        </td>
    </tr>
</table>
<hr />

<center><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php
	require("../lib/footer.inc.php");
?>
