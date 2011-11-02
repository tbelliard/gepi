<?php

/*
 *
 * @version $Id: liste_eleves.php 6810 2011-04-20 19:26:12Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( '/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister_des_eleves', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister des élèves', '');";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// La page est destinée à être appelée via Ajax.
// Il n'y a donc ni entête HTML, ni pied de page.

header('Content-type: text/html; charset=iso-8859-1');

//include("recherche_eleve.php");
include("../eleves/recherche_eleve.php");
?>
