<?php

require 'gepi/om/BaseAbsenceJustification.php';


/**
 * Skeleton subclass for representing a row from the 'a_justifications' table.
 *
 * Liste des justifications possibles pour une absence
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceJustification extends BaseAbsenceJustification {

	/**
	 * Initializes internal state of AbsenceJustification object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AbsenceJustification
