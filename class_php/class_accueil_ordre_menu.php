<?php

/*
 * $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
 * Description of class_accueil_ordre_menu
 *
 * @author regis
 */
class class_accueil_ordre_menu extends class_page_accueil {



/**
 *
 *
 * Charge les menus Accueil en fonction du statut passé en argument
 *
 * @author regis
 */

  function __construct($statut, $gepiSettings, $niveau_arbo,$ordre_menus) {

	switch ($niveau_arbo){
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
	$this->gepiSettings=$gepiSettings;
	$this->loginUtilisateur=$_SESSION['login'];

	$this->chargeOrdreMenu($ordre_menus);

	// On teste si on l'utilisateur est un prof avec des matières. Si oui, on affiche les lignes relatives au cahier de textes et au carnet de notes

	$this->test_prof_matiere = 1;

// On teste si le l'utilisateur est prof de suivi. Si oui on affiche la ligne relative remplissage de l'avis du conseil de classe
	$this->test_prof_suivi = 1;

	$this->test_https = 'y'; // pour ne pas avoir à refaire le test si on a besoin de l'URL complète (rss)
	if (!isset($_SERVER['HTTPS'])
		OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
		OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
	{
		$this->test_https = 'n';
	}

/***** Outils d'administration *****/
	$this->verif_exist_ordre_menu('bloc_administration');
	if ($this->administration())
	$this->chargeAutreNom('bloc_administration');

/***** Outils de gestion des absences vie scolaire *****/
	$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
	if ($this->absences_vie_scol())
	$this->chargeAutreNom('bloc_absences_vie_scol');

/***** Outils de gestion des absences par les professeurs *****/
	$this->verif_exist_ordre_menu('bloc_absences_professeur');
	if ($this->absences_profs())
	$this->chargeAutreNom('bloc_absences_professeur');

/***** Saisie ***********/
	$this->verif_exist_ordre_menu('bloc_saisie');
	if ($this->saisie())
	$this->chargeAutreNom('bloc_saisie');

/***** Cahier de texte CPE ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_CPE');
	if ($this->cahierTexteCPE()){
	  $this->chargeAutreNom('bloc_Cdt_CPE');
	}

/***** Cahier de texte CPE Restreint ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_CPE_Restreint');
	if ($this->cahierTexteCPE_Restreint()){
	  $this->chargeAutreNom('bloc_Cdt_CPE_Restreint');
	}

/***** Visa Cahier de texte Scolarite ***********/
	$this->verif_exist_ordre_menu('bloc_Cdt_Visa');
	if ($this->cahierTexte_Visa()){
	  $this->chargeAutreNom('bloc_Cdt_Visa');
	}

/***** gestion des trombinoscopes : module de Christian Chapel ***********/
	$this->verif_exist_ordre_menu('bloc_trombinoscope');
	if ($this->trombinoscope())
	$this->chargeAutreNom('bloc_trombinoscope');

/***** Outils de relevé de notes *****/
	$this->verif_exist_ordre_menu('bloc_releve_notes');
	if ($this->releve_notes())
	$this->chargeAutreNom('bloc_releve_notes');

/***** Vision des évaluations cumules *****/
    if(getSettingAOui('GepiAccesEvalCumulEleve')) {
        $this->verif_exist_ordre_menu('bloc_carnet_notes_cumules');
        if ($this->notesCumulFamille())
        $this->chargeAutreNom('bloc_carnet_notes_cumules');
    }


/***** Outils de relevé ECTS *****/
	$this->verif_exist_ordre_menu('bloc_releve_ects');
	if ($this->releve_ECTS())
	$this->chargeAutreNom('bloc_releve_ects');

/***** Emploi du temps *****/
	$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	if ($this->emploiDuTemps())
	$this->chargeAutreNom('bloc_emploi_du_temps');

/***** Outils destinés essentiellement aux parents et aux élèves *****/

// Cahier de textes
	$this->verif_exist_ordre_menu('bloc_cahier_texte_famille');
	if ($this->cahierTexteFamille())
	$this->chargeAutreNom('bloc_cahier_texte_famille');
// Relevés de notes
	$this->verif_exist_ordre_menu('bloc_carnet_notes_famille');
	if ($this->releveNotesFamille())
	$this->chargeAutreNom('bloc_carnet_notes_famille');
// Equipes pédagogiques
	$this->verif_exist_ordre_menu('bloc_equipe_peda_famille');
	if ($this->equipePedaFamille())
	$this->chargeAutreNom('bloc_equipe_peda_famille');
// Bulletins simplifiés
	$this->verif_exist_ordre_menu('bloc_bull_simple_famille');
	if ($this->bulletinFamille())
	$this->chargeAutreNom('bloc_bull_simple_famille');
// Graphiques
	$this->verif_exist_ordre_menu('bloc_graphique_famille');
	if ($this->graphiqueFamille())
	$this->chargeAutreNom('bloc_graphique_famille');
// les absences
	$this->verif_exist_ordre_menu('bloc_absences_famille');
	if ($this->absencesFamille())
	$this->chargeAutreNom('bloc_absences_famille');

/***** Outils complémentaires de gestion des AID *****/
	$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
	if ($this->gestionAID())
	$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

/***** Outils de gestion des Bulletins scolaires *****/
	$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
	if ($this->bulletins())
	$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');

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
	$this->verif_exist_ordre_menu('bloc_panneau_affichage');
	if ($this->messages())
	$this->chargeAutreNom('bloc_panneau_affichage');

/***** Module inscription *****/
	$this->verif_exist_ordre_menu('bloc_module_inscriptions');
	if ($this->inscription())
	$this->chargeAutreNom('bloc_module_inscriptions');

/***** Module discipline *****/
	$this->verif_exist_ordre_menu('bloc_module_discipline');
	if ($this->discipline())
	$this->chargeAutreNom('bloc_module_discipline');

/***** Module Modèle Open Office *****/
	$this->verif_exist_ordre_menu('bloc_modeles_Open_Office');
	if ($this->modeleOpenOffice())
	$this->chargeAutreNom('bloc_modeles_Open_Office');

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	$this->verif_exist_ordre_menu('bloc_Genese_classes');
	if ($this->geneseClasses())
	$this->chargeAutreNom('bloc_Genese_classes');

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











  private function saisie(){

	$this->b=0;

	$afficher_correction_validation="n";
	$sql="SELECT 1=1 FROM matieres_app_corrections;";
	$test_mac=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_mac)>0) {$afficher_correction_validation="y";}

        if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
  $this->creeNouveauItem("/absences/index.php",
			  "Bulletins : saisie des absences",
			  "Cet outil vous permet de saisir les absences sur les bulletins." );
        }

	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (affiche_lien_cdt()))
	  $this->creeNouveauItem("/cahier_texte/index.php",
			  "Cahier de textes",
			  "Cet outil vous permet de constituer un cahier de textes pour chacune de vos classes." );

