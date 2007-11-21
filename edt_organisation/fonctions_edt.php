<?php

/**
 * Fonctions pour l'EdT
 *
 * @version $Id$
 * @copyright 2007
 */


// Fonction qui renvoie le nombre de lignes du tableau EdT

function nbre_lignes_tab_edt(){
	$compter_lignes = mysql_query("SELECT nom_definie_periode FROM absences_creneaux");
	$nbre_lignes = (mysql_num_rows($compter_lignes)) + 1;
	return $nbre_lignes;
}


// Fonction qui renvoie le nombre de colonnes du tableau EdT

function nbre_colonnes_tab_edt(){
	//global $compter_colonnes;
	$compter_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement");
	$nbre_colonnes = (mysql_num_rows($compter_colonnes)) + 1;
	return $nbre_colonnes;
}


// Fonction qui renvoie la liste des créneaux (M1, M2, M3, ...) dans l'ordre de la journée

function retourne_creneaux(){

	$req_nom_creneaux_r = mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");

	$rep_creneaux=array();
	while($data_creneaux=mysql_fetch_array($req_nom_creneaux_r)) {
		$rep_creneaux[] = $data_creneaux["nom_definie_periode"];
	}
	return $rep_creneaux;
}

// Fonction qui retourne la liste des horaires 08h00 - 09h00 au lieu des M1 M2 et Cie

function retourne_horaire(){

	$req_nom_horaire = mysql_query("SELECT heuredebut_definie_periode, heurefin_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode");

	$num_nom_horaire = mysql_num_rows($req_nom_horaire);

	$horaire = array();
	for($i=0; $i<$num_nom_horaire; $i++) {
		$horaire1[$i]["heure_debut"] = mysql_result($req_nom_horaire, $i, "heuredebut_definie_periode");
		$exp_hor = explode(":", $horaire1[$i]["heure_debut"]);
		$horaire[$i]["heure_debut"] = $exp_hor[0].":".$exp_hor[1]; // On enlève les secondes
		$horaire1[$i]["heure_fin"] = mysql_result($req_nom_horaire, $i, "heurefin_definie_periode");
		$exp_hor = explode(":", $horaire1[$i]["heure_fin"]);
		$horaire[$i]["heure_fin"] = $exp_hor[0].":".$exp_hor[1];
	}
	return $horaire;
}


// Fonction qui renvoie la liste des id_creneaux dans l'ordre de la journée

function retourne_id_creneaux(){

	$req_id_creneaux = mysql_query("SELECT id_definie_periode FROM absences_creneaux WHERE type_creneaux != 'pause' ORDER BY heuredebut_definie_periode") or die('Erreur : retourne_id_creneaux 1');

	$rep_id_creneaux=array();
	while($data_id_creneaux=mysql_fetch_array($req_id_creneaux)) {
		$rep_id_creneaux[] = $data_id_creneaux["id_definie_periode"];
	}
	return $rep_id_creneaux;
}


// Fonction qui renvoie un tableau des réglages de la table edt-setting

function retourne_setting_edt($reglage_edt){

	$req_edt_set = mysql_query("SELECT valeur FROM edt_setting WHERE reglage ='".$reglage_edt."'");
	$rep_edt_set = mysql_fetch_array($req_edt_set);

	$setting_edt = $rep_edt_set["valeur"];

	return $setting_edt;
}


// Fonction qui renvoie les id_groupe à un horaire donné (jour_semaine id_definie_periode)

function retourne_ens($jour_semaine, $id_creneaux){

	{
	$req_nom_creneaux = mysql_query("SELECT nom_definie_periode FROM absences_creneaux WHERE id_definie_periode ='".$id_creneaux."'");
	$rep_nom_creneaux = mysql_fetch_array($req_nom_creneaux);
	// On récupère tous les enseignements de l'horaire
	$req_ens = mysql_query("SELECT id_groupe FROM edt_cours WHERE id_definie_periode='".$id_creneaux."' && jour_semaine ='".$jour_semaine."'");

	$result_ens = array();
	while($rep_ens = mysql_fetch_array($req_ens)) {
		$result_ens[] = $rep_ens;
	}
	return $result_ens;
	}
}


// Fonction qui renvoie les enseignements d'un professeur (id_groupe)

function enseignements_prof($login_prof, $rep){

	$req = mysql_query("SELECT id_groupe FROM j_groupes_professeurs WHERE login ='".$login_prof."'");
	$enseignements_prof_num = mysql_num_rows($req);

	if ($rep === 1) {
		// on renvoie alors le nombre d'enseignements
		return $enseignements_prof_num;

	}
	else {
		$result = array();
		while($enseignements_prof = mysql_fetch_array($req)) {
		// on renvoie alors la liste des enseignements
		$result[] = $enseignements_prof;
		}
		return $result;
	}
}

// Fonction générale qui renvoie un tableau des enseignements à un horaire donné (heure et jour)

