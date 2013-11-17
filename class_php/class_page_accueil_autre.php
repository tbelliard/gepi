<?php
/* $Id$
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

class class_page_accueil_autre {


  public $titre_Menu=array();
  public $menu_item=array();
  public $canal_rss=array();
  public $message_admin=array();
  public $nom_connecte=array();
  public $referencement=array();
  public $message=array();
  public $probleme_dir=array();
  public $canal_rss_flux="";
  public $gere_connect="";
  public $alert_sums="";
  public $signalement="";
  public $nb_connect="";
  public $nb_connect_lien="";

  protected $ordre_menus=array();
  protected $cheminRelatif="";
  protected $loginUtilisateur="";
  public $statutUtilisateur="";
  protected $gepiSettings="";
  protected $test_prof_matiere="";
  protected $test_prof_suivi="";
  protected $test_prof_ects="";
  protected $test_scol_ects="";
  protected $test_prof_suivi_ects="";
  protected $test_https="";
  protected $a=0;
  protected $b=0;

/**
 * Construit les entrées de la page d'accueil
 *
 * @author regis
 */
  function __construct($gepiSettings, $niveau_arbo,$ordre_menus) {


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

	$this->statutUtilisateur = "autre";
	$this->gepiSettings=$gepiSettings;
	$this->loginUtilisateur=$_SESSION['login'];

	$this->chargeOrdreMenu($ordre_menus);

/***** Outils de gestion des absences vie scolaire *****/
	$this->verif_exist_ordre_menu('bloc_absences_vie_scol');
	if ($this->absences_vie_scol())
	$this->chargeAutreNom('bloc_absences_vie_scol');

/***** Cahier de texte CPE ***********/
	$this->verif_exist_ordre_menu('bloc_saisie');
	if ($this->cahierTexte()){
	  $this->chargeAutreNom('bloc_saisie');
	}

/***** Outils de relevé de notes *****/
	$this->verif_exist_ordre_menu('bloc_releve_notes');
	if ($this->releve_notes())
	$this->chargeAutreNom('bloc_releve_notes');

/***** Emploi du temps *****/
	$this->verif_exist_ordre_menu('bloc_emploi_du_temps');
	if ($this->emploiDuTemps())
	$this->chargeAutreNom('bloc_emploi_du_temps');

/***** gestion des trombinoscopes : module de Christian Chapel ***********/
	$this->verif_exist_ordre_menu('bloc_trombinoscope');
	if ($this->trombinoscope())
	$this->chargeAutreNom('bloc_trombinoscope');

/***** Visualisation / Impression *****/
	$this->verif_exist_ordre_menu('bloc_visulation_impression');
	if ($this->impression())
	$this->chargeAutreNom('bloc_visulation_impression');

/***** Outils de relevé ECTS *****/
	$this->verif_exist_ordre_menu('bloc_releve_ects');
	if ($this->releve_ECTS())
	$this->chargeAutreNom('bloc_releve_ects');

/***** Outils complémentaires de gestion des AID *****/
	$this->verif_exist_ordre_menu('bloc_outil_comp_gestion_aid');
	if ($this->gestionAID())
	$this->chargeAutreNom('bloc_outil_comp_gestion_aid');

/***** Outils de gestion des Bulletins scolaires *****/
	$this->verif_exist_ordre_menu('bloc_gestion_bulletins_scolaires');
	if ($this->bulletins())
	$this->chargeAutreNom('bloc_gestion_bulletins_scolaires');

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

/***** Module plugins : affichage des menus des plugins en fonction des droits *****/
	$this->verif_exist_ordre_menu('');
	$this->plugins();

/***** Module Genese des classes *****/
	$this->verif_exist_ordre_menu('bloc_Genese_classes');
	if ($this->geneseClasses())
	$this->chargeAutreNom('bloc_Genese_classes');

/***** Module Epreuves blanches *****/
	$this->verif_exist_ordre_menu('bloc_epreuve_blanche');
	if ($this->epreuvesBlanches())
	$this->chargeAutreNom('bloc_epreuve_blanche');

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

  private function itemPermis($chemin){
      global $mysqli;
	$sql="SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
				WHERE (ds.nom_fichier='".$chemin."'
				  AND ds.id_statut=du.id_statut
				  AND du.login_user='".$this->loginUtilisateur."');" ;
	//echo "$sql<br />";
    
    
		$result = mysqli_query($mysqli, $sql);
        if (!$result) {
          return FALSE;
        } else {
          $row = $result->fetch_row() ;
          $result->close();
        }
        
	  if ($row[0]=='V' || $row[0]=='v'){
		//if ($chemin=='bulletin/bull_index.php') {echo ("on a bien les bulletins");}
		return TRUE;
	  } else {
		return FALSE;
	  }
  }

  protected function creeNouveauTitre($classe,$texte,$icone,$titre="",$alt=""){
	$this->titre_Menu[$this->a]=new menuGeneral();
	$this->titre_Menu[$this->a]->indexMenu=$this->a;
	$this->titre_Menu[$this->a]->classe=$classe;
	$this->titre_Menu[$this->a]->texte=$texte;
	$this->titre_Menu[$this->a]->icone['chemin']=$this->cheminRelatif.$icone;
	$this->titre_Menu[$this->a]->icone['titre']=$titre;
	$this->titre_Menu[$this->a]->icone['alt']=$alt;
  }

  protected function creeNouveauItem($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	if ($this->itemPermis($nouveauItem->chemin))
	{
	  $nouveauItem->indexMenu=$this->a;
	  $nouveauItem->titre=$titre;
	  $nouveauItem->expli=$expli;
	  $nouveauItem->indexItem=$this->b;
	  $this->menu_item[]=$nouveauItem;
	  $this->b++;
	}
	unset($nouveauItem);
  }

  protected function creeNouveauItemPlugin($chemin,$titre,$expli){
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin=$chemin;
	$nouveauItem->indexMenu=$this->a;
	$nouveauItem->titre=$titre;
	$nouveauItem->expli=$expli;
	$nouveauItem->indexItem=$this->b;
	$this->menu_item[]=$nouveauItem;
	$this->b++;
	unset($nouveauItem);
  }

  protected function chargeOrdreMenu($ordre_menus){
      global $mysqli;
	//$this->ordre_menus=$ordre_menus;
	$sql="SHOW TABLES LIKE 'mn_ordre_accueil'";
            
		$resp = mysqli_query($mysqli, $sql);
        $nb_lignes = $resp->num_rows;
		$resp->close();
	

	if($nb_lignes > 0) {
	  $sql2="SELECT bloc, num_menu
			FROM mn_ordre_accueil
			WHERE statut
			LIKE '$this->statutUtilisateur' " ;
              
		$resp2 =mysqli_query($mysqli, $sql2);
        $nb_lignes2 = $resp2->num_rows;
	  
	  if ($nb_lignes2 > 0){
		while($lig_log=mysqli_fetch_object($resp2)) {
		  $this->ordre_menus[$lig_log->bloc]=$lig_log->num_menu;
		}
	  }else{
		$this->ordre_menus=$ordre_menus;
	  }
	}else{
	  $this->ordre_menus=$ordre_menus;
	}
  }

  protected function verif_exist_ordre_menu($_item){
	if (!isset($this->ordre_menus[$_item]))
	  $this->ordre_menus[$_item] = max($this->ordre_menus)+1;
	  $this->a=$this->ordre_menus[$_item];
  }

  private function chargeAutreNom($bloc){
      global $mysqli;
	$sql1="SHOW TABLES LIKE 'mn_ordre_accueil'";
    
		$resp1 = mysqli_query($mysqli, $sql1);
        $nb_lignes1 = $resp1->num_rows;
		$resp1->close();
    
	if($nb_lignes1 > 0) {
	  $sql="SELECT nouveau_nom FROM mn_ordre_accueil
			WHERE bloc LIKE '$bloc'
			AND statut LIKE 'autre'
			AND nouveau_nom NOT LIKE ''
			;";
      
		$resp = mysqli_query($mysqli, $sql);
        $nb_lignes = $resp->num_rows;
        if ($nb_lignes > 0){
            $this->titre_Menu[$this->a]->texte=$resp->fetch_object($resp)->nouveau_nom;
            $resp->close();
        }
    
	}
  }
  
  protected function absences_vie_scol() {
	if (getSettingValue("active_module_absence")) {
	  $this->b=0;
	  $nouveauItem = new itemGeneral();
	  if (getSettingValue("active_module_absence")=='y' ) {
	  $this->creeNouveauItem('/mod_absences/gestion/gestion_absences.php',
			  "Gestion Absences, dispenses, retards et infirmeries",
			  "Cet outil vous permet de gérer les absences, dispenses, retards et autres bobos à l'infirmerie des ".$this->gepiSettings['denomination_eleves'].".");
	  $this->creeNouveauItem('/mod_absences/gestion/voir_absences_viescolaire.php',
			  "Visualiser les absences",
			  "Vous pouvez visualiser créneau par créneau la saisie des absences.");
		$this->creeNouveauItem("/mod_absences/professeurs/prof_ajout_abs.php",
				"Gestion des Absences",
				"Cet outil vous permet de gérer les absences des élèves");
	  } else if (getSettingValue("active_module_absence")=='2' ) {
		$this->creeNouveauItem("/mod_abs2/index.php",
				"Gestion des Absences",
				"Cet outil vous permet de gérer les absences des élèves");
	  }
	  if ($this->b>0){
		$this->creeNouveauTitre('accueil',"Gestion des retards et absences",'images/icons/absences.png');
		return true;
	  }
    }
  }

  private function cahierTexte(){
	$this->b=0;
	if (getSettingValue("active_cahiers_texte")=='y') {
	  $this->creeNouveauItem("/cahier_texte/see_all.php",
			  "Cahier de textes",
			  "Permet de consulter les compte-rendus de séance et les devoirs à faire pour les enseignements de tous les ".$this->gepiSettings['denomination_eleves']);
	  $this->creeNouveauItem("/cahier_texte_admin/visa_ct.php",
			  "Visa des cahiers de textes",
			  "Permet de viser les cahiers de textes" );
	}
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Cahier de texte",'images/icons/document.png');
	  return true;
	}
  }

  private function releve_notes(){
	$this->b=0;
	if (getSettingValue("active_carnets_notes")=='y') {
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_2.php",
			  "Visualisation et impression des relevés de notes",
			  "Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes.php",
			  "Visualisation et impression des relevés de notes",
			  "Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");
		// Le cas suivant n'est pas encore géré... les requêtes en statut autre ne sont pas gérées dans visu_releve_notes_bis.php
	  $this->creeNouveauItem("/cahier_notes/visu_releve_notes_bis.php",
			  "Visualisation et impression des relevés de notes",
			  "Cet outil vous permet de visualiser à l'écran et d'imprimer les relevés de notes,
				".$this->gepiSettings['denomination_eleve']." par ".$this->gepiSettings['denomination_eleve'].",
				  classe par classe.");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Relevés de notes",'images/icons/document.png');
	  return true;
	}
  }
 
  private function emploiDuTemps(){
	$this->b=0;
    $this->creeNouveauItem("/edt_organisation/index_edt.php",
			"Emploi du temps",
			"Cet outil permet la consultation/gestion de l'emploi du temps.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Emploi du temps",'images/icons/document.png');
	  return true;
	}
  }
  
  private function trombinoscope(){
     global $mysqli; 
	//On vérifie si le module est activé

	$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
	$active_module_trombino_pers=getSettingValue("active_module_trombino_pers");

	$this->b=0;


	if (($active_module_trombinoscopes=='y')
			||($active_module_trombino_pers=='y')) {

	  $this->creeNouveauItem("/mod_trombinoscopes/trombinoscopes.php",
			  "Trombinoscopes",
			  "Cet outil vous permet de visualiser les trombinoscopes des classes.");

	  // On appelle les aid "trombinoscope"
      $sql_call_data = "SELECT * FROM aid_config
								WHERE indice_aid= '".getSettingValue("num_aid_trombinoscopes")."'
								ORDER BY nom";
            
            $call_data = mysqli_query($mysqli, $sql_call_data);
            $nb_aid = $call_data->num_rows;
            
            while ($obj_aid = $call_data->fetch_object()) {
                $indice_aid = $obj_aid->indice_aid;
                $sql_call_prof = "SELECT * FROM j_aid_utilisateurs_gest
                                          WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
                                          AND indice_aid = '$indice_aid')";
                $call_prof = mysqli_query($GLOBALS["mysqli"], $sql_call_prof);
                $nb_result = $call_prof->num_rows;
                if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
                  $nom_aid = $obj_aid->nom;
                  $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
                          $nom_aid,
                          "Cet outil vous permet de visualiser quels ".$this->gepiSettings['denomination_eleves']." ont le droit d'envoyer/modifier leur photo.");
                }
            }            
            $call_data->close();
	}

	  if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Trombinoscope",'images/icons/trombinoscope.png');
		return true;
	  }
  }

  private function impression(){
      global $mysqli;
	$this->b=0;

	$this->creeNouveauItem("/groupes/visu_profs_class.php",
			"Visualisation des équipes pédagogiques",
			"Ceci vous permet de connaître tous les ".$this->gepiSettings['denomination_professeurs']." des classes dans lesquelles vous intervenez, ainsi que les compositions des groupes concernés.");

	$this->creeNouveauItem("/eleves/visu_eleve.php",
			"Consultation d'un ".$this->gepiSettings['denomination_eleve'],
			"Ce menu vous permet de consulter dans une même page les informations concernant un ".$this->gepiSettings['denomination_eleve']." (enseignements suivis, bulletins, relevés de notes, ".$this->gepiSettings['denomination_responsables'].",...). Certains éléments peuvent n'être accessibles que pour certaines catégories de visiteurs.");

	$this->creeNouveauItem("/impression/impression_serie.php",
			"Impression PDF de listes",
			"Ceci vous permet d'imprimer en PDF des listes avec les ".$this->gepiSettings['denomination_eleves'].", à l'unité ou en série. L'apparence des listes est paramétrable.");

	  $this->creeNouveauItem("/groupes/mes_listes.php",
			  "Exporter mes listes",
			  "Ce menu permet de télécharger ses listes avec tous les ".$this->gepiSettings['denomination_eleves']." au format CSV avec les champs CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS.");

	$this->creeNouveauItem("/visualisation/index.php",
			"Outils graphiques de visualisation",
			"Visualisation graphique des résultats des ".$this->gepiSettings['denomination_eleves']." ou des classes, en croisant les données de multiples manières.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
			"Visualiser mes moyennes et appréciations des bulletins",
			"Tableau récapitulatif de vos moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");
	$this->creeNouveauItem("/prepa_conseil/index1.php",
				"Visualiser les moyennes et appréciations des bulletins",
				"Tableau récapitulatif des moyennes et/ou appréciations figurant dans les bulletins avec affichage de statistiques utiles pour le remplissage des livrets scolaires.");

	$this->creeNouveauItem("/prepa_conseil/index2.php",
			"Visualiser toutes les moyennes d'une classe",
			"Tableau récapitulatif des moyennes d'une classe.");

	$this->creeNouveauItem("/prepa_conseil/index3.php",
			"Visualiser les bulletins simplifiés",
			"Bulletins simplifiés d'une classe.");
    $sql_call_data = "SELECT * FROM aid_config 
					WHERE display_bulletin = 'y' 
					OR bull_simplifie = 'y' 
					ORDER BY nom";
            
        $resultat = mysqli_query($mysqli, $sql_call_data);  
        $nb_lignes = $resultat->num_rows;
        while ($obj_call_data = $resultat->fetch_object()) {
          $indice_aid = $obj_call_data->indice_aid;
          $sql_call_prof = "SELECT * FROM j_aid_utilisateurs 
                                WHERE (id_utilisateur = '".$this->loginUtilisateur."'
                                AND indice_aid = '".$indice_aid."')";
          $call_prof = mysqli_query($mysqli, $sql_call_prof);
          $nb_result = $call_prof->num_rows;
          if ($nb_result != 0) {
            $nom_aid = $obj_call_data->nom;
            $this->creeNouveauItem("/prepa_conseil/visu_aid.php?indice_aid=".$indice_aid,
                    "Visualiser des appréciations ".$nom_aid,
                    "Cet outil permet la visualisation et l'impression des appréciations des ".$this->gepiSettings['denomination_eleves']." pour les ".$nom_aid.".");
            }          
        }
        $resultat->close();
    
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Visualisation - Impression",'images/icons/print.png');
	  return true;
	}
  }
  
  protected function releve_ECTS(){
	$this->b=0;

	$chemin = array();
	$this->creeNouveauItem("/mod_ects/edition.php",
			  "Génération des documents ECTS",
			  "Cet outil vous permet de générer les documents ECTS (relevé, attestation, annexe)
				pour les classes concernées.");

	  $this->creeNouveauItem("/mod_ects/recapitulatif.php",
			  "Visualiser tous les ECTS",
			  "Visualiser les tableaux récapitulatif par classe de tous les crédits ECTS.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Documents ECTS",'images/icons/releve.png');
	  return true;
	}
  }

  protected function gestionAID(){
     global $mysqli; 
	$this->b=0;
    
    $sql_call_data = "SELECT distinct ac.indice_aid, ac.nom
		  FROM aid_config ac, aid a
		  WHERE ac.outils_complementaires = 'y'
		  AND a.indice_aid=ac.indice_aid
		  ORDER BY ac.nom_complet";
    
    $sql_call_data2 = "SELECT id
              FROM archivage_types_aid
              WHERE outils_complementaires = 'y'";

		$call_data = mysqli_query($mysqli, $sql_call_data);  
        $nb_aid = $call_data->num_rows;
		$call_data2 = mysqli_query($mysqli, $sql_call_data2);
        $nb_aid_annees_anterieures =  $call_data2->num_rows;
        $nb_total=$nb_aid+$nb_aid_annees_anterieures;
        if ($nb_total != 0) {
            
            while ($obj_call_data = $call_data->fetch_object()) {
                $indice_aid = $obj_call_data->indice_aid;
                $nom_aid = $obj_call_data->nom;
                if ($this->AfficheAid($indice_aid)) {
                    $this->creeNouveauItem("/aid/index_fiches.php?indice_aid=".$indice_aid,
                      $nom_aid,
                      "Tableau récapitulatif, liste des ".$this->gepiSettings['denomination_eleves'].", ...");
                }
            }
            if (($nb_aid_annees_anterieures > 0)) {
              $this->creeNouveauItem("/aid/annees_anterieures_accueil.php",
                      "Fiches projets des années antérieures",
                      "Accès aux fiches projets des années antérieures");
            }
        }
        
        
        $call_data->close();

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',
			  "Outils de visualisation et d'édition des fiches projets",
			  'images/icons/document.png');
	  return true;
	}
  }
  
  private function AfficheAid($indice_aid){
    if ($this->statutUtilisateur == "eleve") {
        $sql = "SELECT count(login) FROM j_aid_eleves
				  WHERE login='".$this->loginUtilisateur."'
				  AND indice_aid='".$indice_aid."' ";
        $test = sql_query1($sql);
        if ($test == 0)
            return false;
        else
            return true;
    } else
        return true;
  }
  
  protected function bulletins(){
	$this->b=0;

	$this->creeNouveauItem("/bulletin/verif_bulletins.php",
			  "Outil de vérification",
			  "Permet de vérifier si toutes les rubriques des bulletins sont remplies.");
	$this->creeNouveauItem("/bulletin/verrouillage.php",
			  "Verrouillage/Déverrouillage des périodes",
			  "Permet de verrouiller ou déverrouiller une période pour une ou plusieurs classes.");
	$this->creeNouveauItem("/classes/acces_appreciations.php",
			  "Accès des ".$this->gepiSettings['denomination_eleves']." et ".$this->gepiSettings['denomination_responsables']." aux appréciations",
			  "Permet de définir quand les comptes ".$this->gepiSettings['denomination_eleves']." et ".$this->gepiSettings['denomination_responsables']."
			  (s'ils existent) peuvent accéder aux appréciations des ".$this->gepiSettings['denomination_professeurs']."
				sur le bulletin et avis du conseil de classe.");

	if(getSettingValue('type_bulletin_par_defaut')=='pdf') {
		$this->creeNouveauItem("/bulletin/param_bull_pdf.php",
			  "Paramètres d'impression des bulletins",
			  "Permet de modifier les paramètres de mise en page et d'impression des bulletins.");
	}
	else {
		$this->creeNouveauItem("/bulletin/param_bull.php",
			  "Paramètres d'impression des bulletins",
			  "Permet de modifier les paramètres de mise en page et d'impression des bulletins.");
	}

	$this->creeNouveauItem("/responsables/index.php",
			  "Gestion des fiches ".$this->gepiSettings['denomination_responsables'],
			  "Cet outil vous permet de modifier/supprimer/ajouter des fiches
			  de ".$this->gepiSettings['denomination_responsables']." des ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/eleves/index.php",
			  "Gestion des fiches ".$this->gepiSettings['denomination_eleves'],
			  "Cet outil vous permet de modifier/supprimer/ajouter des fiches ".$this->gepiSettings['denomination_eleves'].".");
	$this->creeNouveauItem("/bulletin/bull_index.php",
			  "Visualisation et impression des bulletins",
			  "Cet outil vous permet de visualiser à l'écran et d'imprimer les bulletins, classe par classe.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Bulletins scolaires",'images/icons/bulletin_16.png');
	  return true;
	}
  }

  private function notanet(){
	$this->b=0;

	if ((getSettingValue("active_notanet")=='y')) {
	  $this->creeNouveauItem("/mod_notanet/index.php",
				"Notanet/Fiches Brevet",
				"Cet outil permet :<br />
				- d'effectuer les calculs et la génération du fichier CSV requis pour Notanet.
				L'opération renseigne également les tables nécessaires pour générer les Fiches brevet.<br />
				- de générer les fiches brevet");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Notanet/Fiches Brevet",'images/icons/document.png');
	  return true;
	}
  }

  private function anneeAnterieure(){
	$this->b=0;

	if (getSettingValue("active_annees_anterieures")=='y') {
		$this->creeNouveauItem("/mod_annees_anterieures/index.php",
				"Années antérieures",
				"Cet outil permet de gérer et de consulter les données d'années antérieures (bulletins simplifiés,...).");

		$this->creeNouveauItem("/mod_annees_anterieures/consultation_annee_anterieure.php",
				"Années antérieures",
				"Cet outil permet de consulter les données d'années antérieures (bulletins simplifiés,...).");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Années antérieures",'images/icons/document.png');
	  return true;
	}
  }

  protected function messages(){
	$this->b=0;
	$this->creeNouveauItem("/messagerie/index.php",
			"Panneau d'affichage",
			"Cet outil permet la gestion des messages à afficher sur la page d'accueil des utilisateurs.");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Panneau d'affichage",'images/icons/mail.png');
	  return true;
	}
  }

  protected function inscription(){
	$this->b=0;

	if (getSettingValue("active_inscription")=='y') {
	  $this->creeNouveauItem("/mod_inscription/inscription_config.php",
			  "Configuration du module d'inscription/visualisation",
			  "Configuration des différents paramètres du module");

	  if (getSettingValue("active_inscription_utilisateurs")=='y'){
		$this->creeNouveauItem("/mod_inscription/inscription_index.php",
				"Accès au module d'inscription/visualisation",
				"S'inscrire ou se désinscrire - Consulter les inscriptions");
	  }

	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Inscription",'images/icons/document.png');
	  return true;
	}
  }

  protected function discipline(){
	$this->b=0;

	$this->creeNouveauItem("/mod_discipline/index.php",
			"Discipline",
			"Signaler des incidents, prendre des mesures, des sanctions.");

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Discipline",'images/icons/document.png');
	  return true;
	}

  }

  private function plugins(){
      
      global $mysqli;
	$this->b=0;
    
    $sql = 'SELECT * FROM plugins WHERE ouvert = "y" order by description';
            
        $resultat = mysqli_query($mysqli, $sql);
        while ($plugin = $resultat->fetch_object()){
            $this->b=0;
            $nomPlugin=$plugin->nom;
            $this->verif_exist_ordre_menu('bloc_plugin_'.$nomPlugin);
            // On offre la possibilité d'inclure un fichier functions_nom_du_plugin.php
            // Ce fichier peut lui-même contenir une fonction calcul_autorisation_nom_du_plugin voir plus bas.
          if (file_exists($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php"))
                  include_once($this->cheminRelatif."mod_plugins/".$nomPlugin."/functions_".$nomPlugin.".php");
          
          $sql_menu = 'SELECT * FROM plugins_menus
                                    WHERE plugin_id = "'.$plugin->id.'"
                                    ORDER by titre_item';
          $querymenu = mysqli_query($mysqli, $sql_menu);
          while ($menuItem = $querymenu->fetch_object()){
              // On regarde si le plugin a prévu une surcharge dans le calcul de l'affichage de l'item dans le menu
            // On commence par regarder si une fonction du type calcul_autorisation_nom_du_plugin existe
            $nom_fonction_autorisation = "calcul_autorisation_".$nomPlugin;

            if (function_exists($nom_fonction_autorisation))
              // Si une fonction du type calcul_autorisation_nom_du_plugin existe, on calcule le droit de l'utilisateur à afficher cet item dans le menu
              $result_autorisation = $nom_fonction_autorisation($this->loginUtilisateur,$menuItem->lien_item);
            else
              $result_autorisation=true;

            if (($menuItem->user_statut == $this->statutUtilisateur) and ($result_autorisation)) {
              $this->creeNouveauItemPlugin("/".$menuItem->lien_item,
                    supprimer_numero($menuItem->titre_item),$menuItem->description_item);
            }

            
          }
          
        }
        $resultat->close();

  }

  protected function geneseClasses(){
	$this->b=0;
	$this->creeNouveauItem("/mod_genese_classes/index.php",
			"Génèse des classes",
			"Effectuer la répartition des élèves par classes en tenant comptes des options,...");
	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Génèse des classes",'images/icons/document.png');
	  return true;
	}
  }

  protected function epreuvesBlanches(){
	$this->b=0;

	//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_epreuve_blanche")=='y') {
	  $this->creeNouveauItem("/mod_epreuve_blanche/index.php",
			  "Épreuves blanches",
			  "Organisation d'épreuves blanches,...");
	}
//insert into setting set name='active_mod_epreuve_blanche', value='y';
	if (getSettingValue("active_mod_examen_blanc")=='y') {
	  $this->creeNouveauItem("/mod_examen_blanc/index.php",
			  "Examens blancs",
			  "Organisation d'examens blancs,...");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Épreuves blanches",'images/icons/document.png');
	  return true;
	}
  }

  protected function adminPostBac(){
	$this->b=0;

	if (getSettingValue("active_mod_apb")=='y') {
	  $this->creeNouveauItem("/mod_apb/index.php",
			  "Export APB",
			  "Export du fichier XML pour le système Admissions Post-Bac");
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Export Post-Bac",'images/icons/document.png');
	  return true;
	}
  }
 private function gestionEleveAID(){
     global $mysqli;
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
              
            $call_data = mysqli_query($mysqli, $sql);
            $nb_aid = $call_data->num_rows;
            while ($obj_call_data = $call_data->fetch_object()) {
                $indice_aid = $obj_call_data->indice_aid;
                $sql_call_prof = "SELECT *
                        FROM j_aid_utilisateurs_gest
                        WHERE (id_utilisateur = '" . $this->loginUtilisateur . "'
                        AND indice_aid = '$indice_aid')";
                $call_prof = mysqli_query($mysqli,$sql_call_prof);
                $nb_result = $call_prof->num_rows;
                if (($nb_result != 0) or ($this->statutUtilisateur == 'secours')) {
                    $nom_aid = $call_prof->nom;
                    $this->creeNouveauItem("/aid/index2.php?indice_aid=".$indice_aid,
                      $nom_aid,
                      "Cet outil vous permet de gérer l'appartenance des élèves aux différents groupes.");
                }
                
            }
            $call_data->close();
            
	}

	if ($this->b>0){
	  $this->creeNouveauTitre('accueil',"Gestion des AID",'images/icons/document.png');
	  return true;
	}
  }




  
  
  
  
  
  

}
?>
