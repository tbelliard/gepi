<?php
/** Manipulation de la table setting
 * 
 * 
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
 * @global array $GLOBALS['gepiSettings']
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
 * @global array
 * @return bool TRUE if the settings are loaded
 */
function loadSettings()
{
    global $gepiSettings;
    $sql = "SELECT name, value FROM setting";
    $res = sql_query($sql);
    if (! $res) return (FALSE);
    if (sql_count($res) == 0) {
        return (FALSE);
    } else {
        for ($i = 0; ($row = sql_row($res, $i)); $i++) {
            $gepiSettings[$row[0]] = $row[1];
        }
        return (TRUE);
    }
}

/**
 * Renvoie la valeur d'un réglage en fonction de son nom
 *
 * Utilisez cette fonction à l'intérieur des autres fonctions afin de ne pas avoir
 * à déclarer la variable globale $gepiSettings
 *
 * Retourne la valeur si le nom existe
 *
 * @global array 
 * @param text $_name Le nom du réglage que vous cherchez
 * @return text La valeur correspondant à $_name ou null si le setting n'est pas présent
 * 
 */
function getSettingValue($_name)
{
    global $gepiSettings;
    if (isset($gepiSettings[$_name])) return ($gepiSettings[$_name]);
    else return null;
}

/**
 * Renvoie TRUE si le réglage est 'yes' ou 'y'
 *
 *
 * @param text $_name Le nom du réglage que vous cherchez
 * @return bool TRUE si le réglage que vous cherchez est 'yes' or 'y', FALSE sinon
 */
function getSettingAOui($_name)
{
	if (getSettingValue($_name)=="yes" || getSettingValue($_name)=="y"){
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Sauvegarde une paire name, value dans la base
 *
 * Utilisez cette fonction ponctuellement, Si vous devez sauvegarder plusieurs réglages,
 * vous devriez plutôt écrire votre propre code
 * 
 * @global array $gepiSettings
 * @param text $_name Le nom du réglage
 * @param text $_value La valeur du réglage
 * @return bool TRUE si tout s'est bien passé, FALSE sinon
 */
function saveSetting($_name, $_value)
{
    global $gepiSettings;
    $R=mysql_query("SELECT * FROM setting WHERE NAME='".$_name."' LIMIT 1");
   if (mysql_num_rows($R)>0) {
    $sql = "update setting set VALUE = \"" . $_value . "\" where NAME = \"" . $_name . "\"";
    $res = sql_query($sql);
    if ( ! $res) return (FALSE);
    } else {
        $sql = "insert into setting set NAME = \"" . $_name . "\", VALUE = \"" . $_value . "\"";
        $res = sql_query($sql);
        if ( ! $res) return (FALSE);
    }
    $gepiSettings[$_name] = $_value;
    return (TRUE);
}

/**
 * Supprime une entrée dans la table setting
 *
 * Utilisez cette fonction ponctuellement, Si vous devez supprimer plusieurs réglages,
 * vous devriez plutôt écrire votre propre code
 * 
 * @global array $gepiSettings
 * @param text $_name Le nom du réglage
 * @return bool TRUE si tout s'est bien passé, FALSE sinon
 */
function deleteSetting($_name)
{
    global $gepiSettings;
    $R=mysql_query("SELECT * FROM setting WHERE NAME='".$_name."' LIMIT 1");
		if (mysql_num_rows($R)>0) {
		$sql = "DELETE FROM setting where NAME = \"" . $_name . "\"";
		$res = sql_query($sql);
		if ( ! $res) return (FALSE);
		unset($GLOBALS["gepiSettings"][$_name]);
		return (TRUE);
    } else return (FALSE);
}

?>
