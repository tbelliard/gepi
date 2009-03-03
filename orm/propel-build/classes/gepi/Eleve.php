<?php

require 'gepi/om/BaseEleve.php';


/**
 * Skeleton subclass for representing a row from the 'eleves' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Eleve extends BaseEleve {

	/**
	 * Initializes internal state of Eleve object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Classes[]
	 *
	 */
	public function getClasses($periode) {
		$classes = array();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode);
		foreach($this->getJEleveClassesJoinClasse($criteria) as $ref) {
			$classes[] = $ref->getClasse();
		}
		return $classes;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Groupes[]
	 *
	 */
	public function getGroupes($periode) {
		$groupes = array();
		$criteria = new Criteria();
		$criteria->add(JEleveGroupePeer::PERIODE,$periode);
		foreach($this->getJEleveGroupesJoinGroupe($criteria) as $ref) {
			$groupes[] = $ref->getGroupe();
		}
		return $groupes;
	}

} // Eleve
