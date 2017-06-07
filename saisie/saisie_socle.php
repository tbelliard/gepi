<?php
/*
*
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

$msg="";
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);

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

//20170302
$tab_types_enseignements_complement=get_tab_types_enseignements_complement();

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
		if((isset($_POST["forcer"]))&&(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))) {
			$forcer="y";
		}
		else {
			$forcer="n";

			// Gérer/Tester le cas PP
			if((!isset($id_groupe))&&(isset($id_classe))) {
				if(($_SESSION['statut']=="professeur")&&(isset($_POST["forcer"]))&&(getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login'], $id_classe))) {
					$forcer="y";
				}
			}
		}

		$SocleSaisieSyntheses=false;
		if(getSettingAOui("SocleSaisieSyntheses_".$_SESSION["statut"])) {
			$SocleSaisieSyntheses=true;
		}
		elseif(($_SESSION["statut"]=="professeur")&&(isset($id_classe))&&(getSettingAOui("SocleSaisieSyntheses_PP"))&&(is_pp($_SESSION["login"], $id_classe))) {
			$SocleSaisieSyntheses=true;
		}

		$cpt_reg=0;
		$nb_err=0;
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
											}
										}
										elseif($lig->niveau_maitrise>$valeur) {
											// On veut baisser le niveau de maitrise
											if($forcer=="y") {
												$enregistrer_valeur="y";
											}
											else {
												$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de baisser le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
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
			$msg.=$cpt_reg." enregistrement(s) effectué(s).<br />";
			$msg.=$nb_err." erreur(s).<br />";
		}
		elseif(isset($id_classe)) {
			$acces_saisie="n";
			if($_SESSION["statut"]=="scolarite") {
				$sql="SELECT 1=1 FROM j_scol_classes WHERE login='".$_SESSION["login"]."' AND id_classe='".$id_classe."';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($test)>0) {
					$acces_saisie="y";
				}
			}
			elseif($_SESSION["statut"]=="cpe") {
				if(getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
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
											}
										}
										elseif($lig->niveau_maitrise>$valeur) {
											// On veut baisser le niveau de maitrise
											if($forcer=="y") {
												$enregistrer_valeur="y";
											}
											else {
												$msg.="<span title=\"".$tab_domaine_socle[$code]."\">".$code."&nbsp;:</span> Refus de baisser le niveau de maitrise pour ".$ine." <em>(".get_nom_prenom_from_INE($ine).")</em><br />";
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
			$msg.=$cpt_reg." enregistrement(s) effectué(s).<br />";
			$msg.=$nb_err." erreur(s).<br />";
		}

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
echo " | <a href=\"socle_verif.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Vérification du remplissage des bilans de composantes du socle</a>";

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

		require("../lib/footer.inc.php");
		die();
	}
	elseif(!isset($periode)) {

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

		$sql=retourne_sql_mes_classes();
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
	echo "\n<p style='color:red'>La saisie/modification des bilans de composantes du socle est fermée en période $periode.<br />Seule la consultation des saisies est possible.</p>";
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

if(isset($id_groupe)) {
	echo "\n<h2>".get_info_grp($id_groupe)." (période $periode)</h2>";

	$sql="SELECT MAX(periode) AS max_per FROM j_eleves_groupes WHERE id_groupe='$id_groupe';";
	$res_max=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_max)==0) {
		echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> Aucun élève n'a été trouvé dans le groupe/enseignement.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_max=mysqli_fetch_object($res_max);
	$max_per=$lig_max->max_per;

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// A REVOIR POUR RECUPERER LES SAISIES D ANNEES PRECEDENTES
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++

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
		$sql="SELECT jgec.* FROM j_groupes_enseignements_complement jgec, 
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
		echo "
</ul>";
		/*
		echo "<pre>";
		print_r($tab_saisies);
		echo "</pre>";
		*/
		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();

		if(($SocleOuvertureSaisieComposantes)&&
		(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure")&&
		(getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))) {
			echo "
		<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='forcer' id='forcer' value='y' /><label for='forcer'>Forcer les saisies <br />
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

			if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
				echo "
		<p style='color:red'>Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identitfié&nbsp;???</p>";
			}
			else {
				echo "
		<p style='margin-top:2em;'><strong>".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Domaine du socle</th>
					<th colspan='5'>Niveau de maitrise</th>".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
					<th rowspan='2'>Période<br />précédente</th>" : "")."
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
					$checked[0]=" checked";
					$checked[1]="";
					$checked[2]="";
					$checked[3]="";
					$checked[4]="";

					$title[0]="";
					$title[1]="";
					$title[2]="";
					$title[3]="";
					$title[4]="";

					if(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode])) {
						$checked[0]="";
						$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
						$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
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
					$valeur_precedente="";
					if(($periode>1)&&(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]]))) {
						$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]];
					}

					if($SocleOuvertureSaisieComposantes=="y") {
						echo "
				<tr>
					<td style='text-align:left;' id='td_libelle_".$cpt_ele."_".$cpt_domaine."'>".$libelle."</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0'".$title[0].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value=''".$checked[0]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1'".$title[1].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='1'".$checked[1]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2'".$title[2].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='2'".$checked[2]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3'".$title[3].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='3'".$checked[3]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4'".$title[4].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='4'".$checked[4]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
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
						$checked[0]=" checked";
						$checked[1]="";
						$checked[2]="";

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
						echo "
		<p style='margin-left:3em; text-indent:-3em;' title=\"Niveau de maitrise de l'enseignement de complément (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")\">
			<strong>Enseignement de complément&nbsp;:</strong> ".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["code"]." (".$tab_types_enseignements_complement["code"][$code_enseignement_complement]["valeur"].")<br />
			<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$lig->no_gep."_0' value='0'".$checked[0]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_2');\" /><label for='enseignement_complement_".$id_groupe."_".$lig->no_gep."_0' id='texte_enseignement_complement_".$id_groupe."_".$lig->no_gep."_0'".$style[0]."> Objectif non atteint <em>(pas de remontée)</em></label><br />
			<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$lig->no_gep."_1' value='1'".$checked[1]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_2');\" /><label for='enseignement_complement_".$id_groupe."_".$lig->no_gep."_1' id='texte_enseignement_complement_".$id_groupe."_".$lig->no_gep."_1'".$style[1]."> Objectif atteint</label><br />
			<input type='radio' name='enseignement_complement[".$lig->no_gep."]' id='enseignement_complement_".$id_groupe."_".$lig->no_gep."_2' value='2'".$checked[2]." onchange=\"checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_0');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_1');checkbox_change('enseignement_complement_".$id_groupe."_".$lig->no_gep."_2');\" /><label for='enseignement_complement_".$id_groupe."_".$lig->no_gep."_2' id='texte_enseignement_complement_".$id_groupe."_".$lig->no_gep."_2'".$style[2].">Objectif dépassé</label>
		</p>";
					}
				}

				if($SocleSaisieSyntheses) {
					echo "
		<p".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"] : "").">
			<strong>Synthèse&nbsp;:</strong> 
			<input type='hidden' name='indice_synthese[]' value=\"".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."\" />
			<textarea style='vertical-align:top;' 
					cols='80' 
					rows='4' 
					name=\"no_anti_inject_synthese_".$lig->no_gep."_".$tab_cycle[$mef_code_ele]."\">".
					((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"] : "").
			"</textarea>
		</p>";
				}
				else {
					echo "
		<p".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"] : "")."><strong>Synthèse&nbsp;:</strong> ".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"])) ? nl2br($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"]) : "<span style='color:red'>Vide</span>")."</p>";
				}

				$cpt_ele++;
			}
		}

		if($SocleOuvertureSaisieComposantes=="y") {
			echo "
		<input type='hidden' name='enregistrer_saisies' value='y' />
		<input type='hidden' name='id_groupe' value='$id_groupe' />
		<input type='hidden' name='periode' value='$periode' />
		<p><input type='submit' value='Enregistrer' /></p>

		<div id='fixe'>
			<input type='submit' value='Enregistrer' />
		</div>

		<script type='text/javascript'>
			".js_checkbox_change_style()."

			function coche_colonne_ele(cpt_ele, niveau_maitrise) {
				for(i=0;i<8;i++) {
					if(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise)) {
						document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise).checked=true;
						maj_couleurs_maitrise(cpt_ele,i);
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
		</script>";
		}
		echo "
	</fieldset>
</form>";
	}
}
elseif(isset($id_classe)) {
	echo "<h3>Classe de ".get_nom_classe($id_classe)." (période $periode)</h3>";

	$sql="SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe='$id_classe';";
	$res_max=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_max)==0) {
		echo "<p style='color:red'><strong>ANOMALIE&nbsp;:</strong> La classe n'a pas de périodes définies.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_max=mysqli_fetch_object($res_max);
	$max_per=$lig_max->max_per;

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
		echo "
</ul>";

		echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();

		if(($SocleOuvertureSaisieComposantes)&&
		(getSettingValue("SocleSaisieComposantesConcurrentes")=="meilleure")&&
		((getSettingAOui("SocleSaisieComposantesForcer_".$_SESSION["statut"]))||
		((getSettingAOui("SocleSaisieComposantesForcer_PP"))&&(is_pp($_SESSION['login'], $id_classe))))) {
			echo "
		<p style='margin-left:2em;text-indent:-2em;'><input type='checkbox' name='forcer' id='forcer' value='y' /><label for='forcer'>Forcer les saisies <br />
<em>(pour vider/baisser éventuellement les niveaux de maitrise en écrasant les saisies antérieures 
<br />(les votres ou celles de collègues (à manipuler avec précaution, dans un soucis de bonne entente entre collègues)))</em>.</label></p>";
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

			if((!isset($tab_cycle[$mef_code_ele]))||($tab_cycle[$mef_code_ele]=="")) {
				echo "
		<p style='color:red'>Le cycle courant pour ".$lig->nom." ".$lig->prenom." n'a pas pu être identitfié&nbsp;???</p>";
			}
			else {
				echo "
		<p style='margin-top:2em;'><strong>".$lig->nom." ".$lig->prenom."</strong> <em>(".get_liste_classes_eleve($lig->login).")</em> cycle ".$tab_cycle[$mef_code_ele]."&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Domaine du socle</th>
					<th colspan='5'>Niveau de maitrise</th>".((($periode>1)&&($SocleOuvertureSaisieComposantes)) ? "
					<th rowspan='2'>Période<br />précédente</th>" : "")."
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
					$checked[0]=" checked";
					$checked[1]="";
					$checked[2]="";
					$checked[3]="";
					$checked[4]="";

					$title[0]="";
					$title[1]="";
					$title[2]="";
					$title[3]="";
					$title[4]="";

					if(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode])) {
						$checked[0]="";
						$checked[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" checked";
						$title[$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["niveau_maitrise"]]=" title=\"".$tab_saisies[$lig->no_gep][$cycle][$code][$periode]["title"]."\"";
					}

					$valeur_precedente="";
					if(($periode>1)&&(isset($tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]))&&(isset($tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]]))) {
						$valeur_precedente=$tab_traduction_niveau_couleur[$tab_saisies[$lig->no_gep][$cycle][$code][$periode-1]["niveau_maitrise"]];
					}

					if($SocleOuvertureSaisieComposantes=="y") {
						echo "
				<tr>
					<td style='text-align:left;' id='td_libelle_".$cpt_ele."_".$cpt_domaine."'>".$libelle."</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0'".$title[0].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_0' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value=''".$checked[0]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1'".$title[1].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_1' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='1'".$checked[1]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2'".$title[2].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_2' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='2'".$checked[2]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3'".$title[3].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_3' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='3'".$checked[3]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
					</td>
					<td id='td_niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4'".$title[4].">
						<input type='radio' 
							id='niveau_maitrise_".$cpt_ele."_".$cpt_domaine."_4' 
							name=\"niveau_maitrise[".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."|".$code."]\" 
							value='4'".$checked[4]." 
							onchange=\"changement(); maj_couleurs_maitrise($cpt_ele,$cpt_domaine);\" />"."
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
					<td>".(($checked[4]!="") ? "<span style='color:blue'>TBM</span>" : "")."</td>
				</tr>";
					}
				}
				echo "
			</tbody>
		</table>";

				if($SocleSaisieSyntheses) {
					echo "
		<p".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"] : "").">
			<strong>Synthèse&nbsp;:</strong> 
			<input type='hidden' name='indice_synthese[]' value=\"".$lig->no_gep."|".$tab_cycle[$mef_code_ele]."\" />
			<textarea style='vertical-align:top;' 
					cols='80' 
					rows='4' 
					name=\"no_anti_inject_synthese_".$lig->no_gep."_".$tab_cycle[$mef_code_ele]."\">".
					((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"] : "").
			"</textarea>
		</p>";
				}
				else {
					echo "
		<p".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"])) ? $tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["title"] : "")."><strong>Synthèse&nbsp;:</strong> ".((isset($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"])) ? nl2br($tab_syntheses[$lig->no_gep][$tab_cycle[$mef_code_ele]]["synthese"]) : "<span style='color:red'>Vide</span>")."</p>";
				}

				$cpt_ele++;
			}
		}

		if($SocleOuvertureSaisieComposantes=="y") {
			echo "
		<input type='hidden' name='enregistrer_saisies' value='y' />
		<input type='hidden' name='id_classe' value='$id_classe' />
		<input type='hidden' name='periode' value='$periode' />
		<p><input type='submit' value='Enregistrer' /></p>

		<div id='fixe'>
			<input type='submit' value='Enregistrer' />
		</div>

		<script type='text/javascript'>

			function coche_colonne_ele(cpt_ele, niveau_maitrise) {
				for(i=0;i<8;i++) {
					if(document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise)) {
						document.getElementById('niveau_maitrise_'+cpt_ele+'_'+i+'_'+niveau_maitrise).checked=true;
						maj_couleurs_maitrise(cpt_ele,i);
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
