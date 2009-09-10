
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
	date_verrouillage TIME default '2006-01-01 00:00:00' NOT NULL COMMENT 'Date de verrouillage de l\'utilisateur',
	password_ticket VARCHAR(255)  NOT NULL COMMENT 'password_ticket de l\'utilisateur',
	ticket_expiration TIME  NOT NULL COMMENT 'ticket_expiration de l\'utilisateur',
	niveau_alerte SMALLINT default 0 NOT NULL COMMENT 'niveau_alerte de l\'utilisateur',
	observation_securite TINYINT default 0 NOT NULL COMMENT 'observation_securite de l\'utilisateur',
	temp_dir VARCHAR(255)  NOT NULL COMMENT 'Repertoire temporaire de l\'utilisateur',
	numind VARCHAR(255)  NOT NULL COMMENT 'numind de l\'utilisateur',
	auth_mode VARCHAR(255) default 'gepi' NOT NULL COMMENT 'auth_mode de l\'utilisateur (gepi/cas/ldap)',
	PRIMARY KEY (login)
)Type=MyISAM COMMENT='Utilisateur de gepi';

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
)Type=MyISAM COMMENT='Groupe d\'eleves permettant d\'y affecter une matiere et un professeurs';

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
)Type=MyISAM COMMENT='Table permettant le jointure entre groupe d\'eleves et professeurs. Est rarement utilise directement dans le code.';

#-----------------------------------------------------------------------------
#-- classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS classes;


CREATE TABLE classes
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
	classe VARCHAR(100)  NOT NULL COMMENT 'nom de la classe',
	nom_complet VARCHAR(100)  NOT NULL COMMENT 'nom complet de la classe',
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
)Type=MyISAM COMMENT='Classe regroupant des eleves';

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
)Type=MyISAM COMMENT='Table permettant la jointure entre groupe d\'eleves et une classe. Cette jointure permet de definir un enseignement, c\'est à dire un groupe d\'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe';

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
)Type=MyISAM COMMENT='Compte rendu du cahier de texte';

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
)Type=MyISAM COMMENT='Document (fichier joint) appartenant a un compte rendu du cahier de texte';

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
)Type=MyISAM COMMENT='Travail Ã  faire (devoir) cahier de texte';

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
)Type=MyISAM COMMENT='Document (fichier joint) appartenant a un travail Ã  faire du cahier de texte';

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
)Type=MyISAM COMMENT='Notice privee du cahier de texte';

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
)Type=MyISAM COMMENT='Sequence de plusieurs compte-rendus';

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
	INDEX I_referenced_j_eleves_cpe_FK_1_2 (login),
	INDEX I_referenced_j_eleves_groupes_FK_1_3 (login),
	INDEX I_referenced_j_eleves_professeurs_FK_1_4 (login),
	INDEX I_referenced_j_eleves_regime_FK_1_5 (login),
	INDEX I_referenced_responsables2_FK_1_6 (ele_id),
	INDEX I_referenced_j_aid_eleves_FK_2_7 (login),
	INDEX I_referenced_archivage_ects_FK_1_8 (no_gep)
)Type=MyISAM COMMENT='Liste des eleves de l\'etablissement';

#-----------------------------------------------------------------------------
#-- j_eleves_classes
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_eleves_classes;


CREATE TABLE j_eleves_classes
(
	login VARCHAR(50)  NOT NULL COMMENT 'cle etrangere, Login de l\'eleve',
	id_classe INTEGER(11) default 0 NOT NULL COMMENT 'cle etrangere, id de la classe',
	periode INTEGER(11) default 0 NOT NULL COMMENT 'Periode ou l\'eleve est inscrit dans cette classe',
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
)Type=MyISAM COMMENT='Table de jointure entre les eleves et leur classe en fonction de la periode';

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
)Type=MyISAM COMMENT='Table de jointure entre les CPE et les eleves';

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
)Type=MyISAM COMMENT='Table de jointure entre les eleves et leurs enseignements (groupes)';

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
)Type=MyISAM COMMENT='Table de jointure entre les professeurs principaux et les eleves';

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
)Type=MyISAM COMMENT='Mention du redoublement eventuel de l\'eleve ainsi que son regime de presence (externe, demi-pensionnaire, ...)';

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
)Type=MyISAM COMMENT='Table de jointure entre les eleves et leurs responsables legaux avec mention du niveau de ces responsables';

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
)Type=MyISAM COMMENT='Liste des responsables legaux des eleves';

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
)Type=MyISAM COMMENT='Table de jointure entre les responsables legaux et leur adresse';

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
)Type=MyISAM COMMENT='Table de jointure pour connaitre l\'etablissement precedent de l\'eleve';

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
)Type=MyISAM COMMENT='Liste des etablissements precedents des eleves';

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
)Type=MyISAM COMMENT='Liste des AID (Activites Inter-Disciplinaires)';

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
)Type=MyISAM COMMENT='Liste des categories d\'AID (Activites inter-Disciplinaires)';

