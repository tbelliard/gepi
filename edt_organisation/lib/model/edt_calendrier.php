<?php
/*
 *
 * Copyright 2011 Pascal Fautrero
 *
 * This file is part of GEPi.
 *
 * GEPi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
class PeriodeCalendaire {


	public $classe_concernees;
	public $nom;
	public $debut_ts;
	public $fin_ts;
	public $jourdebut;
	public $heuredebut;
	public $jourfin;
	public $heurefin;
	public $periode_note;
	public $etabferme;
	public $etabvacances;
	
/*******************************************************************
 *
 *
 *******************************************************************/	
    function __construct() {
        
    }	
	
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public function save() {
		$sql="INSERT INTO edt_calendrier SET 
				classe_concerne_calendrier = '".$this->classe_concernees."',
				nom_calendrier = '".$this->nom."',
				debut_calendrier_ts = '".$this->debut_ts."',
				fin_calendrier_ts = '".$this->fin_ts."',
				jourdebut_calendrier = '".$this->jourdebut."',
				heuredebut_calendrier = '".$this->heuredebut."',
				jourfin_calendrier = '".$this->jourfin."',
				heurefin_calendrier = '".$this->heurefin."',
				numero_periode = '".$this->periode_note."',
				etabferme_calendrier = '".$this->etabferme."',
				etabvacances_calendrier = '".$this->etabvacances."' ";
		$req = mysql_query($sql);
		if ($req) {
			return true;
		}
		else {
			return false;
		}				
	}
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public static function getPeriods() {
		$result = array();
		$sql="SELECT debut_calendrier_ts, fin_calendrier_ts FROM edt_calendrier ORDER BY debut_calendrier_ts ASC";
		$req = mysql_query($sql);
		if ($req) {
			if (mysql_num_rows($req) != 0) {
				while ($rep = mysql_fetch_array($req)) {
					$result['debut'][] = $rep['debut_calendrier_ts'];
					$result['fin'][] = $rep['fin_calendrier_ts'];
				}

			} 
			else {
				$result['debut'][] = null;
				$result['fin'][] = null;			
			}
			return $result;
		}
		else {
			$result['debut'][] = null;
			$result['fin'][] = null;	
			return $result;
		}				
	}
	
}	

?>