function cree_tab_general($login_general, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec){
		$tab_ens = array();
	if ($type_edt == "prof") {
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours, j_groupes_professeurs WHERE edt_cours.jour_semaine='".$jour_semaine."' AND edt_cours.id_definie_periode='".$id_creneaux."' AND edt_cours.id_groupe=j_groupes_professeurs.id_groupe AND login='".$login_general."' AND edt_cours.heuredeb_dec = '".$heuredeb_dec."'") or die('Erreur : cree_tab_general(prof) !');
		// On cherche les AID du créneau
		$req_aid_horaire = mysql_query("SELECT * FROM edt_cours WHERE jour_semaine = '".$jour_semaine."' AND id_definie_periode='".$id_creneaux."' AND heuredeb_dec = '".$heuredeb_dec."' AND id_groupe LIKE 'AID%'");
		$nbre_reponse = mysql_num_rows($req_aid_horaire);
		for($i=0; $i<$nbre_reponse; $i++) {
			$rep[$i]["id_groupe"] = mysql_result($req_aid_horaire, $i, "id_groupe");
			$test = explode("|", $rep[$i]["id_groupe"]);
			// On vérifie si le prof fait partie de l'AID ou pas
			$req_prof = mysql_num_rows(mysql_query("SELECT indice_aid FROM j_aid_utilisateurs WHERE id_utilisateur = '".$login_general."' AND id_aid = '".$test[1]."'"));
			if ($req_prof == "1") {
				$tab_ens[] = $rep[$i]["id_groupe"];
			}
		}
	}
	elseif ($type_edt == "classe") {
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours, j_groupes_classes WHERE edt_cours.jour_semaine='".$jour_semaine."' AND edt_cours.id_definie_periode='".$id_creneaux."' AND edt_cours.id_groupe=j_groupes_classes.id_groupe AND id_classe='".$login_general."' AND edt_cours.heuredeb_dec = '".$heuredeb_dec."'") or die('Erreur : cree_tab_general(classe) !');
	}
	elseif ($type_edt == "eleve"){
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours, j_eleves_groupes WHERE edt_cours.jour_semaine='".$jour_semaine."' AND edt_cours.id_definie_periode='".$id_creneaux."' AND edt_cours.id_groupe=j_eleves_groupes.id_groupe AND login='".$login_general."' AND edt_cours.heuredeb_dec = '".$heuredeb_dec."'") or die('Erreur : cree_tab_general(eleve) !');
	}
	elseif ($type_edt == "salle") {
		$req_ens_horaire = mysql_query("SELECT * FROM edt_cours WHERE edt_cours.jour_semaine='".$jour_semaine."' AND edt_cours.id_definie_periode='".$id_creneaux."' AND edt_cours.id_salle='".$login_general."' AND edt_cours.heuredeb_dec = '".$heuredeb_dec."'") or die('Erreur : cree_tab_general(salle) !');
	} else {
		$req_ens_horaire = "";
	}

	while($data_rep_ens = mysql_fetch_array($req_ens_horaire)) {
		$tab_ens[] = $data_rep_ens["id_groupe"];
	}
	return $tab_ens;
}


// Fonction qui renvoie la duree d'un enseignement à un créneau et un jour donné pour renseigner le rollspan

function renvoie_duree($id_creneaux, $jour_semaine, $enseignement){
	$req_duree = mysql_query("SELECT duree FROM edt_cours WHERE jour_semaine = '".$jour_semaine."' AND id_definie_periode = '".$id_creneaux."' AND id_groupe = '".$enseignement."'");
	$rep_duree = mysql_fetch_array($req_duree);
	$reponse_duree = $rep_duree["duree"];

	if ($reponse_duree == 2) {
		$duree = "2";
	}
	elseif ($reponse_duree == 3) {
		$duree = "3";
	}
	elseif ($reponse_duree == 4) {
		$duree = "4";
	}
	elseif ($reponse_duree == 5) {
		$duree = "5";
	}
	elseif ($reponse_duree == 6) {
		$duree = "6";
	}
	elseif ($reponse_duree == 7) {
		$duree = "7";
	}
	elseif ($reponse_duree == 8) {
		$duree = "8";
	}
	elseif ($reponse_duree === 0 OR $reponse_duree == 0) {
		$duree = "n";
	}
	else $duree = "2";

	return $duree;
}

// Fonction qui renvoie l'heure de début d'un cours
function renvoie_heuredeb($id_creneaux, $jour_semaine, $enseignement){
	$req_heuredeb = mysql_query("SELECT heuredeb_dec FROM edt_cours WHERE jour_semaine = '".$jour_semaine."' AND id_definie_periode = '".$id_creneaux."' AND id_groupe = '".$enseignement."'");
	$rep_heuredeb = mysql_fetch_array($req_heuredeb);
	$reponse_heuredeb = $rep_heuredeb["heuredeb_dec"];
		// Heure debut = 0 (debut créneau) ou 0.5 (milieu créneau)
	return $reponse_heuredeb;
}

// Fonction qui associe la classe, le professeur, et la matière liés à un enseignement à une heure et un jour donné
// Le tout formaté pour s'afficher dans une case du tableau EdT

