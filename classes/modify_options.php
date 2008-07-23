<?php
/*
 * Last modification  : 04/04/2005
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
    $msg = "";
    $appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' and c.login = e.login)");
    $nombre_lignes = mysql_num_rows($appel_donnees_eleves);
    $i = "0";
    while($i < $nombre_lignes) {
        $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
        $j="1";
        while ($j < $nb_periode) {
            //
            // on teste si l'élève appartient à la classe pour la période en cours
            //
            $call_trim = mysql_query("SELECT periode FROM j_eleves_classes WHERE (id_classe = '$id_classe' and periode = '$j' and login = '$current_eleve_login')");
            $nb_ligne = mysql_num_rows($call_trim);
            if ($nb_ligne != 0) {
                // si l'élève appartient à la classe pour la période en cours, on continue
                $temp = $current_eleve_login."_".$j;
                $option_eleve[$j] = isset($_POST[$temp])?$_POST[$temp]:NULL;
                if ($option_eleve[$j] == 'yes') {
                    $delete = mysql_query("DELETE FROM j_eleves_matieres WHERE (matiere='$current_matiere' and login='$current_eleve_login' and periode = '$j') ");
                } else {
                    $test = mysql_query("SELECT * FROM j_eleves_matieres WHERE (matiere='$current_matiere' and login='$current_eleve_login'  and periode = '$j')");
                    $nb_test = mysql_num_rows($test);
                    if ($nb_test == 0) {
                        $test1 = mysql_query("SELECT * FROM matieres_notes WHERE (matiere='$current_matiere' and login='$current_eleve_login' and periode = '$j')");
                        $nb_test1 = mysql_num_rows($test1);
                        $test2 = mysql_query("SELECT * FROM matieres_appreciations WHERE (matiere='$current_matiere' and login='$current_eleve_login' and periode = '$j')");
                        $nb_test2 = mysql_num_rows($test2);
                        if (($nb_test1 != 0) or ($nb_test2 != 0)) {
                            $msg = $msg."--> Impossible de supprimer cette option pour l'élève $current_eleve_login car des moyennes ou appréciations ont déjà été rentrées en $current_matiere pour la période $j ! Commencez par supprimer ces données !<br />";
                        } else {
                            $reg = mysql_query("INSERT INTO j_eleves_matieres SET matiere='$current_matiere' , login='$current_eleve_login', periode='$j'");
                        }
                    }
                }
            }

        $j++;
        }
    $i++;
    }
    $affiche_message = 'yes';
}
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Gestion des classes | Modification des options";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<script language=javascript>

function CocheCase(boul) {

 nbelements = document.formulaire.elements.length;

 for (i=0;i<nbelements;i++) {

   if (document.formulaire.elements[i].type =='checkbox')

      document.formulaire.elements[i].checked = boul ;



 }

}



function CochePeriode() {

    nbParams = CochePeriode.arguments.length;

    for (var i=0;i<nbParams;i++) {

        theElement = CochePeriode.arguments[i];

        if (document.formulaire.elements[theElement])

            document.formulaire.elements[theElement].checked = true;

    }

}



function DecochePeriode() {

    nbParams = DecochePeriode.arguments.length;

    for (var i=0;i<nbParams;i++) {

        theElement = DecochePeriode.arguments[i];

        if (document.formulaire.elements[theElement])

            document.formulaire.elements[theElement].checked = false;

    }

}

</script>

<?php

$call_nom_class = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");

$classe = mysql_result($call_nom_class, 0, 'classe');

?>

<form enctype="multipart/form-data" action="modify_options.php" name="formulaire" method=post>

<p class=bold><a href="modify_class.php?id_classe=<?echo $id_classe?>"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>|<a href="help.php"> Aide </a>| <input type='submit' value='Enregistrer' />
</p>
<b><a href="javascript:CocheCase(true)">Tout cocher</a> - <a href="javascript:CocheCase(false)">Tout décocher</a></b>

<?php
echo "<p class='grand'>Classe : $classe | Matière : $current_matiere</p>";
?>

<p>
<table border=0 cellspacing=2 cellpadding=5>
<tr>
    <td><p><b>Nom Prenom</b></p></td>
    <?php
    $i="1";
    while ($i < $nb_periode) {
        echo "<td><p>$nom_periode[$i]</p></td>";
        $i++;
    }
    ?>
    <td>&nbsp;</td>
</tr>



<?php
$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login) ORDER BY nom, prenom");

$nombre_lignes = mysql_num_rows($appel_donnees_eleves);



    $i=0;

    $elements = array();

    for ($j=0;$j<$nb_periode;$j++) {

        $elements[$j] = null;

    }



    while($i < $nombre_lignes) {

        $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");

        $k = 1;

        while ($k < $nb_periode) {

            $elements[$k] .= "'" . $current_eleve_login . "_" . $k . "',";

            $k++;

        }



        $i++;

    }



    echo "<tr><td>&nbsp;</td>";

    $k = 1;

        while ($k < $nb_periode) {

            echo "<td>";

            $elements[$k] = substr($elements[$k], 0, -1);

            echo "<a href=\"javascript:CochePeriode($elements[$k])\">Tout</a> <br/> <a href=\"javascript:DecochePeriode($elements[$k])\">Aucun</a></td>";

            $k++;

        }

    echo "<td>&nbsp;</td>";

    echo "</tr>";





    echo "<tr><td>Nb. inscrits :</td>";

    $i="1";

    while ($i < $nb_periode) {

        $testquery = mysql_query("SELECT j.login FROM j_eleves_matieres j, j_eleves_classes c WHERE (".

                    "j.login = c.login AND " .

                    "c.id_classe = '" . $id_classe . "' AND " .

                    "j.matiere = '" . $current_matiere . "' AND " .

                    "j.periode = '" . $i . "' AND " .

                    "c.periode = '" . $i . "'" .

                    ")");



        $test = mysql_num_rows($testquery);



        $total_eleves_periode = mysql_num_rows(mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND c.login = e.login AND c.periode = '" . $i . "')"));



        $inscrits = $total_eleves_periode-$test;



        echo "<td><p>$inscrits</p></td>";

        $i++;

    }

    echo "<td>&nbsp;</td>";

    echo "</tr>";



$i = "0";

while($i < $nombre_lignes) {

    $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");

    $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");

    $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");

    echo "<tr><td><p>$current_eleve_nom $current_eleve_prenom</p></td>";

    $j="1";



    $flag_elements = array();



    while ($j < $nb_periode) {

        $call_trim = mysql_query("SELECT periode FROM j_eleves_classes WHERE (id_classe = '$id_classe' and periode = '$j' and login = '$current_eleve_login')");

        $nb_ligne = mysql_num_rows($call_trim);

        if ($nb_ligne != 0) {

            $option_eleve_login[$j] = $current_eleve_login."_".$j;

            $current_eleve_option_query = mysql_query("SELECT * FROM j_eleves_matieres j, j_eleves_classes c WHERE (j.login='$current_eleve_login' AND j.matiere='$current_matiere' AND j.periode = '$j')");

            $test = mysql_num_rows($current_eleve_option_query);

            if ($test != "0") {

                echo "<td><input type=checkbox value=yes name=$option_eleve_login[$j]></td>";

            } else {

                echo "<td><input type=checkbox value=yes name=$option_eleve_login[$j] CHECKED></td>";

            }



            $flag_elements[] = $option_eleve_login[$j];



        } else {

            echo "<td><p>-</p></td>";

        }

        $j++;

    }

    $elementlist = null;

    foreach($flag_elements as $element) {

        $elementlist .= "'" . $element . "',";

    }

    $elementlist = substr($elementlist, 0, -1);

    echo "<td><a href=\"javascript:CochePeriode($elementlist)\">Tout</a> // <a href=\"javascript:DecochePeriode($elementlist)\">Aucun</a></td>";



$i++;

}

?>
</table>
<input type=hidden name=is_posted value="yes">
<input type=hidden name=current_matiere value=<?php echo "$current_matiere";?>>
<input type=hidden name=id_classe value=<?php echo "$id_classe";?>>
<center><input type=submit value=Enregistrer></center>
</form>
</p>
<?php require("../lib/footer.inc.php");?>