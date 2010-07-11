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

#-----------------------------------------------------------------------------
#-- a_motifs
#-----------------------------------------------------------------------------

$query = mysql_query("
DROP TABLE IF EXISTS a_motifs;
");


$test = sql_query1("SHOW TABLES LIKE 'a_motifs'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_motifs'. ";
	$sql="
CREATE TABLE a_motifs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des motifs possibles pour une absence';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_motifs': ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_justifications
#-----------------------------------------------------------------------------

$query = mysql_query("
DROP TABLE IF EXISTS a_justifications;
");


$test = sql_query1("SHOW TABLES LIKE 'a_justifications'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_justifications'. ";
	$sql="
CREATE TABLE a_justifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des justifications possibles pour une absence';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_justifications': ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_types
#-----------------------------------------------------------------------------


$query = mysql_query("
DROP TABLE IF EXISTS a_types;
");

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------


$test = sql_query1("SHOW TABLES LIKE 'a_types'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_types'. ";
	$sql="
CREATE TABLE a_types
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	justification_exigible TINYINT COMMENT 'Ce type d\'absence doit entrainer une justification de la part de la famille',
	responsabilite_etablissement TINYINT COMMENT 'L\'eleve est encore sous la responsabilite de l\'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l\'eleve est encore sous la responsabilité de l\'etablissement',
	type_saisie VARCHAR(50) COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des types d\'absences possibles dans l\'etablissement';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types': ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_types_statut
#-----------------------------------------------------------------------------

$query = mysql_query("
DROP TABLE IF EXISTS a_types_statut;
");


$test = sql_query1("SHOW TABLES LIKE 'a_types_statut'");
if ($test == -1) {
	$result .= "<br />Création de la table 'types_statut'. ";
	$sql="
CREATE TABLE a_types_statut
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	id_a_type INTEGER(11)  NOT NULL COMMENT 'Cle etrangere de la table a_type',
	statut VARCHAR(20)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	PRIMARY KEY (id),
	INDEX a_types_statut_FI_1 (id_a_type),
	CONSTRAINT a_types_statut_FK_1
		FOREIGN KEY (id_a_type)
		REFERENCES a_types (id)
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Liste des statuts autorises à saisir des types d\'absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_types_statut': ".$result_inter."<br />";
	}
}

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------

$query = mysql_query("
DROP TABLE IF EXISTS a_saisies;
");


$test = sql_query1("SHOW TABLES LIKE 'a_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_saisies'. ";
	$sql="
CREATE TABLE a_saisies
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(11) default -1 COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à -1 si aucun eleve n\'est saisi',
	commentaire TEXT COMMENT 'commentaire de l\'utilisateur',
	debut_abs DATETIME COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs DATETIME COMMENT 'Fin de l\'absence en timestamp UNIX',
	id_edt_creneau INTEGER(12) default -1 COMMENT 'identifiant du creneaux de l\'emploi du temps',
	id_edt_emplacement_cours INTEGER(12) default -1 COMMENT 'identifiant du cours de l\'emploi du temps',
	id_groupe INTEGER default -1 COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
	id_classe INTEGER default -1 COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
	id_aid INTEGER default -1 COMMENT 'identifiant de l\'aid pour lequel la saisie a ete effectuee',
	id_s_incidents INTEGER default -1 COMMENT 'identifiant de la saisie d\'incident discipline',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_saisies_FI_1 (utilisateur_id),
	CONSTRAINT a_saisies_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_2 (eleve_id),
	CONSTRAINT a_saisies_FK_2
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE,
	INDEX a_saisies_FI_3 (id_edt_creneau),
	CONSTRAINT a_saisies_FK_3
		FOREIGN KEY (id_edt_creneau)
		REFERENCES edt_creneaux (id_definie_periode)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_4 (id_edt_emplacement_cours),
	CONSTRAINT a_saisies_FK_4
		FOREIGN KEY (id_edt_emplacement_cours)
		REFERENCES edt_cours (id_cours)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_5 (id_groupe),
	CONSTRAINT a_saisies_FK_5
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_6 (id_classe),
	CONSTRAINT a_saisies_FK_6
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE SET NULL,
	INDEX a_saisies_FI_7 (id_aid),
	CONSTRAINT a_saisies_FK_7
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l\'emploi du temps, le jours du cours etant precisé dans debut_abs.';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_saisies': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_traitements;");
$test = sql_query1("SHOW TABLES LIKE 'a_traitements'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_traitements'. ";
	$sql="
CREATE TABLE a_traitements
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) default '-1' COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	a_type_id INTEGER(4) default -1 COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) default -1 COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) default -1 COMMENT 'cle etrangere de la justification de l\'absence',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_traitements_FI_1 (utilisateur_id),
	CONSTRAINT a_traitements_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX a_traitements_FI_2 (a_type_id),
	CONSTRAINT a_traitements_FK_2
		FOREIGN KEY (a_type_id)
		REFERENCES a_types (id)
		ON DELETE SET NULL,
	INDEX a_traitements_FI_3 (a_motif_id),
	CONSTRAINT a_traitements_FK_3
		FOREIGN KEY (a_motif_id)
		REFERENCES a_motifs (id)
		ON DELETE SET NULL,
	INDEX a_traitements_FI_4 (a_justification_id),
	CONSTRAINT a_traitements_FK_4
		FOREIGN KEY (a_justification_id)
		REFERENCES a_justifications (id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_traitements': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS j_traitements_saisies;");
$test = sql_query1("SHOW TABLES LIKE 'j_traitements_saisies'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_traitements_saisies'. ";
	$sql="
CREATE TABLE j_traitements_saisies
(
	a_saisie_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'absence saisie',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_saisie_id,a_traitement_id),
	CONSTRAINT j_traitements_saisies_FK_1
		FOREIGN KEY (a_saisie_id)
		REFERENCES a_saisies (id)
		ON DELETE CASCADE,
	INDEX j_traitements_saisies_FI_2 (a_traitement_id),
	CONSTRAINT j_traitements_saisies_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Table de jointure entre la saisie et le traitement des absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'j_traitements_saisies': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS a_notifications;");
$test = sql_query1("SHOW TABLES LIKE 'a_notifications'");
if ($test == -1) {
	$result .= "<br />Création de la table 'a_notifications'. ";
	$sql="
CREATE TABLE a_notifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) default '-1' COMMENT 'Login de l\'utilisateur professionnel qui envoi la notification',
	a_traitement_id INTEGER(12) default -1 NOT NULL COMMENT 'cle etrangere du traitement qu\'on notifie',
	type_notification INTEGER(5) default -1 COMMENT 'type de notification (0 : email, 1 : courrier, 2 : sms)',
	email VARCHAR(100) COMMENT 'email de destination (pour le type email)',
	telephone VARCHAR(100) COMMENT 'numero du telephone de destination (pour le type sms)',
	adr_id VARCHAR(10) COMMENT 'cle etrangere vers l\'adresse de destination (pour le type courrier)',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	statut_envoi INTEGER(5) default 0 COMMENT 'Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_notifications_FI_1 (utilisateur_id),
	CONSTRAINT a_notifications_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX a_notifications_FI_2 (a_traitement_id),
	CONSTRAINT a_notifications_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE CASCADE,
	INDEX a_notifications_FI_3 (adr_id),
	CONSTRAINT a_notifications_FK_3
		FOREIGN KEY (adr_id)
		REFERENCES resp_adr (adr_id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Notification (a la famille) des absences';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_notifications': ".$result_inter."<br />";
	}
}

$query = mysql_query("DROP TABLE IF EXISTS j_notifications_resp_pers;");
$test = sql_query1("SHOW TABLES LIKE 'j_notifications_resp_pers'");
if ($test == -1) {
	$result .= "<br />Création de la table 'j_notifications_resp_pers'. ";
	$sql="
CREATE TABLE j_notifications_resp_pers
(
	a_notification_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de la notification',
	pers_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere des personnes',
	PRIMARY KEY (a_notification_id,pers_id),
	CONSTRAINT j_notifications_resp_pers_FK_1
		FOREIGN KEY (a_notification_id)
		REFERENCES a_notifications (id)
		ON DELETE CASCADE,
	INDEX j_notifications_resp_pers_FI_2 (pers_id),
	CONSTRAINT j_notifications_resp_pers_FK_2
		FOREIGN KEY (pers_id)
		REFERENCES resp_pers (pers_id)
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'a_notifications': ".$result_inter."<br />";
	}
}

//===================================================
?>
