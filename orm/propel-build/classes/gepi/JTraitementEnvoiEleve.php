<?php

require 'gepi/om/BaseJTraitementEnvoiEleve.php';


/**
 * Skeleton subclass for representing a row from the 'j_traitements_envois' table.
 *
 * Table de jointure entre le traitement des absences et leur envoi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JTraitementEnvoiEleve extends BaseJTraitementEnvoiEleve {

	/**
	 * Initializes internal state of JTraitementEnvoiEleve object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JTraitementEnvoiEleve
