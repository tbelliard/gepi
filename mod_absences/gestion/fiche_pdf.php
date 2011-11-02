<?php
/*
* $Id: fiche_pdf.php 4878 2010-07-24 13:54:01Z regis $
*
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

$mode_utf8_pdf=getSettingValue('mode_utf8_abs_pdf');
if($mode_utf8_pdf!="y") {$mode_utf8_pdf="";}

require('../../fpdf/fpdf.php');
require('../../fpdf/ex_fpdf.php');

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

//permet de transformer les caractère html
 function unhtmlentities($chaineHtml) {
         $tmp = get_html_translation_table(HTML_ENTITIES);
         $tmp = array_flip ($tmp);
         $chaineTmp = strtr ($chaineHtml, $tmp);

         return $chaineTmp;
 }

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data, $respect_lock = true)
    {
        // Open the file for writing
        $fh = @fopen($filename, 'w');
        if ($fh === false) {
            return false;
        }


       // Check to see if we want to make sure the file is locked before we write to it
        if ($respect_lock === true && !flock($fh, LOCK_EX)) {
            fclose($fh);
            return false;
        }

        // Convert the data to an acceptable string format
        if (is_array($data)) {
            $data = implode('', $data);
        } else {
            $data = (string) $data;
        }

        // Write the data to the file and close it
        $bytes = fwrite($fh, $data);

        // This will implicitly unlock the file if it's locked
        fclose($fh);
        return $bytes;
    }
}

// fonction temps
function microtime_float_img() {
    return array_sum(explode(' ', microtime()));
}

//variable de session
	if(!empty($_SESSION['eleve_multiple'][0]) and $_SESSION['eleve_multiple'] != '')
	 { $id_eleve = $_SESSION['eleve_multiple']; unset($_SESSION['classe_multiple']); } else { unset($_SESSION["eleve_multiple"]); }
	if(!empty($_SESSION['classe_multiple'][0]) and $_SESSION['classe_multiple'][0] != '')
	 { $id_classe = $_SESSION['classe_multiple']; } else { unset($_SESSION['classe_multiple']); }

	if (empty($_GET['du']) and empty($_POST['du'])) { $du = ''; }
	   else { if (isset($_GET['du'])) { $du = $_GET['du']; } if (isset($_POST['du'])) { $du = $_POST['du']; } }
	if (empty($_GET['au']) and empty($_POST['au'])) { $au = ''; }
	   else { if (isset($_GET['au'])) { $au = $_GET['au']; } if (isset($_POST['au'])) { $au = $_POST['au']; } }
	if ( empty($au) or $au === 'JJ/MM/AAAA') { $au = $du; }
// $du = '01/09/2006';
// $au = '01/06/2007';
	$du_explose = explode('/',$du);
	$au_explose = explode('/',$au);
		$jour_du = '1';
		$mois_du = $du_explose[1];
		$annee_du = $du_explose[2];
		$jour_au = '31';
		$mois_au = $au_explose[1];
		$annee_au = $au_explose[2];
		$mois= '';
		$du_sql = $annee_du.'-'.$mois_du.'-'.$du_explose[0];
		$au_sql = $annee_au.'-'.$mois_au.'-'.$au_explose[0];

// variable fixe
	// global
	$caractere_utilse = 'Arial';

	// cadre de datation du bulletin
	$active_bloc_datation = '1'; // fait - afficher les informations de datation du bulletin
	$X_datation_bul = '110';
	$Y_datation_bul = '5';
	$cadre_datation_bul = '1';

	// cadre identitée eleve
	$active_bloc_eleve = '1'; // fait - afficher les informations sur l'élève
	$X_eleve = '5'; $Y_eleve = '40';
	$cadre_eleve = '1';
	$active_photo = '1';
	$affiche_doublement = '1'; // affiche si l'élève à doubler
	$affiche_date_naissance = '1'; // affiche la date de naissance de l'élève
	$affiche_dp = '1'; // affiche l'état de demi pension ou extern
	$affiche_nom_court = '0'; // affiche le nom court de la classe
	$affiche_effectif_classe = '0'; // affiche l'effectif de la classe
	$affiche_numero_impression = '1'; // affiche le numéro d'impression des bulletins

	// datation
	$gepiYear = getSettingValue('gepiYear');
	$annee_scolaire = $gepiYear;
	$date_bulletin=date("d/m/Y H:i");
	$nom_bulletin=date("Ymd_Hi");

	//graphique
	$active_graphique = '1';



$etiquette_action = 'originaux';

if ( $etiquette_action === 'originaux' ) {
	// on sélectionne les informations
	// sql sélection des eleves et de leurs informations
	//requête des classes sélectionné
	if (isset($id_classe[0])) {
		$o=0;
		$prepa_requete = "";
		while(!empty($id_classe[$o]))
		{
			if($o == "0") {
				$prepa_requete = 'ec.id_classe = "'.$id_classe[$o].'"';
			} elseif($o != "0") {
				$prepa_requete = $prepa_requete.' OR ec.id_classe = "'.$id_classe[$o].'" ';
			}
			$o = $o + 1;
		}
	}
	//requête des élèves sélectionné
	if (!empty($id_eleve[0])) {
		$o=0;
		$prepa_requete = "";

		while(!empty($id_eleve[$o]))
		{
			if($o == "0") {
				$prepa_requete = 'e.login = "'.$id_eleve[$o].'"';
			} elseif($o != "0") {
				$prepa_requete = $prepa_requete.' OR e.login = "'.$id_eleve[$o].'" ';
			}
			$o = $o + 1;
		}
	}

	//tableau des données élève
	if (isset($id_classe[0])) {
		$call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves e, '.$prefix_base.'j_eleves_classes ec, '.$prefix_base.'classes c, '.$prefix_base.'j_eleves_regime er WHERE ( ('.$prepa_requete.') AND ec.id_classe = c.id AND e.login = ec.login AND er.login = e.login ) GROUP BY e.login ORDER BY ec.id_classe ASC, e.nom ASC, e.prenom ASC');
	}
	if (isset($id_eleve[0])) {
		$call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves e, '.$prefix_base.'j_eleves_classes ec, '.$prefix_base.'classes c, '.$prefix_base.'j_eleves_regime er WHERE ( ('.$prepa_requete.') AND ec.id_classe = c.id AND e.login = ec.login AND er.login = e.login ) GROUP BY e.login ORDER BY ec.id_classe ASC, e.nom ASC, e.prenom ASC');
	}

	//on compte les élèves sélectionné
	$nb_eleves = mysql_num_rows($call_eleve);
	$i = '0';
	while ( $donne_persone = mysql_fetch_array( $call_eleve ))
	{
		// information sur l'élève
		$id_eleve[$i] = $donne_persone['login']; // id de l'élève
		$ele_id_eleve[$i] = $donne_persone['ele_id']; // ele_id de l'élève
		$classe_eleve[$i] = classe_de($id_eleve[$i]);
		$sexe_eleve[$i] = $donne_persone['sexe']; // M ou F
		$nom_eleve[$i] = strtoupper($donne_persone['nom']); // nom de l'élève
		$prenom_eleve[$i] = ucfirst($donne_persone['prenom']); // prénom de l'élève
		if ($sexe_eleve[$i] == "M") {
			$date_naissance[$i] = 'Né le '.date_fr($donne_persone['naissance']);
		} else {
			$date_naissance[$i] = 'Née le '.date_fr($donne_persone['naissance']);
		}
			$classe_id_eleve[$i] = $donne_persone['id'];
			$classe_nomlong_eleve[$i] = $donne_persone['nom_complet'];
			$classe_nomcour_eleve[$i] = $donne_persone['classe'];

        $nom_photo = nom_photo(strtolower($donne_persone['elenoet']),"eleves",2);
        //if ($nom_photo != ""){
        if ($nom_photo){
			//$photo_eleve[$i] = "../../photos/eleves/".$nom_photo;
			$photo_eleve[$i] = $nom_photo;
		}else{
			$photo_eleve[$i] = '';
		}
        if ((!(file_exists($photo_eleve[$i]))) or ($nom_photo == "")) {
			$photo_eleve[$i] = "";
		}
        $doublement_eleve[$i]='';
		if($donne_persone['doublant']==='R') {  if($sexe_eleve[$i]==='M') { $doublement[$i]='doublant'; } else { $doublement[$i]='doublante'; } }
		if($donne_persone['regime']==='d/p') { $dp_eleve[$i]='demi-pensionnaire'; }
		if($donne_persone['regime']==='ext.') { $dp_eleve[$i]='externe'; }
		if($donne_persone['regime']==='int.') { $dp_eleve[$i]='interne'; }
		if($donne_persone['regime']==='i-e') { if($sexe_eleve[$i]==='M') { $dp_eleve[$i]='interne externé'; } else { $dp_eleve[$i]='interne externée'; } }
		if($donne_persone['regime']!='ext.' and $donne_persone['regime']!='d/p' and $donne_persone['regime']==='int.' and $donne_persone['regime']==='i-e') { $dp_eleve[$i]='inconnu'; }

		$i = $i + 1;
	}
}

// REQUETE SQL SUR LES PERIODES (HORAIRE)
	$i = '0';
	$requete_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux WHERE suivi_definie_periode = "1" ORDER BY heuredebut_definie_periode ASC';
        $execution_periode = mysql_query($requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysql_error());
	while ( $donnee_periode = mysql_fetch_array( $execution_periode ) ) {
		$Horaire[$i] = heure_texte_court($donnee_periode['heuredebut_definie_periode']).' - '.heure_texte_court($donnee_periode['heurefin_definie_periode']);
		$HorDeb[$i] = $donnee_periode['heuredebut_definie_periode'];
		$HorFin[$i] = $donnee_periode['heurefin_definie_periode'];
	$i = $i + 1;
	}

	if ( $i === '0' ) {
		$i = '0';
		$Horaire = Array(0 => "8h-9h", "9h-10h", "10h-11h", "11h-12h", "12h-13h", "13h-14h", "14h-15h", "15h-16h", "16h-17h", "17h-18h","18h-19h");
		$HorDeb = Array(0 => "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00","18:00:00");
		$HorFin = Array(0 => "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00");
	}

// REQUETE SQL POUR LES HORAIRES D'OUVERTURE
	$semaine_horaire = ouverture();


class fiche_PDF extends FPDF
{

/**
* Draws text within a box defined by width = w, height = h, and aligns
* the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
* Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
* drawTextBox uses drawRows
*
* This function is provided by TUFaT.com
*/
function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=1)
{
    $xi=$this->GetX();
    $yi=$this->GetY();

    $hrow=$this->FontSize;
    $textrows=$this->drawRows($w,$hrow,$strText,0,$align,0,0,0);
    $maxrows=floor($h/$this->FontSize);
    $rows=min($textrows,$maxrows);

    if ($border==1)
        $this->Rect($xi,$yi,$w,$h,'D');
    if ($border==2)
        $this->Rect($xi,$yi,$w,$h,'DF');

    $dy=0;
    if (strtoupper($valign)=='M')
        $dy=($h-$rows*$this->FontSize)/2;
    if (strtoupper($valign)=='B')
        $dy=$h-$rows*$this->FontSize;

    $this->SetY($yi+$dy);
    $this->SetX($xi);

    $this->drawRows($w,$hrow,$strText,0,$align,0,$rows,1);

}

