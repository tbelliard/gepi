<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
@set_time_limit(0);



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
include "../lib/bulletin_simple.inc.php";
//==============================
// AJOUT: boireaus 20080209
include "../lib/bulletin_simple_classe.inc.php";
//==============================
require_once("../lib/header.inc");

// Vérifications de sécurité
if (
	($_SESSION['statut'] == "responsable" AND getSettingValue("GepiAccesBulletinSimpleParent") != "yes") OR
	($_SESSION['statut'] == "eleve" AND getSettingValue("GepiAccesBulletinSimpleEleve") != "yes")
	) {
	tentative_intrusion(2, "Tentative de visualisation d'un bulletin simplifié sans y être autorisé.");
	echo "<p>Vous n'êtes pas autorisé à visualiser cette page.</p>";
	require "../lib/footer.inc.php";
	die();
}

// Et une autre vérification de sécurité : est-ce que si on a un statut 'responsable' le $login_eleve est bien un élève dont le responsable a la responsabilité
if ($_SESSION['statut'] == "responsable") {
	$test = mysql_query("SELECT count(e.login) " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "')");
	if (mysql_result($test, 0) == 0) {
	    tentative_intrusion(3, "Tentative d'un parent de visualiser un bulletin simplifié d'un élève dont il n'est pas responsable légal.");
	    echo "Vous ne pouvez visualiser que les bulletins simplifiés des élèves pour lesquels vous êtes responsable légal.\n";
	    require("../lib/footer.inc.php");
		die();
	}
}

// Et une autre...
if ($_SESSION['statut'] == "eleve" AND $_SESSION['login'] != $login_eleve) {
    tentative_intrusion(3, "Tentative d'un élève de visualiser un bulletin simplifié d'un autre élève.");
    echo "Vous ne pouvez visualiser que vos bulletins simplifiés.\n";
    require("../lib/footer.inc.php");
	die();
}

// Et encore une : si on a un reponsable ou un élève, alors seul l'édition pour un élève seul est autorisée
if (($_SESSION['statut'] == "responsable" OR $_SESSION['statut'] == "eleve") AND $choix_edit != "2") {
    tentative_intrusion(3, "Tentative (élève ou parent) de changement du mode de visualisation d'un bulletin simplifié (le mode imposé est la visualisation pour un seul élève)");
    echo "N'essayez pas de tricher...\n";
    require("../lib/footer.inc.php");
	die();
}

//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
	$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
	if ($test == "0") {
		tentative_intrusion("2", "Tentative d'accès par un prof à une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
		require ("../lib/footer.inc.php");
		die();
	}
}

//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" and $choix_edit == "2") {
if ($_SESSION['statut'] == "professeur" AND
getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes" AND
getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND
$choix_edit == "2") {

	$test = mysql_num_rows(mysql_query("SELECT jeg.* FROM j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jeg.id_groupe = jgp.id_groupe AND jeg.login = '".$login_eleve."')"));
	if ($test == "0") {
		tentative_intrusion("2", "Tentative d'accès par un prof à un bulletin simplifié d'un élève qu'il n'a pas en cours, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cet élève !";
		require ("../lib/footer.inc.php");
		die();
	}
}

// On a passé les barrières, on passe au traitement

$gepiYear = getSettingValue("gepiYear");

if ($periode1 > $periode2) {
  $temp = $periode2;
  $periode2 = $periode1;
  $periode1 = $temp;
}
// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
//echo "\$test_coef=$test_coef<br />";
// Apparemment, $test_coef est réaffecté plus loin dans un des include()
$nb_coef_superieurs_a_zero=$test_coef;


// On regarde si on affiche les catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}


// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
if ($affiche_rang == 'y') {
    $periode_num=$periode1;
    while ($periode_num < $periode2+1) {
        include "../lib/calcul_rang.inc.php";
        $periode_num++;
    }
}

/*
// On regarde si on affiche les catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
*/
//echo "\$choix_edit=$choix_edit<br />";

//=========================
// AJOUT: boireaus 20080316
$coefficients_a_1="non";
$affiche_graph = 'n';
/*
$get_cat = mysql_query("SELECT id FROM matieres_categories");
$categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
  	$categories[] = $row["id"];
}
*/
unset($tab_moy_gen);
//unset($tab_moy_cat_classe);
for($loop=$periode1;$loop<=$periode2;$loop++) {
	$periode_num=$loop;
	include "../lib/calcul_moy_gen.inc.php";
	$tab_moy_gen[$loop]=$moy_generale_classe;
	//$tab_moy_cat_classe
}

$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");

//=========================

if ($choix_edit == '2') {
    //bulletin($login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
    bulletin($login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
}

if ($choix_edit != '2') {

	//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	if ($_SESSION['statut'] == "professeur" AND
	getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes" AND
	getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes") {

		// On ne sélectionne que les élèves que le professeur a en cours
	    if ($choix_edit == '1') {
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom");
	    } else {
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp, j_eleves_professeurs jep " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = p.login AND " .
				"p.professeur = '".$login_prof."'" .
				"p.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom");
	    }
	} else {
	    // On sélectionne sans restriction
	    if ($choix_edit == '1') {
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
	        		"FROM eleves e, j_eleves_classes c " .
	        		"WHERE (" .
	        		"c.id_classe='$id_classe' AND " .
	        		"e.login = c.login" .
	        		") ORDER BY e.nom,e.prenom");
	    } else {
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
	        		"FROM eleves e, j_eleves_classes c, j_eleves_professeurs p " .
	        		"WHERE (" .
	        		"c.id_classe='$id_classe' AND " .
	        		"e.login = c.login AND " .
	        		"p.login=c.login AND " .
	        		"p.professeur='$login_prof'" .
	        		") ORDER BY e.nom,e.prenom");
		}
	}

    $nombre_eleves = mysql_num_rows($appel_liste_eleves);

	//=========================
	// AJOUT: boireaus 20080209
	// Affichage des appréciations saisies pour la classe
	//echo "2 \$test_coef=$test_coef<br />";
	//bulletin_classe($nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$test_coef,$affiche_categories);
	bulletin_classe($nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);
	echo "<p class=saut>&nbsp;</p>\n";
	//=========================

    $i=0;
    $k=0;
    while ($i < $nombre_eleves) {
        $current_eleve_login = mysql_result($appel_liste_eleves, $i, "login");
        $k++;
        //bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
        bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
        if ($i != $nombre_eleves-1) {echo "<p class=saut>&nbsp;</p>";}
        $i++;
    }

}
require("../lib/footer.inc.php");
?>