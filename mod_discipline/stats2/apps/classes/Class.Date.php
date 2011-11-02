<?php
/*
 * $Id: Class.Date.php 7799 2011-08-17 08:38:10Z dblanqui $
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

Class Gepi_Date {

  public static function isValid_fr($date2) {
    if(preg_match("'(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}'",$date2)) return true;
    else return false;
  }
  public static function get_date_begin_yearschool() {
    $date=explode('/',date('d/m/Y'));
    $month=$date[1];
    if($month<=8) {
      $year=intval(self::get_current_year())-1;
    }else {
      $year=self::get_current_year();
    }
    $date_debut='01/09/'.$year;
    return $date_debut;
  }

  public static function get_current_year() {
    $date=explode('/',date('d/m/Y'));
    $year=$date[2];
    return $year;
  }

  public static function format_date_fr_iso($date2) {
    $date=explode('/',$date2);
    $date=$date[2].'-'.$date[1].'-'.$date[0];    
    return $date;
  }

  public static function format_date_iso_fr($date2) {
    $date=explode('-',$date2);
    $date=$date[2].'/'.$date[1].'/'.$date[0];
    return $date;
  }

  public static function compare_date($date1,$date2) {
    $valeurs=explode('/',$date1);
    $time1=mktime(0,0,0,$valeurs[1],$valeurs[0],$valeurs[2]);
    $valeurs=explode('/',$date2);
    $time2=mktime(0,0,0,$valeurs[1],$valeurs[0],$valeurs[2]);
    if ($time1>$time2) return true;
  }

  public static function get_begin_end_month($month) {
    $date=explode('/',date('d/m/Y'));
    $month_current=$date[1];
    if ($month_current <9) {
      if($month<=8) {
        $year=self::get_current_year();
      }else {
        $year=intval(self::get_current_year())-1;
      }
    }else {
      if($month>=8) {
        $year=self::get_current_year();
      }else {
        $year=intval(self::get_current_year())+1;
      }
    }
    $dernierJour = strftime("%d", mktime(0, 0, 0, $month+1, 0, $year));
    $premierJour = strftime("%d", mktime(0, 0, 0, $month, 1, $year));
    return Array('month'=>$month,'du'=>$premierJour.'/'.$month.'/'.$year,'au'=>$dernierJour.'/'.$month.'/'.$year);
  }

  public static function calcule_duree_exclusion($date_debut,$heure_debut,$date_fin,$heure_fin) {
    $date1=$date_debut.' '.$heure_debut;
    $date2=$date_fin.' '.$heure_fin;
    return($nbjours = ceil((strtotime($date2) - strtotime($date1))/(60*60*24)));
  }

  public static function compare_nb_heures($ele1, $ele2){
   if ($ele1->nb == $ele2->nb) {
        return 0;
    }
    return ($ele2->nb < $ele1->nb) ? -1 : 1;
}
}
?>