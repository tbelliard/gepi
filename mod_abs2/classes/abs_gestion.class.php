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
 * CLasse qui permet de gérer les motifs, les types, les justifications et les actions possibles
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

  private $_encode = 'ISO-8859-1';

  /**
   * Constructor
   * @access protected
   */
  public function __construct(){

  }

  public function setTable($table){
    $this->_table = $table;
  }

  public function setChamps($champs){
    $this->_champs = $champs;
  }

  public function setEncodage($encode){
    if ($encode == "utf8") {
      $this->_encode = $encode;
    }else{
      throw new Exception("L'encodage demandé n'est pas valide pour Gepi. ||" . $encode);
    }

  }

  public function voirTout(){
    if (!isset($this->_table)) {
      throw new Exception(utf8_encode('La table de la bdd n\'est pas d&eacute;finie ou n\'existe pas'));
    }else{

      $sql = "SELECT * FROM " . $this->_table;
      if ($query = $GLOBALS["cnx"]->query($sql)) {

        return $query->fetchALL(PDO::FETCH_OBJ);

      }else{
        throw new Exception(utf8_encode("Impossible de lire la table " . $this->_table . "||" . $sql));
      }

    }
  }

  public function voirById($_id){

    if (!isset($this->_champ) OR !isset($this->_table)) {
      throw new Exception(utf8_encode('La table et son champs n\'ont pas &eacute;t&eacute; d&eacute;finis ou n\'existent pas'));
    }else{

      $sql = "SELECT " . $this->_champ . " FROM " . $this->_table . " WHERE id = '" . $_id . "'";

    }

  }

  public function _saveNew($new){

    $insert_new = $this->_encode == 'utf8' ? htmlentities(utf8_decode($new)) : $new;

    $sql = "INSERT INTO " . $this->_table . " (" . $this->_champs . ") VALUES ('" . $insert_new . "')";

    if ($insert = $GLOBALS["cnx"]->exec($sql)) {
      return $insert;
    }else{
      throw new Exception('Erreur dans la requ&ecirc;te SQL ||' . $sql);
    }

  }

  public function _deleteById($_id){

    if (!is_numeric($_id)) {
      return FALSE;
    }

    $sql = "DELETE FROM " . $this->_table . " WHERE id = '" . $_id . "' LIMIT 1";

    if ($delete = $GLOBALS["cnx"]->exec($sql)) {
      return $delete;
    }else{
      throw new Exception('Erreur dans la requ&ecirc;te SQL ||' . $sql);
    }

  }

}

?>