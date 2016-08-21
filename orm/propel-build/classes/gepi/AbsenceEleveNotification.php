<?php



/**
 * Skeleton subclass for representing a row from the 'a_notifications' table.
 *
 * Notification (a la famille) des absences
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveNotification extends BaseAbsenceEleveNotification {

    /**
     *
     * Renvoi true / false suivant que la notification est modifiable ou pas
     *
     * @return     String description
     *
     */
    public function getModifiable() {
	//modifiable uniquement si le statut est initial
	return $this->getStatutEnvoi() == AbsenceEleveNotificationPeer::STATUT_ENVOI_ETAT_INITIAL;
    }

    /**
     *
     * Renvoi une description intelligible de la notification
     *
     * @return     String description
     *
     */
    public function getDescription() {
	$desc = '';
	if ($this->getTypeNotification() != '') {
	    $desc .= 'type '.$this->getTypeNotification().'; ';
	}
	if ($this->getStatutEnvoi() != '') {
	    $desc .= 'statut : '.$this->getStatutEnvoi().'; ';
	}
	if ($this->getDateEnvoi() != null) {
	    $desc .= strftime('%a %d/%m/%Y %H:%M', $this->getDateEnvoi('U'));
	}
	return $desc;
    }

    /**
     *
     * Prérempli la notification avec des responsables (sans sauvegarder la notification).
     * Si plusieurs responsables sont disponibles, un responsable 1 est pris en priorité pour remplir la notification,
     * un responsable 2 est ajouté si l'adresse est la même que le premier
     * Si trop de responsables sont disponibles, aucun choix arbitraire n'est fait et alors rien n'est rempli sur la notification
     * Si aucun responsable n'est disponible, la notification n'est pas remplie
     *
     * @return     boolean true ou false suivant que le remplissage a pu être effectué ou pas.
     *
     */
    public function preremplirResponsables() {
            $traitement = $this->getAbsenceEleveTraitement();
            if ($traitement === NULL) return false;

            $responsable_1_coll = new PropelObjectCollection();
            $responsable_2_coll = new PropelObjectCollection();
            foreach ($traitement->getResponsablesInformationsSaisies() as $responsable_information) {
                    if ($responsable_information == null) continue;
                    if ($responsable_information->getNiveauResponsabilite() == '1') {
                        $responsable_1_coll->add($responsable_information->getResponsableEleve());
                    } else if ($responsable_information->getNiveauResponsabilite() == '2') {
                        $responsable_2_coll->add($responsable_information->getResponsableEleve());
                    }
                    //si on ne peut pas choisir les responsables, on retourne sans remplir
                    if ($responsable_1_coll->count() > 1) return false;
            }

            if ($responsable_1_coll->isEmpty() && $responsable_2_coll->count() != 1) {
                //on ne peut pas choisir
                return false;
            }

            $responsable_eleve1 = $responsable_1_coll->getFirst();
            $responsable_eleve2 = $responsable_2_coll->getFirst();
            if ($responsable_eleve1 != null) {
                    $this->setEmail($responsable_eleve1->getMel());
                    $this->setTelephone($responsable_eleve1->getTelPort());
                    $this->setAdresseId($responsable_eleve1->getAdresseId());
                    $this->addResponsableEleve($responsable_eleve1);
            } else {
                    $this->setEmail($responsable_eleve2->getMel());
                    $this->setTelephone($responsable_eleve2->getTelPort());
                    $this->setAdresseId($responsable_eleve2->getAdresseId());
                    $this->addResponsableEleve($responsable_eleve2);
            }

            //on ajoute dans la liste des destinataires le resp 2 si il a la même adresse que le resp 1
            if ($responsable_eleve2 != null && $responsable_eleve1 != null && $responsable_eleve2->getAdresseId() == $responsable_eleve1->getAdresseId()) {
                    $this->addResponsableEleve($responsable_eleve2);
            }

            return true;
    }
    
} // AbsenceEleveNotification
