<?php
/*
*
* $Id$
*
* Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
* sur proposition de Didier Blanqui
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

$niveau_arbo = 2;
//================================
// REMONTé: boireaus 20080102
// Initialisations files
require_once('../../lib/initialisations.inc.php');

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}
//================================
require('../../fpdf/fpdf.php');

include("../../edt_organisation/fonctions_edt.php");
include("../../edt_organisation/fonctions_calendrier.php");

include("../lib/functions.php");

define('FPDF_FONTPATH','../../fpdf/font/');

// définition des marge
	define('TopMargin','5');
	define('RightMargin','2');
	define('LeftMargin','2');
	define('BottomMargin','5');
	define('LargeurPage','210');
	define('HauteurPage','297');


/* ************************* */
/*    fonction spécifique    */
/* ************************* */
function redimensionne_image_logo($photo, $L_max, $H_max)
{
	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher en pixel
	$taille_max_largeur = $L_max;
	$taille_max_hauteur = $H_max;

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $taille_max_largeur;
	$ratio_h = $hauteur / $taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	// des Pixels vers Millimetres
	$nouvelle_largeur = $nouvelle_largeur / 2.8346;
	$nouvelle_hauteur = $nouvelle_hauteur / 2.8346;

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

header('Content-Type: application/pdf');

/* ******************************************** */
/*     initialisation des variable d'entré      */
/* ******************************************** */

//choix du tri pour le tableau
$tri = isset($_GET['tri']) ? $_GET['tri'] : 'nom, prenom';

// On ajoute un paramètre sur les élèves de ce CPE en particulier
$sql_eleves_cpe = "SELECT e_login FROM j_eleves_cpe WHERE cpe_login = '".$_SESSION['login']."'";
$query_eleves_cpe = mysql_query($sql_eleves_cpe) OR die('Erreur SQL ! <br />' . $sql_eleves_cpe . ' <br /> ' . mysql_error());
$test_cpe = array();

$test_nbre_eleves_cpe = mysql_num_rows($query_eleves_cpe);
while($test_eleves_cpe = mysql_fetch_array($query_eleves_cpe)){
	$test_cpe[] = $test_eleves_cpe['e_login'];
}

	// initialisation des variables d'entrée
   if (empty($_GET['type']) and empty($_POST['type'])) { $type = ''; }
    else { if (isset($_GET['type'])) { $type = $_GET['type']; } if (isset($_POST['type'])) { $type = $_POST['type']; } }
   if (empty($_GET['justifie']) and empty($_POST['justifie'])) { $justifie = ''; }
    else { if (isset($_GET['justifie'])) { $justifie = $_GET['justifie']; } if (isset($_POST['justifie'])) { $justifie = $_POST['justifie']; } }
   if (empty($_GET['nonjustifie']) and empty($_POST['nonjustifie'])) { $nonjustifie = ''; }
    else { if (isset($_GET['nonjustifie'])) { $nonjustifie = $_GET['nonjustifie']; } if (isset($_POST['nonjustifie'])) { $nonjustifie = $_POST['nonjustifie']; } }
   if (empty($_GET['motif']) and empty($_POST['motif'])) { $motif = ''; }
    else { if (isset($_GET['motif'])) { $motif = $_GET['motif']; } if (isset($_POST['motif'])) { $motif = $_POST['motif']; } }
   if (empty($_GET['classe_choix']) and empty($_POST['classe_choix'])) { $classe_choix = ''; }
    else { if (isset($_GET['classe_choix'])) { $classe_choix = $_GET['classe_choix']; } if (isset($_POST['classe_choix'])) { $classe_choix = $_POST['classe_choix']; } }
   if (empty($_GET['eleve_choix']) and empty($_POST['eleve_choix'])) { $eleve_choix = ''; }
    else { if (isset($_GET['eleve_choix'])) { $eleve_choix = $_GET['eleve_choix']; } if (isset($_POST['eleve_choix'])) { $eleve_choix = $_POST['eleve_choix']; } }

	$du = isset($_GET["du"]) ? $_GET["du"] : (isset($_POST["du"]) ? $_POST["du"] : '');
	$au = isset($_GET["au"]) ? $_GET["au"] : (isset($_POST["au"]) ? $_POST["au"] : '');

//affichage type de table{au
if ($type=='A') {$typetableau='des absences';}
if ($type=='R') {$typetableau='des retards';}
if ($type=='D') {$typetableau='des dispenses';}
if ($type=='I') {$typetableau='de l\'Infirmerie';}
// prépation de la requête modification pour absences par telephone didier
	if(!empty($type)) {
		$requete_recherche = 'type_absence_eleve = \''.$type.'\'';
	}
	if(!empty($justifie) and $justifie === '1') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'( justify_absence_eleve = \'O\' ';
	}
	if(!empty($nonjustifie) and $nonjustifie === '1') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			if(!empty($justifie)) {
				$requete_recherche = $requete_recherche.' OR ';
			} else {
				$requete_recherche = $requete_recherche.' AND (';
			}
		}
		$requete_recherche = $requete_recherche.'justify_absence_eleve = \'N\' OR justify_absence_eleve = \'T\')';
	}
	if(!empty($justifie) and empty($nonjustifie)) {
		$requete_recherche = $requete_recherche.')';
	}
	if(!empty($motif) and $motif != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		} $requete_recherche = $requete_recherche.'motif_absence_eleve = \''.$motif.'\'';
	}
	if(!empty($classe_choix) and $classe_choix != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'c.id = \''.$classe_choix.'\'';
	}
	if(!empty($eleve_choix) and $eleve_choix != 'tous') {
		if(!empty($requete_recherche) and $requete_recherche != '') {
			$requete_recherche = $requete_recherche.' AND ';
		}
		$requete_recherche = $requete_recherche.'e.login = \''.$eleve_choix.'\'';
	}

	// Pour les dates, on ajoute
	$complement_requete_du = '';
	if ($du != '') {
		$test = explode("/", $du);
		$date1 = $test[2] . '-' . $test[1] . '-' . $test[0];
		$complement_requete_du = " AND ((d_date_absence_eleve >= '" . $date1 . "' ";
	}
    $complement_requete_au = '';
	if ($au != '') {
		$test = explode("/", $au);
		$date2 = $test[2] . '-' . $test[1] . '-' . $test[0];
		$complement_requete_au = " AND d_date_absence_eleve <= '" . $date2 . "' )";
	}
	
				
	$complement_requete_dateincluse = " OR (d_date_absence_eleve <= '" . $date1 . "' AND a_date_absence_eleve >= '" . $date2 . "'))";//modif didier
	
	$requete = "SELECT * FROM
					".$prefix_base."classes c,
					".$prefix_base."eleves e,
					".$prefix_base."j_eleves_classes ec,
					".$prefix_base."absences_eleves
					WHERE eleve_absence_eleve = e.login
					AND e.login = ec.login
					AND c.id = ec.id_classe
					AND ".$requete_recherche.$complement_requete_du.$complement_requete_au.$complement_requete_dateincluse.//modif didier
					" GROUP BY id_absence_eleve ORDER BY ".$tri." ASC";

	$executer = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br>'.mysql_error());
	$nb_d_entree_total = mysql_num_rows( $executer );
	$i = 0;

	//mise des données dans un tableau
	while ($donner = mysql_fetch_array( $executer ))
	{
		// On vérifie que le cpe a les droits sur ces élèves
		if (in_array($donner["eleve_absence_eleve"], $test_cpe) OR $test_nbre_eleves_cpe === 0) {

			$tab_donnee[$i]['nom']							= $donner['nom'];
	    	$tab_donnee[$i]['prenom']						= $donner['prenom'];
			$tab_donnee[$i]['classe']						= $donner['classe'];
			$tab_donnee[$i]['d_date_absence_eleve']			= $donner['d_date_absence_eleve'];
			$tab_donnee[$i]['a_date_absence_eleve']			= $donner['a_date_absence_eleve'];
			$tab_donnee[$i]['d_heure_absence_eleve']		= $donner['d_heure_absence_eleve'];
			$tab_donnee[$i]['a_heure_absence_eleve']		= $donner['a_heure_absence_eleve'];
			$tab_donnee[$i]['motif_absence_eleve']			= $donner['motif_absence_eleve'];
			$tab_donnee[$i]['info_justify_absence_eleve']	= $donner['info_justify_absence_eleve'];
			$tab_donnee[$i]['info_absence_eleve']			= $donner['info_absence_eleve'];

			$i = $i+1; // On incrémente

		}
	}

