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
	$newParcoursTexte = $mysqli->real_escape_string($newParcoursTexte);
	if ($newParcoursId) {
		$newParcoursId = "'".$newParcoursId."'";
	}	else {
		$newParcoursId = 'NULL';
	}
	$sqlNewParcours = "INSERT INTO lsun_parcours_communs (id,periode,classe,codeParcours,description)  VALUES ($newParcoursId, $newParcoursTrim, $newParcoursClasse, '$newParcoursCode', '$newParcoursTexte') ON DUPLICATE KEY UPDATE periode = $newParcoursTrim ,classe = $newParcoursClasse,codeParcours = '$newParcoursCode',description = '$newParcoursTexte' ";
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
	$sqlDelParcoursAid = "DELETE FROM lsun_j_aid_parcours WHERE id_parcours = $deleteParcours ";
	$res=$mysqli->query($sqlDelParcoursAid);
	return $res;
}

/**
 * Modification d'un parcours
 * 
 * @global type $mysqli
 * @param type $modifieParcoursId
 * @param type $modifieParcoursCode
 * @param type $modifieParcoursTexte
 * @param type $modifieParcoursLien
 */
function modifieParcours($modifieParcoursId, $modifieParcoursCode, $modifieParcoursTexte, $modifieParcoursLien, $idLien = NULL) {
	global $mysqli;
	
	//$newParcoursTexte = $mysqli->real_escape_string($newParcoursTexte);
	$modifieParcoursTexte = $mysqli->real_escape_string($modifieParcoursTexte);
	
	$sqlModifieParcours = "UPDATE lsun_parcours_communs "
		. "SET codeParcours = '$modifieParcoursCode', description = '$modifieParcoursTexte' "
		. "WHERE id = '$modifieParcoursId' ";
	//echo $sqlModifieParcours.";<br />";
	$mysqli->query($sqlModifieParcours);
	
	$sqlModifieParcoursLien = "INSERT INTO lsun_j_aid_parcours (id_aid, id_parcours) VALUES ($modifieParcoursLien , $modifieParcoursId) "
		. "ON DUPLICATE KEY UPDATE id_aid = $modifieParcoursLien ";
	//echo $sqlModifieParcoursLien;
	$res=$mysqli->query($sqlModifieParcoursLien);
	return $res;
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
	//echo $sqlMatieres.";<br />";
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
	//echo $sqlGetMatiereOnMatiere.";<br />";
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
	//echo $sqlAppGroupe."<br>";
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
	$sqlEleves01 = "SELECT jec.* , e.nom , e.prenom, e.id_eleve, e.ele_id, DATE_FORMAT(e.`date_entree`, '%Y-%m-%d') AS date_entree FROM j_eleves_classes AS jec "
		. "INNER JOIN eleves as e "
		. "ON e.login = jec.login "
		. "WHERE id_classe IN (".$myData.") ORDER BY jec.id_classe , jec.login , jec.periode ";
	//echo "<p>".$sqlEleves01.";</p>";
	// on récupère le prof principal
	$sqlEleves02 = "SELECT t0.*, jep.professeur FROM ($sqlEleves01) AS t0 "
		. "LEFT JOIN j_eleves_professeurs AS jep "
		. "ON t0.login = jep.login AND jep.id_classe=t0.id_classe";
	//echo "<p>".$sqlEleves02.";</p>";
	// on récupère la date de conseil de classe
	$sqlEleves03 = "SELECT t1.* , DATE_FORMAT(p.date_fin, '%Y-%m-%d') AS date_verrou , DATE_FORMAT(p.date_conseil_classe, '%Y-%m-%d') AS date_conseil FROM ($sqlEleves02) AS t1 "
		. "INNER JOIN periodes AS p "
		. "ON p.id_classe = t1.id_classe AND p.num_periode = t1.periode ";
	//echo "<p>".$sqlEleves03.";</p>";
	// on récupère le responsable
	$sqlEleves04 = "SELECT s3.* , c.mef_code , c.suivi_par FROM ($sqlEleves03) AS s3 INNER JOIN classes AS c ON s3.id_classe = c.id ";
	//echo "<p>".$sqlEleves04.";</p>";

	$sqlEleves = "SELECT s4.* , lr.id AS id_resp_etab FROM ($sqlEleves04) AS s4 INNER JOIN lsun_responsables AS lr ON lr.login = s4.suivi_par ";
	
	//echo $sqlEleves03;
	//echo "<p>".$sqlEleves.";</p>";
	
	$resultchargeDB = $mysqli->query($sqlEleves);
	return $resultchargeDB;
	
}