function drawRows($w,$h,$txt,$border=0,$align='J',$fill=0,$maxline=0,$prn=0)
{
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $b=0;
    if($border)
    {
        if($border==1)
        {
            $border='LTRB';
            $b='LRT';
            $b2='LR';
        }
        else
        {
            $b2='';
            if(is_int(strpos($border,'L')))
                $b2.='L';
            if(is_int(strpos($border,'R')))
                $b2.='R';
            $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
        }
    }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $ns=0;
    $nl=1;
    while($i<$nb)
    {
        //Get next character
        $c=$s[$i];
        if($c=="\n")
        {
            //Explicit line break
            if($this->ws>0)
            {
                $this->ws=0;
                if ($prn==1) $this->_out('0 Tw');
            }
            if ($prn==1) {
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border and $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
            continue;
        }
        if($c==' ')
        {
            $sep=$i;
            $ls=$l;
            $ns++;
        }
        $l+=$cw[$c];
        if($l>$wmax)
        {
            //Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws=0;
                    if ($prn==1) $this->_out('0 Tw');
                }
                if ($prn==1) {
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
            }
            else
            {
                if($align=='J')
                {
                    $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    if ($prn==1) $this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
                }
                if ($prn==1){
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                }
                $i=$sep+1;
            }
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border and $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
        }
        else
            $i++;
    }
    //Last chunk
    if($this->ws>0)
    {
        $this->ws=0;
        if ($prn==1) $this->_out('0 Tw');
    }
    if($border and is_int(strpos($border,'B')))
        $b.='B';
    if ($prn==1) {
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    }
    $this->x=$this->lMargin;
    return $nl;
}

