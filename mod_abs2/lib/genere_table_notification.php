<?php
/**
 *
 *
 *
 * Copyright 2001, 2018 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal, Régis Bouguin, Stephane Boireau
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

/**
 * Classe de helpers sur les types, motifs, justifications et actions des absences
 */
class AbsencesNotificationHelper {

	/**
	* Merge une notification avec son modele
	*
	* @param AbsenceEleveNotification $notification
	* @param String $modele chemin du modele tbs
	* @return clsTinyButStrong $TBS deroulante des types d'absences
	*/
	public static function MergeNotification($notification, $modele) {
		global $tableNotifications;

		$abs2_notifications_detaillees=getSettingAOui('abs2_notifications_detaillees');
		$debug_notifications=false;

		if((!isset($tableNotifications))||(!is_array($tableNotifications))) {
			$tableNotifications=array();
		}
		$indice = count($tableNotifications);
		//on charge le modele et on merge les données de l'établissement
		$TBS=self::MergeInfosEtab($modele);

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

		$lastEleves=array();
		//on récupère la liste des noms d'eleves
		$eleve_col = new PropelCollection();
		if ($notification->getAbsenceEleveTraitement() != null) {
			foreach ($notification->getAbsenceEleveTraitement()->getAbsenceEleveSaisies() as $saisie) {
				if (!in_array($saisie->getEleve()->getLogin(),$lastEleves)) {
					$lastEleves[] = $saisie->getEleve()->getLogin();
					$eleve_col->add($saisie->getEleve());
				}
			}
		}

		// Dans le cas d'une impression courrier, pour un élève en particulier, on récupère le tableNotifications de la boucle ci-dessous
		// Dans le cas d'une impression par lots ou envoi de mail, c'est l'autre boucle foreach($eleve_col as $eleve) (ligne 322)
		$cpt=0;
		foreach ($eleve_col as $eleve) {
			$tableNotifications[$indice]['notif_id'] = $notification->getId();
			$tableNotifications[$indice]['getNom'] = $eleve->getNom();
			$tableNotifications[$indice]['getPrenom'] = $eleve->getPrenom();
			$tableNotifications[$indice]['getClasseNomComplet'] = $eleve->getClasseNomComplet();
			$tableNotifications[$indice]['getId'] = $eleve->getId();
		
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
			$tableNotifications[$indice]['destinataire'] = $destinataire;

			$adr = $notification->getAdresse();
			if ($adr == null) {
				$adr = new Adresse();
			}
			$tableNotifications[$indice]['adr'] = $adr;

			$saisies_col = AbsenceEleveSaisieQuery::create()->filterByEleveId($eleve->getId())
					->useJTraitementSaisieEleveQuery()
					->filterByATraitementId($notification->getAbsenceEleveTraitement()->getId())->endUse()
					->orderBy("DebutAbs", Criteria::ASC)
							->find();
			foreach ($saisies_col as $saisie) {
				$str = $saisie->getDateDescription();
				if($saisie->getGroupeNameAvecClasses()!=''){
					$str.= ', cours de '.$saisie->getGroupeNameAvecClasses();
				}   
				$tableSaisie[] = $str;
			
			}
			$tableNotifications[$indice]['saisies_eleve'] = $tableSaisie;
			//print_r($tableNotifications[$indice]['saisies_eleve']);

			$demi_journee_string_col = new PropelCollection();array ();
				$abs_col = AbsenceEleveSaisieQuery::create()->filterByEleve($eleve)
				->useJTraitementSaisieEleveQuery()
				->filterByATraitementId($notification->getAbsenceEleveTraitement()->getId())->endUse()
				->orderBy("DebutAbs", Criteria::ASC)
				->find();
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

				/*
				echo "<pre>";
				print_r($demi_journee_string_col);
				echo "</pre>";
				*/

				$tableNotifications[$indice]['demi_j_string'] = $demi_journee_string_col;
			}
			else {
				$abs_col = AbsenceEleveSaisieQuery::create()->filterByEleve($eleve)
				->useJTraitementSaisieEleveQuery()
				->filterByATraitementId($notification->getAbsenceEleveTraitement()->getId())->endUse()
				->orderBy("DebutAbs", Criteria::ASC)
				->find();

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
					if($debug_notifications) {
						$tab_details[]="Saisies sélectionnées : ".$current_abs->getPrimaryKey()." du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U'));
					}
				}
				/*
				if($debug_notifications) {
					echo "tab_saisies_selectionnees:<pre>";
					print_r($tab_saisies_selectionnees);
					echo "</pre>";
				}
				*/

				foreach($abs_col as $current_abs) {
					// Il faut ne récupérer que la saisie englobante, pas les détails heure par heure.
					// Récupérer la saisie englobante si elle existe et tester si l'index est dans un tableau tab_englobante_ou_pas_mais_deja_affichee

					if($debug_notifications) {
						$tab_details[]="_______________________";
						$tab_details[]="Parcours de current_abs n°".$current_abs->getPrimaryKey()." (du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U')).")";
					}
					if(in_array($current_abs->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
						if($debug_notifications) {
							$tab_details[]="La saisie ".$current_abs->getPrimaryKey()." a déjà été considérée.";
						}
					}
					else {

						$a_selectionner=true;
						$saisies_englobante_col = $current_abs->getAbsenceEleveSaisiesEnglobantes();
						if (!$saisies_englobante_col->isEmpty()) {
							foreach ($saisies_englobante_col as $saisies_englobante) {
								if($debug_notifications) {
									$tab_details[]="On teste \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." (du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U')).")";
								}
								if(in_array($saisies_englobante->getPrimaryKey(), $tab_saisies_selectionnees)) {
									if($debug_notifications) {
										$tab_details[]="La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." fait partie de celles sélectionnées dans abs_col.";
									}
									// Il faut que la saisie englobante soit dans la liste des current_abs quand même
									if(in_array($saisies_englobante->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
										$a_selectionner=false;
										$details="Du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U'));
										if($debug_notifications) {
											$tab_details[]="RED non retenu ".$details;
										}
									}
									else {
										// Ajouter à la chaine les infos de $saisies_englobante_col
										if($debug_notifications) {
											$tab_details[]="\$current_abs->getPrimaryKey()=".$current_abs->getPrimaryKey()." est englobée par \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey();
											$tab_details[]="On va afficher la saisie englobante seulement.";
										}

										if(strftime("%d/%m/%Y", $saisies_englobante->getDebutAbs('U'))==strftime("%d/%m/%Y", $saisies_englobante->getFinAbs('U'))) {
											$details="Le ".strftime("%a %d/%m/%Y", $saisies_englobante->getDebutAbs('U'))." de ".strftime("%H:%M", $saisies_englobante->getDebutAbs('U'))." à ".strftime("%H:%M", $saisies_englobante->getFinAbs('U'));
										}
										else {
											$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));
										}
										$tab_details->append($details);

										if($debug_notifications) {
											$tab_details[]="BLUE retenu ".$details;
										}

										$tab_englobante_ou_pas_mais_deja_affichee[]=$saisies_englobante->getPrimaryKey();

										// Pour ne pas afficher la saisie englobée
										$a_selectionner=false;
									}
								}
								else {
									if($debug_notifications) {
										$tab_details[]="La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." ne fait pas partie des saisies choisies dans abs_col (on ne va pas l'afficher)";
									}

									// La saisie englobante n'est pas dans celles sélectionnées pour le traitement courant.
									// On ne la mentionne pas.
									// Mais il faudrait voir si il faut quand même mentionner $current_abs ou si la saisie est englobée par une autre acceptée.
									$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));

									if($debug_notifications) {
										$tab_details[]="PLUM non retenu ".$details;
									}

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

							if($debug_notifications) {
								$tab_details[]="GREEN retenu ".$details;
							}
							$tab_englobante_ou_pas_mais_deja_affichee[]=$current_abs->getPrimaryKey();
						}
					}
				}

				/*
				if($debug_notifications) {
					echo "tab_details retenus pour la notification:<pre>";
					print_r($tab_details);
					echo "</pre>";
				}
				*/

				//var_dump($demi_journee_string_col);die;
				//if (count($demi_journee_string_col) == 0) die;

				//$TBS->MergeBlock('demi_j_string_eleve_id_'.$eleve->getId(), $tab_details);
				$tableNotifications[$indice]['demi_j_string'] = $tab_details;

			}
			$indice++;
		}


