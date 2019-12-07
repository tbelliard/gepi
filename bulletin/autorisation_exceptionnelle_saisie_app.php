<?php

/*
 * $Id$
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

// SQL : INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', '');;";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('PeutDonnerAccesBullAppPeriodeCloseScol'))) {
	$mess=rawurlencode("Accès interdit !");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_aid=isset($_POST['id_aid']) ? $_POST['id_aid'] : (isset($_GET['id_aid']) ? $_GET['id_aid'] : NULL);
$periode=isset($_POST['periode']) ? $_POST['periode'] : (isset($_GET['periode']) ? $_GET['periode'] : NULL);
$enseignement_periode=isset($_POST['enseignement_periode']) ? $_POST['enseignement_periode'] : (isset($_GET['enseignement_periode']) ? $_GET['enseignement_periode'] : NULL);
$aid_periode=isset($_POST['aid_periode']) ? $_POST['aid_periode'] : (isset($_GET['aid_periode']) ? $_GET['aid_periode'] : NULL);

$is_posted=isset($_POST['is_posted']) ? $_POST['is_posted'] : (isset($_GET['is_posted']) ? $_GET['is_posted'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$display_date_limite=isset($_POST['display_date_limite']) ? $_POST['display_date_limite'] : (isset($_GET['display_date_limite']) ? $_GET['display_date_limite'] : NULL);
$display_heure_limite=isset($_POST['display_heure_limite']) ? $_POST['display_heure_limite'] : (isset($_GET['display_heure_limite']) ? $_GET['display_heure_limite'] : NULL);

// Pour refermer la page plutôt que proposer un lien retour dans certains cas
$refermer_page=isset($_POST['refermer_page']) ? $_POST['refermer_page'] : (isset($_GET['refermer_page']) ? $_GET['refermer_page'] : NULL);


$msg="";

if((isset($mode))&&(!in_array($mode, array('proposition', 'acces_complet')))) {
	$msg.="Mode invalide.<br />";
	unset($mode);
}

//debug_var();

if((isset($is_posted))&&(isset($_POST['no_anti_inject_message_autorisation_exceptionnelle']))&&($_SESSION['statut']=='administrateur')) {
	check_token();
	//echo "BLIP";
	if (isset($NON_PROTECT["message_autorisation_exceptionnelle"])){
		$message_autorisation_exceptionnelle= traitement_magic_quotes(corriger_caracteres($NON_PROTECT["message_autorisation_exceptionnelle"]));
	}
	else{
		$message_autorisation_exceptionnelle="";
	}

	// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
	//$message_autorisation_exceptionnelle=my_ereg_replace('(\\\r\\\n)+',"\r\n",$message_autorisation_exceptionnelle);
	$message_autorisation_exceptionnelle=suppression_sauts_de_lignes_surnumeraires($message_autorisation_exceptionnelle);

	if(!saveSetting('message_autorisation_exceptionnelle',$message_autorisation_exceptionnelle)) {
		$msg="Erreur lors de l'enregistrement du message personnalisé.<br />";
	}
	else {
		$msg="Enregistrement du message personnalisé effectué.<br />";
	}
}

if((isset($is_posted))&&(isset($id_classe))&&(isset($id_groupe))&&(isset($periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	check_token();
	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_limite'])) {
		$annee = mb_substr($_POST['display_date_limite'],6,4);
		$mois = mb_substr($_POST['display_date_limite'],3,2);
		$jour = mb_substr($_POST['display_date_limite'],0,2);
		//echo "$jourd/$moisd/$anneed<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_limite))) {
				//$heure = mb_substr($_POST['display_heure_limite'],0,2);
				//$minute = mb_substr($_POST['display_heure_limite'],3,2);
				$tmp_tab=explode(':', $display_heure_limite);
				$heure = $tmp_tab[0];
				$minute = $tmp_tab[1];

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					$info_current_group=get_info_grp($id_groupe);

					$sql="DELETE FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$periode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);

					$date_limite_email="$annee/$mois/$jour à $heure:$minute";
					$sql="INSERT INTO matieres_app_delais SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00', mode='$mode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$msg.="ERREUR lors de l'insertion de l'enregistrement pour ".$info_current_group.".<br />";
					}
					else {
						$msg.="Enregistrement de l'autorisation effectué pour ".$info_current_group.".<br />";

						$_SESSION['autorisation_saisie_date_limite']=mktime($heure, $minute, 0, $mois, $jour, $annee);

						$complement_texte_mail="";
						if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
							if((isset($_POST['donner_acces_modif_bull_note']))&&($_POST['donner_acces_modif_bull_note']=='y')) {
								$sql="DELETE FROM acces_exceptionnel_matieres_notes WHERE id_groupe='$id_groupe' AND periode='$periode';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								$sql="INSERT INTO acces_exceptionnel_matieres_notes SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res) {
									$msg.="ERREUR lors de l'insertion de l'enregistrement pour les notes des bulletins pour ".$info_current_group.".<br />";
								}
								else {
									$msg.="Enregistrement de l'autorisation pour les notes des bulletins effectué pour ".$info_current_group.".<br />";
									$complement_texte_mail="Vous pourrez aussi corriger les moyennes du bulletin.\n\n";
								}
							}
						}

						$envoi_mail_actif=getSettingValue('envoi_mail_actif');
						if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
							$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
						}
		
						if($envoi_mail_actif=='y') {
							$email_personne_autorisant="";
							$nom_personne_autorisant="";
							$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
								$email_personne_autorisant=$lig_u->email;
								$tab_param_mail['cc'][]=$email_personne_autorisant;
								$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
								$tab_param_mail['replyto']=$email_personne_autorisant;
								$tab_param_mail['replyto_name']=$nom_personne_autorisant;
							}
	
							$email_destinataires="";
							$designation_destinataires="";
							// Recherche des profs du groupe
							$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.email!='';";
							//echo "$sql<br />";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								if((check_mail($lig_u->email))&&
								((!isset($tab_param_mail['destinataire']))||(!in_array($lig_u->email, $tab_param_mail['destinataire'])))) {
									$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
									$designation_destinataires.=$designation_destinataire_courant;
									$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";

									$tab_param_mail['destinataire'][]=$lig_u->email;
									$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;

									while($lig_u=mysqli_fetch_object($req)) {
										$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
										$designation_destinataires.=", ".$designation_destinataire_courant;
										// Il se passe un truc bizarre avec les suivants
										//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
										$email_destinataires.=", ".$lig_u->email;

										$tab_param_mail['destinataire'][]=$lig_u->email;
										$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
									}

									$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
			
									//$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
									//if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
				
									$ajout_header="";
									if($email_personne_autorisant!="") {
										$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
										$ajout_header.="\r\n";
										$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
									}

									$tab_champs=array('classes');
									$current_group=get_group($id_groupe,$tab_champs);

									//$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\nCette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";

									$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\n";
									$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

									if($message_autorisation_exceptionnelle=='') {
										$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
									}
									else {
										$texte_mail.=$message_autorisation_exceptionnelle."\n";
									}

									$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
									$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

									$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

									if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)." pour ".$info_current_group."<br />";}
								}
							}
		
						}
					}
				}
			}
			else {
				$msg = "ATTENTION : L'heure limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
			}
		}
	}
	else {
		$msg = "ATTENTION : La date limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
	}
}

if((isset($is_posted))&&(isset($id_classe))&&(isset($id_aid))&&(isset($periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	check_token();
	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_limite'])) {
		$annee = mb_substr($_POST['display_date_limite'],6,4);
		$mois = mb_substr($_POST['display_date_limite'],3,2);
		$jour = mb_substr($_POST['display_date_limite'],0,2);
		//echo "$jourd/$moisd/$anneed<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_limite))) {
				//$heure = mb_substr($_POST['display_heure_limite'],0,2);
				//$minute = mb_substr($_POST['display_heure_limite'],3,2);
				$tmp_tab=explode(':', $display_heure_limite);
				$heure = $tmp_tab[0];
				$minute = $tmp_tab[1];

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					$info_current_aid=get_info_aid($id_aid);

					$sql="DELETE FROM matieres_app_delais WHERE id_aid='$id_aid' AND periode='$periode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);

					$date_limite_email="$annee/$mois/$jour à $heure:$minute";
					$sql="INSERT INTO matieres_app_delais SET id_aid='$id_aid', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00', mode='$mode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$res) {
						$msg.="ERREUR lors de l'insertion de l'enregistrement pour ".$info_current_aid.".<br />";
					}
					else {
						$msg.="Enregistrement de l'autorisation effectué pour ".$info_current_aid.".<br />";

						$_SESSION['autorisation_saisie_date_limite']=mktime($heure, $minute, 0, $mois, $jour, $annee);

						$complement_texte_mail="";
						if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
							if((isset($_POST['donner_acces_modif_bull_note']))&&($_POST['donner_acces_modif_bull_note']=='y')) {
								$sql="DELETE FROM acces_exceptionnel_matieres_notes WHERE id_aid='$id_aid' AND periode='$periode';";
								$menage=mysqli_query($GLOBALS["mysqli"], $sql);
								$sql="INSERT INTO acces_exceptionnel_matieres_notes SET id_aid='$id_aid', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
								$res=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$res) {
									$msg.="ERREUR lors de l'insertion de l'enregistrement pour les notes des bulletins pour ".$info_current_aid.".<br />";
								}
								else {
									$msg.="Enregistrement de l'autorisation pour les notes des bulletins effectué pour ".$info_current_aid.".<br />";
									$complement_texte_mail="Vous pourrez aussi corriger les moyennes du bulletin.\n\n";
								}
							}
						}

						$envoi_mail_actif=getSettingValue('envoi_mail_actif');
						if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
							$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
						}
		
						if($envoi_mail_actif=='y') {
							$email_personne_autorisant="";
							$nom_personne_autorisant="";
							$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
								$email_personne_autorisant=$lig_u->email;
								$tab_param_mail['cc'][]=$email_personne_autorisant;
								$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
								$tab_param_mail['replyto']=$email_personne_autorisant;
								$tab_param_mail['replyto_name']=$nom_personne_autorisant;
							}
	
							$email_destinataires="";
							$designation_destinataires="";
							unset($tab_param_mail['destinataire']);
							// Recherche des profs de l'AID
							$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs jau WHERE jau.id_aid='$id_aid' AND jau.id_utilisateur=u.login AND u.email!='';";
							//echo "$sql<br />";
							$req=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($req)>0) {
								$lig_u=mysqli_fetch_object($req);
								if((check_mail($lig_u->email))&&
								((!isset($tab_param_mail['destinataire']))||(!in_array($lig_u->email, $tab_param_mail['destinataire'])))) {
									$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
									$designation_destinataires.=$designation_destinataire_courant;
									$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";

									$tab_param_mail['destinataire'][]=$lig_u->email;
									$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;

									while($lig_u=mysqli_fetch_object($req)) {
										$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
										$designation_destinataires.=", ".$designation_destinataire_courant;
										// Il se passe un truc bizarre avec les suivants
										//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
										$email_destinataires.=", ".$lig_u->email;

										$tab_param_mail['destinataire'][]=$lig_u->email;
										$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
									}

									$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
				
									$ajout_header="";
									if($email_personne_autorisant!="") {
										$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
										$ajout_header.="\r\n";
										$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
									}

									$tab_champs=array('classes');
									$current_aid=get_tab_aid($id_aid, '', $tab_champs);

									$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_aid['nom']." (".$current_aid['nom_complet']." en ".$current_aid['classlist_string'].") en période $periode.\n\n";
									$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

									if($message_autorisation_exceptionnelle=='') {
										$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
									}
									else {
										$texte_mail.=$message_autorisation_exceptionnelle."\n";
									}

									$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
									$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

									$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

									if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)." pour ".$info_current_aid."<br />";}
								}
							}
		
						}
					}
				}
			}
			else {
				$msg = "ATTENTION : L'heure limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
			}
		}
	}
	else {
		$msg = "ATTENTION : La date limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
	}
}

if((isset($is_posted))&&(isset($id_classe))&&(preg_match('/^[0-9]{1,}$/', $id_classe))&&(isset($enseignement_periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	check_token();
	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_limite'])) {
		$annee = mb_substr($_POST['display_date_limite'],6,4);
		$mois = mb_substr($_POST['display_date_limite'],3,2);
		$jour = mb_substr($_POST['display_date_limite'],0,2);
		//echo "$jour/$mois/$annee<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_limite))) {
				//$heure = mb_substr($_POST['display_heure_limite'],0,2);
				//$minute = mb_substr($_POST['display_heure_limite'],3,2);
				$tmp_tab=explode(':', $display_heure_limite);
				$heure = $tmp_tab[0];
				$minute = $tmp_tab[1];
				//echo "heure=$heure et minute=$minute";

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					//echo "mktime($heure, $minute, 0, $mois, $jour, $annee)<br />";
					$_SESSION['autorisation_saisie_date_limite']=mktime($heure, $minute, 0, $mois, $jour, $annee);

					for($loop=0;$loop<count($enseignement_periode);$loop++) {

						if(isset($tab_param_mail)) {
							unset($tab_param_mail);
						}

						$tab_ens_per=explode('|', $enseignement_periode[$loop]);
						if((isset($tab_ens_per[1]))&&($tab_ens_per[0]=='viescolaire')&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {

							$periode=$tab_ens_per[1];
							$mode='acces_complet';

							$sql="DELETE FROM abs_bull_delais WHERE id_classe='$id_classe' AND periode='$periode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);

							$date_limite_email="$annee/$mois/$jour à $heure:$minute";
							$sql="INSERT INTO abs_bull_delais SET id_classe='$id_classe', periode='$periode', appreciation='y', ";

							$complement_texte_mail="";
							if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
								if((getSettingAOui('abs2_import_manuel_bulletin'))&&(isset($_POST['donner_acces_modif_totaux_abs']))&&($_POST['donner_acces_modif_totaux_abs']=='y')) {
									// Dans le cas où il y a une autorisation de modif de totaux et qu'on fait ensuite une autorisation sans modif de totaux, l'autorisation sur les totaux est perdue.
									// La situation ne devrait se produire que si plusieurs personnes donnent des droits.
									$sql.="totaux='y', ";
									$complement_texte_mail="Vous pourrez aussi corriger les totaux d'absences sur les bulletins.\n\n";
								}
							}

							$sql.="date_limite='$annee-$mois-$jour $heure:$minute:00', mode='$mode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res) {
								$msg.="ERREUR lors de l'insertion de l'enregistrement Vie scolaire.<br />";
							}
							else {
								$msg.="Enregistrement de l'autorisation Vie scolaire effectué.<br />";

								$envoi_mail_actif=getSettingValue('envoi_mail_actif');
								if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
									$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
								}
		
								if($envoi_mail_actif=='y') {
									$email_personne_autorisant="";
									$nom_personne_autorisant="";
									$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
										$email_personne_autorisant=$lig_u->email;
										$tab_param_mail['cc'][]=$email_personne_autorisant;
										$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
										$tab_param_mail['replyto']=$email_personne_autorisant;
										$tab_param_mail['replyto_name']=$nom_personne_autorisant;
									}
	
									$email_destinataires="";
									$designation_destinataires="";
									// Recherche des CPE de la classe

									$liste_cpe='';
									$sql="SELECT DISTINCT u.login, u.email, u.nom, u.prenom, civilite FROM j_eleves_cpe jecpe, 
																		j_eleves_classes jec, 
																		utilisateurs u 
																	WHERE jecpe.e_login=jec.login AND 
																		jecpe.cpe_login=u.login AND 
																		jec.id_classe='".$id_classe."'
																	ORDER BY u.nom,u.prenom;";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										if((check_mail($lig_u->email))&&
										((!isset($tab_param_mail['destinataire']))||(!in_array($lig_u->email, $tab_param_mail['destinataire'])))) {
											$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
											$designation_destinataires.=$designation_destinataire_courant;
											$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";

											$tab_param_mail['destinataire'][]=$lig_u->email;
											$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;

											while($lig_u=mysqli_fetch_object($req)) {
												$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
												$designation_destinataires.=", ".$designation_destinataire_courant;
												// Il se passe un truc bizarre avec les suivants
												//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
												$email_destinataires.=", ".$lig_u->email;

												$tab_param_mail['destinataire'][]=$lig_u->email;
												$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
											}

											$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
			
											//$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
											//if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
				
											$ajout_header="";
											if($email_personne_autorisant!="") {
												$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
												$ajout_header.="\r\n";
												$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
											}

											$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations de Vie Scolaire pour la classe de ".get_nom_classe($id_classe)." en période $periode.\n\n";
											$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

											if($message_autorisation_exceptionnelle=='') {
												$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
											}
											else {
												$texte_mail.=$message_autorisation_exceptionnelle."\n";
											}

											$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
											$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

											$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

											if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)."<br />";}
										}
									}
		
								}
								unset($id_groupe);
								unset($periode);
							}

						}
						elseif((isset($tab_ens_per[1]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[0]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {
							$id_groupe=$tab_ens_per[0];
							$periode=$tab_ens_per[1];

							$info_current_group=get_info_grp($id_groupe);

							$sql="DELETE FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$periode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);

							$date_limite_email="$annee/$mois/$jour à $heure:$minute";
							$sql="INSERT INTO matieres_app_delais SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00', mode='$mode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res) {
								$msg.="ERREUR lors de l'insertion de l'enregistrement pour ".$info_current_group.".<br />";
							}
							else {
								$msg.="Enregistrement de l'autorisation effectué pour ".$info_current_group.".<br />";

								$complement_texte_mail="";
								if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
									if((isset($_POST['donner_acces_modif_bull_note']))&&($_POST['donner_acces_modif_bull_note']=='y')) {
										$sql="DELETE FROM acces_exceptionnel_matieres_notes WHERE id_groupe='$id_groupe' AND periode='$periode';";
										//echo "$sql<br />";
										$menage=mysqli_query($GLOBALS["mysqli"], $sql);
										$sql="INSERT INTO acces_exceptionnel_matieres_notes SET id_groupe='$id_groupe', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
										//echo "$sql<br />";
										$res=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$res) {
											$msg.="ERREUR lors de l'insertion de l'enregistrement pour les notes des bulletins pour ".$info_current_group.".<br />";
										}
										else {
											$msg.="Enregistrement de l'autorisation pour les notes des bulletins pour ".$info_current_group." effectué.<br />";
											$complement_texte_mail="Vous pourrez aussi corriger les moyennes du bulletin.\n\n";
										}
									}
								}

								$envoi_mail_actif=getSettingValue('envoi_mail_actif');
								if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
									$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
								}
		
								if($envoi_mail_actif=='y') {
									$email_personne_autorisant="";
									$nom_personne_autorisant="";
									$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
										$email_personne_autorisant=$lig_u->email;
										$tab_param_mail['cc'][]=$email_personne_autorisant;
										$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
										$tab_param_mail['replyto']=$email_personne_autorisant;
										$tab_param_mail['replyto_name']=$nom_personne_autorisant;
									}
	
									$email_destinataires="";
									$designation_destinataires="";
									// Recherche des profs du groupe
									$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$id_groupe' AND jgp.login=u.login AND u.email!='';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										if((check_mail($lig_u->email))&&
										((!isset($tab_param_mail['destinataire']))||(!in_array($lig_u->email, $tab_param_mail['destinataire'])))) {
											$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
											$designation_destinataires.=$designation_destinataire_courant;
											$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";

											$tab_param_mail['destinataire'][]=$lig_u->email;
											$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;

											while($lig_u=mysqli_fetch_object($req)) {
												$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
												$designation_destinataires.=", ".$designation_destinataire_courant;
												// Il se passe un truc bizarre avec les suivants
												//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
												$email_destinataires.=", ".$lig_u->email;

												$tab_param_mail['destinataire'][]=$lig_u->email;
												$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
											}

											$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
			
											//$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
											//if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
				
											$ajout_header="";
											if($email_personne_autorisant!="") {
												$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
												$ajout_header.="\r\n";
												$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
											}

											$tab_champs=array('classes');
											$current_group=get_group($id_groupe,$tab_champs);

											//$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\nCette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";

											$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." (".$current_group['description']." en ".$current_group['classlist_string'].") en période $periode.\n\n";
											$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

											if($message_autorisation_exceptionnelle=='') {
												$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
											}
											else {
												$texte_mail.=$message_autorisation_exceptionnelle."\n";
											}

											$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
											$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

											$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

											if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)." pour ".$info_current_group."<br />";}
										}
									}
		
								}
								unset($id_groupe);
								unset($periode);
							}
						}
						else {
							$msg.="Couple id_groupe/période non valide&nbsp;: ".$enseignement_periode[$loop]."<br />";
						}
					}
				}
			}
			else {
				$msg = "ATTENTION : L'heure limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
			}
		}
	}
	else {
		$msg = "ATTENTION : La date limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
	}
}


if((isset($is_posted))&&(isset($id_classe))&&(preg_match('/^[0-9]{1,}$/', $id_classe))&&(isset($aid_periode))&&(isset($display_date_limite))&&(isset($display_heure_limite))) {
	check_token();
	if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date_limite'])) {
		$annee = mb_substr($_POST['display_date_limite'],6,4);
		$mois = mb_substr($_POST['display_date_limite'],3,2);
		$jour = mb_substr($_POST['display_date_limite'],0,2);
		//echo "$jour/$mois/$annee<br />";

		if(!checkdate($mois, $jour, $annee)) {
			$msg.="ERREUR : La date $jour/$mois/$annee n'est pas valide.<br />";
		}
		else {
			if (preg_match("/([0-9]{1,2}):([0-9]{0,2})/", str_ireplace('h',':',$display_heure_limite))) {
				//$heure = mb_substr($_POST['display_heure_limite'],0,2);
				//$minute = mb_substr($_POST['display_heure_limite'],3,2);
				$tmp_tab=explode(':', $display_heure_limite);
				$heure = $tmp_tab[0];
				$minute = $tmp_tab[1];
				//echo "heure=$heure et minute=$minute";

				if(($heure>23)||($heure<0)||($minute<0)||($minute>59)) {
					$msg.="ERREUR : L'heure $heure/$minute n'est pas valide.<br />";
				}
				else {
					//echo "mktime($heure, $minute, 0, $mois, $jour, $annee)<br />";
					$_SESSION['autorisation_saisie_date_limite']=mktime($heure, $minute, 0, $mois, $jour, $annee);

					for($loop=0;$loop<count($aid_periode);$loop++) {
						$tab_ens_per=explode('|', $aid_periode[$loop]);
						if((isset($tab_ens_per[1]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[0]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {

							if(isset($tab_param_mail)) {
								unset($tab_param_mail);
							}

							$id_aid=$tab_ens_per[0];
							$periode=$tab_ens_per[1];

							$info_current_aid=get_info_aid($id_aid);

							$sql="DELETE FROM matieres_app_delais WHERE id_aid='$id_aid' AND periode='$periode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);

							$date_limite_email="$annee/$mois/$jour à $heure:$minute";
							$sql="INSERT INTO matieres_app_delais SET id_aid='$id_aid', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00', mode='$mode';";
							//echo "$sql<br />";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$res) {
								$msg.="ERREUR lors de l'insertion de l'enregistrement pour ".$info_current_aid.".<br />";
							}
							else {
								$msg.="Enregistrement de l'autorisation ".$info_current_aid." effectué.<br />";

								$complement_texte_mail="";
								if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
									if((isset($_POST['donner_acces_modif_bull_note']))&&($_POST['donner_acces_modif_bull_note']=='y')) {
										$sql="DELETE FROM acces_exceptionnel_matieres_notes WHERE id_aid='$id_aid' AND periode='$periode';";
										//echo "$sql<br />";
										$menage=mysqli_query($GLOBALS["mysqli"], $sql);
										$sql="INSERT INTO acces_exceptionnel_matieres_notes SET id_aid='$id_aid', periode='$periode', date_limite='$annee-$mois-$jour $heure:$minute:00';";
										//echo "$sql<br />";
										$res=mysqli_query($GLOBALS["mysqli"], $sql);
										if(!$res) {
											$msg.="ERREUR lors de l'insertion de l'enregistrement pour les notes des bulletins pour ".$info_current_aid.".<br />";
										}
										else {
											$msg.="Enregistrement de l'autorisation pour les notes des bulletins pour ".$info_current_aid." effectué.<br />";
											// A vérifier: A-t-on ici à coup sûr un AID avec note?
											$complement_texte_mail="Vous pourrez aussi corriger les moyennes du bulletin.\n\n";
										}
									}
								}

								$envoi_mail_actif=getSettingValue('envoi_mail_actif');
								if(($envoi_mail_actif!='n')&&($envoi_mail_actif!='y')) {
									$envoi_mail_actif='y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
								}
		
								if($envoi_mail_actif=='y') {
									$email_personne_autorisant="";
									$nom_personne_autorisant="";
									$sql="select nom, prenom, civilite, email from utilisateurs where login = '".$_SESSION['login']."';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										$nom_personne_autorisant=$lig_u->civilite." ".casse_mot($lig_u->nom,'maj')." ".casse_mot($lig_u->prenom,'majf');
										$email_personne_autorisant=$lig_u->email;
										$tab_param_mail['cc'][]=$email_personne_autorisant;
										$tab_param_mail['cc_name'][]=$nom_personne_autorisant;
										$tab_param_mail['replyto']=$email_personne_autorisant;
										$tab_param_mail['replyto_name']=$nom_personne_autorisant;
									}
	
									$email_destinataires="";
									$designation_destinataires="";
									// Recherche des profs de l'AID
									$sql="SELECT DISTINCT u.email, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs jau WHERE jau.id_aid='$id_aid' AND jau.id_utilisateur=u.login AND u.email!='';";
									//echo "$sql<br />";
									$req=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($req)>0) {
										$lig_u=mysqli_fetch_object($req);
										if((check_mail($lig_u->email))&&
										((!isset($tab_param_mail['destinataire']))||(!in_array($lig_u->email, $tab_param_mail['destinataire'])))) {
											$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
											$designation_destinataires.=$designation_destinataire_courant;
											$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";

											$tab_param_mail['destinataire'][]=$lig_u->email;
											$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;

											while($lig_u=mysqli_fetch_object($req)) {
												$designation_destinataire_courant=remplace_accents($lig_u->civilite." ".$lig_u->nom." ".casse_mot($lig_u->prenom,'majf2'),'all_nospace');
												$designation_destinataires.=", ".$designation_destinataire_courant;
												// Il se passe un truc bizarre avec les suivants
												//$email_destinataires.=$designation_destinataires." <".$lig_u->email.">";
												$email_destinataires.=", ".$lig_u->email;

												$tab_param_mail['destinataire'][]=$lig_u->email;
												$tab_param_mail['destinataire_name'][]=$designation_destinataire_courant;
											}

											$sujet_mail="[GEPI] Autorisation exceptionnelle de saisie/correction d'appréciation";
			
											//$gepiPrefixeSujetMail=getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
											//if($gepiPrefixeSujetMail!='') {$gepiPrefixeSujetMail.=" ";}
				
											$ajout_header="";
											if($email_personne_autorisant!="") {
												$ajout_header.="Cc: $nom_personne_autorisant <".$email_personne_autorisant.">";
												$ajout_header.="\r\n";
												$ajout_header.="Reply-to: $nom_personne_autorisant <".$email_personne_autorisant.">\r\n";
											}

											$tab_champs=array('classes');
											$current_aid=get_tab_aid($id_aid, '', $tab_champs);

											$texte_mail="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'AID ".$current_aid['nom']." (".$current_aid['nom_complet']." en ".$current_aid['classlist_string'].") en période $periode.\n\n";
											$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

											if($message_autorisation_exceptionnelle=='') {
												$texte_mail.="Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";
											}
											else {
												$texte_mail.=$message_autorisation_exceptionnelle."\n";
											}

											$salutation=(date("H")>=18 OR date("H")<=5) ? "Bonsoir" : "Bonjour";
											$texte_mail=$salutation." ".$designation_destinataires.",\n\n".$texte_mail."\nCordialement.\n-- \n".$nom_personne_autorisant;

											$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);

											if($envoi) {$msg.="Email expédié à ".htmlspecialchars($email_destinataires)." pour ".$info_current_aid."<br />";}
										}
									}
		
								}
								unset($id_aid);
								unset($periode);
							}
						}
						else {
							$msg.="Couple id_aid/période non valide&nbsp;: ".$aid_periode[$loop]."<br />";
						}
					}
				}
			}
			else {
				$msg = "ATTENTION : L'heure limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
			}
		}
	}
	else {
		$msg = "ATTENTION : La date limite n'est pas valide.<br />L'enregistrement ne peut avoir lieu.<br />";
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page = "Autorisation exceptionnelle de saisie d'appréciations";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var();
echo "<p class='bold'>\n";

if($refermer_page=='y') {
	echo "<a href='../accueil.php' onClick='self.close();return false;'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Refermer la page </a>\n";
}
else {
	echo "<a href=\"../accueil.php\" ><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>\n";
}

if(($_SESSION['statut']=='administrateur')&&(isset($_GET['definir_message']))) {
	echo " | <a href=\"".$_SERVER['PHP_SELF']."\" > Autorisation exceptionnelle</a>";
	echo "</p>\n";

	echo "<h2>Autoriser la modification d'appréciations des bulletins</h2>";

	echo "<p>Par défaut le message reçu par un professeur exceptionnellement autorisé à saisir en retard ou corriger ses notes/appréciations est le suivant&nbsp;:<br />\n";

	$texte_mail="Bonjour/Bonsoir\n\nVous avez jusqu'au TELLE DATE TELLE HEURE\npour saisir/corriger une ou des appréciations pour l'enseignement XXXXXXXXXX\nen TELLE(S) CLASSE(S) en période NUMERO_PERIODE.\n\n";
	$texte_mail.="<b>Cette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.</b>\n";
	$texte_mail.="\nCordialement.";

	echo "<pre style='color:blue;'>".$texte_mail."</pre>\n";

	echo "<p>Ce message peut être partiellement personnalisé.<br />Vous pouvez intervenir sur la partie en gras du message.</p>\n";

	$message_autorisation_exceptionnelle=getSettingValue('message_autorisation_exceptionnelle');

	if($message_autorisation_exceptionnelle!='') {
		echo "<p>Votre message est actuellement personnalisé de la façon suivante&nbsp;:";
		$texte_mail="Bonjour/Bonsoir\n\nVous avez jusqu'au TELLE DATE TELLE HEURE\npour saisir/corriger une ou des appréciations pour l'enseignement XXXXXXXXXX\nen TELLE(S) CLASSE(S) en période NUMERO_PERIODE.\n\n";
		$texte_mail.="<b>$message_autorisation_exceptionnelle</b>\n";
		$texte_mail.="\nCordialement.";
	
		echo "<pre style='color:green;'>".$texte_mail."</pre>\n";
	}
	else {
		$texte_mail.=$message_autorisation_exceptionnelle."\n";
	}

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
	echo "<p><b>Message personnalisé&nbsp;:</b><br />\n";
	echo "<textarea name='no_anti_inject_message_autorisation_exceptionnelle' rows='2' cols='100'>$message_autorisation_exceptionnelle</textarea>\n";
	echo "<br />\n";
	echo add_token_field();
	echo "<input type='hidden' name='is_posted' value='1' />\n";
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

if(!isset($id_classe)) {
	if($_SESSION['statut']=='administrateur') {
		echo " | <a href=\"".$_SERVER['PHP_SELF']."?definir_message=y\" > Définir le message</a>";
	}
	echo " | <a href='autorisation_exceptionnelle_saisie_note.php'>Autoriser la modification de moyennes des bulletins</a>\n";
	echo "</p>\n";

	echo "<h2>Autoriser la modification d'appréciations des bulletins</h2>";
	//echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";

	// On adapte la liste des classes selon le visiteur
	if($_SESSION['statut']=='scolarite') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	elseif($_SESSION['statut']=='administrateur') {
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}
	else {
		// On ne doit pas arriver là
		echo "<p style='color:red;'>Statut non autorisé.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)==0) {
		echo "<p style='color:red'>Aucune classe n'a été trouvée.";
		if($_SESSION['statut']=='scolarite') {
			echo "<br />Vous n'êtes pas désigné dans Gepi pour suivre une classe.<br />Un administrateur doit vous associer des classes dans <strong>Gestion des bases/Gestion des classes/Paramétrage scolarité</strong>.";
		}
		echo "</p>\n";
	}
	else {
		echo "<p>Choisir une classe&nbsp;:</p>\n";

		$tab_conseils_de_classe=get_tab_date_prochain_evenement_telle_classe("", 'conseil_de_classe');

		$tab_txt=array();
		$tab_lien=array();

		while($lig_clas=mysqli_fetch_object($res_clas)) {
			if(isset($tab_conseils_de_classe[$lig_clas->id])) {

				$lieu_conseil_de_classe="";
				if(isset($tab_conseils_de_classe[$lig_clas->id]['lieu']['designation_complete'])) {
					$lieu_conseil_de_classe=" (".$tab_conseils_de_classe[$lig_clas->id]['lieu']['designation_complete'].")";
				}

				$chaine_tmp=" <span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$tab_conseils_de_classe[$lig_clas->id]['classe']." : ".$tab_conseils_de_classe[$lig_clas->id]['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$tab_conseils_de_classe[$lig_clas->id]['slashdate_ev'].")</span>";

				$tab_txt[]=$lig_clas->classe.$chaine_tmp;
			}
			else {
				$tab_txt[]=$lig_clas->classe;
			}

			if(isset($id_incident)) {
				//$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."&amp;id_incident=$id_incident";
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id."";
			}
			else {
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
			}
		}

		echo "<blockquote>\n";
		tab_liste($tab_txt,$tab_lien,4);
		echo "</blockquote>\n";
	}

	echo "
<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em> <strong>La présente page ne présente d'intérêt qu'en période partiellement close</strong>.<br />
Voici pourquoi&nbsp;:<br />
En <strong>période ouverte en saisie</strong>, les professeurs peuvent saisir/modifier leurs notes et appréciations sans qu'aucune limitation ne se présente.<br />
En <strong>période close</strong>, aucune modification <em>(note, appréciation, avis du conseil de classe,...)</em> n'est plus possible.<br />
En <strong>période partiellement close</strong>, seuls les avis du conseil de classe peuvent être modifiés.<br />
C'est le moment où, à la lueur des saisies de notes et appréciations, le professeur principal <em>(ou le chef d'établissement selon les cas)</em> rédige les avis du conseil de classe.<br />
A cette phase avant le conseil de classe, il arrive qu'un professeur ait manqué de temps pour faire des saisies.<br />
Il est possible, via la présente page, de lui donner un accès exceptionnel à la saisie d'appréciations, sans pour autant rouvrir la saisie à tous les professeurs.<br />
Les modifications peuvent de plus être soit acceptées sans autre formalité, soit seulement proposées par le professeur et validées par un compte scolarité ou administrateur <em>(selon les Droits définis dans <strong>Gestion des droits</strong>)</em>.</p>";
}
elseif(
	(
		((!isset($id_groupe))&&(!isset($id_aid)))||
		(!isset($periode))
	)&&
	(!isset($enseignement_periode))&&
	(!isset($aid_periode))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='autorisation_exceptionnelle_saisie_note.php?id_classe=$id_classe'>Autoriser la modification de moyennes des bulletins</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	$classe=get_nom_classe($id_classe);

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		<h2>Autoriser la modification d'appréciations des bulletins en ".$classe."</h2>

		<p>Pour quel enseignement souhaitez-vous autoriser un enseignant à proposer des saisies/corrections d'appréciations?</p>

		<div id='fixe'><input type='submit' value='Valider' /></div>\n";

	$get_groups_for_class_avec_visibilite="y";
	$groups=get_groups_for_class($id_classe,"","n");

	include("../lib/periodes.inc.php");

	$date_courante=time();

	echo "<table class='boireaus boireaus_alt' summary='Tableau des enseignements et périodes'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>Enseignements</th>\n";
	echo "<th rowspan='2'>Classe(s)</th>\n";
	echo "<th rowspan='2'>Enseignants</th>\n";
	echo "<th colspan='$nb_periode'>Périodes</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	for($i=1;$i<$nb_periode;$i++) {
		if($ver_periode[$i]=='P') {
			echo "
		<th>
			<a href='javascript:coche_per($i,true)' title=\"Tout cocher pour la période $i\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
			<a href='javascript:coche_per($i,false)' title=\"Tout décocher pour la période $i\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
		</th>\n";
		}
		else {
			echo "
		<th></th>";
		}
	}
	echo "</tr>\n";
	foreach($groups as $current_group) {
		if((!isset($current_group["visibilite"]["bulletins"]))||($current_group["visibilite"]["bulletins"]=="y")) {
			echo "<tr class='white_hover'>\n";
			echo "<td style='text-align:left;'>".$current_group['name']." (<span style='font-size:xx-small;'>".$current_group['description']."</span>)</td>\n";

			echo "<td>".$current_group["classlist_string"]."</td>\n";

			echo "<td>\n";
			$sql="SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_groupes_professeurs j WHERE (u.login = j.login and j.id_groupe = '" . $current_group['id'] . "') ORDER BY u.nom, u.prenom";
			$get_profs=mysqli_query($GLOBALS["mysqli"], $sql);

			$nb = mysqli_num_rows($get_profs);
			$i=0;
			while($lig_prof=mysqli_fetch_object($get_profs)) {
				if($i>0) {echo ",<br />\n";}
				echo $lig_prof->civilite." ".casse_mot($lig_prof->nom, 'maj').' '.casse_mot($lig_prof->prenom, 'majf2');
				$i++;
			}
			echo "</td>\n";

			for($i=1;$i<$nb_periode;$i++) {
				if($ver_periode[$i]=='P') {
					//echo "<td><input type='checkbox' name='periode_grp_".$current_group['id']."[]' value='$i' /></td>\n";
					echo "<td>\n";
					//echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;id_groupe=".$current_group['id']."&amp;periode=$i' title=\"Autoriser la saisie pour cet enseignement.\">Période $i</a>\n";
					echo "<input type='checkbox' name='enseignement_periode[]' id='case_".$i."_".$current_group['id']."' value='".$current_group['id']."|".$i."' onchange=\"checkbox_change(this.id)\" />";
					$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$current_group['id']."' AND periode='$i';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						$lig=mysqli_fetch_object($res);
						if($lig->date_limite>$date_courante) {
							echo "<br />";
							echo "Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
						}
					}
					echo "</td>\n";
				}
				elseif($ver_periode[$i]=='O') {
					echo "<td><img src='../images/disabled.png' width='20' height='20' alt='Période $i close' title='Période $i close' /></td>\n";
				}
				else {
					echo "<td>";

					// Vérifier si ce n'est pas verrouillé pour une autre classe, dans le cas d'un groupe multi-classe
					$tmp_tab_etat_per=etat_verrouillage_groupe_periode($current_group['id'], $i, array($id_classe));

					echo "<img src='../images/enabled.png' width='20' height='20' alt='Période $i ouverte en saisie' title=\"Période $i ouverte en saisie pour la classe de ".$classe."\" />";
					$tmp_contenu_td='';
					if($tmp_tab_etat_per['O']>0) {
						$tmp_contenu_td.="<img src='../images/disabled.png' width='20' height='20' title='Période $i close pour ".$tmp_tab_etat_per['O']." classe(s): ".$tmp_tab_etat_per['classes']['O']."' /> ";
					}
					if($tmp_tab_etat_per['P']>0) {
						if($tmp_contenu_td!='') {
							$tmp_contenu_td.="<br />";
						}
						//$tmp_contenu_td.="<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;id_groupe=".$current_group['id']."&amp;periode=$i' title='Période $i partiellement close pour ".$tmp_tab_etat_per['P']." classe(s).'>Période $i</a>";
						$tmp_contenu_td.="<input type='checkbox' name='enseignement_periode[]' id='case_".$i."_".$current_group['id']."' value='".$current_group['id']."|".$i."' onchange=\"checkbox_change(this.id)\" title=\"Période $i close pour ".$tmp_tab_etat_per['P']." classe(s): ".$tmp_tab_etat_per['classes']['P']."\" />";
						$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$current_group['id']."' AND periode='$i';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);
							if($lig->date_limite>$date_courante) {
								$tmp_contenu_td.="<br />";
								$tmp_contenu_td.="Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
							}
						}
					}
					/*
					if($tmp_contenu_td=='') {
						echo "<img src='../images/enabled.png' width='20' height='20' title='Période $i ouverte en saisie pour la classe de ".$classe."' />\n";
					}
					else {
						echo $tmp_contenu_td;
					}
					*/
					echo $tmp_contenu_td;

					echo "</td>\n";
				}
			}
			echo "</tr>\n";
		}
	}
	echo "</table>";


	// AID associés à des élèves de la classe
	$tab_aid_clas=get_tab_aid_ele_clas('', $id_classe);
	if(count($tab_aid_clas)>0) {
		// Tester s'il y en a qui sont visibles sur les bulletins...
		$lignes_aid='';
		foreach($tab_aid_clas as $current_aid) {
			if($current_aid["display_bulletin"]=="y") {
				$lignes_aid.="<tr class='white_hover'>\n";
				$lignes_aid.="<td style='text-align:left;'>".$current_aid['nom']." (<span style='font-size:xx-small;'>".$current_aid['nom_complet']."</span>)</td>\n";

				$lignes_aid.="<td>".$current_aid["classlist_string"]."</td>\n";

				$lignes_aid.="<td>\n";
				$sql="SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_aid_utilisateurs j WHERE (u.login = j.id_utilisateur and j.id_aid = '" . $current_aid['id_aid'] . "') ORDER BY u.nom, u.prenom";
				$get_profs=mysqli_query($GLOBALS["mysqli"], $sql);

				$nb = mysqli_num_rows($get_profs);
				$i=0;
				while($lig_prof=mysqli_fetch_object($get_profs)) {
					if($i>0) {$lignes_aid.=",<br />\n";}
					$lignes_aid.=$lig_prof->civilite." ".casse_mot($lig_prof->nom, 'maj').' '.casse_mot($lig_prof->prenom, 'majf2');
					$i++;
				}
				$lignes_aid.="</td>\n";

				for($i=1;$i<$nb_periode;$i++) {
					if($ver_periode[$i]=='P') {
						$lignes_aid.="<td>\n";
						$lignes_aid.="<input type='checkbox' name='aid_periode[]' id='case_aid_".$i."_".$current_aid['id_aid']."' value='".$current_aid['id_aid']."|".$i."' onchange=\"checkbox_change(this.id)\" />";
						$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_aid='".$current_aid['id_aid']."' AND periode='$i';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);
							if($lig->date_limite>$date_courante) {
								$lignes_aid.="<br />";
								$lignes_aid.="Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
							}
						}
						$lignes_aid.="</td>\n";
					}
					elseif($ver_periode[$i]=='O') {
						$lignes_aid.="<td><img src='../images/disabled.png' width='20' height='20' alt='Période $i close' title='Période $i close pour la classe de ".$classe."' /></td>\n";
					}
					else {
						$lignes_aid.="<td>";

						// Vérifier si ce n'est pas verrouillé pour une autre classe, dans le cas d'un AID multi-classe
						$tmp_tab_etat_per=etat_verrouillage_aid_periode($current_aid['id_aid'], $i, array($id_classe));


						$lignes_aid.="<img src='../images/enabled.png' width='20' height='20' alt='Période $i ouverte en saisie' title='Période $i ouverte en saisie pour la classe de ".$classe."' />";
						$tmp_contenu_td='';
						if($tmp_tab_etat_per['O']>0) {
							$tmp_contenu_td.="<img src='../images/disabled.png' width='20' height='20' title='Période $i close pour ".$tmp_tab_etat_per['O']." classe(s): ".$tmp_tab_etat_per['classes']['O'].".' /> ";
						}
						if($tmp_tab_etat_per['P']>0) {
							if($tmp_contenu_td!='') {
								$tmp_contenu_td.="<br />";
							}
							$tmp_contenu_td.="<input type='checkbox' name='aid_periode[]' id='case_".$i."_".$current_aid['id_aid']."' value='".$current_aid['id_aid']."|".$i."' onchange=\"checkbox_change(this.id)\" title=\"Période $i close pour ".$tmp_tab_etat_per['P']." classe(s): ".$tmp_tab_etat_per['classes']['P']."\" />";
							$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_aid='".$current_aid['id_aid']."' AND periode='$i';";
							$res=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res)>0) {
								$lig=mysqli_fetch_object($res);
								if($lig->date_limite>$date_courante) {
									$tmp_contenu_td.="<br />";
									$tmp_contenu_td.="Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
								}
							}
						}
						/*
						if($tmp_contenu_td=='') {
							$lignes_aid.="<img src='../images/enabled.png' width='20' height='20' title='Période $i ouverte en saisie pour la classe de ".$classe."' />\n";
						}
						else {
							$lignes_aid.=$tmp_contenu_td;
						}
						*/
						$lignes_aid.=$tmp_contenu_td;

						$lignes_aid.="</td>\n";
					}
				}
				$lignes_aid.="</tr>\n";
			}
		}
		if($lignes_aid!='') {
			echo "<table class='boireaus boireaus_alt' summary='Tableau des AID et périodes'>\n";
			echo "<tr>\n";
			echo "<th rowspan='2'>AID</th>\n";
			echo "<th rowspan='2'>Classe(s)</th>\n";
			echo "<th rowspan='2'>Enseignants</th>\n";
			echo "<th colspan='$nb_periode'>Périodes</th>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			for($i=1;$i<$nb_periode;$i++) {
				if($ver_periode[$i]=='P') {
					echo "
				<th>
					<a href='javascript:coche_per_aid($i,true)' title=\"Tout cocher pour la période $i\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/
					<a href='javascript:coche_per_aid($i,false)' title=\"Tout décocher pour la période $i\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>
				</th>\n";
				}
				else {
					echo "
				<th></th>";
				}
			}
			echo "</tr>\n";
			echo $lignes_aid;
			echo '</table>';
		}
	}


	// Récupérer la liste des CPE associés à la classe
	$liste_cpe='';
	$sql="SELECT DISTINCT u.login, nom, prenom, civilite FROM j_eleves_cpe jecpe, 
										j_eleves_classes jec, 
										utilisateurs u 
									WHERE jecpe.e_login=jec.login AND 
										jecpe.cpe_login=u.login AND 
										jec.id_classe='".$id_classe."'
									ORDER BY u.nom,u.prenom;";
	//echo "$sql<br />";
	$res_cpe=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_cpe)>0) {
		while($lig_cpe=mysqli_fetch_object($res_cpe)) {
			if($liste_cpe!='') {
				$liste_cpe.='<br/>';
			}
			$liste_cpe.=$lig_cpe->civilite." ".casse_mot($lig_cpe->nom, 'maj').' '.casse_mot($lig_cpe->prenom, 'majf2');
		}
	}
	if($liste_cpe!='') {
		echo "
	<p class='bold' style='margin-top:1em';>Saisie des appréciations vie scolaire.</p>
	<table class='boireaus boireaus_alt' summary='Tableau Vie Scolaire'>
		<tr>
			<th rowspan='2'>Vie scolaire</th>\n
			<th colspan='$nb_periode'>Périodes</th>
		</tr>
		<tr>";
		for($i=1;$i<$nb_periode;$i++) {
			echo "
			<th>
				P.$i
			</th>";
		}
		echo "
		</tr>
		<tr>
			<td>".$liste_cpe."</td>";
		for($i=1;$i<$nb_periode;$i++) {
			echo "
			<td>";
			if($ver_periode[$i]=='P') {
				echo "<input type='checkbox' name='enseignement_periode[]' id='case_".$i."_viescolaire' value='viescolaire|".$i."' onchange=\"checkbox_change(this.id)\" />";
				$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM abs_bull_delais WHERE id_classe='".$id_classe."' AND periode='$i' AND appreciation='y';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					$lig=mysqli_fetch_object($res);
					if($lig->date_limite>$date_courante) {
						echo "<br />";
						echo "Autorisation jusqu'au<br />".strftime("%d/%m/%Y à %H:%M",$lig->date_limite);
					}
				}
			}
			elseif($ver_periode[$i]=='O') {
				echo "<img src='../images/disabled.png' width='20' height='20' alt='Période $i close' title='Période $i close' />";
			}
			else {
				echo "<img src='../images/enabled.png' width='20' height='20' title='Période $i ouverte en saisie pour la classe de ".$classe."' />\n";
			}
			echo "
			</td>";
		}
		echo "
		</tr>
	</table>";
	}

	echo "
		<input type='hidden' name='id_classe' value='$id_classe' />
		<p><input type='submit' value='Autoriser tous les personnels sélectionnés' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	function coche_per(periode,mode) {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			type=champs_input[i].getAttribute('type');
			if(type=='checkbox'){
				id=champs_input[i].getAttribute('id');
				if(id.substring(0,6)=='case_'+periode) {
					champs_input[i].checked=mode;
				}
			}
		}

	}

	function coche_per_aid(periode,mode) {
		champs_input=document.getElementsByTagName('input');
		for(i=0;i<champs_input.length;i++){
			type=champs_input[i].getAttribute('type');
			if(type=='checkbox'){
				id=champs_input[i].getAttribute('id');
				if(id.substring(0,10)=='case_aid_'+periode) {
					champs_input[i].checked=mode;
				}
			}
		}

	}
