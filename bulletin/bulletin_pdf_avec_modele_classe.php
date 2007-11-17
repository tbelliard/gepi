<?php
/*
*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
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

header('Content-Type: application/pdf');

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');

require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');
require_once("../fpdf/class.multicelltag.php");

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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
Header('Pragma: public');


//variable de session
if(!empty($_SESSION['eleve'][0]) and $_SESSION['eleve'] != '') {
	$id_eleve = $_SESSION['eleve'];
	//modif ERIC pour permettre l'affichage des élèves.
	//unset($_SESSION['classe']);
} else {
	unset($_SESSION["eleve"]);
}
if(!empty($_SESSION['classe'][0]) and $_SESSION['classe'][0] != '') {
	$id_classe = $_SESSION['classe']; }
else {
	unset($_SESSION['classe']);
}

$periode = $_SESSION['periode'];
$periode_ferme = $_SESSION['periode_ferme'];
$model_bulletin = $_SESSION['type_bulletin'];

$type_bulletin = $model_bulletin; // le modèle sélectionné dans le menu deroulant

$RneEtablissement=getSettingValue("gepiSchoolRne");

$tri_par_etab_origine = $_SESSION['tri_par_etab_origine']; //est ce que l'on trie les bulletins par etablissement d'origine ?

	/*
	if(getSettingValue('bull_pdf_INE_eleve')=='y') {
		$bull_pdf_INE_eleve="y";
	}
	else{
		$bull_pdf_INE_eleve="n";
	}
	*/

function redimensionne_image($photo, $L_max, $H_max)
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

//fonction calcul des moyennes de groupe [moyenne | moyenne mini | moyenne maxi]
function calcul_toute_moyenne_classe ($groupe_select, $periode_select)
{
	global $prefix_base;

	$addition_des_notes=0; $note=0; $cpt_notes=0; $moyenne_mini=20; $moyenne_maxi=0;
	// ===========================================
	// MODIF: boireaus 20070616
	//$requete_note = mysql_query('SELECT * FROM '.$prefix_base.'matieres_notes WHERE '.$prefix_base.'matieres_notes.id_groupe = "'.$groupe_select.'" AND '.$prefix_base.'matieres_notes.periode = "'.$periode_select.'"');
	$requete_note = mysql_query('SELECT * FROM '.$prefix_base.'matieres_notes WHERE '.$prefix_base.'matieres_notes.id_groupe = "'.$groupe_select.'" AND '.$prefix_base.'matieres_notes.periode = "'.$periode_select.'" AND matieres_notes.statut=""');
	// ===========================================
	while ($donner_note = mysql_fetch_array($requete_note))
	{
		$note = $donner_note['note'];
		if($moyenne_mini>$note) { $moyenne_mini = $note; }
		if($moyenne_maxi<$note) { $moyenne_maxi = $note; }
		$addition_des_notes = $addition_des_notes+$note;
		$cpt_notes=$cpt_notes+1;
	}
	if ($cpt_notes == 0) {
		$moyenne_groupe = "-";
		$moyenne_mini = "-";
		$moyenne_maxi = "-";
	} else {
		$moyenne_groupe = $addition_des_notes / $cpt_notes;
	}

	// renvoie un tableau avec [moyenne dugroupe | moyenne mini du groupe | moyenne maxi du groupe]
	return array($moyenne_groupe, $moyenne_mini, $moyenne_maxi);
}

//permet de transformer les caractère html
function calcul_toute_moyenne_aid ($indice_aid, $periode_select)
{
	global $prefix_base;

	$addition_des_notes=0; $note=0; $cpt_notes=0; $moyenne_mini=20; $moyenne_maxi=0;
	$requete_note = mysql_query('SELECT * FROM '.$prefix_base.'aid_appreciations aa WHERE aa.indice_aid = "'.$indice_aid.'" AND aa.periode = "'.$periode_select.'" AND aa.statut=""');
	while ($donner_note = mysql_fetch_array($requete_note))
	{
		$note = $donner_note['note'];
		if($moyenne_mini>$note) { $moyenne_mini = $note; }
		if($moyenne_maxi<$note) { $moyenne_maxi = $note; }
		$addition_des_notes = $addition_des_notes+$note;
		$cpt_notes=$cpt_notes+1;
	}
	if ($cpt_notes == 0) {
		$moyenne_groupe = "-";
		$moyenne_mini = "-";
		$moyenne_maxi = "-";
	} else {
		$moyenne_groupe = $addition_des_notes / $cpt_notes;
	}

	// renvoie un tableau avec [moyenne dugroupe | moyenne mini du groupe | moyenne maxi du groupe]
	return array($moyenne_groupe, $moyenne_mini, $moyenne_maxi);
}

//permet de transformer les caractère html
function unhtmlentities($chaineHtml) {
		$tmp = get_html_translation_table(HTML_ENTITIES);
		$tmp = array_flip ($tmp);
		$chaineTmp = strtr ($chaineHtml, $tmp);

		return $chaineTmp;
}


// format de date en français
function date_fr($var)
{
		$var = explode("-",$var);
		$var = $var[2]."/".$var[1]."/".$var[0];
		return($var);
}

// fonction affiche les moyennes avec les arrondies et le nombre de chiffre après la virgule
// precision '0.01' '0.1' '0.25' '0.5' '1'
function present_nombre($nombre, $precision, $nb_chiffre_virgule, $chiffre_avec_zero)
{
	if ( $precision === '' or $precision === '0.0' or $precision === '0' ) { $precision = '0.01'; }
	$nombre=number_format(round($nombre/$precision)*$precision, $nb_chiffre_virgule, ',', '');
		$nombre_explose = explode(",",$nombre);
	if($nombre_explose[1]==='0' and $chiffre_avec_zero==='1') { $nombre=$nombre_explose[0]; }
		return($nombre);
}

class bul_PDF extends FPDF_MULTICELLTAG
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

// fonction graphique de niveau
function DiagBarre($X_placement, $Y_placement, $L_diagramme, $H_diagramme, $data, $place)
	{
		$this->SetFont('Courier', '', 10);
		//encadrement général
		$this->Rect($X_placement, $Y_placement, $L_diagramme, $H_diagramme, 'D');
		//encadrement du diagramme
		$this->SetDrawColor(180);
		$X_placement_diagramme = $X_placement+0.5;
		$Y_placement_diagramme = $Y_placement+0.5;
		$L_diagramme_affiche = $L_diagramme-1;
		$H_diagramme_affiche = $H_diagramme-1;
		$this->Rect($X_placement_diagramme, $Y_placement_diagramme, $L_diagramme_affiche, $H_diagramme_affiche, 'D');


		//calcul de la longeur de chaque barre
		$nb_valeur=count($data);
		$L_barre = $L_diagramme_affiche/$nb_valeur;
		// calcul de la somme total des informations
		$total_des_valeur = array_sum($data);

		if ( $total_des_valeur != '0' and $total_des_valeur != '' ) {
		  $espace_entre = $H_diagramme_affiche / $total_des_valeur;
		} else { $espace_entre = $H_diagramme_affiche; }

		for($o=0;$o<$total_des_valeur;$o++)
		{
		$Y_echelle=$Y_placement_diagramme+($espace_entre*$o);
		//echelle
		$this->SetDrawColor(180);
		$this->Line($X_placement_diagramme, $Y_echelle, $X_placement_diagramme+$L_diagramme_affiche, $Y_echelle);
		}

		$i=0;
		foreach($data as $val) {
			//Barre
			if($place===$i) { $this->SetFillColor(5); } else { $this->SetFillColor(240); }
			$this->SetDrawColor(0, 0, 0);
			if ( $total_des_valeur != '0' and $total_des_valeur != '' ) {
			$H_barre = ($H_diagramme_affiche*$val)/$total_des_valeur;
			} else { $H_barre = ($H_diagramme_affiche*$val); }
			$Y_barre = ($Y_placement_diagramme+$H_diagramme_affiche) - $H_barre;
			$X_barre = $X_placement_diagramme+($L_barre*$i);
			$this->Rect($X_barre, $Y_barre, $L_barre, $H_barre, 'DF');
			$i++;
		}
	}

	//En-tête du document
	function Header()
	{
	}

	//Pied de page du document
	function Footer()
	{
		//Positionnement à 1 cm du bas et 0,5cm + 0,5cm du coté gauche
	$this->SetXY(5,-10);
		//Police Arial Gras 6
		$this->SetFont('Arial','B',8);
		$this->Cell(0,4.5, "Bulletin à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.",0,0,'C');
	}
}

// chargement des information de la base de données
	// connaitre la selection
		//si se sont des classes ou une classe qui à été sélectionner on vas prendre l'id de tout leurs élèves
		//dès que nous avons notre liste d'élèves à imprimer on vas prendre les informations

	//variable invariable
	$gepiYear = getSettingValue('gepiYear');
	$annee_scolaire = $gepiYear;
	$date_bulletin=date("d/m/Y H:i");
	$nom_bulletin=date("Ymd_Hi");

// sélection des information sur le modèle des bulletins choisi.

$option_modele_bulletin=getSettingValue("option_modele_bulletin");