#-----------------------------------------------------------------------------
#-- j_aid_utilisateurs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_aid_utilisateurs;


CREATE TABLE j_aid_utilisateurs
(
	id_aid VARCHAR(100)  NOT NULL COMMENT 'cle etrangere vers l\'AID',
	id_utilisateur VARCHAR(100)  NOT NULL COMMENT 'Login de l\'utilisateur professionnel',
	indice_aid INTEGER(11) default 0 NOT NULL COMMENT 'cle etrangere vers la categorie d\'AID',
	PRIMARY KEY (id_aid,indice_aid),
	CONSTRAINT j_aid_utilisateurs_FK_1
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE CASCADE,
	INDEX j_aid_utilisateurs_FI_2 (id_utilisateur),
	CONSTRAINT j_aid_utilisateurs_FK_2
		FOREIGN KEY (id_utilisateur)
		REFERENCES utilisateurs (login)
		ON DELETE CASCADE,
	INDEX j_aid_utilisateurs_FI_3 (indice_aid),
	CONSTRAINT j_aid_utilisateurs_FK_3
		FOREIGN KEY (indice_aid)
		REFERENCES aid_config (indice_aid)
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Table de liaison entre les AID et les utilisateurs professionnels';

#-----------------------------------------------------------------------------
#-- j_aid_eleves
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS j_aid_eleves;


CREATE TABLE j_aid_eleves
(
	id_aid VARCHAR(100)  NOT NULL COMMENT 'Clé etrangere vers l\'AID',
	login VARCHAR(60)  NOT NULL COMMENT 'Login de l\'eleve qui est membre de cette AID',
	indice_aid INTEGER(11) default 0 NOT NULL COMMENT 'cle etrangere vers la categorie d\'AID',
	PRIMARY KEY (login,indice_aid),
	INDEX j_aid_eleves_FI_1 (id_aid),
	CONSTRAINT j_aid_eleves_FK_1
		FOREIGN KEY (id_aid)
		REFERENCES aid (id)
		ON DELETE CASCADE,
	CONSTRAINT j_aid_eleves_FK_2
		FOREIGN KEY (login)
		REFERENCES eleves (login)
		ON DELETE CASCADE,
	INDEX j_aid_eleves_FI_3 (indice_aid),
	CONSTRAINT j_aid_eleves_FK_3
		FOREIGN KEY (indice_aid)
		REFERENCES aid_config (indice_aid)
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Table de liaison entre les AID et les eleves qui en sont membres';

#-----------------------------------------------------------------------------
#-- a_creneaux
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_creneaux;


CREATE TABLE a_creneaux
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	nom_creneau VARCHAR(50)  NOT NULL COMMENT 'Nom du creneau',
	debut_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui séparent l\'horaire de debut avec 00:00:00 du jour',
	fin_creneau INTEGER(12)  NOT NULL COMMENT 'Nombre de secondes qui séparent l\'horaire de fin avec 00:00:00 du jour',
	jour_creneau INTEGER(2) default 9 NOT NULL COMMENT 'Par defaut, aucun jour en particulier mais on peut imposer que des creneaux soient specifiques a un jour en particulier',
	type_creneau VARCHAR(15)  NOT NULL COMMENT '3 types : cours, pause, repas',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Les creneaux sont la base du temps des eleves et des cours';

#-----------------------------------------------------------------------------
#-- a_actions
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_actions;


CREATE TABLE a_actions
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de l\'action',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage de l\'action dans la liste déroulante',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des actions possibles sur une absence';

#-----------------------------------------------------------------------------
#-- a_motifs
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_motifs;


CREATE TABLE a_motifs
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du motif',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage du motif dans la liste déroulante',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des motifs possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_justifications
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_justifications;


CREATE TABLE a_justifications
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom de la justification',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage de la justification dans la liste déroulante',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des justifications possibles pour une absence';

#-----------------------------------------------------------------------------
#-- a_types
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_types;


CREATE TABLE a_types
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du type d\'absence',
	ordre INTEGER(3)  NOT NULL COMMENT 'Ordre d\'affichage du type dans la liste déroulante',
	PRIMARY KEY (id)
)Type=MyISAM COMMENT='Liste des types d\'absences possibles dans l\'etablissement';

#-----------------------------------------------------------------------------
#-- a_saisies
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_saisies;


CREATE TABLE a_saisies
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT,
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a saisi l\'absence',
	eleve_id INTEGER(4)  NOT NULL COMMENT 'id_eleve de l\'eleve objet de la saisie, egal à \'appel\' si aucun eleve n\'est saisi',
	created_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la saisie de l\'absence en timestamp UNIX',
	updated_on INTEGER(13) default 0 NOT NULL COMMENT 'Date de la modification de la saisie en timestamp UNIX',
	debut_abs INTEGER(12) default 0 NOT NULL COMMENT 'Debut de l\'absence en timestamp UNIX',
	fin_abs INTEGER(12) default 0 NOT NULL COMMENT 'Fin de l\'absence en timestamp UNIX',
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
		ON DELETE CASCADE
)Type=MyISAM COMMENT='Chaque saisie d\'absence doit faire l\'objet d\'une ligne dans la table a_saisies';

