<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
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
$insert=mysql_query($sql);
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
/*
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);

$id_ex_grp=isset($_POST['id_ex_grp']) ? $_POST['id_ex_grp'] : (isset($_GET['id_ex_grp']) ? $_GET['id_ex_grp'] : NULL);

$reg_notes=isset($_POST['reg_notes']) ? $_POST['reg_notes'] : (isset($_GET['reg_notes']) ? $_GET['reg_notes'] : NULL);
$reg_eleves=isset($_POST['reg_eleves']) ? $_POST['reg_eleves'] : (isset($_GET['reg_eleves']) ? $_GET['reg_eleves'] : NULL);

// ATTENTION: Avec $id_exam/$id_groupe et $id_ex_grp on a une clé de trop...

//$modif_exam=isset($_POST['modif_exam']) ? $_POST['modif_exam'] : (isset($_GET['modif_exam']) ? $_GET['modif_exam'] : NULL);

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

	//if(isset($id_exam)) {
	if((isset($id_exam))&&(isset($matiere))) {
		$msg="";

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg="L'examen choisi (<i>$id_exam</i>) n'existe pas.<br />\n";
			unset($reg_eleves);
			unset($reg_notes);
		}
		else {
			$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$msg="Aucun groupe hors enseignement n'a été trouvé pour cet examen.<br />\n";
				unset($reg_eleves);
				unset($reg_notes);
			}
			else {
				$lig=mysql_fetch_object($res);
				$id_ex_grp=$lig->id;
			}
		}

		if($reg_eleves=='y') {
			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());

			//$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
			//$suppr=mysql_query($sql);
			$sql="SELECT login FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			$tab_ele_inscrits=array();
			$nb_suppr_ele=0;
			while($lig=mysql_fetch_object($res)) {
				$tab_ele_inscrits[]=$lig->login;
				if(!in_array($lig->login, $login_ele)) {
					$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp' AND login='$login_ele[$i]';";
					//echo "$sql<br />\n";
					$suppr=mysql_query($sql);
					if($suppr) {$nb_suppr_ele++;}
				}
			}
			if($nb_suppr_ele>0) {$msg.="$nb_suppr_ele élève(s) retiré(s).<br />";}

			$nb_ajout_ele=0;
			for($i=0;$i<count($login_ele);$i++) {
				if(!in_array($login_ele[$i], $tab_ele_inscrits)) {
					$sql="INSERT INTO ex_notes SET id_ex_grp='$id_ex_grp', login='$login_ele[$i]', statut='v';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
					if($insert) {$nb_ajout_ele++;}
				}
			}
			if($nb_ajout_ele>0) {$msg.="$nb_ajout_ele élève(s) ajouté(s).<br />";}
		}
		elseif($reg_notes=='y') {

			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());
			$note=isset($_POST['note']) ? $_POST['note'] : (isset($_GET['note']) ? $_GET['note'] : array());
		
			$msg="";
		
			for($i=0;$i<count($login_ele);$i++) {
				$elev_statut='';
				if(($note[$i]=='disp')){
					$elev_note='0';
					$elev_statut='disp';
				}
				elseif(($note[$i]=='abs')){
					$elev_note='0';
					$elev_statut='abs';
				}
				elseif(($note[$i]=='-')){
					$elev_note='0';
					$elev_statut='-';
				}
				elseif(ereg("^[0-9\.\,]{1,}$",$note[$i])) {
					$elev_note=str_replace(",", ".", "$note[$i]");
					if(($elev_note<0)||($elev_note>20)){
						$elev_note='';
						//$elev_statut='';
						$elev_statut='v';
					}
				}
				else{
					$elev_note='';
					//$elev_statut='';
					$elev_statut='v';
				}
				if(($elev_note!='')or($elev_statut!='')){
					$sql="UPDATE ex_notes SET note='$elev_note', statut='$elev_statut' WHERE id_ex_grp='$id_ex_grp' AND login='$login_ele[$i]';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="Erreur: $sql<br />";
					}
				}
			}
		
			if($msg=='') {
				$msg="Enregistrement effectué.";
			}

		}
	}
}
*/



