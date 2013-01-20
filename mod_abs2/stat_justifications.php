<?php
/**
 * Statistiques des justifications - module Abs2
 *
 * Copyright 2010 Josselin Jacquard Regis Bouguin
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
 * 
 */

/**
 * @todo Recharger la page régulièrement
 * @todo Export .csv
 * @todo Totaux par classes
 * @todo Export .ods
 * @todo Gérer la taille du div
 */

/* *******************************************************************************
 * Vérification des droits sur la page
 ******************************************************************************* */

$timeDebut=time();

// Initialisations files
/**
 * Initialisation Propel
 */
require_once("../lib/initialisationsPropel.inc.php");
/**
 * Initialisation GEPI
 */
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut() != "cpe" && $utilisateur->getStatut() != "scolarite") {
    die("acces interdit");
}

/* *******************************************************************************
 * Initialisation de globales
 ******************************************************************************* */
$dernierePosition = isset ($_SESSION['statJustifie']['dernierePosition']) ? $_SESSION['statJustifie']['dernierePosition'] : NULL;
/* *******************************************************************************
 * Appel des bibliothèques de fonctions
 ******************************************************************************* */
/**
 * Bibliothèque de fonctions du module ABS2
 */
include_once 'lib/function.php';

/* *******************************************************************************
 * Fonctions de la page
 ******************************************************************************* */
/**
 *	Chargement des justifications
 * 
 * @return Object Collection Propel 
 */
function getJustifications() {
  $justifie_query = AbsenceEleveJustificationQuery::create()->orderBy('SortableRank');
  return $justifie_query->distinct()->find();  
}
/**
 * Chargement des élèves
 * @return Object Collection Propel
 */
function getEleves() {  
  $eleve_query = EleveQuery::create();
  if (isset($id_classe) && $id_classe !== null && $id_classe != -1 ) {
	$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
  }
  if (isset($nom_eleve) && $nom_eleve !== null && $nom_eleve != '') {
	  $eleve_query->filterByNom('%'.$nom_eleve.'%');
  }
  if (isset($id_eleve) && $id_eleve !== null && $id_eleve != '') {
	  $eleve_query->filterByIdEleve($id_eleve);
  }
  return ($eleve_query->orderByNom()->orderByPrenom()->distinct()->find());
}
/**
 * Récupère les données d'un élève à afficher
 * @param objet $eleve Un élève issu de getEleves()
 * @param date $date_debut
 * @param date $date_fin
 * @param objet $justifie_col Collection Propel avec les justifications
 * @param bool $donneeBrut
 * @return array Une ligne du tableau à afficher
 * @see getEleves()
 */
