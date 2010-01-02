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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/releve.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
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
			//===========================
			// Classes 
			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="<p>Aucune classe n'est associée à l'examen???</p>\n";
			}
			else {
				$tab_id_classe=array();
				$tab_classe=array();
				while($lig=mysql_fetch_object($res_classes)) {
					$tab_id_classe[]=$lig->id_classe;
					$tab_classe[]=$lig->classe;
				}
			
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
					$tab_coef=array();
					$tab_bonus=array();
					while($lig=mysql_fetch_object($res_matieres)) {
						$tab_matiere[]=$lig->matiere;
						$tab_coef[]=$lig->coef;
						$tab_bonus[]=$lig->bonus;
					}
					//===========================
				
					$tab_note=array();
					$tab_dev=array();
					$tab_bull=array();
					for($i=0;$i<$nb_classes;$i++) {
						//echo "\$tab_id_classe[$i]=$tab_id_classe[$i]<br />";
						//echo "\$tab_classe[$i]=$tab_classe[$i]<br />";
						for($j=0;$j<$nb_matieres;$j++) {
							//$sql="SELECT * FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
							$sql="SELECT eg.id_dev, eg.type, eg.valeur, eg.id_groupe FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
							//echo "$sql<br />\n";
							$res_groupe=mysql_query($sql);
							if(mysql_num_rows($res_groupe)>0) {
								while($lig_groupe=mysql_fetch_object($res_groupe)) {

									if($lig_groupe->type=='moy_bull') {
										$sql="SELECT * FROM matieres_notes WHERE id_groupe='$lig_groupe->id_groupe' AND periode='$lig_groupe->valeur';";
										//echo "$sql<br />\n";
										$res_bull=mysql_query($sql);
										if(mysql_num_rows($res_bull)>0) {
											while($lig_bull=mysql_fetch_object($res_bull)) {
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_bull->statut;
												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_bull->note;

												$tab_note["$lig_bull->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["infobulle"]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;
											}

											if(!in_array('bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$tab_bull)) {
												$tab_bull[]='bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur;

												$titre="Moyenne du bulletin (<i>$lig_per->nom_periode</i>)";
												$texte="<p><b>Moyenne du bulletin sur la période $lig_per->nom_periode</b>";
												$texte.="<br />";

												$reserve_header_tabdiv_infobulle[]=creer_div_infobulle('div_bull_'.$lig_groupe->id_groupe.'_'.$lig_groupe->valeur,$titre,"",$texte,"",30,0,'y','y','n','n');
											}

										}
									}
									else {
										$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev';";
										//echo "$sql<br />\n";
										$res_dev=mysql_query($sql);
										if(mysql_num_rows($res_dev)>0) {
											while($lig_dev=mysql_fetch_object($res_dev)) {
												//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
												//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
												$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]=$lig_groupe->id_dev;
											}

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
							}
						}
					}

					if($mode=='csv') {
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
							$res_ele=mysql_query($sql);
							if(mysql_num_rows($res_ele)>0) {
								while($lig_ele=mysql_fetch_object($res_ele)) {
									$tot_ele=0;
									$tot_coef=0;
									$csv.=$tab_classe[$i].";";

									$csv.=$lig_ele->login.";".casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2').";";
									for($j=0;$j<count($tab_matiere);$j++) {
										if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
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

						$now = gmdate('D, d M Y H:i:s') . ' GMT';
						header('Content-Type: text/x-csv');
						header('Expires: ' . $now);
						// lem9 & loic1: IE need specific headers
						if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
							header('Content-Disposition: inline; filename="' . $nom_fic . '"');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: public');
						} else {
							header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
							header('Pragma: no-cache');
						}

						echo $csv;
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
$titre_page = "Examen blanc: Relevé";
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
	//===========================
/*
	$tab_note=array();
	$tab_dev=array();
	for($i=0;$i<$nb_classes;$i++) {
		for($j=0;$j<$nb_matieres;$j++) {
			//$sql="SELECT * FROM ex_groupes eg WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]';";
			$sql="SELECT eg.id_dev FROM ex_groupes eg, j_groupes_classes jgc WHERE eg.id_exam='$id_exam' AND eg.matiere='$tab_matiere[$j]' AND jgc.id_groupe=eg.id_groupe AND jgc.id_classe='$tab_id_classe[$i]';";
			//echo "$sql<br />\n";
			$res_groupe=mysql_query($sql);
			if(mysql_num_rows($res_groupe)>0) {
				while($lig_groupe=mysql_fetch_object($res_groupe)) {
					$sql="SELECT * FROM cn_notes_devoirs WHERE id_devoir='$lig_groupe->id_dev';";
					//echo "$sql<br />\n";
					$res_dev=mysql_query($sql);
					if(mysql_num_rows($res_dev)>0) {
						while($lig_dev=mysql_fetch_object($res_dev)) {
							//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
							//$tab_note["$lig_dev->login"]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
							$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["statut"]=$lig_dev->statut;
							$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["note"]=$lig_dev->note;
							$tab_note["$lig_dev->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]["id_dev"]=$lig_groupe->id_dev;
						}
	
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
							$tabdiv_infobulle[]=creer_div_infobulle('div_dev_'.$lig_groupe->id_dev,$titre,"",$texte,"",30,0,'y','y','n','n');
						}
					}
				}
			}
*/
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
/*
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
			}
		}
	}
*/
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//unset($tabdiv_infobulle);
	if(isset($reserve_header_tabdiv_infobulle)) {$tabdiv_infobulle=$reserve_header_tabdiv_infobulle;}

	echo "<div style='float:right; width: 5em; text-align:center; border: 1px solid black;'>\n";
	echo "<a href='releve.php?id_exam=$id_exam&amp;mode=csv'";
	echo ">CSV</a>\n";
	echo "</div>\n";

	//$csv="";
	for($i=0;$i<$nb_classes;$i++) {
		echo "<p class='bold'>Classe $tab_classe[$i]</p>\n";

		// Problème avec les élèves qui ont changé de classe en cours d'année... il faudrait choisir une période de référence pour l'appartenance de classe
		$sql="SELECT DISTINCT e.nom, e.prenom, e.login FROM eleves e, j_eleves_classes jec WHERE jec.id_classe='$tab_id_classe[$i]' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
		//echo "$sql<br />\n";
		$res_ele=mysql_query($sql);
		$nb_ele=mysql_num_rows($res_ele);
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
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$tot_ele=0;
			$tot_coef=0;
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>".casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</td>\n";
			for($j=0;$j<count($tab_matiere);$j++) {
				echo "<td>\n";
				if(isset($tab_note["$lig_ele->login"][$tab_id_classe[$i]]["$tab_matiere[$j]"]['statut'])) {
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
