<?php
/*
 * $Id: Class.Individu.php 7799 2011-08-17 08:38:10Z dblanqui $
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

require_once("Modele.Incidents.php");
class ClassIndividu {
  private $modele_select=Null;
  private $individus_identites=Null;
  private $individu_identite=Null;
  private $liste_eleves=Null;
  private $infos_individus=Null;

  function  __construct() {
    $this->modele_select=new modele_select();
    $this->modele_incidents=new Modele_Incidents();
  }
  public function get_individus_data() {
    if (isset($_SESSION['individus'])) {
      foreach($_SESSION['individus']as $key=>$value) {
        $this->individu_identite=$this->modele_select->get_db_individu_identite($value[0],$value[1]);
        $this->individus_identites[]=$this->individu_identite;
      }
      return($this->individus_identites);
    }
  }

  public function get_infos_individu($login,$statut) {
    return($this->modele_select->get_db_individu_identite($login,$statut));
  }

  public function get_liste_eleves(){
    return($this->liste_eleves=$this->modele_incidents->get_liste_eleves_par_classe());
  }

  public function get_infos_liste_individus($liste_eleves) {

    if (isset($_SESSION['individus'])) {
      foreach($_SESSION['individus'] as $individu) {
        $this->infos_individus[$individu[0]]=$this->get_infos_individu($individu[0],$individu[1]);
      }
    }
    if($liste_eleves) foreach($liste_eleves as $classe) {
        foreach($classe as $login) {
          $this->infos_individus[$login]=$this->get_infos_individu($login,'eleves');
        }
      }
    return($this->infos_individus);
  }
}
?>
