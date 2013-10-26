<?php

/** Manipulation de la table setting
 * 
 * 
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin
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
    global $mysqli;
    global $gepiSettings;
    $sql = "SELECT name, value FROM setting";
    
    $resultat = mysqli_query($mysqli, $sql);
    if (!$resultat) return (FALSE);
    if ($resultat->num_rows == 0) return (FALSE);
    
    while($donnees = mysqli_fetch_assoc($resultat))
    {
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


?>