function TextWithDirection($x,$y,$txt,$direction='R')
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
    if ($direction=='R')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='L')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='U')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='D')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    else
        $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
    if ($this->ColorFlag)
        $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

    $font_angle+=90+$txt_angle;
    $txt_angle*=M_PI/180;
    $font_angle*=M_PI/180;

    $txt_dx=cos($txt_angle);
    $txt_dy=sin($txt_angle);
    $font_dx=cos($font_angle);
    $font_dy=sin($font_angle);

    $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
             $txt_dx,$txt_dy,$font_dx,$font_dy,
             $x*$this->k,($this->h-$y)*$this->k,$txt);
    if ($this->ColorFlag)
        $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

    //En-tête du document
    function Header()
    {
	    global $prefix_base;
			$X_etab = '10'; $Y_etab = '10';
		        $caractere_utilse = 'Arial';
			$affiche_filigrame='0'; // affiche un filigramme
			$texte_filigrame='DOCUMENT DE TEST'; // texte du filigrame
			$affiche_logo_etab='1';
			$entente_mel='0'; // afficher l'adresse mel dans l'entête
			$entente_tel='0'; // afficher le numéro de téléphone dans l'entête
			$entente_fax='0'; // afficher le numéro de fax dans l'entête
			$L_max_logo=75; $H_max_logo=75; //dimension du logo

    //Affiche le filigrame
    if($affiche_filigrame==='1')
     {
      $this->SetFont('Arial','B',50);
      $this->SetTextColor(255,192,203);
      $this->TextWithRotation(40,190,$texte_filigrame,45);
      $this->SetTextColor(0,0,0);
     }

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
	  //$gepiSchoolName = utf8_decode(getSettingValue('gepiSchoolName'));
	  //$gepiSchoolName = utf8_encode(getSettingValue('gepiSchoolName'));
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
        //Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
   	$this->SetXY(5,-10);
        //Police Arial Gras 6
        $this->SetFont('Arial','B',8);
        $this->Cell(0,4.5, traite_accents_utf8("Fiche récapitulative des absences - GEPI : solution libre de gestion et de suivi des résultats scolaires."),0,0,'C');
    }
}

define('PARAGRAPH_STRING', '~~~');
define('FPDF_FONTPATH','../../fpdf/font/');
require_once("../../fpdf/class.multicelltag.php");


// mode paysage, a4, etc.
$pdf=new fiche_PDF('P','mm','A4');

$pdf->Open();
$pdf->SetAutoPageBreak(false);

$cpt_eleve = '0';
while ( !empty($id_eleve[$cpt_eleve]) ) {

$pdf->AddPage();

$pdf->SetFont('arial','',10);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);


