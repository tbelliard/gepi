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
require_once ("Controleur.php");
require_once("Class.Date.php");
require_once("Class.Periodes.php");
require_once("Class.Individu.php");
// Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");

class SelectCtrl extends Controleur {
  public $messages=Null;
  private $objet_periodes=Null;
  private $periodes_calendrier;
  private $id_calendrier;
  private $month_selected;
  private $login;
  private $statut;
  private $du;
  private $au;
  private $etab_all;
  private $eleve_all;
  private $pers_all;
  private $classes=Null;
  private $classes_selected;
  private $del_type;
  private $del;
  private $posted=Null;


  function  __construct() {
    parent::__construct();
    $this->objet_periodes=new ClassPeriodes();
    $this->periodes_calendrier=$this->objet_periodes->get_periodes_calendrier();
    $_SESSION['stats_choix']=isset($_POST['choix']) ? $_POST['choix'] :(isset($_GET['choix']) ? $_GET['choix'] : 'eleves');
    $this->id_calendrier=isset($_POST['id_calendrier'])? $_POST['id_calendrier']:(isset($_GET['id_calendrier'])? $_GET['id_calendrier']:null);
    $this->posted=isset($_POST['posted'])? $_POST['posted']:(isset($_GET['posted'])? $_GET['posted']:null);
    $this->month_selected=isset($_POST['month'])? $_POST['month']:(isset($_GET['month'])? $_GET['month']:null);
    $this->login=isset($_POST['login'])? $_POST['login']:(isset($_GET['login'])? $_GET['login']:null);
    $this->statut=isset($_SESSION['stats_choix'])? $_SESSION['stats_choix']:'eleves' ;
    $this->du=isset($_POST['du'])? $_POST['du']:(isset($_GET['du'])? $_GET['du']:Gepi_Date::get_date_begin_yearschool());
    $this->au=isset($_POST['au'])? $_POST['au']:(isset($_GET['au'])? $_GET['au']:date('d/m/Y'));
    $this->etab_all=isset($_POST['etab_all'])? $_POST['etab_all']:(isset($_GET['etab_all'])? $_GET['etab_all']:null);
    $this->eleve_all=isset($_POST['eleve_all'])? $_POST['eleve_all']:(isset($_GET['eleve_all'])? $_GET['eleve_all']:null);
    $this->pers_all=isset($_POST['pers_all'])? $_POST['pers_all']:(isset($_GET['pers_all'])? $_GET['pers_all']:null);
    $this->classes_selected=isset($_POST['classes'])? $_POST['classes']:(isset($_GET['classes'])? $_GET['classes']:null);
    $this->del_type=isset($_POST['del_type'])? $_POST['del_type']:(isset($_GET['del_type'])? $_GET['del_type']:null);
    $this->del=isset($_POST['del'])? $_POST['del']:(isset($_GET['del'])? $_GET['del']:null);    
  }

  function index () {
    $this->cal_1 = new Calendrier("select_donnees", "du");
    $this->cal_2 = new Calendrier("select_donnees", "au");
    $this->vue->setVar('cal_1',$this->cal_1);
    $this->vue->setVar('cal_2',$this->cal_2);
    try {
      if (isset($this->del)&& isset($this->del_type)) $this->del_selected($this->del_type,$this->del);
      $this->traite_periodes();
      $this->set_data_selected();
      $this->vue->setVar('periodes_calendrier',$this->periodes_calendrier);
      $this->vue->setVar('months',$this->objet_periodes->get_months());
      $this->vue->setVar('classes',$this->classes);
      $individu=new ClassIndividu();
      $this->vue->setVar('individus_identites',$individu->get_individus_data());
      $this->vue->setVar('noms_classes',$this->get_noms_classes());
    }
    catch (Exception $e) {
      echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }
    if (($this->messages)) {
      $this->vue->setVar('messages',$this->messages);
      $this->vue->afficheVue('message.php',$this->vue->getVars());
    }
    $this->vue->afficheVue('selection.php',$this->vue->getVars());
  }

  private function traite_periodes() {

    $this->test_edt_active();
    $this->test_db_periodes_calendrier();
    $this->set_stats_periode_selected();
    $this->classes=$this->objet_periodes->get_classes_periode();
  }

  private function test_edt_active() {
    if(! $this->objet_periodes->is_EDT_active())
      echo"<script type='text/javascript'>alert('Activez le module EDT pour les administrateurs puis renseignez les périodes du calendrier en admin');
               document.location.href='../../accueil_modules.php'</script>";
  }
  private function test_db_periodes_calendrier() {
    if(! $this->objet_periodes->is_periodes_renseignees())
      echo"<script type='text/javascript'>alert('Renseignez les périodes du calendrier en admin');
               document.location.href='../../edt_organisation/edt_calendrier.php'</script>";
  }

