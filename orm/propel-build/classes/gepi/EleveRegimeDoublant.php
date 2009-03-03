<?php

require 'gepi/om/BaseEleveRegimeDoublant.php';


/**
 * Skeleton subclass for representing a row from the 'j_eleves_regime' table.
 *
 * Mention du redoublement eventuel de l'eleve ainsi que son regime de presence (externe, demi-pensionnaire, ...)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class EleveRegimeDoublant extends BaseEleveRegimeDoublant {

	/**
	 * Initializes internal state of EleveRegimeDoublant object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // EleveRegimeDoublant
