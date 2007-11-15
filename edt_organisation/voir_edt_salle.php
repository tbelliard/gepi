<?php

/* fichier pour visionner les EdT des salles */

echo '
	<form action="index_edt.php" name="liste_salle" method="post">
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
			<a href='index_edt.php?visioedt=salle1&amp;login_edt=".$tab_select[$precedent]["id_salle"]."&amp;type_edt_2=salle'>Salle précédente</a>
		</span>
			";
	}
}

echo '
		<select name="login_edt" onchange=\'document.liste_salle.submit();\'>
			<option value="rien">Liste des salles</option>
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
	echo "
			<option value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["numero_salle"]." / ".$tab_select[$i]["nom_salle"]."</option>
		";
	}


echo '
		</select>
			<input type="hidden" name="type_edt_2" value="salle" />
			<input type="hidden" name="visioedt" value="salle1" />
	';

if($indice_salle_select != -1){
	$suivant = $indice_salle_select + 1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=salle1&amp;login_edt=".$tab_select[$suivant]["id_salle"]."&amp;type_edt_2=salle'>Salle suivante</a>
		</span>
			";
	}
}

echo '
	</form>
	';

?>