<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Liste des tables à vider avant de procéder à l'importation
 * (ne pas modifier à moins de savoir précisément ce que vous faites)
  */

$liste_tables_del = array(
"a_agregation_decompte",
"a_notifications",
"a_saisies",
"a_saisies_version",
"a_traitements",
"absences",
"absences_gep",
"absences_rb",
"absences_repas",
"absences_eleves",
"vs_alerts_eleves",
"vs_alerts_groupes",
"vs_alerts_types",
"aid",
"aid_appreciations",
"avis_conseil_classe",
"eleves",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",

"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_eleves_resp",
"j_aid_utilisateurs_gest",

// Par la suite, à voir si on efface les classes ou non...
"classes",

"j_eleves_classes",
//==========================
// On ne vide plus la table chaque année
// Problème avec Sconet qui récupère seulement l'établissement de l'année précédente qui peut être l'établissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",

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
"observatoire",
"observatoire_comment",
"observatoire_suivi",

"tempo2",
"tempo",

"trombino_decoupe",
"trombino_decoupe_param",

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
"ct_documents",
"ct_devoirs_entry",
"ct_private_entry"
*/
"ex_classes",
"ex_groupes",
"ex_notes",

"eb_copies",
"eb_epreuves",
"eb_groupes",
"eb_profs",

"gc_ele_arriv_red",
"gc_eleves_options",

// On efface aussi les periodes (logique puisqu'on vide la table 'classes')
"periodes",

"s_communication",
"s_exclusions",
"s_incidents",
"s_protagonistes",
"s_reports",
"s_retenues",
"s_sanctions",
"s_traitement_incident",
"s_travail",
"s_travail_mesure",

"rss_users"
);

/*
 * Nom de la période ajoutée par défaut aux classes lors de l'import
 */
$nom_periode_defaut = "Periode";


?>