function traiteEleve($eleve,$date_debut, $date_fin, $justifie_col, $donneeBrut, $erreur=FALSE) {
  $eleve_id = $eleve->getId();
  $donnees= array();
  $donnees[$eleve_id] = array();
  
  $propel_eleve = EleveQuery::create()->filterById($eleve_id)->findOne();
  $eleveNbAbs['demi_journees'] = $propel_eleve->getDemiJourneesAbsence($date_debut, $date_fin)->count();
  $eleveNbAbs['retards'] = $propel_eleve->getRetards($date_debut, $date_fin)->count();
	
	if ($eleveNbAbs['demi_journees'] > 0 || $eleveNbAbs['retards'] > 0 ) {
	  $eleveNbAbs['non_justifiees'] = $propel_eleve->getDemiJourneesNonJustifieesAbsence($date_debut, $date_fin)->count();
	  $eleveNbAbs['justifiees'] = $eleveNbAbs['demi_journees'] - $eleveNbAbs['non_justifiees']; 
	  $donnees[$eleve_id]['nom'] = $eleve->getNom();
	  $donnees[$eleve_id]['prenom'] = $eleve->getPrenom();
	  $donnees[$eleve_id]['classe'] = $eleve->getClasse();
	  $donnees[$eleve_id]['classe'] = $eleve->getClasseNom();        
	  $donnees[$eleve_id]['nbre_lignes_total'] = 0;
	  $donnees[$eleve_id]['demi_journees'] = $eleveNbAbs['demi_journees'];
	  $donnees[$eleve_id]['justifiees'] = $eleveNbAbs['justifiees'];
	  $donnees[$eleve_id]['non_justifiees'] = $eleveNbAbs['non_justifiees'];
	  $donnees[$eleve_id]['retards'] = $eleveNbAbs['retards'];
	  //Récupérer le décompte des traitements pour chaque élève	  
	  $totalDemi=0;
	foreach ($justifie_col as $justifie) {
	  // Décompte en données brutes 
		if ($donneeBrut == TRUE) {
		  $propel_traitEleve = AbsenceEleveTraitementQuery::create()->filterByAJustificationId($justifie->getid())
			->useJTraitementSaisieEleveQuery()
			  ->useAbsenceEleveSaisieQuery()
				->filterByEleveId($eleve_id)
				->filterByPlageTemps($date_debut,$date_fin )
			  ->endUse()
			->endUse() ;
		  $traiteEleve_col = $propel_traitEleve;
		  $donnees[$eleve_id]['traitement'][] = $traiteEleve_col->distinct()->count();
		} else {
		  // Décompte en 1/2 journées
                    $abs_saisie_col_filtrees = $eleve->getAbsenceEleveSaisiesDecompteDemiJournees($date_debut, $date_fin);
                    $justif_collection = new PropelCollection();
                    foreach ($abs_saisie_col_filtrees as $saisie) {
                        foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
                            if ($traitement->getAJustificationId() == $justifie->getid()) {
                                $justif_collection->add($saisie);
                            }
                        }
                    }

                    require_once(dirname(__FILE__)."/../orm//helpers/AbsencesEleveSaisieHelper.php");
                    $dm = AbsencesEleveSaisieHelper::compte_demi_journee($justif_collection, $date_debut, $date_fin);
                    $donnees[$eleve_id]['traitement'][] = $dm->count();
                    $totalDemi += $dm->count();
		}
	  }
	  $donnees[$eleve_id]['totalDemi']=$totalDemi;
	}
	unset ($eleveNbAbs, $traiteEleve_col, $propel_eleve, $propel_traitEleveDemi, $traiteEleveDemi, $traiteEleveDemi_col, $propel_traitEleve);
	if ($erreur && isset ($donnees[$eleve_id]['justifiees']) && ($donnees[$eleve_id]['justifiees']==$donnees[$eleve_id]['totalDemi'])) {
	  $donnees[$eleve_id] = array();
	}
	
	return $donnees;
}
/**
 * Affiche une page tant que le tableau n'ai pas entièrement calculé
 * @param int $indice L'élève qu'on traite
 * @param int $nbEleves Le nombre total d'élèves à traiter
 */
function afficheChargement($indice,$nbEleves) {
  global $session_gepi; 
  require("../lib/global.inc.php");
  $gepiPath = "../";
  $niveau_arbo = 1;
  $titre_page = "Répartition des justifications (chargement des données)";
  $_SESSION['cacher_header'] = "y";
  require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>
<p style ="display: block; width: 50%; margin:1em auto; text-align: center; font-size: large; font-weight: bold;">
  Recherche des justifications : 
  <?php echo round(($indice*100)/$nbEleves); ?>%
</p>
<p style ="display: block; width: 50%; margin:1em auto; text-align: center; ">
  Veuillez patienter, cette opération peut-être très longue...
</p>
<script type="text/javascript">

    document.location.replace("stat_justifications.php")

</script>
<a href="stat_justifications.php">Cliquez sur ce lien pour continuer</a>
<?php
  die ();
}
/**
 * Crée et envoie un fichier .csv avec le tableau des justifications
 * @param array $donnees
 * @param objet $justifications 
 */
