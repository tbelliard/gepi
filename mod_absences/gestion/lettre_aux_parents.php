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
    if (empty($_GET['choix']) AND empty($_POST['choix'])) { exit(); }
    else { if (isset($_GET['choix'])) {$choix=$_GET['choix'];} if (isset($_POST['choix'])) {$choix=$_POST['choix'];} }
    if (empty($_GET['id_eleve']) AND empty($_POST['id_eleve'])) {$id_eleve="";}
    else { if (isset($_GET['id_eleve'])) {$id_eleve=$_GET['id_eleve'];} if (isset($_POST['id_eleve'])) {$id_eleve=$_POST['id_eleve'];} }
    if (empty($_GET['du']) AND empty($_POST['du'])) {$du="$date_ce_jour";}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['nbi']) AND empty($_POST['nbi'])) { exit(); }
    else { if (isset($_GET['nbi'])) {$nbi=$_GET['nbi'];} if (isset($_POST['nbi'])) {$nbi=$_POST['nbi'];} }
    if (empty($_GET['a_imprimer']) AND empty($_POST['a_imprimer'])) {$a_imprimer="";}
    else { if (isset($_GET['a_imprimer'])) {$a_imprimer=$_GET['a_imprimer'];} if (isset($_POST['a_imprimer'])) {$a_imprimer=$_POST['a_imprimer'];} }

define('FPDF_FONTPATH','../../fpdf/font/');
require('../../fpdf/fpdf.php');
$p = 1;

