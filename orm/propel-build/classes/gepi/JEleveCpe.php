<?php

require 'gepi/om/BaseJEleveCpe.php';


/**
 * Skeleton subclass for representing a row from the 'j_eleves_cpe' table.
 *
 * Table de jointure entre les CPE et les eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JEleveCpe extends BaseJEleveCpe {

	/**
	 * Initializes internal state of JEleveCpe object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JEleveCpe