		foreach ($eleve_col as $eleve) {
			$saisies_string_col = new PropelCollection();
			$saisies_col = AbsenceEleveSaisieQuery::create()->filterByEleveId($eleve->getId())
				->useJTraitementSaisieEleveQuery()
				->filterByATraitementId($notification->getAbsenceEleveTraitement()->getId())->endUse()
				->orderBy("DebutAbs", Criteria::ASC)
				->find();
			foreach ($saisies_col as $saisie) {

				$str = $saisie->getDateDescription();
				if($saisie->getGroupeNameAvecClasses()!=''){
					$str.= ', cours de '.$saisie->getGroupeNameAvecClasses();
				}
				$saisies_string_col->append($str);
			}
			$TBS->MergeBlock('saisies_string_eleve_id_'.$eleve->getId(), $saisies_string_col);
		}

		foreach($eleve_col as $eleve) {
			$demi_journee_string_col = new PropelCollection();array ();
			$abs_col = AbsenceEleveSaisieQuery::create()->filterByEleve($eleve)
			->useJTraitementSaisieEleveQuery()
			->filterByATraitementId($notification->getAbsenceEleveTraitement()->getId())->endUse()
			->orderBy("DebutAbs", Criteria::ASC)
			->find();

			//==============================
			// 20181221
			/*
			echo "<pre>";
			print_r($notification);
			echo "</pre><hr />";
			//die();
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

				//var_dump($demi_journee_string_col);die;
				//if (count($demi_journee_string_col) == 0) die;
				$TBS->MergeBlock('demi_j_string_eleve_id_'.$eleve->getId(), $demi_journee_string_col);
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
				if($debug_notifications) {
					echo "tab_saisies_selectionnees:<pre>";
					print_r($tab_saisies_selectionnees);
					echo "</pre>";
				}

				foreach($abs_col as $current_abs) {
					// Il faut ne récupérer que la saisie englobante, pas les détails heure par heure.
					// Récupérer la saisie englobante si elle existe et tester si l'index est dans un tableau tab_englobante_ou_pas_mais_deja_affichee

					if($debug_notifications) {echo "<br /><p>Parcours de current_abs n°".$current_abs->getPrimaryKey()."</p>";}
					if(in_array($current_abs->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
						if($debug_notifications) {echo "La saisie a déjà été considérée.<br />";}
					}
					else {

						$a_selectionner=true;
						$saisies_englobante_col = $current_abs->getAbsenceEleveSaisiesEnglobantes();
						if (!$saisies_englobante_col->isEmpty()) {
							foreach ($saisies_englobante_col as $saisies_englobante) {
								if($debug_notifications) {echo "On teste \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()."<br />";}
								if(in_array($saisies_englobante->getPrimaryKey(), $tab_saisies_selectionnees)) {
									if($debug_notifications) {echo "La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." fait partie de celles sélectionnées dans abs_col.<br />";}
									// Il faut que la saisie englobante soit dans la liste des current_abs quand même
									if(in_array($saisies_englobante->getPrimaryKey(), $tab_englobante_ou_pas_mais_deja_affichee)) {
										$a_selectionner=false;
										$details="Du ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $current_abs->getFinAbs('U'));
										if($debug_notifications) {echo "<p style='color:red'>".$details."</p>";}
									}
									else {
										// Ajouter à la chaine les infos de $saisies_englobante_col
										if($debug_notifications) {echo "\$current_abs->getPrimaryKey()=".$current_abs->getPrimaryKey()." est englobée par \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()."<br />On va afficher la saisie englobante seulement.<br />";}
										if(strftime("%d/%m/%Y", $saisies_englobante->getDebutAbs('U'))==strftime("%d/%m/%Y", $saisies_englobante->getFinAbs('U'))) {
											$details="Le ".strftime("%a %d/%m/%Y", $saisies_englobante->getDebutAbs('U'))." de ".strftime("%H:%M", $saisies_englobante->getDebutAbs('U'))." à ".strftime("%H:%M", $saisies_englobante->getFinAbs('U'));
										}
										else {
											$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));
										}
										$tab_details->append($details);

										if($debug_notifications) {echo "<p style='color:blue'>".$details."</p>";}

										$tab_englobante_ou_pas_mais_deja_affichee[]=$saisies_englobante->getPrimaryKey();

										// Pour ne pas afficher la saisie englobée
										$a_selectionner=false;
									}
								}
								else {
									if($debug_notifications) {echo "La saisie \$saisies_englobante->getPrimaryKey()=".$saisies_englobante->getPrimaryKey()." ne fait pas partie des saisies choisies dans abs_col (on ne va pas l'afficher)<br />";}

									// La saisie englobante n'est pas dans celles sélectionnées pour le traitement courant.
									// On ne la mentionne pas.
									// Mais il faudrait voir si il faut quand même mentionner $current_abs ou si la saisie est englobée par une autre acceptée.
									$details="Du ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getDebutAbs('U'))." au ".strftime("%a %d/%m/%Y %H:%M", $saisies_englobante->getFinAbs('U'));

									if($debug_notifications) {echo "<p style='color:plum'>".$details."</p>";}

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

							if($debug_notifications) {echo "<p style='color:green'>".$details."</p>";}
							$tab_englobante_ou_pas_mais_deja_affichee[]=$current_abs->getPrimaryKey();
						}
					}
				}

				/*
				echo "<pre>";
				print_r($demi_journee_string_col);
				echo "</pre>";
				*/
				if($debug_notifications) {
					echo "tab_details retenus pour la notification:<pre>";
					print_r($tab_details);
					echo "</pre>";
				}

				//var_dump($demi_journee_string_col);die;
				//if (count($demi_journee_string_col) == 0) die;

				$TBS->MergeBlock('demi_j_string_eleve_id_'.$eleve->getId(), $tab_details);
			}
		}

		if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_COURRIER) {
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
			$TBS->MergeField('destinataire',$destinataire);
			//$tableNotifications[$indice]['destinataire'] = $destinataire;

			$adr = $notification->getAdresse();
			if ($adr == null) {
			    $adr = new Adresse();
			}
			//$TBS->MergeField('adr',$adr);
			//$tableNotifications[$indice]['adr'] = $adr;

		} else if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL) {
			$destinataire = '';
			foreach ($notification->getResponsableEleves() as $responsable) {
				$destinataire .= $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.strtoupper($responsable->getPrenom()).' ';
			}
			$TBS->MergeField('destinataire',$destinataire);
		} else if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS) {
			$destinataire = '';
			foreach ($notification->getResponsableEleves() as $responsable) {
				$destinataire .= $responsable->getCivilite().' '.strtoupper($responsable->getNom()).' '.strtoupper($responsable->getPrenom()).' ';
			}
			$TBS->MergeField('destinataire',$destinataire);
		}

		// $TBS->Show(TBS_NOTHING);
		return $TBS;
	}

  /**
   * Charge le modele TBS et Merge les données établissement  *
   *
   * @param String $modele chemin du modele tbs   *
   */
  public static function MergeInfosEtab($modele){
        // load the TinyButStrong libraries
    include_once(dirname(__FILE__).'/../../tbs/tbs_class.php'); // TinyButStrong template engine    
    include_once(dirname(__FILE__).'/../../tbs/plugins/tbsdb_php.php');

    $TBS = new clsTinyButStrong; // new instance of TBS
    if (mb_substr($modele, -3) == "odt" ||mb_substr($modele, -3) == "ods") {
	include_once(dirname(__FILE__).'/../../tbs/plugins/tbs_plugin_opentbs.php');
	$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
    $TBS->LoadTemplate($modele, OPENTBS_ALREADY_UTF8);
    } else {
    $TBS->LoadTemplate($modele);
    }
    //merge des champs commun
    $TBS->MergeField('nom_etab',getSettingValue("gepiSchoolName"));
    $TBS->MergeField('tel_etab',getSettingValue("gepiSchoolTel"));
    $TBS->MergeField('fax_etab',getSettingValue("gepiSchoolFax"));
    $email_abs_etab = getSettingValue("gepiAbsenceEmail");
    if ($email_abs_etab == null || $email_abs_etab == '') {
	$email_abs_etab = getSettingValue("gepiSchoolEmail");
    }
    $TBS->MergeField('mail_etab', $email_abs_etab);
    $TBS->MergeField('annee_scolaire', getSettingValue("gepiYear"));
    $adr_etablissement = new Adresse();
	$adr_etablissement->setAdr1(getSettingValue("gepiSchoolAdress1"));
	$adr_etablissement->setAdr2(getSettingValue("gepiSchoolAdress2"));
	$adr_etablissement->setCp(getSettingValue("gepiSchoolZipCode"));
	$adr_etablissement->setCommune(getSettingValue("gepiSchoolCity"));
	$TBS->MergeField('adr_etab',$adr_etablissement);
    return($TBS);
    }


