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
//$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
// Modifié pour pouvoir récupérer ces variables en GET pour les CSV
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

$utiliser_coef_perso=isset($_POST['utiliser_coef_perso']) ? $_POST['utiliser_coef_perso'] : (isset($_GET['utiliser_coef_perso']) ? $_GET['utiliser_coef_perso'] : "n");
$coef_perso=isset($_POST['coef_perso']) ? $_POST['coef_perso'] : (isset($_GET['coef_perso']) ? $_GET['coef_perso'] : NULL);
$note_sup_10=isset($_POST['note_sup_10']) ? $_POST['note_sup_10'] : (isset($_GET['note_sup_10']) ? $_GET['note_sup_10'] : NULL);

if ($num_periode=="annee") {
    $referent="annee";
} else {
    $referent="une_periode";
}

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
		$test = mysql_num_rows(mysql_query("SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
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

//============================
//$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] :  NULL;
$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] : (isset($_GET['aff_date_naiss']) ? $_GET['aff_date_naiss'] : NULL);
//============================

$couleur_alterne = isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] :  NULL;
include "../lib/periodes.inc.php";

// On appelle les élèves
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	// On ne sélectionne que les élèves que le professeur a en cours
	if ($referent=="une_periode")
		// Calcul sur une seule période
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* " .
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
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* " .
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
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
	else {
		// Calcul sur l'année
		$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
	}
}

$nb_lignes_eleves = mysql_num_rows($appel_donnees_eleves);
$nb_lignes_tableau = $nb_lignes_eleves;

//Initialisation
$moy_classe_point = 0;
$moy_classe_effectif = 0;
$moy_classe_min = 20;
$moy_classe_max = 0;
$moy_cat_classe_point = array();
$moy_cat_classe_effectif = array();
$moy_cat_classe_min = array();
$moy_cat_classe_max = array();


// On teste la présence d'au moins un coeff pour afficher la colonne des coef
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
$ligne_supl = 0;
if ($test_coef != 0) $ligne_supl = 1;

