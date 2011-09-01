<?php
/**
 * Fichier de mise à jour de la version 1.5.4 à la version 1.5.5
 * 
 * $Id$
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
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.5.5" . $rc . $beta . " :</h3>";

//===================================================
/*

// Exemples de sections:

// Ajout d'un champ dans setting

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_prenom'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_prenom', '');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre cas_attribut_prenom : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre cas_attribut_prenom : Erreur !");
  }
} else {
  $result .= msj_present("Le paramètre cas_attribut_prenom existe déjà dans la table setting.");
}

//===================================================

// Ajout d'une table

$result .= "<br /><br /><strong>Ajout d'une table modeles_grilles_pdf :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'modeles_grilles_pdf'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modeles_grilles_pdf (
		id_modele INT(11) NOT NULL auto_increment,
		login varchar(50) NOT NULL default '',
		nom_modele varchar(255) NOT NULL,
		par_defaut ENUM('y','n') DEFAULT 'n',
		PRIMARY KEY (id_modele)
		);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

//===================================================

// Ajout d'un champ à une table

$result .= "&nbsp;->Ajout d'un champ id_lieu à la table 'a_types'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_types LIKE 'id_lieu';"));
if ($test_date_decompte>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE a_types ADD id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve' AFTER commentaire,
       ADD INDEX a_types_FI_1 (id_lieu),
       ADD CONSTRAINT a_types_FK_1
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL ;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur("Erreur");
	}
}

*/
//===================================================

$result .= "<strong>Ajout d'une table 'j_groupes_visibilite' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_visibilite'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_visibilite (
			id INT(11) NOT NULL auto_increment,
			id_groupe INT(11) NOT NULL,
			domaine varchar(255) NOT NULL default '',
			visible varchar(255) NOT NULL default '',
			PRIMARY KEY (id),
			INDEX id_groupe_domaine (id_groupe, domaine)
		);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

//===================================================

$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_documents LIKE 'visible';"));
if ($test_champ>0) {
	$result .= "&nbsp;->Ajout d'un champ visible à la table 'ct_documents'<br />";
	$query = mysql_query("ALTER TABLE ct_documents DROP visible;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'un champ 'visible_eleve_parent' à la table 'ct_documents'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_documents LIKE 'visible_eleve_parent';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE ct_documents ADD visible_eleve_parent BOOLEAN DEFAULT true COMMENT 'Visibilité élève/parent du document joint' AFTER emplacement;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_devoirs_documents LIKE 'visible';"));
