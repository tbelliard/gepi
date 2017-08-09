<?php
/*
*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../lib/share-trombinoscope.inc.php");

// On teste si on affiche le message de changement de mot de passe
if (isset($_GET['change_mdp'])) $affiche_message = 'yes';
$message_enregistrement = "Par sécurité, vous devez changer votre mot de passe.";

// Resume session
if ($session_gepi->security_check() == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

$msg="";

if (($_SESSION['statut'] == 'professeur') or ($_SESSION['statut'] == 'cpe') or ($_SESSION['statut'] == 'responsable') or ($_SESSION['statut'] == 'eleve')) {
	// Mot de passe comportant des lettres et des chiffres
	$flag = 0;
} else {
	// Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
	$flag = 1;
}

if($_SESSION['statut'] == 'professeur') {
	// Ménage sur l'ordre des groupes dans l'affichage simplifié prof:
	// Sinon, on peut se retrouver avec des rangs aberrants liés à des groupes qui n'existent plus dans la table groupes.
	//$tab_mes_groupes=get_groups_for_prof($_SESSION['login'],array());
	$tab_mes_id_groupe=array();
	$sql="SELECT DISTINCT jgp.id_groupe FROM j_groupes_professeurs jgp WHERE (jgp.login='". $_SESSION['login']."');";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_mes_id_groupe[]=$lig->id_groupe;
	}
	$sql="SELECT name FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'accueil_simpl_id_groupe_order_%';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tmp_id_groupe=preg_replace("/^accueil_simpl_id_groupe_order_/", "", $lig->name);
		if(!in_array($tmp_id_groupe, $tab_mes_id_groupe)) {
			$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name='accueil_simpl_id_groupe_order_".$tmp_id_groupe."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}

	if(isset($_POST['parametrer_edt'])) {
		check_token();

		$edt2_couleur_grp=isset($_POST['edt2_couleur_grp']) ? $_POST['edt2_couleur_grp'] : array();
		$cpt_reg=0;
		foreach($edt2_couleur_grp as $id_groupe => $couleur) {
			if(($couleur=="")||(!in_array($couleur, $tab_couleurs_html))) {
				$sql="DELETE FROM preferences WHERE login='".$_SESSION['login']."' AND name='edt2_couleur_grp_".$id_groupe."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else {
				$couleur_old=getPref($_SESSION['login'], 'edt2_couleur_grp_'.$id_groupe, "");
				if($couleur_old=="") {
					$sql="INSERT INTO preferences SET login='".$_SESSION['login']."', name='edt2_couleur_grp_".$id_groupe."', value='$couleur';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$cpt_reg++;
					}
					else {
						$msg.="Erreur lors de l'enregistrement de la couleur $couleur pour le groupe n°$id_groupe.<br />";
					}
				}
				elseif($couleur_old!=$couleur) {
					$sql="UPDATE preferences SET value='$couleur' WHERE login='".$_SESSION['login']."' AND name='edt2_couleur_grp_".$id_groupe."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						$cpt_reg++;
					}
					else {
						$msg.="Erreur lors de la mise à jour de la couleur $couleur pour le groupe n°$id_groupe.<br />";
					}
				}
			}
		}
	}
}

if ((isset($_POST['valid'])) and ($_POST['valid'] == "yes"))  {
	check_token();

	$msg = '';
	$no_modif = "yes";
	$no_anti_inject_password_a = isset($_POST["no_anti_inject_password_a"]) ? $_POST["no_anti_inject_password_a"] : NULL;
	$no_anti_inject_password1 = isset($_POST["no_anti_inject_password1"]) ? $_POST["no_anti_inject_password1"] : NULL;
	$reg_password2 = isset($_POST["reg_password2"]) ? $_POST["reg_password2"] : NULL;
	$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
	$reg_show_email = isset($_POST["reg_show_email"]) ? $_POST["reg_show_email"] : "no";

	// On commence par récupérer quelques infos.
	$req = mysqli_query($GLOBALS["mysqli"], "SELECT password, auth_mode FROM utilisateurs WHERE (login = '".$session_gepi->login."')");
	$old_password = old_mysql_result($req, 0, "password");
	$user_auth_mode = old_mysql_result($req, 0, "auth_mode");
	if ($no_anti_inject_password_a != '') {
		// Modification du mot de passe

		if ($no_anti_inject_password1 == $reg_password2) {
			// On a bien un mot de passe et sa confirmation qui correspond

			if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
				// On est en mode d'écriture LDAP.
				// On tente un bind pour tester le nouveau mot de passe, et s'assurer qu'il
				// est différent de celui actuellement utilisé :
				$ldap_server = new LDAPServer;
				$test_bind_nouveau = $ldap_server->authenticate_user($session_gepi->login, $no_anti_inject_password1);

				// On teste aussi l'ancien mot de passe.
				$test_bind_ancien = $ldap_server->authenticate_user($session_gepi->login, $no_anti_inject_password_a);

				if (!$test_bind_ancien) {
					// L'ancien mot de passe n'est pas correct
					$msg = "L'ancien mot de passe n'est pas correct !";
				} elseif ($test_bind_nouveau) {
					// Le nouveau mot de passe est le même que l'ancien
					$msg = "ERREUR : Vous devez choisir un nouveau mot de passe différent de l'ancien.";
				} else {
					// C'est bon, on enregistre
					$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', '', '', $no_anti_inject_password1,'');
					if ($write_ldap_success) {
						$msg = "Le mot de passe a ete modifié !";
						$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET change_mdp='n' WHERE login = '" . $session_gepi->login . "'");
						$no_modif = "no";
						if (isset($_POST['retour'])) {
							header("Location:../accueil.php?msg=$msg");
							die();
						}
					}
				}
			} else {
				// Dans le cas où l'accès de secours auth_sso_secours est activé, en se connectant avec l'accès secours, on a:
				// $_SESSION['current_auth_mode']=gepi
				// C'est ce qui doit être testé dans $session_gepi->authenticate_gepi($session_gepi->login,$NON_PROTECT['password_a'])

				function unhtmlentities($chaineHtml)
				{
					$tmp = get_html_translation_table(HTML_ENTITIES);
					$tmp = array_flip ($tmp);
					$chaineTmp = strtr ($chaineHtml, $tmp);
					return $chaineTmp;
				}

				// On fait la mise à jour sur la base de données
				if ($session_gepi->authenticate_gepi($session_gepi->login,$NON_PROTECT['password_a'])) {
					if  ($no_anti_inject_password_a == $no_anti_inject_password1) {
						$msg = "ERREUR : Vous devez choisir un nouveau mot de passe différent de l'ancien.";
					} else if (!(verif_mot_de_passe($NON_PROTECT['password1'],$flag))) {
						$msg = "Erreur lors de la saisie du mot de passe (<em>voir les recommandations</em>), veuillez recommencer !";
						if((isset($info_verif_mot_de_passe))&&($info_verif_mot_de_passe!="")) {$msg.="<br />".$info_verif_mot_de_passe;}
					} else {
						$reg = Session::change_password_gepi($session_gepi->login,$NON_PROTECT['password1']);
						if ($reg) {
							mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET change_mdp='n' WHERE login = '$session_gepi->login'");
							$msg = "Le mot de passe a ete modifié !";
							$no_modif = "no";
							if (isset($_POST['retour'])) {
								header("Location:../accueil.php?msg=$msg");
								die();
							}
						}
					}
				} else {
					$msg = "L'ancien mot de passe n'est pas correct !";
				}
			}
		} else {
			$msg = "Erreur lors de la saisie du mot de passe, les deux mots de passe ne sont pas identiques. Veuillez recommencer !";
		}
	}

	$call_email = mysqli_query($GLOBALS["mysqli"], "SELECT email,show_email FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
	$user_email = old_mysql_result($call_email, 0, "email");
	$user_show_email = old_mysql_result($call_email, 0, "show_email");

	if(($_SESSION['statut']!='responsable')&&($_SESSION['statut']!='eleve')) {
		if ($user_email != $reg_email) {

			if(($reg_email!="")&&(!check_mail($reg_email, "full"))) {
				$msg.="L'adresse mail proposée '$reg_email' n'est pas valide.<br />";
			}
			else {
				if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
					if (!isset($ldap_server)) $ldap_server = new LDAPServer;
					$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
				}
				$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
				if ($reg) {
					if($msg!="") {$msg.="<br />";}
					$msg.="L'adresse e_mail a été modifiéé !";
					$_SESSION['email']=$reg_email;
					$no_modif = "no";
				}
			}
		}
	}
	if(($_SESSION['statut']=='responsable')&&((getSettingValue('mode_email_resp')=='')||(getSettingValue('mode_email_resp')=='mon_compte'))) {
		if ($user_email != $reg_email) {
			if(($reg_email!="")&&(!check_mail($reg_email, "full"))) {
				$msg.="L'adresse mail proposée '$reg_email' n'est pas valide.<br />";
			}
			else {
				if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
					if (!isset($ldap_server)) $ldap_server = new LDAPServer;
					$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
				}
				$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
				if ($reg) {
					if($msg!="") {$msg.="<br />";}
					$msg.="L'adresse e_mail a été modifiéé !";
					$no_modif = "no";
					$_SESSION['email']=$reg_email;

					if((getSettingValue('mode_email_resp')=='mon_compte')) {
						$sql="UPDATE resp_pers SET mel='$reg_email' WHERE login='".$_SESSION['login']."';";
						$update_resp=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update_resp) {$msg.="<br />Erreur lors de la mise à jour de la table 'resp_pers'.";}

						if((getSettingValue('envoi_mail_actif')!='n')&&(getSettingValue('informer_scolarite_modif_mail')!='n')) {
							$sujet_mail=remplace_accents("Mise à jour mail ".$_SESSION['nom']." ".$_SESSION['prenom'],'all');
							$message_mail="L'adresse email du responsable ";
							$message_mail.=remplace_accents($_SESSION['nom']." ".$_SESSION['prenom'],'all')." est passée à '$reg_email'. Vous devriez mettre à jour Sconet en conséquence.";
							$destinataire_mail=getSettingValue('email_dest_info_modif_mail');
							if($destinataire_mail=="") {
								$destinataire_mail=getSettingValue('gepiSchoolEmail');
							}
							$tab_param_mail['destinataire']=$destinataire_mail;
							if(($destinataire_mail!='')&&(check_mail($destinataire_mail))) {
								envoi_mail($sujet_mail, $message_mail, $destinataire_mail, "", "plain", $tab_param_mail);
							}
						}

						if((getSettingValue('envoi_mail_actif')!='n')&&(check_mail($user_email))) {
							$sujet_mail="Mise à jour de votre adresse mail";
							$message_mail="Vous avez procédé à la modification de votre adresse mail dans 'Gérer mon compte' le ".strftime('%A %d/%m/%Y à %H:%M:%S').". Votre nouvelle adresse est donc '$reg_email'. C'est cette adresse qui sera utilisée pour les éventuels prochains messages.";
							$destinataire_mail=$user_email;
							$tab_param_mail['destinataire']=$destinataire_mail;
							envoi_mail($sujet_mail, $message_mail, $destinataire_mail, "", "plain", $tab_param_mail);
						}
					}
				}
			}
		}
	}
	elseif(($_SESSION['statut']=='eleve')&&((getSettingValue('mode_email_ele')=='')||(getSettingValue('mode_email_ele')=='mon_compte'))) {
		if ($user_email != $reg_email) {
			if(($reg_email!="")&&(!check_mail($reg_email, "full"))) {
				$msg.="L'adresse mail proposée '$reg_email' n'est pas valide.<br />";
			}
			else {
				if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
					if (!isset($ldap_server)) $ldap_server = new LDAPServer;
					$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
				}
				$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
				if ($reg) {
					if($msg!="") {$msg.="<br />";}
					$msg.="L'adresse e_mail a été modifiéé !";
					$no_modif = "no";
					$_SESSION['email']=$reg_email;

					if((getSettingValue('mode_email_ele')=='mon_compte')) {
						$sql="UPDATE eleves SET email='$reg_email' WHERE login='".$_SESSION['login']."';";
						$update_eleve=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$update_eleve) {$msg.="<br />Erreur lors de la mise à jour de la table 'eleves'.";}

						if((getSettingValue('envoi_mail_actif')!='n')&&(getSettingValue('informer_scolarite_modif_mail')!='n')) {
							$sujet_mail=remplace_accents("Mise à jour mail ".$_SESSION['nom']." ".$_SESSION['prenom'],'all');
							$message_mail="L'adresse email de l'élève ";
							$message_mail.=remplace_accents($_SESSION['nom']." ".$_SESSION['prenom'],'all')." est passée à '$reg_email'. Vous devriez mettre à jour Sconet en conséquence.";
							$destinataire_mail=getSettingValue('email_dest_info_modif_mail');
							if($destinataire_mail=="") {
								$destinataire_mail=getSettingValue('gepiSchoolEmail');
							}
							$tab_param_mail['destinataire']=$destinataire_mail;
							if(($destinataire_mail!='')&&(check_mail($destinataire_mail))) {
								envoi_mail($sujet_mail, $message_mail, $destinataire_mail, "", "plain", $tab_param_mail);
							}
						}
					}
				}
			}
		}
	}


	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe")
	if ($user_show_email != $reg_show_email) {
	if ($reg_show_email != "no" and $reg_show_email != "yes") $reg_show_email = "no";
		$reg = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET show_email = '$reg_show_email' WHERE login = '" . $_SESSION['login'] . "'");
		if ($reg) {
			if($msg!="") {$msg.="<br />";}
			$msg.="Le paramétrage d'affichage de votre email a été modifié !";
			$no_modif = "no";
		}
	}

	//======================================
	// pour le module trombinoscope
	/*
	if(($_SESSION['statut']=='administrateur')||
	($_SESSION['statut']=='scolarite')||
	($_SESSION['statut']=='cpe')||
	($_SESSION['statut']=='professeur')) {
	*/
	if((getSettingValue("active_module_trombino_pers")=='y')&&
		((($_SESSION['statut']=='administrateur')&&(getSettingValue("GepiAccesModifMaPhotoAdministrateur")=='yes'))||
		(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesModifMaPhotoScolarite")=='yes'))||
		(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesModifMaPhotoCpe")=='yes'))||
		(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesModifMaPhotoProfesseur")=='yes')))) {

		// Envoi de la photo
		// si modification du nom ou du prénom ou du pseudo il faut modifier le nom de la photo d'identitée
		$i_photo = 0;
		$user_login=$_SESSION['login'];
		$calldata_photo = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM utilisateurs WHERE (login = '".$user_login."')");
		$ancien_nom = old_mysql_result($calldata_photo, $i_photo, "nom");
		$ancien_prenom = old_mysql_result($calldata_photo, $i_photo, "prenom");

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
		}else{
		  $repertoire="../photos/personnels/";
		}

		//$repertoire = '../photos/personnels/';



		$ancien_code_photo = md5(mb_strtolower($user_login));
		$nouveau_code_photo = $ancien_code_photo;


		// DEBUG:
		//echo "\$ancien_code_photo=$ancien_code_photo<br />\n";
		//echo "\$nouveau_code_photo=$nouveau_code_photo<br />\n";

		if(isset($ancien_code_photo)) {
			if($ancien_code_photo != "") {
				//if(isset($_POST['suppr_filephoto']) and $valide_form === 'oui' ) {
				if(isset($_POST['suppr_filephoto'])) {
					if($_POST['suppr_filephoto']=='y') {
						if(@unlink($repertoire.$ancien_code_photo.".jpg")) {
							if($msg!="") {$msg.="<br />";}
							$msg.="La photo ".$repertoire.$ancien_code_photo.".jpg a été supprimée. ";
							$no_modif="no";
						}
						else {
							if($msg!="") {$msg.="<br />";}
							$msg.="Echec de la suppression de la photo ".$repertoire.$ancien_code_photo.".jpg ";
						}
					}
				}

				// DEBUG:
				//echo "\$HTTP_POST_FILES['filephoto']['tmp_name']=".$HTTP_POST_FILES['filephoto']['tmp_name']."<br />\n";
				//echo "\$_FILES['filephoto']['tmp_name']=".$_FILES['filephoto']['tmp_name']."<br />\n";

				// filephoto
				//if(isset($HTTP_POST_FILES['filephoto']['tmp_name'])) {
				if(isset($_FILES['filephoto']['tmp_name'])) {
					//$filephoto_tmp=$HTTP_POST_FILES['filephoto']['tmp_name'];
					$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
					//if ( $filephoto_tmp != '' and $valide_form === 'oui' ) {
					if ($filephoto_tmp!='') {
						//$filephoto_name=$HTTP_POST_FILES['filephoto']['name'];
						//$filephoto_size=$HTTP_POST_FILES['filephoto']['size'];
						//$filephoto_type=$HTTP_POST_FILES['filephoto']['type'];
						$filephoto_name=$_FILES['filephoto']['name'];
						$filephoto_size=$_FILES['filephoto']['size'];
						$filephoto_type=$_FILES['filephoto']['type'];
						if (!(preg_match('/jpg$/',strtolower($filephoto_name)) || preg_match('/jpeg$/',strtolower($filephoto_name))) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
							if($msg!="") {$msg.="<br />";}
							$msg .= "Erreur : seuls les fichiers ayant l'extension .jpg ou .jpeg sont autorisés.\n";
						} else {
							// Tester la taille max de la photo?
							if(is_uploaded_file($filephoto_tmp)) {
								$dest_file = $repertoire.$nouveau_code_photo.".jpg";
								//$source_file=stripslashes("$filephoto_tmp");
								$source_file=$filephoto_tmp;
								$res_copy=copy("$source_file" , "$dest_file");
								if($res_copy) {
									//$msg.="Mise en place de la photo effectuée.";
									if($msg!="") {$msg.="<br />";}
									$msg.="Mise en place de la photo effectuée. <br />Il peut être nécessaire de rafraîchir la page, voire de vider le cache du navigateur<br />pour qu'un changement de photo soit pris en compte.";
									$no_modif="no";

									if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
											// si le redimensionnement des photos est activé on redimensionne

											if (getSettingValue("active_module_trombinoscopes_rt")!='')
												$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"),getSettingValue("active_module_trombinoscopes_rt"));
											else
												$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
											if (!$redim_OK) $msg .= "<br /> Echec du redimensionnement de la photo.";
										}
								}
								else {
									if($msg!="") {$msg.="<br />";}
									$msg.="Erreur lors de la mise en place de la photo.";
								}
							}
							else {
								if($msg!="") {$msg.="<br />";}
								$msg.="Erreur lors de l'upload de la photo.";
							}
						}
					}
				}
			}
		}
	}
	//elseif($_SESSION['statut']=='eleve') {
	elseif(($_SESSION['statut']=='eleve')&&(getSettingValue("active_module_trombinoscopes")=='y')&&(getSettingValue("GepiAccesModifMaPhotoEleve")=='yes')) {
		// Upload de la photo en tant qu'élève

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".$_COOKIE['RNE']."/eleves/";
		}else{
		  $repertoire="../photos/eleves/";
		}

		$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
		$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_elenoet)>0) {
			$lig_tmp_elenoet=mysqli_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			// Envoi de la photo
			if(isset($reg_no_gep)) {
				if($reg_no_gep!="") {
					if(mb_strlen(my_ereg_replace("[0-9]","",$reg_no_gep))==0) {
						if(isset($_POST['suppr_filephoto'])) {
							if($_POST['suppr_filephoto']=='y') {

								// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
								$photo=nom_photo($reg_no_gep);

								if("$photo"!="") {
									if(@unlink($photo)) {
										if($msg!="") {$msg.="<br />";}
										$msg.="La photo ".$photo." a été supprimée. ";
										$no_modif="no";
									}
									else {
										if($msg!="") {$msg.="<br />";}
										$msg.="Echec de la suppression de la photo ".$photo." ";
									}
								}
								else {
									if($msg!="") {$msg.="<br />";}
									$msg.="Echec de la suppression de la photo correspondant à $reg_no_gep (<i>non trouvée</i>) ";
								}
							}
						}

						// Contrôler qu'un seul élève a bien cet elenoet???
						$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						$nb_elenoet=mysqli_num_rows($test);
						if($nb_elenoet==1) {
							if(isset($_FILES['filephoto']['tmp_name'])) {
								// filephoto
								//$filephoto_tmp=$HTTP_POST_FILES['filephoto']['tmp_name'];
								$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
								if($filephoto_tmp!="") {
									//$filephoto_name=$HTTP_POST_FILES['filephoto']['name'];
									//$filephoto_size=$HTTP_POST_FILES['filephoto']['size'];
									//$filephoto_type=$HTTP_POST_FILES['filephoto']['type'];
									$filephoto_name=$_FILES['filephoto']['name'];
									$filephoto_size=$_FILES['filephoto']['size'];
									$filephoto_type=$_FILES['filephoto']['type'];
									if (!(preg_match('/jpg$/',strtolower($filephoto_name)) ||  preg_match('/jpg$/',strtolower($filephoto_name))) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
										//$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
										if($msg!="") {$msg.="<br />";}
										$msg .= "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.\n";
									} else {
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)) {
										$dest_file=$repertoire.encode_nom_photo($reg_no_gep).".jpg";
										//$source_file=stripslashes("$filephoto_tmp");
										$source_file=$filephoto_tmp;
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy) {
											//$msg.="Mise en place de la photo effectuée.";
											if($msg!="") {$msg.="<br />";}
											$msg.="Mise en place de la photo effectuée. <br />Il peut être nécessaire de rafraîchir la page, voire de vider le cache du navigateur<br />pour qu'un changement de photo soit pris en compte.";
											$no_modif="no";

											if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
												// si le redimensionnement des photos est activé on redimensionne
												if (getSettingValue("active_module_trombinoscopes_rt")!='')
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"),getSettingValue("active_module_trombinoscopes_rt"));
												else
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
												if (!$redim_OK) $msg .= "<br /> Echec du redimensionnement de la photo.";
												}
										}
										else {
											if($msg!="") {$msg.="<br />";}
											$msg.="Erreur lors de la mise en place de la photo.";
										}
									}
									else {
										if($msg!="") {$msg.="<br />";}
										$msg.="Erreur lors de l'upload de la photo.";
									}
									}
								}
							}
						}
						elseif($nb_elenoet==0) {
							if($msg!="") {$msg.="<br />";}
							//$msg.="Le numéro GEP de l'élève n'est pas enregistré dans la table 'eleves'.";
							$msg.="Le numéro interne Sconet (elenoet) de l'élève n'est pas enregistré dans la table 'eleves'.";
						}
						else {
							if($msg!="") {$msg.="<br />";}
							//$msg.="Le numéro GEP est commun à plusieurs élèves. C'est une anomalie.";
							$msg.="Le numéro interne Sconet (elenoet) est commun à plusieurs élèves. C'est une anomalie.";
						}
					}
					else {
						if($msg!="") {$msg.="<br />";}
						//$msg.="Le numéro GEP proposé contient des caractères non numériques.";
						$msg.="Le numéro interne Sconet (elenoet) proposé contient des caractères non numériques.";
					}
				} else {
						if($msg!="") {$msg.="<br />";}
						$msg.="Le numéro interne Sconet (elenoet) est vide. Impossible de continuer. Veuillez signaler ce problème à l'administrateur.";
				}
			} else {
				if($msg!="") {$msg.="<br />";}
				$msg.="Vous n'avez pas numéro interne Sconet. Impossible de continuer. Veuillez signaler ce problème à l'administrateur.";
			}
		} else {
			if($msg!="") {$msg.="<br />";}
			$msg.="Vous n'avez pas numéro interne Sconet. Impossible de continuer. Veuillez signaler ce problème à l'administrateur.";
		}
	}

	//======================================
	if(($_SESSION['statut']=='professeur')&&(isset($_POST['matiere_principale']))) {
		/*
		// DANS /lib/session.inc, la matière principale du professeur est récupérée ainsi:
			$sql2 = "select id_matiere from j_professeurs_matieres where id_professeur = '" . $_login . "' order by ordre_matieres limit 1";
			$matiere_princ = sql_query1($sql2);

			mysql> show fields from j_professeurs_matieres;
			+----------------+-------------+------+-----+---------+-------+
			| Field          | Type        | Null | Key | Default | Extra |
			+----------------+-------------+------+-----+---------+-------+
			| id_professeur  | varchar(50) | NO   | PRI |         |       |
			| id_matiere     | varchar(50) | NO   | PRI |         |       |
			| ordre_matieres | int(11)     | NO   |     | 0       |       |
			+----------------+-------------+------+-----+---------+-------+
			3 rows in set (0.06 sec)

			mysql>
		*/

		$sql="SELECT DISTINCT jpm.id_matiere FROM j_professeurs_matieres jpm WHERE (jpm.id_professeur='".$_SESSION["login"]."') ORDER BY jpm.ordre_matieres;";
		//echo "$sql<br />\n";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$tab_matieres=array();
			while($lig_mat=mysqli_fetch_object($test)) {
				$tab_matieres[]=$lig_mat->id_matiere;
				//echo $lig_mat->id_matiere." ";
			}
			//echo "<br />\n";

			// On n'accepte la modification que si la matière reçue fait bien déjà partie des matières du professeur
			if(in_array($_POST['matiere_principale'],$tab_matieres)) {
				// On ne modifie que si la matière principale choisie n'est pas celle enregistrée auparavant
				if($_POST['matiere_principale']!=$tab_matieres[0]) {
					$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='".$_SESSION["login"]."';";
					//echo "$sql<br />\n";
					$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

					$ordre_matieres=1;
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$_POST['matiere_principale']."', ordre_matieres='$ordre_matieres';";
					//echo "$sql<br />\n";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if($_POST['matiere_principale']!=$tab_matieres[$loop]) {
							$ordre_matieres++;
							$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$tab_matieres[$loop]."', ordre_matieres='$ordre_matieres';";
							//echo "$sql<br />\n";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}

					$_SESSION['matiere']=$_POST['matiere_principale'];

					$no_modif="no";
					if($msg!="") {$msg.="<br />";}
					$msg.="Modification de la matière principale effectuée.";
				}
			}
		}
	}

	if((($_SESSION['statut']=='professeur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe'))&&(isset($_POST['reg_civilite']))) {
		if($msg!="") {$msg.="<br />";}
		if(($_POST['reg_civilite']!='M.')&&($_POST['reg_civilite']!='Mlle')&&($_POST['reg_civilite']!='Mme')) {
			$msg.="La civilité choisie n'est pas valide.";
		}
		else {
			$sql="SELECT civilite FROM utilisateurs WHERE login='".$_SESSION['login']."';";
			$res_civ=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_civ)>0) {
				$tmp_civ=old_mysql_result($res_civ, 0, "civilite");
				if($tmp_civ!=$_POST['reg_civilite']) {
					$sql="UPDATE utilisateurs SET civilite='".$_POST['reg_civilite']."' WHERE login='".$_SESSION['login']."';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de la civilité.";
					}
					else {
						$msg.="Civilité mise à jour.";
						$no_modif="no";
					}
				}
			}
		}
	}
	//======================================

	if ($no_modif == "yes") {
		if($msg!="") {$msg.="<br />";}
		$msg.="Aucune modification n'a été apportée !";
	}
}

