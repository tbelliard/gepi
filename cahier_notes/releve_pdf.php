<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

// Global configuration file
// Quand on est en SSL, IE n'arrive pas à ouvrir le PDF.
//Le problème peut être résolu en ajoutant la ligne suivante :
Header('Pragma: public');
// On ajoute le bon en tête sur le type de document envoyé sinon FF3 se plante
header('Content-type: application/pdf');

if (!defined('FPDF_VERSION')) {
	require('../fpdf/fpdf.php');
}


define('TopMargin','5');
define('RightMargin','5');
define('LeftMargin','5');
define('BottomMargin','5');
define('LargeurPage','210');
define('HauteurPage','297');

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Lorsque qu'on utilise une session PHP, parfois, IE n'affiche pas le PDF
// C'est un problème qui affecte certaines versions d'IE.
// Pour le contourner, on ajoutez la ligne suivante avant session_start() :
session_cache_limiter('private');

// Resume session
$resultat_session = $session_gepi->security_check();
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

//debug_var();
/*
foreach($_SESSION['classe'] as $key => $value) {
echo "\$_SESSION['classe'][$key] = $value<br />";
}
*/

// fonction qui recoit une date heure est recompose la date en français
function date_fr_dh($var)
{

	$var = explode(" ",$var);
	$var = explode("-",$var[0]);
	$var = $var[2]."/".$var[1]."/".$var[0];

	return($var);

}

// fonction pour mettre la date en français
function date_frc($var)
{

	$var = explode("/",$var);
	$date = "$var[0],$var[1],$var[2]";
	$tab_mois = array('01'=>"Jan.", '02'=>"Fev.", '03'=>"Mar.", '04'=>"Avr.", '05'=>"Mai", '06'=>"Juin", '07'=>"Juil.", '08'=>"Août", '09'=>"Sept.", '10'=>"Oct.", '11'=>"Nov.", '12'=>"Dec.");
	//$tab_jour = array("Dim.", "Lun.", "Mar.", "Mer.", "Jeu.", "Ven.", "Sam.");
	$tab_date = explode(',', $date);

	$jour = date("w", mktime(0, 0, 0, $tab_date[1], $tab_date[0], $tab_date[2]));
	if ($tab_date[0]=='01') {$tab_date[0]='1er';}
	if ($tab_date[0]=='02') {$tab_date[0]='2';}
	if ($tab_date[0]=='03') {$tab_date[0]='3';}
	if ($tab_date[0]=='04') {$tab_date[0]='4';}
	if ($tab_date[0]=='05') {$tab_date[0]='5';}
	if ($tab_date[0]=='06') {$tab_date[0]='6';}
	if ($tab_date[0]=='07') {$tab_date[0]='7';}
	if ($tab_date[0]=='08') {$tab_date[0]='8';}
	if ($tab_date[0]=='09') {$tab_date[0]='9';}

	// $date = ($tab_jour[$jour]." ".$tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
	$date = ($tab_date[0]." ".$tab_mois[$tab_date[1]]." ".$tab_date[2]);
	$var = $date;

	return($var);

}

function unhtmlentities($chaineHtml)
{

		$tmp = get_html_translation_table(HTML_ENTITIES);
		$tmp = array_flip ($tmp);
		$chaineTmp = strtr ($chaineHtml, $tmp);

		return $chaineTmp;

}

// fonction de redimensionnement d'image
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

// variable de session

	// nombre d'élève par page
	$nb_releve_par_page = $_SESSION['type'];
	// afficher les adresses des responsables
	$avec_adresse_responsable = $_SESSION['avec_adresse_responsable'];
	// choix des adresse à affiché
	$choix_adr_parent = $_SESSION['choix_adr_parent'];
	// élève sélectionné
	if(!empty($_SESSION['eleve'][0]) and $_SESSION['eleve'] != "")
	{ $id_eleve = $_SESSION['eleve']; unset($_SESSION["classe"]); } else { unset($_SESSION["eleve"]); }
	// classe sélectionné
	if(!empty($_SESSION['classe'][0]) and $_SESSION['classe'][0] != "")
	{ $id_classe = $_SESSION['classe']; } else { unset($_SESSION["classe"]); }
	// date de début et de un de sélection
	$date_debut = $_SESSION['date_debut_exp'];
	$date_fin = $_SESSION['date_fin_exp'];
	//si trie par regroupement
	if(!empty($_SESSION['active_entete_regroupement']) and $_SESSION['active_entete_regroupement'] != "")
	{ $active_entete_regroupement = $_SESSION['active_entete_regroupement']; }


// class rel_PDF
class rel_PDF extends FPDF
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
	if (mb_strtoupper($valign)=='M')
		$dy=($h-$rows*$this->FontSize)/2;
	if (mb_strtoupper($valign)=='B')
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
	$nb=mb_strlen($s);
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
				$this->Cell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
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
				return mb_substr($s,$i);
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
					$this->Cell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
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
					$this->Cell($w,$h,mb_substr($s,$j,$sep-$j),$b,2,$align,$fill);
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
				return mb_substr($s,$i);
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
		$this->Cell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
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

}
// fin de la class


