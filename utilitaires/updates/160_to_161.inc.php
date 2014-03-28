<?php
/**
 * Fichier de mise à jour de la version 1.6.0 à la version 1.6.1 par défaut
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
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.1 :</h3>";
$result .= "&nbsp;->Modification du champ 'type_saisie' de la table 'a_types' en 'mode_interface'<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_types LIKE 'mode_interface';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ est déjà modifié.");
}
else {
	$query = mysql_query("ALTER TABLE a_types CHANGE type_saisie mode_interface VARCHAR(50) DEFAULT 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE, CHECKBOX, CHECKBOX_HIDDEN'");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'entrées à la table 'setting' pour encodage des fichiers photo élève<br />";
$req_test=mysql_query("SELECT value FROM setting WHERE name = 'encodage_nom_photo'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
	$result_inter = traite_requete("INSERT INTO setting VALUES ('encodage_nom_photo', 'no');");
	if ($result_inter == '') {
		$result.=msj_ok("Définition du paramètre encodage_nom_photo : Ok !");
	} else {
		$result.=msj_erreur("Définition du paramètre encodage_nom_photo : Erreur !");
	}

	$titre="Encodage des photos";
	$texte="Une fonctionnalité d'encodage des photos est proposée pour éviter des téléchargements abusifs.<br />Voir <a href='./mod_trombinoscopes/trombinoscopes_admin.php#encodage'>Administration du module Trombinoscope</a>";
	$destinataire="administrateur";
	$mode="statut";
	enregistre_infos_actions($titre,$texte,$destinataire,$mode);

} else {
	$result .= msj_present("Le paramètre encodage_nom_photo existe déjà dans la table setting.");
}
$req_test=mysql_query("SELECT value FROM setting WHERE name = 'alea_nom_photo'");
$res_test=mysql_num_rows($req_test);
if ($res_test==0){
  $result_inter = traite_requete("INSERT INTO setting VALUES ('alea_nom_photo', MD5(UNIX_TIMESTAMP()));");
  if ($result_inter == '') {
    $result.=msj_ok("Définition du paramètre alea_nom_photo : Ok !");
  } else {
    $result.=msj_erreur("Définition du paramètre alea_nom_photo : Erreur !");
  }
} else {
  $result .= msj_present("Le paramètre alea_nom_photo existe déjà dans la table setting.");
}

$result .= "&nbsp;-> Ajout d'un champ 'mode' à la table 'notanet_corresp'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM notanet_corresp LIKE 'mode';"));
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE notanet_corresp ADD mode varchar(20) NOT NULL default 'extract_moy';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'notanet_saisie' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'notanet_saisie'");
if ($test == -1) {
	$sql="CREATE TABLE IF NOT EXISTS notanet_saisie (login VARCHAR( 50 ) NOT NULL, id_mat INT(4), matiere VARCHAR(50), note VARCHAR(4), PRIMARY KEY ( login )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
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

?>
