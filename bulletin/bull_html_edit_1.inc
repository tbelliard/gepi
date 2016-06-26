<?php
/*
 * $Id$
 */

//$min_max_moyclas=1;

// On initialise le tableau des notes et appréciations :
echo "<table $class_bordure width='$largeurtableau' border='1' cellspacing='".$cellspacing."' cellpadding='".$cellpadding."' summary='Tableau des notes et appréciations'>\n";
echo "<thead>\n";
if($min_max_moyclas!=1) {
	$chaine_rowspan=" rowspan='2'";
}
else{
	$chaine_rowspan="";
}
echo "<tr>\n<td style=\"width: ".$col_matiere_largeur."px; vertical-align: top;\"$chaine_rowspan>
<span class='bulletin'><font size=\"1\">";
if ($bull_affiche_numero == 'yes'){
	// En attendant de corriger le bug sur $k
	if(isset($k)) {
		echo "Bulletin N° ".$k."/".$tab_bull['eff_classe'];
	}
	else {
		echo "Bulletin N° .../".$tab_bull['eff_classe'];
	}
}
else{
	echo "Effectif : ".$tab_bull['eff_classe']." élèves";
}
echo "</font></span></td>\n";

//if ($test_coef != 0 and $affiche_coef == "y"){
if($tab_bull['affiche_coef']=='y') {
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan> <span class='bulletin'>Coef.</span></td>\n";
}

if($tab_bull['affiche_nbdev']=="y"){
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>Nb.dev</span></td>\n";
}

if($min_max_moyclas!=1) {
	// Trois colonnes séparées pour min/moy/max
	echo "<td style=\"text-align: center;\" colspan=\"3\"><span class='bulletin'>Pour la classe</span></td>\n";
}
else{
	// Min/Classe/Max en une seule colonne
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Classe m/C/M</span></td>\n";
}

echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>&Eacute;lève</span></td>\n";

if ($tab_bull['affiche_graph'] == 'y')  {
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>Niveaux<br />ABC<font size=\"-2\">+</font>C<font size=\"-2\">-</font>DE</span></td>\n";
}

if ($tab_bull['affiche_rang'] == 'y'){
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'><i>Rang</i></span></td>\n";
}

// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
if ($bull_affiche_appreciations == 'y') {
	// 20160626
	if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
		$bull_largeur_col_Elements_Programmes=getSettingValue('bull_largeur_col_Elements_Programmes');
		echo "<td style=\"width: ".$bull_largeur_col_Elements_Programmes."px; text-align: center;\"$chaine_rowspan	><span class='bulletin'>Éléments de programmes</span></td>\n";
	}

	echo "<td colspan=\"2\"$chaine_rowspan><span class='bulletin'>".$bull_intitule_app."</span></td>\n";
}
echo "</tr>\n";

if($min_max_moyclas!=1) {
	echo "<tr>\n";
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Min</span></td>\n";
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Moy.</span></td>\n";
	echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Max</span></td>\n";
	echo "</tr>\n";
}
/*
else{
	// Min/Classe/Max en une seule colonne
	echo "<tr>\n";
	echo "</tr>\n";
	// CA ME SEMBLE INUTILE
}
*/
echo "</thead>\n";


