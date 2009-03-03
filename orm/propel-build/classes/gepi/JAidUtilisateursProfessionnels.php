<?php

require 'gepi/om/BaseJAidUtilisateursProfessionnels.php';


/**
 * Skeleton subclass for representing a row from the 'j_aid_utilisateurs' table.
 *
 * Table de liaison entre les AID et les utilisateurs professionnels
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class JAidUtilisateursProfessionnels extends BaseJAidUtilisateursProfessionnels {

	/**
	 * Initializes internal state of JAidUtilisateursProfessionnels object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // JAidUtilisateursProfessionnels