function contenu_enseignement($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec){

	// CHercher la durée de l'enseignement qui précède celui qu'on veut afficher
			// Nouvel essai de technique
			$aff_rien = "non";
			$cours_precedent = "non";
	$cherche_creneaux = array();
	$cherche_creneaux = retourne_id_creneaux();
	$ch_index = array_search($id_creneaux, $cherche_creneaux);
		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 2) {
					$aff_rien = "oui";
				}
				// Cas où un cours se termine au milieu d'un créneau
				if ($aff_precedent == 2 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "1heure";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-2])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-2], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-2], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 4) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 4 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "2heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-3])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-3], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-3], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 6) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 6 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "3heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

		if (isset($cherche_creneaux[$ch_index-4])) {
			$ens_precedent = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-4], $jour_semaine, $type_edt, $heuredeb_dec);
			if (isset($ens_precedent[0])) {
				$aff_precedent = renvoie_duree($cherche_creneaux[$ch_index-4], $jour_semaine, $ens_precedent[0]);
				$nbre_ens_precedent = count($ens_precedent);
				if ($aff_precedent > 8) {
					$aff_rien = "oui";
				}
				if ($aff_precedent == 8 AND $heuredeb_dec == "0.5") {
					$cours_precedent = "4heures";
				}
			}
			else $aff_precedent = NULL;
		}
		else $aff_precedent = NULL;

	// On fait la même opération en inversant le $heuredeb_dec
				$aff_rien_dec = "non";
		if ($heuredeb_dec == "0") {
			$heuredeb_cherch = "0.5";
		}
		else if ($heuredeb_dec == "0.5") {
			$heuredeb_cherch = "0";
		}
		else $heuredeb_cherch = NULL;

		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 1) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 3) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 3) {
					$cours_precedent = "1heuredemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

		if (isset($cherche_creneaux[$ch_index-2])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-2], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-2], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 3) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 5) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 5) {
					$cours_precedent = "2heuresdemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

		if (isset($cherche_creneaux[$ch_index-3])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-3], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-3], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 5) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 7) {
					$aff_rien_dec = "oui";
				}
				elseif ($heuredeb_cherch == "0" AND $aff_precedent_dec == 7) {
					$cours_precedent = "3heuresdemi";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;
		if (isset($cherche_creneaux[$ch_index-4])) {
			$ens_precedent_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-4], $jour_semaine, $type_edt, $heuredeb_cherch);
			if (isset($ens_precedent_dec[0])) {
				$aff_precedent_dec = renvoie_duree($cherche_creneaux[$ch_index-4], $jour_semaine, $ens_precedent_dec[0]);
				if ($heuredeb_cherch == "0.5" AND $aff_precedent_dec > 7) {
					$aff_rien_dec = "oui";
				}
				else if ($heuredeb_cherch == "0" AND $aff_precedent_dec > 9) {
					$aff_rien_dec = "oui";
				}
			}
			else $aff_precedent_dec = NULL;
		}
		else $aff_precedent_dec = NULL;

	// alors on vérifie le cours en heuredeb_dec = 0.5
	// Normalement ces lignes ne servent plus à rien
	if ($heuredeb_dec == "0") {
		$ens_tab_cheval = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, "0.5");
			$nbre_tab_cheval = count($ens_tab_cheval);
		if (isset($ens_tab_cheval[0]) AND $nbre_tab_cheval != 0) {
			$duree_tab_cheval = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab_cheval[0]);
		}
		else $duree_tab_cheval = NULL;
		if (isset($cherche_creneaux[$ch_index-1]) AND isset($ens_tab_cheval[0])) {
			$duree_tab_pre_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_tab_cheval[0]);
			if ($duree_tab_pre_dec > 2) {
				$aff_rien = "oui";
			} else $aff_rien = "non";
		}
	}
	else $ens_tab_cheval = NULL;

	// On vérifie les cours en heuredeb_dec = 0
	// Normalement ces lignes ne servent plus à rien aussi

	if ($heuredeb_dec == "0.5") {
		$ens_tab_0 = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, "0");
			$nbre_tab_0 = count($ens_tab_0);
		if (isset($ens_tab_0[0]) AND $nbre_tab_0 != 0) {
			$duree_tab_0 = renvoie_duree($id_creneaux, $jour_semaine, $ens_tab_0[0]);
		}
		else $duree_tab_0 = NULL;
	// On vérifie quand même le cours précédent de 3 cellules et sa duree
		if (isset($cherche_creneaux[$ch_index-1])) {
			$ens_tab_pre_dec = cree_tab_general($req_type_login, $cherche_creneaux[$ch_index-1], $jour_semaine, $type_edt, "0");
			$nbre_tab_pre_dec = count($ens_tab_pre_dec);
			if (isset($ens_tab_pre_dec[0]) AND $nbre_tab_pre_dec != 0) {
				$duree_tab_pre_dec = renvoie_duree($cherche_creneaux[$ch_index-1], $jour_semaine, $ens_tab_pre_dec[0]);
				if ($duree_tab_pre_dec > 2) {
					$aff_rien = "oui";
				}
			}
		}
		else $duree_tab_pre_dec = NULL;
	}
	else $ens_tab_0 = NULL;

	// Chercher l'enseignement à afficher
	$ens_tab = cree_tab_general($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $heuredeb_dec);
		if ($type_edt == "eleve") {
				$nbre_ens_1 = count($ens_tab);
			if ($nbre_ens_1 === 0) {
				$nbre_ens = 0;
			} // 3 étant le nombre de périodes (il faudra changer cela)
			else {
				$nbre_ens = ($nbre_ens_1/3);
			}
		} else {
			$nbre_ens = count($ens_tab);
		}