if(!empty($model_bulletin)) {
	//on compte le nombre de classes sélecionnées

	//=====================================================
	// Modif: boireaus

	//$nb_classes_selectionnees = 8; //sizeof($id_classe);
	$nb_classes_selectionnees=sizeof($id_classe);
	//=====================================================

	//echo $nb_classes_selectionnees;

	for ($z=0;$z<$nb_classes_selectionnees;$z++) {
		//en fonction de l'id de la classe, on recherche l'id du modèle utilisé.

		$la_classe = $id_classe[$z];

		$sql_classe = "SELECT * FROM classes WHERE id='$la_classe'";
		$call_classe = mysql_query($sql_classe);
		$result_classe = mysql_fetch_array($call_classe);
		$model_bulletin = $result_classe['modele_bulletin_pdf'];
		if ($model_bulletin == NULL) {$model_bulletin=1;}
		$classe_id = $la_classe;

		//echo "<br>Modèle N° ".$model_bulletin." Classe N°".$id_classe[$z]." VAR CLASSE_ID ".$classe_id;

		// la requete à modifier en fonction de $$option_modele_bulletin
		switch ($option_modele_bulletin) {
			case 1 : //uniquement avec modèle classe
				//$requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$model_bulletin.'"');
				$sql='SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$model_bulletin.'"';
				break;
			case 2 : //modèle par classe et choix possible
				if ($type_bulletin == -1) { // cas modèle par classe
					//$requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$model_bulletin.'"');
					$sql='SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$model_bulletin.'"';
				} else { //ici on recopie le même modèle pour chaque classe
					//$requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$type_bulletin.'"');
					$sql='SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$type_bulletin.'"';
				}
				break;
			case 3 : //choix d'un modèle
				//ici on recopie le même modèle pour chaque classe
				//$requete_model = mysql_query('SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$type_bulletin.'"');
				$sql='SELECT * FROM '.$prefix_base.'model_bulletin WHERE id_model_bulletin="'.$type_bulletin.'"';
				break;
		}
		//echo "$sql<br />\n";
		$requete_model = mysql_query($sql);


		//echo $type_bulletin;

		/****************** A T T E N T I O N ****************
		Tous les paramètres des modèles de  bulletin utilisés dans la table model_bulletin sont initialisés ci-dessous.

		Pour chaque paramètre, un tableau est construit. C'est l'ID de la classe qui sert d'indice pour récupérer la valeur du paramètre correspondant au modèle de bulletin de la classe.
		************************************************/

		while($donner_model = mysql_fetch_array($requete_model)) {
			$affiche_filigrame[$classe_id]=$donner_model['affiche_filigrame'];
			$texte_filigrame[$classe_id]=$donner_model['texte_filigrame'];
			$affiche_logo_etab[$classe_id]=$donner_model['affiche_logo_etab'];
			$nom_etab_gras[$classe_id]=$donner_model['nom_etab_gras'];
			$entente_mel[$classe_id]=$donner_model['entente_mel'];
			$entente_tel[$classe_id]=$donner_model['entente_tel'];
			$entente_fax[$classe_id]=$donner_model['entente_fax'];
			$L_max_logo[$classe_id]=$donner_model['L_max_logo'];
			$H_max_logo[$classe_id]=$donner_model['H_max_logo'];
			$active_bloc_datation[$classe_id] = $donner_model['active_bloc_datation']; // afficher le cadre les informations datation du bulletin
			$taille_texte_date_edition[$classe_id] = $donner_model['taille_texte_date_edition']; // taille du texte de la date d'éditon
			$active_bloc_eleve[$classe_id] = $donner_model['active_bloc_eleve']; // afficher le cadre sur les informations élève
			$active_bloc_adresse_parent[$classe_id] = $donner_model['active_bloc_adresse_parent']; // afficher le cadre adresse des parents
			$active_bloc_absence[$classe_id] = $donner_model['active_bloc_absence']; // afficher le cadre absences de l'élève
			$active_bloc_note_appreciation[$classe_id] = $donner_model['active_bloc_note_appreciation']; // afficher les notes et appréciations
			$active_bloc_avis_conseil[$classe_id] = $donner_model['active_bloc_avis_conseil']; // afficher les avis du conseil de classe
			$active_bloc_chef[$classe_id] = $donner_model['active_bloc_chef']; // fait - afficher la signature du chef
			$active_photo[$classe_id] = $donner_model['active_photo']; // fait - afficher la photo de l'élève
			$active_coef_moyenne[$classe_id] = $donner_model['active_coef_moyenne']; // fait - afficher le coéficient des moyenne par matière
			$active_nombre_note[$classe_id] = $donner_model['active_nombre_note']; // fait - afficher le nombre de note par matière sous la moyenne de l'élève
			$active_nombre_note_case[$classe_id] = $donner_model['active_nombre_note_case']; // fait - afficher le nombre de note par matière
			$active_moyenne[$classe_id] = $donner_model['active_moyenne']; // fait - afficher les moyennes
			$active_moyenne_eleve[$classe_id] = $donner_model['active_moyenne_eleve']; // fait - afficher la moyenne de l'élève
			$active_moyenne_classe[$classe_id] = $donner_model['active_moyenne_classe']; // fait - afficher les moyennes de la classe
			$active_moyenne_general[$classe_id] = $donner_model['active_moyenne_general']; // fait - afficher les moyennes général de l'élève
			$active_moyenne_min[$classe_id] = $donner_model['active_moyenne_min']; // fait - afficher les moyennes minimum
			$active_moyenne_max[$classe_id] = $donner_model['active_moyenne_max']; // fait - afficher les moyennes maximum
			$active_regroupement_cote[$classe_id] = $donner_model['active_regroupement_cote']; // fait - afficher le nom des regroupement sur le coté
			$active_entete_regroupement[$classe_id] = $donner_model['active_entete_regroupement']; // fait - afficher les entête des regroupement
			$active_moyenne_regroupement[$classe_id] = $donner_model['active_moyenne_regroupement']; // fait - afficher les moyennes des regroupement
			$active_rang[$classe_id] = $donner_model['active_rang']; // fait - afficher le rang de l'élève
			$active_graphique_niveau[$classe_id] = $donner_model['active_graphique_niveau']; // fait - afficher le graphique des niveaux
			$active_appreciation[$classe_id] = $donner_model['active_appreciation']; // fait - afficher les appréciations des professeurs
			$affiche_doublement[$classe_id] = $donner_model['affiche_doublement']; // affiche si l'élève à doubler
			$affiche_date_naissance[$classe_id] = $donner_model['affiche_date_naissance']; // affiche la date de naissance de l'élève
			$affiche_dp[$classe_id] = $donner_model['affiche_dp']; // affiche l'état de demi pension ou extern
			$affiche_nom_court[$classe_id] = $donner_model['affiche_nom_court']; // affiche le nom court de la classe
			$affiche_effectif_classe[$classe_id] = $donner_model['affiche_effectif_classe']; // affiche l'effectif de la classe
			$affiche_numero_impression[$classe_id] = $donner_model['affiche_numero_impression']; // affiche le numéro d'impression des bulletins
			$affiche_etab_origine[$classe_id] = $donner_model['affiche_etab_origine']; // affiche l'établissement d'orignine
			$active_reperage_eleve[$classe_id] = $donner_model['active_reperage_eleve']; // activé la couleur de réparage des moyenne de l'élève
			$couleur_reperage_eleve1[$classe_id] = $donner_model['couleur_reperage_eleve1']; // couleur 1 du repérage ci-dessus
			$couleur_reperage_eleve2[$classe_id] = $donner_model['couleur_reperage_eleve2']; // couleur 2 du repérage ci-dessus
			$couleur_reperage_eleve3[$classe_id] = $donner_model['couleur_reperage_eleve3']; // couleur 3 du repérage ci-dessus
			$couleur_categorie_entete[$classe_id] = $donner_model['couleur_categorie_entete']; // activé la couleur de fond des catégorie entête
			$couleur_categorie_entete1[$classe_id] = $donner_model['couleur_categorie_entete1']; // couleur 1 du repérage ci-dessus
			$couleur_categorie_entete2[$classe_id] = $donner_model['couleur_categorie_entete2']; // couleur 2 du repérage ci-dessus
			$couleur_categorie_entete3[$classe_id] = $donner_model['couleur_categorie_entete3']; // couleur 3 du repérage ci-dessus
			$couleur_categorie_cote[$classe_id] = $donner_model['couleur_categorie_cote']; // activé la couleur de fond des catégorie sur le coté
			$couleur_categorie_cote1[$classe_id] = $donner_model['couleur_categorie_cote1']; // couleur 1 du repérage ci-dessus
			$couleur_categorie_cote2[$classe_id] = $donner_model['couleur_categorie_cote2']; // couleur 2 du repérage ci-dessus
			$couleur_categorie_cote3[$classe_id] = $donner_model['couleur_categorie_cote3']; // couleur 3 du repérage ci-dessus
			$couleur_moy_general[$classe_id] = $donner_model['couleur_moy_general']; // activer la couleur moyenne général
			$couleur_moy_general1[$classe_id] = $donner_model['couleur_moy_general1']; // couleur 1 de la moyenne général
			$couleur_moy_general2[$classe_id] = $donner_model['couleur_moy_general2']; // couleur 2 de la moyenne général
			$couleur_moy_general3[$classe_id] = $donner_model['couleur_moy_general3']; // couleur 3 de la moyenne général
			$titre_entete_matiere[$classe_id] = $donner_model['titre_entete_matiere']; // texte de la colone matière
			$titre_entete_coef[$classe_id] = $donner_model['titre_entete_coef']; // texte de la colone coéfficiant
			$titre_entete_nbnote[$classe_id] = $donner_model['titre_entete_nbnote']; // texte de la colone nombre de note
			$titre_entete_rang[$classe_id] = $donner_model['titre_entete_rang']; // texte de la colone rang
			$titre_entete_appreciation = unhtmlentities($donner_model['titre_entete_appreciation']); //texte de la colone appréciation
			$toute_moyenne_meme_col[$classe_id] = $donner_model['toute_moyenne_meme_col']; //texte de la colone appréciation
			$entete_model_bulletin[$classe_id] = $donner_model['entete_model_bulletin']; //choix du type d'entete des moyennes
			$ordre_entete_model_bulletin[$classe_id] = $donner_model['ordre_entete_model_bulletin']; // ordre des entêtes tableau du bulletin
			// information paramétrage
			$caractere_utilse[$classe_id] = $donner_model['caractere_utilse'];
			// cadre identitée parents
			$X_parent[$classe_id]=$donner_model['X_parent']; $Y_parent[$classe_id]=$donner_model['Y_parent'];
			$imprime_pour[$classe_id]=$donner_model['imprime_pour'];
			// cadre identitée eleve
			$X_eleve[$classe_id]=$donner_model['X_eleve']; $Y_eleve[$classe_id]=$donner_model['Y_eleve'];
			$cadre_eleve[$classe_id]=$donner_model['cadre_eleve'];
			// cadre de datation du bulletin
			$X_datation_bul[$classe_id]=$donner_model['X_datation_bul']; $Y_datation_bul[$classe_id]=$donner_model['Y_datation_bul'];
			$cadre_datation_bul[$classe_id]=$donner_model['cadre_datation_bul'];
			// si les catégorie son affiché avec moyenne
			$hauteur_info_categorie[$classe_id]=$donner_model['hauteur_info_categorie'];
			// cadre des notes et app
			$X_note_app[$classe_id]=$donner_model['X_note_app']; $Y_note_app[$classe_id]=$donner_model['Y_note_app']; $longeur_note_app[$classe_id]=$donner_model['longeur_note_app']; $hauteur_note_app[$classe_id]=$donner_model['hauteur_note_app'];
			if($active_regroupement_cote[$classe_id]==='1') {
				$X_note_app[$classe_id]=$X_note_app[$classe_id]+5; $Y_note_app[$classe_id]=$Y_note_app[$classe_id]; $longeur_note_app[$classe_id]=$longeur_note_app[$classe_id]-5; $hauteur_note_app[$classe_id]=$hauteur_note_app[$classe_id];
			}
			//coef des matiere
			$largeur_coef_moyenne[$classe_id] = $donner_model['largeur_coef_moyenne'];
			//nombre de note par matière
			$largeur_nombre_note[$classe_id] = $donner_model['largeur_nombre_note'];
			//champ des moyennes
			$largeur_d_une_moyenne[$classe_id] = $donner_model['largeur_d_une_moyenne'];
			//graphique de niveau
			$largeur_niveau[$classe_id] = $donner_model['largeur_niveau'];
			//rang de l'élève
			$largeur_rang[$classe_id] = $donner_model['largeur_rang'];
			// cadre absence
			$X_absence[$classe_id]=$donner_model['X_absence']; $Y_absence[$classe_id]=$donner_model['Y_absence'];
			// entete du bas contient les moyennes gérnéral
			$hauteur_entete_moyenne_general[$classe_id] = $donner_model['hauteur_entete_moyenne_general'];
			// cadre des Avis du conseil de classe
			$X_avis_cons[$classe_id] = $donner_model['X_avis_cons'];
			$Y_avis_cons[$classe_id] = $donner_model['Y_avis_cons'];
			$longeur_avis_cons[$classe_id] = $donner_model['longeur_avis_cons'];
			$hauteur_avis_cons[$classe_id]=$donner_model['hauteur_avis_cons'];
			$cadre_avis_cons[$classe_id]=$donner_model['cadre_avis_cons'];
			// cadre signature du chef
			$X_sign_chef[$classe_id]=$donner_model['X_sign_chef']; $Y_sign_chef[$classe_id]=$donner_model['Y_sign_chef']; $longeur_sign_chef[$classe_id]=$donner_model['longeur_sign_chef']; $hauteur_sign_chef[$classe_id]=$donner_model['hauteur_sign_chef'];
			$cadre_sign_chef[$classe_id]=$donner_model['cadre_sign_chef'];

			//gestion des moyennes
			$arrondie_choix[$classe_id]=$donner_model['arrondie_choix'];
			$nb_chiffre_virgule[$classe_id]=$donner_model['nb_chiffre_virgule'];
			$chiffre_avec_zero[$classe_id]=$donner_model['chiffre_avec_zero'];

			$autorise_sous_matiere[$classe_id]=$donner_model['autorise_sous_matiere'];
			$affichage_haut_responsable[$classe_id]=$donner_model['affichage_haut_responsable'];
			$largeur_matiere[$classe_id]=$donner_model['largeur_matiere'];

			$taille_texte_matiere[$classe_id] = $donner_model['taille_texte_matiere']; // taille du texte des matière

			$titre_bloc_avis_conseil[$classe_id] = $donner_model['titre_bloc_avis_conseil']; // titre du bloc avis du conseil de classe
			$taille_titre_bloc_avis_conseil[$classe_id] = $donner_model['taille_titre_bloc_avis_conseil']; // taille du titre du bloc avis du conseil
			$taille_profprincipal_bloc_avis_conseil[$classe_id] = $donner_model['taille_profprincipal_bloc_avis_conseil']; // taille du texte prof principal du bloc avis conseil de classe
			$affiche_fonction_chef[$classe_id] = $donner_model['affiche_fonction_chef']; // affiche la fonction du chef
			$taille_texte_fonction_chef[$classe_id] = $donner_model['taille_texte_fonction_chef']; // taille du texte de la fonction du chef
			$taille_texte_identitee_chef[$classe_id] = $donner_model['taille_texte_identitee_chef']; // taille du texte du nom du chef

			$tel_image[$classe_id] = $donner_model['tel_image'];
			$tel_texte[$classe_id] = $donner_model['tel_texte'];
			$fax_image[$classe_id] = $donner_model['fax_image'];
			$fax_texte[$classe_id] = $donner_model['fax_texte'];
			$courrier_image[$classe_id] = $donner_model['courrier_image'];
			$courrier_texte[$classe_id] = $donner_model['courrier_texte'];
			$largeur_bloc_eleve[$classe_id] = $donner_model['largeur_bloc_eleve'];
			$hauteur_bloc_eleve[$classe_id] = $donner_model['hauteur_bloc_eleve'];
			$largeur_bloc_adresse[$classe_id] = $donner_model['largeur_bloc_adresse'];
			$hauteur_bloc_adresse[$classe_id] = $donner_model['hauteur_bloc_adresse'];
			$largeur_bloc_datation[$classe_id] = $donner_model['largeur_bloc_datation'];
			$hauteur_bloc_datation[$classe_id] = $donner_model['hauteur_bloc_datation'];
			$taille_texte_classe[$classe_id] = $donner_model['taille_texte_classe'];
			$type_texte_classe[$classe_id] = $donner_model['type_texte_classe'];
			$taille_texte_annee[$classe_id] = $donner_model['taille_texte_annee'];
			$type_texte_annee[$classe_id] = $donner_model['type_texte_annee'];
			$taille_texte_periode[$classe_id] = $donner_model['taille_texte_periode'];
			$type_texte_periode[$classe_id] = $donner_model['type_texte_periode'];
			$taille_texte_categorie_cote[$classe_id] = $donner_model['taille_texte_categorie_cote']; // taille du texte du nom des catégorie sur le coté
			$taille_texte_categorie[$classe_id] = $donner_model['taille_texte_categorie']; // taille du texte du nom des catégorie en entête
			$type_texte_date_datation[$classe_id] = $donner_model['type_texte_date_datation'];
			$cadre_adresse[$classe_id] = $donner_model['cadre_adresse']; // cadre sur l'adresse
			$centrage_logo[$classe_id] = $donner_model['centrage_logo']; // centrer le logo de l'établissement
			$Y_centre_logo[$classe_id] = $donner_model['Y_centre_logo']; // centre du logo sur la page
			$ajout_cadre_blanc_photo[$classe_id] = $donner_model['ajout_cadre_blanc_photo']; // ajouter un cadre blanc pour la photo de l'élève.

		} //FOR ???
	}
} else {
	$classe_id=0;

	// information d'activation des différents partie du bulletin
	$affiche_filigrame[$classe_id]='1'; // affiche un filigramme
	$texte_filigrame[$classe_id]='DUPLICATA INTERNET'; // texte du filigrame
	$affiche_logo_etab[$classe_id]='1';
	$nom_etab_gras[$classe_id]='0';
	$entente_mel[$classe_id]='1'; // afficher l'adresse mel dans l'entête
	$entente_tel[$classe_id]='1'; // afficher le numéro de téléphone dans l'entête
	$entente_fax[$classe_id]='1'; // afficher le numéro de fax dans l'entête
	$L_max_logo[$classe_id]=75; $H_max_logo[$classe_id]=75; //dimension du logo
	$active_bloc_datation[$classe_id] = '1'; // fait - afficher les informations de datation du bulletin
	$taille_texte_date_edition[$classe_id] = '8'; // définit la taille de la date d'édition du bulletin
	$active_bloc_eleve[$classe_id] = '1'; // fait - afficher les informations sur l'élève
	$active_bloc_adresse_parent[$classe_id] = '1'; // fait - afficher l'adresse des parents
	$active_bloc_absence[$classe_id] = '1'; // fait - afficher les absences de l'élève
	$active_bloc_note_appreciation[$classe_id] = '1'; // fait - afficher les notes et appréciations
	$active_bloc_avis_conseil[$classe_id] = '1'; // fait - afficher les avis du conseil de classe
	$active_bloc_chef[$classe_id] = '1'; // fait - afficher la signature du chef
	$active_photo[$classe_id] = '0'; // fait - afficher la photo de l'élève
	$active_coef_moyenne[$classe_id] = '1'; // fait - afficher le coéficient des moyenne par matière
	$active_coef_sousmoyene = '1'; // fait - afficher le coéficient des moyenne par matière
	$active_nombre_note[$classe_id] = '1'; // fait - afficher le nombre de note par matière sous la moyenne de l'élève
	$active_nombre_note_case[$classe_id] = '1'; // fait - afficher le nombre de note par matière
	$active_moyenne[$classe_id] = '1'; // fait - afficher les moyennes
	$active_moyenne_eleve[$classe_id] = '1'; // fait - afficher la moyenne de l'élève
	$active_moyenne_classe[$classe_id] = '1'; // fait - afficher les moyennes de la classe
	$active_moyenne_min[$classe_id] = '1'; // fait - afficher les moyennes minimum
	$active_moyenne_max[$classe_id] = '1'; // fait - afficher les moyennes maximum
	$active_regroupement_cote[$classe_id] = '1'; // fait - afficher le nom des regroupement sur le coté
	$active_entete_regroupement[$classe_id] = '1'; // fait - afficher les entête des regroupement
	$active_moyenne_regroupement[$classe_id] = '1'; // fait - afficher les moyennes des regroupement
	$active_moyenne_general[$classe_id] = '1'; // fait - afficher la moyenne général sur le bulletin
	$active_rang[$classe_id] = '1'; // fait - afficher le rang de l'élève
	$active_graphique_niveau[$classe_id] = '1'; // fait - afficher le graphique des niveaux
	$active_appreciation[$classe_id] = '1'; // fait - afficher les appréciations des professeurs

	$affiche_doublement[$classe_id] = '1'; // affiche si l'élève à doubler
	$affiche_date_naissance[$classe_id] = '1'; // affiche la date de naissance de l'élève
	$affiche_dp[$classe_id] = '1'; // affiche l'état de demi pension ou extern
	$affiche_nom_court[$classe_id] = '1'; // affiche le nom court de la classe
	$affiche_effectif_classe[$classe_id] = '1'; // affiche l'effectif de la classe
	$affiche_numero_impression[$classe_id] = '1'; // affiche le numéro d'impression des bulletins
	$affiche_etab_origine[$classe_id] = '0'; // affiche l'établissement d'origine

	$toute_moyenne_meme_col[$classe_id]='0'; // afficher les information moyenne classe/min/max sous la moyenne général de l'élève
	$active_coef_sousmoyene = '1'; //afficher le coeficent en dessous de la moyenne de l'élève

	$entete_model_bulletin[$classe_id] = '1'; //choix du type d'entete des moyennes
	$ordre_entete_model_bulletin[$classe_id] = '1'; // ordre des entêtes tableau du bulletin

	// information paramétrage
	$caractere_utilse[$classe_id] = 'Arial';
	// cadre identitée parents
	$X_parent[$classe_id]=110; $Y_parent[$classe_id]=40;
	$imprime_pour[$classe_id] = 1;
	// cadre identitée eleve
	$X_eleve[$classe_id]=5; $Y_eleve[$classe_id]=40;
	$cadre_eleve[$classe_id]=1;
	// cadre de datation du bulletin
	$X_datation_bul[$classe_id]=110; $Y_datation_bul[$classe_id]=5;
	$cadre_datation_bul[$classe_id]=1;
	// si les catégorie son affiché avec moyenne
	$hauteur_info_categorie[$classe_id]=5;
	// cadre des notes et app
	$X_note_app[$classe_id]=5; $Y_note_app[$classe_id]=72; $longeur_note_app[$classe_id]=200; $hauteur_note_app[$classe_id]=175;
	if($active_regroupement_cote[$classe_id]==='1') { $X_note_app[$classe_id]=$X_note_app[$classe_id]+5; $Y_note_app[$classe_id]=$Y_note_app[$classe_id]; $longeur_note_app[$classe_id]=$longeur_note_app[$classe_id]-5; $hauteur_note_app[$classe_id]=$hauteur_note_app[$classe_id]; }
	//coef des matiere
	$largeur_coef_moyenne[$classe_id] = 8;
	//nombre de note par matière
	$largeur_nombre_note[$classe_id] = 8;
	//champ des moyennes
	$largeur_d_une_moyenne[$classe_id] = 10;
	//graphique de niveau
	$largeur_niveau[$classe_id] = 18;
	//rang de l'élève
	$largeur_rang[$classe_id] = 8;
	//autres infos
	$active_reperage_eleve[$classe_id] = '1';
	$couleur_reperage_eleve1[$classe_id] = '255';
	$couleur_reperage_eleve2[$classe_id] = '255';
	$couleur_reperage_eleve3[$classe_id] = '207';
	$couleur_categorie_cote[$classe_id] = '1';
	$couleur_categorie_cote1[$classe_id]='239';
	$couleur_categorie_cote2[$classe_id]='239';
	$couleur_categorie_cote3[$classe_id]='239';
	$couleur_categorie_entete[$classe_id] = '1';
	$couleur_categorie_entete1[$classe_id]='239';
	$couleur_categorie_entete2[$classe_id]='239';
	$couleur_categorie_entete3[$classe_id]='239';
	$couleur_moy_general[$classe_id] = '1';
	$couleur_moy_general1[$classe_id]='239';
	$couleur_moy_general2[$classe_id]='239';
	$couleur_moy_general3[$classe_id]='239';
	$titre_entete_matiere[$classe_id]='Matière';
	$active_coef_sousmoyene = '1'; $titre_entete_coef[$classe_id]='coef.';
	$titre_entete_nbnote[$classe_id]='nb. n.';
	$titre_entete_rang[$classe_id]='rang';
	$titre_entete_appreciation='Appréciation/Conseils';
	// cadre absence
	$X_absence[$classe_id]=5; $Y_absence[$classe_id]=246.3;
	// entete du bas contient les moyennes gérnéral
	$hauteur_entete_moyenne_general[$classe_id] = 5;
	// cadre des Avis du conseil de classe
	$X_avis_cons[$classe_id]=5; $Y_avis_cons[$classe_id]=250; $longeur_avis_cons[$classe_id]=130; $hauteur_avis_cons[$classe_id]=37;
	$cadre_avis_cons[$classe_id]=1;
	// cadre signature du chef
	$X_sign_chef[$classe_id]=138; $Y_sign_chef[$classe_id]=250; $longeur_sign_chef[$classe_id]=67; $hauteur_sign_chef[$classe_id]=37;
	$cadre_sign_chef[$classe_id]=0;
	//les moyennes
	$arrondie_choix[$classe_id]='0.01'; //arrondie de la moyenne
	$nb_chiffre_virgule[$classe_id]='1'; //nombre de chiffre après la virgule
	$chiffre_avec_zero[$classe_id]='1'; // si une moyenne se termine par ,00 alors on supprimer les zero

	$autorise_sous_matiere[$classe_id] = '1'; //autorise l'affichage des sous matière
	$affichage_haut_responsable[$classe_id] = '1'; //affiche le nom du haut responsable de la classe

	$largeur_matiere[$classe_id] = '40'; // largeur de la colonne matiere

	$taille_texte_matiere[$classe_id] = '10'; //taille du texte des matières

	$titre_bloc_avis_conseil[$classe_id] = 'Avis du Conseil de classe:'; // titre du bloc avis du conseil de classe
	$taille_titre_bloc_avis_conseil[$classe_id] = '10'; // taille du titre du bloc avis du conseil
	$taille_profprincipal_bloc_avis_conseil[$classe_id] = '10'; // taille du texte prof principal du bloc avis conseil de classe
	$affiche_fonction_chef[$classe_id] = '1'; // affiche la fonction du chef
	$taille_texte_fonction_chef[$classe_id] = '10'; // taille du texte de la fonction du chef
	$taille_texte_identitee_chef[$classe_id] = '10'; // taille du texte du nom du chef

	$cadre_adresse[$classe_id] = ''; // cadre sur l'adresse

	$centrage_logo[$classe_id] = '0'; // centrer le logo de l'établissement
	$Y_centre_logo[$classe_id] = '18'; // centre du logo sur la page
	$ajout_cadre_blanc_photo[$classe_id] = '0'; // ajouter un cadre blanc pour la photo de l'élève.
	}


// information à retenir pour la construction des bulletins

	//récupération des pédiode periode_classe[numéro de la classe][compteur]

	//attribuer une selection de période à une classe
	$cpt_p=0; $ancienne_classe='';
	$cpt_p_interne=0;
	while(!empty($periode[$cpt_p]))
	{
		// $periode[$cpt_p] détien le nom de la période par exemple 1er trimestre

		// nous allons rechercher toute les classes qui ont le même nom de période
		if ( isset($id_classe[0]) and !empty($id_classe[0]) )
		{
			$o=0; $prepa_requete = "";
			while(!empty($id_classe[$o]))
			{
				if($o == "0") { $prepa_requete = 'id_classe = "'.$id_classe[$o].'"'; }
				if($o != "0") { $prepa_requete = $prepa_requete.' OR id_classe = "'.$id_classe[$o].'" '; }
				$o = $o + 1;
			}
		}

		// nous allons rechercher toute les classes qui ont le même nom de période par les élèves
		if ( isset($id_eleve[0]) and !empty($id_eleve[0]) )
		{
			$o=0; $prepa_requete = "";
			while(!empty($id_eleve[$o]))
			{
				if($o == "0") { $prepa_requete = 'jec.login = "'.$id_eleve[$o].'"'; }
				if($o != "0") { $prepa_requete = $prepa_requete.' OR jec.login = "'.$id_eleve[$o].'" '; }
				$o = $o + 1;
			}
		}
	$cpt_p = $cpt_p + 1;
	}

	// on enlève tout le superflu du nom de la période
	if (isset($periode[0]))
	{
		$o=0;
		while(!empty($periode[$o]))
		{
			$periode[$o] = eregi_replace("[ .'_-]{1}",'',$periode[$o]); //supprime les espace les . les ' les _ et -
			$periode[$o] = strtolower($periode[$o]); // mets en minuscule
			$periode[$o] = html_entity_decode($periode[$o]);
			$periode[$o] = eregi_replace("[éèëê]{1}","e",$periode[$o]); // supprime les accents
			// A VOIR: Il pourrait bien y avoir d'autres caractères accentués.
			$o = $o + 1;
		}
	}

	$cpt_p_interne = 0;
	if ( isset($id_classe[0]) and !empty($id_classe[0]) )
	{
		$requete_periode_select ="SELECT * FROM ".$prefix_base."periodes WHERE (".$prepa_requete.") ORDER BY nom_periode";
	}
	if ( isset($id_eleve[0]) and !empty($id_eleve[0]) )
	{
		$requete_periode_select ="SELECT * FROM ".$prefix_base."periodes p, ".$prefix_base."j_eleves_classes jec WHERE ( (".$prepa_requete.") AND jec.id_classe = p.id_classe ) GROUP BY p.num_periode, p.id_classe ORDER BY p.nom_periode";
	}

	$execution_periode_select = mysql_query($requete_periode_select) or die('Erreur SQL !'.$requete_periode_select.'<br />'.mysql_error());
	while ( $donnee_periode_select = mysql_fetch_array($execution_periode_select) )
	{
		// nom de la période ex: 1er trimestre
		$nom_periode_select = $donnee_periode_select['nom_periode'];
		// id de la classe
		$id_classe_periode = $donnee_periode_select['id_classe'];
		// savoir si elles est vérouillez
		$periode_verouillez_classe = $donnee_periode_select['verouiller'];

		// on transforme le nom de la période sans accens sans espace...
		$nom_periode_select = eregi_replace("[ .'_-]{1}",'',$nom_periode_select); //supprime les espace les . les ' les _ et -
		$nom_periode_select = strtolower($nom_periode_select); // mais en minuscule
		$nom_periode_select = html_entity_decode($nom_periode_select);
		$nom_periode_select = eregi_replace("[éèëê]{1}",'e',$nom_periode_select); // supprime les accents
		// A VOIR: Il pourrait bien y avoir d'autres caractères accentués.

		// si la classe et la période corresponde alors on initialise
		if ( ( $periode_verouillez_classe === 'O' or $periode_verouillez_classe === 'P' ) or $periode_ferme != '1' )
		{
			if ( in_array($nom_periode_select, $periode) ) {
				if ( !isset($periode_classe[$id_classe_periode]) )
				{
					$cpt_p_interne = 0;
					$periode_classe[$id_classe_periode][$cpt_p_interne] = $donnee_periode_select['num_periode'];
				}
				elseif ( isset($periode_classe[$id_classe_periode]) )
				{
					$compte_entrer = count($periode_classe[$id_classe_periode]);
					$cpt_p_interne = $compte_entrer;
					$periode_classe[$id_classe_periode][$cpt_p_interne] = $donnee_periode_select['num_periode'];
				}
			}
		}
	}



	// sql sélection des eleves et de leurs informations
	//requête des classes sélectionné
	if (isset($id_classe[0])) {
		$o=0;
		$prepa_requete = "";
		while(!empty($id_classe[$o]))
		{
			if($o == "0") { $prepa_requete = $prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'"'; }
			if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'" '; }
			$o = $o + 1;
		}
	}
	//requête des élèves sélectionné
	if (!empty($id_eleve[0])) {
		$o=0;
		$prepa_requete = "";
		while(!empty($id_eleve[$o]))
		{
			if($o == "0") { $prepa_requete = $prefix_base.'eleves.login = "'.$id_eleve[$o].'"'; }
			if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'eleves.login = "'.$id_eleve[$o].'" '; }
			$o = $o + 1;
		}
	}

	//tableau des données élèves
