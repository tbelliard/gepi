<?php
/*
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
 */

$niveau_arbo=0;

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
};

if($_SESSION['statut']!='administrateur') {
    header("Location: ./logout.php?auto=1");
    die();
}


//**************** EN-TETE *********************
$titre_page = "Page de test";
require_once("./lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p>Cette page est destinée à faire des tests de révision.</p>\n";

echo "<p>La variable '\$revision_svn' déclarée dans cette page à la valeur suivante: <span style='color:green;'>$revision_svn</span></p>\n";

require ("./lib/footer.inc.php");
?>
