<?php
/**
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
 * Classe qui permet de vérifier les requêtes demandées par un plugin lors de son installation
 * Des méthodes permettent ensuite de lancer ces requêtes
 *
 * @author jjocal
 */
class traiterRequetes {

    /**
   * Réponse donnée après la vérification.
   *
   * @var boolean
   */
  private $_reponse = false;

  /**
   * Message d'erreur renvoyé par les différentes vérifications
   *
   * @var string Message d'erreur
   */
  private $erreur = NULL;

  /**
   * On stocke les requêtes dans cet attribut
   *
   * @var string La requête demandée
   */
  private $_requetes = NULL;

  /**
   * Détermine la liste des type de requêtes possibles
   *
   * @var array Liste des requêtes possibles
   */
  //private $_requetes_possibles = array('insert', 'INSERT', 'create', 'CREATE', 'update', 'UPDATE', 'drop', 'DROP');
  private $_requetes_possibles = array('insert', 'INSERT', 'create', 'CREATE', 'update', 'UPDATE', 'drop', 'DROP', 'delete', 'DELETE');

  /**
   * Vérification et envoie des requêtes par Propel::PDO
   *
   * @param object $requetes simpleXMLElement
   */
  public function  __construct(simpleXMLElement $requetes) {

    $this->_requetes = $requetes;
    if (count($this->_requetes->requete)==0) {
            $this->_reponse = true;
	} else {
		foreach ($this->_requetes->requete as $requete) {
		  // On est face à une liste de requêtes
		  if (trim($requete)=='') {
			$this->_reponse = true;
		  }else
		  if ($this->verifRequete($requete) === true){
			$this->insertRequete($requete);
		  }else{
			$this->retourneErreur(1, $requete);
		  }
		}
	}
  }

  /**
   * Méthode de vérification de la structure des requêtes SQL des plugins
   *
   * @param string $requete
   * @return boolean false/true
   */
  protected function verifRequete($requete){
    $test = explode(" ", trim($requete));
    if (in_array($test[0], $this->_requetes_possibles)){
      if (in_array($test[0], array('drop', 'DROP'))){
        if (in_array($test[1], array('table', 'TABLE'))){
          return true;
        }else{
          return false;
        }
      }else{
        return true;
      }
    }else{
      return false;
    }

  }

  /**
   * Méthode qui permet de lancer des requêtes SQL vers la base lors de la création d'un plugin
   *
   * @param string $requete Requête SQL
   */
  protected function insertRequete($requete){

    $con = Propel::getConnection();
    if ($con->exec($requete) !== false){
      $this->_reponse = true;
    }else{
      $this->_reponse = false;
    }

  }

  /**
   * Méthode qui retourne un type d'erreur et un message qui précise où se situe l'erreur.
   *
   * @param integer $_e Type d'erreur
   * @param string $_m noeud lié à cette erreur
   */
  private function retourneErreur($_e, $_m){
    switch ($_e) {
      case 1:
        $message = 'La requête ' . $_m . ' dans le fichier plugin.xml ne passe pas !';
        break;
      case 2:
        $message = '';
        break;
      case 3:
        $message = '';
        break;

      default:
        $message = "pas de message d'erreur";
      break;
    }
    $this->erreur = $message;
  }

  /**
   * Méthode qui renvoie une erreur si elle existe
   *
   * @return string Message d'erreur
   */
  public function getErreur(){
    return $this->erreur;
  }

  /**
   * Méthode qui renvoie la réponse après traitement des requêtes
   *
   * @return boolean false/true
   */
  public function getReponse(){
    return $this->_reponse;
  }

}
?>
