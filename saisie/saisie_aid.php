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


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Si le témoin temoin_check_srv() doit être affiché, on l'affichera dans la page à côté de Enregistrer.
$aff_temoin_serveur_hors_entete="y";

//debug_var();

// Intialisation
unset($indice_aid);
$indice_aid = isset($_POST["indice_aid"]) ? $_POST["indice_aid"] : (isset($_GET["indice_aid"]) ? $_GET["indice_aid"] : NULL);
unset($aid_id);
$aid_id = isset($_POST["aid_id"]) ? $_POST["aid_id"] : (isset($_GET["aid_id"]) ? $_GET["aid_id"] : NULL);

$msg="";

// On appelle les informations de l'aid pour les afficher :
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
$nom_aid = @old_mysql_result($call_data, 0, "nom");
$note_max = @old_mysql_result($call_data, 0, "note_max");
$type_note = @old_mysql_result($call_data, 0, "type_note");
$display_begin = @old_mysql_result($call_data, 0, "display_begin");
$display_end = @old_mysql_result($call_data, 0, "display_end");


if ($_SESSION['statut'] != "secours") {
	$sql="SELECT a.nom, a.id, a.numero FROM j_aid_utilisateurs j, aid a WHERE (j.id_utilisateur = '" . $_SESSION['login'] . "' and a.id = j.id_aid and a.indice_aid=j.indice_aid and j.indice_aid='$indice_aid') ORDER BY a.numero, a.nom";
	//echo "$sql<br />";
	$call_prof_aid = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombre_aid = mysqli_num_rows($call_prof_aid);
	if ($nombre_aid == "0") {
		header("Location: ../accueil.php?msg=$nom_aid : Vous n'êtes pas professeur responsable. Vous n'avez donc pas à entrer d'appréciations.");
		die();
	}
}


//===========================
// Couleurs utilisées
$couleur_devoirs = '#AAE6AA';
$couleur_fond = '#AAE6AA';
$couleur_moy_cn = '#96C8F0';
//===========================

$nom_table = "class_temp".md5(SESSION_ID());

if(isset($aid_id)) {
	$tab_aid=get_tab_aid($aid_id);

	$nb_periode=$tab_aid['maxper'];
}

