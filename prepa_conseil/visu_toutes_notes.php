<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
if (getSettingValue("active_module_absence")=='2'){
    require_once("../lib/initialisationsPropel.inc.php");
}

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

//debug_var();

//Initialisation
//$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
// Modifié pour pouvoir récupérer ces variables en GET pour les CSV
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : "1");

$utiliser_coef_perso=isset($_POST['utiliser_coef_perso']) ? $_POST['utiliser_coef_perso'] : (isset($_GET['utiliser_coef_perso']) ? $_GET['utiliser_coef_perso'] : "n");
$coef_perso=isset($_POST['coef_perso']) ? $_POST['coef_perso'] : (isset($_GET['coef_perso']) ? $_GET['coef_perso'] : NULL);

//$note_sup_10=isset($_POST['note_sup_10']) ? $_POST['note_sup_10'] : (isset($_GET['note_sup_10']) ? $_GET['note_sup_10'] : NULL);
//$mode_moy_perso=isset($_POST['mode_moy_perso']) ? $_POST['mode_moy_perso'] : (isset($_GET['mode_moy_perso']) ? $_GET['mode_moy_perso'] : NULL);
$mode_moy_perso=isset($_POST['mode_moy_perso']) ? $_POST['mode_moy_perso'] : (isset($_GET['mode_moy_perso']) ? $_GET['mode_moy_perso'] : array());

if ($num_periode=="annee") {
	$referent="annee";
} else {
	$referent="une_periode";
}

//$mode_calcul_moy_annee="moyenne_des_moy_enseignements";
$mode_calcul_moy_annee="moyenne_des_moy_gen_periodes";

// On filtre au niveau sécurité pour s'assurer qu'un prof n'est pas en train de chercher
// à visualiser des données pour lesquelles il n'est pas autorisé

if (isset($id_classe)) {
	// On regarde si le type est correct :
	if (!is_numeric($id_classe)) {
		tentative_intrusion("3", "Changement de la valeur de id_classe pour un type non numérique, en changeant la valeur d'un champ 'hidden' d'un formulaire.");
		echo "Erreur.";
		require ("../lib/footer.inc.php");
		die();
	}
	// On teste si le professeur a le droit d'accéder à cette classe
	if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			tentative_intrusion("3", "Tentative d'accès par un prof à une classe dans laquelle il n'enseigne pas, sans en avoir l'autorisation. Tentative avancée : changement des valeurs de champs de type 'hidden' du formulaire.");
			echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
			require ("../lib/footer.inc.php");
			die();
		}
	}
}


function my_echo($texte) {
	$debug=0;
	if($debug!=0) {
		echo $texte;
	}
}


$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;
$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;

//$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] :  NULL;
//$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] :  NULL;
//$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] :  NULL;
//$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] :  NULL;

// Modifié pour pouvoir récupérer ces variables en GET pour les CSV
$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] : (isset($_GET['aff_abs']) ? $_GET['aff_abs'] : NULL);
$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] : (isset($_GET['aff_reg']) ? $_GET['aff_reg'] : NULL);
$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] : (isset($_GET['aff_doub']) ? $_GET['aff_doub'] : NULL);
$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] : (isset($_GET['aff_rang']) ? $_GET['aff_rang'] : NULL);

//echo "\$aff_rang=$aff_rang<br />";
//echo "\$aff_reg=$aff_reg<br />";

//============================
//$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] :  NULL;
$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] : (isset($_GET['aff_date_naiss']) ? $_GET['aff_date_naiss'] : NULL);
//============================
//echo "\$aff_date_naiss=$aff_date_naiss<br />";

$couleur_alterne = isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] : (isset($_GET['couleur_alterne']) ? $_GET['couleur_alterne'] : NULL);

//================================
if(file_exists("../visualisation/draw_graphe.php")){
	$temoin_graphe="oui";
}
else{
	$temoin_graphe="non";
}
//================================

//============================
// Colorisation des résultats
$vtn_couleur_texte=isset($_POST['vtn_couleur_texte']) ? $_POST['vtn_couleur_texte'] : array();
$vtn_couleur_cellule=isset($_POST['vtn_couleur_cellule']) ? $_POST['vtn_couleur_cellule'] : array();
$vtn_borne_couleur=isset($_POST['vtn_borne_couleur']) ? $_POST['vtn_borne_couleur'] : array();
$vtn_coloriser_resultats=isset($_POST['vtn_coloriser_resultats']) ? $_POST['vtn_coloriser_resultats'] : "n";
/*
for($i=0;$i<count($vtn_borne_couleur);$i++) {
echo "\$vtn_borne_couleur[$i]=$vtn_borne_couleur[$i]<br />\n";
}
*/
//============================

//debug_var();

//============================
$avec_moy_gen_periodes_precedentes = isset($_POST['avec_moy_gen_periodes_precedentes']) ? $_POST['avec_moy_gen_periodes_precedentes'] :  (isset($_GET['avec_moy_gen_periodes_precedentes']) ? $_GET['avec_moy_gen_periodes_precedentes'] :  NULL);
//============================

include "../lib/periodes.inc.php";

// On appelle les élèves
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	// On ne sélectionne que les élèves que le professeur a en cours
	if ($referent=="une_periode")
		// Calcul sur une seule période
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."' AND " .
				"jec.periode = '$num_periode' AND " .
				"jeg.periode = '$num_periode') " .
				"ORDER BY e.nom,e.prenom");
	else {
		// Calcul sur l'année
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* " .
				"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
				"WHERE (" .
				"jec.id_classe='$id_classe' AND " .
				"e.login = jeg.login AND " .
				"jeg.login = jec.login AND " .
				"jeg.id_groupe = jgp.id_groupe AND " .
				"jgp.login = '".$_SESSION['login']."') " .
				"ORDER BY e.nom,e.prenom");
	}
} else {
	if ($referent=="une_periode")
		// Calcul sur une seule période
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
	else {
		// Calcul sur l'année
		$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
	}
}

$nb_lignes_eleves = mysqli_num_rows($appel_donnees_eleves);
$nb_lignes_tableau = $nb_lignes_eleves;

//==============================
// Initialisation
// Conservé pour le mode annee
$moy_classe_point = 0;
$moy_classe_effectif = 0;
$moy_classe_min = 20;
$moy_classe_max = 0;
$moy_cat_classe_point = array();
$moy_cat_classe_effectif = array();
$moy_cat_classe_min = array();
$moy_cat_classe_max = array();
//==============================


// =====================================
// AJOUT: boireaus
$largeur_graphe=700;
$hauteur_graphe=600;
$taille_police=3;
$epaisseur_traits=2;
$titre="Graphe";
$graph_title=$titre;
//$v_legend2="moyclasse";
$compteur=0;
$nb_series=2;

if(getSettingValue('graphe_largeur_graphe')){
	$largeur_graphe=getSettingValue('graphe_largeur_graphe');
}
else{
	$largeur_graphe=600;
}

if(getSettingValue('graphe_hauteur_graphe')){
	$hauteur_graphe=getSettingValue('graphe_hauteur_graphe');
}
else{
	$hauteur_graphe=400;
}

if(getSettingValue('graphe_taille_police')){
	$taille_police=getSettingValue('graphe_taille_police');
}
else{
	$taille_police=2;
}

if(getSettingValue('graphe_epaisseur_traits')){
	$epaisseur_traits=getSettingValue('graphe_epaisseur_traits');
}
else{
	$epaisseur_traits=2;
}

if(getSettingValue('graphe_temoin_image_escalier')){
	$temoin_image_escalier=getSettingValue('graphe_temoin_image_escalier');
}
else{
	$temoin_image_escalier="non";
}

if(getSettingValue('graphe_tronquer_nom_court')){
	$tronquer_nom_court=getSettingValue('graphe_tronquer_nom_court');
}
else{
	$tronquer_nom_court=0;
}

// =====================================

// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$sql="SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0);";
//echo "$sql<br />";
//$test_coef=mysql_num_rows(mysql_query($sql));
$nb_coef_non_nuls=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));
$ligne_supl = 0;
if ($nb_coef_non_nuls!=0) {$ligne_supl = 1;}
//echo "\$test_coef=$test_coef<br />";
//echo "\$ligne_supl=$ligne_supl<br />";
// Dans calcul_moy_gen.inc.php, $test_coef est le résultat d'une requête mysql_query()
// On met en réserve le $test_coef correspondant au nombre de coef non nuls
//$test_coef_avant_calcul_moy_gen=$test_coef;

$temoin_note_sup10="n";
$temoin_note_bonus="n";
if($utiliser_coef_perso=='y') {
	/*
	if(isset($note_sup_10)) {
		$ligne_supl++;
		$temoin_note_sup10="y";
	}
	*/
	$nb_note_sup_10=0;
	$nb_note_bonus=0;
	foreach($mode_moy_perso as $tmp_id_groupe => $tmp_mode_moy) {
		if($mode_moy_perso[$tmp_id_groupe]=='sup10') {
			$temoin_note_sup10="y";
			$nb_note_sup_10++;
		}
		if($mode_moy_perso[$tmp_id_groupe]=='bonus') {
			$temoin_note_bonus="y";
			$nb_note_bonus++;
		}
	}
}
else {
	$sql="SELECT 1=1 FROM j_groupes_classes jgc WHERE jgc.id_classe='".$id_classe."' AND jgc.mode_moy='sup10';";
	$test_note_sup10=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_note_sup_10=mysqli_num_rows($test_note_sup10);
	if($nb_note_sup_10>0) {
		//$ligne_supl++;
		$temoin_note_sup10="y";
	}

	$sql="SELECT 1=1 FROM j_groupes_classes jgc WHERE jgc.id_classe='".$id_classe."' AND jgc.mode_moy='bonus';";
	$test_note_bonus=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_note_bonus=mysqli_num_rows($test_note_bonus);
	if($nb_note_bonus>0) {
		//$ligne_supl++;
		$temoin_note_bonus="y";
	}
}

if(($temoin_note_sup10=="y")||($temoin_note_bonus=="y")) {
	$ligne_supl++;
}

// On regarde si on doit afficher les moyennes des catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}

// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
if (($aff_rang) and ($referent=="une_periode")) {
	$periode_num=$num_periode;

	// La variable $test_coef est réclamée par calcul_rang.inc.php
	if(!isset($test_coef)) {
		$test_coef=$nb_coef_non_nuls;
	}

	include "../lib/calcul_rang.inc.php";
}

/*
// On regarde si on doit afficher les moyennes des catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}
*/

if ($affiche_categories) {
	$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
		$categories[] = $row["id"];
		$moy_cat_classe_point[$row["id"]] = 0;
		$moy_cat_classe_effectif[$row["id"]] = 0;
		$moy_cat_classe_min[$row["id"]] = 20;
		$moy_cat_classe_max[$row["id"]] = 0;
	}

	$cat_names = array();
	foreach ($categories as $cat_id) {
		//$cat_names[$cat_id] = html_entity_decode(old_mysql_result(mysql_query("SELECT nom_court FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0));
		$cat_names[$cat_id] = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT nom_court FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0);
	}
}

//$avec_moy_gen_periodes_precedentes="y";

// $nb_periode vaut 4 s'il y a 3 périodes
//echo "\$nb_periode=$nb_periode<br />";
if($referent=="une_periode") {
	if(!isset($avec_moy_gen_periodes_precedentes)) {
		$p=$num_periode;
		// Pour faire un tour dans la boucle seulement:
		$periode_limit=$p+1;
	}
	else {
		$p=1;
		// Pour faire un tour dans la boucle seulement:
		$periode_limit=$num_periode+1;
	}
}
else {
	$p=1;
	// Pour aller jusqu'à la dernière période
	$periode_limit=$nb_periode;
	// $nb_periode initialisé par periodes.inc.php vaut 4 dans le cas où il y a 3 trimestres
}

$coefficients_a_1="non";
$affiche_graph="n";

while ($p < $periode_limit) {
	$periode_num=$p;
	include "../lib/calcul_moy_gen.inc.php";

	// Dans calcul_moy_gen.inc.php, les indices $i et $j sont:
	// $i: élève
	// $j: groupe
	$tab_moy['periodes'][$p]=array();
	$tab_moy['periodes'][$p]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
	$tab_moy['periodes'][$p]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
	$tab_moy['periodes'][$p]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
	//$tab_moy['periodes'][$p]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
	$tab_moy['periodes'][$p]['moy_generale_classe']=$moy_generale_classe;
	$tab_moy['periodes'][$p]['moy_generale_classe1']=$moy_generale_classe1;
	$tab_moy['periodes'][$p]['moy_max_classe']=$moy_max_classe;
	$tab_moy['periodes'][$p]['moy_min_classe']=$moy_min_classe;

	// Il faudrait récupérer/stocker les catégories?
	$tab_moy['periodes'][$p]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
	$tab_moy['periodes'][$p]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
	$tab_moy['periodes'][$p]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
	$tab_moy['periodes'][$p]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]

	$tab_moy['periodes'][$p]['quartile1_classe_gen']=$quartile1_classe_gen;
	$tab_moy['periodes'][$p]['quartile2_classe_gen']=$quartile2_classe_gen;
	$tab_moy['periodes'][$p]['quartile3_classe_gen']=$quartile3_classe_gen;
	$tab_moy['periodes'][$p]['quartile4_classe_gen']=$quartile4_classe_gen;
	$tab_moy['periodes'][$p]['quartile5_classe_gen']=$quartile5_classe_gen;
	$tab_moy['periodes'][$p]['quartile6_classe_gen']=$quartile6_classe_gen;
	$tab_moy['periodes'][$p]['place_eleve_classe']=$place_eleve_classe;

	$tab_moy['periodes'][$p]['current_eleve_login']=$current_eleve_login;   // [$i]
	//$tab_moy['periodes'][$p]['current_group']=$current_group;
	if(($p==1)||((isset($num_periode))&&($p==$num_periode))) {
		$tab_moy['current_group']=$current_group;                                     // [$j]
	}
	$tab_moy['periodes'][$p]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
	$tab_moy['periodes'][$p]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
	//$tab_moy['periodes'][$p]['current_group']=$current_group;
	$tab_moy['periodes'][$p]['current_coef']=$current_coef;                 // [$j]
	$tab_moy['periodes'][$p]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]

	$tab_moy['periodes'][$p]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
	$tab_moy['periodes'][$p]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
	$tab_moy['periodes'][$p]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
	if(isset($current_eleve_rang)) {
		// $current_eleve_rang n'est pas renseigné si $affiche_rang='n'
		$tab_moy['periodes'][$p]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
	}
	$tab_moy['periodes'][$p]['quartile1_grp']=$quartile1_grp;               // [$j]
	$tab_moy['periodes'][$p]['quartile2_grp']=$quartile2_grp;               // [$j]
	$tab_moy['periodes'][$p]['quartile3_grp']=$quartile3_grp;               // [$j]
	$tab_moy['periodes'][$p]['quartile4_grp']=$quartile4_grp;               // [$j]
	$tab_moy['periodes'][$p]['quartile5_grp']=$quartile5_grp;               // [$j]
	$tab_moy['periodes'][$p]['quartile6_grp']=$quartile6_grp;               // [$j]
	$tab_moy['periodes'][$p]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]

	$tab_moy['periodes'][$p]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]

	$p++;
}

