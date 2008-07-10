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
	$tab_traduc['rn_sign_chefetab']="Avec case pour signature du chef d'établissement";
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
	for($k=0;$k<count($tab_item);$k++) {
		$affiche_ligne="y";
		if ((($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable'))&&(ereg("^rn_sign",$tab_item[$k]))) {
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
	echo "<td style='text-align:left;'>Avec l'appréciation (sous réserve d'autorisation par le professeur)\n";
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
		echo "&nbsp;";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<p>Formule à afficher en bas de page:</p>\n";
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