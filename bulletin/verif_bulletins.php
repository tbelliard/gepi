<?php
/*
 * Last modification  : 19/08/2006
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

include "../lib/periodes.inc.php";
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//**************** EN-TETE *****************
$titre_page = "Vérification du remplissage des bulletins";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On teste si un professeur peut effectuer cette operation
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}



// Selection de la classe
if (!(isset($id_classe))) {
	echo "<p class=bold>|<a href='../accueil.php'>Retour</a>|</p>\n";
	echo "<p><b>Choisissez la classe :</b></p>";
	//<table><tr><td>\n";
	if ($_SESSION["statut"] == "scolarite") {
		//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
	}
	else {
		$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
	}

	$lignes = mysql_num_rows($appel_donnees);

	$nb_class_par_colonne=round($lignes/3);
        echo "<table width='100%'>\n";
        echo "<tr valign='top' align='center'>\n";

	$i = 0;

        echo "<td align='left'>\n";
	while($i < $lignes){
		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		$id_classe = mysql_result($appel_donnees, $i, "id");
		$display_class = mysql_result($appel_donnees, $i, "classe");
		echo "<a href='verif_bulletins.php?id_classe=$id_classe'>".ucfirst($display_class)."</a><br />\n";
		$i++;
	}
        echo "</td>\n";
        echo "</tr>\n";
        echo "</table>\n";

	//echo "</td><td></td></table>";
} else if (!(isset($per))){
    echo "<p class=bold>|<a href='verif_bulletins.php'>Retour</a>|</p>\n";

    // On teste si les élèves ont bien un CPE responsable

    $test1 = mysql_query("SELECT distinct(login) login from j_eleves_classes WHERE id_classe='" . $id_classe . "'");
    $nb_eleves = mysql_num_rows($test1);
    $j = 0;
    $flag = true;
    while ($j < $nb_eleves) {
        $login_e = mysql_result($test1, $j, "login");
        $test = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_cpe WHERE e_login='" . $login_e . "'"), 0);
        if ($test == "0") {
            $flag = false;
            break;
        }
        $j++;
    }

    if (!$flag) {
        echo "<p>ATTENTION : certains élèves de cette classe n'ont pas de CPE responsable attribué. Cela génèrera un msesage d'erreur sur la page d'édition des bulletins. Il faut corriger ce problème avant impression (contactez l'administrateur).";
    }

    echo "<p><b>Choisissez la période : </b></p>\n";
    include "../lib/periodes.inc.php";
    $i="1";
    while ($i < $nb_periode) {
        echo "<p><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i'>".ucfirst($nom_periode[$i])."</a>\n";
        if ($ver_periode[$i] == "P")  {
            echo " (période partiellement close, seule la saisie des avis du conseil de classe est possible)\n";
        } else if ($ver_periode[$i] == "O")  {
            echo " (période entièrement close, plus aucune saisie/modification n'est possible)\n";
        } else {
            echo " (période ouverte, les saisies/modifications sont possibles)\n";
        }

        //echo "<p>\n";
        echo "</p>\n";
        $i++;
    }
} else {
    echo "<p class=bold>|<a href='verif_bulletins.php'>Retour</a>|</p>\n";
    $bulletin_rempli = 'yes';
    $call_classe = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
    $classe = mysql_result($call_classe, "0", "classe");
    echo "<p class=bold>Classe : $classe - $nom_periode[$per] - Année scolaire : ".getSettingValue("gepiYear")."</p>";

    //
    // Vérification de paramètres généraux
    //
    $current_classe_nom_complet = mysql_result($call_classe, 0, "nom_complet");
    if ($current_classe_nom_complet == '') {
        $bulletin_rempli = 'no';
        echo "<p>Le nom long de la classe n'est pas défini !</p>\n";
    }
    $current_classe_suivi_par = mysql_result($call_classe, 0, "suivi_par");
    if ($current_classe_suivi_par == '') {
        $bulletin_rempli = 'no';
        echo "<p>La personne de l'administration chargée de la classe n'est pas définie !</p>\n";
    }
    $current_classe_formule = mysql_result($call_classe, 0, "formule");
    if ($current_classe_formule == '') {
        $bulletin_rempli = 'no';
        echo "<p>La formule à la fin de chaque bulletin n'est pas définie !</p>\n";
    }
    $appel_donnees_eleves = mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login and j.periode='$per') ORDER BY login");
    $nb_eleves = mysql_num_rows($appel_donnees_eleves);
    $j = 0;
    //
    //Début de la boucle élève
    //

    while($j < $nb_eleves) {
        $id_eleve[$j] = mysql_result($appel_donnees_eleves, $j, "login");
        $eleve_nom[$j] = mysql_result($appel_donnees_eleves, $j, "nom");
        $eleve_prenom[$j] = mysql_result($appel_donnees_eleves, $j, "prenom");

        $groupeinfo = mysql_query("SELECT DISTINCT id_groupe FROM j_eleves_groupes WHERE login='" . $id_eleve[$j] ."'");
        $lignes_groupes = mysql_num_rows($groupeinfo);
        //
        //Vérification des appréciations
        //

        $i= 0;
        //
        //Début de la boucle matière
        //
        $affiche_nom = 1;
        $affiche_mess_app = 1;
        $affiche_mess_note = 1;
        while($i < $lignes_groupes){
            $group_id = mysql_result($groupeinfo, $i, "id_groupe");
            $current_group = get_group($group_id);

            if (in_array($id_eleve[$j], $current_group["eleves"][$per]["list"])) { // Si l'élève suit cet enseignement pour la période considérée
                //
                //Vérification des appréciations :
                //
                $test_app = mysql_query("SELECT * FROM matieres_appreciations WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per')");
                $app = @mysql_result($test_app, 0, 'appreciation');
                if ($app == '') {
                    $bulletin_rempli = 'no';
                    if ($affiche_nom != 0) {echo "<p><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
                    if ($affiche_mess_app != 0) {echo "<br /><br />Appréciations non remplies pour les matières suivantes : ";}
                    $affiche_nom = 0;
                    $affiche_mess_app = 0;
                    //============================================
		    // MODIF: boireaus
		    // Pour les matières comme Histoire & Géo,...
                    //echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
                    echo "<br />--> " . htmlentities($current_group["description"]) . " (" . $current_group["classlist_string"] . ")  --  (";
                    //============================================
                    $m=0;
                    $virgule = 1;
                    foreach ($current_group["profs"]["list"] as $login_prof) {
                        $email = retourne_email($login_prof);
                        $nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
                        $prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
                        echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
                        $m++;
                        if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
                        if ($virgule == 1) {echo ", ";}
                    }
                    echo ")\n";

                }
            }
            $i++;
        }
        //
        //Vérification des moyennes
        //
        $i= 0;
        //
        //Début de la boucle matière
        //
        while($i < $lignes_groupes){
            $group_id = mysql_result($groupeinfo, $i, "id_groupe");
            $current_group = get_group($group_id);

            if (in_array($id_eleve[$j], $current_group["eleves"][$per]["list"])) { // Si l'élève suit cet enseignement pour la période considérée
                //
                //Vérification des moyennes :
                //
                $test_notes = mysql_query("SELECT * FROM matieres_notes WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per')");
                $note = @mysql_result($test_notes, 0, 'note');
                if ($note == '') {
                    $bulletin_rempli = 'no';
                    if ($affiche_nom != 0) {echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
                    if ($affiche_mess_note != 0) {echo "<br /><br />Moyennes non remplies pour les matières suivantes : ";}
                    $affiche_nom = 0;
                    $affiche_mess_note = 0;
                    //============================================
		    // MODIF: boireaus
		    // Pour les matières comme Histoire & Géo,...
                    //echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
                    echo "<br />--> ".htmlentities($current_group["description"])." (" . $current_group["classlist_string"] . ")  --   (";
                    //============================================
                    $m=0;
                    $virgule = 1;
                    foreach ($current_group["profs"]["list"] as $login_prof) {
                        $email = retourne_email($login_prof);
                        $nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
                        $prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
                        echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
                        $m++;
                        if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
                        if ($virgule == 1) {echo ", ";}
                    }
                    echo ")\n";

                }
            }
            $i++;
        //Fin de la boucle matière
        }
        //
        //Vérification des avis des conseils de classe
        //
        $query_conseil = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login = '$id_eleve[$j]' and periode = '$per')");
        $avis = @mysql_result($query_conseil, 0, 'avis');
        if ($avis == '') {
            $bulletin_rempli = 'no';
            if ($affiche_nom != 0) {echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
            echo "<br /><br />Avis du conseil de classe non rempli !";
            $call_prof = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs j WHERE (j.login = '$id_eleve[$j]' and j.id_classe='$id_classe' and u.login=j.professeur)");
            $nb_result = mysql_num_rows($call_prof);
            if ($nb_result != 0) {
                $login_prof = mysql_result($call_prof, 0, 'login');
                $email = retourne_email($login_prof);
                $nom_prof = mysql_result($call_prof, 0, 'nom');
                $prenom_prof = mysql_result($call_prof, 0, 'prenom');
                echo " (<a href='mailto:$email'>$prenom_prof $nom_prof</a>)";
            } else {
                echo " (pas de ".getSettingValue("gepi_prof_suivi").")";
            }

            $affiche_nom = 0;
        }
        //
        //Vérification des aid
        //
        $call_data = mysql_query("SELECT * FROM aid_config WHERE display_bulletin!='n' ORDER BY nom");
        $nb_aid = mysql_num_rows($call_data);
        $z=0;
        while ($z < $nb_aid) {
            $display_begin = @mysql_result($call_data, $z, "display_begin");
            $display_end = @mysql_result($call_data, $z, "display_end");
            if (($per >= $display_begin) and ($per <= $display_end)) {
                $indice_aid = @mysql_result($call_data, $z, "indice_aid");
                $type_note = @mysql_result($call_data, $z, "type_note");
                $call_data2 = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
                $nom_aid = @mysql_result($call_data2, 0, "nom");
                $aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='$id_eleve[$j]' and indice_aid='$indice_aid')");
                $aid_id = @mysql_result($aid_query, 0, "id_aid");
                if ($aid_id != '') {
                    $aid_app_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$id_eleve[$j]' AND periode='$per' and id_aid='$aid_id' and indice_aid='$indice_aid')");
                    $query_resp = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid = '$aid_id' and u.login = j.id_utilisateur and j.indice_aid='$indice_aid')");
                    $nb_prof = mysql_num_rows($query_resp);
                    //
                    // Vérification des appréciations
                    //
                    $aid_app = @mysql_result($aid_app_query, 0, "appreciation");
                    if ($aid_app == '') {
                        $bulletin_rempli = 'no';
                        if ($affiche_nom != 0) {echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
                        echo "<br /><br />Appréciation $nom_aid non remplie (";
                        $m=0;
                        $virgule = 1;
                        while ($m < $nb_prof) {
                            $login_prof = @mysql_result($query_resp, $m, 'login');
                            $email = retourne_email($login_prof);
                            $nom_prof = @mysql_result($query_resp, $m, 'nom');
                            $prenom_prof = @mysql_result($query_resp, $m, 'prenom');
                            echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
                            $m++;
                            if ($m == $nb_prof) {$virgule = 0;}
                            if ($virgule == 1) {echo ", ";}
                        }
                        echo ")\n";
                    $affiche_nom = 0;
                    }
                    //
                    // Vérification des moyennes
                    //
                    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
                    $periode_max = mysql_num_rows($periode_query);
                    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
                    if (($type_note=='every') or (($type_note=='last') and ($per == $last_periode_aid))) {
                        $aid_note = @mysql_result($aid_app_query, 0, "note");
                        $aid_statut = @mysql_result($aid_app_query, 0, "statut");


                        if (($aid_note == '') or ($aid_statut == 'other')) {
                        $bulletin_rempli = 'no';
                        if ($affiche_nom != 0) {echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
                            echo "<br /><br />Note $nom_aid non remplie (";
                            $m=0;
                            $virgule = 1;
                            while ($m < $nb_prof) {
                                $login_prof = @mysql_result($query_resp, $m, 'login');
                                $email = retourne_email($login_prof);
                                $nom_prof = @mysql_result($query_resp, $m, 'nom');
                                $prenom_prof = @mysql_result($query_resp, $m, 'prenom');
                                echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
                                $m++;
                                if ($m == $nb_prof) {$virgule = 0;}
                                if ($virgule == 1) {echo ", ";}
                            }
                            echo ")\n";
                            $affiche_nom = 0;
                        }
                    }
                }
            }
            $z++;
        }
        //
        //Vérification des absences
        //
        $abs_query = mysql_query("SELECT * FROM absences WHERE (login='$id_eleve[$j]' AND periode='$per')");
        $abs1 = @mysql_result($abs_query, 0, "nb_absences");
        $abs2 = @mysql_result($abs_query, 0, "non_justifie");
        $abs3 = @mysql_result($abs_query, 0, "nb_retards");
        if (($abs1 == '') or ($abs2 == '') or ($abs3 == '')) {
            $bulletin_rempli = 'no';
            if ($affiche_nom != 0) {echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] (<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span> :";}
            echo "<br /><br />Rubrique \"Absences\" non remplie. (";
            $query_resp = mysql_query("SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_cpe j WHERE (j.e_login = '$id_eleve[$j]' AND u.login = j.cpe_login)");
            $nb_prof = mysql_num_rows($query_resp);
            $m=0;
            $virgule = 1;
            while ($m < $nb_prof) {
            $login_prof = @mysql_result($query_resp, $m, 'login');
                    $email = retourne_email($login_prof);
                    $nom_prof = @mysql_result($query_resp, $m, 'nom');
                    $prenom_prof = @mysql_result($query_resp, $m, 'prenom');
                    echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
                    $m++;
                    if ($m == $nb_prof) {$virgule = 0;}
                    if ($virgule == 1) {echo ", ";}
                }
            echo ")\n";
            $affiche_nom = 0;
        }

        $j++;
    //Fin de la boucle élève
    }

    if ($bulletin_rempli == 'yes') {
        echo "<p class='bold'>Toutes les rubriques des bulletins de cette classe ont été renseignées, vous pouvez procéder à l'impression finale.</p>";
    } else {
        echo "<p class='bold'>*** Fin des vérifications. ***</p>";
    }
}
?>
</body>
</html>