</script>";

	echo "
<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em> <strong>La présente page ne présente d'intérêt qu'en période partiellement close</strong>.<br />
Voici pourquoi&nbsp;:<br />
En <strong>période ouverte en saisie</strong>, les professeurs peuvent saisir/modifier leurs notes et appréciations sans qu'aucune limitation ne se présente.<br />
En <strong>période close</strong>, aucune modification <em>(note, appréciation, avis du conseil de classe,...)</em> n'est plus possible.<br />
En <strong>période partiellement close</strong>, seuls les avis du conseil de classe peuvent être modifiés.<br />
C'est le moment où, à la lueur des saisies de notes et appréciations, le professeur principal <em>(ou le chef d'établissement selon les cas)</em> rédige les avis du conseil de classe.<br />
A cette phase avant le conseil de classe, il arrive qu'un professeur ait manqué de temps pour faire des saisies.<br />
Il est possible, via la présente page, de lui donner un accès exceptionnel à la saisie d'appréciations, sans pour autant rouvrir la saisie à tous les professeurs.<br />
Les modifications peuvent de plus être soit acceptées sans autre formalité, soit seulement proposées par le professeur et validées par un compte scolarité ou administrateur <em>(selon les Droits définis dans <strong>Gestion des droits</strong>)</em>.</p>";
}
elseif((isset($id_groupe))&&(isset($periode))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre enseignement de la classe</a>\n";
	echo " | <a href='autorisation_exceptionnelle_saisie_note.php?id_classe=$id_classe'>Autoriser la modification de moyennes des bulletins</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	$classe=get_nom_classe($id_classe);

	echo "<h2>Autoriser la modification d'appréciations des bulletins en ".$classe."</h2>";

	//if(!isset($is_posted)) {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo add_token_field();
		$group=get_group($id_groupe);
		echo "<p>Vous souhaitez autoriser exceptionnellement un enseignant à proposer des saisies/corrections d'appréciations pour l'enseignement <strong>".$group['name']." (<span style='font-size:x-small;'>".$group['description']." en ".$group['classlist_string']." avec ".$group['proflist_string']."</span>)</strong> en <strong>période&nbsp;$periode</strong>.</p>\n";

		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$group['id']."' AND periode='$periode';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;

			$date_courante=time();

			//$tab_date_limite=get_date($date_limite);
			echo "<p class='bold'>Une autorisation exceptionnelle de proposition de saisie existe pour cet enseignement/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</p>\n";
			$display_date_limite=strftime("%d/%m/%Y",$date_limite);
			$display_heure_limite=strftime("%H:%M",$date_limite);

			if($date_courante>$date_limite) {
				echo "<p class='bold' style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</p>\n";
			}
		}
		else {
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
			$display_date_limite=$jour."/".$mois."/".$annee;
		
			$date_courante=getdate();
			$heure_courante=$date_courante['hours'];
			$minute_courante=$date_courante['minutes'];
			if($minute_courante+15>=60) {
				if($heure_courante+1>=24) {
					$heure_limite=$heure_courante+1-24;
					$minute_limite=$minute_courante+15-60;
					// A charge au couche-tard d'augmenter d'un jour...
				}
				else {
					$heure_limite=$heure_courante+1;
					$minute_limite=$minute_courante+15-60;
				}
			}
			else {
				$heure_limite=$heure_courante;
				$minute_limite=$minute_courante+15;
			}
			$display_heure_limite="$heure_limite:$minute_limite";

			$ts_display_date_limite=mktime($heure_limite, $minute_limite, 0, $mois, $jour, $annee);
			if((isset($_SESSION['autorisation_saisie_date_limite']))&&($_SESSION['autorisation_saisie_date_limite']>=$ts_display_date_limite)) {
				$display_date_limite=strftime("%d/%m/%Y", $_SESSION['autorisation_saisie_date_limite']);
				$display_heure_limite=strftime("%H:%M", $_SESSION['autorisation_saisie_date_limite']);
			}
		}

		echo "<p style='margin-top:1em;'>Quelle doit être la date/heure limite de cette autorisation de proposition d'appréciation&nbsp;?<br />\n";
		//include("../lib/calendrier/calendrier.class.php");
		//$cal = new Calendrier("formulaire", "display_date_limite");

		if(isset($refermer_page)) {
			echo "<input type='hidden' name='refermer_page' value='y' />\n";
		}
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
		echo "<input type='hidden' name='periode' value='$periode' />\n";
		echo "<input type='text' name = 'display_date_limite' id = 'display_date_limite' size='8' value = \"".$display_date_limite."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
		//echo "<a href=\"#\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
		echo img_calendrier_js("display_date_limite", "img_bouton_display_date_limite");

		echo " à <input type='text' name='display_heure_limite' id='display_heure_limite' size='8' value = \"".$display_heure_limite."\" onKeyDown=\"clavier_heure(this.id,event);\" autocomplete=\"off\" />\n";
		echo "<br />";

		echo "<input type='radio' name='mode' id='mode_proposition' value='proposition' checked onchange=\"change_style_radio()\" /><label for='mode_proposition' id='texte_mode_proposition'> Permettre la proposition de corrections (<em>proposition qui devront ensuite être validées par un compte scolarité ou administrateur</em>).</label>\n";
		echo "<br />";
		if(getSettingAOui('autoriser_correction_bulletin')) {
			echo "<span style='color:red'>Ce premier mode ne présente pas d'intérêt ici puisque vous avez donné globalement le droit (<em>en administrateur dans Gestion générale/Droits d'accès</em>) de proposer des corrections tant que la période n'est pas complètement close</span>.<br /><span style='color:red'>Seul le mode ci-dessous apporte quelque chose dans votre configuration.</span><br />";
		}
		echo "<input type='radio' name='mode' id='mode_acces_complet' value='acces_complet' onchange=\"change_style_radio()\" /><label for='mode_acces_complet' id='texte_mode_acces_complet'> Permettre la saisie/modification des appréciations sans contrôle de votre part avant validation.</label>\n";
		echo "<br />";

		if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
			echo "<br />\n";
			echo "<input type='checkbox' name='donner_acces_modif_bull_note' id='donner_acces_modif_bull_note' value='y' onchange=\"checkbox_change(this.id)\" /><label for='donner_acces_modif_bull_note' id='texte_donner_acces_modif_bull_note' title=\"Dans le cas d'un AID, il se peut qu'il n'y ait pas de note autorisée.\"> Donner aussi l'accès à la modification de la moyenne sur les bulletins associés.</label>";
			echo "<br />\n";
		}

		echo "<input type='submit' name='Valider' value='Valider' />\n";
		echo "</p>\n";
	
		// Mail

		echo "</form>\n";

		echo "<br />
