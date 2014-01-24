<?php

/**
 *
 *
 * @copyright 2008-2011
 * Fichier d'inclusion à la gestion des statuts 'autre'
 *
 */
//$niveau_arbo = 1;
	// Initialisations files
	//require_once("../lib/initialisations.inc.php");

if (!$_SESSION["statut"] OR ($_SESSION["statut"] != 'autre' AND $_SESSION["statut"] != 'administrateur')) {

		//tentative_intrusion(1, "Tentative d'accès à un fichier sans avoir les droits nécessaires");
		trigger_error('Vous avez procédé à une lecture de fichier interdit', E_USER_ERROR);

}

// droits généraux et communs à tous les utilisateurs
$autorise[0] = array('/accueil.php',
				'/utilisateurs/mon_compte.php',
				'/gestion/contacter_admin.php',
				'/gestion/info_gepi.php');
// droits spécifiques sur les pages relatives aux droits possibles
//$autorise[1] = array('/cahier_notes/visu_releve_notes.php');
$autorise[1] = array('/cahier_notes/visu_releve_notes_bis.php');
$autorise[2] = array('/prepa_conseil/index3.php', '/prepa_conseil/edit_limite.php');
$autorise[3] = array('/mod_absences/gestion/voir_absences_viescolaire.php',
						'/mod_absences/gestion/bilan_absences_quotidien.php',
						'/mod_absences/gestion/bilan_absences_classe.php',
						'/mod_absences/gestion/bilan_absences_quotidien_pdf.php',
						'/mod_absences/lib/tableau.php',
						'/mod_absences/lib/export_csv.php');
$autorise[4] = array('/mod_absences/gestion/select.php',
						'/mod_absences/gestion/ajout_abs.php',
						'/mod_absences/lib/liste_absences.php');
$autorise[5] = array('/cahier_texte/see_all.php');
$autorise[6] = array('/cahier_texte_admin/visa_ct.php');
$autorise[7] = array('/edt_organisation/index_edt.php');
//$autorise[8] = array('/tous_les_edt');
$autorise[8] = array('/tous_les_edt', '/edt_organisation/index_edt.php');
$autorise[9] = array('/messagerie/index.php');
$autorise[10]= array('/eleves/visu_eleve.php',
						'/eleves/liste_eleves.php');
$autorise[11]= array('/voir_resp');
$autorise[12]= array('/voir_ens');
$autorise[13]= array('/voir_notes');
$autorise[14]= array('/voir_bulle');
$autorise[15]= array('/voir_abs');
$autorise[16]= array('/mod_annees_anterieures/consultation_annee_anterieure.php', '/mod_annees_anterieures/popup_annee_anterieure.php');
$autorise[17]= array('/mod_trombinoscopes/trombinoscopes.php', '/mod_trombinoscopes/trombi_impr.php', '/mod_trombinoscopes/trombino_pdf.php');
$autorise[18]= array('/mod_discipline/index.php',
					 '/mod_discipline/saisie_incident.php',
					 '/mod_discipline/check_nature_incident.php',
					 '/mod_discipline/incidents_sans_protagonistes.php',
					 '/mod_discipline/sauve_role.php',
					 '/mod_discipline/update_colonne_retenue.php',
					 '/mod_discipline/traiter_incident.php',
					 '/mod_ooo/retenue.php',
					 '/mod_ooo/rapport_incident.php');
$autorise[19]= array('/mod_abs2/index.php');
$autorise[20]= array('/mod_abs2/saisir_eleve.php',
					 '/mod_abs2/saisir_groupe.php',
					 '/mod_abs2/visu_saisie.php',
					 '/mod_abs2/enregistrement_modif_saisie.php',
					 '/mod_abs2/liste_saisies.php',
					 '/mod_abs2/enregistrement_saisie_eleve.php',
					 '/mod_abs2/enregistrement_saisie_groupe.php');
