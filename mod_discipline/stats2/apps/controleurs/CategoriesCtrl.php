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

$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");
require_once("Class.Date.php");
require_once("Modele.Incidents.php");
require_once ("Controleur.php");


class CategoriesCtrl extends Controleur {
  private $modele_incidents=Null;
  private $natures;
  private $liste_categories=Null;
  private $liste_natures=Null;
  function  __construct() {
    parent::__construct();
    $this->modele_incidents=new Modele_Incidents();

  }
  function index() {

    $this->natures=$this->modele_incidents->get_infos_natures();
    $this->liste_categories=$this->modele_incidents->get_infos_categories();
    $this->liste_natures=$this->add_infos_categories($this->natures,$this->liste_categories);
    $this->vue->setVar('liste_categories', $this->liste_categories);
    $this->vue->setVar('liste_natures', $this->liste_natures);
    $this->vue->afficheVue('categories.php',$this->vue->getVars());
    echo"<script type='text/javascript'>initSortable();</script>";
  }
  private function add_infos_categories($liste_nat,$liste_cat) {
    foreach($liste_nat as $nature) {
      if (!$nature->id_categorie) {
        $nature->categorie='Non affecté';
        $nature->categorie_sigle='Non affecté';
      } else {
        foreach($liste_cat as $categorie) {
          if($categorie->id==$nature->id_categorie) {
            $nature->categorie=$categorie->categorie;
            $nature->categorie_sigle=$categorie->sigle;
          }
        }
      }
    }
    return $liste_nat;
  }
  function save() {
    check_token(false);
    $this->natures_selected=isset($_POST['natures_incidents'])?$_POST['natures_incidents']:(isset($_GET['natures_incidents'])?$_GET['natures_incidents']:Null);
    if(is_null($this->natures_selected)) {
      echo"<script type='text/javascript'>alert('Selectionnez des natures d\'incidents et une catégorie')</script>";
    }
    else {
      $this->categorie_selected=isset($_POST['categorie'])?$_POST['categorie']:(isset($_GET['categorie'])?$_GET['categorie']:Null);
      $this->modele_incidents->update_categorie($this->categorie_selected,$this->natures_selected);
    }
    $this->index();
  }

  function delete() {
    check_token(false);
    $this->nature_selected=isset($_POST['nature'])?$_POST['nature']:(isset($_GET['nature'])?$_GET['nature']:Null);
    $this->nature[]=html_entity_decode($this->nature_selected,ENT_QUOTES);
    $this->categorie=isset($_POST['categorie_id'])?$_POST['categorie_id']:(isset($_GET['categorie_id'])?$_GET['categorie_id']:Null);
    if($this->categorie) {
      $this->modele_incidents->update_categorie('default',Null,$this->categorie);
    }else {
      $this->modele_incidents->update_categorie('default',$this->nature);
    }
    $this->index();
  }
}
?>