function creeCSV($donnees, $justifications) {
  $date = date("d-m-Y_H-i");
  $nom_fic = "Justifications_".$date.".csv";
  send_file_download_headers('text/x-csv',$nom_fic);
  $fd = '"Nom Prénom";"Classe";"Retards";"1/2 journées non justifiées";"1/2 journées justifiées"';
  foreach ($justifications as $justifie) {
	$fd .= ';"'.$justifie->getNom().'"';
  }
  $fd .= "\n";
  
  if (count($donnees)) {
  	foreach ($donnees as $donnee) {
	  $fd .= '"'.$donnee['nom'].' '.$donnee['prenom'].'"';
	  $fd .= ';"'.$donnee['classe'].'"';
	  $fd .= ';"'.$donnee['retards'].'"';
	  $fd .= ';"'.$donnee['non_justifiees'].'"';
	  $fd .= ';"'.$donnee['justifiees'].'"';
	  foreach ($donnee['traitement'] as $justifie) {
		$fd .= ';"'.$justifie.'"';
	  }
	  $fd .= "\n";
	}
  }
	
  echo $fd;
  die ();
}

/**
 * Crée et envoie un fichier .ods avec le tableau des justifications
 * @param array $donnees
 * @param objet $justifications 
 */
function creeODS($donnees, $justifications) {
  $date = date("d-m-Y_H-i");
  $nom_fichier = "Justifications_".$date.".ods";
  
  $nbre_colonnes = count($justifications);
  foreach ($justifications as $justifie) {
	$libelle[] = $justifie->getNom();
  }
  
  $titre = 'extraction des justifications du ';
  $titre .= unserialize($_SESSION['statJustifie']['date_absence_eleve_debut']);
  $titre .= ' au ';
  $titre .= unserialize($_SESSION['statJustifie']['date_absence_eleve_fin']); 
  if (isset ($_SESSION['statJustifie']['erreur']) && $_SESSION['statJustifie']['erreur']) {
	$titre .=  ' (Total des justifications différent des absences justifiées)';
  }
  
  $colonnes_individu = array();
  $colonnes_individu[1] = 'nom';
  $colonnes_individu[2] = 'classe';
  
  if (count($donnees)) {
  	foreach ($donnees as &$donnee) {
	  $j=1;
	  foreach ($donnee['traitement'] as $justifie) {
		$donnee['traite_'.$j] = $justifie;
		$j++;
	  }
	}
  }
  
  // load the TinyButStrong libraries
  include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
  $TBS = new clsTinyButStrong; // new instance of TBS
  include_once('../tbs/plugins/tbs_plugin_opentbs.php');
  $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
  // Load the template
  $extraction_Justifications = repertoire_modeles('absence_nb_justifications.ods');
  $TBS->LoadTemplate($extraction_Justifications, OPENTBS_ALREADY_UTF8);
  
  $TBS->MergeBlock('c2', 'num', $nbre_colonnes);
  $TBS->MergeField('titre', $titre);
  $TBS->MergeBlock('a', $libelle);
  $TBS->MergeBlock('b2', $donnees);
  
  // Output as a download file (some automatic fields are merged here)
  $TBS->Show(OPENTBS_DOWNLOAD + TBS_EXIT, $nom_fichier);
  die ();
}

/* *******************************************************************************
 * Logique de la page
 ******************************************************************************* */

