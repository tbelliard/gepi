<?php
/**
 * Extrait les données pour les relevés de notes
 * 
 * 
 * @package Notes
 * @subpackage scripts
 * @license GNU/GPL
 * @see get_class_from_id()
 * @see getSettingValue()
 * @see recherche_conteneurs_enfants()
*/

	$debug_extract="n";
	$debug_ele_login="toto";
	$debug_id_groupe=2673;

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
	$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
		$categories[] = $row["id"];
	}


	$cat_names = array();
	foreach ($categories as $cat_id) {
		$sql="SELECT nom_complet FROM matieres_categories WHERE id='".$cat_id."';";
		$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);
		if ($res_cat) {
			$cat_names[$cat_id]=old_mysql_result($res_cat, 0);
		}
	}
	//===================================

	//debug_var();

	// Récupération des paramètres
	// Les valeurs des tableaux peuvent ne pas être affectées si aucune case n'est cochée
	$tab_rn_nomdev=isset($_POST['rn_nomdev']) ? $_POST['rn_nomdev'] : (isset($tab_rn_nomdev) ? $tab_rn_nomdev : array());
	$tab_rn_toutcoefdev=isset($_POST['rn_toutcoefdev']) ? $_POST['rn_toutcoefdev'] : (isset($tab_rn_toutcoefdev) ? $tab_rn_toutcoefdev : array());
	$tab_rn_coefdev_si_diff=isset($_POST['rn_coefdev_si_diff']) ? $_POST['rn_coefdev_si_diff'] : (isset($tab_rn_coefdev_si_diff) ? $tab_rn_coefdev_si_diff : array());

	$tab_rn_col_moy=isset($_POST['rn_col_moy']) ? $_POST['rn_col_moy'] : (isset($tab_rn_col_moy) ? $tab_rn_col_moy : array());

	$tab_rn_datedev=isset($_POST['rn_datedev']) ? $_POST['rn_datedev'] : (isset($tab_rn_datedev) ? $tab_rn_datedev : array());
	$tab_rn_app=isset($_POST['rn_app']) ? $_POST['rn_app'] : (isset($tab_rn_app) ? $tab_rn_app : array());
	$tab_rn_sign_chefetab=isset($_POST['rn_sign_chefetab']) ? $_POST['rn_sign_chefetab'] : (isset($tab_rn_sign_chefetab) ? $tab_rn_sign_chefetab : array());
	$tab_rn_sign_pp=isset($_POST['rn_sign_pp']) ? $_POST['rn_sign_pp'] : (isset($tab_rn_sign_pp) ? $tab_rn_sign_pp : array());
	$tab_rn_sign_resp=isset($_POST['rn_sign_resp']) ? $_POST['rn_sign_resp'] : (isset($tab_rn_sign_resp) ? $tab_rn_sign_resp : array());
	// Les deux suivants doivent être affectés (éventuellement avec des chaines vides)
	$tab_rn_sign_nblig=isset($_POST['rn_sign_nblig']) ? $_POST['rn_sign_nblig'] :(isset($tab_rn_sign_nblig) ? $tab_rn_sign_nblig :  array());
	$tab_rn_formule=isset($_POST['rn_formule']) ? $_POST['rn_formule'] : (isset($tab_rn_formule) ? $tab_rn_formule : array());

	$tab_rn_adr_resp=isset($_POST['rn_adr_resp']) ? $_POST['rn_adr_resp'] : (isset($tab_rn_adr_resp) ? $tab_rn_adr_resp : array());
	/*
	if(count($tab_rn_adr_resp)>count($tab_id_classe)/2) {
		savePref($_SESSION['login'], "pref_rn_adr_resp", "y");
	}
	else {
		savePref($_SESSION['login'], "pref_rn_adr_resp", "n");
	}
	*/
	// Bloc observation sur la droite pour le relevé PDF:
	$tab_rn_bloc_obs=isset($_POST['rn_bloc_obs']) ? $_POST['rn_bloc_obs'] : (isset($tab_rn_bloc_obs) ? $tab_rn_bloc_obs : array());
	
	$tab_rn_bloc_abs2=isset($_POST['rn_abs_2']) ? $_POST['rn_abs_2'] : (isset($tab_rn_bloc_abs2) ? $tab_rn_bloc_abs2 : array());

	$tab_rn_aff_classe_nom=isset($_POST['rn_aff_classe_nom']) ? $_POST['rn_aff_classe_nom'] : (isset($tab_rn_aff_classe_nom) ? $tab_rn_aff_classe_nom : array());

	$tab_rn_moy_min_max_classe=isset($_POST['rn_moy_min_max_classe']) ? $_POST['rn_moy_min_max_classe'] : (isset($tab_rn_moy_min_max_classe) ? $tab_rn_moy_min_max_classe : array());
	$tab_rn_moy_classe=isset($_POST['rn_moy_classe']) ? $_POST['rn_moy_classe'] : (isset($tab_rn_moy_classe) ? $tab_rn_moy_classe : array());

	$tab_rn_retour_ligne=isset($_POST['rn_retour_ligne']) ? $_POST['rn_retour_ligne'] : (isset($tab_rn_retour_ligne) ? $tab_rn_retour_ligne : array());
	$tab_rn_rapport_standard_min_font=isset($_POST['rn_rapport_standard_min_font']) ? $_POST['rn_rapport_standard_min_font'] : (isset($tab_rn_rapport_standard_min_font) ? $tab_rn_rapport_standard_min_font : array());

	$chaine_coef=isset($_POST['chaine_coef']) ? $_POST['chaine_coef'] : (isset($chaine_coef) ? $chaine_coef : "");

	$rn_couleurs_alternees=isset($_POST['rn_couleurs_alternees']) ? $_POST['rn_couleurs_alternees'] : (isset($rn_couleurs_alternees) ? $rn_couleurs_alternees : "n");
	savePref($_SESSION['login'], "rn_couleurs_alternees", $rn_couleurs_alternees);

	//+++++++++++++++++++++++++++++++++++
	// A FAIRE
	// Contrôler les paramètres reçus en fonction de
	// GepiAccesOptionsReleveParent
	// GepiAccesOptionsReleveEleve
	//+++++++++++++++++++++++++++++++++++

    /**
     * Renvoie les conteneurs enfants dans un tableau
     * @global array
     * @param int $id_parent 
     * @todo Déjà vu ailleurs
     */
	function recherche_conteneurs_enfants($id_parent) {
		global $tab_conteneurs_enfants;
		$sql="SELECT id FROM cn_conteneurs where parent='$id_parent';";
		//echo "$sql<br />\n";
		$res_conteneurs_enfants=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_conteneurs_enfants)>0) {
			while($lig=mysqli_fetch_object($res_conteneurs_enfants)) {
				$tab_conteneurs_enfants[]=$lig->id;
				recherche_conteneurs_enfants($lig->id);
			}
		}
	}


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

		if($choix_periode=='intervalle') {
			$tab_periode_num[0]="intervalle";
		}

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
			$tab_releve[$id_classe][$periode_num]['rn_nomdev']=isset($tab_rn_nomdev[$loop_classe]) ? $tab_rn_nomdev[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_toutcoefdev']=isset($tab_rn_toutcoefdev[$loop_classe]) ? $tab_rn_toutcoefdev[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_coefdev_si_diff']=isset($tab_rn_coefdev_si_diff[$loop_classe]) ? $tab_rn_coefdev_si_diff[$loop_classe] : "n";

			if((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
				(($_SESSION['statut']=='eleve')&&(getSettingAOui('GepiAccesColMoyReleveEleve')))||
				(($_SESSION['statut']=='responsable')&&(getSettingAOui('GepiAccesColMoyReleveParent')))
				) {
				$tab_releve[$id_classe][$periode_num]['rn_col_moy']=isset($tab_rn_col_moy[$loop_classe]) ? $tab_rn_col_moy[$loop_classe] : "n";
			}
			else {
				$tab_releve[$id_classe][$periode_num]['rn_col_moy']="n";
			}

			$tab_releve[$id_classe][$periode_num]['rn_app']=isset($tab_rn_app[$loop_classe]) ? $tab_rn_app[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_datedev']=isset($tab_rn_datedev[$loop_classe]) ? $tab_rn_datedev[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_chefetab']=isset($tab_rn_sign_chefetab[$loop_classe]) ? $tab_rn_sign_chefetab[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_pp']=isset($tab_rn_sign_pp[$loop_classe]) ? $tab_rn_sign_pp[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_sign_resp']=isset($tab_rn_sign_resp[$loop_classe]) ? $tab_rn_sign_resp[$loop_classe] : "n";

			$tab_releve[$id_classe][$periode_num]['rn_sign_nblig']=isset($tab_rn_sign_nblig[$loop_classe]) ? $tab_rn_sign_nblig[$loop_classe] : 3;
			$tab_releve[$id_classe][$periode_num]['rn_formule']=isset($tab_rn_formule[$loop_classe]) ? $tab_rn_formule[$loop_classe] : "";

			$tab_releve[$id_classe][$periode_num]['rn_adr_resp']=isset($tab_rn_adr_resp[$loop_classe]) ? $tab_rn_adr_resp[$loop_classe] : "n";

			// Bloc observation sur le relevé PDF
			$tab_releve[$id_classe][$periode_num]['rn_bloc_obs']=isset($tab_rn_bloc_obs[$loop_classe]) ? $tab_rn_bloc_obs[$loop_classe] : "n";

			$tab_releve[$id_classe][$periode_num]['rn_aff_classe_nom']=isset($tab_rn_aff_classe_nom[$loop_classe]) ? $tab_rn_aff_classe_nom[$loop_classe] : "n";

			$affiche_adresse=$tab_releve[$id_classe][$periode_num]['rn_adr_resp'];
			$tab_releve[$id_classe][$periode_num]['affiche_adresse']=$affiche_adresse;

			$tab_releve[$id_classe][$periode_num]['rn_moy_min_max_classe']=isset($tab_rn_moy_min_max_classe[$loop_classe]) ? $tab_rn_moy_min_max_classe[$loop_classe] : "n";
			$tab_releve[$id_classe][$periode_num]['rn_moy_classe']=isset($tab_rn_moy_classe[$loop_classe]) ? $tab_rn_moy_classe[$loop_classe] : "n";

			$tab_releve[$id_classe][$periode_num]['rn_retour_ligne']=isset($tab_rn_retour_ligne[$loop_classe]) ? $tab_rn_retour_ligne[$loop_classe] : "n";
			//$_SESSION['pref_rn_retour_ligne']=$tab_releve[$id_classe][$periode_num]['rn_retour_ligne'];

			$tab_releve[$id_classe][$periode_num]['rn_rapport_standard_min_font']=((isset($tab_rn_rapport_standard_min_font[$loop_classe]))&&($tab_rn_rapport_standard_min_font[$loop_classe]!='')&&(preg_match("/^[0-9.]*$/",$tab_rn_rapport_standard_min_font[$loop_classe]))&&($tab_rn_rapport_standard_min_font[$loop_classe]>0)) ? $tab_rn_rapport_standard_min_font[$loop_classe] : 3;

			//$_SESSION['pref_rn_rapport_standard_min_font']=$tab_releve[$id_classe][$periode_num]['rn_rapport_standard_min_font'];

			// Informations sur la période
			if ($choix_periode=="intervalle") {
				$tab_releve[$id_classe][$periode_num]['intervalle']['debut']=$display_date_debut;
				$tab_releve[$id_classe][$periode_num]['intervalle']['fin']=$display_date_fin;
			}
			else {
				$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$periode_num';";
				$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_per=mysqli_fetch_object($res_per);
				$tab_releve[$id_classe][$periode_num]['num_periode']=$lig_per->num_periode;
				$tab_releve[$id_classe][$periode_num]['nom_periode']=$lig_per->nom_periode;
				$tab_releve[$id_classe][$periode_num]['verouiller']=$lig_per->verouiller;
			}
			
			// Bloc absence sur le relevé HTML
			$tab_releve[$id_classe][$periode_num]['rn_abs_2']=isset($tab_rn_bloc_abs2[$loop_classe]) ? $tab_rn_bloc_abs2[$loop_classe] : "n";

			// Liste des élèves à éditer/afficher/imprimer (sélection):
			if($choix_periode=="intervalle") {
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_intervalle']) ? $_POST['tab_selection_ele_'.$loop_classe.'_intervalle'] : array();
			}
			else {
				$tab_selection_eleves=isset($_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num]) ? $_POST['tab_selection_ele_'.$loop_classe.'_'.$loop_periode_num] : array();
			}

			if((count($tab_selection_eleves)==0)&&(isset($ele_login))&&($ele_login!='')) {
				$tab_selection_eleves[]=$ele_login;
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
			$res_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			$lig_classe=mysqli_fetch_object($res_classe);

			$tab_releve[$id_classe][$periode_num]['id_classe']=$lig_classe->id;
			$tab_releve[$id_classe][$periode_num]['classe']=$lig_classe->classe;
			$tab_releve[$id_classe][$periode_num]['classe_nom_complet']=$lig_classe->nom_complet;
			// Formule du bulletin:
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
			$res_eff_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			$eff_classe=mysqli_num_rows($res_eff_classe);


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
				//echo "$sql<br />\n";
				$test_appartenance_classe_periode=mysqli_query($GLOBALS["mysqli"], $sql);

				$appartenance_classe_periode=mysqli_num_rows($test_appartenance_classe_periode);
				//++++++++++++++++++++++++++++++
				if(mysqli_num_rows($test_appartenance_classe_periode)>0) {

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
							$verif=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProf") == "yes") {
							$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
													j_groupes_professeurs jgp
											WHERE (jeg.id_groupe=jgp.id_groupe AND
												jeg.login='".$current_eleve_login[$i]."' AND
												jgp.login='".$_SESSION['login']."');";
							$verif=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProfTousEleves") == "yes") {
							$sql="SELECT 1=1 FROM j_eleves_classes jec,
													j_groupes_classes jgc,
													j_groupes_professeurs jgp
											WHERE (jgc.id_groupe=jgp.id_groupe AND
												jec.id_classe=jgc.id_classe AND
												jec.login='".$current_eleve_login[$i]."' AND
												jgp.login='".$_SESSION['login']."');";
							$verif=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
						}
						elseif (getSettingValue("GepiAccesReleveProfP") == "yes") {
							if(is_pp($_SESSION['login'], $id_classe)) {
								if(getSettingAOui('GepiAccesPPTousElevesDeLaClasse')) {
									// Le prof est PP de la classe, on lui donne l'accès à tous les élèves de la classe
									$autorisation_acces='y';
								}
								else {
									// Le prof est PP d'au moins une partie des élèves de la classe
									// L'est-il de l'élève courant?
									$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE professeur='".$_SESSION['login']."' AND login='".$current_eleve_login[$i]."';";
									$verif=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
								}
							}
							else {
								// Le prof n'est pas PP dans cette classe
								$autorisation_acces='n';
							}
						}
						else {
							// Ou juste:
							$autorisation_acces='n';
						}
					}
					// Si c'est un CPE
					elseif(($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpeTousEleves") == "yes")) {
						$autorisation_acces='y';
					}
					elseif(($_SESSION['statut'] == 'cpe') AND (getSettingValue("GepiAccesReleveCpe") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_cpe jec
							WHERE (jec.e_login='".$current_eleve_login[$i]."' AND
									jec.cpe_login='".$_SESSION['login']."');";
						$verif=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					// Si c'est un compte scolarité
					elseif (($_SESSION['statut'] == 'scolarite') AND (getSettingValue("GepiAccesReleveScol") == "yes")) {
						$sql="SELECT 1=1 FROM j_eleves_classes jec, j_scol_classes jsc
								WHERE (jsc.id_classe=jec.id_classe AND
										jec.login='".$current_eleve_login[$i]."' AND
										jsc.login='".$_SESSION['login']."');";
						$verif=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					// Si c'est un élève
					elseif (($_SESSION['statut'] == 'eleve') AND
							(getSettingValue("GepiAccesReleveEleve") == "yes") AND
							my_strtolower($current_eleve_login[$i])==my_strtolower($_SESSION['login'])) {
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
						$verif=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($verif)>0) {$autorisation_acces='y';}
					}
					// Si c'est un compte secours
					elseif ($_SESSION['statut'] == 'secours') {
						$autorisation_acces='y';
					}
					elseif(($_SESSION['statut'] == 'autre') AND (acces("/cahier_notes/visu_releve_notes_bis.php", $_SESSION['statut'] == 'autre'))) {
						$autorisation_acces='y';
					}

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
							"m.matiere = jgm.id_matiere" .
							" AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n')";

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
							"jgm.id_groupe = jgc.id_groupe" .
							" AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='cahier_notes' AND visible='n')";

							if($choix_periode!="intervalle") {$sql.=" AND jeg.periode='$periode_num'";}

							$sql.=") " .
							"ORDER BY jgc.priorite,jgm.id_matiere";
						}
//echo "$sql<br />\n";
						$appel_liste_groupes = mysqli_query($GLOBALS["mysqli"], $sql);
						$nombre_groupes = mysqli_num_rows($appel_liste_groupes);

						$j = 0;
						while ($j < $nombre_groupes) {
							$current_groupe = old_mysql_result($appel_liste_groupes, $j, "id_groupe");
							$current_matiere = old_mysql_result($appel_liste_groupes, $j, "matiere");
							$current_groupe_cat = old_mysql_result($appel_liste_groupes, $j, "categorie_id");

							$tab_ele['groupe'][$j]['id_groupe']=$current_groupe;
							$tab_ele['groupe'][$j]['matiere']=$current_matiere;
							if ($affiche_categories) {
								$tab_ele['groupe'][$j]['id_cat']=$current_groupe_cat;
							}

							$call_profs = mysqli_query($GLOBALS["mysqli"], "SELECT u.login FROM utilisateurs u, j_groupes_professeurs j WHERE ( u.login = j.login and j.id_groupe='$current_groupe') ORDER BY j.ordre_prof");
							$nombre_profs = mysqli_num_rows($call_profs);
							$k = 0;
							while ($k < $nombre_profs) {
								$current_matiere_professeur_login[$k] = old_mysql_result($call_profs, $k, "login");
								$tab_ele['groupe'][$j]['prof_login'][$k]=$current_matiere_professeur_login[$k];
								$k++;
							}

							if(getSettingValue('bul_rel_nom_matieres')=='nom_groupe') {
								$current_matiere_nom_complet = sql_query1("SELECT name FROM groupes WHERE id='$current_groupe'");
							}
							elseif(getSettingValue('bul_rel_nom_matieres')=='description_groupe') {
								$current_matiere_nom_complet = sql_query1("SELECT description FROM groupes WHERE id='$current_groupe'");
							}
							else {
								$current_matiere_nom_complet_query = mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet FROM matieres WHERE matiere='$current_matiere'");
								$current_matiere_nom_complet = old_mysql_result($current_matiere_nom_complet_query, 0, "nom_complet");
							}
							$tab_ele['groupe'][$j]['matiere_nom_complet']=$current_matiere_nom_complet;

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
								$res_differents_coef=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_differents_coef)>1){
									$differents_coef="y";
								}
								else{
									$differents_coef="n";
								}
								$tab_ele['groupe'][$j]['differents_coef']=$differents_coef;
							}

							// PB: en mode intervalle, on ne sait pas quel cahier de notes récupérer (quelle période?)
							$sql="SELECT DISTINCT id_cahier_notes, periode FROM cn_cahier_notes WHERE id_groupe='$current_groupe' ORDER BY periode;";
							//echo "$sql<br />\n";
							if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
								echo "$sql<br />\n";
							}
							$res_grp_id_cn=mysqli_query($GLOBALS["mysqli"], $sql);
							while($lig_grp_id_cn=mysqli_fetch_object($res_grp_id_cn)) {

								// On n'affiche la moyenne que sur une période, pas intervalle
								if($choix_periode!="intervalle") {
									if($tab_releve[$id_classe][$periode_num]['rn_col_moy']=="y") {
										$sql="SELECT * FROM cn_notes_conteneurs WHERE login='".$current_eleve_login[$i]."' AND id_conteneur='".$lig_grp_id_cn->id_cahier_notes."';";
										if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
											echo "$sql<br />\n";
										}
										$res_moy=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_moy)>0) {
											$lig_moy=mysqli_fetch_object($res_moy);
											if($lig_moy->statut=='y') {
												$tab_ele['groupe'][$j]['moyenne'][$lig_grp_id_cn->periode]=$lig_moy->note;
											}
											else {
												$tab_ele['groupe'][$j]['moyenne'][$lig_grp_id_cn->periode]="-";
											}
										}
										else {
											$tab_ele['groupe'][$j]['moyenne'][$lig_grp_id_cn->periode]="-";
										}
									}
								}

								// Recherche des enfants de niveau 1
								$sql="SELECT id, nom_court, nom_complet, display_parents, coef FROM cn_conteneurs where id_racine='$lig_grp_id_cn->id_cahier_notes' AND parent=id_racine;";
								if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
									echo "$sql<br />\n";
								}
								$res_conteneurs_niv1=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_conteneurs_niv1)>0) {
									$cpt=0;
									$tab_coef_boites=array();
									while($lig_cnt=mysqli_fetch_object($res_conteneurs_niv1)) {
										unset($tab_conteneurs_enfants);
										$tab_conteneurs_enfants=array();

										$tab_ele['groupe'][$j]['existence_sous_conteneurs']='y';
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['periode']=$lig_grp_id_cn->periode;
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['id_racine']=$lig_cnt->id;
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['nom_court']=$lig_cnt->nom_court;
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['nom_complet']=$lig_cnt->nom_complet;
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['display_parents']=$lig_cnt->display_parents;
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['coef']=$lig_cnt->coef;
										if(!in_array($lig_cnt->coef, $tab_coef_boites)) {
											$tab_coef_boites[]=$lig_cnt->coef;
										}

										recherche_conteneurs_enfants($lig_cnt->id);
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['conteneurs_enfants']=$tab_conteneurs_enfants;

										$sql="SELECT cnc.* FROM cn_notes_conteneurs cnc, cn_conteneurs cc WHERE (
										cnc.login='".$current_eleve_login[$i]."' AND
										cnc.id_conteneur=cc.id AND
										cc.id='$lig_cnt->id'
										);";
										if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
											echo "$sql<br />\n";
										}
										// DEBUG:
										//if($current_groupe==760) {echo "$sql<br />\n";}
										$res_note_conteneur=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_note_conteneur)==0) {
											$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']="-";
										}
										else {
											$lig_note_conteneur=mysqli_fetch_object($res_note_conteneur);
											if($lig_note_conteneur->statut=='y') {
												$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']=$lig_note_conteneur->note;
											}
											else {
												$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']="-";
											}
										}
										// DEBUG:
										//if($current_groupe==760) {echo "\$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']=".$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']."<br />\n";}
										if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
											echo "\$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']=".$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['conteneurs'][$cpt]['moy']."<br />\n";
										}

										$cpt++;
									}
									if(count($tab_coef_boites)>1) {
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['temoin_coef_differents_conteneurs']="y";
									}
									else {
										$tab_ele['groupe'][$j]['id_cn'][$lig_grp_id_cn->id_cahier_notes]['temoin_coef_differents_conteneurs']="n";
									}

								}
							}

							if ($choix_periode=="intervalle") {
								$sql1="SELECT cn.id_cahier_notes, d.id, d.id_conteneur, d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.date_ele_resp, d.note_sur, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
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
								$sql1 = "SELECT cn.id_cahier_notes, d.id, d.id_conteneur, d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.date_ele_resp, d.note_sur, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
								nd.login = '".$current_eleve_login[$i]."' and
								nd.id_devoir = d.id and
								d.display_parents='1' and
								d.id_racine = cn.id_cahier_notes and
								cn.id_groupe = '".$current_groupe."' and
								cn.periode = '".$periode_num."'
								)
								ORDER BY d.date, d.nom_court, d.nom_complet
								;";
							}
							if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
								echo "$sql1<br />";
							}
							$query_notes = mysqli_query($GLOBALS["mysqli"], $sql1);
							//echo "$sql1<br />";
							
							// Date actuelle pour le test de la date de visibilité des devoirs
							$timestamp_courant=time();

							$count_notes = mysqli_num_rows($query_notes);
							$m=0;
							$mm=0;
							//while($mm<$count_notes) {
							while($obj_note_courant=$query_notes->fetch_object()) {

								$visible="y";
								if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
									//$date_ele_resp=@old_mysql_result($query_notes,$mm,'d.date_ele_resp');
									$date_ele_resp=@old_mysql_result($query_notes,$mm,'date_ele_resp');
									$date_ele_resp=$obj_note_courant->date_ele_resp;
									$tmp_tabdate=explode(" ",$date_ele_resp);
									$tabdate=explode("-",$tmp_tabdate[0]);

									$timestamp_limite=mktime(0,0,0,$tabdate[1],$tabdate[2],$tabdate[0]);
									if($timestamp_courant<$timestamp_limite) {
										$visible="n";
									}
								}

								if($visible=="y") {
									//$eleve_display_app = @old_mysql_result($query_notes,$mm,'d.display_parents_app');
									$eleve_display_app=$obj_note_courant->display_parents_app;
									//$eleve_app = @old_mysql_result($query_notes,$mm,'nd.comment');
									$eleve_app=$obj_note_courant->comment;
									//if(getSettingValue("note_autre_que_sur_referentiel")=="V" || old_mysql_result($query_notes,$mm,'d.note_sur')!=getSettingValue("referentiel_note")) {
									if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $obj_note_courant->note_sur!=getSettingValue("referentiel_note")) {
										//$eleve_note = @old_mysql_result($query_notes,$mm,'nd.note')."/".@old_mysql_result($query_notes,$mm,'d.note_sur');
										$eleve_note = $obj_note_courant->note."/".$obj_note_courant->note_sur;
									} else {
										//$eleve_note = @old_mysql_result($query_notes,$mm,'nd.note');
										$eleve_note = $obj_note_courant->note;
									}
									$eleve_statut = $obj_note_courant->statut;
									$eleve_nom_court = $obj_note_courant->nom_court;
									$date_note = $obj_note_courant->date;
									$note_sur = $obj_note_courant->note_sur;
									$coef_devoir = $obj_note_courant->coef;

									//===========================================
									// Pour utiliser en DEBUG	
									$current_id_devoir = $obj_note_courant->id;
									$tab_ele['groupe'][$j]['devoir'][$m]['id_devoir']=$current_id_devoir;
									//===========================================
	
									$tab_ele['groupe'][$j]['devoir'][$m]['display_app']=$eleve_display_app;
									$tab_ele['groupe'][$j]['devoir'][$m]['app']=$eleve_app;
									$tab_ele['groupe'][$j]['devoir'][$m]['note']=$eleve_note;
									$tab_ele['groupe'][$j]['devoir'][$m]['statut']=$eleve_statut;
									$tab_ele['groupe'][$j]['devoir'][$m]['nom_court']=$eleve_nom_court;
									$tab_ele['groupe'][$j]['devoir'][$m]['date']=$date_note;
									$tab_ele['groupe'][$j]['devoir'][$m]['note_sur']=$note_sur;
									$tab_ele['groupe'][$j]['devoir'][$m]['coef']=$coef_devoir;

									// AJOUT 20091113
									$tab_ele['groupe'][$j]['devoir'][$m]['id_cahier_notes']=$obj_note_courant->id_cahier_notes;
									$tab_ele['groupe'][$j]['devoir'][$m]['id_conteneur']=$obj_note_courant->id_conteneur;

									// On ne récupère pas le nom long du devoir?
									if(($debug_extract=='y')&&($tab_ele['groupe'][$j]['id_groupe']==$debug_id_groupe)&&($current_eleve_login[$i]==$debug_ele_login)) {
										echo "\$tab_ele['groupe'][$j]['devoir'][$m]['note']=".$tab_ele['groupe'][$j]['devoir'][$m]['note']." (\$current_id_devoir=$current_id_devoir et \$id_cahier_notes=".$tab_ele['groupe'][$j]['devoir'][$m]['id_cahier_notes']." et \$id_conteneur=".$tab_ele['groupe'][$j]['devoir'][$m]['id_conteneur'].")<br />";
										echo "\$eleve_note=$eleve_note<br />";
										echo "\$eleve_display_app=$eleve_display_app<br />";
									}

										$id_dev= $obj_note_courant->id;
										if(!isset($tab_moy_min_max_classe[$id_dev])) {
											$tab_moy_min_max_classe[$id_dev]=array();
											$sql2="SELECT min(note) AS note_min_classe, max(note) AS note_max_classe, ROUND(AVG(note),1) AS moy_classe FROM cn_notes_devoirs WHERE id_devoir='$id_dev' AND statut='';";
											//echo "$sql2<br />\n";
											$res_min_max_classe=mysqli_query($GLOBALS["mysqli"], $sql2);
											if(mysqli_num_rows($res_min_max_classe)>0) {
												$lig_min_max_classe=mysqli_fetch_object($res_min_max_classe);
												$tab_moy_min_max_classe[$id_dev]['min']=$lig_min_max_classe->note_min_classe;
												$tab_moy_min_max_classe[$id_dev]['max']=$lig_min_max_classe->note_max_classe;
												$tab_moy_min_max_classe[$id_dev]['moy_classe']=$lig_min_max_classe->moy_classe;
											}
											else {
												$tab_moy_min_max_classe[$id_dev]['min']="-";
												$tab_moy_min_max_classe[$id_dev]['max']="-";
												$tab_moy_min_max_classe[$id_dev]['moy_classe']="-";
											}
										}
									$tab_ele['groupe'][$j]['devoir'][$m]['min']=$tab_moy_min_max_classe[$id_dev]['min'];
									$tab_ele['groupe'][$j]['devoir'][$m]['max']=$tab_moy_min_max_classe[$id_dev]['max'];
									$tab_ele['groupe'][$j]['devoir'][$m]['moy_classe']=$tab_moy_min_max_classe[$id_dev]['moy_classe'];
									//=================================

									$m++;
								}

								$mm++;
							}

							$j++;
						}


						// Récup des infos sur l'élève, les responsables, le PP, le CPE,...
						$sql="SELECT * FROM eleves e WHERE e.login='".$current_eleve_login[$i]."';";
						$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						$lig_ele=mysqli_fetch_object($res_ele);

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
						$res_ele_reg=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_ele_reg)>0) {
							$lig_ele_reg=mysqli_fetch_object($res_ele_reg);

							$tab_ele['regime']=$lig_ele_reg->regime;
							$tab_ele['doublant']=$lig_ele_reg->doublant;
						}

						$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
						$data_etab = mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($data_etab)>0) {
							$obj_etab=$data_etab->fetch_object();
							$tab_ele['etab_id'] = $obj_etab->id;
							$tab_ele['etab_nom'] = $obj_etab->nom;
							$tab_ele['etab_niveau'] = $obj_etab->niveau;
							$tab_ele['etab_type'] = $obj_etab->type;
							$tab_ele['etab_cp'] = $obj_etab->cp;
							$tab_ele['etab_ville'] = $obj_etab->ville;

							if ($tab_ele['etab_niveau']!='') {
								foreach ($type_etablissement as $type_etab => $nom_etablissement) {
									if ($tab_ele['etab_niveau'] == $type_etab) {
										$tab_ele['etab_niveau_nom']=$nom_etablissement;
									}
								}
								if ($tab_ele['etab_cp']==0) {
									$tab_ele['etab_cp']='';
								}
								if (($tab_ele['etab_type']=='aucun')||($tab_ele['etab_type']=='')) {
									$tab_ele['etab_type']='';
								}
								else {
									$tab_ele['etab_type']= $type_etablissement2[$tab_ele['etab_type']][$tab_ele['etab_niveau']];
								}
							}
						}
						else {
							$tab_ele['etab_id'] = "";
							$tab_ele['etab_nom'] = "Non renseigné";
							$tab_ele['etab_niveau'] = "";
							$tab_ele['etab_type'] = "";
							$tab_ele['etab_cp'] = "";
							$tab_ele['etab_ville'] = "";
						}

						// Récup infos CPE
						$sql="SELECT u.* FROM j_eleves_cpe jec, utilisateurs u WHERE e_login='".$current_eleve_login[$i]."' AND jec.cpe_login=u.login;";
						$res_cpe=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_cpe)>0) {
							$lig_cpe=mysqli_fetch_object($res_cpe);
							$tab_ele['cpe']=array();

							$tab_ele['cpe']['login']=$lig_cpe->login;
							$tab_ele['cpe']['nom']=$lig_cpe->nom;
							$tab_ele['cpe']['prenom']=$lig_cpe->prenom;
							$tab_ele['cpe']['civilite']=$lig_cpe->civilite;
						}

						// Récup infos Prof Principal (prof_suivi)
						$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$current_eleve_login[$i]."' AND id_classe='$id_classe' AND jep.professeur=u.login;";
						$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />\n";
						if(mysqli_num_rows($res_pp)>0) {
							$lig_pp=mysqli_fetch_object($res_pp);
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
						$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />\n";
						if(mysqli_num_rows($res_resp)>0) {
							$cpt=0;
							while($lig_resp=mysqli_fetch_object($res_resp)) {
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
						if(mysqli_num_rows($res_resp)>2) {
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

						// Récup infos responsables non légaux, mais pointés comme destinataires des bulletins
						$sql="SELECT rp.*,ra.adr1,ra.adr2,ra.adr3,ra.adr3,ra.adr4,ra.cp,ra.pays,ra.commune,r.resp_legal FROM resp_pers rp,
														resp_adr ra,
														responsables2 r
									WHERE r.ele_id='".$tab_ele['ele_id']."' AND
											r.resp_legal='0' AND
											r.pers_id=rp.pers_id AND
											rp.adr_id=ra.adr_id AND
											r.envoi_bulletin='y'
									ORDER BY resp_legal;";
						$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
						//echo "$sql<br />";
						if(mysqli_num_rows($res_resp)>0) {
							$cpt=2;
							while($lig_resp=mysqli_fetch_object($res_resp)) {
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

						// On affecte la partie élève $tab_ele dans $tab_releve
						$tab_releve[$id_classe][$periode_num]['eleve'][]=$tab_ele;
					}
				}
			}
		}
	}

?>
