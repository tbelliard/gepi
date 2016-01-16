<?php
/**
 * Réinitialise totalement la table 'droits'
 * 
 * $Id$
 *
 * Ce fichier est toujours appelé lors d'une mise à jour.
 * Il réinitialise totalement la table 'droits' avec les informations détaillées
 * ci-après
 *
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 */

/**
 * Vérifie si l'index unique existe déjà
 * @param type $table
 * @param type $index 
 * @return boolean TRUE si l'index unique existe déjà
 */
function deja_unique($table, $index) {
  $sql = "SHOW COLUMNS FROM $table LIKE '$index'";
  $res = mysqli_query($GLOBALS["mysqli"], $sql);
  $test = mysqli_num_rows($res);
  if ($test){
    $donnees =mysqli_fetch_object($res);
    if ($donnees->Key == 'UNI'){
      return TRUE;
    }
  }
  return FALSE;
}

/**
 * Exécute une requête et renvoie une erreur au besoin
 * @global string
 * @param string $requete La requête à traité
 * @return string l'erreur ou vide
 */
function traite_requete($requete = "") {
	global $pb_maj;
	$retour = "";
	$res = mysqli_query($GLOBALS["mysqli"], $requete);
	$erreur_no = ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));
	if (!$erreur_no) {
		$retour = "";
	} else {
		switch ($erreur_no) {
			case "1060" :
				// le champ existe déjà : pas de problème
				$retour = "";
				break;
			case "1061" :
				// La clé existe déjà : pas de problème
				$retour = "";
				break;
			case "1062" :
				// Présence d'un doublon : création de la cléf impossible
				$retour = msj_erreur("Erreur (<strong>non critique</strong>) sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . mysqli_error($GLOBALS["mysqli"]) . ")");
				$pb_maj = 'yes';
				break;
			case "1068" :
				// Des clés existent déjà : pas de problème
				$retour = "";
				break;
			case "1069" :
				// trop d'index existent déjà pour cette table
				$retour = msj_erreur("Erreur (<strong>critique</strong>) sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . mysqli_error($GLOBALS["mysqli"]) . ")");
				$pb_maj = 'yes';
				break;
			case "1091" :
				// Déjà supprimé : pas de problème
				$retour = "";
				break;
			default :
				$retour = msj_erreur("Erreur sur la requête : <i>" . $requete . "</i> (" . ((is_object($GLOBALS["mysqli"])) ? mysqli_errno($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) . " : " . mysqli_error($GLOBALS["mysqli"]) . ")");
				$pb_maj = 'yes';
				break;
		}
	}
	return $retour;
}

// statuts dynamiques
$result .= "<p>";
$result .= "&nbsp;-> Ajout d'un champ 'autre' à la table 'droits'";
$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM droits LIKE 'autre'"));
if ($test1 == 0) {
        $query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `droits` ADD `autre` VARCHAR( 1 ) NOT NULL DEFAULT 'F' AFTER `secours` ;");
        if ($query) {
                $result .= msj_ok();
        } else {
                $result .= msj_erreur();
        }
}
$result .= "</p>";


