<?php
/*
 * Last modification  : 01/09/2005
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
//**************** EN-TETE *****************************
//$titre_page = "Gestion des utilisateurs | Réinitialisation des mots de passe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On appelle la lib utilisée pour la génération des mots de passe
include("randpass.php");

$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (login!='" . $_SESSION['login'] . "' and etat='actif')");

$nb_users = mysql_num_rows($call_user_info);
$p = 0;

while ($p < $nb_users) {

    $user_login = mysql_result($call_user_info, $p, "login");
    $user_nom = mysql_result($call_user_info, $p, "nom");
    $user_prenom = mysql_result($call_user_info, $p, "prenom");
    $user_password = mysql_result($call_user_info, $p, "password");
    $user_statut = mysql_result($call_user_info, $p, "statut");
    $user_email = mysql_result($call_user_info, $p, "email");


    // On réinitialise le mot de passe
    $new_password = pass_gen();
    $save_new_pass = mysql_query("UPDATE utilisateurs SET password='" . md5($new_password) . "', change_mdp = 'y' WHERE login='" . $user_login . "'");


    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
    while ($k < $nb_mat) {
        $user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
        $k++;
    }

    $call_data = mysql_query("SELECT * FROM classes");
    $nombre_classes = mysql_num_rows($call_data);
    $i = 0;
    while ($i < $nombre_classes){
        $classe[$i] = mysql_result($call_data, $i, "classe");
        $i++;
    }

    $impression = getSettingValue("Impression");

    echo "<p>A l'attention de  <span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span>";
    echo "<br />Nom de login : <span class = \"bold\">" . $user_login . "</span>";
    echo "<br />Mot de passe : <span class = \"bold\">" . $new_password . "</span>";
    echo "<br />Adresse E-mail : <span class = \"bold\">" . $user_email . "</span>";
    echo "</p>";
    echo $impression;
    echo "<p class=saut>&nbsp</p>";
    $p++;

}

echo "</body></html>";