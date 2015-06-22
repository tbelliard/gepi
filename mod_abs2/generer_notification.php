<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

include_once 'lib/function.php';

//récupération des paramètres de la requète
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :NULL);

$notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);

$retour_envoi = '';

if ($notification == null && !isset($_POST["creation_notification"])) {
    $message_enregistrement .= '<span style="color:red">Generation impossible : notification non trouvée.</span> ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER && $notification->getStatutEnvoi() != AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL) {
    $message_enregistrement .= '<span style="color:red">Génération impossible : envoi déjà effectué.</span> ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
    // Load the template
    // $modele_lettre_parents=repertoire_modeles("absence_modele_lettre_parents.odt");
    $modele_lettre_parents=repertoire_modeles("absence_modele_lettre_parents.odt");
	
    //include_once '../orm/helpers/AbsencesNotificationHelper.php';
	include_once 'lib/genere_table_notification.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $modele_lettre_parents);
    $TBS->MergeField('nb_impressions',1);
	
    $notification->setDateEnvoi('now');
    $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_EN_COURS);
    $notification->save();
	
$TBS->MergeBlock('notifications',$tableNotifications);
		
    // Output as a download file (some automatic fields are merged here)
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'abs_notif_'.$notification->getId().'.odt');
    die();

} else if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL) {
    // Load the template
    $email=repertoire_modeles('absence_email.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $email);
    $message = $TBS->Source;

    $retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notification, $message);

} else if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS) {
    // Load the template
    $sms=repertoire_modeles('absence_sms.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $sms);
    $message = $TBS->Source;

    $retour_envoi = AbsencesNotificationHelper::EnvoiNotification($notification, $message);
}
if ($notification->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES) {
    $message_enregistrement = '<span style="color:green">Envoi réussi.</span> '.$retour_envoi;
} else {
    $message_enregistrement = '<span style="color:red">Échec de l\'envoi.</span> '.$retour_envoi;
}
include('visu_notification.php');
?>