function getElevesExportCycle() {
	global $mysqli;
	$classes = $_SESSION['afficheClasse'];
	
	$myData = implode(",", $classes);

	$sql="SELECT DISTINCT e.login, 
				e.nom, 
				e.prenom, 
				e.id_eleve, 
				e.ele_id, 
				e.mef_code, 
				e.no_gep, 
				DATE_FORMAT(e.`date_entree`, '%Y-%m-%d') AS date_entree 
			FROM eleves e 
			WHERE e.login IN (SELECT login FROM j_eleves_classes WHERE id_classe IN (".$myData."));";

	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
	
}


// Bilan
function getAcquisEleve($eleve, $periode) {
	global $mysqli;
	// matieres_notes - matiere_element_programme - matieres_appreciations
	
	//$sqlAcquis = "SELECT * FROM matieres_notes WHERE login = '$eleve' AND periode = $periode ";
	$sqlAcquis01 = "SELECT mn.* , ma.appreciation FROM matieres_notes AS mn INNER JOIN matieres_appreciations AS ma "
		. "ON mn.login = ma.login AND mn.periode = ma.periode AND mn.id_groupe = ma.id_groupe "
		. "WHERE mn.login = '$eleve' AND mn.periode = $periode "
		. "AND mn.`id_groupe` NOT IN (SELECT jgt.id_groupe FROM j_groupes_types AS jgt) "
		. "GROUP BY mn.`id_groupe` ";
	//$sqlAcquis02 = "SELECT s1.*, jme.idEP FROM ($sqlAcquis01) AS s1 INNER JOIN  j_mep_eleve AS jme ON jme.idEleve = s1.login ";
	//$sqlAcquis03 = "SELECT s2.* , mep.libelle FROM ($sqlAcquis02) AS s2 INNER JOIN matiere_element_programme AS mep ON s2.idEP = mep.id ";
	$sqlAcquis04 = "SELECT s3.* , jgm.id_matiere FROM ($sqlAcquis01) AS s3 INNER JOIN j_groupes_matieres AS jgm ON jgm.id_groupe = s3.id_groupe ";
	$sqlAcquis05 = "SELECT s4.* , ma.code_matiere FROM ($sqlAcquis04) AS s4 INNER JOIN matieres AS ma ON s4.id_matiere = ma.matiere ";
	// classe
	$sqlAcquis06 = "SELECT s5.* , jec.id_classe FROM ($sqlAcquis05) AS s5 INNER JOIN j_eleves_classes AS jec ON s5.login = jec.login AND s5.periode = jec.periode ";
	//=== mef
	$sqlAcquis = "SELECT s6.* , c.mef_code FROM ($sqlAcquis06) AS s6 INNER JOIN classes AS c ON s6.id_classe = c.id ";
	
	//echo $sqlAcquis.'<br><br>';
	$resultchargeDB = $mysqli->query($sqlAcquis);
	return $resultchargeDB;
	
}

