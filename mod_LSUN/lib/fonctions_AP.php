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

function getAPCommun() {
	
	global $mysqli;
	getEPIparClasse();
	$sqlGetEpi = "SELECT t0.* , l.id_aid  FROM "
		. "(SELECT lac.* FROM lsun_ap_communs AS lac "
		. "ORDER BY intituleAP) AS t0 "
		. "INNER JOIN lsun_j_ap_aid as l "
		. "ON t0.id = l.id_ap";
	//echo $sqlGetEpi;
	$resultchargeDB = $mysqli->query($sqlGetEpi);
	return $resultchargeDB;
}

function getApAid() {
	global $mysqli;
	global $_AP;
	$afficheClasse = isset($_SESSION['afficheClasse']) ? $_SESSION['afficheClasse'] : array();
	$in = implode(",", $afficheClasse);
	if ($in) {$in = ','.$in;}
	$in = '0'.$in;
	
	$sqlAidAP = "SELECT indice_aid AS indice_aid, nom AS groupe , nom_complet AS description "
		. "FROM aid_config WHERE type_aid = $_AP ";
	//echo '<br>'.$sqlAidAP.';<br>';
		
	$resultchargeDB = $mysqli->query($sqlAidAP);
	return $resultchargeDB;
}

function saveAP($ApIntitule, $ApDisciplines, $ApDescription, $ApLiaisonAID, $id = NULL) {
	global $mysqli;
	global $msg_requetesAdmin;
	
	if (!$ApDescription) {
		$ApDescription = '';
	}
	
	$sqlCreeAP = "INSERT INTO lsun_ap_communs (id , intituleAP , descriptionAP) VALUES (";
	if ($id) {
		$sqlCreeAP .= $id;
	}	else {
		$sqlCreeAP .= "NULL";
	}
	$sqlCreeAP .= ", \"$ApIntitule\" , \"$ApDescription\") ON DUPLICATE KEY UPDATE intituleAP = \"$ApIntitule\" , descriptionAP = \"$ApDescription\" ";
	//echo '<br>'.$sqlCreeAP.';<br>';
	if($mysqli->query($sqlCreeAP)) {
		$msg_requetesAdmin.="<span style='color:green'>Paramètres AP enregistrés.</span><br />";

		//echo $id." → ";
		//On récupère l'id'
		if ($id == NULL) {
			$sqlGetId = "SELECT id FROM lsun_ap_communs WHERE intituleAP = \"$ApIntitule\" AND descriptionAP = \"$ApDescription\" ";
			//echo '<br>'.$sqlGetId.';<br>';
			$id = $mysqli->query($sqlGetId)->fetch_object()->id;
		}
		else {
			//On a un id, il faut supprimer les enregistrements de lsun_j_ap_matiere pour les recréer
			$delMatiereAp = "DELETE FROM lsun_j_ap_matiere WHERE id_ap = $id";
			//echo '<br>'.$delMatiereAp.';<br>';
			$mysqli->query($delMatiereAp);
		}
		//echo "AP: id=$id<br />";

		// $ApDisciplines est un tableau
		if(count($ApDisciplines)>0) {
			$msg_requetesAdmin.="<span style='color:green'>Enregistrement des disciplines associées à l'AP&nbsp;:</span> ";
		}
		foreach ($ApDisciplines as $discipline) {
			$code = substr($discipline,0,-1) ;
			//echo "discipline=$discipline, code=$code<br />";
			$matiere = getMatiereOnMatiere($code)->code_matiere;
			$modalite = substr($discipline,-1) ;
			//echo "modalite=$modalite<br />";
			//echo $id." ".$matiere." ".$modalite."<br>";

			if($matiere=="") {
				$msg_requetesAdmin.="<span style='color:red' title=\"Erreur : Une matière n'a pas été enregistrée pour l'AP n°$id faute de code_matiere valide.\">$matiere</span> ";
			}
			else {
				$sqlMatiereAp = "INSERT INTO lsun_j_ap_matiere (id_enseignements, modalite ,id_ap) VALUES (\"$matiere\",\"$modalite\",\"$id\") ON DUPLICATE KEY UPDATE id_enseignements = \"$matiere\" ";
				//echo '<br>'.$sqlMatiereAp.';<br>';
				if(!$mysqli->query($sqlMatiereAp)) {
					$msg_requetesAdmin.="<span style='color:red' title=\"ERREUR\">".$code."</span> ";
				}
				else {
					$msg_requetesAdmin.="<span style='color:green'>".$code."</span> ";
				}
			}
		}
		if(count($ApDisciplines)>0) {
			$msg_requetesAdmin.="<br />";
		}
		
		$sqlLiaisonApAid = "INSERT INTO lsun_j_ap_aid (id_aid, id_ap) VALUES (\"$ApLiaisonAID\",\"$id\") ON DUPLICATE KEY UPDATE id_aid = \"$ApLiaisonAID\" ";
		//echo '<br>'.$sqlLiaisonApAid.';<br>';
		if(!$mysqli->query($sqlLiaisonApAid)) {
			$msg_requetesAdmin.="<span style='color:red'>Erreur lors de l'enregistrement de la liaison AP/AID.</span><br />";
		}
	}
	else {
		$msg_requetesAdmin.="<span style='color:red'>Erreur lors de l'enrregistrement des paramètres de l'AP.</span><br />";
	}	
}

function getAp() {
	$resultchargeDB = getAPCommun();
	return $resultchargeDB;
}

function getAidConfig($id) {
	global $mysqli;
	$sqlAID = "SELECT * FROM aid_config WHERE indice_aid = $id";
	//echo '<br>'.$sqlAID.'<br>';
	
	$resultchargeDB = $mysqli->query($sqlAID);
	return $resultchargeDB;
}

function disciplineAP($id) {
	global $mysqli;
	$sqlAID = "SELECT * FROM lsun_j_ap_matiere WHERE id_ap = $id";
	//echo '<br>'.$sqlAID.'<br>';
	
	$resultchargeDB = $mysqli->query($sqlAID);
	return $resultchargeDB;
	
}

function getDisciplines($id_ap) {
	global $mysqli;
	$sqlMatAP = "SELECT * FROM lsun_j_ap_matiere WHERE id_ap = '$id_ap' ";
	$resultchargeDB = $mysqli->query($sqlMatAP);
	return $resultchargeDB;
}

function getApGroupes($idAP = NULL) {
	global $mysqli;
	
	$sqlApGroupes= "SELECT a.* , e.id_ap FROM aid AS a "
		. "INNER JOIN  lsun_j_ap_aid AS e "
		. "ON a.indice_aid = e.id_aid ";
	if ($idAP) {
		$sqlApGroupes .= "WHERE e.id_ap = $idAP ";
	}
	//echo '<br>'.$sqlApGroupes.'<br>';
	$resultchargeDB = $mysqli->query($sqlApGroupes);
	return $resultchargeDB;	
}

function delAP($supprimerAp) {
	global $mysqli;
	$sqlDelAP = "DELETE FROM lsun_ap_communs WHERE id = $supprimerAp";
	$mysqli->query($sqlDelAP);
	$sqlDelAPAid = "DELETE FROM lsun_j_ap_aid WHERE id_ap = $supprimerAp";
	$mysqli->query($sqlDelAPAid);
}

function modifieAP($id, $IntituleAp, $ApDescription, $ApLiaisonAid, $ApDisciplines) {
	saveAP($IntituleAp, $ApDisciplines, $ApDescription, $ApLiaisonAid, $id);
}


