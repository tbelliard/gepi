<?php
/*
 * Last modification  : 10/11/2006
 *
 * Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

require('../fpdf/fpdf.php');
require('../fpdf/ex_fpdf.php');

define('FPDF_FONTPATH','../fpdf/font/');
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

// variable de session
  //nombre d'élève par page
  $nb_releve_par_page = $_SESSION['type'];
  $avec_adresse_responsable = $_SESSION['avec_adresse_responsable'];
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

 function unhtmlentities($chaineHtml) {
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

}
// fin de la class

// variable de la création du document
	
	// entête
	$X_entete_etab='5';
	$caractere_utilse='arial'; // caractère utilisé dans le document
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
	$affiche_signature_parent='1'; // affiche le cadre signatures des parents
	$affiche_cachet_pp='0'; // affiche la signature du professeur principal


//recherche d'information de la sélection
	$cpt_i='1';
	//requête des classes sélectionné
	if (isset($id_classe[0])) {
 	$o=0; $prepa_requete = "";
        while(!empty($id_classe[$o]))
	     { 
		if($o == "0") { $prepa_requete = $prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'"'; }
		if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'j_eleves_classes.id_classe = "'.$id_classe[$o].'" '; }
		$o = $o + 1;
             }
	}
	//requête des élèves sélectionné
	if (!empty($id_eleve[0])) {
 	$o=0; $prepa_requete = "";
        while(!empty($id_eleve[$o]))
	     { 
		if($o == "0") { $prepa_requete = $prefix_base.'eleves.login = "'.$id_eleve[$o].'"'; }
		if($o != "0") { $prepa_requete = $prepa_requete.' OR '.$prefix_base.'eleves.login = "'.$id_eleve[$o].'" '; }
		$o = $o + 1;
             }
	}

	//tableau des données élève
		if (isset($id_classe[0])) { $call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'); }
		if (isset($id_eleve[0])) { $call_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id AND ('.$prepa_requete.') AND '.$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'); }
		//on compte les élèves sélectionné
		    $nb_eleves = mysql_num_rows($call_eleve);
		    while ( $donner = mysql_fetch_array( $call_eleve ))
			{
				$login[$cpt_i] = $donner['login']; 
				$ele_id_eleve[$cpt_i] = $donner['ele_id']; 
				$nom[$cpt_i] = $donner['nom'];
				$prenom[$cpt_i] = $donner['prenom'];
				$naissance[$cpt_i] = $donner['naissance'];
				$classe[$cpt_i] = $donner['nom_complet'];
				$classe_id_eleve[$cpt_i] = $donner['id'];
				$ident_eleve_sel1=$login[$cpt_i];

				//les responsables
				$nombre_de_responsable = 0;
				$nombre_de_responsable =  mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id )"),0);
				if($nombre_de_responsable != 0)
				{
					$cpt_parents = 0;
					$requete_parents = mysql_query("SELECT * FROM ".$prefix_base."resp_pers rp, ".$prefix_base."resp_adr ra, ".$prefix_base."responsables2 r WHERE ( r.ele_id = '".$ele_id_eleve[$cpt_i]."' AND r.pers_id = rp.pers_id AND rp.adr_id = ra.adr_id ) ORDER BY resp_legal ASC");
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
			$systeme_de_classement=''.$prefix_base.'matieres.nom_complet ASC';
			if($active_regroupement_cote==='1' or $active_entete_regroupement==='1') { $systeme_de_classement = ' '.$prefix_base.'j_matieres_categories_classes.priority ASC, '.$prefix_base.'j_groupes_classes.priorite ASC, '.$prefix_base.'matieres_categories.id ASC,'.$systeme_de_classement; }
			if($active_regroupement_cote!='1' and $active_entete_regroupement!='1') { $systeme_de_classement = ' '.$prefix_base.'j_groupes_classes.priorite ASC, '.$systeme_de_classement; }
		// requête dans la base
		$base_complete_information = mysql_query('SELECT * FROM '.$prefix_base.'cn_devoirs, '.$prefix_base.'cn_notes_devoirs, '.$prefix_base.'cn_cahier_notes, '.$prefix_base.'groupes, '.$prefix_base.'j_groupes_matieres, '.$prefix_base.'j_groupes_classes, '.$prefix_base.'matieres, '.$prefix_base.'.j_matieres_categories_classes, '.$prefix_base.'matieres_categories WHERE '.$prefix_base.'j_groupes_classes.id_groupe='.$prefix_base.'groupes.id AND '.$prefix_base.'j_groupes_classes.id_classe="'.$classe_id_eleve[$nb_eleves_i].'" AND '.$prefix_base.'.j_matieres_categories_classes.classe_id='.$prefix_base.'j_groupes_classes.id_classe AND '.$prefix_base.'.j_matieres_categories_classes.categorie_id='.$prefix_base.'matieres_categories.id AND '.$prefix_base.'matieres.categorie_id='.$prefix_base.'.j_matieres_categories_classes.categorie_id AND '.$prefix_base.'j_groupes_matieres.id_groupe='.$prefix_base.'groupes.id AND '.$prefix_base.'j_groupes_matieres.id_matiere='.$prefix_base.'matieres.matiere AND '.$prefix_base.'cn_notes_devoirs.login = "'.$login[$nb_eleves_i].'" AND ('.$prefix_base.'cn_devoirs.date >=  "'.$date_debut.'" AND '.$prefix_base.'cn_devoirs.date <= "'.$date_fin.'" ) AND '.$prefix_base.'cn_notes_devoirs.id_devoir = '.$prefix_base.'cn_devoirs.id AND '.$prefix_base.'cn_devoirs.id_racine = '.$prefix_base.'cn_cahier_notes.id_cahier_notes AND '.$prefix_base.'cn_cahier_notes.id_groupe = '.$prefix_base.'groupes.id AND '.$prefix_base.'cn_devoirs.display_parents = "1" ORDER BY '.$systeme_de_classement.', '.$prefix_base.'matieres.nom_complet ASC , '.$prefix_base.'groupes.id ASC, '.$prefix_base.'cn_devoirs.date ASC');

		// répartition des informations pour un relevé
		$id_groupe_avant = ""; $nb_matiere_cpt='1';
		$eleve_select=$login[$nb_eleves_i];
		$login_passe='';
		$regroupement_passer='';
		while($donne_requete = mysql_fetch_array($base_complete_information))
		 { 
		   if($donne_requete['login']!=$login_passe) { $nb_matiere_cpt='1'; }
		   //on vérifi si c'est le même id de groupe pour mettre toutes les notes d'un groupe en même temps puis compter le nombre de groupe
		   if($donne_requete['id_groupe']!=$id_groupe_avant)
		    { 
			$id_groupe_selectionne=$donne_requete['id_groupe'];
			$id_classe=$donne_requete['id_classe'];
			$groupe_select[$eleve_select][$nb_matiere_cpt]=$donne_requete['id_groupe'];
			$name[$eleve_select][$nb_matiere_cpt] = $donne_requete[30]; //$donner_toute_matier['name']
			if(isset($_SESSION['avec_nom_devoir']) and $_SESSION['avec_nom_devoir'] === 'oui') { $nom_devoir_oui = " (".$donne_requete[3].")"; } else { $nom_devoir_oui=''; }
		        $notes[$eleve_select][$nb_matiere_cpt] = $donne_requete['note']."".$nom_devoir_oui;
			$nom_regroupement[$eleve_select][$nb_matiere_cpt]=$donne_requete['nom_complet'];
			if($nom_regroupement[$eleve_select][$nb_matiere_cpt]!=$regroupement_passer)
			 {
			  if(empty($nb_regroupement[$eleve_select])) { $nb_regroupement[$eleve_select] = 0; }
			  $nb_regroupement[$eleve_select]=$nb_regroupement[$eleve_select]+1;
			 }
			$regroupement_passer=$nom_regroupement[$eleve_select][$nb_matiere_cpt];

			// autre requete pour rechercher les professeur responsable de la matière sélectionné
			if(empty($prof_groupe[$id_groupe_selectionne][0])) {
			$call_profs = mysql_query('SELECT u.login FROM '.$prefix_base.'utilisateurs u, '.$prefix_base.'j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe="'.$id_groupe_selectionne.'") ORDER BY j.ordre_prof');
			$nombre_profs = mysql_num_rows($call_profs);
			$k = 0;
			 while ($k < $nombre_profs) {
			        $current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
			        $prof_groupe[$id_groupe_selectionne][$k]=affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe);
			        $k++;
			 }
			}

			$nb_matiere[$eleve_select]=$nb_matiere_cpt;
			$nb_num_matiere_passe=$nb_matiere_cpt;
			$nb_matiere_cpt = $nb_matiere_cpt + 1;
		    } else {
			     if($_SESSION['avec_nom_devoir'] == 'oui') { $nom_devoir_oui = " (".$donne_requete[3].")"; }
			     $notes[$eleve_select][$nb_num_matiere_passe] = $notes[$eleve_select][$nb_num_matiere_passe]." - ".$donne_requete['note']."".$nom_devoir_oui;
			   }
			   $id_groupe_avant = $donne_requete['id_groupe'];
			   $login_passe=$donne_requete['login'];
	 	 }
	$nb_eleves_i=$nb_eleves_i+1;
	}


// Définition de la page
$pdf=new rel_PDF("P","mm","A4");
$pdf->SetTopMargin(TopMargin);
$pdf->SetRightMargin(RightMargin);
$pdf->SetLeftMargin(LeftMargin);
$pdf->SetAutoPageBreak(true, BottomMargin);

// Couleur des traits
$pdf->SetDrawColor(0,0,0);

// Caractéres utilisée
//$pdf->AddFont('Alakob','','Alakob.php');
//$pdf->AddFont('cursif','','cursif.php');
//$pdf->AddFont('bvrondno','','bvrondno.php');
$caractere_utilse = 'arial';

// on appelle une nouvelle page pdf
$responsable_place = 0;
$nb_eleves_i = 1;
while($nb_eleves_i <= $nb_eleves)
{
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

	//BLOC IDENTITEE ELEVE
		 $pdf->SetXY($X_cadre_eleve,$Y_cadre_eleve);
		 $pdf->SetFont($caractere_utilse,'B',14);
		 $pdf->Cell(90,7,strtoupper($nom[$nb_eleves_i])." ".ucfirst($prenom[$nb_eleves_i]),0,2,''); 
		 $pdf->SetFont($caractere_utilse,'',10);
		 $pdf->Cell(90,5,'Né le '.affiche_date_naissance($naissance[$nb_eleves_i]).', demi-pensionnaire',0,2,''); 
		 $pdf->Cell(90,5,'',0,2,''); 
		 $classe_aff = $pdf->WriteHTML('Classe de <B>'.unhtmlentities($classe[$nb_eleves_i]).'<B>');
		 $pdf->Cell(90,5,$classe_aff,0,2,'');
		 $pdf->SetX($X_cadre_eleve);
		 $pdf->SetFont($caractere_utilse,'',10);
		 $pdf->Cell(90,5,'Année scolaire '.$annee_scolaire,0,2,'');  

	// BOLC IDENTITE DE L'ETABLISSEMENT
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
	 	 $pdf->SetFont($caractere_utilse,'',14);
		  $gepiSchoolName = getSettingValue('gepiSchoolName');
		 $pdf->Cell(90,7, $gepiSchoolName,0,2,''); 
		 $pdf->SetFont($caractere_utilse,'',10);
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
		 $texte_1_responsable = $civilite_parents[$ident_eleve_aff][$responsable_place]." ".$nom_parents[$ident_eleve_aff][$responsable_place]." ".$prenom_parents[$ident_eleve_aff][$responsable_place];
			$hauteur_caractere=12;
			$pdf->SetFont($caractere_utilse,'B',$hauteur_caractere);		
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = 90;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
			 if($taille_texte<$val) 
			  {
			     $hauteur_caractere = $hauteur_caractere-0.3;
			     $pdf->SetFont($caractere_utilse,'B',$hauteur_caractere);
			     $val = $pdf->GetStringWidth($texte_1_responsable);
			  } else { $grandeur_texte='ok'; }
        		}
		 $pdf->Cell(90,7, $texte_1_responsable,0,2,''); 
		 $pdf->SetFont($caractere_utilse,'',10);
		 $texte_1_responsable = $adresse1_parents[$ident_eleve_aff][$responsable_place];
			$hauteur_caractere=10;
			$pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = 90;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
			 if($taille_texte<$val) 
			  {
			     $hauteur_caractere = $hauteur_caractere-0.3;
			     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			     $val = $pdf->GetStringWidth($texte_1_responsable);
			  } else { $grandeur_texte='ok'; }
        		}
		 $pdf->Cell(90,5, $texte_1_responsable,0,2,'');
		 $texte_1_responsable = $adresse2_parents[$ident_eleve_aff][$responsable_place];
			$hauteur_caractere=10;
			$pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = 90;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
			 if($taille_texte<$val) 
			  {
			     $hauteur_caractere = $hauteur_caractere-0.3;
			     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			     $val = $pdf->GetStringWidth($texte_1_responsable);
			  } else { $grandeur_texte='ok'; }
        		}
		 $pdf->Cell(90,5, $texte_1_responsable,0,2,''); 
		 $pdf->Cell(90,5, '',0,2,''); 
		 $texte_1_responsable = $cp_parents[$ident_eleve_aff][$responsable_place]." ".$ville_parents[$ident_eleve_aff][$responsable_place];
			$hauteur_caractere=10;
			$pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte_1_responsable);
			$taille_texte = 90;
			$grandeur_texte='test';
			while($grandeur_texte!='ok') {
			 if($taille_texte<$val) 
			  {
			     $hauteur_caractere = $hauteur_caractere-0.3;
			     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere);
			     $val = $pdf->GetStringWidth($texte_1_responsable);
			  } else { $grandeur_texte='ok'; }
        		}
		 $pdf->Cell(90,5, $texte_1_responsable,0,2,'');
		}

	// BLOC NOTATION ET OBSERVATION
		//Titre du tableau
		 $pdf->SetXY($X_cadre_note,$Y_cadre_note);
		 $pdf->SetFont($caractere_utilse,'B',12);
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
					$pdf->SetFont($caractere_utilse,'',8);
					$pdf->Cell($largeur_cadre_matiere, $hauteur_dun_regroupement, unhtmlentities($nom_regroupement[$eleve_select][$cpt_i]), 'LTB', 2, '');
					$hauteur_utilise=$hauteur_utilise+$hauteur_dun_regroupement;
					$nom_regroupement_passer=$nom_regroupement[$eleve_select][$cpt_i];
					$pdf->SetXY($X_cadre_note,$Y_cadre_note+$hauteur_utilise);
				 }
			$pdf->SetFont($caractere_utilse,'B','9');
			 $nom_matiere = $name[$eleve_select][$cpt_i];
				$hauteur_caractere = 9;
				$pdf->SetFont($caractere_utilse,'B',$hauteur_caractere);
				$val = $pdf->GetStringWidth($nom_matiere);
				$taille_texte = $largeur_cadre_matiere;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				 if($taille_texte<$val) 
				  {
				     $hauteur_caractere = $hauteur_caractere-0.3;
				     $pdf->SetFont($caractere_utilse,'B',$hauteur_caractere);
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
				$pdf->SetFont($caractere_utilse,'',$hauteur_caractere_prof);
				$val = $pdf->GetStringWidth($text_prof);
				$taille_texte = ($largeur_cadre_matiere-0.6);
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				 if($taille_texte<$val)
				  {
				     $hauteur_caractere_prof = $hauteur_caractere_prof-0.3;
				     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere_prof);
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
				$pdf->SetFont($caractere_utilse,'I',$hauteur_caractere);
				$val = $pdf->GetStringWidth($nom_prof);
				$taille_texte = $largeur_cadre_matiere;
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				 if($taille_texte<$val) 
				  {
				     $hauteur_caractere = $hauteur_caractere-0.3;
				     $pdf->SetFont($caractere_utilse,'I',$hauteur_caractere);
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
			$pdf->SetXY($X_cadre_note+$largeur_utilise,$Y_cadre_note+$hauteur_utilise);
				// on affiche les nom des regroupement
				if($nom_regroupement[$eleve_select][$cpt_i]!=$nom_regroupement_passer and $active_entete_regroupement === '1')
				 {
					$pdf->SetFont($caractere_utilse,'',8);
					$pdf->Cell($largeur_cadre_note, $hauteur_dun_regroupement, '', 'RTB', 2, '');
					$hauteur_utilise=$hauteur_utilise+$hauteur_dun_regroupement;
					$nom_regroupement_passer=$nom_regroupement[$eleve_select][$cpt_i];
					$pdf->SetXY($X_cadre_note+$largeur_utilise,$Y_cadre_note+$hauteur_utilise);
				 }
			// détermine la taille de la police de caractère
			// on peut allez jusqu'a 275mm de caractère dans trois cases de notes
				$hauteur_caractere_notes=9;
				$pdf->SetFont($caractere_utilse,'',$hauteur_caractere_notes);		
				$val = $pdf->GetStringWidth($notes[$eleve_select][$cpt_i]);
				$taille_texte = (($hauteur_cadre_matiere/4)*$largeur_cadre_note);
				$grandeur_texte='test';
				while($grandeur_texte!='ok') {
				 if($taille_texte<$val) 
				  {
				     $hauteur_caractere_notes = $hauteur_caractere_notes-0.3;
				     $pdf->SetFont($caractere_utilse,'',$hauteur_caractere_notes);
				     $val = $pdf->GetStringWidth($notes[$eleve_select][$cpt_i]);
				  } else { $grandeur_texte='ok'; }
                		}
			$pdf->drawTextBox($notes[$eleve_select][$cpt_i], $largeur_cadre_note, $hauteur_cadre_matiere, 'J', 'M', 1);
			$hauteur_utilise=$hauteur_utilise+$hauteur_cadre_matiere;

		 $cpt_i=$cpt_i+1;
		 }

	// BLOC OBSERVATION
		$largeur_utilise=$largeur_cadre_matiere+$largeur_cadre_note;
		$largeur_restant=$largeur_cadre_note_global-$largeur_utilise;
		$hauteur_utilise = $hauteur_du_titre;
		if($affiche_cachet_pp==='1' or $affiche_signature_parent==='1')
		 {
			$hauteur_cadre_observation=$hauteur_cadre_note_global-$hauteur_cachet;
		 } else { $hauteur_cadre_observation=$hauteur_cadre_note_global; }
		$pdf->Rect($X_cadre_note+$largeur_utilise, $Y_cadre_note+$hauteur_utilise, $largeur_restant, $hauteur_cadre_observation, 'D');
		$pdf->SetXY($X_cadre_note+$largeur_utilise, $Y_cadre_note+$hauteur_utilise);
		$pdf->SetFont($caractere_utilse,'',11);
		$pdf->Cell($largeur_restant,7, $texte_observation,0,1,'C'); 

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

		$pdf->SetFont($caractere_utilse,'',8);
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
	    $pdf->SetFont('arial','',8);
	    $pdf->Cell(200,5,'GEPI - Solution libre de Gestion des élèves par Internet',0,1,''); 

	$passage_i=$passage_i+1;
	$nb_eleves_i = $nb_eleves_i + 1;
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
$pdf->Output($nom_releve,'I');
?>
