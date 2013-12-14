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
include_once("./lib/model/edt_calendrier.php"); 
class Calendrier {

	public $id;
	public $nom;

	
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
		$sql="UPDATE edt_calendrier_manager SET
				nom_calendrier = '".$this->nom."'
				WHERE id = '".$this->id."' ";
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
 
	public function exists() {
		$sql="SELECT id FROM edt_calendrier_manager WHERE id = '".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			if (mysqli_num_rows($req) != 0) {
				return true;
			}
			else {
				return false;
			}
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
		$sql="DELETE FROM edt_calendrier_manager WHERE id = '".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			// ======== Suppression des périodes calendaires
			$PeriodesCalendaires = new PeriodeCalendaire;
			$PeriodesCalendaires->id_calendar = $this->id;
			if ($PeriodesCalendaires->deleteCalendar()) {
				// ========= Suppression des liaisons classes <-> calendar
				$jointure = new jointure_calendar_classes;
				$jointure->id_calendar = $this->id;
				if ($jointure->delete_classes()) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}				
	}
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public function save() {
		$sql="INSERT INTO edt_calendrier_manager SET 
				nom_calendrier = '".$this->nom."'	";
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
 
	public static function getCalendriers() {
		$result = array();
		$sql="SELECT id, nom_calendrier FROM edt_calendrier_manager ORDER BY id ASC ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			while ($rep = mysqli_fetch_array($req)) {
				$result['id'][] = $rep['id'];
				$result['nom'][] = $rep['nom_calendrier'];
			}
		}
		return $result;
	}
	
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public static function getNom($id) {
		$result = null;
		$sql="SELECT nom_calendrier FROM edt_calendrier_manager WHERE id='".$id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			$rep = mysqli_fetch_array($req);
			$result = $rep['nom_calendrier'];
		}
		return $result;
	}
}	
?>