  private function set_stats_periode_selected() {
    if($this->id_calendrier) {
      $_SESSION['stats_periodes']=$this->get_debut_fin_periode($this->id_calendrier);
    }elseif ($this->month_selected) {
      $_SESSION['stats_periodes']=Gepi_Date::get_begin_end_month($this->month_selected);
    }else {
      $this->verif_date_selected();
      if($this->posted || !isset($_SESSION['stats_periodes'])) $_SESSION['stats_periodes']=Array('num'=>'0','du'=>$this->du,'au'=>$this->au);
    }
  }
  private function verif_date_selected() {

    if(!Gepi_Date::isValid_fr($this->du)) {
      $this->messages[]=Array('class'=>'mess_orange','message'=>'La date de début n\'est pas au format JJ/MM/AAAA ; La date de début par défaut est sélectionnée');
      return($this->du=Gepi_Date::get_date_begin_yearschool());
    }
    else if(!Gepi_Date::isValid_fr($this->au)) {
      $this->messages[]=Array('class'=>'mess_orange','message'=>'La date de fin n\'est pas au format JJ/MM/AAAA ; La date de fin par défaut est sélectionnée');
      return($this->au=date('d/m/Y'));
    }
    else if (Gepi_Date::compare_date($this->du,$this->au)) {
      $this->messages[]=Array('class'=>'mess_orange','message'=>'La date de fin doit être postérieure à celle du début ; Les dates par défaut sont sélectionnées');
      return Array($this->du=Gepi_Date::get_date_begin_yearschool(),$this->au=date('d/m/Y'));
    }
  }
  private function get_debut_fin_periode($num) {
    foreach($this->periodes_calendrier as $value) {
      if ($value['id_calendrier']==$num) {
        return Array('periode'=>$num,'du'=>Gepi_Date::format_date_iso_fr($value['jourdebut_calendrier']),
                'au'=>Gepi_Date::format_date_iso_fr($value['jourfin_calendrier']));
      }
    }
  }

  private function set_data_selected() {
    $this->set_data_all_selected();
    $this->set_classes_selected();
    if(!is_null($this->login)) $this->set_individus_selected($this->login,$this->statut);    
  }

  private function set_data_all_selected() {
    if($this->etab_all)	$_SESSION['etab_all']=$this->etab_all;
    else if (isset ($_POST['posted'])) unset ($_SESSION['etab_all']);
    if($this->eleve_all) $_SESSION['eleve_all']=$this->eleve_all;
    else if (isset ($_POST['posted']))unset ($_SESSION['eleve_all']);
    if($this->pers_all)	$_SESSION['pers_all']=$this->pers_all;
    elseif (isset ($_POST['posted'])) unset ($_SESSION['pers_all']);
  }

  private function set_classes_selected() {
    if($this->classes_selected) {
      if (isset($_SESSION['stats_classes_selected'])) {
        foreach ($this->classes_selected as $value) {
          if (!in_array($value,$_SESSION['stats_classes_selected']))
            $_SESSION['stats_classes_selected'][$value]=$value;
        }
      }else {
        foreach ($this->classes_selected as $value) {
          $_SESSION['stats_classes_selected'][$value]=$value;
        }
      }
    }
  }

  public function set_individus_selected($login,$statut) {
    if ($login !='') {
      $del=false;
      $set=true;
      if (isset($_SESSION['individus'])) {
        foreach($_SESSION['individus'] as $key=>$value) {
          if ($set==true) {
            if (in_array($login,$value)) $set=false;
          }
        }
      }
      if ($set==true)
        $_SESSION['individus'][$login]=Array($login,$statut);
    }
  }

  public function test_data_selected() {
    if (isset($_SESSION['individus'])||isset($_SESSION['stats_classes_selected'])
            ||isset($_SESSION['pers_all'])||isset($_SESSION['etab_all'])||isset($_SESSION['eleve_all'])) return true;
    else echo"<script type='text/javascript'>alert('Vous n\'avez pas selectionner d\'individus ou de classes');</script>";
  }

  public function del_selected($type,$choix) {
    switch($type) {
      case'all_data':
        if ($choix=='individus')unset ($_SESSION['individus']);
        if ($choix=='classes') unset($_SESSION['stats_classes_selected']);
        break;
      case 'individus':
        if (count($_SESSION['individus'])==1) unset ($_SESSION['individus']);
        else unset($_SESSION['individus'][$choix]);
        break;
      case 'classes':
        if (count($_SESSION['stats_classes_selected'])==1) unset ($_SESSION['stats_classes_selected']);
        else foreach($_SESSION['stats_classes_selected']as $value) {
            if ($value==$choix)	unset($_SESSION['stats_classes_selected'][$choix]);
          }
        break;
    }
  }

  private function get_noms_classes() {
    if (isset($_SESSION['stats_classes_selected'])) {
      foreach($_SESSION['stats_classes_selected'] as $value) {
        $this->noms_classes[]=$this->objet_periodes->get_infos_classe($value);
      }
      return($this->noms_classes);
    }
  }
}
?>