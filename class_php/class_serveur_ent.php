<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Classe qui implémente un serveur pour permettre à un ENT de se connecter à GEPI
 * Accès limité à la lecture seule. Pour limiter les accès, on liste les méthodes disponibles
 * Les logins des élèves existent sous la forme d'un tableau envoyé en POST par curl
 *
 * @method notesEleve(), cdtDevoirsEleve(), cdtCREleve(), professeursEleve(), edtEleve()
 *
 * @author Julien Jocal
 * @license GPL
 */
$traite_anti_inject = 'no'; // pour éviter les échappements dans les tableaux sérialisés
require_once("../lib/initialisationsPropel.inc.php");
//require_once("../lib/initialisations.inc.php");


class serveur_ent {

  /**
   * Définit le type de demande (utilise le nom des méthodes autorisées)
   * @var string méthode évoquée par la demande
   */
  private $_demande      = NULL;

  /**
   * Définit la période demandée
   * @var integer Numéro de la période, 0 par défaut équivaut à toutes les périodes.
   */
  private $_periode   = 0;
  /**
   * liste des logins des enfants du parent qui demande (envoyé par le client)
   * @var array _enfants
   */
  private $_enfants     = array();
  /**
   * Le login ENT du demandeur (envoyé par le client)
   * @var string _login
   */
  private $_login       = NULL;
  /**
   * Le RNE de l'établissement du demandeur (envoyé par le client)
   * @var string RNE
   */
  private $_etab        = NULL;
  /**
   * la clé secrète entre le client et le serveur
   * @var string clé
   */
  private $_api_key     = NULL;
  /**
   * Ce hash est envoyé par le client, le serveur le renvoie avec la réponse pour permettre au client de vérifier qu'il s'agit bien de sa demande
   * @var string hash
   */
  private $_hash        = NULL;