$tab_moy['categories']['id']=$categories;
$tab_moy['categories']['nom_from_id']=$tab_noms_categories;
$tab_moy['categories']['id_from_nom']=$tab_id_categories;



/*
	// Calcul du nombre de matières à afficher
	if ($affiche_categories) {
		// On utilise les valeurs spécifiées pour la classe en question
		//$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
		$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id, jgc.mode_moy ".
		"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
		"WHERE ( " .
		"jgc.categorie_id = jmcc.categorie_id AND " .
		"jgc.id_classe='".$id_classe."' AND " .
		"jgm.id_groupe=jgc.id_groupe AND " .
		"m.matiere = jgm.id_matiere" .
		") " .
		"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
	} else {
		//$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
		$sql="SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.mode_moy
		FROM j_groupes_classes jgc, j_groupes_matieres jgm
		WHERE (
		jgc.id_classe='".$id_classe."' AND
		jgm.id_groupe=jgc.id_groupe
		)
		ORDER BY jgc.priorite,jgm.id_matiere";
	}
	//echo "$sql<br />";
	$groupeinfo=mysql_query($sql);
	$lignes_groupes = mysql_num_rows($groupeinfo);
*/

$lignes_groupes=count($tab_moy['current_group']);

// Pour débugger:
$lignes_debug="";
$ele_login_debug="couetm";
$lignes_debug.="<p><b>$ele_login_debug</b><br />";

unset($current_eleve_login);

