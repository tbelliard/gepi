<?php

/*

 * Last modification  : 21/03/2005

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



if (isset($_POST['is_posted']) and $_POST['is_posted'] == "yes") {

    if ($_SESSION['statut'] == "cpe") {

        $quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num')");

    } else {

        $quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num')");

}





    $quels_eleves = mysql_query("SELECT e.login FROM eleves e, j_eleves_classes c WHERE (c.id_classe='$id_classe' AND e.login = c.login AND c.periode='$periode_num')");

    $lignes = mysql_num_rows($quels_eleves);

    $j = '0';

    while($j < $lignes) {

        $reg_eleve_login = mysql_result($quels_eleves, $j, "login");

        $nom_log_nb_abs = $reg_eleve_login."_nb_abs";



        $nb_absences = $_POST[$nom_log_nb_abs];



        $nom_log_nb_nj = $reg_eleve_login."_nb_nj";

        $nb_nj = $_POST[$nom_log_nb_nj];



        $nom_log_nb_retard = $reg_eleve_login."_nb_retard";

        $nb_retard = $_POST[$nom_log_nb_retard];



        $nom_log_ap = $reg_eleve_login."_ap";

        $ap = $_POST[$nom_log_ap];


        if (!(ereg ("^[0-9]{1,}$", $nb_absences))) {

            $nb_absences = '';

        }

        if (!(ereg ("^[0-9]{1,}$", $nb_nj))) {

            $nb_nj = '';

        }

        if (!(ereg ("^[0-9]{1,}$", $nb_retard))) {

            $nb_retard = '';

        }



        $test_eleve_nb_absences_query = mysql_query("SELECT * FROM absences WHERE (login='$reg_eleve_login' AND periode='$periode_num')");

        $test_nb = mysql_num_rows($test_eleve_nb_absences_query);

        if ($test_nb != "0") {

            $register = mysql_query("UPDATE absences SET nb_absences='$nb_absences', non_justifie='$nb_nj', nb_retards='$nb_retard', appreciation='$ap' WHERE (login='$reg_eleve_login' AND periode='$periode_num')");

        } else {

            $register = mysql_query("INSERT INTO absences SET login='$reg_eleve_login', periode='$periode_num',nb_absences='$nb_absences',non_justifie='$nb_nj', nb_retards='$nb_retard',appreciation='$ap'");

            }

    if (!$register) {

            $msg = "Erreur lors de l'enregistrement des données";

        }

    $j++;

    }

    $affiche_message = 'yes';

}

$themessage  = 'Des champs ont été modifiés. Voulez-vous vraiment quitter sans enregistrer ?';

$message_enregistrement = 'Les modifications ont été enregistrées !';

//**************** EN-TETE *****************

$titre_page = "Saisie des absences";

require_once("../lib/header.inc");

//**************** FIN EN-TETE *****************

?>

<script type="text/javascript" language="javascript">

change = 'no';

</script>



<form enctype="multipart/form-data" action="saisie_absences.php" method=post>

<p class=bold>|<a href="index.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">Choisir une autre classe</a>|

<a href="index.php?id_classe=<?php echo $id_classe; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">Choisir une autre période</a>|<input type=submit value=Enregistrer></p>





<?php

$call_classe = mysql_query("SELECT classe FROM classes WHERE id = '$id_classe'");

$classe = mysql_result($call_classe, "0", "classe");

?>

<p><b>Classe de <?php echo "$classe"; ?> - Saisie des absences : <?php $temp = strtolower($nom_periode[$periode_num]); echo "$temp"; ?></b>

<br />

<table border=1 cellspacing=2 cellpadding=5>



<tr>

    <td><b>Nom Prénom</b></td>

    <td><b>Nb. total de 1/2 journées d'absence</b></td>

    <td><b>Nb. absences non justifiées</b></td>

    <td><b>Nb. de retard</b></td>

    <td><b>Observations</b></td>

</tr>



<?php



if ($_SESSION['statut'] == "cpe") {

        $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c, j_eleves_cpe j WHERE (c.id_classe='$id_classe' AND j.e_login = c.login AND e.login = j.e_login AND j.cpe_login = '".$_SESSION['login'] . "' AND c.periode = '$periode_num') order by e.nom, e.prenom");

    } else {

        $appel_donnees_eleves = mysql_query("SELECT e.* FROM eleves e, j_eleves_classes c WHERE ( c.id_classe='$id_classe' AND c.login = e.login AND c.periode='$periode_num') order by e.nom, e.prenom");

}



$nombre_lignes = mysql_num_rows($appel_donnees_eleves);

$i = '0';

$num_id=10;

while($i < $nombre_lignes) {

    $current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");

    $current_eleve_absences_query = mysql_query("SELECT * FROM  absences WHERE (login='$current_eleve_login' AND periode='$periode_num')");

    $current_eleve_nb_absences = @mysql_result($current_eleve_absences_query, 0, "nb_absences");

    $current_eleve_nb_nj = @mysql_result($current_eleve_absences_query, 0, "non_justifie");

    $current_eleve_nb_retards = @mysql_result($current_eleve_absences_query, 0, "nb_retards");

    $current_eleve_ap_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");

    $current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");

    $current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");

    $current_eleve_login_nb = $current_eleve_login."_nb_abs";

    $current_eleve_login_nj = $current_eleve_login."_nb_nj";

    $current_eleve_login_retard = $current_eleve_login."_nb_retard";

    $current_eleve_login_ap = $current_eleve_login."_ap";

    echo "<tr><td>$current_eleve_nom $current_eleve_prenom</td>\n";

    echo "<td><input id=\"".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_nb value=\"".$current_eleve_nb_absences."\" onchange=\"changement()\"></td>\n";

    echo "<td><input id=\"1".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_nj value=\"".$current_eleve_nb_nj."\" onchange=\"changement()\"></td>\n";

    echo "<td><input id=\"2".$num_id."\" onKeyDown=\"clavier(this.id,event);\" type=text size=4 name=$current_eleve_login_retard value=\"".$current_eleve_nb_retards."\" onchange=\"changement()\"></td>\n";

    echo "<td><textarea id=\"3".$num_id."\" onKeyDown=\"clavier(this.id,event);\" onchange=\"changement()\" name=$current_eleve_login_ap rows=2 cols=50  wrap=\"virtual\">$current_eleve_ap_absences</textarea></td></tr>\n";

$i++;

$num_id++;

}



?>

</table>

<input type=hidden name=is_posted value="yes">

<input type=hidden name=id_classe value=<?php echo "$id_classe";?>>

<input type=hidden name=periode_num value=<?php echo "$periode_num";?>>

<center><div id="fixe"><input type=submit value=Enregistrer></div></center>

</form>



</p>

</body>

</html>