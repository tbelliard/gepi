<?php

/**
 * Ensemble des fonctions qui permettent de créer un nouveau cours en vérifiant les précédents
 *
 * @version $Id$
 * @copyright 2007
 */

// Fonction qui renvoie l'id du créneau suivant de celui qui est appelé
function creneauSuivant($creneau){
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($creneau, $cherche_creneaux);
	if (isset($cherche_creneaux[$ch_index+1])) {
		$reponse = $cherche_creneaux[$ch_index+1];
	}else{
		$reponse = "aucun";
	}
	return $reponse;
}

// Fonction qui renvoie l'id du créneau précédent de celui qui est appelé
function creneauPrecedent($creneau){
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($creneau, $cherche_creneaux);
	if (isset($cherche_creneaux[$ch_index-1])) {
		$reponse = $cherche_creneaux[$ch_index-1];
	}else{
		$reponse = "aucun";
	}
	return $reponse;
}

// Fonction qui renvoie le nombre de créneaux précédents celui qui est appelé
function nombreCreneauxPrecedent($creneau){
	// On récupère l'heure du creneau appelé
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode < '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
}

// Fonction qui renvoie le nombre de créneaux qui suivent celui qui est appelé
function nombreCreneauxApres($creneau){
	// On récupère l'heure du creneau appelé
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode > '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
}

// Fonction qui renvoie l'inverse de heuredeb_dec
function inverseHeuredeb_dec($heuredeb_dec){
	if ($heuredeb_dec == "0.5") {
		$retour = "0";
	}elseif($heuredeb_dec == "0"){
		$retour = "0.5";
	}else{
		$retour = NULL;
	}

	return $retour;
}

// Fonction qui vérifie que le professeur n'a pas déjà cours à ce moment là et sur la durée
function verifProf($nom, $jour, $creneau, $duree, $heuredeb_dec){
	$coursoupas = "non";
	// Le nouveau cours doit être créer ce $jour, sur ce $creneau sur une $duree donnée.
	$sql = "SELECT * FROM edt_cours, j_groupes_professeurs WHERE edt_cours.jour_semaine='".$jour."' AND edt_cours.id_definie_periode='".$creneau."' AND edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND login='".$nom."' AND edt_cours.heuredeb_dec = '".$heuredeb_dec."'";
	$requete = mysql_query($sql);
	$reponse = mysql_num_rows($requete); //OR DIE('Erreur dans la reponse : '.mysql_error());
		if ($reponse >= 1) {
			$coursoupas = "oui";
		}
	// On vérifie alors pour tous les créneaux précédents sans oublier les 1/2 créneaux
		$creneau_t = $creneau;
				$nbre_test = (nombreCreneauxPrecedent($creneau)) + 1;
		for($a = 1; $a < $nbre_test; $a++){
			$creneau = creneauPrecedent($creneau);
			$requete = mysql_query("SELECT duree FROM edt_cours, j_groupes_professeurs WHERE
				edt_cours.jour_semaine = '".$jour."' AND
				edt_cours.id_definie_periode = '".$creneau."' AND
				edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
				login = '".$nom."' AND
				edt_cours.heuredeb_dec = '".$heuredeb_dec."'") OR DIE('Erreur dans la requete n° '.$a.' du type : '.mysql_error());
			$verif = mysql_fetch_array($requete);
			// On vérifie que la durée n'excède pas le cours appelé
				if ($verif["duree"] > (2 * $a)) {
					$coursoupas = "oui";
				}
			//echo "FOR($a) : |".$verif["duree"]."|".$heuredeb_dec."|".$creneau."<br />";
		}
	// On décale l'heure de début et on refait la même opération
	$inv_heuredeb_dec = inverseHeuredeb_dec($heuredeb_dec);
	$creneau = $creneau_t;
		for($a = 1; $a < $nbre_test; $a++){
			$creneau = creneauPrecedent($creneau);
			$requete = mysql_query("SELECT duree FROM edt_cours, j_groupes_professeurs WHERE
				edt_cours.jour_semaine = '".$jour."' AND
				edt_cours.id_definie_periode = '".$creneau."' AND
				edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
				login = '".$nom."' AND
				edt_cours.heuredeb_dec = '".$inv_heuredeb_dec."'") OR DIE('Erreur dans la requete n° '.$a.' du type : '.mysql_error());
			$verif = mysql_fetch_array($requete);
			// On vérifie que la durée n'excède pas le cours appelé
				if ($verif["duree"] > ((2 * $a) +1)) {
					$coursoupas = "oui";
				}
			//echo "FOR2($a) : ".$verif["duree"]."|".$inv_heuredeb_dec."|".$creneau."<br />";
		}
	$creneau = $creneau_t;
// On vérifie aussi si ce nouveau cours n'empiète pas sur un cours suivant pour le même professeur
	if ($duree >= 2 AND $heuredeb_dec == "0") {
		// On vérifie s'il n'y a pas déjà un cours qui commence en 0.5
	$requete = mysql_query("SELECT duree FROM edt_cours, j_groupes_professeurs WHERE
				edt_cours.jour_semaine = '".$jour."' AND
				edt_cours.id_definie_periode = '".$creneau."' AND
				edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
				login = '".$nom."' AND
				edt_cours.heuredeb_dec = '0.5'")
				OR DIE('Erreur dans la requete : '.mysql_error());
	$verif1 = mysql_num_rows($requete);
	}
	// Si la durée est supérieure à 1 créneau (donc supérieure à 2)
	if ($duree >= 2){
		// Il convient alors de vérifier le tout en fonction de cette durée
			$nbre_test = nombreCreneauxApres($creneau) + 1;
		for($c = 1; $c < $nbre_test; $c++){
			if ($duree >= ($c * 2)) {
				$creneau = creneauSuivant($creneau);
				$requete = mysql_query("SELECT duree FROM edt_cours, j_groupes_professeurs WHERE
					edt_cours.jour_semaine = '".$jour."' AND
					edt_cours.id_definie_periode = '".$creneau."' AND
					edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
					login = '".$nom."' AND
					edt_cours.heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la requete : '.mysql_error());
				$verif = mysql_num_rows($requete);

				if ($verif == "1") {
					$coursoupas = "oui";
				}
			}
			//echo "FOR3a($c) : ".$verif."|".$heuredeb_dec."|".$creneau."<br />";
			if ($duree >= ($c * 2 + 1)) {
				$heuredeb_dec = inverseHeuredeb_dec($heuredeb_dec);
				$requete = mysql_query("SELECT duree FROM edt_cours, j_groupes_professeurs WHERE
					edt_cours.jour_semaine = '".$jour."' AND
					edt_cours.id_definie_periode = '".$creneau."' AND
					edt_cours.id_groupe = j_groupes_professeurs.id_groupe AND
					login = '".$nom."' AND
					edt_cours.heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la requete : '.mysql_error());
				$verif = mysql_num_rows($requete);

				if ($verif == "1") {
					$coursoupas = "oui";
				}
			}
			//echo "FOR3b($c) : ".$verif."|".$heuredeb_dec."|".$creneau."<br />";
		}
	}


	return $coursoupas;
}

?>