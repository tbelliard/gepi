<?php
/**
 * 
 * 
 * @link liste_traitements.php
 */

# $results : collection de traitements affichés dans liste_traitements.php

# On crée une notification pret à envoyer

foreach ($results as $traitement) {
	# reprise du code de enregistrement_modif_notification ligne 86...
	
	$notification = new AbsenceEleveNotification();
	$notification->setUtilisateurProfessionnel($utilisateur);
	$notification->setAbsenceEleveTraitement($traitement);
	
	//on met le type courrier et le statut pret à envoyer
	$notification->setTypeNotification(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER);
    $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_PRET_A_ENVOYER);

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
}


# Voir comment on sort


	header("Location: liste_traitements.php");




	
	
	
	
	die ();
?>