function getModalite($groupe, $eleve, $mef_code, $code_matiere ) {
	global $mysqli;
	global $msgErreur;
	$retour = "S";
	// On recherche la modalite du groupe
	$sqlMefGroupe = "SELECT * FROM mef_matieres WHERE mef_code = '$mef_code' AND code_matiere = '$code_matiere' ";
	//echo $sqlMefGroupe;
	$modaliteGroupe = $mysqli->query($sqlMefGroupe);
	if ($modaliteGroupe->num_rows == 1) {
		$retour = $modaliteGroupe->fetch_object()->code_modalite_elect;
		//echo $retour;
	} else {
		// Si plusieurs ou pas, on recherche la modalite de l'élève
		$sqlModalite = "SELECT code_modalite_elect FROM j_groupes_eleves_modalites WHERE id_groupe = '$groupe' AND login = '$eleve' ";
		$retourQuery = $mysqli->query($sqlModalite);
		if ($retourQuery->num_rows == 1) {
			$retour = $retourQuery->fetch_object()->code_modalite_elect;
		} else if ($retourQuery->num_rows > 1) {
			$msgErreur .= "plusieurs modalités pour la matière du groupe $groupe (".getMatiereGroupe($groupe).") pour l'élève ".get_nom_prenom_eleve($eleve)."<br>";
			$retour = "";
		}	else {
			$msgErreur .= "pas de modalité pour la matière du groupe $groupe (".getMatiereGroupe($groupe).") pour l'élève ".get_nom_prenom_eleve($eleve)." <em><a href=\"../../groupes/edit_eleves.php?id_groupe=$groupe\" target='_BLANK' title='Ne corriger que cet élève' >Corriger</a> ou <a href='../../gestion/gerer_modalites_election_enseignements.php'  target='_BLANK' title='Forcer la même modalité pour tous les élèves' >par lot</a></em> <br>";
			$msgErreur .= $sqlMefGroupe.'<br>';
			$msgErreur .= $sqlModalite.'<br>';
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
	//$sqlEPeleve = "SELECT * FROM j_mep_eleve WHERE idEleve = \"$idEleve\" AND idGroupe = \"$idGroupe\" AND periode = \"$periode\" ";
	$sqlEPeleve = "SELECT * FROM j_mep_eleve AS jmp INNER JOIN matiere_element_programme AS mep ON mep.id = jmp.idEP "
		. "WHERE jmp.idEleve = \"$idEleve\" AND jmp.idGroupe = \"$idGroupe\" AND jmp.periode = \"$periode\" ; ";
	// il faut vérifier que les éléments de programme existent
	//echo $sqlEPeleve;
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

//function getStatutSansApp($login,$id_groupe,$periode) {
function getStatutSansApp($login,$periode) {
	global $mysqli;
	$sqlStatutSSApp = "SELECT mn.* FROM `matieres_notes` AS mn "
		. "WHERE mn.login = '$login' "
		. "AND mn.periode = $periode "
		. "AND NOT EXISTS ("
		. "SELECT ma.* FROM `matieres_appreciations` AS ma "
		. "WHERE ma.login = mn.login "
		. "AND ma.id_groupe = mn.id_groupe "
		. "AND ma.periode = mn.periode "
		. ") "
		. "AND mn.statut NOT LIKE '' ";
	
	
	$sqlAcquis04 = "SELECT s3.* , jgm.id_matiere FROM ($sqlStatutSSApp) AS s3 INNER JOIN j_groupes_matieres AS jgm ON jgm.id_groupe = s3.id_groupe ";
	$sqlAcquis05 = "SELECT s4.* , ma.code_matiere FROM ($sqlAcquis04) AS s4 INNER JOIN matieres AS ma ON s4.id_matiere = ma.matiere ";
	// classe
	$sqlAcquis06 = "SELECT s5.* , jec.id_classe FROM ($sqlAcquis05) AS s5 INNER JOIN j_eleves_classes AS jec ON s5.login = jec.login AND s5.periode = jec.periode ";
	//=== mef
	$sqlStatutSSApp = "SELECT s6.* , c.mef_code FROM ($sqlAcquis06) AS s6 INNER JOIN classes AS c ON s6.id_classe = c.id ";
	
	
	$resultchargeDB = $mysqli->query($sqlStatutSSApp);
	/*
	if ($resultchargeDB->num_rows) {
		echo $sqlStatutSSApp."<br>";
	}
	 * 
	 */
	return $resultchargeDB;
}

function getAppConseil($eleve , $periode) {
	global $mysqli;
	$sqlGetAppConseil = "SELECT * FROM avis_conseil_classe WHERE login = '$eleve' AND periode = $periode ";
	//echo $sqlGetAppConseil;
	$resultchargeDB = $mysqli->query($sqlGetAppConseil);
	return $resultchargeDB;
}

function getAbsencesEleve($eleve_login , $periode_num) {
	global $mysqli;	
	
	$current_eleve= array();
	//On vérifie si le module est activé
	if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
		$sql="SELECT * FROM absences WHERE (login='".$eleve_login."' AND periode='$periode_num');";
		$current_eleve_absences_query = $mysqli->query($sql);
		if($current_eleve_absences_query->num_rows==0) {
			$current_eleve['absences'] = "0";
			$current_eleve['nj'] = "0";
			$current_eleve['retards'] = "0";
			$current_eleve['appreciation'] = "-";
		}
		else {
			$current_eleve_absences_objet = $current_eleve_absences_query->fetch_object();
			$current_eleve['absences'] = $current_eleve_absences_objet->nb_absences ? $current_eleve_absences_objet->nb_absences : 0;
			$current_eleve['nj'] = $current_eleve_absences_objet->non_justifie ? $current_eleve_absences_objet->nb_absences : 0;
			$current_eleve['retards'] = $current_eleve_absences_objet->nb_retards ? $current_eleve_absences_objet->nb_retards : 0;
			$current_eleve['appreciation'] = $current_eleve_absences_objet->appreciation;
		}
	} else {
		$eleve = EleveQuery::create()->findOneByLogin($eleve_login);
		if ($eleve != null) {
			$current_eleve['absences'] = strval($eleve->getDemiJourneesAbsenceParPeriode($periode_num)->count());
			$current_eleve['nj'] = strval($eleve->getDemiJourneesNonJustifieesAbsenceParPeriode($periode_num)->count());
			$current_eleve['retards'] = strval($eleve->getRetardsParPeriode($periode_num)->count());
			$sql2="SELECT * FROM absences WHERE (login='".$eleve_login."' AND periode='$periode_num');";
			//echo "$sql< br />";
			$current_eleve_absences_query = $mysqli->query($sql2);
			$current_eleve_appreciation_absences_objet = $current_eleve_absences_query->fetch_object();
			$current_eleve['appreciation'] = $current_eleve_appreciation_absences_objet ? $current_eleve_appreciation_absences_objet->appreciation : '';
		}
	}
	return $current_eleve;
}

function display_xml_error($error) {
    switch ($error->code) {
        case 1871:
            $return = "Erreur XML ".$error->code." → des données ne sont pas reconnues ou trouvées";
            $return .= "<br />".$error->message;
            $return .= "<br />Vous devez chercher cette erreur et la supprimer manuellement dans le fichier .xml";
            break;
        default :	
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $return = "Attention ".$error->code." : ";
                    $return .= "<br />".$error->message;
                    break;
                 case LIBXML_ERR_ERROR:
                    $return = "Erreur ".$error->code." : ";
                     break;
                case LIBXML_ERR_FATAL:
                    $return = "Erreur Fatale ".$error->code." : ";
                    break;
            }
            $return .= trim($error->message);
    }
    return $return."<hr />";
}

function getMatiereGroupe($groupe) {
	global $mysqli;
	$retour = "";
	$sqlMatiere = "SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe = '$groupe' ";
	$resultchargeDB = $mysqli->query($sqlMatiere);
	
	if ($resultchargeDB->num_rows) {
		$retour = $resultchargeDB->fetch_object()->id_matiere;
	}
	return $retour;
}

function dateScolarite($login, $periode) {
	
	// date début de période ou date changement de classe ou date d'entrée dans l'établissement
	$classe = EleveQuery::create()->findOneByLogin($login)->getClasse($periode)->getId();
	$dateDebutPeriode = getDateDebutPeriode($periode, $classe);
	
	// date entrée dans l'établissement
	$dateEntree = getEntreeEtablissement($login);
	
	//date changement de classe
	
	$dateDebutRetenue = max($dateEntree,$dateDebutPeriode);
	//echo $dateEntree." * ".$dateDebutRetenue." * ".$dateDebutRetenue." - <br>";
	return $dateDebutRetenue;
}

function getDateDebutPeriode($periode, $id_classe) {
	global $mysqli;
	$sqlDebutPeriode = "SELECT 1, numero_periode, jourdebut_calendrier "
		. "FROM edt_calendrier AS ec2 "
		. "WHERE ec2.numero_periode = $periode "
		. "AND FIND_IN_SET($id_classe, replace(ec2.classe_concerne_calendrier, ';', ',')) > 0;";
	//echo $sqlDebutPeriode.'<br><br>';
	$resultchargeDB = $mysqli->query($sqlDebutPeriode);
	return $resultchargeDB->fetch_object()->jourdebut_calendrier ;
}

function getEntreeEtablissement($login) {
	global $mysqli;
	$sqlEntree = "SELECT DATE_FORMAT(date_entree,'%Y-%m-%d') AS  date_entree FROM eleves WHERE login = '$login' ";
	$resultchargeDB = $mysqli->query($sqlEntree);
	return $resultchargeDB->fetch_object()->date_entree ;
	
}

function getResponsableEleve($ele_id) {
	global $mysqli;
	$sqlGetResp = "SELECT t1.*, ra.adr1, ra.adr2, ra.adr3, ra.adr4, ra.cp, ra.pays, ra.commune FROM "
		. "(SELECT t0.*, rp.nom , rp.prenom, IF (rp.civilite='Mme','MME','M') AS civilite , rp.adr_id FROM "
		. "(SELECT resp_legal, pers_id, ele_id, pers_contact FROM responsables2 WHERE ele_id = $ele_id AND resp_legal IN (1 , 2) ) AS t0 "
		. "INNER JOIN resp_pers AS rp "
		. "ON rp.pers_id = t0.pers_id ) AS t1 "
		. "INNER JOIN resp_adr AS ra "
		. "ON t1.adr_id = ra.adr_id ";
	//echo $sqlGetResp.'<br>';
	$resultchargeDB = $mysqli->query($sqlGetResp);
	return $resultchargeDB ;
}

function getResumeAid($aid_id) {
	global $mysqli;
	$sqlResumeAid = "SELECT resume FROM aid WHERE id = $aid_id ";
	$resultchargeDB = $mysqli->query($sqlResumeAid)->fetch_object()->resume;
	return $resultchargeDB ;
	
	
}

function getAidEleve($login, $typeAid, $periode = NULL) {
	global $mysqli;
	//indice_aid → la catégorie de l'AID
	//id_aid → l'identifiant de l'AID
	$sqlGetAidEleve01 = "SELECT  login , indice_aid , id_aid FROM j_aid_eleves WHERE login = '$login' ";
	
	$sqlGetAidEleve02 = "SELECT t0.*, ac.type_aid FROM ($sqlGetAidEleve01) AS t0 "
		. "INNER JOIN aid_config AS ac "
		. "ON ac.indice_aid = t0.indice_aid "
		. "WHERE ac.type_aid = $typeAid ";
	if($periode) {
		$sqlGetAidEleve02 .= " AND ac.display_begin <= $periode AND ac.display_end >= $periode ";
	}
	
	// echo $sqlGetAidEleve02."<br>";
	$resultchargeDB = $mysqli->query($sqlGetAidEleve02);
	return $resultchargeDB ;
	
}

function getModaliteGroupe($groupe_id) {
	global $mysqli;
	// La modalité est dans la matiere de la classe
	
	$sqlGroupeModalite = "SELECT t4.* , u.numind FROM (
	SELECT DISTINCT t3.* , m.code_matiere FROM ( 	
		SELECT t1.id_aid , t1.indice_aid , t1.login , t1.matiere AS matiere , t2.modalite FROM (
			SELECT t00.* , ljee.id_epi FROM (
				SELECT t0.*, jpm.id_matiere AS matiere FROM ( 
					SELECT jgp.id_aid , jgp.indice_aid , jgp.id_utilisateur AS login FROM `j_aid_utilisateurs` AS jgp 
					WHERE jgp.`id_aid` = $groupe_id
				) AS t0 
				LEFT JOIN j_professeurs_matieres AS jpm 
				ON jpm.id_professeur = t0.login
			) AS t00 
			INNER JOIN `lsun_j_epi_enseignements` AS ljee
			ON ljee.id_enseignements = t00.indice_aid
		) AS t1 
		INNER JOIN lsun_j_epi_matieres AS t2 
		ON t1.matiere = t2.id_matiere AND t1.id_epi = t2.id_epi
	) AS t3 
	INNER JOIN matieres AS m 
	ON t3.matiere = m.matiere 
) AS t4
INNER JOIN utilisateurs AS u 
ON t4.login = u.login
";
	
	//echo $sqlGroupeModalite.'<br>';
	$resultchargeDB = $mysqli->query($sqlGroupeModalite);
	return $resultchargeDB ;
	
}