/* *********************************************/
/* information sur la présentation du document */
/* *********************************************/

	$caractere_utilse = 'DejaVu'; // caractère utilisé
	$affiche_logo_etab = '1';
	$nom_etab_gras = '0';
	$entente_mel = '1'; // afficher l'adresse mel dans l'entête
		$courrier_image = '';
		$courrier_texte = '';
	$entente_tel = '1'; // afficher le numéro de téléphone dans l'entête
		$tel_image = '';
		$tel_texte = '';
	$entente_fax = '1'; // afficher le numéro de fax dans l'entête
		$fax_image = '';
		$fax_texte = '';
	$L_max_logo = 75; $H_max_logo=75; //dimension du logo
	$centrage_logo = '1'; // centrer le logo de l'établissement
	$Y_centre_logo = '18'; // centre du logo sur la page
	$affiche_date_edition = '1'; // affiche la date d'édition
	$taille_texte_date_edition = '8'; // définit la taille de la date d'édition

	// point de commencement du tableau sur la page
	$x_tab = '5';
	$y_tab = '40';

	// hauteur de l'entête
	$hau_entete = '6';
    // ajout classe dans tableau  et  modification différentes largeurs de colonne
	// largeur de la colonne du nom de l'élève
	$lar_col_eleve = '58';
	// largeur de la colonneclasse
	$lar_col_classe = '14';
    // largeur de la colonne Le
	$lar_col_date_le = '38';
	// largeur de la colonne date du
	$lar_col_date_du = '38';
	// largeur de la colonne date au
	$lar_col_date_au = '40';
	// largeur de la colonne Heure debut
	$lar_col_heure_debut = '18';
	// largeur de la colonne Heure retard
	$lar_col_heure_a = '18';
	// largeur de la colonne Heure fin
	$lar_col_heure_fin = '18';
	// largeur de la colonne cause
	$lar_col_cause = '50';
	// largeur de la colonne horaire dispense
	$lar_col_heure_dispense = '25';
      // largeur de la colonne motif
	$lar_col_motif = '14';
	// nombre de ligne à affiché sur 1 page
	$nb_ligne_parpage = '22';

	// largeur total du tableau
	$lar_total_tableau = $lar_col_eleve + $lar_col_date_du + $lar_col_date_au + $lar_col_heure_debut + $lar_col_heure_fin + $lar_col_motif;

	// hauteur de la cellule des données
	$hau_donnee = '10';

	// avec couleur ou sans
	$couleur_fond = '1';
