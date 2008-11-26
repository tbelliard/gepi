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
 * CLasse qui permet de gérer les motifs, les types, les justifications et lesactions possibles
 *
 */
class abs_gestion{

  /**
  * private _table
  * private _champ
  *
  *
  * */
  private $_table = NULL;
  private $_champ = NULL;

  /**
   * Constructor
   * @access protected
   */
  public function __construct(){

  }

  public function getTable($table){
    $this->_table = $table;
  }

  public function getChamps($champs){
    $this->_champs = $champs;
  }

  public function voirTout(){
    if (!isset($this->_table)) {
      throw new Exception(utf8_encode('La table de la bdd n\'est pas définie ou n\'existe pas'));
    }else{

      $sql = "SELECT * FROM " . $this->_table;
      if ($query = $GLOBALS["cnx"]->query($sql)) {



      }else{
        throw new Exception(utf8_encode("Impossible de lire la table " . $this->_table . "||" . $sql));
      }

    }
  }

  public function voirById($_id){

    if (!isset($this->_champ) OR !isset($this->_table)) {
      throw new Exception(utf8_encode('La table et son champs n\'ont pas été définis ou n\'existent pas'));
    }else{

      $sql = "SELECT " . $this->_champ . " FROM " . $this->_table . " WHERE id = '" . $_id . "'";

    }

  }

}

?>