<?php
/*
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$periode_query = mysql_query("select max(num_periode) max from periodes");
$max_periode = mysql_result($periode_query, 0, 'max');

if (isset($_POST['is_posted'])) {
	check_token();
	$msg = '';
	$reg_ok = '';
	// Première boucle sur le nombre de periodes
	$per = 0;
	while ($per < $max_periode) {
		$per++;
		// On dresse la liste de toutes les classes non virtuelles
		$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
		$nb_classe = mysql_num_rows($classes_list);
		// $nb : nombre de classes ayant un nombre de periodes égal à $per
		$nb=0;
		$nbc = 0;
		while ($nbc < $nb_classe) {
			$modif_classe = 'no';
			$id_classe = mysql_result($classes_list,$nbc,'id');
			$query_per = mysql_query("SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
			$nb_periode = mysql_num_rows($query_per);
			if ($nb_periode == $per) {
				// la classe dont l'identifiant est $id_classe a $per périodes
				$temp = "case_".$id_classe;
				if (isset($_POST[$temp])) {
					$k = '1';
					While ($k < $per+1) {
						$temp2 = "nb_".$per."_".$k;
						if ($_POST[$temp2] != '') {
							$register = mysql_query("UPDATE periodes SET nom_periode='".$_POST[$temp2]."' where (id_classe='".$id_classe."' and num_periode='".$k."')");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
						$k++;
					}
					$temp2 ="nb_".$per."_reg_suivi_par";
					if ($_POST[$temp2] != '') {
						$register = mysql_query("UPDATE classes SET suivi_par='".$_POST[$temp2]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_suivi_par".$per." : ".$_POST[$temp2]."</br>";
					}
					$temp2 = "nb_".$per."_reg_formule";
					if ($_POST[$temp2] != '') {
						//$register = mysql_query("UPDATE classes SET formule='".$_POST[$temp2]."' where id='".$id_classe."'");
						$register = mysql_query("UPDATE classes SET formule='".html_entity_decode($_POST[$temp2])."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - reg_formule".$per." : ".$_POST[$temp2]."</br>";
					}
					if (isset($_POST['nb_'.$per.'_reg_format'])) {
						$tab = explode("_", $_POST['nb_'.$per.'_reg_format']);
						$register = mysql_query("UPDATE classes SET format_nom='".$tab[2]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
//                        echo "classe : ".$id_classe." - ".$_POST['nb_'.$per.'_reg_format']."</br>";
					}
					if (isset($_POST['display_rang_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_rang='".$_POST['display_rang_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					//====================================
					// AJOUT: boireaus
					if (isset($_POST['display_address_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_address='".$_POST['display_address_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					if (isset($_POST['display_coef_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_coef='".$_POST['display_coef_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}
					if (isset($_POST['display_nbdev_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_nbdev='".$_POST['display_nbdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if (isset($_POST['display_moy_gen_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_moy_gen='".$_POST['display_moy_gen_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}


					if (isset($_POST['display_mat_cat_'.$per])) {
						$register = mysql_query("UPDATE classes SET display_mat_cat='".$_POST['display_mat_cat_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if ((isset($_POST['modele_bulletin_'.$per])) AND ($_POST['modele_bulletin_'.$per]!=0)) {
						$register = mysql_query("UPDATE classes SET modele_bulletin_pdf='".$_POST['modele_bulletin_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_nomdev_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_nomdev='".$_POST['rn_nomdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_toutcoefdev_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_toutcoefdev='".$_POST['rn_toutcoefdev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_coefdev_si_diff_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_coefdev_si_diff='".$_POST['rn_coefdev_si_diff_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_datedev_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_datedev='".$_POST['rn_datedev_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_abs_2_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_abs_2='".$_POST['rn_abs_2_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_chefetab_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_sign_chefetab='".$_POST['rn_sign_chefetab_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_pp_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_sign_pp='".$_POST['rn_sign_pp_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if (isset($_POST['rn_sign_resp_'.$per])) {
						$register = mysql_query("UPDATE classes SET rn_sign_resp='".$_POST['rn_sign_resp_'.$per]."' where id='".$id_classe."'");
						if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
					}

					if($_POST['rn_sign_nblig_'.$per]!="") {
						if(mb_strlen(my_ereg_replace("[0-9]","",$_POST['rn_sign_nblig_'.$per]))!=0){$_POST['rn_sign_nblig_'.$per]=3;}

						if (isset($_POST['rn_sign_nblig_'.$per])) {
							$register = mysql_query("UPDATE classes SET rn_sign_nblig='".$_POST['rn_sign_nblig_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					if (isset($_POST['rn_formule_'.$per])) {
						if ($_POST['rn_formule_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET rn_formule='".$_POST['rn_formule_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					if (isset($_POST['ects_fonction_signataire_attestation_'.$per])) {
						if ($_POST['ects_fonction_signataire_attestation_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET ects_fonction_signataire_attestation='".$_POST['ects_fonction_signataire_attestation_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					if (isset($_POST['ects_type_formation_'.$per])) {
						if ($_POST['ects_type_formation_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET ects_type_formation='".$_POST['ects_type_formation_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_parcours_'.$per])) {
						if ($_POST['ects_parcours_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET ects_parcours='".$_POST['ects_parcours_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_domaines_etude_'.$per])) {
						if ($_POST['ects_domaines_etude_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET ects_domaines_etude='".$_POST['ects_domaines_etude_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

                    if (isset($_POST['ects_code_parcours_'.$per])) {
						if ($_POST['ects_code_parcours_'.$per]!='') {
							$register = mysql_query("UPDATE classes SET ects_code_parcours='".$_POST['ects_code_parcours_'.$per]."' where id='".$id_classe."'");
							if (!$register) $reg_ok = 'no'; else $reg_ok = 'yes' ;
						}
					}

					// On enregistre les infos relatives aux catégories de matières
					$nb_modif_priorite=0;
					$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
					while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
						$reg_priority = $_POST['priority_'.$row["id"].'_'.$per];
						if (isset($_POST['moyenne_'.$row["id"].'_'.$per])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
						if (!is_numeric($reg_priority)) $reg_priority = 0;
						if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;
						$test = mysql_result(mysql_query("select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')"), 0);
						if ($test == 0) {
							// Pas d'entrée... on créé
							$res = mysql_query("INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");
						} else {
							// Entrée existante, on met à jour
							$res = mysql_query("UPDATE j_matieres_categories_classes SET priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "' WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $row["id"] . "')");
						}
						if (!$res) {
							$msg .= "<br />Une erreur s'est produite lors de l'enregistrement des données de catégorie.";
						}
						else {
							$nb_modif_priorite++;
						}
					}


					if((isset($_POST['change_coef']))&&($_POST['change_coef']=='y')) {
						if((isset($_POST['coef_enseignements']))&&($_POST['coef_enseignements']!="")) {
							$coef_enseignements=my_ereg_replace("[^0-9]","",$_POST['coef_enseignements']);
							if($coef_enseignements!="") {
								$sql="UPDATE j_groupes_classes SET coef='".$coef_enseignements."' WHERE id_classe='".$id_classe."';";
								$update_coef=mysql_query($sql);
								if(!$update_coef) {
									$msg .= "<br />Une erreur s'est produite lors de la mise à jour des coefficients pour la classe $id_classe.";
								}
							}
						}
					}

					if((isset($_POST['forcer_recalcul_rang']))&&($_POST['forcer_recalcul_rang']=='y')) {
						$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC LIMIT 1;";
						$res_per=mysql_query($sql);
						if(mysql_num_rows($res_per)>0) {
							$lig_per=mysql_fetch_object($res_per);
							$recalcul_rang="";
							for($i=0;$i<$lig_per->num_periode;$i++) {$recalcul_rang.="y";}
							$sql="UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
							//echo "$sql<br />";
							$res=mysql_query($sql);
							if(!$res) {
								$msg.="<br />Erreur lors de la programmation du recalcul des rangs pour la classe ".get_nom_classe($id_classe).".";
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
							$verif=mysql_query($sql);
							if(mysql_num_rows($verif)==0) {
								$msg .= "<br />La matière $matiere_nouvel_enseignement n'existe pas.";
							}
							else {
								$coef_nouvel_enseignement=isset($_POST['coef_nouvel_enseignement']) ? $_POST['coef_nouvel_enseignement'] : 0;
								$coef_nouvel_enseignement=my_ereg_replace("[^0-9]","",$_POST['coef_nouvel_enseignement']);

								$professeur_nouvel_enseignement=isset($_POST['professeur_nouvel_enseignement']) ? $_POST['professeur_nouvel_enseignement'] : NULL;
								$professeur_nouvel_enseignement=my_ereg_replace("[^A-Za-z0-9._-]","",$professeur_nouvel_enseignement);
								if($professeur_nouvel_enseignement!="") {
									$sql="SELECT 1=1 FROM utilisateurs u WHERE u.login='$professeur_nouvel_enseignement';";
									$verif=mysql_query($sql);
									if(mysql_num_rows($verif)==0) {
										$professeur_nouvel_enseignement="";
									}

									$sql="SELECT 1=1 FROM j_professeurs_matieres jpm WHERE jpm.id_professeur='$professeur_nouvel_enseignement' AND jpm.id_matiere='$matiere_nouvel_enseignement'";
									$verif=mysql_query($sql);
									if(mysql_num_rows($verif)==0) {
										$professeur_nouvel_enseignement="";
									}
								}

								$reg_clazz = array();
								$reg_clazz[] = $id_classe;
								$reg_categorie = 1; // Récupérer par la suite la catégorie par défaut de la table 'matieres' (champ categorie_id)
								$reg_nom_groupe=$matiere_nouvel_enseignement; // Obtenir une unicité...?
								$reg_nom_complet=$matiere_nouvel_enseignement; // Obtenir une unicité...?

								$sql="SELECT nom_complet,categorie_id FROM matieres WHERE matiere='$matiere_nouvel_enseignement';";
								$res_mat=mysql_query($sql);
								if(mysql_num_rows($res_mat)>0) {
									$lig_mat=mysql_fetch_object($res_mat);
									$reg_categorie=$lig_mat->categorie_id;
									$reg_nom_complet=$lig_mat->nom_complet;
								}
								$reg_matiere=$matiere_nouvel_enseignement;
								$create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_categorie);
								if($create) {
									$current_group=get_group($create);

									$reg_professeurs = array();
									if($professeur_nouvel_enseignement!="") {
										$reg_professeurs[]=$professeur_nouvel_enseignement;
									}

									$reg_eleves=array();
									foreach ($current_group["periodes"] as $period) {
										$reg_eleves[$period['num_periode']]=array();
										$sql="SELECT login FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='".$period['num_periode']."';";
										$res_ele=mysql_query($sql);
										if(mysql_num_rows($res_ele)>0){
											while($lig_ele=mysql_fetch_object($res_ele)){
												$reg_eleves[$period['num_periode']][]=$lig_ele->login;
											}
										}
									}

									$res = update_group($create, $reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz, $reg_professeurs, $reg_eleves);

									if($coef_nouvel_enseignement!="") {
										$sql="UPDATE j_groupes_classes SET coef='$coef_nouvel_enseignement' WHERE id_groupe='$create' AND id_classe='$id_classe';";
										$res_coef=mysql_query($sql);
										if(!$res_coef) {
											$msg .= "<br />Erreur lors de la mise à jour du coefficient du groupe n°$create pour la classe n°$id_classe.";
										}
									}
								}
							}

						}
					}


					//====================================
				}
			}
			$nbc++;
		}
	}

	if (($reg_ok=='')&&($nb_modif_priorite==0)) {
		$message_enregistrement = "Aucune modification n'a été effectuée !";
		$affiche_message = 'yes';
	} elseif(($reg_ok=='')&&($nb_modif_priorite>0)) {
		$message_enregistrement = "Les modifications ont été effectuées avec succès.";
		$affiche_message = 'yes';
	} else if ($reg_ok=='yes') {
		$message_enregistrement = "Les modifications ont été effectuées avec succès.";
		$affiche_message = 'yes';
	}
	else {
		$message_enregistrement = "Il y a eu un problème lors de l'enregistrement des modification.";
		$affiche_message = 'yes';
	}
}

//**************** EN-TETE *****************
$titre_page = "Gestion des classes - Paramétrage des classes par lots";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

If ($max_periode <= 0) {
echo "Aucune classe comportant des périodes n'a été définie.";
die();
}
echo "<form action=\"classes_param.php\" method='post'>\n";
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

// Première boucle sur le nombre de periodes
$per = 0;
while ($per < $max_periode) {
	$per++;
	// On dresse la liste de toutes les classes non virtuelles
	$classes_list = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id ORDER BY classe");
	$nb_classe = mysql_num_rows($classes_list);
	// $nb : nombre de classes ayant un nombre de periodes égal à $per
	$nb=0;
	$nbc = 0;
	while ($nbc < $nb_classe) {
		$id_classe = mysql_result($classes_list,$nbc,'id');
		$query_per = mysql_query("SELECT p.num_periode FROM classes c, periodes p WHERE (p.id_classe = c.id  and c.id = '".$id_classe."')");
		$nb_periode = mysql_num_rows($query_per);
		if ($nb_periode == $per) {
			$tab_id_classe[$nb] = $id_classe;
			$tab_nom_classe[$nb] = mysql_result($classes_list,$nbc,'classe');
			$nb++;
		}
		$nbc++;
	}
	If ($nb != 0) {
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
	}

</script>\n";



		?>
		<p class='bold'>Pour la ou les classe(s) sélectionnée(s) ci-dessus&nbsp;: </p>
		<p>Aucune modification ne sera apportée aux champs laissés vides</p>

		<table width=100% border=2 cellspacing=1  cellpadding=3 class='boireaus'>
		<tr>
		<th>&nbsp;</th>
		<th>Nom de la période</th>
		</tr>

		<?php
		$k = '1';
		$alt=1;
		While ($k < $per+1) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<th>Période ".$k."</th>\n";
			echo "<td><input type='text' name='nb_".$per."_".$k."' value=\"\" size='30' /></td>\n";
			echo"</tr>\n";
			$k++;
		}

		?>

		</table>
		<p>Prénom et nom du signataire des bulletins<?php if ($gepiSettings['active_mod_ects'] == "y") echo " et des attestations ECTS" ?> (chef d'établissement ou son représentant)&nbsp;:
		<br /><input type="text" size="30" name="<?php echo "nb_".$per."_reg_suivi_par"; ?>" value="" /></p>
        <?php if ($gepiSettings['active_mod_ects'] == "y") { ?>
            <p>Fonction du signataire sus-nommé (ex.: "Proviseur")&nbsp;: <br /><input type="text" size="40" name="ects_fonction_signataire_attestation_<?php echo $per;?>" value="" /></p>
        <?php } ?>
		<p>Formule à insérer sur les bulletins (cette formule sera suivie des nom et prénom de la personne désignée ci_dessus&nbsp;:
		<br /><input type="text" size="80" name="<?php echo "nb_".$per."_reg_formule"; ?>" value="" /></p>
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
<br />

<h2><b>Enseignements</b></h2>
<table border='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold;"-->
	<td>
	<input type='checkbox' name='change_coef' id='change_coef' value='y' /> Passer les coefficients de tous les enseignements à&nbsp;:
	</td>
	<td>
	<select name='coef_enseignements' onchange="document.getElementById('change_coef').checked=true">
	<?php
	echo "<option value=''>---</option>\n";
	for($i=0;$i<10;$i++){
		echo "<option value='$i'>$i</option>\n";
	}
	?>
	</select>
	</td>
</tr>
</table>

<table border='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<!--td style="font-weight: bold; vertical-align:top;"-->
	<td style="vertical-align:top;">
	<input type='checkbox' name='creer_enseignement' id='creer_enseignement' value='y' /> Créer un enseignement de&nbsp;:
	</td>
	<?php
		$sql="SELECT DISTINCT matiere,nom_complet FROM matieres ORDER BY nom_complet,matiere;";
		$res_mat=mysql_query($sql);
		if(mysql_num_rows($res_mat)==0) {
			echo "<td>Aucune matière n'est encore créée.</td>\n";
		}
		else {
			echo "<td colspan='2'>\n";
			echo "<select name='matiere_nouvel_enseignement' id='matiere_nouvel_enseignement' onchange=\"document.getElementById('creer_enseignement').checked=true;maj_prof_enseignement();\">\n";
			echo "<option value=''>---</option>\n";
			while($lig_mat=mysql_fetch_object($res_mat)) {
				echo "<option value='$lig_mat->matiere'>".htmlspecialchars($lig_mat->nom_complet)."</option>\n";
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
			for($i=0;$i<10;$i++){
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
			echo "Professeur&nbsp;: ";

			echo "<script type='text/javascript'>
				// <![CDATA[
				function maj_prof_enseignement() {
					matiere=document.getElementById('matiere_nouvel_enseignement').value;
					new Ajax.Updater($('td_prof_nouvel_enseignement'),'classes_ajax_lib.php?mode=classes_param&matiere='+matiere,{method: 'get'});
				}
				//]]>
			</script>\n";

			echo "</td>\n";
			echo "<td id='td_prof_nouvel_enseignement'>\n";
			echo "&nbsp;";
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

<table border='0'>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td><input type='checkbox' name='forcer_recalcul_rang' id='forcer_recalcul_rang' value='y' /><label for='forcer_recalcul_rang'>Forcer le recalcul des rangs</label> <a href='#' onclick="afficher_div('recalcul_rang','y',-100,20);return false;"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Forcer le recalcul des rangs' title='Forcer le recalcul des rangs' /></a>.</td>
</tr>
</table>

<br />
<table border='0'>
<tr>
	<td colspan='3'>
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
			<td style='width: auto;'>Catégorie</td><td style='width: 100px; text-align: center;'>Priorité d'affichage</td><td style='width: 100px; text-align: center;'>Afficher la moyenne sur le bulletin</td>
		</tr>
		<?php
		$max_priority_cat=0;
		$get_max_cat = mysql_query("SELECT priority FROM matieres_categories ORDER BY priority DESC LIMIT 1");
		if(mysql_num_rows($get_max_cat)>0) {
			$max_priority_cat=mysql_result($get_max_cat, 0, "priority");
		}
		$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
		while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
			$current_priority = $row["priority"];
			$current_affiche_moyenne = "0";

			echo "<tr>\n";
			echo "<td style='padding: 5px;'>".$row["nom_court"]."</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
			echo "<select name='priority_".$row["id"]."_".$per."' size='1'>\n";
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
		$requete_modele = mysql_query("SELECT id_model_bulletin, valeur as nom_model_bulletin FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY ".$prefix_base."modele_bulletin.valeur ASC;");
		echo "<option value=\"0\">Aucun changement</option>";
		while($donner_modele = mysql_fetch_array($requete_modele)) {
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
	<td><input type="text" name="rn_sign_nblig_<?php echo $per;?>" value="" size="3" /></td>
</tr>

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
<?php require("../lib/footer.inc.php");?>