// bloc affichage information sur l'élèves
	if ( $active_bloc_eleve === '1' ) {
	 $pdf->SetXY($X_eleve,$Y_eleve);
 	 $pdf->SetFont($caractere_utilse,'B',14);
	 $longeur_cadre_eleve = $pdf->GetStringWidth($nom_eleve[$cpt_eleve]." ".$prenom_eleve[$cpt_eleve]);
	 $rajout_cadre_eleve = 100 - $longeur_cadre_eleve;
	 $longeur_cadre_eleve = $longeur_cadre_eleve + $rajout_cadre_eleve;
	 $nb_ligne = '5'; $hauteur_ligne = '6';
	 $hauteur_cadre_eleve = $nb_ligne*$hauteur_ligne;
	 if ( $cadre_eleve != 0 ) { $pdf->Rect($X_eleve, $Y_eleve, $longeur_cadre_eleve, $hauteur_cadre_eleve, 'D'); }
	 $X_eleve_2 = $X_eleve;
	 $Y_eleve_2 = $Y_eleve;

		//photo de l'élève
	 	if ( $active_photo === '1' and $photo_eleve[$cpt_eleve]!='' and file_exists($photo_eleve[$cpt_eleve]) ) {
		 $L_photo_max=$hauteur_cadre_eleve*2.8; $H_photo_max=$hauteur_cadre_eleve*2.8;
		 $valeur=redimensionne_logo($photo_eleve[$cpt_eleve], $L_photo_max, $H_photo_max);
		 $X_photo=$X_eleve+0.20; $Y_photo=$Y_eleve+0.25; $L_photo=$valeur[0]; $H_photo=$valeur[1];
		 $X_eleve_2=$X_eleve+$L_photo; $Y_eleve_2=$Y_photo;
	         $pdf->Image($photo_eleve[$cpt_eleve], $X_photo, $Y_photo, $L_photo, $H_photo);
		}

 	 $pdf->SetXY($X_eleve_2,$Y_eleve_2);
	 $pdf->Cell(90,7, traite_accents_utf8($nom_eleve[$cpt_eleve]." ".$prenom_eleve[$cpt_eleve]),0,2,'');
	 $pdf->SetFont($caractere_utilse,'',10);
	 if ( $affiche_date_naissance === '1' ) {
	  if($date_naissance[$cpt_eleve]!="") { $pdf->Cell(90,5, traite_accents_utf8($date_naissance[$cpt_eleve]),0,2,''); }
	 }
	 if( $affiche_dp === '1' ) {
	  if( $dp_eleve[$cpt_eleve] != '' ) { $pdf->Cell(90,4, traite_accents_utf8($dp_eleve[$cpt_eleve]),0,2,''); }
	 }
	 if ( $affiche_doublement === '1' ) {
	  if($doublement_eleve[$cpt_eleve]!="") { $pdf->Cell(90,4.5, $doublement_eleve[$cpt_eleve],0,2,''); }
	 }
	 if( $affiche_nom_court === '1' ) {
	  if($classe_nomcour_eleve[$cpt_eleve]!="") { $pdf->Cell(90,4.5, traite_accents_utf8(unhtmlentities($classe_nomcour_eleve[$cpt_eleve])),0,2,''); }
	 }
	 if ( $affiche_effectif_classe === '1' ) {
	  if ( $info_bulletin[$ident_eleve_aff][$id_periode]['effectif']!="") {
		$pdf->Cell(45,4.5, traite_accents_utf8('Effectif : '.$info_bulletin[$ident_eleve_aff][$id_periode]['effectif'].' élèves'),0,0,''); }
	 }
	 if ( $affiche_numero_impression === '1' ) {
	  $num_ordre = $cpt_eleve;
	  $pdf->Cell(45,4, 'Impression N° '.$num_ordre,0,2,'');
	 }
	}

	// bloc affichage datation du bulletin
	if($active_bloc_datation==='1') {
 	 $pdf->SetXY($X_datation_bul, $Y_datation_bul);
 	 $pdf->SetFont($caractere_utilse,'B',14);
	 $longeur_cadre_datation_bul = 95;
	 $nb_ligne_datation_bul = 3; $hauteur_ligne_datation_bul = 6;
	 $hauteur_cadre_datation_bul = $nb_ligne_datation_bul*$hauteur_ligne_datation_bul;
	 if($cadre_datation_bul!=0) { $pdf->Rect($X_datation_bul, $Y_datation_bul, $longeur_cadre_datation_bul, $hauteur_cadre_datation_bul, 'D'); }
	 $pdf->Cell(90,7, "Classe de ".traite_accents_utf8(unhtmlentities($classe_nomlong_eleve[$cpt_eleve])),0,2,'C');
	 $pdf->SetFont($caractere_utilse,'',12);
	 $pdf->Cell(90,5, traite_accents_utf8("Année scolaire ".$annee_scolaire),0,2,'C');
	 $pdf->SetFont($caractere_utilse,'',10);
	 $pdf->Cell(90,5, traite_accents_utf8("Fiche récapitulative des absences"),0,2,'C');
	 $pdf->SetFont($caractere_utilse,'',8);
	 $pdf->Cell(95,7, $date_bulletin,0,2,'R');
	 $pdf->SetFont($caractere_utilse,'',10);
	}


// bloc d'information divers
	// nombre de lettre expédié à la famille
	// nombre de lettre resté sans réponse
	// nombre d'avertissement
	// nombre d'exclusion
	// nombre de retenue


	// placement en x du tableau
	$x_divers = '110';
	// placement en y du tableau
	$y_divers = '40';
	// hauteur des lignes
	$h_divers = '5';
	// largeur de la colonne titre à gauche
	$l_divers = '35';

	// placement du bloc
 	$pdf->SetXY($x_divers,$y_divers);
	// nombre de lettre expédié à la famille
        $cpt_lettre_envoye = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$id_eleve[$cpt_eleve]."'"),0);
	$pdf->Cell($l_divers, $h_divers, traite_accents_utf8('Nombre de lettres expédiées : ').$cpt_lettre_envoye, 0, 2, 'L', 0);
	// nombre de lettre resté sans réponse
        $cpt_lettre_envoye_sans_reponse = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$id_eleve[$cpt_eleve]."' AND quireception_lettre_suivi = ''"),0);
	$pdf->Cell($l_divers, $h_divers, traite_accents_utf8('Lettres restées sans réponse : ').$cpt_lettre_envoye_sans_reponse, 0, 2, 'L', 0);
	// nombre d'avertissement
        $cpt_lettre_avertissement = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis, ".$prefix_base."lettres_types WHERE quirecois_lettre_suivi = '".$id_eleve[$cpt_eleve]."' AND id_lettre_type = type_lettre_suivi AND titre_lettre_type LIKE '%avertissement%'"),0);
	$pdf->Cell($l_divers, $h_divers, 'Nombre d\'avertissements : '.$cpt_lettre_avertissement, 0, 2, 'L', 0);
	// nombre d'exclusion
        $cpt_lettre_exclusion = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis, ".$prefix_base."lettres_types WHERE quirecois_lettre_suivi = '".$id_eleve[$cpt_eleve]."' AND id_lettre_type = type_lettre_suivi AND titre_lettre_type LIKE '%exclusion%'"),0);
	$pdf->Cell($l_divers, $h_divers, 'Nombre d\'exclusions : '.$cpt_lettre_exclusion, 0, 2, 'L', 0);
	// nombre de retenue
        $cpt_lettre_retenue = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."lettres_suivis, ".$prefix_base."lettres_types WHERE quirecois_lettre_suivi = '".$id_eleve[$cpt_eleve]."' AND id_lettre_type = type_lettre_suivi AND titre_lettre_type LIKE '%retenu%'"),0);
	$pdf->Cell($l_divers, $h_divers, 'Nombre de retenues : '.$cpt_lettre_retenue, 0, 2, 'L', 0);


// fin du bloc d'information divers

// tableau des absences des jours de chaque mois