#-----------------------------------------------------------------------------
#-- a_traitements
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS a_traitements;


CREATE TABLE a_traitements
(
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'cle primaire auto-incremente',
	utilisateur_id VARCHAR(100) COMMENT 'Login de l\'utilisateur professionnel qui a fait le traitement',
	created_on INTEGER(13)  NOT NULL COMMENT 'Date du traitement de ou des absences en timestamp UNIX',
	updated_on INTEGER(13)  NOT NULL COMMENT 'Date de la modification du traitement de ou des absences en timestamp UNIX',
	a_type_id INTEGER(4) COMMENT 'cle etrangere du type d\'absence',
	a_motif_id INTEGER(4) COMMENT 'cle etrangere du motif d\'absence',
	a_justification_id INTEGER(4) COMMENT 'cle etrangere de la justification de l\'absence',
	texte_justification VARCHAR(250)  NOT NULL COMMENT 'Texte additionnel à ce traitement',
	a_action_id INTEGER(4) COMMENT 'cle etrangere de l\'action sur ce traitement',
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
		ON DELETE SET NULL,
	INDEX a_traitements_FI_5 (a_action_id),
	CONSTRAINT a_traitements_FK_5
		FOREIGN KEY (a_action_id)
		REFERENCES a_actions (id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies';

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
		ON DELETE SET NULL,
	INDEX j_traitements_saisies_FI_2 (a_traitement_id),
	CONSTRAINT j_traitements_saisies_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Table de jointure entre la saisie et le traitement des absences';

#-----------------------------------------------------------------------------
#-- a_envois
#-----------------------------------------------------------------------------

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
	CONSTRAINT a_envois_FK_1
		FOREIGN KEY (utilisateur_id)
		REFERENCES utilisateurs (login)
		ON DELETE SET NULL,
	INDEX a_envois_FI_2 (id_type_envoi),
	CONSTRAINT a_envois_FK_2
		FOREIGN KEY (id_type_envoi)
		REFERENCES a_type_envois (id)
		ON DELETE SET NULL
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
	CONSTRAINT j_traitements_envois_FK_1
		FOREIGN KEY (a_envoi_id)
		REFERENCES a_envois (id)
		ON DELETE SET NULL,
	INDEX j_traitements_envois_FI_2 (a_traitement_id),
	CONSTRAINT j_traitements_envois_FK_2
		FOREIGN KEY (a_traitement_id)
		REFERENCES a_traitements (id)
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Table de jointure entre le traitement des absences et leur envoi';

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
	valeur DECIMAL  NOT NULL COMMENT 'Nombre de crédits obtenus par l\'eleve',
	mention VARCHAR(255)  NOT NULL COMMENT 'Mention obtenue',
	PRIMARY KEY (id,id_eleve,num_periode,id_groupe),
	INDEX ects_credits_FI_1 (id_eleve),
	CONSTRAINT ects_credits_FK_1
		FOREIGN KEY (id_eleve)
		REFERENCES eleves (id_eleve),
	INDEX ects_credits_FI_2 (id_groupe),
	CONSTRAINT ects_credits_FK_2
		FOREIGN KEY (id_groupe)
		REFERENCES groupes (id)
)Type=MyISAM COMMENT='Objet qui précise le nombre d\'ECTS obtenus par l\'eleve pour un enseignement et une periode donnée';

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
)Type=MyISAM COMMENT='Objet qui précise la mention globale obtenue pour un eleve';

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
)Type=MyISAM COMMENT='Enregistrement d\'archive pour les credits ECTS, dont le rapport n\'est edite qu\'au depart de l\'eleve';

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
)Type=MyISAM COMMENT='Categories de matiere, utilisees pour regrouper des enseignements';

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
		REFERENCES matieres_categories (id),
	INDEX j_matieres_categories_classes_FI_2 (classe_id),
	CONSTRAINT j_matieres_categories_classes_FK_2
		FOREIGN KEY (classe_id)
		REFERENCES classes (id)
)Type=MyISAM COMMENT='Liaison entre categories de matiere et classes';

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
)Type=MyISAM COMMENT='Liste des plugins installes sur ce Gepi';

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
)Type=MyISAM COMMENT='Liste des autorisations pour chaque statut';

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
)Type=MyISAM COMMENT='Items pour construire le menu de ce plug-in';

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
		ON DELETE SET NULL
)Type=MyISAM COMMENT='Preference (cle - valeur) associes à un utilisateur professionnel';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
