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

$result .= "<strong>Ajout d'une table 'engagements' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'engagements'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS engagements (
	id int(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(10) NOT NULL,
	nom VARCHAR(100) NOT NULL,
	description TEXT NOT NULL,
	type VARCHAR(20) NOT NULL,
	conseil_de_classe VARCHAR(10) NOT NULL,
	ConcerneEleve VARCHAR(10) NOT NULL,
	ConcerneResponsable VARCHAR(10) NOT NULL,
	SaisieScol VARCHAR(10) NOT NULL,
	SaisieCpe VARCHAR(10) NOT NULL,
	PRIMARY KEY (id)
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$result .= "Activation du module Engagements.<br />";
		saveSetting("active_mod_engagements", "y");

		$result .= "Enregistrement de l'engagement 'Délégué de classe' :<br />";
		$sql="INSERT INTO engagements SET code='C', nom='Délégué de classe', description='Délégué de classe', type='id_classe', conseil_de_classe='yes', ConcerneEleve='yes', SaisieScol='yes';";
		$query = mysqli_query($mysqli, $sql);
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}

		$result .= "Enregistrement de l'engagement 'Délégué du conseil de la vie lycéenne' :<br />";
		$sql="INSERT INTO engagements SET code='V', nom='Délégué du conseil de la vie lycéenne', description='Délégué du conseil de la vie lycéenne', type='', conseil_de_classe='', ConcerneEleve='yes', SaisieScol='yes';";
		$query = mysqli_query($mysqli, $sql);
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}

		$result .= "Enregistrement de l'engagement 'Membre du conseil d'administration' :<br />";
		$sql="INSERT INTO engagements SET code='A', nom='Membre du conseil d''administration', description='Membre du conseil d''administration', type='', conseil_de_classe='', ConcerneEleve='yes', SaisieScol='yes';";
		$query = mysqli_query($mysqli, $sql);
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}

		$result .= "Enregistrement de l'engagement 'Membre du comité d'éducation à la santé et à la citoyenneté' :<br />";
		$sql="INSERT INTO engagements SET code='E', nom='Membre du comité d''éducation à la santé et à la citoyenneté', description='Membre du comité d''éducation à la santé et à la citoyenneté', type='', conseil_de_classe='', ConcerneEleve='', SaisieScol='yes';";
		$query = mysqli_query($mysqli, $sql);
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}

		$result .= "Enregistrement de l'engagement 'Membre de l\'association sportive' :<br />";
		$sql="INSERT INTO engagements SET code='S', nom='Membre de l''association sportive', description='Membre de l''association sportive', type='', conseil_de_classe='', ConcerneEleve='yes', SaisieScol='yes';";
		$query = mysqli_query($mysqli, $sql);
		if ($query) {
				$result .= msj_ok("Ok !");
		} else {
				$result .= msj_erreur();
		}
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'engagements_user' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'engagements_user'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS engagements_user (
	id int(11) NOT NULL AUTO_INCREMENT,
	id_engagement int(11) NOT NULL,
	login VARCHAR(50) NOT NULL,
	id_type VARCHAR(20) NOT NULL,
	valeur INT(11) NOT NULL,
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

$result .= "<strong>Ajout d'une table 'archivage_engagements' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'archivage_engagements'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS archivage_engagements (
	id int(11) NOT NULL AUTO_INCREMENT,
	annee VARCHAR(100) NOT NULL,
	ine VARCHAR(255) NOT NULL,
	code_engagement VARCHAR(10) NOT NULL,
	nom_engagement VARCHAR(100) NOT NULL,
	description_engagement TEXT NOT NULL,
	classe VARCHAR(100) NOT NULL,
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

$result .= "&nbsp;-> Ajout d'un champ 'type' à la table 'engagements'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM engagements LIKE 'type';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE engagements ADD type varchar(20) NOT NULL default '' AFTER description;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_groupe' à la table 'archivage_disciplines'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM archivage_disciplines LIKE 'id_groupe';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE archivage_disciplines ADD id_groupe INT(11) NOT NULL DEFAULT '0' AFTER code_matiere;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<strong>Ajout d'une table 'edt_corresp' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_corresp'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS edt_corresp (
	id int(11) NOT NULL AUTO_INCREMENT,
	champ VARCHAR(100) NOT NULL DEFAULT '',
	nom_edt VARCHAR(255) NOT NULL DEFAULT '',
	nom_gepi VARCHAR(255) NOT NULL DEFAULT '',
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

$result .= "<strong>Ajout d'une table 'edt_lignes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_lignes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS edt_lignes (
	id int(11) NOT NULL AUTO_INCREMENT,
	numero varchar(255) NOT NULL default '',
	classe varchar(255) NOT NULL default '',
	mat_code varchar(255) NOT NULL default '',
	mat_libelle varchar(255) NOT NULL default '',
	prof_nom varchar(255) NOT NULL default '',
	prof_prenom varchar(255) NOT NULL default '',
	salle varchar(255) NOT NULL default '',
	jour varchar(255) NOT NULL default '',
	h_debut varchar(255) NOT NULL default '',
	duree varchar(255) NOT NULL default '',
	frequence varchar(10) NOT NULL default '',
	alternance varchar(10) NOT NULL default '',
	effectif varchar(255) NOT NULL default '',
	modalite varchar(255) NOT NULL default '',
	co_ens varchar(255) NOT NULL default '',
	pond varchar(255) NOT NULL default '',
	traitement varchar(100) NOT NULL default '',
	details_cours VARCHAR(255) NOT NULL DEFAULT '',
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

$result .= "&nbsp;-> Ajout d'un champ 'id_salle' à la table 'd_dates_evenements_classes'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM d_dates_evenements_classes LIKE 'id_salle';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE d_dates_evenements_classes ADD id_salle INT(3) NOT NULL default '0';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<strong>Ajout d'une table 'edt_corresp2' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_corresp2'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS edt_corresp2 (
	id int(11) NOT NULL AUTO_INCREMENT,
	id_groupe int(11) NOT NULL,
	mat_code_edt VARCHAR(255) NOT NULL DEFAULT '',
	nom_groupe_edt VARCHAR(255) NOT NULL DEFAULT '',
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

$result .= "<strong>Ajout d'une table 'edt_eleves_lignes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_eleves_lignes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS edt_eleves_lignes (
	id int(11) NOT NULL AUTO_INCREMENT,
	nom varchar(255) NOT NULL default '',
	prenom varchar(255) NOT NULL default '',
	date_naiss varchar(255) NOT NULL default '',
	sexe varchar(255) NOT NULL default '',
	n_national varchar(255) NOT NULL default '',
	classe varchar(255) NOT NULL default '',
	groupes varchar(255) NOT NULL default '',
	option_1 varchar(255) NOT NULL default '',
	option_2 varchar(255) NOT NULL default '',
	option_3 varchar(255) NOT NULL default '',
	option_4 varchar(255) NOT NULL default '',
	option_5 varchar(255) NOT NULL default '',
	option_6 varchar(255) NOT NULL default '',
	option_7 varchar(255) NOT NULL default '',
	option_8 varchar(255) NOT NULL default '',
	option_9 varchar(255) NOT NULL default '',
	option_10 varchar(255) NOT NULL default '',
	option_11 varchar(255) NOT NULL default '',
	option_12 varchar(255) NOT NULL default '',
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

$result .= "<strong>Ajout d'une table 'edt_tempo' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'edt_tempo'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS edt_tempo (
	id int(11) NOT NULL AUTO_INCREMENT,
	col1 varchar(255) NOT NULL default '',
	col2 varchar(255) NOT NULL default '',
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

$result .= "<strong>Mise à jour de la table 'messages' (token) :</strong><br />";
$result_inter = traite_requete("UPDATE `messages` 
	SET `texte`=REPLACE(`texte`,'f_suppression_message\">','f_suppression_message\">\n\t<input type=\"hidden\" name=\"csrf_alea\" value=\"_CSRF_ALEA_\">')
	WHERE `texte` LIKE '%f_suppression_message%' AND NOT `texte` LIKE '%name=\"csrf_alea\"%'");
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "<strong>Mise à jour de la table 'messages' (_CSRF_ALEA_) :</strong><br />";
$result_inter = traite_requete("UPDATE `messages` 
	SET `texte`=REPLACE(`texte`,'_CRSF_ALEA_','_CSRF_ALEA_')
	WHERE `texte` LIKE '%_CRSF_ALEA_%'");
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

?>
