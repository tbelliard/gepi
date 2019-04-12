<?php

/**
 *
 *
 * @copyright 2008-2019
 * Fichier d'inclusion à la gestion des statuts 'autre'
 *
 */
//$niveau_arbo = 1;
	// Initialisations files
	//require_once("../lib/initialisations.inc.php");

if(!isset($_SESSION["statut"]) OR $_SESSION["statut"]=='') {

		//tentative_intrusion(1, "Tentative d'accès à un fichier sans avoir les droits nécessaires");
		trigger_error('Vous avez procédé à une lecture de fichier interdit', E_USER_ERROR);

}

// droits généraux et communs à tous les utilisateurs
$autorise_statuts_personnalise[0] = array('/accueil.php'=>"Page d\'accueil (aucun droit particulier).",
				'/utilisateurs/mon_compte.php'=>"Accès à la gestion de son compte personnel (préférences, mot de passe,...).",
				'/gestion/contacter_admin.php'=>"Accès à la page Contacter l\'administrateur.",
				'/gestion/info_gepi.php'=>"Accès à la page d\'informations sur Gepi.",
				'/mod_alerte/form_message.php'=>"Accès au module Alerte (consultation/saisie d\'alertes).",
				'/eleves/ajax_consultation.php'=>"Accès à une page de Recherche d\'élève d\'après son nom, son prénom ou sa classe.");
// droits spécifiques sur les pages relatives aux droits possibles
//$autorise_statuts_personnalise[1] = array('/cahier_notes/visu_releve_notes.php');
$autorise_statuts_personnalise[1] = array('/cahier_notes/visu_releve_notes_bis.php'=>"Visualisation des relevés de notes de tous les élèves.");
$autorise_statuts_personnalise[2] = array('/cahier_notes/index2.php'=>"Visualisation des moyennes des carnets de notes de tous les élèves.", 
				'/cahier_notes/visu_toutes_notes2.php'=>"Visualisation des moyennes des carnets de notes de tous les élèves.", 
				'/visualisation/draw_graphe.php'=>"Présentation en courbe des moyennes des carnets de notes de tous les élèves.");
$autorise_statuts_personnalise[3] = array('/prepa_conseil/index3.php'=>"Visionner tous les bulletins simplifiés de tous les élèves.", 
				'/prepa_conseil/edit_limite.php'=>"Visionner tous les bulletins simplifiés de tous les élèves", 
				'/bulletin/bulletins_et_conseils_classes.php'=>"Visionner tous les bulletins simplifiés de tous les élèves", 
				'/lib/ajax_action.php'=>"Visionner tous les bulletins simplifiés de tous les élèves", 
				'/visualisation/draw_graphe.php'=>"Accès aux graphiques des moyennes et appréciations des bulletins de tous les élèves.", 
				'/prepa_conseil/visu_toutes_notes.php'=>"Visionner tous les bulletins simplifiés de tous les élèves");
$autorise_statuts_personnalise[4] = array('/mod_absences/gestion/voir_absences_viescolaire.php'=>"Visionner toutes les absences de l\'établissement.",
						'/mod_absences/gestion/bilan_absences_quotidien.php'=>"Visionner toutes les absences de l\'établissement.",
						'/mod_absences/gestion/bilan_absences_classe.php'=>"Visionner toutes les absences de l\'établissement.",
						'/mod_absences/gestion/bilan_absences_quotidien_pdf.php'=>"Visionner toutes les absences de l\'établissement.",
						'/mod_absences/lib/tableau.php'=>"Visionner toutes les absences de l\'établissement.",
						'/mod_absences/lib/export_csv.php'=>"Export CSV des absences.");
$autorise_statuts_personnalise[5] = array('/mod_absences/gestion/select.php'=>"Accès à la saisie des absences de tous les élèves de l\'établissement.",
						'/mod_absences/gestion/ajout_abs.php'=>"Accès à la saisie des absences de tous les élèves de l\'établissement.",
						'/mod_absences/lib/liste_absences.php'=>"Accès à la saisie des absences de tous les élèves de l\'établissement.");