// Modif Eric pour option tri par etab origine	
//On n'affiche pas les élèves qui correspondent au N° RNE de l'établissement
    switch ($tri_par_etab_origine) {
	case "oui" :
		if (isset($id_classe[0])) { 
			$requete = 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime, '.$prefix_base.'j_eleves_etablissements WHERE '.$prefix_base.'j_eleves_etablissements.id_etablissement != \''.$RneEtablissement.' \' AND '.$prefix_base.'j_eleves_etablissements.id_eleve = '.$prefix_base.'eleves.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_etablissements.id_etablissement ASC, '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC';
			//echo $requete;
			$call_eleve = mysql_query($requete);
		}
		if (isset($id_eleve[0])) { 
			$requete = 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime, '.$prefix_base.'j_eleves_etablissements WHERE '.$prefix_base.'j_eleves_etablissements.id_etablissement != \''.$RneEtablissement.' \' AND '.$prefix_base.'j_eleves_etablissements.id_eleve = '.$prefix_base.'eleves.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_etablissements.id_etablissement ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC' ;
			//echo $requete;
			$call_eleve = mysql_query($requete);
		}
		break;
	case "non" :
		if (isset($id_classe[0])) { 
			$requete = 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC';
			//echo $requete;
			$call_eleve = mysql_query($requete);
		}
		if (isset($id_eleve[0])) { 
			$requete = 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC' ;
			//echo $requete;
			$call_eleve = mysql_query($requete);
		}
		break;
	default:
	    echo "defaut";
		if (isset($id_classe[0])) { $call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'); }	
		if (isset($id_eleve[0])) { $call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_regime.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'); }
    break;
	}
// Fin modif Eric  pour option tri par etab origine
	
	
	//on compte les élèves sélectionnés
	$nb_eleves = mysql_num_rows($call_eleve);
	$cpt_i = 1;
	while ($donner = mysql_fetch_array($call_eleve))
	{
		//AJOUT ERIC
		$eleve_id_classe[$cpt_i]=$donner['id'];
		$ident_eleve[$cpt_i] = $donner['login'];
		$ident_eleve_sel1 = $ident_eleve[$cpt_i];
		$ele_id_eleve[$cpt_i] = $donner['ele_id'];
		$nom_eleve[$cpt_i] = $donner['nom'];
		$prenom_eleve[$cpt_i] = $donner['prenom'];
		$sexe[$cpt_i] = $donner['sexe'];
		if ($sexe[$cpt_i] == "M") {
			$date_naissance[$cpt_i] = 'Né le '.date_fr($donner['naissance']);
		} else {
			$date_naissance[$cpt_i] = 'Née le '.date_fr($donner['naissance']);
		}

		$INE_eleve[$cpt_i] = $donner['no_gep'];


		$classe_tableau_id[$cpt_i] = $donner['id'];
		$classe_nomlong[$cpt_i] = $donner['nom_complet'];
		$classe_nomcour[$cpt_i] = $donner['classe'];
		
		//$photo[$cpt_i] = "../photos/eleves/".strtolower($donner['elenoet']).".jpg";
		$tmp_photo=nom_photo(strtolower($donner['elenoet']));
		if("$tmp_photo"!=""){
			$photo[$cpt_i] = "../photos/eleves/".$tmp_photo;
		}
		else{
			$photo[$cpt_i] = "";
		}

		$doublement[$cpt_i]='';
		if($donner['doublant']==='R') {  if($sexe[$cpt_i]==='M') { $doublement[$cpt_i]='doublant'; } else { $doublement[$cpt_i]='doublante'; } }
		if($donner['regime']==='d/p') { $dp[$cpt_i]='demi-pensionnaire'; }
		if($donner['regime']==='ext.') { $dp[$cpt_i]='externe'; }
		if($donner['regime']==='int.') { $dp[$cpt_i]='interne'; }
		if($donner['regime']==='i-e') { if($sexe[$cpt_i]==='M') { $dp[$cpt_i]='interne externé'; } else { $dp[$cpt_i]='interne externée'; } }
		if($donner['regime']!='ext.' and $donner['regime']!='d/p' and $donner['regime']==='int.' and $donner['regime']==='i-e') { $dp[$cpt_i]='inconnu'; }
		
//modif Eric
		// etablissement d'origine
		// on vérifie si l'élève a un établissement d'origine
		if ( !isset($etablissement_origine) ) {	$etablissement_origine = ''; }
		$cpt_etab_origine = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."j_eleves_etablissements jee, ".$prefix_base."etablissements etab WHERE jee.id_eleve = '".$ident_eleve_sel1."' AND jee.id_etablissement = etab.id"),0);
		if($cpt_etab_origine != 0) {
			$requete_etablissement_origine = "SELECT * FROM ".$prefix_base."j_eleves_etablissements jee, ".$prefix_base."etablissements etab WHERE jee.id_eleve = '".$ident_eleve_sel1."' AND jee.id_etablissement = etab.id";
			$execution_etablissement_origine = mysql_query($requete_etablissement_origine) or die('Erreur SQL !'.$requete_etablissement_origine.'<br />'.mysql_error());
			while ($donnee_etablissement_origine = mysql_fetch_array($execution_etablissement_origine))
			{
				$current_eleve_etab_id = $donnee_etablissement_origine['id'];
				$current_eleve_etab_nom = $donnee_etablissement_origine['nom'];
				$current_eleve_etab_niveau = $donnee_etablissement_origine['niveau'];
				$current_eleve_etab_type = $donnee_etablissement_origine['type'];
				$current_eleve_etab_cp = $donnee_etablissement_origine['cp'];
				$current_eleve_etab_ville = $donnee_etablissement_origine['ville'];
				//$etablissement_origine[$cpt_i] = $donnee_etablissement_origine['nom'].' ('.$donnee_etablissement_origine['id'].')';
			}
		} else {
				$current_eleve_etab_id = '';
				$current_eleve_etab_nom = '';
				$current_eleve_etab_niveau = '';
				$current_eleve_etab_type = '';
				$current_eleve_etab_cp = '';
				$current_eleve_etab_ville = '';
			}

		if ( $current_eleve_etab_niveau != '' ) {
		foreach ($type_etablissement as $type_etab => $nom_etablissement) {
			if ($current_eleve_etab_niveau == $type_etab) {$current_eleve_etab_niveau_nom = $nom_etablissement;}
		}
		if ($current_eleve_etab_cp == 0) { $current_eleve_etab_cp = ''; }

		if ( $current_eleve_etab_type == 'aucun' ) { $current_eleve_etab_type = ''; }
		else {
			if ( isset($type_etablissement2[$current_eleve_etab_type][$current_eleve_etab_niveau]) ) { $current_eleve_etab_type = $type_etablissement2[$current_eleve_etab_type][$current_eleve_etab_niveau]; } else { $current_eleve_etab_type = ''; }
		     }
		}

		if ($current_eleve_etab_nom != '') {
			if ($current_eleve_etab_id != '990') {
			    if ($RneEtablissement != $current_eleve_etab_id) {
				   $etablissement_origine[$cpt_i] = $current_eleve_etab_niveau_nom." ".$current_eleve_etab_type." ".$current_eleve_etab_nom." (".$current_eleve_etab_cp." ".$current_eleve_etab_ville.")";
			    }
			} else {
				$etablissement_origine[$cpt_i] .=  "hors de France";
			}
		}
// fin modif Eric

		//connaitre le professeur responsable de l'élève
		$requete_pp = mysql_query('SELECT professeur FROM '.$prefix_base.'j_eleves_professeurs WHERE (login="'.$ident_eleve[$cpt_i].'" AND id_classe="'.$classe_tableau_id[$cpt_i].'")');
		$prof_suivi_login = @mysql_result($requete_pp, '0', 'professeur');
		$pp_classe[$cpt_i] = '<bppc>'.ucfirst(getSettingValue("gepi_prof_suivi")).' : </bppc><ippc>'.affiche_utilisateur($prof_suivi_login,$classe_tableau_id[$cpt_i]).'</ippc>';

		//les responsables
		$nombre_de_responsable = 0;
		//=========================
		// MODIF: boireaus 20071004
		//$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id )"),0);
		$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id  AND (r.resp_legal='1' OR r.resp_legal='2'))"),0);
		//=========================

		if($nombre_de_responsable != 0)
		{
			$cpt_parents = 0;
			//=========================
			// MODIF: boireaus 20071004
			//$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id ) ORDER BY resp_legal ASC");
			$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id AND (r.resp_legal='1' OR r.resp_legal='2')) ORDER BY resp_legal ASC");
			//=========================

			while ($donner_parents = mysql_fetch_array($requete_parents))
			{
				$civilite_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['civilite'];
				$nom_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['nom'];
				$prenom_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['prenom'];
				$adresse1_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['adr1'];
				$adresse2_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['adr2'];
				$ville_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['commune'];
				$cp_parents[$ident_eleve_sel1][$cpt_parents] = $donner_parents['cp'];
				$cpt_parents = $cpt_parents + 1;
			}
		} else {
			$civilite_parents[$ident_eleve_sel1][0] = '';
			$nom_parents[$ident_eleve_sel1][0] = '';
			$prenom_parents[$ident_eleve_sel1][0] = '';
			$adresse1_parents[$ident_eleve_sel1][0] = '';
			$adresse2_parents[$ident_eleve_sel1][0] = '';
			$ville_parents[$ident_eleve_sel1][0] = '';
			$cp_parents[$ident_eleve_sel1][0] = '';

			$civilite_parents[$ident_eleve_sel1][1] = '';
			$nom_parents[$ident_eleve_sel1][1] = '';
			$prenom_parents[$ident_eleve_sel1][1] = '';
			$adresse1_parents[$ident_eleve_sel1][1] = '';
			$adresse2_parents[$ident_eleve_sel1][1] = '';
			$ville_parents[$ident_eleve_sel1][1] = '';
			$cp_parents[$ident_eleve_sel1][1] = '';
		}

		// si deux envois car adresse différent des responsables, par défaut = 1
		$nb_bulletin_parent[$ident_eleve_sel1] = 1;
		if ( isset($adresse1_parents[$ident_eleve_sel1][1]) )
		{
			if ( $imprime_pour[$classe_id] === '1' ) { $nb_bulletin_parent[$ident_eleve_sel1] = 1; }
			if ( $imprime_pour[$classe_id] === '2' ) {
				if ( $adresse1_parents[$ident_eleve_sel1][0] != $adresse1_parents[$ident_eleve_sel1][1] and $adresse1_parents[$ident_eleve_sel1][1] != '' )
				{
					$nb_bulletin_parent[$ident_eleve_sel1] = 2;
				} else { $nb_bulletin_parent[$ident_eleve_sel1] = 1; }
				if ( $nom_parents[$ident_eleve_sel1][0] === '' ) { $nb_bulletin_parent[$ident_eleve_sel1] = 1; }
			}
			if ( $imprime_pour[$classe_id] === '3' and $nom_parents[$ident_eleve_sel1][1] != '' ) { $nb_bulletin_parent[$ident_eleve_sel1] = 2; }
			if ( $imprime_pour[$classe_id] === '3' and $nom_parents[$ident_eleve_sel1][1] === '' ) { $nb_bulletin_parent[$ident_eleve_sel1] = 1; }
		} else { $nb_bulletin_parent[$ident_eleve_sel1] = 1; }


		//connaître le cpe de l'élève
		$query = mysql_query("SELECT u.login login FROM utilisateurs u, j_eleves_cpe j WHERE (u.login = j.cpe_login AND j.e_login = '".$ident_eleve[$cpt_i]."')");
		$current_eleve_cperesp_login = @mysql_result($query, "0", "login");
		$cpe_eleve[$cpt_i] = '<i>'.affiche_utilisateur($current_eleve_cperesp_login,$classe_tableau_id[$cpt_i]).'</i>';

		//=================================
		// AJOUT: boireaus
		$cperesp_login[$cpt_i] = $current_eleve_cperesp_login;
		//=================================

		$cpt_i = $cpt_i + 1;
	}
	$nb_eleve_total = $cpt_i-1; //nombre total d'élève sélectionné
	// fin de la sélection des informations sur les élèves selectionné

	//recherche des données de notation et d'appreciation
		//on recherche les donne élève par élève

	// Avant toute chose, on s'assure que les rangs sont recalculés si nécessaire
	if ($active_rang[$classe_id] === '1') {
		$tab_classes = array_unique($classe_tableau_id);
		$affiche_categories = false;
		$test_coef = "1";
		foreach($tab_classes as $id_classe) {
			$periode_en_cours = 0;
			while(!empty($periode_classe[$id_classe][$periode_en_cours])) {
				$periode_num = $periode_classe[$id_classe][$periode_en_cours];
				include "../lib/calcul_rang.inc.php";
				$periode_en_cours++;
			}
		}
	}

//=====================
// AJOUT: boireaus 20070616
$matiere=array();
//=====================

