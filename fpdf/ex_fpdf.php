<?php

if (!defined('FPDF_VERSION')) {
	require_once(dirname(__FILE__).'/fpdf.php');
}

/**
 * FPDF class extension
 * 
 * Last modification  : 04/04/2005
 *
 * @package externe
 * @subpackage FPDF                        *
*/

/**
 * @package externe
 * @subpackage FPDF
 */
class Ex_FPDF extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;

function __construct($orientation='P',$unit='mm',$format='A4')
{
    //Appel au constructeur parent
    parent::__construct($orientation,$unit,$format);
}

function Footer()
{
    //Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    //Police DejaVu italique 8
    $this->SetFont('DejaVu','I',8);
    //Numéro de page centré
    $this->Cell(0,8,'Page '.$this->PageNo(),"T",0,'C');
}

function Header()
{
    $bord = 0;
    //Police DejaVu gras 15
    //$this->Image("../images/logo.gif", 0, 0, 50, 50);
    $nom = $_SESSION['prenom'] . " " . $_SESSION['nom'];
    if ($_SESSION['statut'] != "professeur") {
      $user_statut = $_SESSION['statut'];
    } else {
      $nom_complet_matiere = sql_query1("select nom_complet from matieres where matiere = '".$_SESSION['matiere']."'");
      if ($nom_complet_matiere != '-1') {
         $user_statut = "professeur de " . $nom_complet_matiere;
      } else {
         $user_statut = "Invité";
      }
    }

    $etab_text = getSettingValue("gepiSchoolName"). " - année scolaire " . getSettingValue("gepiYear");
    $gepi_text = "GEPI - Solution libre de Gestion des élèves par Internet";

    $this->SetFont('DejaVu','',8);
    
    //Calcul de la largeur des cellules
    $l = (LargeurPage - LeftMargin - LeftMargin)/2;

    // on sauvegarde la position courante
    $x=$this->GetX();
    $y=$this->GetY();
    // on imprime du texte à gauche
    //$this->MultiCell($l, 5, $gepi_text,$bord, "L",0);
    $this->MultiCell($l, 5, $gepi_text,$bord, "L",0);
    // déplace le curseur
    $this->SetXY($x+$l,$y);
    // on imprime du texte à droite
    //$this->MultiCell($l, 5, $etab_text,$bord, "R",0);
    $this->MultiCell($l, 5, $etab_text,$bord, "R",0);

    $this->MultiCell($l, 5, $nom." - ".$user_statut, $bord, "L",0);
    // on trace un trait horizontal
    $this->cell(0,2,"","T",0);
    // Saut de ligne et retour à la marge
    $this->ln();


    //Saut de ligne

    }

    function FancyTable($w1,$header,$data,$align_header)
    {
        // $w : tableau des largeurs des colonnes
        // header : tableau des données de la première ligne
        // $date : tableau 2 dimensions des données autres que la 1ère ligne
        // $align_header : si égal v la première ligne est affichée verticalement

        //Couleurs, épaisseur du trait et police grasse de la première ligne
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('DejaVu','B');
        //En-tête

        // on calcule la hauteur des cellules de la première ligne ($max)
        for($i=0;$i<count($header);$i++) {
            $max = 0;
            $temp = explode("\n",$header[$i]);
            for($j=0;$j<count($temp);$j++) {
               $k=$this->GetStringWidth($temp[$j])+8;
               if ($k > $max) $max = $k;
            }
        }

        // Si la largeur totale des cellules dépasse la largeur de la page, alors on ajuste
        $total_largeur = 0;
        $rapport = 4;
        $wi["i"] = 30; //largeur de la première colonne
        $wi["d"] = 12; //largeur de la colonne "classe"
        $wi["n"] = 10; // largeur des colonnes "notes"
        $wi["c"] = $rapport*$wi["n"]; // largeur des colonnes "commentaires"
        $nb_unit = 0;
        $nb_col_n = 0;
        $nb_col_c = 0;

        for($i=0;$i<count($w1);$i++) {
           // on compte le nombre d'unité de colonnes
           switch($w1[$i]) {
             case "c" :
             $nb_unit += $rapport;
             $nb_col_c++;  // nombre de colonnes "c"
             break;
             case "n" :
             $nb_unit += 1;
             $nb_col_n++;   // nombre de colonnes "n"
             break;
           }
           // on calcule la largeur totale du tableau
           $total_largeur += $wi[$w1[$i]];
        }
        // largeur disponible
        $largeur = LargeurPage - LeftMargin - LeftMargin;
        if ($total_largeur > $largeur) {
            $x_n = ($largeur - $wi["i"] - $wi["d"])/($nb_unit);
            $x_c = $rapport*$x_n;
        } else {
            // si il reste de la place, on élargit au maximum les colonnes "commentaires"
            $x_n = $wi["n"];
            if ($nb_col_c != 0) {
                $x_c =  (($largeur - $wi["i"]) - $wi["d"] - $nb_col_n*$x_n)/ $nb_col_c;
            } else {
                $x_c = $rapport*$x_n;
            }
        }
        // On attribue les largeurs dans le tableau $w
        $w[0] = $wi["i"];
        if (count($w1) == 4) $w[1] = $wi["d"];
        for($i=1;$i<count($w1);$i++)
            switch($w1[$i]) {
            case "c" :
            $w[$i] = $x_c;
            break;
            case "n" :
            $w[$i] = $x_n;
            break;
            }

        // Affichage de la première ligne
        for($i=0;$i<count($header);$i++)
            if (($align_header == "v") and ($i != 0)) {
                $this->Vcell($w[$i],$max,$header[$i],1,0,'C',1);
            } else {
                $this->cell($w[$i],$max,$header[$i],1,0,'C',1);
            }
        $this->Ln();
        //Restauration des couleurs et de la police
        $this->SetFillColor(200,200,200);
        $this->SetTextColor(0);
        $this->SetFont('');
        //Données
//      $fill1=0;   // pour la méthode 1
        $fill2 = '';  //  pour la méthode 2

        // Méthode 1 : les colonnes sont de largeur variable en fonction des celules "commentaires"
        foreach($data as $row)
        {
            $nb=1;
            for($i=0;$i<count($row);$i++)
               // pour chaque ligne, on calcule la hauteur max des cellules "commentaires"
               if ($w1[$i] == 'c')
                   $nb=max($nb,$this->NbLines($w[$i],$row[$i]));
            // on fixe la hauteur de la ligne
            $h=5*$nb;
            $this->CheckPageBreak($h);
            //Dessine les cellules
            for($i=0;$i<count($row);$i++)
            {
                //Sauve la position courante
                $x=$this->GetX();
                $y=$this->GetY();
                //Dessine le cadre
                $this->Rect($x,$y,$w[$i],$h,$fill2);
                //Imprime le texte
                if (($w1[$i] == 'n') or ($w1[$i] == 'i')) {
                    // si ce n'est pas une cellule "commentaire", on condense
                    $this->Cell($w[$i],5,$row[$i],'LR',0,'L');
                } else {
                    // si c'est une cellule "commentaire", on imprime éventuellement sur plusieurs lignes
                    $this->MultiCell($w[$i],5,$row[$i],0,'L');
                }
                //Repositionne à droite
                $this->SetXY($x+$w[$i],$y);
            }
            //Va à la ligne
            $this->Ln($h);
            $fill_t = $fill2;
            if ($fill_t == '') $fill2 = 'DF';
            if ($fill_t == 'DF') $fill2 = '';
        }

        /*
        // Méthode 2 : toutes les lignes sont de largeur 5
        foreach($data as $row)
        {
            $k=0;
            foreach($row as $val) {
               // On élimine les retours à la ligne
               $val = str_replace("\n", " ", $val);
               $this->Cell($w[$k],5,$val,'LR',0,'L',$fill1);
               $k++;
            }
            $this->Ln();
            $fill1=!$fill1;
        }
        $this->Cell(array_sum($w),0,'','T');
        */
    }

    function CheckPageBreak($h)
    {
    //Si la hauteur h provoque un débordement, saut de page manuel
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
    }

    //MultiCell with bullet
    // permet d'ajouter une puce à MultiCell devant la première ligne.
    // L'utilisation est identique à MultiCell sauf qu'il y a un paramètre supplémentaire $blt pour la définition de la puce.
    function MultiCellBlt($w,$h,$blt,$txt,$border=0,$align='J',$fill=0)
    {
    	// Ajout suite au souci sur l'encodage utf8 (merci à l'académie de Guyane)
	    $txt = utf8_decode($txt);
	    
        //Get bullet width including margins
        $blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;

        //Save x
        $bak_x = $this->x;

        //Output bullet
        $this->Cell($blt_width,$h,$blt,0,'',$fill);
        //Output text
        $this->MultiCell($w-$blt_width,$h,$txt,$border,$align,$fill);

        //Restore x
        $this->x = $bak_x;
    }


    // versions étendue de la méthode Cell
    //Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
    // permet d'imprimer plusieurs lignes à l'aide du séparateur "\n".
    //Lorsque la cellule ne contient qu'une ligne et qu'elle déborde, le texte est automatiquement compressé pour tenir à l'intérieur.
    // Les paramètres spécifiques sont :
    // border : indique si une bordure doit être tracée autour de la cellule. La valeur peut être soit un nombre :
    // * 0 : pas de bordure
    // * >0 : bordure de l'épaisseur correspondante
    // ou une chaîne contenant certaines ou toutes les lettres suivantes (dans un ordre quelconque) :
    //    * L : gauche
    //    * T : haut
    //    * R : droite
    //    * B : bas
    // ou bien pour des bords en gras :
    //    * l : gauche
    //    * t : haut
    //    * r : droite
    //    * b : bas
    //    Valeur par défaut : 0.
    function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{    
    //Output a cell
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
    {
        $x=$this->x;
        $ws=$this->ws;
        if($ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation);
        $this->x=$x;
        if($ws>0)
        {
            $this->ws=$ws;
            $this->_out(sprintf('%.3f Tw',$ws*$k));
        }
    }
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $s='';
// begin change Cell function 12.08.2003
    if($fill==1 or $border>0)
    {
        if($fill==1)
            $op=($border>0) ? 'B' : 'f';
        else
            $op='S';
        if ($border>1) {
            $s=sprintf(' q %.2f w %.2f %.2f %.2f %.2f re %s Q ',$border,
                $this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        else
            $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x=$this->x;
        $y=$this->y;
        if(is_int(strpos($border,'L')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'l')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);

        if(is_int(strpos($border,'T')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        else if(is_int(strpos($border,'t')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);

        if(is_int(strpos($border,'R')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'r')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);

        if(is_int(strpos($border,'B')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'b')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if (trim($txt)!='') {
        $cr=substr_count($txt,"\n");
        if ($cr>0) { // Multi line
            $txts = explode("\n", $txt);
            $lines = count($txts);
            //$dy=($h-2*$this->cMargin)/$lines;
            for($l=0;$l<$lines;$l++) {
                $txt=$txts[$l];
                $w_txt=$this->GetStringWidth($txt);
                if($align=='R')
                    $dx=$w-$w_txt-$this->cMargin;
                elseif($align=='C')
                    $dx=($w-$w_txt)/2;
                else
                    $dx=$this->cMargin;

    			if ($this->unifontSubset)
    			{
    				$txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
    				foreach($this->UTF8StringToArray($txt) as $uni)
    					$this->CurrentFont['subset'][$uni] = $uni;
    			}
    			else
    				$txt2='('.str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt))).')';
                if($this->ColorFlag)
                    $s.='q '.$this->TextColor.' ';
                $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET ',
                    ($this->x+$dx)*$k,
                    ($this->h-($this->y+.5*$h+(.7+$l-$lines/2)*$this->FontSize))*$k,
                    $txt2);
                if($this->underline)
                    $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
                if($this->ColorFlag)
                    $s.='Q ';
                if($link)
                    $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
            }
        }
        else { // Single line
            $w_txt=$this->GetStringWidth($txt);
            $Tz=100;
            if ($w_txt>$w-2*$this->cMargin) { // Need compression
                $Tz=($w-2*$this->cMargin)/$w_txt*100;
                $w_txt=$w-2*$this->cMargin;
            }
            if($align=='R')
                $dx=$w-$w_txt-$this->cMargin;
            elseif($align=='C')
                $dx=($w-$w_txt)/2;
            else
                $dx=$this->cMargin;
			if ($this->unifontSubset)
			{
				$txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
				foreach($this->UTF8StringToArray($txt) as $uni)
					$this->CurrentFont['subset'][$uni] = $uni;
			}
			else
				$txt2='('.str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt))).')';
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $s.=sprintf('q BT %.2f %.2f Td %.2f Tz %s Tj ET Q ',
                        ($this->x+$dx)*$k,
                        ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,
                        $Tz,$txt2);
            if($this->underline)
                $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s.='Q ';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
        }
    }
// end change Cell function 12.08.2003
    if($s)
        $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
    {
        //Go to next line
        $this->y+=$h;
        if($ln==1)
            $this->x=$this->lMargin;
    }
    else
        $this->x+=$w;
}


