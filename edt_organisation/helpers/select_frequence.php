<?php

/**
 *
 *
 * @copyright 2008
 *
 * Fichier qui renvoie un select des types de semaine ainsiq ue des différentes périodes du calendier de l'établissement
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
	if ($analyse[4] == "select_frequence.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_semaines_periodes";
$id_select = isset($nom_id_select) ? ' id="'.$nom_id_select.'"' : NULL;
$test_selected = isset($nom_selected) ? $nom_selected : NULL;

echo '
	<select name ="'.$increment.'"'.$id_select.' onmouseover="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'yellow\'}" onmouseout="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'\'}">
		<option value="aucun">Liste des types de semaine et des périodes du calendrier</option>';

// On récupère les différents type de semaine
$query = mysql_query("SELECT DISTINCT type_edt_semaine FROM edt_semaines ORDER BY type_edt_semaine LIMIT 5")
			OR error_reporting('Erreur dans la requête : '.mysql_error());

while($type_semaine = mysql_fetch_array($query)){

	echo '
	<option value="'.$type_semaine["type_edt_semaine"].'">Semaine '.$type_semaine["type_edt_semaine"].'</option>';
}

// On récupère les différentes périodes du calendrier
$query = mysql_query("SELECT id_calendrier, nom_calendrier FROM edt_calendrier WHERE numero_periode = '0' AND etabvacances_calendrier = '0'")
			OR error_reporting('Erreur dans la requête (périodes) : '.mysql_error());

while($periodes = mysql_fetch_array($query)){

	echo '
	<option value="'.$periodes["id_calendrier"].'">'.$periodes["nom_calendrier"].'</option>';
}
echo '
	</select>';

?>