// 20160304
if (((in_array($_SESSION['statut'], array("scolarite", "administrateur")))||
	(($_SESSION['statut']=="professeur")&&(is_pp($_SESSION['login']))))&&
	(isset($_POST['valide_ouverture_periode_et_acces']))) {
	check_token();

	$message_ouverture_periode_et_acces="";

	if(isset($_POST['accueil_tableau_ouverture_periode'])) {
		if(savePref($_SESSION['login'], "accueil_tableau_ouverture_periode", $_POST['accueil_tableau_ouverture_periode'])) {
			$msg.="Enregistrement de la préférence 'accueil_tableau_ouverture_periode' effectué.<br />";
			$message_ouverture_periode_et_acces.="<p style='color:green'>Enregistrement de la préférence 'accueil_tableau_ouverture_periode' effectué&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
		}
		else{
			$msg.="Erreur lors de l'enregistrement de la préférence 'accueil_tableau_ouverture_periode'.<br />";
			$message_ouverture_periode_et_acces.="<p style='color:red'>Erreur lors de l'enregistrement de la préférence 'accueil_tableau_ouverture_periode'&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
		}
	}

	if(isset($_POST['accueil_tableau_acces_app_bull_ele_resp'])) {
		if(savePref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", $_POST['accueil_tableau_acces_app_bull_ele_resp'])) {
			$msg.="Enregistrement de la préférence 'accueil_tableau_acces_app_bull_ele_resp' effectué.<br />";
			$message_ouverture_periode_et_acces.="<p style='color:green'>Enregistrement de la préférence 'accueil_tableau_acces_app_bull_ele_resp' effectué&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
		}
		else{
			$msg.="Erreur lors de l'enregistrement de la préférence 'accueil_tableau_acces_app_bull_ele_resp'.<br />";
			$message_ouverture_periode_et_acces.="<p style='color:red'>Erreur lors de l'enregistrement de la préférence 'accueil_tableau_acces_app_bull_ele_resp'&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
		}
	}
}

//debug_var();
if (($_SESSION["statut"] == "professeur")&&(isset($_POST['valide_accueil_simpl_prof']))) {
	check_token();

	$i=0;
	$prof[$i]=$_SESSION['login'];

	$nb_reg=0;
	$message_accueil_simpl_prof="";

	$tab=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_cn','accueil_bull','accueil_visu','accueil_trombino','accueil_liste_pdf');

	for($j=0;$j<count($tab);$j++){
		unset($valeur);
		//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
		$tmp_champ=$tab[$j]."_".$i;
		$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : "n";

		$insert=savePref($_SESSION['login'], $tab[$j], $valeur);
		if($insert) {
			$nb_reg++;
		}
		else {
			$msg.="Erreur lors de l'enregistrement de $tab[$j] à $valeur<br />\n";
			$message_accueil_simpl_prof.="Erreur lors de l'enregistrement de $tab[$j] à $valeur.<br />";
		}
	}

	if($message_accueil_simpl_prof!='') {
		$message_accueil_simpl_prof="<p style='color:red'>".$message_accueil_simpl_prof."</p>";
	}

	$msg.="$nb_reg enregistrement(s) effectué(s).<br />";
	$message_accueil_simpl_prof.="<p style='color:green'>$nb_reg enregistrement(s) effectué(s).</p>";

	/*
		$tab_grp_order=array();
		$tab_grp_hidden=array();
		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'accueil_simpl_id_groupe_order_%' ORDER BY value;";
		echo "$sql<br />";
		$res_grp_order=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp_order)>0) {
			while($lig_grp_order=mysqli_fetch_object($res_grp_order)) {
				$tmp_id_groupe=preg_replace("/^accueil_simpl_id_groupe_order_/", "", $lig_grp_order->name);
				if($lig_grp_order->value=='-1') {
					$tab_grp_hidden[]=$tmp_id_groupe;
				}
				else {
					$tab_grp_order[]=$tmp_id_groupe;
				}
			}
		}
	*/

	$accueil_simpl_afficher_grp=isset($_POST['accueil_simpl_afficher_grp']) ? $_POST['accueil_simpl_afficher_grp'] : array();
	if(count($accueil_simpl_afficher_grp)>0) {
		$accueil_simpl_afficher_grp_rang=isset($_POST['accueil_simpl_afficher_grp_rang']) ? $_POST['accueil_simpl_afficher_grp_rang'] : array();

		$nb_reg=0;
		$nb_err=0;

		/*
		for($loop=1;$loop<count($accueil_simpl_afficher_grp)+1;$loop++) {
			if(isset($accueil_simpl_afficher_grp_rang[$loop])) {
				if(savePref($_SESSION['login'], "accueil_simpl_id_groupe_order_".$accueil_simpl_afficher_grp[$loop], sprintf("%02d", $accueil_simpl_afficher_grp_rang[$loop]))) {
					$nb_reg++;
				}
				else{
					$nb_err++;
				}
			}
		}
		*/

		foreach($accueil_simpl_afficher_grp as $key => $tmp_id_groupe) {
			if(isset($accueil_simpl_afficher_grp_rang[$key])) {
				if(savePref($_SESSION['login'], "accueil_simpl_id_groupe_order_".$accueil_simpl_afficher_grp[$key], sprintf("%02d", $accueil_simpl_afficher_grp_rang[$key]))) {
					$nb_reg++;
				}
				else{
					$nb_err++;
				}
			}
		}

		$msg.="Enregistrement de l'ordre des groupes pour $nb_reg groupe(s) avec $nb_err erreur(s)<br />";
		$message_accueil_simpl_prof.="<p style='color:green'>Enregistrement de l'ordre des groupes pour $nb_reg groupe(s).";
		if($nb_err>0) {
			$message_accueil_simpl_prof.="<br />$nb_err erreur(s) lors de l'enregistrement.";
		}
		$message_accueil_simpl_prof.="</p>";
	}
}

//================================================================================
if ((($_SESSION["statut"] == "eleve")||($_SESSION["statut"] == "responsable"))&&(isset($_POST['valide_accueil_simpl']))&&(isset($_POST['accueil_simpl']))) {
	check_token();

	$message_accueil_simpl="\n";

	$accueil_simpl="y";
	if($_POST['accueil_simpl']!="y") {
		$accueil_simpl="n";
	}

	if(savePref($_SESSION['login'], "accueil_simpl", $accueil_simpl)) {
		$msg.="Enregistrement de la préférence 'accueil_simpl' effectué.<br />";
		$message_accueil_simpl.="<p style='color:green'>Enregistrement de la préférence 'accueil_simpl' effectué&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
	}
	else{
		$msg.="Erreur lors de l'enregistrement de la préférence 'accueil_simpl'.<br />";
		$message_accueil_simpl.="<p style='color:red'>Erreur lors de l'enregistrement de la préférence 'accueil_simpl'&nbsp;: ".strftime("%d/%m/%Y à %H:%M:%S")."</p>";
	}
}

//================================================================================

// 20121128
if (($_SESSION["statut"] == "professeur")&&(isset($_POST['valide_nom_ou_description_groupe']))) {
	check_token();

	$nb_reg=0;
	$message_nom_ou_description_groupe="";

	$nom_ou_description_groupe_barre_h=isset($_POST['nom_ou_description_groupe_barre_h']) ? $_POST['nom_ou_description_groupe_barre_h'] : NULL;
	if((isset($nom_ou_description_groupe_barre_h))&&(savePref($_SESSION['login'], "nom_ou_description_groupe_barre_h", $nom_ou_description_groupe_barre_h))) {
		$nb_reg++;
	}

	$nom_ou_description_groupe_cdt=isset($_POST['nom_ou_description_groupe_cdt']) ? $_POST['nom_ou_description_groupe_cdt'] : NULL;
	if((isset($nom_ou_description_groupe_cdt))&&(savePref($_SESSION['login'], "nom_ou_description_groupe_cdt", $nom_ou_description_groupe_cdt))) {
		$nb_reg++;
	}

	if($nb_reg==0) {
		$message_nom_ou_description_groupe="<span style='color:red'>Aucun paramètre n'a été enregistré.</span>";
	}
	else {
		$message_nom_ou_description_groupe="<span style='color:green'>$nb_reg paramètre(s) enregistré(s).</span>";
	}

}

