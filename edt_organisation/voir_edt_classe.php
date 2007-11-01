<?php
/* Fichier pour visionner les EdT des classes */

echo '
	<form action="index_edt.php" name="liste_classe" method="post">
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
			<a href='index_edt.php?visioedt=classe1&amp;login_edt=".$tab_select[$precedent]["id"]."&amp;type_edt_2=classe'>Classe précédente</a>
		</span>
			";
	}
}

echo '

		<select name="login_edt" onchange=\'document.liste_classe.submit();\'>
			<option value="rien">Choix de la classe</option>
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

if($indice_classe_select != -1){
	$suivant = $indice_classe_select+1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span class=\"edt_suivant\">
			<a href='index_edt.php?visioedt=classe1&amp;login_edt=".$tab_select[$suivant]["id"]."&amp;type_edt_2=classe'>Classe suivante</a>
		</span>
			";
	}
}

echo '
	</form>
	';

?>