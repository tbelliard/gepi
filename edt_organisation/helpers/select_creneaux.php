<?php

/**
 *
 *
 * @version $Id: select_creneaux.php 4152 2010-03-21 23:32:16Z adminpaulbert $
 * @copyright 2008
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

echo '
	<select name="'.$increment.'"'.$id_select.'>
		<option value="aucun">Liste des créneaux</option>
';
// On appele la liste des créneaux
$query = mysql_query("SELECT * FROM edt_creneaux WHERE type_creneaux != 'pause' AND type_creneaux != 'repas' ORDER BY heuredebut_definie_periode")
			OR trigger_error('Erreur dans la recherche des créneaux : '.mysql_error());

while($creneaux = mysql_fetch_array($query)){
	// On teste pour le selected
	// Dans le cas de edt_init_csv2.php, on modifie la forme des heures de début de créneau
	$test_creneau = explode("/", $_SERVER["SCRIPT_NAME"]);

	if ($test_creneau[3] == "edt_init_csv2.php") {
		// On tranforme le créneau
		$creneau_expl = explode(":", $creneaux["heuredebut_definie_periode"]);
		$creneau_udt = $creneau_expl[0].'H'.$creneau_expl[1];
	}else{
		$creneau_udt = $creneaux["heuredebut_definie_periode"];
	}
	if ($nom_selected == $creneau_udt) {
		$selected = ' selected="selected"';
	}else{
		$selected = '';
	}

	// On enlève les secondes à la fin
	$heure_deb = substr($creneaux["heuredebut_definie_periode"], 0, -3);

	echo '
		<option value="'.$creneaux["id_definie_periode"].'"'.$selected.'>'.$creneaux["nom_definie_periode"].' : '.$heure_deb.'</option>';
}

echo '
	</select>
';
?>