if (!isset($_SESSION['statJustifie'])) {
/***** On arrive pour la première fois sur la page *****/
  // on recherche les justifications
  $justifie_col = getJustifications();
  $_SESSION['statJustifie']['justifications'] = serialize($justifie_col);
  // on initialise les dates à maintenant
  $dt_date_absence_eleve_debut = new DateTime('now');
  $dt_date_absence_eleve_fin = new DateTime('now');
  $_SESSION['statJustifie']['date_absence_eleve_debut'] = serialize($dt_date_absence_eleve_debut->format('d/m/Y'));
  $_SESSION['statJustifie']['date_absence_eleve_fin'] = serialize($dt_date_absence_eleve_fin->format('d/m/Y'));
	$dt_date_absence_eleve_debut->setTime(0, 0, 0);
	$dt_date_absence_eleve_fin->setTime(23, 59, 59);
  // on recherche les élèves
  $eleve_col = getEleves();
  if ($eleve_col->isEmpty()) {
	unset ($_SESSION['statJustifie']);
	die("Aucun élève trouvé.");
  }
  
  $_SESSION['statJustifie']['eleve_col'] = serialize($eleve_col);
  // on initialise le parcours du tableau
  $_SESSION['statJustifie']['dernierePosition'] = -1;
  // on affiche la page de chargement
  afficheChargement($_SESSION['statJustifie']['dernierePosition'], count($eleve_col));
  
} elseif (isset($_SESSION['statJustifie']['dernierePosition']) && ($_SESSION['statJustifie']['dernierePosition'] !== NULL)) {
/***** On a commencé mais tous les élèves n'ont pas été traité *****/
  
  // set_time_limit(8);  // à décommenter pour tester le rechargement de la page
  // On récupère max_execution_time et on se garde 2 secondes
  $max_time = ini_get('max_execution_time') - 2;
  // On vérifie si on ne veut que les erreurs
  if (!isset ($_SESSION['statJustifie']['erreur'])) {
	$_SESSION['statJustifie']['erreur']=FALSE;
  }
  // on récupère les justifications
  $justifie_col = unserialize($_SESSION['statJustifie']['justifications']);
  // on récupère les dates
  $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_debut'])));
  $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_fin'])));
  
	$dt_date_absence_eleve_debut->setTime(0, 0, 0);
	$dt_date_absence_eleve_fin->setTime(23, 59, 59);
  // on récupère les élèves
  $eleve_col = unserialize($_SESSION['statJustifie']['eleve_col']);
  // on récupère le type de statistique
  if (isset ($_SESSION['statJustifie']['donneeBrut'])) {
	$donneeBrut = $_SESSION['statJustifie']['donneeBrut'];
  } else {
	$donneeBrut = FALSE;
  }
  
  // on récupère la dernière position
  $dernierePosition = $_SESSION['statJustifie']['dernierePosition'];
  
  foreach ($eleve_col as $eleve) {
	if ($eleve_col->getPosition() <= $dernierePosition) {
	  continue;
	}
	// on initialise les donnees pour le nouvel eleve
	$retour = traiteEleve($eleve, $dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin, $justifie_col, $donneeBrut,$_SESSION['statJustifie']['erreur']);
	if (!empty ($retour[$eleve->getId()])) {
	  $_SESSION['statJustifie']['donnees'][] = $retour[$eleve->getId()];
	}
	// on met à jour l'index  
	$_SESSION['statJustifie']['dernierePosition'] = $dernierePosition = $eleve_col->getPosition();
	
	// On recharge tous les 10% du nombre d'élèves
	
	// on recherche 10% des élèves
	$dixieme = floor(count($eleve_col)/10);
	if (0 == ($_SESSION['statJustifie']['dernierePosition'] % $dixieme)) {
	  afficheChargement($_SESSION['statJustifie']['dernierePosition'], count($eleve_col));
	}

	// Si on est trop long, recharger la page (on pourrait aussi utiliser set_time_limit())
	$tempsScript = time() - $timeDebut;
	if ($tempsScript >= $max_time) {
	  afficheChargement($_SESSION['statJustifie']['dernierePosition'], count($eleve_col));
	}

  }
  // on a passé tous les élèves, on réinitialise l'index
  unset ($_SESSION['statJustifie']['dernierePosition'], $dernierePosition);
  if (isset ($_SESSION['statJustifie']['donnees'])) {
	$donnees = $_SESSION['statJustifie']['donnees'];
  } else {
	$donnees = array();
  }
} elseif (!empty ($_POST)) {
/***** On a des données en $_POST, il faut initialiser et traiter *****/
  if ($_POST['valide'] == "calcul") {
	/***** On force le rechargement des données *****/
	unset ($_SESSION['statJustifie']);
	// on recherche les justifications
	$justifie_col = getJustifications();
	$_SESSION['statJustifie']['justifications'] = serialize($justifie_col);
	// on récupère les dates
	$date_absence_eleve_debut = isset ($_POST['date_absence_eleve_debut']) ? $_POST['date_absence_eleve_debut'] : NULL;
	$date_absence_eleve_fin = isset ($_POST['date_absence_eleve_fin']) ? $_POST['date_absence_eleve_fin'] : NULL;
	$dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
	$dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
	$dt_date_absence_eleve_debut->setTime(0, 0, 0);
	$dt_date_absence_eleve_fin->setTime(23, 59, 59);
	$inverse_date=false;
	if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
	  $date2=clone $dt_date_absence_eleve_fin;
	  $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
	  $dt_date_absence_eleve_debut= $date2;
	  $inverse_date=true;
	}
	$_SESSION['statJustifie']['date_absence_eleve_debut'] = serialize($dt_date_absence_eleve_debut->format('d/m/Y'));
	$_SESSION['statJustifie']['date_absence_eleve_fin'] = serialize($dt_date_absence_eleve_fin->format('d/m/Y'));
	// on recherche les élèves
	$eleve_col = getEleves();
	if ($eleve_col->isEmpty()) {
	  unset ($_SESSION['statJustifie']);
	  die("Aucun élève trouvé.");
	}
	$_SESSION['statJustifie']['eleve_col'] = serialize($eleve_col);
	// on initialise le parcours du tableau
	$_SESSION['statJustifie']['dernierePosition'] = -1;
	// on récupère le type de statistique
	$donneeBrut = $_POST['donneeBrut'];
	$_SESSION['statJustifie']['donneeBrut'] = $donneeBrut;
	// on affiche la page de chargement
	afficheChargement($_SESSION['statJustifie']['dernierePosition'], count($eleve_col));

  } elseif ($_POST['valide'] == "csv") {
  /***** On crée et envoie un fichier .csv *****/
	if (!isset ($_SESSION['statJustifie']['donnees'])) {
	  $_SESSION['statJustifie']['donnees']=array();
	}
	creeCSV($_SESSION['statJustifie']['donnees'], unserialize($_SESSION['statJustifie']['justifications']));

  } elseif ($_POST['valide'] == "ods") {
  /***** On crée et envoie un fichier .csv *****/
	if (!isset ($_SESSION['statJustifie']['donnees'])) {
	  $_SESSION['statJustifie']['donnees']=array();
	}
	creeODS($_SESSION['statJustifie']['donnees'], unserialize($_SESSION['statJustifie']['justifications']));
  } else {
  /***** On a changer les dates ou le mode de calcul ou on ne veut que les erreurs *****/
	// On initialise les données
	unset ($_SESSION['statJustifie']['donnees']);
	// on récupère les justifications
	$justifie_col = unserialize($_SESSION['statJustifie']['justifications']);
	// on récupère les dates
	$date_absence_eleve_debut = isset ($_POST['date_absence_eleve_debut']) ? $_POST['date_absence_eleve_debut'] : NULL;
	$date_absence_eleve_fin = isset ($_POST['date_absence_eleve_fin']) ? $_POST['date_absence_eleve_fin'] : NULL;
	$dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
	$dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
	$dt_date_absence_eleve_debut->setTime(0, 0, 0);
	$dt_date_absence_eleve_fin->setTime(23, 59, 59);
	$inverse_date=false;
	if($dt_date_absence_eleve_debut->format("U")>$dt_date_absence_eleve_fin->format("U")){
	  $date2=clone $dt_date_absence_eleve_fin;
	  $dt_date_absence_eleve_fin= $dt_date_absence_eleve_debut;
	  $dt_date_absence_eleve_debut= $date2;
	  $inverse_date=true;
	}
	$_SESSION['statJustifie']['date_absence_eleve_debut'] = serialize($dt_date_absence_eleve_debut->format('d/m/Y'));
	$_SESSION['statJustifie']['date_absence_eleve_fin'] = serialize($dt_date_absence_eleve_fin->format('d/m/Y'));
	// on récupère les élèves
	$eleve_col = unserialize($_SESSION['statJustifie']['eleve_col']);
	// on récupère le type de statistique
	$donneeBrut = $_POST['donneeBrut'];
	$_SESSION['statJustifie']['donneeBrut'] = $donneeBrut;
	// on efface la dernière position si besoin
	$_SESSION['statJustifie']['dernierePosition'] = -1;
	// on vérifie si on ne veut que les erreurs
	if ($_POST['valide'] == "erreur") {
	  $_SESSION['statJustifie']['erreur'] = TRUE;
	} else {
	  $_SESSION['statJustifie']['erreur'] = FALSE;
	}
	// on affiche la page de chargement
	afficheChargement($_SESSION['statJustifie']['dernierePosition'], count($eleve_col),$_SESSION['statJustifie']['erreur']);
  }
  
} else {
/***** On revient depuis une autre page *****/
  //On récupère le tableau
  if (isset($_SESSION['statJustifie']['donnees'])) {
	$donnees = $_SESSION['statJustifie']['donnees'];
  } else{
	$donnees = array();
  }
  // on récupère les justifications
  $justifie_col = unserialize($_SESSION['statJustifie']['justifications']);
  // on récupère les dates
  $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_debut'])));
  $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_fin'])));
  // on récupère $donneeBrut
  $donneeBrut = isset($_SESSION['statJustifie']['donneeBrut']) ? $_SESSION['statJustifie']['donneeBrut'] : FALSE;
  
	$dt_date_absence_eleve_debut->setTime(0, 0, 0);
	$dt_date_absence_eleve_fin->setTime(23, 59, 59);
  
}


