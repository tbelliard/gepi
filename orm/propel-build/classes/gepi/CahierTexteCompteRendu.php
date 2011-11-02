<?php



/**
 * Skeleton subclass for representing a row from the 'ct_entry' table.
 *
 * Compte rendu du cahier de texte
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class CahierTexteCompteRendu extends BaseCahierTexteCompteRendu {

	//optimisation de vitesse dans les cas ou les criteres sont null pour recuprer les fichiers joint
	/**
	 * @var        array CahierTexteCompteRenduFichierJoint[] Collection to store aggregation of CahierTexteCompteRenduFichierJoint objects.
	 */
	protected $collCahierTexteCompteRenduFichierJoints;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteCompteRenduFichierJoints.
	 */
	private $lastCahierTexteCompteRenduFichierJointCriteria = null;
		
	/**
	 * Gets an array of CahierTexteCompteRenduFichierJoint objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Manually added : improved performance if criteria and lastCtDocumentCriteria are both null
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteCompteRenduFichierJoint[]
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRenduFichierJoints($criteria = null, PropelPDO $con = null)
	{
		if ($criteria != null || $this->lastCahierTexteCompteRenduFichierJointCriteria != null || $this->collCahierTexteCompteRenduFichierJoints === null) {
			$this->collCahierTexteCompteRenduFichierJoints = parent::getCahierTexteCompteRenduFichierJoints($criteria, $con);
		}
		$this->lastCahierTexteCompteRenduFichierJointCriteria = $criteria;
		return $this->collCahierTexteCompteRenduFichierJoints;
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
		$this->collCahierTexteCompteRenduFichierJoints = null;
	}

	/**
	 * Clears out the collCahierTexteCompteRenduFichierJoints collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearCahierTexteCompteRenduFichierJoints()
	{
		$this->collCahierTexteCompteRenduFichierJoints = null; // important to set this to NULL since that means it is uninitialized
	}
	
} // CahierTexteCompteRendu
