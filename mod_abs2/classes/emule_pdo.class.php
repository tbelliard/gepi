<?php
/**
 *
 *
 * @version $Id: parametrage_ajax.php 2690 2008-11-26 20:57:45Z jjocal $
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
 * Classe php qui émule la présence du module PDO_Mysql
 * une instance est lancé quand PDO est absent du serveur
 * et si le script le demande ($utiliser_pdo = 'on';)
 */
class PDO{
  private $_query = NULL;
  public $FETCH_ASSOC = 'assoc';
  public $FETCH_BOTH = 'both';
  public $FETCH_OBJ = 'obj';
  /**
   * Constructor
   * @access protected
   */
  public function __construct(){

  }

  public function query($sql){
    if ($query = mysql_query($sql)) {

      $this->_query = $query;

    }else{
      throw new Exception('La requête ne passe pas ||' . $sql);
    }
  }

  public function exec($sql){
    if ($query = mysql_query($sql)) {

      $this->_query = $query;

    }else{
      throw new Exception('La requête ne passe pas ||' . $sql);
    }
  }

  public function fetch($query){

  }

  public function fetchAll($option){

    if (!$this->_query) {

    }else{

      $retour = mysql_fetch_object($this->_query);
      return $retour;

    }

  }

  public function prepare($sql){

    return $sql;

  }

  public function execute($array){

  }

}


?>