// nombre de page à créer, arrondit au nombre supérieur
	$nb_page_total = ceil( $nb_d_entree_total / $nb_ligne_parpage );

/* **************************** */
/*     variable invariable      */
/* **************************** */

	$gepiYear = getSettingValue('gepiYear');
	$RneEtablissement = getSettingValue("gepiSchoolRne");
	$annee_scolaire = $gepiYear;
	$datation_fichier = date("Ymd_Hi");

/* **************************** */


// définition d'une variable
	$hauteur_pris = 0;

/* début de la génération du fichier PDF */

	//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
	$pdf=new FPDF('p', 'mm', 'A4');

		// si la variable $gepiSchoolName est vide alors on cherche les informations dans la base
	if ( empty($gepiSchoolName) )
	{

		$gepiSchoolName=getSettingValue('gepiSchoolName');

	}

	// création du document
	$pdf->SetCreator($gepiSchoolName);
	// auteur du document
	$pdf->SetAuthor($gepiSchoolName);
	// mots clé
	$pdf->SetKeywords('');
	// sujet du document
	$pdf->SetSubject('Bilan journalier des absences');
	// titre du document
	$pdf->SetTitle('Bilan journalier des absences');
	// méthode d'affichage du document à son ouverture
	$pdf->SetDisplayMode('fullwidth', 'single');
	// compression du document
	$pdf->SetCompression(TRUE);
	// change automatiquement de page à 5mm du bas
	$pdf->SetAutoPageBreak(TRUE, 5);


