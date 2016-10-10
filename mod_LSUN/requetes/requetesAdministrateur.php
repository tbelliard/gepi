<?php

/*
*
* Copyright 2016 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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


$newResponsable[] = filter_input(INPUT_POST, 'responsableAdmin');
$newResponsable[] = filter_input(INPUT_POST, 'responsableCPE');
$newResponsable[] = filter_input(INPUT_POST, 'responsableEnseignant');
$classesSelectionnee = filter_input(INPUT_POST, 'afficheClasse', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
$_SESSION['afficheClasse'] = $classesSelectionnee ? $classesSelectionnee : (isset($_SESSION['afficheClasse']) ? $_SESSION['afficheClasse'] : NULL);

$ajouteEPI = filter_input(INPUT_POST, 'ajouteEPI');
$supprimeEPI = filter_input(INPUT_POST, 'supprimeEpi');
$modifieEPI = filter_input(INPUT_POST, 'modifieEpi');


if (count($classesSelectionnee)) {
	$_SESSION['afficheClasse']=array();
	foreach ($classesSelectionnee as $key=>$value) {
		$_SESSION['afficheClasse'][]=$key;
	}
}


foreach ($newResponsable as $valeur) {
	if ($valeur) {
		$sqlNewResponsable = "INSERT INTO lsun_responsables (id, login) VALUES ('', '".$valeur."')";
		//echo $sqlNewResponsable;
		$resultchargeDB = $mysqli->query($sqlNewResponsable);
	}
}

$supprimeResponsable = filter_input(INPUT_POST, 'supprimeResponsable');

if ($supprimeResponsable) {
	$sqlNewResponsable = "DELETE FROM lsun_responsables WHERE login = '".$supprimeResponsable."'" ;
	//echo $sqlNewResponsable;
	$resultchargeDB = $mysqli->query($sqlNewResponsable);
}

// on crée un parcours
$newParcours = filter_input(INPUT_POST, 'ajouteParcours');
if ('y' == $newParcours) {
	$newParcoursTrim = filter_input(INPUT_POST, 'newParcoursPeriode');
	$newParcoursClasse = filter_input(INPUT_POST, 'newParcoursClasse');
	$newParcoursCode = filter_input(INPUT_POST, 'newParcoursCode');
	$newParcoursTexte = filter_input(INPUT_POST, 'newParcoursTexte');
	//echo 'on crée '.$newParcoursTrim.' → '.$newParcoursClasse.' → '.$newParcoursCode.' → '.$newParcoursTexte;
	creeParcours($newParcoursTrim, $newParcoursClasse, $newParcoursCode, $newParcoursTexte);
}

// on supprime un parcours
$deleteParcours = filter_input(INPUT_POST, 'supprimeParcours', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

if (count($deleteParcours)) {
	//var_dump($deleteParcours);
	//echo '<br>';
	foreach ($deleteParcours as $key=>$value) {
		supprimeParcours($key);
	}
}

// on modifie un parcours
$modifieParcours = filter_input(INPUT_POST, 'modifieParcours');
if ($modifieParcours) {
	$modifieParcoursPeriode = filter_input(INPUT_POST, 'modifieParcoursPeriode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieParcoursClasse = filter_input(INPUT_POST, 'modifieParcoursClasse', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieParcoursCode = filter_input(INPUT_POST, 'modifieParcoursCode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieParcoursTexte = filter_input(INPUT_POST, 'modifieParcoursTexte', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieParcoursId = filter_input(INPUT_POST, 'modifieParcoursId', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	//echo $modifieParcoursTexte[$modifieParcours];
	//modifieParcours($newParcoursTrim, $newParcoursClasse, $newParcoursCode, $newParcoursTexte, $modifieParcours);
	modifieParcours($modifieParcoursId[$modifieParcours], $modifieParcoursCode[$modifieParcours], $modifieParcoursTexte[$modifieParcours]);
}

if ($ajouteEPI) {
	$newEpiPeriode = filter_input(INPUT_POST, 'newEpiPeriode');
	$newEpiClasse = filter_input(INPUT_POST, 'newEpiClasse', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$newEpiCode = filter_input(INPUT_POST, 'newEpiCode');
	$newEpiIntitule = filter_input(INPUT_POST, 'newEpiIntitule');
	$newEpiMatiere = filter_input(INPUT_POST, 'newEpiMatiere', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$newEpiDescription = filter_input(INPUT_POST, 'newEpiDescription');
	
	sauveEPI($newEpiPeriode, $newEpiClasse, $newEpiCode, $newEpiIntitule, $newEpiDescription, $newEpiMatiere);
}

if ($supprimeEPI) {
	supprimeEPI($supprimeEPI);
}

if ($modifieEPI) {
	$modifieEPIPeriode = filter_input(INPUT_POST, 'modifieEpiPeriode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPICode = filter_input(INPUT_POST, 'modifieEpiCode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPIIntitule = filter_input(INPUT_POST, 'modifieEpiIntitule', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPIMatiere = filter_input(INPUT_POST, 'modifieEpiMatiere'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	// var_dump($modifieEPIMatiere);
	echo '<br>';
	$modifieEPIDescription = filter_input(INPUT_POST, 'modifieEpiDescription', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPIClasse = filter_input(INPUT_POST, 'modifieEpiClasse'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	// var_dump($modifieEPIClasse);
	sauveEPI($modifieEPIPeriode[$modifieEPI], $modifieEPIClasse, $modifieEPICode[$modifieEPI], $modifieEPIIntitule[$modifieEPI], $modifieEPIDescription[$modifieEPI], $modifieEPIMatiere, $modifieEPI);
	
	$listeModifieEpiLiaison = filter_input(INPUT_POST, 'modifieEpiLiaison'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	if ($listeModifieEpiLiaison) {
		foreach ($listeModifieEpiLiaison as $lien) {
			$tableauModifieEpiLiaison = explode('-', $lien);
			if ($tableauModifieEpiLiaison[0] == "aid") {
				$aid = 1;
			} else {
				$aid = 0;
			}
			$id_enseignement = $tableauModifieEpiLiaison[1];
			$id_epi = $modifieEPI;
			//$modifieEpiLiaison[][num] = $tableauModifieEpiLiaison[1];
			lieEpiCours($id_epi , $id_enseignement , $aid);
		}
	}
	//echo var_dump($modifieEpiLiaison);
	
	//modifieEpiLiaison31
	//lieEpiCours($id_epi , $id_enseignement , $aid);
}