/**
   * Envoi une notification (email ou sms uniquement)
   *
   * @param AbsenceEleveNotification $notification
   * @param String $message le message texte a envoyer
   * @return String message d'erreur si envoi échoué
   */
  public static function EnvoiNotification($notification, $message){
    $return_message = '';
    if ($notification->getStatutEnvoi() != AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL && $notification->getStatutEnvoi() != AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL) {
	return 'Seule une notification de statut initial ou prete à envoyer peut être envoyée avec cette méthode';
    }
    if ($notification->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL &&
	    $notification->getTypeNotification() != AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS) {
	return 'Seule une notification de type email ou sms peut être envoyée avec cette méthode';
    } elseif ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_EMAIL) {
	if ($notification->getEmail() == null || $notification->getEmail() == '') {
	    $notification->setErreurMessageEnvoi('email non renseigné');
	    $notification->save();
	    return 'Echec de l\'envoi : email non renseigné.';
	}

	require_once('../lib/email_validator.php');
	if (!validEmail($notification->getEmail())) {
	    $notification->setErreurMessageEnvoi('adresse email non valide');
	    $notification->save();
	    return 'Erreur : adresse email non valide.';
	}

	$email_abs_etab = getSettingValue("gepiAbsenceEmail");
	if ($email_abs_etab == null || $email_abs_etab == '') {
	    $email_abs_etab = getSettingValue("gepiSchoolEmail");
	}

	/*
	$envoi = mail($notification->getEmail(),
		"Notification d'absence ".getSettingValue("gepiSchoolName").' - Ref : '.$notification->getId().' -',
		$message,
	       "From: ".$email_abs_etab."\r\n"
	       ."X-Mailer: PHP/" . phpversion());
	*/
	$envoi=envoi_mail("Notification d'absence ".getSettingValue("gepiSchoolName").' - Ref : '.$notification->getId().' -', 
				$message, 
				$notification->getEmail(), 
				"From: ".$email_abs_etab);

	$notification->setDateEnvoi('now');
	if ($envoi) {
	    $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES);
	    $return_message = '';
	} else {
	    $return_message = 'Non accepté pour livraison.';
	    $notification->setErreurMessageEnvoi($return_message);
	    $notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ECHEC);
	}
	$notification->save();
	return $return_message;

    } else if ($notification->getTypeNotification() == AbsenceEleveNotificationPeer::TYPE_NOTIFICATION_SMS) {
	if (!getSettingAOui('autorise_envoi_sms') || !getSettingAOui('abs2_sms')) {
	    return 'Erreur : envoi de sms désactivé.';
	}

	// Envoi sms
	$tab_to[]=$notification->getTelephone();
	$reponse = envoi_SMS($tab_to,$message);

	$notification->setDateEnvoi('now');

	//traitement de la réponse

	if ($reponse != 'OK') {
	$return_message = 'Erreur : message non envoyé. Code erreur : '.$reponse;
	$notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_ECHEC);
	$notification->setErreurMessageEnvoi($reponse);
	} else {
	$notification->setStatutEnvoi(AbsenceEleveNotificationPeer::STATUT_ENVOI_SUCCES);
	}
	// Fin envoi sms
	
	$notification->save();
	return $return_message;
    }
  }
}

// utilisé pour formater certain champs dans les modele tbs
function tbs_str($FieldName,&$CurrRec) {
    $CurrRec = html_entity_decode($CurrRec,ENT_QUOTES);
    $CurrRec = str_replace('\"','"',str_replace("\'","'",$CurrRec));
    $CurrRec = str_replace('\\'.htmlspecialchars('"',ENT_QUOTES),htmlspecialchars('"',ENT_QUOTES),str_replace("\\".htmlspecialchars("'",ENT_QUOTES),htmlspecialchars("'",ENT_QUOTES),$CurrRec));

    $CurrRec = stripslashes($CurrRec);
}

function tbs_toLower($FieldName,&$CurrRec) {
	$CurrRec = mb_strtolower($CurrRec);
}

// Fonction de comparaison
function TriSaisie(AbsenceEleveSaisie $a, AbsenceEleveSaisie $b) {
	if ($a->getDebutAbs('U') == $b->getDebutAbs('U')) {
		return 0;
	}
	return (($a->getFirst()->getDebutAbs('U') < $b->getFirst()->getDebutAbs('U')) ? -1 : 1);
}

?>