if ((getSettingValue('active_carnets_notes')!='n')&&($_SESSION["statut"] == "professeur")&&(isset($_POST['valide_form_cn']))) {
	check_token();

	$i=0;
	$prof[$i]=$_SESSION['login'];

	$nb_reg=0;
	$message_cn="";

	$tab=array('add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite', 'add_modif_dev_display_parents', 'add_modif_dev_display_parents_app');
	for($j=0;$j<count($tab);$j++){
		unset($valeur);
		//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
		$tmp_champ=$tab[$j]."_".$i;
		$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : "n";

		$insert=savePref($_SESSION['login'], $tab[$j], $valeur);
		if($insert) {
			$nb_reg++;
		}
		else {
			$msg.="Erreur lors de l'enregistrement de $tab[$j] à $valeur<br />\n";
			$message_cn.="Erreur lors de l'enregistrement de $tab[$j] à $valeur.<br />";
		}
	}

	$tab=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');
	for($j=0;$j<count($tab);$j++){
		unset($valeur);
		//$valeur=isset($_POST[$tab[$j]]) ? $_POST[$tab[$j]] : NULL;
		$tmp_champ=$tab[$j]."_".$i;
		$valeur=isset($_POST[$tmp_champ]) ? $_POST[$tmp_champ] : "n";

		$insert=savePref($_SESSION['login'], $tab[$j], $valeur);
		if($insert) {
			$nb_reg++;
		}
		else {
			$msg.="Erreur lors de l'enregistrement de $tab[$j] à $valeur.<br />\n";
			$message_cn.="Erreur lors de l'enregistrement de $tab[$j] à $valeur.<br />";
		}
	}

	$aff_quartiles_cn=isset($_POST['aff_quartiles_cn']) ? $_POST['aff_quartiles_cn'] : "n";
	$insert=savePref($_SESSION['login'], 'aff_quartiles_cn', $aff_quartiles_cn);
	if($insert) {
		$nb_reg++;
	}
	else {
		$msg.="Erreur lors de l'enregistrement de aff_quartiles_cn à $aff_quartiles_cn.<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de aff_quartiles_cn à $aff_quartiles_cn.<br />";
	}

	$aff_photo_cn=isset($_POST['aff_photo_cn']) ? $_POST['aff_photo_cn'] : "n";
	$insert=savePref($_SESSION['login'], 'aff_photo_cn', $aff_photo_cn);
	if($insert) {
		$nb_reg++;
	}
	else {
		$msg.="Erreur lors de l'enregistrement de aff_photo_cn à $aff_photo_cn.<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de aff_photo_cn à $aff_photo_cn.<br />";
	}

	$cn_avec_min_max=isset($_POST['cn_avec_min_max']) ? $_POST['cn_avec_min_max'] : "n";
	if(!savePref($_SESSION['login'],'cn_avec_min_max',$cn_avec_min_max)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_avec_min_max'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_avec_min_max'<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_avec_mediane_q1_q3=isset($_POST['cn_avec_mediane_q1_q3']) ? $_POST['cn_avec_mediane_q1_q3'] : "n";
	if(!savePref($_SESSION['login'],'cn_avec_mediane_q1_q3',$cn_avec_mediane_q1_q3)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_avec_mediane_q1_q3'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_avec_mediane_q1_q3'.<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_avec_sup10=isset($_POST['cn_avec_sup10']) ? $_POST['cn_avec_sup10'] : "n";
	if(!savePref($_SESSION['login'],'cn_avec_sup10',$cn_avec_sup10)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_avec_sup10'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_avec_sup10'.<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_order_by=isset($_POST['cn_order_by']) ? $_POST['cn_order_by'] : "classe";
	if(!savePref($_SESSION['login'],'cn_order_by',$cn_order_by)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_order_by'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_order_by'.<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_default_nom_court=isset($_POST['cn_default_nom_court']) ? $_POST['cn_default_nom_court'] : "Nouvelle évaluation";
	if(!savePref($_SESSION['login'],'cn_default_nom_court',$cn_default_nom_court)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_default_nom_court'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_default_nom_court'.<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_default_nom_complet=isset($_POST['cn_default_nom_complet']) ? $_POST['cn_default_nom_complet'] : "n";
	if(!savePref($_SESSION['login'],'cn_default_nom_complet',$cn_default_nom_complet)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_default_nom_complet'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_default_nom_complet'.<br />";
	}
	else {
		$nb_reg++;
	}

	$cn_default_coef=isset($_POST['cn_default_coef']) ? $_POST['cn_default_coef'] : "n";
	if(!savePref($_SESSION['login'],'cn_default_coef',$cn_default_coef)) {
		$msg.="Erreur lors de l'enregistrement de 'cn_default_coef'<br />\n";
		$message_cn.="Erreur lors de l'enregistrement de 'cn_default_coef'.<br />";
	}
	else {
		$nb_reg++;
	}

	if($message_cn!='') {
		$message_cn="<p style='color:red'>".$message_cn."</p>";
	}

	$cnBoitesModeMoy=isset($_POST['cnBoitesModeMoy']) ? $_POST['cnBoitesModeMoy'] : "";
	if($cnBoitesModeMoy=="") {
		$msg.="Vous n'avez pas choisi le mode de calcul par défaut de la moyenne dans le cas où vous créez des ".getSettingValue('gepi_denom_boite')."s.<br />";
		$message_cn.="<span style='color:red'>Vous n'avez pas choisi le mode de calcul par défaut de la moyenne dans le cas où vous créez des ".getSettingValue('gepi_denom_boite')."s.</span><br />";
	}
	else {
		if(($cnBoitesModeMoy==1)||($cnBoitesModeMoy==2)) {
			if(!savePref($_SESSION['login'],'cnBoitesModeMoy',$cnBoitesModeMoy)) {
				$msg.="Erreur lors de l'enregistrement de 'cnBoitesModeMoy'<br />\n";
				$message_cn.="<span style='color:red'>Erreur lors de l'enregistrement de 'cnBoitesModeMoy'.</span><br />";
			}
			else {
				$nb_reg++;
			}
		}
		else {
			$msg.="Le mode de calcul par défaut de la moyenne choisi dans le cas où vous créez des ".getSettingValue('gepi_denom_boite')."s est invalide.<br />\n";
			$message_cn.="<span style='color:red'>Le mode de calcul par défaut de la moyenne choisi dans le cas où vous créez des ".getSettingValue('gepi_denom_boite')."s est invalide.</span><br />\n";
		}
	}

	$msg.="$nb_reg enregistrement(s) effectué(s).<br />";
	$message_cn.="<p style='color:green'>$nb_reg enregistrement(s) effectué(s).</p>";
}


if(($_SESSION['statut']=='professeur')&&(isset($_POST['valide_form_bull']))) {
	check_token();

	$message_bulletins="";

	$aff_photo_saisie_app=isset($_POST['aff_photo_saisie_app']) ? $_POST['aff_photo_saisie_app'] : "n";
	$insert=savePref($_SESSION['login'], 'aff_photo_saisie_app', $aff_photo_saisie_app);
	if($insert) {
		$msg.="Enregistrement de aff_photo_saisie_app effectué.<br />\n";
	}
	else {
		$msg.="Erreur lors de l'enregistrement de aff_photo_saisie_app à $aff_photo_saisie_app.<br />\n";
		$message_bulletins.="Erreur lors de l'enregistrement de aff_photo_saisie_app à $aff_photo_saisie_app.<br />";
	}

	$saisie_app_nb_cols_textarea=isset($_POST['saisie_app_nb_cols_textarea']) ? $_POST['saisie_app_nb_cols_textarea'] : 100;
	if((!is_numeric($saisie_app_nb_cols_textarea))||($saisie_app_nb_cols_textarea<=0)) {
		$msg.="Valeur invalide sur saisie_app_nb_cols_textarea pour ".$_SESSION['login']."<br />\n";
		$message_bulletins.="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	elseif(!savePref($_SESSION['login'], 'saisie_app_nb_cols_textarea', $saisie_app_nb_cols_textarea)) {
		$msg.="Erreur lors de l'enregistrement de saisie_app_nb_cols_textarea pour ".$_SESSION['login']."<br />\n";
		$message_bulletins.="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	else {
		$msg.="Enregistrement de saisie_app_nb_cols_textarea effectué.<br />\n";
		$message_bulletins.="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}



	$id_groupe_AppPP=isset($_POST['id_groupe_AppPP']) ? $_POST['id_groupe_AppPP'] : array();
	$id_groupe_PP_correction_autorisee=isset($_POST['id_groupe_PP_correction_autorisee']) ? $_POST['id_groupe_PP_correction_autorisee'] : array();

	$cpt_modif=0;
	$cpt_err=0;
	for($loop=0;$loop<count($id_groupe_AppPP);$loop++) {
		if(acces_correction_app_pp($id_groupe_AppPP[$loop])) {
			if(isset($id_groupe_PP_correction_autorisee[$loop])) {
				// Pas de changement.
			}
			else {
				$sql="DELETE FROM groupes_param WHERE id_groupe='".$id_groupe_AppPP[$loop]."' AND name='AutoriserCorrectionAppreciationParPP';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$del) {
					$cpt_err++;
				}
				else {
					$cpt_modif++;
				}
			}
		}
		else {
			if(isset($id_groupe_PP_correction_autorisee[$loop])) {
				$sql="INSERT INTO groupes_param SET id_groupe='".$id_groupe_AppPP[$loop]."', name='AutoriserCorrectionAppreciationParPP', value='y';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$cpt_err++;
				}
				else {
					$cpt_modif++;
				}
			}
		}
	}

	$msg.="$cpt_modif modification(s) d'autorisation de correction d'appréciation par le ".getSettingValue('gepi_prof_suivi')."<br />";
	$msg.="$cpt_err erreur(s) lors de l'opération.<br />";

	$message_bulletins.="<span style='color:green'>$cpt_modif modification(s) d'autorisation de correction d'appréciation par le ".getSettingValue('gepi_prof_suivi')."</span><br />";
	$message_bulletins.="<span style='color:red'>$cpt_err erreur(s) lors de l'opération.</span><br />";
}


if(($_SESSION['statut']=='professeur')&&(isset($_POST['validation_form_cdt2']))) {
	check_token();

	$message_cdt="";
	$temoin_erreur_cdt=0;
	$temoin_reg_cdt=0;


	if(!getSettingAOui("active_cahiers_texte")) {
		if(isset($_POST['acces_cdt_prof'])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!savePref($_SESSION['login'],'acces_cdt_prof',$valeur)) {
			$msg.="Erreur lors de l'enregistrement de acces_cdt_prof.<br />";
			$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de acces_cdt_prof.</p>\n";
			$temoin_erreur_cdt++;
		}
		else {
			$msg.="Enregistrement de acces_cdt_prof.<br />";
			$temoin_reg_cdt++;
			//$message_cdt.="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
	}

	if(isset($_POST['ouverture_auto_WinDevoirsDeLaClasse'])) {
		if(($_POST['ouverture_auto_WinDevoirsDeLaClasse']=='y')||($_POST['ouverture_auto_WinDevoirsDeLaClasse']=='n')) {
			if(!savePref($_SESSION['login'],'ouverture_auto_WinDevoirsDeLaClasse',$_POST['ouverture_auto_WinDevoirsDeLaClasse'])) {
				$msg.="Erreur lors de l'enregistrement de ouverture_auto_WinDevoirsDeLaClasse.<br />";
				$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de ouverture_auto_WinDevoirsDeLaClasse.</p>\n";
				$temoin_erreur_cdt++;
			}
			else {
				$msg.="Enregistrement de ouverture_auto_WinDevoirsDeLaClasse.<br />";
				$temoin_reg_cdt++;
				//$message_cdt.="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
			}
		}
	}

	if(isset($_POST['cdt2_WinListeNotices_nb_compte_rendus'])) {
		if((!preg_match("/^[0-9]*$/", $_POST['cdt2_WinListeNotices_nb_compte_rendus']))||($_POST['cdt2_WinListeNotices_nb_compte_rendus']<=0)||($_POST['cdt2_WinListeNotices_nb_compte_rendus']=="")) {
			$msg.="Valeur invalide pour cdt2_WinListeNotices_nb_compte_rendus.<br />";
			$temoin_erreur_cdt++;
		}
		else {
			if(!savePref($_SESSION['login'],'cdt2_WinListeNotices_nb_compte_rendus',$_POST['cdt2_WinListeNotices_nb_compte_rendus'])) {
				$msg.="Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_compte_rendus.<br />";
				$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_compte_rendus.</p>\n";
				$temoin_erreur_cdt++;
			}
			else {
				$msg.="Enregistrement de cdt2_WinListeNotices_nb_compte_rendus.<br />";
				$temoin_reg_cdt++;
			}
		}
	}

	if(isset($_POST['cdt2_WinListeNotices_nb_devoirs'])) {
		if((!preg_match("/^[0-9]*$/", $_POST['cdt2_WinListeNotices_nb_devoirs']))||($_POST['cdt2_WinListeNotices_nb_devoirs']<=0)||($_POST['cdt2_WinListeNotices_nb_devoirs']=="")) {
			$msg.="Valeur invalide pour cdt2_WinListeNotices_nb_devoirs.<br />";
			$temoin_erreur_cdt++;
		}
		else {
			if(!savePref($_SESSION['login'],'cdt2_WinListeNotices_nb_devoirs',$_POST['cdt2_WinListeNotices_nb_devoirs'])) {
				$msg.="Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_devoirs.<br />";
				$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_devoirs.</p>\n";
				$temoin_erreur_cdt++;
			}
			else {
				$msg.="Enregistrement de cdt2_WinListeNotices_nb_devoirs.<br />";
				$temoin_reg_cdt++;
			}
		}
	}

	if(isset($_POST['cdt2_WinListeNotices_nb_notices_privees'])) {
		if((!preg_match("/^[0-9]*$/", $_POST['cdt2_WinListeNotices_nb_notices_privees']))||($_POST['cdt2_WinListeNotices_nb_notices_privees']<=0)||($_POST['cdt2_WinListeNotices_nb_notices_privees']=="")) {
			$msg.="Valeur invalide pour cdt2_WinListeNotices_nb_notices_privees.<br />";
			$temoin_erreur_cdt++;
		}
		else {
			if(!savePref($_SESSION['login'],'cdt2_WinListeNotices_nb_notices_privees',$_POST['cdt2_WinListeNotices_nb_notices_privees'])) {
				$msg.="Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_notices_privees.<br />";
				$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de cdt2_WinListeNotices_nb_notices_privees.</p>\n";
				$temoin_erreur_cdt++;
			}
			else {
				$msg.="Enregistrement de cdt2_WinListeNotices_nb_notices_privees.<br />";
				$temoin_reg_cdt++;
			}
		}
	}

	if(isset($_POST['cdt2_afficher_passer_au_cours_suivant'])) {
		if(!in_array($_POST['cdt2_afficher_passer_au_cours_suivant'], array('les_2', 'lendemain', 'cours_suivant'))) {
			$msg.="Valeur invalide pour cdt2_afficher_passer_au_cours_suivant.<br />";
			$temoin_erreur_cdt++;
		}
		else {
			if(!savePref($_SESSION['login'],'cdt2_afficher_passer_au_cours_suivant',$_POST['cdt2_afficher_passer_au_cours_suivant'])) {
				$msg.="Erreur lors de l'enregistrement de cdt2_afficher_passer_au_cours_suivant.<br />";
				$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement de cdt2_afficher_passer_au_cours_suivant.</p>\n";
				$temoin_erreur_cdt++;
			}
			else {
				$msg.="Enregistrement de cdt2_afficher_passer_au_cours_suivant.<br />";
				$temoin_reg_cdt++;
			}
		}
	}

	if($temoin_erreur_cdt>0) {
		$message_cdt.="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	elseif($temoin_reg_cdt>0) {
		$message_cdt.="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
}


if(isset($_POST['validation_form_abs_prof'])) {
	check_token();

	if(isset($_POST['AbsProf_jamais_remplacer'])) {
		$valeur='y';
	}
	else {
		$valeur='n';
	}

	if(!savePref($_SESSION['login'],'AbsProf_jamais_remplacer',$valeur)) {
		$msg.="Erreur lors de l'enregistrement de AbsProf_jamais_remplacer.<br />";
		$message_mod_abs_prof="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	else {
		$msg.="Enregistrement de AbsProf_jamais_remplacer.<br />";
		$message_mod_abs_prof="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
}

if(isset($_POST['mod_discipline_travail_par_defaut'])) {
	check_token();

	$message_mod_discipline="";
	$nb_reg=0;
	if(!savePref($_SESSION['login'],'mod_discipline_travail_par_defaut',traitement_magic_quotes($_POST['mod_discipline_travail_par_defaut']))) {
		$msg.="Erreur lors de l'enregistrement de mod_discipline_travail_par_defaut.<br />";
		$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	else {
		$msg.="Enregistrement de mod_discipline_travail_par_defaut.<br />";
		//$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		$nb_reg++;
	}

	//debug_var();
	/*
	$_POST['mod_disc_mail_cat_incluse']=	Array (*)
		$_POST[mod_disc_mail_cat_incluse]['0']=	2
		$_POST[mod_disc_mail_cat_incluse]['1']=	5
		$_POST[mod_disc_mail_cat_incluse]['2']=	6
	$_POST['mod_disc_mail_cat_incluse_NC']=	y

	for($loop=0;$loop<count($mod_disc_mail_cat_incluse);$loop++) {
		
	}
	*/
	$chaine="";
	$mod_disc_mail_cat_incluse=isset($_POST['mod_disc_mail_cat_incluse']) ? $_POST['mod_disc_mail_cat_incluse'] : array();

	$sql="SELECT * FROM s_categories ORDER BY categorie;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->id, $mod_disc_mail_cat_incluse)) {
				$chaine.="|".$lig->id;
			}
		}
		$chaine.="|";

		if(!savePref($_SESSION['login'],'mod_discipline_natures_exclues_mail', $chaine)) {
			$msg.="Erreur lors de l'enregistrement de mod_discipline_natures_exclues_mail.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de mod_discipline_natures_exclues_mail.<br />";
			//$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
			$nb_reg++;
		}
	}


	if(isset($_POST['mod_disc_mail_cat_incluse_NC'])) {
		$value="n";
	}
	else {
		$value="y";
	}

	if(!savePref($_SESSION['login'],'mod_discipline_natures_non_categorisees_exclues_mail', $value)) {
		$msg.="Erreur lors de l'enregistrement de mod_discipline_natures_non_categorisees_exclues_mail.<br />";
		$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	else {
		$msg.="Enregistrement de mod_discipline_natures_non_categorisees_exclues_mail.<br />";
		//$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		$nb_reg++;
	}

	if($_SESSION['statut']=='professeur') {
		if(isset($_POST['DiscTemoinIncidentProf'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentProf', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentProf.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentProf.<br />";
			//$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
			$nb_reg++;
		}

		if(isset($_POST['DiscTemoinIncidentPP'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentPP', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentPP.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentPP.<br />";
			//$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
			$nb_reg++;
		}
	}
	elseif($_SESSION['statut']=='cpe') {
		if(isset($_POST['DiscTemoinIncidentCpe'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentCpe', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentCpe.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentCpe.<br />";
			$nb_reg++;
		}

		if(isset($_POST['DiscTemoinIncidentCpeTous'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentCpeTous', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentCpeTous.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentCpeTous.<br />";
			$nb_reg++;
		}
	}
	elseif($_SESSION['statut']=='scolarite') {
		if(isset($_POST['DiscTemoinIncidentScol'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentScol', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentScol.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentScol.<br />";
			$nb_reg++;
		}

		if(isset($_POST['DiscTemoinIncidentScolTous'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentScolTous', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentScolTous.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentScolTous.<br />";
			$nb_reg++;
		}
	}
	elseif($_SESSION['statut']=='administrateur') {
		if(isset($_POST['DiscTemoinIncidentAdmin'])) {
			$value="y";
		}
		else {
			$value="n";
		}
		if(!savePref($_SESSION['login'],'DiscTemoinIncidentAdmin', $value)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentAdmin.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Enregistrement de DiscTemoinIncidentAdmin.<br />";
			$nb_reg++;
		}
	}

	if(isset($_POST['DiscTemoinIncidentTaille'])) {
		$value=preg_replace("/[^0-9]/", "", $_POST['DiscTemoinIncidentTaille']);
		if(($value!=$_POST['DiscTemoinIncidentTaille'])||($value<10)) {
			$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentTaille : valeur ".$_POST['DiscTemoinIncidentTaille']." invalide.<br />";
			$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			if(!savePref($_SESSION['login'],'DiscTemoinIncidentTaille', $value)) {
				$msg.="Erreur lors de l'enregistrement de DiscTemoinIncidentTaille.<br />";
				$message_mod_discipline="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
			}
			else {
				$msg.="Enregistrement de DiscTemoinIncidentTaille.<br />";
				$nb_reg++;
			}
		}
	}

	if(($message_mod_discipline=="")&&($nb_reg>0)) {
		$message_mod_discipline="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
}

//===== Discipline : Professeur limite à ses groupes
if ($_SESSION['statut'] === 'professeur') {
	$moduleDiscipline = filter_input(INPUT_POST, 'module') === 'discipline' ? TRUE : FALSE;
	$limiteAGroupe = filter_input(INPUT_POST, 'limiteAGroupe') ? filter_input(INPUT_POST, 'limiteAGroupe') : "n";
	
	if ($moduleDiscipline && $limiteAGroupe) {
		savePref($_SESSION['login'], 'limiteAGroupe', $limiteAGroupe);
		$msg.="Module discipline -> ";
		if ($limiteAGroupe === 'y') {
			$msg.="seul les courriels de vos groupes vous seront envoyés.<br />";
		} else {
			$msg.="vous recevrez tous les courriels des classes dont vous avez des élèves.<br />";
		}
		
	}
}



$tab_statuts_barre=array('professeur', 'cpe', 'scolarite', 'administrateur');
$modifier_barre=isset($_POST['modifier_barre']) ? $_POST['modifier_barre'] : NULL;
if((isset($modifier_barre))&&(in_array($_SESSION['statut'], $tab_statuts_barre))) {
	check_token();

	$afficher_menu=isset($_POST['afficher_menu']) ? $_POST['afficher_menu'] : NULL;
	if(!savePref($_SESSION['login'], 'utiliserMenuBarre', $afficher_menu)) {
		$msg.="Erreur lors de la sauvegarde de la préférence d'affichage de la barre de menu.<br />\n";
		$message_modifier_barre="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
	else {
		$msg.="Sauvegarde de la préférence d'affichage de la barre de menu effectuée.<br />\n";
		$message_modifier_barre="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
	}
}



if(isset($_POST['choix_encodage_csv'])) {
	check_token();

	if(in_array($_POST['choix_encodage_csv'],array("ascii", "utf-8", "windows-1252"))) {
		if(!savePref($_SESSION['login'], 'choix_encodage_csv', $_POST['choix_encodage_csv'])) {
			$msg.="Erreur lors de la sauvegarde de la préférence d'encodage des fichiers CSV.<br />\n";
			$message_choixEncodageCsv="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Sauvegarde de la préférence d'encodage des fichiers CSV effectuée.<br />\n";
			$message_choixEncodageCsv="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
	}
}

if(isset($_POST['output_mode_pdf'])) {
	check_token();

	if(in_array($_POST['output_mode_pdf'],array("D", "I"))) {
		if(!savePref($_SESSION['login'], 'output_mode_pdf', $_POST['output_mode_pdf'])) {
			$msg.="Erreur lors de la sauvegarde de la préférence d'export PDF.<br />\n";
			$message_output_mode_pdf="<p style='color:red'>Erreur lors de l'enregistrement&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
		else {
			$msg.="Sauvegarde de la préférence sur les fichiers PDF effectuée.<br />\n";
			$message_output_mode_pdf="<p style='color:green'>Enregistrement effectué&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S').".</p>\n";
		}
	}
}


if (isset($_POST['modifier_hauteur_entete'])) {
	check_token();

	$reglage = isset($_POST['header_bas']) ? $_POST['header_bas'] : 'n';

	//echo "savePref(".$_SESSION['login'].", 'petit_entete', $reglage)<br />";
	if (savePref($_SESSION['login'], 'petit_entete', $reglage)) {
		$message_hauteur_header = "<p style='color: green;'>Modification enregistrée&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="Hauteur de l'entête enregistrée.<br />";
	}else{
		$message_hauteur_header = "<p style='color: red;'>Impossible d'enregistrer la modification&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="Erreur lors de l'enregistrement de la hauteur de l'entête.<br />";
	}
}




//===== Discipline : CPE peut changer le déclarant

if (isset($_POST['autorise_cpe_declarant'])) {
    check_token();
    
    $autorisation= isset($_POST['cpePeuChanger']) ? $_POST['cpePeuChanger'] : 'no';
	if (savePref($_SESSION['login'], 'cpePeuChanger', $autorisation)) {
		$message_autorise_cpe = "<p style='color: green;'>Modification enregistrée&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="État de l'autorisation pour le CPE de vous changer en déclarant d'incident enregistrée.<br />";
	}else{
		$message_autorise_cpe = "<p style='color: red;'>Impossible d'enregistrer la modification&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="Erreur lors de l'enregistrement de l'état de l'autorisation pour le CPE de vous changer en déclarant d'incident.<br />";
	}
}




if (!isset($niveau_arbo)) {$niveau_arbo = 1;}

if ($niveau_arbo == "0") {
	$chemin_sound="./sounds/";
} elseif ($niveau_arbo == "1") {
	$chemin_sound="../sounds/";
} elseif ($niveau_arbo == "2") {
	$chemin_sound="../../sounds/";
} elseif ($niveau_arbo == "3") {
	$chemin_sound="../../../sounds/";
}
$tab_sound=get_tab_file($chemin_sound);

if((count($tab_sound)>0)&&(isset($_POST['footer_sound']))&&(((in_array($_POST['footer_sound'],$tab_sound))&&(preg_match('/\.wav/i',$_POST['footer_sound']))&&(file_exists($chemin_sound.$_POST['footer_sound'])))|| $_POST['footer_sound']=='')) {
	check_token();

	if(!savePref($_SESSION['login'],'footer_sound',$_POST['footer_sound'])) {
		$msg.="Erreur lors de l'enregistrement de l'alerte sonore de fin de session.<br />";
		$message_footer_sound = "<p style='color: red;'>Impossible d'enregistrer la modification&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
	}
	else {
		$msg.="Enregistrement de l'alerte sonore de fin de session effectué.<br />";
		$message_footer_sound = "<p style='color: green;'>Modification enregistrée&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
	}
}

if (isset($_POST['AlertesAvecSon'])) {
	check_token();

	$autorisation= isset($_POST['AlertesAvecSon']) ? $_POST['AlertesAvecSon'] : 'y';
	if (savePref($_SESSION['login'], 'AlertesAvecSon', $autorisation)) {
		$message_AlertesAvecSon = "<p style='color: green;'>Modification enregistrée&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="État de l'autorisation d'accompagnement sonore des alertes enregistré.<br />";
	}else{
		$message_AlertesAvecSon = "<p style='color: red;'>Impossible d'enregistrer la modification&nbsp;: ".strftime('%d/%m/%Y à %H:%M:%S')."</p>";
		$msg.="Erreur lors de l'enregistrement de l'état de l'autorisation d'accompagnement sonore des alertes.<br />";
	}
}

if (isset($_POST['ajout_fichier_signature'])) {
	check_token();

	$tab_signature=get_tab_signature_bull();

	$sign_file = isset($_FILES["sign_file"]) ? $_FILES["sign_file"] : NULL;

	$msg_tmp="";
	$temoin_erreur_sign=0;
	if((isset($sign_file))&&((!isset($sign_file['error']))||($sign_file['error']!=4))) {
		if((!preg_match("/\.jpeg$/i", $sign_file['name']))&&(!preg_match("/\.jpg$/i", $sign_file['name']))) {
			$msg_tmp.= "Seule l'extension JPG est autorisée.<br />";
			$temoin_erreur_sign++;
		}
		else {
			if(!check_user_temp_directory($_SESSION['login'], 1)) {
				$msg_tmp.= "Votre dossier temporaire ne peut pas être créé ou n'est pas accessible en écriture.<br />";
				$temoin_erreur_sign++;
			}
			else {
				$dirname=get_user_temp_directory($_SESSION['login']);
				if((!$dirname)||($dirname=="")) {
					$msg_tmp.= "Votre dossier temporaire n'existe pas ou n'est pas accessible en écriture.<br />";
					$temoin_erreur_sign++;
				}
				else {
					$tmp_dim_img=getimagesize($sign_file['tmp_name']);
					if((isset($tmp_dim_img[2]))&&($tmp_dim_img[2]==2)) {
						$dirname="../temp/".$dirname."/signature";

						if(!file_exists($dirname)) {
							mkdir($dirname);
							if ($f = @fopen("$dirname/index.html", "w")) {
								@fputs($f, '<html><head><script type="text/javascript">
		document.location.replace("../../../login.php")
	</script></head></html>');
								@fclose($f);
							}
						}

						if(!file_exists($dirname)) {
							$msg_tmp.= "Il n'a pas été possible de créer un dossier 'signature' dans votre dossier temporaire.<br />";
							$temoin_erreur_sign++;
						}
						else {
							$ok = false;
							if ($f = @fopen("$dirname/.test", "w")) {
								@fputs($f, '<'.'?php $ok = true; ?'.'>');
								@fclose($f);
								include("$dirname/.test");
							}

							//$msg_tmp.=$dirname."<br />";

							if (!$ok) {
								$msg_tmp.= "Problème d'écriture sur votre répertoire temporaire.<br />Veuillez signaler ce problème à l'administrateur du site.<br />";
								$temoin_erreur_sign++;
							} else {
								if (file_exists($dirname."/".$sign_file['name'])) {
									@unlink($dirname."/".$sign_file['name']);
									$sql="DELETE FROM signature_fichiers WHERE fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."' AND login='".$_SESSION['login']."';";
									$menage=mysqli_query($GLOBALS["mysqli"], $sql);
									$msg_tmp.= "Un fichier de même nom existait pour cet utilisateur.<br />Le fichier précédent a été supprimé.<br />";
								}
								$ok = @copy($sign_file['tmp_name'], $dirname."/".$sign_file['name']);
								if (!$ok) {$ok = @move_uploaded_file($sign_file['tmp_name'], $dirname."/".$sign_file['name']);}
								if (!$ok) {
									$msg_tmp.= "Problème de transfert : le fichier n'a pas pu être transféré dans votre répertoire temporaire.<br />Veuillez signaler ce problème à l'administrateur du site<br />.";
									$temoin_erreur_sign++;
								}
								else {
									$msg_tmp.= "Le fichier a été transféré.<br />";

									// Par précaution, pour éviter des blagues avec des scories...
									$sql="DELETE FROM signature_fichiers WHERE fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."' AND login='".$_SESSION['login']."';";
									$menage=mysqli_query($GLOBALS["mysqli"], $sql);

									$sql="INSERT INTO signature_fichiers SET login='".$_SESSION['login']."', fichier='".mysqli_real_escape_string($GLOBALS["mysqli"], $sign_file['name'])."';";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$insert) {
										$msg_tmp.="Erreur lors de l'enregistrement dans la table 'signature_fichiers'.<br />";
										$temoin_erreur_sign++;
									}
								}
							}
						}
					}
					else {
						$msg_tmp.= "Le type de l'image est incorrect.<br />";
						$temoin_erreur_sign++;
					}
				}
			}
		}
	}
	if($msg_tmp!="") {
		$msg.=$msg_tmp;
		if($temoin_erreur_sign>0) {
			$message_signature_bulletins_ajout="<span style='color:red'>".$msg_tmp."</span>";
		}
		else {
			$message_signature_bulletins_ajout="<span style='color:green'>".$msg_tmp."</span>";
		}
	}


	// Association classe/fichier:
	// Il faut faire l'association avant la suppression pour éviter des erreurs.
	$msg_tmp="";
	$temoin_erreur_sign=0;
	$fich_sign_classe = isset($_POST["fich_sign_classe"]) ? $_POST["fich_sign_classe"] : array();
	foreach($fich_sign_classe as $id_classe => $id_fichier) {
		if(array_key_exists($id_classe, $tab_signature['classe'])) {
			if($id_fichier!=$tab_signature['classe'][$id_classe]['id_fichier']) {
				if(($id_fichier!=-1)&&(!array_key_exists($id_fichier, $tab_signature['fichier']))) {
					$msg_tmp.="Le fichie de signature n°$id_fichier, pour peu qu'il existe, ne vous appartient pas.<br />";
					$temoin_erreur_sign++;
				}
				else {
					$sql="UPDATE signature_classes SET id_fichier='".$id_fichier."' WHERE id_classe='$id_classe' AND login='".$_SESSION['login']."';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if($update) {
						if($id_fichier==-1) {
							$msg_tmp.="Suppression de l'association de fichier signature avec la classe ".get_nom_classe($id_classe)." effectuée.<br />";
						}
						else {
							$msg_tmp.="Association du fichier de signature n°$id_fichier avec la classe ".get_nom_classe($id_classe)." effectuée.<br />";
						}
					}
					else {
						$msg_tmp.="Erreur lors de l'association du fichier de signature n°$id_fichier avec la classe ".get_nom_classe($id_classe)."<br />";
						$temoin_erreur_sign++;
					}
				}
			}
		}
		else {
			$msg_tmp.="Vous n'avez pas le droit d'associer un fichier de signature à la classe ".get_nom_classe($id_classe)."<br />";
			$temoin_erreur_sign++;
		}
	}
	if($msg_tmp!="") {
		$msg.=$msg_tmp;
		if($temoin_erreur_sign>0) {
			$message_signature_bulletins_assoc_fichier_classe="<span style='color:red'>".$msg_tmp."</span>";
		}
		else {
			$message_signature_bulletins_assoc_fichier_classe="<span style='color:green'>".$msg_tmp."</span>";
		}
	}


	// Suppression de fichier
	$msg_tmp="";
	$cpt_suppr=0;
	$cpt_fich_suppr=0;
	$temoin_erreur_sign=0;
	$suppr_fichier = isset($_POST["suppr_fichier"]) ? $_POST["suppr_fichier"] : array();
	for($loop=0;$loop<count($suppr_fichier);$loop++) {
		$sql="SELECT * FROM signature_fichiers WHERE id_fichier='".$suppr_fichier[$loop]."' AND login='".$_SESSION['login']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);

			$dirname=get_user_temp_directory($_SESSION['login']);
			$fichier_courant="../temp/".$dirname."/signature/".$lig->fichier;
			if(($dirname)&&($dirname!="")&&(file_exists($fichier_courant))) {
				$menage=unlink($fichier_courant);
				if(!$menage) {
					$msg_tmp.="Erreur lors de la suppression du fichier $fichier_courant<br />";
					$temoin_erreur_sign++;
				}
				else {
					$cpt_fich_suppr++;
				}
			}

			if(isset($tab_signature['fichier'][$suppr_fichier[$loop]]['id_classe'])) {
				for($loop2=0;$loop2<count($tab_signature['fichier'][$suppr_fichier[$loop]]['id_classe']);$loop2++) {
					$sql="UPDATE signature_classes WHERE SET id_fichier='-1' WHERE login='".$_SESSION['login']."' AND id_classe='".$tab_signature['fichier'][$suppr_fichier[$loop]]['id_classe'][$loop2]."';";
					$menage2=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}

			$sql="DELETE FROM signature_fichiers WHERE id_fichier='".$suppr_fichier[$loop]."';";
			$menage=mysqli_query($GLOBALS["mysqli"], $sql);
			if($menage) {
				$cpt_suppr++;
			}
			else {
				$msg_tmp.="Erreur lors de la suppression de l'enregistrement concernant $fichier_courant<br />";
				$temoin_erreur_sign++;
			}
		}
		else {
			$msg_tmp.="Le fichier n°".$suppr_fichier[$loop]." ne vous appartient pas.<br />";
			$temoin_erreur_sign++;
		}
	}
	if($cpt_suppr>0) {
		$msg_tmp.="$cpt_suppr enregistrement(s) supprimé(s).<br />";
	}
	if($cpt_fich_suppr>0) {
		$msg_tmp.="$cpt_fich_suppr fichier(s) supprimé(s).<br />";
	}
	if($msg_tmp!="") {
		$msg.=$msg_tmp;
		if($temoin_erreur_sign>0) {
			$message_signature_bulletins_suppr="<span style='color:red'>".$msg_tmp."</span>";
		}
		else {
			$message_signature_bulletins_suppr="<span style='color:green'>".$msg_tmp."</span>";
		}
	}

	// Par précaution:
	$sql="UPDATE signature_classes SET id_fichier='-1' WHERE login='".$_SESSION['login']."' AND id_fichier NOT IN (SELECT id_fichier FROM signature_fichiers);";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);
}

// On appelle les informations de l'utilisateur pour les afficher :
$call_user_info = mysqli_query($GLOBALS["mysqli"], "SELECT nom,prenom,statut,email,show_email,civilite FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
$user_civilite = old_mysql_result($call_user_info, "0", "civilite");
$user_nom = old_mysql_result($call_user_info, "0", "nom");
$user_prenom = old_mysql_result($call_user_info, "0", "prenom");
$user_statut = old_mysql_result($call_user_info, "0", "statut");
$user_email = old_mysql_result($call_user_info, "0", "email");
$user_show_email = old_mysql_result($call_user_info, "0", "show_email");

//**************** EN-TETE *****************
$titre_page = "Gérer son compte";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

// On initialise un flag pour savoir si l'utilisateur est 'éditable' ou non.
// Cela consiste à déterminer s'il s'agit d'un utilisateur local ou LDAP, et dans
// ce dernier cas à savoir s'il s'agit d'un accès en écriture ou non.
if ($session_gepi->current_auth_mode == "gepi" || $session_gepi->current_auth_mode == "simpleSAML" || $gepiSettings['ldap_write_access'] == "yes") {
	$editable_user = true;
	$affiche_bouton_submit = 'yes';
} else {
	$editable_user = false;
	$affiche_bouton_submit = 'no';
}

echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if(($_SESSION['statut']!='administrateur')&&(getSettingAOui("AccesFicheBienvenue".ucfirst($_SESSION['statut'])))) {
	echo " | <a href=\"./impression_bienvenue.php\" target='_blank'>Imprimer ma fiche Bienvenue</a>";
}
echo "</p>\n";
echo "<form enctype=\"multipart/form-data\" action=\"mon_compte.php\" method=\"post\">\n";

$tabindex=1;

echo "<fieldset id='infosPerso' style='border: 1px solid grey;";
echo "background-image: url(\"../images/background/opacite50.png\"); ";
echo "'>\n";
echo "<legend style='border: 1px solid grey;";
//echo "background-image: url(\"../images/background/opacite50.png\"); ";
echo "background-color: white; ";
echo "'>Informations personnelles</legend>\n";

if($_SESSION['statut']=='responsable') {
	echo "<div style='float:right; width:8em; text-align:center;' class='fieldset_opacite50'>
	<a href='../responsables/infos_parent.php' title=\"Permet de consulter les informations vous concernant (coordonnées téléphoniques, adresse,...) dont dispose l'établissement.\" target='_blank'>Informations personnelles (<em>coordonnées</em>)</a>
</div>";
}

echo add_token_field();
echo "<h2>Informations personnelles *</h2>\n";


if ($session_gepi->current_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
?>
    <p>
        <span style='color: red;'>Note :</span>
        les modifications de mot de passe et d'email que vous effectuerez sur cette page seront propagées à l'annuaire central, 
        et donc aux autres services qui y font appel.
    </p>
<?php
}
?>
    <table summary='Mise en forme'>
        <tr>
            <td>
                <table summary='Infos'>
                    <tr>
                        <td>Identifiant GEPI : </td>
                        <td><?php echo $_SESSION['login']; ?></td>
                    </tr>
                    <tr>
                        <td>Civilité : </td>
                        <td>
<?php
	if(($_SESSION['statut']=='professeur')||
		($_SESSION['statut']=='scolarite')||
		($_SESSION['statut']=='cpe')) {
?>
                            <select name='reg_civilite' onchange='changement()' <?php echo "tabindex='$tabindex'";$tabindex++;?>>
                                <option value='M.'<?php if ($user_civilite=='M.') {echo " selected='selected' ";} ?> >M.</option>
                                <option value='Mme'<?php if ($user_civilite=='Mme') {echo " selected='selected' ";} ?> >Mme</option>
                                <option value='Mlle'<?php if ($user_civilite=='Mlle') {echo " selected='selected' ";} ?> >Mlle</option>
                            </select>
<?php
	}
	else {
?>
                            <?php echo $user_civilite; ?>
<?php
	}
?>
                        </td>
                    </tr>
                    <tr>
                        <td>Nom : </td>
                        <td><?php echo $user_nom ?></td>
                    </tr>
                    <tr>
                        <td>Prénom : </td>
                        <td><?php echo $user_prenom ?></td>
                    </tr>
<?php
	if($_SESSION['statut']=='eleve') {
		$sql="SELECT naissance, lieu_naissance FROM eleves WHERE login='".$_SESSION['login']."';";
		$res_nais=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_nais)>0) {
			$user_naissance=old_mysql_result($res_nais, 0, "naissance");
			echo "
                    <tr>
                        <td>Date de naissance : </td>
                        <td>".formate_date($user_naissance)."</td>
                    </tr>";

			if(getSettingAOui('ele_lieu_naissance')) {
				$code_lieu_naissance=old_mysql_result($res_nais, 0, "lieu_naissance");
				$sql="SELECT * FROM communes WHERE code_commune_insee='$code_lieu_naissance';";
				$res_nais=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_nais)>0) {
					$lieu_naissance=old_mysql_result($res_nais, 0, "commune")." (".old_mysql_result($res_nais, 0, "departement").")";
					echo "
                    <tr>
                        <td>Lieu de naissance : </td>
                        <td>".$lieu_naissance."</td>
                    </tr>";
				}
			}
		}
	}

	if (($editable_user)&&
		((($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable'))||
		(getSettingValue('mode_email_resp')!='sconet'))) {
?>
                    <tr>
                        <td>
                            <a name='saisie_mail'></a>
                            Email : 
                        </td>
                        <td>
                            <input type=text 
                                   name=reg_email 
                                   size=30
                                   <?php if ($user_email) { echo " value=\"".$user_email."\"";} ?>
                                    <?php echo " tabindex='$tabindex'";$tabindex++;?>
                                   />
                                   <?php
                                       if((isset($_GET['saisie_mail_requise']))&&($_GET['saisie_mail_requise']=='yes')) {
                                           echo "<p><span style='font-weight:bold; color:red; text-decoration:blink;'>Une adresse mail valide est requise !</span></p>";
                                           if(!isset($_GET['change_mdp'])) {echo "<script type='text/javascript'>alert('Une adresse mail valide est requise !')</script>";}
                                       }
                                   ?>
                        </td>
                    </tr>
                                   
<?php
	} else {
?>
                    <tr>
                        <td>
                            <a name='saisie_mail'></a>
                            Email : 
                        </td>
                        <td>
                            <?php echo $user_email ?>
                            <input type="hidden" name="reg_email" value="<?php echo $user_email ?>" />
                            <?php
                                if((getSettingValue('cas_attribut_email')!='')&&(getSettingValue('sso_url_portail')!='')) {
                                    echo " <a href='".getSettingValue('sso_url_portail')."' title=\"Vous pouvez renseigner/modifier votre adresse de courriel là : ".getSettingValue('sso_url_portail')."\" target='_blank'><img src='../images/icons/ico_question.png' width='19' height='19' /></a>";
                                }

                               if((isset($_GET['saisie_mail_requise']))&&($_GET['saisie_mail_requise']=='yes')) {
                                   echo "<p><span style='color:red; text-decoration:blink;'>Une adresse mail valide est requise</span></p>";
                               }
                            ?>
                        </td>
                    </tr>
<?php
	}
	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
		$affiche_bouton_submit = 'yes';
		echo "<tr><td></td><td><label for='reg_show_email' style='cursor: pointer;'><input type='checkbox' name='reg_show_email' id='reg_show_email' value='yes'";
		if ($user_show_email == "yes") echo " CHECKED";
		echo " tabindex='$tabindex'";
		$tabindex++;
		echo "/> Autoriser l'affichage de mon adresse email<br />pour les utilisateurs non personnels de l'établissement **</label></td></tr>\n";
	}
	echo "<tr><td>Statut : </td><td>".statut_accentue($user_statut)."</td></tr>\n";
	echo "</table>\n";
echo "</td>\n";

// PHOTO
echo "<td valign='top' align='center'>\n";
if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='scolarite')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='professeur')||
($_SESSION['statut']=='eleve')
) {
	$user_login=$_SESSION['login'];


	if((($_SESSION['statut']=='eleve')&&(getSettingValue("active_module_trombinoscopes")=='y'))||
		(($_SESSION['statut']!='eleve')&&(getSettingValue("active_module_trombino_pers")=='y'))) {





		$GepiAccesModifMaPhoto='GepiAccesModifMaPhoto'.ucfirst(mb_strtolower($_SESSION['statut']));

		if($_SESSION['statut']=='eleve') {
			$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
			$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_elenoet)==0) {
				echo "</td></tr></table>\n";
				echo "<p><b>ERREUR !</b> Votre statut d'élève ne semble pas être confirmé dans la table 'eleves'.</p>\n";
				// A FAIRE
				// AJOUTER UNE ALERTE INTRUSION
				require("../lib/footer.inc.php");
				die();
			}
			$lig_tmp_elenoet=mysqli_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			if($reg_no_gep!="") {
				// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
				$photo=nom_photo($reg_no_gep);

				//echo "<td align='center'>\n";
				$temoin_photo="non";
				//if("$photo"!="") {
				if($photo) {
					if(file_exists($photo)) {
						$temoin_photo="oui";
						// la photo sera réduite si nécessaire
						$dimphoto=dimensions_affichage_photo($photo,getSettingValue('l_max_aff_trombinoscopes'),getSettingValue('h_max_aff_trombinoscopes'));
						echo "<div>\n";




						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px;" alt="Ma photo" />';


						echo "</div>\n";
						echo "<div style='clear:both;'></div>\n";
					}
				}

				// Cas particulier des élèves pour une gestion plus fine avec les AIDs
				if ((getSettingValue("GepiAccesModifMaPhotoEleve")=='yes') and ($_SESSION['statut']=='eleve')) {
					// Une catégorie d'AID pour accès au trombino existe-t-elle ?
					if (getSettingValue("num_aid_trombinoscopes")!='') {
						// L'AID existe t-elle ?
						$test1 = sql_query1("select count(indice_aid) from aid_config where indice_aid='".getSettingValue("num_aid_trombinoscopes")."'");
						if ($test1!="0") {
							$test_eleve = sql_query1("select count(login) from j_aid_eleves where login='".$_SESSION['login']."' and indice_aid='".getSettingValue("num_aid_trombinoscopes")."'");
						}
						else {
							$test_eleve = "1";
						}
					} else {
						$test_eleve = "1";
					}
				}

				if ((getSettingValue($GepiAccesModifMaPhoto)=='yes') and ($test_eleve!=0)) {
					$affiche_bouton_submit ='yes';
					echo "<div>\n";
					//echo "<span id='lien_photo' style='font-size:xx-small;'>";
					echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px; width:300px;'>";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='';return false;\">";
					if($temoin_photo=="oui") {
						//echo "Modifier le fichier photo</a>\n";
						echo "Modifier le fichier photo</a>\n";
					}
					else {
						//echo "Envoyer un fichier photo</a>\n";
						echo "Envoyer un fichier photo</a>\n";
					}
					//echo "</span>\n";
					echo "</div>\n";
					echo "<div id='div_upload_photo' style='display:none; width:400px;'>";
					echo "<input type='file' name='filephoto' size='30' tabindex='$tabindex' />\n";
					$tabindex++;
					echo "<input type='submit' name='Envoi_photo' value='Envoyer' tabindex='$tabindex' />\n";
					$tabindex++;
					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span style='font-size:x-small;'><b>Remarque : </b>Les photographies sont automatiquement redimensionnées (largeur : ".getSettingValue("l_resize_trombinoscopes")." pixels, hauteur : ".getSettingValue("h_resize_trombinoscopes")." pixels). Afin que votre photographie ne soit pas trop réduite, les dimensions de celle-ci (respectivement largeur et hauteur) doivent être de préférence proportionnelles à ".getSettingValue("l_resize_trombinoscopes")." et ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span style='font-size:x-small;'>Les photos doivent de plus être au format JPEG avec l'extension '<strong>.jpg</strong>'.</span>";
					}

					if("$photo"!="") {
						if(file_exists($photo)) {
							echo "<br />\n";
							//echo "<input type='checkbox' name='suppr_filephoto' value='y' /> Supprimer la photo existante\n";
							echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' tabindex='$tabindex' />\n";
							$tabindex++;
							echo "&nbsp;<label for='suppr_filephoto' style='cursor: pointer; cursor: hand;'>Supprimer la photo existante</label>\n";
						}
					}
					echo "</div>\n";
					echo "</div>\n";
				}
				//echo "</td>\n";
			}

		}
		else {
			/*echo "<table summary='Photo'>\n";
			echo "<tr>\n";
			echo "<td>\n";*/

				// En multisite, on ajoute le répertoire RNE
				if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
					// On récupère le RNE de l'établissement
					$repertoire="../photos/".$_COOKIE['RNE']."/personnels/";
				}
				else{
					$repertoire="../photos/personnels/";
				}

				$code_photo = md5(mb_strtolower($user_login));

				$photo=$repertoire.$code_photo.".jpg";
				$temoin_photo="non";
				if(file_exists($photo)) {
					$temoin_photo="oui";
					echo "<div>\n";
					// la photo sera réduite si nécessaire
					$dimphoto=dimensions_affichage_photo($photo,getSettingValue('l_max_aff_trombinoscopes'),getSettingValue('h_max_aff_trombinoscopes'));
					echo "<div>\n";

					echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px;" alt="Ma photo" />';
					echo "</div>\n";
					echo "<div style='clear:both;'></div>\n";
				}
				if(getSettingValue($GepiAccesModifMaPhoto)=='yes') {
					$affiche_bouton_submit ='yes';
					echo "<div>\n";
					echo "<span style='font-size:small;'>\n";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';return false;\">\n";
					if($temoin_photo=="oui") {
						echo "Modifier le fichier photo</a>\n";
					}
					else {
						echo "Envoyer un fichier photo</a>\n";
					}

					echo "<div id='div_upload_photo' style='display: none; width:400px;'>\n";
					echo "<input type='file' name='filephoto' size='30' tabindex='$tabindex' />\n";
					$tabindex++;

					echo "<input type='submit' name='Envoi_photo' value='Envoyer' tabindex='$tabindex' />\n";
					$tabindex++;

					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span style='font-size:x-small;'><b>Remarque : </b>Les photographies sont automatiquement redimensionnées (largeur : ".getSettingValue("l_resize_trombinoscopes")." pixels, hauteur : ".getSettingValue("h_resize_trombinoscopes")." pixels). Afin que votre photographie ne soit pas trop réduite, les dimensions de celle-ci (respectivement largeur et hauteur) doivent être de préférence proportionnelles à ".getSettingValue("l_resize_trombinoscopes")." et ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span style='font-size:x-small;'>Les photos doivent de plus être au format JPEG avec l'extension '<strong>.jpg</strong>'.</span>";
					}
					echo "<br />\n";
					echo "<span style='text-align:right'>";
					echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' tabindex='$tabindex' />\n";
					$tabindex++;
					echo "&nbsp;<label for='suppr_filephoto' style='cursor: pointer; cursor: hand; '>Supprimer la photo existante</label>\n";
					echo "</span>\n";
					echo "</span>\n";
					echo "</div>\n";
					echo "</div>\n";
				}

			/*echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";*/
		}

	}
}
echo "</td>\n";
echo "</table>\n";
if ($affiche_bouton_submit=='yes') {
	echo "<p><input type='submit' value='Enregistrer' tabindex='$tabindex' /></p>\n";
	$tabindex++;
}

$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
if (empty($groups)) {
	echo "<br /><br />\n";
} else {
	echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
	echo "<ul>\n";
	foreach($groups as $group) {
		echo "<li><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
		echo "" . htmlspecialchars($group["description"]);
		echo "</span>";
		echo "</li>\n";
	}
	echo "</ul>\n";

	// Matière principale:
	$sql="SELECT DISTINCT jpm.id_matiere, m.nom_complet FROM j_professeurs_matieres jpm, matieres m WHERE (jpm.id_professeur='".$_SESSION["login"]."' AND m.matiere=jpm.id_matiere) ORDER BY m.nom_complet;";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb=mysqli_num_rows($test);
	//echo "\$nb=$nb<br />";
	if ($nb>1) {
		echo "Matière principale&nbsp;: <select name='matiere_principale' tabindex='$tabindex'>\n";
		$tabindex++;
		while($lig_mat=mysqli_fetch_object($test)) {
			echo "<option value='$lig_mat->id_matiere'";
			if($lig_mat->id_matiere==$_SESSION['matiere']) {echo " selected='selected'";}
			echo ">$lig_mat->nom_complet</option>\n";
		}
		echo "</select>\n";
		echo "<br />\n";
	}
}

$call_prof_classe = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
$nombre_classe = mysqli_num_rows($call_prof_classe);
if ($nombre_classe != "0") {
	$j = "0";
	echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>\n";
	echo "<ul>\n";
	while ($j < $nombre_classe) {
		$id_classe = old_mysql_result($call_prof_classe, $j, "id");
		$classe_suivi = old_mysql_result($call_prof_classe, $j, "classe");
		echo "<li><b>$classe_suivi</b></li>\n";
		$j++;
	}
	echo "</ul>\n";
}
?>
<p class='small'>
    * Toutes les données nominatives présentes dans la base GEPI et vous concernant vous sont communiquées sur cette page.
    <br />
    Conformément à la loi française n° 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
    vous pouvez demander auprès du Chef d'établissement ou auprès de l'<a href="mailto:<?php echo getSettingValue("gepiAdminAdress")?>">
    administrateur</a> du site, la rectification de ces données.
    <br />
    Les rectifications sont effectuées dans les 48 heures hors week-end et jours fériés qui suivent la demande.
</p>
<?php 
if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
?>
<p class='small'>
    ** Votre email sera affichée sur certaines pages seulement si leur affichage a été activé de manière globale par l'administrateur 
    et si vous avez autorisé l'affichage de votre email en cochant la case appropriée.
    <br />
    Dans l'hypothèse où vous autorisez l'affichage de votre email, celle-ci ne sera accessible que par les élèves que vous avez en classe 
    et/ou leurs responsables légaux disposant d'un identifiant pour se connecter à Gepi.
</p>
<?php 
}

//==========================================
if(getSettingAOui('MonCompteAfficheInfo'.ucfirst($_SESSION['statut']))) {
	echo "<hr />
<a hame='MonCompteAfficheInfo'></a>
<h2>Information ".$_SESSION['statut']."</h2>

".getSettingValue('MonCompteInfo'.ucfirst($_SESSION['statut']));
}
//==========================================

// Changement du mot de passe
if ($editable_user) {
?>
<hr />
<a name="changemdp"></a>
<H2>Changement du mot de passe</H2>
<p>
    <strong>
        Attention : le mot de passe doit comporter <?php echo getSettingValue("longmin_pwd") ;?> caractères minimum.
<?php 
	if ($flag == 1) {
?>
        Il doit comporter au moins une lettre, au moins un chiffre et au moins un caractère spécial (#, *,...)
<?php 
        } else {
?>
        Il doit comporter au moins une lettre et au moins un chiffre.
<?php 
        }
?>
        <br />
        <span style='color: red;'>Il est fortement conseillé de ne pas choisir un mot de passe trop simple</span>
    </strong>
    .<br />
    <strong>
        Votre mot de passe est strictement personnel, vous ne devez pas le diffuser,
        <span style='color: red;'> il garantit la sécurité de votre travail.</span>
    </strong>
</p>

<script type="text/javascript" src="../lib/pwd_strength.js"></script>

<table summary='Mot de passe'>
    <tr>
        <td>Ancien mot de passe : </td>
        <td><input type='password' name='no_anti_inject_password_a' id='no_anti_inject_password_a' size='20' tabindex='<?php echo $tabindex;$tabindex++;?>' /><?php echo input_password_to_text('no_anti_inject_password_a');?></td>
    </tr>
    <tr>
        <td>Nouveau mot de passe (<em><?php echo getSettingValue("longmin_pwd") ;?> caractères minimum</em>) :</td>
        <td>
            <input id="mypassword" 
                    type="password" 
                    name="no_anti_inject_password1" 
                    size="20" 
                    onkeyup="runPassword(this.value, 'mypassword');" 
                    tabindex='<?php echo $tabindex;$tabindex++;?>' />
                    <?php
                        // Cela merdoie: Il doit y avoir un conflit entre le test de solidité et le changement de type.
                        echo input_password_to_text('mypassword');
                    ?>
        </td>
        <td>
            Complexité de votre mot de passe : 
            <div style="width: 150px;">
                <div id="mypassword_text" style="font-size: 11px;"></div>
                <div id="mypassword_bar" style="font-size: 1px; height: 3px; width: 0px; border: 1px solid white;"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>Nouveau mot de passe (<em>à confirmer</em>) : </td>
        <td><input type='password' name='reg_password2' id='reg_password2' size='20' tabindex='<?php echo $tabindex;$tabindex++;?>' /><?php echo input_password_to_text('reg_password2');?></td>
    </tr>
</table>
<?php
	if ((isset($_GET['retour'])) or (isset($_POST['retour'])))
?>
<p><input type="hidden" name="retour" value="accueil" /></p>

<?php
}
if ($affiche_bouton_submit=='yes')
	echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center>\n";
	$tabindex++;
	echo "<input type=\"hidden\" name=\"valid\" value=\"yes\" />\n";
echo "</fieldset>\n";
echo "</form>\n";
//echo "  <hr />\n";
echo "<br/>\n";

//==============================================================================


function cellule_checkbox($prof_login,$item,$num,$special){
	global $tabindex;

	echo "<td align='center'";
	echo " id='td_".$item."_".$num."' ";
	$checked="";
	$coche="";
	$sql="SELECT * FROM preferences WHERE login='$prof_login' AND name='$item'";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0){
		$lig_test=mysqli_fetch_object($test);
		if($lig_test->value=="y"){
			echo " class='coche'";
			$checked=" checked";
			$coche="y";
		}
		else{
			echo " class='decoche'";
			$coche="n";
		}
	}
	echo ">";
	echo "<input type='checkbox' name='$item"."_"."$num' id='$item"."_"."$num' value='y' tabindex='$tabindex'";
	$tabindex++;

	echo $checked;
	echo " onchange=\"changement_et_couleur('$item"."_"."$num','";
	if($special!=''){
		$chaine_td="td_nomprenom_".$num."_".$special;
		echo $chaine_td;
	}
	echo "');\"";
	echo " />";

	if($special!=''){
		if($coche=="y"){
			echo "<script type='text/javascript'>
//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgreen';
document.getElementById('$chaine_td').style.backgroundColor='lightgreen';
</script>\n";
		}
		elseif($coche=="n"){
			echo "<script type='text/javascript'>
//document.getElementById('td_nomprenom_'+$num).style.backgroundColor='lightgray';
document.getElementById('$chaine_td').style.backgroundColor='lightgray';
</script>\n";
		}
	}

	echo "</td>\n";
}

//==============================================================================

if (in_array($_SESSION['statut'], array("scolarite", "administrateur"))) {
	// 20160304
	$checked_accueil_tableau_ouverture_periode_y=(getPref($_SESSION['login'], "accueil_tableau_ouverture_periode", "y")=="y") ? " checked" : "";
	$checked_accueil_tableau_ouverture_periode_n=(getPref($_SESSION['login'], "accueil_tableau_ouverture_periode", "y")=="n") ? " checked" : "";
	$checked_accueil_tableau_acces_app_bull_ele_resp_y=(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")=="y") ? " checked" : "";
	$checked_accueil_tableau_acces_app_bull_ele_resp_n=(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")=="n") ? " checked" : "";

	echo "<a name='tableaux_ouverture_periode_et_acces'></a>
<form name='form_ouverture_periode_et_acces' method='post' action='".$_SERVER['PHP_SELF']."#tableaux_ouverture_periode_et_acces'>
	<fieldset class='fieldset_opacite50'>
		<legend style='border: 1px solid grey; background-color: white; '>Tableau d'ouverture/accès en page d'accueil</legend>
		".add_token_field()."
		<input type='hidden' name='valide_ouverture_periode_et_acces' value='y' />

		<p><input type='radio' name='accueil_tableau_ouverture_periode' id='accueil_tableau_ouverture_periode_y' value='y' tabindex='$tabindex'$checked_accueil_tableau_ouverture_periode_y onchange=\"checkbox_change('accueil_tableau_ouverture_periode_y');checkbox_change('accueil_tableau_ouverture_periode_n');changement()\" /><label for='accueil_tableau_ouverture_periode_y' id='texte_accueil_tableau_ouverture_periode_y'> Afficher en page d'accueil le tableau de l'état d'ouverture/verrouillage des périodes pour mes classes.</label><br />";
	$tabindex++;
	echo "
		<input type='radio' name='accueil_tableau_ouverture_periode' id='accueil_tableau_ouverture_periode_n' value='n' tabindex='$tabindex'$checked_accueil_tableau_ouverture_periode_n onchange=\"checkbox_change('accueil_tableau_ouverture_periode_y');checkbox_change('accueil_tableau_ouverture_periode_n');changement()\"/><label for='accueil_tableau_ouverture_periode_n' id='texte_accueil_tableau_ouverture_periode_n'> Ne pas afficher en page d'accueil le tableau de l'état d'ouverture/verrouillage des périodes pour mes classes.</label></p>";
	$tabindex++;

	if((getSettingAOui('active_bulletins'))&&
		((getSettingValue("acces_app_ele_resp")=="manuel")||(getSettingValue("acces_app_ele_resp")=="manuel_individuel"))) {
		echo "
		<p style='margin-top:1em;'><input type='radio' name='accueil_tableau_acces_app_bull_ele_resp' id='accueil_tableau_acces_app_bull_ele_resp_y' value='y' tabindex='$tabindex'$checked_accueil_tableau_acces_app_bull_ele_resp_y onchange=\"checkbox_change('accueil_tableau_acces_app_bull_ele_resp_y');checkbox_change('accueil_tableau_acces_app_bull_ele_resp_n');changement()\" /><label for='accueil_tableau_acces_app_bull_ele_resp_y' id='texte_accueil_tableau_acces_app_bull_ele_resp_y'> Afficher en page d'accueil le tableau de l'état de visibilité des appréciations et avis du conseil de classe sur les bulletins.</label><br />";
		$tabindex++;
		echo "
		<input type='radio' name='accueil_tableau_acces_app_bull_ele_resp' id='accueil_tableau_acces_app_bull_ele_resp_n' value='n' tabindex='$tabindex'$checked_accueil_tableau_acces_app_bull_ele_resp_n onchange=\"checkbox_change('accueil_tableau_acces_app_bull_ele_resp_y');checkbox_change('accueil_tableau_acces_app_bull_ele_resp_n');changement()\" /><label for='accueil_tableau_acces_app_bull_ele_resp_n' id='texte_accueil_tableau_acces_app_bull_ele_resp_n'> Ne pas afficher en page d'accueil le tableau de l'état de visibilité des appréciations et avis du conseil de classe sur les bulletins.</label></p>";
		$tabindex++;
	}

	echo "
	<p><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center></p>".((isset($message_ouverture_periode_et_acces)) ? "<br />".$message_ouverture_periode_et_acces : "")."
	</fieldset>
</form>
<br />\n";
	$tabindex++;
}
elseif(($_SESSION['statut']=='professeur')&&
	(getSettingAOui('active_bulletins'))&&
	((getSettingValue("acces_app_ele_resp")=="manuel")||
	(getSettingValue("acces_app_ele_resp")=="manuel_individuel"))&&
	(is_pp($_SESSION['login']))) {
	$checked_accueil_tableau_acces_app_bull_ele_resp_y=(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")=="y") ? " checked" : "";
	$checked_accueil_tableau_acces_app_bull_ele_resp_n=(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")=="n") ? " checked" : "";

	echo "<a name='tableaux_ouverture_periode_et_acces'></a>
<form name='form_ouverture_periode_et_acces' method='post' action='".$_SERVER['PHP_SELF']."#tableaux_ouverture_periode_et_acces'>
	<fieldset class='fieldset_opacite50'>
		<legend style='border: 1px solid grey; background-color: white; '>Tableau d'ouverture/accès en page d'accueil</legend>
		".add_token_field()."
		<input type='hidden' name='valide_ouverture_periode_et_acces' value='y' />

		<p style='margin-top:1em;'><input type='radio' name='accueil_tableau_acces_app_bull_ele_resp' id='accueil_tableau_acces_app_bull_ele_resp_y' value='y' tabindex='$tabindex'$checked_accueil_tableau_acces_app_bull_ele_resp_y onchange=\"checkbox_change('accueil_tableau_acces_app_bull_ele_resp_y');checkbox_change('accueil_tableau_acces_app_bull_ele_resp_n');changement()\" /><label for='accueil_tableau_acces_app_bull_ele_resp_y' id='texte_accueil_tableau_acces_app_bull_ele_resp_y'> Afficher en page d'accueil le tableau de l'état de visibilité des appréciations et avis du conseil de classe sur les bulletins.</label><br />";
		$tabindex++;
		echo "
		<input type='radio' name='accueil_tableau_acces_app_bull_ele_resp' id='accueil_tableau_acces_app_bull_ele_resp_n' value='n' tabindex='$tabindex'$checked_accueil_tableau_acces_app_bull_ele_resp_n onchange=\"checkbox_change('accueil_tableau_acces_app_bull_ele_resp_y');checkbox_change('accueil_tableau_acces_app_bull_ele_resp_n');changement()\" /><label for='accueil_tableau_acces_app_bull_ele_resp_n' id='texte_accueil_tableau_acces_app_bull_ele_resp_n'> Ne pas afficher en page d'accueil le tableau de l'état de visibilité des appréciations et avis du conseil de classe sur les bulletins.</label></p>";
		$tabindex++;

	echo "
	<p><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center></p>".((isset($message_ouverture_periode_et_acces)) ? "<br />".$message_ouverture_periode_et_acces : "")."
	</fieldset>
</form>
<br />\n";
	$tabindex++;
}

//==============================================================================

if(($_SESSION['statut']=='responsable')||($_SESSION['statut']=='eleve')) {
	$pref_accueil_simpl=getPref($_SESSION['login'],'accueil_simpl', "y");
	$checked_accueil_simpl_y=($pref_accueil_simpl=="y") ? " checked" : "";
	$checked_accueil_simpl_n=($pref_accueil_simpl=="y") ? "" : " checked";

	echo "<a name='accueil_simpl'></a>
<form name='form_accueil_simpl' method='post' action='".$_SERVER['PHP_SELF']."#accueil_simpl'>
	<fieldset class='fieldset_opacite50'>
		<legend style='border: 1px solid grey; background-color: white; '>Page d'accueil simplifiée</legend>
		".add_token_field()."
		<input type='hidden' name='valide_accueil_simpl' value='y' />

		<p><input type='radio' name='accueil_simpl' id='accueil_simpl_y' value='y' tabindex='$tabindex'$checked_accueil_simpl_y /><label for='accueil_simpl_y'>Utiliser la page d'accueil \"simplifiée\"</label><br />";
	$tabindex++;
	echo "
		<input type='radio' name='accueil_simpl' id='accueil_simpl_n' value='n' tabindex='$tabindex'$checked_accueil_simpl_n /><label for='accueil_simpl_n'>Utiliser la page d'accueil avec le menu classique</label></p>";
	$tabindex++;

	echo "
	<p><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center></p>".((isset($message_accueil_simpl)) ? "<br />".$message_accueil_simpl : "")."
	</fieldset>
</form>
<br />\n";
	$tabindex++;
}

//==============================================================================

if($_SESSION['statut']=='professeur') {
	// 20121128
	$nom_ou_description_groupe_barre_h=getPref($_SESSION['login'], "nom_ou_description_groupe_barre_h", "name");
	$nom_ou_description_groupe_cdt=getPref($_SESSION['login'], "nom_ou_description_groupe_cdt", "name");

	echo "<a name='nom_ou_description_groupe'></a>
<form name='form_nom_ou_description_groupe' method='post' action='".$_SERVER['PHP_SELF']."#nom_ou_description_groupe'>\n";
	echo add_token_field();
	echo "
	<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Dénomination des groupes</legend>
			<input type='hidden' name='valide_nom_ou_description_groupe' value='y' />
			<p>Vous pouvez choisir d'afficher le Nom ou la Description des enseignements/groupes dans différents modules&nbsp;:<br />

				Barre de menu horizontale (<em>si elle est affichée</em>)&nbsp;: 
				<input type='radio' name='nom_ou_description_groupe_barre_h' id='nom_ou_description_groupe_barre_h_name' value='name' ".($nom_ou_description_groupe_barre_h=='name' ? "checked " : "")." tabindex='$tabindex' /><label for='nom_ou_description_groupe_barre_h_name'>Nom</label> - ";
	$tabindex++;
	echo "
				<input type='radio' name='nom_ou_description_groupe_barre_h' id='nom_ou_description_groupe_barre_h_description' value='description' ".($nom_ou_description_groupe_barre_h=='description' ? "checked " : "")." tabindex='$tabindex' /><label for='nom_ou_description_groupe_barre_h_description'>Description</label>
				<br />";
	$tabindex++;
	echo "
				Cahiers de textes&nbsp;: 
				<input type='radio' name='nom_ou_description_groupe_cdt' id='nom_ou_description_groupe_cdt_name' value='name' ".($nom_ou_description_groupe_cdt=='name' ? "checked " : "")." tabindex='$tabindex' /><label for='nom_ou_description_groupe_cdt_name'>Nom</label> - ";
	$tabindex++;
	echo "
				<input type='radio' name='nom_ou_description_groupe_cdt' id='nom_ou_description_groupe_cdt_description' value='description' ".($nom_ou_description_groupe_cdt=='description' ? "checked " : "")." tabindex='$tabindex' /><label for='nom_ou_description_groupe_cdt_description'>Description</label>
				<br />";
	$tabindex++;
	echo "

			</p>

			<p style='text-align:center;'><input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' /></p>\n";

	$tabindex++;

	if(isset($message_nom_ou_description_groupe)) {echo $message_nom_ou_description_groupe;}

	echo "
	</fieldset>
</form>
<br/>\n";

	//============================================================

	//============================================================
	// La page d'accueil simplifiée pour les profs n'est proposée que si un des modules Notes, CDT, Trombinoscope est activé
	if((getSettingAOui('active_carnets_notes'))||(acces_cdt())||(getSettingAOui('active_module_trombinoscopes'))) {
		echo "<a name='accueil_simpl_prof'></a><form name='form_accueil_simpl_prof' method='post' action='".$_SERVER['PHP_SELF']."#accueil_simpl_prof'>\n";
		echo add_token_field();
		echo "<fieldset style='border: 1px solid grey;";
		echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "'>\n";
		echo "<legend style='border: 1px solid grey;";
		//echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "background-color: white; ";
		echo "'>Page d'accueil simplifiée</legend>\n";

		echo "<input type='hidden' name='valide_accueil_simpl_prof' value='y' />\n";

		//echo "<p>Paramétrage de la page d'<b>accueil</b> simplifiée.</p>\n";

		//echo "<div style='margin-left:3em;'>\n";
		$tabchamps=array('accueil_simpl','accueil_infobulles','accueil_ct','accueil_trombino','accueil_cn','accueil_bull','accueil_visu','accueil_liste_pdf');

		//echo "<table border='1'>\n";
		echo "<table class='boireaus' border='1' summary='Préférences professeurs'>\n";

		// 1ère ligne
		//$lignes_entete="<tr style='background-color: white;'>\n";
		$lignes_entete="<tr class='entete'>\n";
		$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
		$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
		$lignes_entete.="<th rowspan='2'>Afficher les infobulles</th>\n";
		$lignes_entete.="<th colspan='6'>Afficher les liens pour</th>\n";
		$lignes_entete.="</tr>\n";

		// 2ème ligne
		//$lignes_entete.="<tr style='background-color: white;'>\n";
		$lignes_entete.="<tr class='entete'>\n";
		$lignes_entete.="<th>le Cahier de textes</th>\n";
		$lignes_entete.="<th>le Trombinoscope</th>\n";
		$lignes_entete.="<th>le Carnet de notes</th>\n";
		$lignes_entete.="<th>les notes et appréciations des Bulletins</th>\n";
		$lignes_entete.="<th>la Visualisation des graphes et bulletins simplifiés</th>\n";
		$lignes_entete.="<th>les Listes PDF des élèves</th>\n";
		$lignes_entete.="</tr>\n";

		echo $lignes_entete;

		$i=0;

		echo "<tr>\n";

		echo "<td id='td_nomprenom_0_accueil_simpl'>";
		echo my_strtoupper($_SESSION['nom'])." ".casse_mot($_SESSION['prenom'],'majf2');
		echo "</td>\n";

		$j=0;
		cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'accueil_simpl');
		for($j=1;$j<count($tabchamps);$j++){
			cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'');
		}

		echo "</tr>\n";

		echo "</table>\n";


		echo "<p class='bold' style='margin-top:1em;'>Vous pouvez trier/masquer des enseignements en page d'accueil simplifiée&nbsp;:</p>";
		$tab_grp_order=array();
		$tab_grp_hidden=array();
		$sql="SELECT * FROM preferences WHERE login='".$_SESSION['login']."' AND name LIKE 'accueil_simpl_id_groupe_order_%' ORDER BY value;";
		$res_grp_order=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp_order)>0) {
			while($lig_grp_order=mysqli_fetch_object($res_grp_order)) {
				$tmp_id_groupe=preg_replace("/^accueil_simpl_id_groupe_order_/", "", $lig_grp_order->name);
				if($lig_grp_order->value=='-1') {
					$tab_grp_hidden[]=$tmp_id_groupe;
				}
				else {
					$tab_grp_order[]=$tmp_id_groupe;
				}
			}
		}

		// On passe en revue les groupes qui ont été triés dans Mon compte
		$cpt=1;
		if(count($tab_grp_order)>0) {
			echo "<p style='margin-left:3em;text-indent:-2em;'>Les groupes suivants sont triés&nbsp;:</p>
<table class='boireaus boireaus_alt'>";
			for($loop=0;$loop<count($tab_grp_order);$loop++) {
				for($i=0;$i<count($groups);$i++) {
					if($groups[$i]['id']==$tab_grp_order[$loop]) {

						echo "
	<tr>
		<td>
			<span id='texte_accueil_simpl_afficher_grp_$cpt'>".$groups[$i]['name']." (<em style='font-size:small'>".$groups[$i]['name']." en ".$groups[$i]['classlist_string'];
						if(count($groups[$i]['profs']['list'])>1) {
							echo " avec ".$groups[$i]['profs']['proflist_string'];
						}
						echo "</em>)"."</span>
					<input type='hidden' name='accueil_simpl_afficher_grp[$cpt]' value='".$groups[$i]['id']."' />
		</td>
		<td>
					<select name='accueil_simpl_afficher_grp_rang[$cpt]'>
						<option value='-1'>Ne pas afficher ce groupe</option>";
						for($loop2=1;$loop2<count($groups)+1;$loop2++) {
							if($loop2==$cpt) {
								$selected=" selected";
							}
							else {
								$selected="";
							}
							echo "
						<option value='$loop2'$selected>$loop2</option>";
						}
						echo "
					</select>
		</td>
	</tr>";
						break;
					}
				}
				$cpt++;
			}
			echo "</table>";
		}

		// Les groupes qui n'ont pas été triés dans Mon compte et pas cachés non plus
		$temoin_grp_non_encore_trie="n";
		for($i=0;$i<count($groups);$i++) {
			if((!in_array($groups[$i]['id'], $tab_grp_order))&&(!in_array($groups[$i]['id'], $tab_grp_hidden))) {
				if($temoin_grp_non_encore_trie=="n") {
					echo "<p style='margin-left:3em;text-indent:-2em;'>L'ordre ou le masquage des groupes suivants n'a pas encore été défini (<em>ils seront affichés</em>)&nbsp;:</p>
<table class='boireaus boireaus_alt'>";
					$temoin_grp_non_encore_trie="y";
				}
				echo "
	<tr>
		<td>
			<span id='texte_accueil_simpl_afficher_grp_$cpt'>".$groups[$i]['name']." (<em style='font-size:small'>".$groups[$i]['name']." en ".$groups[$i]['classlist_string'];
				if(count($groups[$i]['profs']['list'])>1) {
					echo " avec ".$groups[$i]['profs']['proflist_string'];
				}
				echo "</em>)"."</span>
			<input type='hidden' name='accueil_simpl_afficher_grp[$cpt]' value='".$groups[$i]['id']."' />
		</td>
		<td>
			<select name='accueil_simpl_afficher_grp_rang[$cpt]'>
				<option value='-1' selected>Ne pas afficher ce groupe</option>";
				for($loop2=1;$loop2<count($groups)+1;$loop2++) {
						if($loop2==$cpt) {
						$selected=" selected";
					}
					else {
						$selected="";
					}
					echo "
				<option value='$loop2'$selected>$loop2</option>";
				}
				echo "
			</select>
		</td>
	</tr>";
				$cpt++;
			}
		}
		if($temoin_grp_non_encore_trie=="y") {
			echo "</table>";
		}

		// Les groupes cachés
		if(count($tab_grp_hidden)>0) {
			echo "<p style='margin-left:3em;text-indent:-2em;'>Les groupes suivants sont actuellement cachés en page d'accueil simplifiée&nbsp;:</p>
<table class='boireaus boireaus_alt'>";
			for($loop=0;$loop<count($tab_grp_hidden);$loop++) {
				for($i=0;$i<count($groups);$i++) {
					if($groups[$i]['id']==$tab_grp_hidden[$loop]) {

						echo "
	<tr>
		<td>
			<span id='texte_accueil_simpl_afficher_grp_$cpt'>".$groups[$i]['name']." (<em style='font-size:small'>".$groups[$i]['name']." en ".$groups[$i]['classlist_string'];
						if(count($groups[$i]['profs']['list'])>1) {
							echo " avec ".$groups[$i]['profs']['proflist_string'];
						}
						echo "</em>)"."</span>
					<input type='hidden' name='accueil_simpl_afficher_grp[$cpt]' value='".$groups[$i]['id']."' />
		</td>
		<td>
					<select name='accueil_simpl_afficher_grp_rang[$cpt]'>
						<option value='-1' selected>Ne pas afficher ce groupe</option>";
						for($loop2=1;$loop2<count($groups)+1;$loop2++) {
							echo "
						<option value='$loop2'>$loop2</option>";
						}
						echo "
					</select>
		</td>
	</tr>";
						break;
					}
				}
				$cpt++;
			}
			echo "</table>";
		}

		// A FAIRE : Tri et affichage ou non des AID












		echo "<p style='text-align:center;'>\n";
		echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
		$tabindex++;
		echo "</p>\n";

		if(isset($message_accueil_simpl_prof)) {echo $message_accueil_simpl_prof;}

		echo "</fieldset>\n";
		echo "</form>\n";
		//echo "  <hr />\n";
		echo "<br/>\n";

		//echo "</div>\n";
	}
}
//==============================================================================

if ((getSettingValue('active_carnets_notes')!='n')&&($_SESSION["statut"] == "professeur")) {
	echo "<a name='carnets_notes'></a><form name='form_carnets_notes' method='post' action='".$_SERVER['PHP_SELF']."#carnets_notes'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Carnets de notes</legend>\n";

	echo "<input type='hidden' name='valide_form_cn' value='y' />\n";

	//echo "<p><b>Paramètres du carnet de notes&nbsp;:</b></p>\n";

	//echo "<div style='margin-left:3em;'>\n";
	$aff_quartiles_cn=getPref($_SESSION['login'], 'aff_quartiles_cn', 'n');
	echo "<p>\n";
	echo "<input type='checkbox' name='aff_quartiles_cn' id='aff_quartiles_cn' value='y' ";
	echo "onchange=\"checkbox_change('aff_quartiles_cn');changement()\" ";
	if($aff_quartiles_cn=='y') {echo 'checked';}
	echo " tabindex='$tabindex'";
	$tabindex++;
	echo " /><label for='aff_quartiles_cn' id='texte_aff_quartiles_cn'> Afficher par défaut l'infobulle contenant les moyenne, médiane, quartiles, min, max sur les carnets de notes.</label>\n";
	echo "</p>\n";

	$aff_photo_cn=getPref($_SESSION['login'], 'aff_photo_cn', 'n');
	echo "<p>\n";
	echo "<input type='checkbox' name='aff_photo_cn' id='aff_photo_cn' value='y' ";
	echo "onchange=\"checkbox_change('aff_photo_cn');changement()\" ";
	if($aff_photo_cn=='y') {echo 'checked';}
	echo " tabindex='$tabindex'";
	$tabindex++;
	echo " /><label for='aff_photo_cn' id='texte_aff_photo_cn'> Afficher par défaut la photo des élèves sur les carnets de notes.</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	$cn_avec_min_max=getPref($_SESSION['login'], 'cn_avec_min_max', 'y');
	echo "<input type='checkbox' name='cn_avec_min_max' id='cn_avec_min_max' value='y' ";
	echo "onchange=\"checkbox_change('cn_avec_min_max');changement()\" ";
	if($cn_avec_min_max=='y') {echo 'checked';}
	echo " tabindex='$tabindex'";
	$tabindex++;
	echo " /><label for='cn_avec_min_max' id='texte_cn_avec_min_max'> Afficher pour chaque colonne de notes les valeurs minimale et maximale.</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	$cn_avec_mediane_q1_q3=getPref($_SESSION['login'], 'cn_avec_mediane_q1_q3', 'y');
	echo "<input type='checkbox' name='cn_avec_mediane_q1_q3' id='cn_avec_mediane_q1_q3' value='y' ";
	echo "onchange=\"checkbox_change('cn_avec_mediane_q1_q3');changement()\" ";
	if($cn_avec_mediane_q1_q3=='y') {echo 'checked';}
	echo " tabindex='$tabindex'";
	$tabindex++;
	echo " /><label for='cn_avec_mediane_q1_q3' id='texte_cn_avec_mediane_q1_q3'> Afficher pour chaque colonne de notes les valeur médiane, 1er et 3è quartiles.</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	$cn_avec_mediane_q1_q3=getPref($_SESSION['login'], 'cn_avec_sup10', 'y');
	echo "<input type='checkbox' name='cn_avec_sup10' id='cn_avec_sup10' value='y' ";
	echo "onchange=\"checkbox_change('cn_avec_sup10');changement()\" ";
	if($cn_avec_mediane_q1_q3=='y') {echo 'checked';}
	echo " tabindex='$tabindex'";
	$tabindex++;
	echo " /><label for='cn_avec_sup10' id='texte_cn_avec_sup10'> Afficher pour chaque colonne de notes le nombre de notes supérieures ou égales à 10 et le nombre de notes inférieures à 10.</label>\n";
	echo "</p>\n";

	echo "<p>Dans la page de saisie des notes de devoirs, trier par défaut <br />\n";
	$cn_order_by=getPref($_SESSION['login'], 'cn_order_by', 'classe');
	echo "<input type='radio' name='cn_order_by' id='cn_order_by_classe' value='classe' ";
	echo "onchange=\"checkbox_change('cn_order_by_classe');checkbox_change('cn_order_by_nom');changement()\" ";
	if($cn_order_by=='classe') {echo 'checked';}
	echo " tabindex='$tabindex'";
	echo " /><label for='cn_order_by_classe' id='texte_cn_order_by_classe'>par classe puis ordre alphabétique des noms des élèves.</label><br />\n";
	$tabindex++;

	echo "<input type='radio' name='cn_order_by' id='cn_order_by_nom' value='nom' ";
	echo "onchange=\"checkbox_change('cn_order_by_classe');checkbox_change('cn_order_by_nom');changement()\" ";
	if($cn_order_by=='nom') {echo 'checked';}
	echo " tabindex='$tabindex'";
	echo " /><label for='cn_order_by_nom' id='texte_cn_order_by_nom'>par ordre alphabétique des noms des élèves.</label><br />\n";
	$tabindex++;
	echo "</p>\n";

	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo "Nom court par défaut des évaluations&nbsp;: \n";
	echo "</td>";
	echo "<td>";
	$cn_default_nom_court=getPref($_SESSION['login'], 'cn_default_nom_court', 'Nouvelle évaluation');
	echo "<input type='text' name='cn_default_nom_court' id='cn_default_nom_court' value='$cn_default_nom_court' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>";
	echo "Nom complet par défaut des évaluations&nbsp;: \n";
	echo "</td>";
	echo "<td>";
	$cn_default_nom_complet=getPref($_SESSION['login'], 'cn_default_nom_complet', 'Nouvelle évaluation');
	echo "<input type='text' name='cn_default_nom_complet' id='cn_default_nom_complet' value='$cn_default_nom_complet' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>";
	echo "Coefficient par défaut des évaluations&nbsp;: \n";
	$cn_default_coef=getPref($_SESSION['login'], 'cn_default_coef', '1.0');
	echo "</td>";
	echo "<td>";
	echo "<input type='text' name='cn_default_coef' id='cn_default_coef' value='$cn_default_coef' size='3' onkeydown=\"clavier_2(this.id,event,1,20);\" autocomplete='off' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</td>";
	echo "</tr>";

	echo "</table>";

	//===========================================================

	echo "<br />\n";

	echo "<p>Paramétrage de la page de <b>création d'évaluation</b></p>\n";
	echo "<div style='margin-left:3em;'>\n";
	if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
		$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_note_autre_que_referentiel','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite', 'add_modif_dev_display_parents', 'add_modif_dev_display_parents_app');
	} else {
		$tabchamps=array( 'add_modif_dev_simpl','add_modif_dev_nom_court','add_modif_dev_nom_complet','add_modif_dev_description','add_modif_dev_coef','add_modif_dev_date','add_modif_dev_date_ele_resp','add_modif_dev_boite', 'add_modif_dev_display_parents', 'add_modif_dev_display_parents_app');
	}
	//echo "<table border='1'>\n";
	echo "<table class='boireaus' border='1' summary='Préférences professeurs'>\n";

	// 1ère ligne
	$lignes_entete="<tr class='entete'>\n";
	$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
	$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
	if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
		$lignes_entete.="<th colspan='10'>Afficher les champs</th>\n";
	} else {
		$lignes_entete.="<th colspan='9'>Afficher les champs</th>\n";
	}
	$lignes_entete.="</tr>\n";

	// 2ème ligne
	//$lignes_entete.="<tr style='background-color: white;'>\n";
	$lignes_entete.="<tr class='entete'>\n";
	$lignes_entete.="<th>Nom court</th>\n";
	$lignes_entete.="<th>Nom complet</th>\n";
	$lignes_entete.="<th>Description</th>\n";
	$lignes_entete.="<th>Coefficient</th>\n";
	if(getSettingValue("note_autre_que_sur_referentiel")=="V") {
		$lignes_entete.="<th>Note autre que sur le referentiel</th>\n";
	}
	$lignes_entete.="<th>Date</th>\n";
	$lignes_entete.="<th>Date ele/resp</th>\n";
	$lignes_entete.="<th>".casse_mot(getSettingValue("gepi_denom_boite"),'majf2')."</th>\n";
	$lignes_entete.="<th title=\"Visibilité ou non de l'évaluation sur le relevé de notes\">Vis.CN</th>\n";
	$lignes_entete.="<th title=\"Visibilité ou non du commentaire/appréciation sur le relevé de notes\">Vis.App.CN</th>\n";
	$lignes_entete.="</tr>\n";

	echo $lignes_entete;

	echo "<tr>\n";

	echo "<td id='td_nomprenom_0_add_modif_dev'>";
	echo my_strtoupper($_SESSION['nom'])." ".casse_mot($_SESSION['prenom'],'majf2');
	//echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
	echo "</td>\n";

	$j=0;
	cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'add_modif_dev');
	for($j=1;$j<count($tabchamps);$j++){
		cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'');
	}

	echo "</tr>\n";

	echo "</table>\n";
	echo "</div>\n";

	//========================================================

	echo "<br />\n";

	echo "<p>Paramétrage de la page de <b>création de ".casse_mot(getSettingValue("gepi_denom_boite"),'majf2')."</b></p>\n";
	echo "<div style='margin-left:3em;'>\n";

	$tabchamps=array('add_modif_conteneur_simpl','add_modif_conteneur_nom_court','add_modif_conteneur_nom_complet','add_modif_conteneur_description','add_modif_conteneur_coef','add_modif_conteneur_boite','add_modif_conteneur_aff_display_releve_notes','add_modif_conteneur_aff_display_bull');

	//echo "<table border='1'>\n";
	echo "<table class='boireaus' border='1' summary='Préférences professeurs'>\n";

	// 1ère ligne
	//$lignes_entete.="<tr style='background-color: white;'>\n";
	$lignes_entete="<tr class='entete'>\n";
	if($_SESSION['statut']!='professeur'){
		$lignes_entete.="<th rowspan='3'>".$gepiSettings['denomination_professeur']."</th>\n";
	}
	else{
		$lignes_entete.="<th rowspan='2'>".$gepiSettings['denomination_professeur']."</th>\n";
	}
	$lignes_entete.="<th rowspan='2'>Utiliser l'interface simplifiée</th>\n";
	$lignes_entete.="<th colspan='7'>Afficher les champs</th>\n";
	if($_SESSION['statut']!='professeur') {$lignes_entete.="<th rowspan='3'>Tout cocher / décocher</th>\n";}
	$lignes_entete.="</tr>\n";

	// 2ème ligne
	//$lignes_entete.="<tr style='background-color: white;'>\n";
	$lignes_entete.="<tr class='entete'>\n";
	$lignes_entete.="<th>Nom court</th>\n";
	$lignes_entete.="<th>Nom complet</th>\n";
	$lignes_entete.="<th>Description</th>\n";
	$lignes_entete.="<th>Coefficient</th>\n";
	$lignes_entete.="<th>".casse_mot(getSettingValue("gepi_denom_boite"),'majf2')."</th>\n";
	$lignes_entete.="<th>Afficher sur le relevé de notes</th>\n";
	$lignes_entete.="<th>Afficher sur le bulletin</th>\n";
	$lignes_entete.="</tr>\n";

	echo $lignes_entete;

	echo "<tr>\n";

	echo "<td id='td_nomprenom_0_add_modif_conteneur'>";
	echo my_strtoupper($_SESSION['nom'])." ".casse_mot($_SESSION['prenom'],'majf2');
	//echo "<input type='hidden' name='prof[$i]' value='".$prof[$i]['login']."' />";
	echo "</td>\n";

	$j=0;
	cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'add_modif_conteneur');
	for($j=1;$j<count($tabchamps);$j++){
		cellule_checkbox($_SESSION['login'],$tabchamps[$j],0,'');
	}

	echo "</tr>\n";

	echo "</table>\n";
	echo "</div>\n";

	$cnBoitesModeMoy=getPref($_SESSION['login'], 'cnBoitesModeMoy', '');
	echo "<p><br /></p>
<a name='cnBoitesModeMoy'></a>
<p>Mode de calcul <strong title='Vous pourrez effectuer un autre choix pour certains carnets de notes en suivant le lien Configuration dans votre carnet de notes.'>par défaut</strong> des moyennes de carnets de notes dans le cas où vous créez des ".getSettingValue("gepi_denom_boite")."s&nbsp;:</p>
<div style='margin-left:3em;'>

<input type='radio' name='cnBoitesModeMoy' id='cnBoitesModeMoy_1' value='1' ";
	if($cnBoitesModeMoy=='1') {echo "checked ";}
	echo "tabindex='$tabindex' ";
	$tabindex++;
	echo "/><label for='cnBoitesModeMoy_1'>la moyenne s'effectue sur toutes les notes contenues à la racine et dans les ".my_strtolower(getSettingValue("gepi_denom_boite"))."s sans tenir compte des options définies dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label><br />

<input type='radio' name='cnBoitesModeMoy' id='cnBoitesModeMoy_2' value='2' ";
	if($cnBoitesModeMoy=='2') {echo "checked ";}
	echo "tabindex='$tabindex' ";
	$tabindex++;
	echo "/><label for='cnBoitesModeMoy_2'>la moyenne s'effectue sur toutes les notes contenues à la racine et sur les moyennes des ".my_strtolower(getSettingValue("gepi_denom_boite"))."s en tenant compte des options dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.</label><br />

<p style='margin-left:2em;'><em>Explication&nbsp;:</em></p>
<div style='margin-left:7em;'>";
	include("../cahier_notes/explication_moyenne_boites.php");
	echo "</div>
</div>

<p><br /></p>\n";

	echo "<p style='text-align:center;'>\n";
	echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</p>\n";

	if(isset($message_cn)) {echo $message_cn;}

	echo "</fieldset>\n";
	echo "</form>\n";
	//echo "  <hr />\n";
	echo "<br/>\n";
}

//==============================================================================

if($_SESSION["statut"] == "professeur") {
	echo "<a name='bulletins'></a><form name='form_carnets_notes' method='post' action='".$_SERVER['PHP_SELF']."#bulletins'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Bulletins</legend>\n";

	echo "<input type='hidden' name='valide_form_bull' value='y' />\n";

	$aff_photo_saisie_app=getPref($_SESSION['login'], 'aff_photo_saisie_app', 'n');
	
	echo "<p>\n";
	echo "<input type='checkbox' name='aff_photo_saisie_app' id='aff_photo_saisie_app' value='y' ";
	echo "onchange=\"checkbox_change('aff_photo_saisie_app');changement()\" ";
	if($aff_photo_saisie_app=='y') {echo 'checked';}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " /><label for='aff_photo_saisie_app' id='texte_aff_photo_saisie_app'> Afficher par défaut les photos des élèves lors de la saisie des appréciations sur les bulletins.</label>\n";
	echo "</p>\n";


	$saisie_app_nb_cols_textarea=getPref($_SESSION["login"],'saisie_app_nb_cols_textarea',100);
	echo "<p>\n";
	echo "<label for='saisie_app_nb_cols_textarea'> Largeur en nombre de colonnes des champs de saisie des appréciations sur les bulletins&nbsp;: </label>\n";
	echo "<input type='text' name='saisie_app_nb_cols_textarea' id='saisie_app_nb_cols_textarea' value='$saisie_app_nb_cols_textarea' ";
	echo "onchange=\"changement()\" ";
	echo "size='3' onkeydown=\"clavier_2(this.id,event,20,200);\" autocomplete='off' ";
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />";

	// 20140502
	if((getSettingAOui('PeutAutoriserPPaCorrigerSesApp'))&&(count($groups)>0)) {
		echo "<p style='margin-top:1em;'>Vous pouvez autoriser le ".getSettingValue('gepi_prof_suivi')." à corriger vos appréciations sur les bulletins.<br />
(<em>vous recevrez un mail lors des modifications de vos appréciations</em>).</p>
<p>Autoriser la modification/correction pour le ou les enseignements suivants&nbsp;:</p>
<ul>";
		$cpt_grp_pp=0;
		foreach($groups as $group) {
			/*
			echo "<li><pre>";
			print_r($group);
			echo "</pre></li>";
			*/

			//if($group['visibilite']['bulletins']=="y") {
			$sql="SELECT * FROM j_groupes_visibilite WHERE id_groupe='".$group['id']."' AND domaine='bulletins' AND visible='n';";
			$res_vis=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_vis)==0) {
				echo "
	<li>
		<input type='hidden' name='id_groupe_AppPP[$cpt_grp_pp]' value='".$group['id']."' />
		<input type='checkbox' name='id_groupe_PP_correction_autorisee[$cpt_grp_pp]' id='id_groupe_PP_correction_autorisee_$cpt_grp_pp' value='y' onchange=\"checkbox_change('id_groupe_PP_correction_autorisee_$cpt_grp_pp');changement()\" ";
				if(acces_correction_app_pp($group['id'])) {
					echo "checked ";
				}
				echo "/><label for='id_groupe_PP_correction_autorisee_$cpt_grp_pp' id='texte_id_groupe_PP_correction_autorisee_$cpt_grp_pp'";
				if(acces_correction_app_pp($group['id'])) {
					echo "style='font-weight:bold;' ";
				}
				echo ">".$group['name']." (<em style='font-size:small;'>".$group['description']." en ".$group['classlist_string']." avec ".$group['profs']['proflist_string']."</em>)&nbsp;: ";
				$cpt_class_grp=0;
				foreach($group['classes']['classes'] as $current_id_classe => $current_classe) {
					$gepi_prof_suivi=ucfirst(retourne_denomination_pp($current_id_classe));
					if($cpt_class_grp>0) {echo ", ";}
					echo "<strong>".$current_classe['classe']."</strong> (<em title=\"".ucfirst($gepi_prof_suivi)."\">".liste_des_prof_suivi_de_telle_classe($current_id_classe)."</em>)";
					$cpt_class_grp++;
				}
				echo "</label>
	</li>";
				$cpt_grp_pp++;
			}
		}
		echo "
</ul>";
	}

	echo "<p style='text-align:center;'>\n";
	echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</p>\n";

	if(isset($message_bulletins)) {echo $message_bulletins;}

	echo "</fieldset>\n";
	echo "</form>\n";
	//echo "  <hr />\n";
	echo "<br/>\n";
}

//==============================================================================

if ((acces_cdt())&&($_SESSION["statut"] == "professeur")) {
	$ouverture_auto_WinDevoirsDeLaClasse=getPref($_SESSION['login'], 'ouverture_auto_WinDevoirsDeLaClasse', 'y');
	echo "<a name='cdt_pref'></a><form name='form_cdt_pref' method='post' action='".$_SERVER['PHP_SELF']."#cdt_pref'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	echo "background-color: white; ";
	echo "'>Cahier de textes 2</legend>\n";

	if(!getSettingAOui("active_cahiers_texte")) {
		// On doit avoir acces_cdt_prof=y
		echo "<p style='text-align:left; color:red'>Ces cahiers de textes sont personnels. Ils ne sont pas accessibles des élèves, parents,...";
		if(getSettingValue("acces_cdt_prof_url_cdt_officiel")!="") {
			echo "<br />Les CDT officiels <em>(consultés par les élèves,...)</em> sont à l'adresse <a href='".getSettingValue("acces_cdt_prof_url_cdt_officiel")."' target='_blank'>".getSettingValue("acces_cdt_prof_url_cdt_officiel")."</a>";
		}
		echo "</p>";

		$acces_cdt_prof=getPref($_SESSION['login'], 'acces_cdt_prof', 'y');
		echo "<p style='margin-bottom:2em;'>Vous pouvez néanmoins activer ou désactiver le CDT interne à Gepi pour un usage personnel uniquement.<br />";
		echo "<input type='checkbox' name='acces_cdt_prof' id='acces_cdt_prof' value='y' ";
		echo "onchange=\"checkbox_change('acces_cdt_prof');changement()\" ";
		if($acces_cdt_prof=='y') {echo " checked";}
		echo " tabindex='$tabindex' ";
		$tabindex++;
		echo " /><label for='acces_cdt_prof' id='texte_acces_cdt_prof'> Afficher mes CDT personnel dans Gepi.</label><br />\n";
		echo "</p>";
	}

	echo "<p>Lors de la saisie de notices de Travaux à faire dans le CDT2,<br />\n";
	echo "<input type='radio' name='ouverture_auto_WinDevoirsDeLaClasse' id='ouverture_auto_WinDevoirsDeLaClasse_y' value='y' ";
	echo "onchange=\"checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_y');checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_n');changement()\" ";
	if($ouverture_auto_WinDevoirsDeLaClasse=='y') {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " /><label for='ouverture_auto_WinDevoirsDeLaClasse_y' id='texte_ouverture_auto_WinDevoirsDeLaClasse_y'> ouvrir automatiquement la fenêtre listant les travaux donnés par les autres professeurs,</label><br />\n";
	echo "<input type='radio' name='ouverture_auto_WinDevoirsDeLaClasse' id='ouverture_auto_WinDevoirsDeLaClasse_n' value='n' ";
	echo "onchange=\"checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_y');checkbox_change('ouverture_auto_WinDevoirsDeLaClasse_n');changement()\" ";
	if($ouverture_auto_WinDevoirsDeLaClasse!='y') {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " /><label for='ouverture_auto_WinDevoirsDeLaClasse_n' id='texte_ouverture_auto_WinDevoirsDeLaClasse_n'> ne pas ouvrir automatiquement la fenêtre listant les travaux donnés par les autres professeurs.</label><br />\n";

	$cdt2_WinListeNotices_nb_compte_rendus=getPref($_SESSION['login'], 'cdt2_WinListeNotices_nb_compte_rendus', 6);
	$cdt2_WinListeNotices_nb_devoirs=getPref($_SESSION['login'], 'cdt2_WinListeNotices_nb_devoirs', 6);
	$cdt2_WinListeNotices_nb_notices_privees=getPref($_SESSION['login'], 'cdt2_WinListeNotices_nb_notices_privees', 6);
	$tabindex2=$tabindex+1;
	$tabindex3=$tabindex+2;
	echo "<br /><br />
<p style='text-indent:-3em;margin-left:3em;'>Nombre de notices de chaque type à afficher par défaut dans la fenêtre intitulée 'Liste des notices'.<br />
Compte-rendus&nbsp;:&nbsp;<input type='text' name='cdt2_WinListeNotices_nb_compte_rendus' id='cdt2_WinListeNotices_nb_compte_rendus' value='$cdt2_WinListeNotices_nb_compte_rendus' tabindex='$tabindex' size='3' onkeydown=\"clavier_2(this.id,event,1,100);\" autocomplete='off' /><br />
Devoirs à faire&nbsp;:&nbsp;&nbsp;&nbsp;<input type='text' name='cdt2_WinListeNotices_nb_devoirs' id='cdt2_WinListeNotices_nb_devoirs' value='$cdt2_WinListeNotices_nb_devoirs' tabindex='$tabindex2' size='3' onkeydown=\"clavier_2(this.id,event,1,100);\" autocomplete='off' /><br />
Notices privées&nbsp;:&nbsp;<input type='text' name='cdt2_WinListeNotices_nb_notices_privees' id='cdt2_WinListeNotices_nb_notices_privees' value='$cdt2_WinListeNotices_nb_notices_privees' tabindex='$tabindex3' size='3' onkeydown=\"clavier_2(this.id,event,1,100);\" autocomplete='off' /><br />
<p>(<em>en cliquant sur 'Afficher toutes les notices' en bas de la fenêtre 'Liste des notices', vous pourrez tout de même afficher toutes les notices</em>)</p>";
	$tabindex=$tabindex3+1;

	$cdt2_afficher_passer_au_cours_suivant=getPref($_SESSION['login'], 'cdt2_afficher_passer_au_cours_suivant', "les_2");
	$checked_1="";
	$checked_2="";
	$checked_3="";
	if(($cdt2_afficher_passer_au_cours_suivant!="cours_suivant")&&($cdt2_afficher_passer_au_cours_suivant!="lendemain")) {$checked_1="checked ";}
	elseif($cdt2_afficher_passer_au_cours_suivant=="lendemain") {$checked_2="checked ";}
	else {$checked_3="checked ";}
	$tabindex2=$tabindex+1;
	$tabindex3=$tabindex+2;
	echo "<br /><br /><p>Si l'emploi du temps est rempli, il est possible d'afficher un bouton <strong>Enregistrer et passer aux devoirs du Cours suivant</strong> en recherchant ce cours dans l'emploi du temps.<br />
	Si l'emploi du temps est mal rempli ou incomplet, il vaut mieux conserver le bouton classique <strong>Enregistrer et passer aux devoirs du lendemain</strong>.</p>
	<p>
		<input type='radio' name='cdt2_afficher_passer_au_cours_suivant' id='cdt2_afficher_passer_au_cours_suivant_1' value='les_deux' tabindex='$tabindex' $checked_1/>
		<label for='cdt2_afficher_passer_au_cours_suivant_1' id='texte_cdt2_afficher_passer_au_cours_suivant_1'>Afficher les deux boutons (<em title=\"Par précaution... mais il est toujours possible de choisir la date d'une notice dans le calendrier.\">par précaution, le temps de s'assurer que l'emploi du temps est bien complet</em>)</label><br />

		<input type='radio' name='cdt2_afficher_passer_au_cours_suivant' id='cdt2_afficher_passer_au_cours_suivant_2' value='lendemain' tabindex='$tabindex2' $checked_2/>
		<label for='cdt2_afficher_passer_au_cours_suivant_2' id='texte_cdt2_afficher_passer_au_cours_suivant_2'>Afficher seulement le bouton classique <strong>Enregistrer et passer aux devoirs du lendemain</strong></label><br />

		<input type='radio' name='cdt2_afficher_passer_au_cours_suivant' id='cdt2_afficher_passer_au_cours_suivant_3' value='cours_suivant' tabindex='$tabindex3' $checked_3/>
		<label for='cdt2_afficher_passer_au_cours_suivant_3' id='texte_cdt2_afficher_passer_au_cours_suivant_3'>Afficher seulement le bouton classique <strong>Enregistrer et passer aux devoirs du cours suivant</strong></label><br />
	</p>";
	$tabindex=$tabindex3+1;

	echo "<p style='text-align:center;'>\n";
	echo "<input type='hidden' name='validation_form_cdt2' value='y' />\n";
	echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</p>\n";

	if(isset($message_cdt)) {echo $message_cdt;}

	echo "</fieldset>\n";
	echo "</form>\n";

	//echo "<hr />\n";
	echo "<br/>\n";
}

//==============================================================================
if(($_SESSION['statut']=='professeur')&&(getSettingAOui('active_mod_abs_prof'))&&(getSettingAOui('AbsProfAutoriserProfPasApparaitre'))) {
	$AbsProf_jamais_remplacer=getPref($_SESSION['login'], 'AbsProf_jamais_remplacer', 'n');
	echo "<a name='mod_abs_prof'></a><form name='form_mod_abs_prof' method='post' action='".$_SERVER['PHP_SELF']."#mod_abs_prof'>\n";
	echo add_token_field();
	echo "<fieldset class='fieldset_opacite50'>\n";
	echo "<legend style='border: 1px solid grey; background-color: white; ";
	echo "'>Module Absences et remplacements de professeurs</legend>\n";
	echo "<p>Remplacer des collègues pour une absence ponctuelle peut rendre service en déchargeant la permanence et en coupant l'herbe sous le pied des parents médisants quant aux absences de professeurs;).<br />
	Vous pouvez toujours refuser les propositions de remplacement qui vous sont faites.<br />
	L'établissement vous autorise même à ne pas apparaître dans la liste des personnes auxquelles proposer d'effectuer un remplacement&nbsp;: <br />\n";
	echo "<input type='checkbox' name='AbsProf_jamais_remplacer' id='AbsProf_jamais_remplacer' value='$AbsProf_jamais_remplacer' tabindex='$tabindex' onchange=\"changement(); checkbox_change(this.id);\" ".(($AbsProf_jamais_remplacer=="y") ? "checked" : "")." /><label for='AbsProf_jamais_remplacer' id='texte_AbsProf_jamais_remplacer'> Ne pas apparaître dans la liste des candidats potentiels pour effectuer des remplacements</label><br />\n";
	$tabindex++;
	echo "<input type='hidden' name='validation_form_abs_prof' value='y' />\n";
	echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</p>\n";

	if(isset($message_mod_abs_prof)) {echo $message_mod_abs_prof;}

	echo "</fieldset>\n";
	echo "</form>\n";
	echo "<br/>\n";
}
//==============================================================================
//debug_var();
$chaine_champs_checkbox_mod_discipline="";
$tab_statuts_mod_discipline=array('professeur', 'administrateur', 'scolarite', 'cpe');
if ((getSettingValue('active_mod_discipline')!='n')&&(in_array($_SESSION['statut'], $tab_statuts_mod_discipline))) {
	$mod_discipline_travail_par_defaut=getPref($_SESSION['login'], 'mod_discipline_travail_par_defaut', 'Travail : ');
	echo "<a name='mod_discipline'></a><form name='form_mod_discipline' method='post' action='".$_SERVER['PHP_SELF']."#mod_discipline'>\n";
	echo add_token_field();
	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Module Discipline et sanctions</legend>\n";
	echo "<p>Lors de la saisie de travail à faire, le texte par défaut proposé sera&nbsp;: <br />\n";
	echo "<input type='hidden' name='module' value='discipline' />\n";
	echo "<input type='text' name='mod_discipline_travail_par_defaut' value='$mod_discipline_travail_par_defaut' size='30' tabindex='$tabindex' /><br />\n";
	$tabindex++;

if (getSettingAOui('DisciplineCpeChangeDeclarant')) {
    $tab_statuts_cpePeuChanger=array('professeur');
    if(in_array($_SESSION['statut'], $tab_statuts_cpePeuChanger)) {
?>
        <label for='cpePeuChanger'>Autoriser les CPE à m'attribuer des déclarations d'incident</label>
        <input type="checkbox" 
               id="cpePeuChanger" 
               name="cpePeuChanger" 
               value="yes" 
               <?php
                   if (getPref($_SESSION['login'],'cpePeuChanger' ,'yes' ) && getPref($_SESSION['login'],'cpePeuChanger' ,'no' ) == "yes") {
                       echo " checked='checked'";
                   }
                   echo " tabindex='$tabindex' ";
                   $tabindex++;
               ?>
               />
        <input type='hidden' name='autorise_cpe_declarant' value='ok' />
    
<?php    
    }
}

	//==============================================
	echo "<p class='bold' style='margin-top:1em;'>Affichage d'un témoin d'incident en page d'accueil&nbsp;:</p>
<p>Gepi permet d'afficher si vous le souhaitez un témoin indiquant que de nouveaux incidents ont été saisis depuis votre dernière connexion.</p>\n";
	if($_SESSION['statut']=='professeur') {
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentProf',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentProf' id='DiscTemoinIncidentProf' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentProf')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentProf' id='texte_DiscTemoinIncidentProf'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents concernant mes classes/groupes.</label>";
		$tabindex++;
		if(is_pp($_SESSION['login'])) {
			$checked="";
			if(getPref($_SESSION['login'], 'DiscTemoinIncidentPP',"n")=="y") {
				$checked="checked ";
			}

			echo "<br />
<input type='checkbox' name='DiscTemoinIncidentPP' id='DiscTemoinIncidentPP' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentPP')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentPP' id='texte_DiscTemoinIncidentPP'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents concernant la ou les classes dont je suis ".getSettingValue('gepi_prof_suivi').".</label>";
			$tabindex++;
		}
		echo "</p>";
	}
	elseif($_SESSION['statut']=='cpe') {
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentCpe',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentCpe' id='DiscTemoinIncidentCpe' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentCpe')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentCpe' id='texte_DiscTemoinIncidentCpe'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents concernant les élèves dont je suis le CPE responsable.</label>";
		$tabindex++;
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentCpeTous',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentCpeTous' id='DiscTemoinIncidentCpeTous' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentCpeTous')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentCpeTous' id='texte_DiscTemoinIncidentCpeTous'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents quels que soient les élèves.</label>";
		$tabindex++;
		echo "</p>";
	}
	elseif($_SESSION['statut']=='scolarite') {
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentScol',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentScol' id='DiscTemoinIncidentScol' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentScol')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentScol' id='texte_DiscTemoinIncidentScol'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents concernant les élèves des classes auxquelles je suis associé en tant que compte 'scolarité'.</label>";
		$tabindex++;
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentScolTous',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentScolTous' id='DiscTemoinIncidentScolTous' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentScolTous')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentScolTous' id='texte_DiscTemoinIncidentScolTous'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> pour les incidents quels que soient les élèves.</label>";
		$tabindex++;
		echo "</p>";
	}
	elseif($_SESSION['statut']=='administrateur') {
		$checked="";
		if(getPref($_SESSION['login'], 'DiscTemoinIncidentAdmin',"n")=="y") {
			$checked="checked ";
		}
		echo "<p><input type='checkbox' name='DiscTemoinIncidentAdmin' id='DiscTemoinIncidentAdmin' value='yes' onchange=\"checkbox_change('DiscTemoinIncidentAdmin')\" tabindex='$tabindex' $checked/><label for='DiscTemoinIncidentAdmin' id='texte_DiscTemoinIncidentAdmin'> Afficher un témoin <img src='../images/icons/flag2.gif' class='icone16' alt='Témoin' /> lorsque de nouveaux incidents sont saisis.</label>";
		$tabindex++;
		echo "</p>";
	}

	$DiscTemoinIncidentTaille=getPref($_SESSION['login'], 'DiscTemoinIncidentTaille', 16);
	echo "<p><label for='DiscTemoinIncidentTaille' id='texte_DiscTemoinIncidentTaille'> Taille en pixels du témoin affiché&nbsp;: </label><input type='text' name='DiscTemoinIncidentTaille' id='DiscTemoinIncidentTaille' value='$DiscTemoinIncidentTaille' onchange=\"changement()\" tabindex='$tabindex' onkeydown=\"clavier_2(this.id,event,10,40);\" size='3' /></p>";
	$tabindex++;

	echo "<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Le témoin ne disparait pas une fois l'incident visualisé.<br />Il disparait à la connexion suivante.</p>";

	//==============================================
	echo "<p class='bold' style='margin-top:1em;'>Signalement d'incidents par mail&nbsp;:</p>\n";
	$sql2="";
	if($_SESSION['statut']=='cpe') {
		$sql="(SELECT DISTINCT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, 
																	classes c, 
																	j_eleves_cpe jecpe, 
																	j_eleves_classes jec 
																WHERE sam.id_classe=c.id AND 
																	sam.destinataire='cpe' AND
																	jec.id_classe=sam.id_classe AND
																	jec.login=jecpe.e_login AND
																	jecpe.cpe_login='".$_SESSION['login']."'
																ORDER BY c.classe)";
		$qualite="CPE";
	}
	elseif($_SESSION['statut']=='professeur') {
		$sql="(SELECT DISTINCT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, 
																	classes c, 
																	j_eleves_groupes jeg, 
																	j_eleves_classes jec, 
																	j_groupes_professeurs jgp 
																WHERE sam.id_classe=c.id AND 
																	sam.destinataire='professeurs' AND 
																	jec.id_classe=sam.id_classe AND 
																	jec.login=jeg.login AND 
																	jeg.id_groupe=jgp.id_groupe AND 
																	jgp.login='".$_SESSION['login']."'
																ORDER BY c.classe)";
		$qualite="professeur";
		//echo $sql;
		if(is_pp($_SESSION['login'])) {
			$sql2="(SELECT DISTINCT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, 
																	classes c, 
																	j_eleves_professeurs jep, 
																	j_eleves_classes jec 
																WHERE sam.id_classe=c.id AND 
																	sam.destinataire='professeur' AND 
																	jec.id_classe=sam.id_classe AND 
																	jec.login=jep.login AND 
																	jep.id_classe=jec.id_classe AND 
																	jep.professeur='".$_SESSION['login']."'
																ORDER BY c.classe)";
		}
	}
	elseif($_SESSION['statut']=='administrateur') {
		$sql="(SELECT DISTINCT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, classes c WHERE sam.id_classe=c.id AND destinataire='administrateur' ORDER BY c.classe)";
		$qualite="Administrateur";
	}
	elseif($_SESSION['statut']=='scolarite') {
		$sql="(SELECT DISTINCT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, 
																	classes c, 
																	j_scol_classes jsc 
																WHERE sam.id_classe=c.id AND 
																	sam.destinataire='scolarite' AND
																	jsc.id_classe=sam.id_classe AND
																	jsc.login='".$_SESSION['login']."'
																ORDER BY c.classe)";
		$qualite="compte Scolarité";
	}
	$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mail)>0) {
		echo "<p>Vous êtes destinataire, en tant que $qualite, des mail concernant les incidents impliquant des élèves des classes suivantes&nbsp;: <br />";
		$cpt=0;
		$tab_classe_mail=array();
		while($lig_mail=mysqli_fetch_object($res_mail)) {
			if(!in_array($lig_mail->id_classe, $tab_classe_mail)) {
				if($cpt>0) {
					echo ", ";
				}
				echo $lig_mail->classe;
				$tab_classe_mail[]=$lig_mail->id_classe;
			}
			$cpt++;
		}

	}
	else {
		echo "<p>Vous n'êtes destinataire, en tant que $qualite, d'aucun mail signalant des incidents.<br />Si vous pensez que c'est une erreur, contactez l'administrateur.</p>";
	}
	
	if($_SESSION['statut']=='professeur') {
		if(getPref($_SESSION['login'], 'mod_discipline_mail_que_groupe', "")=="y") {
			
		}
?>
		<p>
			<input type="checkbox" 
				   name="limiteAGroupe" 
				   id='limiteAGroupe'
				   <?php 
				      if(getPref($_SESSION['login'], 'limiteAGroupe', "") == "y") {echo "checked = 'checked' ";} 
				      echo " tabindex='$tabindex' ";
				      $tabindex++;
				   ?>
				   value="y"
				   />
			<label for="limiteAGroupe">Limiter les courriels aux élèves que j'ai effectivement en cours</label>
		</p>
<?php
	}

	echo "<p style='margin-top:1em;'>Dans le cas où vous recevez des signalements par mail, vous pouvez restreindre les catégories d'incidents pour lesquelles vous souhaitez être informé&nbsp;: <br />\n";
	$tab_id_categories_exclues=array();
	$mod_discipline_natures_exclues_mail=getPref($_SESSION['login'], 'mod_discipline_natures_exclues_mail', '');
	if($mod_discipline_natures_exclues_mail!="") {
		$tmp_tab=explode("|", $mod_discipline_natures_exclues_mail);
		for($loop=0;$loop<count($tmp_tab);$loop++) {
			if($tmp_tab[$loop]!="") {
				$tab_id_categories_exclues[]=$tmp_tab[$loop];
			}
		}
	}
	$sql="SELECT * FROM s_categories ORDER BY categorie;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune catégorie n'est définie&nbsp;???</p>";
	}
	else {
		while($lig=mysqli_fetch_object($res)) {
			echo "<input type='checkbox' id='mod_disc_mail_cat_incluse_$lig->id' name='mod_disc_mail_cat_incluse[]' value='$lig->id' onchange=\"checkbox_change('mod_disc_mail_cat_incluse_$lig->id')\" ";
			if(!in_array($lig->id, $tab_id_categories_exclues)) {
				echo "checked ";
			}
			echo " tabindex='$tabindex' ";
			$tabindex++;
			echo " /><label for='mod_disc_mail_cat_incluse_$lig->id' id='texte_mod_disc_mail_cat_incluse_$lig->id'>$lig->categorie</label><br />";
			if($chaine_champs_checkbox_mod_discipline!="") {$chaine_champs_checkbox_mod_discipline.=", ";}
			$chaine_champs_checkbox_mod_discipline.="'mod_disc_mail_cat_incluse_$lig->id'";
		}

		echo "<input type='checkbox' id='mod_disc_mail_cat_incluse_NC' name='mod_disc_mail_cat_incluse_NC' value='y' onchange=\"checkbox_change('mod_disc_mail_cat_incluse_NC')\" ";
		if(getPref($_SESSION['login'], 'mod_discipline_natures_non_categorisees_exclues_mail', "")!="y") {
			echo "checked = 'checked' ";
		}
			echo " tabindex='$tabindex' ";
			$tabindex++;
		echo " /><label for='mod_disc_mail_cat_incluse_NC' id='texte_mod_disc_mail_cat_incluse_NC'>Incidents dont la nature n'est pas catégorisée.</label><br />";
		if($chaine_champs_checkbox_mod_discipline!="") {$chaine_champs_checkbox_mod_discipline.=", ";}
		$chaine_champs_checkbox_mod_discipline.="'mod_disc_mail_cat_incluse_NC'";

		if(getSettingValue('DisciplineNaturesRestreintes')=='2') {
			echo "<p>Vous utilisez une liste figée/restreinte de natures d'incidents.<br />Vous ne devriez pas avoir d'incidents de nature non catégorisée.</p>\n";
		}
	}

	if($sql2!="") {
		$res_mail=mysqli_query($GLOBALS["mysqli"], $sql2);
		if(mysqli_num_rows($res_mail)>0) {
			echo "<p style='margin-top:1em;'>Vous êtes destinataire, en tant que ".getSettingValue('gepi_prof_suivi').", des mail concernant les incidents impliquant des élèves des classes suivantes&nbsp;: ";
			$cpt=0;
			$tab_classe_mail=array();
			while($lig_mail=mysqli_fetch_object($res_mail)) {
				if(!in_array($lig_mail->id_classe, $tab_classe_mail)) {
					if($cpt>0) {
						echo ", ";
					}
					echo $lig_mail->classe;
					$tab_classe_mail[]=$lig_mail->id_classe;
				}
				$cpt++;
			}
			echo "</p>\n";
		}
		else {
			echo "<p>Vous n'êtes destinataire, en tant que ".getSettingValue('gepi_prof_suivi').", d'aucun mail signalant des incidents.<br />Si vous pensez que c'est une erreur, contactez l'administrateur.</p>";
		}
	}
	//$sql.=" UNION (SELECT c.id, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, classes c WHERE sam.id_classe=c.id AND destinataire='mail' AND adresse='".$_SESSION['adresse']."' ORDER BY c.classe))";

	$sql="(SELECT c.classe, sam.id_classe, sam.destinataire FROM s_alerte_mail sam, classes c WHERE sam.id_classe=c.id AND destinataire='mail' AND adresse='".$_SESSION['email']."' ORDER BY c.classe)";
	//echo "$sql<br />";
	$res_mail=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mail)>0) {
		echo "<p style='margin-top:1em;'>Vous êtes destinataire, par l'adresse mail saisie directement par l'administrateur, des mail concernant les incidents impliquant des élèves des classes suivantes&nbsp;: ";
		$cpt=0;
		$tab_classe_mail=array();
		while($lig_mail=mysqli_fetch_object($res_mail)) {
			if(!in_array($lig_mail->id_classe, $tab_classe_mail)) {
				if($cpt>0) {
					echo ", ";
				}
				echo $lig_mail->classe;
				$tab_classe_mail[]=$lig_mail->id_classe;
			}
			$cpt++;
		}
		echo "</p>\n";

		echo "<p>Il n'est pas possible actuellement de restreindre les signalements par mail à certaines catégories d'incidents avec ce mode.</p>";
	}

	echo "<p style='text-align:center;'>\n";
	echo "<input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' />\n";
	$tabindex++;
	echo "</p>\n";

	if(isset($message_mod_discipline)) {echo $message_mod_discipline;}

	if(getSettingValue('DisciplineNaturesRestreintes')!='2') {
		echo "<p style='text-indent:-4em;margin-left:4em;'><em>NOTES&nbsp;:</em> Les natures d'incidents ne sont pas figées/restreintes.<br />
		Les utilisateurs peuvent taper librement de nouvelles natures d'incidents.<br />
		Si vous refusez de recevoir les incidents de nature non catégorisée, vous risquez de rater un incident qui sera par la suite catégorisé dans une catégorie que vous suivez.</p>";
	}

	echo "</fieldset>\n";
	echo "</form>\n";

	//echo "<hr />\n";
	echo "<br/>\n";
}



