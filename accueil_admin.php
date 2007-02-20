<?php
/*
 * Last modification  : 11/03/2005
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
    header("Location:utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
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
        echo "<tr>";
        //echo "<td width='30%'><a href=$temp>$titre_</a></span>";
        echo "<td width='30%'><a href=$temp>$titre_</a>";
        echo"</td>";
        echo "<td>$expli_</td>";
        echo "</tr>";
    }
}

if (!checkAccess()) {
    header("Location: ./logout.php?auto=1");
    die();
}

$niveau_arbo = 0;
$titre_page = "Accueil - Administration des bases";

require_once("./lib/header.inc");

?>

<?php if (isset($msg)) { echo "<font color='red' size='2'>$msg</font>"; }

echo "<center>";



$chemin = array(

"/etablissements/index.php",

"/matieres/index.php",

"/utilisateurs/index.php",

"/eleves/index.php",

"/responsables/index.php",

"/classes/index.php",

//"/groupes/index.php",

"/aid/index.php"

);



$titre = array(

"Gestion des établissements",

"Gestion des matières",

"Gestion des utilisateurs",

"Gestion des élèves",

"Gestion des responsables élèves",

"Gestion des classes",

//"Gestion des groupes",

"Gestion des AID"

);



$expli = array(

"Définir, modifier, supprimer des établissements de la base de données.",

"Définir, modifier, supprimer des matières de la base de données.",

"Définir, modifier, supprimer les comptes utilisateurs.",

"Définir, modifier, supprimer les élèves.",

"Définir, modifier, supprimer les responsables élèves.",

"Définir, modifier, supprimer les classes.

<br />Gérer les paramètres des classes : périodes, coefficients, affichage du rang, ...

<br />Affecter les matières et les professeurs aux classes.

<br />Affecter les élèves aux classes.

<br />Affecter les professeurs principaux, les CPE, modifier le régime et la mention \"redoublant\".

<br />Modifier les matières suivies par les élèves.

<br />Modifier des paramètres du bulletin.",

//"Définir, modifier, supprimer les groupes d'enseignement",



"Définir, modifier, supprimer des AID (Activités Inter-Disciplinaires).

<br />Affecter les professeurs et les élèves."

);



$nb_ligne = count($chemin);

//

// Outils d'administration

//

$affiche = 'no';

for ($i=0;$i<$nb_ligne;$i++) {

    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}

}

if ($affiche=='yes') {

    //echo "<table width=750 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table width='750' class='bordercolor'>";

    echo "<tr>";

    echo "<td width='30%'>&nbsp;</td>";

    echo "<td><b>Administration des bases</b></td>";

    echo "</tr>";

    for ($i=0;$i<$nb_ligne;$i++) {

        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut']);

    }

    echo "</table>";

}



?>

</center>

</body>

</html>