<?php
/*
 *
 * $Id: bilan_absence.php 4098 2010-02-26 18:33:42Z crob $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

//$absencenj=isset($_POST['absencenj']) ? $_POST['absencenj'] : '';
//$retardnj=isset($_POST['retardnj']) ? $_POST['retardnj'] : '';

header('Content-Type: application/pdf');

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

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
}

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

$mode_utf8_pdf=getSettingValue('mode_utf8_abs_pdf');
if($mode_utf8_pdf!="y") {$mode_utf8_pdf="";}

define('FPDF_FONTPATH','../../fpdf/font/');
require('../../fpdf/fpdf.php');

// fonction de redimensionnement d'image
function redimensionne_logo($photo, $L_max, $H_max)
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

   $date_ce_jour = date('d/m/Y');
    if (empty($_GET['classe']) and empty($_POST['classe'])) {$classe="";}
    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
    if (empty($_GET['eleve']) and empty($_POST['eleve'])) {$eleve="tous";}
    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
    if (empty($_GET['du']) and empty($_POST['du'])) {$du="$date_ce_jour";}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['au']) and empty($_POST['au'])) {$au="$du";}
    else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
	if (empty($_GET['absencenj']) and empty($_POST['absencenj'])) { $absencenj = ''; }
	   else { if (isset($_GET['absencenj'])) { $absencenj = $_GET['absencenj']; } if (isset($_POST['absencenj'])) { $absencenj = $_POST['absencenj']; } }
	if (empty($_GET['retardnj']) and empty($_POST['retardnj'])) { $retardnj = ''; }
	   else { if (isset($_GET['retardnj'])) { $retardnj = $_GET['retardnj']; } if (isset($_POST['retardnj'])) { $retardnj = $_POST['retardnj']; } }

    if ($au == "" or $au == "JJ/MM/AAAA") { $au = $du; }

class bilan_PDF extends FPDF
{

    //En-tête du document
    function Header()
    {
	    global $prefix_base;
			$X_etab = '10'; $Y_etab = '10';
		        $caractere_utilse = 'Arial';
			$affiche_logo_etab='1';
			$entente_mel='0'; // afficher l'adresse mel dans l'entête
			$entente_tel='0'; // afficher le numéro de téléphone dans l'entête
			$entente_fax='0'; // afficher le numéro de fax dans l'entête
			$L_max_logo=75; $H_max_logo=75; //dimension du logo

    //Affiche le filigrame

	//bloc identification etablissement
	$logo = '../../images/'.getSettingValue('logo_etab');
	$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));
	if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
	{
	 $valeur=redimensionne_logo($logo, $L_max_logo, $H_max_logo);
	 //$X_logo et $Y_logo; placement du bloc identite de l'établissement
	 $X_logo=5; $Y_logo=5; $L_logo=$valeur[0]; $H_logo=$valeur[1];
	 $X_etab=$X_logo+$L_logo; $Y_etab=$Y_logo;
	 //logo
         $this->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
	}

	//adresse
 	 $this->SetXY($X_etab,$Y_etab);
 	 $this->SetFont($caractere_utilse,'',14);
	  //$gepiSchoolName = getSettingValue('gepiSchoolName');
	  $gepiSchoolName = traite_accents_utf8(getSettingValue('gepiSchoolName'));
	 $this->Cell(90,7, $gepiSchoolName,0,2,'');
	 $this->SetFont($caractere_utilse,'',10);
	  $gepiSchoolAdress1 = traite_accents_utf8(getSettingValue('gepiSchoolAdress1'));
	 $this->Cell(90,5, $gepiSchoolAdress1,0,2,'');
	  $gepiSchoolAdress2 = traite_accents_utf8(getSettingValue('gepiSchoolAdress2'));
	 $this->Cell(90,5, $gepiSchoolAdress2,0,2,'');
	  $gepiSchoolZipCode = traite_accents_utf8(getSettingValue('gepiSchoolZipCode'));
	  $gepiSchoolCity = traite_accents_utf8(getSettingValue('gepiSchoolCity'));
	 $this->Cell(90,5, $gepiSchoolZipCode." ".$gepiSchoolCity,0,2,'');
	  $gepiSchoolTel = getSettingValue('gepiSchoolTel');
	  $gepiSchoolFax = getSettingValue('gepiSchoolFax');
	if($entente_tel==='1' and $entente_fax==='1') { $entete_communic = 'Tél: '.$gepiSchoolTel.' / Fax: '.$gepiSchoolFax; }
	if($entente_tel==='1' and empty($entete_communic)) { $entete_communic = 'Tél: '.$gepiSchoolTel; }
	if($entente_fax==='1' and empty($entete_communic)) { $entete_communic = 'Fax: '.$gepiSchoolFax; }
	if( isset($entete_communic) and $entete_communic != '' ) {
	 $this->Cell(90,5, $entete_communic,0,2,'');
	}
	if($entente_mel==='1') {
	  $gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
	 $this->Cell(90,5, $gepiSchoolEmail,0,2,'');
	}
    }

    //Pied de page du document
    function Footer()
    {

                 $niveau_etab = "";
                 $nom_etab = getSettingValue("gepiSchoolName");
                 $adresse1_etab = getSettingValue("gepiSchoolAdress1");
                 $adresse2_etab = getSettingValue("gepiSchoolAdress2");
                 $cp_etab = getSettingValue("gepiSchoolZipCode");
                 $ville_etab = getSettingValue("gepiSchoolCity");
                 $cedex_etab = "";
                 $telephone_etab = getSettingValue("gepiSchoolTel");
                 $fax_etab = getSettingValue("gepiSchoolFax");
                 $mel_etab = getSettingValue("gepiSchoolEmail");

        //Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
   	$this->SetXY(5,-10);
        //Police Arial Gras 6
        $this->SetFont('Arial','B',8);
	$this->SetLineWidth(0,2);
	$this->SetDrawColor(0, 0, 0);
	$this->Line(10, 280, 200, 280);
	$this->SetFont('Arial','',10);
	$this->SetY(280);
	$adresse = $nom_etab." - ".$adresse1_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
	if($adresse2_etab!="")
	{
	  $nom_etab." - ".$adresse1_etab." ".$adresse2_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
	}
	if($telephone_etab!="" and $fax_etab!="" and $mel_etab!="")
	{
	  $adresse2 = "Tél : ".$telephone_etab." - Fax : ".$fax_etab." - Mèl : ".$mel_etab;
	}
	if($telephone_etab=="" and $fax_etab!="" and $mel_etab!="")
	{
	  $adresse2 = "Fax : ".$fax_etab." - Mèl : ".$mel_etab;
	}
	if($telephone_etab!="" and $fax_etab=="" and $mel_etab!="")
	{
	  $adresse2 = "Tél : ".$telephone_etab." - Mèl : ".$mel_etab;
	}
	if($telephone_etab!="" and $fax_etab!="" and $mel_etab=="")
	{
	  $adresse2 = "Tél : ".$telephone_etab." - Fax : ".$fax_etab;
	}

	$this->Cell(0, 4.5, traite_accents_utf8($adresse), 0, 1, 'C', '');
	$this->Cell(0, 4.5, traite_accents_utf8($adresse2), 0, 1, 'C', '');
    }
}


//requete dans la base de donnée
  	//etablissement
    $niveau_etab = "";
    $nom_etab = traite_accents_utf8(getSettingValue("gepiSchoolName"));
	//$nom_etab = traite_accents_utf8(getSettingValue('gepiSchoolName'));
    $adresse1_etab = traite_accents_utf8(getSettingValue("gepiSchoolAdress1"));
    $adresse2_etab = traite_accents_utf8(getSettingValue("gepiSchoolAdress2"));
    $cp_etab = traite_accents_utf8(getSettingValue("gepiSchoolZipCode"));
    $ville_etab = traite_accents_utf8(getSettingValue("gepiSchoolCity"));
    $cedex_etab = "";
    $telephone_etab = getSettingValue("gepiSchoolTel");
    $fax_etab = getSettingValue("gepiSchoolFax");
    $mel_etab = getSettingValue("gepiSchoolEmail");

	/* ********************************* */
	/* DEBUT - préparation de la requête */

	$select_requete = "e.login, e.nom, e.prenom, e.sexe, ae.type_absence_eleve, ae.justify_absence_eleve, ae.info_justify_absence_eleve, ae.motif_absence_eleve, ae.info_absence_eleve, ae.d_date_absence_eleve, ae.a_date_absence_eleve, ae.d_heure_absence_eleve, ae.a_heure_absence_eleve, ae.saisie_absence_eleve, jec.id_classe";
	$from_requete = $prefix_base . "absences_eleves ae, " . $prefix_base . " eleves e, " . $prefix_base . "j_eleves_classes jec";
	$where_requete = "( ae.d_date_absence_eleve <= '" . date_sql($au) . "' AND ae.a_date_absence_eleve >= '" . date_sql($du) . "' ) AND ae.eleve_absence_eleve = e.login AND jec.login = e.login";
	$groupby_requete = "ae.id_absence_eleve";
	$orderby_requete = "e.nom, e.prenom, ae.d_date_absence_eleve ASC";

		if ( $classe != 'tous' )
		{

			if ( $where_requete != '' ) $where_requete .= " AND ";
			$where_requete .= "id_classe = " . $classe;

		}

		if ( $eleve != 'tous' )
		{

			if ( $where_requete != '' ) $where_requete .= " AND ";
			$where_requete .= "e.login = '" . $eleve . "'";

		}

		if ( $absencenj === '1' )
		{

			if ( $where_requete != '' ) $where_requete .= " AND ";
			$where_requete .= "justify_absence_eleve != 'O' AND type_absence_eleve = 'A'";

		}

		if ( $retardnj === '1' )
		{

			if ( $where_requete != '' ) $where_requete .= " AND ";
			$where_requete .= "justify_absence_eleve != 'O' AND type_absence_eleve = 'R'";

		}

	/* FIN - de prépation de la requête */
	/* ******************************** */


	/* ******************************************* */
	/* DEBUT - construction du tableau des données */

		$cpt = 0;

		$requete = "SELECT " . $select_requete . " FROM " . $from_requete . " WHERE " . $where_requete . " GROUP BY " . $groupby_requete . " ORDER BY " . $orderby_requete;
		$execution = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
		while($donnee = mysql_fetch_array($execution))
		{

			$tableau[$cpt]['nom'] = $donnee['nom'];
			$tableau[$cpt]['prenom'] = $donnee['prenom'];
			$tableau[$cpt]['regime'] = regime($donnee['login']);
			$tableau[$cpt]['identite'] = strtoupper($tableau[$cpt]['nom'])." ".ucfirst($tableau[$cpt]['prenom'])." (" . $tableau[$cpt]['regime'] . ")";
			// classe
			$tableau[$cpt]['classe'] = classe_court_de($donnee['login']);

			// motif
			$motif_abrege = $donnee['motif_absence_eleve'];
			$motif_texte['A'] = '';

			if ( !isset($motif_texte[$motif_abrege]) )
			{

				$motif_texte[$motif_abrege] = motif_type_abs($motif_abrege);

			}

        	$tableau[$cpt]['motif'] = tronquer_texte($motif_texte[$motif_abrege], '20')." (".$donnee['type_absence_eleve'].")";

			// horodatage
			// début
			$tableau[$cpt]['debut'] = date_fr($donnee['d_date_absence_eleve']);
			if ( !empty($donnee['d_heure_absence_eleve']) )
			{

				$tableau[$cpt]['debut'] .= " à ".heure($donnee['d_heure_absence_eleve']);

			}

			// fin
            if ( $donnee['a_heure_absence_eleve'] == "" or $donnee['a_heure_absence_eleve'] == "00:00:00" or $donnee['a_heure_absence_eleve'] == $donnee['d_heure_absence_eleve'] )
        	{

            	$tableau[$cpt]['fin'] = "";

        	}
        	else
        	{

            	$tableau[$cpt]['fin'] = date_fr($donnee['a_date_absence_eleve']);
            	if ( !empty($donnee['a_heure_absence_eleve']) )
            	{

            		$tableau[$cpt]['fin'] .= " à ".heure($donnee['a_heure_absence_eleve']);

				}

        	}

			$cpt = $cpt + 1;

		};


	/* FIN - construction du tableau des données */
	/* ***************************************** */

	/* ********************************* */
	/* DEBUT - gestion du nombre de page */

    $nb_par_page = 35;
    $nb_page = ceil( $cpt / $nb_par_page );

    /* FIN - gestion du nombre de page */
    /* ******************************* */

