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
	 * Ajout manuel : mise a jour de la table d'agrégation des saisies
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
	    $result = parent::save($con);
	    
	    
	    return $result;
	}
	
	/**
	 * Removes this object from datastore and sets delete attribute. Custom : suppression des notifications et jointures associées et calcul de la table d'agrégation
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		$oldTraitement = $this->getAbsenceEleveTraitement();
		
		parent::delete();
	}
    
} // AbsenceEleveNotification
