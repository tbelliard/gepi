<?php
/*
 * Last modification  : 24/05/2006
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

if (isset($add_prof) and ($add_prof == "yes")) {
    // On commence par vérifier que le professeur n'est pas déjà présent dans cette liste.
    $test = mysql_query("SELECT * FROM j_aid_utilisateurs WHERE (id_utilisateur = '$reg_prof_login' and id_aid = '$aid_id' and indice_aid='$indice_aid')");
    $test2 = mysql_num_rows($test);
    if ($test2 != "0") {
        $msg = "Le professeur que vous avez tenté d'ajouter appartient déjà à cet AID";
    } else {
        if ($reg_prof_login != '') {
            $reg_data = mysql_query("INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$reg_prof_login', id_aid = '$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout du professeur !"; } else { $msg = "Le professeur a bien été ajouté !"; }
        }
    }
    $flag = "prof";
}

if (isset($add_eleve) and ($add_eleve == "yes")) {
    // On commence par vérifier que l'élève n'est pas déjà présent dans cette liste, ni dans aucune.
    $test = mysql_query("SELECT * FROM j_aid_eleves WHERE (login='$reg_add_eleve_login' and indice_aid='$indice_aid')");
    $test2 = mysql_num_rows($test);
    if ($test2 != "0") {
        $msg = "L'élève que vous avez tenté d'ajouter appartient déjà à une AID";
    } else {
        if ($reg_add_eleve_login != '') {
            $reg_data = mysql_query("INSERT INTO j_aid_eleves SET login='$reg_add_eleve_login', id_aid='$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout de l'élève"; } else { $msg = "L'élève a bien été ajouté."; }
        }
    }
    $flag = "eleve";
}


// On appelle les informations de l'aid pour les afficher :
$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @mysql_result($call_data, 0, "nom");

$calldata = mysql_query("SELECT nom FROM aid where (id = '$aid_id' and indice_aid='$indice_aid')");
$aid_nom = mysql_result($calldata, 0, "nom");
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
//**************** EN-TETE *********************
$titre_page = "Gestion des $nom_aid | Modifier les $nom_aid";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>

<p class=bold>|<a href="index2.php?indice_aid=<?php echo $indice_aid; ?>">Retour</a>|</p>

<?php if ($flag == "prof") { ?>
   <p class='grand'><?php echo "$nom_aid  $aid_nom";?></p>
    <p><span class='bold'>Liste des professeurs responsables :</span>
    <?php
    $vide = 1;
    $call_liste_data = mysql_query("SELECT u.login, u.prenom, u.nom FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')  order by u.nom, u.prenom");
    $nombre = mysql_num_rows($call_liste_data);
    $i = "0";
    while ($i < $nombre) {
        $vide = 0;
        $login_prof = mysql_result($call_liste_data, $i, "login");
        $nom_prof = mysql_result($call_liste_data, $i, "nom");
        $prenom_prof = @mysql_result($call_liste_data, $i, "prenom");
        echo "<br /><b>";
        echo "$nom_prof $prenom_prof</b> | <a href='../lib/confirm_query.php?liste_cible=$login_prof&amp;liste_cible2=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_prof_aid'><font size=2>supprimer</font></a>";
    $i++;
    }
    if ($vide == 1) {
        echo "<br /><font color = red>Il n'y a pas actuellement de professeur responsable !</font>";
    }
    ?>
    <br /><br /><span class='bold'>Ajouter un professeur responsable à la liste de l'AID :</span>
    </p>
    <form enctype="multipart/form-data" action="modify_aid.php" method=post>
    <select size=1 name=reg_prof_login>
    <!--option value=''><p>(aucun)</p></option-->
    <option value=''>(aucun)</option>
    <?php
    $call_prof = mysql_query("SELECT login, nom, prenom FROM utilisateurs WHERE  etat!='inactif' order by nom");
    $nombreligne = mysql_num_rows($call_prof);
    $i = "0" ;
    while ($i < $nombreligne) {
        $login_prof = mysql_result($call_prof, $i, 'login');
        $nom_el = mysql_result($call_prof, $i, 'nom');
        $prenom_el = mysql_result($call_prof, $i, 'prenom');
        //echo "<option value=$login_prof><p>$nom_el  $prenom_el </p></option>";
        echo "<option value=$login_prof>$nom_el  $prenom_el</option>\n";
    $i++;
    }
    ?>
    </select>
    <input type=hidden name=add_prof value=yes />
    <input type=hidden name=aid_id value="<?php echo $aid_id;?>" />
    <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />
    <input type=submit value='Enregistrer' />
    </form>
<?php }

if ($flag == "eleve") { ?>
    <p class='grand'><?php echo "$nom_aid  $aid_nom";?></p>

    <p><span class = 'bold'>Liste des élèves de l'AID <?php echo $aid_nom ?> :</span>
    <?php
    $vide = 1;
    // appel de la liste des élèves de l'AID :
    $call_liste_data = mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e, j_aid_eleves j WHERE (j.id_aid='$aid_id' and e.login=j.login and j.indice_aid='$indice_aid') ORDER BY nom, prenom");
    $nombre = mysql_num_rows($call_liste_data);
    $i = "0";
    while ($i < $nombre) {
        $vide = 0;
        $login_eleve = mysql_result($call_liste_data, $i, "login");
        $nom_eleve = mysql_result($call_liste_data, $i, "nom");
        $prenom_eleve = @mysql_result($call_liste_data, $i, "prenom");

        $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$login_eleve' and j.id_classe = c.id) order by j.periode DESC");
        $classe_eleve = @mysql_result($call_classe, '0', "classe");
        echo "<br /><b>";
        echo "$nom_eleve $prenom_eleve</b>, $classe_eleve   | <a href='../lib/confirm_query.php?liste_cible=$login_eleve&amp;liste_cible2=$aid_id&amp;liste_cible3=$indice_aid&amp;action=del_eleve_aid'><font size=2>supprimer</font></a>";
    $i++;
    }
    if ($vide == 1) {
        echo "<br /><font color = red>Il n'y a pas actuellement d'élèves dans cette AID !</font>";
    }
    $call_eleve = mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e LEFT JOIN j_aid_eleves j ON (e.login = j.login  and j.indice_aid='$indice_aid') WHERE j.login is null order by e.nom, e.prenom");
    $nombreligne = mysql_num_rows($call_eleve);
    if ($nombreligne != 0) {
        echo "<p><span class = 'bold'>Ajouter un élève à la liste de l'AID :</span>";
        echo "<br /><form enctype=\"multipart/form-data\" action=\"modify_aid.php\" method=post>";
        echo "<select size=1 name=reg_add_eleve_login>";
        //echo "<option value=''><p>(aucun)</p></option>";
        echo "<option value=''>(aucun)</option>";
        $i = "0" ;
        while ($i < $nombreligne) {
            $eleve = mysql_result($call_eleve, $i, 'login');
            $nom_el = mysql_result($call_eleve, $i, 'nom');
            $prenom_el = mysql_result($call_eleve, $i, 'prenom');

            $call_classe = mysql_query("SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @mysql_result($call_classe, '0', "classe");
            echo "<option value=$eleve>$nom_el  $prenom_el $classe_eleve</option>\n";
        $i++;
        }
        ?>
        </select>
        <input type=hidden name=add_eleve value=yes />
        <input type=hidden name=indice_aid value=<?php echo $indice_aid;?> />
        <input type=hidden name=aid_id value="<?php echo $aid_id;?>" />
        <input type=submit value='Enregistrer' />
        </form>

<?php } else {
        echo "<p>Tous les élèves de la base ont une AID. Impossible d'ajouter un élève à cette AID !</p>";
    }
}
?>
</body>
</html>