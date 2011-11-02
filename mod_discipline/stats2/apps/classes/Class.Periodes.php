<?php
/*
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

require_once("Class.Date.php");
require_once("Modele.Select.php");

class ClassPeriodes {
private $periodes_calendrier=Null;
private $months=Array("09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre",
          "01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin");
  function  __construct() {
    $this->modele_select=new modele_select();
    $this->periodes_calendrier=$this->modele_select->get_db_periodes_calendrier();
  }

  public function is_periodes_renseignees(){
    if(isset($this->periodes_calendrier['error'])) return false ;
    else return true;
  }

  public function get_periodes_calendrier(){
    return($this->periodes_calendrier);
  }

  public function is_EDT_active(){
    return($this->modele_select->test_edt());
  }
  public function get_classes_periode(){
    return($this->modele_select->get_classes_periode());
  }
  public function get_infos_classe($nom){
  return($this->modele_select->get_infos_classe($nom));
  }
  public function get_months(){
    return($this->months);
  }
}
?>
