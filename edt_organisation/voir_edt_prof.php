<?php
/* fichier pour visionner les EdT des enseignants*/

$login_edt = isset($_GET['login_edt']) ? $_GET['login_edt'] : (isset($_POST['login_edt']) ? $_POST['login_edt'] : NULL);

echo '
	<form action="index_edt.php" name="liste_prof" method="post">
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
		<span style='font-size: xx-small; font-weight:normal;'>
			<a href='index_edt.php?visioedt=prof1&amp;login_edt=".$tab_select[$precedent]["login"]."&amp;type_edt_2=prof'>Professeur précédent</a>
		</span>
			";
	}
}

echo '
		<select name="login_edt" onchange=\'document.liste_prof.submit();\'>
			<option value="rien">Choix du professeur</option>
	';
for($i=0;$i<count($tab_select);$i++) {
	if(isset($login_edt)){
		if($login_edt==$tab_select[$i]["login"]){
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
			<option value='".$tab_select[$i]["login"]."'".$selected.">".$tab_select[$i]["nom"].' '.$tab_select[$i]["prenom"]."</option>
		";

}

echo '
		</select>
			<input type="hidden" name="type_edt_2" value="prof" />
			<input type="hidden" name="visioedt" value="prof1" />
	';

if($indice_prof_select!=-1){
	$suivant=$indice_prof_select+1;
	if($suivant<count($tab_select)){
		//$suivant=$indice_prof_select+1;
		echo "
		<span style='font-size: xx-small; font-weight:normal;'>
			<a href='index_edt.php?visioedt=prof1&amp;login_edt=".$tab_select[$suivant]["login"]."&amp;type_edt_2=prof'>Professeur suivant</a>
		</span>
			";
	}
}

echo "
	</form>
	";
?>