//debuggage intensif (à enlever en prod)
if (isset($ens_tab_cheval[0])) {
	 $aff1 = $ens_tab_cheval[0]."etc ";
} else $aff1 = "Netc ";
if (isset($duree_tab_cheval)) {
	 $aff2 = $duree_tab_cheval."dtc ";
}else $aff2 = "Ndtc ";
if (isset($aff_precedent)) {
	$aff3 = $aff_precedent."ap ";
}else $aff3 = "Nap ";
if (isset($ens_precedent[0])) {
	$aff4 = $ens_precedent[0]."ep ";
}else $aff4 = "Nep ";
if (isset($nbre_ens_precedent)) {
	$aff4b = $nbre_ens_precedent."nep";
}else $aff4b = "Nnep";
if (isset($duree_tab_pre_dec)) {
	$aff5 = $duree_tab_pre_dec."dtpc ";
}else $aff5 = "Ndtpc ";
if (isset($nbre_ens)) {
	$aff6 = $nbre_ens."ne ";
}else $aff6 = "Nne ";

	 // La solution pour le cas où il y a plus de trois réponses est au point.

		if ($nbre_ens === 0) {
			if ($cours_precedent == "1heure" OR $cours_precedent == "2heures" OR $cours_precedent == "3heures" OR $cours_precedent == "4heures") {
				$case_tab = "<td height=\"35\">-<!--raf1 ".$aff1.$aff2.$aff3.$aff4.$aff5.$aff6."--></td>";
			}
			elseif ($cours_precedent == "1heuredemi" OR $cours_precedent == "2heuresdemi" OR $cours_precedent == "3heuresdemi") {
				$case_tab = "<td height=\"35\">-<!--raf1 ".$aff1.$aff2.$aff3.$aff4.$aff5.$aff6."--></td>";
			}
			elseif ($heuredeb_dec == "0.5" AND isset($duree_tab_pre_dec) AND $duree_tab_pre_dec == 3) {
				$case_tab = "<td height=\"35\">-<!--raf1 ".$aff1.$aff2.$aff3.$aff4.$aff5.$aff6."--></td>";
			}
			elseif ($heuredeb_dec == "0.5") {
				$case_tab = "<!--rien1 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
				// Cas d'un demi creneau en heuredeb_dec = 0 qui est vide
			elseif ($heuredeb_dec == "0" AND isset($ens_tab_cheval[0]) AND $duree_tab_cheval != "n" AND isset($aff_precedent) AND $aff_precedent != 3) {
				$case_tab = "<!--raf2 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
			elseif (isset($aff_rien) AND $aff_rien == "oui") {
				$case_tab = "<!--rien2b ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
			else if (isset($aff_rien_dec) AND $aff_rien_dec == "oui") {
				$case_tab = "<!--rien2c ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
			elseif ($heuredeb_dec == "0" AND isset($ens_tab_cheval[0]) AND $duree_tab_cheval != "n" AND (!$aff_precedent OR (isset($aff_precedent) AND $aff_precedent != 3))) {
				$case_tab = "<td height=\"35\">-<!--AFF2 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."--></td>";
			}
			//elseif ((isset($aff_precedent)) AND ($aff_precedent == 3 OR $aff_precedent == 4 OR $aff_precedent == 5 OR $aff_precedent == 6)) {
			//	$case_tab = "<!--rien2 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			//}
			else $case_tab = "<td rowspan=\"2\">-<!--raf3 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."--></td>\n";
		}
		elseif ($nbre_ens === 1) {
			//enseignement = $ens_tab[0]
			if (renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0]) == "n") {
				$case_tab = "<!--rien3 ".$aff1.$aff2.$aff3.$aff4.$aff4b.$aff5.$aff6."-->";
			}
			else $case_tab = "<td rowspan=\"".renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0])."\" style=\"background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0])."</td>\n";
		}
		elseif ($nbre_ens == 2) {
			$case_1_tab = "<td rowspan=\"2\"><table class=\"tab_edt_1\" BORDER=\"0\" CELLSPACING=\"0\"><tbody>\n";
			$case_2_tab = ("<tr>\n<td style=\"font-size: 10px; background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0])."</td>\n<td style=\"font-size: 10px; background-color: ".couleurCellule($ens_tab[1]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[1])."</td>\n</tr>\n");
			$case_3_tab = "</tbody>\n</table>\n</td>";

			$case_tab = $case_1_tab.$case_2_tab.$case_3_tab;
		}
		elseif ($nbre_ens == 3) {
			$case_1_tab = "<td rowspan=\"2\"><table class=\"tab_edt_1\" BORDER=\"0\" CELLSPACING=\"0\"><tbody>\n";
			$case_2_tab = ("<tr>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[0]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[0])."</td>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[1]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[1])."</td>\n<td style=\"font-size: 8px; background-color: ".couleurCellule($ens_tab[2]).";\">".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[2])."</td>\n</tr>\n");
			$case_3_tab = "</tbody>\n</table>\n</td>";

			$case_tab = $case_1_tab.$case_2_tab.$case_3_tab;
		}
		elseif ($nbre_ens > 3) {
				// On met la liste des enseignements dans $contenu de l'infobulle
				$contenu = "";
				for($z=0; $z<$nbre_ens; $z++) {
					$contenu .= "<p>".contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $ens_tab[$z])."</p>";
				}
			$id_div = "ens_".$id_creneaux."_".$jour_semaine;
			$case_tab = "<td rowspan=\"".renvoie_duree($id_creneaux, $jour_semaine, $ens_tab[0])."\"><a href='#' onClick=\"afficher_div('".$id_div."','Y',10,10);return false;\">VOIR</a>".creer_div_infobulle($id_div, "Liste des enseignements", "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n")."</td>\n";
		}
		else {
		// AJOUT: boireaus
		$case_tab=NULL;
		}

	return $case_tab;
}


