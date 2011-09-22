<?php

/**
 * Fonctions utiles uniquement pour l'administrateur
 * 
 * $Id $
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage general
 */

/**
 * Vérifie que le dossier photos est bien configuré pour le multisite
 * 
 * Crée au besoin les dossiers 
 * - photos/xxxRNExxx
 * - photos/xxxRNExxx/eleves
 * - photos/xxxRNExxx/personnels
 * 
 * @return boolean TRUE si les dossiers existent
 */
function check_photos_multisite(&$messageErreur) {
  /* On récupère le répertoire courant */
  $courantDir= dirname(dirname(__FILE__));
  $dirEtablissement=$courantDir.'/photos/'.$_COOKIE['RNE'];
  $dirEleve=$dirEtablissement.'/eleves';
  $dirPersonnels=$dirEtablissement.'/personnels';
  
  /* Vérifier si photos/RNE existe */    
  if (!is_dir($dirEtablissement)) { 
    /* créer photos/RNE au besoin */
    if (!mkdir($dirEtablissement,0770)) {
      $messageErreur = "Echec de la création du dossier $dirEtablissement, vérifier les droits sur le dossier photos";
      return FALSE;
    } 
  }
  /* Vérifier si photos/RNE est protégé */
  if (!is_file($dirEtablissement.'/index.html')) { 
    /* protéger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirEtablissement.'/index.html' )){
      $messageErreur = "Echec lors de l'écriture dans le dossier $dirEtablissement, vérifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }
  }   
  
  /* Vérifier si photos/RNE/eleves existe */
  if (!is_dir($dirEleve)) {
    /* créer photos/RNE/eleves au besoin */
    if (!mkdir($dirEleve,0770))  {
      $messageErreur = "Echec de la création du dossier $dirEleve, vérifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }  
  }
  /* Vérifier si photos/RNE/eleves est protégé */
  if (!is_file($dirEleve.'/index.html')) { 
    /* protéger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirEleve.'/index.html' )){
      $messageErreur = "Echec lors de l'écriture dans le dossier $dirEleve, vérifier les droits sur le dossier $dirEleve";
      return FALSE;
    }
  } 
  
  /* Vérifier si photos/RNE/personnels existe */
  if (!is_dir($dirPersonnels)) {
    /* créer photos/RNE/personnels au besoin */
    if (!mkdir($dirPersonnels,0770))  {
      $messageErreur = "Echec de la création du dossier $dirPersonnels, vérifier les droits sur le dossier $dirEtablissement";
      return FALSE;
    }  
  }
  /* Vérifier si photos/RNE/personnels est protégé */
  if (!is_file($dirPersonnels.'/index.html')) { 
    /* protéger le dossier en copiant /lib/index.html dedans */
    if (!copy($courantDir.'/lib/index.html',$dirPersonnels.'/index.html' )){
      $messageErreur = "Echec lors de l'écriture dans le dossier $dirPersonnels, vérifier les droits sur le dossier $dirPersonnels";
      return FALSE;
    }
  } 
  
  return TRUE;
} 





?>
