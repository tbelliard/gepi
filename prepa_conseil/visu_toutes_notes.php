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
//Initialisation
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] :  NULL;
$num_periode = isset($_POST['num_periode']) ? $_POST['num_periode'] :  NULL;
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


$larg_tab = isset($_POST['larg_tab']) ? $_POST['larg_tab'] :  NULL;
$bord = isset($_POST['bord']) ? $_POST['bord'] :  NULL;
$aff_abs = isset($_POST['aff_abs']) ? $_POST['aff_abs'] :  NULL;
$aff_reg = isset($_POST['aff_reg']) ? $_POST['aff_reg'] :  NULL;
$aff_doub = isset($_POST['aff_doub']) ? $_POST['aff_doub'] :  NULL;
$aff_rang = isset($_POST['aff_rang']) ? $_POST['aff_rang'] :  NULL;

//============================
$aff_date_naiss = isset($_POST['aff_date_naiss']) ? $_POST['aff_date_naiss'] :  NULL;
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
//=========================
if ($aff_date_naiss){$ligne1[] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Date de naissance")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" ALT=\"régime\" />";}
//=========================
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
	// Coeff pour la classe
    $current_coef = mysql_result($groupeinfo, $i, "coef");
	// A FAIRE: A l'affichage, il faudrait mettre 1.0(*) quand le coeff n'est pas 1.0 pour tous les élèves à cause de coeffs personnalisés.


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
		$coef_eleve=number_format($coef_eleve,1, ',', ' ');


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
						}
						$coef_eleve=number_format($coef_eleve,1, ',', ' ');

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
        $col[$k][$nb_lignes_tableau+$ligne_supl] = $temp;
    } else {
        $col[$k][$nb_lignes_tableau+$ligne_supl] = '-';
    }
    if ($referent == "une_periode") {
        $temp = @mysql_result($call_min, 0, "note_min");
        if ($temp != '') {
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = $temp;
        } else {
            $col[$k][$nb_lignes_tableau+1+$ligne_supl] = '-';
        }
        $temp = @mysql_result($call_max, 0, "note_max");
        if ($temp != '') {
            $col[$k][$nb_lignes_tableau+2+$ligne_supl] = $temp;
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
    //$ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=$nom_complet_coupe&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
    $ligne1[$k] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("$nom_complet_coupe")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"$nom_complet_coupe\" />";
    $i++;
}
// Dernière colonne des moyennes générales
if ($ligne_supl == 1) {
    // Les moyennes pour chaque catégorie
    if ($affiche_categories) {
		foreach($displayed_categories as $cat_id) {
			$nb_col++;
			$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne : " . $cat_names[$cat_id])."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"".$cat_names[$cat_id]."\" />";
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
    //$ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=Moyenne générale&width=22\" WIDTH=\"22\" BORDER=\"0\" />";
    $ligne1[$nb_col] = "<IMG SRC=\"../lib/create_im_mat.php?texte=".rawurlencode("Moyenne générale")."&amp;width=22\" WIDTH=\"22\" BORDER=\"0\" alt=\"Moyenne générale\" />";
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
}

$nb_lignes_tableau = $nb_lignes_tableau + 3 + $ligne_supl;

//**************** EN-TETE *****************
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
$classe = sql_query1("SELECT classe FROM classes WHERE id = '$id_classe'");

if ($referent == "une_periode") {
    echo "<p class=bold>Classe : $classe - Résultats : $nom_periode[$num_periode] - Année scolaire : ".getSettingValue("gepiYear")."</p>";
} else {
    echo "<p class=bold>Classe : $classe - Résultats : Moyennes annuelles - Année scolaire : ".getSettingValue("gepiYear")."</p>";
}

//echo "\$affiche_categories=$affiche_categories<br />";

affiche_tableau($nb_lignes_tableau, $nb_col, $ligne1, $col, $larg_tab, $bord,0,1,$couleur_alterne);
require("../lib/footer.inc.php");
?>