<?php


/**
 * Base class that represents a query for the 'ct_documents' table.
 *
 * Document (fichier joint) appartenant a un compte rendu du cahier de texte
 *
 * @method     CahierTexteCompteRenduFichierJointQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CahierTexteCompteRenduFichierJointQuery orderByIdCt($order = Criteria::ASC) Order by the id_ct column
 * @method     CahierTexteCompteRenduFichierJointQuery orderByTitre($order = Criteria::ASC) Order by the titre column
 * @method     CahierTexteCompteRenduFichierJointQuery orderByTaille($order = Criteria::ASC) Order by the taille column
 * @method     CahierTexteCompteRenduFichierJointQuery orderByEmplacement($order = Criteria::ASC) Order by the emplacement column
 *
 * @method     CahierTexteCompteRenduFichierJointQuery groupById() Group by the id column
 * @method     CahierTexteCompteRenduFichierJointQuery groupByIdCt() Group by the id_ct column
 * @method     CahierTexteCompteRenduFichierJointQuery groupByTitre() Group by the titre column
 * @method     CahierTexteCompteRenduFichierJointQuery groupByTaille() Group by the taille column
 * @method     CahierTexteCompteRenduFichierJointQuery groupByEmplacement() Group by the emplacement column
 *
 * @method     CahierTexteCompteRenduFichierJointQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteCompteRenduFichierJointQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteCompteRenduFichierJointQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteCompteRenduFichierJointQuery leftJoinCahierTexteCompteRendu($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteCompteRenduFichierJointQuery rightJoinCahierTexteCompteRendu($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteCompteRenduFichierJointQuery innerJoinCahierTexteCompteRendu($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteCompteRendu relation
 *
 * @method     CahierTexteCompteRenduFichierJoint findOne(PropelPDO $con = null) Return the first CahierTexteCompteRenduFichierJoint matching the query
 * @method     CahierTexteCompteRenduFichierJoint findOneById(int $id) Return the first CahierTexteCompteRenduFichierJoint filtered by the id column
 * @method     CahierTexteCompteRenduFichierJoint findOneByIdCt(int $id_ct) Return the first CahierTexteCompteRenduFichierJoint filtered by the id_ct column
 * @method     CahierTexteCompteRenduFichierJoint findOneByTitre(string $titre) Return the first CahierTexteCompteRenduFichierJoint filtered by the titre column
 * @method     CahierTexteCompteRenduFichierJoint findOneByTaille(int $taille) Return the first CahierTexteCompteRenduFichierJoint filtered by the taille column
 * @method     CahierTexteCompteRenduFichierJoint findOneByEmplacement(string $emplacement) Return the first CahierTexteCompteRenduFichierJoint filtered by the emplacement column
 *
 * @method     array findById(int $id) Return CahierTexteCompteRenduFichierJoint objects filtered by the id column
 * @method     array findByIdCt(int $id_ct) Return CahierTexteCompteRenduFichierJoint objects filtered by the id_ct column
 * @method     array findByTitre(string $titre) Return CahierTexteCompteRenduFichierJoint objects filtered by the titre column
 * @method     array findByTaille(int $taille) Return CahierTexteCompteRenduFichierJoint objects filtered by the taille column
 * @method     array findByEmplacement(string $emplacement) Return CahierTexteCompteRenduFichierJoint objects filtered by the emplacement column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteCompteRenduFichierJointQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCahierTexteCompteRenduFichierJointQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteCompteRenduFichierJoint', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteCompteRenduFichierJointQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteCompteRenduFichierJointQuery) {
			return $criteria;
		}
		$query = new CahierTexteCompteRenduFichierJointQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CahierTexteCompteRenduFichierJointPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			return $this
				->filterByPrimaryKey($key)
				->findOne($con);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the id_ct column
	 * 
	 * @param     int|array $idCt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByIdCt($idCt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idCt)) {
			if (array_values($idCt) === $idCt) {
				return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID_CT, $idCt, Criteria::IN);
			} else {
				if (isset($idCt['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID_CT, $idCt['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($idCt['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID_CT, $idCt['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID_CT, $idCt, $comparison);
		}
	}

	/**
	 * Filter the query on the titre column
	 * 
	 * @param     string $titre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByTitre($titre = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($titre)) {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TITRE, $titre, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $titre)) {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TITRE, str_replace('*', '%', $titre), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TITRE, $titre, $comparison);
		}
	}

	/**
	 * Filter the query on the taille column
	 * 
	 * @param     int|array $taille The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByTaille($taille = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($taille)) {
			if (array_values($taille) === $taille) {
				return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TAILLE, $taille, Criteria::IN);
			} else {
				if (isset($taille['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TAILLE, $taille['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($taille['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TAILLE, $taille['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::TAILLE, $taille, $comparison);
		}
	}

	/**
	 * Filter the query on the emplacement column
	 * 
	 * @param     string $emplacement The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByEmplacement($emplacement = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($emplacement)) {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT, $emplacement, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $emplacement)) {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT, str_replace('*', '%', $emplacement), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT, $emplacement, $comparison);
		}
	}

	/**
	 * Filter the query by a related CahierTexteCompteRendu object
	 *
	 * @param     CahierTexteCompteRendu $cahierTexteCompteRendu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteCompteRendu($cahierTexteCompteRendu, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID_CT, $cahierTexteCompteRendu->getIdCt(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteCompteRendu relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function joinCahierTexteCompteRendu($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteCompteRendu');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteCompteRendu');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteCompteRendu relation CahierTexteCompteRendu object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteCompteRenduQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCahierTexteCompteRendu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteCompteRendu', 'CahierTexteCompteRenduQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteCompteRenduFichierJoint $cahierTexteCompteRenduFichierJoint Object to remove from the list of results
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteCompteRenduFichierJoint = null)
	{
		if ($cahierTexteCompteRenduFichierJoint) {
			$this->addUsingAlias(CahierTexteCompteRenduFichierJointPeer::ID, $cahierTexteCompteRenduFichierJoint->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every SELECT statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreSelect(PropelPDO $con)
	{
		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		return $this->preDelete($con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreUpdate(&$values, PropelPDO $con)
	{
		return $this->preUpdate($values, $con);
	}

} // BaseCahierTexteCompteRenduFichierJointQuery