//==============================================================================
// 20160919
if ((getSettingValue("autorise_edt_tous") == "y")&&($_SESSION['statut']=="professeur")) {
	if(!isset($groups)) {
		$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
	}

	echo "<a name='param_edt'></a>
<form enctype=\"multipart/form-data\" action=\"mon_compte.php#param_edt\" method=\"post\">
	".add_token_field()."
	<fieldset id='paramEDT' class='fieldset_opacite50'>
		<legend style='border: 1px solid grey; background-color: white;'>Paramètres Emploi du temps</legend>
		<input type='hidden' name='parametrer_edt' value='y' />

		<p>Définir des couleurs pour mes enseignements dans l'EDT2&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th colspan='2'>Enseignement</th>
				<th>Classe(s)</th>
				<th colspan='2'>Couleur</th>
			</tr>";
	$chaine_js_initialisation_couleurs="";
	for($loop=0;$loop<count($groups);$loop++) {
		echo "
			<tr>
				<td>".$groups[$loop]['name']."</td>
				<td>".$groups[$loop]['description']."</td>
				<td>".$groups[$loop]['classlist_string']."</td>
				<td>
					<select name='edt2_couleur_grp[".$groups[$loop]["id"]."]' id='edt2_couleur_grp_".$groups[$loop]["id"]."' onchange=\"document.getElementById('div_edt2_couleur_grp_".$groups[$loop]["id"]."').style.backgroundColor=this.options[this.selectedIndex].value;\">
						<option value=''>---</option>";

		$pref_couleur=getPref($_SESSION['login'], "edt2_couleur_grp_".$groups[$loop]["id"], "");

		if($pref_couleur!="") {
			$chaine_js_initialisation_couleurs.="document.getElementById('div_edt2_couleur_grp_".$groups[$loop]["id"]."').style.backgroundColor=document.getElementById('edt2_couleur_grp_".$groups[$loop]["id"]."').options[document.getElementById('edt2_couleur_grp_".$groups[$loop]["id"]."').selectedIndex].value;\n";
		}

		for($loop2=0;$loop2<count($tab_couleurs_html);$loop2++) {
			$selected="";
			if($tab_couleurs_html[$loop2]==$pref_couleur) {
				$selected=" selected='true'";
			}
			echo "
						<option value='".$tab_couleurs_html[$loop2]."' style='background-color:".$tab_couleurs_html[$loop2].";'$selected>".$tab_couleurs_html[$loop2]."</option>";
		}

		echo "
					</select>
				</td>
				<td>
					<div id='div_edt2_couleur_grp_".$groups[$loop]["id"]."' style='width:2em;'>&nbsp;&nbsp;</div>
				</td>
			</tr>";
	}
	echo "
		</table>
		<p><input type='submit' name='Valider' value='Enregistrer' tabindex='$tabindex' /></p>
	</fieldset>
</form>
<br />\n";
	$tabindex++;

	if($chaine_js_initialisation_couleurs!="") {
		echo "<script type='text/javascript'>
".$chaine_js_initialisation_couleurs."
</script>";
	}
}

