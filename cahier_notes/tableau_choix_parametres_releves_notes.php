<?php
	echo "<table class='boireaus' border='1' summary='Tableau des items'>\n";
	echo "<tr>\n";
	//echo "<th width='30%'>Item</th>\n";
	echo "<th>Item</th>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<th>".get_class_from_id($tab_id_classe[$i])."</th>\n";
	}
	echo "<th>\n";
	echo "<a href=\"javascript:ToutCocher()\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:ToutDeCocher()\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</th>\n";
	echo "</tr>\n";

	$gepiProfSuivi=getSettingValue("gepi_prof_suivi");

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
	$tab_item[]='rn_sign_chefetab';
	$tab_traduc['rn_sign_chefetab']="Avec case pour signature du chef d'établissement (<i>relevé HTML</i>)";
	$tab_item[]='rn_sign_pp';
	$tab_traduc['rn_sign_pp']="Avec case pour signature du $gepiProfSuivi";
	$tab_item[]='rn_sign_resp';
	$tab_traduc['rn_sign_resp']="Avec case pour signature des responsables";

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
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>Affichage du nom de la classe (<i>relevé PDF</i>)<br />\n";
	echo "Nom long (1) / Nom court (2) / Nom court (Nom long) (3)";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='1' />&nbsp;1<br />\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='2' />&nbsp;2<br />\n";
		//echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."' value='3' />&nbsp;3<br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_1' value='1' checked /><br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_2' value='2' /><br />\n";
		echo "<input type='radio' name='rn_aff_classe_nom[$i]' id='rn_aff_classe_nom_".$i."_3' value='3' />\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	//echo "&nbsp;";
	echo "Nom long<br />\n";
	echo "Nom court<br />\n";
	echo "Nom court (Nom long)\n";
	echo "</td>\n";
	echo "</tr>\n";


	for($k=0;$k<count($tab_item);$k++) {
		$affiche_ligne="y";
		if ((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&(my_ereg("^rn_sign",$tab_item[$k]))) {
			$affiche_ligne="n";
		}

		if($affiche_ligne=="y") {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			//echo "<td style='text-align:left;'>".$tab_traduc[$tab_item[$k]]."\n";
			echo "<td style='text-align:left;'>".$tab_traduc[$tab_item[$k]]."\n";
			echo "</td>\n";

			for($i=0;$i<count($tab_id_classe);$i++) {
				echo "<td>\n";
				echo "<input type='checkbox' name='".$tab_item[$k]."[$i]' id='".$tab_item[$k]."_".$i."' value='y' ";
				$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
				$res_class_tmp=mysql_query($sql);
				if(mysql_num_rows($res_class_tmp)>0){
					$lig_class_tmp=mysql_fetch_object($res_class_tmp);

					if($lig_class_tmp->$tab_item[$k]=="y") {echo "checked ";}
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
	echo "<tr class='lig$alt'>\n";
	echo "<td style='text-align:left;'>Avec l'appréciation (<i>sous réserve d'autorisation par le professeur</i>)\n";
	echo "</td>\n";
	for($i=0;$i<count($tab_id_classe);$i++) {
		echo "<td>\n";
		echo "<input type='checkbox' name='rn_app[$i]' id='rn_app_".$i."' size='2' value='y' />\n";
		echo "</td>\n";
	}

	echo "<td>\n";
	echo "<a href=\"javascript:CocheLigne('rn_app')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_app')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
	echo "</td>\n";
	echo "</tr>\n";


	//=================================
	// 20100526
	// Il ne faut peut-être pas l'autoriser pour tous les utilisateurs?
	if(($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>Avec la moyenne de la classe pour chaque devoir\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_moy_classe[$i]' id='rn_moy_classe_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
	
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_moy_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
	
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>Avec les moyennes min/classe/max de chaque devoir\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_moy_min_max_classe[$i]' id='rn_moy_min_max_classe_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
	
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_moy_min_max_classe')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_moy_min_max_classe')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
	}
	//=================================


	if (($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')) {
		// "Afficher le bloc adresse du responsable de l'élève :"
		// Non présent dans /classes/modify_nom_class.php?id_classe=...
		// mais il faudrait peut-être l'y ajouter...
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>Afficher le bloc adresse du responsable de l'élève\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_adr_resp[$i]' id='rn_adr_resp_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_adr_resp')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_adr_resp')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";


		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>Afficher le bloc observations (<i>relevé PDF</i>)\n";

		$titre_infobulle="Bloc observations en PDF\n";
		$texte_infobulle="<p>Le bloc observations est affiché si une des conditions suivantes est remplie&nbsp;:</p>\n";
		$texte_infobulle.="<ul>\n";
		$texte_infobulle.="<li>La case Bloc observations est cochée.</li>\n";
		$texte_infobulle.="<li>Une des cases signature est cochée.</li>\n";
		$texte_infobulle.="</ul>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_bloc_observations',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_bloc_observations','y',100,100);\"  onmouseout=\"cacher_div('a_propos_bloc_observations');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</p>\n";

		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='checkbox' name='rn_bloc_obs[$i]' id='rn_bloc_obs_".$i."' size='2' value='y' />\n";
			echo "</td>\n";
		}
		echo "<td>\n";
		echo "<a href=\"javascript:CocheLigne('rn_bloc_obs')\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:DecocheLigne('rn_bloc_obs')\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>";
		echo "</td>\n";
		echo "</tr>\n";


		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='text-align:left;'>Nombre de lignes pour la signature\n";
		echo "</td>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<td>\n";
			echo "<input type='text' name='rn_sign_nblig[$i]' id='rn_sign_nblig_".$i."' size='2' ";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysql_query($sql);
			if(mysql_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysql_fetch_object($res_class_tmp);
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
		$texte_infobulle.="En compte administrateur&nbsp;: <b>Gestion des bases/Gestion des classes/&lt;une_classe&gt; Paramètres/Paramètres des relevés de notes</b><br />ou<br /><b>Gestion des bases/Gestion des classes/Paramétrage de plusieurs classes par lots/Paramètres des relevés de notes</b>\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_parametres_defaut_releve',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_parametres_defaut_releve','y',100,100);\"  onmouseout=\"cacher_div('a_propos_parametres_defaut_releve');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		//echo "<p>Formule à afficher en bas de page (<i>relevé HTML</i>):</p>\n";
		echo "<p>Formule à afficher en bas de page&nbsp;: \n";

		$titre_infobulle="Formule de bas de page\n";
		$texte_infobulle="La formule de bas de page (<i>par défaut</i>) peut être paramétrée dans <b>Gestion des bases/Gestion des classes/&lt;une_classe&gt; Paramètres/Paramètres des relevés de notes</b><br />ou<br /><b>Gestion des bases/Gestion des classes/Paramétrage de plusieurs classes par lots/Paramètres des relevés de notes</b><br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="Si la formule dans le champ ci-dessous est vide, c'est la formule définie dans <b>Paramètres du relevé HTML</b> qui est utilisée.<br />\n";
		$texte_infobulle.="&nbsp;<br />\n";
		$texte_infobulle.="Une différence entre les relevés HTML et PDF&nbsp;:<br />\n";
		$texte_infobulle.="Dans le cas du relevé HTML la formule de <b>Paramètres du relevé HTML</b> est affichée en plus de la formule ci-dessous.<br />\n";
		$tabdiv_infobulle[]=creer_div_infobulle('a_propos_formule_bas_de_page',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_formule_bas_de_page','y',100,100);\"  onmouseout=\"cacher_div('a_propos_formule_bas_de_page');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' /></a>";
		echo "</p>\n";

		echo "<table border='0' summary='Tableau des formules de bas de page'>\n";
		for($i=0;$i<count($tab_id_classe);$i++) {
			echo "<tr><td><b>".get_class_from_id($tab_id_classe[$i])."</b>: </td>";
			echo "<td><input type='text' name='rn_formule[$i]' id='rn_formule_".$i."' size='40' value=\"";
			$sql="SELECT * FROM classes WHERE id='".$tab_id_classe[$i]."';";
			$res_class_tmp=mysql_query($sql);
			if(mysql_num_rows($res_class_tmp)>0){
				$lig_class_tmp=mysql_fetch_object($res_class_tmp);
				echo $lig_class_tmp->rn_formule;
			}
			echo "\" /></td></tr>\n";
		}
	}
	echo "</table>\n";
?>