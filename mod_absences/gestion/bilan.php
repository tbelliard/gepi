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
    if (empty($_GET['id_classe']) AND empty($_POST['id_classe'])) {$id_classe="";}
      else { if (isset($_GET['id_classe'])) {$id_classe=$_GET['id_classe'];} if (isset($_POST['id_classe'])) {$id_classe=$_POST['id_classe'];} }
    if (empty($_GET['du']) AND empty($_POST['du'])) {$du="$date_ce_jour";}
      else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
    if (empty($_GET['au']) AND empty($_POST['au'])) {$au="$du";}
      else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
    if (empty($_GET['a_imprimer']) AND empty($_POST['a_imprimer'])) {$a_imprimer="";}
      else { if (isset($_GET['a_imprimer'])) {$a_imprimer=$_GET['a_imprimer'];} if (isset($_POST['a_imprimer'])) {$a_imprimer=$_POST['a_imprimer'];} }

    if ($au == "" OR $au == "JJ/MM/AAAA") { $au = $du; }

define('FPDF_FONTPATH','../../fpdf/font/');
require('../../fpdf/fpdf.php');

$p = 1;
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

  //information sur l'élève
      $nb = 0;
      $t = 0;
      while(empty($id_classe[$t])==false)
           {
                if (isset($a_imprimer[$t]))
                {
                  $id_classe_pdf = $id_classe[$t];
//                  $eleve_sql=mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'responsables WHERE '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe_pdf.'" AND '.$prefix_base.'j_eleves_classes.login = '.$prefix_base.'eleves.login AND '.$prefix_base.'eleves.ereno = '.$prefix_base.'responsables.ereno GROUP BY '.$prefix_base.'eleves.nom, '.$prefix_base.'eleves.prenom ORDER BY nom, prenom ASC');
                  $eleve_sql=mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe_pdf.'" AND '.$prefix_base.'j_eleves_classes.login = '.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.nom, '.$prefix_base.'eleves.prenom ORDER BY nom, prenom ASC');
                  while($eleve_data = mysql_fetch_array($eleve_sql))
                    {
                        $test = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND eleve_absence_eleve=login AND login='".$eleve_data['login']."'"),0);
                        if ($test != "0")
                         {
                               $id[$nb] = $eleve_data['login'];
                               $id_eleve_pdf = $eleve_data['login'];
                               $civilite[$nb] = "";
                               if ($eleve_data['sexe']=="M") { $civilite[$nb]="M."; } elseif ($eleve_data['sexe']=="F") { $civilite[$nb]="Mlle"; }
                               $nom_eleve[$nb] = $eleve_data['nom'];
                               $prenom_eleve[$nb] = $eleve_data['prenom'];
                               $division[$nb] = classe_de($eleve_data['login']);
                               $test_responsable = mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'responsables WHERE login = "'.$id_eleve_pdf.'" AND '.$prefix_base.'eleves.ereno = '.$prefix_base.'responsables.ereno'),0);
                               if ($test_responsable != 0)
                                {
                                     $responsable_sql=mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'responsables WHERE '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe_pdf.'" AND '.$prefix_base.'j_eleves_classes.login = '.$prefix_base.'eleves.login AND '.$prefix_base.'eleves.ereno = '.$prefix_base.'responsables.ereno GROUP BY '.$prefix_base.'eleves.nom, '.$prefix_base.'eleves.prenom ORDER BY nom, prenom ASC');
                                     $responsable_data = mysql_fetch_array($responsable_sql);
                                     $civilite_responsable[$nb] = "M. et Mme";
                                     $nom_responsable[$nb] = $responsable_data['nom1'];
                                     $prenom_responsable[$nb] = $responsable_data['prenom1'];
                                     $adresse1_responsable[$nb] = $responsable_data['adr1'];
                                     $adresse2_responsable[$nb] = $responsable_data['adr1_comp'];
                                     $cp_responsable[$nb] = $responsable_data['cp1'];
                                     $ville_responsable[$nb] = $responsable_data['commune1'];
                                } else {
                                             $civilite_responsable[$nb] = "Pas de responsable sélectionné";
                                             $nom_responsable[$nb] = "";
                                             $prenom_responsable[$nb] = "";
                                             $adresse1_responsable[$nb] = "";
                                             $adresse2_responsable[$nb] = "";
                                             $cp_responsable[$nb] = "";
                                             $ville_responsable[$nb] = "";
                                       }
                    if($cpe[$t]!="idem")
                    {
                             $cpe_pdf = $cpe[$t];
                    } else {
                                 $t_2 = $t;
                                 $cpe_pdf = "idem";
                                 while($cpe_pdf=="idem")
                                    {
                                     $t_2 = $t_2 - 1;

                                     $cpe_pdf = $cpe[$t_2];
                                    }
                                  }
                         $cpe_sql=mysql_query('SELECT '.$prefix_base.'utilisateurs.login, '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom, '.$prefix_base.'utilisateurs.civilite FROM '.$prefix_base.'utilisateurs WHERE '.$prefix_base.'utilisateurs.login="'.$cpe_pdf.'"');
                         while($cpe_data = mysql_fetch_array($cpe_sql))
                            {
                               $civilite_cpe[$nb] = $cpe_data['civilite'];
                               $nom_cpe[$nb] = strtoupper($cpe_data['nom']);
                               $prenom_cpe[$nb] = ucfirst($cpe_data['prenom']);
                            }

                      $nb = $nb + 1;
                       }
                    }
                   $t = $t + 1;
                } else { $t = $t + 1; }
            }



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

