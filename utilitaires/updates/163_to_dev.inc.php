<?php
/**
 * Fichier de mise à jour de la version 1.6.3 à la version 1.6.4 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.4(dev) :</h3>";

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

$result .= "&nbsp;-> Ajout d'un champ 'date_entree' à la table 'eleves'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'date_entree';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE eleves ADD date_entree DATETIME COMMENT 'Timestamp de sortie de l\'élève de l\'établissement (fin d\'inscription)';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Initialisation du terme 'incident' dans le module Discipline : ";
$mod_disc_terme_incident=getSettingValue('mod_disc_terme_incident');
if ($mod_disc_terme_incident=="") {
	if (!saveSetting("mod_disc_terme_incident", 'incident')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}

$result .= "&nbsp;-> Initialisation du terme 'sanction' dans le module Discipline : ";
$mod_disc_terme_sanction=getSettingValue('mod_disc_terme_sanction');
if ($mod_disc_terme_sanction=="") {
	if (!saveSetting("mod_disc_terme_sanction", 'sanction')) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("Ok !");
	}
} else {
	$result .= msj_present("déjà faite");
}

$result .= "&nbsp;-> Ajout d'un champ 'saisie_prof' à la table 's_types_sanctions2' : ";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_types_sanctions2 LIKE 'saisie_prof';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE s_types_sanctions2 ADD saisie_prof char(1) NOT NULL default 'n';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'saisie_par' à la table 's_sanctions' : ";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_sanctions LIKE 'saisie_par';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE s_sanctions ADD saisie_par varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

?>
