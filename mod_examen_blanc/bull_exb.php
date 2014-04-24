<?php
/*
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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/bull_exb.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_examen_blanc/bull_exb.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Examen blanc: Bulletins',
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

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

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
			$lig_exam=mysqli_fetch_object($res_test_id_exam);
			$intitule_exam=$lig_exam->intitule;
			$description_exam=$lig_exam->description;
			$date_exam=$lig_exam->date;

			//===========================
			// Classes 
			$sql="SELECT c.classe, c.nom_complet, c.suivi_par, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_classes=mysqli_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="<p>Aucune classe n'est associée à l'examen???</p>\n";
			}
			else {
				$tab_id_classe=array();
				$tab_classe=array();
				$tab_classe_nom_complet=array();
				$tab_suivi_par=array();
				while($lig=mysqli_fetch_object($res_classes)) {
					$tab_id_classe[]=$lig->id_classe;
					$tab_classe[]=$lig->classe;
					$tab_classe_nom_complet[]=$lig->nom_complet;
					$tab_suivi_par[]=$lig->suivi_par;
				}

				// Récupération des paramètres par défaut de modèles de bulletin PDF
				// Par la suite, il faudra permettre de modifier les valeurs
				include("get_param_bull.php");

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
					$tab_matiere_nom_complet=array();
					$tab_coef=array();
					$tab_bonus=array();
					while($lig=mysqli_fetch_object($res_matieres)) {
						$tab_matiere[]=$lig->matiere;
						$tab_matiere_nom_complet[]=$lig->nom_complet;
						$tab_coef[]=$lig->coef;
						$tab_bonus[]=$lig->bonus;
					}
					//===========================
				
					$tab_note=array();
					$tab_dev=array();
					$tab_info_dev=array();
					$tab_prof=array();
					for($i=0;$i<$nb_classes;$i++) {
						for($j=0;$j<$nb_matieres;$j++) {
							//$sql="SELECT * FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
							$sql="SELECT eg.id AS id_ex_grp, eg.id_dev, eg.id_groupe, eg.type, eg.valeur FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
							//echo "$sql<br />\n";
							$res_groupe=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_groupe)>0) {
								while($lig_groupe=mysqli_fetch_object($res_groupe)) {

									/*
									// Liste des profs du groupe
									if(!isset($tab_prof[$lig_groupe->id_dev])) {
										$sql="SELECT DISTINCT u.nom,u.prenom,u.login FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_groupe->id_groupe';";
										$res_prof=mysql_query($sql);
										$tab_prof[$lig_groupe->id_dev]=array();
										if(mysql_num_rows($res_prof)) {
											while($lig_prof=mysql_fetch_object($res_prof)) {
												$tab_prof[$lig_groupe->id_dev][]=$lig_prof->login;
											}
										}
									}
									*/

									if($lig_groupe->type=='moy_bull') {
										$moy_min_bull_grp=1000;
										$moy_max_bull_grp=-1;


										// Liste des profs du groupe
										if(!isset($tab_prof['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur])) {
											$sql="SELECT DISTINCT u.nom,u.prenom,u.login FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_groupe->id_groupe';";
											$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
											$tab_prof['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]=array();
											if(mysqli_num_rows($res_prof)) {
												while($lig_prof=mysqli_fetch_object($res_prof)) {
													$tab_prof['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur][]=$lig_prof->login;
												}
											}
										}



										$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig_groupe->id_groupe' AND periode='$lig_groupe->valeur';";
										//echo "$sql<br />\n";
										$res_bull=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_bull)>0) {
											while($lig_bull=mysqli_fetch_object($res_bull)) {
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_bull->statut;
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_bull->note;

												if($lig_bull->statut=='') {
													if($lig_bull->note>$moy_max_bull_grp) {$moy_max_bull_grp=$lig_bull->note;}
													if($lig_bull->note<$moy_min_bull_grp) {$moy_min_bull_grp=$lig_bull->note;}
												}

												// Dans le cas où on utilise une moyenne de groupe sur le bulletin pour une période, on remplace l'id_dev par une référence du type bull_$id_groupe_$periode_num

												//$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]=$lig_groupe->id_dev;
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;

												/*
												if(!isset($tab_info_dev['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur])) {
													$tab_info_dev['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]="";
												}
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"]=$tab_info_dev['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur];
												*/

												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"]="Moyenne de l'élève pour la période $lig_groupe->valeur";
	
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];
	
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["coef"]=$tab_coef[$j];
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["bonus"]=$tab_bonus[$j];
	
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];
											}

											/*
											if(!in_array('bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$tab_bull)) {
												$tab_bull[]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;

												$titre="Moyenne du bulletin (<i>$lig_per->nom_periode</i>)";
												$texte="<p><b>Moyenne du bulletin sur la période $lig_per->nom_periode</b>";
												$texte.="<br />";

												$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
											}
											*/
										}

/*
										// Récupération/stockage des moyennes min/max/classe
										$sql="SELECT ROUND(AVG(note),1) moyenne, MIN(note) note_min, MAX(note) note_max FROM matieres_notes WHERE id_groupe='$lig_groupe->id_groupe' AND periode='$lig_groupe->valeur' AND statut='';";
										$res_moy=mysql_query($sql);
										if(mysql_num_rows($res_moy)>0) {
											$lig_moy_bull=mysql_fetch_object($res_moy);
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["moyenne"]=$lig_moy_bull->moyenne;
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["max"]=$lig_moy_bull->note_max;
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["min"]=$lig_moy_bull->note_min;
										}
										else {
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["moyenne"]="-";
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["max"]="-";
											$tab_moy_bull['bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur]["min"]="-";
										}

*/
									}
									elseif($lig_groupe->type=='') {

										// Liste des profs du groupe
										if(!isset($tab_prof[$lig_groupe->id_dev])) {
											$sql="SELECT DISTINCT u.nom,u.prenom,u.login FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_groupe->id_groupe';";
											$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
											$tab_prof[$lig_groupe->id_dev]=array();
											if(mysqli_num_rows($res_prof)) {
												while($lig_prof=mysqli_fetch_object($res_prof)) {
													$tab_prof[$lig_groupe->id_dev][]=$lig_prof->login;
												}
											}
										}

										$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev';";
										//echo "$sql<br />\n";
										$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_dev)>0) {
	
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
	
												$tab_info_dev[$lig_groupe->id_dev]=$lig_info_dev->nom_court." ($lig_per->nom_periode)";
											}
	
											while($lig_dev=mysqli_fetch_object($res_dev)) {
												//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
												//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]=$lig_groupe->id_dev;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"]=$tab_info_dev[$lig_groupe->id_dev];
	
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];
	
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["coef"]=$tab_coef[$j];
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["bonus"]=$tab_bonus[$j];
	
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];
											}