if(getSettingValue('GepiCahierTexteVersion')=="2") {
	$autorise_statuts_personnalise[6] = array('/cahier_texte/see_all.php'=>"Visionner tous les cahiers de textes de l\'établissement.", 
						'/cahier_texte_2/see_all.php'=>"Visionner tous les cahiers de textes de l\'établissement.");
}
else {
	$autorise_statuts_personnalise[6] = array('/cahier_texte/see_all.php'=>"Visionner tous les cahiers de textes de l\'établissement.");
}
$autorise_statuts_personnalise[7] = array('/cahier_texte_admin/visa_ct.php'=>"Signer (viser) les cahiers de textes.");
$autorise_statuts_personnalise[8] = array('/edt_organisation/index_edt.php'=>"Visionner les emplois du temps de tous les élèves.", 
						'/edt/index2.php'=>"Visionner les emplois du temps de tous les élèves.", 
						'/lib/ajax_action.php'=>"Visionner les emplois du temps de tous les élèves.");
//$autorise_statuts_personnalise[9] = array('/tous_les_edt');
$autorise_statuts_personnalise[9] = array('/tous_les_edt'=>"Visionner tous les emplois du temps de l\'établissement.", 
						'/edt_organisation/index_edt.php'=>"Visionner tous les emplois du temps de l\'établissement.", 
						'/edt/index2.php'=>"Visionner tous les emplois du temps de l\'établissement.", 
						'/lib/ajax_action.php'=>"Visionner tous les emplois du temps de l\'établissement.");
$autorise_statuts_personnalise[10] = array('/messagerie/index.php'=>"Gestion des messages à afficher sur la page d\'accueil des utilisateurs.");
$autorise_statuts_personnalise[11]= array('/eleves/visu_eleve.php'=>"Accès général sur les fiches des élèves. Consultation des listes d\'élèves avec nom, prénom, sexe, date de naissance et classe.",
						'/eleves/liste_eleves.php'=>"Accès général sur les fiches des élèves. Consultation des listes d\'élèves avec nom, prénom, sexe, date de naissance et classe.");
$autorise_statuts_personnalise[12]= array('/voir_resp'=>"Fiches : voir les responsables d\'élèves (nom, prénom).");
$autorise_statuts_personnalise[13]= array('/voir_ens'=>"Fiches : voir les enseignements");
$autorise_statuts_personnalise[14]= array('/voir_notes'=>"Fiches : voir les relevés de notes");
$autorise_statuts_personnalise[15]= array('/voir_bulle'=>"Fiches : voir les bulletins simplifiés");
$autorise_statuts_personnalise[16]= array('/voir_abs'=>"Fiches : voir les absences");
$autorise_statuts_personnalise[17]= array('/mod_annees_anterieures/consultation_annee_anterieure.php'=>"Fiches : voir les bulletins simplifiés des années antérieures des élèves.", 
						'/mod_annees_anterieures/popup_annee_anterieure.php'=>"Fiches : voir les bulletins simplifiés des années antérieures des élèves.");
$autorise_statuts_personnalise[18]= array('/mod_trombinoscopes/trombinoscopes.php'=>"Accès aux trombinoscopes des élèves et personnels.", 
						'/mod_trombinoscopes/trombi_impr.php'=>"Accès aux trombinoscopes des élèves et personnels.", 
						'/mod_trombinoscopes/trombino_pdf.php'=>"Accès aux trombinoscopes des élèves et personnels (PDF).");
$autorise_statuts_personnalise[19]= array('/mod_discipline/index.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/saisie_incident.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/check_nature_incident.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/incidents_sans_protagonistes.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/sauve_role.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/update_colonne_retenue.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_discipline/traiter_incident.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_ooo/retenue.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents",
					 '/mod_ooo/rapport_incident.php'=>"Accéder au module Discipline : Déclarer et gérer ses incidents");
$autorise_statuts_personnalise[20]= array('/mod_abs2/index.php'=>"Absence2 : accéder au module.");
$autorise_statuts_personnalise[21]= array('/mod_abs2/saisir_eleve.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/saisir_groupe.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/visu_saisie.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/enregistrement_modif_saisie.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/liste_saisies.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/enregistrement_saisie_eleve.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/enregistrement_saisie_groupe.php'=>"Absence2 : saisir les absences.",
					 '/mod_abs2/visu_traitement.php'=>"Absence2 : saisir les absences.");
