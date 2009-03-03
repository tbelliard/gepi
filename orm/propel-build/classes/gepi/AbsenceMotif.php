<?php

require 'gepi/om/BaseAbsenceMotif.php';


/**
 * Skeleton subclass for representing a row from the 'a_motifs' table.
 *
 * Liste des motifs possibles pour une absence
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceMotif extends BaseAbsenceMotif {

	/**
	 * Initializes internal state of AbsenceMotif object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AbsenceMotif
