<?php
/**
 *
 * @version $Id: include_requetes_filtre_de_recherche.php 7294 2011-06-22 15:06:01Z jjacquard $
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

if (!isset($_SESSION['filtre_recherche']) || $_SESSION['filtre_recherche'] == null) {
    $_SESSION['filtre_recherche'] = Array();
}
$reinit_filtre = isset($_POST["reinit_filtre"]) ? $_POST["reinit_filtre"] :(isset($_GET["reinit_filtre"]) ? $_GET["reinit_filtre"] :NULL);

if ($reinit_filtre == 'y') {
    $_SESSION['filtre_recherche'] = Array();
    $_SESSION['filtre_recherche']['order'] = 'des_id';
} else {
    $liste_parametres_sauf_checkbox = array('order', 'filter_notification_id', 'filter_traitement_id', 'filter_saisie_id', 'filter_utilisateur', 'filter_eleve', 'filter_classe', 'filter_groupe', 'filter_aid',
	'filter_type', 'filter_type_notification','filter_statut_notification','filter_motif', 'filter_justification', 'filter_date_debut_saisie_debut_plage', 'filter_date_debut_saisie_fin_plage',
	'filter_date_fin_saisie_debut_plage', 'filter_date_fin_saisie_fin_plage',
	'filter_creneau', 'filter_cours', 'filter_date_creation_traitement_debut_plage', 'filter_date_creation_traitement_fin_plage','filter_date_creation_saisie_debut_plage', 'filter_date_creation_saisie_fin_plage','filter_date_creation_notification_debut_plage', 'filter_date_creation_notification_fin_plage',
	'filter_date_traitement_absence_debut_plage', 'filter_date_traitement_absence_fin_plage', 'filter_statut', 'filter_manqement_obligation', 'filter_sous_responsabilite_etablissement'
	    , 'filter_recherche_saisie_a_rattacher', 'filter_regime', 'filter_date_suppression_saisie_debut_plage', 'filter_date_suppression_saisie_fin_plage');
    $liste_parametres_checkbox = array('filter_date_modification', 'filter_discipline', 'filter_marqueur_appel', 'filter_saisies_supprimees');

    //récupération des paramètres de la requète
    foreach ($liste_parametres_sauf_checkbox as $param_name) {
	//echo $param_name.' : '.$_POST[$param_name].'<br/>';
	if (isset($_POST[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_POST[$param_name];
	} else if (isset($_GET[$param_name])) {
	    $_SESSION['filtre_recherche'][$param_name] = $_GET[$param_name];
	}
    }

    //cas particulier pour les checkbox
    foreach ($liste_parametres_checkbox as $param_name) {
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
