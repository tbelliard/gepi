<?php

require 'gepi/om/BaseGroupe.php';


/**
 * Skeleton subclass for representing a row from the 'groupe' table.
 *
 * table des groupes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Groupe extends BaseGroupe {

	/**
	 * The value for DescriptionAvecClasses
	 * 	 * @var        string
	 */
	protected static $descriptionAvecClasses;

	/**
	 * The value for NameAvecClasses
	 * 	 * @var        string
	 */
	protected static $nameAvecClasses;

	/**
	 * Initializes internal state of Groupe object.
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
	public function getClasses($c = null) {
		$classes = array();
		foreach($this->getJGroupesClassessJoinClasse($c) as $ref) {
			$classes[] = $ref->getClasse();
		}
		return $classes;
	}

	/**
	 * Renvoi la description du groupe avec la liste des classes associées
	 *
	 */
	public function getDescriptionAvecClasses() {
		if ($descriptionAvecClasses != null) {
			return $descriptionAvecClasses;
		} else {
			$str = $this->getDescription();
			$str .= "&nbsp;(";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getClasse() . ",&nbsp;";
			}
			$str = substr($str, 0, -7);
			$str.= ")";
			$descriptionAvecClasses = $str;
			return $str;
		}
	}

	/**
	 * Renvoi le nom du groupe avec la liste des classes associées
	 *
	 */
	public function getNameAvecClasses() {
		if ($nameAvecClasses != null) {
			return $nameAvecClasses;
		} else {
			$str = $this->getName();
			$str .= "&nbsp;-&nbsp;(";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getClasse() . ",&nbsp;";
			}
			$str = substr($str, 0, -7);
			$str.= ")";
			$nameAvecClasses = $str;
			return $str;
		}
	}

	/**
	 * Clears out the collJGroupesClassess collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		parent::clearJGroupesClassess();
		$descriptionAvecClasses = null;
		$nameAvecClasses = null;
	}

		/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false) {
		parent::clearAllReferences($deep);
		$this->clearJGroupesClassess();
	}

} // Groupe