// variable de la création du document

	// entête
	$X_entete_etab='5';
	$caractere_utilse='DejaVu'; // caractère utilisé dans le document
	$affiche_logo_etab='1'; // affiché le logo de l'établissement
	$entente_mel='0'; // afficher dans l'entête le mel de l'établissement
	$entente_tel='0'; // afficher dans l'entête le téléphone de l'établissement
	$entente_fax='0'; // afficher dans l'entête le fax de l'établissement
	$L_max_logo='75'; // Longeur maxi du logo
	$H_max_logo='75'; // hauteur maxi du logo

	// bloc responsable parents
	$active_bloc_adresse_parent=$avec_adresse_responsable;
	$X_parent=110; $Y_parent=40;

	//information année
	$gepiYear = getSettingValue('gepiYear');
	$annee_scolaire = $gepiYear;
	$X_cadre_eleve = '130';

	// cadre note
	$titre_du_cadre='Relevé de notes du';
	$largeur_cadre_matiere='50';
	$texte_observation='Observations:';
	$cadre_titre='0'; // affiche le cadre autour du titre ici: "relevé de notes..."
	$largeur_cadre_note_global = '200'; //largeur du cadre note global nom matiere | note | observation
	$hauteur_dun_regroupement='4'; // hauteur de la cellule regroupement

	$hauteur_du_titre = '4.5';
	$largeur_cadre_note = '95';
	$X_cadre_note = '5';

	// cadre des signature
	$hauteur_cachet = '30'; // hauteur des signatures

	// affiche le cadre signatures des parents
	if(isset($_SESSION['avec_sign_parent']) and $_SESSION['avec_sign_parent'] === '1' ) {
	   $affiche_signature_parent = '1';
	} else { $affiche_signature_parent = '0'; }
	// affiche la signature du professeur principal
	if(isset($_SESSION['avec_sign_pp']) and $_SESSION['avec_sign_pp'] === '1' ) {
	   $affiche_cachet_pp = '1';
	} else { $affiche_cachet_pp = '0'; }
	// affiche le bloc d'observation
	if(isset($_SESSION['avec_bloc_obser']) and $_SESSION['avec_bloc_obser'] === '1' ) {
	   $affiche_bloc_observation = '1';
	} else { $affiche_bloc_observation = '0'; }
	// affichage de la classe
	if(isset($_SESSION['aff_classe_nom']) and !empty($_SESSION['aff_classe_nom']) ) {
	  $aff_classe_nom = $_SESSION['aff_classe_nom'];
	} else { $aff_classe_nom = '1'; }
	// affichage des appréciations par devoir si le professeurs à choisi de l'affiché
	if(isset($_SESSION['avec_appreciation_devoir']) and !empty($_SESSION['avec_appreciation_devoir']) ) {
	  $avec_appreciation_devoir = $_SESSION['avec_appreciation_devoir'];
	} else { $avec_appreciation_devoir = ''; }


	//recherche d'information de la sélection
	$cpt_i='1';

	//requête des classes sélectionné
	if (isset($id_classe[0]))
	{

		$o=0; $prepa_requete = "";
		while(!empty($id_classe[$o]))
		{

			if($o == "0") { $prepa_requete = $prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'"'; }
			if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'" '; }
			$o = $o + 1;

		}

	}

	//requête des élèves sélectionné
	if (!empty($id_eleve[0]))
	{
		$o=0; $prepa_requete = "";
		while(!empty($id_eleve[$o]))
		{

			if($o == "0") { $prepa_requete = $prefix_base.'eleves.login = "'.$id_eleve[$o].'"'; }
			if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'eleves.login = "'.$id_eleve[$o].'" '; }
			$o = $o + 1;

		}

	}

	//tableau des données élève
		if (isset($id_classe[0])) {
			//$call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC');
			$sql='SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC';
		}
		if (isset($id_eleve[0])) {
			//$call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC');
			$sql='SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC';
		}
		//echo "$sql<br />";
		$call_eleve = mysqli_query($GLOBALS["mysqli"], $sql);

		//on compte les élèves sélectionnés
		$nb_eleves = mysqli_num_rows($call_eleve);
		while ( $donner = mysqli_fetch_array( $call_eleve ))
		{

			// information élèves
			$login[$cpt_i] = $donner['login'];
			$sexe[$cpt_i] = $donner['sexe'];

			$sql="SELECT * FROM j_eleves_regime WHERE login='".$donner['login']."';";
			$regime_doublant_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($regime_doublant_eleve)>0)
			{

				$current_eleve_regime = mysql_result($regime_doublant_eleve, 0, "regime");
				$current_eleve_doublant = mysql_result($regime_doublant_eleve, 0, "doublant");

			}
			else
			{

				$current_eleve_regime = "ext.";
				$current_eleve_doublant = "-";

			}

			if ($current_eleve_regime == "d/p") {$regime[$cpt_i]="demi-pensionnaire";}
			if ($current_eleve_regime == "ext.") {$regime[$cpt_i]="externe";}
			if ($current_eleve_regime == "int.") {$regime[$cpt_i]="interne";}
			if ($current_eleve_regime == "i-e")
			{
				if ($donner['sexe'] == "M")
				{

						$regime[$cpt_i]="interne externé";

				}
				else
				{

						$regime[$cpt_i]="interne externée";

				}

			}

			if ($current_eleve_doublant == 'R')
			{

				$redoublant[$cpt_i]="redoublant";
				if ($donner['sexe'] == "F")
				{

					$redoublant[$cpt_i]="redoublant";

				}
				else
				{

					$redoublant[$cpt_i]="redoublant";

				}

			}
			else
			{

				$redoublant[$cpt_i]="";

			}
			$ele_id_eleve[$cpt_i] = $donner['ele_id'];
			$nom[$cpt_i] = $donner['nom'];
			$prenom[$cpt_i] = $donner['prenom'];
			$naissance[$cpt_i] = $donner['naissance'];
			$classe[$cpt_i] = $donner['nom_complet'];
			$classe_nom_court[$cpt_i] = $donner['classe'];
			$classe_id_eleve[$cpt_i] = $donner['id'];
			$ident_eleve_sel1=$login[$cpt_i];
			//====================================================

			//les responsables
			$nombre_de_responsable = 0;
			//$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id )"),0);
			$nombre_de_responsable =  mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id AND (r.resp_legal='1' OR r.resp_legal='2'))"),0);
			//echo "\$nombre_de_responsable=$nombre_de_responsable<br />";
			if($nombre_de_responsable != 0)
			{

				$cpt_parents = 0;
				//$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id ) ORDER BY resp_legal ASC");
				$requete_parents = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id AND (r.resp_legal='1' OR r.resp_legal='2')) ORDER BY resp_legal ASC");
				while ($donner_parents = mysqli_fetch_array($requete_parents))
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

				if ($nombre_de_responsable == 1)
				{

				    $nom_parents[$ident_eleve_sel1][1] = '';
					$prenom_parents[$ident_eleve_sel1][1] = '';
					$adresse1_parents[$ident_eleve_sel1][1] = '';
					$adresse2_parents[$ident_eleve_sel1][1] = '';
					$ville_parents[$ident_eleve_sel1][1] = '';
					$cp_parents[$ident_eleve_sel1][1] = '';

				}

			}
			else
			{

				$civilite_parents[$ident_eleve_sel1][0] = '';
				$nom_parents[$ident_eleve_sel1][0] = '';
				$prenom_parents[$ident_eleve_sel1][0] = '';
				$adresse1_parents[$ident_eleve_sel1][0] = '';
				$adresse2_parents[$ident_eleve_sel1][0] = '';
				$ville_parents[$ident_eleve_sel1][0] = '';
				$cp_parents[$ident_eleve_sel1][0] = '';
				$nom_parents[$ident_eleve_sel1][1] = '';
				$prenom_parents[$ident_eleve_sel1][1] = '';
				$adresse1_parents[$ident_eleve_sel1][1] = '';
				$adresse2_parents[$ident_eleve_sel1][1] = '';
				$ville_parents[$ident_eleve_sel1][1] = '';
				$cp_parents[$ident_eleve_sel1][1] = '';

			}

			$cpt_i = $cpt_i + 1;

		}
	// fin de recherche d'information de la sélection

// rechercher maintenant les information par eleve sur leurs notes dans chaques matières