/*
											// Calcul de moyenne, min et max
											//$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]
											$sql="SELECT ROUND(AVG(note),1) moyenne, MIN(note) note_min, MAX(note) note_max FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev' AND statut='';";
											$res_moy=mysql_query($sql);
											if(mysql_num_rows($res_moy)>0) {
												$lig_moy_dev=mysql_fetch_object($res_moy);
												$tab_moy_dev[$lig_groupe->id_dev]["moyenne"]=$lig_moy_dev->moyenne;
												$tab_moy_dev[$lig_groupe->id_dev]["max"]=$lig_moy_dev->note_max;
												$tab_moy_dev[$lig_groupe->id_dev]["min"]=$lig_moy_dev->note_min;
											}
											else {
												$tab_moy_dev[$lig_groupe->id_dev]["moyenne"]="-";
												$tab_moy_dev[$lig_groupe->id_dev]["max"]="-";
												$tab_moy_dev[$lig_groupe->id_dev]["min"]="-";
											}
*/	
										}
									}

									elseif($lig_groupe->type=='moy_plusieurs_periodes') {

										// Liste des profs du groupe
										if(!isset($tab_prof_grp[$lig_groupe->id_groupe])) {
											$sql="SELECT DISTINCT u.nom,u.prenom,u.login FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_groupe->id_groupe';";
											$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
											$tab_prof_grp[$lig_groupe->id_groupe]=array();
											if(mysqli_num_rows($res_prof)) {
												while($lig_prof=mysqli_fetch_object($res_prof)) {
													$tab_prof_grp[$lig_groupe->id_groupe][]=$lig_prof->login;
												}
											}
										}

										$sql="SELECT en.* FROM ex_notes en WHERE en.id_ex_grp='$lig_groupe->id_ex_grp';";
										//echo "$sql<br />\n";
										$res_dev=mysqli_query($GLOBALS["mysqli"], $sql);
										while($lig_dev=mysqli_fetch_object($res_dev)) {
											// Comme on fait une requête sur j_eleves_classes pour lister les élèves, les entrées inutiles du tableau $tab_note ci-dessous ne seront pas prises en compte dans le tableau des résultats
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;

											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["coef"]=$tab_coef[$j];
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["bonus"]=$tab_bonus[$j];
			
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];
			
											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"]="Moyenne des périodes $lig_groupe->valeur.";

											$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_groupe"]=$lig_groupe->id_groupe;
										}
									}


								}
							}

						}
					}
				
					for($j=0;$j<$nb_matieres;$j++) {
						// Moyennes min/max/classe pour les notes hors enseignement
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

								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["coef"]=$tab_coef[$j];
								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["bonus"]=$tab_bonus[$j];

								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["nom_complet"]=$tab_matiere_nom_complet[$j];

								$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"]="Note hors enseignement de l'année.";
/*
								if($lig_dev->statut=='') {
									if(!isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=$lig_dev->note;
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"]=1;
									}
									else {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=$lig_dev->note;
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"]++;
									}

									if(!isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"])) {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]=$lig_dev->note;
									}
									elseif($lig_dev->note<$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]) {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]=$lig_dev->note;
									}

									if(!isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"])) {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]=$lig_dev->note;
									}
									elseif($lig_dev->note>$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]) {
										$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]=$lig_dev->note;
									}
								}
*/
							}
						}