// A effectuer quelquesoit la mise à jour
//champs de la table droits :   `id`   `administrateur`   `professeur`   `cpe`  `scolarite`   `eleve`   `responsable`   `secours`  `autre`   `description`  `statut`
$tab_req[] = "TRUNCATE droits;";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/rapport_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Rapport Incident', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/gerer_modeles_ooo.php', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Gérer et utiliser les modèles', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : Retenue', '');;";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/formulaire_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo : formulaire retenue', '');;";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Modèle Ooo: Index : Index', '');;";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/update_colonne_retenue.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Affichage d une imprimante pour le responsable d un incident', '');;";
$tab_req[] = "INSERT INTO droits VALUES ('/absences/index.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/absences/saisie_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/accueil_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', ' ', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/accueil_modules.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/accueil.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', ' ', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/accueil_professeur.php', 'V', 'V', 'F', 'F', 'F', 'F', 'V', 'F', ' ', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/add_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/config_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/export_csv_aid.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/index2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/modify_aid.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/modify_aid_new.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des AID (profs, élèves)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/lib/confirm_query.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/edit.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/param_bull.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/verif_bulletins.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Vérification du remplissage des bulletins', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/verrouillage.php', 'F', 'F', 'F', 'V', 'F', 'F', 'F', 'F', '(de)Verrouillage des périodes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des carnets de notes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_conteneur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/toutes_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
//$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation et impression des relevés de notes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Visualisation et impression des relevés de notes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/admin_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/modify_limites.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_admin/modify_type_doc.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des cahier de textes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_edition_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_edition_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_affichage_duplication_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_affichage_deplacement_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_suppression_notice.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_enregistrement_compte_rendu.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_enregistrement_notice_privee.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_edition_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_enregistrement_devoir.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_affichages_liste_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/ajax_affichage_dernieres_notices.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/traite_doc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/exportcsv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de textes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de textes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/see_all.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/creer_sequence.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - s&eacute;quences', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/creer_seq_ajax_step1.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Cahier de texte - s&eacute;quences', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_ajout.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_const.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/cpe_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des CPE aux classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/duplicate_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/eleve_options.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/classes/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_nom_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/classes/modify_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/periodes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/prof_suivi.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/eleves/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/eleves/import_eleves_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/eleves/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/eleves/modify_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/etablissements/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/etablissements/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/etablissements/modify_etab.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/accueil_sauve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/gestion_base_test.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'gestion données de test', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/savebackup.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Téléchargement de sauvegardes la base', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/efface_base.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Restauration, suppression et sauvegarde de la base', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/gestion_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des connexions', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/help_import.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/import_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/modify_impression.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des paramètres de la feuille de bienvenue', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/param_gen.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration générale', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/traitement_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/add_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout de groupes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition de groupes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_eleves.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Edition des élèves des groupes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_class.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des groupes de la classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matières aux professeurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_csv/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation CSV de l\'année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
//$tab_req[] = "INSERT INTO `droits` VALUES ('/init_dbf_sts/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l''année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/eleves_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/prof_disc_classes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_scribe/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation scribe de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_lcs/affectations.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation LCS de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/disciplines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/prof_disc_classe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/initialisation/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation de l\'ann?e scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation des matières en CSV', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_categories.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des catégories de matière', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/modify_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des matières', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/matieres/matieres_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/help.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index1.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Visualisation des notes et appréciations', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index3.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/visu_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Visualisation des notes et appréciations AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/visu_toutes_notes.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/responsables/index.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/responsables/modify_resp.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration et gestion des responsables élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/help.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_class_csv.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_note_app.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/index.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_aid.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes et appréciations AID', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_appreciations.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des appréciations du bulletins', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis1.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_avis2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie des avis du conseil de classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/traitement_csv.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie des notes du bulletins', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/change_pwd.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/help.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/import_prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/impression_bienvenue.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/reset_passwords.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Réinitialisation des mots de passe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/modify_user.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/mon_compte.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Gestion du compte (informations personnelles, mot de passe, ...)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', '')";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/classe_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/eleve_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/evol_eleve_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/evol_eleve.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/stats_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_param.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/fpdf/imprime_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/etablissements/import_etab_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration et gestion des établissements', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/import_app_cons.php', 'F', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Importation csv des avis du conseil de classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/messagerie/index.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion de la messagerie', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/absences/import_absences_gep.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/absences/seq_gep_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Maintenance', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/contacter_admin.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/gestion_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/gestion_absences_liste.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/impression_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/select.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_ret.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_dip.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_inf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/ajout_abs.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_absence.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/lettre_aux_parents.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/tableau.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/tableau_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/admin_periodes_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/liste_absences.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/graphiques.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', '', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/professeurs/prof_ajout_abs.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajout des absences en classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombinoscopes.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombi_impr.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualiser le trombinoscope', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_trombinoscopes/trombinoscopes_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(des)activation du module trombinoscope', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/visu_profs_class.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/popup.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des équipes pédagogiques', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/index2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_toutes_notes2.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des moyennes des carnets de notes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/affiche_eleve.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/draw_graphe.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/draw_graphe_star.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/draw_graphe_svg.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Visualisation graphique des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux CSV des listes d élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/get_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Génération de CSV élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/choix_couleurs.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix des couleurs des graphiques des résultats scolaires', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/visualisation/couleur.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Choix d une couleur pour le graphique des résultats scolaires', '');";
//$tab_req[] = "INSERT INTO droits VALUES ('/gestion/config_prefs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/config_prefs.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des préférences d utilisateurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilitaires/recalcul_moy_conteneurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recalcul des moyennes des conteneurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/scol_resp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Affectation des comptes scolarité aux classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/fiche_eleve.php', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Fiche du suivie de l''élève', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/referencement.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Référencement de Gepi sur la base centralisée des utilisateurs de Gepi', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des actions absences', '');";
// Pour un module non présent ni actif par défaut:
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/commentaires_types.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Saisie de commentaires-types', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/releve_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Relevé de note au format PDF', '');";


