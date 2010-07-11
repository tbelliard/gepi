<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

if ($utilisateur->getStatut()=="professeur" &&  getSettingValue("active_module_absence_professeur")!='y') {
    die("Le module n'est pas activé.");
}

//récupération des paramètres de la requète
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :NULL);
$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] :(isset($_GET["commentaire"]) ? $_GET["commentaire"] :NULL);
$modif = isset($_POST["modif"]) ? $_POST["modif"] :(isset($_GET["modif"]) ? $_GET["modif"] :NULL);

$message_enregistrement = '';
$notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);
if ($notification == null && !isset($_POST["creation_notification"])) {
    $message_enregistrement .= 'Generation impossible : notification non trouvée. ';
    include("visu_notification.php");
    die();
}

// load the TinyButStrong libraries
if (version_compare(PHP_VERSION,'5')<0) {
    include_once('../tbs/tbs_class.php'); // TinyButStrong template engine for PHP 4
} else {
    include_once('../tbs/tbs_class_php5.php'); // TinyButStrong template engine
}
// load the OpenTBS plugin
include_once('../tbs/plugins/tbs_plugin_opentbs.php');
include_once('../tbs/plugins/tbsdb_php.php');

$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_COURRIER) {
     // Load the template
    $TBS->LoadTemplate('modeles/modele_lettre_parents.odt');

    //on va mettre les champs dans des variables simple
    if ($notification->getResponsableEleveAdresse()->getResponsableEleves()->count() == 1) {
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getFirst();
	$destinataire = $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.strtoupper($responsable->getPrenom());
    } else {
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getFirst();
	$destinataire = $responsable->getCivilite().' '.strtoupper($responsable->getNom());
	$responsable = $notification->getResponsableEleveAdresse()->getResponsableEleves()->getNext();
	$destinataire .= '  '.strtoupper($responsable->getCivilite()).' '.strtoupper($responsable->getNom());;
    }
    $TBS->MergeField('destinataire',$destinataire);

    $adr = $notification->getResponsableEleveAdresse();
    $TBS->MergeField('adr',$adr);


    $TBS->MergeField('nom_etab',getSettingValue("gepiSchoolName"));
    $adr_etablissement = new ResponsableEleveAdresse();
    $adr_etablissement->setAdr1(getSettingValue("gepiSchoolAdress1"));
    $adr_etablissement->setAdr2(getSettingValue("gepiSchoolAdress2"));
    $adr_etablissement->setCp(getSettingValue("gepiSchoolZipCode"));
    $adr_etablissement->setCommune(getSettingValue("gepiSchoolCity"));
    $TBS->MergeField('adr_etab',$adr_etablissement);

    //telephone fax mail
    $TBS->MergeField('tel_etab',getSettingValue("gepiSchoolTel"));
    $TBS->MergeField('fax_etab',getSettingValue("gepiSchoolFax"));
    $TBS->MergeField('mail_etab',getSettingValue("gepiSchoolEmail"));

    //on récupère la liste des noms d'eleves
    $eleve_col = new PropelCollection();
    foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
	$eleve_col->add($saisie->getEleve());
    }

    $TBS->MergeBlock('el_col',$eleve_col);

    $query_string = 'AbsenceEleveSaisieQuery::create()->filterByEleveId(%p1%)
	->useJTraitementSaisieEleveQuery()
	->filterByATraitementId('.$notification->getAbsenceEleveTraitement()->getId().')->endUse()
	    ->orderBy("DebutAbs", Criteria::ASC)
	    ->find()';

    $TBS->MergeBlock('saisies', 'php', $query_string);
//$notification->getResponsableEleveAdresse()->getResponsableEleves()->getFirst();
//$responsable = new ResponsableEleve();
//$responsable->getPrenom()()
    // Load the template

    // Output as a download file (some automatic fields are merged here)
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'absence.odt');

    // Save as file on the disk (code example)
    //$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
} else if ( $modif == 'statut') {
    if (isset(AbsenceEleveNotification::$LISTE_LABEL_STATUT[$_POST["statut"]])) {
	$notification->setStatutEnvoi($_POST["statut"]);
    } else {
	$notification->setStatutEnvoi(0);
    }
} else if ( $modif == 'commentaire') {
    $notification->setCommentaire($_POST["commentaire"]);
} elseif ($modif == 'enlever_responsable') {
    if (0 != JNotificationResponsableEleveQuery::create()->filterByAbsenceEleveNotification($notification)->filterByPersId($_POST["pers_id"])->limit(1)->delete()) {
	$message_enregistrement .= 'Responsable supprimé';
    } else {
	$message_enregistrement .= 'Suppression impossible';
    }
    include("visu_notification.php");
    die;
} elseif ($modif == 'ajout_responsable') {
    $responsable = ResponsableEleveQuery::create()->findOneByPersId($_POST["pers_id"]);
    if ($responsable != null && !$notification->getResponsableEleves()->contains($responsable)) {
	$notification->addResponsableEleve($responsable);
	$notification->save();
    }
} elseif ($modif == 'email') {
    $notification->setEmail($_POST["email"]);
} elseif ($modif == 'tel') {
    $notification->setTelephone($_POST["tel"]);
}

if (!$notification->isModified()) {
    $message_enregistrement .= 'Pas de modifications';
} else {
    if ($notification->validate()) {
	$notification->save();
	$message_enregistrement .= 'Modification enregistrée';
    } else {
	$no_br = true;
	foreach ($notification->getValidationFailures() as $erreurs) {
	    $message_enregistrement .= $erreurs;
	    if ($no_br) {
		$no_br = false;
	    } else {
		$message_enregistrement .= '<br/>';
	    }
	}
	$traitement->reload();
    }
}

include("visu_notification.php");
?>