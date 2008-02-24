<?php

/**
 * @version $Id$
 * @copyright 2008
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
	if ($analyse[4] == "select_professeurs.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_professeur";
$nom_selected = isset($nom_prof) ? $nom_prof : NULL;

echo '
	<select name ="'.$increment.'">
		<option value="aucun">Liste des professeurs</option>';
	// on recherche la liste des professeurs
	$query = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE statut = 'professeur' ORDER BY nom, prenom");
	$nbre = mysql_num_rows($query);
	for($i = 0; $i < $nbre; $i++){
		$utilisateur[$i] = mysql_result($query, $i, "login");
		$nom[$i] = mysql_result($query, $i, "nom");
		$prenom[$i] = mysql_result($query, $i, "prenom");
		// On détermine le selected si c'est possible
		if ($nom[$i] == $nom_selected) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '
		<option value="'.$utilisateur[$i].'"'.$selected.'>'.$nom[$i].' '.$prenom[$i].'</option>';
	}
echo '</select>';
?>