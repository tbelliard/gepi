
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- utilisateurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS utilisateurs;


CREATE TABLE utilisateurs
(
	login VARCHAR(50)  NOT NULL COMMENT 'Login de l\'utilisateur, et clÃ© primaire de la table utilisateur',
	nom VARCHAR(50)  NOT NULL COMMENT 'Nom de l\'utilisateur',
	prenom VARCHAR(50)  NOT NULL COMMENT 'Prenom de l\'utilisateur',
	civilite VARCHAR(5)  NOT NULL COMMENT 'Civilite',
	password CHAR(32)  NOT NULL COMMENT 'Mot de passe',
	email VARCHAR(50)  NOT NULL COMMENT 'Email de l\'utilisateur',
	show_email VARCHAR(50) default 'no' NOT NULL COMMENT 'L\'email de l\'utilisateur est-il public (yes/no)',
	statut VARCHAR(20)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	etat VARCHAR(20)  NOT NULL COMMENT 'Etat de l\'utilisateur (actif/inactif)',
	change_mdp CHAR(1) default 'n' NOT NULL COMMENT 'L\'utilisateur doit-il changer son mot de passe (y/n) (a la premiere connexion par exemple)',
	date_verrouillage DATE default '2006-01-01 00:00:00' NOT NULL COMMENT 'Date de verrouillage de l\'utilisateur',
	password_ticket VARCHAR(255)  NOT NULL COMMENT 'password_ticket de l\'utilisateur',
	ticket_expiration DATE  NOT NULL COMMENT 'ticket_expiration de l\'utilisateur',
	niveau_alerte SMALLINT default 0 NOT NULL COMMENT 'niveau_alerte de l\'utilisateur',
	observation_securite TINYINT default 0 NOT NULL COMMENT 'observation_securite de l\'utilisateur',
	temp_dir VARCHAR(255)  NOT NULL COMMENT 'Repertoire temporaire de l\'utilisateur',
	numind VARCHAR(255)  NOT NULL COMMENT 'numind de l\'utilisateur',
	auth_mode VARCHAR(255) default 'gepi' NOT NULL COMMENT 'auth_mode de l\'utilisateur (gepi/cas/ldap)',
	PRIMARY KEY (login)
) ENGINE=MyISAM COMMENT='Utilisateur de gepi';

#-----------------------------------------------------------------------------
#-- groupes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS groupes;


CREATE TABLE groupes
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Clee primaire du groupe',
	name VARCHAR(60)  NOT NULL COMMENT 'Nom du groupe',
	description TEXT  NOT NULL COMMENT 'Description du groupe',
	recalcul_rang VARCHAR(10) COMMENT 'recalcul_rang',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Groupe d\'eleves permettant d\'y affecter une matiere et un professeurs';

#-----------------------------------------------------------------------------
#-- j_groupes_professeurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_groupes_professeurs;


