<?php
/*
*
* Copyright 2001-2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';
if (isset($_POST['sup_logo'])) {
	check_token();

	$dest = '../images/';
	$ok = false;
	if ($f = @fopen("$dest/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$dest/.test");
	}
	if (!$ok) {
		$msg = "Problème d'écriture sur le répertoire. Veuillez signaler ce problème à l'administrateur du site";
	} else {
		$old = getSettingValue("logo_etab");
		if (($old != '') and (file_exists($dest.$old))) unlink($dest.$old);
		$msg = "Le logo a été supprimé.";
		if (!saveSetting("logo_etab", '')) $msg .= "Erreur lors de l'enregistrement dans la table setting !";

	}

}

if (isset($_POST['valid_logo'])) {
	check_token();

	$doc_file = isset($_FILES["doc_file"]) ? $_FILES["doc_file"] : NULL;
	//if (ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	//$match=array();
	//if (my_ereg("\.([^.]+)$", $doc_file['name'], $match)) {
	if (((function_exists("mb_ereg"))&&(mb_ereg("\.([^.]+)$", $doc_file['name'], $match)))||((function_exists("ereg"))&&(ereg("\.([^.]+)$", $doc_file['name'], $match)))) {
		$ext = my_strtolower($match[1]);
		if ($ext!='jpg' and $ext!='png'and $ext!='gif') {
		//if ($ext!='jpg' and $ext!='jpeg' and $ext!='png'and $ext!='gif') {
			$msg = "les seules extensions autorisées sont gif, png et jpg";
		} else {
			$dest = '../images/';
			$ok = false;
			if ($f = @fopen("$dest/.test", "w")) {
				@fputs($f, '<'.'?php $ok = true; ?'.'>');
				@fclose($f);
				include("$dest/.test");
			}
			if (!$ok) {
				$msg = "Problème d'écriture sur le répertoire IMAGES. Veuillez signaler ce problème à l'administrateur du site";
			} else {
				$old = getSettingValue("logo_etab");
				if (file_exists($dest.$old)) @unlink($dest.$old);
				if (file_exists($dest.$doc_file)) @unlink($dest.$doc_file);
				// le fichier téléchargé est renommé log_etab.xxx
				$ok = @copy($doc_file['tmp_name'], $dest."logo_etab.".$ext);
				if (!$ok) $ok = @move_uploaded_file($doc_file['tmp_name'], $dest."logo_etab.".$ext);
				if (!$ok) {
					$msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire IMAGES. Veuillez signaler ce problème à l'administrateur du site";
				} else {
					$msg = "Le fichier a été transféré.";
				}
				if (!saveSetting("logo_etab", "logo_etab.".$ext)) {
				$msg .= "Erreur lors de l'enregistrement dans la table setting !";
				}

			}
		}
	} else {
		$msg = "Le fichier sélectionné n'est pas valide !";
	}
}



if (isset($_POST['is_posted'])) {
	if ($_POST['is_posted']=='1') {
		check_token();



		// Max session length
		if (isset($_POST['sessionMaxLength'])) {
			if (!(my_ereg ("^[0-9]{1,}$", $_POST['sessionMaxLength'])) || $_POST['sessionMaxLength'] < 1) {
				$_POST['sessionMaxLength'] = 30;
			}
			if (!saveSetting("sessionMaxLength", $_POST['sessionMaxLength'])) {
				$msg .= "Erreur lors de l'enregistrement da durée max d'inactivité !";
			}
		}
		if (isset($_POST['gepiSchoolRne'])) {
			$enregistrer_gepiSchoolRne='y';
			if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
				if(($_POST['gepiSchoolRne']!='')&&(my_strtoupper($_POST['gepiSchoolRne'])!=my_strtoupper($_COOKIE['RNE']))) {
					$msg .= "Erreur lors de l'enregistrement du numéro RNE de l'établissement !<br />Le paramètre choisi risque de vous empêcher de vous connecter.<br />Enregistrement refusé!";
					$enregistrer_gepiSchoolRne='n';
				}
			}

			if($enregistrer_gepiSchoolRne=='y') {
				if (!saveSetting("gepiSchoolRne", $_POST['gepiSchoolRne'])) {
					$msg .= "Erreur lors de l'enregistrement du numéro RNE de l'établissement !";
				}
			}
		}
		if (isset($_POST['gepiYear'])) {
			if (!saveSetting("gepiYear", $_POST['gepiYear'])) {
				$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
			}
		}
		if (isset($_POST['gepiSchoolName'])) {
			if (!saveSetting("gepiSchoolName", $_POST['gepiSchoolName'])) {
				$msg .= "Erreur lors de l'enregistrement du nom de l'établissement !";
			}
		}
		if (isset($_POST['gepiSchoolStatut'])) {
			if (!saveSetting("gepiSchoolStatut", $_POST['gepiSchoolStatut'])) {
				$msg .= "Erreur lors de l'enregistrement du statut de l'établissement !";
			}
		}
		if (isset($_POST['gepiSchoolAdress1'])) {
			if (!saveSetting("gepiSchoolAdress1", $_POST['gepiSchoolAdress1'])) {
				$msg .= "Erreur lors de l'enregistrement de l'adresse !";
			}
		}
		if (isset($_POST['gepiSchoolAdress2'])) {
			if (!saveSetting("gepiSchoolAdress2", $_POST['gepiSchoolAdress2'])) {
				$msg .= "Erreur lors de l'enregistrement de l'adresse !";
			}
		}
		if (isset($_POST['gepiSchoolZipCode'])) {
			if (!saveSetting("gepiSchoolZipCode", $_POST['gepiSchoolZipCode'])) {
				$msg .= "Erreur lors de l'enregistrement du code postal !";
			}
		}
		if (isset($_POST['gepiSchoolCity'])) {
			if (!saveSetting("gepiSchoolCity", $_POST['gepiSchoolCity'])) {
				$msg .= "Erreur lors de l'enregistrement de la ville !";
			}
		}
		if (isset($_POST['gepiSchoolPays'])) {
			if (!saveSetting("gepiSchoolPays", $_POST['gepiSchoolPays'])) {
				$msg .= "Erreur lors de l'enregistrement du pays !";
			}
		}
		if (isset($_POST['gepiSchoolAcademie'])) {
			if (!saveSetting("gepiSchoolAcademie", $_POST['gepiSchoolAcademie'])) {
				$msg .= "Erreur lors de l'enregistrement de l'académie !";
			}
		}
		if (isset($_POST['gepiSchoolTel'])) {
			if (!saveSetting("gepiSchoolTel", $_POST['gepiSchoolTel'])) {
				$msg .= "Erreur lors de l'enregistrement du numéro de téléphone !";
			}
		}
		if (isset($_POST['gepiSchoolFax'])) {
			if (!saveSetting("gepiSchoolFax", $_POST['gepiSchoolFax'])) {
				$msg .= "Erreur lors de l'enregistrement du numéro de fax !";
			}
		}
		if (isset($_POST['gepiSchoolEmail'])) {
			if (!saveSetting("gepiSchoolEmail", $_POST['gepiSchoolEmail'])) {
				$msg .= "Erreur lors de l'adresse électronique !";
			}
		}
		if (isset($_POST['gepiAdminNom'])) {
			if (!saveSetting("gepiAdminNom", $_POST['gepiAdminNom'])) {
				$msg .= "Erreur lors de l'enregistrement du nom de l'administrateur !";
			}
		}
		if (isset($_POST['gepiAdminPrenom'])) {
			if (!saveSetting("gepiAdminPrenom", $_POST['gepiAdminPrenom'])) {
				$msg .= "Erreur lors de l'enregistrement du prénom de l'administrateur !";
			}
		}
		if (isset($_POST['gepiAdminFonction'])) {
			if (!saveSetting("gepiAdminFonction", $_POST['gepiAdminFonction'])) {
				$msg .= "Erreur lors de l'enregistrement de la fonction de l'administrateur !";
			}
		}
		
		if (isset($_POST['gepiAdminAdress'])) {
			if (!saveSetting("gepiAdminAdress", $_POST['gepiAdminAdress'])) {
				$msg .= "Erreur lors de l'enregistrement de l'adresse email !";
			}
		}

		if (isset($_POST['gepiAdminAdressPageLogin'])) {
			if (!saveSetting("gepiAdminAdressPageLogin", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email sur la page de login !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressPageLogin", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email sur la page de login !";
			}
		}

		if (isset($_POST['contact_admin_mailto'])) {
			if (!saveSetting("contact_admin_mailto", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de 'contact_admin_mailto' !";
			}
		}
		else {
			if (!saveSetting("contact_admin_mailto", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de 'contact_admin_mailto' !";
			}
		}

		if (isset($_POST['envoi_mail_liste'])) {
			if (!saveSetting("envoi_mail_liste", 'y')) {
				$msg .= "Erreur lors de l'enregistrement de 'envoi_mail_liste' !";
			}
		}
		else {
			if (!saveSetting("envoi_mail_liste", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de 'envoi_mail_liste' !";
			}
		}

		if (isset($_POST['gepiAdminAdressFormHidden'])) {
			if (!saveSetting("gepiAdminAdressFormHidden", 'n')) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}
		else{
			if (!saveSetting("gepiAdminAdressFormHidden", 'y')) {
				$msg .= "Erreur lors de l'enregistrement du non-affichage de adresse email dans le formulaire [Contacter l'administrateur] !";
			}
		}

		if (isset($_POST['gepiPrefixeSujetMail'])) {
			if (!saveSetting("gepiPrefixeSujetMail", $_POST['gepiPrefixeSujetMail'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre gepiPrefixeSujetMail !";
			}
		}

		if (isset($_POST['longmin_pwd'])) {
			if (!saveSetting("longmin_pwd", $_POST['longmin_pwd'])) {
				$msg .= "Erreur lors de l'enregistrement de la longueur minimale du mot de passe !";
			}
		}
		
		if (isset($_POST['mode_generation_pwd_majmin'])) {
			if (!saveSetting("mode_generation_pwd_majmin", $_POST['mode_generation_pwd_majmin'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre Min/Maj sur les mots de passe !";
			}
		}
	
		if (isset($_POST['mode_generation_pwd_excl'])) {
			if (!saveSetting("mode_generation_pwd_excl", $_POST['mode_generation_pwd_excl'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre d'exclusion des caractères prêtant à confusion sur les mots de passe !";
			}
		}
		else{
			if (!saveSetting("mode_generation_pwd_excl", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre d'exclusion des caractères prêtant à confusion sur les mots de passe !";
			}
		}

		if (isset($_POST['mode_email_resp'])) {
			if (!saveSetting("mode_email_resp", $_POST['mode_email_resp'])) {
				$msg .= "Erreur lors de l'enregistrement du mode de mise à jour des email responsables !";
			}
			else {
				$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage mode_email_resp requis';";
				$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_test)>0) {
					while($lig_ia=mysqli_fetch_object($res_test)) {
						$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if($del) {
							$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
				}

			}
		}

		if (isset($_POST['mode_email_ele'])) {
			if (!saveSetting("mode_email_ele", $_POST['mode_email_ele'])) {
				$msg .= "Erreur lors de l'enregistrement du mode de mise à jour des email élèves !";
			}
			else {
				$sql="SELECT * FROM infos_actions WHERE titre='Paramétrage mode_email_ele requis';";
				$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_test)>0) {
					while($lig_ia=mysqli_fetch_object($res_test)) {
						$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
						$del=mysqli_query($GLOBALS["mysqli"], $sql);
						if($del) {
							$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
						}
					}
				}
			}
		}

		if (isset($_POST['informer_scolarite_modif_mail'])) {
			if (!saveSetting("informer_scolarite_modif_mail", $_POST['informer_scolarite_modif_mail'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre informer_scolarite_modif_mail !";
			}
		}

		if (isset($_POST['email_dest_info_modif_mail'])) {
			if (!saveSetting("email_dest_info_modif_mail", $_POST['email_dest_info_modif_mail'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre email_dest_info_modif_mail !";
			}
		}

		if (isset($_POST['ele_tel_pers'])) {
			if (!saveSetting("ele_tel_pers", $_POST['ele_tel_pers'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_pers !";
			}
		}
		else{
			if (!saveSetting("ele_tel_pers", 'no')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_pers !";
			}
		}

		if (isset($_POST['ele_tel_port'])) {
			if (!saveSetting("ele_tel_port", $_POST['ele_tel_port'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_port !";
			}
		}
		else{
			if (!saveSetting("ele_tel_port", 'no')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_port !";
			}
		}

		if (isset($_POST['ele_tel_prof'])) {
			if (!saveSetting("ele_tel_prof", $_POST['ele_tel_prof'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_prof !";
			}
		}
		else{
			if (!saveSetting("ele_tel_prof", 'no')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_tel_prof !";
			}
		}

		if (isset($_POST['type_bulletin_par_defaut'])) {
			if(($_POST['type_bulletin_par_defaut']=='html')||($_POST['type_bulletin_par_defaut']=='pdf')) {
				if (!saveSetting("type_bulletin_par_defaut", $_POST['type_bulletin_par_defaut'])) {
					$msg .= "Erreur lors de l'enregistrement du paramètre type_bulletin_par_defaut !";
				}
			}
			else {
				$msg .= "Valeur erronée pour l'enregistrement du paramètre type_bulletin_par_defaut !";
			}
		}
	
		if (isset($_POST['exp_imp_chgt_etab'])) {
			if (!saveSetting("exp_imp_chgt_etab", $_POST['exp_imp_chgt_etab'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre exp_imp_chgt_etab !";
			}
		}
		else{
			if (!saveSetting("exp_imp_chgt_etab", 'no')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre exp_imp_chgt_etab !";
			}
		}

		if (isset($_POST['output_mode_pdf'])) {
			$output_mode_pdf=$_POST['output_mode_pdf'];
			if(!in_array($output_mode_pdf, array("D", "I"))) {
				$msg .= "Erreur : Le mode choisi pour la lecture des PDF générés est invalide !";
			}
			elseif (!saveSetting("output_mode_pdf", $_POST['output_mode_pdf'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre output_mode_pdf !";
			}
		}

		if (isset($_POST['aff_temoin_check_serveur'])) {
			if (!saveSetting("aff_temoin_check_serveur", $_POST['aff_temoin_check_serveur'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre aff_temoin_check_serveur !";
			}
		}
		else{
			if (!saveSetting("aff_temoin_check_serveur", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre aff_temoin_check_serveur !";
			}
		}

		if (isset($_POST['url_racine_gepi'])) {
			if (!saveSetting("url_racine_gepi", $_POST['url_racine_gepi'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre url_racine_gepi !";
			}
		}

		if (isset($_POST['ele_lieu_naissance'])) {
			if (!saveSetting("ele_lieu_naissance", $_POST['ele_lieu_naissance'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_lieu_naissance !";
			}
		}
		else{
			if (!saveSetting("ele_lieu_naissance", 'no')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre ele_lieu_naissance !";
			}
		}
	
		if (isset($_POST['avis_conseil_classe_a_la_mano'])) {
			if (!saveSetting("avis_conseil_classe_a_la_mano", $_POST['avis_conseil_classe_a_la_mano'])) {
				$msg .= "Erreur lors de l'enregistrement du paramètre avis_conseil_classe_a_la_mano !";
			}
		}
		else{
			if (!saveSetting("avis_conseil_classe_a_la_mano", 'n')) {
				$msg .= "Erreur lors de l'enregistrement du paramètre avis_conseil_classe_a_la_mano !";
			}
		}
	
	
		//===============================================================
	
	
		// Dénomination du professeur de suivi
		if (isset($_POST['gepi_prof_suivi'])) {
			if (!saveSetting("gepi_prof_suivi", $_POST['gepi_prof_suivi'])) {
				$msg .= "Erreur lors de l'enregistrement de gepi_prof_suivi !";
			}
		}
		
		// Dénomination des professeurs
		if (isset($_POST['denomination_professeur'])) {
			if (!saveSetting("denomination_professeur", $_POST['denomination_professeur'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_professeur !";
			}
		}
		if (isset($_POST['denomination_professeurs'])) {
			if (!saveSetting("denomination_professeurs", $_POST['denomination_professeurs'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_professeurs !";
			}
		}
		
		// Dénomination des responsables légaux
		if (isset($_POST['denomination_responsable'])) {
			if (!saveSetting("denomination_responsable", $_POST['denomination_responsable'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_responsable !";
			}
		}
		if (isset($_POST['denomination_responsables'])) {
			if (!saveSetting("denomination_responsables", $_POST['denomination_responsables'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_responsables !";
			}
		}
		
		// Dénomination des élèves
		if (isset($_POST['denomination_eleve'])) {
			if (!saveSetting("denomination_eleve", $_POST['denomination_eleve'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_eleve !";
			}
		}
		if (isset($_POST['denomination_eleves'])) {
			if (!saveSetting("denomination_eleves", $_POST['denomination_eleves'])) {
				$msg .= "Erreur lors de l'enregistrement de denomination_eleves !";
			}
		}
		// Initialiser à 'Boite'
		if (isset($_POST['gepi_denom_boite'])) {
			if (!saveSetting("gepi_denom_boite", $_POST['gepi_denom_boite'])) {
				$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite !";
			}
		}
		if (isset($_POST['gepi_denom_boite_genre'])) {
			if (!saveSetting("gepi_denom_boite_genre", $_POST['gepi_denom_boite_genre'])) {
				$msg .= "Erreur lors de l'enregistrement de gepi_denom_boite_genre !";
			}
		}

		if((isset($_POST['gepi_denom_mention']))&&($_POST['gepi_denom_mention']!="")) {
			if (!saveSetting("gepi_denom_mention", $_POST['gepi_denom_mention'])) {
				$msg .= "Erreur lors de l'enregistrement de gepi_denom_mention !";
			}
		}
		else {
			if (!saveSetting("gepi_denom_mention", "mention")) {
				$msg .= "Erreur lors de l'initialisation de gepi_denom_mention !";
			}
		}

		if (isset($_POST['gepi_stylesheet'])) {
			if (!saveSetting("gepi_stylesheet", $_POST['gepi_stylesheet'])) {
				$msg .= "Erreur lors de l'enregistrement de l'année scolaire !";
			}
		}
		
		if (isset($_POST['num_enregistrement_cnil'])) {
			if (!saveSetting("num_enregistrement_cnil", $_POST['num_enregistrement_cnil'])) {
				$msg .= "Erreur lors de l'enregistrement du numéro d'enregistrement à la CNIL !";
			}
		}

		$format_login_ok=0;
		if (isset($_POST['mode_generation_login'])) {
			if(!check_format_login($_POST['mode_generation_login'])) {
				$msg .= "Format de login invalide pour les personnels !";
			}
			else {
				if (!saveSetting("mode_generation_login", $_POST['mode_generation_login'])) {
					$msg .= "Erreur lors de l'enregistrement du mode de génération des logins personnels !";
				}
				else {
					$nbre_carac = mb_strlen($_POST['mode_generation_login']);
					$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login'";
					$modif_maxlong = mysqli_query($GLOBALS["mysqli"], $req);

					$format_login_ok++;				}
			}
		}

		if (isset($_POST['mode_generation_login_casse'])) {
			if(($_POST['mode_generation_login_casse']!='min')&&($_POST['mode_generation_login_casse']!='maj')) {
				$msg .= "Casse invalide pour le format de login invalide pour les personnels !";
			}
			else {
				if (!saveSetting("mode_generation_login_casse", $_POST['mode_generation_login_casse'])) {
					$msg .= "Erreur lors de l'enregistrement de la casse du format de login des personnels !";
				}
			}
		}

		if (isset($_POST['mode_generation_login_eleve'])) {
			if(!check_format_login($_POST['mode_generation_login_eleve'])) {
				$msg .= "Format de login invalide pour les élèves !";
			}
			else {
				if (!saveSetting("mode_generation_login_eleve", $_POST['mode_generation_login_eleve'])) {
					$msg .= "Erreur lors de l'enregistrement du mode de génération des logins élèves !";
				}
				else {
					$nbre_carac = mb_strlen($_POST['mode_generation_login_eleve']);
					$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login_eleve'";
					$modif_maxlong = mysqli_query($GLOBALS["mysqli"], $req);

					$format_login_ok++;
				}
			}
		}

		if (isset($_POST['mode_generation_login_eleve_casse'])) {
			if(($_POST['mode_generation_login_eleve_casse']!='min')&&($_POST['mode_generation_login_eleve_casse']!='maj')) {
				$msg .= "Casse invalide pour le format de login invalide pour les élèves !";
			}
			else {
				if (!saveSetting("mode_generation_login_eleve_casse", $_POST['mode_generation_login_eleve_casse'])) {
					$msg .= "Erreur lors de l'enregistrement de la casse du format de login des élèves !";
				}
			}
		}

		if (isset($_POST['mode_generation_login_responsable'])) {
			if(!check_format_login($_POST['mode_generation_login_responsable'])) {
				$msg .= "Format de login invalide pour les responsables !";
			}
			else {
				if (!saveSetting("mode_generation_login_responsable", $_POST['mode_generation_login_responsable'])) {
					$msg .= "Erreur lors de l'enregistrement du mode de génération des logins responsables !";
				}
				else {
					$nbre_carac = mb_strlen($_POST['mode_generation_login_responsable']);
					$req = "UPDATE setting SET value = '".$nbre_carac."' WHERE name = 'longmax_login_responsable'";
					$modif_maxlong = mysqli_query($GLOBALS["mysqli"], $req);

					$format_login_ok++;
				}
			}
		}

		if($format_login_ok==3) {
			$sql="SELECT * FROM infos_actions WHERE titre='Format des logins générés';";
			$test_ia=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_ia)>0) {
				while($lig=mysqli_fetch_object($test_ia)) {
					del_info_action($lig->id);
				}
			}
		}

		if (isset($_POST['mode_generation_login_responsable_casse'])) {
			if(($_POST['mode_generation_login_responsable_casse']!='min')&&($_POST['mode_generation_login_responsable_casse']!='maj')) {
				$msg .= "Casse invalide pour le format de login invalide pour les responsables !";
			}
			else {
				if (!saveSetting("mode_generation_login_responsable_casse", $_POST['mode_generation_login_responsable_casse'])) {
					$msg .= "Erreur lors de l'enregistrement de la casse du format de login des responsables !";
				}
			}
		}

		if (isset($_POST['FiltrageStrictAlphaNomPrenomPourLogin'])) {
			if(($_POST['FiltrageStrictAlphaNomPrenomPourLogin']!='y')&&($_POST['FiltrageStrictAlphaNomPrenomPourLogin']!='n')) {
				$msg .= "Choix invalide pour le filtrage des caractères lors de la génération de login !";
			}
			else {
				if (!saveSetting("FiltrageStrictAlphaNomPrenomPourLogin", $_POST['FiltrageStrictAlphaNomPrenomPourLogin'])) {
					$msg .= "Erreur lors de l'enregistrement du choix de filtrage des caractères lors de la génération de login !";
				}
			}
		}

		if (isset($_POST['unzipped_max_filesize'])) {
			$unzipped_max_filesize=$_POST['unzipped_max_filesize'];
			if(mb_substr($unzipped_max_filesize,0,1)=="-") {$unzipped_max_filesize=-1;}
			elseif(mb_strlen(my_ereg_replace("[0-9]","",$unzipped_max_filesize))!=0) {
				$unzipped_max_filesize=10;
				$msg .= "Caractères invalides pour le paramètre unzipped_max_filesize<br />Initialisation à 10 Mo !";
			}
		
			if (!saveSetting("unzipped_max_filesize", $unzipped_max_filesize)) {
				$msg .= "Erreur lors de l'enregistrement du paramètre unzipped_max_filesize !";
			}
		}


		if (isset($_POST['bul_rel_nom_matieres'])) {
			$bul_rel_nom_matieres=$_POST['bul_rel_nom_matieres'];
			if (!saveSetting("bul_rel_nom_matieres", $bul_rel_nom_matieres)) {
				$msg .= "Erreur lors de l'enregistrement du paramètre bul_rel_nom_matieres !";
			}
		}



		if (isset($_POST['delais_apres_cloture'])) {
			$delais_apres_cloture=$_POST['delais_apres_cloture'];
			if (!(my_ereg ("^[0-9]{1,}$", $delais_apres_cloture)) || $delais_apres_cloture < 0) {
				//$delais_apres_cloture=0;
				$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
			}
			else {
				if (!saveSetting("delais_apres_cloture", $delais_apres_cloture)) {
					$msg .= "Erreur lors de l'enregistrement de delais_apres_cloture !";
				}
			}
		}
		
		if (isset($_POST['acces_app_ele_resp'])) {
			$acces_app_ele_resp=$_POST['acces_app_ele_resp'];
			if (!saveSetting("acces_app_ele_resp", $acces_app_ele_resp)) {
				$msg .= "Erreur lors de l'enregistrement de acces_app_ele_resp !";
			}
		}



	}
}


if (isset($_POST['gepi_pmv'])) {
	check_token();

	if (!saveSetting("gepi_pmv", $_POST['gepi_pmv'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_pmv !";
	}
}

if (isset($_POST['ne_pas_tester_version_via_git_log'])) {
	check_token();

	if (!saveSetting("ne_pas_tester_version_via_git_log", $_POST['ne_pas_tester_version_via_git_log'])) {
		$msg .= "Erreur lors de l'enregistrement de ne_pas_tester_version_via_git_log !";
	}
}

if (isset($_POST['gepi_en_production'])) {
	check_token();

	if (!saveSetting("gepi_en_production", $_POST['gepi_en_production'])) {
		$msg .= "Erreur lors de l'enregistrement de gepi_en_production !";
	}
}

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";


//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Paramètres généraux";
if(isset ($themessage)) $messageEnregistrer = $themessage;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

?>

<form action="param_gen.php" method="post" id="form1" style="width: 100%;">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>

	<p>
<?php
echo add_token_field();
?>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolRne' class="cellTab70">
			Année scolaire :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiYear" size="20" value="<?php echo(getSettingValue("gepiYear")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolRne' class="cellTab70">
			Numéro RNE de l'établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolRne" size="8" value="<?php echo(getSettingValue("gepiSchoolRne")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolName' class="cellTab70">
			Nom de l'établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolName" size="20" value="<?php echo(getSettingValue("gepiSchoolName")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolAdress1' class="cellTab70">
			Statut de l'établissement :<br />
			(<span style='font-style:italic;font-size:x-small'>utilisé pour certains documents officiels</span>)
		</label>
		<span class="cellTab">
			<select name='gepiSchoolStatut' onchange='changement()'>
				<option value='public'<?php if (getSettingValue("gepiSchoolStatut")=='public') echo " selected='selected'"; ?>>
					établissement public
				</option>
				<option value='prive_sous_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_sous_contrat') echo " selected='selected'"; ?>>
					établissement privé sous contrat
				</option>
				<option value='prive_hors_contrat'<?php if (getSettingValue("gepiSchoolStatut")=='prive_hors_contrat') echo " selected='selected'"; ?>>
					établissement privé hors contrat
				</option>
			</select>
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolAdress1' class="cellTab70">
			Adresse de l'établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolAdress1" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress1")); ?>" onchange='changement()' />
			<br />
			<input type="text" name="gepiSchoolAdress2" size="40" value="<?php echo(getSettingValue("gepiSchoolAdress2")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolZipCode' class="cellTab70">
			Code postal :
		</label>
		<span class="cellTab">
			<input type="text"
			   name="gepiSchoolZipCode" 
			   size="20" 
			   value="<?php echo(getSettingValue("gepiSchoolZipCode")); ?>" 
			   onchange='changement()' />	
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolCity' class="cellTab70">
			Ville :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolCity" size="20" value="<?php echo(getSettingValue("gepiSchoolCity")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolPays' class="cellTab70">
			Pays :<br />
			(<span style='font-style:italic;font-size:x-small'>
				Le pays est utilisé pour comparer avec celui des responsables dans les blocs adresse des courriers adressés 
				aux responsables
			</span>)
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolPays" size="20" value="<?php echo(getSettingValue("gepiSchoolPays")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolAcademie' class="cellTab70">
			Académie :<br />
		(<span style='font-style:italic;font-size:x-small'>utilisé pour certains documents officiels</span>)
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolAcademie" size="20" value="<?php echo(getSettingValue("gepiSchoolAcademie")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolTel' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			Téléphone établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolTel" size="20" value="<?php echo(getSettingValue("gepiSchoolTel")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolFax' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			Fax établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolFax" size="20" value="<?php echo(getSettingValue("gepiSchoolFax")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiSchoolEmail' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			E-mail établissement :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiSchoolEmail" size="20" value="<?php echo(getSettingValue("gepiSchoolEmail")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminNom' class="cellTab70">
			Nom de l'administrateur du site :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiAdminNom" size="20" value="<?php echo(getSettingValue("gepiAdminNom")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminPrenom' class="cellTab70">
			Prénom de l'administrateur du site :
		</label>
		<span class="cellTab">
			<input type="text" name="gepiAdminPrenom" size="20" value="<?php echo(getSettingValue("gepiAdminPrenom")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminFonction' class="cellTab70">
			Fonction de l'administrateur du site :
		</label>
		<span class="cellTab plusPetit">
			<input type="text" name="gepiAdminFonction" size="20" value="<?php echo(getSettingValue("gepiAdminFonction")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminAdress' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			Email de l'administrateur du site :
		</label>
		<span class="cellTab plusPetit">
			<input type="text" id="gepiAdminAdress" name="gepiAdminAdress" size="20" value="<?php echo(getSettingValue("gepiAdminAdress")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminAdressPageLogin' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			Faire apparaitre le lien [Contacter l'administrateur] sur la page de login :
		</label>
		<span class="cellTab plusPetit">
			<input type="checkbox" id='gepiAdminAdressPageLogin' name="gepiAdminAdressPageLogin" value="y"
	<?php
		if(getSettingValue("gepiAdminAdressPageLogin")!='n'){echo " checked='checked'";}
	?>
	onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepiAdminAdressFormHidden' style='cursor: pointer;display: table-cell; width: 70%; vertical-align: middle;'>
			Faire apparaitre l'adresse de l'administrateur dans le formulaire [Contacter l'administrateur] :
		</label>
		<span class="cellTab plusPetit">
			<input type="checkbox" name="gepiAdminAdressFormHidden" id="gepiAdminAdressFormHidden" value="n"
	<?php
		if(getSettingValue("gepiAdminAdressFormHidden")!='y'){echo " checked='checked'";}
	?>
	onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<label for='gepiPrefixeSujetMail' class="cellTab70">
			Ajouter un préfixe au sujet des mails envoyés par Gepi :<br />
			<span class="plusPetit">Cela peut être utile si vous recevez des mails de plusieurs Gepi.<br />
			Notez que le préfixe 'GEPI :' est de toutes façons ajouté.</span>
		</label>
		<span class="cellTab plusPetit">
			<input type="text" id='gepiPrefixeSujetMail' name="gepiPrefixeSujetMail" value="<?php echo getSettingValue('gepiPrefixeSujetMail');?>" onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<label for='contact_admin_mailto' class="cellTab70">
			Remplacer le formulaire [Contacter l'administrateur] par un lien mailto :
		</label>
		<span class="cellTab plusPetit">
			<input type="checkbox" id='contact_admin_mailto' name="contact_admin_mailto" value="y"
	<?php
		if(getSettingValue("contact_admin_mailto")=='y'){echo " checked='checked'";}
	?>
	onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<label for='envoi_mail_liste' class="cellTab70">
			Permettre d'envoyer des mails à une liste d'élèves :
			<br />
			(<em style='font-size: small'>sous réserve que les mails soient remplis</em>)
			<br />
			<span style='font-size: small' title='Cependant, en mettant tous les destinataires en BCC (Blind Carbon Copy, soit Copie Cachée), vous pouvez conserver la confidentialité des destinataires (il faut toutefois la plupart du temps au moins un destinataire non caché pour que l&apos;envoi soit accepté).'>
				Nous attirons votre attention sur le fait qu'envoyer un mail à une liste d'utilisateurs via un lien mailto 
				permet à chaque élève de connaitre les email des autres élèves sans que l'autorisation de divulgation 
				ou non paramétrée dans <strong>Gérer mon compte</strong> soit prise en compte.
			</span>
		</label>
		<span class="cellTab plusPetit">
			<input type="checkbox" id='envoi_mail_liste' name="envoi_mail_liste" value="y"
		<?php
			if(getSettingValue("envoi_mail_liste")=='y'){echo " checked='checked'";}
		?>
		onchange='changement()' />
		</span>
	</p>
		
	<p class="ligneCaps">
		<label for='sessionMaxLength' class="cellTab70">
			<a name='sessionMaxLength'></a>Durée maximum d'inactivité : <br />
			<span class='small'>(<em>Durée d'inactivité, en minutes, au bout de laquelle un utilisateur est automatiquement déconnecté de Gepi.</em>) <b>Attention</b>, la variable <b>session.maxlifetime</b> dans le fichier <b>php.ini</b> est réglée à <?php 
				$session_gc_maxlifetime=ini_get("session.gc_maxlifetime");
				$session_gc_maxlifetime_minutes=$session_gc_maxlifetime/60;

				if((getSettingValue("sessionMaxLength")!="")&&($session_gc_maxlifetime_minutes<getSettingValue("sessionMaxLength"))) {
					echo "<span style='color:red;'>".$session_gc_maxlifetime." secondes</span>, soit un maximum de <span style='color:red; font-weight:bold;'>".$session_gc_maxlifetime_minutes." minutes</span> pour la session (<a href='../mod_serveur/test_serveur.php#reglages_php'>*</a>).";
				}
				else {
					echo $session_gc_maxlifetime." secondes, soit un maximum de ".$session_gc_maxlifetime_minutes." minutes pour la session.";
				}
			?></span>
		</label>
		<span class="cellTab plusPetit">
			<input type="text" 
				   name="sessionMaxLength" 
				   id="sessionMaxLength" 
				   size="6" 
				   value="<?php echo(getSettingValue("sessionMaxLength")); ?>" 
				   onchange="changement();min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');" 
				   onblur="min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');"
				   onclick="min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');"
				   onkeydown="clavier_2(this.id,event,1,600);min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');"
				   onkeyup="min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');"
				   />&nbsp;min
			<span id='sessionMaxLength_en_minutes'<?php

		if(getSettingValue("sessionMaxLength")>$session_gc_maxlifetime_minutes) {echo " style='color:red; text-decoration: blink;'";}
		?>>
			</span>
		</span>
	</p>
		
	<p class="ligneCaps">
		<label for='longmin_pwd' class="cellTab70">
			Longueur minimale du mot de passe :
		</label>
		<span class="cellTab plusPetit">
			<input type="text" name="longmin_pwd" id="longmin_pwd" size="20" value="<?php echo(getSettingValue("longmin_pwd")); ?>" onchange='changement()' onkeydown="clavier_2(this.id,event,1,50)" />
		</span>
	</p>

	<?php
		// insert into setting set name='use_custom_denominations', value='yes';
		if(getSettingAOui('use_custom_denominations')) {
			$use_custom_denominations=true;
		}
		$use_custom_denominations=true;
		if (isset($use_custom_denominations) && $use_custom_denominations) {
	?>

	<br />

	<p class="ligneCaps">Personnaliser certains libellés (<em>étudiants au lieu d'élèves, par ex.</em>).<br />
	(<em>cette fonctionnalité est en cours d'implémentation, pas encore étendue à toutes les pages - ne pas hésiter à signaler les manques criants sur la liste 'users'</em>).</p>

	<p class="ligneCaps">
		<label for='denomination_professeur' class="cellTab70">
			Dénomination des professeurs :
		</label>
		<span class="cellTab plusPetit">
			Sing. :
			<input type="text" id="denomination_professeur" name="denomination_professeur" size="20" value="<?php echo(getSettingValue("denomination_professeur")); ?>" onchange='changement()' />
			<br/>
			Pluriel :
			<input type="text" name="denomination_professeurs" size="20" value="<?php echo(getSettingValue("denomination_professeurs")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='denomination_eleve' class="cellTab70">
			Dénomination des élèves :
		</label>
		<span class="cellTab plusPetit">
			Sing. :
			<input type="text" name="denomination_eleve" size="20" value="<?php echo(getSettingValue("denomination_eleve")); ?>" />
			<br/>
			Pluriel :
			<input type="text" name="denomination_eleves" size="20" value="<?php echo(getSettingValue("denomination_eleves")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='denomination_eleve' class="cellTab70">
			Dénomination des responsables légaux :
		</label>
		<span class="cellTab plusPetit">
			Sing. :
			<input type="text" name="denomination_responsable" size="20" value="<?php echo(getSettingValue("denomination_responsable")); ?>" onchange='changement()' />
			<br/>
			Pluriel :
			<input type="text" name="denomination_responsables" size="20" value="<?php echo(getSettingValue("denomination_responsables")); ?>" onchange='changement()' />
		</span>
	</p>

	<br />
<?php } ?>
	
	<p class="ligneCaps">
		<label for='gepi_prof_suivi' class="cellTab70">
			Dénomination du professeur chargé du suivi des élèves :
		</label>
		<span class="cellTab plusPetit">
			<input type="text" name="gepi_prof_suivi" size="20" value="<?php echo(getSettingValue("gepi_prof_suivi")); ?>" onchange='changement()' />
		</span>
	</p>
	
	<p class="ligneCaps">
		<label for='gepi_denom_boite' class="cellTab70">
			Désignation des boites/conteneurs/emplacements/sous-matières :
		</label>
		<span class="cellTab plusPetit">
			<input type="text" id="gepi_denom_boite" name="gepi_denom_boite" size="20" value="<?php echo(getSettingValue("gepi_denom_boite")); ?>" onchange='changement()' />
			<br />
			<span class="cellTab">Genre :</span>
			<span class="cellTab">
				<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_m" value="m" <?php if(getSettingValue("gepi_denom_boite_genre")=="m"){echo "checked='checked'";} ?> onchange='changement()' />
				<label for='gepi_denom_boite_genre_m' style='cursor: pointer;'>Masculin</label>
				<br />
				<input type="radio" name="gepi_denom_boite_genre" id="gepi_denom_boite_genre_f" value="f" <?php if(getSettingValue("gepi_denom_boite_genre")=="f"){echo "checked='checked'";} ?> onchange='changement()' />
				<label for='gepi_denom_boite_genre_f' style='cursor: pointer;'>Féminin</label>
			</span>
		</span>
	</p>

	<p class="ligneCaps">
		<label for='gepi_denom_mention' class="cellTab70">
			<a name='gepi_denom_mention'></a>
			Désignation des "mentions" pouvant être saisies avec l'avis du conseil de classe :
			<br />
			(<em>terme au singulier</em>)
			<br />
			<a href='../saisie/saisie_mentions.php' <?php echo "onclick=\"return confirm_abandon (this, change, '$themessage')\""; ?>>
				Définir des "mentions"
			</a>
		</label>
		<span class="cellTab plusPetit">
			<input type="text" 
				   id="gepi_denom_mention" 
				   name="gepi_denom_mention" 
				   size="20" 
				   value="<?php	$gepi_denom_mention=getSettingValue("gepi_denom_mention");
					if($gepi_denom_mention=="") {
						$gepi_denom_mention="mention";
					}
					echo $gepi_denom_mention; ?>" 
				   onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<label for='mode_generation_login' class="cellTab70">
			<a name='format_login_pers'></a>
			Mode de génération automatique des logins personnels&nbsp;:
		</label>
		<span class="cellTab plusPetit">
		<?php
			$default_login_gen_type=getSettingValue('mode_generation_login');
			if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type="nnnnnnnp";}
			echo champ_input_choix_format_login('mode_generation_login', $default_login_gen_type);
		?>
		</span>
	</p>

	<p class="ligneCaps">
		<label for='mode_generation_login_eleve' class="cellTab70">
			<a name='format_login_ele'></a>
			Mode de génération automatique des logins élèves&nbsp;:
		</label>
		<span class="cellTab plusPetit">
			<?php
				$default_login_gen_type=getSettingValue('mode_generation_login_eleve');
				if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type="nnnnnnnnn_p";}
				//echo champs_select_choix_format_login('mode_generation_login_eleve', $mode_generation_login_eleve);
				echo champ_input_choix_format_login('mode_generation_login_eleve', $default_login_gen_type);
			?>
		</span>
	</p>

	<p class="ligneCaps">
		<label for='mode_generation_login_responsable' class="cellTab70">
			<a name='format_login_resp'></a>
			Mode de génération automatique des logins responsables&nbsp;:
		</label>
		<span class="cellTab plusPetit">
			<?php
				$default_login_gen_type=getSettingValue('mode_generation_login_responsable');
				if(($default_login_gen_type=='')||(!check_format_login($default_login_gen_type))) {$default_login_gen_type="nnnnnnnnnnnnnnnnnnnn";}
				echo champ_input_choix_format_login('mode_generation_login_responsable', $default_login_gen_type);
			?>
		</span>
	</p>

	<p class="ligneCaps">
		<a name='filtrage_strict_nom_prenom_pour_login'></a>
		Filtrer strictement des noms et prénoms pour la génération de logins&nbsp;:<br />
		(<em>on ne garde que les caractères alphabétiques (on supprime les espaces, tirets,...)</em>)
		<span class="cellTab plusPetit">
			<span class="cellTab">
				<input type="radio" name="FiltrageStrictAlphaNomPrenomPourLogin" id="FiltrageStrictAlphaNomPrenomPourLogin_y" value="y" <?php if(getSettingAOui("FiltrageStrictAlphaNomPrenomPourLogin")){echo "checked='checked'";} ?> onchange='changement()' />
				<label for='FiltrageStrictAlphaNomPrenomPourLogin_y' style='cursor: pointer;'>Oui</label>
				<br />
				<input type="radio" name="FiltrageStrictAlphaNomPrenomPourLogin" id="FiltrageStrictAlphaNomPrenomPourLogin_n" value="n" <?php if(!getSettingAOui("FiltrageStrictAlphaNomPrenomPourLogin")){echo "checked='checked'";} ?> onchange='changement()' />
				<label for='FiltrageStrictAlphaNomPrenomPourLogin_n' style='cursor: pointer;'>Non</label>
			</span>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			Mode de génération des mots de passe :
			<br />
			(<em style='font-size:small;'>
				Jeu de caractères à utiliser en plus des caractères numériques
			</em>)
		</span>
		<span class="cellTab plusPetit">
			<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_y" value="y" <?php if((getSettingValue("mode_generation_pwd_majmin")=="y")||(getSettingValue("mode_generation_pwd_majmin")=="")) {echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_generation_pwd_majmin_y' style='cursor: pointer;'>
				Majuscules et minuscules
			</label>
			<br />
			<input type="radio" name="mode_generation_pwd_majmin" id="mode_generation_pwd_majmin_n" value="n" <?php if(getSettingValue("mode_generation_pwd_majmin")=="n"){echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_generation_pwd_majmin_n' style='cursor: pointer;'>
				Minuscules seulement
			</label>
			<br />

			<span style="display: table-cell; vertical-align: middle;">
				<input type="checkbox" name="mode_generation_pwd_excl" id="mode_generation_pwd_excl" value="y" <?php if(getSettingValue("mode_generation_pwd_excl")=="y") {echo " checked='checked'";} ?> onchange='changement()' />
				<label for='mode_generation_pwd_excl' style='cursor: pointer;'>
					Exclure les caractères prêtant à confusion (<em>i, 1, l, I, 0, O, o</em>)
				</label>
				<br />
			</span>
		</span>
	</p>
	
	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='mode_email_resp'></a>
			Mode de mise à jour des emails responsables :
			<br />
			(<em style='font-size:small;'>
				Les responsables peuvent avoir un email dans deux tables s'ils disposent d'un compte utilisateur 
				['resp_pers' et 'utilisateurs']. Ces email peuvent donc se trouver non synchronisés entre les tables
			</em>)
		</span>
		<span class="cellTab plusPetit">
			<input type="radio" name="mode_email_resp" id="mode_email_resp_sconet" value="sconet" <?php if((getSettingValue("mode_email_resp")=="sconet")||(getSettingValue("mode_email_resp")=="")) {echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_email_resp_sconet' style='cursor: pointer;'>Mise à jour de l'email via Sconet uniquement</label>
			<br />
			<input type="radio" name="mode_email_resp" id="mode_email_resp_mon_compte" value="mon_compte" <?php if(getSettingValue("mode_email_resp")=="mon_compte"){echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_email_resp_mon_compte' style='cursor: pointer;'>
				Mise à jour de l'email depuis Gérer mon compte uniquement
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;
				(<em>modifications dans Sconet non prises en compte</em>)
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;
				(<em>
					sauf sso, voir dans ce cas [<a href='options_connect.php#cas_attribut_email'>Options de connexion</a>]
				</em>)
			</label>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='mode_email_ele'></a>
			Mode de mise à jour des emails élèves :<br />
			(<em style='font-size:small;'>
				Les élèves peuvent avoir un email dans deux tables s'ils disposent d'un compte utilisateur 
				('eleves' et 'utilisateurs'). Ces email peuvent donc se trouver non synchronisés entre les tables
			</em>)
		</span>
		<span class="cellTab plusPetit">
			<input type="radio" name="mode_email_ele" id="mode_email_ele_sconet" value="sconet" <?php if((getSettingValue("mode_email_ele")=="sconet")||(getSettingValue("mode_email_ele")=="")) {echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_email_ele_sconet' style='cursor: pointer;'>
				Mise à jour de l'email via Sconet uniquement
			</label>
			<br />
			<input type="radio" name="mode_email_ele" id="mode_email_ele_mon_compte" value="mon_compte" <?php if(getSettingValue("mode_email_ele")=="mon_compte"){echo " checked='checked'";} ?> onchange='changement()' />
			<label for='mode_email_ele_mon_compte' style='cursor: pointer;'>
				Mise à jour de l'email depuis Gérer mon compte uniquement
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;(<em>modifications dans Sconet non prises en compte</em>)
				<br />
				&nbsp;&nbsp;&nbsp;&nbsp;(<em>sauf sso, voir dans ce cas 
					[<a href='options_connect.php#cas_attribut_email'>Options de connexion</a>]
				</em>)
			</label>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='informer_scolarite_modif_mail'></a>
			Dans le cas où vous choisissez ci-dessus Mise à jour du mail depuis Gérer mon compte, envoyer un mail pour signaler le changement de mail de façon à permettre de reporter la saisie dans Sconet.
		</span>
		<span class="cellTab plusPetit">
		<input type="radio" name="informer_scolarite_modif_mail" id="informer_scolarite_modif_mail_y" value="y" <?php if((getSettingValue("informer_scolarite_modif_mail")=="y")||(getSettingValue("informer_scolarite_modif_mail")=="")) {echo 'checked';} ?> onchange='changement()' /> <label for='informer_scolarite_modif_mail_y' style='cursor: pointer;'>Oui</label> - 
		<input type="radio" name="informer_scolarite_modif_mail" id="informer_scolarite_modif_mail_n" value="n" <?php if(getSettingValue("informer_scolarite_modif_mail")=="n"){echo 'checked';} ?> onchange='changement()' /> <label for='informer_scolarite_modif_mail_n' style='cursor: pointer;'>Non</label><br />
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='email_dest_info_modif_mail'></a>
			Adresse mail du destinataire de l'information de changement de mail
		</span>
		<span class="cellTab plusPetit">
		<input type="text" name="email_dest_info_modif_mail" value="<?php if(getSettingValue("email_dest_info_modif_mail")!="") {echo getSettingValue("email_dest_info_modif_mail");} else {echo getSettingValue('gepiSchoolEmail');} ?>" onchange='changement()' /><br />
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			Type de bulletins par défaut&nbsp;:
		</span>
		<span class="cellTab plusPetit">
			<input type="radio" id='type_bulletin_par_defaut_pdf' name="type_bulletin_par_defaut" value="pdf"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")=='pdf') {echo " checked='checked'";}
		?>
		onchange='changement()' />
			<label for='type_bulletin_par_defaut_pdf'>&nbsp;PDF</label>
			<br />
			<input type="radio" id='type_bulletin_par_defaut_html' name="type_bulletin_par_defaut" value="html"
		<?php
			if(getSettingValue("type_bulletin_par_defaut")!='pdf') {echo " checked='checked'";}
		?>
		onchange='changement()' />
			<label for='type_bulletin_par_defaut_html'>&nbsp;HTML</label>
		</span>
	</p>
	
	<p class="ligneCaps">
		<span class="cellTab70">
			Feuille de style à utiliser :
		</span>
		<span class="cellTab plusPetit">
			<select name='gepi_stylesheet' onchange='changement()'>
				<option value='style'<?php if (getSettingValue("gepi_stylesheet")=='style') echo " selected='selected'"; ?>>
					Nouveau design
				</option>
				<option value='style_old'<?php if (getSettingValue("gepi_stylesheet")=='style_old') echo " selected='selected'"; ?>>
					Design proche des anciennes versions (1.4.*)
				</option>
			</select>
		</span>
	</p>
	
	<p class="ligneCaps">
		<span class="cellTab70">
<?php if(file_exists("../lib/pclzip.lib.php")){ ?>
			Taille maximale extraite des fichiers dézippés:
			<br />
			(<em style='font-size:small;'>
				Un fichier dézippé peut prendre énormément de place.<br /> 
				Par prudence, il convient de fixer une limite à la taille d'un fichier extrait.<br />
				En mettant zéro, vous ne fixez aucune limite.<br />
				En mettant une valeur négative, vous désactivez le désarchivage
			</em>)
		</span>
		<span class="cellTab plusPetit">
			<input type='text' 
				   name='unzipped_max_filesize' 
				   id='unzipped_max_filesize' 
				   value='<?php 
$unzipped_max_filesize=getSettingValue('unzipped_max_filesize'); 
if($unzipped_max_filesize==""){
	echo '10';
} else {
	echo $unzipped_max_filesize;
} ?>' 
				   size='3'
				   onchange='changement()'
				   onkeydown="clavier_2(this.id,event,0,600)" />
			Mo
<?php } else { ?>
			En mettant en place la bibliothèque 'pclzip.lib.php' dans le dossier '/lib/', 
			vous pouvez envoyer des fichiers Zippés vers le serveur.<br />
			Voir 
			<a href='http://www.phpconcept.net/pclzip/index.php' 
			   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')" >
				http://www.phpconcept.net/pclzip/index.php
			</a>
			&nbsp;
<?php } ?>
		</span>
	</p>
	
	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='bul_rel_nom_matieres'></a>
			Pour la colonne matière/enseignement dans les bulletins et relevés de notes, utiliser&nbsp;:
		</span>
		<span class="cellTab plusPetit">

<?php
	$bul_rel_nom_matieres=getSettingValue("bul_rel_nom_matieres");
	if($bul_rel_nom_matieres=="") {$bul_rel_nom_matieres="nom_complet_matiere";}
 ?>
			<input type='radio' 
				   name='bul_rel_nom_matieres' 
				   id='bul_rel_nom_matieres_nom_complet_matiere' 
				   value='nom_complet_matiere'
<?php if($bul_rel_nom_matieres=='nom_complet_matiere') {echo " checked='checked'";} ?>
				   onchange='changement()' />
			<label for='bul_rel_nom_matieres_nom_complet_matiere' style='cursor: pointer'>
				le nom complet de matière
			</label>
			<br />
			<input type='radio' 
				   name='bul_rel_nom_matieres' 
				   id='bul_rel_nom_matieres_nom_groupe' 
				   value='nom_groupe'
<?php if($bul_rel_nom_matieres=='nom_groupe') {echo " checked='checked'";} ?>
				   onchange='changement()' />
			<label for='bul_rel_nom_matieres_nom_groupe' style='cursor: pointer'>
				le nom (court) du groupe
			</label>
			<br />
			<input type='radio' 
				   name='bul_rel_nom_matieres' 
				   id='bul_rel_nom_matieres_description_groupe' 
				   value='description_groupe'
<?php if($bul_rel_nom_matieres=='description_groupe') {echo " checked='checked'";} ?>
				   onchange='changement()' />
			<label for='bul_rel_nom_matieres_description_groupe' style='cursor: pointer'>
				la description du groupe
			</label>
		</span>
	</p>
	
	<div style="display: table-row;">
		<div class="cellTab70">
			<p style="font-variant: small-caps;">
				<a name='mode_ouverture_acces_appreciations'></a>
				<a name='delais_apres_cloture'></a>
				Mode d'accès aux bulletins et résultats graphiques, pour les élèves et leurs responsables&nbsp;:<br />
				<span style='font-variant: normal; font-style: italic; font-size: small;'>Sous réserve :<br />
				</span>
			</p>
			<ul style='font-variant: normal; font-style: italic; font-size: small;'>
				<li style='font-variant: normal; font-style: italic; font-size: small;'>
					de créer des comptes pour les responsables et élèves,
				</li>
				<li style='font-variant: normal; font-style: italic; font-size: small;'>
					d'autoriser l'accès aux bulletins simplifiés ou aux graphes dans 
					<a href='droits_acces.php#bull_simp_ele'
					   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
						Droits d'accès
					</a>
				</li>
			</ul>
		</div>
		<p class="cellTab plusPetit">
<?php
$acces_app_ele_resp=getSettingValue("acces_app_ele_resp");
if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
 ?>
			<input type='radio' 
				   name='acces_app_ele_resp' 
				   id='acces_app_ele_resp_manuel' 
				   value='manuel' 
				   onchange='changement()'
				   <?php if($acces_app_ele_resp=='manuel') {echo "checked='checked'";} ?>
				   />
			<label for='acces_app_ele_resp_manuel'>
				manuel (<em>ouvert par la scolarité, classe par classe</em>)
			</label>
			<br />
			<input type='radio' 
				   name='acces_app_ele_resp' 
				   id='acces_app_ele_resp_date' 
				   value='date' 
				   <?php if($acces_app_ele_resp=='date') {echo "checked='checked'";} ?>
				   onchange='changement()' />
			<label for='acces_app_ele_resp_date'>à une date choisie (<em>par la scolarité</em>)</label>
			<br />
 <?php 
$delais_apres_cloture=getSettingValue("delais_apres_cloture");
if($delais_apres_cloture=="") {$delais_apres_cloture=0;}
 ?>
			<input type='radio' 
				   name='acces_app_ele_resp' 
				   id='acces_app_ele_resp_periode_close' 
				   value='periode_close' 
				   onchange='changement()' 
				   <?php if($acces_app_ele_resp=='periode_close') {echo "checked='checked'";} ?>
				    />
			<input type='text' 
				   name='delais_apres_cloture' 
				   id='delais_apres_cloture' 
				   value='<?php echo $delais_apres_cloture; ?>'
				   size='1' 
				   onchange="changement();document.getElementById('acces_app_ele_resp_periode_close').checked=true;" onkeydown="clavier_2(this.id,event,1,600);document.getElementById('acces_app_ele_resp_periode_close').checked=true;" />
			<label for='acces_app_ele_resp_periode_close'>
				jours après la clôture de la période
			</label>
		</p>
	</div>
	
	<p class="ligneCaps">
		<span class="cellTab70">
			<a name='avis_conseil_classe_a_la_mano'></a>
			Les avis du conseil sont remplis&nbsp;:
		</span>
		<span class="cellTab plusPetit">
<?php
$avis_conseil_classe_a_la_mano=getSettingValue("avis_conseil_classe_a_la_mano");
if($avis_conseil_classe_a_la_mano=="") {$avis_conseil_classe_a_la_mano="n";}
?>
			<input type='radio' 
				   name='avis_conseil_classe_a_la_mano' 
				   id='avis_conseil_classe_saisis' 
				   value='n'
				   <?php if ($avis_conseil_classe_a_la_mano=='n') {echo " checked='checked'";} ?>
				   onchange='changement()' />
			<label for='avis_conseil_classe_saisis' style='cursor: pointer'> avant l'impression des bulletins</label>
			<br />
			<input type='radio' 
				   name='avis_conseil_classe_a_la_mano' 
				   id='avis_conseil_classe_a_la_mano' 
				   value='y'
				   <?php if($avis_conseil_classe_a_la_mano=='y') {echo " checked='checked'";} ?>
				   onchange='changement()' />
			<label for='avis_conseil_classe_a_la_mano' style='cursor: pointer'> à la main sur les bulletins imprimés</label>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">			
			<a name='ancre_ele_lieu_naissance'></a>
			<label for='ele_lieu_naissance' style='cursor: pointer'>
				Faire apparaitre les lieux de naissance des élèves&nbsp;:
			</label>
			<br />
			<span style='font-variant: normal; font-style: italic; font-size: small;'>
				Conditionné par l'utilisation des 'code_commune_insee' importés depuis Sconet et par l'import des correspondances 
				'code_commune_insee/commune' dans la table 'communes' depuis 
				<a href='../eleves/import_communes.php' 
				   onclick="return confirm_abandon (this, change, '<?php echo $themessage; ?>')">
					Import des communes
				</a>.
				<br />
			</span>
		</span>
		<span class="cellTab plusPetit">
<?php
$ele_lieu_naissance=getSettingValue("ele_lieu_naissance");
if($ele_lieu_naissance=="") {$ele_lieu_naissance="no";}
?>
			<input type='checkbox' 
				   name='ele_lieu_naissance' 
				   id='ele_lieu_naissance' 
				   value='y'
				   <?php if($ele_lieu_naissance=='y') {echo " checked='checked'";} ?>
				   onchange='changement()' />
		</span>
	</p>


	<p class="ligneCaps">
		<span class="cellTab70">			
			<a name='ancre_ele_tel_pers'></a>
			<label for='ele_tel_pers' style='cursor: pointer'>
				Faire apparaitre le numéro de téléphone personnel des élèves&nbsp;:
			</label>
		</span>
		<span class="cellTab plusPetit">
<?php
$ele_tel_pers=getSettingValue("ele_tel_pers");
if($ele_tel_pers=="") {$ele_tel_pers="no";}
?>
			<input type='checkbox' 
				   name='ele_tel_pers' 
				   id='ele_tel_pers' 
				   value='yes'
				   <?php if($ele_tel_pers=='yes') {echo " checked='checked'";} ?>
				   onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">			
			<a name='ancre_ele_tel_port'></a>
			<label for='ele_tel_port' style='cursor: pointer'>
				Faire apparaitre le numéro de téléphone portable des élèves&nbsp;:
			</label>
		</span>
		<span class="cellTab plusPetit">
<?php
$ele_tel_port=getSettingValue("ele_tel_port");
if($ele_tel_port=="") {$ele_tel_port="yes";}
?>
			<input type='checkbox' 
				   name='ele_tel_port' 
				   id='ele_tel_port' 
				   value='yes'
				   <?php if($ele_tel_port=='yes') {echo " checked='checked'";} ?>
				   onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">			
			<a name='ancre_ele_tel_prof'></a>
			<label for='ele_tel_prof' style='cursor: pointer'>
				Faire apparaitre le numéro de téléphone professionnel des élèves&nbsp;:
			</label>
		</span>
		<span class="cellTab plusPetit">
<?php
$ele_tel_prof=getSettingValue("ele_tel_prof");
if($ele_tel_prof=="") {$ele_tel_prof="no";}
?>
			<input type='checkbox' 
				   name='ele_tel_prof' 
				   id='ele_tel_prof' 
				   value='yes'
				   <?php if($ele_tel_prof=='yes') {echo " checked='checked'";} ?>
				   onchange='changement()' />
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">			
			<a name='ancre_exp_imp_chgt_etab'></a>
			<label for='exp_imp_chgt_etab' style='cursor: pointer'>
				Permettre l'export/import des bulletins d'élèves au format CSV&nbsp;:
			</label>
			<br />
			<span style='font-variant: normal; font-style: italic; font-size: small;'>
				Le fichier peut être généré pour un élève qui quitte l'établissement en cours d'année.<br />
				L'établissement qui reçoit l'élève peut utiliser ce fichier pour importer les bulletins.
			</span>
		</span>
		<span class="cellTab plusPetit">
<?php
$exp_imp_chgt_etab=getSettingValue("exp_imp_chgt_etab");
if($exp_imp_chgt_etab=="") {$exp_imp_chgt_etab="no";}
?>
			<input type='checkbox' 
				   name='exp_imp_chgt_etab' 
				   id='exp_imp_chgt_etab' 
				   value='yes'
				   <?php if($exp_imp_chgt_etab=='yes') {echo " checked='checked'";} ?>
				   onchange='changement()' />
		</span>
	</p>

	<div style="display: table-row;">
		<div class="cellTab70">
			<p style="font-variant: small-caps;">
				<a name='output_mode_pdf'></a>
				Mode d'ouverture des fichiers PDF produits par Gepi&nbsp;:
			</p>
		</div>
		<p class="cellTab plusPetit">
<?php
$output_mode_pdf=getSettingValue("output_mode_pdf");
if(!in_array($output_mode_pdf, array("D", "I"))) {$output_mode_pdf='D';}
 ?>
			<input type='radio' 
				   name='output_mode_pdf' 
				   id='output_mode_pdf_D' 
				   value='D' 
				   onchange='changement()'
				   <?php if($output_mode_pdf=='D') {echo "checked='checked'";} ?>
				   />
			<label for='output_mode_pdf_D'>
				Proposer le téléchargement du PDF pour lecture hors du navigateur
			</label>
			<br />
			<input type='radio' 
				   name='output_mode_pdf' 
				   id='output_mode_pdf_I' 
				   value='I' 
				   onchange='changement()'
				   <?php if($output_mode_pdf=='I') {echo "checked='checked'";} ?>
				   />
			<label for='output_mode_pdf_I'>
				Ouvrir le PDF dans le navigateur<br />
				(<em>ce choix peut nécessiter qu'un plugin soit installé sur le navigateur</em>)
			</label>
		</p>
	</div>

	<p class="ligneCaps">
		<span class="cellTab70">
			<label for='aff_temoin_check_serveur' style='cursor: pointer'>Effectuer des "contacts" réguliers du serveur et afficher un témoin pour s'assurer que le serveur est bien à l'écoute.</label>
			<br />
			<span class='small'>
				(<em>cela peut être utile dans le cas où vous avez une qualité de connexion aléatoire</em>)&nbsp;:</label>
			</span>
		</span>
		<span class="cellTab plusPetit">
			<?php
				$aff_temoin_check_serveur=getSettingValue("aff_temoin_check_serveur");
				if($aff_temoin_check_serveur=="") {$aff_temoin_check_serveur="n";}
				echo "<input type='checkbox' name='aff_temoin_check_serveur' id='aff_temoin_check_serveur' value='y'";
				if($aff_temoin_check_serveur=='y') {echo " checked";}
				echo " onchange='changement()' />\n";
			?>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			<label for='url_racine_gepi' style='cursor: pointer'>Adresse de la racine Gepi</label>
			<br />
			<span class='small'>
				(<em>utilisé dans des envois de mails pour donner l'adresse d'une page en particulier<br />
				Exemple&nbsp;: https://NOM_SERVEUR/DOSSIER_GEPI</em>)&nbsp;:</label>
			</span>
		</span>
		<span class="cellTab plusPetit">
			<?php
				echo "<input type='text' name='url_racine_gepi' id='url_racine_gepi' value=\"".getSettingValue('url_racine_gepi')."\" onchange='changement()' size='30' />\n";
			?>
		</span>
	</p>

	<p class="ligneCaps">
		<span class="cellTab70">
			N° d'enregistrement à la CNIL : <br />
			<span class='small'>
				Conformément à l'article 16 de la loi 78-17 du 6 janvier 1978, dite loi informatique et liberté, cette 
				installation de GEPI doit faire l'objet d'une déclaration de traitement automatisé d'informations nominatives 
				de la CNIL. Si ce n'est pas encore le cas, laissez libre le champ ci-contre<br />
				<a href='http://www.sylogix.org/projects/gepi/wiki/Declaration_cnil' target='_blank'>Voir wiki</a>
			</span>
		</span>
		<span class="cellTab plusPetit">
			<input type="text" 
				   name="num_enregistrement_cnil" 
				   size="20" 
				   value="<?php echo(getSettingValue("num_enregistrement_cnil")); ?>" 
				   onchange='changement()' />
		</span>
	</p>


	<p class="center">
		<input type="hidden" name="is_posted" value="1" />
		<input type="button" 
			   id="button_form_1"
			   name = "OK" 
			   value="Enregistrer" 
			   style="font-variant: small-caps; display:none;" 
			   onclick="test_puis_submit()" />
	</p>
</fieldset>
</form>

<script type='text/javascript'>
	//<![CDATA[
	document.getElementById('button_form_1').style.display='';
<?php
	echo insere_js_check_format_login('test_format_login', 'n');
?>
	function min_to_jourheureminsec(id_duree_en_min,id_dest) {
		h_min='';

		if((document.getElementById(id_duree_en_min))&&(document.getElementById(id_dest))) {
			m=document.getElementById(id_duree_en_min).value;
			if(isNaN(m)==true) {
				// Ce n'est pas un nombre, on ne convertit pas
				h_min=' Erreur !';

				document.getElementById(id_dest).style.color='red';
				document.getElementById(id_dest).style.textDecoration='blink';
			}
			else {
				s=m*60;
				h_min=sec_to_jourheureminsec_construction_chaine(s);

				if(s<=<?php echo $session_gc_maxlifetime;?>) {
					document.getElementById(id_dest).style.color='green';
					document.getElementById(id_dest).style.textDecoration='none';
				}
				else {
					document.getElementById(id_dest).style.color='red';
					document.getElementById(id_dest).style.textDecoration='blink';
				}
			}
			document.getElementById(id_dest).innerHTML=h_min;
		}
	}

	function sec_to_jourheureminsec(id_duree_en_sec,id_dest) {
		h_min='';

		if((document.getElementById(id_duree_en_sec))&&(document.getElementById(id_dest))) {
			s=document.getElementById(id_duree_en_sec).value;
			if(isNaN(s)==true) {
				// Ce n'est pas un nombre, on ne convertit pas
				h_min=' Erreur !';
			}
			else {
				h_min=sec_to_jourheureminsec_construction_chaine(s);
			}
			document.getElementById(id_dest).innerHTML=h_min;
		}
	}
	
	function sec_to_jourheureminsec_construction_chaine(s) {
		j=0;
		if(s>=3600*24) {
			j=Math.floor(s/(3600*24));
			s=s-j*3600*24;
		}

		h=0;
		if(s>=3600) {
			h=Math.floor(s/3600);
			s=s-h*3600;
		}

		m=0;
		if(s>=60) {
			m=Math.floor(s/60);
			s=s-m*60;
		}

		h_min=' soit ';
		if(j>0) {
			h_min=h_min+j+'&nbsp;j';

			if((h>0)||(m>0)||(s>0)) {
				if(m<10) {
					m='0'+m;
				}
				if(s<10) {
					s='0'+s;
				}
				h_min=h_min+' '+h+'&nbsp;h '+m+'&nbsp;m '+s+'&nbsp;s';
			}
		}
		else {
			if(h>0) {
				h_min=h_min+h+'&nbsp;h';

				if((m>0)||(sec>0)) {
					if(m<10) {
						m='0'+m;
					}
					if(s<10) {
						s='0'+s;
					}
					h_min=h_min+' '+m+'&nbsp;m '+s+'&nbsp;s';
				}
			}
			else {
				if(m<10) {
					m='0'+m;
				}
				if(s<10) {
					s='0'+s;
				}
				h_min=h_min+m+'&nbsp;m '+s+'&nbsp;s';
			}
		}

		return h_min;
	}

	min_to_jourheureminsec('sessionMaxLength','sessionMaxLength_en_minutes');
	
	function test_puis_submit() {
		if(!test_format_login(document.getElementById('mode_generation_login').value)) {
			alert('Le format de login des personnels est invalide');
		}
		else {
			if(!test_format_login(document.getElementById('mode_generation_login_eleve').value)) {
				alert('Le format de login des élèves est invalide');
			}
			else {
				if(!test_format_login(document.getElementById('mode_generation_login_responsable').value)) {
					alert('Le format de login des responsables est invalide');
				}
				else {
					//document.form1.submit();
					document.getElementById('form1').submit();
				}
			}
		}
	}
	//]]>
</script>

<hr />
<form enctype="multipart/form-data" action="param_gen.php" method="post" id="form2" style="width: 100%;">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<p class="cellTab70">
<?php
echo add_token_field();
?>
	<span style="font-variant: small-caps;"><strong>Logo de l'établissement : </strong></span>
	<br />
	Le logo est visible sur les bulletins officiels, ainsi que sur la page d'accueil publique des cahiers de texte
	<br />
	Modifier le Logo (<em>png, jpg et gif uniquement</em>) :
	<br />
	<input type="file" name="doc_file" onchange='changement()' />
	<input type="submit" name="valid_logo" value="Enregistrer" /></p>
	<p class="cellTab">
<?php
$nom_fic_logo = getSettingValue("logo_etab");

$nom_fic_logo_c = "../images/".$nom_fic_logo;
if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
?>
		<br />
	<strong>Logo actuel : </strong>
		<br />
		<img src="<?php echo $nom_fic_logo_c; ?>" border='0' alt="logo" />
		<br /><input type="submit" name="sup_logo" value="Supprimer le logo" />
<?php } else { ?>
		<br />
		<strong><em>Pas de logo actuellement</em></strong>
<?php } ?>
	</p>

	<p>
		<em>Remarques&nbsp;:</em>
		<br />- le fichier sera renommé logo_etab.xxx (<em>où l'extension xxx est fonction du type</em>)
		<br />- les transparences sur les images PNG, GIF ne permettent pas une impression PDF 
		(<em>canal alpha non supporté par fpdf</em>)
		<br />- il a aussi été signalé que les JPEG progressifs/entrelacés peuvent perturber la génération de PDF
	</p>
</fieldset>
</form>

<hr />

	<p  class="cellTab" style="font-variant: small-caps;">
		Fichier de signature/cachet : <a href='gestion_signature.php'>Choisir le fichier et en gérer l'accès</a>
	</p>

<hr />

<form enctype="multipart/form-data" action="param_gen.php" method="post" id="form3" style="width: 100%;">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<p>
<?php
echo add_token_field();
?>
	</p>

	<p  class="cellTab" style="font-variant: small-caps;">
		Tester la présence du module phpMyVisite (<em>pmv.php</em>) :
	</p>
	<p class="cellTab">
		<input type="radio" 
			   name="gepi_pmv" 
			   id="gepi_pmv_y" 
			   value="y" 
			   <?php if(getSettingValue("gepi_pmv")!="n"){echo "checked='checked'";} ?>
			   onchange='changement()' />
		<label for='gepi_pmv_y' style='cursor: pointer;'>
			Oui
		</label>
		<br />
		<input type="radio" 
			   name="gepi_pmv" 
			   id="gepi_pmv_n" 
			   value="n" 
			   <?php if(getSettingValue("gepi_pmv")=="n"){echo "checked='checked'";} ?> 
			   onchange='changement()' />
		<label for='gepi_pmv_n' style='cursor: pointer;'>
			Non
		</label>
	</p>

	<p>
	<input type="hidden" name="is_posted" value="1" />
	</p>
	<p class="center">
		<input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" />
	</p>

	<p>
		<em>Remarque:</em>
	</p>
	<p>
		Il arrive que ce test de présence provoque un affichage d'erreur (<em>à propos de pmv.php</em>).
		Dans ce cas, désactivez simplement le test.
	</p>
</fieldset>
</form>
<hr />

<form enctype="multipart/form-data" action="param_gen.php" method="post" id="form4" style="width: 100%;">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<p>
<?php
echo add_token_field();
?>
	</p>

	<p  class="cellTab" style="font-variant: small-caps;">
		Tester la date de la révision Gepi avec git<br />(<em>nécessite que git soit installé et accessible</em>) :
	</p>
	<p class="cellTab">
		<input type="radio" 
			   name="ne_pas_tester_version_via_git_log" 
			   id="ne_pas_tester_version_via_git_log_n" 
			   value="n" 
			   <?php
				if(!getSettingAOui('ne_pas_tester_version_via_git_log')) {echo "checked='checked'";}
			   ?>
			   onchange='changement()' />
		<label for='ne_pas_tester_version_via_git_log_n' style='cursor: pointer;'>
			Oui
		</label>
		<br />
		<input type="radio" 
			   name="ne_pas_tester_version_via_git_log" 
			   id="ne_pas_tester_version_via_git_log_y" 
			   value="y" 
			   <?php
				if(getSettingAOui('ne_pas_tester_version_via_git_log')) {echo "checked='checked'";}
			   ?>
			   onchange='changement()' />
		<label for='ne_pas_tester_version_via_git_log_y' style='cursor: pointer;'>
			Non
		</label>
	</p>

	<p>
	<input type="hidden" name="is_posted" value="1" />
	</p>
	<p class="center">
		<input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" />
	</p>

	<p>
		<em>Remarque:</em>
	</p>
	<p>
		git est l'outil de gestion de versions de Gepi.<br />
		Si git est installé sur votre serveur et accessible par le serveur web, alors vous pouvez afficher dans l'entête administrateur sur la page d'accueil, la date de la révision en place.<br />
		Cela peut être commode pour apporter des précisions sur votre version quand vous posez une question sur la liste gepi-users, mais l'absence éventuelle de git n'enlèvera aucune fonctionnalité à votre Gepi.
	</p>
</fieldset>
</form>


<hr />

<a name='gepi_en_production'></a>
<form enctype="multipart/form-data" action="param_gen.php" method="post" id="form4" style="width: 100%;">
<fieldset style='border: 1px solid grey; background-image: url("../images/background/opacite50.png");'>
	<p>
<?php
echo add_token_field();
?>
	</p>

	<p  class="cellTab" style="font-variant: small-caps;">
		Gepi en production<br />(<em>par opposition à un Gepi de test</em>)&nbsp;:
	</p>
	<p class="cellTab">
		<input type="radio" 
			   name="gepi_en_production" 
			   id="gepi_en_production_y" 
			   value="y" 
			   <?php
				if(getSettingValue('gepi_en_production')=="") {saveSetting('gepi_en_production', 'y');}
				if(getSettingAOui('gepi_en_production')) {echo "checked='checked'";}
			   ?>
			   onchange='changement()' />
		<label for='gepi_en_production_y' style='cursor: pointer;'>
			Oui
		</label>
		<br />
		<input type="radio" 
			   name="gepi_en_production" 
			   id="gepi_en_production_n" 
			   value="n" 
			   <?php
				if(!getSettingAOui('gepi_en_production')) {echo "checked='checked'";}
			   ?>
			   onchange='changement()' />
		<label for='gepi_en_production_n' style='cursor: pointer;'>
			Non
		</label>
	</p>

	<p>
	<input type="hidden" name="is_posted" value="1" />
	</p>
	<p class="center">
		<input type="submit" name = "OK" value="Enregistrer" style="font-variant: small-caps;" />
	</p>

	<p>
		<em>Remarque:</em>
	</p>
	<p>
		Sur un serveur Gepi en production, avec des données que l'on ne veut pas perdre accidentellement, on désactive l'accès à quelques liens sensibles de Gepi comme <strong>Effacer la base</strong> et <strong>Données de test</strong>.<br />
		Sur un Gepi de test en revanche, on peut souhaiter effectuer ces actions sensibles.
	</p>
</fieldset>
</form>

<?php
require("../lib/footer.inc.php");
?>
