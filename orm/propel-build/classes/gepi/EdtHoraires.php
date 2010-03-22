<?php

require 'gepi/om/BaseEdtHoraires.php';


/**
 * Skeleton subclass for representing a row from the 'horaires_etablissement' table.
 *
 * Table contenant les heures d'ouverture et de fermeture de l'etablissement par journee
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class EdtHoraires extends BaseEdtHoraires {

	/**
	 * Initializes internal state of EdtHoraires object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // EdtHoraires
