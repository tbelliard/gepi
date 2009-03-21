#-----------------------------------------------------------------------------
#-- a_creneaux
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_creneaux;


CREATE TABLE a_creneaux
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	nom_creneau VARCHAR(50)  NOT NULL COMMENT 'Nom du creneau',
	debut_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui separent l\'horaire de debut avec 00:00:00 du jour',
	fin_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui séparent l\'horaire de fin avec 00:00:00 du jour',
	jour_creneau INTEGER(2) default 9 NOT NULL COMMENT 'Par defaut, aucun jour en particulier mais on peut imposer que des creneaux soient specifiques a un jour en particulier',
	type_creneau VARCHAR(15)  NOT NULL COMMENT '3 types : cours, pause, repas',
	PRIMARY KEY (id)
)Type=InnoDB COMMENT='Les creneaux sont la base du temps des eleves';

#-----------------------------------------------------------------------------
#-- a_actions
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_actions;


CREATE TABLE a_actions
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de l\'action',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage de l\'action dans la liste deroulante',
	PRIMARY KEY (id)
)Type=InnoDB COMMENT='Liste des actions possibles sur une absence';

#-----------------------------------------------------------------------------
#-- a_motifs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_motifs;


CREATE TABLE a_motifs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage du motif dans la liste deroulante',
	PRIMARY KEY (id)
)Type=InnoDB COMMENT='Liste des motifs possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_justifications
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_justifications;


CREATE TABLE a_justifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage de la justification dans la liste deroulante',
	PRIMARY KEY (id)
)Type=InnoDB COMMENT='Liste des justifications possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_types
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_types;


CREATE TABLE a_types
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage du type dans la liste deroulante',
	PRIMARY KEY (id)
)Type=InnoDB COMMENT='Liste des types d\'absences possibles dans l\'etablissement';

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_saisies;


CREATE TABLE a_saisies
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100)  NOT NULL COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(4)  NOT NULL COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à \'appel\' si aucun eleve n\'est saisi',
	created_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la saisie de l\'absence en timestamp UNIX',
	updated_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la modification de la saisie en timestamp UNIX',
	debut_abs INTEGER(12) default 0 NOT NULL COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs INTEGER(12) default 0 NOT NULL COMMENT 'Fin de l\'absence en timestamp UNIX',
	PRIMARY KEY (id),
	INDEX a_saisies_FI_1 (utilisateur_id),
	INDEX a_saisies_FI_2 (eleve_id)
)Type=InnoDB COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies';

#-----------------------------------------------------------------------------
#-- a_traitements
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_traitements;


CREATE TABLE a_traitements
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100)  NOT NULL COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	created_on INTEGER(13)  NOT NULL COMMENT 'Date du traitement de ou des absences en timestamp UNIX',
	updated_on INTEGER(13)  NOT NULL COMMENT 'Date de la modification du traitement de ou des absences en timestamp UNIX',
	a_type_id INTEGER(4)  NOT NULL COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4)  NOT NULL COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4)  NOT NULL COMMENT 'cle etrangere de la justification de l\'absence',
	texte_justification VARCHAR(250)  NOT NULL COMMENT 'Texte additionnel a ce traitement',
	a_action_id INTEGER(4)  NOT NULL COMMENT 'cle etrangere de l\'action sur ce traitement',
	PRIMARY KEY (id),
	INDEX a_traitements_FI_1 (utilisateur_id),
	INDEX a_traitements_FI_2 (a_type_id),
	INDEX a_traitements_FI_3 (a_motif_id),
	INDEX a_traitements_FI_4 (a_justification_id),
	INDEX a_traitements_FI_5 (a_action_id)
)Type=InnoDB;
#-----------------------------------------------------------------------------
#-- a_absences
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_absences;


CREATE TABLE a_absences
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) COMMENT 'Login du dernier utilisateur professionnel compile',
	created_on INTEGER(13)  NOT NULL COMMENT 'Date de la premiere compilation des absences en timestamp UNIX',
	updated_on INTEGER(13)  NOT NULL COMMENT 'Date de la derniere compilation des absences en timestamp UNIX',
	debut_abs INTEGER(12) default 0 NOT NULL COMMENT 'Debut de la compilation en timestamp UNIX',
	fin_abs INTEGER(12) default 0 NOT NULL COMMENT 'Fin de la compilation en timestamp UNIX',
	PRIMARY KEY (id),
	INDEX a_absences_FI_1 (utilisateur_id)
)Type=InnoDB COMMENT='Une absence est la compilation des saisies pour un meme eleve, cette compilation est faite automatiquement par Gepi';

#-----------------------------------------------------------------------------
#-- j_traitement_absence
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_traitement_absence;


CREATE TABLE j_traitement_absence
(
	a_absence_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'absence saisie',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_absence_id,a_traitement_id),
	INDEX j_traitement_absence_FI_1 (a_absence_id),
	INDEX j_traitement_absence_FI_2 (a_traitement_id)
)Type=InnoDB COMMENT='Table de jointure entre la saisie et le traitement des absences';