<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

// Ebauche de liste des variables reçues:
// $choix_edit correspond au choix de ce qui doit être affiché:
// Pour $choix_edit=1:
//    - Tous les élèves que le prof a en cours, ou rattaché à une classe qu'a le prof, ou tous les élèves selon le choix paramétré en admin dans Droits d'accès
//    - En compte scolarité ou cpe: Tous les élèves de la classe
// $choix_edit=2
//    - Uniquement l'élève sélectionné: la variable $login_eleve, qui est de toute façon affectée, doit alors être prise en compte pour limiter l'affichage à cet élève
// $choix_edit=3
//    - Ce choix correspond aux classes avec plusieurs professeurs principaux
//      On a alors une variable $login_prof affectée pour limiter les affichages aux élèves suivi par un des profs principaux seulement
//      Cette variable $login_prof ne devrait être prise en compte que dans le cas $choix_edit==3
// $choix_edit=4
//    - Affichage du bulletin des avis sur la classe


// Vérification sur $id_classe
if(!isset($id_classe)) {
	if(isset($login_eleve)) {
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$login_eleve' ORDER BY periode DESC LIMIT 1;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$id_classe=$lig->id_classe;
		}
	}

	if(!isset($id_classe)) {
		header("Location: ../accueil.php?msg=Classe non choisie pour les bulletins simplifiés");
		die();
	}
}
elseif(!is_numeric($id_classe)) {
	header("Location: ../accueil.php?msg=Classe invalide ($id_classe) pour les bulletins simplifiés");
	die();
}
$nom_classe=get_nom_classe($id_classe);
if(!$nom_classe) {
	header("Location: ../accueil.php?msg=Classe invalide ($id_classe) pour les bulletins simplifiés");
	die();
}

//==============================
include "../lib/periodes.inc.php";
include "../lib/bulletin_simple.inc.php";
include "../lib/bulletin_simple_classe.inc.php";
//==============================
if(in_array($_SESSION['statut'], array('administrateur', 'professeur', 'scolarite', 'cpe', 'secours'))) {
	$javascript_specifique[] = "lib/tablekit";
	$utilisation_tablekit="ok";
}
require_once("../lib/header.inc.php");
//==============================

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
	$sql="(SELECT e.login " .
			"FROM eleves e, responsables2 re, resp_pers r " .
			"WHERE (" .
			"e.login = '" . $login_eleve . "' AND " .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login FROM eleves e, resp_pers r, responsables2 re 
						WHERE (e.login = '" . $login_eleve . "' AND
							e.ele_id = re.ele_id AND 
							re.pers_id = r.pers_id AND 
							r.login = '".$_SESSION['login']."' AND 
							re.resp_legal='0' AND 
							re.acces_sp='y'))";
	}
	$sql.=";";
	//echo "$sql<br />";
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($test) == 0) {
	    tentative_intrusion(3, "Tentative d'un parent de visualiser un bulletin simplifié d'un élève ($login_eleve) dont il n'est pas responsable légal.");
	    echo "Vous ne pouvez visualiser que les bulletins simplifiés des élèves pour lesquels vous êtes responsable légal.\n";
	    require("../lib/footer.inc.php");
		die();
	}
}

// Et une autre...
if ($_SESSION['statut'] == "eleve" AND my_strtoupper($_SESSION['login']) != my_strtoupper($login_eleve)) {
    tentative_intrusion(3, "Tentative d'un élève de visualiser un bulletin simplifié d'un autre élève ($login_eleve).");
    echo "Vous ne pouvez visualiser que vos bulletins simplifiés.\n";
    require("../lib/footer.inc.php");
	die();
}