// Fonction qui construit le contenu d'un créneaux.

function contenu_creneaux($req_type_login, $id_creneaux, $jour_semaine, $type_edt, $enseignement){
	// On récupère l'id
	$effacer_cours = "";
	$req_recup_id = mysql_fetch_array(mysql_query("SELECT id_cours FROM edt_cours WHERE id_groupe = '".$enseignement."' AND jour_semaine = '".$jour_semaine."' AND id_definie_periode = '".$id_creneaux."'"));

		// Seul l'admin peut effacer ce cours
		if ($_SESSION["statut"] == "administrateur") {
			$effacer_cours = '
					<a href="./effacer_cours.php?supprimer_cours='.$req_recup_id["id_cours"].'&amp;type_edt='.$type_edt.'&amp;identite='.$req_type_login.'" onClick="return confirm(\'Confirmez-vous cette suppression ?\')">
					<img src="../images/icons/delete.png" title="Effacer" alt="Effacer" /></a>
					';
		}
		else {
			$effacer_cours = "";
		}
		// Seuls l'admin et la scolarité peuvent modifier un cours (sauf si admin n'a pas autorisé scolarite)
		if (($_SESSION["statut"] == "scolarite" AND GetSettingEdt('scolarite_modif_cours') == "y") OR $_SESSION["statut"] == "administrateur") {
			$modifier_cours = '
					<a href=\'javascript:centrerpopup("modifier_cours_popup.php?id_cours='.$req_recup_id["id_cours"].'&amp;type_edt='.$type_edt.'&amp;identite='.$req_type_login.'",700,280,"scrollbars=no,statusbar=no,resizable=no,menubar=no,toolbar=no,status=no")\'>
					<img src="../images/edit16.png" title="Modifier" alt="Modifier" /></a>
						';
		}
		else {
			$modifier_cours = "";
		}

	// On vérifie si $enseignement est ou pas pas un AID
	$analyse = explode("|", $enseignement);
if ($analyse[0] == "AID") {
	//echo "c'est un AID";
	$req_nom_aid = mysql_query("SELECT nom, indice_aid FROM aid WHERE id = '".$analyse[1]."'");
	$rep_nom_aid = mysql_fetch_array($req_nom_aid);

	// On récupère le nom de l'aid
	$req_nom_complet = mysql_query("SELECT nom FROM aid_config WHERE indice_aid = '".$rep_nom_aid["indice_aid"]."'");
	$rep_nom_complet = mysql_fetch_array($req_nom_complet);
	$aff_matiere = $rep_nom_complet["nom"];

		$contenu="";

	// On compte les élèves de l'aid $aff_nbre_eleve
	$req_nbre_eleves = mysql_query("SELECT login FROM j_aid_eleves WHERE id_aid = '".$analyse[1]."' ORDER BY login");
	$aff_nbre_eleve = mysql_num_rows($req_nbre_eleves);
		for($a=0; $a < $aff_nbre_eleve; $a++) {
			$rep_eleves[$a]["login"] = mysql_result($req_nbre_eleves, $a, "login");
			$noms = mysql_fetch_array(mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$rep_eleves[$a]["login"]."'"));
			$contenu .= $noms["nom"]." ".$noms["prenom"]."<br />";
		}
		$titre_listeleve = "Liste des élèves (".$aff_nbre_eleve.")";
		$id_div = $jour_semaine.$rep_nom_aid["nom"].$id_creneaux;
	$classe_js = "<a href=\"#\" onClick=\"afficher_div('".$id_div."','Y',10,10);return false;\">".$rep_nom_aid["nom"]."</a>
			".creer_div_infobulle($id_div, $titre_listeleve, "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n");
	// On dresse la liste des noms de prof
	$rep_nom_prof['civilite'] = "";
	$rep_nom_prof['nom'] = "Cours en groupe";

}else {

	// on récupère le nom court de la classe en question
	{
	$req_id_classe = mysql_query("SELECT id_classe FROM j_groupes_classes WHERE id_groupe ='".$enseignement."'");
	$rep_id_classe = mysql_fetch_array($req_id_classe);
	$req_classe = mysql_query("SELECT classe FROM classes WHERE id ='".$rep_id_classe['id_classe']."'");
	$rep_classe = mysql_fetch_array($req_classe);
	}
	// On compte le nombre d'élèves
	{
	$req_compter_eleves = mysql_query("SELECT COUNT(*) FROM j_eleves_groupes WHERE periode = 1 AND id_groupe ='".$enseignement."'");
	$rep_compter_eleves = mysql_fetch_array($req_compter_eleves);
	$aff_nbre_eleve = $rep_compter_eleves[0];
	}
	// On récupère la liste des élèves de l'enseignement

	if (($type_edt == "prof") OR ($type_edt == "salle")) {
	$current_group = get_group($enseignement);

		$contenu="";
			// 1 étant le numéro de la période
		foreach ($current_group["eleves"][1]["users"] as $eleve_login) {
			$contenu .=$eleve_login['nom']." ".$eleve_login['prenom']."<br />";
		}
		$titre_listeleve = "Liste des élèves (".$aff_nbre_eleve.")";

	//$classe_js = aff_popup($rep_classe['classe'], "edt", $titre_listeleve, $contenu);
		$id_div = $jour_semaine.$rep_classe['classe'].$id_creneaux;
	$classe_js = "<a href=\"#\" onClick=\"afficher_div('".$id_div."','Y',10,10);return false;\">".$rep_classe['classe']."</a>
			".creer_div_infobulle($id_div, $titre_listeleve, "#330033", $contenu, "#FFFFFF", 20,0,"y","y","n","n");
	}
	// On récupère le nom et la civilite du prof en question
	{
	$req_login_prof = mysql_query("SELECT login FROM j_groupes_professeurs WHERE id_groupe ='".$enseignement."'");
	$rep_login_prof = mysql_fetch_array($req_login_prof);
	$req_nom_prof = mysql_query("SELECT nom, civilite FROM utilisateurs WHERE login ='".$rep_login_prof['login']."'");
	$rep_nom_prof = mysql_fetch_array($req_nom_prof);
	}
	// On récupère le nom de l'enseignement en question (en fonction du paramètre long ou court)
	{
	$req_groupe = mysql_query("SELECT description FROM groupes WHERE id ='".$enseignement."'");
	$rep_matiere = mysql_fetch_array($req_groupe);
	if (GetSettingEdt("edt_aff_matiere") == "long") {
		// SI c'est l'admin, il faut réduire la taille de la police de caractères
		//if ($_SESSION["statut"] == "administrateur") {
			//$aff_matiere = "<span class=\"edt_admin\">".$rep_matiere['description']."</span>";
		//}
		//else
		$aff_matiere = $rep_matiere['description'];
	}
	else {
		$req_2_matiere = mysql_query("SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe ='".$enseignement."'");
		$rep_2_matiere = mysql_fetch_array($req_2_matiere);
		$aff_matiere = $rep_2_matiere['id_matiere'];
	}
	}

} // fin du else après les aid

	// On récupère le type de semaine si besoin
	$req_sem = mysql_query("SELECT id_semaine FROM edt_cours WHERE id_cours ='".$req_recup_id["id_cours"]."'");
	$rep_sem = mysql_fetch_array($req_sem);
		if ($rep_sem["id_semaine"] == "0") {
			$aff_sem = '';
		}
		else $aff_sem = '<font color="#663333"> - Sem.'.$rep_sem["id_semaine"].'</font>';

	// On récupère le nom complet de la salle en question
	{
	if (GetSettingEdt("edt_aff_salle") == "nom") {
		$salle_aff = "nom_salle";
	}
	else $salle_aff = "numero_salle";
	$req_id_salle = mysql_query("SELECT id_salle FROM edt_cours WHERE id_groupe ='".$enseignement."' AND id_definie_periode ='".$id_creneaux."' AND jour_semaine ='".$jour_semaine."'");
	$rep_id_salle = mysql_fetch_array($req_id_salle);
	$req_salle = mysql_query("SELECT ".$salle_aff." FROM salle_cours WHERE id_salle ='".$rep_id_salle['id_salle']."'");
	$tab_rep_salle = mysql_fetch_array($req_salle);
	$rep_salle = $tab_rep_salle[0];
	}


	if ($type_edt == "prof")
		return ("".$aff_matiere."<br />\n".$classe_js." ".$effacer_cours." ".$modifier_cours." \n".$aff_sem."<br />\n<i>".$rep_salle."</i> - ".$aff_nbre_eleve." él.\n");
	elseif (($type_edt == "classe") OR ($type_edt == "eleve"))
		return ("".$aff_matiere."<br />".$rep_nom_prof['civilite']." ".$rep_nom_prof['nom']."<br /><i>".$rep_salle."</i> ".$aff_sem."");
	elseif ($type_edt == "salle")
		return ("".$aff_matiere."<br />\n".$rep_nom_prof['civilite']." ".$rep_nom_prof['nom']." ".$aff_sem."<br />\n".$classe_js." - ".$aff_nbre_eleve." él.\n");
}


