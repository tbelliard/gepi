<?php

// Nettoyage des tables

// ATTENTION: Lors de l'ajout de tables à nettoyer à une étape donnée, à veiller, notamment dans le cas de tables temporaires, à vérifier que dans la suite de la procédeure d'initialisation, cette table n'intervient pas... ou sans perturbation

// Etape de l'import des élèves et classes
$liste_tables_del_etape_eleves = array(
// mod_abs2
"a_agregation_decompte",
"a_notifications",
"a_saisies",
"a_saisies_version",
"a_traitements",
// Absences
"absences",
"absences_gep",
"absences_rb",
"absences_repas",
"absences_eleves",
"vs_alerts_eleves",
"vs_alerts_groupes",
"vs_alerts_types",
// AID
"aid",
"aid_appreciations",
"avis_conseil_classe",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_eleves_resp",
"j_aid_utilisateurs_gest",
"j_aidcateg_super_gestionnaires",
"j_aidcateg_utilisateurs",

// Elèves et responsables
"eleves",
"responsables",

"sconet_ele_options",
/*
// NE FAUDRAIT-IL PAS VIDER ICI responsables2, resp_pers et reps_adr?
// NON: Cela empêche de conserver les comptes utilisateurs pour les responsables
"responsables2",
"resp_pers",
"resp_adr",
*/
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque année
// Problème avec Sconet qui récupère seulement l'établissement de l'année précédente qui peut être l'établissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",

"engagements_user",

// Logs des mises à jour sconet
"log_maj_sconet",

// Notes et appréciations
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"synthese_app_classe",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_lvr_ele',
'notanet_socle_commun',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
"socle_eleves_enseignements_complements",
//==========================
"observatoire",
"observatoire_comment",
"observatoire_suivi",

"o_orientations",
"o_voeux",

"tempo2",
"tempo",
// Découpe de trombinoscopes
"trombino_decoupe",
"trombino_decoupe_param",
// Cahier de notes
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
/*
"ct_entry",
// Cahier de textes
"ct_documents",
"ct_devoirs_entry",
"ct_private_entry"
*/
// mod_examen_blanc
"ex_classes",
"ex_groupes",
"ex_notes",
// mod_epreuve_blanche
"eb_copies",
"eb_epreuves",
"eb_groupes",
"eb_profs",
// Génèse des classes
"gc_ele_arriv_red",
"gc_eleves_options",
// mod_discipline
"s_avertissements",
"s_communication",
"s_exclusions",
"s_incidents",
"s_protagonistes",
"s_reports",
"s_retenues",
"s_sanctions",
"s_sanctions_check",
"s_traitement_incident",
"s_travail",
"s_travail_mesure",
// Module remplacements/absences de professeurs
"abs_prof",
"abs_prof_remplacement",
// Table optionnelle pour les fils RSS de CDT
"rss_users",
"log_maj_sconet",
// Tables import EDT
"edt_lignes", 
"edt_tempo", 
"edt_corresp2", 
"edt_eleves_lignes"
);

// Etape de l'import des matières
$liste_tables_del_etape_matieres = array(
"eleves_groupes_settings",
"groupes",
"j_eleves_groupes",
"j_groupes_matieres",
"j_groupes_professeurs",
"j_groupes_classes",
"j_groupes_visibilite",
"j_signalement",
"eleves_groupes_settings",
'groupes_param',
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"matieres_app_corrections",
"matieres_app_delais",
"matieres_appreciations_acces",
"matieres_appreciations_acces_eleve",
"synthese_app_classe",
//==========================
"socle_eleves_enseignements_complements",
//==========================
"sconet_ele_options",
"tempo2",
"tempo",
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"ct_devoirs_entry",
"ct_devoirs_faits",
"ct_documents",
"ct_entry",
"ct_devoirs_documents",
"ct_private_entry",
"ct_sequences",
// Module EDT ICAL/ICS
"edt_ics",
// D'une année sur l'autre, les associations matière_EDT/matiere_GEPI et prof_EDT/prof_GEPI peuvent être conservées
//"edt_ics_matiere",
//"edt_ics_prof",
// EDT?
'edt_classes',
'edt_cours',
"grp_groupes",
"grp_groupes_admin",
"grp_groupes_groupes",
"j_mep_eleve",
"j_mep_groupe"
/*
// Attente de confirmation de Pascal Fautrero pour décommenter...
,
"edt_calendrier",
//"edt_classes",
"edt_cours",
//"edt_creneaux",
//"edt_creneaux_bis",
"edt_dates_special",
"edt_init",
//"edt_semaines",
//"edt_setting"
*/
);

// Etape de l'import des responsables
$liste_tables_del_etape_resp = array(
// On vide l'ancienne table responsables pour ne pas conserver des infos d'années antérieures:
"responsables",

"responsables2",
"resp_pers",
"resp_adr",
"tempo2",
"tempo",

"engagements_user",

"log_maj_sconet"
);

// Etape de l'import des professeurs
$liste_tables_del_etape_professeurs = array(
"j_aid_utilisateurs",
"j_aid_utilisateurs_gest",
"j_groupes_professeurs",
"j_eleves_professeurs",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
"matieres_app_corrections",
"matieres_app_delais",
"matieres_appreciations_acces",
"matieres_appreciations_acces_eleve",
"synthese_app_classe",
"observatoire_j_resp_champ",
"tempo2",
"tempo",
"cc_dev",
"cc_eval",
"cc_notes_eval",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
"udt_lignes"
//,"udt_corresp"
);

?>
