<?php


/**
 * Base class that represents a query for the 'j_notifications_resp_pers' table.
 *
 * Table de jointure entre la notification et les personnes dont on va mettre le nom dans le message.
 *
 * @method     JNotificationResponsableEleveQuery orderByANotificationId($order = Criteria::ASC) Order by the a_notification_id column
 * @method     JNotificationResponsableEleveQuery orderByPersId($order = Criteria::ASC) Order by the pers_id column
 *
 * @method     JNotificationResponsableEleveQuery groupByANotificationId() Group by the a_notification_id column
 * @method     JNotificationResponsableEleveQuery groupByPersId() Group by the pers_id column
 *
 * @method     JNotificationResponsableEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JNotificationResponsableEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JNotificationResponsableEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JNotificationResponsableEleveQuery leftJoinAbsenceEleveNotification($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     JNotificationResponsableEleveQuery rightJoinAbsenceEleveNotification($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     JNotificationResponsableEleveQuery innerJoinAbsenceEleveNotification($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveNotification relation
 *
 * @method     JNotificationResponsableEleveQuery leftJoinResponsableEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the ResponsableEleve relation
 * @method     JNotificationResponsableEleveQuery rightJoinResponsableEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the ResponsableEleve relation
 * @method     JNotificationResponsableEleveQuery innerJoinResponsableEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the ResponsableEleve relation
 *
 * @method     JNotificationResponsableEleve findOne(PropelPDO $con = null) Return the first JNotificationResponsableEleve matching the query
 * @method     JNotificationResponsableEleve findOneOrCreate(PropelPDO $con = null) Return the first JNotificationResponsableEleve matching the query, or a new JNotificationResponsableEleve object populated from the query conditions when no match is found
 *
 * @method     JNotificationResponsableEleve findOneByANotificationId(int $a_notification_id) Return the first JNotificationResponsableEleve filtered by the a_notification_id column
 * @method     JNotificationResponsableEleve findOneByPersId(string $pers_id) Return the first JNotificationResponsableEleve filtered by the pers_id column
 *
 * @method     array findByANotificationId(int $a_notification_id) Return JNotificationResponsableEleve objects filtered by the a_notification_id column
 * @method     array findByPersId(string $pers_id) Return JNotificationResponsableEleve objects filtered by the pers_id column
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
	 * Find object by primary key
	 * <code>
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 * @param     array[$a_notification_id, $pers_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JNotificationResponsableEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JNotificationResponsableElevePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @param     int|array $aNotificationId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $persId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPersId($persId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($persId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $persId)) {
				$persId = str_replace('*', '%', $persId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $persId, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveNotification object
	 *
	 * @param     AbsenceEleveNotification $absenceEleveNotification  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = null)
	{
		return $this
			->addUsingAlias(JNotificationResponsableElevePeer::A_NOTIFICATION_ID, $absenceEleveNotification->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveNotification relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveNotification($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useAbsenceEleveNotificationQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveNotification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveNotification', 'AbsenceEleveNotificationQuery');
	}

	/**
	 * Filter the query by a related ResponsableEleve object
	 *
	 * @param     ResponsableEleve $responsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = null)
	{
		return $this
			->addUsingAlias(JNotificationResponsableElevePeer::PERS_ID, $responsableEleve->getPersId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JNotificationResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinResponsableEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useResponsableEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
			$this->addCond('pruneCond1', $this->getAliasedColName(JNotificationResponsableElevePeer::PERS_ID), $jNotificationResponsableEleve->getPersId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJNotificationResponsableEleveQuery
