<?php
/**
 * Statistiques module Abs2
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

/* *******************************************************************************
 * Vérification des droits sur la page
 ******************************************************************************* */

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

/* *******************************************************************************
 * Appel des bibliothèques de fonctions
 ******************************************************************************* */
include_once 'lib/function.php';

/* *******************************************************************************
 * Recherche des justifications
 ******************************************************************************* */
$justifie_query = AbsenceEleveJustificationQuery::create()->orderBy('SortableRank');
$justifie_col = $justifie_query->distinct()->find();

/* *******************************************************************************
 * Initialisation des dates
 ******************************************************************************* */
//TODO gérer les dates
$date_absence_eleve_debut ='1/9/2011';
if ($date_absence_eleve_debut != null) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/", ".", $date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') , $dt_date_absence_eleve_debut->format('d'));
}
if ($date_absence_eleve_fin != null) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/", ".", $date_absence_eleve_fin));
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
    $_SESSION['date_absence_eleve_debut'] = $dt_date_absence_eleve_debut->format('d/m/Y');
    $_SESSION['date_absence_eleve_fin'] = $dt_date_absence_eleve_fin->format('d/m/Y');
}


/* *******************************************************************************
 * recherche des élèves
 ******************************************************************************* */
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


$timeDebut=time();

$eleve_col = $eleve_query->orderByNom()->orderByPrenom()->distinct()->find();
if ($eleve_col->isEmpty()) {
    echo"<h2 class='no'>Aucun élève avec les paramètres sélectionnés n'a été trouvé.</h2>";
    die();
}

$precedent_eleve_id = null;

foreach ($eleve_col as $eleve) {    
  $eleve_id = $eleve->getIdEleve();
  

  //on initialise les donnees pour le nouvel eleve
  if ($precedent_eleve_id != $eleve_id) {
  
	$propel_eleve = EleveQuery::create()->filterByIdEleve($eleve_id)->findOne();
	$eleveNbAbs['demi_journees'] = $propel_eleve->getDemiJourneesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
	$eleveNbAbs['retards'] = $propel_eleve->getRetards($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count(); 
	//echo '1/2 journées '.$eleveNbAbs['demi_journees']." - ".$eleveNbAbs['retards']." : ";
	if ($eleveNbAbs['demi_journees'] > 0 || $eleveNbAbs['retards'] > 0 ) {
	  $eleveNbAbs['non_justifiees'] = $propel_eleve->getDemiJourneesNonJustifieesAbsence($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)->count();
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
	  $donneeBrut= FALSE;
	  // Décompte en données brutes
	foreach ($justifie_col as $justifie) { 
		if ($donneeBrut== TRUE) {
		  $propel_traitEleve = AbsenceEleveTraitementQuery::create()->filterByAJustificationId($justifie->getid())
			->useJTraitementSaisieEleveQuery()
			  ->useAbsenceEleveSaisieQuery()
				->filterByEleveId($eleve->getIdEleve())
			  ->endUse()
			->endUse() ;

		  $traiteEleve_col = $propel_traitEleve;
		  $donnees[$eleve_id]['traitement'][] = $traiteEleve_col->distinct()->count();
		} else {
		  // Décompte en 1/2 journées
		  $propel_traitEleveDemi = AbsenceEleveSaisieQuery::create()
			->filterByEleveId($eleve->getIdEleve())
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
		}
	  
	  }
	}
		
	
	$precedent_eleve_id = $eleve_id;
	unset ($eleveNbAbs,$traiteEleve_col );
  }
}

// temps de chargement de la page
$timefin=time();
$duree = $timefin - $timeDebut;
echo "Durée de calcul de la page : ".$duree."s";

/* *******************************************************************************
 * Affichage
 ******************************************************************************* */

//==============================================
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
  
  <table>
	
	<tr>
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
	
	
	
<?php foreach ($donnees as $donnee) { ?>
	<tr>
	  <td>
		<?php echo $donnee['nom']." ".$donnee['prenom']; ?>
	  </td>
	  <td style="text-align: center;">
		<?php echo $donnee['classe']; ?>
	  </td>
	  
	  <td style="text-align: center;">
		<?php echo $donnee['retards']; ?>
	  </td>
	  <td style="text-align: center;">
		<?php echo $donnee['non_justifiees']; ?>
	  </td>
	  <td style="text-align: center;">
		<?php echo $donnee['justifiees']; ?>
	  </td>
<?php // foreach ($donnee['traitement'] as $justifie) { ; ?>
<?php foreach ($donnee['traitement'] as $justifie) { ; ?>
	  <td style="text-align: center;">
		<?php echo $justifie; ?>
	  </td>
<?php } ; ?>
	</tr>
<?php } ?>
  </table>
</div>
