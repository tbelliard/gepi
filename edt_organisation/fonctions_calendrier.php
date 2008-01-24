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
	$numero_sem_actu = date("w");
	$query = mysql_query("SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine = '".$numero_sem_actu."'");

	if (count($query) != 1) {
		$retour = "0";
	}else{
		$type = mysql_result($query, "type_edt_semaine");
		$retour = $type;
	}
	return $retour;
}

// Fonction qui retourne le jour actu en français et en toutes lettres
function retourneJour(){

}

// Fonction qui retourne le créneau actuel
function retourneCreneau(){

}

// Fonction qui retourne l'id du cours d'un prof à un créneau, jour et type_semaine donnés
function retourneCours($prof){
	$query = mysql_query("SELECT * FROM edt_cours, j_groupes_professeurs WHERE
			edt_cours.jour_semaine='".retourneJour()."' AND
			edt_cours.id_definie_periode='".retourneCreneau()."' AND
			edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND
			login='".$prof."' AND
			edt_cours.heuredeb_dec = '".$heuredeb_dec."' AND
			edt_cours.id_semaine = '".typeSemaineActu()."'
			ORDER BY edt_cours.id_semaine")
				or die('Erreur : cree_tab_general(prof) !');;
}

?>