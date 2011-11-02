<?php

/*
 *
 *
 * @version $Id: visu_ele_func.lib.php 7952 2011-08-24 14:17:16Z jjocal $
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

function jour_en_fr($en){
	if(substr(strtolower($en),0,3)=='mon') {
		return 'lundi';
	}
	elseif(substr(strtolower($en),0,3)=='tue') {
		return 'mardi';
	}
	elseif(substr(strtolower($en),0,3)=='wed') {
		return 'mercredi';
	}
	elseif(substr(strtolower($en),0,3)=='thu') {
		return 'jeudi';
	}
	elseif(substr(strtolower($en),0,3)=='fri') {
		return 'vendredi';
	}
	elseif(substr(strtolower($en),0,3)=='sat') {
		return 'samedi';
	}
	elseif(substr(strtolower($en),0,3)=='sun') {
		return 'dimanche';
	}
	else {return "";}
}
/*
function get_commune($code_commune_insee,$mode){
	$retour="";

	$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		if($mode==0) {
			$retour=$lig->commune;
		}
		else {
			$retour=$lig->commune." (<i>".$lig->departement."</i>)";
		}
	}
	return $retour;
}
*/
function info_eleve($ele_login) {
	global $ele_lieu_naissance;
	global $active_cahiers_texte;
	global $date_ct1, $date_ct2;
	global $type_etablissement, $type_etablissement2;

	global $acces_eleve,
		$acces_responsables,
		$acces_enseignements,
		$acces_releves,
		$acces_bulletins,
		$acces_cdt,
		$acces_anna;

	global $date_debut_disc, $date_fin_disc;

	unset($tab_ele);
	$tab_ele=array();

	// Récup des infos sur l'élève, les responsables, le PP, le CPE,...
	$sql="SELECT * FROM eleves e WHERE e.login='".$ele_login."';";
	$res_ele=mysql_query($sql);
	$lig_ele=mysql_fetch_object($res_ele);

	$tab_ele['login']=$ele_login;
	$tab_ele['nom']=$lig_ele->nom;
	$tab_ele['prenom']=$lig_ele->prenom;
	$tab_ele['sexe']=$lig_ele->sexe;
	$tab_ele['naissance']=formate_date($lig_ele->naissance);
	$tab_ele['elenoet']=$lig_ele->elenoet;
	$tab_ele['ele_id']=$lig_ele->ele_id;
	$tab_ele['no_gep']=$lig_ele->no_gep;
	$tab_ele['email']=$lig_ele->email;
	$tab_ele['date_sortie']=$lig_ele->date_sortie;

	$tab_ele['code_lieu_naissance']=$lig_ele->lieu_naissance;
	if($ele_lieu_naissance=="y") {
		$tab_ele['lieu_naissance']=get_commune($tab_ele['code_lieu_naissance'],1);
	}
	else {
		$tab_ele['lieu_naissance']="";
	}

	$tab_ele['prof_liste_email']="";
	$tab_ele['tab_prof_liste_email']=array();

	/*
	$tab_ele['classe']=array();
	$sql="SELECT DISTINCT c.*,jec.periode FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
	$res_clas=mysql_query($sql);
	if(mysql_num_rows($res_clas)>0) {
		$tab_ele['liste_classes']="";

		$cpt=0;
		while($lig_clas=mysql_fetch_object($res_clas)) {
			if($cpt>0) {$tab_ele['liste_classes'].=", ";}
			$tab_ele['liste_classes']=$lig_clas->classe;

			$tab_ele['classe'][$cpt]['id_classe']=$lig_clas->id;
			$tab_ele['classe'][$cpt]['classe']=$lig_clas->classe;
			$tab_ele['classe'][$cpt]['nom_complet']=$lig_clas->nom_complet;
			$tab_ele['classe'][$cpt]['periode']=$lig_clas->periode;

			// Récup infos Prof Principal (prof_suivi)
			$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$ele_login."' AND id_classe='".$lig_clas->id."' AND jep.professeur=u.login;";
			$res_pp=mysql_query($sql);
			//echo "$sql<br />";
			if(mysql_num_rows($res_pp)>0) {
				$lig_pp=mysql_fetch_object($res_pp);
				$tab_ele['classe'][$cpt]['pp']=array();

				$tab_ele['classe'][$cpt]['pp']['login']=$lig_pp->login;
				$tab_ele['classe'][$cpt]['pp']['nom']=$lig_pp->nom;
				$tab_ele['classe'][$cpt]['pp']['prenom']=$lig_pp->prenom;
				$tab_ele['classe'][$cpt]['pp']['civilite']=$lig_pp->civilite;
			}

			$cpt++;
		}
	}
	*/

	// Classes
	$tab_ele['classe']=array();
	$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$ele_login' AND c.id=jec.id_classe ORDER BY jec.periode;";
	$res_clas=mysql_query($sql);
	if(mysql_num_rows($res_clas)>0) {
		$tab_ele['liste_classes']="";

		$cpt=0;
		while($lig_clas=mysql_fetch_object($res_clas)) {
			if($cpt>0) {$tab_ele['liste_classes'].=", ";}
			$tab_ele['liste_classes']=$lig_clas->classe;

			$tab_ele['classe'][$cpt]=array();
			$tab_ele['classe'][$cpt]['id_classe']=$lig_clas->id;
			$tab_ele['classe'][$cpt]['classe']=$lig_clas->classe;
			$tab_ele['classe'][$cpt]['nom_complet']=$lig_clas->nom_complet;


			// On devrait mettre $tab_ele['classe'][$cpt]['rn_nomdev'], mais j'ai mis $tab_ele['rn_nomdev']
			// C'est imparfait si l'élève n'est pas dans la même classe sur toutes les périodes , mais cela permet de garder le même code pour visu_releve_notes_func.lib.php et la présente page
			//$tab_ele['rn_app']=$lig_clas->; // Ca ne correspond pas à un champ dans la table 'classes'
			$tab_ele['rn_nomdev']=$lig_clas->rn_nomdev;
			$tab_ele['rn_toutcoefdev']=$lig_clas->rn_toutcoefdev;
			$tab_ele['rn_coefdev_si_diff']=$lig_clas->rn_coefdev_si_diff;
			$tab_ele['rn_datedev']=$lig_clas->rn_datedev;
			$tab_ele['rn_sign_chefetab']=$lig_clas->rn_sign_chefetab;
			$tab_ele['rn_sign_pp']=$lig_clas->rn_sign_pp;
			$tab_ele['rn_sign_resp']=$lig_clas->rn_sign_resp;
			$tab_ele['rn_formule']=$lig_clas->rn_formule;

			$tab_ele['rn_sign_nblig']=$lig_clas->rn_sign_nblig;
			if($tab_ele['rn_sign_nblig']==0) {$tab_ele['rn_sign_nblig']=3;}

			//echo "\$tab_ele['rn_sign_resp']=$lig_clas->rn_sign_resp<br/>";

			// Liste des périodes dans la classe
			$sql="SELECT p.* FROM periodes p, j_eleves_classes jec WHERE jec.login='$ele_login' AND p.num_periode=jec.periode AND jec.id_classe='".$lig_clas->id."' ORDER BY p.num_periode;";
			$res_per=mysql_query($sql);
			$cpt2=0;
			if(mysql_num_rows($res_per)>0) {
				$tab_ele['classe'][$cpt]['periodes'][$cpt2]=array();
				while($lig_per=mysql_fetch_object($res_per)) {
					$tab_ele['classe'][$cpt]['periodes'][$cpt2]['num_periode']=$lig_per->num_periode;
					$tab_ele['classe'][$cpt]['periodes'][$cpt2]['nom_periode']=$lig_per->nom_periode;

					// On pourrait extraire les relevés de notes et bulletins à ce niveau

					$cpt2++;
				}
			}

			// Récup infos Prof Principal (prof_suivi)
			$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$ele_login."' AND id_classe='".$lig_clas->id."' AND jep.professeur=u.login;";
			$res_pp=mysql_query($sql);
			//echo "$sql<br />";
			if(mysql_num_rows($res_pp)>0) {
				$lig_pp=mysql_fetch_object($res_pp);
				$tab_ele['classe'][$cpt]['pp']=array();

				$tab_ele['classe'][$cpt]['pp']['prof_login']=$lig_pp->login;
				$tab_ele['classe'][$cpt]['pp']['nom']=$lig_pp->nom;
				$tab_ele['classe'][$cpt]['pp']['prenom']=$lig_pp->prenom;
				$tab_ele['classe'][$cpt]['pp']['civilite']=$lig_pp->civilite;
				$tab_ele['classe'][$cpt]['pp']['email']=$lig_pp->email;

				$tab_ele['classe'][$cpt]['pp']['civ_nom_prenom']=$lig_pp->civilite." ".$lig_pp->nom." ".substr($lig_pp->prenom,0,1).".";

			}

			$cpt++;
		}
	}

	// Périodes
	//$sql="SELECT DISTINCT p.*, jec.id_classe, c.classe, c.nom_complet FROM periodes p, j_eleves_classes jec, classes c WHERE jec.login='$ele_login' AND p.num_periode=jec.periode AND c.id=jec.id_classe ORDER BY p.num_periode;";
	$sql="SELECT DISTINCT p.*,jec.id_classe, c.classe, c.nom_complet  FROM periodes p, j_eleves_classes jec, classes c WHERE jec.login='$ele_login' AND p.num_periode=jec.periode AND jec.id_classe=p.id_classe AND c.id=jec.id_classe ORDER BY p.num_periode;";
	//echo "$sql<br />";
	$res_per=mysql_query($sql);
	$cpt=0;
	if(mysql_num_rows($res_per)>0) {
		if(($acces_releves=='y')||($acces_enseignements=='y')||($acces_bulletins=='y')) {
			while($lig_per=mysql_fetch_object($res_per)) {
				$tab_ele['periodes'][$cpt]=array();
				$tab_ele['periodes'][$cpt]['num_periode']=$lig_per->num_periode;
				$tab_ele['periodes'][$cpt]['nom_periode']=$lig_per->nom_periode;
				$tab_ele['periodes'][$cpt]['id_classe']=$lig_per->id_classe;
				$tab_ele['periodes'][$cpt]['classe']=$lig_per->classe;
				$tab_ele['periodes'][$cpt]['nom_complet']=$lig_per->nom_complet;

				//echo "\$tab_ele['periodes'][$cpt]['num_periode']=".$tab_ele['periodes'][$cpt]['num_periode']."<br />";
				//echo "\$tab_ele['periodes'][$cpt]['id_classe']=".$tab_ele['periodes'][$cpt]['id_classe']."<br />";

				// On regarde si on affiche les catégories de matières dans la classe courante de l'élève
				$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$lig_per->id_classe."'");
				if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}
				$tab_ele['periodes'][$cpt]['affiche_categories']=$affiche_categories;

				if($affiche_categories) {
					$sql="SELECT DISTINCT g.*,m.nom_complet ".
					"FROM j_eleves_groupes jeg,
							j_groupes_classes jgc,
							j_groupes_matieres jgm,
							j_matieres_categories_classes jmcc,
							matieres m,
							groupes g " .
					"WHERE ( " .
					"jeg.login = '" . $ele_login ."' AND " .
					"jgc.id_groupe = jeg.id_groupe AND " .
					"jgc.categorie_id = jmcc.categorie_id AND " .
					"jgc.id_classe = '".$lig_per->id_classe."' AND " .
					"jgm.id_groupe = jgc.id_groupe AND " .
					"m.matiere = jgm.id_matiere AND " .
					"g.id=jeg.id_groupe".
					") " .
					"ORDER BY jmcc.priority,jgc.priorite,m.nom_complet";
				} else {
					$sql="SELECT DISTINCT g.*,m.nom_complet " .
					"FROM j_groupes_classes jgc, j_groupes_matieres jgm, j_eleves_groupes jeg, matieres m, groupes g " .
					"WHERE ( " .
					"jeg.login = '" . $ele_login . "' AND " .
					"jgc.id_groupe = jeg.id_groupe AND " .
					"jgc.id_classe = '".$lig_per->id_classe."' AND " .
					"jgm.id_groupe = jgc.id_groupe AND
					m.matiere=jgm.id_matiere AND jgm.id_groupe=g.id " .
					") " .
					"ORDER BY jgc.priorite,jgm.id_matiere";
				}
				//$sql="SELECT DISTINCT g.*,m.nom_complet FROM groupes g, j_groupes_matieres jgm, matieres m, j_groupes_classes jgc, j_eleves_groupes jeg WHERE g.id=jgm.id_groupe AND m.matiere=jgm.id_matiere AND jgc.id_groupe=jgm.id_groupe AND jeg.id_groupe=g.id AND jeg.periode='".$lig_per->num_periode."' AND jeg.login='$ele_login' ORDER BY jgc.priorite,m.nom_complet;";
				//echo "$sql<br />";
				$res_grp=mysql_query($sql);
				if(mysql_num_rows($res_grp)>0) {
					$cpt2=0;
					while($lig_grp=mysql_fetch_object($res_grp)) {
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]=array();
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['id_groupe']=$lig_grp->id;
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['name']=$lig_grp->name;
						//echo "\$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['name']=".$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['name']."<br />";
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['description']=$lig_grp->description;
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['matiere_nom_complet']=$lig_grp->nom_complet;

						if($affiche_categories) {
							//$sql="SELECT DISTINCT jgc.categorie_id FROM j_groupes_classes jgc WHERE jgc.id_groupe='".$lig_grp->id."' AND id_classe='".$tab_ele['periodes'][$cpt]['id_classe']."';";
							$sql="SELECT DISTINCT jgc.categorie_id, mc.nom_court, mc.nom_complet FROM j_groupes_classes jgc, matieres_categories mc WHERE jgc.id_groupe='".$lig_grp->id."' AND id_classe='".$tab_ele['periodes'][$cpt]['id_classe']."' AND mc.id=jgc.categorie_id;";
							//echo "$sql<br />";
							$res_cat=mysql_query($sql);
							if(mysql_num_rows($res_cat)>0) {
								$lig_cat=mysql_fetch_object($res_cat);
								$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['id_cat']=$lig_cat->categorie_id;
								$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['cat_nom_court']=$lig_cat->nom_court;
								$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['cat_nom_complet']=$lig_cat->nom_complet;
								//echo "\$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['id_cat']=".$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['id_cat']."<br />";
							}
						}

						$sql="SELECT DISTINCT d.coef FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
						nd.login = '".$ele_login."' and
						nd.id_devoir = d.id and
						d.display_parents='1' and
						d.id_racine = cn.id_cahier_notes and
						cn.id_groupe = '".$lig_grp->id."' and
						cn.periode = '".$lig_per->num_periode."'
						)";
						$res_differents_coef=mysql_query($sql);
						if(mysql_num_rows($res_differents_coef)>1){
							$differents_coef="y";
						}
						else{
							$differents_coef="n";
						}
						$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['differents_coef']=$differents_coef;


						$sql1 = "SELECT d.coef, nd.note, nd.comment, d.nom_court, nd.statut, d.date, d.note_sur, d.display_parents_app FROM cn_notes_devoirs nd, cn_devoirs d, cn_cahier_notes cn WHERE (
						nd.login = '".$ele_login."' and
						nd.id_devoir = d.id and
						d.display_parents='1' and
						d.id_racine = cn.id_cahier_notes and
						cn.id_groupe = '".$lig_grp->id."' and
						cn.periode = '".$lig_per->num_periode."'
						)
						ORDER BY d.date
						";
						$query_notes = mysql_query($sql1);

						$count_notes = mysql_num_rows($query_notes);
						$m = 0;
						while ($m < $count_notes) {
							$eleve_display_app = @mysql_result($query_notes,$m,'d.display_parents_app');
							$eleve_app = @mysql_result($query_notes,$m,'nd.comment');
							$eleve_note = @mysql_result($query_notes,$m,'nd.note');
							$eleve_statut = @mysql_result($query_notes,$m,'nd.statut');
							$eleve_nom_court = @mysql_result($query_notes,$m,'d.nom_court');
							$date_note = @mysql_result($query_notes,$m,'d.date');
							$coef_devoir = @mysql_result($query_notes,$m,'d.coef');
							$note_sur_devoir = @mysql_result($query_notes,$m,'d.note_sur');

							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['display_app']=$eleve_display_app;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['app']=$eleve_app;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['note']=$eleve_note;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['statut']=$eleve_statut;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['nom_court']=$eleve_nom_court;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['date']=$date_note;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['coef']=$coef_devoir;
							$tab_ele['periodes'][$cpt]['groupes'][$cpt2]['devoir'][$m]['note_sur']=$note_sur_devoir;
							// On ne récupère pas le nom long du devoir?

							$m++;
						}

						$cpt2++;
					}
				}
				$cpt++;
			}
		}

		$sql="SELECT DISTINCT g.*,m.nom_complet FROM groupes g, j_groupes_matieres jgm, matieres m, j_groupes_classes jgc, j_eleves_groupes jeg WHERE g.id=jgm.id_groupe AND m.matiere=jgm.id_matiere AND jgc.id_groupe=jgm.id_groupe AND jeg.id_groupe=g.id AND jeg.login='$ele_login' ORDER BY jgc.priorite,m.nom_complet;";
		//echo "$sql<br />";
		$res_grp=mysql_query($sql);
		if(mysql_num_rows($res_grp)>0) {
			$cpt=0;
			while($lig_grp=mysql_fetch_object($res_grp)) {
				$tab_ele['groupes'][$cpt]=array();
				$tab_ele['groupes'][$cpt]['id_groupe']=$lig_grp->id;
				$tab_ele['groupes'][$cpt]['name']=$lig_grp->name;
				$tab_ele['groupes'][$cpt]['description']=$lig_grp->description;
				$tab_ele['groupes'][$cpt]['matiere_nom_complet']=$lig_grp->nom_complet;

				$tab_ele['index_grp'][$lig_grp->id]=$cpt;

				$sql="SELECT periode FROM j_eleves_groupes WHERE login='$ele_login' AND id_groupe='".$lig_grp->id."' ORDER BY periode;";
				$res_per2=mysql_query($sql);
				if(mysql_num_rows($res_per2)>0) {
					$tab_ele['groupes'][$cpt]['periodes']=array();
					while($lig_per2=mysql_fetch_object($res_per2)) {
						$tab_ele['groupes'][$cpt]['periodes'][]=$lig_per2->periode;
					}
				}

				$sql="SELECT u.* FROM utilisateurs u, j_groupes_professeurs jgp WHERE u.login=jgp.login AND id_groupe='".$lig_grp->id."' ORDER BY u.nom, u.prenom;";
				$res_prof=mysql_query($sql);
				if(mysql_num_rows($res_prof)>0) {
					$tab_ele['groupes'][$cpt]['prof']=array();
					$tab_ele['groupes'][$cpt]['prof_liste']="";
					//$tab_ele['groupes'][$cpt]['prof_liste_email']="";
					$cpt2=0;
					while($lig_prof=mysql_fetch_object($res_prof)) {
						if($cpt2>0) {$tab_ele['groupes'][$cpt]['prof_liste'].=", ";}

						$tab_ele['groupes'][$cpt]['prof'][$cpt2]['prof_login']=$lig_prof->login;
						$tab_ele['groupes'][$cpt]['prof'][$cpt2]['nom']=$lig_prof->nom;
						$tab_ele['groupes'][$cpt]['prof'][$cpt2]['prenom']=$lig_prof->prenom;
						$tab_ele['groupes'][$cpt]['prof'][$cpt2]['civilite']=$lig_prof->civilite;
						$tab_ele['groupes'][$cpt]['prof'][$cpt2]['email']=$lig_prof->email;

						//if($lig_prof->email!='') {
						//	if($tab_ele['groupes'][$cpt]['prof'][$cpt2]['prof_liste_email']!='') {$tab_ele['groupes'][$cpt]['prof_liste_email'].=", ";}
						//	$tab_ele['groupes'][$cpt]['prof_liste_email'].=$lig_prof->email;
						//}

						if(($lig_prof->email!='')&&(!in_array($lig_prof->email,$tab_ele['tab_prof_liste_email']))) {$tab_ele['tab_prof_liste_email'][]=$lig_prof->email;}

						$tab_ele['groupes'][$cpt]['prof_liste'].=$lig_prof->civilite." ".$lig_prof->nom." ".substr($lig_prof->prenom,0,1).".";

						$cpt2++;
					}
				}

				$cpt++;
			}
		}

		// Je ne suis pas sûr de la façon la plus pertinente de gérer les groupes/périodes... et il y aurait aussi les classes:
		// $tab_ele['groupes'][$cpt]['periodes'][]
		// ou $tab_ele['periodes'][$cpt]['groupes'][]
		// ou $tab_ele['classes'][$cpt]['groupes'][$cpt2]['periodes'][]

	}

	for($i=0;$i<count($tab_ele['tab_prof_liste_email']);$i++) {
		if($tab_ele['prof_liste_email']!="") {$tab_ele['prof_liste_email'].=", ";}
		$tab_ele['prof_liste_email'].=$tab_ele['tab_prof_liste_email'][$i];
	}

	//===================================
	$get_cat = mysql_query("SELECT id FROM matieres_categories");
	$categories = array();
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$categories[] = $row["id"];
	}

	$cat_names = array();
	foreach ($categories as $cat_id) {
		$sql="SELECT nom_complet FROM matieres_categories WHERE id='".$cat_id."';";
		$res_cat=mysql_query($sql);
		if ($res_cat) {
			$cat_names[$cat_id]=mysql_result($res_cat, 0);
		}
	}

	foreach($cat_names as $key => $value) {
		$tab_ele['categorie'][$key]=$value;
	}
	//===================================

	// Régime et redoublement
	$sql="SELECT * FROM j_eleves_regime WHERE login='".$ele_login."';";
	$res_ele_reg=mysql_query($sql);
	if(mysql_num_rows($res_ele_reg)>0) {
		$lig_ele_reg=mysql_fetch_object($res_ele_reg);

		$tab_ele['regime']=$lig_ele_reg->regime;
		$tab_ele['doublant']=$lig_ele_reg->doublant;
	}

	//$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$ele_login."' AND e.id = j.id_etablissement);";
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
	$sql="SELECT u.* FROM j_eleves_cpe jec, utilisateurs u WHERE e_login='".$ele_login."' AND jec.cpe_login=u.login;";
	$res_cpe=mysql_query($sql);
	if(mysql_num_rows($res_cpe)>0) {
		$lig_cpe=mysql_fetch_object($res_cpe);
		$tab_ele['cpe']=array();

		$tab_ele['cpe']['login']=$lig_cpe->login;
		$tab_ele['cpe']['nom']=$lig_cpe->nom;
		$tab_ele['cpe']['prenom']=$lig_cpe->prenom;
		$tab_ele['cpe']['civilite']=$lig_cpe->civilite;
		$tab_ele['cpe']['email']=$lig_cpe->email;

		$tab_ele['cpe']['civ_nom_prenom']=$lig_cpe->civilite." ".$lig_cpe->nom." ".substr($lig_cpe->prenom,0,1).".";
	}

	$tab_ele['equipe_liste_email']=$tab_ele['prof_liste_email'];
	if((isset($tab_ele['cpe']['email']))&&($tab_ele['cpe']['email']!="")&&(!in_array($tab_ele['cpe']['email'],$tab_ele['tab_prof_liste_email']))) { 
		if($tab_ele['equipe_liste_email']!="") {$tab_ele['equipe_liste_email'].=", ";}
		$tab_ele['equipe_liste_email'].=$tab_ele['cpe']['email'];
	}

	/*
	// Récup infos Prof Principal (prof_suivi)
	$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$ele_login."' AND id_classe='$id_classe' AND jep.professeur=u.login;";
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
	*/

	if($acces_responsables=='y') {
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
				$tab_ele['resp'][$cpt]['mel']=$lig_resp->mel;

				$tab_ele['resp'][$cpt]['adr1']=$lig_resp->adr1;
				$tab_ele['resp'][$cpt]['adr2']=$lig_resp->adr2;
				$tab_ele['resp'][$cpt]['adr3']=$lig_resp->adr3;
				$tab_ele['resp'][$cpt]['adr4']=$lig_resp->adr4;
				$tab_ele['resp'][$cpt]['cp']=$lig_resp->cp;
				$tab_ele['resp'][$cpt]['pays']=$lig_resp->pays;
				$tab_ele['resp'][$cpt]['commune']=$lig_resp->commune;

				$tab_ele['resp'][$cpt]['adr_id']=$lig_resp->adr_id;

				$tab_ele['resp'][$cpt]['resp_legal']=$lig_resp->resp_legal;

				//echo "\$lig_resp->login=".$lig_resp->login."<br />";
				if($lig_resp->login!="") {
					$sql="SELECT etat FROM utilisateurs WHERE login='".$lig_resp->login."';";
					//echo "$sql<br />";
					$res_u=mysql_query($sql);
					if(mysql_num_rows($res_u)>0) {
						$lig_u=mysql_fetch_object($res_u);
						$tab_ele['resp'][$cpt]['etat']=$lig_u->etat;
					}
				}

				$cpt++;
			}
		}
	}

	if(($active_cahiers_texte=="y")&&($acces_cdt=='y')) {
		$cpt1=0; // pour initialiser la variable
		$tab_date_ct=array();
		// Un DISTINCT pour éviter les trois exemplaires dûs à j_eleves_groupes
		$sql="SELECT DISTINCT cte.* FROM  ct_entry cte, j_eleves_groupes jeg WHERE cte.id_groupe=jeg.id_groupe AND jeg.login='".$ele_login."' AND cte.date_ct>=$date_ct1 AND cte.date_ct<=$date_ct2 ORDER BY cte.date_ct, cte.id_groupe;";
		//echo "$sql<br />";
		$res_ct=mysql_query($sql);
		if(mysql_num_rows($res_ct)>0) {
			$cpt1=0;
			while($lig_ct=mysql_fetch_object($res_ct)) {
				$tab_ele['cdt_entry'][$cpt1]=array();
				$tab_ele['cdt_entry'][$cpt1]['id_ct']=$lig_ct->id_ct;
				$tab_ele['cdt_entry'][$cpt1]['heure_entry']=$lig_ct->heure_entry;
				$tab_ele['cdt_entry'][$cpt1]['id_groupe']=$lig_ct->id_groupe;
				$tab_ele['cdt_entry'][$cpt1]['date_ct']=$lig_ct->date_ct;
				$tab_ele['cdt_entry'][$cpt1]['id_login']=$lig_ct->id_login;
				$tab_ele['cdt_entry'][$cpt1]['contenu']=$lig_ct->contenu;
				/*
				echo "<p>\n";
				foreach($tab_ele['cdt_entry'][$cpt] as $key => $value) {
					echo "\$tab_ele['cdt_entry'][$cpt]['$key']=$value<br />\n";
				}
				echo "</p>\n";
				*/
				$tab_date_ct[]=$lig_ct->date_ct;
				$cpt1++;
			}
		}

		$sql="SELECT DISTINCT ctde.* FROM ct_devoirs_entry ctde, j_eleves_groupes jeg WHERE ctde.id_groupe=jeg.id_groupe AND jeg.login='".$ele_login."' AND ctde.date_ct>=$date_ct1 AND ctde.date_ct<=$date_ct2 ORDER BY ctde.date_ct, ctde.id_groupe;";
		//echo "$sql<br />";
		$res_ct=mysql_query($sql);
		$cpt2=0;
		if(mysql_num_rows($res_ct)>0) {
			//$cpt2=0;
			while($lig_ct=mysql_fetch_object($res_ct)) {
				$tab_ele['cdt_dev'][$cpt2]=array();
				$tab_ele['cdt_dev'][$cpt2]['id_ct']=$lig_ct->id_ct;
				$tab_ele['cdt_dev'][$cpt2]['id_groupe']=$lig_ct->id_groupe;
				$tab_ele['cdt_dev'][$cpt2]['date_ct']=$lig_ct->date_ct;
				$tab_ele['cdt_dev'][$cpt2]['id_login']=$lig_ct->id_login;
				$tab_ele['cdt_dev'][$cpt2]['contenu']=$lig_ct->contenu;
				$tab_date_ct[]=$lig_ct->date_ct;
				$cpt2++;
			}
		}

		sort($tab_date_ct);
		$tmp_tab_date_ct=$tab_date_ct;
		unset($tab_date_ct);
		$tab_date_ct=array_unique($tmp_tab_date_ct);
		//array_unique($tab_date_ct);

		$cpt1_2=$cpt1+$cpt2;
		$cpt=0;
		//for($i=0;$i<count($tab_date_ct);$i++) {
		//for($i=0;$i<max($cpt1,$cpt2);$i++) {
		for($i=0;$i<$cpt1_2;$i++) {
			//echo "\$tab_date_ct[$i]=".$tab_date_ct[$i]."<br />";
			//if($tab_date_ct[$i]!="") {
			if((isset($tab_date_ct[$i]))&&($tab_date_ct[$i]!="")) {
				$tab_ele['cdt'][$cpt]['date_ct']=$tab_date_ct[$i];
				$nbre_cdt_dev = isset($tab_ele['cdt_dev']) ? count($tab_ele['cdt_dev']) : 0;
				for($j=0;$j<$nbre_cdt_dev;$j++) {
					if($tab_ele['cdt_dev'][$j]['date_ct']==$tab_date_ct[$i]) {
						$tab_ele['cdt'][$cpt]['dev'][]=$tab_ele['cdt_dev'][$j];
					}
					elseif($tab_ele['cdt_dev'][$j]['date_ct']>$tab_date_ct[$i]) {
						break;
					}
				}
				if(isset($tab_ele['cdt_entry'])) {
					for($j=0;$j<count($tab_ele['cdt_entry']);$j++) {
						if($tab_ele['cdt_entry'][$j]['date_ct']==$tab_date_ct[$i]) {
							$tab_ele['cdt'][$cpt]['entry'][]=$tab_ele['cdt_entry'][$j];
						}
						elseif($tab_ele['cdt_entry'][$j]['date_ct']>$tab_date_ct[$i]) {
							break;
						}
					}
				}
				$cpt++;
			}
		}
	}

	$tab_ele['absences']=array();
	$sql="SELECT * FROM absences WHERE login='$ele_login' ORDER BY periode;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$tab_ele['absences'][$cpt]['periode']=$lig->periode;
			$tab_ele['absences'][$cpt]['nb_absences']=$lig->nb_absences;
			$tab_ele['absences'][$cpt]['non_justifie']=$lig->non_justifie;
			$tab_ele['absences'][$cpt]['nb_retards']=$lig->nb_retards;
			$tab_ele['absences'][$cpt]['appreciation']=$lig->appreciation;
			$cpt++;
		}
	}

	// ============================================================================= //
	// === SUIVI des absences pour ceux qui utilisent la saisie 'fond de classe' === //
	// ============================================================================= //
	$tab_ele['abs_quotidien'] = array();
	$tab_ele['abs_quotidien']['autorisation'] = 'non'; // ne sera changé que dans le cas où la requête suivante renvoie un résultat
	$ts_quinze_jours_avant = date("U") - 1296000;

	$sql2 = "SELECT DISTINCT * FROM absences_rb WHERE eleve_id = '".$ele_login."' AND date_saisie > '".$ts_quinze_jours_avant."'";
	$query = mysql_query($sql2);

	if ($query) {
		$test = mysql_num_rows($query);
		if ($test >= 1) {
			$tab_ele['abs_quotidien']['autorisation'] = 'oui';
		}
		// On enregistre toutes les absences de l'élève dans le tableau
		$s = 0;
		while($rep = mysql_fetch_object($query)){

			$jour = date("d/m", $rep->debut_ts);
			$creneau = mysql_fetch_array(mysql_query("SELECT nom_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$rep->creneau_id."' LIMIT 1"));

			$tab_ele['abs_quotidien'][$s]['retard_absence'] = $rep->retard_absence;
			$tab_ele['abs_quotidien'][$s]['jour_semaine'] = $rep->jour_semaine . ' ' . $jour;
			$tab_ele['abs_quotidien'][$s]['debut_heure'] = date("H:i", $rep->debut_ts);
			$tab_ele['abs_quotidien'][$s]['creneau'] = $creneau["nom_definie_periode"];
			$s++;
		}

	}
	else {
		// rien et on laisse
	}

	//$acces_mod_discipline="n";
	//if($acces_mod_discipline=='y') {
	//if($acces_discipline=='y') {
	$gepiVersion=getSettingValue('version');
	$tmp_tab_gepiVersion=explode(".",$gepiVersion);
	if(($gepiVersion=='trunk')||
		($tmp_tab_gepiVersion[0]>1)||
		(($tmp_tab_gepiVersion[0]==1)&&($tmp_tab_gepiVersion[1]>5))||
		(($tmp_tab_gepiVersion[0]==1)&&($tmp_tab_gepiVersion[1]==5)&&($tmp_tab_gepiVersion[2]>2))||
		(getSettingValue('discipline_experimental')=='y')) {
		// Affecter auparavant la valeur de $acces_mod_discipline sur deux tests:
		// - Module actif
		// - Accès au module discipline précisé dans Gestion générale/Droits d'accès

		require_once("../mod_discipline/sanctions_func_lib.php");

		// tab_mod_discipline($ele_login,$mode,$date_debut,$date_fin);
		// $mode=all ou bien qualité: responsable, victime, témoin,...

		$tab_ele['tab_mod_discipline']=tab_mod_discipline($ele_login,"all",$date_debut_disc,$date_fin_disc);
	}

	return $tab_ele;
}

function fich_debug($texte) {
	$fich=fopen("/tmp/visu_eleve.txt","a+");
	fwrite($fich,$texte);
	fclose($fich);
}

function redimensionne_image_releve($photo){
	//global $bull_photo_largeur_max, $bull_photo_hauteur_max;
	//global $releve_photo_largeur_max, $releve_photo_hauteur_max;
	global $photo_largeur_max, $photo_hauteur_max;

	// prendre les informations sur l'image
	$info_image=getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur=$info_image[0];
	$hauteur=$info_image[1];

	// calcule le ratio de redimensionnement
	//$ratio_l=$largeur/$releve_photo_largeur_max;
	//$ratio_h=$hauteur/$releve_photo_hauteur_max;
	//$ratio_l=$largeur/$bull_photo_largeur_max;
	//$ratio_h=$hauteur/$bull_photo_hauteur_max;
	$ratio_l=$largeur/$photo_largeur_max;
	$ratio_h=$hauteur/$photo_hauteur_max;
	$ratio=($ratio_l>$ratio_h)?$ratio_l:$ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur=round($largeur/$ratio);
	$nouvelle_hauteur=round($hauteur/$ratio);

	//fich_debug("photo=$photo\nlargeur=$largeur\nhauteur=$hauteur\nratio_l=$ratio_l\nratio_h=$ratio_h\nratio=$ratio\nnouvelle_largeur=$nouvelle_largeur\nnouvelle_hauteur=$nouvelle_hauteur\n===============\n");

	return array($nouvelle_largeur, $nouvelle_hauteur);
}

//echo "\$releve_photo_largeur_max=$releve_photo_largeur_max<br />";
//echo "\$releve_photo_hauteur_max=$releve_photo_hauteur_max<br />";



//======================
//======================
//======================
// RELEVé HTML A ADAPTER
//======================
//======================
//======================

//function releve_html($tab_rel,$i,$num_releve_specifie) {
function releve_html($tab_rel,$id_classe,$num_periode,$index_per) {
	global
		//============================================
		// Paramètres généraux:
		// En admin, dans Gestion générale/Configuration générale
		$gepi_prof_suivi,

		$RneEtablissement,
		$gepiSchoolName,
		$gepiSchoolAdress1,
		$gepiSchoolAdress2,
		$gepiSchoolZipCode,
		$gepiSchoolCity,
		$gepiSchoolPays,
		$gepiSchoolTel,
		$gepiSchoolFax,
		$gepiYear,

		//$logo_etab,
		//============================================
		//$choix_periode,	// 'periode' ou 'intervalle'
		//$chaine_coef,	// 'coef.:'
		//============================================

		// Paramètres d'impression des bulletins HTML:

		// Mise en page du bulletin scolaire
		$releve_body_marginleft,
		// $titlesize, $textsize, $p_bulletin_margin sont récupérés plus haut dans l'entête pour écrire les styles
		$releve_largeurtableau,

		$releve_col_matiere_largeur,
		//$col_note_largeur,
		//$col_boite_largeur,
		//$col_hauteur,		// La hauteur minimale de ligne n'est exploitée que dans les boites/conteneurs
		$releve_cellpadding,
		$releve_cellspacing,
		$releve_ecart_entete,
		//$bull_espace_avis,
		// $bull_bordure_classique permet de renseigner $class_bordure
		//$class_bordure,
		$releve_class_bordure,

		$releve_categ_font_size,
		$releve_categ_bgcolor,
		//======================
		//$bull_categ_font_size_avis,
		//$bull_police_avis,
		//$bull_font_style_avis,
		// Ils sont utilisés dans l'entête pour générer les styles
		//======================
		$genre_periode,
		$releve_affich_nom_etab,
		$releve_affich_adr_etab,

		// Informations devant figurer sur le bulletin scolaire
		$releve_mention_nom_court,
		$releve_mention_doublant,
		$releve_affiche_eleve_une_ligne,
		//$releve_affiche_appreciations,
		//$releve_affiche_absences,
		//$releve_affiche_avis,
		//$releve_affiche_aid,
		$releve_affiche_numero,		// affichage du numéro du bulletin
		// L'affichage des graphes devrait provenir des Paramètres d'impression des bulletins HTML, mais le paramètre a été stocké dans $tab_rel
		//$releve_affiche_signature,	// affichage du nom du PP et du chef d'établissement
		$releve_affiche_etab,			// Etablissement d'origine


		$activer_photo_releve,
		// $releve_photo_largeur_max et $releve_photo_hauteur_max sont récupérées via global dans redimensionne_image()

		$releve_affiche_tel,
		$releve_affiche_fax,
		$releve_intitule_app,
		$releve_affiche_INE_eleve,
		$releve_affiche_formule,
		$releve_formule_bas,
		// Nom du fichier déterminé d'après le paramètre choix_bulletin
		$fichier_bulletin,
		$min_max_moyclas,

		// Bloc adresse responsable
		$releve_addressblock_padding_right,
		$releve_addressblock_padding_top,
		$releve_addressblock_padding_text,
		$releve_addressblock_length,
		$releve_addressblock_font_size,
		//addressblock_logo_etab_prop correspond au pourcentage $largeur1 et $largeur2 est le complément à 100%
		$releve_addressblock_logo_etab_prop,
		$releve_addressblock_autre_prop,
		// Pourcentage calculé par rapport au tableau contenant le bloc Classe, Année,...
		$releve_addressblock_classe_annee2,
		// Nombre de sauts de ligne entre le bloc Logo+Etablissement et le bloc Nom, prénom,... de l'élève
		$releve_ecart_bloc_nom,
		$releve_addressblock_debug,

		//============================================
		// Paramètre transmis depuis la page d'impression des bulletins
		$un_seul_bull_par_famille,

		//============================================
		// Tableaux provenant de /lib/global.inc
		$type_etablissement,
		$type_etablissement2,

		//============================================
		// Paramètre du module trombinoscope
		// En admin, dans Gestion des modules
		$active_module_trombinoscopes
;

	// Récupérer avant le nombre de bulletins à imprimer
	// - que le premier resp
	// - tous les resp si adr différentes
	// et le passer via global
	//================================


	//echo "\$choix_periode=$choix_periode<br />";
	$choix_periode="periode";
	$chaine_coef="coef.:";

	// Pour n'imprimer qu'un relevé dans le cas où on n'imprime pas les adresses des responsables
	$nb_releves=1;

	/*
	// Tableau contenant le nom de la classe, l'année et la période.
	echo "<table width='".$releve_addressblock_autre_prop."%' ";
	echo "cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."'>\n";
	echo "<tr>\n";
	echo "<td class='releve_empty'>\n";
	echo "&nbsp;\n";
	echo "</td>\n";
	echo "<td style='width:".$releve_addressblock_classe_annee2."%;'>\n";
	echo "<p class='bulletin' align='center'><span class=\"releve_grand\">Classe de ".$tab_rel['periodes'][$index_per]['nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";

	echo "<b>".$tab_rel['periodes'][$index_per]['nom_periode']."</b> : Relevé de notes";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<hr />";
	*/

	/*
	echo "<pre>";
	print_r($tab_rel);
	echo "</pre>";
	*/

	echo "<table width='$releve_largeurtableau' border='0' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."' summary='Relevé de notes'>\n";

	echo "<tr>\n";
	echo "<td style=\"width: 30%;\">\n";
	if ($activer_photo_releve=='y' and $active_module_trombinoscopes=='y') {
		$photo=nom_photo($tab_rel['elenoet']);
		//echo "$photo";
		//if("$photo"!=""){
		if($photo){
			//$photo="../photos/eleves/".$photo;
			if(file_exists($photo)){
				$dimphoto=redimensionne_image_releve($photo);

				echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
			}
		}
	}

	//affichage des données sur une seule ligne ou plusieurs
	if  ($releve_affiche_eleve_une_ligne == 'no') { // sur plusieurs lignes
		echo "<p class='bulletin'>\n";
		echo "<b><span class=\"releve_grand\">".$tab_rel['nom']." ".$tab_rel['prenom']."</span></b><br />";
		echo "Né";
		if (strtoupper($tab_rel['sexe'])== "F") {echo "e";}
		echo "&nbsp;le&nbsp;".$tab_rel['naissance'];
		//Eric Ajout
		echo "<br />";
		if ($tab_rel['regime'] == "d/p") {echo "Demi-pensionnaire";}
		if ($tab_rel['regime'] == "ext.") {echo "Externe";}
		if ($tab_rel['regime'] == "int.") {echo "Interne";}
		if ($tab_rel['regime'] == "i-e"){
			echo "Interne&nbsp;externé";
			if (strtoupper($tab_rel['sexe'])!= "F") {echo "e";}
		}
		//Eric Ajout
		if ($releve_mention_doublant == 'yes'){
			if ($tab_rel['doublant'] == 'R'){
			echo "<br />";
			echo "Redoublant";
			if (strtoupper($tab_rel['sexe'])!= "F") {echo "e";}
			}
		}


		if ($releve_mention_nom_court == 'no') {
			//Eric Ajout et supp
			//echo "<BR />";
			//echo ", $current_classe";
		} else {
			echo "<br />";
			echo $tab_rel['periodes'][$index_per]['classe'];
		}

	} else { //sur une ligne
		echo "<p class='bulletin'>\n";
		echo "<b><span class=\"releve_grand\">".$tab_rel['nom']." ".$tab_rel['prenom']."</span></b><br />";
		echo "Né";
		if (strtoupper($tab_rel['sexe'])== "F") {echo "e";}
		echo "&nbsp;le&nbsp;".$tab_rel['naissance'];

		if ($tab_rel['regime'] == "d/p") {echo ", Demi-pensionnaire";}
		if ($tab_rel['regime'] == "ext.") {echo ", Externe";}
		if ($tab_rel['regime'] == "int.") {echo ", Interne";}
		if ($tab_rel['regime'] == "i-e"){
			echo ", Interne&nbsp;externé";
			if (strtoupper($tab_rel['sexe'])!= "F") {echo "e";}
		}
		//Eric Ajout
		if ($releve_mention_doublant == 'yes'){
			if ($tab_rel['doublant'] == 'R'){
			echo ", Redoublant";
			if (strtoupper($tab_rel['sexe'])!= "F") {echo "e";}
			}
		}
		if ($releve_mention_nom_court == 'yes') {
			echo ", ".$tab_rel['periodes'][$index_per]['classe'];
		}
	}

	if($releve_affiche_INE_eleve=="y"){
		echo "<br />\n";
		echo "Numéro INE: ".$tab_rel['no_gep'];
	}

	if($releve_affiche_etab=="y"){
		if ((isset($tab_rel['etab_nom']))&&($tab_rel['etab_nom']!='')) {
			echo "<br />\n";
			if ($tab_rel['etab_id'] != '990') {
				if ($RneEtablissement != $tab_rel['etab_id']) {
					echo "Etablissement d'origine : ";
					echo $tab_rel['etab_niveau_nom']." ".$tab_rel['etab_type']." ".$tab_rel['etab_nom']." (".$tab_rel['etab_cp']." ".$tab_rel['etab_ville'].")\n";
				}
			} else {
				echo "Etablissement d'origine : ";
				echo "hors de France\n";
			}
		}
	}

	echo "</p></td>\n<td style=\"width: 40%;text-align: center;\">\n";

	echo "<p class='bulletin'><span class=\"releve_grand\">Classe de ".$tab_rel['periodes'][$index_per]['nom_complet']."<br />Année scolaire ".$gepiYear."</span><br />\n";

	echo "<b>".$tab_rel['periodes'][$index_per]['nom_periode']."</b> : Relevé de notes";
	echo "</p>\n";

	/*
	$nom_fic_logo = $logo_etab;
	$nom_fic_logo_c = "../images/".$nom_fic_logo;
	if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
		echo "</td>\n<td style=\"text-align: right;\"><img src=\"".$nom_fic_logo_c."\" border=\"0\" alt=\"Logo\" />";
	} else {
	*/
		echo "</td>\n<td>&nbsp;";
	//}
	echo "</td>\n";
	echo "<td style=\"width: 20%;text-align: center;\">";
	echo "<p class='bulletin'>";
	if($releve_affich_nom_etab=="y"){
		echo "<span class=\"releve_grand\">".$gepiSchoolName."</span>";
	}
	if($releve_affich_adr_etab=="y"){
		//echo "<span class=\"releve_grand\">".$gepiSchoolName."</span>";
		if($releve_affich_nom_etab=="y"){echo "<br />\n";}
		echo $gepiSchoolAdress1."<br />\n";
		echo $gepiSchoolAdress2."<br />\n";
		echo $gepiSchoolZipCode." ".$gepiSchoolCity;

		if($releve_affiche_tel=="y"){echo "<br />\nTel: ".$gepiSchoolTel;}
		if($releve_affiche_fax=="y"){echo "<br />\nFax: ".$gepiSchoolFax;}
	}
	echo "</p>\n";

	echo "</td>\n</tr>\n</table>\n";


	// On rajoute des lignes vides
	$n = 0;
	while ($n < $releve_ecart_entete) {
		echo "<br />";
		$n++;
	}



	//=============================================

	// Tableau des matieres/devoirs/notes/appréciations

	//include ($fichier_bulletin);

	// On initialise le tableau :

	$larg_tab = $releve_largeurtableau;
	$larg_col1 = $releve_col_matiere_largeur;
	$larg_col2 = $larg_tab - $larg_col1;
	echo "<table width=\"$larg_tab\" class='boireaus' border='1' cellspacing='3' cellpadding='3' summary='Matières/notes/appréciations'>\n";
	//echo "<table width=\"$larg_tab\"$releve_class_bordure border='1' cellspacing='3' cellpadding='3'>\n";
	echo "<tr>\n";
	echo "<td width=\"$larg_col1\" class='releve'><b>Matière</b><br /><i>Professeur</i></td>\n";
	echo "<td width=\"$larg_col2\" class='releve'>Notes sur 20</td>\n";
	echo "</tr>\n";

	// Boucle groupes
	$j = 0;
	$prev_cat_id = null;
	$alt=1;
	while ($j < count($tab_rel['periodes'][$index_per]['groupes'])) {

		$index_grp=-1;
		for($loop=0;$loop<count($tab_rel['groupes']);$loop++) {
			//echo "<tr><td>".$tab_rel['groupes'][$loop]['id_groupe']."</td><td>".$tab_rel['periodes'][$index_per]['groupes'][$j]['id_groupe']."</td></tr>";
			if($tab_rel['groupes'][$loop]['id_groupe']==$tab_rel['periodes'][$index_per]['groupes'][$j]['id_groupe']) {
				$index_grp=$loop;
				break;
			}
		}

		if ($tab_rel['periodes'][$index_per]['affiche_categories']) {
			// On regarde si on change de catégorie de matière
			//echo "<tr><td>\$tab_rel['periodes'][$index_per]['groupes'][$index_grp]['name']=".$tab_rel['periodes'][$index_per]['groupes'][$index_grp]['name']."<br />\$tab_rel['periodes'][$index_per]['groupes'][$index_grp]['id_cat']=".$tab_rel['periodes'][$index_per]['groupes'][$index_grp]['id_cat']."</td><td>$prev_cat_id</td></tr>\n";
			//if ($tab_rel['periodes'][$index_per]['groupes'][$index_grp]['id_cat'] != $prev_cat_id) {
			if ($tab_rel['periodes'][$index_per]['groupes'][$j]['id_cat'] != $prev_cat_id) {
				//$prev_cat_id = $tab_rel['periodes'][$index_per]['groupes'][$index_grp]['id_cat'];
				$prev_cat_id = $tab_rel['periodes'][$index_per]['groupes'][$j]['id_cat'];

				echo "<tr>\n";
				echo "<td colspan='2'>\n\n";
				//echo "<p style='padding: 0; margin:0; font-size: 10px;'>".$tab_rel['categorie'][$prev_cat_id]."</p>\n";
				echo "<p style='padding: 0; margin:0; font-size: ".$releve_categ_font_size."px;";
				if($releve_categ_bgcolor!="") {echo "background-color:$releve_categ_bgcolor;";}
				//echo "'>".$tab_rel['categorie'][$prev_cat_id]."</p>\n";
				echo "'>".$tab_rel['periodes'][$index_per]['groupes'][$j]['cat_nom_complet']."</p>\n";


				echo "</td>\n";
				echo "</tr>\n";
			}
		}

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td class='releve'>\n";
		echo "<b>".htmlentities($tab_rel['periodes'][$index_per]['groupes'][$j]['matiere_nom_complet'])."</b>";
		$k = 0;
                $nbre_professeurs = isset($tab_rel['groupes'][$index_grp]['prof']) ? count($tab_rel['groupes'][$index_grp]['prof']) : NULL;
		While ($k < $nbre_professeurs) {
			echo "<br /><i>".affiche_utilisateur(htmlentities($tab_rel['groupes'][$index_grp]['prof'][$k]['prof_login']),$id_classe)."</i>";
			$k++;
		}
		echo "</td>\n";

		echo "<td class='releve' style='text-align:left;'>\n";

		// Boucle sur la liste des devoirs
		if(!isset($tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'])) {
			echo "&nbsp;";
		}
		else {
			$m=0;
			$tiret = "no";
			while($m<count($tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'])) {
				// Note de l'élève sur le devoir:
				$eleve_note=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['note'];
				// Statut de l'élève sur le devoir:
				$eleve_statut=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['statut'];
				// Appréciation de l'élève sur le devoir:
				$eleve_app=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['app'];
				// Le professeur a-t-il autorisé l'accès à l'appréciation lors de la saisie du devoir
				$eleve_display_app=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['display_app'];
				// Nom court du devoir:
				$eleve_nom_court=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['nom_court'];
				// Date du devoir:
				$eleve_date=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['date'];
				// Coef du devoir:
				$eleve_coef=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['coef'];
				//note sur
				$eleve_note_sur=$tab_rel['periodes'][$index_per]['groupes'][$j]['devoir'][$m]['note_sur'];

				//==========================================
				// On teste s'il y aura une "Note" à afficher
				if (($eleve_statut != '') and ($eleve_statut != 'v')) {
					$affiche_note = $eleve_statut;
				}
				elseif ($eleve_statut == 'v') {
					$affiche_note = "";
				}
				elseif ($eleve_note != '') {
					$affiche_note = $eleve_note;
					//if(getSettingValue("note_autre_que_sur_referentiel")=="V" || $snnote['note_sur']!=getSettingValue("referentiel_note")) {
					if((getSettingValue("note_autre_que_sur_referentiel")=="V") || 
						((isset($snnote['note_sur']))&&($snnote['note_sur']!=getSettingValue("referentiel_note")))) {
						$affiche_note .= "/".$eleve_note_sur;
					}
				}
				else {
					$affiche_note = "";
				}
				//==========================================

				// Nom du devoir ou pas
				if(($tab_rel['rn_app']=="y") and ($eleve_display_app=="1")) {
					if ($affiche_note=="") {
						if ($tab_rel['rn_nomdev']!="y") {
							$affiche_note = $eleve_nom_court;
						}
						else {
							$affiche_note = "&nbsp;";
						}
					}
				}

				// Si une "Note" doit être affichée
				if ($affiche_note != '') {
					if ($tiret == "yes") {
						if (($tab_rel['rn_app']=="y") or ($tab_rel['rn_nomdev']=="y")) {
							echo "<br />";
						}
						else {
							echo " - ";
						}
					}
					if($tab_rel['rn_nomdev']=="y"){
						echo "$eleve_nom_court: <b>".$affiche_note."</b>";
					}
					else{
						echo "<b>".$affiche_note."</b>";
					}

					// Coefficient (si on affiche tous les coef...
					// ou si on ne les affiche que s'il y a plusieurs coef différents)
					//if(($tab_rel['rn_toutcoefdev']=="y")||
					//	(($tab_rel['rn_coefdev_si_diff']=="y")&&($tab_rel['groupes'][$j]['differents_coef']=="y"))) {
					if(($tab_rel['rn_toutcoefdev']=="y")||
						(($tab_rel['rn_coefdev_si_diff']=="y")&&($tab_rel['periodes'][$index_per]['groupes'][$j]['differents_coef']=="y"))) {
						echo " (<i><small>".$chaine_coef.$eleve_coef."</small></i>)";
					}

					// Si on a demandé à afficher les appréciations
					// et si le prof a coché l'autorisation d'accès à l'appréciations
					if(($tab_rel['rn_app']=="y") and ($eleve_display_app=="1")) {
						echo " - Appréciation : ";
						if ($eleve_app!="") {
							echo $eleve_app;
						}
						else {
							echo "-";
						}
					}

					if($tab_rel['rn_datedev']=="y"){
						// Format: 2006-09-28 00:00:00
						$tmpdate=explode(" ",$eleve_date);
						$tmpdate=explode("-",$tmpdate[0]);
						echo " (<i><small>$tmpdate[2]/$tmpdate[1]/$tmpdate[0]</small></i>)";
					}
					//====================================================================
					// Après un tour avec affichage dans la boucle:
					$tiret = "yes";
				}

				$m++;
			}
		}
		echo "</td>\n";
		echo "</tr>\n";
		$j++;
	}

	echo "</table>\n";
	//=============================================



	//================================
	if(($tab_rel['rn_sign_chefetab']=='y')||($tab_rel['rn_sign_pp']=='y')||($tab_rel['rn_sign_resp']=='y')){
		$nb_cases=0;
		if($tab_rel['rn_sign_chefetab']=='y'){
			$nb_cases++;
		}
		if($tab_rel['rn_sign_pp']=='y'){
			$nb_cases++;
		}
		if($tab_rel['rn_sign_resp']=='y'){
			$nb_cases++;
		}
		$largeur_case=round($releve_largeurtableau/$nb_cases);

		echo "<table$releve_class_bordure border='1' width='$releve_largeurtableau' summary='Signatures'>\n";
		echo "<tr>\n";

		if($tab_rel['rn_sign_chefetab']=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature du chef d'établissement:</b>";
			for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		if($tab_rel['rn_sign_pp']=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature du ".$gepi_prof_suivi.":</b>";
			for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		if($tab_rel['rn_sign_resp']=='y'){
			echo "<td width='$largeur_case'>\n";
			echo "<b>Signature des responsables:</b>";
			for($m=0;$m<$tab_rel['rn_sign_nblig'];$m++) {
				echo "<br />\n";
			}
			echo "</td>\n";
		}

		echo "</tr>\n";
		echo "</table>\n";
	}

	if($tab_rel['rn_formule']!=""){
		echo "<p>".htmlentities($tab_rel['rn_formule'])."</p>\n";
	}
	//================================


	//================================
	// Affichage de la formule de bas de page
	if (($releve_formule_bas != '') and ($releve_affiche_formule == 'y')) {
		// Pas d'affichage dans le cas d'un bulletin d'une période "examen blanc"
		echo "<table width='$releve_largeurtableau' style='margin-left:5px; margin-right:5px;' border='0' cellspacing='".$releve_cellspacing."' cellpadding='".$releve_cellpadding."' summary='Formule de bas de page'>\n";
		echo "<tr>";
		echo "<td><p align='center' class='bulletin'>".$releve_formule_bas."</p></td>\n";
		echo "</tr></table>";
	}
	//================================

}

?>
