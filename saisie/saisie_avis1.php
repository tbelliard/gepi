<?php
/*
 * Last modification  : 11/05/2006
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Laurent Viénot-Hauger
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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
// initialisation
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);

include "../lib/periodes.inc.php";

if (isset($_POST['is_posted'])) {
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        $quels_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login
           ) ORDER BY 'nom'");
    } else {
        $quels_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           p.login = c.login AND
           p.professeur = '".$_SESSION['login']."'
           ) ORDER BY 'nom'");
    }
    $lignes = mysql_num_rows($quels_eleves);
    $j = '0';
    $pb_record = 'no';
    while($j < $lignes) {
        $reg_eleve_login = mysql_result($quels_eleves, $j, "login");
        $i = '1';
        while ($i < $nb_periode) {
            if ($ver_periode[$i] != "O"){
                $call_eleve = mysql_query("SELECT login FROM j_eleves_classes WHERE (login = '$reg_eleve_login' and id_classe='$id_classe' and periode='$i')");
                $result_test = mysql_num_rows($call_eleve);
                if ($result_test != 0) {
                    $nom_log = $reg_eleve_login."_t".$i;
                    $avis = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
                    $test_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$reg_eleve_login' AND periode='$i')");
                    $test = mysql_num_rows($test_eleve_avis_query);
                    if ($test != "0") {
                        $register = mysql_query("UPDATE avis_conseil_classe SET avis='$avis',statut='' WHERE (login='$reg_eleve_login' AND periode='$i')");
                    } else {
                        $register = mysql_query("INSERT INTO avis_conseil_classe SET login='$reg_eleve_login',periode='$i',avis='$avis',statut=''");
                    }
                    if (!$register) {
                        $msg = "Erreur lors de l'enregistrement des données de la période $i";
                        $pb_record = 'yes';
                    }
                }
            }

            $i++;
        }
        $j++;
    }
    if ($pb_record == 'no') $affiche_message = 'yes';
}
$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Saisie des avis | Saisie";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>
<?php
// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}
?>
<form enctype="multipart/form-data" action="saisie_avis1.php" method=post>
<p class=bold><a href="saisie_avis.php" onclick="return confirm_abandon(this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Mes classes</a></p>

<?php
if ($id_classe) {
    $classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");
    ?>
    <p class= 'grand'>Avis du conseil de classe. Classe : <?php echo $classe; ?></p>
    <?php
    $test_periode_ouverte = 'no';
    $i = "1";
    while ($i < $nb_periode) {
        if ($ver_periode[$i] != "O") {
            $test_periode_ouverte = 'yes';
        }
        $i++;
    }
    ?>
    <?php
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login
           ) ORDER BY 'nom'");
    } else {
        $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
        WHERE (c.id_classe='$id_classe' AND
           c.login = e.login AND
           p.login = c.login AND
           p.professeur = '".$_SESSION['login']."'
           ) ORDER BY 'nom'");
    }
    $nombre_lignes = mysql_num_rows($appel_donnees_eleves);

    $i = "0";
    $num_id=10;
    while($i < $nombre_lignes) {
        $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
        $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
        $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
        echo "<table width=\"750\" border=1 cellspacing=2 cellpadding=5>";
        echo "<tr>";
        echo "<td width=\"200\"><div align=\"center\"><b>&nbsp;</b></div></td>";
        echo "<td><div align=\"center\"><b>$current_eleve_nom $current_eleve_prenom</b></div></td>";
        echo "</tr>";

        $k='1';
        while ($k < $nb_periode) {
            $current_eleve_avis_query[$k]= mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$k')");
            $current_eleve_avis_t[$k] = @mysql_result($current_eleve_avis_query[$k], 0, "avis");
            $current_eleve_login_t[$k] = $current_eleve_login."_t".$k;
            $k++;
        }

        $k='1';
        while ($k < $nb_periode) {
            if ($ver_periode[$k] != "N") {
                echo "<tr><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>";
            } else {
                echo "<tr><td>$nom_periode[$k]</td>";
            }
            if ($ver_periode[$k] != "O") {
                $call_eleve = mysql_query("SELECT login FROM j_eleves_classes WHERE (login = '$current_eleve_login' and id_classe='$id_classe' and periode='$k')");
                $result_test = mysql_num_rows($call_eleve);
                if ($result_test != 0) {
                    //echo "<td><textarea id=\"".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\"  name=\"no_anti_inject_".$current_eleve_login_t[$k]."\" rows=2 cols=120 wrap='virtual' onchange=\"changement()\">";
                    echo "<td><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\"  name=\"no_anti_inject_".$current_eleve_login_t[$k]."\" rows=2 cols=120 wrap='virtual' onchange=\"changement()\">";
                    echo "$current_eleve_avis_t[$k]";
                    echo "</textarea></td>";
                } else {
                    echo "<td><p>$current_eleve_avis_t[$k]&nbsp;</p></td>";
                }
            } else {
                echo "<td><p class=\"medium\">";
                echo "$current_eleve_avis_t[$k]";
                echo "</p></td>";
            }
            $k++;
        }
        echo "</tr>";
        $num_id++;
        $i++;
        echo"</table><br /><br />";
    }

    if ($test_periode_ouverte == 'yes') {
        ?>
        <input type=hidden name=is_posted value="yes" />
        <input type=hidden name=id_classe value=<?php echo "$id_classe";?> />
        <center><div id="fixe"><input type=submit value=Enregistrer /></div></center>
        <br /><br /><br /><br />
        <?php
    }
}

?>
</form>
<?php require("../lib/footer.inc.php");?>