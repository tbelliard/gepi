<?php
/*
* $Id: bulletin_simple_classe.inc.php 6149 2010-12-16 10:31:02Z crob $
*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*/

//function bulletin_classe_bis($tab_moy,$total,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$test_coef,$affiche_categories) {
function bulletin_classe($tab_moy,$total,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$test_coef,$affiche_categories) {
global $nb_notes,$nombre_eleves,$type_etablissement,$type_etablissement2;

//global $avec_rapport_effectif;
$avec_rapport_effectif="y";

// données requises :
//- $total : nombre total d'élèves
//- $periode1 : numéro de la première période à afficher
//- $periode2 : numéro de la dernière période à afficher
//- $nom_periode : tableau des noms de période
//- $gepiYear : année
//- $id_classe : identifiant de la classe.

//====================
// Ajout: boireaus 20070626
$affiche_coef=sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."'");
//echo "\$affiche_coef=$affiche_coef<br />\n";
//====================

//=========================
// AJOUT: boireaus 20080316
//global $tab_moy_gen;
//global $tab_moy_cat_classe;
global $display_moy_gen;
//=========================

global $affiche_deux_moy_gen;

//echo "\$affiche_categories=$affiche_categories<br />";
if(!getSettingValue("bull_intitule_app")){
	$bull_intitule_app="Appréciations/Conseils";
}
else{
	$bull_intitule_app=getSettingValue("bull_intitule_app");
}

$nb_periodes = $periode2 - $periode1 + 1;

//============================
// Liste des profs principaux:
$data_profsuivi = mysql_query("SELECT DISTINCT u.login FROM utilisateurs u, j_eleves_professeurs j WHERE (j.professeur = u.login AND j.id_classe='$id_classe') ");
$current_profsuivi_login=array();
if(mysql_num_rows($data_profsuivi)>0){
	while($lig_profsuivi=mysql_fetch_object($data_profsuivi)) {
		$current_profsuivi_login[] = $lig_profsuivi->login;
	}
}
//============================


$call_classe = mysql_query("SELECT * FROM classes WHERE id='$id_classe'");
$classe = mysql_result($call_classe, 0, "classe");

//-------------------------------
// On affiche l'en-tête : Les données de la classe
//-------------------------------
echo "<span class='bull_simpl'><span class='bold'>Classe de $classe</span>";
echo ", année scolaire $gepiYear<br />\n";

if ($periode1 < $periode2) {
	echo "Résultats de : ";
	$nb = $periode1;
	while ($nb < $periode2+1) {
	echo $nom_periode[$nb];
	if ($nb < $periode2) echo " - ";
	$nb++;
	}
	echo ".</span>";
} else {
	$temp = strtolower($nom_periode[$periode1]);
	echo "Résultats du $temp.</span>";

}
//
//-------------------------------
// Fin de l'en-tête

//echo "\$test_coef=$test_coef<br />";

// On initialise le tableau :

$larg_tab = 680;
$larg_col1 = 120;
$larg_col2 = 38;
$larg_col3 = 38;
$larg_col4 = 20;
$larg_col5 = $larg_tab - $larg_col1 - $larg_col2 - $larg_col3 - $larg_col4;
//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
echo "<table width='$larg_tab' class='boireaus' cellspacing='1' cellpadding='1' summary=''>\n";
echo "<tr><td width=\"$larg_col1\" class='bull_simpl'>$total élèves";
echo "</td>\n";

//====================
// Modif: boireaus 20070626
if($affiche_coef=='y'){
	if ($test_coef != 0) echo "<td width=\"$larg_col2\" align=\"center\"><p class='bull_simpl'>Coef.</p></td>\n";
}
//====================

if($avec_rapport_effectif=="y") {
	echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>Effectif</td>\n";
}

echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>Classe</td>\n";
//echo "<td width=\"$larg_col3\" align=\"center\" class='bull_simpl'>&Eacute;lève</td>\n";
/*
if ($affiche_rang=='y') {
	echo "<td width=$larg_col4 align=\"center\" class='bull_simpl'><i>Rang</i></td>\n";
}
*/
echo "<td width=\"$larg_col5\" class='bull_simpl'>$bull_intitule_app</td></tr>\n";
//echo "</table>";
/*
// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
$call_data = mysql_query("SELECT * FROM aid_config WHERE order_display1 = 'b' ORDER BY order_display2");
$nb_aid = mysql_num_rows($call_data);
$z=0;
while ($z < $nb_aid) {
	$display_begin = mysql_result($call_data, $z, "display_begin");
	$display_end = mysql_result($call_data, $z, "display_end");
	if (($periode1 >= $display_begin) and ($periode2 <= $display_end)) {
		$indice_aid = @mysql_result($call_data, $z, "indice_aid");
		$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='$current_eleve_login' and indice_aid='$indice_aid')");
		$aid_id = @mysql_result($aid_query, 0, "id_aid");
		if ($aid_id != '') {
			affiche_aid_simple($affiche_rang, $test_coef,$indice_aid,$aid_id,$current_eleve_login,$periode1,$periode2,$id_classe, 'bull_simpl');
		}
	}
	$z++;
}
*/
//------------------------------
// Boucle 'groupes'
//------------------------------

