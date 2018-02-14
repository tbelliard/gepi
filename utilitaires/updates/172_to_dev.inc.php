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
// ------------------------------------
// Modifications méthode encodage photo
// ------------------------------------

$titre="Encodage des noms de fichier des photos élèves";
$texte="La méthode d'encodage a été modifiée, vérifier qu'il n'y a pas d'incohérence.<br />Voir <a href='./mod_trombinoscopes/trombinoscopes_admin.php#encodage'>Administration du module Trombinoscope</a>";
$destinataire="administrateur";
$mode="statut";
enregistre_infos_actions($titre,$texte,$destinataire,$mode);

$result .= "&nbsp;-> Ajout d'un champ 'encodage_photos_eleves_alea' à la table 'setting'<br />";
$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME='encodage_photos_eleves_alea'");
$res_test = mysqli_num_rows($req_test);
if ($res_test == 0){
	$result .= "Initialisation du paramètre 'encodage_photos_eleves_alea'";
	$sql="INSERT INTO setting SET name='encodage_photos_eleves_alea', value=MD5(UNIX_TIMESTAMP());";
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


$result .= "&nbsp;-> Ajout d'un champ 'encodage_photos_eleves_longueur' à la table 'setting'<br />";
$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME='encodage_photos_eleves_longueur'");
$res_test = mysqli_num_rows($req_test);
if ($res_test == 0){
	$result .= "Initialisation du paramètre 'encodage_photos_eleves_longueur'";
	$sql="INSERT INTO setting SET name='encodage_photos_eleves_longueur', value='10';";
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

// un peu de ménage, si échec des requêtes cela n'a pas d'importance
mysqli_query($GLOBALS["mysqli"], "DELETE FROM setting WHERE NAME='encodage_nom_photo'");
mysqli_query($GLOBALS["mysqli"], "DELETE FROM setting WHERE NAME='alea_nom_photo'");


$result .= "&nbsp;-> Ajout d'un champ 'encodage_photos_1_7_2_vers_1_7_3' à la table 'setting'<br />";
$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME='encodage_photos_1_7_2_vers_1_7_3'");
$res_test = mysqli_num_rows($req_test);
if ($res_test == 0){
	$result .= "Initialisation du paramètre 'encodage_photos_1_7_2_vers_1_7_3' à non : ";
	$sql="INSERT INTO setting SET name='encodage_photos_1_7_2_vers_1_7_3', value='non';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
		$encodage_photos_1_7_2_vers_1_7_3='non';
	}
	else {
		$result .= msj_erreur("ECHEC !");
		$encodage_photos_1_7_2_vers_1_7_3='oui'; // inutile d'aller plus loin
	}
} else {
	$result .= msj_present("Le champ existe déjà");
	$une_ligne=mysqli_fetch_assoc($req_test);
	$encodage_photos_1_7_2_vers_1_7_3=$une_ligne['VALUE'];
}

if ($encodage_photos_1_7_2_vers_1_7_3=='non') {
	
	// Sauvegarde du dossier photos
	$result .= '&nbsp;-> Sauvegarde du dossier photos<br />';
	$retour=cree_zip_archive_avec_msg_erreur('photos');
	if ($retour=='') {
		msj_ok('Le dossiers photos a bien été archivé.');
	}
	else {
		msj_erreur('ECHEC ! '.$retour);
	}
	
	$result .= "&nbsp;-> Mise à jour du champ 'encodage_photos_1_7_2_vers_1_7_3' dans la table 'setting'<br />";
	$sql="UPDATE `setting` SET `value`='oui' WHERE `name`='encodage_photos_1_7_2_vers_1_7_3'";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
}
// ------------------------------------------
// Modifications méthode encodage photo (fin)
// ------------------------------------------
?>
