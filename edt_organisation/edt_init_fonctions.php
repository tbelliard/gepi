<?php

/**
 *
 *
 *
 * Ensemble des fonctions qui renvoient la concordance pour le fichier txt
 * de l'import des EdT.
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stéphane Boireau, Julien Jocal
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

		$test = mysql_num_rows($query);
		if ($test >= 1) {
			$retour = mysql_result($query, 0,"nom_gepi");
		}else{
			$retour = 'erreur_prof';
		}

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
	$cherche = mb_substr($chiffre, 0, 10);
	$query = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$cherche."'");
	if ($query) {
		//$reponse = mysql_result($query, 0,"id_salle");
		$reponse = mysql_fetch_array($query);
		if ($reponse["id_salle"] == '') {
			$retour = "inc";
		}else{
			$retour = $reponse["id_salle"];
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
		$retour = 'edt_creneaux_bis';
	}else{
		$retour = 'edt_creneaux';
	}

	return $retour;
}
// Id du créneau de début
function renvoiIdCreneau($heure_brute, $jour){
	// On transforme $heure_brute en un horaire de la forme hh:mm:ss
	$minutes = mb_substr($heure_brute, 2);
	$heures = mb_substr($heure_brute, 0, -2);
	$heuredebut = $heures.':'.$minutes.':00';
	$table = nomTableCreneau($jour);
	$query = mysql_query("SELECT id_definie_periode FROM ".$table." WHERE
					heuredebut_definie_periode <= '".$heuredebut."' AND
					heurefin_definie_periode > '".$heuredebut."'")
						OR DIE('Erreur renvoiIdCreneau : '.mysql_error());
	if ($query) {
		$nbre = mysql_num_rows($query);
		if ($nbre >= 1) {
			$retour = mysql_result($query, 0,"id_definie_periode");
		}else{
			$retour = '0';
		}

	}else{
		$retour = 'erreur_creneau';
	}

	return $retour;
}

// durée d'un créneau dans Gepi
function dureeCreneau(){
	// On récupère les infos sur un créneau
	$creneau = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux LIMIT 1"));
	$deb = $creneau["heuredebut_definie_periode"];
	$fin = $creneau["heurefin_definie_periode"];
	$nombre_mn_deb = (mb_substr($deb, 0, -5) * 60) + (mb_substr($deb, 3, -3));
	$nombre_mn_fin = (mb_substr($fin, 0, -5) * 60) + (mb_substr($fin, 3, -3));
	$retour = $nombre_mn_fin - $nombre_mn_deb;

	return $retour;
}

// La durée pour les imports texte
function renvoiDuree($deb, $fin){
	// On détermine la durée d'un cours
	$duree_cours_base = dureeCreneau();
	$nombre_mn_deb = (mb_substr($deb, 0, -2) * 60) + (mb_substr($deb, 2));
	$nombre_mn_fin = (mb_substr($fin, 0, -2) * 60) + (mb_substr($fin, 2));
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
	$nombre_mn_deb = (mb_substr($heure_deb, 0, -2) * 60) + (mb_substr($heure_deb, 2));
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
	// 2=Classe 3=GROUPE 4=PARTIE 5=Matières pour IndexEducation
	// 1=créneaux 2=classe 3=matière 4=professeurs 7=regroupements 10=fréquence pour UDT de OMT
	if ($chiffre != '') {
		$sql = "SELECT nom_gepi FROM edt_init WHERE
								(nom_export = '".$chiffre."' OR nom_export = '".remplace_accents($chiffre, 'all_nospace')."')
								AND ident_export = '".$etape."'";
		$query = mysql_query($sql);
	}else{
		$query = NULL;
	}

	if ($query) {
		$test = mysql_num_rows($query);
		if ($test >= 1) {
			$reponse = mysql_fetch_array($query)
				OR trigger_error('Erreur dans le $reponse pour le '.$chiffre.'<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> sur la requête '.$sql, E_USER_WARNING);
		}else{
			$reponse["nom_gepi"] = '';
		}

		if ($reponse["nom_gepi"] == '') {
			$retour = "inc";
		}else{
			$retour = $reponse["nom_gepi"];
		}
	}else{
		$retour = "erreur";
	}

	return $retour;
}

// L'id_groupe
function renvoiIdGroupe($prof, $classe_txt, $matiere_txt, $grp_txt, $partie_txt, $type_import){
	// $prof est le login du prof tel qu'il existe dans Gepi, alors que les autres infos ne sont pas encore "concordés"

	if ($type_import == 'texte') {
		// On se préoccupe de la partie qui arrive de edt_init_texte.php et edt_init_concordance.php
		// Les autres variables sont explicites dans leur désignation (c'est leur nom dans l'export texte)
		$classe = renvoiConcordances($classe_txt, 2);
		$matiere = renvoiConcordances($matiere_txt, 5);
		$partie = $partie_txt; //renvoiConcordances($partie_txt, 4);
		$grp = renvoiConcordances($grp_txt, 3);
		//echo $classe.'|'.$matiere.'|'.$prof.'&nbsp;&nbsp;->&nbsp;&nbsp;';
	}elseif($type_import == 'csv2'){
		// On se préoccupe de la partie csv2 venant de edt_init_csv2.php et edt_init_concordance2.php
		$classe = $classe_txt;
		$matiere = $matiere_txt;
		$partie = '';
		$grp = $grp_txt;
	}else{
		$classe = '';
		$matiere = '';
		$partie = '';
		$grp = '';
	}


	// On commence par le groupe. S'il existe, on le renvoie tout de suite
	if($type_import == 'texte'
		AND $grp != "erreur"
		AND $grp != "aucun"
		AND $grp != ''
		AND $grp != "inc"){

		return $grp;

	}elseif ($grp != "aucun"
		AND $grp != ''
		AND $grp != "inc"
		AND $type_import != 'texte') {

		return $grp;

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
		//print_r($rep_groupe);
		//echo '<br />';
		$nbre_rep = mysql_num_rows($req_groupe);


		// On vérifie ce qu'il y a dans la réponse
		if ($nbre_rep == 0) {
			$retour = "aucun";
		} elseif ($nbre_rep > 1) {

			//il y a plusieurs groupes correspondants 
			//on essaye d'utiliser l'information $partie_txt
			//pour déterminer le bon groupe


			//si il n'y a pas de groupe donné
			//ou si le groupe est la matière 
			//on essaye d'utiliser la MATIERE comme nom de groupe
			//comme nom de groupe
			if($partie_txt == '' OR $partie_txt == $matiere_txt){

				//on fait la requette avec le nom de la matière
				$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm, groupes grp WHERE
				jgp.login = '".$prof."' AND
				jgc.id_classe = '".$classe."' AND
				jgm.id_matiere = '".$matiere."' AND
				grp.name = '".$matiere_txt."' AND
				jgp.id_groupe = grp.id AND
				jgp.id_groupe = jgc.id_groupe AND
				jgp.id_groupe = jgm.id_groupe");

			}

			// on regarde si on a bien obtenu un seul groupe
			$nbre_rep = mysql_num_rows($req_groupe);

			if( $nbre_rep != 1 ){

				//on va essayer de construire le nom de groupe
				//avec la classe, la matière et la partie

				//on prend le nom de groupe
				$groupe_nom = str_replace ( ' ', '_', $partie_txt);

				//on récupére le nom de la classe
				$req_classe_list = mysql_query("SELECT classe FROM classes WHERE id = '".$classe."' ");
				$classe_list = mysql_fetch_array($req_classe_list);
				$classe_nom = $classe_list["classe"];
				$classe_nom = str_replace ( ' ', '_', $classe_nom);

				//----------------------------------------
				//On essaye de de voir si un seul groupe 
				// correspond aux informations données
				// REGEXP '^MATIERE.?(CLASSE)?.?GROUPE$'
				//----------------------------------------

				$req_groupe = mysql_query("SELECT jgp.id_groupe FROM j_groupes_professeurs jgp, j_groupes_classes jgc, j_groupes_matieres jgm, groupes grp WHERE
					jgp.login = '".$prof."' AND
					jgc.id_classe = '".$classe."' AND
					jgm.id_matiere = '".$matiere."' AND
					grp.name REGEXP '^".$matiere.".?(".$classe_nom.")?.?".$groupe_nom.'$'."' AND
					jgp.id_groupe = grp.id AND
					jgp.id_groupe = jgc.id_groupe AND
					jgp.id_groupe = jgm.id_groupe");

			}

			// on regarde si on a bien obtenu un seul groupe
			$nbre_rep = mysql_num_rows($req_groupe);

			if( $nbre_rep == 1){
				$rep_groupe = mysql_fetch_array($req_groupe);
				$retour = $rep_groupe["id_groupe"];
			}else{
				$retour = "plusieurs";
			}

		}else{
			$retour = $rep_groupe["id_groupe"];
		}

	} // fin du else

	return $retour;
}

/*
 * Fonction qui teste si une salle existe dans Gepi et qui l'enregistre si elle n'existe pas
 * $numero est le numéro de la salle
*/
function testerSalleCsv2($numero){
	// On teste la table
	$query = mysql_query("SELECT id_salle FROM salle_cours WHERE numero_salle = '".$numero."'")
				OR trigger_error('Erreur dans la requête '.$query.' : '.mysql_error());
	$rep = @mysql_result($query, 0,"id_salle");
	if ($rep != '' AND $rep != NULL AND $rep != FALSE) {
		// On renvoie "ok"
		return "ok";
	}else{
		// On enregistre la nouvelle salle
		$query2 = mysql_query("INSERT INTO salle_cours SET numero_salle = '".$numero."', nom_salle = ''");
		if ($query2) {
			return "enregistree";
		}
	}
}

