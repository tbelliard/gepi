<?php


/**
 * Base class that represents a query for the 'ct_sequences' table.
 *
 * Sequence de plusieurs compte-rendus
 *
 * @method     CahierTexteSequenceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CahierTexteSequenceQuery orderByTitre($order = Criteria::ASC) Order by the titre column
 * @method     CahierTexteSequenceQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     CahierTexteSequenceQuery groupById() Group by the id column
 * @method     CahierTexteSequenceQuery groupByTitre() Group by the titre column
 * @method     CahierTexteSequenceQuery groupByDescription() Group by the description column
 *
 * @method     CahierTexteSequenceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteSequenceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteSequenceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteCompteRendu($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteCompteRendu($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteCompteRendu($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteCompteRendu relation
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteTravailAFaire($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteTravailAFaire($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteTravailAFaire($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteTravailAFaire relation
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteNoticePrivee($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteNoticePrivee($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteNoticePrivee($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteNoticePrivee relation
 *
 * @method     CahierTexteSequence findOne(PropelPDO $con = null) Return the first CahierTexteSequence matching the query
 * @method     CahierTexteSequence findOneOrCreate(PropelPDO $con = null) Return the first CahierTexteSequence matching the query, or a new CahierTexteSequence object populated from the query conditions when no match is found
 *
 * @method     CahierTexteSequence findOneById(int $id) Return the first CahierTexteSequence filtered by the id column
 * @method     CahierTexteSequence findOneByTitre(string $titre) Return the first CahierTexteSequence filtered by the titre column
 * @method     CahierTexteSequence findOneByDescription(string $description) Return the first CahierTexteSequence filtered by the description column
 *
 * @method     array findById(int $id) Return CahierTexteSequence objects filtered by the id column
 * @method     array findByTitre(string $titre) Return CahierTexteSequence objects filtered by the titre column
 * @method     array findByDescription(string $description) Return CahierTexteSequence objects filtered by the description column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteSequenceQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCahierTexteSequenceQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteSequence', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteSequenceQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteSequenceQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteSequenceQuery) {
			return $criteria;
		}
		$query = new CahierTexteSequenceQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key.
	 * Propel uses the instance pool to skip the database if the object exists.
	 * Go fast if the query is untouched.
	 *
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CahierTexteSequence|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CahierTexteSequencePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteSequencePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		if ($this->formatter || $this->modelAlias || $this->with || $this->select
		 || $this->selectColumns || $this->asColumns || $this->selectModifiers
		 || $this->map || $this->having || $this->joins) {
			return $this->findPkComplex($key, $con);
		} else {
			return $this->findPkSimple($key, $con);
		}
	}

	/**
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    CahierTexteSequence A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, TITRE, DESCRIPTION FROM ct_sequences WHERE ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new CahierTexteSequence();
			$obj->hydrate($row);
			CahierTexteSequencePeer::addInstanceToPool($obj, (string) $key);
		}
		$stmt->closeCursor();

		return $obj;
	}

	/**
	 * Find object by primary key.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    CahierTexteSequence|array|mixed the result, formatted by the current formatter
	 */
	protected function findPkComplex($key, $con)
	{
		// As the query uses a PK condition, no limit(1) is necessary.
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKey($key)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
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
		if ($con === null) {
			$con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKeys($keys)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->format($stmt);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteSequencePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteSequencePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById(1234); // WHERE id = 1234
	 * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CahierTexteSequencePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the titre column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTitre('fooValue');   // WHERE titre = 'fooValue'
	 * $query->filterByTitre('%fooValue%'); // WHERE titre LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $titre The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CahierTexteSequencePeer::TITRE, $titre, $comparison);
	}

	/**
	 * Filter the query on the description column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
	 * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $description The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByDescription($description = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($description)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $description)) {
				$description = str_replace('*', '%', $description);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteSequencePeer::DESCRIPTION, $description, $comparison);
	}

	/**
	 * Filter the query by a related CahierTexteCompteRendu object
	 *
	 * @param     CahierTexteCompteRendu $cahierTexteCompteRendu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteCompteRendu($cahierTexteCompteRendu, $comparison = null)
	{
		if ($cahierTexteCompteRendu instanceof CahierTexteCompteRendu) {
			return $this
				->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteCompteRendu->getIdSequence(), $comparison);
		} elseif ($cahierTexteCompteRendu instanceof PropelCollection) {
			return $this
				->useCahierTexteCompteRenduQuery()
				->filterByPrimaryKeys($cahierTexteCompteRendu->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByCahierTexteCompteRendu() only accepts arguments of type CahierTexteCompteRendu or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteCompteRendu relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteCompteRendu($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteCompteRendu');

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
	public function useCahierTexteCompteRenduQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteCompteRendu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteCompteRendu', 'CahierTexteCompteRenduQuery');
	}

	/**
	 * Filter the query by a related CahierTexteTravailAFaire object
	 *
	 * @param     CahierTexteTravailAFaire $cahierTexteTravailAFaire  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteTravailAFaire($cahierTexteTravailAFaire, $comparison = null)
	{
		if ($cahierTexteTravailAFaire instanceof CahierTexteTravailAFaire) {
			return $this
				->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteTravailAFaire->getIdSequence(), $comparison);
		} elseif ($cahierTexteTravailAFaire instanceof PropelCollection) {
			return $this
				->useCahierTexteTravailAFaireQuery()
				->filterByPrimaryKeys($cahierTexteTravailAFaire->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByCahierTexteTravailAFaire() only accepts arguments of type CahierTexteTravailAFaire or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteTravailAFaire relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteTravailAFaire($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useCahierTexteTravailAFaireQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteTravailAFaire($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteTravailAFaire', 'CahierTexteTravailAFaireQuery');
	}

	/**
	 * Filter the query by a related CahierTexteNoticePrivee object
	 *
	 * @param     CahierTexteNoticePrivee $cahierTexteNoticePrivee  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteNoticePrivee($cahierTexteNoticePrivee, $comparison = null)
	{
		if ($cahierTexteNoticePrivee instanceof CahierTexteNoticePrivee) {
			return $this
				->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteNoticePrivee->getIdSequence(), $comparison);
		} elseif ($cahierTexteNoticePrivee instanceof PropelCollection) {
			return $this
				->useCahierTexteNoticePriveeQuery()
				->filterByPrimaryKeys($cahierTexteNoticePrivee->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByCahierTexteNoticePrivee() only accepts arguments of type CahierTexteNoticePrivee or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteNoticePrivee relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteNoticePrivee($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteNoticePrivee');

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
			$this->addJoinObject($join, 'CahierTexteNoticePrivee');
		}

		return $this;
	}

	/**
	 * Use the CahierTexteNoticePrivee relation CahierTexteNoticePrivee object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteNoticePriveeQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteNoticePriveeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteNoticePrivee($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteNoticePrivee', 'CahierTexteNoticePriveeQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteSequence $cahierTexteSequence Object to remove from the list of results
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteSequence = null)
	{
		if ($cahierTexteSequence) {
			$this->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteSequence->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseCahierTexteSequenceQuery