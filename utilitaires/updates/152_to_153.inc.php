<?php
/* 
 * $Id$
 *
 * Fichier de mise à jour de la version 1.5.2 à la version 1.5.3
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= "<font color='gree'>Champ XXX ajouté avec succès</font>";
 */

$result .= "&nbsp;->Extension à 255 caractères du champ 'SESSION_ID' de la table 'log'<br />";
$query = mysql_query("ALTER TABLE `log` CHANGE `SESSION_ID` `SESSION_ID` VARCHAR( 255 ) NOT NULL;");
if ($query) {
        $result .= "<font color=\"green\">Ok !</font><br />";
} else {
        $result .= "<font color=\"red\">Erreur</font><br />";
}

//===================================================

// Module examens blancs
$test = sql_query1("SHOW TABLES LIKE 'ex_examens'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_examens'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_examens (id int(11) unsigned NOT NULL auto_increment,
		intitule VARCHAR( 255 ) NOT NULL ,description TEXT NOT NULL ,
		date DATE NOT NULL default '0000-00-00',
		etat VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( id ));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_examens': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_matieres'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_matieres'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_matieres (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		matiere VARCHAR( 255 ) NOT NULL ,
		coef DECIMAL(3,1) NOT NULL default '1.0',
		bonus CHAR(1) NOT NULL DEFAULT 'n',
		ordre INT(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_matieres': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_classes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_classes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_classes (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		id_classe int(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_classes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_groupes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_groupes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_groupes (
		id int(11) unsigned NOT NULL auto_increment,
		id_exam int(11) unsigned NOT NULL,
		matiere varchar(50) NOT NULL,
		id_groupe int(11) unsigned NOT NULL,
		type VARCHAR( 255 ) NOT NULL ,
		id_dev int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_groupes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'ex_notes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'ex_notes'. ";
	$sql="CREATE TABLE IF NOT EXISTS ex_notes (
		id int(11) unsigned NOT NULL auto_increment,
		id_ex_grp int(11) unsigned NOT NULL,
		login VARCHAR(255) NOT NULL default '',
		note float(10,1) NOT NULL default '0.0',
		statut varchar(4) NOT NULL default '',
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_notes': ".$result_inter."<br />";
	}
}

//===================================================

// Module examens blancs

$test = sql_query1("SHOW TABLES LIKE 'eb_epreuves'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_epreuves'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_epreuves (
		id int(11) unsigned NOT NULL auto_increment,
		intitule VARCHAR( 255 ) NOT NULL ,
		description TEXT NOT NULL ,
		type_anonymat VARCHAR( 255 ) NOT NULL ,
		date DATE NOT NULL default '0000-00-00',
		etat VARCHAR( 255 ) NOT NULL ,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'ex_examens': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_copies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_copies'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_copies (
		id int(11) unsigned NOT NULL auto_increment,
		login_ele VARCHAR( 255 ) NOT NULL ,
		n_anonymat VARCHAR( 255 ) NOT NULL,
		id_salle INT( 11 ) NOT NULL default '-1',
		login_prof VARCHAR( 255 ) NOT NULL ,
		note float(10,1) NOT NULL default '0.0',
		statut VARCHAR(255) NOT NULL default '',
		id_epreuve int(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_copies': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_salles'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_salles'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_salles (
		id int(11) unsigned NOT NULL auto_increment,
		salle VARCHAR( 255 ) NOT NULL ,
		id_epreuve int(11) unsigned NOT NULL,
		PRIMARY KEY ( id )
		);";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_salles': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_groupes'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_groupes'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_groupes (
		id int(11) unsigned NOT NULL auto_increment,
		id_epreuve int(11) unsigned NOT NULL,
		id_groupe int(11) unsigned NOT NULL,
		transfert varchar(1) NOT NULL DEFAULT 'n',
		PRIMARY KEY ( id )
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_groupes': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'eb_profs'");
if ($test == -1) {
	$result .= "<br />Création de la table 'eb_profs'. ";
	$sql="CREATE TABLE IF NOT EXISTS eb_profs (
		id int(11) unsigned NOT NULL auto_increment,
		id_epreuve int(11) unsigned NOT NULL,
		login_prof VARCHAR(255) NOT NULL default '',
		PRIMARY KEY ( id )
		);";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'eb_profs': ".$result_inter."<br />";
	}
}

?>
