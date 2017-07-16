
-- $Id: structure_gepi.sql 7944 2011-08-24 11:31:56Z crob $
DROP TABLE IF EXISTS `absences`;
CREATE TABLE `absences` (`login` varchar(50) NOT NULL default '', `periode` int(11) NOT NULL default '0', `nb_absences` char(2) NOT NULL default '', `non_justifie` char(2) NOT NULL default '', `nb_retards` char(2) NOT NULL default '', `appreciation` text NOT NULL, PRIMARY KEY  (`login`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `absences_gep`;
CREATE TABLE `absences_gep` ( `id_seq` char(2) NOT NULL default '', `type` char(1) NOT NULL default '', PRIMARY KEY  (`id_seq`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `aid`;
CREATE TABLE `aid` (`id` varchar(100) NOT NULL default '', 
`nom` varchar(100) NOT NULL default '', 
`numero` varchar(8) NOT NULL default '0', 
`indice_aid` int(11) NOT NULL default '0', 
`perso1` varchar(255) NOT NULL default '', 
`perso2` varchar(255) NOT NULL default '', 
`perso3` varchar(255) NOT NULL default '', 
`productions` varchar(100) NOT NULL default '', 
`resume` text NOT NULL, 
`famille` smallint(6) NOT NULL default '0', 
`mots_cles` varchar(255) NOT NULL default '', 
`adresse1` varchar(255) NOT NULL default '', 
`adresse2` varchar(255) NOT NULL default '', 
`public_destinataire` varchar(50) NOT NULL default '', 
`contacts` text NOT NULL, 
`divers` text NOT NULL, 
`matiere1` varchar(100) NOT NULL default '', 
`matiere2` varchar(100) NOT NULL default '', 
`eleve_peut_modifier` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , 
`prof_peut_modifier` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , 
`cpe_peut_modifier` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , 
`fiche_publique` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , 
`affiche_adresse1` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' , 
`en_construction` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n', 
`sous_groupe` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n',
`inscrit_direct` ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n',
PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `aid_appreciations`;
CREATE TABLE `aid_appreciations` ( `login` varchar(50) NOT NULL default '', `id_aid` varchar(100), `periode` int(11) NOT NULL default '0', `appreciation` text NOT NULL, `statut`  char(10) NOT NULL default '', `note` float default NULL, `indice_aid` int(11) NOT NULL default '0', PRIMARY KEY  (`login`,`id_aid`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `aid_config`;
CREATE TABLE `aid_config` (`nom` char(100) NOT NULL default '', `nom_complet` char(100) NOT NULL default '', `note_max` int(11) NOT NULL default '0', `order_display1` char(1) NOT NULL default '0', `order_display2` int(11) NOT NULL default '0', `type_note` char(5) NOT NULL default '', type_aid int(11) NOT NULL default '0', `display_begin` int(11) NOT NULL default '0', `display_end` int(11) NOT NULL default '0', `message` varchar(40) NOT NULL default '', `display_nom` char(1) NOT NULL default '', `indice_aid` int(11) NOT NULL default '0', `display_bulletin` char(1) NOT NULL default 'y', `bull_simplifie` char(1) NOT NULL default 'y', outils_complementaires enum('y','n') NOT NULL default 'n', feuille_presence enum('y','n') NOT NULL default 'n',`autoriser_inscript_multiples` char(1) NOT NULL default 'n', PRIMARY KEY  (`indice_aid`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `avis_conseil_classe`;
CREATE TABLE `avis_conseil_classe` (`login` varchar(50) NOT NULL default '', `periode` int(11) NOT NULL default '0', `avis` text NOT NULL, `id_mention` int(11) NOT NULL default '0', `statut`  varchar(10) NOT NULL default '', PRIMARY KEY  (`login`,`periode`), KEY `login` (`login`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (`id` smallint(6) unsigned NOT NULL auto_increment, `classe` varchar(100) NOT NULL default '', `nom_complet` varchar(100) NOT NULL default '', `suivi_par` varchar(50) NOT NULL default '', `formule` varchar(100) NOT NULL default '', `format_nom` varchar(5) NOT NULL default '', `format_nom_eleve` VARCHAR(5) NOT NULL DEFAULT 'np', `display_rang` char(1) NOT NULL default 'n', `display_address` char(1) NOT NULL default 'n', `display_coef` char(1) NOT NULL default 'y', `display_mat_cat` CHAR(1) NOT NULL default 'n', `display_nbdev` char(1) NOT NULL default 'n', `display_moy_gen` char(1) NOT NULL default 'y', `modele_bulletin_pdf` varchar(255) default NULL, `rn_nomdev` char(1) NOT NULL default 'n', `rn_toutcoefdev` char(1) NOT NULL default 'n', `rn_coefdev_si_diff` char(1) NOT NULL default 'n', `rn_datedev` char(1) NOT NULL default 'n',  `rn_sign_chefetab` char(1) NOT NULL default 'n', `rn_sign_pp` char(1) NOT NULL default 'n', `rn_sign_resp` char(1) NOT NULL default 'n', `rn_sign_nblig` int(11) NOT NULL default '3', `rn_formule` text NOT NULL, ects_type_formation VARCHAR(255) NOT NULL DEFAULT '', ects_parcours VARCHAR(255) NOT NULL DEFAULT '', ects_code_parcours VARCHAR(255) NOT NULL DEFAULT '', ects_domaines_etude VARCHAR(255) NOT NULL DEFAULT '', ects_fonction_signataire_attestation VARCHAR(255) NOT NULL DEFAULT '', apb_niveau VARCHAR(15) NOT NULL DEFAULT '', rn_abs_2 VARCHAR(1) NOT NULL DEFAULT 'n', PRIMARY KEY `id` (`id`), INDEX classe (classe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `cn_cahier_notes`;
CREATE TABLE `cn_cahier_notes` ( `id_cahier_notes` int(11) NOT NULL auto_increment, `id_groupe` INT(11) NOT NULL, `periode` int(11) NOT NULL default '0', PRIMARY KEY  (`id_cahier_notes`, `id_groupe`, `periode`), INDEX groupe_periode (`id_groupe`, `periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `cn_conteneurs`;
CREATE TABLE `cn_conteneurs` ( `id` int(11) NOT NULL auto_increment, `id_racine` int(11) NOT NULL default '0', `nom_court` varchar(32) NOT NULL default '', `nom_complet` varchar(64) NOT NULL default '', `description` varchar(128) NOT NULL default '', `mode` char(1) NOT NULL default '2', `coef` decimal(3,1) NOT NULL default '1.0', `arrondir` char(2) NOT NULL default 's1', `ponderation` decimal(3,1) NOT NULL default '0.0', `display_parents` char(1) NOT NULL default '0', `display_bulletin` char(1) NOT NULL default '1', `parent` int(11) NOT NULL default '0', modele_id_conteneur int(11) NOT NULL default '0', PRIMARY KEY  (`id`), INDEX parent_racine (`parent`,`id_racine`), INDEX racine_bulletin (`id_racine`,`display_bulletin`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `cn_devoirs`;
CREATE TABLE `cn_devoirs` (`id` int(11) NOT NULL auto_increment, `id_conteneur` int(11) NOT NULL default '0', `id_racine` int(11) NOT NULL default '0', `nom_court` varchar(32) NOT NULL default '', `nom_complet` varchar(64) NOT NULL default '', `description` varchar(128) NOT NULL default '', `facultatif` char(1) NOT NULL default '', `date` datetime NOT NULL default '0000-00-00 00:00:00', `coef` decimal(3,1) NOT NULL default '0.0', `note_sur` int(11) default '20', `ramener_sur_referentiel` char(1) NOT NULL default 'F', `display_parents` char(1) NOT NULL default '', `display_parents_app` char(1) NOT NULL default '0', `date_ele_resp` datetime NOT NULL, PRIMARY KEY  (`id`), INDEX conteneur_date (`id_conteneur`, `date`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `cn_notes_conteneurs`;
CREATE TABLE `cn_notes_conteneurs` ( `login` varchar(50) NOT NULL default '', `id_conteneur` int(11) NOT NULL default '0', `note` float(10,1) NOT NULL default '0.0', `statut` char(1) NOT NULL default '', `comment` text NOT NULL, PRIMARY KEY  (`login`,`id_conteneur`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `cn_notes_devoirs`;
CREATE TABLE `cn_notes_devoirs` ( `login` varchar(50) NOT NULL default '', `id_devoir` int(11) NOT NULL default '0', `note` float(10,1) NOT NULL default '0.0', `comment` text NOT NULL, `statut` varchar(4) NOT NULL default '', PRIMARY KEY  (`login`,`id_devoir`), INDEX devoir_statut (`id_devoir`,`statut`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `ct_devoirs_entry`;
CREATE TABLE `ct_devoirs_entry` ( `id_ct` int(11) NOT NULL auto_increment, `id_groupe` INT(11) NOT NULL, `date_ct` int(11) NOT NULL default '0', `id_login` varchar(32) NOT NULL default '',id_sequence INT ( 11 ) NOT NULL DEFAULT '0', `contenu` text NOT NULL, `vise` CHAR( 1 ) NOT NULL DEFAULT 'n', special varchar(20) NOT NULL default '', PRIMARY KEY (`id_ct`), KEY `id_groupe` (`id_groupe`), date_visibilite_eleve TIMESTAMP NOT NULL default now() COMMENT 'Timestamp précisant quand les devoirs sont portés à la connaissance des élèves', INDEX groupe_date (`id_groupe`, `date_ct`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `ct_documents`;
CREATE TABLE `ct_documents` ( `id` int(11) NOT NULL auto_increment, `id_ct` int(11) NOT NULL default '0', `titre` varchar(255) NOT NULL default '', `taille` int(11) NOT NULL default '0', `emplacement` varchar(255) NOT NULL default '', visible_eleve_parent BOOLEAN default true, PRIMARY KEY  (`id`)) ENGINE=MyISAM;
DROP TABLE IF EXISTS `ct_devoirs_documents`;
CREATE TABLE `ct_devoirs_documents` ( `id` int(11) NOT NULL auto_increment, `id_ct_devoir` int(11) NOT NULL default '0', `titre` varchar(255) NOT NULL default '', `taille` int(11) NOT NULL default '0', `emplacement` varchar(255) NOT NULL default '', visible_eleve_parent BOOLEAN default true, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `ct_entry`;
CREATE TABLE `ct_entry` ( `id_ct` int(11) NOT NULL auto_increment, `heure_entry` time NOT NULL default '00:00:00', `id_groupe` INT(11) NOT NULL, `date_ct` int(11) NOT NULL default '0', `id_login` varchar(32) NOT NULL default '', id_sequence INT ( 11 ) NOT NULL DEFAULT '0', `contenu` text NOT NULL, `vise` CHAR( 1 ) NOT NULL DEFAULT 'n', `visa` CHAR( 1 ) NOT NULL DEFAULT 'n', PRIMARY KEY (`id_ct`), KEY `id_groupe` (`id_groupe`), INDEX id_date_heure (id_groupe, date_ct, heure_entry)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `ct_types_documents`;
CREATE TABLE `ct_types_documents` ( `id_type` bigint(21) NOT NULL auto_increment, `titre` text NOT NULL, `extension` varchar(10) NOT NULL default '', `upload` enum('oui','non') NOT NULL default 'oui', PRIMARY KEY  (`id_type`), UNIQUE KEY `extension` (`extension`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `droits`;
CREATE TABLE `droits` ( `id` varchar(200) NOT NULL default '', `administrateur` char(1) NOT NULL default '', `professeur` char(1) NOT NULL default '', `cpe` char(1) NOT NULL default '', `scolarite` char(1) NOT NULL default '', `eleve` char(1) NOT NULL default '', `responsable` char(1) NOT NULL default '', `secours` char(1) NOT NULL default '', `autre` char(1) NOT NULL default 'F', `description` varchar(255) NOT NULL default '', `statut` char(1) NOT NULL default '', PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

-- ---------------------------------------------------------------------
-- eleves
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS eleves;

CREATE TABLE eleves
(
	no_gep VARCHAR(50) NOT NULL COMMENT 'Ancien numero GEP, Numero national de l\'eleve',
	login VARCHAR(50) NOT NULL COMMENT 'Login de l\'eleve, est conserve pour le login utilisateur',
	nom VARCHAR(50) NOT NULL COMMENT 'Nom eleve',
	prenom VARCHAR(50) NOT NULL COMMENT 'Prenom eleve',
	sexe VARCHAR(1) NOT NULL COMMENT 'M ou F',
	naissance DATE NOT NULL COMMENT 'Date de naissance AAAA-MM-JJ',
	lieu_naissance VARCHAR(50) DEFAULT '' NOT NULL COMMENT 'Code de Sconet',
	elenoet VARCHAR(50) NOT NULL COMMENT 'Numero interne de l\'eleve dans l\'etablissement',
	ereno VARCHAR(50) NOT NULL COMMENT 'Plus utilise',
	ele_id VARCHAR(10) DEFAULT '' NOT NULL COMMENT 'cle utilise par Sconet dans ses fichiers xml',
	email VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'Courriel de l\'eleve',
	tel_pers varchar(255) DEFAULT '' NOT NULL COMMENT 'Telephone personnel de l\'eleve',
	tel_port varchar(255) DEFAULT '' NOT NULL COMMENT 'Telephone portable de l\'eleve',
	tel_prof varchar(255) DEFAULT '' NOT NULL COMMENT 'Telephone professionnel (?) de l\'eleve',
	id_eleve INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire autoincremente',
	date_entree DATETIME COMMENT 'Timestamp d\'entrée de l\'élève de l\'établissement (début d\'inscription)',
	date_sortie DATETIME COMMENT 'Timestamp de sortie de l\'élève de l\'établissement (fin d\'inscription)',
	mef_code VARCHAR(50) DEFAULT '' NOT NULL COMMENT 'code mef de la formation de l\'eleve',
	PRIMARY KEY (id_eleve),
	INDEX eleves_FI_1 (mef_code),
	INDEX I_referenced_j_eleves_classes_FK_1_1 (login),
	INDEX I_referenced_responsables2_FK_1_2 (ele_id),
	INDEX I_referenced_archivage_ects_FK_1_3 (no_gep),
	CONSTRAINT eleves_FK_1
		FOREIGN KEY (mef_code)
		REFERENCES mef (mef_code)
		ON DELETE SET NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste des eleves de l\'etablissement';

-- ---------------------------------------------------------------------
-- mef
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `mef`;

CREATE TABLE `mef`
(
	id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
	mef_code VARCHAR(50) DEFAULT '' NOT NULL COMMENT 'Numero de la nomenclature officielle (numero MEF)',
	libelle_court VARCHAR(50) NOT NULL COMMENT 'libelle de la formation',
	libelle_long VARCHAR(300) NOT NULL COMMENT 'libelle de la formation',
	libelle_edition VARCHAR(300) NOT NULL COMMENT 'libelle de la formation pour presentation',
	code_mefstat varchar(50) NOT NULL default '',
	mef_rattachement varchar(50) NOT NULL default '',
	PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Module élémentaire de formation';

DROP TABLE IF EXISTS `etablissements`;
CREATE TABLE `etablissements` ( `id` char(8) NOT NULL default '', `nom` char(50) NOT NULL default '', `niveau` char(50) NOT NULL default '', `type` char(50) NOT NULL default '', `cp` varchar(10) NOT NULL default '0', `ville` char(50) NOT NULL default '', PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aid_eleves`;
CREATE TABLE `j_aid_eleves` ( `id_aid` varchar(100) NOT NULL default '', `login` varchar(60) NOT NULL default '', `indice_aid` int(11) NOT NULL default '0', PRIMARY KEY  (`id_aid`,`login`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aid_utilisateurs`;
CREATE TABLE `j_aid_utilisateurs` ( `id_aid` varchar(100) NOT NULL default '', `id_utilisateur` varchar(50) NOT NULL default '', `indice_aid` int(11) NOT NULL default '0', PRIMARY KEY  (`id_aid`,`id_utilisateur`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_eleves_classes`;
CREATE TABLE `j_eleves_classes` ( `login` varchar(50) NOT NULL default '', `id_classe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `rang` smallint(6) NOT NULL default '0', PRIMARY KEY  (`login`,`id_classe`,`periode`), INDEX id_classe ( `id_classe` ), INDEX login_periode (`login`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_eleves_etablissements`;
CREATE TABLE `j_eleves_etablissements` ( `id_eleve` varchar(50) NOT NULL default '', `id_etablissement` varchar(8) NOT NULL default '',  PRIMARY KEY  (`id_eleve`,`id_etablissement`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_eleves_professeurs`;
CREATE TABLE `j_eleves_professeurs` ( `login` varchar(50) NOT NULL default '', `professeur` varchar(50) NOT NULL default '', `id_classe` int(11) NOT NULL default '0', PRIMARY KEY  (`login`,`professeur`,`id_classe`), INDEX classe_professeur (`id_classe`,`professeur`), INDEX professeur_classe (`professeur`,`id_classe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_eleves_regime`;
CREATE TABLE `j_eleves_regime` ( `login` varchar(50) NOT NULL default '', `doublant` char(1) NOT NULL default '', `regime` varchar(5) NOT NULL default '', PRIMARY KEY  (`login`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_matieres_categories_classes`;
CREATE TABLE `j_matieres_categories_classes` ( `categorie_id` int(11) NOT NULL default '0', `classe_id` int(11) NOT NULL default '0', `priority` smallint(6) NOT NULL default '0', `affiche_moyenne` tinyint(1) NOT NULL default '0', PRIMARY KEY  (`categorie_id`,`classe_id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_professeurs_matieres`;
CREATE TABLE `j_professeurs_matieres` ( `id_professeur` varchar(50) NOT NULL default '', `id_matiere` varchar(50) NOT NULL default '', `ordre_matieres` int(11) NOT NULL default '0', PRIMARY KEY  (`id_professeur`,`id_matiere`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` ( `LOGIN` varchar(50) NOT NULL default '', `START` datetime NOT NULL default '0000-00-00 00:00:00', `SESSION_ID` varchar(255) NOT NULL default '', `REMOTE_ADDR` varchar(16) NOT NULL default '', `USER_AGENT` varchar(255) NOT NULL default '', `REFERER` varchar(64) NOT NULL default '', `AUTOCLOSE` enum('0','1','2','3','4') NOT NULL default '0', `END` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY  (`SESSION_ID`,`START`), INDEX start_time (`START`), INDEX end_time (`END`), INDEX login_session_start (`LOGIN`,`SESSION_ID`,`START`) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres`;
CREATE TABLE `matieres` ( `matiere` varchar(255) NOT NULL default '', `nom_complet` varchar(200) NOT NULL default '', `priority` smallint(6) NOT NULL default '0', `categorie_id` INT NOT NULL default '1', matiere_aid CHAR(1) NOT NULL default 'n', matiere_atelier CHAR(1) NOT NULL default 'n', code_matiere varchar(255) NOT NULL default '', PRIMARY KEY  (`matiere`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_appreciations`;
CREATE TABLE `matieres_appreciations` ( `login` varchar(50) NOT NULL default '', `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `appreciation` text NOT NULL, PRIMARY KEY  (`login`,`id_groupe`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_appreciations_tempo`;
CREATE TABLE `matieres_appreciations_tempo` ( `login` varchar(50) NOT NULL default '', `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `appreciation` text NOT NULL, PRIMARY KEY  (`login`,`id_groupe`,`periode`), INDEX groupe_periode (`id_groupe`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_notes`;
CREATE TABLE `matieres_notes` ( `login` varchar(50) NOT NULL default '', `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `note` float(10,1) default NULL, `statut` varchar(10) NOT NULL default '', `rang` smallint(6) NOT NULL default '0', PRIMARY KEY  (`login`,`id_groupe`,`periode`), INDEX groupe_periode_statut (`id_groupe`,`periode`,`statut`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_categories`;
CREATE TABLE `matieres_categories` (`id` int(11) NOT NULL AUTO_INCREMENT, `nom_court` varchar(255) NOT NULL default '', `nom_complet` varchar(255) NOT NULL default '', `priority` smallint(6) NOT NULL default '0', PRIMARY KEY (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` ( `id` int(11) NOT NULL auto_increment, `texte` text NOT NULL, `date_debut` int(11) NOT NULL default '0', `date_fin` int(11) NOT NULL default '0', `auteur` varchar(50) NOT NULL default '', `statuts_destinataires` varchar(10) NOT NULL default '', login_destinataire VARCHAR( 50 ) NOT NULL default '', `date_decompte` int(11) NOT NULL default '0', PRIMARY KEY  (`id`), INDEX date_debut_fin (`date_debut`,`date_fin`), INDEX `login_destinataire` (`login_destinataire`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `periodes`;
CREATE TABLE `periodes` ( `nom_periode` varchar(50) NOT NULL default '', `num_periode` int(11) NOT NULL default '0', `verouiller` char(1) NOT NULL default '', `id_classe` int(11) NOT NULL default '0', `date_verrouillage` TIMESTAMP NOT NULL,  date_fin TIMESTAMP, date_conseil_classe TIMESTAMP NOT NULL, PRIMARY KEY  (`num_periode`,`id_classe`), INDEX id_classe (`id_classe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `responsables`;
CREATE TABLE `responsables` ( `ereno` varchar(10) NOT NULL default '', `nom1` varchar(50) NOT NULL default '', `prenom1` varchar(50) NOT NULL default '', `adr1` varchar(100) NOT NULL default '', `adr1_comp` varchar(100) NOT NULL default '', `commune1` varchar(50) NOT NULL default '', `cp1` varchar(6) NOT NULL default '', `nom2` varchar(50) NOT NULL default '', `prenom2` varchar(50) NOT NULL default '', `adr2` varchar(100) NOT NULL default '', `adr2_comp` varchar(100) NOT NULL default '', `commune2` varchar(50) NOT NULL default '', `cp2` varchar(6) NOT NULL default '', PRIMARY KEY  (`ereno`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` ( `NAME` varchar(255) NOT NULL default '', `VALUE` text NOT NULL, PRIMARY KEY  (`NAME`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `temp_gep_import`;
CREATE TABLE `temp_gep_import` ( `ID_TEMPO` varchar(40) NOT NULL default '', `LOGIN` varchar(40) NOT NULL default '', `ELENOM` varchar(40) NOT NULL default '', `ELEPRE` varchar(40) NOT NULL default '', `ELESEXE` varchar(40) NOT NULL default '', `ELEDATNAIS` varchar(40) NOT NULL default '', `ELENOET` varchar(40) NOT NULL default '', `ERENO` varchar(40) NOT NULL default '', `ELEDOUBL` varchar(40) NOT NULL default '', `ELENONAT` varchar(40) NOT NULL default '', `ELEREG` varchar(40) NOT NULL default '', `DIVCOD` varchar(40) NOT NULL default '', `ETOCOD_EP` varchar(40) NOT NULL default '', `ELEOPT1` varchar(40) NOT NULL default '', `ELEOPT2` varchar(40) NOT NULL default '', `ELEOPT3` varchar(40) NOT NULL default '', `ELEOPT4` varchar(40) NOT NULL default '', `ELEOPT5` varchar(40) NOT NULL default '', `ELEOPT6` varchar(40) NOT NULL default '', `ELEOPT7` varchar(40) NOT NULL default '', `ELEOPT8` varchar(40) NOT NULL default '', `ELEOPT9` varchar(40) NOT NULL default '', `ELEOPT10` varchar(40) NOT NULL default '', `ELEOPT11` varchar(40) NOT NULL default '', `ELEOPT12` varchar(40) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `tempo`;
CREATE TABLE `tempo` ( `id_classe` int(11) NOT NULL default '0', `max_periode` int(11) NOT NULL default '0', `num` varchar(255) NOT NULL default '0') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `tempo2`;
CREATE TABLE `tempo2` ( `col1` varchar(100) NOT NULL default '', `col2` varchar(100) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` ( `login` varchar(50) NOT NULL default '', `nom` varchar(50) NOT NULL default '', `prenom` varchar(50) NOT NULL default '', `civilite` varchar(5) NOT NULL default '', `password` varchar(128) NOT NULL default '', `salt` varchar(128), `email` varchar(50) NOT NULL default '', `show_email` varchar(3) NOT NULL default 'no', `statut` varchar(20) NOT NULL default '', `etat` varchar(20) NOT NULL default '', `change_mdp` char(1) NOT NULL default 'n', `date_verrouillage` datetime NOT NULL default '2006-01-01 00:00:00', `password_ticket` varchar(255) NOT NULL default '', `ticket_expiration` datetime NOT NULL, `niveau_alerte` SMALLINT NOT NULL DEFAULT '0', `observation_securite` TINYINT NOT NULL DEFAULT '0', `temp_dir` VARCHAR( 255 ) NOT NULL, `numind` varchar(255) NOT NULL, `type` varchar(10) NOT NULL, `auth_mode` enum('gepi', 'ldap', 'sso') NOT NULL default 'gepi', PRIMARY KEY  (`login`), INDEX statut ( `statut` ), INDEX etat ( `etat` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_eleves_cpe;
CREATE TABLE j_eleves_cpe (e_login varchar(50) NOT NULL default '', cpe_login varchar(50) NOT NULL default '', PRIMARY KEY  (e_login,cpe_login)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS suivi_eleve_cpe;
CREATE TABLE `suivi_eleve_cpe` (`id_suivi_eleve_cpe` int(11) NOT NULL auto_increment, `eleve_suivi_eleve_cpe` varchar(30) NOT NULL default '', `parqui_suivi_eleve_cpe` varchar(150) NOT NULL, `date_suivi_eleve_cpe` date NOT NULL default '0000-00-00', `heure_suivi_eleve_cpe` time NOT NULL, `komenti_suivi_eleve_cpe` text NOT NULL, `niveau_message_suivi_eleve_cpe` varchar(1) NOT NULL, `action_suivi_eleve_cpe` VARCHAR( 2 ) NOT NULL, `support_suivi_eleve_cpe` tinyint(4) NOT NULL, `courrier_suivi_eleve_cpe` int(11) NOT NULL,PRIMARY KEY (`id_suivi_eleve_cpe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS absences_eleves;
CREATE TABLE `absences_eleves` (`id_absence_eleve` int(11) NOT NULL auto_increment, `type_absence_eleve` char(1) NOT NULL default '', `eleve_absence_eleve` varchar(25) NOT NULL default '0', `justify_absence_eleve` char(3) NOT NULL default '', `info_justify_absence_eleve` text NOT NULL, `motif_absence_eleve` varchar(4) NOT NULL default '', `info_absence_eleve` text NOT NULL, `d_date_absence_eleve` date NOT NULL default '0000-00-00', `a_date_absence_eleve` date default NULL, `d_heure_absence_eleve` time default NULL, `a_heure_absence_eleve` time default NULL, `saisie_absence_eleve` varchar(50) NOT NULL default '', PRIMARY KEY  (`id_absence_eleve`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS edt_creneaux;
CREATE TABLE `edt_creneaux` (`id_definie_periode` int(11) NOT NULL auto_increment, `nom_definie_periode` varchar(10) NOT NULL default '', `heuredebut_definie_periode` time NOT NULL default '00:00:00', `heurefin_definie_periode` time NOT NULL default '00:00:00', `suivi_definie_periode` tinyint(4) NOT NULL,`type_creneaux` varchar(15) NOT NULL, jour_creneau VARCHAR(20), PRIMARY KEY  (`id_definie_periode`), INDEX heures_debut_fin (heuredebut_definie_periode, heurefin_definie_periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS absences_motifs;
CREATE TABLE `absences_motifs` (`id_motif_absence` int(11) NOT NULL auto_increment, `init_motif_absence` char(2) NOT NULL default '', `def_motif_absence` varchar(255) NOT NULL default '', PRIMARY KEY  (`id_motif_absence`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS groupes;
CREATE TABLE `groupes` (`id` int(11) NOT NULL auto_increment, `name` varchar(60) NOT NULL default '', `description` text NOT NULL, `recalcul_rang` varchar(10) NOT NULL default '', PRIMARY KEY  (`id`), INDEX id_name (`id`,`name`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_groupes_classes;
CREATE TABLE `j_groupes_classes` (`id_groupe` int(11) NOT NULL default '0', `id_classe` int(11) NOT NULL default '0', `priorite` smallint(6) NOT NULL, `coef` decimal(3,1) NOT NULL, `categorie_id` int(11) NOT NULL default '1', saisie_ects TINYINT(1) NOT NULL DEFAULT 0, valeur_ects INT(11) NULL, mode_moy enum('-','sup10','bonus') NOT NULL default '-', apb_langue_vivante varchar(3) NOT NULL DEFAULT '', PRIMARY KEY  (`id_groupe`,`id_classe`), INDEX id_classe_coef (`id_classe`,`coef`), INDEX saisie_ects_id_groupe (`saisie_ects`,`id_groupe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_groupes_matieres;
CREATE TABLE `j_groupes_matieres` (`id_groupe` int(11) NOT NULL default '0',`id_matiere` varchar(50) NOT NULL default '', PRIMARY KEY  (`id_groupe`,`id_matiere`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_groupes_professeurs;
CREATE TABLE `j_groupes_professeurs` (`id_groupe` int(11) NOT NULL default '0',`login` varchar(50) NOT NULL default '', `ordre_prof` smallint(6) NOT NULL default '0', PRIMARY KEY  (`id_groupe`,`login`), INDEX login (`login`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_eleves_groupes;
CREATE TABLE `j_eleves_groupes` (`login` varchar(50) NOT NULL default '', `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', PRIMARY KEY  (`id_groupe`,`login`,`periode`), INDEX login (`login`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eleves_groupes_settings;
CREATE TABLE `eleves_groupes_settings` (login varchar(50) NOT NULL, id_groupe int(11) NOT NULL, `name` varchar(50) NOT NULL, `value` varchar(50) NOT NULL, PRIMARY KEY  (`id_groupe`,`login`,`name`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS preferences;
CREATE TABLE IF NOT EXISTS `preferences` (`login` VARCHAR( 50 ) NOT NULL ,`name` VARCHAR( 255 ) NOT NULL ,`value` TEXT NOT NULL, INDEX login_name (`login`,`name`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_scol_classes;
CREATE TABLE `j_scol_classes` (`login` VARCHAR( 50 ) NOT NULL ,`id_classe` INT( 11 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS miseajour;
CREATE TABLE `miseajour` (`id_miseajour` int(11) NOT NULL auto_increment, `fichier_miseajour` varchar(250) NOT NULL, `emplacement_miseajour` varchar(250) NOT NULL, `date_miseajour` date NOT NULL, `heure_miseajour` time NOT NULL, PRIMARY KEY  (`id_miseajour`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS absences_actions;
CREATE TABLE `absences_actions` (`id_absence_action` int(11) NOT NULL auto_increment, `init_absence_action` char(2) NOT NULL default '', `def_absence_action` varchar(255) NOT NULL default '', PRIMARY KEY  (`id_absence_action`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `responsables2`;
CREATE TABLE IF NOT EXISTS `responsables2` (`ele_id` varchar(10) NOT NULL, `pers_id` varchar(10) NOT NULL, `resp_legal` varchar(1) NOT NULL, `pers_contact` varchar(1) NOT NULL, `acces_sp` varchar(1) NOT NULL, envoi_bulletin char(1) NOT NULL default 'n' COMMENT 'Envoi des bulletins pour les resp_legal=0', INDEX pers_id ( `pers_id` ), INDEX ele_id ( `ele_id` ), INDEX resp_legal ( `resp_legal` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `resp_adr`;
CREATE TABLE IF NOT EXISTS `resp_adr` (`adr_id` varchar(10) NOT NULL,`adr1` varchar(100) NOT NULL,`adr2` varchar(100) NOT NULL,`adr3` varchar(100) NOT NULL,`adr4` varchar(100) NOT NULL,`cp` varchar(6) NOT NULL,`pays` varchar(50) NOT NULL,`commune` varchar(50) NOT NULL,PRIMARY KEY  (`adr_id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `resp_pers`;
CREATE TABLE IF NOT EXISTS `resp_pers` (`pers_id` varchar(10) NOT NULL,`login` varchar(50) NOT NULL,`nom` varchar(50) NOT NULL,`prenom` varchar(50) NOT NULL,`civilite` varchar(5) NOT NULL,`tel_pers` varchar(255) NOT NULL,`tel_port` varchar(255) NOT NULL,`tel_prof` varchar(255) NOT NULL,`mel` varchar(100) NOT NULL,`adr_id` varchar(10) NOT NULL,PRIMARY KEY  (`pers_id`), INDEX login ( `login` ), INDEX adr_id ( `adr_id` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `tentatives_intrusion`;
CREATE TABLE `tentatives_intrusion` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,`login` VARCHAR( 255 ) NULL ,`adresse_ip` VARCHAR( 255 ) NOT NULL ,`date` DATETIME NOT NULL ,`niveau` SMALLINT NOT NULL ,`fichier` VARCHAR( 255 ) NOT NULL ,`description` TEXT NOT NULL ,`statut` VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( `id` ) ,UNIQUE KEY ( `id`, `login` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `etiquettes_formats`;
CREATE TABLE `etiquettes_formats` (`id_etiquette_format` int(11) NOT NULL auto_increment,`nom_etiquette_format` varchar(150) NOT NULL,`xcote_etiquette_format` float NOT NULL,`ycote_etiquette_format` float NOT NULL,`espacementx_etiquette_format` float NOT NULL,`espacementy_etiquette_format` float NOT NULL,`largeur_etiquette_format` float NOT NULL,`hauteur_etiquette_format` float NOT NULL, `nbl_etiquette_format` tinyint(4) NOT NULL,`nbh_etiquette_format` tinyint(4) NOT NULL, PRIMARY KEY  (`id_etiquette_format`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `horaires_etablissement`;
CREATE TABLE `horaires_etablissement` (`id_horaire_etablissement` int(11) NOT NULL auto_increment, `date_horaire_etablissement` date NOT NULL, `jour_horaire_etablissement` varchar(15) NOT NULL, `ouverture_horaire_etablissement` time NOT NULL, `fermeture_horaire_etablissement` time NOT NULL, `pause_horaire_etablissement` time NOT NULL, `ouvert_horaire_etablissement` tinyint(4) NOT NULL, PRIMARY KEY  (`id_horaire_etablissement`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `lettres_cadres`;
CREATE TABLE `lettres_cadres` (`id_lettre_cadre` int(11) NOT NULL auto_increment,`nom_lettre_cadre` varchar(150) NOT NULL,`x_lettre_cadre` float NOT NULL,`y_lettre_cadre` float NOT NULL,`l_lettre_cadre` float NOT NULL,`h_lettre_cadre` float NOT NULL,`texte_lettre_cadre` text NOT NULL,`encadre_lettre_cadre` tinyint(4) NOT NULL,`couleurdefond_lettre_cadre` varchar(11) NOT NULL, PRIMARY KEY  (`id_lettre_cadre`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `lettres_suivis`;
CREATE TABLE `lettres_suivis` (`id_lettre_suivi` int(11) NOT NULL auto_increment, `lettresuitealettren_lettre_suivi` int(11) NOT NULL, `quirecois_lettre_suivi` varchar(50) NOT NULL, `partde_lettre_suivi` varchar(200) NOT NULL, `partdenum_lettre_suivi` text NOT NULL, `quiemet_lettre_suivi` varchar(150) NOT NULL, `emis_date_lettre_suivi` date NOT NULL, `emis_heure_lettre_suivi` time NOT NULL, `quienvoi_lettre_suivi` varchar(150) NOT NULL, `envoye_date_lettre_suivi` date NOT NULL, `envoye_heure_lettre_suivi` time NOT NULL, `type_lettre_suivi` int(11) NOT NULL, `quireception_lettre_suivi` varchar(150) NOT NULL, `reponse_date_lettre_suivi` date NOT NULL, `reponse_remarque_lettre_suivi` varchar(250) NOT NULL, `statu_lettre_suivi` varchar(20) NOT NULL, PRIMARY KEY  (`id_lettre_suivi`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `lettres_tcs`;
CREATE TABLE `lettres_tcs` (`id_lettre_tc` int(11) NOT NULL auto_increment, `type_lettre_tc` int(11) NOT NULL, `cadre_lettre_tc` int(11) NOT NULL, `x_lettre_tc` float NOT NULL, `y_lettre_tc` float NOT NULL, `l_lettre_tc` float NOT NULL, `h_lettre_tc` float NOT NULL, `encadre_lettre_tc` int(1) NOT NULL, PRIMARY KEY  (`id_lettre_tc`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `lettres_types`;
CREATE TABLE `lettres_types` (`id_lettre_type` int(11) NOT NULL auto_increment, `titre_lettre_type` varchar(250) NOT NULL, `categorie_lettre_type` varchar(250) NOT NULL, `reponse_lettre_type` varchar(3) NOT NULL, PRIMARY KEY  (`id_lettre_type`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `vs_alerts_eleves`;
CREATE TABLE `vs_alerts_eleves` (`id_alert_eleve` int(11) NOT NULL auto_increment, `eleve_alert_eleve` varchar(100) NOT NULL, `date_alert_eleve` date NOT NULL, `groupe_alert_eleve` int(11) NOT NULL, `type_alert_eleve` int(11) NOT NULL, `nb_trouve` int(11) NOT NULL, `temp_insert` varchar(100) NOT NULL, `etat_alert_eleve` tinyint(4) NOT NULL, `etatpar_alert_eleve` varchar(100) NOT NULL, PRIMARY KEY  (`id_alert_eleve`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `vs_alerts_groupes`;
CREATE TABLE `vs_alerts_groupes` (`id_alert_groupe` int(11) NOT NULL auto_increment, `nom_alert_groupe` varchar(150) NOT NULL, `creerpar_alert_groupe` varchar(100) NOT NULL, PRIMARY KEY  (`id_alert_groupe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `vs_alerts_types`;
CREATE TABLE `vs_alerts_types` (`id_alert_type` int(11) NOT NULL auto_increment, `groupe_alert_type` int(11) NOT NULL, `type_alert_type` varchar(10) NOT NULL, `specifisite_alert_type` varchar(25) NOT NULL, `eleve_concerne` text NOT NULL, `date_debut_comptage` date NOT NULL, `nb_comptage_limit` varchar(200) NOT NULL, PRIMARY KEY  (`id_alert_type`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `salle_cours`;
CREATE TABLE `salle_cours` (`id_salle` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `numero_salle` VARCHAR( 10 ) NOT NULL , `nom_salle` VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS edt_classes;
CREATE TABLE `edt_classes` (`id_edt_classe` int(11) NOT NULL auto_increment, `groupe_edt_classe` int(11) NOT NULL, `prof_edt_classe` varchar(25) NOT NULL, `matiere_edt_classe` varchar(10) NOT NULL, `semaine_edt_classe` varchar(5) NOT NULL, `jour_edt_classe` tinyint(4) NOT NULL, `datedebut_edt_classe` date NOT NULL, `datefin_edt_classe` date NOT NULL, `heuredebut_edt_classe` time NOT NULL, `heurefin_edt_classe` time NOT NULL, `salle_edt_classe` varchar(50) NOT NULL, PRIMARY KEY  (`id_edt_classe`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `model_bulletin`;
CREATE TABLE `model_bulletin` ( `id_model_bulletin` int(11) NOT NULL auto_increment, `nom_model_bulletin` varchar(100) NOT NULL default '', `active_bloc_datation` decimal(4,0) NOT NULL default '0', `active_bloc_eleve` tinyint(4) NOT NULL default '0', `active_bloc_adresse_parent` tinyint(4) NOT NULL default '0', `active_bloc_absence` tinyint(4) NOT NULL default '0', `active_bloc_note_appreciation` tinyint(4) NOT NULL default '0', `active_bloc_avis_conseil` tinyint(4) NOT NULL default '0', `active_bloc_chef` tinyint(4) NOT NULL default '0', `active_photo` tinyint(4) NOT NULL default '0', `active_coef_moyenne` tinyint(4) NOT NULL default '0', `active_nombre_note` tinyint(4) NOT NULL default '0', `active_nombre_note_case` tinyint(4) NOT NULL default '0', `active_moyenne` tinyint(4) NOT NULL default '0', `active_moyenne_eleve` tinyint(4) NOT NULL default '0', `active_moyenne_classe` tinyint(4) NOT NULL default '0', `active_moyenne_min` tinyint(4) NOT NULL default '0', `active_moyenne_max` tinyint(4) NOT NULL default '0', `active_regroupement_cote` tinyint(4) NOT NULL default '0', `active_entete_regroupement` tinyint(4) NOT NULL default '0', `active_moyenne_regroupement` tinyint(4) NOT NULL default '0', `active_rang` tinyint(4) NOT NULL default '0', `active_graphique_niveau` tinyint(4) NOT NULL default '0', `active_appreciation` tinyint(4) NOT NULL default '0', `affiche_doublement` tinyint(4) NOT NULL default '0', `affiche_date_naissance` tinyint(4) NOT NULL default '0', `affiche_dp` tinyint(4) NOT NULL default '0', `affiche_nom_court` tinyint(4) NOT NULL default '0', `affiche_effectif_classe` tinyint(4) NOT NULL default '0', `affiche_numero_impression` tinyint(4) NOT NULL default '0', `caractere_utilse` varchar(20) NOT NULL default '', `X_parent` float NOT NULL default '0', `Y_parent` float NOT NULL default '0', `X_eleve` float NOT NULL default '0', `Y_eleve` float NOT NULL default '0', `cadre_eleve` tinyint(4) NOT NULL default '0', `X_datation_bul` float NOT NULL default '0', `Y_datation_bul` float NOT NULL default '0', `cadre_datation_bul` tinyint(4) NOT NULL default '0', `hauteur_info_categorie` float NOT NULL default '0', `X_note_app` float NOT NULL default '0', `Y_note_app` float NOT NULL default '0', `longeur_note_app` float NOT NULL default '0', `hauteur_note_app` float NOT NULL default '0', `largeur_coef_moyenne` float NOT NULL default '0', `largeur_nombre_note` float NOT NULL default '0', `largeur_d_une_moyenne` float NOT NULL default '0', `largeur_niveau` float NOT NULL default '0', `largeur_rang` float NOT NULL default '0', `X_absence` float NOT NULL default '0', `Y_absence` float NOT NULL default '0', `hauteur_entete_moyenne_general` float NOT NULL default '0', `X_avis_cons` float NOT NULL default '0', `Y_avis_cons` float NOT NULL default '0', `longeur_avis_cons` float NOT NULL default '0', `hauteur_avis_cons` float NOT NULL default '0', `cadre_avis_cons` tinyint(4) NOT NULL default '0', `X_sign_chef` float NOT NULL default '0', `Y_sign_chef` float NOT NULL default '0', `longeur_sign_chef` float NOT NULL default '0', `hauteur_sign_chef` float NOT NULL default '0', `cadre_sign_chef` tinyint(4) NOT NULL default '0', `affiche_filigrame` tinyint(4) NOT NULL default '0', `texte_filigrame` varchar(100) NOT NULL default '', `affiche_logo_etab` tinyint(4) NOT NULL default '0', `entente_mel` tinyint(4) NOT NULL default '0', `entente_tel` tinyint(4) NOT NULL default '0', `entente_fax` tinyint(4) NOT NULL default '0', `L_max_logo` tinyint(4) NOT NULL default '0', `H_max_logo` tinyint(4) NOT NULL default '0', `toute_moyenne_meme_col` tinyint(4) NOT NULL default '0', `active_reperage_eleve` tinyint(4) NOT NULL default '0', `couleur_reperage_eleve1` smallint(6) NOT NULL default '0', `couleur_reperage_eleve2` smallint(6) NOT NULL default '0', `couleur_reperage_eleve3` smallint(6) NOT NULL default '0', `couleur_categorie_entete` tinyint(4) NOT NULL default '0', `couleur_categorie_entete1` smallint(6) NOT NULL default '0', `couleur_categorie_entete2` smallint(6) NOT NULL default '0', `couleur_categorie_entete3` smallint(6) NOT NULL default '0', `couleur_categorie_cote` tinyint(4) NOT NULL default '0', `couleur_categorie_cote1` smallint(6) NOT NULL default '0', `couleur_categorie_cote2` smallint(6) NOT NULL default '0', `couleur_categorie_cote3` smallint(6) NOT NULL default '0', `couleur_moy_general` tinyint(4) NOT NULL default '0', `couleur_moy_general1` smallint(6) NOT NULL default '0', `couleur_moy_general2` smallint(6) NOT NULL default '0', `couleur_moy_general3` smallint(6) NOT NULL default '0', `titre_entete_matiere` varchar(50) NOT NULL default '', `titre_entete_coef` varchar(20) NOT NULL default '', `titre_entete_nbnote` varchar(20) NOT NULL default '', `titre_entete_rang` varchar(20) NOT NULL default '', `titre_entete_appreciation` varchar(50) NOT NULL default '', `active_coef_sousmoyene` tinyint(4) NOT NULL default '0', `arrondie_choix` float NOT NULL default '0', `nb_chiffre_virgule` tinyint(4) NOT NULL default '0', `chiffre_avec_zero` tinyint(4) NOT NULL default '0', `autorise_sous_matiere` tinyint(4) NOT NULL default '0', `affichage_haut_responsable` tinyint(4) NOT NULL default '0', `entete_model_bulletin` tinyint(4) NOT NULL default '0', `ordre_entete_model_bulletin` tinyint(4) NOT NULL default '0', `affiche_etab_origine` tinyint(4) NOT NULL default '0', `imprime_pour` tinyint(4) NOT NULL default '0', `largeur_matiere` float NOT NULL default '0', `nom_etab_gras` tinyint(4) NOT NULL default '0', `taille_texte_date_edition` float NOT NULL, `taille_texte_matiere` float NOT NULL, `active_moyenne_general` tinyint(4) NOT NULL, `titre_bloc_avis_conseil` varchar(50) NOT NULL, `taille_titre_bloc_avis_conseil` float NOT NULL, `taille_profprincipal_bloc_avis_conseil` float NOT NULL, `affiche_fonction_chef` tinyint(4) NOT NULL, `taille_texte_fonction_chef` float NOT NULL, `taille_texte_identitee_chef` float NOT NULL, `tel_image` varchar(20) NOT NULL, `tel_texte` varchar(20) NOT NULL, `fax_image` varchar(20) NOT NULL, `fax_texte` varchar(20) NOT NULL, `courrier_image` varchar(20) NOT NULL, `courrier_texte` varchar(20) NOT NULL, `largeur_bloc_eleve` float NOT NULL, `hauteur_bloc_eleve` float NOT NULL, `largeur_bloc_adresse` float NOT NULL, `hauteur_bloc_adresse` float NOT NULL, `largeur_bloc_datation` float NOT NULL, `hauteur_bloc_datation` float NOT NULL, `taille_texte_classe` float NOT NULL, `type_texte_classe` varchar(1) NOT NULL, `taille_texte_annee` float NOT NULL, `type_texte_annee` varchar(1) NOT NULL, `taille_texte_periode` float NOT NULL, `type_texte_periode` varchar(1) NOT NULL, `taille_texte_categorie_cote` float NOT NULL, `taille_texte_categorie` float NOT NULL, `type_texte_date_datation` varchar(1) NOT NULL, `cadre_adresse` tinyint(4) NOT NULL, `centrage_logo` tinyint(4) NOT NULL default '0', `Y_centre_logo` float NOT NULL default '18', `ajout_cadre_blanc_photo` tinyint(4) NOT NULL default '0', `affiche_moyenne_mini_general` TINYINT NOT NULL DEFAULT '1', `affiche_moyenne_maxi_general` TINYINT NOT NULL DEFAULT '1', `affiche_date_edition` TINYINT NOT NULL DEFAULT '1', PRIMARY KEY (`id_model_bulletin`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_dates_special`;
CREATE TABLE `edt_dates_special` (`id_edt_date_special` int(11) NOT NULL auto_increment, `nom_edt_date_special` varchar(200) NOT NULL, `debut_edt_date_special` date NOT NULL, `fin_edt_date_special` date NOT NULL, PRIMARY KEY  (`id_edt_date_special`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_semaines`;
CREATE TABLE `edt_semaines` (`id_edt_semaine` int(11) NOT NULL auto_increment,`num_edt_semaine` int(11) NOT NULL default '0',`type_edt_semaine` varchar(10) NOT NULL default '', `num_semaines_etab` int(11) NOT NULL default '0', PRIMARY KEY  (`id_edt_semaine`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_cours`;
CREATE TABLE `edt_cours` (`id_cours` int(11) NOT NULL auto_increment, `id_groupe` varchar(10) NOT NULL, `id_aid` varchar(10) NOT NULL, `id_salle` varchar(3) NOT NULL, `jour_semaine` varchar(10) NOT NULL, `id_definie_periode` varchar(3) NOT NULL, `duree` varchar(10) NOT NULL default '2', `heuredeb_dec` varchar(3) NOT NULL default '0', `id_semaine` varchar(10) NOT NULL default '0', `id_calendrier` varchar(3) NOT NULL default '0', `modif_edt` varchar(3) NOT NULL default '0', `login_prof` varchar(50) NOT NULL, PRIMARY KEY  (`id_cours`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_setting`;
CREATE TABLE `edt_setting` (`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`reglage` VARCHAR( 30 ) NOT NULL ,`valeur` VARCHAR( 30 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_calendrier`;
CREATE TABLE `edt_calendrier` (`id_calendrier` int(11) NOT NULL auto_increment,`classe_concerne_calendrier` text NOT NULL,`nom_calendrier` varchar(100) NOT NULL default '',`debut_calendrier_ts` varchar(11) NOT NULL,`fin_calendrier_ts` varchar(11) NOT NULL,`jourdebut_calendrier` date NOT NULL default '0000-00-00',`heuredebut_calendrier` time NOT NULL default '00:00:00',`jourfin_calendrier` date NOT NULL default '0000-00-00',`heurefin_calendrier` time NOT NULL default '00:00:00',`numero_periode` tinyint(4) NOT NULL default '0',`etabferme_calendrier` tinyint(4) NOT NULL,`etabvacances_calendrier` tinyint(4) NOT NULL,PRIMARY KEY (`id_calendrier`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_init`;
CREATE TABLE `edt_init` (`id_init` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `ident_export` VARCHAR( 100 ) NOT NULL , `nom_export` VARCHAR( 200 ) NOT NULL , `nom_gepi` VARCHAR( 200 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `edt_creneaux_bis`;
CREATE TABLE IF NOT EXISTS edt_creneaux_bis (`id_definie_periode` int(11) NOT NULL auto_increment, `nom_definie_periode` varchar(10) NOT NULL default '', `heuredebut_definie_periode` time NOT NULL default '00:00:00', `heurefin_definie_periode` time NOT NULL default '00:00:00', `suivi_definie_periode` tinyint(4) NOT NULL, `type_creneaux` varchar(15) NOT NULL, PRIMARY KEY  (`id_definie_periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `absences_rb`;
CREATE TABLE `absences_rb` (`id` int(5) NOT NULL auto_increment,`eleve_id` varchar(30) NOT NULL,`retard_absence` varchar(1) NOT NULL default 'A',`groupe_id` varchar(8) NOT NULL,`edt_id` int(5) NOT NULL default '0',`jour_semaine` varchar(10) NOT NULL,`creneau_id` int(5) NOT NULL,`debut_ts` int(11) NOT NULL,`fin_ts` int(11) NOT NULL,`date_saisie` int(20) NOT NULL,`login_saisie` varchar(30) NOT NULL, PRIMARY KEY  (`id`), INDEX eleve_debut_fin_retard (eleve_id, debut_ts, fin_ts, retard_absence) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `inscription_items`;
CREATE TABLE IF NOT EXISTS inscription_items (id int(11) NOT NULL auto_increment, date varchar(10) NOT NULL default '', heure varchar(20) NOT NULL default '', description varchar(200) NOT NULL default '', PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `inscription_j_login_items`;
CREATE TABLE IF NOT EXISTS inscription_j_login_items (login varchar(50) NOT NULL default '', id int(11) NOT NULL default '0') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aidcateg_utilisateurs`;
CREATE TABLE  IF NOT EXISTS `j_aidcateg_utilisateurs` (`indice_aid` INT NOT NULL ,`id_utilisateur` VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aidcateg_super_gestionnaires`;
CREATE TABLE  IF NOT EXISTS `j_aidcateg_super_gestionnaires` (`indice_aid` INT NOT NULL ,`id_utilisateur` VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `aid_familles`;
CREATE TABLE IF NOT EXISTS `aid_familles` (`ordre_affichage` smallint(6) NOT NULL default '0',`id` smallint(6) NOT NULL default '0',`type` varchar(250) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `aid_public`;
CREATE TABLE IF NOT EXISTS `aid_public` (`ordre_affichage` smallint(6) NOT NULL default '0',`id` smallint(6) NOT NULL default '0',`public` varchar(100) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `aid_productions`;
CREATE TABLE IF NOT EXISTS `aid_productions` (`id` smallint(6) NOT NULL auto_increment, `nom` varchar(100) NOT NULL default '', PRIMARY KEY  (`id`) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `droits_aid`;
CREATE TABLE IF NOT EXISTS `droits_aid` (`id` varchar(200) NOT NULL default '',`public` char(1) NOT NULL default '',`professeur` char(1) NOT NULL default '',`cpe` char(1) NOT NULL default '',`scolarite` char(1) NOT NULL default '',`eleve` char(1) NOT NULL default '',`responsable` char(1) NOT NULL default 'F',`secours` char(1) NOT NULL default '',`description` varchar(255) NOT NULL default '',`statut` char(1) NOT NULL default '',PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_appreciations_grp`;
CREATE TABLE `matieres_appreciations_grp` ( `id_groupe` int(11) NOT NULL default '0', `periode` int(11) NOT NULL default '0', `appreciation` text NOT NULL, PRIMARY KEY  (`id_groupe`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `matieres_appreciations_acces`;
CREATE TABLE IF NOT EXISTS `matieres_appreciations_acces` (`id_classe` INT( 11 ) NOT NULL , `statut` VARCHAR( 255 ) NOT NULL , `periode` INT( 11 ) NOT NULL , `date` DATE NOT NULL , `acces` ENUM( 'y', 'n', 'date', 'd' ) NOT NULL ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aid_eleves_resp`;
CREATE TABLE IF NOT EXISTS `j_aid_eleves_resp` (`id_aid` varchar(100) NOT NULL default '',`login` varchar(60) NOT NULL default '',`indice_aid` int(11) NOT NULL default '0',PRIMARY KEY  (`id_aid`,`login`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `ateliers_config`;
CREATE TABLE IF NOT EXISTS `ateliers_config` (`nom_champ` char(100) NOT NULL default '', `content` char(255) NOT NULL default '',`param` char(100) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS `j_aid_utilisateurs_gest`;
CREATE TABLE `j_aid_utilisateurs_gest` ( `id_aid` varchar(100) NOT NULL default '', `id_utilisateur` varchar(50) NOT NULL default '', `indice_aid` int(11) NOT NULL default '0', PRIMARY KEY  (`id_aid`,`id_utilisateur`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet;
CREATE TABLE IF NOT EXISTS notanet (login varchar(50) NOT NULL default '',ine text NOT NULL,id_mat int(4) NOT NULL,notanet_mat varchar(255) NOT NULL,matiere varchar(50) NOT NULL,note varchar(4) NOT NULL default '',note_notanet varchar(4) NOT NULL,id_classe smallint(6) NOT NULL default '0') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_app;
CREATE TABLE IF NOT EXISTS notanet_app (login varchar(50) NOT NULL,id_mat int(4) NOT NULL,matiere varchar(50) NOT NULL,appreciation text NOT NULL,id int(11) NOT NULL auto_increment,PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_corresp;
CREATE TABLE IF NOT EXISTS notanet_corresp (id int(11) NOT NULL auto_increment,type_brevet tinyint(4) NOT NULL,id_mat int(4) NOT NULL,notanet_mat varchar(255) NOT NULL default '',matiere varchar(50) NOT NULL default '',statut enum('imposee','optionnelle','non dispensee dans l etablissement') NOT NULL default 'imposee', mode varchar(20) NOT NULL default 'extract_moy', PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_ele_type;
CREATE TABLE IF NOT EXISTS notanet_ele_type (login varchar(50) NOT NULL,type_brevet tinyint(4) NOT NULL,PRIMARY KEY  (login)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_verrou;
CREATE TABLE IF NOT EXISTS notanet_verrou (id_classe SMALLINT(6) NOT NULL ,type_brevet TINYINT NOT NULL ,verrouillage CHAR( 1 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS droits_statut;
CREATE TABLE IF NOT EXISTS droits_statut (`id` int(11) NOT NULL auto_increment, `nom_statut` varchar(30) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS droits_utilisateurs;
CREATE TABLE IF NOT EXISTS droits_utilisateurs (`id` int(11) NOT NULL auto_increment, `id_statut` int(11) NOT NULL, `login_user` varchar(50) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS droits_speciaux;
CREATE TABLE IF NOT EXISTS droits_speciaux (`id` int(11) NOT NULL auto_increment, `id_statut` int(11) NOT NULL, `nom_fichier` varchar(200) NOT NULL, `autorisation` char(1) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_socles;
CREATE TABLE IF NOT EXISTS notanet_socles (login VARCHAR( 50 ) NOT NULL, b2i ENUM( 'MS', 'ME', 'MN', 'AB' ) NOT NULL ,a2 ENUM( 'MS', 'ME', 'AB' ) NOT NULL ,lv VARCHAR( 50 ) NOT NULL ,PRIMARY KEY ( login )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS notanet_avis;
CREATE TABLE IF NOT EXISTS notanet_avis (login VARCHAR( 50 ) NOT NULL, favorable ENUM( 'O', 'N' ) NOT NULL ,avis TEXT NOT NULL ,PRIMARY KEY ( login )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS communes;
CREATE TABLE IF NOT EXISTS communes (code_commune_insee VARCHAR( 50 ) NOT NULL, departement VARCHAR( 50 ) NOT NULL ,commune VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( code_commune_insee )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_aids;
CREATE TABLE IF NOT EXISTS `archivage_aids` (`id` int(11) NOT NULL auto_increment, `annee` varchar(200) NOT NULL default '',`nom` varchar(100) NOT NULL default '',`id_type_aid` int(11) NOT NULL default '0',`productions` varchar(100) NOT NULL default '',`resume` text NOT NULL,`famille` smallint(6) NOT NULL default '0',`mots_cles` text NOT NULL,`adresse1` varchar(255) NOT NULL default '',`adresse2` varchar(255) NOT NULL default '',`public_destinataire` varchar(50) NOT NULL default '',`contacts` text NOT NULL,`divers` text NOT NULL,`matiere1` varchar(100) NOT NULL default '',`matiere2` varchar(100) NOT NULL default '',`fiche_publique` enum('y','n') NOT NULL default 'n',`affiche_adresse1` enum('y','n') NOT NULL default 'n',`en_construction` enum('y','n') NOT NULL default 'n',`notes_moyenne` varchar(255) NOT NULL,`notes_min` varchar(255) NOT NULL,`notes_max` varchar(255) NOT NULL,`responsables` text NOT NULL,`eleves` text NOT NULL,`eleves_resp` text NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_aid_eleve;
CREATE TABLE IF NOT EXISTS `archivage_aid_eleve` (`id_aid` int(11) NOT NULL default '0',`id_eleve` varchar(255) NOT NULL,`eleve_resp` char(1) NOT NULL default 'n',PRIMARY KEY  (`id_aid`,`id_eleve`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_appreciations_aid;
CREATE TABLE IF NOT EXISTS `archivage_appreciations_aid` (`id_eleve` varchar(255) NOT NULL,`annee` varchar(200) NOT NULL,`classe` varchar(255) NOT NULL,`id_aid` int(11) NOT NULL,`periode` int(11) NOT NULL default '0',`appreciation` text NOT NULL,`note_eleve` varchar(50) NOT NULL,`note_moyenne_classe` varchar(255) NOT NULL,`note_min_classe` varchar(255) NOT NULL,`note_max_classe` varchar(255) NOT NULL,PRIMARY KEY  (`id_eleve`,`id_aid`,`periode`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_disciplines;
CREATE TABLE IF NOT EXISTS `archivage_disciplines` ( `id` int(11) NOT NULL AUTO_INCREMENT, `annee` varchar(200) NOT NULL, `INE` varchar(255) NOT NULL, `classe` varchar(255) NOT NULL, `mef_code` varchar(50) NOT NULL, `num_periode` tinyint(4) NOT NULL, `nom_periode` varchar(255) NOT NULL, `special` varchar(255) NOT NULL, `matiere` varchar(255) NOT NULL, `code_matiere` varchar(255) NOT NULL, `id_groupe` INT(11) NOT NULL DEFAULT '0', `effectif` smallint(6) NOT NULL, `prof` varchar(255) NOT NULL, id_prof varchar(255) NOT NULL default '', type_prof varchar(10) NOT NULL default '', `nom_prof` varchar(50) NOT NULL, `prenom_prof` varchar(50) NOT NULL, `note` varchar(255) NOT NULL, `moymin` varchar(255) NOT NULL, `moymax` varchar(255) NOT NULL, `moyclasse` varchar(255) NOT NULL, `repar_moins_8` float(4,2) NOT NULL, `repar_8_12` float(4,2) NOT NULL, `repar_plus_12` float(4,2) NOT NULL, `rang` tinyint(4) NOT NULL, `appreciation` mediumtext NOT NULL, `nb_absences` int(11) NOT NULL, `non_justifie` int(11) NOT NULL, `nb_retards` int(11) NOT NULL, `ordre_matiere` smallint(6) NOT NULL, PRIMARY KEY (`id`), KEY `annee` (`annee`), KEY `INE` (`INE`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_eleves;
CREATE TABLE IF NOT EXISTS `archivage_eleves` (`ine` varchar(255) NOT NULL,`nom` varchar(255) NOT NULL default '',`prenom` varchar(255) NOT NULL default '',`sexe` char(1) NOT NULL,`naissance` date NOT NULL default '0000-00-00', PRIMARY KEY  (`ine`),  KEY `nom` (`nom`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_eleves2;
CREATE TABLE IF NOT EXISTS `archivage_eleves2` (`annee` varchar(50) NOT NULL default '',`ine` varchar(50) NOT NULL,`doublant` enum('-','R') NOT NULL default '-',`regime` varchar(255) NOT NULL, PRIMARY KEY  (`ine`,`annee`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_types_aid;
CREATE TABLE IF NOT EXISTS `archivage_types_aid` (`id` int(11) NOT NULL auto_increment,`annee` varchar(200) NOT NULL default '',`nom` varchar(100) NOT NULL default '',`nom_complet` varchar(100) NOT NULL default '',`note_sur` int(11) NOT NULL default '0',`type_note` varchar(5) NOT NULL default '',  `display_bulletin` char(1) NOT NULL default 'y', `outils_complementaires` enum('y','n') NOT NULL default 'n', PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS commentaires_types;
CREATE TABLE IF NOT EXISTS `commentaires_types` (`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`commentaire` TEXT NOT NULL ,`num_periode` INT NOT NULL ,`id_classe` INT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS commentaires_types_profs;
CREATE TABLE IF NOT EXISTS commentaires_types_profs (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,login VARCHAR( 50 ) NOT NULL ,app TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_incidents;
CREATE TABLE IF NOT EXISTS s_incidents (id_incident INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,declarant VARCHAR( 50 ) NOT NULL ,date DATE NOT NULL ,heure VARCHAR( 20 ) NOT NULL ,id_lieu INT( 11 ) NOT NULL ,nature VARCHAR( 255 ) NOT NULL , id_categorie INT(11), description TEXT NOT NULL,etat VARCHAR( 20 ) NOT NULL, message_id VARCHAR(50) NOT NULL, primo_declarant VARCHAR(50), commentaire TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_qualites;
CREATE TABLE IF NOT EXISTS s_qualites (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,qualite VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_types_sanctions2;
CREATE TABLE IF NOT EXISTS s_types_sanctions2 (id_nature INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,nature VARCHAR( 255 ) NOT NULL ,type VARCHAR( 255 ) NOT NULL DEFAULT 'autre', saisie_prof char(1) NOT NULL default 'n', rang INT(11) NOT NULL default 0) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_autres_sanctions;
CREATE TABLE IF NOT EXISTS s_autres_sanctions (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_sanction INT( 11 ) NOT NULL ,id_nature INT( 11 ) NOT NULL ,description TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_mesures;
CREATE TABLE IF NOT EXISTS s_mesures (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,type ENUM('prise','demandee') ,mesure VARCHAR( 50 ) NOT NULL ,commentaire TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_traitement_incident;
CREATE TABLE IF NOT EXISTS s_traitement_incident (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_incident INT( 11 ) NOT NULL ,login_ele VARCHAR( 50 ) NOT NULL ,login_u VARCHAR( 50 ) NOT NULL ,id_mesure INT( 11 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_lieux_incidents;
CREATE TABLE IF NOT EXISTS s_lieux_incidents (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,lieu VARCHAR( 255 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_protagonistes;
CREATE TABLE IF NOT EXISTS s_protagonistes (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_incident INT NOT NULL ,login VARCHAR( 50 ) NOT NULL ,statut VARCHAR( 50 ) NOT NULL ,qualite VARCHAR( 50 ) NOT NULL,avertie ENUM('N','O') NOT NULL DEFAULT 'N') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_sanctions;
CREATE TABLE IF NOT EXISTS s_sanctions (id_sanction INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,login VARCHAR( 50 ) NOT NULL ,description TEXT NOT NULL ,nature VARCHAR( 255 ) NOT NULL ,id_nature_sanction INT(11), effectuee ENUM( 'N', 'O' ) NOT NULL ,id_incident INT( 11 ) NOT NULL, saisie_par varchar(255) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_communication;
CREATE TABLE IF NOT EXISTS s_communication (id_communication INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_incident INT( 11 ) NOT NULL ,login VARCHAR( 50 ) NOT NULL ,nature VARCHAR( 255 ) NOT NULL ,description TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_travail;
CREATE TABLE IF NOT EXISTS s_travail (id_travail INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_sanction INT( 11 ) NOT NULL ,date_retour DATE NOT NULL ,heure_retour VARCHAR( 20 ) NOT NULL ,travail TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_retenues;
CREATE TABLE IF NOT EXISTS s_retenues (id_retenue INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_sanction INT( 11 ) NOT NULL ,date DATE NOT NULL ,heure_debut VARCHAR( 20 ) NOT NULL ,duree FLOAT NOT NULL ,travail TEXT NOT NULL ,lieu VARCHAR( 255 ) NOT NULL , materiel VARCHAR( 150 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_exclusions;
CREATE TABLE s_exclusions (id_exclusion int(11) NOT NULL AUTO_INCREMENT,  id_sanction int(11) NOT NULL DEFAULT '0',  date_debut date NOT NULL DEFAULT '0000-00-00',  heure_debut varchar(20) NOT NULL DEFAULT '',  date_fin date NOT NULL DEFAULT '0000-00-00',  heure_fin varchar(20) NOT NULL DEFAULT '',  travail text NOT NULL,  lieu varchar(255) NOT NULL DEFAULT '',  nombre_jours varchar(50) NOT NULL,  qualification_faits text NOT NULL,  num_courrier varchar(50) NOT NULL,  type_exclusion varchar(50) NOT NULL, id_signataire INT NOT NULL,  PRIMARY KEY (id_exclusion)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_reports;
CREATE TABLE s_reports (id_report int(11) NOT NULL AUTO_INCREMENT,  id_sanction int(11) NOT NULL,  id_type_sanction int(11) NOT NULL,  nature_sanction varchar(255) NOT NULL,  `date` date NOT NULL,  informations text NOT NULL,  motif_report varchar(255) NOT NULL,  PRIMARY KEY (id_report)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS absences_repas;
CREATE TABLE IF NOT EXISTS absences_repas (`id` int(5) NOT NULL AUTO_INCREMENT, `date_repas` date NOT NULL default '0000-00-00', `id_groupe` varchar(8) NOT NULL, `eleve_id` varchar(30) NOT NULL, `pers_id` varchar(30) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_projets;
CREATE TABLE IF NOT EXISTS gc_projets (id smallint(6) unsigned NOT NULL auto_increment,projet VARCHAR( 255 ) NOT NULL ,commentaire TEXT NOT NULL ,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_divisions;
CREATE TABLE IF NOT EXISTS gc_divisions (id int(11) unsigned NOT NULL auto_increment,projet VARCHAR( 255 ) NOT NULL ,id_classe smallint(6) unsigned NOT NULL,classe varchar(100) NOT NULL default '',statut enum( 'actuelle', 'future', 'red', 'arriv' ) NOT NULL DEFAULT 'future',PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_options;
CREATE TABLE IF NOT EXISTS gc_options (id int(11) unsigned NOT NULL auto_increment,projet VARCHAR( 255 ) NOT NULL ,opt VARCHAR( 255 ) NOT NULL ,type ENUM('lv1','lv2','lv3','autre') NOT NULL ,obligatoire ENUM('o','n') NOT NULL ,exclusive smallint(6) unsigned NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_options_classes;
CREATE TABLE IF NOT EXISTS gc_options_classes (id int(11) unsigned NOT NULL auto_increment,projet VARCHAR( 255 ) NOT NULL ,opt_exclue VARCHAR( 255 ) NOT NULL ,classe_future VARCHAR( 255 ) NOT NULL ,commentaire TEXT NOT NULL ,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_ele_arriv_red;
CREATE TABLE IF NOT EXISTS gc_ele_arriv_red (login VARCHAR( 50 ) NOT NULL,statut ENUM('Arriv','Red') NOT NULL ,projet VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( login , projet )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_affichages;
CREATE TABLE IF NOT EXISTS gc_affichages (id int(11) unsigned NOT NULL auto_increment,id_aff int(11) unsigned NOT NULL,id_req int(11) unsigned NOT NULL,projet VARCHAR( 255 ) NOT NULL , nom_requete VARCHAR( 255 ) NOT NULL, type VARCHAR(255) NOT NULL,valeur varchar(255) NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS gc_eleves_options;
CREATE TABLE IF NOT EXISTS gc_eleves_options (id int(11) unsigned NOT NULL auto_increment,login VARCHAR( 50 ) NOT NULL ,profil VARCHAR(10) NOT NULL default 'RAS',moy VARCHAR( 255 ) NOT NULL ,nb_absences VARCHAR( 255 ) NOT NULL ,non_justifie VARCHAR( 255 ) NOT NULL ,nb_retards VARCHAR( 255 ) NOT NULL ,projet VARCHAR( 255 ) NOT NULL ,id_classe_actuelle VARCHAR(255) NOT NULL ,classe_future VARCHAR(255) NOT NULL ,liste_opt VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS plugins;
CREATE TABLE IF NOT EXISTS plugins (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,nom VARCHAR( 100 ) NOT NULL,repertoire VARCHAR( 255 ) NOT NULL,description LONGTEXT NOT NULL,ouvert CHAR( 1 ) default 'n',  UNIQUE KEY `nom` (`nom`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS plugins_autorisations;
CREATE TABLE IF NOT EXISTS plugins_autorisations (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,plugin_id INT( 11 ) NOT NULL,fichier VARCHAR( 100 ) NOT NULL,user_statut VARCHAR( 50 ) NOT NULL,auth CHAR( 1 ) default 'n') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS plugins_menus;
CREATE TABLE IF NOT EXISTS plugins_menus (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,plugin_id INT( 11 ) NOT NULL,user_statut VARCHAR( 50 ) NOT NULL,titre_item VARCHAR ( 255 ) NOT NULL,lien_item VARCHAR( 255 ) NOT NULL,description_item VARCHAR( 255 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ects_credits;
CREATE TABLE IF NOT EXISTS ects_credits (id INTEGER(11)  NOT NULL AUTO_INCREMENT, id_eleve INTEGER(11)  NOT NULL COMMENT 'Identifiant de l\'eleve', num_periode INTEGER(11)  NOT NULL COMMENT 'Identifiant de la periode', id_groupe INTEGER(11)  NOT NULL COMMENT 'Identifiant du groupe', valeur DECIMAL(3,1) COMMENT 'Nombre de credits obtenus par l\'eleve', mention VARCHAR(255) COMMENT 'Mention obtenue', `mention_prof` VARCHAR(255) COMMENT 'Mention presaisie par le prof', PRIMARY KEY (id,id_eleve,num_periode,id_groupe), INDEX ects_credits_FI_1 (id_eleve), CONSTRAINT ects_credits_FK_1 FOREIGN KEY (id_eleve) REFERENCES eleves (id_eleve), INDEX ects_credits_FI_2 (id_groupe), CONSTRAINT ects_credits_FK_2 FOREIGN KEY (id_groupe) REFERENCES groupes (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS archivage_ects;
CREATE TABLE IF NOT EXISTS archivage_ects (id INTEGER(11)  NOT NULL AUTO_INCREMENT, annee VARCHAR(255)  NOT NULL COMMENT 'Annee scolaire', ine VARCHAR(55)  NOT NULL COMMENT 'Identifiant de l\'eleve', classe VARCHAR(255)  NOT NULL COMMENT 'Classe de l\'eleve', num_periode INTEGER(11)  NOT NULL COMMENT 'Identifiant de la periode', nom_periode VARCHAR(255)  NOT NULL COMMENT 'Nom complet de la periode', special VARCHAR(255)  NOT NULL COMMENT 'Cle utilisee pour isoler certaines lignes (par exemple un credit ECTS pour une periode et non une matiere)', matiere VARCHAR(255) COMMENT 'Nom de l\'enseignement', profs VARCHAR(255) COMMENT 'Liste des profs de l\'enseignement', valeur DECIMAL  NOT NULL COMMENT 'Nombre de crédits obtenus par l\'eleve', mention VARCHAR(255)  NOT NULL COMMENT 'Mention obtenue', PRIMARY KEY (id,ine,num_periode,special), INDEX archivage_ects_FI_1 (ine), CONSTRAINT archivage_ects_FK_1 FOREIGN KEY (ine) REFERENCES eleves (no_gep)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ects_global_credits;
CREATE TABLE IF NOT EXISTS ects_global_credits (id INTEGER(11)  NOT NULL AUTO_INCREMENT, id_eleve INTEGER(11)  NOT NULL COMMENT 'Identifiant de l\'eleve', mention VARCHAR(255)  NOT NULL COMMENT 'Mention obtenue', PRIMARY KEY (id,id_eleve), INDEX ects_global_credits_FI_1 (id_eleve), CONSTRAINT ects_global_credits_FK_1 FOREIGN KEY (id_eleve) REFERENCES eleves (id_eleve)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ref_wiki;
CREATE TABLE IF NOT EXISTS ref_wiki (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , ref VARCHAR( 255 ) NOT NULL , url VARCHAR( 255 ) NOT NULL , INDEX ( ref ) ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ex_examens;
CREATE TABLE IF NOT EXISTS ex_examens (id int(11) unsigned NOT NULL auto_increment,intitule VARCHAR( 255 ) NOT NULL ,description TEXT NOT NULL ,date DATE NOT NULL default '0000-00-00',etat VARCHAR( 255 ) NOT NULL ,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ex_matieres;
CREATE TABLE IF NOT EXISTS ex_matieres (id int(11) unsigned NOT NULL auto_increment,id_exam int(11) unsigned NOT NULL,matiere VARCHAR( 255 ) NOT NULL ,coef DECIMAL(3,1) NOT NULL default '1.0',bonus CHAR(1) NOT NULL DEFAULT 'n',ordre INT(11) unsigned NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ex_classes;
CREATE TABLE IF NOT EXISTS ex_classes (id int(11) unsigned NOT NULL auto_increment,id_exam int(11) unsigned NOT NULL,id_classe int(11) unsigned NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ex_groupes;
CREATE TABLE IF NOT EXISTS ex_groupes (id int(11) unsigned NOT NULL auto_increment,id_exam int(11) unsigned NOT NULL,matiere varchar(50) NOT NULL,id_groupe int(11) unsigned NOT NULL,type VARCHAR( 255 ) NOT NULL ,id_dev int(11) NOT NULL DEFAULT '0', valeur VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ex_notes;
CREATE TABLE IF NOT EXISTS ex_notes (id int(11) unsigned NOT NULL auto_increment,id_ex_grp int(11) unsigned NOT NULL,login VARCHAR(50) NOT NULL default '',note float(10,1) NOT NULL default '0.0',statut varchar(4) NOT NULL default '',PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eb_epreuves;
CREATE TABLE IF NOT EXISTS eb_epreuves (id int(11) unsigned NOT NULL auto_increment,intitule VARCHAR( 255 ) NOT NULL ,description TEXT NOT NULL ,type_anonymat VARCHAR( 255 ) NOT NULL ,date DATE NOT NULL default '0000-00-00',etat VARCHAR( 255 ) NOT NULL, note_sur int(11) unsigned not null default '20', PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eb_copies;
CREATE TABLE IF NOT EXISTS eb_copies (id int(11) unsigned NOT NULL auto_increment,login_ele VARCHAR( 255 ) NOT NULL ,n_anonymat VARCHAR( 255 ) NOT NULL,id_salle INT( 11 ) NOT NULL default '-1',login_prof VARCHAR( 255 ) NOT NULL ,note float(10,1) NOT NULL default '0.0',statut VARCHAR(255) NOT NULL default '',id_epreuve int(11) unsigned NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eb_salles;
CREATE TABLE IF NOT EXISTS eb_salles (id int(11) unsigned NOT NULL auto_increment,salle VARCHAR( 255 ) NOT NULL ,id_epreuve int(11) unsigned NOT NULL,PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eb_groupes;
CREATE TABLE IF NOT EXISTS eb_groupes (id int(11) unsigned NOT NULL auto_increment,id_epreuve int(11) unsigned NOT NULL,id_groupe int(11) unsigned NOT NULL,transfert varchar(1) NOT NULL DEFAULT 'n',PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS eb_profs;
CREATE TABLE IF NOT EXISTS eb_profs (id int(11) unsigned NOT NULL auto_increment,id_epreuve int(11) unsigned NOT NULL,login_prof VARCHAR(255) NOT NULL default '',PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS synthese_app_classe;
CREATE TABLE IF NOT EXISTS synthese_app_classe (  id_classe int(11) NOT NULL default '0',  periode int(11) NOT NULL default '0',  synthese text NOT NULL,  PRIMARY KEY  (id_classe,periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS message_login;
CREATE TABLE IF NOT EXISTS message_login (id int(11) NOT NULL auto_increment,texte text NOT NULL,PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS pays;
CREATE TABLE IF NOT EXISTS pays (code_pays VARCHAR( 50 ) NOT NULL, nom_pays VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( code_pays )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_signalement;
CREATE TABLE IF NOT EXISTS j_signalement (id_groupe int(11) NOT NULL default '0',login varchar(50) NOT NULL default '',periode int(11) NOT NULL default '0',nature varchar(50) NOT NULL default '',valeur varchar(50) NOT NULL default '',declarant varchar(50) NOT NULL default '',PRIMARY KEY (id_groupe,login,periode,nature), INDEX (login)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_alerte_mail;
CREATE TABLE IF NOT EXISTS s_alerte_mail (id int(11) unsigned NOT NULL auto_increment, id_classe smallint(6) unsigned NOT NULL, destinataire varchar(50) NOT NULL default '', adresse varchar(250) DEFAULT NULL, type varchar(50) NULL NULL DEFAULT 'mail', PRIMARY KEY (id), INDEX (id_classe,destinataire)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_categories;
CREATE TABLE IF NOT EXISTS s_categories ( id INT(11) NOT NULL auto_increment, categorie varchar(50) NOT NULL default '',sigle varchar(20) NOT NULL default '', PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS matieres_app_corrections;
CREATE TABLE IF NOT EXISTS matieres_app_corrections (login varchar(50) NOT NULL default '', id_groupe int(11) NOT NULL default '0', periode int(11) NOT NULL default '0', appreciation text NOT NULL, PRIMARY KEY (login,id_groupe,periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ---------------------------------------------------------------------
-- a_motifs
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_motifs;

CREATE TABLE a_motifs
(
	id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250) NOT NULL COMMENT 'Nom du motif',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	valable VARCHAR(3) NOT NULL default 'y' COMMENT 'caractere valable ou non du motif',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste des motifs possibles pour une absence';

-- ---------------------------------------------------------------------
-- a_justifications
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_justifications;

CREATE TABLE a_justifications
(
	id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250) NOT NULL COMMENT 'Nom de la justification',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste des justifications possibles pour une absence';

-- ---------------------------------------------------------------------
-- a_types
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_types;

CREATE TABLE a_types
(
	id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250) NOT NULL COMMENT 'Nom du type d\'absence',
	justification_exigible TINYINT COMMENT 'Ce type d\'absence doit entrainer une justification de la part de la famille',
	sous_responsabilite_etablissement VARCHAR(255) DEFAULT 'NON_PRECISE' COMMENT 'L\'eleve est sous la responsabilite de l\'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l\'eleve est encore sous la responsabilité de l\'etablissement. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	manquement_obligation_presence VARCHAR(50) DEFAULT 'NON_PRECISE' COMMENT 'L\'eleve manque à ses obligations de presence (L\'absence apparait sur le bulletin). Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	retard_bulletin VARCHAR(50) DEFAULT 'NON_PRECISE' COMMENT 'La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	mode_interface VARCHAR(50) DEFAULT 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE, CHECKBOX, CHECKBOX_HIDDEN',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'élève',
	sortable_rank INTEGER,
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_types_FI_1 (id_lieu),
	CONSTRAINT a_types_FK_1
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Liste des types d\'absences possibles dans l\'etablissement';

DROP TABLE IF EXISTS a_types_statut;
CREATE TABLE IF NOT EXISTS a_types_statut(	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',	id_a_type INTEGER(11)  NOT NULL COMMENT 'Cle etrangere de la table a_type',	statut VARCHAR(20)  NOT NULL COMMENT 'Statut de l\'utilisateur',	PRIMARY KEY (id),	INDEX a_types_statut_FI_1 (id_a_type),	CONSTRAINT a_types_statut_FK_1		FOREIGN KEY (id_a_type)		REFERENCES a_types (id)		ON DELETE CASCADE) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Liste des statuts autorises à saisir des types d\'absences';

-- ---------------------------------------------------------------------
-- a_saisies
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_saisies;

CREATE TABLE a_saisies
(
	id INTEGER(11) NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(11) COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à null si aucun eleve n\'est saisi',
	commentaire TEXT COMMENT 'commentaire de l\'utilisateur',
	debut_abs DATETIME COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs DATETIME COMMENT 'Fin de l\'absence en timestamp UNIX',
	id_edt_creneau INTEGER(12) COMMENT 'identifiant du creneaux de l\'emploi du temps',
	id_edt_emplacement_cours INTEGER(12) COMMENT 'identifiant du cours de l\'emploi du temps',
	id_groupe INTEGER COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
	id_classe INTEGER COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
	id_aid INTEGER COMMENT 'identifiant de l\'aid pour lequel la saisie a ete effectuee',
	id_s_incidents INTEGER COMMENT 'identifiant de la saisie d\'incident discipline',
	id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve',
	deleted_by VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a supprimé la saisie',
	created_at DATETIME COMMENT 'Date de creation de la saisie',
	updated_at DATETIME COMMENT 'Date de modification de la saisie, y compris suppression, restauration et changement de version',
	deleted_at DATETIME,
	version INTEGER DEFAULT 0,
	version_created_at DATETIME,
	version_created_by VARCHAR(100),
	PRIMARY KEY (id),
	INDEX a_saisies_I_1 (deleted_at),
	INDEX a_saisies_I_2 (debut_abs),
	INDEX a_saisies_I_3 (fin_abs),
	INDEX a_saisies_FI_1 (utilisateur_id),
	INDEX a_saisies_FI_2 (eleve_id),
	INDEX a_saisies_FI_3 (id_edt_creneau),
	INDEX a_saisies_FI_4 (id_edt_emplacement_cours),
	INDEX a_saisies_FI_5 (id_groupe),
	INDEX a_saisies_FI_6 (id_classe),
	INDEX a_saisies_FI_7 (id_aid),
	INDEX a_saisies_FI_8 (id_lieu),
	CONSTRAINT a_saisies_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
	CONSTRAINT a_saisies_FK_2
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE,
	CONSTRAINT a_saisies_FK_3
		FOREIGN KEY (id_edt_creneau)
		REFERENCES edt_creneaux (id_definie_periode)
		ON DELETE SET NULL,
	CONSTRAINT a_saisies_FK_4
		FOREIGN KEY (id_edt_emplacement_cours)
		REFERENCES edt_cours (id_cours)
		ON DELETE SET NULL,
	CONSTRAINT a_saisies_FK_5
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE SET NULL,
	CONSTRAINT a_saisies_FK_6
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE SET NULL,
	CONSTRAINT a_saisies_FK_7
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE SET NULL,
	CONSTRAINT a_saisies_FK_8
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l\'emploi du temps, le jours du cours etant precisé dans debut_abs.';

-- ---------------------------------------------------------------------
-- a_traitements
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_traitements;

CREATE TABLE a_traitements
(
	id INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	a_type_id INTEGER(4) COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) COMMENT 'cle etrangere de la justification de l\'absence',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	modifie_par_utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a modifie en dernier le traitement',
	created_at DATETIME,
	updated_at DATETIME,
	deleted_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_traitements_I_1 (deleted_at),
	INDEX a_traitements_FI_1 (utilisateur_id),
	INDEX a_traitements_FI_2 (a_type_id),
	INDEX a_traitements_FI_3 (a_motif_id),
	INDEX a_traitements_FI_4 (a_justification_id),
	INDEX a_traitements_FI_5 (modifie_par_utilisateur_id),
	CONSTRAINT a_traitements_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
	CONSTRAINT a_traitements_FK_2
		FOREIGN KEY (a_type_id)
		REFERENCES a_types (id)
		ON DELETE SET NULL,
	CONSTRAINT a_traitements_FK_3
		FOREIGN KEY (a_motif_id)
		REFERENCES a_motifs (id)
		ON DELETE SET NULL,
	CONSTRAINT a_traitements_FK_4
		FOREIGN KEY (a_justification_id)
		REFERENCES a_justifications (id)
		ON DELETE SET NULL,
	CONSTRAINT a_traitements_FK_5
		FOREIGN KEY (modifie_par_utilisateur_id)
		REFERENCES utilisateurs (login)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies';
DROP TABLE IF EXISTS j_traitements_saisies;
CREATE TABLE IF NOT EXISTS j_traitements_saisies(	a_saisie_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de l\'absence saisie',	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement de ces absences',	PRIMARY KEY (a_saisie_id,a_traitement_id),	CONSTRAINT j_traitements_saisies_FK_1		FOREIGN KEY (a_saisie_id)		REFERENCES a_saisies (id)		ON DELETE CASCADE,	INDEX j_traitements_saisies_FI_2 (a_traitement_id),	CONSTRAINT j_traitements_saisies_FK_2		FOREIGN KEY (a_traitement_id)		REFERENCES a_traitements (id)		ON DELETE CASCADE) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Table de jointure entre la saisie et le traitement des absences';
DROP TABLE IF EXISTS a_notifications;
CREATE TABLE IF NOT EXISTS a_notifications(	id INTEGER(11)  NOT NULL AUTO_INCREMENT,	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui envoi la notification',	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement qu\'on notifie',	type_notification INTEGER(5) COMMENT 'type de notification (0 : email, 1 : courrier, 2 : sms)',	email VARCHAR(100) COMMENT 'email de destination (pour le type email)',	telephone VARCHAR(100) COMMENT 'numero du telephone de destination (pour le type sms)',	adr_id VARCHAR(10) COMMENT 'cle etrangere vers l\'adresse de destination (pour le type courrier)',	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',	statut_envoi INTEGER(5) default 0 COMMENT 'Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)',	date_envoi DATETIME COMMENT 'Date envoi',	erreur_message_envoi TEXT COMMENT 'Message d\'erreur retourné par le service d\'envoi',	created_at DATETIME,	updated_at DATETIME,	PRIMARY KEY (id),	INDEX a_notifications_FI_1 (utilisateur_id),	CONSTRAINT a_notifications_FK_1		FOREIGN KEY (utilisateur_id)		REFERENCES utilisateurs (login)		ON DELETE SET NULL,	INDEX a_notifications_FI_2 (a_traitement_id),	CONSTRAINT a_notifications_FK_2		FOREIGN KEY (a_traitement_id)		REFERENCES a_traitements (id)		ON DELETE CASCADE,	INDEX a_notifications_FI_3 (adr_id),	CONSTRAINT a_notifications_FK_3		FOREIGN KEY (adr_id)		REFERENCES resp_adr (adr_id)		ON DELETE SET NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Notification (a la famille) des absences';
DROP TABLE IF EXISTS j_notifications_resp_pers;
CREATE TABLE IF NOT EXISTS j_notifications_resp_pers(	a_notification_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de la notification',	pers_id VARCHAR(10)  NOT NULL COMMENT 'cle etrangere des personnes',	PRIMARY KEY (a_notification_id,pers_id),	CONSTRAINT j_notifications_resp_pers_FK_1		FOREIGN KEY (a_notification_id)		REFERENCES a_notifications (id)		ON DELETE CASCADE,	INDEX j_notifications_resp_pers_FI_2 (pers_id),	CONSTRAINT j_notifications_resp_pers_FK_2		FOREIGN KEY (pers_id)		REFERENCES resp_pers (pers_id)		ON DELETE CASCADE) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci  COMMENT='Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.';
DROP TABLE IF EXISTS matieres_app_delais;
CREATE TABLE matieres_app_delais (periode int(11) NOT NULL default '0', id_groupe int(11) NOT NULL default '0', date_limite TIMESTAMP NOT NULL, mode VARCHAR(100) NOT NULL DEFAULT '', PRIMARY KEY  (periode,id_groupe), INDEX id_groupe (id_groupe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ct_private_entry;
CREATE TABLE ct_private_entry (id_ct INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la cotice privee',heure_entry TIME default '00:00:00' NOT NULL COMMENT 'heure de l\'entree',date_ct INTEGER default 0 NOT NULL COMMENT 'date du compte rendu',contenu TEXT  NOT NULL COMMENT 'contenu redactionnel du compte rendu',id_groupe INTEGER  NOT NULL COMMENT 'Cle etrangere du groupe auquel appartient le compte rendu',id_login VARCHAR(32)  COMMENT 'Cle etrangere de l\'utilisateur auquel appartient le compte rendu',id_sequence INT ( 11 ) NOT NULL DEFAULT '0',PRIMARY KEY (id_ct),INDEX ct_private_entry_FI_1 (id_groupe),CONSTRAINT ct_private_entry_FK_1 FOREIGN KEY (id_groupe) REFERENCES groupes (id) ON DELETE CASCADE,INDEX ct_private_entry_FI_2 (id_login),CONSTRAINT ct_private_entry_FK_2 FOREIGN KEY (id_login) REFERENCES utilisateurs (login) ON DELETE CASCADE) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Notice privee du cahier de texte';
DROP TABLE IF EXISTS cc_dev;
CREATE TABLE cc_dev (id int(11) NOT NULL auto_increment, id_cn_dev int(11) NOT NULL default '0',id_groupe int(11) NOT NULL default '0',nom_court varchar(32) NOT NULL default '',nom_complet varchar(64) NOT NULL default '',description varchar(128) NOT NULL default '',arrondir char(2) NOT NULL default 's1', vision_famille TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Autorisation de voir pour les familles', PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS cc_eval;
CREATE TABLE cc_eval (id int(11) NOT NULL auto_increment,id_dev int(11) NOT NULL default '0',nom_court varchar(32) NOT NULL default '',nom_complet varchar(64) NOT NULL default '',description varchar(128) NOT NULL default '',date datetime NOT NULL default '0000-00-00 00:00:00',note_sur int(11) default '5', vision_famille DATE NOT NULL COMMENT 'Autorisation de voir pour les familles', PRIMARY KEY  (id),INDEX dev_date (id_dev, date)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS cc_notes_eval;
CREATE TABLE cc_notes_eval ( login varchar(50) NOT NULL default '',id_eval int(11) NOT NULL default '0',note float(10,1) NOT NULL default '0.0',statut char(1) NOT NULL default '',comment text NOT NULL,PRIMARY KEY  (login,id_eval)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_delegation;
CREATE TABLE s_delegation (`id_delegation` INT NOT NULL AUTO_INCREMENT ,`fct_delegation` VARCHAR( 100 ) NOT NULL ,`fct_autorite` VARCHAR( 50 ) NOT NULL ,`nom_autorite` VARCHAR( 50 ) NOT NULL ,PRIMARY KEY ( `id_delegation` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS infos_actions;
CREATE TABLE IF NOT EXISTS infos_actions (id int(11) NOT NULL auto_increment,titre varchar(255) NOT NULL default '',description text NOT NULL,date datetime,PRIMARY KEY (id),INDEX id_titre (id, titre)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS infos_actions_destinataires;
CREATE TABLE IF NOT EXISTS infos_actions_destinataires (id int(11) NOT NULL auto_increment,id_info int(11) NOT NULL,nature enum('statut', 'individu') default 'individu',valeur varchar(255) default '',PRIMARY KEY (id),INDEX id_info (id_info)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS acces_cdt;
CREATE TABLE IF NOT EXISTS acces_cdt (id INT(11) NOT NULL auto_increment,description TEXT NOT NULL,chemin VARCHAR(255) NOT NULL DEFAULT '',date1 DATETIME NOT NULL default '0000-00-00 00:00:00',date2 DATETIME NOT NULL default '0000-00-00 00:00:00',PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS acces_cdt_groupes;
CREATE TABLE IF NOT EXISTS acces_cdt_groupes (id INT(11) NOT NULL auto_increment,id_acces INT(11) NOT NULL,id_groupe INT(11) NOT NULL,PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS vocabulaire;
CREATE TABLE IF NOT EXISTS vocabulaire (id INT(11) NOT NULL auto_increment,terme VARCHAR(255) NOT NULL DEFAULT '',terme_corrige VARCHAR(255) NOT NULL DEFAULT '',PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS j_groupes_visibilite;
CREATE TABLE IF NOT EXISTS j_groupes_visibilite (id INT(11) NOT NULL auto_increment,id_groupe INT(11) NOT NULL,domaine varchar(255) NOT NULL default '',visible varchar(255) NOT NULL default '',PRIMARY KEY (id),INDEX id_groupe_domaine (id_groupe, domaine)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS ct_sequences;
CREATE TABLE IF NOT EXISTS ct_sequences (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,titre VARCHAR( 255 ) NOT NULL , description TEXT NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_travail_mesure;
CREATE TABLE IF NOT EXISTS s_travail_mesure (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_incident INT( 11 ) NOT NULL ,login_ele VARCHAR( 50 ) NOT NULL , travail TEXT NOT NULL , materiel VARCHAR( 150 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS s_natures;
CREATE TABLE IF NOT EXISTS s_natures ( id INT(11) NOT NULL auto_increment, nature varchar(50) NOT NULL default '', id_categorie int(11) not null default '0', PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS udt_lignes;
CREATE TABLE IF NOT EXISTS udt_lignes (id INT(11) unsigned NOT NULL auto_increment,division varchar(255) NOT NULL default '',matiere varchar(255) NOT NULL default '',prof varchar(255) NOT NULL default '',groupe varchar(255) NOT NULL default '',regroup varchar(255) NOT NULL default '',mo varchar(255) NOT NULL default '', PRIMARY KEY id (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
DROP TABLE IF EXISTS udt_corresp;
CREATE TABLE IF NOT EXISTS udt_corresp (champ varchar(255) NOT NULL default '',nom_udt varchar(255) NOT NULL default '',nom_gepi varchar(255) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

-- ---------------------------------------------------------------------
-- a_saisies_version
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_saisies_version;

CREATE TABLE a_saisies_version
(
	id INTEGER(11) NOT NULL,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(11) COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à null si aucun eleve n\'est saisi',
	commentaire TEXT COMMENT 'commentaire de l\'utilisateur',
	debut_abs DATETIME COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs DATETIME COMMENT 'Fin de l\'absence en timestamp UNIX',
	id_edt_creneau INTEGER(12) COMMENT 'identifiant du creneaux de l\'emploi du temps',
	id_edt_emplacement_cours INTEGER(12) COMMENT 'identifiant du cours de l\'emploi du temps',
	id_groupe INTEGER COMMENT 'identifiant du groupe pour lequel la saisie a ete effectuee',
	id_classe INTEGER COMMENT 'identifiant de la classe pour lequel la saisie a ete effectuee',
	id_aid INTEGER COMMENT 'identifiant de l\'aid pour lequel la saisie a ete effectuee',
	id_s_incidents INTEGER COMMENT 'identifiant de la saisie d\'incident discipline',
	id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve',
	deleted_by VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a supprimé la saisie',
	created_at DATETIME COMMENT 'Date de creation de la saisie',
	updated_at DATETIME COMMENT 'Date de modification de la saisie, y compris suppression, restauration et changement de version',
	deleted_at DATETIME,
	version INTEGER DEFAULT 0,
	version_created_at DATETIME,
	version_created_by VARCHAR(100),
	PRIMARY KEY (id,version),
	CONSTRAINT a_saisies_version_FK_1
		FOREIGN KEY (id)
		REFERENCES a_saisies (id)
		ON DELETE CASCADE
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;


-- ---------------------------------------------------------------------
-- a_agregation_decompte
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS a_agregation_decompte;

CREATE TABLE a_agregation_decompte
(
	eleve_id INTEGER(11) NOT NULL COMMENT 'id de l\'eleve',
	date_demi_jounee DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT 'Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi',
	manquement_obligation_presence TINYINT DEFAULT 0 COMMENT 'Cette demi journée est comptée comme absence',
	non_justifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une justification',
	notifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une notification à la famille',
	retards INTEGER DEFAULT 0 COMMENT 'Nombre de retards total décomptés dans la demi journée',
	retards_non_justifies INTEGER DEFAULT 0 COMMENT 'Nombre de retards non justifiés décomptés dans la demi journée',
	motifs_absences TEXT COMMENT 'Liste des motifs (table a_motifs) associés à cette demi-journée d\'absence',
	motifs_retards TEXT COMMENT 'Liste des motifs (table a_motifs) associés aux retard de cette demi-journée',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (eleve_id,date_demi_jounee),
	CONSTRAINT a_agregation_decompte_FK_1
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Table d\'agregation des decomptes de demi journees d\'absence et de retard';

DROP TABLE IF EXISTS modeles_grilles_pdf;
CREATE TABLE IF NOT EXISTS modeles_grilles_pdf (
id_modele INT(11) NOT NULL auto_increment,
login varchar(50) NOT NULL default '',
nom_modele varchar(255) NOT NULL,
par_defaut ENUM('y','n') DEFAULT 'n',
PRIMARY KEY (id_modele)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS modeles_grilles_pdf_valeurs;
CREATE TABLE IF NOT EXISTS modeles_grilles_pdf_valeurs (
id_modele INT(11) NOT NULL,
nom varchar(255) NOT NULL default '',
valeur varchar(255) NOT NULL,
INDEX id_modele_champ (id_modele, nom)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS a_lieux;
CREATE TABLE IF NOT EXISTS a_lieux (
id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
nom VARCHAR(250)  NOT NULL COMMENT 'Nom du lieu',
commentaire TEXT   COMMENT 'commentaire saisi par l\'utilisateur',
sortable_rank INTEGER,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Lieu pour les types d\'absence ou les saisies';

DROP TABLE IF EXISTS tempo3;
CREATE TABLE IF NOT EXISTS tempo3 (
id int(11) NOT NULL auto_increment,
col1 VARCHAR(255) NOT NULL,
col2 TEXT,
PRIMARY KEY  (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS tempo3_cdt;
CREATE TABLE IF NOT EXISTS tempo3_cdt (id_classe int(11) NOT NULL default '0', classe varchar(255) NOT NULL default '', matiere varchar(255) NOT NULL default '', enseignement varchar(255) NOT NULL default '', id_groupe int(11) NOT NULL default '0', fichier varchar(255) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS mentions;
CREATE TABLE IF NOT EXISTS mentions (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
mention VARCHAR(255) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS j_mentions_classes;
CREATE TABLE IF NOT EXISTS j_mentions_classes (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_mention INT(11) NOT NULL ,
id_classe INT(11) NOT NULL ,
ordre TINYINT(4) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS temp_gep_import2;
CREATE TABLE IF NOT EXISTS temp_gep_import2 (
ID_TEMPO varchar(40) NOT NULL default '',
LOGIN varchar(40) NOT NULL default '',
ELENOM varchar(40) NOT NULL default '',
ELEPRE varchar(40) NOT NULL default '',
ELESEXE varchar(40) NOT NULL default '',
ELEDATNAIS varchar(40) NOT NULL default '',
ELENOET varchar(40) NOT NULL default '',
ELE_ID varchar(40) NOT NULL default '',
ELEDOUBL varchar(40) NOT NULL default '',
ELENONAT varchar(40) NOT NULL default '',
ELEREG varchar(40) NOT NULL default '',
DIVCOD varchar(40) NOT NULL default '',
ETOCOD_EP varchar(40) NOT NULL default '',
ELEOPT1 varchar(40) NOT NULL default '',
ELEOPT2 varchar(40) NOT NULL default '',
ELEOPT3 varchar(40) NOT NULL default '',
ELEOPT4 varchar(40) NOT NULL default '',
ELEOPT5 varchar(40) NOT NULL default '',
ELEOPT6 varchar(40) NOT NULL default '',
ELEOPT7 varchar(40) NOT NULL default '',
ELEOPT8 varchar(40) NOT NULL default '',
ELEOPT9 varchar(40) NOT NULL default '',
ELEOPT10 varchar(40) NOT NULL default '',
ELEOPT11 varchar(40) NOT NULL default '',
ELEOPT12 varchar(40) NOT NULL default '',
LIEU_NAISSANCE varchar(50) NOT NULL default '',
MEL varchar(255) NOT NULL default '',
TEL_PERS varchar(255) NOT NULL default '',
TEL_PORT varchar(255) NOT NULL default '',
TEL_PROF varchar(255) NOT NULL default ''
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS temp_abs_import;
CREATE TABLE IF NOT EXISTS temp_abs_import (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login varchar(50) NOT NULL default '',
cpe_login varchar(50) NOT NULL default '',
elenoet varchar(50) NOT NULL default '',
libelle varchar(50) NOT NULL default '',
nbAbs INT(11) NOT NULL default '0',
nbNonJustif INT(11) NOT NULL default '0',
nbRet INT(11) NOT NULL default '0',
UNIQUE KEY elenoet (elenoet)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS tempo_utilisateurs;
CREATE TABLE IF NOT EXISTS tempo_utilisateurs
(login VARCHAR( 50 ) NOT NULL PRIMARY KEY,
password VARCHAR(128) NOT NULL,
salt VARCHAR(128) NOT NULL,
email VARCHAR(50) NOT NULL,
identifiant1 VARCHAR( 10 ) NOT NULL COMMENT 'eleves.ele_id ou resp_pers.pers_id',
identifiant2 VARCHAR( 50 ) NOT NULL COMMENT 'eleves.elenoet',
nom VARCHAR( 50 ) NOT NULL ,
prenom VARCHAR( 50 ) NOT NULL ,
statut VARCHAR( 20 ) NOT NULL ,
auth_mode ENUM('gepi','ldap','sso') NOT NULL default 'gepi',
date_reserve DATE DEFAULT '0000-00-00',
temoin VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

DROP TABLE IF EXISTS t_plan_de_classe;
CREATE TABLE IF NOT EXISTS t_plan_de_classe (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_groupe INT(11) NOT NULL ,
login_prof VARCHAR(50) NOT NULL ,
dim_photo INT(11) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS t_plan_de_classe_ele;
CREATE TABLE IF NOT EXISTS t_plan_de_classe_ele (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_plan INT( 11 ) NOT NULL,
login_ele VARCHAR(50) NOT NULL ,
x INT(11) NOT NULL ,
y INT(11) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS rss_users;
CREATE TABLE rss_users (id int(11) NOT NULL auto_increment, user_login varchar(30) NOT NULL, user_uri varchar(30) NOT NULL, PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS ldap_bx;
CREATE TABLE ldap_bx (
id INT( 11 ) NOT NULL AUTO_INCREMENT ,
login_u VARCHAR( 200 ) NOT NULL ,
nom_u VARCHAR( 200 ) NOT NULL ,
prenom_u VARCHAR( 200 ) NOT NULL ,
statut_u VARCHAR( 50 ) NOT NULL ,
identite_u VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS droits_acces_fichiers;
CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
id INT(11) unsigned NOT NULL auto_increment,
fichier VARCHAR( 255 ) NOT NULL ,
identite VARCHAR( 255 ) NOT NULL ,
type VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS log_maj_sconet;
CREATE TABLE log_maj_sconet (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login VARCHAR( 50 ) NOT NULL ,
texte LONGTEXT NOT NULL ,
date_debut DATETIME NOT NULL ,
date_fin DATETIME NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS classes_param;
CREATE TABLE classes_param (
id int(11) NOT NULL AUTO_INCREMENT,
id_classe smallint(6) NOT NULL,
name varchar(100) NOT NULL,
value varchar(255) NOT NULL,
PRIMARY KEY (id),
UNIQUE KEY id_classe_name (id_classe,name)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS messagerie;
CREATE TABLE IF NOT EXISTS messagerie (
id int(11) NOT NULL AUTO_INCREMENT,
in_reply_to int(11) NOT NULL,
login_src varchar(50) NOT NULL,
login_dest varchar(50) NOT NULL,
sujet varchar(100) NOT NULL,
message text NOT NULL,
date_msg timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
date_visibilite timestamp NOT NULL,
vu tinyint(4) NOT NULL,
date_vu timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS acces_cn;
CREATE TABLE acces_cn (
id INT( 11 ) NOT NULL AUTO_INCREMENT ,
id_groupe INT( 11 ) NOT NULL ,
periode INT( 11 ) NOT NULL ,
date_limite timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
commentaires text NOT NULL,
PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Acces exceptionnel au CN en periode close';

DROP TABLE IF EXISTS acces_exceptionnel_matieres_notes;
CREATE TABLE acces_exceptionnel_matieres_notes (
id INT( 11 ) NOT NULL AUTO_INCREMENT ,
id_groupe INT( 11 ) NOT NULL ,
periode INT( 11 ) NOT NULL ,
date_limite timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
commentaires text NOT NULL,
PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Acces exceptionnel à la modif de notes du bulletin en periode close';

DROP TABLE IF EXISTS ct_devoirs_faits;
CREATE TABLE ct_devoirs_faits (
id INT(11) unsigned NOT NULL auto_increment,
id_ct INT(11) unsigned NOT NULL,
login VARCHAR( 255 ) NOT NULL ,
etat VARCHAR( 50 ) NOT NULL,
date_initiale DATETIME,
date_modif DATETIME,
commentaire VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS cn_conteneurs_modele;
CREATE TABLE cn_conteneurs_modele (
id_modele int(11) NOT NULL auto_increment, 
nom_court varchar(32) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
PRIMARY KEY  (id_modele)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS cn_conteneurs_modele_conteneurs;
CREATE TABLE cn_conteneurs_modele_conteneurs (
id int(11) NOT NULL auto_increment, 
id_modele int(11) NOT NULL default '0', 
id_racine int(11) NOT NULL default '0', 
nom_court varchar(32) NOT NULL default '', 
nom_complet varchar(64) NOT NULL default '', 
description varchar(128) NOT NULL default '', 
mode char(1) NOT NULL default '2', 
coef decimal(3,1) NOT NULL default '1.0', 
arrondir char(2) NOT NULL default 's1', 
ponderation decimal(3,1) NOT NULL default '0.0', 
display_parents char(1) NOT NULL default '0', 
display_bulletin char(1) NOT NULL default '1', 
parent int(11) NOT NULL default '0', 
PRIMARY KEY  (id), 
INDEX parent_racine (parent,id_racine), 
INDEX racine_bulletin (id_racine,display_bulletin)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS d_dates_evenements;
CREATE TABLE d_dates_evenements (
id_ev int(11) NOT NULL AUTO_INCREMENT, 
type varchar(50) NOT NULL default '', 
periode INT(11) NOT NULL default '0', 
texte_avant TEXT NOT NULL default '', 
texte_apres TEXT NOT NULL default '', 
texte_apres_ele_resp TEXT NOT NULL default '', 
date_debut TIMESTAMP NOT NULL, 
PRIMARY KEY  (id_ev)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS d_dates_evenements_classes;
CREATE TABLE d_dates_evenements_classes (
id_ev_classe int(11) NOT NULL AUTO_INCREMENT, 
id_ev int(11) NOT NULL, 
id_classe int(11) NOT NULL default '0', 
date_evenement TIMESTAMP NOT NULL, 
id_salle INT(3) NOT NULL default '0', 
PRIMARY KEY  (id_ev_classe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS d_dates_evenements_utilisateurs;
CREATE TABLE d_dates_evenements_utilisateurs (
id_ev int(11) NOT NULL, 
statut varchar(20) NOT NULL, 
KEY id_ev_u (id_ev,statut)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS s_avertissements;
CREATE TABLE IF NOT EXISTS s_avertissements (
id_avertissement INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
login_ele VARCHAR( 50 ) NOT NULL DEFAULT '',
id_type_avertissement INT(11) NOT NULL DEFAULT '0',
periode INT(11) NOT NULL DEFAULT '0',
s_periode CHAR(1) NOT NULL DEFAULT 'n',
date_avertissement DATETIME NOT NULL ,
declarant VARCHAR( 50 ) NOT NULL DEFAULT '',
commentaire TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS s_types_avertissements;
CREATE TABLE IF NOT EXISTS s_types_avertissements (
id_type_avertissement INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
nom_court VARCHAR( 50 ) NOT NULL ,
nom_complet VARCHAR( 255 ) NOT NULL,
description TEXT NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS notanet_saisie;
CREATE TABLE IF NOT EXISTS notanet_saisie (login VARCHAR( 50 ) NOT NULL, id_mat INT(4), matiere VARCHAR(50), note VARCHAR(4), PRIMARY KEY login_id_mat ( login , id_mat )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS grp_groupes;
CREATE TABLE IF NOT EXISTS grp_groupes (
id int(11) NOT NULL AUTO_INCREMENT,
nom_court varchar(20) NOT NULL,
nom_complet varchar(100) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS grp_groupes_admin;
CREATE TABLE IF NOT EXISTS grp_groupes_admin (
id int(11) NOT NULL AUTO_INCREMENT,
id_grp_groupe int(11) NOT NULL,
login varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS grp_groupes_groupes;
CREATE TABLE IF NOT EXISTS grp_groupes_groupes (
id int(11) NOT NULL AUTO_INCREMENT,
id_grp_groupe int(11) NOT NULL,
id_groupe int(11) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS groupes_param;
CREATE TABLE IF NOT EXISTS groupes_param (
id int(11) NOT NULL AUTO_INCREMENT,
id_groupe int(11) NOT NULL,
name varchar(100) NOT NULL,
value varchar(100) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS nomenclatures;
CREATE TABLE IF NOT EXISTS nomenclatures (
id INT(11) unsigned NOT NULL auto_increment,
type VARCHAR( 255 ) NOT NULL,
code VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS nomenclatures_valeurs;
CREATE TABLE IF NOT EXISTS nomenclatures_valeurs (
id INT(11) unsigned NOT NULL auto_increment,
type VARCHAR( 255 ) NOT NULL,
code VARCHAR( 255 ) NOT NULL,
nom VARCHAR( 255 ) NOT NULL,
valeur VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS gc_noms_affichages;
CREATE TABLE IF NOT EXISTS gc_noms_affichages (
id int(11) unsigned NOT NULL auto_increment,
id_aff int(11) NOT NULL,
nom varchar(100) NOT NULL,
description tinytext NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_ics;
CREATE TABLE IF NOT EXISTS edt_ics (
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
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_ics_prof;
CREATE TABLE IF NOT EXISTS edt_ics_prof (
id int(11) NOT NULL AUTO_INCREMENT,
login_prof varchar(100) NOT NULL DEFAULT '',
prof_ics varchar(200) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_ics_matiere;
CREATE TABLE IF NOT EXISTS edt_ics_matiere (
id int(11) NOT NULL AUTO_INCREMENT,
matiere varchar(100) NOT NULL DEFAULT '',
matiere_ics varchar(100) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS abs_prof;
CREATE TABLE IF NOT EXISTS abs_prof (
id int(11) NOT NULL AUTO_INCREMENT,
login_user varchar(50) NOT NULL,
date_debut datetime NOT NULL,
date_fin datetime NOT NULL,
titre varchar(100) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS abs_prof_remplacement;
CREATE TABLE IF NOT EXISTS abs_prof_remplacement (
id int(11) NOT NULL AUTO_INCREMENT,
id_absence INT(11) NOT NULL,
id_groupe INT(11) NOT NULL,
id_aid INT(11) NOT NULL,
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
texte_famille TEXT NOT NULL,
info_famille VARCHAR( 10 ) NOT NULL,
duree varchar(10) NOT NULL default '0',
heuredeb_dec varchar(3) NOT NULL default '0',
jour_semaine varchar(10) NOT NULL,
id_cours_remplaced INT(11) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS abs_prof_divers;
CREATE TABLE IF NOT EXISTS abs_prof_divers (
id INT(11) unsigned NOT NULL auto_increment,
name VARCHAR( 255 ) NOT NULL ,
value VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS mod_alerte_divers;
CREATE TABLE IF NOT EXISTS mod_alerte_divers (
id INT(11) unsigned NOT NULL auto_increment,
name VARCHAR( 255 ) NOT NULL ,
value VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS engagements;
CREATE TABLE IF NOT EXISTS engagements (
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
SaisiePP VARCHAR(10) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS engagements_user;
CREATE TABLE IF NOT EXISTS engagements_user (
id int(11) NOT NULL AUTO_INCREMENT,
id_engagement int(11) NOT NULL,
login VARCHAR(50) NOT NULL,
id_type VARCHAR(20) NOT NULL,
valeur INT(11) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS archivage_engagements;
CREATE TABLE IF NOT EXISTS archivage_engagements (
id int(11) NOT NULL AUTO_INCREMENT,
annee VARCHAR(100) NOT NULL,
ine VARCHAR(255) NOT NULL,
code_engagement VARCHAR(10) NOT NULL,
nom_engagement VARCHAR(100) NOT NULL,
description_engagement TEXT NOT NULL,
classe VARCHAR(100) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_corresp;
CREATE TABLE IF NOT EXISTS edt_corresp (
id int(11) NOT NULL AUTO_INCREMENT,
champ VARCHAR(100) NOT NULL DEFAULT '',
nom_edt VARCHAR(255) NOT NULL DEFAULT '',
nom_gepi VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_lignes;
CREATE TABLE IF NOT EXISTS edt_lignes (
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
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_corresp2;
CREATE TABLE IF NOT EXISTS edt_corresp2 (
id int(11) NOT NULL AUTO_INCREMENT,
id_groupe int(11) NOT NULL,
mat_code_edt VARCHAR(255) NOT NULL DEFAULT '',
nom_groupe_edt VARCHAR(255) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_eleves_lignes;
CREATE TABLE IF NOT EXISTS edt_eleves_lignes (
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
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS edt_tempo;
CREATE TABLE IF NOT EXISTS edt_tempo (
id int(11) NOT NULL AUTO_INCREMENT,
col1 varchar(255) NOT NULL default '',
col2 varchar(255) NOT NULL default '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS sp_saisies;
CREATE TABLE IF NOT EXISTS sp_saisies (
id int(11) NOT NULL AUTO_INCREMENT,
id_type int(11) NOT NULL,
login VARCHAR(50) NOT NULL default '',
date_sp datetime NOT NULL default '0000-00-00 00:00:00',
commentaire text NOT NULL,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_by VARCHAR(50) NOT NULL DEFAULT '',
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS sp_types_saisies;
CREATE TABLE IF NOT EXISTS sp_types_saisies (
id_type int(11) NOT NULL AUTO_INCREMENT,
nom VARCHAR(255) NOT NULL default '',
description TEXT NOT NULL,
rang int(11) NOT NULL,
PRIMARY KEY (id_type)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS sp_seuils;
CREATE TABLE IF NOT EXISTS sp_seuils (
id_seuil int(11) NOT NULL AUTO_INCREMENT,
seuil int(11) NOT NULL,
periode CHAR(1) NOT NULL default 'y',
type VARCHAR(255) NOT NULL default '',
administrateur CHAR(1) NOT NULL default '',
scolarite CHAR(1) NOT NULL default '',
cpe CHAR(1) NOT NULL default '',
eleve CHAR(1) NOT NULL default '',
responsable CHAR(1) NOT NULL default '',
professeur_principal CHAR(1) NOT NULL default '',
PRIMARY KEY (id_seuil)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS signature_droits;
CREATE TABLE IF NOT EXISTS signature_droits (
id INT(11) unsigned NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS signature_fichiers;
CREATE TABLE IF NOT EXISTS signature_fichiers (
id_fichier INT(11) unsigned NOT NULL auto_increment,
fichier VARCHAR( 255 ) NOT NULL ,
login VARCHAR( 255 ) NOT NULL ,
type VARCHAR( 255 ) NOT NULL,
PRIMARY KEY ( id_fichier )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS signature_classes;
CREATE TABLE IF NOT EXISTS signature_classes (
id INT(11) unsigned NOT NULL auto_increment,
login VARCHAR( 255 ) NOT NULL ,
id_classe INT( 11 ) NOT NULL ,
id_fichier INT( 11 ) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS aid_sous_groupes;
CREATE TABLE IF NOT EXISTS aid_sous_groupes  (
id INT(11) unsigned NOT NULL auto_increment,
aid varchar(100) NOT NULL ,
parent varchar(100) NOT NULL ,
PRIMARY KEY ( id ), 
UNIQUE KEY `aid` (`aid`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS s_sanctions_check;
CREATE TABLE IF NOT EXISTS s_sanctions_check (
id INT(11) NOT NULL auto_increment,
id_sanction INT(11) NOT NULL,
etat varchar(100) NOT NULL ,
login varchar(50) NOT NULL ,
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `mod_listes_perso_definition`;
CREATE TABLE IF NOT EXISTS `mod_listes_perso_definition` (
`id` int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
`nom` varchar(50) NOT NULL default '' COMMENT 'Nom de la liste',
`sexe` BOOLEAN default true COMMENT 'Affichage ou non du sexe des élèves ',
`classe` BOOLEAN default true COMMENT 'Affichage ou non de la classe des élèves',
`photo` BOOLEAN default true COMMENT 'Affichage ou non de la photo',
`proprietaire` VARCHAR( 50 ) NOT NULL COMMENT 'Nom du créateur de la liste',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste personnelle : création';

DROP TABLE IF EXISTS `mod_listes_perso_colonnes`;
CREATE TABLE IF NOT EXISTS `mod_listes_perso_colonnes` (
`id` int(11) NOT NULL auto_increment COMMENT 'identifiant unique', 
`id_def` int(11) NOT NULL COMMENT 'identifiant de la liste',
`titre` varchar(30) NOT NULL default '' COMMENT 'Titre de la colonne',
`placement` int(11) NOT NULL COMMENT 'Place de la colonne dans le tableau',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste personnelle : Définition des colonnes' ;

DROP TABLE IF EXISTS `mod_listes_perso_eleves`;
CREATE TABLE IF NOT EXISTS `mod_listes_perso_eleves` (
`id` int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
`id_def` int(11) NOT NULL COMMENT 'identifiant de la liste',
`login` varchar(50) NOT NULL default '' COMMENT 'identifiant des élèves',
PRIMARY KEY  (`id`),
INDEX combinaison (`id_def`, `login`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste personnelle : élèves de la liste' ;

DROP TABLE IF EXISTS `mod_listes_perso_contenus`;
CREATE TABLE IF NOT EXISTS `mod_listes_perso_contenus` (
`id` int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
`id_def` int(11) NOT NULL COMMENT 'identifiant de la liste',
`login` varchar(50) NOT NULL default '' COMMENT 'identifiant des élèves',
`colonne` int(11) NOT NULL COMMENT 'identifiant de la colonne',
`contenu` varchar(50) NOT NULL default '' COMMENT 'contenu de la cellule',
PRIMARY KEY (`id`),
INDEX contenu (`id_def`, `login`, `contenu`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Liste personnelle : contenu du tableau' ;

DROP TABLE IF EXISTS modele_bulletin;
CREATE TABLE IF NOT EXISTS modele_bulletin (
id_model_bulletin INT( 11 ) NOT NULL ,
nom VARCHAR( 255 ) NOT NULL ,
valeur VARCHAR( 255 ) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS sso_table_correspondance;
CREATE TABLE IF NOT EXISTS sso_table_correspondance ( `login_gepi` varchar(100) NOT NULL default '', 
`login_sso` varchar(100) NOT NULL default '', 
PRIMARY KEY (`login_gepi`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS tempo4;
CREATE TABLE IF NOT EXISTS tempo4 ( col1 varchar(100) NOT NULL default '', col2 varchar(100) NOT NULL default '', col3 varchar(255) NOT NULL default '', col4 varchar(100) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS engagements_pages;
CREATE TABLE IF NOT EXISTS engagements_pages (
id int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
page varchar(255) NOT NULL default '' COMMENT 'Page ou module',
id_type int(11) NOT NULL COMMENT 'identifiant du type d engagement',
PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS calendrier_vacances;
CREATE TABLE IF NOT EXISTS calendrier_vacances (
id int(11) NOT NULL auto_increment,
nom_calendrier varchar(100) NOT NULL default '',
debut_calendrier_ts varchar(11) NOT NULL,
fin_calendrier_ts varchar(11) NOT NULL,
jourdebut_calendrier date NOT NULL default '0000-00-00',
heuredebut_calendrier time NOT NULL default '00:00:00',
jourfin_calendrier date NOT NULL default '0000-00-00',
heurefin_calendrier time NOT NULL default '00:00:00',
PRIMARY KEY (id)) 
ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_orientations;
CREATE TABLE IF NOT EXISTS o_orientations (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
commentaire text NOT NULL,
date_orientation datetime NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login_rang (login,rang)) 
ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_orientations_base;
CREATE TABLE IF NOT EXISTS o_orientations_base (
id int(11) NOT NULL AUTO_INCREMENT,
titre varchar(255) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_orientations_mefs;
CREATE TABLE IF NOT EXISTS o_orientations_mefs (
id int(11) NOT NULL AUTO_INCREMENT,
id_orientation int(11) NOT NULL,
mef_code varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_voeux;
CREATE TABLE IF NOT EXISTS o_voeux (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
date_voeu datetime NOT NULL,
commentaire varchar(255) NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login_rang (login,rang)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_mef;
CREATE TABLE IF NOT EXISTS o_mef (
id int(11) NOT NULL AUTO_INCREMENT,
mef_code varchar(50) NOT NULL,
affichage char(1) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY mef_code_affichage (mef_code,affichage)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS o_avis;
CREATE TABLE IF NOT EXISTS o_avis (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
avis varchar(255) NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login (login)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS matieres_appreciations_acces_eleve;
CREATE TABLE IF NOT EXISTS matieres_appreciations_acces_eleve (login VARCHAR( 50 ) NOT NULL , periode INT( 11 ) NOT NULL, acces ENUM( 'y', 'n') NOT NULL ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS mef_matieres;
CREATE TABLE IF NOT EXISTS mef_matieres (mef_code varchar(50) NOT NULL, code_matiere VARCHAR( 250 ) NOT NULL, code_modalite_elect VARCHAR(6) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS nomenclature_modalites_election;
CREATE TABLE IF NOT EXISTS nomenclature_modalites_election (code_modalite_elect VARCHAR( 6 ) NOT NULL, libelle_court VARCHAR(50) NOT NULL, libelle_long VARCHAR(250) NOT NULL, PRIMARY KEY ( code_modalite_elect )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS j_groupes_eleves_modalites;
CREATE TABLE IF NOT EXISTS j_groupes_eleves_modalites (id_groupe int(11) NOT NULL, login VARCHAR( 50 ) NOT NULL, code_modalite_elect VARCHAR(6) NOT NULL, UNIQUE KEY id_groupe_login_modalite (id_groupe,login,code_modalite_elect)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS sconet_ele_options;
CREATE TABLE IF NOT EXISTS sconet_ele_options (id int(11) unsigned NOT NULL auto_increment, ele_id varchar(10) NOT NULL default '', code_matiere varchar(255) NOT NULL default '', code_modalite_elect char(1) NOT NULL default '', num_option int(2) NOT NULL default '0', PRIMARY KEY id (id));

DROP TABLE IF EXISTS ct_tag_type;
CREATE TABLE IF NOT EXISTS ct_tag_type (
id int(11) unsigned NOT NULL auto_increment, 
nom_tag varchar(255) NOT NULL default '',
tag_compte_rendu char(1) NOT NULL default 'y',
tag_devoir char(1) NOT NULL default 'y',
tag_notice_privee char(1) NOT NULL default 'y',
drapeau varchar(255) NOT NULL default '',
PRIMARY KEY id (id));

DROP TABLE IF EXISTS ct_tag;
CREATE TABLE IF NOT EXISTS ct_tag (
id int(11) unsigned NOT NULL auto_increment, 
id_ct int(11) unsigned NOT NULL, 
type_ct char(1) NOT NULL DEFAULT '', 
id_tag int(11) unsigned NOT NULL, 
PRIMARY KEY id (id), UNIQUE KEY idct_idtag (id_ct, type_ct, id_tag));

DROP TABLE IF EXISTS gc_eleves_profils;
CREATE TABLE IF NOT EXISTS gc_eleves_profils (id int(11) unsigned NOT NULL auto_increment, login VARCHAR( 50 ) NOT NULL , profil VARCHAR(10) NOT NULL default 'RAS', PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

/*===== éléments de programme =====*/

DROP TABLE IF EXISTS  matiere_element_programme;
CREATE TABLE IF NOT EXISTS matiere_element_programme ( 
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    libelle varchar(255) NOT NULL default '' COMMENT "Libellé de l'élément de programme", 
    id_user VARCHAR(50) NOT NULL default '' COMMENT "Auteur/proprio de l'élément de programme", 
    PRIMARY KEY id (id), 
    UNIQUE KEY libelle (libelle)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Éléments de programme travaillé' ;

DROP TABLE IF EXISTS j_mep_mat;
CREATE TABLE IF NOT EXISTS j_mep_mat( 
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    idMat varchar(50) COMMENT 'identifiant unique de la matière', 
    idEP int(11) COMMENT "identifiant unique de l'élément de programme", 
    PRIMARY KEY id (id) , 
    UNIQUE KEY jointMapMat (idMat, idEP)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Jointure éléments de programme travaillé ↔ matière' ;

DROP TABLE IF EXISTS j_mep_prof;
CREATE TABLE IF NOT EXISTS j_mep_prof( 
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    idEP int(11) COMMENT "identifiant unique de l'élément de programme", 
    id_prof varchar(50) COMMENT 'identifiant unique du professeur', 
    PRIMARY KEY id (id) , 
    UNIQUE KEY jointMapProf (id_prof, idEP)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Jointure éléments de programme travaillé ↔ enseignant' ;

DROP TABLE IF EXISTS j_mep_groupe;
CREATE TABLE IF NOT EXISTS j_mep_groupe( 
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    idEP int(11) COMMENT "identifiant unique de l'élément de programme", 
    idGroupe int(11) COMMENT 'identifiant du groupe', 
    annee varchar(4) COMMENT 'année sur 4 caractères', 
    periode int(11) COMMENT 'période sur 4 caractères',
    PRIMARY KEY id (id) , 
    UNIQUE KEY jointGroupe (idEP,idGroupe,annee, periode)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Jointure éléments de programme travaillé ↔ groupe enseignement' ;

DROP TABLE IF EXISTS j_mep_eleve;
CREATE TABLE IF NOT EXISTS j_mep_eleve( 
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    idEP int(11) COMMENT "identifiant unique de l'élément de programme", 
    idEleve varchar(50) COMMENT 'login élève', 
    idGroupe int(11)  COMMENT 'identifiant du groupe', 
    annee varchar(4) COMMENT 'année sur 4 caractères', 
    periode int(11) COMMENT 'période sur 4 caractères', 
    date_insert DATETIME NOT NULL default '0000-00-00 00:00:00', 
    PRIMARY KEY id (id) , 
    UNIQUE KEY jointMapProf (idEP , idEleve , annee , periode)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Jointure éléments de programme travaillé ↔ groupe enseignement' ;

DROP TABLE IF EXISTS j_mep_niveau;
CREATE TABLE IF NOT EXISTS j_mep_niveau(
    id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', 
    idEP int(11) COMMENT "identifiant unique de l'élément de programme", 
    idNiveau varchar(50) COMMENT "niveau auquel se réfère l'élément" , 
    PRIMARY KEY id (id) , 
    UNIQUE KEY niveau (idEP , idNiveau)) 
    ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'Jointure éléments de programme travaillé ↔ Niveau' ;

/*===== Types de groupes (AP, EPI, Parcours) =====*/

DROP TABLE IF EXISTS groupes_types;
CREATE TABLE IF NOT EXISTS groupes_types (
id int(11) unsigned NOT NULL auto_increment, 
nom_court varchar(255) NOT NULL default '',
nom_complet varchar(255) NOT NULL default '',
nom_complet_pluriel varchar(255) NOT NULL default '',
PRIMARY KEY id (id));

DROP TABLE IF EXISTS j_groupes_types;
CREATE TABLE IF NOT EXISTS j_groupes_types (
id int(11) unsigned NOT NULL auto_increment, 
id_groupe int(11) NOT NULL,
id_type int(11) NOT NULL,
PRIMARY KEY id (id));

DROP TABLE IF EXISTS temp_abs_extract;
CREATE TABLE IF NOT EXISTS temp_abs_extract (
id int(11) unsigned NOT NULL auto_increment, 
login VARCHAR(50) NOT NULL DEFAULT '', 
date_extract DATETIME NOT NULL default '0000-00-00 00:00:00', 
login_ele VARCHAR(50) NOT NULL DEFAULT '', 
item VARCHAR(100) NOT NULL DEFAULT '', 
valeur VARCHAR(255) NULL DEFAULT '', 
PRIMARY KEY id (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS aid_appreciations_grp;
CREATE TABLE aid_appreciations_grp ( id_aid int(11) NOT NULL default '0', periode int(11) NOT NULL default '0', appreciation text NOT NULL, indice_aid int(11) NOT NULL default '0', PRIMARY KEY  (id_aid, indice_aid, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS absences_appreciations_grp;
CREATE TABLE absences_appreciations_grp (id_classe int(11) NOT NULL default '0', periode int(11) NOT NULL default '0', appreciation text NOT NULL, PRIMARY KEY  (id_classe, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS j_groupes_aid;
CREATE TABLE IF NOT EXISTS j_groupes_aid (id_groupe INT(11) NOT NULL default '0', 
id_aid INT(11) NOT NULL default '0', 
indice_aid INT(11) NOT NULL default '0', 
etat varchar(255) NOT NULL default '', 
PRIMARY KEY  (id_groupe, id_aid), INDEX id_groupe_id_aid (id_groupe, id_aid)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS engagements_droit_saisie;
CREATE TABLE IF NOT EXISTS engagements_droit_saisie (
id INT(11) unsigned NOT NULL auto_increment,
id_engagement INT(11) NOT NULL ,
login VARCHAR( 50 ) NOT NULL DEFAULT '', 
PRIMARY KEY ( id )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS `edt_cours_remplacements`;
CREATE TABLE `edt_cours_remplacements` (`id_cours` int(11) NOT NULL auto_increment, `id_groupe` varchar(10) NOT NULL, `id_aid` varchar(10) NOT NULL, `id_salle` varchar(3) NOT NULL, `jour_semaine` varchar(10) NOT NULL, `id_definie_periode` varchar(3) NOT NULL, `duree` varchar(10) NOT NULL default '2', `heuredeb_dec` varchar(3) NOT NULL default '0', `id_semaine` varchar(10) NOT NULL default '0', `id_calendrier` varchar(3) NOT NULL default '0', `modif_edt` varchar(3) NOT NULL default '0', `login_prof` varchar(50) NOT NULL, id_absence int(11) NOT NULL, jour varchar(10) NOT NULL DEFAULT '', PRIMARY KEY  (`id_cours`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS socle_eleves_composantes;
CREATE TABLE socle_eleves_composantes (id int(11) NOT NULL auto_increment, 
ine varchar(50) NOT NULL, 
cycle tinyint(2) NOT NULL, 
annee varchar(10) NOT NULL default '', 
code_composante varchar(10) NOT NULL DEFAULT '', 
niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
periode INT(11) NOT NULL default '1', 
login_saisie varchar(50) NOT NULL DEFAULT '', 
date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
PRIMARY KEY (id), INDEX ine_cycle_id_composante_periode (ine, cycle, code_composante, periode, annee), UNIQUE(ine, cycle, code_composante, periode, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS socle_eleves_syntheses;
CREATE TABLE socle_eleves_syntheses (id int(11) NOT NULL auto_increment, 
ine varchar(50) NOT NULL, 
cycle tinyint(2) NOT NULL, 
annee varchar(10) NOT NULL default '', 
synthese TEXT, 
login_saisie varchar(50) NOT NULL DEFAULT '', 
date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
PRIMARY KEY (id), INDEX ine_cycle_annee (ine, cycle, annee), UNIQUE(ine, cycle, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS j_groupes_enseignements_complement;
CREATE TABLE IF NOT EXISTS j_groupes_enseignements_complement (
id int(11) unsigned NOT NULL auto_increment, 
id_groupe int(11) NOT NULL,
code VARCHAR(50) NOT NULL,
PRIMARY KEY id (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS socle_eleves_enseignements_complements;
CREATE TABLE socle_eleves_enseignements_complements (id int(11) NOT NULL auto_increment, 
ine varchar(50) NOT NULL, 
id_groupe INT(11) NOT NULL, 
positionnement varchar(10) NOT NULL DEFAULT '', 
login_saisie varchar(50) NOT NULL DEFAULT '', 
date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
PRIMARY KEY (id), INDEX ine_id_groupe (ine, id_groupe), UNIQUE(ine, id_groupe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS modalites_accompagnement;
CREATE TABLE IF NOT EXISTS modalites_accompagnement (code VARCHAR(10) DEFAULT '', 
libelle varchar(255) NOT NULL default '', 
avec_commentaire char(1) NOT NULL default 'n', 
PRIMARY KEY (code)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

DROP TABLE IF EXISTS j_modalite_accompagnement_eleve;
CREATE TABLE IF NOT EXISTS j_modalite_accompagnement_eleve (code VARCHAR(10) DEFAULT '', 
id_eleve INT(11) NOT NULL default '0', 
periode INT(11) NOT NULL default '0', 
commentaire TEXT, 
PRIMARY KEY code_id_eleve_periode (code, id_eleve, periode), INDEX code_id_eleve_periode (code, id_eleve, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