	if ((($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
			and (getSettingValue("active_carnets_notes")=='y'))
	  $this->creeNouveauItem("/cahier_notes/index.php",
			  "Carnet de notes : saisie des notes",
			  "Cet outil vous permet de constituer un carnet de notes pour chaque période et de saisir les notes de toutes vos évaluations.");

	if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur'))
	  $this->creeNouveauItem("/saisie/index.php",
			  "Bulletin : saisie des moyennes et des appréciations par matière",
			  "Cet outil permet de saisir directement, sans passer par le carnet de notes, les moyennes et les appréciations du bulletin");

	if($afficher_correction_validation=="y")
	  $this->creeNouveauItem("/saisie/validation_corrections.php",
			  "Correction des bulletins",
			  "Cet outil vous permet de valider les corrections d'appréciations proposées par des professeurs après la clôture d'une période.<br /><span style='color:red;'>Une ou des propositions requièrent votre attention.</span>\n");

	if ((($this->test_prof_suivi != "0") and (getSettingValue("GepiRubConseilProf")=='yes'))
			or (($this->statutUtilisateur!='professeur') and (getSettingValue("GepiRubConseilScol")=='yes') )
			or ($this->statutUtilisateur=='secours')  )
	  $this->creeNouveauItem("/saisie/saisie_avis.php",
			  "Bulletin : saisie des avis du conseil",
			  "Cet outil permet la saisie des avis du conseil de classe.");

	// Saisie ECTS - ne doit être affichée que si l'utilisateur a bien des classes ouvrant droit à ECTS
	if ($this->statutUtilisateur == 'professeur') {
		$this->test_prof_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_groupes_professeurs jgp
				WHERE (jgc.saisie_ects = TRUE
				  AND jgc.id_groupe = jgp.id_groupe
				  )"));
		$this->test_prof_suivi_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_eleves_professeurs jep, j_eleves_groupes jeg
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_groupe = jeg.id_groupe
				AND jeg.login = jep.login )"));
	} else {
		$this->test_scol_ects = sql_count(sql_query("SELECT jgc.saisie_ects
				FROM j_groupes_classes jgc, j_scol_classes jsc
				WHERE (jgc.saisie_ects = TRUE
				AND jgc.id_classe = jsc.id_classe
				)"));
	}
	$conditions_ects = ($this->gepiSettings['active_mod_ects'] == 'y' AND
		  (($this->test_prof_suivi != "0" and $this->gepiSettings['GepiAccesSaisieEctsPP'] =='yes'
			  AND $this->test_prof_suivi_ects != "0")
		  OR ($this->statutUtilisateur == 'professeur'
			  AND $this->gepiSettings['GepiAccesSaisieEctsProf'] =='yes'
			  AND $this->test_prof_ects != "0")
		  OR ($this->statutUtilisateur=='scolarite'
			  AND $this->gepiSettings['GepiAccesSaisieEctsScolarite'] =='yes'
			  AND $this->test_scol_ects != "0")
		  OR ($this->statutUtilisateur=='secours')));
	if ($conditions_ects)
	  $this->creeNouveauItem("/mod_ects/index_saisie.php","Crédits ECTS","Saisie des crédits ECTS");

	// Pour un professeur, on n'appelle que les aid qui sont sur un bulletin
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config
							  WHERE display_bulletin = 'y'
							  OR bull_simplifie = 'y'
							  ORDER BY nom");
	$nb_aid = mysqli_num_rows($call_data);
	$i=0;
	while ($i < $nb_aid) {
	  $indice_aid = @old_mysql_result($call_data, $i, "indice_aid");
	  $call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_aid_utilisateurs
								WHERE indice_aid = '".$indice_aid."'");
	  $nb_result = mysqli_num_rows($call_prof);
	  if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		$nom_aid = @old_mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/saisie/saisie_aid.php?indice_aid=".$indice_aid,
				$nom_aid,
				"Cet outil permet la saisie des appréciations des ".$this->gepiSettings['denomination_eleves']." pour les $nom_aid.");
	  }
	  $i++;
	}

	//==============================
// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
	if(file_exists('saisie/commentaires_types.php')) {
	  if ((($this->statutUtilisateur=='professeur')
			  AND (getSettingValue("CommentairesTypesPP")=='yes')
			  )
			  OR (($this->statutUtilisateur=='scolarite')
					  AND (getSettingValue("CommentairesTypesScol")=='yes')))
	  {
		$this->creeNouveauItem("/saisie/commentaires_types.php",
				"Saisie de commentaires-types",
				"Permet de définir des commentaires-types pour l'avis du conseil de classe.");
	  }
	}

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Saisie",'images/icons/configure.png');
		return true;
	  }
  }

  private function cahierTexteCPE(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "cpe" AND getSettingValue("GepiAccesCdtCpe") == 'yes')
		OR ($this->statutUtilisateur == "scolarite" AND getSettingValue("GepiAccesCdtScol") == 'yes')
	));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_texte_2/see_all.php",
			  "Cahier de textes",
			  "Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements de tous les ".$this->gepiSettings['denomination_eleves']);
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }



  private function trombinoscope(){
	//On vérifie si le module est activé

	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
	$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

	$this->b=0;

	$affiche="yes";
	if(($this->statutUtilisateur=='eleve')) {
	  $GepiAccesEleTrombiTousEleves=getSettingValue("GepiAccesEleTrombiTousEleves");
	  $GepiAccesEleTrombiElevesClasse=getSettingValue("GepiAccesEleTrombiElevesClasse");
	  $GepiAccesEleTrombiPersonnels=getSettingValue("GepiAccesEleTrombiPersonnels");
	  $GepiAccesEleTrombiProfsClasse=getSettingValue("GepiAccesEleTrombiProfsClasse");

	  if(($GepiAccesEleTrombiTousEleves!="yes")&&
			($GepiAccesEleTrombiElevesClasse!="yes")&&
			($GepiAccesEleTrombiPersonnels!="yes")&&
			($GepiAccesEleTrombiProfsClasse!="yes")) {
		$affiche = 'no';
	  }else {
		// Au moins un des droits est donné aux élèves.
		$affiche = 'yes';

		if (($active_module_trombinoscopes!='y')
				&&($GepiAccesEleTrombiPersonnels!="yes")
				&&($GepiAccesEleTrombiProfsClasse!="yes")) {
		  $affiche = 'no';
		}

		if (($active_module_trombino_pers!='y')
				&&($GepiAccesEleTrombiTousEleves!="yes")
				&&($GepiAccesEleTrombiElevesClasse!="yes")) {
		  $affiche = 'no';
		}
	  }
	}

	if ($affiche=="yes"
			&& (($active_module_trombinoscopes=='y')
			||($active_module_trombino_pers=='y'))) {

	  $this->creeNouveauItem("/mod_trombinoscopes/trombinoscopes.php",
			  "Trombinoscopes",
			  "Cet outil vous permet de visualiser les trombinoscopes des classes.");

	  // On appelle les aid "trombinoscope"
	  $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config
								WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."'
								ORDER BY nom");
	  $nb_aid = mysqli_num_rows($call_data);
	  $i=0;
	  while ($i < $nb_aid) {
		$indice_aid = @old_mysql_result($call_data, $i, "indice_aid");
		$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_aid_utilisateurs_gest
								  WHERE indice_aid = '$indice_aid'");
		$nb_result = mysqli_num_rows($call_prof);
		if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
		  $nom_aid = @old_mysql_result($call_data, $i, "nom");
		  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de visualiser quels ".$this->gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.");
		}
		$i++;
	  }
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }





  private function emploiDuTemps(){
	$this->b=0;

    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Emploi du temps",
			"Cet outil permet la consultation/gestion de l'emploi du temps.");

	$this->creeNouveauItem("/edt_organisation/edt_eleve.php",
			  "Emploi du temps",
			  "Cet outil permet la consultation de votre emploi du temps.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Emploi du temps",'images/icons/document.png');
	  return true;
	}
 }

  private function cahierTexteFamille(){
	$this->b=0;

	$condition = (
	getSettingValue("active_cahiers_texte")=='y' AND (
		($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesCahierTexteParent") == 'yes')
		OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesCahierTexteEleve") == 'yes')
	));

		  $this->creeNouveauItem("/cahier_texte/consultation.php",
				  "Cahier de textes",
				  "Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements que vous suivez.");


	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }

  private function releveNotesFamille(){
	$this->b=0;

   $condition = (
		getSettingValue("active_carnets_notes")=='y' AND (
			($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesReleveParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesReleveEleve") == 'yes')
			));

	if ($condition) {
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
				  "Relevés de notes",
				  "Permet de consulter vos relevés de notes détaillés.");
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Carnet de notes",'images/icons/releve.png');
	  return true;
	}
  }

  private function equipePedaFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable"
			  AND getSettingValue("GepiAccesEquipePedaParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve"
			  AND getSettingValue("GepiAccesEquipePedaEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/groupes/visu_profs_eleve.php",
				  "Équipe pédagogique",
				  "Permet de consulter l'équipe pédagogique qui vous concerne.");

	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Équipe pédagogique",'images/icons/trombinoscope.png');
	  return true;
	}
  }

  private function bulletinFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable"
			  AND getSettingValue("GepiAccesBulletinSimpleParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve"
			  AND getSettingValue("GepiAccesBulletinSimpleEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/prepa_conseil/index3.php",
				  "Bulletins simplifiés",
				  "Permet de consulter vos bulletins sous forme simplifiée.");

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Bulletins simplifiés",'images/icons/bulletin_simp.png');
	  return true;
	}
  }

  private function graphiqueFamille(){
	$this->b=0;

	$condition = (
			($this->statutUtilisateur == "responsable" AND getSettingValue("GepiAccesGraphParent") == 'yes')
			OR ($this->statutUtilisateur == "eleve" AND getSettingValue("GepiAccesGraphEleve") == 'yes')
			);

	if ($condition) {

		  $this->creeNouveauItem("/visualisation/affiche_eleve.php",
				  "Visualisation graphique",
				  "Permet de consulter vos résultats sous forme graphique, comparés à la classe.");

    }

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualisation graphique",'images/icons/graphes.png');
	  return true;
	}
  }





  private function AfficheAid($indice_aid){
    if ($this->statutUtilisateur == "eleve") {
        $test = sql_query1("SELECT count(login) FROM j_aid_eleves
				  WHERE indice_aid='".$indice_aid."' ");
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
  }



  private function impression(){
	$this->b=0;

	$conditions_moyennes = (
        ($this->statutUtilisateur != "professeur")
        OR
        (
        ($this->statutUtilisateur == "professeur") AND
            (
            (getSettingValue("GepiAccesMoyennesProf") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
            (getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")
            )
        )
        );

	$conditions_bulsimples = (
        	(
	        ($this->statutUtilisateur != "eleve") AND ($this->statutUtilisateur != "responsable")
        	)
        AND
        (
        ($this->statutUtilisateur != "professeur") OR
        (
	    	($this->statutUtilisateur == "professeur") AND
	            (
	            (getSettingValue("GepiAccesBulletinSimpleProf") == "yes") OR
	            (getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes") OR
	            (getSettingValue("GepiAccesBulletinSimpleProfToutesClasses") == "yes")
	            )
        	)
        )
        );

	$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualisation des équipes pédagogiques",
			"Ceci vous permet de connaître tous les ".$this->gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.");

	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation d'un ".$this->gepiSettings['denomination_eleve'],
			"Ce menu vous permet de consulter dans une même page les informations concernant un ".$this->gepiSettings['denomination_eleve']." (enseignements suivis, bulletins, relevés de notes, ".$this->gepiSettings['denomination_responsables'].",...). Certains éléments peuvent n'être accessibles que pour certaines catégories de visiteurs.");

	$this->creeNouveauItem("/impression/impression_serie.php",
			"Impression PDF de listes",
			"Ceci vous permet d'imprimer en PDF des listes avec les ".$this->gepiSettings['denomination_eleves'].", à l'unité ou en série. L'apparence des listes est paramétrable.");

	if(($this->statutUtilisateur=='scolarite')||(($this->statutUtilisateur=='professeur')
			AND ($this->test_prof_suivi != "0"))){
	  $this->creeNouveauItem("/saisie/impression_avis.php",
			  "Impression PDF des avis du conseil de classe",
			  "Ceci vous permet d'imprimer en PDF la synthèse des avis du conseil de classe.");
	}

	if(($this->statutUtilisateur=='scolarite')||
			($this->statutUtilisateur=='professeur')||
			($this->statutUtilisateur=='cpe')){
	  $this->creeNouveauItem("/groupes/mes_listes.php",
			  "Exporter mes listes",
			  "Ce menu permet de télécharger ses listes avec tous les ".$this->gepiSettings['denomination_eleves']." au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");
	}

	$this->creeNouveauItem("/visualisation/index.php",
			"Outils graphiques de visualisation",
			"Visualisation graphique des résultats des ".$this->gepiSettings['denomination_eleves']." ou des classes, en croisant les données de multiples manières.");

	if (($this->test_prof_matiere != "0") or ($this->statutUtilisateur!='professeur')) {

	  if ($this->statutUtilisateur!='scolarite'){
		$this->creeNouveauItem("/prepa_conseil/index1.php",
				"Visualiser mes moyennes et appréciations des bulletins",
				"Tableau récapitulatif de vos moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
	  }
	  else{
		$this->creeNouveauItem("/prepa_conseil/index1.php",
				"Visualiser les moyennes et appréciations des bulletins",
				"Tableau récapitulatif des moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
	  }

	}

	if ($conditions_moyennes)  {
	  $this->creeNouveauItem("/prepa_conseil/index2.php",
			  "Visualiser toutes les moyennes d'une classe",
			  "Tableau récapitulatif des moyennes d'une classe.");
	}

	if ($conditions_bulsimples) {
	  $this->creeNouveauItem("/prepa_conseil/index3.php",
			  "Visualiser les bulletins simplifiés",
			  "Bulletins simplifiés d'une classe.");
	}
	elseif(($this->statutUtilisateur=='professeur')&&(getSettingValue("GepiAccesBulletinSimplePP")=="yes")) {
	  $sql="SELECT 1=1 FROM j_eleves_professeurs ;";
	  $test_pp=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], $sql));
	  if($test_pp>0) {
		$this->creeNouveauItem("/prepa_conseil/index3.php",
				"Visualiser les bulletins simplifiés",
				"Bulletins simplifiés d'une classe.");
	  }
	}

	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config
					WHERE display_bulletin = 'y'
					OR bull_simplifie = 'y'
					ORDER BY nom");
	$nb_aid = mysqli_num_rows($call_data);

	$i=0;
	while ($i < $nb_aid) {
	  $indice_aid = @old_mysql_result($call_data, $i, "indice_aid");
	  $call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_aid_utilisateurs
								WHERE indice_aid = '".$indice_aid."'");
	  $nb_result = mysqli_num_rows($call_prof);
	  if ($nb_result != 0) {
		$nom_aid = @old_mysql_result($call_data, $i, "nom");
		$this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid,
				"Visualiser des appréciations ".$nom_aid,
				"Cet outil permet la visualisation et l'impression des appréciations des ".$this->gepiSettings['denomination_eleves']." pour les ".$nom_aid.".");
	  }
	  $i++;
	}

	if(($this->statutUtilisateur=='professeur')&&(getSettingValue('GepiAccesGestElevesProfP')=='yes')) {
	  // Le professeur est-il professeur principal dans une classe au moins.
	  $sql="SELECT 1=1 FROM j_eleves_professeurs ;";
	  $test=mysqli_query($GLOBALS["mysqli"], $sql);
	  if (mysqli_num_rows($test)>0) {
		$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
		$this->creeNouveauItem("/eleves/index.php",
				"Gestion des ".$this->gepiSettings['denomination_eleves'],
				"Cet outil permet d'accéder aux informations des ".$this->gepiSettings['denomination_eleves']." dont vous êtes ".$gepi_prof_suivi.".");
	  }
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualisation - Impression",'images/icons/print.png');
	  return true;
	}

  }

  private function notanet(){
	$this->b=0;

	$affiche='yes';
	if($this->statutUtilisateur=='professeur') {
	  $sql="SELECT DISTINCT g.*,c.classe FROM groupes g,
						  j_groupes_classes jgc,
						  j_groupes_professeurs jgp,
						  j_groupes_matieres jgm,
						  classes c,
						  notanet n
					  WHERE g.id=jgc.id_groupe AND
						  jgc.id_classe=n.id_classe AND
						  jgc.id_classe=c.id AND
						  jgc.id_groupe=jgp.id_groupe AND
						  jgm.id_groupe=g.id AND
						  jgm.id_matiere=n.matiere
					  ORDER BY jgc.id_classe;";
	  //echo "$sql<br />";
	  $res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	  if(mysqli_num_rows($res_grp)==0) {
		  $affiche='no';
	  }
	}

	if ((getSettingValue("active_notanet")=='y')&&($affiche=='yes')) {
	  if($this->statutUtilisateur=='professeur') {
		$this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Cet outil permet de saisir les appréciations pour les Fiches Brevet.");
	  }
	  else {
		$this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Cet outil permet :<br />
				- d'effectuer les calculs et la génération du fichier CSV requis pour Notanet.
				L'opération renseigne également les tables nécessaires pour générer les Fiches brevet.<br />
				- de générer les fiches brevet");
	  }
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Fiches Brevet",'images/icons/document.png');
	  return true;
	}
  }

  private function anneeAnterieure(){
	$this->b=0;

	if (getSettingValue("active_annees_anterieures")=='y') {

	  if($this->statutUtilisateur=='administrateur'){
		$this->creeNouveauItem("/mod_annees_anterieures/index.php",
				"Années antérieures",
				"Cet outil permet de gérer et de consulter les données d'années antérieures (bulletins simplifiés,...).");
	  }
	  else{
		if($this->statutUtilisateur=='professeur') {
		  $AAProfTout=getSettingValue('AAProfTout');
		  $AAProfPrinc=getSettingValue('AAProfPrinc');
		  $AAProfClasses=getSettingValue('AAProfClasses');
		  $AAProfGroupes=getSettingValue('AAProfGroupes');

		  if(($AAProfTout=="yes")||($AAProfClasses=="yes")||($AAProfGroupes=="yes")){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Années antérieures",
					"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
		  }
		  elseif($AAProfPrinc=="yes"){
			$sql="SELECT 1=1 FROM classes c,
									j_eleves_professeurs jep
							WHERE jep.id_classe=c.id
							ORDER BY c.classe";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Années antérieures",
					  "Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='scolarite') {
		  $AAScolTout=getSettingValue('AAScolTout');
		  $AAScolResp=getSettingValue('AAScolResp');

		  if($AAScolTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Années antérieures",
					"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
		  }
		  elseif($AAScolResp=="yes"){
			$sql="SELECT 1=1 FROM j_scol_classes ;";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Années antérieures",
					  "Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
			}
		  }

		}
		elseif($this->statutUtilisateur=='cpe') {
		  $AACpeTout=getSettingValue('AACpeTout');
		  $AACpeResp=getSettingValue('AACpeResp');

		  if($AACpeTout=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Années antérieures",
					"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
		  }
		  elseif($AACpeResp=="yes"){
			$sql="SELECT 1=1 FROM j_eleves_cpe ";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Années antérieures",
					  "Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
			}

		  }

		}
		elseif($this->statutUtilisateur=='responsable') {
		  $AAResponsable=getSettingValue('AAResponsable');

		  if($AAResponsable=="yes"){
			// Est-ce que le responsable est bien associé à un élève?
			$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e
				WHERE rp.pers_id=r.pers_id AND
					  r.ele_id=e.ele_id ";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0){
			  $this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					  "Années antérieures",
					  "Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
 			}
		  }

		}
		elseif($this->statutUtilisateur=='eleve') {
		  $AAEleve=getSettingValue('AAEleve');

		  if($AAEleve=="yes"){
			$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
					"Années antérieures",
					"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
		  }

		}

	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Années antérieures",'images/icons/document.png');
	  return true;
	}
  }









  private function plugins(){
	$this->b=0;

	$query = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM plugins WHERE ouvert = "y" order by description');

	while ($plugin = mysqli_fetch_object($query)){
	$this->b=0;
	  $nomPlugin=$plugin->nom;
	  $this->verif_exist_ordre_menu('bloc_plugin_'.$nomPlugin);
	  // On offre la possibilité d'inclure un fichier functions_nom_du_plugin.php
	  // Ce fichier peut lui-même contenir une fonction calcul_autorisation_nom_du_plugin voir plus bas.
	  if (file_exists($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php"))
		include_once($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php");

	  $querymenu = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM plugins_menus
								WHERE plugin_id = "'.$plugin->id.'"
								ORDER by titre_item');

	  while ($menuItem = mysqli_fetch_object($querymenu)){
		// On regarde si le plugin a prévu une surcharge dans le calcul de l'affichage de l'item dans le menu
		// On commence par regarder si une fonction du type calcul_autorisation_nom_du_plugin existe
		$nom_fonction_autorisation = "calcul_autorisation_".$nomPlugin;


		  $result_autorisation=true;

		if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
		  $this->creeNouveauItemPlugin("/".$menuItem->lien_item,
				supprimer_numero($menuItem->titre_item),$menuItem->description_item);
		}

	  }

	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',$plugin->description,'images/icons/package.png');
		$this->chargeAutreNom('bloc_plugin_'.$nomPlugin);
	  }

	}

  }



  private function fluxRSS(){
	$this->b=0;

	if (getSettingValue("rss_cdt_eleve") == 'y' AND $this->statutUtilisateur == "eleve") {
	  // Les flux rss sont ouverts pour les élèves
	  $this->canal_rss_flux=1;

	  // A vérifier pour les cdt
	  if (getSettingValue("rss_acces_ele") == 'direct') {
	// echo "il y a un flux RSS direct";
		$this->canal_rss=array("lien"=>"URL de l'élève" ,
				  "texte"=>"URL de l'élève",
				  "mode"=>1 ,
				  "expli"=>"En cliquant sur la cellule de gauche,
				  vous pourrez récupérer votre URI (si vous avez activé le javascript sur votre navigateur).");
	  }elseif(getSettingValue("rss_acces_ele") == 'csv'){
		$this->canal_rss=array("lien"=>"" , "texte"=>"", "mode"=>2, "expli"=>"");
	  }

	  $this->creeNouveauTitre('accueil',"Votre flux RSS",'images/icons/rss.png');
	  return true;
	}

  }








  private function gestionEleveAID(){
	$this->b=0;

	if (getSettingValue("active_mod_gest_aid")=='y') {

	  $sql = "SELECT * FROM aid_config ";
	  // on exclue la rubrique permettant de visualiser quels élèves ont le droit d'envoyer/modifier leur photo
	  $flag_where = 'n';

	  if (getSettingValue("num_aid_trombinoscopes") != "") {
		$sql .= "WHERE indice_aid!= '".getSettingValue("num_aid_trombinoscopes")."'";
		$flag_where = 'y';
	  }

	  // si le plugin "gestion_autorisations_publications" existe et est activé, on exclue la rubrique correspondante
	  $test_plugin = sql_query1("select ouvert from plugins where nom='gestion_autorisations_publications'");

	  if (($test_plugin=='y') and (getSettingValue("indice_aid_autorisations_publi") != ""))
		if ($flag_where == 'n')
		  $sql .= "WHERE indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";
		else
		  $sql .= "and indice_aid!= '".getSettingValue("indice_aid_autorisations_publi")."'";

	  $sql .= " ORDER BY nom";
	  $call_data = mysqli_query($GLOBALS["mysqli"], $sql);
	  $nb_aid = mysqli_num_rows($call_data);
	  $i=0;

	  while ($i < $nb_aid) {
		$indice_aid = @old_mysql_result($call_data, $i, "indice_aid");
		$call_prof1 = mysqli_query($GLOBALS["mysqli"], "SELECT *
					FROM j_aid_utilisateurs_gest
					WHERE indice_aid = '".$indice_aid."'");
		$nb_result1 = mysqli_num_rows($call_prof1);
		$call_prof2 = mysqli_query($GLOBALS["mysqli"], "SELECT *
					FROM j_aidcateg_super_gestionnaires
					WHERE indice_aid = '".$indice_aid."'");
		$nb_result2 = mysqli_num_rows($call_prof2);

		if (($nb_result1 != 0) or ($nb_result2 != 0)) {
		  $nom_aid = @old_mysql_result($call_data, $i, "nom");
  		if ($nb_result2 != 0)
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de gérer les groupes (création, suppression, modification).");
			else
      		$this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
				  $nom_aid,
				  "Cet outil vous permet de gérer l'appartenance des élèves aux différents groupes.");
		}

		$i++;
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Gestion des AID",'images/icons/document.png');
	  return true;
	}
  }










  private function chargeAutreNom($bloc){

	$this->titre_Menu[$this->a]->bloc=$bloc;
	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
	$resp1 = mysqli_query($GLOBALS["mysqli"], $sql1);

	if(mysqli_num_rows($resp1)>0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE '$this->statutUtilisateur'
			;";
	  $resp=mysqli_query($GLOBALS["mysqli"], $sql);

	  if (mysqli_num_rows($resp)>0){
		$this->titre_Menu[$this->a]->nouveauNom=mysqli_fetch_object($resp)->nouveau_nom;
	  }else{
		$this->titre_Menu[$this->a]->nouveauNom="";
	  }

	}else{
		$this->titre_Menu[$this->a]->nouveauNom="";
	}

  }


}

?>