/*
if ($affiche_categories) {
	// On utilise les valeurs spécifiées pour la classe en question
	$sql="SELECT DISTINCT jgc.id_groupe ".
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
	"WHERE ( " .
	"jgc.categorie_id = jmcc.categorie_id AND " .
	"jgc.id_classe = '".$id_classe."' AND " .
	"jgm.id_groupe = jgc.id_groupe AND " .
	"m.matiere = jgm.id_matiere" .
	") " .
	"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
	//echo $sql."<br />\n";
	$appel_liste_groupes = mysql_query($sql);

} else {
	$appel_liste_groupes = mysql_query("SELECT DISTINCT jgc.id_groupe, jgc.coef " .
	"FROM j_groupes_classes jgc, j_groupes_matieres jgm " .
	"WHERE ( " .
	"jgc.id_classe = '".$id_classe."' AND " .
	"jgm.id_groupe = jgc.id_groupe" .
	") " .
	"ORDER BY jgc.priorite,jgm.id_matiere");
}

// La ligne suivante a été remplacée par les requêtes intégrant le classement par catégories de matières
// $appel_liste_groupes = mysql_query("SELECT DISTINCT jeg.id_groupe id_groupe FROM j_eleves_groupes jeg, j_groupes_classes jgc WHERE (jeg.login = '" . $current_eleve_login . "' AND jeg.id_groupe = jgc.id_groupe AND jgc.id_classe = '" . $id_classe . "') ORDER BY jgc.priorite");
$nombre_groupes = mysql_num_rows($appel_liste_groupes);
*/

// Récupération des noms de catgories
$get_cat = mysql_query("SELECT id FROM matieres_categories");
$categories = array();
while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
	$categories[] = $row["id"];
}

$cat_names = array();
foreach ($categories as $cat_id) {
	$cat_names[$cat_id] = html_entity_decode_all_version(mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0));
}

// Nombre de groupes sur la classe
$nombre_groupes=count($tab_moy['current_group']);

/*
//$total_cat_eleve = array();
$total_cat_classe = array();

//===========================
// MODIF: boireaus 20070627
//$total_cat_coef = array();
//$total_cat_coef_eleve = array();
$total_cat_coef_classe = array();
//===========================

//===========================
// AJOUT: boireaus 20070627
//$total_coef_eleve=array();
$total_coef_classe=array();
//===========================

$nb=$periode1;
while ($nb < $periode2+1) {
	$total_points_classe[$nb] = 0;
	//$total_points_eleve[$nb] = 0;

	//===========================
	// MODIF: boireaus 20070627
	//$total_coef[$nb] = 0;
	//$total_coef_eleve[$nb] = 0;
	$total_coef_classe[$nb] = 0;
	//===========================

	//$total_cat_eleve[$nb] = array();
	$total_cat_classe[$nb] = array();
	$total_cat_coef[$nb] = array();
	foreach($categories as $cat_id) {
		//$total_cat_eleve[$nb][$cat_id] = 0;
		$total_cat_classe[$nb][$cat_id] = 0;

		//===========================
		// MODIF: boireaus 20070627
		//$total_cat_coef[$nb][$cat_id] = 0;
		//$total_cat_coef_eleve[$nb][$cat_id] = 0;
		$total_cat_coef_classe[$nb][$cat_id] = 0;
		//===========================
	}
	//echo "\$nb=$nb<br />\n";
	$nb++;
}
$j = 0;
*/

$prev_cat_id = null;

