<?php

/**
 * Fichier de traitement des demandes en ajax pour les aid
 *
 * @version $Id$
 *
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