$autorise[21]= array('/mod_abs2/bilan_individuel.php' );
$autorise[22]= array('/mod_abs2/totaux_du_jour.php' );
$autorise[23]= array('/bulletin/bull_index.php' );
$autorise[24]= array('/groupes/visu_profs_class.php', '/groupes/popup.php');
$autorise[25]= array('/groupes/visu_mes_listes.php', '/groupes/popup.php', '/impression/liste_pdf.php', '/impression/impression.php', '/impression/impression_serie.php', '/impression/parametres_impression_pdf.php');
$autorise[26]= array('/groupes/mes_listes.php', '/groupes/get_csv.php', '/groupes/update_champs_periode.php');


$iter = count($autorise);
$nbre_menu = $iter - 1;
// Intitulés pour le menu et le champ name du formulaire
$menu_accueil[0] = array($nbre_menu);
$menu_accueil[1] = array('relevés de notes', 'Visionner les relevés de notes de tous les élèves', 'ne');
$menu_accueil[2] = array('Bulletins simplifiés', 'Visionner tous les bulletins simplifiés de tous les élèves', 'bs');
$menu_accueil[3] = array('Voir les absences', 'Visionner toutes les absences de l\'établissement', 'va');
$menu_accueil[4] = array('Saisir les absences', 'Saisir les absences de tous les élèves de l\'établissement', 'sa');
$menu_accueil[5] = array('Cahiers de textes', 'Visionner tous les cahiers de textes de l\'établissement', 'cdt');
$menu_accueil[6] = array('Signer les cahiers de textes', 'Signer (viser) les cahiers de textes', 'cdt_visa');
$menu_accueil[7] = array('Emploi du temps', 'Visionner les emplois du temps de tous les élèves', 'ee');
$menu_accueil[8] = array('Emploi du temps', 'Visionner tous les emplois du temps de l\'établissement', 'te');
$menu_accueil[9] = array('Panneau d\'affichage', 'Gérer les messages à afficher sur la page d\'accueil des utilisateurs.', 'pa');
$menu_accueil[10] = array('Fiches élèves', 'Accès général sur les fiches des élèves', 've');
$menu_accueil[11] = array('Fiches : ', 'Fiches : voir les responsables', 'vre');
$menu_accueil[12] = array('Fiches : ', 'Fiches : voir les enseignements', 'vee');
$menu_accueil[13] = array('Fiches : ', 'Fiches : voir les relevés de notes', 'vne');
$menu_accueil[14] = array('Fiches : ', 'Fiches : voir les bulletins simplifiés', 'vbe');
$menu_accueil[15] = array('Fiches : ', 'Fiches : voir les absences', 'vae');
$menu_accueil[16] = array('Fiches : ', 'Fiches : voir année antérieure', 'anna');
$menu_accueil[17] = array('Trombinoscope', 'Trombinoscope des élèves et personnels', 'tr');
$menu_accueil[18] = array('Discipline', 'Accéder au module Discipline : Déclarer et gérer ses incidents', 'dsi');
$menu_accueil[19] = array('Absence2', 'Absence2 : accéder au module.', 'abs');
$menu_accueil[20] = array('Absence2', 'Absence2 : saisir les absences.', 'abs_saisie');
$menu_accueil[21] = array('Absence2', 'Absence2 : accéder à la page bilan individuel.', 'abs_bilan');
$menu_accueil[22] = array('Absence2', 'Absence2 : accéder à la page totaux du jour.', 'abs_totaux');
$menu_accueil[23] = array('Bulletin', 'Visualisation et impression des bulletins ','bul_print');
$menu_accueil[24] = array('Visualisation équipes', 'Visualisation des équipes pédagogiques', 'visu_equipes_peda');
$menu_accueil[25] = array('Visualisation listes élèves et grilles PDF', 'Visualisation listes élèves et grilles PDF', 'visu_listes_ele');
$menu_accueil[26] = array('Accès aux listes CSV élèves', 'Accès aux listes CSV élèves', 'listes_ele_csv');


?>
