<?php

require 'gepi/om/BaseResponsableEleveAdresse.php';


/**
 * Skeleton subclass for representing a row from the 'resp_adr' table.
 *
 * Table de jointure entre les responsables legaux et leur adresse
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class ResponsableEleveAdresse extends BaseResponsableEleveAdresse {

	/**
	 * Initializes internal state of ResponsableEleveAdresse object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // ResponsableEleveAdresse