$tab_req[] = "INSERT INTO droits VALUES ('/impression/parametres_impression_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes PDF; réglage des paramètres', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/impression_serie.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF) en série', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/impression.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression rapide d une listes (PDF) ', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/liste_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des listes (PDF)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/lecture_xml_sconet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/disciplines_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/lecture_xml_sts_emp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/prof_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/save_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/responsables/maj_import.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/responsables/conversion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conversion des données responsables', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/create_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut responsable', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/create_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Création des utilisateurs au statut élève', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/edit_responsable.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut responsable', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/edit_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Edition des utilisateurs au statut élève', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/consultation.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/see_all.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des cahiers de texte', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/droits_acces.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrage des droits d accès', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/groupes/visu_profs_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Consultation équipe pédagogique', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/impression_avis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe.', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/avis_pdf.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des avis trimestrielles des conseils de classe. Module PDF', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/parametres_impression_pdf_avis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Impression des avis conseil classe PDF; reglage des parametres', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe en csv', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/impression/password_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Impression des identifiants et des mots de passe en PDF', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/buletin_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Bulletin scolaire au format PDF', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/gestion/etiquette_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Etiquette au format PDF', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/lib/export_csv.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fichier d''exportation en csv des absences', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/gestion/statistiques.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistique du module vie scolaire', '1');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/lib/graph_camembert.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/lib/graph_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique camembert', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/edt_organisation/admin_horaire_ouverture.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des horaires d''ouverture de l''établissement', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/edt_organisation/admin_config_semaines.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des types de semaines', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/gestion/fiche_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Fiche récapitulatif des absences', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_absences/lib/graph_double_ligne.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'graphique absence et retard sur le même graphique', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/bulletin/param_bull_pdf.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page de gestion des parametres du bulletin pdf', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/bulletin/bulletin_pdf_avec_modele_classe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'page generant le bulletin pdf en fonction du modele affecte a la classe ', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/gestion/security_panel.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'panneau de controle des atteintes a la securite', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/gestion/security_policy.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'definition des politiques de securite', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/gestion/options_connect.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Options de connexions', '');";
$tab_req[] = "INSERT INTO `droits` VALUES('/mod_absences/gestion/alert_suivi.php', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'système d''alerte de suivi d''élève', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/gestion/efface_photos.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression des photos non associées à des élèves', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/responsables/gerer_adr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des adresses de responsables', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/responsables/choix_adr_existante.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Choix adresse de responsable existante', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/cahier_notes/export_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export CSV/ODS du cahier de notes', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/cahier_notes/import_cahier_notes.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Import CSV du cahier de notes', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/eleves/add_eleve.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Gestion des élèves', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/saisie/export_class_ods.php', 'F', 'V', 'F', 'F', 'F', 'F', 'V', 'F', 'Export ODS des notes/appréciations', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/gestion_temp_dir.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des dossiers temporaires d utilisateurs', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définition des couleurs pour Gepi', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/utilisateurs/creer_remplacant.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'script de création d un remplaçant', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/lettre_pdf.php', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Publipostage des lettres d absences PDF', '1');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/accueil_simpl_prof.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Page d accueil simplifiée pour les profs', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/step1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/step2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/step3.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/responsables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/professeurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/prof_disc_classe_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/init_options.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/init_pp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/clean_tables.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/clean_temp.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des données antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/consultation_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données d années antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Index données antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/popup_annee_anterieure.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Consultation des données antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Activation/désactivation du module données antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/nettoyer_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression de données antérieures', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/archivage_aid.php', 'V', 'F', 'F', 'F', 'F', 'F','F', 'F', 'Fiches projets', '1');";

$tab_req[] = "INSERT INTO droits VALUES ('/responsables/maj_import1.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/responsables/maj_import2.php', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mise à jour depuis Sconet', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/corriger_ine.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction d INE dans la table annees_anterieures', '');";
$tab_req[] = "INSERT INTO `droits` VALUES ('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d élèves', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/gerer_annees_anterieures.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les années antérieures', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/lib/graph_double_ligne_fiche.php', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'F', 'Graphique de la fiche élève', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/admin_config_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Définir les différentes périodes', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/index_edt.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des emplois du temps', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_initialiser.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation des emplois du temps', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/effacer_cours.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des emplois du temps', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_calendrier.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation du calendrier', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/ajouter_salle.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion des salles', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_parametrer.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les paramètres EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/voir_groupe.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Voir les groupes de Gepi', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/modif_edt_tempo.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modification temporaire des EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_xml.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation EdT par xml', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par csv', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_csv2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre csv', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_texte.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_concordance.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un fichier texte', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_init_concordance2.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'initialisation EdT par un autre fichier csv', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/modifier_cours.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/modifier_cours_popup.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Modifier un cours', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler le module emploi du temps', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_eleve.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Régler le module emploi du temps', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/edt_param_couleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Régler les couleurs des matières (EdT)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_organisation/ajax_edtcouleurs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modifier les couleurs des affichages des emplois du temps.', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/creer_statut.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter et gérer des statuts personnalisés', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/utilisateurs/creer_statut_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Autoriser la création des statuts personnalisés', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_gestion_gr/edt_aff_gr.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Gérer les groupes du module EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_gestion_gr/edt_ajax_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Gérer les groupes du module EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_gestion_gr/edt_liste_eleves.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Gérer les groupes du module EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_gestion_gr/edt_liste_profs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Gérer les groupes du module EdT', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/edt_gestion_gr/edt_win.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Gérer les groupes du module EdT', '');";


$tab_req[] = "INSERT INTO droits VALUES ('/absences/import_absences_sconet.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/export_modele_pdf.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'exportation en csv des modeles de bulletin pdf', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/absences/consulter_absences.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Consulter les absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/professeurs/bilan_absences_professeur.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/professeurs/bilan_absences_classe.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Bilan des absences pour chaque professeur', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/voir_absences_viescolaire.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences du jour', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_absences_quotidien.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_absences_quotidien_pdf.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par créneau en pdf', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_absences_classe.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter les absences par classe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/gestion/bilan_repas_quotidien.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Consulter l inscription aux repas', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/absences.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Consulter les absences de son enfant', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_absences/admin/interface_abs.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Paramétrer les interfaces des professeurs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/absences/import_absences_gepi.php', 'F', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Page d''importation des absences de gepi mod_absences', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/ajax_appreciations.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Sauvegarde des appréciations du bulletins', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/lib/change_mode_header.php', 'V', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'Page AJAX pour changer la variable cacher_header', '1');";

