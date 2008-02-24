<?php

/**
 *
 *
 * @version $Id$
 *
 * Ensemble des fonctions qui renvoient la concordance pour le fichier txt
 * de l'import des EdT.
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
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

// le login du prof
function renvoiLoginProf($numero){
	// on cherche dans la base
	$query = mysql_query("SELECT nom_gepi FROM edt_init WHERE nom_export = '".$numero."' AND ident_export = '1'");
	if ($query) {
		$retour = mysql_result($query, "nom_gepi");
	}else{
		$retour = 'erreur_prof';
	}

	return $retour;
}

// la salle
function renvoiIdSalle($chiffre){
	// On cherche l'Id de la salle
		$retour = 'erreur_salle';
	// On ne prend que les 10 premières lettres du numéro ($chiffre)
	$cherche = substr($chiffre, 0, 10);
	$query = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$cherche."'");
	if ($query) {
		$reponse = mysql_result($query, "id_salle");
		if ($reponse == '') {
			$retour = "inc";
		}else{
			$retour = $reponse;
		}
	}else{
		$retour = 'erreur_salle';
	}

	return $retour;
}

// le jour
function renvoiJour($diminutif){
	// Les jours sont de la forme lu, Ma, Je,...
	switch ($diminutif) {
	case 'Lu':
	    $retour = 'lundi';
	    break;
	case 'Ma':
	    $retour = 'mardi';
	    break;
	case 'Me':
	    $retour = 'mercredi';
	    break;
	case 'Je':
	    $retour = 'jeudi';
	    break;
	case 'Ve':
	    $retour = 'vendredi';
	    break;
	case 'Sa':
	    $retour = 'samedi';
	    break;
	case 'Di':
	    $retour = 'dimanche';
	    break;
	default :
		$retour = 'inc';
	}
	return $retour;
}

// renvoie le nom de la bonne table des créneaux
function nomTableCreneau($jour){
	$jour_semaine = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$numero_jour = NULL;
	for($t = 0; $t < 7; $t++){
		// On cherche à faire correspondre le numero_jour avec ce que donne la fonction php date("w")
		if ($jour == $jour_semaine[$t]) {
			$numero_jour = $t;
		}else{
			// A priori il n'y a rien à faire
		}
	}
	// Ensuite, en fonction du résultat, on teste et on renvoie la bonne table des créneaux
	if ($numero_jour == getSettingValue("jour_different")) {
		$retour = 'absences_creneaux_bis';
	}else{
		$retour = 'absences_creneaux';
	}

	return $retour;
}
// Id du créneau de début
function renvoiIdCreneau($heure_brute, $jour){
	// On transforme $heure_brute en un horaire de la forme hh:mm:ss
	$minutes = substr($heure_brute, 2);
	$heures = substr($heure_brute, 0, -2);
	$heuredebut = $heures.':'.$minutes.':00';
	$table = nomTableCreneau($jour);
	$query = mysql_query("SELECT id_definie_periode FROM ".$table." WHERE
					heuredebut_definie_periode <= '".$heuredebut."' AND
					heurefin_definie_periode > '".$heuredebut."'")
						OR DIE('Erreur renvoiIdCreneau : '.mysql_error());
	if ($query) {
		$retour = mysql_result($query, "id_definie_periode");
	}else{
		$retour = 'erreur_creneau';
	}

	return $retour;
}

// durée d'un créneau dans Gepi
function dureeCreneau(){
	// On récupère les infos sur un créneau
	$creneau = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux LIMIT 1"));
	$deb = $creneau["heuredebut_definie_periode"];
	$fin = $creneau["heurefin_definie_periode"];
	$nombre_mn_deb = (substr($deb, 0, -5) * 60) + (substr($deb, 3, -3));
	$nombre_mn_fin = (substr($fin, 0, -5) * 60) + (substr($fin, 3, -3));
	$retour = $nombre_mn_fin - $nombre_mn_deb;

	return $retour;
}

// La durée
function renvoiDuree($deb, $fin){
	// On détermine la durée d'un cours
	$duree_cours_base = dureeCreneau();
	$nombre_mn_deb = (substr($deb, 0, -2) * 60) + (substr($deb, 2));
	$nombre_mn_fin = (substr($fin, 0, -2) * 60) + (substr($fin, 2));
	$duree_mn = $nombre_mn_fin - $nombre_mn_deb;
	// le nombre d'heures entières
	$nbre = $duree_mn / $duree_cours_base;
	settype($nbre, 'integer');
	// le nombre de minutes qui restent
	$mod = $duree_mn % $duree_cours_base;
	// Et on analyse ce dernier (attention, la durée se compte en demi-créneaux)
	if ($mod >= (($duree_cours_base * 2) / 3)) {
		// Si c'est supérieur au 2/3 de la durée du cours, alors c'est une heure entière
		$retour = ($nbre * 2) + 2;
	}elseif($mod > (($duree_cours_base) / 3)) {
		// Si c'est supérieur au tiers de la durée d'un cours, alors c'est un demi-créneau de plus
		$retour = ($nbre * 2) + 1;
	}else{
		// sinon, c'est un souci de quelques minutes sans importance
		$retour = $nbre * 2;
	}

	return $retour;
}

// Heure debut decalée ou pas
function renvoiDebut($id_creneau, $heure_deb, $jour){
	// On détermine la durée d'un cours
	$duree_cours_base = dureeCreneau();
	// nbre de mn de l'heure de l'import
	$nombre_mn_deb = (substr($heure_deb, 0, -2) * 60) + (substr($heure_deb, 2));
	// Nombre de mn de l'horaire de Gepi
	$table = nomTableCreneau($jour);
	if ($id_creneau != '') {
			$heure = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM ".$table." WHERE id_definie_periode = '".$id_creneau."'"));
		$decompose = explode(":", $heure["heuredebut_definie_periode"]);
		$nbre_mn_gepi = ($decompose[0] * 60) + $decompose[1];
		// On fait la différence entre les deux horaires qui ont été convertis en nombre de minutes
		$diff = $nombre_mn_deb - $nbre_mn_gepi;
		// et on analyse cette différence
		if ($diff === 0 OR $diff < ($duree_cours_base / 4)) {
			$retour = '0';
		}elseif($diff > ($duree_cours_base / 3) AND $diff < (($duree_cours_base / 3) * 2)){
			$retour = '0.5';
		}else{
			$retour = '0';
		}
	}else{
		// par défaut, on renvoie un début classique
		$retour = '0';
	}

	return $retour;
}

// Renvoi des concordances
function renvoiConcordances($chiffre, $etape){
	// On récupère dans la table edt_init la bonne concordance
	// 2=Classe 3=GROUPE 4=PARTIE 5=Matières
	$query = mysql_query("SELECT nom_gepi FROM edt_init WHERE nom_export = '".$chiffre."' AND ident_export = '".$etape."'");
	if ($query) {
		$reponse = mysql_result($query, "nom_gepi");
		if ($reponse == '') {
			$retour = "inc";
		}else{
			$retour = $reponse;
		}
	}else{
		$retour = "erreur_".$etape;
	}

	return $retour;
}

// L'id_groupe
function renvoiIdGroupe($prof, $classe_txt, $matiere_txt, $grp_txt, $partie_txt){
	// $prof est le login du prof tel qu'il existe dans Gepi, alors que les autresinfos ne sont pas encore "concordés"
	// Les autres variables sont explicites dans leur désignation (c'est leur nom dans l'export texte)
	$classe = renvoiConcordances($classe_txt, 2);
	$matiere = renvoiConcordances($matiere_txt, 5);
	$partie = $partie_txt; //renvoiConcordances($partie_txt, 4);
	$grp = renvoiConcordances($grp_txt, 3);

	// On commence par le groupe. S'il existe, on le renvoie tout de suite
	if ($grp != "aucun" AND $grp != '' AND $grp != "inc") {
		return $grp;
	//}elseif($partie != "aucun" AND $partie != ''){
		// Pour le moment, on n'utilise pas ça
	}else{
		// On récupère la classe, la matière et le professeur
		// et on cherche un enseignement qui pourrait correspondre avec
		$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm WHERE
						jgp.login = '".$prof."' AND
						jgc.id_classe = '".$classe."' AND
						jgm.id_matiere = '".$matiere."' AND
						jgp.id_groupe = jgc.id_groupe AND
						jgp.id_groupe = jgm.id_groupe");

    	$rep_groupe = mysql_fetch_array($req_groupe);
    	$nbre_rep = mysql_num_rows($req_groupe);
    	// On vérifie ce qu'il y a dans la réponse
    	if ($nbre_rep == 0) {
			$retour = "aucun";
		} elseif ($nbre_rep > 1) {
    		$retour = "plusieurs";
    	}else{
			$retour = $rep_groupe["id_groupe"];
		}

	} // fin du else

	return $retour;
}
?>