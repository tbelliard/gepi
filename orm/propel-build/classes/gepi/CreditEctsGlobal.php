<?php

require 'gepi/om/BaseCreditEctsGlobal.php';


/**
 * Skeleton subclass for representing a row from the 'ects_global_credits' table.
 *
 * Objet qui précise la mention globale obtenue pour un eleve
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CreditEctsGlobal extends BaseCreditEctsGlobal {

	/**
	 * Initializes internal state of CreditEctsGlobal object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // CreditEctsGlobal
