<?php
/**
 * Ensemble de méthodes utilisées par le script d'initialisation
 * 
 * $Id$
 * 
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
*/

/**
 *
 * @param type $tablename
 * @param type $indexname
 * @param type $indexcolumns
 * @return string 
 */
function add_index($tablename, $indexname, $indexcolumns) {
  $result = "&nbsp;->Ajout de l'index '$indexname' à la table $tablename<br />";
  $req_res=0;
  $req_test = mysqli_query($GLOBALS["mysqli"], "SHOW INDEX FROM $tablename");
  if (mysqli_num_rows($req_test)!=0) {
    while ($enrg = mysqli_fetch_object($req_test)) {
      if ($enrg-> Key_name == $indexname) {$req_res++;}
    }
  }
  if ($req_res == 0) {
    $query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `$tablename` ADD INDEX $indexname ($indexcolumns)");
    if ($query) {
      $result .= msj_ok();
    } else {
      $result .= msj_erreur();
    }
  } else {
    $result .= msj_present("L'index existe déjà.");
  }
  return $result;
}

/**
 * mise à jour réussie
 * @param sring $message
 * @return string Ok ! ou $message écrit en vert 
 */
function msj_ok($message=""){
  if ($message=="") {
    return "<span class='msj_ok'>Ok !</span><br />";
  } else {
    return "<span style='color:green;'>$message</span><br />";
  }
  
}

/**
 * Echec d'une mise à jour
 * @param string $message
 * @return string Erreur suivi de $message écrit en rouge
 */
function msj_erreur($message=""){
  return "<span class='msj_erreur'>Erreur $message</span><br />";
}

/**
 * Mise à jour déjà effectuée
 * @param string $message
 * @return string $message écrit en bleu
 */
function msj_present($message){
  return "<span class='msj_present'> $message.</span><br />";
}

?>
