<?php
/*
*
* Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

$sql="SELECT 1=1 FROM droits WHERE id='/saisie/saisie_socle.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/saisie/saisie_socle.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Socle: Saisie',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// A FAIRE : Modifier pour permettre tout de même une consultation sans droits de saisie.

if(!getSettingAOui("SocleSaisieComposantes")) {
	header("Location: ../accueil.php?msg=Accès non autorisé");
	die();
}

if(!getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"])) {
	if(($_SESSION['statut']=="professeur")&&(getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION["login"]))) {
		// Accès autorisé
	}
	else {
		header("Location: ../accueil.php?msg=Accès non autorisé");
		die();
	}
}

$SocleSaisieComposantesMode=getSettingValue("SocleSaisieComposantesMode");
if($SocleSaisieComposantesMode=='') {
	$SocleSaisieComposantesMode=1;
}

$msg="";
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);

$cycle_particulier=isset($_POST['cycle_particulier']) ? $_POST['cycle_particulier'] : (isset($_GET['cycle_particulier']) ? $_GET['cycle_particulier'] : NULL);
$checked_cycle_particulier["courant_eleve"]="";
$checked_cycle_particulier[2]="";
$checked_cycle_particulier[3]="";
$checked_cycle_particulier[4]="";
if((isset($cycle_particulier))&&($cycle_particulier=="")) {
	unset($cycle_particulier);
	$checked_cycle_particulier["courant_eleve"]=" checked";
}
else {
	$checked_cycle_particulier[$cycle_particulier]=" checked";
}

if(isset($id_classe)) {
	unset($id_groupe);
}

if(($_SESSION['statut']=="professeur")&&(isset($id_classe))&&
((!getSettingAOui("SocleSaisieComposantes_PP"))||(!is_pp($_SESSION["login"],$id_classe)))) {
	//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre groupe ou classe</a></p>";
	//echo "<p style='color:red'>Vous n'avez pas accès à cette classe.</p>";
	//require("../lib/footer.inc.php");
	//die();

	$msg="Vous n'avez pas accès à cette classe.<br />";
	unset($id_classe);
}

$gepiYear=getSettingValue("gepiYear");
$gepiYear_debut=mb_substr($gepiYear, 0, 4);
if(!preg_match("/^20[0-9]{2}/", $gepiYear_debut)) {
	header("Location: ../accueil.php?msg=Année scolaire non définie dans Gestion générale/Configuration générale.");
	die();
}

//==============================================================
// Pour tenir compte d'un ajout de champ 'annee' oublié en 1.7.1
check_tables_modifiees();

// Suppression de l'index sur ine+cycle pour ne conserver que celui sur ine+cycle+annee
$sql="show index from socle_eleves_syntheses where key_name='ine';";
$test_index=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($test_index)>0) {
	$sql="ALTER TABLE socle_eleves_syntheses DROP INDEX ine;";
	$del=mysqli_query($mysqli, $sql);
}
//==============================================================

// Etat d'ouverture ou non des saisies
$max_per=0;
$sql="SELECT MAX(num_periode) AS max_per FROM periodes;";
$res_max=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_max)==0) {
	echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucune classe avec périodes ne semble définie.</p>";
	require("../lib/footer.inc.php");
	die();
}
$lig_max=mysqli_fetch_object($res_max);
$max_per=$lig_max->max_per;

$SocleOuvertureSaisieComposantes=array();
for($i=1;$i<$max_per+1;$i++) {
	$SocleOuvertureSaisieComposantes[$i]=getSettingAOui("SocleOuvertureSaisieComposantesPeriode".$i);
}

$tab_domaine_socle=array();
$tab_domaine_socle["CPD_FRA"]="Comprendre, s'exprimer en utilisant la langue française à l'oral et à l'écrit";
$tab_domaine_socle["CPD_ETR"]="Comprendre, s'exprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale";
$tab_domaine_socle["CPD_SCI"]="Comprendre, s'exprimer en utilisant les langages mathématiques, scientifiques et informatiques";
$tab_domaine_socle["CPD_ART"]="Comprendre, s'exprimer en utilisant les langages des arts et du corps";
$tab_domaine_socle["MET_APP"]="Les méthodes et outils pour apprendre";
$tab_domaine_socle["FRM_CIT"]="La formation de la personne et du citoyen";
$tab_domaine_socle["SYS_NAT"]="Les systèmes naturels et les systèmes techniques";
$tab_domaine_socle["REP_MND"]="Les représentations du monde et l'activité humaine";

$tab_traduction_niveau=array();
$tab_traduction_niveau[0]="";
$tab_traduction_niveau[1]="MI";
$tab_traduction_niveau[2]="MF";
$tab_traduction_niveau[3]="MS";
$tab_traduction_niveau[4]="TBM";

$tab_traduction_niveau_couleur=array();
$tab_traduction_niveau_couleur[0]="";
$tab_traduction_niveau_couleur[1]="<span style='color:red' title=\"\">MI</span>";
$tab_traduction_niveau_couleur[2]="<span style='color:orange' title=\"\">MF</span>";
$tab_traduction_niveau_couleur[3]="<span style='color:green' title=\"\">MS</span>";
$tab_traduction_niveau_couleur[4]="<span style='color:blue' title=\"\">TBM</span>";

$tab_couleur_niveau=array();
$tab_couleur_niveau[0]="";
$tab_couleur_niveau[1]="red";
$tab_couleur_niveau[2]="orange";
$tab_couleur_niveau[3]="green";
$tab_couleur_niveau[4]="blue";

//20170302
$tab_types_enseignements_complement=get_tab_types_enseignements_complement();

// 20180210
if(getSettingAOui('langue_vivante_regionale')) {
	$tab_type_LVR=get_tab_types_LVR();
}

// 20170521: Ménage:
//===========================================
$sql="DELETE FROM socle_eleves_composantes WHERE ine='';";
$del=mysqli_query($mysqli, $sql);
$sql="DELETE FROM socle_eleves_enseignements_complements WHERE ine='';";
$del=mysqli_query($mysqli, $sql);
$sql="DELETE FROM socle_eleves_syntheses WHERE ine='';";
$del=mysqli_query($mysqli, $sql);
//===========================================

//debug_var();

if((isset($_POST['enregistrer_saisies']))&&(isset($periode))) {
	check_token();

	if(!$SocleOuvertureSaisieComposantes[$periode]) {
		$msg="La saisie est fermée.<br />";
	}
	else {
		// SocleSaisieComposantesForcer_PP
		// SocleSaisieComposantesForcer_cpe
		// SocleSaisieComposantesForcer_scolarite
		// SocleSaisieComposantesForcer_professeur

		// SocleSaisieComposantesConcurrentes derniere ou meilleure
		$SocleSaisieComposantesConcurrentes=getSettingValue("SocleSaisieComposantesConcurrentes");

		$niveau_maitrise=isset($_POST["niveau_maitrise"]) ? $_POST["niveau_maitrise"] : array();
		if((isset($_POST["forcer"]))&&
		(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))) {
			$forcer="y";
		}
		else {
			$forcer="n";

			// Gérer/Tester le cas PP
			if((!isset($id_groupe))&&(isset($id_classe))) {
				if(($_SESSION['statut']=="professeur")&&
				(isset($_POST["forcer"]))&&
				(getSettingAOui("SocleSaisieComposantesForcer_PP"))&&
				(is_pp($_SESSION['login'], $id_classe))) {
					$forcer="y";
				}
			}
		}

		$SocleSaisieSyntheses=false;
		if(getSettingAOui("SocleSaisieSyntheses_".$_SESSION["statut"])) {
			$SocleSaisieSyntheses=true;
		}
		elseif(($_SESSION["statut"]=="professeur")&&
		(isset($id_classe))&&
		(getSettingAOui("SocleSaisieSyntheses_PP"))&&
		(is_pp($_SESSION["login"], $id_classe))) {
			$SocleSaisieSyntheses=true;
		}

		$cpt_reg=0;
		$nb_err=0;
		$nb_refus_baisser=0;
		// Si id_groupe, vérifier que l'utilisateur a le droit de saisie.
		if(isset($id_groupe)) {
			$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE login='".$_SESSION["login"]."' AND id_groupe='".$id_groupe."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.="Vous n'avez pas le droit de saisir les composantes pour ce groupe.<br />";
			}
			else {
				$tab_ine_du_groupe=array();
				$sql="SELECT DISTINCT no_gep FROM eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND periode='".$periode."' AND id_groupe='".$id_groupe."' AND e.no_gep!='';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig=mysqli_fetch_object($res)) {
					$tab_ine_du_groupe[]=$lig->no_gep;
				}

				foreach($niveau_maitrise as $current_element => $valeur) {
					if(($valeur!="")&&($valeur!="1")&&($valeur!="2")&&($valeur!="3")&&($valeur!="4")) {
						// 20170521
						$tmp_tab=explode("|", $current_element);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];
						$code=$tmp_tab[2];

						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte.<br />";
						}
						else {
							$msg.=get_nom_prenom_from_INE($ine)."&nbsp;: Valeur invalide pour '$current_element'&nbsp;: '$valeur'.<br />";
						}
					}
					else {
						//$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code
						$tmp_tab=explode("|", $current_element);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];
						$code=$tmp_tab[2];

						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte.<br />";
						}
						elseif(!in_array($ine, $tab_ine_du_groupe)) {
							$msg.="L'élève $ine <em>(".get_nom_prenom_from_INE($ine).")</em> n'est pas membre de la classe.<br />";
						}
						else {
							// 20191027
							if($SocleSaisieComposantesMode==1) {
								$sql="SELECT * FROM socle_eleves_composantes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND annee='".$gepiYear_debut."';";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									if($valeur!="") {
										$sql="INSERT INTO socle_eleves_composantes SET ine='".$ine."', cycle='".$cycle."', annee='".$gepiYear_debut."', code_composante='".$code."', niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."', periode='".$periode."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if($insert) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de l'enregistrement $sql<br />";
											$nb_err++;
										}
									}
								}
								else {
									$lig=mysqli_fetch_object($test);
									if($lig->niveau_maitrise!=$valeur) {
										$enregistrer_valeur="n";
										if($SocleSaisieComposantesConcurrentes!="meilleure") {
											$enregistrer_valeur="y";
										}
										else {
											// La saisie est-elle meilleure ou a-t-on forcé
											if($lig->niveau_maitrise=="") {
												$enregistrer_valeur="y";
											}
											elseif($valeur=="") {
												// On veut vider l'enregistrement
												if($forcer=="y") {
													$enregistrer_valeur="y";
												}
												else {
													$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de vider le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
													$nb_refus_baisser++;
												}
											}
											elseif($lig->niveau_maitrise>$valeur) {
												// On veut baisser le niveau de maitrise
												if($forcer=="y") {
													$enregistrer_valeur="y";
												}
												else {
													$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de baisser le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
													$nb_refus_baisser++;
												}
											}
											else {
												// La valeur est meilleure, on enregistre
												$enregistrer_valeur="y";
											}
										}

										if($enregistrer_valeur=="y") {
											if($valeur=="") {
												$sql="DELETE FROM socle_eleves_composantes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND annee='".$gepiYear_debut."';";
											}
											else {
												$sql="UPDATE socle_eleves_composantes SET niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."' AND code_composante='".$code."' AND periode='".$periode."';";
											}
											//echo "$sql<br />";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if($update) {
												$cpt_reg++;
											}
											else {
												$msg.="Erreur lors de la mise à jour $sql<br />";
												$nb_err++;
											}
										}
									}
								}
							}
							else {
								// 20191027
								$sql="SELECT * FROM socle_eleves_composantes_groupes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND annee='".$gepiYear_debut."' AND id_groupe='".$id_groupe."';";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									if($valeur!="") {
										$sql="INSERT INTO socle_eleves_composantes_groupes SET ine='".$ine."', cycle='".$cycle."', annee='".$gepiYear_debut."', code_composante='".$code."', niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."', periode='".$periode."', id_groupe='".$id_groupe."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if($insert) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de l'enregistrement $sql<br />";
											$nb_err++;
										}
									}
								}
								else {
									$lig=mysqli_fetch_object($test);
									if($lig->niveau_maitrise!=$valeur) {
										/*
										$enregistrer_valeur="n";
										if($SocleSaisieComposantesConcurrentes!="meilleure") {
											$enregistrer_valeur="y";
										}
										else {
											// La saisie est-elle meilleure ou a-t-on forcé
											if($lig->niveau_maitrise=="") {
												$enregistrer_valeur="y";
											}
											elseif($valeur=="") {
												// On veut vider l'enregistrement
												if($forcer=="y") {
													$enregistrer_valeur="y";
												}
												else {
													$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de vider le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
													$nb_refus_baisser++;
												}
											}
											elseif($lig->niveau_maitrise>$valeur) {
												// On veut baisser le niveau de maitrise
												if($forcer=="y") {
													$enregistrer_valeur="y";
												}
												else {
													$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de baisser le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
													$nb_refus_baisser++;
												}
											}
											else {
												// La valeur est meilleure, on enregistre
												$enregistrer_valeur="y";
											}
										}
										*/

										//if($enregistrer_valeur=="y") {
											if($valeur=="") {
												$sql="DELETE FROM socle_eleves_composantes_groupes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND id_groupe='".$id_groupe."' AND annee='".$gepiYear_debut."';";
											}
											else {
												$sql="UPDATE socle_eleves_composantes_groupes SET niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."' AND code_composante='".$code."' AND periode='".$periode."' AND id_groupe='".$id_groupe."';";
											}
											//echo "$sql<br />";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if($update) {
												$cpt_reg++;
											}
											else {
												$msg.="Erreur lors de la mise à jour $sql<br />";
												$nb_err++;
											}
										//}
									}
								}
							}
						}
					}
				}

				$enseignement_complement=isset($_POST["enseignement_complement"]) ? $_POST["enseignement_complement"] : NULL;
				if(isset($enseignement_complement)) {
					foreach($enseignement_complement as $ine => $positionnement) {
						// 20170521 : 
						if($ine=="") {
							$msg.="Enseignement de complément&nbsp;: Un identifiant INE est vide pour un élève. Il ne peut pas être pris en compte.<br />";
						}
						else {
							$sql="SELECT * FROM socle_eleves_enseignements_complements WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO socle_eleves_enseignements_complements SET ine='".$ine."', 
											id_groupe='".$id_groupe."', 
											positionnement='".$positionnement."', 
											login_saisie='".$_SESSION['login']."', 
											date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$cpt_reg++;
								}
								else {
									$msg.="Erreur lors de l'enregistrement $sql<br />";
									$nb_err++;
								}
							}
							else {
								$lig=mysqli_fetch_object($test);

								if($positionnement!=$lig->positionnement) {
									$sql="UPDATE socle_eleves_enseignements_complements SET 
													positionnement='".$positionnement."', 
													login_saisie='".$_SESSION['login']."', 
													date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' 
												WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if($update) {
										$cpt_reg++;
									}
									else {
										$msg.="Erreur lors de la mise à jour $sql<br />";
										$nb_err++;
									}
								}
							}
						}
					}
				}

				// 20180211
				$lvr=isset($_POST["lvr"]) ? $_POST["lvr"] : NULL;
				if(isset($lvr)) {
					foreach($lvr as $ine => $positionnement) {
						// 20170521 : 
						if($ine=="") {
							$msg.="LVR&nbsp;: Un identifiant INE est vide pour un élève. Il ne peut pas être pris en compte.<br />";
						}
						else {
							$sql="SELECT * FROM socle_eleves_lvr WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								$sql="INSERT INTO socle_eleves_lvr SET ine='".$ine."', 
											id_groupe='".$id_groupe."', 
											positionnement='".$positionnement."', 
											login_saisie='".$_SESSION['login']."', 
											date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$cpt_reg++;
								}
								else {
									$msg.="Erreur lors de l'enregistrement $sql<br />";
									$nb_err++;
								}
							}
							else {
								$lig=mysqli_fetch_object($test);

								if($positionnement!=$lig->positionnement) {
									$sql="UPDATE socle_eleves_lvr SET 
													positionnement='".$positionnement."', 
													login_saisie='".$_SESSION['login']."', 
													date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' 
												WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if($update) {
										$cpt_reg++;
									}
									else {
										$msg.="Erreur lors de la mise à jour $sql<br />";
										$nb_err++;
									}
								}
							}
						}
					}
				}

				$indice_synthese=isset($_POST['indice_synthese']) ? $_POST['indice_synthese'] : array();
				if(($SocleSaisieSyntheses)&&(count($indice_synthese)>0)) {
					for($loop=0;$loop<count($indice_synthese);$loop++) {
						$tmp_tab=explode("|", $indice_synthese[$loop]);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];

						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte pour la synthèse.<br />";
						}
						elseif(!in_array($ine, $tab_ine_du_groupe)) {
							$msg.="L'élève $ine <em>(".get_nom_prenom_from_INE($ine).")</em> n'est pas membre de l'enseignement.<br />";
						}
						else {

							if((isset($NON_PROTECT["synthese_".$ine."_".$cycle]))&&
							(($NON_PROTECT["synthese_".$ine."_".$cycle]!="")||($forcer=="y"))) {
								//$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese_".$ine."_".$cycle]));
								$synthese=$NON_PROTECT["synthese_".$ine."_".$cycle];
								$synthese=trim(suppression_sauts_de_lignes_surnumeraires($synthese));

								$sql="SELECT * FROM socle_eleves_syntheses WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									if($synthese!="") {
										$sql="INSERT INTO socle_eleves_syntheses SET ine='".$ine."', 
													cycle='".$cycle."', 
													annee='".$gepiYear_debut."', 
													synthese='".mysqli_real_escape_string($GLOBALS["mysqli"], $synthese)."', 
													login_saisie='".$_SESSION['login']."', 
													date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if($insert) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de l'enregistrement $sql<br />";
											$nb_err++;
										}
									}
								}
								else {
									$lig=mysqli_fetch_object($test);

									if($synthese=="") {
										if($forcer=="y") {
											$sql="DELETE FROM socle_eleves_syntheses WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
											//echo "$sql<br />";
											$del=mysqli_query($GLOBALS["mysqli"], $sql);
											if($del) {
												$cpt_reg++;
											}
											else {
												$msg.="Erreur lors de la suppression $sql<br />";
												$nb_err++;
											}
										}
									}
									elseif($synthese!=$lig->synthese) {
										$sql="UPDATE socle_eleves_syntheses SET synthese='".mysqli_real_escape_string($GLOBALS["mysqli"], $synthese)."', 
														login_saisie='".$_SESSION['login']."', 
														date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' 
													WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
										//echo "$sql<br />";
										$update=mysqli_query($GLOBALS["mysqli"], $sql);
										if($update) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de la mise à jour $sql<br />";
											$nb_err++;
										}
									}
								}
							}
							/*
							else {
								echo "\$NON_PROTECT[\"synthese_".$ine."_".$cycle."] est non défini.<br />";
							}
							*/

						}
					}
				}
			}

			if(($nb_refus_baisser>0)&&(
				(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))||
				(
					($_SESSION['statut']=="professeur")&&(isset($id_classe))&&(getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login'], $id_classe))
				)
			)) {
				$msg.="Un ou des refus de baisser un niveau de compétence a été signalé, mais vous pourriez forcer la baisse en cochant la case idoine en début du formulaire.<br />";
			}

			$msg.=$cpt_reg." enregistrement(s) effectué(s).<br />";
			$msg.=$nb_err." erreur(s).<br />";
		}
		elseif(isset($id_classe)) {
			$acces_saisie="n";
			if($_SESSION["statut"]=="scolarite") {
				if(getSettingAOui('SocleSaisieComposantes_scolariteToutesClasses')) {
					$acces_saisie="y";
				}
				else {
					$sql="SELECT 1=1 FROM j_scol_classes WHERE login='".$_SESSION["login"]."' AND id_classe='".$id_classe."';";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$acces_saisie="y";
					}
				}
			}
			elseif($_SESSION["statut"]=="cpe") {
				//if(getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
				if(getSettingAOui('SocleSaisieComposantes_cpeToutesClasses')) {
					$acces_saisie="y";
				}
				else {
					$sql="SELECT 1=1 FROM j_eleves_cpe jec, j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe='$id_classe'";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test)>0) {
						$acces_saisie="y";
					}
				}
			}
			elseif(($_SESSION["statut"]=="professeur")&&(getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION['login'], $id_classe))) {
				$acces_saisie="y";
			}

			if($acces_saisie=="n") {
				$msg.="Saisie non autorisée sur cette classe.<br />";
			}
			else {

				$tab_ine_du_groupe=array();
				$sql="SELECT DISTINCT no_gep FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND id_classe='".$id_classe."';";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig=mysqli_fetch_object($res)) {
					$tab_ine_du_groupe[]=$lig->no_gep;
				}

				foreach($niveau_maitrise as $current_element => $valeur) {
					if(($valeur!="")&&($valeur!="1")&&($valeur!="2")&&($valeur!="3")&&($valeur!="4")) {
						// 20170521
						$tmp_tab=explode("|", $current_element);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];
						$code=$tmp_tab[2];

						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte.<br />";
						}
						else {
							$msg.=get_nom_prenom_from_INE($ine)."&nbsp;: Valeur invalide pour '$current_element'&nbsp;: '$valeur'.<br />";
						}
					}
					else {
						//$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code
						$tmp_tab=explode("|", $current_element);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];
						$code=$tmp_tab[2];

						// 20170521
						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte pour la composante $code.<br />";
						}
						elseif(!in_array($ine, $tab_ine_du_groupe)) {
							$msg.="L'élève $ine <em>(".get_nom_prenom_from_INE($ine).")</em> n'est pas membre de la classe.<br />";
						}
						else {
							$sql="SELECT * FROM socle_eleves_composantes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND annee='".$gepiYear_debut."';";
							//echo "$sql<br />";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test)==0) {
								if($valeur!="") {
									$sql="INSERT INTO socle_eleves_composantes SET ine='".$ine."', cycle='".$cycle."', annee='".$gepiYear_debut."', code_composante='".$code."', niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."', periode='".$periode."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if($insert) {
										$cpt_reg++;
									}
									else {
										$msg.="Erreur lors de l'enregistrement $sql<br />";
										$nb_err++;
									}
								}
							}
							else {
								$lig=mysqli_fetch_object($test);
								if($lig->niveau_maitrise!=$valeur) {
									$enregistrer_valeur="n";
									if($SocleSaisieComposantesConcurrentes!="meilleure") {
										$enregistrer_valeur="y";
									}
									else {
										// La saisie est-elle meilleure ou a-t-on forcé
										if($lig->niveau_maitrise=="") {
											$enregistrer_valeur="y";
										}
										elseif($valeur=="") {
											// On veut vider l'enregistrement
											if($forcer=="y") {
												$enregistrer_valeur="y";
											}
											else {
												$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de vider le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
												$nb_refus_baisser++;
											}
										}
										elseif($lig->niveau_maitrise>$valeur) {
											// On veut baisser le niveau de maitrise
											if($forcer=="y") {
												$enregistrer_valeur="y";
											}
											else {
												$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de baisser le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
												$nb_refus_baisser++;
											}
										}
										else {
											// La valeur est meilleure, on enregistre
											$enregistrer_valeur="y";
										}
									}

									if($enregistrer_valeur=="y") {
										if($valeur=="") {
											$sql="DELETE FROM socle_eleves_composantes WHERE ine='".$ine."' AND cycle='".$cycle."' AND code_composante='".$code."' AND periode='".$periode."' AND annee='".$gepiYear_debut."';";
										}
										else {
											$sql="UPDATE socle_eleves_composantes SET niveau_maitrise='".$valeur."', login_saisie='".$_SESSION['login']."', date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."' AND code_composante='".$code."' AND periode='".$periode."';";
										}
										//echo "$sql<br />";
										$update=mysqli_query($GLOBALS["mysqli"], $sql);
										if($update) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de la mise à jour $sql<br />";
											$nb_err++;
										}
									}
								}
							}
						}
					}
				}

				// 20180603
				$sql="SELECT DISTINCT jgec.* FROM j_groupes_enseignements_complement jgec, 
								j_groupes_classes jgc 
							WHERE jgec.id_groupe=jgc.id_groupe AND 
								jgc.id_classe='".$id_classe."' AND 
								jgec.code!='';";
				//echo "$sql<br />";
				$res_jgec=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_jgec)>0) {
					while($lig_jgec=mysqli_fetch_object($res_jgec)) {
						$id_groupe=$lig_jgec->id_groupe;

						$enseignement_complement=isset($_POST["enseignement_complement".$id_groupe]) ? $_POST["enseignement_complement".$id_groupe] : NULL;
						if(isset($enseignement_complement)) {
							foreach($enseignement_complement as $ine => $positionnement) {
								// 20170521 : 
								if($ine=="") {
									$msg.="Enseignement de complément&nbsp;: Un identifiant INE est vide pour un élève. Il ne peut pas être pris en compte.<br />";
								}
								elseif(!preg_match("/^[0-9]{1,}$/", $positionnement)) {
									$msg.="Positionnement ".$positionnement." invalide pour l'enseignement de complément $id_groupe pour l'élève $ine.<br />";
								}
								else {
									$sql="SELECT jgec.* FROM j_groupes_enseignements_complement jgec 
													WHERE jgec.id_groupe='".$id_groupe."' AND 
														jgec.code!='';";
									$test=mysqli_query($mysqli, $sql);
									if(mysqli_num_rows($test)>0) {

										$sql="SELECT * FROM socle_eleves_enseignements_complements WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
										//echo "$sql<br />";
										$test=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($test)==0) {
											$sql="INSERT INTO socle_eleves_enseignements_complements SET ine='".$ine."', 
														id_groupe='".$id_groupe."', 
														positionnement='".$positionnement."', 
														login_saisie='".$_SESSION['login']."', 
														date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if($insert) {
												$cpt_reg++;
											}
											else {
												$msg.="Erreur lors de l'enregistrement $sql<br />";
												$nb_err++;
											}
										}
										else {
											$lig=mysqli_fetch_object($test);

											if($positionnement!=$lig->positionnement) {
												$sql="UPDATE socle_eleves_enseignements_complements SET 
																positionnement='".$positionnement."', 
																login_saisie='".$_SESSION['login']."', 
																date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' 
															WHERE ine='".$ine."' AND id_groupe='".$id_groupe."';";
												//echo "$sql<br />";
												$update=mysqli_query($GLOBALS["mysqli"], $sql);
												if($update) {
													$cpt_reg++;
												}
												else {
													$msg.="Erreur lors de la mise à jour $sql<br />";
													$nb_err++;
												}
											}
										}
									}
								}
							}
						}
					}
					unset($id_groupe);
				}

				// 20180603 : A FAIRE : Permettre aussi la saisie LVR


				$indice_synthese=isset($_POST['indice_synthese']) ? $_POST['indice_synthese'] : array();
				if(($SocleSaisieSyntheses)&&(count($indice_synthese)>0)) {
					for($loop=0;$loop<count($indice_synthese);$loop++) {
						$tmp_tab=explode("|", $indice_synthese[$loop]);
						$ine=$tmp_tab[0];
						$cycle=$tmp_tab[1];

						if($ine=="") {
							$msg.="Un identifiant INE est vide. Il ne peut pas être pris en compte pour la synthèse.<br />";
						}
						elseif(!in_array($ine, $tab_ine_du_groupe)) {
							$msg.="L'élève $ine <em>(".get_nom_prenom_from_INE($ine).")</em> n'est pas membre de la classe.<br />";
						}
						else {

							if((isset($NON_PROTECT["synthese_".$ine."_".$cycle]))&&
							(($NON_PROTECT["synthese_".$ine."_".$cycle]!="")||($forcer=="y"))) {
								//$synthese=traitement_magic_quotes(corriger_caracteres($NON_PROTECT["synthese_".$ine."_".$cycle]));
								$synthese=$NON_PROTECT["synthese_".$ine."_".$cycle];
								$synthese=trim(suppression_sauts_de_lignes_surnumeraires($synthese));

								$sql="SELECT * FROM socle_eleves_syntheses WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
								//echo "$sql<br />";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test)==0) {
									if($synthese!="") {
										$sql="INSERT INTO socle_eleves_syntheses SET ine='".$ine."', 
													cycle='".$cycle."', 
													annee='".$gepiYear_debut."', 
													synthese='".mysqli_real_escape_string($GLOBALS["mysqli"], $synthese)."', 
													login_saisie='".$_SESSION['login']."', 
													date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if($insert) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de l'enregistrement $sql<br />";
											$nb_err++;
										}
									}
								}
								else {
									$lig=mysqli_fetch_object($test);

									if($synthese=="") {
										if($forcer=="y") {
											$sql="DELETE FROM socle_eleves_syntheses WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
											//echo "$sql<br />";
											$del=mysqli_query($GLOBALS["mysqli"], $sql);
											if($del) {
												$cpt_reg++;
											}
											else {
												$msg.="Erreur lors de la suppression $sql<br />";
												$nb_err++;
											}
										}
									}
									elseif($synthese!=$lig->synthese) {
										$sql="UPDATE socle_eleves_syntheses SET synthese='".mysqli_real_escape_string($GLOBALS["mysqli"], $synthese)."', 
														login_saisie='".$_SESSION['login']."', 
														date_saisie='".strftime("%Y-%m-%d %H:%M:%S")."' 
													WHERE ine='".$ine."' AND cycle='".$cycle."' AND annee='".$gepiYear_debut."';";
										//echo "$sql<br />";
										$update=mysqli_query($GLOBALS["mysqli"], $sql);
										if($update) {
											$cpt_reg++;
										}
										else {
											$msg.="Erreur lors de la mise à jour $sql<br />";
											$nb_err++;
										}
									}
								}
							}
							/*
							else {
								echo "\$NON_PROTECT[\"synthese_".$ine."_".$cycle."] est non défini.<br />";
							}
							*/

						}
					}
				}
			}

			if(($nb_refus_baisser>0)&&(
				(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))||
				(
					($_SESSION['statut']=="professeur")&&(isset($id_classe))&&(getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login'], $id_classe))
				)
			)) {
				$msg.="Un ou des refus de baisser un niveau de compétence a été signalé, mais vous pourriez forcer la baisse en cochant la case idoine en début du formulaire.<br />";
			}

			$msg.=$cpt_reg." enregistrement(s) effectué(s).<br />";
			$msg.=$nb_err." erreur(s).<br />";
		}

	}
}