if (isset($_POST['is_posted'])) {
	check_token();

	$nb_reg=0;

	// Appréciations groupe classe

	$nb_periode=$tab_aid['maxper'];

	//=====================================
	// Tableau pour les autorisations exceptionnelles de saisie
	// Il n'est pris en compte comme le getSettingValue('autoriser_correction_bulletin') que pour une période partiellement close
	$une_autorisation_exceptionnelle_de_saisie_au_moins='n';
	$tab_autorisation_exceptionnelle_de_saisie_app=array();
	$tab_autorisation_exceptionnelle_de_saisie_note=array();
	$date_courante=time();
	//echo "\$aid_id=$aid_id<br />";
	//echo "\$date_courante=$date_courante<br />";
	$k=1;
	while ($k <= $nb_periode) {
		$tab_autorisation_exceptionnelle_de_saisie_app[$k]='n';
		$tab_autorisation_exceptionnelle_de_saisie_note[$k]='n';
		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_aid='$aid_id' AND periode='$k';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;
			// 20131204
			//echo "\$date_limite=$date_limite en période $k.<br />";
			//echo "\$date_courante=$date_courante.<br />";

			if($date_courante<$date_limite) {
				$tab_autorisation_exceptionnelle_de_saisie_app[$k]='y';
				if($lig->mode=='acces_complet') {
					$tab_autorisation_exceptionnelle_de_saisie_app[$k]='yy';
					$proposer_liens_enregistrement="y";
				}
				$une_autorisation_exceptionnelle_de_saisie_au_moins='y';
			}
		}
		//echo "\$tab_autorisation_exceptionnelle_de_saisie_app[$k]=".$tab_autorisation_exceptionnelle_de_saisie_app[$k]."<br />";

		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM acces_exceptionnel_matieres_notes WHERE id_aid='$aid_id' AND periode='$k';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;
			// 20131204
			//echo "\$date_limite=$date_limite en période $k.<br />";
			//echo "\$date_courante=$date_courante.<br />";

			if($date_courante<$date_limite) {
				$tab_autorisation_exceptionnelle_de_saisie_note[$k]='y';
				$une_autorisation_exceptionnelle_de_saisie_au_moins='y';
			}
		}
		//echo "\$tab_autorisation_exceptionnelle_de_saisie_note[$k]=".$tab_autorisation_exceptionnelle_de_saisie_note[$k]."<br />";

		$k++;
	}
	//=====================================

	//$tab_notes_app_aid_prec=array();
	$liste_modifs='';
	$texte_mail_proposition='';

	$k=1;
	while ($k < $nb_periode + 1) {
		if (($k >= $display_begin) and ($k <= $display_end)) {
			if(isset($_POST['no_anti_inject_app_grp_'.$k])) {
				//f_write_tmp("\$_POST['app_grp_'.$k]=".$_POST['app_grp_'.$k]);
				//f_write_tmp("\$current_group[\"classe\"][\"ver_periode\"]['all'][$k]=".$current_group["classe"]["ver_periode"]['all'][$k]);
				if(($tab_aid["classe"]["ver_periode"]['all'][$k]>=2)||
				(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol')))||
				(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours'))||
					(($tab_aid["classe"]["ver_periode"]['all'][$k]>=1)&&(in_array($_SESSION['login'], $tab_aid['profs']['list']))&&
					(isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&
					($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='yy'))
				) {

					if (isset($NON_PROTECT["app_grp_".$k])) {
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
					}
					else {
						$app = "";
					}
					//echo "<pre>$k: $app</pre>";
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$app=suppression_sauts_de_lignes_surnumeraires($app);

					$sql="SELECT * FROM aid_appreciations_grp WHERE (id_aid='".$aid_id."' AND periode='$k' AND indice_aid='".$indice_aid."')";
					//echo "$sql<br />";
					$test_grp_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
					$test = mysqli_num_rows($test_grp_app_query);
					if ($test != "0") {
						// 20190703
						$lig_tmp=mysqli_fetch_object($test_grp_app_query);
						if(stripslashes(nl2br($app))!=nl2br($lig_tmp->appreciation)) {
							//$tab_notes_app_aid_prec[$k]['app_grp']="Modification de l'appréciation de groupe en période $k de \n=========================\n".$lig_tmp->appreciation."\n=========================\nen\n=========================\n".$app."\n=========================\n";
							$liste_modifs.="\nModification de l'appréciation de groupe en période $k de \n=========================\n".$lig_tmp->appreciation."\n=========================\nen\n=========================\n".($app)."\n=========================\n";
						}

						if ($app != "") {
							$sql="UPDATE aid_appreciations_grp SET appreciation='" . $app . "' WHERE (id_aid='".$aid_id."' AND periode='$k' AND indice_aid='".$indice_aid."')";
						} else {
							$sql="DELETE FROM aid_appreciations_grp WHERE (id_aid='".$aid_id."' AND periode='$k' AND indice_aid='".$indice_aid."')";
						}
						//echo "$sql<br />";
						$register=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$register) {
							$msg.="Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";
						}
						else {
							$nb_reg++;
						}
					} else {
						if ($app != "") {
							// 20190703
							$liste_modifs.="\nAjout d'une nouvelle appréciation de groupe en période $k\n=========================\n".$app."\n=========================\n";

							$sql="INSERT INTO aid_appreciations_grp SET id_aid='".$aid_id."', periode='$k', indice_aid='".$indice_aid."', appreciation='" . $app . "'";
							//echo "$sql<br />";
							$register=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$register) {
								$msg.="Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
				elseif(($tab_aid["classe"]["ver_periode"]['all'][$k]>=1)&&(in_array($_SESSION['login'], $tab_aid['profs']['list']))&&
					(isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&
					($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='y')) {
					// Proposition à enregistrer

					if (isset($NON_PROTECT["app_grp_".$k])) {
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
					}
					else {
						$app = "";
					}
					//echo "<pre>$k: $app</pre>";
					// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
					$app=suppression_sauts_de_lignes_surnumeraires($app);

					// COMPARER L'APPRECIATION AVEC CELLE EXISTANTE
					//$texte_mail_proposition.='';

					//echo "<p>app='$app'</p>";

					$correction_proposee=true;
					$sql="SELECT * FROM aid_appreciations_grp WHERE (id_aid='$aid_id' AND periode='$k');";
					//echo "$sql<br />";
					$res_aag=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_aag)) {
						$lig_aag=mysqli_fetch_object($res_aag);
						//echo "<p>lig_aag->appreciation=$lig_aag->appreciation</p><p>app=$app</p>";
						if($lig_aag->appreciation==$app) {
							$correction_proposee=false;
						}
					}
					elseif($app=='') {
						$correction_proposee=false;
					}

					if($correction_proposee) {

						//$texte_mail="";

						$sql="SELECT * FROM matieres_app_corrections WHERE (login='' AND id_aid='$aid_id' AND periode='$k');";
						$test_correction=mysqli_query($GLOBALS["mysqli"], $sql);
						$test=mysqli_num_rows($test_correction);
						if ($test!="0") {
							if ($app!="") {
								$sql="UPDATE matieres_app_corrections SET appreciation='$app' WHERE (login='' AND id_aid='$aid_id' AND periode='$k');";
								//echo "$sql<br />";
								$register=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des corrections pour $correction_nom_prenom_eleve sur la période $k.<br />";} 
								else {
									$msg.="Enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $k effectué.<br />";
									$url_racine_gepi=getSettingValue('url_racine_gepi');
									if($url_racine_gepi!="") {
										//$valider_ou_rejeter="<a href='".$url_racine_gepi."/saisie/validation_corrections.php' target='_blank'>valider ou rejeter la proposition</a>";
										$valider_ou_rejeter="valider ou rejeter la proposition (".$url_racine_gepi."/saisie/validation_corrections.php)";
									}
									else {
										$valider_ou_rejeter="valider ou rejeter la proposition";
									}

									$texte_mail_proposition.="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation de groupe sur la période $k\r\nen ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].").\r\n\r\nVous pouvez ".$valider_ou_rejeter." la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
								}
							} else {
								$sql="DELETE FROM matieres_app_corrections WHERE (login='' AND id_aid='$aid_id' AND periode='$k');";
								//echo "$sql<br />";
								$register=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$register) {$msg = $msg."Erreur lors de la suppression de la proposition de correction pour l'appréciation de groupe sur la période $k.<br />";} 
								else {
									$msg.="Suppression de la proposition de correction pour l'appréciation de groupe sur la période $k effectuée.<br />";
									$texte_mail_proposition.="Suppression de la proposition de correction pour l'appréciation de groupe\r\nsur la période $k en ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\r\n";
								}
							}
		
						}
						else {
							if ($app != "") {
								$sql="INSERT INTO matieres_app_corrections SET login='', id_aid='$aid_id', periode='$k', appreciation='".$app."';";
								//echo "$sql<br />";
								$register=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $k.<br />";}
								else {
									$msg.="Enregistrement de la proposition de correction pour l'appréciation de groupe sur la période $k effectué.<br />";
									$url_racine_gepi=getSettingValue('url_racine_gepi');
									if($url_racine_gepi!="") {
										$valider_ou_rejeter="valider ou rejeter la proposition (".$url_racine_gepi."/saisie/validation_corrections.php)";
									}
									else {
										$valider_ou_rejeter="valider ou rejeter la proposition";
									}
									$texte_mail_proposition.="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation de groupe sur la période $k\r\nen ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].").\r\n\r\nVous pouvez ".$valider_ou_rejeter." en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
								}
							}
						}

						/*
						if($texte_mail!="") {
							$envoi_mail_actif=getSettingValue('envoi_mail_actif');
							if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
								$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
							}
		
							if($envoi_mail_actif=='y') {
								$email_destinataires="";
								//$sql="select email from utilisateurs where statut='secours' AND email!='';";
								$sql="(select DISTINCT email from utilisateurs where statut='secours' AND email!='')";
								$sql.=" UNION (select DISTINCT email from utilisateurs u, 
													j_scol_classes jsc, 
													j_eleves_classes jec,
													j_aid_eleves jae  
												where u.statut='scolarite' AND 
													u.email!='' AND 
													u.login=jsc.login AND 
													jsc.id_classe=jec.id_classe AND 
													jec.login=jae.login AND 
													jae.id_aid='$aid_id')";
								//echo "$sql<br />";
								$req=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($req)>0) {
									while($lig_u=mysqli_fetch_object($req)) {
										if(check_mail($lig_u->email)) {
											if($email_destinataires!='') {
												$email_destinataires.=", ";
											}
											$email_destinataires.=$lig_u->email;
											$tab_param_mail['destinataire'][]=$lig_u->email;
										}
									}
		
									$email_declarant="";
									$nom_declarant="";
									$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										if(check_mail($lig_u->email)) {
											$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
											$email_declarant=$lig_u->email;
											$tab_param_mail['cc'][]=$lig_u->email;
											$tab_param_mail['cc_name'][]=$nom_declarant;
										}
									}
		
									$email_autres_profs_grp="";
									// Recherche des autres profs du groupe
									$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_aid_utilisateurs jau WHERE jau.id_aid='$aid_id' AND jau.id_utilisateur=u.login AND u.login!='".$_SESSION['login']."' AND u.email!='';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										while($lig_u=mysqli_fetch_object($req)) {
											if(check_mail($lig_u->email)) {
												if($email_autres_profs_grp!='') {
													$email_autres_profs_grp.=", ";
												}
												$email_autres_profs_grp.=",".$lig_u->email;
												$tab_param_mail['cc'][]=$lig_u->email;
											}
										}
									}
		
									$sujet_mail="Demande de validation de correction d'appréciation";

									$ajout_header="";
									if($email_declarant!="") {
										$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
										if($email_autres_profs_grp!='') {
											$ajout_header.=", $email_autres_profs_grp";
										}
										$ajout_header.="\r\n";
										$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
										$tab_param_mail['replyto']=$email_declarant;
										$tab_param_mail['replyto_name']=$nom_declarant;

									}
									elseif($email_autres_profs_grp!='') {
										$ajout_header.="Cc: $email_autres_profs_grp\r\n";
									}

									$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
									$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;

									$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);
								}
							}
						}
						*/
					}
				}
				else {
					$msg.="Anomalie: Tentative de saisie d'une appréciation de groupe-classe alors que la période n'est pas ouverte en saisie.<br />";
				}
			}
		}
		$k++;
	}


	// =============================
	// Appréciations et notes élèves

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];
	//echo "\$indice_max_log_eleve=$indice_max_log_eleve<br />";

	$sql="SELECT e.* FROM eleves e, j_aid_eleves j WHERE (j.id_aid='$aid_id' and e.login = j.login and j.indice_aid='$indice_aid')";
	//echo "$sql<br />";
	$quels_eleves=mysqli_query($GLOBALS["mysqli"], $sql);
	$lignes = mysqli_num_rows($quels_eleves);
	//echo "\$lignes=$lignes (nombre d'élèves inscrits dans l'AID)<br />";
	$j = '0';
	while($j < $lignes) {
		$reg_eleve_login = old_mysql_result($quels_eleves, $j, "login");

		//echo "<hr /><p>Elève $reg_eleve_login<br />";

		//echo "\$reg_eleve_login=$reg_eleve_login<br />";
		//$call_classe = mysql_query("SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login = '$reg_eleve_login' ORDER BY periode DESC");
		$sql="SELECT DISTINCT id_classe FROM j_eleves_classes WHERE login = '$reg_eleve_login' ORDER BY periode DESC";
		//echo "$sql<br />";
		$call_classe = mysqli_query($GLOBALS["mysqli"], $sql);
		//echo "$sql<br />";
		// On passe en revue tous les élèves inscrits à l'AID, même si ils ne sont pas dans une classe...
		// ... par contre, dans la partie saisie, seuls les élèves effectivement dans une classe sont proposés.
		if(mysqli_num_rows($call_classe)>0){
			$id_classe = old_mysql_result($call_classe, '0', "id_classe");
			$sql="SELECT * FROM periodes WHERE id_classe = '$id_classe'  ORDER BY num_periode";
			//echo "$sql<br />";
			$periode_query = mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_periode = mysqli_num_rows($periode_query) ;
			if ($type_note == 'last') {$last_periode_aid = min($nb_periode,$display_end);}
			$k='1';
			while ($k < $nb_periode + 1) {
				if(isset($_POST['log_eleve_'.$k])) {
					//echo "<p>Période $k<br />";
					if (($k >= $display_begin) and ($k <= $display_end)) {
						$ver_periode[$k] = old_mysql_result($periode_query, $k-1, "verouiller");
						//if ($ver_periode[$k] == "N"){
						if ((($_SESSION['statut']=='secours')&&($ver_periode[$k] != "O"))||
							(($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol'))&&($ver_periode[$k] != "O"))||
							(($_SESSION['statut']!='secours')&&($ver_periode[$k] == "N"))) {
							//echo "La période n'est pas fermée en saisie.<br />";
							//=========================
							unset($log_eleve);
							$log_eleve=$_POST['log_eleve_'.$k];
							unset($note_eleve);
							// On n'a pas nécessairement de note
							// cf: if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
							if(isset($_POST['note_eleve_'.$k])) {
								$note_eleve=$_POST['note_eleve_'.$k];
							}
							//=========================

							//echo "\$log_eleve=$log_eleve et \$note_eleve=$note_eleve<br />";

							//=========================
							// AJOUT: boireaus 20071003
							// Récupération du numéro de l'élève dans les saisies:
							$num_eleve=-1;
							//for($i=0;$i<count($log_eleve);$i++){
							for($i=0;$i<$indice_max_log_eleve;$i++){
								if(isset($log_eleve[$i])){
									if(my_strtolower("$reg_eleve_login"."_t".$k)==my_strtolower("$log_eleve[$i]")){
										$num_eleve=$i;
										break;
									}
								}
							}
							//echo "\$num_eleve=$num_eleve<br />";
							if($num_eleve!=-1){
								//echo "L'élève a été trouvé dans le tableau \$log_eleve soumis.<br />";
								//=========================
								// MODIF: boireaus 20071003
								//$nom_log = $reg_eleve_login."_t".$k;
								$nom_log = "app_eleve_".$k."_".$num_eleve;
								//=========================

								//$nom_log2 = $reg_eleve_login."_n_t".$k;

								if (isset($NON_PROTECT[$nom_log])){
									$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
								}
								else{
									$app = "";
								}

								//echo "\$app=$app<br />";

								$elev_statut = '';
								//=========================
								if(isset($note_eleve[$num_eleve])) {
									// cf: if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
									$note=$note_eleve[$num_eleve];

									if (($note == 'disp')) {
										$note = '0';
										$elev_statut = 'disp';
									}
									else if (($note == '-')) {
										$note = '0';
										$elev_statut = '-';
									}
									else if (($note == 'abs')) {
										$note = '0';
										$elev_statut = 'abs';
									} else if (preg_match ("/^[0-9\.\,]{1,}$/", $note)) {
										$note = str_replace(",", ".", "$note");
										if (($note < 0) or ($note > $note_max)) {
											$note = '';
											$elev_statut = '';
										}
									}
									else {
										$note = '';
										$elev_statut = 'other';
									}
								}
								//=========================

								//echo "\$note=$note et \$elev_statut=$elev_statut<br />";

								$sql="SELECT * FROM aid_appreciations WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
								//echo "$sql<br />";
								$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
								$test = mysqli_num_rows($test_eleve_app_query);
								if ($test != "0") {
									//echo "Il y avait déjà un enregistrement.<br />";
									if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {

										// 20190703
										$lig_tmp=mysqli_fetch_object($test_eleve_app_query);
										if(stripslashes(nl2br($app))!=nl2br($lig_tmp->appreciation)) {
											$liste_modifs.="\nModification de l'appréciation de ".get_nom_prenom_eleve($reg_eleve_login)." en période $k de \n=========================\n".$lig_tmp->appreciation."\n=========================\nen\n=========================\n".($app)."\n=========================\n";
										}
										if(($note!=$lig_tmp->note)||($elev_statut!=$lig_tmp->statut)) {
											$liste_modifs.="\nModification de la note de ".get_nom_prenom_eleve($reg_eleve_login)." en période $k de ".$lig_tmp->note." (".$lig_tmp->statut.") en ".$note." (".$elev_statut.")\n";
										}

										$sql="UPDATE aid_appreciations SET appreciation='$app', note='$note',statut='$elev_statut' WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
										//echo "$sql<br />";
										$register=mysqli_query($GLOBALS["mysqli"], $sql);
									} else {
										// 20190703
										$lig_tmp=mysqli_fetch_object($test_eleve_app_query);
										if(stripslashes(nl2br($app))!=nl2br($lig_tmp->appreciation)) {
											$liste_modifs.="\nModification de l'appréciation de ".get_nom_prenom_eleve($reg_eleve_login)." en période $k de \n=========================\n".$lig_tmp->appreciation."\n=========================\nen\n=========================\n".($app)."\n=========================\n";
										}

										$sql="UPDATE aid_appreciations SET appreciation='$app' WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
										//echo "$sql<br />";
										$register=mysqli_query($GLOBALS["mysqli"], $sql);
									}
								} else {
									//echo "Il n'y avait pas encore d'enregistrement.<br />";
									if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
										// 20190703
										$liste_modifs.="\nAjout d'une appréciation pour ".get_nom_prenom_eleve($reg_eleve_login)." en période $k\n=========================\n".($app)."\n=========================\navec la note ".$note." (".$elev_statut.")\n";

										$sql="INSERT INTO aid_appreciations SET login='$reg_eleve_login',id_aid='$aid_id',periode='$k',appreciation='$app', note = '$note', statut='$elev_statut', indice_aid='$indice_aid';";
										//echo "$sql<br />";
										$register=mysqli_query($GLOBALS["mysqli"], $sql);
									} else {
										$liste_modifs.="\nAjout d'une appréciation pour ".get_nom_prenom_eleve($reg_eleve_login)." en période $k\n=========================\n".($app)."\n=========================\n";

										$sql="INSERT INTO aid_appreciations SET login='$reg_eleve_login',id_aid='$aid_id',periode='$k',appreciation='$app',statut='$elev_statut', indice_aid='$indice_aid';";
										//echo "$sql<br />";
										$register=mysqli_query($GLOBALS["mysqli"], $sql);
									}
								}
								if (!$register) {
									$msg.="Erreur lors de l'enregistrement des données de la période $k pour $reg_eleve_login<br />";
								}
								else {
									$nb_reg++;
								}
								/*
								else {
									$msg.="Les modifications ont été enregistrées !<br />";
									$affiche_message = 'yes';
								}
								*/
							}
						}
						elseif(($ver_periode[$k] == "P")&&(in_array($_SESSION['login'], $tab_aid['profs']['list']))&&
							(
								((isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_app[$k]!='n'))||
								((isset($tab_autorisation_exceptionnelle_de_saisie_note[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_note[$k]=='y'))
							)) {
							// A FAIRE

							if((isset($tab_autorisation_exceptionnelle_de_saisie_note[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_note[$k]=='y')) {

								//=========================
								unset($log_eleve);
								$log_eleve=$_POST['log_eleve_'.$k];
								unset($note_eleve);
								// On n'a pas nécessairement de note
								// cf: if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
								if(isset($_POST['note_eleve_'.$k])) {
									$note_eleve=$_POST['note_eleve_'.$k];
								}
								//=========================

								//echo "\$log_eleve=$log_eleve et \$note_eleve=$note_eleve<br />";

								//=========================
								// AJOUT: boireaus 20071003
								// Récupération du numéro de l'élève dans les saisies:
								$num_eleve=-1;
								//for($i=0;$i<count($log_eleve);$i++){
								for($i=0;$i<$indice_max_log_eleve;$i++){
									if(isset($log_eleve[$i])){
										if(my_strtolower("$reg_eleve_login"."_t".$k)==my_strtolower("$log_eleve[$i]")){
											$num_eleve=$i;
											break;
										}
									}
								}
								//echo "\$num_eleve=$num_eleve<br />";
								if($num_eleve!=-1){
									//echo "L'élève a été trouvé dans le tableau \$log_eleve soumis.<br />";
									//=========================
									// MODIF: boireaus 20071003
									//$nom_log = $reg_eleve_login."_t".$k;
									$nom_log = "app_eleve_".$k."_".$num_eleve;
									//=========================

									//$nom_log2 = $reg_eleve_login."_n_t".$k;

									if (isset($NON_PROTECT[$nom_log])){
										$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
									}
									else{
										$app = "";
									}

									//echo "\$app=$app<br />";

									$elev_statut = '';
									//=========================
									if(isset($note_eleve[$num_eleve])) {
										// cf: if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
										$note=$note_eleve[$num_eleve];

										if (($note == 'disp')) {
											$note = '0';
											$elev_statut = 'disp';
										}
										else if (($note == '-')) {
											$note = '0';
											$elev_statut = '-';
										}
										else if (($note == 'abs')) {
											$note = '0';
											$elev_statut = 'abs';
										} else if (preg_match ("/^[0-9\.\,]{1,}$/", $note)) {
											$note = str_replace(",", ".", "$note");
											if (($note < 0) or ($note > $note_max)) {
												$note = '';
												$elev_statut = '';
											}
										}
										else {
											$note = '';
											$elev_statut = 'other';
										}
									}
									//=========================

									//echo "\$note=$note et \$elev_statut=$elev_statut<br />";

									$sql="SELECT * FROM aid_appreciations WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
									//echo "$sql<br />";
									$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
									$test = mysqli_num_rows($test_eleve_app_query);
									if ($test != "0") {
										//echo "Il y avait déjà un enregistrement.<br />";
										if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {

											// 20190703
											$lig_tmp=mysqli_fetch_object($test_eleve_app_query);

											if(($note!=$lig_tmp->note)||($elev_statut!=$lig_tmp->statut)) {
												$liste_modifs.="\nModification de la note de ".get_nom_prenom_eleve($reg_eleve_login)." en période $k de ".$lig_tmp->note." (".$lig_tmp->statut.") en ".$note." (".$elev_statut.")\n";
											}

											$sql="UPDATE aid_appreciations SET note='$note',statut='$elev_statut' WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
											//echo "$sql<br />";
											$register=mysqli_query($GLOBALS["mysqli"], $sql);

											if (!$register) {
												$msg.="Erreur lors de l'enregistrement des données de la période $k pour $reg_eleve_login<br />";
											}
											else {
												$nb_reg++;
											}
										}
									} else {
										//echo "Il n'y avait pas encore d'enregistrement.<br />";
										if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
											$sql="INSERT INTO aid_appreciations SET login='$reg_eleve_login', id_aid='$aid_id', periode='$k', note = '$note', statut='$elev_statut', indice_aid='$indice_aid';";
											//echo "$sql<br />";
											$register=mysqli_query($GLOBALS["mysqli"], $sql);

											if (!$register) {
												$msg.="Erreur lors de l'enregistrement des données de la période $k pour $reg_eleve_login<br />";
											}
											else {
												$nb_reg++;
											}
										}
									}
								}
							}

							if(isset($tab_autorisation_exceptionnelle_de_saisie_app[$k])) {
								if($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='yy') {

									//=========================
									unset($log_eleve);
									$log_eleve=$_POST['log_eleve_'.$k];

									//=========================
									// Récupération du numéro de l'élève dans les saisies:
									$num_eleve=-1;
									//for($i=0;$i<count($log_eleve);$i++){
									for($i=0;$i<$indice_max_log_eleve;$i++){
										if(isset($log_eleve[$i])){
											if(my_strtolower("$reg_eleve_login"."_t".$k)==my_strtolower("$log_eleve[$i]")){
												$num_eleve=$i;
												break;
											}
										}
									}
									//echo "\$num_eleve=$num_eleve<br />";
									if($num_eleve!=-1){
										//echo "L'élève a été trouvé dans le tableau \$log_eleve soumis.<br />";
										//=========================
										// MODIF: boireaus 20071003
										//$nom_log = $reg_eleve_login."_t".$k;
										$nom_log = "app_eleve_".$k."_".$num_eleve;
										//=========================

										//$nom_log2 = $reg_eleve_login."_n_t".$k;

										if (isset($NON_PROTECT[$nom_log])) {
											$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
										}
										else {
											$app = "";
										}

										$sql="SELECT * FROM aid_appreciations WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
										//echo "$sql<br />";
										$test_eleve_app_query = mysqli_query($GLOBALS["mysqli"], $sql);
										$test = mysqli_num_rows($test_eleve_app_query);
										if ($test != "0") {
											//echo "Il y avait déjà un enregistrement.<br />";
											$lig_tmp=mysqli_fetch_object($test_eleve_app_query);
											if(stripslashes(nl2br($app))!=nl2br($lig_tmp->appreciation)) {
												$liste_modifs.="\nModification de l'appréciation de ".get_nom_prenom_eleve($reg_eleve_login)." en période $k de \n=========================\n".$lig_tmp->appreciation."\n=========================\nen\n=========================\n".($app)."\n=========================\n";
											}

											$sql="UPDATE aid_appreciations SET appreciation='$app' WHERE (login='$reg_eleve_login' AND periode='$k' and id_aid = '$aid_id' and indice_aid='$indice_aid');";
											//echo "$sql<br />";
											$register=mysqli_query($GLOBALS["mysqli"], $sql);
										} else {
											//echo "Il n'y avait pas encore d'enregistrement.<br />";
											$liste_modifs.="\nAjout d'une appréciation pour ".get_nom_prenom_eleve($reg_eleve_login)." en période $k\n=========================\n".($app)."\n=========================\n";

											$sql="INSERT INTO aid_appreciations SET login='$reg_eleve_login', id_aid='$aid_id', periode='$k', appreciation='$app', indice_aid='$indice_aid';";
											//echo "$sql<br />";
											$register=mysqli_query($GLOBALS["mysqli"], $sql);
										}
										if (!$register) {
											$msg.="Erreur lors de l'enregistrement des données de la période $k pour $reg_eleve_login<br />";
										}
										else {
											$nb_reg++;
										}
									}
								}
								elseif($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='y') {

									// Proposition à enregistrer

									//=========================
									unset($log_eleve);
									$log_eleve=$_POST['log_eleve_'.$k];

									//=========================
									// Récupération du numéro de l'élève dans les saisies:
									$num_eleve=-1;
									//for($i=0;$i<count($log_eleve);$i++){
									for($i=0;$i<$indice_max_log_eleve;$i++){
										if(isset($log_eleve[$i])){
											if(my_strtolower("$reg_eleve_login"."_t".$k)==my_strtolower("$log_eleve[$i]")){
												$num_eleve=$i;
												break;
											}
										}
									}
									//echo "\$num_eleve=$num_eleve<br />";
									if($num_eleve!=-1) {
										//echo "L'élève a été trouvé dans le tableau \$log_eleve soumis.<br />";
										//=========================
										// MODIF: boireaus 20071003
										//$nom_log = $reg_eleve_login."_t".$k;
										$nom_log = "app_eleve_".$k."_".$num_eleve;
										//=========================

										//$nom_log2 = $reg_eleve_login."_n_t".$k;

										if (isset($NON_PROTECT[$nom_log])) {
											$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
										}
										else {
											$app = "";
										}

										$correction_proposee=true;
										$sql="SELECT * FROM aid_appreciations WHERE (login='".$reg_eleve_login."' AND id_aid='$aid_id' AND periode='$k');";
										$res_aa=mysqli_query($GLOBALS["mysqli"], $sql);
										if(mysqli_num_rows($res_aa)) {
											$lig_aa=mysqli_fetch_object($res_aa);
											if($lig_aa->appreciation==$app) {
												$correction_proposee=false;
											}
										}

										if($correction_proposee) {

											$correction_nom_prenom_eleve=get_nom_prenom_eleve($reg_eleve_login);

											//$texte_mail="";

											$sql="SELECT * FROM matieres_app_corrections WHERE (login='".$reg_eleve_login."' AND id_aid='$aid_id' AND periode='$k');";
											//echo "$sql<br />";
											$test_correction=mysqli_query($GLOBALS["mysqli"], $sql);
											$test=mysqli_num_rows($test_correction);
											if ($test!="0") {
												if ($app!="") {
													$sql="UPDATE matieres_app_corrections SET appreciation='$app' WHERE (login='".$reg_eleve_login."' AND id_aid='$aid_id' AND periode='$k');";
													//echo "$sql<br />";
													$register=mysqli_query($GLOBALS["mysqli"], $sql);
													if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des corrections pour $correction_nom_prenom_eleve sur la période $k.<br />";} 
													else {
														$msg.="Enregistrement de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k effectué.<br />";
														$url_racine_gepi=getSettingValue('url_racine_gepi');
														if($url_racine_gepi!="") {
															//$valider_ou_rejeter="<a href='".$url_racine_gepi."/saisie/validation_corrections.php' target='_blank'>valider ou rejeter la proposition</a>";
															$valider_ou_rejeter="valider ou rejeter la proposition (".$url_racine_gepi."/saisie/validation_corrections.php)";
														}
														else {
															$valider_ou_rejeter="valider ou rejeter la proposition";
														}

														$texte_mail_proposition.="Une correction proposée a été mise à jour par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k\r\nen ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].").\r\n\r\nVous pouvez ".$valider_ou_rejeter." la proposition en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
													}
												} else {
													$sql="DELETE FROM matieres_app_corrections WHERE (login='".$reg_eleve_login."' AND id_aid='$aid_id' AND periode='$k');";
													//echo "$sql<br />";
													$register=mysqli_query($GLOBALS["mysqli"], $sql);
													if (!$register) {$msg = $msg."Erreur lors de la suppression de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k.<br />";} 
													else {
														$msg.="Suppression de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k effectuée.<br />";
														$texte_mail_proposition.="Suppression de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve."\r\nsur la période $k en ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].")\r\npar ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj').".\r\n";
													}
												}
		
											}
											else {
												if ($app != "") {
													$sql="INSERT INTO matieres_app_corrections SET login='".$reg_eleve_login."', id_aid='$aid_id', periode='$k', appreciation='".$app."';";
													//echo "$sql<br />";
													$register=mysqli_query($GLOBALS["mysqli"], $sql);
													if (!$register) {$msg = $msg."Erreur lors de l'enregistrement de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k.<br />";}
													else {
														$msg.="Enregistrement de la proposition de correction pour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k effectué.<br />";
														$url_racine_gepi=getSettingValue('url_racine_gepi');
														if($url_racine_gepi!="") {
															$valider_ou_rejeter="valider ou rejeter la proposition (".$url_racine_gepi."/saisie/validation_corrections.php)";
														}
														else {
															$valider_ou_rejeter="valider ou rejeter la proposition";
														}
														$texte_mail_proposition.="Une correction a été proposée par ".casse_mot($_SESSION['prenom'],'majf2')." ".casse_mot($_SESSION['nom'],'maj')."\r\npour l'appréciation pour ".$correction_nom_prenom_eleve." sur la période $k\r\nen ".$tab_aid['nom']." (".$tab_aid["nom_complet"]." en ".$tab_aid["classlist_string"].").\r\n\r\nVous pouvez ".$valider_ou_rejeter." en vous connectant avec un compte de statut scolarité ou secours.\r\nVous trouverez en page d'accueil, dans la rubrique Saisie, un message en rouge concernant la Correction de bulletins.\r\n";
													}
												}
											}

											/*
											if($texte_mail!="") {
												$envoi_mail_actif=getSettingValue('envoi_mail_actif');
												if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
													$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
												}
		
												if($envoi_mail_actif=='y') {
													$email_destinataires="";
													//$sql="select email from utilisateurs where statut='secours' AND email!='';";
													$sql="(select DISTINCT email from utilisateurs where statut='secours' AND email!='')";
													$sql.=" UNION (select DISTINCT email from utilisateurs u, 
																		j_scol_classes jsc, 
																		j_eleves_classes jec,
																		j_aid_eleves jae  
																	where u.statut='scolarite' AND 
																		u.email!='' AND 
																		u.login=jsc.login AND 
																		jsc.id_classe=jec.id_classe AND 
																		jec.login=jae.login AND 
																		jae.id_aid='$aid_id')";
													//echo "$sql<br />";
													$req=mysqli_query($GLOBALS["mysqli"], $sql);
													if(mysqli_num_rows($req)>0) {
														while($lig_u=mysqli_fetch_object($req)) {
															if(check_mail($lig_u->email)) {
																if($email_destinataires!='') {
																	$email_destinataires.=", ";
																}
																$email_destinataires.=$lig_u->email;
																$tab_param_mail['destinataire'][]=$lig_u->email;
															}
														}
		
														$email_declarant="";
														$nom_declarant="";
														$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
														$req=mysqli_query($GLOBALS["mysqli"], $sql);
														if(mysqli_num_rows($req)>0) {
															$lig_u=mysqli_fetch_object($req);
															if(check_mail($lig_u->email)) {
																$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
																$email_declarant=$lig_u->email;
																$tab_param_mail['cc'][]=$lig_u->email;
																$tab_param_mail['cc_name'][]=$nom_declarant;
															}
														}
		
														$email_autres_profs_grp="";
														// Recherche des autres profs du groupe
														$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_aid_utilisateurs jau WHERE jau.id_aid='$aid_id' AND jau.id_utilisateur=u.login AND u.login!='".$_SESSION['login']."' AND u.email!='';";
														//echo "$sql<br />";
														$req=mysqli_query($GLOBALS["mysqli"], $sql);
														if(mysqli_num_rows($req)>0) {
															while($lig_u=mysqli_fetch_object($req)) {
																if(check_mail($lig_u->email)) {
																	if($email_autres_profs_grp!='') {
																		$email_autres_profs_grp.=", ";
																	}
																	$email_autres_profs_grp.=",".$lig_u->email;
																	$tab_param_mail['cc'][]=$lig_u->email;
																}
															}
														}
		
														$sujet_mail="Demande de validation de correction d'appréciation";

														$ajout_header="";
														if($email_declarant!="") {
															$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
															if($email_autres_profs_grp!='') {
																$ajout_header.=", $email_autres_profs_grp";
															}
															$ajout_header.="\r\n";
															$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
															$tab_param_mail['replyto']=$email_declarant;
															$tab_param_mail['replyto_name']=$nom_declarant;

														}
														elseif($email_autres_profs_grp!='') {
															$ajout_header.="Cc: $email_autres_profs_grp\r\n";
														}

														$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
														$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;

														$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);
													}
												}
											}
											*/
										}

									}

								}
							}
						}
					}
				}
				$k++;
			}
		}
		$j++;
	}

	if($liste_modifs!='') {
		if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='secours')||($_SESSION['statut']=='cpe')) {
			// S'il y a d'autres profs associés à l'AID, on les informe

			$sql="SELECT DISTINCT u.nom, u.prenom, u.civilite, u.login, u.email FROM utilisateurs u, 
													j_aid_utilisateurs jau 
												WHERE jau.id_utilisateur=u.login AND 
													jau.id_aid='".$aid_id."' AND 
													jau.indice_aid='".$indice_aid."' AND 
													u.login!='".$_SESSION['login']."';";
			$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_u)>0) {
				$tab_param_mail=array();

				$texte_mail="Je viens d'effectuer (".strftime("%d/%m/%Y à %H:%M:%S").") des saisies sur l'AID ".get_info_aid($aid_id, array('nom_general_complet', 'classes', 'profs'), '')."\n".$liste_modifs."\n\nCordialement.\n-- \n".civ_nom_prenom($_SESSION['login']);

				$email_destinataires="";
				$tab_login_destinataire=array();
				$cpt_dest=0;
				while($lig_u=mysqli_fetch_object($res_u)) {
					if($cpt_dest>0) {
						$email_destinataires.=", ";
					}

					if(check_mail($lig_u->email)) {
						$email_destinataires=$lig_u->email;
						$tab_param_mail['destinataire'][]=$lig_u->email;
						$cpt_dest++;
					}

					$tab_login_destinataire[]=$lig_u->login;
				}

				// Au cas où l'envoi de mail foirerait, on met toujours un message:
				// On met un message en page d'accueil
				for($loop=0;$loop<count($tab_login_destinataire);$loop++) {
					$r_sql = "INSERT INTO messages
							SET texte = '".mysqli_real_escape_string($GLOBALS["mysqli"], nl2br($texte_mail))."',
							date_debut = '".time()."',
							date_fin = '".(time()+7*24*3600)."',
							date_decompte = '',
							auteur='".$_SESSION['login']."',
							statuts_destinataires = '_',
							login_destinataire='".$tab_login_destinataire[$loop]."';";
					//echo "$r_sql<br />";
					$retour=mysqli_query($GLOBALS["mysqli"], $r_sql);
					if ($retour) {
						$id_message=mysqli_insert_id($GLOBALS["mysqli"]);

						$contenu_cor='
						<form method="POST" action="accueil.php" name="f_suppression_message">
						<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">
						<input type="hidden" name="supprimer_message" value="'.$id_message.'">
						<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="images/icons/delete.png"></button>
						</form>'.mysqli_real_escape_string($GLOBALS["mysqli"], nl2br($texte_mail));
						$r_sql="UPDATE messages SET texte='".$contenu_cor."' WHERE id='".$id_message."';";
						//echo "<span style='color:green'>$r_sql</span><br />";
						$retour=mysqli_query($GLOBALS["mysqli"], $r_sql);
					}
				}

				$envoi_mail_actif=getSettingValue('envoi_mail_actif');
				if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
					$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
				}

				if($envoi_mail_actif=='y') {
					if($email_destinataires!="") {

						$email_declarant='';
						$email_cc='';
						$cpt_cc=0;
						if((isset($_SESSION['email']))&&(check_mail($_SESSION['email']))) {
							$nom_declarant=$_SESSION['prenom'].' '.$_SESSION['nom'];
							$email_declarant=$_SESSION['email'];
							//echo "\$email_declarant=$email_declarant<br />";
							$tab_param_mail['from']=$_SESSION['email'];
							$tab_param_mail['from_name']=$_SESSION['prenom'].' '.$_SESSION['nom'];
						}

						$sujet_mail="Correction d'appréciation AID";

						$ajout_header="";
						if($email_declarant!="") {
							$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
							$tab_param_mail['cc'][$cpt_cc]=$email_declarant;
							$tab_param_mail['cc_name'][$cpt_cc]=$nom_declarant;
							if($email_cc!='') {
								$ajout_header.=", $email_cc";
							}
							$ajout_header.="\r\n";
							$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
							$tab_param_mail['replyto']=$email_declarant;
							$tab_param_mail['replyto_name']=$nom_declarant;

						}
						elseif($email_cc!='') {
							$ajout_header.="Cc: $email_cc\r\n";
						}

						$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
						//$texte_mail=$salutation.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_declarant;
						$texte_mail=$salutation.",\n\n".$texte_mail;

						/*
						echo "<pre>";
						print_r($tab_param_mail);
						echo "</pre>";
						*/

						$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header,"plain",$tab_param_mail);
						if($envoi) {
							$msg_mail='Mail d\'information envoyé aux animateurs de l\'AID ('.strftime('%d/%m/%Y à %H:%M:%S').').<br />';
						}
						else {
							$msg_mail='Échec de l\'envoi d\'un mail d\'information aux animateurs de l\'AID ('.strftime('%d/%m/%Y à %H:%M:%S').').<br />';
						}
					}
				}


			}
		}
	}

	if($texte_mail_proposition!="") {
		$envoi_mail_actif=getSettingValue('envoi_mail_actif');
		if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
			$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}

		if($envoi_mail_actif=='y') {
			$email_destinataires="";
			//$sql="select email from utilisateurs where statut='secours' AND email!='';";
			$sql="(select DISTINCT email from utilisateurs where statut='secours' AND email!='')";
			$sql.=" UNION (select DISTINCT email from utilisateurs u, 
								j_scol_classes jsc, 
								j_eleves_classes jec,
								j_aid_eleves jae  
							where u.statut='scolarite' AND 
								u.email!='' AND 
								u.login=jsc.login AND 
								jsc.id_classe=jec.id_classe AND 
								jec.login=jae.login AND 
								jae.id_aid='$aid_id')";
			//echo "$sql<br />";
			$req=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($req)>0) {
				while($lig_u=mysqli_fetch_object($req)) {
					if(check_mail($lig_u->email)) {
						if($email_destinataires!='') {
							$email_destinataires.=", ";
						}
						$email_destinataires.=$lig_u->email;
						$tab_param_mail['destinataire'][]=$lig_u->email;
					}
				}

				$email_declarant="";
				$nom_declarant="";
				$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
				$req=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($req)>0) {
					$lig_u=mysqli_fetch_object($req);
					if(check_mail($lig_u->email)) {
						$nom_declarant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
						$email_declarant=$lig_u->email;
						$tab_param_mail['cc'][]=$lig_u->email;
						$tab_param_mail['cc_name'][]=$nom_declarant;
					}
				}

				$email_autres_profs_grp="";
				// Recherche des autres profs du groupe
				$sql="SELECT DISTINCT u.email FROM utilisateurs u, j_aid_utilisateurs jau WHERE jau.id_aid='$aid_id' AND jau.id_utilisateur=u.login AND u.login!='".$_SESSION['login']."' AND u.email!='';";
				//echo "$sql<br />";
				$req=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($req)>0) {
					while($lig_u=mysqli_fetch_object($req)) {
						if(check_mail($lig_u->email)) {
							if($email_autres_profs_grp!='') {
								$email_autres_profs_grp.=", ";
							}
							$email_autres_profs_grp.=",".$lig_u->email;
							$tab_param_mail['cc'][]=$lig_u->email;
						}
					}
				}

				$sujet_mail="Demande de validation de correction d'appréciation";

				$ajout_header="";
				if($email_declarant!="") {
					$ajout_header.="Cc: $nom_declarant <".$email_declarant.">";
					if($email_autres_profs_grp!='') {
						$ajout_header.=", $email_autres_profs_grp";
					}
					$ajout_header.="\r\n";
					$ajout_header.="Reply-to: $nom_declarant <".$email_declarant.">\r\n";
					$tab_param_mail['replyto']=$email_declarant;
					$tab_param_mail['replyto_name']=$nom_declarant;

				}
				elseif($email_autres_profs_grp!='') {
					$ajout_header.="Cc: $email_autres_profs_grp\r\n";
				}

				$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
				$texte_mail_proposition=$salutation.",\n\n".$texte_mail_proposition."\nCordialement.\n-- \n".$nom_declarant;

				$envoi = envoi_mail($sujet_mail, $texte_mail_proposition, $email_destinataires, $ajout_header, "plain", $tab_param_mail);
			}
		}
	}


	if(($msg=="")&&($nb_reg>0)) {
		$msg.="Les modifications ont été enregistrées !<br />";
		$affiche_message = 'yes';
	}
	if(isset($msg_mail)) {
		$msg.=$msg_mail;
	}
}
//
// on calcule le nombre maximum de périodes dans une classe
//

