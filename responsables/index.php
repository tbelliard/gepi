<?php
/*
 * Last modification  : 04/10/2006
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
$titre_page = "Gestion des responsables élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
echo "<p class=bold>";
if ($_SESSION['statut'] == 'administrateur')
    echo "|<a href=\"../accueil_admin.php\">Retour</a>";
else
    echo "|<a href=\"../accueil.php\">Retour</a>";
echo "|<a href=\"modify_resp.php\">Ajouter un responsable</a>|</p>";

if (!isset($order_by)) {$order_by = "nom1,prenom1";}
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
$call_resp = mysql_query("SELECT * FROM responsables ORDER BY $order_by");
$nombreligne = mysql_num_rows($call_resp);
// si la table des responsables est non vide :
if ($nombreligne != 0) {
    echo "<table width = '100%' border= '1' cellpadding = '5'><tr>";

    echo "<td><p class='bold'><a href='index.php?order_by=nom1,prenom1'>Nom et prénom du responsable principal</a></p></td>";
    echo "<td><p class='bold'><a href='index.php?order_by=adr1'>Adresse</a></p></td>";
    echo "<td><p class='bold'><a href='index.php?order_by=nom2,prenom2'>Nom et prénom du deuxième responsable</a></p></td>";
    echo "<td><p class='bold'><a href='index.php?order_by=adr2'>Adresse</a></p></td>";
    echo "<td><p class='bold'>Nom prénom de l'élève</p></td>";
    if ($_SESSION['statut'] == 'administrateur')
        echo "<td><p class='bold'>Supprimer</p></td>";
    echo "</tr>";
    $i = 0;
    while ($i < $nombreligne){
        $ereno = mysql_result($call_resp , $i, "ereno");
        $nom1 = mysql_result($call_resp , $i, "nom1");
        $prenom1 = mysql_result($call_resp , $i, "prenom1");
        $adr1 = mysql_result($call_resp , $i, "adr1");
        $adr1_comp = mysql_result($call_resp , $i, "adr1_comp");
        $commune1 = mysql_result($call_resp , $i, "commune1");
        $cp1 = mysql_result($call_resp , $i, "cp1");
        $nom2 = mysql_result($call_resp , $i, "nom2");
        $prenom2 = mysql_result($call_resp , $i, "prenom2");
        $adr2 = mysql_result($call_resp , $i, "adr2");
        $adr2_comp =  mysql_result($call_resp , $i, "adr2_comp");
        $commune2 = mysql_result($call_resp , $i, "commune2");
        $cp2 = mysql_result($call_resp , $i, "cp2");
        if ($nom2 == '') $nom2 = "&nbsp;";
        if ($prenom2 == '') $prenom2 = "&nbsp;";
        if ($adr2_comp == '') $adr2_comp = "&nbsp;";
        if ($commune2 == '') $commune2 = "&nbsp;";
        if ($cp2 == '') $cp2 = "&nbsp;";

        echo "<tr><td><a href='modify_resp.php?ereno=".$ereno."'>".$nom1." ".$prenom1."</a></td>";
        echo "<td>".$adr1." ".$adr1_comp." ".$cp1.", ".$commune1."</td>";
        echo "<td>".$nom2." ".$prenom2."</td>";
        echo "<td>";
        if ($adr2 != '')
            echo $adr2." ".$adr2_comp." ".$cp2.", ".$commune2;
        else
            echo "&nbsp;";
        echo "&nbsp;</td>";

        echo "<td>";
        $call_eleves = mysql_query("SELECT * FROM eleves WHERE ereno='$ereno'");
        $nombreeleves = mysql_num_rows($call_eleves);
        $j = 0;
        $aucun = 'y';
        while ($j < $nombreeleves){
            $eleve_nom = mysql_result($call_eleves, $j, "nom");
            $eleve_prenom = mysql_result($call_eleves, $j, "prenom");
            if ($j > 0) echo "<br />";
            //echo $eleve_prenom." ".$eleve_nom;
            echo $eleve_nom." ".$eleve_prenom;
            $aucun = 'n';
            $j++;
        }
        if ($aucun == 'y') echo "<font color=\"#FF0000\">(pas d'élève associé)</font>";
        echo "</td>";
        if ($_SESSION['statut'] == 'administrateur')
            echo "<td><a href='../lib/confirm_query.php?liste_cible=$ereno&amp;action=del_resp'>Supprimer</a></td>";
        echo "</tr>\n";
    $i++;
    }
    echo "</table>\n";
} else {
    echo "<p class='grand'>Actuellement, aucun responsable dans la base.</p>\n";
}
echo "</body></html>\n";