if ($test_champ>0) {
	$result .= "&nbsp;->Suppression du champ 'visible' de la table 'ct_devoirs_documents'<br />";
	$query = mysql_query("ALTER TABLE ct_devoirs_documents DROP visible;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'un champ 'visible_eleve_parent' à la table 'ct_devoirs_documents'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_devoirs_documents LIKE 'visible_eleve_parent';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE ct_devoirs_documents ADD visible_eleve_parent BOOLEAN DEFAULT true COMMENT 'Visibilité élève/parent du document joint' AFTER emplacement;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

//===================================================

$result .= "&nbsp;->Ajout d'un champ 'date_visibilite_eleve' à la table 'ct_devoirs_entry'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM ct_devoirs_entry LIKE 'date_visibilite_eleve';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE ct_devoirs_entry ADD date_visibilite_eleve TIMESTAMP NOT NULL default now() COMMENT 'Timestamp precisant quand les devoirs sont portes a la conaissance des eleves' AFTER id_sequence;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

//===================================================

$result .= "<br /><strong>Mots de passe :</strong><br />";
$result .= "&nbsp;->Ajout d'un champ 'salt' à la table 'utilisateur' et allongement du champs password<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM utilisateurs LIKE 'salt';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE utilisateurs ADD salt varchar(128) COMMENT 'sel pour le hmac du mot de passe' AFTER password;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
	$query = mysql_query("ALTER TABLE utilisateurs MODIFY password varchar(128);");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

//===================================================

$result .= "<br /><strong>Messagerie :</strong><br />";
$result .= "&nbsp;->Modification du champ 'destinataires' de la table 'messages' en 'statuts_destinataires'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM messages LIKE 'statuts_destinataires';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ est déjà modifié.");
}
else {
	$query = mysql_query("ALTER TABLE messages CHANGE destinataires statuts_destinataires VARCHAR( 10 ) NOT NULL DEFAULT '';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'un champ 'login_destinataire' à la table 'messages'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM messages LIKE 'login_destinataire';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE messages ADD login_destinataire VARCHAR( 50 ) NOT NULL default '' AFTER statuts_destinataires, ADD INDEX ( login_destinataire ) ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

// Ajout d'index
$result .= "&nbsp;->Ajout de l'index 'login_destinataire' à la table 'messages'<br />";
$req_res=0;
$req_test = mysql_query("SHOW INDEX FROM messages ");
if (mysql_num_rows($req_test)!=0) {
	while ($enrg = mysql_fetch_object($req_test)) {
		if ($enrg-> Key_name == 'login_destinataire') {$req_res++;}
	}
}
if ($req_res == 0) {
	$query = mysql_query("ALTER TABLE messages ADD INDEX login_destinataire ( login_destinataire )");
	if ($query) {
		$result .= msj_ok();
	} else {
		$result .= msj_erreur();
	}
} else {
	$result .= msj_present("L'index existe déjà.");
}

$result .= "<br /><strong>Ajout d'une table 's_travail_mesure' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 's_travail_mesure'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS s_travail_mesure (id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,id_incident INT( 11 ) NOT NULL ,login_ele VARCHAR( 50 ) NOT NULL , travail TEXT NOT NULL);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Absence 2 :</strong><br />";
$result .= "&nbsp;->Ajout des champs versions a la table a_saisies<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies LIKE 'version';"));
if ($test_champ>0) {
	$result .= msj_present("Les versions de saisies existent déjà.");
} else {
	 $query = mysql_query("ALTER TABLE a_saisies ADD (version INTEGER DEFAULT 0, version_created_at DATETIME, version_created_by VARCHAR(100));");
	if ($query) {
                $result .= msj_ok();
				$result .= "&nbsp;->Remplissage des champs version_created_at et version_created_by de la table a_saisies<br />";
				$query = mysql_query("UPDATE
												a_saisies
											SET
												version_created_at = created_at,
												version_created_by = utilisateur_id,
												version = 1
											WHERE
												version = 0");
				if ($query) {
					$result .= msj_ok();
				} else {
				    $result .= msj_erreur();
				}
	} else {
                $result .= msj_erreur();
	}
}


$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'a_saisies_version'"));
if ($test!=0) {
	$result .= msj_present("La table des versions de saisies existent déjà.");
} else {
	$query = mysql_query("CREATE TABLE a_saisies_version
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
) ENGINE=MyISAM;");
	if ($query) {
                $result .= msj_ok();
                
				$result .= "&nbsp;->Remplissage de la table a_saisies_version<br />";
				$query = mysql_query("INSERT INTO a_saisies_version (id,utilisateur_id,eleve_id,commentaire,debut_abs,fin_abs,id_edt_creneau,id_edt_emplacement_cours,id_groupe,id_classe,id_aid,id_s_incidents,id_lieu,created_at,updated_at,version,version_created_at,version_created_by)
									SELECT                           id,utilisateur_id,eleve_id,commentaire,debut_abs,fin_abs,id_edt_creneau,id_edt_emplacement_cours,id_groupe,id_classe,id_aid,id_s_incidents,id_lieu,created_at,updated_at,version,version_created_at,version_created_by FROM a_saisies;");
				if ($query) {
					$result .= msj_ok();
				} else {
	                $result .= msj_erreur();
				}
		
	} else {
                $result .= msj_erreur();
	}
}

$result .= "&nbsp;->Suppression du champs inutile modifie_par_utilisateur_id de la table a_saisies<br />";
$query = mysql_query("ALTER TABLE `a_saisies` DROP `modifie_par_utilisateur_id` ;");
if ($query) {
		$result .= msj_present("Le champ modifie_par_utilisateur_id de la table a_saisies n'existe plus.");
} else {
		$result .= msj_ok();
}

$result .= "&nbsp;->Suppression du champs inutile modifie_par_utilisateur_id de la table a_saisies_version<br />";
$query = mysql_query("ALTER TABLE `a_saisies_version` DROP `modifie_par_utilisateur_id` ;");
if ($query) {
		$result .= msj_present("Le champ modifie_par_utilisateur_id de la table a_saisies_version n'existe plus.");
} else {
		$result .= msj_ok();
}

$result .= "&nbsp;->Ajout d'un champ 'deleted_at' à la table 'a_saisies'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies LIKE 'deleted_at';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_saisies` ADD deleted_at DATETIME AFTER updated_at;");
	if ($query) {
			$result .= msj_present("Le champ deleted_at de la table a_saisies a été ajouté.");
	} else {
			$result .= msj_erreur("Erreur : Le champ deleted_at de la table a_saisies n'a pas été ajouté");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'deleted_at' à la table 'a_saisies_version'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies_version LIKE 'deleted_at';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_saisies_version` ADD deleted_at DATETIME AFTER updated_at;");
	if ($query) {
			$result .= msj_present("Le champ deleted_at de la table a_saisies_version a été ajouté.");
	} else {
			$result .= msj_erreur(": Le champ deleted_at de la table a_saisies_version n'a pas été ajouté");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'deleted_by' à la table 'a_saisies'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies LIKE 'deleted_by';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_saisies` ADD deleted_by VARCHAR(100) AFTER id_lieu;");
	if ($query) {
			$result .= msj_present("Le champ deleted_by de la table a_saisies a été ajouté.");
	} else {
			$result .= msj_erreur(": Le champ deleted_by de la table a_saisies n'a pas été ajouté");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'deleted_by' à la table 'a_saisies_version'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies_version LIKE 'deleted_by';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_saisies_version` ADD deleted_by VARCHAR(100) AFTER id_lieu;");
	if ($query) {
			$result .= msj_present("Le champ deleted_by de la table a_saisies_version a été ajouté.");
	} else {
			$result .= msj_erreur(": Le champ deleted_by de la table a_saisies_version n'a pas été ajouté");
	}
}


$test = mysql_num_rows(mysql_query("SHOW TABLES LIKE 'a_agregation_decompte'"));
if ($test!=0) {
	$result .= msj_present("La table des agrégation de décompte de saisies existe déjà.");
} else {
	$result .= msj_present("Ajout de la table des agrégation de décompte de saisies.");
	$query = mysql_query("CREATE TABLE a_agregation_decompte
(
	eleve_id INTEGER(11) NOT NULL COMMENT 'id de l\'eleve',
	date_demi_jounee DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT 'Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi',
	manquement_obligation_presence TINYINT DEFAULT 0 COMMENT 'Cette demi journée est comptée comme absence',
	justifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une justification',
	notifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une notification à la famille',
	nb_retards INTEGER DEFAULT 0 COMMENT 'Nombre de retards décomptés dans la demi journée',
	nb_retards_justifies INTEGER DEFAULT 0 COMMENT 'Nombre de retards justifiés décomptés dans la demi journée',
	motifs_absences TEXT COMMENT 'Liste des motifs (table a_motifs) associés à cette demi-journée d\'absence',
	motifs_retards TEXT COMMENT 'Liste des motifs (table a_motifs) associés aux retard de cette demi-journée',
	created_at DATETIME,
	updated_at DATETIME,
	PRIMARY KEY (eleve_id,date_demi_jounee),
	CONSTRAINT a_agregation_decompte_FK_1
		FOREIGN KEY (eleve_id)
		REFERENCES eleves (id_eleve)
		ON DELETE CASCADE
) ENGINE=MyISAM COMMENT='Table d\'agregation des decomptes de demi journees d\'absence et de retard';
	");
	if ($query) {
                $result .= msj_ok();
	} else {
                $result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'un champ 'deleted_at' à la table 'a_traitements'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_traitements LIKE 'deleted_at';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_traitements` ADD deleted_at DATETIME AFTER updated_at;");
	if ($query) {
			$result .= msj_present("Le champ deleted_at de la table a_traitements a été ajouté.");
	} else {
			$result .= msj_erreur(": Le champ deleted_at de la table a_traitements n'a pas été ajouté");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'created_at' et 'updated_at' à la table 'a_types'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_types LIKE 'created_at';"));
if ($test_champ>0) {
	$result .= msj_present("Les champs existent déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_types` ADD (created_at DATETIME, updated_at DATETIME);");
	if ($query) {
			$result .= msj_present("Les champs ont étés ajoutés.");
			$query = mysql_query("UPDATE a_types SET created_at = NOW(), updated_at = NOW();");
	} else {
			$result .= msj_erreur(": Les champ created_at' et 'updated_at' de la table a_types n'ont pas étés ajoutés");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'created_at' et 'updated_at' à la table 'a_motifs'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_motifs LIKE 'created_at';"));
if ($test_champ>0) {
	$result .= msj_present("Les champs existent déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_motifs` ADD (created_at DATETIME, updated_at DATETIME);");
	if ($query) {
			$result .= msj_present("Les champs ont étés ajoutés.");
			$query = mysql_query("UPDATE a_motifs SET created_at = NOW(), updated_at = NOW();");
	} else {
			$result .= msj_erreur(": Les champ created_at' et 'updated_at' de la table a_motifs n'ont pas étés ajoutés");
	}
}

$result .= "&nbsp;->Ajout d'un champ 'created_at' et 'updated_at' à la table 'a_justifications'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_justifications LIKE 'created_at';"));
if ($test_champ>0) {
	$result .= msj_present("Les champs existent déjà.");
} else {
	$query = mysql_query("ALTER TABLE `a_justifications` ADD (created_at DATETIME, updated_at DATETIME);");
	if ($query) {
			$result .= msj_present("Les champs ont étés ajoutés.");
			$query = mysql_query("UPDATE a_justifications SET created_at = NOW(), updated_at = NOW();");
	} else {
			$result .= msj_erreur(": Les champ created_at' et 'updated_at' de la table a_justifications n'ont pas étés ajoutés");
	}
}

$result .= add_index('a_saisies','a_saisies_I_1','`deleted_at`');
$result .= add_index('a_saisies','a_saisies_I_2','`debut_abs`');
$result .= add_index('a_saisies','a_saisies_I_3','`fin_abs`');
$result .= add_index('a_traitements','a_traitements_I_1','`deleted_at`');

$result .= "<br /><strong>Ajout d'une table mentions :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mentions'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS mentions (
	id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	mention VARCHAR(255) NOT NULL);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Ajout d'une table j_mentions_classes :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_mentions_classes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_mentions_classes (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_mention INT(11) NOT NULL ,
id_classe INT(11) NOT NULL ,
ordre TINYINT(4) NOT NULL);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;->Ajout d'un champ 'id_mention' à la table 'avis_conseil_classe'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM avis_conseil_classe LIKE 'id_mention';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE avis_conseil_classe ADD id_mention INT( 11 ) NOT NULL DEFAULT '0' AFTER avis;");
	if ($query) {
		$result .= msj_present("Le champ id_mention de la table avis_conseil_classe a été ajouté.");
	} else {
		$result .= msj_erreur(": Le champ id_mention de la table avis_conseil_classe n'a pas été ajouté");
	}
}

$result .= "<br /><strong>Ajout d'une table s_natures :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 's_natures'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS s_natures ( id INT(11) NOT NULL auto_increment, nature varchar(50) NOT NULL default '', PRIMARY KEY (id));");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;->Ajout d'un champ 'id_categorie' à la table 's_natures'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_natures LIKE 'id_categorie';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("alter table s_natures add id_categorie int(11) not null default '0' after nature;");
	if ($query) {
		$result .= msj_present("Le champ id_categorie de la table s_natures a été ajouté.");
	} else {
		$result .= msj_erreur(": Le champ id_categorie de la table s_natures n'a pas été ajouté");
	}
}

