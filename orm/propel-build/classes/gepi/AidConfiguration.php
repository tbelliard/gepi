<?php

require 'gepi/om/BaseAidConfiguration.php';


/**
 * Skeleton subclass for representing a row from the 'aid_config' table.
 *
 * Liste des categories d'AID (Activites inter-Disciplinaires)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AidConfiguration extends BaseAidConfiguration {

	/**
	 * Initializes internal state of AidConfiguration object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AidConfiguration