//===============================================================================
// Début de la partie AID de début de bulletin
// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
if ($bull_affiche_aid == 'y') {
	// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :

	if(isset($tab_bull['eleve'][$i]['aid_b'])) {
		for($z=0;$z<count($tab_bull['eleve'][$i]['aid_b']);$z++) {
			echo "<tr>\n";

			echo "<td><span class='bulletin'>".htmlspecialchars($tab_bull['eleve'][$i]['aid_b'][$z]['nom_complet']);
			echo "<br />";
			$cpt=0;
			foreach($tab_bull['eleve'][$i]['aid_b'][$z]['aid_prof_resp_login'] as $current_aid_prof_login) {
				if($cpt>0) {echo ", ";}
				echo "<i>".affiche_utilisateur($current_aid_prof_login,$tab_bull['id_classe'])."</i>";
				$cpt++;
			}
			echo "</span></td>\n";

			//if ($test_coef != 0 and $affiche_coef == "y"){
			if($tab_bull['affiche_coef']=='y') {
				echo "<td>-</td>\n";
			}

			if($tab_bull['affiche_nbdev']=="y"){
				echo "<td>-</td>\n";
			}

			// Moyenne min/classe/max de la classe
			if($min_max_moyclas!=1) {
				// Trois colonnes séparées pour min/moy/max
				echo "<td><span class='bulletin' title=\"Note minimale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_min']."</span></td>\n";
				echo "<td><span class='bulletin' title=\"Note moyenne des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_moyenne']."</span></td>\n";
				echo "<td><span class='bulletin' title=\"Note maximale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_max']."</span></td>\n";
			}
			else{
				// Min/Classe/Max en une seule colonne
				echo "<td><span class='bullminclasmax' title=\"Note minimale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_min']."</span><br />\n";
				echo "<span class='bullminclasmax' title=\"Note moyenne des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_moyenne']."</span><br />\n";
				echo "<span class='bullminclasmax' title=\"Note maximale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_b'][$z]['aid_note_max']."</span></td>\n";
			}

			// Moyenne de l'élève
			echo "<td style='font-weight:bold;'><span class='bulletin'>";
			if($tab_bull['eleve'][$i]['aid_b'][$z]['aid_statut']=="") {
				echo nbsp_au_lieu_de_vide($tab_bull['eleve'][$i]['aid_b'][$z]['aid_note']);
			}
			elseif($tab_bull['eleve'][$i]['aid_b'][$z]['aid_statut']=="other") {
				echo "-";
			}
			else {
				echo $tab_bull['eleve'][$i]['aid_b'][$z]['aid_statut'];
			}
			echo "</span></td>\n";

			if ($tab_bull['affiche_graph'] == 'y') {
				echo "<td>";
				if((isset($tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve']))&&($tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve']!="")) {
					echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['eleve'][$i]['aid_b'][$z]['place_eleve'].
						"&amp;temp1=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile1_classe'].
						"&amp;temp2=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile2_classe'].
						"&amp;temp3=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile3_classe'].
						"&amp;temp4=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile4_classe'].
						"&amp;temp5=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile5_classe'].
						"&amp;temp6=".$tab_bull['eleve'][$i]['aid_b'][$z]['quartile6_classe'].
						"&amp;nb_data=7' alt='Quartiles' />\n";
				}
				else {
					echo "-";
				}
				echo "</td>\n";
			}

			if($tab_bull['affiche_rang']=='y') {
				echo "<td>-</td>\n";
			}

			// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
			if ($bull_affiche_appreciations == 'y'){
				// 20160626
				if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
					// Pas d'éléments de programmes dans les AID pour le moment
					echo "<td style=\"width: ".$bull_largeur_col_Elements_Programmes."px; text-align: center;\"><span class='bulletin'>-</span></td>\n";
				}

				echo "<td style='text-align: left;' colspan='2'><span class='bulletin'>";
				if(($tab_bull['eleve'][$i]['aid_b'][$z]['message']!='')||(($tab_bull['eleve'][$i]['aid_b'][$z]['aid_nom']!='')&&($tab_bull['eleve'][$i]['aid_b'][$z]['display_nom']=='y'))) {
					echo "<b>";
					if($tab_bull['eleve'][$i]['aid_b'][$z]['message']!='') {
						echo $tab_bull['eleve'][$i]['aid_b'][$z]['message']." ";
					}
					echo $tab_bull['eleve'][$i]['aid_b'][$z]['aid_nom']."</b><br />";
				}
				echo texte_html_ou_pas($tab_bull['eleve'][$i]['aid_b'][$z]['aid_appreciation']);
				echo "</span></td>\n";
			}

			echo "</tr>\n";
		}
	}
}
// Fin de la partie AID de début de bulletin
//===============================================================================

