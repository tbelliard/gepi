<?php
/**
 *
 * @version $Id: voir_edt_classe.php 4059 2010-01-31 20:03:48Z adminpaulbert $
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

/* Fichier pour visionner les EdT des classes */

echo '
	<form action="index_edt.php" id="liste_classe_id" method="post">
	<p>
	';

	$tab_select = renvoie_liste("classe");

$indice_classe_select=-1;
if(isset($login_edt)){
	for($i=0; $i<count($tab_select); $i++) {
		if($login_edt == $tab_select[$i]["id"]){
			$indice_classe_select = $i;
			break;
		}
	}
}

//if(isset($login_edt)){
if($indice_classe_select != -1){
	if($indice_classe_select != 0){
		$precedent = $indice_classe_select-1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=classe1&amp;login_edt=".$tab_select[$precedent]["id"]."&amp;type_edt_2=classe'>".PREVIOUS_CLASS."</a>
		</span>
			";
	}
}

echo '
	
		<select name="login_edt" onchange=\'document.getElementById("liste_classe_id").submit();\'>
			<option value="rien">'.CHOOSE_CLASS.'</option>
	';

for($i=0;$i<count($tab_select);$i++) {
if(isset($login_edt)){
		if($login_edt==$tab_select[$i]["id"]){
			$selected=" selected='selected'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
	echo ("			<option value='".$tab_select[$i]["id"]."'".$selected.">".$tab_select[$i]["classe"]."</option>\n");
	}


echo '
		</select>
			<input type="hidden" name="type_edt_2" value="classe" />
			<input type="hidden" name="visioedt" value="classe1" />
	';

if($indice_classe_select != count($tab_select)){
	$suivant = $indice_classe_select+1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=classe1&amp;login_edt=".$tab_select[$suivant]["id"]."&amp;type_edt_2=classe'>".NEXT_CLASS."</a>
		</span>
			";
	}
}

echo '
	</p>
	</form>
	';

?>