/* *******************************************************************************
 * Affichage
 ******************************************************************************* */

//==============================================
$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";

$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "lib/tablekit";

$titre_page = "Répartition des justifications";
$utilisation_jsdivdrag = "non";
$utilisation_scriptaculous="ok";
$utilisation_win = 'oui';
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
//===========================

?>
<div id="contain_div" class="css-panes">
  <form dojoType="dijit.form.Form" action="stat_justifications.php" method="post" id="filtres" style="text-align: center;">
	<fieldset>
	  <legend>Paramétrage des statistiques sur les justifications</legend>
	  <p>
		<label for="bouton1">Données brutes</label>
		<input type="radio" 
			   name="donneeBrut" 
			   value="<?php echo TRUE; ?>" 
			   id="bouton1" 
			   <?php if ($donneeBrut) echo "checked = 'checked'"; ?> />
		<input type="radio" 
			   name="donneeBrut" 
			   value="<?php echo FALSE; ?>" 
			   id="bouton2"
			   <?php if (!$donneeBrut) echo "checked = 'checked'"; ?> />

		<label for="bouton2">½ journées</label>
	  </p>
	  <p>
		  <label for="date_absence_eleve_debut">Bilan du</label>
		<input style="width : 8em;font-size:14px;" 
			   type="text" 
			   dojoType="dijit.form.DateTextBox" 
			   id="date_absence_eleve_debut" 
			   name="date_absence_eleve_debut" 
			   value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
		<label for="date_absence_eleve_fin">au</label>
		<input style="width : 8em;font-size:14px;" 
			   type="text" 
			   dojoType="dijit.form.DateTextBox" 
			   id="date_absence_eleve_fin" 
			   name="date_absence_eleve_fin" 
			   value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
      </p>
	  <p>      
		<button type="submit" dojoType="dijit.form.Button" name="valide" value="calcul" title="Recalculer la page et afficher le tableau">
		  Forcer le recalcul des statistiques
		</button> 
		<button type="submit" 
				dojoType="dijit.form.Button" 
				name="valide" 
				value="erreur" 
				title="N'afficher que les enregistrements où le total des justifications diffère du nombre d'absences justifiées">
		  N'afficher que les différences de total
		</button> 
		<button type="submit" dojoType="dijit.form.Button" name="valide" value="soumettre" title="Recalculer et afficher le tableau">
		  Afficher les statistiques avec ces réglages
		</button> 
		<button type="submit" dojoType="dijit.form.Button" name="valide" value="csv" title="Exporter le tableau au format texte (Comma-separated values)">
		  Export .csv
		</button>
		<button type="submit" dojoType="dijit.form.Button" name="valide" value="ods" title="Exporter le tableau au format OpenDocument (OASIS)">
		  Export .ods
		</button>
	  </p>
	</fieldset>
  </form>
  
  <table  class="sortable" style ="border:3px groove #aaaaaa;">
	<caption style ="font-size:larger;" >
	  Justifications du
	  <?php echo unserialize($_SESSION['statJustifie']['date_absence_eleve_debut']); ?>
	  au
	  <?php echo unserialize($_SESSION['statJustifie']['date_absence_eleve_fin']); ?> 
	  <?php if ($donneeBrut) {
		echo 'Données brutes';
	  } else {
		echo '½ journées';
	  } ?>
	  (<?php echo count($donnees); ?> élèves)
	</caption>
	
	<tr  style ="border:3px groove #aaaaaa;">
	  <th title ="Cliquez pour trier sur la colonne">
		Nom Prénom
	  </th>
	  <th title ="Cliquez pour trier sur la colonne">
		Classe
	  </th>
	  <th class="number" title ="Cliquez pour trier sur la colonne">
		Retards
	  </th>
	  <th class="number" title ="Cliquez pour trier sur la colonne">
		1/2 journées non justifiées
	  </th>
	  <th class="number" title ="Cliquez pour trier sur la colonne">
		1/2 journées justifiées
	  </th>