$pdf->SetFont('Arial','',10);
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
$pdf->Text(109, 15,$aujourdhui);
$pdf->SetFont('Arial','',12);
$ident_responsable = $civilite_responsable[$i]." ".ucfirst($prenom_responsable[$i])." ".strtoupper($nom_responsable[$i]);
$pdf->Text(109, 40,$ident_responsable);
$pdf->Text(109, 45,$adresse1_responsable[$i]);
if($adresse2_responsable[$i] != "")
  {
    $pdf->Text(109, 50,'adresse2');
  }
$ident_ville = $cp_responsable[$i]." ".strtoupper($ville_responsable[$i]);
if($adresse2_responsable[$i] != "")
  {
    $pdf->Text(109, 55,$ident_ville);
  } else {
            $pdf->Text(109, 50,$ident_ville);
         }
$pdf->SetFont('Arial','',12);
$pdf->Text(20, 70,'Madame, Monsieur,');
$ident = "Voici le suivi de l'élève ".$nom_eleve[$i]." ".$prenom_eleve[$i]." de la classe de ".$division[$i].",";
$pdf->Text(20, 80, $ident);
$pdf->Text(20, 85,'sur la période du '.date_frl(date_sql($du))." au ".date_frl(date_sql($au)).".");
//tableau
$pdf->SetX(30);
$pdf->SetY(90);
            $pdf->SetFont('Arial','',9.5);
            $pdf->Cell(55, 5, 'Les Absences', 0, 1, '', '');
            $pdf->Cell(55, 5, 'Du', 1, 0, '', '');
            $pdf->Cell(55, 5, 'Au', 1, 0, '', '');
            $pdf->Cell(22, 5, 'Motif', 1, 0, 'C', '');
            $pdf->Cell(54, 5, 'le motif spécifiée', 1, 1, 'C', '');
