<?php


/**
 * Base class that represents a query for the 'ct_devoirs_documents' table.
 *
 * Document (fichier joint) appartenant a un travail Ã  faire du cahier de texte
 *
 * @method     CahierTexteTravailAFaireFichierJointQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CahierTexteTravailAFaireFichierJointQuery orderByIdCtDevoir($order = Criteria::ASC) Order by the id_ct_devoir column
 * @method     CahierTexteTravailAFaireFichierJointQuery orderByTitre($order = Criteria::ASC) Order by the titre column
 * @method     CahierTexteTravailAFaireFichierJointQuery orderByTaille($order = Criteria::ASC) Order by the taille column
 * @method     CahierTexteTravailAFaireFichierJointQuery orderByEmplacement($order = Criteria::ASC) Order by the emplacement column
 *
 * @method     CahierTexteTravailAFaireFichierJointQuery groupById() Group by the id column
 * @method     CahierTexteTravailAFaireFichierJointQuery groupByIdCtDevoir() Group by the id_ct_devoir column
 * @method     CahierTexteTravailAFaireFichierJointQuery groupByTitre() Group by the titre column
 * @method     CahierTexteTravailAFaireFichierJointQuery groupByTaille() Group by the taille column
 * @method     CahierTexteTravailAFaireFichierJointQuery groupByEmplacement() Group by the emplacement column
 *
 * @method     CahierTexteTravailAFaireFichierJointQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteTravailAFaireFichierJointQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteTravailAFaireFichierJointQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteTravailAFaireFichierJointQuery leftJoinCahierTexteTravailAFaire($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteTravailAFaireFichierJointQuery rightJoinCahierTexteTravailAFaire($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteTravailAFaireFichierJointQuery innerJoinCahierTexteTravailAFaire($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteTravailAFaire relation
 *
 * @method     CahierTexteTravailAFaireFichierJoint findOne(PropelPDO $con = null) Return the first CahierTexteTravailAFaireFichierJoint matching the query
 * @method     CahierTexteTravailAFaireFichierJoint findOneOrCreate(PropelPDO $con = null) Return the first CahierTexteTravailAFaireFichierJoint matching the query, or a new CahierTexteTravailAFaireFichierJoint object populated from the query conditions when no match is found
 *
 * @method     CahierTexteTravailAFaireFichierJoint findOneById(int $id) Return the first CahierTexteTravailAFaireFichierJoint filtered by the id column
 * @method     CahierTexteTravailAFaireFichierJoint findOneByIdCtDevoir(int $id_ct_devoir) Return the first CahierTexteTravailAFaireFichierJoint filtered by the id_ct_devoir column
 * @method     CahierTexteTravailAFaireFichierJoint findOneByTitre(string $titre) Return the first CahierTexteTravailAFaireFichierJoint filtered by the titre column
 * @method     CahierTexteTravailAFaireFichierJoint findOneByTaille(int $taille) Return the first CahierTexteTravailAFaireFichierJoint filtered by the taille column
 * @method     CahierTexteTravailAFaireFichierJoint findOneByEmplacement(string $emplacement) Return the first CahierTexteTravailAFaireFichierJoint filtered by the emplacement column
 *
 * @method     array findById(int $id) Return CahierTexteTravailAFaireFichierJoint objects filtered by the id column
 * @method     array findByIdCtDevoir(int $id_ct_devoir) Return CahierTexteTravailAFaireFichierJoint objects filtered by the id_ct_devoir column
 * @method     array findByTitre(string $titre) Return CahierTexteTravailAFaireFichierJoint objects filtered by the titre column
 * @method     array findByTaille(int $taille) Return CahierTexteTravailAFaireFichierJoint objects filtered by the taille column
 * @method     array findByEmplacement(string $emplacement) Return CahierTexteTravailAFaireFichierJoint objects filtered by the emplacement column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteTravailAFaireFichierJointQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCahierTexteTravailAFaireFichierJointQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteTravailAFaireFichierJoint', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteTravailAFaireFichierJointQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteTravailAFaireFichierJointQuery) {
			return $criteria;
		}
		$query = new CahierTexteTravailAFaireFichierJointQuery();
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
	 * @return    CahierTexteTravailAFaireFichierJoint|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CahierTexteTravailAFaireFichierJointPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
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
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the id_ct_devoir column
	 * 
	 * @param     int|array $idCtDevoir The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByIdCtDevoir($idCtDevoir = null, $comparison = null)
	{
		if (is_array($idCtDevoir)) {
			$useMinMax = false;
			if (isset($idCtDevoir['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID_CT_DEVOIR, $idCtDevoir['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idCtDevoir['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID_CT_DEVOIR, $idCtDevoir['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID_CT_DEVOIR, $idCtDevoir, $comparison);
	}

	/**
	 * Filter the query on the titre column
	 * 
	 * @param     string $titre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByTitre($titre = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($titre)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $titre)) {
				$titre = str_replace('*', '%', $titre);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::TITRE, $titre, $comparison);
	}

	/**
	 * Filter the query on the taille column
	 * 
	 * @param     int|array $taille The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByTaille($taille = null, $comparison = null)
	{
		if (is_array($taille)) {
			$useMinMax = false;
			if (isset($taille['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::TAILLE, $taille['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($taille['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::TAILLE, $taille['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::TAILLE, $taille, $comparison);
	}

	/**
	 * Filter the query on the emplacement column
	 * 
	 * @param     string $emplacement The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByEmplacement($emplacement = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($emplacement)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $emplacement)) {
				$emplacement = str_replace('*', '%', $emplacement);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::EMPLACEMENT, $emplacement, $comparison);
	}

	/**
	 * Filter the query by a related CahierTexteTravailAFaire object
	 *
	 * @param     CahierTexteTravailAFaire $cahierTexteTravailAFaire  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteTravailAFaire($cahierTexteTravailAFaire, $comparison = null)
	{
		return $this
			->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID_CT_DEVOIR, $cahierTexteTravailAFaire->getIdCt(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteTravailAFaire relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function joinCahierTexteTravailAFaire($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteTravailAFaire');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteTravailAFaire');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteTravailAFaire relation CahierTexteTravailAFaire object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteTravailAFaireQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCahierTexteTravailAFaire($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteTravailAFaire', 'CahierTexteTravailAFaireQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteTravailAFaireFichierJoint $cahierTexteTravailAFaireFichierJoint Object to remove from the list of results
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteTravailAFaireFichierJoint = null)
	{
		if ($cahierTexteTravailAFaireFichierJoint) {
			$this->addUsingAlias(CahierTexteTravailAFaireFichierJointPeer::ID, $cahierTexteTravailAFaireFichierJoint->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCahierTexteTravailAFaireFichierJointQuery
