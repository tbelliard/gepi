<?php
/**
 * Fichier de mise à jour de la version 1.6.2 à la version 1.6.3 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.3(dev) :</h3>";

/*
// Section d'exemple

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';");
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
*/

$result .= "<br />";
$result .= "<strong>Modularité de l'affichage des absences sur les bulletins HTML&nbsp;:</strong><br />";
if((getSettingValue("bull_affiche_abs_tot")=="")&&(getSettingValue("bull_affiche_abs_nj")=="")&&(getSettingValue("bull_affiche_abs_ret")=="")) {
	if(getSettingValue("bull_affiche_absences")=="y") {
		$result .= "Enregistrement du non affichage des absences, absences non justifiées et retards&nbsp;: ";
		if((saveSetting("bull_affiche_abs_tot", "y"))&&(saveSetting("bull_affiche_abs_nj", "y"))&&(saveSetting("bull_affiche_abs_ret", "y"))) {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_ok("SUCCES !");
		}
	}
	else {
		$result .= "Enregistrement du non affichage des absences, absences non justifiées et retards&nbsp;: ";
		if((saveSetting("bull_affiche_abs_tot", "n"))&&(saveSetting("bull_affiche_abs_nj", "n"))&&(saveSetting("bull_affiche_abs_ret", "n"))) {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_ok("SUCCES !");
		}
	}
}
else {
	$result .= msj_present("La migration a déjà été réalisée");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'messagerie' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'messagerie'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS messagerie (
		id int(11) NOT NULL AUTO_INCREMENT,
		in_reply_to int(11) NOT NULL,
		login_src varchar(50) NOT NULL,
		login_dest varchar(50) NOT NULL,
		sujet varchar(100) NOT NULL,
		message text NOT NULL,
		date_msg timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		vu tinyint(4) NOT NULL,
		date_vu timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'date_visibilite' à la table 'messagerie'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM messagerie LIKE 'date_visibilite';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE messagerie ADD date_visibilite timestamp NOT NULL AFTER date_msg;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

if(getSettingValue('active_messagerie')=="") {
	$result .= "&nbsp;-> Initialisation du module Messagerie&nbsp;: ";
	if (!saveSetting("active_messagerie", 'y')) {
		$result .= msj_erreur();
	} else {
		$result .= msj_ok("Ok !");
	}
}

saveSetting("PeutPosterMessageAdministrateur", 'y');

if(getSettingValue('MessagerieDelaisTest')=="") {
	$result .= "&nbsp;-> Initialisation du délais entre deux tests de présence de messages non lus&nbsp;: ";
	if (!saveSetting("MessagerieDelaisTest", '1')) {
		$result .= msj_erreur();
	} else {
		$result .= msj_ok("Ok !");
	}
}

if(getSettingValue('PeutPosterMessageScolarite')=="") {
	$result .= "&nbsp;-> Initialisation de la possibilité de poster des messages pour les comptes 'scolarité'&nbsp;: ";
	if (!saveSetting("PeutPosterMessageScolarite", 'y')) {
		$result .= msj_erreur();
	} else {
		$result .= msj_ok("Ok !");
	}
}

if(getSettingValue('PeutPosterMessageCpe')=="") {
	$result .= "&nbsp;-> Initialisation de la possibilité de poster des messages pour les comptes 'cpe'&nbsp;: ";
	if (!saveSetting("PeutPosterMessageCpe", 'y')) {
		$result .= msj_erreur();
	} else {
		$result .= msj_ok("Ok !");
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'acces_cn' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'acces_cn'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS acces_cn (
			id INT( 11 ) NOT NULL AUTO_INCREMENT ,
			id_groupe INT( 11 ) NOT NULL ,
			periode INT( 11 ) NOT NULL ,
			date_limite timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			commentaires text NOT NULL,
			PRIMARY KEY ( id )
			) CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Acces exceptionnel au CN en periode close';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'acces_exceptionnel_matieres_notes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'acces_exceptionnel_matieres_notes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS acces_exceptionnel_matieres_notes (
			id INT( 11 ) NOT NULL AUTO_INCREMENT ,
			id_groupe INT( 11 ) NOT NULL ,
			periode INT( 11 ) NOT NULL ,
			date_limite timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			commentaires text NOT NULL,
			PRIMARY KEY ( id )
			) CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Acces exceptionnel à la modif de notes du bulletin en periode close';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

?>
