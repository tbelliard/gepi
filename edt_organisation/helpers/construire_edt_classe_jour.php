<?php
/**
 *
 * @version $Id: construire_edt_jour.php 4152 2010-03-21 23:32:16Z adminpaulbert $
 *
 * Copyright 2001, 2010 Pascal Fautrero
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

// ================================================
//
//          Fichier utilisé avec AJAX 
//          pour le fichier voir_absences_viescolaire.php
//
// ================================================

if (!isset($niveau_arbo)) {
	$niveau_arbo = 2;
}
if ($niveau_arbo == 0) {
	$CurrentPath = "./";
}
else {
	$CurrentPath = "";
	$countdown = $niveau_arbo;
	while ($countdown > 0) {
		$CurrentPath .= "../";
		$countdown--;
	}
}

// Initialisations files
require_once($CurrentPath."lib/initialisations.inc.php");
//mes fonctions
include($CurrentPath."edt_organisation/fonctions_calendrier.php");
include($CurrentPath."edt_organisation/fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
include($CurrentPath."edt_organisation/fonctions_edt_prof.php");       // --- edt prof
include($CurrentPath."edt_organisation/fonctions_edt_classe.php");     // --- edt classe
include($CurrentPath."edt_organisation/fonctions_edt_salle.php");      // --- edt salle
include($CurrentPath."edt_organisation/fonctions_edt_eleve.php");      // --- edt eleve
include($CurrentPath."edt_organisation/fonctions_affichage.php");
include($CurrentPath."edt_organisation/req_database.php");

$idClasse = isset($_GET["classe"]) ? $_GET["classe"] : NULL;
$jour = date("N")-1;
// =====================================
//
//	Récupérer le nom de la classe
//
// =====================================
function NomClasse($idClasse) {
    $result = "";
    $sql = "SELECT classe FROM classes WHERE id = '".$idClasse."' ";
    $req = mysql_query($sql);
    if ($req) {
        if ($rep = mysql_fetch_array($req)) {
            $result = $rep['classe'];
        }
    }
    return $result;
}

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
	die();
};
// =====================================
//
//	Construction de l'emploi du temps
//
// =====================================
	$tab_data = ConstruireEDTClasseDuJour($idClasse, 0, $jour);
	$flags = INFOBULLE + HORIZONTAL + CRENEAUX_VISIBLES;		// pour toutes les valeurs possibles, voir /edt_organisation/fonctions_affichage.php
	$tab_data['entete_creneaux'] = NomClasse($idClasse);
        //$tab_data[4]['extras'][0] = "Hello";  // Ajoute "Hello" Au premier emplacement(cours ou vide) pour vendredi
// =====================================
//
//	Affichage de l'emploi du temps
//
// =====================================

	echo EdtDuJour($tab_data, $jour, $flags);

?>