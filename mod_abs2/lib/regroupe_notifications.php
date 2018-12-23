<?php
$abs2_notifications_detaillees=getSettingAOui('abs2_notifications_detaillees');

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
	//==============================
	// 20181221
	/*
	echo "<pre>";
	print_r($notification);
	echo "</pre>";
	die();
	*/
	//==============================
	require_once("helpers/AbsencesEleveSaisieHelper.php");
	if(!$abs2_notifications_detaillees) {
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
	else {
		// 20181221 détails des absences
		$tab_details=new PropelCollection();array();
		$tab_englobante_ou_pas_mais_deja_affichee=array();
		/*
		echo "abs_col:<pre>";
		print_r($abs_col);
		echo "</pre>";
		*/
		$tab_saisies_selectionnees=array();
		foreach($abs_col as $current_abs) {
			$tab_saisies_selectionnees[]=$current_abs->getPrimaryKey();
		}
		/*
		echo "tab_saisies_selectionnees:<pre>";
		print_r($tab_saisies_selectionnees);
		echo "</pre>";
		*/

		foreach($abs_col as $current_abs) {
			// Il faut ne récupérer que la saisie englobante, pas les détails heure par heure.
			// Récupérer la saisie englobante si elle existe et tester si l'index est dans un tableau tab_englobante_ou_pas_mais_deja_affichee

			//echo "<br /><p>Parcours de current_abs n°".$current_abs->getPrimaryKey()."</p>";
			if(in_array($current_abs->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
				//echo "La saisie a déjà été considérée.<br />";
			}
			else {

				$a_selectionner=true;
				$saisies_englobante_col = $current_abs->getAbsenceEleveSaisiesEnglobantes();
				if (!$saisies_englobante_col->isEmpty()) {
					foreach ($saisies_englobante_col as $saisies_englobante) {
						//echo "On teste \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()."<br />";
						if(in_array($saisies_englobante->getPrimaryKey(), $tab_saisies_selectionnees)) {
							//echo "La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." fait partie de celles sélectionnées dans abs_col.<br />";
							// Il faut que la saisie englobante soit dans la liste des current_abs quand même
							if(in_array($saisies_englobante->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
								$a_selectionner=false;
								$details="Du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U'));
								//echo "<p style='color:red'>".$details."</p>";
							}
							else {
								// Ajouter à la chaine les infos de $saisies_englobante_col
								//echo "\$current_abs->getPrimaryKey()=".$current_abs->getPrimaryKey()." est englobée par \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()."<br />On va afficher la saisie englobante seulement.<br />";
								if(strftime("%d/%m/%Y", $saisies_englobante->getDebutAbs('U'))==strftime("%d/%m/%Y", $saisies_englobante->getFinAbs('U'))) {
									$details="Le ".strftime("%a %d/%m/%Y", $saisies_englobante->getDebutAbs('U'))." de ".strftime("%H:%M", $saisies_englobante->getDebutAbs('U'))." à ".strftime("%H:%M", $saisies_englobante->getFinAbs('U'));
								}
								else {
									$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));
								}
								$tab_details->append($details);

								//echo "<p style='color:blue'>".$details."</p>";

								$tab_englobante_ou_pas_mais_deja_affichee[]=$saisies_englobante->getPrimaryKey();

								// Pour ne pas afficher la saisie englobée
								$a_selectionner=false;
							}
						}
						else {
							//echo "La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." ne fait pas partie des saisies choisies dans abs_col (on ne va pas l'afficher)<br />";

							// La saisie englobante n'est pas dans celles sélectionnées pour le traitement courant.
							// On ne la mentionne pas.
							// Mais il faudrait voir si il faut quand même mentionner $current_abs ou si la saisie est englobée par une autre acceptée.
							$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));

							//echo "<p style='color:plum'>".$details."</p>";

							$tab_englobante_ou_pas_mais_deja_affichee[]=$saisies_englobante->getPrimaryKey();
						}
					}
				}

				if($a_selectionner) {
					if(strftime("%d/%m/%Y", $current_abs->getDebutAbs('U'))==strftime("%d/%m/%Y", $current_abs->getFinAbs('U'))) {
						$details="Le ".strftime("%a %d/%m/%Y", $current_abs->getDebutAbs('U'))." de ".strftime("%H:%M", $current_abs->getDebutAbs('U'))." à ".strftime("%H:%M", $current_abs->getFinAbs('U'));
					}
					else {
						$details="Du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U'));
					}
					$tab_details->append($details);

					//echo "<p style='color:green'>".$details."</p>";
					$tab_englobante_ou_pas_mais_deja_affichee[]=$current_abs->getPrimaryKey();
				}
			}
		}

		/*
		echo "<pre>";
		print_r($demi_journee_string_col);
		echo "</pre>";
		echo "<pre>";
		print_r($tab_details);
		echo "</pre>";
		*/

		//var_dump($demi_journee_string_col);die;
		//if (count($demi_journee_string_col) == 0) die;

		//$TBS->MergeBlock('demi_j_string_eleve_id_'.$eleve->getId(), $tab_details);
		$notification['demi_j_string'] = $tab_details;
	}

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
