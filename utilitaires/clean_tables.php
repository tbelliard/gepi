<?php
@set_time_limit(0);
/*
 * Last modification  : 11/05/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Check access

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$valid = isset($_POST["valid"]) ? $_POST["valid"] : 'no';

// header
$titre_page = "Vérification/nettoyage des tables de la base de données GEPI";
require_once("../lib/header.inc");

//$total_etapes = 8;
$total_etapes = 9;
$duree = 8;
if (!isset($_GET['cpt'])) {
    $cpt = 0;
} else {
    $cpt = $_GET['cpt'];
}

$maj=isset($_POST['maj']) ? $_POST['maj'] : (isset($_GET['maj']) ? $_GET['maj'] : NULL);
//if (($_POST['maj'])=="9") {
if ($maj=="9") {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
	echo "| <a href='clean_tables.php'>Retour page Vérification / Nettoyage des tables</a></p>\n";
}

function init_time() {
    global $TPSDEB,$TPSCOUR;
    list ($usec,$sec)=explode(" ",microtime());
    $TPSDEB=$sec;
    $TPSCOUR=0;
}

function current_time() {
    global $TPSDEB,$TPSCOUR;
    list ($usec,$sec)=explode(" ",microtime());
    $TPSFIN=$sec;
    if (round($TPSFIN-$TPSDEB,1)>=$TPSCOUR+1) //une seconde de plus
    {
    $TPSCOUR=round($TPSFIN-$TPSDEB,1);
    flush();
    }
}

function etape7() {
    global $TPSCOUR,$offset,$duree,$cpt,$nb_lignes;
    // Cas de la table matieres_appreciations
    $req = mysql_query("SELECT * FROM matieres_appreciations order by login,id_groupe,periode");
    $nb_lignes = mysql_num_rows($req);
    if ($offset >= $nb_lignes) {
        $offset = -1;
        return true;
        exit();
    }
    $fin = '';
    $cpt_2 = 0;
    while (($offset<$nb_lignes) and ($fin == '')) {
        $login_user = mysql_result($req,$offset,'login');
        $id_groupe = mysql_result($req,$offset,'id_groupe');
        $periode = mysql_result($req,$offset,'periode');

        // Détection des doublons
        $req2 = mysql_query("SELECT login FROM matieres_appreciations
        where
        login ='$login_user' and
        id_groupe ='$id_groupe' and
        periode ='$periode'
        ");
        $nb_lignes2 = mysql_num_rows($req2);
        if ($nb_lignes2 > "1") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'un doublon : login = $login_user - identifiant matiere = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_appreciations where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode' LIMIT $nb");
            $cpt++;
            $cpt_2++;
        }

        // Détection des données inutiles
        $test = mysql_query("select ma.login
       from
        matieres_appreciations ma,
        eleves e,
        j_eleves_classes jec,
        periodes p,
        matieres m,
        j_eleves_groupes jeg,
        groupes g
       where
        ma.login = '$login_user' and
        e.login = '$login_user' and
        jec.login = '$login_user' and
        jeg.login = '$login_user' and
        jec.id_classe = p.id_classe and
        jec.periode = '$periode' and
        p.num_periode = '$periode' and
        ma.periode = '$periode' and
        g.id = '$id_groupe' and
        jeg.id_groupe = '$id_groupe'
        ");
        $nb_lignes2 = mysql_num_rows($test);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - identifiant matière = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_appreciations where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode'");
            $cpt++;
            $cpt_2++;
        }
        // on regarde si l'élève suit l'option pour la période donnée.
        $test2 = mysql_query("select login from j_eleves_groupes where
        login = '$login_user' and
        id_groupe = '$id_groupe' and
        periode = '$periode'");
        $nb_lignes2 = mysql_num_rows($test2);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - identifiant matière = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_appreciations where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode'");
        }
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) { //on atteint la fin du temps imparti
            $fin = 'yes';
        } else {
            $offset++;
        }
    }
    $offset = $offset - $cpt_2;
    return true;
}
function etape8() {
    global $TPSCOUR,$offset,$duree,$cpt,$nb_lignes;
    // Cas de la table matieres_appreciations
    $req = mysql_query("SELECT * FROM matieres_notes order by login,matiere,periode");
    $nb_lignes = mysql_num_rows($req);
    if ($offset >= $nb_lignes) {
        $offset = -1;
        return true;
        exit();
    }
    $fin = '';
    $cpt_2 = 0;
    while (($offset<$nb_lignes) and ($fin == '')) {
        $login_user = mysql_result($req,$offset,'login');
        $id_groupe = mysql_result($req,$offset,'id_groupe');
        $periode = mysql_result($req,$offset,'periode');

         // Détection des doublons
        $req2 = mysql_query("SELECT login FROM matieres_notes
        where
        login ='$login_user' and
        id_groupe ='$id_groupe' and
        periode ='$periode'
        ");
        $nb_lignes2 = mysql_num_rows($req2);
        if ($nb_lignes2 > "1") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'un doublon : login = $login_user - identifiant matiere = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_notes where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode' LIMIT $nb");
            $cpt++;
            $cpt_2++;
        }


        // Détection des lignes inutiles
        $test = mysql_query("select mn.login
        from
        matieres_notes mn,
        eleves e,
        j_eleves_classes jec,
        periodes p,
        groupes g,
        j_eleves_groupes jeg

        where
        mn.login = '$login_user' and
        e.login = '$login_user' and
        jec.login = '$login_user' and
        jec.id_classe = p.id_classe and
        jec.periode = '$periode' and
        p.num_periode = '$periode' and
        mn.periode = '$periode' and
        g.id = '$id_groupe'
        ");
        $nb_lignes2 = mysql_num_rows($test);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - identifiant matière = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_notes where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode'");
            $cpt++;
            $cpt_2++;
        }
        // on regarde si l'élève suit l'option pour la période donnée.
        $test2 = mysql_query("select login from j_eleves_groupes where
        login = '$login_user' and
        id_groupe = '$id_groupe' and
        periode = '$periode'");
        $nb_lignes2 = mysql_num_rows($test2);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - identifiant matière = $id_matiere - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from matieres_notes where
            login ='$login_user' and
            id_groupe ='$id_groupe' and
            periode ='$periode'");
            $cpt++;
            $cpt_2++;
        }
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) { //on atteint la fin du temps imparti
            $fin = 'yes';
        } else {
            $offset++;
        }
    }
    $offset = $offset - $cpt_2;
    return true;
}
if (isset($_POST['maj']) and (($_POST['maj'])=="1")) {
    echo "<H2 align=\"center\">Etape 1/$total_etapes</H2>";
    $tab["j_aid_eleves"][0] = "aid"; //1ère table
    $tab["j_aid_eleves"][1] = "eleves"; // 2ème table
    $tab["j_aid_eleves"][2] = "id_aid"; // nom du champ de la table de liaison lié à la première table
    $tab["j_aid_eleves"][3] = "login";  // nom du champ de la table de liaison lié à la deuxième table
    $tab["j_aid_eleves"][4] = "id";  // nom du champ de la première table lié à la table de liaison
    $tab["j_aid_eleves"][5] = "login";  // nom du champ de la deuxième table lié à la table de liaison

    $tab["j_aid_utilisateurs"][0] = "aid"; //1ère table
    $tab["j_aid_utilisateurs"][1] = "utilisateurs"; // 2ème table
    $tab["j_aid_utilisateurs"][2] = "id_aid"; // nom du champ de la table de liaison lié à la première table
    $tab["j_aid_utilisateurs"][3] = "id_utilisateur";  // nom du champ de la table de liaison lié à la deuxième table
    $tab["j_aid_utilisateurs"][4] = "id";  // nom du champ de la première table lié à la table de liaison
    $tab["j_aid_utilisateurs"][5] = "login";  // nom du champ de la deuxième table lié à la table de liaison

    $tab["j_eleves_etablissements"][0] = "eleves"; //1ère table
    $tab["j_eleves_etablissements"][1] = "etablissements"; // 2ème table
    $tab["j_eleves_etablissements"][2] = "id_eleve"; // nom du champ de la table de liaison lié à la première table
    $tab["j_eleves_etablissements"][3] = "id_etablissement";  // nom du champ de la table de liaison lié à la deuxième table
    $tab["j_eleves_etablissements"][4] = "login";  // nom du champ de la première table lié à la table de liaison
    $tab["j_eleves_etablissements"][5] = "id";  // nom du champ de la deuxième table lié à la table de liaison

    $tab["j_eleves_regime"][0] = "eleves"; //1ère table
    $tab["j_eleves_regime"][1] = "eleves"; // 2ème table
    $tab["j_eleves_regime"][2] = "login"; // nom du champ de la table de liaison lié à la première table
    $tab["j_eleves_regime"][3] = "login";  // nom du champ de la table de liaison lié à la deuxième table
    $tab["j_eleves_regime"][4] = "login";  // nom du champ de la première table lié à la table de liaison
    $tab["j_eleves_regime"][5] = "login";  // nom du champ de la deuxième table lié à la table de liaison

    $tab[" j_professeurs_matieres"][0] = "utilisateurs"; //1ère table
    $tab[" j_professeurs_matieres"][1] = "matieres"; // 2ème table
    $tab[" j_professeurs_matieres"][2] = "id_professeur"; // nom du champ de la table de liaison lié à la première table
    $tab[" j_professeurs_matieres"][3] = "id_matiere";  // nom du champ de la table de liaison lié à la deuxième table
    $tab[" j_professeurs_matieres"][4] = "login";  // nom du champ de la première table lié à la table de liaison
    $tab[" j_professeurs_matieres"][5] = "matiere";  // nom du champ de la deuxième table lié à la table de liaison


    foreach ($tab as $key => $val) {
       echo "<H2>Vérification de la table ".$key."</H2>";
       // $key : le nom de la table de liaison
       // $val[0] : le nom de la première table
       // $val[1] : le nom de la deuxième table
       // etc...
       $req = mysql_query("SELECT * FROM $key order by $val[2],$val[3]");
       $nb_lignes = mysql_num_rows($req);
       $i = 0;
       $affiche = 'yes';
           while ($i < $nb_lignes) {
               $temp1 = mysql_result($req,$i,$val[2]);
               $temp2 = mysql_result($req,$i,$val[3]);

               $req2 = mysql_query("SELECT * FROM $key j, $val[0] t1, $val[1] t2

             where
               j.$val[2]=t1.$val[4] and
               j.$val[3]=t2.$val[5] and
               j.$val[2]='$temp1' and
               j.$val[3]='$temp2'
               ");
               $nb_lignes2 = mysql_num_rows($req2);
               // suppression des doublons
               if ($nb_lignes2 > "1") {
                   $nb = $nb_lignes2-1;
                   // cas j_aid_eleves et j_aid_utilisateurs
                   if (($key == "j_aid_eleves") or ($key == "j_aid_utilisateurs")) {
                       $indice_aid = mysql_result($req,$i,'indice_aid');
                       $test = sql_query1("select a.indice_aid from aid_config ac, aid a
                       where
                       ac.indice_aid ='$indice_aid' and
                       a.id = '$temp1'and
                       a.indice_aid = '$indice_aid' ");
                       if ($test == "-1") {
//                           echo "Suppression d'un doublon : $temp1 - $temp2<br />";
                           $del = mysql_query("delete from $key where ($val[2]='$temp1' and $val[3]='$temp2' and indice_aid='$indice_aid')");
                           $cpt++;
                       }
                   // autres cas
                   } else {
//                       echo "Suppression d'un doublon : $temp1 - $temp2<br />";
                       $del = mysql_query("delete from $key where ($val[2]='$temp1' and $val[3]='$temp2') LIMIT $nb");
                       $cpt++;
                   }
               }
               // On supprime les lignes inutiles
               if ($nb_lignes2 == "0") {
//                   echo "Suppression d'une ligne inutile : $temp1 - $temp2<br />";
                   $del = mysql_query("delete from $key where $val[2]='$temp1' and $val[3]='$temp2'");
                   $cpt++;
               }

               $i++;
           }
        if ($cpt != 0) {
            echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
        } else {
            echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";

      }
        echo "<b>La table $key est OK.</b><br />";
    }
    echo "<form action=\"clean_tables.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"maj\" value=\"2\" />";
    echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
    echo "</form>\n";

} else if (isset($_POST['maj']) and (($_POST['maj'])=="2")) {
    echo "<H2 align=\"center\">Etape 2/$total_etapes</H2>";
    // cas j_eleves_professeurs
    echo "<H2>Vérification de la table j_eleves_professeurs</H2>";
    $req = mysql_query("SELECT * FROM j_eleves_professeurs order by login,professeur,id_classe");
    $nb_lignes = mysql_num_rows($req);
    $i = 0;
    while ($i < $nb_lignes) {

       $login_user = mysql_result($req,$i,'login');
        $professeur = mysql_result($req,$i,'professeur');
        $id_classe = mysql_result($req,$i,'id_classe');

        // Détection des doublons
        $req2 = mysql_query("SELECT * FROM j_eleves_professeurs
        where
        login ='$login_user' and
        professeur ='$professeur' and
        id_classe ='$id_classe'
        ");
        $nb_lignes2 = mysql_num_rows($req2);
        if ($nb_lignes2 > "1") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'un doublon : identifiant élève : $login_user - identifiant professeur = $professeur - identifiant classe = $id_classe<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from j_eleves_professeurs where
            login ='$login_user' and
            professeur ='$professeur' and
            id_classe ='$id_classe' LIMIT $nb");
            $cpt++;
        }

        // Détection des lignes inutiles
        $req3 = mysql_query("SELECT *
        FROM j_eleves_professeurs j,
        eleves e,
        utilisateurs u,
        j_eleves_classes jec,
        j_groupes_classes jgc,
        j_groupes_professeurs jgp
        where
        j.login ='$login_user' and
        e.login ='$login_user' and
        jec.login = '$login_user' and
        jec.id_classe = '$id_classe' and
        j.professeur ='$professeur' and
        u.login ='$professeur' and
        jgp.login = '$professeur' and
        jgc.id_classe = '$id_classe' and
        jgp.id_groupe = jgc.id_groupe and
        j.id_classe ='$id_classe'
        ");
        $nb_lignes3 = mysql_num_rows($req3);
        if ($nb_lignes3 == "0") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'une ligne inutile : identifiant élève : $login_user - identifiant professeur = $professeur - identifiant classe = $id_classe<br />";
           // On efface les lignes en trop
            $del = mysql_query("delete from j_eleves_professeurs where
            login ='$login_user' and
            professeur ='$professeur' and
            id_classe ='$id_classe'");
            $cpt++;
        }
        mysql_free_result($req2);
        mysql_free_result($req3);

    $i++;
    }
    if ($cpt != 0) {
        echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
    } else {
        echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
    }
    echo "<b>La table j_eleves_professeurs est OK.</b><br />";

    echo "<form action=\"clean_tables.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"maj\" value=\"3\" />";
    echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
    echo "</form>\n";

} else if (isset($_POST['maj']) and (($_POST['maj'])=="3")) {
    echo "<H2 align=\"center\">Etape 3/$total_etapes</H2>";
    // Cas de la table j_classes_matieres_professeurs
    echo "<H2>Vérification de la table j_classes_matieres_professeurs</H2>";

    /*
    $req = mysql_query("SELECT * FROM j_classes_matieres_professeurs order by id_classe,id_matiere,id_professeur");
    $nb_lignes = mysql_num_rows($req);
    $i = 0;
    while ($i < $nb_lignes) {
        $id_classe = mysql_result($req,$i,'id_classe');
        $id_matiere = mysql_result($req,$i,'id_matiere');
        $id_professeur = mysql_result($req,$i,'id_professeur');

        // Détection des doublons
        $req2 = mysql_query("SELECT * FROM j_classes_matieres_professeurs
        where
        id_classe ='$id_classe' and
        id_matiere ='$id_matiere' and
        id_professeur ='$id_professeur'
        ");
        $nb_lignes2 = mysql_num_rows($req2);
        if ($nb_lignes2 > "1") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'un doublon : identifiant classe = $id_classe - identifiant matiere = $id_matiere - Identifiant professeur = $id_professeur<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from j_classes_matieres_professeurs where
            id_classe ='$id_classe' and
            id_matiere ='$id_matiere' and
            id_professeur ='$id_professeur' LIMIT $nb");

           $cpt++;
        }

        // Détection des lignes inutiles
        $req3 = mysql_query("SELECT *
        FROM j_classes_matieres_professeurs j,
        matieres m,
        utilisateurs u,
        j_professeurs_matieres jpm,
        classes c

        where
        j.id_matiere = '$id_matiere' and
        m.matiere = '$id_matiere' and
        j.id_professeur = '$id_professeur' and
        u.login = '$id_professeur' and
        jpm.id_professeur = '$id_professeur' and
        jpm.id_matiere = '$id_matiere' and
        j.id_classe ='$id_classe' and
        c.id = '$id_classe'
        ");
        $nb_lignes3 = mysql_num_rows($req3);
        if ($nb_lignes3 == "0") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'une ligne inutile : identifiant classe = $id_classe - identifiant matiere = $id_matiere - Identifiant professeur = $id_professeur<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from j_classes_matieres_professeurs where
            id_classe ='$id_classe' and
            id_matiere ='$id_matiere' and
            id_professeur ='$id_professeur'");
            $cpt++;
        }
        mysql_free_result($req2);
        mysql_free_result($req3);

    $i++;
    }

    if ($cpt != 0) {
        echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
    } else {
        echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
    }
    echo "<b>La table j_classes_matieres_professeurs est OK.</b><br />";
    */
    echo "<p>La table j_classes_matieres_professeurs n'existe plus et ne peut donc pas être nettoyée. Cette étape sera remplacée par un nettoyage des tables de gestion des groupes.</p>";
    echo "<form action=\"clean_tables.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"maj\" value=\"4\" />";
    echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
    echo "</form>\n";

} else if (isset($_POST['maj']) and (($_POST['maj'])=="4")) {
    echo "<H2 align=\"center\">Etape 4/$total_etapes</H2>";

    // Vérification de la table j_eleves_classes

    echo "<H2>Vérification de la table j_eleves_classes</H2>";
    $req = mysql_query("SELECT * FROM j_eleves_classes");
    $nb_lignes = mysql_num_rows($req);
    $i = 0;
    while ($i < $nb_lignes) {
        $login_user = mysql_result($req,$i,'login');
        $id_classe = mysql_result($req,$i,'id_classe');
        $periode = mysql_result($req,$i,'periode');
        // Détection des doublons
        $req2 = mysql_query("SELECT * FROM j_eleves_classes
       where
        login ='$login_user' and
        id_classe ='$id_classe' and
        periode ='$periode'
        ");
        $nb_lignes2 = mysql_num_rows($req2);
        if ($nb_lignes2 > "1") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'un doublon : login = $login_user - identifiant classe = $id_classe - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from j_eleves_classes where
            login ='$login_user' and
            id_classe ='$id_classe' and
            periode ='$periode' LIMIT $nb");
            $cpt++;
        }
        // Détection des lignes inutiles
        $req3 = mysql_query("SELECT * FROM j_eleves_classes j, eleves e, classes c, periodes p
        where
        j.login ='$login_user' and
        j.id_classe ='$id_classe' and
        j.periode ='$periode' and
        e.login ='$login_user' and
        c.id ='$id_classe' and
        p.num_periode ='$periode' and
        p.id_classe = '$id_classe'


        ");
        $nb_lignes3 = mysql_num_rows($req3);
        if ($nb_lignes3 == "0") {
            $nb = $nb_lignes2-1;
//            echo "Suppression d'une ligne inutile : login = $login_user - identifiant classe = $id_classe - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from j_eleves_classes where
            login ='$login_user' and
            id_classe ='$id_classe' and
            periode ='$periode'");
            $cpt++;
        }
        mysql_free_result($req2);
        mysql_free_result($req3);

   $i++;
   }
    if ($cpt != 0) {
        echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
    } else {
        echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
    }
    echo "<b>La table j_eleves_classes est OK.</b><br />";

    echo "<form action=\"clean_tables.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"maj\" value=\"5\" />";
    echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
    echo "<center><b>Attention : l'étape suivante peut être très longue.</b></center>";
    echo "</form>\n";
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="5")) or (isset($_GET['maj']) and (($_GET['maj'])=="5"))) {
    echo "<H2 align=\"center\">Etape 5/$total_etapes</H2>";
    echo "<H2>Nettoyage de la table j_eleves_matieres</H2>";
    /*init_time(); //initialise le temps
    //début de fichier
    if (!isset($_GET["offset"])) $offset=0;
        else $offset=$_GET["offset"];
    if (!isset($_GET['nb_lignes'])) {
        $req = mysql_query("SELECT * FROM j_eleves_matieres order by login");
        $nb_lignes = mysql_num_rows($req);
    } else {
        $nb_lignes = $_GET['nb_lignes'];
    }
    if(isset($offset)){
        if ($offset>=0)
           $percent=min(100,round(100*$offset/$nb_lignes,0));
        else $percent=100;
    }
    else $percent=0;

    if ($percent >= 0) {
        $percentwitdh=$percent*4;
        echo "<div align='center'><table width=\"400\" border=\"0\">
        <tr><td width='400' align='center'><b>Nettoyage en cours </b><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>";
    }
    flush();
    if ($offset>=0){
        if (etape5()) {
            echo "<br />Redirection automatique sinon cliquez <a href=\"clean_tables.php?maj=5&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\">ici</a>";
            echo "<script>window.location=\"clean_tables.php?maj=5&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\";</script>";
            flush();
            exit;
       }
    } else {
        if ($cpt != 0) {
            echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
            echo "<b>La table j_eleves_matieres OK.</b><br />";
        } else {
            echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
            echo "<b>La table j_eleves_matieres est OK.</b><br />";
        }
        */
        echo "<p>Cette table n'est plus utilisée. Cette étape devrait donc être, un jour, remplacée par une étape de nettoyage des attributions d'élèves aux groupes...</p>";
        echo "<form action=\"clean_tables.php\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"maj\" value=\"6\" />";
        echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
       echo "</form>\n";
    //}
} else if (isset($_POST['maj']) and (($_POST['maj'])=="6")) {
   echo "<H2 align=\"center\">Etape 6/$total_etapes</H2>";

   // Cas de la table aid_appreciations
    echo "<H2>Nettoyage de la table aid_appreciations (tables des appréciations AID)</H2>";
    $req = mysql_query("SELECT * FROM aid_appreciations order by login,id_aid,periode");
    $nb_lignes = mysql_num_rows($req);
    $i = 0;
    while ($i < $nb_lignes) {
        $login_user = mysql_result($req,$i,'login');
        $id_aid = mysql_result($req,$i,'id_aid');

       $periode = mysql_result($req,$i,'periode');


       $test = mysql_query("select aa.login
        from
        aid_appreciations aa,
        eleves e,
        j_eleves_classes jec,
        periodes p,
        aid a,

        j_aid_eleves jae

        where
        aa.login = '$login_user' and
        e.login = '$login_user' and
        jec.login = '$login_user' and
        jec.id_classe = p.id_classe and
        jec.periode = '$periode' and
        p.num_periode = '$periode' and
        aa.periode = '$periode' and
        aa.id_aid = '$id_aid' and

       a.id = '$id_aid' and
        jae.login = '$login_user' and
        jae.id_aid = '$id_aid'
        ");
        $nb_lignes2 = mysql_num_rows($test);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - identifiant aid = $id_aid - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from aid_appreciations where
            login ='$login_user' and
            id_aid ='$id_aid' and
            periode ='$periode'");
            $cpt++;
        }
        mysql_free_result($test);
        $i++;
    }
    if ($cpt != 0) {
        echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
    } else {
        echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
    }
    echo "<b>La table aid_appreciations est OK.</b><br />";



   // Cas de la table avis_conseil_classe
    echo "<H2>Nettoyage de la table avis_conseil_classe (tables des avis du conseil de classe)</H2>";
    $req = mysql_query("SELECT * FROM avis_conseil_classe order by login,periode");
    $nb_lignes = mysql_num_rows($req);
    $i = 0;
    while ($i < $nb_lignes) {
        $login_user = mysql_result($req,$i,'login');
        $periode = mysql_result($req,$i,'periode');

        $test = mysql_query("select acc.login
        from
        avis_conseil_classe acc,
        eleves e,
        j_eleves_classes jec,
        periodes p

        where
        acc.login = '$login_user' and
        e.login = '$login_user' and

       jec.login = '$login_user' and
        jec.id_classe = p.id_classe and

       jec.periode = '$periode' and
        p.num_periode = '$periode' and
        acc.periode = '$periode'
        ");
        $nb_lignes2 = mysql_num_rows($test);
        if ($nb_lignes2 == "0") {
//            echo "Suppression d'une donnée orpheline : login = $login_user - Numéro période = $periode<br />";
            // On efface les lignes en trop
            $del = mysql_query("delete from avis_conseil_classe where
            login ='$login_user' and
            periode ='$periode'");
            $cpt++;
        }
        mysql_free_result($test);
        $i++;
    }
    echo "<b>La table avis_conseil_classe est OK.</b><br />";
    echo "<form action=\"clean_tables.php\" method=\"post\">\n";
    echo "<input type=\"hidden\" name=\"maj\" value=\"7\" />";
    echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
    echo "<center><b>Attention : l'étape suivante peut être très longue.</b></center>";
    echo "</form>\n";
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="7")) or (isset($_GET['maj']) and (($_GET['maj'])=="7"))) {
    echo "<H2 align=\"center\">Etape 7/$total_etapes</H2>";

   echo "<H2>Nettoyage de la table matieres_appreciations (tables des appréciations par discipline)</H2>";
    init_time(); //initialise le temps
    //début de fichier
    if (!isset($_GET["offset"])) $offset=0;
        else $offset=$_GET["offset"];
    if (!isset($_GET['nb_lignes'])) {
        $req = mysql_query("SELECT * FROM matieres_appreciations order by login,id_groupe,periode");

       $nb_lignes = mysql_num_rows($req);
    } else {
        $nb_lignes = $_GET['nb_lignes'];
    }

    if(isset($offset)){

       if ($offset>=0)
           $percent=min(100,round(100*$offset/$nb_lignes,0));
        else $percent=100;
    }
    else $percent=0;
    if ($percent >= 0) {
        $percentwitdh=$percent*4;

       echo "<div align='center'><table width=\"400\" border=\"0\">
        <tr><td width='400' align='center'><b>Nettoyage en cours</b><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>";
    }
    flush();
    if ($offset>=0){
        if (etape7()) {
            echo "<br />Redirection automatique sinon cliquez <a href=\"clean_tables.php?maj=7&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\">ici</a>";
            echo "<script>window.location=\"clean_tables.php?maj=7&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\";</script>";
            flush();
            exit;
       }
  } else {
        if ($cpt != 0) {
            echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
            echo "<b>La table matieres_appreciations est OK.</b><br />";
        } else {
            echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
            echo "<b>La table matieres_appreciations est OK.</b><br />";
        }
        echo "<form action=\"clean_tables.php\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"maj\" value=\"8\" />";
        echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
        echo "<center><b>Attention : l'étape suivante peut être très longue.</b></center>";
        echo "</form>\n";
    }
} else if ((isset($_POST['maj']) and (($_POST['maj'])=="8")) or (isset($_GET['maj']) and (($_GET['maj'])=="8"))) {
    echo "<H2 align=\"center\">Etape 8/$total_etapes</H2>";
    echo "<H2>Nettoyage de la table matieres_notes (tables des notes par discipline)</H2>";
    init_time(); //initialise le temps
    //début de fichier
    if (!isset($_GET["offset"])) $offset=0;
        else $offset=$_GET["offset"];
    if (!isset($_GET['nb_lignes'])) {
        $req = mysql_query("SELECT * FROM matieres_notes order by login,id_groupe,periode");
        $nb_lignes = mysql_num_rows($req);
    } else {
        $nb_lignes = $_GET['nb_lignes'];
    }
    if(isset($offset)){
        if ($offset>=0)
           $percent=min(100,round(100*$offset/$nb_lignes,0));
        else $percent=100;
    }
    else $percent=0;
    if ($percent >= 0) {
        $percentwitdh=$percent*4;
        echo "<div align='center'><table width=\"400\" border=\"0\">
        <tr><td width='400' align='center'><b>Nettoyage en cours</b><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>";
    }
    flush();
    if ($offset>=0){
        if (etape8()) {
            echo "<br />Redirection automatique sinon cliquez <a href=\"clean_tables.php?maj=8&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\">ici</a>";
            echo "<script>window.location=\"clean_tables.php?maj=8&duree=$duree&offset=$offset&cpt=$cpt&nb_lignes=$nb_lignes\";</script>";
            flush();
            exit;
       }
    } else {
        if ($cpt != 0) {
            echo "<font color=\"red\">Nombre de lignes supprimées : ".$cpt."</font><br />";
            echo "<b>La table matieres_notes est OK.</b><br />";

       } else {
            echo "<font color=\"green\">Aucune ligne n'a été supprimée.</font><br />";
            echo "<b>La table matieres_notes est OK.</b><br />";
        }
        //echo "<hr /><H2 align=\"center\">Fin de la vérification des tables</H2>";
        echo "<form action=\"clean_tables.php\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"maj\" value=\"9\" />";
        echo "<center><input type=\"submit\" name=\"ok\" value=\"Suite de la vérification\" /></center>";
        //echo "<center><b>Attention : l'étape suivante peut être très longue.</b></center>";
        echo "</form>\n";
    }

}
elseif ((isset($_POST['maj']) and (($_POST['maj'])=="9")) or (isset($_GET['maj']) and (($_GET['maj'])=="9"))) {
	echo "<H2 align=\"center\">Etape 9/$total_etapes</H2>\n";

	echo "<p><a href='index.php'>Retour à Outils de gestion</a> | <a href='index.php'>Retour à Vérification/nettoyage des tables</a></p>\n";

	echo "<H2>Nettoyage des aberrations sur les groupes</H2>\n";

	$table=array('j_groupes_classes','j_groupes_matieres','j_groupes_professeurs','j_eleves_groupes');

	for($i=0;$i<count($table);$i++){
		$err_no=0;
		$sql="SELECT DISTINCT id_groupe FROM ".$table[$i]." ORDER BY id_groupe";
		$res_grp1=mysql_query($sql);

		if(mysql_num_rows($res_grp1)>0){
			//echo "<p>On parcourt la table '".$table[$i]."'.</p>\n";
			while($ligne=mysql_fetch_array($res_grp1)){
				$sql="SELECT 1=1 FROM groupes WHERE id='".$ligne[0]."'";
				$res_test=mysql_query($sql);

				if(mysql_num_rows($res_test)==0){
					$sql="DELETE FROM $table[$i] WHERE id_groupe='$ligne[0]'";
					echo "Suppression d'une référence à un groupe d'identifiant $ligne[0] dans la table $table[$i] alors que le groupe n'existe pas dans la table 'groupes'.<br />\n";
					//echo "$sql<br />";
					$res_suppr=mysql_query($sql);
					$err_no++;
				}
			}
		}
		if($err_no==0){
			echo "<b>La table $table[$i] est OK.</b><br />\n";
		}
	}

	echo "<H2>Nettoyage des erreurs d'appartenance à des groupes</H2>\n";

	// Elèves dans des groupes pour lesquels ils ne sont pas dans la classe sur la période
	// Mais association classe/groupe OK dans j_groupes_classes
	//===========
	// A FAIRE
	//===========

	// Elèves dans des groupes pour lesquels l'association classe/groupe n'existe pas dans j_groupes_classes pour leurs classes
	//===========
	// A FAIRE
	//===========





	$err_no=0;
	// On commence par ne récupérer que les login/periode pour ne pas risquer d'oublier d'élèves
	// (il peut y avoir des incohérences non détectées si on essaye de récupérer davantage d'infos dans un premier temps)
	$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode";
	$res_ele=mysql_query($sql);
	$ini="";
	while($lig_ele=mysql_fetch_object($res_ele)){
		if(strtoupper(substr($lig_ele->login,0,1))!=$ini){
			$ini=strtoupper(substr($lig_ele->login,0,1));
			echo "<p>\n<i>Parcours des login commençant par la lettre $ini</i></p>\n";
		}

		// Récupération de la liste des groupes auxquels l'élève est inscrit sur la période en cours d'analyse:
		$sql="SELECT id_groupe FROM j_eleves_groupes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
		//echo "$sql<br />\n";
		$res_jeg=mysql_query($sql);

		if(mysql_num_rows($res_jeg)>0){
			// On vérifie si l'élève est dans une classe pour cette période:
			$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
			$res_jec=mysql_query($sql);

			if(mysql_num_rows($res_jec)==0){
				// L'élève n'est dans aucune classe sur la période choisie.
				echo "<p>";
				echo "<b>$lig_ele->login</b> n'est dans aucune classe en période <b>$lig_ele->periode</b> et se trouve pourtant dans des groupes.<br />\n";
				echo "Suppression de l'élève du(es) groupe(s) ";
				$cpt_tmp=1;
				while($lig_grp=mysql_fetch_object($res_jeg)){
					$id_groupe=$lig_grp->id_groupe;
					//$tmp_groupe=get_group($id_groupe);
					//$nom_groupe=$tmp_groupe['description'];
					$sql="SELECT description FROM groupes WHERE id='$id_groupe'";
					$res_grp_tmp=mysql_query($sql);
					if(mysql_num_rows($res_grp_tmp)==0){
						$nom_groupe="<font color='red'>GROUPE INEXISTANT</font>";
					}
					else{
						$lig_grp_tmp=mysql_fetch_object($res_grp_tmp);
						$nom_groupe=$lig_grp_tmp->description;
					}

					// On va le supprimer du groupe après un dernier test:
					$test1=mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
					$nb_test1 = mysql_num_rows($test1);

					$test2=mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
					$nb_test2 = mysql_num_rows($test2);

					if (($nb_test1 != 0) or ($nb_test2 != 0)) {
						echo "<br /><font color='red'>Impossible de supprimer cette option pour l'élève $lig_ele->login car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $lig_ele->periode !<br />\nCommencez par supprimer ces données !</font><br />";
					} else {
						if($req=mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' and id_groupe='".$id_groupe."' and periode = '".$lig_ele->periode."')")){
							if($cpt_tmp>1){echo ", ";}
							echo "$nom_groupe (<i>n°$id_groupe</i>)";
							$cpt_tmp++;
						}
					}
				}
			}
			else{
				if(mysql_num_rows($res_jec)==1){
					$lig_clas=mysql_fetch_object($res_jec);
					while($lig_grp=mysql_fetch_object($res_jeg)){
						// On cherche si l'association groupe/classe existe:
						$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe' AND id_classe='$lig_clas->id_classe'";
						$res_test_grp_clas=mysql_query($sql);

						if(mysql_num_rows($res_test_grp_clas)==0){

							$id_groupe=$lig_grp->id_groupe;
							$tmp_groupe=get_group($id_groupe);
							$nom_groupe=$tmp_groupe['description'];

							$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
							$res_tmp=mysql_query($sql);
							$lig_tmp=mysql_fetch_object($res_tmp);
							$clas_tmp=$lig_tmp->classe;

							$sql="SELECT description FROM groupes WHERE id='$lig_grp->id_groupe'";
							$res_tmp=mysql_query($sql);
							$lig_tmp=mysql_fetch_object($res_tmp);
							$grp_tmp=$lig_tmp->description;

							echo "<p>\n";
							echo "<b>$lig_ele->login</b> est inscrit en période $lig_ele->periode dans le groupe <b>$grp_tmp</b> (<i>groupe n°$lig_grp->id_groupe</i>) alors que ce groupe n'est pas associé à la classe <b>$clas_tmp</b> dans 'j_groupes_classes'.<br />\n";

							echo "Suppression de l'élève du groupe ";
							// On va le supprimer du groupe après un dernier test:
							$test1=mysql_query("SELECT 1=1 FROM matieres_notes WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
							$nb_test1 = mysql_num_rows($test1);

							$test2=mysql_query("SELECT 1=1 FROM matieres_appreciations WHERE (id_groupe = '".$id_groupe."' and login = '".$lig_ele->login."' and periode = '$lig_ele->periode')");
							$nb_test2 = mysql_num_rows($test2);

							if (($nb_test1 != 0) or ($nb_test2 != 0)) {
								echo "<br /><font color='red'>Impossible de supprimer cette option pour l'élève $lig_ele->login car des moyennes ou appréciations ont déjà été rentrées pour le groupe $nom_groupe pour la période $lig_ele->periode !<br />\nCommencez par supprimer ces données !</font><br />";
							} else {
								if($req=mysql_query("DELETE FROM j_eleves_groupes WHERE (login='".$lig_ele->login."' and id_groupe='".$id_groupe."' and periode = '".$lig_ele->periode."')")){
									echo "$nom_groupe (<i>n°$id_groupe</i>)";
									//$cpt_tmp++;
								}
							}

							echo "</p>\n";
							$err_no++;
						}
					}
				}
				else{
					echo "<p>\n";
					echo "<b>$lig_ele->login</b> est inscrit dans plusieurs classes sur la période $lig_ele->periode:<br />\n";
					while($lig_clas=mysql_fetch_object($res_jec)){
						$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;
						echo "Classe de <a href='../classes/classes_const.php?id_classe=$lig_clas->id_classe'>$clas_tmp</a> (<i>n°$lig_clas->id_classe</i>)<br />\n";
					}
					echo "Cela ne devrait pas être possible.<br />\n";
					echo "Faites le ménage dans les effectifs des classes ci-dessus.\n";
					echo "</p>\n";
					$err_no++;
				}
			}
		}
		// Pour envoyer ce qui a été écrit vers l'écran sans attendre la fin de la page...
		flush();
	}
	if($err_no==0){
		echo "<p>Aucune erreur d'affectation dans des groupes/classes n'a été détectée.</p>\n";
	}
	else{
		echo "<p>Une ou des erreurs ont été relevées.";
		echo "</p>\n";
	}




	echo "<hr />\n";
	echo "<H2 align=\"center\">Fin de la vérification des tables</H2>\n";

} else {
    echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a> ";
    echo "| <a href='clean_tables.php'>Retour page Vérification / Nettoyage des tables</a></p>";

    echo "<p>Il est très vivement conseillé de <b>faire une sauvegarde de la base MySql avant de lancer la procédure.</b></p>\n";
    echo "<center><form enctype=\"multipart/form-data\" action=\"../gestion/accueil_sauve.php?action=dump\" method=post name=formulaire>";
    echo "<input type=\"submit\" value=\"Lancer une sauvegarde de la base de données\" /></form></center>\n";
    echo "<p>Il est également vivement conseillé de <b><a href='../gestion/gestion_connect.php'>désactiver les connexions à GEPI</a> durant la phase de nettoyage</b>.</p>
   <p align='center'><b><font size=\"+1\">Attention : selon la taille de la base, cette opération peut durer plusieurs heures.</font></b></p>\n";
    echo "<hr />\n";
    echo "<p>Cette procédure opère un <b>nettoyage</b> des lignes inutiles dans les <b>tables de liaison</b> de la base MySql de GEPI et dans les tables des données scolaires des élèves (notes, appréciations, absences).";
    echo "<br />Les tables de liaison contiennent des informations qui mettent en relation les tables principales de GEPI
   (élèves, professeurs, matières, classes).<br /><br />
   Du fait de bugs mineurs (éventuellement déjà réglés mais présents dans des versions antérieures de GEPI) ou de mauvaises manipulations,
   ces tables de liaison peuvent contenir des données obsolètes ou des doublons qui peuvent nuire à un fonctionnement optimal de GEPI.";


    echo "<form action=\"clean_tables.php\" method=\"post\">";
    echo "<p><b>Cliquez sur le bouton suivant pour commencer le nettoyage des tables de la base</b></p>";
    echo "<p>Cette procédure s'effectue en plusieurs étapes : à chaque étape, une page affiche le compte-rendu du nettoyage et un <b>bouton situé en bas de la page</b> vous permet de passer à l'étape suivante.</p>";

    echo "<center><input type=submit value='Procéder au nettoyage des tables' /></center>";
    echo "<input type=hidden name='maj' value='1' />";
    echo "<input type=hidden name='valid' value='$valid' />";
    echo "</form>\n";

    echo "<hr />\n";

    echo "<p>Il est arrivé que des élèves puissent être inscrits à des groupes sur des périodes où ils ne sont plus dans la classe (<i>suite à des changements de classes, départs,... par exemple</i>).<br />Il en résulte des affichages d'erreur non fatales, mais disgracieuses.<br />Le problème n'est normalement plus susceptible de revenir, mais dans le cas où vous auriez des erreurs inexpliquées concernant /lib/groupes.inc.php, vous pouvez contrôler les appartenances aux groupes/classes en visitant la page suivante:</p>\n";

    //echo "<p><a href='verif_groupes.php'>Contrôler les appartenances d'élèves à des groupes/classes</a>.</p>\n";

    echo "<form action=\"verif_groupes.php\" method=\"post\">";
    echo "<center><input type=submit value='Contrôler les groupes' /></center>";
    echo "</form>\n";

    echo "<hr />\n";

    echo "<p>Jusqu'à la version 1.4.3-1, GEPI a comporté un bug sur le calcul des moyennes de conteneurs (<i>boites/sous-matières</i>).<br />\nSi on déplaçait un devoir ou un conteneur vers un autre conteneur, il pouvait se produire une absence de recalcul des moyennes de certains conteneurs.<br />\nLe problème est désormais corrigé, mais dans le cas où vos moyennes ne sembleraient pas correctes, vous pouvez provoquer le recalcul des moyennes de l'ensemble des conteneurs pour l'ensemble des groupes/matières.<br />\nLes modifications effectuées seront affichées.</p>\n";

    echo "<form action=\"recalcul_moy_conteneurs.php\" method=\"post\">";
    echo "<center><input type=submit value='Recalculer les moyennes de conteneurs' /></center>";
    echo "</form>\n";
}

require("../lib/footer.inc.php");
?>