// 20191027
if((isset($_GET['export_csv']))&&
(isset($_GET['id_classe']))&&
(isset($_GET['periode']))&&
(preg_match("/^[0-9]{1,}$/", $_GET['id_classe']))&&
(preg_match("/^[0-9]{1,}$/", $_GET['periode']))) {

	$id_classe=$_GET['id_classe'];
	$periode=$_GET['periode'];

	$tab_saisies_grp=array();
	$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes_groupes sec, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND sec.ine=e.no_gep AND sec.periode=jec.periode AND jec.id_classe='".$id_classe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["niveau_maitrise"]=$lig->niveau_maitrise;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			//$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
		}
	}

	// Récupérer la liste des élèves et leur cycle.
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$id_classe."' AND jec.periode='".$periode."' AND e.no_gep!='' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="Aucun élève avec INE non vide n'a été trouvé pour cette classe.<br />";
	}
	else {

		$classe=get_nom_classe($id_classe);

		$csv="INE;Nom;Prenom;Classe;Periode;Cycle;Domaine;Domaine explicite;Maitrise Insuffisante;Maitrise Fragile;Maitrise Satisfaisante;Très Bonne Maitrise;Moyenne;Niveau moyen;\n";

		$cpt_ele=0;
		$tab_cycle=array();
		while($lig=mysqli_fetch_object($res)) {

			$mef_code_ele=$lig->mef_code;
			// 20180608
			//echo "\$mef_code_ele=$mef_code_ele<br />";
			if(!isset($tab_cycle[$mef_code_ele])) {
				$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
				$cycle=$tmp_tab_cycle_niveau["mef_cycle"];
				$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
				$tab_cycle[$mef_code_ele]=$cycle;
			}

			if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
				// Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identifié&nbsp;???
			}
			else {
				if(isset($cycle_particulier)) {
					$cycle=$cycle_particulier;
				}
				else {
					$cycle=$tab_cycle[$mef_code_ele];
				}

				// 20191027
				if($SocleSaisieComposantesMode==1) {
					$csv.=$lig->no_gep.";".$lig->nom.";".$lig->prenom.";".$classe.";".$periode.";".$tab_cycle[$mef_code_ele];


					// Quel intérêt ?







				}
				else {
					//$csv="INE;Nom;Prenom;Classe;Periode;Cycle;Domaine;Maitrise Insuffisante;Maitrise Fragile;Maitrise Satisfaisante;Très Bonne Maitrise;\n";

					$cpt_domaine=0;
					foreach($tab_domaine_socle as $code => $libelle) {

						$effectif_niveau=array();
						for($loop=0;$loop<5;$loop++) {
							$effectif_niveau[$loop]=array();
							$effectif_niveau[$loop]['effectif']=0;
							$effectif_niveau[$loop]['info']='';
						}

						if(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode])) {

							foreach($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode] as $current_id_groupe => $current_saisie) {
								$effectif_niveau[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]['effectif']++;
							}
						}

						$csv.=$lig->no_gep.";".$lig->nom.";".$lig->prenom.";".$classe.";".$periode.";".$tab_cycle[$mef_code_ele].";";
						$csv.=$code.";".$tab_domaine_socle[$code].";";
						//for($loop=0;$loop<5;$loop++) {
						$moyenne="-";
						$niveau_moyen="-";
						$total=0;
						$total_sur=0;
						for($loop=1;$loop<5;$loop++) {
							$csv.=$effectif_niveau[$loop]['effectif'].";";
							$total+=$effectif_niveau[$loop]['effectif']*$loop;
							$total_sur+=$effectif_niveau[$loop]['effectif'];
						}

						// Ajouter une colonne moyenne?
						if($total_sur>0) {
							$moyenne=round(10*$total/$total_sur)/10;

							if(isset($tab_traduction_niveau[round($total/$total_sur)])) {
								$niveau_moyen=$tab_traduction_niveau[round($total/$total_sur)];
							}
						}
						$csv.=$moyenne.";".$niveau_moyen.";";

						$csv.="\n";
					}
				}
			}
		}

		$nom_fic="socle_classe_".remplace_accents($classe, 'all')."_periode_".$periode."_".strftime("%Y%m%d_%H%M%S").".csv";
		send_file_download_headers('text/x-csv',$nom_fic);
		echo echo_csv_encoded($csv);
		die();
	}
}

$themessage  = 'Des valeurs ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
//**************** EN-TETE *****************
$titre_page = "Saisie Socle";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

