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

class ClassFilter {
  private $modele_incidents=Null;
  private $liste_sanctions=Null;
  private $liste_roles=Null;
  private $liste_categories=Null;
  private $liste_mesures=Null;
  private $libelles=Null;
  private $libelles_categories=Null;
  private $libelles_mesures=Null;
  private $id_categories_selected;
  private $id_mesures_selected;

  function  __construct() {
    $this->modele_incidents=new Modele_Incidents();
    $this->get_params_module();
    $this->roles_selected=isset($_SESSION['filtre']['roles'])?$_SESSION['filtre']['roles']:Null;
    $this->id_categories_selected=isset($_SESSION['filtre']['categories'])?$_SESSION['filtre']['categories']:Null;
    $this->id_mesures_selected=isset($_SESSION['filtre']['mesures'])?$_SESSION['filtre']['mesures']:Null;
    $this->natures_sanctions_selected=isset($_SESSION['filtre']['sanctions'])?$_SESSION['filtre']['sanctions']:Null;

  }

  public function traite_maj_filtre($type,$choix=Null) {
    if (!isset($choix)) unset($_SESSION['filtre'][$type]);
    else {
      switch ($type) {
        case'categories':
          foreach($this->get_liste_categories() as $categorie) {
            if ($categorie->categorie==$choix) {
              foreach($_SESSION['filtre']['categories'] as $key=>$value) {
                if($categorie->id==$value) {
                  unset($_SESSION['filtre']['categories'][$key]);
                }
              }
            }
          }
          break;
        case'mesures':
          foreach($this->get_liste_mesures() as $mesure) {
            if ($mesure->mesure==$choix) {
              foreach($_SESSION['filtre']['mesures'] as $key=>$value) {
                if($mesure->id==$value) {
                  unset($_SESSION['filtre']['mesures'][$key]);
                }
              }
            }
          }
          break;
        case'sanctions':
          foreach($_SESSION['filtre']['sanctions'] as $key=>$value) {
            if($choix==$value) {
              unset($_SESSION['filtre']['sanctions'][$key]);
            }
          }
          break;
        case'roles':
          foreach($_SESSION['filtre']['roles'] as $key=>$value) {
            if($choix==$value) {
              unset($_SESSION['filtre']['roles'][$key]);
            }
          }
          break;
      }
    }
  }

  // On recupère les infos du module discipline sur sanctions,rôles......
  private function get_params_module() {

    $this->liste_sanctions=$this->modele_incidents->get_types_sanctions();
    $this->liste_roles=$this->modele_incidents->get_types_roles();
    $this->liste_categories=$this->add_choix_null($this->modele_incidents->get_infos_categories(),'categorie','Non affecté','NA');
    $this->liste_mesures=$this->modele_incidents->get_types_mesures();    
  }
  
  private function add_choix_null($liste,$propriete,$valeur,$sigle=Null) {
    $objet=new stdClass();
    $objet->id='Null';
    $objet->$propriete=$valeur;
    if($sigle)$objet->sigle=$sigle;
    $liste[]=$objet;
    return($liste);
  } // fin des infos


   public function get_libelles_categories(){
    return ($this->libelles_categories=$this->get_libelle_from_id('categorie',$this->id_categories_selected,$this->liste_categories));
  }
  public function get_libelles_mesures(){
    return ($this->libelles_mesures=$this->get_libelle_from_id('mesure',$this->id_mesures_selected,$this->liste_mesures));
  }

  private function get_libelle_from_id($type,$array_filtres,$array_libelles) {
    unset ($this->libelles);
    foreach($array_filtres as $value_filtre) {
      foreach ($array_libelles as $value_libelle) {
        if ($value_libelle->id==$value_filtre)
          $this->libelles[]=$value_libelle->$type;
      }
    }
    return($this->libelles);
  }

 public function get_categorie_from_sigle($sigle){
   foreach($this->liste_categories as $categorie){
     if ($categorie->sigle==$sigle) return $categorie->categorie;
   }
 }
  public function get_roles_selected() {
    return $this->roles_selected;
  }
  public function get_id_categories_selected() {
    return $this->id_categories_selected;
  }
  public function get_id_mesures_selected() {
    return $this->id_mesures_selected;
  }
  public function get_natures_sanctions_selected() {
    return $this->natures_sanctions_selected;
  }

  public function get_liste_sanctions(){
    return $this->liste_sanctions;
  }

  public function get_liste_mesures(){
   return $this->liste_mesures;
  }

  public function get_liste_roles(){
    return $this->liste_roles;
  }

  public function get_liste_categories(){
    return $this->liste_categories;
  }


}
?>
