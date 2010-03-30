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

$result .= "<br /><br /><b>Mise à jour vers la version mod_abs2 :</b><br />";

$result .= "&nbsp;->Ajout des tables absence 2<br />";
$query = mysql_query("DROP TABLE IF EXISTS a_actions;");
$test = sql_query1("SHOW TABLES LIKE 'a_actions'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_actions'. ";
	$sql="CREATE TABLE a_actions(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de l\'action',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id))Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_actions': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_motifs;");
$test = sql_query1("SHOW TABLES LIKE 'a_motifs'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_motifs'. ";
	$sql="CREATE TABLE a_motifs (
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id))Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_motifs': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_justifications;");
$test = sql_query1("SHOW TABLES LIKE 'a_justifications'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_justifications'. ";
	$sql="CREATE TABLE a_justifications(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id))Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_justifications': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_types;");
$test = sql_query1("SHOW TABLES LIKE 'a_types'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_types'. ";
	$sql="CREATE TABLE a_types(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	justification_exigible TINYINT COMMENT 'Ce type d\'absence doit entrainer une justification de la part de la famille',
	responsabilite_etablissement TINYINT,
	type_saisie VARCHAR(50) COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_types_statut;");
$test = sql_query1("SHOW TABLES LIKE 'a_types_statut'");
if ($test == -1) {
	$result .= "<br />Création de la table 'types_statut'. ";
	$sql="CREATE TABLE a_types_statut(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	id_a_type INTEGER(11)  NOT NULL COMMENT 'Cle etrangere de la table a_type',
	statut VARCHAR(20)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	PRIMARY KEY (id),
	INDEX a_types_statut_FI_1 (id_a_type))
	Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types_statut': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_saisies;");
$test = sql_query1("SHOW TABLES LIKE 'a_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_saisies'. ";
	$sql="CREATE TABLE a_saisies(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(11) default -1 COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à -1 si aucun eleve n\'est saisi',
	commentaire TEXT COMMENT 'commentaire de l\'utilisateur',
	debut_abs TIME COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs TIME COMMENT 'Fin de l\'absence en timestamp UNIX',
	id_edt_creneau INTEGER(12) default -1 COMMENT 'identifiant du creneaux de l\'emploi du temps',
	id_edt_emplacement_cours INTEGER(12) default -1 COMMENT 'identifiant du cours de l\'emploi du temps',
	id_groupe INTEGER default -1 COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
	id_classe INTEGER default -1 COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_saisies_FI_1 (utilisateur_id),
	INDEX a_saisies_FI_2 (eleve_id),
	INDEX a_saisies_FI_3 (id_edt_creneau),
	INDEX a_saisies_FI_4 (id_edt_emplacement_cours),
	INDEX a_saisies_FI_5 (id_groupe),
	INDEX a_saisies_FI_6 (id_classe)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_saisies': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_traitements;");
$test = sql_query1("SHOW TABLES LIKE 'a_traitements'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_traitements'. ";
	$sql="CREATE TABLE a_traitements(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) default '-1' COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	a_type_id INTEGER(4) default -1 COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) default -1 COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) default -1 COMMENT 'cle etrangere de la justification de l\'absence',
	texte_justification VARCHAR(250) COMMENT 'Texte additionnel à ce traitement',
	a_action_id INTEGER(4) default -1 COMMENT 'cle etrangere de l\'action sur ce traitement',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_traitements_FI_1 (utilisateur_id),
	INDEX a_traitements_FI_2 (a_type_id),
	INDEX a_traitements_FI_3 (a_motif_id),
	INDEX a_traitements_FI_4 (a_justification_id),
	INDEX a_traitements_FI_5 (a_action_id)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_traitements': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS j_traitements_saisies;");
$test = sql_query1("SHOW TABLES LIKE 'j_traitements_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_traitements_saisies'. ";
	$sql="CREATE TABLE j_traitements_saisies(
	a_saisie_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'absence saisie',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_saisie_id,a_traitement_id),
	INDEX j_traitements_saisies_FI_2 (a_traitement_id)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'j_traitements_saisies': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_envois;");
$test = sql_query1("SHOW TABLES LIKE 'a_envois'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_envois'. ";
	$sql="CREATE TABLE a_envois(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) default '-1' COMMENT 'Login de l\'utilisateur professionnel qui a lance l\'envoi',
	id_type_envoi INTEGER(4) default -1 NOT NULL COMMENT 'id du type de l\'envoi',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	statut_envoi VARCHAR(20) default '0' COMMENT 'Statut de cet envoi (envoye, en cours,...)',
	date_envoi TIME COMMENT 'Date en timestamp UNIX de l\'envoi',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_envois_FI_1 (utilisateur_id),
	INDEX a_envois_FI_2 (id_type_envoi)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_envois': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_type_envois;");
$test = sql_query1("SHOW TABLES LIKE 'a_type_envois'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_type_envois'. ";
	$sql="CREATE TABLE a_type_envois(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	nom VARCHAR(100)  NOT NULL COMMENT 'nom du type de l\'envoi',
	contenu LONGTEXT  NOT NULL COMMENT 'Contenu modele de l\'envoi',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_type_envois': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS j_traitements_envois;");
$test = sql_query1("SHOW TABLES LIKE 'j_traitements_envois'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_traitements_envois'. ";
	$sql="CREATE TABLE j_traitements_envois(
	a_envoi_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'envoi',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_envoi_id,a_traitement_id),
	INDEX j_traitements_envois_FI_2 (a_traitement_id)
	)Type=MyISAM;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'j_traitements_envois': ".$result_inter."<br />";
	}
}

mysql_query("INSERT INTO droits VALUES ('/mod_abs2/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');");
mysql_query("INSERT INTO droits VALUES ('/mod_abs2/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');");
mysql_query("INSERT INTO droits VALUES ('/mod_abs2/admin/admin_types_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');");
mysql_query("INSERT INTO droits VALUES ('/mod_abs2/admin/admin_justifications_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');");

//===================================================
?>
