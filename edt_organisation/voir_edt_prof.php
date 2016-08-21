<?php
/**
 *
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

/* fichier pour visionner les EdT des enseignants*/

$login_edt = isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);

echo '
	<form action="index_edt.php" id="liste_prof" method="post">
		<p>
	';

$tab_select = renvoie_liste("prof");

$indice_prof_select=-1;
if(isset($login_edt)){
	for($i=0;$i<count($tab_select);$i++) {
		if($login_edt==$tab_select[$i]["login"]){
			$indice_prof_select=$i;
			break;
		}
	}
}

//if(isset($login_edt)){
if($indice_prof_select!=-1){
	if($indice_prof_select!=0){
		$precedent=$indice_prof_select-1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=prof1&amp;login_edt=".$tab_select[$precedent]["login"]."&amp;type_edt_2=prof'>".PREVIOUS_TEACHER."</a>
		</span>
			";
	}
}

echo '
		<select name="login_edt" onchange=\'document.getElementById("liste_prof").submit();\'>
			<option value="rien">'.CHOOSE_TEACHER.'</option>
	';
for($i=0; $i<count($tab_select); $i++) {
	if(isset($login_edt)){
		if($login_edt == $tab_select[$i]["login"]){
			$selected = " selected='selected'";
		}
		else{
			$selected = "";
		}
	}
	else{
		$selected = "";
	}
	echo "
			<option value='".$tab_select[$i]["login"]."'".$selected.">".my_strtoupper($tab_select[$i]["nom"]).' '.casse_mot($tab_select[$i]["prenom"],'majf2')."</option>
		";

}

echo '
		</select>
			<input type="hidden" name="type_edt_2" value="prof" />
			<input type="hidden" name="visioedt" value="prof1" />
	';

if($indice_prof_select!=count($tab_select)){
	$suivant=$indice_prof_select+1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=prof1&amp;login_edt=".$tab_select[$suivant]["login"]."&amp;type_edt_2=prof'>".NEXT_TEACHER."</a>
		</span>
			";
	}
}

echo "
	</p>
	</form>
	";
?>
