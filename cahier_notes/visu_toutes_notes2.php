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



// INSERT INTO `droits` VALUES ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//Initialisation

// Modifié pour pouvoir récupérer ces variables en GET pour les CSV
//$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;

//$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_classe=NULL;
$id_classe_recu_en_post="n";
if(isset($_POST['id_classe'])) {
	$id_classe=$_POST['id_classe'];
	$id_classe_recu_en_post="y";
}
elseif(isset($_GET['id_classe'])) {
	$id_classe=$_GET['id_classe'];
}

//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
//$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : "1");


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
	if ($_SESSION['statut'] == "professeur" AND 
	getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND
	getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
		$test = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT jgc.* FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE (jgp.login='".$_SESSION['login']."' AND jgc.id_groupe = jgp.id_groupe AND jgc.id_classe = '".$id_classe."')"));
		if ($test == "0") {
			if((!is_pp($_SESSION['login'], $id_classe))||(!getSettingAOui('GepiAccesReleveProfP'))) {
				$classe=get_nom_classe($id_classe);
				if($id_classe_recu_en_post=="y") {
					tentative_intrusion("3", "Tentative d'accès par un prof à une classe ($classe) dans laquelle il n'enseigne pas, sans en avoir l'autorisation. Tentative avancée : changement des valeurs de champs de type 'hidden' du formulaire.");
				}
				else {
					tentative_intrusion("3", "Tentative d'accès par un prof à une classe ($classe) dans laquelle il n'enseigne pas, sans en avoir l'autorisation. Changement des valeurs de id_classe dans la barre d'adresse.");
				}

				//echo "Vous ne pouvez pas accéder à cette classe car vous n'y êtes pas professeur !";
				//require ("../lib/footer.inc.php");
				header("Location: ../accueil.php?msg=Vous n'êtes pas professeur de la classe de $classe.");
				die();
			}
		}
	}
}

if((!isset($id_classe))||(!isset($num_periode))) {
	header("Location: index2.php?msg=".rawurlencode('Choisissez une classe'));
	die();
}

//debug_var();

check_token();

function my_echo($texte) {
	$debug=0;
	if($debug!=0) {
		echo $texte;
	}
}


$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;
$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;

//============================
//$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] :  NULL;
//$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] :  NULL;
//$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] :  NULL;
//$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] :  NULL;
//$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] :  NULL;

// Modifié pour pouvoir récupérer ces variables en GET pour les CSV
$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] : (isset($_GET['aff_abs']) ? $_GET['aff_abs'] : NULL);
$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] : (isset($_GET['aff_reg']) ? $_GET['aff_reg'] : NULL);
$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] : (isset($_GET['aff_doub']) ? $_GET['aff_doub'] : NULL);
$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] : (isset($_GET['aff_rang']) ? $_GET['aff_rang'] : NULL);
$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] : (isset($_GET['aff_date_naiss']) ? $_GET['aff_date_naiss'] : NULL);
//============================


$couleur_alterne = isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] :  NULL;

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
//============================

include "../lib/periodes.inc.php";

// On appelle les élèves
if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	if((!is_pp($_SESSION['login'], $id_classe))||(!getSettingAOui('GepiAccesReleveProfP'))) {
		// On ne sélectionne que les élèves que le professeur a en cours
		if ($referent=="une_periode")
			// Calcul sur une seule période
			$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* " .
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
			$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* " .
					"FROM eleves e, j_eleves_classes jec, j_eleves_groupes jeg, j_groupes_professeurs jgp " .
					"WHERE (" .
					"jec.id_classe='$id_classe' AND " .
					"e.login = jeg.login AND " .
					"jeg.login = jec.login AND " .
					"jeg.id_groupe = jgp.id_groupe AND " .
					"jgp.login = '".$_SESSION['login']."') " .
					"ORDER BY e.nom,e.prenom");
		}
	}
	else {
		if ($referent=="une_periode")
			// Calcul sur une seule période
			$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
		else {
			// Calcul sur l'année
			$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
		}
	}
} else {
	if ($referent=="une_periode")
		// Calcul sur une seule période
		$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
	else {
		// Calcul sur l'année
		$appel_donnees_eleves = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
	}
}

$nb_lignes_eleves = mysqli_num_rows($appel_donnees_eleves);
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
$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
$ligne_supl = 0;
if ($test_coef != 0) $ligne_supl = 1;


// On regarde si on doit afficher les moyennes des catégories de matières
$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
if ($affiche_categories == "y") {
	$affiche_categories = true;
} else {
	$affiche_categories = false;
}


// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
/*
if (($aff_rang) and ($referent=="une_periode")) {
	$periode_num=$num_periode;
	include "../lib/calcul_rang.inc.php";
	// Oui, mais pour la visualisation des moyennes des carnets de notes, 
	// ce rang ne correspond pas nécessairement tant que la recopie des moyennes n'est pas faite.
}
*/

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
	$get_cat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id FROM matieres_categories");
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
		$cat_names[$cat_id] = html_entity_decode(mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_court FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0));
	}
}
// Calcul du nombre de matières à afficher

if ($affiche_categories) {
	// On utilise les valeurs spécifiées pour la classe en question
	$groupeinfo = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
	"WHERE ( " .
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
} else {
	$groupeinfo = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
}
$lignes_groupes = mysqli_num_rows($groupeinfo);

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
		if(mb_strlen($tmptab[0])==4){$tmptab[0]=mb_substr($tmptab[0],2,2);}
        $col[$ind][$j+$ligne_supl]=$tmptab[2]."/".$tmptab[1]."/".$tmptab[0];
        $ind++;
	}
	//=======================================

	// colonne régime
	if (($aff_reg) or ($aff_doub))
		$regime_doublant_eleve = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM j_eleves_regime WHERE login = '$current_eleve_login[$j]'");
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
        if (getSettingValue("active_module_absence") != '2' || getSettingValue("abs2_import_manuel_bulletin") == 'y') {
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
                        $date_fin = new DateTime($date_jour->format('y')+1 . '-08-31');
                    } else {
                        $date_debut = new DateTime($date_jour->format('y')-1 . '-09-01');
                        $date_fin = new DateTime($date_jour->format('y') . '-08-31');
	}
                    $abs_eleve = strval($eleve->getDemiJourneesAbsence($date_debut,$date_fin)->count());
                }
            } else {
                $abs_eleve = "NR";
            }
            $col[$ind][$j + $ligne_supl] = $abs_eleve;
            $ind++;
        }
    }

	// Colonne rang
	if (($aff_rang) and ($referent=="une_periode")) {
		$ind_colonne_rang=$ind;
		// La table j_eleves_classes ne contient les bonnes infos de rang qu'une fois la recopie des moyennes effectuée.
		// Elle s'appuye sur la table matieres_notes qui n'est remplie qu'une fois la recopie faite, c'est-à-dire en fin de période... pas au moment où on utilise la Visualisation des moyennes des carnets de notes!
		/*
		$rang = sql_query1("select rang from j_eleves_classes where (
		periode = '".$num_periode."' and
		id_classe = '".$id_classe."' and
		login = '".$current_eleve_login[$j]."' )
		");
		if (($rang == 0) or ($rang == -1)) $rang = "-";
		$col[$ind][$j+$ligne_supl] = $rang;
		*/
		$col[$ind][$j+$ligne_supl]="-";

		$ind++;
	}

	$j++;
}

$num_debut_colonnes_matieres=$ind;

/*
// Colonne rang
if (($aff_rang) and ($referent!="une_periode")) {
	// Calculer le rang dans le cas année entière
}
*/


// Etiquettes des premières colonnes
//$ligne1[1] = "Nom ";
$ligne1[1] = "<a href='#' onclick=\"document.getElementById('col_tri').value='1';".
			"document.forms['formulaire_tri'].submit();\"".
			" style='text-decoration:none; color:black;'>".
			"Nom ".
			"</a>";
$ligne1_csv[1] = "Nom ";

//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Régime&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"régime\">";
//if ($aff_doub) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"doublant\">";
//if ($aff_abs) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=1/2 journées d'absence&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"1/2 journées d'absence\">";
//if (($aff_rang) and ($referent=="une_periode")) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Rang de l'élève&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"Rang de l'élève\">";
//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".htmlspecialchars("Régime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";
//=========================
if ($aff_date_naiss){
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Date de naissance")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";
	$ligne1_csv[] = "Date de naissance";
}
//=========================
if ($aff_reg) {
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Régime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";
	$ligne1_csv[]="Régime";
}
if ($aff_doub) {
	$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"doublant\" />";
	$ligne1_csv[]="Redoublant";
}
if ($aff_abs) {
	//$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"1/2 journées d'absence\" />";
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"1/2 journées d'absence\" />".
				"</a>";
	$ligne1_csv[]="1/2 journées d'absence";
}
if (($aff_rang) and ($referent=="une_periode")) {
	//$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'élève\" />";
	$ligne1[] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".(count($ligne1)+1)."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'élève\" />".
				"</a>";
	$ligne1_csv[]="Rang de l'élève";
}