/*
						for($i=0;$i<$nb_classes;$i++) {
							if(isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["moyenne"]=round(10*$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]/$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"])/10;
							}
						}
*/
					}





					if($mode=='imprimer') {
						// Le check_token() est ici surtout destiné à éviter de bouffer inutilement des ressources
						check_token();

						//=====================================================

						// RECALCUL DES MOYENNES DE MATIERES
						// Initialisations:
						for($i=0;$i<$nb_classes;$i++) {
							for($j=0;$j<$nb_matieres;$j++) {
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]=1000;
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]=-1;
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]=0;
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"]=0;
							}
						}
	
						// Parcours des élèves/classes/matières
						foreach($tab_note as $current_login => $tab_note_ele) {
							for($i=0;$i<$nb_classes;$i++) {
								for($j=0;$j<$nb_matieres;$j++) {
									if(isset($tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"])) {
										if($tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=='') {
											$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]+=$tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"];
											$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"]++;
		
											if($tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]<$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]) {
												$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]=$tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"];
											}
		
											if($tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]>$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]) {
												$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]=$tab_note[$current_login][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"];
											}
										}
									}
								}
							}
						}

						// Pour des moyennes de matières indépendantes des classes.
						$tab_effectif_matiere=array();
						$tab_total_matiere=array();
						$tab_moy_matiere=array();
						$tab_min_matiere=array();
						$tab_max_matiere=array();
						for($j=0;$j<$nb_matieres;$j++) {
							$tab_effectif_matiere["$tab_matiere[$j]"]=0;
							$tab_total_matiere["$tab_matiere[$j]"]=0;
							$tab_moy_matiere["$tab_matiere[$j]"]="-";
							$tab_min_matiere["$tab_matiere[$j]"]=1000;
							$tab_max_matiere["$tab_matiere[$j]"]=-1;
						}

						// Finalisation des min/max/moy_classe_grp propres à chaque classe
						for($i=0;$i<$nb_classes;$i++) {
							for($j=0;$j<$nb_matieres;$j++) {
								if($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]==1000) {$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]="-";}
								elseif($tab_min_matiere["$tab_matiere[$j]"]>$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]) {$tab_min_matiere["$tab_matiere[$j]"]=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"];}

								if($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]==-1) {$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]="-";}
								elseif($tab_max_matiere["$tab_matiere[$j]"]<$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]) {$tab_max_matiere["$tab_matiere[$j]"]=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"];}
	
								if($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"]!=0) {
									$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["moyenne"]=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]/$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"];

									// Pour des moyennes de matières indépendantes des classes.
									$tab_effectif_matiere["$tab_matiere[$j]"]+=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"];
									$tab_total_matiere["$tab_matiere[$j]"]+=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"];

								}
								else {
									$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["moyenne"]="-";
								}
							}
						}

						// Calcul des moyennes de matières toutes classes confondues
						for($j=0;$j<$nb_matieres;$j++) {
							if($tab_effectif_matiere["$tab_matiere[$j]"]>0) {
								$tab_moy_matiere["$tab_matiere[$j]"]=$tab_total_matiere["$tab_matiere[$j]"]/$tab_effectif_matiere["$tab_matiere[$j]"];
							}
						}

						// FAUT-IL IMPOSER DES MOYENNES COMMUNES OU PROPOSER LES DEUX MODES?
						// On ré-impose les moyennes communes
						for($i=0;$i<$nb_classes;$i++) {
							for($j=0;$j<$nb_matieres;$j++) {
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["moyenne"]=$tab_moy_matiere["$tab_matiere[$j]"];
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["min"]=$tab_min_matiere["$tab_matiere[$j]"];
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["max"]=$tab_max_matiere["$tab_matiere[$j]"];
							}
						}
						//=====================================================

						// CALCUL DES MOYENNES GENERALES
						$tab_moy_gen=array();
						// Pour les moyennes min/max/examen toutes classes confondues
						$tab_commun_moy_gen=array();
						$commun_min=1000;
						$commun_max=-1;
						$commun_effectif=0;
						$commun_total=0;

						// Calcul des moyennes générales
						for($i=0;$i<$nb_classes;$i++) {
							$tab_moy_gen[$tab_id_classe[$i]]=array();

							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele)>0) {
								$tab_tmp=array();
								$tab_tmp['total']=0;

								$tab_tmp['min']=1000;
								$tab_tmp['max']=-1;
								$effectif=0;

								while($lig_ele=mysqli_fetch_object($res_ele)) {
									$tot_ele=0;
									$tot_coef=0;
									for($j=0;$j<count($tab_matiere);$j++) {
										if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
											if($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut']=='') {
												if($tab_bonus[$j]=='n') {
													$tot_coef+=$tab_coef[$j];
													$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
												}
												else {
													$tot_ele+=max(0,($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
												}
											}
										}
									}
									if($tot_coef>0) {
										$moyenne=round(10*$tot_ele/$tot_coef)/10;
										$tab_tmp['total']+=$moyenne;

										if($moyenne<$tab_tmp['min']) {
											$tab_tmp['min']=$moyenne;
										}

										if($moyenne>$tab_tmp['max']) {
											$tab_tmp['max']=$moyenne;
										}

										$effectif++;
									}
								}

								if($effectif>0) {
									$tab_moy_gen[$tab_id_classe[$i]]['moyenne']=round(10*$tab_tmp['total']/$effectif)/10;
									$tab_moy_gen[$tab_id_classe[$i]]['min']=$tab_tmp['min'];
									$tab_moy_gen[$tab_id_classe[$i]]['max']=$tab_tmp['max'];
								}
								else {
									$tab_moy_gen[$tab_id_classe[$i]]['moyenne']="-";
									$tab_moy_gen[$tab_id_classe[$i]]['min']="-";
									$tab_moy_gen[$tab_id_classe[$i]]['max']="-";
								}

							}

							$commun_total+=$tab_tmp['total'];
							$commun_effectif+=$effectif;
							if(($tab_moy_gen[$tab_id_classe[$i]]['min']!="-")&&($tab_moy_gen[$tab_id_classe[$i]]['min']<$commun_min)) {
								$commun_min=$tab_moy_gen[$tab_id_classe[$i]]['min'];
							}
							if(($tab_moy_gen[$tab_id_classe[$i]]['max']!="-")&&($tab_moy_gen[$tab_id_classe[$i]]['max']>$commun_max)) {
								$commun_max=$tab_moy_gen[$tab_id_classe[$i]]['max'];
							}
						}


						// On force comme moyennes générales les min/max/moy_tous_eleves calculées toutes classes confondues
						for($i=0;$i<$nb_classes;$i++) {
							if($commun_effectif>0) {
								$tab_moy_gen[$tab_id_classe[$i]]['moyenne']=round(10*$commun_total/$commun_effectif)/10;
								$tab_moy_gen[$tab_id_classe[$i]]['min']=$commun_min;
								$tab_moy_gen[$tab_id_classe[$i]]['max']=$commun_max;
							}
							else {
								$tab_moy_gen[$tab_id_classe[$i]]['moyenne']="-";
								$tab_moy_gen[$tab_id_classe[$i]]['min']="-";
								$tab_moy_gen[$tab_id_classe[$i]]['max']="-";
							}
						}

						//================================================================

						// Extraire les infos générales sur l'établissement
						require("../bulletin/header_bulletin_pdf.php");

						header('Content-type: application/pdf');
						//création du PDF en mode Portrait, unitée de mesure en mm, de taille A4
						$pdf=new bul_PDF('p', 'mm', 'A4');
						//$nb_eleve_aff = 1;
						//$categorie_passe = '';
						//$categorie_passe_count = 0;
						$pdf->SetCreator($gepiSchoolName);
						$pdf->SetAuthor($gepiSchoolName);
						$pdf->SetKeywords('');
						$pdf->SetSubject('Bulletin');
						$pdf->SetTitle('Bulletin');
						$pdf->SetDisplayMode('fullwidth', 'single');
						$pdf->SetCompression(TRUE);
						$pdf->SetAutoPageBreak(TRUE, 5);

						//for($i=0;$i<$nb_classes;$i++) {
						for($i=0;$i<count($id_classe);$i++) {

							// Récupération des infos sur les classes sélectionnées pour l'impression
							//$sql="SELECT classe, nom_complet FROM classes WHERE id='$id_classe';";
							for($loop=0;$loop<$nb_classes;$loop++) {
								if($id_classe[$i]==$tab_id_classe[$loop]) {
									$tmp_tab_classe[$i]=$tab_classe[$loop];
									$tmp_tab_classe_nom_complet[$i]=$tab_classe_nom_complet[$loop];
									$tmp_tab_suivi_par[$i]=$tab_suivi_par[$loop];
									break;
								}
							}

							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							//$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
							$cpt_ele_clas=0;
							if(mysqli_num_rows($res_ele)>0) {
								while($lig_ele=mysqli_fetch_object($res_ele)) {

									$tab_ele=array();
									$tab_ele['login']=$lig_ele->login;
									$tab_ele['nom']=$lig_ele->nom;
									$tab_ele['prenom']=$lig_ele->prenom;
									$tab_ele['denom_eleve']=casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2');
									$tab_ele['sexe']=$lig_ele->sexe;
									$tab_ele['naissance']=formate_date($lig_ele->naissance);
									$tab_ele['ine']=$lig_ele->no_gep;
									$tab_ele['no_gep']=$lig_ele->no_gep;
									$tab_ele['ele_id']=$lig_ele->ele_id;
									$tab_ele['elenoet']=$lig_ele->elenoet;

									/*
									$tab_ele['classe']=$tab_classe[$i];
									$tab_ele['id_classe']=$tab_id_classe[$i];
									$tab_ele['classe_nom_complet']=$tab_classe_nom_complet[$i];
									$tab_ele['suivi_par']=$tab_suivi_par[$i];
									*/
									$tab_ele['classe']=$tmp_tab_classe[$i];
									$tab_ele['id_classe']=$id_classe[$i];
									$tab_ele['classe_nom_complet']=$tmp_tab_classe_nom_complet[$i];
									$tab_ele['suivi_par']=$tmp_tab_suivi_par[$i];

									$tab_ele['intitule_exam']=$intitule_exam;
									$tab_ele['description_exam']=$description_exam;
									$tab_ele['date_exam']=$date_exam;

									// Récup infos Prof Principal (prof_suivi)
									$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$lig_ele->login."' AND id_classe='$tab_id_classe[$i]' AND jep.professeur=u.login;";
									$res_pp=mysqli_query($GLOBALS["mysqli"], $sql);
									//echo "$sql<br />";
									if(mysqli_num_rows($res_pp)>0) {
										$lig_pp=mysqli_fetch_object($res_pp);
										$tab_ele['pp']=array();
				
										$tab_ele['pp']['login']=$lig_pp->login;
										$tab_ele['pp']['nom']=$lig_pp->nom;
										$tab_ele['pp']['prenom']=$lig_pp->prenom;
										$tab_ele['pp']['civilite']=$lig_pp->civilite;
									}

									// Régime et redoublement
									$sql="SELECT * FROM j_eleves_regime WHERE login='".$lig_ele->login."';";
									$res_ele_reg=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_ele_reg)>0) {
										$lig_ele_reg=mysqli_fetch_object($res_ele_reg);
				
										$tab_ele['regime']=$lig_ele_reg->regime;
										$tab_ele['doublant']=$lig_ele_reg->doublant;
									}

									$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
									//echo "$sql<br />";
									$data_etab = mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($data_etab)>0) {
										$tab_ele['etab_id'] = @old_mysql_result($data_etab, 0, "id");
										$tab_ele['etab_nom'] = @old_mysql_result($data_etab, 0, "nom");
										$tab_ele['etab_niveau'] = @old_mysql_result($data_etab, 0, "niveau");
										$tab_ele['etab_type'] = @old_mysql_result($data_etab, 0, "type");
										$tab_ele['etab_cp'] = @old_mysql_result($data_etab, 0, "cp");
										$tab_ele['etab_ville'] = @old_mysql_result($data_etab, 0, "ville");
				
										if ($tab_ele['etab_niveau']!='') {
										foreach ($type_etablissement as $type_etab => $nom_etablissement) {
											if ($tab_ele['etab_niveau'] == $type_etab) {
												$tab_ele['etab_niveau_nom']=$nom_etablissement;
											}
										}
										if ($tab_ele['etab_cp']==0) {
											$tab_ele['etab_cp']='';
										}
										if ($tab_ele['etab_type']=='aucun')
											$tab_ele['etab_type']='';
										else
											$tab_ele['etab_type']= $type_etablissement2[$tab_ele['etab_type']][$tab_ele['etab_niveau']];
										}
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
									//echo "$sql<br />";
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


									// Remplissage d'un tableau avec les indices comme attendus par la fonction bull_exb() calquée sur celle des bulletins PDF classiques
									// Et calcul de la moyenne générale de l'élève
									$tot_ele=0;
									$tot_coef=0;
									$tab_ele['matieres']=array();
									for($j=0;$j<count($tab_matiere);$j++) {

										//$login_debug='';
										//if($lig_ele->login==$login_debug) {echo "\$tab_matiere[$j]=$tab_matiere[$j]<br />\n";}
										//if($lig_ele->login==$login_debug) {echo "\$tab_note[".$lig_ele->login."][$id_classe[$i]][".$tab_matiere[$j]."]['statut']=".$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['statut']."<br />\n";}

										if(isset($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
											if($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['statut']!='') {
												$tab_ele['matieres']["$tab_matiere[$j]"]['statut']=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['statut'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['note']="";
											}
											else {
												if($tab_bonus[$j]=='n') {
													$tot_coef+=$tab_coef[$j];
													$tot_ele+=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
												}
												else {
													$tot_ele+=max(0,($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
												}
												$tab_ele['matieres']["$tab_matiere[$j]"]['note']=strtr($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",");
												$tab_ele['matieres']["$tab_matiere[$j]"]['statut']="";
											}

											//if($lig_ele->login==$login_debug) {echo "\$tab_ele['matieres'][".$tab_matiere[$j]."]['note']=".$tab_ele['matieres']["$tab_matiere[$j]"]['note']."<br />\n";}
											//if($lig_ele->login==$login_debug) {echo "\$tot_ele=$tot_ele<br />\n";}

											unset($current_id_dev);

											if(isset($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_dev'])) {
												$current_id_dev=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_dev'];
											}

											// INFO: $current_id_dev contient un identifiant de devoir si un devoir a été choisi et si c'est la moyenne du bulletin qui a été choisie, c'est $current_id_dev=bull_$id_groupe_$periode_num

											//if($lig_ele->login==$login_debug) {echo "\$current_id_dev=$current_id_dev<br />\n";}

											if(isset($current_id_dev)) {
												if(isset($tab_prof[$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_dev']])) {
													$tab_ele['matieres']["$tab_matiere[$j]"]['profs_list']=$tab_prof[$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_dev']];
												}
											}

											if(isset($tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_groupe'])) {
												$tab_ele['matieres']["$tab_matiere[$j]"]['profs_list']=$tab_prof_grp[$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['id_groupe']];
											}

											$tab_ele['matieres']["$tab_matiere[$j]"]['coef']=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['coef'];
											$tab_ele['matieres']["$tab_matiere[$j]"]['bonus']=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['bonus'];
											$tab_ele['matieres']["$tab_matiere[$j]"]['nom_complet']=$tab_note["$lig_ele->login"][$id_classe[$i]]["$tab_matiere[$j]"]['nom_complet'];

											if(isset($tab_moy[$id_classe[$i]]["$tab_matiere[$j]"])) {
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']=$tab_moy[$id_classe[$i]]["$tab_matiere[$j]"]['moyenne'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']=$tab_moy[$id_classe[$i]]["$tab_matiere[$j]"]['min'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']=$tab_moy[$id_classe[$i]]["$tab_matiere[$j]"]['max'];

												if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"])) {
													$tab_ele['matieres']["$tab_matiere[$j]"]['app']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["info_dev"];
												}
												else {
													$tab_ele['matieres']["$tab_matiere[$j]"]['app']="-";
												}
											}
											else {
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['app']="-";
											}

										}
									}
									// Moyenne générale de l'élève
									if($tot_coef>0) {
										$moyenne=round(10*$tot_ele/$tot_coef)/10;
										$tab_ele['moyenne']=strtr($moyenne,".",",");
									}
									else {
										$tab_ele['moyenne']="-";
									}

									$tab_ele['moy_generale_classe']=$tab_moy_gen[$id_classe[$i]]['moyenne'];
									$tab_ele['moy_min_classe']=$tab_moy_gen[$id_classe[$i]]['min'];
									$tab_ele['moy_max_classe']=$tab_moy_gen[$id_classe[$i]]['max'];
									$tab_ele['avis']="-";

									bull_exb($tab_ele,$cpt_ele_clas);

									$cpt_ele_clas++;

								}
							}
						}

						// Datation du nom de fichier
						$instant=getdate();
						$heure=$instant['hours'];
						$minute=$instant['minutes'];
						$seconde=$instant['seconds'];
						$mois=$instant['mon'];
						$jour=$instant['mday'];
						$annee=$instant['year'];
						$chaine_tmp="$annee-".sprintf("%02d",$mois)."-".sprintf("%02d",$jour)."-".sprintf("%02d",$heure)."-".sprintf("%02d",$minute)."-".sprintf("%02d",$seconde);

						// Génération du fichier PDF
						$nom_bulletin='bulletins_examen_num_'.$id_exam.'_'.$chaine_tmp.'.pdf';
						$pdf->Output($nom_bulletin,'I');
						die();

					}
				}
			}
		}
	}
}



