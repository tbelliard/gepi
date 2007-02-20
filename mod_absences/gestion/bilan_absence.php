<?php
/*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
die();
}

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
    if (empty($_GET['classe']) AND empty($_POST['classe'])) {$classe="";}
    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
    if (empty($_GET['eleve']) AND empty($_POST['eleve'])) {$eleve="tous";}
    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
    if (empty($_GET['du']) AND empty($_POST['du'])) {$du="$date_ce_jour";}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['au']) AND empty($_POST['au'])) {$au="$du";}
    else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }

    if ($au == "" OR $au == "JJ/MM/AAAA") { $au = $du; }


define('FPDF_FONTPATH','../../fpdf/font/');
require('../../fpdf/fpdf.php');

//requete dans la base de donnée
  //etablissement
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
  //contage des pages
      if ($classe != "tous" AND $eleve == "tous")
        {
          $cpt_requete_1 =mysql_result(mysql_query("SELECT DISTINCT count(*) FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=".$prefix_base."eleves.login AND ".$prefix_base."j_eleves_classes.login=".$prefix_base."eleves.login AND id_classe='".$classe."' GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC"),0);
        }
      if ($classe == "tous" AND $eleve == "tous")
        {
          $cpt_requete_1 =mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=login GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC"),0);
        }
      if (($classe != "tous" OR $classe == "tous") AND $eleve != "tous")
        {
          $cpt_requete_1 =mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=login AND login='".$eleve."' GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC"),0);
        }

        //je compte le nombre de page
        $nb_par_page = 35;
        $nb_page = $cpt_requete_1 / $nb_par_page;
        if(number_format($cpt_requete_1, 0, ',' ,'') == number_format($nb_page, 0, ',' ,'')) { $nb_page = number_format($nb_page, 0, ',' ,''); } else { $nb_page = number_format($nb_page, 0, ',' ,'') + 1; }

// mode paysage, a4, etc.
$pdf=new FPDF('P','mm','A4');
$pdf->Open();
$pdf->SetAutoPageBreak(false);

// champs facultatifs
$pdf->SetAuthor('');
$pdf->SetCreator('créer avec Fpdf');
$pdf->SetTitle('Titre');
$pdf->SetSubject('Sujet');

// on charge les 83 gfx...
$pdf->SetMargins(10,10);
$page = 0;
$nb_debut = 0;
$nb_fin = 0;
while ($page<$nb_page) {
$pdf->AddPage();
	// information logo
	$L_max_logo='75'; // Longeur maxi du logo
	$H_max_logo='75'; // hauteur maxi du logo
	$logo = '../../images/'.getSettingValue('logo_etab');
	$valeur=redimensionne_logo($logo, $L_max_logo, $H_max_logo);
	$X_logo='23';
	$Y_logo='10';
	$L_logo=$valeur[0];
	$H_logo=$valeur[1];
        //logo
	$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
$pdf->SetY(38);
$pdf->SetFont('Arial','B',10);
$int_etab = $niveau_etab." de ".$nom_etab;
$pdf->Cell(50, 4, $int_etab, 0, 1, 'C', '');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50, 4, $adresse1_etab, 0, 1, 'C', '');
if($adresse2_etab!="")
{
  $pdf->Cell(50, 4, $adresse2_etab, 0, 1, 'C', '');
}
$ville = $cp_etab." ".$ville_etab." ".$cedex_etab;
$pdf->Cell(50, 4, $ville, 0, 1, 'C', '');
if($mel_etab!="")
{
  $pdf->Cell(50, 4, $mel_etab, 0, 1, 'C', '');
}
$pdf->SetFont('Arial','',12);
$pdf->SetY(20);
$pdf->SetX(65);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0, 6, 'RELEVE DES ABSENCES', 0, 1, 'C', '');
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
//tableau
$pdf->SetX(30);
$pdf->SetY(60);
            $pdf->SetFont('Arial','',9.5);
            $pdf->Cell(55, 5, 'Nom et Prénom', 1, 0, 'C', '');
            $pdf->Cell(15, 5, 'Classe', 1, 0, 'C', '');
            $pdf->Cell(40, 5, 'Motif', 1, 0, 'C', '');
            $pdf->Cell(40, 5, 'Du', 1, 0, 'C', '');
            $pdf->Cell(40, 5, 'Au', 1, 1, 'C', '');

if ($classe != "tous" AND $eleve == "tous")
    {
      $requete_1 ="SELECT DISTINCT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=".$prefix_base."eleves.login AND ".$prefix_base."j_eleves_classes.login=".$prefix_base."eleves.login AND id_classe='".$classe."' GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC";
    }
if ($classe == "tous" AND $eleve == "tous")
    {
      $requete_1 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=login GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC LIMIT $nb_debut, $nb_par_page";
    }
if (($classe != "tous" OR $classe == "tous") AND $eleve != "tous")
    {
      $requete_1 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=login AND login='".$eleve."' GROUP BY id_absence_eleve ORDER BY nom, prenom, d_date_absence_eleve ASC";
    }

$execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
while ( $data_1 = mysql_fetch_array($execution_1))
      {
      //tableau des absences
            $pdf->SetFont('Arial','',9);
            $pdf->SetFont('Arial','',9);
            $ident_eleve = strtoupper($data_1['nom'])." ".ucfirst($data_1['prenom']);
            $pdf->Cell(55, 5, $ident_eleve, 1, 0, '', '');
            $classe_eleve = classe_de($data_1['login']);
            $pdf->Cell(15, 5, $classe_eleve, 1, 0, '', '');
            $motif = motab_c($data_1['motif_absence_eleve'])." (".$data_1['type_absence_eleve'].")";
            $pdf->Cell(40, 5, $motif, 1, 0, '', '');
            $debut = date_fr($data_1['d_date_absence_eleve'])." à ".heure($data_1['d_heure_absence_eleve']);
            $pdf->Cell(40, 5, $debut, 1, 0, '', '');
            if($data_1['a_heure_absence_eleve'] == "" OR $data_1['a_heure_absence_eleve'] == "00:00:00" OR $data_1['a_heure_absence_eleve'] == $data_1['d_heure_absence_eleve'])
            {
            $fin = "";
            } else {
                     $fin = date_fr($data_1['a_date_absence_eleve'])." à ".heure($data_1['a_heure_absence_eleve']);
                   }

            $pdf->Cell(40, 5, $fin, 1, 1, '', '');
      }
    $pdf->Cell(0, 5, '(A): absence     (R): retard     (I): infirmerie     (D): dispence', 0, 1, '', '');

if($nb_page>1)
{
    $nb_affiche_page = $page + 1;
    $nb_affiche_sur_page = $nb_page;
    $info_page = "page : ".$nb_affiche_page."/".$nb_affiche_sur_page;
    $pdf->Cell(0, 5, $info_page, 0, 1, 'C', '');
}

$pdf->SetLineWidth(0,2);
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, 280, 200, 280);
$pdf->SetFont('Arial','',10);
$pdf->SetY(280);
$adresse = $niveau_etab." de ".$nom_etab." - ".$adresse1_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
if($adresse2_etab!="")
{
  $niveau_etab." de ".$nom_etab." - ".$adresse1_etab." ".$adresse2_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
}
if($telephone_etab!="" AND $fax_etab!="" AND $mel_etab!="")
{
  $adresse2 = "Tel: ".$telephone_etab." - Fax: ".$fax_etab." - Mele: ".$mel_etab;
}
if($telephone_etab=="" AND $fax_etab!="" AND $mel_etab!="")
{
  $adresse2 = "Fax: ".$fax_etab." - Mele: ".$mel_etab;
}
if($telephone_etab!="" AND $fax_etab=="" AND $mel_etab!="")
{
  $adresse2 = "Tel: ".$telephone_etab." - Mele: ".$mel_etab;
}
if($telephone_etab!="" AND $fax_etab!="" AND $mel_etab=="")
{
  $adresse2 = "Tel: ".$telephone_etab." - Fax: ".$fax_etab;
}

$pdf->Cell(0, 5, $adresse, 0, 1, 'C', '');
$pdf->Cell(0, 5, $adresse2, 0, 1, 'C', '');
//}
$nb_debut = $nb_debut + $nb_par_page;
$page = $page + 1;
}
// Et on affiche le pdf généré... (ou on le sauvegarde en local)
// $pdf->Output(); pour afficher sur votre browser
$nom_lettre=date("Ymd_Hi");
$nom_lettre='Bilan_absence_'.$nom_lettre.'.pdf';
$pdf->Output($nom_lettre,'I');


?>
