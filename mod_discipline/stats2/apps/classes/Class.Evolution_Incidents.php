<?php
/*
 *
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

require_once("Class.Date.php");
require_once("Modele.Incidents.php");
require_once("Modele.Select.php");
require_once("Class.Incidents.php");
require_once("Class.Filter.php");
require_once("Class.Individu.php");

class ClassEvolution_Incidents extends ClassIncidents {
  private $months=Null;
  private $liste_categories=Null;
  private $liste_type=Null;
  private $evolution=Null;
  private $libelles_categories=Null;
  private $libelles_mesures=Null;
  private $incidents_mois=Null;
  private $totaux_par_mois=Null;
  private $totaux_par_type=Null;
  private $total_general=Null;
  private $infos_individus=Null;
  private $filtres_sanctions=Null;
  private $filtres_roles=Null;
  
  public function  __construct() {
    $this->modele_incidents=new Modele_Incidents();
    $this->modele_select=new modele_select();
    $this->objet_periodes=new ClassPeriodes();
    $this->objet_filtre=new ClassFilter();
    $this->months=$this->objet_periodes->get_months();
  }


  public function get_liste_type() {
    return $this->liste_type;
  }
  public function get_evolution() {
    return $this->evolution;
  }
  public function get_incidents_mois() {
    return $this->incidents_mois;
  }
  public function get_totaux_par_mois() {
    return $this->totaux_par_mois;
  }
  public function get_totaux_par_type() {
    return $this->totaux_par_type;
  }
  public function get_total_general() {
    return $this->total_general;
  }
  public function get_infos_individus() {
    return $this->infos_individus;
  }

  public function traite_incidents_evolutions($filtres_roles=Null,$filtres_categories=Null,$filtres_mesures=Null,$filtres_sanctions=Null) {

    switch($_SESSION['choix_evolution']) {
      case 'Catégories':
        $this->traite_evolution_categories($filtres_roles,$filtres_categories,$filtres_mesures,$filtres_sanctions);
        break;
      case 'Mesures prises':
        $this->traite_evolution_mesures($filtres_roles,$filtres_categories,$filtres_mesures,$filtres_sanctions);
        break;
      case 'Sanctions':
        $this->traite_evolution_sanctions($filtres_roles,$filtres_categories,$filtres_mesures,$filtres_sanctions);
        break;
      case 'Rôles':
        $this->traite_evolution_roles($filtres_roles,$filtres_categories,$filtres_mesures,$filtres_sanctions);
        break;
    }
  }

  private function traite_evolution_categories($filtres_roles=Null,$filtres_categories=Null,$filtres_mesures=Null,$filtres_sanctions=Null) {
    $this->liste_categories=$this->objet_filtre->get_liste_categories();
    //on cree la liste type
    if($filtres_categories) {
      $this->libelles_categories=$this->objet_filtre->get_libelles_categories();
      foreach($this->libelles_categories as $categorie) {
        $this->liste_type[]=$categorie;
      }
    } else {
      foreach($this->liste_categories as $categorie) {
        $this->liste_type[]=$categorie->categorie;
      }
    }
    //On traite mois par mois
    foreach($this->months as $key=>$month) {
      $this->objet_incident=new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incident->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $filtres_categories,$filtres_mesures,$filtres_sanctions,$filtres_roles);
      $this->incidents_mois=$this->objet_incident->get_incidents();
      //puis selection par selection
      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $categorie) {
          $this->evolution[$selection][$categorie][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$categorie])) $this->totaux_par_type[$selection][$categorie]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_general[$selection])) $this->total_general[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (is_null($incident->id_categorie))$incident->sigle_categorie='NA';
            $this->evolution[$selection][$this->objet_filtre->get_categorie_from_sigle($incident->sigle_categorie)][$key]+=1;
            $this->totaux_par_type[$selection][$this->objet_filtre->get_categorie_from_sigle($incident->sigle_categorie)]+=1;
            $this->totaux_par_mois[$selection][$key]+=1;
            $this->total_general[$selection]+=1;
          }
        }
      }
    }
    $this->infos_individus=$this->objet_incident->get_infos_individus();
  }

  private function traite_evolution_mesures($filtres_roles=Null,$filtres_categories=Null,$filtres_mesures=Null,$filtres_sanctions=Null) {
    $this->liste_mesures=$this->objet_filtre->get_liste_mesures();
    //on cree la liste type
    if($filtres_mesures) {
      $this->libelles_mesures=$this->objet_filtre->get_libelles_mesures();
      foreach($this->libelles_mesures as $mesure) {
        $this->liste_type[]=$mesure;
      }
    } else {
      foreach($this->liste_mesures as $mesure) {
        $this->liste_type[]=$mesure->mesure;
      }
    }
    //On traite mois par mois
    foreach($this->months as $key=>$month) {
      $this->objet_incident=new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incident->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $filtres_categories,$filtres_mesures,$filtres_sanctions,$filtres_roles);
      $this->incidents_mois=$this->objet_incident->get_incidents();      
      $this->mesures_mois=$this->objet_incident->get_mesures();

      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $mesure) {
          $this->evolution[$selection][$mesure][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$mesure])) $this->totaux_par_type[$selection][$mesure]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_general[$selection])) $this->total_general[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (isset($this->mesures_mois[$incident->id_incident])) {
              foreach($this->mesures_mois[$incident->id_incident] as $protagoniste) {
                  //var_dump($_SESSION['stats_classes_selected']);
                foreach($protagoniste as $id_mesure) {
                  if($selection !='L\'Etablissement' && $selection !='Tous les élèves'&& $selection !='Tous les personnels') {
                     //on a une classe ou un eleve
                    if($this->is_classe($selection)){
                       if(!$this->is_in_classe($id_mesure->login_ele, $selection)){
                         break;  //la mesure ne correspond pas à un eleve de la classe
                       }
                    }else{
                        //on a un eleve on verifie si la mesure est à lui
                        if($id_mesure->login_ele!=$selection)break;
                    }
                  }
                  if($id_mesure->type=='prise') {
                    //si le type n'est pas initialisée on le fait
                    if(!in_array($id_mesure->mesure,$this->liste_type)) {
                      foreach($this->months as $key2=>$month2) {
                        $this->evolution[$selection][$id_mesure->mesure][$key2]=0;
                      }
                      $this->totaux_par_type[$selection][$id_mesure->mesure]=0;
                      $this->liste_type[]=$id_mesure->mesure;
                    }
                    $this->evolution[$selection][$id_mesure->mesure][$key]+=1;
                    $this->totaux_par_type[$selection][$id_mesure->mesure]+=1;
                    $this->totaux_par_mois[$selection][$key]+=1;
                    $this->total_general[$selection]+=1;
                  }
                }
              }
            }
          }
        }
      }
    }
    $this->infos_individus=$this->objet_incident->get_infos_individus();
  }

  private function traite_evolution_sanctions($filtres_roles=Null,$filtres_categories=Null,$filtres_mesures=Null,$filtres_sanctions=Null) {
    $this->liste_sanctions=$this->objet_filtre->get_liste_sanctions();
    $this->liste_sanctions=array_reverse($this->liste_sanctions);
    $objet =new stdClass();
    $objet->nature='exclusion';
    $this->liste_sanctions[]=$objet;
    $objet =new stdClass();
    $objet->nature='retenue';
    $this->liste_sanctions[]=$objet;
    $objet =new stdClass();
    $objet->nature='travail';
    $this->liste_sanctions[]=$objet;
    $this->liste_sanctions=array_reverse($this->liste_sanctions);

    //on cree la liste type
    if($filtres_sanctions) {
      $this->filtres_sanctions=$this->objet_filtre->get_natures_sanctions_selected();
      foreach($this->filtres_sanctions as $sanction) {
        $this->liste_type[]=$sanction;
      }
    } else {
      foreach($this->liste_sanctions as $sanction) {
        $this->liste_type[]=$sanction->nature;
      }
    }
    //On traite mois par mois
    foreach($this->months as $key=>$month) {
      $this->objet_incident= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incident->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $filtres_categories,$filtres_mesures,$filtres_sanctions,$filtres_roles);
      $this->incidents_mois=$this->objet_incident->get_incidents();
      $this->sanctions_mois=$this->objet_incident->get_sanctions();

      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $sanction) {
          $this->evolution[$selection][$sanction][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$sanction])) $this->totaux_par_type[$selection][$sanction]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_general[$selection])) $this->total_general[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (isset($this->sanctions_mois[$incident->id_incident])) {
              foreach($this->sanctions_mois[$incident->id_incident] as $protagoniste) {
                foreach($protagoniste as $id_sanction) {
                   if($selection !='L\'Etablissement' && $selection !='Tous les élèves'&& $selection !='Tous les personnels') {
                     //on a une classe ou un eleve
                    if($this->is_classe($selection)){
                       if(!$this->is_in_classe($id_sanction->login, $selection)){
                         break;  //la mesure ne correspond pas à un eleve de la classe
                       }
                    }else{
                        //on a un eleve on verifie si la mesure est à lui
                        if($id_sanction->login!=$selection)break;
                    }
                  }
                  //si le type n'est pas initialisée on le fait
                  if(!in_array($id_sanction->nature,$this->liste_type)) {
                    foreach($this->months as $key2=>$month2) {
                      $this->evolution[$selection][$id_sanction->nature][$key2]=0;
                    }
                    $this->totaux_par_type[$selection][$id_sanction->nature]=0;
                    $this->liste_type[]=$id_sanction->nature;
                  }
                  $this->evolution[$selection][$id_sanction->nature][$key]+=1;
                  $this->totaux_par_type[$selection][$id_sanction->nature]+=1;
                  $this->totaux_par_mois[$selection][$key]+=1;
                  $this->total_general[$selection]+=1;

                }
              }
            }
          }
        }
      }
    }
    $this->infos_individus=$this->objet_incident->get_infos_individus();
  }
  private function traite_evolution_roles($filtres_roles=Null,$filtres_categories=Null,$filtres_mesures=Null,$filtres_sanctions=Null) {
    $this->liste_roles=$this->objet_filtre->get_liste_roles();
    //on cree la liste type
    if($filtres_roles) {
      $this->filtres_roles=$this->objet_filtre->get_roles_selected();
      foreach($this->filtres_roles as $role) {
        $this->liste_type[]=$role;
      }
    } else {
      foreach($this->liste_roles as $role) {
        $this->liste_type[]=$role->qualite;
      }
      $this->liste_type[]='Non défini';
    }

    //On traite mois par mois
    foreach($this->months as $key=>$month) {
      $this->objet_incident= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incident->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $filtres_categories,$filtres_mesures,$filtres_sanctions,$filtres_roles);
      $this->incidents_mois=$this->objet_incident->get_incidents();
      $this->protagonistes_mois=$this->objet_incident->get_protagonistes();

      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $role) {
          $this->evolution[$selection][$role][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$role])) $this->totaux_par_type[$selection][$role]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_general[$selection])) $this->total_general[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if(isset($this->protagonistes_mois[$incident->id_incident])) {
              foreach($this->protagonistes_mois[$incident->id_incident] as $protagoniste) {
                if ($protagoniste->qualite =='')$protagoniste->qualite='Non défini';
                //si le type n'est pas initialisée on le fait
                if(!in_array($protagoniste->qualite,$this->liste_type)) {
                  foreach($this->months as $key2=>$month2) {
                    $this->evolution[$selection][$protagoniste->qualite][$key2]=0;
                  }
                  $this->totaux_par_type[$selection][$protagoniste->qualite]=0;
                  $this->liste_type[]=$protagoniste->qualite;
                }
                if($selection !='L\'Etablissement' && $selection !='Tous les élèves'&& $selection !='Tous les personnels') {
                     //on a une classe ou un eleve
                    if($this->is_classe($selection)){
                       if(!$this->is_in_classe($protagoniste->login, $selection)){
                         break;  //la mesure ne correspond pas à un eleve de la classe
                       }
                    }else{
                        //on a un eleve on verifie si la mesure est à lui
                        if($protagoniste->login!=$selection)break;
                    }
                  }
                $this->evolution[$selection][$protagoniste->qualite][$key]+=1;
                $this->totaux_par_type[$selection][$protagoniste->qualite]+=1;
                $this->totaux_par_mois[$selection][$key]+=1;
                $this->total_general[$selection]+=1;
              }
            }
          }
        }
      }
    }
    $this->infos_individus=$this->objet_incident->get_infos_individus();
  }

  private function is_classe($selection){
   $test=false;
   $modele_select=new modele_select();
   foreach($_SESSION['stats_classes_selected'] as $id_classe){
       $infos_classe=$modele_select->get_infos_classe($id_classe);
       if($infos_classe[0]['classe']==$selection){
           $test=true;
           break;
       }
   }
   return $test;
  }

  private function is_in_classe($login_ele,$classe){
   $test=false;
   $modele_select=new modele_select();
   $id_classe=$modele_select->get_id_from_classe($classe);
   $login_eleves=$modele_select->get_eleves_classe($id_classe);
   if(in_array($login_ele,$login_eleves)){
       $test=true;
   }
   return($test);
  }
}
?>
