<?php

require 'gepi/om/BaseAbsenceTraitement.php';


/**
 * Skeleton subclass for representing a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceTraitement extends BaseAbsenceTraitement {

	/**
	 * Initializes internal state of AbsenceTraitement object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
		$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$this->setCreatedOn($currentDate);
	}

	/**
	 * Persists this object to the database.
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
	public function save(PropelPDO $con = null) {
		$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
		$this->setUpdatedOn($currentDate);
		if ($this->isNew()) {
			$this->setCreatedOn($currentDate);
		}
		parent::save($con);
	}

} // AbsenceTraitement
