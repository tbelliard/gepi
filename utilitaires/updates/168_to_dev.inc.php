<?php
/**
 * Fichier de mise à jour de la version 1.6.8 à la version 1.6.9 par défaut
 *
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
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.9 :</h3>";

/*
// Section d'exemple

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'droits_acces_fichiers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'droits_acces_fichiers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
	id INT(11) unsigned NOT NULL auto_increment,
	fichier VARCHAR( 255 ) NOT NULL ,
	identite VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

// Merci de ne pas enlever le témoin ci-dessous de "fin de section exemple"
// Fin SECTION EXEMPLE
*/

$result .= "<strong>Ajout d'une table 'engagements_pages' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'engagements_pages'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS engagements_pages (
		id int(11) NOT NULL auto_increment COMMENT 'identifiant unique',
		page varchar(255) NOT NULL default '' COMMENT 'Page ou module',
		id_type int(11) NOT NULL COMMENT 'identifiant du type d engagement',
		PRIMARY KEY (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'calendrier_vacances' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'calendrier_vacances'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS calendrier_vacances (
		id int(11) NOT NULL auto_increment,
		nom_calendrier varchar(100) NOT NULL default '',
		debut_calendrier_ts varchar(11) NOT NULL,
		fin_calendrier_ts varchar(11) NOT NULL,
		jourdebut_calendrier date NOT NULL default '0000-00-00',
		heuredebut_calendrier time NOT NULL default '00:00:00',
		jourfin_calendrier date NOT NULL default '0000-00-00',
		heurefin_calendrier time NOT NULL default '00:00:00',
		PRIMARY KEY (id)) 
		ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'entrées de configuration envoi SMS dans la table setting</strong><br />";

// Il faut tenir compte de données déjà saisies dans le module absence 2
$result .= "&nbsp;-> Ajout de l'entrée autorise_envoi_sms dans la table setting<br />";
if (getSettingValue('autorise_envoi_sms')===null) {
	if (getSettingValue('abs2_sms')!==null) $OK=saveSetting('autorise_envoi_sms',getSettingValue('abs2_sms')); else $OK=saveSetting('autorise_envoi_sms','n');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée autorise_envoi_sms existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_prestataire dans la table setting<br />";
if (getSettingValue('sms_prestataire')===null) {
	if (getSettingValue('abs2_sms_prestataire')!==null) $OK=saveSetting('sms_prestataire',strtoupper(getSettingValue('abs2_sms_prestataire')));
	else $OK=saveSetting('sms_prestataire','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_prestataire existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_username dans la table setting<br />";
if (getSettingValue('sms_username')===null) {
	if (getSettingValue('abs2_sms_username')!==null) $OK=saveSetting('sms_username',getSettingValue('abs2_sms_username'));
	else $OK=saveSetting('sms_username','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_username existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_password dans la table setting<br />";
if (getSettingValue('sms_password')===null) {
	if (getSettingValue('abs2_sms_password')!==null) $OK=saveSetting('sms_password',getSettingValue('abs2_sms_password'));
	else $OK=saveSetting('sms_password','');
	if ($OK) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_password existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée sms_identite dans la table setting<br />";
if (getSettingValue('sms_identite')===null) {
	if (saveSetting('sms_identite',getSettingValue('gepiSchoolName'))) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée sms_identite existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nj dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nj')===null) {
if (saveSetting('abs2_afficher_alerte_nj',"y")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nj existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nb_nj dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nb_nj')===null) {
	if (saveSetting('abs2_afficher_alerte_nb_nj',"4")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nb_nj existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée abs2_afficher_alerte_nj_delai dans la table setting<br />";
if (getSettingValue('abs2_afficher_alerte_nj_delai')===null) {
	if (saveSetting('abs2_afficher_alerte_nj_delai',"30")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée abs2_afficher_alerte_nj_delai existe déjà dans la table setting");

$result .= "<strong>Ajout d'une table 'o_orientations' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_orientations'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_orientations (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
commentaire text NOT NULL,
date_orientation datetime NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login_rang (login,rang)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'o_orientations_base' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_orientations_base'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_orientations_base (
id int(11) NOT NULL AUTO_INCREMENT,
titre varchar(255) NOT NULL,
description text NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'o_orientations_mefs' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_orientations_mefs'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_orientations_mefs (
id int(11) NOT NULL AUTO_INCREMENT,
id_orientation int(11) NOT NULL,
mef_code varchar(50) NOT NULL,
PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'o_voeux' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_voeux'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_voeux (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
id_orientation int(11) NOT NULL,
rang int(3) NOT NULL,
date_voeu datetime NOT NULL,
commentaire varchar(255) NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login_rang (login,rang)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout de l'entrée active_mod_orientation dans la table setting<br />";
if (getSettingValue('active_mod_orientation')===null) {
	if (saveSetting('active_mod_orientation',"n")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée active_mod_orientation existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée OrientationNbMaxOrientation dans la table setting<br />";
if (getSettingValue('OrientationNbMaxOrientation')===null) {
	if (saveSetting('OrientationNbMaxOrientation',"3")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée OrientationNbMaxOrientation existe déjà dans la table setting");

$result .= "&nbsp;-> Ajout de l'entrée OrientationNbMaxVoeux dans la table setting<br />";
if (getSettingValue('OrientationNbMaxVoeux')===null) {
	if (saveSetting('OrientationNbMaxVoeux',"3")) $result .= msj_ok("SUCCES !"); else $result .= msj_erreur("ECHEC !");
} else $result .= msj_present("L'entrée OrientationNbMaxVoeux existe déjà dans la table setting");

$result .= "<strong>Ajout d'une table 'o_mef' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_mef'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_mef (
id int(11) NOT NULL AUTO_INCREMENT,
mef_code varchar(50) NOT NULL,
affichage char(1) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY mef_code_affichage (mef_code,affichage)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}


$result .= "<strong>Ajout d'une table 'o_avis' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'o_avis'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS o_avis (
id int(11) NOT NULL AUTO_INCREMENT,
login varchar(50) NOT NULL,
avis varchar(255) NOT NULL,
saisi_par varchar(50) NOT NULL,
PRIMARY KEY (id), UNIQUE KEY login (login)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'type' à la table 'utilisateurs'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM utilisateurs LIKE 'type';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE utilisateurs ADD type varchar(10) NOT NULL default '';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<strong>Ajout d'une table 'matieres_appreciations_acces_eleve' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'matieres_appreciations_acces_eleve';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS matieres_appreciations_acces_eleve (login VARCHAR( 50 ) NOT NULL, periode INT( 11 ) NOT NULL, acces ENUM( 'y', 'n') NOT NULL, UNIQUE KEY login_periode (login,periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'mef_matieres' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mef_matieres';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS mef_matieres (mef_code varchar(50) NOT NULL, code_matiere VARCHAR( 250 ) NOT NULL, code_modalite_elect VARCHAR(6) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'type_prof' à la table 'archivage_disciplines'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM archivage_disciplines LIKE 'type_prof';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE archivage_disciplines ADD type_prof varchar(10) NOT NULL default '' AFTER prof;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_prof' à la table 'archivage_disciplines'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM archivage_disciplines LIKE 'id_prof';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE archivage_disciplines ADD id_prof varchar(255) NOT NULL default '' AFTER prof;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<strong>Ajout d'une table 'nomenclature_modalites_election' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'nomenclature_modalites_election';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS nomenclature_modalites_election (code_modalite_elect VARCHAR( 6 ) NOT NULL, 
	libelle_court VARCHAR(50) NOT NULL, 
	libelle_long VARCHAR(250) NOT NULL,
	PRIMARY KEY ( code_modalite_elect )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'j_groupes_eleves_modalites' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_eleves_modalites';");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_eleves_modalites (id_groupe int(11) NOT NULL, login VARCHAR( 50 ) NOT NULL, code_modalite_elect VARCHAR(6) NOT NULL, UNIQUE KEY id_groupe_login_modalite (id_groupe,login,code_modalite_elect)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'sconet_ele_options' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'sconet_ele_options';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS sconet_ele_options (
						id int(11) unsigned NOT NULL auto_increment, 
						ele_id varchar(10) NOT NULL default '',
						code_matiere varchar(255) NOT NULL default '',
						code_modalite_elect char(1) NOT NULL default '',
						num_option int(2) NOT NULL default '0',
						PRIMARY KEY id (id));");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'rang' à la table 's_types_sanctions2'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM s_types_sanctions2 LIKE 'rang';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE s_types_sanctions2 ADD rang INT(11) NOT NULL default 0 AFTER saisie_prof;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}

	$cpt_sts=1;
	$sql="SELECT * FROM s_types_sanctions2 ORDER BY rang, nature, type;";
	$res_sts=mysqli_query($mysqli, $sql);
	while($lig_sts=mysqli_fetch_object($res_sts)) {
		$sql="UPDATE s_types_sanctions2 SET rang='".$cpt_sts."' WHERE id_nature='".$lig_sts->id_nature."';";
		$update=mysqli_query($mysqli, $sql);
		$cpt_sts++;
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'affiche_vacances_eleresp'");
$res_test = mysqli_num_rows($req_test);
if ($res_test == 0){
	$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO setting SET name='affiche_vacances_eleresp', value='yes';");
	$result .= "Initialisation du paramètre affiche_vacances_eleresp à 'yes': ";
	if($query){
		$result .= msj_ok();
	}
	else{
		$result .= msj_erreur('!');
	}
}

$result .= "&nbsp;-> Ajout d'un champ 'valable' à la table 'a_motifs'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM a_motifs LIKE 'valable';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE a_motifs ADD valable VARCHAR(3) NOT NULL default 'y' AFTER sortable_rank;");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<strong>Ajout d'une table 'ct_tag_type' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'ct_tag_type';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS ct_tag_type (
						id int(11) unsigned NOT NULL auto_increment, 
						nom_tag varchar(255) NOT NULL default '',
						tag_compte_rendu char(1) NOT NULL default 'y',
						tag_devoir char(1) NOT NULL default 'y',
						tag_notice_privee char(1) NOT NULL default 'y',
						drapeau varchar(255) NOT NULL default '',
						PRIMARY KEY id (id));");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'ct_tag' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'ct_tag';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS ct_tag (
						id int(11) unsigned NOT NULL auto_increment, 
						id_ct int(11) unsigned NOT NULL, 
						type_ct char(1) NOT NULL DEFAULT '', 
						id_tag int(11) unsigned NOT NULL, 
						PRIMARY KEY id (id), UNIQUE KEY idct_idtag (id_ct, type_ct, id_tag));");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
		$sql="SELECT * FROM ct_devoirs_entry WHERE special='controle';";
		$res_controle=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_controle)>0) {
			$sql="SELECT * FROM ct_tag_type WHERE nom_tag='controle';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)==0) {
				$sql="INSERT INTO ct_tag_type SET nom_tag='controle', tag_compte_rendu='y', tag_devoir='y', drapeau='images/icons/flag2.gif';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				$id_tag_controle=mysqli_insert_id($GLOBALS['mysqli']);
			}
			else {
				// Normalement, on ne devrait pas passer là
				$lig_controle=mysqli_fetch_object($test);
				$id_tag_controle=$lig_controle->id;
			}

			while($lig_controle=mysqli_fetch_object($res_controle)) {
				$sql="INSERT INTO ct_tag SET id_ct='".$lig_controle->id_ct."', type_ct='t', id_tag='".$id_tag_controle."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
			}
		}
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<strong>Ajout d'une table 'gc_eleves_profils' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'gc_eleves_profils';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS gc_eleves_profils (id int(11) unsigned NOT NULL auto_increment, login VARCHAR( 50 ) NOT NULL , profil enum('GC','C','RAS','B','TB') NOT NULL default 'RAS', PRIMARY KEY ( id )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}


//=================================
//===== éléments de programme =====
//=================================
$result .= "<strong>Création des tables Éléments de programme </strong><br />";

$result .= "→ Ajout d'une table 'matiere_element_programme' :<br />";
$test = sql_query1("SHOW TABLES LIKE 'matiere_element_programme';");
if ($test == -1) {
    $sql = "CREATE TABLE IF NOT EXISTS matiere_element_programme ( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "libelle varchar(255) NOT NULL default '' COMMENT \"Libellé de l'élément de programme\", "
            . "PRIMARY KEY id (id) , UNIQUE KEY libelle (libelle)) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Éléments de programme travaillé' ;";
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$result .= "→ Ajout d'une table 'j_mep_mat' :<br />";  
$test = sql_query1("SHOW TABLES LIKE 'j_mep_mat';");   
if ($test == -1) {   
    $sql = "CREATE TABLE IF NOT EXISTS  j_mep_mat( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "idMat varchar(50) COMMENT 'identifiant unique de la matière', "
            . "idEP int(11)  COMMENT \"identifiant unique de l'élément de programme\", "
            . "PRIMARY KEY id (id) , UNIQUE KEY jointMapMat (idMat, idEP)) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Jointure éléments de programme travaillé ↔ matière' ;";
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$result .= "→ Ajout d'une table 'j_mep_prof' :<br />";  
$test = sql_query1("SHOW TABLES LIKE 'j_mep_prof';");   
if ($test == -1) {   
    $sql = "CREATE TABLE IF NOT EXISTS  j_mep_prof( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "idEP int(11)  COMMENT \"identifiant unique de l'élément de programme\", "
            . "id_prof varchar(50) COMMENT 'identifiant unique du professeur', "
            . "PRIMARY KEY id (id) , UNIQUE KEY jointMapProf (id_prof, idEP)) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Jointure éléments de programme travaillé ↔ enseignant' ;";
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$result .= "→ Ajout d'une table 'j_mep_groupe' :<br />"; 
$test = sql_query1("SHOW TABLES LIKE 'j_mep_groupe';");  
if ($test == -1) {   
    $sql = "CREATE TABLE IF NOT EXISTS  j_mep_groupe( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "idEP int(11)  COMMENT \"identifiant unique de l'élément de programme\", "
            . "idGroupe int(11)  COMMENT 'identifiant du groupe', "
            . "annee varchar(4) COMMENT 'année sur 4 caractères', "
            . "periode int(11) COMMENT 'période sur 4 caractères', "
            . "PRIMARY KEY id (id) , UNIQUE KEY jointGroupe (idEP,idGroupe,annee, periode) ) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Jointure éléments de programme travaillé ↔ groupe enseignement' ;";
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$result .= "→ Ajout d'une table 'j_mep_eleve' :<br />"; 
$test = sql_query1("SHOW TABLES LIKE 'j_mep_eleve';");  
if ($test == -1) {   
    $sql = "CREATE TABLE IF NOT EXISTS  j_mep_eleve( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "idEP int(11)  COMMENT \"identifiant unique de l'élément de programme\", "
            . "idEleve varchar(50) COMMENT 'login élève', "
            . "annee varchar(4) COMMENT 'année sur 4 caractères', "
            . "periode int(11) COMMENT 'période sur 4 caractères', "
            . "PRIMARY KEY id (id) , UNIQUE KEY jointMapProf (idEP , idEleve , annee , periode)) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Jointure éléments de programme travaillé ↔ groupe enseignement' ;";
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$result .= "→ Ajout d'une table 'j_mep_niveau' :<br />";   
$test = sql_query1("SHOW TABLES LIKE 'j_mep_niveau';");  
if ($test == -1) {   
    $sql = "CREATE TABLE IF NOT EXISTS  j_mep_niveau( "
            . "id int(11) unsigned NOT NULL auto_increment COMMENT 'identifiant unique', "
            . "idEP int(11)  COMMENT \"identifiant unique de l'élément de programme\", "
            . "idNiveau varchar(50) COMMENT \"niveau auquel se réfère l'élément\" , "
            . "PRIMARY KEY id (id) , UNIQUE KEY niveau (idEP , idNiveau)) "
            . "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci "
            . "COMMENT 'Jointure éléments de programme travaillé ↔ Niveau' ;";
    //echo $sql;
    $result_inter = traite_requete($sql);
    if ($result_inter == '') {
        $result .= msj_ok("SUCCES !");
    }
    else {
        $result .= msj_erreur("ECHEC !");
    }
} else {
    $result .= msj_present("La table existe déjà");
}

$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME='bullNoSaisieElementsProgrammes'");
$res_test = mysqli_num_rows($req_test);
if ($res_test == 0){
	$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO setting SET name='bullNoSaisieElementsProgrammes', value='no';");
	$result .= "Initialisation du paramètre 'bullNoSaisieElementsProgrammes' à 'no': ";
	if($query){
		$result .= msj_ok();
	}
	else{
		$result .= msj_erreur('!');
	}

	// Dans ce cas, on force l'affichage par défaut de la colonne Elements de Programmes sur les bulletins PDF.
	$sql="SELECT DISTINCT id_model_bulletin, valeur FROM modele_bulletin WHERE nom='nom_model_bulletin';";
	$res_model_bull=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_model_bull)>0) {
		while($lig_model_bull=mysqli_fetch_object($res_model_bull)) {
			$sql="SELECT 1=1 FROM modele_bulletin WHERE id_model_bulletin='".$lig_model_bull->id_model_bulletin."' AND nom='active_colonne_Elements_Programmes';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)==0) {
				$result .= "Affichage par défaut de la colonne Éléments de Programmes dans le modèle de bulletins PDF '<strong>".$lig_model_bull->valeur."</strong>'&nbsp;:<br />";
				$sql="INSERT INTO modele_bulletin SET id_model_bulletin='".$lig_model_bull->id_model_bulletin."', nom='active_colonne_Elements_Programmes', valeur='1';";
				$result_inter = traite_requete($sql);
				if ($result_inter == '') {
					$result .= msj_ok("SUCCES !");
				}
				else {
					$result .= msj_erreur("ECHEC !");
				}
			}

			$sql="SELECT 1=1 FROM modele_bulletin WHERE id_model_bulletin='".$lig_model_bull->id_model_bulletin."' AND nom='largeur_Elements_Programmes';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)==0) {
				$result .= "Initialisation de la largeur de la colonne Éléments de Programmes dans le modèle de bulletins PDF '<strong>".$lig_model_bull->valeur."</strong>' à 50mm&nbsp;:<br />";
				$sql="INSERT INTO modele_bulletin SET id_model_bulletin='".$lig_model_bull->id_model_bulletin."', nom='largeur_Elements_Programmes', valeur='50';";
				$result_inter = traite_requete($sql);
				if ($result_inter == '') {
					$result .= msj_ok("SUCCES !");
				}
				else {
					$result .= msj_erreur("ECHEC !");
				}
			}
		}
	}
}

$sql="show index from notanet_saisie";
//echo "$sql<br />";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==1) {
	$result .= "Suppression d'un index invalide sur la table'notanet_saisie'&nbsp;: ";
	$sql="ALTER TABLE notanet_saisie DROP PRIMARY KEY;";
	//echo "$sql<br />";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$result .= "Création d'un nouvel index sur la table'notanet_saisie'&nbsp;: ";
		$sql="ALTER TABLE notanet_saisie ADD UNIQUE login_id_mat (login, id_mat);";
		//echo "$sql<br />";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
		}
		else {
			$result .= msj_erreur("ECHEC !");
		}
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
}

?>