//requete dans la base de donnée
  //etablissement
                 $niveau_etab = "";
                 $nom_etab = (getSettingValue("gepiSchoolName"));
                 $adresse1_etab = (getSettingValue("gepiSchoolAdress1"));
                 $adresse2_etab = (getSettingValue("gepiSchoolAdress2"));
                 $cp_etab = (getSettingValue("gepiSchoolZipCode"));
                 $ville_etab = (getSettingValue("gepiSchoolCity"));
                 $cedex_etab = "";
                 $telephone_etab = getSettingValue("gepiSchoolTel");
                 $fax_etab = getSettingValue("gepiSchoolFax");
                 $mel_etab = getSettingValue("gepiSchoolEmail");

  //information sur l'élève
      $nb = 0;
      $t = 0;
      while(empty($id_eleve[$t])==false)
           {
            if (isset($a_imprimer[$t]))
            {
              $id_eleve_pdf = $id_eleve[$t];
              $eleve_sql=mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT * FROM '.$prefix_base.'eleves WHERE login = "'.$id_eleve_pdf.'"');
              while($eleve_data = mysqli_fetch_array($eleve_sql))
                {
                     $id[$nb] =  $eleve_data['login'];
                   $civilite[$nb] = "";
                   if ($eleve_data['sexe']=="M") { $civilite[$nb]="M."; } elseif ($eleve_data['sexe']=="F") { $civilite[$nb]="Mlle"; }
                   $nom_eleve[$nb] = strtoupper($eleve_data['nom']);
                   $prenom_eleve[$nb] = ucfirst($eleve_data['prenom']);
                   $division[$nb] = classe_de($eleve_data['login']);
                   $nb = $nb + 1;
                }
            $t = $t + 1;
            } else { $t = $t + 1; }
            }

  //information sur les parents
      $nb_1 = 0;
      $t_1 = 0;
      while(empty($id_eleve[$t_1])==false)
           {
               if (isset($a_imprimer[$t_1]))
                {
                    $id_eleve_pdf = $id_eleve[$t_1];
                    $test_responsable = mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'responsables WHERE login = "'.$id_eleve_pdf.'" AND '.$prefix_base.'eleves.ereno = '.$prefix_base.'responsables.ereno'),0);
                    if ($test_responsable != 0)
                    {
                          $eleve_sql=mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'responsables WHERE login = "'.$id_eleve_pdf.'" AND '.$prefix_base.'eleves.ereno = '.$prefix_base.'responsables.ereno');
                          while($eleve_data = mysqli_fetch_array($eleve_sql))
                            {
                               $civilite_responsable[$nb_1] = "M. et Mme";
                               $nom_responsable[$nb_1] = $eleve_data['nom1'];
                               $prenom_responsable[$nb_1] = $eleve_data['prenom1'];
                               $adresse1_responsable[$nb_1] = $eleve_data['adr1'];
                               $adresse2_responsable[$nb_1] = $eleve_data['adr1_comp'];
                               $cp_responsable[$nb_1] = $eleve_data['cp1'];
                               $ville_responsable[$nb_1] = $eleve_data['commune1'];
                               $nb_1 = $nb_1 + 1;
                            }
                     } else
                         {
                               $civilite_responsable[$nb_1] = "Pas de responsable existant";
                               $nom_responsable[$nb_1] = "";
                               $prenom_responsable[$nb_1] = "";
                               $adresse1_responsable[$nb_1] = "";
                               $adresse2_responsable[$nb_1] = "";
                               $cp_responsable[$nb_1] = "";
                               $ville_responsable[$nb_1] = "";
                               $nb_1 = $nb_1 + 1;
                         }
                    $t_1 = $t_1 + 1;
                } else { $t_1 = $t_1 + 1; }
            }


  //CPE
      $nb_cpe = 0;
      $t_cpe = 0;
      while(empty($id_eleve[$t_cpe])==false)
           {
            if (isset($a_imprimer[$t_cpe]))
            {
              if($cpe[$t_cpe]!="idem")
               {
                   $cpe_pdf = $cpe[$t_cpe];
               } else {
                         $t_cpe2 = $t_cpe;
                         $cpe_pdf = "idem";
                        while($cpe_pdf=="idem")
                        {
                          $t_cpe2 = $t_cpe2 - 1;
                          $cpe_pdf = $cpe[$t_cpe2];
                        }
                      }
              $cpe_sql=mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT '.$prefix_base.'utilisateurs.login, '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom, '.$prefix_base.'utilisateurs.civilite FROM '.$prefix_base.'utilisateurs WHERE '.$prefix_base.'utilisateurs.login="'.$cpe_pdf.'"');
              while($cpe_data = mysqli_fetch_array($cpe_sql))
                {
                   $civilite_cpe[$nb_cpe] = $cpe_data['civilite'];
                   $nom_cpe[$nb_cpe] = strtoupper($cpe_data['nom']);
                   $prenom_cpe[$nb_cpe] = ucfirst($cpe_data['prenom']);
                   $nb_cpe = $nb_cpe + 1;
                }
            $t_cpe = $t_cpe + 1;
            } else { $t_cpe = $t_cpe + 1; }
           }




// mode paysage, a4, etc.
$pdf=new FPDF('P','mm','A4');
$pdf->Open();
$pdf->SetAutoPageBreak(false);

// champs facultatifs
$pdf->SetAuthor('');
$pdf->SetCreator('créé avec Fpdf');
$pdf->SetTitle('Titre');
$pdf->SetSubject('Sujet');