function getModaliteGroupeAP($groupe_id) {
	global $mysqli;
	// La modalité est dans la matiere de la classe
	
	$sqlGroupeModaliteProfs = "
		SELECT t0.*, jpm.id_matiere AS matiere FROM (
			SELECT jgp.id_aid , jgp.indice_aid , jgp.id_utilisateur AS login FROM 
				`j_aid_utilisateurs` AS jgp 
			WHERE jgp.`id_aid` = $groupe_id
		) AS t0
		LEFT JOIN
			j_professeurs_matieres AS jpm
		ON jpm.id_professeur = t0.login";
	//echo $sqlGroupeModaliteProfs.";<br /><br />";
	
	$sqlGroupeModaliteClasses = "
	SELECT DISTINCT t4.* , jgp.login FROM (
		SELECT DISTINCT t3.*, jgem.code_modalite_elect AS modalite FROM (
			SELECT t2.* , jgm.id_matiere FROM (
				SELECT t1.id_classe , jgc.id_groupe FROM (
					SELECT DISTINCT jec.id_classe FROM (
						SELECT DISTINCT jeg.* FROM `j_aid_eleves` AS jeg 
						WHERE jeg.`id_aid` = $groupe_id
					) AS t0
					INNER JOIN
						j_eleves_classes AS jec
					ON t0.login = jec.login
				) AS t1
				INNER JOIN
				j_groupes_classes AS jgc
				ON jgc.id_classe = t1.id_classe
				WHERE jgc.id_groupe NOT IN (SELECT jgt.id_groupe FROM j_groupes_types AS jgt) 
			) AS t2
			INNER JOIN
				j_groupes_matieres AS jgm
			ON jgm.id_groupe = t2.id_groupe
		) AS t3
		INNER JOIN 
			j_groupes_eleves_modalites AS jgem
		ON jgem.id_groupe = t3.id_groupe
	) AS t4
	INNER JOIN
		j_groupes_professeurs AS jgp
	ON jgp.id_groupe = t4.id_groupe ";
	
	//echo $sqlGroupeModaliteClasses.';<br><br>';
	
	$sqlGroupeModalite01 = "
SELECT DISTINCT t6.* FROM (
	$sqlGroupeModaliteProfs
	) AS t5
INNER JOIN 
	(
	$sqlGroupeModaliteClasses
	) AS t6
ON t6.login = t5.login AND t6.id_matiere = t5.matiere
	";
	
	//echo $sqlGroupeModalite01.';<br><br>';
	
	//SELECT DISTINCT t7.* , m.code_matiere FROM
	$sqlGroupeModalite = ""
		. "SELECT DISTINCT t7.id_matiere , t7.modalite , t7.login , m.code_matiere FROM "
		. "	($sqlGroupeModalite01) AS t7 "
		. "INNER JOIN "
		. "matieres AS m "
		. "ON m.matiere = t7.id_matiere";
	
	//echo $sqlGroupeModalite.';<br><br>';
	$resultchargeDB = $mysqli->query($sqlGroupeModalite);
	return $resultchargeDB ;
	
}