//while ($j < $nombre_groupes) {
for($j=0;$j<$nombre_groupes;$j++) {

		//echo "<table width=$larg_tab border=1 cellspacing=0 cellpadding=1 style='margin-bottom: 0px; border-bottom: 1px solid black; border-top: none;'>";

	$inser_ligne='no';

	//$group_id = mysql_result($appel_liste_groupes, $j, "id_groupe");
	//$current_group = get_group($group_id);

	// On récupère le groupe depuis $tab_moy
	$current_group=$tab_moy['current_group'][$j];

	// Coefficient pour le groupe
	$current_coef = $current_group["classes"]["classes"][$id_classe]["coef"];

	$current_matiere_professeur_login = $current_group["profs"]["list"];

	//$current_matiere_nom_complet = $current_group["matiere"]["nom_complet"];
	if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
		$current_matiere_nom_complet = $current_group["name"];
	}
	elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
		$current_matiere_nom_complet = $current_group["description"];
	}
	else {
		$current_matiere_nom_complet = $current_group["matiere"]["nom_complet"];
	}


	//echo "\$current_matiere_nom_complet=$current_matiere_nom_complet<br />\n";
	$nb=$periode1;
	while ($nb < $periode2+1) {
		//$current_classe_matiere_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$nb')");
		//$current_classe_matiere_moyenne[$nb] = mysql_result($current_classe_matiere_moyenne_query, 0, "moyenne");
		$current_classe_matiere_moyenne[$nb]=$tab_moy['periodes'][$nb]['current_classe_matiere_moyenne'][$j];


		//$test_current_classe_matiere_moyenne_query = mysql_query("SELECT 1=1 FROM matieres_notes WHERE (statut ='' AND id_groupe='" . $current_group["id"] . "' AND periode='$nb')");
		//$nb_moyennes_current_classe_matiere[$nb]=mysql_num_rows($test_current_classe_matiere_moyenne_query);
		//if($nb_moyennes_current_classe_matiere[$nb]==0) {$current_classe_matiere_moyenne[$nb]='-';}


		// On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichées
		$test_cn = mysql_query("select c.nom_court, c.id from cn_cahier_notes cn, cn_conteneurs c
		where (cn.periode = '$nb' and cn.id_groupe='".$current_group["id"]."' and cn.id_cahier_notes = c.id_racine and c.id_racine!=c.id and c.display_bulletin = 1) ");
		$nb_ligne_cn[$nb] = mysql_num_rows($test_cn);
		$n = 0;
		while ($n < $nb_ligne_cn[$nb]) {
			$cn_id[$nb][$n] = mysql_result($test_cn, $n, 'c.id');
			$cn_nom[$nb][$n] = mysql_result($test_cn, $n, 'c.nom_court');
			$n++;
		}

		//echo "\$nb=$nb<br />\n";
		$nb++;

	}


	$nb=$periode1;
	while ($nb < $periode2+1) {
		$current_grp_appreciation_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"] . "' AND periode='$nb')");
		$current_grp_appreciation[$nb] = @mysql_result($current_grp_appreciation_query, 0, "appreciation");
		//echo "\$current_grp_appreciation[$nb]=$current_grp_appreciation[$nb]<br />\n";
		$nb++;
	}


	/*
	// Maintenant on regarde si l'élève suit bien cette matière ou pas
	//-----------------------------
	$nb=$periode1;
	while ($nb < $periode2+1) {
		if (in_array($current_eleve_login, $current_group["eleves"][$nb]["list"])) {
			$inser_ligne='yes';
			$current_eleve_note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$current_eleve_login' AND periode='$nb' AND id_groupe='" . $current_group["id"] . "')");
			$current_eleve_note[$nb] = @mysql_result($current_eleve_note_query, 0, "note");
			$current_eleve_statut[$nb] = @mysql_result($current_eleve_note_query, 0, "statut");
			$current_eleve_appreciation_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$current_eleve_login' AND id_groupe='" . $current_group["id"] . "' AND periode='$nb')");
			$current_eleve_appreciation[$nb] = @mysql_result($current_eleve_appreciation_query, 0, "appreciation");

			// Coefficient personnalisé pour l'élève?
			$sql="SELECT value FROM eleves_groupes_settings WHERE (" .
					"login = '".$current_eleve_login."' AND " .
					"id_groupe = '".$group_id."' AND " .
					"name = 'coef')";
			$test_coef_personnalise = mysql_query($sql);
			if (mysql_num_rows($test_coef_personnalise) > 0) {
				$coef_eleve = mysql_result($test_coef_personnalise, 0);
			} else {
				// Coefficient du groupe:
				$coef_eleve = $current_coef;
			}
			//=========================
			// MODIF: boireaus 20071217 On arrondira seulement à l'affichage
			//$coef_eleve=number_format($coef_eleve,1, ',', ' ');
			//=========================
		} else {
			$current_eleve_note[$nb] = '';
			$current_eleve_statut[$nb] = 'Non suivie';
			$current_eleve_appreciation[$nb] = '';
		}


	//++++++++++++++++++++++++
	// Modif d'après F.Boisson
	// notes dans appreciation
		$sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$current_eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$nb' AND cnd.statut='';";
		$result_nbct=mysql_query($sql);
	$string_notes='';
	if ($result_nbct ) {
		while ($snnote =  mysql_fetch_assoc($result_nbct)) {
			if ($string_notes != '') $string_notes .= ", ";
			$string_notes .= $snnote['note'];
		}
	}
	$current_eleve_appreciation[$nb] = str_replace('@@Notes', $string_notes,$current_eleve_appreciation[$nb]);
	//++++++++++++++++++++++++


		$nb++;
	}
	*/

	//if ($inser_ligne == 'yes') {
		if ($affiche_categories) {
		// On regarde si on change de catégorie de matière
			if ($current_group["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
				$prev_cat_id = $current_group["classes"]["classes"][$id_classe]["categorie_id"];
				// On est dans une nouvelle catégorie
				// On récupère les infos nécessaires, et on affiche une ligne

				// On détermine le nombre de colonnes pour le colspan
				//$nb_total_cols = 4;
				$nb_total_cols = 3;
				//====================
				// Modif: boireaus 20070626
				if($affiche_coef=='y'){
					if ($test_coef != 0) $nb_total_cols++;
				}
				//====================
				if($avec_rapport_effectif=='y'){
					$nb_total_cols++;
				}
				//====================

				//if ($affiche_rang == 'y')  $nb_total_cols++;

				// On regarde s'il faut afficher la moyenne de l'élève pour cette catégorie

				$affiche_cat_moyenne_query = mysql_query("SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $prev_cat_id . "')");
				if (mysql_num_rows($affiche_cat_moyenne_query) == "0") {
					$affiche_cat_moyenne = false;
				} else {
					$affiche_cat_moyenne = mysql_result($affiche_cat_moyenne_query, 0);
				}

				// On a toutes les infos. On affiche !
				echo "<tr>\n";
				echo "<td colspan='" . $nb_total_cols . "'>\n";
				echo "<p style='padding: 0; margin:0; font-size: 10px;'>".$cat_names[$prev_cat_id]."</p></td>\n";
				echo "</tr>\n";
			}
		}
		echo "<tr>\n";
		echo "<td ";
		if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
		//echo" width=\"$larg_col1\" class='bull_simpl'><b>$current_matiere_nom_complet</b>";
		echo " width=\"$larg_col1\" class='bull_simpl'><b>".htmlentities($current_matiere_nom_complet)."</b>";
		$k = 0;
		while ($k < count($current_matiere_professeur_login)) {
			echo "<br /><i>".affiche_utilisateur($current_matiere_professeur_login[$k],$id_classe)."</i>";
			$k++;
		}
		echo "</td>\n";

		//====================
		// Modif: boireaus 20070626
		if($affiche_coef=='y'){
			if ($test_coef != 0) {
				//if ($current_coef > 0) $print_coef= $current_coef ; else $print_coef='-';
				//if ($coef_eleve > 0) $print_coef= $coef_eleve; else $print_coef='-';
				//if ($coef_eleve > 0) $print_coef= number_format($coef_eleve,1, ',', ' '); else $print_coef='-';
				$print_coef= number_format($current_coef,1, ',', ' ');
				echo "<td width=\"$larg_col2\"";
				if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
				echo " align=\"center\"><p class='bull_simpl'>".$print_coef."</p></td>\n";
			}
		}
		//====================

		/*
		if($avec_rapport_effectif=="y") {
			$nb=$periode1;
			$print_tr = 'no';
			while ($nb < $periode2+1) {
				$sql="SELECT 1=1 FROM j_eleves_classes jec,
									j_eleves_groupes jeg,
									j_groupes_classes jgc
								WHERE jec.id_classe='$id_classe' AND
										jec.periode='$nb' AND
										jec.periode=jeg.periode AND
										jec.login=jeg.login AND
										jeg.id_groupe='".$current_group["id"]."';";
				echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl'>Effectif</td>\n";
				$nb++;
			}
		}
		*/

		$nb=$periode1;
		$print_tr = 'no';
		while ($nb < $periode2+1) {
			if ($print_tr == 'yes') echo "<tr style='border-width: 5px;'>\n";

			//=========================
			// MODIF: boireaus 20080316
			if($nb==$periode1) {
				if($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
				}
			}
			elseif($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
			}
			//=========================


			if($avec_rapport_effectif=="y") {
				//$sql="SELECT 1=1 FROM j_eleves_classes jec,
				$sql="SELECT DISTINCT jeg.login FROM j_eleves_classes jec,
									j_eleves_groupes jeg,
									j_groupes_classes jgc
								WHERE jec.id_classe='$id_classe' AND
										jec.periode='$nb' AND
										jec.periode=jeg.periode AND
										jec.login=jeg.login AND
										jeg.id_groupe=jgc.id_groupe AND
										jeg.id_groupe='".$current_group["id"]."';";
				//$sql0=$sql;
				$res_effectif=mysql_query($sql);
				$effectif_grp_classe=mysql_num_rows($res_effectif);

				$sql="SELECT 1=1 FROM j_eleves_groupes jeg
								WHERE jeg.periode='$nb' AND
										jeg.id_groupe='".$current_group["id"]."';";
				$res_effectif_tot=mysql_query($sql);
				$effectif_grp_total=mysql_num_rows($res_effectif_tot);

				echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>";
				//echo "$sql0<br /><br />";
				//echo "$sql<br /><br />";
				if($effectif_grp_classe==$effectif_grp_total) {
					echo $effectif_grp_classe.'&nbsp;él.';
				}
				else {
					echo "$effectif_grp_classe&nbsp;él. /$effectif_grp_total";
				}
				echo "</td>\n";
			}


			echo "<td width=\"$larg_col2\" align=\"center\" class='bull_simpl' style='$style_bordure_cell'>\n";
			//$note=number_format($current_classe_matiere_moyenne[$nb],1, ',', ' ');
			$note=nf($current_classe_matiere_moyenne[$nb]);
			if ($note != "0,0")  {echo $note;} else {echo "-";}
			echo "</td>\n";
			/*
			echo "<td width=\"$larg_col3\" align=\"center\" class='bull_simpl'>\n<b>";
			$flag_moy[$nb] = 'no';
			if ($current_eleve_note[$nb] != '') {
				if ($current_eleve_statut[$nb] != '') {
					echo $current_eleve_statut[$nb];
				} else {
					$note=number_format($current_eleve_note[$nb],1, ',', ' ');
					echo "$note";
					$flag_moy[$nb] = 'yes';
				}
			} else {
				echo '-';
			}
			echo "</b></td>\n";
			//Affichage des cellules rang le cas échéant
			if ($affiche_rang == 'y')  {
				$rang = sql_query1("select rang from matieres_notes where (
				periode = '".$nb."' and
				id_groupe = '".$current_group["id"]."' and
				login = '".$current_eleve_login."' )
				");
				//if (($rang == 0) or ($rang == -1)) $rang = "-"; else $rang.="/".$nb_notes[$j][$nb];
				if (($rang == 0) or ($rang == -1)){
			$rang = "-";
		}
		else{
			//$rang.="/".$nb_notes[$current_group["id"]][$nb];
			//if(isset($nb_notes[$current_group["id"]][$nb])){
				$rang.="/".$nb_notes[$current_group["id"]][$nb];
			//}
			//$rang.="<br />\$nb_notes[".$current_group["id"]."][$nb]";
		}
				echo "<td width=\"$larg_col4\" align=\"center\" class='bull_simpl'><i>".$rang."</i></td>\n";
			}
			*/
			// Affichage des cases appréciations
			echo "<td width=\"$larg_col5\" class='bull_simpl' style='text-align:left; $style_bordure_cell'>\n";
			// Affichage des moyennes secondaires
			/*
			if ($nb_ligne_cn[$nb] != 0) {
				$tiret = 'no';
				for ($cn=0; $cn<$nb_ligne_cn[$nb]; $cn++) {
					$appel_cn = mysql_query("select note, statut from cn_notes_conteneurs where (login='$current_eleve_login' and id_conteneur='".$cn_id[$nb][$cn]."')");
					$cn_statut = @mysql_result($appel_cn,0,'statut');
					if ($cn_statut == 'y') {
						$cn_note = @mysql_result($appel_cn,0,'note');
						if ($tiret == 'yes')   echo " - ";
						echo $cn_nom[$nb][$cn]."&nbsp;:&nbsp;".$cn_note;
						$tiret = 'yes';

					}

				}
				echo "<br />\n";
			}
			*/


			if ($current_grp_appreciation[$nb]) {
				if ($current_grp_appreciation[$nb]=="-1") {
					echo "<span class='noprint'>-</span>\n";
				}
				else{
					if((strstr($current_grp_appreciation[$nb],">"))||(strstr($current_grp_appreciation[$nb],"<"))){
						echo "$current_grp_appreciation[$nb]";
					}
					else{
						echo nl2br($current_grp_appreciation[$nb]);
					}
				}
				//======================================
			} else {
				echo " -";
			}


			echo "</td></tr>\n";
			$print_tr = 'yes';
			$nb++;
		}

		/*
		// On calcule les moyennes générales de la classe :
		//if ($test_coef != 0) {
		$nb=$periode1;
		while ($nb < $periode2+1) {
			//if ($flag_moy[$nb] == 'yes') {

				//===========================
				// MODIF: boireaus 20070627
				//$total_coef[$nb] += $current_coef;
				//$total_points_classe[$nb] += $current_coef*$current_classe_matiere_moyenne[$nb];
				//$total_points_eleve[$nb] += $current_coef*$current_eleve_note[$nb];

				//$total_coef_eleve[$nb] += $coef_eleve;
				//$total_points_eleve[$nb] += $coef_eleve*$current_eleve_note[$nb];

				$total_coef_classe[$nb] += $current_coef;
				$total_points_classe[$nb] += $current_coef*$current_classe_matiere_moyenne[$nb];
				//===========================

				//if($affiche_categories=='1'){
				if(($affiche_categories=='1')||($affiche_categories==true)){
					if($current_classe_matiere_moyenne[$nb]!="-") {
						$total_cat_classe[$nb][$prev_cat_id] += $current_coef*$current_classe_matiere_moyenne[$nb];

						//===========================
						// MODIF: boireaus 20070627
						//$total_cat_eleve[$nb][$prev_cat_id] += $current_coef*$current_eleve_note[$nb];
						//$total_cat_eleve[$nb][$prev_cat_id] += $coef_eleve*$current_eleve_note[$nb];
						//$total_cat_coef[$nb][$prev_cat_id] += $current_coef;
						//$total_cat_coef_eleve[$nb][$prev_cat_id] += $coef_eleve;
						$total_cat_coef_classe[$nb][$prev_cat_id] += $current_coef;
						//===========================
					}
				}
			//}
			$nb++;
		}
		*/
		//}
	//}
	//$j++;
//  echo "</table>";
}

/*
//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>";
// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin de bulletin :
$call_data = mysql_query("SELECT * FROM aid_config WHERE order_display1 = 'e' ORDER BY order_display2");
$nb_aid = mysql_num_rows($call_data);
$z=0;
while ($z < $nb_aid) {
	$display_begin = mysql_result($call_data, $z, "display_begin");
	$display_end = mysql_result($call_data, $z, "display_end");
	if (($periode1 >= $display_begin) and ($periode2 <= $display_end)) {
		$indice_aid = @mysql_result($call_data, $z, "indice_aid");
		$aid_query = mysql_query("SELECT id_aid FROM j_aid_eleves WHERE (login='$current_eleve_login' and indice_aid='$indice_aid')");
		$aid_id = @mysql_result($aid_query, 0, "id_aid");
		if ($aid_id != '') {
			affiche_aid_simple($affiche_rang, $test_coef,$indice_aid,$aid_id,$current_eleve_login,$periode1,$periode2,$id_classe, 'bull_simpl');
		}
	}
	$z++;
}
//echo "</table>";
*/

//echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>";

//echo "<tr><td>\$test_coef=$test_coef</td></tr>";
// Affichage des moyennes générales
if($display_moy_gen=="y") {
	if ($test_coef != 0) {
		echo "<tr>\n<td";
		if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
		echo ">\n<p class='bull_simpl'><b>Moyenne générale</b></p>\n</td>\n";
		//====================
		// Modif: boireaus 20070626
		if($affiche_coef=='y'){
			echo "<td";
			if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
			echo " align=\"center\">-</td>\n";
		}
		//====================
		/*
		if($avec_rapport_effectif=="y") {
			echo "<td";
			if ($nb_periodes > 1) echo " rowspan=".$nb_periodes;
			echo " align=\"center\">-</td>\n";
		}
		*/

		$nb=$periode1;
		$print_tr = 'no';
		while ($nb < $periode2+1) {
			//=============================
			//if($nb==$periode1){echo "<tr>\n";}
			if($print_tr=='yes'){echo "<tr style='border-width: 5px;'>\n";}
			//=============================

			//=========================
			// AJOUT: boireaus 20080316
			if($nb==$periode1) {
				if($nb==$periode2) {
					$style_bordure_cell="border: 1px solid black";
				}
				else {
					$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
				}
			}
			elseif($nb==$periode2) {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
			}
			else {
				$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
			}
			//=========================

			if($avec_rapport_effectif=="y") {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE periode='$nb' AND id_classe='$id_classe';";
				$res_eff_classe=mysql_query($sql);

				echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
				//echo "$sql<br />";
				echo mysql_num_rows($res_eff_classe).' él.';
				echo "</td>\n";
			}


			echo "<td class='bull_simpl' align=\"center\" style='$style_bordure_cell'>\n";
			/*
			if ($total_points_classe[$nb] != 0) {
				//$moy_classe=number_format($total_points_classe[$nb]/$total_coef[$nb],1, ',', ' ');
				//=========================
				// MODIF: boireaus 20080316
				//$moy_classe=number_format($total_points_classe[$nb]/$total_coef_classe[$nb],1, ',', ' ');
				$moy_classe=$tab_moy_gen[$nb];
				//=========================
			} else {
				$moy_classe = '-';
			}
			*/
			echo nf($tab_moy['periodes'][$nb]['moy_generale_classe'],2);

			if ($affiche_deux_moy_gen==1) {
				echo "<br />\n";
				$moy_classe1=$tab_moy['periodes'][$nb]['moy_generale_classe1'];
				echo "<i>".nf($moy_classe1,2)."</i>\n";
			}
			echo "</td>\n";
			/*
			echo "<td class='bull_simpl' align=\"center\">\n";
			if ($total_points_eleve[$nb] != '0') {
				//$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef[$nb],1, ',', ' ');
				$moy_eleve=number_format($total_points_eleve[$nb]/$total_coef_eleve[$nb],1, ',', ' ');
			} else {
				$moy_eleve = '-';
			}
			echo "<b>".$moy_eleve."</b>\n</td>\n";
			if ($affiche_rang == 'y')  {
				$rang = sql_query1("select rang from j_eleves_classes where (
				periode = '".$nb."' and
				id_classe = '".$id_classe."' and
				login = '".$current_eleve_login."' )
				");
				if (($rang == 0) or ($rang == -1)) $rang = "-"; else  $rang .="/".$nombre_eleves;
					echo "<td class='bull_simpl' align=\"center\">".$rang."</td>\n";
			}
			*/
			if ($affiche_categories) {
				echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>\n";
				foreach($categories as $cat_id) {

					// MODIF: boireaus 20070627 ajout du test et utilisation de $total_cat_coef_eleve, $total_cat_coef_classe
					// Tester si cette catégorie doit avoir sa moyenne affichée
					$affiche_cat_moyenne_query = mysql_query("SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '".$id_classe."' and categorie_id = '".$cat_id."')");
					if (mysql_num_rows($affiche_cat_moyenne_query) == "0") {
						$affiche_cat_moyenne = false;
					} else {
						$affiche_cat_moyenne = mysql_result($affiche_cat_moyenne_query, 0);
					}

					if($affiche_cat_moyenne){
						/*
						//if ($total_cat_coef[$nb][$cat_id] != "0") {
						//if ($total_cat_coef_eleve[$nb][$cat_id] != "0") {
							//$moy_eleve=number_format($total_cat_eleve[$nb][$cat_id]/$total_cat_coef[$nb][$cat_id],1, ',', ' ');
							//$moy_classe=number_format($total_cat_classe[$nb][$cat_id]/$total_cat_coef[$nb][$cat_id],1, ',', ' ');
							//$moy_eleve=number_format($total_cat_eleve[$nb][$cat_id]/$total_cat_coef_eleve[$nb][$cat_id],1, ',', ' ');
							//echo "\$total_cat_coef_classe[$nb][$cat_id]=".$total_cat_coef_classe[$nb][$cat_id]."<br />";
							if ($total_cat_coef_classe[$nb][$cat_id] != "0") {
								$moy_classe=number_format($total_cat_classe[$nb][$cat_id]/$total_cat_coef_classe[$nb][$cat_id],1, ',', ' ');
							}
							else{
								$moy_classe="-";
							}

							//echo $cat_names[$cat_id] . " - <b>$moy_eleve</b> (classe : " . $moy_classe . ")<br/>\n";
							echo $cat_names[$cat_id] . " - <b>$moy_classe</b><br />\n";
						//}
						*/
						$moy_classe="-";
						$loop_i=0;
						while($loop_i<count($tab_moy['periodes'][$nb]['current_eleve_login'])) {
							if(isset($tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id])) {
								$moy_classe=$tab_moy['periodes'][$nb]['moy_cat_classe'][$loop_i][$cat_id];
								break;
							}
							$loop_i++;
						}

						echo $cat_names[$cat_id] . " - <b>".nf($moy_classe,2)."</b><br/>\n";

					}
				}
				echo "</td>\n</tr>\n";
			} else {
				echo "<td class='bull_simpl' style='text-align:left; $style_bordure_cell'>-</td>\n</tr>\n";
			}
			$nb++;
			$print_tr = 'yes';
		}
	}
}

echo "</table>\n";

/*
// Les absences

echo "<table width=$larg_tab border=1 cellspacing=1 cellpadding=1>\n";
$nb=$periode1;
while ($nb < $periode2+1) {
	$current_eleve_absences_query = mysql_query("SELECT * FROM absences WHERE (login='$current_eleve_login' AND periode='$nb')");
	$eleve_abs[$nb] = @mysql_result($current_eleve_absences_query, 0, "nb_absences");
	$eleve_abs_nj[$nb] = @mysql_result($current_eleve_absences_query, 0, "non_justifie");
	$eleve_retards[$nb] = @mysql_result($current_eleve_absences_query, 0, "nb_retards");
	$current_eleve_appreciation_absences = @mysql_result($current_eleve_absences_query, 0, "appreciation");
	if (($eleve_abs[$nb] != '') and ($eleve_abs_nj[$nb] != '')) {
		$eleve_abs_j[$nb] = $eleve_abs[$nb]-$eleve_abs_nj[$nb];
	} else {
		$eleve_abs_j[$nb] = "?";
	}
	$eleve_app_abs[$nb] = @mysql_result($current_eleve_absences_query, 0, "appreciation");
	if ($eleve_abs_nj[$nb] == '') { $eleve_abs_nj[$nb] = "?"; }
	if ($eleve_retards[$nb] == '') { $eleve_retards[$nb] = "?"; }
	echo "<tr>\n<td valign=top class='bull_simpl'>$nom_periode[$nb]</td>\n";
	echo "<td valign=top class='bull_simpl'>\n";
	if ($eleve_abs_j[$nb] == "1") {
		echo "Absences justifiées : une demi-journée";
	} else if ($eleve_abs_j[$nb] != "0") {
		echo "Absences justifiées : $eleve_abs_j[$nb] demi-journées";
	} else {
		echo "Aucune absence justifiée";
	}
	echo "</td>\n";
	echo "<td valign=top class='bull_simpl'>\n";
	if ($eleve_abs_nj[$nb] == '1') {
		echo "Absences non justifiées : une demi-journée";
	} else if ($eleve_abs_nj[$nb] != '0') {
		echo "Absences non justifiées : $eleve_abs_nj[$nb] demi-journées";
	} else {
		echo "Aucune absence non justifiée";
	}
	echo "</td>\n";
	echo "<td valign=top class='bull_simpl'>Nb. de retards : $eleve_retards[$nb]</td>\n</tr>\n";
	//Ajout Eric
	if ($current_eleve_appreciation_absences != "") {
	echo "<tr>\n";
	echo "<td valign=top class='bull_simpl'>&nbsp;</td>\n";
	echo "<td valign=top class='bull_simpl' colspan=\"3\">";
	echo " Observation(s) : $current_eleve_appreciation_absences</td>\n</tr>\n";
	}

	$nb++;
}
echo "</table>\n";
*/

// Maintenant, on met l'avis du conseil de classe :

echo "<span class='bull_simpl'><b>Avis du conseil de classe </b> ";
$gepi_prof_suivi=ucfirst(getSettingValue("gepi_prof_suivi"));
/*
if ($current_eleve_profsuivi_login) {
	echo "<b>(".ucfirst(getSettingValue("gepi_prof_suivi"))." : <i>".affiche_utilisateur($current_eleve_profsuivi_login,$id_classe)."</i>)</b>";
}
*/
if(empty($current_profsuivi_login)) {
	//echo "Pas de $gepi_prof_suivi désigné.";
	//echo "(-)";
	echo "";
}
else {
	echo "<b>($gepi_prof_suivi <i>";
	for($loop=0;$loop<count($current_profsuivi_login);$loop++) {
		if($loop>0) {echo ", ";}
		echo affiche_utilisateur($current_profsuivi_login[$loop],$id_classe);
	}
	echo "</i></b>)";
}

echo " :</span>\n";
$larg_col1b = $larg_tab - $larg_col1 ;
echo "<table width=\"$larg_tab\" class='boireaus' cellspacing='1' cellpadding='1' summary=''>\n";
$nb=$periode1;
while ($nb < $periode2+1) {
	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$nb');";
	//echo "$sql<br />";
	$res_current_synthese=mysql_query($sql);
	$current_synthese[$nb] = @mysql_result($res_current_synthese, 0, "synthese");
	if ($current_synthese[$nb] == '') {$current_synthese[$nb] = ' -';}

	//=========================
	// AJOUT: boireaus 20080317
	if($nb==$periode1) {
		if($nb==$periode2) {
			$style_bordure_cell="border: 1px solid black";
		}
		else {
			$style_bordure_cell="border: 1px solid black; border-bottom: 1px dashed black";
		}
	}
	elseif($nb==$periode2) {
		$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black;";
	}
	else {
		$style_bordure_cell="border: 1px solid black; border-top: 1px dashed black; border-bottom: 1px dashed black;";
	}
	//=========================

	echo "<tr>\n<td valign=\"top\" width =\"$larg_col1\" class='bull_simpl' style='$style_bordure_cell'>$nom_periode[$nb]</td>\n";
	echo "<td valign=\"top\" width = \"$larg_col1b\" class='bull_simpl' style='text-align:left; $style_bordure_cell'>".nl2br($current_synthese[$nb])."</td>\n";
	//=====================
	echo "</tr>\n";
	//=====================
	$nb++;
}
echo "</table>\n";
}