//==============================================================================

$tab_statuts_barre=array('professeur', 'cpe', 'scolarite', 'administrateur', 'secours');
if(in_array($_SESSION['statut'], $tab_statuts_barre)) {
	// On affiche si c'est autorisé
	if (getSettingValue("utiliserMenuBarre") != "no") {
		$aff_checked=getPref($_SESSION['login'],"utiliserMenuBarre","");

		echo "<a name='modifier_barre'></a><form enctype=\"multipart/form-data\" action=\"mon_compte.php#modifier_barre\" method=\"post\">\n";
		echo add_token_field();

		echo "<fieldset id='afficherBarreMenu' style='border: 1px solid grey;";
		echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "'>\n";
		echo "<legend style='border: 1px solid grey;";
		//echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "background-color: white; ";
		echo "'>Gérer la barre horizontale du menu</legend>\n";
		echo "<input type='hidden' name='modifier_barre' value='ok' />\n";

		if (getSettingValue("utiliserMenuBarre") == "yes") {
			echo "<p>\n";
			echo "<label for='visibleMenu' id='texte_visibleMenu'>Rendre visible la barre de menu horizontale complète sous l'en-tête.</label>\n";
			echo "<input type='radio' id='visibleMenu' name='afficher_menu' value='yes'";
			echo " onchange=\"checkbox_change('invisibleMenu');checkbox_change('visibleMenuLight');checkbox_change('visibleMenu');changement()\" ";
			if($aff_checked=="yes") echo " checked";
			echo " tabindex='$tabindex' ";
			$tabindex++;
			echo " />\n";
			echo "</p>\n";
		}

		echo "<p>\n";
		echo "<label for='visibleMenuLight' id='texte_visibleMenuLight'>Rendre visible la barre de menu horizontale allégée sous l'en-tête.</label>\n";
		echo "<input type='radio' id='visibleMenuLight' name='afficher_menu' value='light'";
		echo " onchange=\"checkbox_change('invisibleMenu');checkbox_change('visibleMenuLight');checkbox_change('visibleMenu');changement()\" ";
		if($aff_checked=="light") echo " checked";
		echo " tabindex='$tabindex' ";
		$tabindex++;
		echo " />\n";
		echo "</p>\n";

		echo "<p>\n";
		echo "<label for='invisibleMenu' id='texte_invisibleMenu'>Ne pas utiliser la barre de menu horizontale.</label>\n";
		echo "<input type='radio' id='invisibleMenu' name='afficher_menu' value='no'";
		echo " onchange=\"checkbox_change('invisibleMenu');checkbox_change('visibleMenuLight');checkbox_change('visibleMenu');changement()\" ";
		if($aff_checked=="no") echo " checked";
		echo " tabindex='$tabindex' ";
		$tabindex++;
		echo " />\n";
		echo "</p>\n";

		echo "<p>
				<em>La barre de menu horizontale allégée a une arborescence moins profonde pour que les menus 'professeurs' s'affichent plus rapidement au cas où le serveur serait saturé.</em>
			</p>\n";

		echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center>\n";
		$tabindex++;

		if(isset($message_modifier_barre)) {echo $message_modifier_barre;}

		echo "</fieldset>\n";
		echo "</form>\n";
		//echo "  <hr />\n";
		echo "<br/>\n";
	}
}
//==============================================================================

