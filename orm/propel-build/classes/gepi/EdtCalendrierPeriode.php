<?php

require 'gepi/om/BaseEdtCalendrierPeriode.php';


/**
 * Skeleton subclass for representing a row from the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class EdtCalendrierPeriode extends BaseEdtCalendrierPeriode {

	/**
	 * Initializes internal state of EdtCalendrierPeriode object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // EdtCalendrierPeriode
