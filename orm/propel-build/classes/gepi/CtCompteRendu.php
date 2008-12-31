<?php

require 'gepi/om/BaseCtCompteRendu.php';


/**
 * Skeleton subclass for representing a row from the 'ct_entry' table.
 *
 * Compte rendu cahier de texte
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CtCompteRendu extends BaseCtCompteRendu {

	/**
	 * @var        array CtDocument[] Collection to store aggregation of CtDocument objects.
	 */
	protected $collCtDocuments;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtDocuments.
	 */
	private $lastCtDocumentCriteria = null;
		
	/**
	 * Gets an array of CtDocument objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Manually added : improved performance if criteria and lastCtDocumentCriteria are both null
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtDocument[]
	 * @throws     PropelException
	 */
	public function getCtDevoirDocuments($criteria = null, PropelPDO $con = null)
	{
		if ($criteria != null || $this->lastCtDocumentCriteria != null || $this->collCtDocuments === null) {
			$this->collCtDocuments = parent::getCtDocuments($criteria, $con);
		}
		$this->lastCtDocumentCriteria = $criteria;
		return $this->collCtDocuments;
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
		$this->collCtDocuments = null;
	}

	/**
	 * Clears out the collCtDocuments collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtDocuments()
	 */
	public function clearCtDocuments()
	{
		$this->collCtDocuments = null; // important to set this to NULL since that means it is uninitialized
	}
	
	/**
	 * Initializes internal state of CtCompteRendu object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // CtCompteRendu
