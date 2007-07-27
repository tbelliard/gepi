<?php
/*
 * Last modification  : 31/12/2006
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Mise à jour eleves/responsables";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
echo "<a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

echo "<p>Vous pouvez effectuer les mises à jour de deux façons:</p>\n";
echo "<ul>\n";
echo "<li><a href='maj_import1.php'>Méthode 1</a>: En générant des fichiers CSV à partir des fichiers XML de Sconet/STS.</li>\n";
echo "<li><a href='maj_import2.php'>Méthode 2</a>: Nouvelle méthode, en fournissant directement les fichiers XML de Sconet/STS.</li>\n";
echo "</ul>\n";

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
