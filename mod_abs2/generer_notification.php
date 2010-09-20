<?php
/**
 *
 * @version $Id$
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

$message_enregistrement = '';

if ($notification == null && !isset($_POST["creation_notification"])) {
    $message_enregistrement .= 'Generation impossible : notification non trouvée. ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() != AbsenceEleveNotification::$TYPE_COURRIER && $notification->getStatutEnvoi() != AbsenceEleveNotification::$STATUT_INITIAL) {
    $message_enregistrement .= 'Génération impossible : envoi déjà effectué. ';
    include("visu_notification.php");
    die();
}

if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_COURRIER) {
    // Load the template
    $modele_lettre_parents=repertoire_modeles("modele_lettre_parents.odt");
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $modele_lettre_parents);

    $notification->setDateEnvoi('now');
    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_EN_COURS);
    $notification->save();

    // Output as a download file (some automatic fields are merged here)
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    include_once('../tbs/tbs_class_php5.php');
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, 'abs_notif_'.$notification->getId().'.odt');
    die();

} else if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_EMAIL) {
    if ($notification->getEmail() == null || $notification->getEmail() == '') {
	$message_enregistrement = 'Echec de l\'envoi : email non renseigné.';
	include('visu_notification.php');
	die();
    }

    include('../lib/email_validator.php');
    if (!validEmail($notification->getEmail())) {
	$message_enregistrement = 'Echec de l\'envoi : adresse email non valide.';
	include('visu_notification.php');
	die();
    }
    
    // Load the template
    $email=repertoire_modeles('email.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $email);
    $message = $TBS->Source;

    $envoi = mail($notification->getEmail(),
	    "Notification d'absence ".getSettingValue("gepiSchoolName").' - Ref : '.$notification->getId().' -',
	    $message,
           "From: ".$email_abs_etab."\r\n"
           ."X-Mailer: PHP/" . phpversion());

    $notification->setDateEnvoi('now');
    if ($envoi) {
	$notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_SUCCES);
	$message_enregistrement = 'Email envoyé.';
    } else {
	$notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_ECHEC);
	$message_enregistrement = 'Echec de l\'envoi.';
    }
    $notification->save();

} else if ($notification->getTypeNotification() == AbsenceEleveNotification::$TYPE_SMS) {
    if (getSettingValue("abs2_sms")!='y') {
	$message_enregistrement = 'Envoi de sms désactivé.';
	include('visu_notification.php');
	die();
    }

    // Load the template
    $sms=repertoire_modeles('sms.txt');
    include_once '../orm/helpers/AbsencesNotificationHelper.php';
    $TBS = AbsencesNotificationHelper::MergeNotification($notification, $sms);
    $message = $TBS->Source;

    if (getSettingValue("abs2_sms_prestataire")=='tm4b') {
	$url = "http://www.tm4b.com/client/api/http.php";
	$hote = "tm4b.com";
	$script = "/client/api/http.php";
	$param['username'] = getSettingValue("abs2_sms_username"); // identifiant de notre compte TM4B
	$param['password'] = getSettingValue("abs2_sms_password"); // mot de passe de notre compte TM4B
	$param['type'] = 'broadcast'; // envoi de sms
	$param['msg'] = $message; // message que l'on désire envoyer

	$tel = $notification->getTelephone();
	if (substr($tel, 0, 1) == '0') {
	    $tel = '33'.substr($tel, 1, 9);
	}
	$param['to'] = $tel; // numéros de téléphones auxquels on envoie le message
	$param['from'] = getSettingValue("gepiSchoolName"); // expéditeur du message (first class uniquement)
	$param['route'] = 'business'; // type de route (pour la france, business class uniquement)
	$param['version'] = '2.1';
	$param['sim'] = 'yes'; // on active le mode simulation, pour tester notre script
    } else if (getSettingValue("abs2_sms_prestataire")=='123-sms') {
	$url = "http://www.123-SMS.net/http.php";
	$hote = "123-SMS.net";
	$script = "/http.php";
	$param['email'] = getSettingValue("abs2_sms_username"); // identifiant de notre compte TM4B
	$param['pass'] = getSettingValue("abs2_sms_password"); // mot de passe de notre compte TM4B
	$param['message'] = $message; // message que l'on désire envoyer
	$param['numero'] = $notification->getTelephone(); // numéros de téléphones auxquels on envoie le message
    }

    $requete = '';
    foreach($param as $clef => $valeur)  {
	$requete .= $clef . '=' . urlencode($valeur); // il faut bien formater les valeurs
	$requete .= '&';
    }

    if (in_array  ('curl', get_loaded_extensions())) {
	//on utilise curl pour la requete au service sms
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);
	$reponse = curl_exec($ch);
	curl_close($ch);
    } else {
	$longueur_requete = strlen($requete);
	$methode = "POST";
	$entete = $methode . " " . $script . " HTTP/1.1\r\n";
	$entete .= "Host: " . $hote . "\r\n";
	$entete .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$entete .= "Content-Length: " . $longueur_requete . "\r\n";
	$entete .= "Connection: close\r\n\r\n";
	$entete .= $requete . "\r\n";
	$socket = fsockopen($hote, 80, $errno, $errstr);
	if($socket) {
	    fputs($socket, $entete); // envoi de l'entete
	    while(!feof($socket)) {
		$reponseArray[] = fgets($socket); // recupere les resultats
	    }
	    $reponse = $reponseArray[8];
	    fclose($socket);
	} else {
	    $reponse = 'error : no socket available.';
	}
    }

    $notification->setDateEnvoi('now');

    //traitement de la réponse
    if (getSettingValue("abs2_sms_prestataire")=='tm4b') {
	if (substr($reponse, 0, 5) == 'error') {
	    $message_enregistrement .= 'Erreur : message non envoyé. Code erreur : '.$reponse;
	    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_ECHEC);
	    $notification->setErreurMessageEnvoi($reponse);
	} else {
	    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_SUCCES);
	    $message_enregistrement = 'Envoi réussi.';
	}
	$notification->save();
    } else if (getSettingValue("abs2_sms_prestataire")=='123-sms') {
	if ($reponse != '80') {
	    $message_enregistrement .= 'Erreur : message non envoyé. Code erreur : '.$reponse;
	    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_ECHEC);
	    $notification->setErreurMessageEnvoi($reponse);
	} else {
	    $notification->setStatutEnvoi(AbsenceEleveNotification::$STATUT_SUCCES);
	    $message_enregistrement = 'Envoi réussi.';
	}
	$notification->save();
    }
}

include('visu_notification.php');
?>
