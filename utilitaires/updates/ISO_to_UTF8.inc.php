<?php
/**
 * Mise à jour de la base lors du passage en UTF-8
 * 
 * $Id:  $
 *
 * 
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Bouguin Régis
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @todo vérifier l'encodage de la base et des tables avant de les réencoder
 */

/* *
define('SET_ORIGINE', 'utf8');
define('SET_DEST', 'latin1');
 /* */
/* */
define('SET_ORIGINE', 'latin1');
define('SET_DEST', 'utf8');
 /* */

$result.="<br /><strong>Passage de la base en ".SET_DEST."</strong><br />";

$query = mysql_query("SHOW VARIABLES LIKE 'character_set_%'");
if ($query) {
  while ($row = mysql_fetch_object($query)) {
    $donneesBase[] = $row;
  }
} else {
  die ('Erreur de lecture de la base');
}

foreach ($donneesBase as $donnees) {
/*
 * non modifier
  if (($donnees->Variable_name == 'character_set_client')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_connection')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_results')&&($donnees->Value != SET_DEST)) {
    // non modifier character_set_results
  }
  if (($donnees->Variable_name == 'character_set_server')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_system')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
*/
  if (($donnees->Variable_name == 'character_set_database')&&($donnees->Value != SET_DEST)) {
      $result.=$donnees->Variable_name." est réglé à ".$donnees->Value."<br />";
      $result.="passage de ".$donnees->Variable_name." à  ".SET_DEST."<br />";
    $queryBase = mysql_query("ALTER DATABASE  CHARACTER SET ".SET_DEST.";");
  }
}
unset ($donnees, $donneesBase);

//debug sur les variables
//$query = mysql_query("SHOW VARIABLES LIKE 'character\_set\_%'");
//if ($query) {
//  while ($row = mysql_fetch_object($query)) {
//    $donneesBase[] = $row;
//  }
//} else {
//  die ('Erreur de lecture de la base');
//}
//foreach ($donneesBase as $donnees) {
//    $result.=msj_present($donnees->Variable_name.' => '.$donnees->Value);
//}
//unset ($donnees);

/* on s'occupe des tables */
$result.="<br />Passage des tables en ".SET_DEST." en cours<br />";

$query = mysql_query("SHOW table status");
if ($query) {
	while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
        if (substr($row['Collation'],0,6) == 'latin1' ) {
            $donneesTable[] = $row['Name'];
        }
	}
} else {
	die ('Erreur de lecture de la base');
}
if (empty($donneesTable) ){
    $result .= msj_ok("Tables déjà encodées en ".SET_DEST);
} else {
    foreach ($donneesTable as $table) {
        $result.="Passage de $table en ".SET_DEST." en cours. ";
    	$querytable = mysql_query('ALTER TABLE '.$table.' CONVERT TO CHARACTER SET '.SET_DEST);
        $querytable = mysql_query('ALTER TABLE '.$table.' CHARACTER SET '.SET_DEST);
        $result.=" Terminé<br />";
    }
    unset ( $table);
    $result .= msj_ok("Migration terminée : Tables encodées en ".SET_DEST);
}


?>
