<?php

/**
 * Classe de gestion de l'impression PDF
 *
 * $Id$
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Christian Chapel
 * @package General
 * @subpackage Impression
 */

/* This file is part of GEPI.
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

/**
 * @package General
 * @subpackage Impression
 */
class bul_PDF extends FPDF_MULTICELLTAG {

	/**
	* Draws text within a box defined by width = w, height = h, and aligns
	* the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
	* Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
	* drawTextBox uses drawRows
	*
	* This function is provided by TUFaT.com
     * @param type $strText
     * @param type $w
     * @param type $h
     * @param type $align
     * @param type $valign
     * @param type $border 
	*/
	function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=1) {
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
    
/**
 *
 * @param type $w
 * @param type $h
 * @param type $txt
 * @param string $border
 * @param type $align
 * @param type $fill
 * @param type $maxline
 * @param type $prn
 * @return int 
 */
	function drawRows($w,$h,$txt,$border=0,$align='J',$fill=0,$maxline=0,$prn=0) {

		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=mb_strlen($s);
		if($nb>0 and mb_substr($s, $nb-1, 1)=="\n")
			$nb--;
		$b=0;
		if($border){
			if($border==1)
			{
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			}else{
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
		while($i<$nb){

			//Get next character
			$c=mb_substr($s, $i, 1);
			if($c=="\n"){
				//Explicit line break
				if($this->ws>0)
				{
					$this->ws=0;
					if ($prn==1)
						$this->_out('0 Tw');
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
			$l+=$this->GetStringWidth($c)*1000/($this->FontSize);
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
				}else{
					if($align=='J')
					{
						$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						if ($prn==1) $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
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
    
/**
 *
 * @param type $x
 * @param type $y
 * @param type $txt
 * @param type $direction 
 */
	function TextWithDirection($x,$y,$txt,$direction='R'){

		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
                // Output a string
                if ($this->unifontSubset)
                {
                        $txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
                        foreach($this->UTF8StringToArray($txt) as $uni)
                                $this->CurrentFont['subset'][$uni] = $uni;
                }
                else
                        $txt2 = '('.$this->_escape($txt).')';

		if ($direction=='R')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm %s Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt2);
		elseif ($direction=='L')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm %s Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt2);
		elseif ($direction=='U')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm %s Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt2);
		elseif ($direction=='D')
			$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm %s Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt2);
		else
			$s=sprintf('BT %.2f %.2f Td %s Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}

    /**
     *
     * @param type $x
     * @param type $y
     * @param type $txt
     * @param type $txt_angle
     * @param type $font_angle 
     */
	function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0){

		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

		$font_angle+=90+$txt_angle;
		$txt_angle*=M_PI/180;
		$font_angle*=M_PI/180;

		$txt_dx=cos($txt_angle);
		$txt_dy=sin($txt_angle);
		$font_dx=cos($font_angle);
		$font_dy=sin($font_angle);

                // Output a string
                if ($this->unifontSubset)
                {
                        $txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
                        foreach($this->UTF8StringToArray($txt) as $uni)
                                $this->CurrentFont['subset'][$uni] = $uni;
                }
                else
                        $txt2 = '('.$this->_escape($txt).')';

		$s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm %s Tj ET',
			$txt_dx,$txt_dy,$font_dx,$font_dy,
			$x*$this->k,($this->h-$y)*$this->k,$txt2);
		if ($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		$this->_out($s);
	}

/**
 * Graphique de niveau
 * @param type $X_placement
 * @param type $Y_placement
 * @param type $L_diagramme
 * @param type $H_diagramme
 * @param type $data
 * @param type $place 
 */
	function DiagBarre($X_placement, $Y_placement, $L_diagramme, $H_diagramme, $data, $place)
	{
		$this->SetFont('DejaVu', '', 10);
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
		} else {
			$espace_entre = $H_diagramme_affiche;
		}

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
			if($place===$i) {
				$this->SetFillColor(5);
			} else {
				$this->SetFillColor(240);
			}
			$this->SetDrawColor(0, 0, 0);
			if ( $total_des_valeur != '0' and $total_des_valeur != '' ) {
				$H_barre = ($H_diagramme_affiche*$val)/$total_des_valeur;
			} else {
				$H_barre = ($H_diagramme_affiche*$val);
			}
			$Y_barre = ($Y_placement_diagramme+$H_diagramme_affiche) - $H_barre;
			$X_barre = $X_placement_diagramme+($L_barre*$i);
			$this->Rect($X_barre, $Y_barre, $L_barre, $H_barre, 'DF');
			$i++;
		}
	}
 

/**
 * En-tête du document
 */
	function Header(){

	}

/**
 * Pied de page du document
 */
	function Footer() {
		// On utilise la classe bul_PDF pour les bulletins et pour les relevés de notes et la formule de bas de page ne doit pas nécessairement être la même.
		// Traitement du footer déplacé dans les pages concernées.
	}

	//========================================================================

	// http://www.fpdf.org/en/script/script6.php

	// Sets line style
	// Parameters:
	// - style: Line style. Array with keys among the following:
	//   . width: Width of the line in user units
	//   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
	//   . join: miter, round or bevel
	//   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
	//           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
	//                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
	//   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
	//   . color: Draw color. Array with components (red, green, blue)
	function SetLineStyle($style) {
		extract($style);
		if (isset($width)) {
			$width_prev = $this->LineWidth;
			$this->SetLineWidth($width);
			$this->LineWidth = $width_prev;
		}
		if (isset($cap)) {
			$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
			if (isset($ca[$cap]))
				$this->_out($ca[$cap] . ' J');
		}
		if (isset($join)) {
			$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
			if (isset($ja[$join]))
				$this->_out($ja[$join] . ' j');
		}
		if (isset($dash)) {
			$dash_string = '';
			if ($dash) {
				$tab = explode(',', $dash);
				$dash_string = '';
				foreach ($tab as $i => $v) {
					if ($i > 0)
						$dash_string .= ' ';
					$dash_string .= sprintf('%.2F', $v);
				}
			}
			if (!isset($phase) || !$dash)
				$phase = 0;
			$this->_out(sprintf('[%s] %.2F d', $dash_string, $phase));
		}
		if (isset($color)) {
			list($r, $g, $b) = $color;
			$this->SetDrawColor($r, $g, $b);
		}
	}

	// Draws a line
	// Parameters:
	// - x1, y1: Start point
	// - x2, y2: End point
	// - style: Line style. Array like for SetLineStyle
	function Line($x1, $y1, $x2, $y2, $style = null) {
		if ($style)
			$this->SetLineStyle($style);
		parent::Line($x1, $y1, $x2, $y2);
	}

	// Draws a rectangle
	// Parameters:
	// - x, y: Top left corner
	// - w, h: Width and height
	// - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
	// - border_style: Border style of rectangle. Array with some of this index
	//   . all: Line style of all borders. Array like for SetLineStyle
	//   . L: Line style of left border. null (no border) or array like for SetLineStyle
	//   . T: Line style of top border. null (no border) or array like for SetLineStyle
	//   . R: Line style of right border. null (no border) or array like for SetLineStyle
	//   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$border_style = null;
				parent::Rect($x, $y, $w, $h, $style);
				break;
			case 'DF': case 'FD':
				if (!$border_style || isset($border_style['all'])) {
					if (isset($border_style['all'])) {
						$this->SetLineStyle($border_style['all']);
						$border_style = null;
					}
				} else
					$style = 'F';
				parent::Rect($x, $y, $w, $h, $style);
				break;
			default:
				if (!$border_style || isset($border_style['all'])) {
					if (isset($border_style['all']) && $border_style['all']) {
						$this->SetLineStyle($border_style['all']);
						$border_style = null;
					}
					parent::Rect($x, $y, $w, $h, $style);
				}
				break;
		}
		if ($border_style) {
			if (isset($border_style['L']) && $border_style['L'])
				$this->Line($x, $y, $x, $y + $h, $border_style['L']);
			if (isset($border_style['T']) && $border_style['T'])
				$this->Line($x, $y, $x + $w, $y, $border_style['T']);
			if (isset($border_style['R']) && $border_style['R'])
				$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
			if (isset($border_style['B']) && $border_style['B'])
				$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
		}
	}

	// Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
	// Parameters:
	// - x0, y0: Start point
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	// - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
	// - line_style: Line style for curve. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$op = 'f';
				$line_style = null;
				break;
			case 'FD': case 'DF':
				$op = 'B';
				break;
			default:
				$op = 'S';
				break;
		}
		if ($line_style)
			$this->SetLineStyle($line_style);

		$this->_Point($x0, $y0);
		$this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
		$this->_out($op);
	}

	// Draws an ellipse
	// Parameters:
	// - x0, y0: Center point
	// - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
	// - angle: Orientation angle (anti-clockwise)
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
	// - line_style: Line style for ellipse. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg Bézier curves
	function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
		if ($rx) {
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
					$op = 'f';
					$line_style = null;
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				case 'C':
					$op = 's'; // small 's' means closing the path as well
					break;
				default:
					$op = 'S';
					break;
			}
			if ($line_style)
				$this->SetLineStyle($line_style);
			if (!$ry)
				$ry = $rx;
			$rx *= $this->k;
			$ry *= $this->k;
			if ($nSeg < 2)
				$nSeg = 2;

			$astart = deg2rad((float) $astart);
			$afinish = deg2rad((float) $afinish);
			$totalAngle = $afinish - $astart;

			$dt = $totalAngle/$nSeg;
			$dtm = $dt/3;

			$x0 *= $this->k;
			$y0 = ($this->h - $y0) * $this->k;
			if ($angle != 0) {
				$a = -deg2rad((float) $angle);
				$this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F %.2F cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
				$x0 = 0;
				$y0 = 0;
			}

			$t1 = $astart;
			$a0 = $x0 + ($rx * cos($t1));
			$b0 = $y0 + ($ry * sin($t1));
			$c0 = -$rx * sin($t1);
			$d0 = $ry * cos($t1);
			$this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
			for ($i = 1; $i <= $nSeg; $i++) {
				// Draw this bit of the total curve
				$t1 = ($i * $dt) + $astart;
				$a1 = $x0 + ($rx * cos($t1));
				$b1 = $y0 + ($ry * sin($t1));
				$c1 = -$rx * sin($t1);
				$d1 = $ry * cos($t1);
				$this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
							$this->h - (($b0 + ($d0 * $dtm)) / $this->k),
							($a1 - ($c1 * $dtm)) / $this->k,
							$this->h - (($b1 - ($d1 * $dtm)) / $this->k),
							$a1 / $this->k,
							$this->h - ($b1 / $this->k));
				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}
			$this->_out($op);
			if ($angle !=0)
				$this->_out('Q');
		}
	}

	// Draws a circle
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius
	// - astart: Start angle
	// - afinish: Finish angle
	// - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
	// - line_style: Line style for circle. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - nSeg: Ellipse is made up of nSeg Bézier curves
	function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
		$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
	}

	// Draws a polygon
	// Parameters:
	// - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
		$np = count($p) / 2;
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$line_style = null;
				$op = 'f';
				break;
			case 'FD': case 'DF':
				$op = 'B';
				break;
			default:
				$op = 'S';
				break;
		}
		$draw = true;
		if ($line_style)
			if (isset($line_style['all']))
				$this->SetLineStyle($line_style['all']);
			else { // 0 .. (np - 1), op = {B, S}
				$draw = false;
				if ('B' == $op) {
					$op = 'f';
					$this->_Point($p[0], $p[1]);
					for ($i = 2; $i < ($np * 2); $i = $i + 2)
						$this->_Line($p[$i], $p[$i + 1]);
					$this->_Line($p[0], $p[1]);
					$this->_out($op);
				}
				$p[$np * 2] = $p[0];
				$p[($np * 2) + 1] = $p[1];
				for ($i = 0; $i < $np; $i++)
					if (!empty($line_style[$i]))
						$this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
			}

		if ($draw) {
			$this->_Point($p[0], $p[1]);
			for ($i = 2; $i < ($np * 2); $i = $i + 2)
				$this->_Line($p[$i], $p[$i + 1]);
			$this->_Line($p[0], $p[1]);
			$this->_out($op);
		}
	}

	// Draws a regular polygon
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius of circumscribed circle
	// - ns: Number of sides
	// - angle: Orientation angle (anti-clockwise)
	// - circle: Draw circumscribed circle or not
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
		if ($ns < 3)
			$ns = 3;
		if ($circle)
			$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
		$p = null;
		for ($i = 0; $i < $ns; $i++) {
			$a = $angle + ($i * 360 / $ns);
			$a_rad = deg2rad((float) $a);
			$p[] = $x0 + ($r * sin($a_rad));
			$p[] = $y0 + ($r * cos($a_rad));
		}
		$this->Polygon($p, $style, $line_style, $fill_color);
	}

	// Draws a star polygon
	// Parameters:
	// - x0, y0: Center point
	// - r: Radius of circumscribed circle
	// - nv: Number of vertices
	// - ng: Number of gaps (ng % nv = 1 => regular polygon)
	// - angle: Orientation angle (anti-clockwise)
	// - circle: Draw circumscribed circle or not
	// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	// - line_style: Line style. Array with one of this index
	//   . all: Line style of all lines. Array like for SetLineStyle
	//   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
		if ($nv < 2)
			$nv = 2;
		if ($circle)
			$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
		$p2 = null;
		$visited = null;
		for ($i = 0; $i < $nv; $i++) {
			$a = $angle + ($i * 360 / $nv);
			$a_rad = deg2rad((float) $a);
			$p2[] = $x0 + ($r * sin($a_rad));
			$p2[] = $y0 + ($r * cos($a_rad));
			$visited[] = false;
		}
		$p = null;
		$i = 0;
		do {
			$p[] = $p2[$i * 2];
			$p[] = $p2[($i * 2) + 1];
			$visited[$i] = true;
			$i += $ng;
			$i %= $nv;
		} while (!$visited[$i]);
		$this->Polygon($p, $style, $line_style, $fill_color);
	}

	// Draws a rounded rectangle
	// Parameters:
	// - x, y: Top left corner
	// - w, h: Width and height
	// - r: Radius of the rounded corners
	// - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
	// - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
	// - border_style: Border style of rectangle. Array like for SetLineStyle
	// - fill_color: Fill color. Array with components (red, green, blue)
	function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
		if ('0000' == $round_corner) // Not rounded
			$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
		else { // Rounded
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($red, $g, $b) = $fill_color;
				$this->SetFillColor($red, $g, $b);
			}
			switch ($style) {
				case 'F':
					$border_style = null;
					$op = 'f';
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				default:
					$op = 'S';
					break;
			}
			if ($border_style)
				$this->SetLineStyle($border_style);

			$MyArc = 4 / 3 * (sqrt(2) - 1);

			$this->_Point($x + $r, $y);
			$xc = $x + $w - $r;
			$yc = $y + $r;
			$this->_Line($xc, $y);
			if ($round_corner[0])
				$this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
			else
				$this->_Line($x + $w, $y);

			$xc = $x + $w - $r ;
			$yc = $y + $h - $r;
			$this->_Line($x + $w, $yc);

			if ($round_corner[1])
				$this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
			else
				$this->_Line($x + $w, $y + $h);

			$xc = $x + $r;
			$yc = $y + $h - $r;
			$this->_Line($xc, $y + $h);
			if ($round_corner[2])
				$this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
			else
				$this->_Line($x, $y + $h);

			$xc = $x + $r;
			$yc = $y + $r;
			$this->_Line($x, $yc);
			if ($round_corner[3])
				$this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
			else {
				$this->_Line($x, $y);
				$this->_Line($x + $r, $y);
			}
			$this->_out($op);
		}
	}

	/* PRIVATE METHODS */

	// Sets a draw point
	// Parameters:
	// - x, y: Point
	function _Point($x, $y) {
		$this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
	}

	// Draws a line from last draw point
	// Parameters:
	// - x, y: End point
	function _Line($x, $y) {
		$this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
	}

	// Draws a Bézier curve from last draw point
	// Parameters:
	// - x1, y1: Control point 1
	// - x2, y2: Control point 2
	// - x3, y3: End point
	function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	}

}