$requete_1 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND type_absence_eleve = 'A' AND eleve_absence_eleve=login AND login='".$id[$i]."'";
$execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
while ( $data_1 = mysql_fetch_array($execution_1))
      {
      //tableau des absences
            $pdf->SetFont('Arial','',9.5);
            $debut = date_frc($data_1['d_date_absence_eleve'])." à ".heure($data_1['d_heure_absence_eleve']);
            $pdf->Cell(55, 5, $debut, 0, 0, '', '');
            $fin = date_frc($data_1['a_date_absence_eleve'])." à ".heure($data_1['a_heure_absence_eleve']);
            $pdf->Cell(55, 5, $fin, 0, 0, '', '');
            if ($data_1['type_absence_eleve'] == 'A') {$pour = "Absence"; }
            if ($data_1['type_absence_eleve'] == 'R') {$pour = "Retard"; }
            if ($data_1['type_absence_eleve'] == 'D') {$pour = "Dispence"; }
            if ($data_1['type_absence_eleve'] == 'I') {$pour = "Infirmerie"; }
            $pdf->Cell(22, 5, $pour, 0, 0, 'C', '');
            $pdf->Cell(54, 5, '', 0, 1, 'C', '');
      }

            $pdf->Cell(54, 5, '', 0, 1, 'C', '');
            $pdf->Cell(55, 5, 'Les Retard', 0, 1, '', '');
            $pdf->Cell(55, 5, 'Le', 1, 0, '', '');
            $pdf->Cell(131, 5, 'le motif spécifiée', 1, 1, 'C', '');
$requete_2 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND type_absence_eleve = 'R' AND eleve_absence_eleve=login AND login='".$id[$i]."'";
$execution_2 = mysql_query($requete_2) or die('Erreur SQL !'.$requete_2.'<br />'.mysql_error());
while ( $data_2 = mysql_fetch_array($execution_2))
      {
      //tableau des absences
            $pdf->SetFont('Arial','',9.5);
            $debut = date_frc($data_2['d_date_absence_eleve'])." à ".heure($data_2['d_heure_absence_eleve']);
            $pdf->Cell(55, 5, $debut, 0, 0, '', '');
            $pdf->Cell(131, 5, '', 0, 1, 'C', '');
      }

            $pdf->Cell(54, 5, '', 0, 1, 'C', '');
            $pdf->Cell(55, 5, 'Les Dispenses', 0, 1, '', '');
            $pdf->Cell(55, 5, 'Du', 1, 0, '', '');
            $pdf->Cell(55, 5, 'Au', 1, 0, '', '');
            $pdf->Cell(76, 5, 'le motif spécifiée', 1, 1, 'C', '');
$requete_3 ="SELECT * FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE ((d_date_absence_eleve >= '".date_sql($du)."' AND d_date_absence_eleve <= '".date_sql($au)."') OR (a_date_absence_eleve >= '".date_sql($du)."' AND a_date_absence_eleve <= '".date_sql($au)."')) AND type_absence_eleve = 'D' AND eleve_absence_eleve=login AND login='".$id[$i]."'";
$execution_3 = mysql_query($requete_3) or die('Erreur SQL !'.$requete_3.'<br />'.mysql_error());
while ( $data_3 = mysql_fetch_array($execution_3))
      {
      //tableau des absences
            $pdf->SetFont('Arial','',9.5);
            $debut = date_frc($data_3['d_date_absence_eleve']);
            $pdf->Cell(55, 5, $debut, 0, 0, '', '');
            $fin = date_frc($data_3['a_date_absence_eleve']);
            $pdf->Cell(55, 5, $fin, 0, 0, '', '');
            $pdf->Cell(76, 5, $data_3['info_justify_absence_eleve'], 0, 1, 'C', '');
      }



$pdf->SetY(250);
if(substr($civilite_cpe[$i],0,1) == "M" OR substr($civilite_cpe[$i],0,1) == "" ) { $nomine = 'Le conseillé Principal d\'Education'; }
if(substr($civilite_cpe[$i],0,2) == "Mm") { $nomine = 'La conseillère Principal d\'Education'; }
if(substr($civilite_cpe[$i],0,2) == "Ml") { $nomine = 'La conseillère Principal d\'Education'; }
$pdf->Cell(0, 5, $nomine, 0, 1, 'R', '');
$pdf->Cell(0, 5, $civilite_cpe[$i]." ".substr($prenom_cpe[$i],0,1).". ".$nom_cpe[$i], 0, 1, 'R', '');
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
}

// Et on affiche le pdf généré... (ou on le sauvegarde en local)
// $pdf->Output(); pour afficher sur votre browser

$nom_lettre=date("Ymd_Hi");
$nom_lettre='Bilan_'.$nom_lettre.'.pdf';
$pdf->Output($nom_lettre,'I');



?>
