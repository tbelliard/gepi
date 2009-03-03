<?php

require 'gepi/om/BaseAncienEtablissement.php';


/**
 * Skeleton subclass for representing a row from the 'etablissements' table.
 *
 * Liste des etablissements precedents des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AncienEtablissement extends BaseAncienEtablissement {

	/**
	 * Initializes internal state of AncienEtablissement object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AncienEtablissement
