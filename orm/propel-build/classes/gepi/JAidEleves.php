<?php

require 'gepi/om/BaseJAidEleves.php';


/**
 * Skeleton subclass for representing a row from the 'j_aid_eleves' table.
 *
 * Table de liaison entre les AID et les eleves qui en sont membres
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JAidEleves extends BaseJAidEleves {

	/**
	 * Initializes internal state of JAidEleves object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JAidEleves
