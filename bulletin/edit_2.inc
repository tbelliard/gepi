<?php
/*
 * @version: $Id$
 */

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
            echo "Bulletin N° ".$k."/".$nombre_eleves;
        }
        else{
            echo "Effectif : ".$nombre_eleves." élèves";
        }
        echo "</font></span></td>\n";
        if ($test_coef != 0 and $affiche_coef == "y"){
            echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan> <span class='bulletin'>Coef.</span></td>\n";
        }
    //========================================
    // AJOUT:boireaus
    if($affiche_nbdev=="y"){
        echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>Nb.dev</span></td>\n";
    }
    //========================================

        echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>&Eacute;lève</span></td>\n";
        if ($affiche_graph == 'y')  {
            //echo"</td>\n<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Niveaux<br />ABC<font size=\"-2\">+</font>C<font size=\"-2\">-</font>DE</span></td>\n";
            echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'>Niveaux<br />ABC<font size=\"-2\">+</font>C<font size=\"-2\">-</font>DE</span></td>\n";
        }
        if ($affiche_rang == 'y'){
            echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"$chaine_rowspan><span class='bulletin'><i>Rang</i></span></td>\n";
        }

        // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
        if ($bull_affiche_appreciations == 'y'){
            //echo "<td colspan=\"2\"><span class='bulletin'>Appréciation/Conseils</span></td>\n";
            echo "<td colspan=\"2\"$chaine_rowspan><span class='bulletin'>".$bull_intitule_app."</span></td>\n";
        }

		if($min_max_moyclas!=1){
	    //Eric Ajout
		//echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"colspan=\"3\"><span class='bulletin'>Pour la classe</span></td>\n";
		echo "<td style=\"text-align: center; \"colspan=\"3\"><span class='bulletin'>Pour la classe</span></td>\n";

		//Eric Suppr
		//echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Min</span></td>\n";
		//echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Max</span></td>\n";
		//echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Classe</span></td>\n";
	}
	else{
		// Min/Classe/Max en une seule colonne
		echo "<td style=\"width: ".$col_note_largeur."px; text-align: center;\"><span class='bulletin'>Classe m/C/M</span></td>\n";
	}
        echo "</tr>\n";

	    // Eric Ajout
	    if($min_max_moyclas!=1){
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
	    }
		*/
    echo "</thead>\n";

        // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
        if ($bull_affiche_aid == 'y') {
            // On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en tête des bulletins :
            //------------------------------
            $z=0;
            while ($z < $nb_aid_b) {
                $display_begin = @mysql_result($call_data_aid_b, $z, "display_begin");
                $display_end = @mysql_result($call_data_aid_b, $z, "display_end");
                if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
                    $indice_aid = @mysql_result($call_data_aid_b, $z, "indice_aid");
                    $aid_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
                    $aid_id = @mysql_result($aid_query, 0, "id_aid");
                    if ($aid_id != '') {
                        //affich_aid($affiche_graph, $affiche_rang, $affiche_coef,$test_coef,$indice_aid,$aid_id,$current_eleve_login[$i],$periode_num,$id_classe, 'bulletin');
                        affich_aid2($affiche_graph, $affiche_rang, $affiche_coef,$test_coef,$affiche_nbdev,$indice_aid,$aid_id,$current_eleve_login[$i],$periode_num,$id_classe, 'bulletin');
                    }
                }
                $z++;
            }
        }



        // On va créer une nouvelle boucle interne : la boucle 'matière'
        //------------------------------
        $j = 0;
        $prev_cat_id = null;
        while ($j < $nombre_groupes) {
            // On teste si des notes de une ou plusieurs boites du carnet de notes doivent être affichée
            $test_cn = mysqli_query($GLOBALS["___mysqli_ston"], "select distinct c.nom_court, c.id, nc.note from cn_cahier_notes cn, cn_conteneurs c, cn_notes_conteneurs nc
            where (
            cn.periode = '".$periode_num."' and
            cn.id_groupe='".$current_group[$j]["id"]."' and
            cn.id_cahier_notes = c.id_racine and
            c.id_racine!=c.id and
            nc.id_conteneur = c.id and
            nc.statut ='y' and
            nc.login='".$current_eleve_login[$i]."' and
            c.display_bulletin = 1
            ) ");
            $nb_ligne_cn = mysqli_num_rows($test_cn);
            $n = 0;
            while ($n < $nb_ligne_cn) {
                $cn_id[$n] = mysql_result($test_cn, $n, 'c.id');
                $cn_nom[$n] = mysql_result($test_cn, $n, 'c.nom_court');
                $cn_note[$n] = @mysql_result($test_cn, $n ,'nc.note');
                $cn_note[$n] = number_format($cn_note[$n],1, ',', ' ');
                $n++;
            }
            $nb_ligne_par_matiere = max($nb_ligne_cn,1) ;

			// ERIC : Ajout booleen pour n'afficher qu'une seule fois les moyennes min, max et moy pour la classe lorsqu'il y a des sous matières
			$impression_une_ligne_par_matiere = false;

            // Si l'élève suit bien cette matière ou pas
            //-----------------------------
            if (in_array($current_eleve_login[$i], $current_group[$j]["eleves"][$periode_num]["list"])) {
                // On appelle les résultats :
                $current_matiere_note_min = sql_query1("SELECT MIN(note) note_min FROM matieres_notes WHERE (id_groupe='".$current_group[$j]["id"]."' AND periode='$periode_num' AND statut ='')");
                $current_matiere_note_max = sql_query1("SELECT MAX(note) note_max FROM matieres_notes WHERE (id_groupe='".$current_group[$j]["id"]."' AND periode='$periode_num' AND statut ='')");
                $current_eleve_appreciation = sql_query1("SELECT appreciation FROM matieres_appreciations WHERE (login='".$current_eleve_login[$i]."' AND id_groupe='".$current_group[$j]["id"]."' AND periode='$periode_num')");



        //==========================================
        // AJOUT: boireaus
        // Nombre de contrôles
        $sql="SELECT cnd.note, cd.note_sur FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$current_eleve_login[$i]."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num' AND cnd.statut='';";
        //echo "\n<!--sql=$sql-->\n";
        $result_nbct=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        $current_eleve_nbct=mysqli_num_rows($result_nbct);

	//++++++++++++++++++++++++
	// Modif d'après F.Boisson
	// on prend les notes dans $string_notes
	$string_notes='';
	if ($result_nbct ) {
		while ($snnote =  mysqli_fetch_assoc($result_nbct)) {
			if ($string_notes != '') $string_notes .= ", ";
			$string_notes .= $snnote['note'];
			if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
				$string_notes .= "/".$snnote['note_sur'];
			}
		}
	}
	$current_eleve_appreciation = str_replace('@@Notes', $string_notes,$current_eleve_appreciation);
	//++++++++++++++++++++++++

        // Nombre total de devoirs:
        $sql="SELECT cd.id FROM cn_devoirs cd, cn_cahier_notes ccn WHERE cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group[$j]["id"]."' AND ccn.periode='$periode_num';";
        //echo "\n<!--sql=$sql-->\n";
        $result_nbct_tot=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        $current_matiere_nbct=mysqli_num_rows($result_nbct_tot);
        //==========================================



                if ($affiche_categories) {
                // On regarde si on change de catégorie de matière
                    if ($current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"] != $prev_cat_id) {
                        $prev_cat_id = $current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"];
                        // On est dans une nouvelle catégorie
                        // On récupère les infos nécessaires, et on affiche une ligne
                        $cat_name = html_entity_decode(mysql_result(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT nom_complet FROM matieres_categories WHERE id = '" . $current_group[$j]["classes"]["classes"][$id_classe]["categorie_id"] . "'"), 0));
                        // On détermine le nombre de colonnes pour le colspan


			if($min_max_moyclas!=1){
				// Eric Modif avec min max àprès appreciations 5 ==>6
				$nb_total_cols = 6;
				$nb_first_cols = 1;
			}
			else{
				//$nb_total_cols = 3;
				$nb_total_cols = 3;
				$nb_first_cols = 1;
			}
                        //$nb_last_cols = 0;
                        $nb_last_cols = 1;
                        if ($test_coef != 0 and $affiche_coef == "y") { $nb_total_cols++; $nb_first_cols++;}

            echo "<!-- \n";
            echo "\$test_coef=$test_coef\n";
            echo "\$affiche_coef=$affiche_coef\n";
            echo "\$nb_total_cols=$nb_total_cols\n";
            echo "\$nb_first_cols=$nb_first_cols\n";
            echo " -->\n";

            //==========================================
            // AJOUT: boireaus
                        if ($affiche_nbdev == 'y') {$nb_total_cols++; $nb_first_cols++;}
            //==========================================

                        if ($affiche_graph == 'y') {$nb_total_cols++; $nb_last_cols++;}
                        if ($affiche_rang == 'y')  {$nb_total_cols++; $nb_last_cols++;}
                        if ($bull_affiche_appreciations == 'y') {$nb_total_cols++; $nb_last_cols++;}

                        // On regarde s'il faut afficher la moyenne de l'élève pour cette catégorie
		                $affiche_cat_moyenne_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT affiche_moyenne FROM j_matieres_categories_classes WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $prev_cat_id . "')");
		                if (mysqli_num_rows($affiche_cat_moyenne_query) == "0") {
		                	$affiche_cat_moyenne = false;
		                } else {
		                	$affiche_cat_moyenne = mysql_result($affiche_cat_moyenne_query, 0);
		                }

                        // On a toutes les infos. On affiche !
                        //echo "<tr>\n";
			if($bull_categ_bgcolor!=''){
	                        echo "<tr bgcolor='".$bull_categ_bgcolor."'>\n";
			}
			else{
	                        echo "<tr>\n";
			}
                        if ($affiche_cat_moyenne == "1") {
				echo "<td colspan='" . $nb_first_cols . "'>";
                        } else {
                            echo "<td colspan='" . $nb_total_cols . "'>";
                        }
                        echo "<p style='padding: 0; margin:0; font-size: ".$bull_categ_font_size."px;'>".$cat_name."</p></td>\n";
                        if ($affiche_cat_moyenne == "1") {
				echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center'>".nf($moy_cat_eleve[$i][$prev_cat_id])."</td>\n"; // moy elv

				echo "<td colspan='" . $nb_last_cols . "'>&nbsp;</td>\n";

				//ERIC  Modif pour les cas où les moy sont regroupées en fin de bulletin dans une seule cas
				if($min_max_moyclas!=1){
				  echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>"."-"."</td>\n"; // max classe
				  echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>".nf($moy_cat_classe[$i][$prev_cat_id])."</td>\n"; //moy classe
				  echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>"."-"."</td>\n"; // max classe
                }
				else {
				  echo "<td style='padding: 0; margin: 0; font-size: ".$bull_categ_font_size."px; text-align: center;'>".nf($moy_cat_classe[$i][$prev_cat_id])."</td>\n"; //moy classe
				}

                        }
                        echo "</tr>\n";
                    }
                }
                for ($cn=0; $cn<$nb_ligne_par_matiere; $cn++) {
                    echo "<tr>\n";
                    if ($cn == 0) {
                        //echo "<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".$col_matiere_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\"><span class='bulletin'><b>".$current_matiere_nom_complet[$j]."</b>";
                        echo "<td style=\"height: ".$col_hauteur."px; width: ".$col_matiere_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\"><span class='bulletin'><b>".htmlspecialchars($current_matiere_nom_complet[$j])."</b>";
                        $k = 0;
                        foreach($current_group[$j]["profs"]["list"] as $current_prof_login) {
                            echo "<br /><i>".affiche_utilisateur($current_prof_login,$id_classe)."</i>";
                        }
                        echo "</span></td>\n";

            if ($test_coef != 0 and $affiche_coef == "y")  {
                            if ($current_eleve_coef[$j][$i] > 0){$print_coef= $current_eleve_coef[$j][$i];}else{$print_coef='-';}
                            echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\"><span class='bulletin'>".$print_coef."</span></td>\n";
                        }


            //=========================================
            // AJOUT: boireaus
            if($affiche_nbdev=="y"){
                // Nombre de contrôles:
                echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\">";
                //echo "<span class='bulletin'>$current_eleve_nbct</span>";
                echo "<span class='bulletin'>$current_eleve_nbct/$current_matiere_nbct</span>";
                echo "</td>\n";
            }
            //=========================================

		echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\"><span class='bulletin'><b>";

                        $place_eleve = "";
                        $flag_moy = 'no';
                        if ($current_eleve_note[$j][$i] != '') {
                            if ($current_eleve_statut[$j][$i] != '') {
                                echo $current_eleve_statut[$j][$i];
                            } else {
                                if (($affiche_graph == 'y') and ($current_classe_matiere_moyenne[$j]!="")) {
                                    if ($current_eleve_note[$j][$i]<5) { $place_eleve=6;}
                                    if (($current_eleve_note[$j][$i]>=5) and ($current_eleve_note[$j][$i]<8))  { $place_eleve=5;}
                                    if (($current_eleve_note[$j][$i]>=8) and ($current_eleve_note[$j][$i]<10)) { $place_eleve=4;}
                                    if (($current_eleve_note[$j][$i]>=10) and ($current_eleve_note[$j][$i]<12)) {$place_eleve=3;}
                                    if (($current_eleve_note[$j][$i]>=12) and ($current_eleve_note[$j][$i]<15)) { $place_eleve=2;}
                                    if ($current_eleve_note[$j][$i]>=15) { $place_eleve=1;}
                                }
                                //$note=number_format($current_eleve_note[$j][$i],1, ',', ' ');
                                //if($note!="-") {$note=number_format($current_eleve_note[$j][$i],1, ',', ' ');}
                                if($current_eleve_note[$j][$i]!="-") {
									$note=number_format($current_eleve_note[$j][$i],1, ',', ' ');
								} else {
									$note="-";
								}
                                echo $note;
								// Est-il judicieux d'avoir ce $flag_moy ici même si la note est un '-'? A quoi sert-il?
                                $flag_moy = 'yes';
                            }
                        } else {
                            echo '-';
                        }
                        echo "</b></span></td>\n";

                        if ($affiche_graph == 'y') {
                            if ($current_classe_matiere_moyenne[$j]!="") {
                                $quartile1_classe = sql_query1("SELECT COUNT( note ) as quartile1 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note>=15)");
                                $quartile2_classe = sql_query1("SELECT COUNT( note ) as quartile2 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note>=12 AND note<15)");
                                $quartile3_classe = sql_query1("SELECT COUNT( note ) as quartile3 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note>=10 AND note<12)");
                                $quartile4_classe = sql_query1("SELECT COUNT( note ) as quartile4 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note>=8 AND note<10)");
                                $quartile5_classe = sql_query1("SELECT COUNT( note ) as quartile5 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note>=5 AND note<8)");
                                $quartile6_classe = sql_query1("SELECT COUNT( note ) as quartile6 FROM matieres_notes WHERE (periode='$periode_num' AND id_groupe='" . $current_group[$j]["id"] ."' AND statut ='' AND note<5)");
                                //echo "<td style=\"text-align: center; \" rowspan=\"".$nb_ligne_cn."\"><img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&amp;temp1=$quartile1_classe&amp;temp2=$quartile2_classe&amp;temp3=$quartile3_classe&amp;temp4=$quartile4_classe&amp;temp5=$quartile5_classe&amp;temp6=$quartile6_classe&amp;nb_data=7' alt='Quartiles' /></td>\n";
                                echo "<td style=\"text-align: center; \" rowspan=\"".$nb_ligne_par_matiere."\"><img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&amp;temp1=$quartile1_classe&amp;temp2=$quartile2_classe&amp;temp3=$quartile3_classe&amp;temp4=$quartile4_classe&amp;temp5=$quartile5_classe&amp;temp6=$quartile6_classe&amp;nb_data=7' alt='Quartiles' /></td>\n";
                            } else {
                                //echo "<td style=\"text-align: center; \" rowspan=\"".$nb_ligne_cn."\">-</td>\n";
                                echo "<td style=\"text-align: center; \" rowspan=\"".$nb_ligne_par_matiere."\">-</td>\n";
                            }
                        }

                        if ($affiche_rang == 'y')  {
                            $rang = sql_query1("select rang from matieres_notes where (
                            periode = '".$periode_num."' and
                            id_groupe = '".$current_group[$j]["id"]."' and
                            login = '".$current_eleve_login[$i]."')
                            ");
                            if (($rang == 0) or ($rang == -1)) $rang = "-"; else $rang.="/".$nb_notes[$current_group[$j]["id"]][$periode_num];
                            echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\"><span class='bulletin'><i>".$rang."</i></span>\n</td>\n";
                        }
                    }
                    // Cas où des notes de une ou plusieurs boites du carnet de notes doivent être affichées

                    // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
                    if ($bull_affiche_appreciations == 'y') {
                        if ($nb_ligne_cn == 0){
                            echo "<td colspan=\"2\" >";
                        } else {
                            // Il faudrait peut-être ci-dessous un htmlspecialchars() sur $cn_nom[$cn]
                            echo "<td style=\"width: ".$col_boite_largeur."px;\">
                            <span class='bulletin'>".$cn_nom[$cn]."&nbsp;:&nbsp;".$cn_note[$cn]."</span></td>\n";
                            if ($cn == 0){echo "<td rowspan=\"".$nb_ligne_cn."\">";}
                        }
                    }
                    // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
                    if (($cn == 0) and  ($bull_affiche_appreciations == 'y')) {
                        /*
                        if ($current_eleve_appreciation) {
                            echo "<span class='bulletin'>$current_eleve_appreciation</span>";
                        } else {
                            echo "-";
                        }
                        */
                        if ($current_eleve_appreciation) {
                            if ($current_eleve_appreciation=="-1") {
                                //echo "<span class='bulletin'><span class='noprint'>$current_eleve_appreciation</span></span>";
                                echo "<span class='bulletin'><span class='noprint'>-</span></span>";
                            }
                            else{
                                if((strstr($current_eleve_appreciation,">"))||(strstr($current_eleve_appreciation,"<"))){
                                    echo "<span class='bulletin'>$current_eleve_appreciation</span>";
                                }
                                else{
                                    echo "<span class='bulletin'>".nl2br($current_eleve_appreciation)."</span>";
                                }
                            }
                        } else {
                            //echo "-";
                            echo "<span class='noprint'>-</span>";
                        }
                        echo "</td>\n";
                    }
	if (!$impression_une_ligne_par_matiere)	{
        //Eric Modif ordre max moy min
		if($min_max_moyclas!=1){
			// Colonne Note minimale:
			echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\">";
			if ($current_matiere_note_min != '-1') {
				$note=number_format($current_matiere_note_min,1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bulletin'>$note</span>";
			echo "</td>\n";

			// Colonne Moyenne Classe:
			echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\">";
			if ($current_classe_matiere_moyenne[$j] != '') {
				$note=number_format($current_classe_matiere_moyenne[$j],1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bulletin'>$note</span>";
			echo "</td>\n";


			// Colonne Note maximale:
			echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\">";
			if ($current_matiere_note_max != '-1') {
				$note=number_format($current_matiere_note_max,1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bulletin'>$note</span>";
			echo "</td>\n";

		}
		else{
			// Colonne Notes de la classe: min, classe et max en une seule colonne
			echo "<td style=\"text-align: center; width: ".$col_note_largeur."px;\" rowspan=\"".$nb_ligne_par_matiere."\">";

			// Ligne Moyenne minimale:
			if ($current_matiere_note_min != '-1') {
				$note=number_format($current_matiere_note_min,1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bullminclasmax'>$note</span><br />\n";

			// Ligne moyenne classe:
			if ($current_classe_matiere_moyenne[$j] != '') {
				$note=number_format($current_classe_matiere_moyenne[$j],1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bullminclasmax'>$note</span><br />\n";


			// Ligne Moyenne maximale:
			if ($current_matiere_note_max != '-1') {
				$note=number_format($current_matiere_note_max,1, ',', ' ');
			} else {
				$note = '-';
			}
			echo "<span class='bullminclasmax'>$note</span>";
			echo "</td>\n";
		}
    }
	//ERIC Ajout
    $impression_une_ligne_par_matiere = true;

                    echo "</tr>\n";
                }
            }
            $j++;
        }



        // Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
        if ($bull_affiche_aid == 'y') {
            // On attaque maintenant l'affichage des appréciations des Activités Interdisciplinaires devant apparaître en fin des bulletins :
            //------------------------------
            $z=0;
            while ($z < $nb_aid_e) {
                $display_begin = @mysql_result($call_data_aid_e, $z, "display_begin");
                $display_end = @mysql_result($call_data_aid_e, $z, "display_end");
                if (($periode_num >= $display_begin) and ($periode_num <= $display_end)) {
                    $indice_aid = @mysql_result($call_data_aid_e, $z, "indice_aid");
                    $aid_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_aid FROM j_aid_eleves WHERE (login='".$current_eleve_login[$i]."' and indice_aid='$indice_aid')");
                    $aid_id = @mysql_result($aid_query, 0, "id_aid");
                    if ($aid_id != '') {
			//affich_aid($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$indice_aid,$aid_id,$current_eleve_login[$i],$periode_num,$id_classe, 'bulletin');
			affich_aid2($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid,$aid_id,$current_eleve_login[$i],$periode_num,$id_classe, 'bulletin');
                    }
                }
                $z++;
            }
        }


	//if($ne_pas_afficher_moy_gen==""){
	if($display_moy_gen=="y"){
		// Affichage des moyennes générales
		if ($test_coef != 0) {
			//if ($total_coef != 0) {
			if ($total_coef_eleve[$i] != 0) {
				echo "<tr>\n<td style=\"width: ".$col_matiere_largeur."px;\"><span class='bulletin'><b>Moyenne générale</b></span></td>\n";

				if ($affiche_coef == "y"){echo "<td style=\"text-align: center;\">-</td>\n";}
				if ($affiche_nbdev == "y"){echo "<td style=\"text-align: center;\">-</td>\n";}

				// Colonne Moyenne générale d'élève:
				echo "<td style=\"text-align: center;\">";
				echo "<span class='bulletin'><b>".nf($moy_gen_eleve[$i])."</b></span></td>\n";
				if ($affiche_graph == 'y')  {
					if (($quartile1_classe_gen != 0)  or ($quartile2_classe_gen != 0) or ($quartile3_classe_gen != 0) or ($quartile4_classe_gen != 0) or ($quartile5_classe_gen != 0) or ($quartile6_classe_gen != 0)){
						echo "<td style=\"text-align: center;\"><img height='40' width='40' src='../visualisation/draw_artichow4.php?place_eleve=".$place_eleve_classe[$i]."&amp;temp1=$quartile1_classe_gen&amp;temp2=$quartile2_classe_gen&amp;temp3=$quartile3_classe_gen&amp;temp4=$quartile4_classe_gen&amp;temp5=$quartile5_classe_gen&amp;temp6=$quartile6_classe_gen&amp;nb_data=7' alt='Quartiles' /></td>\n";
					}
					else{
						echo "<td style=\"text-align: center;\"><span class='bulletin'>-</span></td>\n";
					}
				}

				if ($affiche_rang == 'y')  {
					$rang = sql_query1("select rang from j_eleves_classes where (
					periode = '".$periode_num."' and
					id_classe = '".$id_classe."' and
					login = '".$current_eleve_login[$i]."' )
					");
				if (($rang == 0) or ($rang == -1)){$rang = "-";}else{$rang .="/".$nombre_eleves;}
					echo "<td style=\"text-align: center;\"><span class='bulletin'>".$rang."</span></td>\n";
				}

				// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
				if ($bull_affiche_appreciations == 'y'){
					//echo "<td colspan=\"2\"><span class='bulletin'>-</span></td>\n</tr>\n";
					echo "<td colspan=\"2\"><span class='bulletin'>-</span></td>\n";
				}
/*
                //Eric Modif ordre max ; moy ; min + affichage min et max de la moyenne de la classe après les appréciations
				if($min_max_moyclas!=1){
					// Colonne Min:
					echo "<td style=\"text-align: center;\">";
					echo "<span class='bulletin'>".min($moy_gen_eleve)."</span>";
				    echo "</td>\n";
				}

				// Colonne Moyenne générale de la classe:
				echo "<td style=\"text-align: center;\">";
				echo "<span class='bulletin'>$moy_gen_classe[$i]</span>";
				echo "</td>\n";

				if($min_max_moyclas!=1){
					// Colonne Max:
				    echo "<td style=\"text-align: center;\">";
					echo "<span class='bulletin'>".max($moy_gen_eleve)."</span>";
				    echo "</td>\n";
				}
*/
               //Eric Modif ordre max ; moy ; min + affichage min et max de la moyenne de la classe après les appréciations
				if($min_max_moyclas!=1){
					// Colonne Min:
					echo "<td style=\"text-align: center;\">";
					echo "<span class='bulletin'>".nf($moy_min_classe)."</span>";
				    echo "</td>\n";


				// Colonne Moyenne générale de la classe:
				echo "<td style=\"text-align: center;\">";
				echo "<span class='bulletin'>".nf($moy_generale_classe)."</span>";
				echo "</td>\n";


					// Colonne Max:
				    echo "<td style=\"text-align: center;\">";
					echo "<span class='bulletin'>".nf($moy_max_classe)."</span>";
				    echo "</td>\n";
				} else {
				    //Eric Ajout colonne min classe max une seule cellule
				    echo "<td style=\"text-align: center;\">";
				    echo "<span class='bullminclasmax'>".nf($moy_min_classe)."</span><br />\n";
					echo "<span class='bullminclasmax'>".nf($moy_generale_classe)."</span><br />\n";
					echo "<span class='bullminclasmax'>".nf($moy_max_classe)."</span><br />\n";
					echo "</td>\n";
				}

				echo "</tr>\n";
			}
		}
	}
        echo "</table>\n";

?>
