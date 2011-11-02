<?php
/**
 * @version : $Id: traiterXml.class.php 3248 2009-06-24 20:06:15Z jjocal $
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
if (!$_SESSION["login"]){Die();}

/**
 * Classe qui vérifie la structure du fichier plugin.xml des plugins de Gepi
 *
 * @author jjocal
 */
class traiterXml {

  /**
   * Fichier plugin.xml parsé par simpleXML
   *
   * @var object simpleXMLElement
   */
  private $_xml = NULL;

  /**
   * Liste des fichiers du répertoire du plugin
   *
   * @var array (résultat du scandir du répertoire du plugin)
   */
  private $_fichiers = NULL;

  /**
   * Liste des noeuds de niveau 1 du fichier plugin.xml
   *
   * @var array $_noeuds
   */
  private $_noeuds = array('nom',
                           'creationDate',
                           'auteur',
                           'licence',
                           'auteurCourriel',
                           'auteurSite',
                           'version',
                           'versiongepi',
                           'description',
                           'installation',
                           'desinstallation',
                           'administration');

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

  public function __construct(SimpleXMLElement $xml) {

    $this->_xml = $xml;
    if ($this->verifNiveau1() === true){
      $this->scanDirPlugin();
      if ($this->verifFichiers() === true){
        $this->_reponse = true;
      }else{
        $this->_reponse = false;
      }
    }else{
      $this->_reponse = false;
    }

  }

  private function scanDirPlugin(){
    $this->_fichiers = scandir($this->_xml->nom);
  }

  /**
   * Méthode qui vérifie si les noeuds de premier niveau sont bien présents dans le fichier plugin.xml
   *
   * @return boolean
   */
  private function verifNiveau1(){
    // On vérifie tous les noeuds
    $nbre = count($this->_noeuds);
    for($a = 0 ; $a < $nbre ; $a++){
      $noeud = $this->_noeuds[$a];

      if (!isset($this->_xml->$noeud) OR $this->_xml->$noeud == ''){
        $this->retourneErreur(1, htmlentities('<'.$noeud.'>'));
        return false;
      }
    }
    return true;
  }

  /**
   * Méthode qui vérifie si les fichiers déclarés dans plugin.xml sont bien dans le répoertoire du plugin
   *
   * @return boolean
   */
  private function verifFichiers(){
    $xml_fichiers = $this->_xml->administration->fichier;

    foreach ($xml_fichiers->nomfichier as $item) {

      if (!in_array($item, $this->_fichiers)){
        $this->retourneErreur(2, $item);
        return false;
      }

    }
    return true;
  }

  /**
   * Méthode qui vérifie si les fichiers du répertoire sont bien tous déclarés dans le fichier plugin.xml
   *
   * @return boolean 
   */
  private function verifXmlFichiers(){
    return true;
  }

  /**
   * Méthode qui retourne un type d'erreur et un message qui précise où se situe l'erreur.
   *
   * @param integer $_e
   * @param string $_m
   */
  private function retourneErreur($_e, $_m){
    switch ($_e) {
      case 1:
        $message = 'Il manque le noeud ' . $_m . ' dans le fichier plugin.xml';
        break;
      case 2:
        $message = 'Le fichier ' . $_m . ' est déclaré dans plugin.xml mais est manquant dans le plugin.';
        break;
      case 3:
        $message = 'Le fichier ' . $_m . ' est présent dans le plugin mais n\'a pas ses droits dans plugin.xml.';
        break;

      default:
        $message = "pas de message d'erreur";
      break;
    }
    $this->erreur = $message;
  }

    public function getErreur(){
      return $this->erreur;
    }
    public function getReponse(){
      return $this->_reponse;
    }

}
?>
