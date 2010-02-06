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
require_once ("Controleur.php");
require_once("Class.Date.php");
require_once("Modele.Select.php");
require_once("Class.Individu.php");
// Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");

class SelectCtrl extends Controleur {

    private $modele_select;
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
    private $months=Array("09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre",
            "01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin");

    function  __construct() {
        $this->modele_select=new modele_select();
        $_SESSION['stats_choix']=isset($_REQUEST['choix']) ? $_REQUEST['choix'] : 'eleves';
        $this->id_calendrier=isset($_REQUEST['id_calendrier'])? $_REQUEST['id_calendrier']:null ;
        $this->month_selected=isset($_REQUEST['month'])? $_REQUEST['month']:null ;
        $this->login=isset($_REQUEST['nom_login'])? $_REQUEST['nom_login']:null ;
        $this->statut=isset($_SESSION['stats_choix'])? $_SESSION['stats_choix']:'eleves' ;
        $this->du=isset($_REQUEST['du'])? $_REQUEST['du']:Gepi_Date::get_date_begin_yearschool() ;
        $this->au=isset($_REQUEST['au'])? $_REQUEST['au']:date('d/m/Y') ;
        $this->etab_all=isset($_REQUEST['etab_all'])? $_REQUEST['etab_all']:null ;
        $this->eleve_all=isset($_REQUEST['eleve_all'])? $_REQUEST['eleve_all']:null ;
        $this->pers_all=isset($_REQUEST['pers_all'])? $_REQUEST['pers_all']:null ;
        $this->classes_selected=isset($_REQUEST['classes'])? $_REQUEST['classes']:null ;
        $this->del_type=isset($_REQUEST['del_type'])? $_REQUEST['del_type']:null ;
        $this->del=isset($_REQUEST['del'])? $_REQUEST['del']:null ;
    }

    function index () {
        $cal_1 = new Calendrier("select_donnees", "du");
        $cal_2 = new Calendrier("select_donnees", "au");
        try {
            if (isset($this->del)&& isset($this->del_type)) $this->del_selected($this->del_type,$this->del);
            $this->traite_periodes();
            $this->set_data_selected();
            $periodes_calendrier=$this->periodes_calendrier;
            $months=$this->months;  
            $classes=$this->classes;
            $individu=new ClassIndividu();
            $individus_identites=$individu->get_individus_data();
            $noms_classes=$this->get_noms_classes();            
        }
        catch (Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }
        include('selection.php');
        echo"<script type='text/javascript'> new Ajax.Autocompleter ('nom','nom_update','autocompletion.php',{
        method: 'post',
        paramName: 'nom',
        minChars: 2,
        indicator:'indicateur',
        afterUpdateElement: ac_return });</script>";
        
    }

    private function traite_periodes() {

        $this->test_edt_active();
        $this->periodes_calendrier=$this->modele_select->get_db_periodes_calendrier();
        $this->test_db_periodes_calendrier();
        $this->set_stats_periode_selected();
        $this->classes=$this->modele_select->get_classes_periode();
    }

    private function test_edt_active(){
        if(!$this->modele_select->test_edt())
                echo"<script type='text/javascript'>alert('Activez le module EDT puis renseignez les périodes du calendrier en admin');
               document.location.href='../../accueil_modules.php'</script>";
    }
    private function test_db_periodes_calendrier() {
        if(isset($this->periodes_calendrier['error']))
            echo"<script type='text/javascript'>alert('Renseignez les périodes du calendrier en admin');
               document.location.href='../../edt_organisation/edt_calendrier.php'</script>";
    }

    private function set_stats_periode_selected() {
        if($this->id_calendrier) {
            $_SESSION['stats_periodes']=$this->get_debut_fin_periode($this->id_calendrier);
        }elseif ($this->month_selected) {
            $_SESSION['stats_periodes']=Gepi_Date::get_begin_end_month($this->month_selected);
        }else {
            if (isset ($_POST['posted']))$_SESSION['stats_periodes']=Array('num'=>'0','du'=>$this->du,'au'=>$this->au);
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
        $this->verif_date_selected();
        $this->set_periode_selected();
        $this->set_data_all_selected();
        $this->set_classes_selected();
        $this->set_individus_selected();
        $this->test_type_abs();
    }
    private function verif_date_selected() {
        if (Gepi_Date::compare_date($this->du,$this->au)) {
            echo"<script type='text/javascript'>alert('La date de fin doit être postérieure à celle du début');</script>";
            $this->du=Gepi_Date::get_date_begin_yearschool();
        }
    }

    private function set_periode_selected() {
        if (!isset($_POST['posted'])&& !isset($_SESSION['stats_periodes']))
            $_SESSION['stats_periodes']=Array('num'=>'0','du'=>$this->du,'au'=>$this->au);
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

    private function set_individus_selected() {
        if ($this->login !='') {
            $del=false;
            $set=true;
            if (isset($_SESSION['individus'])) {
                foreach($_SESSION['individus'] as $key=>$value) {
                    if ($set==true) {
                        if (in_array($this->login,$value)) $set=false;
                    }
                }
            }
            if ($set==true)
                $_SESSION['individus'][$this->login]=Array($this->login,$this->statut);
        }
    }

    private function test_type_abs() {
        if ($_SESSION['type']=='Abs')$this->clear_data_selected_for_abs();
    }

    private function clear_data_selected_for_abs() {
        unset($_SESSION['eleve_all']);
        unset($_SESSION['pers_all']);
        if (isset($_SESSION['individus'])) {
            foreach ($_SESSION['individus'] as $value) {
                if ($value[1]=='personnels') {
                    if (count($_SESSION['individus'])==1) unset ($_SESSION['individus']);
                    else unset($_SESSION['individus'][$value[0]]);
                }
            }
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
                $this->noms_classes[]=$this->modele_select->get_infos_classe($value);
            }
            return($this->noms_classes);
        }
    }
    
}
?>