$autorise_statuts_personnalise[22]= array('/mod_abs2/bilan_individuel.php'=>"Absence2 : accéder à la page bilan individuel." );
$autorise_statuts_personnalise[23]= array('/mod_abs2/totaux_du_jour.php'=>"Absence2 : accéder à la page totaux du jour." );

// 20180217
// Problème: Si on donne le droit de voir les bulletins simplifiés, mais pas celui d'imprimer les bulletins, on bloque l\'accès aux pages '/bulletin/bulletins_et_conseils_classes.php', '/lib/ajax_action.php' parce que c'est le dernier test qui l'emporte et la valeur passe à F sur ces pages faute du droit n°24
//$autorise_statuts_personnalise[24]= array('/bulletin/bull_index.php', '/prepa_conseil/index2.php', '/prepa_conseil/visu_toutes_notes.php', '/visualisation/draw_graphe.php');
$autorise_statuts_personnalise[24]= array('/bulletin/bull_index.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/prepa_conseil/index2.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/prepa_conseil/visu_toutes_notes.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/visualisation/draw_graphe.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/bulletin/bulletins_et_conseils_classes.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/lib/ajax_action.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/saisie/impression_avis.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).", 
						'/impression/avis_pdf.php'=>"Visualisation et impression des bulletins (donne l\'accès aux adresses des responsables d\'élèves).");
// Corrigé par l'ajout d'un test dans creer_statut.php

$autorise_statuts_personnalise[25]= array('/groupes/visu_profs_class.php'=>"Visualisation des équipes pédagogiques", 
						'/groupes/popup.php'=>"Visualisation des équipes pédagogiques", 
						'/aid/popup.php'=>"Visualisation des équipes pédagogiques");
$autorise_statuts_personnalise[26]= array('/groupes/visu_mes_listes.php'=>"Visualisation listes élèves et grilles PDF", 
						'/groupes/popup.php'=>"Visualisation listes élèves et grilles PDF", 
						'/aid/popup.php'=>"Visualisation listes élèves et grilles PDF", 
						'/impression/liste_pdf.php'=>"Visualisation listes élèves et grilles PDF", 
						'/impression/impression.php'=>"Visualisation listes élèves et grilles PDF", 
						'/impression/impression_serie.php'=>"Visualisation listes élèves et grilles PDF", 
						'/impression/parametres_impression_pdf.php'=>"Visualisation listes élèves et grilles PDF");
$autorise_statuts_personnalise[27]= array('/groupes/mes_listes.php'=>"Accès aux listes CSV élèves", 
						'/groupes/get_csv.php'=>"Accès aux listes CSV élèves", 
						'/groupes/update_champs_periode.php'=>"Accès aux listes CSV élèves");

