<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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

require_once('Class.Date.php');
require_once('Class.Modele.php');
require_once('Modele.Select.php');
Class Modele_Incidents extends Modele {

  private $data=Null;
  private $filter=Null;
  private $res=Null;
  private $sql=Null;
  private $incidents=Null;
  private $protagonistes=Null;
  private $nbre=Null;
  private $total=Null;
  private $mesures=Null;
  private $sanctions=Null;
  private $crenaux=Null;  
  private $top_incidents=Null;
  private $top_sanctions=Null;
  private $top_retenues=Null;
  private $top_exclusions=Null;


  public function get_liste($champ,$table) {
    return($this->get_db_liste($champ,$table));
  }
  public function get_total() {
    return($this->total);
  }
  public function get_infos_natures() {
    return($this->get_db_infos_natures());
  }
  public function get_types_roles() {
    return($this->get_db_types_roles());
  }
  public function get_types_sanctions() {
    return($this->get_db_types_sanctions());
  }
  public function get_types_mesures() {
    return($this->get_db_types_mesures());
  }

  public function get_infos_categories($id=null) {
    return($this->get_db_infos_categories($id));
  }

  public function get_protagonistes($liste_incidents) {
    return $this->get_protagonistes_incident($liste_incidents);
  }
  public function get_mesures($liste_incidents) {
    return $this->get_mesures_incident($liste_incidents);
  }
  public function get_sanctions($liste_incidents) {
    return $this->get_sanctions_incident($liste_incidents);
  }

  public function get_incidents($choix,$titre,$du,$au,$critere,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
     return $this->get_infos_incidents($choix,$titre,$du,$au,$critere,$filtre_cat,$filtre_mes,$filtre_san,$filtre_role);

  }
  public function get_nombre_total_incidents($liste_incidents=Null) {
    unset($this->nbre);
    $this->nbre=$this->get_db_nbre_total_incidents($liste_incidents);
    return($this->nbre[0]);
  }
  public function get_nombre_total_mesures($liste_incidents=Null,$type_mesures=Null) {
    unset($this->nbre);
    $this->nbre=$this->get_db_nbre_total_mesures($liste_incidents,$type_mesures);
    return($this->nbre[0]);
  }
  public function get_nombre_total_sanctions($liste_incidents=Null) {
    unset($this->nbre);
    $this->nbre=$this->get_db_nbre_total_sanctions($liste_incidents);
    return($this->nbre[0]);
  }
  public function get_crenaux() {
    return $this->get_db_infos_crenaux();
  }  
  public function get_infos_individu() {
    return $this->infos_individu;
  }
  public function get_top_incidents($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    return($this->get_db_top_incidents($du,$au,$filtre_cat,$filtre_mes,$filtre_san,$filtre_role));
  }
  public function get_top_sanctions($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    return($this->get_db_top_sanctions($du,$au,$filtre_cat,$filtre_mes,$filtre_san,$filtre_role));
  }
  public function get_top_retenues($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    return($this->get_db_top_retenues($du,$au,$filtre_cat,$filtre_mes,$filtre_san,$filtre_role));
  }
  public function get_top_exclusions($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    return($this->get_db_top_exclusions($du,$au,$filtre_cat,$filtre_mes,$filtre_san,$filtre_role));
  }

  public function update_categorie($categorie_selected,$natures_selected=Null,$categorie=Null) {
    if(isset($natures_selected)) {
      foreach ($natures_selected as $nature) {
        $this->sql="UPDATE s_incidents SET id_categorie=".$categorie_selected." WHERE nature='".traitement_magic_quotes($nature)."';";
        $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
      }
    }else {
      $this->sql="UPDATE s_incidents SET id_categorie=".$categorie_selected." WHERE id_categorie=".$categorie.";";
      $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    }
  }

  private function get_db_liste($champ,$table) {
    $this->sql='SELECT DISTINCT '.$champ.' from '.$table.'';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }
  private function get_db_infos_natures() {
    $this->sql='SELECT DISTINCT nature,id_categorie from s_incidents ORDER BY id_categorie ASC';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }
  private function get_db_infos_categories($id=Null) {
    $this->sql="SELECT  id,categorie,sigle from s_categories ";
    if ($id) $this->sql.="WHERE id='".$id."'";
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }
  private function get_db_types_sanctions() {
    $this->sql="SELECT id_nature,nature from s_types_sanctions2";
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }
  private function get_db_types_roles() {
    $this->sql="SELECT  id,qualite from s_qualites";
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }
  private function get_db_types_mesures() {
    $this->sql="SELECT  id,mesure from s_mesures";
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return($this->data=parent::set_array('object',$this->res));
  }

  private function get_db_incidents_totaux($du,$au,$tri=Null,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->get_infos_incidents('etab_all','total_etab',$du,$au,Null,$tri,Null,Null,Null,Null);
    if(!isset($this->incidents['L\'Etablissement']['error'])) {
      $this->liste_id_incidents_selected=$this->make_liste_id($this->incidents['total_etab']);
      $this->get_protagonistes_incident($this->liste_id_incidents_selected);
      $this->get_mesures_incident($this->liste_id_incidents_selected);
      $this->get_sanctions_incident($this->liste_id_incidents_selected);
    }
  }

  private function get_infos_incidents($choix,$titre,$du,$au,$critere=Null,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->sql='SELECT DISTINCT sin.id_incident,sin.declarant,sin.date,sin.heure,
                       sin.nature,sin.id_categorie,sin.description,sin.etat
                      FROM s_incidents sin ';
    if(!$filtre_san && !$filtre_mes &&!$filtre_role){
        //on garde les incidents sans protagonistes si pas de filtres
        $this->sql.="LEFT JOIN s_protagonistes spr ON sin.id_incident=spr.id_incident ";
    } else{
        //on supprime les incidents sans protagonistes
       $this->sql.="INNER JOIN s_protagonistes spr ON sin.id_incident=spr.id_incident ";
    }
    if($filtre_san)$this->sql.=' INNER JOIN s_sanctions ssan ON sin.id_incident=ssan.id_incident';
    if($filtre_mes)$this->sql.=' INNER JOIN s_traitement_incident str ON sin.id_incident=str.id_incident INNER JOIN s_mesures smes ON str.id_mesure=smes.id ';
    $this->sql.=' WHERE date BETWEEN \''.$du.
            '\' AND \''.$au.'\' ';
    if($filtre_role)  $this->sql.=" AND (spr.qualite IN ('".parent::make_list_for_request_in($filtre_role)."') OR spr.qualite IS NULL)";

    if($filtre_san) {
      $this->sql.=" AND (ssan.nature IN ('".parent::make_list_for_request_in($filtre_san)."')";
      if ($critere) $this->sql.=" AND ssan.login IN('".$critere."'))"; else $this->sql.= ")";
    }
    if($filtre_mes) {
      $this->sql.=" AND smes.type='prise' ";
      $this->sql.=" AND (str.id_mesure IN ('".parent::make_list_for_request_in($filtre_mes)."')";
      if ($critere ) $this->sql.=" AND str.login_ele IN ('".$critere."'))"; else $this->sql.= ")";
    }
    if($filtre_cat) {
      $this->sql.=" AND ( sin.id_categorie IN ('".parent::make_list_for_request_in($filtre_cat)."')";
      if (in_array('Null',$filtre_cat)) $this->sql.="OR sin.id_categorie is null)";
      else $this->sql.=")";
    }
    $this->sql.=''.$this->filter_individu($choix,$critere);
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    return(parent::set_array('object',$this->res));
  }

  private function filter_individu($choix,$critere=Null) {
    switch($choix) {
      case'etab_all':$this->filter='';
        break;
      case'eleves_all':$this->filter=' AND spr.statut=\'eleve\'';
        break;
      case'pers_all':	$this->filter=' AND spr.statut!=\'eleve\'';
        break;
      case 'individu': $this->filter=' AND spr.login IN ("'.$critere.'") ';
        break;
      case 'classe' :
        $this->filter=" AND spr.login IN ('".$critere."')";
        break;
    }
    return $this->filter;
  }
  private function get_protagonistes_incident($liste_incidents) {
    $this->sql="SELECT spr.id_incident,spr.login,spr.statut,spr.qualite,c.classe
                    FROM s_protagonistes spr
                    LEFT JOIN j_eleves_classes jec ON spr.login=jec.login
                    LEFT JOIN classes c ON jec.id_classe=c.id
                    WHERE id_incident IN ('".$liste_incidents."')";
    $this->sql.=' GROUP BY spr.id';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->protagonistes[$this->row->id_incident][$this->row->login]=$this->row;
      }
      return($this->protagonistes);
    }
  }
  private function get_mesures_incident($liste_incidents) {

    $this->sql="SELECT str.id_incident,str.id_mesure,str.login_ele,str.login_u,smes.type,smes.mesure,smes.commentaire
                    FROM s_traitement_incident str,s_mesures smes,s_protagonistes spr 
                    WHERE str.id_mesure=smes.id
                    AND (str.id_incident=spr.id_incident AND spr.login=str.login_ele) 
                    AND str.id_incident IN ('".$liste_incidents."')";
    $this->sql.=' GROUP BY str.id';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->mesures[$this->row->id_incident][$this->row->login_ele][$this->row->id_mesure]=$this->row;
      }
      return($this->mesures);
    }
  }

  private function get_sanctions_incident($liste_incidents) {
    $this->sql="SELECT san.id_sanction,san.id_incident,san.login,san.nature,san.effectuee,
                           sret.date as ret_date,sret.heure_debut as ret_heure_debut,sret.duree as ret_duree,sret.travail as ret_travail,
                           sexc.date_debut as exc_date_debut,sexc.heure_debut as exc_heure_debut,sexc.date_fin as exc_date_fin,sexc.heure_fin as exc_heure_fin,sexc.travail as exc_travail,
                           str.date_retour as trv_date_retour,str.heure_retour as trv_heure_retour ,str.travail as trv_travail,
                           saut.description as autre_description,sts.nature as autre_nature  FROM s_sanctions san
                    LEFT JOIN s_retenues sret ON san.id_sanction=sret.id_sanction 
                    LEFT JOIN s_exclusions sexc ON san.id_sanction=sexc.id_sanction
                    LEFT JOIN s_travail str ON san.id_sanction=str.id_sanction
                    LEFT JOIN s_autres_sanctions saut ON san.id_sanction=saut.id_sanction
                    LEFT JOIN s_types_sanctions2 sts ON sts.id_nature=saut.id_nature
                    INNER JOIN s_protagonistes spr ON (spr.id_incident=san.id_incident AND spr.login=san.login)
                    WHERE  san.id_incident IN ('".$liste_incidents."')";
    $this->sql.=' GROUP BY san.id_sanction';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    while($this->row=mysqli_fetch_object($this->res)) {
      $this->sanctions[$this->row->id_incident][$this->row->login][$this->row->id_sanction]=$this->row;
    }
    return($this->sanctions);
  }
  private function get_db_nbre_total_incidents($liste=Null) {
    $this->sql='SELECT COUNT(id_incident) from s_incidents WHERE date BETWEEN \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']).
            '\' AND \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']).'\' ';
    if($liste) $this->sql.=" AND id_incident IN ('".parent::make_list_for_request_in($liste)."')";
    return(mysqli_fetch_row($this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql)));
  }
  private function get_db_nbre_total_mesures($liste=Null,$type=Null) {

    $this->sql="SELECT COUNT(str.id) from s_traitement_incident str
          INNER JOIN s_incidents sin  ON str.id_incident=sin.id_incident
          INNER JOIN s_protagonistes spr ON (spr.login=str.login_ele AND spr.id_incident=str.id_incident) ";
    if ($type)$this->sql.="INNER JOIN s_mesures smes ON str.id_mesure=smes.id ";
    $this->sql.=' WHERE sin.date BETWEEN \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']).
            '\' AND \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']).'\'  ';
    if($liste)$this->sql.=" AND sin.id_incident IN ('".parent::make_list_for_request_in($liste)."')";
    if ($type)$this->sql.=" AND smes.type='".$type."'";
    // $this->sql.=" OR sin.id_incident is null";
    return(mysqli_fetch_row($this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql)));
  }
  private function get_db_nbre_total_sanctions($liste=Null) {

    $this->sql='SELECT COUNT(san.id_sanction) from s_sanctions san 
        INNER JOIN s_incidents sin  ON  san.id_incident=sin.id_incident
        INNER JOIN s_protagonistes spr ON (spr.id_incident=san.id_incident AND spr.login=san.login)
        WHERE sin.date BETWEEN \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['du']).
            '\' AND \''.Gepi_Date::format_date_fr_iso($_SESSION['stats_periodes']['au']).'\'  ';
    if($liste)$this->sql.=" AND sin.id_incident IN ('".parent::make_list_for_request_in($liste)."')";
    //$this->sql.=" OR sin.id_incident is null"; (je pense que les sanctions ne sont pas supprimées)
    return(mysqli_fetch_row($this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql)));
  }
  private function get_db_infos_crenaux() {
    $this->sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->crenaux[$this->row->nom_definie_periode]=$this->row;
      }
    }
    return($this->crenaux);
  }

  private function get_db_top_incidents($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->sql="SELECT sp.login, count(DISTINCT sin.id_incident) AS nb FROM s_protagonistes sp INNER JOIN s_incidents sin ON sp.id_incident=sin.id_incident ";
    if($filtre_san)$this->sql.=' INNER JOIN s_sanctions ssan ON sp.id_incident=ssan.id_incident';
    if($filtre_mes)$this->sql.=' INNER JOIN s_traitement_incident str ON (sp.id_incident=str.id_incident AND sp.login=str.login_ele)
                                 INNER JOIN s_mesures smes ON str.id_mesure=smes.id ';
    $this->sql.=' WHERE date BETWEEN \''.$du.
            '\' AND \''.$au.'\' ';
    $this->sql.=" AND sp.statut='eleve'";
    if($filtre_role)  $this->sql.=" AND (sp.qualite IN ('".parent::make_list_for_request_in($filtre_role)."') OR sp.qualite IS NULL)";

    if($filtre_san) {
      $this->sql.=" AND (ssan.nature IN ('".parent::make_list_for_request_in($filtre_san)."')) AND ssan.login=sp.login ";
    }
    if($filtre_mes) {
      $this->sql.=" AND smes.type='prise' ";
      $this->sql.=" AND (str.id_mesure IN ('".parent::make_list_for_request_in($filtre_mes)."'))";
    }
    if($filtre_cat) {
      $this->sql.=" AND ( sin.id_categorie IN ('".parent::make_list_for_request_in($filtre_cat)."')";
      if (in_array('Null',$filtre_cat)) $this->sql.="OR sin.id_categorie is null)";
      else $this->sql.=")";
    }
    $this->sql.='GROUP BY sp.login ';    
    $this->sql.='ORDER BY nb DESC LIMIT 10 ';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->top_incidents[]=$this->row;        
      }
    }
    return($this->top_incidents);

  }

  private function get_db_top_sanctions($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->sql="SELECT ssan.login, count(DISTINCT ssan.id_sanction) AS nb FROM s_sanctions ssan
        INNER JOIN s_incidents sin ON ssan.id_incident=sin.id_incident
        INNER JOIN s_protagonistes sp ON (sp.id_incident=ssan.id_incident AND sp.login=ssan.login)";
   // if($filtre_role)$this->sql.=' LEFT JOIN s_protagonistes sp ON ssan.id_incident=sp.id_incident ';
    if($filtre_mes)$this->sql.=' INNER JOIN s_traitement_incident str ON (ssan.id_incident=str.id_incident AND ssan.login=str.login_ele)
                                 INNER JOIN s_mesures smes ON str.id_mesure=smes.id ';
    $this->sql.=' WHERE date BETWEEN \''.$du.
            '\' AND \''.$au.'\' ';
    if($filtre_role)  $this->sql.=" AND (sp.qualite IN ('".parent::make_list_for_request_in($filtre_role)."') OR sp.qualite IS NULL) AND ssan.login=sp.login ";

    if($filtre_san) {
      $this->sql.=" AND (ssan.nature IN ('".parent::make_list_for_request_in($filtre_san)."'))";
    }
    if($filtre_mes) {
      $this->sql.=" AND smes.type='prise' ";
      $this->sql.=" AND (str.id_mesure IN ('".parent::make_list_for_request_in($filtre_mes)."'))";
    }
    if($filtre_cat) {
      $this->sql.=" AND ( sin.id_categorie IN ('".parent::make_list_for_request_in($filtre_cat)."')";
      if (in_array('Null',$filtre_cat)) $this->sql.="OR sin.id_categorie is null)";
      else $this->sql.=")";
    }
    $this->sql.='GROUP BY ssan.login ';
    $this->sql.='ORDER BY count(ssan.login) DESC LIMIT 10 ';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->top_sanctions[]=$this->row;
      }
    }
    return($this->top_sanctions);
  }
  private function get_db_top_retenues($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->sql="SELECT ssan.login, sret.duree AS nb FROM s_retenues sret
       INNER JOIN s_sanctions ssan ON sret.id_sanction=ssan.id_sanction
       INNER JOIN s_incidents sin ON ssan.id_incident=sin.id_incident
       INNER JOIN s_protagonistes sp ON (sp.id_incident=ssan.id_incident AND sp.login=ssan.login)";
    //if($filtre_role)$this->sql.=' LEFT JOIN s_protagonistes sp ON ssan.id_incident=sp.id_incident';
    if($filtre_mes)$this->sql.=' INNER JOIN s_traitement_incident str ON (ssan.id_incident=str.id_incident AND str.login_ele=ssan.login)
                                 INNER JOIN s_mesures smes ON str.id_mesure=smes.id ';
    $this->sql.=' WHERE sin.date BETWEEN \''.$du.
            '\' AND \''.$au.'\' ';
    if($filtre_role)  $this->sql.=" AND (sp.qualite IN ('".parent::make_list_for_request_in($filtre_role)."') OR sp.qualite IS NULL) ";

    if($filtre_san) {     
      $this->sql.=" AND (ssan.nature IN ('".parent::make_list_for_request_in($filtre_san)."'))";
    }
    if($filtre_mes) {
      $this->sql.=" AND smes.type='prise' ";
      $this->sql.=" AND (str.id_mesure IN ('".parent::make_list_for_request_in($filtre_mes)."'))";
    }
    if($filtre_cat) {
      $this->sql.=" AND ( sin.id_categorie IN ('".parent::make_list_for_request_in($filtre_cat)."')";
      if (in_array('Null',$filtre_cat)) $this->sql.="OR sin.id_categorie is null)";
      else $this->sql.=")";
    }
    $this->sql.=' GROUP BY ssan.id_sanction ';
   // $this->sql.=' ORDER BY sum(sret.duree) DESC LIMIT 10 ';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {          
        if(!isset($this->top_retenues[$this->row->login])){
            $this->top_retenues[$this->row->login]=$this->row;
            $this->top_retenues[$this->row->login]->nb=$this->top_retenues[$this->row->login]->nb+0;
        }else{
            $this->top_retenues[$this->row->login]->nb=$this->top_retenues[$this->row->login]->nb+$this->row->nb;
        }
      }
      if(is_array($this->top_retenues))usort($this->top_retenues,array("Gepi_Date", "compare_nb_heures"));
    }    
    return($this->top_retenues);
  }
  private function get_db_top_exclusions($du,$au,$filtre_cat=Null,$filtre_mes=Null,$filtre_san=Null,$filtre_role=Null) {
    $this->sql="SELECT ssan.login, count(se.id_exclusion) AS nb FROM s_exclusions se
       INNER JOIN s_sanctions ssan ON se.id_sanction=ssan.id_sanction
       INNER JOIN s_incidents sin ON ssan.id_incident=sin.id_incident
       INNER JOIN s_protagonistes sp ON (sp.id_incident=ssan.id_incident AND sp.login=ssan.login) ";
    //if($filtre_role)$this->sql.=' LEFT JOIN s_protagonistes sp ON ssan.id_incident=sp.id_incident';
    if($filtre_mes)$this->sql.=' INNER JOIN s_traitement_incident str (ON ssan.id_incident=str.id_incident AND ssan.login=str.login_ele)
                                 INNER JOIN s_mesures smes ON str.id_mesure=smes.id ';
    $this->sql.=' WHERE sin.date BETWEEN \''.$du.
            '\' AND \''.$au.'\' ';
    if($filtre_role)  $this->sql.=" AND (sp.qualite IN ('".parent::make_list_for_request_in($filtre_role)."') OR sp.qualite IS NULL) AND ssan.login=sp.login";

    if($filtre_san) {
      $this->sql.=" AND (ssan.nature IN ('".parent::make_list_for_request_in($filtre_san)."'))";
    }
    if($filtre_mes) {
      $this->sql.=" AND smes.type='prise' ";
      $this->sql.=" AND (str.id_mesure IN ('".parent::make_list_for_request_in($filtre_mes)."'))";
    }
    if($filtre_cat) {
      $this->sql.=" AND ( sin.id_categorie IN ('".parent::make_list_for_request_in($filtre_cat)."')";
      if (in_array('Null',$filtre_cat)) $this->sql.="OR sin.id_categorie is null)";
      else $this->sql.=")";
    }
    $this->sql.=' GROUP BY ssan.login ';
    $this->sql.=' ORDER BY count(se.id_exclusion) DESC LIMIT 10 ';
    $this->res=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
    if($this->res) {
      while($this->row=mysqli_fetch_object($this->res)) {
        $this->top_exclusions[]=$this->row;
      }
    }
    return($this->top_exclusions);
  }
  
}
?>
