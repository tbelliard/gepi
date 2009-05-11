<?php

require 'gepi/om/BaseCategorieMatiere.php';


/**
 * Skeleton subclass for representing a row from the 'matieres_categories' table.
 *
 * Categories de matiere, utilisees pour regrouper des enseignements
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CategorieMatiere extends BaseCategorieMatiere {

	/**
	 * Initializes internal state of CategorieMatiere object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // CategorieMatiere
