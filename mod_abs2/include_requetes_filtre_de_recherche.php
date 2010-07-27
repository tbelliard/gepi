<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($_SESSION['filtre_recherche']) || $_SESSION['filtre_recherche'] == null) {
    $_SESSION['filtre_recherche'] = Array();
}
$reinit_filtre = isset($_POST["reinit_filtre"]) ? $_POST["reinit_filtre"] :(isset($_GET["reinit_filtre"]) ? $_GET["reinit_filtre"] :NULL);

if ($reinit_filtre == 'y') {
    $_SESSION['filtre_recherche'] = Array();
    $_SESSION['filtre_recherche']['order'] = 'des_id';
} else {
    $liste_parametres_sauf_chechbox = array('order', 'filter_notification_id', 'filter_traitement_id', 'filter_saisie_id', 'filter_utilisateur', 'filter_eleve', 'filter_classe', 'filter_groupe', 'filter_aid',
	'filter_type', 'filter_type_notification','filter_statut_notification', 'filter_justification', 'filter_date_debut_absence_debut_plage', 'filter_date_debut_absence_fin_plage', 'filter_date_fin_absence_debut_plage',
	'filter_date_fin_absence_fin_plage', 'filter_creneau', 'filter_cours', 'filter_date_creation_traitement_debut_plage', 'filter_date_creation_traitement_fin_plage',
	'filter_date_traitement_absence_debut_plage', 'filter_date_traitement_absence_fin_plage', 'filter_statut');
    //récupération des paramètres de la requète
    foreach ($liste_parametres_sauf_chechbox as $param_name) {
	if (isset($_POST[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_POST[$param_name];
	} else if (isset($_GET[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_GET[$param_name];
	}
    }

    //cas particulier pour les checkbox
    $liste_parametres_sauf_chechbox = array('date_modification', 'discipline');
    foreach ($liste_parametres_sauf_chechbox as $param_name) {
	if (isset($_POST[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_POST[$param_name];
	} elseif (isset($_GET[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_GET[$param_name];
	} elseif (isset($_POST["filter_checkbox_posted"]) || isset($_GET["filter_checkbox_posted"])) {
	    $_SESSION['filtre_recherche'][$param_name] = null;
	}
    }

    if (!isFiltreRechercheParam('order')) {
	$_SESSION['filtre_recherche']['order'] = 'des_id';
    }
}

function isFiltreRechercheParam($param_name) {
    if (isset($_SESSION['filtre_recherche'][$param_name])
	&& $_SESSION['filtre_recherche'][$param_name] != null
	&& $_SESSION['filtre_recherche'][$param_name] != '') {
	return true;
    } else {
	return false;
    }
}

function getFiltreRechercheParam($param_name) {
    if (isset($_SESSION['filtre_recherche'][$param_name])
	&& $_SESSION['filtre_recherche'][$param_name] != null
	&& $_SESSION['filtre_recherche'][$param_name] != '') {
	return $_SESSION['filtre_recherche'][$param_name];
    } else {
	return null;
    }
}
?>
