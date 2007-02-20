<?php
/*
 * Last modification  : 03/12/2006
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

$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
};

$tab[0] = "administrateur";
$tab[1] = "professeur";
$tab[2] = "cpe";
$tab[3] = "scolarite";
$tab[4] = "eleve";
$tab[5] = "secours";

function acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}



function affiche_ligne($chemin_,$titre_,$expli_,$tab,$statut_) {
    if (acces($chemin_,$statut_)==1)  {
        $temp = substr($chemin_,1);
        echo "<tr>\n";
        //echo "<td width=30%><a href=$temp>$titre_</a></span>";
        echo "<td width='30%'><a href=$temp>$titre_</a>";
        echo "</td>\n";
        echo "<td>$expli_</td>\n";
        echo "</tr>\n";
    }
}
if (!checkAccess()) {
    header("Location: ./logout.php?auto=1");
die();
}
$titre_page = "Accueil - Administration des modules";
$racine_gepi = 'yes';
require_once("./lib/header.inc");
?>
<?php if (isset($msg)) { echo "<font color='red' size=2>$msg</font>"; }
echo "<center>";

$chemin = array(
"/cahier_texte_admin/index.php",
"/cahier_notes_admin/index.php");
if ($force_abs) $chemin[] = "/mod_absences/admin/index.php";
if ($force_msj) $chemin[] = "/mod_miseajour/admin/index.php";
$chemin[] = "/mod_trombinoscopes/trombinoscopes_admin.php";

$titre = array(
"Gestion du cahier de texte",
"Gestion des carnets de notes");
if ($force_abs) $titre[] = "Gestion des absences";
if ($force_msj) $titre[] = "Gestion des mise à jour";
$titre[] = "Gestion du trombinoscope";

$expli = array(
"Pour gérer les cahiers de texte, (configuration générale, ...)",
"Pour gérer les carnets de notes (configuration générale, ...)");
if ($force_abs) $expli[] = "Pour gérer le module absences";
if ($force_msj) $expli[] = "Pour gérer le module de mise à jour de GEPI";
$expli[] = "Pour gérer le module trombinoscope";

$nb_ligne = count($chemin);
//
// Outils d'administration
//
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table width='700' class='bordercolor'>\n";
    echo "<tr>\n";
    echo "<td width='30%'>&nbsp;</td>\n";
    echo "<td><b>Administration des modules</b></td>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);
    }
    echo "</table>\n";
}

?>
</center>
</body>
</html>
