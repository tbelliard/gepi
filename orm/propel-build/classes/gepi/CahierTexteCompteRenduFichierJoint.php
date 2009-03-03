<?php

require 'gepi/om/BaseCahierTexteCompteRenduFichierJoint.php';


/**
 * Skeleton subclass for representing a row from the 'ct_documents' table.
 *
 * Document (fichier joint) appartenant a un compte rendu du cahier de texte
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CahierTexteCompteRenduFichierJoint extends BaseCahierTexteCompteRenduFichierJoint {

	/**
	 * Initializes internal state of CahierTexteCompteRenduFichierJoint object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // CahierTexteCompteRenduFichierJoint
