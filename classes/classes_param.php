<?php
/*
*
* Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

$periode_query = mysqli_query($GLOBALS["mysqli"], "select max(num_periode) max from periodes");
$max_periode = old_mysql_result($periode_query, 0, 'max');

if (isset($_POST['is_posted'])) {
	check_token();
	$msg = '';
	$reg_ok = '';
	$nb_reg_ok=0;
	$nb_modif_priorite=0;
	// Première boucle sur le nombre de periodes
	$per = 0;
	while ($per < $max_periode) {
		$per++;
		// On dresse la liste de toutes les classes non virtuelles
		$classes_list = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
		$nb_classe = mysqli_num_rows($classes_list);
		// $nb : nombre de classes ayant un nombre de periodes égal à $per
		$nb=0;
		$nbc = 0;
		while ($nbc < $nb_classe) {
			$modif_classe = 'no';
			$id_classe = old_mysql_result($classes_list,$nbc,'id');
			$query_per = mysqli_query($GLOBALS["mysqli"], "SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
			$nb_periode = mysqli_num_rows($query_per);
			if ($nb_periode == $per) {
				// la classe dont l'identifiant est $id_classe a $per périodes
				$temp = "case_".$id_classe;
				if (isset($_POST[$temp])) {
					$k = '1';
					while ($k < $per+1) {
						$temp2 = "nb_".$per."_".$k;
						if ($_POST[$temp2] != '') {
							$sql="UPDATE periodes SET nom_periode='".$_POST[$temp2]."' WHERE (id_classe='".$id_classe."' and num_periode='".$k."')";
							$register = mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}

						$temp2 = "date_fin_".$per."_".$k;
						if ($_POST[$temp2] != '') {
							$tmp_tab=explode("/", $_POST[$temp2]);
							if((!isset($tmp_tab[2]))||(!checkdate($tmp_tab[1], $tmp_tab[0], $tmp_tab[2]))) {
								$msg.="Erreur sur la modification de date de fin de période : ".$_POST[$temp2]."<br />";
							}
							else {
								$sql="UPDATE periodes SET date_fin='".$tmp_tab[2]."-".$tmp_tab[1]."-".$tmp_tab[0]." 00:00:00'";
								$sql.=" WHERE (id_classe='".$id_classe."' and num_periode='".$k."')";
								$register = mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
							}
						}
						$k++;
					}
					$temp2 ="nb_".$per."_reg_suivi_par";
					if ($_POST[$temp2] != '') {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET suivi_par='".$_POST[$temp2]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_suivi_par".$per." : ".$_POST[$temp2]."</br>";
					}
					$temp2 = "nb_".$per."_reg_formule";
					if ($_POST[$temp2] != '') {
						//$register = mysql_query("UPDATE classes SET formule='".$_POST[$temp2]."' where id='".$id_classe."'");
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET formule='".html_entity_decode($_POST[$temp2])."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_formule".$per." : ".$_POST[$temp2]."</br>";
					}


					$temp2 ="nb_".$per."_reg_suivi_par_alt";
					if ($_POST[$temp2] != '') {
						$register = saveParamClasse($id_classe, 'suivi_par_alt', $_POST[$temp2]);
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					$temp2 ="nb_".$per."_reg_suivi_par_alt_fonction";
					if ($_POST[$temp2] != '') {
						$register = saveParamClasse($id_classe, 'suivi_par_alt_fonction', $_POST[$temp2]);
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if (isset($_POST['nb_'.$per.'_reg_format'])) {
						$tab = explode("_", $_POST['nb_'.$per.'_reg_format']);
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET format_nom='".$tab[2]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - ".$_POST['nb_'.$per.'_reg_format']."</br>";
					}

					if (isset($_POST['nb_'.$per.'_reg_elformat'])) {
						$tab = explode("_", $_POST['nb_'.$per.'_reg_elformat']);
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET format_nom_eleve='".$tab[2]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - ".$_POST['nb_'.$per.'_reg_elformat']."</br>";
					}

					if (isset($_POST['display_rang_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_rang='".$_POST['display_rang_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					//====================================
					// AJOUT: boireaus
					if (isset($_POST['display_address_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_address='".$_POST['display_address_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					if (isset($_POST['display_coef_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_coef='".$_POST['display_coef_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					if (isset($_POST['display_nbdev_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_nbdev='".$_POST['display_nbdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if (isset($_POST['display_moy_gen_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_moy_gen='".$_POST['display_moy_gen_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if (isset($_POST['display_mat_cat_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET display_mat_cat='".$_POST['display_mat_cat_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if ((isset($_POST['modele_bulletin_'.$per])) AND ($_POST['modele_bulletin_'.$per]!=0)) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET modele_bulletin_pdf='".$_POST['modele_bulletin_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_nomdev_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_nomdev='".$_POST['rn_nomdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_toutcoefdev_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_toutcoefdev='".$_POST['rn_toutcoefdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_coefdev_si_diff_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_coefdev_si_diff='".$_POST['rn_coefdev_si_diff_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_datedev_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_datedev='".$_POST['rn_datedev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_abs_2_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_abs_2='".$_POST['rn_abs_2_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_chefetab_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_sign_chefetab='".$_POST['rn_sign_chefetab_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_pp_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_sign_pp='".$_POST['rn_sign_pp_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_resp_'.$per])) {
						$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_sign_resp='".$_POST['rn_sign_resp_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if($_POST['rn_sign_nblig_'.$per]!="") {
						if(mb_strlen(my_ereg_replace("[0-9]","",$_POST['rn_sign_nblig_'.$per]))!=0){$_POST['rn_sign_nblig_'.$per]=3;}

						if (isset($_POST['rn_sign_nblig_'.$per])) {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_sign_nblig='".$_POST['rn_sign_nblig_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					if (isset($_POST['rn_formule_'.$per])) {
						if ($_POST['rn_formule_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET rn_formule='".$_POST['rn_formule_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}


					if (isset($_POST['ects_fonction_signataire_attestation_'.$per])) {
						if ($_POST['ects_fonction_signataire_attestation_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET ects_fonction_signataire_attestation='".$_POST['ects_fonction_signataire_attestation_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					if (isset($_POST['ects_type_formation_'.$per])) {
						if ($_POST['ects_type_formation_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET ects_type_formation='".$_POST['ects_type_formation_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_parcours_'.$per])) {
						if ($_POST['ects_parcours_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET ects_parcours='".$_POST['ects_parcours_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_domaines_etude_'.$per])) {
						if ($_POST['ects_domaines_etude_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET ects_domaines_etude='".$_POST['ects_domaines_etude_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_code_parcours_'.$per])) {
						if ($_POST['ects_code_parcours_'.$per]!='') {
							$register = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET ects_code_parcours='".$_POST['ects_code_parcours_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					// 20121027
					//$tab_param=array('rn_aff_classe_nom');
					$tab_param=array('rn_aff_classe_nom','rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne','rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy', 'rn_type_par_defaut');
					for($loop=0;$loop<count($tab_param);$loop++) {
						if (isset($_POST[$tab_param[$loop].'_'.$per])) {
							if ($_POST[$tab_param[$loop].'_'.$per]!='') {
								$register = saveParamClasse($id_classe, $tab_param[$loop], $_POST[$tab_param[$loop].'_'.$per]);
								if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
							}
						}
					}

					if(isset($_POST['modifier_bull_prefixe_periode_'.$per])) {
						$register = saveParamClasse($id_classe, 'bull_prefixe_periode', $_POST['bull_prefixe_periode_'.$per]);
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if(isset($_POST['modifier_gepi_prof_suivi_'.$per])) {
						$register = saveParamClasse($id_classe, 'gepi_prof_suivi', $_POST['gepi_prof_suivi_'.$per]);
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					// On enregistre les infos relatives aux catégories de matières
					$tab_priorites_categories=array();
					$temoin_pb_ordre_categories="n";
					$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, priority FROM matieres_categories");
					while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
						$reg_priority = $_POST['priority_'.$row["id"].'_'.$per];
						if($reg_priority!='') {
							if (isset($_POST['moyenne_'.$row["id"].'_'.$per])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
							if (!is_numeric($reg_priority)) $reg_priority = 0;
							if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;

							if(in_array($reg_priority, $tab_priorites_categories)) {
								$temoin_pb_ordre_categories="y";
								$reg_priority=max($tab_priorites_categories)+1;
							}
							$tab_priorites_categories[]=$reg_priority;

							$test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')"), 0);
							if ($test == 0) {
								// Pas d'entrée... on créé
								$res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");
							} else {
								// Entrée existante, on met à jour
								$res = mysqli_query($GLOBALS["mysqli"], "UPDATE j_matieres_categories_classes SET priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "' WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $row["id"] . "')");
							}
							if (!$res) {
								$msg .= "<br />Une erreur s'est produite lors de l'enregistrement des données de catégorie.";
							}
							else {
								$nb_modif_priorite++;
							}
						}
					}
					if($temoin_pb_ordre_categories=="y") {
						$msg.="<br /><strong>Anomalie&nbsp;:</strong> Les catégories de matières ne doivent pas avoir le même rang.<br />Cela risque de provoquer des problèmes sur les bulletins.<br />Des mesures ont été prises pour imposer des ordres différents, mais il se peut que l'ordre ne vous convienne pas.<br />\n";
					}


					if((isset($_POST['change_coef']))&&($_POST['change_coef']=='y')) {
						if((isset($_POST['coef_enseignements']))&&($_POST['coef_enseignements']!="")) {
							$coef_enseignements=my_ereg_replace("[^0-9]","",$_POST['coef_enseignements']);
							if($coef_enseignements!="") {
								$sql="UPDATE j_groupes_classes SET coef='".$coef_enseignements."' WHERE id_classe='".$id_classe."';";
								$update_coef=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$update_coef) {
									$msg .= "<br />Une erreur s'est produite lors de la mise à jour des coefficients pour la classe $id_classe.";
								}
								else {
									$nb_reg_ok++;
								}
							}
						}
					}

					if((isset($_POST['forcer_recalcul_rang']))&&($_POST['forcer_recalcul_rang']=='y')) {
						$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
						$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res_per)>0) {
							$lig_per=mysqli_fetch_object($res_per);
							$recalcul_rang="";
							for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
							$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res) {
								$msg.="<br />Erreur lors de la programmation du recalcul des rangs pour la classe ".get_nom_classe($id_classe).".";
							}
							else {
								$nb_reg_ok++;
							}
						}
						else {
							$msg.="<br />Aucune période n'est définie pour cette classe.<br />Recalcul des rangs impossible pour la classe ".get_nom_classe($id_classe).".";
						}
					}


					if((isset($_POST['creer_enseignement']))&&($_POST['creer_enseignement']=='y')) {
						if((isset($_POST['matiere_nouvel_enseignement']))&&($_POST['matiere_nouvel_enseignement']!="")) {

							$matiere_nouvel_enseignement=$_POST['matiere_nouvel_enseignement'];
							$sql="SELECT 1=1 FROM matieres WHERE matiere='$matiere_nouvel_enseignement';";
							$verif=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($verif)==0) {
								$msg .= "<br />La matière $matiere_nouvel_enseignement n'existe pas.";
							}
							else {
								$coef_nouvel_enseignement=isset($_POST['coef_nouvel_enseignement']) ? $_POST['coef_nouvel_enseignement'] : 0;
								$coef_nouvel_enseignement=my_ereg_replace("[^0-9]","",$_POST['coef_nouvel_enseignement']);

								$nouvel_enseignement_visibilite=isset($_POST['nouvel_enseignement_visibilite']) ? $_POST['nouvel_enseignement_visibilite'] : array();
								$nouvel_enseignement_non_visible=array();
								for($loop=0;$loop<count($tab_domaines);$loop++) {
									if(!in_array($tab_domaines[$loop], $nouvel_enseignement_visibilite)) {
										$nouvel_enseignement_non_visible[]=$tab_domaines[$loop];
									}
								}

								$professeur_nouvel_enseignement=isset($_POST['professeur_nouvel_enseignement']) ? $_POST['professeur_nouvel_enseignement'] : NULL;
								$professeur_nouvel_enseignement=my_ereg_replace("[^A-Za-z0-9._-]","",$professeur_nouvel_enseignement);
								if($professeur_nouvel_enseignement!="") {
									$sql="SELECT 1=1 FROM utilisateurs u WHERE u.login='$professeur_nouvel_enseignement';";
									$verif=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($verif)==0) {
										$professeur_nouvel_enseignement="";
									}

									$sql="SELECT 1=1 FROM j_professeurs_matieres jpm WHERE jpm.id_professeur='$professeur_nouvel_enseignement' AND jpm.id_matiere='$matiere_nouvel_enseignement'";
									$verif=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($verif)==0) {
										// Si JavaScript est inactif, on peut proposer un prof qui n'est pas professeur dans la matière.
										// Associons le alors à la matière.

										$sql="SELECT ordre_matieres FROM j_professeurs_matieres jpm WHERE jpm.id_professeur='$professeur_nouvel_enseignement' ORDER BY ordre_matieres DESC LIMIT 1;";
										$res_ordre_matieres=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_ordre_matieres)==0) {
											$tmp_ordre_matieres=1;
										}
										else {
											$tmp_ordre_matieres=old_mysql_result($res_ordre_matieres,0,"ordre_matieres")+1;
										}

										$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$professeur_nouvel_enseignement', id_matiere='$matiere_nouvel_enseignement', ordre_matieres='$tmp_ordre_matieres';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											$professeur_nouvel_enseignement="";
											$msg.="Erreur lors de l'association de ".civ_nom_prenom($professeur_nouvel_enseignement)." avec la matière '$matiere_nouvel_enseignement'";
										}
									}
								}

								$reg_clazz = array();
								$reg_clazz[] = $id_classe;
								$reg_categorie = 1; // Récupérer par la suite la catégorie par défaut de la table 'matieres' (champ categorie_id)

								$nom_nouvel_enseignement=isset($_POST['nom_nouvel_enseignement']) ? $_POST['nom_nouvel_enseignement'] : "";
								if($nom_nouvel_enseignement!="") {
									$reg_nom_groupe=$nom_nouvel_enseignement;
								}
								else {
									$reg_nom_groupe=$matiere_nouvel_enseignement; // Obtenir une unicité...?
								}

								$sql="SELECT nom_complet,categorie_id FROM matieres WHERE matiere='$matiere_nouvel_enseignement';";
								$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_mat)>0) {
									$lig_mat=mysqli_fetch_object($res_mat);
									$reg_categorie=$lig_mat->categorie_id;
									$reg_nom_complet=$lig_mat->nom_complet;
								}

								$description_nouvel_enseignement=isset($_POST['description_nouvel_enseignement']) ? $_POST['description_nouvel_enseignement'] : "";
								if($description_nouvel_enseignement!="") {
									$reg_nom_complet=$description_nouvel_enseignement;
								}

								$reg_matiere=$matiere_nouvel_enseignement;
								$create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_categorie);
								if($create) {
									$current_group=get_group($create);
									// Si le groupe a été créé, il faut pointer le succès de création pour le message de retour.
									$nb_reg_ok++;

									$reg_professeurs = array();
									if($professeur_nouvel_enseignement!="") {
										$reg_professeurs[]=$professeur_nouvel_enseignement;
									}

									if(isset($_POST['declarer_pp_professeur_nouvel_enseignement'])) {
										$sql="SELECT DISTINCT professeur FROM j_eleves_professeurs WHERE id_classe='$id_classe';";
										$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_pp)>0) {
											while($lig_pp=mysqli_fetch_object($res_pp)) {
												if(!in_array($lig_pp->professeur, $reg_professeurs)) {
													$sql="SELECT 1=1 FROM j_professeurs_matieres jpm WHERE jpm.id_professeur='$lig_pp->professeur' AND jpm.id_matiere='$matiere_nouvel_enseignement'";
													$verif=mysqli_query($GLOBALS["mysqli"], $sql);
													if(mysqli_num_rows($verif)==0) {
														// Si JavaScript est inactif, on peut proposer un prof qui n'est pas professeur dans la matière.
														// Associons le alors à la matière.

														$sql="SELECT ordre_matieres FROM j_professeurs_matieres jpm WHERE jpm.id_professeur='$lig_pp->professeur' ORDER BY ordre_matieres DESC LIMIT 1;";
														$res_ordre_matieres=mysqli_query($GLOBALS["mysqli"], $sql);
														if(mysqli_num_rows($res_ordre_matieres)==0) {
															$tmp_ordre_matieres=1;
														}
														else {
															$tmp_ordre_matieres=old_mysql_result($res_ordre_matieres,0,"ordre_matieres")+1;
														}

														$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$lig_pp->professeur', id_matiere='$matiere_nouvel_enseignement', ordre_matieres='$tmp_ordre_matieres';";
														$insert=mysqli_query($GLOBALS["mysqli"], $sql);
														if(!$insert) {
															$msg.="Erreur lors de l'association de ".civ_nom_prenom($lig_pp->professeur)." avec la matière '$matiere_nouvel_enseignement'.<br />";
															//$msg.="$sql<br />";
														}
														else {
															$reg_professeurs[]=$lig_pp->professeur;
														}
													}
													else {
														$reg_professeurs[]=$lig_pp->professeur;
													}
												}
											}
										}
									}

									$nouvel_enseignement_eleves=isset($_POST['nouvel_enseignement_eleves']) ? $_POST['nouvel_enseignement_eleves'] : "tous";
									$tab_choix_nouvel_enseignement_eleves=array("tous", "aucun", "1", "2");
									if(!in_array($nouvel_enseignement_eleves, $tab_choix_nouvel_enseignement_eleves)) {$nouvel_enseignement_eleves="tous";}
									$reg_eleves=array();
									foreach ($current_group["periodes"] as $period) {
										$reg_eleves[$period['num_periode']]=array();
										if($nouvel_enseignement_eleves!="aucun") {
											$sql="SELECT jec.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='$id_classe' AND jec.periode='".$period['num_periode']."' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
											$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
											$eff_ele_ens=mysqli_num_rows($res_ele);
											if($eff_ele_ens>0){
												$cpt_ele_ens=0;
												if($nouvel_enseignement_eleves=='1') {
													while($lig_ele=mysqli_fetch_object($res_ele)){
														if($cpt_ele_ens<$eff_ele_ens/2) {
															$reg_eleves[$period['num_periode']][]=$lig_ele->login;
														}
														$cpt_ele_ens++;
													}
												}
												elseif($nouvel_enseignement_eleves=='2') {
													while($lig_ele=mysqli_fetch_object($res_ele)){
														if($cpt_ele_ens>=$eff_ele_ens/2) {
															$reg_eleves[$period['num_periode']][]=$lig_ele->login;
														}
														$cpt_ele_ens++;
													}
												}
												else {
													while($lig_ele=mysqli_fetch_object($res_ele)){
														$reg_eleves[$period['num_periode']][]=$lig_ele->login;
													}
												}
											}
										}
									}

									$res = update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);

									if($coef_nouvel_enseignement!="") {
										$sql="UPDATE j_groupes_classes SET coef='$coef_nouvel_enseignement' WHERE id_groupe='$create' AND id_classe='$id_classe';";
										$res_coef=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$res_coef) {
											$msg .= "<br />Erreur lors de la mise à jour du coefficient du groupe n°$create pour la classe n°$id_classe.";
										}
										else {
											$nb_reg_ok++;
										}
									}

									for($loop=0;$loop<count($nouvel_enseignement_non_visible);$loop++) {
										$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$create', domaine='".$nouvel_enseignement_non_visible[$loop]."', visible='n';";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$insert) {
											$msg .= "<br />Erreur lors de la mise à jour de la non visibilité de ".$nouvel_enseignement_non_visible[$loop]." du groupe n°$create pour la classe n°$id_classe.";
										}
										/*
										else {
											$nb_reg_ok++;
										}
										*/
									}
								}
							}

						}
					}

					if((isset($_POST['change_visibilite']))&&(isset($_POST['matiere_modif_visibilite_enseignement']))&&($_POST['matiere_modif_visibilite_enseignement']!="")) {
						$matiere_modif_visibilite_enseignement=$_POST['matiere_modif_visibilite_enseignement'];
						$modif_enseignement_visibilite=isset($_POST['modif_enseignement_visibilite']) ? $_POST['modif_enseignement_visibilite'] : array();

						$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_classe='".$id_classe."' AND jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$matiere_modif_visibilite_enseignement."';";
						//echo "$sql<br />";
						$res_grp_vis=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_grp_vis=mysqli_fetch_object($res_grp_vis)) {
							for($loop=0;$loop<count($tab_domaines);$loop++) {
								$sql="DELETE FROM j_groupes_visibilite WHERE id_groupe='$lig_grp_vis->id_groupe' AND domaine='$tab_domaines[$loop]';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!in_array($tab_domaines[$loop], $modif_enseignement_visibilite)) {
									$sql="INSERT INTO j_groupes_visibilite SET id_groupe='$lig_grp_vis->id_groupe', domaine='$tab_domaines[$loop]', visible='n';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$insert) {
										$msg.="<br />Erreur lors de l'enregistrement de la non-visibilité du groupe n°$lig_grp_vis->id_groupe sur ".$tab_domaines[$loop];
									}
									else {
										$nb_reg_ok++;
									}
								}
							}
						}
					}


					/*
					$_POST['change_inscription_eleves']=	y
					$_POST['matiere_modif_inscription_eleves']=	Asdt
					$_POST['change_inscription_eleves_inscrire']=	n
					$_POST['change_inscription_eleves_periodes']=	Array (*)
					$_POST[change_inscription_eleves_periodes]['0']=	1
					$_POST[change_inscription_eleves_periodes]['1']=	2
					$_POST[change_inscription_eleves_periodes]['2']=	3
					*/
					if((isset($_POST['change_inscription_eleves']))&&(isset($_POST['matiere_modif_inscription_eleves']))&&($_POST['change_inscription_eleves_inscrire']!="")&&(isset($_POST['change_inscription_eleves_periodes']))) {

						$matiere_modif_inscription_eleves=$_POST['matiere_modif_inscription_eleves'];
						$change_inscription_eleves_inscrire=$_POST['change_inscription_eleves_inscrire'];
						$change_inscription_eleves_periodes=$_POST['change_inscription_eleves_periodes'];

						if($change_inscription_eleves_inscrire=="y") {
							$tab_ele_clas=array();
							for($loop=0;$loop<count($change_inscription_eleves_periodes);$loop++) {
								$tab_ele_clas[$change_inscription_eleves_periodes[$loop]]=array();
								$sql="SELECT * FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='".$change_inscription_eleves_periodes[$loop]."';";
								$res_ele_clas=mysqli_query($GLOBALS["mysqli"], $sql);
								while($lig_ele_clas=mysqli_fetch_object($res_ele_clas)) {
									$tab_ele_clas[$change_inscription_eleves_periodes[$loop]][]=$lig_ele_clas->login;
								}
							}
						}

						$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_classe='".$id_classe."' AND jgc.id_groupe=jgm.id_groupe AND jgm.id_matiere='".$matiere_modif_inscription_eleves."';";
						//echo "$sql<br />";
						$res_grp_inscr=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_grp_inscr=mysqli_fetch_object($res_grp_inscr)) {
							if($change_inscription_eleves_inscrire=="y") {
								for($loop=0;$loop<count($change_inscription_eleves_periodes);$loop++) {
									foreach($tab_ele_clas[$change_inscription_eleves_periodes[$loop]] as $current_eleve_login) {
										$sql="SELECT 1=1 FROM j_eleves_groupes WHERE login='".$current_eleve_login."' AND id_groupe='$lig_grp_inscr->id_groupe' AND periode='".$change_inscription_eleves_periodes[$loop]."';";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)==0) {
											$sql="INSERT INTO j_eleves_groupes SET login='".$current_eleve_login."', id_groupe='$lig_grp_inscr->id_groupe', periode='".$change_inscription_eleves_periodes[$loop]."';";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if($insert) {
												$nb_reg_ok++;
											}
											else {
												$msg.="<br />ERREUR lors de l'inscription de ".get_nom_prenom_eleve($current_eleve_login)." du groupe n°".$lig_grp_inscr->id_groupe." en période ".$change_inscription_eleves_periodes[$loop];
											}
										}
									}
								}
							}
							else {
								for($loop=0;$loop<count($change_inscription_eleves_periodes);$loop++) {
									$sql="SELECT login FROM j_eleves_groupes WHERE id_groupe='$lig_grp_inscr->id_groupe' AND periode='".$change_inscription_eleves_periodes[$loop]."';";
									$res_ele_inscr=mysqli_query($GLOBALS["mysqli"], $sql);

									while($lig_ele_inscr=mysqli_fetch_object($res_ele_inscr)) {
										if (!test_before_eleve_removal($lig_ele_inscr->login, $lig_grp_inscr->id_groupe, $change_inscription_eleves_periodes[$loop])) {
											$msg.="<br />".get_nom_prenom_eleve($lig_ele_inscr->login)." a un bulletin non vide en période ".$change_inscription_eleves_periodes[$loop];
										}
										elseif(nb_notes_ele_dans_tel_enseignement($lig_ele_inscr->login, $lig_grp_inscr->id_groupe, $change_inscription_eleves_periodes[$loop])>0) {
											$msg.="<br />".get_nom_prenom_eleve($lig_ele_inscr->login)." a un bulletin non vide en période ".$change_inscription_eleves_periodes[$loop];
										}
										else {
											$sql="DELETE FROM j_eleves_groupes WHERE login='".$lig_ele_inscr->login."' AND id_groupe='$lig_grp_inscr->id_groupe' AND periode='".$change_inscription_eleves_periodes[$loop]."';";
											$del=mysqli_query($GLOBALS["mysqli"], $sql);
											if($del) {
												$nb_reg_ok++;
											}
											else {
												$msg.="<br />ERREUR lors de la désinscription de ".get_nom_prenom_eleve($lig_ele_inscr->login)." du groupe n°".$lig_grp_inscr->id_groupe." en période ".$change_inscription_eleves_periodes[$loop];
											}
										}
									}
								}
							}
						}
					}



					/*
					$_POST['change_coef2']=	y
					$_POST['coef_enseignements2']=	3
					$_POST['matiere_modif_coef']=	MATHS
					$_POST['modif_enseignement_visibilite2']=	bulletins|y
					*/

					if((isset($_POST['change_coef2']))&&(isset($_POST['coef_enseignements2']))&&($_POST['coef_enseignements2']!="")&&(is_numeric($_POST['coef_enseignements2']))&&(isset($_POST['matiere_modif_coef']))&&($_POST['matiere_modif_coef']!="")) {
						$modif_enseignement_visibilite2=isset($_POST['modif_enseignement_visibilite2']) ? $_POST['modif_enseignement_visibilite2'] : "";
						$coef_enseignements2=$_POST['coef_enseignements2'];
						$matiere_modif_coef=$_POST['matiere_modif_coef'];

						if($modif_enseignement_visibilite2!="") {
							$tmp_tab_vis=explode("|", $modif_enseignement_visibilite2);
							if(isset($tmp_tab_vis[1])) {
								if($matiere_modif_coef=='___Tous_les_enseignements___') {
									if($tmp_tab_vis[1]=='y') {
										$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}
									else {
										$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}

									$res_modif_coef=mysqli_query($GLOBALS["mysqli"], $sql);
									if(!$res_modif_coef) {
										$msg.="Erreur lors de la requête<br />$sql<br />";
									}
									else {
										$nb_reg_ok++;
									}

								}
								else {
									/*
									if($tmp_tab_vis[1]=='y') {
										$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe IN (SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."' AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe') AND id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}
									else {
										$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe IN (SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."' AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe') AND id_groupe IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}
									*/
									if($tmp_tab_vis[1]=='y') {
										$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."' AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe' AND jgc.id_groupe NOT IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}
									else {
										$sql="SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."' AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe' AND jgc.id_groupe IN (SELECT id_groupe FROM j_groupes_visibilite WHERE domaine='".$tmp_tab_vis[0]."' AND visible='n');";
									}
									$res_grp_modif_coef=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_grp_modif_coef)>0) {
										while($lig_grp_modif_coef=mysqli_fetch_object($res_grp_modif_coef)) {
											$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe='$lig_grp_modif_coef->id_groupe';";
											$res_modif_coef=mysqli_query($GLOBALS["mysqli"], $sql);
											if(!$res_modif_coef) {
												$msg.="Erreur lors de la requête<br />$sql<br />";
											}
											else {
												$nb_reg_ok++;
											}
										}
									}
								}
								/*
								$res_modif_coef=mysql_query($sql);
								if(!$res_modif_coef) {
									$msg.="Erreur lors de la requête<br />$sql<br />";
								}
								else {
									$nb_reg_ok++;
								}
								*/
							}
							else {
								$msg.="Mode de visibilité ou non choisi inattendu pour les enseignements dont vous souhaitez modifier le coefficient.<br />";
							}
						}
						else {
							if($matiere_modif_coef=='___Tous_les_enseignements___') {
								$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe';";
							}
							else {
								//$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe IN (SELECT jgc.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."' AND jgc.id_groupe=jgm.id_groupe AND jgc.id_classe='$id_classe');";
								$sql="UPDATE j_groupes_classes SET coef='$coef_enseignements2' WHERE id_classe='$id_classe' AND id_groupe IN (SELECT jgm.id_groupe FROM j_groupes_matieres jgm WHERE jgm.id_matiere='".$matiere_modif_coef."');";

							}
							//echo "$sql<br />";
							$res_modif_coef=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res_modif_coef) {
								$msg.="Erreur lors de la requête<br />$sql<br />";
							}
							else {
								$nb_reg_ok++;
							}
						}
					}

					//====================================
				}
			}
			$nbc++;
		}
	}

	if ($reg_ok=='') {
		if(($nb_reg_ok==0)&&($nb_modif_priorite==0)) {
			$message_enregistrement = "Aucune modification n'a été effectuée !";
		}
		else {
			$message_enregistrement = ($nb_reg_ok+$nb_modif_priorite)." modification(s) effectuée(s) !";
		}
		$affiche_message = 'yes';
	} else if ($reg_ok=='yes') {
		$message_enregistrement = "Les modifications ont été effectuées avec succès.";
		$affiche_message = 'yes';
	} else {
		$message_enregistrement = "Il y a eu un problème lors de l'enregistrement des modifications.";
		$affiche_message = 'yes';
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Paramétrage des classes par lots";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
if($max_periode <= 0) {
	echo "<p style='color:red'>Aucune classe comportant des périodes n'a été définie.</p>";
	die();
}
echo "<form action=\"classes_param.php\" method='post' name='formulaire'>\n";
echo add_token_field();
echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <input type='submit' name='enregistrer1' value='Enregistrer' /></p>";
echo "Sur cette page, vous pouvez modifier différents paramètres par lots de classes cochées ci-dessous.";
/*
echo "<script language='javascript' type='text/javascript'>
function checkAll(){
	champs_input=document.getElementsByTagName('input');
	//alert('champs_input.length='+champs_input.length)
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		//if(type==\"checkbox\"){
		name=champs_input[i].getAttribute('name');
		//alert('name='+name+'\\ntype='+type)
		if((type==\"checkbox\")&&(name.mb_substr(0,5)=='case_')){
			champs_input[i].checked=true;
		}
	}
	//alert(champs_input[i-1])
}
function UncheckAll(){
	champs_input=document.getElementsByTagName('input');
	for(i=0;i<champs_input.length;i++){
		type=champs_input[i].getAttribute('type');
		//if(type==\"checkbox\"){
		name=champs_input[i].getAttribute('name');
		if((type==\"checkbox\")&&(name.mb_substr(0,5)=='case_')){
			champs_input[i].checked=false;
		}
	}
}
</script>\n";
echo "<p><a href='javascript:checkAll();'>Cocher toutes les classes</a> / <a href='javascript:UncheckAll();'>Tout décocher</a></p>\n";
*/

$tab_id_cases_classes_postees_precedemment=array();
$liste_classes_postees_precedemment="";
// Première boucle sur le nombre de periodes
$per = 0;
while ($per < $max_periode) {
	$per++;
	// On dresse la liste de toutes les classes non virtuelles
	$classes_list = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
	$nb_classe = mysqli_num_rows($classes_list);
	// $nb : nombre de classes ayant un nombre de periodes égal à $per
	$nb=0;
	$nbc = 0;
	while ($nbc < $nb_classe) {
		$id_classe = old_mysql_result($classes_list,$nbc,'id');
		$query_per = mysqli_query($GLOBALS["mysqli"], "SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
		$nb_periode = mysqli_num_rows($query_per);
		if ($nb_periode == $per) {
			$tab_id_classe[$nb] = $id_classe;
			$tab_nom_classe[$nb] = old_mysql_result($classes_list,$nbc,'classe');
			$nb++;
		}
		$nbc++;
	}
	if ($nb != 0) {
		echo "<center><p class='grand'>Classes ayant ".$per." période";
		if ($per > 1) echo "s";
		echo "</p></center>\n";
		// S'il existe des classe ayant un nombre de periodes égal = $per :
		$nb_ligne = intval($nb/3)+1;
		echo "<table width = 100% class='boireaus' border='1'>\n";

		$alt=1;
		$i ='0';
		while ($i < $nb_ligne) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			$j = 0;
			while ($j < 3) {
				unset($nom_case);
				$nom_classe = '';
				if (isset($tab_id_classe[$i+$j*$nb_ligne])) {$nom_case = "case_".$tab_id_classe[$i+$j*$nb_ligne];}
				if (isset($tab_nom_classe[$i+$j*$nb_ligne])) {$nom_classe = $tab_nom_classe[$i+$j*$nb_ligne];}

				echo "<td>\n";
				if ($nom_classe != '') {
					echo "<input type=\"checkbox\" name=\"".$nom_case."\" id='case_".$per."_".$i."_".$j."' onchange=\"change_style_classe('".$per."_".$i."_".$j."')\" checked /><label id='label_case_".$per."_".$i."_".$j."' for='case_".$per."_".$i."_".$j."' style='cursor:pointer; font-weight:bold'>&nbsp;".$nom_classe."</label>\n";
					if(isset($_POST[$nom_case])) {
						$tab_id_cases_classes_postees_precedemment[]="case_".$per."_".$i."_".$j;
						if($liste_classes_postees_precedemment!="") {
							$liste_classes_postees_precedemment.=", ";
						}
						$liste_classes_postees_precedemment.=$nom_classe;
					}
				}
				echo "</td>\n";

				$j++;
			}

			echo "<th>";
			echo "<a href='javascript:modif_case($per,$i,\"lig\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			echo "<a href='javascript:modif_case($per,$i,\"lig\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "</th>\n";

			echo "</tr>\n";
			$i++;
		}

		echo "<tr>\n";
		$j=0;
		while ($j < 3) {
			echo "<th>\n";
			echo "<a href='javascript:modif_case($per,$j,\"col\",true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			echo "<a href='javascript:modif_case($per,$j,\"col\",false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "</th>\n";
			$j++;
		}
		//echo "<td>&nbsp;</td>\n";
		echo "<th>";

			echo "<a href='javascript:tout_cocher($per,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			echo "<a href='javascript:tout_cocher($per,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

		echo "</th>\n";
		echo "</tr>\n";

		echo "</table>\n";



		echo "<script type='text/javascript' language='javascript'>
	function modif_case(per,rang,type,statut){
		// type: col ou lig
		// rang: le numéro de la colonne ou de la ligne
		// statut: true ou false
		if(type=='col'){
			for(k=0;k<$nb_ligne;k++){
				if(document.getElementById('case_'+per+'_'+k+'_'+rang)){
					document.getElementById('case_'+per+'_'+k+'_'+rang).checked=statut;
					change_style_classe(per+'_'+k+'_'+rang);
				}
			}
		}
		else{
			for(k=0;k<3;k++){
				if(document.getElementById('case_'+per+'_'+rang+'_'+k)){
					document.getElementById('case_'+per+'_'+rang+'_'+k).checked=statut;
					change_style_classe(per+'_'+rang+'_'+k);
				}
			}
		}
		changement();
	}

	function tout_cocher(per,statut){
		for(kk=0;kk<=3;kk++){
			for(k=0;k<=$nb_ligne;k++){
				if(document.getElementById('case_'+per+'_'+k+'_'+kk)){
					document.getElementById('case_'+per+'_'+k+'_'+kk).checked=statut;
					change_style_classe(per+'_'+k+'_'+kk);
				}
			}
		}
	}

	function change_style_classe(num) {
		//alert(num);
		if(document.getElementById('case_'+num)) {
			if(document.getElementById('case_'+num).checked) {
				document.getElementById('label_case_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_case_'+num).style.fontWeight='normal';
			}
		}
	}";

		if(count($tab_id_cases_classes_postees_precedemment)>0) {
			echo "
	function cocher_classes_post_precedent() {
		tout_cocher($per, false);";
		for($loop=0;$loop<count($tab_id_cases_classes_postees_precedemment);+$loop++) {
			echo "
				if(document.getElementById('".$tab_id_cases_classes_postees_precedemment[$loop]."')){
					document.getElementById('".$tab_id_cases_classes_postees_precedemment[$loop]."').checked=true;
					change_style_classe('".preg_replace("/^case_/", "", $tab_id_cases_classes_postees_precedemment[$loop])."');
				}
			";
		}
		echo "
	function cocher_classes_post_precedent_inverse() {
		tout_cocher($per, true);";
		for($loop=0;$loop<count($tab_id_cases_classes_postees_precedemment);+$loop++) {
			echo "
				if(document.getElementById('".$tab_id_cases_classes_postees_precedemment[$loop]."')){
					document.getElementById('".$tab_id_cases_classes_postees_precedemment[$loop]."').checked=false;
					change_style_classe('".preg_replace("/^case_/", "", $tab_id_cases_classes_postees_precedemment[$loop])."');
				}
			";
		}
		echo "
	}";
		}

		echo "
</script>\n";

		if(count($tab_id_cases_classes_postees_precedemment)>0) {
			echo "<p style='margin-top:1em;margin-bottom:1em;'>
	<a href='javascript:cocher_classes_post_precedent()'>Effectuer la même sélection de classes qu'à l'opération précédente (<em>$liste_classes_postees_precedemment</em>).</a><br />
	<a href='javascript:cocher_classes_post_precedent_inverse()'>Effectuer la sélection de classes inverse de celle de l'opération précédente.</a>
</p>";
		}

		?>
		<p style='text-indent:-6em; margin-left:6em;'><em>Remarque&nbsp;:</em> Les modifications qui seront apportées ne concerneront que les cases cochées ci-dessus.<br />
		Les modifications porteront sur ce que vous cocherez/remplirez ci-dessous.<br />
		Aucune modification ne sera apportée (<em>sur les classes choisies</em>) pour les champs laissés vides ci-dessous.</p>
		<br />

		<p class='bold'>Pour la ou les classe(s) sélectionnée(s) ci-dessus&nbsp;: </p>

		<table width=100% border=2 cellspacing=1  cellpadding=3 class='boireaus'>
		<tr>
		<th>&nbsp;</th>
		<th>Nom de la période</th>
		<th title="La date précisée ici est prise en compte pour les appartenances des élèves à telle classe sur telle période (notamment pour les élèves changeant de classe).
Il n'est pas question ici de verrouiller automatiquement une période de note à la date saisie.">Date de fin de la période</th>
		</tr>

		<?php

		include_once("../lib/calendrier/calendrier.class.php");

		$k = '1';
		$alt=1;
		while($k < $per+1) {
			$alt=$alt*(-1);
			//$cal[$per][$k] = new Calendrier("formulaire", "date_fin_".$per."_".$k);
			echo "<tr class='lig$alt'>\n";
			echo "<th>Période ".$k."</th>\n";
			echo "<td><input type='text' name='nb_".$per."_".$k."' value=\"\" size='30' /></td>\n";
			echo "<td><input type='text' name='date_fin_".$per."_".$k."' id='date_fin_".$per."_".$k."' value=\"\" size='10' ";
			echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
			echo "/>";
			//echo "<a href=\"#calend\" onClick=\"".$cal[$per][$k]->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
			echo img_calendrier_js('date_fin_'.$per.'_'.$k, 'img_bouton_date_fin_'.$per.'_'.$k);
			echo "</td>\n";
			echo"</tr>\n";
			$k++;
		}

		?>

		</table>
		<p style='margin-top:1em;'>Prénom et nom du signataire des bulletins<?php if ($gepiSettings['active_mod_ects'] == "y") echo " et des attestations ECTS" ?> (chef d'établissement ou son représentant)&nbsp;:
		<br /><input type="text" size="30" name="<?php echo "nb_".$per."_reg_suivi_par"; ?>" value="" /></p>
        <?php if ($gepiSettings['active_mod_ects'] == "y") { ?>
            <p>Fonction du signataire sus-nommé (ex.: "Proviseur")&nbsp;: <br /><input type="text" size="40" name="ects_fonction_signataire_attestation_<?php echo $per;?>" value="" /></p>
        <?php } ?>
		<p>Formule à insérer sur les bulletins (cette formule sera suivie des nom et prénom de la personne désignée ci_dessus&nbsp;:
		<br /><input type="text" size="80" name="<?php echo "nb_".$per."_reg_formule"; ?>" value="" /></p>

		<p style='margin-top:1em;'>Désignation alternative de la personne suivant la classe (<em>chef d'établissement ou son représentant</em>) pouvant être utilisée dans des publipostages OOo&nbsp;: <br />
		<input type='text' size='30' name="<?php echo "nb_".$per."_reg_suivi_par_alt"; ?>" value = ""  onchange='changement()' /><br />
		Fonction associée (<em>chef, adjoint</em>)&nbsp;:<br />
		<input type='text' size='30' name="<?php echo "nb_".$per."_reg_suivi_par_alt_fonction"; ?>" value = ""  onchange='changement()' />
		</p>

		<p style='margin-top:1em;'><input type='checkbox' name='modifier_gepi_prof_suivi_<?php echo $per;?>' id='modifier_gepi_prof_suivi_<?php echo $per;?>' value='y' /><label for='modifier_gepi_prof_suivi_<?php echo $per;?>'>Modifier la dénomination du professeur chargé du suivi des élèves</label><br />
		&nbsp;&nbsp;&nbsp;Dénomination du professeur chargé du suivi des élèves&nbsp;:<?php 
				echo "
			<input type='text' name='gepi_prof_suivi_".$per."' id='gepi_prof_suivi_".$per."' value=\"".getSettingValue('gepi_prof_suivi')."\" onchange=\"document.getElementById('modifier_gepi_prof_suivi_".$per."').checked=true;changement()\" />";
			?>
		</td>
	</tr>


		<p>Formatage de l'identité des professeurs&nbsp;:

		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_np" value="<?php echo "nb_".$per."_np"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_np' style='cursor: pointer;'>Nom Prénom (Durand Albert)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_pn" value="<?php echo "nb_".$per."_pn"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_pn' style='cursor: pointer;'>Prénom Nom (Albert Durand)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_in" value="<?php echo "nb_".$per."_in"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_in' style='cursor: pointer;'>Initiale-Prénom Nom (A. Durand)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_ni" value="<?php echo "nb_".$per."_ni"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_ni' style='cursor: pointer;'>Nom Initiale-Prénom (Durand A.)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_cnp" value="<?php echo "nb_".$per."_cnp"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_cnp' style='cursor: pointer;'>Civilité Nom Prénom (M. Durand Albert)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_cpn" value="<?php echo "nb_".$per."_cpn"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_cpn' style='cursor: pointer;'>Civilité Prénom Nom (M. Albert Durand)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_cin" value="<?php echo "nb_".$per."_cin"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_cin' style='cursor: pointer;'>Civ. initiale-Prénom Nom (M. A. Durand)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_cni" value="<?php echo "nb_".$per."_cni"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_cni' style='cursor: pointer;'>Civ. Nom initiale-Prénom (M. Durand A.)</label>
		<br />
		<input type="radio" name="<?php echo "nb_".$per."_reg_format"; ?>" id="<?php echo "nb_".$per."_reg_format"; ?>_cn" value="<?php echo "nb_".$per."_cn"; ?>" />
		<label for='<?php echo "nb_".$per."_reg_format"; ?>_cn' style='cursor: pointer;'>Civ. Nom (M. Durand)</label>
		<br />

	<p>Formatage de l'identité des élèves sur les bulletins&nbsp;:

	<br />
	<input type="radio" name="<?php echo "nb_".$per."_reg_elformat"; ?>" id="<?php echo "nb_".$per."_reg_elformat"; ?>_np" value="<?php echo "nb_".$per."_np"; ?>" />
	<label for='<?php echo "nb_".$per."_reg_elformat"; ?>_np' style='cursor: pointer;'>Nom Prénom (Durand Albert)</label>
	<br />
	<input type="radio" name="<?php echo "nb_".$per."_reg_elformat"; ?>" id="<?php echo "nb_".$per."_reg_elformat"; ?>_pn" value="<?php echo "nb_".$per."_pn"; ?>" />
	<label for='<?php echo "nb_".$per."_reg_elformat"; ?>_pn' style='cursor: pointer;'>Prénom Nom (Albert Durand)</label>
	<br />
<br />
<h2><b>Enseignements</b></h2>
<table border='0' cellspacing='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold;"-->
	<td>
	<input type='checkbox' name='change_coef' id='change_coef' value='y' /><label for='change_coef'> Passer les coefficients de tous les enseignements à</label>&nbsp;:
	</td>
	<td>
	<select name='coef_enseignements' onchange="document.getElementById('change_coef').checked=true">
	<?php
	echo "<option value=''>---</option>\n";
	for($i=0;$i<20;$i++){
		echo "<option value='$i'>$i</option>\n";
	}
	?>
	</select>
	</td>
</tr>
</table>

<?php
	$sql="SELECT DISTINCT matiere,nom_complet FROM matieres m, j_groupes_matieres jgm WHERE jgm.id_matiere=m.matiere ORDER BY m.nom_complet,m.matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mat)>0) {
?>
<table border='0' cellspacing='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold;"-->
	<td>
	<input type='checkbox' name='change_coef2' id='change_coef2' value='y' /><label for='change_coef2'> Forcer à </label>
	<select name='coef_enseignements2' id='coef_enseignements2' onchange="if((document.getElementById('matiere_modif_coef').selectedIndex==0)||(document.getElementById('coef_enseignements2').selectedIndex==0)) {document.getElementById('change_coef2').checked=false} else {document.getElementById('change_coef2').checked=true}">
	<?php
		echo "<option value=''>---</option>\n";
		for($i=0;$i<20;$i++){
			echo "<option value='$i'>$i</option>\n";
		}
	?>
	</select>
	<label for='change_coef2'> les coefficients des enseignements de </label>
	<select name='matiere_modif_coef' id='matiere_modif_coef' onchange="if((document.getElementById('matiere_modif_coef').selectedIndex==0)||(document.getElementById('coef_enseignements2').selectedIndex==0)) {document.getElementById('change_coef2').checked=false} else {document.getElementById('change_coef2').checked=true}">
		<option value=''>---</option>
		<option value='___Tous_les_enseignements___'>Tous les enseignements</option>
	<?php
		while($lig_mat=mysqli_fetch_object($res_mat)) {
			echo "		<option value='$lig_mat->matiere' title=\"$lig_mat->matiere ($lig_mat->nom_complet)\">".htmlspecialchars($lig_mat->nom_complet)."</option>\n";
		}
	?>
	</select>
	</td>
	<td>
		s'ils sont
		<table class='boireaus' cellspacing='0'>
			<?php
				echo "<tr>\n";
				echo "<th><input type='radio' name='modif_enseignement_visibilite2' value='' title='Ne pas tenir compte de la visibilité ou non des enseignements pour modifier leur coefficient' checked /></th>\n";
				for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
					echo "<th title=\"Visibilité : ".$tab_domaines_texte[$loop]."\">\n";
					echo $tab_domaines_sigle[$loop];
					echo "</th>\n";
				}

				echo "</tr>\n";
				echo "<tr class='lig-1'>\n";
				echo "<th>visibles sur </th>";
				for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
					echo "<td title=\"visibles sur ".$tab_domaines_texte[$loop]."\">\n";
					echo "<input type='radio' name='modif_enseignement_visibilite2' value='$tab_domaines[$loop]|y' />\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
				echo "<tr class='lig1'>\n";
				echo "<th>invisibles sur </th>";
				for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
					echo "<td title=\"invisibles sur ".$tab_domaines_texte[$loop]."\">\n";
					echo "<input type='radio' name='modif_enseignement_visibilite2' value='$tab_domaines[$loop]|n' />\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			?>
		</table>
	</td>
</tr>
</table>
<?php
}
?>


<?php
	$sql="SELECT DISTINCT matiere,nom_complet FROM matieres m, j_groupes_matieres jgm WHERE jgm.id_matiere=m.matiere ORDER BY m.nom_complet,m.matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mat)>0) {
		echo "<table border='0' cellspacing='0'>
	<tr>
		<td rowspan='2'>&nbsp;&nbsp;&nbsp;</td>
		<td valign='top' rowspan='2'>
			<input type='checkbox' name='change_visibilite' id='change_visibilite' value='y' /><label for='change_visibilite'> Modifier la visibilité des enseignements de</label>&nbsp;:
		</td>
		<td colspan='2'>
			<select name='matiere_modif_visibilite_enseignement' id='matiere_modif_visibilite_enseignement' onchange=\"document.getElementById('change_visibilite').checked=true;\">\n";
			echo "			<option value=''>---</option>\n";
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				echo "			<option value='$lig_mat->matiere' title=\"$lig_mat->matiere ($lig_mat->nom_complet)\">".htmlspecialchars($lig_mat->nom_complet)."</option>\n";
			}
			echo "	</select>
		</td>
	</tr>
	<tr>
		<td valign='top'>Visibilité&nbsp;: </td>
		<td>
			<table class='boireaus' cellspacing='0'>
				<tr>\n";
			for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
				echo "<th title=\"Visibilité : ".$tab_domaines_texte[$loop]."\">\n";
				echo $tab_domaines_sigle[$loop];
				echo "</th>\n";
			}
			echo "</tr>\n";
			echo "<tr class='lig-1'>\n";
			for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
				echo "<td title=\"Visibilité : ".$tab_domaines_texte[$loop]."\">\n";
				echo "<input type='checkbox' name='modif_enseignement_visibilite[]' value='$tab_domaines[$loop]' checked />\n";
				echo "</td>\n";
			}
			echo "
				</tr>
			</table>
		</td>
	</tr>
</table>\n";
	}



	$sql="SELECT DISTINCT matiere,nom_complet FROM matieres m, j_groupes_matieres jgm WHERE jgm.id_matiere=m.matiere ORDER BY m.nom_complet,m.matiere;";
	$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mat)>0) {
		echo "<table border='0' cellspacing='0'>
	<tr>
		<td rowspan='2'>&nbsp;&nbsp;&nbsp;</td>
		<td valign='top' rowspan='2'>
			<input type='checkbox' name='change_inscription_eleves' id='change_inscription_eleves' value='y' /><label for='change_inscription_eleves'> Modifier les inscriptions d'élèves dans les enseignements de</label>&nbsp;:
		</td>
		<td colspan='3'>
			<select name='matiere_modif_inscription_eleves' id='matiere_modif_inscription_eleves' onchange=\"document.getElementById('change_inscription_eleves').checked=true;\">\n";
			echo "			<option value=''>---</option>\n";
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				echo "			<option value='$lig_mat->matiere' title=\"$lig_mat->matiere ($lig_mat->nom_complet)\">".htmlspecialchars($lig_mat->nom_complet)."</option>\n";
			}
			echo "	</select>
		</td>
	</tr>
	<tr>
		<td valign='top'>
			<input type='radio' name='change_inscription_eleves_inscrire' id='change_inscription_eleves_inscrire_y' value='y' checked /><label for='change_inscription_eleves_inscrire_y'> Inscrire tous les élèves</label><br />
			<input type='radio' name='change_inscription_eleves_inscrire' id='change_inscription_eleves_inscrire_n' value='n' /><label for='change_inscription_eleves_inscrire_n' title=\"Désinscrire sous réserve qu'il n'y ait pas de note ou appréciation sur les bulletins ou dans les carnets de notes.\"> Désinscrire tous les élèves (*)</label>
		</td>
		<td valign='top'>sur les périodes&nbsp;: </td>
		<td>";
			for($loop=1;$loop<=$max_periode;$loop++) {
				echo "<input type='checkbox' name='change_inscription_eleves_periodes[]' id='change_inscription_eleves_periodes_$loop' value='$loop' checked /><label for='change_inscription_eleves_periodes_$loop'> $loop </label><br />\n";
			}
			echo "
		</td>
	</tr>