//$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantes");

echo "<p class='bold'><a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if(acces("/saisie/socle_verif.php", $_SESSION["statut"])) {
	echo " | <a href=\"socle_verif.php";
	if(isset($id_classe)) {
		echo "?id_classe=".$id_classe;
		if(isset($periode)) {
			echo "&periode=".$periode;
		}
	}
	echo "\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Vérification du remplissage des bilans de composantes du socle</a>";
}

if((acces("/saisie/socle_verrouillage.php", $_SESSION["statut"]))&&(
	(getSettingAOui("SocleOuvertureSaisieComposantes_".$_SESSION["statut"]))||
	((getSettingAOui("SocleOuvertureSaisieComposantes_PP"))&&(is_pp($_SESSION["login"])))
)) {
	echo " | <a href=\"socle_verrouillage.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Ouverture/verrouillage des saisies</a>";
}
if((acces("/saisie/socle_import.php", $_SESSION["statut"]))&&
	(getSettingAOui("SocleImportComposantes"))&&
	(($_SESSION['statut']=="administrateur")||(getSettingAOui("SocleImportComposantes_".$_SESSION["statut"])))) {
	echo " | <a href=\"socle_import.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Import des bilans de composantes du socle</a>";
}

// Choix du groupe ou de la classe
if($_SESSION['statut']=="professeur") {
	if((!isset($id_groupe))&&(!isset($id_classe))) {
		echo "</p>";

		echo "<h2>Saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

		// Choix du groupe

		/*
		if(!$SocleOuvertureSaisieComposantes) {
			echo "<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée.<br />Seule la consultation des saisies est possible.</p>";
		}
		*/

		$nb_entrees=0;
		if(getSettingAOui("SocleSaisieComposantes_professeur")) {
			$groups=get_groups_for_prof($_SESSION['login']);

			$nb_entrees+=count($groups);
			if(count($groups)>0) {
				echo "<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>Choisissez un enseignement&nbsp;: <br />";
				for($loop=0;$loop<count($groups);$loop++) {
					echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=".$groups[$loop]["id"]."'>".$groups[$loop]["name"]." <em>(".$groups[$loop]["description"].")</em> en ".$groups[$loop]["classlist_string"]."</a><br />";
				}
				echo "</p>";
			}
		}

		if((getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION["login"]))) {
			echo "<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>Choisissez une classe&nbsp;: <br />";
			$tab_clas=get_tab_ele_clas_pp($_SESSION["login"]);
			$nb_entrees+=count($tab_clas["id_classe"]);
			for($loop=0;$loop<count($tab_clas["id_classe"]);$loop++) {
				//retourne_denomination_pp($id_classe) 
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$tab_clas["id_classe"][$loop]."'>".$tab_clas["classe"][$loop]." <em>(".retourne_denomination_pp($tab_clas["id_classe"][$loop]).")</em></a><br />";
			}
		}

		if($nb_entrees==0) {
			echo "<p style='color:red;'>Aucun accès enseignement ou classe trouvé(s) pour vous.</p>";
		}
		else {
			// 20191027
			if($SocleSaisieComposantesMode==1) {
				if(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure") {
					echo "<p style='margin-top:1em; margin-left:3em;text-indent:-3em;'><em>Note&nbsp;:</em> Le module est paramétré de tel sorte qu'un niveau de maitrise ne peut <em>(normalement)</em> pas être baissé.";

					$liste_profils_autorises_forcer="";
					if(getSettingAOui("SocleSaisieComposantesForcer_scolarite")) {
						$liste_profils_autorises_forcer.="Scolarité";
					}
					if(getSettingAOui("SocleSaisieComposantesForcer_cpe")) {
						if($liste_profils_autorises_forcer!="") {
							$liste_profils_autorises_forcer.=", ";
						}
						$liste_profils_autorises_forcer.="CPE";
					}
					if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {
						if($liste_profils_autorises_forcer!="") {
							$liste_profils_autorises_forcer.=", ";
						}
						$liste_profils_autorises_forcer.="Professeur de l'enseignement";
					}
					if(getSettingAOui("SocleSaisieComposantesForcer_PP")) {
						if($liste_profils_autorises_forcer!="") {
							$liste_profils_autorises_forcer.=", ";
						}
						$liste_profils_autorises_forcer.=getSettingValue("gepi_prof_suivi")." de la classe";
					}
					if($liste_profils_autorises_forcer!="") {
						echo "<br />Certains profils <em>($liste_profils_autorises_forcer)</em> sont cependant autorisés à forcer la saisie pour baisser le niveau.";
					}

					echo "</p>";


					if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {
						// Il est possible pour n'importe quel prof de forcer les saisies
					}
					elseif((getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login']))) {
						echo "
				<p style='margin-top:1em; margin-left:2em;text-indent:-2em; color:red;'><em>Attention&nbsp;:</em> Pour pouvoir forcer les saisies en tant que ".getSettingValue("gepi_prof_suivi").", il est nécessaire de choisir la classe plutôt que l'enseignement.</p>";
					}
				}
				else {
					echo "<p style='margin-top:1em; margin-left:3em;text-indent:-3em;'><em>Note&nbsp;:</em> En cas de saisies concurrentes, la dernière saisie effectuée l'emporte.</p>";
				}
			}
			else {
				echo "<p style='margin-top:1em;'>Le module est paramétré de telle sorte que les professeurs font chacun leurs saisies pour leurs enseignements et le ".getSettingValue('gepi_prof_suivi')." ou compte scolarité effectue le bilan en faisant la saisie pour la classe.<br />
				Lors de la saisie pour la classe, le nombre de validations pour tel niveau de maitrise est affiché.</p>";
			}
		}

		require("../lib/footer.inc.php");
		die();
	}
	elseif(!isset($periode)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir un autre groupe ou classe</a>";

		echo "<h2>Saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

		if(isset($id_classe)) {
			echo "<h3>Saisie des composantes du socle pour la classe de ".get_nom_classe($id_classe)."</h3>";
			$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
			$res_max=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_max)==0) {
				echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> La classe n'a pas de périodes définies.</p>";
				require("../lib/footer.inc.php");
				die();
			}
			$lig_max=mysqli_fetch_object($res_max);
			$max_per=$lig_max->max_per;

			echo "<p style='margin-left:3em;text-indent:-3em;'>Choisissez la période&nbsp;:<br />";
			for($i=1;$i<$max_per+1;$i++) {
				$etat_periode="";
				if(!$SocleOuvertureSaisieComposantes[$i]) {
					$etat_periode=" (période close)";
				}
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."&periode=".$i."'>Période $i</a>".$etat_periode."<br />";
			}
			echo "</p>";
		}
		else {
			echo "<h3>Saisie des composantes du socle pour l'enseignement de ".get_info_grp($id_groupe)."</h3>";
			$sql="SELECT MAX(periode) AS max_per FROM j_eleves_groupes WHERE id_groupe='$id_groupe';";
			$res_max=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_max)==0) {
				echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucun élève n'a été trouvé dans le groupe/enseignement.</p>";
				require("../lib/footer.inc.php");
				die();
			}
			$lig_max=mysqli_fetch_object($res_max);
			$max_per=$lig_max->max_per;

			echo "<p style='margin-left:3em;text-indent:-3em;'>Choisissez la période&nbsp;:<br />";
			for($i=1;$i<$max_per+1;$i++) {
				$etat_periode="";
				if(!$SocleOuvertureSaisieComposantes[$i]) {
					$etat_periode=" (période close)";
				}
				echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=".$id_groupe."&periode=".$i."'>Période $i</a>".$etat_periode."<br />";
			}
			echo "</p>";
		}

		echo "<p style='margin-top:2em; text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le verrouillage/ouverture de saisie sont effectuées en compte scolarité dans le menu Socle.<br />
		Ce n'est pas lié à l'ouverture des périodes pour la saisie des notes et appréciations des bulletins.<br />
		La saisie pour le socle peut être ouverte alors que toutes les périodes de bulletins sont closes.</p>";

		require("../lib/footer.inc.php");
		die();
	}
}
else {

	if(!isset($id_classe)) {
		echo "</p>";

		echo "<h2>Saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

		// Choix de la classe

		/*
		if(!$SocleOuvertureSaisieComposantes) {
			echo "<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée.<br />Seule la consultation des saisies est possible.</p>";
		}
		*/


		if((($_SESSION['statut']=='scolarite')&&(getSettingAOui('SocleSaisieComposantes_scolariteToutesClasses')))||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('SocleSaisieComposantes_cpeToutesClasses')))) {
			$sql="SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c ORDER BY classe;";
		}
		else {
			$sql=retourne_sql_mes_classes();
		}
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red;'>Aucune classe trouvée pour vous.</p>";
		}
		else {
			echo "<p style='margin-top:1em; margin-left:3em; text-indent:-3em;'>Choisissez une classe&nbsp;: <br />";
			while($lig=mysqli_fetch_object($res)) {
				echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$lig->id_classe."'>".$lig->classe."</a><br />";
			}
		}

		if(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure") {
			echo "<p style='margin-top:1em; margin-left:3em;text-indent:-3em;'><em>Note&nbsp;:</em> Le module est paramétré de tel sorte qu'un niveau de maitrise ne peut <em>(normalement)</em> pas être baissé.";

			$liste_profils_autorises_forcer="";
			if(getSettingAOui("SocleSaisieComposantesForcer_scolarite")) {
				$liste_profils_autorises_forcer.="Scolarité";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_cpe")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.="CPE";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.="Professeur de l'enseignement";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_PP")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.=getSettingValue("gepi_prof_suivi")." de la classe";
			}
			if($liste_profils_autorises_forcer!="") {
				echo "<br />Certains profils <em>($liste_profils_autorises_forcer)</em> sont cependant autorisés à forcer la saisie pour baisser le niveau.";
			}

			echo "</p>";
		}
		else {
			echo "<p style='margin-top:1em; margin-left:3em;text-indent:-3em;'><em>Note&nbsp;:</em> En cas de saisies concurrentes, la dernière saisie effectuée l'emporte.</p>";
		}

		require("../lib/footer.inc.php");
		die();
	}
	elseif(!isset($periode)) {
		echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre classe</a>";

		echo "<h2>Saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

		echo "<h3>Saisie des composantes du socle pour la classe de ".get_nom_classe($id_classe)."</h3>";
		$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
		$res_max=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_max)==0) {
			echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> La classe n'a pas de périodes définies.</p>";
			require("../lib/footer.inc.php");
			die();
		}
		$lig_max=mysqli_fetch_object($res_max);
		$max_per=$lig_max->max_per;

		echo "<p style='margin-left:3em;text-indent:-3em;'>Choisissez la période&nbsp;:<br />";
		for($i=1;$i<$max_per+1;$i++) {
			$etat_periode="";
			if(!$SocleOuvertureSaisieComposantes[$i]) {
				$etat_periode=" (période close)";
			}
			echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."&periode=".$i."'>Période $i</a>".$etat_periode."<br />";
		}
		echo "</p>";

		require("../lib/footer.inc.php");
		die();
	}
}

$SocleOuvertureSaisieComposantes=getSettingAOui("SocleOuvertureSaisieComposantesPeriode".$periode);