// Et encore une : si on a un reponsable ou un élève, alors seul l'édition pour un élève seul est autorisée
if ($_SESSION['statut'] == "responsable" AND $choix_edit != "2") {
	if((!getSettingAOui('GepiAccesBulletinSimpleClasseResp'))||($choix_edit != "4")) {
		tentative_intrusion(3, "Tentative parent de changement du mode de visualisation d'un bulletin simplifié (le mode imposé est la visualisation pour un seul élève)");
		echo "N'essayez pas de tricher...\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		// Récupérer l'id_classe:
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='".$login_eleve."' ORDER BY periode DESC LIMIT 1;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$id_classe=old_mysql_result($res, 0, "id_classe");
		}
	}
}

if ($_SESSION['statut'] == "eleve" AND $choix_edit != "2") {
	if((!getSettingAOui('GepiAccesBulletinSimpleClasseEleve'))||($choix_edit != "4")) {
		tentative_intrusion(3, "Tentative élève de changement du mode de visualisation d'un bulletin simplifié (le mode imposé est la visualisation pour un seul élève)");
		echo "N'essayez pas de tricher...\n";
		require("../lib/footer.inc.php");
		die();
	}
	else {
		// Récupérer l'id_classe:
		$sql="SELECT id_classe FROM j_eleves_classes WHERE login='".$login_eleve."' ORDER BY periode DESC LIMIT 1;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$id_classe=old_mysql_result($res, 0, "id_classe");
		}
	}
}

if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes") {
	// On vérifie si le prof peut pour une raison (droit) ou une autre accéder à au moins un élève de la classe
	if(is_pp($_SESSION['login'], $id_classe)) {
		if(getSettingAOui('GepiAccesBulletinSimpleProf')) {
			$sql="(SELECT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')) " .
				"UNION (SELECT jec.id_classe " .
				"FROM j_eleves_classes jec, j_eleves_professeurs jep " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"jep.login = jec.login AND " .
				"jep.professeur = '".$_SESSION['login']."'));";
		}
		else {
			$sql="SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_professeurs jep " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jep.login AND " .
				"jep.login = jec.login AND " .
				"jep.professeur = '".$_SESSION['login']."') ORDER BY e.nom,e.prenom;";
		}
	}
	else {
	    $sql="SELECT DISTINCT e.* " .
			"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
			"WHERE (" .
			"jec.id_classe='$id_classe' AND " .
			"e.login = jeg.login AND " .
			"jeg.login = jec.login AND " .
			"jeg.id_groupe = jgp.id_groupe AND " .
			"jgp.login = '".$_SESSION['login']."') " .
			"ORDER BY e.nom,e.prenom";
	}
	//echo "$sql<br />";
	$res_test = mysqli_query($GLOBALS["mysqli"], $sql);

	$test = mysqli_num_rows($res_test);
	//$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));

	if ($test == "0") {
		tentative_intrusion("2", "Tentative d'accès par un prof à une classe (".$nom_classe.") dans laquelle il n'enseigne pas, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
		require ("../lib/footer.inc.php");
		die();
	}
}

if ($_SESSION['statut'] == "professeur" AND
getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes" AND
getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes" AND
$choix_edit == "2") {
	//$choix_edit==2 : on teste le droit du prof d'accéder au bulletin d'un élève en particulier.

	if(is_pp($_SESSION['login'], "", $login_eleve)) {
		if(getSettingAOui('GepiAccesBulletinSimpleProf')) {
			$sql="(SELECT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgp.id_groupe = jeg.id_groupe AND jeg.login = '".$login_eleve."')) " .
				"UNION (SELECT DISTINCT jep.login " .
				"FROM j_eleves_professeurs jep " .
				"WHERE (" .
				"jep.login='$login_eleve' AND " .
				"jep.professeur = '".$_SESSION['login']."'));";
		}
		else {
			$sql="SELECT DISTINCT jep.login " .
				"FROM j_eleves_professeurs jep " .
				"WHERE (" .
				"jep.login='$login_eleve' AND " .
				"jep.professeur = '".$_SESSION['login']."');";
		}
	}
	else {
		$sql="SELECT jeg.* FROM j_eleves_groupes jeg, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jeg.id_groupe = jgp.id_groupe AND jeg.login = '".$login_eleve."')";
	}
	//echo "$sql<br />";

	$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));
	if ($test == "0") {
		tentative_intrusion("2", "Tentative d'accès par un prof à un bulletin simplifié d'un élève ($login_eleve) qu'il n'a pas en cours, sans en avoir l'autorisation.");
		echo "Vous ne pouvez pas accéder à cet élève !";
		require ("../lib/footer.inc.php");
		die();
	}
}

