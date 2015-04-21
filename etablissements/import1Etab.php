<?php
/*
 *
 * Copyright 2015 Bouguin Régis
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

if ($_POST['csv_file_'.$recherche] != "choisissez un fichier") {
	if(!preg_match("/^[A-Za-z0-9_]*.csv$/", $_POST['csv_file_'.$recherche])) {
		$_SESSION['msg_etab'] = "Fichier proposé ".$_POST['csv_file_'.$recherche]." invalide";
	}
	else {
		$fichierCSV =  './bases/'.$_POST['csv_file_'.$recherche];
		$separateur = ';';
	
		if (($handle = fopen($fichierCSV, "r")) !== FALSE) {
			$_SESSION['msg_etab'] = "Établissement ".$recherche." non trouvé dans ".$_POST['csv_file_'.$recherche];
			while (($data = fgetcsv($handle, 10000, $separateur)) !== FALSE) {
				$num = count($data);

				if ($data[0] == $recherche) {
					//On a trouvé l'établissement, on l'ajoute dans la base
					$message = "Id : ".$data[0]." - nom : ".$data[1]." - "
					   . "niveau : ". $data[2]." - type : ".$data[3]." - "
					   . "cp : ".$data[4]." - ville : ".$data[5] ;
					if (enregistreEtab($data)) {
						$_SESSION['msg_etab'] = "Établissement enregistré → ".$message;
					} else {
						$_SESSION['msg_etab'] = "Échec de l'enregistrement de ".$message;
					}
					break;
				}
			}
			fclose($handle);
		}
	}
} else {
	$_SESSION['msg_etab'] = "Vous devez choisir un fichier .csv où rechercher l'établissement";
}




