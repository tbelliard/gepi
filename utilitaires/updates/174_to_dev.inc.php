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
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'prenom' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE prenom prenom VARCHAR(50) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_pers' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_pers tel_pers VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_port' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_port tel_port VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

$result .= "&nbsp;-> Contrôle de la valeur par défaut du champ 'tel_prof' de la table 'resp_pers'&nbsp;: ";
$sql="ALTER TABLE resp_pers CHANGE tel_prof tel_prof VARCHAR(255) NOT NULL DEFAULT '';";
$result_inter = traite_requete($sql);
if ($result_inter == '') {
	$result .= msj_ok("SUCCES !");
}
else {
	$result .= msj_erreur("ECHEC !");
}

?>
