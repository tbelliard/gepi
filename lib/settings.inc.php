<?php

/** Manipulation de la table setting
 *
 *
 *
 * Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin, Romain Neil
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Initialisation
 * @subpackage settings
 *
 */

/**
 * Tableau des réglages
 *
 * $gepiSettings['name'] = 'value'
 *
 * name le nom du réglage dans setting.name
 *
 * value la valeur du réglage dans setting.value
 *
 * @global array $GLOBALS ['gepiSettings']
 * @name $gepiSettings
 */
$GLOBALS['gepiSettings'] = array();

/**
 * Charge les réglages depuis la base de données
 *
 * Recherche tous les réglages
 * Retourne le résultat dans le tableau associatif $gepiSettings
 *
 * Retourne TRUE si tout c'est bien passé, FALSE sinon
 *
 * @return bool TRUE if the settings are loaded
 * @global array
 */
function loadSettings() {
	global $mysqli;
	global $gepiSettings;
	$sql = "SELECT name, value FROM setting";

	$resultat = mysqli_query($mysqli, $sql);
	if (!$resultat) return (FALSE);
	if ($resultat->num_rows == 0) return (FALSE);

	while ($donnees = mysqli_fetch_assoc($resultat)) {
		$gepiSettings[$donnees['name']] = $donnees['value'];
	}

	$resultat->free();
	return (TRUE);
}

/**
 * Renvoie la valeur d'un réglage en fonction de son nom
 *
 * Utilisez cette fonction à l'intérieur des autres fonctions afin de ne pas avoir
 * à déclarer la variable globale $gepiSettings
 *
 * Retourne la valeur si le nom existe
 *
 * @param string $_name Le nom du réglage que vous cherchez
 * @return string La valeur correspondant à $_name ou null si le setting n'est pas présent
 *
 * @global array
 */
function getSettingValue($_name) {
	global $gepiSettings;
	if (isset($gepiSettings[$_name])) return ($gepiSettings[$_name]);
	else return null;
}

/**
 * Sauvegarde une paire name, value dans la base
 *
 * Utilisez cette fonction ponctuellement, Si vous devez sauvegarder plusieurs réglages,
 * vous devriez plutôt écrire votre propre code
 *
 * @param string $_name Le nom du réglage
 * @param string $_value La valeur du réglage
 * @return bool TRUE si tout s'est bien passé, FALSE sinon
 * @global array $gepiSettings
 */
function saveSetting($_name, $_value) {
	global $gepiSettings;
	global $mysqli;
	$R = mysqli_query($mysqli, "SELECT * FROM setting WHERE NAME='" . $_name . "' LIMIT 1");
	if ($R->num_rows > 0) {
		$sql = "update setting set VALUE = \"" . $_value . "\" where NAME = \"" . $_name . "\"";
	} else {
		$sql = "insert into setting set NAME = \"" . $_name . "\", VALUE = \"" . $_value . "\"";
	}

	$res = mysqli_query($mysqli, $sql);
	if (!$res) return (FALSE);

	$gepiSettings[$_name] = $_value;
	return (TRUE);
}

/**
 * Renvoie TRUE si le réglage est 'yes' ou 'y'
 *
 *
 * @param string $_name Le nom du réglage que vous cherchez
 * @return bool TRUE si le réglage que vous cherchez est 'yes' or 'y', FALSE sinon
 */
function getSettingAOui($_name) {
	if (getSettingValue($_name) == "yes" || getSettingValue($_name) == "y") {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Renvoie TRUE si le réglage est 'no' ou 'n'
 *
 *
 * @param string $_name Le nom du réglage que vous cherchez
 * @return bool TRUE si le réglage que vous cherchez est 'no' or 'n', FALSE sinon
 */
function getSettingANon($_name) {
	if (getSettingValue($_name) == "no" || getSettingValue($_name) == "n") {
		return TRUE;
	} else {
		return FALSE;
	}
}


/**
 * Supprime une entrée dans la table setting
 *
 * Utilisez cette fonction ponctuellement, Si vous devez supprimer plusieurs réglages,
 * vous devriez plutôt écrire votre propre code
 *
 * @param string $_name Le nom du réglage
 * @return bool TRUE si tout s'est bien passé, FALSE sinon
 * @global array $gepiSettings
 */
function deleteSetting($_name) {
	global $gepiSettings;
	global $mysqli;
	$sql = "SELECT * FROM setting WHERE NAME='" . $_name . "' LIMIT 1";
	$R = mysqli_query($mysqli, $sql);
	if ($R->num_rows > 0) {
		$sql = "DELETE FROM setting where NAME = \"" . $_name . "\"";
		$res = mysqli_query($mysqli, $sql);
		if (!$res) return (FALSE);
		unset($GLOBALS["gepiSettings"][$_name]);
		return (TRUE);
	} else {
		return (FALSE);
	}
}
