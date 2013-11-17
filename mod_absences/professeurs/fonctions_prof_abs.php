<?php

/**
 * @version $Id$
 *
 * Fichier de fonctions pour prof_ajout_abs.php
 *
 * @copyright 2008
 */

// permet de supprimer un courrier s'il y a besoin par rapport à l'id de l'absence
function modif_suivi_du_courrier($id_absence_eleve, $eleve_absence_eleve) {
	global $prefix_base;
		// on vérify s'il y a un courrier si oui on le supprime s'il fait parti d'un ensemble de courrier alors on le modifi.
		// première option il existe une lettre qui fait seulement référence à cette id donc suppression
	$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'"),0);
	if( $cpt_lettre_suivi == 1 ) {
		$requete = "DELETE FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi = ',".$id_absence_eleve.",'";
		mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
	// deuxième option il existe une lettre qui fait référence à cette id mais à d'autre aussi donc modification
	$cpt_lettre_suivi = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE quirecois_lettre_suivi = '".$eleve_absence_eleve."' AND partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'"),0);
	if( $cpt_lettre_suivi == 1 ) {
		$requete = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'");
		$donnee = mysqli_fetch_array($requete);
		$remplace_sa = ','.$id_absence_eleve.',';
		$modifier_par = my_ereg_replace($remplace_sa,',',$donnee['partdenum_lettre_suivi']);
		$requete = "UPDATE ".$prefix_base."lettres_suivis SET partdenum_lettre_suivi = '".$modifier_par."', envoye_date_lettre_suivi = '', envoye_heure_lettre_suivi = '', quienvoi_lettre_suivi = '' WHERE partde_lettre_suivi = 'absences_eleves' AND type_lettre_suivi = '6' AND partdenum_lettre_suivi LIKE '%,".$id_absence_eleve.",%'";
			mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
}

// fonction qui teste l'élève à un créneau donné à la date du jour
function suivi_absence($creneau_id, $eleve_id){
	// On récupère les horaires de début du créneau en question et on les transforme en timestamp UNIX
	if (getSettingValue("creneau_different") != 'n') {
		if (date("w") == getSettingValue("creneau_different")) {
			$req_sql = mysqli_query($GLOBALS["mysqli"], "SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux_bis WHERE id_definie_periode = '".$creneau_id."'");
		} else {
			$req_sql = mysqli_query($GLOBALS["mysqli"], "SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$creneau_id."'");
		}
	}else {
		$req_sql = mysqli_query($GLOBALS["mysqli"], "SELECT heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux WHERE id_definie_periode = '".$creneau_id."'");
	}
		$rep_sql = mysqli_fetch_array($req_sql);
		$heuredeb = explode(":", $rep_sql["heuredebut_definie_periode"]);
		$heurefin = explode(":", $rep_sql["heurefin_definie_periode"]);

		$ts_heuredeb = mktime($heuredeb[0], $heuredeb[1], 0, date("m"), date("d"), date("Y"));
		$ts_heurefin = mktime($heurefin[0], $heurefin[1], 0, date("m"), date("d"), date("Y"));

		// On teste si l'élève était absent ou en retard le cours du créneau (on ne teste que le début du créneau)
		//$req = mysql_query("SELECT id, retard_absence FROM absences_rb WHERE
		//		eleve_id = '".$eleve_id."' AND
		//		debut_ts = '".$ts_heuredeb."'");
		$req = mysqli_query($GLOBALS["mysqli"], "SELECT id, retard_absence FROM absences_rb WHERE
								eleve_id = '".$eleve_id."'
								AND retard_absence = 'A'
								AND (debut_ts <= '".$ts_heuredeb."'
								AND fin_ts >= '".$ts_heurefin."')");
		$rep = mysqli_fetch_array($req);
			// S'il est marqué absent A -> fond rouge
		if ($rep["retard_absence"] == "A") {
			return " class=\"td_Absence\">A";
		//}
			// S'il est marqué en retard R -> fond vert
			//else if ($rep["retard_absence"] == "R") {
		}
		else{
			$req = mysqli_query($GLOBALS["mysqli"], "SELECT id, retard_absence FROM absences_rb WHERE
				eleve_id = '".$eleve_id."'
				AND retard_absence = 'R'
				AND debut_ts = '".$ts_heuredeb."'");
			$rep = mysqli_fetch_array($req);
			if ($rep["retard_absence"] == "R") {
				return " class=\"td_Retard\">R";
			}else{
				return ">";
			}
		}
	}

//================ Début du rajout des fonctions du jour différent =============
function periode_actuel_jourdifferent($heure_choix) {
	// fonction permettant de savoir dans quelle période nous nous trouvons
	if($heure_choix == "") {
		$heure_choix = date('H:i:s');
	}
	$num_periode = "";
      //on liste dans un tableau les périodes existantes
	$requete_periode = ('SELECT * FROM edt_creneaux_bis WHERE
					heuredebut_definie_periode <= "'.$heure_choix .'" AND
					heurefin_definie_periode >= "'.$heure_choix.'"
						ORDER BY heuredebut_definie_periode, nom_definie_periode ASC');
	$resultat_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode)
					or die('Erreur SQL !'.$requete_periode.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($data_periode = mysqli_fetch_array($resultat_periode)) {
		$debut = $data_periode['heuredebut_definie_periode'];
		$num_periode = $data_periode['id_definie_periode'];
	}
	return($num_periode);
}

//connaitre l'heure du début soit de la fin d'une période
// ex: periode_heure($id_periode) > [0]11:00:00 [1]11:55:00
function periode_heure_jourdifferent($periode){
	if ($periode == "") {
		return "";
	}
	$debut = '';
	$fin = '';
	// on recherche les informations sur la périodes sélectionné
	$requete_periode = ('SELECT * FROM edt_creneaux_bis WHERE id_definie_periode = "'.$periode.'"');
	$resultat_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode)
						or die('Erreur SQL !'.$requete_periode.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($data_periode = mysqli_fetch_array($resultat_periode)) {
		$debut = $data_periode['heuredebut_definie_periode'];
		$fin = $data_periode['heurefin_definie_periode'];
	}
	return array('debut'=> $debut, 'fin'=>$fin);
}

?>
