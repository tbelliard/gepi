<?php
	/*
	*/

	$tab_param_classe=array();
	echo "<table class='boireaus' border='1' summary='Tableau des items'>\n";
	echo "<tr>\n";
	//echo "<th width='30%'>Item</th>\n";
	echo "<th>Item</th>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<th>".get_class_from_id($tab_id_classe[$i])."</th>\n";
		$tab_param_classe[$i]=getAllParamClasse($tab_id_classe[$i]);
	}
	echo "<th>\n";
	echo "<a href=\"javascript:ToutCocher()\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:ToutDeCocher()\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</th>\n";
	echo "</tr>\n";

	$gepiProfSuivi=getSettingValue("gepi_prof_suivi");

	$tab_js_lignes_specifiques_releve_html="var tab_html=new Array('rn_col_moy', 'rn_sign_chefetab', 'rn_abs_2');";
	$tab_js_lignes_specifiques_releve_pdf="var tab_pdf=new Array('rn_aff_classe_nom', 'rn_rapport_standard_min_font', 'rn_bloc_obs');";

	$tab_item=array();
	$tab_item[]='rn_nomdev';
	$tab_traduc['rn_nomdev']="Avec le nom des devoirs";
	$tab_item[]='rn_toutcoefdev';
	$tab_traduc['rn_toutcoefdev']="Avec coefficients";
	$tab_item[]='rn_coefdev_si_diff';
	$tab_traduc['rn_coefdev_si_diff']="Avec coefficients s'il y a plusieurs coefficients différents";
	//$tab_item[]='rn_app';
	//$tab_traduc['rn_app']="Avec l'appréciation (sous réserve d'autorisation par le professeur)";
	$tab_item[]='rn_datedev';
	$tab_traduc['rn_datedev']="Avec les dates";

	// SELON LE STATUT: Accès ou pas
	if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesColMoyReleveEleve')))||
		(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesColMoyReleveParent')))
		) {
		$tab_item[]='rn_col_moy';
		$tab_traduc['rn_col_moy']="Avec la colonne moyenne (<em>relevé HTML</em>)<br />(<em style='font-size:small'>sur une période seulement, pas sur un intervalle de dates</em>)";
	}

	$tab_item[]='rn_sign_chefetab';
	$tab_traduc['rn_sign_chefetab']="Avec case pour signature du chef d'établissement (<em>relevé HTML</em>)";
	$tab_item[]='rn_sign_pp';
	$tab_traduc['rn_sign_pp']="Avec case pour signature du $gepiProfSuivi";
	$tab_item[]='rn_sign_resp';
	$tab_traduc['rn_sign_resp']="Avec case pour signature des responsables";

	if(getSettingValue("active_module_absence")=='2') {
		$tab_item[]='rn_abs_2';
		$tab_traduc['rn_abs_2']="Afficher les absences (<em>ABS2 et relevé HTML</em>)";
	}

	/*
	$tab_item[]='rn_sign_nblig';
	$tab_traduc['rn_sign_nblig']="Nombre de lignes pour la signature";
	$tab_item[]='rn_formule';
	$tab_traduc['rn_formule']="Formule à afficher en bas de page";
	*/
	// Il manque $avec_appreciation_devoir
	$chaine_coef="coef.: ";

	//++++++++++++
	// A REVOIR: ON FAIT LES MEMES REQUETES A PLUSIEURS REPRISES...
	//++++++++++++

	$alt=1;
	// Affichage du nom de la classe Nom long  Nom court  Nom long (Nom court)
	//$alt=$alt*(-1);
	echo "<tr id='tr_rn_aff_classe_nom' class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>Affichage du nom de la classe (<em>relevé PDF</em>)<br />\n";
	echo "Nom long (1) / Nom court (2) / Nom court (Nom long) (3)";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<label for='rn_aff_classe_nom_".$i."_1' class='invisible'>Nom long</label>
		<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_1' value='1' ";
		if((!isset($tab_param_classe[$i]['rn_aff_classe_nom']))||($tab_param_classe[$i]['rn_aff_classe_nom']=='1')) {
			echo "checked='checked' ";
		}
		echo "/><br />\n";
		echo "<label for='rn_aff_classe_nom_".$i."_2' class='invisible'>Nom long</label>
		<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_2' value='2' ";
		if((isset($tab_param_classe[$i]['rn_aff_classe_nom']))&&($tab_param_classe[$i]['rn_aff_classe_nom']=='2')) {
			echo "checked='checked' ";
		}
		echo "/><br />\n";
		echo "<label for='rn_aff_classe_nom_".$i."_3' class='invisible'>Nom long</label>
		<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_3' value='3' ";
		if((isset($tab_param_classe[$i]['rn_aff_classe_nom']))&&($tab_param_classe[$i]['rn_aff_classe_nom']=='3')) {
			echo "checked='checked' ";
		}
		echo "/>\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	//echo "&nbsp;";
	echo "Nom long<br />\n";
	echo "Nom court<br />\n";
	echo "Nom court (Nom long)\n";
	echo "</td>\n";
	echo "</tr>\n";

	// A changer: il vaudrait mieux lister les paramètres correspondant à des champs de la table 'classes' (on ne devrait plus en ajouter)
	$tab_param_table_classes_param=array('rn_aff_classe_nom','rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne','rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy');

	for($k=0;$k<count($tab_item);$k++) {
		$affiche_ligne="y";
		if ((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&(preg_match("/^rn_sign/",$tab_item[$k]))) {
			$affiche_ligne="n";
		}

		if($affiche_ligne=="y") {
			$alt=$alt*(-1);
			echo "<tr id='tr_".$tab_item[$k]."' class='lig$alt white_hover'>\n";
			echo "<td style='text-align:left;'>".$tab_traduc[$tab_item[$k]]."\n";
			echo "</td>\n";

			for($i=0;$i<count($tab_id_classe);$i++) {
				echo "<td>\n";
				echo "<label for='".$tab_item[$k]."_".$i."' class='invisible'>".$tab_traduc[$tab_item[$k]]."</label>
					<input type='checkbox' name='".$tab_item[$k]."[$i]' id='".$tab_item[$k]."_".$i."' value='y' ";

				if(!in_array($tab_item[$k], $tab_param_table_classes_param)) {
					$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
					$res_class_tmp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(mysqli_num_rows($res_class_tmp)>0){
						$lig_class_tmp=mysqli_fetch_object($res_class_tmp);

						if($lig_class_tmp->$tab_item[$k]=="y") {echo "checked ='checked' ";}
						//$tmp_champ=$tab_item[$k];
						//if($lig_class_tmp->$tmp_champ=="y") {echo "checked ='checked' ";}
					}
				}
				elseif(getParamClasse($tab_id_classe[$i],$tab_item[$k],'')=="y") {
					echo "checked ='checked' ";
				}
				echo "/>\n";
				echo "</td>\n";
			}

			echo "<td>\n";
			echo "<a href=\"javascript:CocheLigne('".$tab_item[$k]."')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('".$tab_item[$k]."')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</td>\n";
			echo "</tr>\n";
		}
	}

	//$tab_item[]='rn_app';
	//$tab_traduc['rn_app']="Avec l'appréciation (sous réserve d'autorisation par le professeur)";
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>Avec l'appréciation (<em>sous réserve d'autorisation par le professeur</em>)\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<label for='rn_app_".$i."' class='invisible'>Avec l'appréciation</label>
				<input type='checkbox' name='rn_app[$i]' id='rn_app_".$i."' size='2' value='y' ";
		if((isset($tab_param_classe[$i]['rn_app']))&&($tab_param_classe[$i]['rn_app']=='y')) {
			echo "checked ";
		}
		echo "/>\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_app')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_app')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</td>\n";
	echo "</tr>\n";


	//=================================
	// 20100526
	// Il ne faut peut-être pas l'autoriser pour tous les utilisateurs?
	//if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
	if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesMoyClasseReleveEleve')))||
		(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesMoyClasseReleveParent')))
		) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:left;'>Avec la moyenne de la classe pour chaque devoir\n";
			echo "</td>\n";
			for($i=0;$i<count($tab_id_classe);$i++) {
				echo "<td>\n";
				echo "<label for='rn_moy_classe_".$i."' class='invisible'>Avec la moyenne de la classe</label>
					<input type='checkbox' name='rn_moy_classe[$i]' id='rn_moy_classe_".$i."' size='2' value='y' ";
				if((isset($tab_param_classe[$i]['rn_moy_classe']))&&($tab_param_classe[$i]['rn_moy_classe']=='y')) {
					echo "checked ";
				}
				echo "/>\n";
				echo "</td>\n";
			}
	
			echo "<td>\n";
			echo "<a href=\"javascript:CocheLigne('rn_moy_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</td>\n";
			echo "</tr>\n";
	}

	if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesMoyMinClasseMaxReleveEleve')))||
		(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesMoyMinClasseMaxReleveParent')))
		) {

			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:left;'>Avec les moyennes min/classe/max de chaque devoir\n";
			echo "</td>\n";
			for($i=0;$i<count($tab_id_classe);$i++) {
				echo "<td>\n";
				echo "<label for='rn_moy_min_max_classe_".$i."' class='invisible'>Avec les moyennes min/classe/max</label> 
						<input type='checkbox' name='rn_moy_min_max_classe[$i]' id='rn_moy_min_max_classe_".$i."' size='2' value='y' ";
				if((isset($tab_param_classe[$i]['rn_moy_min_max_classe']))&&($tab_param_classe[$i]['rn_moy_min_max_classe']=='y')) {
					echo "checked ";
				}
				echo "/>\n";
				echo "</td>\n";
			}
	
			echo "<td>\n";
			echo "<a href=\"javascript:CocheLigne('rn_moy_min_max_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_min_max_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
			echo "</td>\n";
			echo "</tr>\n";
	}
	//=================================
	/*
	$rn_retour_ligne_defaut="y";
	if((isset($_SESSION['pref_rn_retour_ligne']))&&(($_SESSION['pref_rn_retour_ligne']=='y')||($_SESSION['pref_rn_retour_ligne']=='n'))) {
		$rn_retour_ligne_defaut=$_SESSION['pref_rn_retour_ligne'];
	}
	*/
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>Avec retour à la ligne après chaque devoir si on affiche le nom du devoir ou le commentaire\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<label for='rn_retour_ligne_".$i."' class='invisible'>Avec retour à la ligne</label> 
					<input type='checkbox' name='rn_retour_ligne[$i]' id='rn_retour_ligne_".$i."' size='2' value='y' ";
		//if($rn_retour_ligne_defaut=='y') {echo "checked='checked' ";}
		if((isset($tab_param_classe[$i]['rn_retour_ligne']))&&($tab_param_classe[$i]['rn_retour_ligne']=='y')) {
			echo "checked ";
		}
		echo "/>\n";
		echo "</td>\n";
	}
	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_retour_ligne')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_retour_ligne')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</td>\n";
	echo "</tr>\n";

	/*
	if(isset($_SESSION['pref_rn_rapport_standard_min_font'])) {
		$rn_rapport_standard_min_font_defaut=$_SESSION['pref_rn_rapport_standard_min_font'];
	}
	else {
		$rn_rapport_standard_min_font_defaut=getSettingValue('rn_rapport_standard_min_font_defaut');
		$rn_rapport_standard_min_font_defaut=(($rn_rapport_standard_min_font_defaut!='')&&(preg_match("/^[0-9.]*$/",$rn_rapport_standard_min_font_defaut))&&($rn_rapport_standard_min_font_defaut>0)) ? $rn_rapport_standard_min_font_defaut : 3;
	}
	*/

	$rn_rapport_standard_min_font_defaut=getSettingValue('rn_rapport_standard_min_font_defaut');
	$rn_rapport_standard_min_font_defaut=(($rn_rapport_standard_min_font_defaut!='')&&(preg_match("/^[0-9.]*$/",$rn_rapport_standard_min_font_defaut))&&($rn_rapport_standard_min_font_defaut>0)) ? $rn_rapport_standard_min_font_defaut : 3;

	$alt=$alt*(-1);
	echo "<tr id='tr_rn_rapport_standard_min_font' class='lig$alt white_hover'>\n";
	echo "<td style='text-align:left;'>Rapport taille_standard / taille_minimale_de_police (<em>relevé PDF avec cell_ajustee()</em>)<br />(<em>Si pour que les notes tiennent dans la cellule, il faut réduire davantage la police, on supprime les retours à la ligne.</em>)\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		if((isset($tab_param_classe[$i]['rn_rapport_standard_min_font']))&&(preg_match("/^[0-9.]*$/", $tab_param_classe[$i]['rn_rapport_standard_min_font']))&&($tab_param_classe[$i]['rn_rapport_standard_min_font']>0)) {
			$rn_rapport_standard_min_font_defaut=$tab_param_classe[$i]['rn_rapport_standard_min_font'];
		}
		echo "<td>\n";
		echo "<label for='rn_rapport_standard_min_font_".$i."' class='invisible'>Rapport taille</label> 
					<input type='text' name='rn_rapport_standard_min_font[$i]' id='rn_rapport_standard_min_font_".$i."' size='2' value='".$rn_rapport_standard_min_font_defaut."' />\n";
		echo "</td>\n";
	}
	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_rapport_standard_min_font')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_rapport_standard_min_font')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</td>\n";
	echo "</tr>\n";


	if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		// "Afficher le bloc adresse du responsable de l'élève :"
		// Non présent dans /classes/modify_nom_class.php?id_classe=...
		// mais il faudrait peut-être l'y ajouter...
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Afficher le bloc adresse du responsable de l'élève\n";
		echo "</td>\n";
		/*
		$chaine_coche_rn_adr_resp="";
		if(getPref($_SESSION['login'], 'pref_rn_adr_resp', "")=="y") {
			$chaine_coche_rn_adr_resp=" checked";
		}
		*/
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<label for='rn_adr_resp_".$i."' class='invisible'>Afficher l'adresse</label> 
					<input type='checkbox' name='rn_adr_resp[$i]' id='rn_adr_resp_".$i."' size='2' value='y' ";
					//$chaine_coche_rn_adr_resp." ";
			if((isset($tab_param_classe[$i]['rn_adr_resp']))&&($tab_param_classe[$i]['rn_adr_resp']=='y')) {
				echo "checked ";
			}
			echo "/>\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_adr_resp')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_adr_resp')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";


		$alt=$alt*(-1);
		echo "<tr id='tr_rn_bloc_obs' class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Afficher le bloc observations (<em>relevé PDF</em>)\n";

		$titre_infobulle="Bloc observations en PDF\n";
		$texte_infobulle="<p>Le bloc observations est affiché si une des conditions suivantes est remplie&nbsp;:</p>\n";
		$texte_infobulle.="<ul>\n";
		$texte_infobulle.="<li>La case Bloc observations est cochée.</li>\n";
		$texte_infobulle.="<li>Une des cases signature est cochée.</li>\n";
		$texte_infobulle.="</ul>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_bloc_observations',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_bloc_observations','y',100,100);\"  onmouseout=\"cacher_div('a_propos_bloc_observations');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Bloc observations en PDF'/></a>";
		// echo "</p>\n";

		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<label for='rn_bloc_obs_".$i."' class='invisible'>bloc observations</label> 
					<input type='checkbox' name='rn_bloc_obs[$i]' id='rn_bloc_obs_".$i."' size='2' value='y' ";
			if((isset($tab_param_classe[$i]['rn_bloc_obs']))&&($tab_param_classe[$i]['rn_bloc_obs']=='y')) {
				echo "checked ";
			}
			echo "/>\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_bloc_obs')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_bloc_obs')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";

		// Nombre de lignes pour la signature
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>Nombre de lignes pour la signature\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<label for='rn_sign_nblig_".$i."' class='invisible'>lignes pour la signature</label> 
					<input type='text' name='rn_sign_nblig[$i]' id='rn_sign_nblig_".$i."' size='2' ";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysqli_fetch_object($res_class_tmp);
				echo "value='$lig_class_tmp->rn_sign_nblig' ";
			}
			else {
				echo "value='3' ";
			}
			echo "/>\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		//echo "&nbsp;";

		$titre_infobulle="Paramètres par défaut\n";
		$texte_infobulle="Les paramètres par défaut sont proposés d'après le paramétrage de la classe.<br />\n";
		$texte_infobulle.="En compte administrateur&nbsp;: <strong>Gestion des bases/Gestion des classes/&lt;une_classe&gt; Paramètres/Paramètres des relevés de notes</strong><br />ou<br /><strong>Gestion des bases/Gestion des classes/Paramétrage de plusieurs classes par lots/Paramètres des relevés de notes</strong>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_parametres_defaut_releve',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_parametres_defaut_releve','y',100,100);\"  onmouseout=\"cacher_div('a_propos_parametres_defaut_releve');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Paramètres par défaut' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p><label for='chaine_coef'>Préfixe pour les coefficients&nbsp;: </label>\n";
		echo "<input type='text' name='chaine_coef' id='chaine_coef' size='5' value='$chaine_coef' />\n";
		echo "</p>\n";

		echo "<p>\n";
		echo "<input type='checkbox' name='rn_couleurs_alternees' id='rn_couleurs_alternees' size='5' value='y' ";
		if(getPref($_SESSION['login'], "rn_couleurs_alternees", "n")=="y") {echo "checked ";}
		echo "/> \n";
		echo "<label for='rn_couleurs_alternees'>Afficher des couleurs alternées pour les lignes de matières</label>\n";
		echo "</p>\n";

		//echo "<p>Formule à afficher en bas de page (<em>relevé HTML</em>):</p>\n";
		echo "<p>Formule à afficher en bas de page&nbsp;: \n";

		$titre_infobulle="Formule de bas de page\n";
		$texte_infobulle="La formule de bas de page (<em>par défaut</em>) peut être paramétrée dans <strong>Gestion des bases/Gestion des classes/&lt;une_classe&gt; Paramètres/Paramètres des relevés de notes</strong><br />ou<br /><strong>Gestion des bases/Gestion des classes/Paramétrage de plusieurs classes par lots/Paramètres des relevés de notes</strong><br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="Si la formule dans le champ ci-dessous est vide, c'est la formule définie dans <strong>Paramètres du relevé HTML</strong> qui est utilisée.<br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="Une différence entre les relevés HTML et PDF&nbsp;:<br />\n";
		$texte_infobulle.="Dans le cas du relevé HTML la formule de <strong>Paramètres du relevé HTML</strong> est affichée en plus de la formule ci-dessous.<br />\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_formule_bas_de_page',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_formule_bas_de_page','y',100,100);\"  onmouseout=\"cacher_div('a_propos_formule_bas_de_page');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur formule de bas de page' /></a>";
		echo "</p>\n";

		echo "<table border='0' summary='Tableau des formules de bas de page'>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<tr><td><strong>".get_class_from_id($tab_id_classe[$i])."</strong>: </td>";
			echo "<td>
			<label for='rn_formule_".$i."' class='invisible'>Formule</label> 
					<input type='text' name='rn_formule[$i]' id='rn_formule_".$i."' size='40' value=\"";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			if(mysqli_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysqli_fetch_object($res_class_tmp);
				echo $lig_class_tmp->rn_formule;
			}
			echo "\" /></td></tr>\n";
		}

	}
	echo "</table>\n";

	echo "<script type='text/javascript'>
	$tab_js_lignes_specifiques_releve_html;
	$tab_js_lignes_specifiques_releve_pdf;

	function reinit_lignes_specifiques_pdf_html() {
		for(i=0;i<tab_html.length;i++) {
			//alert(tab_html[i]);
			if(document.getElementById('tr_'+tab_html[i])) {
				document.getElementById('tr_'+tab_html[i]).style.backgroundColor='';
			}
		}

		for(i=0;i<tab_pdf.length;i++) {
			if(document.getElementById('tr_'+tab_pdf[i])) {
				document.getElementById('tr_'+tab_pdf[i]).style.backgroundColor='';
			}
		}
	}

	function griser_lignes_specifiques_html() {
		reinit_lignes_specifiques_pdf_html();
		for(i=0;i<tab_html.length;i++) {
			if(document.getElementById('tr_'+tab_html[i])) {
				document.getElementById('tr_'+tab_html[i]).style.backgroundColor='grey';
			}
		}
	}

	function griser_lignes_specifiques_pdf() {
		reinit_lignes_specifiques_pdf_html();
		for(i=0;i<tab_pdf.length;i++) {
			if(document.getElementById('tr_'+tab_pdf[i])) {
				document.getElementById('tr_'+tab_pdf[i]).style.backgroundColor='grey';
			}
		}
	}

</script>
";

//echo "\$chaine_coef=$chaine_coef<br />";
?>
