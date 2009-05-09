<?php

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
/*
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
*/
//============================================================

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

//============================================================

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

	$avec_adresse_responsable=1;

	// bloc responsable parents
	$active_bloc_adresse_parent=$avec_adresse_responsable;
	$X_parent=110; $Y_parent=40;

	//information année
	$gepiYear = getSettingValue('gepiYear');
	$annee_scolaire = $gepiYear;
	$X_cadre_eleve = '130';

	// cadre note
	$titre_du_cadre='Relevé de notes du ';
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




	// Définition de la page
	$pdf=new rel_PDF("P","mm","A4");
	$pdf->SetTopMargin(TopMargin);
	$pdf->SetRightMargin(RightMargin);
	$pdf->SetLeftMargin(LeftMargin);
	$pdf->SetAutoPageBreak(true, BottomMargin);

	// Couleur des traits
	$pdf->SetDrawColor(0,0,0);

	// Caractéres utilisée
	$caractere_utilse = 'arial';



/*
		$nom_releve=date("Ymd_Hi");
		$nom_releve = 'Releve_'.$nom_releve.'.pdf';
		$pdf->Output($nom_releve,'I');
*/




	$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "y";
	$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "";


?>