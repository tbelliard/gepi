<?php
/*
 * Created on 23 juin 2006
 *
 */
 
 function get_classe($_id_classe) {
	
	if (!is_numeric($_id_classe)) $_id_classe = "0";
	
	$query = mysql_query("select id, classe, nom_complet ".
							"from classes ".
							"where (" .
							"id = '" . $_id_classe . "'".
							")");
							
	$temp["id"] = mysql_result($query, 0, "id");
	$temp["classe"] = mysql_result($query, 0, "classe");
	$temp["nom_complet"] = mysql_result($query, 0, "nom_complet");
	
	return $temp;
}

?>
