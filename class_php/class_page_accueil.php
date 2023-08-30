<?php
/*
 * $Id$
 *
 * Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

/**
 * Description of class_page_accueil
 *
 * @author regis
 */
class class_page_accueil {

	public $titre_Menu = array();
	public $menu_item = array();
	public $canal_rss = array();
	public $message_admin = array();
	public $nom_connecte = array();
	public $referencement = array();
	public $message = array();
	public $probleme_dir = array();
	public $canal_rss_flux = "";
	public $gere_connect = "";
	public $alert_sums = "";
	public $signalement = "";
	public $nb_connect = "";
	public $nb_connect_lien = "";

	protected $ordre_menus = array();
	protected $cheminRelatif = "";
	protected $loginUtilisateur = "";
	public $statutUtilisateur = "";
	protected $gepiSettings = "";
	protected $test_prof_matiere = "";
	protected $test_prof_suivi = "";
	protected $test_prof_ects = "";
	protected $test_scol_ects = "";
	protected $test_prof_suivi_ects = "";
	protected $test_https = "";
	protected $a = 0;
	protected $b = 0;

	/**
	 * Construit les entrées de la page d'accueil
	 *
	 * @author regis
	 */
	function __construct($statut, $gepiSettings, $niveau_arbo, $ordre_menus) {

		global $mysqli;

		switch ($niveau_arbo) {
			case 0:
				$this->cheminRelatif = './';
				break;
			case 1:
				$this->cheminRelatif = '../';
				break;
			case 2:
				$this->cheminRelatif = '../../';
				break;
			case 3:
				$this->cheminRelatif = '../../../';
				break;
			default:
				$this->cheminRelatif = './';
		}

		$this->statutUtilisateur = $statut;
		$this->gepiSettings = $gepiSettings;
		$this->loginUtilisateur = $_SESSION['login'];

		$this->chargeOrdreMenu($ordre_menus);

		// On teste si l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de textes et au carnet de notes
		$sql = "SELECT login
			FROM j_groupes_professeurs
			WHERE login = '" . $this->loginUtilisateur . "'";
		$resultat1 = mysqli_query($mysqli, $sql);
		$this->test_prof_matiere = $resultat1->num_rows;


		// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
		$sql = "SELECT professeur
			FROM j_eleves_professeurs
			WHERE professeur = '" . $this->loginUtilisateur . "'";
		$resultat = mysqli_query($mysqli, $sql);
		$this->test_prof_suivi = $resultat->num_rows;


		$this->test_https = 'y'; // pour ne pas avoir à refaire le test si on a besoin de l'URL complète (rss)
		if (!isset($_SERVER['HTTPS'])
			or (isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) != "on")
			or (isset($_SERVER['X-Forwaded-Proto']) and $_SERVER['X-Forwaded-Proto'] != "https")) {
			$this->test_https = 'n';
		}

		/***** Outils d'administration *****/
		$this->verif_exist_ordre_menu('bloc_administration');
		if ($this->administration()) {
			$this->chargeAutreNom('bloc_administration');
		}

		/***** Outils de gestion des absences vie scolaire *****/
		$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
		if ($this->absences_vie_scol()) {
			$this->chargeAutreNom('bloc_absences_vie_scol');
		}

		/***** Outils de gestion des absences par les professeurs *****/
		$this->verif_exist_ordre_menu('bloc_absences_professeur');
		if ($this->absences_profs()) {
			$this->chargeAutreNom('bloc_absences_professeur');
		}

		/***** Saisie ***********/
		$this->verif_exist_ordre_menu('bloc_saisie');
		if ($this->saisie()) {
			$this->chargeAutreNom('bloc_saisie');
		}

		/***** Cahier de texte CPE ***********/
		$this->verif_exist_ordre_menu('bloc_Cdt_CPE');
		if ($this->cahierTexteCPE()) {
			$this->chargeAutreNom('bloc_Cdt_CPE');
		}

		/***** Cahier de texte CPE Restreint ***********/
		$this->verif_exist_ordre_menu('bloc_Cdt_CPE_Restreint');
		if ($this->cahierTexteCPE_Restreint()) {
			$this->chargeAutreNom('bloc_Cdt_CPE_Restreint');
		}

		/***** Visa Cahier de texte Scolarite ***********/
		$this->verif_exist_ordre_menu('bloc_Cdt_Visa');
		if ($this->cahierTexte_Visa()) {
			$this->chargeAutreNom('bloc_Cdt_Visa');
		}

		/***** Livret scolaire ***********/
		$this->verif_exist_ordre_menu('bloc_livret');
		if ($this->livret()) {
			$this->chargeAutreNom('bloc_livret');
		}

		/***** gestion des trombinoscopes : module de Christian Chapel ***********/
		$this->verif_exist_ordre_menu('bloc_trombinoscope');
		if ($this->trombinoscope()) {
			$this->chargeAutreNom('bloc_trombinoscope');
		}

		/***** Outils de relevé de notes *****/
		$this->verif_exist_ordre_menu('bloc_releve_notes');
		if ($this->releve_notes()) {
			$this->chargeAutreNom('bloc_releve_notes');
		}

		/***** Outils de relevé ECTS *****/
		$this->verif_exist_ordre_menu('bloc_releve_ects');
		if ($this->releve_ECTS()) {
			$this->chargeAutreNom('bloc_releve_ects');
		}

		/***** Listes personnelles *****/
		$this->verif_exist_ordre_menu('bloc_listes_personnelles');
		if ($this->listesPerso()) {
			$this->chargeAutreNom('bloc_listes_personnelles');
		}

		/***** Emploi du temps *****/
		//if (getSettingAOui('autorise_edt_tous')){
		$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
		if ($this->emploiDuTemps()) {
			$this->chargeAutreNom('bloc_emploi_du_temps');
		}
		//}

		/***** Outils destinés essentiellement aux parents et aux élèves *****/

		// Informations famille
		$this->verif_exist_ordre_menu('bloc_infos_famille');
		if ($this->infosFamille()) {
			$this->chargeAutreNom('bloc_infos_famille');
		}
		// Cahier de textes
		$this->verif_exist_ordre_menu('bloc_cahier_texte_famille');
		if ($this->cahierTexteFamille()) {
			$this->chargeAutreNom('bloc_cahier_texte_famille');
		}
		// Relevés de notes
		$this->verif_exist_ordre_menu('bloc_carnet_notes_famille');
		if ($this->releveNotesFamille()) {
			$this->chargeAutreNom('bloc_carnet_notes_famille');
		}
		// Relevés de notes cumulées
		if ('eleve' == $this->statutUtilisateur) {
			$result = FALSE;
			$sql = "SELECT 1=1 FROM `cc_notes_eval` WHERE login ='" . $this->loginUtilisateur . "'";
			$resultat = mysqli_query($mysqli, $sql);
			$result = $nb_aid = $resultat->num_rows;

			// $result += 1;
		} elseif ('responsable' == $this->statutUtilisateur) {
			$result = FALSE;

			$sql = "SELECT 1=1 
            FROM `cc_notes_eval` ne ,
                 `resp_pers` rp,
                 `responsables2` r2,
                 `eleves` e
            WHERE rp.login = '" . $this->loginUtilisateur . "'
                AND rp.pers_id = r2.pers_id
                AND r2.ele_id = e.ele_id
                AND e.login = ne.login";
			$resultat = mysqli_query($mysqli, $sql);
			$result = $nb_aid = $resultat->num_rows;

		} else {
			$result = FALSE;
		}
		if ($result) {
			if (getSettingAOui('GepiAccesEvalCumulEleve')) {
				$this->verif_exist_ordre_menu('bloc_carnet_notes_cumules');
				if ($this->notesCumulFamille())
					$this->chargeAutreNom('bloc_carnet_notes_cumules');
			}
		}

		// Equipes pédagogiques
		$this->verif_exist_ordre_menu('bloc_equipe_peda_famille');
		if ($this->equipePedaFamille())
			$this->chargeAutreNom('bloc_equipe_peda_famille');
		if (getSettingAOui('active_bulletins')) {
			// Bulletins simplifiés
			$this->verif_exist_ordre_menu('bloc_bull_simple_famille');
			if ($this->bulletinFamille())
				$this->chargeAutreNom('bloc_bull_simple_famille');
			// Graphiques
			$this->verif_exist_ordre_menu('bloc_graphique_famille');
			if ($this->graphiqueFamille())
				$this->chargeAutreNom('bloc_graphique_famille');
		}
		// les absences
		$this->verif_exist_ordre_menu('bloc_absences_famille');
		if ($this->absencesFamille())
			$this->chargeAutreNom('bloc_absences_famille');

		if (getSettingAOui("active_mod_discipline")) {
			// Discipline
			$this->verif_exist_ordre_menu('bloc_module_discipline_famille');
			if ($this->modDiscFamille())
				$this->chargeAutreNom('bloc_module_discipline_famille');
		}
		/***** Outils complémentaires de gestion des AID *****/
		$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
		if ($this->gestionAID())
			$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

		/***** Outils de gestion des Bulletins scolaires *****/
		if (getSettingAOui('active_bulletins')) {
			$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
			if ($this->bulletins())
				$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');
		}

		/***** Visualisation / Impression *****/
		$this->verif_exist_ordre_menu('bloc_visulation_impression');
		if ($this->impression())
			$this->chargeAutreNom('bloc_visulation_impression');

		/***** Gestion Notanet *****/
		$this->verif_exist_ordre_menu('bloc_notanet_fiches_brevet');
		if ($this->notanet())
			$this->chargeAutreNom('bloc_notanet_fiches_brevet');

		/***** Gestion années antérieures *****/
		$this->verif_exist_ordre_menu('bloc_annees_antérieures');
		if ($this->anneeAnterieure())
			$this->chargeAutreNom('bloc_annees_antérieures');

		/***** Gestion des messages *****/
		if (($_SESSION['statut'] == 'administrateur') ||
			($_SESSION['statut'] == 'scolarite') ||
			(($_SESSION['statut'] == 'cpe') && (getSettingAOui('GepiAccesPanneauAffichageCpe')))) {
			$this->verif_exist_ordre_menu('bloc_panneau_affichage');
			if ($this->messages())
				$this->chargeAutreNom('bloc_panneau_affichage');
		}

		/***** Module inscription *****/
		$this->verif_exist_ordre_menu('bloc_module_inscriptions');
		if ($this->inscription())
			$this->chargeAutreNom('bloc_module_inscriptions');

		/***** Module discipline *****/
		if (getSettingAOui("active_mod_discipline")) {
			$this->verif_exist_ordre_menu('bloc_module_discipline');
			if ($this->discipline())
				$this->chargeAutreNom('bloc_module_discipline');
		}

		/***** Module Modèle Open Office *****/
		$this->verif_exist_ordre_menu('bloc_modeles_Open_Office');
		if ($this->modeleOpenOffice())
			$this->chargeAutreNom('bloc_modeles_Open_Office');

		/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
		$this->verif_exist_ordre_menu('');
		$this->plugins();

		/***** Module Genese des classes *****/
		if (getSettingAOui("active_mod_genese_classes")) {
			$this->verif_exist_ordre_menu('bloc_Genese_classes');
			if ($this->geneseClasses())
				$this->chargeAutreNom('bloc_Genese_classes');
		}

		/***** Lien vers les flux rss pour les élèves s'ils sont activés *****/
		$this->verif_exist_ordre_menu('bloc_RSS');
		if ($this->fluxRSS())
			$this->chargeAutreNom('bloc_RSS');

		/***** Statut AUTRE *****/
		$this->verif_exist_ordre_menu('bloc_navigation');
		if ($this->statutAutre())
			$this->chargeAutreNom('bloc_navigation');

		/***** Module Epreuves blanches *****/
		$this->verif_exist_ordre_menu('bloc_epreuve_blanche');
		if ($this->epreuvesBlanches())
			$this->chargeAutreNom('bloc_epreuve_blanche');

		/***** Module Examen blanc *****/
		$this->verif_exist_ordre_menu('bloc_examen_blanc');
		if ($this->examenBlanc())
			$this->chargeAutreNom('bloc_examen_blanc');

		// 20181102
		/***** Module Actions *****/
		$this->verif_exist_ordre_menu('bloc_mod_actions');
		if ($this->mod_actions())
			$this->chargeAutreNom('bloc_mod_actions');

		/***** Module Admissions Post-Bac *****/
		$this->verif_exist_ordre_menu('bloc_admissions_post_bac');
		if ($this->adminPostBac())
			$this->chargeAutreNom('bloc_admissions_post_bac');

		/***** Module Gestionnaire d'AID *****/
		$this->verif_exist_ordre_menu('bloc_Gestionnaire_aid');
		if ($this->gestionEleveAID())
			$this->chargeAutreNom('bloc_Gestionnaire_aid');