//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Examen blanc: Bulletins";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

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
	/*
	$tab_matiere=array();
	$tab_coef=array();
	$tab_bonus=array();
	while($lig=mysql_fetch_object($res_matieres)) {
		$tab_matiere[]=$lig->matiere;
		$tab_coef[]=$lig->coef;
		$tab_bonus[]=$lig->bonus;
	}
	*/

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1' target='_blank'>\n";
	echo "<p class='bold'>Choisissez les classes pour lesquelles vous souhaitez éditer des bulletins&nbsp;:</p>\n";
	for($i=0;$i<$nb_classes;$i++) {
		echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$tab_id_classe[$i]' ";
		echo "onchange=\"checkbox_change($i);\" ";
		echo "/>\n";
		echo "<label for='id_classe_$i'> <span id='texte_id_classe_$i'>$tab_classe[$i]</span></label><br />\n";
	}
	echo add_token_field();
	echo "<input type='submit' name='Imprimer' value='Imprimer' />\n";
	echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
	echo "<input type='hidden' name='mode' value='imprimer' />\n";
	echo "</form>\n";

	echo "<p><a href='#' onClick='ModifCase(true)'>Cocher</a> / <a href='#' onClick='ModifCase(false)'>décocher</a> toutes les classes</p>\n";

	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('id_classe_'+cpt)) {
		if(document.getElementById('id_classe_'+cpt).checked) {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_id_classe_'+cpt).style.fontWeight='normal';
		}
	}
}

