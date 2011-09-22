<?php
/**
 *
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

$eleve_login = isset($_POST["eleve_login"]) ? $_POST["eleve_login"] :(isset($_GET["eleve_login"]) ? $_GET["eleve_login"] : NULL);

if ($eleve_login == null) {
    echo 'Erreur : eleve_login est null';
    die;
}

require_once("../edt_organisation/fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
require_once("../edt_organisation/fonctions_edt_eleve.php");      // --- edt eleve
require_once("../edt_organisation/fonctions_affichage.php");
require_once("../edt_organisation/req_database.php");
$tab_data = ConstruireEDTEleve($eleve_login , 0);
$entetes = ConstruireEnteteEDT();
$creneaux = ConstruireCreneauxEDT();
AfficherEDT($tab_data, $entetes, $creneaux, "eleve", $eleve_login , 0);

?>