if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

	if(isset($id_exam)) {
		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		//echo "$sql<br />\n";
		$res_test_id_exam=mysql_query($sql);
		if(mysql_num_rows($res_test_id_exam)==0) {
			$msg="L'examen choisi (<i>$id_exam</i>) n'existe pas.<br />\n";
		}
		else {
			$lig_exam=mysql_fetch_object($res_test_id_exam);
			$intitule_exam=$lig_exam->intitule;
			$description_exam=$lig_exam->description;
			$date_exam=$lig_exam->date;

			//===========================
			// Classes 
			$sql="SELECT c.classe, c.nom_complet, c.suivi_par, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="<p>Aucune classe n'est associée à l'examen???</p>\n";
			}
			else {
				$tab_id_classe=array();
				$tab_classe=array();
				$tab_classe_nom_complet=array();
				$tab_suivi_par=array();
				while($lig=mysql_fetch_object($res_classes)) {
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
				$res_matieres=mysql_query($sql);
				$nb_matieres=mysql_num_rows($res_matieres);
				if($nb_matieres==0) {
					$msg="<p>Aucune matière n'est associée à l'examen???</p>\n";
				}
				else {
					$tab_matiere=array();
					$tab_matiere_nom_complet=array();
					$tab_coef=array();
					$tab_bonus=array();
					while($lig=mysql_fetch_object($res_matieres)) {
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
							$sql="SELECT eg.id_dev, eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
							//echo "$sql<br />\n";
							$res_groupe=mysql_query($sql);
							if(mysql_num_rows($res_groupe)>0) {
								while($lig_groupe=mysql_fetch_object($res_groupe)) {

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

									$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev';";
									//echo "$sql<br />\n";
									$res_dev=mysql_query($sql);
									if(mysql_num_rows($res_dev)>0) {

										if(!in_array($lig_groupe->id_dev,$tab_dev)) {
											$tab_dev[]=$lig_groupe->id_dev;
					
											$sql="SELECT cd.nom_court, cd.nom_complet, cd.description, cd.date, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_cahier_notes=cd.id_racine AND cd.id='$lig_groupe->id_dev';";
											//echo "$sql<br />\n";
											$res_info_dev=mysql_query($sql);
					
											$lig_info_dev=mysql_fetch_object($res_info_dev);
											$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig_info_dev->periode' AND id_classe='$tab_id_classe[$i]';";
											//echo "$sql<br />\n";
											$res_per=mysql_query($sql);
											$lig_per=mysql_fetch_object($res_per);

											$tab_info_dev[$lig_groupe->id_dev]=$lig_info_dev->nom_court." ($lig_per->nom_periode)";
										}

										while($lig_dev=mysql_fetch_object($res_dev)) {
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

										/*
										if(!in_array($lig_groupe->id_dev,$tab_dev)) {
											$tab_dev[]=$lig_groupe->id_dev;
					
											$sql="SELECT cd.nom_court, cd.nom_complet, cd.description, cd.date, ccn.periode FROM cn_devoirs cd, cn_cahier_notes ccn WHERE ccn.id_cahier_notes=cd.id_racine AND cd.id='$lig_groupe->id_dev';";
											//echo "$sql<br />\n";
											$res_info_dev=mysql_query($sql);
					
											$lig_info_dev=mysql_fetch_object($res_info_dev);
											$sql="SELECT nom_periode FROM periodes WHERE num_periode='$lig_info_dev->periode' AND id_classe='$tab_id_classe[$i]';";
											//echo "$sql<br />\n";
											$res_per=mysql_query($sql);
											$lig_per=mysql_fetch_object($res_per);
					
											$titre="Devoir n°$lig_groupe->id_dev (<i>$lig_per->nom_periode</i>)";
											$texte="<p><b>".htmlentities($lig_info_dev->nom_court)."</b>";
											if($lig_info_dev->nom_court!=$lig_info_dev->nom_complet) {
												$texte.=" (<i>".htmlentities($lig_info_dev->nom_complet)."</i>)";
											}
											$texte.="<br />";
											if($lig_info_dev->description!='') {
												$texte.=htmlentities($lig_info_dev->description);
											}
											//$tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig_groupe->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
											$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig_groupe->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
										}
										*/
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
				
					for($j=0;$j<$nb_matieres;$j++) {
						$sql="SELECT en.* FROM ex_groupes eg, ex_notes en WHERE eg.id=en.id_ex_grp AND eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
						//echo "$sql<br />\n";
						$res_dev=mysql_query($sql);
						while($lig_dev=mysql_fetch_object($res_dev)) {
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
							}
						}

						for($i=0;$i<$nb_classes;$i++) {
							if(isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"])) {
								$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["moyenne"]=round(10*$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["total"]/$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]["effectif"])/10;
							}
						}

					}

					if($mode=='imprimer') {

						$tab_moy_gen=array();

						// Calcul des moyennes générales
						for($i=0;$i<$nb_classes;$i++) {
							$tab_moy_gen[$tab_id_classe[$i]]=array();

							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysql_query($sql);
							if(mysql_num_rows($res_ele)>0) {
								$tab_tmp=array();
								$tab_tmp['total']=0;

								$tab_tmp['min']=21;
								$tab_tmp['max']=-1;
								$effectif=0;

								while($lig_ele=mysql_fetch_object($res_ele)) {
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
						}



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

						for($i=0;$i<$nb_classes;$i++) {
							// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
							$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
							//echo "$sql<br />\n";
							$res_ele=mysql_query($sql);
							$cpt_ele_clas=0;
							if(mysql_num_rows($res_ele)>0) {
								while($lig_ele=mysql_fetch_object($res_ele)) {

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

									$tab_ele['classe']=$tab_classe[$i];
									$tab_ele['id_classe']=$tab_id_classe[$i];
									$tab_ele['classe_nom_complet']=$tab_classe_nom_complet[$i];
									$tab_ele['suivi_par']=$tab_suivi_par[$i];

									$tab_ele['intitule_exam']=$intitule_exam;
									$tab_ele['description_exam']=$description_exam;
									$tab_ele['date_exam']=$date_exam;

									// Récup infos Prof Principal (prof_suivi)
									$sql="SELECT u.* FROM j_eleves_professeurs jep, utilisateurs u WHERE jep.login='".$lig_ele->login."' AND id_classe='$tab_id_classe[$i]' AND jep.professeur=u.login;";
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

									// Régime et redoublement
									$sql="SELECT * FROM j_eleves_regime WHERE login='".$lig_ele->login."';";
									$res_ele_reg=mysql_query($sql);
									if(mysql_num_rows($res_ele_reg)>0) {
										$lig_ele_reg=mysql_fetch_object($res_ele_reg);
				
										$tab_ele['regime']=$lig_ele_reg->regime;
										$tab_ele['doublant']=$lig_ele_reg->doublant;
									}

									$sql="SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve ='".$tab_ele['elenoet']."' AND e.id = j.id_etablissement);";
									//echo "$sql<br />";
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


									$tot_ele=0;
									$tot_coef=0;
									$tab_ele['matieres']=array();
									for($j=0;$j<count($tab_matiere);$j++) {
										if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
											if($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut']!='') {
												$tab_ele['matieres']["$tab_matiere[$j]"]['statut']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['note']="";
											}
											else {
												if($tab_bonus[$j]=='n') {
													$tot_coef+=$tab_coef[$j];
													$tot_ele+=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']*$tab_coef[$j];
												}
												else {
													$tot_ele+=max(0,($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note']-10)*$tab_coef[$j]);
												}
												$tab_ele['matieres']["$tab_matiere[$j]"]['note']=strtr($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['note'],".",",");
												$tab_ele['matieres']["$tab_matiere[$j]"]['statut']="";
											}

											unset($current_id_dev);

											if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev'])) {
												$current_id_dev=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev'];
											}

											if(isset($current_id_dev)) {
												$tab_ele['matieres']["$tab_matiere[$j]"]['profs_list']=$tab_prof[$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['id_dev']];
											}

											$tab_ele['matieres']["$tab_matiere[$j]"]['coef']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['coef'];
											$tab_ele['matieres']["$tab_matiere[$j]"]['bonus']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['bonus'];
											$tab_ele['matieres']["$tab_matiere[$j]"]['nom_complet']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['nom_complet'];

											if(isset($current_id_dev)) {
												if(isset($tab_moy_dev[$current_id_dev])) {
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']=$tab_moy_dev[$current_id_dev]['moyenne'];
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']=$tab_moy_dev[$current_id_dev]['min'];
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']=$tab_moy_dev[$current_id_dev]['max'];
													$tab_ele['matieres']["$tab_matiere[$j]"]['app']=$tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['info_dev'];
												}
												else {
													// Ca ne devrait pas arriver
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']="-";
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']="-";
													$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']="-";
													$tab_ele['matieres']["$tab_matiere[$j]"]['app']="-";
												}
											}
											elseif(isset($tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"])) {
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]['moyenne'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]['min'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']=$tab_moy[$tab_id_classe[$i]]["$tab_matiere[$j]"]['max'];
												$tab_ele['matieres']["$tab_matiere[$j]"]['app']="-";
											}
											else {
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_min_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['moy_max_classe_grp']="-";
												$tab_ele['matieres']["$tab_matiere[$j]"]['app']="-";
											}
										}
										/*
										else {
											$csv.=";";
										}
										*/
									}
									if($tot_coef>0) {
										$moyenne=round(10*$tot_ele/$tot_coef)/10;
										$tab_ele['moyenne']=strtr($moyenne,".",",");
									}
									else {
										$tab_ele['moyenne']="-";
									}

									$tab_ele['moy_generale_classe']=$tab_moy_gen[$tab_id_classe[$i]]['moyenne'];
									$tab_ele['moy_min_classe']=$tab_moy_gen[$tab_id_classe[$i]]['min'];
									$tab_ele['moy_max_classe']=$tab_moy_gen[$tab_id_classe[$i]]['max'];
									$tab_ele['avis']="-";

									bull_exb($tab_ele,$cpt_ele_clas);

									$cpt_ele_clas++;

								}
							}
						}

						$nom_bulletin='bulletins_examen_num_'.$id_exam.'.pdf';
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
require_once("../lib/header.inc");
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
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
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

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {

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

	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
	echo "<p class='bold'>Choisissez les classes pour lesquelles vous souhaitez éditer des bulletins&nbsp;:</p>\n";
	for($i=0;$i<$nb_classes;$i++) {
		echo "<input type='checkbox' name='id_classe[]' id='id_classe_$i' value='$tab_id_classe[$i]' />\n";
		echo "<label for='id_classe_$i'> $tab_classe[$i]</label><br />\n";
	}
	echo "<input type='submit' name='Imprimer' value='Imprimer' />\n";
	echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
	echo "<input type='hidden' name='mode' value='imprimer' />\n";
	echo "</form>\n";

	//echo "<p style='color:red;'><b>A FAIRE&nbsp;:</b> Calculer les moyennes par matières,...</p>\n";
	echo "<p style='color:red;'><i>NOTES&nbsp;:</i> Les moyennes supposent actuellement que le référentiel des devoirs est 20.<br />Il faudra modifier pour prendre en compte des notes sur autre chose que 20.<br />Les 'bonus' consistent à ne compter que les points supérieurs à 10.<br />Ex.: Pour 12 (coef 3), 14 (coef 1) et 13 (coef 2 et bonus), le calcul est (12*3+14*1+(13-10)*2)/(3+1)</p>\n";

	echo "<p style='color:red;'><i>PROBLEME&nbsp;:</i> 3B1 moyenne min  '-' alors que cela devrait être '0' ???</p>\n";
}

echo "<p><br /></p>\n";
require("../lib/footer.inc.php");

?>