/*
function affiche_aid_simple($affiche_rang, $test_coef,$indice_aid,$aid_id,$current_eleve_login,$periode1,$periode2,$id_classe,$style_bulletin) {
	$nb_periodes = $periode2 - $periode1 + 1;
	$call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
	$AID_NOM = @mysql_result($call_data, 0, "nom");
	$note_max = @mysql_result($call_data, 0, "note_max");
	$type_note = @mysql_result($call_data, 0, "type_note");
	$display_begin = @mysql_result($call_data, 0, "display_begin");
	$display_end = @mysql_result($call_data, 0, "display_end");
	$bull_simplifie = @mysql_result($call_data, 0, "bull_simplifie");
	// On vérifie que cet AID soit autorisée à l'affichage dans le bulletin simplifié
	if ($bull_simplifie == "n") {
		return "";
	}

	$aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
	$aid_nom = @mysql_result($aid_nom_query, 0, "nom");
	//------
	// On regarde maintenant quelle sont les profs responsables de cette AID
	$aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id' and indice_aid='$indice_aid')");
	$nb_lig = mysql_num_rows($aid_prof_resp_query);
	$n = '0';
	while ($n < $nb_lig) {
		$aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
		$n++;
	}
	//------
	// On appelle l'appréciation de l'élève, et sa note le cas échéant
	//------
	$nb=$periode1;
	while($nb < $periode2+1) {
		$current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$nb' and id_aid='$aid_id' and indice_aid='$indice_aid')");
		$eleve_aid_app[$nb] = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
		if ($eleve_aid_app[$nb] == '') {$eleve_aid_app[$nb] = ' -';}
		$periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
		$periode_max = mysql_num_rows($periode_query);
		$last_periode_aid = min($periode_max,$display_end);
		if (($type_note == 'every') or (($type_note == 'last') and ($nb == $last_periode_aid))) {
			$current_eleve_aid_note[$nb] = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
			$current_eleve_aid_statut[$nb] = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
			if ($note_max != 20) {
				$eleve_aid_app[$nb] = "(note sur ".$note_max.") ".$eleve_aid_app[$nb];
			}
			if ($current_eleve_aid_note[$nb] != '') $current_eleve_aid_note[$nb]=number_format($current_eleve_aid_note[$nb],1, ',', ' ');
			$aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_min[$nb] = @mysql_result($aid_note_min_query, 0, "note_min");
			if ($aid_note_min[$nb] == '') {$aid_note_min[$nb] = '-';}
			$aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_max[$nb] = @mysql_result($aid_note_max_query, 0, "note_max");
			if ($aid_note_max[$nb] == '') {$aid_note_max[$nb] = '-';}

			$aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$nb' and j.periode='$nb' and a.indice_aid='$indice_aid')");

			$aid_note_moyenne[$nb] = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
			if ($aid_note_moyenne[$nb] == '') {
				$aid_note_moyenne[$nb] = '-';
			} else {
				$aid_note_moyenne[$nb]=number_format($aid_note_moyenne[$nb],1, ',', ' ');
			}
		} else {
			$current_eleve_aid_statut[$nb] = '-';
			$current_eleve_aid_note[$nb] = '-';
			$aid_note_min[$nb] = '-';
			$aid_note_max[$nb] = '-';
			$aid_note_moyenne[$nb] = '-';
		}
		$nb++;
	}
	//------
	// On affiche l'appréciation aid :
	//------

	echo "<tr><td ";
	if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
	echo " class='$style_bulletin'><b>$AID_NOM : $aid_nom</b><br /><i>";
	$n = '0';
	while ($n < $nb_lig) {
		echo affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
		$n++;
	}
	echo "</i></td>";
	if ($test_coef != 0) {
		echo "<td ";
		if ($nb_periodes > 1) echo " rowspan= ".$nb_periodes;
		echo " align=\"center\"><p class='".$style_bulletin."'>-</p></td>";
	}
	$nb=$periode1;
	$print_tr = 'no';
	while ($nb < $periode2+1) {
		if ($print_tr == 'yes') echo "<tr>";
		echo "<td align=\"center\" class='$style_bulletin'>$aid_note_moyenne[$nb]</td>";
		echo "<td align=\"center\" class='$style_bulletin'><b>";
		if ($current_eleve_aid_statut[$nb] == '') {
			if ($current_eleve_aid_note[$nb] != '') {
				echo $current_eleve_aid_note[$nb];
			} else {
				echo "-";
			}
		} else if ($current_eleve_aid_statut[$nb] != 'other'){
			echo "$current_eleve_aid_statut[$nb]";
		} else {
			echo "-";
		}
		echo "</b></td>";
		if ($affiche_rang == 'y') echo "<td align=\"center\" class='".$style_bulletin."'>-</td>";

		echo "<td class='$style_bulletin'>$eleve_aid_app[$nb]</td></tr>";
		$print_tr = 'yes';
		$nb++;
	}


	//------

}
*/
?>