<?php

/**
 *
 *
 * @copyright 2008-2011
 *
 * Fichier qui renvoie un select des jours ouvrés de l'établissement
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
	if ($analyse[4] == "select_jours.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_jours"; // name du select
$id_select = isset($nom_id_select) ? (' id="'.$nom_id_select.'"') : NULL; // id du select
if (!isset($nom_selected)) {
	$nom_selected = isset($nom_jour) ? $nom_jour : NULL; // permet de définir le selected
}

echo '
	<select name="'.$increment.'"'.$id_select.' onmouseover="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'yellow\'}" onmouseout="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'\'}">
		<option value="aucun">Liste des jours</option>
';
// On appele la liste des créneaux
$query = mysql_query("SELECT * FROM horaires_etablissement LIMIT 0, 7")
			OR error_reporting('Erreur dans la recherche des jours : '.mysql_error());

while($jours = mysql_fetch_array($query)){
	// le selected
	if (my_strtoupper($jours["jour_horaire_etablissement"]) == $nom_selected) {
		$selected = ' selected="selected"';
	}else{
		$selected = '';
	}
	echo '
		<option value="'.$jours["id_horaire_etablissement"].'"'.$selected.'>'.ucfirst($jours["jour_horaire_etablissement"]).'</option>';
}

echo '
	</select>
';

?>
