<?php

/*
 *
 * Copyright 2015 Régis Bouguin
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

/*

    [eleves.nom]
    [eleves.prenom]
    [eleves.sexe]
    [eleves.date_nais]
    [eleves.lieu_nais]
    [eleves.classe]
    [eleves.ine]
    [eleves.elenoet]
    [eleves.ele_id]
    [eleves.login]
 */


/*
echo 'AID dans 1 fichier<br />';
print_r($id_AID);
echo '<br />';
print_r($num_fich);
echo '<br />';
 */

$i = 0;
$filtre = "";
foreach ($id_AID as $id) {
	if ($i) {
		$filtre .= "OR ";
	}
	$filtre .= "a.`id_aid` LIKE '".$id."' ";
	$i++;
}

$sqlElv = "SELECT  DISTINCT a.`login` , e.`nom` , e.`prenom` , "
   . "e.`sexe` , e.`naissance` , e.`lieu_naissance` , "
   . "c.`classe` , e.`no_gep` , e.`elenoet`, e.`ele_id` "
   . "FROM  `j_aid_eleves` AS a "
   . "INNER JOIN `eleves` AS e ON e.`login` = a.`login`"
   . "INNER JOIN `j_eleves_classes` AS jec ON jec.`login` = e.`login` "
   . "INNER JOIN `classes` AS c ON c.`id` = jec.`id_classe` "
   . "WHERE ".$filtre." "
   . "ORDER BY e.nom, e.prenom ";
// echo $sqlElv.'<br />';
$resElv = mysqli_query($mysqli, $sqlElv);

if(mysqli_num_rows($resElv)>0) {
	$tab_eleves_OOo=array();
	$nb_eleve=0;
	
	while($lig=mysqli_fetch_object($resElv)) {
		$nb_eleve_actuel=$nb_eleve;
		include 'lib/charge_tableau.php';
		$tab_eleves_OOo[$nb_eleve_actuel]['classe']=$lig->classe;
	}
}