$info_absence = repartire_jour($id_eleve[$cpt_eleve], 'A', $du_sql, $au_sql);
$info_retard = repartire_jour($id_eleve[$cpt_eleve], 'R', $du_sql, $au_sql);


	// on initialise le tableau des mois sélectionné
	if ( $mois === '' ) { $mois = tableau_mois($mois_du, $annee_du, $mois_au, $annee_au); }

	// placement en x du tableau
	$x_mois = '10';
	// placement en y du tableau
	$y_mois = '72.5'; $y_mois_o = $y_mois;
	// hauteur des lignes
	$h_mois = '5';
	// largeur de la colonne titre à gauche
	$l_mois = '25';
	// on compte le nombre de mois à affiché pour établire ensuite la largeur de ces colonnes
		$nb_jour = '31';
		// 210 - 10(marge gauche) - 10(marge gauche) - 35(titre) - 13(total)
		// largeur total qu'on réserve pour l'affichage des mois
		$largeur_total = '166';
		// largeur des colonnes jour
		$l_jour = $largeur_total / $nb_jour;

	// entête du tableau
	// placement en x et y
	$pdf->SetXY($x_mois, $y_mois);
	// case vide
	$pdf->Cell($l_mois, $h_mois, '', 'RB', 0, 'C', 0);
		// entête des jours
		$i = '1';
		while ( $i <= 31 ) {
			$pdf->Cell($l_jour, $h_mois, $i, 1, 0, 'C', 0);
		$i = $i + 1;
		}

	// horaire de chaque jour
	$i = '0';
	while ( !empty($mois[$i]) ) {
		$y_mois = $y_mois + $h_mois;
		$pdf->SetXY($x_mois, $y_mois);
		$pdf->Cell($l_mois, $h_mois, $mois[$i]['mois'], 'TLRB', 0, 'C', 0);
			$j = '1';
			while ( $j <= '31' ) {

				if(checkdate($mois[$i]['num_mois'], $j, $mois[$i]['num_annee'])) {
					if(aff_jour($j, $mois[$i]['num_mois'], $mois[$i]['num_annee'])!='Dimanche') { $pdf->SetFillColor(255, 255, 255); $aff_couleur = '0'; } else { $pdf->SetFillColor(140, 239, 134); $aff_couleur = '1'; }
					$cadre = 'LRTB';

					$pass = '';
					if ( $j < 10 ) { $jour_num = '0'.$j; } else { $jour_num = $j; }
					$jour_select = $mois[$i]['num_annee'].'-'.$mois[$i]['num_mois'].'-'.$jour_num;

					if ( !empty($info_absence[$jour_select.'-0']) ) {

					   // boucle pour vérifier si plusieurs horraire dans cette journé
					   $cpt_horraire_jour = 0;
					   $jour_select_tt = $jour_select.'-'.$cpt_horraire_jour;
					   while ( !empty($info_absence[$jour_select_tt]) )
					   {
						// connaitre si l'absence à été le matin
						if ( $info_absence[$jour_select_tt]['heure_debut'] >= '06:00:00' and $info_absence[$jour_select_tt]['heure_fin'] <= '13:00:00' ) {
							// on prend l'emplacement du X et du Y initial de la case
							$valeur_x_carre = $pdf->GetX();
							$valeur_y_carre = $pdf->GetY();
							// couleur de remplissage
							$pdf->SetFillColor(0, 0, 0); $aff_couleur = '0';
							$pdf->SetTextColor(255, 255, 255);
							// si matin ou aprés midi on divise par deux
							$l_jour_case = $l_jour / 2;
							// placement de la case absences
							  // si après midi on déplace la case +$l_jour_case
							$pdf->SetXY($valeur_x_carre, $valeur_y_carre);
							// cellule de l'absences
							$pdf->Cell($l_jour_case, $h_mois, 'M', $cadre, 0, 'C', 1);
							// on remet les valeurs initial
							$pdf->SetXY($valeur_x_carre, $valeur_y_carre);
							$pdf->SetTextColor(0, 0, 0);
							$pass = 'ok';
						}
						// connaitre si l'absence a été l'après-midi
						if ( $info_absence[$jour_select_tt]['heure_debut'] > '13:00:00' and $info_absence[$jour_select_tt]['heure_fin'] <= '19:00:00' ) {
							// on prend l'emplacement du X et du Y initial de la case
							$valeur_x_carre = $pdf->GetX();
							$valeur_y_carre = $pdf->GetY();
							// couleur de remplissage
							$pdf->SetFillColor(0, 0, 0); $aff_couleur = '0';
							$pdf->SetTextColor(255, 255, 255);
							// si matin ou après midi on divise par deux
							$l_jour_case = $l_jour / 2;
							// placement de la case absences
							  // si après midi on déplace la case +$l_jour_case
							$pdf->SetXY($valeur_x_carre+$l_jour_case, $valeur_y_carre);
							// cellule de l'absences
							$pdf->Cell($l_jour_case, $h_mois, 'A', $cadre, 0, 'C', 1);
							// on remet les valeurs initial
							$pdf->SetXY($valeur_x_carre, $valeur_y_carre);
							$pdf->SetTextColor(0, 0, 0);
							$pass = 'ok';
						}
						// connaitre si l'absence a été toute la journée
						if ( $info_absence[$jour_select_tt]['heure_debut'] > '06:00:00' and $info_absence[$jour_select_tt]['heure_fin'] <= '19:00:00' and $pass != 'ok' ) {
							// on prend l'emplacement du X et du Y initial de la case
							$valeur_x_carre = $pdf->GetX();
							$valeur_y_carre = $pdf->GetY();
							// couleur de remplissage
							$pdf->SetFillColor(0, 0, 0); $aff_couleur = '0';
							$pdf->SetTextColor(255, 255, 255);
							// si matin ou après midi on divise par deux
							$l_jour_case = $l_jour;
							// placement de la case absences
							  // si après midi on déplace la case +$l_jour_case
							$pdf->SetXY($valeur_x_carre, $valeur_y_carre);
							// cellule de l'absences
							$pdf->Cell($l_jour_case, $h_mois, 'MA', $cadre, 0, 'C', 1);
							// on remet les valeurs initial
							$pdf->SetXY($valeur_x_carre, $valeur_y_carre);
							$pdf->SetTextColor(0, 0, 0);
						}
					   $cpt_horraire_jour = $cpt_horraire_jour + 1;
					   $jour_select_tt = $jour_select.'-'.$cpt_horraire_jour;
					   }
					}

				 } else {
						if ( empty($mois[$i+1]) ) { $cadre = 'T'; } else { $cadre = 'TB'; } $aff_couleur = '0';
					}

				$pdf->Cell($l_jour, $h_mois, '', $cadre, 0, 'C', $aff_couleur);
			$j = $j + 1;
			}
	$i = $i + 1;
	}