$result .= "<br /><strong>Ajout d'une table udt_lignes :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'udt_lignes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS udt_lignes (
id INT(11) unsigned NOT NULL auto_increment,
division varchar(255) NOT NULL default '',
matiere varchar(255) NOT NULL default '',
prof varchar(255) NOT NULL default '',
groupe varchar(255) NOT NULL default '',
regroup varchar(255) NOT NULL default '',
mo varchar(255) NOT NULL default '', 
PRIMARY KEY id (id)
);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Ajout d'une table udt_corresp :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'udt_corresp'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS udt_corresp (
champ varchar(255) NOT NULL default '',
nom_udt varchar(255) NOT NULL default '',
nom_gepi varchar(255) NOT NULL default ''
);");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Mef :</strong><br />";
$result .= "&nbsp;->Modification du champ 'ext_id' de la table 'mef' en 'mef_code'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM mef LIKE 'mef_code';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ est déjà modifié.");
}
else {
	$query = mysql_query("ALTER TABLE mef CHANGE ext_id mef_code INTEGER COMMENT 'code mef de la formation de l\'eleve';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}
$result .= "&nbsp;->Modification du champ 'id_mef' de la table 'eleves' en 'mef_code'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM eleves LIKE 'mef_code';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ est déjà modifié.");
}
else {
	$query = mysql_query("ALTER TABLE eleves CHANGE id_mef mef_code INTEGER COMMENT 'code mef de la formation de l\'eleve';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Extension du 'mef_code' de la table 'eleves' en BIGINT(20) : ";
$result.="<br />";
$query = mysql_query("ALTER TABLE eleves CHANGE mef_code mef_code BIGINT( 20 ) NULL DEFAULT NULL COMMENT 'code mef de la formation de l''eleve';");
if ($query) {
		$result .= msj_ok();
} else {
		$result .= msj_erreur();
}

$result .= "&nbsp;->Extension du 'mef_code' de la table 'mef' en BIGINT(20) : ";
$result.="<br />";
$query = mysql_query("ALTER TABLE mef CHANGE mef_code mef_code BIGINT( 20 ) NULL DEFAULT NULL COMMENT 'code mef de la formation de l''eleve';");
if ($query) {
		$result .= msj_ok();
} else {
		$result .= msj_erreur();
}

$result .= "<br />&nbsp;->Ajout d'un champ 'nom_requete' à la table 'gc_affichages'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM gc_affichages LIKE 'nom_requete';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ existe déjà.");
} else {
	$query = mysql_query("ALTER TABLE gc_affichages ADD nom_requete VARCHAR( 255 ) NOT NULL AFTER projet;");
	if ($query) {
		$result .= msj_present("Le champ 'nom_requete' de la table 'gc_affichages' a été ajouté.");
	} else {
		$result .= msj_erreur(": Le champ 'nom_requete' de la table 'gc_affichages' n'a pas été ajouté");
	}
}