//===============================================================================
// Partie matières/groupes
$categorie_precedente="";
for($j=0;$j<count($tab_bull['groupe']);$j++) {
	// Si l'élève suit l'option, sa note est affectée (éventuellement vide)
	if(isset($tab_bull['note'][$j][$i])) {

		if($tab_bull['affiche_categories']) {
			if($categorie_precedente!=$tab_bull['cat_id'][$j]) {
				if($bull_categ_bgcolor!=''){
					echo "<tr bgcolor='".$bull_categ_bgcolor."'>\n";
				}
				else{
					echo "<tr>\n";
				}

				if($tab_bull['affiche_moyenne'][$j]==1) {
					$colspan=1;
					if($tab_bull['affiche_coef']=='y') {$colspan++;}
					if($tab_bull['affiche_nbdev']=='y') {$colspan++;}

					echo "<td colspan='$colspan'><p style='padding: 0; margin:0; font-size: ".$bull_categ_font_size."px;'>".$tab_bull['nom_cat_complet'][$j]."</p></td>\n";

					if($min_max_moyclas!=1) {
						// Min
						//echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>-</td>\n";
						echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;' title=\"Moyenne minimale des élèves pour cette catégorie\">".nf($tab_bull['moy_cat_min'][$i][$tab_bull['cat_id'][$j]])."</td>\n";
						// Moyenne catégorie classe
						echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;' title=\"Moyenne de la classe pour cette catégorie\">".nf($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$j]])."</td>\n";
						// Max
						//echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>-</td>\n";
						echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;' title=\"Moyenne maximale des élèves pour cette catégorie\">".nf($tab_bull['moy_cat_max'][$i][$tab_bull['cat_id'][$j]])."</td>\n";
					}
					else {
						// Moyenne catégorie classe
						echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;' title=\"Moyenne de la classe pour cette catégorie\">".nf($tab_bull['moy_cat_classe'][$i][$tab_bull['cat_id'][$j]])."</td>\n";
					}

					// Moyenne catégorie élève
					//echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>".nbsp_au_lieu_de_vide($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$j]])."</td>\n";
					echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>".nf($tab_bull['moy_cat_eleve'][$i][$tab_bull['cat_id'][$j]])."</td>\n";

					$colspan=2;
					if($tab_bull['affiche_graph']=='y') {$colspan++;}
					if($tab_bull['affiche_rang']=='y') {$colspan++;}
					if ($bull_affiche_appreciations != 'y') {
						if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
							$colspan--;
						}
						$colspan-=2;
					}
					if($colspan>0) {
						// Appréciation
						echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: left;' colspan='$colspan'>-</td>\n";
					}
				}
				else {
					$colspan=7;
					if($tab_bull['affiche_coef']=='y') {$colspan++;}
					if($tab_bull['affiche_nbdev']=='y') {$colspan++;}
					if($min_max_moyclas==1){$colspan-=2;}
					if($tab_bull['affiche_graph']=='y') {$colspan++;}
					if($tab_bull['affiche_rang']=='y') {$colspan++;}
					if ($bull_affiche_appreciations != 'y') {
						if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
							$colspan--;
						}
						$colspan-=2;
					}

					echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;' colspan='$colspan'>".$tab_bull['nom_cat_complet'][$j]."</td>\n";
				}

				echo "</tr>\n";

				$categorie_precedente=$tab_bull['cat_id'][$j];
			}
		}

		if(isset($tab_bull['groupe'][$j][$i]['cn_note'])) {
			//$rowspan=count($tab_bull['groupe'][$j][$i]['cn_note']);
			$chaine_rowspan=" rowspan='".count($tab_bull['groupe'][$j][$i]['cn_note'])."'";
		}
		else {
			$chaine_rowspan="";
		}

		echo "<tr>\n";
		//echo "<td$chaine_rowspan style='text-align: left;'><span class='bulletin'>".htmlspecialchars($tab_bull['groupe'][$j]['matiere']['nom_complet'])."</span>";

		echo "<td$chaine_rowspan style='text-align: left;'>";
		if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
			$info_nom_matiere=$tab_bull['groupe'][$j]['name'];
		}
		elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
			$info_nom_matiere=$tab_bull['groupe'][$j]['description'];
		}
		else {
			// Pour parer au bug sur la suppression de matière alors que des groupes sont conservés:
			if(isset($tab_bull['groupe'][$j]['matiere']['nom_complet'])) {
				$info_nom_matiere=$tab_bull['groupe'][$j]['matiere']['nom_complet'];
			}
			else {
				$info_nom_matiere=$tab_bull['groupe'][$j]['name']." (".$tab_bull['groupe'][$j]['id'].")";
			}
		}
		echo "<span class='bulletin'>".htmlspecialchars($info_nom_matiere)."</span>";

		echo "<br />\n";
		echo "<span class='bulletin'>";
		$cpt=0;
		foreach($tab_bull['groupe'][$j]["profs"]["list"] as $current_prof_login) {
			if($cpt>0) {echo ", ";}
			echo "<i>".affiche_utilisateur($current_prof_login,$tab_bull['id_classe'])."</i>";
			$cpt++;
		}
		echo "</span></td>\n";

		if($tab_bull['affiche_coef']=='y') {
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin'>".$tab_bull['coef_eleve'][$i][$j]."</span></td>\n";
		}

		if($tab_bull['affiche_nbdev']=="y"){
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin'>".$tab_bull['nbct'][$j][$i]."/".$tab_bull['groupe'][$j]['nbct']."</span></td>\n";
		}

		if($min_max_moyclas!=1) {
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin' title=\"Note minimale des élèves pour cet enseignement\">".nf($tab_bull['moy_min_classe_grp'][$j])."</span></td>\n";
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin' title=\"Note moyenne des élèves pour cet enseignement\">".nf($tab_bull['moy_classe_grp'][$j])."</span></td>\n";
			//echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin'>\$tab_bull['moy_classe_grp'][$j]=".$tab_bull['moy_classe_grp'][$j]."</span></td>\n";
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin' title=\"Note maximale des élèves pour cet enseignement\">".nf($tab_bull['moy_max_classe_grp'][$j])."</span></td>\n";
		}
		else {
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bullminclasmax' title=\"Note minimale des élèves pour cet enseignement\">".nf($tab_bull['moy_min_classe_grp'][$j])."</span><br />\n";
			echo "<span class='bullminclasmax' title=\"Note moyenne des élèves pour cet enseignement\">".nf($tab_bull['moy_classe_grp'][$j])."</span><br />\n";
			echo "<span class='bullminclasmax' title=\"Note maximale des élèves pour cet enseignement\">".nf($tab_bull['moy_max_classe_grp'][$j])."</span></td>\n";
		}

		echo "<td$chaine_rowspan style='font-weight:bold; text-align: center;'><span class='bulletin'>";

		if(($moyennes_periodes_precedentes=="y")&&(isset($tab_bull['moy_annee'][$j][$i]))) {
			if(isset($tab_bull['login_prec'])) {
				foreach($tab_bull['login_prec'] as $key => $value) {
					// Il faut récupérer l'id_groupe et l'indice de l'élève... dans les tableaux récupérés de calcul_moy_gen.inc.php
					// Tableaux d'indices [$j][$i] (groupe, élève)
					//		$tab_bull['note_prec'][$loop_p]=$current_eleve_note;
					//		$tab_bull['statut_prec'][$loop_p]=$current_eleve_statut;
					$indice_eleve=-1;
					for($loop_l=0;$loop_l<count($tab_bull['login_prec'][$key]);$loop_l++) {
						if($tab_bull['login_prec'][$key][$loop_l]==$tab_bull['eleve'][$i]['login']) {$indice_eleve=$loop_l;break;}
					}

					if($indice_eleve!=-1) {
						// Recherche du groupe
						$indice_grp=-1;
						for($loop_l=0;$loop_l<count($tab_bull['group_prec'][$key]);$loop_l++) {
							if($tab_bull['group_prec'][$key][$loop_l]['id']==$tab_bull['groupe'][$j]['id']) {$indice_grp=$loop_l;break;}
						}

						if($indice_grp!=-1) {
							if(isset($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve])) {
								if ($tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve]!="") {
									$valeur = $tab_bull['statut_prec'][$key][$indice_grp][$indice_eleve];
								}
								else {
									$valeur = nf($tab_bull['note_prec'][$key][$indice_grp][$indice_eleve]);
								}
								echo "<span style='font-size:x-small; font-weight:normal;' title=\"Moyenne sur la période $key\">P$key:&nbsp;".$valeur."</span>";
								echo "<br />\n";
							}
						}
					}

				}
			}
		}

		if($tab_bull['statut'][$j][$i]=="") {
			echo nbsp_au_lieu_de_vide(nf($tab_bull['note'][$j][$i]));
		}
		else {
			echo $tab_bull['statut'][$j][$i];
		}

		if(($moyennes_annee=="y")&&(isset($tab_bull['moy_annee'][$j][$i]))) {
			echo "<br />\n";
			echo "<span style='font-size:x-small; font-weight:normal;' title=\"Moyenne sur l'année\">An:&nbsp;".$tab_bull['moy_annee'][$j][$i]."</span>";
		}

		echo "</span></td>\n";

		if ($tab_bull['affiche_graph'] == 'y') {
			echo "<td$chaine_rowspan style='text-align: center;'>";
			if((isset($tab_bull['place_eleve'][$j][$i]))&&($tab_bull['place_eleve'][$j][$i]!="")) {
				//echo $place_eleve_classe[$i]." ";
				echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['place_eleve'][$j][$i].
					"&amp;temp1=".$tab_bull['quartile1_grp'][$j].
					"&amp;temp2=".$tab_bull['quartile2_grp'][$j].
					"&amp;temp3=".$tab_bull['quartile3_grp'][$j].
					"&amp;temp4=".$tab_bull['quartile4_grp'][$j].
					"&amp;temp5=".$tab_bull['quartile5_grp'][$j].
					"&amp;temp6=".$tab_bull['quartile6_grp'][$j].
					"&amp;nb_data=7' alt='Quartiles' />\n";
			}
			else {
				echo "-";
			}
			echo "</td>\n";
		}

		if($tab_bull['affiche_rang']=='y') {
			echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin'>";
			//if((isset($tab_bull['rang'][$i][$j]))&&($tab_bull['rang'][$i][$j]!="-")) {
				//echo $tab_bull['rang'][$i][$j]."/".$tab_bull['groupe'][$j]['effectif'];
			if((isset($tab_bull['rang'][$j][$i]))&&($tab_bull['rang'][$j][$i]!="-")) {
				echo $tab_bull['rang'][$j][$i]."/".$tab_bull['groupe'][$j]['effectif_avec_note'];
				//echo "\$tab_bull['rang'][$j][$i]=".$tab_bull['rang'][$i][$j]."/".$tab_bull['groupe'][$j]['effectif_avec_note'];
			}
			else {
				echo "-";
			}
			echo "</span></td>\n";
		}

		if ($bull_affiche_appreciations == 'y') {
			// 20160626
			if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
				$bull_largeur_col_Elements_Programmes=getSettingValue('bull_largeur_col_Elements_Programmes');
				echo "<td style=\"width: ".$bull_largeur_col_Elements_Programmes."px; text-align: left;\"><span class='bulletin'>";

				$texte_Elements_Programmes="";
				if((isset($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$j]['id']]))&&(is_array($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$j]['id']]))) {
					for($loop_mep=0;$loop_mep<count($tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$j]['id']]);$loop_mep++) {
						if($texte_Elements_Programmes!="") {
							$texte_Elements_Programmes.="<br />\n";
						}
						$texte_Elements_Programmes.="- ".$tab_bull['ElementsProgrammes']['ele'][$tab_bull['eleve'][$i]['login']][$tab_bull['groupe'][$j]['id']][$loop_mep];
					}
				}
				echo $texte_Elements_Programmes;
				echo "</span></td>\n";
			}

			if(!isset($tab_bull['groupe'][$j][$i]['cn_note'])) {
				// Appréciation
				echo "<td colspan='2' style='text-align: left;'><span class='bulletin'>";
				echo texte_html_ou_pas($tab_bull['app'][$j][$i]);
				echo "</span></td>\n";
			}
			else {
				$n = 0;
				// Première boite
				echo "<td style='text-align: left; height: ".$col_hauteur."px; width: ".$col_boite_largeur."px;'><span class='bulletin'>";
				echo $tab_bull['groupe'][$j][$i]['cn_nom'][$n].":".nf($tab_bull['groupe'][$j][$i]['cn_note'][$n]);
				echo "</span></td>\n";
				$n++;

				// Appréciation
				echo "<td$chaine_rowspan style='text-align: center;'><span class='bulletin'>";
				echo texte_html_ou_pas($tab_bull['app'][$j][$i]);
				echo "</span></td>\n";

				// Boites suivantes
				while ($n < count($tab_bull['groupe'][$j][$i]['cn_note'])) {
					echo "</tr>\n";
					echo "<tr>\n";
					echo "<td style='height: ".$col_hauteur."px; text-align: left;'><span class='bulletin'>";
					echo $tab_bull['groupe'][$j][$i]['cn_nom'][$n].":".nf($tab_bull['groupe'][$j][$i]['cn_note'][$n]);
					echo "</span></td>\n";
					$n++;
				}
			}
		}
		echo "</tr>\n";
	}
}
// Fin de la partie Matières/groupes
//===============================================================================