function getAidParcours($typeAid = 3) {
	global $mysqli;
	$sqlAidParcours = "SELECT t0.* , aid.nom AS aid ,aid.id AS idAid FROM (
	SELECT indice_aid , nom , nom_complet , type_aid FROM aid_config WHERE type_aid = $typeAid
) AS t0
INNER JOIN
	aid
ON aid.indice_aid = t0.indice_aid
ORDER BY aid";
	
	// echo $sqlAidParcours.'<br><br>';
	$resultchargeDB = $mysqli->query($sqlAidParcours);
	return $resultchargeDB ;
	
}

function getLiaisonsAidParcours($idAid = NULL, $parcoursCommun = NULL) {
	global $mysqli;
	$sqlAidParcours = "SELECT * FROM lsun_j_aid_parcours ";
	if ($idAid) {
		$sqlAidParcours .= "WHERE id_aid = $idAid ";
		if ($parcoursCommun) {
			$sqlAidParcours .= "AND id_parcours = $parcoursCommun ";
		}
	}
	//echo $idAid." ".$sqlAidParcours;
	$resultchargeDB = $mysqli->query($sqlAidParcours);
	return $resultchargeDB ;
	
}

function getCodeParcours($id_aid ) {
	global $mysqli;
	$sqlParcours = "SELECT DISTINCT jap.id_parcours, pc.codeParcours, pc.periode FROM lsun_j_aid_parcours AS jap "
		. "INNER JOIN "
		. "lsun_parcours_communs AS pc "
		. "ON jap.id_parcours = pc.id "
		. "WHERE jap.id_aid = $id_aid  ";
	//echo $sqlParcours.'<br><br>';
	$resultchargeDB = $mysqli->query($sqlParcours);

	return $resultchargeDB ;
	
}

