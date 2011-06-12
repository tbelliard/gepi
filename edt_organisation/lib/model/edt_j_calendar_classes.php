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

class jointure_calendar_classes {

	public $id_calendar;
	public $id_classes;

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
 
	public function delete_classes() {
		$sql="DELETE FROM edt_j_calendar_classes WHERE id_calendar = '".$this->id_calendar."' ";
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
 
	public function save_classe() {
		$sql="INSERT INTO edt_j_calendar_classes SET 
				id_calendar = '".$this->id_calendar."',
				id_classe = '".$this->id_classe."'";
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
 
	public function exists() {
		$sql="SELECT id_calendar FROM edt_j_calendar_classes WHERE 
				id_calendar = '".$this->id_calendar."' AND
				id_classe = '".$this->id_classe."'";
		$req = mysql_query($sql);
		if ($req) {
			if (mysql_num_rows($req) > 0) {
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
 
	public function bad_calendar() {
		$sql="SELECT id_calendar FROM edt_j_calendar_classes WHERE 
				id_classe = '".$this->id_classe."'";
		$req = mysql_query($sql);
		if ($req) {
			if (mysql_num_rows($req) != 0) {
				$rep= mysql_fetch_array($req);
				if ($rep['id_calendar'] != $this->id_calendar) {
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
}	
?>