<?php
	//========================================

	if($mode_bulletin!='pdf') {
		echo "<script type='text/javascript'>
	document.getElementById('titre_infodiv').innerHTML='Relevés de notes';
	document.getElementById('td_info').innerHTML='Préparatifs';
	document.getElementById('td_classe').innerHTML='';
	document.getElementById('td_periode').innerHTML='';
	document.getElementById('td_ele').innerHTML='';
</script>\n";
	}
	//========================================

	// Tableau destiné à stocker toutes les infos
	$tab_releve=array();

	//===================================
	// Remplir $cat_names[$cat_id] hors des boucles classe/période
	$get_cat = mysql_query("SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$categories[] = $row["id"];
	}

	//$tab_releve['categorie']=array();

	$cat_names = array();
	foreach ($categories as $cat_id) {
		//$cat_names[$cat_id]=mysql_result(mysql_query("SELECT nom_complet FROM matieres_categories WHERE id = '" . $cat_id . "'"), 0);
		$sql="SELECT nom_complet FROM matieres_categories WHERE id='".$cat_id."';";
		$res_cat=mysql_query($sql);
		if ($res_cat) {
			$cat_names[$cat_id]=mysql_result($res_cat, 0);
			//$tab_releve['categorie'][$cat_id]=$cat_names[$cat_id];
		}
	}
	//===================================

	//debug_var();

	// Récupération des paramètres
	// Les valeurs des tableaux peuvent ne pas être affectées si aucune case n'est cochée
	$tab_rn_nomdev=isset($_POST['rn_nomdev']) ? $_POST['rn_nomdev'] : array();
	$tab_rn_toutcoefdev=isset($_POST['rn_toutcoefdev']) ? $_POST['rn_toutcoefdev'] : array();
	$tab_rn_coefdev_si_diff=isset($_POST['rn_coefdev_si_diff']) ? $_POST['rn_coefdev_si_diff'] : array();
	$tab_rn_datedev=isset($_POST['rn_datedev']) ? $_POST['rn_datedev'] : array();
	$tab_rn_app=isset($_POST['rn_app']) ? $_POST['rn_app'] : array();
	$tab_rn_sign_chefetab=isset($_POST['rn_sign_chefetab']) ? $_POST['rn_sign_chefetab'] : array();
	$tab_rn_sign_pp=isset($_POST['rn_sign_pp']) ? $_POST['rn_sign_pp'] : array();
	$tab_rn_sign_resp=isset($_POST['rn_sign_resp']) ? $_POST['rn_sign_resp'] : array();
	// Les deux suivants doivent être affectés (éventuellement avec des chaines vides)
	$tab_rn_sign_nblig=isset($_POST['rn_sign_nblig']) ? $_POST['rn_sign_nblig'] : array();
	$tab_rn_formule=isset($_POST['rn_formule']) ? $_POST['rn_formule'] : array();

	$tab_rn_adr_resp=isset($_POST['rn_adr_resp']) ? $_POST['rn_adr_resp'] : array();

	// Bloc observation sur la droite pour le relevé PDF:
	$tab_rn_bloc_obs=isset($_POST['rn_bloc_obs']) ? $_POST['rn_bloc_obs'] : array();

	$tab_rn_aff_classe_nom=isset($_POST['rn_aff_classe_nom']) ? $_POST['rn_aff_classe_nom'] : array();

	//+++++++++++++++++++++++++++++++++++
	// A FAIRE
	// Contrôler les paramètres reçus en fonction de
	// GepiAccesOptionsReleveParent
	// GepiAccesOptionsReleveEleve
	//+++++++++++++++++++++++++++++++++++


	// Boucle sur les classes
	for($loop_classe=0;$loop_classe<count($tab_id_classe);$loop_classe++) {

		//==============================
		if($mode_bulletin!='pdf') {
			echo "<script type='text/javascript'>
	document.getElementById('td_classe').innerHTML='".get_class_from_id($tab_id_classe[$loop_classe])."';
</script>\n";
			flush();
		}
		//==============================

		//$id_classe=2;
		$id_classe=$tab_id_classe[$loop_classe];


		// Tableau destiné à stocker toutes les infos
		$tab_releve[$id_classe]=array();

		// ++++++++++++++++++++++++++++++++++
		// A REVOIR: PEUT-ETRE QU'ON IMPRIME PAS L'ADRESSE SUR LE RELEVE, MAIS SEULEMENT SUR LE BULLETIN
		//$affiche_adresse = sql_query1("SELECT display_address FROM classes WHERE id='".$id_classe."'");
		//echo "\$affiche_adresse=$affiche_adresse<br />";
		// ++++++++++++++++++++++++++++++++++


		/*
		//+++++++++++++++++++++++++++++
		// RECUPERER LES PARAMETRES ICI
		//+++++++++++++++++++++++++++++
		$tab_releve[$id_classe]['rn_nomdev']=isset($tab_rn_nomdev[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_toutcoefdev']=isset($tab_rn_toutcoefdev[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_coefdev_si_diff']=isset($tab_rn_coefdev_si_diff[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_datedev']=isset($tab_rn_datedev[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_sign_chefetab']=isset($tab_rn_sign_chefetab[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_sign_pp']=isset($tab_rn_sign_pp[$loop_classe]) ? "y" : "n";
		$tab_releve[$id_classe]['rn_sign_resp']=isset($tab_rn_sign_resp[$loop_classe]) ? "y" : "n";

		$tab_releve[$id_classe]['rn_sign_nblig']=isset($tab_rn_sign_nblig[$loop_classe]) ? $tab_rn_sign_nblig[$loop_classe] : 3;
		$tab_releve[$id_classe]['rn_formule']=isset($tab_rn_formule[$loop_classe]) ? $tab_rn_formule[$loop_classe] : "";
		*/


		//++++++++++++++++++++++++++++
		// A VOIR: COMMENT TRAITER LE CAS $choix_periode=='intervalle'
		if($choix_periode=='intervalle') {
			$tab_periode_num[0]="intervalle";
		}
		// Fixé ainsi pour entrer dans la boucle ci-dessous
		//++++++++++++++++++++++++++++

		//echo "count(\$tab_periode_num)=".count($tab_periode_num)."<br />";

		// Boucle sur les périodes
		for($loop_periode_num=0;$loop_periode_num<count($tab_periode_num);$loop_periode_num++) {

			//$periode_num=1;
			$periode_num=$tab_periode_num[$loop_periode_num];

			//==============================
			if($mode_bulletin!='pdf') {
				echo "<script type='text/javascript'>
	document.getElementById('td_periode').innerHTML='".$periode_num."';
</script>\n";
				flush();
			}
			//==============================


			//============================
			// On vide les variables de la boucle précédente
			unset($current_eleve_login);

			//+++++++++++++++
			// LISTE A FAIRE
			// Il faut essentiellement réinitialiser les tableaux pour ne pas risquer de récupérer des indices du tour précédent
			//+++++++++++++++

			//============================


			// Tableau destiné à stocker toutes les infos
			$tab_releve[$id_classe][$periode_num]=array();


			foreach($cat_names as $key => $value) {
				$tab_releve[$id_classe][$periode_num]['categorie'][$key]=$value;
			}



			//+++++++++++++++++++++++++++++
			// RECUPERER LES PARAMETRES ICI
			// après l'initialisation de $tab_releve[$id_classe][$periode_num]
			// Remarque: le $periode_num n'est pas discriminant pour les paramètres,
			//           mais on passe le sous-tableau $tab_releve[$id_classe][$periode_num]
			//           à la génération de relevé si bien qu'on l'accède pas à $tab_releve[$id_classe]
			//+++++++++++++++++++++++++++++
			// ****************************************
			// A FAIRE
			// Dans le cas d'un appel depuis la génération de bulletin, il faudrait prendre les paramètres par défaut de la classe
			// ****************************************
			$tab_releve[$id_classe][$periode_num]['rn_nomdev']=isset($tab_rn_nomdev[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_toutcoefdev']=isset($tab_rn_toutcoefdev[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_coefdev_si_diff']=isset($tab_rn_coefdev_si_diff[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_app']=isset($tab_rn_app[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_datedev']=isset($tab_rn_datedev[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_chefetab']=isset($tab_rn_sign_chefetab[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_pp']=isset($tab_rn_sign_pp[$loop_classe]) ? "y" : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_resp']=isset($tab_rn_sign_resp[$loop_classe]) ? "y" : "n";

			$tab_releve[$id_classe][$periode_num]['rn_sign_nblig']=isset($tab_rn_sign_nblig[$loop_classe]) ? $tab_rn_sign_nblig[$loop_classe] : 3;
			$tab_releve[$id_classe][$periode_num]['rn_formule']=isset($tab_rn_formule[$loop_classe]) ? $tab_rn_formule[$loop_classe] : "";

			$tab_releve[$id_classe][$periode_num]['rn_adr_resp']=isset($tab_rn_adr_resp[$loop_classe]) ? $tab_rn_adr_resp[$loop_classe] : "n";

			// Bloc observation sur le relevé PDF
			$tab_releve[$id_classe][$periode_num]['rn_bloc_obs']=isset($tab_rn_bloc_obs[$loop_classe]) ? $tab_rn_bloc_obs[$loop_classe] : "n";

			$tab_releve[$id_classe][$periode_num]['rn_aff_classe_nom']=isset($tab_rn_aff_classe_nom[$loop_classe]) ? $tab_rn_aff_classe_nom[$loop_classe] : "n";

			$affiche_adresse=$tab_releve[$id_classe][$periode_num]['rn_adr_resp'];
			$tab_releve[$id_classe][$periode_num]['affiche_adresse']=$affiche_adresse;


			//echo "\$tab_releve[$id_classe][$periode_num]['affiche_adresse']=".$tab_releve[$id_classe][$periode_num]['affiche_adresse']."<br />";

			// Informations sur la période
			if ($choix_periode=="intervalle") {
				$tab_releve[$id_classe][$periode_num]['intervalle']['debut']=$display_date_debut;
				$tab_releve[$id_classe][$periode_num]['intervalle']['fin']=$display_date_fin;
			}
			else {
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode_num';";
				$res_per=mysql_query($sql);
				$lig_per=mysql_fetch_object($res_per);
				$tab_releve[$id_classe][$periode_num]['num_periode']=$lig_per->num_periode;
				$tab_releve[$id_classe][$periode_num]['nom_periode']=$lig_per->nom_periode;
				$tab_releve[$id_classe][$periode_num]['verouiller']=$lig_per->verouiller;
			}

			// Liste des élèves à éditer/afficher/imprimer (sélection):
			// tab_ele_".$i."_".$j.
			//$tab_releve[$id_classe][$periode_num]['selection_eleves']=array();
			if($choix_periode=="intervalle") {
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_intervalle']) ? $_POST['tab_selection_ele_'.$loop_classe.'_intervalle'] : array();
			}
			else {
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num]) ? $_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num] : array();
			}
			$tab_releve[$id_classe][$periode_num]['selection_eleves']=$tab_selection_eleves;


			//========================================
			$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
			if ($affiche_categories == "y") {
				$affiche_categories = true;
			} else {
				$affiche_categories = false;
			}
			//========================================


			// Informations sur la classe
			$sql="SELECT * FROM classes WHERE id='".$id_classe."';";
			$res_classe=mysql_query($sql);
			$lig_classe=mysql_fetch_object($res_classe);

			$tab_releve[$id_classe][$periode_num]['id_classe']=$lig_classe->id;
			$tab_releve[$id_classe][$periode_num]['classe']=$lig_classe->classe;
			$tab_releve[$id_classe][$periode_num]['classe_nom_complet']=$lig_classe->nom_complet;
			// Formule du bulletin:
			//$tab_releve[$id_classe][$periode_num]['formule']=$lig_classe->formule;
			$tab_releve[$id_classe][$periode_num]['suivi_par']=$lig_classe->suivi_par;

			$classe=$lig_classe->classe;
			$classe_nom_complet=$lig_classe->nom_complet;


			// Récupérer l'effectif de la classe,...
			if ($choix_periode=="intervalle") {
				$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec WHERE id_classe='$id_classe';";
			}
			else {
				$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$periode_num';";
			}
			$res_eff_classe=mysql_query($sql);
			//$lig_eff_classe=mysql_fetch_object($res_eff_classe);
			$eff_classe=mysql_num_rows($res_eff_classe);
			//echo "<p>Effectif de la classe: $eff_classe</p>\n";


			// Variables simples
			$tab_releve[$id_classe][$periode_num]['eff_classe']=$eff_classe;
			$tab_releve[$id_classe][$periode_num]['affiche_categories']=$affiche_categories;

			if ($choix_periode=="intervalle") {
				unset($tab_tmp_date);
				$tab_tmp_date=explode("/",$display_date_debut);
				$date_debut=$tab_tmp_date[2]."-".$tab_tmp_date[1]."-".$tab_tmp_date[0]." 00:00:00";

				unset($tab_tmp_date);
				$tab_tmp_date=explode("/",$display_date_fin);
				$date_fin=$tab_tmp_date[2]."-".$tab_tmp_date[1]."-".$tab_tmp_date[0]." 00:00:00";
			}

			// Contrairement au dispositif pour les bulletins, on récupère le $current_eleve_login d'après la liste POSTée
			// Cette liste peut avoir été manipulée...
			// il faut contrôler si les élèves de la liste sont bien dans la classe sur la période indiquée
			$current_eleve_login=$tab_releve[$id_classe][$periode_num]['selection_eleves'];
			//echo "count(\$current_eleve_login)=".count($current_eleve_login)."<br />";


			// Boucle élèves de la classe $id_classe pour la période $periode_num
			for($i=0;$i<count($current_eleve_login);$i++) {
				// Réinitialisation pour ne pas récupérer des infos de l'élève précédent
				unset($tab_ele);
				$tab_ele=array();

				//==============================
				if($mode_bulletin!='pdf') {
					echo "<script type='text/javascript'>
	document.getElementById('td_ele').innerHTML='".$current_eleve_login[$i]."';
</script>\n";
					//echo "\$current_eleve_login[$i]=".$current_eleve_login[$i]."<br />";
					flush();
				}
				//==============================

				//++++++++++++++++++++++++++++++
				// Contrairement au dispositif pour les bulletins, on récupère le $current_eleve_login d'après la liste POSTée
				// Cette liste peut avoir été manipulée...
				// il faut contrôler si les élèves de la liste sont bien dans la classe sur la période indiquée
				if ($choix_periode=="intervalle") {
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE (login='".$current_eleve_login[$i]."' AND id_classe='$id_classe');";
				}
				else {
					$sql="SELECT 1=1 FROM j_eleves_classes WHERE (login='".$current_eleve_login[$i]."' AND id_classe='$id_classe' AND periode='$periode_num');";
				}
				//echo "$sql<br />";
				$test_appartenance_classe_periode=mysql_query($sql);

				$appartenance_classe_periode=mysql_num_rows($test_appartenance_classe_periode);
				//++++++++++++++++++++++++++++++
				if(mysql_num_rows($test_appartenance_classe_periode)>0) {
				//if(($appartenance_classe_periode!=0)&&($autorisation_acces=='y')) {
					// L'élève fait bien partie de la classe pour la période indiquée
					//echo "OK classe/période<br />";


					//+++++++++++++++++++++++++++++++++++
					//===============================================
					// A FAIRE
					// Contrôler qu'il n'y a pas d'usurpation d'accès
					$autorisation_acces='n';
					// Si c'est un prof
					if($_SESSION['statut']=='professeur') {
						// GepiAccesReleveProf               -> que les élèves de ses groupes
						// GepiAccesReleveProfTousEleves     -> tous les élèves de ses classes
						// GepiAccesReleveProfToutesClasses  -> tous les élèves de toutes les classes

						// GepiAccesReleveProfToutesClasses  -> tous les élèves de toutes les classes
						if(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") {
							// On vérifie seulement que c'est bien le login d'un élève d'une classe
							$sql="SELECT 1=1 FROM j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND login='".$current_eleve_login[$i]."';";
							$verif=mysql_query($sql);
							if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProf") == "yes") {
							$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
													j_groupes_professeurs jgp
											WHERE (jeg.id_groupe=jgp.id_groupe AND
												jeg.login='".$current_eleve_login[$i]."' AND
												jgp.login='".$_SESSION['login']."');";
							$verif=mysql_query($sql);
							if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") {
							$sql="SELECT 1=1 FROM j_eleves_classes jec,
													j_groupes_classes jgc,
													j_groupes_professeurs jgp
											WHERE (jgc.id_groupe=jgp.id_groupe AND
												jec.id_classe=jgc.id_classe AND
												jec.login='".$current_eleve_login[$i]."' AND
												jgp.login='".$_SESSION['login']."');";
							$verif=mysql_query($sql);
							if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProfP") == "yes") {
							$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND login='".$current_eleve_login[$i]."';";
							$verif=mysql_query($sql);
							if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						else {
							/*
							tentative_intrusion(2, "Tentative d'un professeur d'accéder à un relevé de notes (".$current_eleve_login[$i].") sans y être autorisé.");
							require("../lib/footer.inc.php");
							die();
							*/
							// Ou juste:
							$autorisation_acces='n';
						}
					}
					// Si c'est un CPE
					elseif(($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_cpe jec
							WHERE (jec.e_login='".$current_eleve_login[$i]."' AND
									jec.cpe_login='".$_SESSION['login']."');";
						$verif=mysql_query($sql);
						if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					// Si c'est un compte scolarité
					elseif (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_classes jec, j_scol_classes jsc
								WHERE (jsc.id_classe=jec.id_classe AND
										jec.login='".$current_eleve_login[$i]."' AND
										jsc.login='".$_SESSION['login']."');";
						$verif=mysql_query($sql);
						if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					// Si c'est un élève
					elseif (($_SESSION['statut'] == 'eleve') AND
							(getSettingValue("GepiAccesReleveEleve") == "yes") AND
							strtolower($current_eleve_login[$i])==strtolower($_SESSION['login'])) {
						$autorisation_acces='y';
					}
					// Si c'est un responsable
					elseif (($_SESSION['statut'] == 'responsable') AND
							(getSettingValue("GepiAccesReleveParent") == "yes")) {
						$sql="SELECT 1=1 FROM eleves e, responsables2 r, resp_pers rp
								WHERE (e.ele_id=r.ele_id AND
										r.pers_id=rp.pers_id AND
										e.login='".$current_eleve_login[$i]."' AND
										rp.login='".$_SESSION['login']."');";
						$verif=mysql_query($sql);
						if(mysql_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					//echo "\$current_eleve_login[$i]=$current_eleve_login[$i]<br />\n";
					//echo "\$_SESSION['login']=".$_SESSION['login']."<br />\n";
					//echo "$sql<br />";
					//$autorisation_acces='y';
					//===============================================
					//+++++++++++++++++++++++++++++++++++

					if($autorisation_acces=='y') {

						if ($affiche_categories) {
							// On utilise les valeurs spécifiées pour la classe en question
							$sql="SELECT DISTINCT jgc.id_groupe, jgm.id_matiere matiere, jgc.categorie_id ".
							"FROM j_eleves_groupes jeg, j_groupes_classes jgc, j_groupes_matieres jgm, j_matieres_categories_classes jmcc, matieres m " .
							"WHERE ( " .
							"jeg.login = '" . $current_eleve_login[$i] ."' AND " .
							"jgc.id_groupe = jeg.id_groupe AND " .
							"jgc.categorie_id = jmcc.categorie_id AND " .
							"jgc.id_classe = '".$id_classe."' AND " .
							"jgm.id_groupe = jgc.id_groupe AND " .
							"m.matiere = jgm.id_matiere";

							if($choix_periode!="intervalle") {$sql.=" AND jeg.periode='$periode_num'";}

							$sql.=") " .
							"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
						} else {
							$sql="SELECT DISTINCT jgc.id_groupe, jgc.categorie_id, jgc.coef, jgm.id_matiere matiere " .
							"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_eleves_groupes jeg " .
							"WHERE ( " .
							"jeg.login = '" . $current_eleve_login[$i] . "' AND " .
							"jgc.id_groupe = jeg.id_groupe AND " .
							"jgc.id_classe = '".$id_classe."' AND " .
							"jgm.id_groupe = jgc.id_groupe";

							if($choix_periode!="intervalle") {$sql.=" AND jeg.periode='$periode_num'";}

							$sql.=") " .
							"ORDER BY jgc.priorite,jgm.id_matiere";
						}
						$appel_liste_groupes = mysql_query($sql);
						$nombre_groupes = mysql_num_rows($appel_liste_groupes);

						$j = 0;
						while ($j < $nombre_groupes) {
							$current_groupe = mysql_result($appel_liste_groupes, $j, "id_groupe");
							$current_matiere = mysql_result($appel_liste_groupes, $j, "matiere");
							$current_groupe_cat = mysql_result($appel_liste_groupes, $j, "categorie_id");

							$tab_ele['groupe'][$j]['id_groupe']=$current_groupe;
							$tab_ele['groupe'][$j]['matiere']=$current_matiere;
							if ($affiche_categories) {
								$tab_ele['groupe'][$j]['id_cat']=$current_groupe_cat;
							}

							$call_profs = mysql_query("SELECT u.login FROM utilisateurs u, j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe='$current_groupe') ORDER BY j.ordre_prof");
							$nombre_profs = mysql_num_rows($call_profs);
							$k = 0;
							while ($k < $nombre_profs) {
								$current_matiere_professeur_login[$k] = mysql_result($call_profs, $k, "login");
								$tab_ele['groupe'][$j]['prof_login'][$k]=$current_matiere_professeur_login[$k];
								$k++;
							}

							$current_matiere_nom_complet_query = mysql_query("SELECT nom_complet FROM matieres WHERE matiere='$current_matiere'");
							$current_matiere_nom_complet = mysql_result($current_matiere_nom_complet_query, 0, "nom_complet");
							$tab_ele['groupe'][$j]['matiere_nom_complet']=$current_matiere_nom_complet;

							//if($avec_coef_devoir=="oui"){
							if($tab_releve[$id_classe][$periode_num]['rn_coefdev_si_diff']=='y') {
								// On teste s'il y a des coeff différents
								if ($choix_periode=="intervalle") {
									$sql="SELECT DISTINCT d.coef FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
									nd.login = '".$current_eleve_login[$i]."' and
									nd.id_devoir = d.id and
									d.display_parents='1' and
									d.id_racine = cn.id_cahier_notes and
									cn.id_groupe = '".$current_groupe."' and
									d.date >= '".$date_debut."' and
									d.date <= '".$date_fin."'
									)";
								}
								else {
									$sql="SELECT DISTINCT d.coef FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
									nd.login = '".$current_eleve_login[$i]."' and
									nd.id_devoir = d.id and
									d.display_parents='1' and
									d.id_racine = cn.id_cahier_notes and
									cn.id_groupe = '".$current_groupe."' and
									cn.periode = '".$periode_num."'
									)";
								}
								$res_differents_coef=mysql_query($sql);
								if(mysql_num_rows($res_differents_coef)>1){
									$differents_coef="y";
								}
								else{
									$differents_coef="n";
								}
								$tab_ele['groupe'][$j]['differents_coef']=$differents_coef;
							}


							//if ($choix_periode ==0) {
							if ($choix_periode=="intervalle") {
								//$sql1="SELECT d.coef, nd.note, d.nom_court, nd.statut FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
								$sql1="SELECT d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.date_ele_resp, d.note_sur, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
								nd.login = '".$current_eleve_login[$i]."' and
								nd.id_devoir = d.id and
								d.display_parents='1' and
								d.id_racine = cn.id_cahier_notes and
								cn.id_groupe = '".$current_groupe."' and
								d.date >= '".$date_debut."' and
								d.date <= '".$date_fin."'
								)
								ORDER BY d.date, d.nom_court, d.nom_complet
								";
							}
							else {
								$sql1 = "SELECT d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.date_ele_resp, d.note_sur, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
								nd.login = '".$current_eleve_login[$i]."' and
								nd.id_devoir = d.id and
								d.display_parents='1' and
								d.id_racine = cn.id_cahier_notes and
								cn.id_groupe = '".$current_groupe."' and
								cn.periode = '".$periode_num."'
								)
								ORDER BY d.date, d.nom_court, d.nom_complet
								";
							}
							$query_notes = mysql_query($sql1);
							//echo "$sql1<br />";
							//====================================================

							// Date actuelle pour le test de la date de visibilité des devoirs
							$timestamp_courant=time();

							$count_notes = mysql_num_rows($query_notes);
							$m=0;
							$mm=0;
							while($mm<$count_notes) {

								$visible="y";
								if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
									$date_ele_resp=@mysql_result($query_notes,$mm,'d.date_ele_resp');
									$tmp_tabdate=explode(" ",$date_ele_resp);
									$tabdate=explode("-",$tmp_tabdate[0]);

									//echo "\$date_ele_resp=$date_ele_resp<br />";
									//echo "\$tabdate[0]=$tabdate[0]<br />";
									//echo "\$tabdate[1]=$tabdate[1]<br />";
									//echo "\$tabdate[2]=$tabdate[2]<br />";
	
									$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
									if($timestamp_courant<$timestamp_limite) {
										$visible="n";
									}
									//echo "\$timestamp_courant=$timestamp_courant<br />";
									//echo "\$timestamp_limite=$timestamp_limite<br />";
								}

								if($visible=="y") {
									$eleve_display_app = @mysql_result($query_notes,$mm,'d.display_parents_app');
									$eleve_app = @mysql_result($query_notes,$mm,'nd.comment');
									if(getSettingValue("note_autre_que_sur_referentiel")=="V" || mysql_result($query_notes,$mm,'d.note_sur')!=getSettingValue("referentiel_note")) {
										$eleve_note = @mysql_result($query_notes,$mm,'nd.note')."/".@mysql_result($query_notes,$mm,'d.note_sur');
									} else {
										$eleve_note = @mysql_result($query_notes,$mm,'nd.note');
									}
									$eleve_statut = @mysql_result($query_notes,$mm,'nd.statut');
									$eleve_nom_court = @mysql_result($query_notes,$mm,'d.nom_court');
									$date_note = @mysql_result($query_notes,$mm,'d.date');
									$note_sur = @mysql_result($query_notes,$mm,'d.note_sur');
									$coef_devoir = @mysql_result($query_notes,$mm,'d.coef');
	
									$tab_ele['groupe'][$j]['devoir'][$m]['display_app']=$eleve_display_app;
									$tab_ele['groupe'][$j]['devoir'][$m]['app']=$eleve_app;
									$tab_ele['groupe'][$j]['devoir'][$m]['note']=$eleve_note;
									$tab_ele['groupe'][$j]['devoir'][$m]['statut']=$eleve_statut;
									$tab_ele['groupe'][$j]['devoir'][$m]['nom_court']=$eleve_nom_court;
									$tab_ele['groupe'][$j]['devoir'][$m]['date']=$date_note;
									$tab_ele['groupe'][$j]['devoir'][$m]['note_sur']=$note_sur;
									$tab_ele['groupe'][$j]['devoir'][$m]['coef']=$coef_devoir;
									// On ne récupère pas le nom long du devoir?

									//echo "\$eleve_nom_court=$eleve_nom_court<br />";
									//echo "\$eleve_note=$eleve_note<br />";
									//echo "\$eleve_statut=$eleve_statut<br />";

									$m++;
								}
								//echo "===================================<br />";

								$mm++;
							}

							$j++;
						}


						// Récup des infos sur l'élève, les responsables, le PP, le CPE,...
						$sql="SELECT * FROM eleves e WHERE e.login='".$current_eleve_login[$i]."';";
						$res_ele=mysql_query($sql);
						$lig_ele=mysql_fetch_object($res_ele);

						$tab_ele['login']=$current_eleve_login[$i];
						$tab_ele['nom']=$lig_ele->nom;
						$tab_ele['prenom']=$lig_ele->prenom;
						$tab_ele['sexe']=$lig_ele->sexe;
						$tab_ele['naissance']=formate_date($lig_ele->naissance);
						$tab_ele['elenoet']=$lig_ele->elenoet;
						$tab_ele['ele_id']=$lig_ele->ele_id;
						$tab_ele['no_gep']=$lig_ele->no_gep;

						$tab_ele['classe']=$classe;
						$tab_ele['id_classe']=$id_classe;
						$tab_ele['classe_nom_complet']=$classe_nom_complet;

						// Régime et redoublement
						$sql="SELECT * FROM j_eleves_regime WHERE login='".$current_eleve_login[$i]."';";
						$res_ele_reg=mysql_query($sql);
						if(mysql_num_rows($res_ele_reg)>0) {
							$lig_ele_reg=mysql_fetch_object($res_ele_reg);

							$tab_ele['regime']=$lig_ele_reg->regime;
							$tab_ele['doublant']=$lig_ele_reg->doublant;
						}

						//$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$current_eleve_login[$i]."' AND e.id = j.id_etablissement);";
						$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
						$data_etab = mysql_query($sql);
						if(mysql_num_rows($data_etab)>0) {
							$tab_ele['etab_id'] = @mysql_result($data_etab, 0, "id");
							$tab_ele['etab_nom'] = @mysql_result($data_etab, 0, "nom");
							$tab_ele['etab_niveau'] = @mysql_result($data_etab, 0, "niveau");
							$tab_ele['etab_type'] = @mysql_result($data_etab, 0, "type");
							$tab_ele['etab_cp'] = @mysql_result($data_etab, 0, "cp");
							$tab_ele['etab_ville'] = @mysql_result($data_etab, 0, "ville");

							if ($tab_ele['etab_niveau']!='') {
								foreach ($type_etablissement as $type_etab => $nom_etablissement) {
									if ($tab_ele['etab_niveau'] == $type_etab) {
										$tab_ele['etab_niveau_nom']=$nom_etablissement;
									}
								}
								if ($tab_ele['etab_cp']==0) {
									$tab_ele['etab_cp']='';
								}
								if ($tab_ele['etab_type']=='aucun') {
									$tab_ele['etab_type']='';
								}
								else {
									$tab_ele['etab_type']= $type_etablissement2[$tab_ele['etab_type']][$tab_ele['etab_niveau']];
								}
							}
						}

						// Récup infos CPE
						$sql="SELECT u.* FROM j_eleves_cpe jec, utilisateurs u WHERE e_login='".$current_eleve_login[$i]."' AND jec.cpe_login=u.login;";
						$res_cpe=mysql_query($sql);
						if(mysql_num_rows($res_cpe)>0) {
							$lig_cpe=mysql_fetch_object($res_cpe);
							$tab_ele['cpe']=array();

							$tab_ele['cpe']['login']=$lig_cpe->login;
							$tab_ele['cpe']['nom']=$lig_cpe->nom;
							$tab_ele['cpe']['prenom']=$lig_cpe->prenom;
							$tab_ele['cpe']['civilite']=$lig_cpe->civilite;
						}

						// Récup infos Prof Principal (prof_suivi)
						$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$current_eleve_login[$i]."' AND id_classe='$id_classe' AND jep.professeur=u.login;";
						$res_pp=mysql_query($sql);
						//echo "$sql<br />";
						if(mysql_num_rows($res_pp)>0) {
							$lig_pp=mysql_fetch_object($res_pp);
							$tab_ele['pp']=array();

							$tab_ele['pp']['login']=$lig_pp->login;
							$tab_ele['pp']['nom']=$lig_pp->nom;
							$tab_ele['pp']['prenom']=$lig_pp->prenom;
							$tab_ele['pp']['civilite']=$lig_pp->civilite;
						}

						// Récup infos responsables
						$sql="SELECT rp.*,ra.adr1,ra.adr2,ra.adr3,ra.adr3,ra.adr4,ra.cp,ra.pays,ra.commune,r.resp_legal FROM resp_pers rp,
														resp_adr ra,
														responsables2 r
									WHERE r.ele_id='".$tab_ele['ele_id']."' AND
											r.resp_legal!='0' AND
											r.pers_id=rp.pers_id AND
											rp.adr_id=ra.adr_id
									ORDER BY resp_legal;";
						$res_resp=mysql_query($sql);
						//echo "$sql<br />";
						if(mysql_num_rows($res_resp)>0) {
							$cpt=0;
							while($lig_resp=mysql_fetch_object($res_resp)) {
								$tab_ele['resp'][$cpt]=array();

								$tab_ele['resp'][$cpt]['pers_id']=$lig_resp->pers_id;

								$tab_ele['resp'][$cpt]['login']=$lig_resp->login;
								$tab_ele['resp'][$cpt]['nom']=$lig_resp->nom;
								$tab_ele['resp'][$cpt]['prenom']=$lig_resp->prenom;
								$tab_ele['resp'][$cpt]['civilite']=$lig_resp->civilite;
								$tab_ele['resp'][$cpt]['tel_pers']=$lig_resp->tel_pers;
								$tab_ele['resp'][$cpt]['tel_port']=$lig_resp->tel_port;
								$tab_ele['resp'][$cpt]['tel_prof']=$lig_resp->tel_prof;

								$tab_ele['resp'][$cpt]['adr1']=$lig_resp->adr1;
								$tab_ele['resp'][$cpt]['adr2']=$lig_resp->adr2;
								$tab_ele['resp'][$cpt]['adr3']=$lig_resp->adr3;
								$tab_ele['resp'][$cpt]['adr4']=$lig_resp->adr4;
								$tab_ele['resp'][$cpt]['cp']=$lig_resp->cp;
								$tab_ele['resp'][$cpt]['pays']=$lig_resp->pays;
								$tab_ele['resp'][$cpt]['commune']=$lig_resp->commune;

								$tab_ele['resp'][$cpt]['adr_id']=$lig_resp->adr_id;

								$tab_ele['resp'][$cpt]['resp_legal']=$lig_resp->resp_legal;

								$cpt++;
							}
						}

						// Vérification
						if(mysql_num_rows($res_resp)>2) {
							if($mode_bulletin=="html") {
								echo "<div class='alerte_erreur'><b style='color:red;'>Erreur:</b>";
								echo $tab_ele['nom']." ".$tab_ele['prenom']." a plus de deux responsables légaux 1 et 2.<br />C'est une anomalie.<br />";
								for ($z=0;$z<count($tab_ele['resp']);$z++) {
									echo $tab_ele['resp'][$z]['nom']." ".$tab_ele['resp'][$z]['prenom']." (<i>responsable légal ".$tab_ele['resp'][$z]['resp_legal']."</i>)<br />";
								}
								echo "Seuls les deux premiers apparaîtront sur des bulletins.";
								echo "</div>\n";
							}
						}

						// On affecte la partie élève $tab_ele dans $tab_releve
						//$tab_releve[$id_classe][$periode_num]['eleve'][$i]=$tab_ele;
						$tab_releve[$id_classe][$periode_num]['eleve'][]=$tab_ele;
						//echo "On affecte \$tab_releve[$id_classe][$periode_num]['eleve'][$i]<br />";
					}
				}
			}
		}
	}
?>