// Fonction qui construit la première ligne du tableau

function premiere_ligne_tab_edt(){

	echo("<table class=\"tab_edt\">\n");
	echo("<tbody>\n");
	echo("<tr>\n");
	echo("<th>Horaires</th>\n");

	$compter_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement");
while ($jour_semaine = mysql_fetch_array($compter_colonnes)) {

   printf("<th>".$jour_semaine["jour_horaire_etablissement"]."</th>\n");
   }
    echo("</tr>\n");
}


// Fonction qui construit chaque ligne du tableau

function construction_tab_edt($heure, $heuredeb_dec){

if ($_SESSION['statut'] == "eleve") {
	$req_type_login = $_SESSION['login'];
}
else $req_type_login = isset($_GET["login_edt"]) ? $_GET["login_edt"] : (isset($_POST["login_edt"]) ? $_POST["login_edt"] : NULL);

if ($_SESSION['statut'] == "eleve") {
		$type_edt = $_SESSION['statut'];
}
else $type_edt = isset($_GET["type_edt_2"]) ? $_GET["type_edt_2"] : (isset($_POST["type_edt_2"]) ? $_POST["type_edt_2"] : NULL);

	$compter_nbre_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement");
	$compter_colonnes = mysql_num_rows($compter_nbre_colonnes);

	$req_colonnes = mysql_query("SELECT jour_horaire_etablissement FROM horaires_etablissement");
		$jour_sem_tab=array();
		while($data_sem_tab=mysql_fetch_array($req_colonnes)) {
		$jour_sem_tab[]=$data_sem_tab["jour_horaire_etablissement"];
		}
	if ($compter_colonnes <= 4) {
	return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
	elseif ($compter_colonnes <= 5) {
	return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[4], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
	elseif ($compter_colonnes <= 6) {
	return "".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[0], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[1], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[2], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[3], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_sem_tab[4], $type_edt, $heuredeb_dec))."\n".(contenu_enseignement($req_type_login, $heure, $jour_semaine_tab[5], $type_edt, $heuredeb_dec))."\n</tr>\n";
	}
}


