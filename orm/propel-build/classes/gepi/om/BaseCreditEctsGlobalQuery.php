<?php


/**
 * Base class that represents a query for the 'ects_global_credits' table.
 *
 * Objet qui précise la mention globale obtenue pour un eleve
 *
 * @method     CreditEctsGlobalQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CreditEctsGlobalQuery orderByIdEleve($order = Criteria::ASC) Order by the id_eleve column
 * @method     CreditEctsGlobalQuery orderByMention($order = Criteria::ASC) Order by the mention column
 *
 * @method     CreditEctsGlobalQuery groupById() Group by the id column
 * @method     CreditEctsGlobalQuery groupByIdEleve() Group by the id_eleve column
 * @method     CreditEctsGlobalQuery groupByMention() Group by the mention column
 *
 * @method     CreditEctsGlobalQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CreditEctsGlobalQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CreditEctsGlobalQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CreditEctsGlobalQuery leftJoinEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsGlobalQuery rightJoinEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsGlobalQuery innerJoinEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     CreditEctsGlobal findOne(PropelPDO $con = null) Return the first CreditEctsGlobal matching the query
 * @method     CreditEctsGlobal findOneOrCreate(PropelPDO $con = null) Return the first CreditEctsGlobal matching the query, or a new CreditEctsGlobal object populated from the query conditions when no match is found
 *
 * @method     CreditEctsGlobal findOneById(int $id) Return the first CreditEctsGlobal filtered by the id column
 * @method     CreditEctsGlobal findOneByIdEleve(int $id_eleve) Return the first CreditEctsGlobal filtered by the id_eleve column
 * @method     CreditEctsGlobal findOneByMention(string $mention) Return the first CreditEctsGlobal filtered by the mention column
 *
 * @method     array findById(int $id) Return CreditEctsGlobal objects filtered by the id column
 * @method     array findByIdEleve(int $id_eleve) Return CreditEctsGlobal objects filtered by the id_eleve column
 * @method     array findByMention(string $mention) Return CreditEctsGlobal objects filtered by the mention column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCreditEctsGlobalQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCreditEctsGlobalQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CreditEctsGlobal', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CreditEctsGlobalQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CreditEctsGlobalQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CreditEctsGlobalQuery) {
			return $criteria;
		}
		$query = new CreditEctsGlobalQuery();
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
	 * <code>
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 * @param     array[$id, $id_eleve] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CreditEctsGlobal|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CreditEctsGlobalPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(CreditEctsGlobalPeer::ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(CreditEctsGlobalPeer::ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(CreditEctsGlobalPeer::ID_ELEVE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the id_eleve column
	 * 
	 * @param     int|array $idEleve The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByIdEleve($idEleve = null, $comparison = null)
	{
		if (is_array($idEleve) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $idEleve, $comparison);
	}

	/**
	 * Filter the query on the mention column
	 * 
	 * @param     string $mention The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByMention($mention = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mention)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mention)) {
				$mention = str_replace('*', '%', $mention);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CreditEctsGlobalPeer::MENTION, $mention, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		return $this
			->addUsingAlias(CreditEctsGlobalPeer::ID_ELEVE, $eleve->getIdEleve(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');
		
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
			$this->addJoinObject($join, 'Eleve');
		}
		
		return $this;
	}

	/**
	 * Use the Eleve relation Eleve object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery A secondary query class using the current class as primary query
	 */
	public function useEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CreditEctsGlobal $creditEctsGlobal Object to remove from the list of results
	 *
	 * @return    CreditEctsGlobalQuery The current query, for fluid interface
	 */
	public function prune($creditEctsGlobal = null)
	{
		if ($creditEctsGlobal) {
			$this->addCond('pruneCond0', $this->getAliasedColName(CreditEctsGlobalPeer::ID), $creditEctsGlobal->getId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(CreditEctsGlobalPeer::ID_ELEVE), $creditEctsGlobal->getIdEleve(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseCreditEctsGlobalQuery
