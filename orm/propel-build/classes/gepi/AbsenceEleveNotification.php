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
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null) { 
	    
		if ($this->getAbsenceEleveTraitement() != null) {
			//$this->getAbsenceEleveTraitement()->updateAgregationTable();
		}
	}
	
	/**
	 * Code to be run after deleting the object in database
	 * @param PropelPDO $con
	 */
	public function postDelete(PropelPDO $con = null) {
		if ($this->getAbsenceEleveTraitement() != null) {
			//$this->getAbsenceEleveTraitement()->updateAgregationTable();
		}
	}
    
} // AbsenceEleveNotification
