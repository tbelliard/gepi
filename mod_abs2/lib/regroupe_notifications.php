<?php
$eleve_col = new PropelCollection();
$elevesLogin=array();
$tableNotifications=array();

foreach ($notifications_col as $notification) {
			if ($notification->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
				continue;
			}
	
    if ($notification->getAbsenceEleveTraitement() != null) {
		foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
			$eleve_col->add ($saisie->getEleve());
			
			if (!isset ($tableNotifications[$saisie->getEleve()->getLogin()]['notif_id_col'])) {
				// première fois qu'on trouve un élève
				$tableNotifications[$saisie->getEleve()->getLogin()]['notif_id_col'] = new PropelCollection();
				$tableNotifications[$saisie->getEleve()->getLogin()]['notif_col'] = new PropelCollection();
				$tableNotifications[$saisie->getEleve()->getLogin()]['notif_id']='';
				$tableNotifications[$saisie->getEleve()->getLogin()]['eleve'] = new PropelCollection();
				$tableNotifications[$saisie->getEleve()->getLogin()]['saisies'] = new PropelCollection();
				
				// On récupère les infos de l'élève
				$tableNotifications[$saisie->getEleve()->getLogin()]['getNom'] = $saisie->getEleve()->getNom();
				$tableNotifications[$saisie->getEleve()->getLogin()]['getPrenom'] = $saisie->getEleve()->getPrenom();
				$tableNotifications[$saisie->getEleve()->getLogin()]['getClasseNomComplet'] = $saisie->getEleve()->getClasseNomComplet();
				$tableNotifications[$saisie->getEleve()->getLogin()]['getId'] = $saisie->getEleve()->getId();
				$tableNotifications[$saisie->getEleve()->getLogin()]['responsable'] = $notification->getResponsableEleves();
						
				//on va mettre les champs dans des variables simple
				//on fait un petit traitement pour bien formatter ça si on a un ou deux responsables, avec le même nom de famille ou pas.
				if ($notification->getAdresse() != null && $notification->getResponsableEleves()->count() == 1) {
					$responsable = $notification->getResponsableEleves()->getFirst();
					$destinataire = $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.strtoupper($responsable->getPrenom());
				} elseif ($notification->getAdresse() != null&& $notification->getResponsableEleves()->count() == 2) {
					$responsable1 = $notification->getResponsableEleves()->getFirst();
					$responsable2 = $notification->getResponsableEleves()->getNext();
					if (strtoupper($responsable1->getNom()) == strtoupper($responsable2->getNom())) {
					$destinataire = $responsable1->getCivilite().' et '.$responsable2->getCivilite().' '.strtoupper($responsable1->getNom());
					} else {
					$destinataire = $responsable1->getCivilite().' '.strtoupper($responsable1->getNom());
					$destinataire .= ' et '.$responsable2->getCivilite().' '.strtoupper($responsable2->getNom());
					}
				} else {
					$destinataire = '';
				}
				$tableNotifications[$saisie->getEleve()->getLogin()]['destinataire'] = $destinataire;
				
				$adr = $notification->getAdresse();
				if ($adr == null) {
					$adr = new Adresse();
				}

				$tableNotifications[$saisie->getEleve()->getLogin()]['adr'] = $adr;
				
			}
			$tableNotifications[$saisie->getEleve()->getLogin()]['notif_id_col']->add($notification->getId());
			$tableNotifications[$saisie->getEleve()->getLogin()]['notif_col']->add($notification);
			$tableNotifications[$saisie->getEleve()->getLogin()]['eleve']->add ($saisie->getEleve());
			$tableNotifications[$saisie->getEleve()->getLogin()]['saisies']->add ($saisie);
			$tableNotifications[$saisie->getEleve()->getLogin()]['notif_id'] = $tableNotifications[$saisie->getEleve()->getLogin()]['notif_id']."-".$notification->getId();
			
		}
    }
}

// On récupère l'heure de demi-journée
$heure_demi_journee = 11;
$minute_demi_journee = 50;
if (getSettingValue("abs2_heure_demi_journee") != null) {
	try {
	$dt_demi_journee = new DateTime(getSettingValue("abs2_heure_demi_journee"));
	$heure_demi_journee = $dt_demi_journee->format('H');
	$minute_demi_journee = $dt_demi_journee->format('i');
	} catch (Exception $x) {
	}
}
$temps_demi_journee = $heure_demi_journee.$minute_demi_journee;
	


$indice=0;
//on charge le modele et on merge les données de l'établissement
$TBS=AbsencesNotificationHelper::MergeInfosEtab($courrier_lot_modele);

$courrier_nouvellement_envoyes_col = new PropelCollection();

