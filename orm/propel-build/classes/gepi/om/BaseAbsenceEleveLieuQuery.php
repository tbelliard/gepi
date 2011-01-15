<?php


/**
 * Base class that represents a query for the 'a_lieux' table.
 *
 * Lieu pour les types d'absence ou les saisies
 *
 * @method     AbsenceEleveLieuQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveLieuQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AbsenceEleveLieuQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveLieuQuery orderBySortableRank($order = Criteria::ASC) Order by the sortable_rank column
 *
 * @method     AbsenceEleveLieuQuery groupById() Group by the id column
 * @method     AbsenceEleveLieuQuery groupByNom() Group by the nom column
 * @method     AbsenceEleveLieuQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveLieuQuery groupBySortableRank() Group by the sortable_rank column
 *
 * @method     AbsenceEleveLieuQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveLieuQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveLieuQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveLieuQuery leftJoinAbsenceEleveType($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveLieuQuery rightJoinAbsenceEleveType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveLieuQuery innerJoinAbsenceEleveType($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveType relation
 *
 * @method     AbsenceEleveLieuQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AbsenceEleveLieuQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AbsenceEleveLieuQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     AbsenceEleveLieu findOne(PropelPDO $con = null) Return the first AbsenceEleveLieu matching the query
 * @method     AbsenceEleveLieu findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveLieu matching the query, or a new AbsenceEleveLieu object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveLieu findOneById(int $id) Return the first AbsenceEleveLieu filtered by the id column
 * @method     AbsenceEleveLieu findOneByNom(string $nom) Return the first AbsenceEleveLieu filtered by the nom column
 * @method     AbsenceEleveLieu findOneByCommentaire(string $commentaire) Return the first AbsenceEleveLieu filtered by the commentaire column
 * @method     AbsenceEleveLieu findOneBySortableRank(int $sortable_rank) Return the first AbsenceEleveLieu filtered by the sortable_rank column
 *
 * @method     array findById(int $id) Return AbsenceEleveLieu objects filtered by the id column
 * @method     array findByNom(string $nom) Return AbsenceEleveLieu objects filtered by the nom column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveLieu objects filtered by the commentaire column
 * @method     array findBySortableRank(int $sortable_rank) Return AbsenceEleveLieu objects filtered by the sortable_rank column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveLieuQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveLieuQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveLieu', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveLieuQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveLieuQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveLieuQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveLieuQuery();
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
	 * @return    AbsenceEleveLieu|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveLieuPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveLieuPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveLieuPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveLieuPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nom)) {
				$nom = str_replace('*', '%', $nom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveLieuPeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByCommentaire($commentaire = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($commentaire)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $commentaire)) {
				$commentaire = str_replace('*', '%', $commentaire);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveLieuPeer::COMMENTAIRE, $commentaire, $comparison);
	}

	/**
	 * Filter the query on the sortable_rank column
	 * 
	 * @param     int|array $sortableRank The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterBySortableRank($sortableRank = null, $comparison = null)
	{
		if (is_array($sortableRank)) {
			$useMinMax = false;
			if (isset($sortableRank['min'])) {
				$this->addUsingAlias(AbsenceEleveLieuPeer::SORTABLE_RANK, $sortableRank['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($sortableRank['max'])) {
				$this->addUsingAlias(AbsenceEleveLieuPeer::SORTABLE_RANK, $sortableRank['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveLieuPeer::SORTABLE_RANK, $sortableRank, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveType object
	 *
	 * @param     AbsenceEleveType $absenceEleveType  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveType($absenceEleveType, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveLieuPeer::ID, $absenceEleveType->getIdLieu(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveType relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveType($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveType');
		
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
			$this->addJoinObject($join, 'AbsenceEleveType');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveType relation AbsenceEleveType object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveType($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveType', 'AbsenceEleveTypeQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveLieuPeer::ID, $absenceEleveSaisie->getIdLieu(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisie');
		
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
			$this->addJoinObject($join, 'AbsenceEleveSaisie');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveSaisie relation AbsenceEleveSaisie object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveSaisieQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveLieu $absenceEleveLieu Object to remove from the list of results
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveLieu = null)
	{
		if ($absenceEleveLieu) {
			$this->addUsingAlias(AbsenceEleveLieuPeer::ID, $absenceEleveLieu->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	// sortable behavior
	
	/**
	 * Filter the query based on a rank in the list
	 *
	 * @param     integer   $rank rank
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function filterByRank($rank)
	{
		return $this
			->addUsingAlias(AbsenceEleveLieuPeer::RANK_COL, $rank, Criteria::EQUAL);
	}
	
	/**
	 * Order the query based on the rank in the list.
	 * Using the default $order, returns the item with the lowest rank first
	 *
	 * @param     string $order either Criteria::ASC (default) or Criteria::DESC
	 *
	 * @return    AbsenceEleveLieuQuery The current query, for fluid interface
	 */
	public function orderByRank($order = Criteria::ASC)
	{
		$order = strtoupper($order);
		switch ($order) {
			case Criteria::ASC:
				return $this->addAscendingOrderByColumn($this->getAliasedColName(AbsenceEleveLieuPeer::RANK_COL));
				break;
			case Criteria::DESC:
				return $this->addDescendingOrderByColumn($this->getAliasedColName(AbsenceEleveLieuPeer::RANK_COL));
				break;
			default:
				throw new PropelException('AbsenceEleveLieuQuery::orderBy() only accepts "asc" or "desc" as argument');
		}
	}
	
	/**
	 * Get an item from the list based on its rank
	 *
	 * @param     integer   $rank rank
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveLieu
	 */
	public function findOneByRank($rank, PropelPDO $con = null)
	{
		return $this
			->filterByRank($rank)
			->findOne($con);
	}
	
	/**
	 * Returns the list of objects
	 *
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     mixed the list of results, formatted by the current formatter
	 */
	public function findList($con = null)
	{
		return $this
			->orderByRank()
			->find($con);
	}
	
	/**
	 * Get the highest rank
	 * 
	 * @param     PropelPDO optional connection
	 *
	 * @return    integer highest position
	 */
	public function getMaxRank(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveLieuPeer::DATABASE_NAME);
		}
		// shift the objects with a position lower than the one of object
		$this->addSelectColumn('MAX(' . AbsenceEleveLieuPeer::RANK_COL . ')');
		$stmt = $this->getSelectStatement($con);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * Reorder a set of sortable objects based on a list of id/position
	 * Beware that there is no check made on the positions passed
	 * So incoherent positions will result in an incoherent list
	 *
	 * @param     array     $order id => rank pairs
	 * @param     PropelPDO $con   optional connection
	 *
	 * @return    boolean true if the reordering took place, false if a database problem prevented it
	 */
	public function reorder(array $order, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveLieuPeer::DATABASE_NAME);
		}
		
		$con->beginTransaction();
		try {
			$ids = array_keys($order);
			$objects = $this->findPks($ids, $con);
			foreach ($objects as $object) {
				$pk = $object->getPrimaryKey();
				if ($object->getSortableRank() != $order[$pk]) {
					$object->setSortableRank($order[$pk]);
					$object->save($con);
				}
			}
			$con->commit();
	
			return true;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

} // BaseAbsenceEleveLieuQuery
