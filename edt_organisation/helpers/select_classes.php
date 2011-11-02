<?php

/**
 * @version $Id: select_classes.php 1838 2008-05-19 19:35:59Z jjocal $
 * @copyright 2008
 *
 * Fichier qui renvoie un select des classes de l'établissement
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
	if ($analyse[4] == "select_classes.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_classes";
$classe_selected = isset($nom_classe) ? $nom_classe : (isset($nom_selected) ? $nom_selected : NULL);
$id_select = isset($nom_id_select) ? $nom_id_select : NULL;

echo '
	<select name ="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des classes</option>';
	// on recherche la liste des classes
	$query = mysql_query("SELECT id, classe FROM classes ORDER BY classe");
	$nbre = mysql_num_rows($query);
	for($i = 0; $i < $nbre; $i++){

		$classe[$i] = mysql_result($query, $i, "id");
		$nom[$i] = strtoupper(remplace_accents(mysql_result($query, $i, "classe"), 'all_nospace'));

		// On détermine le selected si c'est possible
		if (trim($nom[$i]) == $classe_selected OR $nom[$i] == $classe_selected) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}

		echo '
		<option value="'.$classe[$i].'"'.$selected.'>'.$nom[$i].'</option>';
	}
echo '</select>';
?>