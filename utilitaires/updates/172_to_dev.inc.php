<?php
/**
 * Fichier de mise à jour de la version 1.7.2 à la version 1.7.3 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.3 :</h3>";

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

$result .= "&nbsp;-> Contrôle du type du champ 'note_sur' de la table 'cc_eval'<br />";
$sql="show fields from cc_eval where field='note_sur';";
$query=mysqli_query($mysqli, $sql);
if (mysqli_num_rows($query)>0) {
	$lig=mysqli_fetch_assoc($query);
	if(strtolower($lig["Type"])!='float(10,1)') {
		$result .= "Correction du type du champ 'note_sur' de la table 'cc_eval'&nbsp;: ";
		$sql="ALTER TABLE cc_eval CHANGE note_sur note_sur FLOAT(10,1) NULL DEFAULT '5';";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= msj_ok("SUCCES !")."<br />";
		}
		else {
			$result .= msj_erreur("ECHEC !")."<br />";
		}
	} else {
		$result .= msj_present("Le champ 'note_sur' a le bon type")."<br />";
	}
} else {
	$result .= msj_erreur("Le champ 'note_sur' n'existe pas")."<br />";
}

$result .= "&nbsp;-> Ajout d'un champ 'login' à la table 's_alerte_mail'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM s_alerte_mail LIKE 'login';"));
if ($test_champ==0) {
	$sql="ALTER TABLE s_alerte_mail ADD login varchar(50) NOT NULL default '' AFTER destinataire;";
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

$result .= "&nbsp;-> Ajout d'une modalité d'accompagnement 'Contrat de réussite'&nbsp;: ";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT 1=1 FROM modalites_accompagnement WHERE code='CTR';"));
if ($test_champ==0) {
	$sql="INSERT INTO modalites_accompagnement SET code='CTR', libelle='Contrat de réussite', avec_commentaire='y';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La modalité existe déjà");
}

$result .= "<strong>Ajout d'une table 'j_groupes_lvr' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'j_groupes_lvr';");
if ($test == -1) {
$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_groupes_lvr (
						id int(11) unsigned NOT NULL auto_increment, 
						id_groupe int(11) NOT NULL,
						code VARCHAR(50) NOT NULL,
						PRIMARY KEY id (id)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
$result .= "<strong>Ajout d'une table 'socle_eleves_lvr' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'socle_eleves_lvr'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE socle_eleves_lvr (id int(11) NOT NULL auto_increment, 
	ine varchar(50) NOT NULL, 
	id_groupe INT(11) NOT NULL, 
	positionnement varchar(10) NOT NULL DEFAULT '', 
	login_saisie varchar(50) NOT NULL DEFAULT '', 
	date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
	PRIMARY KEY (id), INDEX ine_id_groupe (ine, id_groupe), UNIQUE(ine, id_groupe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
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
