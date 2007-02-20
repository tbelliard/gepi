<?php
/*
 * Last modification  : 12/11/2006
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



// INSERT INTO `droits` VALUES ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


//Initialisation
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
if ($num_periode=="annee") {
    $referent="annee";
} else {
    $referent="une_periode";
}
$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;
$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;
$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] :  NULL;
$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] :  NULL;
$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] :  NULL;
$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] :  NULL;
$couleur_alterne = isset($_POST['couleur_alterne']) ? $_POST['couleur_alterne'] :  NULL;

//================================
// MODIF: boireaus
if(file_exists("../visualisation/draw_graphe.php")){
	$temoin_graphe="oui";
}
else{
	$temoin_graphe="non";
}
//================================

include "../lib/periodes.inc.php";

if ($referent=="une_periode")
	// Calcul sur une seule période
	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login AND j.periode='$num_periode') ORDER BY nom,prenom");
else {
	// Calcul sur l'année
	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.* FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login) ORDER BY nom,prenom");
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
$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
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
	$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef, jgc.categorie_id ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
	"WHERE ( " .
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe='".$id_classe."' AND " .
	"jgm.id_groupe=jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet");
} else {
	$groupeinfo = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef
	FROM j_groupes_classes jgc, j_groupes_matieres jgm
	WHERE (
	jgc.id_classe='".$id_classe."' AND
	jgm.id_groupe=jgc.id_groupe
	)
	ORDER BY jgc.priorite,jgm.id_matiere");
}

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
		$ind++;
	}

	$j++;
}
// Etiquettes des premières colonnes
$ligne1[1] = "Nom ";
//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Régime&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"régime\">";
//if ($aff_doub) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"doublant\">";
//if ($aff_abs) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=1/2 journées d'absence&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"1/2 journées d'absence\">";
//if (($aff_rang) and ($referent=="une_periode")) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Rang de l'élève&width=22\" WIDTH=\"22\" BORDER=0 ALT=\"Rang de l'élève\">";
//if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".htmlentities("Régime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";
if ($aff_reg) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Régime")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";
if ($aff_doub) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Redoublant&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"doublant\" />";
if ($aff_abs) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("1/2 journées d'absence")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"1/2 journées d'absence\" />";
if (($aff_rang) and ($referent=="une_periode")) $ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Rang de l'élève")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"Rang de l'élève\" />";


// Etiquettes des quatre dernières lignes
if ($test_coef != 0) $col[1][0] = "Coefficient";
$col[1][$nb_lignes_tableau+$ligne_supl] = "Moyenne";
$col[1][$nb_lignes_tableau+1+$ligne_supl] = "Min";
$col[1][$nb_lignes_tableau+2+$ligne_supl] = "Max";
$ind = 2;
$nb_col = 1;
$k= 1;

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
	$total_coef[$j+$ligne_supl] = 0;
	$total_points[$j+$ligne_supl] = 0;
	// =================================
	// MODIF: boireaus
	if ($affiche_categories) {
		$total_cat_coef[$j+$ligne_supl] = array();
		$total_cat_points[$j+$ligne_supl] = array();
		foreach ($categories as $cat_id) {
			$total_cat_coef[$j+$ligne_supl][$cat_id] = 0;
			$total_cat_points[$j+$ligne_supl][$cat_id] = 0;
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
while($i < $lignes_groupes){
	$moy_max = -1;
	$moy_min = 21;

	$nb_col++;
	$k++;

	foreach ($moyenne_annee_matiere as $tableau => $value)  unset($moyenne_annee_matiere[$tableau]);

	$var_group_id = mysql_result($groupeinfo, $i, "id_groupe");
	$current_group = get_group($var_group_id);
	$current_coef = mysql_result($groupeinfo, $i, "coef");


	//==============================
	// AJOUT: boireaus
	if ($referent == "une_periode") {
		$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
		ccn.id_groupe='".$current_group["id"]."' AND
		ccn.periode='$num_periode' AND
		cc.id_racine = ccn.id_cahier_notes AND
		cnc.id_conteneur=cc.id AND
		cnc.statut='y'";
		$call_moyenne = mysql_query($sql);
	}
	else{
		$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
		ccn.id_groupe='".$current_group["id"]."' AND
		cc.id_racine = ccn.id_cahier_notes AND
		cnc.id_conteneur=cc.id AND
		cnc.statut='y'";
		$call_moyenne = mysql_query($sql);
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
				cnc.login='".$current_eleve_login[$j]."'";
				//echo "$sql";
				$res_moy=mysql_query($sql);

				if(mysql_num_rows($res_moy)>0){
					$lig_moy=mysql_fetch_object($res_moy);
					if($lig_moy->statut=='y'){
						if($lig_moy->note!=""){
							$col[$k][$j+$ligne_supl] = number_format($lig_moy->note,1, ',', ' ');
							$temp=$lig_moy->note;
							if ($current_coef > 0) {
								if ($affiche_categories) {
									if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
									$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
									$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$temp;
								}
								$total_coef[$j+$ligne_supl] += $current_coef;
								$total_points[$j+$ligne_supl] += $current_coef*$temp;
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
				else{
					$col[$k][$j+$ligne_supl] = '-';
				}

				$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."' AND periode='$num_periode'";
				$test_eleve_grp=mysql_query($sql);
				if(mysql_num_rows($test_eleve_grp)>0){
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
					cnc.login='".$current_eleve_login[$j]."'";
					//echo "$sql";
					$res_moy=mysql_query($sql);

					if(mysql_num_rows($res_moy)>0){
						$lig_moy=mysql_fetch_object($res_moy);
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
					if($affiche_categories){
						if (!in_array($prev_cat_id, $displayed_categories)) $displayed_categories[] = $prev_cat_id;
						$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
						$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
					}
					$total_coef[$j+$ligne_supl] += $current_coef;
					$total_points[$j+$ligne_supl] += $current_coef*$moy;
					//$total_cat_coef[$j+$ligne_supl][$prev_cat_id] += $current_coef;
					//$total_cat_points[$j+$ligne_supl][$prev_cat_id] += $current_coef*$moy;
				}
			} else {
				// Bien que suivant la matière, l'élève n'a aucune note à toutes les période (absent, pas de note, disp ...)
				$col[$k][$j+$ligne_supl] = "-";
			}







			$sql="SELECT * FROM j_eleves_groupes WHERE id_groupe='".$current_group["id"]."'";
			$test_eleve_grp=mysql_query($sql);
			if(mysql_num_rows($test_eleve_grp)>0){
				//if($chaine_matieres[$j+$ligne_supl]==""){
				if(!isset($chaine_matieres[$j+$ligne_supl])){
					$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
					$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
					$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
				}
				else{
					if($chaine_matieres[$j+$ligne_supl]==""){
						$chaine_matieres[$j+$ligne_supl]=$current_group["matiere"]["matiere"];
						$chaine_moy_eleve1[$j+$ligne_supl]=$lig_moy->note;
						$chaine_moy_classe[$j+$ligne_supl]=$moy_classe_tmp;
					}
					else{
						$chaine_matieres[$j+$ligne_supl].="|".$current_group["matiere"]["matiere"];
						$chaine_moy_eleve1[$j+$ligne_supl].="|".$lig_moy->note;
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
		cnc.statut='y'";
		$call_moyenne = mysql_query($sql);

		//$call_max = mysql_query("SELECT max(note) note_max FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
		$sql="SELECT max(cnc.note) note_max FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
		ccn.id_groupe='".$current_group["id"]."' AND
		ccn.periode='$num_periode' AND
		cc.id_racine = ccn.id_cahier_notes AND
		cnc.id_conteneur=cc.id AND
		cnc.statut='y'";
		$call_max = mysql_query($sql);

		//$call_min = mysql_query("SELECT min(note) note_min FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$num_periode')");
		$sql="SELECT min(cnc.note) note_min FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
		ccn.id_groupe='".$current_group["id"]."' AND
		ccn.periode='$num_periode' AND
		cc.id_racine = ccn.id_cahier_notes AND
		cnc.id_conteneur=cc.id AND
		cnc.statut='y'";
		$call_min = mysql_query($sql);
	}
	else {
		//$call_moyenne = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "')");
		$sql="SELECT round(avg(cnc.note),1) moyenne FROM cn_cahier_notes ccn, cn_conteneurs cc, cn_notes_conteneurs cnc WHERE
		ccn.id_groupe='".$current_group["id"]."' AND
		cc.id_racine = ccn.id_cahier_notes AND
		cnc.id_conteneur=cc.id AND
		cnc.statut='y'";
		$call_moyenne = mysql_query($sql);
	}
	//================================

	$temp = @mysql_result($call_moyenne, 0, "moyenne");

/*
	//========================================
	// AJOUT: boireaus
	if($chaine_moy_classe==""){
		$chaine_moy_classe=$temp;
	}
	else{
		$chaine_moy_classe.="|".$temp;
	}
*/
	//================================
	// AJOUT: boireaus
	if($temoin_graphe=="oui"){
		if($i==$lignes_groupes-1){
			for($loop=0;$loop<$nb_lignes_tableau;$loop++){
				//$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe?".rawurlencode($chaine_matieres[$loop+$ligne_supl])."'>".$col[1][$loop+$ligne_supl]."</a>";

				//$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe?".$chaine_matieres[$loop+$ligne_supl]."'>".$col[1][$loop+$ligne_supl]."</a>";
				//$col[1][$loop+$ligne_supl]="<a href='draw_graphe.php?".

				$tmp_col=$col[1][$loop+$ligne_supl];
				$col[1][$loop+$ligne_supl]="<a href='../visualisation/draw_graphe.php?".
				"temp1=".$chaine_moy_eleve1[$loop+$ligne_supl].
				"&amp;temp2=".$chaine_moy_classe[$loop+$ligne_supl].
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
			//echo "\$chaine_moy_classe=".$chaine_moy_classe."<br /><br />\n";
		}
	}
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
		$col[$k][$nb_lignes_tableau+$ligne_supl] = $temp;
	}
	else {
		$col[$k][$nb_lignes_tableau+$ligne_supl] = '-';
	}
	if ($referent == "une_periode") {
		$temp = @mysql_result($call_min, 0, "note_min");
		if($temp != ''){
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
		}
		else{
			$col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
		}
		$temp=@mysql_result($call_max, 0, "note_max");
		if ($temp != '') {
			$col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
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
	$nom_complet_coupe = (strlen($nom_complet_matiere) > 20)? urlencode(substr($nom_complet_matiere,0,20)."...") : urlencode($nom_complet_matiere);
	//$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
	$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
	$i++;
}
// Dernière colonne des moyennes générales
if ($ligne_supl == 1) {
	// Les moyennes pour chaque catégorie
	//echo "\$displayed_categories=$displayed_categories<br />";
	foreach($displayed_categories as $cat_id) {
		$nb_col++;
		$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"".$cat_names[$cat_id]."\" />";
		$j = '0';
		while($j < $nb_lignes_tableau) {
			if ($total_cat_coef[$j+$ligne_supl][$cat_id] > 0) {
				$col[$nb_col][$j+$ligne_supl] = number_format($total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id],1, ',', ' ');
				$moy_cat_classe_point[$cat_id] +=$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id];
				$moy_cat_classe_effectif[$cat_id]++;
				$moy_cat_classe_min[$cat_id] = min($moy_cat_classe_min[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
				//echo "\$moy_cat_classe_min[$cat_id]=$moy_cat_classe_min[$cat_id]<br />\n";
				//echo "\$moy_cat_classe_min[$cat_id]=\$total_cat_points[$j+$ligne_supl][$cat_id]/\$total_cat_coef[$j+$ligne_supl][$cat_id]=".$total_cat_points[$j+$ligne_supl][$cat_id]."/".$total_cat_coef[$j+$ligne_supl][$cat_id]."=".$moy_cat_classe_min[$cat_id]."<br /><br />\n";
				$moy_cat_classe_max[$cat_id] = max($moy_cat_classe_max[$cat_id],$total_cat_points[$j+$ligne_supl][$cat_id]/$total_cat_coef[$j+$ligne_supl][$cat_id]);
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
	$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne générale\" />";
	$j = '0';
	while($j < $nb_lignes_tableau) {
		if ($total_coef[$j+$ligne_supl] > 0) {
			$col[$nb_col][$j+$ligne_supl] = number_format($total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl],1, ',', ' ');
			$moy_classe_point +=$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl];
			$moy_classe_effectif++;
			$moy_classe_min = min($moy_classe_min,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
			$moy_classe_max = max($moy_classe_max,$total_points[$j+$ligne_supl]/$total_coef[$j+$ligne_supl]);
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
}

$nb_lignes_tableau = $nb_lignes_tableau + 3 + $ligne_supl;

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

if ($referent == "une_periode") {
	echo "<p class=bold>Classe : $classe - Résultats : $nom_periode[$num_periode] - Année scolaire : ".getSettingValue("gepiYear")."</p>";
}
else {
	echo "<p class=bold>Classe : $classe - Résultats : Moyennes annuelles - Année scolaire : ".getSettingValue("gepiYear")."</p>";
}

//affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);

function affiche_tableau2($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {
	// $col1_centre = 1 --> la première colonne est centrée
	// $col1_centre = 0 --> la première colonne est alignée à gauche
	// $col_centre = 1 --> toutes les autres colonnes sont centrées.
	// $col_centre = 0 --> toutes les autres colonnes sont alignées.
	// $couleur_alterne --> les couleurs de fond des lignes sont alternés

	echo "<table border=\"$bord\" cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\">\n";
	echo "<tr>\n";
	$j = 1;
	while($j < $nb_col+1) {
		echo "<th class='small'>$ligne1[$j]</th>\n";
		$j++;
	}
	echo "</tr>\n";
	$i = "0";
	$bg_color = "";
	$flag = "1";
	while($i < $nombre_lignes) {
		if ($couleur_alterne) {
			if ($flag==1) $bg_color = "bgcolor=\"#C0C0C0\""; else $bg_color = "     " ;
		}

		echo "<tr>\n";
		$j = 1;
		while($j < $nb_col+1) {
			if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))){
				echo "<td class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
			} else {
				echo "<td align=\"center\" class='small' ".$bg_color.">{$col[$j][$i]}</td>\n";
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

?>
</body>
</html>