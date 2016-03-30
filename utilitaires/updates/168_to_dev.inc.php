<?php
/**
 * Fichier de mise à jour de la version 1.6.8 à la version 1.6.9 par défaut
 *
 *
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= msj_ok("Champ XXX ajouté avec succès");
 *
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.9 :</h3>";

/*
// Section d'exemple

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'droits_acces_fichiers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'droits_acces_fichiers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
	id INT(11) unsigned NOT NULL auto_increment,
	fichier VARCHAR( 255 ) NOT NULL ,
	identite VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

// Merci de ne pas enlever le témoin ci-dessous de "fin de section exemple"
// Fin SECTION EXEMPLE
*/

$result .= "<strong>Ajout d'une table 'engagements_pages' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'engagements_pages'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS engagements_pages (
		id int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
		page varchar(255) NOT NULL default '' COMMENT 'Page ou module',
		id_type int(11) NOT NULL COMMENT 'identifiant du type d engagement',
		PRIMARY KEY (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'calendrier_vacances' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'calendrier_vacances'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS calendrier_vacances (
		id int(11) NOT NULL auto_increment,
		nom_calendrier varchar(100) NOT NULL default '',
		debut_calendrier_ts varchar(11) NOT NULL,
		fin_calendrier_ts varchar(11) NOT NULL,
		jourdebut_calendrier date NOT NULL default '0000-00-00',
		heuredebut_calendrier time NOT NULL default '00:00:00',
		jourfin_calendrier date NOT NULL default '0000-00-00',
		heurefin_calendrier time NOT NULL default '00:00:00',
		PRIMARY KEY (id)) 
		ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'entrées de configuration envoi SMS dans la table setting</strong><br />";

// Il faut tenir compte de données déjà saisies dans le module absence 2
$result .= "&nbsp;-> Ajout de l'entrée autorise_envoi_sms dans la table setting<br />";
if (getSettingValue('autorise_envoi_sms')===null) {
	if (getSettingValue('abs2_sms')!==null) $OK=saveSetting('autorise_envoi_sms',getSettingValue('abs2_sms')); else $OK=saveSetting('autorise_envoi_sms','n');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée autorise_envoi_sms existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_prestataire dans la table setting<br />";
if (getSettingValue('sms_prestataire')===null) {
	if (getSettingValue('abs2_sms_prestataire')!==null) $OK=saveSetting('sms_prestataire',getSettingValue('abs2_sms_prestataire'));
	else $OK=saveSetting('sms_prestataire','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_prestataire existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_username dans la table setting<br />";
if (getSettingValue('sms_username')===null) {
	if (getSettingValue('abs2_sms_username')!==null) $OK=saveSetting('sms_username',getSettingValue('abs2_sms_username'));
	else $OK=saveSetting('sms_username','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_username existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_password dans la table setting<br />";
if (getSettingValue('sms_password')===null) {
	if (getSettingValue('abs2_sms_password')!==null) $OK=saveSetting('sms_password',getSettingValue('abs2_sms_password'));
	else $OK=saveSetting('sms_password','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_password existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_identite dans la table setting<br />";
if (getSettingValue('sms_identite')===null) {
	if (saveSetting('sms_identite',getSettingValue('gepiSchoolName'))) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_identite existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nj dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nj')===null) {
if (saveSetting('abs2_afficher_alerte_nj',"y")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nj existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nb_nj dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nb_nj')===null) {
	if (saveSetting('abs2_afficher_alerte_nb_nj',"4")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nb_nj existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nj_delai dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nj_delai')===null) {
	if (saveSetting('abs2_afficher_alerte_nj_delai',"30")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nj_delai existe déjà dans la table setting");

?>
