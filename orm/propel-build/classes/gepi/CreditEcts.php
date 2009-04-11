<?php

require 'gepi/om/BaseCreditEcts.php';


/**
 * Skeleton subclass for representing a row from the 'ects_credits' table.
 *
 * Association élève/période/enseignement qui précise le nombre d'ECTS obtenus par l'élève
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CreditEcts extends BaseCreditEcts {

	/**
	 * Initializes internal state of CreditEcts object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // CreditEcts
