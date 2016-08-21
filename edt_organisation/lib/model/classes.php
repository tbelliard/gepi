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

class Classe {

	public $id;
	public $classe;

	
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
 
	public static function getClasses() {
		$result = array();
		$sql="SELECT id, classe FROM classes ORDER BY classe ASC";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			while ($rep = mysqli_fetch_array($req)) {
				$result['id'][] = $rep['id'];
				$result['nom'][] = $rep['classe'];
			}
		}
		return $result;
	}
/*******************************************************************
 *
 *
 *******************************************************************/
 
	public function getShortName() {
		$result = null;
		$sql="SELECT classe FROM classes WHERE id = '".$this->id."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			$rep = mysqli_fetch_array($req);
			$result = $rep['classe'];
		}
		return $result;
	}
	
}	
?>