//echo "\$aff_date_naiss=$aff_date_naiss<br />";
//
// définition des premières colonnes nom, régime, doublant, ...
//
$displayed_categories = array();
$j = 0;
while($j < $nb_lignes_tableau) {
	// colonne nom+prénom
	$current_eleve_login[$j] = old_mysql_result($appel_donnees_eleves, $j, "login");
	$col[1][$j+$ligne_supl] = @old_mysql_result($appel_donnees_eleves, $j, "nom")." ".@old_mysql_result($appel_donnees_eleves, $j, "prenom");
	$ind = 2;

	//echo "<p>\$current_eleve_login[$j]=$current_eleve_login[$j]<br />";
	//echo "\$col[1][$j+$ligne_supl]=".$col[1][$j+$ligne_supl]."<br />";
	//=======================================
	// colonne date de naissance
	if (($aff_date_naiss)&&($aff_date_naiss=='y')) {
		$tmpdate=old_mysql_result($appel_donnees_eleves, $j, "naissance");
		$tmptab=explode("-",$tmpdate);
		if(mb_strlen($tmptab[0])==4){$tmptab[0]=mb_substr($tmptab[0],2,2);}
		$col[$ind][$j+$ligne_supl]=$tmptab[2]."/".$tmptab[1]."/".$tmptab[0];
		$ind++;
	}
	//=======================================

	// colonne régime
	if ((($aff_reg)&&($aff_reg=='y')) or (($aff_doub)&&($aff_doub=='y'))) {
		$regime_doublant_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login[$j]';");
	}
	if (($aff_reg)&&($aff_reg=='y')) {
		$col[$ind][$j+$ligne_supl] = @old_mysql_result($regime_doublant_eleve, 0, "regime");
		$ind++;
	}
	// colonne doublant
	if (($aff_doub)&&($aff_doub=='y')) {
		$col[$ind][$j+$ligne_supl] = @old_mysql_result($regime_doublant_eleve, 0, "doublant");
		$ind++;
	}
	// Colonne absence
	if (($aff_abs)&&($aff_abs=='y')) {
        if (getSettingValue("active_module_absence") != '2' || getSettingValue("abs2_import_manuel_bulletin") == 'y') {
            $abs_eleve = "NR";
            if ($referent == "une_periode")
                $abs_eleve = sql_query1("SELECT nb_absences FROM absences WHERE
			login = '$current_eleve_login[$j]' and
			periode = '" . $num_periode . "'
			");
            else {
                $abs_eleve = sql_query1("SELECT sum(nb_absences) FROM absences WHERE
			login = '$current_eleve_login[$j]'");
            }

            if ($abs_eleve == '-1')
                $abs_eleve = "NR";
            $col[$ind][$j + $ligne_supl] = $abs_eleve;
            $ind++;
        }else {
            $eleve = EleveQuery::create()->findOneByLogin($current_eleve_login[$j]);
            if ($eleve != null) {
                if ($referent == "une_periode") {
                    $abs_eleve = strval($eleve->getDemiJourneesAbsenceParPeriode($num_periode)->count());
                } else {
                    $date_jour = new DateTime('now');
                    $month = $date_jour->format('m');
                    if ($month > 7) {
                        $date_debut = new DateTime($date_jour->format('y') . '-09-01');
                        $date_fin = new DateTime($date_jour->format('y') + 1 . '-08-31');
                    } else {
                        $date_debut = new DateTime($date_jour->format('y') - 1 . '-09-01');
                        $date_fin = new DateTime($date_jour->format('y') . '-08-31');
                    }
                    $abs_eleve = strval($eleve->getDemiJourneesAbsence($date_debut, $date_fin)->count());
                }
            } else {
                $abs_eleve = "NR";
            }
            $col[$ind][$j + $ligne_supl] = $abs_eleve;
            $ind++;
        }
    }

	// Colonne rang
	if (($aff_rang) and ($aff_rang=='y') and ($referent=="une_periode")) {
		$rang = sql_query1("select rang from j_eleves_classes where (
			periode = '".$num_periode."' and
			id_classe = '".$id_classe."' and
			login = '".$current_eleve_login[$j]."' )
			");
		if (($rang == 0) or ($rang == -1)) $rang = "-";
		$col[$ind][$j+$ligne_supl] = $rang;
		//echo "\$col[$ind][$j+$ligne_supl])=".$col[$ind][$j+$ligne_supl]."<br />";
		$ind++;
	}

	$j++;
}

// Etiquettes des premières colonnes
//$ligne1[1] = "Nom ";
$ligne1[1] = "<a href='#' onclick=\"document.getElementById('col_tri').value='1';".
			"document.forms['formulaire_tri'].submit();\"".
			" style='text-decoration:none; color:black;'>".
			"Nom ".
			"</a>";
$ligne1_csv[1] = "Nom ";
//=========================
if (($aff_date_naiss)&&($aff_date_naiss=='y')) {
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Date de naissance")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"date de naissance\" />";
	$ligne1_csv[] = "Date de naissance";
}
//=========================
if (($aff_reg)&&($aff_reg=='y')) {
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Régime")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"régime\" />";
	$ligne1_csv[]="Régime";
}
if(($aff_doub)&&($aff_doub=='y')) {
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=Redoublant&amp;width=22\" width=\"22\" border=\"0\" alt=\"doublant\" />";
	$ligne1_csv[]="Redoublant";
}
if (($aff_abs)&&($aff_abs=='y')) {
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"1/2 journées d'absence\" />".
				"</a>";

	$ligne1_csv[]="1/2 journées d'absence";
}
if (($aff_rang) and ($aff_rang=='y') and ($referent=="une_periode")){
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".
				"</a>";
	//"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode(html_entity_decode("Rang de l&apos;&eacute;l&egrave;ve"))."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".

	//echo count($ligne1);

	$ligne1_csv[]="Rang de l'élève";
}

//echo "\$test_coef=$test_coef<br />";
// Dans calcul_moy_gen.inc.php, $test_coef est le résultat d'une requête mysql_query()
//$test_coef=$test_coef_avant_calcul_moy_gen;

if($nb_coef_non_nuls!=0) {$col[1][0] = "Coefficient";}

// Etiquettes des trois dernières lignes
$col[1][$nb_lignes_tableau+$ligne_supl] = "Moyenne";
$col[1][$nb_lignes_tableau+1+$ligne_supl] = "Min";
$col[1][$nb_lignes_tableau+2+$ligne_supl] = "Max";
$ind = 2;
$nb_col = 1;
$k= 1;

//=========================
if (($aff_date_naiss)&&($aff_date_naiss=='y')) {
	if ($nb_coef_non_nuls != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
//=========================

if (($aff_reg)&&($aff_reg=='y')) {
	if ($nb_coef_non_nuls != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if (($aff_doub)&&($aff_doub=='y')) {
	if ($nb_coef_non_nuls != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if (($aff_abs)&&($aff_abs=='y')) {
	if ($nb_coef_non_nuls != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}
if (($aff_rang) and ($aff_rang=='y') and ($referent=="une_periode")) {
	if ($nb_coef_non_nuls != 0) $col[$ind][0] = "-";
	$col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
	$col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
	$nb_col++;
	$k++;
	$ind++;
}

//=============================
// Utilisé pour referent=annee
// On initialise les totaux coef et notes pour les lignes élèves ($j)
$j = '0';
while($j < $nb_lignes_tableau) {
	//$total_coef[$j+$ligne_supl] = 0;
	$total_coef_classe[$j+$ligne_supl] = 0;
	$total_coef_eleve[$j+$ligne_supl] = 0;
	
	//$total_points[$j+$ligne_supl] = 0;
	//$total_points_classe[$j+$ligne_supl] = 0;
	$total_points_eleve[$j+$ligne_supl] = 0;
	
	//$total_cat_coef[$j+$ligne_supl] = array();
	//$total_cat_coef_classe[$j+$ligne_supl] = array();
	$total_cat_coef_eleve[$j+$ligne_supl] = array();
	
	//$total_cat_points[$j+$ligne_supl] = array();
	//$total_cat_points_classe[$j+$ligne_supl] = array();
	$total_cat_points_eleve[$j+$ligne_supl] = array();
	// =================================
	// MODIF: boireaus
	if ($affiche_categories) {
		foreach ($categories as $cat_id) {
			//$total_cat_coef[$j+$ligne_supl][$cat_id] = 0;
			//$total_cat_coef_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_coef_eleve[$j+$ligne_supl][$cat_id] = 0;
	
			//$total_cat_points[$j+$ligne_supl][$cat_id] = 0;
			//$total_cat_points_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_points_eleve[$j+$ligne_supl][$cat_id] = 0;
		}
	}
	// =================================
	$j++;
}
//=============================


//=============================
// AJOUT: boireaus
$chaine_matieres=array();
$chaine_moy_eleve1=array();
$chaine_moy_classe=array();
//$chaine_moy_classe="";
//=============================


//if((($utiliser_coef_perso=='y')&&(isset($note_sup_10)))||($temoin_note_sup10=='y')) {
//if($temoin_note_sup10=='y') {
if(($temoin_note_sup10=='y')||($temoin_note_bonus=='y')) {
	//$col[1][1]="Note&gt;10";
	//$col[1][1]="Note sup 10";
	$col[1][1]="Mode moy";
	//$col_csv[1][1]="Note sup 10";
	$col_sup=0;
	if(isset($avec_moy_gen_periodes_precedentes)){
		$col_sup=$periode_num;
	}
	for($t=2;$t<=$nb_col+$lignes_groupes+$col_sup;$t++) {$col[$t][1]='-';}

	if ($affiche_categories) {
		foreach ($categories as $cat_id) {
			$col[$t][1]='-';
			$t++;
		}
	}
	// Pour la colonne moyenne générale
	if ($ligne_supl >= 1) {
		$col[$t][1]='-';
	}
}

//
// définition des colonnes matières
//
$i= '0';

$num_debut_colonnes_matieres=$nb_col+1;
$num_debut_lignes_eleves=$ligne_supl;
//echo "\$num_debut_colonnes_matieres=$num_debut_colonnes_matieres<br />";
//echo "\$num_debut_lignes_eleves=$num_debut_lignes_eleves<br />";

//pour calculer la moyenne annee de chaque matiere
$moyenne_annee_matiere=array();
$prev_cat_id = null;
while($i < $lignes_groupes) {
	//=============================
	// Utilisé pour referent=annee
	$moy_max = -1;
	$moy_min = 21;
	//=============================

	$nb_col++;
	$k++;

	foreach ($moyenne_annee_matiere as $tableau => $value) { unset($moyenne_annee_matiere[$tableau]);}

	//$var_group_id = old_mysql_result($groupeinfo, $i, "id_groupe");
	//$current_group = get_group($var_group_id);

	// On choisit une période pour la récup des infos générales sur le groupe (id, coef,... bref des trucs qui ne dépendent pas de la période)
	if($referent=='une_periode') {$p=$num_periode;}
	else {$p=1;}

	$var_group_id=$tab_moy['current_group'][$i]['id'];
	$current_group=$tab_moy['current_group'][$i];

	// Coeff pour la classe
	//$current_coef = old_mysql_result($groupeinfo, $i, "coef");
	$current_coef=$tab_moy['periodes'][$p]['current_coef'][$i];

	// Mode de calcul sur la moyenne: standard (-) ou note supérieure à 10
	//$current_mode_moy = old_mysql_result($groupeinfo, $i, "mode_moy");
	$current_mode_moy=$current_group["classes"]["classes"][$id_classe]["mode_moy"];

	// A FAIRE: A l'affichage, il faudrait mettre 1.0(*) quand le coeff n'est pas 1.0 pour tous les élèves à cause de coeffs personnalisés.
	if($utiliser_coef_perso=='y') {
		if(isset($coef_perso[$var_group_id])) {
			$current_coef=$coef_perso[$var_group_id];
			//$_SESSION['coef_perso_'.$current_group['matiere']['matiere']]=$coef_perso[$var_group_id];
			$_SESSION['coef_perso_'.$current_group['id']]=$coef_perso[$var_group_id];
		}

		// Les mode_moy_perso imposés depuis index2.php:
		//if(isset($note_sup_10[$var_group_id])) {
		if((isset($mode_moy_perso[$var_group_id]))&&($mode_moy_perso[$var_group_id]=='sup10')) {
			//$col[$nb_col][1]='X';
			//$_SESSION['note_sup_10_'.$current_group['matiere']['matiere']]='y';
			$col[$nb_col][1]='sup10';
			//$_SESSION['mode_moy_'.$current_group['matiere']['matiere']]='sup10';
			$_SESSION['mode_moy_'.$current_group['id']]='sup10';
			$current_mode_moy='sup10';
		}
		elseif((isset($mode_moy_perso[$var_group_id]))&&($mode_moy_perso[$var_group_id]=='bonus')) {
			$col[$nb_col][1]='bonus';
			//$_SESSION['mode_moy_'.$current_group['matiere']['matiere']]='bonus';
			$_SESSION['mode_moy_'.$current_group['id']]='bonus';
			$current_mode_moy='bonus';
		}
		else {
			// On remet en standard
			//unset($_SESSION['mode_moy_'.$current_group['matiere']['matiere']]);
			//$_SESSION['mode_moy_'.$current_group['matiere']['matiere']]='-';
			$_SESSION['mode_moy_'.$current_group['id']]='-';
			$current_mode_moy='-';
		}

	}
	else {
		//if($current_mode_moy=='sup10') {$col[$nb_col][1]='X';}
		if($current_mode_moy=='sup10') {$col[$nb_col][1]='sup10';}
		if($current_mode_moy=='bonus') {$col[$nb_col][1]='bonus';}
	}


	if ($affiche_categories) {
	// On regarde si on change de catégorie de matière
		if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
			$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
		}
	}


	// Boucle sur la liste des élèves retournés par la requête
	$j = '0';
	while($j < $nb_lignes_tableau) {

		if($current_eleve_login[$j]==$ele_login_debug) {
			$lignes_debug.="<p>\$current_group['name']=".$current_group['name']."<br />";
			$lignes_debug.="\$current_coef=".$current_coef."<br />";
			$lignes_debug.="\$current_mode_moy=".$current_mode_moy."<br />";
		}

		// Valeur des lignes du bas avec moyenne classe/min/max pour le groupe $i... pour pouvoir mettre dans les liens draw_graphe.php
		if ($referent == "une_periode") {
			$moy_classe_tmp=$tab_moy['periodes'][$p]['current_classe_matiere_moyenne'][$i];
			$moy_min_classe_grp=$tab_moy['periodes'][$p]['moy_min_classe_grp'][$i];
			$moy_max_classe_grp=$tab_moy['periodes'][$p]['moy_max_classe_grp'][$i];
		}
		else {
			$call_moyenne = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
			$moy_classe_tmp = @old_mysql_result($call_moyenne, 0, "moyenne");
		}


		/*
		// Coefficient personnalisé pour l'élève?
		$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
				"login = '".$current_eleve_login[$j]."' AND " .
				"id_groupe = '".$current_group["id"]."' AND " .
				"name = 'coef')";
		$test_coef_personnalise = mysql_query($sql);
		if (mysql_num_rows($test_coef_personnalise) > 0) {
			$coef_eleve = old_mysql_result($test_coef_personnalise, 0);
		} else {
			// Coefficient du groupe:
			$coef_eleve = $current_coef;
		}
		//$coef_eleve=number_format($coef_eleve,1, ',', ' ');
		*/
		/*
		// On recherche l'indice de l'élève dans tab_moy pour la période $p... qui vaut $num_periode pour $referent==une_periode et 1 sinon
		// Mais pour le coef, il doit être le même pour toutes les périodes
		// Par contre pour l'indice de l'élève, cela peut changer
		// !!!!!!!!!!!!
		// A REVOIR !!!
		// !!!!!!!!!!!!
		$indice_j_ele=$tab_moy['periodes'][$p]['tab_login_indice'][$current_eleve_login[$j]];
		$coef_eleve=$tab_moy['periodes'][$p]['current_coef_eleve'][$indice_j_ele][$i];
		*/

		if ($referent == "une_periode") {
	
			if (!in_array($current_eleve_login[$j], $current_group["eleves"][$num_periode]["list"])) {
				// L'élève ne suit pas cet enseignement
				$col[$k][$j+$ligne_supl] = "/";
			}
			else {
				// L'élève suit cet enseignement

				// On récupère l'indice de l'élève dans $tab_moy pour la période $num_periode
				//$indice_j_ele=$tab_moy['periodes'][$num_periode]['tab_login_indice'][$current_eleve_login[$j]];
				$indice_j_ele=$tab_moy['periodes'][$num_periode]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];
				$coef_eleve=$tab_moy['periodes'][$num_periode]['current_coef_eleve'][$indice_j_ele][$i];

				//echo "\$current_eleve_login[$j]=$current_eleve_login[$j]<br />";
				//echo "\$indice_j_ele=$indice_j_ele<br />";
				/*
				$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
				$current_eleve_statut = @old_mysql_result($current_eleve_note_query, 0, "statut");
				*/

				$current_eleve_statut=$tab_moy['periodes'][$num_periode]['current_eleve_statut'][$i][$indice_j_ele];
				$current_eleve_note=$tab_moy['periodes'][$num_periode]['current_eleve_note'][$i][$indice_j_ele];

				//echo "\$current_eleve_note=$current_eleve_note<br />";

				if ($current_eleve_statut != "") {
					$col[$k][$j+$ligne_supl] = $current_eleve_statut;
				}
				elseif($current_eleve_note=='-') {
					$col[$k][$j+$ligne_supl] = '-';
				}
				else {
					$temp=$current_eleve_note;
					//echo "\$current_eleve_note=$current_eleve_note<br />";
					if($temp != '')  {
						$col[$k][$j+$ligne_supl] = number_format($temp,1, ',', ' ');
						if ($current_coef > 0) {
							// ===================================
							// MODIF: boireaus
							//if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
							if ($affiche_categories) {
								if (!in_array($prev_cat_id, $displayed_categories)) {$displayed_categories[] = $prev_cat_id;}
							}
							// ===================================
	
							/*
							// Coefficient personnalisé pour l'élève?
							$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
									"login = '".$current_eleve_login[$j]."' AND " .
									"id_groupe = '".$current_group["id"]."' AND " .
									"name = 'coef')";
							$test_coef_personnalise = mysql_query($sql);
							if (mysql_num_rows($test_coef_personnalise) > 0) {
								$coef_eleve = old_mysql_result($test_coef_personnalise, 0);
							} else {
								// Coefficient du groupe:
								$coef_eleve = $current_coef;
								if($utiliser_coef_perso=='y') {
									if ((isset($note_sup_10[$current_group["id"]]))&&($note_sup_10[$current_group["id"]]=='y')&&($temp<10)) {
										$coef_eleve=0;
										//echo $current_eleve_login[$j]." groupe n°".$current_group["id"]." (".$current_group["name"]."): coeff 0<br />";
									}
								}
								else {
									if(($current_mode_moy=='sup10')&&($temp<10)) {$coef_eleve=0;}
								}
							}
							//$coef_eleve=number_format($coef_eleve,1, ',', ' ');
	
							//$total_coef[$j+$ligne_supl] += $current_coef;
							$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
							$total_coef_classe[$j+$ligne_supl] += $current_coef;
							//$total_points[$j+$ligne_supl] += $current_coef*$temp;
							//$total_points[$j+$ligne_supl] += $coef_eleve*$temp;
							$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$temp;
							$total_points_classe[$j+$ligne_supl] += $current_coef*$temp;
	
							if ($affiche_categories) {
								//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
								$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
								$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
	
								//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
								$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$temp;
								$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
							}
							*/
						}
					} else {
						$col[$k][$j+$ligne_supl] = '-';
					}


					$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."' AND periode='$num_periode'";
					$test_eleve_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_eleve_grp)>0){
						if(!isset($chaine_matieres[$j+$ligne_supl])){
						//if($chaine_matieres[$j+$ligne_supl]==""){
							$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl]=$col[$k][$j+$ligne_supl];
							$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
						}
						else{
							$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
							//$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
							$chaine_moy_eleve1[$j+$ligne_supl].="|".$col[$k][$j+$ligne_supl];
							$chaine_moy_classe[$j+$ligne_supl].="|".$moy_classe_tmp;
						}
					}


				}
				//echo "\$col[$k][$j+$ligne_supl]=".$col[$k][$j+$ligne_supl]."<br />";
			}

		}
		else {
			// ANNEE ENTIERE... on fait les calculs
			$p = "1";
			$moy = 0;
			$non_suivi = 2;
			$coef_moy = 0;
			while ($p < $nb_periode) {

				// On récupère l'indice de l'élève dans $tab_moy pour la période $num_periode
				//$indice_j_ele=$tab_moy['periodes'][$p]['tab_login_indice'][$current_eleve_login[$j]];
				//$coef_eleve=$tab_moy['periodes'][$p]['current_coef_eleve'][$indice_j_ele][$i];


				if (!in_array($current_eleve_login[$j], $current_group["eleves"][$p]["list"])) {
					$non_suivi = $non_suivi*2;

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="Période $p: Non suivi<br />";
					}
				}
				else {
					// On récupère l'indice de l'élève dans $tab_moy pour la période $num_periode
					//$indice_j_ele=$tab_moy['periodes'][$p]['tab_login_indice'][$current_eleve_login[$j]];
					$indice_j_ele=$tab_moy['periodes'][$p]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];

					//$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$p')");
					//$current_eleve_statut = @old_mysql_result($current_eleve_note_query, 0, "statut");

					$current_eleve_statut=$tab_moy['periodes'][$p]['current_eleve_statut'][$i][$indice_j_ele];
					$current_eleve_note=$tab_moy['periodes'][$p]['current_eleve_note'][$i][$indice_j_ele];

					//if ($current_eleve_statut == "") {
					if(($current_eleve_statut=="")&&($current_eleve_note!="")&&($current_eleve_note!="-")) {
						//$temp = @old_mysql_result($current_eleve_note_query, 0, "note");
						$temp=$current_eleve_note;
						if  ($temp != '')  {
							$moy += $temp;
							$coef_moy++;
						}
					}

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="\$current_eleve_statut=$current_eleve_statut<br />";
						$lignes_debug.="\$current_eleve_note=$current_eleve_note<br />";
						$lignes_debug.="Total pour la matière: $moy (pour $coef_moy note(s))<br />";
					}

					/*
					if($current_eleve_login[$j]=='BABOUIN_D') {
						echo "<p>\$tab_moy['periodes'][$p]['current_eleve_statut'][$i][$indice_j_ele]=".$current_eleve_statut."<br />";
						echo "\$tab_moy['periodes'][$p]['current_eleve_note'][$i][$indice_j_ele]=".$current_eleve_note."<br />";
						echo "\$moy=$moy et \$coef_moy=$coef_moy<br />";
					}
					*/
				}
				$p++;
			}

			$moy_eleve_grp_courant_annee="-";
			if ($non_suivi == (pow(2,$nb_periode))) {
				// L'élève n'a suivi la matière sur aucune période
				$col[$k][$j+$ligne_supl] = "/";

				if($current_eleve_login[$j]==$ele_login_debug) {
					$lignes_debug.="Enseignement non suivi de l'année.<br />";
				}
			}
			else if ($coef_moy != 0) {
				// L'élève a au moins une note sur au moins une période
				$moy = $moy/$coef_moy;

				if($current_eleve_login[$j]==$ele_login_debug) {
					$lignes_debug.="Moyenne annuelle: $moy<br />";
				}

				$moy_min = min($moy_min,$moy);
				$moy_max = max($moy_max,$moy);
				$col[$k][$j+$ligne_supl] = number_format($moy,1, ',', ' ');
				if ($current_coef > 0) {
					//$temoin_current_note_bonus="n";

					$coef_eleve = $current_coef;
					$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
							"login = '".$current_eleve_login[$j]."' AND " .
							"id_groupe = '".$current_group["id"]."' AND " .
							"name = 'coef')";
					$test_coef_personnalise = mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($test_coef_personnalise) > 0) {
						$coef_eleve = old_mysql_result($test_coef_personnalise, 0);
					}

					//==============================
					// Pour prendre en compte les coef pour les catégories:
					$coef_eleve_reserve=$coef_eleve;
					// On met en réserve le coef pour ne pas tenir compte des mode_moy au niveau des catégories
					//==============================

					// A FAIRE: PRENDRE EN COMPTE AUSSI mode_moy=bonus et mode_moy=ameliore
					if($utiliser_coef_perso=='y') {
						//if((isset($note_sup_10[$current_group["id"]]))&&($note_sup_10[$current_group["id"]]=='y')&&($moy<10)) {
						if(($current_mode_moy=='sup10')&&($moy<10)) {
							$coef_eleve=0;
							//echo $current_eleve_login[$j]." groupe n°".$current_group["id"]." (".$current_group["name"]."): coeff 0<br />";
						}
						/*
						elseif($current_mode_moy=='bonus')) {
							$temoin_current_note_bonus="y";
						}
						*/
					}
					else {
						if(($current_mode_moy=='sup10')&&($moy<10)) {$coef_eleve=0;}
						//elseif($current_mode_moy=='bonus') {$temoin_current_note_bonus="y";}
					}

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="\$current_coef=$current_coef<br />";
						$lignes_debug.="\$coef_eleve_reserve=$coef_eleve_reserve<br />";
						$lignes_debug.="\$coef_eleve=$coef_eleve<br />";
					}
	
					if (!in_array($prev_cat_id, $displayed_categories)) {$displayed_categories[] = $prev_cat_id;}
					//$total_coef[$j+$ligne_supl] += $current_coef;
					$total_coef_classe[$j+$ligne_supl] += $current_coef;

					// On ne compte pas le coef dans le total pour une note à bonus
					//if($temoin_current_note_bonus!="y") {
					if($current_mode_moy!="bonus") {
						$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
					}

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="<b>Total des coef:</b> ".$total_coef_eleve[$j+$ligne_supl]."<br />";
					}

					//$total_points[$j+$ligne_supl] += $current_coef*$moy;
					// On fait le même calcul pour la classe que pour l'élève, mais sans les particularités de coefficients personnalisés pour un élève...
					// ... mais du coup, on ne gère pas non plus les mode_moy: A REVOIR
					//$total_points_classe[$j+$ligne_supl] += $current_coef*$moy;

					//if($temoin_current_note_bonus!="y") {
					if($current_mode_moy!="bonus") {
						// Cas standard et sup10
						$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$moy;
						// Dans le cas d'une note_sup_10 si $moy<10, $coef=0 si bien que ça n'augmente pas le total

						if($current_eleve_login[$j]==$ele_login_debug) {
							$lignes_debug.="On augmente le total des points de $coef_eleve*$moy<br />";
						}

					}
					elseif($moy>10) { // Cas d'une note à bonus:
						$total_points_eleve[$j+$ligne_supl] += $coef_eleve*($moy-10);

						if($current_eleve_login[$j]==$ele_login_debug) {
							$lignes_debug.="On augmente le total des points de $coef_eleve*($moy-10)<br />";
						}
					}

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="<b>Total des points:</b> ".$total_points_eleve[$j+$ligne_supl]."<br />";
					}

					if ($affiche_categories) {
						//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;

						// Pour les catégories, on ne tient pas compte des mode_moy: les coef comptent normalement
						// On utilise donc le coef_eleve_reserve mis en réserve avant l'éventuelle mise à zéro dans le cas sup10 avec une note inférieure à 10
						//$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
						$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve_reserve;
						//$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						//$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$moy;
						$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve_reserve*$moy;
						//$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
						// Avec le $total_cat_points_classe, la différence porte sur les coef personnalisés (eleves_groupes_settings) et coef_perso
						// Faut-il tenir compte de ça ou se contenter pour la moyenne de classe des moyennes des moy_ele_cat?

						if($current_eleve_login[$j]==$ele_login_debug) {
							$lignes_debug.="<p>On augmente le total des coef de la catégorie $prev_cat_id de $coef_eleve_reserve<br />";
							$lignes_debug.="\$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id]=".$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id]."<br />";

							$lignes_debug.="On augmente le total des points de la catégorie $prev_cat_id de $coef_eleve_reserve*$moy<br />";
							$lignes_debug.="\$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id]=".$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id]."<br />";
						}
					}
				}
			}
			else {
				// Bien que suivant la matière, l'élève n'a aucune note à toutes les période (absent, pas de note, disp ...)
				$col[$k][$j+$ligne_supl] = "-";
			}


			$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."'";
			$test_eleve_grp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_eleve_grp)>0) {
				//if($chaine_matieres[$j+$ligne_supl]==""){
				if(!isset($chaine_matieres[$j+$ligne_supl])){
					$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
					//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
					$chaine_moy_eleve1[$j+$ligne_supl]=$moy_eleve_grp_courant_annee;
					$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
				}
				else{
					if($chaine_matieres[$j+$ligne_supl]==""){
						$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
						//$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
						$chaine_moy_eleve1[$j+$ligne_supl]=$moy_eleve_grp_courant_annee;
						$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
					}
					else{
						$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
						//$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
						$chaine_moy_eleve1[$j+$ligne_supl].="|".$moy_eleve_grp_courant_annee;
						$chaine_moy_classe[$j+$ligne_supl].="|".$moy_classe_tmp;
					}
				}
			}

		}
		$j++;
		//echo "<br />";
	}


	// Lignes du bas avec moyenne classe/min/max pour le groupe $i
	if ($referent == "une_periode") {
		//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
		//$call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
		//$call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");

		//$temp = @old_mysql_result($call_moyenne, 0, "moyenne");

		$temp=$tab_moy['periodes'][$p]['current_classe_matiere_moyenne'][$i];
		$moy_min_classe_grp=$tab_moy['periodes'][$p]['moy_min_classe_grp'][$i];
		$moy_max_classe_grp=$tab_moy['periodes'][$p]['moy_max_classe_grp'][$i];

	}
	else {
		$call_moyenne = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
		$temp = @old_mysql_result($call_moyenne, 0, "moyenne");
	}

	//$moy_classe_tmp=$temp;

	//========================================
	//================================
	$col_csv=array();
	//echo "DEBUG : Initialisation de \$col_csv<br />";
	if($temoin_graphe=="oui"){
		if($i==$lignes_groupes-1){
			for($loop=0;$loop<$nb_lignes_tableau;$loop++){

				if(isset($chaine_moy_eleve1[$loop+$ligne_supl])) {

					$col_csv[1][$loop+$ligne_supl]=$col[1][$loop+$ligne_supl];

					$tmp_col=$col[1][$loop+$ligne_supl];
					//echo "\$current_eleve_login[$loop]=$current_eleve_login[$loop]<br />";
					$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe.php?".
					"temp1=".preg_replace('/,/','.',$chaine_moy_eleve1[$loop+$ligne_supl]).
					"&amp;temp2=".preg_replace('/,/','.',$chaine_moy_classe[$loop+$ligne_supl]).
					"&amp;etiquette=".$chaine_matieres[$loop+$ligne_supl].
					"&amp;titre=$graph_title".
					"&amp;v_legend1=".$current_eleve_login[$loop].
					"&amp;v_legend2=moyclasse".
					"&amp;compteur=$compteur".
					"&amp;nb_series=$nb_series".
					"&amp;id_classe=$id_classe".
					"&amp;mgen1=".
					"&amp;mgen2=";
					//"&amp;periode=$periode".
					$col[1][$loop+$ligne_supl].="&amp;tronquer_nom_court=$tronquer_nom_court";
					if($referent == "une_periode"){
						$col[1][$loop+$ligne_supl].="&amp;periode=".rawurlencode("Période ".$num_periode);
					}
					else{
						$col[1][$loop+$ligne_supl].="&amp;periode=".rawurlencode("Année");
					}
					$col[1][$loop+$ligne_supl].="&amp;largeur_graphe=$largeur_graphe".
					"&amp;hauteur_graphe=$hauteur_graphe".
					"&amp;taille_police=$taille_police".
					"&amp;epaisseur_traits=$epaisseur_traits".
					"&amp;temoin_image_escalier=$temoin_image_escalier".
					"' target='_blank'>".$tmp_col.
					"</a>";

				}
			}
			//echo "\$chaine_moy_classe=".$chaine_moy_classe."<br /><br />\n";
		}
	}
	// ===============================
	//========================================




	if ($nb_coef_non_nuls != 0) {
		if ($current_coef > 0) {
			// A FAIRE: A l'affichage, il faudrait mettre 1.0(*) quand le coeff n'est pas 1.0 pour tous les élèves à cause de coeffs personnalisés.
			$col[$k][0] = number_format($current_coef,1, ',', ' ');
		} else {
			$col[$k][0] = "-";
		}
	}

	if ($temp != '') {
		//$col[$k][$nb_lignes_tableau+$ligne_supl] = $temp;
		$col[$k][$nb_lignes_tableau+$ligne_supl] = number_format($temp,1, ',', ' ');
	} else {
		$col[$k][$nb_lignes_tableau+$ligne_supl] = '-';
	}

	if ($referent == "une_periode") {
		//$temp = @old_mysql_result($call_min, 0, "note_min");
		$temp = $moy_min_classe_grp;
		if ($temp != '') {
			//$col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($temp,1, ',', ' ');
		} else {
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
		}
		//$temp = @old_mysql_result($call_max, 0, "note_max");
		$temp = $moy_max_classe_grp;
		if ($temp != '') {
			//$col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
			$col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($temp,1, ',', ' ');
		} else {
			$col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
		}
	}
	else {
		// Moyenne annuelle
		if ($moy_min <=20) {
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_min,1, ',', ' ');
		}
		else {
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
		}

		if ($moy_max >= 0) {
			$col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_max,1, ',', ' ');
		}
		else {
			$col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
		}
	}

	$nom_complet_matiere = $current_group["description"];
	$nom_complet_coupe = (mb_strlen($nom_complet_matiere) > 20)? urlencode(mb_substr($nom_complet_matiere,0,20)."...") : urlencode($nom_complet_matiere);

	$nom_complet_coupe_csv=(mb_strlen($nom_complet_matiere) > 20) ? mb_substr($nom_complet_matiere,0,20) : $nom_complet_matiere;
	$nom_complet_coupe_csv=preg_replace("/;/","",$nom_complet_coupe_csv);

	//$ligne1[$k] = "<img src=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" width=\"22\" border=\"0\" />";
	//$ligne1[$k] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"$nom_complet_coupe\" />";

	$ligne1[$k]="<a href='#' onclick=\"document.getElementById('col_tri').value='$k';";
	$ligne1[$k].="document.forms['formulaire_tri'].submit();\">";
	$ligne1[$k] .= "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"$nom_complet_coupe\" />";
	$ligne1[$k].="</a>";

	$ligne1_csv[$k] = "$nom_complet_coupe_csv";
	$i++;
}
// Fin de la boucle sur la liste des groupes/enseignements