$call_data = mysqli_query($GLOBALS["mysqli"], "DROP TABLE IF EXISTS $nom_table");
$call_data = mysqli_query($GLOBALS["mysqli"], "CREATE TEMPORARY TABLE $nom_table (id_classe integer, num integer NOT NULL)");
$msg_pb="";
if(!$call_data) {
	$msg_pb="ERREUR&nbsp;: La création d'une table temporaire a échoué.<br />Le droit de créer des tables temporaires n'est peut-être pas attribué à l'utilisateur MySQL.<br />La présente page risque de ne pas fonctionner.";
}
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes");
$nombre_lignes = mysqli_num_rows($call_data);
$i = 0;
while ($i < $nombre_lignes){
	$id_classe = old_mysql_result($call_data, $i, "id");
	$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe' ORDER BY num_periode");
	$k = mysqli_num_rows($periode_query);
	$call_reg = mysqli_query($GLOBALS["mysqli"], "insert into $nom_table Values('$id_classe', '$k')");
	$i++;
}
$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT max(num) as max FROM $nom_table");
$nb_periode_max = old_mysql_result($call_data, 0, "max");

$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage  = 'Des notes ou des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations ".$nom_aid;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
insere_lien_calendrier_crob("right");
?>
<script type="text/javascript" language="javascript">
change = 'no';
</script>
<p class=bold><a href="../accueil.php" onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil</a>
<?php