$tab_req[] = "INSERT INTO droits VALUES ('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Recopie des moyennes', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/groupes/fusion_group.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fusionner des groupes', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/security_panel_archives.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'page archive du panneau de sécurité', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/responsables/corrige_ele_id.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Correction des ELE_ID d apres Sconet', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_inscription/inscription_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', '(De)activation du module inscription', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_inscription/inscription_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'accès au module configuration', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_inscription/inscription_config.php', 'V', 'F', 'F', 'V', 'F', 'F','F',  'F', 'Configuration du module inscription', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_inscription/help.php', 'V', 'F', 'F', 'V', 'F', 'F','F', 'F', 'Configuration du module inscription', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/aid/index_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/visu_fiches.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/modif_fiches.php', 'V', 'V', 'V', 'F', 'V', 'V', 'F', 'F', 'Outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/config_aid_fiches_projet.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/config_aid_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/config_aid_productions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Configuration des outils complémentaires de gestion des AIDs', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/acces_appreciations.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Configuration de la restriction d accès aux appréciations pour les élèves et responsables', '');";


$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/rouen/fiches_brevet.php','V','F','F','F','F','F','F','F', 'Accès aux fiches brevet','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/poitiers/fiches_brevet.php','V','F','F','F','F','F','F','F', 'Accès aux fiches brevet','');";


$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/notanet_admin.php','V','F','F','F','F','F','F','F', 'Gestion du module NOTANET','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/index.php','V','V','F','V','F','F','V','F', 'Notanet: Accueil','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/extract_moy.php','V','F','F','F','F','F','F','F', 'Notanet: Extraction des moyennes','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/corrige_extract_moy.php','V','F','F','F','F','F','F','F', 'Notanet: Extraction des moyennes','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/select_eleves.php','V','F','F','F','F','F','F','F', 'Notanet: Associations élèves/type de brevet','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/select_matieres.php','V','F','F','F','F','F','F','F', 'Notanet: Associations matières/type de brevet','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/saisie_app.php','F','V','F','F','F','F','V','F', 'Notanet: Saisie des appréciations','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/generer_csv.php','V','F','F','F','F','F','F','F', 'Notanet: Génération de CSV','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/choix_generation_csv.php','V','F','F','F','F','F','F','F', 'Notanet: Génération de CSV','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/verrouillage_saisie_app.php','V','F','F','F','F','F','F','F', 'Notanet: (Dé)Verrouillage des saisies','');";

$tab_req[] = "INSERT INTO droits SET id='/mod_notanet/verif_saisies.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet: Verification avant impression des fiches brevet',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/bull_index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Edition des bulletins', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes_bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'V','V', 'Relevé de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/param_releve_html.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F','F', 'Paramètres du relevé de notes', '1');";

$tab_req[] = "INSERT INTO droits VALUES ('/classes/changement_eleve_classe.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F','F', 'Changement de classe pour un élève', '1');";

$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/saisie_avis.php','V','F','F','V','F','F','F','F','Notanet: Saisie avis chef etablissement','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/poitiers/param_fiche_brevet.php','V','F','F','F','F','F','F','F','Notanet: Paramètres d impression','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/saisie_b2i_a2.php','V','F','F','V','F','F','F','F','Notanet: Saisie socles B2i et A2','');";

$tab_req[] = "INSERT INTO droits VALUES ( '/eleves/liste_eleves.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Lister des élèves', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/cahier_texte_admin/rss_cdt_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gérer les flux rss du cdt', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/matieres/suppr_matiere.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Suppression d une matiere', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/eleves/import_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Importation bulletin élève', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/eleves/export_bull_eleve.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Exportation bulletin élève', '');";

$tab_req[] = "INSERT INTO `droits`  VALUES ('/cahier_texte_admin/visa_ct.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page de signature des cahiers de texte', '');";

$tab_req[] = "INSERT INTO droits VALUES('/saisie/saisie_cmnt_type_prof.php','F','V','F','F','F','F','F','F', 'Saisie appréciations-types pour les profs','');";
$tab_req[] = "INSERT INTO droits VALUES('/saisie/export_cmnt_type_prof.php','F','V','F','F','F','F','F','F', 'Export des appréciations-types pour les profs','');";

$tab_req[] = "INSERT INTO droits VALUES('/mod_ent/index.php','V','F','F','F','F','F','F','F', 'Gestion de l intégration de GEPI dans un ENT','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_ent/gestion_ent_eleves.php','V','F','F','F','F','F','F','F', 'Gestion de l intégration de GEPI dans un ENT','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_ent/gestion_ent_profs.php','V','F','F','F','F','F','F','F', 'Gestion de l intégration de GEPI dans un ENT','');";
$tab_req[] = "INSERT INTO droits VALUES('/mod_ent/miseajour_ent_eleves.php','V','F','F','F','F','F','F','F', 'Gestion de l intégration de GEPI dans un ENT','');";

// Module discipline:
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/traiter_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Traitement', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie incident', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/occupation_lieu_heure.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Occupation lieu', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/liste_sanctions_jour.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/index.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Index', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/incidents_sans_protagonistes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Incidents sans protagonistes', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: EDT élève', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/ajout_sanction.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Ajout sanction', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_sanction.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Saisie sanction', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_roles.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définition des rôles', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_lieux.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définition des lieux', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_mesures.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définition des mesures', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/sauve_role.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg rôle incident', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_autres_sanctions.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définir types sanctions', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/liste_retenues_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Liste des retenues du jour', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/avertir_famille_html.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Avertir famille incident', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/sauve_famille_avertie.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Svg famille avertie', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/discipline_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Activation/desactivation du module', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/aid/annees_anterieures_accueil.php', 'V', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'Configuration des AID', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_secours_eleve.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'Saisie notes/appréciations pour un élève en compte secours', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Page appelée via ajax.', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/responsables/dedoublonnage_adresses.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Dédoublonnage des adresses responsables', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/bulletin/index.php', 'V', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Edition des bulletins', '1');";



