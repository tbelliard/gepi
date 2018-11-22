<?php
/**
 * Fichier de mise à jour de la version 1.7.3 à la version 1.7.4 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.4 :</h3>";

/*
// Section d'exemple

// Attention : on peut effectuer des mysqli_query() pour des tests en SELECT,
//             mais toujours utiliser traite_requete() pour les CREATE, ALTER, INSERT, UPDATE
//             pour que le message indiquant qu'il s'est produit une erreur soit affiché en haut de la page (l'admin ne lit pas toute la page;)

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$sql="ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'abs_bull_delais' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'abs_bull_delais'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE abs_bull_delais (periode int(11) NOT NULL default '0', id_classe int(11) NOT NULL default '0', totaux CHAR(1) NOT NULL default 'n', appreciation CHAR(1) NOT NULL default 'n',date_limite TIMESTAMP NOT NULL, mode VARCHAR(100) NOT NULL DEFAULT '', PRIMARY KEY  (periode, id_classe), INDEX id_classe (id_classe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$acces_moy_ele_resp=getSettingValue('acces_moy_ele_resp');
$result .= "&nbsp;-> Initialisation de 'acces_moy_ele_resp' pour l'accès élève/responsable aux moyennes des bulletins&nbsp;: ";
if ($acces_moy_ele_resp=='') {
	$result_inter=saveSetting('acces_moy_ele_resp', 'immediat');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée");
}

$acces_moy_ele_resp_cn=getSettingValue('acces_moy_ele_resp_cn');
$result .= "&nbsp;-> Initialisation de 'acces_moy_ele_resp_cn' pour l'accès élève/responsable aux moyennes des carnets de notes&nbsp;: ";
if ($acces_moy_ele_resp_cn=='') {
	$result_inter=saveSetting('acces_moy_ele_resp_cn', 'immediat');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'b_droits_divers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'b_droits_divers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'commentaires_types_d_apres_moy' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'commentaires_types_d_apres_moy'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS commentaires_types_d_apres_moy (
						id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						login VARCHAR( 255 ) NOT NULL ,
						note_min float(10,2) NOT NULL DEFAULT '0.0' ,
						note_max float(10,2) NOT NULL DEFAULT '20.1' ,
						app TEXT NOT NULL
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

$result .= "&nbsp;-> Ajout d'un champ 'visibilite_eleve' à la table 'aid'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM aid LIKE 'visibilite_eleve';"));
if ($test_champ==0) {
	$sql="ALTER TABLE aid ADD visibilite_eleve ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'b_droits_divers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'b_droits_divers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'mod_actions_categories' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mod_actions_categories'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS mod_actions_categories (
						id INT( 11 ) NOT NULL AUTO_INCREMENT, 
						nom VARCHAR( 255 ) NOT NULL DEFAULT '', 
						description TEXT NOT NULL DEFAULT '', 
						PRIMARY KEY ( id ), 
						UNIQUE KEY ( nom )
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//echo "$sql<br />";
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'mod_actions_gestionnaires' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mod_actions_gestionnaires'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS mod_actions_gestionnaires (
						id INT( 11 ) NOT NULL AUTO_INCREMENT, 
						id_categorie INT( 11 ) NOT NULL DEFAULT '0', 
						login_user VARCHAR( 50 ) NOT NULL DEFAULT '', 
						PRIMARY KEY ( id )
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//echo "$sql<br />";
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'mod_actions_action' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mod_actions_action'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS mod_actions_action (
						id INT( 11 ) NOT NULL AUTO_INCREMENT, 
						id_categorie INT( 11 ) NOT NULL DEFAULT '0', 
						nom VARCHAR( 255 ) NOT NULL DEFAULT '', 
						description TEXT NOT NULL DEFAULT '', 
						date_action DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00', 
						PRIMARY KEY ( id )
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	//echo "$sql<br />";
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'mod_actions_inscriptions' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'mod_actions_inscriptions'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS mod_actions_inscriptions (
						id INT( 11 ) NOT NULL AUTO_INCREMENT, 
						id_action INT( 11 ) NOT NULL DEFAULT '0', 
						login_ele VARCHAR( 50 ) NOT NULL DEFAULT '', 
						presence VARCHAR(10) NOT NULL DEFAULT '', 
						date_pointage DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00', 
						login_pointage VARCHAR( 50 ) NOT NULL DEFAULT '', 
						PRIMARY KEY ( id )
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	//echo "$sql<br />";
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

$terme_mod_action=getSettingValue('terme_mod_action');
$result .= "&nbsp;-> Initialisation de 'terme_mod_action' pour le module Actions&nbsp;: ";
if ($terme_mod_action=='') {
	$result_inter=saveSetting('terme_mod_action', 'Action');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée");
}

$result .= "&nbsp;-> Initialisation de 'mod_actions_affichage_familles'&nbsp;: ";
if (getSettingValue('mod_actions_affichage_familles')=='') {
	$result_inter=saveSetting('mod_actions_affichage_familles', 'y');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée");
}

$result .= "&nbsp;-> Contrôle de l'initialisation des codes et intitulés d'enseignements de complément&nbsp;: ";
$tab_enseignements_complement=array();
// Aucun, c'est si on ne remonte rien.
//$tab_enseignements_complement["AUC"]="Aucun";
$tab_enseignements_complement["LCA"]="Langues et cultures de l'Antiquité";
$tab_enseignements_complement["LCR"]="Langue et culture régionale";
$tab_enseignements_complement["PRO"]="Découverte professionnelle";
$tab_enseignements_complement["LSF"]="Langue des signes française";
$tab_enseignements_complement["LVE"]="Langue vivante étrangère";
$tab_enseignements_complement["CHK"]="Chant Choral";
$tab_enseignements_complement["LCE"]="Langues et cultures européennes";
$temoin_modif_reg_enseignements_complement=0;
foreach($tab_enseignements_complement as $code => $libelle) {
	$sql="SELECT * FROM nomenclatures_valeurs WHERE type='enseignement_complement' AND code='".$code."';";
	$test=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($test)==0) {
		$sql="INSERT INTO nomenclatures_valeurs SET type='enseignement_complement', code='".$code."', nom='".$code."', valeur='".mysqli_real_escape_string($GLOBALS['mysqli'], $libelle)."';";
		$insert=mysqli_query($GLOBALS['mysqli'], $sql);
		if($insert) {
			$result.="<br /><span style='color:green'>Enregistrement du code <strong>$code</strong> pour <em>$libelle</em> effectué.</span>";
		}
		else {
			$result .= msj_erreur("<br /><span style='color:red'>Erreur lors de l'enregistrement du code <strong>$code</strong> pour <em>$libelle</em>.</span>");
		}
		$temoin_modif_reg_enseignements_complement++;
	}
}
if($temoin_modif_reg_enseignements_complement==0) {
	$result.=msj_present("Les valeurs sont déjà correctes");
}
else {
	$result.="<br />";
}

$result .= "&nbsp;-> Ajout d'un champ 'resume' à la table 'aid_config'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM aid_config LIKE 'resume';"));
if ($test_champ==0) {
	$sql="ALTER TABLE aid_config ADD resume text NOT NULL;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
$result .= "&nbsp;-> Ajout d'un champ 'imposer_resume' à la table 'aid_config'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM aid_config LIKE 'imposer_resume';"));
if ($test_champ==0) {
	$sql="ALTER TABLE aid_config ADD imposer_resume CHAR(1) NOT NULL DEFAULT 'n';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

?>
