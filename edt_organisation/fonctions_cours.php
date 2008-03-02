<?php

/**
 * Ensemble des fonctions qui permettent de créer un nouveau cours en vérifiant les précédents
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Toutes les fonctions d'initialisation de l'EdT sont utiles
require_once("edt_init_fonctions.php");

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
} // creneauSuivant()

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
} // creneauPrecedent()

// Fonction qui renvoie le nombre de créneaux précédents celui qui est appelé
function nombreCreneauxPrecedent($creneau){
	// On récupère l'heure du creneau appelé
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE
						heuredebut_definie_periode < '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND
						type_creneaux != 'pause'
						ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
} // nombreCreneauxPrecedent()

// Fonction qui renvoie le nombre de créneaux qui suivent celui qui est appelé
function nombreCreneauxApres($creneau){
	// On récupère l'heure du creneau appelé
	$heure_creneau_appele = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'"));
	$requete = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE heuredebut_definie_periode > '".$heure_creneau_appele["heuredebut_definie_periode"]."' AND type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");
	$nbre = mysql_num_rows($requete);

	return $nbre;
} // nombreCreneauxApres()

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
} // inverseHeuredeb_dec()

// Fonction qui vérifie si l'id_groupe de l'Edt n'est pas une AID
function retourneAid($id_groupe){
	// On explode pour voir
	$explode = explode("|", $id_groupe);
	if ($explode[0] == "AID") {
		return $explode[1];
	}else{
		return "non";
	}
}

/*
 * Fonction qui renvoie le début et la fin d'un cours en prenant en compte l'idée que chaque créneau
 * dure 2 "temps". Par exemple, pour un cours qui commence au début du 4ème créneau de la journée et
 * qui dure 2 heures, la fonction renvoie $retour["deb"] = 5 et $retour["fin"] = 8;
 * $jour = le jour de la semaine en toute lettre et en Français
 * $creneau = id du créneau (table absences_creneaux)
 * $heuredeb_dec vaut '0' si le cours commence au début d'un créneau et '0.5' si le cours commence au milieu du créneau
 * $duree = nombre de demi-cours (un cours d'un créneau et demi aura donc une durée de 3)
*/
function dureeTemps($jour, $creneau, $heuredeb_dec, $duree){
	// On détermine le "lieu" du début du cours
	$deb = 0;
	$fin = 0;
	$c_p = nombreCreneauxPrecedent($creneau);
	// et on calcule de début
	if ($c_p == 0) {
		$deb = 0;
	}elseif ($heuredeb_dec == 0) {
		$deb = ($c_p * 2) + 1;
	}else{
		$deb = ($c_p * 2) + 2;
	}
	// puis la fin
	$fin = $deb + $duree - 1;
	$retour = array();
	$retour["deb"] = $deb;
	$retour["fin"] = $fin;

	return $retour;
}

