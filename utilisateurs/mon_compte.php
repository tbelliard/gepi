<?php
/*
* $Id$
*
* Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if (($_SESSION['statut'] == 'professeur') or ($_SESSION['statut'] == 'cpe') or ($_SESSION['statut'] == 'responsable') or ($_SESSION['statut'] == 'eleve')) {
	// Mot de passe comportant des lettres et des chiffres
	$flag = 0;
} else {
	// Mot de passe comportant des lettres et des chiffres et au moins un caractère spécial
	$flag = 1;
}

if ((isset($_POST['valid'])) and ($_POST['valid'] == "yes"))  {
	$msg = '';
	$no_modif = "yes";
	$no_anti_inject_password_a = isset($_POST["no_anti_inject_password_a"]) ? $_POST["no_anti_inject_password_a"] : NULL;
	$no_anti_inject_password1 = isset($_POST["no_anti_inject_password1"]) ? $_POST["no_anti_inject_password1"] : NULL;
	$reg_password2 = isset($_POST["reg_password2"]) ? $_POST["reg_password2"] : NULL;
	$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
	$reg_show_email = isset($_POST["reg_show_email"]) ? $_POST["reg_show_email"] : "no";

	// On commence par récupérer quelques infos.
	$req = mysql_query("SELECT password, auth_mode FROM utilisateurs WHERE (login = '".$session_gepi->login."')");
	$old_password = mysql_result($req, 0, "password");
	$user_auth_mode = mysql_result($req, 0, "auth_mode");
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
						$reg = mysql_query("UPDATE utilisateurs SET change_mdp='n' WHERE login = '" . $session_gepi->login . "'");
						$no_modif = "no";
						if (isset($_POST['retour'])) {
							header("Location:../accueil.php?msg=$msg");
							die();
						}
					}
				}
			} else {
				// On fait la mise à jour sur la base de données
				$reg_password_a_c = md5($NON_PROTECT['password_a']);
				$old_password = mysql_result(mysql_query("SELECT password FROM utilisateurs WHERE (login = '".$session_gepi->login."')"), 0);
				if ($old_password == $reg_password_a_c) {
					if  ($no_anti_inject_password_a == $no_anti_inject_password1) {
						$msg = "ERREUR : Vous devez choisir un nouveau mot de passe différent de l'ancien.";
					} else if (!(verif_mot_de_passe($NON_PROTECT['password1'],$flag))) {
						$msg = "Erreur lors de la saisie du mot de passe (voir les recommandations), veuillez recommencer !";
					} else {
						$reg_password1 = md5($NON_PROTECT['password1']);
						$reg = mysql_query("UPDATE utilisateurs SET password = '$reg_password1', change_mdp='n' WHERE login = '" . $_SESSION['login'] . "'");
						if ($reg) {
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

	$call_email = mysql_query("SELECT email,show_email FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
	$user_email = mysql_result($call_email, 0, "email");
	$user_show_email = mysql_result($call_email, 0, "show_email");
	if ($user_email != $reg_email) {
		if ($user_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
			if (!isset($ldap_server)) $ldap_server = new LDAPServer;
			$write_ldap_success = $ldap_server->update_user($session_gepi->login, '', '', $reg_email, '', '', '');
		}
		$reg = mysql_query("UPDATE utilisateurs SET email = '$reg_email' WHERE login = '" . $_SESSION['login'] . "'");
		if ($reg) {
			if($msg!="") {$msg.="<br />";}
			$msg.="L'adresse e_mail a été modifiéé !";
			$no_modif = "no";
		}
	}
	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe")
	if ($user_show_email != $reg_show_email) {
	if ($reg_show_email != "no" and $reg_show_email != "yes") $reg_show_email = "no";
		$reg = mysql_query("UPDATE utilisateurs SET show_email = '$reg_show_email' WHERE login = '" . $_SESSION['login'] . "'");
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
		$calldata_photo = mysql_query("SELECT * FROM utilisateurs WHERE (login = '".$user_login."')");
		$ancien_nom = mysql_result($calldata_photo, $i_photo, "nom");
		$ancien_prenom = mysql_result($calldata_photo, $i_photo, "prenom");

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire="../photos/".getSettingValue("gepiSchoolRne")."/personnels/";
		}else{
		  $repertoire="../photos/personnels/";
		}

		//$repertoire = '../photos/personnels/';



		$ancien_code_photo = md5(strtolower($user_login));
		$nouveau_code_photo = $ancien_code_photo;

		/*
		// si on modify le nom ou le prénom de la personne et s'il y a une photo on renomme alors la photo.
		if ( $ancien_nom != $_POST['reg_nom'] or $ancien_prenom != $_POST['reg_prenom'] ) {
			$ancien_nom_fichier = $repertoire.$ancien_code_photo.'.jpg';
			$nouveau_nom_fichier = $repertoire.$nouveau_code_photo.'.jpg';

			@rename($ancien_nom_fichier, $nouveau_nom_fichier);
		}
		*/

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
						if (!preg_match('/jpg$/',strtolower($filephoto_name)) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
							if($msg!="") {$msg.="<br />";}
							$msg .= "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.\n";
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
										// si le redimensionnement des photos est activé on redimenssionne
										$source = imagecreatefromjpeg($repertoire.$nouveau_code_photo.".jpg"); // La photo est la source
										if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes")); } // On crée la miniature vide
										if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes")); } // On crée la miniature vide
		
										// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
										$largeur_source = imagesx($source);
										$hauteur_source = imagesy($source);
										$largeur_destination = imagesx($destination);
										$hauteur_destination = imagesy($destination);
		
										// On crée la miniature
										imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
										if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }
										// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
										imagejpeg($destination, $repertoire.$nouveau_code_photo.".jpg",100);
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
		$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
		$res_elenoet=mysql_query($sql);
		if(mysql_num_rows($res_elenoet)>0) {
			$lig_tmp_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			// Envoi de la photo
			if(isset($reg_no_gep)) {
				if($reg_no_gep!="") {
					if(strlen(my_ereg_replace("[0-9]","",$reg_no_gep))==0) {
						if(isset($_POST['suppr_filephoto'])) {
							if($_POST['suppr_filephoto']=='y') {

								// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
								$photo=nom_photo($reg_no_gep);

								if("$photo"!="") {
									if(@unlink($repertoire.$photo)) {
										if($msg!="") {$msg.="<br />";}
										$msg.="La photo ".$repertoire.$photo." a été supprimée. ";
										$no_modif="no";
									}
									else {
										if($msg!="") {$msg.="<br />";}
										$msg.="Echec de la suppression de la photo ".$repertoire.$photo." ";
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
						$test=mysql_query($sql);
						$nb_elenoet=mysql_num_rows($test);
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
									if ((!preg_match('/jpg$/',$filephoto_name)) || ($filephoto_type != "image/jpeg" && $filephoto_type != "image/pjpeg") ) {
										//$msg = "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.";
										if($msg!="") {$msg.="<br />";}
										$msg .= "Erreur : seuls les fichiers ayant l'extension .jpg sont autorisés.\n";
									} else {
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)) {
										$dest_file=$repertoire.$reg_no_gep.".jpg";
										//$source_file=stripslashes("$filephoto_tmp");
										$source_file=$filephoto_tmp;
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy) {
											//$msg.="Mise en place de la photo effectuée.";
											if($msg!="") {$msg.="<br />";}
											$msg.="Mise en place de la photo effectuée. <br />Il peut être nécessaire de rafraîchir la page, voire de vider le cache du navigateur<br />pour qu'un changement de photo soit pris en compte.";
											$no_modif="no";

											if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
												// si le redimensionnement des photos est activé on redimenssionne
												$source = imagecreatefromjpeg($repertoire.$reg_no_gep.".jpg"); // La photo est la source
												if (getSettingValue("active_module_trombinoscopes_rt")=='') { $destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes")); } // On crée la miniature vide
												if (getSettingValue("active_module_trombinoscopes_rt")!='') { $destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes")); } // On crée la miniature vide
			
												// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
												$largeur_source = imagesx($source);
												$hauteur_source = imagesy($source);
												$largeur_destination = imagesx($destination);
												$hauteur_destination = imagesy($destination);
			
												// On crée la miniature
												imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
												if (getSettingValue("active_module_trombinoscopes_rt")!='') { $degrees = getSettingValue("active_module_trombinoscopes_rt"); /* $destination = imagerotate($destination,$degrees); */$destination = ImageRotateRightAngle($destination,$degrees); }
												// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
												imagejpeg($destination, $repertoire.$reg_no_gep.".jpg",100);
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
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0) {
			$tab_matieres=array();
			while($lig_mat=mysql_fetch_object($test)) {
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
					$nettoyage=mysql_query($sql);

					$ordre_matieres=1;
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$_POST['matiere_principale']."', ordre_matieres='$ordre_matieres';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
					for($loop=0;$loop<count($tab_matieres);$loop++) {
						if($_POST['matiere_principale']!=$tab_matieres[$loop]) {
							$ordre_matieres++;
							$sql="INSERT INTO j_professeurs_matieres SET id_professeur='".$_SESSION["login"]."', id_matiere='".$tab_matieres[$loop]."', ordre_matieres='$ordre_matieres';";
							//echo "$sql<br />\n";
							$insert=mysql_query($sql);
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
	//======================================

	if ($no_modif == "yes") {
		if($msg!="") {$msg.="<br />";}
		$msg.="Aucune modification n'a été apportée !";
	}
}


// On appelle les informations de l'utilisateur pour les afficher :
$call_user_info = mysql_query("SELECT nom,prenom,statut,email,show_email,civilite FROM utilisateurs WHERE login='" . $_SESSION['login'] . "'");
$user_civilite = mysql_result($call_user_info, "0", "civilite");
$user_nom = mysql_result($call_user_info, "0", "nom");
$user_prenom = mysql_result($call_user_info, "0", "prenom");
$user_statut = mysql_result($call_user_info, "0", "statut");
$user_email = mysql_result($call_user_info, "0", "email");
$user_show_email = mysql_result($call_user_info, "0", "show_email");

//**************** EN-TETE *****************
$titre_page = "Gérer son compte";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

//debug_var();

// On initialise un flag pour savoir si l'utilisateur est 'éditable' ou non.
// Cela consiste à déterminer s'il s'agit d'un utilisateur local ou LDAP, et dans
// ce dernier cas à savoir s'il s'agit d'un accès en écriture ou non.
if ($session_gepi->current_auth_mode == "gepi" || $gepiSettings['ldap_write_access'] == "yes") {
	$editable_user = true;
	$affiche_bouton_submit = 'yes';
} else {
	$editable_user = false;
	$affiche_bouton_submit = 'no';
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
echo "<form enctype=\"multipart/form-data\" action=\"mon_compte.php\" method=\"post\">\n";
echo "<h2>Informations personnelles *</h2>\n";

if ($session_gepi->current_auth_mode != "gepi" && $gepiSettings['ldap_write_access'] == "yes") {
	echo "<p><span style='color: red;'>Note :</span> les modifications de mot de passe et d'email que vous effectuerez sur cette page seront propagées à l'annuaire central, et donc aux autres services qui y font appel.</p>";
}

echo "<table summary='Mise en forme'>\n";
echo "<tr><td>\n";
	echo "<table summary='Infos'>\n";
	echo "<tr><td>Identifiant GEPI : </td><td>" . $_SESSION['login']."</td></tr>\n";
	echo "<tr><td>Civilité : </td><td>".$user_civilite."</td></tr>\n";
	echo "<tr><td>Nom : </td><td>".$user_nom."</td></tr>\n";
	echo "<tr><td>Prénom : </td><td>".$user_prenom."</td></tr>\n";
	if ($editable_user) {
		echo "<tr><td>Email : </td><td><input type=text name=reg_email size=30";
		if ($user_email) { echo " value=\"".$user_email."\"";}
		echo " /></td></tr>\n";
	} else {
		echo "<tr><td>Email : </td><td>".$user_email."<input type=\"hidden\" name=\"reg_email\" value=\"".$user_email."\" /></td></tr>\n";
	}
	if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
		$affiche_bouton_submit = 'yes';
		echo "<tr><td></td><td><label for='reg_show_email' style='cursor: pointer;'><input type='checkbox' name='reg_show_email' id='reg_show_email' value='yes'";
		if ($user_show_email == "yes") echo " CHECKED";
		echo "/> Autoriser l'affichage de mon adresse email<br />pour les utilisateurs non personnels de l'établissement **</label></td></tr>\n";
	}
	echo "<tr><td>Statut : </td><td>".statut_accentue($user_statut)."</td></tr>\n";
	echo "</table>\n";
echo "</td>\n";

// PHOTO
echo "<td valign='top'>\n";
if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='scolarite')||
($_SESSION['statut']=='cpe')||
($_SESSION['statut']=='professeur')||
($_SESSION['statut']=='eleve')
) {
	$user_login=$_SESSION['login'];

	//echo "active_module_trombino_pers=".getSettingValue("active_module_trombino_pers")."<br />";
	//echo "active_module_trombinoscopes=".getSettingValue("active_module_trombinoscopes")."<br />";

	//if(getSettingValue("active_module_trombinoscopes")=='y') {
	if((($_SESSION['statut']=='eleve')&&(getSettingValue("active_module_trombinoscopes")=='y'))||
		(($_SESSION['statut']!='eleve')&&(getSettingValue("active_module_trombino_pers")=='y'))) {

		// pour module trombinoscope
		$photo_largeur_max=150;
		$photo_hauteur_max=150;

		$GepiAccesModifMaPhoto='GepiAccesModifMaPhoto'.ucfirst(strtolower($_SESSION['statut']));

		if($_SESSION['statut']=='eleve') {
			$sql="SELECT elenoet FROM eleves WHERE login='".$_SESSION['login']."';";
			$res_elenoet=mysql_query($sql);
			if(mysql_num_rows($res_elenoet)==0) {
				echo "</td></tr></table>\n";
				echo "<p><b>ERREUR !</b> Votre statut d'élève ne semble pas être confirmé dans la table 'eleves'.</p>\n";
				// A FAIRE
				// AJOUTER UNE ALERTE INTRUSION
				require("../lib/footer.inc.php");
				die();
			}
			$lig_tmp_elenoet=mysql_fetch_object($res_elenoet);
			$reg_no_gep=$lig_tmp_elenoet->elenoet;

			if($reg_no_gep!="") {
				// Récupération du nom de la photo en tenant compte des histoires des zéro 02345.jpg ou 2345.jpg
				$photo=nom_photo($reg_no_gep);

				//echo "<td align='center'>\n";
				$temoin_photo="non";
				//if("$photo"!="") {
				if($photo) {
					//$photo="../photos/eleves/".$photo;
					if(file_exists($photo)) {
						$temoin_photo="oui";
						//echo "<td>\n";
						echo "<div align='center'>\n";
						$dimphoto=redimensionne_image2($photo);
						//echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border: 3px solid #FFFFFF;" alt="Ma photo" />';
						//echo "</td>\n";
						//echo "<br />\n";
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
					echo "<div align='center'>\n";
					//echo "<span id='lien_photo' style='font-size:xx-small;'>";
					echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px;'>";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='none';return false;\">";
					if($temoin_photo=="oui") {
						//echo "Modifier le fichier photo</a>\n";
						echo "Modifier le fichier photo</a>\n";
					}
					else {
						//echo "Envoyer un fichier photo</a>\n";
						echo "Envoyer<br />un fichier<br />photo</a>\n";
					}
					//echo "</span>\n";
					echo "</div>\n";
					echo "<div id='div_upload_photo' style='display:none;'>";
					echo "<input type='file' name='filephoto' size='30' />\n";
					echo "<input type='submit' name='Envoi_photo' value='Envoyer' />\n";
					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span class='small'><b>Remarque : </b>Les photographies sont automatiquement redimensionnées (largeur : ".getSettingValue("l_resize_trombinoscopes")." pixels, hauteur : ".getSettingValue("h_resize_trombinoscopes")." pixels).<br />Afin que votre photographie ne soit pas déformée, les dimensions de celle-ci (respectivement largeur et hauteur) doivent être proportionnelles à ".getSettingValue("l_resize_trombinoscopes")." et ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span class='small'>Les photos doivent de plus être au format JPEG avec l'extension '<strong>.jpg</strong>'.</span>";
					}

					if("$photo"!="") {
						if(file_exists($photo)) {
							echo "<br />\n";
							//echo "<input type='checkbox' name='suppr_filephoto' value='y' /> Supprimer la photo existante\n";
							echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' />\n";
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
			echo "<table style='text-align: center;' summary='Photo'>\n";
			echo "<tr>\n";
			echo "<td style='text-align: center;'>\n";

				$code_photo = md5(strtolower($user_login));

				$photo=$repertoire.$code_photo.".jpg";
				$temoin_photo="non";
				if(file_exists($photo)) {
					$temoin_photo="oui";
					echo "<div align='center'>\n";
					$dimphoto=redimensionne_image2($photo);
					echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
					echo "</div>\n";
					echo "<div style='clear:both;'></div>\n";
				}
				if(getSettingValue($GepiAccesModifMaPhoto)=='yes') {
					$affiche_bouton_submit ='yes';
					echo "<div align='center'>\n";
					echo "<span style='font-size:xx-small;'>\n";
					echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';return false;\">\n";
					if($temoin_photo=="oui") {
						echo "Modifier le fichier photo</a>\n";
					}
					else {
						echo "Envoyer un fichier photo</a>\n";
					}
					echo "</span>\n";
					echo "<div id='div_upload_photo' style='display: none;'>\n";
					echo "<input type='file' name='filephoto' size='30' />\n";

					echo "<input type='submit' name='Envoi_photo' value='Envoyer' />\n";

					if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
						echo "<br /><span class='small'><b>Remarque : </b>Les photographies sont automatiquement redimensionnées (largeur : ".getSettingValue("l_resize_trombinoscopes")." pixels, hauteur : ".getSettingValue("h_resize_trombinoscopes")." pixels).<br />Afin que votre photographie ne soit pas déformée, les dimensions de celle-ci (respectivement largeur et hauteur) doivent être proportionnelles à ".getSettingValue("l_resize_trombinoscopes")." et ".getSettingValue("h_resize_trombinoscopes").".</span>"."<br /><span class='small'>Les photos doivent de plus être au format JPEG avec l'extension '<strong>.jpg</strong>'.</span>";
					}
					echo "<br />\n";
					echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' />\n";
					echo "&nbsp;<label for='suppr_filephoto' style='cursor: pointer; cursor: hand;'>Supprimer la photo existante</label>\n";
					echo "</div>\n";
					echo "</div>\n";
				}

			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
		}

	}
}
echo "</td>\n";
echo "</table>\n";
if ($affiche_bouton_submit=='yes') {
	echo "<p><input type='submit' value='Enregistrer' /></p>\n";
}
/*
//Supp ERIC
$tab_class_mat =  make_tables_of_classes_matieres();
if (count($tab_class_mat)!=0) {
	echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
	$i = 0;
	echo "<ul>";
	while ($i < count($tab_class_mat['id_c'])) {
		//echo "<li>".$tab_class_mat['nom_m'][$i]." dans la classe : ".$tab_class_mat['nom_c'][$i]."</li>";
		echo "<li>".$tab_class_mat['nom_c'][$i]." : ".$tab_class_mat['nom_m'][$i]."</li>";
		$i++;
	}
	echo "</ul>";
}
*/

// AJOUT Eric
//$groups = get_groups_for_prof($_SESSION["login"]);
$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
if (empty($groups)) {
	echo "<br /><br />\n";
} else {
	echo "<br /><br />Vous êtes professeur dans les classes et matières suivantes :";
	echo "<ul>\n";
	foreach($groups as $group) {
		echo "<li><span class='norme'><b>" . $group["classlist_string"] . "</b> : ";
		echo "" . htmlentities($group["description"]);
		echo "</span>";
		echo "</li>\n";
	}
	echo "</ul>\n";

	// Matière principale:
	/*
	$test = mysql_query("SELECT DISTINCT(jgm.id_matiere) FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (" .
		"jgp.login = '".$_SESSION["login"]."' and " .
		"jgm.id_groupe = jgp.id_groupe)");
	*/
	$sql="SELECT DISTINCT jpm.id_matiere, m.nom_complet FROM j_professeurs_matieres jpm, matieres m WHERE (jpm.id_professeur='".$_SESSION["login"]."' AND m.matiere=jpm.id_matiere) ORDER BY m.nom_complet;";
	$test=mysql_query($sql);
	$nb=mysql_num_rows($test);
	//echo "\$nb=$nb<br />";
	if ($nb>1) {
		echo "Matière principale&nbsp;: <select name='matiere_principale'>\n";
		while($lig_mat=mysql_fetch_object($test)) {
			echo "<option value='$lig_mat->id_matiere'";
			if($lig_mat->id_matiere==$_SESSION['matiere']) {echo " selected='selected'";}
			echo ">$lig_mat->nom_complet</option>\n";
		}
		echo "</select>\n";
		echo "<br />\n";
	}
}

$call_prof_classe = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
$nombre_classe = mysql_num_rows($call_prof_classe);
if ($nombre_classe != "0") {
	$j = "0";
	echo "<p>Vous êtes ".getSettingValue("gepi_prof_suivi")." dans la classe de :</p>\n";
	echo "<ul>\n";
	while ($j < $nombre_classe) {
		$id_classe = mysql_result($call_prof_classe, $j, "id");
		$classe_suivi = mysql_result($call_prof_classe, $j, "classe");
		echo "<li><b>$classe_suivi</b></li>\n";
		$j++;
	}
	echo "</ul>\n";
}





echo "<p class='small'>* Toutes les données nominatives présentes dans la base GEPI et vous concernant vous sont communiquées sur cette page.
Conformément à la loi française n° 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
vous pouvez demander auprès du Chef d'établissement ou auprès de l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a> du site,
la rectification de ces données.
Les rectifications sont effectuées dans les 48 heures hors week-end et jours fériés qui suivent la demande.";
if ($_SESSION['statut'] == "scolarite" OR $_SESSION['statut'] == "professeur" OR $_SESSION['statut'] == "cpe") {
	echo "<p class='small'>** Votre email sera affichée sur certaines pages seulement si leur affichage a été activé de manière globale par l'administrateur et si vous avez autorisé l'affichage de votre email en cochant la case appropriée. ";
	echo "Dans l'hypothèse où vous autorisez l'affichage de votre email, celle-ci ne sera accessible que par les élèves que vous avez en classe et/ou leurs responsables légaux disposant d'un identifiant pour se connecter à Gepi.</p>\n";
}
// Changement du mot de passe
if ($editable_user) {
	echo "<hr /><a name=\"changemdp\"></a><H2>Changement du mot de passe</H2>\n";
	echo "<p><b>Attention : le mot de passe doit comporter ".getSettingValue("longmin_pwd") ." caractères minimum. ";
	if ($flag == 1)
		echo "Il doit comporter au moins une lettre, au moins un chiffre et au moins un caractère spécial parmi&nbsp;: ".htmlentities($char_spec);
	else
		echo "Il doit comporter au moins une lettre et au moins un chiffre.";

	echo "<br /><span style='color: red;'>Il est fortement conseillé de ne pas choisir un mot de passe trop simple</b>.</span>";
	echo "<br /><b>Votre mot de passe est strictement personnel, vous ne devez pas le diffuser,<span style='color: red;'> il garantit la sécurité de votre travail.</b></span></p>\n";
	echo "<script type=\"text/javascript\" src=\"../lib/pwd_strength.js\"></script>";	

	echo "<table summary='Mot de passe'><tr>\n";
	echo "<td>Ancien mot de passe : </td><td><input type=password name=no_anti_inject_password_a size=20 /></td>\n";
	echo "</tr><tr>\n";
	echo "<td>Nouveau mot de passe (".getSettingValue("longmin_pwd") ." caractères minimum) :</td>";
	echo "<td> <input id=\"mypassword\" type=password name=no_anti_inject_password1 size=20 onkeyup=\"runPassword(this.value, 'mypassword');\" />";
	echo "<td>";
	echo "Complexité de votre mot de passe : ";	
	echo "		<div style=\"width: 150px;\"> ";
	echo "			<div id=\"mypassword_text\" style=\"font-size: 11px;\"></div>";
	echo "			<div id=\"mypassword_bar\" style=\"font-size: 1px; height: 3px; width: 0px; border: 1px solid white;\"></div> ";
	echo "		</div>";
	echo "</td>\n";
	echo "</td>\n";
	echo "</tr><tr>\n";
	echo "<td>Nouveau mot de passe (à confirmer) : </td><td><input type=password name=reg_password2 size=20 /></td>\n";
	echo "</tr></table>\n";
	if ((isset($_GET['retour'])) or (isset($_POST['retour'])))
		echo "<input type=\"hidden\" name=\"retour\" value=\"accueil\" />\n";
}
if ($affiche_bouton_submit=='yes')
	echo "<br /><center><input type=\"submit\" value=\"Enregistrer\" /></center>\n";
	echo "<input type=\"hidden\" name=\"valid\" value=\"yes\" />\n";
echo "</form>\n";
echo "  <hr />\n";
// Journal des connexions
echo "<a name=\"connexion\"></a>\n";
if (isset($_POST['duree'])) {
$duree = $_POST['duree'];
} else {
$duree = '7';
}
switch( $duree ) {
case 7:
$display_duree="une semaine";
break;
case 15:
$display_duree="quinze jours";
break;
case 30:
$display_duree="un mois";
break;
case 60:
$display_duree="deux mois";
break;
case 183:
$display_duree="six mois";
break;
case 365:
$display_duree="un an";
break;
case 'all':
$display_duree="le début";
break;
}

echo "<h2>Journal de vos connexions depuis <b>".$display_duree."</b>**</h2>\n";
$requete = '';
if ($duree != 'all') $requete = "and START > now() - interval " . $duree . " day";

$sql = "select START, SESSION_ID, REMOTE_ADDR, USER_AGENT, AUTOCLOSE, END from log where LOGIN = '".$_SESSION['login']."' ".$requete." order by START desc";

$day_now   = date("d");
$month_now = date("m");
$year_now  = date("Y");
$hour_now  = date("H");
$minute_now = date("i");
$seconde_now = date("s");
$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

?>
<ul>
<li>Les lignes en rouge signalent une tentative de connexion avec un mot de passe erroné.</li>
<li>Les lignes en orange signalent une session close pour laquelle vous ne vous êtes pas déconnecté correctement.</li>
<li>Les lignes en noir signalent une session close normalement.</li>
<li>Les lignes en vert indiquent les sessions en cours (cela peut correspondre à une connexion actuellement close mais pour laquelle vous ne vous êtes pas déconnecté correctement).</li>
</ul>
<table class="col" style="width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;" cellpadding="5" cellspacing="0" summary='Connexions'>
	<tr>
		<th class="col">Début session</th>
		<th class="col">Fin session</th>
		<th class="col">Adresse IP et nom de la machine cliente</th>
		<th class="col">Navigateur</th>
	</tr>
<?php
$res = sql_query($sql);
if ($res) {
	for ($i = 0; ($row = sql_row($res, $i)); $i++)
	{
		$annee_b = substr($row[0],0,4);
		$mois_b =  substr($row[0],5,2);
		$jour_b =  substr($row[0],8,2);
		$heures_b = substr($row[0],11,2);
		$minutes_b = substr($row[0],14,2);
		$secondes_b = substr($row[0],17,2);
		$date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;

		$annee_f = substr($row[5],0,4);
		$mois_f =  substr($row[5],5,2);
		$jour_f =  substr($row[5],8,2);
		$heures_f = substr($row[5],11,2);
		$minutes_f = substr($row[5],14,2);
		$secondes_f = substr($row[5],17,2);
		$date_fin = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
		$end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);

		$temp1 = '';
		$temp2 = '';
		if ($end_time > $now) {
			$temp1 = "<font color='green'>";
			$temp2 = "</font>";
		} else if (($row[4] == 1) or ($row[4] == 2) or ($row[4] == 3)) {
			//$temp1 = "<font color=orange>\n";
			$temp1 = "<font color='#FFA500'>";
			$temp2 = "</font>";
		} else if ($row[4] == 4) {
			$temp1 = "<b><font color='red'>";
			$temp2 = "</font></b>";

		}

		echo "<tr>\n";
		echo "<td class=\"col\">".$temp1.$date_debut.$temp2."</td>\n";
		if ($row[4] == 2) {
			echo "<td class=\"col\">".$temp1."Tentative de connexion<br />avec mot de passe erroné.".$temp2."</td>\n";
		}
		else {
			echo "<td class=\"col\">".$temp1.$date_fin.$temp2."</td>\n";
		}
		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
		}
		else if ($active_hostbyaddr == "no_local") {
			if ((substr($row[2],0,3) == 127) or
				(substr($row[2],0,3) == 10.) or
				(substr($row[2],0,7) == 192.168)) {
				$result_hostbyaddr = "";
			}
			else {
				$tabip=explode(".",$row[2]);
				if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
					$result_hostbyaddr = "";
				}
				else {
					$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
				}
			}
		}
		else {
			$result_hostbyaddr = "";
		}

		echo "<td class=\"col\"><span class='small'>".$temp1.$row[2].$result_hostbyaddr.$temp2. "</span></td>\n";
		echo "<td class=\"col\">".$temp1. detect_browser($row[3]) .$temp2. "</td>\n";
		echo "</tr>\n";
		flush();
	}
}


echo "</table>\n";

echo "<form action=\"mon_compte.php\" name=\"form_affiche_log\" method=\"post\">\n";
echo "Afficher le journal des connexions depuis : <select name=\"duree\" size=\"1\">\n";
echo "<option ";
if ($duree == 7) echo "selected";
echo " value=7>Une semaine</option>\n";
echo "<option ";
if ($duree == 15) echo "selected";
echo " value=15 >Quinze jours</option>\n";
echo "<option ";
if ($duree == 30) echo "selected";
echo " value=30>Un mois</option>\n";
echo "<option ";
if ($duree == 60) echo "selected";
echo " value=60>Deux mois</option>\n";
echo "<option ";
if ($duree == 183) echo "selected";
echo " value=183>Six mois</option>\n";
echo "<option ";
if ($duree == 365) echo "selected";
echo " value=365>Un an</option>\n";
echo "<option ";
if ($duree == 'all') echo "selected";
echo " value='all'>Le début</option>\n";
echo "</select>\n";
echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n";


echo "</form>\n";
echo "<p class='small'>** Les renseignements ci-dessus peuvent vous permettre de vérifier qu'une connexion pirate n'a pas été effectuée sur votre compte.
Dans le cas contraire, vous devez immédiatement en avertir l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a>.</p>\n";
require("../lib/footer.inc.php");
?>