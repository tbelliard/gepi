<?php
/*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//$variables_non_protegees = 'yes';

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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/releve.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_examen_blanc/releve.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Examen blanc: Relevé',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}


//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

include('lib_exb.php');

$id_exam=isset($_POST['id_exam']) ? $_POST['id_exam'] : (isset($_GET['id_exam']) ? $_GET['id_exam'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);


$acces_mod_exb_prof="n";
if($_SESSION['statut']=='professeur') {

	if(!is_pp($_SESSION['login'])) {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if(getSettingValue('modExbPP')!='yes') {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if((isset($id_exam))&&(!is_pp_proprio_exb($id_exam))) {
		header("Location: ../accueil.php?msg=".rawurlencode("Vous n'êtes pas propriétaire de l'examen blanc n°$id_exam."));
		die();
	}

	$acces_mod_exb_prof="y";
}

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {

	if(isset($id_exam)) {
		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		//echo "$sql<br />\n";
		$res_test_id_exam=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test_id_exam)==0) {
			$msg="L'examen choisi (<i>$id_exam</i>) n'existe pas.<br />\n";
		}
		else {
			//===========================
			// Classes 
			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_classes=mysqli_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="<p>Aucune classe n'est associée à l'examen???</p>\n";
			}
			else {
				$tab_id_classe=array();
				$tab_classe=array();
				while($lig=mysqli_fetch_object($res_classes)) {
					$tab_id_classe[]=$lig->id_classe;
					$tab_classe[]=$lig->classe;
				}
			
				//===========================
				// Matières
				//$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere AND id_exam='$id_exam' ORDER BY em.ordre, m.matiere;";
				// Pour mettre les matières à bonus à la fin si aucun ordre n'a été défini
				$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere AND id_exam='$id_exam' ORDER BY em.ordre, em.bonus, m.matiere;";
				$res_matieres=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_matieres=mysqli_num_rows($res_matieres);
				if($nb_matieres==0) {
					$msg="<p>Aucune matière n'est associée à l'examen???</p>\n";
				}
				else {
					$tab_matiere=array();
					$tab_coef=array();
					$tab_bonus=array();
					while($lig=mysqli_fetch_object($res_matieres)) {
						$tab_matiere[]=$lig->matiere;
						$tab_coef[]=$lig->coef;
						$tab_bonus[]=$lig->bonus;
					}
					//===========================

					// Tableau des notes des élèves
					$tab_note=array();

					// Tableau des identifiants d'infobulle déjà créés (pour ne pas créer deux fois les mêmes infobulles)
					$tab_dev=array();
					$tab_bull=array();
					$tab_moy_plusieurs_periodes=array();
					$tab_moy_epb=array();

					// Tableau des paramètres des évaluations et épreuves blanches (pour ramener sur 20)
					$tab_dev_param=array();
					$tab_epb_param=array();

					for($i=0;$i<$nb_classes;$i++) {
						//echo "\$tab_id_classe[$i]=$tab_id_classe[$i]<br />";
						//echo "\$tab_classe[$i]=$tab_classe[$i]<br />";
						for($j=0;$j<$nb_matieres;$j++) {
							//$sql="SELECT * FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
							//$sql="SELECT eg.id_dev, eg.type, eg.valeur, eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
							$sql="SELECT eg.id AS id_ex_grp,eg.id_dev, eg.type, eg.valeur, eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
							//echo "$sql<br />\n";
							$res_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_groupe)>0) {
								while($lig_groupe=mysqli_fetch_object($res_groupe)) {

									if($lig_groupe->type=='moy_bull') {
										$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig_groupe->id_groupe' AND periode='$lig_groupe->valeur';";
										//echo "$sql<br />\n";
										$res_bull=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_bull)>0) {
											while($lig_bull=mysqli_fetch_object($res_bull)) {
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_bull->statut;
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_bull->note;

												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["infobulle"]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;

												if($lig_bull->statut=="") {
													if(!isset($tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
														$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
														$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]=0;
													}

													$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=$lig_bull->note;
													$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]++;
												}
											}

											if(!in_array('bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$tab_bull)) {
												$tab_bull[]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;

												$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig_groupe->valeur' AND id_classe='$tab_id_classe[$i]';";
												//echo "$sql<br />\n";
												$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
												$lig_per=mysqli_fetch_object($res_per);

												$titre="Moyenne du bulletin (<i>$lig_per->nom_periode</i>)";
												$texte="<p><b>Moyenne du bulletin sur la période $lig_per->nom_periode</b>";
												$texte.="<br />";

												$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
												//$tabdiv_infobulle[]=creer_div_infobulle('div_bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
											}

										}
									}
									elseif($lig_groupe->type=='') {
										// Si $lig_groupe->type est vide et si $lig_groupe->id_dev est nul, alors c'est que le choix du devoir n'est pas encore fait dans le paramétrage de l'examen blanc
										if($lig_groupe->id_dev!="0") {
											$note_sur=20;
											if(!array_key_exists($lig_groupe->id_dev, $tab_dev_param)) {
												$tab_dev_param[$lig_groupe->id_dev]=get_tab_infos_cn_devoir($lig_groupe->id_dev);
											}

											if(isset($tab_dev_param[$lig_groupe->id_dev]['note_sur'])) {
												$note_sur=$tab_dev_param[$lig_groupe->id_dev]['note_sur'];
											}
											else {
												if(!isset($msg)) {$msg="";}
												$msg.="Anomalie : Le devoir n°$lig_groupe->id_dev n'a pas été trouvé.<br />";
												//echo $msg;
											}

											if($note_sur==0) {
												$msg.="ANOMALIE&nbsp;: Le devoir n°$lig_groupe->id_dev serait noté sur zéro.<br />Référentiel ramené à 20.<br />";
												$note_sur=20;
											}

											$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev';";
											//echo "$sql<br />\n";
											$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
											if(mysqli_num_rows($res_dev)>0) {
												while($lig_dev=mysqli_fetch_object($res_dev)) {
													//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
													//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
													$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
													$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note*20/$note_sur;
													$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]=$lig_groupe->id_dev;

													if($lig_dev->statut=="") {
														if(!isset($tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
															$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
															$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]=0;
														}

														$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=20*$lig_dev->note/$note_sur;
														$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]++;
													}

												}

												if(!in_array($lig_groupe->id_dev,$tab_dev)) {
													$tab_dev[]=$lig_groupe->id_dev;
						
													$sql="SELECT cd.nom_court, cd.nom_complet, cd.description, cd.date, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_cahier_notes=cd.id_racine AND cd.id='$lig_groupe->id_dev';";
													//echo "$sql<br />\n";
													$res_info_dev=mysqli_query($GLOBALS["mysqli"], $sql);

													$lig_info_dev=mysqli_fetch_object($res_info_dev);
													$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig_info_dev->periode' AND id_classe='$tab_id_classe[$i]';";
													//echo "$sql<br />\n";
													$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
													$lig_per=mysqli_fetch_object($res_per);

													$titre="Devoir n°$lig_groupe->id_dev (<i>$lig_per->nom_periode</i>)";
													$texte="<p><b>".htmlspecialchars($lig_info_dev->nom_court)."</b>";
													if($lig_info_dev->nom_court!=$lig_info_dev->nom_complet) {
														$texte.=" (<i>".htmlspecialchars($lig_info_dev->nom_complet)."</i>)";
													}
													$texte.="<br />";
													if($lig_info_dev->description!='') {
														$texte.=htmlspecialchars($lig_info_dev->description);
													}
													//$tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig_groupe->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
													$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig_groupe->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
	//echo "count(\$reserve_header_tabdiv_infobulle)=".count($reserve_header_tabdiv_infobulle)." pour div_dev_$lig_groupe->id_dev<br />";
												}
											}
										}
									}
									elseif($lig_groupe->type=='moy_plusieurs_periodes') {
										// Dans le cas d'une moyenne de bulletins sur plusieurs périodes, on a calculé la moyenne dans mod_examen_blanc/index.php et rempli ex_notes (les valeurs sont figées à l'instant de la sélection dans mod_examen_blanc/index.php; si les moyennes sont modifiées/corrigées après coup, il faut reprovoquer le calcul dans mod_examen_blanc/index.php)

										$chaine_mpp="moy_plusieurs_periodes_".$lig_groupe->id_groupe."_".strtr($lig_groupe->valeur," ","_");

										$sql="SELECT en.* FROM ex_notes en WHERE en.id_ex_grp='$lig_groupe->id_ex_grp';";
										//echo "$sql<br />\n";
										$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
										while($lig_dev=mysqli_fetch_object($res_dev)) {
											// Comme on fait une requête sur j_eleves_classes pour lister les élèves, les entrées inutiles du tableau $tab_note ci-dessous ne seront pas prises en compte dans le tableau des résultats
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["infobulle"]=$chaine_mpp;

											if($lig_dev->statut=="") {
												if(!isset($tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
													$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
													$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]=0;
												}

												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=$lig_dev->note;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]++;
											}
										}

										if(!in_array($chaine_mpp,$tab_moy_plusieurs_periodes)) {
											$tab_moy_plusieurs_periodes[]=$chaine_mpp;

											$titre="Moyennes des périodes $lig_groupe->valeur";
											$texte="<p><b>Moyennes des moyennes de bulletins pour les périodes $lig_groupe->valeur</b>";
											$texte.="<br />";

											$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_'.$chaine_mpp,$titre,"",$texte,"",30,0,'y','y','n','n');
											//$tabdiv_infobulle[]=creer_div_infobulle('div_bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
										}
									}
									elseif($lig_groupe->type=='epreuve_blanche') {
										$note_sur=20;
										if(!array_key_exists($lig_groupe->valeur, $tab_epb_param)) {
											$tab_epb_param[$lig_groupe->valeur]=get_tab_infos_epreuve_blanche($lig_groupe->valeur);
										}

										if(isset($tab_epb_param[$lig_groupe->valeur]['note_sur'])) {
											$note_sur=$tab_epb_param[$lig_groupe->valeur]['note_sur'];
										}
										else {
											if(!isset($msg)) {$msg="";}
											$msg.="Anomalie : L'épreuve blanche n°$lig_groupe->valeur n'a pas été trouvée.<br />";
										}

										if($note_sur==0) {
											$msg.="ANOMALIE&nbsp;: L'épreuve blanche n°$lig_groupe->valeur serait notée sur zéro.<br />Référentiel ramené à 20.<br />";
											$note_sur=20;
										}

										$chaine_mpp="note_epreuve_blanche_".$lig_groupe->id_groupe."_".strtr($lig_groupe->valeur," ","_");

										$sql="SELECT DISTINCT ec.* FROM eb_copies ec, 
													eb_groupes eg,
													j_eleves_groupes jeg
												WHERE eg.id_groupe='".$lig_groupe->id_groupe."' AND 
													eg.id_groupe=jeg.id_groupe AND 
													jeg.login=ec.login_ele AND 
													eg.id_epreuve=ec.id_epreuve AND 
													eg.id_epreuve='".$lig_groupe->valeur."' AND 
													ec.statut!='v';";
										//echo "$sql<br />\n";
										$res_notes_epb=mysqli_query($GLOBALS["mysqli"], $sql);
										$eff_notes_epb=mysqli_num_rows($res_notes_epb);
										if($eff_notes_epb>0) {
											while($lig_note=mysqli_fetch_object($res_notes_epb)) {
												$tab_note["$lig_note->login_ele"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_note->statut;
												$tab_note["$lig_note->login_ele"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_note->note*20/$note_sur;
												$tab_note["$lig_note->login_ele"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["infobulle"]=$chaine_mpp;

												if($lig_note->statut=="") {
													if(!isset($tab_note["$lig_note->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
														$tab_note["$lig_note->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
														$tab_note["$lig_note->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]=0;
													}

													$tab_note["$lig_note->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=20*$lig_dev->note/$note_sur;
													$tab_note["$lig_note->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]++;
												}
											}
										}

										if(!in_array($chaine_mpp,$tab_moy_epb)) {
											$tab_moy_epb[]=$chaine_mpp;

											$titre="Note EPB $lig_groupe->valeur";
											$texte="<p><b>Note provenant de l'épreuve blanche n°$lig_groupe->valeur</b>";
											$texte.="<br />";

											$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_'.$chaine_mpp,$titre,"",$texte,"",30,0,'y','y','n','n');
										}
									}

								}
							}
							/*
							else {
								$sql="SELECT en.* FROM ex_groupes eg, ex_notes en WHERE eg.id=en.id_ex_grp AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
								//echo "$sql<br />\n";
								$res_dev=mysql_query($sql);
								while($lig_dev=mysql_fetch_object($res_dev)) {
									//echo "\$tab_note[\"$lig_dev->login\"][\"$tab_matiere[$j]\"]['statut']<br />";
				
									$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
									$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
								}
							}
							*/
						}
					}

					// On recherche les notes hors enseignement:
					for($j=0;$j<$nb_matieres;$j++) {
						//$sql="SELECT en.* FROM ex_groupes eg, ex_notes en WHERE eg.id=en.id_ex_grp AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
						$sql="SELECT en.* FROM ex_groupes eg, ex_notes en WHERE eg.id=en.id_ex_grp AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND eg.type='hors_enseignement';";
						//echo "$sql<br />\n";
						$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
						while($lig_dev=mysqli_fetch_object($res_dev)) {
							//echo "\$tab_note[\"$lig_dev->login\"][\"$tab_matiere[$j]\"]['statut']<br />";
							//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
							//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
							// Comme on fait une requête sur j_eleves_classes pour lister les élèves, les entrées inutiles du tableau $tab_note ci-dessous ne seront pas prises en compte dans le tableau des résultats
							for($i=0;$i<$nb_classes;$i++) {
								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;

								if($lig_dev->statut=="") {
									if(!isset($tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
										$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
										$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]=0;
									}

									$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=$lig_dev->note;
									$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nb_notes"]++;
								}
							}
						}
					}
