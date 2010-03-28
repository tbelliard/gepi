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
//
// ================================================


$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../../edt_organisation/fonctions_calendrier.php");
include("../../edt_organisation/fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
include("../../edt_organisation/fonctions_edt_prof.php");       // --- edt prof
include("../../edt_organisation/fonctions_edt_classe.php");     // --- edt classe
include("../../edt_organisation/fonctions_edt_salle.php");      // --- edt salle
include("../../edt_organisation/fonctions_edt_eleve.php");      // --- edt eleve
include("../../edt_organisation/fonctions_affichage.php");
include("../../edt_organisation/req_database.php");

$loginProf = isset($_GET["login"]) ? $_GET["login"] : NULL;
$jour = date("N")-1;

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
	die();
};

$tab_data = ConstruireEDTProfDuJour($loginprof, 0, $jour);
echo EdtDuJourVertical($tab_data, $jour);

?>