/*
echo "<p style='color:red'>";
for($loop=0;$loop<$nb_lignes_tableau;$loop++) {

	//echo "\$col[1][$loop+$ligne_supl]=".$col[1][$j+$ligne_supl]."<br />";
	echo "\$col[1][$loop+$ligne_supl]=".$col[1][$loop+$ligne_supl]."<br />";

}
echo "</p>";
*/

//==================================================================================================================
//==================================================================================================================
//==================================================================================================================

// Dernières colonnes des moyennes générales

// Moyennes de catégories et de classe

//if ($ligne_supl == 1) {
if ($ligne_supl >= 1) {

	unset($num_p1);
	unset($num_p2);
	if($referent=='une_periode') {
		if(!isset($avec_moy_gen_periodes_precedentes)) {
			$num_p1=$num_periode;
			$num_p2=$num_p1+1;
		}
		else {
			$num_p1=1;
			$num_p2=$num_periode+1;
		}
	}
	else {
		if(isset($avec_moy_gen_periodes_precedentes)) {
			$num_p1=1;
			$num_p2=$nb_periode;
		}

		// Pour calculer la moyenne annuelle même si on n'affiche pas les moyennes des différentes périodes
		$num_p1bis=1;
		$num_p2bis=$nb_periode;
	}


	// Les moyennes pour chaque catégorie
	if ($affiche_categories) {
		foreach($displayed_categories as $cat_id) {
			$nb_col++;
			//$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" width=\"22\" border=\"0\" alt=\"".$cat_names[$cat_id]."\" />";

			$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" width=\"22\" border=\"0\" alt=\"".$cat_names[$cat_id]."\" />".
				"</a>";

			$ligne1_csv[$nb_col] = "Moyenne : " . $cat_names[$cat_id];

			//if(isset($note_sup_10)) {$col[$nb_col][1]='-';}
			if($temoin_note_sup10=='y') {$col[$nb_col][1]='-';}

			if($referent=='une_periode') {
				$j = '0';
				while($j < $nb_lignes_tableau) {

					//$indice_j_ele=$tab_moy['periodes'][$num_periode]['tab_login_indice'][$current_eleve_login[$j]];
					$indice_j_ele=$tab_moy['periodes'][$num_periode]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];
					$tmp_moy_cat_ele=$tab_moy['periodes'][$num_periode]['moy_cat_eleve'][$indice_j_ele][$cat_id];

					//echo "$current_eleve_login[$j]: \$tab_moy['periodes'][$num_periode]['moy_cat_eleve'][$indice_j_ele][$cat_id]=".$tmp_moy_cat_ele."<br />";

					if(($tmp_moy_cat_ele!='')&&($tmp_moy_cat_ele!='-')) {
						//$col[$nb_col][$j+$ligne_supl]=number_format($tmp_moy_cat_ele,1, ',', ' ');
						$col[$nb_col][$j+$ligne_supl]=nf($tmp_moy_cat_ele,1);
					} else {
						$col[$nb_col][$j+$ligne_supl] = '/';
					}
					$j++;
				}

				$col[$nb_col][0] = "-";

				// On récupère les valeurs avec le $indice_j_ele du dernier élève, mais les moyennes de catégories pour la classe doivent être les mêmes quel que soit l'élève
				$tmp_moy_cat_classe=$tab_moy['periodes'][$num_periode]['moy_cat_classe'][$indice_j_ele][$cat_id];

				//echo "$current_eleve_login[$j-1]: \$tab_moy['periodes'][$num_periode]['moy_cat_classe'][$indice_j_ele][$cat_id]=".$tmp_moy_cat_classe."<br />";

				if(($tmp_moy_cat_classe!='')&&($tmp_moy_cat_classe!='-')) {
					//$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($tmp_moy_cat_classe,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = nf($tmp_moy_cat_classe,1);
				}
				else {
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
				}

				$tmp_moy_cat_min=$tab_moy['periodes'][$num_periode]['moy_cat_min'][$indice_j_ele][$cat_id];
				if(($tmp_moy_cat_min!='')&&($tmp_moy_cat_min!='-')) {
					//$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($tmp_moy_cat_min,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = nf($tmp_moy_cat_min,1);
				}
				else {
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
				}

				$tmp_moy_cat_max=$tab_moy['periodes'][$num_periode]['moy_cat_max'][$indice_j_ele][$cat_id];
				if(($tmp_moy_cat_max!='')&&($tmp_moy_cat_max!='-')) {
					//$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($tmp_moy_cat_max,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = nf($tmp_moy_cat_max,1);
				}
				else {
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
				}
			}
			else {
				// Mode Année entière
				// Moyennes de catégories en mode année entière: 20140527: Il faudrait un autre mode de calcul
				if($mode_calcul_moy_annee=="moyenne_des_moy_gen_periodes") {

					$moy_annee_somme_moy_ele_categorie_courante=array();
					$moy_annee_nb_moy_ele_categorie_courante=array();
					$moy_annee_moy_ele_categorie_courante=array();
					$j=0;
					while($j < $nb_lignes_tableau) {
						$moy_annee_somme_moy_ele_categorie_courante[$j]=0;
						$moy_annee_nb_moy_ele_categorie_courante[$j]=0;
						$j++;
					}

					for($loop=$num_p1bis;$loop<$num_p2bis;$loop++) {
						$j=0;
						//echo "\$loop=$loop<br />";
						while($j < $nb_lignes_tableau) {
							if(isset($tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])])) {
								//echo $current_eleve_login[$j]."<br />";
								$indice_j_ele=$tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];
								$tmp_moy_cat_ele=$tab_moy['periodes'][$loop]['moy_cat_eleve'][$indice_j_ele][$cat_id];
								if(($tmp_moy_cat_ele!='')&&($tmp_moy_cat_ele!='-')) {
									$moy_annee_somme_moy_ele_categorie_courante[$j]+=$tmp_moy_cat_ele;
									$moy_annee_nb_moy_ele_categorie_courante[$j]++;
								}
							}
							$j++;
						}
					}

					$moy_annee_somme_toutes_moy_ele_categorie_courante=0;
					$moy_annee_nb_toutes_moy_ele_categorie_courante=0;
					$moy_annee_moy_max_moy_ele_categorie_courante=-1;
					$moy_annee_moy_min_moy_ele_categorie_courante=100;
					$j=0;
					while($j < $nb_lignes_tableau) {
						if($moy_annee_nb_moy_ele_categorie_courante[$j]==0) {
							$moy_annee_moy_ele_categorie_courante[$j]="/";
						}
						else {
							$moy_tmp=$moy_annee_somme_moy_ele_categorie_courante[$j]/$moy_annee_nb_moy_ele_categorie_courante[$j];
							$moy_annee_moy_ele_categorie_courante[$j]=number_format($moy_tmp,1, ',', ' ');;

							$moy_annee_somme_toutes_moy_ele_categorie_courante+=$moy_tmp;
							$moy_annee_nb_toutes_moy_ele_categorie_courante++;

							if($moy_tmp>$moy_annee_moy_max_moy_ele_categorie_courante) {
								$moy_annee_moy_max_moy_ele_categorie_courante=$moy_tmp;
							}
							if($moy_tmp<$moy_annee_moy_min_moy_ele_categorie_courante) {
								$moy_annee_moy_min_moy_ele_categorie_courante=$moy_tmp;
							}
						}
						$j++;
					}
					$moy_annee_moy_classe_categorie_courante="-";
					if($moy_annee_nb_toutes_moy_ele_categorie_courante>0) {
						$moy_annee_moy_classe_categorie_courante=number_format($moy_annee_somme_toutes_moy_ele_categorie_courante/$moy_annee_nb_toutes_moy_ele_categorie_courante,1, ',', ' ');;
					}

					if($moy_annee_moy_max_moy_ele_categorie_courante==-1) {
						$moy_annee_moy_max_moy_ele_categorie_courante="-";
					}
					else {
						$moy_annee_moy_max_moy_ele_categorie_courante=number_format($moy_annee_moy_max_moy_ele_categorie_courante,1, ',', ' ');;
					}

					if($moy_annee_moy_min_moy_ele_categorie_courante==100) {
						$moy_annee_moy_min_moy_ele_categorie_courante="-";
					}
					else {
						$moy_annee_moy_min_moy_ele_categorie_courante=number_format($moy_annee_moy_min_moy_ele_categorie_courante,1, ',', ' ');;
					}






					$j = '0';
					while($j < $nb_lignes_tableau) {
						$col[$nb_col][$j+$ligne_supl]=$moy_annee_moy_ele_categorie_courante[$j];
						$j++;
					}

					$col[$nb_col][0] = "-";

					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = $moy_annee_moy_classe_categorie_courante;
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = $moy_annee_moy_min_moy_ele_categorie_courante;
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = $moy_annee_moy_max_moy_ele_categorie_courante;



				}
				else {
					$j = '0';
					while($j < $nb_lignes_tableau) {
						if ($total_cat_coef_eleve[$j+$ligne_supl][$cat_id] > 0) {
							$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id],1, ',', ' ');

							if($current_eleve_login[$j]==$ele_login_debug) {
								$lignes_debug.="Moyenne de la catégorie $cat_id=".$total_cat_points_eleve[$j+$ligne_supl][$cat_id]."/".$total_cat_coef_eleve[$j+$ligne_supl][$cat_id]."=".$col[$nb_col][$j+$ligne_supl]."<br />";
							}

							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							// A REVOIR... calcul des moyennes min/max/classe de catégories,...
							//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
							//$moy_cat_classe_point[$cat_id] +=$total_cat_points_classe[$j+$ligne_supl][$cat_id]/$total_cat_coef_classe[$j+$ligne_supl][$cat_id];
							$moy_cat_classe_point[$cat_id] +=$total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id];

							$moy_cat_classe_effectif[$cat_id]++;

							$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id]);

							$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id]);
						} else {
							$col[$nb_col][$j+$ligne_supl] = '/';
						}
						$j++;
					}

					$col[$nb_col][0] = "-";
					if ($moy_cat_classe_point[$cat_id] == 0) {
						$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
						$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
						$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
					} else {
						$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_cat_classe_point[$cat_id]/$moy_cat_classe_effectif[$cat_id],1, ',', ' ');
						$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_cat_classe_min[$cat_id],1, ',', ' ');
						$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_cat_classe_max[$cat_id],1, ',', ' ');
					}
				}
			}
		}
	}

	//================================================================================================

	// La moyenne générale des élèves (dernière colonne... ou avant-dernière dans le cas année_entière)
	$nb_col++;

	// Pour affichage des moyennes de périodes précédentes
	if((isset($num_p1))&&(isset($num_p2))) {
		for($loop=$num_p1;$loop<$num_p2;$loop++) {
			if($loop>$num_p1) {$nb_col++;}
	
			//if(isset($note_sup_10)) {$col[$nb_col][1]='-';}
			if($temoin_note_sup10=='y') {$col[$nb_col][1]='-';}
		
			$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
			if(preg_match("/^Rang/i",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
			$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
			$ligne1[$nb_col].="<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale P$loop")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale P$loop\" />";
			$ligne1[$nb_col].="</a>";
			$ligne1_csv[$nb_col] = "Moyenne générale P$loop";
			$j = '0';
			while($j < $nb_lignes_tableau) {

//				if($referent=='une_periode') {
					//$indice_j_ele=$tab_moy['periodes'][$num_periode]['tab_login_indice'][$current_eleve_login[$j]];
					if(isset($tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])])) {
						$indice_j_ele=$tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];
						$tmp_moy_gen_ele=$tab_moy['periodes'][$loop]['moy_gen_eleve'][$indice_j_ele];
						if(($tmp_moy_gen_ele!='')&&($tmp_moy_gen_ele!='-')) {
							$col[$nb_col][$j+$ligne_supl] = number_format($tmp_moy_gen_ele,1, ',', ' ');
							//$col[$nb_col][$j+$ligne_supl] = $tmp_moy_gen_ele;
						}
						else {
							$col[$nb_col][$j+$ligne_supl] = '/';
						}
					}
					else {
						$col[$nb_col][$j+$ligne_supl] = '/';
					}
