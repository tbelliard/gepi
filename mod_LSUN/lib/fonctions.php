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
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 
*/

include_once 'fonctions_EPI.php';
include_once 'fonctions_AP.php';

function enregistreMEF() {
	global $mysqli;
	$classeBase = filter_input(INPUT_POST, 'classeBase', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	$classeMEF = filter_input(INPUT_POST, 'mefAppartenance', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	foreach ($classeBase as $key=>$value) {
		$sqlSaveMEF = "UPDATE classes SET mef_code = '$classeMEF[$key]' WHERE id = $key ";
		//echo $sqlSaveMEF.'<br>';
		$mysqli->query($sqlSaveMEF);	
	}
}

/**
 * Renvoie la première partie de l'année scolaire au format complet (2009/2010 ou 2009/10 ou 2009-2010 …)
 * 
 * @param String $_annee année scolaire
 * @return int début de l'année scolaire
 */
function LSUN_annee($_annee) {
	$expl = preg_split("/[^0-9]/", $_annee);
	$retour = intval($expl[0]);
	//$retour = ($expl[0]);
	return $retour;
}

/**
 * Récupère les données de la table utilisateurs
 *   
 * @global type $mysqli
 * @param String $login Login de l'utilisateur
 * @return Object mysqliQuery
 */
function getUtilisateur($login) {
	global $mysqli;
	$sql = "SELECT * FROM `utilisateurs` WHERE login = '".$login."' ";
	//echo "<br />".$sql."<br />";
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB->fetch_object();	
}

/**
 * Récupère les utilisateurs sur le statut
 * 
 * @global type $mysqli
 * @param string $statut statut recherché
 * @return type
 */
function getUtilisateurSurStatut($statut = "%") {
	global $mysqli;
	$sql = "SELECT login, nom, prenom FROM utilisateurs WHERE statut LIKE '".$statut."' ";
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

/**
 * Récupère id, nom, prenom civilité d'un responsable du livret
 * 
 * @global type $mysqli
 * @return type
 */
function getResponsables() {
	global $mysqli;
	$sql = "SELECT r.id, r.login, u.nom, u.prenom, u.civilite FROM lsun_responsables AS r "
		. "INNER JOIN utilisateurs AS u "
		. "ON u.login = r.login ";
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

/**
 * Récupçre les périodes
 * 
 * @global type $mysqli
 * @param type $myData id des classes 
 * @return type
 */
function getPeriodes($myData = NULL) {
	global $mysqli;
	$sqlPeriodes = "SELECT DISTINCT num_periode FROM periodes ";
	if ($myData) {
		$sqlPeriodes .=  "WHERE id_classe IN (".$myData.") ";
	}
	$sqlPeriodes .=  "ORDER BY num_periode ";
	//echo $sqlPeriodes;
	$resultchargeDB = $mysqli->query($sqlPeriodes);	
	return $resultchargeDB;	
}

/**
 * Renvoie id, classe, nom_complet  d'une classe
 * 
 * @global type $mysqli
 * @param type $classeId
 * @return type
 */
function getClasses($classeId = NULL) {
	global $mysqli;
	$sqlClasses = "SELECT DISTINCT id, classe, nom_complet, mef_code FROM classes ";
	if ($classeId) {$sqlClasses .= " WHERE id = '$classeId' ";}
	$sqlClasses .= " ORDER BY classe ";
	//echo $sqlClasses;
	$resultchargeDB = $mysqli->query($sqlClasses);	
	return $resultchargeDB;	
}

/**
 * Enregistre les données d'un parcours
 * 
 * @global type $mysqli
 * @param type $newParcoursTrim
 * @param type $newParcoursClasse
 * @param type $newParcoursCode
 * @param type $newParcoursTexte
 * @param type $newParcoursId
 * @return type
 */
function creeParcours($newParcoursTrim, $newParcoursClasse, $newParcoursCode, $newParcoursTexte, $newParcoursId = '') {
	global $mysqli;
	$sqlNewParcours = "INSERT INTO lsun_parcours_communs (id,periode,classe,codeParcours,description)  VALUES ('$newParcoursId', '$newParcoursTrim', '$newParcoursClasse', '$newParcoursCode', '$newParcoursTexte') ON DUPLICATE KEY UPDATE periode = '$newParcoursTrim',classe = '$newParcoursClasse',codeParcours = '$newParcoursCode',description = '$newParcoursTexte' ";
	//echo $sqlNewParcours;
	$resultchargeDB = $mysqli->query($sqlNewParcours);	
	return $resultchargeDB;	
}

/**
 * Récupère un parcours
 * 
 * @global type $mysqli
 * @global type $selectionClasse
 * @param type $parcoursId
 * @param type $classe
 * @param type $periode
 * @return type
 */
function getParcoursCommuns($parcoursId = NULL, $classe = NULL, $periode = NULL) {
	global $mysqli;
	global $selectionClasse;
	$myData = implode(",", $selectionClasse);
	$sqlGetParcours = "SELECT * FROM lsun_parcours_communs WHERE classe IN ($myData) ";
	if($parcoursId || $classe || $periode) {
		$sqlGetParcours .= setFiltreParcoursCommuns($parcoursId, $classe, $periode);
		//echo $sqlGetParcours;
	}
	$sqlGetParcours .= " ORDER BY classe, periode, codeParcours ";
	//echo $sqlGetParcours;
	$resultchargeDB = $mysqli->query($sqlGetParcours);	
	return $resultchargeDB;	
}

/**
 * Filtre pour récupérer un parcours
 * 
 * @param type $parcoursId
 * @param type $classe
 * @param type $periode
 * @return type
 */
function setFiltreParcoursCommuns($parcoursId, $classe, $periode) {
	$sqlGetParcours = " AND ";
	if ($parcoursId) {
		$sqlGetParcours .= " id = '$parcoursId' ";
		if ($classe || $periode) {
			$sqlGetParcours .= " AND ";
		}
	}
	if ($classe) {
		$sqlGetParcours .= " classe = '$classe' ";
		if ($periode) {
			$sqlGetParcours .= " AND ";
		}
	}
	if ($periode) {$sqlGetParcours .= " periode = '$periode' ";}
	return $sqlGetParcours;
}

/**
 * Suppression d'un parcours
 * 
 * @global type $mysqli
 * @param type $deleteParcours
 */
function supprimeParcours($deleteParcours) {
	global $mysqli;
	$sqlDelParcours = "DELETE FROM lsun_parcours_communs WHERE id = $deleteParcours ";
	//echo $sqlDelParcours;
	$mysqli->query($sqlDelParcours);
}

/**
 * Modification d'un parcours
 * 
 * @global type $mysqli
 * @param type $modifieParcoursId
 * @param type $modifieParcoursCode
 * @param type $modifieParcoursTexte
 */
function modifieParcours($modifieParcoursId, $modifieParcoursCode, $modifieParcoursTexte) {
	global $mysqli;
	$sqlModifieParcours = "UPDATE lsun_parcours_communs "
		. "SET codeParcours = '$modifieParcoursCode', description = '$modifieParcoursTexte' "
		. "WHERE id = '$modifieParcoursId' ";
	//echo $sqlModifieParcours;
	$mysqli->query($sqlModifieParcours);
}

/**
 * 
 * @global type $mysqli
 * @param type $mefClasse
 * @return type
 */
function getMatiereLSUN($mefClasse = NULL) {
	global $mysqli;
	
	 $sqlMatieres = "SELECT DISTINCT m.*, mm.code_modalite_elect FROM mef_matieres AS mm INNER JOIN matieres AS m ON mm.code_matiere = m.code_matiere ";
	 if ($mefClasse) {
		 $sqlMatieres .= "WHERE mef_code = $mefClasse ";
	 }
	$sqlMatieres .= " ORDER BY matiere , code_modalite_elect DESC ";
	// echo $sqlMatieres;
	$resultchargeDB = $mysqli->query($sqlMatieres);
	return $resultchargeDB;
}


/**
 * Retourne une matière sur son nom court
 * 
 * @global type $mysqli
 * @param type $matiere
 * @return type
 */
function getMatiereOnMatiere($matiere) {
	global $mysqli;
	$sqlGetMatiereOnMatiere = "SELECT * FROM matieres WHERE matiere = '$matiere' ";
	//echo $sqlGetMatiereOnMatiere;
	$resultchargeDB = $mysqli->query($sqlGetMatiereOnMatiere);
	$retour = $resultchargeDB->fetch_object();
	return $retour;
}


function getCoursById($id) {
	global $mysqli;
	
	$sqlGetCours = "SELECT DISTINCT t1.*, c.classe FROM "
		. "(SELECT t0.* , jgc.id_classe FROM "
		. "(SELECT jgt.*,jgm.id_matiere FROM j_groupes_types AS jgt "
		. "INNER JOIN j_groupes_matieres AS jgm ON jgt.id_groupe = jgm.id_groupe "
		. "WHERE jgt.id_groupe = '$id') AS t0 "
		. "INNER JOIN j_groupes_classes AS jgc ON jgc.id_groupe = t0.id_groupe) AS t1 "
		. "INNER JOIN classes AS c ON c.id = t1.id_classe "
		. "ORDER BY id_groupe ";
	
	//echo '<br><br>'.$sqlGetCours.'<br><br>';
	$retour = $mysqli->query($sqlGetCours);
	return $retour;
}

function MefAppartenanceAbsent() {
	global $mysqli;
	$retour = FALSE;
	$sqlGetMefAppartenance = "SELECT 1=1 FROM classes WHERE mef_code = '' ";
	//echo $sqlGetMefAppartenance;
	if ($mysqli->query($sqlGetMefAppartenance)->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}


function getMatiereSurMEF($mef) {
	global $mysqli;
	$sqlMef = "SELECT * FROM `matieres` WHERE `code_matiere` LIKE '$mef' ";
	//echo $sqlMef;
	$resultchargeDB = $mysqli->query($sqlMef);
	return $resultchargeDB;
}

function getCommentaireGroupe($id_aid,$periode = NULL) {
	//echo $id_aid."-".$periode;
	global $mysqli;
	$sqlAppGroupe = "SELECT * FROM `aid_appreciations_grp` WHERE `id_aid` = $id_aid ";
	if($periode) {
		$sqlAppGroupe .= "AND `periode` = $periode ";
	}
	
	$resultchargeDB = $mysqli->query($sqlAppGroupe);
	return $resultchargeDB;
}

function droitLSUN($droit=NULL) {
	global $mysqli;
	if ($droit === NULL) {
		$retour = FALSE;
		$sql = "SELECT `VALUE` FROM `setting` WHERE `NAME`='active_module_LSUN' ";
		$valeur = $mysqli->query($sql)->fetch_object()->VALUE;
		if ($valeur === 'y') {
			$retour = TRUE;
		}
		return $retour;
	} elseif ('y' == $droit || 'n' == $droit) {
		$sql = "INSERT INTO `setting` (`NAME`, `VALUE`) VALUES ('active_module_LSUN', '".$droit."') "
	   . "ON DUPLICATE KEY UPDATE VALUE = '".$droit."' ";
		mysqli_query($mysqli, $sql);
	}
}

