<?php
/*
 * @version: $Id: export_csv_aid.php 6588 2011-03-02 17:53:54Z crob $
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_generique_aid = @mysql_result($call_data, 0, "nom");

//**************** EN-TETE *****************
$titre_page = "Gestion des ".$nom_generique_aid." | Outil d'importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href=\"index2.php?indice_aid=$indice_aid\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | ";

if (isset($is_posted) and ($is_posted=='avec_id_etape_4')) {echo "<a href=\"export_csv_aid.php?is_posted=avec_id_etape_1&indice_aid=$indice_aid".add_token_in_url()."\">Importer un autre fichier</a> |";}
if (isset($is_posted) and ($is_posted=='sans_id_etape_4')) {echo "<a href=\"export_csv_aid.php?is_posted=sans_id_etape_1&indice_aid=$indice_aid".add_token_in_url()."\">Importer un autre fichier</a> |";}

echo "</p>";

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV

$long_max = 8000;

if (!isset($is_posted)) {

    $test = mysql_query("SELECT * FROM aid WHERE indice_aid='$indice_aid'");

    $nb_test = mysql_num_rows($test);

    if ($nb_test == 0) {

        // Par sécurité, on efface d'éventuelles données résiduelles dans les tables j_aid_utilisateurs et j_aid_eleves
        $del = mysql_query("DELETE FROM j_aidcateg_super_gestionnaires WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs_gest WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves_resp WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM aid_appreciations WHERE indice_aid='$indice_aid'");

        $is_posted='debut';

    } else {

        if (!isset($confirm) or ($confirm != 'Effacer')) {

            echo "<p><b>ATTENTION</b> : Des $nom_generique_aid ont déjà été enregistré(e)s. La procédure d'importation permet l'insertion de <b>nouvelles données</b> et la <b>mise à jour</b> des données  existantes. <br /><b>Les données déjà présentes dans GEPI ne sont donc pas détruites par cette procédure</b>.<br /><br />Cliquez sur <b>\"Effacer\"</b> si vous souhaitez effacer <b>toutes</b> les données déjà présentes concernant les $nom_generique_aid,<br />Cliquez sur <b>\"Continuer\"</b> si vous souhaitez conserver les données existantes.</p>";

            echo "<table border=0><tr><td>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

            echo add_token_field();

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Effacer' />";

            echo "</FORM></td><td>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";

            echo add_token_field();

            echo "<INPUT TYPE=HIDDEN name=is_posted value = 'debut' /> ";

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Continuer' />";

            echo "</FORM></td></tr></table>";

        } else {

            echo "<p><b>Etes-vous sûr de vouloir effacer toutes les données concernant les $nom_generique_aid ?</b></p>";

            echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

            echo add_token_field();

            echo "<INPUT TYPE=HIDDEN name=is_posted value = 'debut' /> ";

            echo "<input type=hidden name=indice_aid value=$indice_aid />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Oui' />";

            echo "<INPUT TYPE=SUBMIT name='confirm' value = 'Non' />";

            echo "</FORM>";
        }
    }
}

if (isset($is_posted) and ($is_posted == 'debut')) {
    //check_token();

    if (isset($confirm) and ($confirm == 'Oui')) {
        check_token(false);

        $del = mysql_query("DELETE FROM aid WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_utilisateurs WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM j_aid_eleves WHERE indice_aid='$indice_aid'");
        $del = mysql_query("DELETE FROM aid_appreciations WHERE indice_aid='$indice_aid'");
        echo "<p>Les données concernant les $nom_generique_aid ont été définitivement supprimées !</p>";
    }
    echo "<p>Choisissez une des deux options suivantes :</p>";
    echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

    echo add_token_field();

    echo "<p>--&gt; Vous avez <b>vous-même</b> défini un identifiant unique pour chaque $nom_generique_aid.";
    echo "<INPUT TYPE=SUBMIT value = 'Valider' /></p>";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_1' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "</FORM>";
    echo "<form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";
    echo add_token_field();
    echo "<p>--&gt; Vous voulez laisser <b>GEPI</b> définir un identifiant unique pour chaque $nom_generique_aid .";
    echo "<INPUT TYPE=SUBMIT value = 'Valider' /></p>";
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_1' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "</FORM>";
}

//*************************************************************************************************
// Début de la procédure dans laquelle on laisse GEPI définir un identifiant unique pour chaque AID
//*************************************************************************************************

if (isset($is_posted) and ($is_posted == "sans_id_etape_1")) {
    check_token(false);

    echo "<table border=0>";
    //    cas où on importe un fichier ELEVES-AID
    echo "<tr><td><p>Importer un fichier <b>\"ELEVES-$nom_generique_aid\"</b></p></td>";
    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";
    echo add_token_field();
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_2' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "<INPUT TYPE=HIDDEN name=type_import value = 1 /> ";
    echo "<INPUT TYPE=SUBMIT value = Valider />";
    echo "</FORM></td></tr>";
    //    cas où on importe un fichier prof-AID
    echo "<tr><td><p>Importer un fichier <b>\"PROF-$nom_generique_aid\"</b></p></td>";
    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";
    echo add_token_field();
    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_2' /> ";
    echo "<input type=hidden name=indice_aid value=$indice_aid />";
    echo "<INPUT TYPE=HIDDEN name=type_import value=2 /> ";
    echo "<INPUT TYPE=SUBMIT value=Valider />";
    echo "</FORM></td></tr>";
    echo "</table>";
}


if (isset($is_posted) and ($is_posted == 'sans_id_etape_2')) {
    check_token(false);

    ?>
    <form enctype="multipart/form-data" action="export_csv_aid.php" method=post name=formulaire>
    <?php
    $csvfile="";
    echo add_token_field();
    ?>
    <p>Fichier CSV à Importer <a href='help_import.php'>Aide </a> : <input TYPE=FILE NAME="csvfile" /></p>
    <input TYPE=HIDDEN name=is_posted value = 'sans_id_etape_3' />
    <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />
    <input TYPE=HIDDEN name=type_import value = "<?php echo $type_import; ?>" />
    <p>Le fichier à importer comporte une première ligne d'en-tête, à ignorer&nbsp;
    <input TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED /></p>
    <input TYPE=SUBMIT value = "Valider" /><br />
    </form>
    <?php
    echo "<p>Le fichier d'importation doit être au format csv (séparateur : point-virgule)<br />";
    if ($type_import == 1) {
        echo "Le fichier doit contenir les deux champs suivants, obligatoires :<br />";
        echo "--&gt; <B>IDENTIFIANT</B> : l'identifiant de l'élève<br />";
        echo "--&gt; <B>Nom complet de l'activité</B><br /></p>";
    } else if ($type_import == 2) {
        echo "Le fichier doit contenir les deux champs suivants, obligatoires :<br />";
        echo "--&gt; <B>IDENTIFIANT</B> : l'identifiant du professeur<br />";
        echo "--&gt; <B>Nom complet de l'activité</B><br /></p>";
    }
}

if (isset($is_posted) and ($is_posted == 'sans_id_etape_3')) {
    check_token(false);

	$csvfile = isset($_FILES["csvfile"]) ? $_FILES["csvfile"] : NULL;
   //if($csvfile != "none") {
    if(isset($csvfile)) {
        //$fp = fopen($csvfile, "r");
        $fp = fopen($csvfile['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible d'ouvrir le fichier CSV (".$csvfile['name'].")";
        } else {
            $erreur = 'no';
            //    Dans le cas où on importe un fichier PROF-AID ou ELEVE-AID, on vérifie le login
            $row = 0;
            while(!feof($fp)) {
                if ($en_tete == 'yes') {
                    $data = fgetcsv ($fp, $long_max, ";");
                    $en_tete = 'no';
                    $en_tete2 = 'yes';
                }
                $data = fgetcsv ($fp, $long_max, ";");
                $num = count ($data);
                if ($num == 2) {
                    $row++;
                    //login
                    if ($type_import == 1) {
                        $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[0]'");
                    } else {
                        $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$data[0]'");
                    }
                    $test = mysql_num_rows($call_login);
                    if ($test == 0) {
                        $erreur = 'yes';
                        echo "<p><font color='red'>Erreur dans le fichier à la ligne $row : $data[0] ne correspond à aucun identifiant GEPI.</font></p>";
                    }
                }
            }
            fclose($fp);
            //

            // On stocke les info du fichier dans une table

            //

            if ($erreur == 'no') {

                $del = mysql_query("delete from tempo2");

                //$fp = fopen($csvfile, "r");
                $fp = fopen($csvfile['tmp_name'], "r");

                $row = 0;

                $erreur_reg = 'no';

                while(!feof($fp)) {

                    if ($en_tete2 == 'yes') {

                        $data = fgetcsv ($fp, $long_max, ";");

                        $en_tete2 = 'no';

                    }

                    $data = fgetcsv ($fp, $long_max, ";");

                    $num = count ($data);

                    if ($num == 2) {

                        $row++;

                        $data[1] = traitement_magic_quotes(corriger_caracteres($data[1]));

                        $query = "INSERT INTO tempo2 VALUES('$data[0]', '$data[1]')";

                        $register = mysql_query($query);

                        if (!$register) {

                            $erreur_reg = 'yes';

                            echo "<p><font color='red'>Erreur lors de l'enregistrement de la ligne $row dans la table temporaire.</font></p>";

                        }

                    }

                }

                fclose($fp);

                if ($erreur_reg == 'no') {

                    // On affiche les aid détectées dans la table tempo2

                    echo "<form enctype='multipart/form-data' action='export_csv_aid.php' method=post >";

                    echo add_token_field();

                    if ($type_import == 1) {

                        echo "<input type=submit value='Enregistrer les $nom_generique_aid et mettre à jour les élèves' />";

                    } else if ($type_import == 2) {

                        echo "<input type=submit value='Enregistrer les $nom_generique_aid et mettre à jour les professeurs' />";

                    } else {

                        echo "<input type=submit value='Enregistrer les $nom_generique_aid' />";

                    }

                    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'sans_id_etape_4' />";

                    echo "<input type=hidden name=indice_aid value=$indice_aid />";

                    echo "<INPUT TYPE=HIDDEN name=type_import value='$type_import' />";

                    echo "</FORM>";

                    echo "<p>Si un(e) $nom_generique_aid existe déjà dans la base GEPI, seule une mise à jour des données sera effectuée conformément aux données figurant dans le fichier csv</p>";



                    $call_data = mysql_query("SELECT distinct col2 FROM tempo2 WHERE col2!='' ORDER BY col2");

                    $nb_aid = mysql_num_rows($call_data);





                    echo "<table border=1 cellpadding=2 cellspacing=2>";

                    echo "<tr><td><p class=\"small\">$nom_generique_aid :Nom</p></td>";

                    echo "<td><p class=\"small\">Remarque</p></td></tr>";

                    $i = "0";

                    while ($i < $nb_aid) {

                        $nom_aid = mysql_result($call_data, $i, "col2");

                        $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

                        $test = mysql_query("SELECT * FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

                        $nb_test = mysql_num_rows($test);

                        if ($nb_test == 0) {

                            $mess = "<font color='green'>Cette activité n'existe pas dans GEPI.</font>";

                        } else {

                            $mess = "<font color='blue'>Cette activité existe déjà dans GEPI.</font>";

                        }

                        echo "<tr><td><p><b>$nom_aid</b></p></td>";

                        echo "<td><p>$mess</p></td></tr>";

                        $i++;

                    }

                    echo "</table>";



                } else {

                    $del = mysql_query("delete from tempo2");

                    echo "<p>AVERTISSEMENT : Une ou plusieurs erreurs ont été détectées lors de l'enregistrement des données dans la table temporaire : l'opération d'importation ne peut continuer !</p>";

                }

            } else {

                echo "<p>AVERTISSEMENT : Une ou plusieurs erreurs ont été détectées dans le fichier : l'opération d'importation ne peut continuer !</p>";

            }

        }

    } else {

        echo "<p>Aucun fichier n'a été sélectionné !</p>";

    }

}



if (isset($is_posted) and ($is_posted == 'sans_id_etape_4')) {
    check_token(false);

    echo "<p class='bold'>Mise à jour de la liste des $nom_generique_aid</p>";

    echo "<table border=1 cellpadding=2 cellspacing=2><tr>";

    echo "<td><p class=\"small\">Nom de l'acticité</p></td>";

    echo "<td><p class=\"small\">Remarque</p></td></tr>";

    $call_max = mysql_query("SELECT max(id) max FROM aid WHERE indice_aid='$indice_aid'");

    $max_id = mysql_result($call_max,0,max);

    $call_data = mysql_query("SELECT distinct col2 FROM tempo2 WHERE col2!='' ORDER BY col2");

    $nb_aid = mysql_num_rows($call_data);

    // On enregistre les AID

    $pb_reg = 'no';

    $i = "0";

    while ($i < $nb_aid) {

        $nom_aid = mysql_result($call_data, $i, "col2");

        $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

        $num_aid = '';

        $test = mysql_query("SELECT * FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

        $nb_test = mysql_num_rows($test);

        if ($nb_test == 0) {

            $max_id++;

            $reg = mysql_query("INSERT INTO aid SET id = '$max_id', nom='$temp', numero='$num_aid', indice_aid='$indice_aid'");

            if ($reg) {

                $mess = "<font color='green'>L'activité a été enregistrée avec succès !</font>";

            } else {

                $mess = "<font color='red'>Problème lors de l'enregistrement !</font>";

                $pb_reg = 'yes';

            }

        } else {

            $mess = "<font color='blue'>Pas d'enregistrement : cette acticité existait déjà dans GEPI !</font>";

        }

        echo "<tr>";

        echo "<td><p><b>$nom_aid</b></p></td>";

        echo "<td><p>$mess</p></td></tr>";

        $i++;

    }

    echo "</table>";



    if ($pb_reg == 'yes') {

        echo "<p>Il y a eu un problème lors de l'enregistrement des $nom_generique_aid, l'opération d'importation ne peut continuer : la table des identifiants pour les $nom_generique_aid n'a pas été mise à jour !</p>";

    } else {

        // initialisation de variables

        if ($type_import == 1) {

            $aid_table = "j_aid_eleves";

            $nom_champ = "login";

        } else {

            $aid_table = "j_aid_utilisateurs";

            $nom_champ = "id_utilisateur";

        }

        // On enregistre les login

        $nb = 0;

        $call_data = mysql_query("SELECT * FROM tempo2");

        $nb_lignes = mysql_num_rows($call_data);

        $pb_reg = "no";

        $i = "0";

        while ($i < $nb_lignes) {

            $champ1 = mysql_result($call_data, $i, "col1");

            if ($type_import == 1) {

                $call_login = mysql_query("SELECT login FROM eleves WHERE login='$champ1'");

            } else {

                $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$champ1'");

            }

            $test = mysql_num_rows($call_login);

            if ($test != 0) {

                // cas où un login existe dans la table eleves ou utilisateurs

                // On peut continuer !

                $nom_aid = mysql_result($call_data, $i, "col2");

                $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

                $call_id = mysql_query("SELECT id FROM aid WHERE (nom = '$temp' and indice_aid='$indice_aid')");

                $id_aid = mysql_result($call_id, 0, "id");

                if ($type_import == 1) {

                    $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and indice_aid='$indice_aid')");

                } else {

                    $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and id_aid='$id_aid' and indice_aid='$indice_aid')");

                }

                $test2 = mysql_num_rows($call_test);

                // pour les élèves : un élève ne peut suivre qu'une seule AID. Si une ligne existe déjà on la met à jour (update)

                // pour les prof : un prof peut être responsable de plusieurs AID, mais on teste qu'il n'y ait pas de lignes 'doublons' dans le fichier j_aid_utilisateurs.

                if ($test2 == 0) {

                    $reg = mysql_query("INSERT INTO $aid_table SET id_aid='$id_aid', $nom_champ = '$champ1', indice_aid='$indice_aid'");

                    if (!$reg) {

                        $pb_reg = "yes";

                    } else {

                        $nb++;

                    }

                } else {

                    if ($type_import == 1) {

                        $reg = mysql_query("UPDATE $aid_table SET id_aid='$id_aid' WHERE ($nom_champ = '$champ1' and indice_aid='$indice_aid')");

                        if (!$reg) {

                            $pb_reg = "yes";

                        } else {

                            $nb++;

                        }

                    }

                }

                $i++;

            }

        }

        if ($type_import == 1) {

            echo "<p class='bold'>Mise à jour des élèves</p>";

            echo "<p>$nb lignes élèves ont été mises à jour dans la table de liaison <b>Eleves&lt;--&gt;$nom_generique_aid</b> !</p>";

            if ($pb_reg == "yes") {

                echo "<p><font color = 'red'>Il y a eu des problèmes d'enregistrement pour un ou plusieurs autres élèves !</font></p>";

            }

        } else {

            echo "<p class='bold'>Mise à jour des professeurs</p>";

            echo "<p>$nb lignes professeurs ont été mises à jour dans la table de liaison <b>Professeurs&lt;--&gt;$nom_generique_aid</b> !</p>";

            if ($pb_reg == "yes") {

                echo "<p><font color = 'red'>Il y a eu des problèmes d'enregistrement pour un ou plusieurs autres professeurs !</font></p>";

            }

        }



        $del = mysql_query("delete from tempo2");

    }

}



//*************************************************************************************************

// Fin de la procédure dans laquelle on laisse GEPI définir un identifiant unique pour chaque AID

//*************************************************************************************************



//*************************************************************************************************

// Début de la procédure dans laquelle l'utilisateur définie lui-même un identifiant unique pour chaque AID

//*************************************************************************************************



if (isset($is_posted) and ($is_posted == 'avec_id_etape_1')) {
    check_token(false);

    echo "<table border=0>";

    //    cas où on importe un fichier numéro-AID

    echo "<tr><td><p>Importer un fichier <b>\"$nom_generique_aid - Identifiant $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 3 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";

    //    cas où on Importe un fichier ELEVES-N° AID

    echo "<tr><td><p>Importer un fichier <b>\"ELEVES-Identifiant $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire2>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 1 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";

    //    cas où on importe un fichier prof-AID

    echo "<tr><td><p>Importer un fichier <b>\"PROF-Identifiant $nom_generique_aid\"</b></p></td>";

    echo "<td><form enctype=\"multipart/form-data\" action=\"export_csv_aid.php\" method=post name=formulaire3>";

    echo add_token_field();

    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_2' /> ";

    echo "<input type=hidden name=indice_aid value=$indice_aid />";

    echo "<INPUT TYPE=HIDDEN name=type_import value = 2 /> ";

    echo "<INPUT TYPE=SUBMIT value = Valider />";

    echo "</FORM></td></tr>";



    echo "</table>";

}





if (isset($is_posted) and ($is_posted == 'avec_id_etape_2')) {
    check_token(false);

    ?>

    <form enctype="multipart/form-data" action="export_csv_aid.php" method=post name=formulaire>

    <?php
    $csvfile="";
    echo add_token_field();
    ?>

    <p>Fichier CSV à importer <a href='help_import.php'>Aide </a> : <INPUT TYPE=FILE NAME="csvfile" /></p>

    <input TYPE=HIDDEN name=is_posted value = 'avec_id_etape_3' />

    <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />

    <input TYPE=HIDDEN name=type_import value = "<?php echo $type_import; ?>" />

    <p>Le fichier à importer comporte une première ligne d'en-tête, à ignorer&nbsp;

    <input TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED /></p>

    <input TYPE=SUBMIT value = "Valider" /><br />

    </form>

    <?php

    echo "<p>Le fichier d'importation doit être au format csv (séparateur : point-virgule)<br />";

    if ($type_import == 1) {

        echo "Le fichier doit contenir les deux champs suivants, obligatoires :<br />";

        echo "--&gt; <B>l'identifiant de l'élève</b><br />";

        echo "--&gt; <B>L'identifiant de l'activité</B><br /></p>";

    } else if ($type_import == 2) {

        echo "Le fichier doit contenir les deux champs suivants, obligatoires :<br />";

        echo "--&gt; <B>l'identifiant du professeur</b><br />";

        echo "--&gt; <B>L'identifiant de l'activité</B><br /></p>";

    } else {

        echo "Le fichier doit contenir les deux champs suivants, obligatoires :<br />";

        echo "--&gt; <B>Nom complet de l'activité</B><br />";

        echo "--&gt; <B>L'identifiant de l'activité</B><br /></p>";

    }

}



if (isset($is_posted) and ($is_posted == 'avec_id_etape_3')) {
    check_token(false);

	$csvfile = isset($_FILES["csvfile"]) ? $_FILES["csvfile"] : NULL;
    //if($csvfile != "none") {
    if(isset($csvfile)) {

        //$fp = fopen($csvfile, "r");
        $fp = fopen($csvfile['tmp_name'], "r");

        if(!$fp) {

            //echo "Impossible d'ouvrir le fichier CSV ($csvfile)";
            echo "Impossible d'ouvrir le fichier CSV (".$csvfile['name'].")";

        } else {

            $erreur = 'no';

            //

            //    Dans le cas où on importe un fichier PROF-AID ou ELEVE-AID, on vérifie le login

            //  ainsi que l'existence d'une AID corrspondant à chaque identifiant AID

            //

            $row = 0;

            while(!feof($fp)) {

                if ($en_tete == 'yes') {

                    $data = fgetcsv ($fp, $long_max, ";");

                    $en_tete = 'no';

                    $en_tete2 = 'yes';

                }

                $data = fgetcsv ($fp, $long_max, ";");

                $num = count ($data);

                if ($num == 2) {

                    $row++;

                    // vérification du login

                    if ($type_import == 1) {

                        $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[0]'");

                        $test = mysql_num_rows($call_login);

                    } else if ($type_import == 2) {

                        $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$data[0]'");

                        $test = mysql_num_rows($call_login);

                    } else {

                        $test = 1;

                    }

                    if ($test == 0) {

                        $erreur = 'yes';

                        echo "<p><font color='red'>Erreur dans le fichier à la ligne $row : $data[0] ne correspond à aucun identifiant GEPI.</font></p>";

                    }

                    //

                    // Vérification sur l'identifiant AID

                    //

                    if (!(preg_match("/^[a-zA-Z0-9_]{1,10}$/", $data[1]))) {

                        $erreur = 'yes';

                        echo "<p><font color='red'>Erreur dans le fichier à la ligne $row : l'identifiant $nom_generique_aid n'est pas valide (un identifiant doit être constitué de uniquement de chiffres, de lettres et caractères de soulignement).</font></p>";

                    }

                    $call_aid = mysql_query("SELECT * FROM aid WHERE (id='$data[1]' and indice_aid='$indice_aid')");

                    $test = mysql_num_rows($call_aid);

                    if (($test == 0) and ($type_import != 3)) {

                        // Vérification de l'existence d'une AID correspondant à chaque identifiant AID

                        //

                        $erreur = 'yes';

                        echo "<p><font color='red'>Erreur dans le fichier à la ligne $row : l'identifiant $nom_generique_aid ne correspond à aucun(e) $nom_generique_aid déjà enregistré(e).</font></p>";

                    } else if (($test != 0) and ($type_import == 3)) {

                        // Vérification que l'identifiant n'existe pas déjà

                        //

                        $erreur = 'yes';

                        echo "<p><font color='red'>Erreur dans le fichier à la ligne $row : l'identifiant $nom_generique_aid existe déjà pour un(e) $nom_generique_aid déjà enregistré(e) !</font></p>";

                    }

                    // Recherche de doublons sur les identifiants

                    if ($type_import == 3) {

                        $doublons = 'no';

                        $tab_id[$row] = $data[1];

                        for ($k=1;$k<$row;$k++) {

                            if ($data[1] == $tab_id[$k]) {

                                $erreur = 'yes';

                                echo "<p><font color='red'>Erreur dans le fichier : il y a des doublons dans les identifiants $nom_generique_aid !</font></p>";

                            }

                        }

                    }

                }

            }

            fclose($fp);

            //

            // On stocke les info du fichier dans une table

            //

            if ($erreur == 'no') {

                $del = mysql_query("delete from tempo2");

                //$fp = fopen($csvfile, "r");
                $fp = fopen($csvfile['tmp_name'], "r");

                $row = 0;

                $erreur_reg = 'no';

                while(!feof($fp)) {

                    if ($en_tete2 == 'yes') {

                        $data = fgetcsv ($fp, $long_max, ";");

                        $en_tete2 = 'no';

                    }

                    $data = fgetcsv ($fp, $long_max, ";");

                    $num = count ($data);

                    if ($num == 2) {

                        $row++;

                        $data[0] = traitement_magic_quotes(corriger_caracteres($data[0]));

                        $query = "INSERT INTO tempo2 VALUES('$data[0]', '$data[1]')";

                        $register = mysql_query($query);

                        if (!$register) {

                            $erreur_reg = 'yes';

                            echo "<p><font color='red'>Erreur lors de l'enregistrement de la ligne $row dans la table temporaire.</font></p>";

                        }

                    }

                }

                fclose($fp);



                if ($erreur_reg == 'no') {

                    // On affiche les aid détectées dans la table tempo2

                    echo "<form enctype='multipart/form-data' action='export_csv_aid.php' method=post >";

                    echo add_token_field();

                    if ($type_import != 3) {

                        echo "<input type=submit value='Enregistrer' />";

                        $call_data = mysql_query("SELECT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

                        $nb_aid = mysql_num_rows($call_data);

                        echo "<table border=1 cellpadding=2 cellspacing=2>";

                        echo "<tr><td><p class=\"small\">Nom prénom</p></td><td><p class=\"small\">Nom de l'activité</p></td></tr>";

                        $i = "0";

                        while ($i < $nb_aid) {

                            $login_individu = mysql_result($call_data, $i, "col1");

                            $id_aid = mysql_result($call_data, $i, "col2");

                            if ($type_import == 1) {

                                $call_individus = mysql_query("SELECT nom, prenom FROM eleves WHERE login='$login_individu'");

                            } else {

                                $call_individus = mysql_query("SELECT nom, prenom FROM utilisateurs WHERE login='$login_individu'");

                            }

                            $nom_individu = mysql_result($call_individus, 0, 'nom');

                            $prenom_individu = mysql_result($call_individus, 0, 'prenom');

                            $call_aid = mysql_query("SELECT nom FROM aid WHERE (id='$id_aid' and indice_aid='$indice_aid')");

                            $nom_aid = mysql_result($call_aid, 0, 'nom');



                            echo "<tr><td><p>$nom_individu $prenom_individu</p></td>";

                            echo "<td><p>$nom_aid</p></td></tr>";

                            $i++;

                        }

                        echo "</table>";

                    } else {

                        echo "<input type=submit value='Enregistrer les $nom_generique_aid' />";

                        $call_data = mysql_query("SELECT DISTINCT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

                        $nb_aid = mysql_num_rows($call_data);

                        echo "<table border=1 cellpadding=2 cellspacing=2>";

                        echo "<tr><td><p class=\"small\">Identifiant</p></td><td><p class=\"small\">Nom de l'activité</p></td></tr>";

                        $i = "0";

                        while ($i < $nb_aid) {

                            $nom_aid = mysql_result($call_data, $i, "col1");

                            $id_aid = mysql_result($call_data, $i, "col2");

                            echo "<tr><td><p><b>$id_aid</b></p></td>";

                            echo "<td><p><b>$nom_aid</b></p></td></tr>";

                            $i++;

                        }

                        echo "</table>";

                    }

                    echo "<INPUT TYPE=HIDDEN name=is_posted value = 'avec_id_etape_4' />";

                    echo "<input type=hidden name=indice_aid value=$indice_aid />";

                    echo "<INPUT TYPE=HIDDEN name=type_import value='$type_import' />";

                    echo "</FORM>";

                } else {

                    $del = mysql_query("delete from tempo2");

                    echo "<p>AVERTISSEMENT : Une ou plusieurs erreurs ont été détectées lors de l'enregistrement des données dans la table temporaire : l'opération d'importation ne peut continuer !</p>";

                }



            } else {

                echo "<p>AVERTISSEMENT : Une ou plusieurs erreurs ont été détectées dans le fichier : l'opération d'importation ne peut continuer !</p>";

            }

        }

    } else {

        echo "<p>Aucun fichier n'a été sélectionné !</p>";

    }

}



if (isset($is_posted) and ($is_posted == 'avec_id_etape_4')) {
    check_token(false);

    if ($type_import == 3) {

        $call_data = mysql_query("SELECT DISTINCT * FROM tempo2 WHERE ((col1 !='') and (col2!='')) ORDER BY col1");

        $nb_aid = mysql_num_rows($call_data);

        // On enregistre les AID

        $i = "0";

        while ($i < $nb_aid) {

            $nom_aid = mysql_result($call_data, $i, "col1");

            $temp = traitement_magic_quotes(corriger_caracteres($nom_aid));

            $id_aid = mysql_result($call_data, $i, "col2");

            $test = mysql_query("SELECT * FROM aid WHERE (id='$id_aid' and indice_aid='$indice_aid')");

            $nb_test = mysql_num_rows($test);

            if ($nb_test == 0) {

                $reg = mysql_query("INSERT INTO aid SET id = '$id_aid', nom='$temp', numero='$id_aid', indice_aid='$indice_aid'");

                if ($reg) {

                    echo "<p><font color='green'>L'activité $nom_aid a été enregistrée avec succès !</font></p>";

                } else {

                    echo "<p><font color='red'>Il y a eu un problème lors de l'enregistrement de l'activité $nom_aid  !</font></p>";

                }

            } else {

                echo "<p><font color='red'>L'activité $nom_aid n'a pas été enregistrée, car un(e) $nom_generique_aid ayant le même identifiant existe déjà dans la base !</font></p>";

            }

            $i++;

        }

    } else {

        // initialisation de variables

        if ($type_import == 1) {

            $aid_table = "j_aid_eleves";

            $nom_champ = "login";

        } else {

            $aid_table = "j_aid_utilisateurs";

            $nom_champ = 'id_utilisateur';

        }

        // On enregistre les login

        $nb = 0;

        $call_data = mysql_query("SELECT * FROM tempo2");

        $nb_lignes = mysql_num_rows($call_data);

        $pb_reg = "no";

        $i = "0";

        while ($i < $nb_lignes) {

            $champ1 = mysql_result($call_data, $i, "col1");

            if ($type_import == 1) {

                $call_login = mysql_query("SELECT login FROM eleves WHERE login='$champ1'");

            } else {

                $call_login = mysql_query("SELECT login FROM utilisateurs WHERE login='$champ1'");

            }

            $test = mysql_num_rows($call_login);

            if ($test != 0) {

                // cas où un login existe dans la table eleves ou utilisateurs

                // On peut continuer !

                $id_aid = mysql_result($call_data, $i, "col2");

                $call_aid = mysql_query("SELECT * FROM aid WHERE (id = '$id_aid' and indice_aid='$indice_aid')");

                $test1 = mysql_num_rows($call_aid);

                if ($test1 != 0) {

                    if ($type_import == 1) {

                        $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and indice_aid='$indice_aid')");

                    } else {

                        $call_test = mysql_query("SELECT * FROM $aid_table WHERE ($nom_champ='$champ1' and id_aid='$id_aid' and indice_aid='$indice_aid')");

                    }

                    $test2 = mysql_num_rows($call_test);

                    // pour les élèves : un élève ne peut suivre qu'une seule AID. Si une ligne existe déjà on la met à jour (update)

                    // pour les prof : un prof peut être responsable de plusieurs AID, mais on teste qu'il n'y ait pas de lignes 'doublons' dans le fichier j_aid_utilisateurs.

                    if ($test2 == 0) {

                        $reg = mysql_query("INSERT INTO $aid_table SET id_aid='$id_aid', $nom_champ = '$champ1', indice_aid='$indice_aid'");

                        if (!$reg) {

                            $pb_reg = "yes";

                        } else {

                            $nb++;

                        }

                    } else {

                        if ($type_import == 1) {

                            $reg = mysql_query("UPDATE $aid_table SET id_aid='$id_aid' WHERE ($nom_champ = '$champ1' and indice_aid='$indice_aid')");

                            if (!$reg) {

                                $pb_reg = "yes";

                            } else {

                                $nb++;

                            }

                        }

                    }

                }

            }

            $i++;

        }

    }

    if ($type_import == 1) {

        echo "<p class='bold'>Mise à jour des élèves</p>";

        echo "<p>$nb élèves ont été mis à jour dans la table de liaison <b>Eleves&lt;--&gt;$nom_generique_aid</b> !</p>";

        if ($pb_reg == "yes") {

            echo "<p><font color = 'red'>Il y a eu des problèmes d'enregistrement pour un ou plusieurs autres élèves !</font></p>";

        }

    } else if ($type_import == 2) {

        echo "<p class='bold'>Mise à jour des professeurs</p>";

        echo "<p>$nb professeurs ont été mis à jour dans la table de liaison <b>Professeurs&lt;--&gt;$nom_generique_aid</b> !</p>";

        if ($pb_reg == "yes") {

            echo "<p><font color = 'red'>Il y a eu des problèmes d'enregistrement pour un ou plusieurs autres professeurs !</font></p>";

        }

    }

    $del = mysql_query("delete from tempo2");

}

//*************************************************************************************************

// Fin de la procédure dans laquelle l'utilisateur définie lui-même un identifiant unique pour chaque AID

//*************************************************************************************************


require("../lib/footer.inc.php");
?>