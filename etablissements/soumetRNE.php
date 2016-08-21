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

$nom = $_POST['nom'][$enregistrer];
$niveau = $_POST['niveau'][$enregistrer];
$type = $_POST['type'][$enregistrer];
$cp = $_POST['cp'][$enregistrer];
$ville = $_POST['ville'][$enregistrer];


//$data = array($enregistrer,$_POST['nom_'.$enregistrer],$_POST['niveau_'.$enregistrer],$_POST['type_'.$enregistrer],$_POST['cp_'.$enregistrer],$_POST['ville_'.$enregistrer]);
$data = array($enregistrer, $nom ,$niveau ,$type, $cp,$ville);

$message = "Id : ".$data[0]." - nom : ".$data[1]." - "
   . "niveau : ". $data[2]." - type : ".$data[3]." - "
   . "cp : ".$data[4]." - ville : ".$data[5] ;
if (enregistreEtab($data)) {
	$_SESSION['msg_etab'] = "Établissement enregistré → ".$message;
} else {
	$_SESSION['msg_etab'] = "Échec de l'enregistrement de ".$message;
}
