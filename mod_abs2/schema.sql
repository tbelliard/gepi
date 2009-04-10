#-----------------------------------------------------------------------------
#-- a_creneaux
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_creneaux;


CREATE TABLE a_creneaux
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	nom_creneau VARCHAR(50)  NOT NULL COMMENT 'Nom du creneau',
	debut_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui separent l\'horaire de debut avec 00:00:00 du jour',
	fin_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui sÃ©parent l\'horaire de fin avec 00:00:00 du jour',
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
	eleve_id INTEGER(4)  NOT NULL COMMENT 'id_eleve de l\'eleve objet de la saisie, egal Ã  \'appel\' si aucun eleve n\'est saisi',
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
DROP TABLE IF EXISTS j_traitement_absence;

DROP TABLE IF EXISTS j_traitements_saisies;
 CREATE TABLE j_traitements_saisies
(
`a_traitement_id` INT( 12 ) NOT NULL COMMENT 'identifiant du traitement',
`a_saisie_id` INT( 12 ) NOT NULL COMMENT 'identifiant de la saisie',
PRIMARY KEY ( `a_traitement_id` , `a_saisie_id` ),
INDEX j_traitements_saisies_FI_1 (a_traitement_id),
INDEX j_traitements_saisies_FI_2 (a_saisie_id)
) ENGINE = InnoDB COMMENT = 'Table de jointure entre les traitements et la saisie des absences';


DROP TABLE IF EXISTS a_envois;


CREATE TABLE a_envois
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a lance l\'envoi',
	id_type_envoi INTEGER(4)  NOT NULL COMMENT 'id du type de l\'envoi',
	statut_envoi VARCHAR(20) default '0' NOT NULL COMMENT 'Statut de cet envoi (envoye, en cours,...)',
	date_envoi INTEGER(12) default 0 NOT NULL COMMENT 'Date en timestamp UNIX de l\'envoi',
	created_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la saisie de l\'envoi en timestamp UNIX',
	updated_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la modification de l\'envoi en timestamp UNIX',
	PRIMARY KEY (id),
	INDEX a_envois_FI_1 (utilisateur_id),
	INDEX a_envois_FI_2 (id_type_envoi)
)Type=MyISAM COMMENT='Chaque envoi est repertorie ici';

#-----------------------------------------------------------------------------
#-- a_type_envois
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_type_envois;


CREATE TABLE a_type_envois
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	nom VARCHAR(100)  NOT NULL COMMENT 'nom du type de l\'envoi',
	ordre_affichage INTEGER(4)  NOT NULL COMMENT 'ordre d\'affichage du type de l\'envoi',
	contenu LONGTEXT  NOT NULL COMMENT 'Contenu modele de l\'envoi',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Chaque envoi dispose d\'un type qui est stocke ici';

#-----------------------------------------------------------------------------
#-- j_traitements_envois
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_traitements_envois;


CREATE TABLE j_traitements_envois
(
	a_envoi_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'envoi',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',
	PRIMARY KEY (a_envoi_id,a_traitement_id),
  INDEX j_traitements_saisies_FI_1 (a_traitement_id),
  INDEX j_traitements_saisies_FI_2 (a_envoi_id)
)Type=MyISAM COMMENT='Table de jointure entre le traitement des absences et leur envoi';


#-----------------------------------------------------------------------------
#-- Le contenu des tables pour les paramètres
#-----------------------------------------------------------------------------

--
-- Contenu de la table `a_actions`
--

INSERT INTO `a_actions` (`id`, `nom`, `ordre`) VALUES
(1, 'Aucune action', 1),
(2, 'Courrier papier', 2),
(3, 'sms', 3),
(4, 'Courriel', 4),
(5, 'Convocation de l''élève', 5);

--
-- Contenu de la table `a_creneaux`
--

INSERT INTO `a_creneaux` (`id`, `nom_creneau`, `debut_creneau`, `fin_creneau`, `jour_creneau`, `type_creneau`) VALUES
(4, 'M1', 30900, 34200, 9, 'cours'),
(5, 'M2', 34200, 37500, 9, 'cours'),
(7, 'P1', 37500, 38400, 9, 'pause'),
(8, 'M3', 38400, 41700, 9, 'cours'),
(9, 'M4', 41700, 45000, 9, 'cours'),
(10, 'R', 45000, 47100, 9, 'repas'),
(11, 'S1', 47100, 50400, 9, 'cours'),
(12, 'S2', 50400, 53700, 9, 'cours'),
(13, 'S3', 54600, 57900, 9, 'cours'),
(14, 'S4', 57900, 61200, 9, 'cours'),
(15, 'P2', 53700, 54600, 9, 'pause');

--
-- Contenu de la table `a_justifications`
--

INSERT INTO `a_justifications` (`id`, `nom`, `ordre`) VALUES
(1, 'Aucune justification', 1),
(2, 'Par courrier', 2),
(3, 'Par téléphone', 3),
(4, 'Par courriel', 4),
(5, 'Par l''ENT', 5);

--
-- Contenu de la table `a_motifs`
--

INSERT INTO `a_motifs` (`id`, `nom`, `ordre`) VALUES
(1, 'Aucun', 1),
(2, 'Maladie', 2),
(3, 'Convocation interne', 3),
(4, 'Convenances familiales', 4),
(5, 'Infirmerie', 5),
(6, 'Erreur d''emploi du temps', 6),
(7, 'Sortie Pédagogique', 7),
(8, 'Exclusion', 8),
(9, 'Transport', 9);

--
-- Contenu de la table `a_types`
--

INSERT INTO `a_types` (`id`, `nom`, `ordre`) VALUES
(1, 'Absence', 1),
(2, 'Retard', 2),
(3, 'Inclusion', 3),
(4, 'Dispense', 4);