/*
 * Fonction qui vérifie si un prof a déjà cours pendant le cours qu'on veut créer
 * en tenant compte des types de semaine, des cours avant et après le cours qu'on veut créer.
 * $nom = login du prof (de Gepi)
 * $jour = le jour en toute lettres et en Français
 * $creneau = id de la table absences_creneaux
 * $duree = nombre de demi-cours (un cours d'un créneau et demi aura donc une durée de 3)
 * $heuredeb_dec vaut '0' si le cours commence au début d'un créneau et '0.5' si le cours commence au milieu du créneau
 * $type_semaine reprend la table edt_semaines ('0' si le cours a lieu toutes les semaines).
 * la fonction renvoie 'oui' si le prof n'a pas déjà cours et 'non' s'il a déjà cours.
*/
function verifProf($nom, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){
	$prof_libre = 'oui';
	// On récupère quelques infos utiles sur le cours qu'on veut créer
	$verif_temps = dureeTemps($jour, $creneau, $heuredeb_dec, $duree);
	$query_hdddc = mysql_query("SELECT heuredebut_definie_periode FROM absences_creneaux WHERE id_definie_periode = '".$creneau."'");
	$heurededebutducours = mysql_result($query_hdddc, "heuredebut_definie_periode");
echo '<br /> Essai pour un cours '.$nom.' '.$jour.' '.$creneau.' '.$duree.' '.$heuredeb_dec.' '.$type_semaine.' '.$verif_temps["deb"].' '.$verif_temps["fin"];
	// On essaie de récupérer directement tous les cours de ce prof dans la journée
	$query = mysql_query("SELECT e.jour_semaine, e.id_definie_periode, e.heuredeb_dec, e.duree, c.heuredebut_definie_periode
							FROM edt_cours e, absences_creneaux c
							WHERE jour_semaine = '".$jour."'
							AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
							AND login_prof = '".$nom."'
							AND e.id_definie_periode = c.id_definie_periode
							ORDER BY heuredebut_definie_periode
							") OR DIE('Erreur dans la requête : '.mysql_error());
	$compter_reponses = mysql_num_rows($query);
	if ($compter_reponses == 0) {
		// c'est fini, comme c'est le premier cours de ce prof sur cette journée, le cours est ok
		return $prof_libre;
	}else{
		// On détermine pour chaque cours quel est le début et la fin
		for($a = 0; $a < $compter_reponses; $a++){
			$jour_v[$a] = mysql_result($query, $a, "jour_semaine");
			$creneau_v[$a] = mysql_result($query, $a, "id_definie_periode");
			$heuredebut_v[$a] = mysql_result($query, $a, "heuredebut_definie_periode");
			$heuredeb_dec_v[$a] = mysql_result($query, $a, "heuredeb_dec");
			$duree_v[$a] = mysql_result($query, $a, "duree");
			$temps_v = dureeTemps($jour_v[$a], $creneau_v[$a], $heuredeb_dec_v[$a], $duree_v[$a]);
echo '<br />'.$jour_v[$a].' | '.$creneau_v[$a].' | '.$heuredeb_dec_v[$a].' | '.$duree_v[$a].' | '.$heuredebut_v[$a].' | '.$heuredeb_dec;
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> '.$temps_v["deb"].' - '.$temps_v["fin"].'<br />';
			// On vérifie d'abord les cours qui commencent avant le cours qu'on veut créer
			if ($heuredebut_v[$a] < $heurededebutducours) {
				// alors on vérifie si la fin des cours précédents n'empiètent pas sur le cours à créer
				if ($verif_temps["deb"] <= $temps_v["fin"]) {
					// Si la fin d'un cours existant dépasse le début du cours qu'on veut créer, on renvoie 'non'
					return 'non';
				}else{
					$prof_libre = 'oui';
				}
			}elseif($heuredebut_v[$a] == $heurededebutducours){
				// Le cas où les cours existants commencent sur le même créneau que le cours à créer
				if ($heuredeb_dec_v[$a] == $heuredeb_dec) {
					// les deux cours se chevauchent
					return 'non';
				}elseif($heuredeb_dec_v[$a] < $heuredeb_dec){
					// le cours existant commence juste avant celui qu'on veut créer
					// Si sa durée dépasse ou égale 2, alors il y a chevauchement
					if ($duree_v[$a] >= 2) {
						return 'non';
					}else{
						$prof_libre = 'oui';
					}
				}else{
					// le cours existant commence juste après celui qu'on veut créer
					// Si la durée du cours à créer dépasse ou égale 2, alors il y a chevauchement
					if ($duree >= 2) {
						return 'non';
					}else{
						$prof_libre = 'oui';
					}
				}

			}else{
				// Nous sommes dans le cas où des cours existants commencent après le cours à créer
				if ($verif_temps["fin"] >= $temps_v["deb"]) {
					// Alors on ne peut pas créer un nouveau cours la dessus
					return 'non';
				}else{
					$prof_libre = 'oui';
				}
			}
		}
	}
	return $prof_libre;
}

// Fonction qui vérifie si la salle est libre pour ce nouveau cours
function verifSalle($salle, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){

		$sallelibre = "oui";
	// On commence par vérifier le creneau demandé TEST1
	$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la vérification TEST1 : '.mysql_error());
	$verif = mysql_num_rows($requete);
	// S'il y a une réponse, c'est que la salle est déjà prise
	if ($verif >= 1) {
		$sallelibre = "non TEST1";
	}
	//echo "TEST1 : ".$verif."|".$sallelibre."<br />";

	// On vérifie alors les créneaux précédents TEST2
	$nbre_tests = nombreCreneauxPrecedent($creneau) + 1;
	$test_creneau = $creneau;
	$heuredeb_dec_i = inverseHeuredeb_dec($heuredeb_dec);

		// d'abord sur le même $heuredeb_dec
	for($a = 1; $a < $nbre_tests; $a++) {
		$test_creneau = creneauPrecedent($test_creneau);
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$test_creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '".$heuredeb_dec."'")
						OR DIE('Erreur dans la vérification TEST2a : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		// Si la duree du cours précédent excède le cours qu'on veut créer, c'est pas possible (sauf si la semaine en question l'exige)
		if ($verif["duree"] > (2 * $a)) {
			$sallelibre = "non TEST2a ".$verif["id_cours"];
		}
		//echo "TEST2a(".$a.") : ".$verif."|".$sallelibre."<br />";

		// Puis on vérifie en inverseHeuredeb_dec($heuredeb_dec)
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$test_creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '".$heuredeb_dec_i."'")
						OR DIE('Erreur dans la vérification TEST2b : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		// Si la duree du cours précédent excède le cours qu'on veut créer, c'est pas possible (sauf si la semaine en question l'exige)
		if ($verif["duree"] > ((2 * $a) + 1)) {
			$sallelibre = "non TEST2b";
		}
		//echo "TEST2b(".$a.") : ".$verif."|".$sallelibre."<br />";

	} // fin du for($a

	// En fonction du cours appelé, on vérifie les créneaux suivant si la durée excède 1 créneau
	// TEST3
	$nbre_tests = nombreCreneauxApres($creneau) + 1;
	$creneau_s = $creneau;
	$creneau_a = creneauSuivant($creneau);
	// On vérifie d'abord le demi-creneau suivant
	if ($duree > 1) {
		if ($heuredeb_dec == 0) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$creneau."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '0.5'")
						OR DIE('Erreur dans la vérification TEST3a : '.mysql_error());
		}elseif ($heuredeb_dec == "0.5"){
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
					id_salle = '".$salle."'
					AND jour_semaine = '".$jour."'
					AND id_definie_periode = '".$creneau_a."'
					AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
					AND heuredeb_dec = '0'")
						OR DIE('Erreur dans la vérification TEST3b : '.mysql_error());
		}
		$verif = mysql_num_rows($requete);
		if ($verif >= 1) {
			$sallelibre = "non TEST3ab";
		}
	}
	// Puis on vérifie tous les cours suivants pour être certain que la durée du cours à créer n'empiète pas sur un autre
	// mais d'abord, si la durée est de 1/2 créneau, on renvoie un "oui"
	if ($duree == '1') {
		return "oui";
	}
	// sinon on vérifie en fonction de la durée demandée
	for($b = 1; $b < $nbre_tests; $b++) {
			$creneau_s = creneauSuivant($creneau_s);
		if ($duree > ($b * 2)) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau_s."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la vérification TEST3c : '.mysql_error());
			$verif = mysql_num_rows($requete);
		}elseif($duree > (($b * 2) + 1)) {
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_salle = '".$salle."'
				AND jour_semaine = '".$jour."'
				AND id_definie_periode = '".$creneau_s."'
				AND (id_semaine = '".$type_semaine."' OR id_semaine = '0')
				AND heuredeb_dec = '".$heuredeb_dec_i."'")
					OR DIE('Erreur dans la vérification TEST3d : '.mysql_error());
			$verif = mysql_num_rows($requete);
		}
		if ($verif >= 1) {
			$sallelibre = "non TEST3cd";
		}
	} // fin du for($b...

	return $sallelibre;
} // verifSalle()

