<?php

require 'gepi/om/BaseJEleveProfesseurPrincipal.php';


/**
 * Skeleton subclass for representing a row from the 'j_eleves_professeurs' table.
 *
 * Table de jointure entre les professeurs principaux et les eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JEleveProfesseurPrincipal extends BaseJEleveProfesseurPrincipal {

	/**
	 * Initializes internal state of JEleveProfesseurPrincipal object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JEleveProfesseurPrincipal
