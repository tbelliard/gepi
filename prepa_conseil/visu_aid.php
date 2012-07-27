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



//Initialisation

unset($indice_aid);

$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);

unset($aid_id);

$aid_id = isset($_POST["aid_id"]) ? $_POST["aid_id"] : (isset($_GET["aid_id"]) ? $_GET["aid_id"] : NULL);

unset($max_boucle);

$max_boucle = isset($_POST["max_boucle"]) ? $_POST["max_boucle"] : (isset($_GET["max_boucle"]) ? $_GET["max_boucle"] : NULL);

unset($choix_visu);

$choix_visu = isset($_POST["choix_visu"]) ? $_POST["choix_visu"] : (isset($_GET["choix_visu"]) ? $_GET["choix_visu"] : NULL);

$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;

$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;







include "../lib/periodes.inc.php";

// On appelle les informations de l'aid pour les afficher :

$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");

$nom_aid = @mysql_result($call_data, 0, "nom");

$note_max = @mysql_result($call_data, 0, "note_max");

$type_note = @mysql_result($call_data, 0, "type_note");

$display_begin = @mysql_result($call_data, 0, "display_begin");

$display_end = @mysql_result($call_data, 0, "display_end");





    //**************** EN-TETE *****************
if (!isset($bord))     $titre_page = "Visualisation des appréciations ".$nom_aid;
require_once("../lib/header.inc.php");
    //**************** FIN EN-TETE *****************