//===============================================================================
// Début de la partie AID de fin de bulletin
// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
if ($bull_affiche_aid == 'y') {
	// On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :

	if(isset($tab_bull['eleve'][$i]['aid_e'])) {
		for($z=0;$z<count($tab_bull['eleve'][$i]['aid_e']);$z++) {
			echo "<tr>\n";

			echo "<td style='text-align: center;'><span class='bulletin'>".htmlspecialchars($tab_bull['eleve'][$i]['aid_e'][$z]['nom_complet']);
			echo "<br />";
			$cpt=0;
			foreach($tab_bull['eleve'][$i]['aid_e'][$z]['aid_prof_resp_login'] as $current_aid_prof_login) {
				if($cpt>0) {echo ", ";}
				echo "<i>".affiche_utilisateur($current_aid_prof_login,$tab_bull['id_classe'])."</i>";
				$cpt++;
			}
			echo "</span></td>\n";

			//if ($test_coef != 0 and $affiche_coef == "y"){
			if($tab_bull['affiche_coef']=='y') {
				echo "<td style='text-align: center;'>-</td>\n";
			}

			if($tab_bull['affiche_nbdev']=="y"){
				echo "<td style='text-align: center;'>-</td>\n";
			}

			// Moyenne min/classe/max de la classe
			if($min_max_moyclas!=1) {
				// Trois colonnes séparées pour min/moy/max
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Note minimale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_min']."</span></td>\n";
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Note moyenne des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_moyenne']."</span></td>\n";
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Note maximale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_max']."</span></td>\n";
			}
			else{
				// Min/Classe/Max en une seule colonne
				echo "<td style='text-align: center;'><span class='bullminclasmax' title=\"Note minimale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_min']."</span><br />\n";
				echo "<span class='bullminclasmax' title=\"Note moyenne des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_moyenne']."</span><br />\n";
				echo "<span class='bullminclasmax' title=\"Note maximale des élèves pour cet enseignement\">".$tab_bull['eleve'][$i]['aid_e'][$z]['aid_note_max']."</span></td>\n";
			}

			// Moyenne de l'élève
			echo "<td style='text-align: center; font-weight:bold;'><span class='bulletin'>";
			if($tab_bull['eleve'][$i]['aid_e'][$z]['aid_statut']=="") {
				echo nbsp_au_lieu_de_vide($tab_bull['eleve'][$i]['aid_e'][$z]['aid_note']);
			}
			elseif($tab_bull['eleve'][$i]['aid_e'][$z]['aid_statut']=="other") {
				echo "-";
			}
			else {
				echo $tab_bull['eleve'][$i]['aid_e'][$z]['aid_statut'];
			}
			echo "</span></td>\n";

			if ($tab_bull['affiche_graph'] == 'y') {
				echo "<td style='text-align: center;'>";
				if((isset($tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve']))&&($tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve']!="")) {
					echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['eleve'][$i]['aid_e'][$z]['place_eleve'].
						"&amp;temp1=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile1_classe'].
						"&amp;temp2=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile2_classe'].
						"&amp;temp3=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile3_classe'].
						"&amp;temp4=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile4_classe'].
						"&amp;temp5=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile5_classe'].
						"&amp;temp6=".$tab_bull['eleve'][$i]['aid_e'][$z]['quartile6_classe'].
						"&amp;nb_data=7' alt='Quartiles' />\n";
				}
				else {
					echo "-";
				}
				echo "</td>\n";
			}

			if($tab_bull['affiche_rang']=='y') {
				echo "<td style='text-align: center;'>-</td>\n";
			}

			// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
			if ($bull_affiche_appreciations == 'y'){
				// 20160626
				if((!getSettingAOui('bullNoSaisieElementsProgrammes'))&&(getSettingAOui('bull_aff_Elements_Programmes'))){
					// Pas d'éléments de programmes dans les AID pour le moment
					echo "<td style=\"width: ".$bull_largeur_col_Elements_Programmes."px; text-align: center;\"><span class='bulletin'>-</span></td>\n";
				}

				echo "<td style='text-align: left;' colspan='2'><span class='bulletin'>";
				if(($tab_bull['eleve'][$i]['aid_e'][$z]['message']!='')||(($tab_bull['eleve'][$i]['aid_e'][$z]['aid_nom']!='')&&($tab_bull['eleve'][$i]['aid_e'][$z]['display_nom']=='y'))) {
					echo "<b>";
					if($tab_bull['eleve'][$i]['aid_e'][$z]['message']!='') {
						echo $tab_bull['eleve'][$i]['aid_e'][$z]['message']." ";
					}
					echo $tab_bull['eleve'][$i]['aid_e'][$z]['aid_nom']."</b><br />";
				}
				echo texte_html_ou_pas($tab_bull['eleve'][$i]['aid_e'][$z]['aid_appreciation']);
				echo "</span></td>\n";
			}

			echo "</tr>\n";
		}
	}
}
// Fin de la partie AID de fin de bulletin
//===============================================================================

