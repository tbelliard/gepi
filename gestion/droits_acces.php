<?php
/*
*
* Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Begin standart header
$titre_page = "Droits d'accès";
$niveau_arbo = 1;
$gepiPathJava="./..";

// Initialisations files
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
// Check access

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

include "../class_php/gestion/class_droit_acces_template.php";


// ====== Initialisation des messages =====
$tbs_message = '';
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// on demande une validation quitte sans enregistrer les changements
$messageEnregistrer = $themessage;

// ====== Inclusion des balises head et du bandeau =====

include_once("../lib/header_template.inc.php");


/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			ENREGISTREMENT DES DONNÉES SI BESOIN
****************************************************************/

// Load settings
if (!loadSettings()) {
	die("Erreur chargement settings");
}
if (isset($_POST['OK'])) {
  $droitAffiche= new class_droit_acces_template($_POST);
} else {
  $droitAffiche= new class_droit_acces_template();
}
//debug_var();

if(isset($_POST['is_posted'])) {
	check_token(false);
}

//=======================================================================================

// DROITS ENSEIGNANT

$statutItem="enseignant";
$titreItem='GepiAccesReleveProf';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveProfTousEleves';
$texteItem="a accès aux relevés de notes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveProfToutesClasses';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPeutCreerBoitesProf';
$texteItem="a le droit de créer et paramétrer des ".$gepiSettings['gepi_denom_boite']."s dans ses carnets de notes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyennesProf';
$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyennesProfTousEleves';
$texteItem="a accès aux moyennes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyennesProfToutesClasses';
$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='GepiAccesBulletinSimpleProf';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleProfTousEleves';
$texteItem="a accès aux bulletins simples de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleProfToutesClasses';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='appreciations_types_profs';
$texteItem="peut utiliser des appréciations-types sur les bulletins.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='autoriser_correction_bulletin';
$texteItem="peut solliciter des corrections de ses appréciations sur les bulletins une fois la période (<em>partiellement</em>) close (<em>pour reformuler une appréciation, corriger des fautes... de frappe</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='autoriser_signalement_faute_app_prof';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCDTToutesClasses';
$texteItem="a accès à la visualisation des cahiers de textes de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesVisuToutesEquipProf';
$texteItem="a accès à la Visualisation de toutes les équipes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGestElevesProf';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est professeur.<br />(<em>ce droit donne aussi accès à l'adresse des parents, à l'INE de l'élève, à l'établissement d'origine,...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='AccesDerniereConnexionEleProfesseur';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionEleProfesseur';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionEleProfesseur';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionRespProfesseur';
$texteItem="a accès à la date de la dernière connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionRespProfesseur';
$texteItem="a accès aux statistiques de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionRespProfesseur';
$texteItem="a accès au détail de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='AccesProf_EdtProfs';
$texteItem="a accès aux emplois du temps des autres professeurs (<em>sous réserve que le <a href='../edt_organisation/edt.php' target='_blank'>module EDT soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPasswordReinitProf';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoProfesseur';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesFicheBienvenueProfesseur';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