/*
				}
				else {
					// En mode annee, on fait les calculs
					if ($total_coef_eleve[$j+$ligne_supl] > 0) {
		
						$col[$nb_col][$j+$ligne_supl] = number_format($total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl],1, ',', ' ');
		
		
						if($current_eleve_login[$j]==$ele_login_debug) {
							$lignes_debug.="<b>Moyenne de l'élève=</b>".$total_points_eleve[$j+$ligne_supl]."/".$total_coef_eleve[$j+$ligne_supl]."=".$col[$nb_col][$j+$ligne_supl]."<br />";
						}
		
		
						// A REVOIR: IL FAUDRAIT CALCULER LES MOYENNES GENERALES DE CLASSE COMME MOYENNES DES MOYENNES GENERALES DES ELEVES
						// C'est presque le cas: les tableaux $total_points_classe et $total_points_classe sont des totaux effectués pour chaque élève en prenant les coef non bricolés.
						//$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
						//$moy_classe_point+=$total_points_classe[$j+$ligne_supl]/$total_coef_classe[$j+$ligne_supl];
						$moy_classe_point+=$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl];
						$moy_classe_effectif++;
		
						//$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
						//$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
						$moy_classe_min = min($moy_classe_min,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
						$moy_classe_max = max($moy_classe_max,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
					} else {
						$col[$nb_col][$j+$ligne_supl] = '/';
					}
				}
*/
				$j++;
			}
	
	
			// Lignes moyennes des dernières colonnes:
			//if($referent=='une_periode') {
				$col[$nb_col][0] = "-";
		
				$tmp_moy_gen_classe=$tab_moy['periodes'][$loop]['moy_generale_classe'];
				$moy_classe_min=$tab_moy['periodes'][$loop]['moy_min_classe'];
				$moy_classe_max=$tab_moy['periodes'][$loop]['moy_max_classe'];
		
				if(($tmp_moy_gen_classe=='')||($tmp_moy_gen_classe=='-')) {
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
				} else {
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($tmp_moy_gen_classe,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_classe_min,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_classe_max,1, ',', ' ');
				}
/*
			}
			else {
				$col[$nb_col][0] = "-";
				if ($moy_classe_point == 0) {
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
				} else {
					// A REVOIR: IL FAUDRAIT CALCULER LES MOYENNES GENERALES DE CLASSE COMME MOYENNES DES MOYENNES GENERALES DES ELEVES
					$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_classe_point/$moy_classe_effectif,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_classe_min,1, ',', ' ');
					$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_classe_max,1, ',', ' ');
				}
			}

			// Colonne rang (en fin de tableau (dernière colonne) dans le cas Année entière)
			if (($aff_rang) and ($referent!="une_periode")) {
				// Calculer le rang dans le cas année entière
				//$nb_col++;

				// Préparatifs
		
				// Initialisation d'un tableau pour les rangs et affectation des valeurs réindexées dans un tableau temporaire
				my_echo("<table>");
				my_echo("<tr>");
				my_echo("<td>");
					my_echo("<table>");
				unset($tmp_tab);
				$k=0;
				unset($rg);
				while($k < $nb_lignes_tableau) {
					$rg[$k]=$k;
		
					if ($total_coef_eleve[$k+$ligne_supl] > 0) {
						$tmp_tab[$k]=my_ereg_replace(",",".",$col[$nb_col][$k+1]);
						my_echo("<tr>");
						my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k]</td>");
						my_echo("</tr>");
					}
					else {
						my_echo("<tr>");
						my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k] --</td>");
						my_echo("</tr>");
						$tmp_tab[$k]="?";
					}
		
					$k++;
				}
					my_echo("</table>");
				my_echo("</td>");
		
				array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
		
				my_echo("<td>");
					my_echo("<table>");
				$k=0;
				while($k < $nb_lignes_tableau) {
					if(isset($rg[$k])) {
						my_echo("<tr><td>\$rg[$k]+1=".($rg[$k]+1)."</td><td>".$col[1][$rg[$k]+1]."</td></tr>");
		
					}
					$k++;
				}
					my_echo("</table>");
				my_echo("</td>");
				my_echo("</tr>");
				my_echo("</table>");
		
				// On ajoute une colonne
				$nb_col++;
		
				// Initialisation de la colonne ajoutée
				$j=1;
				while($j <= $nb_lignes_tableau) {
					$col[$nb_col][$j]="-";
					$j++;
				}
		
				// Affectation des rangs dans la colonne ajoutée
				$k=0;
				while($k < $nb_lignes_tableau) {
					if(isset($rg[$k])) {
						$col[$nb_col][$rg[$k]+1]=$k+1;
					}
					$k++;
				}
		
				// Remplissage de la ligne de titre
				//$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />";
		
				$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
						"document.getElementById('sens_tri').value='inverse';".
						"document.forms['formulaire_tri'].submit();\">".
						"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".
						"</a>";
		
				$ligne1_csv[$nb_col] = "Rang de l'élève";
		
				// Remplissage de la ligne coefficients
				$col[$nb_col][0] = "-";
		
				// Remplissage des lignes Moyenne générale, minimale et maximale
				$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
				$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
				$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
			}
*/

		}
	}

	if(($referent!="une_periode")&&($mode_calcul_moy_annee=="moyenne_des_moy_gen_periodes")) {
		// On est en mode annee_entiere (avec ou sans affichage des moyennes de périodes précédentes)
		// On va calculer les moyennes pour chaque élève comme (MoyGenEleP1+MoyGenEleP2+MoyGenEleP3)/3

		$moy_annee_somme_moy_gen_ele=array();
		$moy_annee_nb_moy_gen_ele=array();
		$moy_annee_moy_moy_gen_ele=array();
		$j=0;
		while($j < $nb_lignes_tableau) {
			$moy_annee_somme_moy_gen_ele[$j]=0;
			$moy_annee_nb_moy_gen_ele[$j]=0;
			$j++;
		}

		for($loop=$num_p1bis;$loop<$num_p2bis;$loop++) {
			$j=0;
			//echo "\$loop=$loop<br />";
			while($j < $nb_lignes_tableau) {
				if(isset($tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])])) {
					//echo $current_eleve_login[$j]."<br />";
					$indice_j_ele=$tab_moy['periodes'][$loop]['tab_login_indice'][my_strtoupper($current_eleve_login[$j])];
					$tmp_moy_gen_ele=$tab_moy['periodes'][$loop]['moy_gen_eleve'][$indice_j_ele];
					if(($tmp_moy_gen_ele!='')&&($tmp_moy_gen_ele!='-')) {
						$moy_annee_somme_moy_gen_ele[$j]+=$tmp_moy_gen_ele;
						$moy_annee_nb_moy_gen_ele[$j]++;
					}
				}
				$j++;
			}
		}

		$moy_annee_somme_toutes_moy_gen_ele=0;
		$moy_annee_nb_toutes_moy_gen_ele=0;
		$moy_annee_moy_max_moy_gen_ele=-1;
		$moy_annee_moy_min_moy_gen_ele=100;
		$j=0;
		while($j < $nb_lignes_tableau) {
			if($moy_annee_nb_moy_gen_ele[$j]==0) {
				$moy_annee_moy_moy_gen_ele[$j]="/";
			}
			else {
				$moy_tmp=$moy_annee_somme_moy_gen_ele[$j]/$moy_annee_nb_moy_gen_ele[$j];
				$moy_annee_moy_moy_gen_ele[$j]=number_format($moy_tmp,1, ',', ' ');;

				$moy_annee_somme_toutes_moy_gen_ele+=$moy_tmp;
				$moy_annee_nb_toutes_moy_gen_ele++;

				if($moy_tmp>$moy_annee_moy_max_moy_gen_ele) {
					$moy_annee_moy_max_moy_gen_ele=$moy_tmp;
				}
				if($moy_tmp<$moy_annee_moy_min_moy_gen_ele) {
					$moy_annee_moy_min_moy_gen_ele=$moy_tmp;
				}
			}
			$j++;
		}
		$moy_annee_moy_classe_moy_gen_ele="/";
		if($moy_annee_nb_toutes_moy_gen_ele>0) {
			$moy_annee_moy_classe_moy_gen_ele=number_format($moy_annee_somme_toutes_moy_gen_ele/$moy_annee_nb_toutes_moy_gen_ele,1, ',', ' ');;
		}

		if($moy_annee_moy_max_moy_gen_ele==-1) {
			$moy_annee_moy_max_moy_gen_ele="/";
		}
		else {
			$moy_annee_moy_max_moy_gen_ele=number_format($moy_annee_moy_max_moy_gen_ele,1, ',', ' ');;
		}

		if($moy_annee_moy_min_moy_gen_ele==100) {
			$moy_annee_moy_min_moy_gen_ele="/";
		}
		else {
			$moy_annee_moy_min_moy_gen_ele=number_format($moy_annee_moy_min_moy_gen_ele,1, ',', ' ');;
		}
	}

	if($referent!='une_periode') {
		if(isset($avec_moy_gen_periodes_precedentes)) {
			$nb_col++;
		}

		//if(isset($note_sup_10)) {$col[$nb_col][1]='-';}
		if($temoin_note_sup10=='y') {$col[$nb_col][1]='-';}
	
		$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
		if(preg_match("/^Rang/i",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
		$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
		$ligne1[$nb_col].="<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale\" />";
		$ligne1[$nb_col].="</a>";
		$ligne1_csv[$nb_col] = "Moyenne générale";
		$j = '0';



		for($y=0;$y<$nb_lignes_tableau+$ligne_supl;$y++) {
			my_echo("\$col[1][$y]=".$col[1][$y]."<br />");
		}

		while($j < $nb_lignes_tableau) {

			if($mode_calcul_moy_annee=="moyenne_des_moy_gen_periodes") {
				$col[$nb_col][$j+$ligne_supl] = $moy_annee_moy_moy_gen_ele[$j];
			}
			else {
				//echo "\$total_coef_eleve[$j+$ligne_supl]=".$total_coef_eleve[$j+$ligne_supl]."<br />";
				// En mode annee, on fait les calculs
				if ($total_coef_eleve[$j+$ligne_supl] > 0) {

					// 20140527: Il faudrait un autre mode de calcul
					$col[$nb_col][$j+$ligne_supl] = number_format($total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl],1, ',', ' ');
					//$col[$nb_col][$j+$ligne_supl] = $total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl];

					my_echo("\$col[$nb_col][$j+$ligne_supl]=".$col[$nb_col][$j+$ligne_supl]."<br />");

					if($current_eleve_login[$j]==$ele_login_debug) {
						$lignes_debug.="<b>Moyenne de l'élève=</b>".$total_points_eleve[$j+$ligne_supl]."/".$total_coef_eleve[$j+$ligne_supl]."=".$col[$nb_col][$j+$ligne_supl]."<br />";
					}

					// 20140527: Il faudrait un autre mode de calcul
					// A REVOIR: IL FAUDRAIT CALCULER LES MOYENNES GENERALES DE CLASSE COMME MOYENNES DES MOYENNES GENERALES DES ELEVES
					// C'est presque le cas: les tableaux $total_points_classe et $total_points_classe sont des totaux effectués pour chaque élève en prenant les coef non bricolés.
					//$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
					//$moy_classe_point+=$total_points_classe[$j+$ligne_supl]/$total_coef_classe[$j+$ligne_supl];
					$moy_classe_point+=$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl];
					$moy_classe_effectif++;

					//$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
					//$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
					if(($moy_classe_min!="-")&&($moy_classe_min!="")) {
						//echo "\$moy_classe_min = min($moy_classe_min,".$total_points_eleve[$j+$ligne_supl]."/".$total_coef_eleve[$j+$ligne_supl].")=".$moy_classe_min."<br />";
						$moy_classe_min = min($moy_classe_min,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
					}
					else {
						$moy_classe_min = $total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl];
					}
					$moy_classe_max = max($moy_classe_max,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
				} else {
					$col[$nb_col][$j+$ligne_supl] = '/';
				}
			}
			$j++;
		}


		// Lignes moyennes des dernières colonnes:

		//echo "\$nb_col=$nb_col<br />";
		//echo "\$moy_classe_point=$moy_classe_point<br />";
		//echo "\$moy_classe_min=$moy_classe_min<br />";

		$col[$nb_col][0] = "-";

		if(($temoin_note_sup10=='y')||($temoin_note_bonus=='y')) {
			$col[$nb_col][1] = "-";
		}
		my_echo("\$col[$nb_col][0]=".$col[$nb_col][0]."<br />");
		my_echo("\$col[$nb_col][1]=".$col[$nb_col][1]."<br />");


		if($mode_calcul_moy_annee=="moyenne_des_moy_gen_periodes") {
			$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = $moy_annee_moy_classe_moy_gen_ele;
			$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = $moy_annee_moy_min_moy_gen_ele;
			$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = $moy_annee_moy_max_moy_gen_ele;
		}
		else {
			if ($moy_classe_point == 0) {
				$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
				$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
				$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
			} else {
				// 20140527: Il faudrait un autre mode de calcul
				// A REVOIR: IL FAUDRAIT CALCULER LES MOYENNES GENERALES DE CLASSE COMME MOYENNES DES MOYENNES GENERALES DES ELEVES
				$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_classe_point/$moy_classe_effectif,1, ',', ' ');
				$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_classe_min,1, ',', ' ');
				$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_classe_max,1, ',', ' ');
			}
		}




		$corr=0;
		// Ajout d'une ligne de décalage si il y a une ligne de coeff
		if($col[1][0]=="Coefficient") {
			//$b_inf=1;
			//$b_sup=$nb_lignes_tableau+1;
			//$corr=1;
			$corr++;
		}
		// Ajout d'une ligne de décalage si il y a une ligne mode_moy
		//if($temoin_note_sup10=='y') {
		if(($temoin_note_sup10=='y')||($temoin_note_bonus=='y')) {
			$corr++;
		}




		// Colonne rang (en fin de tableau (dernière colonne) dans le cas Année entière)
		if (($aff_rang) and ($aff_rang=='y') and ($referent!="une_periode")) {
			// Calculer le rang dans le cas année entière
			//$nb_col++;

			// Préparatifs

			// Initialisation d'un tableau pour les rangs et affectation des valeurs réindexées dans un tableau temporaire
			my_echo("<table>");
			my_echo("<tr>");
			my_echo("<td>");
				my_echo("<table>");
			unset($tmp_tab);
			$k=0;
			unset($rg);
			while($k < $nb_lignes_tableau) {
				$rg[$k]=$k;
	
				if ($total_coef_eleve[$k+$ligne_supl] > 0) {
					//$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+1]);
					$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+$corr]);
					my_echo("<tr>");
					//my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k]</td>");
					my_echo("<td>".($k+$corr)."</td><td>".$col[1][$k+$corr]."</td><td>".$col[$nb_col][$k+$corr]."</td><td>$tmp_tab[$k]</td>");
					my_echo("</tr>");
				}
				else {
					my_echo("<tr>");
					//my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k] --</td>");
					my_echo("<td>".($k+$corr)."</td><td>".$col[1][$k+$corr]."</td><td>".$col[$nb_col][$k+$corr]."</td><td>$tmp_tab[$k] --</td>");
					my_echo("</tr>");
					$tmp_tab[$k]="?";
				}
	
				$k++;
			}
				my_echo("</table>");
			//my_echo("PLOP");
			my_echo("</td>");
	
			array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
	
			my_echo("<td>");
				my_echo("<table>");
			$k=0;
			while($k < $nb_lignes_tableau) {
				if(isset($rg[$k])) {
					//my_echo("<tr><td>\$rg[$k]+1=".($rg[$k]+1)."</td><td>".$col[1][$rg[$k]+1]."</td></tr>");
					my_echo("<tr><td>\$rg[$k]+$corr=".($rg[$k]+$corr)."</td><td>".$col[1][$rg[$k]+$corr]."</td></tr>");
	
				}
				$k++;
			}
				my_echo("</table>");
			my_echo("</td>");
			my_echo("</tr>");
			my_echo("</table>");
	
			// On ajoute une colonne
			$nb_col++;
	
			// Initialisation de la colonne ajoutée
			$j=1;
			while($j <= $nb_lignes_tableau) {
				$col[$nb_col][$j]="-";
				$j++;
			}

			// Affectation des rangs dans la colonne ajoutée
			$k=0;
			while($k < $nb_lignes_tableau) {
				if(isset($rg[$k])) {
					//$col[$nb_col][$rg[$k]+1]=$k+1;
					//$col[$nb_col][$rg[$k]+1]=$k+$corr;
					$col[$nb_col][$rg[$k]+$corr]=$k+1;
					//$col[$nb_col][$rg[$k]+$corr]=$k+1;
				}
				$k++;
			}

			/*
			echo "\$ligne_supl=$ligne_supl<br />";
			echo "\$col[$nb_col]<br />";
			echo "<pre>";
			print_r($col[$nb_col]);
			echo "</pre>";
			*/

			// Remplissage de la ligne de titre
			//$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />";
	
			$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
					"document.getElementById('sens_tri').value='inverse';".
					"document.forms['formulaire_tri'].submit();\">".
					"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".
					"</a>";
	
			$ligne1_csv[$nb_col] = "Rang de l'élève";
	
			// Remplissage de la ligne coefficients
			$col[$nb_col][0] = "-";
	
			// Remplissage des lignes Moyenne générale, minimale et maximale
			$col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
			$col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
			$col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
		}

	}

}
/*
echo "<p style='color:green'>";
for($loop=0;$loop<$nb_lignes_tableau;$loop++) {

	//echo "\$col[1][$loop+$ligne_supl]=".$col[1][$j+$ligne_supl]."<br />";
	echo "\$col[1][$loop+$ligne_supl]=".$col[1][$loop+$ligne_supl]."<br />";

}
echo "</p>";
*/
//====================
// DEBUG:
//echo $lignes_debug;
//====================

