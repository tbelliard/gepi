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
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//mes fonctions
include("../edt_organisation/fonctions_calendrier.php");
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
define('ABS2', dirname(__FILE__));
define('GEPI', dirname(ABS2));
$dernierePosition = isset ($_SESSION['statJustifie']['dernierePosition']) ? $_SESSION['statJustifie']['dernierePosition'] : -1;
/* *******************************************************************************
 * Appel des bibliothèques de fonctions
 ******************************************************************************* */
include_once 'lib/function.php';

/* *******************************************************************************
 * Fonctions de la page
 ******************************************************************************* */
function getJustifications() {
  $justifie_query = AbsenceEleveJustificationQuery::create()->orderBy('SortableRank');
  return $justifie_query->distinct()->find();  
}

function getEleves() {  
  $eleve_query = EleveQuery::create();
  if ($id_classe !== null && $id_classe != -1 ) {
	$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
  }
  if ($nom_eleve !== null && $nom_eleve != '') {
	  $eleve_query->filterByNom('%'.$nom_eleve.'%');
  }
  if ($id_eleve !== null && $id_eleve != '') {
	  $eleve_query->filterByIdEleve($id_eleve);
  }
  return ($eleve_query->orderByNom()->orderByPrenom()->distinct()->find());
}

function traiteEleve($eleve,$date_debut, $date_fin, $justifie_col, $donneeBrut) {
  $donnees = array();
  $eleve_id = $eleve->getIdEleve();
  
  $propel_eleve = EleveQuery::create()->filterByIdEleve($eleve_id)->findOne();
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
		if ($donneeBrut== TRUE) {
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
		  $propel_traitEleveDemi = AbsenceEleveSaisieQuery::create()
			->filterByEleveId($eleve_id)
			->filterByPlageTemps($date_debut,$date_fin )
			->orderByDebutAbs()
			->useJTraitementSaisieEleveQuery()
			  ->useAbsenceEleveTraitementQuery()
				->filterByAJustificationId($justifie->getid())
			  ->endUse()
			->endUse()
			;
		  $traiteEleveDemi_col = $propel_traitEleveDemi->find();
		  $traiteEleveDemi = $propel_eleve->getDemiJourneesAbsenceParCollection($traiteEleveDemi_col);
		  $donnees[$eleve_id]['traitement'][] = $traiteEleveDemi->count();
		  $totalDemi += $traiteEleveDemi->count();
		}
	  }
	  $donnees[$eleve_id]['totalDemi']=$totalDemi;
	}
	unset ($eleveNbAbs, $traiteEleve_col, $propel_eleve, $propel_traitEleveDemi, $traiteEleveDemi, $traiteEleveDemi_col, $propel_traitEleve);
	
	return $donnees[$eleve_id];
}

/* *******************************************************************************
 * Récupération des données passées en $_POST
 ******************************************************************************* */
if (isset ($_POST)) unset ($_SESSION['statJustifie']['donnees']);
$donneeBrut = isset ($_POST['donneeBrut']) ? $_POST['donneeBrut'] : (isset ($_SESSION[abs2StatJustifications]['donneeBrut']) ? $_SESSION[abs2StatJustifications]['donneeBrut'] : TRUE);
$date_absence_eleve_debut = isset ($_POST['date_absence_eleve_debut']) ? $_POST['date_absence_eleve_debut'] : NULL;
$date_absence_eleve_fin = isset ($_POST['date_absence_eleve_fin']) ? $_POST['date_absence_eleve_fin'] : NULL;

/* *******************************************************************************
 * Recherche des justifications
 ******************************************************************************* */
// $justifie_query = AbsenceEleveJustificationQuery::create()->orderBy('SortableRank');
// $justifie_col = $justifie_query->distinct()->find();
$justifie_col = getJustifications();
$_SESSION['statJustifie']['justifications'] = serialize($justifie_col);

/* *******************************************************************************
 * Initialisation des dates
 ******************************************************************************* */