// Fonction qui vérifie si un groupe est libre pour le cours appelé
function verifGroupe($groupe, $jour, $creneau, $duree, $heuredeb_dec, $type_semaine){

		$groupelibre = "oui";
		$heuredeb_dec_i = inverseHeuredeb_dec($heuredeb_dec);
	// On vérifie le créneau demandé TEST
	$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
		id_groupe = '".$groupe."' AND
		jour_semaine = '".$jour."' AND
		id_definie_periode = '".$creneau."' AND
		(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
		heuredeb_dec = '".$heuredeb_dec."'")
			OR DIE('Erreur dans la verification TESTa : '.mysql_error());
	$verif = mysql_num_rows($requete);
	if ($verif >= 1) {
		$groupelibre = "non";
	}
	// Dans le cas où le cours commence au milieu du créneau, il faut vérifier le demi-creneau précédent
	if ($heuredeb_dec == "O.5") {
		$requete = mysql_query("SELECT id_cours FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '0'")
				OR DIE('Erreur dans la verification TESTb : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > 1) {
			$groupelibre = "non";
		}
	}
	// On vérifie les créneaux avant TEST1
	$nbre_tests = nombreCreneauxPrecedent($creneau) + 1;
	$creneau_test = $creneau;
	for($a = 1; $a < $nbre_tests; $a++){
		$creneau_test = creneauPrecedent($creneau_test);
		// Premier test sur le créneau précédent avec le même début (même $heuredeb_dec)
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau_test."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '".$heuredeb_dec."'")
				OR DIE('Erreur dans la verification TEST1a : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > (2 * $a)) {
			$groupelibre = "non";
		}
		// Deuxième test sur le créneau précédent avec le début inversé (inverseHeuredeb_dec($heuredeb_dec))
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".$creneau_test."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '".$heuredeb_dec_i."'")
				OR DIE('Erreur dans la verification TEST1b : '.mysql_error());
		$verif = mysql_fetch_array($requete);
		if ($verif["duree"] > ((2 * $a) + 1)) {
			$groupelibre = "non";
		}
		// Quand un cours commence sur le début du créneau, on vérifie le demi-cours précédent
		// dont la durée ne doit pas excéder 1 (seulement dans le premier tour de la boucle)
		if($heuredeb_dec == "0" AND $a == 1){
			if ($verif["duree"] > 1) {
				$groupelibre = "non";
			}
		}
	} // fin du for($a...

	// Si la durée du cours demandé dépasse 1, on vérifie alors les créneaux suivants TEST2
	$nbre_tests = nombreCreneauxApres($creneau) + 1;
	$creneau_test = $creneau;

	// SI le cours commence au milieu du creneau, on vérifie le demi-creneau suivant
	if ($heuredeb_dec == "0.5" AND $duree > 1) {
		$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
			id_groupe = '".$groupe."' AND
			jour_semaine = '".$jour."' AND
			id_definie_periode = '".creneauSuivant($creneau)."' AND
			(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
			heuredeb_dec = '0'")
				OR DIE('Erreur dans la verification TEST2 : '.mysql_error());
		$verif = mysql_num_rows($requete);
		if ($verif >= 1) {
			$groupelibre = "non";
		}
	}

	for($b = 1; $b < $nbre_tests; $b++) {
		$creneau_test = creneauSuivant($creneau_test);

		if ($duree >= 2 * $b) {
			// Premier test sur le créneau suivant avec le même début (même $heuredeb_dec)
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_groupe = '".$groupe."' AND
				jour_semaine = '".$jour."' AND
				id_definie_periode = '".$creneau_test."' AND
				(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
				heuredeb_dec = '".$heuredeb_dec."'")
					OR DIE('Erreur dans la verification TEST2a : '.mysql_error());
			$verif = mysql_num_rows($requete);
			if ($verif >= 1) {
				$groupelibre = "non";
			}
			// Deuxième test sur le créneau suivant avec le début inversé (inverseHeuredeb_dec($heuredeb_dec))
			$requete = mysql_query("SELECT id_cours, duree FROM edt_cours WHERE
				id_groupe = '".$groupe."' AND
				jour_semaine = '".$jour."' AND
				id_definie_periode = '".$creneau_test."' AND
				(id_semaine = '".$type_semaine."' OR id_semaine = '0') AND
				heuredeb_dec = '".$heuredeb_dec_i."'")
					OR DIE('Erreur dans la verification TEST2b : '.mysql_error());
			$verif_b = mysql_num_rows($requete);
			if ($verif_b >= 1) {
				$groupelibre = "non";
			}
		}
	//echo "TEST2b($b)".$creneau_test."|".$verif."|".$verif_b."|".$duree."|".$heuredeb_dec."|".$heuredeb_dec_i."<br />";
	} // fin du fror($b...

	return $groupelibre;
} // verifGroupe()
?>