// Fonction qui renvoie la liste des professeurs, des classe ou des salles

function renvoie_liste($type) {

	$rep_liste = "";
	if ($type == "prof") {
		$req_liste = mysql_query("SELECT nom, prenom, login FROM utilisateurs WHERE etat ='actif' AND statut='professeur' ORDER BY nom");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_liste, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_liste, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_liste, $i, "login");
			}
	return $rep_liste;
	}
	if ($type == "classe") {
		$req_liste = mysql_query("SELECT id, classe FROM classes ORDER BY classe");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["id"] = mysql_result($req_liste, $i, "id");
			$rep_liste[$i]["classe"] = mysql_result($req_liste, $i, "classe");
			}
	return $rep_liste;
	}
	if ($type == "salle") {
		$req_liste = mysql_query("SELECT id_salle, numero_salle, nom_salle FROM salle_cours ORDER BY numero_salle");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["id_salle"] = mysql_result($req_liste, $i, "id_salle");
			$rep_liste[$i]["numero_salle"] = mysql_result($req_liste, $i, "numero_salle");
			$rep_liste[$i]["nom_salle"] = mysql_result($req_liste, $i, "nom_salle");
			}
	return $rep_liste;
	}
	if ($type == "eleve") {
		$req_liste = mysql_query("SELECT nom, prenom, login FROM eleves GROUP BY nom");

		$nb_liste = mysql_num_rows($req_liste);

	$tab_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_liste, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_liste, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_liste, $i, "login");
			}
	return $rep_liste;
	}
}

// Fonction qui retourne le nom long et court de la classe d'un élève

function aff_nom_classe($log_eleve) {
	$req_id_classe = mysql_query("SELECT id_classe FROM j_eleves_classes WHERE login = '".$log_eleve."'");
	$rep_id_classe = mysql_fetch_array($req_id_classe);

	$req_nom_classe = mysql_query("SELECT classe, nom_complet FROM classes WHERE id ='".$rep_id_classe["id_classe"]."'");

	$rep_nom_classe1 = mysql_fetch_array($req_nom_classe);
	$rep_nom_classe = $rep_nom_classe1["classe"];

	return $rep_nom_classe;
}

// Fonction qui renvoie la liste des élèves dont le nom commence par la lettre $alpha

function renvoie_liste_a($type, $alpha){
	if ($type == "eleve") {
		$req_eleves_a = mysql_query("SELECT login, nom, prenom FROM eleves WHERE nom LIKE '$alpha%' ORDER BY nom");

		$nb_liste = mysql_num_rows($req_eleves_a);

	$rep_liste = array();

		for($i=0;$i<$nb_liste;$i++) {
			$rep_liste[$i]["nom"] = mysql_result($req_eleves_a, $i, "nom");
			$rep_liste[$i]["prenom"] = mysql_result($req_eleves_a, $i, "prenom");
			$rep_liste[$i]["login"] = mysql_result($req_eleves_a, $i, "login");
			}
	return $rep_liste;
	}
}

// Fonction qui renvoie la liste des élèves d'une classe