/* **************************** */
/* début de la boucle des pages */

// comptage du nombre de page traité
$nb_page_traite = 0;

// initialiser la variable compteur de ligne passé pour le tableau
$nb_ligne_passe = 0;

// initialiser un compteur temporaire autre que i
// il serviras pour savoir à quelle endroit de la liste nous somme rendus
$j = 0;

// boucle des page
while ($nb_page_traite < $nb_page_total)
{

	// ajout de l'initialisation d'une nouvelle page dans le document
	$pdf->AddPage();

	// police de caractère utilisé
	$pdf->SetFont('DejaVu');

/* ENTETE - DEBUT */

	//bloc identification etablissement
	$logo = '../../images/'.getSettingValue('logo_etab');
	$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));

	if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
	{

		$valeur=redimensionne_image_logo($logo, $L_max_logo, $H_max_logo);
		$X_logo = 5;
		$Y_logo = 5;
		$L_logo = $valeur[0];
		$H_logo = $valeur[1];
		$X_etab = $X_logo + $L_logo + 1;
		$Y_etab = $Y_logo;

		if ( !isset($centrage_logo) or empty($centrage_logo) )
		{

			$centrage_logo = '0';

		}
		if ( $centrage_logo === '1' )
		{

			// centrage du logo
			$centre_du_logo = ( $H_logo / 2 );
			$Y_logo = $Y_centre_logo - $centre_du_logo;

		}

		//logo
		$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);

	}

	//adresse
	if ( !isset($X_etab) or empty($X_etab) )
	{

		$X_etab = '5';
		$Y_etab = '5';

	}
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont('DejaVu','',14);
	$gepiSchoolName = getSettingValue('gepiSchoolName');

	// mettre en gras le nom de l'établissement si $nom_etab_gras = 1
	if ( $nom_etab_gras === '1' )
	{

		$pdf->SetFont('DejaVu','B',14);

	}
	$pdf->Cell(90,7, $gepiSchoolName,0,2,'');

	$pdf->SetFont('DejaVu','',10);
	$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');

	if ( $gepiSchoolAdress1 != '' )
	{

		$pdf->Cell(90,5, $gepiSchoolAdress1,0,2,'');

	}
	$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');

	if ( $gepiSchoolAdress2 != '' )
	{

		$pdf->Cell(90,5, $gepiSchoolAdress2,0,2,'');

	}

	$gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
	$gepiSchoolCity = getSettingValue('gepiSchoolCity');
	$pdf->Cell(90,5, $gepiSchoolZipCode." ".$gepiSchoolCity,0,2,'');
	$gepiSchoolTel = getSettingValue('gepiSchoolTel');
	$gepiSchoolFax = getSettingValue('gepiSchoolFax');

	$passealaligne = '0';
	// entête téléphone
			// emplacement du cadre télécome
			$x_telecom = $pdf->GetX();
			$y_telecom = $pdf->GetY();

	if( $entente_tel==='1' )
	{

		$grandeur = '';
		$text_tel = '';

		if ( $tel_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$tel_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_tel = '      '.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);
			$grandeur = $grandeur + 2;

		}
		if ( $tel_texte != '' and $tel_image === '' )
		{

			$text_tel = $tel_texte.''.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);

		}

		$pdf->Cell($grandeur,5, $text_tel,0,$passealaligne,'');

	}

	$passealaligne = '2';
	// entête fax
	if( $entente_fax==='1' )
	{

		$text_fax = '';

		if ( $fax_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$fax_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_fax = '      '.$gepiSchoolFax;

		}
		if ( $fax_texte != '' and $fax_image === '' )
		{

			$text_fax = $fax_texte.''.$gepiSchoolFax;

		}
		$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');

	}


	if($entente_mel==='1')
	{

		$text_mel = '';
		$y_telecom = $y_telecom + 5;
		$pdf->SetXY($x_telecom,$y_telecom);
		$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		$text_mel = $gepiSchoolEmail;

		if ( $courrier_image != '' )
		{

			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../../images/imabulle/'.$courrier_image.'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_mel = '      '.$gepiSchoolEmail;

		}
		if ( $courrier_texte != '' and $courrier_image === '' )
		{

			$text_mel = $courrier_texte.' '.$gepiSchoolEmail;

		}
		$pdf->Cell(90,5, $text_mel,0,2,'');

	}