$temoin_note_sup10="n";
if($utiliser_coef_perso=='y') {
	if(isset($note_sup_10)) {
		$ligne_supl++;
		$temoin_note_sup10="y";
	}
}
else {
	$sql="SELECT 1=1 FROM j_groupes_classes jgc WHERE jgc.id_classe='".$id_classe."' AND jgc.mode_moy='sup10';";
	$test_note_sup10 = mysql_query($sql);
	if(mysql_num_rows($test_note_sup10)>0) {
		$ligne_supl++;
		$temoin_note_sup10="y";
	}
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
    $get_cat = mysql_query("SELECT id FROM matieres_categories");
    $categories = array();
    while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
        $categories[] = $row["id"];
        $moy_cat_classe_point[$row["id"]] = 0;
        $moy_cat_classe_effectif[$row["id"]] = 0;
        $moy_cat_classe_min[$row["id"]] = 20;
        $moy_cat_classe_max[$row["id"]] = 0;
    }

    $cat_names = array();
    foreach ($categories as $cat_id) {
        $cat_names[$cat_id] = html_entity_decode_all_version(mysql_result(mysql_query("SELECT nom_court FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0));
    }
}
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

//
// définition des premières colonnes nom, régime, doublant, ...
//
$displayed_categories = array();
$j = 0;
while($j < $nb_lignes_tableau) {
    // colonne nom+prénom
    $current_eleve_login[$j] = mysql_result($appel_donnees_eleves, $j, "login");
    $col[1][$j+$ligne_supl] = @mysql_result($appel_donnees_eleves, $j, "nom")." ".@mysql_result($appel_donnees_eleves, $j, "prenom");
    $ind = 2;
	//=======================================
	// colonne date de naissance
    if ($aff_date_naiss){
		$tmpdate=mysql_result($appel_donnees_eleves, $j, "naissance");
		$tmptab=explode("-",$tmpdate);
		if(strlen($tmptab[0])==4){$tmptab[0]=substr($tmptab[0],2,2);}
        $col[$ind][$j+$ligne_supl]=$tmptab[2]."/".$tmptab[1]."/".$tmptab[0];
        $ind++;
	}
	//=======================================

    // colonne régime
    if (($aff_reg) or ($aff_doub))
        $regime_doublant_eleve = mysql_query("SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login[$j]'");
    if ($aff_reg) {
        $col[$ind][$j+$ligne_supl] = @mysql_result($regime_doublant_eleve, 0, "regime");
        $ind++;
    }
    // colonne doublant
    if ($aff_doub) {
        $col[$ind][$j+$ligne_supl] = @mysql_result($regime_doublant_eleve, 0, "doublant");
        $ind++;
    }
    // Colonne absence
    if ($aff_abs) {
        $abs_eleve = "NR";
        if ($referent=="une_periode")
            $abs_eleve = sql_query1("SELECT nb_absences FROM absences WHERE
            login = '$current_eleve_login[$j]' and
            periode = '".$num_periode."'
            ");
        else {
            $abs_eleve = sql_query1("SELECT sum(nb_absences) FROM absences WHERE
            login = '$current_eleve_login[$j]'");
        }

        if ($abs_eleve == '-1') $abs_eleve = "NR";
        $col[$ind][$j+$ligne_supl] = $abs_eleve;
        $ind++;
    }

    // Colonne rang
    if (($aff_rang) and ($referent=="une_periode")) {
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
//if ($aff_reg) $ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=Régime&width=22\" width=\"22\" border=0 alt=\"régime\">";
//if ($aff_doub) $ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=Redoublant&width=22\" width=\"22\" border=0 alt=\"doublant\">";
//if ($aff_abs) $ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=1/2 journées d'absence&width=22\" width=\"22\" border=0 alt=\"1/2 journées d'absence\">";
//if (($aff_rang) and ($referent=="une_periode")) $ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=Rang de l'élève&width=22\" width=\"22\" border=0 alt=\"Rang de l'élève\">";
//if ($aff_reg) $ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".htmlentities("Régime")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"régime\" />";
//=========================
if ($aff_date_naiss){
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Date de naissance")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"date de naissance\" />";
	$ligne1_csv[] = "Date de naissance";
}
//=========================
if ($aff_reg){
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Régime")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"régime\" />";
	$ligne1_csv[]="Régime";
}
if($aff_doub){
	$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=Redoublant&amp;width=22\" width=\"22\" border=\"0\" alt=\"doublant\" />";
	$ligne1_csv[]="Redoublant";
}
if ($aff_abs){
	//$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"1/2 journées d'absence\" />";
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"1/2 journées d'absence\" />".
				"</a>";

	$ligne1_csv[]="1/2 journées d'absence";
}
if (($aff_rang) and ($referent=="une_periode")){
	//$ligne1[] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />";
	/*
	$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
	if(my_eregi("^Rang",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
	$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
    $ligne1[$nb_col].="<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale\" />";
	$ligne1[$nb_col].="</a>";
	*/
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".
				"</a>";
	//"<img src=\"../lib/create_im_mat.php?texte=".rawurlencode(html_entity_decode("Rang de l&apos;&eacute;l&egrave;ve"))."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Rang de l'élève\" />".

	//echo count($ligne1);

	$ligne1_csv[]="Rang de l'élève";
}

if ($test_coef != 0) {$col[1][0] = "Coefficient";}

// Etiquettes des trois dernières lignes
$col[1][$nb_lignes_tableau+$ligne_supl] = "Moyenne";
$col[1][$nb_lignes_tableau+1+$ligne_supl] = "Min";
$col[1][$nb_lignes_tableau+2+$ligne_supl] = "Max";
$ind = 2;
$nb_col = 1;
$k= 1;

//=========================
if ($aff_date_naiss){
	if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}
//=========================

if ($aff_reg) {
    if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}
if ($aff_doub) {
    if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}
if ($aff_abs) {
    if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}
if (($aff_rang) and ($referent=="une_periode")) {
    if ($test_coef != 0) $col[$ind][0] = "-";
    $col[$ind][$nb_lignes_tableau+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+1+$ligne_supl] = "-";
    $col[$ind][$nb_lignes_tableau+2+$ligne_supl] = "-";
    $nb_col++;
    $k++;
    $ind++;
}


$j = '0';
while($j < $nb_lignes_tableau) {
   //$total_coef[$j+$ligne_supl] = 0;
   $total_coef_classe[$j+$ligne_supl] = 0;
   $total_coef_eleve[$j+$ligne_supl] = 0;

   //$total_points[$j+$ligne_supl] = 0;
   $total_points_classe[$j+$ligne_supl] = 0;
   $total_points_eleve[$j+$ligne_supl] = 0;

   //$total_cat_coef[$j+$ligne_supl] = array();
   $total_cat_coef_classe[$j+$ligne_supl] = array();
   $total_cat_coef_eleve[$j+$ligne_supl] = array();

   //$total_cat_points[$j+$ligne_supl] = array();
   $total_cat_points_classe[$j+$ligne_supl] = array();
   $total_cat_points_eleve[$j+$ligne_supl] = array();
   // =================================
   // MODIF: boireaus
   if ($affiche_categories) {
	foreach ($categories as $cat_id) {
		//$total_cat_coef[$j+$ligne_supl][$cat_id] = 0;
		$total_cat_coef_classe[$j+$ligne_supl][$cat_id] = 0;
		$total_cat_coef_eleve[$j+$ligne_supl][$cat_id] = 0;

		//$total_cat_points[$j+$ligne_supl][$cat_id] = 0;
		$total_cat_points_classe[$j+$ligne_supl][$cat_id] = 0;
		$total_cat_points_eleve[$j+$ligne_supl][$cat_id] = 0;
	}
   }
   // =================================
   $j++;
}


//if((($utiliser_coef_perso=='y')&&(isset($note_sup_10)))||($temoin_note_sup10=='y')) {
if($temoin_note_sup10=='y') {
	//$col[1][1]="Note&gt;10";
	$col[1][1]="Note sup 10";
	//$col_csv[1][1]="Note sup 10";
	for($t=2;$t<=$nb_col+$lignes_groupes;$t++) {$col[$t][1]='-';}
}

//
// définition des colonnes matières
//
$i= '0';

//pour calculer la moyenne annee de chaque matiere
$moyenne_annee_matiere=array();
$prev_cat_id = null;
while($i < $lignes_groupes) {
    $moy_max = -1;
    $moy_min = 21;

    $nb_col++;
    $k++;

    foreach ($moyenne_annee_matiere as $tableau => $value) { unset($moyenne_annee_matiere[$tableau]);}

    $var_group_id = mysql_result($groupeinfo, $i, "id_groupe");
    $current_group = get_group($var_group_id);
    // Coeff pour la classe
    $current_coef = mysql_result($groupeinfo, $i, "coef");

    // Mode de calcul sur la moyenne: standard (-) ou note supérieure à 10
    $current_mode_moy = mysql_result($groupeinfo, $i, "mode_moy");
    // A FAIRE: A l'affichage, il faudrait mettre 1.0(*) quand le coeff n'est pas 1.0 pour tous les élèves à cause de coeffs personnalisés.
    if($utiliser_coef_perso=='y') {
        if(isset($coef_perso[$var_group_id])) {$current_coef=$coef_perso[$var_group_id];}
        if(isset($note_sup_10[$var_group_id])) {$col[$nb_col][1]='X';}
    }
    else {
        if($current_mode_moy=='sup10') {$col[$nb_col][1]='X';}
    }

    if ($affiche_categories) {
    // On regarde si on change de catégorie de matière
        if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
            $prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
        }
    }
    $j = '0';
    while($j < $nb_lignes_tableau) {

		// Coefficient personnalisé pour l'élève?
		$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
				"login = '".$current_eleve_login[$j]."' AND " .
				"id_groupe = '".$current_group["id"]."' AND " .
				"name = 'coef')";
		$test_coef_personnalise = mysql_query($sql);
		if (mysql_num_rows($test_coef_personnalise) > 0) {
			$coef_eleve = mysql_result($test_coef_personnalise, 0);
		} else {
			// Coefficient du groupe:
			$coef_eleve = $current_coef;
		}
		//$coef_eleve=number_format($coef_eleve,1, ',', ' ');


      if ($referent == "une_periode") {
        if (!in_array($current_eleve_login[$j], $current_group["eleves"][$num_periode]["list"])) {
            $col[$k][$j+$ligne_supl] = "/";
        } else {
            $current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
            $current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
            if ($current_eleve_statut != "") {
                $col[$k][$j+$ligne_supl] = $current_eleve_statut;
            } else {
                $temp = @mysql_result($current_eleve_note_query, 0, "note");
                if  ($temp != '')  {
                    $col[$k][$j+$ligne_supl] = number_format($temp,1, ',', ' ');
                    if ($current_coef > 0) {
                        // ===================================
						// MODIF: boireaus
						//if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
						if ($affiche_categories) {
							if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
						}
                        // ===================================


						// Coefficient personnalisé pour l'élève?
						$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
								"login = '".$current_eleve_login[$j]."' AND " .
								"id_groupe = '".$current_group["id"]."' AND " .
								"name = 'coef')";
						$test_coef_personnalise = mysql_query($sql);
						if (mysql_num_rows($test_coef_personnalise) > 0) {
							$coef_eleve = mysql_result($test_coef_personnalise, 0);
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
                    }
                } else {
                    $col[$k][$j+$ligne_supl] = '-';
                }
            }
        }
      } else {
        $p = "1";
        $moy = 0;
        $non_suivi = 2;
        $coef_moy = 0;
        while ($p < $nb_periode) {
            if (!in_array($current_eleve_login[$j], $current_group["eleves"][$p]["list"])) {
                $non_suivi = $non_suivi*2;
            } else {
                $current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$p')");
                $current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
                if ($current_eleve_statut == "") {
                    $temp = @mysql_result($current_eleve_note_query, 0, "note");
                    if  ($temp != '')  {
                        $moy += $temp;
                        $coef_moy++;
                    }
                 }
             }
             $p++;
         }
         if ($non_suivi == (pow(2,$nb_periode))) {
             // L'élève n'a suivi la matière sur aucune période
             $col[$k][$j+$ligne_supl] = "/";
         } else if ($coef_moy != 0) {
             // L'élève a au moins une note sur au moins une période
             $moy = $moy/$coef_moy;
             $moy_min = min($moy_min,$moy);
             $moy_max = max($moy_max,$moy);
             $col[$k][$j+$ligne_supl] = number_format($moy,1, ',', ' ');
             if ($current_coef > 0) {

				if($utiliser_coef_perso=='y') {
					if((isset($note_sup_10[$current_group["id"]]))&&($note_sup_10[$current_group["id"]]=='y')&&($moy<10)) {
						$coef_eleve=0;
						//echo $current_eleve_login[$j]." groupe n°".$current_group["id"]." (".$current_group["name"]."): coeff 0<br />";
					}
				}
				else {
					if(($current_mode_moy=='sup10')&&($moy<10)) {$coef_eleve=0;}
				}

                 if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
                 //$total_coef[$j+$ligne_supl] += $current_coef;
                 $total_coef_classe[$j+$ligne_supl] += $current_coef;
                 $total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
                 //$total_points[$j+$ligne_supl] += $current_coef*$moy;
                 $total_points_classe[$j+$ligne_supl] += $current_coef*$moy;
                 $total_points_eleve[$j+$ligne_supl] += $coef_eleve*$moy;
				if ($affiche_categories) {
					//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
					//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
					$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
					$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
					$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$moy;
					$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
				}
             }
         } else {
             // Bien que suivant la matière, l'élève n'a aucune note à toutes les période (absent, pas de note, disp ...)
             $col[$k][$j+$ligne_supl] = "-";
         }
      }
      $j++;
    }
    if ($referent == "une_periode") {
        $call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
        $call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
        $call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
    } else {
        $call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
    }

    $temp = @mysql_result($call_moyenne, 0, "moyenne");

    if ($test_coef != 0) {
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
        $temp = @mysql_result($call_min, 0, "note_min");
        if ($temp != '') {
            //$col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($temp,1, ',', ' ');
        } else {
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
        }
        $temp = @mysql_result($call_max, 0, "note_max");
        if ($temp != '') {
            //$col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
            $col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($temp,1, ',', ' ');
        } else {
            $col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
        }
    } else {
        if ($moy_min <=20)
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_min,1, ',', ' ');
        else
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';

        if ($moy_max >= 0)
            $col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_max,1, ',', ' ');
        else
            $col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';


    }

    $nom_complet_matiere = $current_group["description"];
    $nom_complet_coupe = (strlen($nom_complet_matiere) > 20)? urlencode(substr($nom_complet_matiere,0,20)."...") : urlencode($nom_complet_matiere);

    $nom_complet_coupe_csv=(strlen($nom_complet_matiere) > 20) ? substr($nom_complet_matiere,0,20) : $nom_complet_matiere;
	$nom_complet_coupe_csv=my_ereg_replace(";","",$nom_complet_coupe_csv);

    //$ligne1[$k] = "<img src=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" width=\"22\" border=\"0\" />";
    //$ligne1[$k] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"$nom_complet_coupe\" />";

	$ligne1[$k]="<a href='#' onclick=\"document.getElementById('col_tri').value='$k';";
	$ligne1[$k].="document.forms['formulaire_tri'].submit();\">";
	$ligne1[$k] .= "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"$nom_complet_coupe\" />";
	$ligne1[$k].="</a>";

    $ligne1_csv[$k] = "$nom_complet_coupe_csv";
    $i++;
}
// Dernière colonne des moyennes générales
//if ($ligne_supl == 1) {
if ($ligne_supl >= 1) {
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

			$j = '0';
			while($j < $nb_lignes_tableau) {
				//if ($total_cat_coef[$j+$ligne_supl][$cat_id] > 0) {
				if ($total_cat_coef_eleve[$j+$ligne_supl][$cat_id] > 0) {
					//$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id],1, ',', ' ');
					$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id],1, ',', ' ');
					//$moy_cat_classe_point[$cat_id] +=$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id];
					$moy_cat_classe_point[$cat_id] +=$total_cat_points_classe[$j+$ligne_supl][$cat_id]/$total_cat_coef_classe[$j+$ligne_supl][$cat_id];
					$moy_cat_classe_effectif[$cat_id]++;
					//$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
					//$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
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

    // La moyenne générale
    $nb_col++;

	//if(isset($note_sup_10)) {$col[$nb_col][1]='-';}
	if($temoin_note_sup10=='y') {$col[$nb_col][1]='-';}

    //$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=Moyenne générale&width=22\" width=\"22\" border=\"0\" />";
    //$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale\" />";
    //$ligne1[$nb_col] = "<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale\" />";
	$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
	if(my_eregi("^Rang",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
	$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
    $ligne1[$nb_col].="<img src=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" width=\"22\" border=\"0\" alt=\"Moyenne générale\" />";
	$ligne1[$nb_col].="</a>";
    $ligne1_csv[$nb_col] = "Moyenne générale";
    $j = '0';
    while($j < $nb_lignes_tableau) {
        //if ($total_coef[$j+$ligne_supl] > 0) {
        if ($total_coef_eleve[$j+$ligne_supl] > 0) {
            //$col[$nb_col][$j+$ligne_supl] = number_format($total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl],1, ',', ' ');

            $col[$nb_col][$j+$ligne_supl] = number_format($total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl],1, ',', ' ');

            //$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
            $moy_classe_point +=$total_points_classe[$j+$ligne_supl]/$total_coef_classe[$j+$ligne_supl];
            $moy_classe_effectif++;
            //$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
            //$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
            $moy_classe_min = min($moy_classe_min,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
            $moy_classe_max = max($moy_classe_max,$total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl]);
        } else {
            $col[$nb_col][$j+$ligne_supl] = '/';
        }
        $j++;
    }
    $col[$nb_col][0] = "-";
    if ($moy_classe_point == 0) {
        $col[$nb_col][$nb_lignes_tableau+$ligne_supl] = "-";
        $col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = "-";
        $col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = "-";
    } else {
        $col[$nb_col][$nb_lignes_tableau+$ligne_supl] = number_format($moy_classe_point/$moy_classe_effectif,1, ',', ' ');
        $col[$nb_col][$nb_lignes_tableau+1+$ligne_supl] = number_format($moy_classe_min,1, ',', ' ');
        $col[$nb_col][$nb_lignes_tableau+2+$ligne_supl] = number_format($moy_classe_max,1, ',', ' ');
    }



	// Colonne rang (en fin de tableau (dernière colonne) dans le cas Année entière)
	if (($aff_rang) and ($referent!="une_periode")) {
		// Calculer le rang dans le cas année entière
		//$nb_col++;
		/*
		function my_echo($texte) {
			$debug=0;
			if($debug!=0) {
				echo $texte;
			}
		}
		*/
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

}

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
	if($temoin_note_sup10=='y') {
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
	if((strlen(my_ereg_replace("[0-9]","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
		my_echo("<table>");
		my_echo("<tr><td valign='top'>");
		unset($tmp_tab);
		for($loop=0;$loop<$nb_lignes_tableau;$loop++) {
		//for($loop=$b_inf;$loop<$b_sup;$loop++) {
			// Il faut le POINT au lieu de la VIRGULE pour obtenir un tri correct sur les notes
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col_csv[$col_tri][$loop]);
			//$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop]);
			$tmp_tab[$loop]=my_ereg_replace(",",".",$col[$col_tri][$loop+$corr]);
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



function affiche_tableau_csv2($nombre_lignes, $nb_col, $ligne1, $col) {
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
			$chaine.=$col[$j][$i];
			$j++;
		}
		//echo "<br />";
		//echo "\n";
		$chaine.="\n";
		$i++;
	}
	return $chaine;
}


if(isset($_GET['mode'])){
	if($_GET['mode']=="csv"){
		$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

		if ($referent == "une_periode") {
			$chaine_titre="Classe_".$classe."_Resultats_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
		}

		$now = gmdate('D, d M Y H:i:s') . ' GMT';

		$nom_fic=$chaine_titre."_".$now.".csv";

		// Filtrer les caractères dans le nom de fichier:
		$nom_fic=my_ereg_replace("[^a-zA-Z0-9_.-]","",remplace_accents($nom_fic,'all'));

		header('Content-Type: text/x-csv');
		header('Expires: ' . $now);
		// lem9 & loic1: IE need specific headers
		if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: inline; filename="' . $nom_fic . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
			header('Pragma: no-cache');
		}

		$fd="";
		$fd.=affiche_tableau_csv2($nb_lignes_tableau, $nb_col, $ligne1_csv, $col);
		echo $fd;
		die();
	}
}

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

// Lien pour générer un CSV
echo "<div class='noprint' style='float: right; border: 1px solid black; background-color: white; width: 7em; height: 1em; text-align: center; padding-bottom:3px;'>
<a href='".$_SERVER['PHP_SELF']."?mode=csv&amp;id_classe=$id_classe&amp;num_periode=$num_periode";

if($aff_abs){
	echo "&amp;aff_abs=$aff_abs";
}
if($aff_reg){
	echo "&amp;aff_reg=$aff_reg";
}
if($aff_doub){
	echo "&amp;aff_doub=$aff_doub";
}
if($aff_rang){
	echo "&amp;aff_rang=$aff_rang";
}
if($aff_date_naiss){
	echo "&amp;aff_date_naiss=$aff_date_naiss";
}

if($utiliser_coef_perso=='y') {
	echo "&amp;utiliser_coef_perso=y";
	foreach($coef_perso as $key => $value) {
		echo "&amp;coef_perso[$key]=$value";
	}
	foreach($note_sup_10 as $key => $value) {
		echo "&amp;note_sup_10[$key]=$value";
	}
}

//echo "'>CSV</a>
echo "'>Export CSV</a>
</div>\n";


if ($referent == "une_periode") {
    echo "<p class=bold>Classe : $classe - Résultats : $nom_periode[$num_periode] - Année scolaire : ".getSettingValue("gepiYear")."</p>";
} else {
    echo "<p class=bold>Classe : $classe - Résultats : Moyennes annuelles - Année scolaire : ".getSettingValue("gepiYear")."</p>";
}

//echo "\$affiche_categories=$affiche_categories<br />";

affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

if(isset($note_sup_10)) {
	if(count($note_sup_10)==1) {
		echo "<p>Une matière n'est comptée que pour les notes supérieures à 10.</p>\n";
	}
	else {
		echo "<p>".count($note_sup_10)." matières ne sont comptées que pour les notes supérieures à 10.</p>\n";
	}
}

echo "<p><br /></p>\n";



//=======================================================
// MODIF: boireaus 20080424
// Pour permettre de trier autrement...
echo "\n<!-- Formulaire pour l'affichage avec tri sur la colonne cliquée -->\n";
echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" name=\"formulaire_tri\">\n";

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
	echo creer_div_infobulle('div_informations',$titre,"",$texte,"",35,0,'y','y','n','n');

	echo "<script type='text/javascript'>
	// Je ne saisis pas pourquoi la capture des mouvements ne fonctionne pas correctement ici???
	// En fait, il y avait un problème d'initialisation de xMousePos et yMousePos (corrigé dans position.js)
	//setTimeout(\"if(document.getElementById('div_informations')) {document.onmousemove=crob_position;afficher_div('div_informations','y',20,20);}\",1500);
	setTimeout(\"if(document.getElementById('div_informations')) {afficher_div('div_informations','y',20,20);}\",1500);
</script>\n";

}
//=======================================================

require("../lib/footer.inc.php");
?>