function renvoie_liste_classe($id_classe_post){
	$req_liste_login = mysql_query("SELECT login FROM j_eleves_classes WHERE id_classe = '".$id_classe_post."' AND periode = '1'") OR die ('Erreur : renvoie_liste_classe() : '.mysql_error().'.');
	$nb_eleves = mysql_num_rows($req_liste_login);

	$rep_liste_eleves = array();

		for($i=0; $i<$nb_eleves; $i++) {

			$rep_liste_eleves[$i]["login"] = mysql_result($req_liste_login, $i, "login");
		}
	return $rep_liste_eleves;
}

// Fonction qui renvoie le nom qui correspond à l'identifiant envoyé et au type (salle, prof, classe et élève)

function renvoie_nom_long($id, $type){
	{
	if ($type == "prof") {
		$req_nom_long = mysql_query("SELECT nom, prenom, civilite FROM utilisateurs WHERE login = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom');
    	$prenom = @mysql_result($req_nom_long, 0, 'prenom');
    	$civilite = @mysql_result($req_nom_long, 0, 'civilite');

    	$nom_long = $civilite." ".$nom." ".$prenom;
	}

	elseif ($type == "eleve") {
		$req_nom_long = mysql_query("SELECT nom, prenom FROM eleves WHERE login = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom');
    	$prenom = @mysql_result($req_nom_long, 0, 'prenom');

    	$nom_long = $prenom." ".$nom;
	}
	elseif ($type == "salle") {
		$req_nom_long = mysql_query("SELECT nom_salle FROM salle_cours WHERE id_salle = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom_salle');

    	$nom_long = 'la '.$nom;
	}
	elseif ($type == "classe") {
		$req_nom_long = mysql_query("SELECT nom_complet FROM classes WHERE id = '".$id."'");
		$nom = @mysql_result($req_nom_long, 0, 'nom_complet');

		$nom_long = 'la classe de '.$nom;
	}
	}
	return $nom_long;
}


// Fonction qui affiche toutes les salles sans enseignements à un horaire donné

function aff_salles_vides($id_creneaux, $id_jour_semaine){

	// tous les id de toutes les salles
	$req_liste_salle = mysql_query("SELECT id_salle FROM salle_cours");
		$tab_toutes = array();
		while($rep_toutes = mysql_fetch_array($req_liste_salle))
		{
		$tab_toutes[]=$rep_toutes["id_salle"];
		}
	// Tous les id des salles qui ont cours à id_creneaux et id_jour_semaine
	$req_liste_salle_c = mysql_query("SELECT id_salle FROM edt_cours WHERE id_definie_periode = '".$id_creneaux."' AND jour_semaine = '".$id_jour_semaine."'");
		$tab_utilisees = array();
		while($rep_utilisees = mysql_fetch_array($req_liste_salle_c))
		{
		$tab_utilisees[]=$rep_utilisees["id_salle"];
		}

	$result = array_diff($tab_toutes, $tab_utilisees);

	return $result;
}


// checked pour le paramétrage de l'EdT
function aff_checked($aff, $valeur){
	$req_aff = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$aff."'");
	$rep_aff = mysql_fetch_array($req_aff);

	if ($rep_aff['valeur'] === $valeur) {
		$retour_aff = ("checked='checked' ");
	}
	else {
		$retour_aff = ("");
	}
	return $retour_aff;
}

// retourne les settings de l'EdT
function GetSettingEdt($param_edt){
	$req_param_edt = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$param_edt."'");
	$rep_param_edt = mysql_fetch_array($req_param_edt);

	$retourne = $rep_param_edt["valeur"];

	return $retourne;
}

// Retourne le nom de la salle
function nom_salle($id_salle_r){
	$req_nom_salle = mysql_query("SELECT nom_salle FROM salle_cours WHERE id_salle = '".$id_salle_r."'");
	$reponse = mysql_fetch_array($req_nom_salle);
		$nom_salle_r = $reponse["nom_salle"];

	return $nom_salle_r;
}

// Fonction qui renvoie la couleur de fond de cellule pour une matière
function couleurCellule($enseignement){
	// On vérifie si on a affaire à une aid ou pas
	$verif_aid = explode("|", $enseignement);
	if ($verif_aid[0] == "AID") {
		return "none";
	} else {
		// Ce n'est pas une aid, on cherche donc la matière rattachée à cet enseignement
		$sql = mysql_query("SELECT id_matiere FROM j_groupes_matieres WHERE id_groupe = '".$enseignement."'");
		$req_matiere = mysql_fetch_array($sql);
		$matiere = "M_".$req_matiere["id_matiere"];

		// on cherche s'il existe un réglage pour cette matière
		$sql = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = '".$matiere."'");
		$nbre_reponse = mysql_num_rows($sql);
		// On construit la réponse en fonction de l'existence ou non de ce réglage
		if ($nbre_reponse == 0) {
			return "none";
		} else {
			// On vérifie que le réglage soit sur coul
			if (GetSettingEdt("edt_aff_couleur") == "coul") {
				$couleur = mysql_fetch_array($sql);
				return $couleur["valeur"];
			} else {
				return "none";
			}

		}
	}
}

?>