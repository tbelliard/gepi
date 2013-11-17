<?php

/**
 *
 * Module d'intégration de Gepi dans un ENT réalisé au moment de l'intégration de Gepi dans ARGOS dans l'académie de Bordeaux
 * Fichier permettant de récupérer de nouveaux élèves dans le ldap de l'ENT
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane boireau, Julien Jocal
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

// Sécurité supplémentaire pour éviter d'aller voir ce fichier si on n'est pas dans un ent
if (getSettingValue("use_ent") != 'y') {
	die('Fichier interdit.');
}

if((preg_match("/^027/", getSettingValue('gepiSchoolRne')))||(preg_match("/^076/", getSettingValue('gepiSchoolRne')))) {
	header("Location:./index_itop.php");
	die();
}

// ======================= Initialisation des variables ==========================
//$ = isset($_POST[""]) ? $_POST[""] : NULL;
$aff_continuer = NULL;
$msg2 = NULL;
$etape = isset($_GET["etape"]) ? $_GET["etape"] : NULL;


// ======================= Traitement des données ================================
// On récupère le RNE de l'établissement en question
$RNE = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'] : getSettingValue("gepiSchoolRne");
if ($RNE === '') {
	$msg = "Attention, votre RNE n'est pas renseigné dans la page des <a href=\"gestion/param_gen.php\">paramètres généraux.</a>";
} else {

	$msg = "<p>Votre RNE est ".$RNE.". S'il est exact, vous pouvez passer à l'étape suivante.
				&nbsp;<a href=\"index.php?etape=2".add_token_in_url()."\">Enregistrer les utilisateurs</a>";

}

// On teste pour la table
if ($etape == 2) {
	check_token();

	$msg = NULL;
	// On crée la table si nécessaire

	$result = "&nbsp;->Ajout de la table ldap_bx. <br />";
	$test1 = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES LIKE 'ldap_bx'"));
	if ($test1 == 0) {
			$sql = "CREATE TABLE `ldap_bx` (
					`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
					`login_u` VARCHAR( 200 ) NOT NULL ,
					`nom_u` VARCHAR( 200 ) NOT NULL ,
					`prenom_u` VARCHAR( 200 ) NOT NULL ,
					`statut_u` VARCHAR( 50 ) NOT NULL ,
					`identite_u` VARCHAR( 50 ) NOT NULL ,
					PRIMARY KEY ( `id` )
					) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if ($query) {
			$msg = "<font style=\"color: green;\">Ok !</font><br />";
		} else {
			$msg = "<font style=\"color: red;\">Erreur</font><br />";
		}
	}else{
		$msg = "<font style=\"color: blue;\">La table existe déjà.</font><br />";
	}

	// On truncate la table
	$tr = mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE TABLE ldap_bx");

	// On ouvre une connexion avec le ldap
	$ldap = new LDAPServer;
	$info = $ldap->get_all_users('rne', $RNE);

	// $infos est donc un tableau de tous les utilisateurs du LDAP qui ont ce $RNE en attribut (sic)
	for($a=0; $a < $info["count"]; $a++){

		if (file_exists("../secure/config_ldap.inc.php")) {
			require("../secure/config_ldap.inc.php");
		}
			$ldap_login		= (isset($ldap_champ_login) AND $ldap_champ_login != '') ? $ldap_champ_login : 'uid';
			$ldap_nom		= (isset($ldap_champ_nom) AND $ldap_champ_nom != '') ? $ldap_champ_nom : 'sn';
			$ldap_prenom	= (isset($ldap_champ_prenom) AND $ldap_champ_prenom != '') ? $ldap_champ_prenom : 'givenname';
			$ldap_statut	= (isset($ldap_champ_statut) AND $ldap_champ_statut != '') ? $ldap_champ_statut : 'edupersonaffiliation';
			$ldap_numero	= (isset($ldap_champ_numero) AND $ldap_champ_numero != '') ? $ldap_champ_numero : 'employeenumber';

		if (isset($info[$a][$ldap_numero][0])) {
			$ident = $info[$a][$ldap_numero][0];
		}else{
			$ident = 'non';
		}

		$sql = "INSERT INTO ldap_bx (id, login_u, nom_u, prenom_u, statut_u, identite_u)
					VALUES ('',
							'".$info[$a][$ldap_login][0]."',
							'".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $info[$a][$ldap_nom][0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
							'".((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $info[$a][$ldap_prenom][0]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))."',
							'".$info[$a][$ldap_statut][0]."',
							'".$ident."')";
		$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if ($query) {
			$msg2 .= '<br />L\'utilisateur '.$info[$a][$ldap_login][0].' a été enregistré.';
		}else{
			$msg2 .= '<br /><span style="color: red;">L\'utilisateur '.$info[$a][$ldap_login][0].' n\'a pas été enregistré.</span>';
		}
	}
	$aff_continuer = '<p>Vous pouvez retourner sur la page d\'initialisation par sconet/STSweb <a href="../init_xml2/index.php">CONTINUER</a></p>
	<p><a href="miseajour_ent_eleves.php">Ajouter de nouveaux utilisateurs arrivés en cours d\'année</a></p>';

}

// =========== fichiers spéciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Les utilisateurs de l'ENT";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var(); // à enlever en production
?>

<!-- Mise à jour à partir de l'ENT -->
<p class="bold"><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>R&eacute;cup&eacute;ration des informations de l'ENT</h2>

<?php echo $msg2 . $aff_continuer; ?>



<?php require_once("../lib/footer.inc.php");