$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ects/ects_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Module ECTS : Admin', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ects/index_saisie.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Accueil saisie', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ects/saisie_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Saisie', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ects/edition.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Edition des documents', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/documents_ects.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Génération des documents', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ects/recapitulatif.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'Module ECTS : Recapitulatif globaux', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_notanet/fb_rouen_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Rouen', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_notanet/fb_montpellier_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Montpellier', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter/enlever des plugins', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/index.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Accueil',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/admin.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Activation/désactivation',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/select_options.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Choix des options',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/select_eleves_options.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Choix des options des élèves',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/select_classes.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Choix des classes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/saisie_contraintes_opt_classe.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Saisie des contraintes options/classes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/liste_classe_fut.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Liste des classes futures (appel ajax)',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/affiche_listes.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Affichage de listes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/genere_ods.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Génération d un fichier ODS de listes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/affect_eleves_classes.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Affectation des élèves',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/select_arriv_red.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Sélection des arrivants/redoublants',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/liste_options.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Liste des options de classes existantes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/import_options.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Génèse des classes: Import options depuis CSV',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/eleves/import_communes.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Import des communes de naissance', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_notanet/fb_lille_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Lille', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_notanet/fb_creteil_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Fiches brevet PDF pour Creteil', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/disc_stat.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Statistiques', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/admin.php', administrateur='V', professeur='F', cpe='F', scolarite='F', eleve='F', responsable='F', secours='F', autre='F', description='Epreuves blanches: Activation/désactivation du module', statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/admin.php', administrateur='V', professeur='F', cpe='F', scolarite='F', eleve='F', responsable='F', secours='F', autre='F', description='Examens blancs: Activation/désactivation du module', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/index.php', administrateur='V', professeur='V', cpe='F', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Epreuve blanche: Accueil', statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/transfert_cn.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Transfert vers carnet de notes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/saisie_notes.php',administrateur='V',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Saisie des notes',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/genere_emargement.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Génération émargement',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/definir_salles.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Définir les salles',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/attribuer_copies.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Attribuer les copies aux professeurs',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/bilan.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Bilan',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/genere_etiquettes.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Génération étiquettes',statut='';";


$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/saisie_notes.php',administrateur='V',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Examen blanc: Saisie devoir hors enseignement',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/index.php',administrateur='V',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Examen blanc: Accueil',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/releve.php',administrateur='V',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Examen blanc: Relevé',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/bull_exb.php',administrateur='V',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Examen blanc: Bulletins',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_synthese_app_classe.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Synthèse des appréciations sur le groupe classe.', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/saisie_message_connexion.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de messages de connexion.', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/groupes/repartition_ele_grp.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Répartir des élèves dans des groupes', '');";


$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/edit_limite_bis.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index2bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/index3bis.php', 'F', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/prepa_conseil/visu_toutes_notes_bis.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Visualisation des notes par classes', '');";

$tab_req[] = "INSERT INTO `droits` VALUES ('/utilitaires/import_pays.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Import des pays', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_apb/admin.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Gestion du module Admissions PostBac',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_apb/index.php',administrateur='F',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Export XML pour le système Admissions Post-Bac',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_apb/export_xml.php',administrateur='F',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Export XML pour le système Admissions Post-Bac',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_gest_aid/admin.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Gestionnaire d\'AID',statut='';";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/ajax_edit_limite.php', 'V', 'V', 'V', 'V', 'V', 'V', 'F', 'F', 'Edition des bulletins simplifiés (documents de travail)', '');";
//$tab_req[] = "INSERT INTO droits VALUES ( '/mod_plugins/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajouter/enlever des plugins', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/check_nature_incident.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Discipline: Recherche de natures d incident', '');";

$tab_req[] = "INSERT INTO droits SET id='/groupes/signalement_eleves.php',administrateur='F',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Groupes: signalement des erreurs d affectation élève',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/bulletin/envoi_mail.php', administrateur='V',professeur='F',cpe='V',scolarite='V',eleve='F',responsable='F',secours='V',autre='F',description='Envoi de mail via ajax',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', '');";


// Initialisation Scribe NG
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/index.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - index',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape1.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 1',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape2.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 2',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape3.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 3',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape4.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 4',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape5.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 5',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape6.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 6',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/init_scribe_ng/etape7.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation Scribe NG - etape 7',statut='';";