//===============================
// A FAIRE: 20080424
// INTERCALER ICI un dispositif analogue à celui de index1.php pour trier autrement

if((isset($_POST['col_tri']))&&($_POST['col_tri']!='')) {
	// Pour activer my_echo à des fins de debug, passer $debug à 1 dans la déclaration de la fonction plus haut dans la page
	my_echo("\$_POST['col_tri']=".$_POST['col_tri']."<br />");
	$col_tri=$_POST['col_tri'];

	$nb_colonnes=$nb_col;

	// if ($test_coef != 0) $col[1][0] = "Coefficient";

	$corr=0;
	// Ajout d'une ligne de décalage si il y a une ligne de coeff
	if($col[1][0]=="Coefficient") {
		//$b_inf=1;
		//$b_sup=$nb_lignes_tableau+1;
		//$corr=1;
		$corr++;
	}
	// Ajout d'une ligne de décalage si il y a une ligne mode_moy
	//if($temoin_note_sup10=='y') {
	if(($temoin_note_sup10=='y')||($temoin_note_bonus=='y')) {
		$corr++;
	}
	/*
	else {
		//$b_inf=0;
		//$b_sup=$nb_lignes_tableau;
		$corr=0;
	}
	*/

	// Vérifier si $col_tri est bien un entier compris entre 0 et $nb_col ou $nb_col+1
	if((mb_strlen(preg_replace("/[0-9]/","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
		my_echo("<table>");
		my_echo("<tr><td valign='top'>");
		unset($tmp_tab);
		for($loop=0;$loop<$nb_lignes_tableau;$loop++) {
		//for($loop=$b_inf;$loop<$b_sup;$loop++) {
			// Il faut le POINT au lieu de la VIRGULE pour obtenir un tri correct sur les notes
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col_csv[$col_tri][$loop]);
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop]);
			$tmp_tab[$loop]=preg_replace("/,/",".",$col[$col_tri][$loop+$corr]);
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop]);
			my_echo("\$tmp_tab[$loop]=".$tmp_tab[$loop]."<br />");
		}

		my_echo("</td>");
		my_echo("<td valign='top'>");

		$i=0;
		while($i < $nb_lignes_tableau) {
		//$i=$b_inf;
		//while($i < $b_sup) {
			//my_echo($col_csv[1][$i]."<br />");
			my_echo($col[1][$i+$corr]."<br />");
			$i++;
		}
		my_echo("</td>");
		my_echo("<td valign='top'>");


		//$i=0;
		//while($i < $nb_lignes_tableau) {
		$i=0;
		while($i < $nb_lignes_tableau) {
			$rg[$i]=$i;
			$i++;
		}

		// Tri du tableau avec stockage de l'ordre dans $rg d'après $tmp_tab
		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);


		$i=0;
		while($i < $nb_lignes_tableau) {
			my_echo("\$rg[$i]=".$rg[$i]."<br />");
			$i++;
		}
		my_echo("</td>");
		my_echo("<td valign='top'>");


		// On utilise des tableaux temporaires le temps de la réaffectation dans l'ordre
		$tmp_col=array();
		//$tmp_col_csv=array();

		$i=0;
		$rang_prec = 1;
		$note_prec='';
		while ($i < $nb_lignes_tableau) {
			$ind = $rg[$i];
			if ($tmp_tab[$i] == "-") {
				//$rang_gen = '0';
				$rang_gen = '-';
			}
			else {
				if ($tmp_tab[$i] == $note_prec) {
					$rang_gen = $rang_prec;
				}
				else {
					$rang_gen = $i+1;
				}
				$note_prec = $tmp_tab[$i];
				$rang_prec = $rang_gen;
			}

			//$col[$nb_col+1][$ind]="ind=$ind, i=$i et rang_gen=$rang_gen";
			for($m=1;$m<=$nb_colonnes;$m++) {
				my_echo("\$tmp_col[$m][$i]=\$col[$m][$ind+$corr]=".$col[$m][$ind+$corr]."<br />");
				$tmp_col[$m][$i]=$col[$m][$ind+$corr];
				//$tmp_col_csv[$m][$ind]=$col_csv[$m][$ind];

			}
			$i++;
		}
		my_echo("</td></tr>");
		my_echo("</table>");

		// On réaffecte les valeurs dans le tableau initial à l'aide du tableau temporaire
		if((isset($_POST['sens_tri']))&&($_POST['sens_tri']=="inverse")) {
			for($m=1;$m<=$nb_colonnes;$m++) {
				for($i=0;$i<$nb_lignes_tableau;$i++) {
					$col[$m][$i+$corr]=$tmp_col[$m][$nb_lignes_tableau-1-$i];
					//$col_csv[$m][$i]=$tmp_col_csv[$m][$nombre_eleves-1-$i];
				}
			}
		}
		else {
			for($m=1;$m<=$nb_colonnes;$m++) {
				//$col[$m]=$tmp_col[$m];
				//$col_csv[$m]=$tmp_col_csv[$m];
				// Pour ne pas perdre les lignes de moyennes de classe
				for($i=0;$i<$nb_lignes_tableau;$i++) {
					$col[$m][$i+$corr]=$tmp_col[$m][$i];
					//$col_csv[$m][$i]=$tmp_col_csv[$m][$i];
				}
			}
		}
	}
}
//=========================

//===============================


$nb_lignes_tableau = $nb_lignes_tableau + 3 + $ligne_supl;



function affiche_tableau_csv2($nombre_lignes, $nb_col, $ligne1, $col, $col_csv) {
	$chaine="";
	$j = 1;
	while($j < $nb_col+1) {
		if($j>1){
			//echo ";";
			$chaine.=";";
		}
		//echo $ligne1[$j];
		$chaine.=$ligne1[$j];
		$j++;
	}
	//echo "<br />";
	//echo "\n";
	$chaine.="\n";

	$i = "0";
	while($i < $nombre_lignes) {
		$j = 1;
		while($j < $nb_col+1) {
			if($j>1){
				//echo ";";
				$chaine.=";";
			}
			//echo $col[$j][$i];
			if(isset($col_csv[$j][$i])) {
				$chaine.=$col_csv[$j][$i];
			}
			else {
				$chaine.=$col[$j][$i];
			}
			$j++;
		}
		//echo "<br />";
		//echo "\n";
		$chaine.="\n";
		$i++;
	}
	return $chaine;
}


