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

	public $id;
	public $classes_concernees;
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
	public $id_calendar;	
	
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
 
	public function update() {
		$sql="UPDATE edt_calendrier SET
				classe_concerne_calendrier = '".$this->classes_concernees."',
				nom_calendrier = '".$this->nom."',
				debut_calendrier_ts = '".$this->debut_ts."',
				fin_calendrier_ts = '".$this->fin_ts."',
				jourdebut_calendrier = '".$this->jourdebut."',
				heuredebut_calendrier = '".$this->heuredebut."',
				jourfin_calendrier = '".$this->jourfin."',
				heurefin_calendrier = '".$this->heurefin."',
				numero_periode = '".$this->periode_note."',
				etabferme_calendrier = '".$this->etabferme."',
				etabvacances_calendrier = '".$this->etabvacances."',
				id_calendar = '".$this->id_calendar."'
				WHERE id_calendrier = '".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
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
 
	public function update_classes() {
		$sql="UPDATE edt_calendrier SET
				classe_concerne_calendrier = '".$this->classes_concernees."'
				WHERE id_calendar = '".$this->id_calendar."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
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
 
	public function delete() {
		$sql="DELETE FROM edt_calendrier WHERE id_calendrier = '".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
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
 
	public function deleteCalendar() {
		$sql="DELETE FROM edt_calendrier WHERE id_calendar = '".$this->id_calendar."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
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
 
	public function getCalendarID() {
		$result = null;
		$sql="SELECT id_calendar FROM edt_calendrier WHERE id_calendrier='".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			while ($rep = mysqli_fetch_array($req)) {
				$result = $rep['id_calendar'];
			}
		}
		return $result;
	}
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public function save() {
		$sql="INSERT INTO edt_calendrier SET 
				classe_concerne_calendrier = '".$this->classes_concernees."',
				nom_calendrier = '".$this->nom."',
				debut_calendrier_ts = '".$this->debut_ts."',
				fin_calendrier_ts = '".$this->fin_ts."',
				jourdebut_calendrier = '".$this->jourdebut."',
				heuredebut_calendrier = '".$this->heuredebut."',
				jourfin_calendrier = '".$this->jourfin."',
				heurefin_calendrier = '".$this->heurefin."',
				numero_periode = '".$this->periode_note."',
				etabferme_calendrier = '".$this->etabferme."',
				etabvacances_calendrier = '".$this->etabvacances."',
				id_calendar = '".$this->id_calendar."'	";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
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
 
	public static function getPeriods($id_calendar) {
		$result = array();
		if ($id_calendar) {
			$sql="SELECT id_calendrier, debut_calendrier_ts, fin_calendrier_ts FROM edt_calendrier WHERE id_calendar='".$id_calendar."' ORDER BY debut_calendrier_ts ASC";
		}
		else {
			$sql="SELECT id_calendrier, debut_calendrier_ts, fin_calendrier_ts FROM edt_calendrier ORDER BY debut_calendrier_ts ASC";		
		}
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			if (mysqli_num_rows($req) != 0) {
				while ($rep = mysqli_fetch_array($req)) {
					$result['id'][] = $rep['id_calendrier'];
					$result['debut'][] = $rep['debut_calendrier_ts'];
					$result['fin'][] = $rep['fin_calendrier_ts'];
				}

			} 
			else {
				$result['id'][] = null;
				$result['debut'][] = null;
				$result['fin'][] = null;			
			}
			return $result;
		}
		else {
			$result['id'][] = null;
			$result['debut'][] = null;
			$result['fin'][] = null;	
			return $result;
		}				
	}

/*******************************************************************
 *
 *
 *******************************************************************/
 
	public function insertable() {
		$result = array();
		$insertable = true;
		if ($this->id != null) {
			$sql="SELECT debut_calendrier_ts, fin_calendrier_ts FROM edt_calendrier 
					WHERE 	id_calendrier != '".$this->id."' AND
							id_calendar = '".$this->id_calendar."' 
					ORDER BY debut_calendrier_ts ASC";		
		}
		else {
			$sql="SELECT debut_calendrier_ts, fin_calendrier_ts FROM edt_calendrier WHERE
							id_calendar = '".$this->id_calendar."' ORDER BY debut_calendrier_ts ASC";		
		}

		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			if (mysqli_num_rows($req) != 0) {
				while ($rep = mysqli_fetch_array($req)) {
					$result['debut'][] = $rep['debut_calendrier_ts'];
					$result['fin'][] = $rep['fin_calendrier_ts'];
				}

			} 
			else {
				$result['debut'][] = null;
				$result['fin'][] = null;			
			}
		}
		else {
			$result['debut'][] = null;
			$result['fin'][] = null;	
		}				
		$i = 0;
		while ((isset($result['debut'][$i])) && ($insertable)) {
			if (($result['debut'][$i] <= $this->debut_ts) && ($this->debut_ts <= $result['fin'][$i])) {
				$insertable = false;
			}
			else if (($result['debut'][$i] <= $this->fin_ts) && ($this->fin_ts <= $result['fin'][$i])) {
				$insertable = false;			
			}
			else if (($result['debut'][$i] >= $this->debut_ts) && ($this->fin_ts >= $result['fin'][$i])) {
				$insertable = false;			
			}
			$i++;
		}
		return $insertable;
	}	
}	

?>