<?php
/**
 * Portion des styles et initialisations à reprendre dans le cas d'une insertion des relevés de notes entre les bulletins
 * 
 * @license GNU/GPL 
 * @package Carnet_de_notes
 * @subpackage affichage
 */


	$p_releve_margin=getSettingValue("p_releve_margin") ? getSettingValue("p_releve_margin") : "";
	$releve_textsize=getSettingValue("releve_textsize") ? getSettingValue("releve_textsize") : 10;
	$releve_titlesize=getSettingValue("releve_titlesize") ? getSettingValue("releve_titlesize") : 16;


	$style_releve_notes_html="<style type='text/css'>
.releve_grand {
	color: #000000;
	font-size: ".$releve_titlesize."pt;
	font-style: normal;
}

.releve {
	color: #000000;
	font-size: ".$releve_textsize."pt;
	font-style: normal;\n";
	if($p_releve_margin!=""){
		$style_releve_notes_html.="      margin-top: ".$p_releve_margin."pt;\n";
		$style_releve_notes_html.="      margin-bottom: ".$p_releve_margin."pt;\n";
	}
	$style_releve_notes_html.="}\n";

	$style_releve_notes_html.="td.releve_empty{
	width:auto;
	padding-right: 20%;
}

.boireaus td {
	text-align:left;
}\n";

	// Récupération des variables du bloc adresses:
	// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
	// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
	// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
	$releve_addressblock_logo_etab_prop=getSettingValue("releve_addressblock_logo_etab_prop") ? getSettingValue("releve_addressblock_logo_etab_prop") : 40;
	$releve_addressblock_autre_prop=100-$releve_addressblock_logo_etab_prop;

	// Taille des polices sur le bloc adresse:
	$releve_addressblock_font_size=getSettingValue("releve_addressblock_font_size") ? getSettingValue("releve_addressblock_font_size") : 12;

	// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
	$releve_addressblock_classe_annee=getSettingValue("releve_addressblock_classe_annee") ? getSettingValue("releve_addressblock_classe_annee") : 35;
	// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
	$releve_addressblock_classe_annee2=round(100*$releve_addressblock_classe_annee/(100-$releve_addressblock_logo_etab_prop));

	// Débug sur l'entête pour afficher les cadres
	$releve_addressblock_debug=getSettingValue("releve_addressblock_debug") ? getSettingValue("releve_addressblock_debug") : "n";

	// Nombre de sauts de lignes entre le tableau logo+etab et le nom, prénom,... de l'élève
	$releve_ecart_bloc_nom=getSettingValue("releve_ecart_bloc_nom") ? getSettingValue("releve_ecart_bloc_nom") : 0;

	// Afficher l'établissement d'origine de l'élève:
	$releve_affiche_etab=getSettingValue("releve_affiche_etab") ? getSettingValue("releve_affiche_etab") : "n";

	// Bordure classique ou trait-noir:
	$releve_bordure_classique=getSettingValue("releve_bordure_classique") ? getSettingValue("releve_bordure_classique") : "y";
	if($releve_bordure_classique!="y"){
		$releve_class_bordure=" class='uneligne' ";
	}
	else{
		$releve_class_bordure="";
	}

	$releve_addressblock_length=getSettingValue("releve_addressblock_length") ? getSettingValue("releve_addressblock_length") : 60;
	$releve_addressblock_padding_top=getSettingValue("releve_addressblock_padding_top") ? getSettingValue("releve_addressblock_padding_top") : 20;
	$releve_addressblock_padding_text=getSettingValue("releve_addressblock_padding_text") ? getSettingValue("releve_addressblock_padding_text") : 0;
	$releve_addressblock_padding_right=getSettingValue("releve_addressblock_padding_right") ? getSettingValue("releve_addressblock_padding_right") : 0;



	// Affichage ou non du nom et de l'adresse de l'établissement
	$releve_affich_nom_etab=getSettingValue("releve_affich_nom_etab") ? getSettingValue("releve_affich_nom_etab") : "y";
	$releve_affich_adr_etab=getSettingValue("releve_affich_adr_etab") ? getSettingValue("releve_affich_adr_etab") : "y";
	if(($releve_affich_nom_etab!="n")&&($releve_affich_nom_etab!="y")) {$releve_affich_nom_etab="y";}
	if(($releve_affich_adr_etab!="n")&&($releve_affich_adr_etab!="y")) {$releve_affich_adr_etab="y";}

	$releve_ecart_entete=getSettingValue("releve_ecart_entete") ? getSettingValue("releve_ecart_entete") : 0;


	$releve_mention_doublant=getSettingValue("releve_mention_doublant") ? getSettingValue("releve_mention_doublant") : "n";


	$releve_cellspacing=getSettingValue("releve_cellspacing") ? getSettingValue("releve_cellspacing") : 2;
	$releve_cellpadding=getSettingValue("releve_cellpadding") ? getSettingValue("releve_cellpadding") : 5;


	$releve_affiche_numero=getSettingValue("releve_affiche_numero") ? getSettingValue("releve_affiche_numero") : "n";


	$releve_affiche_signature=getSettingValue("releve_affiche_signature") ? getSettingValue("releve_affiche_signature") : "y";

	$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "n";
	$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "Relevé à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.";


	$releve_col_hauteur=getSettingValue("releve_col_hauteur") ? getSettingValue("releve_col_hauteur") : 0;
	$releve_largeurtableau=getSettingValue("releve_largeurtableau") ? getSettingValue("releve_largeurtableau") : 800;
	$releve_col_matiere_largeur=getSettingValue("releve_col_matiere_largeur") ? getSettingValue("releve_col_matiere_largeur") : 150;

	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi") ? getSettingValue("gepi_prof_suivi") : "professeur principal";

	$releve_affiche_eleve_une_ligne=getSettingValue("releve_affiche_eleve_une_ligne") ? getSettingValue("releve_affiche_eleve_une_ligne") : "n";
	$releve_mention_nom_court=getSettingValue("releve_mention_nom_court") ? getSettingValue("releve_mention_nom_court") : "y";

	$releve_photo_largeur_max=getSettingValue("releve_photo_largeur_max") ? getSettingValue("releve_photo_largeur_max") : 100;
	$releve_photo_hauteur_max=getSettingValue("releve_photo_hauteur_max") ? getSettingValue("releve_photo_hauteur_max") : 100;

	$releve_categ_font_size=getSettingValue("releve_categ_font_size") ? getSettingValue("releve_categ_font_size") : 10;
	$releve_categ_bgcolor=getSettingValue("releve_categ_bgcolor") ? getSettingValue("releve_categ_bgcolor") : "";

	$releve_affiche_tel=getSettingValue("releve_affiche_tel") ? getSettingValue("releve_affiche_tel") : "n";
	$releve_affiche_fax=getSettingValue("releve_affiche_fax") ? getSettingValue("releve_affiche_fax") : "n";

	//if($releve_affiche_fax=="y"){
		$gepiSchoolFax=getSettingValue("gepiSchoolFax");
	//}

	//if($releve_affiche_tel=="y"){
		$gepiSchoolTel=getSettingValue("gepiSchoolTel");
	//}

	$releve_affiche_mail=getSettingValue("releve_affiche_mail") ? getSettingValue("releve_affiche_mail") : "n";
	$gepiSchoolEmail=getSettingValue('gepiSchoolEmail');

	$releve_affiche_INE_eleve=getSettingValue("releve_affiche_INE_eleve") ? getSettingValue("releve_affiche_INE_eleve") : "n";

	$genre_periode=getSettingValue("genre_periode") ? getSettingValue("genre_periode") : "M";

	$activer_photo_releve=getSettingValue("activer_photo_releve") ? getSettingValue("activer_photo_releve") : "n";
	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes") ? getSettingValue("active_module_trombinoscopes") : "n";


	$style_releve_notes_html.="</style>\n";

	$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "y";
	$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "";
?>