if(isset($_GET['mode'])) {
	if($_GET['mode']=="csv") {
		$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

		if ($referent == "une_periode") {
			$chaine_titre="Classe_".$classe."_Resultats_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
		}

		$now = gmdate('D, d M Y H:i:s') . ' GMT';

		$nom_fic=$chaine_titre."_".$now;

		// Filtrer les caractères dans le nom de fichier:
		$nom_fic=preg_replace("/[^a-zA-Z0-9_.-]/","",remplace_accents($nom_fic,'all'));
		$nom_fic.=".csv";

		/*
		echo "<table>";
		echo "<tr>";
		echo "<td style='vertical-align:top'>";
		echo "<pre>";
		echo print_r($ligne1_csv);
		echo "</pre>";
		echo "</td>";

		echo "<td style='vertical-align:top'>";
		echo "<pre>";
		echo print_r($col);
		echo "</pre>";
		echo "</td>";

		echo "<td style='vertical-align:top'>";
		echo "<pre>";
		echo print_r($col_csv);
		echo "</pre>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		die();
		*/
		send_file_download_headers('text/x-csv',$nom_fic);

		$fd="";
		$fd.=affiche_tableau_csv2($nb_lignes_tableau, $nb_col, $ligne1_csv, $col, $col_csv);
		//echo $fd;
		echo echo_csv_encoded($fd);
		die();
	}
	elseif($_GET['mode']=="pdf") {
		$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

		if ($referent == "une_periode") {
			$chaine_titre="Classe_".$classe."_Resultats_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
		}

		$now = gmdate('D, d M Y H:i:s') . ' GMT';

		$nom_fic=$chaine_titre."_".$now;

		// Filtrer les caractères dans le nom de fichier:
		$nom_fic=preg_replace("/[^a-zA-Z0-9_.-]/","",remplace_accents($nom_fic,'all'));
		$nom_fic.=".pdf";

		require_once('../fpdf/fpdf.php');
		require_once("../fpdf/class.multicelltag.php");

		// Fichier d'extension de fpdf pour le bulletin
		require_once("../class_php/gepi_pdf.class.php");

		// Fonctions php des bulletins pdf
		require_once("../bulletin/bulletin_fonctions.php");
		// Ensemble des données communes
		require_once("../bulletin/bulletin_donnees.php");

	
		session_cache_limiter('private');

		$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
		$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
		$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;

		$largeur_page=210;
		$hauteur_page=297;

		$pref_marge=7;
		/*
		$pref_marge=isset($_POST['marge_pdf_mes_moyennes']) ? $_POST['marge_pdf_mes_moyennes'] : getPref($_SESSION['login'],'marge_pdf_mes_moyennes',7);
		if(($pref_marge=="")||(!preg_match("/^[0-9]*$/", $pref_marge))||($pref_marge<5)) {
			$pref_marge=7;
		}
		else {
			savePref($_SESSION['login'], 'marge_pdf_mes_moyennes', $pref_marge);
		}
		*/
		//marge_pdf_mes_moyennes
		$marge_gauche=$pref_marge;
		$marge_droite=$pref_marge;
		$marge_haute=$pref_marge;
		$marge_basse=$pref_marge;

		$hauteur_police=10;
		$largeur_col_nom_ele=40;

		// Hauteur de la ligne du titre de la page
		$h_ligne_titre_page=10;

		// Hauteur de la première ligne de tableau avec les noms de matières à la verticale.
		$h_ligne_titre_tableau=40;

		// Hauteur par defaut des lignes de tableau:
		$h_cell=10;
		// La hauteur de ligne est-elle imposée?
		// Par défaut, on tente d'utiliser au mieux la hauteur de la page en modifiant $h_cell plus loin.
		// Il est possible d'interdire la modification
		// (cela peut servir, en mettant un $h_cell élevé, en DEBUG à forcer l'affichage sur plusieurs pages).
		$hauteur_ligne_imposee="n";

		if((isset($_GET['forcer_hauteur_ligne_pdf']))&&
		($_GET['forcer_hauteur_ligne_pdf']=="y")&&
		(isset($_GET['visu_toutes_notes_h_cell_pdf']))&&
		($_GET['visu_toutes_notes_h_cell_pdf']!="")&&
		($_GET['visu_toutes_notes_h_cell_pdf']>0)&&
		(preg_match("/^[0-9]*$/", $_GET['visu_toutes_notes_h_cell_pdf']))) {
			$hauteur_ligne_imposee="y";
			$h_cell=$_GET['visu_toutes_notes_h_cell_pdf'];
			if(getPref($_SESSION["login"], "visu_toutes_notes_forcer_h_cell_pdf", "n")!="y") {
				savePref($_SESSION['login'], "visu_toutes_notes_forcer_h_cell_pdf", "y");
			}
			savePref($_SESSION['login'], "visu_toutes_notes_h_cell_pdf", $_GET['visu_toutes_notes_h_cell_pdf']);
		}
		else {
			if(getPref($_SESSION["login"], "visu_toutes_notes_forcer_h_cell_pdf", "n")!="n") {
				savePref($_SESSION['login'], "visu_toutes_notes_forcer_h_cell_pdf", "n");
			}
		}

		// Largeur des colonnes
		$largeur_col=array();
		$largeur_col[1]=$largeur_col_nom_ele;
		$indice_col_app=array();

		$taille_max_police=$hauteur_police;
		$taille_min_police=ceil($taille_max_police/3);

		$x0=$marge_gauche;
		$y0=$marge_haute;

		$largeur_nomprenom_classe_et_notes=$marge_gauche+$largeur_col_nom_ele;

		$format_page="P";

		$pdf=new bul_PDF($format_page, 'mm', 'A4');
		$pdf->SetCreator($gepiSchoolName);
		$pdf->SetAuthor($gepiSchoolName);
		$pdf->SetKeywords('');
		$pdf->SetSubject('Toutes_notes');
		$pdf->SetTitle('Toutes_notes');
		$pdf->SetDisplayMode('fullwidth', 'single');
		$pdf->SetCompression(TRUE);
		$pdf->SetAutoPageBreak(TRUE, 5);

		$pdf->AddPage();
		$fonte='DejaVu';

		$pdf->SetFont($fonte,'B',8);


		$avec_date_naiss="n";
		for($i=2;$i<=count($ligne1_csv);$i++) {
			if(preg_match("/^Date de naiss/", $ligne1_csv[$i])) {
				$avec_date_naiss="y";
				break;
			}
		}

		$largeur_col_notes=floor(10*($largeur_page-$marge_gauche-$marge_droite-$largeur_col_nom_ele-15)/(count($ligne1_csv)-2))/10;
		//$info_largeur_col_notes="\$largeur_col_notes=floor(10*($largeur_page-$marge_gauche-$marge_droite-$largeur_col_nom_ele-15)/(".count($ligne1_csv)."-2))/10=$largeur_col_notes";

		function ajuste_FontSize($texte, $largeur_dispo, $hauteur_caractere_initiale, $graisse='', $hauteur_caractere_minimale, $fonte='DejaVu') {
			global $pdf;

			$hauteur_caractere=$hauteur_caractere_initiale;
			$pdf->SetFont($fonte,$graisse,$hauteur_caractere);
			$val = $pdf->GetStringWidth($texte);

			$etat_grandeur_texte='test';
			while($etat_grandeur_texte != 'ok') {
				if(($largeur_dispo < $val)&&($hauteur_caractere>=$hauteur_caractere_minimale-0.3)) {
					$hauteur_caractere = $hauteur_caractere-0.3;
					$pdf->SetFont($fonte,$graisse,$hauteur_caractere);
					$val = $pdf->GetStringWidth($texte);
				} else {
					$etat_grandeur_texte = 'ok';
				}
			}

			return $hauteur_caractere;
		}

		//====================================================
		// Recherche des tailles de polices optimales
/*
echo "\n";
print_r($col_csv[1]);
echo "\n";
*/
		// Une taille sans importance, histoire de tester
		$pdf->SetFont($fonte,'',12);
		// Recherche du plus long nom_prenom
		$texte_test[1]="Edmou Dugenou";
		$longueur_max_nom_prenom=0;
		$largeur_col[1]=$largeur_col_nom_ele;
		// $col_csv[1] contient la première colonne du tableau affiché et son indice commence à 0 avec le nom du premier élève ou le coefficient s'il est affiché
		//for($i=1;$i<=count($col_csv[1]);$i++) {
		//for($i=0;$i<=count($col_csv[1]);$i++) {
		for($i=0;$i<=count($col[1]);$i++) {
			// Si on n'affiche pas de coefficient, on ne va pas jusqu'à count($col_csv[1]

			$chaine_test="";
			if(isset($col_csv[1][$i])) {
				$chaine_test=$col_csv[1][$i];
			}
			elseif(isset($col[1][$i])) {
				$chaine_test=$col[1][$i];
			}

			if($chaine_test!="") {
				//echo "\$col_csv[1][$i]=".$col_csv[1][$i]."\n";
				//$longueur_courante=$pdf->GetStringWidth($col_csv[1][$i]);
				//$longueur_courante=$pdf->GetStringWidth($col[1][$i]);
				$longueur_courante=$pdf->GetStringWidth($chaine_test);
				if($longueur_courante>$longueur_max_nom_prenom) {
					//$texte_test[1]=$col_csv[1][$i];
					//$texte_test[1]=$col[1][$i];
					$texte_test[1]=$chaine_test;
					$longueur_max_nom_prenom=$longueur_courante;
				}
			}
		}
		$taille_police_col[1]=ajuste_FontSize($texte_test[1], $largeur_col[1], 12, 'B', 3);
/*
echo "\n";
print_r($ligne1_csv);
echo "\n";
*/
		// $ligne1_csv contient la première ligne du tableau affiché et son indice commence à 1 avec le Nom_prenom de l'eleve
		for($i=2;$i<=count($ligne1_csv);$i++) {
//echo "\$ligne1_csv[$i]=".$ligne1_csv[$i]."\n";
			if(preg_match("/^Date de naiss/", $ligne1_csv[$i])) {
				$largeur_col[$i]=15;
				$texte_test[$i]="99/99/99";
			}
			else {
				$largeur_col[$i]=$largeur_col_notes;
				$texte_test[$i]="disp";
			}
		}

		for($i=2;$i<=count($ligne1_csv);$i++) {
			$taille_police_col[$i]=ajuste_FontSize(" ".$texte_test[$i]." ", $largeur_col[$i], 12, '', 3);
		}

		$longueur_max_matiere=0;
		$chaine_longueur_max_matiere="";
		for($i=2;$i<=count($ligne1_csv);$i++) {
			// Texte à mettre à la verticale:
			$texte=$ligne1_csv[$i];

			$longueur_courante=$pdf->GetStringWidth($texte);
			if($longueur_courante>$longueur_max_matiere) {
				$longueur_max_matiere=$longueur_courante;
				$chaine_longueur_max_matiere=$texte;
			}
		}
		$taille_police_matiere=ajuste_FontSize(" ".$chaine_longueur_max_matiere." ", $h_ligne_titre_tableau, 12, 'B', 3);
		//====================================================

		//$texte_titre=$current_group['profs']['proflist_string']." - ".$current_group['description']." en ".$current_group['classlist_string'];
		$texte_titre=$chaine_titre;

		$pdf->SetXY($x0,$y0);

		$texte=$texte_titre;
		$largeur_dispo=$largeur_page-$marge_gauche-$marge_droite;
		$hauteur_caractere=12;
		$h_ligne=$h_ligne_titre_page;
		$graisse='B';
		$alignement='C';
		$bordure='';
		cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$hauteur_caractere,$fonte,$graisse,$alignement,$bordure);
		$y2=$y0+$h_ligne_titre_page;

		//===========================
		// Ligne d'entête du tableau
		//$pdf->SetXY($x0,$y0);
		$pdf->SetXY($x0,$y2);
		$largeur_dispo=$largeur_col_nom_ele;
		$texte=$ligne1_csv[1];

		$graisse='B';
		//$alignement='L';
		$alignement='C';
		$bordure='LRBT';
		cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

		$pdf->SetFont($fonte,'B',$taille_police_matiere);
		$alignement='C';
		$x2=$x0+$largeur_col_nom_ele;
		for($i=2;$i<=count($ligne1_csv);$i++) {
			$pdf->SetXY($x2, $y2);
			$largeur_dispo=$largeur_col[$i];

			// Cadre de la cellule:
			$pdf->Cell($largeur_dispo,$h_ligne_titre_tableau, "",'LRBT',2,'');

			// Texte à la verticale:
			$texte=" ".$ligne1_csv[$i]." ";

			//ajuste_FontSize($texte, $h_ligne_titre_tableau, 12, 'B', 5);

			$pdf->TextWithRotation($x2+Ceil($largeur_dispo/2),$y2+$h_ligne_titre_tableau,$texte,90);

			$x2+=$largeur_dispo;
		}
		//===========================

		//$h_cell=min(10, floor(($hauteur_page-$marge_haute-$marge_basse-$h_ligne_titre_page-$h_ligne_titre_tableau)/(count($col)-1)));
		// Il faut ajouter les trois lignes Min/Moy/Max
		//$h_cell=min(10, floor(($hauteur_page-$marge_haute-$marge_basse-$h_ligne_titre_page-$h_ligne_titre_tableau)/(count($col)-1+3)));
		if($hauteur_ligne_imposee!="y") {
			$h_cell=min(10, floor(($hauteur_page-$marge_haute-$marge_basse-$h_ligne_titre_page-$h_ligne_titre_tableau)/(count($col)+3)));
		}

		/*
		$pdf->SetXY(10, 110);
		$pdf->Cell(190,10, $info_largeur_col_notes,'LRBT',2,'');
		*/

		$graisse='';
		$alignement='C';
		$bordure='LRBT';
		$h_ligne=$h_cell;

		$y2=$y2+$h_ligne_titre_tableau;
		$y_sous_ligne_titre_tableau_pages_suivantes=$marge_haute+$h_ligne_titre_tableau;
		$k=1;
		//for($j=1;$j<count($col[1]);$j++) {
		for($j=0;$j<count($col[1]);$j++) {
			$x2=$x0;

			// 20130328
			if($j>0) {
				//if($y2+$h_ligne<$hauteur_page-$marge_basse) {
				if($y2+$h_ligne*2<$hauteur_page-$marge_basse) {
					$y2+=$h_ligne;
				}
				else {
					$pdf->AddPage();
					$y2=$y0;

					//===========================
					// Ligne d'entête du tableau
					//$pdf->SetXY($x0,$y0);
					$pdf->SetXY($x0,$y2);
					$largeur_dispo=$largeur_col_nom_ele;
					$texte=$ligne1_csv[1];

					$graisse='B';
					//$alignement='L';
					$alignement='C';
					$bordure='LRBT';
					cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

					$pdf->SetFont($fonte,'B',$taille_police_matiere);
					$alignement='C';
					$x2=$x0+$largeur_col_nom_ele;
					for($i=2;$i<=count($ligne1_csv);$i++) {
						$pdf->SetXY($x2, $y2);
						$largeur_dispo=$largeur_col[$i];

						// Cadre de la cellule:
						$pdf->Cell($largeur_dispo,$h_ligne_titre_tableau, "",'LRBT',2,'');

						// Texte à la verticale:
						$texte=" ".$ligne1_csv[$i]." ";

						//ajuste_FontSize($texte, $h_ligne_titre_tableau, 12, 'B', 5);

						$pdf->TextWithRotation($x2+Ceil($largeur_dispo/2),$y2+$h_ligne_titre_tableau,$texte,90);

						$x2+=$largeur_dispo;
					}
					//===========================
					// On réinitialise la graisse après la ligne d'entête
					$graisse='';
					//===========================

					$x2=$x0;
					$y2=$y_sous_ligne_titre_tableau_pages_suivantes;
				}
			}

			/*
			if($j%2==0) {
			$pdf->SetFillColor(0,0,0);
			}
			else {
			$pdf->SetFillColor(100,100,100);
			}
			*/

			for($i=1;$i<=count($ligne1_csv);$i++) {
				$pdf->SetXY($x2, $y2);

				$largeur_dispo=$largeur_col[$i];

				if(isset($col_csv[$i][$j])) {
					$texte=" ".$col_csv[$i][$j]." ";
					//$texte=$col_csv[$i][$j]." ";
				}
				else {
					$texte=" ".$col[$i][$j]." ";
					//$texte=$col[$i][$j]." ";
				}
				//$texte.=$taille_police_col[$i];

				$pdf->SetFont($fonte,$graisse, $taille_police_col[$i]);

				$pdf->Cell($largeur_dispo,$h_ligne, $texte,'LRBT',2,'C');

				// On n'obtient pas des notes toutes de la même taille... c'est tout moche:
				//cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

				$x2+=$largeur_dispo;
			}

			// 20130328
			//$y2+=$h_ligne;

			$k++;
		}

		$pref_output_mode_pdf=get_output_mode_pdf();

		send_file_download_headers('application/pdf',$nom_fic);
		$pdf->Output($nom_fic,$pref_output_mode_pdf);
		die();

	}
}

