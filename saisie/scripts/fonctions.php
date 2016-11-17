<?php

/*
*
* Copyright 2016 Bouguin Régis
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

/**
 * Renvoie tous les éléments de programme
 * 
 * @global DB_connect $mysqli
 * @return mysqli_query
 */
function getToutElemProg($quePerso = false, $queMat = false, $queNiveau = false, $matiere = NULL) {
	global $mysqli;
	
	if (!$quePerso && !$queMat) {
		$sql = "SELECT * FROM matiere_element_programme AS mep ORDER BY mep.libelle";
	} elseif ($quePerso) {
			$sql = "SELECT mep.* FROM matiere_element_programme AS mep INNER JOIN j_mep_prof AS jmp ON jmp.idEP = mep.id WHERE jmp.id_prof = '".$_SESSION['login']."' ORDER BY mep.libelle ";
		if ($queMat) {
			
			$sql = "SELECT t0.* FROM (SELECT mep.* FROM matiere_element_programme AS mep INNER JOIN j_mep_prof AS jmp ON jmp.idEP = mep.id WHERE jmp.id_prof = '".$_SESSION['login']."' ) AS t0 "
				. "INNER JOIN j_mep_mat AS jmm ON jmm.idEP = t0.id WHERE jmm.idMat = '".$matiere."' ORDER BY t0.libelle ";
		}
	} else {
		$sql = "SELECT mep.* FROM matiere_element_programme AS mep INNER JOIN j_mep_mat AS jmm ON jmm.idEP = mep.id WHERE jmm.idMat = '".$matiere."'";
	}
	
	if ($queNiveau) {
		global $id_groupe;
		$sql1 = "SELECT jmn.* FROM (SELECT DISTINCT e.mef_code FROM eleves AS e INNER JOIN j_eleves_groupes AS jeg ON e.login = jeg.login WHERE jeg.id_groupe = '".$id_groupe."' ) AS tm "
			. "INNER JOIN j_mep_niveau AS jmn ON jmn.idNiveau = tm.mef_code ";
		
		$sql = "SELECT DISTINCT ts.*  FROM (".$sql1.") AS tn INNER JOIN (".$sql.") AS ts ON ts.id = tn.idEP ";
	}
	
	$resultchargeDB = $mysqli->query($sql);
	
	return $resultchargeDB;
	
}

/**
 * Renvoie tous les éléments de programme d'un groupe
 * 
 * @global DB_connect $mysqli
 * @param type $groupe
 * @param type $anneeScolaire
 * @param type $periode
 * @return type
 */
function getGroupElemProg($groupe, $anneeScolaire, $periode) {
	global $mysqli;
	
	$sql = "SELECT * FROM matiere_element_programme AS mep "
		. "INNER JOIN j_mep_groupe AS jmp "
		. "ON mep.id = jmp.idEP "
		. "WHERE jmp.periode = '".$periode."' "
		. "AND jmp.annee = '".$anneeScolaire."' "
		. "AND jmp.idGroupe = '".$groupe."' "
		. "ORDER BY mep.libelle";
	
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}


/**
 * Renvoie un éléments de programme par son libelle
 * 
 * @global DB_connect $mysqli
 * @param string $libelle
 * @return mysqli_query
 */
function getElemProgByLibelle($libelle) {
	global $mysqli;
	
	$sql = "SELECT * FROM matiere_element_programme "
		. "WHERE libelle = \"".$libelle."\" ";
	
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}

/**
 * Sauvegarde un nouvel élément de programme
 * 
 * @global DB_connect $mysqli
 * @param string $id_groupe
 * @param string $newElemGroupe
 * @param string $annee
 * @param string $periode
 */
function saveNewElemGroupe($id_groupe, $newElemGroupe, $annee, $periode) {
	global $mefDuGroupe;
	
	//enregistre le nouvel élément
	saveNewElement($newElemGroupe);
	
	// récupère l'id de l'élément
	$idNewLibelle = getElemProgByLibelle($newElemGroupe)->fetch_object()->id;
	//echo $idNewLibelle.'<br />';
	
	// enregistre la jointure avec le groupe
	saveJointureGroupeEP($id_groupe, $idNewLibelle, $annee, $periode);
	
	// enregistre la jointure de chaque élève du groupe
	$groupe = get_group($id_groupe);
	foreach ($groupe['eleves'][$periode]['list'] as $login) {
		saveJointureEleveEP($login, $idNewLibelle, $annee, $periode, $id_groupe);
	}
	
	// enregistre la jointure du prof '.$_SESSION['login']
	saveJointureProfEP($_SESSION['login'], $idNewLibelle);

	// enregistre la jointure avec la matière';
	$matiere = $groupe["matiere"]["matiere"];
	saveJointureMatiereEP($matiere, $idNewLibelle);

	// enregistre la jointure avec le niveau
	if ($mefDuGroupe) {
		saveJointureEPMef($mefDuGroupe,$idNewLibelle);
	}
	
}

