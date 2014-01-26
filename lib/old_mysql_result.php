<?php
/**
 * Fonction basée sur l'api mysqli et
 * simulant la fonction mysql_result()
 * Copyright 2014 Marc Leygnac
 *
 * @param type $result résultat après requête
 * @param integer $row numéro de la ligne
 * @param string/integer $field indice ou nom du champ
 * @return type valeur du champ ou false si erreur
 */
function old_mysql_result($result,$row,$field=0) {
	if ($result===false) return;
	if ($row>mysqli_num_rows($result)) return false;
	if (is_string($field) && !(strpos($field,".")===false)) {
		// si $field est de la forme table.field ou alias.field
		// on convertit $field en indice numérique
		$t_field=explode(".",$field);
		$field=-1;
		$t_fields=mysqli_fetch_fields($result);
		for ($id=0;$id<mysqli_num_fields($result);$id++) {
			if ($t_fields[$id]->table==$t_field[0] && $t_fields[$id]->name==$t_field[1]) {
				$field=$id;
				break;
			}
		}
		if ($field==-1) return false;
	}
	mysqli_data_seek($result,$row);
	$line=mysqli_fetch_array($result);
	return isset($line[$field])?$line[$field]:false;
}
?>
