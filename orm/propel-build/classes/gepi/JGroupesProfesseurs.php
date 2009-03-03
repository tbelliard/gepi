<?php

require 'gepi/om/BaseJGroupesProfesseurs.php';


/**
 * Skeleton subclass for representing a row from the 'j_groupes_professeurs' table.
 *
 * Table permettant le jointure entre groupe d'eleves et professeurs. Est rarement utilise directement dans le code.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JGroupesProfesseurs extends BaseJGroupesProfesseurs {

	/**
	 * Initializes internal state of JGroupesProfesseurs object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JGroupesProfesseurs
