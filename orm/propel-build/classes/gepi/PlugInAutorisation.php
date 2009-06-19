<?php

require 'gepi/om/BasePlugInAutorisation.php';


/**
 * Skeleton subclass for representing a row from the 'plugins_autorisations' table.
 *
 * Liste des autorisations pour chaque statut
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class PlugInAutorisation extends BasePlugInAutorisation {

	/**
	 * Initializes internal state of PlugInAutorisation object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // PlugInAutorisation