$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_motifs_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_lieux_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_types_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_justifications_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_actions_absences.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/admin/admin_table_agregation.php', 'V', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Administration du module absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/saisir_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Affichage du formulaire de saisie de absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/absences_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Affichage des absences du jour', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_saisie_groupe.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrement des saisies d un groupe', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/liste_saisies.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Liste des saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/liste_traitements.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des traitements', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/liste_notifications.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des notifications', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/liste_saisies_selection_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Liste des saisits pour faire les traitement', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/visu_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/visu_traitement.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Visualisation d une saisie', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/visu_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation d une notification', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_modif_saisie.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Modification d une saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_modif_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d un traitement', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_modif_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Modification d une notification', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/generer_notification.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'generer une notification', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/saisir_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir l absence d un eleve', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/enregistrement_saisie_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'V', 'Enregistrer absence d un eleve', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/creation_traitement.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Crer un traitement', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_discipline/saisie_incident_abs2.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'V', 'Saisir un incident relatif a une absence', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/tableau_des_appels.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation du tableaux des saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/bilan_du_jour.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Visualisation du bilan du jour', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/extraction_saisies.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction des saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/extraction_demi-journees.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Extraction des saisies', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/ajax_edt_eleve.php', 'V', 'F', 'V', 'V', 'F', 'F', 'V', 'F', 'Affichage edt', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/bilan_individuel.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Bilan individuel des absences eleve', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/totaux_du_jour.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'V', 'Totaux du jour des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/statistiques.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistiques des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/generer_notifications_par_lot.php','F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Génération groupée des courriers', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/bilan_parent.php', 'F', 'F', 'F', 'F', 'F', 'V', 'F', 'F', 'Affichage parents des absences de leurs enfants', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/stat_justifications.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Statistiques des justifications des absences', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/liste_eleves.php', 'F', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Liste des élèves avec les filtes absence', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/saisie/validation_corrections.php', 'V', 'V', 'F', 'V', 'F', 'F', 'V', 'F', 'Validation des corrections proposées par des professeurs après la cloture d une période', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/param_ordre_item.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modifier l ordre des items dans les menus', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_categories.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définir les catégories', '');";
$tab_req[] = "INSERT INTO droits SET id='/mod_discipline/stats2/index.php',administrateur='V',professeur='F',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Module discipline: Statistiques',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_app.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie d appréciation', '');";

$tab_req[] = "INSERT INTO droits SET id='/init_csv/export_tables.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Initialisation CSV: Export tables',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_trombinoscopes/trombino_pdf.php', administrateur='V', professeur='V', cpe='V', scolarite='V', eleve='F', responsable='F', secours='F', autre='V', description='Trombinoscopes PDF', statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_trombinoscopes/trombino_decoupe.php', administrateur='V', professeur='F', cpe='F', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Génération d une grille PDF pour les trombinoscopes,...', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/groupes/menage_eleves_groupes.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Groupes: Desinscription des eleves sans notes ni appreciations',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/statistiques/export_donnees_bulletins.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Export de données des bulletins',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/statistiques/index.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Statistiques: Index',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/statistiques/classes_effectifs.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Statistiques: classe, effectifs',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_annees_anterieures/ajax_bulletins.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='V',responsable='V',secours='F',autre='V',description='Accès aux bulletins d années antérieures',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/lib/ajax_signaler_faute.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Possibilité de signaler une faute de frappe dans une appréciation',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/ajax_modif_eleve.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Enregistrement des modifications élève',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/ajouter_periode.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Classes: Ajouter des périodes',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/supprimer_periode.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Classes: Supprimer des périodes',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/groupes/visu_mes_listes.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Accès aux listes d élèves', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/index_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_cc_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/add_modif_cc_eval.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/saisie_notes_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_cc.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/delegation.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les délégations pour exclusion temporaire', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mef/admin_mef.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mef : administration des niveau et formations', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mef/associer_eleve_mef.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mef : administration des niveau et formations', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mef/enregistrement_eleve_mef.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Mef : administration des niveau et formations', '');";

$tab_req[] = "INSERT INTO droits SET id='/responsables/synchro_mail.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Synchronisation des mail responsables',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/eleves/synchro_mail.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Synchronisation des mail élèves',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/cahier_texte_2/archivage_cdt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Archivage des CDT', '');";
$tab_req[] = "INSERT INTO droits SET id='/documents/archives/index.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Archives des CDT',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/saisie/saisie_vocabulaire.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Saisie de vocabulaire',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_epreuve_blanche/genere_liste_affichage.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Epreuve blanche: Génération listes affichage',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/ajax_devoirs_classe.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Cahiers de textes : Devoirs d une classe pour tel jour',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/ajax_liste_notices_privees.php',administrateur='F',professeur='V',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Cahiers de textes : Liste des notices privées',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/publipostage_ooo.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'V', 'Modèle Ooo : Publipostage', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/saisie/saisie_mentions.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Saisie de mentions', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/visu_disc.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Discipline: Accès élève/parent', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_natures.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Définir les natures', '');";

$tab_req[] = "INSERT INTO droits SET id='/init_xml2/traite_csv_udt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des enseignements via un Export CSV UDT',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/init_xml2/init_alternatif.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Initialisation année scolaire', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_sso_table/index.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Gestion de la table de correspondance sso', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_examen_blanc/copie_exam.php', administrateur='V', professeur='V', cpe='F', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Examen blanc: Copie', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/gestion/changement_d_annee.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Changement d\'année.',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/absences/import_absences_csv.php', 'F', 'F', 'V', 'F', 'F', 'F', 'V', 'F', 'Saisie des absences', '');";