if($SocleOuvertureSaisieComposantes) {
	if($_SESSION['statut']=='scolarite') {
		if(getSettingAOui("SocleSaisieComposantes_scolariteToutesClasses")) {
			$SocleOuvertureSaisieComposantes=true;
		}
		else {
			$SocleOuvertureSaisieComposantes=false;

			if((isset($id_classe))&&(getSettingAOui("SocleSaisieComposantes_scolarite"))) {
				$tab_classe_user_courant=get_classes_from_user($_SESSION["login"], $_SESSION["statut"]);
				if(array_key_exists($id_classe, $tab_classe_user_courant)) {
					$SocleOuvertureSaisieComposantes=true;
				}
			}
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		if(getSettingAOui("SocleSaisieComposantes_cpeToutesClasses")) {
			$SocleOuvertureSaisieComposantes=true;
		}
		else {
			$SocleOuvertureSaisieComposantes=false;

			if((isset($id_classe))&&(getSettingAOui("SocleSaisieComposantes_cpe"))) {
				$tab_classe_user_courant=get_classes_from_user($_SESSION["login"], $_SESSION["statut"]);
				if(array_key_exists($id_classe, $tab_classe_user_courant)) {
					$SocleOuvertureSaisieComposantes=true;
				}
			}
		}
	}
	else {
		$SocleOuvertureSaisieComposantes=false;
		if(getSettingAOui("SocleSaisieComposantes_".$_SESSION['statut'])) {
			$SocleOuvertureSaisieComposantes=true;
		}
		else {
			// Test PP
			if(($_SESSION["statut"]=="professeur")&&(isset($id_classe))&&(getSettingAOui("SocleSaisieComposantes_PP"))&&(is_pp($_SESSION["login"], $id_classe))) {
				$SocleOuvertureSaisieComposantes=true;
			}
		}
	}
}

// Saisies (sous réserve que la saisie soit ouverte, sinon affichage)
$complement_url_retour="";
if(isset($id_groupe)) {
	$complement_url_retour="id_groupe=".$id_groupe;
}
elseif(isset($id_classe)) {
	$complement_url_retour="id_classe=".$id_classe;
}

echo " | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir un autre groupe ou classe</a>
 | <a href='".$_SERVER['PHP_SELF']."?".$complement_url_retour."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre période</a>
</p>";

echo "<h2>Saisies socle pour l'année <span style='color:red' title='Année récupérée des **4 premiers caractères** du paramètre **Année scolaire** de **Gestion générale/Configuration générale**'>$gepiYear_debut</span></h2>";

if(!$SocleOuvertureSaisieComposantes) {
	echo "\n<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée en période $periode, ou pas ouverte pour votre compte/statut selon le paramétrage du module.<br />Seule la consultation des saisies est possible.</p>";
}


$SocleSaisieSyntheses=false;
if($SocleOuvertureSaisieComposantes) {
	if(getSettingAOui("SocleSaisieSyntheses_".$_SESSION["statut"])) {
		$SocleSaisieSyntheses=true;
	}
	elseif(($_SESSION["statut"]=="professeur")&&(isset($id_classe))&&(getSettingAOui("SocleSaisieSyntheses_PP"))&&(is_pp($_SESSION["login"], $id_classe))) {
		$SocleSaisieSyntheses=true;
	}
}

// Saisie pour un cycle particulier, pas forcément le cycle actuel de l'élève.
echo "<form action='".$_SERVER['PHP_SELF']."' method='post' style='float:right; width:20em;' style='display:none;' id='form_choix_cycle'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='forcer_cycle' value='y' />
		".((isset($id_groupe)) ? "<input type='hidden' name='id_groupe' value='$id_groupe' />" : ((isset($id_classe)) ? "<input type='hidden' name='id_classe' value='$id_classe' />" : ""))."
		<input type='hidden' name='periode' value='$periode' />
		<p>Effectuer les saisies pour un cycle particulier, autre que le cycle actuel de l'élève&nbsp;:<br />
			<input type='radio' name='cycle_particulier' id='cycle_VIDE' value=''".$checked_cycle_particulier["courant_eleve"]." onchange=\"checkbox_change('cycle_VIDE');checkbox_change('cycle_2');checkbox_change('cycle_3');checkbox_change('cycle_4');\" /><label for='cycle_VIDE' id='texte_cycle_VIDE'>Cycle courant de l'élève</label><br />
			<input type='radio' name='cycle_particulier' id='cycle_2' value='2'".$checked_cycle_particulier[2]." onchange=\"checkbox_change('cycle_VIDE');checkbox_change('cycle_2');checkbox_change('cycle_3');checkbox_change('cycle_4');\" /><label for='cycle_2' id='texte_cycle_2'>Cycle 2</label><br />
			<input type='radio' name='cycle_particulier' id='cycle_3' value='3'".$checked_cycle_particulier[3]." onchange=\"checkbox_change('cycle_VIDE');checkbox_change('cycle_2');checkbox_change('cycle_3');checkbox_change('cycle_4');\" /><label for='cycle_3' id='texte_cycle_3'>Cycle 3</label><br />
			<input type='radio' name='cycle_particulier' id='cycle_4' value='4'".$checked_cycle_particulier[4]." onchange=\"checkbox_change('cycle_VIDE');checkbox_change('cycle_2');checkbox_change('cycle_3');checkbox_change('cycle_4');\" /><label for='cycle_4' id='texte_cycle_4'>Cycle 4</label>
		</p>
		<p><input type='submit' value='Choisir' /></p>
	</fieldset>
</form>
<!--
<div style='clear:both;'></div>
-->";

$sql="CREATE TABLE IF NOT EXISTS socle_eleves_composantes_groupes (id int(11) NOT NULL auto_increment, 
ine varchar(50) NOT NULL DEFAULT '', 
cycle tinyint(2) NOT NULL DEFAULT '0', 
annee varchar(10) NOT NULL default '', 
code_composante varchar(10) NOT NULL DEFAULT '', 
niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
id_groupe INT(11) NOT NULL default '0', 
periode INT(11) NOT NULL default '1', 
login_saisie varchar(50) NOT NULL DEFAULT '', 
date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
PRIMARY KEY (id), INDEX ine_cycle_id_composante_id_groupe_periode (ine, cycle, code_composante, id_groupe, periode, annee), UNIQUE(ine, cycle, code_composante, id_groupe, periode, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
//echo "$sql<br />";
$create_table=mysqli_query($mysqli, $sql);


if(isset($id_groupe)) {

	$sql="SELECT MAX(periode) AS max_per FROM j_eleves_groupes WHERE id_groupe='$id_groupe';";
	$res_max=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_max)==0) {
		echo "\n<h2>".get_info_grp($id_groupe)." (période $periode)</h2>";

		echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucun élève n'a été trouvé dans le groupe/enseignement.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_max=mysqli_fetch_object($res_max);
	$max_per=$lig_max->max_per;

	echo "\n<h2>".get_info_grp($id_groupe)." (période ";
	if($periode>1) {
		echo "<a href='saisie_socle.php?id_groupe=".$id_groupe."&periode=".($periode-1)."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Période précédente\"><img src='../images/icons/arrow-left.png' class='icone16' /></a> ";
	}
	echo $periode;
	if($periode<$max_per) {
		echo " <a href='saisie_socle.php?id_groupe=".$id_groupe."&periode=".($periode+1)."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Période suivante\"><img src='../images/icons/arrow-right.png' class='icone16' /></a> ";
	}
	echo ")</h2>";

	if(isset($cycle_particulier)) {
		echo "<p style='color:red; font-weight:bold; margin-bottom:1em;'>Saisie pour le cycle $cycle_particulier sans tenir compte du cycle actuel lié au MEF de l'élève.</p>";
	}


	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// A REVOIR POUR RECUPERER LES SAISIES D ANNEES PRECEDENTES
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

/*
// 20191026
Choisir entre deux modes: dans mod_LSUN/admin.php
- mode historique 							$SocleSaisieComposantesMode==1
- mode avec enregistrement des saisies de chaque prof 	$SocleSaisieComposantesMode==2

*/
	// Récupérer les saisies antérieures
	$tab_civ_nom_prenom=array();
	$tab_saisies=array();
	$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes sec, eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND sec.periode=jeg.periode AND sec.ine=e.no_gep AND jeg.id_groupe='".$id_groupe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_saisies[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode]["niveau_maitrise"]=$lig->niveau_maitrise;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			$tab_saisies[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
		}
	}

	//20191027
	$tab_saisies_grp=array();
	$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes_groupes sec, eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND sec.periode=jeg.periode AND sec.ine=e.no_gep AND jeg.id_groupe='".$id_groupe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	//echo "$sql<br />";
	// On ne filtre pas avec sec.id_groupe=jeg.id_groupe pour récupérer les saisies des collègues
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["niveau_maitrise"]=$lig->niveau_maitrise;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			//$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
		}
	}

	$tab_syntheses=array();
	$sql="SELECT DISTINCT ses.* FROM socle_eleves_syntheses ses, eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND ses.ine=e.no_gep AND jeg.id_groupe='".$id_groupe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_syntheses[$lig->ine][$lig->cycle]["synthese"]=$lig->synthese;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			$tab_syntheses[$lig->ine][$lig->cycle]["title"]=" title=\"Saisie par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2")."\"";
		}
	}

	// INE vide:
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND jeg.id_groupe='".$id_groupe."' AND jeg.periode='".$periode."' AND e.no_gep='' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p style='color:red; margin-bottom:1em;'>Un ou des élèves ont un numéro national (INE) vide&nbsp;: ";
		$cpt_ine_vide=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt_ine_vide>0) {
				echo ", ";
			}
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$lig->login."' target='_blank'>".$lig->nom." ".$lig->prenom."</a>";
			$cpt_ine_vide++;
		}
		echo "<br />La saisie n'est pas possible pour ces élèves.<br />Demandez à l'administrateur de faire une mise à jour des informations élèves d'après Sconet.</p>";
	}

	// Récupérer la liste des élèves et leur cycle.
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_groupes jeg WHERE e.login=jeg.login AND jeg.id_groupe='".$id_groupe."' AND jeg.periode='".$periode."' AND e.no_gep!='' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red;'>Aucun élève avec INE non vide n'a été trouvé pour ce groupe.</p>";
	}
	else {
		//20170302
/*
		$sql="SELECT jeg.id_groupe FROM j_groupes_enseignements_complement jgec, 
							j_eleves_groupes jeg, 
							j_groupes_professeurs jgp 
						WHERE jgec.id_groupe=jeg.id_groupe AND 
							jeg.id_groupe=jgp.id_groupe AND 
							jgp.login='".$_SESSION["login"]."' AND 
							jeg.login='".$lig->login."';";
*/
		$enseignement_complement=false;
		$sql="SELECT DISTINCT jgec.* FROM j_groupes_enseignements_complement jgec, 
							j_groupes_professeurs jgp 
						WHERE jgec.id_groupe=jgp.id_groupe AND 
							jgec.id_groupe='".$id_groupe."' AND 
							jgp.login='".$_SESSION["login"]."' AND 
							jgec.code!='';";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			$enseignement_complement=true;
			$lig_test=mysqli_fetch_object($test);
			$code_enseignement_complement=$lig_test->code;

			$tab_niveaux_eleves_enseignement_complement=array();

			// A VOIR : Si on fait un TRUNCATE au lieu d'un DELETE sur les groupes au changement d'année, on risque de ré-attribuer des id_groupe correspondant à des valeurs de socle_eleves_enseignements_complements
			$sql="SELECT * FROM socle_eleves_enseignements_complements 
							WHERE id_groupe='".$id_groupe."' AND ine!='';";
			$res_ec=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_ec)>0) {
				while($lig_ec=mysqli_fetch_assoc($res_ec)) {
					$tab_niveaux_eleves_enseignement_complement[$lig_ec['ine']]=$lig_ec;
				}
			}
		}

		//20180211
		$is_lvr=false;
		$sql="SELECT jgl.* FROM j_groupes_lvr jgl, 
							j_groupes_professeurs jgp 
						WHERE jgl.id_groupe=jgp.id_groupe AND 
							jgl.id_groupe='".$id_groupe."' AND 
							jgp.login='".$_SESSION["login"]."' AND 
							jgl.code!='';";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			$is_lvr=true;
			$lig_test=mysqli_fetch_object($test);
			$code_lvr=$lig_test->code;

			$tab_niveaux_eleves_LVR=array();

			// A VOIR : Si on fait un TRUNCATE au lieu d'un DELETE sur les groupes au changement d'année, on risque de ré-attribuer des id_groupe correspondant à des valeurs de socle_eleves_lvr
			$sql="SELECT * FROM socle_eleves_lvr 
							WHERE id_groupe='".$id_groupe."' AND ine!='';";
			$res_ec=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_ec)>0) {
				while($lig_ec=mysqli_fetch_assoc($res_ec)) {
					$tab_niveaux_eleves_LVR[$lig_ec['ine']]=$lig_ec;
				}
			}
		}

		echo "<p><em>Notes&nbsp;:</em></p>
<ul>
	<li><p>Pour les bilans de fin de cycle en 6ème et 3ème, la saisie d'une synthèse est requise.<br />
	Elle n'est prise en compte dans les remontées LSUN que pour le Bilan de fin de cycle.</p></li>";
		if($SocleSaisieComposantesMode==1) {
			if(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure") {
				echo "<li><p>Le module est paramétré de tel sorte qu'un niveau de maitrise ne peut <em>(normalement)</em> pas être baissé.";

				$liste_profils_autorises_forcer="";
				if(getSettingAOui("SocleSaisieComposantesForcer_scolarite")) {
					$liste_profils_autorises_forcer.="Scolarité";
				}
				if(getSettingAOui("SocleSaisieComposantesForcer_cpe")) {
					if($liste_profils_autorises_forcer!="") {
						$liste_profils_autorises_forcer.=", ";
					}
					$liste_profils_autorises_forcer.="CPE";
				}
				if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {
					if($liste_profils_autorises_forcer!="") {
						$liste_profils_autorises_forcer.=", ";
					}
					$liste_profils_autorises_forcer.="Professeur de l'enseignement";
				}
				if(getSettingAOui("SocleSaisieComposantesForcer_PP")) {
					if($liste_profils_autorises_forcer!="") {
						$liste_profils_autorises_forcer.=", ";
					}
					$liste_profils_autorises_forcer.=getSettingValue("gepi_prof_suivi")." de la classe";
				}
				if($liste_profils_autorises_forcer!="") {
					echo "<br />Certains profils <em>($liste_profils_autorises_forcer)</em> sont cependant autorisés à forcer la saisie pour baisser le niveau.";
				}

				echo "</p></li>";
			}
		}
		else {
			echo "<li><p>Chaque professeur fait les saisies selon son opinion, ses évaluations.<br />
			Un bilan sera fait par le ".getSettingValue('gepi_prof_suivi')." ou un compte scolarité selon ce qui a été paramétré dans le module.</p></li>";
		}

		if(($periode>1)&&($SocleOuvertureSaisieComposantes=='y')) {
			echo "
			<li>
				<p>Vous pouvez recopier les niveaux de maitrise de la période précédente et ajuster ensuite les évolutions relevées.<br />
				Vous pouvez le faire en cliquant sur le niveau de maitrise dans la colonne <strong>période précédente</strong> pour une composante donnée et un élève donné.<br />
				Vous pouvez le faire pour toutes les composantes pour un élève donné en cliquant en entête de tableau élève sur l'icone <img src='../images/icons/wizard.png' class='icone16' /> de la colonne Période précédente.<br />
				Et enfin, vous pouvez recopier les niveaux de maitrise de la période précédente pour tous les élèves et sur toutes les composantes 
				<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente_tous_eleves(); return false;\" title=\"Recopier les niveaux de maitrise de la période précédente pour tous les élèves et sur toutes les composantes.\">
					en cliquant ici 
					<img src='../images/icons/paste.png' class='icone16' />
				</a>
			</li>";
		}
		echo "
</ul>";

		echo "
