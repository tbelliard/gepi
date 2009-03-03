<?php

require 'gepi/om/BaseResponsableInformation.php';


/**
 * Skeleton subclass for representing a row from the 'responsables2' table.
 *
 * Table de jointure entre les eleves et leurs responsables legaux avec mention du niveau de ces responsables
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class ResponsableInformation extends BaseResponsableInformation {

	/**
	 * Initializes internal state of ResponsableInformation object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // ResponsableInformation