  /**
   * Constructeur de la classe
   *
   * @example Si on est en multisite, il faut un cookie["RNE"] qui donne le bon RNE pour que GEPI se connecte sur la bonne base
   */
  public function __construct(){
    // On initialise toutes nos propriétés
    $this->setData();
    // Vérification de la clé
    $this->verifKey('4567123');
    // On intègre les fichiers d'initialisation de GEPI
    //require_once("../lib/initialisationsPropel.inc.php");
    //require_once("../lib/initialisations.inc.php");

    // On vérifie que la demande est disponible
    if (!in_array($this->_demande, $this->getMethodesAutorisees())){
      $this->writeLog(__METHOD__, 'Méthode inexistante:'.$this->_demande, ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('Méthode inexistante !');
    }
    // On vérifie si les logins des enfants envoyés existent bien dans GEPI
    $reponse = array(); // permet de stocker les informations sur les enfants (tableau d'objets propel)

    foreach (unserialize($this->_enfants) as $enfants){
      // On cherche si cet enfant existe
      $enf = EleveQuery::create()->filterByLogin($enfants)->find();

      if ($enf->isEmpty()){
        // Ce login n'existe pas dans cette base
        $this->writeLog(__METHOD__, 'login enfant inexistant : ' . $enfants, ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
        $reponse[] = 'inexistant';
      }else{
        // on recherche la réponse pour ce login
        $arenvoyer = $this->{$this->_demande}($enf[0]);
        $reponse[$enf[0]->getLogin()] = $arenvoyer;
      }

    } // foreach
    //$this->_enfants = $reponse; // Désormais on a les objets propel de ces enfants, reste à les manipuler

    if (is_array($reponse)){
      echo serialize($reponse);
    }else{
      echo serialize(array('erreur'=>'service absent'));
    }

  }

  /**
   * renvoie le header du REQUEST_METHOD
   * @return string GET POST PUT ...
   */
  public function testRequestMethod(){
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * @todo Mieux gérer le cas où la requête n'est pas en POST
   * @return void initialise les propriétés de l'objet
   */
  private function setData(){
    // On ne fonctionne qu'en POST
    if ($this->testRequestMethod() != 'POST'){
      $this->writeLog(__METHOD__, 'La demande n\'a pas été passée en POST', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die();
    }else{
      // On vérifie que les données demandées existent
      $this->_etab      = (array_key_exists('etab', $_POST)) ? $_POST['etab'] : null;
      $this->_enfants   = (array_key_exists('enfants', $_POST)) ? $_POST['enfants'] : null;
      $this->_api_key   = (array_key_exists('api_key', $_POST)) ? $_POST['api_key'] : 'false';
      $this->_demande   = (array_key_exists('demande', $_POST)) ? $_POST['demande'] : null;
      $this->_hash      = (array_key_exists('hash', $_POST)) ? $_POST['hash'] : null;
      $this->_login     = (array_key_exists('login', $_POST)) ? $_POST['login'] : null;
      $this->_periode   = (array_key_exists('periode', $_POST)) ? $_POST['periode'] : $this->_periode;
    }
  }

  private function verifKey($key){
    if ($this->_api_key != $key){
      $this->writeLog(__METHOD__, 'La clé n\'est pas bonne ('.$this->_api_key.'|'.$key.')', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('la clé est obsolète : ' . $this->_api_key . '|+|' . $key);
    }
  }
  /**
   * Renvoie la liste des méthodes autorisées par le serveur
   * @todo Penser à mettre à jour cette liste au fur et à mesure de la définition des méthodes
   * @return array liste des méthodes autorisées
   */
  public function getMethodesAutorisees(){
    return array('notesEleve', 'cdtDevoirsEleve', 'cdtCREleve', 'professeursEleve', 'edtEleve');
  }

  /**
   * Renvoie la liste des notes d'un élève en fonction de son login pour les deux derniers mois
   *
   * @param string $_login login de l'élève
   * @return array Liste des notes d'un élève
   */
  public function notesEleve($_login){
    return array();
  }

  /**
   * Renvoie la liste des devoirs à faire pour un élève (en fonction du login de l'élève)
   *
   * @todo Pour le moment, on renvoie pour chaque matière le devoir le plus éloigné dans le temps, il faudrait renvoyer tous les devoirs dont la date est postérieure
   * @param string $_login
   * @return array Liste des devoirs à faire du cdt de l'élève
   */
  public function cdtDevoirsEleve(eleve $_eleve){
    $var = array();

    foreach ($_eleve->getGroupes() as $groupes) {
      $devoirs = $groupes->getCahierTexteTravailAFairesJoinUtilisateurProfessionnel();
      if (!$devoirs->isEmpty()){
        foreach ($devoirs as $devoir){
          $dev = array($devoir->getDateCt() => strip_tags($devoir->getContenu(), 'div'));
        }
        $var[$groupes->getDescription()] = $dev;
      }else{
        $var[$groupes->getDescription()] = array(''=>'Regardez le cahier de textes de l\'enfant.');
      }
    }
    return $var;
  }

  /**
   * Renvoie la liste des derniers compte-rendus pour chaque enseignement auxquels est inscrit un élève
   *
   * @param string $_login login de l'élève
   * @return array Liste des Compte-Rendus d'un élève
   */
  public function cdtCREleve($_login){
    return array();
  }

  /**
   * Renvoie la liste des professeurs d'un élève avec leur matière associée
   *
   * @param string $_login login de l'élève
   * @return array Liste des professeurs de l'élève
   */
  public function professeursEleve(eleve $eleve){
    $reponse = array();
    if (!is_object($eleve)){
      $this->writeLog(__METHOD__, 'objet inexistant', ((array_key_exists('login', $_POST)) ? $_POST['login'] : 'inexistant'));
      Die('Erreur prof-eleve');
    }else{
      foreach ($eleve->getGroupes() as $groupes) {
        $reponse[] = $groupes->getUtilisateurProfessionnels();
      }
    }
    return $reponse;
  }

  /**
   * Renvoie la liste des cours d'un élève au cours de la semaine actuelle
   *
   * @param string $_login
   * @return array edt d'un élève sous la forme d'un tableau php : array('lundi'=>array('M1'=>'Mathématiques',...), 'mardi'=>array(),...)
   */
  public function edtEleve($_login){
    return array();
  }

  /**
   * Loggue les erreurs du serveur dans un fichier
   *
   * @param string $methode méthode demandée
   * @param string $message message d'erreur
   * @param string $login_demandeur login du demandeur
   */
  private function writeLog($methode, $message, $login_demandeur){
    // Du code pour écrire dans un fichier de log
    $fichier = fopen('../temp/serveur_ent.log', 'a+');
    fputs($fichier, ($this->_etab !== NULL ? $this->_etab : 'ETAB') . ' :: ' . $login_demandeur . ' = ' . $message . ' -> ' . $methode . ' ' . $_SERVER['REMOTE_ADDR'] . ".\n");
    fclose($fichier);

  }
}
$test = new serveur_ent();
?>