<?php
/*
 * Last modification  : 10/03/2005
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
$resultat_session = $session_gepi->security_check();
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

if (isset($is_posted) and ($is_posted == 'yes')) {
    $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' and c.login = e.login)");
    $nombre_lignes = mysql_num_rows($appel_donnees_eleves);
    $i = "0";
    while($i < $nombre_lignes) {
        $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
        $call_data = mysql_query("SELECT ELEOPT1,ELEOPT2,ELEOPT3,ELEOPT4,ELEOPT5,ELEOPT6,ELEOPT7,ELEOPT8,ELEOPT9,ELEOPT10,ELEOPT11,ELEOPT12 FROM temp_gep_import WHERE LOGIN = '$current_eleve_login'");
        $j="0";
        while ($j < $nb_options) {
            $temp = "matiere".$j;
            $matiere = isset($_POST[$temp])?$_POST[$temp]:NULL;
            if ($matiere != '') {
                $mat[$j] = $matiere;
                $ind = 1;
                $suit_option = 'no';
                while ($ind < 13) {
                    $optionx = "ELEOPT".$ind;
                    $temp = mysql_result($call_data, 0, $optionx);
                    if ($tab_options[$j] == $temp) {
                        $suit_option = "yes";
                    }
                    $ind++;
                }

                if ($suit_option == 'no') {
                    // on gère le cas ou on a choisit au moins deux fois la meme matiere GEPI pour deux matières GEP différentes
                    // Si la matière a déjà été traitée, on va accumuler les élèves qui suivent l'option.
                    $inser = 'yes';
                    $m = 0;
                    while ($m < $j) {
                        if (isset($mat[$m]) and ($mat[$m] == $matiere)) $inser = 'no';
                        $m++;
                    }

                    if ($inser == 'yes') {
                        $k = 1;
                        while ($k < $nb_periode) {
                            $test = mysql_query("SELECT * FROM j_eleves_matieres WHERE (matiere='$matiere' and login='$current_eleve_login' and periode='$k')");
                            $nb_test = mysql_num_rows($test);
                            if ($nb_test == 0) {
                                $reg = mysql_query("INSERT INTO j_eleves_matieres SET matiere='$matiere' , login='$current_eleve_login', periode='$k'");
                            }
                            $k++;
                        }
                    }
                } else {
                    $k = 1;
                    while ($k < $nb_periode) {
                        $test = mysql_query("SELECT * FROM j_eleves_matieres WHERE (matiere='$matiere' and login='$current_eleve_login' and periode='$k')");
                        $nb_test = mysql_num_rows($test);
                        if ($nb_test != 0) {
                            $reg = mysql_query("DELETE FROM j_eleves_matieres WHERE (matiere='$matiere' and login='$current_eleve_login' and periode='$k')");
                        }
                        $k++;
                    }
                }

            }
            $j++;
        }
        $i++;
    }
    $affiche_message = 'yes';
    header("Location: ./modify_class.php?id_classe=$id_classe&affiche_message=yes");

}


//**************** EN-TETE *****************
$titre_page = "Gestion des classes | Initialisation  des options par GEP";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<form enctype="multipart/form-data" action="init_options.php" method=post>
<p class=bold><a href="modify_class.php?id_classe=<?echo $id_classe?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<?php

// Test d'existence de données concernant les options et affichage le cas échéant d'un message d'avertissement
$test = mysql_query("SELECT * FROM j_eleves_matieres m, j_eleves_classes j, classes c WHERE (m.login=j.login and j.id_classe = c.id and c.id = '$id_classe')");
$nb = mysql_num_rows($test);
if ($nb != 0) {
    echo "<p><b><font color='red'>ATTENTION : des données concernant les options suivies dans cette classe ont déjà été enregistrées. Si vous validez cette page, les données déjà enregistrées seront effacées et mises à jour.</font></b></p>";
}

$call_nom_class = mysql_query("SELECT classe, nom_complet FROM classes WHERE id = '$id_classe'");
$classe = mysql_result($call_nom_class, 0, 'classe');
$nom_complet_classe = mysql_result($call_nom_class, 0, 'nom_complet');



// Reste à faire :
// tester si tous les élèves appartiennent à la classe pour toutes les périodes
// tester si j_eleves_matieres est vide pour les élèves de la classe


$nb_options = 0;
$i = 1;
$info = 'no';
while ($i < 13) {
    $tempo = "ELEOPT".$i;
    $call_data = mysql_query("SELECT distinct $tempo FROM temp_gep_import WHERE DIVCOD = '$classe'");
    $nb_lignes = mysql_num_rows($call_data);
    if ($nb_lignes != 0) {
        $info = 'yes';
    }
    $m = "0";
    while ($m < $nb_lignes) {
        $temp = mysql_result($call_data, $m, $tempo);
        if ($temp!='') {
            // On s'assure de ne pas ranger dans le tableau tab_options, plusieurs fois la même option
            $n = 0;
            $double = 'no';
            while ($n < $nb_options) {
                if ($tab_options[$n] == $temp) {$double = 'yes';}
                $n++;
            }
            if ($double == 'no') {
                $tab_options[$nb_options] = $temp;
                echo "<input type=hidden name=\"tab_options[$nb_options]\" value=\"$tab_options[$nb_options]\">";
                $nb_options++;

            }

        }
        $m++;
    }
    $i++;
}
if ($info == 'no') {
    echo "<p class='grand'>Aucune information disponible concernant cette classe.
    Soit vous n'avez pas utilisé les fichiers GEP pour initialiser les données,
    soit la table temp_gep_import contenant les données sur les options a été vidée.</p>";
    die();
} else {
    echo "<p>Classe : $nom_complet_classe <input type=submit value=Enregistrer></p>";
    echo "<p><b>Attention</b> : vous ne devez utiliser cette procédure qu'en tout début d'année et lorsque <b>toutes</b> les matières ont été définies pour cette classe.</p>";
    echo "<p>Les données du tableau ci-dessous sont fournies par les fichiers GEP. Si vous désirez importer ces informations dans GEPI, vous devez, pour chacune des colonnes, mettre en correspondance le nom de la matière dans GEPI (menu déroulant) avec le nom de l'option tel qu'elle est définie dans le fichier GEP.</p>";
}
?>
<p>
<table border=1 cellspacing=2 cellpadding=5>
<tr><td><b>Nom GEPI de la matière : </b></td>
<?php
$i="0";
while ($i < $nb_options) {
    $temp = "matiere".$i;
    echo "<td><span class=\"small\">";
    echo "<select size=1 name='$temp'>";
    $callmat = mysql_query("SELECT DISTINCT m.* FROM matieres m, j_classes_matieres_professeurs j WHERE (j.id_classe='$id_classe' and j.id_matiere=m.matiere) ORDER BY m.nom_complet");

    $nb_matieres = mysql_num_rows($callmat);
    echo "<option value=''>(vide)</option>";
    $j = 0;
    while ($j < $nb_matieres){
        $matiere_list = mysql_result($callmat, $j, "matiere");
        echo "<option value=$matiere_list>$matiere_list</option>";
        $j++;
    }
    echo "</select></span></td>";
    $i++;
}
?>
</tr>
<tr>
    <td><p class="small"><b>Nom GEP de l'option :</b></p></td>
    <?php
    $i="0";
    while ($i < $nb_options) {
        echo "<td><p class=\"small\"><b>Nom GEP : $tab_options[$i]</b></p></td>";
        $i++;
    }
    ?>
</tr>
<?php
$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login) ORDER BY nom, prenom");
$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
$i = "0";
while($i < $nombre_lignes) {
    $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
    $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
    $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
    echo "<tr><td><p class=\"small\">$current_eleve_nom $current_eleve_prenom</p></td>";
    $call_data = mysql_query("SELECT ELEOPT1,ELEOPT2,ELEOPT3,ELEOPT4,ELEOPT5,ELEOPT6,ELEOPT7,ELEOPT8,ELEOPT9,ELEOPT10,ELEOPT11,ELEOPT12 FROM temp_gep_import WHERE LOGIN = '$current_eleve_login'");
    $j="0";
    while ($j < $nb_options) {
        $ind = 1;
        $affiche = 'no';
        while ($ind < 13) {
            $optionx = "ELEOPT".$ind;
            $temp = @mysql_result($call_data, 0, $optionx);
            if ($tab_options[$j] == $temp) {
                $affiche = "yes";
            }
            $ind++;
        }
        if ($affiche == 'yes') {
                echo "<td><p class=\"small\"><font color=\"green\">Oui</font></p></td>";
        } else {
            echo "<td><p class=\"small\"><font color=\"red\">Non</font></p></td>";
        }

        $j++;
    }
    echo "<tr>";
$i++;
}
?>
</table>
<input type=hidden name=nb_options value=<?php echo "$nb_options";?>>
<input type=hidden name=is_posted value="yes">
<input type=hidden name=id_classe value=<?php echo "$id_classe";?>>
<input type=submit value=Enregistrer>
</form>
</p>
<?php require("../lib/footer.inc.php");?>