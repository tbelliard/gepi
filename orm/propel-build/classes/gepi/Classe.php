<?php

require 'gepi/om/BaseClasse.php';


/**
 * Skeleton subclass for representing a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Classe extends BaseClasse {

	/**
	 * Initializes internal state of Classe object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	/**
	 * Renvoi sous forme d'un tableau la liste des groupes d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Classes[]
	 */
	public function getGroupes($con = null) {
		$groupes = array();
		foreach($this->getJGroupesClassessJoinGroupe($con) as $ref) {
			$groupes[] = $ref->getGroupe();
		}
		return $groupes;
	}

} // Classe
