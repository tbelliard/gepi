<?php
/**
 * Fichier de mise à jour de la version 1.5.3 à la version 1.5.4
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
 * Exemple : $result .= "<font color='gree'>Champ XXX ajouté avec succès</font>";
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.5.4" . $rc . $beta . " :</h3>";

//===================================================
//
//deja mis dans 153_to_1531
//
//$champ_courant=array('nom1', 'prenom1', 'nom2', 'prenom2');
//for($loop=0;$loop<count($champ_courant);$loop++) {
//	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'responsables'<br />";
//	$query = mysql_query("ALTER TABLE responsables CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
//	if ($query) {
//			$result .= msj_ok();
//	} else {
//			$result .= msj_erreur();
//	}
//}
//
//$champ_courant=array('nom', 'prenom');
//for($loop=0;$loop<count($champ_courant);$loop++) {
//	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'resp_pers'<br />";
//	$query = mysql_query("ALTER TABLE resp_pers CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
//	if ($query) {
//			$result .= msj_ok();
//	} else {
//			$result .= msj_erreur();
//	}
//}
//===================================================


// Ajout de paramètres pour l'import d'attributs depuis CAS
// Paramètre d'activation de la synchro à la volée Scribe NG

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_prenom'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_prenom', '');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre cas_attribut_prenom : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre cas_attribut_prenom : Erreur !" );
  }
} else {
  $result .= msj_present("Le paramètre cas_attribut_prenom existe déjà dans la table setting.");
}

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_nom'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_nom', '');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre cas_attribut_nom : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre cas_attribut_nom : Erreur !" );
  }
} else {
  $result .= msj_present("Le paramètre cas_attribut_nom existe déjà dans la table setting.");
}

$req_test=mysql_query("SELECT value FROM setting WHERE name = 'cas_attribut_email'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('cas_attribut_email', '');");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre cas_attribut_email : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre cas_attribut_email : Erreur !" );
  }
} else {
  $result .= msj_present("Le paramètre cas_attribut_email existe déjà dans la table setting.");
}


//===================================================
$result .= "<br /><br /><strong>Ajout d'une table modeles_grilles_pdf :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'modeles_grilles_pdf'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modeles_grilles_pdf (
		id_modele INT(11) NOT NULL auto_increment,
		login varchar(50) NOT NULL default '',
		nom_modele varchar(255) NOT NULL,
		par_defaut ENUM('y','n') DEFAULT 'n',
		PRIMARY KEY (id_modele)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><br /><strong>Ajout d'une table modeles_grilles_pdf_valeurs :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'modeles_grilles_pdf_valeurs'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modeles_grilles_pdf_valeurs (
		id_modele INT(11) NOT NULL,
		nom varchar(255) NOT NULL default '',
		valeur varchar(255) NOT NULL,
		INDEX id_modele_champ (id_modele, nom)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><br /><strong>Ajout d'une table pour les lieux des absences :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'a_lieux'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS a_lieux (
	id INTEGER(11)  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire auto-incrementee',
	nom VARCHAR(250)  NOT NULL COMMENT 'Nom du lieu',
	commentaire TEXT   COMMENT 'commentaire saisi par l\'utilisateur',
	sortable_rank INTEGER,
	PRIMARY KEY (id)
)  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Lieu pour les types d\'absence ou les saisies';");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

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
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'un champ id_lieu à la table 'a_saisies'<br />";
$test_date_decompte=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_saisies LIKE 'id_lieu';"));
if ($test_date_decompte>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE a_saisies ADD id_lieu INTEGER(11) COMMENT 'cle etrangere du lieu ou se trouve l\'eleve' AFTER modifie_par_utilisateur_id,
       ADD INDEX a_saisies_FI_9 (id_lieu),
        ADD CONSTRAINT a_saisies_FK_9
		FOREIGN KEY (id_lieu)
		REFERENCES a_lieux (id)
		ON DELETE SET NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

//===================================
$result .= "<br /><br /><strong>Ajout d'une table pour les contrôles de cours :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_dev'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_dev (id int(11) NOT NULL auto_increment, 
id_cn_dev int(11) NOT NULL default '0',
id_groupe int(11) NOT NULL default '0',
nom_court varchar(32) NOT NULL default '',
nom_complet varchar(64) NOT NULL default '',
description varchar(128) NOT NULL default '',
arrondir char(2) NOT NULL default 's1',
PRIMARY KEY  (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}


$result .= "<br /><strong>Ajout d'une table pour les évaluations des contrôles de cours :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_eval'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_eval (id int(11) NOT NULL auto_increment,
id_dev int(11) NOT NULL default '0',
nom_court varchar(32) NOT NULL default '',
nom_complet varchar(64) NOT NULL default '',
description varchar(128) NOT NULL default '',
date datetime NOT NULL default '0000-00-00 00:00:00',
note_sur int(11) default '5',
PRIMARY KEY  (id),
INDEX dev_date (id_dev, date)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}


$result .= "<br /><strong>Ajout d'une table pour les notes des évaluations des contrôles de cours :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'cc_notes_eval'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE cc_notes_eval ( login varchar(50) NOT NULL default '',
id_eval int(11) NOT NULL default '0',
note float(10,1) NOT NULL default '0.0',
statut char(1) NOT NULL default '',
comment text NOT NULL,
PRIMARY KEY  (login,id_eval)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;->Extension du champ statut de la table 'cc_notes_eval' à 4 caractères : ";
$query = mysql_query("ALTER TABLE cc_notes_eval CHANGE statut statut VARCHAR( 4 ) NOT NULL;");
if ($query) {
        $result .= msj_ok();
} else {
        $result .= msj_erreur();
}
//===================================

$result .= "<br />&nbsp;->Ajout d'un champ 'note_sur' à la table 'eb_epreuves' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM eb_epreuves LIKE 'note_sur';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("alter table eb_epreuves add note_sur int(11) unsigned not null default '20';");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

// A1jout Eric Module discipline génération des exclusion Ooo
$result .= "<br /><strong>Ajout de champs pour la tables s_exclusions du module discipline :</strong><br />";
$result .= "<br />&nbsp;->Ajout d'un champ 'nombre_jours' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'nombre_jours';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` ADD `nombre_jours` VARCHAR( 50 ) NOT NULL ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "<br />&nbsp;->Ajout d'un champ 'qualification_faits' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'qualification_faits';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` ADD `qualification_faits` text NOT NULL ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "<br />&nbsp;->Ajout d'un champ 'num_courrier' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'num_courrier';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` ADD `num_courrier` VARCHAR( 50 ) NOT NULL ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "<br />&nbsp;->Ajout d'un champ 'type_exclusion' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'type_exclusion';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` ADD `type_exclusion` VARCHAR( 50 ) NOT NULL ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "<br />&nbsp;->Supression du champ 'fct_delegation' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'fct_delegation';"));
if ($test_note_sur<0) {
	$result .= msj_present(" Le champ n'existe plus.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` DROP `fct_delegation` ;");
	if ($query) {
			$result .= msj_present(" Le champ n'existe plus.");
	} else {
			$result .= msj_ok();
	}
}


$result .= "<br />&nbsp;->Supression du champ 'fct_autorite' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'fct_autorite';"));
if ($test_note_sur<0) {
	$result .= msj_present("> Le champ n'existe plus.");
}
else {
    $query = mysql_query("ALTER TABLE `s_exclusions` DROP `fct_autorite` ;");
	if ($query) {
			$result .= msj_present(" Le champ n'existe plus.");
	} else {
			$result .= msj_ok();
	}
}


$result .= "<br />&nbsp;->Supression du champ 'nom_autorite' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'nom_autorite';"));
if ($test_note_sur<0) {
	$result .= msj_present("> Le champ n'existe plus.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` DROP `nom_autorite` ;");
	if ($query) {
			$result .= msj_present(" Le champ n'existe plus.");
	} else {
			$result .= msj_ok();
	}
}

$result .= "<br />&nbsp;->Ajout d'un champ 'id_signataire' à la table 's_exclusions' : ";
$test_note_sur=mysql_num_rows(mysql_query("SHOW COLUMNS FROM s_exclusions LIKE 'id_signataire';"));
if ($test_note_sur>0) {
	$result .= msj_present("Le champ existe déjà.");
}
else {
	$query = mysql_query("ALTER TABLE `s_exclusions` ADD `id_signataire` INT NOT NULL ;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "<br /><strong>Ajout d'une table Délégation pour le module discipline :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 's_delegation'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE `s_delegation` (
`id_delegation` INT NOT NULL AUTO_INCREMENT ,
`fct_delegation` VARCHAR( 100 ) NOT NULL ,
`fct_autorite` VARCHAR( 50 ) NOT NULL ,
`nom_autorite` VARCHAR( 50 ) NOT NULL ,
PRIMARY KEY ( `id_delegation` )
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$test = sql_query1("SHOW TABLES LIKE 'mef'");
if ($test == -1) {
	$result .= "<br />Création de la table 'mef'. ";
	$sql="
CREATE TABLE mef
(
	id INTEGER  NOT NULL AUTO_INCREMENT COMMENT 'Cle primaire de la classe',
	ext_id INTEGER   COMMENT 'Numero de la nomenclature officielle (numero MEF)',
	libelle_court VARCHAR(50)  NOT NULL COMMENT 'libelle de la formation',
	libelle_long VARCHAR(300)  NOT NULL COMMENT 'libelle de la formation',
	libelle_edition VARCHAR(300)  NOT NULL COMMENT 'libelle de la formation pour presentation',
	PRIMARY KEY (id)
)  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Module élémentaire de formation';
";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'mef': ".$result_inter."<br />";
	}
}

$sql = "SELECT id_mef FROM eleves LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `eleves` ADD `id_mef` INTEGER   COMMENT 'cle externe pour le jointure avec mef'";
    $req_add_rank = traite_requete($sql_request);
    $sql_request = "ALTER TABLE `eleves` ADD INDEX eleves_FI_1 (id_mef)";
    $req_add_rank_2 = traite_requete($sql_request);
    $sql_request = "ALTER TABLE `eleves` ADD CONSTRAINT eleves_FK_1
		FOREIGN KEY (id_mef)
		REFERENCES mef (id)
		ON DELETE SET NULL";
    $req_add_rank_3 = traite_requete($sql_request);
    if ($req_add_rank == '' && $req_add_rank_2 == '' && $req_add_rank_3 == '') {
        $result .= "<p style=\"color:green;\">Ajout du champ id_mef dans la table <strong>eleves</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ id_mef à la table <strong>eleves</strong> : Erreur. $req_add_rank $req_add_rank_2 $req_add_rank_3</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ id_mef à la table <strong>eleves</strong> : déjà réalisé.</p>";
}


$test = sql_query1("SHOW TABLES LIKE 'infos_actions'");
if ($test == -1) {
	$result .= "<br />Création de la table 'infos_actions'.";
	$sql="CREATE TABLE IF NOT EXISTS infos_actions (id int(11) NOT NULL auto_increment,
titre varchar(255) NOT NULL default '',
description text NOT NULL,
date datetime,
PRIMARY KEY (id),
INDEX id_titre (id, titre)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'infos_actions': ".$result_inter."<br />";
	}
}

$test = sql_query1("SHOW TABLES LIKE 'infos_actions_destinataires'");
if ($test == -1) {
	$result .= "<br />Création de la table 'infos_actions_destinataires'.";
	$sql="CREATE TABLE IF NOT EXISTS infos_actions_destinataires (id int(11) NOT NULL auto_increment,
id_info int(11) NOT NULL,
nature enum('statut', 'individu') default 'individu',
valeur varchar(255) default '',
PRIMARY KEY (id),
INDEX id_info (id_info)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'infos_actions_destinataires': ".$result_inter."<br />";
	}
}


$test = sql_query1("SELECT 1=1 FROM setting WHERE name='mode_email_resp' AND value!='';");
if ($test == -1) {
	$result .= "<br />Vous devez effectuer le paramétrage de la synchronisation des emails responsables et comptes responsables (table 'resp_pers' et 'utilisateurs').";

	$sql="SELECT 1=1 FROM infos_actions WHERE titre='Paramétrage mode_email_resp requis';";
	$test=sql_query1($sql);
	if ($test == -1) {
		$info_action_titre="Paramétrage mode_email_resp requis";
		$info_action_texte="Vous devez effectuer un choix de paramétrage pour la synchronisation des email des responsables&nbsp;: <a href='gestion/param_gen.php#mode_email_resp'>Paramétrage</a>";
		$info_action_destinataire="administrateur";
		$info_action_mode="statut";
		enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
	}
}

$result .= "<br /><strong>Ajout d'une table s_reports pour le module discipline :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 's_reports'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE s_reports (
  id_report int(11) NOT NULL AUTO_INCREMENT,
  id_sanction int(11) NOT NULL,
  id_type_sanction int(11) NOT NULL,
  nature_sanction varchar(255) NOT NULL,
  `date` date NOT NULL,
  informations text NOT NULL,
  motif_report varchar(255) NOT NULL,
  PRIMARY KEY (id_report)
)  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}


$sql = "SELECT date_sortie FROM eleves LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `eleves` ADD `date_sortie` DATETIME COMMENT 'Timestamp de sortie de l\'eleve de l\'etablissement (fin d\'inscription)'";
    $req_add_rank = traite_requete($sql_request);
    if ($req_add_rank == '') {
        $result .= "<p style=\"color:green;\">Ajout du champ date_sortie dans la table <strong>eleves</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ date_sortie à la table <strong>eleves</strong> : Erreur. $req_add_rank</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ date_sortie à la table <strong>eleves</strong> : déjà réalisé.</p>";
}

$sql="SELECT 1=1 FROM setting WHERE name='csrf_mode';";
$res_csrf=mysql_query($sql);
if(mysql_num_rows($res_csrf)==0) {
	$sql="SELECT 1=1 FROM infos_actions WHERE titre='Paramétrage csrf_mode requis';";
	$res_test=mysql_query($sql);
	if(mysql_num_rows($res_test)==0) {
		$result .= "<br /><p style=\"color:blue;\">Paramétrage csrf_mode requis.<br /></p>";
	
		$info_action_titre="Paramétrage csrf_mode requis";
		$info_action_texte="Vous devez effectuer un choix de paramétrage pour la protection contre les attaques CSRF&nbsp;: <a href='gestion/security_policy.php#csrf_mode'>Paramétrage</a>";
		$info_action_destinataire="administrateur";
		$info_action_mode="statut";
		enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
	}
}

$result .= "<br /><strong>Ajout d'une table 'acces_cdt' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'acces_cdt';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS acces_cdt (id INT(11) NOT NULL auto_increment,
					description TEXT NOT NULL,
					chemin VARCHAR(255) NOT NULL DEFAULT '',
					date1 DATETIME NOT NULL default '0000-00-00 00:00:00',
					date2 DATETIME NOT NULL default '0000-00-00 00:00:00',
					PRIMARY KEY (id))  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Ajout d'une table 'acces_cdt_groupes' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'acces_cdt_groupes';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS acces_cdt_groupes (id INT(11) NOT NULL auto_increment,
					id_acces INT(11) NOT NULL,
					id_groupe INT(11) NOT NULL,
					PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
} else {
		$result .= msj_present("La table existe déjà");
}

$result .= "<br /><strong>Ajout d'une table 'vocabulaire' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'vocabulaire';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS vocabulaire (id INT(11) NOT NULL auto_increment,
			terme VARCHAR(255) NOT NULL DEFAULT '',
			terme_corrige VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY (id))  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}

	// A titre d'exemple
	$tab_vocab=array('jute','juste',
						'il peu','il peut',
						'elle peu','elle peut',
						'un peut', 'un peu',
						'trop peut','trop peu',
						'baise','baisse',
						'baisé','baissé',
						'baiser','baisser',
						'courge','courage',
						'camer','calmer',
						'came','calme',
						'camé','calmé',
						'tu est','tu es',
						'tu et','tu es',
						'il et','il est',
						'il es','il est',
						'elle et','elle est',
						'elle es','elle est'
						);
	for($i=0;$i<count($tab_vocab);$i+=2) {
		$sql="insert into vocabulaire set terme='".$tab_vocab[$i]."', terme_corrige='".$tab_vocab[$i+1]."';";
		//$result .= "$sql<br />";
		$result_inter = traite_requete($sql);
		if ($result_inter != '') {
			$result .= msj_erreur("ECHEC : $sql");
		}
	}

} else {
		$result .= msj_present("La table existe déjà");
}

$sql="SELECT 1=1 FROM setting WHERE name='verif_cdt_documents_index';";
$res_cdt=mysql_query($sql);
if(mysql_num_rows($res_cdt)==0) {
	$sql="SELECT 1=1 FROM infos_actions WHERE titre='Contrôle des index dans les documents des CDT requis';";
	$res_test=mysql_query($sql);
	if(mysql_num_rows($res_test)==0) {
		$result .= "<br /><p style=\"color:blue;\">Contrôle des index dans les documents des CDT requis.<br /></p>";
	
		$info_action_titre="Contrôle des index dans les documents des CDT requis";
		$info_action_texte="Il a existé un bug dans la création des fichiers index.html protégeant d'accès anormaux les documents joints aux cahiers de textes.<br />Il est recommandé de lancer une vérification de présence des index&nbsp;: <a href='cahier_texte_admin/index.php?ajout_index_documents=y'>Contrôler</a>";
		$info_action_destinataire="administrateur";
		$info_action_mode="statut";
		enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
		saveSetting('verif_cdt_documents_index', 'fait');
	}
}

$sql = "SELECT ordre_matiere FROM archivage_disciplines LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE archivage_disciplines ADD ordre_matiere SMALLINT( 6 ) NOT NULL;";
    $req_add_rank = traite_requete($sql_request);
    if ($req_add_rank == '') {
        $result .= "<p style=\"color:green;\">Ajout du champ ordre_matiere dans la table <strong>archivage_disciplines</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ ordre_matiere dans la table <strong>archivage_disciplines</strong> : Erreur. $req_add_rank</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ ordre_matiere à la table <strong>archivage_disciplines</strong> : déjà réalisé.</p>";
}

$test = sql_query1("SHOW TABLES LIKE 'tempo3';");
if ($test != -1) {
	// La table existe... est-elle correctement fichue (collision de deux tables tempo3 pendant un temps)
	$sql="show columns from tempo3 like 'col1';";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0) {
		$result .= "<br /><font color=\"red\">ERREUR ! La table 'tempo3' n'a pas la bonne structure.</font><br />";
		$result .= "Suppression de la table mal 'fichue' : ";
		$sql="DROP table tempo3;";
		$menage=mysql_query($sql);
		if(!$menage) {
			$result .= msj_erreur("ECHEC !" );
		}
		else {
			$result .= msj_ok("SUCCES !");
			$result .= "Re-Création de la table 'tempo3' : ";
		}
	}
}
else {
	$result .= "<br /><strong>Ajout d'une table temporaire 'tempo3' :</strong><br />";
}

$test = sql_query1("SHOW TABLES LIKE 'tempo3';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS tempo3 (
		id int(11) NOT NULL auto_increment,
		col1 VARCHAR(255) NOT NULL,
		col2 TEXT,
		PRIMARY KEY  (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
}

$test = sql_query1("SHOW TABLES LIKE 'tempo3_cdt';");
if ($test == -1) {
	$result .= "<br /><strong>Ajout d'une table temporaire 'tempo3_cdt' :</strong><br />";

	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS tempo3_cdt (id_classe int(11) NOT NULL default '0', classe varchar(255) NOT NULL default '', matiere varchar(255) NOT NULL default '', enseignement varchar(255) NOT NULL default '', id_groupe int(11) NOT NULL default '0', fichier varchar(255) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !" );
	}
}


?>