</ul>";
		/*
		echo "<pre>";
		print_r($tab_saisies);
		echo "</pre>";
		*/

		$acces_bull_simp=acces("/prepa_conseil/edit_limite.php", $_SESSION['statut']);

		$titre_infobulle="Bulletin simplifié";
		$texte_infobulle="<div id='div_bull_simp'></div>";
		$tabdiv_infobulle[]=creer_div_infobulle('div_bulletin_simplifie',$titre_infobulle,"",$texte_infobulle,"",50,0,'y','y','n','n',2);

		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();


		// 20191027 : A PARTIR DE LA TENIR COMPTE DU MODE $SocleSaisieComposantesMode

		//echo "<p style='color:red'>\$SocleSaisieComposantesMode=$SocleSaisieComposantesMode</p>";
		if($SocleSaisieComposantesMode==1) {

			if(($SocleOuvertureSaisieComposantes)&&
			(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure")&&
			(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))) {
				echo "
			<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='forcer' id='forcer' value='y'".(((isset($_POST["forcer"]))&&($_POST["forcer"]=="y")) ? " checked" : "")." onchange=\"checkbox_change(this.id)\"/><label for='forcer' id='texte_forcer'>Forcer les saisies <br />
	<em>(pour vider/baisser éventuellement les niveaux de maîtrise en écrasant les saisies antérieures 
	<br />(les vôtres ou celles de collègues (à manipuler avec précaution, dans un souci de bonne entente entre collègues)))</em>.</label></p>";
			}

			$cpt_ele=0;
			$tab_cycle=array();
			while($lig=mysqli_fetch_object($res)) {
				$mef_code_ele=$lig->mef_code;

				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle;
				}

				// 20180518
				echo "<div style='margin:0.5em; padding:0.5em;' class='fieldset_opacite50'>";

				if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
					echo "
			<p style='color:red'>Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identifié&nbsp;???";
					echo " <a href='http://www.sylogix.org/projects/gepi/wiki/LSUN#Socle-Cycle-non-identifi%C3%A9' target='_blank' title=\"Voir les explications possibles sur le wiki.\"><img src='../images/icons/ico_aide.png' width='15' height='25' alt='Info' /></a>";
					echo "</p>";
				}
				else {
					if(isset($cycle_particulier)) {
						$cycle=$cycle_particulier;
					}
					else {
						$cycle=$tab_cycle[$mef_code_ele];
					}

					$chaine_bull_simp="";
					if($acces_bull_simp) {
						$sql="SELECT id_classe, periode FROM j_eleves_classes WHERE login='".$lig->login."' ORDER BY periode DESC LIMIT 1;";
						$res_clas_ele=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_clas_ele)>0) {
							$lig_clas_ele=mysqli_fetch_object($res_clas_ele);
							$chaine_bull_simp=" <a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$lig->login."&id_classe=".$lig_clas_ele->id_classe."&periode1=1&periode2=".$lig_clas_ele->periode."&couleur_alterne=y' onclick=\"affiche_bull_simp('".$lig->login."', ".$lig_clas_ele->id_classe.", 1, ".$lig_clas_ele->periode.") ;return false;\" target='_blank'><img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
						}
					}

					$nb_pts_dnb=0;
					echo "
			<p style='margin-top:2em;'><strong";
					// 20180812
					echo " onmouseover=\"affiche_photo_courante('".nom_photo($lig->elenoet)."')\" onmouseout=\"vide_photo_courante();\"";
					echo ">".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;:".$chaine_bull_simp."</p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th rowspan='2'>Domaine du socle</th>
						<th colspan='5'>Niveau de maitrise</th>";
					if(($periode>1)&&($SocleOuvertureSaisieComposantes)) {
						echo "
						<th rowspan='2'>Période<br />précédente";
						if($SocleOuvertureSaisieComposantes=="y") {
							echo " 
						<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente($cpt_ele); return false;\" title=\"Prendre pour valeur, le niveau de maitrise de la période précédente sur chacun des domaines pour cet élève\">
							<img src='../images/icons/paste.png' class='icone16' />
						</a>";
						}
						echo "</th>";
					}
					echo "
					</tr>
					<tr>
						<th title='Non encore défini' onclick=\"coche_colonne_ele($cpt_ele, 0)\">X</th>
						<th title='Maitrise Insuffisante' style='color:red' onclick=\"coche_colonne_ele($cpt_ele, 1)\">MI</th>
						<th title='Maitrise Fragile' style='color:orange' onclick=\"coche_colonne_ele($cpt_ele, 2)\">MF</th>
						<th title='Maitrise Satisfaisante' style='color:green' onclick=\"coche_colonne_ele($cpt_ele, 3)\">MS</th>
						<th title='Très Bonne Maitrise' style='color:blue' onclick=\"coche_colonne_ele($cpt_ele, 4)\">TBM</th>
					</tr>
				</thead>
				<tbody>";
					$cpt_domaine=0;
					foreach($tab_domaine_socle as $code => $libelle) {
						$checked=array();
						$checked[0]=" checked";
						$checked[1]="";
						$checked[2]="";
						$checked[3]="";
						$checked[4]="";

						$title=array();
						$title[0]="";
						$title[1]="";
						$title[2]="";
						$title[3]="";
						$title[4]="";

						if(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode])) {
							$checked[0]="";
							$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
							$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
							$nb_pts_dnb+=nb_pts_DNB($tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]);
						}
						/*
						echo "
						<tr>
							<td>";
						echo "<pre>";
						print_r($tab_saisies[$lig->no_gep][$cycle][$code]);
						echo "</pre>";
						echo "</td>
						</tr>";
						*/

						/*
						$valeur_precedente="";
						if(($periode>1)&&(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]]))) {
							$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]];
						}
						*/

						$valeur_precedente="";
						if(($periode>1)&&(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]]))) {

							if($SocleOuvertureSaisieComposantes=="y") {
								$val=$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"];

								$valeur_precedente="<span style='color:".$tab_couleur_niveau[$val]."' onclick=\"coche_niveau_maitrise($cpt_ele, $cpt_domaine, ".$val.")\">".$tab_traduction_niveau[$val]."</span><span id='span_periode_precedente_".$cpt_ele."_".$cpt_domaine."' style='display:none'>".$val."</span>";
							}
							else {
								$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]];
							}
						}


						if($SocleOuvertureSaisieComposantes=="y") {
							echo "
					<tr>
						<td style='text-align:left;' id='td_libelle_".$cpt_ele."_".$cpt_domaine."'>".$libelle."</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0'".$title[0].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value=''".$checked[0]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1'".$title[1].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='1'".$checked[1]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2'".$title[2].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='2'".$checked[2]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3'".$title[3].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='3'".$checked[3]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4'".$title[4].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='4'".$checked[4]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
						<td>
							$valeur_precedente
						</td>" : "")."
					</tr>";
							$cpt_domaine++;
						}
						else {
							echo "
					<tr>
						<td style='text-align:left;'>".$libelle."</td>
						<td>".(($checked[0]!="") ? "X" : "")."</td>
						<td>".(($checked[1]!="") ? "<span style='color:red'>MI</span>" : "")."</td>
						<td>".(($checked[2]!="") ? "<span style='color:orange'>MF</span>" : "")."</td>
						<td>".(($checked[3]!="") ? "<span style='color:green'>MS</span>" : "")."</td>
						<td>".(($checked[4]!="") ? "<span style='color:blue'>TBM</span>" : "")."</td>".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
						<td>
							$valeur_precedente
						</td>" : "")."
					</tr>";
						}
					}
					echo "
				</tbody>
			</table>";

					// 20170302 :
					if(($tab_cycle[$mef_code_ele]==4)&&($periode==$max_per)) {
						if($enseignement_complement) {
							//$tab_types_enseignements_complement
							$checked=array();
							$checked[0]=" checked";
							$checked[1]="";
							$checked[2]="";

							$style=array();
							$style[0]=" style='font-weight:bold'";
							$style[1]="";
							$style[2]="";
							if(isset($tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"])) {
								$style[0]="";
								$style[1]="";
								$style[2]="";

								$checked[0]="";
								$checked[1]="";
								$checked[2]="";

								$checked[$tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"]]=" checked";
								$style[$tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"]]=" style='font-weight:bold'";
							}

							if($SocleOuvertureSaisieComposantes=="y") {
								echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
				<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' value='0'".$checked[0]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' value='1'".$checked[1]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' value='2'".$checked[2]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
			</p>";
							}
							else {
								echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
				<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
				".($checked[0]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
				".($checked[1]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
				".($checked[2]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
			</p>";
							}
						}
					}

					// 20180603


					$nb_points_enseignement_complement=calcule_points_DNB_enseignement_complement($lig->no_gep);
					$nb_pts_dnb+=$nb_points_enseignement_complement;

					//20180210
					if(($tab_cycle[$mef_code_ele]==4)&&($periode==$max_per)) {
						if($is_lvr) {
							//echo "\$is_lvr=true.<br />";
							//$tab_type_LVR
							$checked=array();
							$checked[1]=" checked";
							$checked[2]="";

							$style=array();
							$style[1]=" style='font-weight:bold'";
							$style[2]="";
							if(isset($tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"])) {

								//echo "\$tab_niveaux_eleves_LVR[$lig->no_gep][\"positionnement\"] est défini.<br />";

								$style[1]="";
								$style[2]="";

								$checked[1]="";
								$checked[2]="";

								$checked[$tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"]]=" checked";
								$style[$tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"]]=" style='font-weight:bold'";
							}

							/*
							echo "<pre>";
							echo "\$style[0]=".$style[0]."\n";
							echo "\$style[1]=".$style[1]."\n";
							echo "\$style[2]=".$style[2]."\n";
							echo "</pre>";
							*/

							echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de la Langue vivante régionale (".$tab_type_LVR["code"][$code_lvr].")\">
				<strong>Langue vivante régionale&nbsp;:</strong> ".$tab_type_LVR["code"][$code_lvr]." (".$tab_type_LVR["code"][$code_lvr].")<br />
				<input type='radio' name='lvr[".$lig->no_gep."]' id='lvr_".$id_groupe."_".$lig->no_gep."_1' value='1'".$checked[1]." 
						onchange=\"checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_1');
								checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_2');\" />
				<label for='lvr_".$id_groupe."_".$lig->no_gep."_1' id='texte_lvr_".$id_groupe."_".$lig->no_gep."_1'".$style[1]."> Objectif non atteint <em>(pas de remontée)</em></label><br />

				<input type='radio' name='lvr[".$lig->no_gep."]' id='lvr_".$id_groupe."_".$lig->no_gep."_2' value='2'".$checked[2]." 
						onchange=\"checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_1');
								checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_2');\" />
				<label for='lvr_".$id_groupe."_".$lig->no_gep."_2' id='texte_lvr_".$id_groupe."_".$lig->no_gep."_2'".$style[2]."> Niveau A2 atteint</label>
			</p>";
						}
					}


					$commentaire_nb_points="";
					$style_nb_points="";
					if($nb_pts_dnb>=420) {
						$style_nb_points="font-weight:bold; color:blue;";
						$commentaire_nb_points=".\nDNB d'ores et déjà obtenu *avec mention*";
					}
					elseif($nb_pts_dnb>=350) {
						$style_nb_points="font-weight:bold; color:green;";
						$commentaire_nb_points=".\nDNB d'ores et déjà obtenu";
					}
					echo "<div id='nb_points_".$cpt_ele."' style='float:right;width:3em;text-align:center;".$style_nb_points."' title=\"Nombre de points pour le DNB".((($nb_points_enseignement_complement!="")&&($nb_points_enseignement_complement>0)) ? " (dont $nb_points_enseignement_complement points d'enseignement de complément)" : "")."".$commentaire_nb_points.".\" class='fieldset_opacite50'>".$nb_pts_dnb."</div>";

					if($SocleSaisieSyntheses) {
						echo "
			<p style='margin-top:0.5em;' ".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "").">
				<strong>Synthèse&nbsp;:</strong> 
				<input type='hidden' name='indice_synthese[]' value=\"".$lig->no_gep."|".$cycle."\" />
				<textarea style='vertical-align:top;' 
						cols='80' 
						rows='4' 
						name=\"no_anti_inject_synthese_".$lig->no_gep."_".$cycle."\">".
						((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? $tab_syntheses[$lig->no_gep][$cycle]["synthese"] : "").
				"</textarea>
			</p>";
					}
					else {
						echo "
			<p style='margin-top:0.5em;' ".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "")."><strong>Synthèse&nbsp;:</strong> ".((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? nl2br($tab_syntheses[$lig->no_gep][$cycle]["synthese"]) : "<span style='color:red'>Vide</span>")."</p>";
					}

					$cpt_ele++;
				}

				// 20180518
				echo "</div>";
			}

			if($SocleOuvertureSaisieComposantes=="y") {
				if(isset($cycle_particulier)) {
					echo "
			<input type='hidden' name='cycle_particulier' value='".$cycle_particulier."' />";
				}

				echo "
			<input type='hidden' name='enregistrer_saisies' value='y' />
			<input type='hidden' name='id_groupe' value='$id_groupe' />
			<input type='hidden' name='periode' value='$periode' />
			<p><input type='submit' value='Enregistrer' /></p>

			<div id='fixe'>
				<div id='div_photo'></div>
				<input type='submit' value='Enregistrer' />
			</div>

			<script type='text/javascript'>
				".js_checkbox_change_style()."
				checkbox_change('forcer');

				function coche_colonne_ele(cpt_ele, niveau_maitrise) {
					var i;
					for(i=0;i<8;i++) {
						if(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise)) {
							document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise).checked=true;
							maj_couleurs_maitrise(cpt_ele,i);
							calcule_score_socle(cpt_ele);
						}
					}
				}

				var couleur_maitrise=new Array('black', 'red', 'orange', 'green', 'blue');
				function maj_couleurs_maitrise(cpt_ele, cpt_domaine) {
					if(document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine)) {
						/*
						if(cpt_ele<1) {
							alert('plop');
						}
						*/
						num_k='_';
						for(k=0;k<=4;k++) {
							if((document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k))&&
							(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).checked==true)) {
								num_k=k;
								break;
							}
						}

						if(num_k!='_') {
							for(k=0;k<=4;k++) {
								document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).style.backgroundColor='';
							}
							document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine).style.color=couleur_maitrise[num_k];
							if(num_k!=0) {
								document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+num_k).style.backgroundColor=couleur_maitrise[num_k];
							}
						}
					}
				}

				for(i=0;i<$cpt_ele;i++) {
					for(j=0;j<".count($tab_domaine_socle).";j++) {
						maj_couleurs_maitrise(i, j);
					}
				}

				function affiche_bull_simp(login_ele, id_classe, periode1, periode2) {
					new Ajax.Updater($('div_bull_simp'),'../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve='+login_ele+'&id_classe='+id_classe+'&periode1='+periode1+'&periode2='+periode2+'&couleur_alterne=y',{method: 'get'});

					afficher_div('div_bulletin_simplifie', 'y', 10, 10);
				}

				var points=new Array(0, 10, 25, 40, 50);

				function calcule_score_socle(num_ele) {
					var score=0;

					//alert('num_ele='+num_ele);

					if(document.getElementById('nb_points_'+num_ele)) {
						for(j=0;j<8;j++) {
							for(niveau=0;niveau<5;niveau++) {
								if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau)) {
									if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau).checked==true) {
										score+=points[niveau];
										//alert(score);
									}
								}
							}
						}

						// 20180601
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_0'
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_1' Objectif atteint (10 points)
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_2' Objectif dépassé (20 points)

						var input=document.getElementsByTagName('input');
						for(k=0;k<input.length;k++) {
							current_type=input[k].getAttribute('type');
							if(current_type=='radio') {
								current_id=input[k].getAttribute('id');
								if(document.getElementById(current_id)) {
									if(document.getElementById(current_id).checked==true) {
										if(current_id.substr(0,24)=='enseignement_complement_') {
											var tab=current_id.split('_');
											/*
											var tmp_chaine='';
											for(l=0;l<tab.length;l++) {
												tmp_chaine=tmp_chaine+'tab['+l+']='+tab[l]+' ';
											}
											tmp_chaine='current_id='+current_id+' et sous-chaine='+current_id.substr(0,24)+' '+tmp_chaine;
											if((k>90)&&(k<100)) {
												alert(tmp_chaine);
											}
											*/
											//current_id=enseignement_complement_4377_1_2 et sous-chaine=enseignement_complement_ tab[0]=enseignement tab[1]=complement tab[2]=4377 tab[3]=1 tab[4]=2 

											if(tab[3]==num_ele) {
												if(tab[4]==0) {
													// Pas de modif
												}
												else if(tab[4]==1) {
													score+=10;
												}
												else if(tab[4]==2) {
													score+=20;
												}
												else {
													// Pas de modif
												}
											}
										}
									}
								}
							}
						}

						// Et idem pour LVR

						document.getElementById('nb_points_'+num_ele).innerHTML='<span title=\'Non enregistré\'>'+score+' (*)</span>';
					}
				}

				checkbox_change('cycle_VIDE');
				checkbox_change('cycle_2');
				checkbox_change('cycle_3');
				checkbox_change('cycle_4');
				document.getElementById('form_choix_cycle').style.display='';

				// 20180812
				function affiche_photo_courante(photo) {
					document.getElementById('div_photo').innerHTML=\"<img src='\"+photo+\"' width='150' alt='Photo' />\";
				}

				function vide_photo_courante() {
					document.getElementById('div_photo').innerHTML='';
				}


				function coche_niveau_maitrise(cpt_ele, cpt_domaine, niveau) {
					id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
					if(document.getElementById(id)) {
						document.getElementById(id).checked=true;
						changement();
						maj_couleurs_maitrise(cpt_ele, cpt_domaine);
						calcule_score_socle(cpt_ele);
					}
				}

				function coche_niveau_maitrise_moyen(cpt_ele) {
					//alert(cpt_ele);
					for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
						//id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
						id='span_moy_'+cpt_ele+'_'+cpt_domaine;
						if(document.getElementById(id)) {
							//alert(id+' existe.');
							moy=document.getElementById(id).innerHTML;
							coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
						}
					}
				}

				function coche_niveau_maitrise_moyen_tous_eleves() {
					for(i=0;i<$cpt_ele;i++) {
						coche_niveau_maitrise_moyen(i);
					}
				}

				function copie_niveau_maitrise_periode_precedente(cpt_ele) {
					//alert(cpt_ele);
					for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
						id='span_periode_precedente_'+cpt_ele+'_'+cpt_domaine;
						if(document.getElementById(id)) {
							//alert(id+' existe.');
							moy=document.getElementById(id).innerHTML;
							coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
						}
					}
				}

				function copie_niveau_maitrise_periode_precedente_tous_eleves() {
					for(i=0;i<$cpt_ele;i++) {
						copie_niveau_maitrise_periode_precedente(i);
					}
				}

			</script>";
			}
		}
		else {
			// 20191027
			// Chaque prof fait la saisie pour son groupe

			/*
			if(($SocleOuvertureSaisieComposantes)&&
			(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure")&&
			(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))) {
				echo "
			<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='forcer' id='forcer' value='y'".(((isset($_POST["forcer"]))&&($_POST["forcer"]=="y")) ? " checked" : "")." onchange=\"checkbox_change(this.id)\"/><label for='forcer' id='texte_forcer'>Forcer les saisies <br />
	<em>(pour vider/baisser éventuellement les niveaux de maîtrise en écrasant les saisies antérieures 
	<br />(les vôtres ou celles de collègues (à manipuler avec précaution, dans un souci de bonne entente entre collègues)))</em>.</label></p>";
			}
			*/

			$cpt_ele=0;
			$tab_cycle=array();
			while($lig=mysqli_fetch_object($res)) {
				$mef_code_ele=$lig->mef_code;

				if(!isset($tab_cycle[$mef_code_ele])) {
					$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
					$cycle=$tmp_tab_cycle_niveau["mef_cycle"];
					$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
					$tab_cycle[$mef_code_ele]=$cycle;
				}

				// 20180518
				echo "<div style='margin:0.5em; padding:0.5em;' class='fieldset_opacite50'>";

				if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
					echo "
			<p style='color:red'>Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identifié&nbsp;???";
					echo " <a href='http://www.sylogix.org/projects/gepi/wiki/LSUN#Socle-Cycle-non-identifi%C3%A9' target='_blank' title=\"Voir les explications possibles sur le wiki.\"><img src='../images/icons/ico_aide.png' width='15' height='25' alt='Info' /></a>";
					echo "</p>";
				}
				else {
					if(isset($cycle_particulier)) {
						$cycle=$cycle_particulier;
					}
					else {
						$cycle=$tab_cycle[$mef_code_ele];
					}

					$chaine_bull_simp="";
					if($acces_bull_simp) {
						$sql="SELECT id_classe, periode FROM j_eleves_classes WHERE login='".$lig->login."' ORDER BY periode DESC LIMIT 1;";
						$res_clas_ele=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_clas_ele)>0) {
							$lig_clas_ele=mysqli_fetch_object($res_clas_ele);
							$chaine_bull_simp=" <a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$lig->login."&id_classe=".$lig_clas_ele->id_classe."&periode1=1&periode2=".$lig_clas_ele->periode."&couleur_alterne=y' onclick=\"affiche_bull_simp('".$lig->login."', ".$lig_clas_ele->id_classe.", 1, ".$lig_clas_ele->periode.") ;return false;\" target='_blank'><img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
						}
					}

					$nb_pts_dnb=0;
					echo "
			<p style='margin-top:2em;'><strong";
					// 20180812
					echo " onmouseover=\"affiche_photo_courante('".nom_photo($lig->elenoet)."')\" onmouseout=\"vide_photo_courante();\"";
					echo ">".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;:".$chaine_bull_simp."</p>
			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th rowspan='2'>Domaine du socle</th>
						<th colspan='9'>Niveau de maitrise</th>";
					if(($periode>1)&&($SocleOuvertureSaisieComposantes)) {
						echo "
						<th rowspan='2'>Période<br />précédente";
						if($SocleOuvertureSaisieComposantes=="y") {
							echo " 
						<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente($cpt_ele); return false;\" title=\"Prendre pour valeur, le niveau de maitrise de la période précédente sur chacun des domaines pour cet élève\">
							<img src='../images/icons/paste.png' class='icone16' />
						</a>";
						}
						echo "</th>";
					}
					echo "
					</tr>
					<tr>
						<th title='Non encore défini' onclick=\"coche_colonne_ele($cpt_ele, 0)\">X</th>
						<th colspan='2' title='Maitrise Insuffisante' style='color:red' onclick=\"coche_colonne_ele($cpt_ele, 1)\">MI</th>
						<th colspan='2' title='Maitrise Fragile' style='color:orange' onclick=\"coche_colonne_ele($cpt_ele, 2)\">MF</th>
						<th colspan='2' title='Maitrise Satisfaisante' style='color:green' onclick=\"coche_colonne_ele($cpt_ele, 3)\">MS</th>
						<th colspan='2' title='Très Bonne Maitrise' style='color:blue' onclick=\"coche_colonne_ele($cpt_ele, 4)\">TBM</th>
					</tr>
				</thead>
				<tbody>";
					$cpt_domaine=0;
					foreach($tab_domaine_socle as $code => $libelle) {
						$checked=array();
						$checked[0]=" checked";
						$checked[1]="";
						$checked[2]="";
						$checked[3]="";
						$checked[4]="";

						$title=array();
						$title[0]="";
						$title[1]="";
						$title[2]="";
						$title[3]="";
						$title[4]="";

						$effectif_niveau=array();
						for($loop=0;$loop<5;$loop++) {
							$effectif_niveau[$loop]=array();
							$effectif_niveau[$loop]['effectif']=0;
							$effectif_niveau[$loop]['info']='';
						}

						if(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode])) {
							foreach($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode] as $current_id_groupe => $current_saisie) {
								if($current_id_groupe==$id_groupe) {
									// Ca concerne la saisie du prof courant
									$checked[0]="";
									$checked[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]=" checked";
									$title[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]=" title=\"".$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["title"]."\"";
								}
								else {
									// Sinon, il faut compter le nombre de saisies pour tel niveau de compétence et...
									//$current_saisie
									$effectif_niveau[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]['effectif']++;
									$effectif_niveau[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]['info'].=$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["title"]."\n";
								}

							}
						}

						/*
						echo "<tr><td colspan='13'><pre>";
						print_r($effectif_niveau);
						echo "</pre></td></tr>";
						*/

						// Pour afficher ce qui a été validé pour LSUN par le PP ou Scol
						/*
						if(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode])) {
							//$checked[0]="";
							//$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
							//$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
							$nb_pts_dnb+=nb_pts_DNB($tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]);
						}
						*/
						// Ou plutôt, ce que ce serait avec les saisies de l'utilisateur courant
						if(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$id_groupe])) {
							//$checked[0]="";
							//$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
							//$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
							$nb_pts_dnb+=nb_pts_DNB($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$id_groupe]["niveau_maitrise"]);
						}

						/*
						echo "
						<tr>
							<td>";
						echo "<pre>";
						print_r($tab_saisies[$lig->no_gep][$cycle][$code]);
						echo "</pre>";
						echo "</td>
						</tr>";
						*/
						/*
						$valeur_precedente="";
						if(($periode>1)&&(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]]))) {
							$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]];
						}
						*/

						$valeur_precedente="";
						if(($periode>1)&&(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]]))) {

							if($SocleOuvertureSaisieComposantes=="y") {
								$val=$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"];

								$valeur_precedente="<span style='color:".$tab_couleur_niveau[$val]."' onclick=\"coche_niveau_maitrise($cpt_ele, $cpt_domaine, ".$val.")\">".$tab_traduction_niveau[$val]."</span><span id='span_periode_precedente_".$cpt_ele."_".$cpt_domaine."' style='display:none'>".$val."</span>";
							}
							else {
								$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode-1][$id_groupe]["niveau_maitrise"]];
							}
						}

						if($SocleOuvertureSaisieComposantes=="y") {
							echo "
					<tr>
						<td style='text-align:left;' id='td_libelle_".$cpt_ele."_".$cpt_domaine."'>".$libelle."</td>
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0'".$title[0].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value=''".$checked[0]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>

						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1'".$title[1].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='1'".$checked[1]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>";

							if((isset($effectif_niveau[1]['info']))&&($effectif_niveau[1]['info']!='')) {
								echo "
						<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[1]['info']."\" style='color:red;'>
							".$effectif_niveau[1]['effectif']."
						</td>";
							}
							else {
								echo "
						<td></td>";
							}

							echo "
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2'".$title[2].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='2'".$checked[2]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>";

							if((isset($effectif_niveau[2]['info']))&&($effectif_niveau[2]['info']!='')) {
								echo "
						<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[2]['info']."\" style='color:orange;'>
							".$effectif_niveau[2]['effectif']."
						</td>";
							}
							else {
								echo "
						<td></td>";
							}

							echo "
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3'".$title[3].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='3'".$checked[3]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>";

							if((isset($effectif_niveau[3]['info']))&&($effectif_niveau[3]['info']!='')) {
								echo "
						<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[3]['info']."\" style='color:green;'>
							".$effectif_niveau[3]['effectif']."
						</td>";
							}
							else {
								echo "
						<td></td>";
							}

							echo "
						<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4'".$title[4].">
							<input type='radio' 
								id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4' 
								name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
								value='4'".$checked[4]." 
								onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
						</td>";

							if((isset($effectif_niveau[4]['info']))&&($effectif_niveau[4]['info']!='')) {
								echo "
						<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[4]['info']."\" style='color:blue;'>
							".$effectif_niveau[4]['effectif']."
						</td>";
							}
							else {
								echo "
						<td></td>";
							}

							echo "
						".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
						<td>
							$valeur_precedente
						</td>" : "")."
					</tr>";
							$cpt_domaine++;
						}
						else {
							echo "
					<tr>
						<td style='text-align:left;'>".$libelle."</td>
						<td>".(($checked[0]!="") ? "X" : "")."</td>
						<td>".(($checked[1]!="") ? "<span style='color:red'>MI</span>" : "")."</td>
						<td>".(($checked[2]!="") ? "<span style='color:orange'>MF</span>" : "")."</td>
						<td>".(($checked[3]!="") ? "<span style='color:green'>MS</span>" : "")."</td>
						<td>".(($checked[4]!="") ? "<span style='color:blue'>TBM</span>" : "")."</td>
					</tr>";
						}
					}
					echo "
				</tbody>
			</table>";

					// 20170302 :
					if(($tab_cycle[$mef_code_ele]==4)&&($periode==$max_per)) {
						if($enseignement_complement) {
							//$tab_types_enseignements_complement
							$checked=array();
							$checked[0]=" checked";
							$checked[1]="";
							$checked[2]="";

							$style=array();
							$style[0]=" style='font-weight:bold'";
							$style[1]="";
							$style[2]="";
							if(isset($tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"])) {
								$style[0]="";
								$style[1]="";
								$style[2]="";

								$checked[0]="";
								$checked[1]="";
								$checked[2]="";

								$checked[$tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"]]=" checked";
								$style[$tab_niveaux_eleves_enseignement_complement[$lig->no_gep]["positionnement"]]=" style='font-weight:bold'";
							}

							if($SocleOuvertureSaisieComposantes=="y") {
								echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
				<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' value='0'".$checked[0]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' value='1'".$checked[1]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
				<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' value='2'".$checked[2]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele)\" /><label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
			</p>";
							}
							else {
								echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
				<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
				".($checked[0]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
				".($checked[1]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
				".($checked[2]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
			</p>";
							}
						}
					}

					// 20180603


					$nb_points_enseignement_complement=calcule_points_DNB_enseignement_complement($lig->no_gep);
					$nb_pts_dnb+=$nb_points_enseignement_complement;

					//20180210
					if(($tab_cycle[$mef_code_ele]==4)&&($periode==$max_per)) {
						if($is_lvr) {
							//echo "\$is_lvr=true.<br />";
							//$tab_type_LVR
							$checked=array();
							$checked[1]=" checked";
							$checked[2]="";

							$style=array();
							$style[1]=" style='font-weight:bold'";
							$style[2]="";
							if(isset($tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"])) {

								//echo "\$tab_niveaux_eleves_LVR[$lig->no_gep][\"positionnement\"] est défini.<br />";

								$style[1]="";
								$style[2]="";

								$checked[1]="";
								$checked[2]="";

								$checked[$tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"]]=" checked";
								$style[$tab_niveaux_eleves_LVR[$lig->no_gep]["positionnement"]]=" style='font-weight:bold'";
							}

							/*
							echo "<pre>";
							echo "\$style[0]=".$style[0]."\n";
							echo "\$style[1]=".$style[1]."\n";
							echo "\$style[2]=".$style[2]."\n";
							echo "</pre>";
							*/

							echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de la Langue vivante régionale (".$tab_type_LVR["code"][$code_lvr].")\">
				<strong>Langue vivante régionale&nbsp;:</strong> ".$tab_type_LVR["code"][$code_lvr]." (".$tab_type_LVR["code"][$code_lvr].")<br />
				<input type='radio' name='lvr[".$lig->no_gep."]' id='lvr_".$id_groupe."_".$lig->no_gep."_1' value='1'".$checked[1]." 
						onchange=\"checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_1');
								checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_2');\" />
				<label for='lvr_".$id_groupe."_".$lig->no_gep."_1' id='texte_lvr_".$id_groupe."_".$lig->no_gep."_1'".$style[1]."> Objectif non atteint <em>(pas de remontée)</em></label><br />

				<input type='radio' name='lvr[".$lig->no_gep."]' id='lvr_".$id_groupe."_".$lig->no_gep."_2' value='2'".$checked[2]." 
						onchange=\"checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_1');
								checkbox_change('lvr_".$id_groupe."_".$lig->no_gep."_2');\" />
				<label for='lvr_".$id_groupe."_".$lig->no_gep."_2' id='texte_lvr_".$id_groupe."_".$lig->no_gep."_2'".$style[2]."> Niveau A2 atteint</label>
			</p>";
						}
					}


					$commentaire_nb_points="";
					$style_nb_points="";
					if($nb_pts_dnb>=420) {
						$style_nb_points="font-weight:bold; color:blue;";
						$commentaire_nb_points=".\nDNB d'ores et déjà obtenu *avec mention*";
					}
					elseif($nb_pts_dnb>=350) {
						$style_nb_points="font-weight:bold; color:green;";
						$commentaire_nb_points=".\nDNB d'ores et déjà obtenu";
					}
					echo "<div id='nb_points_".$cpt_ele."' style='float:right;width:3em;text-align:center;".$style_nb_points."' title=\"Avec ce que vous avez coché \n(ce qui ne présume pas de ce qui sera finalement validé),\n le nombre de points pour le DNB".((($nb_points_enseignement_complement!="")&&($nb_points_enseignement_complement>0)) ? " (dont $nb_points_enseignement_complement points d'enseignement de complément)" : "")." serait de ".$nb_pts_dnb.$commentaire_nb_points.".\" class='fieldset_opacite50'>".$nb_pts_dnb."</div>";

					if($SocleSaisieSyntheses) {
						echo "
			<p style='margin-top:0.5em;' ".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "").">
				<strong>Synthèse&nbsp;:</strong> 
				<input type='hidden' name='indice_synthese[]' value=\"".$lig->no_gep."|".$cycle."\" />
				<textarea style='vertical-align:top;' 
						cols='80' 
						rows='4' 
						name=\"no_anti_inject_synthese_".$lig->no_gep."_".$cycle."\">".
						((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? $tab_syntheses[$lig->no_gep][$cycle]["synthese"] : "").
				"</textarea>
			</p>";
					}
					else {
						echo "
			<p style='margin-top:0.5em;' ".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "")."><strong>Synthèse&nbsp;:</strong> ".((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? nl2br($tab_syntheses[$lig->no_gep][$cycle]["synthese"]) : "<span style='color:red'>Vide</span>")."</p>";
					}

					$cpt_ele++;
				}

				// 20180518
				echo "</div>";
			}

			if($SocleOuvertureSaisieComposantes=="y") {
				if(isset($cycle_particulier)) {
					echo "
			<input type='hidden' name='cycle_particulier' value='".$cycle_particulier."' />";
				}

				echo "
			<input type='hidden' name='enregistrer_saisies' value='y' />
			<input type='hidden' name='id_groupe' value='$id_groupe' />
			<input type='hidden' name='periode' value='$periode' />
			<p><input type='submit' value='Enregistrer' /></p>

			<div id='fixe'>
				<div id='div_photo'></div>
				<input type='submit' value='Enregistrer' />
			</div>

			<script type='text/javascript'>
				".js_checkbox_change_style()."
				checkbox_change('forcer');

				function coche_colonne_ele(cpt_ele, niveau_maitrise) {
					var i;
					for(i=0;i<8;i++) {
						if(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise)) {
							document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise).checked=true;
							maj_couleurs_maitrise(cpt_ele,i);
							calcule_score_socle(cpt_ele);
						}
					}
				}

				var couleur_maitrise=new Array('black', 'red', 'orange', 'green', 'blue');
				function maj_couleurs_maitrise(cpt_ele, cpt_domaine) {
					if(document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine)) {
						/*
						if(cpt_ele<1) {
							alert('plop');
						}
						*/
						num_k='_';
						for(k=0;k<=4;k++) {
							if((document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k))&&
							(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).checked==true)) {
								num_k=k;
								break;
							}
						}

						if(num_k!='_') {
							for(k=0;k<=4;k++) {
								document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).style.backgroundColor='';
							}
							document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine).style.color=couleur_maitrise[num_k];
							if(num_k!=0) {
								document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+num_k).style.backgroundColor=couleur_maitrise[num_k];
							}
						}
					}
				}

				for(i=0;i<$cpt_ele;i++) {
					for(j=0;j<".count($tab_domaine_socle).";j++) {
						maj_couleurs_maitrise(i, j);
					}
				}

				function affiche_bull_simp(login_ele, id_classe, periode1, periode2) {
					new Ajax.Updater($('div_bull_simp'),'../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve='+login_ele+'&id_classe='+id_classe+'&periode1='+periode1+'&periode2='+periode2+'&couleur_alterne=y',{method: 'get'});

					afficher_div('div_bulletin_simplifie', 'y', 10, 10);
				}

				var points=new Array(0, 10, 25, 40, 50);

				function calcule_score_socle(num_ele) {
					var score=0;

					//alert('num_ele='+num_ele);

					if(document.getElementById('nb_points_'+num_ele)) {
						for(j=0;j<8;j++) {
							for(niveau=0;niveau<5;niveau++) {
								if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau)) {
									if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau).checked==true) {
										score+=points[niveau];
										//alert(score);
									}
								}
							}
						}

						// 20180601
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_0'
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_1' Objectif atteint (10 points)
						// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_2' Objectif dépassé (20 points)

						var input=document.getElementsByTagName('input');
						for(k=0;k<input.length;k++) {
							current_type=input[k].getAttribute('type');
							if(current_type=='radio') {
								current_id=input[k].getAttribute('id');
								if(document.getElementById(current_id)) {
									if(document.getElementById(current_id).checked==true) {
										if(current_id.substr(0,24)=='enseignement_complement_') {
											var tab=current_id.split('_');
											/*
											var tmp_chaine='';
											for(l=0;l<tab.length;l++) {
												tmp_chaine=tmp_chaine+'tab['+l+']='+tab[l]+' ';
											}
											tmp_chaine='current_id='+current_id+' et sous-chaine='+current_id.substr(0,24)+' '+tmp_chaine;
											if((k>90)&&(k<100)) {
												alert(tmp_chaine);
											}
											*/
											//current_id=enseignement_complement_4377_1_2 et sous-chaine=enseignement_complement_ tab[0]=enseignement tab[1]=complement tab[2]=4377 tab[3]=1 tab[4]=2 

											if(tab[3]==num_ele) {
												if(tab[4]==0) {
													// Pas de modif
												}
												else if(tab[4]==1) {
													score+=10;
												}
												else if(tab[4]==2) {
													score+=20;
												}
												else {
													// Pas de modif
												}
											}
										}
									}
								}
							}
						}

						// Et idem pour LVR

						document.getElementById('nb_points_'+num_ele).innerHTML='<span title=\'Non enregistré\'>'+score+' (*)</span>';
					}
				}

				checkbox_change('cycle_VIDE');
				checkbox_change('cycle_2');
				checkbox_change('cycle_3');
				checkbox_change('cycle_4');
				document.getElementById('form_choix_cycle').style.display='';

				// 20180812
				function affiche_photo_courante(photo) {
					document.getElementById('div_photo').innerHTML=\"<img src='\"+photo+\"' width='150' alt='Photo' />\";
				}

				function vide_photo_courante() {
					document.getElementById('div_photo').innerHTML='';
				}


				function coche_niveau_maitrise(cpt_ele, cpt_domaine, niveau) {
					id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
					if(document.getElementById(id)) {
						document.getElementById(id).checked=true;
						changement();
						maj_couleurs_maitrise(cpt_ele, cpt_domaine);
						calcule_score_socle(cpt_ele);
					}
				}

				function coche_niveau_maitrise_moyen(cpt_ele) {
					//alert(cpt_ele);
					for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
						//id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
						id='span_moy_'+cpt_ele+'_'+cpt_domaine;
						if(document.getElementById(id)) {
							//alert(id+' existe.');
							moy=document.getElementById(id).innerHTML;
							coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
						}
					}
				}

				function coche_niveau_maitrise_moyen_tous_eleves() {
					for(i=0;i<$cpt_ele;i++) {
						coche_niveau_maitrise_moyen(i);
					}
				}

				function copie_niveau_maitrise_periode_precedente(cpt_ele) {
					//alert(cpt_ele);
					for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
						id='span_periode_precedente_'+cpt_ele+'_'+cpt_domaine;
						if(document.getElementById(id)) {
							//alert(id+' existe.');
							moy=document.getElementById(id).innerHTML;
							coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
						}
					}
				}

				function copie_niveau_maitrise_periode_precedente_tous_eleves() {
					for(i=0;i<$cpt_ele;i++) {
						copie_niveau_maitrise_periode_precedente(i);
					}
				}

			</script>";
			}

		}

		echo "
	</fieldset>