//===============================================================================
// Début de la partie moyenne générale
if($tab_bull['display_moy_gen']=='y') {
	// Affichage des moyennes générales
	if ($tab_bull['test_coef']!=0) {

		$total_coeff_eleve=0;
		for($j=0;$j<count($tab_bull['groupe']);$j++) {
			// Si l'élève suit l'option
			if(isset($tab_bull['coef_eleve'][$i][$j])) {
				$total_coeff_eleve+=$tab_bull['coef_eleve'][$i][$j];
			}
		}

		if ($total_coeff_eleve) {
			echo "<tr>\n";
			echo "<td style='text-align: left;'><span class='bulletin'><b>Moyenne générale</b></span></td>\n";

			// Coef
			if($tab_bull['affiche_coef']=='y') {
				echo "<td style='text-align: center;'><span class='bulletin'>-</span></td>\n";
			}

			// Nb dev
			if($tab_bull['affiche_nbdev']=='y') {
				echo "<td style='text-align: center;'><span class='bulletin'>-</span></td>\n";
			}

			if($min_max_moyclas!=1) {
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Moyenne générale minimale de la classe\">".nf($tab_bull['moy_min_classe'])."</span></td>\n";
				//$tab_bull['moy_gen_classe'][$i]
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Moyenne générale de la classe\"><b>".nf($tab_bull['moy_generale_classe'])."</b></span></td>\n";
				echo "<td style='text-align: center;'><span class='bulletin' title=\"Moyenne générale maximale de la classe\">".nf($tab_bull['moy_max_classe'])."</span></td>\n";
			}
			else {
				echo "<td style='text-align: center;'><span class='bullminclasmax' title=\"Moyenne générale minimale de la classe\">".nf($tab_bull['moy_min_classe'])."</span><br />\n";
				//$tab_bull['moy_gen_classe'][$i]
				echo "<span class='bullminclasmax' title=\"Moyenne générale de la classe\"><b>".nf($tab_bull['moy_generale_classe'])."</b></span><br />\n";
				echo "<span class='bullminclasmax' title=\"Moyenne générale maximale de la classe\">".nf($tab_bull['moy_max_classe'])."</span></td>\n";
			}

			echo "<td style='text-align: center; font-weight:bold;'><span class='bulletin'>".nf($tab_bull['moy_gen_eleve'][$i])."</span></td>\n";

			if ($tab_bull['affiche_graph']=='y') {
				echo "<td style='text-align: center;'>";

				if((isset($tab_bull['place_eleve_classe'][$i]))&&($tab_bull['place_eleve_classe'][$i]!="")) {
					//echo $place_eleve_classe[$i]." ";
					echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['place_eleve_classe'][$i].
						"&amp;temp1=".$tab_bull['quartile1_classe_gen'].
						"&amp;temp2=".$tab_bull['quartile2_classe_gen'].
						"&amp;temp3=".$tab_bull['quartile3_classe_gen'].
						"&amp;temp4=".$tab_bull['quartile4_classe_gen'].
						"&amp;temp5=".$tab_bull['quartile5_classe_gen'].
						"&amp;temp6=".$tab_bull['quartile6_classe_gen'].
						"&amp;nb_data=7' alt='Quartiles' />\n";
				}
				else {
					echo "-";
				}

				echo "</td>\n";
			}

			// Rang
			if($tab_bull['affiche_rang']=='y') {
				echo "<td style='text-align: center;'><span class='bulletin'>";
				if(isset($tab_bull['rang_classe'][$i])) {
					echo $tab_bull['rang_classe'][$i]."/".$tab_bull['eff_classe'];
				}
				else {
					echo "-";
				}
				echo "</span></td>\n";
			}

			echo "<td style='text-align: left;' colspan='2'><span class='bulletin'>\n";
			//echo $tab_bull['avis'][$i];
			echo "-";
			echo "</span></td>\n";
			echo "</tr>\n";
		}
	}
}
// Fin de la partie moyenne générale
//===============================================================================