// Etiquettes des quatre dernières lignes
if ($test_coef != 0) $col[1][0] = "Coefficient";
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
	//$total_points[$j+$ligne_supl] = 0;
	$total_coef_classe[$j+$ligne_supl] = 0;
	$total_coef_eleve[$j+$ligne_supl] = 0;

	$total_points_classe[$j+$ligne_supl] = 0;
	$total_points_eleve[$j+$ligne_supl] = 0;

	// =================================
	// MODIF: boireaus
	if ($affiche_categories) {
		//$total_cat_coef[$j+$ligne_supl] = array();
		//$total_cat_points[$j+$ligne_supl] = array();
		$total_cat_coef_classe[$j+$ligne_supl] = array();
		$total_cat_coef_eleve[$j+$ligne_supl] = array();

		$total_cat_points_classe[$j+$ligne_supl] = array();
		$total_cat_points_eleve[$j+$ligne_supl] = array();

		foreach ($categories as $cat_id) {
			//$total_cat_coef[$j+$ligne_supl][$cat_id] = 0;
			//$total_cat_points[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_coef_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_coef_eleve[$j+$ligne_supl][$cat_id] = 0;

			$total_cat_points_classe[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_points_eleve[$j+$ligne_supl][$cat_id] = 0;
		}
	}
	// =================================
	$j++;
}


