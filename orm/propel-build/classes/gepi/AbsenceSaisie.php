<?php

require 'gepi/om/BaseAbsenceSaisie.php';


/**
 * Skeleton subclass for representing a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceSaisie extends BaseAbsenceSaisie {

	/**
	 * Initializes internal state of AbsenceSaisie object.
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
	 *
	 * Renvoi sous forme d'un tableau la liste des traitements associés a une absence
	 * Manually added for N:M relationship
	 *
	 * @return     array AbsenceTraitements[]
	 *
	 */
	public function getAbsenceTraitements() {
		$absenceTraitements = array();
		$criteria = new Criteria();
		foreach($this->getJTraitementSaisieAbsencesJoinAbsenceTraitement() as $ref) {
			$absenceTraitements[] = $ref->getAbsenceTraitement();
		}
		return $absenceTraitements;
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
		$currentDate = date("U"); //mktime(0,0,0,date("m"),date("d"),date("Y"));
		$this->setUpdatedOn($currentDate);
		if ($this->isNew()) {
			$this->setCreatedOn($currentDate);
		}
		return parent::save($con);
	}

	/**
	 *
	 * Ajoute un traitement a une saisie d'absence
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      AbsenceTraitement $absenceTraitement Le traitement a ajoute
	 * @return     array Eleves[]
	 */
	public function addAbsenceTraitement(AbsenceTraitement $absenceTraitement) {
		if ($absenceTraitement->isNew()) {
			$absenceTraitement->save();
		}
		$jTraitementSaisieAbsence = new JTraitementSaisieAbsence();
		$jTraitementSaisieAbsence->setAbsenceTraitement($absenceTraitement);
		$this->addJTraitementSaisieAbsence($jTraitementSaisieAbsence);
		$jTraitementSaisieAbsence->save();
	}

} // AbsenceSaisie