/* ENTETE - FIN */


/* ENTETE TITRE - DEBUT */

		$pdf->SetFont('DejaVu','B',18);

		$pdf->SetXY(85, 10);

		$pdf->Cell(120, 6, 'Impression du Tableau '.$typetableau, 0, 1, 'C');

		$pdf->SetFont('DejaVu','',12);

		$pdf->SetX(85);
		$pdf->Cell(80, 6, 'du '.$du, 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(80, 6, 'au '.$au, 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(80, 4, 'année', 0, 1, 'C');

		$pdf->SetX(85);
		$pdf->Cell(80, 4, $annee_scolaire, 0, 1, 'C');


/* ENTETE TITRE - FIN */


/* ENTETE TABLEAU - DEBUT */
if ($type=='A') {
	//Sélection de la police
	$pdf->SetFont('DejaVu', 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule identité
	$pdf->Cell($lar_col_eleve, $hau_entete, 'Nom Prénom', 1, 0, 'C');

	// Cellule classe ajout

	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule date du
    $pdf->Cell($lar_col_date_du, $hau_entete, 'Absence du', 1, 0, 'C');

	// Cellule date au
    $pdf->Cell($lar_col_date_au, $hau_entete, 'au', 1, 0, 'C');

	// Cellule Heure debut
    $pdf->Cell($lar_col_heure_debut, $hau_entete, 'de', 1, 0, 'C');

		// Cellule Heure fin
    $pdf->Cell($lar_col_heure_debut, $hau_entete, 'à', 1, 0, 'C');
	// Cellule motif
    $pdf->Cell($lar_col_motif, $hau_entete, 'Motif', 1, 0, 'C');

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;}

	if ($type=='R') {
	//Sélection de la police
	$pdf->SetFont('DejaVu', 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule identité
	$pdf->Cell($lar_col_eleve, $hau_entete, 'Nom Prénom', 1, 0, 'C');

	// Cellule classe ajout

	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule date du
    $pdf->Cell($lar_col_date_le, $hau_entete, 'le', 1, 0, 'C');

	// Cellule heure
    $pdf->Cell($lar_col_heure_a, $hau_entete, 'a', 1, 0, 'C');

	// Cellule cause
    $pdf->Cell($lar_col_cause, $hau_entete, 'Cause', 1, 0, 'C');

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;}

	if ($type=='D') {
	//Sélection de la police
	$pdf->SetFont('DejaVu', 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule identité
	$pdf->Cell($lar_col_eleve, $hau_entete, 'Nom Prénom', 1, 0, 'C');

	// Cellule classe ajout

	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule date du
    $pdf->Cell($lar_col_date_du, $hau_entete, 'Du', 1, 0, 'C');

	// Cellule date au
    $pdf->Cell($lar_col_date_au, $hau_entete, 'au', 1, 0, 'C');

	// Cellule Heure debut
    $pdf->Cell($lar_col_horaire, $hau_entete, 'horaire', 1, 0, 'C');

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;}

	if ($type=='I') {
	//Sélection de la police
	$pdf->SetFont('DejaVu', 'B', 10);

	// placement du point de commencement du tableau
	$pdf->SetXY($x_tab, $y_tab);

	// Cellule identité
	$pdf->Cell($lar_col_eleve, $hau_entete, 'Nom Prénom', 1, 0, 'C');

	// Cellule classe ajout

	$pdf->Cell($lar_col_classe, $hau_entete, 'Classe', 1, 0, 'C');

	// Cellule date
    $pdf->Cell($lar_col_date_du, $hau_entete, 'Date', 1, 0, 'C');

	// Cellule Heure debut
    $pdf->Cell($lar_col_heure_debut, $hau_entete, 'de', 1, 0, 'C');

		// Cellule Heure fin
    $pdf->Cell($lar_col_heure_debut, $hau_entete, 'à', 1, 0, 'C');

	// variable qui contient le point Y suivant pour la ligne suivante
	$y_dernier = $y_tab + $hau_entete;}



/* ENTETE TABLEAU - FIN */

	/* ***************************************** */
	/* début de la boucle du tableau des données */

	// initialiser la variable compteur de ligne pour la page actuel
	$nb_ligne_passe_reel = 0;

	// tant qu'on a pas atteind le nombre de ligne maximum par page on fait la boucle
	if ($type=='A') {
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{        if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

	          // initialisation du point X et Y de la ligne des données
				$pdf->SetXY($x_tab, $y_dernier);
				// colonne du nom et prénom de l'élève
				$pdf->Cell($lar_col_eleve, $hau_donnee, $tab_donnee[$nb_ligne_passe]['nom']." ".$tab_donnee[$nb_ligne_passe]['prenom'], 1, 0, 'C');
				// classe ajout
				$pdf->Cell($lar_col_classe, $hau_donnee, $tab_donnee[$nb_ligne_passe]['classe'], 1, 0, 'C');
				// colonne du debut date absence
				$pdf->Cell($lar_col_date_du, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['d_date_absence_eleve']), 1, 0, 'C');
				// colonne de fin date absence
				$pdf->Cell($lar_col_date_au, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['a_date_absence_eleve']), 1, 0, 'C');
				// colonne de heyre début
				$pdf->Cell($lar_col_heure_debut, $hau_donnee, heure($tab_donnee[$nb_ligne_passe]['d_heure_absence_eleve']), 1, 0, 'C');
				// colonne de heure fin
				$pdf->Cell($lar_col_heure_fin, $hau_donnee, heure($tab_donnee[$nb_ligne_passe]['a_heure_absence_eleve']), 1, 0, 'C');
				// colonne motif
				$pdf->Cell($lar_col_motif, $hau_donnee, $tab_donnee[$nb_ligne_passe]['motif_absence_eleve'], 1, 0, 'C');
				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;
				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;
				// on incrémente le nombre de ligne traité dans le tableau des données
				$nb_ligne_passe = $nb_ligne_passe + 1;
				}
				else
		{

			// s'il n'y a plus de donnée à afficher alors on lui dit que le
			// maximum de ligne à était atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}


		}
	}

if ($type=='R') {
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{        if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

	          // initialisation du point X et Y de la ligne des données
				$pdf->SetXY($x_tab, $y_dernier);
				// colonne du nom et prénom de l'élève
				$pdf->Cell($lar_col_eleve, $hau_donnee, $tab_donnee[$nb_ligne_passe]['nom']." ".$tab_donnee[$nb_ligne_passe]['prenom'], 1, 0, 'C');
				// classe ajout
				$pdf->Cell($lar_col_classe, $hau_donnee, $tab_donnee[$nb_ligne_passe]['classe'], 1, 0, 'C');
				// colonne de date retard
				$pdf->Cell($lar_col_date_le, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['d_date_absence_eleve']), 1, 0, 'C');
				// colonne de heure retard
				$pdf->Cell($lar_col_heure_a, $hau_donnee, heure($tab_donnee[$nb_ligne_passe]['d_heure_absence_eleve']), 1, 0, 'C');
				// colonne motif
				$pdf->Cell($lar_col_cause, $hau_donnee, $tab_donnee[$nb_ligne_passe]['info_justify_absence_eleve'], 1, 0, 'C');
				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;
				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;
				// on incrémente le nombre de ligne traité dans le tableau des données
				$nb_ligne_passe = $nb_ligne_passe + 1;
				}
				else
		{

			// s'il n'y a plus de donnée à afficher alors on lui dit que le
			// maximum de ligne à était atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}


		}
	}
// tant qu'on a pas atteind le nombre de ligne maximum par page on fait la boucle
	if ($type=='D') {
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{        if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

	          // initialisation du point X et Y de la ligne des données
				$pdf->SetXY($x_tab, $y_dernier);
				// colonne du nom et prénom de l'élève
				$pdf->Cell($lar_col_eleve, $hau_donnee, $tab_donnee[$nb_ligne_passe]['nom']." ".$tab_donnee[$nb_ligne_passe]['prenom'], 1, 0, 'C');
				// classe ajout
				$pdf->Cell($lar_col_classe, $hau_donnee, $tab_donnee[$nb_ligne_passe]['classe'], 1, 0, 'C');
				// colonne du debut date dispense
				$pdf->Cell($lar_col_date_du, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['d_date_absence_eleve']), 1, 0, 'C');
				// colonne de fin date dispense
				$pdf->Cell($lar_col_date_au, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['a_date_absence_eleve']), 1, 0, 'C');
				// colonne Horaire
				$pdf->Cell($lar_col_horaire, $hau_donnee, $tab_donnee[$nb_ligne_passe]['info_absence_eleve'], 1, 0, 'C');
				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;
				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;
				// on incrémente le nombre de ligne traité dans le tableau des données
				$nb_ligne_passe = $nb_ligne_passe + 1;
				}
				else
		{

			// s'il n'y a plus de donnée à afficher alors on lui dit que le
			// maximum de ligne à était atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}


		}
	}
	if ($type=='I') {
	while ( $nb_ligne_passe_reel <= $nb_ligne_parpage )
	{        if ( !empty($tab_donnee[$nb_ligne_passe]) )
		{

	          // initialisation du point X et Y de la ligne des données
				$pdf->SetXY($x_tab, $y_dernier);
				// colonne du nom et prénom de l'élève
				$pdf->Cell($lar_col_eleve, $hau_donnee, $tab_donnee[$nb_ligne_passe]['nom']." ".$tab_donnee[$nb_ligne_passe]['prenom'], 1, 0, 'C');
				// classe ajout
				$pdf->Cell($lar_col_classe, $hau_donnee, $tab_donnee[$nb_ligne_passe]['classe'], 1, 0, 'C');
				// colonne du debut date
				$pdf->Cell($lar_col_date_du, $hau_donnee, date_frc($tab_donnee[$nb_ligne_passe]['d_date_absence_eleve']), 1, 0, 'C');
				// colonne de heure début
				$pdf->Cell($lar_col_heure_debut, $hau_donnee, heure($tab_donnee[$nb_ligne_passe]['d_heure_absence_eleve']), 1, 0, 'C');
				// colonne de heure fin
				$pdf->Cell($lar_col_heure_fin, $hau_donnee, heure($tab_donnee[$nb_ligne_passe]['a_heure_absence_eleve']), 1, 0, 'C');
				// variable qui contient le point Y suivant pour la ligne suivante
				$y_dernier = $y_dernier + $hau_donnee;
				// on incrémente le nombre de ligne passé sur la page
				$nb_ligne_passe_reel = $nb_ligne_passe_reel + 1;
				// on incrémente le nombre de ligne traité dans le tableau des données
				$nb_ligne_passe = $nb_ligne_passe + 1;
				}
				else
		{

			// s'il n'y a plus de donnée à afficher alors on lui dit que le
			// maximum de ligne à était atteint pour qu'il termine la boucle
			$nb_ligne_passe_reel = $nb_ligne_parpage + 1;

		}


		}
	}
	/* fin de la boucle du tableau des données */
	/* *************************************** */

/* PIED DE PAGE - DEBUT */

	//Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
	$pdf->SetXY(5,-10);

	//Police DejaVu Gras 6
	$pdf->SetFont('DejaVu','B',8);

	// formule du pied de page
	$nb_page_affiche=$nb_page_traite +1;
	$fomule = 'Tableau des absences du ' . $du . ' au ' . $au .' - page ' . $nb_page_affiche . '/' . $nb_page_total;

	// cellule de pied de page
	$pdf->Cell(0, 4.5, $fomule, 0, 0, 'C');

/* PIED DE PAGE - FIN */

	// on incrément le nombre d'entrée passé
	$nb_d_entree_passe = $nb_ligne_passe;

	// on incrément le nombre de page traité
	$nb_page_traite = $nb_page_traite + 1;
}
/* fin de la boucle des pages */
/* ************************** */

// fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'

	// génération du nom du document
	$nom_fichier = 'bilan_journalier_.pdf';

	// génération du document
	$pdf->Output($nom_fichier,'I');

?>
