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

//récupération des paramètres de la requète
$menu = isset($_POST["menu"]) ? $_POST["menu"] :(isset($_GET["menu"]) ? $_GET["menu"] : NULL);
$id_notification = isset($_POST["id_notification"]) ? $_POST["id_notification"] :(isset($_GET["id_notification"]) ? $_GET["id_notification"] :NULL);
$commentaire = isset($_POST["commentaire"]) ? $_POST["commentaire"] :(isset($_GET["commentaire"]) ? $_GET["commentaire"] :NULL);
$modif = isset($_POST["modif"]) ? $_POST["modif"] :(isset($_GET["modif"]) ? $_GET["modif"] :NULL);

$message_enregistrement = '';
$notification = AbsenceEleveNotificationQuery::create()->findPk($id_notification);
if ($notification == null && !isset($_POST["creation_notification"])) {
    $message_enregistrement .= 'Modification impossible : notification non trouvée. ';
    include("visu_notification.php");
    die();
}

if ( isset($_POST["creation_notification"])) {
    $id_traitement = isset($_POST["id_traitement"]) ? $_POST["id_traitement"] :(isset($_GET["id_traitement"]) ? $_GET["id_traitement"] :NULL);
    $traitement = AbsenceEleveTraitementQuery::create()->findPk($id_traitement);
    if ($traitement == null) {
	$message_enregistrement .= 'Modification impossible : traitement non trouvé. ';
	include("visu_notification.php");
	die();
    } else {
	$notification = new AbsenceEleveNotification();
	$notification->setUtilisateurProfessionnel($utilisateur);
	$notification->setAbsenceEleveTraitement($traitement);

	//on met le type courrier par défaut
	$notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);

	$responsable_eleve1 = null;
	$responsable_eleve2 = null;
	foreach ($traitement->getResponsablesInformationsSaisies() as $responsable_information) {
	    if ($responsable_information->getNiveauResponsabilite() == '1') {
		$responsable_eleve1 = $responsable_information->getResponsableEleve();
	    } else if ($responsable_information->getNiveauResponsabilite() == '2') {
		$responsable_eleve2 = $responsable_information->getResponsableEleve();
	    }
	}
	if ($responsable_eleve1 != null) {
	    $notification->setEmail($responsable_eleve1->getMel());
	    $notification->setTelephone($responsable_eleve1->getTelPort());
	    $notification->setAdresseId($responsable_eleve1->getAdresseId());
	    $notification->addResponsableEleve($responsable_eleve1);
	}
	if ($responsable_eleve2 != null) {
	    if ($responsable_eleve1 == null
		    || $responsable_eleve2->getAdresseId() == $responsable_eleve1->getAdresseId()) {
		$notification->addResponsableEleve($responsable_eleve2);
	    }
	}
	$notification->save();
	$_POST["id_notification"] = $notification->getId();
	include("visu_notification.php");
	die();
    }
}

if ( $modif == 'type') {
    $notification->setTypeNotification($_POST['type']);
    $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
} else if ( $modif == 'statut') {
    $notification->setStatutEnvoi($_POST["statut"]);
} else if ( $modif == 'commentaire') {
    $notification->setCommentaire($_POST["commentaire"]);
} elseif ($modif == 'enlever_responsable') {
    if (0 != JNotificationResponsableEleveQuery::create()->filterByAbsenceEleveNotification($notification)->filterByResponsableEleveId($_POST["pers_id"])->limit(1)->delete()) {
	$message_enregistrement .= 'Responsable supprimé';
    } else {
	$message_enregistrement .= 'Suppression impossible';
    }
    include("visu_notification.php");
    die;
} elseif ($modif == 'ajout_responsable') {
    $responsable = ResponsableEleveQuery::create()->findOneByResponsableEleveId($_POST["pers_id"]);
    if ($responsable != null && !$notification->getResponsableEleves()->contains($responsable)) {
	$notification->addResponsableEleve($responsable);
	$notification->save();
        $message_enregistrement .= 'Responsable ajouté';
        include("visu_notification.php");
        die;
    }
} elseif ($modif == 'email') {
    $notification->setEmail($_POST["email"]);
} elseif ($modif == 'tel') {
    $notification->setTelephone($_POST["tel"]);
} elseif ($modif == 'adresse') {
    $notification->setAdresseId($_POST["adr_id"]);
} elseif ($modif == 'duplication') {
    $clone = $notification->copy(); //no deep copy
    $clone->save();
    $id = $clone->getId();
    //this is done to avoid a bug in deepcopy
    $notification->copyInto($clone, true);// deep copy
    $clone->setId($id);
    $clone->setNew(false);
    $clone->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
    $clone->setDateEnvoi(null);
    $clone->setErreurMessageEnvoi(null);
    $clone->save();
    $_POST["id_notification"] = $clone->getId();
    $message_enregistrement .= 'Nouvelle notification';
    include("visu_notification.php");
    die();
} elseif ($modif == 'duplication_par_responsable') {    
    $responsablesToAdd = new PropelCollection;
    $responsables_informations = $notification->getAbsenceEleveTraitement()->getResponsablesInformationsSaisies();
    Foreach ($responsables_informations as $responsable_information) {
        $responsable = $responsable_information->getResponsableEleve();
        if ($responsable == null || $notification->getResponsableEleves()->contains($responsable) || $responsable_information->getNiveauResponsabilite() == '0') {
            continue;
        }
        $responsablesToAdd->append($responsable);
    }
    foreach ($responsablesToAdd as $responsableToAdd) {
        $clone = $notification->copy(); //no deep copy
        $clone->save();
        $id = $clone->getId();
        //this is done to avoid a bug in deepcopy
        $notification->copyInto($clone, true); // deep copy        
        $clone->setId($id);
        $clone->setNew(false);
        $clone->setEmail($responsableToAdd->getMel());
        $clone->setTelephone($responsableToAdd->getTelPort());
        $clone->setAdresseId($responsableToAdd->getAdresseId());
        $clone->save();
        // On supprime les anciens responsables de la notification initiale
        $responsables = JNotificationResponsableEleveQuery::create()->filterByAbsenceEleveNotification($clone)->find();
        foreach ($responsables as $responsable) {
            $responsable->delete();
        }
        $clone->addResponsableEleve($responsableToAdd);
        $clone->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL);
        $clone->setDateEnvoi(null);
        $clone->setErreurMessageEnvoi(null);
        $clone->save();        
        $message_enregistrement .= 'Nouvelle notification <a href="./visu_notification.php?id_notification='.$clone->getId();
        if ($menu) {
            $message_enregistrement .='&menu=false';
        }
        $message_enregistrement .= '">'.$clone->getId().'</a> créée pour '.$responsableToAdd->getCivilite().' '.$responsableToAdd->getPrenom().' '.$responsableToAdd->getNom().' <br />';
    }
    include("visu_notification.php");
    die();
} elseif ($modif == 'supprimer') {
    $notification->delete();
    if ($menu) {
        include("visu_traitement.php");
    } else {
        include("liste_notifications.php");
    }
    die;
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
	$notification->reload();
    }
}

include("visu_notification.php");
?>