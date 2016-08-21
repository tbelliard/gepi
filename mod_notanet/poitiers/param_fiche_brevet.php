<?php

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form_param' method='post'>\n";
	echo "<table border='0'>\n";

	$alt=1;
	$fb_academie=getSettingValue("fb_academie");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Académie de: </td>\n";
	echo "<td><input type='text' name='fb_academie' value='$fb_academie' /></td>\n";
	echo "</tr>\n";




	$fb_departement=getSettingValue("fb_departement");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Département de: </td>\n";
	echo "<td><input type='text' name='fb_departement' value='$fb_departement' /></td>\n";
	echo "</tr>\n";




	$fb_session=getSettingValue("fb_session");
	//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
	if($fb_session==""){
		$tmp_date=getdate();
		$tmp_mois=$tmp_date['mon'];
		if($tmp_mois>9){
			$fb_session=$tmp_date['year']+1;
		}
		else{
			$fb_session=$tmp_date['year'];
		}
	}
	//echo "<tr><td colspan='2'>\$fb_session=$fb_session</td></tr>";
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Session: </td>\n";
	echo "<td><input type='text' name='fb_session' value='$fb_session' /></td>\n";
	echo "</tr>\n";




	// ****************************************************************************
	// MODE DE CALCUL POUR LES MOYENNES DES REGROUPEMENTS DE MATIERES:
	// - LV1: on fait la moyenne de toutes les LV1 (AGL1, ALL1)
	// ou
	// - LV1: on présente pour chaque élève, la moyenne qui correspond à sa LV1: ALL1 s'il fait ALL1,...
	// ****************************************************************************
	$fb_mode_moyenne=getSettingValue("fb_mode_moyenne");
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Mode de calcul des moyennes pour les options Notanet associées à plusieurs matières (<i>ex.: LV1 associée à AGL1 et ALL1</i>): </td>\n";
	echo "<td>";
		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='1' ";
		if($fb_mode_moyenne!="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer la moyenne de toutes matières d'une même option Notanet confondues<br />\n";
		echo "(<i>on compte ensemble les AGL1 et ALL1; c'est la moyenne de toute la LV1 qui est effectuée</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='radio' name='fb_mode_moyenne' value='2' ";
		if($fb_mode_moyenne=="2"){
			echo "checked />";
		}
		else{
			echo "/>";
		}
		echo "</td>\n";
		echo "<td>\n";
		echo "Calculer les moyennes par matières<br />\n";
		echo "(<i>on ne mélange pas AGL1 et ALL1 dans le calcul de la moyenne de classe pour un élève</i>)\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";

	/*
	$fb_nblig_avis_chef=getSettingValue("fb_nblig_avis_chef");
	if($fb_nblig_avis_chef==""){
		$fb_nblig_avis_chef=4;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Nombre de lignes pour l'avis du chef d'établissement: </td>\n";
	echo "<td><input type='text' name='fb_nblig_avis_chef' value='$fb_nblig_avis_chef' /></td>\n";
	echo "</tr>\n";
	*/

	//===============================================
	echo "<tr><td colspan='2' style='font-weight:bold;'>Largeur des colonnes du tableau des disciplines</td></tr>\n";

	$fb_largeur_tableau=getSettingValue("fb_largeur_tableau");
	if($fb_largeur_tableau==""){
		$fb_largeur_tableau=950;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur du tableau en pixels: </td>\n";
	echo "<td><input type='text' name='fb_largeur_tableau' value='$fb_largeur_tableau' /></td>\n";
	echo "</tr>\n";


	$fb_largeur_col_disc=getSettingValue("fb_largeur_col_disc");
	if($fb_largeur_col_disc==""){
		$fb_largeur_col_disc=31;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne DISCIPLINES (<i>1ère colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_disc' value='$fb_largeur_col_disc' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_opt=getSettingValue("fb_largeur_col_opt");
	if($fb_largeur_col_opt==""){
		$fb_largeur_col_opt=8;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur des colonnes 'NOTE MOYENNE affectée du coefficient' en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_opt' value='$fb_largeur_col_opt' /></td>\n";
	echo "</tr>\n";

	$fb_largeur_col_note=getSettingValue("fb_largeur_col_note");
	if($fb_largeur_col_note==""){
		$fb_largeur_col_note=7;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne 'Note moyenne de la classe' en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_note' value='$fb_largeur_col_note' /></td>\n";
	echo "</tr>\n";


	$fb_largeur_col_app=getSettingValue("fb_largeur_col_app");
	if($fb_largeur_col_app==""){
		$fb_largeur_col_app=46;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne Appréciation des professeurs (<i>dernière colonne</i>) en pourcentage: </td>\n";
	echo "<td><input type='text' name='fb_largeur_col_app' value='$fb_largeur_col_app' /></td>\n";
	echo "</tr>\n";


	//===============================================
	echo "<tr><td colspan='2' style='font-weight:bold;'>Informations académie, département établissement,..., élèves</td></tr>\n";


	$fb_taille_acad=getSettingValue("fb_taille_acad");
	if($fb_taille_acad==""){
		$fb_taille_acad=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des informations 'Académie, Département': </td>\n";
	echo "<td><input type='text' name='fb_taille_acad' value='$fb_taille_acad' /></td>\n";
	echo "</tr>\n";


	$fb_taille_etab=getSettingValue("fb_taille_etab");
	if($fb_taille_etab==""){
		$fb_taille_etab=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des informations 'Etablissement': </td>\n";
	echo "<td><input type='text' name='fb_taille_etab' value='$fb_taille_etab' /></td>\n";
	echo "</tr>\n";

	$fb_marg_etab=getSettingValue("fb_marg_etab");
	if($fb_marg_etab==""){
		$fb_marg_etab=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges des infos établissement en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_etab' value='$fb_marg_etab' /></td>\n";
	echo "</tr>\n";




	$fb_titrepage=getSettingValue("fb_titrepage");
	if($fb_titrepage==""){
		$fb_titrepage=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points du titre de la page 'FICHE SCOLAIRE BREVET...': </td>\n";
	echo "<td><input type='text' name='fb_titrepage' value='$fb_titrepage' /></td>\n";
	echo "</tr>\n";




	$fb_taille_txt_ele=getSettingValue("fb_taille_txt_ele");
	if($fb_taille_txt_ele==""){
		$fb_taille_txt_ele=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en point des textes des informations élève: </td>\n";
	echo "<td><input type='text' name='fb_taille_txt_ele' value='$fb_taille_txt_ele' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h_ele=getSettingValue("fb_marg_h_ele");
	if($fb_marg_h_ele==""){
		$fb_marg_h_ele=1;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basses des infos élève en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h_ele' value='$fb_marg_h_ele' /></td>\n";
	echo "</tr>\n";




	//===============================================
	echo "<tr><td colspan='2' style='font-weight:bold;'>Tableau des disciplines</td></tr>\n";

	$fb_titretab=getSettingValue("fb_titretab");
	if($fb_titretab==""){
		$fb_titretab=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des intitulés de l'entête du tableau 'DISCIPLINES', 'NOTE MOYENNE',...: </td>\n";
	echo "<td><input type='text' name='fb_titretab' value='$fb_titretab' /></td>\n";
	echo "</tr>\n";


	$fb_tittab_lineheight=getSettingValue("fb_tittab_lineheight");
	if($fb_tittab_lineheight==""){
		$fb_tittab_lineheight=14;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points de la ligne 'TOTAL DES POINTS': </td>\n";
	echo "<td><input type='text' name='fb_tittab_lineheight' value='$fb_tittab_lineheight' /></td>\n";
	echo "</tr>\n";



	$fb_taille_txt_disc=getSettingValue("fb_taille_txt_disc");
	if($fb_taille_txt_disc==""){
		$fb_taille_txt_disc=10;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des noms des disciplines: </td>\n";
	echo "<td><input type='text' name='fb_taille_txt_disc' value='$fb_taille_txt_disc' /></td>\n";
	echo "</tr>\n";

	$fb_marg_h=getSettingValue("fb_marg_h");
	if($fb_marg_h==""){
		$fb_marg_h=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges haute et basse des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_h' value='$fb_marg_h' /></td>\n";
	echo "</tr>\n";

	$fb_marg_l=getSettingValue("fb_marg_l");
	if($fb_marg_l==""){
		$fb_marg_l=2;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Marges gauche et droite des disciplines en pixels: </td>\n";
	echo "<td><input type='text' name='fb_marg_l' value='$fb_marg_l' /></td>\n";
	echo "</tr>\n";



	$fb_textetab=getSettingValue("fb_textetab");
	if($fb_textetab==""){
		$fb_textetab=9;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Taille en points des autres textes du tableau: </td>\n";
	echo "<td><input type='text' name='fb_textetab' value='$fb_textetab' /></td>\n";
	echo "</tr>\n";

	$fb_txttab_lineheight=getSettingValue("fb_txttab_lineheight");
	if($fb_txttab_lineheight==""){
		$fb_txttab_lineheight=11;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Hauteur de ligne en points des autres textes: </td>\n";
	echo "<td><input type='text' name='fb_txttab_lineheight' value='$fb_txttab_lineheight' /></td>\n";
	echo "</tr>\n";

	//===============================================
	echo "<tr><td colspan='2' style='font-weight:bold;'>Largeur des colonnes B2i, niveau A2 de langue vivante</td></tr>\n";

	$fb_largeur_b2i=getSettingValue("fb_largeur_b2i");
	if($fb_largeur_b2i==""){
		$fb_largeur_b2i=12;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne B2i en pourcentage de la largeur du tableau: </td>\n";
	echo "<td><input type='text' name='fb_largeur_b2i' value='$fb_largeur_b2i' /></td>\n";
	echo "</tr>\n";


	$fb_largeur_coche_b2i=getSettingValue("fb_largeur_coche_b2i");
	if($fb_largeur_coche_b2i==""){
		$fb_largeur_coche_b2i=5;
	}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td>Largeur de la colonne des cases à cocher B2i (<i>sans la partie texte</i>) en pourcentage de la largeur du tableau: </td>\n";
	echo "<td><input type='text' name='fb_largeur_coche_b2i' value='$fb_largeur_coche_b2i' /></td>\n";
	echo "</tr>\n";



	//===============================================
	echo "<tr><td colspan='2' style='font-weight:bold;'>Images</td></tr>\n";
	echo "<tr><td colspan='2'>Il peut arriver avec certains navigateurs que la transparence des PNG pose des problèmes lors de l'impression.<br />Un jeu de fichiers alternatifs avec un fond blanc est proposé pour résoudre ce problème.</td></tr>\n";

	$fb_modele_img=getSettingValue("fb_modele_img");
	if($fb_modele_img=="") {$fb_modele_img=1;}
	$alt=$alt*(-1);
	echo "<tr";
	if($alt==1){echo " style='background: white;'";}else{echo " style='background: silver;'";}
	echo ">\n";
	echo "<td valign='top'>Modèle d'image: </td>\n";
	echo "<td>\n";
	echo "<input type='radio' name='fb_modele_img' id='fb_modele_img_std' value='1' ";
	if($fb_modele_img==1) {echo "checked ";}
	echo "/><label for='fb_modele_img_std' style='cursor:pointer;'> standard</label><br />\n";
	echo "<input type='radio' name='fb_modele_img' id='fb_modele_img_alt' value='2' ";
	if($fb_modele_img==2) {echo "checked ";}
	echo "/><label for='fb_modele_img_alt' style='cursor:pointer;'> alternatif</label>\n";
	echo "</td>\n";
	echo "</tr>\n";



	echo "</table>\n";
	echo "<p align='center'><input type='submit' name='enregistrer_param' value='Enregistrer' /></p>\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	function fixe_proportions(mode){
		if(mode=='agri'){
			document.form_param.fb_largeur_col_disc.value='31';
			document.form_param.fb_largeur_col_note.value='8';
			document.form_param.fb_largeur_col_app.value='42';
			document.form_param.fb_largeur_col_opt.value='10';
			document.form_param.fb_largeur_b2i.value='12';
			document.form_param.fb_largeur_coche_b2i.value='5';
		}
		else{
			if(mode=='college') {
				document.form_param.fb_largeur_col_disc.value='31';
				document.form_param.fb_largeur_col_note.value='7';
				document.form_param.fb_largeur_col_app.value='46';
				document.form_param.fb_largeur_col_opt.value='8';
				document.form_param.fb_largeur_b2i.value='12';
				document.form_param.fb_largeur_coche_b2i.value='5';
			}

			if(mode=='professionnelle') {
				document.form_param.fb_largeur_col_disc.value='25';
				document.form_param.fb_largeur_col_note.value='9';
				document.form_param.fb_largeur_col_app.value='46';
				document.form_param.fb_largeur_col_opt.value='10';
				document.form_param.fb_largeur_b2i.value='13';
				document.form_param.fb_largeur_coche_b2i.value='5';
			}

			if(mode=='technologique') {
				document.form_param.fb_largeur_col_disc.value='26';
				document.form_param.fb_largeur_col_note.value='8';
				document.form_param.fb_largeur_col_app.value='44';
				document.form_param.fb_largeur_col_opt.value='11';
				document.form_param.fb_largeur_b2i.value='15';
				document.form_param.fb_largeur_coche_b2i.value='5';
			}
		}
	}
</script>\n";

	//echo "<p>Les proportions de largeur des colonnes diffèrent un peu entre les brevets agricoles et les brevets non agricoles.<br />Les liens suivants permettent de fixer les proportions par défaut correspondant à ces deux cas.<br />\n";
	echo "<p>Les proportions de largeur des colonnes diffèrent un peu entre les brevets.<br />Les liens suivants permettent de fixer les proportions par défaut correspondant aux différents cas.</p>\n";

	echo "<table><tr>";
	echo "<td>Brevet série</td>\n";
	//echo "<a href='#' onClick='fixe_proportions(\"non_agri\")'>non agricoles</a>";
	//echo " ou \n";
	//echo "<a href='#' onClick='fixe_proportions(\"agri\")'>agricoles</a>";
	echo "<td><a href='#' onClick='fixe_proportions(\"college\")'>collège</a>,</td>";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>&nbsp;</td>\n";
	echo "<td><a href='#' onClick='fixe_proportions(\"professionnelle\")'>professionnelle</a>,</td>";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>&nbsp;</td>\n";
	echo "<td><a href='#' onClick='fixe_proportions(\"technologique\")'>technologique</a>.</td>";
	//echo ".</p>\n";

	//echo "<p style='color:red;'>PROPORTIONS A RECALCULER...</p>";
	echo "<p><br /></p>\n";
?>