// fin du tableau des absences des jours de chaque mois


// tableau annuel des absences et retards

$annuel = $mois;

	// placement en x du tableau
	$x_annuel = '10';
	// placement en y du tableau
		$nb_mois_au_dessus = count($mois);
		/*if( $nb_mois_au_dessus > '11' ) { $y_annuel = '138'; }
		 else { $y_annuel = '135.5'; }*/
		// automatique
		$y_annuel_limit = '135.5';
		$y_annuel = $y_mois_o + ($nb_mois_au_dessus * 5) + 5;
		if ( $y_annuel < $y_annuel_limit ) { $y_annuel = $y_annuel + 5; }
		$y_annuel_o = $y_annuel;

	// hauteur des lignes
	$h_annuel = '5';
	// largeur de la colonne titre à gauche
	$l_intituler = '35';
	// hauteur de la colonne titre à gauche quand il y a deux informations
	$h_intituler = $h_annuel * 2;
	// on compte le nombre de mois à affiché pour établire ensuite la largeur de ces colonnes
		$nb_mois = count($annuel);
		// 210 - 10(marge gauche) - 10(marge gauche) - 35(titre) - 13(total)
		// largeur total qu'on réserve pour l'affichage des mois
		$largeur_total = '156';
		// largeur des colonnes mois
		$l_annuel = $largeur_total / ($nb_mois+1);

	// variable vide pour la totalisation des données
	$total_absence_nb = '0';
	$total_absence_heure = '0';
	$total_retard = '0';

	// entête du tableau
	// placement en x et y
	$pdf->SetXY($x_annuel, $y_annuel);
	// case vide
	$pdf->Cell($l_intituler, $h_annuel, '', 'RB', 0, 'C', 0);
		// entête des mois
		$i = '0';
		while ( !empty($annuel[$i]) ) {
			$pdf->Cell($l_annuel, $h_annuel, $annuel[$i]['mois_court'], 1, 0, 'C', 0);
		$i = $i + 1;
		}
		// entête total
		$pdf->Cell($l_annuel, $h_annuel, 'total', 'LRT', 0, 'C', 0);


	// ligne des absences non justifé
	// décaler y par rapport à la première ligne
	$y_annuel = $y_annuel + $h_annuel;
	$pdf->SetFont('arial','',9);
	$pdf->SetXY($x_annuel, $y_annuel);
	$pdf->Cell($l_intituler, $h_intituler, traite_accents_utf8('Absence non justifiée'), 1, 0, 'L', 0);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_absence[$annee_texte.'-'.$mois_texte]) and $info_absence[$annee_texte.'-'.$mois_texte]['nb_nj'] != '0' ) { $texte = $info_absence[$annee_texte.'-'.$mois_texte]['nb_nj']; }
			$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRT', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$texte = '';
		if ( $total != '0' ) { $texte = $total; }
		$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRT', 0, 'C', 0);
			$total_absence_nb = $total_absence_nb + $total;

	$y_annuel = $y_annuel + $h_annuel;
	$x_sous_decal = $x_annuel + $l_intituler;
	$pdf->SetFont('arial','',9);
	$pdf->SetXY($x_sous_decal, $y_annuel);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = ''; $texte_h = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_absence[$annee_texte.'-'.$mois_texte]) and $info_absence[$annee_texte.'-'.$mois_texte]['nb_nj'] != '0' ) { $texte = $info_absence[$annee_texte.'-'.$mois_texte]['nb_heure_nj']; $texte_h = convert_minutes_heures($texte); }
			$pdf->Cell($l_annuel, $h_annuel, $texte_h, 'LRB', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$texte = '';
		if ( $total != '0' ) { $texte = convert_minutes_heures($total); }
		$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRB', 0, 'C', 0);
			$total_absence_heure = $total_absence_heure + $total;

	// ligne des absences justifé
	$y_annuel = $y_annuel + $h_annuel;
	$pdf->SetXY($x_annuel, $y_annuel);
	$pdf->Cell($l_intituler, $h_intituler, traite_accents_utf8('Absence justifiée'), 1, 0, 'L', 0);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_absence[$annee_texte.'-'.$mois_texte]) and $info_absence[$annee_texte.'-'.$mois_texte]['nb_j'] != '0' ) { $texte = $info_absence[$annee_texte.'-'.$mois_texte]['nb_j']; }
			$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRT', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$texte = '';
		if ( $total != '0' ) { $texte = $total; }
		$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRT', 0, 'C', 0);
			$total_absence_nb = $total_absence_nb + $total;

	$y_annuel = $y_annuel + $h_annuel;
	$x_sous_decal = $x_annuel + $l_intituler;
	$pdf->SetFont('arial','',9);
	$pdf->SetXY($x_sous_decal, $y_annuel);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = ''; $texte_h = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_absence[$annee_texte.'-'.$mois_texte]) and $info_absence[$annee_texte.'-'.$mois_texte]['nb_j'] != '0' ) { $texte = $info_absence[$annee_texte.'-'.$mois_texte]['nb_heure_j']; $texte_h = convert_minutes_heures($texte); }
			$pdf->Cell($l_annuel, $h_annuel, $texte_h, 'LRB', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$texte = '';
		if ( $total != '0' ) { $texte = convert_minutes_heures($total); }
		$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRB', 0, 'C', 0);
			$total_absence_heure = $total_absence_heure + $total;

	// ligne des retards non justifié
	$y_annuel = $y_annuel + $h_annuel;
	$pdf->SetFont('arial','',9);
	$pdf->SetXY($x_annuel, $y_annuel);
	$pdf->Cell($l_intituler, $h_annuel, traite_accents_utf8('Retard non justifié'), 1, 0, 'L', 0);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_retard[$annee_texte.'-'.$mois_texte]) ) { $texte = $info_retard[$annee_texte.'-'.$mois_texte]['nb_nj']; }
			$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRB', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$texte = '';
		if ( $total != '0' ) { $texte = $total; }
		$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRB', 0, 'C', 0);
			$total_retard = $total_retard + $total;

	// ligne des retards justifié
	$y_annuel = $y_annuel + $h_annuel;
	$pdf->SetXY($x_annuel, $y_annuel);
	$pdf->Cell($l_intituler, $h_annuel, traite_accents_utf8('Retard justifié'), 1, 0, 'L', 0);
		$i = '0'; $total = '0';
		while ( !empty($annuel[$i]) ) {
				$texte = '';
				$annee_texte = $annuel[$i]['num_annee'];
				$mois_texte = $annuel[$i]['num_mois'];
				if ( isset($info_retard[$annee_texte.'-'.$mois_texte]) ) { $texte = $info_retard[$annee_texte.'-'.$mois_texte]['nb_j']; }
			$pdf->Cell($l_annuel, $h_annuel, $texte, 'LRB', 0, 'C', 0);
			$total = $total + $texte;
		$i = $i + 1;
		}
		// total
		$pdf->Cell($l_annuel, $h_annuel, $total, 'LRB', 0, 'C', 0);
			$total_retard = $total_retard + $total;

	// ligne sous le tableau qui affiche les totaux
	$y_annuel = $y_annuel + $h_annuel;
	$pdf->SetXY($x_annuel, $y_annuel);
	$pdf->SetFont('arial','',10);

