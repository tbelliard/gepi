<?php

require 'gepi/om/BaseAbsenceAbsence.php';


/**
 * Skeleton subclass for representing a row from the 'a_absences' table.
 *
 * Une absence est la compilation des saisies pour un meme eleve, cette compilation est faite automatiquement par Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceAbsence extends BaseAbsenceAbsence {

	/**
	 * Initializes internal state of AbsenceAbsence object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AbsenceAbsence