//=============================
// AJOUT: boireaus
$chaine_matieres=array();
$chaine_moy_eleve1=array();
$chaine_moy_classe=array();
//$chaine_moy_classe="";
//=============================

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

	//echo "<p>Debut d'un tour de boucle<br />\$i=$i et \$lignes_groupes=$lignes_groupes<br />";

	foreach ($moyenne_annee_matiere as $tableau => $value) { unset($moyenne_annee_matiere[$tableau]);}

	$var_group_id = mysql_result($groupeinfo, $i, "id_groupe");
	$current_group = get_group($var_group_id);
	// Coeff pour la classe
	$current_coef = mysql_result($groupeinfo, $i, "coef");

	//echo $current_group['name']."<br />";

	if((!isset($current_group['visibilite']['cahier_notes']))||($current_group['visibilite']['cahier_notes']=='y')) {
		$nb_col++;
		$k++;
	
		//==============================
		// AJOUT: boireaus
		if ($referent == "une_periode") {
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
		else{
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
	
		$moy_classe_tmp = @mysql_result($call_moyenne, 0, "moyenne");
		//==============================
	
	
	
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
			$test_coef_personnalise = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if (mysqli_num_rows($test_coef_personnalise) > 0) {
				$coef_eleve = mysql_result($test_coef_personnalise, 0);
			} else {
				// Coefficient du groupe:
				$coef_eleve = $current_coef;
			}
			$coef_eleve=number_format($coef_eleve,1, ',', ' ');
	
	
			if ($referent == "une_periode") {
				if (!in_array($current_eleve_login[$j], $current_group["eleves"][$num_periode]["list"])) {
					$col[$k][$j+$ligne_supl] = "/";
				} else {
					//================================
					// MODIF: boireaus
					//$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
	
					$sql="SELECT cnc.note, cnc.statut FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
					ccn.id_groupe='".$current_group["id"]."' AND
					ccn.periode='$num_periode' AND
					cc.id_racine = ccn.id_cahier_notes AND
					cnc.id_conteneur=cc.id AND
					cc.id=cc.id_racine AND
					cnc.login='".$current_eleve_login[$j]."'";
					//echo "$sql";
					$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
					if(mysqli_num_rows($res_moy)>0) {
						$lig_moy=mysqli_fetch_object($res_moy);
						if($lig_moy->statut=='y') {
							if($lig_moy->note!="") {
								$col[$k][$j+$ligne_supl] = number_format($lig_moy->note,1, ',', ' ');
								$temp=$lig_moy->note;
								if ($current_coef > 0) {
									if ($affiche_categories) {
										if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
										//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
										//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
	
										$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
										$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$temp;
	
										$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
										$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
									}
									//$total_coef[$j+$ligne_supl] += $current_coef;
									//$total_points[$j+$ligne_supl] += $current_coef*$temp;
	
									$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
									$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$temp;
	
									$total_coef_classe[$j+$ligne_supl] += $current_coef;
									$total_points_classe[$j+$ligne_supl] += $current_coef*$temp;
	
									//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
									//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
								}
								/*
								if($chaine_matieres[$j+$ligne_supl]==""){
									$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
									$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
								}
								else{
									$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
									$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
								}
								*/
							}
							else{
								$col[$k][$j+$ligne_supl] = '-';
							}
						}
						else{
							$col[$k][$j+$ligne_supl] = '-';
						}
					}
					else {
						$col[$k][$j+$ligne_supl] = '-';
					}
	
					$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."' AND periode='$num_periode'";
					$test_eleve_grp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
	
			/*
				//$current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
				if ($current_eleve_statut != "") {
					$col[$k][$j+$ligne_supl] = $current_eleve_statut;
				} else {
					$temp = @mysql_result($current_eleve_note_query, 0, "note");
					if  ($temp != '')  {
					$col[$k][$j+$ligne_supl] = number_format($temp,1, ',', ' ');
					if ($current_coef > 0) {
						if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
						$total_coef[$j+$ligne_supl] += $current_coef;
						$total_points[$j+$ligne_supl] += $current_coef*$temp;
						$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
					}
					} else {
					$col[$k][$j+$ligne_supl] = '-';
					}
				}
			*/
					//================================
				}
			}
			else {
				// Année entière
				$p = "1";
				$moy = 0;
				$non_suivi = 2;
				$coef_moy = 0;
				while ($p < $nb_periode) {
					if (!in_array($current_eleve_login[$j], $current_group["eleves"][$p]["list"])) {
						$non_suivi = $non_suivi*2;
					} else {
						//================================
						// MODIF: boireaus
						//$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login[$j]' AND id_groupe='" . $current_group["id"] . "' AND periode='$p')");
	
						$sql="SELECT cnc.note, cnc.statut FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
						ccn.id_groupe='".$current_group["id"]."' AND
						ccn.periode='$p' AND
						cc.id_racine = ccn.id_cahier_notes AND
						cnc.id_conteneur=cc.id AND
						cc.id=cc.id_racine AND
						cnc.login='".$current_eleve_login[$j]."'";
						//echo "$sql";
						$res_moy=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
						if(mysqli_num_rows($res_moy)>0){
							$lig_moy=mysqli_fetch_object($res_moy);
							if($lig_moy->statut=='y'){
								if($lig_moy->note!=""){
									$moy += $lig_moy->note;
									$coef_moy++;
								}
							}
						}
	
				/*
						$current_eleve_statut = @mysql_result($current_eleve_note_query, 0, "statut");
						if ($current_eleve_statut == "") {
						$temp = @mysql_result($current_eleve_note_query, 0, "note");
						if  ($temp != '')  {
							$moy += $temp;
							$coef_moy++;
						}
						}
				*/
						//================================
					}
					$p++;
				}
	
	
				$moy_eleve_grp_courant_annee="-";
	
				if ($non_suivi == (pow(2,$nb_periode))) {
					// L'élève n'a suivi la matière sur aucune période
					$col[$k][$j+$ligne_supl] = "/";
				} else if ($coef_moy != 0) {
					// L'élève a au moins une note sur au moins une période
					$moy = $moy/$coef_moy;
					$moy_min = min($moy_min,$moy);
					$moy_max = max($moy_max,$moy);
					$col[$k][$j+$ligne_supl] = number_format($moy,1, ',', ' ');
	
					$moy_eleve_grp_courant_annee=$col[$k][$j+$ligne_supl];
	
					if ($current_coef > 0) {
						if($affiche_categories){
							if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
							//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
							//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
	
							$total_cat_coef_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve;
							$total_cat_points_eleve[$j+$ligne_supl][$prev_cat_id] += $coef_eleve*$moy;
	
							$total_cat_coef_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef;
							$total_cat_points_classe[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
						}
						//$total_coef[$j+$ligne_supl] += $current_coef;
						//$total_points[$j+$ligne_supl] += $current_coef*$moy;
	
						$total_coef_eleve[$j+$ligne_supl] += $coef_eleve;
						$total_points_eleve[$j+$ligne_supl] += $coef_eleve*$moy;
	
						$total_coef_classe[$j+$ligne_supl] += $current_coef;
						$total_points_classe[$j+$ligne_supl] += $current_coef*$moy;
						//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
					}
				} else {
					// Bien que suivant la matière, l'élève n'a aucune note à toutes les période (absent, pas de note, disp ...)
					$col[$k][$j+$ligne_supl] = "-";
				}
	
	
	
	
	
	
	
				$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."'";
				$test_eleve_grp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
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
			/*
			echo "\$current_eleve_login[$j]=".$current_eleve_login[$j]."<br />\n";
			echo "\$chaine_matieres[$j+$ligne_supl]=".$chaine_matieres[$j+$ligne_supl]."<br />\n";
			echo "\$chaine_moy_eleve1[$j+$ligne_supl]=".$chaine_moy_eleve1[$j+$ligne_supl]."<br /><br />\n";
			*/
			$j++;
		}
	
		//================================
		// MODIF: boireaus
		if ($referent == "une_periode") {
			//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
			//$call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT max(cnc.note) note_max FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_max = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	
			//$call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
			$sql="SELECT min(cnc.note) note_min FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			ccn.periode='$num_periode' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_min = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
		else {
			//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
			$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
			ccn.id_groupe='".$current_group["id"]."' AND
			cc.id_racine = ccn.id_cahier_notes AND
			cnc.id_conteneur=cc.id AND
			cc.id=cc.id_racine AND
			cnc.statut='y'";
			$call_moyenne = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		}
		//================================
	
		$temp = @mysql_result($call_moyenne, 0, "moyenne");
	
		//================================
		$col_csv=array();
/*
echo "\$temoin_graphe=$temoin_graphe<br />";
		if($temoin_graphe=="oui"){
echo "\$i=$i et \$lignes_groupes=$lignes_groupes<br />";
			if($i==$lignes_groupes-1){
				for($loop=0;$loop<$nb_lignes_tableau;$loop++){

echo "\$chaine_moy_eleve1[$loop+$ligne_supl]=".$chaine_moy_eleve1[$loop+$ligne_supl].'<br />';
					if(isset($chaine_moy_eleve1[$loop+$ligne_supl])) {
	
						$col_csv[1][$loop+$ligne_supl]=$col[1][$loop+$ligne_supl];
	
						$tmp_col=$col[1][$loop+$ligne_supl];
						//echo "\$current_eleve_login[$loop]=$current_eleve_login[$loop]<br />";
						$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe.php?".
						"temp1=".strtr($chaine_moy_eleve1[$loop+$ligne_supl],',','.').
						"&amp;temp2=".strtr($chaine_moy_classe[$loop+$ligne_supl],',','.').
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
*/
		// ===============================
		//========================================
	
		if ($test_coef != 0) {
			if ($current_coef > 0) {
				$col[$k][0] = number_format($current_coef,1, ',', ' ');
			}
			else {
				$col[$k][0] = "-";
			}
		}
		if ($temp != '') {
			//$col[$k][$nb_lignes_tableau+$ligne_supl] = $temp;
			$col[$k][$nb_lignes_tableau+$ligne_supl] = number_format($temp,1, ',', ' ');
		}
		else {
			$col[$k][$nb_lignes_tableau+$ligne_supl] = '-';
		}
		if ($referent == "une_periode") {
			$temp = @mysql_result($call_min, 0, "note_min");
			if($temp != ''){
				//$col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = number_format($temp,1, ',', ' ');
			}
			else{
				$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
			}
			$temp=@mysql_result($call_max, 0, "note_max");
			if ($temp != '') {
				//$col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = number_format($temp,1, ',', ' ');
			}
			else {
				$col[$k][$nb_lignes_tableau+2+$ligne_supl] = '-';
			}
		}
		else{
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
		$nom_complet_coupe = (mb_strlen($nom_complet_matiere) > 20)? urlencode(mb_substr($nom_complet_matiere,0,20)."...") : urlencode($nom_complet_matiere);
	
		$nom_complet_coupe_csv=(mb_strlen($nom_complet_matiere) > 20) ? mb_substr($nom_complet_matiere,0,20) : $nom_complet_matiere;
		$nom_complet_coupe_csv=preg_replace("/;/","",$nom_complet_coupe_csv);
	
		//$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
		//$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
		$ligne1[$k]="<a href='#' onclick=\"document.getElementById('col_tri').value='$k';";
		$ligne1[$k].="document.forms['formulaire_tri'].submit();\">";
		$ligne1[$k] .= "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
		$ligne1[$k].="</a>";
	
		$ligne1_csv[$k] = "$nom_complet_coupe_csv";
	}
	$i++;
}

//=====================================================================
// Liens vers les graphes sur les colonnes Nom_prenom:
if($temoin_graphe=="oui"){
	for($loop=0;$loop<$nb_lignes_tableau;$loop++){
		if(isset($chaine_moy_eleve1[$loop+$ligne_supl])) {

			$col_csv[1][$loop+$ligne_supl]=$col[1][$loop+$ligne_supl];

			$tmp_col=$col[1][$loop+$ligne_supl];
			//echo "\$current_eleve_login[$loop]=$current_eleve_login[$loop]<br />";
			$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe.php?".
			"temp1=".strtr($chaine_moy_eleve1[$loop+$ligne_supl],',','.').
			"&amp;temp2=".strtr($chaine_moy_classe[$loop+$ligne_supl],',','.').
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
//=====================================================================

// Dernière colonne des moyennes générales
if ($ligne_supl == 1) {
	// Les moyennes pour chaque catégorie
	//echo "\$displayed_categories=$displayed_categories<br />";
	foreach($displayed_categories as $cat_id) {
		$nb_col++;
		//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"".$cat_names[$cat_id]."\" />";

		$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"".$cat_names[$cat_id]."\" />".
				"</a>";

		$ligne1_csv[$nb_col] = "Moyenne : " . $cat_names[$cat_id];

		$j = '0';
		while($j < $nb_lignes_tableau) {
			//if ($total_cat_coef[$j+$ligne_supl][$cat_id] > 0) {
			if ($total_cat_coef_eleve[$j+$ligne_supl][$cat_id] > 0) {
				/*
				$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id],1, ',', ' ');
				$moy_cat_classe_point[$cat_id] +=$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id];
				$moy_cat_classe_effectif[$cat_id]++;
				$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
				*/

				//echo "\$moy_cat_classe_min[$cat_id]=$moy_cat_classe_min[$cat_id]<br />\n";
				//echo "\$moy_cat_classe_min[$cat_id]=\$total_cat_points[$j+$ligne_supl][$cat_id]/\$total_cat_coef[$j+$ligne_supl][$cat_id]=".$total_cat_points[$j+$ligne_supl][$cat_id]."/".$total_cat_coef[$j+$ligne_supl][$cat_id]."=".$moy_cat_classe_min[$cat_id]."<br /><br />\n";

				//$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);

				$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points_eleve[$j+$ligne_supl][$cat_id]/$total_cat_coef_eleve[$j+$ligne_supl][$cat_id],1, ',', ' ');
				$moy_cat_classe_point[$cat_id] +=$total_cat_points_classe[$j+$ligne_supl][$cat_id]/$total_cat_coef_classe[$j+$ligne_supl][$cat_id];
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

	// La moyenne générale
	$nb_col++;
	//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Moyenne générale&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
	//$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne générale\" />";

	$ligne1[$nb_col]="<a href='#' onclick=\"document.getElementById('col_tri').value='$nb_col';";
	if(preg_match("/^Rang/i",$ligne1[$nb_col])) {$ligne1[$nb_col].="document.getElementById('sens_tri').value='inverse';";}
	$ligne1[$nb_col].="document.forms['formulaire_tri'].submit();\">";
    $ligne1[$nb_col].="<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne générale\" />";
	$ligne1[$nb_col].="</a>";

	$ligne1_csv[$nb_col] = "Moyenne générale";

	$j = '0';
	while($j < $nb_lignes_tableau) {
		//if ($total_coef[$j+$ligne_supl] > 0) {
		if ($total_coef_eleve[$j+$ligne_supl] > 0) {
			/*
			$col[$nb_col][$j+$ligne_supl] = number_format($total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl],1, ',', ' ');
			$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
			$moy_classe_effectif++;
			$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
			$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
			*/
            $col[$nb_col][$j+$ligne_supl] = number_format($total_points_eleve[$j+$ligne_supl]/$total_coef_eleve[$j+$ligne_supl],1, ',', ' ');
            $moy_classe_point +=$total_points_classe[$j+$ligne_supl]/$total_coef_classe[$j+$ligne_supl];
            $moy_classe_effectif++;
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


	// Colonne Rang: Cas du choix d'une période: on calcule le rang d'après la moyenne générale de l'élève
	if (($aff_rang)&&(isset($ind_colonne_rang))) {
		// On va calculer les rangs
		//$ind_colonne_rang
		// Initialisation:
		$k=0;
		unset($tmp_tab);
		unset($rg);
		//while($k <= $nb_lignes_tableau) {
		while($k < $nb_lignes_tableau) {
			$rg[$k]=$k;

			//echo $col[1][$k+1].": ".$col[$nb_col][$k+1]." et total_coef:".$total_coef_eleve[$k+1]."<br />";
			//echo "\$col[$nb_col][$k+$ligne_supl]=\$col[$nb_col][".($k+$ligne_supl)."]=".$col[$nb_col][$k+$ligne_supl]."<br />";
			$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+$ligne_supl]);

			// Initialisation:
			$col[$ind_colonne_rang][$k]="-";
			$k++;
		}

		array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);

		$k=0;
		while($k < $nb_lignes_tableau) {
			if(isset($rg[$k])) {
				$col[$ind_colonne_rang][$rg[$k]+1]=$k+1;
			}
			$k++;
		}

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
		unset($rg);
		unset($tmp_tab);
		$k=0;
		unset($rg);
		while($k < $nb_lignes_tableau) {
			$rg[$k]=$k;

			if ($total_coef_eleve[$k+$ligne_supl] > 0) {
				$tmp_tab[$k]=preg_replace("/,/",".",$col[$nb_col][$k+1]);
				my_echo("<tr>");
				my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k]</td>");
				my_echo("</tr>");
			}
			else {
				$tmp_tab[$k]="?";
				my_echo("<tr>");
				my_echo("<td>".($k+1)."</td><td>".$col[1][$k+1]."</td><td>".$col[$nb_col][$k+1]."</td><td>$tmp_tab[$k] --</td>");
				my_echo("</tr>");
			}

			$k++;
		}
			my_echo("</table>");
		my_echo("</td>");

		if(isset($tmp_tab)) {
			array_multisort ($tmp_tab, SORT_DESC, SORT_NUMERIC, $rg, SORT_ASC, SORT_NUMERIC);
		}

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
        //$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Rang de l'élève\" />";

		$ligne1[$nb_col] = "<a href='#' onclick=\"document.getElementById('col_tri').value='".$nb_col."';".
				"document.getElementById('sens_tri').value='inverse';".
				"document.forms['formulaire_tri'].submit();\">".
				"<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'élève\" />".
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

	// Ajout d'une ligne de décalage si il y a une ligne de coeff
	if($col[1][0]=="Coefficient") {
		//$b_inf=1;
		//$b_sup=$nb_lignes_tableau+1;
		$corr=1;
	}
	else {
		//$b_inf=0;
		//$b_sup=$nb_lignes_tableau;
		$corr=0;
	}

	// Vérifier si $col_tri est bien un entier compris entre 0 et $nb_col ou $nb_col+1
	if((mb_strlen(preg_replace("/[0-9]/","",$col_tri))==0)&&($col_tri>0)&&($col_tri<=$nb_colonnes)) {
		my_echo("<table>");
		my_echo("<tr><td valign='top'>");
		unset($tmp_tab);
		unset($rg);
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
			$chaine_titre="Classe_".$classe."_Resultats_CN_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_CN_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
		}

		$now = gmdate('D, d M Y H:i:s') . ' GMT';

		$nom_fic=$chaine_titre."_".$now;

		// Filtrer les caractères dans le nom de fichier:
		$nom_fic=preg_replace("/[^a-zA-Z0-9_.-]/","",remplace_accents($nom_fic,'all'));
		$nom_fic.=".csv";

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
			$chaine_titre="Classe_".$classe."_Resultats_CN_".$nom_periode[$num_periode]."_Annee_scolaire_".getSettingValue("gepiYear");
		} else {
			$chaine_titre="Classe_".$classe."_Resultats_CN_Moyennes_annuelles_Annee_scolaire_".getSettingValue("gepiYear");
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

		// Une taille sans importance, histoire de tester
		$pdf->SetFont($fonte,'',12);
		// Recherche du plus long nom_prenom
		$texte_test[1]="Edmou Dugenou";
		$longueur_max_nom_prenom=0;
		$largeur_col[1]=$largeur_col_nom_ele;
		for($i=1;$i<=count($col_csv[1]);$i++) {
			/*
			// Le COL_CSV ne contient pas le nom prénom d'un élève qui n'a aucune note dans aucune matière
			echo "<pre>";
			print_r($col_csv);
			echo "</pre>";
			*/
			if(isset($col_csv[1][$i])) {
				$longueur_courante=$pdf->GetStringWidth($col_csv[1][$i]);
				if($longueur_courante>$longueur_max_nom_prenom) {
					$texte_test[1]=$col_csv[1][$i];
					$longueur_max_nom_prenom=$longueur_courante;
				}
			}
		}
		$taille_police_col[1]=ajuste_FontSize($texte_test[1], $largeur_col[1], 12, 'B', 3);

		for($i=2;$i<=count($ligne1_csv);$i++) {
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

		$h_cell=min(10, floor(($hauteur_page-$marge_haute-$marge_basse-$h_ligne_titre_page-$h_ligne_titre_tableau)/(count($col)-1)));

		/*
		$pdf->SetXY(10, 110);
		$pdf->Cell(190,10, $info_largeur_col_notes,'LRBT',2,'');
		*/

		$graisse='';
		$alignement='C';
		$bordure='LRBT';
		$h_ligne=$h_cell;

		$y2=$y2+$h_ligne_titre_tableau;
		$k=1;
		for($j=1;$j<count($col[1]);$j++) {
			$x2=$x0;

			// 20130328
			if($j>1) {
				//if($y2+$h_ligne<$hauteur_page-$marge_basse) {
				if($y2+$h_ligne*2<$hauteur_page-$marge_basse) {
					$y2+=$h_ligne;
				}
				else {
					$pdf->AddPage();
					$y2=$y0;
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
		$pdf->Output($nom_fic, $pref_output_mode_pdf);
		die();

	}
}

//**************** EN-TETE *****************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//=================================================
if($vtn_coloriser_resultats=='y') {
	//check_token();
	$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'vtn_%';";
	$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

	foreach($vtn_couleur_texte as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_texte$key', value='$value';";
		$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	}
	foreach($vtn_couleur_cellule as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_couleur_cellule$key', value='$value';";
		$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	}
	foreach($vtn_borne_couleur as $key => $value) {
		$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='vtn_borne_couleur$key', value='$value';";
		$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	}
}
//=================================================
$sql="DELETE FROM preferences WHERE name LIKE 'vtn_pref_%' AND login='".$_SESSION['login']."';";
$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

//$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_date_naiss', 'aff_rang');
$tab_pref=array('num_periode', 'larg_tab', 'bord', 'couleur_alterne', 'aff_abs', 'aff_reg', 'aff_doub', 'aff_rang');
for($loop=0;$loop<count($tab_pref);$loop++) {
	$tmp_var=$tab_pref[$loop];
	if($$tmp_var=='') {$$tmp_var="n";}
	$sql="INSERT INTO preferences SET name='vtn_pref_".$tmp_var."', value='".$$tmp_var."', login='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$_SESSION['vtn_pref_'.$tmp_var]=$$tmp_var;
}
$sql="INSERT INTO preferences SET name='vtn_pref_coloriser_resultats', value='$vtn_coloriser_resultats', login='".$_SESSION['login']."';";
$insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
$_SESSION['vtn_pref_coloriser_resultats']=$vtn_coloriser_resultats;
//=================================================

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
echo add_token_in_url();

echo "' target='_blank'>PDF</a>
</div>\n";

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
echo add_token_in_url();
echo "'>Export CSV</a>
</div>\n";

$la_date=date("d/m/Y H:i");

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

echo "<div>\n";

if ($referent == "une_periode") {
	//echo "<p class=bold>Classe : $classe - Résultats : $nom_periode[$num_periode] - Année scolaire : ".getSettingValue("gepiYear")."</p>";
	echo "<p class='bold'>Classe : $classe - Moyennes du carnet de notes du $nom_periode[$num_periode] - (<i>".$la_date."</i>) - ".getSettingValue("gepiYear")."</p>\n";
}
else {
	//echo "<p class=bold>Classe : $classe - Résultats : Moyennes annuelles - Année scolaire : ".getSettingValue("gepiYear")."</p>";
	echo "<p class='bold'>Classe : $classe - Moyennes annuelles du carnet de notes - (<i>".$la_date."</i>) - ".getSettingValue("gepiYear")."</p>\n";
}

//affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

function affiche_tableau2($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {
	// $col1_centre = 1 --> la première colonne est centrée
	// $col1_centre = 0 --> la première colonne est alignée à gauche
	// $col_centre = 1 --> toutes les autres colonnes sont centrées.
	// $col_centre = 0 --> toutes les autres colonnes sont alignées.
	// $couleur_alterne --> les couleurs de fond des lignes sont alternés
	global $num_debut_colonnes_matieres, $num_debut_lignes_eleves, $vtn_coloriser_resultats, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

	echo "<table summary=\"Moyennes des carnets de notes\" class='boireaus' border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\">\n";
	echo "<tr>\n";
	$j = 1;
	while($j < $nb_col+1) {
		//echo "<th class='small'>$ligne1[$j]</th>\n";
		echo "<th class='small' id='td_ligne1_$j'>$ligne1[$j]</th>\n";
		$j++;
	}
	echo "</tr>\n";
	$i = "0";
	$bg_color = "";
	$flag = "1";
	$alt=1;
	while($i < $nombre_lignes) {
        if((isset($couleur_alterne))&&($couleur_alterne=='y')) {
			if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
		}

	    $alt=$alt*(-1);
        echo "<tr class='";
        if((isset($couleur_alterne))&&($couleur_alterne=='y')) {echo "lig$alt ";}
		echo "white_hover'>\n";
		$j = 1;
		while($j < $nb_col+1) {
			if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
				echo "<td class='small' ";
				//echo $bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(mb_strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
			} else {
				echo "<td align=\"center\" class='small' ";
				//echo $bg_color;
				if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
					if(mb_strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
						for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
							if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
								echo " style='";
								if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
								if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
								echo "'";
								break;
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
			}
			$j++;
		}
		echo "</tr>\n";
		if ($flag == "1") $flag = "0"; else $flag = "1";
		$i++;
	}
	echo "</table>\n";
}

affiche_tableau2($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

if($vtn_coloriser_resultats=='y') {
	echo "<p class='bold'>Légende de la colorisation&nbsp;:</p>\n";
	echo $legende_colorisation;
}

echo "<div class='noprint'>\n";
echo "<p><b>Attention:</b> Les moyennes visualisées ici sont des photos à un instant t de ce qui a été saisi par les professeurs.<br />\n";
echo "Cela ne correspond pas nécessairement à ce qui apparaitra sur le bulletin après saisie d'autres résultats et ajustements éventuels des coefficients.</p>\n";
echo "</div>\n";

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
//=======================================================

require("../lib/footer.inc.php");
?>