/* ************************* */
/* DEBUT - Génération du PDF */

	$pdf=new bilan_PDF('P','mm','A4');
	$pdf->Open();
	$pdf->SetAutoPageBreak(true);

	// champs facultatifs
	$pdf->SetAuthor('');
	$pdf->SetCreator('créer avec Fpdf');
	$pdf->SetTitle('Bilan des absences général');
	$pdf->SetSubject('Bilan des absences général');

	$pdf->SetMargins(10,10);

	$page = 0;
	$nb_debut = 0;
	$nb_fin = 0;
	// compteur du passage des entree
	$cpt = 0;

while ( $page < $nb_page )
{

	$pdf->AddPage();

	/* *********************************** */
	/*            DEBUT - ENTETE           */

	$pdf->SetFont('Arial','',12);
	$pdf->SetY(20);
	$pdf->SetX(65);

	$pdf->SetFont('Arial','B',18);
	$pdf->Cell(0, 6, 'RELEVE DES ABSENCES', 0, 1, 'C', '');

	$pdf->SetFont('Arial','',10);
	if ( $absencenj === '1' ) { $pdf->SetX(65); $pdf->Cell(0, 4, 'avec option absence sans justification', 0, 1, 'C', ''); }
	if ( $retardnj === '1' ) { $pdf->SetX(65); $pdf->Cell(0, 4, 'avec option retard sans justification', 0, 1, 'C', ''); }

	$pdf->SetFont('Arial','',14);
	$duu = "du ".date_frl(date_sql($du));
	$auu = "au ".date_frl(date_sql($au));

	$pdf->SetX(65);
	$pdf->Cell(0, 8, $duu, 0, 1, 'C', '');
	if ($du != $au)
	{

		$pdf->SetX(65);
  		$pdf->Cell(0, 6, $auu, 0, 1, 'C', '');

	}

	/*            FIN - ENTETE           */
	/* ********************************* */


	/* **************************** */
	/* DEBUT - Affichage du tableau */

	$pdf->SetX(30);
	$pdf->SetY(52);
    $pdf->SetFont('Arial','',9.5);
    $pdf->Cell(55, 5, traite_accents_utf8('Nom et Prénom'), 1, 0, 'C', '');
    $pdf->Cell(17, 5, 'Classe', 1, 0, 'C', '');
    $pdf->Cell(42, 5, 'Motif', 1, 0, 'C', '');
    $pdf->Cell(38, 5, 'Du', 1, 0, 'C', '');
    $pdf->Cell(38, 5, 'Au', 1, 1, 'C', '');

	$nb_sur_cette_page = 0;
	while ( $nb_sur_cette_page < $nb_par_page )
    {

    	//tableau des absences
    	if ( !empty($tableau[$cpt]['identite']) )
    	{

        	$pdf->SetFont('Arial','',9);
        	$pdf->SetFont('Arial','',9);
			$pdf->Cell(55, 5, traite_accents_utf8($tableau[$cpt]['identite']), 1, 0, '', '');
        	$pdf->Cell(17, 5, traite_accents_utf8($tableau[$cpt]['classe']), 1, 0, '', '');
			$pdf->Cell(42, 5, traite_accents_utf8($tableau[$cpt]['motif']), 1, 0, '', '');
        	$pdf->Cell(38, 5, traite_accents_utf8($tableau[$cpt]['debut']), 1, 0, '', '');
        	$pdf->Cell(38, 5, traite_accents_utf8($tableau[$cpt]['fin']), 1, 1, '', '');

        }

		$cpt = $cpt + 1;
		$nb_sur_cette_page = $nb_sur_cette_page + 1;

    }
    $pdf->Cell(0, 5, '(A): absence     (R): retard     (I): infirmerie     (D): dispense', 0, 1, '', '');

    /* FIN - Affichage du tableau */
    /* ************************** */

	/* ******************** */
	/* DEBUT - Pied de page */

	if ( $nb_page > 1 )
	{

    	$nb_affiche_page = $page + 1;
    	$nb_affiche_sur_page = $nb_page;
    	$info_page = "page : ".$nb_affiche_page."/".$nb_affiche_sur_page;
    	$pdf->Cell(0, 5, $info_page, 0, 1, 'C', '');

	}

	/* FIN - Pied de page */
	/* ******************** */

	$nb_debut = $nb_debut + $nb_par_page;
	$page = $page + 1;

}

// initialise le nom du fichier
$datation_fichier = date("Ymd_Hi");
$nom_fichier = 'Bilan_absence_' . $datation_fichier . '.pdf';

// générer la sotie PDF
$pdf->Output($nom_fichier,'I');

/* FIN - Génération du PDF */
/* *********************** */
?>