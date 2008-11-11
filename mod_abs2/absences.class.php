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

	/**
	 * Constructor
	 * Permet d'initialiser les attributs de l'objet
	 * @access protected
	 */
	public function __construct(){
    /**
  	* + id
  	* + utilisateurs_id (qui a saisi ?)
  	* + groupes_id (cet événement a eu lieu pendant quel groupe ?)
    * + eleves_id (qui est absent ?)
   	* + date_saisie (timestamp UNIX)
  	* + debut_abs (timestamp UNIX)
  	* + fin_abs (timestamp UNIX)
  	*/
    $this->champs = array('utilisateurs_id' => $_SESSION["login"],
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

	public function setChamps($donnees = array()){

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
    return true;
  }

  protected function affErreur(){

  }

  public function insertAbs(){
    if ($this->verifAbs()) {
      $sql = "";
      $sql .= "INSERT INTO abs_informations (utilisateurs_id, groupes_id, eleves_id, date_saisie, debut_abs, fin_abs)";
      $sql .= " VALUES('".$this->champs["utilisateurs_id"]."', '".$this->champs["groupes_id"]."', '".$this->champs["eleves_id"]."', '".$this->champs["date_saisie"]."', '".$this->champs["debut_abs"]."', '".$this->champs["fin_abs"]."')";
      $this->pdoConnect()->query($sql);
    }else{
      echo $this->affErreur();
    }
  }

  public function updateAbs(){

  }

  public function findAllAbs($_login = NULL){

  }

  public function deleteAbs(){

  }

  public function __call($methode, $action){

  }

  public function absEleveJour($jour){

  }

  public function donneesFicheEleve($_login){
    return array();
  }
}
?>