if(getSettingValue('active_mod_ooo')=='y') {
  $titreItem='OOoUploadProf';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

if(getSettingValue('active_mod_alerte')=='y') {
  $titreItem='PeutChoisirAlerteSansSonProfesseur';
  $texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAProfTout';
$texteItem="a accès aux données d'années antérieures pour tous les ".$gepiSettings['denomination_eleves']."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAProfClasses';
$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des classes pour lesquelles il fournit un enseignement
			<br />(<em>sans nécessairement avoir tous les ".$gepiSettings['denomination_eleves']." de la classe</em>)<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAProfGroupes';
$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des groupes auxquels il enseigne
		  <br />(<em>il a ces ".$gepiSettings['denomination_eleves']." en classe</em>)<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

if(getSettingValue('active_mod_discipline')=='y') {

  $titreItem='';
  $texteItem="";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
    $tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='visuDiscProfClasses';
  $texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses classes.
	<br />(<em>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
  
  $titreItem='visuDiscProfGroupes';
  $texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses enseignements.
	<br />(<em>Par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

	if(getSettingValue('active_mod_ooo')=='y') {
		$titreItem='imprDiscProfRetenueOOo';
		$texteItem="peut imprimer dans le module Discipline une demande de Retenue au format OpenOffice pour un élève pour lequel le professeur saisit un incident";
		if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
			$tbs_message = 'Erreur lors du chargement de '.$titreItem;
	}
}

//+++++++++++++++++++++++++++
if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	  $titreItem='GepiAccesSaisieEctsProf';
	  $texteItem="a accès à la pré-saisie des mentions ECTS pour ses groupes.";
	  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
		$tbs_message = 'Erreur lors du chargement de '.$titreItem;

	  $titreItem='GepiAccesRecapitulatifEctsProf';
	  $texteItem="a accès aux récapitulatifs globaux des crédits ECTS pour ses classes.";
	  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
		$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}
//=======================================================================================

// DROITS PROFESSEUR PRINCIPAL

$statutItem="professeur_principal";

$titreItem='GepiAccesPPTousElevesDeLaClasse';
$texteItem="dans le cas où il y a plusieurs '".getSettingValue('gepi_prof_suivi')."' dans la classe, donner les mêmes droits à chaque '".getSettingValue('gepi_prof_suivi')." sur l'ensemble des élèves de la classe<br />(<em>sinon un élève n'est associé qu'à un seul ".getSettingValue('gepi_prof_suivi')."</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiRubConseilProf';
$texteItem="peut saisir les avis du conseil de classe pour sa classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CommentairesTypesPP';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe
	  <br />(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiProfImprBul';
$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
			(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiProfImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='autoriser_signalement_faute_app_pp';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>à ses collègues professeurs</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesRestrAccesAppProfP';
$texteItem="a accès au paramétrage des accès ".$gepiSettings['denomination_responsables']."/".$gepiSettings['denomination_eleves']." aux appréciations/avis des classes dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimplePP';
$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveProfP';
$texteItem="a accès aux relevés des classes dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiProfImprRelSettings';
$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGestElevesProfP';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi")."<br />(<em>ce droit donne aussi accès à l'adresse des parents, à l'INE de l'élève, à l'établissement d'origine,...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGestPhotoElevesProfP';
$texteItem="a accès à l'upload des photos de ses ".$gepiSettings['denomination_eleves']." si le module
  		trombinoscope est activé et si le ".$gepiSettings['denomination_professeur']." a accès aux fiches
		".$gepiSettings['denomination_eleves']." (<em>ci-dessus</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='AccesDerniereConnexionEleProfP';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionEleProfP';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionEleProfP';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionRespProfP';
$texteItem="a accès à la date de la dernière connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionRespProfP';
$texteItem="a accès aux statistiques de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionRespProfP';
$texteItem="a accès au détail de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAProfPrinc';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est ".$gepiSettings['denomination_professeur']." principal"."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiAccesSaisieEctsPP';
	$texteItem="peut saisir les crédits ECTS pour sa classe";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiAccesEditionDocsEctsPP';
	$texteItem="peut éditer les relevés ECTS pour sa classe";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='modExbPP';
$texteItem="peut créer des examens blancs pour les classes dont il est ".getSettingValue('gepi_prof_suivi');
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//=======================================================================================

// DROITS SCOLARITE

$statutItem="scolarite";
$titreItem='GepiRubConseilScol';
$texteItem="peut saisir les avis du conseil de classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CommentairesTypesScol';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
			(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiScolImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='autoriser_signalement_faute_app_scol';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='PeutDonnerAccesBullNotePeriodeCloseScol';
$texteItem="a accès à l'ouverture exceptionnelle de saisie/correction de notes du bulletin d'un enseignement particulier en période partiellement close<br />(<em>typiquement pour corriger une erreur sans devoir rouvrir complètement la période en saisie pour tous les professeurs, ni devoir passer par un compte secours pour faire la modification à la place du professeur.</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveScol';
$texteItem="a accès à tous les relevés de notes de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiScolImprRelSettings';
$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='PeutDonnerAccesCNPeriodeCloseScol';
$texteItem="a accès à l'ouverture exceptionnelle de saisie/correction dans le carnet de notes d'un enseignement particulier en période partiellement close<br />(<em>typiquement pour corriger une erreur sans devoir rouvrir complètement la période en saisie pour tous les professeurs</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCdtScol';
$texteItem="a accès à tous les cahiers de textes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCdtScolRestreint';
$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
			<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// ====== Visa des cahiers de texte =====
$titreItem='GepiAccesCdtVisa';
$texteItem="Peut viser les cahiers de textes ";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// ====== Droits sur la page cahiers de texte =====
if (getSettingValue('GepiAccesCdtScolRestreint') =='yes'
		||getSettingValue('GepiAccesCdtScol')=='yes'
		||getSettingValue('GepiAccesCdtVisa')=='yes'){
  // il faut pouvoir voir les cahiers de textes
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'yes'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'yes'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
} else {
  // il ne faut pas pouvoir voir les cahiers de textes même en accès direct à la page
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'no'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'no'))
	$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
}

// ====== Droits sur la page Visa des cahiers de texte =====
if (!$droitAffiche->ouvreDroits($statutItem, $titreItem, "/cahier_texte_admin/visa_ct.php"))
  $tbs_message = "Erreur lors de l'enregistrement des droits de ".$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesVisuToutesEquipScol';
$texteItem="a accès à la Visualisation de toutes les équipes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='ScolEditElevesGroupes';
$texteItem="peut modifier la liste des élèves participant à tel ou tel enseignement<br />
(<em>cela permet de prendre en compte les signalements d'erreurs d'affectation d'élèves remontés par les professeurs</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesTouteFicheEleveScolarite';
$texteItem="a le droit d'accéder à toutes les fiches élève";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionEleScolarite';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionEleScolarite';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionEleScolarite';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionRespScolarite';
$texteItem="a accès à la date de la dernière connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionRespScolarite';
$texteItem="a accès aux statistiques de connexion des des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionRespScolarite';
$texteItem="a accès au détail de connexion des des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMajSconetScol';
$texteItem="a le droit d'effectuer les mises à jour des tables élèves et responsables d'après les XML de Sconet";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirLieuxScol';
$texteItem="a accès à la définition des lieux d'incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirRolesScol';
$texteItem="a accès à la définition des rôles dans les incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirMesuresScol';
$texteItem="a accès à la définition des mesures prises ou demandées à la suite d'incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirSanctionsScol';
$texteItem="a accès à la définition des sanctions";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirNaturesScol';
$texteItem="a accès à la définition des natures d'incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirCategoriesScol';
$texteItem="a accès à la définition des catégories d'incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiDiscDefinirDestAlertesScol';
$texteItem="a accès à la définition des destinataires d'alertes suite à des incidents";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_ooo')=='y') {
	//+++++++++++++++++++++++++++

	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadScol';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadScolDiscipline';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Discipline.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadScolAbs2';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Absences 2.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadScolNotanet';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Notanet.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadScolECTS';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module ECTS.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPasswordReinitScolarite';
$texteItem="peut réinitialiser elle-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoScolarite';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesFicheBienvenueScolarite';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='ScolResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='ScolResetPassEle';
$texteItem="peut réinitialiser les mots de passe des comptes de statut élève<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAScolTout';
$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves']."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAScolResp';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." des classes dont il est responsable"."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiAccesSaisieEctsScolarite';
	$texteItem="peut saisir les crédits ECTS";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiAccesEditionDocsEctsScolarite';
	$texteItem="peut éditer les relevés d'ECTS";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiAccesRecapitulatifEctsScolarite';
	$texteItem="a accès aux récapitulatifs globaux des crédits ECTS.";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_alerte')=='y') {
  $titreItem='PeutChoisirAlerteSansSonScolarite';
  $texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//=======================================================================================

// DROITS CPE

$statutItem="cpe";

// Relevés de notes
$titreItem='GepiAccesReleveCpe';
$texteItem="a accès aux relevés de notes des élèves qu'il a en responsabilité";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveCpeTousEleves';
$texteItem="a accès à tous les relevés de notes de toutes les classes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiCpeImprRelSettings';
$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Bulletins
$titreItem='GepiCpeImprBul';
$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
			(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiCpeImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='autoriser_signalement_faute_app_cpe';
$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


// Avis du conseil de classe
$titreItem='GepiRubConseilCpe';
$texteItem="peut saisir les avis du conseil de classe (<em>pour les élèves qu'il a en responsabilité</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiRubConseilCpeTous';
$texteItem="peut saisir les avis du conseil de classe (<em>pour tous les élèves de toutes les classes</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CommentairesTypesCpe';
$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
			(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// CDT
$titreItem='GepiAccesCdtCpe';
$texteItem="a accès aux cahiers de textes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCdtCpeRestreint';
$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
			<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//Années antérieures
$titreItem='AACpeTout';
$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves']."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AACpeResp';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable"."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Mon compte
$titreItem='GepiPasswordReinitCpe';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoCpe';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesFicheBienvenueCpe';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeResetPassEle';
$texteItem="peut réinitialiser les mots de passe des comptes de statut élève<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++
/*

// Inutile: Pour le moment, le CPE n'a pas accès aux fiches responsables (seulement aux infos via Consultation d'un élève)

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
*/
//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Divers
$titreItem='GepiAccesVisuToutesEquipCpe';
$texteItem="a accès à la Visualisation de toutes les équipes<br />
(<em>Par défaut, un CPE ne voit que les équipes des classes dans lesquelles il est responsable d'au moins un élève</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeEditElevesGroupes';
$texteItem="peut modifier la liste des élèves participant à tel ou tel enseignement<br />
(<em>cela permet de prendre en compte les signalements d'erreurs d'affectation d'élèves remontés par les professeurs</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Photos
$titreItem='GepiAccesTouteFicheEleveCpe';
$texteItem="a le droit d'accéder à toutes les fiches élève<br />
(<em>Par défaut, un CPE ne voit que les fiches des élèves dont il est responsable</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionEleCpe';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionEleCpe';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionEleCpe';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDerniereConnexionRespCpe';
$texteItem="a accès à la date de la dernière connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesStatConnexionRespCpe';
$texteItem="a accès aux statistiques de connexion des des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesDetailConnexionRespCpe';
$texteItem="a accès au détail de connexion des des responsables d'".$gepiSettings['denomination_eleves'].".";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CpeAccesUploadPhotosEleves';
$texteItem="a accès à l'upload des photos des ".$gepiSettings['denomination_eleves']."<br />sous réserve d'activation du module Trombinoscopes (<em>";
if(!getSettingAOui('active_module_trombinoscopes')) {
	$texteItem.="in";
}
$texteItem.="actif</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

// Absences
$titreItem='GepiAccesAbsTouteClasseCpe';
$texteItem="a le droit d'accéder à toutes les classes pour saisir les absences";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesCpeAgregationAbs2';
$texteItem="a le droit d'accéder au remplissage/vidage de la table agrégation des absences";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

// Discipline
if(getSettingValue('active_mod_discipline')=='y') {
	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirLieuxCpe';
	$texteItem="a accès à la définition des lieux d'incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirRolesCpe';
	$texteItem="a accès à la définition des rôles dans les incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirMesuresCpe';
	$texteItem="a accès à la définition des mesures prises ou demandées à la suite d'incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirSanctionsCpe';
	$texteItem="a accès à la définition des sanctions";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirNaturesCpe';
	$texteItem="a accès à la définition des natures d'incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirCategoriesCpe';
	$texteItem="a accès à la définition des catégories d'incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='GepiDiscDefinirDestAlertesCpe';
	$texteItem="a accès à la définition des destinataires d'alertes suite à des incidents";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='DisciplineCpeChangeDeclarant';
	$texteItem="a le droit de changer le déclarant d'un incident";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
		  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

	$titreItem='DisciplineCpeChangeDefaut';
	$texteItem="Par défaut, a le droit de changer le déclarant d'un incident (si ce droit existe)";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
		  $tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

// OOo
if(getSettingValue('active_mod_ooo')=='y') {
//+++++++++++++++++++++++++++

	$titreItem='';
	$texteItem="";
	if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadCpe';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice personnels.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadCpeDiscipline';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Discipline.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadCpeAbs2';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Absences 2.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadCpeNotanet';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module Notanet.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;

  $titreItem='OOoUploadCpeECTS';
  $texteItem="a accès à l'upload de fichiers modèles OpenOffice pour le module ECTS.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}



//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_alerte')=='y') {
  $titreItem='PeutChoisirAlerteSansSonCpe';
  $texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//=======================================================================================

// DROITS ADMINISTRATEUR

$statutItem="administrateur";
$titreItem='GepiAdminImprBulSettings';
$texteItem="a accès au paramétrage de l'impression des bulletins";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAdminValidationCorrectionBulletins';
$texteItem="a accès à la validation des propositions de corrections des bulletins";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPasswordReinitAdmin';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoAdministrateur';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_alerte')=='y') {
  $titreItem='PeutChoisirAlerteSansSonAdministrateur';
  $texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
  if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
	$tbs_message = 'Erreur lors du chargement de '.$titreItem;
}

//=======================================================================================

// DROITS ELEVE

$statutItem='eleve';
$titreItem='autorise_edt_eleve';
$texteItem="a accès à son emploi du temps (ouvre également le droit aux parents)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveEleve';
$texteItem="a accès à ses relevés de notes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesColMoyReleveEleve';
$texteItem="a accès à la colonne moyenne du carnet de notes.<br />Notez que tant que la période n'est pas close, cette moyenne peut évoluer (<em>ajout de notes, modifications de coefficients,...</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesOptionsReleveEleve';
$texteItem="a accès au tableau des options du relevés de notes (<em>nom court, coef, date des devoirs, ...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyClasseReleveEleve';
$texteItem="a accès à la moyenne de la classe pour chaque devoir";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyMinClasseMaxReleveEleve';
$texteItem="a accès aux moyennes min/classe/max de chaque devoir";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEvalCumulEleve';
$texteItem="peut voir les évaluations cumulées (ouvre également le droit aux parents)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCahierTexteEleve';
$texteItem="a accès à son cahier de texte";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CDTPeutPointerTravailFaitEleve';
$texteItem="peut pointer les travaux faits ou non du CDT";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPasswordReinitEleve';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesFicheBienvenueEleve';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEquipePedaEleve';
$texteItem="a accès à l'équipe pédagogique le concernant";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCpePPEmailEleve';
$texteItem="a accès aux adresses email de son CPE et de son professeur principal (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEquipePedaEmailEleve';
$texteItem="a accès aux adresses email de l'équipe pédagogique le concernant";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleEleve';
$texteItem="a accès à ses bulletins simplifiés<br />";
  $acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
  if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
  $delais_apres_cloture=getSettingValue('delais_apres_cloture');
  if(!my_ereg("^[0-9]*$",$delais_apres_cloture)) {$delais_apres_cloture=0;}
  $texteItem.="<em>";
  if($acces_app_ele_resp=='manuel') {
	$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
  } elseif($acces_app_ele_resp=='date') {
	$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
  } elseif($acces_app_ele_resp=='periode_close') {
	$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
	if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
	$texteItem.= "la clôture de la période par un compte scolarité.";
	$texteItem.= "<br />";
  }
  $texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
  $texteItem.= "</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='GepiAccesBulletinSimpleClasseEleve';
$texteItem="a accès au bulletin simplifié du groupe-classe";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleColonneMoyClasseEleve';
$texteItem="a accès à la colonne moyenne de la classe pour les enseignements,... sur les bulletins simplifiés et sur les graphes<br />(<em>sous réserve que l'accès aux bulletins simplifiés ou aux graphes soit donné</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleColonneMoyClasseMinMaxEleve';
$texteItem="a accès aux valeurs min/max des moyennes de la classe<br />(<em>sous réserve que l'accès aux bulletins simplifiés et à la colonne Moyenne de la classe soient donnés</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGraphEleve';
$texteItem="a accès à la visualisation graphique de ses résultats<br />";
$texteItem.= "<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.= $delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
  $texteItem.= "<br />";
  $texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
  $texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGraphParamEleve';
$texteItem="a accès aux paramètres des graphes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGraphRangEleve';
$texteItem="a accès au choix permettant d'afficher son rang dans les graphes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAEleve';
$texteItem="a accès à ses données d'années antérieures"."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesModifMaPhotoEleve';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
				<br /><em>(voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEleTrombiTousEleves';
$texteItem="a accès au trombinoscope de tous les ".$gepiSettings['denomination_eleves']." de l'établissement.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEleTrombiElevesClasse';
$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_eleves']." de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />
				voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEleTrombiPersonnels';
$texteItem="a accès au trombinoscope de tous les personnels de l'établissement.<br />
			<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
			voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEleTrombiProfsClasse';
$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_professeurs']." de sa classe.<br />
				<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
				voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='visuEleDisc';
$texteItem="a accès dans le module Discipline aux incidents le concernant."."<br />(<em>sous réserve que le <a href='../mod_discipline/discipline_admin.php' target='_blank'>module Discipline soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//=======================================================================================

// DROITS RESPONSABLE

$statutItem='responsable';
$titreItem='active_absences_parents';
$texteItem="a accès aux absences des ".$gepiSettings['denomination_eleves']." dont il est responsable (affichage des saisies non traitées 4 heures après la création)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesReleveParent';
$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesColMoyReleveParent';
$texteItem="a accès à la colonne moyenne du carnet de notes.<br />Notez que tant que la période n'est pas close, cette moyenne peut évoluer (<em>ajout de notes, modifications de coefficients,...</em>).";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesOptionsReleveParent';
$texteItem="a accès au tableau des options du relevés de notes (<em>nom court, coef, date des devoirs,...</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyClasseReleveParent';
$texteItem="a accès à la moyenne de la classe pour chaque devoir";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesMoyMinClasseMaxReleveParent';
$texteItem="a accès aux moyennes min/classe/max de chaque devoir";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCahierTexteParent';
$texteItem="a accès au cahier de texte des ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='CDTPeutPointerTravailFaitResponsable';
$texteItem="peut pointer les travaux faits ou non du CDT pour les ".$gepiSettings['denomination_eleves']." dont il est responsable.";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiPasswordReinitParent';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AccesFicheBienvenueResponsable';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEquipePedaParent';
$texteItem="a accès à l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesCpePPEmailParent';
$texteItem="a accès aux adresses email du CPE et du professeur principal responsables des ".$gepiSettings['denomination_eleves']." dont il est responsable (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesEquipePedaEmailParent';
$texteItem="a accès aux adresses email de l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleParent';
$texteItem="a accès aux bulletins simplifiés des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";

$texteItem.="<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.= $delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
$texteItem.="<br />";
$texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
$texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='GepiAccesBulletinSimpleClasseResp';
$texteItem="a accès au bulletin simplifié du groupe-classe des élèves dont il est responsable";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleColonneMoyClasseResp';
$texteItem="a accès à la colonne moyenne de la classe pour les enseignements,... sur les bulletins simplifiés et sur les graphes<br />(<em>sous réserve que l'accès aux bulletins simplifiés ou aux graphes soit donné</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesBulletinSimpleColonneMoyClasseMinMaxResp';
$texteItem="a accès aux valeurs min/max des moyennes de la classe<br />(<em>sous réserve que l'accès aux bulletins simplifiés et à la colonne Moyenne de la classe soient donnés</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;


$titreItem='GepiAccesGraphParent';
$texteItem="a accès à la visualisation graphique des résultats des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";
$texteItem.= "<em>";
if($acces_app_ele_resp=='manuel') {
  $texteItem.= "L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='date') {
  $texteItem.= "L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.";
} elseif($acces_app_ele_resp=='periode_close') {
  $texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
  if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
  $texteItem.= "la clôture de la période par un compte scolarité.";
}
$texteItem.= "<br />";
$texteItem.= "Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion générale/Configuration générale</a>";
$texteItem.= "</em>";

if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGraphParamParent';
$texteItem="a accès aux paramètres des graphes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiAccesGraphRangParent';
$texteItem="a accès au choix permettant d'afficher son rang dans les graphes";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='AAResponsable';
$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable"."<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='visuRespDisc';
$texteItem="a accès dans le module Discipline aux incidents concernant les enfants dont il est responsable."."<br />(<em>sous réserve que le <a href='../mod_discipline/discipline_admin.php' target='_blank'>module Discipline soit activé</a></em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

$titreItem='GepiMemesDroitsRespNonLegaux';
$texteItem="donner les mêmes droits aux responsables non légaux (resp_legal=0) qu'aux responsables légaux sous réserve que les responsables non légaux disposent d'un compte d'utilisateur et que vous cochiez une case dans la fiche de ces responsables.<br />(<em>Note&nbsp;: A l'heure actuelle, créer des comptes resp_legal=0 ne présente pas d'intérêt sans donner de droits, donc sans cocher cette case</em>)";
if (!$droitAffiche->set_entree($statutItem, $titreItem, $texteItem))
  $tbs_message = 'Erreur lors du chargement de '.$titreItem;

//=======================================================================================

$tbs_message = $droitAffiche->get_erreurs();

if (isset($_POST['OK']) AND ($tbs_message=='')) {
  $tbs_message = "Les modifications ont été enregistrées !";
  $post_reussi=TRUE;
}
$tbs_msg=$tbs_message;

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/gestion/droit_acces_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($droitAffiche);




?>
