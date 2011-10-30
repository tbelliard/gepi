<?php
/**
 * Fichier de mise à jour de la version 1.5.4 à la version 1.5.5
 * 
 * $Id$
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

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version courante :</h3>";

require 'updates/ISO_to_UTF8.inc.php';

$result.="<br />";
$result.="<strong>Module relevé de notes :</strong>";
$result.="<br />";

$result .= "&nbsp;-> Ajout d'un champ rn_abs_2 à la table 'classes'<br />";
// Ajout d'une colonne rn_abs_2 dans classes pour stocker l'affichage ou non des absences sur les relevés de notes
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM classes LIKE 'rn_abs_2';"));

	// $result .= "&nbsp;-> Place du champ rn_abs_2 dans la table 'classes' : ".$test_champ."<br />";
if ($test_champ==0) {
	$query = mysql_query("ALTER TABLE classes ADD rn_abs_2 char(1) NOT NULL default 'n';");
	if ($query) {
			$result .= msj_ok("Ok !");
	} else {
			$result .= msj_erreur();
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}
	
$result .= "<br /><strong>Table abs2 agrégation</strong><br />";
$result .= "&nbsp;->Changement du nom de la colonne justifiee en non_justifiee<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_agregation_decompte LIKE 'non_justifiee';"));
if ($test_champ>0) {
	$result .= msj_present("La colonne est déjà renommée");
} else {
	$query = mysql_query("ALTER TABLE a_agregation_decompte change justifiee non_justifiee TINYINT DEFAULT 0 COMMENT 'Si cette demi journée est compté comme absence, y a-t-il une justification';");
	if ($query) {
			$result .= msj_ok();
			mysql_query("DELETE * from a_agregation_decompte;");
	} else {
			$result .= msj_erreur(mysql_error());
	}
}
$result .= "&nbsp;->Changement du nom de la colonne nb_retards en nb_retards_non_justifies<br />";
$test_champ=mysql_num_rows(mysql_query("SHOW COLUMNS FROM a_agregation_decompte LIKE 'nb_retards_non_justifies';"));
if ($test_champ>0) {
	$result .= msj_present("La colonne est déjà renommée");
} else {
	$query = mysql_query("ALTER TABLE a_agregation_decompte change nb_retards nb_retards_non_justifies INTEGER DEFAULT 0 COMMENT 'Nombre de retards non justifiés décomptés dans la demi journée';");
	if ($query) {
			$result .= msj_ok();
			mysql_query("DELETE * from a_agregation_decompte;");
	} else {
			$result .= msj_erreur(mysql_error());
	}
}

$result.="<br />Fin mise à jour<br/>";
?>
