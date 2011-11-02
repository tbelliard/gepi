<?php
/**
 *
 * @version $Id: voir_edt_salle.php 4059 2010-01-31 20:03:48Z adminpaulbert $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

/* fichier pour visionner les EdT des salles */

echo '
	<form action="index_edt.php" id="liste_salle" method="post">
	<p>
	';

$tab_select = renvoie_liste("salle");

$indice_salle_select = -1;
if(isset($login_edt)){
	for($i=0; $i<count($tab_select); $i++) {
		if($login_edt == $tab_select[$i]["id_salle"]){
			$indice_salle_select = $i;
			break;
		}
	}
}

//if(isset($login_edt)){
if($indice_salle_select != -1){
	if($indice_salle_select != 0){
		$precedent = $indice_salle_select - 1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=salle1&amp;login_edt=".$tab_select[$precedent]["id_salle"]."&amp;type_edt_2=salle'>".PREVIOUS_CLASSROOM."</a>
		</span>
			";
	}
}

echo '
		<select name="login_edt" onchange=\'document.getElementById("liste_salle").submit();\'>
			<option value="rien">'.CHOOSE_CLASSROOM.'</option>
	';



for($i=0; $i<count($tab_select); $i++) {
	if(isset($login_edt)){
		if($login_edt == $tab_select[$i]["id_salle"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
	// On affiche ou non le nom de la salle
	if ($tab_select[$i]["nom_salle"] != "") {
		$aff_nom_salle = "(".$tab_select[$i]["nom_salle"].")";
	} else {
		$aff_nom_salle = "";
	}
	echo "
			<option value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["numero_salle"]." ".$aff_nom_salle."</option>
		";
	}


echo '
		</select>
			<input type="hidden" name="type_edt_2" value="salle" />
			<input type="hidden" name="visioedt" value="salle1" />
	';

if($indice_salle_select != count($tab_select)){
	$suivant = $indice_salle_select + 1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=salle1&amp;login_edt=".$tab_select[$suivant]["id_salle"]."&amp;type_edt_2=salle'>".NEXT_CLASSROOM."</a>
		</span>
			";
	}
}

echo '
	</p>
	</form>
	';

?>
