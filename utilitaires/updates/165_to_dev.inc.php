<?php
/**
 * Fichier de mise à jour de la version 1.6.5 à la version 1.6.6 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.6(dev) :</h3>";

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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'gc_noms_affichages' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'gc_noms_affichages'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS gc_noms_affichages (
	id int(11) unsigned NOT NULL auto_increment,
	id_aff int(11) NOT NULL,
	nom varchar(100) NOT NULL,
	description tinytext NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'edt_ics' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_ics'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS edt_ics (
	id int(11) NOT NULL AUTO_INCREMENT,
	id_classe INT(11) NOT NULL,
	classe_ics varchar(100) NOT NULL DEFAULT '',
	prof_ics varchar(200) NOT NULL DEFAULT '',
	matiere_ics varchar(100) NOT NULL DEFAULT '',
	salle_ics varchar(100) NOT NULL DEFAULT '',
	jour_semaine varchar(10) NOT NULL DEFAULT '',
	num_semaine varchar(10) NOT NULL DEFAULT '',
	annee char(4) NOT NULL DEFAULT '',
	date_debut DATETIME NOT NULL default '0000-00-00 00:00:00',
	date_fin DATETIME NOT NULL default '0000-00-00 00:00:00',
	description TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (id)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'edt_ics_prof' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_ics_prof'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS edt_ics_prof (
	id int(11) NOT NULL AUTO_INCREMENT,
	login_prof varchar(100) NOT NULL DEFAULT '',
	prof_ics varchar(200) NOT NULL DEFAULT '',
	PRIMARY KEY (id)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'edt_ics_matiere' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_ics_matiere'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS edt_ics_matiere (
	id int(11) NOT NULL AUTO_INCREMENT,
	matiere varchar(100) NOT NULL DEFAULT '',
	matiere_ics varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (id)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'abs_prof' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'abs_prof'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS abs_prof (
id int(11) NOT NULL AUTO_INCREMENT,
login_user varchar(50) NOT NULL,
date_debut datetime NOT NULL,
date_fin datetime NOT NULL,
titre varchar(100) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'abs_prof_remplacement' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'abs_prof_remplacement'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS abs_prof_remplacement (
id int(11) NOT NULL AUTO_INCREMENT,
id_absence INT(11) NOT NULL,
id_groupe INT(11) NOT NULL,
id_classe INT(11) NOT NULL,
jour CHAR(8) NOT NULL,
id_creneau INT(11) NOT NULL,
date_debut_r datetime NOT NULL,
date_fin_r datetime NOT NULL,
reponse varchar(30) NOT NULL,
date_reponse datetime NOT NULL,
login_user varchar(50) NOT NULL,
commentaire_prof text NOT NULL,
validation_remplacement varchar(30) NOT NULL,
commentaire_validation text NOT NULL,
salle VARCHAR(100) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
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
$result .= "<strong>Ajout d'une table 'abs_prof_divers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'abs_prof_divers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS abs_prof_divers (
	id INT(11) unsigned NOT NULL auto_increment,
	name VARCHAR( 255 ) NOT NULL ,
	value VARCHAR( 255 ) NOT NULL ,
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

$result .= "&nbsp;-> Ajout d'un champ 'texte_famille' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'texte_famille';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE abs_prof_remplacement ADD texte_famille TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'info_famille' à la table 'abs_prof_remplacement'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'info_famille';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE abs_prof_remplacement ADD info_famille VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Initialisation de 'abs_prof_modele_message_eleve' : ";
$sql="SELECT value FROM setting WHERE name='abs_prof_modele_message_eleve';";
$query = mysqli_query($mysqli, $sql);
if(mysqli_num_rows($query)==0) {
	$sql="INSERT INTO setting SET name='abs_prof_modele_message_eleve', value='En raison de l''absence de __PROF_ABSENT__, le cours __COURS__ du __DATE_HEURE__ sera remplacé par un cours avec __PROF_REMPLACANT__ en salle __SALLE__.';";
	$query = mysqli_query($mysqli, $sql);
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Initialisation déjà faite");
}

?>