</form>";
	}
}
elseif(isset($id_classe)) {

	$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
	$res_max=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_max)==0) {
		echo "<h3>Classe de ".get_nom_classe($id_classe)." (période $periode)</h3>";

		echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> La classe n'a pas de périodes définies.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_max=mysqli_fetch_object($res_max);
	$max_per=$lig_max->max_per;

	echo "<h3>Classe de ".get_nom_classe($id_classe)." (période ";
	if($periode>1) {
		echo "<a href='saisie_socle.php?id_classe=".$id_classe."&periode=".($periode-1)."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Période précédente\"><img src='../images/icons/arrow-left.png' class='icone16' /></a> ";
	}
	echo $periode;
	if($periode<$max_per) {
		echo " <a href='saisie_socle.php?id_classe=".$id_classe."&periode=".($periode+1)."' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Période suivante\"><img src='../images/icons/arrow-right.png' class='icone16' /></a> ";
	}
	echo ")</h3>";

	if(isset($cycle_particulier)) {
		echo "<p style='color:red; font-weight:bold; margin-bottom:1em;'>Saisie pour le cycle $cycle_particulier sans tenir compte du cycle actuel lié au MEF de l'élève.</p>";
	}

	// 20191027
	if($SocleSaisieComposantesMode==2) {
		echo "<div style='float:right; width:3em; margin:0.5em; text-align:center;' class='fieldset_opacite50'>
		<a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."&periode=".$periode."&export_csv=y' target='_blank'>CSV</a>
	</div>";
	}

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// A REVOIR POUR RECUPERER LES SAISIES D ANNEES PRECEDENTES
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// Récupérer les saisies antérieures
	$tab_saisies=array();
	$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes sec, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND sec.ine=e.no_gep AND sec.periode=jec.periode AND jec.id_classe='".$id_classe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_saisies[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode]["niveau_maitrise"]=$lig->niveau_maitrise;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			$tab_saisies[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
		}
	}

	// 20191027
	$tab_saisies_grp=array();
	$sql="SELECT DISTINCT sec.* FROM socle_eleves_composantes_groupes sec, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND sec.ine=e.no_gep AND sec.periode=jec.periode AND jec.id_classe='".$id_classe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["niveau_maitrise"]=$lig->niveau_maitrise;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			//$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="Saisi par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
			$tab_saisies_grp[$lig->ine][$lig->cycle][$lig->code_composante][$lig->periode][$lig->id_groupe]["title"]="".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
		}
	}

	$tab_syntheses=array();
	$sql="SELECT DISTINCT ses.* FROM socle_eleves_syntheses ses, eleves e, j_eleves_classes jec WHERE e.login=jec.login AND ses.ine=e.no_gep AND jec.id_classe='".$id_classe."' AND annee='".$gepiYear_debut."' AND e.no_gep!='';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			$tab_syntheses[$lig->ine][$lig->cycle]["synthese"]=$lig->synthese;
			if(!isset($tab_civ_nom_prenom[$lig->login_saisie])) {
				$tab_civ_nom_prenom[$lig->login_saisie]=civ_nom_prenom($lig->login_saisie);
			}
			//$tab_syntheses[$lig->ine][$lig->cycle]["title"]="Saisie par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2");
			$tab_syntheses[$lig->ine][$lig->cycle]["title"]=" title=\"Saisie par ".$tab_civ_nom_prenom[$lig->login_saisie]." le ".formate_date($lig->date_saisie,"y2")."\"";
		}
	}


	// INE vide:
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$id_classe."' AND jec.periode='".$periode."' AND e.no_gep='' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "<p style='color:red; margin-bottom:1em;'>Un ou des élèves ont un numéro national (INE) vide&nbsp;: ";
		$cpt_ine_vide=0;
		while($lig=mysqli_fetch_object($res)) {
			if($cpt_ine_vide>0) {
				echo ", ";
			}
			echo "<a href='../eleves/visu_eleve.php?ele_login=".$lig->login."' target='_blank'>".$lig->nom." ".$lig->prenom."</a>";
			$cpt_ine_vide++;
		}
		echo "<br />La saisie n'est pas possible pour ces élèves.<br />Demandez à l'administrateur de faire une mise à jour des informations élèves d'après Sconet.</p>";
	}

	// Récupérer la liste des élèves et leur cycle.
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='".$id_classe."' AND jec.periode='".$periode."' AND e.no_gep!='' ORDER BY e.nom, e.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red;'>Aucun élève avec INE non vide n'a été trouvé pour cette classe.</p>";
	}
	else {

		echo "<p><em>Notes&nbsp;:</em></p>
<ul>
	<li><p>Pour les bilans de fin de cycle en 6ème et 3ème, la saisie d'une synthèse est requise.<br />
	Elle n'est prise en compte dans les remontées LSUN que pour le Bilan de fin de cycle.</p></li>";
		if(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure") {
			echo "<li><p>Le module est paramétré de tel sorte qu'un niveau de maitrise ne peut <em>(normalement)</em> pas être baissé.";

			$liste_profils_autorises_forcer="";
			if(getSettingAOui("SocleSaisieComposantesForcer_scolarite")) {
				$liste_profils_autorises_forcer.="Scolarité";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_cpe")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.="CPE";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_professeur")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.="Professeur de l'enseignement";
			}
			if(getSettingAOui("SocleSaisieComposantesForcer_PP")) {
				if($liste_profils_autorises_forcer!="") {
					$liste_profils_autorises_forcer.=", ";
				}
				$liste_profils_autorises_forcer.=getSettingValue("gepi_prof_suivi")." de la classe";
			}
			if($liste_profils_autorises_forcer!="") {
				echo "<br />Certains profils <em>($liste_profils_autorises_forcer)</em> sont cependant autorisés à forcer la saisie pour baisser le niveau.";
			}

			echo "</p></li>";
		}

		if($SocleSaisieComposantesMode==2) {
			echo "<li>
				<p>
					Lorsque les professeurs ont saisi des niveaux de maitrise sur leurs enseignements, un niveau moyen est calculé.<br />
					Il apparait pour chaque élève dans la colonne <strong>Niveau moyen</strong>.<br />
					Vous pouvez prendre les niveaux moyens calculés en cliquant sur le niveau en question dans la colonne niveau moyen pour une composante donnée.<br />
					Vous pouvez prendre globalement pour l'ensemble des composantes les niveaux moyens calculés pour un élève donné en cliquant en entête de tableau élève sur l'icone <img src='../images/icons/wizard.png' class='icone16' /> de la colonne Niveau moyen.<br />
					Et enfin, vous pouvez prendre les niveaux moyens calculés pour tous les élèves et sur toutes les composantes 
					<a href='#' onclick=\"coche_niveau_maitrise_moyen_tous_eleves(); return false;\" title=\"Prendre, pour tous les élèves, les valeurs moyennes des niveaux de maitrise calculés.\">
						en cliquant ici 
						<img src='../images/icons/wizard.png' class='icone16' />
					</a>
				</p>
			</li>";
		}

		if(($periode>1)&&($SocleOuvertureSaisieComposantes=='y')) {
			echo "
			<li>
				<p>Vous pouvez recopier les niveaux de maitrise de la période précédente et ajuster ensuite les évolutions relevées.<br />
				Vous pouvez le faire en cliquant sur le niveau de maitrise dans la colonne <strong>période précédente</strong> pour une composante donnée et un élève donné.<br />
				Vous pouvez le faire pour toutes les composantes pour un élève donné en cliquant en entête de tableau élève sur l'icone <img src='../images/icons/wizard.png' class='icone16' /> de la colonne Période précédente.<br />
				Et enfin, vous pouvez recopier les niveaux de maitrise de la période précédente pour tous les élèves et sur toutes les composantes 
				<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente_tous_eleves(); return false;\" title=\"Recopier les niveaux de maitrise de la période précédente pour tous les élèves et sur toutes les composantes.\">
					en cliquant ici 
					<img src='../images/icons/paste.png' class='icone16' />
				</a>
			</li>";
		}
		echo "
