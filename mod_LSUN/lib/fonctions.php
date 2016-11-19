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
	$sql = "SELECT login, nom, prenom FROM utilisateurs WHERE statut LIKE '".$statut."' ORDER BY nom, prenom";
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

/**
 * Récupère responsable du livret
 * 
 * vaudrait mieux remonter le compte scolarité ?
 * 
 * @global type $mysqli
 * @return type
 */
function getResponsables() {
	global $mysqli;
	// les responsables sont les "suivi_par" de la table classe
	$sql = "SELECT DISTINCT suivi_par, lr.id FROM classes AS c INNER JOIN lsun_responsables as lr ON c.suivi_par = lr.login  ORDER BY suivi_par ";
	// 
	//echo $sql;
	$resultchargeDB = $mysqli->query($sql);	
	return $resultchargeDB;		
}

function MetAJourResp() {
	global $mysqli;
	$responsables = $mysqli->query("SELECT DISTINCT suivi_par FROM classes");
	while ($resp = $responsables->fetch_object()) {
		$sql = "INSERT INTO lsun_responsables (login) VALUES (\"$resp->suivi_par\") ON DUPLICATE KEY UPDATE login = \"$resp->suivi_par\" ";
		//echo $sql;
		$mysqli->query($sql);	
	}
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
	
	$sqlGetParcours = "SELECT * FROM lsun_parcours_communs WHERE ";
	if (isset($selectionClasse) && count($selectionClasse)){
		$myData = implode(",", $selectionClasse);
		$sqlGetParcours .= "classe IN ($myData) ";
	}	else {
		$sqlGetParcours .= " 1=1 ";
	}
	
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

function getElevesExport() {
	global $mysqli;
	$classes = $_SESSION['afficheClasse'];
	
	$myData = implode(",", $classes);
	$sqlEleves01 = "SELECT jec.* , e.nom , e.prenom, e.id_eleve, DATE_FORMAT(e.`date_entree`, '%Y-%m-%d') AS date_entree FROM j_eleves_classes AS jec "
		. "INNER JOIN eleves as e "
		. "ON e.login = jec.login "
		. "WHERE id_classe IN (".$myData.") ORDER BY jec.id_classe , jec.login , jec.periode ";
	// on récupère le prof principal
	$sqlEleves02 = "SELECT t0.*, jep.professeur FROM ($sqlEleves01) AS t0 "
		. "LEFT JOIN j_eleves_professeurs AS jep "
		. "ON t0.login = jep.login";
	// on récupère la date de conseil de classe
	$sqlEleves03 = "SELECT t1.* , DATE_FORMAT(p.date_fin, '%Y-%m-%d')  AS date_verrou FROM ($sqlEleves02) AS t1 "
		. "INNER JOIN periodes AS p "
		. "ON p.id_classe = t1.id_classe AND p.num_periode = t1.periode ";
	// on récupère le responsable
	
	//echo $sqlEleves03;
	
	$resultchargeDB = $mysqli->query($sqlEleves03);
	return $resultchargeDB;
	
}



// Bilan
function getAcquisEleve($eleve, $periode) {
	global $mysqli;
	// matieres_notes - matiere_element_programme - matieres_appreciations
	
	//$sqlAcquis = "SELECT * FROM matieres_notes WHERE login = '$eleve' AND periode = $periode ";
	$sqlAcquis01 = "SELECT mn.* , ma.appreciation FROM matieres_notes AS mn INNER JOIN matieres_appreciations AS ma "
		. "ON mn.login = ma.login AND mn.periode = ma.periode AND mn.id_groupe = ma.id_groupe "
		. "WHERE mn.login = '$eleve' AND mn.periode = $periode GROUP BY mn.`id_groupe` ";
	$sqlAcquis02 = "SELECT s1.*, jme.idEP FROM ($sqlAcquis01) AS s1 INNER JOIN  j_mep_eleve AS jme ON jme.idEleve = s1.login ";
	$sqlAcquis03 = "SELECT s2.* , mep.libelle FROM ($sqlAcquis02) AS s2 INNER JOIN matiere_element_programme AS mep ON s2.idEP = mep.id ";
	$sqlAcquis04 = "SELECT s3.* , jgm.id_matiere FROM ($sqlAcquis03) AS s3 INNER JOIN j_groupes_matieres AS jgm ON jgm.id_groupe = s3.id_groupe ";
	$sqlAcquis05 = "SELECT s4.* , ma.code_matiere FROM ($sqlAcquis04) AS s4 INNER JOIN matieres AS ma ON s4.id_matiere = ma.matiere ";
	// classe
	$sqlAcquis06 = "SELECT s5.* , jec.id_classe FROM ($sqlAcquis05) AS s5 INNER JOIN j_eleves_classes AS jec ON s5.login = jec.login AND s5.periode = jec.periode ";
	//=== mef
	$sqlAcquis = "SELECT s6.* , c.mef_code FROM ($sqlAcquis06) AS s6 INNER JOIN classes AS c ON s6.id_classe = c.id ";
	
	//echo $sqlAcquis;
	$resultchargeDB = $mysqli->query($sqlAcquis);
	return $resultchargeDB;
	
}

function getModalite($groupe, $eleve, $mef_code, $code_matiere ) {
	global $mysqli;
	$retour = "S";
	// On recherche la modalite du groupe
	$sqlMefGroupe = "SELECT * FROM mef_matieres WHERE mef_code = '$mef_code' AND code_matiere = '$code_matiere' ";
	//echo $sqlMefGroupe;
	$modaliteGroupe = $mysqli->query($sqlMefGroupe);
	if ($modaliteGroupe->num_rows == 1) {
		//echo "coucou ".$sqlMefGroupe;
		$retour = $modaliteGroupe->fetch_object()->code_modalite_elect;
		//echo $retour;
	} else {
		// Si plusieurs ou pas, on recherche la modalite de l'élève
		$sqlModalite = "SELECT code_modalite_elect FROM j_groupes_eleves_modalites WHERE id_groupe = '$groupe' AND login = '$eleve' ";
		$retourQuery = $mysqli->query($sqlModalite);
		if ($retourQuery->num_rows == 1) {
			$retour = $retourQuery->fetch_object()->code_modalite_elect;
		} else if ($retourQuery->num_rows > 1) {
			echo "plusieurs modalités pour la matière du groupe $groupe pour l'élève $eleve";
			$retour = "";
		}	else {
			echo "pas de modalité pour la matière du groupe $groupe pour l'élève $eleve";
			$retour = "";
		}
	}
	return $retour;
	
}

function getProfGroupe ($groupe) {
	global $mysqli;
	$sqlProf = "SELECT gp.* , SUBSTR(u.numind,2) AS numind FROM j_groupes_professeurs AS gp INNER JOIN utilisateurs u ON gp.login = u.login WHERE id_groupe = \"$groupe\" ";
	// echo $sqlProf;
	$resultchargeDB = $mysqli->query($sqlProf);
	return $resultchargeDB;
}

function getEPeleve ($idEleve, $idGroupe, $periode) {
	global $mysqli;
	$sqlEPeleve = "SELECT * FROM j_mep_eleve WHERE idEleve = \"$idEleve\" AND idGroupe = \"$idGroupe\" AND periode = \"$periode\" ";
	$resultchargeDB = $mysqli->query($sqlEPeleve);
	return $resultchargeDB;
}

function getMoyenne($id_groupe) {
	global $mysqli;
	$sqlMoyenne = "SELECT ROUND(AVG(`note`), 2) AS moyenne FROM `matieres_notes` WHERE `id_groupe` = $id_groupe GROUP BY `id_groupe` ";
	$resultchargeDB = $mysqli->query($sqlMoyenne)->fetch_object()->moyenne;
	return $resultchargeDB;
	
}

function getStatutNote($login,$id_groupe,$periode) {
	global $mysqli;
	$sqlStatutNote = "SELECT * FROM `matieres_notes` WHERE `id_groupe` = $id_groupe AND login = '$login' AND periode = $periode ";
	$resultchargeDB =  $mysqli->query($sqlStatutNote)->fetch_object()->statut;
	if ($resultchargeDB !='' ) {
		return $resultchargeDB;
	}
	return FALSE;
}