//on recherche les groupes qui ont des notes pour un élève donnée ($login[$nb_eleves_i])
	$nb_eleves_i='1';
	while($nb_eleves_i <= $nb_eleves)
	{

		// système de classement par ordre
		if(!isset($active_entete_regroupement)) { $active_entete_regroupement = '0'; }
		if(!isset($active_regroupement_cote)) { $active_regroupement_cote = '0'; }
		$systeme_de_classement='m.nom_complet ASC';
		if($active_regroupement_cote==='1' or $active_entete_regroupement==='1') { $systeme_de_classement = ' jmcc.priority ASC, jgc.priorite ASC, mc.id ASC,'.$systeme_de_classement; }
		if($active_regroupement_cote!='1' and $active_entete_regroupement!='1') { $systeme_de_classement = ' jgc.priorite ASC, '.$systeme_de_classement; }

		// requête dans la base
		$sql = 'SELECT *,m.nom_complet AS matiere_nom_complet,cd.nom_court AS nom_devoir,cd.coef AS coef_devoir, cd.note_sur AS note_sur, mc.nom_complet AS mat_cat_nom_complet FROM '.$prefix_base.'cn_devoirs cd, '.$prefix_base.'cn_notes_devoirs cnd, '.$prefix_base.'cn_cahier_notes ccn, '.$prefix_base.'groupes g, '.$prefix_base.'j_groupes_matieres jgm, '.$prefix_base.'j_groupes_classes jgc, '.$prefix_base.'matieres m, '.$prefix_base.'.j_matieres_categories_classes jmcc, '.$prefix_base.'matieres_categories mc
				 WHERE jgc.id_groupe = g.id
				   AND jgc.id_classe = "'.$classe_id_eleve[$nb_eleves_i].'"
				   AND jmcc.classe_id = jgc.id_classe
				   AND jmcc.categorie_id = mc.id
				   AND m.categorie_id = jmcc.categorie_id
				   AND jgm.id_groupe = g.id
				   AND jgm.id_matiere = m.matiere
				   AND cnd.login = "'.$login[$nb_eleves_i].'"
				   AND ( cd.date >= "'.$date_debut.'"
					AND cd.date <= "'.$date_fin.'" )
				   AND cnd.id_devoir = cd.id
				   AND cd.id_racine = ccn.id_cahier_notes
				   AND ccn.id_groupe = g.id
				   AND cd.display_parents = "1"
				 ORDER BY '.$systeme_de_classement.', m.nom_complet ASC , g.id ASC, cd.date ASC';

		//echo $sql."<br />";

		$base_complete_information = mysqli_query($GLOBALS["mysqli"], $sql);

		// répartition des informations pour un relevé
		$id_groupe_avant = "";
		$nb_matiere_cpt='1';
		$eleve_select=$login[$nb_eleves_i];
		$login_passe='';
		$regroupement_passer='';
		while($donne_requete = mysqli_fetch_array($base_complete_information))
		{

			$sql_groupe = "SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='".$donne_requete['id_groupe']."' AND login='".$donne_requete['login']."';";
			$test_eleve_grp=mysqli_query($GLOBALS["mysqli"], $sql_groupe);

			if ( mysqli_num_rows($test_eleve_grp)>0 )
			{

				if($donne_requete['login']!=$login_passe) { $nb_matiere_cpt='1'; }

				//on vérifie si c'est le même id de groupe pour mettre toutes les notes d'un groupe en même temps puis compter le nombre de groupe
				if($donne_requete['id_groupe']!=$id_groupe_avant)
				{

					$id_groupe_selectionne=$donne_requete['id_groupe'];
					$id_classe=$donne_requete['id_classe'];
					$groupe_select[$eleve_select][$nb_matiere_cpt]=$donne_requete['id_groupe'];
					//$name[$eleve_select][$nb_matiere_cpt] = $donne_requete[30]; //$donner_toute_matier['name']
					//$name[$eleve_select][$nb_matiere_cpt] = $donne_requete[31]; //$donner_toute_matier['name']
					//$name[$eleve_select][$nb_matiere_cpt] = $donne_requete['nom_complet']; //$donner_toute_matier['name']
					$name[$eleve_select][$nb_matiere_cpt] = $donne_requete['matiere_nom_complet']; //$donner_toute_matier['name']

					// si nom des devoirs
					if(isset($_SESSION['avec_nom_devoir']) and $_SESSION['avec_nom_devoir'] == 'oui')
					{

						//$nom_devoir_oui = " (".$donne_requete[3].")";
						//$nom_devoir_oui = " (".$donne_requete[3].")";
						$nom_devoir_oui= " (".$donne_requete['nom_devoir'].")";

					}
					else
					{

						$nom_devoir_oui='';

					}
					//=======================

					// si coef
					$coef_oui = '';
					if(isset($_SESSION['avec_coef']) and ( $_SESSION['avec_coef'] == 'oui1' or $_SESSION['avec_coef'] == 'oui2') )
					{
						//if ( $_SESSION['avec_coef'] == 'oui1' ) { $coef_oui = " (coef: ".$donne_requete[8].")"; }
						//if ( $_SESSION['avec_coef'] == 'oui2' ) { if ( $donne_requete[8] != '1.0' ) { $coef_oui = " (coef: ".$donne_requete[8].")"; } else { $coef_oui = ''; } }

						$tmp_coef_devoir=$donne_requete['coef_devoir'];
						if ( $_SESSION['avec_coef'] == 'oui1' ) { $coef_oui = " (coef: ".$tmp_coef_devoir.")"; }
						if ( $_SESSION['avec_coef'] == 'oui2' ) { if ( $tmp_coef_devoir != '1.0' ) { $coef_oui = " (coef: ".$tmp_coef_devoir.")"; } else { $coef_oui = ''; } }

					}
					else
					{

						$coef_oui='';

					}
					//=======================

					// si date devoir
					if(isset($_SESSION['avec_date_devoir']) and $_SESSION['avec_date_devoir'] == '1' )
					{

						$date_devoir_oui = " (".date_fr_dh($donne_requete['date']).") ";

					}
					else
					{

						$date_devoir_oui='';

					}
					//=======================

					// si affiché l'appréciation
					if ( $avec_appreciation_devoir == 'oui' )
					{

						$appdevoir_chaine = str_replace("&#039;", "'", unhtmlentities($donne_requete['comment']));
						$appdevoir = ' - ' . $appdevoir_chaine;

					}
					else
					{

						$appdevoir = '';

				 	}
					//=======================

					// =======================================
					// Modif: boireaus d'après C.Chapel
					//$notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note']."".$nom_devoir_oui;
					// gestion de la notation si statut est défini alors on l'affiche à la place de la note
					// si le statut est égale = - alors on considère que l'élève n'a pas participé au devoir donc il ne sera pas affiché sur son relevé.
					//=================
					if ( $donne_requete['note'] == '0.0' )
					{

						if ( $donne_requete['statut'] != '' )
						{

							if ( $donne_requete['statut'] != '-' and $donne_requete['statut'] != 'v' )
							{

								$notes[$eleve_select][$nb_matiere_cpt]=$donne_requete['statut'];

							}
							else
							{

								// Si c'est vide 'v', on ne met rien...
								// Faudrait-il indiquer 'Non Noté'?
								$notes[$eleve_select][$nb_matiere_cpt]="";

							}

						}
						else
						{

							if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $donne_requete['note_sur']!=getSettingValue("referentiel_note")) {
								$notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note']."/".$donne_requete['note_sur'];
							} else {
								$notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note'];
							}

						}

					}
					else
					{

						// si différent de 0
						if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $donne_requete['note_sur']!=getSettingValue("referentiel_note")) {
							$notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note']."/".$donne_requete['note_sur'];
						} else {
							$notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note'];
						}

					}
					//=======================

					// si une note est validé
					if ( $notes[$eleve_select][$nb_matiere_cpt] != '' )
					{

						// on affiche sur le relevé
						$notes[$eleve_select][$nb_matiere_cpt] = $notes[$eleve_select][$nb_matiere_cpt]."".$nom_devoir_oui."".$coef_oui."".$date_devoir_oui."".$appdevoir;

					}
					//=================

					// =======================================
					//$nom_regroupement[$eleve_select][$nb_matiere_cpt]=$donne_requete['nom_complet'];
					$nom_regroupement[$eleve_select][$nb_matiere_cpt]=$donne_requete['mat_cat_nom_complet'];
					if($nom_regroupement[$eleve_select][$nb_matiere_cpt]!=$regroupement_passer) {
						if(empty($nb_regroupement[$eleve_select])) { $nb_regroupement[$eleve_select] = 0; }
						$nb_regroupement[$eleve_select]=$nb_regroupement[$eleve_select]+1;
					}
					$regroupement_passer=$nom_regroupement[$eleve_select][$nb_matiere_cpt];

					// autre requete pour rechercher les professeur responsable de la matière sélectionné
					if(empty($prof_groupe[$id_groupe_selectionne][0]))
					{

						$call_profs = mysqli_query($GLOBALS["mysqli"], 'SELECT u.login FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe="'.$id_groupe_selectionne.'") ORDER BY j.ordre_prof');
						$nombre_profs = mysqli_num_rows($call_profs);
						$k = 0;

						while ($k < $nombre_profs)
						{

							$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
							$prof_groupe[$id_groupe_selectionne][$k]=affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe);
							$k++;

						}

					}

					$nb_matiere[$eleve_select]=$nb_matiere_cpt;
					$nb_num_matiere_passe=$nb_matiere_cpt;
					$nb_matiere_cpt = $nb_matiere_cpt + 1;

				}
				else
				{

					// si nom de devoir
					if($_SESSION['avec_nom_devoir'] == 'oui')
					{

						//$nom_devoir_oui = " (".$donne_requete[3].")";
						$nom_devoir_oui = " (".$donne_requete['nom_devoir'].")";

					}
					//=======================

					// si coef
					$coef_oui = '';
					if(isset($_SESSION['avec_coef']) and ( $_SESSION['avec_coef'] == 'oui1' or $_SESSION['avec_coef'] == 'oui2') )
					{

						//if ( $_SESSION['avec_coef'] == 'oui1' ) { $coef_oui = " (coef: ".$donne_requete[8].")"; }
						//if ( $_SESSION['avec_coef'] == 'oui2' ) { if ( $donne_requete[8] != '1.0' ) { $coef_oui = " (coef: ".$donne_requete[8].")"; } else { $coef_oui = ''; } }

						$tmp_coef_devoir=$donne_requete['coef_devoir'];
						if ( $_SESSION['avec_coef'] == 'oui1' ) { $coef_oui = " (coef: ".$tmp_coef_devoir.")"; }
						if ( $_SESSION['avec_coef'] == 'oui2' ) { if ( $tmp_coef_devoir != '1.0' ) { $coef_oui = " (coef: ".$tmp_coef_devoir.")"; } else { $coef_oui = ''; } }

					}
					else
					{

						$coef_oui = '';

					}
					//=======================

					// si date devoir
					if(isset($_SESSION['avec_date_devoir']) and $_SESSION['avec_date_devoir'] == '1' )
					{

						$date_devoir_oui = " (".date_fr_dh($donne_requete['date']).") ";

					}
					else
					{

						$date_devoir_oui='';

					}
					//=======================

					// si affiché l'appréciation
					if ( $avec_appreciation_devoir == 'oui' )
					{

						$appdevoir_chaine = str_replace("&#039;", "'", unhtmlentities($donne_requete['comment']));
						$appdevoir = ' - ' . $appdevoir_chaine;
						//$appdevoir = ' - ' . unhtmlentities($donne_requete['comment']);

					}
					else
					{

						$appdevoir = '';

				 	}
					//=======================

					// =======================================
					// Modif: boireaus d'après C.Chapel

					if ( $donne_requete['note'] === '0.0' )
					{

						if ( $donne_requete['statut'] != '' )
						{

							if ( $donne_requete['statut'] != 'v' )
							{

								$notes_actif=$donne_requete['statut'];

							}
							else
							{

								// Si c'est vide 'v', on ne met rien...
								// Faudrait-il indiquer 'Non Noté'?
								$notes_actif="";

							}

						}
						else
						{

							if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $donne_requete['note_sur']!=getSettingValue("referentiel_note")) {
								$notes_actif = $donne_requete['note']."/".$donne_requete['note_sur'];
							} else {
								$notes_actif = $donne_requete['note'];
							}

						}

					}
					else
					{

						// si note différent de 0
							if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $donne_requete['note_sur']!=getSettingValue("referentiel_note")) {
								$notes_actif = $donne_requete['note']."/".$donne_requete['note_sur'];
							} else {
								$notes_actif = $donne_requete['note'];
							}

					}

					// On n'ajoute que si il y a eu une saisie au devoir et pas un statut 'v'.
					if($notes_actif!="")
					{

						if($notes[$eleve_select][$nb_num_matiere_passe]!="")
						{

							$notes[$eleve_select][$nb_num_matiere_passe].=" - ";

						}

						// information sur le relevé
						$notes[$eleve_select][$nb_num_matiere_passe].=$notes_actif."".$nom_devoir_oui."".$coef_oui."".$date_devoir_oui."".$appdevoir;

					}
					// =======================================

				}

			}

			$id_groupe_avant = $donne_requete['id_groupe'];
			$login_passe=$donne_requete['login'];

		}

		$nb_eleves_i=$nb_eleves_i+1;

	}

