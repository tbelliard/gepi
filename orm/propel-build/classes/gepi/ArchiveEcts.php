<?php

require 'gepi/om/BaseArchiveEcts.php';


/**
 * Skeleton subclass for representing a row from the 'archivage_ects' table.
 *
 * Enregistrement d'archive pour les credits ECTS, dont le rapport n'est edite qu'au depart de l'eleve
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class ArchiveEcts extends BaseArchiveEcts {

	/**
	 * Initializes internal state of ArchiveEcts object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // ArchiveEcts