echo "<a name='change_header_user'></a><form name='change_header_user' method='post' action='".$_SERVER['PHP_SELF']."#change_header_user'>\n";
echo add_token_field();
echo "	<fieldset style='border: 1px solid grey;";
echo "background-image: url(\"../images/background/opacite50.png\"); ";
echo "'>
		<legend style='border: 1px solid grey;";
//echo "background-image: url(\"../images/background/opacite50.png\"); ";
echo "background-color: white; ";
echo "'>Gérer la hauteur de l'entête</legend>
		<input type='hidden' name='modifier_hauteur_entete' value='ok' />
		<p>
			<label for='headerBas' id='texte_headerBas'>Utiliser une entête basse par défaut</label>
			<input type='radio' id='headerBas' name='header_bas' value='y' ";
echo "onchange=\"checkbox_change('headerBas');checkbox_change('headerNormal');changement()\" ";
//echo " onclick='document.change_header_user.submit();'";
$petit_entete=getPref($_SESSION['login'], 'petit_entete', "n");
if($petit_entete=="y") {
	echo "checked";
}
echo " tabindex='$tabindex' ";
$tabindex++;
echo " />
		</p>
		<p>
			<label for='headerNormal' id='texte_headerNormal'>Utiliser l'entête classique complète</label>
			<input type='radio' id='headerNormal' name='header_bas' value='n' ";
