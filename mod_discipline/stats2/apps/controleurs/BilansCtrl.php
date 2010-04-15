<?php
/*
 * $Id$
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
$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");
require_once("Class.Date.php");
require_once ("Controleur.php");
require_once ("Class.Stats.php");
require_once("Class.Filter.php");
require_once("Class.Periodes.php");
require_once("Class.Individu.php");
require_once("Class.Incidents.php");


class BilansCtrl extends Controleur {

  private $objet_filtre=Null;
  private $objet_incidents=Null;
  private $objet_periodes=Null;
  private $filtres_roles=Null;
  private $filtres_categories=Null;
  private $filtres_mesures=Null;
  private $filtres_sanctions=Null;
  private $incidents=Null;
  private $protagonistes=Null;
  private $mesures=Null;
  private $sanctions=Null;
  private $liste_categories=Null;
  private $months=Null;
  private $evolution=Null;
  private $libelles_categories=Null;
  private $liste_type=Null;
  private $top_incidents=Null;
  private $top_sanctions=Null;
  private $top_retenues=Null;
  private $top_exclusions=Null;

  function  __construct() {
    parent::__construct();
    $this->objet_periodes=new ClassPeriodes();
    $_SESSION['choix_evolution']=isset($_SESSION['choix_evolution'])?$_SESSION['choix_evolution']:'Catégories';
    $this->choix_evolution=isset($_REQUEST['evolution'])?$_REQUEST['evolution']:Null;
    if( $this->choix_evolution) $_SESSION['choix_evolution']=$this->choix_evolution;
  }

  function affiche_bilans() {
    try {
      $this->teste_selection();
      $this->action_from='affiche_bilans';
      $this->vue->setVar('action_from',$this->action_from);
      $this->traite_filtres();
      $this->traite_incidents_bilans();
      $this->affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
      $this->vue->setVar('affichage_etab',$this->affichage_etab);
      $this->vue->afficheVue('bilans.php',$this->vue->getVars());
      echo"<script type='text/javascript'>inittab();</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
  }

  private function teste_selection() {
    if (!isset($_SESSION['etab_all'])&& !isset($_SESSION['eleve_all'])
            && !isset($_SESSION['pers_all'])
            && !isset($_SESSION['stats_classes_selected'])
            && !isset($_SESSION['individus']))
      echo"<script type='text/javascript'>document.location.href='index.php?ctrl=error&action=select'</script>";
  }
  public function affiche_details() {
    $_SESSION['mode_detaille']=isset($_REQUEST['value'])? $_REQUEST['value']:Null ;
    $this->redirect();
  }

  public function choix_filtres() {
    $this->action_from=isset($_REQUEST['action_from'])?$_REQUEST['action_from']:Null;
    $this->vue->setVar('action_from',$this->action_from);
    $this->traite_filtres();
    $this->liste_categories=$this->objet_filtre->get_liste_categories();
    $this->liste_sanctions=$this->objet_filtre->get_liste_sanctions();
    $this->liste_roles=$this->objet_filtre->get_liste_roles();
    $this->liste_mesures=$this->objet_filtre->get_liste_mesures();
    $this->vue->setVar('categories',$this->liste_categories);
    $this->vue->setVar('sanctions',$this->liste_sanctions);
    $this->vue->setVar('roles',$this->liste_roles);
    $this->vue->setVar('mesures',$this->liste_mesures);
    $this->vue->afficheVue('filtre.php',$this->vue->getVars());

  }
  public function filtrer() {
    $_SESSION['filtre']['roles']=(isset($_REQUEST['roles'])?$_REQUEST['roles']:Null);
    $_SESSION['filtre']['categories']=(isset($_REQUEST['categories'])?$_REQUEST['categories']:Null);
    $_SESSION['filtre']['mesures']=(isset($_REQUEST['mesures'])?$_REQUEST['mesures']:Null);
    $_SESSION['filtre']['sanctions']=(isset($_REQUEST['sanctions'])?$_REQUEST['sanctions']:Null);
    $this->redirect();
  }

  private function redirect() {
    $this->action_from=isset($_REQUEST['action_from'])?$_REQUEST['action_from']:Null;
    switch($this->action_from) {
      case 'evolutions':
        $this->evolutions();
        break;
      case 'affiche_bilans':
        $this->affiche_bilans();
        break;
      case 'top':
        $this->top();
        break;
      default:
        $this->affiche_bilans();
    }
  }

  public function maj_filtre() {
    $this->type=isset($_REQUEST['type'])?$_REQUEST['type']:Null;
    $this->choix=isset($_REQUEST['choix'])?$_REQUEST['choix']:Null;
    $this->traite_maj_filtre($this->type,$this->choix);
    $this->redirect();
  }

  private function traite_filtres() {
    $this->objet_filtre=new  ClassFilter();
    $this->filtres_roles=$this->objet_filtre->get_roles_selected();
    $this->filtres_categories=$this->objet_filtre->get_id_categories_selected();
    $this->filtres_mesures=$this->objet_filtre->get_id_mesures_selected();
    $this->filtres_sanctions=$this->objet_filtre->get_natures_sanctions_selected();
    $this->vue->setVar('filtres_roles',$this->filtres_roles);
    $this->vue->setVar('filtres_mesures',$this->filtres_mesures);
    $this->vue->setVar('filtres_sanctions',$this->filtres_sanctions);
    $this->vue->setVar('filtres_categories',$this->filtres_categories);
    if($this->filtres_categories) {
      $this->libelles_categories=$this->objet_filtre->get_libelles_categories();
      $this->vue->setVar('libelles_categories',$this->libelles_categories);
    }
    if($this->filtres_mesures) {
      $this->libelles_mesures=$this->objet_filtre->get_libelles_mesures();
      $this->vue->setVar('libelles_mesures',$this->libelles_mesures);
    }
    $this->mode_detaille=isset($_SESSION['mode_detaille'])?$_SESSION['mode_detaille']:Null;
    $this->vue->setVar('mode_detaille', $this->mode_detaille);
  }

  private function traite_incidents_bilans() {
    $this->objet_incidents= new ClassIncidents();
    $this->objet_incidents->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']),Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']),$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
    $this->incidents=$this->objet_incidents->get_incidents();
    $this->protagonistes=$this->objet_incidents->get_protagonistes();
    $this->mesures=$this->objet_incidents->get_mesures();
    $this->sanctions=$this->objet_incidents->get_sanctions();
    $this->totaux=$this->objet_incidents->get_totaux();
    $this->liste_eleves=$this->objet_incidents->get_liste_eleves();
    $this->traite_infos_individus();
    $this->totaux_indiv=$this->objet_incidents->get_totaux_indiv();
    $this->totaux_par_classe=$this->objet_incidents->get_totaux_par_classe();
    $this->vue->setVar('incidents', $this->incidents);
    $this->vue->setVar('protagonistes', $this->protagonistes);
    $this->vue->setVar('mesures', $this->mesures);
    $this->vue->setVar('sanctions', $this->sanctions);
    $this->vue->setVar('totaux', $this->totaux);
    $this->vue->setVar('liste_eleves', $this->liste_eleves);
    $this->vue->setVar('totaux_indiv', $this->totaux_indiv);
    $this->vue->setVar('totaux_par_classe', $this->totaux_par_classe);
  }

  private function traite_infos_individus() {
    $this->infos_individus=$this->objet_incidents->get_infos_individus();
    $this->vue->setVar('infos_individus', $this->infos_individus);
  }


  private function traite_maj_filtre($type,$choix=Null) {
    $this->objet_filtre=new  ClassFilter();
    if (!isset($choix)) unset($_SESSION['filtre'][$type]);
    else {
      switch ($type) {
        case'categories':
          foreach($this->objet_filtre->get_liste_categories() as $categorie) {
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
          foreach($this->objet_filtre->get_liste_mesures() as $mesure) {
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

  public function evolutions() {
    try {
      $this->teste_selection();
      $this->months=$this->objet_periodes->get_months();
      $this->vue->setVar('months',$this->months);
      $this->action_from='evolutions';
      $this->vue->setVar('action_from',$this->action_from);
      $this->affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
      $this->vue->setVar('affichage_etab',$this->affichage_etab);
      $this->vue->setVar('choix_evolution', $this->choix_evolution);
      $this->traite_filtres();
      $this->traite_incidents_evolutions();
      $this->vue->afficheVue('evolutions.php',$this->vue->getVars());
      echo"<script type='text/javascript'>inittab();</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
  }

  private function traite_incidents_evolutions() {

    switch($_SESSION['choix_evolution']) {
      case 'Catégories':
        $this->traite_evolution_categories();
        break;
      case 'Mesures prises':
        $this->traite_evolution_mesures();
        break;
      case 'Sanctions':
        $this->traite_evolution_sanctions();
        break;
      case 'Rôles':
        $this->traite_evolution_roles();
        break;
    }
  }

  private function traite_evolution_categories() {
    $this->liste_categories=$this->objet_filtre->get_liste_categories();
    //on cree la liste type
    if($this->filtres_categories) {
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
      $this->objet_incidents= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incidents->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->incidents_mois=$this->objet_incidents->get_incidents();
      //puis selection par selection
      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $categorie) {
          $this->evolution[$selection][$categorie][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$categorie])) $this->totaux_par_type[$selection][$categorie]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_incidents[$selection])) $this->total_incidents[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (is_null($incident->id_categorie))$incident->sigle_categorie='NA';
            $this->evolution[$selection][$this->objet_filtre->get_categorie_from_sigle($incident->sigle_categorie)][$key]+=1;
            $this->totaux_par_type[$selection][$this->objet_filtre->get_categorie_from_sigle($incident->sigle_categorie)]+=1;
            $this->totaux_par_mois[$selection][$key]+=1;
            $this->total_incidents[$selection]+=1;
          }
        }
      }
    }
    $this->vue->setVar('incidents', $this->incidents_mois);
    $this->vue->setVar('liste_type', $this->liste_type);
    $this->vue->setVar('evolution', $this->evolution);
    $this->vue->setVar('totaux_par_mois', $this->totaux_par_mois);
    $this->vue->setVar('totaux_par_type', $this->totaux_par_type);
    $this->vue->setVar('total_general', $this->total_incidents);
    $this->traite_infos_individus();
  }

  private function traite_evolution_mesures() {
    $this->liste_mesures=$this->objet_filtre->get_liste_mesures();
    //on cree la liste type
    if($this->filtres_mesures) {
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
      $this->objet_incidents= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incidents->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->incidents_mois=$this->objet_incidents->get_incidents();
      $this->mesures_mois=$this->objet_incidents->get_mesures();

      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $mesure) {
          $this->evolution[$selection][$mesure][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$mesure])) $this->totaux_par_type[$selection][$mesure]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_mesures[$selection])) $this->total_mesures[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (isset($this->mesures_mois[$incident->id_incident])) {
              foreach($this->mesures_mois[$incident->id_incident] as $protagoniste) {
                foreach($protagoniste as $id_mesure) {
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
                    $this->total_mesures[$selection]+=1;
                  }
                }
              }
            }
          }
        }
      }
      $this->vue->setVar('incidents', $this->incidents_mois);
      $this->vue->setVar('liste_type', $this->liste_type);
      $this->vue->setVar('evolution', $this->evolution);
      $this->vue->setVar('totaux_par_mois', $this->totaux_par_mois);
      $this->vue->setVar('totaux_par_type', $this->totaux_par_type);
      $this->vue->setVar('total_general', $this->total_mesures);
      $this->traite_infos_individus();
    }
  }

  private function traite_evolution_sanctions() {
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
    if($this->filtres_sanctions) {
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
      $this->objet_incidents= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incidents->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->incidents_mois=$this->objet_incidents->get_incidents();
      $this->sanctions_mois=$this->objet_incidents->get_sanctions();
      // var_dump( $this->mesures_mois);
      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $sanction) {
          $this->evolution[$selection][$sanction][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$sanction])) $this->totaux_par_type[$selection][$sanction]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_sanctions[$selection])) $this->total_sanctions[$selection]=0;
        }
        //On compte les incidents du mois et le total global
        foreach($incidents as $titre=>$incident) {
          if(!$titre['error']) {
            if (isset($this->sanctions_mois[$incident->id_incident])) {
              foreach($this->sanctions_mois[$incident->id_incident] as $protagoniste) {
                foreach($protagoniste as $id_sanction) {
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
                  $this->total_sanctions[$selection]+=1;

                }
              }
            }
          }
        }
      }
      $this->vue->setVar('incidents', $this->incidents_mois);
      $this->vue->setVar('liste_type', $this->liste_type);
      $this->vue->setVar('evolution', $this->evolution);
      $this->vue->setVar('totaux_par_mois', $this->totaux_par_mois);
      $this->vue->setVar('totaux_par_type', $this->totaux_par_type);
      $this->vue->setVar('total_general', $this->total_sanctions);
      $this->traite_infos_individus();
    }
  }
  private function traite_evolution_roles() {
    $this->liste_roles=$this->objet_filtre->get_liste_roles();
    //on cree la liste type
    if($this->filtres_roles) {
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
      $this->objet_incidents= new ClassIncidents();
      $this->data_months=Gepi_Date::get_begin_end_month($key);
      $this->objet_incidents->traite_incidents_criteres(Gepi_Date::format_date_fr_iso($this->data_months['du']),Gepi_Date::format_date_fr_iso( $this->data_months['au']), $this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->incidents_mois=$this->objet_incidents->get_incidents();
      $this->protagonistes_mois=$this->objet_incidents->get_protagonistes();

      foreach( $this->incidents_mois as $selection=>$incidents) {
        //on met les compteurs à 0
        foreach($this->liste_type as $role) {
          $this->evolution[$selection][$role][$key]=0;
          if(!isset($this->totaux_par_type[$selection][$role])) $this->totaux_par_type[$selection][$role]=0;
          $this->totaux_par_mois[$selection][$key]=0;
          if(!isset($this->total_roles[$selection])) $this->total_roles[$selection]=0;
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
                $this->evolution[$selection][$protagoniste->qualite][$key]+=1;
                $this->totaux_par_type[$selection][$protagoniste->qualite]+=1;
                $this->totaux_par_mois[$selection][$key]+=1;
                $this->total_roles[$selection]+=1;
              }
            }
          }
        }
      }
      $this->vue->setVar('incidents', $this->incidents_mois);
      $this->vue->setVar('liste_type', $this->liste_type);
      $this->vue->setVar('evolution', $this->evolution);
      $this->vue->setVar('totaux_par_mois', $this->totaux_par_mois);
      $this->vue->setVar('totaux_par_type', $this->totaux_par_type);
      $this->vue->setVar('total_general', $this->total_roles);
      $this->traite_infos_individus();
    }

  }

  public function top() {
    try {
      $this->teste_selection();
      $this->action_from='top';
      $this->vue->setVar('action_from',$this->action_from);
      $this->affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
      $this->traite_filtres();
      $this->objet_incidents= new ClassIncidents();
      $this->top_incidents=$this->objet_incidents->get_top_incidents(Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']),Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']),$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->vue->setVar('top_incidents', $this->top_incidents);
      $this->top_sanctions=$this->objet_incidents->get_top_sanctions(Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']),Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']),$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->vue->setVar('top_sanctions', $this->top_sanctions);
      $this->top_retenues=$this->objet_incidents->get_top_retenues(Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']),Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']),$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->vue->setVar('top_retenues', $this->top_retenues);
      $this->top_exclusions=$this->objet_incidents->get_top_exclusions(Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']),Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']),$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions,$this->filtres_roles);
      $this->vue->setVar('top_exclusions', $this->top_exclusions);
      $this->vue->afficheVue('top.php',$this->vue->getVars());
      echo"<script type='text/javascript'>inittab();</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
  }

}