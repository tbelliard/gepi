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
require_once ("Controleur.php");
require_once ("Class.Stats.php");
require_once("Class.Filter.php");
require_once("Class.Periodes.php");
require_once("Class.Individu.php");
require_once("Class.Incidents.php");
require_once("Class.Evolution_Incidents.php");
require_once("SelectCtrl.php");
require_once("../../lib/CsvClass.php");

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
  private $libelles_categories=Null;
  private $libelles_mesures=Null;
  private $liste_type=Null;
  private $evolution=Null;
  private $incidents_mois=Null;
  private $totaux_par_mois=Null;
  private $totaux_par_type=Null;
  private $total_general=Null;
  private $top_incidents=Null;
  private $top_sanctions=Null;
  private $top_retenues=Null;
  private $top_exclusions=Null;
 

  function  __construct() {
    parent::__construct();
    $this->objet_periodes=new ClassPeriodes();
    $_SESSION['choix_evolution']=isset($_SESSION['choix_evolution'])?$_SESSION['choix_evolution']:'Catégories';
    $this->choix_evolution=isset($_POST['evolution'])?$_POST['evolution']:(isset($_GET['evolution'])?$_GET['evolution']:Null);
    if( $this->choix_evolution) $_SESSION['choix_evolution']=$this->choix_evolution;
    $this->current_onglet=isset($_SESSION['current_onglet']['id'])?$_SESSION['current_onglet']['id']:0;
    $this->temp=get_user_temp_directory();    
  }

  public function affiche_bilans() {
    try {
      $this->teste_selection();
      $this->action_from='affiche_bilans';
      $_SESSION['current_onglet']['from']='affiche_bilans';
      $this->vue->setVar('action_from',$this->action_from);
      $this->traite_filtres();
      $this->traite_incidents_bilans();
      $this->affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
      $this->vue->setVar('affichage_etab',$this->affichage_etab);
      $this->vue->setVar('temp_dir',$this->temp);
      $this->vue->afficheVue('bilans.php',$this->vue->getVars());
      echo"<script type='text/javascript'>inittab('$this->current_onglet');</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
  }

  public function set_onglet() {
    $_SESSION['current_onglet']['id']=isset($_GET['current_onglet'])?$_GET['current_onglet']:Null;
  }
  private function teste_selection() {
    if (!isset($_SESSION['etab_all'])&& !isset($_SESSION['eleve_all'])
            && !isset($_SESSION['pers_all'])
            && !isset($_SESSION['stats_classes_selected'])
            && !isset($_SESSION['individus']))
      echo"<script type='text/javascript'>document.location.href='index.php?ctrl=error&action=select'</script>";
  }
  public function affiche_details() {
    $_SESSION['mode_detaille']=isset($_POST['value'])? $_POST['value']:(isset($_GET['value'])? $_GET['value']:Null);
    $this->redirect();
  }

  public function choix_filtres() {
    $this->action_from=isset($_POST['action_from'])?$_POST['action_from']:(isset($_GET['action_from'])?$_GET['action_from']:Null);
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
    $_SESSION['filtre']['roles']=isset($_POST['roles'])?$_POST['roles']:(isset($_GET['roles'])?$_GET['roles']:Null);
    $_SESSION['filtre']['categories']=isset($_POST['categories'])?$_POST['categories']:(isset($_GET['categories'])?$_GET['categories']:Null);
    $_SESSION['filtre']['mesures']=isset($_POST['mesures'])?$_POST['mesures']:(isset($_GET['mesures'])?$_GET['mesures']:Null);
    $_SESSION['filtre']['sanctions']=isset($_POST['sanctions'])?$_POST['sanctions']:(isset($_GET['sanctions'])?$_GET['sanctions']:Null);
    $this->redirect();
  }

  private function redirect() {
    $this->action_from=isset($_POST['action_from'])?$_POST['action_from']:(isset($_GET['action_from'])?$_GET['action_from']:Null);
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
    $this->type=isset($_POST['type'])?$_POST['type']:(isset($_GET['type'])?$_GET['type']:Null);
    $this->choix=isset($_POST['choix'])?$_POST['choix']:(isset($_GET['choix'])?$_GET['choix']:Null);
    $this->objet_filtre=new ClassFilter();
    $this->objet_filtre->traite_maj_filtre($this->type,$this->choix);
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
    $this->liste_eleves=$this->objet_incidents->get_liste_eleves_par_classe();
    $this->infos_individus=$this->objet_incidents->get_infos_individus();
    $this->vue->setVar('infos_individus', $this->infos_individus);
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

  public function evolutions() {
    try {
      $this->teste_selection();      
      if (isset($_SESSION['current_onglet']['id'])) {
        if ($_SESSION['current_onglet']['from']!=='evolutions') {
          $this->adapt_onglet=explode("-onglet-",$_SESSION['current_onglet']['id']);
          $this->current_onglet=$this->adapt_onglet[0].'-onglet-01';
        }
      }
      $this->months=$this->objet_periodes->get_months();
      $this->vue->setVar('months',$this->months);
      $this->action_from='evolutions';
      $_SESSION['current_onglet']['from']='evolutions';
      $this->vue->setVar('action_from',$this->action_from);
      $this->affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
      $this->vue->setVar('affichage_etab',$this->affichage_etab);
      $this->vue->setVar('choix_evolution', $this->choix_evolution);
      $this->traite_filtres();
      $this->evolution_incidents=new ClassEvolution_Incidents();
      $this->evolution_incidents->traite_incidents_evolutions($this->filtres_roles,$this->filtres_categories,$this->filtres_mesures,$this->filtres_sanctions);
      $this->incidents_mois=$this->evolution_incidents->get_incidents_mois();
      $this->liste_type=$this->evolution_incidents->get_liste_type();
      $this->evolution=$this->evolution_incidents->get_evolution();
      $_SESSION['evolution']=$this->evolution;
      $this->totaux_par_mois=$this->evolution_incidents->get_totaux_par_mois();
      $this->totaux_par_type=$this->evolution_incidents->get_totaux_par_type();
      $this->total_general=$this->evolution_incidents->get_total_general();
      $this->infos_individus=$this->evolution_incidents->get_infos_individus();
      $this->vue->setVar('infos_individus', $this->infos_individus);
      $this->vue->setVar('incidents', $this->incidents_mois);
      $this->vue->setVar('liste_type', $this->liste_type);
      $this->vue->setVar('evolution', $this->evolution);
      $this->vue->setVar('totaux_par_mois', $this->totaux_par_mois);
      $this->vue->setVar('totaux_par_type', $this->totaux_par_type);
      $this->vue->setVar('total_general', $this->total_general);
      $this->vue->afficheVue('evolutions.php',$this->vue->getVars());
      echo"<script type='text/javascript'>inittab('$this->current_onglet');</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
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
      //echo"<script type='text/javascript'>inittab();</script>";
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
  }

  public function add_selection() {
    $this->login_to_add=isset($_POST['login'])?$_POST['login']:(isset($_GET['login'])?$_GET['login']:Null);
    $this->objet_select=new SelectCtrl();
    $this->objet_select->set_individus_selected($this->login_to_add,'eleves');
    $this->current_onglet=$this->login_to_add.'-onglet-01';
    $_SESSION['current_onglet']['id']=$this->current_onglet;
    $_SESSION['current_onglet']['from']="top";
    $this->affiche_bilans();
  }

  public function make_csv() {
    $this->name=isset($_GET['onglet'])?$_GET['onglet']:Null;
    $this->name=stripslashes($this->name);        
    $this->csv=new CsvClass(($this->name),'../../temp/'.$this->temp.'/');
    $this->csv->set_data($this->make_array_for_csv($this->name));
  }

  private function make_array_for_csv($name) {
    $this->traite_filtres();
    $this->traite_incidents_bilans();
    $csv=Array('Nom;Prenom;Incidents;Mesures prises;% Mesures prises;Sanctions prises;% sanctions prises;Heures de retenues;% heures retenues;Jours d\'exclusion;% jours d\'exclusion');
    foreach ($this->liste_eleves[$name] as $eleve) {
      if(!isset($this->totaux_indiv[$eleve]['mesures']))$this->totaux_indiv[$eleve]['mesures']=0;
      if(!isset($this->totaux_indiv[$eleve]['sanctions']))$this->totaux_indiv[$eleve]['sanctions']=0;
      if(!isset($this->totaux_indiv[$eleve]['heures_retenues']))$this->totaux_indiv[$eleve]['heures_retenues']=0;
      if(!isset($this->totaux_indiv[$eleve]['jours_exclusions']))$this->totaux_indiv[$eleve]['jours_exclusions']=0;
      if(!$this->totaux['L\'Etablissement']['mesures'])$this->totaux['L\'Etablissement']['mesures']=0;
      if(!$this->totaux['L\'Etablissement']['sanctions'])$this->totaux['L\'Etablissement']['sanctions']=0;
      if(!$this->totaux['L\'Etablissement']['heures_retenues'])$this->totaux['L\'Etablissement']['heures_retenues']=0;
      if(!$this->totaux['L\'Etablissement']['jours_exclusions'])$this->totaux['L\'Etablissement']['jours_exclusions']=0;
      $this->totaux_indiv[$eleve]['%mesures']=round(100*($this->totaux_indiv[$eleve]['mesures']/$this->totaux['L\'Etablissement']['mesures']),2);
      $this->totaux_indiv[$eleve]['%sanctions']=round(100*($this->totaux_indiv[$eleve]['sanctions']/$this->totaux['L\'Etablissement']['sanctions']),2);
      $this->totaux_indiv[$eleve]['%heures_retenues']=round(100*($this->totaux_indiv[$eleve]['heures_retenues']/$this->totaux['L\'Etablissement']['heures_retenues']),2);
      $this->totaux_indiv[$eleve]['%jours_exclusions']=round(100*($this->totaux_indiv[$eleve]['jours_exclusions']/$this->totaux['L\'Etablissement']['jours_exclusions']),2);


      $csv[]=($this->totaux_indiv[$eleve]['nom'].';'.$this->totaux_indiv[$eleve]['prenom'].';'.$this->totaux_indiv[$eleve]['incidents'].';'.$this->totaux_indiv[$eleve]['mesures'].';'.$this->totaux_indiv[$eleve]['%mesures']
                      .';'.$this->totaux_indiv[$eleve]['sanctions'].';'.$this->totaux_indiv[$eleve]['%sanctions']
                      .';'.$this->totaux_indiv[$eleve]['heures_retenues'].';'.$this->totaux_indiv[$eleve]['%heures_retenues']
                      .';'.$this->totaux_indiv[$eleve]['jours_exclusions'].';'.$this->totaux_indiv[$eleve]['%jours_exclusions']);
    }
    return $csv;
  }

  private function count_onglets() {
    $this->nbre_onglets=0;
    if (isset($_SESSION['etab_all'])) $this->nbre_onglets=$this->nbre_onglets+2;
    if (isset($_SESSION['eleve_all'])) $this->nbre_onglets=$this->nbre_onglets+2;
    if (isset($_SESSION['pers_all'])) $this->nbre_onglets=$this->nbre_onglets+2;
    if (isset($_SESSION['stats_classes_selected'])) $this->nbre_onglets=$this->nbre_onglets+2*count($_SESSION['stats_classes_selected']);
    if (isset($_SESSION['individus'])) {
      foreach ($_SESSION['individus']as $individu) {
        if ($individu[1]=='eleves')$this->nbre_onglets=$this->nbre_onglets+2;
        else $this->nbre_onglets=$this->nbre_onglets+1;
      }
    }
    return($this->nbre_onglets);
  }
}