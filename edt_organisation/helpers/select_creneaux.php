<?php

/**
 *
 *
 * @copyright 2008-2012
 *
 * Fichier qui renvoie un select des créneaux horaires de l'établissement
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
	if ($analyse[4] == "select_creneaux.php") {
		die();
	}

$increment = isset($nom_select) ? $nom_select : "liste_creneaux"; // name du select
$id_select = isset($nom_id_select) ? (' id="'.$nom_id_select.'"') : NULL; // id du select
if (!isset($nom_selected)) {
	$nom_selected = isset($nom_creneau) ? $nom_creneau : NULL; // permet de définir le selected
}

$nb_min_ref="";
$ecart_creneau=120;
if(preg_match("/[0-9]{1,2}h[0-9]{1,2}/", mb_strtolower($val))) {
	$tab_tmp=explode("h", mb_strtolower($val));
	$nb_min_ref=$tab_tmp[0]*60+$tab_tmp[1];
}

echo '
	<select name="'.$increment.'"'.$id_select.' onmouseover="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'yellow\'}" onmouseout="if(document.getElementById(\'texte_nomGepi'.$l.'\')) {document.getElementById(\'texte_nomGepi'.$l.'\').style.backgroundColor=\'\'}">
		<option value="aucun">Liste des créneaux</option>
';
// On appele la liste des créneaux
$query = mysql_query("SELECT * FROM edt_creneaux WHERE type_creneaux != 'pause' AND type_creneaux != 'repas' ORDER BY heuredebut_definie_periode")
			OR trigger_error('Erreur dans la recherche des créneaux : '.mysql_error());

while($creneaux = mysql_fetch_array($query)) {
	// On teste pour le selected
	// Dans le cas de edt_init_csv2.php, on modifie la forme des heures de début de créneau
	$test_creneau = explode("/", $_SERVER["SCRIPT_NAME"]);

	//echo "<!-- \$test_creneau[3]=$test_creneau[3] -->\n";
	$selected_2="n";

	if ($test_creneau[3] == "edt_init_csv2.php") {
		// On tranforme le créneau
		$creneau_expl = explode(":", $creneaux["heuredebut_definie_periode"]);
		$creneau_udt = $creneau_expl[0].'H'.$creneau_expl[1];
		//echo "<!-- \$creneau_udt=$creneau_udt -->\n";
	}else {
		$creneau_udt = $creneaux["heuredebut_definie_periode"];

		$tab_tmp=explode(":", $creneaux["heuredebut_definie_periode"]);
		$nb_min_test=$tab_tmp[0]*60+$tab_tmp[1];
		$ecart_test=abs($nb_min_test-$nb_min_ref);
		if(($ecart_test<=35)&&($ecart_test<$ecart_creneau)) {
			$ecart_creneau=$ecart_test;
			$selected_2="y";
		}
	}

	//if ($nom_selected == $creneau_udt) {
	if (($nom_selected == $creneau_udt)||($selected_2=='y')) {
		$selected = ' selected="selected"';
	}
	else {
		$selected = '';
	}

	// On enlève les secondes à la fin
	$heure_deb = mb_substr($creneaux["heuredebut_definie_periode"], 0, -3);

	echo '
		<option value="'.$creneaux["id_definie_periode"].'"'.$selected.'>'.$creneaux["nom_definie_periode"].' : '.$heure_deb.'</option>';
}

echo '
	</select>
';
?>