</table>\n";
	}
?>


<table border='0' cellspacing='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold; vertical-align:top;"-->
	<td style="vertical-align:top;">
	<input type='checkbox' name='creer_enseignement' id='creer_enseignement' value='y' /><label for='creer_enseignement'> Créer un enseignement de</label>&nbsp;:
	</td>
	<?php
		$sql="SELECT DISTINCT matiere,nom_complet FROM matieres ORDER BY nom_complet,matiere;";
		$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_mat)==0) {
			echo "<td>Aucune matière n'est encore créée.</td>\n";
		}
		else {
			echo "<td colspan='2'>\n";
			echo "<select name='matiere_nouvel_enseignement' id='matiere_nouvel_enseignement' onchange=\"document.getElementById('creer_enseignement').checked=true;maj_prof_enseignement();maj_nom_descr_enseignement();\">\n";
			echo "<option value=''>---</option>\n";
			while($lig_mat=mysqli_fetch_object($res_mat)) {
				echo "<option value='$lig_mat->matiere' title=\"$lig_mat->matiere ($lig_mat->nom_complet)\" nom_matiere=\"$lig_mat->nom_complet\">".htmlspecialchars($lig_mat->nom_complet)."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td>\n";
			echo "Coefficient&nbsp;: ";
			echo "</td>\n";
			echo "<td>\n";
			echo "<select name='coef_nouvel_enseignement' onchange=\"document.getElementById('creer_enseignement').checked=true;\">";
			echo "<option value=''>---</option>\n";
			for($i=0;$i<20;$i++){
				echo "<option value='$i'>$i</option>\n";
			}
			echo "</select>\n";
			//echo "<span style='color:red'>A FAIRE: pas pris en compte pour le moment</span>";
			//echo "<br /><span style='color:red'>A FAIRE aussi: récupérer la catégorie associée à la matière dans 'matieres.categorie_id' et récupérer le matieres.nom_complet pour le nom du groupe</span>";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td>\n";
			echo "Nom&nbsp;: ";
			echo "</td>\n";
			echo "<td><input type='text' name='nom_nouvel_enseignement' id='nom_nouvel_enseignement' value='' />";

			$titre_infobulle="Ajouter un suffixe au nom de l'enseignement";
			$texte_infobulle="<div align='center' style='padding:3px;'>".html_ajout_suffixe_ou_renommer('nom_nouvel_enseignement', 'description_nouvel_enseignement', 'matiere_nouvel_enseignement')."</div>";
			$tabdiv_infobulle[]=creer_div_infobulle('suffixe_nom_grp',$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
			echo " <a href=\"javascript:afficher_div('suffixe_nom_grp','y',-100,20)\"><img src='../images/icons/wizard.png' width='16' height='16' alt='Suffixe' title=\"Ajouter un suffixe ou renommer l'enseignement.\" /></a>";

			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td>\n";
			echo "Description&nbsp;: ";
			echo "</td>\n";
			echo "<td>\n";
			echo "<div id='div_description_nouvel_enseignement' style='display:none;'></div>\n";
			echo "<input type='text' name='description_nouvel_enseignement' id='description_nouvel_enseignement' value='' />\n";
			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td style='vertical-align:top'>\n";
			echo "Visibilité&nbsp;: ";
			echo "</td>\n";
			echo "<td>\n";

				echo "<table class='boireaus'>\n";
				echo "<tr>\n";
				for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
					echo "<th title=\"Visibilité : ".$tab_domaines_texte[$loop]."\">\n";
					echo $tab_domaines_sigle[$loop];
					echo "</th>\n";
				}
				echo "</tr>\n";
				echo "<tr class='lig-1'>\n";
				for($loop=0;$loop<count($tab_domaines_sigle);$loop++) {
					echo "<td title=\"Visibilité : ".$tab_domaines_texte[$loop]."\">\n";
					echo "<input type='checkbox' name='nouvel_enseignement_visibilite[]' value='$tab_domaines[$loop]' checked />\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
				echo "</table>\n";


			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";
			echo "<td style='vertical-align:top'>\n";
			echo "Mettre dans le groupe&nbsp;: ";
			echo "</td>\n";
			echo "<td>\n";

			echo "<input type='radio' name='nouvel_enseignement_eleves' id='nouvel_enseignement_eleves_tous' value='tous' checked /><label for='nouvel_enseignement_eleves_tous'>tous les élèves de la classe</label><br />\n";
			echo "<input type='radio' name='nouvel_enseignement_eleves' id='nouvel_enseignement_eleves_aucun' value='aucun' /><label for='nouvel_enseignement_eleves_aucun'>aucun élève</label><br />\n";
			echo "<input type='radio' name='nouvel_enseignement_eleves' id='nouvel_enseignement_eleves_1' value='1' /><label for='nouvel_enseignement_eleves_1'>la première moitié de la classe</label><br />\n";
			echo "<input type='radio' name='nouvel_enseignement_eleves' id='nouvel_enseignement_eleves_2' value='2' /><label for='nouvel_enseignement_eleves_2'>la deuxième moitié de la classe</label><br />\n";

			echo "</td>\n";
			echo "</tr>\n";

			echo "<tr>\n";
			echo "<td colspan='2'>&nbsp;&nbsp;&nbsp;</td>\n";

			echo "<td>\n";
			echo "Professeur&nbsp;: ";
			echo "</td>\n";

			echo "<td id='td_prof_nouvel_enseignement'>\n";
			echo "<span id='span_prof_nouvel_enseignement'>";
			// Pour fonctionner sans JavaScript:
			$sql="SELECT u.login, u.nom, u.prenom FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif';";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_prof)==0) {
				echo "&nbsp;";
			}
			else {
				echo "<select name='professeur_nouvel_enseignement'>\n";
				if(mysqli_num_rows($res_prof)>0) {
					while($lig_prof=mysqli_fetch_object($res_prof)) {
						echo "<option value='$lig_prof->login'>".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2')."</option>\n";
					}
				}
				echo "</select>";
			}
			echo "</span><br />\n";

			echo "<input type='checkbox' name='declarer_pp_professeur_nouvel_enseignement' id='declarer_pp_professeur_nouvel_enseignement' value='y' /><label for='declarer_pp_professeur_nouvel_enseignement'> Déclarer le ou les ".getSettingValue('gepi_prof_suivi')." professeur(s) de cet enseignement.</label>";

			echo "<script type='text/javascript'>
				// <![CDATA[

				// Au chargement, on vide le champ de choix du prof pour ne proposer que les profs de la matière, une fois une matière choisie
				if(document.getElementById('span_prof_nouvel_enseignement')) {
					document.getElementById('span_prof_nouvel_enseignement').innerHTML='Choisissez d\'abord une matière.';
				}

				function maj_prof_enseignement() {
					matiere=document.getElementById('matiere_nouvel_enseignement').value;
					new Ajax.Updater($('span_prof_nouvel_enseignement'),'classes_ajax_lib.php?mode=classes_param&matiere='+matiere,{method: 'get'});

					//maj_nom_descr_enseignement();
				}

				function maj_nom_descr_enseignement() {
					matiere=document.getElementById('matiere_nouvel_enseignement').value;

					document.getElementById('nom_nouvel_enseignement').value=matiere;

					new Ajax.Updater($('div_description_nouvel_enseignement'),'../matieres/matiere_ajax_lib.php?champ=nom_complet&matiere='+matiere,{method: 'get'});
					//document.getElementById('description_nouvel_enseignement').value=document.getElementById('div_description_nouvel_enseignement').innerHTML;
					setTimeout(\"document.getElementById('description_nouvel_enseignement').value=document.getElementById('div_description_nouvel_enseignement').innerHTML\", 1000);
				}
				//]]>
			</script>\n";

			echo "</td>\n";
		}
	?>
</tr>
</table>

<?php
	$titre="Recalcul des rangs";
	$texte="<p>Un utilisateur a rencontré un jour le problème suivant&nbsp;:<br />Le rang était calculé pour les enseignements, mais pas pour le rang général de l'élève.<br />Ce lien permet de forcer le recalcul des rangs pour les enseignements comme pour le rang général.<br />Le recalcul sera effectué lors du prochain affichage de bulletin ou de moyennes.</p>";
	$tabdiv_infobulle[]=creer_div_infobulle('recalcul_rang',$titre,"",$texte,"",25,0,'y','y','n','n');
?>

<table border='0' cellspacing='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td><input type='checkbox' name='forcer_recalcul_rang' id='forcer_recalcul_rang' value='y' /><label for='forcer_recalcul_rang'>Forcer le recalcul des rangs</label> <a href='#' onclick="afficher_div('recalcul_rang','y',-100,20);return false;"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Forcer le recalcul des rangs' title='Forcer le recalcul des rangs' /></a>.</td>
</tr>
</table>

<style type='text/css'>
tr:hover {
	background-color:white;
}
td {
	vertical-align:top;
}
</style>
<br />
<table border='0' cellspacing='0'>
<tr>
	<td colspan='3'>
	<a name='parametres_generaux'></a>
	<h2><b>Paramètres généraux&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold;"-->
	<td>
	Afficher les rubriques de matières sur le bulletin (HTML),<br />les relevés de notes (HTML), et les outils de visualisation&nbsp;:
	</td>
	<td>
	<?php
		//echo "<input type='checkbox' value='y' name='display_mat_cat_".$per."' />\n";
		echo "<input type='radio' value='y' name='display_mat_cat_".$per."' />Oui\n";
		echo "<input type='radio' value='n' name='display_mat_cat_".$per."' />Non\n";
	?>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold;" valign="top"-->
	<td valign="top">
	Paramétrage des catégories de matière pour cette classe<br />
	(<i>la prise en compte de ce paramètrage est conditionnée<br />
	par le fait de cocher la case<br />
	'Afficher les rubriques de matières...' ci-dessus</i>)
	</td>
	<td>
		<table style='border: 1px solid black;'>
		<tr>
			<td style='width: auto; vertical-align:middle;'>Catégorie</td><td style='width: 100px; text-align: center; vertical-align:middle;'>Priorité d'affichage</td><td style='width: 100px; text-align: center; vertical-align:middle;'>Afficher la moyenne sur le bulletin</td>
		</tr>
		<?php
		$max_priority_cat=0;
		$get_max_cat = mysqli_query($GLOBALS["mysqli"], "SELECT priority FROM matieres_categories ORDER BY priority DESC LIMIT 1");
		if(mysqli_num_rows($get_max_cat)>0) {
			$max_priority_cat=old_mysql_result($get_max_cat, 0, "priority");
		}
		$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, priority FROM matieres_categories");
		while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
			$current_priority = $row["priority"];
			$current_affiche_moyenne = "0";

			echo "<tr>\n";
			echo "<td style='padding: 5px;'>".$row["nom_court"]."</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
			echo "<select name='priority_".$row["id"]."_".$per."' size='1'>\n";
			echo "<option value=''>---</option>\n";
			for ($i=0;$i<max(100,$max_priority_cat);$i++) {
				echo "<option value='$i'";
				//if ($current_priority == $i) echo " SELECTED";
				echo ">$i</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
			echo "<input type='checkbox' name='moyenne_".$row["id"]."_".$per."'";
			//if ($current_affiche_moyenne == '1') echo " CHECKED";
			echo " />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		?>
		</table>
	</td>
	</tr>

	<tr>
		<td colspan='3'>
			<h2><b>Paramètres généraux des bulletins&nbsp;: </b></h2>
		</td>
	</tr>
	<tr>
		<td><input type='checkbox' name='modifier_bull_prefixe_periode_<?php echo $per;?>' id='modifier_bull_prefixe_periode_<?php echo $per;?>' value='y' /></td>
		<td style="font-variant: small-caps; width: 35%;" colspan='2'>
			<label for='modifier_bull_prefixe_periode_<?php echo $per;?>'>Modifier le préfixe du titre du bulletin</label>
		</td>
	</tr>
	<tr>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td style="font-variant: small-caps; width: 35%;">
			Préfixe du titre du bulletin&nbsp;:<br />
			(<em style="font-variant: small-caps;">Par défaut, on a "<strong>Bulletin du </strong>" suivi du nom de la période</em>)
		</td>
		<td><?php 
				echo "
			<input type='text' name='bull_prefixe_periode_".$per."' id='bull_prefixe_periode_".$per."' value=\"Bulletin du \" onchange=\"document.getElementById('modifier_bull_prefixe_periode_".$per."').checked=true;changement()\" />";
			?>
		</td>
	</tr>

	<tr>
	<td colspan='3'>
	<h2><b>Paramètres bulletin HTML&nbsp;: </b></h2>
	</td>
	<td>
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
		Afficher sur le bulletin le rang de chaque élève&nbsp;: 
	</td>
	<td valign="bottom">
		<input type="radio" name="<?php echo "display_rang_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "display_rang_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	Afficher le bloc adresse du responsable de l'élève&nbsp;: 
	</td>
	<td valign="bottom">
		<input type="radio" name="<?php echo "display_address_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "display_address_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	Afficher les coefficients des matières<br />(<i>uniquement si au moins un coef différent de 0</i>)&nbsp;: 
	</td>
	<td valign="bottom">
		<input type="radio" name="<?php echo "display_coef_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "display_coef_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	Afficher les moyennes générales sur les bulletins<br />(<i>uniquement si au moins un coef différent de 0</i>)&nbsp;: 
	</td>
	<td valign="bottom">
		<input type="radio" name="<?php echo "display_moy_gen_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "display_moy_gen_".$per; ?>" value="n" />Non
	</td>
	</tr>

	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td valign="top">
	Afficher sur le bulletin le nombre de devoirs&nbsp;: 
	</td>
	<td valign="bottom">
		<input type="radio" name="<?php echo "display_nbdev_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "display_nbdev_".$per; ?>" value="n" />Non
	</td>
	</tr>
	<tr>
	<td colspan='3'>
	<h2><b>Paramètres bulletin PDF&nbsp;: </b></h2>
	</td>
	<td>
	</td>
	</tr>
	<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
	Sélectionner le modèle de bulletin pour l'impression en PDF&nbsp;:
	</td>
	<td><?php
		echo "<select tabindex=\"5\" name=\"modele_bulletin_".$per."\">";
		// sélection des modèle des bulletins.
		//$requete_modele = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
		$requete_modele = mysqli_query($GLOBALS["mysqli"], "SELECT id_model_bulletin, valeur as nom_model_bulletin FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY ".$prefix_base."modele_bulletin.valeur ASC;");
		echo "<option value=\"0\">Aucun changement</option>";
		while($donner_modele = mysqli_fetch_array($requete_modele)) {
			echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
			echo ">".ucfirst($donner_modele['nom_model_bulletin'])."</option>\n";
		}
		echo "</select>\n";
		?>
	</td>
</tr>


<!-- ========================================= -->
<tr>
	<td colspan='3'>
	<h2><b>Paramètres des relevés de notes&nbsp;: </b></h2>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Type de relevé à produire par défaut&nbsp;:<br />
		<em style='font-size:small'>(si plusieurs classes sont sélectionnées, c'est le type de la première qui est proposé par défaut)</em>
	</td>
	<td>
		<input type="radio" value="html" name="rn_type_par_defaut_<?php echo $per;?>" id="rn_type_par_defaut_html" onchange='changement()' /><label for='rn_type_par_defaut_html' style='cursor: pointer;'>HTML</label><br />
		<input type="radio" value="pdf" name="rn_type_par_defaut_<?php echo $per;?>" id="rn_type_par_defaut_pdf" onchange='changement()' /><label for='rn_type_par_defaut_pdf' style='cursor: pointer;'>PDF</label>
	</td>
</tr>

<!-- ================================================================= -->
<!-- 20121027 -->
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Affichage du nom de la classe sur le relevé&nbsp;:
	</td>
	<td>
		<input type="radio" value="1" name="rn_aff_classe_nom_<?php echo $per;?>" id="rn_aff_classe_nom_1" onchange='changement()' /><label for='rn_aff_classe_nom_1' style='cursor: pointer;'>Nom long</label><br />
		<input type="radio" value="2" name="rn_aff_classe_nom_<?php echo $per;?>" id="rn_aff_classe_nom_2" onchange='changement()' /><label for='rn_aff_classe_nom_2' style='cursor: pointer;'>Nom court</label><br />
		<input type="radio" value="3" name="rn_aff_classe_nom_<?php echo $per;?>" id="rn_aff_classe_nom_3" onchange='changement()' /><label for='rn_aff_classe_nom_3' style='cursor: pointer;'>Nom court (Nom long)</label><br />
	</td>
</tr>
<!-- ================================================================= -->

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher le nom des devoirs&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_nomdev_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_nomdev_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher tous les coefficients des devoirs&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_toutcoefdev_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_toutcoefdev_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher les coefficients des devoirs si des coefficients différents sont présents&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_coefdev_si_diff_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_coefdev_si_diff_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher les dates des devoirs&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_datedev_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_datedev_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher les absences (ABS2 et relevé HTML)&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_abs_2_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_abs_2_".$per; ?>" value="n" />Non
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Formule/Message à insérer sous le relevé de notes&nbsp;:</td>
	<td><input type=text size=40 name="rn_formule_<?php echo $per;?>" value="" /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher une case pour la signature du chef d'établissement&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_sign_chefetab_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_sign_chefetab_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher une case pour la signature du prof principal&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_sign_pp_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_sign_pp_".$per; ?>" value="n" />Non
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Afficher une case pour la signature des parents/responsables&nbsp;:</td>
	<td>
		<input type="radio" name="<?php echo "rn_sign_resp_".$per; ?>" value="y" />Oui
		<input type="radio" name="<?php echo "rn_sign_resp_".$per; ?>" value="n" />Non
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Nombre de lignes pour la signature&nbsp;:</td>
	<td><input type="text" name="rn_sign_nblig_<?php echo $per;?>" value="" size="3" /> (<em>par défaut, c'est 3</em>)</td>
</tr>

<!-- ================================================================= -->
<!-- 20121027 -->
<!-- A MODIFIER EN CAS DE MODE CNIL STRICT -->
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Afficher l'appréciation/commentaire du professeur<br />(<em>sous réserve d'autorisation par le professeur dans les paramètres du devoir</em>)&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_app_<?php echo $per;?>" id="rn_app_y" onchange='changement()' /><label for='rn_app_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_app_<?php echo $per;?>" id="rn_app_n" onchange='changement()' /><label for='rn_app_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Avec la colonne moyenne (<em title="Moyenne du carnet de notes :
Notez que tant que la période n'est pas close, cette moyenne peut évoluer
(ajout de notes, modifications de coefficients,...)">du CN</em>) de l'élève&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_col_moy_<?php echo $per;?>" id="rn_col_moy_y" onchange='changement()' /><label for='rn_col_moy_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_col_moy_<?php echo $per;?>" id="rn_col_moy_n" onchange='changement()' /><label for='rn_col_moy_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Avec la moyenne de la classe pour chaque devoir&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_moy_classe_<?php echo $per;?>" id="rn_moy_classe_y" onchange='changement()' /><label for='rn_moy_classe_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_moy_classe_<?php echo $per;?>" id="rn_moy_classe_n" onchange='changement()' /><label for='rn_moy_classe_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Avec les moyennes min/classe/max de chaque devoir&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_moy_min_max_classe_<?php echo $per;?>" id="rn_moy_min_max_classe_y" onchange='changement()' /><label for='rn_moy_min_max_classe_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_moy_min_max_classe_<?php echo $per;?>" id="rn_moy_min_max_classe_n" onchange='changement()' /><label for='rn_moy_min_max_classe_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Avec retour à la ligne après chaque devoir si on affiche le nom du devoir ou le commentaire&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_retour_ligne_<?php echo $per;?>" id="rn_retour_ligne_y" onchange='changement()' /><label for='rn_retour_ligne_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="y" name="rn_retour_ligne_<?php echo $per;?>" id="rn_retour_ligne_n" onchange='changement()' /><label for='rn_retour_ligne_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>


<?php
	$titre_infobulle="Rapport taille polices\n";
	$texte_infobulle="<p>Pour que la liste des devoirs tienne dans la cellule, on réduit la taille de la police.<br />Pour que cela reste lisible, vous pouvez fixer ici une taille minimale en dessous de laquelle ne pas descendre.</p><br /><p>Si la taille minimale ne suffit toujours pas à permettre l'affichage dans la cellule, on supprime les retours à la ligne.</p><br /><p>Et cela ne suffit toujours pas, le texte est tronqué (<em>dans ce cas, un relevé HTML pourra permettre l'affichage (les hauteurs de cellules s'adaptent à la quantité de texte... L'inconvénient&nbsp;: Une matière peut paraître plus importante qu'une autre par la place qu'elle occupe)</em>).</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('a_propos_rapport_tailles_polices',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
?>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Rapport taille_standard / taille_minimale_de_police (<em>relevé PDF</em>) <?php
		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_rapport_tailles_polices','y',100,-50);\" onmouseout=\"cacher_div('a_propos_rapport_tailles_polices');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Bloc observations en PDF'/></a>";
	?>&nbsp;:
	</td>
	<td><input type="text" name="rn_rapport_standard_min_font_<?php echo $per;?>" size="3" value="" onchange='changement()' /> (<em>par défaut, c'est 3</em>)</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Afficher le bloc adresse du responsable de l'élève&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_adr_resp_<?php echo $per;?>" id="rn_adr_resp_y" onchange='changement()' /><label for='rn_adr_resp_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_adr_resp_<?php echo $per;?>" id="rn_adr_resp_n" onchange='changement()' /><label for='rn_adr_resp_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>

<?php
	$titre_infobulle="Bloc observations en PDF\n";
	$texte_infobulle="<p>Le bloc observations est affiché si une des conditions suivantes est remplie&nbsp;:</p>\n";
	$texte_infobulle.="<ul>\n";
	$texte_infobulle.="<li>La case Bloc observations est cochée.</li>\n";
	$texte_infobulle.="<li>Une des cases signature est cochée.</li>\n";
	$texte_infobulle.="</ul>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('a_propos_bloc_observations',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
?>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Afficher le bloc observations (<em>relevé PDF</em>) <?php
		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_bloc_observations','y',100,-50);\"  onmouseout=\"cacher_div('a_propos_bloc_observations');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Bloc observations en PDF'/></a>";
	?>&nbsp;:
	</td>
	<td>
		<input type="radio" value="y" name="rn_bloc_obs_<?php echo $per;?>" id="rn_bloc_obs_y" onchange='changement()' /><label for='rn_bloc_obs_y' style='cursor: pointer;'>Oui</label> 
		<input type="radio" value="n" name="rn_bloc_obs_<?php echo $per;?>" id="rn_bloc_obs_n" onchange='changement()' /><label for='rn_bloc_obs_n' style='cursor: pointer;'>Non</label>
	</td>
</tr>
<!-- ================================================================= -->



<?php
if ($gepiSettings['active_mod_ects'] == "y") {
    ?>
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres des attestations ECTS&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Type de formation (ex: "Classe préparatoire scientifique")&nbsp;:</td>
    <td><input type="text" size="40" name="ects_type_formation_<?php echo $per;?>" value="" /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Nom complet du parcours de formation (ex: "BCPST (Biologie, Chimie, Physique et Sciences de la Terre)")&nbsp;:</td>
    <td><input type="text" size="40" name="ects_parcours_<?php echo $per;?>" value="" /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Nom cours du parcours de formation (ex: "BCPST")&nbsp;:</td>
    <td><input type="text" size="40" name="ects_code_parcours_<?php echo $per;?>" value="" /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Domaines d'étude (ex: "Biologie, Chimie, Physique, Mathématiques, Sciences de la Terre")&nbsp;:</td>
    <td><input type="text" size="40" name="ects_domaines_etude_<?php echo $per;?>" value="" /></td>
</tr>
<?php
} else {
?>
<input type="hidden" name="ects_type_formation_<?php echo $per;?>" value="" />
<input type="hidden" name="ects_parcours_<?php echo $per;?>" value="" />
<input type="hidden" name="ects_code_parcours_<?php echo $per;?>" value="" />
<input type="hidden" name="ects_domaines_etude_<?php echo $per;?>" value="" />
<input type="hidden" name="ects_fonction_signataire_attestation_<?php echo $per;?>" value="" />
<?php } ?>
</table>
<hr />
<?php

	}
}


?>

<center><input type='submit' name='enregistrer2' value='Enregistrer' /></center>
<input type=hidden name='is_posted' value="yes" />
</form>

<p><br /></p>

<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Les cases ne sont pas cochées par défaut.<br />
Comme vous pouvez modifier la liste des classes concernées par le paramétrage par lots, il n'est pas possible de pré-cocher l'état actuel du paramétrage des classes.<br />
Tout ce que vous cocherez correspondra aux modifications que vous souhaitez apporter.</p>

<?php require("../lib/footer.inc.php");?>