//echo "\$nb_eleves_i=$nb_eleves_i<br />";

// Définition de la page
$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin(TopMargin);
$pdf->SetRightMargin(RightMargin);
$pdf->SetLeftMargin(LeftMargin);
$pdf->SetAutoPageBreak(true, BottomMargin);

// Couleur des traits
$pdf->SetDrawColor(0,0,0);

// Caractères utilisés
$caractere_utilse = 'DejaVu';

// on appelle une nouvelle page pdf
// modif eric 16022008
switch ($choix_adr_parent) {
    case '1' :
	    $responsable_place = 0;
		$nb_boucle_a_faire = 1;
	break;
	case '2' :
	    $responsable_place = 0; // lors de la 1er boucle
		$nb_boucle_a_faire = 2;
	break;
	case '3' :
	    $responsable_place = 1;
		$nb_boucle_a_faire = 1;
	break;
	case '4' :
	    $responsable_place = 1; // les responsables N°2 uniquement
		$nb_boucle_a_faire = 1;
	break;
    default :
	    $responsable_place = 0;
		$nb_boucle_a_faire = 1;
}

$pref_output_mode_pdf=get_output_mode_pdf();

$nb_eleves_i = 1;
$nb_boucle = 0; //compteur de boucle à faire pour R1 et R2 != R1
//while (($nb_eleves_i <= $nb_eleves) and ($nb_boucle <= $nb_boucle_a_faire))
while (($nb_eleves_i <= $nb_eleves) and ($nb_boucle < $nb_boucle_a_faire))
{

	//cas N°4 uniquement les responsables 2 différent de responsable 1
	// et Cas N°2 lors de la 2ème boucle
	if (($active_bloc_adresse_parent == '1') and ($choix_adr_parent == '4') or
		((($active_bloc_adresse_parent == '1') and ($choix_adr_parent == '2')) and ($nb_boucle == 1)) )	{

		//test si les adresses sont identiques

		$temoin = true;
		//tant que l'on ne trouve pas 2 adresses différentes on boucle et on incrément le compteur $nb_eleves_i
		while ($temoin) {
			$ident_eleve=$login[$nb_eleves_i];

			if (($adresse1_parents[$ident_eleve][0] != $adresse1_parents[$ident_eleve][1]) or
				($adresse2_parents[$ident_eleve][0] != $adresse2_parents[$ident_eleve][1]) or
				($ville_parents[$ident_eleve][0] != $ville_parents[$ident_eleve][1]) or
				($cp_parents[$ident_eleve][0] != $cp_parents[$ident_eleve][1]) ) {

				$adresse2_vide = false;
				//si l'adresse N°2 est vide, (pas de nom de famille)
				if ($nom_parents[$ident_eleve][1] == '') {
					/*($adresse1_parents[$ident_eleve][1] == '') and
					($adresse2_parents[$ident_eleve][1] == '') and
					($ville_parents[$ident_eleve][1] == '') and
					($cp_parents[$ident_eleve][1] == '') ) { */

				$nb_eleves_i++;
				$adresse2_vide = true;

				} else {
					$temoin=false;
				}

				if (!$adresse2_vide) {$temoin=false;}

			} else {
				// R1 et R2 sont différentes
				$nb_eleves_i++;
			}

			if ($nb_eleves_i > $nb_eleves) {
				$temoin = false;
				$nb_eleves_i = $nb_eleves_i - 1;

				//si on dépase le nombre d'élèves, on clos le fichier PDF
				// sortie PDF sur écran
				$nom_releve=date("Ymd_Hi");
				$nom_releve = 'Releve_'.$nom_releve.'.pdf';
				$pdf->Output($nom_releve, $pref_output_mode_pdf);
				die();
			}
		} // while temoin
	}

	$pdf->AddPage("P");
	$pdf->SetFontSize(10);

	if($nb_releve_par_page === '1' and $active_bloc_adresse_parent != '1') { $hauteur_cadre_note_global = 250; }
	if($nb_releve_par_page === '1' and $active_bloc_adresse_parent === '1') { $hauteur_cadre_note_global = 205; }
	if($nb_releve_par_page === '2') { $hauteur_cadre_note_global = 102; }

	$passage_i = 1;

		while($passage_i <= $nb_releve_par_page and $nb_eleves_i <= $nb_eleves)
		{
			// login de l'élève
			$eleve_select=$login[$nb_eleves_i];

			// différente Y pour les présentation sur 1 ou 2 par page avec ident parents
			if($nb_releve_par_page=='1' and $passage_i == '1' and $active_bloc_adresse_parent!='1') { $Y_cadre_note = '32'; $Y_cadre_eleve = '5'; $Y_entete_etab='5'; }
			if($nb_releve_par_page=='1' and $passage_i == '1' and $active_bloc_adresse_parent==='1') { $Y_cadre_note = '75'; $Y_cadre_eleve = '5'; $Y_entete_etab='5'; }
			if($nb_releve_par_page=='2' and $passage_i == '1') { $Y_cadre_note = '32'; $Y_cadre_eleve = '5'; $Y_entete_etab='5'; }
			if($nb_releve_par_page=='2' and $passage_i == '2') { $Y_cadre_note = $Y_cadre_note+145; $Y_cadre_eleve = $Y_cadre_eleve+145; $Y_entete_etab=$Y_entete_etab+145; }

		//BLOC IDENTITE ELEVE
			$pdf->SetXY($X_cadre_eleve,$Y_cadre_eleve);
			$pdf->SetFont('DejaVu','B',14);
			$pdf->Cell(90,7,my_strtoupper($nom[$nb_eleves_i])." ".casse_mot($prenom[$nb_eleves_i],'majf2'),0,2,'');
			$pdf->SetFont('DejaVu','',10);
			//$pdf->Cell(90,5,'Né le '.affiche_date_naissance($naissance[$nb_eleves_i]).', demi-pensionnaire',0,2,'');
			if($sexe[$nb_eleves_i]=="M"){$e_au_feminin="";}else{$e_au_feminin="e";}
			$pdf->Cell(90,5,'Né'.$e_au_feminin.' le '.affiche_date_naissance($naissance[$nb_eleves_i]).', '.$regime[$nb_eleves_i],0,2,'');
			$pdf->Cell(90,5,'',0,2,'');

			if ( $aff_classe_nom === '1' or $aff_classe_nom === '3' ) {
				$classe_aff = $pdf->WriteHTML('Classe de <B>'.unhtmlentities($classe[$nb_eleves_i]).'<B>');
			}
			if ( $aff_classe_nom === '2' ) {
				$classe_aff = $pdf->WriteHTML('Classe de <B>'.unhtmlentities($classe_nom_court[$nb_eleves_i]).'<B>');
			}
			if ( $aff_classe_nom === '3' ) {
				$classe_aff = $pdf->WriteHTML(' ('.unhtmlentities($classe_nom_court[$nb_eleves_i]).')');
			}

			$pdf->Cell(90,5,$classe_aff,0,2,'');
			$pdf->SetX($X_cadre_eleve);
			$pdf->SetFont('DejaVu','',10);
			$pdf->Cell(90,5,'Année scolaire '.$annee_scolaire,0,2,'');

		// BLOC IDENTITE DE L'ETABLISSEMENT
			$logo = '../images/'.getSettingValue('logo_etab');
			$format_du_logo = str_replace('.','',strstr(getSettingValue('logo_etab'), '.'));
			if($affiche_logo_etab==='1' and file_exists($logo) and getSettingValue('logo_etab') != '' and ($format_du_logo==='jpg' or $format_du_logo==='png'))
			{
			$valeur=redimensionne_image($logo, $L_max_logo, $H_max_logo);
			//$X_logo et $Y_logo; placement du bloc identite de l'établissement
			$X_logo=$X_entete_etab; $Y_logo=$Y_entete_etab; $L_logo=$valeur[0]; $H_logo=$valeur[1];
			$X_etab=$X_logo+$L_logo; $Y_etab=$Y_logo;
			//logo
				$pdf->Image($logo, $X_logo, $Y_logo, $L_logo, $H_logo);
			} else {
				$X_etab = $X_entete_etab; $Y_etab = $Y_entete_etab;
				}

		// BLOC ADRESSE ETABLISSEMENT
			$pdf->SetXY($X_etab,$Y_etab);
			$pdf->SetFont('DejaVu','',14);
			$gepiSchoolName = getSettingValue('gepiSchoolName');
			$pdf->Cell(90,7, $gepiSchoolName,0,2,'');
			$pdf->SetFont('DejaVu','',10);
			$gepiSchoolAdress1 = getSettingValue('gepiSchoolAdress1');
			$pdf->Cell(90,5, $gepiSchoolAdress1,0,2,'');
			$gepiSchoolAdress2 = getSettingValue('gepiSchoolAdress2');
			$pdf->Cell(90,5, $gepiSchoolAdress2,0,2,'');
			$gepiSchoolZipCode = getSettingValue('gepiSchoolZipCode');
			$gepiSchoolCity = getSettingValue('gepiSchoolCity');
			$pdf->Cell(90,5, $gepiSchoolZipCode." ".$gepiSchoolCity,0,2,'');
			$gepiSchoolTel = getSettingValue('gepiSchoolTel');
			$gepiSchoolFax = getSettingValue('gepiSchoolFax');
			if($entente_tel==='1' and $entente_fax==='1') { $entete_communic = 'Tél: '.$gepiSchoolTel.' / Fax: '.$gepiSchoolFax; }
			if($entente_tel==='1' and empty($entete_communic)) { $entete_communic = 'Tél: '.$gepiSchoolTel; }
			if($entente_fax==='1' and empty($entete_communic)) { $entete_communic = 'Fax: '.$gepiSchoolFax; }
			if(isset($entete_communic) and $entete_communic!='') {
			$pdf->Cell(90,5, $entete_communic,0,2,'');
			}
			if($entente_mel==='1') {
			$gepiSchoolEmail = getSettingValue('gepiSchoolEmail');
			$pdf->Cell(90,5, $gepiSchoolEmail,0,2,'');
			}

		// BLOC ADRESSE DES PARENTS
			if($active_bloc_adresse_parent==='1' and $nb_releve_par_page==='1') {
			$ident_eleve_aff=$login[$nb_eleves_i];
			$pdf->SetXY($X_parent,$Y_parent);
            //==========================================
            // MODIF: 20081021
			//$texte_1_responsable = $civilite_parents[$ident_eleve_aff][$responsable_place]." ".$nom_parents[$ident_eleve_aff][$responsable_place]." ".$prenom_parents[$ident_eleve_aff][$responsable_place];
			$texte_1_responsable="";
            if(isset($civilite_parents[$ident_eleve_aff][$responsable_place])) {$texte_1_responsable.=$civilite_parents[$ident_eleve_aff][$responsable_place]." ";}
            if(isset($nom_parents[$ident_eleve_aff][$responsable_place])) {$texte_1_responsable.=$nom_parents[$ident_eleve_aff][$responsable_place]." ";}
            if(isset($prenom_parents[$ident_eleve_aff][$responsable_place])) {$texte_1_responsable.=$prenom_parents[$ident_eleve_aff][$responsable_place]." ";}
            //==========================================
				$hauteur_caractere=12;
				$pdf->SetFont('DejaVu','B',$hauteur_caractere);
				$val = $pdf->GetStringWidth($texte_1_responsable);
				$taille_texte = 90;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont('DejaVu','B',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else { $grandeur_texte='ok'; }
					}
			$pdf->Cell(90,7, $texte_1_responsable,0,2,'');
			$pdf->SetFont('DejaVu','',10);
			$texte_1_responsable = $adresse1_parents[$ident_eleve_aff][$responsable_place];
				$hauteur_caractere=10;
				$pdf->SetFont('DejaVu','',$hauteur_caractere);
				$val = $pdf->GetStringWidth($texte_1_responsable);
				$taille_texte = 90;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont('DejaVu','',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else { $grandeur_texte='ok'; }
					}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
			$texte_1_responsable = $adresse2_parents[$ident_eleve_aff][$responsable_place];
				$hauteur_caractere=10;
				$pdf->SetFont('DejaVu','',$hauteur_caractere);
				$val = $pdf->GetStringWidth($texte_1_responsable);
				$taille_texte = 90;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont('DejaVu','',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else { $grandeur_texte='ok'; }
					}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
			$pdf->Cell(90,5, '',0,2,'');
			$texte_1_responsable = $cp_parents[$ident_eleve_aff][$responsable_place]." ".$ville_parents[$ident_eleve_aff][$responsable_place];
				$hauteur_caractere=10;
				$pdf->SetFont('DejaVu','',$hauteur_caractere);
				$val = $pdf->GetStringWidth($texte_1_responsable);
				$taille_texte = 90;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				if($taille_texte<$val)
				{
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont('DejaVu','',$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte_1_responsable);
				} else { $grandeur_texte='ok'; }
					}
			$pdf->Cell(90,5, $texte_1_responsable,0,2,'');
			}

		// BLOC NOTATION ET OBSERVATION
			//Titre du tableau
			$pdf->SetXY($X_cadre_note,$Y_cadre_note);
			$pdf->SetFont('DejaVu','B',12);
			if($cadre_titre==='1') { $var_encadrement_titre='LTR'; } else { $var_encadrement_titre=''; }
			$pdf->Cell(0, $hauteur_du_titre, $titre_du_cadre.' '.date_frc($_SESSION['date_debut_aff']).' au '.date_frc($_SESSION['date_fin_aff']), $var_encadrement_titre,0,'C');
			$hauteur_utilise = $hauteur_du_titre;

		//s'il y des notes alors on affiche le cadre avec les notes
		if(isset($nb_matiere[$eleve_select]) and !empty($nb_matiere[$eleve_select])) {
			// Hauteur d'une ligne pour une matière
			if($active_entete_regroupement === '1') { $hauteur_cadre_matiere=($hauteur_cadre_note_global-($nb_regroupement[$eleve_select]*$hauteur_dun_regroupement))/$nb_matiere[$eleve_select]; }
			if($active_entete_regroupement != '1') { $hauteur_cadre_matiere=$hauteur_cadre_note_global/$nb_matiere[$eleve_select]; }

			// Tableau des matières et des notes de l'élève
			$cpt_i='1';
			$nom_regroupement_passer='';
			while($cpt_i<=$nb_matiere[$eleve_select])
			{
				$id_groupe_selectionne=$groupe_select[$eleve_select][$cpt_i];
				//MATIERE
				$pdf->SetXY($X_cadre_note,$Y_cadre_note+$hauteur_utilise);

					// on affiche les nom des regroupement
					if($nom_regroupement[$eleve_select][$cpt_i]!=$nom_regroupement_passer and $active_entete_regroupement === '1')
					{
						$pdf->SetFont('DejaVu','',8);
						$pdf->Cell($largeur_cadre_matiere, $hauteur_dun_regroupement, unhtmlentities($nom_regroupement[$eleve_select][$cpt_i]), 'LTB', 2, '');
						$hauteur_utilise=$hauteur_utilise+$hauteur_dun_regroupement;
						$nom_regroupement_passer=$nom_regroupement[$eleve_select][$cpt_i];
						$pdf->SetXY($X_cadre_note,$Y_cadre_note+$hauteur_utilise);
					}
				$pdf->SetFont('DejaVu','B','9');
				$nom_matiere = $name[$eleve_select][$cpt_i];
					$hauteur_caractere = 9;
					$pdf->SetFont('DejaVu','B',$hauteur_caractere);
					$val = $pdf->GetStringWidth($nom_matiere);
					$taille_texte = $largeur_cadre_matiere;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
						$hauteur_caractere = $hauteur_caractere-0.3;
						$pdf->SetFont('DejaVu','B',$hauteur_caractere);
						$val = $pdf->GetStringWidth($nom_matiere);
					} else { $grandeur_texte='ok'; }
						}
				$pdf->Cell($largeur_cadre_matiere, $hauteur_cadre_matiere/2, $nom_matiere, 'LRT', 2, '');
				$nom_matiere = '';

				$nb_prof_matiere = count($prof_groupe[$id_groupe_selectionne]);
				$espace_matiere_prof = $hauteur_cadre_matiere/2;
				$nb_pass_count = '0';
				$text_prof = '';
					if ( $nb_releve_par_page === '2' )
					{
						$nb_pass_count_2 = 0;
						while ( !empty($prof_groupe[$id_groupe_selectionne][$nb_pass_count_2]) )
						{
							if ( $nb_pass_count_2 === 0 ) { $text_prof = $prof_groupe[$id_groupe_selectionne][$nb_pass_count_2]; }
							if ( $nb_pass_count_2 != 0 ) { $text_prof = $text_prof.', '.$prof_groupe[$id_groupe_selectionne][$nb_pass_count_2]; }
						$nb_pass_count_2 = $nb_pass_count_2 + 1;
						}
					$nb_prof_matiere = 1;
					}
				if ( $nb_prof_matiere != 1 ) { $espace_matiere_prof = $espace_matiere_prof/$nb_prof_matiere; }
				while ($nb_prof_matiere > $nb_pass_count)
				{
					// calcule de la hauteur du caractère du prof
					if ( $nb_releve_par_page === '1' ) { $text_prof = $prof_groupe[$id_groupe_selectionne][$nb_pass_count]; }
					if ( $nb_prof_matiere <= 2 ) { $hauteur_caractere_prof = 9; }
					elseif ( $nb_prof_matiere == 3) { $hauteur_caractere_prof = 7; }
					elseif ( $nb_prof_matiere > 3) { $hauteur_caractere_prof = 2; }
					$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
					$val = $pdf->GetStringWidth($text_prof);
					$taille_texte = ($largeur_cadre_matiere-0.6);
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
						$hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
						$pdf->SetFont('DejaVu','',$hauteur_caractere_prof);
						$val = $pdf->GetStringWidth($text_prof);
					} else { $grandeur_texte='ok'; }
							}
					$grandeur_texte='test';
						$pdf->SetX($X_cadre_note);
					if( empty($prof_groupe[$id_groupe_selectionne][$nb_pass_count+1]) or $nb_prof_matiere === 1 ) {
						$pdf->Cell($largeur_cadre_matiere, $espace_matiere_prof, $text_prof, 'LRB', 2, '');
					}
					if( !empty($prof_groupe[$id_groupe_selectionne][$nb_pass_count+1]) and $nb_prof_matiere != 1 ) {
						$pdf->Cell($largeur_cadre_matiere, $espace_matiere_prof, $text_prof, 'LR', 2, '');
					}
				$nb_pass_count = $nb_pass_count + 1;
				}

	//			if(isset($prof_groupe[$id_groupe_selectionne][0]) and $prof_groupe[$id_groupe_selectionne][0] != '') { $prof_1 = $prof_groupe[$id_groupe_selectionne][0]; } else { $prof_1 = ''; }
	//			if(isset($prof_groupe[$id_groupe_selectionne][1]) and $prof_groupe[$id_groupe_selectionne][1] != '') { $prof_2 = $prof_groupe[$id_groupe_selectionne][1]; } else { $prof_2 = ''; }
	//			if(isset($prof_groupe[$id_groupe_selectionne][2]) and $prof_groupe[$id_groupe_selectionne][2] != '') { $prof_3 = $prof_groupe[$id_groupe_selectionne][2]; } else { $prof_3 = ''; }
	/*			 $nom_prof = $prof_1;
					$hauteur_caractere = 8;
					$pdf->SetFont('DejaVu','I',$hauteur_caractere);
					$val = $pdf->GetStringWidth($nom_prof);
					$taille_texte = $largeur_cadre_matiere;
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
						$hauteur_caractere = $hauteur_caractere-0.3;
						$pdf->SetFont('DejaVu','I',$hauteur_caractere);
						$val = $pdf->GetStringWidth($nom_prof);
					} else { $grandeur_texte='ok'; }
						}

				$pdf->Cell($largeur_cadre_matiere, $hauteur_cadre_matiere/2, $nom_prof, 'LRB', 2, '');*/
	//			$pdf->Cell($largeur_cadre_matiere, $hauteur_cadre_matiere/3, $prof_2, 'LR', 2, '');
	//			$pdf->Cell($largeur_cadre_matiere, $hauteur_cadre_matiere/4, $prof_3, 'LRB', 2, '');
				$hauteur_utilise=$hauteur_utilise+$hauteur_cadre_matiere;

			$cpt_i=$cpt_i+1;
			}

				$hauteur_utilise = $hauteur_du_titre;

			$cpt_i='1';
			$nom_regroupement_passer='';
			while($cpt_i<=$nb_matiere[$eleve_select])
			{
				//NOTES
				$largeur_utilise=$largeur_cadre_matiere;
				//=======================
				// AJOUT: chapel 20071019
				if ( $affiche_bloc_observation === '1' ) { $largeur_cadre_note = $largeur_cadre_note; } else { $largeur_cadre_note = $largeur_cadre_note_global - $largeur_utilise; }
				//=======================
				$pdf->SetXY($X_cadre_note+$largeur_utilise,$Y_cadre_note+$hauteur_utilise);
					// on affiche les nom des regroupement
					if($nom_regroupement[$eleve_select][$cpt_i]!=$nom_regroupement_passer and $active_entete_regroupement === '1')
					{
						$pdf->SetFont('DejaVu','',8);
						$pdf->Cell($largeur_cadre_note, $hauteur_dun_regroupement, '', 'RTB', 2, '');
						$hauteur_utilise=$hauteur_utilise+$hauteur_dun_regroupement;
						$nom_regroupement_passer=$nom_regroupement[$eleve_select][$cpt_i];
						$pdf->SetXY($X_cadre_note+$largeur_utilise,$Y_cadre_note+$hauteur_utilise);
					}
				// détermine la taille de la police de caractère
				// on peut allez jusqu'a 275mm de caractère dans trois cases de notes
					$hauteur_caractere_notes=9;
					$pdf->SetFont('DejaVu','',$hauteur_caractere_notes);
					$val = $pdf->GetStringWidth($notes[$eleve_select][$cpt_i]);
					$taille_texte = (($hauteur_cadre_matiere/4)*$largeur_cadre_note);
					$grandeur_texte='test';
					while($grandeur_texte!='ok') {
					if($taille_texte<$val)
					{
						$hauteur_caractere_notes = $hauteur_caractere_notes-0.3;
						$pdf->SetFont('DejaVu','',$hauteur_caractere_notes);
						$val = $pdf->GetStringWidth($notes[$eleve_select][$cpt_i]);
					} else { $grandeur_texte='ok'; }
							}
				$pdf->drawTextBox($notes[$eleve_select][$cpt_i], $largeur_cadre_note, $hauteur_cadre_matiere, 'J', 'M', 1);
				$hauteur_utilise=$hauteur_utilise+$hauteur_cadre_matiere;

			$cpt_i=$cpt_i+1;
			}

		// BLOC OBSERVATION
			//=======================
			// MODIF: chapel 20071019
			if($affiche_bloc_observation === '1')
			{
				$largeur_utilise=$largeur_cadre_matiere+$largeur_cadre_note;
				$largeur_restant=$largeur_cadre_note_global-$largeur_utilise;
				$hauteur_utilise = $hauteur_du_titre;
				if($affiche_cachet_pp==='1' or $affiche_signature_parent==='1')
				{
					$hauteur_cadre_observation=$hauteur_cadre_note_global-$hauteur_cachet;
				} else { $hauteur_cadre_observation=$hauteur_cadre_note_global; }
				$pdf->Rect($X_cadre_note+$largeur_utilise, $Y_cadre_note+$hauteur_utilise, $largeur_restant, $hauteur_cadre_observation, 'D');
				$pdf->SetXY($X_cadre_note+$largeur_utilise, $Y_cadre_note+$hauteur_utilise);
				$pdf->SetFont('DejaVu','',11);
				$pdf->Cell($largeur_restant,7, $texte_observation,0,1,'C');
			}
			//=======================

		// BLOC SIGNATURE
			if($affiche_cachet_pp==='1' or $affiche_signature_parent==='1')
			{
			$nb_col_sign = 0;
			if($affiche_cachet_pp==='1') { $nb_col_sign=$nb_col_sign+1; }
			if($affiche_signature_parent==='1') { $nb_col_sign=$nb_col_sign+1; }
			$largeur_utilise=$largeur_cadre_matiere+$largeur_cadre_note;

			$X_signature = $X_cadre_note+$largeur_utilise;
			$Y_signature = $Y_cadre_note+$hauteur_cadre_observation+$hauteur_du_titre;
			$hauteur_cadre_signature=$hauteur_cadre_note_global-$hauteur_cadre_observation;
			$largeur_cadre_signature=$largeur_cadre_note_global-$largeur_utilise;

			$pdf->SetFont('DejaVu','',8);
			$pdf->Rect($X_signature, $Y_signature, $largeur_cadre_signature, $hauteur_cadre_signature, 'D');

			if($affiche_cachet_pp==='1')
			{
				$pdf->SetXY($X_signature, $Y_signature);
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,4, 'Signature','LTR',2,'C');
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,4, 'professeur principal','LR',2,'C');
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,$hauteur_cachet-8, '','LR',2,'C');
				$X_signature = $X_signature+($largeur_restant/$nb_col_sign);
			}
			if($affiche_signature_parent==='1')
			{
				$pdf->SetXY($X_signature, $Y_signature);
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,4, 'Signatures','LTR',2,'C');
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,4, 'des parents','LR',2,'C');
				$pdf->Cell($largeur_cadre_signature/$nb_col_sign,$hauteur_cachet-8, '','LR',2,'C');
			}
			}
			}

		//PUB ;)
			$pdf->SetXY($X_cadre_note, $Y_cadre_note+$hauteur_cadre_note_global+$hauteur_du_titre);
			$pdf->SetFont('DejaVu','',8);
			$pdf->Cell(200,5,'GEPI - Solution libre de Gestion des élèves par Internet',0,1,'');

		$passage_i=$passage_i+1;
		$nb_eleves_i = $nb_eleves_i + 1;
		}

  // on prépare la 2ème boucle pour faire R1 et R2 != R1 si nécessaire
  if ($nb_eleves_i > $nb_eleves) { // dans ce cas on a fait la première boucle, on prépare la 2éme pour les R2 != à R1
    $nb_boucle++;
    $responsable_place = 1;
	$nb_eleves_i = 1;
  }

}

// vider les variables de session
//    unset($_SESSION["classe"]);
//    unset($_SESSION["eleve"]);
//    unset($_SESSION["type"]);
//    unset($_SESSION["date_debut"]);
//    unset($_SESSION["date_fin"]);
//    unset($_SESSION["date_debut_aff"]);
//    unset($_SESSION["date_fin_aff"]);
//    unset($_SESSION["avec_nom_devoir"]);

// sortie PDF sur écran
$nom_releve=date("Ymd_Hi");
$nom_releve = 'Releve_'.$nom_releve.'.pdf';
$pdf->Output($nom_releve, $pref_output_mode_pdf);
?>