//**************** EN-TETE *****************
// Sans $titre_page, on n'affiche pas l'entête, mais on se retrouve alors sans rien dans le titre de la page dans la barre de menu du navigateur
// Ou plutôt, on se retrouve avec:
//          : Collège Tartempion
// $titre_page="";
// Ajout d'une variable:
$titre_page_title="Notes des bulletins";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if($vtn_coloriser_resultats=='y') {
	check_token(false);
	$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_%';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	foreach($vtn_couleur_texte as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_texte$key', value='$value';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}
	foreach($vtn_couleur_cellule as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_cellule$key', value='$value';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}
	foreach($vtn_borne_couleur as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_borne_couleur$key', value='$value';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}

//if(!isset($_SESSION['vtn_pref_num_periode'])) {
	$sql="DELETE FROM preferences WHERE name LIKE 'vtn_pref_%' AND login='".$_SESSION['login']."';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	//$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_date_naiss', 'aff_rang');
	$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_date_naiss', 'aff_rang', 'avec_moy_gen_periodes_precedentes');

	for($loop=0;$loop<count($tab_pref);$loop++) {
		$tmp_var=$tab_pref[$loop];
		if($$tmp_var=='') {$$tmp_var="n";}
		$sql="INSERT INTO preferences SET name='vtn_pref_".$tmp_var."', value='".$$tmp_var."', login='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		$_SESSION['vtn_pref_'.$tmp_var]=$$tmp_var;
	}

	// Mettre aussi utiliser_coef_perso et vtn_coloriser_resultats
	// PB pour les coef perso, ce sont des associations coef/groupe qui sont faites et le groupe n'est que rarement commun d'une classe à une autre
	$sql="INSERT INTO preferences SET name='vtn_pref_coloriser_resultats', value='$vtn_coloriser_resultats', login='".$_SESSION['login']."';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	$_SESSION['vtn_pref_coloriser_resultats']=$vtn_coloriser_resultats;
	
//}

$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

// Lien pour générer un PDF
echo "<div class='noprint' style='float: right; border: 1px solid black; background-color: white; width: 3em; height: 1em; text-align: center; padding-bottom:3px; margin-left:3px;'>
<a href='".$_SERVER['PHP_SELF']."?mode=pdf&amp;id_classe=$id_classe&amp;num_periode=$num_periode";

if(($aff_abs)&&($aff_abs=='y')) {
	echo "&amp;aff_abs=$aff_abs";
}
if(($aff_reg)&&($aff_reg=='y')) {
	echo "&amp;aff_reg=$aff_reg";
}
if(($aff_doub)&&($aff_doub=='y')) {
	echo "&amp;aff_doub=$aff_doub";
}
if(($aff_rang)&&($aff_rang=='y')) {
	echo "&amp;aff_rang=$aff_rang";
}
if(($aff_date_naiss)&&($aff_date_naiss=='y')) {
	echo "&amp;aff_date_naiss=$aff_date_naiss";
}

if($utiliser_coef_perso=='y') {
	echo "&amp;utiliser_coef_perso=y";
	foreach($coef_perso as $key => $value) {
		echo "&amp;coef_perso[$key]=$value";
	}
	/*
	foreach($note_sup_10 as $key => $value) {
		echo "&amp;note_sup_10[$key]=$value";
	}
	*/
	foreach($mode_moy_perso as $tmp_id_groupe => $tmp_mode_moy) {
		echo "&amp;mode_moy_perso[$tmp_id_groupe]=$tmp_mode_moy";
	}
}

if((isset($avec_moy_gen_periodes_precedentes))&&($avec_moy_gen_periodes_precedentes=="y")) {
	echo "&amp;avec_moy_gen_periodes_precedentes=y";
}

if((isset($_POST['forcer_hauteur_ligne_pdf']))&&
($_POST['forcer_hauteur_ligne_pdf']=="y")&&
(isset($_POST['visu_toutes_notes_h_cell_pdf']))&&
($_POST['visu_toutes_notes_h_cell_pdf']!="")&&
($_POST['visu_toutes_notes_h_cell_pdf']>0)&&
(preg_match("/^[0-9]*$/", $_POST['visu_toutes_notes_h_cell_pdf']))) {
	echo "&amp;forcer_hauteur_ligne_pdf=".$_POST['forcer_hauteur_ligne_pdf'];
	echo "&amp;visu_toutes_notes_h_cell_pdf=".$_POST['visu_toutes_notes_h_cell_pdf'];
}

echo "' target='_blank'>PDF</a>
</div>\n";

// Lien pour générer un CSV
echo "<div class='noprint' style='float: right; border: 1px solid black; background-color: white; width: 7em; height: 1em; text-align: center; padding-bottom:3px;'>
<a href='".$_SERVER['PHP_SELF']."?mode=csv&amp;id_classe=$id_classe&amp;num_periode=$num_periode";

if(($aff_abs)&&($aff_abs=='y')) {
	echo "&amp;aff_abs=$aff_abs";
}
if(($aff_reg)&&($aff_reg=='y')) {
	echo "&amp;aff_reg=$aff_reg";
}
if(($aff_doub)&&($aff_doub=='y')) {
	echo "&amp;aff_doub=$aff_doub";
}
if(($aff_rang)&&($aff_rang=='y')) {
	echo "&amp;aff_rang=$aff_rang";
}
if(($aff_date_naiss)&&($aff_date_naiss=='y')) {
	echo "&amp;aff_date_naiss=$aff_date_naiss";
}

if($utiliser_coef_perso=='y') {
	echo "&amp;utiliser_coef_perso=y";
	foreach($coef_perso as $key => $value) {
		echo "&amp;coef_perso[$key]=$value";
	}
	/*
	foreach($note_sup_10 as $key => $value) {
		echo "&amp;note_sup_10[$key]=$value";
	}
	*/
	foreach($mode_moy_perso as $tmp_id_groupe => $tmp_mode_moy) {
		echo "&amp;mode_moy_perso[$tmp_id_groupe]=$tmp_mode_moy";
	}
}

if((isset($avec_moy_gen_periodes_precedentes))&&($avec_moy_gen_periodes_precedentes=="y")) {
	echo "&amp;avec_moy_gen_periodes_precedentes=y";
}
//echo "'>CSV</a>
echo "'>Export CSV</a>
</div>\n";

// Pour ajouter une marge:
echo "<div id='div_prepa_conseil_vtn'";
if(isset($_POST['vtn_pref_marges'])) {
	$vtn_pref_marges=preg_replace('/[^0-9]/','',$_POST['vtn_pref_marges']);
	if($vtn_pref_marges!='') {
		echo " style='margin:".$vtn_pref_marges."px;'";
		savePref($_SESSION['login'],'vtn_pref_marges',$vtn_pref_marges);
	}
	// Pour permettre de ne pas inserer de margin et memoriser ce choix, on accepte le champ vide:
	$_SESSION['vtn_pref_marges']=$vtn_pref_marges;
}
echo ">\n";

// Affichage de la légende de la colorisation
if($vtn_coloriser_resultats=='y') {
	echo "<div class='noprint' style='float: right; width: 10em; text-align: center; padding-bottom:3px;'>\n";

	echo "<p class='bold' style='text-align:center;'>Légende de la colorisation</p>\n";
	$legende_colorisation="<table class='boireaus' summary='Légende de la colorisation'>\n";
	$legende_colorisation.="<thead>\n";
		$legende_colorisation.="<tr>\n";
		$legende_colorisation.="<th>Borne<br />supérieure</th>\n";
		$legende_colorisation.="<th>Couleur texte</th>\n";
		$legende_colorisation.="<th>Couleur cellule</th>\n";
		$legende_colorisation.="</tr>\n";
	$legende_colorisation.="</thead>\n";
	$legende_colorisation.="<tbody>\n";
	$alt=1;
	foreach($vtn_borne_couleur as $key => $value) {
		$alt=$alt*(-1);
		$legende_colorisation.="<tr class='lig$alt'>\n";
		$legende_colorisation.="<td>$vtn_borne_couleur[$key]</td>\n";
		$legende_colorisation.="<td style='color:$vtn_couleur_texte[$key]'>$vtn_couleur_texte[$key]</td>\n";
		$legende_colorisation.="<td style='color:$vtn_couleur_cellule[$key]'>$vtn_couleur_cellule[$key]</td>\n";
		$legende_colorisation.="</tr>\n";
	}
	$legende_colorisation.="</tbody>\n";
	$legende_colorisation.="</table>\n";

	echo $legende_colorisation;
	echo "</div>\n";
}

if ($referent == "une_periode") {
	echo "<p class=bold>Classe : $classe - Résultats : $nom_periode[$num_periode] - Année scolaire : ".getSettingValue("gepiYear")."</p>";
} else {
	echo "<p class=bold>Classe : $classe - Résultats : Moyennes annuelles - Année scolaire : ".getSettingValue("gepiYear")."</p>";
}

//echo "\$affiche_categories=$affiche_categories<br />";

affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

//if(isset($note_sup_10)) {
if($temoin_note_sup10=='y') {
	//if(count($note_sup_10)==1) {
	if($nb_note_sup_10==1) {
		echo "<p>Une matière n'est comptée que pour les notes supérieures à 10.</p>\n";
	}
	else {
		//echo "<p>".count($note_sup_10)." matières ne sont comptées que pour les notes supérieures à 10.</p>\n";
		echo "<p>".$nb_note_sup_10." matières ne sont comptées que pour les notes supérieures à 10.</p>\n";
	}
}

if($temoin_note_bonus=='y') {
	if($nb_note_bonus==1) {
		echo "<p>Il y a une matière à bonus&nbsp;: ";
	}
	else {
		echo "<p>Il y a ".$nb_note_bonus." matières à bonus&nbsp;: ";
	}

	echo "seuls les points au-dessus de 10/20 comptent (<em>éventuellement pondérés</em>), mais leur coefficient n'est pas intégré dans le total des coefficients. (<em>règle appliquée aux options du Baccalauréat, par ex.</em>).</p>\n";
}

if($vtn_coloriser_resultats=='y') {
	echo "<p class='bold'>Légende de la colorisation&nbsp;:</p>\n";
	echo $legende_colorisation;
}
echo "<p><br /></p>\n";

echo "</div>\n"; // Fin du div_prepa_conseil_vtn

//=======================================================
// MODIF: boireaus 20080424
// Pour permettre de trier autrement...
echo "\n<!-- Formulaire pour l'affichage avec tri sur la colonne cliquée -->\n";
echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" name=\"formulaire_tri\">\n";
echo add_token_field();

echo "<input type='hidden' name='col_tri' id='col_tri' value='' />\n";
echo "<input type='hidden' name='sens_tri' id='sens_tri' value='' />\n";

echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";

if(isset($_POST['aff_abs'])) {
	echo "<input type='hidden' name='aff_abs' value='".$_POST['aff_abs']."' />\n";
}
if(isset($_POST['aff_reg'])) {
	echo "<input type='hidden' name='aff_reg' value='".$_POST['aff_reg']."' />\n";
}
if(isset($_POST['aff_doub'])) {
	echo "<input type='hidden' name='aff_doub' value='".$_POST['aff_doub']."' />\n";
}
if(isset($_POST['aff_rang'])) {
	echo "<input type='hidden' name='aff_rang' value='".$_POST['aff_rang']."' />\n";
}
if(isset($_POST['aff_date_naiss'])) {
	echo "<input type='hidden' name='aff_date_naiss' value='".$_POST['aff_date_naiss']."' />\n";
}

if($utiliser_coef_perso=='y') {
	echo "<input type='hidden' name='utiliser_coef_perso' value='$utiliser_coef_perso' />\n";
	foreach($coef_perso as $key => $value) {
		echo "<input type='hidden' name='coef_perso[$key]' value='$value' />\n";
	}
	if(isset($note_sup_10)) {
		foreach($note_sup_10 as $key => $value) {
			echo "<input type='hidden' name='note_sup_10[$key]' value='$value' />\n";
		}
	}
}

if($vtn_coloriser_resultats=='y') {
	echo "<input type='hidden' name='vtn_coloriser_resultats' value='$vtn_coloriser_resultats' />\n";
	foreach($vtn_couleur_texte as $key => $value) {
		echo "<input type='hidden' name='vtn_couleur_texte[$key]' value='$value' />\n";
	}
	foreach($vtn_couleur_cellule as $key => $value) {
		echo "<input type='hidden' name='vtn_couleur_cellule[$key]' value='$value' />\n";
	}
	foreach($vtn_borne_couleur as $key => $value) {
		echo "<input type='hidden' name='vtn_borne_couleur[$key]' value='$value' />\n";
	}
}

echo "<input type='hidden' name='larg_tab' value='$larg_tab' />\n";
echo "<input type='hidden' name='bord' value='$bord' />\n";
echo "<input type='hidden' name='couleur_alterne' value='$couleur_alterne' />\n";

echo "</form>\n";

if(isset($col_tri)) {
	echo "<script type='text/javascript'>
	if(document.getElementById('td_ligne1_$col_tri')) {
		document.getElementById('td_ligne1_$col_tri').style.backgroundColor='white';
	}
</script>\n";
}
else {
	echo "<script type='text/javascript'>
	if(document.getElementById('td_ligne1_1')) {
		document.getElementById('td_ligne1_1').style.backgroundColor='white';
	}
</script>\n";

	// Infobulle
/*
	echo creer_div_infobulle("div_stop","","","Ce bouton permet s'il est coché d'interrompre les passages automatiques à la page suivante","",12,0,"n","n","y","n");
	$texte.="</div>\n";
	$texte.="</form>\n";
*/
	$titre="Informations";
	$texte="<p>Cette page affiche les moyennes des élèves de la classe de ".$classe.".</p>";
	$texte.="<ul>";
	$texte.="<li>Vous pouvez trier ce tableau à la demande&nbsp;: chaque intitulé de colonne est une clef de tri.</li>";
	$texte.="<li>Vous pouvez aussi exporter ces moyennes au format CSV (<i>lisible par un tableur</i>).</li>";
	$texte.="</ul>";
	//$texte.="";
	//$tabdiv_infobulle[]=creer_div_infobulle('div_informations',$titre,"",$texte,"",35,0,'y','y','n','n');
	$class_special_infobulle="noprint";
	echo creer_div_infobulle('div_informations',$titre,"",$texte,"",35,0,'y','y','n','n');
	$class_special_infobulle="";

	echo "<script type='text/javascript'>
	// Je ne saisis pas pourquoi la capture des mouvements ne fonctionne pas correctement ici???
	// En fait, il y avait un problème d'initialisation de xMousePos et yMousePos (corrigé dans position.js)
	//setTimeout(\"if(document.getElementById('div_informations')) {document.onmousemove=crob_position;afficher_div('div_informations','y',20,20);}\",1500);
	setTimeout(\"if(document.getElementById('div_informations')) {afficher_div('div_informations','y',20,20);}\",1500);
</script>\n";

}
//=======================================================

echo "<div class='noprint'>\n";
//===========================================================
echo "<p><em>NOTE&nbsp;:</em></p>\n";
require("../lib/textes.inc.php");
echo "<p style='margin-left: 3em;'>$explication_bulletin_ou_graphe_vide";
echo "<br />\n";
echo "Vous pouvez aussi consulter les moyennes des carnets de notes à un instant T avant la fin de période via <a href='../cahier_notes/index2.php?id_classe=$id_classe'>Visualisation des moyennes des carnets de notes</a> tout en sachant qu'avant la fin de période, toutes les notes ne sont pas encore nécessairement saisies... et que par conséquent les informations obtenues peuvent être remises en cause par les résultats saisis par la suite.";
echo "</p>\n";
//===========================================================
echo "</div>\n";

require("../lib/footer.inc.php");
?>
