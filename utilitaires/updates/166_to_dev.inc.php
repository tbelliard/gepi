<?php
/**
 * Fichier de mise à jour de la version 1.6.6 à la version 1.6.7 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.7 :</h3>";

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

$result .= "&nbsp;-> Ajout d'un champ 'texte_apres_ele_resp' à la table 'd_dates_evenements'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM d_dates_evenements LIKE 'texte_apres_ele_resp';"));
if ($test_champ==0) {
	$query = mysqli_query($mysqli, "ALTER TABLE d_dates_evenements ADD texte_apres_ele_resp TEXT NOT NULL default '';");
	if ($query) {
		$result .= msj_ok("Ok !");

		$sql="SELECT * FROM d_dates_evenements;";
		$res = mysqli_query($mysqli, $sql);
		if($res) {
			while($lig=mysqli_fetch_object($res)) {
				$sql="UPDATE d_dates_evenements SET texte_apres_ele_resp='".mysqli_real_escape_string($GLOBALS['mysqli'], $lig->texte_apres)."' WHERE id_ev='".$lig->id_ev."';";
				//$sql="UPDATE d_dates_evenements SET texte_apres_ele_resp='".$lig->texte_apres."' WHERE id_ev='".$lig->id_ev."';";
				//$result.="$sql<br />";
				$update = mysqli_query($mysqli, $sql);
				if(!$update) {
					$result.=msj_erreur("ERREUR lors la mise à jour du champ 'texte_apres_ele_resp' de l'<a href='../classes/dates_classes.php?id_ev=".$lig->id_ev."' title=\"Consulter dans un nouvel onglet.\" target='_blank'>événément n°".$lig->id_ev."</a><br />");
				}
			}
		}
	} else {
		$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'sp_saisies' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'sp_saisies'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS sp_saisies (
id int(11) NOT NULL AUTO_INCREMENT,
id_type int(11) NOT NULL,
login VARCHAR(50) NOT NULL default '',
date_sp datetime NOT NULL default '0000-00-00 00:00:00',
commentaire text NOT NULL,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_by VARCHAR(50) NOT NULL DEFAULT '',
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'sp_types_saisies' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'sp_types_saisies'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS sp_types_saisies (
id_type int(11) NOT NULL AUTO_INCREMENT,
nom VARCHAR(255) NOT NULL default '',
description TEXT NOT NULL,
rang int(11) NOT NULL,
PRIMARY KEY (id_type)
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

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'sp_seuils' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'sp_seuils'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS sp_seuils (
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

?>
