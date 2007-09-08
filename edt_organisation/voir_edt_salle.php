<?php

/* fichier pour visionner les EdT des salles */

echo '
	<form action="index_edt.php" name="liste_salle" method="post">
		<select name="login_edt" onchange=\'document.liste_salle.submit();\'>
			<option value="rien">Liste des salles</option>
	';

$tab_select = renvoie_liste("salle");

for($i=0;$i<count($tab_select);$i++) {
	if(isset($login_edt)){
		if($login_edt==$tab_select[$i]["id_salle"]){
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
			<option value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["nom_salle"]."</option>
		";
	}


echo '
			<input type="hidden" name="type_edt_2" value="salle" />
			<input type="hidden" name="visioedt" value="salle1" />
		</select>
	</form>
	';

?>