function VCell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0)
{
    
    //Output a cell
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
    {
        $x=$this->x;
        $ws=$this->ws;
        if($ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation);
        $this->x=$x;
        if($ws>0)
        {
            $this->ws=$ws;
            $this->_out(sprintf('%.3f Tw',$ws*$k));
        }
    }
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $s='';
// begin change Cell function
    if($fill==1 or $border>0)
    {
        if($fill==1)
            $op=($border>0) ? 'B' : 'f';
        else
            $op='S';
        if ($border>1) {
            $s=sprintf(' q %.2f w %.2f %.2f %.2f %.2f re %s Q ',$border,
                        $this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        else
            $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x=$this->x;
        $y=$this->y;
        if(is_int(strpos($border,'L')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'l')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);

        if(is_int(strpos($border,'T')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        else if(is_int(strpos($border,'t')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);

        if(is_int(strpos($border,'R')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'r')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);

        if(is_int(strpos($border,'B')))
            $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        else if(is_int(strpos($border,'b')))
            $s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if(trim($txt)!='')
    {
        $cr=substr_count($txt,"\n");
        if ($cr>0) { // Multi line
            $txts = explode("\n", $txt);
            $lines = count($txts);
            for($l=0;$l<$lines;$l++) {
                $txt=$txts[$l];
                $w_txt=$this->GetStringWidth($txt);
                if ($align=='U')
                    $dy=$this->cMargin+$w_txt;
                elseif($align=='D')
                    $dy=$h-$this->cMargin;
                else
                    $dy=($h+$w_txt)/2;
    			if ($this->unifontSubset)
    			{
    				$txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
    				foreach($this->UTF8StringToArray($txt) as $uni)
    					$this->CurrentFont['subset'][$uni] = $uni;
    			}
    			else
    				$txt2='('.str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt))).')';
                if($this->ColorFlag)
                    $s.='q '.$this->TextColor.' ';
                $s.=sprintf('BT 0 1 -1 0 %.2f %.2f Tm (%s) Tj ET ',
                    ($this->x+.5*$w+(.7+$l-$lines/2)*$this->FontSize)*$k,
                    ($this->h-($this->y+$dy))*$k,$txt2);
                if($this->ColorFlag)
                    $s.='Q ';
            }
        }
        else { // Single line
            $w_txt=$this->GetStringWidth($txt);
            $Tz=100;
            if ($w_txt>$h-2*$this->cMargin) {
                $Tz=($h-2*$this->cMargin)/$w_txt*100;
                $w_txt=$h-2*$this->cMargin;
            }
            if ($align=='U')
                $dy=$this->cMargin+$w_txt;
            elseif($align=='D')
                $dy=$h-$this->cMargin;
            else
                $dy=($h+$w_txt)/2;
			if ($this->unifontSubset)
			{
				$txt2 = '('.$this->_escape($this->UTF8ToUTF16BE($txt, false)).')';
				foreach($this->UTF8StringToArray($txt) as $uni)
					$this->CurrentFont['subset'][$uni] = $uni;
			}
			else
				$txt2='('.str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt))).')';
            if($this->ColorFlag)
                $s.='q '.$this->TextColor.' ';
            $s.=sprintf('q BT 0 1 -1 0 %.2F %.2F Tm %.2f Tz %s Tj ET Q ',
                        ($this->x+.5*$w+.3*$this->FontSize)*$k,
                        ($this->h-($this->y+$dy))*$k,$Tz,$txt2);
            if($this->ColorFlag)
                $s.='Q ';
        }
    }
// end change Cell function
    if($s)
        $this->_out($s);
    $this->lasth=$h;
    if($ln>0)
    {
        //Go to next line
        $this->y+=$h;
        if($ln==1)
            $this->x=$this->lMargin;
    }
    else
        $this->x+=$w;
}


function NbLines($w,$txt)
{
    //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}



}

?>