// on charge les 83 gfx...
$pdf->SetMargins(10,10);
for ($i=0; $i<$nb; $i++) {
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

//$pdf->Image('../../images/logo.jpg',23,10,21,27,'JPEG');
$pdf->SetY(38);
$pdf->SetFont('DejaVu','B',10);
$int_etab = $niveau_etab.$nom_etab;
$pdf->Cell(50, 4, ($int_etab), 0, 1, 'C', '');
$pdf->SetFont('DejaVu','',10);
$pdf->Cell(50, 4, ($adresse1_etab), 0, 1, 'C', '');
if($adresse2_etab!="")
{
  $pdf->Cell(50, 4, ($adresse2_etab), 0, 1, 'C', '');
}
$ville = $cp_etab." ".$ville_etab." ".$cedex_etab;
$pdf->Cell(50, 4, ($ville), 0, 1, 'C', '');
if($mel_etab!="")
{
  $pdf->Cell(50, 4, $mel_etab, 0, 1, 'C', '');
}

$pdf->SetFont('DejaVu','',10);
// date
$Jour_semaine=date("w");
if ($Jour_semaine==0) {$jour='dimanche';}
elseif ($Jour_semaine==1) {$jour='lundi';}
elseif ($Jour_semaine==2) {$jour='mardi';}
elseif ($Jour_semaine==3) {$jour='mercredi';}
elseif ($Jour_semaine==4) {$jour='jeudi';}
elseif ($Jour_semaine==5) {$jour='vendredi';}
elseif ($Jour_semaine==6) {$jour='samedi';}
$aujourdhui = date("d/m/Y");
$aujourdhui = explode('/', $aujourdhui);
if ($aujourdhui[1]==1) { $aujourdhui[1]="janvier"; }
if ($aujourdhui[1]==2) { $aujourdhui[1]="février"; }
if ($aujourdhui[1]==3) { $aujourdhui[1]="mars"; }
if ($aujourdhui[1]==4) { $aujourdhui[1]="avril"; }
if ($aujourdhui[1]==5) { $aujourdhui[1]="mai"; }
if ($aujourdhui[1]==6) { $aujourdhui[1]="juin"; }
if ($aujourdhui[1]==7) { $aujourdhui[1]="juillet"; }
if ($aujourdhui[1]==8) { $aujourdhui[1]="août"; }
if ($aujourdhui[1]==9) { $aujourdhui[1]="septembre"; }
if ($aujourdhui[1]==10) { $aujourdhui[1]="octobre"; }
if ($aujourdhui[1]==11) { $aujourdhui[1]="novembre"; }
if ($aujourdhui[1]==12) { $aujourdhui[1]="décembre"; }
$aujourdhui = $ville_etab.', le '.$jour.' '.$aujourdhui[0].' '.$aujourdhui[1].' '.$aujourdhui[2];
$pdf->Text(109, 15,($aujourdhui));
$pdf->SetFont('DejaVu','',12);
$ident_responsable = $civilite_responsable[$i]." ".ucfirst($prenom_responsable[$i])." ".strtoupper($nom_responsable[$i]);
$pdf->Text(109, 40,($ident_responsable));
$pdf->Text(109, 45,($adresse1_responsable[$i]));
if($adresse2_responsable[$i] != "")
  {
    $pdf->Text(109, 50,'adresse2');
 }
$ident_ville = $cp_responsable[$i]." ".strtoupper($ville_responsable[$i]);
if($adresse2_responsable[$i] != "")
  {
    $pdf->Text(109, 55,($ident_ville));
  } else {
            $pdf->Text(109, 50,($ident_ville));
         }

if ($choix=="rappel")
  {
    $pdf->SetFont('DejaVu','B',24);
    $pdf->SetTextColor(190, 190, 190);
    $pdf->Text(65, 50,'RAPPEL');
    $pdf->SetTextColor(0, 0, 0);
  }
$pdf->SetFont('DejaVu','B',18);
$pdf->Text(39, 75,'LETTRE D\'INFORMATION AUX FAMILLES');
$pdf->SetLineWidth(0,2);
$pdf->SetDrawColor(0, 0, 0);
$pdf->Rect(30, 67.5, 145, 10, 'D');
$pdf->SetFont('DejaVu','',12);
$pdf->Text(20, 90,'Madame, Monsieur,');
$ident = "A notre connaissance, l'élève ".$nom_eleve[$i]." ".$prenom_eleve[$i]." de la classe de ".$division[$i].",";
$pdf->Text(20, 100, ($ident));
$pdf->Text(20, 110,('n\'a pas assisté au(x) cours suivant(s)'));

//tableau
$pdf->SetX(30);
$pdf->SetY(120);
            $pdf->SetFont('DejaVu','',9.5);
            $pdf->Cell(55, 5, 'Du', 1, 0, '', '');
            $pdf->Cell(55, 5, 'Au', 1, 0, '', '');
            $pdf->Cell(22, 5, 'Type', 1, 0, 'C', '');
            $pdf->Cell(54, 5, 'Merci d\'indiquer le motif', 1, 1, 'C', '');
$requete_1 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE (d_date_absence_eleve = '".date_sql($du)."' OR (d_date_absence_eleve <= '".date_sql($du)."' AND a_date_absence_eleve >= '".date_sql($du)."')) AND justify_absence_eleve != 'O' AND eleve_absence_eleve=login AND login='".$id[$i]."'";
$execution_1 = mysqli_query($GLOBALS["___mysqli_ston"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
while ( $data_1 = mysqli_fetch_array($execution_1))
      {
      //tableau des absences
            $pdf->SetFont('DejaVu','',9.5);
            $debut = date_frc($data_1['d_date_absence_eleve'])." à ".heure($data_1['d_heure_absence_eleve']);
            $pdf->Cell(55, 10, ($debut), 1, 0, '', '');
            $fin = date_frc($data_1['a_date_absence_eleve'])." à ".heure($data_1['a_heure_absence_eleve']);
            $pdf->Cell(55, 10, ($fin), 1, 0, '', '');
            if ($data_1['type_absence_eleve'] == 'A') {$pour = "Absence"; }
            if ($data_1['type_absence_eleve'] == 'R') {$pour = "Retard"; }
            if ($data_1['type_absence_eleve'] == 'D') {$pour = "Dispence"; }
            if ($data_1['type_absence_eleve'] == 'I') {$pour = "Infirmerie"; }
            $pdf->Cell(22, 10, $pour, 1, 0, 'C', '');
            $pdf->Cell(54, 10, '', 1, 1, 'C', '');
      }

$pdf->SetX(30);
$pdf->SetY(210);
$pdf->Write( 5, ('         Je vous remercie de bien vouloir faire connaître le motif de son absence dans les meilleurs délais afin de régulariser sa situation. Si vous avez déjà fourni un justificatif, veuillez ne pas tenir compte de ce courrier.'));
$pdf->SetY(230);
if(mb_substr($civilite_cpe[$i],0,1) == "M" OR mb_substr($civilite_cpe[$i],0,1) == "" ) { $nomine = 'Le Conseiller Principal d\'Education'; }
if(mb_substr($civilite_cpe[$i],0,2) == "Mm") { $nomine = 'La Conseillère Principale d\'Education'; }
if(mb_substr($civilite_cpe[$i],0,2) == "Ml") { $nomine = 'La Conseillère Principale d\'Education'; }
$pdf->Cell(0, 5, ($nomine), 0, 1, 'R', '');
$pdf->Cell(0, 5, ($civilite_cpe[$i]." ".mb_substr($prenom_cpe[$i],0,1).". ".$nom_cpe[$i]), 0, 1, 'R', '');
$pdf->SetY(250);
$pdf->Cell(60, 5, 'DATE :', 0, 0, '', '');
$pdf->Cell(50, 5, 'SIGNATURE DU RESPONSABLE :', 0, 1, '', '');
$pdf->SetY(275);
//$pdf->Cell(0, 5, 'Numéro de référence : 154214', 0, 1, 'R', '');
$pdf->SetLineWidth(0,2);
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, 280, 200, 280);
$pdf->SetFont('DejaVu','',10);
$pdf->SetY(280);
$adresse = $niveau_etab.$nom_etab." - ".$adresse1_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
if($adresse2_etab!="")
{
  $niveau_etab.$nom_etab." - ".$adresse1_etab." ".$adresse2_etab." - ".$cp_etab." ".$ville_etab." ".$cedex_etab;
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

$pdf->Cell(0, 5, ($adresse), 0, 1, 'C', '');
$pdf->Cell(0, 5, ($adresse2), 0, 1, 'C', '');
}

// Et on affiche le pdf généré... (ou on le sauvegarde en local)
// $pdf->Output(); pour afficher sur votre browser

$pref_output_mode_pdf=get_output_mode_pdf();

$nom_lettre=date("Ymd_Hi");
$nom_lettre='Lettre_'.$nom_lettre.'.pdf';
$pdf->Output($nom_lettre,$pref_output_mode_pdf);


?>