if ($date_absence_eleve_debut != NULL) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} elseif (isset ($_SESSION[abs2StatJustifications]['date_absence_eleve_debut'])) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_debut'])));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
}
if ($date_absence_eleve_fin != NULL) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
} elseif (isset ($_SESSION[abs2StatJustifications]['date_absence_eleve_fin'])) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", unserialize($_SESSION['statJustifie']['date_absence_eleve_fin'])));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
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

/* *******************************************************************************
 * recherche des élèves
 ******************************************************************************* */

$eleve_col = getEleves();

if ($eleve_col->isEmpty()) {
    echo"<h2 class='no'>Aucun élève trouvé.</h2>";
    die();
}

$_SESSION['statJustifie']['eleve_col'] = serialize($eleve_col);

foreach ($eleve_col as $eleve) {    
  if ($eleve_col->getPosition() <= $dernierePosition) {
	continue;
  }
  //on initialise les donnees pour le nouvel eleve
  $retour = traiteEleve($eleve, $dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin, $justifie_col, $donneeBrut);
  if (!empty ($retour)) {
	$_SESSION['statJustifie']['donnees'][] = $retour;
  }
  //on met à jour l'index  
  $_SESSION['statJustifie']['dernierePosition'] = $dernierePosition = $eleve_col->getPosition();
  
  // Si on est trop long, recharger la page
  
}

//on a passé tous les élèves, on réinitialise l'index  
$_SESSION['statJustifie']['dernierePosition'] = $dernierePosition = -1;
$donnees = $_SESSION['statJustifie']['donnees'];

// temps de chargement de la page
$timefin=time();
$duree = $timefin - $timeDebut;
echo " Durée de calcul de la page : ".$duree."s";

/* *******************************************************************************
 * Affichage
 ******************************************************************************* */

//==============================================
$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";

$style_specifique[] = "mod_abs2/lib/abs_style";
$javascript_specifique[] = "mod_abs2/lib/include";
$titre_page = "Absences du jour";
$utilisation_jsdivdrag = "non";
$utilisation_scriptaculous="ok";
$utilisation_win = 'oui';
$_SESSION['cacher_header'] = "y";
$dojo = true;
require_once("../lib/header.inc");
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
			   onchange="submit()"
			   <?php if ($donneeBrut) echo "checked = 'checked'"; ?> />
		<input type="radio" 
			   name="donneeBrut" 
			   value="<?php echo FALSE; ?>" 
			   id="bouton2"
			   onchange="submit()"
			   <?php if (!$donneeBrut) echo "checked = 'checked'"; ?> />

		<label for="bouton2">½ journées</label>
	  </p>
	  <p>Bilan du
		<input style="width : 8em;font-size:14px;" 
			   type="text" 
			   dojoType="dijit.form.DateTextBox" 
			   id="date_absence_eleve_debut" 
			   name="date_absence_eleve_debut" 
			   value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
		au
		<input style="width : 8em;font-size:14px;" 
			   type="text" 
			   dojoType="dijit.form.DateTextBox" 
			   id="date_absence_eleve_fin" 
			   name="date_absence_eleve_fin" 
			   value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
      </p>
	  <p>      
		<button type="submit" dojoType="dijit.form.Button" name="valide" value="Soumettre">
		  Afficher les statistiques
		</button>
	  </p>
	</fieldset>
  </form>
  
  <table  style ="border:3px groove #aaaaaa;">
	<caption>
	  Justifications du
	  <?php echo unserialize($_SESSION['statJustifie']['date_absence_eleve_debut']); ?>
	  au
	  <?php echo unserialize($_SESSION['statJustifie']['date_absence_eleve_fin']); ?>
	  (<?php echo count($donnees); ?> élèves)
	</caption>
	
	<tr  style ="border:3px groove #aaaaaa;">
	  <th>
		Nom Prénom
	  </th>
	  <th>
		Classe
	  </th>
	  <th>
		Retards
	  </th>
	  <th>
		1/2 journées non justifiées
	  </th>
	  <th>
		1/2 journées justifiées
	  </th>
<?php foreach ($justifie_col as $justifie) {  ?>
	  <th>
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
<?php // foreach ($donnee['traitement'] as $justifie) { ; ?>
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