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
class PeriodeNote {


	public $nom;
	public $numero;
	public $verrouillage;
	public $id_classe;
	public $date_verrouillage;
	
	
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
		$sql="INSERT INTO periodes SET 
				nom_periode = '".$this->nom."',
				num_periode = '".$this->numero."',
				verouiller = '".$this->verrouillage."',
				id_classe = '".$this->id_classe."',
				date_verrouillage = '".$this->date_verrouillage."'";
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
 
	public function getPeriods() {
		$result = array();
		$sql="SELECT nom FROM periodes WHERE 
				id_classe = '".$this->id_classe."' ";
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($req) {
			while ($rep=mysqli_fetch_array($req)) {
				$result[] = $rep['nom'];
			}
		}
		return $result;
	}

}

?>
