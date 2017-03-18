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
$soumetSelection = filter_input(INPUT_POST, 'soumetSelection');

$ajouteAP = filter_input(INPUT_POST, 'creeAP');

$msg_requetesAdmin="";

if ($soumetSelection) {
	$_SESSION['afficheClasse']=array();
	foreach ($classesSelectionnee as $key=>$value) {
		$_SESSION['afficheClasse'][]=$key;
	}
	$msg_requetesAdmin.="<span style='color:green'>".count($classesSelectionnee)." classe(s) sélectionnée(s).</span><br />";
}


foreach ($newResponsable as $valeur) {
	if ($valeur) {
		$sqlNewResponsable = "INSERT INTO lsun_responsables (id, login) VALUES ('', '".$valeur."')";
		//echo $sqlNewResponsable;
		$resultchargeDB = $mysqli->query($sqlNewResponsable);
		$msg_requetesAdmin.="<span style='color:green'>Ajout du responsable établissement ".civ_nom_prenom($valeur).".</span><br />";
	}
}

$supprimeResponsable = filter_input(INPUT_POST, 'supprimeResponsable');

if ($supprimeResponsable) {
	$sqlNewResponsable = "DELETE FROM lsun_responsables WHERE login = '".$supprimeResponsable."'" ;
	//echo $sqlNewResponsable;
	$resultchargeDB = $mysqli->query($sqlNewResponsable);
	$msg_requetesAdmin.="<span style='color:green'>Suppression de ".civ_nom_prenom($supprimeResponsable)." comme responsable établissement.</span><br />";
}

// on crée un parcours
$newParcours = filter_input(INPUT_POST, 'ajouteParcours');
if ('y' == $newParcours) {
	$newParcoursTrim = filter_input(INPUT_POST, 'newParcoursPeriode');
	$newParcoursClasse = filter_input(INPUT_POST, 'newParcoursClasse');
	$newParcoursCode = filter_input(INPUT_POST, 'newParcoursCode');
	$newParcoursTexte = filter_input(INPUT_POST, 'newParcoursTexte');
	//echo 'on crée '.$newParcoursTrim.' → '.$newParcoursClasse.' → '.$newParcoursCode.' → '.$newParcoursTexte;
	$res_creation_parcours=creeParcours($newParcoursTrim, $newParcoursClasse, $newParcoursCode, $newParcoursTexte);
	if($res_creation_parcours) {
		$msg_requetesAdmin.="<span style='color:green'>Nouveau parcours créé.</span><br />";

		$newParcoursLien = filter_input(INPUT_POST, 'newParcoursLien');
		//echo "\$newParcoursLien=$newParcoursLien<br />";
		if(preg_match("/^[0-9]{1,}$/", $newParcoursLien)) {
			$id_new_parcours=mysqli_insert_id($mysqli);
			//echo "\$id_new_parcours=$id_new_parcours<br />";
			if(preg_match("/^[0-9]{1,}$/", $id_new_parcours)) {
				if(!modifieParcours($id_new_parcours, $newParcoursCode, $newParcoursTexte, $newParcoursLien)) {
					$msg_requetesAdmin.="<span style='color:red'>Échec lors de la liaison AID.</span><br />";
				}
				/*
				else {
					$msg_requetesAdmin.="<span style='color:green'>Liaison AID effectuée.</span><br />";
				}
				*/
			}
		}
	}
	else {
		$msg_requetesAdmin.="<span style='color:red'>Échec lors de la création du Nouveau parcours.</span><br />";
	}
}