if (!isset($aid_id)) {
    ?><p class=bold><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Accueil</a></p><?php
    $call_prof_aid = mysql_query("SELECT a.nom, a.id FROM j_aid_utilisateurs j, aid a WHERE (j.id_utilisateur = '" . $_SESSION['login'] . "' and a.id = j.id_aid and a.indice_aid=j.indice_aid and j.indice_aid='$indice_aid') ORDER BY a.nom");
    $nombre_aid = mysql_num_rows($call_prof_aid);
    if ($nombre_aid == "0") {

        echo "<p>Vous n'êtes pas professeur responsable. Vous n'avez donc pas accès à ce module.</p></html></body>\n";

        die();

    } else {

        $i = "0";

        echo "<p>Vous êtes professeur responsable dans les $nom_aid :<br />\n";

        while ($i < $nombre_aid) {

            $aid_display = mysql_result($call_prof_aid, $i, "nom");

            $aid_id = mysql_result($call_prof_aid, $i, "id");

            echo "<br /><span class='bold'>$aid_display</span> --- <a href='visu_aid.php?aid_id=$aid_id&aid=yes&indice_aid=$indice_aid'>Visualiser les appréciations pour cette rubrique</a>\n";

        $i++;

        }

    echo "</p>\n";

    }

} else if (!isset($choix_visu)) {

    echo "<p class=bold><a href=\"../accueil.php\">Accueil</a>|<a href=visu_aid.php?indice_aid=$indice_aid><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

    //

    // on calcule le nombre maximum de périodes dans les classes concernées par l'AID

    //

    $nom_table = "class_temp".SESSION_ID();

    $call_data = mysql_query("DROP TABLE IF EXISTS $nom_table");

    $call_data = mysql_query("CREATE TEMPORARY TABLE $nom_table (id_classe integer, num integer NOT NULL)");

    $call_data = mysql_query("SELECT c.* FROM classes c, j_eleves_classes cc, j_aid_eleves a WHERE (a.id_aid='$aid_id' and cc.login = a.login and cc.id_classe = c.id and a.indice_aid='$indice_aid')");

    $nombre_lignes = mysql_num_rows($call_data);

    $i = 0;

    while ($i < $nombre_lignes){

        $id_classe = mysql_result($call_data, $i, "id");

        $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");

        $k = mysql_num_rows($periode_query);

        $call_reg = mysql_query("insert into $nom_table Values('$id_classe', '$k')");

    $i++;

    }

    $call_data = mysql_query("SELECT max(num) as max FROM $nom_table");

    $nb_periode_max = mysql_result($call_data, 0, "max");



    //

    // On propose de selectionner les périodes à imprimer

    //



    echo "<form enctype=\"multipart/form-data\" action=\"visu_aid.php\" target=\"_blank\" method=\"post\" name=\"formulaire\">\n";

    $calldata = mysql_query("SELECT nom FROM aid where (id = '$aid_id'  and indice_aid='$indice_aid')");

    $aid_nom = mysql_result($calldata, 0, "nom");

    echo "<p class='bold'>Appréciations $nom_aid : $aid_nom</p>\n";

    echo "<p>Choisissez les données à imprimer ou à visualiser (vous pouvez cocher plusieurs cases) : </p>\n";

    $i=$display_begin;

    $max_boucle = min($display_end,$nb_periode_max)+1;



    while ($i < $max_boucle) {

        $name = "visu_app_".$i;

        echo "<p><INPUT TYPE=CHECKBOX NAME='$name' VALUE='yes'>Période $i - Extraire les appréciations<p>\n";

        if ($type_note == 'last') {$last_periode_aid = min($nb_periode_max,$display_end);}

        if (($type_note == 'every') or (($type_note == 'last') and ($i == $last_periode_aid))) {

            $name = "visu_note_".$i;

            echo "<p><INPUT TYPE=CHECKBOX NAME='$name' VALUE='yes'>Période $i - Extraire les notes<p>\n";

        }



    $i++;

    }



    echo "<b>Paramètres d'affichage</b><br />\n";

    echo "<br />\nLargeur en pixel du tableau : <input type=text name=larg_tab size=3 value=\"680\">";

    echo "<br />\nBords en pixel du tableau : <input type=text name=bord size=3 value=\"1\">";

    echo "<br />\n<br />\n<span class='small'><b>Remarque :</b> après validation, les résultats s'affichent sous la forme d'un tableau dans une nouvelle page sans en-tête. Pour revenir à cet écran, il vous suffira de fermer la fenêtre (croix en haut à droite).</span>";



    echo "<center><input type=submit value=Valider></center>\n";

    echo "<input type=hidden name=aid_id value=$aid_id>\n";

    echo "<input type=hidden name=indice_aid value=$indice_aid>\n";

    echo "<input type=hidden name=max_boucle value=$max_boucle>\n";

    echo "<input type=hidden name=choix_visu value=yes>\n";

    echo "</form>\n";

} else {

    $appel_login_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_aid_eleves j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid' and j.login=e.login) ORDER BY e.nom,e.prenom");

    $nombre_eleves = mysql_num_rows($appel_login_eleves);

    $indice_col = 1;

    //

    // Calcul du nombre de colonnes à afficher et définition de la première ligne à afficher

    //

    $ligne1[1] = "Nom Prénom";

    $k = $display_begin;

    while ($k < $max_boucle) {

        $temp = "visu_app_".$k;

        if ((isset($_POST[$temp])) and ($_POST[$temp] == 'yes')) {

            $indice_col++;

            $ligne1[$indice_col] = "Période ".$k;

        }

        $temp = "visu_note_".$k;

        if ((isset($_POST[$temp])) and ($_POST[$temp] == 'yes')) {

            $indice_col++;

            $ligne1[$indice_col] = "Période ".$k;

        }

        $k++;

    }



    $i = "0";

    while($i < $nombre_eleves) {

        $login_eleve[$i] = mysql_result($appel_login_eleves, $i, "login");

        $col[1][$i] = mysql_result($appel_login_eleves, $i, "prenom")." ".mysql_result($appel_login_eleves, $i, "nom");

        $k = $display_begin;

        $j=1;

        while ($k < $max_boucle) {

            $temp = "visu_app_".$k;

            if ((isset($_POST[$temp])) and ($_POST[$temp] == 'yes')) {

                $j++;

                $app_query = mysql_query("SELECT appreciation FROM aid_appreciations WHERE (login='$login_eleve[$i]' AND periode='$k' AND id_aid = '$aid_id' and indice_aid='$indice_aid')");

                $app = @mysql_result($app_query, 0, "appreciation");

                if ($app != '') {

                    $col[$j][$i] = $app;

                } else {

                    $col[$j][$i] = '-';

                }

            }

            $temp = "visu_note_".$k;

            if ((isset($_POST[$temp])) and ($_POST[$temp] == 'yes')) {

                $j++;

                $note_query = mysql_query("SELECT note, statut FROM aid_appreciations WHERE (login='$login_eleve[$i]' AND periode='$k' AND id_aid = '$aid_id' and indice_aid='$indice_aid')");

                $note = @mysql_result($note_query, 0, "note");

                $statut = @mysql_result($note_query, 0, "statut");

                if ($note !='') {

                    if ($statut == 'other') {

                        $col[$j][$i] = "&nbsp;";

                    } else if ($statut != '') {

                            $col[$j][$i] = "<center>".$statut."</center>";

                    } else {

                        $col[$j][$i] = "<center>".$note." / ".$note_max."</center>";

                    }

                } else {

                    $col[$j][$i] = "<center>&nbsp;</center>";

                }

            }



            $k++;

        }

    $i++;

    }

    //

    // Affichage du tableau

    //



    $calldata = mysql_query("SELECT nom FROM aid where (id = '$aid_id'  and indice_aid='$indice_aid')");

    $aid_nom = mysql_result($calldata, 0, "nom");

    echo "<p class='bold'>" . $_SESSION['nom'] . " " . $_SESSION['prenom'] . " | Année : ".getSettingValue("gepiYear")." | Appréciations $nom_aid : $aid_nom</p>";

    echo "</p>\n";

    affiche_tableau($nombre_eleves, $indice_col, $ligne1, $col, $larg_tab, $bord,0,0,"");

}
require("../lib/footer.inc.php");
?>