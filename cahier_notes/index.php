<?php
/*
 * Last modification  : 07/08/2006
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

//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
    die("Le module n'est pas activé.");
}

unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);

if (is_numeric($id_groupe) && $id_groupe > 0) {
    $current_group = get_group($id_groupe);
}

// On teste si le carnet de notes appartient bien à la personne connectée
if ((isset($_POST['id_racine'])) or (isset($_GET['id_racine']))) {
    $id_racine = isset($_POST['id_racine']) ? $_POST['id_racine'] : (isset($_GET['id_racine']) ? $_GET['id_racine'] : NULL);
    if (!(Verif_prof_cahier_notes ($_SESSION['login'],$id_racine))) {
        $mess=rawurlencode("Vous tentez de pénétrer dans un carnet de notes qui ne vous appartient pas !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

//**************** EN-TETE *****************
$titre_page = "Carnet de notes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************

//-----------------------------------------------------------------------------------
if (isset($_GET['id_groupe']) and isset($_GET['periode_num'])) {
    $id_groupe = $_GET['id_groupe'];
    $periode_num = $_GET['periode_num'];
    $login_prof = $_SESSION['login'];
    $appel_cahier_notes = mysql_query("SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')");
    $nb_cahier_note = mysql_num_rows($appel_cahier_notes);
    if ($nb_cahier_note == 0) {
        $nom_complet_matiere = $current_group["matiere"]["nom_complet"];
        $nom_court_matiere = $current_group["matiere"]["matiere"];
        $reg = mysql_query("INSERT INTO cn_conteneurs SET id_racine='', nom_court='".traitement_magic_quotes($current_group["description"])."', nom_complet='". traitement_magic_quotes($nom_complet_matiere)."', description = '', mode = '2', coef = '1.0', arrondir = 's1', ponderation = '0.0', display_parents = '0', display_bulletin = '1', parent = '0'");
        if ($reg) {
            $id_racine = mysql_insert_id();
            $reg = mysql_query("UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine'");
            $reg = mysql_query("INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine'");
        }
    } else {
        $id_racine = mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
    }
}

// Recopie de la structure de la periode précédente
if ((isset($_GET['creer_structure'])) and ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2)) {
  function recopie_arbo($id_racine, $id_prec,$id_new) {
    global $vide;
    $query_cont = mysql_query("SELECT * FROM cn_conteneurs
    WHERE (
        id != id_racine and
        parent = '".$id_prec."'
        )");
    $nb_lignes = mysql_num_rows($query_cont);
    $i = 0;
    while ($i < $nb_lignes) {
        $id_prec = mysql_result($query_cont,$i,'id');
        $val2 = mysql_result($query_cont,$i,'id_racine');
        $val3 = mysql_result($query_cont,$i,'nom_court');
        $val4 = mysql_result($query_cont,$i,'nom_complet');
        $val5 = mysql_result($query_cont,$i,'description');
        $val6 = mysql_result($query_cont,$i,'mode');
        $val7 = mysql_result($query_cont,$i,'coef');
        $val8 = mysql_result($query_cont,$i,'arrondir');
        $val9 = mysql_result($query_cont,$i,'ponderation');
        $val10 = mysql_result($query_cont,$i,'display_parents');
        $val11 = mysql_result($query_cont,$i,'display_bulletin');
        $val12 = mysql_result($query_cont,$i,'parent');
        $query_insert = mysql_query("INSERT INTO cn_conteneurs
        set id_racine = '".$id_racine."',
        nom_court = '".traitement_magic_quotes($val3)."',
        nom_complet = '".traitement_magic_quotes($val4)."',
        description = '".traitement_magic_quotes($val5)."',
        mode = '".$val6."',
        coef = '".$val7."',
        arrondir = '".$val8."',
        ponderation = '".$val9."',
        display_parents = '".$val10."',
        display_bulletin = '".$val11."',
        parent = '".$id_new."' ");
        $vide = 'no';
        $id_new1 = mysql_insert_id();
        recopie_arbo($id_racine, $id_prec, $id_new1);
        $i++;
    }

  }

    $periode_num = $_GET['periode_num'];
    $id_cahier_prec = sql_query1("SELECT id_cahier_notes FROM cn_cahier_notes
    WHERE (
        id_groupe = '".$id_groupe."' and
        periode = '".($periode_num-1)."'
        )
    ");
    $vide = 'yes';
    recopie_arbo($id_racine,$id_cahier_prec,$id_racine);
    if ($vide == 'yes') echo "<p><center><b><font color='red'>Structure vide : aucune boîte n'a été crée dans le carnet de notes de la période précédente.</font></b></center></p><hr />";
}

if  (isset($id_racine) and ($id_racine!='')) {
    $appel_conteneurs = mysql_query("SELECT * FROM cn_conteneurs WHERE id ='$id_racine'");
    $nom_court = mysql_result($appel_conteneurs, 0, 'nom_court');

    $appel_cahier_notes = mysql_query("SELECT * FROM cn_cahier_notes WHERE id_cahier_notes = '$id_racine'");
    $id_groupe = mysql_result($appel_cahier_notes, 0, 'id_groupe');
    if (!isset($current_group)) $current_group = get_group($id_groupe);
    $periode_num = mysql_result($appel_cahier_notes, 0, 'periode');
    include "../lib/periodes.inc.php";

    //
    // Supression d'une évaluation
    //
    if ((isset($_GET['del_dev'])) and ($_GET['js_confirmed'] ==1)) {
        $temp = $_GET['del_dev'];

        $sql= mysql_query("SELECT id_conteneur FROM cn_devoirs WHERE id='$temp'");
        $id_cont = mysql_result($sql, 0, 'id_conteneur');
        $sql = mysql_query("DELETE FROM cn_notes_devoirs WHERE id_devoir='$temp'");
        $sql = mysql_query("DELETE FROM cn_devoirs WHERE id='$temp'");

        // On teste si le conteneur est vide
        $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_cont'");
        $nb_dev = mysql_num_rows($sql);
        $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_cont'");
        $nb_cont = mysql_num_rows($sql);
        if (($nb_dev == 0) or ($nb_cont == 0)) {
            $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_cont'");
        }

        // On teste si le carnet de notes est vide
        $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
        $nb_dev = mysql_num_rows($sql);
        $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
        $nb_cont = mysql_num_rows($sql);
        if (($nb_dev == 0) and ($nb_cont == 0)) {
            $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
        } else {
            $arret = 'no';
            mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
        }
    }
    //
    // Supression d'un conteneur
    //
    if ((isset($_GET['del_cont'])) and ($_GET['js_confirmed'] ==1)) {
        $temp = $_GET['del_cont'];
        $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$temp'");
        $nb_dev = mysql_num_rows($sql);
        $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$temp'");
        $nb_cont = mysql_num_rows($sql);
        if (($nb_dev != 0) or ($nb_cont != 0)) {
            echo "<script type=\"text/javascript\" language=\"javascript\">\n";
            echo 'alert("Impossible de supprimer une boîte qui n\'est pas vide !");\n';
            echo "</script>\n";
        } else {
            $sql = mysql_query("DELETE FROM cn_conteneurs WHERE id='$temp'");
            $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$temp'");
            // On teste si le carnet de notes est vide
            $sql= mysql_query("SELECT id FROM cn_devoirs WHERE id_conteneur='$id_racine'");
            $nb_dev = mysql_num_rows($sql);
            $sql= mysql_query("SELECT id FROM cn_conteneurs WHERE parent='$id_racine'");
            $nb_cont = mysql_num_rows($sql);
            if (($nb_dev == 0) and ($nb_cont == 0)) {
                $sql = mysql_query("DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_racine'");
            } else {
                $arret = 'no';
                mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_racine,$arret);
            }

        }
    }

    //echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"index.php\" method=\"POST\">\n";
    echo "<div class='norme'><p class='bold'>\n";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|\n";
    echo "<a href='index.php'> Mes enseignements </a>|\n";
    //echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'>" . $current_group["description"] . " : Choisir une autre période</a>|";
    echo "<a href='index.php?id_groupe=" . $current_group["id"] . "'> " . htmlentities($current_group["description"]) . " : Choisir une autre période</a>\n";
    if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
        //echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&mode_navig=retour_index'>Créer une boîte</a>|";

        //echo "<a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'>Créer une boîte</a>|\n";
        echo "<br/><a href='add_modif_conteneur.php?id_racine=$id_racine&amp;mode_navig=retour_index'> Créer un";
    if(getSettingValue("gepi_denom_boite_genre")=='f'){echo "e";}
    echo " ".htmlentities(strtolower(getSettingValue("gepi_denom_boite")))." </a>|\n";

        //echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&mode_navig=retour_index'>Créer une évaluation</a>|";
        echo "<a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_index'> Créer une évaluation </a>|\n";
        if ($periode_num!='1')  {
            $themessage = 'En cliquant sur OK, vous allez créer la même structure de boîtes que celle de la période précédente. Si des boîtes existent déjà, elles ne seront pas supprimées.';
            //echo "<a href='index.php?id_groupe=$id_groupe&periode_num=$periode_num&creer_structure=yes'  onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Créer la même structure que la période précédent</a>|";
            echo "<a href='index.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;creer_structure=yes'  onclick=\"return confirm_abandon (this, 'yes', '$themessage')\"> Créer la même structure que la période précédent </a>\n";
        }
    }
    //echo "</b>\n";
    echo "</p></div>\n";

    //echo "<h2 class='gepi'>Carnet de notes : ". $current_group["description"] . " ($nom_periode[$periode_num])</h2>\n";
    echo "<h2 class='gepi'>Carnet de notes : ". htmlentities($current_group["description"]) . " ($nom_periode[$periode_num])</h2>\n";
    //echo "<p class='bold'> Classe(s) : " . $current_group["classlist_string"] . " | Matière : " . $current_group["matiere"]["nom_complet"] . "(" . $current_group["matiere"]["matiere"] . ")";
    echo "<p class='bold'> Classe(s) : " . $current_group["classlist_string"] . " | Matière : " . htmlentities($current_group["matiere"]["nom_complet"]) . "(" . htmlentities($current_group["matiere"]["matiere"]) . ")";
    // On teste si le carnet de notes est partagé ou non avec d'autres utilisateurs
    $login_prof = $_SESSION['login'];
    if (count($current_group["profs"]["list"]) > 1) {
        echo " | Carnet de notes partagé avec : ";
        $flag = 0;
        foreach($current_group["profs"]["users"] as $prof) {
            $l_prof = $prof["login"];
            $nom_prof = $prof["nom"];
            $prenom_prof = $prof["prenom"];
            if ($l_prof != $login_prof) {
                if ($flag > 0) echo ", ";
                echo $prenom_prof." ".$nom_prof;
                $flag++;
            }
        }
    }
    echo "</p>\n";

    echo "<h3 class='gepi'>Liste des évaluations du carnet de notes</h3>\n";
    $empty = affiche_devoirs_conteneurs($id_racine,$periode_num, $empty, $current_group["classe"]["ver_periode"]["all"][$periode_num]);
    echo "</ul>\n";
    if ($empty == 'yes') echo "<p><b>Actuellement, aucune évaluation.</b> Vous devez créer au moins une évaluation.</p>\n";
    if ($empty != 'yes') {
        if ($current_group["classe"]["ver_periode"]["all"][$periode_num] >= 2) {
            echo "<h3 class='gepi'>Saisie du bulletin ($nom_periode[$periode_num])</h3>\n";
            echo "<ul><li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Saisie des moyennes</a></li>\n";
            echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Saisie des appréciations</a></li></ul>\n";
        } else {
            echo "<h3 class='gepi'>Visualisation du bulletin ($nom_periode[$periode_num])</h3>\n";
            echo "<ul><li><a href='../saisie/saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num&amp;retour_cn=yes'>Visualisation des moyennes</a> (<b>".$gepiClosedPeriodLabel."</b>).</li>\n";
            echo "<li><a href='../saisie/saisie_appreciations.php?id_groupe=$id_groupe&amp;periode_cn=$periode_num'>Visualisation des appréciations</a> (<b>".$gepiClosedPeriodLabel."</b>).</li></ul>\n";
        }

    }

}

if (isset($_GET['id_groupe']) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {

    $matiere_nom = $current_group["matiere"]["nom_complet"];
    $matiere_nom_court = $current_group["matiere"]["matiere"];

    $nom_classes = $current_group["classlist_string"];

    echo "<p class=bold>";
    echo "<a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil </a>|";
    echo "<a href='index.php'> Mes enseignements </a>|</p>\n";
    echo "<p class='bold'>Enseignement : ".htmlentities($current_group["description"])." (" . $current_group["classlist_string"] .")</p>\n";

    echo "<H3>Visualisation/modification - Choisissez la période : </H3>\n";
    $i="1";
    while ($i < ($current_group["nb_periode"])) {
        echo "<p><a href='index.php?id_groupe=$id_groupe&amp;periode_num=$i'>".ucfirst($current_group["periodes"][$i]["nom_periode"])."</a>";

	$sql="SELECT * FROM periodes WHERE num_periode='$i' AND id_classe='".$current_group["classes"]["list"][0]."' AND verouiller='N'";
	//echo "<br />$sql<br />";
	$res_test=mysql_query($sql);
	if(mysql_num_rows($res_test)==0){
		echo " (<i>période close</i>)";
	}

	echo "</p>\n";
    $i++;
    }
    echo "<H3>Visualisation uniquement : </H3>\n";
    echo "<p><a href='toutes_notes.php?id_groupe=$id_groupe'>Voir toutes les évaluations de l'année</a></p>\n";

}

if (!(isset($_GET['id_groupe'])) and !(isset($_GET['periode_num'])) and !(isset($id_racine))) {
    ?>
    <p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a></p>
    <p>Accéder au carnet de notes : </p>
    <?php
    $groups = get_groups_for_prof($_SESSION["login"]);

    if (empty($groups)) {
        echo "<br /><br />";
        //echo "<b>Aucun cahier de texte n'est disponible.</b>";
        echo "<b>Aucun cahier de notes n'est disponible.</b>";
        echo "<br /><br />";
    }

    foreach($groups as $group) {
       echo "<p><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
       //echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . $group["description"] . "</a> <span class=small>(" . $group["matiere"]["nom_complet"] .")</span></p>";
       echo "<a href='index.php?id_groupe=" . $group["id"] ."'>" . htmlentities($group["description"]) . "</a>";
       echo "</span></p>\n";
    }
}
?>
</body>
</html>