<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
    $ds = @ldap_connect($l_adresse, $l_port);
    if($ds) {
       // On dit qu'on utilise LDAP V3, sinon la V2 par d?faut est utilis? et le bind ne passe pas.
       $norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
       // Acc?s non anonyme
       if ($l_login != '') {
          // On tente un bind
          $b = @ldap_bind($ds, $l_login, $l_pwd);
       } else {
          // Acc?s anonyme
          $b = @ldap_bind($ds);
       }
       if ($b) {
           return $ds;
       } else {
           return false;
       }
    } else {
       return false;
    }
}

$liste_tables_del = array(
"groupes",
"j_eleves_groupes",
"j_groupes_classes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_groupes_visibilite",
"eleves_groupes_settings",
"j_signalement",
"edt_classes",
"edt_cours"
);


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
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Initialisation
$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : affectation des matières et des professeurs aux classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href='../init_lcs/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

function is_prof($login,$matiere) {
    $test = sql_query1("select count(id_professeur) from j_professeurs_matieres where id_professeur = '".$login."' and id_matiere = '".$matiere."'");
    if ($test > 0)
        return true;
    else
        return false;
}
if (isset($_POST['is_posted'])) {
	check_token();

    // L'admin a validé la procédure, on procède donc...
    $j=0;
    while ($j < count($liste_tables_del)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM ".$liste_tables_del[$j]),0)!=0) {
            $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
        }
        $j++;
    }

    // On se connecte au LDAP
    $ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");

    echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"3\">\n";
    echo "<tr><td>Classe</td><td>Matière</td><td>identifiants prof.</td></tr>\n";

    // Première boucle sur les classes :
    //================================================
    // Modif: boireaus 20091119
    //$res = sql_query("select id, classe from classes");
    $res = sql_query("select id, classe from classes");
    //================================================
    if (!$res) die("problème : impossible de sélectionner les classes.");
    for ($i = 0; ($row = sql_row($res, $i)); $i++) {
        $id_classe = $row[0];
        $nom_classe = $row[1];
        // Deuxième boucle sur les matières :
        $res2 = sql_query("select matiere, nom_complet from matieres");
        if (!$res2) die("problème : impossible de sélectionner les matières.");
        for ($j = 0; ($row2 = sql_row($res2, $j)); $j++) {
            $id_matiere = $row2[0];
            $nom_complet = $row2[1];
            // On cherche tous groupes qui commence par "Cours_Matiere_Classe"
            //================================================
            // Modif: boireaus 20091119
            //$nom_cours = "Cours_".$id_matiere."_".$nom_classe."_";
            $nom_cours = "Cours_".$id_matiere."_".$nom_classe;
            //================================================
            $sr = ldap_search($ds,$ldap_base,"(cn=".$nom_cours."*)");
            $info = ldap_get_entries($ds,$sr);
            $ordre = 0;
            // boucle sur les "Cours_Matiere_Classe..."
            for ($k=0;$k<$info["count"];$k++) {
                // On récupère tous les membres de ces groupes
                for ( $u = 0; $u < $info[$k]["memberuid"]["count"] ; $u++ ) {
                    $uid = $info[$k]["memberuid"][$u] ;
                    if (is_prof($uid,$id_matiere)) {
                        // On regarde si cette association correspond déjà à un groupe
                        $test = mysql_query("SELECT g.id FROM groupes g, j_groupes_classes jgc, j_groupes_matieres jgm WHERE (" .
                        "g.id = jgc.id_groupe AND " .
                        "jgc.id_classe = '" . $id_classe . "' AND " .
                        "jgc.id_groupe = jgm.id_groupe AND " .
                        "jgm.id_matiere = '" . $id_matiere . "')");
                        if (mysql_num_rows($test) != 0) {
                             $ordre++;
                             // Si un enregistrement existe déjà, ça veut dire que le groupe a déjà été traité
                             // il ne reste alors qu'à ajouter le professeur mentionné dans cette association
                             $group_id = mysql_result($test, 0, "id");
                             $insert_prof = mysql_query("INSERT into j_groupes_professeurs SET id_groupe = '" . $group_id ."', login = '" . $uid . "', ordre_prof = '" . $ordre ."'");
                        } else {
                            // La première étape consiste à créer le nouveau groupe, pour obtenir son ID
                            $ordre++;
                            $new_group = create_group($nom_complet, $nom_complet, $id_matiere, array($id_classe));
                            // On ajoute le professeur
                            $insert_prof = mysql_query("INSERT into j_groupes_professeurs SET id_groupe = '" . $new_group ."', login = '" . $uid . "', ordre_prof = '" . $ordre ."'");
                            // On s'occupe maintenant des élèves, période par période
                            $call_periodes = mysql_query("select num_periode FROM periodes WHERE id_classe = '" . $id_classe . "'");
                            $nb_per = mysql_num_rows($call_periodes);
                            for ($m=0;$m<$nb_per;$m++) {
                                $num_periode = mysql_result($call_periodes, $m, "num_periode");
                                $call_eleves = mysql_query("SELECT login FROM j_eleves_classes WHERE (periode = '" . $num_periode . "' AND id_classe = '" . $id_classe ."')");
                                $eleves = array();
                                while ($row1 = mysql_fetch_row($call_eleves)) {
                                    $eleves[] = $row1[0];
                                }
                                foreach ($eleves as $login) {
                                    if ($new_group == 0) echo "ERREUR! New_group ID = 0<br />";
                                    // Appartenance au groupe
                                    $insert = mysql_query("INSERT into j_eleves_groupes SET login = '" . $login . "', id_groupe = '" . $new_group . "', periode = '" . $num_periode . "'");
                                    // Mise à jour de la référence à la note du bulletin
                                    $update = mysql_query("UPDATE matieres_notes SET id_groupe = '" . $new_group . "' WHERE (login = '" . $login . "' AND periode = '" . $num_periode . "' AND matiere = '" . $id_matiere . "')");
                                    // Mise à jour de la référence à l'appréciation du bulletin
                                    $update = mysql_query("UPDATE matieres_appreciations SET id_groupe = '" . $new_group . "' WHERE (login = '" . $login . "' AND periode = '" . $num_periode . "' AND matiere = '" . $id_matiere . "')");
                                }
                            }
                            // Et on fait les mises à jours de références pour les carnets de notes et cahiers de texte
                            $update_cn = mysql_query("UPDATE cn_cahier_notes SET id_groupe = '" . $new_group . "' WHERE (matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");
                            $update_ct1 = mysql_query("UPDATE ct_devoir_entry SET id_groupe = '" . $new_group . "' WHERE (id_matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");
                            $update_ct2 = mysql_query("UPDATE ct_entry SET id_groupe = '" . $new_group . "' WHERE (id_matiere = '" . $id_matiere . "' AND id_classe = '" . $id_classe . "')");
                            echo "<tr><td>".$nom_classe."</td><td>".$id_matiere." (".$nom_complet.")</td><td>".$uid."</td></tr>\n";
                        }
                    }
                }
            }
        }
    }
    echo "</table>";
    echo "<p>Opération effectuée.</p>";
//        echo "<p>Vous pouvez vérifier l'importation en allant sur la page de <a href='../matieres/index.php'>gestion des matières</a>.</p>";

} else {

    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des données concernant la constitution des classes et l'affectation des élèves dans les classes sont présentes dans la base GEPI ! Si vous poursuivez la procédure, ces données seront définitivement effacées !</p>";
    }
    echo "<p>Cette procédure a pour but, à partir des données présentes dans l'annuaire LCS, d'affecter des matières aux classes et des professeurs aux matières.</p>";
    echo "<p>Pour chaque classe et chaque matière actuellement présentes dans la base GEPI, le script recherche les professeurs membres des groupes du type \"Cours_classe_matiere..\" et les affecte au couple (classe/matière).</p>";
    echo "<p>Tous les élèves sont systématiquement affectés pour toutes les périodes de l'année.</p>";
    echo "<p>Le résultat n'est pas parfait et vous aurez besoin de faire des ajustements classe par classe.</p>";
    echo "<form enctype='multipart/form-data' action='affectations.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='is_posted' value='yes'>";
    echo "<input type=hidden name='record' value='no'>";
    echo "<p>Etes-vous sûr de vouloir continuer ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis sûr'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>