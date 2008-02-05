<?php

/**
 * @version $Id$
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
	if ($analyse[4] == "select_matieres.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_matieres";

echo '
	<select name ="'.$increment.'">
		<option value="aucun">Liste des matières</option>';
	// on recherche la liste des matières
	$query = mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY nom_complet");
	$nbre = mysql_num_rows($query);
	for($i = 0; $i < $nbre; $i++){
		$matiere[$i] = mysql_result($query, $i, "matiere");
		$nom[$i] = mysql_result($query, $i, "nom_complet");

		echo '
		<option value="'.$matiere[$i].'">'.$nom[$i].'</option>';
	}
echo '</select>';
?>