/*
 * Fonction qui teste si une salle existe dans Gepi
 * $numero est le numéro de la salle
*/
function salleifexists($numero){
	// On teste la table
	$sql = "SELECT id_salle FROM salle_cours WHERE numero_salle = '".$numero."'";
	$query = mysql_query($sql)
				OR trigger_error('Impossible de vérifier l\'existence de cette salle : la requête '.$sql.' a échoué : '.mysql_error(), E_USER_WARNING);
	// On force tout de même le résultat
	$rep = @mysql_result($query, 0,"id_salle");
	if ($rep != '' AND $rep != NULL AND $rep != FALSE) {
		// On renvoie "oui"
		return "oui";
	}else{
		return "non";
	}
}


/*
 * Fonction qui fonction renvoie l'id du créneau de départ, la durée et le moment du début du cours (CSV2)
 * sous la forme d'un tableau id_creneau, duree et debut
*/
function rechercheCreneauCsv2($creneau){
	$duree_base = dureeCreneau();

	// On fait attention à la construction de ce créneau
	$test1 = explode(" - ", $creneau);

	// Pour le id du creneau
	$id_creneau = renvoiConcordances($creneau, 1);
	if ($id_creneau != 'inc') {
		$retour["id_creneau"] = $id_creneau;
	}else{
		// Il faut chercher d'une autre façon le bon id de cours avec $test1[0]
		$test2 = explode("h", $test1[0]); // $test2[0] = 8 et $test2[1] = 00
		if (mb_strlen($test2[0]) < 2) {
			// On ajoute un '0' devant l'heure
			$heure = '0'.$test2[0];
		}else{
			$heure = $test2[0];
		}
		$heure_reconstruite = $heure.':'.$test2[1].':'.'00';
		$query = mysql_query("SELECT DISTINCT id_definie_periode FROM edt_creneaux
						WHERE heuredebut_definie_periode <= '".$heure_reconstruite."'
						ORDER BY heuredebut_definie_periode ASC LIMIT 1");
		if ($query) {
			// On a trouvé
			$reponse_id = mysql_fetch_array($query);
			if ($reponse_id["id_definie_periode"] != '') {
				$retour["id_creneau"] = $id_creneau = $reponse_id["id_definie_periode"];
			}else{
				// Si on n'a pas de réponse valide, on ne peut pas définir le cours
				return 'erreur';
			}
		}
	}

	// la durée et le début
	if (isset($test1[1])) {
		// ça veut dire que le créneau étudié est de la forme 8h00 - 9h35 : $test1[0] = 8h00 et $test1(1] = 9h00
		// on recherche si le début est bon ou pas pour savoir si le cours commence au début du créneau ou pas
		$heure_debut = mysql_fetch_array(mysql_query("SELECT heuredebut_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$id_creneau."'"));
		$test3 = explode(":", $heure_debut["heuredebut_definie_periode"]);
		if (mb_substr($test3[0], 0, -1) == "0") {
			$heu = mb_substr($test3[0], -1);
		}else{
			$heu = $test3[0];
		}

		// On définit le moment de début du cours
		if (($heu.'h'.$test3[1]) == $test1[0]) {
			// Le cours commence au début du créneau
			$retour["debut"] = '0';
		}else{
			// Le cours commence au milieu du créneau
			$retour["debut"] = 'O.5';
		}

		// On définit la durée
		$he0 = explode("h", $test1[0]); // l'heure de début de la demande
		$he1 = explode("h", $test1[1]); // l'heure de fin de la demande
		if (!isset($he0[1])) { $he0[1] = '00';	}
		if (!isset($he1[1])) { $he1[1] = '00';	}
		$duree_demandee = (60 * ($he1[0] - $he0[0])) + ($he1[1] - $he0[1]);
		if ($duree_demandee == $duree_base) {
			// ALors la durée est de 1 créneau donc 2 pour Gepi
			$retour["duree"] = 2;
		}elseif($duree_demandee < $duree_base){
			// Alors le cours la moitié d'un créneau
			$retour["duree"] = 1;
		}else{
			// Le cours dure plus de 1 créneau
			// On détermine la durée exacte
			$test_duree = $duree_demandee / $duree_base;
			// On récupère le nombre de créneaux entiers
			$nbre_t = explode(".", $test_duree); // $nbre_t[0] est donc le nombre créneaux entiers
			if (isset($nbre_t[1])) {
				$test2 = mb_substr($nbre_t[1], 0, 1); // on ne garde que le premier chiffre après la virgule
			}else{
				$test2 = 0;
			}

			if ($test2 < 3) {
				// c'est fini
				$retour["duree"] = $nbre_t[0] * 2;
			}elseif($test2 > 7){
				// On ajoute 1 créneau entier en plus
				$retour["duree"] = ($nbre_t[0] * 2) + 2;
			}else{
				// On ajoute un demi créneau en plus
				$retour["duree"] = ($nbre_t[0] * 2) + 1;
			}

		}

	}else{
		// ça veut dire que le cours commence au début du créneau et dure 1 créneau (donc 2 pour Gepi)
		$retour["duree"] = '2';
		$retour["debut"] = '0';
	}
	return $retour;
}

/*
 * Fonction qui enregistre les cours des imports UDT de OMT
*/
function enregistreCoursCsv2($jour, $creneau, $classe, $matiere, $prof, $salle, $groupe, $regroupement, $effectif, $modalite, $frequence, $aire){
	$retour["msg_erreur"] = '';
	// Les étapes vont de 0 à 11 en suivant l'ordre des variables ci-dessus
	// Si un cours est enregistré, on renvoie 'oui', sinon on renvoie 'non'

	// le jour => il est bon, il faut juste l'écrire en minuscule
	$jour_e = my_strtolower($jour);
	// Cette fonction renvoie l'id du créneau de départ, la durée et le moment du début du cours
	$test_creneau = rechercheCreneauCsv2($creneau);
	$creneau_e = $test_creneau["id_creneau"];
	$duree_e = $test_creneau["duree"];
	$heuredeb_dec = $test_creneau["debut"];
	// On récupère les concordances
	$classe_e = renvoiConcordances($classe, 2);
	$matiere_e = renvoiConcordances($matiere, 3);
	$prof_e = renvoiConcordances($prof, 4);
	$salle_e = renvoiIdSalle($salle); // on peut se le permettre puisque le travail sur les salles a déjà été effectué
	$type_semaine = renvoiConcordances($frequence, 10);
	if ($type_semaine == '' OR $type_semaine == 'erreur') {
		$type_semaine = '0';
	}

	// Il reste à déterminer le groupe
	if ($regroupement != '') {
		//echo "\$regroupement=$regroupement<br />";

		$test = explode("|", $regroupement);

		if ($test[0] == 'EDT') {

			$groupe_e = $regroupement;

		}else{

			$groupe_e = renvoiConcordances($regroupement, 7);
			//echo "\$groupe_e=$groupe_e<br />";

			if ($groupe_e == 'erreur') {
				$regrp = '';
				$groupe_e = renvoiIdGroupe($prof_e, $classe_e, $matiere_e, $regrp, $groupe, 'csv2');
			}

		}

	}else{
		// On recherche le groupe
		$regrp = '';
		$groupe_e = renvoiIdGroupe($prof_e, $classe_e, $matiere_e, $regrp, $groupe, 'csv2');
		//echo "\$groupe_e = renvoiIdGroupe($prof_e, $classe_e, $matiere_e, $regrp, $groupe, 'csv2');=$groupe_e<br />";
	}

	// On vérifie si tous les champs importants sont précisés ou non
	if ($jour_e == '' OR $creneau_e == 'erreur'
		OR $groupe_e == 'aucun' OR $groupe_e == 'plusieurs' OR $groupe_e == 'erreur'
		OR $matiere_e == 'inc' OR $classe_e == 'inc'
		OR $prof_e == 'inc' OR $prof_e == 'erreur' OR $prof_e == 'aucun') {

		// Il manque des informations
		$retour["reponse"] = 'non';
		$retour["msg_erreur"] .= $jour_e.'|'.$creneau_e.'|'.$groupe_e.'|'.$matiere_e.'|'.$classe_e.'|'.$prof_e;
		//echo "Il manque des infos.<br />";
	}else{
		// On vérifie que cette ligne n'existe pas déjà
		// On ne tient pas compte du type de semaine car on estime que si un enseignement a lieu sur deux types de semaines, c'est qu'il a lieu toutes les semaines
		if(preg_match("/^AID/", $groupe_e)) {
			$tmp_tab=explode('|', $groupe_e);
			$id_aid_courant=$tmp_tab[1];
			$sql="SELECT id_cours FROM edt_cours WHERE
							id_aid = '".$id_aid_courant."' AND
							id_salle = '".$salle_e."' AND
							jour_semaine = '".$jour_e."' AND
							id_definie_periode = '".$creneau_e."' AND
							duree = '".$duree_e."' AND
							heuredeb_dec = '".$heuredeb_dec."' AND
							id_calendrier = '0' AND
							modif_edt = '0' AND
							login_prof = '".$prof_e."';";
		}
		else {
			$sql="SELECT id_cours FROM edt_cours WHERE
							id_groupe = '".$groupe_e."' AND
							id_salle = '".$salle_e."' AND
							jour_semaine = '".$jour_e."' AND
							id_definie_periode = '".$creneau_e."' AND
							duree = '".$duree_e."' AND
							heuredeb_dec = '".$heuredeb_dec."' AND
							id_calendrier = '0' AND
							modif_edt = '0' AND
							login_prof = '".$prof_e."';";
		}
		//echo "Test ifexists : $sql<br />";
		$ifexists = mysql_query($sql)
							OR DIE('erreur dans la requête '.$ifexists.' : '.mysql_error());

		$erreur_report = mysql_fetch_array($ifexists);
		$retour["msg_erreur"] .= 'Ce cours existe déjà ('.$erreur_report["id_cours"].').';

		if (mysql_num_rows($ifexists) < 1) {
			// On enregistre la ligne
			//echo "\$groupe_e=$groupe_e<br />";
			if(preg_match("/^AID/", $groupe_e)) {
				$tmp_tab=explode('|', $groupe_e);
				$id_aid_courant=$tmp_tab[1];
				$sql = "INSERT INTO `edt_cours` (`id_cours`,
										`id_aid`,
										`id_salle`,
										`jour_semaine`,
										`id_definie_periode`,
										`duree`,
										`heuredeb_dec`,
										`id_semaine`,
										`id_calendrier`,
										`modif_edt`,
										`login_prof`)
								VALUES ('',
										'".$id_aid_courant."',
										'".$salle_e."',
										'".$jour_e."',
										'".$creneau_e."',
										'".$duree_e."',
										'".$heuredeb_dec."',
										'".$type_semaine."',
										'0',
										'0',
										'".$prof_e."')";
			}
			else {
				$sql = "INSERT INTO `edt_cours` (`id_cours`,
										`id_groupe`,
										`id_salle`,
										`jour_semaine`,
										`id_definie_periode`,
										`duree`,
										`heuredeb_dec`,
										`id_semaine`,
										`id_calendrier`,
										`modif_edt`,
										`login_prof`)
								VALUES ('',
										'".$groupe_e."',
										'".$salle_e."',
										'".$jour_e."',
										'".$creneau_e."',
										'".$duree_e."',
										'".$heuredeb_dec."',
										'".$type_semaine."',
										'0',
										'0',
										'".$prof_e."')";
			}
			//echo "$sql<br />";

			$retour["msg_erreur"] .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;'.$sql;

			$envoi = mysql_query($sql) OR DIE('Erreur dans la requête '.$sql);
			if ($envoi) {
				// et on renvoie 'ok'
				$retour["reponse"] = 'ok';
			}else{
				$retour["reponse"] = 'non';
			}
		}else{
			$retour["reponse"] = 'non';
		}
	}
	return $retour;
}

// fonction qui permet de vérifier si on doit / peut créer un edt_gr pour les emplois de temps
function gestion_edt_gr($tab){

	$retour = '';
// On va regarder si on peut créer un edt_gr avec pour nom $tab[7] et pour nom long $tab[3]
if ($tab[4] != '') {
	// le professeur est précisé, donc il s'agit d'un cours
	if ($tab[8] == 'CG') {

		$type_sub = 'classe';
		$subdivision = renvoiConcordances($tab[2], 2);
		$nom = $tab[7];

	}elseif($tab[8] == 'TP' OR $tab[8] == 'TD'){

		$type_sub = 'demi';
		$subdivision = renvoiConcordances($tab[2], 2);
		// On vérifie que les regroupements soient bien précisé sinon, c'est le groupe qui est choisi
		if ($tab[7] != '') {
			$nom = $tab[7];
		}else{
			$nom = $tab[6];
		}

	}else{
		$type_sub = 'autre';
		$subdivision = 'plusieurs';
		$nom = $tab[7];
	}

	$nom_long = $tab[3];

	// On vérifie si ce edt_gr n'existe pas déjà... s'il existe, on précise que le type de subdivision passe à 'autre'
	// et on passe subdivision à 'plusieurs'
	$query_verif = mysql_query("SELECT id FROM edt_gr_nom
										WHERE nom = '".$nom."'
										AND nom_long = '".$nom_long."'
										AND (subdivision_type = '".$type_sub."' OR subdivision_type = 'autre')");
	$nbre = mysql_num_rows($query_verif);

	if ($nbre >= 1) {

		// alors il existe déjà, on le met à jour et on s'en va
		//$rep_id = mysql_result($query_verif, 0,"id");
		$rep_id = mysql_fetch_array($query_verif);
		$maj = mysql_query("UPDATE edt_gr_nom SET subdivision_type = 'autre', subdivision = 'plusieurs' WHERE id = '".$rep_id["id"]."'");

		$retour = $rep_id["id"];

	}else{

		// on crée cet edt_gr
		$query_create = mysql_query("INSERT INTO edt_gr_nom (id, nom, nom_long, subdivision_type, subdivision)
												VALUES ('', '".$nom."', '".$nom_long."', '".$type_sub."', '".$subdivision."')");
		// On récupère son id
		$query_id = mysql_query("SELECT id FROM edt_gr_nom
												WHERE nom = '".$nom."'
												AND nom_long = '".$nom_long."'
												AND subdivision_type = '".$type_sub."'
												AND subdivision = '".$subdivision."");
		//$recup_id = mysql_result($query_id, 0,"id");
		$recup_id = mysql_fetch_array($query_id);
		$create_prof = mysql_query("INSERT INTO edt_gr_prof (id, id_gr_nom, id_utilisateurs)
																				VALUES('', '".$recup_id["id"]."', '".renvoiConcordances($tab[4], 4)."')");
		$retour = $recup_id["id"];

	}

}else{

	// on n'a pas créé de edt_gr donc on renvoie 'non
	$retour = 'non';
}

	return $retour;

}
?>
