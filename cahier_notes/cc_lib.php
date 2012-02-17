<?php
/**
 * Fonctions de évaluation cumule
 * 
*
* @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Carnet_de_notes
 * @subpackage Evaluation_cumule
 * @license GNU/GPL, 
 * @see COPYING.txt
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

$nom_cc=getSettingValue('nom_cc');
if($nom_cc=='') {
	$nom_cc="evaluation-cumul";
}
/**
 *
 * @param float $moyenne note à arrondir
 * @param string $arrondir
 * @return float
 */
function precision_arrondi($moyenne,$arrondir) {
	//
	// Calcul des arrondis
	//
	$moyenne=strtr($moyenne,",",".");
	if ($arrondir == 's1') {
		// s1 : arrondir au dixième de point supérieur
		$moyenne = number_format(ceil(strval(10*$moyenne))/10,1,'.','');
	} else if ($arrondir == 's5') {
		// s5 : arrondir au demi-point supérieur
		$moyenne = number_format(ceil(strval(2*$moyenne))/2,1,'.','');
		//$moyenne = ceil(strval(2*$moyenne))/2;
	} else if ($arrondir == 'se') {
		// se : arrondir au point entier supérieur
		$moyenne = number_format(ceil(strval($moyenne)),1,'.','');
	} else if ($arrondir == 'p1') {
		// s1 : arrondir au dixième le plus proche
		$moyenne = number_format(round(strval(10*$moyenne))/10,1,'.','');
	} else if ($arrondir == 'p5') {
		// s5 : arrondir au demi-point le plus proche
		$moyenne = number_format(round(strval(2*$moyenne))/2,1,'.','');
	} else if ($arrondir == 'pe') {
		// se : arrondir au point entier le plus proche
		$moyenne = number_format(round(strval($moyenne)),1,'.','');
	}
	return $moyenne;
}

function get_infos_devoir($id_devoir) {
	$retour="";
	$sql="SELECT * FROM cn_devoirs cd WHERE cd.id='$id_devoir';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);

		$retour=$lig->nom_court." (".formate_date($lig->date).")";
	}
	return $retour;
}

?>
