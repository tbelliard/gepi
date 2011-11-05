<?php
/*
 * Last modification  : 18/03/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On précise de ne pas traiter les données avec la fonction anti_inject
$traite_anti_inject = 'no';
// Initialisations files
require_once("../lib/initialisations.inc.php");

//$texte : le texte à afficher
//$height : la hauteur de l'image
//$width : la largeur de l'image
//$colortxt : la couleur du texte de la forme #xxxxxx ou xxxxxx

unset($height);
$height = isset($_GET["height"]) ? $_GET["height"] : NULL;
unset($width);
$width = isset($_GET["width"]) ? $_GET["width"] : NULL;
unset($texte);
$texte = isset($_GET["texte"]) ? unslashes($_GET["texte"]) : NULL;
unset($colortxt);
$colortxt = isset($_GET["colortxt"]) ? $_GET["colortxt"] : NULL;

$long_chaine = mb_strlen($texte);
if ($height != '') {
    $haut_im = $height;
} else {
    $haut_im = $long_chaine*8;
}
if ($width != '') {
    $larg_im = $width;
} else {
    $larg_im = 20;
}

Header("Content-Type: image/png");
$texte = urldecode($texte);
if ($colortxt != '') {
    $colortxt = urldecode($colortxt);
    if (mb_substr($colortxt ,0,1)=="#") $color_bg=mb_substr($colortxt,1,6);
    $col_txt[0] = hexdec(mb_substr($colortxt, 0, 2));
    $col_txt[1] = hexdec(mb_substr($colortxt, 2, 2));
    $col_txt[2] = hexdec(mb_substr($colortxt, 4, 2));
} else {
    $col_txt[0] = 0;
    $col_txt[1] = 0;
    $col_txt[2] = 0;
}

//$im = @imageCreate($larg_im, $haut_im) or die ();
$im = @imageCreate($larg_im, $haut_im+10) or die ();
$color_back = imageColorAllocate($im, 255,255,255);
$color_text = imageColorAllocate($im, $col_txt[0],$col_txt[1],$col_txt[2]);
imageFill($im, $larg_im, $haut_im, $color_back);
imagecolortransparent ($im,$color_back);

$y = ($haut_im+$long_chaine*8)/2;
if ($y > $haut_im) $y=$haut_im;
//$x = ($larg_im)/2;
$x = ($larg_im)*0.65;

imagettftext($im, 9, 90, $x, $y, $color_text, dirname(__FILE__)."/../fpdf/font/unifont/DejaVuSansCondensed.ttf", $texte);
imagepng($im);
imagedestroy($im);
?>