//===============================================================================
if($affiche_deux_moy_gen==1) {
	// Début de la partie moyenne générale avec coef 1
	if($tab_bull['display_moy_gen']=='y') {
		// Affichage des moyennes générales
		if ($tab_bull['test_coef']!=0) {
	
			$total_coeff_eleve=0;
			for($j=0;$j<count($tab_bull['groupe']);$j++) {
				// Si l'élève suit l'option
				if(isset($tab_bull['coef_eleve'][$i][$j])) {
					$total_coeff_eleve+=$tab_bull['coef_eleve'][$i][$j];
				}
			}
	
			if ($total_coeff_eleve) {
				echo "<tr>\n";
				echo "<td style='text-align: left;'><span class='bulletin'><b>Moy.gén.non coef.</b></span></td>\n";
	
				// Coef
				if($tab_bull['affiche_coef']=='y') {
					echo "<td style='text-align: center;'><span class='bulletin'>-</span></td>\n";
				}
	
				// Nb dev
				if($tab_bull['affiche_nbdev']=='y') {
					echo "<td style='text-align: center;'><span class='bulletin'>-</span></td>\n";
				}
	
				if($min_max_moyclas!=1) {
					echo "<td style='text-align: center;'><span class='bulletin'>".nf($tab_bull['moy_min_classe_noncoef'])."</span></td>\n";
					//$tab_bull['moy_gen_classe'][$i]
					echo "<td style='text-align: center;'><span class='bulletin'><b>".nf($tab_bull['moy_generale_classe_noncoef'])."</b></span></td>\n";
					echo "<td style='text-align: center;'><span class='bulletin'>".nf($tab_bull['moy_max_classe_noncoef'])."</span></td>\n";
				}
				else {
					echo "<td style='text-align: center;'><span class='bullminclasmax'>".nf($tab_bull['moy_min_classe_noncoef'])."</span><br />\n";
					//$tab_bull['moy_gen_classe'][$i]
					echo "<span class='bullminclasmax'><b>".nf($tab_bull['moy_generale_classe_noncoef'])."</b></span><br />\n";
					echo "<span class='bullminclasmax'>".nf($tab_bull['moy_max_classe_noncoef'])."</span></td>\n";
				}
	
				echo "<td style='text-align: center; font-weight:bold;'><span class='bulletin'>".nf($tab_bull['moy_gen_eleve_noncoef'][$i])."</span></td>\n";
	
				if ($tab_bull['affiche_graph']=='y') {
					echo "<td style='text-align: center;'>";
	
					/*
					if((isset($tab_bull['place_eleve_classe'][$i]))&&($tab_bull['place_eleve_classe'][$i]!="")) {
						//echo $place_eleve_classe[$i]." ";
						echo "<img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$tab_bull['place_eleve_classe'][$i].
							"&amp;temp1=".$tab_bull['quartile1_classe_gen'].
							"&amp;temp2=".$tab_bull['quartile2_classe_gen'].
							"&amp;temp3=".$tab_bull['quartile3_classe_gen'].
							"&amp;temp4=".$tab_bull['quartile4_classe_gen'].
							"&amp;temp5=".$tab_bull['quartile5_classe_gen'].
							"&amp;temp6=".$tab_bull['quartile6_classe_gen'].
							"&amp;nb_data=7' alt='Quartiles' />\n";
					}
					else {
					*/
						echo "-";
					//}
	
					echo "</td>\n";
				}
	
				// Rang
				if($tab_bull['affiche_rang']=='y') {
					echo "<td style='text-align: center;'><span class='bulletin'>";
					/*
					if(isset($tab_bull['rang_classe'][$i])) {
						echo $tab_bull['rang_classe'][$i]."/".$tab_bull['eff_classe'];
					}
					else {
					*/
						echo "-";
					//}
					echo "</span></td>\n";
				}
	
				echo "<td style='text-align: left;' colspan='2'><span class='bulletin'>\n";
				//echo $tab_bull['avis'][$i];
				echo "-";
				echo "</span></td>\n";
				echo "</tr>\n";
			}
		}
	}
}
// Fin de la partie moyenne générale avec coef 1
//===============================================================================

echo "</table>\n";

?>
