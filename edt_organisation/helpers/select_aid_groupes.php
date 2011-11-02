<?php

/**
 * @version $Id: select_aid_groupes.php 7526 2011-07-27 19:21:30Z crob $
 * @copyright 2008-2011
 *
 * Fichier qui renvoie un select des professeurs de l'établissement
 * pour l'intégrer dans un fomulaire
 *
 */
// On récupère les infos utiles pour le fonctionnement des requêtes sql
$niveau_arbo = 1;
require_once("../lib/initialisations.inc.php");

// Sécurité : éviter que quelqu'un appelle ce fichier seul
$serveur_script = $_SERVER["SCRIPT_NAME"];
$analyse = explode("/", $serveur_script);
$analyse[4] = isset($analyse[4]) ? $analyse[4] : NULL;
	if ($analyse[4] == "select_aid_groupes.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_aid_groupes";
$id_select = isset($nom_id_select) ? ' id="'.$nom_id_select.'"' : NULL;
$test_selected = isset($nom_selected) ? $nom_selected : NULL;

$id_groupe_defaut="";
$sql="SELECT id FROM groupes WHERE name LIKE '%$valeur%' LIMIT 1;";
$res_grp=mysql_query($sql);
if(mysql_num_rows($res_grp)>0) {
	$lig_grp_def=mysql_fetch_object($res_grp);
	$id_groupe_defaut=$lig_grp_def->id;
}

echo '
	<select name ="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des AID et des groupes</option>
			<optgroup label="Les AID">';
	// on recherche la liste des AID
	$query = mysql_query("SELECT id, nom FROM aid");
	$nbre = mysql_num_rows($query);
	for($i = 0; $i < $nbre; $i++){
		$nom[$i] = mysql_result($query, $i, "nom");
		$indice_aid[$i] = mysql_result($query, $i, "id");
		/*/ On récupère le nom précis de cette AID
		$query2 = mysql_query("SELECT nom FROM aid WHERE id = '".$indice_aid[$i]."' ORDER BY nom");
		$nom_aid = mysql_result($query2, 0,"nom");
		$query3 = mysql_query("SELECT login FROM j_aid_eleves WHERE indice_aid = '".$indice_aid[$i]."'");
		$nbre_eleves = mysql_num_rows($query3);
		 ('.$nom_aid.' avec '.$nbre_eleves.' élèves)*/
		// On teste le selected
		if ($nom[$i] == $test_selected) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '
		<option value="AID|'.$indice_aid[$i].'"'.$selected.'>'.$nom[$i].'</option>';
	}
	echo '
			</optgroup>
			<optgroup label="Les groupes">';
	$query = mysql_query("SELECT id, description, name FROM groupes ORDER BY description");
	$nbre_groupes = mysql_num_rows($query);
	for($a = 0; $a < $nbre_groupes; $a++){
		$id_groupe[$a]["id"] = mysql_result($query, $a, "id");
		$id_groupe[$a]["description"] = mysql_result($query, $a, "description");
		$id_groupe[$a]["name"] = mysql_result($query, $a, "name");

		// On récupère toutes les infos pour l'affichage
		// On n'utilise pas getGroup() car elle est trop longue et récupère trop de choses dont on n'a pas besoin

		$query1 = mysql_query("SELECT classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_classe = c.id AND jgc.id_groupe = '".$id_groupe[$a]["id"]."'");
		$classe = mysql_fetch_array($query1);

		// On teste le selected après s'être assuré qu'il n'était pas déjà renseigné
			if ($id_groupe[$a]["description"] == $test_selected) {
				$selected = ' selected="selected"';
			} elseif($id_groupe[$a]["id"] == $id_groupe_defaut) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

		//echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected.'>'.$id_groupe[$a]["description"].'('.$classe[0].')</option>';
		echo '		<option value="'.$id_groupe[$a]["id"].'"'.$selected.'>'.$id_groupe[$a]["description"].'('.$classe[0].') ('.$id_groupe[$a]["name"].')</option>';
	}
echo '
			</optgroup>
	</select>';

?>