if (!isset($aid_id)) {
	?></p><?php
	if ($_SESSION['statut'] != "secours") {
		$sql="SELECT a.nom, a.id, a.numero FROM j_aid_utilisateurs j, aid a WHERE (j.id_utilisateur = '" . $_SESSION['login'] . "' and a.id = j.id_aid and a.indice_aid=j.indice_aid and j.indice_aid='$indice_aid') ORDER BY a.numero, a.nom";
		//echo "$sql<br />";
		$call_prof_aid = mysqli_query($GLOBALS["mysqli"], $sql);
		$nombre_aid = mysqli_num_rows($call_prof_aid);
		if ($nombre_aid == "0") {
			echo "<p>$nom_aid : Vous n'êtes pas professeur responsable. Vous n'avez donc pas à entrer d'appréciations.</p></html></body>\n";
			die();
		} else {
			$i = "0";
			echo "<p>Vous êtes professeur responsable dans les $nom_aid :<br />\n";
			while ($i < $nombre_aid) {
				$aid_display = old_mysql_result($call_prof_aid, $i, "nom");
				$aid_id = old_mysql_result($call_prof_aid, $i, "id");
				$aid_numero = old_mysql_result($call_prof_aid, $i, "numero")." : ";
				if ($aid_numero == " : ") {$aff_numero_aid = "";} else {$aff_numero_aid = $aid_numero;}
				echo "<br /><span class='bold'>".$aff_numero_aid.$aid_display."</span>
				 --- <a href='saisie_aid.php?aid_id=".$aid_id."&amp;indice_aid=".$indice_aid."'>Saisir les appréciations pour cette rubrique</a>\n";
				$i++;
			}
			echo "</p>\n";
		}
	} else {
		$call_prof_aid = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY numero, nom");
		$nombre_aid = mysqli_num_rows($call_prof_aid);
		if ($nombre_aid == "0") {
			echo "<p>$nom_aid : Il n'y a pas d'entrées !</p>\n";
		} else {
			$i = "0";
			echo "<p><b>".$nom_aid." - Saisie des appréciations :</b><br />\n";
			while ($i < $nombre_aid) {
				$aid_display = old_mysql_result($call_prof_aid, $i, "nom");
				$aid_id = old_mysql_result($call_prof_aid, $i, "id");
				$aid_numero = old_mysql_result($call_prof_aid, $i, "numero")." : ";
				if ($aid_numero == " : ") {$aff_numero_aid = "";} else {$aff_numero_aid = $aid_numero;}
				echo "<br /><span class='bold'>".$aff_numero_aid.$aid_display."</span> --- <a href='saisie_aid.php?aid_id=$aid_id&amp;indice_aid=$indice_aid'>Saisir les appréciations.</a>\n";
				$i++;
			}
			echo "</p>\n";
		}
	}
} else {

	//================================================
	$proposer_liens_enregistrement="n";
	if(isset($tab_aid)) {
		$k = '1';
		while ($k < $nb_periode_max + 1) {
			if (($k >= $display_begin) and ($k <= $display_end)) {
				if((isset($tab_aid["classe"]["ver_periode"]['all'][$k]))&&
				(($tab_aid["classe"]["ver_periode"]['all'][$k]>=2)||
				(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol')))||
				(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours')))) {
					$proposer_liens_enregistrement="y";
					break;
				}
			}
			$k++;
		}
	}
	//================================================

	//=====================================
	// Tableau pour les autorisations exceptionnelles de saisie
	// Il n'est pris en compte comme le getSettingValue('autoriser_correction_bulletin') que pour une période partiellement close
	$une_autorisation_exceptionnelle_de_saisie_au_moins='n';
	$tab_autorisation_exceptionnelle_de_saisie_app=array();
	$tab_autorisation_exceptionnelle_de_saisie_note=array();
	$date_courante=time();
	//echo "\$aid_id=$aid_id<br />";
	//echo "\$date_courante=$date_courante<br />";
	$k=1;
	while ($k <= $nb_periode) {
		$tab_autorisation_exceptionnelle_de_saisie_app[$k]='n';
		$tab_autorisation_exceptionnelle_de_saisie_note[$k]='n';
		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite, mode FROM matieres_app_delais WHERE id_aid='$aid_id' AND periode='$k';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;
			// 20131204
			//echo "\$date_limite=$date_limite en période $k.<br />";
			//echo "\$date_courante=$date_courante.<br />";

			if($date_courante<$date_limite) {
				$tab_autorisation_exceptionnelle_de_saisie_app[$k]='y';
				if($lig->mode=='acces_complet') {
					$tab_autorisation_exceptionnelle_de_saisie_app[$k]='yy';
					$proposer_liens_enregistrement="y";
				}
				$une_autorisation_exceptionnelle_de_saisie_au_moins='y';
			}
		}
		//echo "\$tab_autorisation_exceptionnelle_de_saisie_app[$k]=".$tab_autorisation_exceptionnelle_de_saisie_app[$k]."<br />";

		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM acces_exceptionnel_matieres_notes WHERE id_aid='$aid_id' AND periode='$k';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;
			// 20131204
			//echo "\$date_limite=$date_limite en période $k.<br />";
			//echo "\$date_courante=$date_courante.<br />";

			if($date_courante<$date_limite) {
				$tab_autorisation_exceptionnelle_de_saisie_note[$k]='y';
				$une_autorisation_exceptionnelle_de_saisie_au_moins='y';
			}
		}
		//echo "\$tab_autorisation_exceptionnelle_de_saisie_note[$k]=".$tab_autorisation_exceptionnelle_de_saisie_note[$k]."<br />";

		$k++;
	}
	//=====================================


	echo " | <a href='saisie_aid.php?indice_aid=$indice_aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choix $nom_aid</a>";
	if(acces("/saisie/import_note_app_aid.php", $_SESSION['statut'])) {
		echo " | <a href='import_note_app_aid.php?indice_aid=$indice_aid&aid_id=$aid_id' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Importer les notes et/ou appréciations depuis un fichier CSV.\">Import CSV</a>";
	}
	if(acces("/saisie/import_note_app_aid2.php", $_SESSION['statut'])) {
		echo " | <a href='import_note_app_aid2.php?indice_aid=$indice_aid&aid_id=$aid_id' onclick=\"return confirm_abandon (this, change, '$themessage')\" title=\"Importer les notes et/ou appréciations depuis le carnet de notes ou les bulletins dans l'un de vos enseignements.\">Import d'après un autre enseignement</a>";
	}
	echo "</p>\n";

	if($msg_pb!='') {
		echo "<p style='color:red'>$msg_pb</p>\n";
	}

	echo "<form enctype='multipart/form-data' action='saisie_aid.php' method='post'>\n";
	if($proposer_liens_enregistrement=='y') {
		echo "<center><input type='submit' value='Enregistrer' /></center>\n";
	}

	$sql="SELECT * FROM aid where (id = '$aid_id'  and indice_aid='$indice_aid')";
	$calldata = mysqli_query($GLOBALS["mysqli"], $sql);
	$lig_aid=mysqli_fetch_object($calldata);
	$aid_nom = $lig_aid->nom;

	echo "<h2>Appréciations $nom_aid : $aid_nom";
	// Accès à la fiche?
	if(VerifAccesFicheProjet($_SESSION['login'],$aid_id,$indice_aid,'','')) {
		echo " <a href='../aid/modif_fiches.php?aid_id=$aid_id&amp;indice_aid=$indice_aid&amp;action=modif&amp;retour=index_fiches.php' title=\"Éditer la fiche projet et notamment le résumé dans un nouvel onglet.\" target='_blank'><img src='../images/edit16.png' class='icone16' alt='Insérer' /></a>";
	}
	echo "</h2>\n";

	echo "<div style='float:right;width:20em;'>".get_info_categorie_aid("", $aid_id)."</div>";

	if(trim($lig_aid->resume)!="") {
		echo "<p style='margin-left:5.7em;text-indent:-5.7em;margin-bottom:1em;'><strong>Résumé&nbsp;:</strong> ".nl2br($lig_aid->resume);
		echo "<span id='resumeBulletin' style='display:none'>".$lig_aid->resume."</span>";
		if($lig_aid->resumeBulletin=="y") {
			echo "<br /><em style='color:blue;'>Le résumé ci-dessus est automatiquement intégré à l'appréciation de l'élève sur le bulletin.</em>";
		}
		else {
			echo " <a href='#' onclick=\"ajoute_resume_a_textarea_vide()\" title=\"Insérer le résumé dans les appréciations vides.\"><img src='../images/icons/wizard.png' class='icone16' alt='Insérer' /></a>";
		}
		echo "</p>";
	}


	echo "<script type='text/javascript'>
	function ajoute_resume_a_textarea_vide() {
		champs_textarea=document.getElementsByTagName('textarea');
		//alert('champs_textarea.length='+champs_textarea.length);
		for(i=0;i<champs_textarea.length;i++){
			if(champs_textarea[i].value=='') {
				champs_textarea[i].value=document.getElementById('resumeBulletin').value;
			}
		}
	}

	function ajoute_app_periode_a_textarea(num_per) {
		//alert('plop');
		if(document.getElementById('app_grp_'+num_per)) {

			champs_textarea=document.getElementsByTagName('textarea');
			//alert('champs_textarea.length='+champs_textarea.length);
			for(i=0;i<champs_textarea.length;i++) {
				champ_courant=champs_textarea[i];
				id_textarea=champ_courant.getAttribute('id');
				if(id_textarea) {
					//if(i<3) {alert('id_textarea='+id_textarea)}
					if(id_textarea.substring(0,2)=='n'+num_per) {
						if(champs_textarea[i].value=='') {
							champs_textarea[i].value=document.getElementById('app_grp_'+num_per).value;
						}
						else {
							champs_textarea[i].value=champs_textarea[i].value+' '+document.getElementById('app_grp_'+num_per).value;
						}
					}
				}
			}

		}
	}

	function ajoute_app_periode_a_textarea_vide(num_per) {
		if(document.getElementById('app_grp_'+num_per)) {

			champs_textarea=document.getElementsByTagName('textarea');
			for(i=0;i<champs_textarea.length;i++) {
				champ_courant=champs_textarea[i];
				id_textarea=champ_courant.getAttribute('id');
				if(id_textarea) {
					if(id_textarea.substring(0,2)=='n'+num_per) {
						if(champs_textarea[i].value=='') {
							champs_textarea[i].value=document.getElementById('app_grp_'+num_per).value;
						}
					}
				}
			}

		}
	}

</script>\n";

	/*
	echo "<pre>";
	print_r($tab_aid);
	echo "</pre>";
	*/

	echo "<p class='bold'>Groupe-classe&nbsp;:</p>
<table class='boireaus boireaus_alt' border=1 cellspacing=2 cellpadding=5>
	<tr>";
			$i = "1";
			while ($i < $nb_periode_max + 1) {
				if (($i >= $display_begin) and ($i <= $display_end)) {
					$nom_periode[$i] = old_mysql_result($periode_query, $i-1, "nom_periode");
					echo "
			<th>
				<b>$nom_periode[$i]</b>";

					//echo "\$tab_aid[\"classe\"][\"ver_periode\"]['all'][$i]=".$tab_aid["classe"]["ver_periode"]['all'][$i]."<br />";

					if((isset($tab_aid["classe"]["ver_periode"]['all'][$i]))&&
					(($tab_aid["classe"]["ver_periode"]['all'][$i]>=2)||
					(($tab_aid["classe"]["ver_periode"]['all'][$i]!=0)&&($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol')))||
					(($tab_aid["classe"]["ver_periode"]['all'][$i]!=0)&&($_SESSION['statut']=='secours')))) {

						echo "
						 <a href='#' onclick=\"ajoute_app_periode_a_textarea($i)\" title=\"Ajouter l'avis sur le groupe aux appréciations de la période $i.\"><img src='../images/icons/add.png' class='icone16' alt='Insérer' /></a> 
						<a href='#' onclick=\"ajoute_app_periode_a_textarea_vide($i)\" title=\"Insérer l'avis sur le groupe aux appréciations de la période $i\n*lorsque l'appréciation est vide*.\"><img src='../images/icons/wizard.png' class='icone16' alt='Insérer' /></a>";

					}
					echo "
			</th>";
				}
				$i++;
			}
			echo "
	</tr>
	<tr>";
			$k = '1';
			while ($k < $nb_periode_max + 1) {
				if (($k >= $display_begin) and ($k <= $display_end)) {

					$current_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations_grp WHERE (periode='$k' AND id_aid = '$aid_id' and indice_aid='$indice_aid')");
					if(mysqli_num_rows($current_app_query)>0) {
						$lig_app=mysqli_fetch_object($current_app_query);
						$current_app_t[$k]=$lig_app->appreciation;
					}
					else {
						$current_app_t[$k]="";
					}

					if((isset($tab_aid["classe"]["ver_periode"]['all'][$k]))&&
					(($tab_aid["classe"]["ver_periode"]['all'][$k]>=2)||
					(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol')))||
					(($tab_aid["classe"]["ver_periode"]['all'][$k]!=0)&&($_SESSION['statut']=='secours')))) {
						echo "
		<td>
			<textarea name=\"no_anti_inject_app_grp_".$k."\" id=\"app_grp_".$k."\" rows=4 cols=60 wrap='virtual' onchange=\"changement()\">".$current_app_t[$k]."</textarea>";
						$sql="SELECT * FROM matieres_app_corrections WHERE login='' AND id_aid='".$aid_id."' AND periode='".$k."';";
						//echo "$sql<br />";
						$res_mac=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_mac)>0) {
							$lig_mac=mysqli_fetch_object($res_mac);
							echo "<div style='color:red' title=\"Proposition de correction en attente de validation.\nUn compte scolarité ou administrateur doit valider la proposition.\">".nl2br($lig_mac->appreciation)."</div>";
						}
						echo "
		</td>";
						$proposer_liens_enregistrement="y";
					}
					elseif((isset($tab_aid["classe"]["ver_periode"]['all'][$k]))&&
					($tab_aid["classe"]["ver_periode"]['all'][$k]>=1)&&(in_array($_SESSION['login'], $tab_aid['profs']['list']))&&
					(isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&
					($tab_autorisation_exceptionnelle_de_saisie_app[$k]!='n')) {

						if($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='yy') {
							$tmp_title="title=\"Modification autorisée à titre exceptionnel.\"";
						}
						else {
							$tmp_title="title=\"Proposition de modification autorisée.\nUn compte scolarité ou administrateur devra valider la proposition.\"";
						}
						echo "
		<td style='background-color:orange'".$tmp_title.">
			<textarea name=\"no_anti_inject_app_grp_".$k."\" id=\"app_grp_".$k."\" rows=4 cols=60 wrap='virtual' onchange=\"changement()\">".$current_app_t[$k]."</textarea>";
						$sql="SELECT * FROM matieres_app_corrections WHERE login='' AND id_aid='".$aid_id."' AND periode='".$k."';";
						//echo "$sql<br />";
						$res_mac=mysqli_query($mysqli, $sql);
						if(mysqli_num_rows($res_mac)>0) {
							$lig_mac=mysqli_fetch_object($res_mac);
							echo "<div style='color:red' title=\"Proposition de correction en attente de validation.\nUn compte scolarité ou administrateur doit valider la proposition.\">".nl2br($lig_mac->appreciation)."</div>";
						}
						echo "
		</td>";
						$proposer_liens_enregistrement="y";
					}
					else {
						// Période close
						echo "
		<td><b>";
						if ($current_app_t[$k] != '') {
							echo "$current_app_t[$k]";
						} else {
							echo "-";
						}
						echo "</b></td>";
					}
				}
				$k++;
			}
			echo "
	</tr>";


	echo "
</table>\n";

/*
echo "<p>".$_SESSION['login']."</p>";
echo "<pre>";
print_r($tab_aid['profs']['list']);
echo "</pre>";

echo "tab_autorisation_exceptionnelle_de_saisie_app<pre>";
print_r($tab_autorisation_exceptionnelle_de_saisie_app);
echo "</pre>";

echo "tab_autorisation_exceptionnelle_de_saisie_note<pre>";
print_r($tab_autorisation_exceptionnelle_de_saisie_note);
echo "</pre>";
*/
	echo "<table class='boireaus' border=1 cellspacing=2 cellpadding=5>\n";

	$compteur_eleve=0;
	$indice_max_log_eleve=0;
	$num_id=10;
	$num = '1';
	// Initialisation de $num3 pour le cas où il n'y a pas de période ouverte:
	$num3=0;
	while ($num < $nb_periode_max + 1) {
		if ($type_note == 'last') {
			$last_periode_aid = min($num,$display_end);
		}
		$appel_login_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT a.login
									FROM j_eleves_classes cc, j_aid_eleves a, $nom_table c, eleves e
									WHERE (a.id_aid='$aid_id' AND
									cc.login = a.login AND
									a.login = e.login AND
									cc.id_classe = c.id_classe AND
									c.num = $num AND
									a.indice_aid='$indice_aid') ORDER BY e.nom, e.prenom");
		$nombre_lignes = mysqli_num_rows($appel_login_eleves);
		if ($nombre_lignes != '0') {
			echo "<tr>\n";
			echo "<th><b>Nom Prénom</b></th>\n";

			$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM $nom_table WHERE num = '$num' ");
			$id_classe = old_mysql_result($call_data, '0', 'id_classe');
			$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'  ORDER BY num_periode");

			$i = "1";
			while ($i < $num + 1) {
				$nom_periode[$i] = old_mysql_result($periode_query, $i-1, "nom_periode");
				echo "<th><b>$nom_periode[$i]</b></th>\n";
				$i++;
			}
			while ($i < $nb_periode_max + 1) {
				echo "<th>X</th>\n";
				$i++;
			}
			echo "</tr>\n";

			$i = "0";
			$alt=1;
			while($i < $nombre_lignes) {
				$current_eleve_login = old_mysql_result($appel_login_eleves, $i, 'login');
				$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE (login = '$current_eleve_login')");
				$current_eleve_nom = old_mysql_result($appel_donnees_eleves, '0', "nom");
				$current_eleve_prenom = old_mysql_result($appel_donnees_eleves, '0', "prenom");
				$appel_classe_eleve = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, j_eleves_classes cc WHERE (cc.login = '$current_eleve_login' AND cc.id_classe = c.id) ORDER BY cc.periode DESC");
				$current_eleve_classe = old_mysql_result($appel_classe_eleve, '0', "classe");
				$current_eleve_id_classe = old_mysql_result($appel_classe_eleve, '0', "id");

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td>
					$current_eleve_nom $current_eleve_prenom $current_eleve_classe
					<a name='saisie_app_".$current_eleve_login."'></a>
				</td>\n";
				$k = '1';

				$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$current_eleve_id_classe'  ORDER BY num_periode");

				while ($k < $num + 1) {
					if (($k >= $display_begin) and ($k <= $display_end)) {

						$current_eleve_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$k' AND id_aid = '$aid_id' and indice_aid='$indice_aid')");
						$current_eleve_statut_t[$k] = @old_mysql_result($current_eleve_app_query, 0, "statut");
						$current_eleve_app_t[$k] = @old_mysql_result($current_eleve_app_query, 0, "appreciation");
						$current_eleve_note_t[$k] = @old_mysql_result($current_eleve_app_query, 0, "note");
						$current_eleve_login_t[$k] = $current_eleve_login."_t".$k;
						$current_eleve_login_n_t[$k] = $current_eleve_login."_n_t".$k;

						$ver_periode[$k] = old_mysql_result($periode_query, $k-1, "verouiller");
						//if ($ver_periode[$k] != "N") {



						if(($ver_periode[$k]=='P')&&
							(in_array($_SESSION['login'], $tab_aid['profs']['list']))&&
							(((isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_app[$k]!='n'))||
							((isset($tab_autorisation_exceptionnelle_de_saisie_note[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_note[$k]=='y')))) {

							$proposer_liens_enregistrement="y";

							$num2=2*$num_id;
							$num3=$num2+1;

							$tmp_title='';
							$tmp_style='';
							if($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='yy') {
								$tmp_title=" title=\"Modification d'appréciation autorisée à titre exceptionnel.\"";
								$tmp_style=" style='background-color:orange'";
							}
							elseif($tab_autorisation_exceptionnelle_de_saisie_app[$k]=='y') {
								$tmp_title=" title=\"Proposition de modification d'appréciation autorisée.\nUn compte scolarité ou administrateur devra valider la proposition.\"";
								$tmp_style=" style='background-color:orange'";
							}

							echo "<td id=\"td_".$k.$num3."\"".$tmp_style.$tmp_title.">\n";
							echo "<b>";

							echo "<input type='hidden' name='log_eleve_".$k."[$compteur_eleve]' id='log_eleve_".$k.$num2."' value=\"".$current_eleve_login_t[$k]."\" />\n";

							if((isset($tab_autorisation_exceptionnelle_de_saisie_app[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_app[$k]!='n')) {
								echo "<textarea id=\"n".$k.$num2."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$compteur_eleve."\" rows=4 cols=60 wrap='virtual' onchange=\"changement()\" onfocus=\"focus_suivant(".$k.$num2.");document.getElementById('focus_courant').value='".$k.$num2."';repositionner_commtype();\"";
								if(!getSettingANon('active_recherche_lapsus')) {
									echo " onblur=\"ajaxVerifAppAid('".$current_eleve_login_t[$k]."', '".$aid_id."', 'n".$k.$num2."');\"";
								}
								echo ">";
								echo $current_eleve_app_t[$k];
								echo "</textarea>\n";

								echo "<div id='div_verif_n".$k.$num2."' style='color:red;'>";
								if(!getSettingANon('active_recherche_lapsus')) {
									echo teste_lapsus($current_eleve_app_t[$k]);
								}
								echo "</div>";

							}
							else {
								if ($current_eleve_app_t[$k] != '') {
									echo "$current_eleve_app_t[$k]";
								} else {
									echo "-";
								}
							}

							$sql="SELECT * FROM matieres_app_corrections WHERE login='".$current_eleve_login."' AND id_aid='".$aid_id."' AND periode='".$k."';";
							//echo "$sql<br />";
							$res_mac=mysqli_query($mysqli, $sql);
							if(mysqli_num_rows($res_mac)>0) {
								$lig_mac=mysqli_fetch_object($res_mac);
								echo "<div style='color:red' title=\"Proposition de correction en attente de validation.\nUn compte scolarité ou administrateur doit valider la proposition.\">".nl2br($lig_mac->appreciation)."</div>";
							}

							if((isset($tab_autorisation_exceptionnelle_de_saisie_note[$k]))&&($tab_autorisation_exceptionnelle_de_saisie_note[$k]=='y')) {
								if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
									echo "<br />Note (sur $note_max) : ";
									echo "<input id=\"n".$k.$num3."\" onKeyDown=\"clavier(this.id,event);\" type=text size = '4' name=\"note_eleve_".$k."[$compteur_eleve]\" value=";
									//=========================
									if ($current_eleve_statut_t[$k] == 'other') {
										echo "\"\"";
									} else if ($current_eleve_statut_t[$k] != '') {
										echo "\"".$current_eleve_statut_t[$k]."\"";
									} else {
										echo "\"".$current_eleve_note_t[$k]."\"";
									}
									//echo " onchange=\"changement()\" /></td>\n";
									echo " onfocus=\"javascript:this.select()\" onchange=\"verifcol(".$k.$num3.");changement()\" />\n";
								}
							}
							else {
								if ((($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) and ($current_eleve_note_t[$k] !='')) {
									echo "<br />Note (sur $note_max) : ";
									if ($current_eleve_statut_t[$k] == 'other') {
										echo "&nbsp;";
									} else if ($current_eleve_statut_t[$k] != '') {
										echo "$current_eleve_statut_t[$k]";
									} else {
										echo "$current_eleve_note_t[$k]";
									}
								}
							}

							echo "</b></td>\n";

						}
						elseif (
							(($_SESSION['statut']=='secours')&&($ver_periode[$k] == "O"))||
							(($_SESSION['statut']=='scolarite')&&(getSettingAOui('AccesModifAppreciationScol'))&&($ver_periode[$k] == "O"))||
							(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('AccesModifAppreciationScol'))&&($ver_periode[$k] != "N"))||
							(($_SESSION['statut']!='secours')&&($_SESSION['statut']!='scolarite')&&($ver_periode[$k] != "N"))
						) {
							echo "<td><b>";
							if ($current_eleve_app_t[$k] != '') {
								echo "$current_eleve_app_t[$k]";
							} else {
								echo "-";
							}
							if ((($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) and ($current_eleve_note_t[$k] !='')) {
								echo "<br />Note (sur $note_max) : ";
								if ($current_eleve_statut_t[$k] == 'other') {
									echo "&nbsp;";
								} else if ($current_eleve_statut_t[$k] != '') {
									echo "$current_eleve_statut_t[$k]";
								} else {
									echo "$current_eleve_note_t[$k]";
								}
							}
							echo "</b></td>\n";
						} else {
							$proposer_liens_enregistrement="y";

							$num2=2*$num_id;
							$num3=$num2+1;
							//echo "<td>\n";
							//echo "<td id=\"td_".$k.$num3."\" bgcolor=\"$couleur_fond\">\n";

							echo "<td id=\"td_".$k.$num3."\">\n";

							//=========================
							echo "<input type='hidden' name='log_eleve_".$k."[$compteur_eleve]' id='log_eleve_".$k.$num2."' value=\"".$current_eleve_login_t[$k]."\" />\n";
							echo "<textarea id=\"n".$k.$num2."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$compteur_eleve."\" rows=4 cols=60 wrap='virtual' onchange=\"changement()\" onfocus=\"focus_suivant(".$k.$num2.");document.getElementById('focus_courant').value='".$k.$num2."';repositionner_commtype();\"";
							if(!getSettingANon('active_recherche_lapsus')) {
								echo " onblur=\"ajaxVerifAppAid('".$current_eleve_login_t[$k]."', '".$aid_id."', 'n".$k.$num2."');\"";
							}
							echo ">";
							echo $current_eleve_app_t[$k];
							echo "</textarea>\n";

							echo "<div id='div_verif_n".$k.$num2."' style='color:red;'>";
							if(!getSettingANon('active_recherche_lapsus')) {
								echo teste_lapsus($current_eleve_app_t[$k]);
							}
							echo "</div>";

							if (($type_note=='every') or (($type_note=='last') and ($k == $last_periode_aid))) {
								echo "<br />Note (sur $note_max) : ";
								echo "<input id=\"n".$k.$num3."\" onKeyDown=\"clavier(this.id,event);\" type=text size = '4' name=\"note_eleve_".$k."[$compteur_eleve]\" value=";
								//=========================
								if ($current_eleve_statut_t[$k] == 'other') {
									echo "\"\"";
								} else if ($current_eleve_statut_t[$k] != '') {
									echo "\"".$current_eleve_statut_t[$k]."\"";
								} else {
									echo "\"".$current_eleve_note_t[$k]."\"";
								}
								//echo " onchange=\"changement()\" /></td>\n";
								echo " onfocus=\"javascript:this.select()\" onchange=\"verifcol(".$k.$num3.");changement()\" />\n";
							}
							echo "</td>\n";
						}
					} else {
						echo "<td>-</td>\n";
					}
					$k++;
				}

				while ($k < $nb_periode_max + 1) {
					echo "<td>X</td>\n";
					$k++;
				}

				echo "</tr>\n";

				$i++;
				$num_id++;

				$indice_max_log_eleve++;
				$compteur_eleve++;
			}
		}
		$num++;
	}
	?>
	</table>

	<table>
	<tr><td>
	<?php
		//echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
		echo "<input type='hidden' name='indice_max_log_eleve' value='$indice_max_log_eleve' />\n";

		echo add_token_field(true);
	?>
	<input type=hidden name=is_posted value="yes" />
	<input type=hidden name=aid_id value="<?php echo "$aid_id";?>" />
	<input type=hidden name=indice_aid value="<?php echo "$indice_aid";?>" />
	<center>
		<div id="fixe">
			<?php
				if($proposer_liens_enregistrement=='y') {
					if(getSettingAOui('aff_temoin_check_serveur')) {
						temoin_check_srv();
					}
					echo "
					<input type='submit' value='Enregistrer' /><br />

					<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
					<div id='decompte' title=\"La session ne sera plus valide, si vous ne consultez pas une page
					ou ne validez pas ce formulaire avant le nombre de secondes indiqué.\"></div>\n";

					//============================================
					if((($_SESSION["statut"]=="professeur")&&(getSettingAOui('appreciations_types_profs')))||
					($_SESSION["statut"]=="cpe")||
					($_SESSION["statut"]=="scolarite")) {include('ctp.php');}
					//============================================
				}
		?>

			<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
			<input type='hidden' id='info_focus' name='champ_info_focus' value='' />
			<input type='hidden' id='focus_courant' name='focus_courant' value='' />
		</div>
	</center>
	</td></tr>
	</table>
	</form>
	<?php


//=============================================================
// MODIF: boireaus
echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}
	note=document.getElementById('n'+num_id).value;

	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){
		if((note>$note_max)||(note<0)){
			couleur='red';
		}
		else{
			couleur='$couleur_devoirs';
		}
		}
		else{
		couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}

for(i=10;i<".$k.$num3.";i++){
	if(i/2-Math.round(i/2)!=0){
		if(document.getElementById('n'+i)){
			if(document.getElementById('n'+i).value!=''){
				eval(\"verifcol(\"+i+\")\");
			}
		}
	}
}";

if((isset($chaine_test_vocabulaire))&&($chaine_test_vocabulaire!="")) {
	echo $chaine_test_vocabulaire;
}

echo "
// Pour éviter une erreur dans les commentaires-types:
id_groupe='';

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".$compteur_eleve."
	// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
	// (pour réussir à passer un élève qui ne serait plus dans la période)
	// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

function repositionner_commtype() {
	if(document.getElementById('div_commtype')) {
		if(document.getElementById('div_commtype').style.display!='none') {
			x=document.getElementById('div_commtype').style.left;
			afficher_div('div_commtype','y',20,20);
			document.getElementById('div_commtype').style.left=x;
		}
	}
}

</script>
";
//=============================================================


}
require("../lib/footer.inc.php");
?>
