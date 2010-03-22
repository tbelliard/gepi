<?php

require 'gepi/om/BaseAbsenceEleveTypeStatut.php';


/**
 * Skeleton subclass for representing a row from the 'a_types_statut' table.
 *
 * Liste des statuts etant autorisé à saisir des types d'absences donnes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceEleveTypeStatut extends BaseAbsenceEleveTypeStatut {

	/**
	 * Initializes internal state of AbsenceEleveTypeStatut object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // AbsenceEleveTypeStatut
