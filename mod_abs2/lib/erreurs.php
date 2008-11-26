<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

function affExceptions($e){
  $tab = $e->getMessage();
  $erreur = explode("||", $tab);
  $erreur[1] = isset($erreur[1]) ? $erreur[1] : 'Pas de requ&ecirc;te';
  $erreur2 = $e->getTrace();
  $aff_classe = isset($erreur2[0]["class"]) ? $erreur2[0]["class"] : 'scripting hors POO';
  $file = explode("/", $erreur2[0]["file"]);
  $nbre = count($file);
  $aff = $nbre>0 ? $nbre - 1 : 0;
  $aff2 = $nbre>1 ? $nbre - 2 : 0;
  if ($aff != $aff2) {
    $aff_file = $file[$aff2] . '/' . $file[$aff];
  }else{
    $aff_file = $file[$aff2];
  }

  echo '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
      <head><title>ERREUR la requ&ecirc;te est erron&eacute;e.</title></head>
      <body>
        <div style="width: 500px; height: 350px; border: 2px solid grey; background-color: silver; margin-left: 200px; margin-top: 100px;">
        <p style="color: red; font-weight: bold; text-align: center;">ERREUR</p>
        <p>Message : ' . $erreur[0] . '</p>
        <p>Erreur : ' . $e->getCode() . '</p>
        <p>Requ&ecirc;te : ' . $erreur[1] . '</p>
        <p>Classe : ' . $aff_classe . '&nbsp;&nbsp;|&nbsp;&nbsp;M&eacute;thode : ' . $erreur2[0]["function"] . '</p>
        <p>fichier : ' . $aff_file . ' <br />&agrave; la ligne : ' . $erreur2[0]["line"] . '</p>
        </div>
        <p style="color: blue; cursor: pointer;" onclick="window.history.back();">Revenir en arri&egrave;re</p>
      </body></html>';

  afficher_erreur_et_exit();
}

function afficher_erreur_et_exit(){
  exit();
}
?>