foreach ($tableNotifications as &$notification) {
	
	
	$idNotifications = '';
	
	foreach ($notification['notif_col'] as &$notif) {
		//on met un code d'erreur au cas ou la generation se fait mal
			$notif->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ECHEC);
			$notif->setUpdatedAt('now');
			$notif->save();
			$courrier_nouvellement_envoyes_col->append($notif);
		
	//on crée l'id
		$idNotifications = $idNotifications.'-'.$notif->getId();
	}
	
	$notification['notif_id'] = $idNotifications;
	
	$recap = $idNotifications.', ';
	foreach ($notification['responsable'] as $responsable) {
		$recap .= $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.$responsable->getPrenom().' ';
	}
	$courrier_recap_col->append($recap);
		
// merger toutes les saisies
	$tableSaisie = array();
	
// Il faut trier les saisies sur debut d'absence
	$toutesSaisies = array();
	foreach ($notification['saisies'] as $saisie) {
		$toutesSaisies[$saisie->getDebutAbs('U')] = $saisie;
		// echo $saisie->getDebutAbs('U').'<br />';
	}
	
	ksort($toutesSaisies);
	
	/*
	echo '<br />';
	
	foreach ($toutesSaisies as $saisie) {
		echo $saisie->getDebutAbs('U').'<br />';
	}
	
	echo '<br />';
	 * 
	 */
	
	// print_r($toutesSaisies);
	
	// $notification['saisies']->uksort('TriSaisie');
	// $notification['saisies']->uasort('TriSaisie');
	// usort($notification['saisies'], 'TriSaisie');
	
	// $notification['saisies']->uksort("TriSaisie");
	// usort($saisiesEleve, array("AbsenceEleveSaisie", "TriSaisie"));
	
	// $notification['saisies']->uasort(array("AbsenceEleveSaisie", "TriSaisie"));
	
/*	
	foreach ($notification['saisies'] as $saisie) {
		$tableSaisie[$saisie->getFirst()->getDebutAbs('U')]
	}
	*/
	
	// foreach ($notification['saisies'] as $saisie) {
	
	$notification['saisies'] = new PropelCollection();
	
	foreach ($toutesSaisies as $saisie) {
		$str = $saisie->getDateDescription();
		if($saisie->getGroupeNameAvecClasses()!=''){
			$str.= ', cours de '.$saisie->getGroupeNameAvecClasses();
		}   
		$tableSaisie[] = $str;
		$notification['saisies']->add($saisie);
	}
	$notification['saisies_eleve'] = $tableSaisie;
	
	
	$demi_journee_string_col = new PropelCollection();array ();
	$abs_col = $notification['saisies'];
	require_once("helpers/AbsencesEleveSaisieHelper.php");
	$demi_j = AbsencesEleveSaisieHelper::compte_demi_journee($abs_col);
	
	foreach($demi_j as $date) {
		$str = 'Le ';
		$str .= (strftime("%a %d/%m/%Y", $date->format('U')));
		if ($date->format('H') < 12) {
		$next_date = $demi_j->getNext();
		if ($next_date != null && $next_date->format('Y-m-d') == $date->format('Y-m-d')) {
			$str .= ' la journée';
		} else {
			$str .= ' le matin';
			//on recule le pointeur car on l'a avancé avec $demi_j->getNext()
			$demi_j->getPrevious();
		}
		} else {
		$str .= ' l\'après midi';
		}
		$demi_journee_string_col->append($str);
	}
	$notification['demi_j_string'] = $demi_journee_string_col;
	
	
	
	
	

}



	
	
    //on imprime le global
	$TBS->MergeBlock('notifications',$tableNotifications);

    $TBS->MergeField('nb_impressions',count($tableNotifications));
    $TBS->MergeBlock('courrier_recap_col',$courrier_recap_col);


		foreach($courrier_nouvellement_envoyes_col as $notif) {
		$notif->setDateEnvoi('now');
		$notif->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_EN_COURS);
		$notif->setErreurMessageEnvoi('');
		$notif->save();
		}
 

// On vérifie ce qu'on obtient
// print_r($notification);
/* *
foreach ($tableNotifications as $notification) {
	echo $notification['destinataire'].'<br />';
	echo $notification['adr']->getAdr1().'<br />';
	echo $notification['adr']->getAdr2().'<br />';
	echo $notification['adr']->getCp().'<br />';
	echo $notification['adr']->getCommune().'<br />';
	echo $notification['adr']->getPays().'<br />';
	echo $notification['getNom'].'<br />';
	echo $notification['getPrenom'].'<br />';
	echo $notification['notif_id'].'<br />';
	
	echo '- - -';
	echo '<br /><br />';
}
/*  */

//	die ();
