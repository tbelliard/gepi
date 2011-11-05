<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*/

// Initialisation des valeurs
$largeur_page=210;
$hauteur_page=297;

$MargeHaut=10;
$MargeDroite=10;
$MargeGauche=10;
$MargeBas=10;

$largeur_utile_page=$largeur_page-$MargeDroite-$MargeGauche;

$x0=$MargeGauche;
$y0=$MargeHaut;

$trombino_pdf_nb_col=getSettingValue("trombino_pdf_nb_col");
if($trombino_pdf_nb_col=="") {$trombino_pdf_nb_col=4;}

$trombino_pdf_nb_lig=getSettingValue("trombino_pdf_nb_lig");
if($trombino_pdf_nb_lig=="") {$trombino_pdf_nb_lig=5;}

// Espace entre deux photos
$dx=2;
$dy=2;

// Hauteur classe
$hauteur_classe=10;
// Ecart sous la classe
$ecart_sous_classe=2;

// Espace pour Nom et prénom dans le cadre
$hauteur_info_eleve=5;

// Pour pouvoir ne pas imprimer le Footer
$no_footer="n";

// Il arrive qu'il y ait un décalage vertical s'amplifiant ligne après ligne sur les découpes
// Par défaut, pas de décalage:
$correctif_vertical=1;
// 1=100%
//===================
// Valeurs calculées:

// Nombre de cases par page
$nb_cell=$trombino_pdf_nb_lig*$trombino_pdf_nb_col;

// Hauteur d'un cadre
$haut_cadre=Floor($hauteur_page-$MargeHaut-$MargeBas-$hauteur_classe-$ecart_sous_classe-($trombino_pdf_nb_lig-1)*$dy)/$trombino_pdf_nb_lig;

// Largeur d'un cadre
$larg_cadre=Floor($largeur_page-$MargeDroite-$MargeGauche-($trombino_pdf_nb_col-1)*$dx)/$trombino_pdf_nb_col;

?>