/**
 * Enregistre un nouvel élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $newElemGroupe
 */
function saveNewElement($newElemGroupe) {
	global $mysqli;
	$sql1 = "INSERT INTO matiere_element_programme (`id`, `libelle`, `id_user`) "
		. "VALUES (NULL, \"".$newElemGroupe."\", \"".$_SESSION['login']."\") "
		. "ON DUPLICATE KEY UPDATE `libelle` = \"".$newElemGroupe."\" ; ";
	// echo '<br />'.$sql1.'<br />';
	$mysqli->query($sql1);
	
}

/**
 *  Enregistre la jointure groupe/élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $id_groupe
 * @param type $idNewLibelle
 * @param type $annee
 * @param type $periode
 */
function saveJointureGroupeEP($id_groupe, $idNewLibelle, $annee, $periode) {
	global $mysqli;
	$sql = "INSERT INTO j_mep_groupe ( id , idEP , idGroupe , annee , periode) "
		. "VALUES (NULL, '".$idNewLibelle."' , '".$id_groupe."' , '".$annee."' , '".$periode."') "
		. "ON DUPLICATE KEY UPDATE `idGroupe` = '".$id_groupe."' ; ";
	//echo $sql.'<br />';
	$mysqli->query($sql);
}

/**
 *  Enregistre la jointure élève/élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $login
 * @param type $idEP
 * @param type $annee
 * @param type $periode
 */
function saveJointureEleveEP($login, $idEP, $annee, $periode, $id_groupe) {
	global $mysqli;
	$sql = "INSERT INTO j_mep_eleve (id , idEP , idEleve , idGroupe, annee , periode, date_insert) "
		. "VALUES (NULL, '".$idEP."' , '".$login."' , '".$id_groupe."' , '".$annee."' , '".$periode."' , '".strftime('%Y-%m-%d %H:%M:%S')."') "
		. "ON DUPLICATE KEY UPDATE `idEleve` = '".$login."' ; ";
	//echo $sql.'<br />';
	$mysqli->query($sql);
	
}

/**
 *  Enregistre la jointure professeur/élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $login
 * @param type $idEP
 */
function saveJointureProfEP($login, $idEP) {
	global $mysqli;
	$sql = "INSERT INTO j_mep_prof (id , idEP , id_prof) "
		. "VALUES (NULL, '".$idEP."' , '".$login."') "
		. "ON DUPLICATE KEY UPDATE `id_prof` = '".$login."' ; ";
	//echo $sql.'<br />';
	$mysqli->query($sql);
}

/**
 *  Enregistre la jointure matière/élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $matiere
 * @param type $idEP
 */
function saveJointureMatiereEP($matiere, $idEP) {
	global $mysqli;
	
	$sql = "INSERT INTO j_mep_mat (id , idEP , idMat) "
		. "VALUES (NULL, '".$idEP."' , '".$matiere."') "
		. "ON DUPLICATE KEY UPDATE `idMat` = '".$matiere."' ; ";
	//echo $sql.'<br />';
	$mysqli->query($sql);
}

/**
 * Enregistre la jointure MEF/élément de programme
 * 
 * @global DB_connect $mysqli
 * @param type $mefDuGroupe
 * @param type $idEP
 */
function saveJointureEPMef($mefDuGroupe , $idEP) {	
	global $mysqli;
	
	while($mef = $mefDuGroupe->fetch_object()){
		$sql = "INSERT INTO j_mep_niveau (id , idEP , idNiveau) "
			. "VALUES (NULL, '".$idEP."' , '".$mef->mef_code."') "
			. "ON DUPLICATE KEY UPDATE `idNiveau` = '".$mef->mef_code."' ; ";
		//echo $sql.'<br />';
		$mysqli->query($sql);
	}
	
}

/**
 * Dissocie un élément de programme d'un groupe et ses élèves 
 * 
 * @param type $id_groupe
 * @param type $idElem
 * @param type $anneeScolaire
 * @param type $periode
 */
function dissocieElemGroupe($id_groupe, $idElem, $annee, $periode) {
	
	// On supprime pour tous les élèves
	$groupe = get_group($id_groupe);
	foreach ($groupe['eleves'][$periode]['list'] as $login) {
		delJointureEleveEP($login, $idElem, $annee, $periode);
	}
	
	// On supprime pour le groupe
	delJointureGroupeEP($id_groupe, $idElem, $annee, $periode);
}

/**
 * Supprime une liaison élève/élément
 * 
 * @global DB_connect $mysqli
 * @param type $login
 * @param type $idElem
 * @param type $annee
 * @param type $periode
 * @throws Exception
 */
function delJointureEleveEP($login, $idElem, $annee, $periode) {
	global $mysqli;
	$sql = "DELETE FROM j_mep_eleve WHERE idEP  = '".$idElem."' AND idEleve = '".$login."' AND annee = '".$annee."' AND periode = '".$periode."' ; ";
	//echo $sql."<br />";
	$mysqli->query($sql);
	
}

/**
 * Supprime une liaison groupe/élément
 * 
 * @global DB_connect $mysqli
 * @param type $id_groupe
 * @param type $idElem
 * @param type $annee
 * @param type $periode
 */
function delJointureGroupeEP($id_groupe, $idElem, $annee, $periode) {
	global $mysqli;
	$sql = "DELETE FROM j_mep_groupe WHERE idEP  = '".$idElem."' AND idGroupe = '".$id_groupe."' AND annee = '".$annee."' AND periode = '".$periode."' ; ";
	//echo $sql."<br />";
	$mysqli->query($sql);
	
}

function associeElemGroupe($id_groupe, $idElem, $annee, $periode) {
		
	// enregistre la jointure avec le groupe
	saveJointureGroupeEP($id_groupe, $idElem, $annee, $periode);
	
	// enregistre la jointure de chaque élève du groupe
	$groupe = get_group($id_groupe);
	foreach ($groupe['eleves'][$periode]['list'] as $login) {
		saveJointureEleveEP($login, $idElem, $annee, $periode, $id_groupe);
		//echo "saveJointureEleveEP($login, $idElem, $annee, $periode, $id_groupe);<br />";
	}
	
	// enregistre la jointure du prof '.$_SESSION['login']
	saveJointureProfEP($_SESSION['login'], $idElem);

	// enregistre la jointure avec la matière';
	$matiere = $groupe["matiere"]["matiere"];
	saveJointureMatiereEP($matiere, $idElem);
	
}

/**
 * Renvoie la matière enseignée pour un groupe
 * 
 * @param type $id_groupe
 * @return type
 */
function getMatiere($id_groupe) {
	$groupe = get_group($id_groupe);
	$matiere = $groupe["matiere"]["matiere"];
	return $matiere;
}

/**
 * Renvoie les éléments de programme d'une période pour un élève
 * 
 * @global DB_connect $mysqli
 * @param type $login_eleve
 * @param type $annee
 * @param type $periode
 * @return type
 */
function getElementEleve($login_eleve, $annee, $periode,$groupe = NULL) {
	//echo $id_eleve.'<br';
	global $mysqli;
	$sql = "SELECT * FROM matiere_element_programme AS mep "
		. "INNER JOIN j_mep_eleve AS jme "
		. "ON mep.id = jme.idEP "
		. "WHERE jme.periode = '".$periode."' "
		. "AND jme.annee = '".$annee."' "
		. "AND jme.idEleve = '".$login_eleve."' ";
	if ($groupe) {
		$sql .= " AND jme.idGroupe = '".$groupe."' ";
	}
	//$sql .= "ORDER BY jme.date_insert, mep.libelle";
	$sql .= "ORDER BY mep.libelle";
	
	//echo $sql."<br />";
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}
	
/**
 * Supprime une association élève/élément
 * 
 * @param type $login
 * @param type $idElem
 * @param type $annee
 * @param type $periode
 * @param type $groupe
 */
function supprimeElemProgElv($login, $idElem, $annee, $periode, $groupe) {
	// on commence par traiter le groupe ".$groupe;
	delJointureGroupeEP($groupe, $idElem, $annee, $periode);
	// puis on traite l'élève ".$login;
	delJointureEleveEP($login, $idElem, $annee, $periode);
}

/**
 * Appelle les fonctions lors de la création d'un élément de programme pour un élève
 * 
 * @param type $loginEleve
 * @param type $id_groupe
 * @param type $texteElem
 * @param type $annee
 * @param type $periode
 */
function creeElementPourEleve($loginEleve, $id_groupe, $texteElem, $annee, $periode) {
	
	// on commence par créér l'élément de programme → ".$texteElem;
	saveNewElement($texteElem);
	
	// puis on récupère l'index ";
	$idNewLibelle = getElemProgByLibelle($texteElem)->fetch_object()->id;
	
	// puis on traite le prof ".$_SESSION['login'];
	saveJointureProfEP($_SESSION['login'], $idNewLibelle);
	
	// puis pour la matière
	$matiere = getMatiere($id_groupe);
	saveJointureMatiereEP($matiere, $idNewLibelle);
	
	// puis pour l'éleve ".$loginEleve; pour l'année ".$annee; et la période ".$periode;
	saveJointureEleveEP($loginEleve, $idNewLibelle, $annee, $periode, $id_groupe);
	
}

/**
 * Renvoir les différents MEFS du groupe
 * 
 * @global DB_connect $mysqli
 * @param type $id_groupe
 * @return type
 */
function getMef($id_groupe) {	
	global $mysqli;
	$sql = "SELECT DISTINCT mef_code FROM eleves AS e "
		. "INNER JOIN j_eleves_groupes AS jeg "
		. "ON e.login = jeg.login "
		. "WHERE jeg.id_groupe = '".$id_groupe."' ";
	
	//echo $sql."<br />";
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}


	
