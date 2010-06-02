<?php



/**
 * Skeleton subclass for representing a row from the 'ct_devoirs_entry' table.
 *
 * Travail Ã  faire (devoir) cahier de texte
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class CahierTexteTravailAFaire extends BaseCahierTexteTravailAFaire {

	//optimisation de vitess pour recuperer les fichiers joints dans le cas ou le critere est null

	/**
	 * @var        array CahierTexteTravailAFaireFichierJoint[] Collection to store aggregation of CahierTexteTravailAFaireFichierJoint objects.
	 */
	protected $collCahierTexteTravailAFaireFichierJoints;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtDocuments.
	 */
	private $lastCahierTexteTravailAFaireFichierJointCriteria = null;
		
	/**
	 * Gets an array of CahierTexteTravailAFaireFichierJoint objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Manually added : improved performance if criteria and lastCtDocumentCriteria are both null
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteTravailAFaireFichierJoint[]
	 * @throws     PropelException
	 */
	public function getCahierTexteTravailAFaireFichierJoints($criteria = null, PropelPDO $con = null)
	{
		if ($criteria != null || $this->lastCahierTexteTravailAFaireFichierJointCriteria != null || $this->collCahierTexteTravailAFaireFichierJoints === null) {
			$this->collCahierTexteTravailAFaireFichierJoints = parent::getCahierTexteTravailAFaireFichierJoints($criteria, $con);
		}
		$this->lastCahierTexteTravailAFaireFichierJointCriteria = $criteria;
		return $this->collCahierTexteTravailAFaireFichierJoints;
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
	public function clearAllReferences($deep = false)
	{
		parent::clearAllReferences($deep);
		$this->collCahierTexteTravailAFaireFichierJoints = null;
	}

	/**
	 * Clears out the collCahierTexteTravailAFaireFichierJoints collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearCahierTexteTravailAFaireFichierJoints()
	{
		$this->collCahierTexteTravailAFaireFichierJoints = null; // important to set this to NULL since that means it is uninitialized
	}

} // CahierTexteTravailAFaire