$req_test=mysql_query("SELECT value FROM setting WHERE name = 'sso_cas_table'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('sso_cas_table', 'no');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre sso_cas_table : Ok !");
  } else {
    $result.=msj_erreur(": Définition du paramètre sso_cas_table !");
  }
} else {
  $result .= msj_present("Le paramètre sso_cas_table existe déjà dans la table setting.");
}

//$result.="<br />";
$result.="Définition du champ 'reglage' de la table 'edt_setting' comme UNIQUE :";
$result.="<br />";
$deja_cree = deja_unique('edt_setting','reglage' );
if (!$deja_cree) {
  $result_inter = traite_requete("ALTER TABLE edt_setting ADD UNIQUE (reglage);");
  if ($result_inter == '') {
      $result.=msj_ok(" Ok !");
  } else {
      $result.=msj_erreur(" ! ".$result_inter);
  }
} else {
  $result.= msj_present(' le champ est déjà défini comme UNIQUE');
}

$result.="Définition du champ 'ref' de la table 'ref_wiki' comme UNIQUE :";
$result.="<br />";
$deja_cree = deja_unique('ref_wiki','ref' );
if (!$deja_cree) {
  $result_inter = traite_requete("ALTER TABLE ref_wiki ADD UNIQUE (ref);");
  if ($result_inter == '') {
      $result.=msj_ok(" Ok !");
  } else {
      $result.=msj_erreur(" !".$result_inter);
  }
} else {
  $result.= msj_present(' le champ est déjà défini comme UNIQUE');
}

