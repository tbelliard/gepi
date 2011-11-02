<?php

/*
 * $Id: edit_limite.inc.php 6822 2011-04-26 12:07:42Z crob $
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

//$affiche_deux_moy_gen=1;

for($loop=$periode1;$loop<=$periode2;$loop++) {
	$periode_num=$loop;
	include "../lib/calcul_moy_gen.inc.php";

	$tab_moy['periodes'][$periode_num]=array();
	$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
	$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
	$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
	//$tab_moy['periodes'][$periode_num]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
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
	//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
	if($loop==$periode1) {
		$tab_moy['current_group']=$current_group;                                     // [$j]
	}
	$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
	$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
	//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
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

/*
// De calcul_moy_gen.inc.php, on récupère en sortie:
//     - $moy_gen_eleve[$i]
//     - $moy_gen_eleve1[$i] idem avec les coef forcés à 1
//     - $moy_gen_classe[$i]
//     - $moy_gen_classe1[$i] idem avec les coef forcés à 1
//     - $moy_generale_classe
//     - $moy_max_classe
//     - $moy_min_classe

// A VERIFIER, mais s'il n'y a pas de coef spécifique pour un élève, on devrait avoir
//             $moy_gen_classe[$i] == $moy_generale_classe
// NON: Cela correspond à un mode de calcul qui ne retient que les matières suivies par l'élève pour calculer la moyenne générale
//      Le LATIN n'est pas compté dans cette moyenne générale si l'élève ne fait pas latin.
//      L'Allemand n'est pas comptabilisé si l'élève ne fait pas allemand
// FAIRE LE TOUR DES PAGES POUR VIRER TOUS CES $moy_gen_classe s'il en reste?

//     - $moy_cat_classe[$i][$cat]
//     - $moy_cat_eleve[$i][$cat]

//     - $moy_cat_min[$i][$cat] égale à $moy_min_categorie[$cat]
//     - $moy_cat_max[$i][$cat] égale à $moy_max_categorie[$cat]

// Là le positionnement au niveau moyenne générale:
//     - $quartile1_classe_gen
//       à
//     - $quartile6_classe_gen
//     - $place_eleve_classe[$i]

// On a récupéré en intermédiaire les
//     - $current_eleve_login[$i]
//     - $current_group[$j]
//     - $current_eleve_note[$j][$i]
//     - $current_eleve_statut[$j][$i]
//     - $current_coef[$j] (qui peut être différent du $coef_eleve pour une matière spécifique)
//     - $categories -> id
//     - $current_classe_matiere_moyenne[$j] (moyenne de la classe dans la matière)

// AJOUTé:
//     - $current_coef_eleve[$i][$j]
//     - $moy_min_classe_grp[$j]
//     - $moy_max_classe_grp[$j]
//     - $current_eleve_rang[$j][$i] sous réserve que $affiche_rang=='y'
//     - $quartile1_grp[$j] à $quartile6_grp[$j]
//     - $place_eleve_grp[$j][$i]
//     - $current_group_effectif_avec_note[$j] pour le nombre de "vraies" moyennes pour le rang (pas disp, abs,...)
//     - $tab_login_indice[LOGIN_ELEVE]=$i

//     $categories[] = $row["id"];
//     $tab_noms_categories[$row["id"]]=$row["nom_complet"];
//     $tab_id_categories[$row["nom_complet"]]=$row["id"];

*/

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
$res_ele= mysql_query($sql);
if(mysql_num_rows($res_ele)>0) {
	while($lig_ele=mysql_fetch_object($res_ele)) {
		$tab_moy['eleves'][]=$lig_ele->login;
		/*
		$tab_moy['ele'][$lig_ele->login]=array();
		$tab_moy['ele'][$lig_ele->login]['nom']=$lig_ele->nom;
		$tab_moy['ele'][$lig_ele->login]['prenom']=$lig_ele->prenom;
		$tab_moy['ele'][$lig_ele->login]['sexe']=$lig_ele->sexe;
		$tab_moy['ele'][$lig_ele->login]['naissance']=$lig_ele->naissance;
		$tab_moy['ele'][$lig_ele->login]['elenoet']=$lig_ele->elenoet;
		*/
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
//echo "\$login_eleve=$login_eleve<br />";
if ($choix_edit == '2') {
    //bulletin($login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
    //bulletin($login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);

    //bulletin_bis($tab_moy,$login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
    bulletin($tab_moy,$login_eleve,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,'y');
}

if ($choix_edit != '2') {
	// Si on arrive là, on n'est ni élève, ni responsable

	//if ($_SESSION['statut'] == "professeur" AND getSettingValue("GepiAccesMoyennesProfTousEleves") != "yes" AND getSettingValue("GepiAccesMoyennesProfToutesClasses") != "yes") {
	if ($_SESSION['statut'] == "professeur" AND
	getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") != "yes" AND
	getSettingValue("GepiAccesBulletinSimpleProfTousEleves") != "yes") {

		// On ne sélectionne que les élèves que le professeur a en cours
	    //if ($choix_edit == '1') {
	    if (($choix_edit == '1')||(!isset($login_prof))) {
			// On a alors $choix_edit==1 ou $choix_edit==4
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
			// On a alors $choix_edit==3 uniquement les élèves du professeur principal $login_prof
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

	    //if ($choix_edit == '1') {
	    if (($choix_edit == '1')||(!isset($login_prof))) {
			// On a alors $choix_edit==1 ou $choix_edit==4
	        $appel_liste_eleves = mysql_query("SELECT DISTINCT e.* " .
	        		"FROM eleves e, j_eleves_classes c " .
	        		"WHERE (" .
	        		"c.id_classe='$id_classe' AND " .
	        		"e.login = c.login" .
	        		") ORDER BY e.nom,e.prenom");
	    } else {
			// On a alors $choix_edit==3
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
	//bulletin_classe($nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);
	//bulletin_classe_bis($tab_moy,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);
	bulletin_classe($tab_moy,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$nb_coef_superieurs_a_zero,$affiche_categories);
	if ($choix_edit == '4') {
		require("../lib/footer.inc.php");
		die();
	}
	echo "<p class=saut>&nbsp;</p>\n";
	//=========================

    $i=0;
    $k=0;
    while ($i < $nombre_eleves) {
        $current_eleve_login = mysql_result($appel_liste_eleves, $i, "login");
        $k++;
        //bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
        //bulletin($current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
        //bulletin_bis($tab_moy,$current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories);
        bulletin($tab_moy,$current_eleve_login,$k,$nombre_eleves,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,'y');
        if ($i != $nombre_eleves-1) {echo "<p class=saut>&nbsp;</p>";}
        $i++;
    }

}

?>