$tab_req[] = "INSERT INTO droits SET id='/statistiques/stat_connexions.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Statistiques de connexion',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/groupes/check_enseignements.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Controle des enseignements',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/lib/ajax_corriger_app.php',administrateur='F',professeur='V',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='V',description='Correction appreciation',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_annees_anterieures/archivage_bull_pdf.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Génération archives bulletins PDF', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_notanet/OOo/imprime_ooo.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Imprime fiches brevet openDocument',statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_notanet/OOo/fiches_brevet.php',administrateur='V',professeur='F',cpe='F',scolarite='V',eleve='F',responsable='F',secours='F',autre='F',description='Fiches brevet openDocument',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/modif_sexe.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Eleves: Modification ajax du sexe d un eleve',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/correction_notices_cdt_formules_maths.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des notices CDT',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/gestion/gestion_signature.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Gestion signature',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_abs2/saisir_groupe_plan.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Affichage du formulaire de saisie de absences sur plan de classe', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/matieres/matiere_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');";

$tab_req[] = "INSERT INTO droits VALUES ('/gestion/gestion_infos_actions.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Gestion des actions en attente signalées en page d accueil.', '1');";

$tab_req[] = "INSERT INTO droits SET id='/responsables/maj_import3.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Mise à jour Sconet',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/responsables/consult_maj_sconet.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Consultation des compte-rendus de mise à jour Sconet',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_discipline/mod_discipline_extraction_ooo.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline : Extrait OOo des incidents',
statut='';";

$tab_req[]="INSERT INTO droits VALUES ('/cahier_notes/visu_releve_notes_ter.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F','F', 'Relevé de notes : accès parents et élèves', '1');";

$tab_req[]="INSERT INTO droits VALUES ('/utilisateurs/modif_par_lots.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Personnels : Traitements par lots', '1');";

$tab_req[]="INSERT INTO droits VALUES ('/bulletin/index_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Bulletins : Activation du module bulletins', '1');";

$tab_req[]="INSERT INTO droits SET id='/a_lire.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='V',
description='A lire...',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_alerte/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Dispositif d alerte : Administration du module',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_alerte/form_message.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='V',
description='Dispositif d alerte',
statut='';";

$tab_req[]="INSERT INTO droits VALUES ( '/cahier_notes/autorisation_exceptionnelle_saisie.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie dans le carnet de notes.', '');";

$tab_req[]="INSERT INTO droits VALUES ( '/bulletin/autorisation_exceptionnelle_saisie_note.php', 'V', 'F', 'F', 'V', 'F', 'F', 'F', 'F', 'Autorisation exceptionnelle de saisie de notes du bulletin.', '');";

$tab_req[]="INSERT INTO droits VALUES ('/cahier_notes/copie_dev.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Carnet de notes', '1');";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/consultation2.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='V',
description='Cahiers de textes: Consultation',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_trombinoscopes/plan_de_classe.php',administrateur='F',professeur='V',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Plan de classe',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/cherche_login.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Ajax: Recherche d un login',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/ajout_eleve_classe.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Enregistrement des inscriptions élève/classe',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/export_stat.php',administrateur='V',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Exports statistiques',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/calcul_score.php',administrateur='V',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Exports statistiques',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/admin/admin_table_totaux_absences.php', administrateur='V', scolarite='F', cpe='F', professeur='F', secours='F', eleve='F', responsable='F';";

$tab_req[] = "INSERT INTO droits SET id='/responsables/infos_parent.php', administrateur='F', scolarite='F', cpe='F', professeur='F', secours='F', eleve='F', responsable='V';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/ajax_cdt.php', administrateur='F', professeur='F', cpe='F', scolarite='F', eleve='V', responsable='V', secours='F', autre='F', description='Enregistrement des modifications sur CDT',statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes_admin/creation_conteneurs_par_lots.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F','F', 'Création de conteneurs/boites par lots', '1');";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/traitements_par_lots.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Abs2: Creation lot de traitements',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/cahier_notes/visu_cc_elv.php', 'F', 'F', 'F', 'F', 'V', 'V', 'F', 'F', 'Carnet de notes - visualisation par les élèves', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_discipline/aide.php',administrateur='V',professeur='V',cpe='V',scolarite='V',eleve='V',responsable='V',secours='V',autre='V',description='Discipline : Aide',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/recherche.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='V',
description='Effectuer une recherche sur une personne',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/dates_classes.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Définition de dates pour les classes',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/definir_bilan_periode.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Discipline: Définir les sanctions/avertissements de fin de période', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/imprimer_bilan_periode.php', 'V', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline: Imprimer les avertissements de fin de période', '');";
$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/saisie_avertissement_fin_periode.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Discipline: Saisie des sanctions/avertissements de fin de période', '');";

$tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/afficher_incidents_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Discipline: Affichage des incidents pour un élève.', '');";

$tab_req[] = "INSERT INTO droits SET id='/groupes/grp_groupes_edit_eleves.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Edition des élèves des groupes de groupes',
statut='';";
$tab_req[] = "INSERT INTO droits SET id='/groupes/modify_grp_group.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Gestion des groupes de groupes',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/saisie_bulletin.php',administrateur='F',professeur='F',cpe='V',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Saisie des absences et appréciations sur les bulletins',statut='';";

$tab_req[] = "INSERT INTO droits SET id='/gestion/admin_nomenclatures.php',administrateur='V',professeur='F',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Import des nomenclatures',statut='';";

