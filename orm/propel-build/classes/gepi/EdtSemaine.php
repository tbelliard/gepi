<?php

require 'gepi/om/BaseEdtSemaine.php';


/**
 * Skeleton subclass for representing a row from the 'edt_semaines' table.
 *
 * Liste des semaines de l'annee scolaire courante - 53 enregistrements obligatoires (pas 52!)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class EdtSemaine extends BaseEdtSemaine {

	/**
	 * Initializes internal state of EdtSemaine object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // EdtSemaine