//$result.="<br />";
$result.="Extension du champ 'description' de la table 'infos_actions' en LONGTEXT :";
$result.="<br />";
$result_inter = traite_requete("ALTER TABLE infos_actions CHANGE description description LONGTEXT NOT NULL;");
if ($result_inter == '') {
	$result.=msj_ok(" Ok !");
} else {
	$result.=msj_erreur(" !".$result_inter);
}

$test_file = '../lib/global.inc';
if (file_exists($test_file)) { 
    $result.="<br />";
    $result.="Tentative de Suppression du fichier global.inc obsolète :";
    unlink($test_file);
    if (file_exists($test_file)){
       $result.=msj_erreur(" !");
    }else{
       $result.=msj_ok(" Réussi !"); 
    }
}
$result.="<br />";
$result.="<strong>Module sso_table :</strong>";
$test = sql_query1("SHOW TABLES LIKE 'plugin_sso_table_import'");
if ($test != -1) {
    $result.="Renommage de la table plugin_sso_table_import :";
    $result_inter = traite_requete("RENAME TABLE `plugin_sso_table_import` TO `sso_table_import`;");
    if ($result_inter == '') {
        $result.=msj_ok(" Ok !");
    } else {
        $result.=msj_erreur(" !" . $result_inter);
    }
}
$test = sql_query1("SHOW TABLES LIKE 'plugin_sso_table'");
if ($test != -1) {
    $result.="<br />";
    $result.="Renommage de la table plugin_sso_table :";
    $result_inter = traite_requete("RENAME TABLE `plugin_sso_table` TO `sso_table_correspondance`;");
    if ($result_inter == '') {
        $result.=msj_ok(" Ok !");
    } else {
        $result.=msj_erreur(" !" . $result_inter);
    }
    $result.="<br />";
    $result.="Désinstallation du plugin_sso_table :";
    $result_inter = traite_requete("DELETE FROM `plugins` WHERE `nom`='plugin_sso_table';");
    if ($result_inter == '') {
        $result.=msj_ok(" Ok !");
    } else {
        $result.=msj_erreur(" !" . $result_inter);
    }
}

$test = sql_query1("SHOW TABLES LIKE 'sso_table_correspondance'");
if ($test == -1) {
    $result.="<br />";
    $result.="Création de la table sso_table_correspondance :";
    $result_inter = traite_requete("CREATE TABLE IF NOT EXISTS `sso_table_correspondance` ( `login_gepi` varchar(100) NOT NULL
                default '', `login_sso` varchar(100) NOT NULL
                default '', PRIMARY KEY (`login_gepi`) )
                ENGINE=MyISAM;");
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    } else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result.="<br />";
    $result.="La table de correspondance existe déja.";
}
?>