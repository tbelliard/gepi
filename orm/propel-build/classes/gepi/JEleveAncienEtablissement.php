<?php

require 'gepi/om/BaseJEleveAncienEtablissement.php';


/**
 * Skeleton subclass for representing a row from the 'j_eleves_etablissements' table.
 *
 * Table de jointure pour connaitre l'etablissement precedent de l'eleve
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JEleveAncienEtablissement extends BaseJEleveAncienEtablissement {

	/**
	 * Initializes internal state of JEleveAncienEtablissement object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JEleveAncienEtablissement