$passage_deux = 'non';
$cpt_info_eleve=1;
while($cpt_info_eleve<=$nb_eleve_total)
{
	$cpt_info_periode=0;
	$id_classe = $classe_tableau_id[$cpt_info_eleve]; // classe de l'élève

	//$login_eleve_select = $ident_eleve[$cpt_info_eleve]; // login de l'élève
	//=====================
	// AJOUT: boireaus 20070616
	$matiere[$ident_eleve[$cpt_info_eleve]]=array();
	//$matiere[$ident_eleve[$cpt_info_eleve]][$id_periode]=array();
	//=====================

	//AJOUT ERIC
	$classe_id=$id_classe;

	while(!empty($periode_classe[$id_classe][$cpt_info_periode]))
	{
		$id_periode=$periode_classe[$id_classe][$cpt_info_periode];
		$nombre_de_matiere = 0;
		$moy_general_eleve = 0;
		$cpt_info_eleve_matiere=0;
		//prendre toutes les matières dont fait partie l'élève dans une période donné

		//=====================
		// AJOUT: boireaus 20070616
		//$matiere[$ident_eleve[$cpt_info_eleve]]=array();
		$matiere[$ident_eleve[$cpt_info_eleve]][$id_periode]=array();
		//=====================

		if($active_regroupement_cote[$classe_id]==='1' or $active_entete_regroupement[$classe_id]==='1') {
			// Requête pour le classement par catégories de matières
			//$requete_toute_matier = mysql_query("SELECT " .
			$sql="SELECT " .
						"jeg.id_groupe id_groupe, " .
						"m.nom_complet nom_long_matiere,  " .
						"mc.nom_complet nom_categorie ".
					"FROM " .
						"j_groupes_classes jgc, " .
						"j_eleves_classes jec, " .
						"j_eleves_groupes jeg, " .
						"j_groupes_matieres jgm, " .
						"j_matieres_categories_classes jmcc, " .
						"matieres_categories mc, " .
						"matieres m " .
					"WHERE (" .
						"jec.login = '".$ident_eleve[$cpt_info_eleve]."' AND " .
						"jec.periode = '".$id_periode."' AND " .
						"jeg.login = jec.login AND " .
						"jeg.periode = jec.periode AND " .
						"jgc.categorie_id = jmcc.categorie_id AND " .
						"jmcc.classe_id = jec.id_classe AND " .
						"mc.id = jgc.categorie_id AND " .
						"jgc.id_classe = jec.id_classe AND " .
						"jgc.id_groupe = jeg.id_groupe AND " .
						"jgm.id_groupe = jeg.id_groupe AND " .
						"m.matiere = jgm.id_matiere" .
					") " .
					"GROUP BY id_groupe ORDER BY jmcc.priority,jmcc.categorie_id,jgc.priorite,m.nom_complet";
		} else {
			// Requête pour le classement sans catégories de matières
			//============================
			// Modif: boireaus 20070828
			//$requete_toute_matier = mysql_query("SELECT " .
			/*
			$sql="SELECT " .
					"jeg.id_groupe id_groupe, " .
					"m.nom_complet nom_long_matiere,  " .
					"mc.nom_complet nom_categorie ".
				"FROM " .
					"j_groupes_classes jgc, " .
					"j_eleves_classes jec, " .
					"j_eleves_groupes jeg, " .
					"j_groupes_matieres jgm, " .
					"j_matieres_categories_classes jmcc, " .
					"matieres_categories mc, " .
					"matieres m " .
				"WHERE (" .
					"jec.login = '".$ident_eleve[$cpt_info_eleve]."' AND " .
					"jec.periode = '".$id_periode."' AND " .
					"jeg.login = jec.login AND " .
					"jeg.periode = jec.periode AND " .
					"mc.id = jgc.categorie_id AND " .
					"jgc.id_classe = jec.id_classe AND " .
					"jgc.id_groupe = jeg.id_groupe AND " .
					"jgm.id_groupe = jeg.id_groupe AND " .
					"m.matiere = jgm.id_matiere" .
				") " .
				"GROUP BY id_groupe ORDER BY jgc.priorite,m.nom_complet";
			*/

			$sql="SELECT " .
					"jeg.id_groupe id_groupe, " .
					"m.nom_complet nom_long_matiere " .
				"FROM " .
					"j_groupes_classes jgc, " .
					"j_eleves_classes jec, " .
					"j_eleves_groupes jeg, " .
					"j_groupes_matieres jgm, " .
					"matieres m " .
				"WHERE (" .
					"jec.login = '".$ident_eleve[$cpt_info_eleve]."' AND " .
					"jec.periode = '".$id_periode."' AND " .
					"jeg.login = jec.login AND " .
					"jeg.periode = jec.periode AND " .
					"jgc.id_classe = jec.id_classe AND " .
					"jgc.id_groupe = jeg.id_groupe AND " .
					"jgm.id_groupe = jeg.id_groupe AND " .
					"m.matiere = jgm.id_matiere" .
				") " .
				"GROUP BY id_groupe ORDER BY jgc.priorite,m.nom_complet";

			//============================
		}
		$requete_toute_matier = mysql_query($sql);
		//echo "$sql<br />";

		// compteur du nombre de matière
//		$nombre_de_matiere = mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'matieres_notes, '.$prefix_base.'j_groupes_matieres, '.$prefix_base.'matieres, '.$prefix_base.'groupes, '.$prefix_base.'matieres_categories WHERE '.$prefix_base.'matieres_notes.login = "'.$ident_eleve[$cpt_info_eleve].'" AND '.$prefix_base.'matieres_notes.periode = "'.$id_periode.'" AND '.$prefix_base.'j_groupes_matieres.id_groupe='.$prefix_base.'groupes.id AND '.$prefix_base.'j_groupes_matieres.id_matiere='.$prefix_base.'matieres.matiere AND '.$prefix_base.'matieres_notes.id_groupe = '.$prefix_base.'groupes.id AND '.$prefix_base.'matieres.categorie_id='.$prefix_base.'matieres_categories.id ORDER BY '.$prefix_base.'matieres_categories.id ASC'),0);
		//login de l'élève
		$login_eleve_select = $ident_eleve[$cpt_info_eleve]; // login de l'élève
		// mise à 0 des totals coef
		$total_coef='0';

		// DEBUT information AID en début de bulletin
		$requete_aid = "SELECT * FROM aid a, aid_config ac, j_aid_eleves jae, aid_appreciations aa
				 	 WHERE ac.indice_aid = a.indice_aid
				 	   AND ac.indice_aid = jae.indice_aid
				 	   AND ac.indice_aid = aa.indice_aid
 	   				   AND ac.display_bulletin = 'y'
 	   				   AND aa.login = '".$ident_eleve[$cpt_info_eleve]."'
					   AND aa.login = jae.login
 	   				   AND aa.periode = '".$id_periode."'
					   AND ac.order_display1 = 'b'
					   AND a.id = aa.id_aid
					 ORDER BY ac.order_display2 ASC";
		$resultat_aid = mysql_query($requete_aid);

		while ($donner_aid = mysql_fetch_array($resultat_aid))
		{
			$id_groupe_aff = $donner_aid['indice_aid'];
			$nom_aid_select = $donner_aid[1];

			// information AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['matiere'] = $donner_aid['nom']; // nom court de l'AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['id_groupe'] = $donner_aid['indice_aid']; // id de l'AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'] = 'AID'; // nom de la catégorie de la matière
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['affiche_moyenne'] = '1'; // afficher ou ne pas afficher la moyenne de la catégorie

			// calcule du nombre d'élève fesant partie de ce groupe
			if(empty($nb_eleve_groupe[$id_groupe_aff])) { $nb_eleve_groupe[$id_groupe_aff]= mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'j_aid_eleves jae, '.$prefix_base.'aid_config ac, '.$prefix_base.'aid_appreciations aa WHERE ac.indice_aid = jae.indice_aid AND aa.indice_aid = ac.indice_aid AND aa.periode = "'.$id_periode.'" AND ac.indice_aid = "'.$id_groupe_aff.'" AND jae.login = aa.login'),0); }
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_eleve_rang'] = $nb_eleve_groupe[$id_groupe_aff];

			// désactiver pour l'instant - 20071106
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_eleve_rang'] = '-';
			// fin

			//calcule des moyennes du groupe
			$groupe_matiere = $donner_aid['indice_aid']; // id du groupe de la matière sélectionné
			$moyenne_general_groupe[$groupe_matiere] = calcul_toute_moyenne_aid ($id_groupe_aff, $id_periode); // on récupère les donnes le tableau des moyennes moyenne_classe/min/max d'un groupe/nombre de note
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe'] = $moyenne_general_groupe[$groupe_matiere][0]; // moyenne du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min'] = $moyenne_general_groupe[$groupe_matiere][1]; // moyenne minimal du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max'] = $moyenne_general_groupe[$groupe_matiere][2]; //moyenne maximal du groupe

			//calcule du nombre de note dans une période donner pour un groupe
			if($active_nombre_note[$classe_id]==='1' or $active_nombre_note_case[$classe_id] === '1') {
			// Nombre total de devoirs
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_total_notes_matiere'] = '';
			// Nombre de devoir de l'élève
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_notes_matiere'] = '';
			}

			// autre requete pour rechercher les professeur responsable de la matière sélectionné
			$call_profs = mysql_query('SELECT * FROM '.$prefix_base.'aid a, '.$prefix_base.'j_aid_utilisateurs jau, '.$prefix_base.'utilisateurs u
							 	  WHERE ( jau.indice_aid = "'.$id_groupe_aff.'"
							            AND jau.id_utilisateur = u.login
								    AND a.id = jau.id_aid
								    AND a.indice_aid = jau.indice_aid
								    AND a.nom = "'.$nom_aid_select.'"
								    )');
			$nombre_profs = mysql_num_rows($call_profs);
			$k = 0;
			while ($k < $nombre_profs) {
					$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
					$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['prof'][$k]=affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe);
					$k++;
			}

			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] = $donner_aid['note']; // moyenne de l'élève pour une matière donnée dans une périodes données
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['rang'] = '-'; // rang de l'élève pour une matière donnée dans une périodes données

			$note_rang = '';

			// appréciation
			$apprec_aid = '';
			if ($donner_aid['message'] != '') {
		            $apprec_aid = $donner_aid['message'];
		        }
			if ($donner_aid['display_nom'] === 'y') {
		            $apprec_aid = $apprec_aid.' '.$donner_aid['nom_complet'].' : ';
		        }
			if (($donner_aid['statut'] === '') and ($donner_aid['note_max'] != 20) ) {
		            $apprec_aid = $apprec_aid.' (note sur '.$donner_aid['note_max'].')';
		        }
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['appreciation'] = $apprec_aid.' '.$donner_aid['appreciation'];

			// connaitre le coefficient de la matière
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = '0';
			$coef_matiere[$id_classe][$groupe_matiere]['coef'] = '0';

			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != ''
				and $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-') {
			$total_coef =$total_coef+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];
			}

			//calcule des moyennes par catégorie
			if($active_entete_regroupement[$classe_id]==='1') {
			$categorie_passage = $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'];
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve'] = '0'; }

			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];

			if(empty($matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'])) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'] = 0; }
			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-' ) {
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']+$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			}
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max'] = '0'; }
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			}

			//total pour la moyenne général
			$moy_general_eleve = $moy_general_eleve+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];

			// gestion des graphique de niveau par matière
			if ($active_graphique_niveau[$classe_id]==='1' and empty($data_grap[$id_periode][$id_groupe_aff][0])) {
								$data_grap[$id_periode][$id_groupe_aff][0] = sql_query1("SELECT COUNT( note ) as quartile1 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=15)");
								$data_grap[$id_periode][$id_groupe_aff][1] = sql_query1("SELECT COUNT( note ) as quartile2 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=12 AND note<15)");
								$data_grap[$id_periode][$id_groupe_aff][2] = sql_query1("SELECT COUNT( note ) as quartile3 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=10 AND note<12)");
								$data_grap[$id_periode][$id_groupe_aff][3] = sql_query1("SELECT COUNT( note ) as quartile4 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=8 AND note<10)");
								$data_grap[$id_periode][$id_groupe_aff][4] = sql_query1("SELECT COUNT( note ) as quartile5 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=5 AND note<8)");
								$data_grap[$id_periode][$id_groupe_aff][5] = sql_query1("SELECT COUNT( note ) as quartile6 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note<5)");
							}
			$nombre_de_matiere = $nombre_de_matiere + 1;

		$cpt_info_eleve_matiere=$cpt_info_eleve_matiere+1;
		}
		// FIN information AID en début de bulletin


		// DEBUT information ENSEIGNEMENT sur le bulletin
		while ($donner_toute_matier = mysql_fetch_array($requete_toute_matier))
		{
			$id_groupe_aff=$donner_toute_matier['id_groupe'];

			// ses matières
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['matiere'] = $donner_toute_matier['nom_long_matiere']; // nom long de la matière je ne peut utilise le nom_complet car il est déjas utiliser avec les catégorie
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['id_groupe'] = $donner_toute_matier['id_groupe']; // id du groupe

			//============================
			// Modif: boireaus 20070828
			if($active_regroupement_cote[$classe_id]==='1' or $active_entete_regroupement[$classe_id]==='1') {
				$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'] = $donner_toute_matier['nom_categorie']; // nom de la catégorie de la matière
			}
			//============================

			//$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['affiche_moyenne'] = $donner_toute_matier['affiche_moyenne']; // afficher ou ne pas afficher la moyenne de la catégorie
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['affiche_moyenne'] = '1'; // afficher ou ne pas afficher la moyenne de la catégorie

			// calcule du nombre d'élève fesant partie de ce groupe
			if(empty($nb_eleve_groupe[$id_groupe_aff])) { $nb_eleve_groupe[$id_groupe_aff]= mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'j_eleves_groupes WHERE periode="'.$id_periode.'" AND id_groupe="'.$id_groupe_aff.'"'),0); }
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_eleve_rang'] = $nb_eleve_groupe[$id_groupe_aff];

			//calcule des moyennes du groupe
			$groupe_matiere = $donner_toute_matier['id_groupe']; // id du groupe de la matière sélectionné
			$moyenne_general_groupe[$groupe_matiere] = calcul_toute_moyenne_classe ($groupe_matiere, $id_periode); // on récupère les donnes le tableau des moyennes moyenne_classe/min/max d'un groupe/nombre de note
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe'] = $moyenne_general_groupe[$groupe_matiere][0]; // moyenne du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min'] = $moyenne_general_groupe[$groupe_matiere][1]; // moyenne minimal du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max'] = $moyenne_general_groupe[$groupe_matiere][2]; //moyenne maximal du groupe

			//calcule du nombre de note dans une période donner pour un groupe
			if($active_nombre_note[$classe_id]==='1' or $active_nombre_note_case[$classe_id] === '1') {
			// Nombre total de devoirs
			$sql="SELECT cd.id FROM cn_devoirs cd, cn_cahier_notes ccn WHERE (cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$id_groupe_aff."' AND ccn.periode='".$id_periode."');";
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_total_notes_matiere'] = mysql_num_rows(mysql_query($sql));
			//$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_notes_matiere']=mysql_result(mysql_query('SELECT count(*) FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (nd.login = "'.$login_eleve_select.'" and nd.id_devoir = d.id and d.display_parents="1" and cn.id_groupe = "'.$id_groupe_aff.'" and d.id_racine = cn.id_cahier_notes AND cn.periode="1")'),0);
			// Nombre de devoir de l'élève
			$sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login='".$login_eleve_select."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$id_groupe_aff."' AND ccn.periode='".$id_periode."' AND cnd.statut='');";
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_notes_matiere'] = mysql_num_rows(mysql_query($sql));
			}

			// autre requete pour rechercher les professeur responsable de la matière sélectionné
			$call_profs = mysql_query('SELECT u.login FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe="'.$id_groupe_aff.'") ORDER BY j.ordre_prof');
			$nombre_profs = mysql_num_rows($call_profs);
			$k = 0;
			while ($k < $nombre_profs) {
					$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
					$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['prof'][$k]=affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe);
					$k++;
			}

			$res_note_rang = mysql_query("SELECT note, statut, rang " .
					"FROM matieres_notes WHERE (" .
					"login='".$login_eleve_select."' AND " .
					"id_groupe='".$groupe_matiere."' AND " .
					"periode='".$id_periode."')");
			if (mysql_num_rows($res_note_rang) > 0) {
				$note_rang=mysql_fetch_array($res_note_rang);
				if($note_rang['statut']!=''){
					//$note_rang['note']=$note_rang['statut'];
					$note_rang['note']="-";
					// Le tiret est accepté à l'affichage pour la note, mais pas 'disp'???
					$note_rang['rang']="-";
				}
			} else {
				$note_rang = array("note" => "-", "rang" => "-");
			}

			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] = $note_rang['note']; // moyenne de l'élève pour une matière donnée dans une périodes données
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['rang'] = $note_rang['rang']; // rang de l'élève pour une matière donnée dans une périodes données

			$note_rang = '';

			// autre requete pour rechercher les appréciation d'une matière pour une période donné
			$appreciation = mysql_fetch_array(mysql_query('SELECT * FROM '.$prefix_base.'matieres_appreciations WHERE login="'.$login_eleve_select.'" AND id_groupe="'.$groupe_matiere.'" AND periode="'.$id_periode.'"'));
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['appreciation'] = $appreciation['appreciation'];
			$appreciation=''; //remise à vide de la variable

			// connaitre le coefficient de la matière
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef']='1';

			// On teste si l'élève a un coef spécifique pour cette matière
			// ==================================================
			// MODIF: boireaus
			/*
			$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
					"login = '".$current_eleve_login[$i]."' AND " .
					"id_groupe = '".$current_group[$j]["id"]."' AND " .
					"name = 'coef')");
			*/



			/*
			$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
					"login = '".$login_eleve_select."' AND " .
					"id_groupe = '".$groupe_matiere."' AND " .
					"name = 'coef')");
			// ==================================================
			if (mysql_num_rows($test_coef_eleve) > 0) {
				$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = mysql_result($test_coef_eleve, 0);
			} else {
			*/
				if(empty($coef_matiere[$id_classe][$groupe_matiere]['coef'])) // si on le connait on ne retourne pas le chercher
				{
					$coef_matiere[$id_classe][$groupe_matiere] = mysql_fetch_array(mysql_query('SELECT * FROM '.$prefix_base.'j_groupes_classes WHERE id_classe="'.$id_classe.'" AND id_groupe="'.$groupe_matiere.'"'));
					if($coef_matiere[$id_classe][$groupe_matiere]['coef']!=0.0)
					{
						$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = $coef_matiere[$id_classe][$groupe_matiere]['coef'];
					} else {
						$coef_matiere[$id_classe][$groupe_matiere]['coef'] = '0';
						$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = '0';
					}
				} else {
					$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = $coef_matiere[$id_classe][$groupe_matiere]['coef'];
				}
			//}

			$test_coef_eleve = mysql_query("SELECT value FROM eleves_groupes_settings WHERE (" .
					"login = '".$login_eleve_select."' AND " .
					"id_groupe = '".$groupe_matiere."' AND " .
					"name = 'coef')");
			// ==================================================
			if (mysql_num_rows($test_coef_eleve) > 0) {
				$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = mysql_result($test_coef_eleve, 0);
			}



			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != ''
				and $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-') {
			$total_coef =$total_coef+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];
			}
			//$total_coef = $total_coef+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];

			//calcule des moyennes par catégorie
			if($active_entete_regroupement[$classe_id]==='1') {
			$categorie_passage = $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'];
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve'] = '0'; }

			//echo "\$coef_matiere[$id_classe][$groupe_matiere]['coef']=".$coef_matiere[$id_classe][$groupe_matiere]['coef']."<br />";

			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];

			//			 $matiere[$login_eleve_select][$id_periode][$categorie_passage][nb_moy_eleve]=$matiere[$login_eleve_select][$id_periode][$categorie_passage][nb_moy_eleve]+1;

			if(empty($matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'])) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'] = 0; }
			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-' ) {
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']+$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			}
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max'] = '0'; }
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
//			 $matiere[$login_eleve_select][$id_periode][$categorie_passage][nb_moy_classe]=$matiere[$login_eleve_select][$id_periode][$categorie_passage][nb_moy_classe]+1;
			}

			//total pour la moyenne général
			$moy_general_eleve = $moy_general_eleve+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];

			// gestion des graphique de niveau par matière
			if ($active_graphique_niveau[$classe_id]==='1' and empty($data_grap[$id_periode][$id_groupe_aff][0])) {
								$data_grap[$id_periode][$id_groupe_aff][0] = sql_query1("SELECT COUNT( note ) as quartile1 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note>=15)");
								$data_grap[$id_periode][$id_groupe_aff][1] = sql_query1("SELECT COUNT( note ) as quartile2 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note>=12 AND note<15)");
								$data_grap[$id_periode][$id_groupe_aff][2] = sql_query1("SELECT COUNT( note ) as quartile3 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note>=10 AND note<12)");
								$data_grap[$id_periode][$id_groupe_aff][3] = sql_query1("SELECT COUNT( note ) as quartile4 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note>=8 AND note<10)");
								$data_grap[$id_periode][$id_groupe_aff][4] = sql_query1("SELECT COUNT( note ) as quartile5 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note>=5 AND note<8)");
								$data_grap[$id_periode][$id_groupe_aff][5] = sql_query1("SELECT COUNT( note ) as quartile6 FROM matieres_notes WHERE (periode='".$id_periode."' AND id_groupe='".$id_groupe_aff."' AND statut ='' AND note<5)");
							}

			//on cherche s'il faut affiche des sous matière pour cette matière
			$test_cn = mysql_query('SELECT cnc.note, cnc.statut, c.nom_court, c.id from '.$prefix_base.'cn_cahier_notes cn, '.$prefix_base.'cn_conteneurs c, '.$prefix_base.'cn_notes_conteneurs cnc WHERE cnc.login="'.$login_eleve_select.'" AND cn.periode="'.$id_periode.'" AND cn.id_groupe="'.$id_groupe_aff.'" AND cn.id_cahier_notes = c.id_racine AND c.id_racine!=c.id AND c.display_bulletin="1" AND cnc.id_conteneur=c.id');
				$nb_ligne_cn = mysql_num_rows($test_cn);
				$n = 0;
			$sous_matiere[$login_eleve_select][$id_periode][$id_groupe_aff]['nb']=$nb_ligne_cn;
				while ($n < $nb_ligne_cn) {
			$sous_matiere[$login_eleve_select][$id_periode][$id_groupe_aff][$n]['titre']=mysql_result($test_cn, $n, 'c.nom_court');
			$sous_matiere[$login_eleve_select][$id_periode][$id_groupe_aff][$n]['moyenne']=mysql_result($test_cn, $n, 'cnc.note');
					$n++;
					}

			$nombre_de_matiere = $nombre_de_matiere + 1;

		$cpt_info_eleve_matiere=$cpt_info_eleve_matiere+1;
		}
		// FIN information ENSEIGNEMENT sur le bulletin

		// DEBUT information AID en fin de bulletin
		$requete_aid = "SELECT * FROM aid a, aid_config ac, j_aid_eleves jae, aid_appreciations aa
				 	 WHERE ac.indice_aid = a.indice_aid
				 	   AND ac.indice_aid = jae.indice_aid
				 	   AND ac.indice_aid = aa.indice_aid
 	   				   AND ac.display_bulletin = 'y'
 	   				   AND aa.login = '".$ident_eleve[$cpt_info_eleve]."'
					   AND aa.login = jae.login
 	   				   AND aa.periode = '".$id_periode."'
					   AND ac.order_display1 = 'e'
					   AND a.id = aa.id_aid
					 ORDER BY ac.order_display2 ASC";
		$resultat_aid = mysql_query($requete_aid);

		while ($donner_aid = mysql_fetch_array($resultat_aid))
		{
			$id_groupe_aff = $donner_aid['indice_aid'];
			$nom_aid_select =  $donner_aid[1];

			// information AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['matiere'] = $donner_aid['nom']; // nom court de l'AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['id_groupe'] = $donner_aid['indice_aid']; // id de l'AID
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'] = 'AID'; // nom de la catégorie de la matière
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['affiche_moyenne'] = '1'; // afficher ou ne pas afficher la moyenne de la catégorie

			// calcule du nombre d'élève fesant partie de ce groupe
			if(empty($nb_eleve_groupe[$id_groupe_aff])) { $nb_eleve_groupe[$id_groupe_aff]= mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'j_aid_eleves jae, '.$prefix_base.'aid_config ac, '.$prefix_base.'aid_appreciations aa WHERE ac.indice_aid = jae.indice_aid AND aa.indice_aid = ac.indice_aid AND aa.periode = "'.$id_periode.'" AND ac.indice_aid = "'.$id_groupe_aff.'" AND jae.login = aa.login'),0); }
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_eleve_rang'] = $nb_eleve_groupe[$id_groupe_aff];

			// désactiver pour l'instant - 20071106
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_eleve_rang'] = '-';
			// fin

			//calcule des moyennes du groupe
			$groupe_matiere = $donner_aid['indice_aid']; // id du groupe de la matière sélectionné
                        $moyenne_general_groupe[$groupe_matiere] = calcul_toute_moyenne_aid($groupe_matiere, $id_periode); // on récupère les donnes le tableau des moyennes moyenne_classe/min/max d'un groupe/nombre de note
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe'] = $moyenne_general_groupe[$groupe_matiere][0]; // moyenne du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min'] = $moyenne_general_groupe[$groupe_matiere][1]; // moyenne minimal du groupe
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max'] = $moyenne_general_groupe[$groupe_matiere][2]; //moyenne maximal du groupe

			//calcule du nombre de note dans une période donner pour un groupe
			if($active_nombre_note[$classe_id]==='1' or $active_nombre_note_case[$classe_id] === '1') {
			// Nombre total de devoirs
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_total_notes_matiere'] = '';
			// Nombre de devoir de l'élève
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['nb_notes_matiere'] = '';
			}

			// autre requete pour rechercher les professeur responsable de la matière sélectionné
			$call_profs = mysql_query('SELECT * FROM '.$prefix_base.'aid a, '.$prefix_base.'j_aid_utilisateurs jau, '.$prefix_base.'utilisateurs u
							 	  WHERE ( jau.indice_aid = "'.$id_groupe_aff.'"
							            AND jau.id_utilisateur = u.login
								    AND a.id = jau.id_aid
								    AND a.indice_aid = jau.indice_aid
								    AND a.nom = "'.$nom_aid_select.'"
								    )');
			$nombre_profs = mysql_num_rows($call_profs);
			$k = 0;
			while ($k < $nombre_profs) {
					$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
					$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['prof'][$k]=affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe);
					$k++;
			}

			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] = $donner_aid['note']; // moyenne de l'élève pour une matière donnée dans une périodes données
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['rang'] = '-'; // rang de l'élève pour une matière donnée dans une périodes données

			$note_rang = '';

			// appréciation
			$apprec_aid = '';
			if ($donner_aid['message'] != '') {
		            $apprec_aid = $donner_aid['message'];
		        }
			if ($donner_aid['display_nom'] === 'y') {
		            $apprec_aid = $apprec_aid.' '.$donner_aid['nom_complet'].' : ';
		        }
			if (($donner_aid['statut'] === '') and ($donner_aid['note_max'] != 20) ) {
		            $apprec_aid = $apprec_aid.' (note sur '.$donner_aid['note_max'].')';
		        }
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['appreciation'] = $apprec_aid.' '.$donner_aid['appreciation'];

			// connaitre le coefficient de la matière
			$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'] = '0';
			$coef_matiere[$id_classe][$groupe_matiere]['coef'] = '0';

			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != ''
				and $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-') {
			$total_coef =$total_coef+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];
			}

			//calcule des moyennes par catégorie
			if($active_entete_regroupement[$classe_id]==='1') {
			$categorie_passage = $matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['categorie'];
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve'] = '0'; }

			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_eleve']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];

			if(empty($matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'])) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego'] = 0; }
			if ($matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve'] != '-' ) {
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['coef_tt_catego']+$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			}
			if ( !isset($matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']) ) { $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min'] = '0'; $matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max'] = '0'; }
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_classe']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_classe']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_min']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_min']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']=$matiere[$login_eleve_select][$id_periode][$categorie_passage]['moy_max']+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_max']*$coef_matiere[$id_classe][$groupe_matiere]['coef'];
			}

			//total pour la moyenne général
			$moy_general_eleve = $moy_general_eleve+$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['moy_eleve']*$matiere[$login_eleve_select][$id_periode][$cpt_info_eleve_matiere]['coef'];

			// gestion des graphique de niveau par matière
			if ($active_graphique_niveau[$classe_id]==='1' and empty($data_grap[$id_periode][$id_groupe_aff][0])) {
								$data_grap[$id_periode][$id_groupe_aff][0] = sql_query1("SELECT COUNT( note ) as quartile1 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=15)");
								$data_grap[$id_periode][$id_groupe_aff][1] = sql_query1("SELECT COUNT( note ) as quartile2 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=12 AND note<15)");
								$data_grap[$id_periode][$id_groupe_aff][2] = sql_query1("SELECT COUNT( note ) as quartile3 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=10 AND note<12)");
								$data_grap[$id_periode][$id_groupe_aff][3] = sql_query1("SELECT COUNT( note ) as quartile4 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=8 AND note<10)");
								$data_grap[$id_periode][$id_groupe_aff][4] = sql_query1("SELECT COUNT( note ) as quartile5 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note>=5 AND note<8)");
								$data_grap[$id_periode][$id_groupe_aff][5] = sql_query1("SELECT COUNT( note ) as quartile6 FROM aid_appreciations WHERE (periode='".$id_periode."' AND indice_aid='".$id_groupe_aff."' AND statut ='' AND note<5)");
							}
			$nombre_de_matiere = $nombre_de_matiere + 1;

		$cpt_info_eleve_matiere=$cpt_info_eleve_matiere+1;
		}
		// FIN information AID en fin de bulletin
		

		// attribue le nombre de matière pour un élève donner et une période
		$info_bulletin[$login_eleve_select][$id_periode]['nb_matiere']=$nombre_de_matiere;
		//calcule de la moyenne général de l'élève pour un période données
		// if($nombre_de_matiere!=0) { $info_bulletin[$login_eleve_select][$id_periode][moy_general_eleve] = $moy_general_eleve/$info_bulletin[$login_eleve_select][$id_periode][nb_matiere]; } else { $info_bulletin[$login_eleve_select][$id_periode][moy_general_eleve]=0; }
		if($nombre_de_matiere!=0) {
			if($total_coef>0){
				$info_bulletin[$login_eleve_select][$id_periode]['moy_general_eleve'] = $moy_general_eleve/$total_coef;
			}
			else{
				$info_bulletin[$login_eleve_select][$id_periode]['moy_general_eleve'] = "-";
			}
		}
		else {
			$info_bulletin[$login_eleve_select][$id_periode]['moy_general_eleve']='0';
		}
		$moy_gene_eleve = $info_bulletin[$login_eleve_select][$id_periode]['moy_general_eleve'];

		// gestion des graphique de niveau pour la classe
//		if ($active_graphique_niveau==='1') {
			// initialisation à 0 si vide
			if(empty($data_grap_classe[$id_periode][$id_classe][0])) { $data_grap_classe[$id_periode][$id_classe][0]='0'; }
			if(empty($data_grap_classe[$id_periode][$id_classe][1])) { $data_grap_classe[$id_periode][$id_classe][1]='0'; }
			if(empty($data_grap_classe[$id_periode][$id_classe][2])) { $data_grap_classe[$id_periode][$id_classe][2]='0'; }
			if(empty($data_grap_classe[$id_periode][$id_classe][3])) { $data_grap_classe[$id_periode][$id_classe][3]='0'; }
			if(empty($data_grap_classe[$id_periode][$id_classe][4])) { $data_grap_classe[$id_periode][$id_classe][4]='0'; }
			if(empty($data_grap_classe[$id_periode][$id_classe][5])) { $data_grap_classe[$id_periode][$id_classe][5]='0'; }

			// mini et maxi de la classe
			if(empty($moyenne_classe_minmax[$id_periode][$id_classe]['min'])) { $moyenne_classe_minmax[$id_periode][$id_classe]['min']='20'; }
			if(empty($moyenne_classe_minmax[$id_periode][$id_classe]['max'])) { $moyenne_classe_minmax[$id_periode][$id_classe]['max']='0'; }
			//=====================================
			// MODIF: boireaus 20070616
			if($moy_gene_eleve!="-"){
				if($moyenne_classe_minmax[$id_periode][$id_classe]['min']>$moy_gene_eleve) { $moyenne_classe_minmax[$id_periode][$id_classe]['min'] = $moy_gene_eleve; }
				if($moyenne_classe_minmax[$id_periode][$id_classe]['max']<$moy_gene_eleve) { $moyenne_classe_minmax[$id_periode][$id_classe]['max'] = $moy_gene_eleve; }
			}
			//=====================================

			if ($moy_gene_eleve >= 15) { $data_grap_classe[$id_periode][$id_classe][0]=$data_grap_classe[$id_periode][$id_classe][0]+1; }
			else if (($moy_gene_eleve >= 12) and ($moy_gene_eleve < 15)) { $data_grap_classe[$id_periode][$id_classe][1]=$data_grap_classe[$id_periode][$id_classe][1]+1; }
			else if (($moy_gene_eleve >= 10) and ($moy_gene_eleve < 12)) { $data_grap_classe[$id_periode][$id_classe][2]=$data_grap_classe[$id_periode][$id_classe][2]+1; }
				else if (($moy_gene_eleve >= 8) and ($moy_gene_eleve < 10)) { $data_grap_classe[$id_periode][$id_classe][3]=$data_grap_classe[$id_periode][$id_classe][3]+1; }
				else if (($moy_gene_eleve >= 5) and ($moy_gene_eleve < 8)) { $data_grap_classe[$id_periode][$id_classe][4]=$data_grap_classe[$id_periode][$id_classe][4]+1; }
				else {
					//=====================================
					// MODIF: boireaus 20070616
					if($moy_gene_eleve!="-"){
						$data_grap_classe[$id_periode][$id_classe][5]=$data_grap_classe[$id_periode][$id_classe][5]+1;
					}
					//=====================================
				}
//                }

		//avis du conseil de classe pour un élève et une période donnée
		$avis_conseil_de_classe = mysql_fetch_array(mysql_query('SELECT * FROM avis_conseil_classe WHERE login="'.$login_eleve_select.'" AND periode="'.$id_periode.'"'));
			$info_bulletin[$login_eleve_select][$id_periode]['avis_conseil_classe'] = $avis_conseil_de_classe['avis'];
		$avis_conseil_de_classe=''; //remise à vide de la variable

		//connaitre l'effectif de la classe
		if(empty($classe_effectif_tab[$id_classe][$id_periode]['effectif'])) // si on le connait on ne retourne pas le chercher
		{
			$info_bulletin[$login_eleve_select][$id_periode]['effectif'] = mysql_result(mysql_query('SELECT count(*) FROM '.$prefix_base.'j_eleves_classes WHERE id_classe="'.$id_classe.'" AND periode="'.$id_periode.'"'),0);
			$classe_effectif_tab[$id_classe][$id_periode]['effectif'] = $info_bulletin[$login_eleve_select][$id_periode]['effectif'];
		} else { $info_bulletin[$login_eleve_select][$id_periode]['effectif'] = $classe_effectif_tab[$id_classe][$id_periode]['effectif']; }

		// rang de l'élève dans la classe
		$rang_eleve_classe_requete = mysql_query('SELECT rang FROM '.$prefix_base.'j_eleves_classes WHERE periode="'.$id_periode.'" AND id_classe="'.$id_classe.'" AND login="'.$login_eleve_select.'"');
		$rang_eleve_classe[$login_eleve_select][$id_periode]=@mysql_result($rang_eleve_classe_requete, "0", "rang");
				if ((isset($rang_eleve_classe[$cpt_i]) and $rang_eleve_classe[$cpt_i] === 0) or (isset($rang_eleve_classe[$cpt_i]) and $rang_eleve_classe[$cpt_i] == -1)) { $rang_eleve_classe[$cpt_i] = "-"; } else { $rang_eleve_classe[$cpt_i] = ''; }

		//absences de l'élève
		$current_eleve_absences_query = mysql_query('SELECT * FROM absences WHERE (login="'.$login_eleve_select.'" AND periode="'.$id_periode.'")');
			$info_bulletin[$login_eleve_select][$id_periode]['absences'] = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
			$info_bulletin[$login_eleve_select][$id_periode]['absences_nj'] = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
			$info_bulletin[$login_eleve_select][$id_periode]['absences_retards'] = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
			$info_bulletin[$login_eleve_select][$id_periode]['absences_appreciation'] = @mysql_result($current_eleve_absences_query, 0, "appreciation");
			if($info_bulletin[$login_eleve_select][$id_periode]['absences'] == '') { $info_bulletin[$login_eleve_select][$id_periode]['absences'] = "?"; }
			if($info_bulletin[$login_eleve_select][$id_periode]['absences_nj'] == '') { $info_bulletin[$login_eleve_select][$id_periode]['absences_nj'] = "?"; }
			if($info_bulletin[$login_eleve_select][$id_periode]['absences_retards']=='') { $info_bulletin[$login_eleve_select][$id_periode]['absences_retards'] = "?"; }

		//haut responsable de la classe
		if(empty($info_classe[$id_classe]['nom_hautresponsable']))
		{
		$calldata = mysql_query('SELECT * FROM '.$prefix_base.'classes WHERE id="'.$id_classe.'"');
		$info_classe[$id_classe]['fonction_hautresponsable']=mysql_result($calldata, 0, "formule");
		$info_classe[$id_classe]['nom_hautresponsable']= @mysql_result($calldata, 0, "suivi_par");
		}

	$cpt_info_periode=$cpt_info_periode+1;
	}
$cpt_info_eleve=$cpt_info_eleve+1;
}

// christian pdf
//echo "<pre>";
//print_r($matiere);
//echo "</pre>";
	// définition d'une variable
	$hauteur_pris = 0;

// début de la génération du fichier PDF
$pdf=new bul_PDF('p', 'mm', 'A4'); //création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
$nb_eleve_aff = 1;
$categorie_passe = '';
$categorie_passe_count = 0;
if(empty($gepiSchoolName)) { $gepiSchoolName=getSettingValue('gepiSchoolName'); }
$pdf->SetCreator($gepiSchoolName);
$pdf->SetAuthor($gepiSchoolName);
$pdf->SetKeywords('');
$pdf->SetSubject('Bulletin');
$pdf->SetTitle('Bulletin');
$pdf->SetDisplayMode('fullwidth', 'single');
$pdf->SetCompression(TRUE);
$pdf->SetAutoPageBreak(TRUE, 5);

$responsable_place = 0;