<?php foreach ($justifie_col as $justifie) {  ?>
	  <th class="number" title ="Cliquez pour trier sur la colonne">
		<?php echo $justifie->getNom();  ?>
	  </th>
<?php } ?>
	</tr>
	
	
	
<?php if (count($donnees)) {
foreach ($donnees as $donnee) { ?>
	<tr>
	  <td style ="border:1px groove #aaaaaa;">
		<?php echo $donnee['nom']." ".$donnee['prenom']; ?>
	  </td>
	  <td style="border:1px groove #aaaaaa;text-align: center;">
		<?php echo $donnee['classe']; ?>
	  </td>
	  
	  <td style="border:1px groove #aaaaaa;text-align: center;">
		<?php echo $donnee['retards']; ?>
	  </td>
	  <td style="border:1px groove #aaaaaa;text-align: center;">
		<?php echo $donnee['non_justifiees']; ?>
	  </td>
	  <td style="border:1px groove #aaaaaa;text-align: center;
<?php if (!$donneeBrut && ($donnee['totalDemi'] != $donnee['justifiees'])) echo 'background:#ff0000;'; ?>
		  ">
		<?php echo $donnee['justifiees']; ?>
	  </td>
<?php foreach ($donnee['traitement'] as $justifie) { ; ?>
	  <td style="border:1px groove #aaaaaa;text-align: center;">
		<?php echo $justifie; ?>
	  </td>
<?php } ; ?>
	</tr>
<?php } 
} ?>
  </table>
</div>
 
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");   
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.DateTextBox");    
    dojo.require("dijit.form.Select");
    dojo.require("dijit.form.NumberTextBox");
    dojo.require("dijit.form.TextBox");
    </script>';
require_once("../lib/footer.inc.php");
?>
