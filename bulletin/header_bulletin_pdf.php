<?php

	header('Content-Type: application/pdf');

	// Global configuration file
	// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
	//Le problème peut être résolu en ajoutant la ligne suivante :
	Header('Pragma: public');

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

	require('../fpdf/fpdf.php');
	require('../fpdf/ex_fpdf.php');
	require_once("../fpdf/class.multicelltag.php");

	// Fichier d'extension de fpdf pour le bulletin
	require_once("../class_php/gepi_pdf.class.php");

	// Fonctions php des bulletins pdf
	require_once("bulletin_fonctions.php");
	// Ensemble des données communes
	require_once("bulletin_donnees.php");

	define('FPDF_FONTPATH','../fpdf/font/');
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');
	session_cache_limiter('private');

	$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
	$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
	$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;

	//variables invariables
	$annee_scolaire = $gepiYear;
	$date_bulletin = date("d/m/Y H:i");
	$nom_bulletin = date("Ymd_Hi");

/*
	//=========================================
	//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
	$pdf=new bul_PDF('p', 'mm', 'A4');
	$nb_eleve_aff = 1;
	$categorie_passe = '';
	$categorie_passe_count = 0;
	$pdf->SetCreator($gepiSchoolName);
	$pdf->SetAuthor($gepiSchoolName);
	$pdf->SetKeywords('');
	$pdf->SetSubject('Bulletin');
	$pdf->SetTitle('Bulletin');
	$pdf->SetDisplayMode('fullwidth', 'single');
	$pdf->SetCompression(TRUE);
	$pdf->SetAutoPageBreak(TRUE, 5);

	$pdf->AddPage(); //ajout d'une page au document
	$pdf->SetFont('Arial');

	if ( !isset($X_etab) or empty($X_etab) ) {
		$X_etab = '5';
		$Y_etab = '5';
	}
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont('Arial','',14);
	$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
	$pdf->Cell(90,7, $gepiSchoolName,0,2,'');


	//fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'
	$nom_bulletin = 'bulletin_'.$nom_bulletin.'.pdf';
	$pdf->Output($nom_bulletin,'I');
	die();
	//=========================================
*/

	$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
	$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
	$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
	$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
	$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
	$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
	$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";

	$gepiYear=getSettingValue("gepiYear") ? getSettingValue("gepiYear") : ((strftime("%m")>7) ? ((strftime("%Y")-1)."-".strftime("%Y")) : (strftime("%Y")."-".strftime("%Y")+1));

	$logo_etab=getSettingValue("logo_etab") ? getSettingValue("logo_etab") : "";

	/*

	$bull_body_marginleft=getSettingValue("bull_body_marginleft") ? getSettingValue("bull_body_marginleft") : 1;

	$p_bulletin_margin=getSettingValue("p_bulletin_margin") ? getSettingValue("p_bulletin_margin") : "";
	$textsize=getSettingValue("textsize") ? getSettingValue("textsize") : 10;
	$titlesize=getSettingValue("titlesize") ? getSettingValue("titlesize") : 16;


	echo "<html>
<head>
<meta HTTP-EQUIV='Content-Type' content='text/html; charset=iso-8859-1' />
<META HTTP-EQUIV='Pragma' CONTENT='no-cache' />
<META HTTP-EQUIV='Cache-Control' CONTENT='no-cache' />
<META HTTP-EQUIV='Expires' CONTENT='0' />
<title>".$gepiSchoolName." : Bulletin | Edition des bulletins</title>
<link rel='stylesheet' type='text/css' href='../style.css' />
<style type='text/css'>
   body {
      margin-left: ".$bull_body_marginleft."px;
   }

   .bgrand {
      color: #000000;
      font-size: ".$titlesize."pt;
      font-style: normal;
   }

   .bulletin {
      color: #000000;
      font-size: ".$textsize."pt;
      font-style: normal;\n";

	if($p_bulletin_margin!=""){
		echo "      margin-top: ".$p_bulletin_margin."pt;\n";
		echo "      margin-bottom: ".$p_bulletin_margin."pt;\n";
	}
	echo "   }\n";

	$textminclasmax=$textsize-2;
	echo "
   .bullminclasmax{
      color: #000000;
      font-size: ".$textminclasmax."pt;
      font-style: normal;\n";
	if($p_bulletin_margin!=""){
		echo "      margin-top: ".$p_bulletin_margin."pt;\n";
		echo "      margin-bottom: ".$p_bulletin_margin."pt;\n";
	}
	echo "   }\n";

	//$tab_styles_avis=Array("Normal","Gras","Italique","Gras et Italique");

	$bull_categ_font_size_avis=getSettingValue("bull_categ_font_size_avis") ? getSettingValue("bull_categ_font_size_avis") : 10;
	$bull_police_avis=getSettingValue("bull_police_avis") ? getSettingValue("bull_police_avis") : "";
	$bull_font_style_avis=getSettingValue("bull_font_style_avis") ? getSettingValue("bull_font_style_avis") : "Normal";
	echo "
   .avis_bulletin {
      color: #000000;
      font-size: ".$bull_categ_font_size_avis."pt;\n";

	if($bull_police_avis!="") {
		echo "      font-family:'".$bull_police_avis."';\n";
	}

	switch ($bull_font_style_avis) {
	case "Normal":
		echo "      font-style: normal;\n";
		break;
	case "Gras":
		echo "      font-weight:bold;\n";
		break;
	case "Italique":
		echo "      font-style: italic;\n";
		break;
	case "Gras et Italique":
		echo "      font-style: italic;\n";
		echo "      font-weight: bold;\n";
		break;
	default :
		echo "      font-style: normal;";
	}
	echo "   }

   @media print  {
      .noprint{
         display: none;
      }
   }

   td.adresse{
      font-size: 1em;
      color: black;
      width:".getSettingValue("addressblock_length")."mm;
      padding-top:".getSettingValue("addressblock_padding_top")."mm;
      padding-bottom:".getSettingValue("addressblock_padding_text")."mm;
      padding-right:".getSettingValue("addressblock_padding_right")."mm;
      text-align:left;
   }

   td.empty{
      width:auto;
      padding-right: 20%;
}\n";

	// Récupération des variables du bloc adresses:
	// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
	// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
	// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
	$largeur1=getSettingValue("addressblock_logo_etab_prop") ? getSettingValue("addressblock_logo_etab_prop") : 40;
	$largeur2=100-$largeur1;

	// Taille des polices sur le bloc adresse:
	$addressblock_font_size=getSettingValue("addressblock_font_size") ? getSettingValue("addressblock_font_size") : 12;

	// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
	$addressblock_classe_annee=getSettingValue("addressblock_classe_annee") ? getSettingValue("addressblock_classe_annee") : 35;
	// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
	$addressblock_classe_annee2=round(100*$addressblock_classe_annee/(100-$largeur1));

	// Débug sur l'entête pour afficher les cadres
	$addressblock_debug=getSettingValue("addressblock_debug") ? getSettingValue("addressblock_debug") : "n";

	// Nombre de sauts de lignes entre le tableau logo+etab et le nom, prénom,... de l'élève
	$bull_ecart_bloc_nom=getSettingValue("bull_ecart_bloc_nom") ? getSettingValue("bull_ecart_bloc_nom") : 0;

	// Afficher l'établissement d'origine de l'élève:
	$bull_affiche_etab=getSettingValue("bull_affiche_etab") ? getSettingValue("bull_affiche_etab") : "n";

	// Bordure classique ou trait-noir:
	$bull_bordure_classique=getSettingValue("bull_bordure_classique") ? getSettingValue("bull_bordure_classique") : "y";
	if($bull_bordure_classique!="y"){
		$class_bordure=" class='uneligne' ";
	}
	else{
		$class_bordure="";
	}

	$addressblock_length=getSettingValue("addressblock_length") ? getSettingValue("addressblock_length") : 6;
	$addressblock_padding_top=getSettingValue("addressblock_padding_top") ? getSettingValue("addressblock_padding_top") : 0;
	$addressblock_padding_text=getSettingValue("addressblock_padding_text") ? getSettingValue("addressblock_padding_text") : 0;
	$addressblock_padding_right=getSettingValue("addressblock_padding_right") ? getSettingValue("addressblock_padding_right") : 0;

	*/


	// Affichage ou non du nom et de l'adresse de l'établissement
	$bull_affich_nom_etab=getSettingValue("bull_affich_nom_etab") ? getSettingValue("bull_affich_nom_etab") : "y";
	$bull_affich_adr_etab=getSettingValue("bull_affich_adr_etab") ? getSettingValue("bull_affich_adr_etab") : "y";
	if(($bull_affich_nom_etab!="n")&&($bull_affich_nom_etab!="y")) {$bull_affich_nom_etab="y";}
	if(($bull_affich_adr_etab!="n")&&($bull_affich_adr_etab!="y")) {$bull_affich_adr_etab="y";}

	$bull_ecart_entete=getSettingValue("bull_ecart_entete") ? getSettingValue("bull_ecart_entete") : 0;

	$page_garde_imprime=getSettingValue("page_garde_imprime") ? getSettingValue("page_garde_imprime") : "n";
    $affiche_page_garde = $page_garde_imprime;


	$bull_mention_doublant=getSettingValue("bull_mention_doublant") ? getSettingValue("bull_mention_doublant") : "n";


	$cellspacing=getSettingValue("cellspacing") ? getSettingValue("cellspacing") : 2;
	$cellpadding=getSettingValue("cellpadding") ? getSettingValue("cellpadding") : 5;


	$bull_affiche_numero=getSettingValue("bull_affiche_numero") ? getSettingValue("bull_affiche_numero") : "n";


	$bull_affiche_avis=getSettingValue("bull_affiche_avis") ? getSettingValue("bull_affiche_avis") : "y";
	$bull_affiche_signature=getSettingValue("bull_affiche_signature") ? getSettingValue("bull_affiche_signature") : "y";
	$bull_affiche_appreciations=getSettingValue("bull_affiche_appreciations") ? getSettingValue("bull_affiche_appreciations") : "y";

	$bull_affiche_formule=getSettingValue("bull_affiche_formule") ? getSettingValue("bull_affiche_formule") : "n";
	$bull_formule_bas=getSettingValue("bull_formule_bas") ? getSettingValue("bull_formule_bas") : "Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.";

	$bull_affiche_absences=getSettingValue("bull_affiche_absences") ? getSettingValue("bull_affiche_absences") : "y";
	$bull_affiche_aid=getSettingValue("bull_affiche_aid") ? getSettingValue("bull_affiche_aid") : "y";

	$col_hauteur=getSettingValue("col_hauteur") ? getSettingValue("col_hauteur") : 0;
	$col_note_largeur=getSettingValue("col_note_largeur") ? getSettingValue("col_note_largeur") : 50;
	$largeurtableau=getSettingValue("largeurtableau") ? getSettingValue("largeurtableau") : 800;

	$gepi_prof_suivi=getSettingValue("gepi_prof_suivi") ? getSettingValue("gepi_prof_suivi") : "professeur principal";

	$bull_espace_avis=getSettingValue("bull_espace_avis") ? getSettingValue("bull_espace_avis") : 1;

	$bull_affiche_eleve_une_ligne=getSettingValue("bull_affiche_eleve_une_ligne") ? getSettingValue("bull_affiche_eleve_une_ligne") : "n";
	$bull_mention_nom_court=getSettingValue("bull_mention_nom_court") ? getSettingValue("bull_mention_nom_court") : "y";

	$bull_photo_largeur_max=getSettingValue("bull_photo_largeur_max") ? getSettingValue("bull_photo_largeur_max") : 100;
	$bull_photo_hauteur_max=getSettingValue("bull_photo_hauteur_max") ? getSettingValue("bull_photo_hauteur_max") : 100;

	$bull_categ_font_size=getSettingValue("bull_categ_font_size") ? getSettingValue("bull_categ_font_size") : 10;
	$bull_categ_bgcolor=getSettingValue("bull_categ_bgcolor") ? getSettingValue("bull_categ_bgcolor") : "";

	$bull_intitule_app=getSettingValue("bull_intitule_app") ? getSettingValue("bull_intitule_app") : "Appréciations/Conseils";

	$bull_affiche_tel=getSettingValue("bull_affiche_tel") ? getSettingValue("bull_affiche_tel") : "n";
	$bull_affiche_fax=getSettingValue("bull_affiche_fax") ? getSettingValue("bull_affiche_fax") : "n";

	if($bull_affiche_fax=="y"){
		$gepiSchoolFax=getSettingValue("gepiSchoolFax");
	}

	if($bull_affiche_tel=="y"){
		$gepiSchoolTel=getSettingValue("gepiSchoolTel");
	}

	$bull_affiche_INE_eleve=getSettingValue("bull_affiche_INE_eleve") ? getSettingValue("bull_affiche_INE_eleve") : "n";

	$genre_periode=getSettingValue("genre_periode") ? getSettingValue("genre_periode") : "M";

	$activer_photo_bulletin=getSettingValue("activer_photo_bulletin") ? getSettingValue("activer_photo_bulletin") : "n";
	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes") ? getSettingValue("active_module_trombinoscopes") : "n";


	$option_affichage_bulletin=getSettingValue("choix_bulletin") ? getSettingValue("choix_bulletin") : 2;

	/*
	switch ($option_affichage_bulletin) {
	case 1:
		// La seule différence entre le 0 et le 1, c'est un ajout de "Pour la classe" au-dessus de min/classe/max
		$fichier_bulletin = "bull_html_edit_0.inc";
		break;
	case 2:
		$fichier_bulletin = "bull_html_edit_1.inc";
		break;
	case 3:
		$fichier_bulletin = "bull_html_edit_2.inc";
		break;
	default:
		$fichier_bulletin = "bull_html_edit_1.inc";
	}
	//
	// Pour afficher les trois colonnes en une seule, on transmet '1':
	$min_max_moyclas=getSettingValue("min_max_moyclas") ? getSettingValue("min_max_moyclas") : 0;


	echo "</style>
    <link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />
    <link rel='icon' type='image/ico' href='../favicon.ico' />\n";

	if(isset($style_screen_ajout)){
		// Styles paramétrables depuis l'interface:
		if($style_screen_ajout=='y'){
			// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
			// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
			echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />\n";
		}
	}

	echo "<style type='text/css'>
	@media screen{
		#infodiv {
			float: right;
			width: 20em;
			background-color: white;
		}
	}
	@media print{
		#infodiv {
			display:none;
		}
	}
</style>\n";


	echo "<style type='text/css'>
	@media screen{
		.espacement_bulletins {
			width: 100%;
			height: 50px;
			border:1px solid red;
			background-color: white;
		}
	}
	@media print{
		.espacement_bulletins {
			display:none;
		}

		#remarques_bas_de_page {
			display:none;
		}

		.alerte_erreur {
			display:none;
		}
	}
</style>\n";


	echo "</head>\n";
	echo "<body>\n";
	echo "<div>\n";
	echo "<div>\n";
	*/

/*
	// Inclusion des librairies spécifiques pour la génération du pdf

	require('../fpdf/fpdf.php');
	require('../fpdf/ex_fpdf.php');
	require_once("../fpdf/class.multicelltag.php");

	// Fichier d'extension de fpdf pour le bulletin
	require_once("../class_php/gepi_pdf.class.php");

	// Fonctions php des bulletins pdf
	require_once("bulletin_fonctions.php");
	// Ensemble des données communes
	require_once("bulletin_donnees.php");

	define('FPDF_FONTPATH','../fpdf/font/');
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');
	session_cache_limiter('private');

	$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
	$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
	$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;

	//variables invariables
	$annee_scolaire = $gepiYear;
	$date_bulletin = date("d/m/Y H:i");
	$nom_bulletin = date("Ymd_Hi");

*/



?>