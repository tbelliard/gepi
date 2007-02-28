<?php
@set_time_limit(0);
/*
* Last modification  : 15/09/2006
*
* Copyright 2001, 2006 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$liste_tables_del = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
//"aid_config",
"avis_conseil_classe",
//"classes",
//"droits",
"eleves",
"responsables",
//"etablissements",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_eleves_classes",
"j_eleves_etablissements",
"j_eleves_professeurs",
"j_eleves_regime",
"j_eleves_groupes",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);




//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des élèves - Etape 1";
require_once("../lib/header.inc");
//************** FIN EN-TETE ***************
?>
<p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Première phase d'initialisation<br />Importation des élèves</h3></center>";


if (!isset($_POST["action"])) {
    //
    // On sélectionne le fichier à importer
    //

    echo "<p>Vous allez effectuer la première étape : elle consiste à importer le fichier <b>g_eleves.csv</b> contenant les données élèves.";
    echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : ";
    echo "<ul><li>Nom</li>" .
            "<li>Prénom</li>" .
            "<li>Date de naissance au format JJ/MM/AAAA</li>" .
            "<li>n° identifiant interne à l'établissement (indispensable : c'est ce numéro qui est utilisé pour faire la liaison lors des autres importations)</li>" .
            "<li>n° identifiant national</li>" .
            "<li>Code établissement précédent</li>" .
            "<li>Doublement (OUI ou NON)</li>" .
            "<li>Régime (INTERN ou EXTERN ou IN.EX. ou DP DAN)</li>" .
            "<li>Sexe (F ou M)</li>" .
            "</ul>";
    echo "<p>Veuillez préciser le nom complet du fichier <b>g_eleves.csv</b>.";
    echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>";
    echo "<input type='hidden' name='action' value='upload_file' />";
    echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />";
    echo "<p><input type='submit' value='Valider' />";
    echo "</form>";

} else {
    //
    // Quelque chose a été posté
    //
    if ($_POST['action'] == "save_data") {
        //
        // On enregistre les données dans la base.
        // Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
        //

        // Première étape : on vide les tables

        $j=0;
        while ($j < count($liste_tables_del)) {
            if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
            }
            $j++;
        }

        $i = 0;
        // Compteur d'erreurs
        $error = 0;
        // Compteur d'enregistrement
        $total = 0;
        while (true) {

            $reg_nom = $_POST["ligne".$i."_nom"];
            $reg_prenom = $_POST["ligne".$i."_prenom"];
            $reg_naissance = $_POST["ligne".$i."_naissance"];
            $reg_id_int = $_POST["ligne".$i."_id_int"];
            $reg_id_nat = $_POST["ligne".$i."_id_nat"];
            $reg_etab_prec = $_POST["ligne".$i."_etab_prec"];
            $reg_double = $_POST["ligne".$i."_doublement"];
            $reg_regime = $_POST["ligne".$i."_regime"];
            $reg_sexe = $_POST["ligne".$i."_sexe"];

            // On nettoie et on vérifie :
            //$reg_nom = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($reg_nom)));
            $reg_nom = preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtoupper($reg_nom)));
            if (strlen($reg_nom) > 50) $reg_nom = substr($reg_nom, 0, 50);
            //$reg_prenom = preg_replace("/[^A-Za-z .\-éèüëïäê]/","",trim($reg_prenom));
            $reg_prenom = preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($reg_prenom));
            if (strlen($reg_prenom) > 50) $reg_prenom = substr($reg_prenom, 0, 50);
            $naissance = explode("/", $reg_naissance);
            if (!preg_match("/[0-9]/", $naissance[0]) OR strlen($naissance[0]) > 2 OR strlen($naissance[0]) == 0) $naissance[0] = "00";
            if (strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

            if (!preg_match("/[0-9]/", $naissance[1]) OR strlen($naissance[1] OR strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
            if (strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

            if (!preg_match("/[0-9]/", $naissance[2]) OR strlen($naissance[2]) > 4 OR strlen($naissance[2]) == 3 OR strlen($naissance[2]) == 1) $naissance[2] = "00";
            if (strlen($naissance[2]) == 1) $naissance[2] = "0" . $naissance[2];

            //$reg_naissance = mktime(0, 0, 0, $naissance[1], $naissance[0], $naissance[2]);
            $reg_naissance = $naissance[2] . "-" . $naissance[1] . "-" . $naissance[0];
            $reg_id_int = preg_replace("/[^0-9]/","",trim($reg_id_int));

            $reg_id_nat = preg_replace("/[^A-Z0-9]/","",trim($reg_id_nat));

            $reg_etab_prec = preg_replace("/[^A-Z0-9]/","",trim($reg_etab_prec));

            $reg_double = trim(strtoupper($reg_double));
            if ($reg_double != "OUI" AND $reg_double != "NON") $reg_double = "NON";


            $reg_regime = trim(strtoupper($reg_regime));
            if ($reg_regime != "INTERN" AND $reg_regime != "EXTERN" AND $reg_regime != "IN.EX." AND $reg_regime != "DP DAN") $reg_regime = "DP DAN";

            if ($reg_sexe != "F" AND $reg_sexe != "M") $reg_sexe = "F";

            // Maintenant que tout est propre, on fait un test sur la table eleves pour s'assurer que l'élève n'existe pas déjà.
            // Ca permettra d'éviter d'enregistrer des élèves en double

            $test = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE elenoet = '" . $reg_id_int . "'"), 0);

            if ($test == 0) {
                // Test négatif : aucun élève avec cet ID... on enregistre !

                // On génère un login
                $reg_login = preg_replace("/\040/","_", $reg_nom);
                //====================================
                // AJOUT: boireaus
                $reg_login = strtr($reg_login,"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
                //====================================
                $reg_login = preg_replace("/[^a-zA-Z]/", "", $reg_login);
                if (strlen($reg_login) > 9) $reg_login = substr($reg_login, 0, 9);
                //====================================
                // MODIF: boireaus
                //$reg_login .= "_" . substr($reg_prenom, 0, 1);
                $reg_login .= "_" . strtr(substr($reg_prenom, 0, 1),"àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ","aaaeeeeiioouuucAAAEEEEIIOOUUUC");
                //====================================
                $reg_login = strtoupper($reg_login);

                $p = 1;
                while (true) {
                    $test_login = mysql_result(mysql_query("SELECT count(login) FROM eleves WHERE login = '" . $reg_login . "'"), 0);
                    if ($test_login != 0) {
                        $reg_login .= strtr(substr($reg_prenom, $p, 1), "àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ", "aaaeeeeiioouuucAAAEEEEIIOOUUUC");
                        $p++;
                    } else {
                        break 1;
                    }
                    $reg_login = strtoupper($reg_login);
                }

                // Normalement on a maintenant un login dont on est sûr qu'il est unique...

                // On insert les données

                $insert = mysql_query("INSERT INTO eleves SET " .
                        "no_gep = '" . $reg_id_nat . "', " .
                        "login = '" . $reg_login . "', " .
                        "nom = '" . $reg_nom . "', " .
                        "prenom = '" . $reg_prenom . "', " .
                        "sexe = '" . $reg_sexe . "', " .
                        "naissance = '" . $reg_naissance . "', " .
                        "elenoet = '" . $reg_id_int . "', " .
                        "ereno = '" . $reg_id_int . "'");

                if (!$insert) {
                    $error++;
                    echo mysql_error();
                } else {
                    $total++;

                    // On enregistre l'établissement d'origine, le régime, et si l'élève est redoublant
                    $insert2 = mysql_query("INSERT INTO j_eleves_etablissements SET id_eleve = '" . $reg_login . "', id_etablissement = '" . $reg_etab_prec . "'");

                    if (!$insert2) {
                        $error++;
                        echo mysql_error();
                    }

                    if ($reg_double == "OUI") {
                        $reg_double = "R";
                    } else {
                        $reg_double = "-";
                    }

                    if ($reg_regime == "INTERN") {
                        $reg_regime = "int.";
                    } else if ($reg_regime == "EXTERN") {
                        $reg_regime = "ext.";
                    } else if ($reg_regime == "DP DAN") {
                        $reg_regime = "d/p";
                    } else if ($reg_regime == "IN.EX.") {
                        $reg_regime = "i-e";
                    }

                    $insert3 = mysql_query("INSERT INTO j_eleves_regime SET login = '" . $reg_login . "', doublant = '" . $reg_double . "', regime = '" . $reg_regime . "'");
                    if (!$insert3) {
                        $error++;
                        echo mysql_error();
                    }

                }

            }
            $i++;
            if (!isset($_POST['ligne'.$i.'_nom'])) break 1;
        }

        if ($error > 0) echo "<p><font color=red>Il y a eu " . $error . " erreurs.</font></p>";
        if ($total > 0) echo "<p>" . $total . " élèves ont été enregistrés.</p>";

        echo "<p><a href='index.php'>Revenir à la page précédente</a></p>";


    } else if ($_POST['action'] == "upload_file") {
        //
        // Le fichier vient d'être envoyé et doit être traité
        // On va donc afficher le contenu du fichier tel qu'il va être enregistré dans Gepi
        // en proposant des champs de saisie pour modifier les données si on le souhaite
        //

        $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

        // On vérifie le nom du fichier... Ce n'est pas fondamentalement indispensable, mais
        // autant forcer l'utilisateur à être rigoureux
        if(strtolower($csv_file['name']) == "g_eleves.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp=fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href='eleves.php'>Cliquer ici </a> pour recommencer !</center></p>";
            } else {

                // Fichier ouvert ! On attaque le traitement

                // On va stocker toutes les infos dans un tableau
                // Une ligne du CSV pour une entrée du tableau
                $data_tab = array();

                //=========================
                // On lit une ligne pour passer la ligne d'entête:
                $ligne = fgets($fp, 4096);
                //=========================

                    $k = 0;
                    $nat_num = array();
                    while (!feof($fp)) {
                        $ligne = fgets($fp, 4096);
                        if(trim($ligne)!="") {

                            $tabligne=explode(";",$ligne);

                            // 0 : Nom
                            // 1 : Prénom
                            // 2 : Date de naissance
                            // 3 : identifiant interne
                            // 4 : identifiant national
                            // 5 : établissement précédent
                            // 6 : Doublement (OUI || NON)
                            // 7 : Régime : INTERN || EXTERN || IN.EX. || DP DAN
                            // 8 : Sexe : F || M

                            // On nettoie et on vérifie :
                            //=====================================
                            // MODIF: boireaus
                            //$tabligne[0] = preg_replace("/[^A-Za-z .\-]/","",trim(strtoupper($tabligne[0])));
                            $tabligne[0] = preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim(strtoupper($tabligne[0])));
                            //=====================================
                            if (strlen($tabligne[0]) > 50) $tabligne[0] = substr($tabligne[0], 0, 50);

                            //=====================================
                            // MODIF: boireaus
                            //$tabligne[1] = preg_replace("/[^A-Za-z .\-éèüëïäê]/","",trim($tabligne[1]));
                            $tabligne[1] = preg_replace("/[^A-Za-z .\-àâäéèêëîïôöùûüçÀÄÂÉÈÊËÎÏÔÖÙÛÜÇ]/","",trim($tabligne[1]));
                            //=====================================
                            if (strlen($tabligne[1]) > 50) $tabligne[1] = substr($tabligne[1], 0, 50);

                            $naissance = explode("/", $tabligne[2]);
                            if (!preg_match("/[0-9]/", $naissance[0]) OR strlen($naissance[0]) > 2 OR strlen($naissance[0]) == 0) $naissance[0] = "00";
                            if (strlen($naissance[0]) == 1) $naissance[0] = "0" . $naissance[0];

                            if (!preg_match("/[0-9]/", $naissance[1]) OR strlen($naissance[1] OR strlen($naissance[1]) == 0) > 2) $naissance[1] = "00";
                            if (strlen($naissance[1]) == 1) $naissance[1] = "0" . $naissance[1];

                            if (!preg_match("/[0-9]/", $naissance[2]) OR strlen($naissance[2]) > 4 OR strlen($naissance[2]) == 3 OR strlen($naissance[2]) < 2) $naissance[2] = "0000";

                            $tabligne[2] = $naissance[0] . "/" . $naissance[1] . "/" . $naissance[2];

                            $tabligne[3] = preg_replace("/[^0-9]/","",trim($tabligne[3]));

                            $tabligne[4] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[4]));
                            $tabligne[4] = preg_replace("/\"/", "", $tabligne[4]);

                            $tabligne[5] = preg_replace("/[^A-Z0-9]/","",trim($tabligne[5]));
                            $tabligne[5] = preg_replace("/\"/", "", $tabligne[5]);

                            $tabligne[6] = trim(strtoupper($tabligne[6]));
                            $tabligne[6] = preg_replace("/\"/", "", $tabligne[6]);
                            if ($tabligne[6] != "OUI" AND $tabligne[6] != "NON") $tabligne[6] = "NON";


                            $tabligne[7] = trim(strtoupper($tabligne[7]));
                            $tabligne[7] = preg_replace("/\"/", "", $tabligne[7]);
                            if ($tabligne[7] != "INTERN" AND $tabligne[7] != "EXTERN" AND $tabligne[7] != "IN.EX." AND $tabligne[7] != "DP DAN") $tabligne[7] = "DP DAN";

                            $tabligne[8] = trim(strtoupper($tabligne[8]));
                            $tabligne[8] = preg_replace("/\"/", "", $tabligne[8]);
                            if ($tabligne[8] != "F" AND $tabligne[8] != "M") $tabligne[8] = "F";

                            if ($tabligne[4] != "" AND !in_array($tabligne[4], $nat_num)) {
                                $nat_num[] = $tabligne[4];
                                $data_tab[$k] = array();
                                $data_tab[$k]["nom"] = $tabligne[0];
                                $data_tab[$k]["prenom"] = $tabligne[1];
                                $data_tab[$k]["naissance"] = $tabligne[2];
                                $data_tab[$k]["id_int"] = $tabligne[3];
                                $data_tab[$k]["id_nat"] = $tabligne[4];
                                $data_tab[$k]["etab_prec"] = $tabligne[5];
                                $data_tab[$k]["doublement"] = $tabligne[6];
                                $data_tab[$k]["regime"] = $tabligne[7];
                                $data_tab[$k]["sexe"] = $tabligne[8];
                                // On incrémente pour le prochain enregistrement
                                $k++;
                            }
                        }
                    }

                fclose($fp);

                // Fin de l'analyse du fichier.
                // Maintenant on va afficher tout ça.

                echo "<form enctype='multipart/form-data' action='eleves.php' method='post'>";
                echo "<input type='hidden' name='action' value='save_data' />";
                //echo "<table>";
                //echo "<tr><td>Nom</td><td>Prénom</td><td>Sexe</td><td>Date de naissance</td><td>n° étab.</td><td>n° nat.</td><td>Code étab.</td><td>Double.</td><td>Régime</td></tr>";

                for ($i=0;$i<$k;$i++) {
                //  echo "<tr>";
                //  echo "<td>";
                //  echo $data_tab[$i]["nom"];
                    echo "<input type='hidden' name='ligne".$i."_nom' value='" . $data_tab[$i]["nom"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["prenom"];
                    echo "<input type='hidden' name='ligne".$i."_prenom' value='" . $data_tab[$i]["prenom"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["sexe"];
                    echo "<input type='hidden' name='ligne".$i."_sexe' value='" . $data_tab[$i]["sexe"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["naissance"];
                    echo "<input type='hidden' name='ligne".$i."_naissance' value='" . $data_tab[$i]["naissance"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["id_int"];
                    echo "<input type='hidden' name='ligne".$i."_id_int' value='" . $data_tab[$i]["id_int"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["id_nat"];
                    echo "<input type='hidden' name='ligne".$i."_id_nat' value='" . $data_tab[$i]["id_nat"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["etab_prec"];
                    echo "<input type='hidden' name='ligne".$i."_etab_prec' value='" . $data_tab[$i]["etab_prec"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["doublement"];
                    echo "<input type='hidden' name='ligne".$i."_doublement' value='" . $data_tab[$i]["doublement"] . "'>";
                //  echo "</td>";
                //  echo "<td>";
                //  echo $data_tab[$i]["regime"];
                    echo "<input type='hidden' name='ligne".$i."_regime' value='" . $data_tab[$i]["regime"] . "'>\n";
                //  echo "</td>";
                //  echo "</tr>";
                }

                //echo "</table>";
                echo "$k élèves ont été détectés dans le fichier.</br>";
                echo "<input type='submit' value='Enregistrer'>";

                echo "</form>";
            }

        } else if (trim($csv_file['name'])=='') {

            echo "<p>Aucun fichier n'a été sélectionné !<br />";
            echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</center></p>";

        } else {
            echo "<p>Le fichier sélectionné n'est pas valide !<br />";
            echo "<a href='eleves.php'>Cliquer ici </a> pour recommencer !</center></p>";
        }
    }
}
require("../lib/footer.inc.php");
?>