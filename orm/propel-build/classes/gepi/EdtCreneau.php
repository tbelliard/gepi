<?php

require 'gepi/om/BaseEdtCreneau.php';


/**
 * Skeleton subclass for representing a row from the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class EdtCreneau extends BaseEdtCreneau {

	/**
	 * Initializes internal state of EdtCreneau object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // EdtCreneau