while(!empty($nom_eleve[$nb_eleve_aff])) {
    $ident_eleve_aff = $ident_eleve[$nb_eleve_aff];
	$cpt_info_periode=0;
	$id_classe_selection = $classe_tableau_id[$nb_eleve_aff]; // classe de l'élève

	// AJOUT ERIC on récupère l'id de la classe pour les paramètres.
	$classe_id=$id_classe_selection;

	// quand on change d'élève on vide les variables suivante
	$categorie_passe = '';

	$total_moyenne_classe_en_calcul=0; $total_moyenne_min_en_calcul=0; $total_moyenne_max_en_calcul=0; $total_coef_en_calcul=0;

	//boucle pour chaque période d'un élève
	while(!empty($periode_classe[$id_classe_selection][$cpt_info_periode]))
	{

	$pdf->AddPage(); //ajout d'une page au document
	$pdf->SetFont('Arial');
//DEBUT ENTETE BULLETIN
	//Affiche le filigrame
	if($affiche_filigrame[$classe_id]==='1')
	{
	$pdf->SetFont('Arial','B',50);
	$pdf->SetTextColor(255,192,203);
	$pdf->TextWithRotation(40,190,$texte_filigrame[$classe_id],45);
	$pdf->SetTextColor(0,0,0);
	}

	//bloc identification etablissement
	$logo = '../images/'.getSettingValue('logo_etab');
	$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));
	if($affiche_logo_etab[$classe_id]==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
	{
	$valeur=redimensionne_image($logo, $L_max_logo[$classe_id], $H_max_logo[$classe_id]);
	$X_logo = 5;
	$Y_logo = 5;
	$L_logo = $valeur[0];
	$H_logo = $valeur[1];
	$X_etab = $X_logo + $L_logo + 1;
	$Y_etab = $Y_logo;

	if ( !isset($centrage_logo[$classe_id]) or empty($centrage_logo[$classe_id]) ) { $centrage_logo[$classe_id] = '0'; }
	if ( $centrage_logo[$classe_id] === '1' ) {
		// centrage du logo
		$centre_du_logo = ( $H_logo / 2 );
		$Y_logo = $Y_centre_logo[$classe_id] - $centre_du_logo;
	}

	//logo
		$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
	}

	//adresse
	if ( !isset($X_etab) or empty($X_etab) ) { $X_etab = '5'; $Y_etab = '5'; }
	$pdf->SetXY($X_etab,$Y_etab);
	$pdf->SetFont($caractere_utilse[$classe_id],'',14);
//	 $pdf->SetFont($caractere_utilse[$classe_id],'',$taille);
	$gepiSchoolName = getSettingValue('gepiSchoolName');

	// mettre en gras le nom de l'établissement si $nom_etab_gras = 1
	if ( $nom_etab_gras[$classe_id] === '1' ) { $pdf->SetFont($caractere_utilse[$classe_id],'B',14); }
	$pdf->Cell(90,7, $gepiSchoolName,0,2,'');

	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');

	if ( $gepiSchoolAdress1 != '' ) {
	$pdf->Cell(90,5, $gepiSchoolAdress1,0,2,'');
	}
	$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');

	if ( $gepiSchoolAdress2 != '' ) {
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

	if( $entente_tel[$classe_id]==='1' ) {
		$grandeur = ''; $text_tel = '';
		if ( $tel_image[$classe_id] != '' ) {
			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../images/imabulle/'.$tel_image[$classe_id].'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_tel = '      '.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);
			$grandeur = $grandeur + 2;
		}
		if ( $tel_texte[$classe_id] != '' and $tel_image[$classe_id] === '' ) {
			$text_tel = $tel_texte[$classe_id].''.$gepiSchoolTel;
			$grandeur = $pdf->GetStringWidth($text_tel);
		}

		$pdf->Cell($grandeur,5, $text_tel,0,$passealaligne,'');
	}

	$passealaligne = '2';
	// entête fax
	if( $entente_fax[$classe_id]==='1' ) {
		$text_fax = '';
		if ( $fax_image[$classe_id] != '' ) {
			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../images/imabulle/'.$fax_image[$classe_id].'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_fax = '      '.$gepiSchoolFax;
		}
		if ( $fax_texte[$classe_id] != '' and $fax_image[$classe_id] === '' ) {
			$text_fax = $fax_texte[$classe_id].''.$gepiSchoolFax;
		}
		$pdf->Cell(90,5, $text_fax,0,$passealaligne,'');
	}


	if($entente_mel[$classe_id]==='1') {
		$text_mel = '';
		$y_telecom = $y_telecom + 5;
		$pdf->SetXY($x_telecom,$y_telecom);
	$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
		$text_mel = $gepiSchoolEmail;
		if ( $courrier_image[$classe_id] != '' ) {
			$a = $pdf->GetX();
			$b = $pdf->GetY();
			$ima = '../images/imabulle/'.$courrier_image[$classe_id].'.jpg';
			$valeurima=redimensionne_image($ima, 15, 15);
			$pdf->Image($ima, $a, $b, $valeurima[0], $valeurima[1]);
			$text_mel = '      '.$gepiSchoolEmail;
		}
		if ( $courrier_texte[$classe_id] != '' and $courrier_image[$classe_id] === '' ) {
			$text_mel = $courrier_texte[$classe_id].' '.$gepiSchoolEmail;
		}
		$pdf->Cell(90,5, $text_mel,0,2,'');
	}
// FIN ENTETE BULLETIN


	$i = $nb_eleve_aff;
	$id_periode = $periode_classe[$id_classe_selection][$cpt_info_periode];

	// AJOUT ERIC
	$classe_id=$id_classe_selection;

	$pdf->SetFont('Arial','B',12);

	// gestion des styles
	$pdf->SetStyle("b","arial","B",8,"0,0,0");
	$pdf->SetStyle("i","arial","I",8,"0,0,0");
	$pdf->SetStyle("u","arial","U",8,"0,0,0");

		// style pour la case appréciation général
		// identité du professeur principal
			if ( $taille_profprincipal_bloc_avis_conseil[$classe_id] != '' and $taille_profprincipal_bloc_avis_conseil[$classe_id] < '15' ) { $taille = $taille_profprincipal_bloc_avis_conseil[$classe_id]; } else { $taille = '10'; }
			$pdf->SetStyle("bppc","arial","B",$taille,"0,0,0");
			$pdf->SetStyle("ippc","arial","I",$taille,"0,0,0");

	// bloc affichage de l'adresse des parents
	if($active_bloc_adresse_parent[$classe_id]==='1') {
	$pdf->SetXY($X_parent[$classe_id],$Y_parent[$classe_id]);
		// définition des Lageur - hauteur	
		if ( $largeur_bloc_adresse[$classe_id] != '' and $largeur_bloc_adresse[$classe_id] != '0' ) { $longeur_cadre_adresse = $largeur_bloc_adresse[$classe_id]; } else { $longeur_cadre_adresse = '90'; }
		if ( $hauteur_bloc_adresse[$classe_id] != '' and $hauteur_bloc_adresse[$classe_id] != '0' ) { $hauteur_cadre_adresse = $hauteur_bloc_adresse[$classe_id]; } else { $hauteur_cadre_adresse = '1'; }

	$texte_1_responsable = $civilite_parents[$ident_eleve_aff][$responsable_place]." ".$nom_parents[$ident_eleve_aff][$responsable_place]." ".$prenom_parents[$ident_eleve_aff][$responsable_place];
	$texte_1_responsable = trim($texte_1_responsable);
		$hauteur_caractere=12;
		$pdf->SetFont($caractere_utilse[$classe_id],'B',$hauteur_caractere);
		$val = $pdf->GetStringWidth($texte_1_responsable);
		$taille_texte = $longeur_cadre_adresse;
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
		if($taille_texte<$val)
		{
			$hauteur_caractere = $hauteur_caractere-0.3;
			$pdf->SetFont($caractere_utilse[$classe_id],'B',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
		} else { $grandeur_texte='ok'; }
			}
	$pdf->Cell(90,7, $texte_1_responsable,0,2,'');
	$texte_1_responsable = $adresse1_parents[$ident_eleve_aff][$responsable_place];
		$hauteur_caractere=10;
		$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
		$val = $pdf->GetStringWidth($texte_1_responsable);
		$taille_texte = $longeur_cadre_adresse;
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
		if($taille_texte<$val)
		{
			$hauteur_caractere = $hauteur_caractere-0.3;
			$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
		} else { $grandeur_texte='ok'; }
			}
	$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
	$texte_1_responsable = $adresse2_parents[$ident_eleve_aff][$responsable_place];
		$hauteur_caractere=10;
		$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
		$val = $pdf->GetStringWidth($texte_1_responsable);
		$taille_texte = $longeur_cadre_adresse;
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
		if($taille_texte<$val)
		{
			$hauteur_caractere = $hauteur_caractere-0.3;
			$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
		} else { $grandeur_texte='ok'; }
			}
	$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
	$pdf->Cell(90,5, '',0,2,'');
	$texte_1_responsable = $cp_parents[$ident_eleve_aff][$responsable_place]." ".$ville_parents[$ident_eleve_aff][$responsable_place];
		$hauteur_caractere=10;
		$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
		$val = $pdf->GetStringWidth($texte_1_responsable);
		$taille_texte = $longeur_cadre_adresse;
		$grandeur_texte='test';
		while($grandeur_texte!='ok') {
		if($taille_texte<$val)
		{
			$hauteur_caractere = $hauteur_caractere-0.3;
			$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
		} else { $grandeur_texte='ok'; }
			}
	$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
	$texte_1_responsable = '';
	if ( $cadre_adresse[$classe_id] != 0 ) { $pdf->Rect($X_parent[$classe_id], $Y_parent[$classe_id], $longeur_cadre_adresse, $hauteur_cadre_adresse, 'D'); }
	}


	// bloc affichage information sur l'élèves
	if($active_bloc_eleve[$classe_id]==='1') {
	$pdf->SetXY($X_eleve[$classe_id],$Y_eleve[$classe_id]);
		// définition des Lageur - hauteur	
		if ( $largeur_bloc_eleve[$classe_id] != '' and $largeur_bloc_eleve[$classe_id] != '0' ) { $longeur_cadre_eleve = $largeur_bloc_eleve[$classe_id]; } else { $longeur_cadre_eleve = $pdf->GetStringWidth($nom_eleve[$i]." ".$prenom_eleve[$i]); $rajout_cadre_eleve = 100-$longeur_cadre_eleve; $longeur_cadre_eleve = $longeur_cadre_eleve + $rajout_cadre_eleve; }
		if ( $hauteur_bloc_eleve[$classe_id] != '' and $hauteur_bloc_eleve[$classe_id] != '0' ) { $hauteur_cadre_eleve = $hauteur_bloc_eleve[$classe_id]; } else { $nb_ligne = 5; $hauteur_ligne = 6; $hauteur_cadre_eleve = $nb_ligne*$hauteur_ligne; }

	$pdf->SetFont($caractere_utilse[$classe_id],'B',14);

	if($cadre_eleve[$classe_id]!=0) { $pdf->Rect($X_eleve[$classe_id], $Y_eleve[$classe_id], $longeur_cadre_eleve, $hauteur_cadre_eleve, 'D'); }
	$X_eleve_2=$X_eleve[$classe_id]; $Y_eleve_2=$Y_eleve[$classe_id];

		//photo de l'élève
		if ( !isset($ajout_cadre_blanc_photo[$classe_id]) or empty($ajout_cadre_blanc_photo[$classe_id]) ) { $ajout_cadre_blanc_photo[$classe_id] = '0'; }
		if ( $ajout_cadre_blanc_photo[$classe_id] === '1' ) { $ajouter = '1'; } else { $ajouter = '0'; }
		if($active_photo[$classe_id]==='1' and $photo[$i]!='' and file_exists($photo[$i])) {
		$L_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
		$H_photo_max = ($hauteur_cadre_eleve - ( $ajouter * 2 )) * 2.8;
		$valeur=redimensionne_image($photo[$i], $L_photo_max, $H_photo_max);
		$X_photo = $X_eleve[$classe_id]+ 0.20 + $ajouter;
		$Y_photo = $Y_eleve[$classe_id]+ 0.25 + $ajouter;
		$L_photo = $valeur[0]; $H_photo = $valeur[1];
		$X_eleve_2 = $X_eleve[$classe_id] + $L_photo + $ajouter + 1;
		$Y_eleve_2 = $Y_photo;
			$pdf->Image($photo[$i], $X_photo, $Y_photo, $L_photo, $H_photo);
			$longeur_cadre_eleve = $longeur_cadre_eleve - ( $valeur[0] + $ajouter );
		}

	$pdf->SetXY($X_eleve_2,$Y_eleve_2);
	$pdf->Cell(90,7, $nom_eleve[$i]." ".$prenom_eleve[$i],0,2,'');
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	if($affiche_date_naissance[$classe_id]==='1') {
		if($date_naissance[$i]!="") { $pdf->Cell(90,5, $date_naissance[$i],0,2,''); }
	}

	if($affiche_dp[$classe_id]==='1') {
	if($dp[$i]!="") { $pdf->Cell(90,4, $dp[$i],0,2,''); }
	}
	if($affiche_doublement[$classe_id]==='1') {
	if($doublement[$i]!="") { $pdf->Cell(90,4.5, $doublement[$i],0,2,''); }
	}
	if($affiche_nom_court[$classe_id]==='1') {
	if($classe_nomcour[$i]!="") { $pdf->Cell(90,4.5, unhtmlentities($classe_nomcour[$i]),0,2,''); }
	}

	//if($affiche_dp[$classe_id]==='1') {
	//	if($dp[$i]!="") {
			/*
			if ($affiche_doublement[$classe_id]=='1') {
				if ($doublement[$i]!="") {
					$pass_ligne=0;
					$ajout_virg=", ";
				}
				else{
					$pass_ligne=0;
					$ajout_virg="";
				}
			}
			else{
				$pass_ligne=2;
				$ajout_virg="";
			}
			*/

	//		$pdf->Cell(90,4, $dp[$i],0,2,'');
	//		//$pdf->Cell(50,4, $dp[$i].$ajout_virg,0,$pass_ligne,'');
	//	}
	//}
	//if($affiche_doublement[$classe_id]==='1') {
	//	$pdf->Cell(90,4.5, $doublement[$i],0,2,'');
	//}
	//if($affiche_nom_court[$classe_id]==='1') {
	//	if($classe_nomcour[$i]!="") { $pdf->Cell(90,4.5, unhtmlentities($classe_nomcour[$i]),0,2,''); }
	//}

/*
	// INSERTION DE L'INE DE L'ELEVE
	if($bull_pdf_INE_eleve=="y"){
		$pdf->Cell(45,4.5, 'N°INE : '.$INE_eleve[$i],0,2,'');
	}
*/


	if($affiche_effectif_classe[$classe_id]==='1') {
		if($affiche_numero_impression[$classe_id]==='1') {
			$pass_ligne = '0';
		} else { $pass_ligne = '2'; }
		if($info_bulletin[$ident_eleve_aff][$id_periode]['effectif']!="") {
			$pdf->Cell(45,4.5, 'Effectif :
			'.$info_bulletin[$ident_eleve_aff][$id_periode]['effectif'].'
			élèves',0,$pass_ligne,''); }
	}



	//if($affiche_effectif_classe==='1') {
	// if($info_bulletin[$ident_eleve_aff][$id_periode]['effectif']!="") { $pdf->Cell(45,4.5, 'Effectif : '.$info_bulletin[$ident_eleve_aff][$id_periode]['effectif'].' élèves',0,0,''); }
	//}

	if($affiche_numero_impression[$classe_id]==='1') {
	$num_ordre = $i;
	$pdf->Cell(45,4, 'Bulletin N° '.$num_ordre,0,2,'');
	}
	if($affiche_etab_origine[$classe_id]==='1' and !empty($etablissement_origine[$i]) ) {
	$pdf->SetX($X_eleve_2);
	$hauteur_caractere_etaborigine = '10';
	$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_etaborigine);
 	  $val = $pdf->GetStringWidth('Etab. Origine : '.$etablissement_origine[$i]);
	  $taille_texte = $longeur_cadre_eleve-3;
	  $grandeur_texte='test';
	  while($grandeur_texte!='ok') {
		if($taille_texte<$val)
		{
			$hauteur_caractere_etaborigine = $hauteur_caractere_etaborigine-0.3;
			$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_etaborigine);
			$val = $pdf->GetStringWidth('Etab. Origine : '.$etablissement_origine[$i]);
		} else { $grandeur_texte='ok'; }
	  }
	  $grandeur_texte='test';
	$pdf->Cell(90,4, 'Etab. Origine : '.$etablissement_origine[$i],0,2);
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	}
	}

	// bloc affichage datation du bulletin
	if($active_bloc_datation[$classe_id]==='1') {
	$pdf->SetXY($X_datation_bul[$classe_id], $Y_datation_bul[$classe_id]);
		// définition des Lageur - hauteur	
		if ( $largeur_bloc_datation[$classe_id] != '' and $largeur_bloc_datation[$classe_id] != '0' ) { $longeur_cadre_datation_bul = $largeur_bloc_datation[$classe_id]; } else { $longeur_cadre_datation_bul = '95'; }
		if ( $hauteur_bloc_datation[$classe_id] != '' and $hauteur_bloc_datation[$classe_id] != '0' ) { $hauteur_cadre_datation_bul = $hauteur_bloc_datation[$classe_id]; } else { $nb_ligne_datation_bul = 3; $hauteur_ligne_datation_bul = 6; $hauteur_cadre_datation_bul = $nb_ligne_datation_bul*$hauteur_ligne_datation_bul; }

if($cadre_datation_bul[$classe_id]!=0) { $pdf->Rect($X_datation_bul[$classe_id], $Y_datation_bul[$classe_id], $longeur_cadre_datation_bul, $hauteur_cadre_datation_bul, 'D'); }
	$taille_texte = '14'; $type_texte = 'B';
	if ( $taille_texte_classe[$classe_id] != '' and $taille_texte_classe[$classe_id] != '0' ) { $taille_texte = $taille_texte_classe[$classe_id]; } else { $taille_texte = '14'; }
	if ( $type_texte_classe[$classe_id] != '' ) { if ( $type_texte_classe[$classe_id] === 'N' ) { $type_texte = ''; } else { $type_texte = $type_texte_classe[$classe_id]; } } else { $type_texte = 'B'; }
	$pdf->SetFont($caractere_utilse[$classe_id], $type_texte, $taille_texte);
	$pdf->Cell(90,7, "Classe de ".unhtmlentities($classe_nomlong[$i]),0,2,'C');
	$taille_texte = '12'; $type_texte = '';
	if ( $taille_texte_annee[$classe_id] != '' and $taille_texte_annee[$classe_id] != '0') { $taille_texte = $taille_texte_annee[$classe_id]; } else { $taille_texte = '12'; }
	if ( $type_texte_annee[$classe_id] != '' ) { if ( $type_texte_annee[$classe_id] === 'N' ) { $type_texte = ''; } else { $type_texte = $type_texte_annee[$classe_id]; } } else { $type_texte = ''; }
	$pdf->SetFont($caractere_utilse[$classe_id], $type_texte, $taille_texte);
	$pdf->Cell(90,5, "Année scolaire ".$annee_scolaire,0,2,'C');
	$taille_texte = '10'; $type_texte = '';
	if ( $taille_texte_periode[$classe_id] != '' and $taille_texte_periode[$classe_id] != '0' ) { $taille_texte = $taille_texte_periode[$classe_id]; } else { $taille_texte = '10'; }
	if ( $type_texte_periode[$classe_id] != '' ) { if ( $type_texte_periode[$classe_id] === 'N' ) { $type_texte = ''; } else { $type_texte = $type_texte_periode[$classe_id]; } } else { $type_texte = ''; }
	$pdf->SetFont($caractere_utilse[$classe_id], $type_texte, $taille_texte);
		//connaître le nom de la période
		if(empty($nom_periode[$id_classe_selection][$id_periode])) {
		$requete_periode = mysql_query('SELECT * FROM '.$prefix_base.'periodes WHERE id_classe="'.$id_classe_selection.'" AND num_periode="'.$id_periode.'"');
		$nom_periode[$id_classe_selection][$id_periode] = @mysql_result($requete_periode, '0', 'nom_periode');
		}
	$pdf->Cell(90,5, "Bulletin du ".unhtmlentities($nom_periode[$id_classe_selection][$id_periode]),0,2,'C');
	$taille_texte = '8'; $type_texte = '';
	if ( $taille_texte_date_edition[$classe_id] != '' and $taille_texte_date_edition[$classe_id] != '0' ) { $taille_texte = $taille_texte_date_edition[$classe_id]; } else { $taille_texte = '8'; }
	if ( $type_texte_date_datation[$classe_id] != '' ) { if ( $type_texte_date_datation[$classe_id] === 'N' ) { $type_texte = ''; } else { $type_texte = $type_texte_date_datation[$classe_id]; } } else { $type_texte = ''; }
	$pdf->SetFont($caractere_utilse[$classe_id], $type_texte, $taille_texte);
	$pdf->Cell(95,7, $date_bulletin,0,2,'R');
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	}

	// bloc note et appréciation
	//nombre de matiere à afficher
	$nb_matiere = $info_bulletin[$ident_eleve_aff][$id_periode]['nb_matiere'];
	if($active_bloc_note_appreciation[$classe_id]==='1' and $nb_matiere!='0') {
	$pdf->Rect($X_note_app[$classe_id], $Y_note_app[$classe_id], $longeur_note_app[$classe_id], $hauteur_note_app[$classe_id], 'D');
		//entête du tableau des note et app
		$nb_entete_moyenne = $active_moyenne_eleve[$classe_id]+$active_moyenne_classe[$classe_id]+$active_moyenne_min[$classe_id]+$active_moyenne_max[$classe_id]; //min max classe eleve
		$hauteur_entete = 8;
		$hauteur_entete_pardeux = $hauteur_entete/2;
		$pdf->SetXY($X_note_app[$classe_id], $Y_note_app[$classe_id]);
		$pdf->SetFont($caractere_utilse[$classe_id],'',10);
		// $largeur_matiere[$classe_id] = 40;
		$pdf->Cell($largeur_matiere[$classe_id], $hauteur_entete, $titre_entete_matiere[$classe_id],1,0,'C');
		$largeur_utilise = $largeur_matiere[$classe_id];

		// coéfficient matière
		if($active_coef_moyenne[$classe_id]==='1') {
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
		$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->Cell($largeur_coef_moyenne[$classe_id], $hauteur_entete, $titre_entete_coef[$classe_id],'LRB',0,'C');
		$largeur_utilise = $largeur_utilise + $largeur_coef_moyenne[$classe_id];
		}

		// nombre de note
		if($active_nombre_note_case[$classe_id]==='1') {
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
		$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->Cell($largeur_nombre_note[$classe_id], $hauteur_entete, $titre_entete_nbnote[$classe_id],'LRB',0,'C');
		$largeur_utilise = $largeur_utilise + $largeur_nombre_note[$classe_id];
		}

// eleve | min | classe | max | rang | niveau | appreciation |
if ( $ordre_entete_model_bulletin[$classe_id] === '1' ) {
	$ordre_moyenne[0] = 'eleve';
	$ordre_moyenne[1] = 'min';
	$ordre_moyenne[2] = 'classe';
	$ordre_moyenne[3] = 'max';
	$ordre_moyenne[4] = 'rang';
	$ordre_moyenne[5] = 'niveau';
	$ordre_moyenne[6] = 'appreciation';
}

// min | classe | max | eleve | niveau | rang | appreciation |
if ( $ordre_entete_model_bulletin[$classe_id] === '2' ) {
	$ordre_moyenne[0] = 'min';
	$ordre_moyenne[1] = 'classe';
	$ordre_moyenne[2] = 'max';
	$ordre_moyenne[3] = 'eleve';
	$ordre_moyenne[4] = 'niveau';
	$ordre_moyenne[5] = 'rang';
	$ordre_moyenne[6] = 'appreciation';
}


// eleve | niveau | rang | appreciation | min | classe | max
if ( $ordre_entete_model_bulletin[$classe_id] === '3' ) {
	$ordre_moyenne[0] = 'eleve';
	$ordre_moyenne[1] = 'niveau';
	$ordre_moyenne[2] = 'rang';
	$ordre_moyenne[3] = 'appreciation';
	$ordre_moyenne[4] = 'min';
	$ordre_moyenne[5] = 'classe';
	$ordre_moyenne[6] = 'max';
}

// eleve | classe | min | max | rang | niveau | appreciation |
if ( $ordre_entete_model_bulletin[$classe_id] === '4' ) {
	$ordre_moyenne[0] = 'eleve';
	$ordre_moyenne[1] = 'classe';
	$ordre_moyenne[2] = 'min';
	$ordre_moyenne[3] = 'max';
	$ordre_moyenne[4] = 'rang';
	$ordre_moyenne[5] = 'niveau';
	$ordre_moyenne[6] = 'appreciation';
}

// eleve | min | classe | max | niveau | rang | appreciation |
if ( $ordre_entete_model_bulletin[$classe_id] === '5' ) {
	$ordre_moyenne[0] = 'eleve';
	$ordre_moyenne[1] = 'min';
	$ordre_moyenne[2] = 'classe';
	$ordre_moyenne[3] = 'max';
	$ordre_moyenne[4] = 'niveau';
	$ordre_moyenne[5] = 'rang';
	$ordre_moyenne[6] = 'appreciation';
}

// min | classe | max | eleve | rang | niveau | appreciation |
if ( $ordre_entete_model_bulletin[$classe_id] === '6' ) {
	$ordre_moyenne[0] = 'min';
	$ordre_moyenne[1] = 'classe';
	$ordre_moyenne[2] = 'max';
	$ordre_moyenne[3] = 'eleve';
	$ordre_moyenne[4] = 'rang';
	$ordre_moyenne[5] = 'niveau';
	$ordre_moyenne[6] = 'appreciation';
}


// les moyennes eleve, classe, min, max
/*		if( $active_moyenne[$classe_id]==='1') {

		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
		$largeur_moyenne = $largeur_d_une_moyenne[$classe_id] * $nb_entete_moyenne;
		$text_entete_moyenne = 'Moyenne';
			if ( $type_bulletin === '2' and $active_moyenne_eleve[$classe_id] === '1' and $nb_entete_moyenne > 1 )
			{
				$largeur_moyenne = $largeur_d_une_moyenne[$classe_id] * ( $nb_entete_moyenne - 1 );
				$text_entete_moyenne = 'Pour la classe';
				if ( $ordre_moyenne[0] === 'eleve' ) {
					$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise+$largeur_d_une_moyenne[$classe_id], $Y_note_app[$classe_id]);
				}
				if ( $ordre_moyenne[0] != 'eleve' ) {
					$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
				}
			}
		$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, $text_entete_moyenne,1,0,'C');
		$pdf->SetFont($caractere_utilse[$classe_id],'',9);
			$largeur_d_une_moyenne[$classe_id] = $largeur_moyenne / $nb_entete_moyenne;
				if ( $type_bulletin === '2' and $active_moyenne_eleve[$classe_id] === '1' and $nb_entete_moyenne > 1 )
				{
					$largeur_d_une_moyenne[$classe_id] = $largeur_moyenne / ( $nb_entete_moyenne - 1 );
				}
		}*/

$cpt_ordre = 0;
$chapeau_moyenne = 'non';
while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
		$categorie_passe_count = 0;
		// le chapeau des moyennes
			$ajout_espace_au_dessus = 4;
			if ( $entete_model_bulletin[$classe_id] === '1' and $nb_entete_moyenne > 1 and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' or $ordre_moyenne[$cpt_ordre] === 'eleve' ) and $chapeau_moyenne === 'non' and $ordre_entete_model_bulletin[$classe_id] != '3' )
			{
				$largeur_moyenne = $largeur_d_une_moyenne[$classe_id] * $nb_entete_moyenne;
				$text_entete_moyenne = 'Moyenne';
				$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
				$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, $text_entete_moyenne,1,0,'C');
				$chapeau_moyenne = 'oui';
			}

			if ( ($entete_model_bulletin[$classe_id] === '2' and $nb_entete_moyenne > 1 and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' ) and $chapeau_moyenne === 'non' ) or ( $entete_model_bulletin[$classe_id] === '1' and $ordre_entete_model_bulletin[$classe_id] === '3' and $chapeau_moyenne === 'non' and ( $ordre_moyenne[$cpt_ordre] === 'classe' or $ordre_moyenne[$cpt_ordre] === 'min' or $ordre_moyenne[$cpt_ordre] === 'max' )  ) )
			{
				$largeur_moyenne = $largeur_d_une_moyenne[$classe_id] * ( $nb_entete_moyenne - 1 );
				$text_entete_moyenne = 'Pour la classe';
				$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
				$hauteur_caractere=10;
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
					$val = $pdf->GetStringWidth($text_entete_moyenne);
					$taille_texte = $largeur_moyenne;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
						$hauteur_caractere = $hauteur_caractere-0.3;
						$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
						$val = $pdf->GetStringWidth($text_entete_moyenne);
					} else { $grandeur_texte='ok'; }
						}
				$pdf->Cell($largeur_moyenne, $hauteur_entete_pardeux, $text_entete_moyenne,1,0,'C');
				$chapeau_moyenne = 'oui';
			}

			//eleve
			if($active_moyenne_eleve[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
			$ajout_espace_au_dessus = 4;
			$hauteur_de_la_cellule = $hauteur_entete_pardeux;
				if ( $entete_model_bulletin[$classe_id] === '2' and $active_moyenne_eleve[$classe_id] === '1' and $nb_entete_moyenne > 1 )
				{
				$hauteur_de_la_cellule = $hauteur_entete;
				$ajout_espace_au_dessus = 0;
				}
			$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]+$ajout_espace_au_dessus);
			$pdf->SetFillColor($couleur_reperage_eleve1[$classe_id], $couleur_reperage_eleve2[$classe_id], $couleur_reperage_eleve3[$classe_id]);
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_de_la_cellule, "Elève",1,0,'C',$active_reperage_eleve[$classe_id]);
			$pdf->SetFillColor(0, 0, 0);
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}

			//classe
			if($active_moyenne_classe[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
			$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]+4);
				$hauteur_caractere = '8.5';
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$text_moy_classe = 'Classe';
			if ( $entete_model_bulletin[$classe_id] === '2' ) { $text_moy_classe = 'Moy.'; }
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_pardeux, $text_moy_classe,1,0,'C');
			$X_moyenne_classe = $X_note_app[$classe_id]+$largeur_utilise;
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}
			//min
			if($active_moyenne_min[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
			$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]+4);
				$hauteur_caractere = '8.5';
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_pardeux, "Min.",1,0,'C');
			$X_min_classe = $X_note_app[$classe_id]+$largeur_utilise;
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}
			//max
			if($active_moyenne_max[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
			$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]+4);
				$hauteur_caractere = '8.5';
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_pardeux, "Max.",1,0,'C');
			$X_max_classe = $X_note_app[$classe_id]+$largeur_utilise;
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}


		$pdf->SetFont($caractere_utilse[$classe_id],'',10);

		// rang de l'élève
		if( $active_rang[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
		$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->Cell($largeur_rang[$classe_id], $hauteur_entete, $titre_entete_rang[$classe_id],'LRB',0,'C');
		$largeur_utilise = $largeur_utilise + $largeur_rang[$classe_id];
		}

		// graphique de niveau
		if( $active_graphique_niveau[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
				$hauteur_caractere = '10';
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
		$pdf->Cell($largeur_niveau[$classe_id], $hauteur_entete_pardeux, "Niveau",'LR',0,'C');
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]+4);
		$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->Cell($largeur_niveau[$classe_id], $hauteur_entete_pardeux, "ABC+C-DE",'LRB',0,'C');
		$largeur_utilise = $largeur_utilise+$largeur_niveau[$classe_id];
		}

		//appreciation
				$hauteur_caractere = '10';
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere);
		if($active_appreciation[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
		$pdf->SetXY($X_note_app[$classe_id]+$largeur_utilise, $Y_note_app[$classe_id]);
			if ( !empty($ordre_moyenne[$cpt_ordre+1]) ) {
				$cpt_ordre_sous = $cpt_ordre + 1;
				$largeur_appret = 0;
				while ( !empty($ordre_moyenne[$cpt_ordre_sous]) ) {
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'eleve' ) { $largeur_appret = $largeur_appret + $largeur_d_une_moyenne[$classe_id]; }
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'rang' ) { $largeur_appret = $largeur_appret + $largeur_rang[$classe_id]; }
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'niveau' ) { $largeur_appret = $largeur_appret + $largeur_niveau[$classe_id]; }
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'min' ) { $largeur_appret = $largeur_appret + $largeur_d_une_moyenne[$classe_id]; }
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'classe' ) { $largeur_appret = $largeur_appret + $largeur_d_une_moyenne[$classe_id]; }
					if ( $ordre_moyenne[$cpt_ordre_sous] === 'max' ) { $largeur_appret = $largeur_appret + $largeur_d_une_moyenne[$classe_id]; }
				$cpt_ordre_sous = $cpt_ordre_sous + 1;
				}
				$largeur_appreciation = $longeur_note_app[$classe_id] - $largeur_utilise - $largeur_appret;
		} else { $largeur_appreciation = $longeur_note_app[$classe_id]-$largeur_utilise; }
		$pdf->SetFont($caractere_utilse[$classe_id],'',10);
		$pdf->Cell($largeur_appreciation, $hauteur_entete, $titre_entete_appreciation,'LRB',0,'C');
			$largeur_utilise = $largeur_utilise + $largeur_appreciation;
		}
$cpt_ordre = $cpt_ordre + 1;
}
		$largeur_utilise = 0;