$affiche_colonne_moy_classe="y";
if((($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseEleve')))||
(($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseResp')))) {
	$affiche_colonne_moy_classe="n";
}
if((isset($_POST['pas_de_colonne_moy_classe']))&&($_POST['pas_de_colonne_moy_classe']=='y')) {
	$affiche_colonne_moy_classe="n";
}
//echo "\$affiche_colonne_moy_classe=$affiche_colonne_moy_classe<br />";

// debug_var();
// On a passé les barrières, on passe au traitement

$gepiYear = getSettingValue("gepiYear");

if ($periode1 > $periode2) {
  $temp = $periode2;
  $periode2 = $periode1;
  $periode1 = $temp;
}

// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
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

//=========================
// AJOUT: boireaus 20080316
$coefficients_a_1="non";
$affiche_graph = 'n';

for($loop=$periode1;$loop<=$periode2;$loop++) {
	$periode_num=$loop;
	include "../lib/calcul_moy_gen.inc.php";

	$tab_moy['periodes'][$periode_num]=array();
	$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
	$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
	$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
	$tab_moy['periodes'][$periode_num]['moy_generale_classe']=$moy_generale_classe;
	$tab_moy['periodes'][$periode_num]['moy_generale_classe1']=$moy_generale_classe1;
	$tab_moy['periodes'][$periode_num]['moy_max_classe']=$moy_max_classe;
	$tab_moy['periodes'][$periode_num]['moy_min_classe']=$moy_min_classe;

	// Il faudrait récupérer/stocker les catégories?
	$tab_moy['periodes'][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
	$tab_moy['periodes'][$periode_num]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
	$tab_moy['periodes'][$periode_num]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
	$tab_moy['periodes'][$periode_num]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]

	$tab_moy['periodes'][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
	$tab_moy['periodes'][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
	$tab_moy['periodes'][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
	$tab_moy['periodes'][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
	$tab_moy['periodes'][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
	$tab_moy['periodes'][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;
	$tab_moy['periodes'][$periode_num]['place_eleve_classe']=$place_eleve_classe;

	$tab_moy['periodes'][$periode_num]['current_eleve_login']=$current_eleve_login;   // [$i]
	if($loop==$periode1) {
		$tab_moy['current_group']=$current_group;                                     // [$j]
	}
	$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
	$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
	$tab_moy['periodes'][$periode_num]['current_coef']=$current_coef;                 // [$j]
	$tab_moy['periodes'][$periode_num]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]

	$tab_moy['periodes'][$periode_num]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
	$tab_moy['periodes'][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
	$tab_moy['periodes'][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
	if(isset($current_eleve_rang)) {
		// $current_eleve_rang n'est pas renseigné si $affiche_rang='n'
		$tab_moy['periodes'][$periode_num]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
	}
	$tab_moy['periodes'][$periode_num]['quartile1_grp']=$quartile1_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['quartile2_grp']=$quartile2_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['quartile3_grp']=$quartile3_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['quartile4_grp']=$quartile4_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['quartile5_grp']=$quartile5_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['quartile6_grp']=$quartile6_grp;               // [$j]
	$tab_moy['periodes'][$periode_num]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]

	$tab_moy['periodes'][$periode_num]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]
}
$tab_moy['categories']['id']=$categories;
$tab_moy['categories']['nom_from_id']=$tab_noms_categories;
$tab_moy['categories']['id_from_nom']=$tab_id_categories;


$sql="SELECT DISTINCT e.*
FROM eleves e, j_eleves_classes c 
WHERE (
c.id_classe='$id_classe' AND 
e.login = c.login
) ORDER BY e.nom,e.prenom;";
$res_ele= mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_ele)>0) {
	while($lig_ele=mysqli_fetch_object($res_ele)) {
		$tab_moy['eleves'][]=$lig_ele->login;
	}
}

$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."';");

$affiche_coef=sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."';");

if(!getSettingValue("bull_intitule_app")){
	$bull_intitule_app="Appréciations/Conseils";
}
else{
	$bull_intitule_app=getSettingValue("bull_intitule_app");
}

//=========================
// Sauvegarde le temps de la session des paramètres pour le passage d'une classe à une autre
$_SESSION['choix_edit']=$choix_edit;
$_SESSION['periode1']=$periode1;
$_SESSION['periode2']=$periode2;
if(isset($login_prof)) {$_SESSION['login_prof']=$login_prof;}
//=========================

// Pour ajouter une marge:
echo "<div id='div_prepa_conseil_bull_simp'";
if(isset($_POST['bull_simp_pref_marges'])) {
	$bull_simp_pref_marges=preg_replace('/[^0-9]/','',$_POST['bull_simp_pref_marges']);
	if($bull_simp_pref_marges!='') {
		echo " style='margin:".$bull_simp_pref_marges."px;'";
		savePref($_SESSION['login'],'bull_simp_pref_marges',$bull_simp_pref_marges);
	}
	// Pour permettre de ne pas inserer de margin et memoriser ce choix, on accepte le champ vide:
	$_SESSION['bull_simp_pref_marges']=$bull_simp_pref_marges;
}
echo ">\n";

$couleur_alterne=isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] : (isset($_GET['couleur_alterne']) ? $_GET['couleur_alterne'] : "n");
if(($couleur_alterne!='y')&&($couleur_alterne!='n')) {
	$couleur_alterne="n";
}
else {
	savePref($_SESSION['login'],'bull_simp_pref_couleur_alterne',$couleur_alterne);
}

//====================================
$bull_simp_larg_tab_defaut = 680;
$bull_simp_larg_col1_defaut = 120;
$bull_simp_larg_col2_defaut = 38;
$bull_simp_larg_col3_defaut = 38;
$bull_simp_larg_col4_defaut = 20;

$bull_simp_larg_tab=isset($_POST['bull_simp_larg_tab']) ? $_POST['bull_simp_larg_tab'] : (isset($_GET['bull_simp_larg_tab']) ? $_GET['bull_simp_larg_tab'] : getPref($_SESSION['login'], 'bull_simp_larg_tab', $bull_simp_larg_tab_defaut));
$bull_simp_larg_col1=isset($_POST['bull_simp_larg_col1']) ? $_POST['bull_simp_larg_col1'] : (isset($_GET['bull_simp_larg_col1']) ? $_GET['bull_simp_larg_col1'] : getPref($_SESSION['login'], 'bull_simp_larg_col1', $bull_simp_larg_col1_defaut));
$bull_simp_larg_col2=isset($_POST['bull_simp_larg_col2']) ? $_POST['bull_simp_larg_col2'] : (isset($_GET['bull_simp_larg_col2']) ? $_GET['bull_simp_larg_col2'] : getPref($_SESSION['login'], 'bull_simp_larg_col2', $bull_simp_larg_col2_defaut));
$bull_simp_larg_col3=isset($_POST['bull_simp_larg_col3']) ? $_POST['bull_simp_larg_col3'] : (isset($_GET['bull_simp_larg_col3']) ? $_GET['bull_simp_larg_col3'] : getPref($_SESSION['login'], 'bull_simp_larg_col3', $bull_simp_larg_col3_defaut));
$bull_simp_larg_col4=isset($_POST['bull_simp_larg_col4']) ? $_POST['bull_simp_larg_col4'] : (isset($_GET['bull_simp_larg_col4']) ? $_GET['bull_simp_larg_col4'] : getPref($_SESSION['login'], 'bull_simp_larg_col4', $bull_simp_larg_col4_defaut));
if(!preg_match("/^[0-9]{1,}$/", $bull_simp_larg_tab)) {
	$bull_simp_larg_tab=$bull_simp_larg_tab_defaut;
}
if(!preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col1)) {
	$bull_simp_larg_col1=$bull_simp_larg_col1_defaut;
}
if(!preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col2)) {
	$bull_simp_larg_col2=$bull_simp_larg_col2_defaut;
}
if(!preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col3)) {
	$bull_simp_larg_col3=$bull_simp_larg_col3_defaut;
}
if(!preg_match("/^[0-9]{1,}$/", $bull_simp_larg_col4)) {
	$bull_simp_larg_col4=$bull_simp_larg_col4_defaut;
}
if($bull_simp_larg_tab<$bull_simp_larg_col1+$bull_simp_larg_col2+$bull_simp_larg_col3+$bull_simp_larg_col4) {
	$bull_simp_larg_tab = $bull_simp_larg_tab_defaut;
	$bull_simp_larg_col1 = $bull_simp_larg_col1_defaut;
	$bull_simp_larg_col2 = $bull_simp_larg_col2_defaut;
	$bull_simp_larg_col3 = $bull_simp_larg_col3_defaut;
	$bull_simp_larg_col4 = $bull_simp_larg_col4_defaut;
}
savePref($_SESSION['login'], 'bull_simp_larg_tab', $bull_simp_larg_tab);
savePref($_SESSION['login'], 'bull_simp_larg_col1', $bull_simp_larg_col1);
savePref($_SESSION['login'], 'bull_simp_larg_col2', $bull_simp_larg_col2);
savePref($_SESSION['login'], 'bull_simp_larg_col3', $bull_simp_larg_col3);
savePref($_SESSION['login'], 'bull_simp_larg_col4', $bull_simp_larg_col4);
//====================================

if ($choix_edit == '2') {
	bulletin($tab_moy,$login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,$couleur_alterne);
}

//echo "choix_edit=$choix_edit<br />";
if ($choix_edit != '2') {
	// Si on arrive là, on n'est ni élève, ni responsable

	unset($sql);
	//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	if ($_SESSION['statut'] == "professeur" AND
	getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes" AND
	getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes") {

		// On ne sélectionne que les élèves que le professeur a en cours
	    //if ($choix_edit == '1') {
	    if (($choix_edit == '1')||(!isset($login_prof))) {
			// On a alors $choix_edit==1 ou $choix_edit==4
			/*
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom");
			*/
			if(is_pp($_SESSION['login'], $id_classe)) {
				if(getSettingAOui('GepiAccesBulletinSimpleProf')) {
					$sql="(SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jeg.login AND " .
						"jeg.login = jec.login AND " .
						"jeg.id_groupe = jgp.id_groupe AND " .
						"jgp.login = '".$_SESSION['login']."')) " .
						"UNION (SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_professeurs jep " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jep.login AND " .
						"jep.login = jec.login AND " .
						"jep.professeur = '".$_SESSION['login']."')) ORDER BY nom,prenom;";
					$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
				}
				else {
					$sql="SELECT DISTINCT e.* " .
						"FROM eleves e, j_eleves_classes jec, j_eleves_professeurs jep " .
						"WHERE (" .
						"jec.id_classe='$id_classe' AND " .
						"e.login = jep.login AND " .
						"jep.login = jec.login AND " .
						"jep.professeur = '".$_SESSION['login']."') ORDER BY e.nom,e.prenom;";
					$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
			else {
			    $sql="SELECT DISTINCT e.* " .
					"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
					"WHERE (" .
					"jec.id_classe='$id_classe' AND " .
					"e.login = jeg.login AND " .
					"jeg.login = jec.login AND " .
					"jeg.id_groupe = jgp.id_groupe AND " .
					"jgp.login = '".$_SESSION['login']."') " .
					"ORDER BY e.nom,e.prenom";
				$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
			}
	    } else {
			// On a alors $choix_edit==3 uniquement les élèves du professeur principal $login_prof
			if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
				// Tous les élèves vont être affichés
				$sql="SELECT DISTINCT e.* " .
					"FROM eleves e, j_eleves_classes jec " .
					"WHERE (" .
					"jec.id_classe='$id_classe' AND " .
					"jec.login=e.login) ".
					"ORDER BY e.nom,e.prenom";
			}
			else {
				$sql="SELECT DISTINCT e.* " .
					"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp, j_eleves_professeurs jep " .
					"WHERE (" .
					"jec.id_classe='$id_classe' AND " .
					"e.login = jeg.login AND " .
					"jeg.login = jep.login AND " .
					"jep.professeur = '".$login_prof."' AND " .
					"jep.login = jec.login AND " .
					"jeg.id_groupe = jgp.id_groupe AND " .
					"jgp.login = '".$_SESSION['login']."') " .
					"ORDER BY e.nom,e.prenom";
				}
	    }
	} else {
	    // On sélectionne sans restriction

	    //if ($choix_edit == '1') {
	    if (($choix_edit == '1')||(!isset($login_prof))) {
			// On a alors $choix_edit==1 ou $choix_edit==4
	        $sql="SELECT DISTINCT e.* " .
	        		"FROM eleves e, j_eleves_classes c " .
	        		"WHERE (" .
	        		"c.id_classe='$id_classe' AND " .
	        		"e.login = c.login" .
	        		") ORDER BY e.nom,e.prenom";
	    } else {
			// On a alors $choix_edit==3
	        $sql="SELECT DISTINCT e.* " .
	        		"FROM eleves e, j_eleves_classes c, j_eleves_professeurs jep " .
	        		"WHERE (" .
	        		"c.id_classe='$id_classe' AND " .
	        		"e.login = c.login AND " .
	        		"jep.login=c.login AND " .
	        		"jep.professeur='$login_prof'" .
	        		") ORDER BY e.nom,e.prenom";
		}
	}

	if(!isset($sql)) {
		echo "<p style='color:red'>Aucune liste d'élèves n'a été extraite.<br />Êtes-vous bine autorisé à vous trouver ici&nbsp;?</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	//echo "$sql<br />";
	$appel_liste_eleves = mysqli_query($GLOBALS["mysqli"], $sql);

    $nombre_eleves = mysqli_num_rows($appel_liste_eleves);

	$avec_moy_min_max_classe="y";
	if((($_SESSION['statut']=='eleve')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseMinMaxEleve')))||
	(($_SESSION['statut']=='responsable')&&(!getSettingAOui('GepiAccesBulletinSimpleColonneMoyClasseMinMaxResp')))) {
		$avec_moy_min_max_classe="n";
	}
	//echo "\$avec_moy_min_max_classe=$avec_moy_min_max_classe<br />";
	//=========================
	// AJOUT: boireaus 20080209
	// Affichage des appréciations saisies pour la classe
	bulletin_classe($tab_moy,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories,$couleur_alterne);
	if ($choix_edit == '4') {
		require("../lib/footer.inc.php");
		die();
	}
	echo "<p class=saut>&nbsp;</p>\n";
	//=========================

    $i=0;
    $k=0;
    while ($i < $nombre_eleves) {
        $current_eleve_login = old_mysql_result($appel_liste_eleves, $i, "login");
        $k++;
        //bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
        //bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
        //bulletin_bis($tab_moy,$current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
        bulletin($tab_moy,$current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,$couleur_alterne);
        if ($i != $nombre_eleves-1) {echo "<p class=saut>&nbsp;</p>";}
        $i++;
    }

}

echo "</div>\n"; // Fin du div_prepa_conseil_bull_simp

echo "<div class='noprint'>\n";
//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide</p>\n";
//===========================================================
echo "</div>\n";

require("../lib/footer.inc.php");
?>