// on supprime un parcours
$deleteParcours = filter_input(INPUT_POST, 'supprimeParcours', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

if (count($deleteParcours)) {
	//var_dump($deleteParcours);
	//echo '<br>';
	foreach ($deleteParcours as $key=>$value) {
		if(supprimeParcours($key)) {
			$msg_requetesAdmin.="<span style='color:green'>Parcours n°$key supprimé.</span><br />";
		}
		else {
			$msg_requetesAdmin.="<span style='color:red'>Échec lors de la suppresion du parcours n°$key.</span><br />";
		}
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
	$modifieParcoursLien = filter_input(INPUT_POST, 'modifieParcoursLien', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	//echo $modifieParcoursTexte[$modifieParcours];
	//modifieParcours($newParcoursTrim, $newParcoursClasse, $newParcoursCode, $newParcoursTexte, $modifieParcours);
	if(modifieParcours($modifieParcoursId[$modifieParcours], $modifieParcoursCode[$modifieParcours], $modifieParcoursTexte[$modifieParcours], $modifieParcoursLien[$modifieParcours])) {
		$msg_requetesAdmin.="<span style='color:green'>Parcours modifié.</span><br />";
	}
	else {
		$msg_requetesAdmin.="<span style='color:red'>Échec lors de la modification du parcours.</span><br />";
	}
}

if ($ajouteEPI) {
	$newEpiPeriode = filter_input(INPUT_POST, 'newEpiPeriode');
	$newEpiClasse = filter_input(INPUT_POST, 'newEpiClasse', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$newEpiCode = filter_input(INPUT_POST, 'newEpiCode');
	$newEpiIntitule = filter_input(INPUT_POST, 'newEpiIntitule');
	$newEpiMatiere = filter_input(INPUT_POST, 'newEpiMatiere', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$newEpiDescription = filter_input(INPUT_POST, 'newEpiDescription');

	// Suppression des retours à la ligne.
	//$newEpiDescription=preg_replace('/\r/', " - ", preg_replace('/\n/', " - ", preg_replace('/\r\n/', " - ", $newEpiDescription)));
	// A faire plutôt à l'export

	sauveEPI($newEpiPeriode, $newEpiClasse, $newEpiCode, $newEpiIntitule, $newEpiDescription, $newEpiMatiere);
}

if ($supprimeEPI) {
	//echo "supprimeEPI($supprimeEPI)<br />";
	if(supprimeEPI($supprimeEPI)) {
		$msg_requetesAdmin.="<span style='color:green'>EPI supprimé.</span><br />";
	}
	else {
		$msg_requetesAdmin.="<span style='color:red'>Échec lors de la suppression de l'EPI.</span><br />";
	}
}

if ($modifieEPI) {
	$modifieEPIPeriode = filter_input(INPUT_POST, 'modifieEpiPeriode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPICode = filter_input(INPUT_POST, 'modifieEpiCode', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPIIntitule = filter_input(INPUT_POST, 'modifieEpiIntitule', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$modifieEPIMatiere = filter_input(INPUT_POST, 'modifieEpiMatiere'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	// var_dump($modifieEPIMatiere);
	//echo '<br>';
	$modifieEPIDescription = filter_input(INPUT_POST, 'modifieEpiDescription', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	// Suppression des retours à la ligne.
	//foreach($modifieEPIDescription as $key => $value) {
	//	$modifieEPIDescription[$key]=preg_replace('/\r/', " - ", preg_replace('/\n/', " - ", preg_replace('/\r\n/', " - ", $value)));
	//}
	// A faire plutôt à l'export

	$modifieEPIClasse = filter_input(INPUT_POST, 'modifieEpiClasse'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	//var_dump($modifieEPIClasse);
	sauveEPI($modifieEPIPeriode[$modifieEPI], $modifieEPIClasse, $modifieEPICode[$modifieEPI], $modifieEPIIntitule[$modifieEPI], $modifieEPIDescription[$modifieEPI], $modifieEPIMatiere, $modifieEPI);
	
	$listeModifieEpiLiaison = filter_input(INPUT_POST, 'modifieEpiLiaison'.$modifieEPI, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	if ($listeModifieEpiLiaison) {
		foreach ($listeModifieEpiLiaison as $lien) {
//echo "<p>\$lien=$lien</p>";
			$tableauModifieEpiLiaison = explode('-', $lien);
			if ($tableauModifieEpiLiaison[0] == "aid") {
				$aid = 1;
			} else {
				$aid = 0;
			}
/*
echo "<pre>";
print_r($tableauModifieEpiLiaison);
echo "</pre>";
*/
			if(isset($tableauModifieEpiLiaison[1])) {
				$id_enseignement = $tableauModifieEpiLiaison[1];
				$id_epi = $modifieEPI;
				//$modifieEpiLiaison[][num] = $tableauModifieEpiLiaison[1];
				//echo $id_enseignement."<br>";
				if(!lieEpiCours($id_epi , $id_enseignement , $aid)) {
					$msg_requetesAdmin.="<span style='color:red'>Erreur lors de la liaison de l'EPI avec l'AID n°$aid.</span><br />";
				}
			}
		}
	}
}

if ($ajouteAP) {
	$newApIntitule = filter_input(INPUT_POST, 'newApIntituleAP');
	$newApDescription = filter_input(INPUT_POST, 'newApDescription');
	$newApDisciplines = filter_input(INPUT_POST, 'newApDisciplines', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$newApLiaisonAID = filter_input(INPUT_POST, 'newApLiaisonAID' );
	saveAP($newApIntitule, $newApDisciplines, $newApDescription, $newApLiaisonAID);
}


//===== Suppression ou modification des AP =====
$supprimerAp = filter_input(INPUT_POST, 'supprimerAp');
$modifierAp = filter_input(INPUT_POST, 'modifierAp');

if ($supprimerAp) {
	delAP($supprimerAp);
}

if ($modifierAp) {
	$changeIntituleAp = filter_input(INPUT_POST, 'intituleAp', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeApDescription = filter_input(INPUT_POST, 'ApDescription', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeLiaisonApAid = filter_input(INPUT_POST, 'liaisonApAid', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$changeApDisciplines = filter_input(INPUT_POST, 'ApDisciplines'.$modifierAp, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	
	modifieAP($modifierAp, $changeIntituleAp[$modifierAp], $changeApDescription[$modifierAp], $changeLiaisonApAid[$modifierAp], $changeApDisciplines);
}

if(isset($_GET['nettoyer_doublons_AP'])) {
	check_token(false);

	$nettoyage_anomalies_tables_AP=corrige_anomalie_mod_LSUN();
	if($nettoyage_anomalies_tables_AP!="") {
		echo "<div align='center'>".$nettoyage_anomalies_tables_AP."</div>";
	}
}

