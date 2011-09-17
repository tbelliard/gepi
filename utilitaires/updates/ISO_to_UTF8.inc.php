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

/* Passage de la base en UTF8 */

$result.="<br /><strong>Passage de la base en ".SET_DEST."</strong><br />";

/* VÃ©rifier si la base est en ISO-8559 */

$result.="Test de l'encodage de la base<br />";
$query = mysql_query("SHOW VARIABLES LIKE 'character\_set\_%'");
if ($query) {
  while ($row = mysql_fetch_object($query)) {
    $donneesBase[] = $row;
  }
} else {
  die ('Erreur de lecture de la base');
}

$continue=FALSE;
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
    $queryBase = mysql_query("ALTER DATABASE  CHARACTER SET ".SET_DEST.";");
    $result.="passage de ".$donnees->Variable_name." Ã  ".SET_DEST."<br />";
    $continue=TRUE;
  }
}
unset ($donnees, $donneesBase);

$query = mysql_query("SHOW VARIABLES LIKE 'character\_set\_%'");
if ($query) {
  while ($row = mysql_fetch_object($query)) {
    $donneesBase[] = $row;
  }
} else {
  die ('Erreur de lecture de la base');
}
foreach ($donneesBase as $donnees) {
    $result.=msj_present($donnees->Variable_name.' => '.$donnees->Value);
}
unset ($donnees);

/**
 * @todo revoir le test, si on récupère une ancienne base il faudrait initialisé $forceUtf8 plutôt que de le forcer dans maj.php
 */
if ($continue || $forceUtf8) {
/* On vient de passer la base en UTF-8, on s'occupe des tables */

	/* Passage des tables en UTF8 */
	$result.="<br />Passage des tables en ".SET_DEST."<br />";

	$query = mysql_query("SHOW tables");
	if ($query) {
		while ($row = mysql_fetch_array($query)) {
		  $donneesTable[] = $row;
		}
	} else {
		die ('Erreur de lecture de la base');
	}

	foreach ($donneesTable as $table) {
		$querytable = mysql_query('ALTER TABLE '.$table[0].' CHARACTER SET '.SET_DEST);
		$querytable = mysql_query('ALTER TABLE '.$table[0].' CONVERT TO CHARACTER SET '.SET_DEST);

	}
	unset ( $table);
	$result .= msj_ok("Tables encodées en ".SET_DEST);

}



?>
