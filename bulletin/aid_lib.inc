<?php
/*
*/

function affich_aid0($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid, $aid_id,$current_eleve_login,$periode_num,$id_classe,$style_bulletin) {
    //============================
    // AJOUT: boireaus
    global $min_max_moyclas;
    //============================

    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = $indice_aid");
    $AID_NOM_COMPLET = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_end = @mysql_result($call_data, 0, "display_end");


    $aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
    $aid_nom = @mysql_result($aid_nom_query, 0, "nom");
    //------
    // On regarde maintenant quelle sont les profs responsables de cette AID
    $aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
    $nb_lig = mysql_num_rows($aid_prof_resp_query);
    $n = '0';
    while ($n < $nb_lig) {
        $aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
        $n++;
    }
    //------
    // On appelle l'appréciation de l'élève, et sa note
    //------
    $current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    $current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $periode_max = mysql_num_rows($periode_query);
    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
        $place_eleve = "";
        $current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
        $current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
        if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
            $current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
        }
        if ($current_eleve_aid_note == '') {
            $current_eleve_aid_note = '-';
        } else {
            if ($affiche_graph == 'y')  {
                if ($current_eleve_aid_note<5) { $place_eleve=6;}
                if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
                if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
                if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
                if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
                if ($current_eleve_aid_note>=15) { $place_eleve=1;}
            }
            $current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
        }
        $aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

        $aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
        if ($aid_note_min == '') {
            $aid_note_min = '-';
        } else {
            $aid_note_min=number_format($aid_note_min,1, ',', ' ');
        }
        $aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

        if ($aid_note_max == '') {
            $aid_note_max = '-';
        } else {
            $aid_note_max=number_format($aid_note_max,1, ',', ' ');
        }

        $aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
        if ($aid_note_moyenne == '') {
            $aid_note_moyenne = '-';
        } else {
            $aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
        }

    }
    //------
    // On affiche l'appréciation aid :
    //------
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><b>$AID_NOM_COMPLET</b><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<i>".$chaine_prof."</i>";
    }
    echo "</span></td>\n";
    if ($test_coef != 0 AND $affiche_coef == "y") echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";

    if ($affiche_nbdev=="y"){echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";}

    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";
	}
	else{
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='bullminclasmax'>$aid_note_min<br />\n";
		echo "$aid_note_max<br />\n";
		echo "$aid_note_moyenne</span></td>\n";
	}
	//==========================

	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
	echo "<span class='$style_bulletin'><b>";
	if ($current_eleve_aid_statut == '') {
		echo $current_eleve_aid_note;
	} else if ($current_eleve_aid_statut == 'other') {
		echo "-";
	} else {
		echo $current_eleve_aid_statut;
	}
	echo "</b></span></td>\n";
    } else {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	else{
		// On ne met pas trois tirets.
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	//==========================
	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
    }
    if ($affiche_graph == 'y')  {
      if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid)))  {
        $quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
        $quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
        $quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
        $quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
        $quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
        $quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");
        echo "<td style=\"text-align: center; \"><img height=40 width=40 src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&amp;temp1=$quartile1_classe&amp;temp2=$quartile2_classe&amp;temp3=$quartile3_classe&amp;temp4=$quartile4_classe&amp;temp5=$quartile5_classe&amp;temp6=$quartile6_classe&amp;nb_data=7' /></td>\n";
     } else
      echo "<td style=\"text-align: center; \"><span class='".$style_bulletin."'>-</span></td>\n";
    }
    if ($affiche_rang == 'y') echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";
    if (getSettingValue("bull_affiche_appreciations") == 'y') {
        echo "<td style=\"\" colspan=\"2\"><span class='$style_bulletin'>";
        if (($message != '') or ($display_nom == 'y')) {
            echo "$message ";
            if ($display_nom == 'y') {echo "<b>$aid_nom</b><br />";}
        }
    }
    echo "$current_eleve_aid_appreciation</span></td>\n</tr>\n";
    //------
}