CREATE TABLE j_groupes_professeurs
(
	id_groupe INTEGER  NOT NULL COMMENT 'Cle primaire du groupe',
	login VARCHAR(50)  NOT NULL COMMENT 'Cle primaire de l\'utilisateur',
	PRIMARY KEY (id_groupe,login),
	CONSTRAINT j_groupes_professeurs_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX j_groupes_professeurs_FI_2 (login),
	CONSTRAINT j_groupes_professeurs_FK_2
		FOREIGN KEY (login)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table permettant le jointure entre groupe d\'eleves et professeurs. Est rarement utilise directement dans le code.';

#-----------------------------------------------------------------------------
#-- j_groupes_matieres
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_groupes_matieres;


CREATE TABLE j_groupes_matieres
(
	id_groupe INTEGER  NOT NULL COMMENT 'Cle primaire du groupe',
	id_matiere VARCHAR(255)  NOT NULL COMMENT 'Cle primaire de la matiere',
	PRIMARY KEY (id_groupe,id_matiere),
	CONSTRAINT j_groupes_matieres_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX j_groupes_matieres_FI_2 (id_matiere),
	CONSTRAINT j_groupes_matieres_FK_2
		FOREIGN KEY (id_matiere)
		REFERENCES matieres (matiere)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table permettant le jointure entre un enseignement et une matière.';

#-----------------------------------------------------------------------------
#-- classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS classes;


CREATE TABLE classes
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
	classe VARCHAR(100)  NOT NULL COMMENT 'nom de la classe. Le nom court est différent pour chaque classe.',
	nom_complet VARCHAR(100)  NOT NULL COMMENT 'nom complet de la classe. Le nom long n\'est pas toujours différent pour chaque classe. Le nom long peu servir à catégoriser le niveau.',
	suivi_par VARCHAR(50)  NOT NULL,
	formule VARCHAR(100)  NOT NULL,
	format_nom VARCHAR(5)  NOT NULL,
	display_rang CHAR(1) default 'n' NOT NULL,
	display_address CHAR(1) default 'n' NOT NULL,
	display_coef CHAR(1) default 'y' NOT NULL,
	display_mat_cat CHAR(1) default 'n' NOT NULL,
	display_nbdev CHAR(1) default 'n' NOT NULL,
	display_moy_gen CHAR(1) default 'y' NOT NULL,
	modele_bulletin_pdf VARCHAR(255),
	rn_nomdev CHAR default 'n' NOT NULL,
	rn_toutcoefdev CHAR default 'n' NOT NULL,
	rn_coefdev_si_diff CHAR default 'n' NOT NULL,
	rn_datedev CHAR(1) default 'n' NOT NULL,
	rn_sign_chefetab CHAR(1) default 'n' NOT NULL,
	rn_sign_pp CHAR(1) default 'n' NOT NULL,
	rn_sign_resp CHAR(1) default 'n' NOT NULL,
	rn_sign_nblig INTEGER default 3 NOT NULL,
	rn_formule TEXT  NOT NULL,
	ects_type_formation VARCHAR(255),
	ects_parcours VARCHAR(255),
	ects_code_parcours VARCHAR(255),
	ects_domaines_etude VARCHAR(255),
	ects_fonction_signataire_attestation VARCHAR(255),
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Classe regroupant des eleves';

#-----------------------------------------------------------------------------
#-- periodes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS periodes;


CREATE TABLE periodes
(
	nom_periode VARCHAR(10) COMMENT 'Nom de la periode de note',
	num_periode INTEGER(10)  NOT NULL COMMENT 'identifiant numerique de la periode (1, 2 ou3)',
	verouiller VARCHAR(1) default 'O' NOT NULL COMMENT 'Verrouillage de la periode : O pour verouillee, N pour non verrouillee, P pour partiel (pied de bulletin)',
	id_classe INTEGER(11)  NOT NULL COMMENT 'identifiant numerique de la classe.',
	date_verrouillage DATETIME COMMENT 'date de verrouillage de la periode',
	date_fin DATETIME COMMENT 'date de verrouillage de la periode',
	PRIMARY KEY (num_periode,id_classe),
	INDEX periodes_FI_1 (id_classe),
	CONSTRAINT periodes_FK_1
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table regroupant les periodes de notes pour les classes';

#-----------------------------------------------------------------------------
#-- j_scol_classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_scol_classes;


CREATE TABLE j_scol_classes
(
	login VARCHAR(50)  NOT NULL COMMENT 'Cle primaire de l\'utilisateur',
	id_classe INTEGER(11)  NOT NULL COMMENT 'Cle primaire de la classe',
	PRIMARY KEY (login,id_classe),
	CONSTRAINT j_scol_classes_FK_1
		FOREIGN KEY (login)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE,
	INDEX j_scol_classes_FI_2 (id_classe),
	CONSTRAINT j_scol_classes_FK_2
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table permettant le jointure entre un utilisateur scolarite et une classe.';

#-----------------------------------------------------------------------------
#-- j_groupes_classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_groupes_classes;


CREATE TABLE j_groupes_classes
(
	id_groupe INTEGER  NOT NULL COMMENT 'Cle primaire du groupe',
	id_classe INTEGER  NOT NULL COMMENT 'Cle primaire de la classe',
	priorite SMALLINT  NOT NULL,
	coef DECIMAL  NOT NULL,
	categorie_id INTEGER  NOT NULL,
	saisie_ects TINYINT default 0 COMMENT 'Active ou non la saisie ECTS pour cet enseignement',
	valeur_ects DECIMAL COMMENT 'Valeur par défaut des ECTS pour cet enseignement',
	PRIMARY KEY (id_groupe,id_classe),
	CONSTRAINT j_groupes_classes_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX j_groupes_classes_FI_2 (id_classe),
	CONSTRAINT j_groupes_classes_FK_2
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE CASCADE,
	INDEX j_groupes_classes_FI_3 (categorie_id),
	CONSTRAINT j_groupes_classes_FK_3
		FOREIGN KEY (categorie_id)
		REFERENCES matieres_categories (id)
) ENGINE=MyISAM COMMENT='Table permettant la jointure entre groupe d\'enseignement et une classe. Cette jointure permet de definir un enseignement, c\'est à dire un groupe d\'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe';

#-----------------------------------------------------------------------------
#-- ct_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_entry;


CREATE TABLE ct_entry
(
	id_ct INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire du compte rendu',
	heure_entry TIME default '00:00:00' NOT NULL COMMENT 'heure de l\'entree',
	date_ct INTEGER default 0 NOT NULL COMMENT 'date du compte rendu',
	contenu TEXT  NOT NULL COMMENT 'contenu redactionnel du compte rendu',
	vise CHAR default 'n' NOT NULL COMMENT 'vise',
	visa CHAR default 'n' NOT NULL COMMENT 'visa',
	id_groupe INTEGER  NOT NULL COMMENT 'Cle etrangere du groupe auquel appartient le compte rendu',
	id_login VARCHAR(32) COMMENT 'Cle etrangere de l\'utilisateur auquel appartient le compte rendu',
	id_sequence INTEGER(5) default 0 COMMENT 'Cle etrangere de la sequence auquel appartient le compte rendu',
	PRIMARY KEY (id_ct),
	INDEX ct_entry_FI_1 (id_groupe),
	CONSTRAINT ct_entry_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX ct_entry_FI_2 (id_login),
	CONSTRAINT ct_entry_FK_2
		FOREIGN KEY (id_login)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX ct_entry_FI_3 (id_sequence),
	CONSTRAINT ct_entry_FK_3
		FOREIGN KEY (id_sequence)
		REFERENCES ct_sequences (id)
		ON DELETE SET NULL
) ENGINE=MyISAM COMMENT='Compte rendu du cahier de texte';

#-----------------------------------------------------------------------------
#-- ct_documents
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_documents;


CREATE TABLE ct_documents
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire du document',
	id_ct INTEGER default 0 NOT NULL COMMENT 'Cle etrangere du compte rendu auquel appartient ce document',
	titre VARCHAR(255)  NOT NULL COMMENT 'Titre du document (fichier joint)',
	taille INTEGER default 0 NOT NULL COMMENT 'Taille du document (fichier joint)',
	emplacement VARCHAR(255)  NOT NULL COMMENT 'Chemin du systeme de fichier vers le document (fichier joint)',
	PRIMARY KEY (id),
	INDEX ct_documents_FI_1 (id_ct),
	CONSTRAINT ct_documents_FK_1
		FOREIGN KEY (id_ct)
		REFERENCES ct_entry (id_ct)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Document (fichier joint) appartenant a un compte rendu du cahier de texte';

#-----------------------------------------------------------------------------
#-- ct_devoirs_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_devoirs_entry;


CREATE TABLE ct_devoirs_entry
(
	id_ct INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire du travail Ã  faire',
	date_ct INTEGER default 0 NOT NULL COMMENT 'date pour laquelle le travail est a faire',
	contenu TEXT  NOT NULL COMMENT 'contenu redactionnel du travail a faire',
	vise CHAR default 'n' NOT NULL COMMENT 'vise',
	id_groupe INTEGER  NOT NULL COMMENT 'Cle etrangere du groupe auquel appartient ce travail a faire',
	id_login VARCHAR(32) COMMENT 'Cle etrangere du l\'utilisateur auquel appartient ce travail a faire',
	id_sequence INTEGER(5) default 0 COMMENT 'Cle etrangere de la sequence auquel appartient le devoir a faire',
	PRIMARY KEY (id_ct),
	INDEX ct_devoirs_entry_FI_1 (id_groupe),
	CONSTRAINT ct_devoirs_entry_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX ct_devoirs_entry_FI_2 (id_login),
	CONSTRAINT ct_devoirs_entry_FK_2
		FOREIGN KEY (id_login)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX ct_devoirs_entry_FI_3 (id_sequence),
	CONSTRAINT ct_devoirs_entry_FK_3
		FOREIGN KEY (id_sequence)
		REFERENCES ct_sequences (id)
		ON DELETE SET NULL
) ENGINE=MyISAM COMMENT='Travail Ã  faire (devoir) cahier de texte';

#-----------------------------------------------------------------------------
#-- ct_devoirs_documents
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_devoirs_documents;


CREATE TABLE ct_devoirs_documents
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire du document',
	id_ct_devoir INTEGER default 0 NOT NULL COMMENT 'Cle etrangere du travail a faire auquel appartient ce document',
	titre VARCHAR(255)  NOT NULL COMMENT 'Titre du document (fichier joint)',
	taille INTEGER default 0 NOT NULL COMMENT 'Taille du document (fichier joint)',
	emplacement VARCHAR(255)  NOT NULL COMMENT 'Chemin du systeme de fichier vers le document (fichier joint)',
	PRIMARY KEY (id),
	INDEX ct_devoirs_documents_FI_1 (id_ct_devoir),
	CONSTRAINT ct_devoirs_documents_FK_1
		FOREIGN KEY (id_ct_devoir)
		REFERENCES ct_devoirs_entry (id_ct)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Document (fichier joint) appartenant a un travail Ã  faire du cahier de texte';

#-----------------------------------------------------------------------------
#-- ct_private_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_private_entry;


CREATE TABLE ct_private_entry
(
	id_ct INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la cotice privee',
	heure_entry TIME default '00:00:00' NOT NULL COMMENT 'heure de l\'entree',
	date_ct INTEGER default 0 NOT NULL COMMENT 'date du compte rendu',
	contenu TEXT  NOT NULL COMMENT 'contenu redactionnel du compte rendu',
	id_groupe INTEGER  NOT NULL COMMENT 'Cle etrangere du groupe auquel appartient le compte rendu',
	id_login VARCHAR(32) COMMENT 'Cle etrangere de l\'utilisateur auquel appartient le compte rendu',
	id_sequence INTEGER(5) default 0 COMMENT 'Cle etrangere de la sequence auquel appartient la notice privee',
	PRIMARY KEY (id_ct),
	INDEX ct_private_entry_FI_1 (id_groupe),
	CONSTRAINT ct_private_entry_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX ct_private_entry_FI_2 (id_login),
	CONSTRAINT ct_private_entry_FK_2
		FOREIGN KEY (id_login)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX ct_private_entry_FI_3 (id_sequence),
	CONSTRAINT ct_private_entry_FK_3
		FOREIGN KEY (id_sequence)
		REFERENCES ct_sequences (id)
		ON DELETE SET NULL
) ENGINE=MyISAM COMMENT='Notice privee du cahier de texte';

#-----------------------------------------------------------------------------
#-- ct_sequences
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ct_sequences;


CREATE TABLE ct_sequences
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire des sequences',
	titre VARCHAR(255)  NOT NULL COMMENT 'Titre de la sequence',
	description LONGTEXT  NOT NULL COMMENT 'Description de la sequence',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Sequence de plusieurs compte-rendus';

#-----------------------------------------------------------------------------
#-- eleves
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS eleves;


CREATE TABLE eleves
(
	no_gep VARCHAR(50)  NOT NULL COMMENT 'Ancien numero GEP, Numero national de l\'eleve',
	login VARCHAR(50)  NOT NULL COMMENT 'Login de l\'eleve, est conserve pour le login utilisateur',
	nom VARCHAR(50)  NOT NULL COMMENT 'Nom eleve',
	prenom VARCHAR(50)  NOT NULL COMMENT 'Prenom eleve',
	sexe VARCHAR(1)  NOT NULL COMMENT 'M ou F',
	naissance DATE  NOT NULL COMMENT 'Date de naissance AAAA-MM-JJ',
	lieu_naissance VARCHAR(50) default '' NOT NULL COMMENT 'Code de Sconet',
	elenoet VARCHAR(50)  NOT NULL COMMENT 'Numero interne de l\'eleve dans l\'etablissement',
	ereno VARCHAR(50)  NOT NULL COMMENT 'Plus utilise',
	ele_id VARCHAR(10) default '' NOT NULL COMMENT 'cle utilise par Sconet dans ses fichiers xml',
	email VARCHAR(255) default '' NOT NULL COMMENT 'Courriel de l\'eleve',
	id_eleve INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire autoincremente',
	PRIMARY KEY (id_eleve),
	INDEX I_referenced_j_eleves_classes_FK_1_1 (login),
	INDEX I_referenced_responsables2_FK_1_2 (ele_id),
	INDEX I_referenced_archivage_ects_FK_1_3 (no_gep)
) ENGINE=MyISAM COMMENT='Liste des eleves de l\'etablissement';

#-----------------------------------------------------------------------------
#-- j_eleves_classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_classes;


CREATE TABLE j_eleves_classes
(
	login VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, Login de l\'eleve',
	id_classe INTEGER(11) default 0 NOT NULL COMMENT 'cle etrangere, id de la classe',
	periode INTEGER(11) default 0 NOT NULL COMMENT 'Numéro de la periode ou l\'eleve est inscrit dans cette classe',
	rang SMALLINT(6) default 0 NOT NULL,
	PRIMARY KEY (login,id_classe,periode),
	CONSTRAINT j_eleves_classes_FK_1
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE,
	INDEX j_eleves_classes_FI_2 (id_classe),
	CONSTRAINT j_eleves_classes_FK_2
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure entre les eleves et leur classe en fonction de la periode';

#-----------------------------------------------------------------------------
#-- j_eleves_cpe
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_cpe;


CREATE TABLE j_eleves_cpe
(
	e_login VARCHAR(50) default '' NOT NULL COMMENT 'cle etrangere, login de l\'eleve',
	cpe_login VARCHAR(50) default '' NOT NULL COMMENT 'cle etrangere, login du CPE (utilisateur professionnel)',
	PRIMARY KEY (e_login,cpe_login),
	CONSTRAINT j_eleves_cpe_FK_1
		FOREIGN KEY (e_login)
		REFERENCES eleves (login)
		ON DELETE CASCADE,
	INDEX j_eleves_cpe_FI_2 (cpe_login),
	CONSTRAINT j_eleves_cpe_FK_2
		FOREIGN KEY (cpe_login)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure entre les CPE et les eleves';

#-----------------------------------------------------------------------------
#-- j_eleves_groupes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_groupes;


CREATE TABLE j_eleves_groupes
(
	login VARCHAR(50) default '' NOT NULL COMMENT 'cle etrangere, login de l\'eleve',
	id_groupe INTEGER(11) default 0 NOT NULL COMMENT 'cle etrangere, id du groupe',
	periode INTEGER(11) default 0 NOT NULL COMMENT 'Periode ou l\'eleve est inscrit dans cet enseignement (groupe)',
	PRIMARY KEY (login,id_groupe,periode),
	CONSTRAINT j_eleves_groupes_FK_1
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE,
	INDEX j_eleves_groupes_FI_2 (id_groupe),
	CONSTRAINT j_eleves_groupes_FK_2
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure entre les eleves et leurs enseignements (groupes)';

#-----------------------------------------------------------------------------
#-- j_eleves_professeurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_professeurs;


CREATE TABLE j_eleves_professeurs
(
	login VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, login de l\'eleve',
	professeur VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, login du professeur (utilisateur professionnel)',
	id_classe INTEGER(11)  NOT NULL COMMENT 'cle etrangere, id de la classe',
	PRIMARY KEY (login,professeur,id_classe),
	CONSTRAINT j_eleves_professeurs_FK_1
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE,
	INDEX j_eleves_professeurs_FI_2 (professeur),
	CONSTRAINT j_eleves_professeurs_FK_2
		FOREIGN KEY (professeur)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE,
	INDEX j_eleves_professeurs_FI_3 (id_classe),
	CONSTRAINT j_eleves_professeurs_FK_3
		FOREIGN KEY (id_classe)
		REFERENCES classes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure entre les professeurs principaux et les eleves';

#-----------------------------------------------------------------------------
#-- j_eleves_regime
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_regime;


CREATE TABLE j_eleves_regime
(
	login VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, login de l\'eleve',
	doublant CHAR(1)  NOT NULL COMMENT 'R pour les redoublants et - ou rien pour les autres',
	regime CHAR(5)  NOT NULL COMMENT 'Regime de l\'eleve d/p ext. ... etc',
	PRIMARY KEY (login),
	CONSTRAINT j_eleves_regime_FK_1
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Mention du redoublement eventuel de l\'eleve ainsi que son regime de presence (externe, demi-pensionnaire, ...)';

#-----------------------------------------------------------------------------
#-- responsables2
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS responsables2;


CREATE TABLE responsables2
(
	ele_id VARCHAR(10)  NOT NULL COMMENT 'cle etrangere, ele_id de l\'eleve',
	pers_id VARCHAR(10)  NOT NULL COMMENT 'cle etrangere vers le responsable',
	resp_legal VARCHAR(1)  NOT NULL COMMENT 'Niveau de responsabilite du responsable legal',
	pers_contact VARCHAR(1)  NOT NULL,
	PRIMARY KEY (ele_id,resp_legal),
	CONSTRAINT responsables2_FK_1
		FOREIGN KEY (ele_id)
		REFERENCES eleves (ele_id)
		ON DELETE CASCADE,
	INDEX responsables2_FI_2 (pers_id),
	CONSTRAINT responsables2_FK_2
		FOREIGN KEY (pers_id)
		REFERENCES resp_pers (pers_id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure entre les eleves et leurs responsables legaux avec mention du niveau de ces responsables';

#-----------------------------------------------------------------------------
#-- resp_pers
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS resp_pers;


CREATE TABLE resp_pers
(
	pers_id VARCHAR(10)  NOT NULL COMMENT 'cle primaire genere par sconet',
	login VARCHAR(50)  NOT NULL COMMENT 'cle primaire du responsable, login utilise comme utilisateur',
	nom VARCHAR(30)  NOT NULL COMMENT 'Nom du responsable legal',
	prenom VARCHAR(30)  NOT NULL COMMENT 'Prenom du responsable legal',
	civilite VARCHAR(5)  NOT NULL COMMENT 'civilite du responsable legal : M. Mlle Mme',
	tel_pers VARCHAR(255)  NOT NULL COMMENT 'Telephone personnel du responsable legal',
	tel_port VARCHAR(255)  NOT NULL COMMENT 'Telephone portable du responsable legal',
	tel_prof VARCHAR(255)  NOT NULL COMMENT 'Telephone professionnel du responsable lega',
	mel VARCHAR(100)  NOT NULL COMMENT 'Courriel du responsable legal',
	adr_id VARCHAR(10) COMMENT 'cle etrangere vers l\'adresse du responsable lega',
	PRIMARY KEY (pers_id),
	INDEX resp_pers_FI_1 (adr_id),
	CONSTRAINT resp_pers_FK_1
		FOREIGN KEY (adr_id)
		REFERENCES resp_adr (adr_id)
		ON DELETE SET NULL
) ENGINE=MyISAM COMMENT='Liste des responsables legaux des eleves';

#-----------------------------------------------------------------------------
#-- resp_adr
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS resp_adr;


CREATE TABLE resp_adr
(
	adr_id VARCHAR(10)  NOT NULL COMMENT 'cle primaire, genere par sconet',
	adr1 VARCHAR(100)  NOT NULL COMMENT '1ere ligne adresse',
	adr2 VARCHAR(100)  NOT NULL COMMENT '2eme ligne adresse',
	adr3 VARCHAR(100)  NOT NULL COMMENT '3eme ligne adresse',
	adr4 VARCHAR(100)  NOT NULL COMMENT '4eme ligne adresse',
	cp VARCHAR(6)  NOT NULL COMMENT 'Code postal',
	pays VARCHAR(50)  NOT NULL COMMENT 'Pays (quand il est autre que France)',
	commune VARCHAR(50)  NOT NULL COMMENT 'Commune de residence',
	PRIMARY KEY (adr_id)
) ENGINE=MyISAM COMMENT='Table de jointure entre les responsables legaux et leur adresse';

#-----------------------------------------------------------------------------
#-- j_eleves_etablissements
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_etablissements;


CREATE TABLE j_eleves_etablissements
(
	id_eleve VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, id_eleve de l\'eleve',
	id_etablissement VARCHAR(8) default '' NOT NULL COMMENT 'cle etrangere, id de l\'etablissement',
	PRIMARY KEY (id_eleve,id_etablissement),
	CONSTRAINT j_eleves_etablissements_FK_1
		FOREIGN KEY (id_eleve)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE,
	INDEX j_eleves_etablissements_FI_2 (id_etablissement),
	CONSTRAINT j_eleves_etablissements_FK_2
		FOREIGN KEY (id_etablissement)
		REFERENCES etablissements (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de jointure pour connaitre l\'etablissement precedent de l\'eleve';

#-----------------------------------------------------------------------------
#-- etablissements
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS etablissements;


CREATE TABLE etablissements
(
	id INTEGER(8)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(50)  NOT NULL COMMENT 'Nom de l\'etablissement',
	niveau VARCHAR(50)  NOT NULL COMMENT 'niveau',
	type VARCHAR(50)  NOT NULL COMMENT 'type d\'etablissement',
	cp INTEGER(10)  NOT NULL COMMENT 'code postal de l\'etablissement',
	ville VARCHAR(50) default '' NOT NULL COMMENT 'Ville de l\'etablissement',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des etablissements precedents des eleves';

#-----------------------------------------------------------------------------
#-- aid
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS aid;


CREATE TABLE aid
(
	id VARCHAR(100)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	nom VARCHAR(100) default '' NOT NULL COMMENT 'Nom de l\'AID',
	numero VARCHAR(8) default '0' NOT NULL COMMENT 'Numero d\'ordre d\'affichage',
	indice_aid INTEGER(11) default 0 NOT NULL COMMENT 'Cle etrangere, vers la liste des categories d\'AID (aid_config)',
	perso1 VARCHAR(255)  NOT NULL,
	perso2 VARCHAR(255)  NOT NULL,
	perso3 VARCHAR(255)  NOT NULL,
	productions VARCHAR(100)  NOT NULL,
	resume TEXT  NOT NULL,
	famille SMALLINT(6)  NOT NULL,
	mots_cles VARCHAR(255)  NOT NULL,
	adresse1 VARCHAR(255)  NOT NULL,
	adresse2 VARCHAR(255)  NOT NULL,
	public_destinataire VARCHAR(50)  NOT NULL,
	contacts TEXT  NOT NULL,
	divers TEXT  NOT NULL,
	matiere1 VARCHAR(100)  NOT NULL,
	matiere2 VARCHAR(100)  NOT NULL,
	eleve_peut_modifier CHAR(1) default 'n' NOT NULL,
	prof_peut_modifier CHAR(1) default 'n' NOT NULL,
	cpe_peut_modifier CHAR(1) default 'n' NOT NULL,
	fiche_publique CHAR(1) default 'n' NOT NULL,
	affiche_adresse1 CHAR(1) default 'n' NOT NULL,
	en_construction CHAR(1) default 'n' NOT NULL,
	PRIMARY KEY (id),
	INDEX aid_FI_1 (indice_aid),
	CONSTRAINT aid_FK_1
		FOREIGN KEY (indice_aid)
		REFERENCES aid_config (indice_aid)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Liste des AID (Activites Inter-Disciplinaires)';

#-----------------------------------------------------------------------------
#-- aid_config
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS aid_config;


CREATE TABLE aid_config
(
	nom CHAR(100) default '' NOT NULL COMMENT 'Nom de la categorie d\'AID',
	nom_complet CHAR(100) default '' NOT NULL COMMENT 'Nom complet de la categorie d\'AID',
	note_max INTEGER(11) default 0 NOT NULL COMMENT 'Note maximum qu\'on peut mettre pour cette categorie d\'AID',
	order_display1 CHAR(1) default '0' NOT NULL,
	order_display2 INTEGER(11) default 0 NOT NULL,
	type_note CHAR(5) default '' NOT NULL COMMENT 'A no si cette AID n\'est pas notee',
	display_begin INTEGER(11) default 0 NOT NULL COMMENT 'Numero de la periode de debut de cette categorie d\'AID',
	display_end INTEGER(11) default 0 NOT NULL COMMENT 'Numero de la periode de fin de cette categorie d\'AID',
	message CHAR(20) default '' NOT NULL,
	display_nom CHAR(1) default '' NOT NULL,
	indice_aid INTEGER(11) default 0 NOT NULL COMMENT 'cle primaire de chaque categorie d\'AID',
	display_bulletin CHAR(1) default 'y' NOT NULL COMMENT 'Pour savoir si cette categorie d\'AID est presente sur le bulletin classique',
	bull_simplifie CHAR(1) default 'y' NOT NULL COMMENT 'Pour savoir si cette categorie d\'AID est presente sur le bulletin simplifie',
	outils_complementaires CHAR(1) default 'n' NOT NULL,
	feuille_presence CHAR(1) default 'n' NOT NULL,
	PRIMARY KEY (indice_aid)
) ENGINE=MyISAM COMMENT='Liste des categories d\'AID (Activites inter-Disciplinaires)';

#-----------------------------------------------------------------------------
#-- j_aid_utilisateurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_aid_utilisateurs;


CREATE TABLE j_aid_utilisateurs
(
	id_aid VARCHAR(100)  NOT NULL COMMENT 'cle etrangere vers l\'AID',
	id_utilisateur VARCHAR(100)  NOT NULL COMMENT 'Login de l\'utilisateur professionnel',
	PRIMARY KEY (id_aid,id_utilisateur),
	CONSTRAINT j_aid_utilisateurs_FK_1
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE CASCADE,
	INDEX j_aid_utilisateurs_FI_2 (id_utilisateur),
	CONSTRAINT j_aid_utilisateurs_FK_2
		FOREIGN KEY (id_utilisateur)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de liaison entre les AID et les utilisateurs professionnels';

#-----------------------------------------------------------------------------
#-- j_aid_eleves
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_aid_eleves;


CREATE TABLE j_aid_eleves
(
	id_aid VARCHAR(100)  NOT NULL COMMENT 'Clé etrangere vers l\'AID',
	login VARCHAR(60)  NOT NULL COMMENT 'Login de l\'eleve qui est membre de cette AID',
	PRIMARY KEY (id_aid,login),
	CONSTRAINT j_aid_eleves_FK_1
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE CASCADE,
	INDEX j_aid_eleves_FI_2 (login),
	CONSTRAINT j_aid_eleves_FK_2
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table de liaison entre les AID et les eleves qui en sont membres';

#-----------------------------------------------------------------------------
#-- a_motifs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_motifs;


CREATE TABLE a_motifs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des motifs possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_justifications
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_justifications;


CREATE TABLE a_justifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des justifications possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_types
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_types;


CREATE TABLE a_types
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	justification_exigible TINYINT COMMENT 'Ce type d\'absence doit entrainer une justification de la part de la famille',
	sous_responsabilite_etablissement VARCHAR(255) default 'NON_PRECISE' COMMENT 'L\'eleve est sous la responsabilite de l\'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l\'eleve est encore sous la responsabilité de l\'etablissement. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	manquement_obligation_presence VARCHAR(50) default 'NON_PRECISE' COMMENT 'L\'eleve manque à ses obligations de presence (L\'absence apparait sur le bulletin). Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	retard_bulletin VARCHAR(50) default 'NON_PRECISE' COMMENT 'La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : \'vrai\'/\'faux\'/\'non_precise\'',
	type_saisie VARCHAR(50) default 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des types d\'absences possibles dans l\'etablissement';

#-----------------------------------------------------------------------------
#-- a_types_statut
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_types_statut;


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
) ENGINE=MyISAM COMMENT='Liste des statuts autorises à saisir des types d\'absences';

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_saisies;


CREATE TABLE a_saisies
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
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
	modifie_par_utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a modifie en dernier le traitement',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_saisies_FI_1 (utilisateur_id),
	CONSTRAINT a_saisies_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
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
		ON DELETE SET NULL,
	INDEX a_saisies_FI_8 (modifie_par_utilisateur_id),
	CONSTRAINT a_saisies_FK_8
		FOREIGN KEY (modifie_par_utilisateur_id)
		REFERENCES utilisateurs (login)
) ENGINE=MyISAM COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l\'emploi du temps, le jours du cours etant precisé dans debut_abs.';

#-----------------------------------------------------------------------------
#-- a_traitements
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_traitements;


CREATE TABLE a_traitements
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	a_type_id INTEGER(4) COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) COMMENT 'cle etrangere de la justification de l\'absence',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	modifie_par_utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a modifie en dernier le traitement',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (id),
	INDEX a_traitements_FI_1 (utilisateur_id),
	CONSTRAINT a_traitements_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login),
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
		ON DELETE SET NULL,
	INDEX a_traitements_FI_5 (modifie_par_utilisateur_id),
	CONSTRAINT a_traitements_FK_5
		FOREIGN KEY (modifie_par_utilisateur_id)
		REFERENCES utilisateurs (login)
) ENGINE=MyISAM COMMENT='Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies';

#-----------------------------------------------------------------------------
#-- j_traitements_saisies
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_traitements_saisies;


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
) ENGINE=MyISAM COMMENT='Table de jointure entre la saisie et le traitement des absences';

#-----------------------------------------------------------------------------
#-- a_notifications
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_notifications;


CREATE TABLE a_notifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui envoi la notification',
	a_traitement_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere du traitement qu\'on notifie',
	type_notification INTEGER(5) COMMENT 'type de notification (0 : email, 1 : courrier, 2 : sms)',
	email VARCHAR(100) COMMENT 'email de destination (pour le type email)',
	telephone VARCHAR(100) COMMENT 'numero du telephone de destination (pour le type sms)',
	adr_id VARCHAR(10) COMMENT 'cle etrangere vers l\'adresse de destination (pour le type courrier)',
	commentaire TEXT COMMENT 'commentaire saisi par l\'utilisateur',
	statut_envoi INTEGER(5) default 0 COMMENT 'Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)',
	date_envoi DATETIME COMMENT 'Date envoi',
	erreur_message_envoi TEXT COMMENT 'Message d\'erreur retourné par le service d\'envoi',
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
) ENGINE=MyISAM COMMENT='Notification (a la famille) des absences';

#-----------------------------------------------------------------------------
#-- j_notifications_resp_pers
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_notifications_resp_pers;


CREATE TABLE j_notifications_resp_pers
(
	a_notification_id INTEGER(12)  NOT NULL COMMENT 'cle etrangere de la notification',
	pers_id VARCHAR(10)  NOT NULL COMMENT 'cle etrangere des personnes',
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
) ENGINE=MyISAM COMMENT='Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.';

#-----------------------------------------------------------------------------
#-- ects_credits
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ects_credits;


CREATE TABLE ects_credits
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	id_eleve INTEGER(11)  NOT NULL COMMENT 'Identifiant de l\'eleve',
	num_periode INTEGER(11)  NOT NULL COMMENT 'Identifiant de la periode',
	id_groupe INTEGER(11)  NOT NULL COMMENT 'Identifiant du groupe',
	valeur DECIMAL COMMENT 'Nombre de credits obtenus par l\'eleve',
	mention VARCHAR(255) COMMENT 'Mention obtenue',
	mention_prof VARCHAR(255) COMMENT 'Mention presaisie par le prof',
	PRIMARY KEY (id,id_eleve,num_periode,id_groupe),
	INDEX ects_credits_FI_1 (id_eleve),
	CONSTRAINT ects_credits_FK_1
		FOREIGN KEY (id_eleve)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE,
	INDEX ects_credits_FI_2 (id_groupe),
	CONSTRAINT ects_credits_FK_2
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Objet qui précise le nombre d\'ECTS obtenus par l\'eleve pour un enseignement et une periode donnée';

#-----------------------------------------------------------------------------
#-- ects_global_credits
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS ects_global_credits;


CREATE TABLE ects_global_credits
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	id_eleve INTEGER(11)  NOT NULL COMMENT 'Identifiant de l\'eleve',
	mention VARCHAR(255)  NOT NULL COMMENT 'Mention obtenue',
	PRIMARY KEY (id,id_eleve),
	INDEX ects_global_credits_FI_1 (id_eleve),
	CONSTRAINT ects_global_credits_FK_1
		FOREIGN KEY (id_eleve)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Objet qui précise la mention globale obtenue pour un eleve';

#-----------------------------------------------------------------------------
#-- archivage_ects
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS archivage_ects;


CREATE TABLE archivage_ects
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	annee VARCHAR(255)  NOT NULL COMMENT 'Annee scolaire',
	ine VARCHAR(255)  NOT NULL COMMENT 'Identifiant de l\'eleve',
	classe VARCHAR(255)  NOT NULL COMMENT 'Classe de l\'eleve',
	num_periode INTEGER(11)  NOT NULL COMMENT 'Identifiant de la periode',
	nom_periode VARCHAR(255)  NOT NULL COMMENT 'Nom complet de la periode',
	special VARCHAR(255)  NOT NULL COMMENT 'Cle utilisee pour isoler certaines lignes (par exemple un credit ECTS pour une periode et non une matiere)',
	matiere VARCHAR(255) COMMENT 'Nom de l\'enseignement',
	profs VARCHAR(255) COMMENT 'Liste des profs de l\'enseignement',
	valeur DECIMAL  NOT NULL COMMENT 'Nombre de crédits obtenus par l\'eleve',
	mention VARCHAR(255)  NOT NULL COMMENT 'Mention obtenue',
	PRIMARY KEY (id,ine,num_periode,special),
	INDEX archivage_ects_FI_1 (ine),
	CONSTRAINT archivage_ects_FK_1
		FOREIGN KEY (ine)
		REFERENCES eleves (no_gep)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Enregistrement d\'archive pour les credits ECTS, dont le rapport n\'est edite qu\'au depart de l\'eleve';

#-----------------------------------------------------------------------------
#-- matieres
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS matieres;


CREATE TABLE matieres
(
	matiere VARCHAR(255)  NOT NULL,
	nom_complet VARCHAR(200)  NOT NULL COMMENT 'Nom complet',
	priority INTEGER(6) default 0 NOT NULL COMMENT 'Priorite d\'affichage',
	matiere_aid VARCHAR(1) default 'n' COMMENT 'Matiere AID',
	matiere_atelier VARCHAR(1) default 'n' COMMENT 'Matiere Atelier',
	categorie_id INTEGER(11) default 1 NOT NULL COMMENT 'Association avec Categories de matieres',
	PRIMARY KEY (matiere),
	INDEX matieres_FI_1 (categorie_id),
	CONSTRAINT matieres_FK_1
		FOREIGN KEY (categorie_id)
		REFERENCES matieres_categories (id)
) ENGINE=MyISAM COMMENT='Matières';

#-----------------------------------------------------------------------------
#-- matieres_categories
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS matieres_categories;


CREATE TABLE matieres_categories
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	nom_court VARCHAR(255)  NOT NULL COMMENT 'Nom court',
	nom_complet VARCHAR(255)  NOT NULL COMMENT 'Nom complet',
	priority INTEGER(6)  NOT NULL COMMENT 'Priorite d\'affichage',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Categories de matiere, utilisees pour regrouper des enseignements';

#-----------------------------------------------------------------------------
#-- j_matieres_categories_classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_matieres_categories_classes;


CREATE TABLE j_matieres_categories_classes
(
	categorie_id INTEGER(11)  NOT NULL,
	classe_id INTEGER(11)  NOT NULL,
	affiche_moyenne TINYINT default 0 COMMENT 'Nom complet',
	priority INTEGER(6)  NOT NULL COMMENT 'Priorite d\'affichage',
	PRIMARY KEY (categorie_id,classe_id),
	CONSTRAINT j_matieres_categories_classes_FK_1
		FOREIGN KEY (categorie_id)
		REFERENCES matieres_categories (id)
		ON DELETE CASCADE,
	INDEX j_matieres_categories_classes_FI_2 (classe_id),
	CONSTRAINT j_matieres_categories_classes_FK_2
		FOREIGN KEY (classe_id)
		REFERENCES classes (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Liaison entre categories de matiere et classes';

#-----------------------------------------------------------------------------
#-- j_professeurs_matieres
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_professeurs_matieres;


CREATE TABLE j_professeurs_matieres
(
	id_matiere VARCHAR(50)  NOT NULL,
	id_professeur VARCHAR(50)  NOT NULL,
	ordre_matieres INTEGER(11) default 0 NOT NULL COMMENT 'Priorite d\'affichage',
	PRIMARY KEY (id_matiere,id_professeur),
	CONSTRAINT j_professeurs_matieres_FK_1
		FOREIGN KEY (id_matiere)
		REFERENCES matieres (matiere),
	INDEX j_professeurs_matieres_FI_2 (id_professeur),
	CONSTRAINT j_professeurs_matieres_FK_2
		FOREIGN KEY (id_professeur)
		REFERENCES utilisateurs (login)
) ENGINE=MyISAM COMMENT='Liaison entre les profs et les matières';

#-----------------------------------------------------------------------------
#-- plugins
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS plugins;


CREATE TABLE plugins
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire',
	nom VARCHAR(100)  NOT NULL COMMENT 'Nom du plugin',
	repertoire VARCHAR(255)  NOT NULL COMMENT 'Repertoire du plugin',
	description LONGTEXT  NOT NULL COMMENT 'Description du plugin',
	ouvert CHAR(1)  NOT NULL COMMENT 'Statut du plugin, si il est operationnel y/n',
	PRIMARY KEY (id)
) ENGINE=MyISAM COMMENT='Liste des plugins installes sur ce Gepi';

#-----------------------------------------------------------------------------
#-- plugins_autorisations
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS plugins_autorisations;


CREATE TABLE plugins_autorisations
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire',
	plugin_id INTEGER(11)  NOT NULL COMMENT 'cle etrangere vers la table plugins',
	fichier VARCHAR(100)  NOT NULL COMMENT 'Nom d\'un fichier de ce plugin',
	user_statut VARCHAR(50)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	auth CHAR(1)  NOT NULL COMMENT 'Est-ce que ce statut a le droit de voir ce fichier y/n',
	PRIMARY KEY (id),
	INDEX plugins_autorisations_FI_1 (plugin_id),
	CONSTRAINT plugins_autorisations_FK_1
		FOREIGN KEY (plugin_id)
		REFERENCES plugins (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Liste des autorisations pour chaque statut';

#-----------------------------------------------------------------------------
#-- plugins_menus
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS plugins_menus;


CREATE TABLE plugins_menus
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire',
	plugin_id INTEGER(11)  NOT NULL COMMENT 'cle etrangere vers la table plugins',
	user_statut VARCHAR(50)  NOT NULL COMMENT 'Statut de l\'utilisateur',
	titre_item VARCHAR(255)  NOT NULL COMMENT 'Titre du lien qui amène vers le bon fichier',
	lien_item VARCHAR(255)  NOT NULL COMMENT 'url relative',
	description_item VARCHAR(255)  NOT NULL COMMENT 'Description du lien',
	PRIMARY KEY (id),
	INDEX plugins_menus_FI_1 (plugin_id),
	CONSTRAINT plugins_menus_FK_1
		FOREIGN KEY (plugin_id)
		REFERENCES plugins (id)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Items pour construire le menu de ce plug-in';

#-----------------------------------------------------------------------------
#-- preferences
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS preferences;


CREATE TABLE preferences
(
	name VARCHAR(50)  NOT NULL COMMENT 'Cle primaire du compte rendu',
	value TEXT  NOT NULL AUTO_INCREMENT COMMENT 'valeur associe a la cle',
	login VARCHAR(50)  NOT NULL COMMENT 'Cle etrangere de l\'utilisateur auquel appartient le compte rendu',
	PRIMARY KEY (name,login),
	INDEX preferences_FI_1 (login),
	CONSTRAINT preferences_FK_1
		FOREIGN KEY (login)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Preference (cle - valeur) associes à un utilisateur professionnel';

#-----------------------------------------------------------------------------
#-- edt_creneaux
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS edt_creneaux;


CREATE TABLE edt_creneaux
(
	id_definie_periode INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	nom_definie_periode VARCHAR(50)  NOT NULL COMMENT 'Nom du creneau - typiquement, M1, M2, R (pour repas), P (pour récréation), S1, S2 etc',
	heuredebut_definie_periode TIME  NOT NULL COMMENT 'Heure de debut du creneau',
	heurefin_definie_periode TIME  NOT NULL COMMENT 'Heure de fin du creneau',
	suivi_definie_periode INTEGER(2) default 9 COMMENT 'champ inutilise',
	type_creneaux VARCHAR(15) default 'cours' COMMENT 'types possibles : cours, pause, repas, vie_scolaire',
	jour_creneau VARCHAR(20) COMMENT 'Par defaut, aucun jour en particulier mais on peut imposer que des creneaux soient specifiques a un jour en particulier : \'lundi\', \'mardi\', \'mercredi\'...',
	PRIMARY KEY (id_definie_periode)
) ENGINE=MyISAM COMMENT='Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)';

#-----------------------------------------------------------------------------
#-- horaires_etablissement
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS horaires_etablissement;


CREATE TABLE horaires_etablissement
(
	id_horaire_etablissement INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	date_horaire_etablissement DATE COMMENT 'NULL (c\'etait un 0 a l\'origine...voir si pb) = horaires valables toute l\'annee pour le jour specifie - date precise = horaires valables uniquement pour cette date',
	jour_horaire_etablissement VARCHAR(15)  NOT NULL COMMENT 'defini le jour de la semaine - typiquement, lundi, mardi, etc...',
	ouverture_horaire_etablissement TIME  NOT NULL COMMENT 'Heure d\'ouverture de l\'etablissement',
	fermeture_horaire_etablissement TIME  NOT NULL COMMENT 'Heure de fermeture de l\'etablissement',
	pause_horaire_etablissement TIME COMMENT 'champ non utilise',
	ouvert_horaire_etablissement TINYINT  NOT NULL COMMENT '1 = etablissement ouvert - 0 = etablissement ferme',
	PRIMARY KEY (id_horaire_etablissement)
) ENGINE=MyISAM COMMENT='Table contenant les heures d\'ouverture et de fermeture de l\'etablissement par journee';

#-----------------------------------------------------------------------------
#-- edt_semaines
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS edt_semaines;


CREATE TABLE edt_semaines
(
	id_edt_semaine INTEGER(10)  NOT NULL COMMENT 'cle primaire',
	num_edt_semaine INTEGER(10)  NOT NULL COMMENT 'numero de la semaine dans l\'annee civile',
	type_edt_semaine VARCHAR(10) COMMENT 'typiquement, champ egal a \'A\' ou \'B\' pour l\'alternance des semaines',
	num_semaines_etab INTEGER(10) COMMENT 'numero de la semaine propre a l\'etablissement',
	PRIMARY KEY (id_edt_semaine)
) ENGINE=MyISAM COMMENT='Liste des semaines de l\'annee scolaire courante - 53 enregistrements obligatoires (pas 52!), pour lesquel on assigne un type (A ou B par exemple)';

#-----------------------------------------------------------------------------
#-- edt_calendrier
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS edt_calendrier;


CREATE TABLE edt_calendrier
(
	id_calendrier INTEGER(11)  NOT NULL COMMENT 'cle primaire',
	classe_concerne_calendrier TEXT  NOT NULL COMMENT 'id des classes (separes par des ;) concernees par cette periode',
	nom_calendrier VARCHAR(100)  NOT NULL COMMENT 'nom de la periode definie',
	debut_calendrier_ts VARCHAR(255)  NOT NULL COMMENT 'timestamp du debut de la periode',
	fin_calendrier_ts VARCHAR(255)  NOT NULL COMMENT 'timestamp de la fin de la periode',
	jourdebut_calendrier DATE(11)  NOT NULL COMMENT 'date du debut de la periode',
	heuredebut_calendrier TIME(11)  NOT NULL COMMENT 'heure du debut de la periode',
	jourfin_calendrier DATE(11)  NOT NULL COMMENT 'date de la fin de la periode',
	heurefin_calendrier TIME(11)  NOT NULL COMMENT 'heure de la fin de la periode',
	numero_periode TINYINT(4)  NOT NULL COMMENT 'id de la periode de notes associee',
	etabferme_calendrier TINYINT(4)  NOT NULL COMMENT 'egal a 1 si etablissement ouvert sur cette periode - 0 sinon',
	etabvacances_calendrier TINYINT(4)  NOT NULL COMMENT 'egal a 1 si la periode est definie sur les vacances - 0 sinon',
	PRIMARY KEY (id_calendrier)
) ENGINE=MyISAM COMMENT='Liste des periodes datees de l\'annee courante(pour definir par exemple les trimestres)';

#-----------------------------------------------------------------------------
#-- edt_cours
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS edt_cours;


CREATE TABLE edt_cours
(
	id_cours INTEGER(3)  NOT NULL COMMENT 'cle primaire',
	id_groupe CHAR(10) COMMENT 'id du groupe d\'enseignement concerne - \'\' sinon',
	id_aid CHAR(10) COMMENT 'id de l\'aid concerne - \'\' sinon',
	id_salle CHAR(10) COMMENT 'id de la salle concernee',
	jour_semaine VARCHAR(10)  NOT NULL COMMENT 'jour de la semaine ou a lieu le cours : lundi, mardi etc...',
	id_definie_periode VARCHAR(3)  NOT NULL COMMENT 'id du creneau de la journee ou a lieu le cours - voir table edt_creneaux ',
	duree VARCHAR(10) default '2' NOT NULL COMMENT 'duree du cours definie en demi-creneaux.1h de cours correspond a une duree=2. Les creneaux de pause ne sont pas comptabilisé',
	heuredeb_dec VARCHAR(3) default '0' NOT NULL COMMENT '0 si le cours commence au debut du creneau - 0.5 s\'il commence au milieu',
	id_semaine VARCHAR(3) default '' COMMENT 'type de semaine - typiquement, \'A\' ou \'B\' si on a une alternance semaine A, semaine B.',
	id_calendrier VARCHAR(3) COMMENT 'NULL = le cours a lieu toute l\'annee - sinon, id de la periode (EdtCalendrierPeriode) durant laquelle a lieu le cours',
	modif_edt VARCHAR(3) COMMENT 'champ inutilise',
	login_prof VARCHAR(50) COMMENT 'login du prof qui dispense le cours',
	PRIMARY KEY (id_cours),
	INDEX edt_cours_FI_1 (id_groupe),
	CONSTRAINT edt_cours_FK_1
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
		ON DELETE CASCADE,
	INDEX edt_cours_FI_2 (id_aid),
	CONSTRAINT edt_cours_FK_2
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE CASCADE,
	INDEX edt_cours_FI_3 (id_salle),
	CONSTRAINT edt_cours_FK_3
		FOREIGN KEY (id_salle)
		REFERENCES salle_cours (id_salle)
		ON DELETE SET NULL,
	INDEX edt_cours_FI_4 (id_definie_periode),
	CONSTRAINT edt_cours_FK_4
		FOREIGN KEY (id_definie_periode)
		REFERENCES edt_creneaux (id_definie_periode)
		ON DELETE CASCADE,
	INDEX edt_cours_FI_5 (id_calendrier),
	CONSTRAINT edt_cours_FK_5
		FOREIGN KEY (id_calendrier)
		REFERENCES edt_calendrier (id_calendrier)
		ON DELETE SET NULL,
	INDEX edt_cours_FI_6 (login_prof),
	CONSTRAINT edt_cours_FK_6
		FOREIGN KEY (login_prof)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL
) ENGINE=MyISAM COMMENT='Liste de tous les creneaux de tous les emplois du temps';

#-----------------------------------------------------------------------------
#-- salle_cours
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS salle_cours;


CREATE TABLE salle_cours
(
	id_salle INTEGER(3)  NOT NULL COMMENT 'cle primaire',
	numero_salle VARCHAR(10)  NOT NULL COMMENT 'numero de la salle defini par l\'utilisateur',
	nom_salle VARCHAR(50) COMMENT 'nom de la salle defini par l\'utilisateur',
	PRIMARY KEY (id_salle)
) ENGINE=MyISAM COMMENT='Liste des salles de classe';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