function getCommentaireAidElv($login, $id_aid, $periode) {
	global $mysqli;
	$sqlComAidEpi = "SELECT appreciation FROM aid_appreciations WHERE login = '$login' AND id_aid = '$id_aid' AND periode = '$periode' ";
	
	//echo $sqlComAidEpi."<br>";
	$resultchargeDB = $mysqli->query($sqlComAidEpi);
	return $resultchargeDB ;
	
	
}	


function assureDisciplinePresente($refDisciplines) {
	global $xml;
	//echo "recherché ".$refDisciplines.'<br>';
	$listediscipline = $xml->getElementsByTagName('discipline');

	foreach($listediscipline as $discipline) {
		$trouve = FALSE;
		//echo $discipline->getAttribute("id")." → ";
		if ($discipline->getAttribute("id") == $refDisciplines ) {
			$trouve = TRUE;
			break;
		}
	}
	
	if (!$trouve) {
		//echo $refDisciplines." n'a pas été trouvé, il faut le créer<br>";
		$disciplines = $xml->getElementsByTagName("disciplines")->item(0);
		
		$noeudDiscipline = $xml->createElement('discipline');
		$code = substr($refDisciplines, 3, -1);
		$modalite = substr($refDisciplines, -1);
		$libelle = getMatiereSurMEF($code)->fetch_object()->nom_complet;
		$attributsDiscipline = array('id'=>$refDisciplines,'code'=>$code, 'modalite-election'=>$modalite,'libelle'=>htmlspecialchars($libelle));
		foreach ($attributsDiscipline as $cle=>$valeur) {
			$attDiscipline = $xml->createAttribute($cle);
			$attDiscipline->value = $valeur;
			$noeudDiscipline->appendChild($attDiscipline);
		}
		$disciplines->appendChild($noeudDiscipline);
			
	}

}

