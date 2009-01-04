<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 *
 * Fichier temporaire pour tester le module mos_abs2
 * Création des tables nécessaires
 *
 */
?>

<html>
  <head>
    <title>Cr&eacute;ation des tables du module mod_abs2</title>
  </head>
  <body>

    <h2>Ces tables sont cr&eacute;&eacute;es pour tester le nouveau module absences</h2>
    <h3 style="color: red; font-weight: bold;">NE PAS UTILISER EN PRODUCTION</h3>
<?php


// Initialisations files
require_once("../lib/initialisations.inc.php");

$tab = array();

$verif = array('abs_creneaux', 'abs_informations', 'abs_traitements', 'abs_types', 'abs_motifs', 'abs_justifications', 'abs_actions');

$tab[] = "CREATE TABLE IF NOT EXISTS `abs_creneaux` (`id` INT( 11 ) NOT NULL , `nom_creneau` VARCHAR( 50 ) NOT NULL , `debut_creneau` INT( 12 ) NOT NULL , `fin_creneau` INT( 12 ) NOT NULL , `jour_creneau` INT( 2 ) NOT NULL DEFAULT '9', `type_creneau` ENUM( 'pause', 'repas', 'cours' ) NOT NULL , PRIMARY KEY ( `id` ) ) ENGINE = InnoDB ";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_informations` (`id` int(11) NOT NULL auto_increment, `utilisateurs_id` VARCHAR(100) NOT NULL, `eleves_id` INT(4) NOT NULL, `date_saisie` INT(13) NOT NULL, `debut_abs` INT(12) NOT NULL, `fin_abs` INT(12) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB ";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_traitements` (`id` int(11) NOT NULL auto_increment, `abs_informations_id` VARCHAR(250) NOT NULL, `utilisateurs_id` INT(13) NOT NULL, `date_traitement` INT(13) NOT NULL, `date_modif_traitement` INT(13) NOT NULL, `abs_type_id` INT(4) NOT NULL, `abs_motif_id` INT(4) NOT NULL, `abs_justification_id` INT(4) NOT NULL, `texte_justification` VARCHAR(250) NOT NULL, `abs_action_id` INT(4) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_types` (`id` int(11) NOT NULL auto_increment, `type_absence` VARCHAR(250) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_motifs` (`id` int(11) NOT NULL auto_increment, `type_motif` VARCHAR(250) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_justifications` (`id` int(11) NOT NULL auto_increment, `type_justification` VARCHAR(250) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB";
$tab[] = "CREATE TABLE IF NOT EXISTS `abs_actions` (`id` int(11) NOT NULL auto_increment, `type_action` VARCHAR(250) NOT NULL, PRIMARY KEY ( `id` ) ) ENGINE = InnoDB";
$tab[] = "CREATE TABLE IF NOT EXISTS `j_abs_informations_abs_traitements` (`abs_information_id` int(11) NOT NULL, `id_abs_traitement_id` int(11) NOT NULL)";

$nbre = count($tab);

for($a = 0 ; $a < $nbre ; $a++){

  if (mysql_query($tab[$a])) {
    echo '<p> La table ' . $verif[$a] . ' a été créée</p>';
  }else{
    echo '<p> Une erreur est apparue sur ' . $verif[$a] . ' : ' . mysql_error() . '</p>';
  }

}
?>
  </body>
</html>