function affich_aid1($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid, $aid_id,$current_eleve_login,$periode_num,$id_classe,$style_bulletin) {
    //============================
    // AJOUT: boireaus
    global $min_max_moyclas;
    //============================

    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = $indice_aid");
    $AID_NOM_COMPLET = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_end = @mysql_result($call_data, 0, "display_end");


    $aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
    $aid_nom = @mysql_result($aid_nom_query, 0, "nom");
    //------
    // On regarde maintenant quelle sont les profs responsables de cette AID
    $aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
    $nb_lig = mysql_num_rows($aid_prof_resp_query);
    $n = '0';
    while ($n < $nb_lig) {
        $aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
        $n++;
    }
    //------
    // On appelle l'appréciation de l'élève, et sa note
    //------
    $current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    $current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $periode_max = mysql_num_rows($periode_query);
    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
        $place_eleve = "";
        $current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
        $current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
        if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
            $current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
        }
        if ($current_eleve_aid_note == '') {
            $current_eleve_aid_note = '-';
        } else {
            if ($affiche_graph == 'y')  {
                if ($current_eleve_aid_note<5) { $place_eleve=6;}
                if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
                if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
                if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
                if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
                if ($current_eleve_aid_note>=15) { $place_eleve=1;}
            }
            $current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
        }
        $aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

        $aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
        if ($aid_note_min == '') {
            $aid_note_min = '-';
        } else {
            $aid_note_min=number_format($aid_note_min,1, ',', ' ');
        }
        $aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

        if ($aid_note_max == '') {
            $aid_note_max = '-';
        } else {
            $aid_note_max=number_format($aid_note_max,1, ',', ' ');
        }

        $aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
        if ($aid_note_moyenne == '') {
            $aid_note_moyenne = '-';
        } else {
            $aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
        }

    }
    //------
    // On affiche l'appréciation aid :
    //------
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><b>$AID_NOM_COMPLET</b><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<i>".$chaine_prof."</i>";
    }
    echo "</span></td>\n";
    if ($test_coef != 0 AND $affiche_coef == "y") echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";

    if ($affiche_nbdev=="y"){echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";}

    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";
	}
	else{
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='bullminclasmax'>$aid_note_min<br />\n";
		echo "$aid_note_moyenne<br />\n";
		echo "$aid_note_max</span></td>\n";
	}
	//==========================

	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
	echo "<span class='$style_bulletin'><b>";
	if ($current_eleve_aid_statut == '') {
		echo $current_eleve_aid_note;
	} else if ($current_eleve_aid_statut == 'other') {
		echo "-";
	} else {
		echo $current_eleve_aid_statut;
	}
	echo "</b></span></td>\n";
    } else {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	else{
		// On ne met pas trois tirets.
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	//==========================
	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
    }
    if ($affiche_graph == 'y')  {
      if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid)))  {
        $quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
        $quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
        $quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
        $quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
        $quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
        $quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");
        echo "<td style=\"text-align: center; \"><img height=40 width=40 src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&amp;temp1=$quartile1_classe&amp;temp2=$quartile2_classe&amp;temp3=$quartile3_classe&amp;temp4=$quartile4_classe&amp;temp5=$quartile5_classe&amp;temp6=$quartile6_classe&amp;nb_data=7' /></td>\n";
     } else
      echo "<td style=\"text-align: center; \"><span class='".$style_bulletin."'>-</span></td>\n";
    }
    if ($affiche_rang == 'y') echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";
    if (getSettingValue("bull_affiche_appreciations") == 'y') {
        echo "<td style=\"\" colspan=\"2\"><span class='$style_bulletin'>";
        if (($message != '') or ($display_nom == 'y')) {
            echo "$message ";
            if ($display_nom == 'y') {echo "<b>$aid_nom</b><br />";}
        }
    }
    echo "$current_eleve_aid_appreciation</span></td>\n</tr>\n";
    //------
}




