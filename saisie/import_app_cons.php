<?php
/*
 * Last modification  : 15/03/2005
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
include "../lib/periodes.inc.php";

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations du conseil | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='saisie_avis.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_classe, "0", "classe");
echo "<p><span class = 'grand'>Première phase d'importation des appréciations </span>";
echo "<p class = 'bold'>Classe : $classe | Période : $nom_periode[$periode_num]</p>";

if (!isset($is_posted)) {
    ?>
    <form enctype="multipart/form-data" action="import_app_cons.php" method=post name=formulaire>
    <?php $csv_file=""; ?>
    <p>Fichier CSV à importer : <INPUT TYPE=FILE NAME="csv_file" />    <INPUT TYPE=SUBMIT value = Ouvrir /></p>
    <p>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre&nbsp;
    <INPUT TYPE=CHECKBOX NAME="en_tete" VALUE="yes" checked /></p>
    <INPUT TYPE=HIDDEN name=is_posted value = 1 />
    <?php
    echo "<input type=hidden name=id_classe value= $id_classe />";
    echo "<input type=hidden name=periode_num value= $periode_num />";
    ?>
    </FORM>
    <?php
    echo "<p>Vous avez décidé d'importer directement un fichier d'appréciations. Le fichier d'importation doit être au format csv (séparateur : point-virgule) et doit contenir les deux champs suivants :<br />";
    echo "--> <B>IDENTIFIANT</B> : L'identifiant GEPI de l'élève (<b>voir les explications plus bas</b>).<br />";
    echo "--> <B>Appréciation</B> : le texte de l'appréciation de l'élève.<br />Si ce champ est vide, Il n'y aura pas modification de l'appréciation enregistrée dans GEPI pour l'élève en question.</p>";
    echo "<p>Pour constituer le fichier d'importation vous avez besoin de connaître
    l'identifiant <b>GEPI</b> de chaque élève. Vous pouvez télécharger :
    <ul>
    <li>le fichier élèves des identifiants GEPI (<i>sans les nom et prénom</i>) en
    <a href='import_class_csv.php?id_classe=$id_classe&amp;periode_num=$periode_num&amp;champs=2&amp;ligne_entete=y&amp;mode=Id_App'><b>cliquant ici</b></a></li>";

    //echo ", ou bien,";
    echo "<li>ou bien le fichier élèves (nom - prénom - identifiant GEPI) en
    <a href='import_class_csv.php?id_classe=$id_classe&amp;periode_num=$periode_num&amp;champs=4&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_App'><b>cliquant ici</b></a><br />(<i>ce deuxième fichier n'est pas directement adapté à l'import<br />(il faudra en supprimer les colonnes Nom et Prénom avant import)</i>)</li>";

    echo "</ul>
    <p>Une fois téléchargé, utilisez votre tableur habituel pour ouvrir ce fichier en précisant que le type de fichier est csv avec point-virgule comme séparateur.</p>";

}
if (isset($is_posted ) and ($is_posted==1)) {
    $non_def = 'no';
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='import_app_cons.php' method=post >";
    if($csv_file['tmp_name'] != "") {
        echo "<p><b>Attention</b>, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>";

        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible d'ouvrir le fichier CSV";
        } else {
            $row = 0;
            echo "<table border=1><tr>
            <td><p class='bold'>IDENTIFIANT</p></td>
            <td><p class='bold'>Nom</p></td>
            <td><p class='bold'>Prénom</p></td>
            <td><p class='bold'>Classe</p></td>
            <td><p class='bold'>Appréciation</p></td>
            <td><p class='bold'>Statut</p></td>
            </tr>";
            $valid = 1;
            while(!feof($fp)) {
                if (isset($en_tete)) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    unset($en_tete);
                }
                $data = fgetcsv ($fp, $long_max, ";");
                 $num = count ($data);
                // On commence par repérer les lignes qui comportent 2 champs vides de façon à ne pas les retenir
                if ($num == 2) {
                    $champs_vides = 'yes';
                    for ($c=0; $c<$num; $c++) {
                        if ($data[$c] != '') {
                            $champs_vides = 'no';
                        }
                    }
                }
                // On ne retient que les lignes qui comportent 2 champs dont au moins un est non vide
                if (($num == 2) and ($champs_vides == 'no')) {
                    $row++;
                    echo "<tr>";
                    for ($c=0; $c<$num; $c++) {
                        $col3 = '';
                        $reg_app = '';
                        $data_app = '';
                        $reg_ok = "reg_".$row."_ok";
                        switch ($c) {
                        case 0:
                            //login
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $call_login = mysql_query("SELECT * FROM eleves WHERE login='$data[$c]'");
                            $test = @mysql_num_rows($call_login);
                            if ($test != 0) {
                                $nom_eleve = @mysql_result($call_login, 0, "nom");
                                $prenom_eleve = @mysql_result($call_login, 0, "prenom");

                                $classe_eleve = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_classes j WHERE (j.login = '$data[$c]' AND j.id_classe =  c.id AND j.periode='$periode_num')");
                                $eleve_classe = @mysql_result($classe_eleve, 0, "classe");
                                $eleve_id_classe = @mysql_result($classe_eleve, 0, "id");
                                if ($eleve_classe == '') {
                                   $eleve_classe = '<font color = red>???</font>';
                                   $valid = 0;
                                }

                                //
                                // On verifie que l'élève a bien pour professeur principal le professeur connecté.
                                //
                                if ($_SESSION['statut'] != 'scolarite') {
                                    $eleve_profsuivi_query = mysql_query("SELECT * FROM  j_eleves_professeurs
                                    WHERE (
                                    login='$data[$c]' AND
                                    professeur='".$_SESSION['login']."' AND
                                    id_classe = '$id_classe')");
                                    $test_suivi = mysql_num_rows($eleve_profsuivi_query);
                                } else {
                                    $test_suivi = 1;
                                }
                                //
                                // Si l'utilisateur n'est pas prof de suivi de l'élève ou si l'élève n'appartient pas à la classe, echec !
                                //
                                if (($test_suivi != "0") and ($eleve_id_classe == $id_classe))  {
                                    echo "<td><p>$data[$c]</p></td>";
                                    $stat = "OK";
                                    echo "<INPUT TYPE=HIDDEN name='$reg_ok' value = 'yes' />";

                                } else {
                                    echo "<td><p><font color = red>* $data[$c] (non valide) *</font></p></td>";
                                    $valid = 0;
                                    $stat = "<font color = red>Non valide</font>";
                                    echo "<INPUT TYPE=HIDDEN name='$reg_ok' value = 'no' />";

                                }
                                echo "<td><p>$nom_eleve</p></td>";
                                echo "<td><p>$prenom_eleve</p></td>";
                                echo "<td><p>$eleve_classe</p></td>";
                                $data_login = urlencode($data[$c]);
                                echo "<INPUT TYPE=HIDDEN name='$reg_login' value = $data_login />";
                            } else {
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                $valid = 0;
                                $stat = "<font color = red>Non valide</font>";
                               echo "<INPUT TYPE=HIDDEN name='$reg_ok' value = 'no' />";

                            }
                            break;
                        case 1:
                            // Appréciation
                            if ($data[$c] == "") {
                                $col3 = "<font color = green>ND</font>";
                                $non_def = 'yes';
                                $data_app = '';
                            } else {
                                $col3 = $data[$c];
                                $data_app = urlencode($data[$c]);
                            }
                            $reg_app = "reg_".$row."_app";
                            break;
                        }
                    }
                    echo "<td><p>$col3</p></td>";
                    echo "<td>".$stat."</td>";


                    echo "</tr>";
                    echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app />";
                // fin de la condition "if ($num == 2)"
                }

            // fin de la boucle "while(!feof($fp))"
            }
            fclose($fp);
            echo "</table>";
            echo "<p>Première phase de l'importation : $row entrées détectées !</p>";
            if ($row > 0) {
                echo "<INPUT TYPE=HIDDEN name=nb_row value = $row />";
                echo "<input type=hidden name=id_classe value= $id_classe />";
                echo "<input type=hidden name=periode_num value= $periode_num />";
                echo "<input type=hidden name=is_posted value=2 />";
                echo "<input type=submit value='Enregistrer les données' />";
                echo "</FORM>";
                if ($valid != '1') {
                    echo "<p class='bold'>AVERTISSEMENT : Les symboles <font color=red>???</font> et les messages en rouge indiquent des lignes non valides et qui ne seront pas enregistrées.<br /></p>";
                    echo "</FORM>";
                }
                if ($non_def == 'yes') {
                    echo "<p class='bold'>Les symboles <font color=green>ND</font> signifient que le champ en question sera ignoré. Il n'y aura donc pas modification de la donnée existante dans la base de GEPI.<br /></p>";
                }
                ?>
                <script type="text/javascript" language="javascript">
                <!--
                alert("Attention, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !");
                //-->
                </script>
                <?php

            } else {
                echo "<p>L'importation a échoué !</p>";
            }
        }
    // suite de la condition "if($csv_file != "none")"
    } else {
        echo "<p>Aucun fichier n'a été sélectionné !</p>";
    // fin de la condition "if($csv_file != "none")"
    }
}

// On enregistre les données
if (isset($is_posted ) and ($is_posted==2)) {
    // on vérifie que la période n'est pas totalement verrouillée
    if ($ver_periode[$periode_num] == "O") {
        echo "<p class='grand'>La période sur laquelle vous voulez enregistrer est verrouillée.</p></body></html>";
        die();
    }
    // si la période n'est pas totalement verrouillée, on continue
    $nb_row++;
    for ($row=1; $row<$nb_row; $row++) {
        $temp = "reg_".$row."_ok";
        if (isset($$temp)) {
            $reg_ok = $$temp;
        } else {
            $reg_ok = 'no';
        }
        // Si la ligne est valide, on continue
        if ($reg_ok=='yes') {
            $temp = "reg_".$row."_login";
            if (isset($$temp)) {
                $reg_login = $$temp;
                $reg_login = urldecode($reg_login);
            } else {
                $reg_login = '';
            }
            $temp = "reg_".$row."_app";
            if (isset($$temp)) {
                $reg_app = $$temp;
                $reg_app = urldecode($reg_app);
                $reg_app = traitement_magic_quotes(corriger_caracteres($reg_app));
            } else {
                $reg_app = '';
            }
            if (($reg_app != "") and ($reg_login !='')) {
                $test_eleve_app_query = mysql_query("SELECT * FROM avis_conseil_classe
                WHERE (login='$reg_login' AND
                periode='$periode_num')");
                $test = mysql_num_rows($test_eleve_app_query);
                if ($test != "0") {
                    $reg_data = mysql_query("UPDATE avis_conseil_classe
                    SET avis='$reg_app', statut=''
                    WHERE (login='$reg_login' AND periode='$periode_num')");
                } else {
                    $reg_data = mysql_query("INSERT INTO avis_conseil_classe
                    SET login='$reg_login',
                    periode='$periode_num',
                    avis='$reg_app',
                    statut=''");
                }
            } else {
                $reg_data ='ok';
            }

            if (!$reg_data) {
                echo "<font color=red>Erreur lors de la modification de l'appréciation de l'utilisateur $reg_login !</font><br />";
            } else {
                echo "L'appréciation de l'utilisateur $reg_login a été modifiée avec succès !<br />";
            }
        }
    }
    echo "</p>";
    echo "<br /><a href='saisie_avis1.php?id_classe=$id_classe'>Accéder à la page de saisie des appréciations pour vérification</a></p>";
}
require("../lib/footer.inc.php");
?>