<?php
/*
 * $Id$
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
//$variables_non_protegees = 'yes';

// Begin standart header

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/param_bull_pdf_2016.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/bulletin/param_bull_pdf_2016.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Paramètres des bulletins PDF Réforme CLG 2016',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module bulletins non activé");
	die();
}

$reg_ok = 'yes';
$msg = '';

//debug_var();

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

if (isset($_POST['is_posted'])) {
	check_token();

	$bull2016_INE=isset($_POST["bull2016_INE"]) ? $_POST["bull2016_INE"] : "n";
	if(($bull2016_INE!="y")&&($bull2016_INE!="n")) {
		$bull2016_INE="n";
	}
	if (!saveSetting("bull2016_INE", $bull2016_INE)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_INE !";
		$reg_ok = 'no';
	}

	$bull2016_cadre_visa_famille=isset($_POST["bull2016_cadre_visa_famille"]) ? $_POST["bull2016_cadre_visa_famille"] : "n";
	if(($bull2016_cadre_visa_famille!="y")&&($bull2016_cadre_visa_famille!="n")) {
		$bull2016_cadre_visa_famille="n";
	}
	if (!saveSetting("bull2016_cadre_visa_famille", $bull2016_cadre_visa_famille)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_cadre_visa_famille !";
		$reg_ok = 'no';
	}

	$bull2016_afficher_cadre_adresse_resp=isset($_POST["bull2016_afficher_cadre_adresse_resp"]) ? $_POST["bull2016_afficher_cadre_adresse_resp"] : "n";
	if(($bull2016_afficher_cadre_adresse_resp!="y")&&($bull2016_afficher_cadre_adresse_resp!="n")) {
		$bull2016_afficher_cadre_adresse_resp="n";
	}
	if (!saveSetting("bull2016_afficher_cadre_adresse_resp", $bull2016_afficher_cadre_adresse_resp)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_afficher_cadre_adresse_resp !";
		$reg_ok = 'no';
	}

	// Paramètre désactivé
	//$bull2016_pas_espace_reserve_EPI_AP_Parcours=isset($_POST["bull2016_pas_espace_reserve_EPI_AP_Parcours"]) ? $_POST["bull2016_pas_espace_reserve_EPI_AP_Parcours"] : "n";
	$bull2016_pas_espace_reserve_EPI_AP_Parcours="y";
	if(($bull2016_pas_espace_reserve_EPI_AP_Parcours!="y")&&($bull2016_pas_espace_reserve_EPI_AP_Parcours!="n")) {
		$bull2016_pas_espace_reserve_EPI_AP_Parcours="n";
	}
	if (!saveSetting("bull2016_pas_espace_reserve_EPI_AP_Parcours", $bull2016_pas_espace_reserve_EPI_AP_Parcours)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_pas_espace_reserve_EPI_AP_Parcours !";
		$reg_ok = 'no';
	}

	$bull2016_autorise_sous_matiere=isset($_POST["bull2016_autorise_sous_matiere"]) ? $_POST["bull2016_autorise_sous_matiere"] : "n";
	if(($bull2016_autorise_sous_matiere!="y")&&($bull2016_autorise_sous_matiere!="n")) {
		$bull2016_autorise_sous_matiere="n";
	}
	if (!saveSetting("bull2016_autorise_sous_matiere", $bull2016_autorise_sous_matiere)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_autorise_sous_matiere !";
		$reg_ok = 'no';
	}

	$bull2016_arrondi=isset($_POST["bull2016_arrondi"]) ? $_POST["bull2016_arrondi"] : 0.01;
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_arrondi))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_arrondi)))||
	($bull2016_arrondi==0)||
	($bull2016_arrondi=="")) {
		$bull2016_arrondi=0.01;
	}
	if (!saveSetting("bull2016_arrondi", $bull2016_arrondi)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_arrondi !";
		$reg_ok = 'no';
	}

	$bull2016_nb_chiffre_virgule=isset($_POST["bull2016_nb_chiffre_virgule"]) ? $_POST["bull2016_nb_chiffre_virgule"] : 1;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_nb_chiffre_virgule))||
	($bull2016_nb_chiffre_virgule=="")) {
		$bull2016_nb_chiffre_virgule=1;
	}
	if (!saveSetting("bull2016_nb_chiffre_virgule", $bull2016_nb_chiffre_virgule)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_nb_chiffre_virgule !";
		$reg_ok = 'no';
	}

	$bull2016_chiffre_avec_zero=isset($_POST["bull2016_chiffre_avec_zero"]) ? $_POST["bull2016_chiffre_avec_zero"] : 0;
	if(($bull2016_chiffre_avec_zero!="0")&&($bull2016_chiffre_avec_zero!="1")) {
		$bull2016_chiffre_avec_zero=0;
	}
	if (!saveSetting("bull2016_chiffre_avec_zero", $bull2016_chiffre_avec_zero)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_chiffre_avec_zero !";
		$reg_ok = 'no';
	}

	$bull2016_evolution_moyenne_periode_precedente=isset($_POST["bull2016_evolution_moyenne_periode_precedente"]) ? $_POST["bull2016_evolution_moyenne_periode_precedente"] : "n";
	if(($bull2016_evolution_moyenne_periode_precedente!="y")&&($bull2016_evolution_moyenne_periode_precedente!="n")) {
		$bull2016_evolution_moyenne_periode_precedente="y";
	}
	if (!saveSetting("bull2016_evolution_moyenne_periode_precedente", $bull2016_evolution_moyenne_periode_precedente)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_evolution_moyenne_periode_precedente !";
		$reg_ok = 'no';
	}

	$bull2016_evolution_moyenne_periode_precedente_seuil=isset($_POST["bull2016_evolution_moyenne_periode_precedente_seuil"]) ? $_POST["bull2016_evolution_moyenne_periode_precedente_seuil"] : 0;
	if(((!preg_match("/^[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil))&&
	(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil)))||
	($bull2016_evolution_moyenne_periode_precedente_seuil=="")) {
		$bull2016_evolution_moyenne_periode_precedente_seuil=0;
	}
	if (!saveSetting("bull2016_evolution_moyenne_periode_precedente_seuil", $bull2016_evolution_moyenne_periode_precedente_seuil)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_evolution_moyenne_periode_precedente_seuil !";
		$reg_ok = 'no';
	}


	$bull2016_moyminclassemax=isset($_POST["bull2016_moyminclassemax"]) ? $_POST["bull2016_moyminclassemax"] : "n";
	if(($bull2016_moyminclassemax!="y")&&($bull2016_moyminclassemax!="n")) {
		$bull2016_affich_mentions="n";
	}
	if (!saveSetting("bull2016_moyminclassemax", $bull2016_moyminclassemax)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_moyminclassemax !";
		$reg_ok = 'no';
	}


	$bull2016_affich_mentions=isset($_POST["bull2016_affich_mentions"]) ? $_POST["bull2016_affich_mentions"] : "y";
	if(($bull2016_affich_mentions!="y")&&($bull2016_affich_mentions!="n")) {
		$bull2016_affich_mentions="y";
	}
	if (!saveSetting("bull2016_affich_mentions", $bull2016_affich_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_affich_mentions !";
		$reg_ok = 'no';
	}

	$bull2016_avec_coches_mentions=isset($_POST["bull2016_avec_coches_mentions"]) ? $_POST["bull2016_avec_coches_mentions"] : "n";
	if(($bull2016_avec_coches_mentions!="y")&&($bull2016_avec_coches_mentions!="n")) {
		$bull2016_avec_coches_mentions="y";
	}
	if (!saveSetting("bull2016_avec_coches_mentions", $bull2016_avec_coches_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_avec_coches_mentions !";
		$reg_ok = 'no';
	}

	$bull2016_intitule_mentions=isset($_POST["bull2016_intitule_mentions"]) ? $_POST["bull2016_intitule_mentions"] : "n";
	if(($bull2016_intitule_mentions!="y")&&($bull2016_intitule_mentions!="n")) {
		$bull2016_intitule_mentions="y";
	}
	if (!saveSetting("bull2016_intitule_mentions", $bull2016_intitule_mentions)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_intitule_mentions !";
		$reg_ok = 'no';
	}



	//$afficher_nb_heures_perdues="n";

	$bull2016_aff_abs_nj=isset($_POST["bull2016_aff_abs_nj"]) ? $_POST["bull2016_aff_abs_nj"] : "n";
	if(($bull2016_aff_abs_nj!="y")&&($bull2016_aff_abs_nj!="n")) {
		$bull2016_aff_abs_nj="y";
	}
	if (!saveSetting("bull2016_aff_abs_nj", $bull2016_aff_abs_nj)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_abs_nj !";
		$reg_ok = 'no';
	}

	$bull2016_aff_abs_justifiees=isset($_POST["bull2016_aff_abs_justifiees"]) ? $_POST["bull2016_aff_abs_justifiees"] : "n";
	if(($bull2016_aff_abs_justifiees!="y")&&($bull2016_aff_abs_justifiees!="n")) {
		$bull2016_aff_abs_justifiees="y";
	}
	if (!saveSetting("bull2016_aff_abs_justifiees", $bull2016_aff_abs_justifiees)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_abs_justifiees !";
		$reg_ok = 'no';
	}

	$bull2016_aff_total_abs=isset($_POST["bull2016_aff_total_abs"]) ? $_POST["bull2016_aff_total_abs"] : "n";
	if(($bull2016_aff_total_abs!="y")&&($bull2016_aff_total_abs!="n")) {
		$bull2016_aff_total_abs="y";
	}
	if (!saveSetting("bull2016_aff_total_abs", $bull2016_aff_total_abs)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_total_abs !";
		$reg_ok = 'no';
	}

	$bull2016_aff_retards=isset($_POST["bull2016_aff_retards"]) ? $_POST["bull2016_aff_retards"] : "n";
	if(($bull2016_aff_retards!="y")&&($bull2016_aff_retards!="n")) {
		$bull2016_aff_retards="y";
	}
	if (!saveSetting("bull2016_aff_retards", $bull2016_aff_retards)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_aff_retards !";
		$reg_ok = 'no';
	}




	$bull2016_voeux_orientation=isset($_POST["bull2016_voeux_orientation"]) ? $_POST["bull2016_voeux_orientation"] : "n";
	if(($bull2016_voeux_orientation!="y")&&($bull2016_voeux_orientation!="n")) {
		$bull2016_voeux_orientation="y";
	}
	if (!saveSetting("bull2016_voeux_orientation", $bull2016_voeux_orientation)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_voeux_orientation !";
		$reg_ok = 'no';
	}

	$bull2016_orientation_proposee=isset($_POST["bull2016_orientation_proposee"]) ? $_POST["bull2016_orientation_proposee"] : "n";
	if(($bull2016_orientation_proposee!="y")&&($bull2016_orientation_proposee!="n")) {
		$bull2016_orientation_proposee="y";
	}
	if (!saveSetting("bull2016_orientation_proposee", $bull2016_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_titre_voeux_orientation=isset($_POST["bull2016_titre_voeux_orientation"]) ? $_POST["bull2016_titre_voeux_orientation"] : "";
	if($bull2016_titre_voeux_orientation=="") {
		$bull2016_titre_voeux_orientation="Voeux";
	}
	if (!saveSetting("bull2016_titre_voeux_orientation", $bull2016_titre_voeux_orientation)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_voeux_orientation !";
		$reg_ok = 'no';
	}

	$bull2016_titre_orientation_proposee=isset($_POST["bull2016_titre_orientation_proposee"]) ? $_POST["bull2016_titre_orientation_proposee"] : "";
	if($bull2016_titre_orientation_proposee=="") {
		$bull2016_titre_orientation_proposee="Orientation proposée";
	}
	if (!saveSetting("bull2016_titre_orientation_proposee", $bull2016_titre_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_titre_avis_orientation_proposee=isset($_POST["bull2016_titre_avis_orientation_proposee"]) ? $_POST["bull2016_titre_avis_orientation_proposee"] : "";
	if($bull2016_titre_avis_orientation_proposee=="") {
		$bull2016_titre_avis_orientation_proposee="Commentaire";
	}
	if (!saveSetting("bull2016_titre_avis_orientation_proposee", $bull2016_titre_avis_orientation_proposee)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_titre_avis_orientation_proposee !";
		$reg_ok = 'no';
	}

	$bull2016_orientation_periodes=isset($_POST["bull2016_orientation_periodes"]) ? $_POST["bull2016_orientation_periodes"] : "";
	if($bull2016_orientation_periodes!="") {
		if(!preg_match("/^[0-9;]{1,}$/", $bull2016_orientation_periodes)) {
			$bull2016_orientation_periodes="";
		}
		else {
			$tmp_tab_periode_orientation=explode(";", preg_replace("/[^0-9]/",";",$bull2016_orientation_periodes));
			$bull2016_orientation_periodes="";
			for($loop=0;$loop<count($tmp_tab_periode_orientation);$loop++) {
				if($tmp_tab_periode_orientation[$loop]!="") {
					if($bull2016_orientation_periodes!="") {
						$bull2016_orientation_periodes.=";";
					}
					$bull2016_orientation_periodes.=$tmp_tab_periode_orientation[$loop];
				}
			}
		}
	}
	if (!saveSetting("bull2016_orientation_periodes", $bull2016_orientation_periodes)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_orientation_periodes !";
		$reg_ok = 'no';
	}

	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	$bull2016_largeur_engagements=isset($_POST["bull2016_largeur_engagements"]) ? $_POST["bull2016_largeur_engagements"] : 30;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_engagements))||
	($bull2016_largeur_engagements=="")) {
		$bull2016_largeur_engagements=30;
	}
	if (!saveSetting("bull2016_largeur_engagements", $bull2016_largeur_engagements)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_largeur_engagements !";
		$reg_ok = 'no';
	}

	$sql="DELETE FROM setting WHERE name LIKE 'bull2016_afficher_engagements_id_%';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);
	if(isset($_POST['bull2016_afficher_engagements_id'])) {
		$bull2016_afficher_engagements_id=$_POST['bull2016_afficher_engagements_id'];
		for($loop=0;$loop<count($bull2016_afficher_engagements_id);$loop++) {
			if (!saveSetting("bull2016_afficher_engagements_id_".$bull2016_afficher_engagements_id[$loop], "y")) {
				$msg .= "Erreur lors de l'enregistrement de l'affichage de l'engagement n°".$bull2016_afficher_engagements_id[$loop]." !";
				$reg_ok = 'no';
			}
		}
	}
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// Colonne Noms de matières
	$bull2016_largeur_acquis_col_1=isset($_POST["bull2016_largeur_acquis_col_1"]) ? $_POST["bull2016_largeur_acquis_col_1"] : 44;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_1))||
	($bull2016_largeur_acquis_col_1=="")) {
		$bull2016_largeur_acquis_col_1=44;
	}
	if (!saveSetting("bull2016_largeur_acquis_col_1", $bull2016_largeur_acquis_col_1)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_largeur_acquis_col_1 !";
		$reg_ok = 'no';
	}

	// Colonne Éléments de programmes
	$bull2016_largeur_acquis_col_2=isset($_POST["bull2016_largeur_acquis_col_2"]) ? $_POST["bull2016_largeur_acquis_col_2"] : 49;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_2))||
	($bull2016_largeur_acquis_col_2=="")) {
		$bull2016_largeur_acquis_col_2=49;
	}
	if (!saveSetting("bull2016_largeur_acquis_col_2", $bull2016_largeur_acquis_col_2)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_largeur_acquis_col_2 !";
		$reg_ok = 'no';
	}

	// Colonne Moyenne élève
	$bull2016_largeur_acquis_col_moy=isset($_POST["bull2016_largeur_acquis_col_moy"]) ? $_POST["bull2016_largeur_acquis_col_moy"] : 15;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moy))||
	($bull2016_largeur_acquis_col_moy=="")) {
		$bull2016_largeur_acquis_col_moy=15;
	}
	if (!saveSetting("bull2016_largeur_acquis_col_moy", $bull2016_largeur_acquis_col_moy)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_largeur_acquis_col_moy !";
		$reg_ok = 'no';
	}

	// Colonne Moyenne classe
	$bull2016_largeur_acquis_col_moyclasse=isset($_POST["bull2016_largeur_acquis_col_moyclasse"]) ? $_POST["bull2016_largeur_acquis_col_moyclasse"] : 15;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moyclasse))||
	($bull2016_largeur_acquis_col_moyclasse=="")) {
		$bull2016_largeur_acquis_col_moyclasse=15;
	}
	if (!saveSetting("bull2016_largeur_acquis_col_moyclasse", $bull2016_largeur_acquis_col_moyclasse)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_largeur_acquis_col_moyclasse !";
		$reg_ok = 'no';
	}

	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	// Hauteurs en page 2:

	// Hauteur Cadre Bilan des acquisitions en cycle 3
	$bull2016_hauteur_bilan_acquisitions_cycle_3=isset($_POST["bull2016_hauteur_bilan_acquisitions_cycle_3"]) ? $_POST["bull2016_hauteur_bilan_acquisitions_cycle_3"] : 83;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_3))||
	($bull2016_hauteur_bilan_acquisitions_cycle_3=="")) {
		$bull2016_hauteur_bilan_acquisitions_cycle_3=83;
	}
	if (!saveSetting("bull2016_hauteur_bilan_acquisitions_cycle_3", $bull2016_hauteur_bilan_acquisitions_cycle_3)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_hauteur_bilan_acquisitions_cycle_3 !";
		$reg_ok = 'no';
	}

	// Hauteur Cadre Bilan des acquisitions en cycle 4
	$bull2016_hauteur_bilan_acquisitions_cycle_4=isset($_POST["bull2016_hauteur_bilan_acquisitions_cycle_4"]) ? $_POST["bull2016_hauteur_bilan_acquisitions_cycle_4"] : 44;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_4))||
	($bull2016_hauteur_bilan_acquisitions_cycle_4=="")) {
		$bull2016_hauteur_bilan_acquisitions_cycle_4=44;
	}
	if (!saveSetting("bull2016_hauteur_bilan_acquisitions_cycle_4", $bull2016_hauteur_bilan_acquisitions_cycle_4)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_hauteur_bilan_acquisitions_cycle_4 !";
		$reg_ok = 'no';
	}

	// Hauteur Cadre Communication avec la famille
	$bull2016_hauteur_communication_famille=isset($_POST["bull2016_hauteur_communication_famille"]) ? $_POST["bull2016_hauteur_communication_famille"] : 49;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_communication_famille))||
	($bull2016_hauteur_communication_famille=="")) {
		$bull2016_hauteur_communication_famille=49;
	}
	if (!saveSetting("bull2016_hauteur_communication_famille", $bull2016_hauteur_communication_famille)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_hauteur_communication_famille !";
		$reg_ok = 'no';
	}

	// Hauteur Cadre Visa de la famille
	$bull2016_hauteur_visa_famille=isset($_POST["bull2016_hauteur_visa_famille"]) ? $_POST["bull2016_hauteur_visa_famille"] : 18;
	if((!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_visa_famille))||
	($bull2016_hauteur_visa_famille=="")) {
		$bull2016_hauteur_visa_famille=18;
	}
	if (!saveSetting("bull2016_hauteur_visa_famille", $bull2016_hauteur_visa_famille)) {
		$msg .= "Erreur lors de l'enregistrement de bull2016_hauteur_visa_famille !";
		$reg_ok = 'no';
	}

	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	if (isset($_POST['bull_affiche_aid'])) {
		if (!saveSetting("bull_affiche_aid", $_POST['bull_affiche_aid'])) {
			$msg .= "Erreur lors de l'enregistrement de bull_affiche_aid !";
			$reg_ok = 'no';
		}
	}
}

if (($reg_ok == 'yes') and (isset($_POST['ok']))) {
	$msg = "Enregistrement réussi <em>(".strftime("Le %A %d/%m/%Y à %H:%M:%S").")</em> !";
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// End standart header

//=====================================
$titre_page = "Paramètres de configuration des bulletins PDF Réforme CLG 2016";
require_once("../lib/header.inc.php");
//=====================================

if (!loadSettings()) {
	die("Erreur chargement settings");
}
?>

<script type="text/javascript">
	change='no';
</script>

<p class="bold"><a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
| <a href="./bull_index.php"> Imprimer les bulletins</a>
| <a href="./param_bull.php"> Paramètres d'impression des bulletins HTML</a>
| <a href="./param_bull_pdf.php"> Paramètres d'impression des bulletins PDF</a>
</p>
<?php

if ((($_SESSION['statut']=='professeur') AND ((getSettingValue("GepiProfImprBul")!='yes') OR ((getSettingValue("GepiProfImprBul")=='yes') AND (getSettingValue("GepiProfImprBulSettings")!='yes')))) OR (($_SESSION['statut']=='scolarite') AND (getSettingValue("GepiScolImprBulSettings")!='yes')) OR (($_SESSION['statut']=='administrateur') AND (getSettingValue("GepiAdminImprBulSettings")!='yes')))
{
	die("Droits insuffisants pour effectuer cette opération");
}

$bull2016_arrondi=getSettingValue("bull2016_arrondi");
if(((!preg_match("/^[0-9]{1,}$/", $bull2016_arrondi))&&
(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_arrondi)))||
($bull2016_arrondi==0)||
($bull2016_arrondi=="")) {
	$bull2016_arrondi=0.01;
	//echo "Correction de bull2016_arrondi à $bull2016_arrondi";
}

$bull2016_nb_chiffre_virgule=getSettingValue("bull2016_nb_chiffre_virgule");
if((!preg_match("/^[0-9]{1,}$/", $bull2016_nb_chiffre_virgule))||
($bull2016_nb_chiffre_virgule=="")) {
	$bull2016_nb_chiffre_virgule=1;
}

$bull2016_chiffre_avec_zero=getSettingValue("bull2016_chiffre_avec_zero");
if(($bull2016_chiffre_avec_zero!="0")&&($bull2016_chiffre_avec_zero!="1")) {
	$bull2016_chiffre_avec_zero=0;
}

$bull2016_evolution_moyenne_periode_precedente=getSettingValue("bull2016_evolution_moyenne_periode_precedente");
if($bull2016_evolution_moyenne_periode_precedente=="") {
	$bull2016_evolution_moyenne_periode_precedente="y";
}

$bull2016_evolution_moyenne_periode_precedente_seuil=getSettingValue("bull2016_evolution_moyenne_periode_precedente_seuil");
if(((!preg_match("/^[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil))&&
(!preg_match("/^[0-9]{1,}\.[0-9]{1,}$/", $bull2016_evolution_moyenne_periode_precedente_seuil)))||
($bull2016_evolution_moyenne_periode_precedente_seuil=="")) {
	$bull2016_evolution_moyenne_periode_precedente_seuil=0;
}

$bull2016_moyminclassemax=getSettingValue("bull2016_moyminclassemax");
if($bull2016_moyminclassemax=="") {
	$bull2016_moyminclassemax="n";
}

$bull2016_affich_mentions=getSettingValue("bull2016_affich_mentions");
if($bull2016_affich_mentions=="") {
	$bull2016_affich_mentions="y";
}

$bull2016_avec_coches_mentions=getSettingValue("bull2016_avec_coches_mentions");
if($bull2016_avec_coches_mentions=="") {
	$bull2016_avec_coches_mentions="y";
}

$bull2016_intitule_mentions=getSettingValue("bull2016_intitule_mentions");
if($bull2016_intitule_mentions=="") {
	$bull2016_intitule_mentions="y";
}

//$afficher_nb_heures_perdues="n";
$bull2016_aff_abs_nj=getSettingValue("bull2016_aff_abs_nj");
if($bull2016_aff_abs_nj=="") {
	$bull2016_aff_abs_nj="y";
}

$bull2016_aff_abs_justifiees=getSettingValue("bull2016_aff_abs_justifiees");
if($bull2016_aff_abs_justifiees=="") {
	$bull2016_aff_abs_justifiees="y";
}

$bull2016_aff_total_abs=getSettingValue("bull2016_aff_total_abs");
if($bull2016_aff_total_abs=="") {
	$bull2016_aff_total_abs="y";
}

$bull2016_aff_retards=getSettingValue("bull2016_aff_retards");
if($bull2016_aff_retards=="") {
	$bull2016_aff_retards="y";
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$bull2016_titre_voeux_orientation=getSettingValue("bull2016_titre_voeux_orientation");
if($bull2016_titre_voeux_orientation=="") {
	$bull2016_titre_voeux_orientation="Voeux";
}
$bull2016_titre_orientation_proposee=getSettingValue("bull2016_titre_orientation_proposee");
if($bull2016_titre_orientation_proposee=="") {
	$bull2016_titre_orientation_proposee="Orientation proposée";
}
$bull2016_titre_avis_orientation_proposee=getSettingValue("bull2016_titre_avis_orientation_proposee");
if($bull2016_titre_avis_orientation_proposee=="") {
	$bull2016_titre_avis_orientation_proposee="Commentaire";
}

$bull2016_orientation_periodes=getSettingValue("bull2016_orientation_periodes");

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$bull2016_INE_checked="";
if(getSettingAOui('bull2016_INE')) {
	$bull2016_INE_checked=" checked";
}

$bull2016_afficher_cadre_adresse_resp_checked="";
if(getSettingAOui('bull2016_afficher_cadre_adresse_resp')) {
	$bull2016_afficher_cadre_adresse_resp_checked=" checked";
}

// Paramètre désactivé
$bull2016_pas_espace_reserve_EPI_AP_Parcours=getSettingValue('bull2016_pas_espace_reserve_EPI_AP_Parcours');
$bull2016_pas_espace_reserve_EPI_AP_Parcours_checked="";
if($bull2016_pas_espace_reserve_EPI_AP_Parcours=="y") {
	$bull2016_pas_espace_reserve_EPI_AP_Parcours_checked=" checked";
}

$bull2016_autorise_sous_matiere=getSettingValue('bull2016_autorise_sous_matiere');
$bull2016_autorise_sous_matiere_checked="";
if($bull2016_autorise_sous_matiere=="y") {
	$bull2016_autorise_sous_matiere_checked=" checked";
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$bull2016_largeur_engagements=getSettingValue('bull2016_largeur_engagements');
if($bull2016_largeur_engagements=="") {
	$bull2016_largeur_engagements=30;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_engagements)) {
	$bull2016_largeur_engagements=30;
}

$bull2016_afficher_engagements_id=array();
$sql="SELECT * FROM setting WHERE name LIKE 'bull2016_afficher_engagements_id_%';";
$res_eng=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_eng)>0) {
	while($lig_eng=mysqli_fetch_object($res_eng)) {
		$bull2016_afficher_engagements_id[]=preg_replace("/^bull2016_afficher_engagements_id_/", "", $lig_eng->NAME);
	}
}

// Colonne Noms de matières
$bull2016_largeur_acquis_col_1=getSettingValue('bull2016_largeur_acquis_col_1');
if($bull2016_largeur_acquis_col_1=="") {
	$bull2016_largeur_acquis_col_1=44;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_1)) {
	$bull2016_largeur_acquis_col_1=44;
}


// Colonne Éléments de programmes
$bull2016_largeur_acquis_col_2=getSettingValue('bull2016_largeur_acquis_col_2');
if($bull2016_largeur_acquis_col_2=="") {
	$bull2016_largeur_acquis_col_2=49;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_2)) {
	$bull2016_largeur_acquis_col_2=49;
}


// Colonne Moyenne élève
$bull2016_largeur_acquis_col_moy=getSettingValue('bull2016_largeur_acquis_col_moy');
if($bull2016_largeur_acquis_col_moy=="") {
	$bull2016_largeur_acquis_col_moy=15;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moy)) {
	$bull2016_largeur_acquis_col_moy=15;
}


// Colonne Moyenne classe
$bull2016_largeur_acquis_col_moyclasse=getSettingValue('bull2016_largeur_acquis_col_moyclasse');
if($bull2016_largeur_acquis_col_moyclasse=="") {
	$bull2016_largeur_acquis_col_moyclasse=15;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_largeur_acquis_col_moyclasse)) {
	$bull2016_largeur_acquis_col_moyclasse=15;
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// Page 2:
// Hauteur cadre Bilan des acquisitions cycle 3
$bull2016_hauteur_bilan_acquisitions_cycle_3=getSettingValue('bull2016_hauteur_bilan_acquisitions_cycle_3');
if($bull2016_hauteur_bilan_acquisitions_cycle_3=="") {
	$bull2016_hauteur_bilan_acquisitions_cycle_3=83;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_3)) {
	$bull2016_hauteur_bilan_acquisitions_cycle_3=83;
}

// Hauteur cadre Bilan des acquisitions cycle 4
$bull2016_hauteur_bilan_acquisitions_cycle_4=getSettingValue('bull2016_hauteur_bilan_acquisitions_cycle_4');
if($bull2016_hauteur_bilan_acquisitions_cycle_4=="") {
	$bull2016_hauteur_bilan_acquisitions_cycle_4=44;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_bilan_acquisitions_cycle_4)) {
	$bull2016_hauteur_bilan_acquisitions_cycle_4=44;
}

// Hauteur cadre Communication avec la famille
$bull2016_hauteur_communication_famille=getSettingValue('bull2016_hauteur_communication_famille');
if($bull2016_hauteur_communication_famille=="") {
	$bull2016_hauteur_communication_famille=49;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_communication_famille)) {
	$bull2016_hauteur_communication_famille=49;
}

// Hauteur cadre Visa de la famille
$bull2016_hauteur_visa_famille=getSettingValue('bull2016_hauteur_visa_famille');
if($bull2016_hauteur_visa_famille=="") {
	$bull2016_hauteur_visa_famille=18;
}
elseif(!preg_match("/^[0-9]{1,}$/", $bull2016_hauteur_visa_famille)) {
	$bull2016_hauteur_visa_famille=18;
}



$bull2016_cadre_visa_famille_checked="";
if(!getSettingANon('bull2016_cadre_visa_famille')) {
	$bull2016_cadre_visa_famille_checked=" checked";
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

?>

<form name="formulaire" action="param_bull_pdf_2016.php" method="post" style="width: 100%;">
<?php
echo add_token_field();
?>
<input type='hidden' name='is_posted' value='y' />
<h3>Paramètres divers</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Paramètres divers'>
	<tr>
		<td><label for='bull2016_INE'>Faire apparaître le numéro INE de l'élève&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_INE" id="bull2016_INE" onchange="changement()" value="y"<?php
				echo $bull2016_INE_checked;
			?> />
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_afficher_cadre_adresse_resp'>Faire apparaître le cadre adresse responsable&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_afficher_cadre_adresse_resp" id="bull2016_afficher_cadre_adresse_resp" onchange="changement()" value="y"<?php
				echo $bull2016_afficher_cadre_adresse_resp_checked;
			?> />
		</td>
	</tr>

	<!--
	<tr>
		<td><label for='bull2016_pas_espace_reserve_EPI_AP_Parcours'>Ne pas réserver d'espace pour les EPI, AP, Parcours en page 2<br />
		<span style='font-size:x-small'>Remonter les cadres suivants si un espace libre apparait</span>&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_pas_espace_reserve_EPI_AP_Parcours" id="bull2016_pas_espace_reserve_EPI_AP_Parcours" onchange="changement()" value="y"<?php
				echo $bull2016_pas_espace_reserve_EPI_AP_Parcours_checked;
			?> />
		</td>
	</tr>
	-->

	<tr>
		<td><label for='bull2016_autorise_sous_matiere'>Prendre en compte le souhait des professeurs<br />de voir apparaitre telle ou telle sous-matière&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_autorise_sous_matiere" id="bull2016_autorise_sous_matiere" onchange="changement()" value="y"<?php
				echo $bull2016_autorise_sous_matiere_checked;
			?> />
		</td>
	</tr>
</table>
</blockquote>

<h3>Paramètres des moyennes</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Paramètres des moyennes'>
	<tr>
		<td>Arrondi dans le calcul de moyennes&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_arrondi" id="bull2016_arrondi" size="5" onchange="changement()" value="<?php
				echo $bull2016_arrondi;
			?>" onKeyDown="clavier_3(this.id,event, 0.001, 1, 0.001);" />
		</td>
	</tr>
	<tr>
		<td>Nombre de chiffres à droite de la virgule&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_nb_chiffre_virgule" id="bull2016_nb_chiffre_virgule" size="5" onchange="changement()" value="<?php
				echo $bull2016_nb_chiffre_virgule;
			?>" onKeyDown="clavier_2(this.id,event, 0, 5);" />
		</td>
	</tr>
	<tr>
		<td>Afficher les zéros inutiles à droite de la virgule&nbsp;:</td>
		<td>
			<input type="radio" name="bull2016_chiffre_avec_zero" id="bull2016_chiffre_avec_zero_0" onchange="changement()" value="0" <?php
			if(getSettingValue('bull2016_chiffre_avec_zero')!="1") {
				echo "checked ";
			}
			?>/><label for='bull2016_chiffre_avec_zero_0'> Oui</label><br />
			<input type="radio" name="bull2016_chiffre_avec_zero" id="bull2016_chiffre_avec_zero_1" onchange="changement()" value="1" <?php
			if(getSettingValue('bull2016_chiffre_avec_zero')=="1") {
				echo "checked ";
			}
			?>/><label for='bull2016_chiffre_avec_zero_1'> Non</label>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_evolution_moyenne_periode_precedente'>Afficher l'évolution (+/-) par rapport à la période précédente&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_evolution_moyenne_periode_precedente" id="bull2016_evolution_moyenne_periode_precedente" onchange="changement()" value="y" <?php
			if($bull2016_evolution_moyenne_periode_precedente=="y") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td title="Une variation inférieure au nombre proposé ne sera pas considérée comme justifiant l'affichage d'un + ou d'un - indiquant une évolution de la moyenne.">Seuil de la variation pour estimer qu'il y a<br />évolution de la moyenne par rapport à la période précédente&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_evolution_moyenne_periode_precedente_seuil" id="bull2016_evolution_moyenne_periode_precedente_seuil" size="5" onchange="changement()" value="<?php
				echo $bull2016_evolution_moyenne_periode_precedente_seuil;
			?>" onKeyDown="clavier_3(this.id,event, 0.001, 1, 0.001);" />
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_moyminclassemax'>Afficher les moyennes min/classe/max dans la colonne Classe&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_moyminclassemax" id="bull2016_moyminclassemax" onchange="changement()" value="y" <?php
			if($bull2016_moyminclassemax=="y") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
</table>
</blockquote>

<h3>Paramètres des <?php echo getSettingValue("gepi_denom_mention");?>s</h3>
<blockquote>
<p><em>(sous réserve que des <?php echo getSettingValue("gepi_denom_mention");?>s soient associées aux classes)</em></p>
<table class='boireaus boireaus_alt' summary='Paramètres des mentions'>
	<tr>
		<td>Afficher les <?php echo getSettingValue("gepi_denom_mention");?>s&nbsp;:</td>
		<td>
			<input type="radio" name="bull2016_affich_mentions" id="bull2016_affich_mentions_y" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_affich_mentions')!="n") {
				echo "checked ";
			}
			?>/><label for='bull2016_affich_mentions_y'> Oui</label><br />
			<input type="radio" name="bull2016_affich_mentions" id="bull2016_affich_mentions_n" onchange="changement()" value="n" <?php
			if(getSettingValue('bull2016_affich_mentions')=="n") {
				echo "checked ";
			}
			?>/><label for='bull2016_affich_mentions_n'> Non</label>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_avec_coches_mentions'>Afficher les <?php echo getSettingValue("gepi_denom_mention");?>s sous la forme de cases à cocher&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_avec_coches_mentions" id="bull2016_avec_coches_mentions" onchange="changement()" value="y" <?php
			if($bull2016_avec_coches_mentions!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_intitule_mentions'>Dans le cas où on n'affiche pas de case à cocher,<br />faire précéder la <?php echo getSettingValue("gepi_denom_mention");?> obtenue de l'intitulé "<?php echo getSettingValue("gepi_denom_mention");?>"&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_intitule_mentions" id="bull2016_intitule_mentions" onchange="changement()" value="y" <?php
			if($bull2016_intitule_mentions!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
</table>
</blockquote>

<h3>Paramètres des absences</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Paramètres des absences'>
	<tr>
		<td><label for='bull2016_aff_abs_nj'>Afficher le nombre d'absences non justifiées&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_aff_abs_nj" id="bull2016_aff_abs_nj" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_abs_nj')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_aff_abs_justifiees'>Afficher le nombre d'absences justifiées&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_aff_abs_justifiees" id="bull2016_aff_abs_justifiees" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_abs_justifiees')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_aff_total_abs'>Afficher le nombre total d'absences&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_aff_total_abs" id="bull2016_aff_total_abs" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_total_abs')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_aff_retards'>Afficher le nombre de retards&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_aff_retards" id="bull2016_aff_retards" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_aff_retards')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
</table>
</blockquote>

<h3>Paramètres Orientation</h3>
<blockquote>
<p>Sous réserve que le module Orientation soit activé et que l'orientation soit activée pour les MEFS associés à la classe demandée à l'impression.</p>
<table class='boireaus boireaus_alt' summary='Paramètres Orientation'>
	<tr>
		<td>Liste des périodes avec affichage du cadre orientation&nbsp;:<br />
		<em>(laissez vide pour désactiver l'affichage de l'ensemble du cadre Orientation;<br />
		sinon donnez les numéros de périodes, séparés par des point-virgules)</em></td>
		<td>
			<input type="text" name="bull2016_orientation_periodes" id="bull2016_orientation_periodes" size="20" onchange="changement()" value="<?php
				echo $bull2016_orientation_periodes;
			?>" />
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_voeux_orientation'>Afficher le cadre des Voeux d'orientation&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_voeux_orientation" id="bull2016_voeux_orientation" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_voeux_orientation')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Titre du bloc Voeux&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_voeux_orientation" id="bull2016_titre_voeux_orientation" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_voeux_orientation;
			?>" />
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_orientation_proposee'>Afficher le cadre des Orientations proposées&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_orientation_proposee" id="bull2016_orientation_proposee" onchange="changement()" value="y" <?php
			if(getSettingValue('bull2016_orientation_proposee')!="n") {
				echo "checked ";
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>Titre du bloc Orientation proposée&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_orientation_proposee" id="bull2016_titre_orientation_proposee" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_orientation_proposee;
			?>" />
		</td>
	</tr>
	<tr>
		<td>Titre de l'avis/commentaire sur l'orientation proposée&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_titre_avis_orientation_proposee" id="bull2016_titre_avis_orientation_proposee" size="20" onchange="changement()" value="<?php
				echo $bull2016_titre_avis_orientation_proposee;
			?>" />
		</td>
	</tr>
</table>
</blockquote>

<?php
	if(getSettingAOui('active_mod_engagements')) {
		$tab_engagements=get_tab_engagements();

		if(count($tab_engagements["indice"]>0)) {
			echo "
<a name='engagements'></a>
<h3>Engagements</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Engagements'>
	<tr>
		<td>Largeur réservée pour les Engagements dans le cadre Vie scolaire&nbsp;:</td>
		<td>
			<input type='text' name='bull2016_largeur_engagements' id='bull2016_largeur_engagements' size='5' onchange='changement()' value='".$bull2016_largeur_engagements."' onKeyDown='clavier_3(this.id,event, 0, 100, 1);' />
		</td>
	</tr>";
		for($loop=0;$loop<count($tab_engagements["indice"]);$loop++) {
			if($tab_engagements["indice"][$loop]["ConcerneEleve"]=="yes") {
				$checked="";
				if(in_array($tab_engagements["indice"][$loop]["id"], $bull2016_afficher_engagements_id)) {
					$checked=" checked";
				}
				echo "
	<tr>
		<td><label for='bull2016_largeur_engagements_$loop'>Afficher les engagements <strong>".$tab_engagements["indice"][$loop]["nom"]."</strong>&nbsp;:</label></td>
		<td>
			<input type='checkbox' name='bull2016_afficher_engagements_id[]' id='bull2016_largeur_engagements_$loop' onchange='changement()' value='".$tab_engagements["indice"][$loop]["id"]."'".$checked." />
		</td>
	</tr>";
			}
		}
		echo "
</table>
</blockquote>";
		}
	}
?>

<h3>Largeurs des colonnes du tableau de Suivi des acquis</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Largeurs de colonnes'>
	<tr>
		<td>Largeur de la colonne 'Nom de matières'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_largeur_acquis_col_1" id="bull2016_largeur_acquis_col_1" size="5" onchange="changement()" value="<?php
				echo $bull2016_largeur_acquis_col_1;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td>Largeur de la colonne 'Éléments de programmes'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_largeur_acquis_col_2" id="bull2016_largeur_acquis_col_2" size="5" onchange="changement()" value="<?php
				echo $bull2016_largeur_acquis_col_2;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td>Largeur de la colonne 'Moyenne élève'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_largeur_acquis_col_moy" id="bull2016_largeur_acquis_col_moy" size="5" onchange="changement()" value="<?php
				echo $bull2016_largeur_acquis_col_moy;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td>Largeur de la colonne 'Moyenne classe'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_largeur_acquis_col_moyclasse" id="bull2016_largeur_acquis_col_moyclasse" size="5" onchange="changement()" value="<?php
				echo $bull2016_largeur_acquis_col_moyclasse;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
</table>
<p>La colonne appréciation prend la largeur restante&nbsp;: <?php
	$bull2016_largeur_acquis_col_3=(189-$bull2016_largeur_acquis_col_1-$bull2016_largeur_acquis_col_2-$bull2016_largeur_acquis_col_moy-$bull2016_largeur_acquis_col_moyclasse);
	if($bull2016_largeur_acquis_col_3<0) {
		$bull2016_largeur_acquis_col_3="<span style='color:red; font-weight:bold;' title=\"La largeur est négative. L'affichage va planter.\">".$bull2016_largeur_acquis_col_3."</span>";
	}
	echo "189-$bull2016_largeur_acquis_col_1-$bull2016_largeur_acquis_col_2-$bull2016_largeur_acquis_col_moy-$bull2016_largeur_acquis_col_moyclasse=".$bull2016_largeur_acquis_col_3."mm";
?></p>
<p><a href='#' onclick="document.getElementById('bull2016_largeur_acquis_col_1').value=44;
document.getElementById('bull2016_largeur_acquis_col_2').value=49;
document.getElementById('bull2016_largeur_acquis_col_moy').value=15;
document.getElementById('bull2016_largeur_acquis_col_moyclasse').value=15;
return false;">Reprendre les largeurs de colonnes par défaut</a></p>
</blockquote>

<h3>Hauteurs en page 2</h3>
<blockquote>
<table class='boireaus boireaus_alt' summary='Hauteurs en page 2'>
	<tr>
		<td>Hauteur du cadre 'Bilan des acquisitions' en cycle 3&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_hauteur_bilan_acquisitions_cycle_3" id="bull2016_hauteur_bilan_acquisitions_cycle_3" size="5" onchange="changement()" value="<?php
				echo $bull2016_hauteur_bilan_acquisitions_cycle_3;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td>Hauteur du cadre 'Bilan des acquisitions' en cycle 4&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_hauteur_bilan_acquisitions_cycle_4" id="bull2016_hauteur_bilan_acquisitions_cycle_4" size="5" onchange="changement()" value="<?php
				echo $bull2016_hauteur_bilan_acquisitions_cycle_4;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td>Hauteur du cadre 'Communication avec la famille'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_hauteur_communication_famille" id="bull2016_hauteur_communication_famille" size="5" onchange="changement()" value="<?php
				echo $bull2016_hauteur_communication_famille;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
	<tr>
		<td><label for='bull2016_cadre_visa_famille'>Faire apparaître le cadre Visa de la famille en bas de 2è page&nbsp;:</label></td>
		<td>
			<input type="checkbox" name="bull2016_cadre_visa_famille" id="bull2016_cadre_visa_famille" onchange="changement()" value="y"<?php
				echo $bull2016_cadre_visa_famille_checked;
			?> />
		</td>
	</tr>
	<tr>
		<td>Hauteur du cadre 'Visa la famille'&nbsp;:</td>
		<td>
			<input type="text" name="bull2016_hauteur_visa_famille" id="bull2016_hauteur_visa_famille" size="5" onchange="changement()" value="<?php
				echo $bull2016_hauteur_visa_famille;
			?>" onKeyDown="clavier_3(this.id,event, 0, 100, 1);" />
		</td>
	</tr>
</table>
<!--
<p>La colonne appréciation prend la largeur restante&nbsp;: <?php
	$bull2016_largeur_acquis_col_3=(189-$bull2016_largeur_acquis_col_1-$bull2016_largeur_acquis_col_2-$bull2016_largeur_acquis_col_moy-$bull2016_largeur_acquis_col_moyclasse);
	if($bull2016_largeur_acquis_col_3<0) {
		$bull2016_largeur_acquis_col_3="<span style='color:red; font-weight:bold;' title=\"La largeur est négative. L'affichage va planter.\">".$bull2016_largeur_acquis_col_3."</span>";
	}
	echo "189-$bull2016_largeur_acquis_col_1-$bull2016_largeur_acquis_col_2-$bull2016_largeur_acquis_col_moy-$bull2016_largeur_acquis_col_moyclasse=".$bull2016_largeur_acquis_col_3."mm";
?></p>
-->
<p><a href='#' onclick="document.getElementById('bull2016_hauteur_bilan_acquisitions_cycle_3').value=83;
document.getElementById('bull2016_hauteur_bilan_acquisitions_cycle_4').value=44;
document.getElementById('bull2016_hauteur_communication_famille').value=49;
document.getElementById('bull2016_hauteur_visa_famille').value=18;
return false;">Reprendre les hauteurs de sections par défaut</a></p>
</blockquote>

<?php
echo "
<h3>Paramètres communs (HTML/PDF)</h3>
<blockquote>
<p><a name='bull_affiche_aid'></a>Afficher les données sur les AID&nbsp;: 
<input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidy\" value=\"y\" ";
if (getSettingValue("bull_affiche_aid") == 'y') echo " checked";
echo " /><label for='bull_affiche_aidy' style='cursor: pointer;'>&nbsp;Oui</label>";
echo " <input type=\"radio\" name=\"bull_affiche_aid\" id=\"bull_affiche_aidn\" value=\"n\" ";
if (getSettingValue("bull_affiche_aid") != 'y') echo " checked";
echo " /><label for='bull_affiche_aidn' style='cursor: pointer;'>&nbsp;Non</label><br />
<em>(l'affichage risque d'être nécessaire en collège pour les EPI, AP et Parcours)</em>
</p>
</blockquote>";
?>
<p style="text-align: center; margin-bottom:2em;"><input type="submit" name="ok" value="Enregistrer" style="font-variant: small-caps;"/></p>

</form>

<?php require("../lib/footer.inc.php");
