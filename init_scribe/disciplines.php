<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include("../lib/initialisation_annee.inc.php");
$liste_tables_del = $liste_tables_del_etape_matieres;


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['is_posted'])) {
	check_token(false);
    // L'admin a validé la procédure, on procède donc...
    include "../lib/eole_sync_functions.inc.php";
    // On commence par récupérer toutes les matières depuis le LDAP
    $ldap_server = new LDAPServer;
    $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Matiere*)");
    $info = ldap_get_entries($ldap_server->ds,$sr);


    if ($_POST['record'] == "yes") {
        // Suppression des données présentes dans les tables en lien avec les matières
        /* NON! On ne fait qu'une mise à jour de la liste, le cas échéant...
        $j=0;
        while ($j < count($liste_tables_del)) {
            if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
            }
            $j++;
        }
        */

        $new_matieres = array();
        for ($i=0;$i<$info["count"];$i++) {

            $matiere = $info[$i]["cn"][0];
            $matiere = traitement_magic_quotes(corriger_caracteres(trim($matiere)));
            $matiere = preg_replace("/[^A-Za-z0-9.\-]/","",strtoupper($matiere));
            $get_matieres = mysqli_query($GLOBALS["mysqli"], "SELECT matiere FROM matieres");
            $nbmat = mysqli_num_rows($get_matieres);
            $matieres = array();
            for($j=0;$j<$nbmat;$j++) {
                $matieres[] = mysql_result($get_matieres, $j, "matiere");
            }

            if (!in_array($matiere, $matieres)) {
                $reg_matiere = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres SET matiere='".$matiere."',nom_complet='".($_POST['reg_nom_complet'][$matiere])."', priority='11',matiere_aid='n',matiere_atelier='n'");
            } else {
                $reg_matiere = mysqli_query($GLOBALS["mysqli"], "UPDATE matieres SET nom_complet='".($_POST['reg_nom_complet'][$matiere])."' WHERE matiere = '" . $matiere . "'");
            }
            if (!$reg_matiere) echo "<p>Erreur lors de l'enregistrement de la matière $matiere.";
            $new_matieres[] = $matiere;

            // On regarde maintenant les affectations professeur/matière
            for($k=0;$k<$info[$i]["memberuid"]["count"];$k++) {
                $member = $info[$i]["memberuid"][$k];
                $test = mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_professeurs_matieres WHERE (id_professeur = '" . $member . "' and id_matiere = '" . $matiere . "')"), 0);
                if ($test == 0) {
                    $res = mysqli_query($GLOBALS["mysqli"], "INSERT into j_professeurs_matieres SET id_professeur = '" . $member . "', id_matiere = '" . $matiere . "'");
                }
            }

        }

        // On efface les matières qui ne sont plus utilisées

        $to_remove = array_diff($matieres, $new_matieres);

        foreach($to_remove as $delete) {
            $res = mysqli_query($GLOBALS["mysqli"], "DELETE from matieres WHERE matiere = '" . $delete . "'");
            $res2 = mysqli_query($GLOBALS["mysqli"], "DELETE from j_professeurs_matieres WHERE id_matiere = '" . $delete . "'");
        }

        echo "<p>Opération effectuée.</p>";
        echo "<p>Vous pouvez vérifier l'importation en allant sur la page de <a href='../matieres/index.php'>gestion des matières</a>.</p>";

    } elseif ($_POST['record'] == "no") {

            echo "<form enctype='multipart/form-data' action='disciplines.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='is_posted' value='yes'>";

            echo "<p>Les matières en vert indiquent des matières déjà existantes dans la base GEPI.<br />Les matières en rouge indiquent des matières nouvelles et qui vont être ajoutées à la base GEPI.<br /></p>";
            echo "<p>Attention !!! Il n'y a pas de tests sur les champs entrés. Soyez vigilant à ne pas mettre des caractères spéciaux dans les champs ...</p>";
            echo "<p>Essayez de remplir tous les champs, cela évitera d'avoir à le faire ultérieurement.</p>";
            echo "<p>N'oubliez pas <b>d'enregistrer les données</b> en cliquant sur le bouton en bas de la page<br /><br />";
            echo "<br/>";
            echo "<center>";
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\">Identifiant de la matière</p></td><td><p class=\"small\">Nom complet</p></td></tr>";
            for ($i=0;$i<$info["count"];$i++) {
                $matiere = $info[$i]["cn"][0];
                $matiere = traitement_magic_quotes(corriger_caracteres(trim($matiere)));
                $nom_court = preg_replace("/[^A-Za-z0-9.\-]/","",strtoupper($matiere));
                $nom_long = htmlspecialchars($matiere);
                $test_exist = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM matieres WHERE matiere='$nom_court'");
                $nb_test_matiere_exist = mysqli_num_rows($test_exist);

                if ($nb_test_matiere_exist==0) {
                    $disp_nom_court = "<font color=red>".$nom_court."</font>";
                } else {
                    $disp_nom_court = "<font color=green>".$nom_court."</font>";
                }
                echo "<tr>";
                echo "<td>";
                echo "<p><b><center>$disp_nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$nom_court]' value=\"".$nom_long."\">\n";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "</center>";
            echo "<center><input type='submit' value='Enregistrer les données'></center>\n";
            echo "</form>\n";
    }

} else {

    echo "<p><b>ATTENTION ...</b><br />";
    echo "<p>Si vous poursuivez la procédure les données telles que notes, appréciations, ... seront effacées.</p>";
    echo "<p>Seules la table contenant les matières et la table mettant en relation les matières et les professeurs seront conservées.</p>";
    echo "<p>L'opération d'importation des matières depuis le LDAP de Scribe va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Ajout ou mise à jour de chaque matières présente dans le LDAP</li>";
    echo "<li>Association professeurs <-> matières</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='disciplines.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Etes-vous sûr de vouloir continuer ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>