// a changer par la suite corrige une erreur
//$total_absence_heure = my_eregi_replace("[-]{1}",'',$total_absence_heure);

	$pdf->Cell(0, $h_annuel, 'Total des absences : '.$total_absence_nb.', Total des absences en heure : '.convert_minutes_heures($total_absence_heure).', Total des retards : '.$total_retard, 0, 0, 'C', 0);
// fin du tableau annuel des absences et retards

// tableau des nombre d'absences par jour et par heure (période)
$i = '0';
if ( isset($semaine_horaire['lundi']['ouverture']) ) { $semaine[$i]['jour'] = 'lundi'; $i = $i + 1; }
if ( isset($semaine_horaire['mardi']['ouverture']) ) { $semaine[$i]['jour'] = 'mardi'; $i = $i + 1; }
if ( isset($semaine_horaire['mercredi']['ouverture']) ) { $semaine[$i]['jour'] = 'mercredi'; $i = $i + 1; }
if ( isset($semaine_horaire['jeudi']['ouverture']) ) { $semaine[$i]['jour'] = 'jeudi'; $i = $i + 1; }
if ( isset($semaine_horaire['vendredi']['ouverture']) ) { $semaine[$i]['jour'] = 'vendredi'; $i = $i + 1; }
if ( isset($semaine_horaire['samedi']['ouverture']) ) { $semaine[$i]['jour'] = 'samedi'; $i = $i + 1; }

	// placement en x du tableau
	$x_semaine = '10';
	// placement en y du tableau
	$y_semaine = '178.5';
	// hauteur des lignes
	$h_semaine = '5';
	// largeur de la colonne titre à gauche
	$l_horaire = '25';
	// on compte le nombre de mois à affiché pour établire ensuite la largeur de ces colonnes
		$nb_jour = count($semaine);
		// 210 - 10(marge gauche) - 10(marge gauche) - 35(titre) - 13(total)
		// largeur total qu'on réserve pour l'affichage des mois
		$largeur_total = '70';
		// largeur des colonnes jour
		$l_semaine = $largeur_total / $nb_jour;

	// entête du tableau
	// placement en x et y
	$pdf->SetXY($x_semaine, $y_semaine);
	// case vide
	$pdf->Cell($l_horaire, $h_semaine, 'ABSENCES', 'RB', 0, 'C', 0);
		// entête des jours
		$i = '0';
		while ( !empty($semaine[$i]) ) {
			$pdf->SetFont('arial','',9);
			$pdf->Cell($l_semaine, $h_semaine, $semaine[$i]['jour'], 1, 0, 'C', 0);
			$pdf->SetFont('arial','',10);
		$i = $i + 1;
		}

	// horaire de chaque jour
	$i = '0';
	$icouleur = '1';
	$aff_chiffre = '0';
		$tab_jour['lundi'] = '1';
		$tab_jour['mardi'] = '2';
		$tab_jour['mercredi'] = '3';
		$tab_jour['jeudi'] = '4';
		$tab_jour['vendredi'] = '5';
		$tab_jour['samedi'] = '6';
		$tab_jour['dimanche'] = '0';

		// calcul du nombre de période à affiché dans la semaine
		$maxHor = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."edt_creneaux
							WHERE suivi_definie_periode = '1'"),0);

		// si il est égale à 0 alors on l'initialise à 11
		if ( $maxHor === '0' or $maxHor > '11' ) { $maxHor = '11'; }

	while ($i < $maxHor) {
		$y_semaine = $y_semaine + $h_semaine;
		$pdf->SetXY($x_semaine, $y_semaine);
		$pdf->Cell($l_horaire, $h_semaine, $Horaire[$i], 'TLRB', 0, 'C', 0);
			$j = '0';
			while ( !empty($semaine[$j]) ) {
				$jour_select = $semaine[$j]['jour'];
				$aff_chiffre = '';
				if ( isset($semaine_horaire[$jour_select]['ouverture']) and $semaine_horaire[$jour_select]['fermeture'] > $HorDeb[$i] ) {
					if ( !empty($info_absence) ) { $aff_chiffre = absence_fiche($tab_jour[$jour_select], $HorDeb[$i], $HorFin[$i], $info_absence); }
					$pdf->Cell($l_semaine, $h_semaine, $aff_chiffre, 1, 0, 'C', 0);
				} else { $pdf->Cell($l_semaine, $h_semaine, '', 'LR', 0, 'C', 0); }
			$j = $j + 1;
			}
	$i = $i + 1;
	}

