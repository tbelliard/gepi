<?php

/**
 * @version $Id: bulletin_fonctions.php 1570 2008-03-03 20:19:56Z jjocal $
 *
 * Les fonctions utiles pour les bulletins pdf
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Calcul de la moyenne des AID
function calcul_toute_moyenne_aid ($indice_aid, $periode_select) {
	global $prefix_base;

	$addition_des_notes=0; $note=0; $cpt_notes=0; $moyenne_mini=20; $moyenne_maxi=0;
	$requete_note = mysql_query('SELECT * FROM '.$prefix_base.'aid_appreciations aa
						WHERE aa.indice_aid = "'.$indice_aid.'" AND
						aa.periode = "'.$periode_select.'" AND
						aa.statut = ""');
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

//permet de transformer les caractères html
function unhtmlentities($chaineHtml) {

	$tmp = get_html_translation_table(HTML_ENTITIES);
	$tmp = array_flip ($tmp);
	$chaineTmp = strtr ($chaineHtml, $tmp);

/*
	// La deuxième solution ci-dessous pose des problèmes
	$string=$chaineHtml;
	// Remplace les entités numériques
	$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
	$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
	// Remplace les entités littérales
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	$chaineTmp = strtr ($string, $trans_tbl);
*/
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

?>