function affich_aid2($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid, $aid_id,$current_eleve_login,$periode_num,$id_classe,$style_bulletin) {
    //============================
    // AJOUT: boireaus
    global $min_max_moyclas;
    //============================

    $call_data = mysql_query("SELECT * FROM aid_config WHERE indice_aid = $indice_aid");
    $AID_NOM_COMPLET = @mysql_result($call_data, 0, "nom_complet");
    $note_max = @mysql_result($call_data, 0, "note_max");
    $type_note = @mysql_result($call_data, 0, "type_note");
    $message = @mysql_result($call_data, 0, "message");
    $display_nom = @mysql_result($call_data, 0, "display_nom");
    $display_end = @mysql_result($call_data, 0, "display_end");


    $aid_nom_query = mysql_query("SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
    $aid_nom = @mysql_result($aid_nom_query, 0, "nom");
    //------
    // On regarde maintenant quelle sont les profs responsables de cette AID
    $aid_prof_resp_query = mysql_query("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
    $nb_lig = mysql_num_rows($aid_prof_resp_query);
    $n = '0';
    while ($n < $nb_lig) {
        $aid_prof_resp_login[$n] = mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
        $n++;
    }
    //------
    // On appelle l'appréciation de l'élève, et sa note
    //------
    $current_eleve_aid_appreciation_query = mysql_query("SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    $current_eleve_aid_appreciation = @mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
    $periode_query = mysql_query("SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $periode_max = mysql_num_rows($periode_query);
    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
        $place_eleve = "";
        $current_eleve_aid_note = @mysql_result($current_eleve_aid_appreciation_query, 0, "note");
        $current_eleve_aid_statut = @mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
        if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
            $current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
        }
        if ($current_eleve_aid_note == '') {
            $current_eleve_aid_note = '-';
        } else {
            if ($affiche_graph == 'y')  {
                if ($current_eleve_aid_note<5) { $place_eleve=6;}
                if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
                if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
                if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
                if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
                if ($current_eleve_aid_note>=15) { $place_eleve=1;}
            }
            $current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
        }
        $aid_note_min_query = mysql_query("SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

        $aid_note_min = @mysql_result($aid_note_min_query, 0, "note_min");
        if ($aid_note_min == '') {
            $aid_note_min = '-';
        } else {
            $aid_note_min=number_format($aid_note_min,1, ',', ' ');
        }
        $aid_note_max_query = mysql_query("SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_max = @mysql_result($aid_note_max_query, 0, "note_max");

        if ($aid_note_max == '') {
            $aid_note_max = '-';
        } else {
            $aid_note_max=number_format($aid_note_max,1, ',', ' ');
        }

        $aid_note_moyenne_query = mysql_query("SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_moyenne = @mysql_result($aid_note_moyenne_query, 0, "moyenne");
        if ($aid_note_moyenne == '') {
            $aid_note_moyenne = '-';
        } else {
            $aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
        }

    }
    //------
    // On affiche l'appréciation aid :
    //------
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><b>$AID_NOM_COMPLET</b><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<i>".$chaine_prof."</i>";
    }
    echo "</span></td>\n";
    if ($test_coef != 0 AND $affiche_coef == "y") echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";

    if ($affiche_nbdev=="y"){echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";}

    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
/*	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";
	}
	else{
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='bullminclasmax'>$aid_note_min<br />\n";
		echo "$aid_note_moyenne<br />\n";
		echo "$aid_note_max</span></td>\n";
	}
	//==========================
*/
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'><b>";
		if ($current_eleve_aid_statut == '') {
			echo $current_eleve_aid_note;
		} else if ($current_eleve_aid_statut == 'other') {
			echo "-";
		} else {
			echo $current_eleve_aid_statut;
		}
		echo "</b></span></td>\n";



    } else {
/*	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	else{
		// On ne met pas trois tirets.
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	//==========================
	*/

	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
    }
    if ($affiche_graph == 'y')  {
      if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid)))  {
        $quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
        $quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
        $quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
        $quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
        $quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
        $quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");
        echo "<td style=\"text-align: center; \"><img height=40 width=40 src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&amp;temp1=$quartile1_classe&amp;temp2=$quartile2_classe&amp;temp3=$quartile3_classe&amp;temp4=$quartile4_classe&amp;temp5=$quartile5_classe&amp;temp6=$quartile6_classe&amp;nb_data=7' /></td>\n";
     } else
      echo "<td style=\"text-align: center; \"><span class='".$style_bulletin."'>-</span></td>\n";
    }
    if ($affiche_rang == 'y') echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";
    if (getSettingValue("bull_affiche_appreciations") == 'y') {
        echo "<td style=\"\" colspan=\"2\"><span class='$style_bulletin'>";
        if (($message != '') or ($display_nom == 'y')) {
            echo "$message ";
            if ($display_nom == 'y') {echo "<b>$aid_nom</b><br />";}
        }
    }
    echo "$current_eleve_aid_appreciation</span></td>\n";

	if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {

			if($min_max_moyclas!=1){
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
			echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
			echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";

			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
			echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";
		}
		else{
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
			echo "<span class='bullminclasmax'>$aid_note_min<br />\n";
			echo "$aid_note_moyenne<br />\n";
			echo "$aid_note_max</span></td>\n";
		}

	}
	else {
    	if($min_max_moyclas!=1){
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		}
		else{
			// On ne met pas trois tirets.
			echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		}
    }








	echo "</tr>\n";
    //------
}
?>