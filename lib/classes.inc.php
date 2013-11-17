<?php
/*
 * version: $id$
 *
 * Created on 23 juin 2006
 *
 */

 function get_classe($_id_classe) {

	if (!is_numeric($_id_classe)) $_id_classe = "0";

	$query = mysqli_query($GLOBALS["___mysqli_ston"], "select id, classe, nom_complet ".
							"from classes ".
							"where (" .
							"id = '" . $_id_classe . "'".
							")");

	$temp["id"] = mysql_result($query, 0, "id");
	$temp["classe"] = mysql_result($query, 0, "classe");
	$temp["nom_complet"] = mysql_result($query, 0, "nom_complet");

	return $temp;
}

function get_eleves_classe($_id_classe){

	if (!is_numeric($_id_classe)) $_id_classe = "0";

	$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT nom, prenom, e.login, id_eleve
							FROM j_eleves_classes jec, eleves e
							WHERE e.login = jec.login
							AND jec.id_classe = '".$_id_classe."'
							ORDER BY nom, prenom");
	$nbre = mysqli_num_rows($query);

	$retour = array();
	$retour["nbre"] = $nbre;

	for($i = 0 ; $i < $nbre ; $i++){

		$retour[$i]["nom"] = mysql_result($query, $i, "nom");
		$retour[$i]["prenom"] = mysql_result($query, $i, "prenom");
		$retour[$i]["login"] = mysql_result($query, $i, "login");
		$retour[$i]["id_eleve"] = mysql_result($query, $i, "id_eleve");

	}
	return $retour;
}
?>