// 20170326 : total et nb_notes à prendre en compte dans les exports.
					if($mode=='csv') {
						check_token();

						$csv="CLASSE;LOGIN_ELEVE;NOM_PRENOM_ELEVE;";
						for($j=0;$j<$nb_matieres;$j++) {$csv.=$tab_matiere[$j].";";}
						$csv.="MOYENNE;\r\n";

						$csv.=";;COEFFICIENT;";
						for($j=0;$j<$nb_matieres;$j++) {$csv.=strtr($tab_coef[$j],".",",").";";}
						$csv.=";\r\n";

						$csv.=";;BONUS;";
						for($j=0;$j<$nb_matieres;$j++) {$csv.=$tab_bonus[$j].";";}
						$csv.=";\r\n";

						for($i=0;$i<$nb_classes;$i++) {

							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele)>0) {
								while($lig_ele=mysqli_fetch_object($res_ele)) {
									$tot_ele=0;
									$tot_coef=0;
									$csv.=$tab_classe[$i].";";

									$csv.=$lig_ele->login.";".casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2').";";
									for($j=0;$j<count($tab_matiere);$j++) {
										if((isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']))&&($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']>1)) {
											$note_courante=round($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['total']/$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']*10)/10;

											if($tab_bonus[$j]=='n') {
												$tot_coef+=$tab_coef[$j];
												$tot_ele+=$note_courante*$tab_coef[$j];
											}
											else {
												$tot_ele+=max(0,($note_courante-10)*$tab_coef[$j]);
											}

											$csv.=strtr($note_courante,".",",").";";

										}
										elseif(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
											if($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut']!='') {
												$csv.=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'].";";
											}
											else {
												if($tab_bonus[$j]=='n') {
													$tot_coef+=$tab_coef[$j];
													$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
												}
												else {
													$tot_ele+=max(0,($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
												}
												$csv.=strtr($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",").";";
											}
										}
										else {
											$csv.=";";
										}
									}
									if($tot_coef>0) {
										$moyenne=round(10*$tot_ele/$tot_coef)/10;
										$csv.=strtr($moyenne,".",",").";";
									}
									else {
										$csv.="-;";
									}
									$csv.="\r\n";
								}
							}
						}

						$nom_fic="releve_examen_num_".$id_exam.".csv";

						send_file_download_headers('text/x-csv',$nom_fic);
						//echo $csv;
						echo echo_csv_encoded($csv);
						die();

					}
					if($mode=='pdf') {
						if (!defined('FPDF_VERSION')) {
							require_once('../fpdf/fpdf.php');
						}
						require_once("../fpdf/class.multicelltag.php");
					
						// Fichier d'extension de fpdf pour le bulletin
						require_once("../class_php/gepi_pdf.class.php");
					
						// Fonctions php des bulletins pdf
						require_once("../bulletin/bulletin_fonctions.php");
						// Ensemble des données communes
						require_once("../bulletin/bulletin_donnees.php");
					
						
					
						session_cache_limiter('private');
					
						$X1 = 0; $Y1 = 0; $X2 = 0; $Y2 = 0;
						$X3 = 0; $Y3 = 0; $X4 = 0; $Y4 = 0;
						$X5 = 0; $Y5 = 0; $X6 = 0; $Y6 = 0;
					
						$annee_scolaire = $gepiYear;
					
						$gepiSchoolName=getSettingValue('gepiSchoolName');
					
						$largeur_page=210;
						$hauteur_page=297;
					
						$marge_gauche=5;
						$marge_droite=5;
						$marge_haute=5;
						$marge_basse=5;
					
						$hauteur_police=10;
						$taille_max_police=$hauteur_police;
						$taille_min_police=ceil($taille_max_police/3);

						// Largeur des colonnes
						$largeur_col_nom_ele=40;
						$largeur_min_col_note=10;
						$largeur_max_col_note=30;
						$largeur_col_note=($largeur_page-$marge_gauche-$marge_droite-$largeur_col_nom_ele)/($nb_matieres+1);
						if($largeur_col_note<$largeur_min_col_note) {
							$largeur_col_note=$largeur_min_col_note;
						}
						elseif($largeur_col_note>$largeur_max_col_note) {
							$largeur_col_note=$largeur_max_col_note;
						}

						// Hauteur des lignes:
						$h_ligne_titre=10;
						$h_ligne_titre_tableau=10;
						$h_cell=10;
						$h_min_cell=6;
						$h_max_cell=10;

						$x0=$marge_gauche;
						$y0=$marge_haute;

						$format_page="P";

						// Tester si cela tient en largeur
						$largeur_totale=$largeur_col_nom_ele+($nb_matieres+1)*$largeur_col_note;
						if($largeur_totale>$largeur_page-$marge_gauche-$marge_droite) {
							$format_page="L";
							$tmp=$largeur_page;
							$largeur_page=$hauteur_page;
							$hauteur_page=$tmp;

							if($largeur_totale>$largeur_page-$marge_gauche-$marge_droite) {
								// Il va falloir réduire la taille des cellules
								$largeur_col_note=floor(($largeur_page-$marge_gauche-$marge_droite-$largeur_col_nom_ele)/($nb_matieres+1));
							}
						}

						$nb_max_eleves_par_classe=0;
						for($i=0;$i<$nb_classes;$i++) {
							$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$tab_id_classe[$i]';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)>0) {
								if(mysqli_num_rows($res)>$nb_max_eleves_par_classe) {$nb_max_eleves_par_classe=mysqli_num_rows($res);}
							}
						}
						if($nb_max_eleves_par_classe>0) {
							// On ajoute les 6 lignes de stat en bas de tableau
							$h_cell=floor(($hauteur_page-$h_ligne_titre-$h_ligne_titre_tableau-$marge_haute-$marge_basse)/($nb_max_eleves_par_classe+6));

							if($h_cell>$h_max_cell) {$h_cell=$h_max_cell;}

							if($h_cell<$h_min_cell) {
								$h_cell=$h_min_cell;
								// Et on changera de page...
								// On pourrait recalculer une hauteur optimale avec 2 pages, 3 pages,...
							}
						}

						$pdf=new bul_PDF($format_page, 'mm', 'A4');
						$pdf->SetCreator($gepiSchoolName);
						$pdf->SetAuthor($gepiSchoolName);
						$pdf->SetKeywords('');
						$pdf->SetSubject('Examen blanc '.$id_exam);
						$pdf->SetTitle('Examen blanc '.$id_exam);
						$pdf->SetDisplayMode('fullwidth', 'single');
						$pdf->SetCompression(TRUE);
						$pdf->SetAutoPageBreak(TRUE, 5);

						$fonte='DejaVu';

						for($i=0;$i<$nb_classes;$i++) {
							$tab_notes=array();
							$tab_moy=array();

							$pdf->AddPage();
							//========================================
							// Titre
							$pdf->SetXY($x0, $y0);
							$texte="Relevé de notes de l'examen blanc n°$id_exam - Classe de ".$tab_classe[$i];
							$largeur_dispo=$largeur_page-$marge_gauche-$marge_droite;
							$hauteur_caractere=12;
							$h_ligne=$h_ligne_titre;
							$graisse='B';
							$alignement='C';
							$bordure='';
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$hauteur_caractere,$fonte,$graisse,$alignement,$bordure);
							$y2=$y0+$h_ligne_titre;
							//========================================
	
							//========================================
							// Ligne d'entête du tableau
							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte='Nom prénom';
							$graisse='B';
							$alignement='C';
							$bordure='LRBT';
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
	
							$x2=$x0+$largeur_col_nom_ele;
							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=$tab_matiere[$j];
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}
	
							$pdf->SetXY($x2, $y2);
							$texte='Moyenne';
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
	
							$y2=$y2+$h_ligne_titre_tableau;
							//========================================


							//========================================
							// Lignes du tableau
							$graisse='';
							$alignement='C';
							$bordure='LRBT';
							$h_ligne=$h_cell;

							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele)>0) {
								while($lig_ele=mysqli_fetch_object($res_ele)) {
									$tot_ele=0;
									$tot_coef=0;

									if($y2+$h_ligne>$hauteur_page-$marge_basse) {
										$pdf->AddPage();

										//========================================
										// Ligne d'entête du tableau
										$x2=$x0;
										$y2=$y0;
										$pdf->SetXY($x2, $y2);
										$largeur_dispo=$largeur_col_nom_ele;
										$texte='Nom prénom';
										$graisse='B';
										$alignement='C';
										$bordure='LRBT';
										cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
										$x2+=$largeur_dispo;
				
										$x2=$x0+$largeur_col_nom_ele;
										$largeur_dispo=$largeur_col_note;
										for($j=0;$j<$nb_matieres;$j++) {
											$pdf->SetXY($x2, $y2);
											$texte=$tab_matiere[$j];
											cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
											$x2+=$largeur_dispo;
										}
				
										$pdf->SetXY($x2, $y2);
										$texte='Moyenne';
										cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne_titre_tableau,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
										$x2+=$largeur_dispo;
				
										$y2+=$h_ligne_titre_tableau;
										//========================================

									}

									// Colonne Nom_prénom
									$x2=$x0;
									$pdf->SetXY($x2, $y2);
									$largeur_dispo=$largeur_col_nom_ele;
									$texte=casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2');
									cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
									$x2+=$largeur_dispo;

									// Colonnes matières
									$largeur_dispo=$largeur_col_note;
									for($j=0;$j<count($tab_matiere);$j++) {
										$pdf->SetXY($x2, $y2);
										$texte="";
										if((isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']))&&($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']>1)) {
											$note_courante=round($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['total']/$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']*10)/10;

											if($tab_bonus[$j]=='n') {
												$tot_coef+=$tab_coef[$j];
												$tot_ele+=$note_courante*$tab_coef[$j];
											}
											else {
												$tot_ele+=max(0,($note_courante-10)*$tab_coef[$j]);
											}

											$texte=strtr($note_courante,".",",");
											$tab_notes[$j][]=$note_courante;

										}
										elseif(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
											if($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut']!='') {
												$texte=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'];
											}
											else {
												if($tab_bonus[$j]=='n') {
													$tot_coef+=$tab_coef[$j];
													$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
												}
												else {
													$tot_ele+=max(0,($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
												}
												$texte=strtr($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",");

												$tab_notes[$j][]=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'];
											}
										}
										else {
											$texte="";
										}

										cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

										$x2+=$largeur_dispo;
									}

									// Colonne Moyenne
									if($tot_coef>0) {
										$moyenne=round(10*$tot_ele/$tot_coef)/10;

										$texte=strtr($moyenne,".",",");

										$tab_moy[]=$moyenne;
									}
									else {
										$texte="";
									}

									$pdf->SetXY($x2, $y2);
									cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);

									$x2+=$largeur_dispo;
									$y2+=$h_cell;
								}
							}

							// 20140603
							$graisse='B';
							//=============================================
							for($j=0;$j<$nb_matieres;$j++) {
								$tab_stat[$j]=calcul_moy_med($tab_notes[$j]);
							}
							$tab_stat2=calcul_moy_med($tab_moy);
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="Moyenne";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['moyenne'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['moyenne'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="1er quartile";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['q1'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['q1'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="Médiane";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['mediane'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['mediane'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="3è quartile";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['q3'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['q3'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="Min";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['min'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['min'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================

							if($y2+$h_ligne>$hauteur_page-$marge_basse) {
								$pdf->AddPage();
								$y2=$y0;
							}

							$x2=$x0;
							$pdf->SetXY($x2, $y2);
							$largeur_dispo=$largeur_col_nom_ele;
							$texte="Max";
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;

							$largeur_dispo=$largeur_col_note;
							for($j=0;$j<$nb_matieres;$j++) {
								$pdf->SetXY($x2, $y2);
								$texte=strtr($tab_stat[$j]['max'],".",",");
								cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
								$x2+=$largeur_dispo;
							}

							$pdf->SetXY($x2, $y2);
							$texte=strtr($tab_stat2['max'],".",",");
							cell_ajustee_une_ligne(($texte),$pdf->GetX(),$pdf->GetY(),$largeur_dispo,$h_ligne,$taille_max_police,$fonte,$graisse,$alignement,$bordure);
							$x2+=$largeur_dispo;
							$y2+=$h_cell;
							//=============================================


							//========================================

						}

						$pref_output_mode_pdf=get_output_mode_pdf();

						$nom_fic="releve_examen_num_".$id_exam.".pdf";
						send_file_download_headers('application/pdf',$nom_fic);
						$pdf->Output($nom_fic,$pref_output_mode_pdf);
						die();
					}
				}
			}
		}
	}
}

// Sauvegarde de $tabid_infobulle qui est réinitialisé dans le header
if(isset($tabid_infobulle)) {
	$reserve_tabid_infobulle=$tabid_infobulle;
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Examen blanc: Relevé";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

// Restauration
if(isset($reserve_tabid_infobulle)) {
	$tabid_infobulle=$reserve_tabid_infobulle;
}

//debug_var();
/*
echo "<script type='text/javascript'>
var change='no';
</script>\n";
*/

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Examens blancs</a>";

if(!isset($id_exam)) {
	echo "</p>\n";

	echo "<p>Erreur&nbsp;: Aucun examen n'a été choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
//echo "$sql<br />\n";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "</p>\n";

	echo "<p>L'examen choisi (<i>$id_exam</i>) n'existe pas.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo " | <a href='index.php?id_exam=$id_exam&amp;mode=modif_exam'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Examen n°$id_exam</a>";
//echo "</p>\n";
//echo "</div>\n";

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//===========================
	// Classes 
	//$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
	//$res_classes=mysql_query($sql);
	//$nb_classes=mysql_num_rows($res_classes);
	if($nb_classes==0) {
		echo "</p>\n";

		echo "<p>Aucune classe n'est associée à l'examen???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	/*
	$tab_id_classe=array();
	$tab_classe=array();
	while($lig=mysql_fetch_object($res_classes)) {
		$tab_id_classe[]=$lig->id_classe;
		$tab_classe[]=$lig->classe;
	}
	*/

	//===========================
	// Matières
	//$sql="SELECT m.*,em.coef,em.bonus FROM ex_matieres em, matieres m WHERE em.matiere=m.matiere ORDER BY em.ordre, m.matiere;";
	//$res_matieres=mysql_query($sql);
	//$nb_matieres=mysql_num_rows($res_matieres);
	if($nb_matieres==0) {
		echo "</p>\n";

		echo "<p>Aucune matière n'est associée à l'examen???</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	if(isset($reserve_header_tabdiv_infobulle)) {
		$tabdiv_infobulle=$reserve_header_tabdiv_infobulle;
		//echo "BLA";
		//echo " count(\$tabdiv_infobulle)=".count($tabdiv_infobulle)."<br />";
	}

	echo "<div style='float:right; width: 5em; text-align:center; border: 1px solid black;' class='fieldset_opacite50'>\n";
	echo "<a href='releve.php?id_exam=$id_exam&amp;mode=csv".add_token_in_url()."'";
	echo " target='_blank'>CSV</a>\n";
	echo "<br />\n";
	echo "<a href='releve.php?id_exam=$id_exam&amp;mode=pdf".add_token_in_url()."'";
	echo " target='_blank'>PDF</a>\n";
	echo "</div>\n";

	//$csv="";
	for($i=0;$i<$nb_classes;$i++) {
		echo "<p class='bold'>Classe $tab_classe[$i]</p>\n";

		// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />\n";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_ele=mysqli_num_rows($res_ele);
		if($nb_ele==0) {
			echo "<p>Aucun élève dans cette classe???</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<div style='float:right; width: 20em;'>\n";
		echo "<p class='bold'>Classe $tab_classe[$i]</p>\n";
		javascript_tab_stat2('tab_stat_'.$tab_id_classe[$i].'_',$nb_ele);
		echo "</div>\n";

		echo "<table class='boireaus' summary='Classe de $tab_classe[$i]'>\n";
		echo "<tr>\n";
		echo "<th>Matières</th>\n";
		for($j=0;$j<$nb_matieres;$j++) {echo "<th>$tab_matiere[$j]</th>\n";}
		echo "<th rowspan='2'>Moyenne</th>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th>Coefficients et bonus /<br />Elèves</th>\n";
		for($j=0;$j<count($tab_matiere);$j++) {
			echo "<th>$tab_coef[$j]";
			if($tab_bonus[$j]=='y') {
				echo "<br />";
				echo "Bonus";
			}
			echo "</th>\n";
		}
		//echo "<th></th>\n";
		echo "</tr>\n";

		$alt=1;
		$cpt_ele=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$tot_ele=0;
			$tot_coef=0;
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</td>\n";
			for($j=0;$j<count($tab_matiere);$j++) {
				echo "<td>\n";
				if((isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']))&&($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']>1)) {
					$note_courante=round($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['total']/$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nb_notes']*10)/10;
					echo "<span title='Moyenne de plusieurs notes.'>".strtr($note_courante, ".", ",")."</span>";

					if($tab_bonus[$j]=='n') {
						$tot_coef+=$tab_coef[$j];
						$tot_ele+=$note_courante*$tab_coef[$j];
					}
					else {
						$tot_ele+=max(0,($note_courante-10)*$tab_coef[$j]);
					}

				}
				elseif(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
					if($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut']!='') {
						if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev'])) {
							echo "<a href='#' onmouseover=\"delais_afficher_div('div_dev_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev']."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_dev_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev']."')\" onclick='return false;'>";
							echo $tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'];
							echo "</a>\n";
						}
						elseif(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle'])) {
							echo "<a href='#' onmouseover=\"delais_afficher_div('div_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle']."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle']."')\" onclick='return false;'>";
							echo $tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'];
							echo "</a>\n";
						}
						else {
							echo $tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'];
						}
					}
					else {
						//$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
						if($tab_bonus[$j]=='n') {
							$tot_coef+=$tab_coef[$j];
							$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
						}
						else {
							$tot_ele+=max(0,($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
						}
						if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev'])) {
							echo "<a href='#' onmouseover=\"delais_afficher_div('div_dev_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev']."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_dev_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev']."')\" onclick='return false;'>";
							echo strtr($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",");
							echo "</a>\n";
						}
						elseif(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle'])) {
							echo "<a href='#' onmouseover=\"delais_afficher_div('div_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle']."','y',10,-10,1000,20,20)\" onmouseout=\"cacher_div('div_".$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['infobulle']."')\" onclick='return false;'>";
							echo $tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'];
							echo "</a>\n";
						}
						else {
							echo strtr($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",");
						}
					}
				}
				else {
					//echo "\$tab_note[\"$lig_ele->login\"][$tab_id_classe[$i]][\"$tab_matiere[$j]\"]['statut']";
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			echo "<td class='bold'>\n";
			if($tot_coef>0) {
				$moyenne=round(10*$tot_ele/$tot_coef)/10;
				echo strtr($moyenne,".",",");
				echo "<input type='hidden' name='tab_stat_".$tab_id_classe[$i]."_$cpt_ele' id='tab_stat_".$tab_id_classe[$i]."_$cpt_ele' value='$moyenne' />\n";
			}
			else {
				echo "-";
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt_ele++;
		}
		// Lignes de moyennes, médiane,...

		echo "</table>\n";

	}
}
//echo "<p style='color:red;'><i>PROBLEME&nbsp;:</i> Pour les élèves qui ont changé de classe, si on a sélectionné des devoirs de périodes différentes, on peut ne pas récupérer la note souhaitée.</p>\n";
echo "<p style='color:red;'><i>NOTES&nbsp;:</i> Les moyennes supposent actuellement que le référentiel des devoirs est 20.<br />Il faudra modifier pour prendre en compte des notes sur autre chose que 20.<br />Les 'bonus' consistent à ne compter que les points supérieurs à 10.<br />Ex.: Pour 12 (coef 3), 14 (coef 1) et 13 (coef 2 et bonus), le calcul est (12*3+14*1+(13-10)*2)/(3+1)</p>\n";
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