// fin de boucle d'ordre

		//emplacement des blocs matière et note et appréciation

			//si catégorie activé il faut conmpte le nombre de catégorie
			$nb_categories_select=0;
			$categorie_passe_for='';

		//============================
		// Modif: boireaus 20070828
		if($active_regroupement_cote[$classe_id]==='1' or $active_entete_regroupement[$classe_id]==='1') {
			for($x=0;$x<$nb_matiere;$x++) {
				if($matiere[$ident_eleve_aff][$id_periode][$x]['categorie']!=$categorie_passe_for) {
					$nb_categories_select=$nb_categories_select+1;
				}
				$categorie_passe_for=$matiere[$ident_eleve_aff][$id_periode][$x]['categorie'];
			}
		}
		//============================

		$X_bloc_matiere=$X_note_app[$classe_id]; $Y_bloc_matiere=$Y_note_app[$classe_id]+$hauteur_entete; $longeur_bloc_matiere=$longeur_note_app[$classe_id];
		// calcule de la hauteur total que peut prendre le cadre matière dans sa globalité
		if ( $active_moyenne[$classe_id] === '1' and $active_moyenne_general[$classe_id] === '1' ) 
		{ 
			// si les moyennes et la moyenne général sont activé alors on les ajoute à ceux qui vaudras soustraire au cadre global matiere
			$hauteur_toute_entete = $hauteur_entete + $hauteur_entete_moyenne_general[$classe_id]; 
		} else { $hauteur_toute_entete = $hauteur_entete; }

		$hauteur_bloc_matiere=$hauteur_note_app[$classe_id]-$hauteur_toute_entete;
		$X_note_moy_app = $X_note_app[$classe_id]; $Y_note_moy_app = $Y_note_app[$classe_id]+$hauteur_note_app[$classe_id]-$hauteur_entete;

			if($active_entete_regroupement[$classe_id]==='1') {
		$espace_entre_matier = ($hauteur_bloc_matiere-($nb_categories_select*5))/$nb_matiere;
		} else { $espace_entre_matier = $hauteur_bloc_matiere/$nb_matiere; }
		$pdf->SetXY($X_bloc_matiere, $Y_bloc_matiere);
		$Y_decal = $Y_bloc_matiere;

		for($m=0; $m<$nb_matiere; $m++)
		{
			$pdf->SetXY($X_bloc_matiere, $Y_decal);

			// si on affiche les catégorie
			if($active_entete_regroupement[$classe_id]==='1') {
				//si on affiche les moyenne des catégorie
				if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$categorie_passe)
				{
					$hauteur_caractere_catego = '10';
					if ( $taille_texte_categorie[$classe_id] != '' and $taille_texte_categorie[$classe_id] != '0' ) { $hauteur_caractere_catego = $taille_texte_categorie[$classe_id]; } else { $hauteur_caractere_catego = '10'; }
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_catego);
					$tt_catego = unhtmlentities($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']);
					$val = $pdf->GetStringWidth($tt_catego);
					$taille_texte = ($largeur_matiere[$classe_id]);
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
							$hauteur_caractere_catego = $hauteur_caractere_catego-0.3;
							$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_catego);
							$val = $pdf->GetStringWidth($tt_catego);
					} else { $grandeur_texte='ok'; }
							}
					$grandeur_texte='test';
					$pdf->SetFillColor($couleur_categorie_entete1[$classe_id], $couleur_categorie_entete2[$classe_id], $couleur_categorie_entete3[$classe_id]);
					$pdf->Cell($largeur_matiere[$classe_id], $hauteur_info_categorie[$classe_id], $tt_catego,'TLB',0,'L',$couleur_categorie_entete[$classe_id]);
					$largeur_utilise = $largeur_matiere[$classe_id];

					// coéfficient matière
					if($active_coef_moyenne[$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
					$pdf->SetFont($caractere_utilse[$classe_id],'',10);
					$pdf->Cell($largeur_coef_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C',$couleur_categorie_entete[$classe_id]);
					$largeur_utilise = $largeur_utilise+$largeur_coef_moyenne[$classe_id];
					}

					// nombre de note
					if($active_nombre_note_case[$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
					$pdf->SetFont($caractere_utilse[$classe_id],'',10);
					$pdf->Cell($largeur_nombre_note[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C',$couleur_categorie_entete[$classe_id]);
					$largeur_utilise = $largeur_utilise+$largeur_nombre_note[$classe_id];
					}
					$pdf->SetFillColor(0, 0, 0);

					// les moyennes eleve, classe, min, max par catégorie
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);

					$cpt_ordre = 0;
					$chapeau_moyenne = 'non';
					while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
							//eleve
					if($active_moyenne_eleve[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
							$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							if($active_moyenne_regroupement[$classe_id]==='1') {
								$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
								if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
								{
									if ($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_eleve'] == "") {
										$valeur = "-";
									} else {
										$calcule_moyenne_eleve_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_eleve']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
										$valeur = present_nombre($calcule_moyenne_eleve_categorie[$categorie_passage], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
									}
									$pdf->SetFont($caractere_utilse[$classe_id],'B',8);
									$pdf->SetFillColor($couleur_reperage_eleve1[$classe_id], $couleur_reperage_eleve2[$classe_id], $couleur_reperage_eleve3[$classe_id]);
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id],$valeur,1,0,'C',$active_reperage_eleve[$classe_id]);
									$pdf->SetFillColor(0, 0, 0);
									$valeur = "";
								} else {
									$pdf->SetFillColor(255, 255, 255);
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','TL',0,'C',$active_reperage_eleve[$classe_id]);
								}
							} else {
								$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
							}

							$largeur_utilise = $largeur_utilise+$largeur_d_une_moyenne[$classe_id];
						}
							//classe
						if($active_moyenne_classe[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
	//					     $pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
							$pdf->SetXY($X_moyenne_classe, $Y_decal);
							if($active_moyenne_regroupement[$classe_id]==='1') {
								$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
								if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
								{
									//================================================
									// MODIF: boireaus
									//$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_classe']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
									if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']!=0){
										$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_classe']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
									}
									else{
										$calcule_moyenne_classe_categorie[$categorie_passage]="";
									}
									$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
									//================================================
									$pdf->SetFont($caractere_utilse[$classe_id],'',8);
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),'TLR',0,'C');
								} else {
										$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
									}
							} else {
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
										}
						$largeur_utilise = $largeur_utilise+$largeur_d_une_moyenne[$classe_id];
						}

						$pdf->SetFont($caractere_utilse[$classe_id],'',10);
							//min
						if($active_moyenne_min[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
						$pdf->SetXY($X_min_classe, $Y_decal);
							if($active_moyenne_regroupement[$classe_id]==='1') {
								$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
								if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
								{
									//================================================
									// MODIF: boireaus
									//$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_min']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];

									if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']!=0){
										$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_min']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
									}
									else{
										$calcule_moyenne_classe_categorie[$categorie_passage]="";
									}
									//================================================

									$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
									$pdf->SetFont($caractere_utilse[$classe_id],'',8);
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),'TLR',0,'C');
								} else {
										$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
										}
							} else {
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
										}
						$largeur_utilise = $largeur_utilise+$largeur_d_une_moyenne[$classe_id];
						}

							//max
						if($active_moyenne_max[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
						$pdf->SetXY($X_max_classe, $Y_decal);
							if($active_moyenne_regroupement[$classe_id]==='1') {
								$categorie_passage=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
								if($matiere[$ident_eleve_aff][$id_periode][$m]['affiche_moyenne']==='1')
								{
									//================================================
									// MODIF: boireaus
									//$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_max']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];

									if($matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego']){
										$calcule_moyenne_classe_categorie[$categorie_passage]=$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['moy_max']/$matiere[$ident_eleve_aff][$id_periode][$categorie_passage]['coef_tt_catego'];
									}
									else{
										$calcule_moyenne_classe_categorie[$categorie_passage]="";
									}
									//================================================

									$calcule_moyenne_classe_categorie[$categorie_passage]=$calcule_moyenne_classe_categorie[$categorie_passage];
									$pdf->SetFont($caractere_utilse[$classe_id],'',8);
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], present_nombre($calcule_moyenne_classe_categorie[$categorie_passage], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),'TLR',0,'C');
								} else {
										$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
										}
							} else {
									$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
										}
						$largeur_utilise = $largeur_utilise+$largeur_d_une_moyenne[$classe_id];
						}
					$cpt_ordre = $cpt_ordre + 1;
					}
					$largeur_utilise = 0;
					// fin de boucle d'ordre

					// rang de l'élève
					if($active_rang[$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
					$pdf->SetFont($caractere_utilse[$classe_id],'',10);
					$pdf->Cell($largeur_rang[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
					$largeur_utilise = $largeur_utilise+$largeur_rang[$classe_id];
					}
					// graphique de niveau
					if($active_graphique_niveau[$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
					$pdf->Cell($largeur_niveau[$classe_id], $hauteur_info_categorie[$classe_id], '','T',0,'C');
					$largeur_utilise = $largeur_utilise+$largeur_niveau[$classe_id];
					}
					//appreciation
					if($active_appreciation[$classe_id]==='1') {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal);
					$pdf->Cell($largeur_appreciation, $hauteur_info_categorie[$classe_id], '','TB',0,'C');
					$largeur_utilise=0;
					}
					$Y_decal = $Y_decal + 5;

				}
			}


			//============================
			// Modif: boireaus 20070828
			if($active_regroupement_cote[$classe_id]==='1' or $active_entete_regroupement[$classe_id]==='1') {
				if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']===$categorie_passe) {
					$categorie_passe_count=$categorie_passe_count+1;
				}
				else {
					$categorie_passe_count=0;
				}
				if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$categorie_passe) { $categorie_passe_count=$categorie_passe_count+1; }
				// fin des moyen par catégorie
			}
			//============================



				// si on affiche les catégorie sur le coté

			if(!isset($matiere[$ident_eleve_aff][$id_periode][$m+1]['categorie'])) { $matiere[$ident_eleve_aff][$id_periode][$m+1]['categorie']=''; }

			if($active_regroupement_cote[$classe_id]==='1') {
				if($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']!=$matiere[$ident_eleve_aff][$id_periode][$m+1]['categorie'] and $categorie_passe!='')
				{
					//hauteur du regroupement hauteur des matier * nombre de matier de la catégorie
					$hauteur_regroupement=$espace_entre_matier*$categorie_passe_count;

					//placement du cadre
						if($nb_eleve_aff===0) { $enplus = 5; }
						if($nb_eleve_aff!=0) { $enplus = 0; }

						$pdf->SetXY($X_bloc_matiere-5,$Y_decal-$hauteur_regroupement+$espace_entre_matier);

						$pdf->SetFillColor($couleur_categorie_cote1[$classe_id], $couleur_categorie_cote2[$classe_id], $couleur_categorie_cote3[$classe_id]);
					if($couleur_categorie_cote[$classe_id] === '1') { $mode_choix_c = '2'; } else { $mode_choix_c = '1'; }
					$pdf->drawTextBox("", 5, $hauteur_regroupement, 'C', 'T', $mode_choix_c);
					//texte à afficher
					$hauteur_caractere_vertical = '8';
					if ( $taille_texte_categorie_cote[$classe_id] != '' and $taille_texte_categorie_cote[$classe_id] != '0') { $hauteur_caractere_vertical = $taille_texte_categorie_cote[$classe_id]; } else { $hauteur_caractere_vertical = '8'; }
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_vertical);
					$text_s = unhtmlentities($matiere[$ident_eleve_aff][$id_periode][$m]['categorie']);
					$longeur_test_s = $pdf->GetStringWidth($text_s);

					// gestion de la taille du texte vertical
					$taille_texte = $hauteur_regroupement;
					$grandeur_texte = 'test';
					while($grandeur_texte != 'ok') {
					if($taille_texte < $longeur_test_s)
					{
						$hauteur_caractere_vertical = $hauteur_caractere_vertical-0.3;
						$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_vertical);
						$longeur_test_s = $pdf->GetStringWidth($text_s);
					} else { $grandeur_texte = 'ok'; }
							}


					//décalage pour centre le texte
					$deca = ($hauteur_regroupement-$longeur_test_s)/2;
						$deca = 0;
					$deca = ($hauteur_regroupement-$longeur_test_s)/2;

					//place le texte dans le cadre
					$placement = $Y_decal+$espace_entre_matier-$deca;
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_vertical);
					$pdf->TextWithDirection($X_bloc_matiere-1,$placement,unhtmlentities($text_s),'U');
					$pdf->SetFont($caractere_utilse[$classe_id],'',10);
						$pdf->SetFillColor(0, 0, 0);
				}
			}

			//============================
			// Modif: boireaus 20070828
			if($active_regroupement_cote[$classe_id]==='1' or $active_entete_regroupement[$classe_id]==='1') {
				// fin d'affichage catégorie sur le coté
				$categorie_passe=$matiere[$ident_eleve_aff][$id_periode][$m]['categorie'];
				// fin de gestion de catégorie
			}
			//============================


			$pdf->SetXY($X_bloc_matiere, $Y_decal);

			// calcule la taille du titre de la matière
				$hauteur_caractere_matiere=10;
				if ( $taille_texte_matiere[$classe_id] != '' and $taille_texte_matiere[$classe_id] != '0' and $taille_texte_matiere[$classe_id] < '11' )
				{
					$hauteur_caractere_matiere = $taille_texte_matiere[$classe_id];
				}
				$pdf->SetFont($caractere_utilse[$classe_id],'B',$hauteur_caractere_matiere);
				$val = $pdf->GetStringWidth($matiere[$ident_eleve_aff][$id_periode][$m]['matiere']);
				$taille_texte = $largeur_matiere[$classe_id] - 0.9;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere_matiere = $hauteur_caractere_matiere-0.3;
					$pdf->SetFont($caractere_utilse[$classe_id],'B',$hauteur_caractere_matiere);
					$val = $pdf->GetStringWidth($matiere[$ident_eleve_aff][$id_periode][$m]['matiere']);
				} else { $grandeur_texte='ok'; }
						}
				$grandeur_texte='test';
			$pdf->Cell($largeur_matiere[$classe_id], $espace_entre_matier/2, $matiere[$ident_eleve_aff][$id_periode][$m]['matiere'],'LR',1,'L');
			$Y_decal = $Y_decal+($espace_entre_matier/2);
			$pdf->SetXY($X_bloc_matiere, $Y_decal);
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
			// nom des professeurs
			$nb_prof_matiere = count($matiere[$ident_eleve_aff][$id_periode][$m]['prof']);
			$espace_matiere_prof = $espace_entre_matier/2;
			$espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere;
			$nb_pass_count = '0';
			$text_prof = '';
			while ($nb_prof_matiere > $nb_pass_count)
			{
				// calcule de la hauteur du caractère du prof
				$text_prof = $matiere[$ident_eleve_aff][$id_periode][$m]['prof'][$nb_pass_count];
				if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 8; }
				elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 5; }
				elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
				$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_prof);
				$val = $pdf->GetStringWidth($text_prof);
				$taille_texte = ($largeur_matiere[$classe_id]);
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_prof);
					$val = $pdf->GetStringWidth($text_prof);
				} else { $grandeur_texte='ok'; }
						}
				$grandeur_texte='test';
					$pdf->SetX($X_bloc_matiere);
				if( empty($matiere[$ident_eleve_aff][$id_periode][$m]['prof'][$nb_pass_count+1]) ) {
					$pdf->Cell($largeur_matiere[$classe_id], $espace_matiere_prof, $text_prof,'LRB',1,'L');
				}
				if( !empty($matiere[$ident_eleve_aff][$id_periode][$m]['prof'][$nb_pass_count+1]) ) {
					$pdf->Cell($largeur_matiere[$classe_id], $espace_matiere_prof, $text_prof,'LR',1,'L');
				}
			$nb_pass_count = $nb_pass_count + 1;
			}
			//$pdf->Cell($largeur_matiere[$classe_id], $espace_entre_matier/3, $matiere[$ident_eleve_aff][$id_periode][$m]['prof'],'LRB',0,'L');
			$largeur_utilise = $largeur_matiere[$classe_id];

			// coéfficient matière
			if($active_coef_moyenne[$classe_id]==='1') {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			$pdf->SetFont($caractere_utilse[$classe_id],'',10);
			$pdf->Cell($largeur_coef_moyenne[$classe_id], $espace_entre_matier, $matiere[$ident_eleve_aff][$id_periode][$m]['coef'],1,0,'C');
			$largeur_utilise = $largeur_utilise+$largeur_coef_moyenne[$classe_id];
			}
				//permet le calcule total des coefficients
				// if(empty($moyenne_min[$id_classe][$id_periode])) {
				$total_coef_en_calcul=$total_coef_en_calcul+$matiere[$ident_eleve_aff][$id_periode][$m]['coef'];
				//}

			// nombre de note
			if($active_nombre_note_case[$classe_id]==='1') {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			$pdf->SetFont($caractere_utilse[$classe_id],'',10);
			$valeur = $matiere[$ident_eleve_aff][$id_periode][$m]['nb_notes_matiere'] . "/" . $matiere[$ident_eleve_aff][$id_periode][$m]['nb_total_notes_matiere'];
			$pdf->Cell($largeur_nombre_note[$classe_id], $espace_entre_matier, $valeur,1,0,'C');
			$largeur_utilise = $largeur_utilise+$largeur_nombre_note[$classe_id];
			}

			// les moyennes eleve, classe, min, max
$cpt_ordre = 0;
while (!empty($ordre_moyenne[$cpt_ordre]) ) {
				//eleve
				if($active_moyenne_eleve[$classe_id] === '1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
				$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
				$pdf->SetFont($caractere_utilse[$classe_id],'B',10);
				$pdf->SetFillColor($couleur_reperage_eleve1[$classe_id], $couleur_reperage_eleve2[$classe_id], $couleur_reperage_eleve3[$classe_id]);

				//calcul nombre de sous affichage

				$nb_sousaffichage='1';
				if(empty($active_coef_sousmoyene)) { $active_coef_sousmoyene = ''; }

				if($active_coef_sousmoyene==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
				if($active_nombre_note[$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; }
				if($toute_moyenne_meme_col[$classe_id]==='1') { if($active_moyenne_classe[$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
				if($toute_moyenne_meme_col[$classe_id]==='1') { if($active_moyenne_min[$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }
				if($toute_moyenne_meme_col[$classe_id]==='1') { if($active_moyenne_max[$classe_id]==='1') { $nb_sousaffichage = $nb_sousaffichage + 1; } }

				// On filtre si la moyenne est vide, on affiche seulement un tiret
				if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve'] == "-") {
					$valeur = "-";
				} else {
					$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
				}
				$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier/$nb_sousaffichage, $valeur,1,2,'C',$active_reperage_eleve[$classe_id]);
				$valeur = "";

				if($active_coef_sousmoyene==='1') {
				$pdf->SetFont($caractere_utilse[$classe_id],'I',7);
				$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier/$nb_sousaffichage, 'coef. '.$matiere[$ident_eleve_aff][$id_periode][$m]['coef'],'LR',2,'C',$active_reperage_eleve[$classe_id]);
				}

				if($toute_moyenne_meme_col[$classe_id]==='1') {
					// On affiche toutes les moyennes dans la même colonne
				$pdf->SetFont($caractere_utilse[$classe_id],'I',7);
				if($active_moyenne_classe[$classe_id]==='1') {
					if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe'] == "-") {
						$valeur = "-";
					} else {
						$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
					}
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier/$nb_sousaffichage, 'cla.'.$valeur,'LR',2,'C',$active_reperage_eleve[$classe_id]);
				}
				if($active_moyenne_min[$classe_id]==='1') {
					if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min'] == "-") {
						$valeur = "-";
					} else {
						$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
					}
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier/$nb_sousaffichage, 'min.'.$valeur,'LR',2,'C',$active_reperage_eleve[$classe_id]);
				}
				if($active_moyenne_max[$classe_id]==='1') {
					if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max'] == "-") {
						$valeur = "-";
					} else {
						$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
					}
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier/$nb_sousaffichage, 'max.'.$valeur,'LR',2,'C',$active_reperage_eleve[$classe_id]);
					$valeur = ''; // on remet à vide.
				}

				}
				if($active_nombre_note[$classe_id]==='1') {
					$pdf->SetFont($caractere_utilse[$classe_id],'I',7);
					$espace_pour_nb_note = $espace_entre_matier/$nb_sousaffichage;
						$espace_pour_nb_note = $espace_pour_nb_note / 2;
					$valeur1 = ''; $valeur2 = '';
					if ( $matiere[$ident_eleve_aff][$id_periode][$m]['nb_notes_matiere'] != 0 ) {
					    if ( $matiere[$ident_eleve_aff][$id_periode][$m]['nb_notes_matiere'] == 1 ) {
						$valeur1 = $matiere[$ident_eleve_aff][$id_periode][$m]['nb_notes_matiere'].' note';
						} else {
						  $valeur1 = $matiere[$ident_eleve_aff][$id_periode][$m]['nb_notes_matiere'].' notes';
						}
						$valeur2 = 'sur '.$matiere[$ident_eleve_aff][$id_periode][$m]['nb_total_notes_matiere'];
					}
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_pour_nb_note, $valeur1, 'LR',2,'C',$active_reperage_eleve[$classe_id]);
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_pour_nb_note, $valeur2, 'LR',2,'C',$active_reperage_eleve[$classe_id]);
					$valeur1 = ''; $valeur2 = '';
				}
				$pdf->SetFont($caractere_utilse[$classe_id],'',10);
				$pdf->SetFillColor(0, 0, 0);
				$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];

				} // Fin affichage élève

				//classe
				if( $active_moyenne_classe[$classe_id] === '1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
				$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
				if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe'] == "-") {
					$valeur = "-";
				} else {
					$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
				}
				$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
				//permet le calcule de la moyenne général de la classe
				if(empty($moyenne_classe[$id_classe][$id_periode])) { $total_moyenne_classe_en_calcul=$total_moyenne_classe_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_classe']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']); }
				$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
				}
				//min
				if( $active_moyenne_min[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
				$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
				$pdf->SetFont($caractere_utilse[$classe_id],'',8);
				if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min'] == "-") {
						$valeur = "-";
				} else {
						$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
				}
				$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
				//permet le calcule de la moyenne mini
				if(empty($moyenne_min[$id_classe][$id_periode])) { $total_moyenne_min_en_calcul=$total_moyenne_min_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_min']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']); }
				$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
				}
				//max
				if( $active_moyenne_max[$classe_id] === '1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
					$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
					if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max'] == "-") {
							$valeur = "-";
					} else {
							$valeur = present_nombre($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]);
					}
					$pdf->Cell($largeur_d_une_moyenne[$classe_id], $espace_entre_matier, $valeur,'TLRB',0,'C');
				//permet le calcule de la moyenne maxi
				if(empty($moyenne_max[$id_classe][$id_periode])) { $total_moyenne_max_en_calcul=$total_moyenne_max_en_calcul+($matiere[$ident_eleve_aff][$id_periode][$m]['moy_max']*$matiere[$ident_eleve_aff][$id_periode][$m]['coef']); }
				$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
				}
//			    $largeur_utilise = $largeur_utilise+$largeur_moyenne;


			// rang de l'élève
			if($active_rang[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
			$pdf->Cell($largeur_rang[$classe_id], $espace_entre_matier, $matiere[$ident_eleve_aff][$id_periode][$m]['rang'].'/'.$matiere[$ident_eleve_aff][$id_periode][$m]['nb_eleve_rang'],1,0,'C');
			$largeur_utilise = $largeur_utilise+$largeur_rang[$classe_id];
			}

			// graphique de niveau
			if($active_graphique_niveau[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			$pdf->SetFont($caractere_utilse[$classe_id],'',10);
			$id_groupe_graph = $matiere[$ident_eleve_aff][$id_periode][$m]['id_groupe'];
			// placement de l'élève dans le graphique de niveau
				if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']!="") {
								if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<5) { $place_eleve=5;}
								if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=5) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<8))  { $place_eleve=4;}
								if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=8) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<10)) { $place_eleve=3;}
								if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=10) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<12)) {$place_eleve=2;}
								if (($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=12) and ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']<15)) { $place_eleve=1;}
								if ($matiere[$ident_eleve_aff][$id_periode][$m]['moy_eleve']>=15) { $place_eleve=0;}
							}
			if (array_sum($data_grap[$id_periode][$id_groupe_graph]) != 0) {
				$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2), $largeur_niveau[$classe_id], $espace_entre_matier, $data_grap[$id_periode][$id_groupe_graph], $place_eleve);
			}
			$place_eleve=''; // on vide la variable
			$largeur_utilise = $largeur_utilise+$largeur_niveau[$classe_id];
			}

			//appréciation
			if($active_appreciation[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
				// si on autorise l'affichage des sous matière et s'il y en a alors on les affiche
				$id_groupe_select = $matiere[$ident_eleve_aff][$id_periode][$m]['id_groupe'];
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			$X_sous_matiere = 0; $largeur_sous_matiere=0;

				if($autorise_sous_matiere[$classe_id]==='1' and !empty($sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][0]['titre'])) {
					$X_sous_matiere = $X_note_moy_app+$largeur_utilise;
					$Y_sous_matiere = $Y_decal-($espace_entre_matier/2);
					$n=0;
					$largeur_texte_sousmatiere=0; $largeur_sous_matiere=0;
					while( !empty($sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['titre']) )
					{
					$pdf->SetFont($caractere_utilse[$classe_id],'',8);
					$largeur_texte_sousmatiere = $pdf->GetStringWidth($sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['titre'].': '.$sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['moyenne']);
					if($largeur_sous_matiere<$largeur_texte_sousmatiere) { $largeur_sous_matiere=$largeur_texte_sousmatiere; }
					$n = $n + 1;
					}
					if($largeur_sous_matiere!='0') { $largeur_sous_matiere = $largeur_sous_matiere + 2; }
					$n=0;
					while( !empty($sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['titre']) )
					{
					$pdf->SetXY($X_sous_matiere, $Y_sous_matiere);
					$pdf->SetFont($caractere_utilse[$classe_id],'',8);
					$pdf->Cell($largeur_sous_matiere, $espace_entre_matier/$sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select]['nb'], $sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['titre'].': '.$sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select][$n]['moyenne'],1,0,'L');
					$Y_sous_matiere = $Y_sous_matiere+$espace_entre_matier/$sous_matiere[$ident_eleve_aff][$id_periode][$id_groupe_select]['nb'];
					$n = $n + 1;
					}
					$largeur_utilise = $largeur_utilise+$largeur_sous_matiere;
				}
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_decal-($espace_entre_matier/2));
			// calcule de la taille du texte des appréciation
			$hauteur_caractere_appreciation=9;
			$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_appreciation);
			$val = $pdf->GetStringWidth($matiere[$ident_eleve_aff][$id_periode][$m]['appreciation']);
				$largeur_appreciation2 = $largeur_appreciation - $largeur_sous_matiere;

			$taille_texte = (($espace_entre_matier/3)*$largeur_appreciation2);
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere_appreciation = $hauteur_caractere_appreciation-0.3;
					$pdf->SetFont($caractere_utilse[$classe_id],'',$hauteur_caractere_appreciation);
					$val = $pdf->GetStringWidth($matiere[$ident_eleve_aff][$id_periode][$m]['appreciation']);
				} else { $grandeur_texte='ok'; }
						}
				$grandeur_texte='test';
			$pdf->drawTextBox($matiere[$ident_eleve_aff][$id_periode][$m]['appreciation'], $largeur_appreciation2, $espace_entre_matier, 'J', 'M', 1);
			$pdf->SetFont($caractere_utilse[$classe_id],'',10);
			$largeur_utilise = $largeur_utilise + $largeur_appreciation2;
