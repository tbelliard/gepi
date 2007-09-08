<?php
/* Fichier pour visionner les EdT des classes */

echo '
	<form action="index_edt.php" name="liste_classe" method="post">
		<select name="login_edt" onchange=\'document.liste_classe.submit();\'>
			<OPTION value="rien">Choix de la classe</OPTION>
	';

$tab_select = renvoie_liste("classe");

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
	echo ("			<OPTION value='".$tab_select[$i]["id"]."'".$selected.">".$tab_select[$i]["classe"]."</OPTION>\n");
	}


echo '
			<input type="hidden" name="type_edt_2" value="classe" />
			<input type="hidden" name="visioedt" value="classe1" />
		</select>
	</form>';

?>