</ul>";

		$tab_id_groupe_enseignement_complement=array();

		$acces_bull_simp=acces("/prepa_conseil/edit_limite.php", $_SESSION['statut']);

		$titre_infobulle="Bulletin simplifié";
		$texte_infobulle="<div id='div_bull_simp'></div>";
		$tabdiv_infobulle[]=creer_div_infobulle('div_bulletin_simplifie',$titre_infobulle,"",$texte_infobulle,"",50,0,'y','y','n','n',2);

		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();

		if(($SocleOuvertureSaisieComposantes)&&
		(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure")&&
		((getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))||
		((getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login'], $id_classe))))) {
			echo "
		<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='forcer' id='forcer' value='y' onchange=\"checkbox_change(this.id)\" ".(((isset($_POST["forcer"]))&&($_POST["forcer"]=="y")) ? " checked" : "")." /><label for='forcer' id='texte_forcer'>Forcer les saisies <br />
<em>(pour vider/baisser éventuellement les niveaux de maitrise en écrasant les saisies antérieures 
<br />(les votres ou celles de collègues (à manipuler avec précaution, dans un soucis de bonne entente entre collègues)))</em>.</label></p>";
		}

		$cpt_ele=0;
		$tab_cycle=array();
		while($lig=mysqli_fetch_object($res)) {

			$mef_code_ele=$lig->mef_code;
			// 20180608
			//echo "\$mef_code_ele=$mef_code_ele<br />";
			if(!isset($tab_cycle[$mef_code_ele])) {
				$tmp_tab_cycle_niveau=calcule_cycle_et_niveau($mef_code_ele, "", "");
				$cycle=$tmp_tab_cycle_niveau["mef_cycle"];
				$niveau=$tmp_tab_cycle_niveau["mef_niveau"];
				$tab_cycle[$mef_code_ele]=$cycle;
			}

			// 20180518
			echo "<div style='margin:0.5em; padding:0.5em;' class='fieldset_opacite50'>";

			if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
				echo "
		<p style='color:red'>Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identifié&nbsp;???</p>";
			}
			else {
				if(isset($cycle_particulier)) {
					$cycle=$cycle_particulier;
				}
				else {
					$cycle=$tab_cycle[$mef_code_ele];
				}

				$chaine_bull_simp="";
				if($acces_bull_simp) {
					$sql="SELECT id_classe, periode FROM j_eleves_classes WHERE login='".$lig->login."' ORDER BY periode DESC LIMIT 1;";
					$res_clas_ele=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_clas_ele)>0) {
						$lig_clas_ele=mysqli_fetch_object($res_clas_ele);
						$chaine_bull_simp=" <a href='../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$lig->login."&id_classe=".$lig_clas_ele->id_classe."&periode1=1&periode2=".$lig_clas_ele->periode."&couleur_alterne=y' onclick=\"affiche_bull_simp('".$lig->login."', ".$lig_clas_ele->id_classe.", 1, ".$lig_clas_ele->periode.") ;return false;\" target='_blank'><img src='../images/icons/bulletin_16.png' class='icone16' alt='BullSimp' /></a>";
					}
				}

				$nb_pts_dnb=0;

				// 20191027
				if($SocleSaisieComposantesMode==1) {
					echo "
		<p style='margin-top:2em;'><strong>".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;: ".$chaine_bull_simp."</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Domaine du socle</th>
					<th colspan='5'>Niveau de maitrise</th>";
					if(($periode>1)&&($SocleOuvertureSaisieComposantes)) {
						echo "
						<th rowspan='2'>Période<br />précédente";
						if($SocleOuvertureSaisieComposantes=="y") {
							echo " 
						<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente($cpt_ele); return false;\" title=\"Prendre pour valeur, le niveau de maitrise de la période précédente sur chacun des domaines pour cet élève\">
							<img src='../images/icons/paste.png' class='icone16' />
						</a>";
						}
						echo "</th>";
					}
					echo "
				</tr>
				<tr>
					<th title='Non encore défini' onclick=\"coche_colonne_ele($cpt_ele, 0)\">X</th>
					<th title='Maitrise Insuffisante' style='color:red' onclick=\"coche_colonne_ele($cpt_ele, 1)\">MI</th>
					<th title='Maitrise Fragile' style='color:orange' onclick=\"coche_colonne_ele($cpt_ele, 2)\">MF</th>
					<th title='Maitrise Satisfaisante' style='color:green' onclick=\"coche_colonne_ele($cpt_ele, 3)\">MS</th>
					<th title='Très Bonne Maitrise' style='color:blue' onclick=\"coche_colonne_ele($cpt_ele, 4)\">TBM</th>
				</tr>
			</thead>
			<tbody>";
				}
				else {
					echo "
		<p style='margin-top:2em;'><strong>".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;: ".$chaine_bull_simp."</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Domaine du socle</th>
					<th colspan='9'>Niveau de maitrise</th>
					<th rowspan='2' title=\"Niveau moyen de maitrise calculé d'après les saisies des professeurs.\">
						Niveau moyen";
					if($SocleOuvertureSaisieComposantes=="y") {
						echo " 
						<a href='#' onclick=\"coche_niveau_maitrise_moyen($cpt_ele); return false;\" title=\"Prendre la valeur moyenne calculée sur chacun des domaines pour cet élève\">
							<img src='../images/icons/wizard.png' class='icone16' />
						</a>";
					}
					echo "
					</th>";
					if(($periode>1)&&($SocleOuvertureSaisieComposantes)) {
						echo "
					<th rowspan='2'>
						Période<br />précédente";
						if($SocleOuvertureSaisieComposantes=="y") {
							echo " 
						<a href='#' onclick=\"copie_niveau_maitrise_periode_precedente($cpt_ele); return false;\" title=\"Prendre pour valeur, le niveau de maitrise de la période précédente sur chacun des domaines pour cet élève\">
							<img src='../images/icons/paste.png' class='icone16' />
						</a>";
						}
						echo "
					</th>";
					}
					echo "
				</tr>
				<tr>
					<th title='Non encore défini' onclick=\"coche_colonne_ele($cpt_ele, 0)\">X</th>
					<th colspan='2' title='Maitrise Insuffisante' style='color:red' onclick=\"coche_colonne_ele($cpt_ele, 1)\">MI</th>
					<th colspan='2' title='Maitrise Fragile' style='color:orange' onclick=\"coche_colonne_ele($cpt_ele, 2)\">MF</th>
					<th colspan='2' title='Maitrise Satisfaisante' style='color:green' onclick=\"coche_colonne_ele($cpt_ele, 3)\">MS</th>
					<th colspan='2' title='Très Bonne Maitrise' style='color:blue' onclick=\"coche_colonne_ele($cpt_ele, 4)\">TBM</th>
				</tr>
			</thead>
			<tbody>";
				}
				$cpt_domaine=0;
				foreach($tab_domaine_socle as $code => $libelle) {
					$checked=array();
					$checked[0]=" checked";
					$checked[1]="";
					$checked[2]="";
					$checked[3]="";
					$checked[4]="";

					$title=array();
					$title[0]="";
					$title[1]="";
					$title[2]="";
					$title[3]="";
					$title[4]="";

					$effectif_niveau=array();
					for($loop=0;$loop<5;$loop++) {
						$effectif_niveau[$loop]=array();
						$effectif_niveau[$loop]['effectif']=0;
						$effectif_niveau[$loop]['info']='';
					}

					/*
					echo "<tr><td colspan='13'>";
					echo "effectif_niveau<pre>";
					print_r($effectif_niveau);
					echo "</pre>";
					echo "</td></tr>";
					*/

					if(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode])) {
						$checked[0]="";
						$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
						$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
						$nb_pts_dnb+=nb_pts_DNB($tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]);
					}

					$valeur_precedente="";
					if(($periode>1)&&(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]]))) {

						if($SocleOuvertureSaisieComposantes=="y") {
							$val=$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"];

							$valeur_precedente="<span style='color:".$tab_couleur_niveau[$val]."' onclick=\"coche_niveau_maitrise($cpt_ele, $cpt_domaine, ".$val.")\">".$tab_traduction_niveau[$val]."</span><span id='span_periode_precedente_".$cpt_ele."_".$cpt_domaine."' style='display:none'>".$val."</span>";
						}
						else {
							$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]];
						}
					}

					if($SocleSaisieComposantesMode==2) {

						$moyenne="-";
						$niveau_moyen="-";
						$total=0;
						$total_sur=0;

						//$effectif_niveau=array();
						if(isset($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode])) {
							/*
							echo "<tr><td colspan='13'>";
							echo "\$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode]<pre>";
							print_r($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode]);
							echo "</pre>";
							echo "</td></tr>";
							*/

							foreach($tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode] as $current_id_groupe => $current_saisie) {
								$effectif_niveau[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]['effectif']++;
								$effectif_niveau[$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["niveau_maitrise"]]['info'].=$tab_saisies_grp[$lig->no_gep][$cycle][$code][$periode][$current_id_groupe]["title"]."\n";
							}

							//$moyenne="-";
							//$niveau_moyen="-";
							//$total=0;
							//$total_sur=0;
							for($loop=1;$loop<5;$loop++) {
								$total+=$effectif_niveau[$loop]['effectif']*$loop;
								$total_sur+=$effectif_niveau[$loop]['effectif'];
							}

							// Ajouter une colonne moyenne?
							if($total_sur>0) {
								$moyenne=round(10*$total/$total_sur)/10;

								if(isset($tab_traduction_niveau[round($total/$total_sur)])) {
									$moy=round($total/$total_sur);
									//$niveau_moyen="<span style='color:".$tab_couleur_niveau[$moy]."' onclick=\"coche_niveau_maitrise('niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_".$moy."')\">".$tab_traduction_niveau[$moy]."</span>";
									$niveau_moyen="<span style='color:".$tab_couleur_niveau[$moy]."' onclick=\"coche_niveau_maitrise($cpt_ele, $cpt_domaine, $moy)\">".$tab_traduction_niveau[$moy]."</span><span id='span_moy_".$cpt_ele."_".$cpt_domaine."' style='display:none'>$moy</span>";
								}
							}

						}
					}

					if($SocleOuvertureSaisieComposantes=="y") {
						echo "
				<tr>
					<td style='text-align:left;' id='td_libelle_".$cpt_ele."_".$cpt_domaine."'>".$libelle."</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0'".$title[0].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
							value=''".$checked[0]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1'".$title[1].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
							value='1'".$checked[1]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
					</td>";

						// 20191027
						if($SocleSaisieComposantesMode==2) {
							if((isset($effectif_niveau[1]['info']))&&($effectif_niveau[1]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[1]['info']."\" style=\"color:red\">".$effectif_niveau[1]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
						}

						echo "
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2'".$title[2].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
							value='2'".$checked[2]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
					</td>";

						// 20191027
						if($SocleSaisieComposantesMode==2) {
							if((isset($effectif_niveau[2]['info']))&&($effectif_niveau[2]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[2]['info']."\" style=\"color:orange\">".$effectif_niveau[2]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
						}

						echo "
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3'".$title[3].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
							value='3'".$checked[3]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
					</td>";

						// 20191027
						if($SocleSaisieComposantesMode==2) {
							if((isset($effectif_niveau[3]['info']))&&($effectif_niveau[3]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[3]['info']."\" style=\"color:green\">".$effectif_niveau[3]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
						}

						echo "
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4'".$title[4].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$cycle."|".$code."]\" 
							value='4'".$checked[4]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);calcule_score_socle($cpt_ele)\" />"."
					</td>";

						// 20191027
						if($SocleSaisieComposantesMode==2) {
							if((isset($effectif_niveau[4]['info']))&&($effectif_niveau[4]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[4]['info']."\" style=\"color:blue\">".$effectif_niveau[4]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}

							echo "
					<td>".$niveau_moyen."</td>";
						}

						echo "".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
					<td>
						$valeur_precedente
					</td>" : "")."
				</tr>";
						$cpt_domaine++;
					}
					else {

						if($SocleSaisieComposantesMode==1) {
							echo "
				<tr>
					<td style='text-align:left;'>".$libelle."</td>
					<td>".(($checked[0]!="") ? "X" : "")."</td>
					<td>".(($checked[1]!="") ? "<span style='color:red'>MI</span>" : "")."</td>
					<td>".(($checked[2]!="") ? "<span style='color:orange'>MF</span>" : "")."</td>
					<td>".(($checked[3]!="") ? "<span style='color:green'>MS</span>" : "")."</td>
					<td>".(($checked[4]!="") ? "<span style='color:blue'>TBM</span>" : "")."</td>
				</tr>";
						}
						else {
							// 20191030
							echo "
				<tr>
					<td style='text-align:left;'>".$libelle."</td>
					<td>".(($checked[0]!="") ? "X" : "")."</td>
					<td>".(($checked[1]!="") ? "<span style='color:red'>MI</span>" : "")."</td>";
							if((isset($effectif_niveau[1]['info']))&&($effectif_niveau[1]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[1]['info']."\" style=\"color:red\">".$effectif_niveau[1]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
							echo "
					<td>".(($checked[2]!="") ? "<span style='color:orange'>MF</span>" : "")."</td>";
							if((isset($effectif_niveau[2]['info']))&&($effectif_niveau[2]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[2]['info']."\" style=\"color:orange\">".$effectif_niveau[2]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
							echo "
					<td>".(($checked[3]!="") ? "<span style='color:green'>MS</span>" : "")."</td>";
							if((isset($effectif_niveau[3]['info']))&&($effectif_niveau[3]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[3]['info']."\" style=\"color:green\">".$effectif_niveau[3]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
							echo "
					<td>".(($checked[4]!="") ? "<span style='color:blue'>TBM</span>" : "")."</td>";
							if((isset($effectif_niveau[4]['info']))&&($effectif_niveau[4]['info']!='')) {
								echo "
					<td title=\"Niveau de maitrise saisi par\n".$effectif_niveau[4]['info']."\" style=\"color:blue\">".$effectif_niveau[4]['effectif']."</td>";
							}
							else {
								echo "
					<td></td>";
							}
							echo "
					<td>".$niveau_moyen."</td>
				</tr>";
						}
					}
				}
				echo "
			</tbody>
		</table>";

				// 20180603
				/*
				// Afficher l'enseignement de complément
				$sql="SELECT seec.* FROM socle_eleves_enseignements_complements seec, 
								j_groupes_enseignements_complement jgec
							WHERE seec.ine='".$lig->no_gep."' AND 
								jgec.id_groupe=seec.id_groupe 
							ORDER BY positionnement DESC;";
				//echo "$sql<br />";
				$res_ens_comp=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_ens_comp)>0) {
					while($lig_ens_comp=mysqli_fetch_object($res_ens_comp)) {
						echo get_info_grp($lig_ens_comp->id_groupe)."&nbsp;: ";
						if($lig_ens_comp->positionnement==1) {
							echo "Objectif atteint (10 points)";
							echo "<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$lig_ens_comp->id_groupe."_".$cpt_ele."_1' value='1' style='display:none' checked />";
						}
						elseif($lig_ens_comp->positionnement==2) {
							echo "Objectif dépassé (20 points)";
							echo "<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$lig_ens_comp->id_groupe."_".$cpt_ele."_2' value='2' style='display:none' checked />";
						}
						else {
							echo "Objectif non atteint";
						}
						echo "<br />";
					}
				}
				*/

				if(($tab_cycle[$mef_code_ele]==4)&&($periode==$max_per)) {
					$sql="SELECT DISTINCT jgec.* FROM j_groupes_enseignements_complement jgec, 
										j_eleves_groupes jeg 
									WHERE jgec.id_groupe=jeg.id_groupe AND 
										jeg.login='".$lig->login."' AND 
										jgec.code!='';";
					//echo "$sql<br />";
					$test=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($test)>0) {
						while($lig_test=mysqli_fetch_object($test)) {
							if(!in_array($lig_test->id_groupe, $tab_id_groupe_enseignement_complement)) {
								$tab_id_groupe_enseignement_complement[]=$lig_test->id_groupe;
							}

							$code_enseignement_complement=$lig_test->code;

							$niveau_enseignement_complement_eleve_courant=0;

							$sql="SELECT * FROM socle_eleves_enseignements_complements 
											WHERE id_groupe='".$lig_test->id_groupe."' AND ine='".$lig->no_gep."';";
							//echo "$sql<br />";
							$res_ec=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_ec)>0) {
								$lig_ec=mysqli_fetch_object($res_ec);
								$niveau_enseignement_complement_eleve_courant=$lig_ec->positionnement;
							}

							$style=array();
							$style[0]="";
							$style[1]="";
							$style[2]="";

							$checked=array();
							$checked[0]="";
							$checked[1]="";
							$checked[2]="";

							$checked[$niveau_enseignement_complement_eleve_courant]=" checked";
							$style[$niveau_enseignement_complement_eleve_courant]=" style='font-weight:bold'";

							if($SocleOuvertureSaisieComposantes=="y") {
								echo "
			<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
				<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
				".get_info_grp($lig_test->id_groupe)."&nbsp;: <br />
				<input type='radio' name='enseignement_complement".$lig_test->id_groupe."[".$lig->no_gep."]' id='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0' value='0'".$checked[0]." onchange=\"checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele);\" /><label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
				<input type='radio' name='enseignement_complement".$lig_test->id_groupe."[".$lig->no_gep."]' id='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1' value='1'".$checked[1]." onchange=\"checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele);\" /><label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
				<input type='radio' name='enseignement_complement".$lig_test->id_groupe."[".$lig->no_gep."]' id='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2' value='2'".$checked[2]." onchange=\"checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1');checkbox_change('enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2');calcule_score_socle($cpt_ele);\" /><label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
			</p>";
							}
							else {
								echo "
		<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
			<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
			".($checked[0]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
			".($checked[1]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_1'".$style[1]."> Objectif atteint</label><br />
			".($checked[2]!='' ? '<img src="../images/enabled.png" class="icone20" />' : '<img src="../images/disabled.png" class="icone20" />')."<label for='enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2' id='texte_enseignement_complement_".$lig_test->id_groupe."_".$cpt_ele."_2'".$style[2].">Objectif dépassé</label>
		</p>";
							}
						}
					}
				}

				$nb_points_enseignement_complement=calcule_points_DNB_enseignement_complement($lig->no_gep);
				$nb_pts_dnb+=$nb_points_enseignement_complement;

				$commentaire_nb_points="";
				$style_nb_points="";
				// 20180603
				if($nb_pts_dnb>=480) {
					$style_nb_points="font-weight:bold; color:blue;";
					$commentaire_nb_points=".\nDNB d'ores et déjà obtenu *avec mention*";
				}
				elseif($nb_pts_dnb>=400) {
					$style_nb_points="font-weight:bold; color:green;";
					$commentaire_nb_points=".\nDNB d'ores et déjà obtenu";
				}
				echo "<div id='nb_points_".$cpt_ele."' style='float:right;width:3em;text-align:center;".$style_nb_points."' title=\"Nombre de points pour le DNB".((($nb_points_enseignement_complement!="")&&($nb_points_enseignement_complement>0)) ? " (dont $nb_points_enseignement_complement points d'enseignement de complément)" : "")."".$commentaire_nb_points.".\" class='fieldset_opacite50'>".$nb_pts_dnb."</div>";

				if($SocleSaisieSyntheses) {
					echo "
		<p style='margin-top:0.5em;'".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "").">
			<strong>Synthèse&nbsp;:</strong> 
			<input type='hidden' name='indice_synthese[]' value=\"".$lig->no_gep."|".$cycle."\" />
			<textarea style='vertical-align:top;' 
					cols='80' 
					rows='4' 
					name=\"no_anti_inject_synthese_".$lig->no_gep."_".$cycle."\">".
					((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? $tab_syntheses[$lig->no_gep][$cycle]["synthese"] : "").
			"</textarea>
		</p>";
				}
				else {
					echo "
		<p style='margin-top:0.5em;' ".((isset($tab_syntheses[$lig->no_gep][$cycle]["title"])) ? $tab_syntheses[$lig->no_gep][$cycle]["title"] : "")."><strong>Synthèse&nbsp;:</strong> ".((isset($tab_syntheses[$lig->no_gep][$cycle]["synthese"])) ? nl2br($tab_syntheses[$lig->no_gep][$cycle]["synthese"]) : "<span style='color:red'>Vide</span>")."</p>";
				}

				$cpt_ele++;
			}

			// 20180518
			echo "</div>";

		}
		/*
		for($loop_grp_compl=0;$loop_grp_compl<count($tab_id_groupe_enseignement_complement);$loop_grp_compl++) {
			echo "
		<input type='hidden' name='id_groupe_enseignement_complement[]' value='".$tab_id_groupe_enseignement_complement[$loop_grp_compl]."' />";
		}
		*/

		if($SocleOuvertureSaisieComposantes=="y") {

			if(isset($cycle_particulier)) {
				echo "
		<input type='hidden' name='cycle_particulier' value='".$cycle_particulier."' />";
			}

			echo "
		<input type='hidden' name='enregistrer_saisies' value='y' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='periode' value='$periode' />
		<p><input type='submit' value='Enregistrer' /></p>

		<div id='fixe' style='text-align:center;'>
			<div id='div_photo'></div>
			<input type='submit' value='Enregistrer' />";
			if($SocleSaisieComposantesMode==2) {
				echo "<br />
				<a href='#' onclick=\"coche_niveau_maitrise_moyen_tous_eleves(); return false;\" title=\"Prendre, pour tous les élèves, les valeurs moyennes des niveaux de maitrise calculés.\">
					Niveaux moyens
					<img src='../images/icons/wizard.png' class='icone16' />
				</a>";
			}
			echo "
		</div>

		<script type='text/javascript'>
			".js_checkbox_change_style()."
			checkbox_change('forcer');

			function coche_colonne_ele(cpt_ele, niveau_maitrise) {
				var i;
				for(i=0;i<8;i++) {
					if(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise)) {
						document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise).checked=true;
						maj_couleurs_maitrise(cpt_ele,i);
						calcule_score_socle(cpt_ele);
					}
				}
			}

			var couleur_maitrise=new Array('black', 'red', 'orange', 'green', 'blue');
			function maj_couleurs_maitrise(cpt_ele, cpt_domaine) {
				if(document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine)) {
					/*
					if(cpt_ele<1) {
						alert('plop');
					}
					*/
					num_k='_';
					for(k=0;k<=4;k++) {
						if((document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k))&&
						(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).checked==true)) {
							num_k=k;
							break;
						}
					}

					if(num_k!='_') {
						for(k=0;k<=4;k++) {
							document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+k).style.backgroundColor='';
						}
						document.getElementById('td_libelle_'+cpt_ele+'_'+cpt_domaine).style.color=couleur_maitrise[num_k];
						if(num_k!=0) {
							document.getElementById('td_niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+num_k).style.backgroundColor=couleur_maitrise[num_k];
						}
					}
				}
			}

			for(i=0;i<$cpt_ele;i++) {
				for(j=0;j<".count($tab_domaine_socle).";j++) {
					maj_couleurs_maitrise(i, j);
				}
			}

			function affiche_bull_simp(login_ele, id_classe, periode1, periode2) {
				new Ajax.Updater($('div_bull_simp'),'../prepa_conseil/edit_limite.php?choix_edit=2&login_eleve='+login_ele+'&id_classe='+id_classe+'&periode1='+periode1+'&periode2='+periode2+'&couleur_alterne=y',{method: 'get'});

				afficher_div('div_bulletin_simplifie', 'y', 10, 10);
			}

			var points=new Array(0, 10, 25, 40, 50);

			function calcule_score_socle(num_ele) {
				var score=0;

				if(document.getElementById('nb_points_'+num_ele)) {
					for(j=0;j<8;j++) {
						for(niveau=0;niveau<5;niveau++) {
							if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau)) {
								if(document.getElementById('niveau_maitrise_'+num_ele+'_'+j+'_'+niveau).checked==true) {
									score+=points[niveau];
									//alert(score);
								}
							}
						}
					}


					// 20180601
					// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_0'
					// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_1' Objectif atteint (10 points)
					// 'enseignement_complement_'+id_groupe+'_'+cpt_ele+'_2' Objectif dépassé (20 points)

					var score_enseignement_complement=0;
					var input=document.getElementsByTagName('input');
					for(k=0;k<input.length;k++) {
						current_type=input[k].getAttribute('type');
						if(current_type=='radio') {
							current_id=input[k].getAttribute('id');
							if(document.getElementById(current_id)) {
								if(document.getElementById(current_id).checked==true) {
									if(current_id.substr(0,24)=='enseignement_complement_') {
										var tab=current_id.split('_');
										/*
										var tmp_chaine='';
										for(l=0;l<tab.length;l++) {
											tmp_chaine=tmp_chaine+'tab['+l+']='+tab[l]+' ';
										}
										tmp_chaine='current_id='+current_id+' et sous-chaine='+current_id.substr(0,24)+' '+tmp_chaine;
										if((k>90)&&(k<100)) {
											alert(tmp_chaine);
										}
										*/
										//current_id=enseignement_complement_4377_1_2 et sous-chaine=enseignement_complement_ tab[0]=enseignement tab[1]=complement tab[2]=4377 tab[3]=1 tab[4]=2 

										if(tab[3]==num_ele) {
											if(tab[4]==0) {
												// Pas de modif
											}
											else if(tab[4]==1) {
												if(score_enseignement_complement<10) {
													score_enseignement_complement=10;
												}
											}
											else if(tab[4]==2) {
												if(score_enseignement_complement<20) {
													score_enseignement_complement=20;
												}
											}
											else {
												// Pas de modif
											}
										}
									}
								}
							}
						}
					}
					score+=score_enseignement_complement;

					// Et idem pour LVR

					document.getElementById('nb_points_'+num_ele).innerHTML='<span title=\'Non enregistré\'>'+score+' (*)</span>';

					if(score>=480) {
						document.getElementById('nb_points_'+num_ele).style.color='blue';
						document.getElementById('nb_points_'+num_ele).title='DNB d ores et déjà obtenu *avec mention*';
					}
					else if(score>=400) {
						document.getElementById('nb_points_'+num_ele).style.color='green';
						document.getElementById('nb_points_'+num_ele).title='DNB d ores et déjà obtenu';
					}
					else {
						document.getElementById('nb_points_'+num_ele).style.color='black';
						document.getElementById('nb_points_'+num_ele).title='';
					}

				}
			}

			checkbox_change('cycle_VIDE');
			checkbox_change('cycle_2');
			checkbox_change('cycle_3');
			checkbox_change('cycle_4');
			document.getElementById('form_choix_cycle').style.display='';

			// 20180812
			function affiche_photo_courante(photo) {
				document.getElementById('div_photo').innerHTML=\"<img src='\"+photo+\"' width='150' alt='Photo' />\";
			}

			function vide_photo_courante() {
				document.getElementById('div_photo').innerHTML='';
			}

			/*
			function coche_niveau_maitrise(id) {
				if(document.getElementById(id)) {
					document.getElementById(id).checked=true;
				}
			}
			*/

			function coche_niveau_maitrise(cpt_ele, cpt_domaine, niveau) {
				id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
				if(document.getElementById(id)) {
					document.getElementById(id).checked=true;
					changement();
					maj_couleurs_maitrise(cpt_ele, cpt_domaine);
					calcule_score_socle(cpt_ele);
				}
			}

			function coche_niveau_maitrise_moyen(cpt_ele) {
				//alert(cpt_ele);
				for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
					//id='niveau_maitrise_'+cpt_ele+'_'+cpt_domaine+'_'+niveau;
					id='span_moy_'+cpt_ele+'_'+cpt_domaine;
					if(document.getElementById(id)) {
						//alert(id+' existe.');
						moy=document.getElementById(id).innerHTML;
						coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
					}
				}
			}

			function coche_niveau_maitrise_moyen_tous_eleves() {
				for(i=0;i<$cpt_ele;i++) {
					coche_niveau_maitrise_moyen(i);
				}
			}

			function copie_niveau_maitrise_periode_precedente(cpt_ele) {
				//alert(cpt_ele);
				for(cpt_domaine=0;cpt_domaine<".count($tab_domaine_socle).";cpt_domaine++) {
					id='span_periode_precedente_'+cpt_ele+'_'+cpt_domaine;
					if(document.getElementById(id)) {
						//alert(id+' existe.');
						moy=document.getElementById(id).innerHTML;
						coche_niveau_maitrise(cpt_ele, cpt_domaine, moy);
					}
				}
			}

			function copie_niveau_maitrise_periode_precedente_tous_eleves() {
				for(i=0;i<$cpt_ele;i++) {
					copie_niveau_maitrise_periode_precedente(i);
				}
			}

		</script>";
		}
		echo "
	</fieldset>
</form>";
	}
}
else {

	echo "<p style='color:red;'>Aucun choix ne semble avoir été fait&nbsp;???</p>";
	require("../lib/footer.inc.php");
	die();
}

require("../lib/footer.inc.php");
?>
