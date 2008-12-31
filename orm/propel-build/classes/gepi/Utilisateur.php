<?php

require 'gepi/om/BaseUtilisateur.php';


/**
 * Skeleton subclass for representing a row from the 'utilisateurs' table.
 *
 * Table utilisateurs
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Utilisateur extends BaseUtilisateur {

	/**
	 * Initializes internal state of Utilisateur object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

		/**
	 * Manually added for N:M relationship
	 *
	 */
	public function getGroupes($c = null) {
		$groupes = array();
		foreach($this->getJGroupesProfesseurssJoinGroupe($c) as $ref) {
			$groupes[] = $ref->getGroupe();
		}
		return $groupes;
	}

} // Utilisateur