function getCommentaireEleveParcours($login, $id_aid, $periode) {
	global $mysqli;
	
	$sql = "SELECT * FROM aid_appreciations WHERE login = '$login' AND id_aid ='$id_aid' AND periode ='$periode' ";
	
	//echo $sql.'<br>';
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB ;
	
}

function libxml_display_error($error) {
	$return = "<br/>\n";
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
		$return .= "<b>Warning $error->code</b>: ";
		break;
		case LIBXML_ERR_ERROR:
		$return .= "<b>Error $error->code</b>: ";
		break;
		case LIBXML_ERR_FATAL:
		$return .= "<b>Fatal Error $error->code</b>: ";
		break;
	}
	$return .= trim($error->message);
	if ($error->file) {
		$return .= " in <b>$error->file</b>";
	}
	$return .= " on line <b>$error->line</b>\n";

	return $return;
}

function libxml_display_errors($display_errors = true) {
	$errors = libxml_get_errors();
	$chain_errors = "";

	foreach ($errors as $error) {
		$chain_errors .= preg_replace('/( in\ \/(.*))/', "", strip_tags(libxml_display_error($error)))."<br>\n";
		if ($display_errors) {
			trigger_error(libxml_display_error($error), E_USER_WARNING);
		}
	}
	libxml_clear_errors();

	return $chain_errors;
}