		/***** Tri des menus *****/
		sort($this->titre_Menu);

	}

	protected function creeNouveauTitre($classe, $texte, $icone, $titre = "", $alt = "") {
		$this->titre_Menu[$this->a] = new menuGeneral();
		$this->titre_Menu[$this->a]->indexMenu = $this->a;
		$this->titre_Menu[$this->a]->classe = $classe;
		$this->titre_Menu[$this->a]->texte = $texte;
		$this->titre_Menu[$this->a]->icone['chemin'] = $this->cheminRelatif . $icone;
		$this->titre_Menu[$this->a]->icone['titre'] = $titre;
		$this->titre_Menu[$this->a]->icone['alt'] = $alt;
	}

	protected function creeNouveauItem($chemin, $titre, $expli) {
		$nouveauItem = new itemGeneral();
		$nouveauItem->chemin = $chemin;
		if ($nouveauItem->acces($nouveauItem->chemin, $this->statutUtilisateur)) {
			$nouveauItem->indexMenu = $this->a;
			$nouveauItem->titre = $titre;
			$nouveauItem->expli = $expli;
			$nouveauItem->indexItem = $this->b;
			$this->menu_item[] = $nouveauItem;
			$this->b++;
		}
		unset($nouveauItem);
	}

	protected function creeNouveauItemPlugin($chemin, $titre, $expli) {
		$nouveauItem = new itemGeneral();
		$nouveauItem->chemin = $chemin;
		$nouveauItem->indexMenu = $this->a;
		$nouveauItem->titre = $titre;
		$nouveauItem->expli = $expli;
		$nouveauItem->indexItem = $this->b;
		$this->menu_item[] = $nouveauItem;
		$this->b++;
		unset($nouveauItem);
	}

	protected function administration() {
		if ($this->statutUtilisateur == 'administrateur') {

			$this->b = 0;

			$this->creeNouveauItem('/gestion/accueil_sauve.php',
				"Sauvegarde de la base",
				"Sauvegarde de la base, les répertoires \"documents\" (contenant les documents joints aux cahiers de texte) et \"photos\" (contenant les photos du trombinoscope) ne seront pas sauvegardés.");

			$this->creeNouveauItem('/gestion/index.php',
				"Gestion générale",
				"Pour accéder aux outils de gestion (sécurité, configuration générale, bases de données, initialisation de GEPI).");
			$this->creeNouveauItem('/accueil_modules.php',
				"Gestion des modules",
				"Pour gérer les modules (cahier de textes, carnet de notes, absences, trombinoscope).");
			$this->creeNouveauItem('/accueil_admin.php',
				"Gestion des bases",
				"Pour gérer les bases (établissements, utilisateurs, matières, classes, " . $this->gepiSettings['denomination_eleves'] . ", " . $this->gepiSettings['denomination_responsables'] . ", AIDs).");
			if (getSettingValue('use_ent') == 'y') {
				// On ajoute la page du module ENT
				$this->creeNouveauItem('/mod_ent/index.php',
					"Liaison ENT",
					"Entrer en liaison avec l\'ENT pour gérer les utilisateurs et récupérer les logins pour le sso");
			}

			$this->creeNouveauItem('/a_lire.php',
				"À lire",
				"Quelques fichiers concernant Gepi.");

			if ($this->b > 0) {
				$this->creeNouveauTitre('accueil', "Administration", 'images/icons/configure.png');
				return true;
			}
		}
	}

	protected function absences_vie_scol() {

		$this->b = 0;
		if (getSettingValue("active_module_absence") == 'y') {

			$this->creeNouveauItem('/mod_absences/gestion/gestion_absences.php',
				"Gestion Absences, dispenses, retards et infirmeries",
				"Cet outil vous permet de gérer les absences, dispenses, retards et autres bobos à l'infirmerie des " . $this->gepiSettings['denomination_eleves'] . ".");

			$this->creeNouveauItem('/mod_absences/gestion/voir_absences_viescolaire.php',
				"Visualiser les absences",
				"Vous pouvez visualiser créneau par créneau la saisie des absences.");


		} else if (getSettingValue("active_module_absence") == '2' && ($this->statutUtilisateur == "scolarite" || $this->statutUtilisateur == "cpe")) {
			$this->creeNouveauItem("/mod_abs2/index.php",
				"Gestion des Absences",
				"Cet outil vous permet de gérer les absences des élèves");

			if (($this->statutUtilisateur == "cpe") && (getSettingAOui('AccesCpeAgregationAbs2'))) {
				$this->creeNouveauItem("/mod_abs2/admin/admin_table_agregation.php",
					"Agrégation des Absences",
					"Cet outil vous permet de remplir/vider la table d'agrégation des absences");
			}
		}

		if ((getSettingAOui("active_mod_abs_prof")) && ($this->statutUtilisateur == "scolarite" || $this->statutUtilisateur == "cpe")) {
			$this->creeNouveauItem("/mod_abs_prof/index.php",
				"Absences et remplacements de professeurs",
				"Cet outil vous permet de gérer les absences et remplacements ponctuels de professeurs");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Gestion des retards et absences", 'images/icons/absences.png');
			return true;
		}

	}

	protected function absences_profs() {

		$this->b = 0;

		if ($_SESSION['statut'] == 'professeur') {
			if (getSettingValue("active_module_absence_professeur") == 'y') {

				//  $nouveauItem = new itemGeneral();
				if (getSettingValue("active_module_absence") == 'y') {
					$this->creeNouveauItem("/mod_absences/professeurs/prof_ajout_abs.php",
						"Gestion des Absences",
						"Cet outil vous permet de gérer les absences des élèves");
				} else if (getSettingValue("active_module_absence") == '2' && !($this->statutUtilisateur == "scolarite" || $this->statutUtilisateur == "cpe")) {
					$this->creeNouveauItem("/mod_abs2/index.php",
						"Gestion des Absences",
						"Cet outil vous permet de gérer les absences des élèves");
				}

				if (getSettingAOui("active_mod_abs_prof")) {
					$this->creeNouveauItem("/mod_abs_prof/index.php",
						"Absences et remplacements de professeurs",
						"Cet outil vous permet de gérer les absences et remplacements ponctuels de professeurs");
				}

			} elseif (getSettingAOui("active_mod_abs_prof")) {

				$nouveauItem = new itemGeneral();

				$this->creeNouveauItem("/mod_abs_prof/index.php",
					"Absences et remplacements de professeurs",
					"Cet outil vous permet de gérer les absences et remplacements ponctuels de professeurs");

			}

		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Gestion des retards et absences", 'images/icons/absences.png');
			return true;
		}

	}

	private function saisie() {
		global $mysqli;

		$this->b = 0;

		$afficher_correction_validation = "n";
		if ($_SESSION['statut'] == 'scolarite') {
			// Il faut détecter les corrections d'appréciation de groupe et pas seulement celles d'élèves:
			$sql_correction_app = "(SELECT DISTINCT c.id, c.classe FROM classes c, 
								j_groupes_classes jgc, 
								matieres_app_corrections mac, 
								j_scol_classes jsc 
							WHERE c.id=jgc.id_classe AND 
								jgc.id_groupe=mac.id_groupe AND 
								jsc.id_classe=c.id AND 
								jsc.login='" . $_SESSION['login'] . "')
						UNION (SELECT DISTINCT c.id, c.classe FROM classes c, 
								j_eleves_classes jec, 
								j_aid_eleves jae, 
								matieres_app_corrections mac, 
								j_scol_classes jsc 
							WHERE c.id=jec.id_classe AND 
								jec.login=jae.login AND 
								jae.id_aid=mac.id_aid AND 
								jsc.id_classe=c.id AND 
								jsc.login='" . $_SESSION['login'] . "') 
							ORDER BY classe;";
		} elseif (($_SESSION['statut'] == 'professeur') && (getSettingAOui('autoriser_valider_correction_app_pp')) && (is_pp($_SESSION['login']))) {
			$sql_correction_app = "SELECT DISTINCT c.id, c.classe 
						FROM classes c, 
							j_eleves_classes jec, 
							j_eleves_professeurs jep, 
							matieres_app_corrections mac 
						WHERE c.id=jec.id_classe AND 
							jec.login=mac.login AND 
							jep.login=mac.login AND 
							jep.professeur='" . $_SESSION['login'] . "' ORDER BY classe;";
		} elseif (($_SESSION['statut'] == 'administrateur') || ($_SESSION['statut'] == 'secours')) {
			$sql_correction_app = "(SELECT DISTINCT c.id, c.classe FROM matieres_app_corrections mac, 
									j_groupes_classes jgc, 
									classes c 
								WHERE mac.id_groupe=jgc.id_groupe AND 
									jgc.id_classe=c.id) 
						UNION 
						(SELECT DISTINCT c.id, c.classe FROM matieres_app_corrections mac, 
									j_aid_eleves jae,
									j_eleves_classes jec, 
									classes c 
								WHERE mac.id_aid=jae.id_aid AND 
									jae.login=jec.login AND 
									jec.id_classe=c.id) 
								ORDER BY classe;";
		}
		if (isset($sql_correction_app)) {
			//echo "$sql_correction_app<br />";
			$resultat = mysqli_query($mysqli, $sql_correction_app);
			if ($resultat and ($resultat->num_rows > 0)) {
				$afficher_correction_validation = "y";
			}
			//echo "\$afficher_correction_validation=$afficher_correction_validation<br />";
		}

		if (getSettingAOui('active_bulletins')) {
			if (getSettingValue("active_module_absence") != '2' || getSettingValue("abs2_import_manuel_bulletin") == 'y') {
				$this->creeNouveauItem("/absences/index.php",
					"Bulletins : saisie des absences",
					"Cet outil vous permet de saisir les absences sur les bulletins.");
			}
		}

		if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur != 'professeur'))
			and (affiche_lien_cdt()))
			$this->creeNouveauItem("/cahier_texte/index.php",
				"Cahier de textes",
				"Cet outil vous permet de constituer un cahier de textes pour chacune de vos classes.");

		if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur != 'professeur'))
			and (getSettingValue("active_carnets_notes") == 'y'))
			$this->creeNouveauItem("/cahier_notes/index.php",
				"Carnet de notes : saisie des notes",
				"Cet outil vous permet de constituer un carnet de notes pour chaque période et de saisir les notes de toutes vos évaluations.");

		if (getSettingAOui('active_bulletins')) {
			if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur != 'professeur'))
				$this->creeNouveauItem("/saisie/index.php",
					"Bulletin : saisie des moyennes et des appréciations par matière",
					"Cet outil permet de saisir directement, sans passer par le carnet de notes, les moyennes et les appréciations du bulletin");

			if ($this->statutUtilisateur == 'secours')
				$this->creeNouveauItem("/saisie/saisie_secours_eleve.php",
					"Bulletin : saisie des moyennes et des appréciations pour un élève",
					"Cet outil permet de saisir/corriger directement, sans passer par le carnet de notes, les moyennes et les appréciations du bulletin pour un élève");

			if ($afficher_correction_validation == "y") {
				$texte_item = "Cet outil vous permet de valider les corrections d'appréciations proposées par des professeurs après la clôture d'une période.";
				if ($_SESSION['statut'] == 'scolarite') {
					$sql = "(SELECT 1=1 FROM matieres_app_corrections map, j_scol_classes jsc, j_groupes_classes jgc where jsc.login='" . $_SESSION['login'] . "' AND jsc.id_classe=jgc.id_classe AND jgc.id_groupe=map.id_groupe) 
				UNION (SELECT 1=1 FROM matieres_app_corrections map, 
							j_scol_classes jsc, 
							j_eleves_classes jec, 
							j_aid_eleves jae 
						where jsc.login='" . $_SESSION['login'] . "' AND 
							jsc.id_classe=jec.id_classe AND 
							jec.login=jae.login AND 
							jae.id_aid=map.id_aid);";
					$resultat = mysqli_query($mysqli, $sql);
					$nb_aid = $resultat->num_rows;
					if ($nb_aid > 0) {
						$texte_item .= "<br /><span style='color:red;'>Une ou des propositions requièrent votre attention.</span>\n";
					}
				} else {
					$texte_item .= "<br /><span style='color:red;'>Une ou des propositions requièrent votre attention.</span>\n";
				}
				$this->creeNouveauItem("/saisie/validation_corrections.php",
					"Correction des bulletins",
					$texte_item);
			}

			if ((($this->test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf") == 'yes'))
				or (($this->statutUtilisateur == 'scolarite') and (getSettingValue("GepiRubConseilScol") == 'yes'))
				or (($this->statutUtilisateur == 'cpe') and ((getSettingValue("GepiRubConseilCpe") == 'yes') || (getSettingValue("GepiRubConseilCpeTous") == 'yes')))
				or ($this->statutUtilisateur == 'secours')) {
				$this->creeNouveauItem("/saisie/saisie_avis.php",
					"Bulletin : saisie des avis du conseil",
					"Cet outil permet la saisie des avis du conseil de classe.");
			}

			if (!getSettingAOui('bullNoSaisieElementsProgrammes')) {
				if ((($_SESSION['statut'] == 'scolarite') && (getSettingAOui("ScolGererMEP"))) ||
					($_SESSION['statut'] == 'administrateur') ||
					($_SESSION['statut'] == 'professeur')) {
					$this->creeNouveauItem("/saisie/gerer_mep.php",
						"Éléments de programmes",
						"Cet outil permet de gérer les éléments de programmes associés aux appréciations des bulletins.");
				}
			}


		}

		// Saisie ECTS - ne doit être affichée que si l'utilisateur a bien des classes ouvrant droit à ECTS
		if ($this->statutUtilisateur == 'professeur') {
			$this->test_prof_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_groupes_professeurs jgp
				WHERE (jgc.saisie_ects = TRUE
				  AND jgc.id_groupe = jgp.id_groupe
				  AND jgp.login = '" . $this->loginUtilisateur . "')"));
			$this->test_prof_suivi_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_eleves_professeurs jep, j_eleves_groupes jeg
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_groupe = jeg.id_groupe
				AND jeg.login = jep.login AND jep.professeur = '" . $this->loginUtilisateur . "')"));
		} else {
			$sql = "SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_scol_classes jsc
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_classe = jsc.id_classe
				AND jsc.login = '" . $this->loginUtilisateur . "')";
			$resultat = mysqli_query($mysqli, $sql);
			$this->test_scol_ects = $resultat->num_rows;
		}
		$conditions_ects = ($this->gepiSettings['active_mod_ects'] == 'y' and
			(($this->test_prof_suivi != "0" and $this->gepiSettings['GepiAccesSaisieEctsPP'] == 'yes'
					and $this->test_prof_suivi_ects != "0")
				or ($this->statutUtilisateur == 'professeur'
					and $this->gepiSettings['GepiAccesSaisieEctsProf'] == 'yes'
					and $this->test_prof_ects != "0")
				or ($this->statutUtilisateur == 'scolarite'
					and $this->gepiSettings['GepiAccesSaisieEctsScolarite'] == 'yes'
					and $this->test_scol_ects != "0")
				or ($this->statutUtilisateur == 'secours')));
		if ($conditions_ects)
			$this->creeNouveauItem("/mod_ects/index_saisie.php", "Crédits ECTS", "Saisie des crédits ECTS");

		if (getSettingAOui('active_bulletins')) {
			// Pour un professeur, on n'appelle que les aid qui sont sur un bulletin

			$sql = "SELECT * FROM aid_config
								  WHERE display_bulletin = 'y'
								  OR bull_simplifie = 'y'
								  ORDER BY nom";
			if ($_SESSION['statut'] == 'professeur') {
				$sqlUtilisateur = "(SELECT indice_aid , id_utilisateur FROM j_aid_utilisateurs) UNION DISTINCT (SELECT  indice_aid , id_utilisateur FROM j_aid_utilisateurs)";
				$sql = "SELECT DISTINCT * FROM ($sql) AS t0 "
					. "INNER JOIN ($sqlUtilisateur) AS u "
					. "ON u.indice_aid = t0.indice_aid "
					. "WHERE u.id_utilisateur = \"" . $_SESSION['login'] . "\" ";

			}
			//echo $sql;
			$resultat = mysqli_query($mysqli, $sql);
			while ($obj = $resultat->fetch_object()) {
				$indice_aid = $obj->indice_aid;
				$sql = "SELECT * FROM j_aid_utilisateurs
				WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
				AND indice_aid = '" . $indice_aid . "');";
				//echo "$sql<br />";
				$call_prof = mysqli_query($mysqli, $sql);
				$nb_result = $call_prof->num_rows;
				//echo "\$nb_result=$nb_result<br />";
				if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
					$nom_aid = $obj->nom;
					$this->creeNouveauItem("/saisie/saisie_aid.php?indice_aid=" . $indice_aid,
						$nom_aid,
						"Cet outil permet la saisie des appréciations des " . $this->gepiSettings['denomination_eleves'] . " pour les $nom_aid.");
				}
			}


			//==============================
			// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
			// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
			if (file_exists('saisie/commentaires_types.php')) {
				$resultat = $nb_lignes = mysqli_query($mysqli, "SELECT 1=1 FROM j_eleves_professeurs
			WHERE professeur='" . $this->loginUtilisateur . "'");
				$nb_lignes = $resultat->num_rows;
				if ((($this->statutUtilisateur == 'professeur')
						and (getSettingValue("CommentairesTypesPP") == 'yes')
						and ($nb_lignes > 0))
					or (($this->statutUtilisateur == 'scolarite')
						and (getSettingValue("CommentairesTypesScol") == 'yes'))
					or (($this->statutUtilisateur == 'cpe')
						and (getSettingValue("CommentairesTypesCpe") == 'yes'))
				) {
					$this->creeNouveauItem("/saisie/commentaires_types.php",
						"Saisie de commentaires-types",
						"Permet de définir des commentaires-types pour l'avis du conseil de classe.");
				}
			}

		}

		if (getSettingAOui("SocleSaisieComposantes")) {
			if (getSettingAOui("SocleSaisieComposantes_" . $_SESSION["statut"])) {
				$this->creeNouveauItem("/saisie/saisie_socle.php",
					"Saisie Composantes du Socle",
					"Permet de saisir les bilans de composantes du Socle.");
			} elseif (($_SESSION['statut'] == "professeur") && (getSettingAOui("SocleSaisieComposantes_PP")) && (is_pp($_SESSION["login"]))) {
				$this->creeNouveauItem("/saisie/saisie_socle.php",
					"Saisie des Composantes du Socle",
					"Permet de saisir les bilans de composantes du Socle.");
			}

			if ((getSettingAOui("SocleOuvertureSaisieComposantes")) && (getSettingAOui("SocleOuvertureSaisieComposantes_" . $_SESSION["statut"]))) {
				$this->creeNouveauItem("/saisie/socle_verrouillage.php",
					"Verrouillage saisies Composantes Socle",
					"Permet de verrouiller/déverrouiller la saisie des bilans de composantes du Socle.");
			}

			if (acces("/saisie/socle_verif.php", $_SESSION["statut"])) {
				$this->creeNouveauItem("/saisie/socle_verif.php",
					"Vérification du remplissage des Composantes Socle",
					"Permet de vérifier l'état du remplissage/saisie des Composantes du Socle.");
			}

			if ((getSettingAOui("SocleImportComposantes")) && (getSettingAOui("SocleImportComposantes_" . $_SESSION["statut"]))) {
				$this->creeNouveauItem("/saisie/socle_import.php",
					"Import des Composantes Socle",
					"Permet d'importer depuis SACoche, les bilans de composantes du Socle.");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Saisie", 'images/icons/configure.png');
			return true;
		}

	}

	private function cahierTexteCPE() {
		$this->b = 0;

		$condition = (
			getSettingValue("active_cahiers_texte") == 'y' and (
				($this->statutUtilisateur == "cpe"
					and getSettingValue("GepiAccesCdtCpe") == 'yes'
					and getSettingValue("GepiAccesCdtCpeRestreint") != 'yes')
				or ($this->statutUtilisateur == "scolarite"
					and getSettingValue("GepiAccesCdtScol") == 'yes'
					and getSettingValue("GepiAccesCdtScolRestreint") != 'yes')
			));

		if ($condition) {
			$this->creeNouveauItem("/cahier_texte_2/see_all.php",
				"Cahier de textes",
				"Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements de tous les " . $this->gepiSettings['denomination_eleves']);
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Cahier de texte", 'images/icons/document.png');
			return true;
		}
	}

	protected function cahierTexteCPE_Restreint() {
		$this->b = 0;

		$condition = (
			getSettingValue("active_cahiers_texte") == 'y' and (
				($this->statutUtilisateur == "cpe" and getSettingValue("GepiAccesCdtCpeRestreint") == 'yes')
				or ($this->statutUtilisateur == "scolarite" and getSettingValue("GepiAccesCdtScolRestreint") == 'yes')
			));

		if ($condition) {
			$this->creeNouveauItem("/cahier_texte_2/see_all.php",
				"Cahier de textes des classes suivies",
				"Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements des " . $this->gepiSettings['denomination_eleves'] . " dont vous avez la responsabilité");
		}
		if ($this->b > 0)
			$this->creeNouveauTitre('accueil', "Cahier de texte", 'images/icons/document.png');
	}

	private function trombinoscope() {
		global $mysqli;
		//On vérifie si le module est activé

		$active_module_trombinoscopes = getSettingValue("active_module_trombinoscopes");
		$active_module_trombino_pers = getSettingValue("active_module_trombino_pers");

		$this->b = 0;

		$affiche = "yes";
		if (($this->statutUtilisateur == 'eleve')) {
			$GepiAccesEleTrombiTousEleves = getSettingValue("GepiAccesEleTrombiTousEleves");
			$GepiAccesEleTrombiElevesClasse = getSettingValue("GepiAccesEleTrombiElevesClasse");
			$GepiAccesEleTrombiPersonnels = getSettingValue("GepiAccesEleTrombiPersonnels");
			$GepiAccesEleTrombiProfsClasse = getSettingValue("GepiAccesEleTrombiProfsClasse");

			if (($GepiAccesEleTrombiTousEleves != "yes") &&
				($GepiAccesEleTrombiElevesClasse != "yes") &&
				($GepiAccesEleTrombiPersonnels != "yes") &&
				($GepiAccesEleTrombiProfsClasse != "yes")) {
				$affiche = 'no';
			} else {
				// Au moins un des droits est donné aux élèves.
				$affiche = 'yes';

				if (($active_module_trombinoscopes != 'y')
					&& ($GepiAccesEleTrombiPersonnels != "yes")
					&& ($GepiAccesEleTrombiProfsClasse != "yes")) {
					$affiche = 'no';
				}

				if (($active_module_trombino_pers != 'y')
					&& ($GepiAccesEleTrombiTousEleves != "yes")
					&& ($GepiAccesEleTrombiElevesClasse != "yes")) {
					$affiche = 'no';
				}
			}
		}

		if ($affiche == "yes"
			&& (($active_module_trombinoscopes == 'y')
				|| ($active_module_trombino_pers == 'y'))) {

			$this->creeNouveauItem("/mod_trombinoscopes/trombinoscopes.php",
				"Trombinoscopes",
				"Cet outil vous permet de visualiser les trombinoscopes des classes.");

			// On appelle les aid "trombinoscope"

			$sql = "SELECT * FROM aid_config
                        WHERE indice_aid= '" . getSettingValue("num_aid_trombinoscopes") . "'
                        ORDER BY nom";
			$call_data = mysqli_query($mysqli, $sql);
			$nb_aid = $call_data->num_rows;
			while ($obj = $call_data->fetch_object()) {
				$indice_aid = $obj->indice_aid;
				$call_prof = mysqli_query($mysqli, "SELECT * FROM j_aid_utilisateurs_gest
                                        WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
                                        AND indice_aid = '$indice_aid')");
				$nb_result = $call_prof->num_rows;
				if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
					$nom_aid = $obj->nom;
					$this->creeNouveauItem("/aid/index2.php?indice_aid=" . $indice_aid,
						$nom_aid,
						"Cet outil vous permet de visualiser quels " . $this->gepiSettings['denomination_eleves'] . " ont le droit d'envoyer/modifier leur photo.");
				}
			}


		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Trombinoscope", 'images/icons/trombinoscope.png');
			return true;
		}
	}

	protected function releve_notes() {
		$this->b = 0;

		$condition = ((getSettingValue("active_carnets_notes") == 'y')
			and
			((($this->statutUtilisateur == "scolarite") and (getSettingValue("GepiAccesReleveScol") == "yes"))
				or
				(
					($this->statutUtilisateur == "professeur") and
					(
						(getSettingValue("GepiAccesReleveProf") == "yes") or
						(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") or
						(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes") or
						((getSettingValue("GepiAccesReleveProfP") == "yes") and ($this->test_prof_suivi != "0"))
					)
				)
				or
				(($this->statutUtilisateur == "cpe") and ((getSettingValue("GepiAccesReleveCpe") == "yes") or (getSettingValue("GepiAccesReleveCpeTousEleves") == "yes")))));

		$condition2 = ($this->statutUtilisateur != "professeur" or
			(
				$this->statutUtilisateur == "professeur" and
				(
					(getSettingValue("GepiAccesMoyennesProf") == "yes") or
					(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") or
					(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
				)
			)
		);

		if ($condition)
			$this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
				"Visualisation et impression des relevés de notes",
				"Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes, " . $this->gepiSettings['denomination_eleve'] . " par " . $this->gepiSettings['denomination_eleve'] . ", classe par classe.");

		if ($condition && (($condition2) || (is_pp($this->loginUtilisateur))))
			$this->creeNouveauItem("/cahier_notes/index2.php",
				"Visualisation des moyennes des carnets de notes",
				"Cet outil vous permet de visualiser à l'écran les moyennes calculées d'après le contenu des carnets de notes, indépendamment de la saisie des moyennes sur les bulletins.");

		if (($condition) && (getSettingAOui('PeutDonnerAccesCNPeriodeCloseScol'))) {
			if ($this->statutUtilisateur == 'scolarite') {
				$this->creeNouveauItem("/cahier_notes/autorisation_exceptionnelle_saisie.php",
					"Autorisation exceptionnelle de saisie de CN",
					"Permet d'autoriser exceptionnellement un enseignant à saisir/corriger des notes du carnet de notes pour un enseignement sur une période partiellement close.");
			}
		}

		if (($condition) && ($this->statutUtilisateur == 'scolarite')) {
			$this->creeNouveauItem("/cahier_notes/extraction_notes_cn.php",
				"Extraction/export CSV des CN",
				"Permet d'effectuer une extraction/export CSV des notes des carnets de notes de telle ou telle classe.");
		}

		if (($this->statutUtilisateur == 'administrateur') && (getSettingAOui('active_carnets_notes'))) {
			$this->creeNouveauItem("/cahier_notes/autorisation_exceptionnelle_saisie.php",
				"Autorisation exceptionnelle de saisie de CN",
				"Permet d'autoriser exceptionnellement un enseignant à saisir/corriger des notes du carnet de notes pour un enseignement sur une période partiellement close.");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Relevés de notes", 'images/icons/releve.png');
			return true;
		}

	}

	protected function releve_ECTS() {
		$this->b = 0;

		$condition = ($this->gepiSettings['active_mod_ects'] == 'y'
			and ((($this->test_prof_suivi != "0")
					and ($this->gepiSettings['GepiAccesEditionDocsEctsPP'] == 'yes')
					and $this->test_prof_ects != "0")
				or (($this->statutUtilisateur == 'scolarite')
					and ($this->gepiSettings['GepiAccesEditionDocsEctsScolarite'] == 'yes')
					and $this->test_scol_ects != "0")
				or ($this->statutUtilisateur == 'secours'))
		);

		$chemin = array();
		if ($condition)
			$this->creeNouveauItem("/mod_ects/edition.php",
				"Génération des documents ECTS",
				"Cet outil vous permet de générer les documents ECTS (relevé, attestation, annexe) pour les classes concernées.");

		$recap_ects = ($this->gepiSettings['active_mod_ects'] == 'y'
			and (
				($this->statutUtilisateur == 'professeur'
					and $this->gepiSettings['GepiAccesRecapitulatifEctsProf'] == 'yes'
					and $this->test_prof_ects != '0')
				or ($this->statutUtilisateur == 'scolarite'
					and $this->gepiSettings['GepiAccesRecapitulatifEctsScolarite'] == 'yes'
					and $this->test_scol_ects != '0')
			)
		);
		if ($recap_ects)
			$this->creeNouveauItem("/mod_ects/recapitulatif.php",
				"Visualiser tous les ECTS",
				"Visualiser les tableaux récapitulatif par classe de tous les crédits ECTS.");

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Documents ECTS", 'images/icons/releve.png');
			return true;
		}
	}

	private function emploiDuTemps() {
		$this->b = 0;
		if (getSettingAOui('autorise_edt_tous') || (getSettingAOui('autorise_edt_admin') && $this->statutUtilisateur == 'administrateur')) {
			if (getSettingValue('edt_version_defaut') == "2") {
				if ($_SESSION["statut"] == 'responsable') {
					if (getSettingValue("autorise_edt_eleve") == "yes") {
						// on propose l'edt d'un élève, les autres enfants seront disponibles dans la page de l'edt.
						//$tab_tmp_ele = get_enfants_from_resp_login($this->loginUtilisateur,'', 'y');
						//$tmp_id_classe=get_id_classe_derniere_classe_ele($tab_tmp_ele[0]);
						//"/edt/index2.php?affichage=semaine&type_affichage=eleve&id_classe=".$tmp_id_classe."&login_eleve=".$tab_tmp_ele[0],
						$this->creeNouveauItem("/edt/index2.php",
							"Emploi du temps",
							"Cet outil permet la consultation de l'emploi du temps de votre enfant.");
					}
				} else if ($_SESSION["statut"] == 'eleve') {
					if (getSettingValue("autorise_edt_eleve") == "yes") {
						$this->creeNouveauItem("/edt/index2.php",
							"Emploi du temps",
							"Cet outil permet la consultation de votre emploi du temps.");
					}
				} else if ($_SESSION["statut"] == 'professeur') {
					$this->creeNouveauItem("/edt/index2.php",
						"Emploi du temps",
						"Cet outil permet la consultation de votre emploi du temps.");
				} else {
					$this->creeNouveauItem("/edt/index2.php",
						"Emploi du temps",
						"Cet outil permet la consultation/gestion de l'emploi du temps.");
				}

			} else {
				$this->creeNouveauItem("/edt_organisation/index_edt.php",
					"Emploi du temps",
					"Cet outil permet la consultation/gestion de l'emploi du temps.");

				if ($_SESSION["statut"] == 'responsable') {
					if (getSettingValue("autorise_edt_eleve") == "yes") {
						// on propose l'edt d'un élève, les autres enfants seront disponibles dans la page de l'edt.
						$tab_tmp_ele = get_enfants_from_resp_login($this->loginUtilisateur, '', 'y');
						$this->creeNouveauItem("/edt_organisation/edt_eleve.php?login_edt=" . $tab_tmp_ele[0],
							"Emploi du temps",
							"Cet outil permet la consultation de l'emploi du temps de votre enfant.");
					}
				} else if ($_SESSION["statut"] == 'eleve') {
					if (getSettingValue("autorise_edt_eleve") == "yes") {
						$this->creeNouveauItem("/edt_organisation/edt_eleve.php",
							"Emploi du temps",
							"Cet outil permet la consultation de votre emploi du temps.");
					}
				} else {
					$this->creeNouveauItem("/edt_organisation/edt_eleve.php",
						"Emploi du temps",
						"Cet outil permet la consultation de votre emploi du temps.");
				}
			}

			/*
		if ($this->b>0) {
			$this->creeNouveauTitre('accueil',"Emploi du temps",'images/icons/document.png');
			return true;
		}
		*/
		}

		if (getSettingAOui('active_edt_ical')) {
			if ((in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) ||
				(($_SESSION['statut'] == 'professeur') && (getSettingAOui('EdtIcalProfTous'))) ||
				(($_SESSION['statut'] == 'professeur') && (getSettingAOui('EdtIcalProf'))) ||
				(($_SESSION['statut'] == 'eleve') && (getSettingAOui('EdtIcalEleve'))) ||
				(($_SESSION['statut'] == 'responsable') && (getSettingAOui('EdtIcalResponsable')))
			) {
				$this->creeNouveauItem("/edt/index.php",
					"Emploi du temps ICAL",
					"Cet outil permet la consultation/gestion de l'emploi du temps importé depuis des fichiers ICAL/ICS.");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Emploi du temps", 'images/icons/document.png');
			return true;
		}
	}

	private function cahierTexteFamille() {
		$this->b = 0;

		$condition = (
			getSettingValue("active_cahiers_texte") == 'y' and (
				($this->statutUtilisateur == "responsable" and getSettingValue("GepiAccesCahierTexteParent") == 'yes')
				or ($this->statutUtilisateur == "eleve" and getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
			));

		if ($condition) {
			if ($this->statutUtilisateur == "responsable") {
				$this->creeNouveauItem("/cahier_texte/consultation.php",
					"Cahier de textes",
					"Permet de consulter les compte-rendus de séance et les devoirs à faire pour les " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes le " . $this->gepiSettings['denomination_responsable'] . ".");
			} else {
				$this->creeNouveauItem("/cahier_texte/consultation.php",
					"Cahier de textes",
					"Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements que vous suivez.");
			}
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Cahier de texte", 'images/icons/document.png');
			return true;
		}
	}

	private function releveNotesFamille() {
		$this->b = 0;

		$condition = (
			getSettingValue("active_carnets_notes") == 'y' and (
				($this->statutUtilisateur == "responsable" and getSettingValue("GepiAccesReleveParent") == 'yes' and is_eleve_avec_carnet_notes($_SESSION['login']))
				or ($this->statutUtilisateur == "eleve" and getSettingValue("GepiAccesReleveEleve") == 'yes' and is_eleve_avec_carnet_notes($_SESSION['login']))
			));

		if ($condition) {
			if ($this->statutUtilisateur == "responsable") {
				$this->creeNouveauItem("/cahier_notes/visu_releve_notes_ter.php",
					"Relevés de notes",
					"Permet de consulter les relevés de notes des " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes le " . $this->gepiSettings['denomination_responsable'] . ".");
			} else {
				$this->creeNouveauItem("/cahier_notes/visu_releve_notes_ter.php",
					"Relevés de notes",
					"Permet de consulter vos relevés de notes détaillés.");
			}
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Carnet de notes", 'images/icons/releve.png');
			return true;
		}
	}

	protected function notesCumulFamille() {
		$this->b = 0;

		$condition = (
			getSettingValue("active_carnets_notes") == 'y' and (
				($this->statutUtilisateur == "responsable" and getSettingValue("GepiAccesReleveParent") == 'yes' and is_responsable_avec_eleve_avec_carnet_notes($_SESSION['login']))
				or ($this->statutUtilisateur == "eleve" and getSettingAOui("GepiAccesReleveEleve") and is_eleve_avec_carnet_notes($_SESSION['login']))
			));

		if ($condition) {
			$this->creeNouveauItem("/cahier_notes/visu_cc_elv.php",
				"Notes cumulées",
				"Permet de consulter les notes cumulées.");
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Notes cumulées", 'images/icons/releve.png');
			return true;
		}
	}


	private function modDiscFamille() {
		$this->b = 0;

		$mod_disc_terme_incident = getSettingValue('mod_disc_terme_incident');
		if ($mod_disc_terme_incident == "") {
			$mod_disc_terme_incident = "incident";
		}
		$mod_disc_terme_sanction = getSettingValue('mod_disc_terme_sanction');
		if ($mod_disc_terme_sanction == "") {
			$mod_disc_terme_sanction = "sanction";
		}

		$temoin_disc = "";
		$cpt_disc = get_temoin_discipline();
		if ($cpt_disc > 0) {
			$temoin_disc = " <img src='./images/icons/flag2.gif' class='icone16' title=\"Un ou des " . $mod_disc_terme_incident . "s ou " . $mod_disc_terme_sanction . "s vous concernant ont été saisis.\" />";
		}

		if (($_SESSION['statut'] == 'eleve')) {
			if ((getSettingAOui('visuEleDisc')) || (getSettingAOui('visuEleDiscNature'))) {
				$this->creeNouveauItem("/mod_discipline/visu_disc.php",
					"Discipline" . $temoin_disc,
					ucfirst($mod_disc_terme_incident) . "s vous concernant.");
			}
		} elseif (($_SESSION['statut'] == 'responsable')) {
			if ((getSettingAOui('visuRespDisc')) || (getSettingAOui('visuRespDiscNature'))) {
				$this->creeNouveauItem("/mod_discipline/visu_disc.php",
					"Discipline" . $temoin_disc,
					ucfirst($mod_disc_terme_incident) . "s concernant les élèves/enfants dont vous êtes responsable.");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Carnet de notes", 'images/icons/releve.png');
			return true;
		}
	}

	private function equipePedaFamille() {
		$this->b = 0;

		$condition = (
			($this->statutUtilisateur == "responsable"
				and getSettingValue("GepiAccesEquipePedaParent") == 'yes')
			or ($this->statutUtilisateur == "eleve"
				and getSettingValue("GepiAccesEquipePedaEleve") == 'yes')
		);

		if ($condition) {
			if ($this->statutUtilisateur == "responsable") {
				$this->creeNouveauItem("/groupes/visu_profs_eleve.php",
					"Équipe pédagogique",
					"Permet de consulter l'équipe pédagogique des " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes " . $this->gepiSettings['denomination_responsable'] . ".");
			} else {
				$this->creeNouveauItem("/groupes/visu_profs_eleve.php",
					"Équipe pédagogique",
					"Permet de consulter l'équipe pédagogique qui vous concerne.");
			}
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Équipe pédagogique", 'images/icons/trombinoscope.png');
			return true;
		}
	}

	private function bulletinFamille() {
		$this->b = 0;

		if (getSettingAOui('active_bulletins')) {
			$condition = (
				($this->statutUtilisateur == "responsable"
					and getSettingValue("GepiAccesBulletinSimpleParent") == 'yes')
				or ($this->statutUtilisateur == "eleve"
					and getSettingValue("GepiAccesBulletinSimpleEleve") == 'yes')
			);

			if ($condition) {
				if ($this->statutUtilisateur == "responsable") {
					$sql = "(SELECT e.login, e.prenom, e.nom " .
						"FROM eleves e, resp_pers r, responsables2 re, j_eleves_classes jec " .
						"WHERE (" .
						"e.ele_id = re.ele_id AND " .
						"re.pers_id = r.pers_id AND " .
						"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2') AND 
						jec.login=e.login AND 
						jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe')))";
					if (getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
						$sql .= " UNION (SELECT e.login, e.prenom, e.nom " .
							"FROM eleves e, resp_pers r, responsables2 re, j_eleves_classes jec " .
							"WHERE (" .
							"e.ele_id = re.ele_id AND " .
							"re.pers_id = r.pers_id AND " .
							"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y' AND 
						jec.login=e.login AND 
						jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe')))";
					}
					$sql .= ";";
				} elseif ($this->statutUtilisateur == "eleve") {
					$sql = "SELECT id_classe FROM j_eleves_classes jec WHERE login = '" . $_SESSION['login'] . "' AND 
				jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe') ORDER BY jec.periode DESC LIMIT 1;";
				} else {
					$sql = "SELECT id FROM classes WHERE 1=2;";
				}
				//echo "$sql<br />";
				$res_classe_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($res_classe_eleve) > 0) {
					if ($this->statutUtilisateur == "responsable") {
						$this->creeNouveauItem("/prepa_conseil/index3.php",
							"Bulletins simplifiés",
							"Permet de consulter les bulletins simplifiés des " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes " . $this->gepiSettings['denomination_responsable'] . ".");
					} else {
						$this->creeNouveauItem("/prepa_conseil/index3.php",
							"Bulletins simplifiés",
							"Permet de consulter vos bulletins sous forme simplifiée.");
					}
				}
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Bulletins simplifiés", 'images/icons/bulletin_simp.png');
			return true;
		}
	}

	private function graphiqueFamille() {
		$this->b = 0;

		if (getSettingAOui('active_bulletins')) {
			$condition = (
				($this->statutUtilisateur == "responsable" and getSettingValue("GepiAccesGraphParent") == 'yes')
				or ($this->statutUtilisateur == "eleve" and getSettingValue("GepiAccesGraphEleve") == 'yes')
			);

			if ($condition) {
				if ($this->statutUtilisateur == "responsable") {
					$sql = "(SELECT e.login, e.prenom, e.nom " .
						"FROM eleves e, resp_pers r, responsables2 re, j_eleves_classes jec " .
						"WHERE (" .
						"e.ele_id = re.ele_id AND " .
						"re.pers_id = r.pers_id AND " .
						"r.login = '" . $_SESSION['login'] . "' AND (re.resp_legal='1' OR re.resp_legal='2') AND 
						jec.login=e.login AND 
						jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe')))";
					if (getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
						$sql .= " UNION (SELECT e.login, e.prenom, e.nom " .
							"FROM eleves e, resp_pers r, responsables2 re, j_eleves_classes jec " .
							"WHERE (" .
							"e.ele_id = re.ele_id AND " .
							"re.pers_id = r.pers_id AND " .
							"r.login = '" . $_SESSION['login'] . "' AND re.resp_legal='0' AND re.acces_sp='y' AND 
						jec.login=e.login AND 
						jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe')))";
					}
					$sql .= ";";
				} elseif ($this->statutUtilisateur == "eleve") {
					$sql = "SELECT id_classe FROM j_eleves_classes jec WHERE login = '" . $_SESSION['login'] . "' AND 
				jec.id_classe NOT IN (SELECT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe') ORDER BY jec.periode DESC LIMIT 1;";
				} else {
					$sql = "SELECT id FROM classes WHERE 1=2;";
				}
				$res_classe_eleve = mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($res_classe_eleve) > 0) {
					if ($this->statutUtilisateur == "responsable") {
						$this->creeNouveauItem("/visualisation/affiche_eleve.php",
							"Visualisation graphique",
							"Permet de visualiser sous forme graphique les résultats des " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes " . $this->gepiSettings['denomination_responsable'] . ", par rapport à la classe.");
					} else {
						$this->creeNouveauItem("/visualisation/affiche_eleve.php",
							"Visualisation graphique",
							"Permet de consulter vos résultats sous forme graphique, comparés à la classe.");
					}
				}
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Visualisation graphique", 'images/icons/graphes.png');
			return true;
		}
	}

	protected function absencesFamille() {
		$this->b = 0;
		$conditions2 = ($this->statutUtilisateur == "responsable" and
			getSettingValue("active_module_absence") == '2' and
			getSettingAOui("active_absences_parents"));

		$conditions3 = ($this->statutUtilisateur == "responsable" and
			getSettingValue("active_module_absence") == 'y' and
			getSettingAOui("active_absences_parents"));

		if ($conditions2) {
			$this->creeNouveauItem("/mod_abs2/bilan_parent.php",
				"Absences",
				"Permet de suivre les absences et les retards des élèves dont je suis " . $this->gepiSettings['denomination_responsable']);

		} else if ($conditions3) {
			$this->creeNouveauItem("/mod_absences/absences.php",
				"Absences",
				"Permet de suivre les absences et les retards des élèves dont je suis " . $this->gepiSettings['denomination_responsable']);
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Absences", 'images/icons/absences.png');
			return true;
		}
	}

	protected function infosFamille() {
		$this->b = 0;
		$conditions = ($this->statutUtilisateur == "responsable");

		if ($conditions) {
			$this->creeNouveauItem("/responsables/infos_parent.php",
				"Informations",
				"Permet de consulter les nom, prénom, date de naissance, adresse, téléphone,... que vous avez fournis pour éventuellement signaler une erreur ou une modification.");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Informations", 'images/icons/document.png');
			return true;
		}
	}

	protected function gestionAID() {
		global $mysqli;
		$this->b = 0;

		$sql = "SELECT distinct ac.indice_aid, ac.nom, ac.nom_complet
              FROM aid_config ac, aid a
              WHERE ac.outils_complementaires = 'y'
              AND a.indice_aid=ac.indice_aid
              ORDER BY ac.nom_complet";
		$call_data = mysqli_query($mysqli, $sql);
		$nb_aid = $call_data->num_rows;

		$sql2 = "SELECT id
            FROM archivage_types_aid
            WHERE outils_complementaires = 'y'";
		$resultat2 = mysqli_query($mysqli, $sql2);
		$nb_aid_annees_anterieures = $resultat2->num_rows;
		$nb_total = $nb_aid + $nb_aid_annees_anterieures;
		if ($nb_total != 0) {
			while ($obj = $call_data->fetch_object()) {
				$indice_aid = $obj->indice_aid;
				$nom_aid = $obj->nom;
				if ($this->AfficheAid($indice_aid)) {
					$this->creeNouveauItem("/aid/index_fiches.php?indice_aid=" . $indice_aid,
						$nom_aid . "<br /><span style='font-size:x-small'>" . $obj->nom_complet . "</span>",
						"Tableau récapitulatif, liste des " . $this->gepiSettings['denomination_eleves'] . ", ...");
				}
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil',
				"Outils de visualisation et d'édition des fiches projets",
				'images/icons/document.png');
			return true;
		}
	}

	private function AfficheAid($indice_aid) {
		if ($this->statutUtilisateur == "eleve") {
			/*
			$test = sql_query1("SELECT count(login) FROM j_aid_eleves
			WHERE login='".$this->loginUtilisateur."'
			AND indice_aid='".$indice_aid."' ");
			*/
			$sql = "SELECT 1=1 FROM j_aid_eleves jae, aid a 
					WHERE jae.login='" . $this->loginUtilisateur . "' AND 
						jae.indice_aid='" . $indice_aid . "' AND 
						a.indice_aid=jae.indice_aid AND 
						a.visibilite_eleve='y';";
			//echo "$sql<br />";
			$test = mysqli_num_rows(mysqli_query($GLOBALS['mysqli'], $sql));
			if ($test == 0)
				return false;
			else
				return true;
		} else {
			return true;
		}
	}

	protected function bulletins() {
		global $mysqli;
		$this->b = 0;

		if (getSettingAOui('active_bulletins')) {

			if (($this->statutUtilisateur == 'administrateur') && (getSettingAOui('GepiAdminValidationCorrectionBulletins')) && (acces('/saisie/validation_corrections.php', 'administrateur'))) {
				$afficher_correction_validation = "n";
				$sql = "SELECT 1=1 FROM matieres_app_corrections;";
				$test_mac = mysqli_query($mysqli, $sql);
				$nb_lignes = $test_mac->num_rows;


				if ($test_mac and $nb_lignes > 0) {
					$afficher_correction_validation = "y";
				}

				if ($afficher_correction_validation == "y") {
					$texte_item = "Cet outil vous permet de valider les corrections d'appréciations proposées par des professeurs après la clôture d'une période.";
					$texte_item .= "<br /><span style='color:red;'>Une ou des propositions requièrent votre attention.</span>\n";
					$this->creeNouveauItem("/saisie/validation_corrections.php",
						"Correction des bulletins",
						$texte_item);
				}
			}

			if ((($this->test_prof_suivi != "0")
					and (getSettingValue("GepiProfImprBul") == 'yes'))
				or ($this->statutUtilisateur != 'professeur')) {
				$this->creeNouveauItem("/bulletin/verif_bulletins.php",
					"Outil de vérification",
					"Permet de vérifier si toutes les rubriques des bulletins sont remplies.");
			}

			if (($this->statutUtilisateur == 'administrateur') || (($this->statutUtilisateur == 'scolarite') && (getSettingAOui('PeutDonnerAccesBullAppPeriodeCloseScol')))) {
				$this->creeNouveauItem("/bulletin/autorisation_exceptionnelle_saisie_app.php",
					"Autorisation exceptionnelle de saisie d'appréciations",
					"Permet d'autoriser exceptionnellement un enseignant à proposer une saisie d'appréciations pour un enseignement sur une période partiellement close.");
			}


			if (($this->statutUtilisateur == 'administrateur') || (($this->statutUtilisateur == 'scolarite') && (getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol')))) {
				$this->creeNouveauItem("/bulletin/autorisation_exceptionnelle_saisie_note.php",
					"Autorisation exceptionnelle de saisie de note",
					"Permet d'autoriser exceptionnellement un enseignant à saisir/corriger des notes de bulletins pour un enseignement sur une période partiellement close.");
			}

			if ($this->statutUtilisateur != 'professeur') {
				$this->creeNouveauItem("/bulletin/verrouillage.php",
					"Verrouillage/Déverrouillage des périodes",
					"Permet de verrouiller ou déverrouiller une période pour une ou plusieurs classes.");
			}

			//==========================================================
			//        Dispositif de restriction des accès aux appréciations pour les comptes responsables/eleves

			//        Sur quel droit s'appuyer pour donner l'accès?
			//            GepiAccesRestrAccesAppProfP : peut saisir les avis du conseil de classe pour sa classe
			if ((($this->test_prof_suivi != "0")
					and ($this->statutUtilisateur == 'professeur')
					and (getSettingValue("GepiAccesRestrAccesAppProfP") == 'yes'))
				or ($this->statutUtilisateur == 'scolarite')
				or ($this->statutUtilisateur == 'administrateur')) {
				$this->creeNouveauItem("/classes/acces_appreciations.php",
					"Accès des " . $this->gepiSettings['denomination_eleves'] . " et " . $this->gepiSettings['denomination_responsables'] . " aux appréciations",
					"Permet de définir quand les comptes " . $this->gepiSettings['denomination_eleves'] . " et " . $this->gepiSettings['denomination_responsables'] . " (s'ils existent) peuvent accéder aux appréciations des " . $this->gepiSettings['denomination_professeurs'] . " sur le bulletin et avis du conseil de classe.");
			}

			//==========================================================

			if ((($this->test_prof_suivi != "0")
					and ($this->statutUtilisateur == 'professeur')
					and (getSettingValue("GepiProfImprBul") == 'yes')
					and (getSettingValue("GepiProfImprBulSettings") == 'yes'))
				or (($this->statutUtilisateur == 'scolarite')
					and (getSettingValue("GepiScolImprBulSettings") == 'yes'))
				or (($this->statutUtilisateur == 'administrateur')
					and (getSettingValue("GepiAdminImprBulSettings") == 'yes'))
				or (($this->statutUtilisateur == 'cpe')
					and (getSettingValue("GepiCpeImprBulSettings") == 'yes'))
			) {

				if (getSettingValue('type_bulletin_par_defaut') == 'pdf') {
					$this->creeNouveauItem("/bulletin/param_bull_pdf.php",
						"Paramètres d'impression des bulletins",
						"Permet de modifier les paramètres de mise en page et d'impression des bulletins.");
				} else {
					$this->creeNouveauItem("/bulletin/param_bull.php",
						"Paramètres d'impression des bulletins",
						"Permet de modifier les paramètres de mise en page et d'impression des bulletins.");
				}
			}
		}

		if (getSettingAOui('active_bulletins')) {
			if ((($this->test_prof_suivi != "0")
					and (getSettingValue("GepiProfImprBul") == 'yes'))
				or (($this->statutUtilisateur == 'cpe')
					and (getSettingValue("GepiCpeImprBul") == 'yes'))
				or (($this->statutUtilisateur != 'professeur') and ($this->statutUtilisateur != 'cpe'))) {
				$this->creeNouveauItem("/bulletin/bull_index.php",
					"Visualisation et impression des bulletins",
					"Cet outil vous permet de visualiser à l'écran et d'imprimer les bulletins, classe par classe.");
			}

			if ($this->statutUtilisateur == 'administrateur') {
				$this->creeNouveauItem("/statistiques/index.php",
					"Extractions statistiques",
					"Cet outil vous permet d'extraire des données à des fins statistiques (des bulletins, ...).");

				$gepi_denom_mention = getSettingValue("gepi_denom_mention");
				if ($gepi_denom_mention == "") {
					$gepi_denom_mention = "mention";
				}

				$this->creeNouveauItem("/saisie/saisie_mentions.php",
					ucfirst($gepi_denom_mention) . "s des bulletins",
					"Cet outil vous permet de définir les " . $gepi_denom_mention . "s (<i>Félicitations, Encouragements,...</i>) des bulletins.");

				$this->creeNouveauItem("/saisie/saisie_vocabulaire.php",
					"Lapsus ou fautes de frappe",
					"Cet outil vous permet de définir les associations de mots avec et sans faute de frappe à contrôler lors de la saisie des bulletins.<br />Il arrive qu'un professeur fasse une faute de frappe, mais que le mot obtenu existe bien (<em>Il n'est alors pas souligné par le navigateur comme erroné... et la faute passe inaperçue</em>)");
			}

			$acces_saisie_engagement = "n";
			if (getSettingAOui('active_mod_engagements')) {

				$tab_engagements_avec_droit_saisie = get_tab_engagements_droit_saisie_tel_user($_SESSION['login']);
				if (count($tab_engagements_avec_droit_saisie['indice']) > 0) {
					$acces_saisie_engagement = "y";

					if (($_SESSION['statut'] == 'cpe') ||
						($_SESSION['statut'] == 'scolarite') ||
						($_SESSION['statut'] == 'administrateur') ||
						($_SESSION['statut'] == 'professeur')) {
						$this->creeNouveauItem("/mod_engagements/imprimer_documents.php",
							"Imprimer les documents concernant les engagements",
							"Les engagements sont par exemple les rôles de Délégué de classe, membre du Conseil d'Administration,...<br />Cet outil permet d'imprimer les convocations aux conseils de classe,...");
					}
				}
			}

			if (!getSettingAOui('bullNoSaisieElementsProgrammes')) {
				if ((($_SESSION['statut'] == 'scolarite') && (getSettingAOui("ScolGererMEP"))) ||
					($_SESSION['statut'] == 'administrateur') ||
					($_SESSION['statut'] == 'professeur')) {
					$this->creeNouveauItem("/saisie/gerer_mep.php",
						"Gérer les éléments de programmes",
						"Gérer les éléments de programmes inscrits dans les bulletins.");
				}
			}

			if ((($_SESSION['statut'] == 'administrateur') &&
					(getSettingAOui('active_mod_orientation'))
				) ||
				(($_SESSION['statut'] == 'scolarite') &&
					(getSettingAOui('active_mod_orientation')) &&
					(
						(getSettingAOui('OrientationSaisieTypeScolarite')) ||
						(getSettingAOui('OrientationSaisieOrientationScolarite')) ||
						(getSettingAOui('OrientationSaisieVoeuxScolarite'))
					)
				) ||
				(($_SESSION['statut'] == 'professeur') &&
					(getSettingAOui('active_mod_orientation')) &&
					(is_pp($_SESSION['login'])) &&
					(
						(getSettingAOui('OrientationSaisieTypePP')) ||
						(getSettingAOui('OrientationSaisieOrientationPP')) ||
						(getSettingAOui('OrientationSaisieVoeuxPP'))
					)
				) ||
				(($_SESSION['statut'] == 'cpe') &&
					(getSettingAOui('active_mod_orientation')) &&
					(
						(getSettingAOui('OrientationSaisieTypeCpe')) ||
						(getSettingAOui('OrientationSaisieOrientationCpe')) ||
						(getSettingAOui('OrientationSaisieVoeuxCpe'))
					)
				)
			) {
				$this->creeNouveauItem("/mod_orientation/index.php",
					"Orientation",
					"Saisir les voeux, l'orientation des élèves pour faire apparaître ces informations sur les bulletins.");
			}


		}

		if ($this->statutUtilisateur == 'scolarite') {
			$this->creeNouveauItem("/responsables/index.php",
				"Gestion des fiches " . $this->gepiSettings['denomination_responsables'],
				"Cet outil vous permet de modifier/supprimer/ajouter des fiches de " . $this->gepiSettings['denomination_responsables'] . " des " . $this->gepiSettings['denomination_eleves'] . ".");
		}

		// Ce n'est pas vraiment à sa place dans la rubrique Bulletins... mais il faudrait ajouter une rubrique Gestion juste pour ça
		if ($this->statutUtilisateur == 'scolarite') {
			$this->creeNouveauItem("/eleves/index.php",
				"Gestion des fiches " . $this->gepiSettings['denomination_eleves'],
				"Cet outil vous permet de modifier/supprimer/ajouter des fiches " . $this->gepiSettings['denomination_eleves'] . ".");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Bulletins scolaires", 'images/icons/bulletin_16.png');
			return true;
		}
	}

	private function impression() {
		global $mysqli;

		$this->b = 0;

		if (getSettingAOui('active_bulletins')) {
			$conditions_moyennes = (
				($this->statutUtilisateur != "professeur")
				or
				(
					($this->statutUtilisateur == "professeur") and
					(
						(getSettingValue("GepiAccesMoyennesProf") == "yes") or
						(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") or
						(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
					)
				)
			);

			$conditions_bulsimples = (
				(
					($this->statutUtilisateur != "eleve") and ($this->statutUtilisateur != "responsable")
				)
				and
				(
					($this->statutUtilisateur != "professeur") or
					(
						($this->statutUtilisateur == "professeur") and
						(
							(getSettingValue("GepiAccesBulletinSimpleProf") == "yes") or
							(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes") or
							(getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") == "yes")
						)
					)
				)
			);
		}

		$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualisation des équipes pédagogiques",
			"Ceci vous permet de connaître tous les " . $this->gepiSettings['denomination_professeurs'] . " des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.");

		if (($this->statutUtilisateur == 'scolarite') ||
			($this->statutUtilisateur == 'professeur') ||
			($this->statutUtilisateur == 'cpe')) {
			$this->creeNouveauItem("/groupes/visu_mes_listes.php",
				"Visualisation de mes élèves",
				"Ce menu vous permet de consulter vos listes d'" . $this->gepiSettings['denomination_eleves'] . " par groupe constitué et enseigné.");
		}

		if ((acces_modif_liste_eleves_grp_groupes()) &&
			(($this->statutUtilisateur == 'scolarite') ||
				($this->statutUtilisateur == 'professeur') ||
				($this->statutUtilisateur == 'cpe'))) {
			$this->creeNouveauItem("/groupes/grp_groupes_edit_eleves.php",
				"Correction des listes d'" . $this->gepiSettings['denomination_eleves'] . "",
				"Ce menu vous permet de corriger les listes d'" . $this->gepiSettings['denomination_eleves'] . " de certains groupes/enseignements.");
		}

		if ((($this->statutUtilisateur == 'cpe') && (getSettingAOui('GepiAccesTouteFicheEleveCpe'))) ||
			(($this->statutUtilisateur == 'cpe') && (getSettingAOui('CpeAccesUploadPhotosEleves')))
		) {
			$complement_texte = "";
			if (getSettingAOui('active_module_trombinoscopes')) {
				$complement_texte = "<br />Ce menu permet aussi d'uploader les photos des " . $this->gepiSettings['denomination_eleves'] . ".";
			}
			$this->creeNouveauItem("/eleves/index.php",
				"Gestion des fiches " . $this->gepiSettings['denomination_eleves'],
				"Cet outil vous permet de modifier/supprimer/ajouter des fiches " . $this->gepiSettings['denomination_eleves'] . "." . $complement_texte);
		}

		if (getSettingValue('active_mod_ooo') == 'y') {
			if (($this->statutUtilisateur == 'scolarite') ||
				($this->statutUtilisateur == 'administrateur') ||
				($this->statutUtilisateur == 'professeur') ||
				($this->statutUtilisateur == 'cpe')) {
				$this->creeNouveauItem("/mod_ooo/publipostage_ooo.php",
					"Publipostage OOo",
					"Ce menu vous permet d'effectuer des publipostages openDocument à l'aide des données des tables 'eleves' et 'classes'.");
			}
		}

		$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation d'un " . $this->gepiSettings['denomination_eleve'],
			"Ce menu vous permet de consulter dans une même page les informations concernant un " . $this->gepiSettings['denomination_eleve'] . " (enseignements suivis, bulletins, relevés de notes, " . $this->gepiSettings['denomination_responsables'] . ",...). Certains éléments peuvent n'être accessibles que pour certaines catégories de visiteurs.");

		if (affiche_lien_cdt()) {
			if ((($this->statutUtilisateur == "professeur") && (affiche_lien_cdt())) or
				(($this->statutUtilisateur == "cpe") && ((getSettingValue("GepiAccesCdtCpe") == "yes") || (getSettingValue("GepiAccesCdtCpeRestreint") == "yes"))) or
				(($this->statutUtilisateur == "scolarite") && ((getSettingValue("GepiAccesCdtScol") == "yes") || (getSettingValue("GepiAccesCdtScolRestreint") == "yes")))) {
				$this->creeNouveauItem("/cahier_texte_2/see_all.php",
					"Consultation des cahiers de textes",
					"Ce menu vous permet de consulter les cahiers de textes.");
				$this->creeNouveauItem("/cahier_texte_2/extract_tag.php",
					"Extraction des tags de CDT",
					"Ce menu vous permet d'extraire les notices de CDT portant tel ou tel tag (contrôle, AP, EPI,...).");
			}

			if (getSettingAOui('acces_archives_cdt')) {
				if ($this->statutUtilisateur == "professeur") {
					$this->creeNouveauItem("/documents/archives/index.php",
						"Mes archives de cahiers de textes",
						"Ce menu vous permet de consulter vos cahiers de textes des années précédentes.");
				} elseif (($this->statutUtilisateur == "cpe") || ($this->statutUtilisateur == "scolarite") || ($this->statutUtilisateur == "administrateur")) {
					$this->creeNouveauItem("/documents/archives/index.php",
						"Archives de cahiers de textes",
						"Ce menu vous permet de consulter les cahiers de textes des années précédentes.");
				}
			}

			if ($this->statutUtilisateur == "professeur") {
				$this->creeNouveauItem("/cahier_texte_2/documents_cdt.php",
					"Documents joints aux CDT",
					"Ce menu vous permet de consulter les documents joints à vos cahiers de textes.");
			}

		}

		$this->creeNouveauItem("/impression/impression_serie.php",
			"Impression PDF de listes",
			"Ceci vous permet d'imprimer en PDF des listes avec les " . $this->gepiSettings['denomination_eleves'] . ", à l'unité ou en série. L'apparence des listes est paramétrable.");

		if (getSettingAOui('active_bulletins')) {
			if (($this->statutUtilisateur == 'scolarite') ||
				(($this->statutUtilisateur == 'professeur') and ($this->test_prof_suivi != "0")) ||
				(($this->statutUtilisateur == 'cpe') and ((getSettingAOui('GepiRubConseilCpeTous')) || (getSettingAOui('GepiRubConseilCpe'))))
			) {
				$this->creeNouveauItem("/saisie/impression_avis.php",
					"Impression PDF des avis du conseil de classe",
					"Ceci vous permet d'imprimer en PDF la synthèse des avis du conseil de classe.");
			}

			if ($this->statutUtilisateur == 'scolarite') {
				$this->creeNouveauItem("/bulletin/impression_avis_grp.php",
					"Avis groupes-classes",
					"Cet outil vous permet d'imprimer les avis/appréciations des enseignants sur le groupe classe pour telle ou telle période.<br />Cela permet de disposer d'un récapitulatif des avis pour les différents enseignements de la classe.");
			}

		}

		if (($this->statutUtilisateur == 'scolarite') ||
			($this->statutUtilisateur == 'professeur') ||
			($this->statutUtilisateur == 'cpe')) {
			$this->creeNouveauItem("/groupes/mes_listes.php",
				"Exporter mes listes",
				"Ce menu permet de télécharger ses listes avec tous les " . $this->gepiSettings['denomination_eleves'] . " au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");
		}

		if (getSettingAOui('active_bulletins')) {
			$this->creeNouveauItem("/visualisation/index.php",
				"Outils graphiques de visualisation",
				"Visualisation graphique des résultats des " . $this->gepiSettings['denomination_eleves'] . " ou des classes, en croisant les données de multiples manières.");

			if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur != 'professeur')) {

				if ($this->statutUtilisateur != 'scolarite') {
					$this->creeNouveauItem("/prepa_conseil/index1.php",
						"Visualiser mes moyennes et appréciations des bulletins",
						"Tableau récapitulatif de vos moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
				} else {
					$this->creeNouveauItem("/prepa_conseil/index1.php",
						"Visualiser les moyennes et appréciations des bulletins",
						"Tableau récapitulatif des moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
				}

			}

			if ($conditions_moyennes) {
				$this->creeNouveauItem("/prepa_conseil/index2.php",
					"Visualiser toutes les moyennes d'une classe",
					"Tableau récapitulatif des moyennes d'une classe.");
			}

			if ($conditions_bulsimples) {
				$this->creeNouveauItem("/prepa_conseil/index3.php",
					"Visualiser les bulletins simplifiés",
					"Bulletins simplifiés d'une classe.");
			} elseif (($this->statutUtilisateur == 'professeur') && (getSettingValue("GepiAccesBulletinSimplePP") == "yes")) {
				$sql = "SELECT 1=1 FROM j_eleves_professeurs
				WHERE professeur='" . $this->loginUtilisateur . "';";

				$resultat = mysqli_query($mysqli, $sql);
				$test_pp = $resultat->num_rows;
				if ($test_pp > 0) {
					$this->creeNouveauItem("/prepa_conseil/index3.php",
						"Visualiser les bulletins simplifiés",
						"Bulletins simplifiés d'une classe.");
				}
			}

			$call_data = mysqli_query($mysqli, "SELECT * FROM aid_config
                            WHERE display_bulletin = 'y'
                            OR bull_simplifie = 'y'
                            ORDER BY nom");
			while ($obj = $call_data->fetch_object()) {
				$indice_aid = $obj->indice_aid;
				$call_prof = mysqli_query($mysqli, "SELECT * FROM j_aid_utilisateurs
                                          WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
                                          AND indice_aid = '" . $indice_aid . "')");
				$nb_result = $call_prof->num_rows;
				if ($nb_result != 0) {
					$nom_aid = $obj->nom;
					$this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=" . $indice_aid,
						"Visualiser des appréciations " . $nom_aid,
						"Cet outil permet la visualisation et l'impression des appréciations des " . $this->gepiSettings['denomination_eleves'] . " pour les " . $nom_aid . ".");
				}
			}
		}

		if (($this->statutUtilisateur == 'professeur') && (getSettingValue('GepiAccesGestElevesProfP') == 'yes')) {
			// Le professeur est-il professeur principal dans une classe au moins.
			$sql = "SELECT 1=1 FROM j_eleves_professeurs
			WHERE professeur='" . $this->loginUtilisateur . "';";
			$test = mysqli_query($mysqli, $sql);
			$nb_lignes = $test->num_rows;
			if ($nb_lignes > 0) {
				$gepi_prof_suivi = getSettingValue('gepi_prof_suivi');
				$this->creeNouveauItem("/eleves/index.php",
					"Gestion des " . $this->gepiSettings['denomination_eleves'],
					"Cet outil permet d'accéder aux informations des " . $this->gepiSettings['denomination_eleves'] . " dont vous êtes " . $gepi_prof_suivi . ".");
			}
		}

		if (getSettingAOui('active_bulletins')) {
			if ($this->statutUtilisateur != 'administrateur') {
				if (acces("/statistiques/index.php", $this->statutUtilisateur)) {
					$this->creeNouveauItem("/statistiques/index.php",
						"Extractions statistiques",
						"Cet outil vous permet d'extraire des données à des fins statistiques (des bulletins, ...).");
				}
			}
		}

		/*if((!getSettingAOui('active_cahiers_texte'))&&
		(
			(getSettingValue("acces_archives_cdt")=='')||(getSettingValue("acces_archives_cdt")=='y')
		)) {
	*/
		if ((getSettingValue("acces_archives_cdt") == '') || (getSettingValue("acces_archives_cdt") == 'y')) {
			if (($this->statutUtilisateur == 'scolarite') ||
				($this->statutUtilisateur == 'administrateur') ||
				($this->statutUtilisateur == 'professeur') ||
				($this->statutUtilisateur == 'cpe')) {
				$this->creeNouveauItem("/documents/archives/index.php",
					"Archives CDT",
					"Ce menu vous permet d'accéder aux archives du cahier de textes.");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Visualisation - Impression", 'images/icons/print.png');
			return true;
		}

	}

	private function notanet() {
		global $mysqli;
		$this->b = 0;

		$affiche = 'yes';
		if ($this->statutUtilisateur == 'professeur') {
			$sql = "SELECT DISTINCT g.*,c.classe FROM groupes g,
						  j_groupes_classes jgc,
						  j_groupes_professeurs jgp,
						  j_groupes_matieres jgm,
						  classes c,
						  notanet n
					  WHERE g.id=jgc.id_groupe AND
						  jgc.id_classe=n.id_classe AND
						  jgc.id_classe=c.id AND
						  jgc.id_groupe=jgp.id_groupe AND
						  jgp.login='" . $this->loginUtilisateur . "' AND
						  jgm.id_groupe=g.id AND
						  jgm.id_matiere=n.matiere
					  ORDER BY jgc.id_classe;";
			//echo "$sql<br />";
			$resultat = mysqli_query($mysqli, $sql);
			$nb_lignes = $resultat->num_rows;
			if ($nb_lignes == 0) {
				$affiche = 'no';
			}
		}

		if ((getSettingValue("active_notanet") == 'y') && (($affiche == 'yes') || (getSettingAOui("notanet_saisie_note_ouverte")))) {
			if ($this->statutUtilisateur == 'professeur') {
				$this->creeNouveauItem("/mod_notanet/index.php",
					"Notanet/Fiches Brevet",
					"Cet outil permet de saisir les appréciations pour les Fiches Brevet.");
			} else {
				$this->creeNouveauItem("/mod_notanet/index.php",
					"Notanet/Fiches Brevet",
					"Cet outil permet :<br />
				- d'effectuer les calculs et la génération du fichier CSV requis pour Notanet.
				L'opération renseigne également les tables nécessaires pour générer les Fiches brevet.<br />
				- de générer les fiches brevet");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Notanet/Fiches Brevet", 'images/icons/document.png');
			return true;
		}
	}

	private function anneeAnterieure() {
		global $mysqli;
		$this->b = 0;

		if (getSettingValue("active_annees_anterieures") == 'y') {

			if ($this->statutUtilisateur == 'administrateur') {
				$this->creeNouveauItem("/mod_annees_anterieures/index.php",
					"Années antérieures",
					"Cet outil permet de gérer et de consulter les données d'années antérieures (bulletins simplifiés,...).");
			} else {
				if ($this->statutUtilisateur == 'professeur') {
					$AAProfTout = getSettingValue('AAProfTout');
					$AAProfPrinc = getSettingValue('AAProfPrinc');
					$AAProfClasses = getSettingValue('AAProfClasses');
					$AAProfGroupes = getSettingValue('AAProfGroupes');

					if (($AAProfTout == "yes") || ($AAProfClasses == "yes") || ($AAProfGroupes == "yes")) {
						$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
							"Années antérieures",
							"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
					} elseif ($AAProfPrinc == "yes") {
						$sql = "SELECT 1=1 FROM classes c,
									j_eleves_professeurs jep
							WHERE jep.professeur='" . $this->loginUtilisateur . "' AND
									jep.id_classe=c.id
							ORDER BY c.classe";

						$resultat = mysqli_query($mysqli, $sql);
						$nb_lignes = $resultat->num_rows;
						$resultat->close();

						if ($nb_lignes > 0) {
							$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
								"Années antérieures",
								"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
						}
					}

				} elseif ($this->statutUtilisateur == 'scolarite') {
					$AAScolTout = getSettingValue('AAScolTout');
					$AAScolResp = getSettingValue('AAScolResp');

					if ($AAScolTout == "yes") {
						$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
							"Années antérieures",
							"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
					} elseif ($AAScolResp == "yes") {
						$sql = "SELECT 1=1 FROM j_scol_classes jsc
							WHERE jsc.login='" . $this->loginUtilisateur . "';";

						$resultat = mysqli_query($mysqli, $sql);
						$nb_lignes = $resultat->num_rows;
						$resultat->close();

						if ($nb_lignes > 0) {
							$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
								"Années antérieures",
								"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
						}
					}

				} elseif ($this->statutUtilisateur == 'cpe') {
					$AACpeTout = getSettingValue('AACpeTout');
					$AACpeResp = getSettingValue('AACpeResp');

					if ($AACpeTout == "yes") {
						$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
							"Années antérieures",
							"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
					} elseif ($AACpeResp == "yes") {
						$sql = "SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='" . $this->loginUtilisateur . "'";

						$resultat = mysqli_query($mysqli, $sql);
						$nb_lignes = $resultat->num_rows;
						$resultat->close();

						if ($nb_lignes > 0) {
							$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
								"Années antérieures",
								"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
						}

					}

				} elseif ($this->statutUtilisateur == 'responsable') {
					$AAResponsable = getSettingValue('AAResponsable');

					if ($AAResponsable == "yes") {
						// Est-ce que le responsable est bien associé à un élève?
						$sql = "SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e
				WHERE rp.pers_id=r.pers_id AND
					  r.ele_id=e.ele_id AND
					  rp.login='" . $this->loginUtilisateur . "'";

						$resultat = mysqli_query($mysqli, $sql);
						$nb_lignes = $resultat->num_rows;
						$resultat->close();
						if ($nb_lignes > 0) {
							$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
								"Années antérieures",
								"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
						}
					}

				} elseif ($this->statutUtilisateur == 'eleve') {
					$AAEleve = getSettingValue('AAEleve');

					if ($AAEleve == "yes") {
						$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
							"Années antérieures",
							"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
					}

				}

			}

		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Années antérieures", 'images/icons/document.png');
			return true;
		}
	}

	protected function messages() {
		$this->b = 0;

		$this->creeNouveauItem("/messagerie/index.php",
			"Panneau d'affichage",
			"Cet outil permet la gestion des messages à afficher sur la page d'accueil des utilisateurs.");


		$this->creeNouveauItem("/classes/dates_classes.php",
			"Événements classes",
			"Cet outil permet de saisir des événements pour telle ou telle classe et notamment les dates de conseils de classe.");

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Panneau d'affichage", 'images/icons/mail.png');
			return true;
		}
	}

	protected function inscription() {
		$this->b = 0;

		if (getSettingValue("active_inscription") == 'y') {
			$this->creeNouveauItem("/mod_inscription/inscription_config.php",
				"Configuration du module d'inscription/visualisation",
				"Configuration des différents paramètres du module");

			if (getSettingValue("active_inscription_utilisateurs") == 'y') {
				$this->creeNouveauItem("/mod_inscription/inscription_index.php",
					"Accès au module d'inscription/visualisation",
					"S'inscrire ou se désinscrire - Consulter les inscriptions");
			}

		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Inscription", 'images/icons/document.png');
			return true;
		}
	}

	protected function discipline() {
		global $mod_disc_terme_incident, $gepiPath;
		$this->b = 0;

		$temoin_disc = "";
		$cpt_disc = get_temoin_discipline_personnel();
		if ($cpt_disc > 0) {
			$DiscTemoinIncidentTaille = getPref($_SESSION['login'], 'DiscTemoinIncidentTaille', 16);
			$temoin_disc = " <img src='$gepiPath/images/icons/flag2.gif' width='$DiscTemoinIncidentTaille' height='$DiscTemoinIncidentTaille' title=\"Un ou des " . $mod_disc_terme_incident . "s ($cpt_disc) ont été saisis dans les dernières 24h ou depuis votre dernière connexion.\" />";
		}

		$this->creeNouveauItem("/mod_discipline/index.php",
			"Discipline" . $temoin_disc,
			"Signaler des incidents, prendre des mesures, des sanctions.");
		//}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Discipline", 'images/icons/document.png');
			return true;
		}

	}

	protected function modeleOpenOffice() {
		$this->b = 0;

		if (getSettingValue("active_mod_ooo") == 'y') {

			$this->creeNouveauItem("/mod_ooo/index.php",
				"Modèle Open Office",
				"Gérer les modèles Open Office dans Gepi et Utiliser les formulaires de saisie");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Modèles Open Office", 'mod_ooo/images/ico_gene_ooo.png');
			return true;
		}
	}

	private function plugins() {
		global $mysqli;
		$this->b = 0;

		$sql = "SELECT * FROM plugins WHERE ouvert = 'y' order by description;";
		//echo "$sql<br />";
		$query = mysqli_query($mysqli, $sql);
		while ($plugin = $query->fetch_object()) {
			$this->b = 0;
			$nomPlugin = $plugin->nom;
			$this->verif_exist_ordre_menu('bloc_plugin_' . $nomPlugin);

			// On offre la possibilité d'inclure un fichier functions_nom_du_plugin.php
			// Ce fichier peut lui-même contenir une fonction calcul_autorisation_nom_du_plugin voir plus bas.
			if (file_exists($this->cheminRelatif . "mod_plugins/" . $nomPlugin . "/functions_" . $nomPlugin . ".php"))
				include_once($this->cheminRelatif . "mod_plugins/" . $nomPlugin . "/functions_" . $nomPlugin . ".php");

			$sql = "SELECT DISTINCT lien_item, description_item, titre_item, user_statut FROM plugins_menus
		WHERE plugin_id = '" . $plugin->id . "' AND 
		user_statut='" . $_SESSION['statut'] . "'
		ORDER by titre_item;";
			//echo "$sql<br />";
			$querymenu = mysqli_query($mysqli, $sql);
			while ($menuItem = $querymenu->fetch_object()) {
				// On regarde si le plugin a prévu une surcharge dans le calcul de l'affichage de l'item dans le menu
				// On commence par regarder si une fonction du type calcul_autorisation_nom_du_plugin existe
				$nom_fonction_autorisation = "calcul_autorisation_" . $nomPlugin;
				if (function_exists($nom_fonction_autorisation)) {
					// Si une fonction du type calcul_autorisation_nom_du_plugin existe, on calcule le droit de l'utilisateur à afficher cet item dans le menu
					$result_autorisation = $nom_fonction_autorisation($this->loginUtilisateur, $menuItem->lien_item);
				} else {
					$result_autorisation = true;
				}
				if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
					$this->creeNouveauItemPlugin("/" . $menuItem->lien_item,
						supprimer_numero($menuItem->titre_item),
						$menuItem->description_item);
				}
			}

			if ($this->b > 0) {
				$descriptionPlugin = $plugin->description;
				$this->creeNouveauTitre('accueil', "$descriptionPlugin", 'images/icons/package.png');
			}
		}
	}

	protected function geneseClasses() {
		$this->b = 0;

		if ($_SESSION['statut'] == 'administrateur') {
			$this->creeNouveauItem("/mod_genese_classes/index.php",
				"Genèse des classes",
				"Effectuer la répartition des élèves par classes en tenant comptes des options,...");
		} elseif (($_SESSION['statut'] == 'scolarite') && (getSettingAOui('geneseClassesSaisieProfilsScol'))) {
			$this->creeNouveauItem("/mod_genese_classes/saisie_profils_eleves.php",
				"Genèse des classes",
				"Pointer les profils élèves en vue de la Genèse des classes futures.");
		} elseif (($_SESSION['statut'] == 'cpe') && (getSettingAOui('geneseClassesSaisieProfilsCpe'))) {
			$this->creeNouveauItem("/mod_genese_classes/saisie_profils_eleves.php",
				"Genèse des classes",
				"Pointer les profils élèves en vue de la Genèse des classes futures.");
		} elseif (($_SESSION['statut'] == 'professeur') && (getSettingAOui('geneseClassesSaisieProfilsPP')) && (is_pp($_SESSION['login']))) {
			$this->creeNouveauItem("/mod_genese_classes/saisie_profils_eleves.php",
				"Genèse des classes",
				"Pointer les profils élèves en vue de la Genèse des classes futures.");
		}


		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Genèse des classes", 'images/icons/document.png');
			return true;
		}
	}

	private function fluxRSS() {
		$this->b = 0;

		if (getSettingAOui("active_cahiers_texte")) {
			if (getSettingValue("rss_cdt_eleve") == 'y' and $this->statutUtilisateur == "eleve") {
				// Les flux rss sont ouverts pour les élèves
				$this->canal_rss_flux = 1;

				// A vérifier pour les cdt
				if (getSettingValue("rss_acces_ele") == 'direct') {
					// echo "il y a un flux RSS direct";
					$uri_el = retourneUri($this->loginUtilisateur, $this->test_https, 'cdt');
					$this->canal_rss = array("lien" => $uri_el["uri"],
						"texte" => $uri_el["text"],
						"mode" => 1,
						"expli" => "En cliquant sur la cellule de gauche,
											vous pourrez récupérer votre URI (<em>si vous avez activé le javascript sur votre navigateur</em>).
											<br />
											<br />
											<em style='font-size:small'>Avec cette URL, vous pourrez consulter les travaux à faire sans devoir vous connecter dans Gepi.<br />Firefox, Internet Explorer,... savent lire les flux RSS.<br />Il existe également des lecteurs de flux RSS pour les SmartPhone,...</em>");
				} elseif (getSettingValue("rss_acces_ele") == 'csv') {
					$this->canal_rss = array("lien" => "", "texte" => "", "mode" => 2, "expli" => "");
				}

				$this->creeNouveauTitre('accueil', "Votre flux RSS", 'images/icons/rss.png');
				return true;
			} elseif (getSettingValue("rss_cdt_responsable") == 'y' and $this->statutUtilisateur == "responsable") {
				// Les flux rss sont ouverts pour les élèves
				$this->canal_rss_flux = 1;

				// A vérifier pour les cdt
				if (getSettingValue("rss_acces_ele") == 'direct') {
					// echo "il y a un flux RSS direct";
					$this->canal_rss = array("mode" => 1,
						"expli" => "En cliquant sur la cellule de gauche,
											vous pourrez récupérer votre URI (<em>si vous avez activé le javascript sur votre navigateur</em>).
											<br />
											<br />
											<em style='font-size:small'>Avec cette URL, vous pourrez consulter les travaux à faire sans devoir vous connecter dans Gepi.<br />Firefox, Internet Explorer,... savent lire les flux RSS.<br />Il existe également des lecteurs de flux RSS pour les SmartPhone,...</em>");

					$tab_ele_resp = get_enfants_from_resp_login($this->loginUtilisateur, 'avec_classe', "yy");
					if (count($tab_ele_resp) > 2) {
						$cpt_ele_rss = 0;
						$this->canal_rss_plus = "";
						for ($loop = 0; $loop < count($tab_ele_resp); $loop += 2) {
							$uri_el = retourneUri($tab_ele_resp[$loop], $this->test_https, 'cdt');
							$this->canal_rss_plus .= $tab_ele_resp[$loop + 1] . "<br /><a href='" . $uri_el["uri"] . "'>" . $uri_el["text"] . "</a><br />";
						}
					} elseif (count($tab_ele_resp) == 2) {

						$uri_el = retourneUri($tab_ele_resp[0], $this->test_https, 'cdt');

						$this->canal_rss['lien'] = $uri_el["uri"];
						$this->canal_rss['texte'] = $uri_el["text"];
					} else {
						$this->canal_rss['lien'] = "Aucune URL";
						$this->canal_rss['texte'] = "Aucun eleve trouvé.";
					}
				} elseif (getSettingValue("rss_acces_ele") == 'csv' and $this->statutUtilisateur == "responsable") {
					$this->canal_rss = array("lien" => "", "texte" => "", "mode" => 2, "expli" => "");
				}

				$this->creeNouveauTitre('accueil', "Votre flux RSS", 'images/icons/rss.png');
				return true;
			}
		}
	}

	protected function statutAutre() {
		global $mysqli;

		$this->b = 0;

		if ($_SESSION["statut"] == 'autre') {
			// On récupère la liste des fichiers à autoriser
			require_once("utilisateurs/creer_statut_autorisation.php");
			$nbre_a = count($autorise_statuts_personnalise);

			// On démarre à 1, parce que l'indice 0 correspond à la page d'accueil, à Gérer mon compte,...
			for ($i = 1; $i < count($autorise_statuts_personnalise); $i++) {
				$droit_courant = $autorise_statuts_personnalise[$i];
				foreach ($droit_courant as $nom_fichier => $commentaire) {
					// On récupère le droit sur le fichier
					$sql_f = "SELECT autorisation FROM droits_speciaux
						  WHERE id_statut = '" . $_SESSION["statut_special_id"] . "'
						  AND nom_fichier = '" . $nom_fichier . "'
						  ORDER BY id";
					$query_f = mysqli_query($mysqli, $sql_f) or trigger_error('Impossible de trouver le droit : ' . mysqli_error($GLOBALS["mysqli"]), E_USER_WARNING);
					if ($query_f->num_rows >= 1) {
						$rep_f = old_mysql_result($query_f, 0, "autorisation");
					} else {
						$rep_f = '';
					}

					if ($rep_f == 'V') {
						$test = explode(".", $nom_fichier); // On teste pour voir s'il y a un .php à la fin de la chaîne

						if (!isset($test[1])) {
							// rien, la vérification se fait dans le module EdT
							// ou alors dans les autres modules spécifiés
						} else {
							if ($i == 4) {
								// Dans le cas de la saisie des absences, il faut ajouter une variable pour le GET
								$var = '?type=A';
							} else {
								$var = '';
							}

							$this->creeNouveauItem($_SESSION["gepiPath"] . $nom_fichier . $var,
								$menu_accueil[$i][0],
								$menu_accueil[$i][1]);
						}

					}

					// On ne teste que le premier fichier associé à un droit particulier
					break;
				}
			}

			/*
	  $a = 1;
	  while($a < $nbre_a){
		$numitem=$a;
		// On récupère le droit sur le fichier
		$sql_f = "SELECT autorisation FROM droits_speciaux
				  WHERE id_statut = '".$_SESSION["statut_special_id"]."'
				  AND nom_fichier = '".$autorise_statuts_personnalise[$a][0]."'
				  ORDER BY id";

		$query_f = mysqli_query($mysqli, $sql_f) OR trigger_error('Impossible de trouver le droit : '.mysqli_error($GLOBALS["mysqli"]), E_USER_WARNING);
		$nbre = $query_f->num_rows;

		if ($nbre >= 1) {
		  $rep_f = old_mysql_result($query_f, 0, "autorisation");
		}else{
		  $rep_f = '';
		}

		if ($rep_f == 'V') {
		  $test = explode(".", $autorise_statuts_personnalise[$a][0]); // On teste pour voir s'il y a un .php à la fin de la chaîne

		  if (!isset($test[1])) {
				// rien, la vérification se fait dans le module EdT
				// ou alors dans les autres modules spécifiés
		  }else{
			if($a == 4){
				// Dans le cas de la saisie des absences, il faut ajouter une variable pour le GET
				$var = '?type=A';
			}else{
				$var = '';
			}

			$this->creeNouveauItem($_SESSION["gepiPath"].$autorise_statuts_personnalise[$a][0].$var,
					$menu_accueil[$a][0],
					$menu_accueil[$a][1]);
		  }

		}

		$a++;
	  }
	*/

		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Navigation", 'images/icons/document.png');
			return true;
		}
	}

	protected function epreuvesBlanches() {
		$this->b = 0;

		//insert into setting set name='active_mod_epreuve_blanche', value='y';
		if (getSettingValue("active_mod_epreuve_blanche") == 'y') {
			$this->creeNouveauItem("/mod_epreuve_blanche/index.php",
				"Épreuves blanches",
				"Organisation d'épreuves blanches,...");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Épreuves blanches", 'images/icons/document.png');
			return true;
		}
	}

	protected function examenBlanc() {
		$this->b = 0;

		//insert into setting set name='active_mod_epreuve_blanche', value='y';

		if (getSettingValue("active_mod_examen_blanc") == 'y') {
			$acces_mod_examen_blanc = "y";
			if ($_SESSION['statut'] == 'professeur') {
				$acces_mod_examen_blanc = "n";

				if ((is_pp($_SESSION['login'])) && (getSettingValue('modExbPP') == 'yes')) {
					$acces_mod_examen_blanc = "y";
				}
			}

			if ($acces_mod_examen_blanc == "y") {
				$this->creeNouveauItem("/mod_examen_blanc/index.php",
					"Examens blancs",
					"Organisation d'examens blancs,...");
			}
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Examens blancs", 'images/icons/document.png');
			return true;
		}
	}

	protected function adminPostBac() {
		$this->b = 0;

		if (getSettingValue("active_mod_apb") == 'y') {
			$this->creeNouveauItem("/mod_apb/index.php",
				"Export APB",
				"Export du fichier XML pour le système Admissions Post-Bac");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Export Post-Bac", 'images/icons/document.png');
			return true;
		}
	}

	private function gestionEleveAID() {
		global $mysqli;
		$this->b = 0;

		if (getSettingValue("active_mod_gest_aid") == 'y') {

			$sql = "SELECT * FROM aid_config ";
			// on exclue la rubrique permettant de visualiser quels élèves ont le droit d'envoyer/modifier leur photo
			$flag_where = 'n';

			if (getSettingValue("num_aid_trombinoscopes") != "") {
				$sql .= "WHERE indice_aid!= '" . getSettingValue("num_aid_trombinoscopes") . "'";
				$flag_where = 'y';
			}

			// si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
			$test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

			if (($test_plugin == 'y') and (getSettingValue("indice_aid_autorisations_publi") != ""))
				if ($flag_where == 'n')
					$sql .= "WHERE indice_aid!= '" . getSettingValue("indice_aid_autorisations_publi") . "'";
				else
					$sql .= "and indice_aid!= '" . getSettingValue("indice_aid_autorisations_publi") . "'";

			$sql .= " ORDER BY nom";

			$call_data = mysqli_query($mysqli, $sql);
			while ($obj = $call_data->fetch_object()) {
				$indice_aid = $obj->indice_aid;
				$call_prof1 = mysqli_query($mysqli, "SELECT *
                          FROM j_aid_utilisateurs_gest
                          WHERE indice_aid = '" . $indice_aid . "' and id_utilisateur='" . $this->loginUtilisateur . "'");
				$nb_result1 = $call_prof1->num_rows;
				$call_prof2 = mysqli_query($mysqli, "SELECT *
                          FROM j_aidcateg_super_gestionnaires
                          WHERE indice_aid = '" . $indice_aid . "' and id_utilisateur='" . $this->loginUtilisateur . "'");
				$nb_result2 = $call_prof2->num_rows;
				if (($nb_result1 != 0) or ($nb_result2 != 0)) {
					//$nom_aid = @old_mysql_result($call_data, $i, "nom");
					$nom_aid = $obj->nom;
					if ($nb_result2 != 0)
						$this->creeNouveauItem("/aid/index2.php?indice_aid=" . $indice_aid,
							$nom_aid,
							"Cet outil vous permet de gérer les groupes (création, suppression, modification).");
					else
						$this->creeNouveauItem("/aid/index2.php?indice_aid=" . $indice_aid,
							$nom_aid,
							"Cet outil vous permet de gérer l'appartenance des élèves aux différents groupes.");
				}
			}

		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Gestion des AID", 'images/icons/document.png');
			return true;
		}
	}

	protected function cahierTexte_Visa() {
		$this->b = 0;

		if (getSettingValue("GepiAccesCdtVisa") == 'yes') {
			$this->creeNouveauItem("/cahier_texte_admin/visa_ct.php",
				"Visa des cahiers de textes",
				"Voir et viser les cahiers de textes");
		}

		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Visa CDT", 'images/icons/document.png');
		}

	}

	protected function listesPerso() {
		$this->b = 0;

		if (getSettingAOui("GepiListePersonnelles")) {
			$this->creeNouveauItem("/mod_listes_perso/index.php", "Listes personnelles", "Créer et imprimer des listes personnelles");
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', "Listes personnelles", 'images/icons/document.png');
		}

	}

	protected function livret() {
		$this->b = 0;
		if (getSettingAOui("active_module_LSUN")) {
			$this->creeNouveauItem("/mod_LSUN/index.php", "LSU", "LSU : remplir et voir les APs, les EPIs, les parcours");
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', 'Livret Scolaire', 'images/icons/document.png');
		}

	}

	// 20181102
	protected function mod_actions() {
		$this->b = 0;
		if (getSettingAOui("active_mod_actions")) {
			$terme_mod_action = getSettingValue('terme_mod_action');
			if (acces_mod_action()) {
				$this->creeNouveauItem("/mod_actions/index.php", $terme_mod_action . 's', $terme_mod_action . 's' . "&nbsp;: Définir, consulter, pointer les " . $terme_mod_action . 's');
			} elseif (getSettingAOui('mod_actions_affichage_familles')) {
				if ($_SESSION['statut'] == 'responsable') {
					$this->creeNouveauItem("/mod_actions/accueil.php", $terme_mod_action . 's', $terme_mod_action . 's' . "&nbsp;: Consulter les " . $terme_mod_action . "s dans lesquelles votre enfant est inscrit(e).");
				} elseif ($_SESSION['statut'] == 'eleve') {
					$this->creeNouveauItem("/mod_actions/accueil.php", $terme_mod_action . 's', $terme_mod_action . 's' . "&nbsp;: Consulter les " . $terme_mod_action . "s dans lesquelles vous êtes inscrit(e).");
				}
			}
		}
		if ($this->b > 0) {
			$this->creeNouveauTitre('accueil', $terme_mod_action . 's', 'images/icons/document.png');
		}
	}

	protected function verif_exist_ordre_menu($_item) {
		if (!isset($this->ordre_menus[$_item]))
			$this->ordre_menus[$_item] = max($this->ordre_menus) + 1;
		$this->a = $this->ordre_menus[$_item];
	}

	protected function chargeOrdreMenu($ordre_menus) {
		global $mysqli;
		//$this->ordre_menus=$ordre_menus;
		$sql = "SHOW TABLES LIKE 'mn_ordre_accueil'";

		$resultat = mysqli_query($mysqli, $sql);
		$nb_lignes = $resultat->num_rows;

		if ($nb_lignes > 0) {
			$sql2 = "SELECT bloc, num_menu
                FROM mn_ordre_accueil
                WHERE statut
                LIKE '$this->statutUtilisateur' ";

			$resultat2 = mysqli_query($mysqli, $sql2);
			$nb_lignes2 = $resultat2->num_rows;
			if ($nb_lignes2 > 0) {
				while ($lig_log = $resultat2->fetch_object()) {
					$this->ordre_menus[$lig_log->bloc] = $lig_log->num_menu;
				}
			} else {
				$this->ordre_menus = $ordre_menus;
			}
		} else {
			$this->ordre_menus = $ordre_menus;
		}
	}

	private function chargeAutreNom($bloc) {
		global $mysqli;

		$sql1 = "SHOW TABLES LIKE 'mn_ordre_accueil'";

		$resultat1 = mysqli_query($mysqli, $sql1);
		$nb_lignes1 = $resultat1->num_rows;

		if ($nb_lignes1 > 0) {
			$sql = "SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE '$this->statutUtilisateur'
			AND nouveau_nom NOT LIKE ''
			;";

			$resultat = mysqli_query($mysqli, $sql);
			$nb_lignes = $resultat->num_rows;
			if ($nb_lignes > 0) {
				$tmp_obj_nouveau_nom = $resultat->fetch_object();
				$this->titre_Menu[$this->a]->texte = $tmp_obj_nouveau_nom->nouveau_nom;
			}
		}
	}

}

?>
