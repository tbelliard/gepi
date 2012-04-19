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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.6.1(dev) :</h3>";
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

?>