// fin du tableau des nombre d'absences par jour et par heure (période)

// tableau des nombre de retards par jour et par heure (période)
$i = '0';

	// placement en x du tableau
	$x_semaine = '107';
	// placement en y du tableau
	$y_semaine = '178.5';
	// hauteur des lignes
	$h_semaine = '5';
	// largeur de la colonne titre à gauche
	$l_horaire = '25';
	// on compte le nombre de mois à affiché pour établire ensuite la largeur de ces colonnes
		$nb_jour = count($semaine);
		// 210 - 10(marge gauche) - 10(marge gauche) - 35(titre) - 13(total)
		// largeur total qu'on réserve pour l'affichage des mois
		$largeur_total = '69';//77.5
		// largeur des colonnes jour
		$l_semaine = $largeur_total / $nb_jour;

	// entête du tableau
	// placement en x et y
	$pdf->SetXY($x_semaine, $y_semaine);
	// case vide
	$pdf->Cell($l_horaire, $h_semaine, 'RETARDS', 'RB', 0, 'C', 0);
		// entête des jours
		$i = '0';
		while ( !empty($semaine[$i]) ) {
			$pdf->SetFont('arial','',9);
			$pdf->Cell($l_semaine, $h_semaine, $semaine[$i]['jour'], 1, 0, 'C', 0);
			$pdf->SetFont('arial','',10);
		$i = $i + 1;
		}

	// horaire de chaque jour
	$i = '0';

	$icouleur = '1';
	$aff_chiffre = '0';
	while ($i < $maxHor) {
		$y_semaine = $y_semaine + $h_semaine;
		$pdf->SetXY($x_semaine, $y_semaine);
		$pdf->Cell($l_horaire, $h_semaine, $Horaire[$i], 'TLRB', 0, 'C', 0);
			$j = '0';
			while ( !empty($semaine[$j]) ) {
				$jour_select = $semaine[$j]['jour'];
				$aff_chiffre = '';
				if ( isset($semaine_horaire[$jour_select]['ouverture']) and $semaine_horaire[$jour_select]['fermeture'] > $HorDeb[$i] ) {
					if ( !empty($info_retard) ) { $aff_chiffre = retard_fiche($tab_jour[$jour_select], $HorDeb[$i], $HorFin[$i], $info_retard); }
					$pdf->Cell($l_semaine, $h_semaine, $aff_chiffre, 1, 0, 'C', 0);
				} else { $pdf->Cell($l_semaine, $h_semaine, '', 'LR', 0, 'C', 0); }
			$j = $j + 1;
			}
	$i = $i + 1;
	}

// fin du tableau des nombre d'absences par jour et par heure (période)

// le graphique
$test = 'on';
if ( $active_graphique === '1' AND $test == 'on') {
	// placement en x du graphique
	$x_graphique = '10';
	// placement en y du graphique
	$y_graphique = '240';
	// hauteur du graphique au maximum
	$h_graphique = '45';
	// largeur du graphique au maximum
	$l_graphique = '96';

	$l_graphique_max = $l_graphique * 2.8;
	$h_graphique_max = $h_graphique * 2.8;

	//préparation des valeurs
	// axe x
	if ( !isset($valeur_x) ) {
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$valeur_x[$i] = $mois[$i]['mois_court'];
		$i = $i + 1;
		}
		$_SESSION['axe_x'] = $valeur_x;
	}
	// axe y des absences et des retards
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$mois_p = $mois[$i]['num_mois'];
			$annee_p = $mois[$i]['num_annee'];
				$total_abs = '0';
				if ( isset($info_absence[$annee_p.'-'.$mois_p]) and ($info_absence[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_absence[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb'];
				}
				$total_ret = '0';
				if ( isset($info_retard[$annee_p.'-'.$mois_p]) and ($info_retard[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_retard[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb'];
				}
			$valeur_y_abs[$i] = $total_abs;
			$valeur_y_ret[$i] = $total_ret;
		$i = $i + 1;
		}
		$_SESSION['axe_y_abs'] = $valeur_y_abs;
		$_SESSION['axe_y_ret'] = $valeur_y_ret;

	// nom du fichier en fonction du temps
	$temps_debut = microtime_float_img();
	$nom_fichier = md5($temps_debut);
	$_SESSION['nom_fichier_png'] = $nom_fichier;
	$graphique = '../../documents/'.$nom_fichier.'.png';

	// génération du graphique
	include('../lib/graph_double_ligne.php');

	$valeur=redimensionne_logo($graphique, $l_graphique_max, $h_graphique_max);

	// hauteur du graphique définie
	$h_graphique = $valeur[1];
	// largeur du graphique définie
	$l_graphique = $valeur[0];

	// le graphique
	//$pdf->Image($graphique, $x_graphique, $y_graphique, $l_graphique, $h_graphique, 'PNG');
	$pdf->Image($graphique, $x_graphique, $y_graphique, 96, 45, 'PNG');

	// supprimer le graphique temporaire
	unlink($graphique);
}
// fin du graphique

// cadre observation
	// placement en x du tableau
	$x_observation = '107';
	// placement en y du tableau
	$y_observation = '240';
	// hauteur des lignes
	$h_observation = '5';
	// largeur de la colonne titre à gauche
	$l_observation = '94';

	$pdf->SetXY($x_observation, $y_observation);
	$pdf->Cell($l_observation, $h_observation, 'Observation :', 1, 0, 'C', 0);
	$y_observation = $y_observation + $h_observation;
	$pdf->SetXY($x_observation, $y_observation);
	$h_observation = '39';
	$pdf->Cell($l_observation, $h_observation, '', 1, 0, 'C', 0);
// fin du cadre observation

$cpt_eleve = $cpt_eleve + 1;
}

$pdf->Output('fiche_recap_abs_'.date("Ymd_Hi").'.pdf','I');
?>