function ModifCase(mode) {
	for (var k=0;k<$nb_classes;k++) {
		if(document.getElementById('id_classe_'+k)){
			document.getElementById('id_classe_'+k).checked=mode;
			checkbox_change(k);
		}
	}
}

</script>\n";

	//echo "<p style='color:red;'><b>A FAIRE&nbsp;:</b> Calculer les moyennes par matières,...</p>\n";
	echo "<p style='color:red;'><i>NOTES&nbsp;:</i> Les moyennes supposent actuellement que le référentiel des devoirs est 20.<br />Il faudra modifier pour prendre en compte des notes sur autre chose que 20.<br />Les 'bonus' consistent à ne compter que les points supérieurs à 10.<br />Ex.: Pour 12 (coef 3), 14 (coef 1) et 13 (coef 2 et bonus), le calcul est (12*3+14*1+(13-10)*2)/(3+1)</p>\n";

	//echo "<p><br /></p>\n";
	//echo "<p style='color:red;'><i>PROBLEME&nbsp;:</i> 3B1 moyenne min  '-' alors que cela devrait être '0' ???</p>\n";
	// CORRIGé

	//echo "<p><br /></p>\n";
	//echo "<p style='color:red;'><i>PROBLEME&nbsp;:</i> Les moyennes min/max/classes d'un 'enseignement' sont erronées si on a à la fois des notes de devoirs et des notes hors enseignement pour un même 'enseignement'.</p>\n";
	// CORRIGé

	//echo "<p><br /></p>\n";
	//echo "<p style='color:red;'><i>A VERIFIER&nbsp;:</i> Les moyennes min/max/classes d'un 'enseignement' sont calculées classe par classe alors qu'elles devraient l'être pour l'ensemble de l'examen.</p>\n";
	// CORRIGé

	echo "<p><br /></p>\n";
	echo "<p style='color:red;'><i>PROBLEME&nbsp;:</i> Les élèves qui changent de classe sont mal gérés.<br />Il faudrait choisir une période de référence pour les appartenances des élèves à une classe.</p>\n";

}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");

?>
