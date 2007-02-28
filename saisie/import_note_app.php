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



$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if (!is_numeric($periode_num)) $periode_num = 0;

if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes et appréciations | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// $long_max : doit être plus grand que la plus grande ligne trouvée dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a></p>";

echo "<p><span class = 'grand'>Première phase d'importation des moyennes et appréciations </span>";
//echo "<p class = 'bold'>Groupe : " . $current_group["description"] ." (" . $current_group["classlist_string"] . ")| Matière : " . $current_group["matiere"]["nom_complet"] . " | Période : $nom_periode[$periode_num]</p>";
echo "<p class = 'bold'>Groupe : " . htmlentities($current_group["description"]) ." (" . $current_group["classlist_string"] . ")| Matière : " . htmlentities($current_group["matiere"]["nom_complet"]) . " | Période : $nom_periode[$periode_num]</p>";

if (!isset($is_posted)) {
    ?>
    <form enctype="multipart/form-data" action="import_note_app.php" method=post name=formulaire>
    <?php $csv_file=""; ?>
    <p>Fichier CSV à importer : <INPUT TYPE=FILE NAME="csv_file" />    <INPUT TYPE=SUBMIT value = Ouvrir /></p>
    <p>Si le fichier à importer comporte une première ligne d'en-tête (non vide) à ignorer, <br />cocher la case ci-contre&nbsp;
    <INPUT TYPE=CHECKBOX NAME="en_tete" VALUE="yes" checked /></p>
    <INPUT TYPE=HIDDEN name=is_posted value = 1 />
    <?php
    echo "<input type=hidden name=id_groupe value='" . $id_groupe . "' />";
    echo "<input type=hidden name=periode_num value='" . $periode_num . "' />";
    ?>
    </FORM>
    <?php
    echo "<p>Vous avez décidé d'importer directement un fichier de moyennes et/ou d'appréciations. Le fichier d'importation doit être au format csv (séparateur : point-virgule) et doit contenir les trois champs suivants :<br />";
    echo "--> <B>IDENTIFIANT</B> : L'identifiant GEPI de l'élève (<b>voir les explications plus bas</b>).<br />";
    echo "--> <B>NOTE</B> : note entre 0 et 20 avec le point ou la virgule comme symbole décimal.<br />Autres codes possibles (sans les guillemets) : \"<b>abs</b>\" pour \"absent\", \"<b>disp</b>\" pour \"dispensé\", \"<b>-</b>\" pour absence de note.<br />Si ce champ est vide, Il n'y aura pas modification de la note déjà enregistrée dans GEPI pour l'élève en question.<br />";
    echo "--> <B>Appréciation</B> : le texte de l'appréciation de l'élève.<br />Si ce champ est vide, Il n'y aura pas modification de l'appréciation enregistrée dans GEPI pour l'élève en question.</p>";
    echo "<p>Pour constituer le fichier d'importation vous avez besoin de connaître l'identifiant <b>GEPI</b> de chaque élève. Vous pouvez télécharger:";
    echo "<ul>";
    echo "<li>le fichier élèves (identifiant GEPI, sans nom et prénom) en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'><b>cliquant ici</b></a></li>";
    echo "<li>ou bien le fichier élèves (nom - prénom - identifiant GEPI) en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=5&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_Note_App'><b>cliquant ici</b></a><br />(<i>ce deuxième fichier n'est pas directement adapté à l'import<br />(il faudra en supprimer les colonnes Nom et Prénom avant import)</i>)</li>";
    echo "<p>Une fois téléchargé, utilisez votre tableur habituel pour ouvrir ce fichier en précisant que le type de fichier est csv avec point-virgule comme séparateur.</p>";

}
if (isset($is_posted )) {
    $non_def = 'no';
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='traitement_csv.php' method=post >";
    if($csv_file['tmp_name'] != "") {
        echo "<p><b>Attention</b>, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>";

        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible d'ouvrir le fichier CSV";
        } else {
            $row = 0;
            echo "<table border=1><tr><td><p class='bold'>IDENTIFIANT</p></td><td><p class='bold'>Nom</p></td><td><p class='bold'>Prénom</p></td><td><p class='bold'>Note</p></td><td><p class='bold'>Appréciation</p></td></tr>";
            $valid = 1;
            while(!feof($fp)) {
                if (isset($en_tete)) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    unset($en_tete);
                }
                $data = fgetcsv ($fp, $long_max, ";");
                 $num = count ($data);
                // On commence par repérer les lignes qui comportent 2 ou 3 champs tous vides de façon à ne pas les retenir
                if (($num == 2) or ($num == 3)) {
                    $champs_vides = 'yes';
                    for ($c=0; $c<$num; $c++) {
                        if ($data[$c] != '') {
                            $champs_vides = 'no';
                        }
                    }
                }
                // On ne retient que les lignes qui comportent 2 ou 3 champs dont au moins un est non vide
                if ((($num == 3) or ($num == 2)) and ($champs_vides == 'no')) {
                    $row++;
                    echo "<tr>";
                    for ($c=0; $c<$num; $c++) {
                        $col3 = '';
                        $reg_app = '';
                        $data_app = '';
                        switch ($c) {
                        case 0:
                            //login
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $call_login = mysql_query("SELECT * FROM eleves WHERE login='" . $data[$c] . "'");
                            $test = @mysql_num_rows($call_login);
                            if ($test != 0) {
                                $nom_eleve = @mysql_result($call_login, 0, "nom");
                                $prenom_eleve = @mysql_result($call_login, 0, "prenom");

                                //
                                // Si l'élève ne suit pas la matière
                                //
                                if (in_array($data[$c], $current_group["eleves"][$periode_num]["list"]))  {
                                    echo "<td><p>$data[$c]</p></td>";
                                } else {
                                    echo "<td><p><font color = red>* $data[$c] ??? *</font></p></td>";
                                    $valid = 0;
                                }
                                echo "<td><p>$nom_eleve</p></td>";
                                //echo "<td><p>$prenom_eleve</p></td>";
                                echo "<td><p>$prenom_eleve</p>";
                                $data_login = urlencode($data[$c]);
                                echo "<INPUT TYPE=HIDDEN name='$reg_login' value = $data_login />";
                                echo "</td>";
                            } else {
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                echo "<td><font color = red>???</font></td>";
                                $valid = 0;
                            }
                            break;
                        case 1:
                            // Note
                            if (ereg ("^[0-9\.\,]{1,}$", $data[$c])) {
                                $data[$c] = str_replace(",", ".", "$data[$c]");
                                $test_num = settype($data[$c],"double");
                                if ($test_num) {
                                    if (($data[$c] >= 0) and ($data[$c] <= 20)) {
                                        //echo "<td><p>$data[$c]</p></td>";
                                        echo "<td><p>$data[$c]</p>";
                                        $reg_note = "reg_".$row."_note";
                                        echo "<INPUT TYPE=HIDDEN name='$reg_note' value = $data[$c] />";
                                        echo "</td>";
                                    } else {
                                        echo "<td><font color = red>???</font></td>";
                                        $valid = 0;
                                    }
                                } else {
                                    echo "<td><font color = red>???</font></td>";
                                    $valid = 0;
                                }
                            } else {
                                $tempo = strtolower($data[$c]);
                                if (($tempo == "disp") or ($tempo == "abs") or ($tempo == "-")) {
                                    //echo "<td><p>$data[$c]</p></td>";
                                    echo "<td><p>$data[$c]</p>";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<INPUT TYPE=HIDDEN name='$reg_note' value = $data[$c] />";
                                    echo "</td>";
                                } else if ($data[$c] == "") {
                                    //echo "<td><p><font color = green>ND</font></p></td>";
                                    echo "<td><p><font color = green>ND</font></p>";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<INPUT TYPE=HIDDEN name='$reg_note' value = '' />";
                                    echo "</td>";
                                    $non_def = 'yes';
                                } else {
                                    echo "<td><font color = red>???</font></td>";
                                    $valid = 0;
                                }
                            }
                            break;
                        case 2:
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
//                            echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app>";
                            break;
                        }
                    }
                    //echo "<td><p>$col3</p></td></tr>";
                    echo "<td><p>$col3</p>";
                    echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app />";
                    echo "</td></tr>";
                // fin de la condition "if ($num == 3)"
                }

            // fin de la boucle "while(!feof($fp))"
            }
            fclose($fp);
            echo "</table>";
            echo "<p>Première phase de l'importation : $row entrées importées !</p>";
            if ($row > 0) {
                if ($valid == '1') {
                    echo "<INPUT TYPE=HIDDEN name=nb_row value = $row />";
                    echo "<input type=hidden name=id_groupe value= $id_groupe />";
                    echo "<input type=hidden name=periode_num value= $periode_num />";
                    echo "<input type=submit value='Enregistrer les données' />";
                    echo "</FORM>";
                    ?>
                    <script type="text/javascript" language="javascript">
                    <!--
                    alert("Attention, les données ne sont pas encore enregistrées dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !");
                    //-->
                    </script>
                    <?php
                } else {
                    echo "<p class='bold'>AVERTISSEMENT : Les symboles <font color=red>???</font> signifient que le champ en question n'est pas valide. L'opération d'importation des données ne peut continuer normalement. Veuillez corriger le fichier à importer <br /></p>";
                    echo "</FORM>";
                }
                if ($non_def == 'yes') {
                    echo "<p class='bold'>Les symboles <font color=green>ND</font> signifient que le champ en question sera ignoré. Il n'y aura donc pas modification de la donnée existante dans la base de GEPI.<br /></p>";
                }
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
require("../lib/footer.inc.php");
?>