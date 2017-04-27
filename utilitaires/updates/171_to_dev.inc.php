<?php
/**
 * Fichier de mise à jour de la version 1.7.1 à la version 1.7.2 par défaut
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.2 :</h3>";

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

$sql="SELECT * FROM preferences GROUP BY login,name HAVING COUNT(login)>1;";
//echo "$sql<br />\n";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	$result .= "<br />";
	$result .= "<span style='color:red'>Des préférences utilisateurs sont en doublon.<br />Un <a href='../utilitaires/clean_tables.php?maj=controle_preferences".add_token_in_url()."'>nettoyage des tables</a> est nécessaire.</span><br />";

	$info_action_titre="Préférences utilisateurs en doublon";
	$info_action_texte="Des préférences utilisateurs sont en doublon.<br />Un <a href='./utilitaires/clean_tables.php#controle_preferences'>nettoyage des tables</a> est nécessaire.";
	$info_action_destinataire=array("administrateur");
	$info_action_mode="statut";
	enregistre_infos_actions($info_action_titre,$info_action_texte,$info_action_destinataire,$info_action_mode);
}
else {
	$sql="SHOW INDEX FROM preferences WHERE Key_name='PRIMARY';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		$sql="ALTER TABLE preferences ADD PRIMARY KEY ( login , name );";
		$result_inter = traite_requete($sql);
		if ($result_inter == '') {
			$result .= "<br />";
			$result .= "Ajout d'une clé primaire sur la table 'preferences'&nbsp;:".msj_ok("SUCCES !")."<br />";
		}
		else {
			$result .= "<br />";
			$result .= "Ajout d'une clé primaire sur la table 'preferences'&nbsp;:".msj_erreur("ECHEC !")."<br />";
		}
	}
}

?>