//			$largeur_utilise = 0;
			}

$cpt_ordre = $cpt_ordre + 1;
}
		$largeur_utilise = 0;
// fin de boucle d'ordre
			$Y_decal = $Y_decal+($espace_entre_matier/2);
		}



		//bas du tableau des note et app si les affichage des moyennes ne sont pas affiché le bas du tableau ne seras pas affiché
		if ( $active_moyenne[$classe_id] === '1' and $active_moyenne_general[$classe_id] === '1' ) {
		$X_note_moy_app = $X_note_app[$classe_id]; $Y_note_moy_app = $Y_note_app[$classe_id]+$hauteur_note_app[$classe_id]-$hauteur_entete_moyenne_general[$classe_id];
		$pdf->SetXY($X_note_moy_app, $Y_note_moy_app);
		$pdf->SetFont($caractere_utilse[$classe_id],'',10);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
		$pdf->Cell($largeur_matiere[$classe_id], $hauteur_entete_moyenne_general[$classe_id], "Moyenne générale",1,0,'C', $couleur_moy_general[$classe_id]);
		$largeur_utilise = $largeur_matiere[$classe_id];

		// coéfficient matière
		if($active_coef_moyenne[$classe_id]==='1') {
		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
		$pdf->Cell($largeur_coef_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], "",1,0,'C', $couleur_moy_general[$classe_id]);
		$largeur_utilise = $largeur_utilise + $largeur_coef_moyenne[$classe_id];
		}

		// nombre de note
		if($active_nombre_note_case[$classe_id]==='1') {
		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
		$pdf->Cell($largeur_nombre_note[$classe_id], $hauteur_entete_moyenne_general[$classe_id], "",1,0,'C', $couleur_moy_general[$classe_id]);
		$largeur_utilise = $largeur_utilise + $largeur_nombre_note[$classe_id];
		}

		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);

$cpt_ordre = 0;
while ( !empty($ordre_moyenne[$cpt_ordre]) ) {
			//eleve
			if($active_moyenne_eleve[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'eleve' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
			$pdf->SetFont($caractere_utilse[$classe_id],'B',10);
			$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);

			// On a deux paramètres de couleur qui se croisent. On utilise une variable tierce.
			$utilise_couleur = $couleur_moy_general[$classe_id];
			if($active_reperage_eleve[$classe_id]==='1') {
				// Si on affiche une couleur spécifique pour les moyennes de l'élève,
				// on utilise cette couleur ici aussi, quoi qu'il arrive
				$pdf->SetFillColor($couleur_reperage_eleve1[$classe_id], $couleur_reperage_eleve2[$classe_id], $couleur_reperage_eleve3[$classe_id]);
				$utilise_couleur = 1;
			}
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve'], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C',$utilise_couleur);
			//if($active_reperage_eleve==='1' and $couleur_moy_general==='1') { $couleur_moy_general = 0; }
			$pdf->SetFont($caractere_utilse[$classe_id],'',10);
			$pdf->SetFillColor(0, 0, 0);
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}

			//classe
			if($active_moyenne_classe[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'classe' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
			$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
//			if(empty($moyenne_classe[$id_classe_selection][$id_periode])) { $moyenne_classe[$id_classe_selection][$id_periode]=$total_moyenne_classe_en_calcul/$total_coef_en_calcul; }
			if($total_coef_en_calcul!=0){
				$moyenne_classe = $total_moyenne_classe_en_calcul / $total_coef_en_calcul;
			}
			else{
				$moyenne_classe="-";
			}
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($moyenne_classe, $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C', $couleur_moy_general[$classe_id]);
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}
			//min
//$moyenne_classe_minmax[$id_periode][$id_classe_selection]['min']='20';
//$moyenne_classe_minmax[$id_periode][$id_classe_selection]['max']='0';
			if($active_moyenne_min[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'min' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
			$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);

			if($total_coef_en_calcul!=0){
				$moyenne_min = $total_moyenne_min_en_calcul / $total_coef_en_calcul;
			}
			else{
				$moyenne_min="-";
			}

//		     $pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($moyenne_min[$id_classe_selection][$id_periode], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C', $couleur_moy_general);
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($moyenne_min, $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C', $couleur_moy_general[$classe_id]);
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}
			//max
			if($active_moyenne_max[$classe_id]==='1' and $active_moyenne[$classe_id] === '1' and $ordre_moyenne[$cpt_ordre] === 'max' ) {
			$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
			$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
//			if(empty($moyenne_min[$id_classe_selection][$id_periode])) { $moyenne_max[$id_classe_selection][$id_periode]=$total_moyenne_max_en_calcul/$total_coef_en_calcul; }

			if($total_coef_en_calcul!=0){
				$moyenne_max = $total_moyenne_max_en_calcul / $total_coef_en_calcul;
			} else {
				$moyenne_max="-";
			}

//		    $pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($moyenne_max[$id_classe_selection][$id_periode], $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C', $couleur_moy_general);
			$pdf->Cell($largeur_d_une_moyenne[$classe_id], $hauteur_entete_moyenne_general[$classe_id], present_nombre($moyenne_max, $arrondie_choix[$classe_id], $nb_chiffre_virgule[$classe_id], $chiffre_avec_zero[$classe_id]),1,0,'C', $couleur_moy_general[$classe_id]);
			$largeur_utilise = $largeur_utilise + $largeur_d_une_moyenne[$classe_id];
			}

		// rang de l'élève
		if($active_rang[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'rang') {
		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
			$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
		if ($rang_eleve_classe[$ident_eleve_aff][$id_periode] != 0) {
			$rang_a_afficher = $rang_eleve_classe[$ident_eleve_aff][$id_periode].'/'.$classe_effectif_tab[$id_classe_selection][$id_periode]['effectif'];
		} else {
			$rang_a_afficher = "";
		}
		$pdf->Cell($largeur_rang[$classe_id], $hauteur_entete_moyenne_general[$classe_id], $rang_a_afficher ,'TLRB',0,'C', $couleur_moy_general[$classe_id]);
		$largeur_utilise = $largeur_utilise + $largeur_rang[$classe_id];
		}

		// graphique de niveau
		if($active_graphique_niveau[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'niveau' ) {
		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
			// placement de l'élève dans le graphique de niveau
				if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']!="") {
								if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<5) { $place_eleve=5;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=5) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<8))  { $place_eleve=4;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=8) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<10)) { $place_eleve=3;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=10) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<12)) {$place_eleve=2;}
								if (($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=12) and ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']<15)) { $place_eleve=1;}
								if ($info_bulletin[$ident_eleve_aff][$id_periode]['moy_general_eleve']>=15) { $place_eleve=0;}
							}
			$pdf->DiagBarre($X_note_moy_app+$largeur_utilise, $Y_note_moy_app, $largeur_niveau[$classe_id], $hauteur_entete_moyenne_general[$classe_id], $data_grap_classe[$id_periode][$id_classe_selection], $place_eleve);
			$place_eleve=''; // on vide la variable
		$largeur_utilise = $largeur_utilise+$largeur_niveau[$classe_id];
		}
		//appréciation
		if($active_appreciation[$classe_id]==='1' and $ordre_moyenne[$cpt_ordre] === 'appreciation' ) {
		$pdf->SetXY($X_note_moy_app+$largeur_utilise, $Y_note_moy_app);
		$pdf->SetFillColor($couleur_moy_general1[$classe_id], $couleur_moy_general2[$classe_id], $couleur_moy_general3[$classe_id]);
		$pdf->Cell($largeur_appreciation, $hauteur_entete_moyenne_general[$classe_id], '','TLRB',0,'C', $couleur_moy_general[$classe_id]);
		$largeur_utilise = $largeur_utilise + $largeur_appreciation;
		}
$cpt_ordre = $cpt_ordre + 1;
}
		$largeur_utilise = 0;
// fin de boucle d'ordre
		$pdf->SetFillColor(0, 0, 0);
		}
	}

	// bloc absence
	if($active_bloc_absence[$classe_id]==='1') {
	$pdf->SetXY($X_absence[$classe_id], $Y_absence[$classe_id]);
	$origine_Y_absence = $Y_absence[$classe_id];
	$pdf->SetFont($caractere_utilse[$classe_id],'I',8);
	$info_absence='';
	if($info_bulletin[$ident_eleve_aff][$id_periode]['absences'] != '?' or $info_bulletin[$ident_eleve_aff][$id_periode]['absences_retards'] != '?') {
		if($info_bulletin[$ident_eleve_aff][$id_periode]['absences'] == '0')
		{
					$info_absence="<i>Aucune demi-journée d'absence</i>.";
			} else {
						$info_absence="<i>Nombre de demi-journées d'absence ";
				if ( $info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj'] == '0' or $info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj'] === '?' ) { $info_absence = $info_absence."justifiées "; }
						$info_absence = $info_absence.": </i><b>".$info_bulletin[$ident_eleve_aff][$id_periode]['absences']."</b>";
				if ( $info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj'] != '0' and $info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj'] != '?')
				{
								$info_absence = $info_absence." (dont <b>".$info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj']."</b> non justifiée";
					if ($info_bulletin[$ident_eleve_aff][$id_periode]['absences_nj'] > '1') { $info_absence = $info_absence."s"; }
								$info_absence = $info_absence.")";
						}
				$info_absence = $info_absence.".";
			}
		if($info_bulletin[$ident_eleve_aff][$id_periode]['absences_retards'] > '0')
		{
					$info_absence = $info_absence."<i> Nombre de retards : </i><b>".$info_bulletin[$ident_eleve_aff][$id_periode]['absences_retards']."</b>";
			}
	}
	$pdf->SetFont($caractere_utilse[$classe_id],'',8);
/*
		echo "  (C.P.E. chargé";
		$sql="SELECT civilite FROM utilisateurs WHERE login='$current_eleve_cperesp_login'";
		$res_civi=mysql_query($sql);
		if(mysql_num_rows($res_civi)>0){
			$lig_civi=mysql_fetch_object($res_civi);
			if($lig_civi->civilite!="M."){
				echo "e";
			}
		}
		echo " du suivi : ". affiche_utilisateur($current_eleve_cperesp_login,$id_classe) . ")";
			if ($current_eleve_appreciation_absences != ""){echo "<br />$current_eleve_appreciation_absences";}
			echo "</p></td>\n</tr>\n</table>\n";
		}
*/
		// MODIF: boireaus
		//$info_absence = $info_absence." (C.P.E. chargé du suivi : ".$cpe_eleve[$i].")";
		$info_absence = $info_absence." (C.P.E. chargé";
		$sql="SELECT civilite FROM utilisateurs WHERE login='".$cperesp_login[$i]."'";
		$res_civi=mysql_query($sql);
		if(mysql_num_rows($res_civi)>0){
			$lig_civi=mysql_fetch_object($res_civi);
			if($lig_civi->civilite!="M."){
				$info_absence = $info_absence."e";
			}
		}
		$info_absence = $info_absence." du suivi : ".$cpe_eleve[$i].")";
		$pdf->MultiCellTag(200, 5, $info_absence, '', 'J', '');


	if ( isset($Y_avis_cons_init) ) { $Y_avis_cons[$classe_id] = $Y_avis_cons_init; }
	if ( isset($Y_sign_chef_init) ) { $Y_sign_chef[$classe_id] = $Y_sign_chef_init; }
	if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $Y_avis_cons[$classe_id] + 0.5; }
	if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $Y_sign_chef[$classe_id] + 0.5; }

	if ( isset($hauteur_avis_cons_init) ) { $hauteur_avis_cons[$classe_id] = $hauteur_avis_cons_init; }
	if ( isset($hauteur_sign_chef_init) ) { $hauteur_sign_chef[$classe_id] = $hauteur_sign_chef_init; }
	if ( !isset($hauteur_avis_cons_init) ) { $hauteur_avis_cons_init = $hauteur_avis_cons[$classe_id] - 0.5; }
	if ( !isset($hauteur_sign_chef_init) ) { $hauteur_sign_chef_init = $hauteur_sign_chef[$classe_id] - 0.5; }

		if($info_bulletin[$ident_eleve_aff][$id_periode]['absences_appreciation'] != "")
	{

		// supprimer les espaces
		$text_absences_appreciation = trim(str_replace(array("\r\n","\r","\n"), ' ', $info_bulletin[$ident_eleve_aff][$id_periode]['absences_appreciation']));
		$info_absence_appreciation = "<i>Avis CPE:</i> <b>".$text_absences_appreciation."</b>";
		$text_absences_appreciation = '';
		$pdf->SetXY($X_absence[$classe_id], $Y_absence[$classe_id]+4);
		$pdf->SetFont($caractere_utilse[$classe_id],'',8);
		$pdf->MultiCellTag(200, 3, $info_absence_appreciation, '', 'J', '');
		//$hauteur_avis_cons_init = $hauteur_avis_cons[$classe_id];
		$val = $pdf->GetStringWidth($info_absence_appreciation);
		// nombre de ligne que prend la remarque cpe
			//Arrondi à l'entier supérieur : ceil()
		$nb_ligne = 1;
		$nb_ligne = ceil($val / 200);
		$hauteur_pris = $nb_ligne * 3;

		$Y_avis_cons[$classe_id] = $Y_avis_cons[$classe_id] + $hauteur_pris; $hauteur_avis_cons[$classe_id] = $hauteur_avis_cons[$classe_id] - ( $hauteur_pris + 0.5 );
		$Y_sign_chef[$classe_id] = $Y_sign_chef[$classe_id] + $hauteur_pris; $hauteur_sign_chef[$classe_id] = $hauteur_sign_chef[$classe_id] - ( $hauteur_pris + 0.5 );
		$hauteur_pris = 0;
	} else {
			if($Y_avis_cons_init!=$Y_avis_cons[$classe_id])
			{
			$Y_avis_cons[$classe_id] = $Y_avis_cons[$classe_id] - $hauteur_pris;
			$hauteur_avis_cons[$classe_id] = $hauteur_avis_cons[$classe_id] + $hauteur_pris;
			$Y_sign_chef[$classe_id] = $Y_sign_chef[$classe_id] - $hauteur_pris;
			$hauteur_sign_chef[$classe_id] = $hauteur_sign_chef[$classe_id] + $hauteur_pris;
			$hauteur_pris = 0;
			}
		}
	$info_absence = '';
	$info_absence_appreciation = '';
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	}

	// si le bloc absence n'est pas activé
	if($active_bloc_absence[$classe_id] != '1') {
	if ( isset($Y_avis_cons_init) ) { $Y_avis_cons[$classe_id] = $Y_avis_cons_init; }
	if ( isset($Y_sign_chef_init) ) { $Y_sign_chef[$classe_id] = $Y_sign_chef_init; }
	if ( !isset($Y_avis_cons_init) ) { $Y_avis_cons_init = $Y_avis_cons[$classe_id]; }
	if ( !isset($Y_sign_chef_init) ) { $Y_sign_chef_init = $Y_sign_chef[$classe_id]; }
	}
	// fin

	if($Y_avis_cons_init!=$Y_avis_cons[$classe_id]) {
		$Y_avis_cons[$classe_id] = $Y_avis_cons[$classe_id] + 0.5;
		$Y_sign_chef[$classe_id] = $Y_sign_chef[$classe_id] + 0.5;
	}

	// bloc avis du conseil de classe
	if($active_bloc_avis_conseil[$classe_id]==='1') {
	if($cadre_avis_cons[$classe_id]!=0) { $pdf->Rect($X_avis_cons[$classe_id], $Y_avis_cons[$classe_id], $longeur_avis_cons[$classe_id], $hauteur_avis_cons[$classe_id], 'D'); }
	$pdf->SetXY($X_avis_cons[$classe_id],$Y_avis_cons[$classe_id]);
		if ( $taille_titre_bloc_avis_conseil[$classe_id] != '' and $taille_titre_bloc_avis_conseil[$classe_id] < '15' ) { $taille = $taille_titre_bloc_avis_conseil[$classe_id]; } else { $taille = '10'; }
	$pdf->SetFont($caractere_utilse[$classe_id],'I',$taille);
		if ( $titre_bloc_avis_conseil[$classe_id] != '' ) { $tt_avis = $titre_bloc_avis_conseil[$classe_id]; } else { $tt_avis = 'Avis du Conseil de classe:'; }
	$pdf->Cell($longeur_avis_cons[$classe_id],5, $tt_avis,0,2,'');
	$pdf->SetXY($X_avis_cons[$classe_id]+2.5,$Y_avis_cons[$classe_id]+5);
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	$texteavis = $info_bulletin[$ident_eleve_aff][$id_periode]['avis_conseil_classe'];
	$pdf->drawTextBox($texteavis, $longeur_avis_cons[$classe_id]-5, $hauteur_avis_cons[$classe_id]-10, 'J', 'M', 0);
	$X_pp_aff=$X_avis_cons[$classe_id]; $Y_pp_aff=$Y_avis_cons[$classe_id]+$hauteur_avis_cons[$classe_id]-5;
	$pdf->SetXY($X_pp_aff,$Y_pp_aff);
		if ( $taille_profprincipal_bloc_avis_conseil[$classe_id] != '' and $taille_profprincipal_bloc_avis_conseil[$classe_id] < '15' ) { $taille = $taille_profprincipal_bloc_avis_conseil[$classe_id]; } else { $taille = '10'; }
	$pdf->SetFont($caractere_utilse[$classe_id],'I',$taille);
	$pdf->MultiCellTag(200, 5, $pp_classe[$i], '', 'J', '');
	}

	// bloc du chef
	if($active_bloc_chef[$classe_id]==='1') {
	if($cadre_sign_chef[$classe_id]!=0) { $pdf->Rect($X_sign_chef[$classe_id], $Y_sign_chef[$classe_id], $longeur_sign_chef[$classe_id], $hauteur_sign_chef[$classe_id], 'D'); }
	$pdf->SetXY($X_sign_chef[$classe_id],$Y_sign_chef[$classe_id]);
	$pdf->SetFont($caractere_utilse[$classe_id],'',10);
	if($affichage_haut_responsable[$classe_id]==='1') {
		if ( $affiche_fonction_chef[$classe_id] === '1' )
		{
			if ( $taille_texte_fonction_chef[$classe_id] != '' and $taille_texte_fonction_chef[$classe_id] != '0' and $taille_texte_fonction_chef[$classe_id] < '15' ) { $taille = $taille_texte_fonction_chef[$classe_id]; } else { $taille = '10'; }
			$pdf->SetFont($caractere_utilse[$classe_id],'B',$taille);
		$pdf->Cell($longeur_sign_chef[$classe_id],5, $info_classe[$id_classe_selection]['fonction_hautresponsable'],0,2,'');
		}
		if ( $taille_texte_identitee_chef[$classe_id] != '' and $taille_texte_identitee_chef[$classe_id] != '0' and $taille_texte_identitee_chef[$classe_id] < '15' ) { $taille = $taille_texte_identitee_chef[$classe_id]; } else { $taille_avis = '8'; }
		$pdf->SetFont($caractere_utilse[$classe_id],'I',$taille);
		$pdf->Cell($longeur_sign_chef[$classe_id],5, $info_classe[$id_classe_selection]['nom_hautresponsable'],0,2,'');
	} else {
			$pdf->MultiCell($longeur_sign_chef[$classe_id],5, "Visa du Chef d'établissement\nou de son délégué",0,2,'');
		}
	}
$cpt_info_periode = $cpt_info_periode+1;
}

	//$nb_eleve_aff = $nb_eleve_aff + 1;
	if ( $nb_bulletin_parent[$ident_eleve_aff] === 1 or $passage_deux === 'oui' )
	{
		//compte le nombre d'élément affiché
		$nb_eleve_aff = $nb_eleve_aff + 1;
		$passage_deux = 'non';
		$responsable_place = 0;
	}
	elseif ( $nb_bulletin_parent[$ident_eleve_aff] === 2 and $passage_deux != 'oui' )
	{
		//compte le nombre d'élément affiché
		$nb_eleve_aff = $nb_eleve_aff ;
		$passage_deux = 'oui';
		$responsable_place = 1;
	}
	elseif ( $nb_bulletin_parent[$ident_eleve_aff] === 2 and $passage_deux === 'oui' )
	{
		//compte le nombre d'élément affiché
		$nb_eleve_aff = $nb_eleve_aff + 1;
		$passage_deux = 'non';
		$responsable_place = 0;
	}


}

//vidé les variable de session utilisé
//unset($_SESSION["periode"]);
unset($_SESSION["classe"]);
unset($_SESSION["eleve"]);
unset ($_SESSION['tri_par_etab_origine']);

//fermeture du fichier pdf et lecture dans le navigateur 'nom', 'I/D'
$nom_bulletin = 'bulletin_'.$nom_bulletin.'.pdf';
$pdf->Output($nom_bulletin,'I');
//$pdf->closeParsers();
?>