<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Par défaut, lorsque vous donnez un accès exceptionnel, c'est juste la possibilité pour le professeur de proposer des corrections en cliquant sur l'icone <img src='../images/edit16.png' class='icone16' alt='Modifier' /> dans sa page de saisie d'appréciations.<br />Les propositions formulées peuvent ensuite être contrôlées et validées par un compte scolarité ou administrateur.<br />
		Vous pouvez, en cochant, la case ci-dessus</p>";

/*
	}
	else {
		// Si le mail n'a pas pu être envoyé, proposer un lien mailto
			$message="Vous avez jusqu'au $date_limite_email pour saisir/corriger une ou des appréciations pour l'enseignement ".$current_group['name']." ($current_group['description']) en période $periode.\n\nCette autorisation est exceptionnelle.\nIl conviendra de veiller à effectuer les saisies dans les temps une prochaine fois.\n";

			echo "<tr class='lig$alt'>\n";
			echo "<td>\n";
			if($tab_alerte_prof[$login_prof]['email']!="") {
				$sujet_mail="[Gepi]: Appreciations et/ou moyennes manquantes";
				echo "<a href='mailto:".$tab_alerte_prof[$login_prof]['email']."?subject=$sujet_mail&amp;body=".rawurlencode($message)."'>".$info_prof."</a>";
	}
*/
}
elseif((isset($id_aid))&&(isset($periode))) {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre enseignement ou AID de la classe</a>\n";
	echo " | <a href='autorisation_exceptionnelle_saisie_note.php?id_classe=$id_classe'>Autoriser la modification de moyennes des bulletins</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	$classe=get_nom_classe($id_classe);

	echo "<h2>Autoriser la modification d'appréciations des bulletins en ".$classe."</h2>";

	//if(!isset($is_posted)) {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>\n";
		echo add_token_field();
		$aid=get_tab_aid($id_aid);
		echo "<p>Vous souhaitez autoriser exceptionnellement un enseignant à proposer des saisies/corrections d'appréciations pour l'AID <strong>".$aid['nom']." (<span style='font-size:x-small;'>".$aid['nom_complet']." en ".$aid['classlist_string']." avec ".$aid['proflist_string']."</span>)</strong> en <strong>période&nbsp;$periode</strong>.</p>\n";

		$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_aid='".$aid['id_aid']."' AND periode='$periode';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$date_limite=$lig->date_limite;

			$date_courante=time();

			//$tab_date_limite=get_date($date_limite);
			echo "<p class='bold'>Une autorisation exceptionnelle de proposition de saisie existe pour cet enseignement/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</p>\n";
			$display_date_limite=strftime("%d/%m/%Y",$date_limite);
			$display_heure_limite=strftime("%H:%M",$date_limite);

			if($date_courante>$date_limite) {
				echo "<p class='bold' style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</p>\n";
			}
		}
		else {
			$annee = strftime("%Y");
			$mois = strftime("%m");
			$jour = strftime("%d");
			$display_date_limite=$jour."/".$mois."/".$annee;
		
			$date_courante=getdate();
			$heure_courante=$date_courante['hours'];
			$minute_courante=$date_courante['minutes'];
			if($minute_courante+15>=60) {
				if($heure_courante+1>=24) {
					$heure_limite=$heure_courante+1-24;
					$minute_limite=$minute_courante+15-60;
					// A charge au couche-tard d'augmenter d'un jour...
				}
				else {
					$heure_limite=$heure_courante+1;
					$minute_limite=$minute_courante+15-60;
				}
			}
			else {
				$heure_limite=$heure_courante;
				$minute_limite=$minute_courante+15;
			}
			$display_heure_limite="$heure_limite:$minute_limite";

			$ts_display_date_limite=mktime($heure_limite, $minute_limite, 0, $mois, $jour, $annee);
			if((isset($_SESSION['autorisation_saisie_date_limite']))&&($_SESSION['autorisation_saisie_date_limite']>=$ts_display_date_limite)) {
				$display_date_limite=strftime("%d/%m/%Y", $_SESSION['autorisation_saisie_date_limite']);
				$display_heure_limite=strftime("%H:%M", $_SESSION['autorisation_saisie_date_limite']);
			}
		}

		echo "<p style='margin-top:1em;'>Quelle doit être la date/heure limite de cette autorisation de proposition d'appréciation&nbsp;?<br />\n";
		//include("../lib/calendrier/calendrier.class.php");
		//$cal = new Calendrier("formulaire", "display_date_limite");

		if(isset($refermer_page)) {
			echo "<input type='hidden' name='refermer_page' value='y' />\n";
		}
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='id_aid' value='$id_aid' />\n";
		echo "<input type='hidden' name='periode' value='$periode' />\n";
		echo "<input type='text' name = 'display_date_limite' id = 'display_date_limite' size='8' value = \"".$display_date_limite."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
		//echo "<a href=\"#\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
		echo img_calendrier_js("display_date_limite", "img_bouton_display_date_limite");

		echo " à <input type='text' name='display_heure_limite' id='display_heure_limite' size='8' value = \"".$display_heure_limite."\" onKeyDown=\"clavier_heure(this.id,event);\" autocomplete=\"off\" />\n";
		echo "<br />";

		echo "<input type='radio' name='mode' id='mode_proposition' value='proposition' checked onchange=\"change_style_radio()\" /><label for='mode_proposition' id='texte_mode_proposition'> Permettre la proposition de corrections (<em>proposition qui devront ensuite être validées par un compte scolarité ou administrateur</em>).</label>\n";
		echo "<br />";
		if(getSettingAOui('autoriser_correction_bulletin')) {
			echo "<span style='color:red'>Ce premier mode ne présente pas d'intérêt ici puisque vous avez donné globalement le droit (<em>en administrateur dans Gestion générale/Droits d'accès</em>) de proposer des corrections tant que la période n'est pas complètement close</span>.<br /><span style='color:red'>Seul le mode ci-dessous apporte quelque chose dans votre configuration.</span><br />";
		}
		echo "<input type='radio' name='mode' id='mode_acces_complet' value='acces_complet' onchange=\"change_style_radio()\" /><label for='mode_acces_complet' id='texte_mode_acces_complet'> Permettre la saisie/modification des appréciations sans contrôle de votre part avant validation.</label>\n";
		echo "<br />";

		if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
			echo "<br />\n";
			echo "<input type='checkbox' name='donner_acces_modif_bull_note' id='donner_acces_modif_bull_note' value='y' onchange=\"checkbox_change(this.id)\" /><label for='donner_acces_modif_bull_note' id='texte_donner_acces_modif_bull_note' title=\"Dans le cas d'un AID, il se peut qu'il n'y ait pas de note autorisée.\"> Donner aussi l'accès à la modification de la moyenne sur les bulletins associés.</label>";
			echo "<br />\n";
		}

		echo "<input type='submit' name='Valider' value='Valider' />\n";
		echo "</p>\n";
	
		// Mail

		echo "</form>\n";

		echo "<br />
