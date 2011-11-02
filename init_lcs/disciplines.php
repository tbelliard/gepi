<?php
/*
 * $Id: disciplines.php 7858 2011-08-21 13:12:55Z crob $
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

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}


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

// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['is_posted'])) {
	check_token();

    // L'admin a validé la procédure, on procède donc...

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");

    // On commence par récupérer tous les profs depuis le LDAP
    $sr = ldap_search($ds,$lcs_ldap_base_dn,"(cn=Matiere_*)");
    $info = ldap_get_entries($ds,$sr);

    if ($_POST['record'] == "yes") {
        // Suppression des données présentes dans les tables en lien avec les matières

        $j=0;
        while ($j < count($liste_tables_del)) {
            if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
            }
            $j++;
        }

        $new_matieres = array();
        echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n";
        echo "<tr><td>Identifiant matière</td><td>Nom complet matière</td><td>identifiants prof.</td></tr>\n";
        for ($i=0;$i<$info["count"];$i++) {
            $matiere=preg_replace("/Matiere_/","",$info[$i]["cn"][0]);
            $get_matieres = mysql_query("SELECT matiere FROM matieres");
            $nbmat = mysql_num_rows($get_matieres);
            $matieres = array();
            for($j=0;$j<$nbmat;$j++) {
                $matieres[] = mysql_result($get_matieres, $j, "matiere");
            }

            if (!in_array($matiere, $matieres)) {
                $reg_matiere = mysql_query("INSERT INTO matieres SET matiere='".$matiere."',nom_complet='".html_entity_decode_all_version(stripslashes($_POST['reg_nom_complet'][$matiere]))."', priority='0',matiere_aid='n',matiere_atelier='n'");
            } else {
                $reg_matiere = mysql_query("UPDATE matieres SET nom_complet='".html_entity_decode_all_version(stripslashes($_POST['reg_nom_complet'][$matiere]))."' WHERE matiere = '" . $matiere . "'");
            }
            if (!$reg_matiere) echo "<p>Erreur lors de l'enregistrement de la matière $matiere.";
            $new_matieres[] = $matiere;

            // On regarde maintenant les affectations professeur/matière
            $list_member = "";
            if ($info[$i]["memberuid"]["count"] > 0) {
              for ( $u = 0; $u < $info[$i]["memberuid"]["count"] ; $u++ ) {
                $member = preg_replace ("/^uid=([^,]+),ou=.*/" , "\\1", $info[$i]["memberuid"][$u] );
                if (trim($member) !="") {
                    if ($list_member != "") $list_member .=", ";
                    $list_member .=$member;
                    $test = mysql_result(mysql_query("SELECT count(*) FROM j_professeurs_matieres WHERE (id_professeur = '" . $member . "' and id_matiere = '" . $matiere . "')"), 0);
                    if ($test == 0) {
                        $res = mysql_query("INSERT into j_professeurs_matieres SET id_professeur = '" . $member . "', id_matiere = '" . $matiere . "'");
                    }
                }
              }
            } else {
              for ( $u = 0; $u < $info[$i]["member"]["count"] ; $u++ ) {
                $member = preg_replace ("/^uid=([^,]+),ou=.*/" , "\\1", $info[$i]["member"][$u] );
                if (trim($member) !="") {
                    if ($list_member != "") $list_member .=", ";
                    $list_member .=$member;
                    $test = mysql_result(mysql_query("SELECT count(*) FROM j_professeurs_matieres WHERE (id_professeur = '" . $member . "' and id_matiere = '" . $matiere . "')"), 0);
                    if ($test == 0) {
                        $res = mysql_query("INSERT into j_professeurs_matieres SET id_professeur = '" . $member . "', id_matiere = '" . $matiere . "'");
                    }
                }
              }
            }
            echo "<tr><td>".$matiere."</td><td>".stripslashes($_POST['reg_nom_complet'][$matiere])."</td><td>".$list_member."</td></tr>\n";
        }
        // On efface les matières qui ne sont plus utilisées
        echo "</table>";
        $to_remove = array_diff($matieres, $new_matieres);

        foreach($to_remove as $delete) {
            $res = mysql_query("DELETE from matieres WHERE matiere = '" . $delete . "'");
            $res2 = mysql_query("DELETE from j_professeurs_matieres WHERE id_matiere = '" . $delete . "'");
        }

        echo "<p>Opération effectuée.</p>";
        echo "<p>Vous pouvez vérifier l'importation en allant sur la page de <a href='../matieres/index.php'>gestion des matières</a>.</p>";

    } elseif ($_POST['record'] == "no") {

            echo "<form action='disciplines.php' method='post' name='formulaire'>";
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
                $matiere=preg_replace("/Matiere_/","",$info[$i]["cn"][0]);
                $description = $info[$i]["description"][0];
                $test_exist = mysql_query("SELECT * FROM matieres WHERE matiere='$matiere'");
                $nb_test_matiere_exist = mysql_num_rows($test_exist);

                if ($nb_test_matiere_exist==0) {
                    $nom_complet = $description;
                    $nom_court = "<font color=red>".$matiere."</font>";
                } else {
                    $id_matiere = mysql_result($test_exist, 0, 'matiere');
                    $nom_court = "<font color=green>".$matiere."</font>";
                    $nom_complet = mysql_result($test_exist, 0, 'nom_complet');
                }
                echo "<tr>";
                echo "<td>";
                echo "<p><b><center>$nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=\"text\" size=\"40\" name='reg_nom_complet[$matiere]' value=\"".$nom_complet."\">\n";
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
    echo "<p>L'opération d'importation des matières depuis le LDAP de LCS va effectuer les opérations suivantes :</p>";
    echo "<ul>";
    echo "<li>Ajout ou mise à jour de chaque matières présente dans le LDAP</li>";
    echo "<li>Association professeurs <-> matières</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='disciplines.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<input type=hidden name='record' value='no'>";

    echo "<p>Etes-vous sûr de vouloir importer toutes les matières depuis l'annuaire du serveur LCS vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>