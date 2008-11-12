<?php

/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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
 * Classe de base du module absences2 (mod_abs2)
 *
 */
class absences{
	/**
	 * cet attribut est unique et permet de lister tous les champs de la table
	 * abs_informations
	 * @access protected
	 */
  protected $champs = array();

  private $affErreurs = 'n';
	/**
	 * Constructor
	 * Permet d'initialiser les attributs de l'objet
	 * @access protected
	 */
	public function __construct(){
    /**
  	* + id
  	* + utilisateurs_id (qui a saisi ?)
  	* + groupes_id (cet événement a eu lieu pendant quel groupe ou AID ?)
    * + eleves_id (qui est absent ?)
   	* + date_saisie (timestamp UNIX)
  	* + debut_abs (timestamp UNIX)
  	* + fin_abs (timestamp UNIX)
  	*/
    $this->champs = array('id'=>'',
                          'utilisateurs_id' => $_SESSION["login"],
                          'groupes_id' => NULL,
                          'eleves_id' => NULL,
                          'date_saisie' => date("U"),
                          'debut_abs' => NULL,
                          'fin_abs' => NULL);
	}

	private function pdoConnect(){
    global $cnx;
    return $cnx;
  }

  public function setAffErreur($aff = NULL){
    /**
    * Permet d'arrêter d'afficher les erreurs autres que les exceptions
    */
    if ($aff !== NULL) {
      $this->affErreurs = $aff;
    }
  }

	public function setChamps($donnees = array()){

    $this->champs["id"] = isset($donnees["id"]) ? $donnees["id"] : $this->champs["id"];
    $this->champs["utilisateurs_id"] = isset($donnees["utilisateurs_id"]) ? $donnees["utilisateurs_id"] : $this->champs["utilisateurs_id"];
    $this->champs["groupes_id"] = isset($donnees["groupes_id"]) ? $donnees["groupes_id"] : $this->champs["groupes_id"];
    $this->champs["eleves_id"] = isset($donnees["eleves_id"]) ? $donnees["eleves_id"] : $this->champs["eleves_id"];
    $this->champs["date_saisie"] = isset($donnees["date_saisie"]) ? $donnees["date_saisie"] : $this->champs["date_saisie"];
    $this->champs["debut_abs"] = isset($donnees["debut_abs"]) ? $donnees["debut_abs"] : $this->champs["debut_abs"];
    $this->champs["fin_abs"] = isset($donnees["fin_abs"]) ? $donnees["fin_abs"] : $this->champs["fin_abs"];

  }

  public function getChamps(){
    return $this->champs;
  }

  protected function verifAbs(){
    if(!is_string($this->champs["utilisateurs_id"])){return false;}
    if(!is_numeric($this->champs["groupes_id"]) AND strpos($this->champs["groupes_id"], "AID") !== FALSE){return false;}
    if(!is_numeric($this->champs["eleves_id"])){return false;}
    if(!is_numeric($this->champs["date_saisie"])){return false;}
    if(!is_numeric($this->champs["debut_abs"])){return false;}
    if(!is_numeric($this->champs["fin_abs"])){return false;}
    return true;
  }

  protected function voirErreur($message){
    // --> affiche les erreurs si le réglage est souhaité
    if ($this->affErreurs == 'aff') {
      echo '
      <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
      <head><title>ERREUR la requête est erronée.</title></head>
      <body>
      <div style="width: 400px; height: 300px; border: 2px solid grey; background-color: silver;">
        <p>ERREUR : une erreur s\'est glissée dans le traitement php.</p>
        <p>Méthode : ' . $message["methode"] . '</p>
        <p>Requête : ' . $message["sql"] . '</p>
        <p>Erreur GEPI : ' . $message["erreur"] . '</p>
      </div>
      </body></html>
      ';
    }
    exit();
  }

  public function insertAbs(){
      $sql = "";
      $sql .= "INSERT INTO abs_informations (utilisateurs_id, groupes_id, eleves_id, date_saisie, debut_abs, fin_abs)";
      $sql .= " VALUES('".$this->champs["utilisateurs_id"]."', '".$this->champs["groupes_id"]."', '".$this->champs["eleves_id"]."', '".$this->champs["date_saisie"]."', '".$this->champs["debut_abs"]."', '".$this->champs["fin_abs"]."')";
    if ($this->verifAbs()) {
      if ($this->pdoConnect()->query($sql)) {

      }else{
        throw new Exception('Impossible d\'enregistrer une nouvelle absence.||' . $sql);
      }
    }else{
      $this->voirErreur(array('methode'=>__method__, 'erreur'=>'Il y a une erreur dans la vérification de l\'absence', 'sql'=>$sql));
    }
  }

  public function updateAbs(){

  }

  public function findAllAbs($_options = NULL){
    /**
    * Permet de récupérer toutes les absences d'un élève entre deux dates données ou de récupérer toutes les absences entre ces deux dates
    */
    $where  = isset($this->champ["eleves_id"]) ? ' WHERE eleves_id ="' . $this->champ["eleves_id"] . '"' : NULL;
    $_debut = isset($_options["debut"]) ? $_options["debut"] : 0;
    $_fin   = isset($_options["fin"]) ? $_options["fin"] : date("U");
    $sql = "SELECT * FROM";
  }

  public function deleteAbs(){
    if (!isset($this->id)) {

    }
  }

  public function __call($methode, $action){
    $test = explode("By", $methode);
    if ($test[0] == 'find') {
      $sql = "SELECT * FROM abs_informations WHERE " . $test[1] . " = '" . $action . "'";
      if ($query = $this->pdoConnect()->query($sql)) {
      }else{
        throw new Exception('Cet enregistrement n\'existe pas dans la table abs_informations.||' . $sql);
      }
    }else{
      $this->voirErreur(array('methode'=>$methode, 'erreur'=>'Cette méthode n\'existe pas dans la classe ' . __CLASS__, 'sql'=>$action));
      exit();
    }
    $retour = $query->fetchAll(PDO::FETCH_OBJ);
  }

  public function absEleveJour($jour){

  }

}
?>