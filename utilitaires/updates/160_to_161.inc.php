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
$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM a_types LIKE 'mode_interface';"));
if ($test_champ>0) {
	$result .= msj_present("Le champ est déjà modifié.");
}
else {
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE a_types CHANGE type_saisie mode_interface VARCHAR(50) DEFAULT 'NON_PRECISE' COMMENT 'Enumeration des possibilités de l\'interface de saisie de l\'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE, CHECKBOX, CHECKBOX_HIDDEN'");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$result .= "&nbsp;->Ajout d'entrées à la table 'setting' pour encodage des fichiers photo élève<br />";
$req_test=mysqli_query($GLOBALS["mysqli"], "SELECT value FROM setting WHERE name = 'encodage_nom_photo'");
$res_test=mysqli_num_rows($req_test);
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
$req_test=mysqli_query($GLOBALS["mysqli"], "SELECT value FROM setting WHERE name = 'alea_nom_photo'");
$res_test=mysqli_num_rows($req_test);
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

?>
