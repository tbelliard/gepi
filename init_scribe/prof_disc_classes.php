<?php
@set_time_limit(0);
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
$liste_tables_del = $liste_tables_del_etape_professeurs;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation des matières";
require_once("../lib/header.inc.php");
//************** FIN EN-TETE ***************
?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil initialisation</a></p>
<?php

echo "<center><h3 class='gepi'>Quatrième phase d'initialisation<br />Importation des associations profs-matières-classes (enseignements)</h3></center>";


if (!isset($_POST["action"])) {
    //
    // On sélectionne le fichier à importer
    //

    echo "<p>Vous allez effectuer la quatrième étape : elle consiste à importer le fichier <b>prof_disc_classes.csv</b> contenant les données relatives aux enseignements.";
    echo "<p>ATTENTION ! Avec cette opération, vous effacez tous les groupes d'enseignement qui avaient été définis l'année dernière. Ils seront écrasés par ceux que vous allez importer avec la procédure courante.</p>";
    echo "<p>Les champs suivants doivent être présents, dans l'ordre, et <b>séparés par un point-virgule</b> : ";
    echo "<ul><li>Login du professeur</li>" .
            "<li>Nom court de la matière</li>" .
            "<li>Le ou les identifiant(s) de classe (séparés par un point d'exclamation ; ex : 1S1!1S2)</li>" .
            "<li>Type d'enseignement (CG pour enseignement général suivi par toute la classe, OPT pour un enseignement optionnel)</li>" .
            "</ul>";
    echo "<p>Exemple de ligne pour un enseignement général :<br/>" .
            "DUPONT.JEAN;MATHS;1S1;CG<br/>" .
            "Exemple de ligne pour un enseignement optionnel avec des élèves de plusieurs classes :<br/>" .
            "DURANT.PATRICE;ANGL2;1S1!1S2!1S3;OPT</p>";
    echo "<p>Veuillez préciser le nom complet du fichier <b>prof_disc_classes.csv</b>.";
    echo "<form enctype='multipart/form-data' action='prof_disc_classes.php' method='post'>";
	echo add_token_field();
    echo "<input type='hidden' name='action' value='upload_file' />";
    echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" />";
    echo "<p><input type='submit' value='Valider' />";
    echo "</form>";

} else {
	check_token(false);
    //
    // Quelque chose a été posté
    //
    if ($_POST['action'] == "save_data") {
        //
        // On enregistre les données dans la base.
        // Le fichier a déjà été affiché, et l'utilisateur est sûr de vouloir enregistrer
        //

        $j=0;
        while ($j < count($liste_tables_del)) {
            if (old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                $del = @mysqli_query($GLOBALS["mysqli"], "DELETE FROM $liste_tables_del[$j]");
            }
            $j++;
        }


        $go = true;
        $i = 0;
        // Compteur d'erreurs
        $error = 0;
        // Compteur d'enregistrement
        $total = 0;
        // Warning matière, si jamais une matière est créée à la volée
        $warning_matiere = false;
        while ($go) {

            $reg_prof = $_POST["ligne".$i."_prof"];
            $reg_matiere = $_POST["ligne".$i."_matiere"];
            $reg_classes = $_POST["ligne".$i."_classes"];
            $reg_type = $_POST["ligne".$i."_type"];

            // On nettoie et on vérifie :
            $reg_prof = preg_replace("/[^A-Za-z0-9\._]/","",trim(mb_strtoupper($reg_prof)));
            if (mb_strlen($reg_prof) > 50) $reg_prof = mb_substr($reg_prof, 0, 50);

            $reg_matiere = preg_replace("/[^A-Za-z0-9\.\-]/","",trim(mb_strtoupper($reg_matiere)));
            if (mb_strlen($reg_matiere) > 50) $reg_matiere = mb_substr($reg_matiere, 0, 50);

            $reg_classes = preg_replace("/[^A-Za-z0-9\.\-!]/","",trim($reg_classes));
            if (mb_strlen($reg_classes) > 2000) $reg_classes = mb_substr($reg_classes, 0, 2000); // C'est juste pour éviter une tentative d'overflow...

            // On ne garde véritablement que les types CG et OPT. En effet la génération par Scribe
            // est supposée n'intégrer que ces deux types.
            if ($reg_type != "CG" AND $reg_type != "OPT") $reg_type = false;

            if ($reg_type) {

                // Première étape : on s'assure que le prof existe. S'il n'existe pas, on laisse tomber.
                $test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(login) FROM utilisateurs WHERE login = '" . $reg_prof . "'"),0);
                if ($test == 1) {

                    // Le prof existe. cool. Maintenant on récupère la matière.
                    $test = mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres WHERE matiere = '" . $reg_matiere . "'");

                    if (mysqli_num_rows($test) == 0) {
                        // La matière n'existe pas, on la créé
                        $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO matieres SET matiere = '" . $reg_matiere . "', nom_complet = '" . $reg_matiere . "',priority='0',matiere_aid='n',matiere_atelier='n'");
                        $reg_matiere_complet = $reg_matiere;
                        $warning_matiere = true;
                    } else {
                        $reg_matiere_complet = old_mysql_result($test, 0, "nom_complet");
                    }

                    // Maintenant on en arrive aux classes
                    // On récupère un tableau :
                    $reg_classes = explode("!", $reg_classes);

                    // On détermine le type de groupe
                    if (count($reg_classes) > 1) {
                        // On force le type "OPT" s'il y a plusieurs classes
                        $reg_type = "OPT";
                    } else {
                        if ($reg_type == "") {
                            // Si on n'a qu'une seule classe et que rien n'est spécifié, on a par défaut
                            // un cours général
                            $reg_type = "CG";
                        }
                    }

                    // Si on arrive ici, c'est que normalement tout est bon.
                    // On va quand même s'assurer qu'on a des classes valides.

                    $valid_classes = array();
                    foreach ($reg_classes as $classe) {
                        $test = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM classes WHERE classe = '" . $classe . "'");
                        if (mysqli_num_rows($test) == 1) $valid_classes[] = old_mysql_result($test, 0, "id");
                    }

                    if (count($valid_classes) > 0) {
                        // C'est bon, on a au moins une classe valide. On peut créer le groupe !

                        $new_group = mysqli_query($GLOBALS["mysqli"], "INSERT INTO groupes SET name = '" . $reg_matiere . "', description = '" . $reg_matiere_complet . "'");
                        $group_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
                        if (!$new_group) echo mysqli_error($GLOBALS["mysqli"]);
                        // Le groupe est créé. On associe la matière.
                        $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_groupes_matieres SET id_groupe = '".$group_id."', id_matiere = '" . $reg_matiere . "'");
                        if (!$res) echo mysqli_error($GLOBALS["mysqli"]);
                        // On associe le prof
                        $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_groupes_professeurs SET id_groupe = '" . $group_id . "', login = '" . $reg_prof . "'");
                        if (!$res) echo mysqli_error($GLOBALS["mysqli"]);
                        // On associe la matière au prof
                        $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_professeurs_matieres SET id_professeur = '" . $reg_prof . "', id_matiere = '" . $reg_matiere . "'");
                        // On associe le groupe aux classes (ou à la classe)
                        foreach ($valid_classes as $classe_id) {
                            $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_groupes_classes SET id_groupe = '" . $group_id . "', id_classe = '" . $classe_id ."'");
                            if (!$res) echo mysqli_error($GLOBALS["mysqli"]);
                        }

                        // Si le type est à "CG", on associe les élèves de la classe au groupe
                        if ($reg_type == "CG") {

                            // On récupère le nombre de périodes pour la classe
                            $periods = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(num_periode) FROM periodes WHERE id_classe = '" . $valid_classes[0] . "'"), 0);
                            $get_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT(login) FROM j_eleves_classes WHERE id_classe = '" . $valid_classes[0] . "'");
                            $nb = mysqli_num_rows($get_eleves);
                            for ($e=0;$e<$nb;$e++) {
                                $current_eleve = old_mysql_result($get_eleves, $e, "login");
                                for ($p=1;$p<=$periods;$p++) {
                                    $res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_groupes SET login = '" . $current_eleve . "', id_groupe = '" . $group_id . "', periode = '" . $p . "'");
                                    if (!$res) echo mysqli_error($GLOBALS["mysqli"]);
                                }
                            }
                        }

                        if (!$new_group) {
                            $error++;
                        } else {
                            $total++;
                        }
                    } // -> Fin du test si on a au moins une classe valide

                } // -> Fin du test où le prof existe
            }

            $i++;
            if (!isset($_POST['ligne'.$i.'_prof'])) $go = false;
        }

        if ($error > 0) echo "<p><font color=red>Il y a eu " . $error . " erreurs.</font></p>";
        if ($total > 0) echo "<p>" . $total . " groupes ont été enregistrés.</p>";
        if ($warning_matiere) echo "<p><font color=red>Attention !</font> Des matières ont été créées à la volée lors de l'importation. Leur nom complet n'a pu être déterminé. Vous devez donc vous rendre sur la page de <a href='../matieres/index.php'>gestion des matières</a> pour les renommer.</p>";
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
        if(mb_strtolower($csv_file['name']) == "prof_disc_classes.csv") {

            // Le nom est ok. On ouvre le fichier
            $fp=fopen($csv_file['tmp_name'],"r");

            if(!$fp) {
                // Aie : on n'arrive pas à ouvrir le fichier... Pas bon.
                echo "<p>Impossible d'ouvrir le fichier CSV !</p>";
                echo "<p><a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</center></p>";
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
                    while (!feof($fp)) {
                        $ligne = fgets($fp, 4096);
                        if(trim($ligne)!="") {

                            $tabligne=explode(";",$ligne);

                            // 0 : Login du prof
                            // 1 : nom court de la matière
                            // 2 : identifiant(s) de là (des) classe(s) (Format : 1S1!1S2!1S3)
                            // 3 : type de groupe (CG || OPT)


            // On nettoie et on vérifie :
            $tabligne[0] = preg_replace("/[^A-Za-z0-9\._]/","",trim(mb_strtoupper($tabligne[0])));
            if (mb_strlen($tabligne[0]) > 50) $tabligne[0] = mb_substr($tabligne[0], 0, 50);

            $tabligne[1] = preg_replace("/[^A-Za-z0-9\.\-]/","",trim(mb_strtoupper($tabligne[1])));
            if (mb_strlen($tabligne[1]) > 50) $tabligne[1] = mb_substr($tabligne[1], 0, 50);

            $tabligne[2] = preg_replace("/[^A-Za-z0-9\.\-!]/","",trim($tabligne[2]));
            if (mb_strlen($tabligne[2]) > 2000) $tabligne[2] = mb_substr($tabligne[2], 0, 2000);

            $tabligne[3] = preg_replace("/[^A-Za-z]/","",trim(mb_strtoupper($tabligne[3])));

            if ($tabligne[3] != "CG" AND $tabligne[3] != "OPT") $tabligne[3] = "";



                            $data_tab[$k] = array();

                            $data_tab[$k]["prof"] = $tabligne[0];
                            $data_tab[$k]["matiere"] = $tabligne[1];
                            $data_tab[$k]["classes"] = $tabligne[2];
                            $data_tab[$k]["type"] = $tabligne[3];
                        }
                    $k++;
                    }

                fclose($fp);

                // Fin de l'analyse du fichier.
                // Maintenant on va afficher tout ça.

                echo "<form enctype='multipart/form-data' action='prof_disc_classes.php' method='post'>";
				echo add_token_field();
                echo "<input type='hidden' name='action' value='save_data' />";
                echo "<table>";
                echo "<tr><td>Login prof</td><td>Matière</td><td>Classe(s)</td><td>Type</td></tr>";

                for ($i=0;$i<$k-1;$i++) {

                        echo "<tr>";
                        echo "<td>";
                        echo $data_tab[$i]["prof"];
                        echo "<input type='hidden' name='ligne".$i."_prof' value='" . $data_tab[$i]["prof"] . "'>";
                        echo "</td>";
                        echo "<td>";
                        echo $data_tab[$i]["matiere"];
                        echo "<input type='hidden' name='ligne".$i."_matiere' value='" . $data_tab[$i]["matiere"] . "'>";
                        echo "</td>";
                        echo "<td>";
                        echo $data_tab[$i]["classes"];
                        echo "<input type='hidden' name='ligne".$i."_classes' value='" . $data_tab[$i]["classes"] . "'>";
                        echo "</td>";
                        echo "<td>";
                        echo $data_tab[$i]["type"];
                        echo "<input type='hidden' name='ligne".$i."_type' value='" . $data_tab[$i]["type"] . "'>";
                        echo "</td>";
                        echo "</tr>";
                }

                echo "</table>";

                echo "<input type='submit' value='Enregistrer'>";

                echo "</form>";
            }

        } else if (trim($csv_file['name'])=='') {

            echo "<p>Aucun fichier n'a été sélectionné !<br />";
            echo "<a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</center></p>";

        } else {
            echo "<p>Le fichier sélectionné n'est pas valide !<br />";
            echo "<a href='prof_disc_classes.php'>Cliquer ici </a> pour recommencer !</center></p>";
        }
    }
}
require("../lib/footer.inc.php");
?>