<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Par défaut, lorsque vous donnez un accès exceptionnel, c'est juste la possibilité pour le professeur de proposer des corrections en cliquant sur l'icone <img src='../images/edit16.png' class='icone16' alt='Modifier' /> dans sa page de saisie d'appréciations.<br />Les propositions formulées peuvent ensuite être contrôlées et validées par un compte scolarité ou administrateur.<br />
		Vous pouvez, en cochant, la case ci-dessus</p>";
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'>Choisir un autre enseignement de la classe</a>\n";
	echo " | <a href='autorisation_exceptionnelle_saisie_note.php?id_classe=$id_classe'>Autoriser la modification de moyennes des bulletins</a>\n";
	echo "</p>\n";

	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe, "", "span");
	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe="<div class='fieldset_opacite50' style='float:right; width:10em; font-size:normal; text-align:center;'>".$chaine_date_conseil_classe."</div>";
		echo $chaine_date_conseil_classe;
	}

	$classe=get_nom_classe($id_classe);

	echo "<h2>Autoriser la modification d'appréciations des bulletins en ".$classe."</h2>";

	if(((!isset($enseignement_periode))||(!is_array($enseignement_periode))||(count($enseignement_periode)==0))&&
	((!isset($aid_periode))||(!is_array($aid_periode))||(count($aid_periode)==0))) {
		echo "<p style='color:red'>Enseignement(s) ou AID(s) et période(s) non choisis.</p>";
		die();
	}

	//20171222

	//if(!isset($is_posted)) {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field();

		echo "<p style='margin-left:3em; text-indent:-3em;'>Vous souhaitez autoriser exceptionnellement un ou des enseignants à proposer des saisies/corrections d'appréciations pour le ou les enseignements suivants&nbsp;:</p>
	<div style='margin-left:3em; margin-bottom:1em;'>";
		$temoin_vie_scolaire=false;
		$temoin_enseignement=false;
		$temoin_aid=false;
		if((isset($enseignement_periode))&&(is_array($enseignement_periode))) {
			for($loop=0;$loop<count($enseignement_periode);$loop++) {
				$tab_ens_per=explode('|', $enseignement_periode[$loop]);
				if((isset($tab_ens_per[1]))&&($tab_ens_per[0]=='viescolaire')&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {
					// On le traite dans une deuxième partie
					$temoin_vie_scolaire=true;
					$periode=$tab_ens_per[1];
				}
				elseif((isset($tab_ens_per[1]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[0]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {
					$temoin_enseignement=true;
					$id_groupe=$tab_ens_per[0];
					$periode=$tab_ens_per[1];

					$group=get_group($id_groupe);
					if(isset($group['name'])) {
						echo "<input type='hidden' name='enseignement_periode[]' value='".$enseignement_periode[$loop]."' />\n";

						echo "<strong>".$group['name']." (<span style='font-size:x-small;'>".$group['description']." en ".$group['classlist_string']." avec ".$group['proflist_string']."</span>)</strong> en <strong>période $periode</strong>";

						$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='".$group['id']."' AND periode='$periode';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);
							$date_limite=$lig->date_limite;

							$date_courante=time();

							if($date_courante>$date_limite) {
								//echo "<span style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</span><br />\n";
								// On fait le ménage:
								$sql="DELETE FROM matieres_app_delais WHERE id_groupe='".$group['id']."' AND periode='$periode';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								echo "<br /><span style='color:blue'>Une autorisation exceptionnelle de proposition de saisie existe pour cet enseignement/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</span><br />\n";
							}
							$display_date_limite=strftime("%d/%m/%Y",$date_limite);
							$display_heure_limite=strftime("%H:%M",$date_limite);

							//if($date_courante>$date_limite) {
							//	echo "<span style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</span><br />\n";
							//}
						}
						echo "<br />";
					}
					else {
						echo "<span style='color:red'>L'enseignement n°".$id_groupe." est inconnu.</span><br />";
					}
				}
				else {
					echo "<span style='color:red'>Le couple id_groupe/période est invalide&nbsp;: ".$enseignement_periode[$loop]."</span><br />";
				}
			}
		}

		if((isset($aid_periode))&&(is_array($aid_periode))) {
			for($loop=0;$loop<count($aid_periode);$loop++) {
				$tab_ens_per=explode('|', $aid_periode[$loop]);
				if((isset($tab_ens_per[1]))&&($tab_ens_per[0]=='viescolaire')&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {
					// On le traite dans une deuxième partie
					$temoin_vie_scolaire=true;
				}
				elseif((isset($tab_ens_per[1]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[0]))&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {
					$temoin_aid=true;
					$id_aid=$tab_ens_per[0];
					$periode=$tab_ens_per[1];

					$aid=get_tab_aid($id_aid);
					if(isset($aid['nom'])) {
						echo "<input type='hidden' name='aid_periode[]' value='".$aid_periode[$loop]."' />\n";

						echo "<strong>".$aid['nom']." (<span style='font-size:x-small;'>".$aid['nom_complet']." en ".$aid['classlist_string']." avec ".$aid['proflist_string']."</span>)</strong> en <strong>période $periode</strong>";

						$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_aid='".$aid['id_aid']."' AND periode='$periode';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($res)>0) {
							$lig=mysqli_fetch_object($res);
							$date_limite=$lig->date_limite;

							$date_courante=time();

							if($date_courante>$date_limite) {
								//echo "<span style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</span><br />\n";
								// On fait le ménage:
								$sql="DELETE FROM matieres_app_delais WHERE id_aid='".$aid['id_aid']."' AND periode='$periode';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
							}
							else {
								echo "<br /><span style='color:blue'>Une autorisation exceptionnelle de proposition de saisie existe pour cet AID/période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</span><br />\n";
							}
							$display_date_limite=strftime("%d/%m/%Y",$date_limite);
							$display_heure_limite=strftime("%H:%M",$date_limite);

							//if($date_courante>$date_limite) {
							//	echo "<span style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</span><br />\n";
							//}
						}
						echo "<br />";
					}
					else {
						echo "<span style='color:red'>L'AID n°".$id_aid." est inconnu.</span><br />";
					}
				}
				else {
					echo "<span style='color:red'>Le couple id_aid/période est invalide&nbsp;: ".$aid_periode[$loop]."</span><br />";
				}
			}
		}
		echo "</div>";


		$annee = strftime("%Y");
		$mois = strftime("%m");
		$jour = strftime("%d");
		$display_date_limite=$jour."/".$mois."/".$annee;
	
		$date_courante=getdate();
		$heure_courante=$date_courante['hours'];
		$minute_courante=$date_courante['minutes'];
		if($minute_courante+15>=60) {
			if($heure_courante+1>=24) {
				$heure_limite=$heure_courante+1-24;
				$minute_limite=$minute_courante+15-60;
				// A charge au couche-tard d'augmenter d'un jour...
			}
			else {
				$heure_limite=$heure_courante+1;
				$minute_limite=$minute_courante+15-60;
			}
		}
		else {
			$heure_limite=$heure_courante;
			$minute_limite=$minute_courante+15;
		}
		$display_heure_limite="$heure_limite:$minute_limite";

		$ts_display_date_limite=mktime($heure_limite, $minute_limite, 0, $mois, $jour, $annee);
		if((isset($_SESSION['autorisation_saisie_date_limite']))&&($_SESSION['autorisation_saisie_date_limite']>=$ts_display_date_limite)) {
			$display_date_limite=strftime("%d/%m/%Y", $_SESSION['autorisation_saisie_date_limite']);
			$display_heure_limite=strftime("%H:%M", $_SESSION['autorisation_saisie_date_limite']);
		}

		echo "<p style='margin-top:1em;'>Quelle doit être la date/heure limite de l'autorisation de proposition d'appréciation&nbsp;?<br />\n";
		//include("../lib/calendrier/calendrier.class.php");
		//$cal = new Calendrier("formulaire", "display_date_limite");

		if(isset($refermer_page)) {
			echo "<input type='hidden' name='refermer_page' value='y' />\n";
		}
		echo "<input type='hidden' name='is_posted' value='y' />\n";
		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='periode' value='$periode' />\n";
		echo "<input type='text' name = 'display_date_limite' id = 'display_date_limite' size='8' value = \"".$display_date_limite."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
		//echo "<a href=\"#\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>\n";
		echo img_calendrier_js("display_date_limite", "img_bouton_display_date_limite");

		echo " à <input type='text' name='display_heure_limite' id='display_heure_limite' size='8' value = \"".$display_heure_limite."\" onKeyDown=\"clavier_heure(this.id,event);\" autocomplete=\"off\" />\n";
		echo "<br />";

		if(($temoin_enseignement)||($temoin_aid)) {
			echo "<p style='margin-top:1em;'>";
			echo "<input type='radio' name='mode' id='mode_proposition' value='proposition' checked onchange=\"change_style_radio()\" /><label for='mode_proposition' id='texte_mode_proposition'> Permettre la proposition de corrections (<em>proposition qui devront ensuite être validées par un compte scolarité ou administrateur</em>).</label>\n";
			echo "<br />";
			if(getSettingAOui('autoriser_correction_bulletin')) {
				echo "<span style='color:red'>Ce premier mode ne présente pas d'intérêt ici puisque vous avez donné globalement le droit (<em>en administrateur dans Gestion générale/Droits d'accès</em>) de proposer des corrections tant que la période n'est pas complètement close</span>.<br /><span style='color:red'>Seul le mode ci-dessous apporte quelque chose dans votre configuration.</span><br />";
			}
			echo "<input type='radio' name='mode' id='mode_acces_complet' value='acces_complet' onchange=\"change_style_radio()\" /><label for='mode_acces_complet' id='texte_mode_acces_complet'> Permettre la saisie/modification des appréciations sans contrôle de votre part avant validation.</label>\n";
			echo "<br />";

			if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
				echo "<p style='margin-top:1em;'>\n";
				echo "<input type='checkbox' name='donner_acces_modif_bull_note' id='donner_acces_modif_bull_note' value='y' onchange=\"checkbox_change(this.id)\" /><label for='donner_acces_modif_bull_note' id='texte_donner_acces_modif_bull_note' title=\"Dans le cas d'un AID, il se peut qu'il n'y ait pas de note autorisée.\"> Donner aussi l'accès à la modification de la moyenne sur les bulletins associés.</label>";
				echo "</p>\n";
			}
		}

		if($temoin_vie_scolaire) {

			echo "<p style='margin-top:1em;margin-left:3em; text-indent:-3em;'>Vous souhaitez autoriser exceptionnellement un ou des CPE à effectuer des saisies/corrections d'appréciations Vie Scolaire&nbsp;:<br />";

			for($loop=0;$loop<count($enseignement_periode);$loop++) {
				$tab_ens_per=explode('|', $enseignement_periode[$loop]);
				if((isset($tab_ens_per[1]))&&($tab_ens_per[0]=='viescolaire')&&(preg_match('/^[0-9]{1,}$/', $tab_ens_per[1]))) {

					$periode=$tab_ens_per[1];

					echo "<input type='hidden' name='enseignement_periode[]' value='".$enseignement_periode[$loop]."' />\n";

					echo "<strong>Vie Scolaire</strong> en <strong>période $periode</strong>";

					$sql="SELECT UNIX_TIMESTAMP(date_limite) AS date_limite FROM matieres_app_delais WHERE id_groupe='-1' AND periode='$periode';";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)>0) {
						$lig=mysqli_fetch_object($res);
						$date_limite=$lig->date_limite;

						$date_courante=time();

						if($date_courante>$date_limite) {
							//echo "<span style='color:red;'>Le délais imparti pour la proposition de saisie/correction est dépassé.</span><br />\n";
							// On fait le ménage:
							$sql="DELETE FROM matieres_app_delais WHERE id_groupe='-1' AND periode='$periode';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
						}
						else {
							echo "<br /><span style='color:blue'>Une autorisation exceptionnelle de proposition de saisie existe pour la période&nbsp;: ".strftime("%d/%m/%Y à %H:%M",$date_limite)."</span><br />\n";
						}
						$display_date_limite=strftime("%d/%m/%Y",$date_limite);
						$display_heure_limite=strftime("%H:%M",$date_limite);
					}
					echo "<br />";
				}
			}

			if((getSettingAOui('abs2_import_manuel_bulletin'))&&
			(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol'))))) {
				echo "<p style='margin-top:1em;'>\n";
				echo "<input type='checkbox' name='donner_acces_modif_totaux_abs' id='donner_acces_modif_totaux_abs' value='y' onchange=\"checkbox_change(this.id)\" /><label for='donner_acces_modif_totaux_abs' id='texte_donner_acces_modif_totaux_abs'> Donner aussi l'accès à la modification des totaux d'absences, non justifiées et retards sur les bulletins associés.</label>";
				echo "</p>\n";
			}
		}

		echo "<p style='margin-top:1em;'><input type='submit' name='Valider' value='Valider' /></p>\n";

		echo "</form>\n";

		echo "<br />
<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Par défaut, lorsque vous donnez un accès exceptionnel, c'est juste la possibilité pour le professeur de proposer des corrections en cliquant sur l'icone <img src='../images/edit16.png' class='icone16' alt='Modifier' /> dans sa page de saisie d'appréciations.<br />Les propositions formulées peuvent ensuite être contrôlées et validées par un compte scolarité ou administrateur.<br />
		Dans le cas où vous donnez une autorisation de modification Vie scolaire aux CPE de la classe, la modification effectuée par un CPE est immédiate, sans attente de confirmation/validation par un compte scolarité ou administrateur.</p>";
}

echo "
<script type='text/javascript'>
	".js_checkbox_change_style().
	js_change_style_radio()."
	change_style_radio();
</script>
<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
