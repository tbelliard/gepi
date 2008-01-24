<?php

/**
 * @version $Id$
 *
 * Fichier de fonctions destinées au calendrier
 *
 * @copyright 2008
 */

// Fonction qui retourne le type de la semaine en cours
function typeSemaineActu(){
		$retour = '0';
	$numero_sem_actu = date("w");
	$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");

	if (count($query) != 1) {
		$retour = '0';
	}else{
		$type = mysql_result($query, "type_edt_semaine");
		$retour = $type;
	}
	return $retour;
}

// Fonction qui retourne le jour actu en français et en toutes lettres
function retourneJour(){
		$jour = date("w");
	// On traduit le nom du jour
	$semaine = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
			$jour_semaine = '';
	for($a = 0; $a < 7; $a++) {
		if ($jour == $a) {
			$jour_semaine = $semaine[$a];
		}
	}
	return $jour_semaine;
}

// Fonction qui retourne l'id du créneau actuel
function retourneCreneau(){
		$retour = 'non';
	$heure = date("H:i:s");
	$query = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE
			heuredebut_definie_periode <= '".$heure."' AND
			heurefin_definie_periode > '".$heure."'")
				OR DIE('Le creneau n\'est pas trouvé : '.mysql_error());
	if ($query) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_definie_periode"];
	}else {
		$retour = "non";
	}
	return $retour;
}

//Fonction qui retourne si on est dans la première ou la seconde partie d'un créneau
function heureDeb(){
		$retour = '0';
	// On compare des minutes car c'est plus simple
	$heureMn = (date("H") * 60) + date("i");
	$creneauId = retourneCreneau();
	// On récupère l'heure de début et celle de fin du créneau
	$query = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneauId."'");
	if ($query) {
		$reponse = mysql_fetch_array($query);
		// On enlève les secondes
		$explodeDeb = explode(":", $reponse["heuredebut_definie_periode"]);
		$explodeFin = explode(":", $reponse["heurefin_definie_periode"]);
		$dureeCreneau = (($explodeFin[0] - $explodeDeb[0]) * 60) + ($explodeFin[1] - $explodeDeb[1]);
		$miCreneau = $dureeCreneau / 2;
		$heureMilieu = ($explodeDeb[0] * 60) + $explodeDeb[1] + $miCreneau;
		// et on compare
		if ($heureMn > $heureMilieu) {
			$retour = 'O.5';
		}elseif($heureMn < $heureMilieu){
			$retour = '0';
		}else{
			$retour = '0';
		}
	}
	return $retour;
}

// Fonction qui retourne l'id du cours d'un prof à un créneau, jour et type_semaine donnés
function retourneCours($prof){
		$retour = 'non';
	$query = mysql_query("SELECT id_cours FROM edt_cours, j_groupes_professeurs WHERE
			edt_cours.jour_semaine='".retourneJour()."' AND
			edt_cours.id_definie_periode='".retourneCreneau()."' AND
			edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND
			login='".$prof."' AND
			edt_cours.heuredeb_dec = '0' AND
			(edt_cours.id_semaine = '".typeSemaineActu()."' OR edt_cours.id_semaine = '0')
			ORDER BY edt_cours.id_semaine")
				or die('Erreur : retourneCours(prof) !'.mysql_error());
	$nbreCours = mysql_num_rows($query);
	if ($nbreCours >= 1) {
		$reponse = mysql_fetch_array($query);
		$retour = $reponse["id_cours"];
	}
	return $retour;
}

?>