$tab_req[]="INSERT INTO droits SET id='/cahier_texte_2/correction_notices_url_absolues_docs_joints.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des notices CDT',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES('/mod_notanet/saisie_notes.php','V','V','F','V','F','F','V','F','Notanet: Saisie notes','');";

$tab_req[] = "INSERT INTO droits SET id='/edt/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='EDT ICAL : Administration',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/edt/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='EDT ICAL : Index',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_admin/suppr_docs_joints_cdt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Suppression des documents joints aux CDT',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Absences et remplacements de professeurs',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/saisir_absence.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des absences de professeurs',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/proposer_remplacement.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Proposer des remplacements aux professeurs',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/attribuer_remplacement.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Attribuer les remplacements de professeurs',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Absences/remplacements de professeurs : Administration',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/afficher_remplacements.php', administrateur='V', professeur='F', cpe='V', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Afficher les remplacements de professeurs', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_engagements/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Engagements',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_engagements/saisie_engagements.php', administrateur='V', professeur='V', cpe='V', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Saisie des engagements', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_engagements/imprimer_documents.php', administrateur='V', professeur='V', cpe='V', scolarite='V', eleve='V', responsable='V', secours='F', autre='F', description='Imprimer documents', statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_engagements/saisie_engagements_user.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des engagements pour un utilisateur',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/groupes/correction_inscriptions_grp_csv.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des inscriptions dans des groupes d après un CSV',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/edt_organisation/import_edt_edt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des EDT depuis un XML EDT',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_engagements/extraction_engagements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Extraction des engagements',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/cdt_choix_caracteres.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Choix de caractères spéciaux pour le CDT2',
statut='';";
$tab_req[] = "INSERT INTO droits SET id='/cahier_texte_2/ajax_affichage_car_spec.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='CDT2: Caractères spéciaux à insérer',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/groupes/maj_inscript_ele_d_apres_edt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des inscriptions élèves depuis un XML EDT',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/resume_ele.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='Accueil élève résumé',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs_prof/consulter_remplacements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Consulter les remplacements de professeurs',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/groupes/export_groupes_sconet.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Exporter les groupes Gepi vers Sconet',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/eleves/ajax_consultation.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='V',
description='Recherches/consultations classes/élèves via ajax',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/info_dates_classes.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Informer des dates d événements pour les classes',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/cahier_notes/extraction_notes_cn.php',
administrateur='F',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Extraction et export des notes des carnets de notes',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/etablissements/chercheRNE.php', "
   . "administrateur='V', "
   . "professeur='F', "
   . "cpe='F', "
   . "scolarite='F', "
   . "eleve='F', "
   . "responsable='F', "
   . "secours='F', "
   . "autre='F', "
   . "description='Recherche des RNE sans établissements', "
   . "statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/dates_classes2.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Définition de dates pour les classes',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/classes/export_ele_opt.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export options élèves',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_discipline/saisie_pointages.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Pointages petits incidents',
statut='';";
$tab_req[] = "INSERT INTO droits SET id='/mod_discipline/param_pointages.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline: Definir les types de pointages de petits incidents',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/visu_eleve_calendrier.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Absences2 : Visualisation absences élève dans un calendrier',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_notanet/recherche_ine.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet : Recherche INE',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/visualisation/graphes_classe.php',
administrateur='F',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Tous les graphes sur une page pour une classe donnée',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/lib/ajax_action.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='V',
autre='F',
description='Action ajax',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_genese_classes/affiche_listes2.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Genèse des classes: Affichage de listes (2)',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/edt/import_vacances_ics.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='EDT : Import des vacances depuis l ICAL officiel EducNat',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/edt/index2.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='F',
description='EDT 2 : Index',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/bulletin/bulletins_et_conseils_classes.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Bulletins et conseils de classe',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/aid/popup.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Visualisation des membres d un AID',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/init_xml2/traite_xml_edt.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Import des enseignements via un Export XML EDT',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_sso_table/traite_export_csv.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='SSO table : Export CSV',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_sso_table/publipostage.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='SSO table : Publipostage',
statut='';";

$tab_req[]="INSERT INTO droits SET id='/mod_ent/index_lea.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Rapprochement des comptes ENT/GEPI : ENT LEA',
statut='';";

$tab_req[] = "INSERT INTO droits SET id='/mod_abs2/visu_saisies_ele_jour.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Voir les saisies d absences pour un élève tel jour',
statut='';";

$tab_req[] = "INSERT INTO droits VALUES ('/mod_listes_perso/index.php', 'F', 'V', 'V', 'V', 'F', 'F', 'F', 'F', 'Listes personnelles', '');";
$tab_req[] = "INSERT INTO droits VALUES ('/mod_listes_perso/index_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Listes personnelles', '');";

$tab_req[] = "INSERT INTO droits SET id='/mod_discipline/ajout_travail_sanction.php', administrateur='V', professeur='V', cpe='V', scolarite='V', eleve='F', responsable='F', secours='F', autre='F', description='Discipline: Ajout de travail pour une sanction', statut='';";

//$tab_req[] = "";

$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM droits LIKE 'responsable'"));
if ($test1 == 1) {
        foreach ($tab_req as $key => $value) {
                $result .= traite_requete($value);
        }
} else {
        $droits_requests = $tab_req;
        $tab_req = array ();
}


?>
