<?php


/**
 * Base class that represents a query for the 'j_notifications_resp_pers' table.
 *
 * Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.
 *
 * @method     JNotificationResponsableEleveQuery orderByANotificationId($order = Criteria::ASC) Order by the a_notification_id column
 * @method     JNotificationResponsableEleveQuery orderByResponsableEleveId($order = Criteria::ASC) Order by the pers_id column
 *
 * @method     JNotificationResponsableEleveQuery groupByANotificationId() Group by the a_notification_id column
 * @method     JNotificationResponsableEleveQuery groupByResponsableEleveId() Group by the pers_id column
 *
 * @method     JNotificationResponsableEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JNotificationResponsableEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JNotificationResponsableEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JNotificationResponsableEleveQuery leftJoinAbsenceEleveNotification($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     JNotificationResponsableEleveQuery rightJoinAbsenceEleveNotification($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     JNotificationResponsableEleveQuery innerJoinAbsenceEleveNotification($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveNotification relation
 *
 * @method     JNotificationResponsableEleveQuery leftJoinResponsableEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableEleve relation
 * @method     JNotificationResponsableEleveQuery rightJoinResponsableEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableEleve relation
 * @method     JNotificationResponsableEleveQuery innerJoinResponsableEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableEleve relation
 *
 * @method     JNotificationResponsableEleve findOne(PropelPDO $con = null) Return the first JNotificationResponsableEleve matching the query
 * @method     JNotificationResponsableEleve findOneOrCreate(PropelPDO $con = null) Return the first JNotificationResponsableEleve matching the query, or a new JNotificationResponsableEleve object populated from the query conditions when no match is found
 *
 * @method     JNotificationResponsableEleve findOneByANotificationId(int $a_notification_id) Return the first JNotificationResponsableEleve filtered by the a_notification_id column
 * @method     JNotificationResponsableEleve findOneByResponsableEleveId(string $pers_id) Return the first JNotificationResponsableEleve filtered by the pers_id column
 *
 * @method     array findByANotificationId(int $a_notification_id) Return JNotificationResponsableEleve objects filtered by the a_notification_id column
 * @method     array findByResponsableEleveId(string $pers_id) Return JNotificationResponsableEleve objects filtered by the pers_id column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJNotificationResponsableEleveQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseJNotificationResponsableEleveQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JNotificationResponsableEleve', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JNotificationResponsableEleveQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JNotificationResponsableEleveQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JNotificationResponsableEleveQuery) {
			return $criteria;
		}
		$query = new JNotificationResponsableEleveQuery();
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
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 *
	 * @param     array[$a_notification_id, $pers_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JNotificationResponsableEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = JNotificationResponsableElevePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(JNotificationResponsableElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    JNotificationResponsableEleve A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT A_NOTIFICATION_ID, PERS_ID FROM j_notifications_resp_pers WHERE A_NOTIFICATION_ID = :p0 AND PERS_ID = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new JNotificationResponsableEleve();
			$obj->hydrate($row);
			JNotificationResponsableElevePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    JNotificationResponsableEleve|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JNotificationResponsableElevePeer::PERS_ID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the a_notification_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByANotificationId(1234); // WHERE a_notification_id = 1234
	 * $query->filterByANotificationId(array(12, 34)); // WHERE a_notification_id IN (12, 34)
	 * $query->filterByANotificationId(array('min' => 12)); // WHERE a_notification_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveNotification()
	 *
	 * @param     mixed $aNotificationId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByANotificationId($aNotificationId = null, $comparison = null)
	{
		if (is_array($aNotificationId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $aNotificationId, $comparison);
	}

	/**
	 * Filter the query on the pers_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByResponsableEleveId('fooValue');   // WHERE pers_id = 'fooValue'
	 * $query->filterByResponsableEleveId('%fooValue%'); // WHERE pers_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $responsableEleveId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleveId($responsableEleveId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($responsableEleveId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $responsableEleveId)) {
				$responsableEleveId = str_replace('*', '%', $responsableEleveId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $responsableEleveId, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveNotification object
	 *
	 * @param     AbsenceEleveNotification|PropelCollection $absenceEleveNotification The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = null)
	{
		if ($absenceEleveNotification instanceof AbsenceEleveNotification) {
			return $this
				->addUsingAlias(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $absenceEleveNotification->getId(), $comparison);
		} elseif ($absenceEleveNotification instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $absenceEleveNotification->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveNotification() only accepts arguments of type AbsenceEleveNotification or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveNotification relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveNotification($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveNotification');

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
			$this->addJoinObject($join, 'AbsenceEleveNotification');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveNotification relation AbsenceEleveNotification object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveNotificationQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveNotificationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveNotification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveNotification', 'AbsenceEleveNotificationQuery');
	}

	/**
	 * Filter the query by a related ResponsableEleve object
	 *
	 * @param     ResponsableEleve|PropelCollection $responsableEleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = null)
	{
		if ($responsableEleve instanceof ResponsableEleve) {
			return $this
				->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $responsableEleve->getResponsableEleveId(), $comparison);
		} elseif ($responsableEleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $responsableEleve->toKeyValue('PrimaryKey', 'ResponsableEleveId'), $comparison);
		} else {
			throw new PropelException('filterByResponsableEleve() only accepts arguments of type ResponsableEleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinResponsableEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ResponsableEleve');

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
			$this->addJoinObject($join, 'ResponsableEleve');
		}

		return $this;
	}

	/**
	 * Use the ResponsableEleve relation ResponsableEleve object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery A secondary query class using the current class as primary query
	 */
	public function useResponsableEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinResponsableEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableEleve', 'ResponsableEleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JNotificationResponsableEleve $jNotificationResponsableEleve Object to remove from the list of results
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function prune($jNotificationResponsableEleve = null)
	{
		if ($jNotificationResponsableEleve) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JNotificationResponsableElevePeer::A_NOTIFICATION_ID), $jNotificationResponsableEleve->getANotificationId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JNotificationResponsableElevePeer::PERS_ID), $jNotificationResponsableEleve->getResponsableEleveId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseJNotificationResponsableEleveQuery