<?php

/* fichier pour visionner les EdT des salles */

echo "<form action=\"index_edt.php\" name=\"liste_salle\" method=\"POST\">\n";
echo "<FORM>\n";
echo "<select name=\"login_edt\" onchange='document.liste_salle.submit();'>\n";

echo "<OPTION value=\"rien\">Liste des salles</OPTION>\n";

$tab_select = renvoie_liste("salle");

for($i=0;$i<count($tab_select);$i++) {
	if(isset($login_edt)){
		if($login_edt==$tab_select[$i]["id_salle"]){
			$selected=" selected='true'";
		}
		else{
			$selected="";
		}
	}
	else{
		$selected="";
	}
	echo ("<OPTION value='".$tab_select[$i]["id_salle"]."'".$selected.">".$tab_select[$i]["nom_salle"]."</OPTION>\n");
	}


echo "<input type=hidden name=\"type_edt_2\" value=\"salle\" />\n";
echo "<input type=hidden name=\"visioedt\" value=\"salle1\" />\n";

echo "</SELECT>\n";
echo "</FORM>\n";

?>