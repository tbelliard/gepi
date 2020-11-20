<?php
/**
 * Fichier de mise à jour de la version 1.7.4 à la version 1.7.5 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.5 :</h3>";

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
$result .= "<strong>Ajout d'une table 'bull_mail' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'bull_mail'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS bull_mail (
		id INT(11) unsigned NOT NULL auto_increment,
		login_sender VARCHAR(50) NOT NULL DEFAULT '',
		pers_id VARCHAR(10) NOT NULL DEFAULT '',
		email VARCHAR(100) NOT NULL DEFAULT '',
		login_ele VARCHAR(50) NOT NULL DEFAULT '',
		nom_prenom_ele VARCHAR(255) NOT NULL DEFAULT '',
		id_classe INT(11) unsigned NOT NULL DEFAULT '0',
		periodes VARCHAR(100) NOT NULL DEFAULT '',
		id_envoi VARCHAR(255) NOT NULL DEFAULT '',
		envoi VARCHAR(50) NOT NULL DEFAULT '',
		date_envoi DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
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

$result .= "&nbsp;-> Ajout d'un champ 'id_aid' à la table 'matieres_app_corrections'&nbsp;: ";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM matieres_app_corrections LIKE 'id_aid';"));
if ($test_champ==0) {
	$sql="ALTER TABLE matieres_app_corrections ADD id_aid INT(11) NOT NULL default '0' AFTER id_groupe;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$result .= "&nbsp;-> Suppression de la clé primaire sur la table 'matieres_app_corrections'&nbsp;: ";
		$sql="alter table matieres_app_corrections drop primary key;";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
			$result .= "&nbsp;-> Création d'une nouvelle clé primaire sur la table 'matieres_app_corrections'&nbsp;: ";
			$sql="alter table matieres_app_corrections add primary key login_periode_grp_aid(login,periode,id_groupe,id_aid);";
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
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_aid' à la table 'matieres_app_delais'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM matieres_app_delais LIKE 'id_aid';"));
if ($test_champ==0) {
	$sql="ALTER TABLE matieres_app_delais ADD id_aid INT(11) NOT NULL default '0' AFTER id_groupe;";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$result .= "&nbsp;-> Suppression de la clé primaire sur la table 'matieres_app_delais'&nbsp;: ";
		$sql="alter table matieres_app_delais drop primary key;";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !");
			$result .= "&nbsp;-> Création d'une nouvelle clé primaire sur la table 'matieres_app_delais'&nbsp;: ";
			$sql="alter table matieres_app_delais add primary key periode_grp_aid(periode,id_groupe,id_aid);";
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
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_aid' à la table 'acces_exceptionnel_matieres_notes'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM acces_exceptionnel_matieres_notes LIKE 'id_aid';"));
if ($test_champ==0) {
	$sql="ALTER TABLE acces_exceptionnel_matieres_notes ADD id_aid INT(11) NOT NULL default '0' AFTER id_groupe;";
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


$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'nom' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE nom nom VARCHAR(50) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");

	$sql="UPDATE resp_pers SET nom='' WHERE nom IS NULL;";
	//echo "$sql<br />";
	$correction=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'prenom' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE prenom prenom VARCHAR(50) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");

	$sql="UPDATE resp_pers SET prenom='' WHERE prenom IS NULL;";
	//echo "$sql<br />";
	$correction=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_pers' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_pers tel_pers VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");

	$sql="UPDATE resp_pers SET tel_pers='' WHERE tel_pers IS NULL;";
	//echo "$sql<br />";
	$correction=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_port' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_port tel_port VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");

	$sql="UPDATE resp_pers SET tel_port='' WHERE tel_port IS NULL;";
	//echo "$sql<br />";
	$correction=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_prof' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_prof tel_prof VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");

	$sql="UPDATE resp_pers SET tel_prof='' WHERE tel_prof IS NULL;";
	//echo "$sql<br />";
	$correction=mysqli_query($GLOBALS["mysqli"], $sql);
}
else {
	$result .= msj_erreur("ECHEC !");
}

$tab_champs_a_corriger=array('no_gep'=>50, 
'nom'=>100,
'prenom'=>100,
'sexe'=>1,
'elenoet'=>50, 
'ereno'=>50, 
'lieu_naissance'=>50,
'email'=>255,
'tel_pers'=>255, 
'tel_port'=>255, 
'tel_prof'=>255, 
'mef_code'=>50,
'adr_id'=>10);
foreach($tab_champs_a_corriger as $key => $value) {
	$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ '".$key."' de la table 'eleves'&nbsp;: ";
	$sql="ALTER TABLE eleves CHANGE ".$key." ".$key." VARCHAR(".$value.") NOT NULL DEFAULT '';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");

		$sql="UPDATE eleves SET ".$key."='' WHERE ".$key." IS NULL;";
		//echo "$sql<br />";
		$correction=mysqli_query($GLOBALS["mysqli"], $sql);
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
}


$tab_champs_a_corriger=array();
$tab_champs_a_corriger['engagements_droit_saisie']=array('id_engagement' => "INT(11) NOT NULL DEFAULT '0'|0");
$tab_champs_a_corriger['edt_cours_remplacements']=array('id_groupe' => "varchar(10) NOT NULL DEFAULT ''|", 
'id_aid' => "varchar(10) NOT NULL DEFAULT ''|", 
'id_salle' => "varchar(3) NOT NULL DEFAULT ''|", 
'jour_semaine' => "varchar(10) NOT NULL DEFAULT ''|", 
'id_definie_periode' => "varchar(3) NOT NULL DEFAULT ''|", 
'login_prof' => "varchar(50) NOT NULL DEFAULT ''|", 
'id_absence' => "int(11) NOT NULL DEFAULT '0'|0");

$tab_champs_a_corriger['socle_eleves_composantes']=array('ine' => "varchar(50) NOT NULL DEFAULT ''|", 
'cycle' =>"tinyint(2) NOT NULL DEFAULT '0'|0");
$tab_champs_a_corriger['socle_eleves_syntheses']=array('ine' => "varchar(50) NOT NULL DEFAULT ''|", 
'cycle' =>"tinyint(2) NOT NULL DEFAULT '0'|0");

$tab_champs_a_corriger['j_groupes_enseignements_complement']=array('id_groupe' => "int(11) NOT NULL DEFAULT '0'|0",
'code' => "VARCHAR(50) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['socle_eleves_enseignements_complements']=array('id_groupe' => "int(11) NOT NULL DEFAULT '0'|0",
'ine' => "varchar(50) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['elements_programmes']=array('cycle' => "TINYINT(1) NOT NULL DEFAULT '0'|0", 
'matiere' => "VARCHAR(255) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['j_groupes_lvr']=array('id_groupe' => "int(11) NOT NULL DEFAULT '0'|0",
'code' => "VARCHAR(50) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['socle_eleves_lvr']=array('ine' => "varchar(50) NOT NULL DEFAULT ''|", 
'id_groupe' => "INT(11) NOT NULL DEFAULT '0'|0");

$tab_champs_a_corriger['ele_adr']=array('adr_id' => "varchar(10) NOT NULL DEFAULT ''|", 
'adr1' => "varchar(100) NOT NULL DEFAULT ''|", 
'adr2' => "varchar(100) NOT NULL DEFAULT ''|", 
'adr3' => "varchar(100) NOT NULL DEFAULT ''|", 
'adr4' => "varchar(100) NOT NULL DEFAULT ''|", 
'cp' => "varchar(6) NOT NULL DEFAULT ''|", 
'pays' => "varchar(50) NOT NULL DEFAULT ''|", 
'commune' => "varchar(50) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['a_droits']=array('login' => "varchar(50) NOT NULL DEFAULT ''|", 
'page' => "varchar(255) NOT NULL DEFAULT ''|");

$tab_champs_a_corriger['commentaires_types_d_apres_moy']=array('login' => "VARCHAR( 50 ) NOT NULL DEFAULT ''");


foreach($tab_champs_a_corriger as $table => $corrections) {
	$sql="SHOW TABLES LIKE '".$table."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)>0) {
		foreach($corrections as $champ => $description_champ) {
			$tmp_tab=array();
			$tmp_tab=explode('|', $description_champ);
			if(!isset($tmp_tab[1])) {
				$tmp_tab[1]='';
			}
			$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ '".$champ."' de la table '$table'&nbsp;: ";
			$sql="ALTER TABLE ".$table." CHANGE ".$champ." ".$champ." ".$tmp_tab[0].";";
			//$result.="$sql<br />";
			$result_inter = traite_requete($sql);
			if ($result_inter == '') {
				$result .= msj_ok("SUCCES !");

				$sql="UPDATE ".$table." SET ".$champ."='".$tmp_tab[1]."' WHERE ".$champ." IS NULL;";
				//echo "$sql<br />";
				$correction=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else {
				$result .= msj_erreur("ECHEC !");
			}
		}
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'socle_eleves_composantes_groupes'&nbsp;:</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_composantes_groupes'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS socle_eleves_composantes_groupes (id int(11) NOT NULL auto_increment, 
		ine varchar(50) NOT NULL DEFAULT '', 
		cycle tinyint(2) NOT NULL DEFAULT '0', 
		annee varchar(10) NOT NULL default '', 
		code_composante varchar(10) NOT NULL DEFAULT '', 
		niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
		id_groupe INT(11) NOT NULL default '0', 
		periode INT(11) NOT NULL default '1', 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), INDEX ine_cycle_id_composante_id_groupe_periode (ine, cycle, code_composante, id_groupe, periode, annee), UNIQUE(ine, cycle, code_composante, id_groupe, periode, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$SocleSaisieComposantesMode=getSettingValue("SocleSaisieComposantesMode");
if($SocleSaisieComposantesMode=='') {
	$result .= "&nbsp;-> Initialisation de la valeur de 'SocleSaisieComposantesMode'&nbsp;: ";
	if(!saveSetting('SocleSaisieComposantesMode', 1)) {
		$result .= msj_erreur("ECHEC !");
	}
	else {
		$result .= msj_ok("SUCCES !");
	}
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'modules_restrictions'&nbsp;:</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'modules_restrictions'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS modules_restrictions 
		(id int(11) NOT NULL auto_increment, 
		module varchar(50) NOT NULL DEFAULT '', 
		name varchar(50) NOT NULL DEFAULT '', 
		value varchar(50) NOT NULL DEFAULT '', 
		PRIMARY KEY (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_eleves_competences_numeriques'&nbsp;:</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_competences_numeriques'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS socle_eleves_competences_numeriques (id int(11) NOT NULL auto_increment, 
		ine varchar(50) NOT NULL DEFAULT '', 
		cycle tinyint(2) NOT NULL DEFAULT '0', 
		annee varchar(10) NOT NULL DEFAULT '',
		code_competence varchar(10) NOT NULL DEFAULT '', 
		niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
		periode INT(11) NOT NULL default '1', 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), INDEX ine_cycle_id_competence_periode (ine, cycle, code_competence, periode), UNIQUE(ine, cycle, code_competence, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_eleves_syntheses_numeriques'&nbsp;:</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_syntheses_numeriques'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS socle_eleves_syntheses_numeriques (id int(11) NOT NULL auto_increment, 
		ine varchar(50) NOT NULL, 
		cycle tinyint(2) NOT NULL, 
		annee varchar(10) NOT NULL DEFAULT '',
		periode INT(11) NOT NULL default '1', 
		synthese TEXT, 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), UNIQUE(ine, cycle, annee, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_classes_syntheses_numeriques'&nbsp;:</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_classes_syntheses_numeriques'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS socle_classes_syntheses_numeriques (id int(11) NOT NULL auto_increment, 
		id_classe int(11) NOT NULL, 
		classe varchar(50) NOT NULL, 
		annee varchar(10) NOT NULL DEFAULT '',
		synthese TEXT, 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), UNIQUE(id_classe, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'id_sacoche' à la table 'eleves'&nbsp;: ";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'id_sacoche';"));
if ($test_champ==0) {
	$sql="ALTER TABLE eleves ADD id_sacoche INT(11) NOT NULL default '0' AFTER adr_id;";
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
