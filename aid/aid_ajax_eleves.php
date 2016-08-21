<?php

/**
 * Fichier de traitement des demandes en ajax pour les aid
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
 */

// Initialisation des variables

$id_eleve = isset($_POST["id_eleve"]) ? $_POST["id_eleve"] : NULL;
$id_aid = isset($_POST["id_aid"]) ? $_POST["id_aid"] : NULL;

if ($id_eleve = '') {
	// On quitte s'il n'y a pas d'élève
	exit('coucou 1');
//}
//elseif($id_aid = '') {
	// On quitte s'il n'y a pas d'aid
	//exit();
}
else {
	// Traitement des demandes
	// On insère le nouveau nom après avoir vérifier s'il est déjà membre de l'AID
	echo 'fraise';
}
?>