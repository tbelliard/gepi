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


/**
 * Enregistre un EPI
 * 
 * @global type $mysqli
 * @param type $newEpiPeriode
 * @param type $newEpiClasse
 * @param type $newEpiCode
 * @param type $newEpiIntitule
 * @param type $newEpiDescription
 * @param type $newEpiMatiere
 * @param type $modifieEPIMatiereModalite
 * @param type $idEpi
 */
function sauveEPI($newEpiPeriode, $newEpiClasse, $newEpiCode, $newEpiIntitule, $newEpiDescription, $newEpiMatiere, $idEpi = NULL) {
	global $mysqli;
	//echo $newEpiMatiere;
	if(is_array($newEpiMatiere)) {
		foreach ($newEpiMatiere as $matiereAid) {
			$sqlMatiereEPI = "UPDATE matieres SET matiere_aid = 'y' WHERE matiere = '".substr($matiereAid,0,-1)."' ";
			//echo $sqlMatiereEPI."<br>";
			$mysqli->query($sqlMatiereEPI);
		}
	}
	
	$sqlCreeEpi = "INSERT INTO lsun_epi_communs (id, periode, codeEPI, intituleEpi, descriptionEpi) VALUES (";
	
	if ($idEpi) {
		$sqlCreeEpi .= $idEpi;
		delMatiereEPI($idEpi);
		delLienEPI($idEpi);
		delClasseEPI($idEpi);
	}
	else {$sqlCreeEpi .= "NULL";}
	
	$sqlCreeEpi .= ", '$newEpiPeriode', '$newEpiCode', \"".htmlspecialchars($newEpiIntitule)."\", \"".htmlspecialchars($newEpiDescription)."\") "
		. "ON DUPLICATE KEY UPDATE periode = \"".$newEpiPeriode."\", codeEPI = \"".$newEpiCode."\", intituleEpi = \"".htmlspecialchars($newEpiIntitule)."\", descriptionEpi = \"".htmlspecialchars($newEpiDescription)."\" ";
	//echo $sqlCreeEpi.';<br>';
	$mysqli->query($sqlCreeEpi);
	$idEPI = getIdEPI($newEpiPeriode, $newEpiCode, $newEpiIntitule, htmlspecialchars($newEpiDescription))->fetch_object()->id;
	
	if ($newEpiMatiere) {
		foreach ($newEpiMatiere AS $valeur) {
			$matiere = substr($valeur, 0, -1);
			$modalite = substr($valeur, -1);
			$sqlCreLienEPI = "INSERT INTO lsun_j_epi_matieres (id_matiere,  modalite, id_epi) VALUES ('$matiere', '$modalite' , $idEPI) ON DUPLICATE KEY UPDATE id_matiere = '$matiere' , modalite = '$modalite' ";
			//echo $sqlCreLienEPI.";<br>";
			$mysqli->query($sqlCreLienEPI);
		}
	}
	
	if ($newEpiClasse) {
		foreach ($newEpiClasse AS $valeur) {
			$sqlCrejoinEpiClasse = "INSERT INTO lsun_j_epi_classes (id_epi, id_classe) VALUES ($idEPI , $valeur) ";
			//echo $sqlCrejoinEpiClasse.';<br>';
			$mysqli->query($sqlCrejoinEpiClasse);
		}
	}
}

/**
 * Réupère l'Id d'un EPI en fonction de ses caractéristiques
 * 
 * @global type $mysqli
 * @param type $newParcoursPeriode
 * @param type $newEpiClasse
 * @param type $newEpiCode
 * @param type $newEpiIntitule
 * @param type $newEpiDescription
 * @return type
 */
function getIdEPI($newParcoursPeriode, $newEpiCode, $newEpiIntitule, $newEpiDescription) {
	global $mysqli;
	$sqlGetIdEpi = "SELECT id FROM lsun_epi_communs WHERE "
		. "periode = '$newParcoursPeriode' AND "
		. "codeEPI = '$newEpiCode' AND "
		. "intituleEpi = \"$newEpiIntitule\" AND "
		. "descriptionEpi = \"$newEpiDescription\" ";
	// echo $sqlGetIdEpi;
	$resultchargeDB = $mysqli->query($sqlGetIdEpi);
	return $resultchargeDB;
}

/**
 * Retourne un EPI commun
 * 
 * @global type $mysqli
 * @global type $selectionClasse
 * @return type
 */
function getEPICommun() {
	global $mysqli;
	//global $selectionClasse;
	//$myData = implode(",", $selectionClasse);
	getEPIparClasse();
	$sqlGetEpi = "SELECT lec.* FROM lsun_epi_communs AS lec "
		. "ORDER BY periode , codeEPI , id ";
	//echo $sqlGetEpi;
	$resultchargeDB = $mysqli->query($sqlGetEpi);
	return $resultchargeDB;
}

