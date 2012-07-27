<?php
/*
 * Last modification  : 04/04/2005
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

};




if (!checkAccess()) {

    header("Location: ../logout.php?auto=1");

die();

}

//**************** EN-TETE *****************
$titre_page = "Aide en ligne";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<p>Le tableau suivant indique quelles fonctions de GEPI sont accessibles aux utilisateurs connectés selon leur statut (administrateur, professeur, C.P.E., scolarité, ou secours).";
echo "<br />Le symbole * signale que l'accès à la fonction est configurable.<br /><br />";
echo "<b>Astuce</b> : Le statut visiteur n'existe pas dans GEPI. Mais on obtient un résultat équivalent en affectant à un utilisateur le statut professeur sans affectation de matières ni de classe.<br /><br /></p>";
if (!isset($_GET['order_by'])) $order_by = "description"; else $order_by = $_GET['order_by']." ".$_GET['order'];
echo "<table cellpadding=2 cellspacing=0 border=0><tr><td><b><a href='help.php?order_by=description&order='>Description</a></b></td><td><b><a href='help.php?order_by=administrateur&order=DESC'>Administrateur</a></b></td><td><b><a href='help.php?order_by=professeur&order=DESC'>Professeur</a></b></td><td><b><a href='help.php?order_by=cpe&order=DESC'>C.P.E.</a></b></td><td><b><a href='help.php?order_by=scolarite&order=DESC'>Scolarité</a></b></td><td><b><a href='help.php?order_by=secours&order=DESC'>Secours</a></b></td></tr>";
$sql = "select distinct description, administrateur, professeur, cpe, scolarite, secours, statut  from droits where description != '' group by description order by ".$order_by."";
$flag = 1;
$res = sql_query($sql);
for ($i = 0; ($row = sql_row($res, $i)); $i++) {
    echo "<tr ";
    if ($row[6] == "1") $etoile = " *"; else $etoile = "";
    if ($flag==1) { echo " class='fond_sombre'"; $flag = 0;} else {$flag=1;};
    echo "><td>".$row[0]."</td><td>";
    if ($row[1] == "V") echo "<font color='green'>Oui".$etoile."</font>"; else echo "<font color='red'>Non</font>";
    echo "</td><td>";
    if ($row[2] == "V") echo "<font color='green'>Oui".$etoile."</font>"; else echo "<font color='red'>Non</font>";
    echo "</td><td>";
    if ($row[3] == "V") echo "<font color='green'>Oui".$etoile."</font>"; else echo "<font color='red'>Non</font>";
    echo "</td><td>";
    if ($row[4] == "V") echo "<font color='green'>Oui".$etoile."</font>"; else echo "<font color='red'>Non</font>";
    echo "</td><td>";
    if ($row[5] == "V") echo "<font color='green'>Oui".$etoile."</font>"; else echo "<font color='red'>Non</font>";
    echo "</td></tr>";
}
echo "</table>";
require("../lib/footer.inc.php");
?>