$autorise_statuts_personnalise[28]= array('/AccesAdresseParents'=>"Accès aux adresses postales responsables (parents,...)");
$autorise_statuts_personnalise[29]= array('/AccesTelParents'=>"Accès aux numéros de téléphone des responsables (parents,...)");
$autorise_statuts_personnalise[30]= array('/AccesMailParents'=>"Accès aux adresses mail des responsables (parents,...)");
$autorise_statuts_personnalise[31]= array('/AccesTelEleves'=>"Accès aux numéros de téléphone des élèves						
");
$autorise_statuts_personnalise[32]= array('/AccesMailEleves'=>"Accès aux adresses mail des élèves	");


$iter = count($autorise_statuts_personnalise);
$nbre_menu = $iter - 1;
// Intitulés pour le menu et le champ name du formulaire
$menu_accueil_statuts_personnalise[0] = array($nbre_menu);
$menu_accueil_statuts_personnalise[1] = array('relevés de notes', 'Visionner les relevés de notes de tous les élèves', 'ne');
$menu_accueil_statuts_personnalise[2] = array('Moyenne des carnets de notes', 'Visionner les moyennes des carnets de notes', 'moy_cn');
$menu_accueil_statuts_personnalise[3] = array('Bulletins simplifiés', 'Visionner tous les bulletins simplifiés de tous les élèves', 'bs');
$menu_accueil_statuts_personnalise[4] = array('Voir les absences', 'Visionner toutes les absences de l\'établissement', 'va');
$menu_accueil_statuts_personnalise[5] = array('Saisir les absences', 'Saisir les absences de tous les élèves de l\'établissement', 'sa');
$menu_accueil_statuts_personnalise[6] = array('Cahiers de textes', 'Visionner tous les cahiers de textes de l\'établissement', 'cdt');
$menu_accueil_statuts_personnalise[7] = array('Signer les cahiers de textes', 'Signer (viser) les cahiers de textes', 'cdt_visa');
$menu_accueil_statuts_personnalise[8] = array('Emploi du temps', 'Visionner les emplois du temps de tous les élèves', 'ee');
$menu_accueil_statuts_personnalise[9] = array('Emploi du temps', 'Visionner tous les emplois du temps de l\'établissement', 'te');
$menu_accueil_statuts_personnalise[10] = array('Panneau d\'affichage', 'Gérer les messages à afficher sur la page d\'accueil des utilisateurs.', 'pa');
$menu_accueil_statuts_personnalise[11] = array('Fiches élèves', 'Accès général sur les fiches des élèves', 've');
$menu_accueil_statuts_personnalise[12] = array('Fiches : ', 'Fiches : voir les responsables', 'vre');
$menu_accueil_statuts_personnalise[13] = array('Fiches : ', 'Fiches : voir les enseignements', 'vee');
$menu_accueil_statuts_personnalise[14] = array('Fiches : ', 'Fiches : voir les relevés de notes', 'vne');
$menu_accueil_statuts_personnalise[15] = array('Fiches : ', 'Fiches : voir les bulletins simplifiés', 'vbe');
$menu_accueil_statuts_personnalise[16] = array('Fiches : ', 'Fiches : voir les absences', 'vae');
$menu_accueil_statuts_personnalise[17] = array('Fiches : ', 'Fiches : voir année antérieure', 'anna');
$menu_accueil_statuts_personnalise[18] = array('Trombinoscope', 'Trombinoscope des élèves et personnels', 'tr');
$menu_accueil_statuts_personnalise[19] = array('Discipline', 'Accéder au module Discipline : Déclarer et gérer ses incidents', 'dsi');
$menu_accueil_statuts_personnalise[20] = array('Absence2', 'Absence2 : accéder au module.', 'abs');
$menu_accueil_statuts_personnalise[21] = array('Absence2', 'Absence2 : saisir les absences.', 'abs_saisie');
$menu_accueil_statuts_personnalise[22] = array('Absence2', 'Absence2 : accéder à la page bilan individuel.', 'abs_bilan');
$menu_accueil_statuts_personnalise[23] = array('Absence2', 'Absence2 : accéder à la page totaux du jour.', 'abs_totaux');
$menu_accueil_statuts_personnalise[24] = array('Bulletin', 'Visualisation et impression des bulletins ','bul_print');
$menu_accueil_statuts_personnalise[25] = array('Visualisation équipes', 'Visualisation des équipes pédagogiques', 'visu_equipes_peda');
$menu_accueil_statuts_personnalise[26] = array('Visualisation listes élèves et grilles PDF', 'Visualisation listes élèves et grilles PDF', 'visu_listes_ele');
$menu_accueil_statuts_personnalise[27] = array('Accès aux listes CSV élèves', 'Accès aux listes CSV élèves', 'listes_ele_csv');

$menu_accueil_statuts_personnalise[28] = array('Accès aux adresses postales responsables', 'Accès aux adresses postales responsables (parents,...)', 'AccesAdresseParents');
$menu_accueil_statuts_personnalise[29] = array('Accès aux numéros téléphones responsables', 'Accès aux numéros de téléphone des responsables (parents,...)', 'AccesTelParents');
$menu_accueil_statuts_personnalise[30] = array('Accès aux adresses mail responsables ', 'Accès aux adresses mail des responsables (parents,...)', 'AccesMailParents');
$menu_accueil_statuts_personnalise[31] = array('Accès aux numéros téléphones élèves', 'Accès aux numéros de téléphone des élèves', 'AccesTelEleves');
$menu_accueil_statuts_personnalise[32] = array('Accès aux adresses mail élèves', 'Accès aux adresses mail des élèves', 'AccesMailEleves');
/*
echo "<pre>";
print_r($autorise_statuts_personnalise);
echo "</pre>";
*/
?>
