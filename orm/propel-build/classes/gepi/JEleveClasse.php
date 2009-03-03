<?php

require 'gepi/om/BaseJEleveClasse.php';


/**
 * Skeleton subclass for representing a row from the 'j_eleves_classes' table.
 *
 * Table de jointure entre les eleves et leur classe en fonction de la periode
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JEleveClasse extends BaseJEleveClasse {

	/**
	 * Initializes internal state of JEleveClasse object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JEleveClasse