echo "onchange=\"checkbox_change('headerBas');checkbox_change('headerNormal');changement()\" ";
//echo " onclick='document.change_header_user.submit();'";
if($petit_entete=="n") {
	echo "checked";
}
echo " tabindex='$tabindex' ";
$tabindex++;
echo " />
		</p>\n";

echo "<p style='text-align:center;'>\n";
echo "<input type='submit' name='valider' id='change_header_user_button' value='Enregistrer' tabindex='$tabindex' /></p>
<script type='text/javascript'>
//document.getElementById('change_header_user_button').style.display='none';
</script>\n";
$tabindex++;

if(isset($message_hauteur_header)) {echo $message_hauteur_header;}

echo "<p><em>NOTE&nbsp;:</em> L'entête basse prend moins de place à l'écran.</p>";
echo "
	</fieldset>
</form>\n";
//echo "<hr />\n";
echo "<br/>\n";

//==============================================================================

$tab_statuts_barre=array('professeur', 'cpe', 'scolarite', 'administrateur', 'autre', 'secours');
if(in_array($_SESSION['statut'], $tab_statuts_barre)) {
	echo "<a name='choixEncodageCsv'></a><form enctype=\"multipart/form-data\" action=\"mon_compte.php#choixEncodageCsv\" method=\"post\">\n";
	echo add_token_field();

	echo "<fieldset id='choixEncodageCsv' style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Choix de l'encodage des CSV téléchargés</legend>\n";
	echo "<input type='hidden' name='choix_encodage_csv' value='ok' />\n";

	$choix_encodage_csv=getPref($_SESSION['login'], "choix_encodage_csv", "");
	if($choix_encodage_csv=='') {
		if($_SESSION['statut']=='administrateur') {
			$choix_encodage_csv="ascii";
		}
		else {
			$choix_encodage_csv="windows-1252";
		}
	}

	echo "<p>\n";
	echo "<input type='radio' id='choix_encodage_csv_ascii' name='choix_encodage_csv' value='ascii'";
	echo " onchange=\" checkbox_change('choix_encodage_csv_ascii');checkbox_change('choix_encodage_csv_utf8');checkbox_change('choix_encodage_csv_windows_1252');changement()\"";
	if($choix_encodage_csv=="ascii") {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />\n";
	//echo "<label for='choix_encodage_csv_ascii' id='texte_choix_encodage_csv_ascii'>ASCII (<em>sans accents</em>)</label>\n";
	echo "<label for='choix_encodage_csv_ascii' id='texte_choix_encodage_csv_ascii'>Sans accents</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	echo "<input type='radio' id='choix_encodage_csv_utf8' name='choix_encodage_csv' value='utf-8'";
	echo " onchange=\" checkbox_change('choix_encodage_csv_ascii');checkbox_change('choix_encodage_csv_utf8');checkbox_change('choix_encodage_csv_windows_1252');changement()\"";
	if($choix_encodage_csv=="utf-8") {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />\n";
	echo "<label for='choix_encodage_csv_utf8' id='texte_choix_encodage_csv_utf8'>Accents UTF-8</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	echo "<input type='radio' id='choix_encodage_csv_windows_1252' name='choix_encodage_csv' value='windows-1252'";
	echo " onchange=\" checkbox_change('choix_encodage_csv_ascii');checkbox_change('choix_encodage_csv_utf8');checkbox_change('choix_encodage_csv_windows_1252');changement()\"";
	if($choix_encodage_csv=="windows-1252") {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />\n";
	echo "<label for='choix_encodage_csv_windows_1252' id='texte_choix_encodage_csv_windows_1252'>Accents WINDOWS-1252</label>\n";
	echo "</p>\n";

	echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center>\n";
	$tabindex++;

	if(isset($message_choixEncodageCsv)) {
		echo $message_choixEncodageCsv;
	}

	echo "<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Ce paramétrage concerne les fichiers produits par Gepi et proposés au téléchargement.<br />Cela ne concerne pas les fichiers que vous uploadez/envoyez vers Gepi.</p>\n";

	echo "</fieldset>\n";
	echo "</form>\n";
	//echo "  <hr />\n";
	echo "<br/>\n";
}
//==============================================================================

$tab_statuts_barre=array('professeur', 'cpe', 'scolarite', 'administrateur', 'autre', 'secours');
if(in_array($_SESSION['statut'], $tab_statuts_barre)) {
	echo "<a name='choixModePDF'></a><form enctype=\"multipart/form-data\" action=\"mon_compte.php#choixModePDF\" method=\"post\">\n";
	echo add_token_field();

	echo "<fieldset id='choixModePDF' style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Choix du mode d'export PDF</legend>\n";
	echo "<input type='hidden' name='choix_mode_export_pdf' value='ok' />\n";

	$output_mode_pdf=get_output_mode_pdf();

	echo "<p>\n";
	echo "<input type='radio' id='output_mode_pdf_I' name='output_mode_pdf' value='I'";
	echo " onchange=\" checkbox_change('output_mode_pdf_I');checkbox_change('output_mode_pdf_D');changement()\"";
	if($output_mode_pdf!="D") {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />\n";
	echo "<label for='output_mode_pdf_I' id='texte_output_mode_pdf_I'>Affichage interne au navigateur</label>\n";
	echo "</p>\n";

	echo "<p>\n";
	echo "<input type='radio' id='output_mode_pdf_D' name='output_mode_pdf' value='D'";
	echo " onchange=\" checkbox_change('output_mode_pdf_I');checkbox_change('output_mode_pdf_D');changement()\"";
	if($output_mode_pdf=="D") {echo " checked";}
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />\n";
	echo "<label for='output_mode_pdf_D' id='texte_output_mode_pdf_D'>Fenêtre de téléchargement/ouverture</label>\n";
	echo "</p>\n";

	echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" tabindex='$tabindex' /></center>\n";
	$tabindex++;

	if(isset($message_output_mode_pdf)) {
		echo $message_output_mode_pdf;
	}

	echo "<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Les fichiers PDF générés par Gepi peuvent être affichés dans le navigateur si vous avez installé un plugin comme Acrobat Reader, ou si vous utilisez un navigateur comme Firefox&gt;=19.0.0.<br />Cet affichage interne au navigateur peut ne pas être souhaité.<br />En choisisssant <b>Fenêtre de téléchargement/ouverture</b>, il est possible de forcer le navigateur à vous proposer l'enregistrement ou l'ouverture avec le programme de votre choix.</p>\n";

	echo "</fieldset>\n";
	echo "</form>\n";
	//echo "  <hr />\n";
	echo "<br/>\n";
}
//==============================================================================


if(count($tab_sound)>=0) {
	$footer_sound_actuel=getPref($_SESSION['login'],'footer_sound',"");

	echo "<a name='footer_sound'></a><form name='change_footer_sound' method='post' action='".$_SERVER['PHP_SELF']."#footer_sound'>\n";
	echo add_token_field();

	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>
	<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Choix de l'alerte sonore de fin de session</legend>
	<p><select name='footer_sound' id='footer_sound' onchange='test_play_footer_sound()' tabindex='$tabindex'>\n";
	$tabindex++;
	echo "	<option value=''";
	if($footer_sound_actuel=='') {echo " selected='true'";}
	echo ">Aucun son</option>\n";
	for($i=0;$i<count($tab_sound);$i++) {
		echo "	<option value='".$tab_sound[$i]."'";
		if($tab_sound[$i]==$footer_sound_actuel) {echo " selected='true'";}
		echo ">".$tab_sound[$i]."</option>\n";
	}
	echo "	</select>
	<a href='javascript:test_play_footer_sound()'><img src='../images/icons/sound.png' width='16' height='16' alt='Ecouter le son choisi' title='Ecouter le son choisi' /></a>
	</p>\n";

	echo "
	<p align='center'><input type='submit' name='enregistrer' value='Enregistrer' style='font-variant: small-caps;' tabindex='$tabindex' /></p>\n";
	$tabindex++;

	if(isset($message_footer_sound)) {
		echo $message_footer_sound;
	}

	echo "</fieldset>
</form>\n";

	for($i=0;$i<count($tab_sound);$i++) {
		echo "<audio id='footer_sound_$i' preload='auto' autobuffer>
  <source src='$chemin_sound".$tab_sound[$i]."' />
</audio>\n";
	}

	echo "<script type='text/javascript'>
function test_play_footer_sound() {
	n=document.getElementById('footer_sound').selectedIndex;
	if(n>0) {
		n--;
		if(document.getElementById('footer_sound_'+n)) {
			document.getElementById('footer_sound_'+n).play();
		}
	}
}
</script>
";

	//echo "  <hr />\n";

}

$pref_AlertesAvecSon=getPref($_SESSION['login'],'AlertesAvecSon',"y");
if(getSettingAOui("PeutChoisirAlerteSansSon".ucfirst($_SESSION['statut']))) {
	echo "<br />
<a name='AlertesAvecSon'></a>
<form name='change_AlertesAvecSon' method='post' action='".$_SERVER['PHP_SELF']."#AlertesAvecSon'>
	".add_token_field()."
	<fieldset style='border: 1px solid grey;
	background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Accompagnement sonore des alertes</legend>
			<p>Accepter ou non le son émis toutes les ".getSettingValue('MessagerieDelaisTest')."min lorsque le dispositif d'alerte interne signale la présence d'un message non lu&nbsp;:<br/>
			<input type='radio' id='AlertesAvecSon_y' name='AlertesAvecSon' value='y'
			 onchange=\"checkbox_change('AlertesAvecSon_y');checkbox_change('AlertesAvecSon_n');changement()\"".(($pref_AlertesAvecSon!="n") ? " checked" : "");
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />
			<label for='AlertesAvecSon_y' id='texte_AlertesAvecSon_y'>Oui</label>
			<br />

			<input type='radio' id='AlertesAvecSon_n' name='AlertesAvecSon' value='n'
			 onchange=\" checkbox_change('AlertesAvecSon_n');checkbox_change('AlertesAvecSon_y');changement()\"".(($pref_AlertesAvecSon=="n") ? " checked" : "");
	echo " tabindex='$tabindex' ";
	$tabindex++;
	echo " />
			<label for='AlertesAvecSon_n' id='texte_AlertesAvecSon_n'>Non</label>
			</p>

	".(isset($message_AlertesAvecSon) ? $message_AlertesAvecSon : "")."

			<p align='center'><input type='submit' name='enregistrer' value='Enregistrer' style='font-variant: small-caps;' tabindex='$tabindex' /></p>
		</fieldset>
	</form>\n";
	$tabindex++;

}


if(getSettingAOui("active_bulletins")) {
	$sql="SELECT 1=1 FROM signature_droits WHERE login='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$tab_signature=get_tab_signature_bull();
		/*
		echo "<pre>";
		print_r($tab_signature);
		echo "</pre>";
		*/
		echo "<br />
<a name='signature_bulletins'></a>
<form name='form_signature_bulletins' enctype=\"multipart/form-data\" method='post' action='".$_SERVER['PHP_SELF']."#signature_bulletins'>
	".add_token_field()."
	<fieldset style='border: 1px solid grey;
	background-image: url(\"../images/background/opacite50.png\");'>
		<legend style='border: 1px solid grey; background-color: white;'>Signature des bulletins</legend>";

		if((isset($tab_signature['fichier']))&&(count($tab_signature['fichier'])>0)) {
			echo "
			<p class='bold'>Un ou des fichiers de signature sont en place&nbsp;:</p>
			<ul>";
			$cpt=0;
			foreach($tab_signature['fichier'] as $id_fichier => $tmp_tab) {
				if(isset($tab_signature['fichier'][$id_fichier]['chemin'])) {
					if(file_exists($tab_signature['fichier'][$id_fichier]['chemin'])) {
						$texte="<center><img src='".$tab_signature['fichier'][$id_fichier]['chemin']."' width='200' /></center>";
						$tabdiv_infobulle[]=creer_div_infobulle('fichier_signature_'.$cpt,"Fichier de signature","",$texte,"",14,0,'y','y','n','n');

						echo "
					<li title=\"Cochez la case pour supprimer ce fichier\"><input type='checkbox' name='suppr_fichier[]' id='suppr_fichier_$cpt' value='".$id_fichier."' onchange='changement()' ";
						echo " tabindex='$tabindex' ";
						$tabindex++;
						echo "/><label for='suppr_fichier_$cpt' onmouseover=\"delais_afficher_div('fichier_signature_$cpt','y',-100,20,1000,20,20);\"> ".$tab_signature['fichier'][$id_fichier]['fichier']."</label></li>";
						$cpt++;
					}
					else {
						echo "
					<li title=\"Cochez la case pour supprimer ce fichier\"><input type='checkbox' name='suppr_fichier[]' id='suppr_fichier_$cpt' value='".$id_fichier."' onchange='changement()' ";
						echo " tabindex='$tabindex' ";
						$tabindex++;
						echo "/><label for='suppr_fichier_$cpt'> ".$tab_signature['fichier'][$id_fichier]['fichier']." <span style='color:red'>ANOMALIE : Le fichier semble absent&nbsp;???</span></label></li>";
						$cpt++;
					}
				}
			}
			echo "
			</ul>
			<p><input type='submit' value='Supprimer le ou les fichiers cochés' tabindex='$tabindex' /></p>

			".(isset($message_signature_bulletins_suppr) ? $message_signature_bulletins_suppr : "");
			$tabindex++;
		}
		else {
			echo "
			<p class='bold'>Aucun fichier de signature n'est encore en place.</p>";
		}

		echo "
		<hr width='200px' />

		<p class='bold' style='margin-top:3em;'>
			Ajouter le fichier&nbsp;: 
			<input type=\"file\" name=\"sign_file\" onchange='changement()' tabindex='$tabindex' />";
		$tabindex++;
		echo "
			<input type='hidden' name='ajout_fichier_signature' value='y' />
			<input type='submit' value='Valider' tabindex='$tabindex' />
		</p>";
		$tabindex++;

		echo (isset($message_signature_bulletins_ajout) ? $message_signature_bulletins_ajout : "")."

		<p style='margin-top:1em'><em>NOTE&nbsp;:</em> Seuls les fichiers JPEG sont autorisés.</p>";

		if((isset($tab_signature['classe']))&&(count($tab_signature['classe'])>0)) {
				echo "
		<hr width='200px' />

		<p class='bold' style='margin-top:3em;'>Associer votre(vos) fichier(s) aux classes&nbsp;:</p>
		<table class='boireaus boireaus_alt' summary='Tableau des associations Fichier/Classe'>
			<tr>
				<th>Classe</th>
				<th>Fichier</th>
			</tr>";
			foreach($tab_signature['classe'] as $id_classe => $tmp_tab) {
				echo "
			<tr>
				<td>".get_nom_classe($id_classe)."</td>
				<td>
					<select name='fich_sign_classe[$id_classe]' onchange='changement()' tabindex='$tabindex'>
						<option value='-1'>---</option>";
				$tabindex++;
				foreach($tab_signature['fichier'] as $id_fichier => $tmp_tab) {
					if(isset($tmp_tab['fichier'])) {
						echo "
							<option value='$id_fichier'";
						if((isset($tab_signature['classe'][$id_classe]['id_fichier']))&&($tab_signature['classe'][$id_classe]['id_fichier']==$id_fichier)) {
							echo " selected='selected'";
						}
						echo ">".$tmp_tab['fichier']."</option>";
					}
				}
				echo "
					</select>
				</td>
			</tr>";
			}
			echo "
		</table>

		<p><input type='submit' name='enregistrer' value='Enregistrer' style='font-variant: small-caps;' tabindex='$tabindex' /></p>

		".(isset($message_signature_bulletins_assoc_fichier_classe) ? $message_signature_bulletins_assoc_fichier_classe : "");
			$tabindex++;
		}
		else {
			echo "
		<hr width='200px' />

		<p class='bold' style='margin-top:3em;'>Vous n'avez pas de classe associée pour la signature.<br />
		Contactez l'administrateur.</p>";
		}

		echo "

		<p style='margin-top:3em;'><em>NOTES&nbsp;:</em></p>
		<ul>
			<li>
				<p>Les fichiers mis en place ne sont pas protégés contre un téléchargement abusif.<br />
				Toute personne connaissant le chemin (<em>aléatoire tout de même</em>) et le nom du fichier signature pourrait le récupérer.</p>
			</li>
			<li>
				<p>Le chemin d'un fichier mis en place peut se trouver après affichage dans une page web,... dans le cache de votre navigateur ou dans les fichiers temporaires du navigateur.<br />
				Pensez à effacer vos traces après impression de bulletins avec signature insérée.</p>
			</li>
		</ul>

	</fieldset>
</form>\n";
	}
}

echo js_checkbox_change_style('checkbox_change', 'texte_', 'y');

echo "<script type='text/javascript'>
var champs_checkbox=new Array('aff_quartiles_cn', 'aff_photo_cn', 'aff_photo_saisie_app', 'cn_avec_min_max', 'cn_avec_mediane_q1_q3', 'cn_avec_sup10', 'cn_order_by_classe', 'cn_order_by_nom', 'visibleMenu', 'visibleMenuLight', 'invisibleMenu', 'headerBas', 'headerNormal', 'footer_sound_pour_qui_perso', 'footer_sound_pour_qui_tous_profs', 'footer_sound_pour_qui_tous_personnels', 'footer_sound_pour_qui_tous', 'ouverture_auto_WinDevoirsDeLaClasse_y', 'ouverture_auto_WinDevoirsDeLaClasse_n', 'choix_encodage_csv_ascii', 'choix_encodage_csv_utf8', 'choix_encodage_csv_windows_1252', 'output_mode_pdf_D', 'output_mode_pdf_I','AlertesAvecSon_y','AlertesAvecSon_n', 'DiscTemoinIncidentAdmin', 'DiscTemoinIncidentPP', 'DiscTemoinIncidentProf', 'DiscTemoinIncidentCpe', 'DiscTemoinIncidentCpeTous', 'DiscTemoinIncidentScol', 'DiscTemoinIncidentScolTous', 'AbsProf_jamais_remplacer', 'accueil_tableau_ouverture_periode_y', 'accueil_tableau_ouverture_periode_n', 'accueil_tableau_acces_app_bull_ele_resp_y', 'accueil_tableau_acces_app_bull_ele_resp_n', $chaine_champs_checkbox_mod_discipline);
function maj_style_label_checkbox() {
	for(i=0;i<champs_checkbox.length;i++) {
		checkbox_change(champs_checkbox[i]);
	}
}
maj_style_label_checkbox();
</script>
";

echo "<hr />\n";

//==============================================================================

// Journal des connexions
echo "<a name=\"connexion\"></a>\n";
if (isset($_POST['duree'])) {
$duree = $_POST['duree'];
} else {
$duree = '7';
}

//journal_connexions($_SESSION['login'],$duree);

require("../lib/footer.inc.php");
?>