function getEPIparClasse($classe = NULL) {
	global $mysqli;
	//global $selectionClasse;
	//$myData = implode(",", $selectionClasse);
	$classes = $classe ? $classe : (isset($_SESSION['afficheClasse']) ? $_SESSION['afficheClasse'] : array());
	
	$myData = implode(",", $classes);
	
	$sqlGetEpi = "SELECT lec.* , ljec.id_classe FROM lsun_epi_communs AS lec "
		. "INNER JOIN lsun_j_epi_classes AS ljec "
		. "ON ljec.id_epi = lec.id "
		. "WHERE ljec.id_classe IN ($myData) "
		. "ORDER BY lec.periode , lec.codeEPI , lec.id ";

	//echo $sqlGetEpi;
	$resultchargeDB = $mysqli->query($sqlGetEpi);
	return $resultchargeDB;
}

/**
 * Recherche les matières d'un EPI commun
 * 
 * @global type $mysqli
 * @param type $idEPI
 * @return type
 */
function getMatieresEPICommun($idEPI) {
	global $mysqli;
	$sqlGetMatieresEpi = "SELECT id_matiere, modalite FROM lsun_j_epi_matieres WHERE id_epi = '$idEPI' ORDER BY id_matiere;";
	//echo $sqlGetMatieresEpi;
	$resultchargeDB = $mysqli->query($sqlGetMatieresEpi);
	return $resultchargeDB;
}


/**
 * Supprime un EPI sur son Id
 * 
 * @global type $mysqli
 * @param type $EpiId
 */
function supprimeEPI($EpiId) {
	global $mysqli;
	delMatiereEPI($EpiId);
	delClasseEPI($EpiId);
	delLienEPI($EpiId);
	//delLsun_j_epi_enseignements($EpiId);
	//$sqlDelEpiEns = "DELETE FROM lsun_j_epi_enseignements WHERE id_epi = '$EpiId'; ";
	//$mysqli->query($sqlDelEpiEns);
	$sqlDeleteEpi = "DELETE FROM lsun_epi_communs WHERE id = '$EpiId' ";
	//echo $sqlDeleteEpi.';<br>';
	$mysqli->query($sqlDeleteEpi);
}

/**
 * Supprime les matières d'un EPI
 * 
 * @global type $mysqli
 * @param type $EpiId
 */
function delMatiereEPI($EpiId) {
	global $mysqli;
	$sqlDeleteJointureEpi = "DELETE FROM lsun_j_epi_matieres WHERE id_epi = '$EpiId' ";
	//echo $sqlDeleteJointureEpi.';<br>';
	$mysqli->query($sqlDeleteJointureEpi);
}

function getEpiAid() {
	global $mysqli;
	global $_EPI;
	//$in = implode(",",$_SESSION['afficheClasse']);
	//if ($in) {$in = ','.$in;}
	//$in = '0'.$in;
	
	$sqlAidClasse = "SELECT "
		. "indice_aid AS id_enseignement, indice_aid AS indice_aid, nom AS groupe , nom_complet AS description, NULL AS id_groupe,  NULL AS id_classe "
		. "FROM aid_config WHERE type_aid = $_EPI ";
	//echo '<br>'.$sqlAidClasse.'<br>';
		
	$resultchargeDB = $mysqli->query($sqlAidClasse);
	return $resultchargeDB;
}

function getEpiCours() {
	global $mysqli;
	global $_EPI;
	$in = implode(",",$_SESSION['afficheClasse']);
	if ($in) {$in = ','.$in;}
	$in = '0'.$in;
	
	$sqlAidClasse = "SELECT t2.* , c.classe "
		. "FROM ( SELECT t1.* , jcg.id_classe AS toutesClasses "
		. "FROM ("
		. "SELECT jgm.id_matiere , t0.* FROM "
		. "(SELECT jgt.id_groupe , jgc.id_classe FROM j_groupes_types AS jgt "
		. "INNER JOIN j_groupes_classes AS jgc ON jgc.id_groupe = jgt.id_groupe "
		. "WHERE jgt.id_type = $_EPI AND jgc.id_classe IN ($in)) AS t0 "
		. "INNER JOIN j_groupes_matieres AS jgm ON jgm.id_groupe = t0.id_groupe"
		. ""
		. ") AS t1 "
		. "INNER JOIN j_groupes_classes AS jcg ON jcg.id_groupe = t1.id_groupe ) "
		. "AS t2 "
		. "INNER JOIN classes AS c ON t2.toutesClasses = c.id";
	
	//echo '<br>--*--<br>'.$sqlAidClasse.'<br>--*--<br>';
	$resultchargeDB = $mysqli->query($sqlAidClasse);
	return $resultchargeDB;
}

function lieEpiCours($id_epi , $id_enseignement , $aid, $id=NULL) {
	global $mysqli;
	$sqLieEpiCours = "INSERT INTO lsun_j_epi_enseignements (id , id_epi , id_enseignements , aid) VALUES (";
	if ($id) {
		$sqLieEpiCours .= $id;
	}	else {
		$sqLieEpiCours .= "NULL";
	}
	$sqLieEpiCours .= ",$id_epi , $id_enseignement , $aid)";
	//echo $sqLieEpiCours;
	$mysqli->query($sqLieEpiCours);
}

function getLiaisonEpiEnseignementByIdEpi($id) {
	global $mysqli;
	$sqlGetLiaisonEpiEnseignement = "SELECT * FROM lsun_j_epi_enseignements WHERE id_epi = '$id' ";
	//echo $sqlGetLiaisonEpiEnseignement;
	$resultchargeDB = $mysqli->query($sqlGetLiaisonEpiEnseignement);
	return $resultchargeDB;
}



function delLienEPI($idEPI) {
	global $mysqli;
	$sqlDelLienEPI = "DELETE FROM lsun_j_epi_enseignements WHERE id_epi = '$idEPI' ";
	//echo $sqlDelLienEPI.";<br />";
	$mysqli->query($sqlDelLienEPI);
}

function getEpisGroupes($idEPI = NULL) {
	global $mysqli;
	
	$tableClasses = implode(",",$_SESSION['afficheClasse']);
	//if ($in) {$in = ','.$in;}
	//$in = '0'.$in;
	
	$sqlEpisGroupes= "SELECT a.* , e.id_epi FROM aid AS a "
		. "INNER JOIN  lsun_j_epi_enseignements AS e "
		. "ON a.indice_aid = e.id_enseignements ";
	if ($idEPI) {
		$sqlEpisGroupes .= "WHERE e.id_epi = $idEPI ";
	}	
	
	$sqlEpisGroupes = "SELECT DISTINCT t2.id , t2.nom , t2.indice_aid , t2.id_epi , t2.periode FROM (
	SELECT t1.* , jae.login FROM (
		SELECT t0.* , lec.periode FROM (
			$sqlEpisGroupes 
		) AS t0 
		INNER JOIN lsun_epi_communs AS lec 
		ON t0.id_epi = lec.id
	) AS t1
	INNER JOIN j_aid_eleves AS jae
	ON jae.id_aid = t1.id
) AS t2
INNER JOIN j_eleves_classes AS jec
ON jec.login = t2.login
WHERE jec.id_classe IN ($tableClasses) ";
	
	
	//echo $sqlEpisGroupes.";<br /><br />";
	$resultchargeDB = $mysqli->query($sqlEpisGroupes);
	return $resultchargeDB;
}

function estClasseEPI($id_epi , $id_classe) {
	global $mysqli;
	$retour = FALSE;
	$sqlEpisClasse = "SELECT 1=1 FROM lsun_j_epi_classes WHERE id_classe = '$id_classe' AND id_epi = '$id_epi' ";
	//echo $sqlEpisClasse;
	if ($mysqli->query($sqlEpisClasse)->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function estCoursEpi($id_epi , $id_cours) {
	global $mysqli;
	$retour = FALSE;
	$cours = explode('-', $id_cours);
	$sqlEpisCours= "SELECT 1=1 FROM lsun_j_epi_enseignements WHERE id_epi = '$id_epi' AND id_enseignements = '$cours[1]' ";	
	if ($mysqli->query($sqlEpisCours)->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function delClasseEPI($EpiId) {
	global $mysqli;
	$sqlDelClasseEPI = "DELETE FROM lsun_j_epi_classes WHERE id_epi = '$EpiId' ";
	//echo $sqlDelClasseEPI.";<br />";
	$mysqli->query($sqlDelClasseEPI);
	
}

function getAID($id) {
	global $mysqli;
	$sqlGetAid = "SELECT * FROM aid_config WHERE indice_aid = '$id' AND type_aid = '2' ";
	//echo $sqlGetAid;
	$retour = $mysqli->query($sqlGetAid)->fetch_object();
	return $retour;
}

function existeLienCours ($id_classe, $id_enseignements) {
	global $mysqli;
	$retour = FALSE;
	$sqlGetExisteLien = "SELECT 1=1 FROM lsun_j_epi_enseignements AS lsje "
		. " INNER JOIN lsun_epi_communs AS lec ON lec.id = lsje.id_epi "
		. "WHERE id_enseignements='$id_enseignements' AND aid='0' AND lec.classe ='$id_classe' ";
	//echo $sqlGetExisteLien;
	$resultchargeDB = $mysqli->query($sqlGetExisteLien);
	if ($resultchargeDB->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function existeLienAID($id_classe, $id_enseignements) {
	global $mysqli;
	$retour = FALSE;
	$sqlGetExisteLien = "SELECT 1=1 FROM lsun_j_epi_enseignements AS lsje "
		. " INNER JOIN lsun_epi_communs AS lec ON lec.id = lsje.id_epi "
		. " WHERE lsje.id_enseignements='$id_enseignements' AND aid='1' AND lec.classe ='$id_classe' ";
	//echo $sqlGetExisteLien;
	$resultchargeDB = $mysqli->query($sqlGetExisteLien);
	if ($resultchargeDB->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function verifieGroupeEPI($id_aid) {
	global $mysqli;
	$sql = "SELECT jea.*, a.id AS idAid, a.nom FROM lsun_j_epi_enseignements AS jea "
		. "INNER JOIN aid AS a "
		. "ON a.indice_aid = jea.id_enseignements "
		. " WHERE a.id = '$id_aid' ";
	//echo $sql."<br>";
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB->num_rows;
}
