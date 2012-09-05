<?php


/**
 * Base class that represents a query for the 'j_traitements_saisies' table.
 *
 * Table de jointure entre la saisie et le traitement des absences
 *
 * @method     JTraitementSaisieEleveQuery orderByASaisieId($order = Criteria::ASC) Order by the a_saisie_id column
 * @method     JTraitementSaisieEleveQuery orderByATraitementId($order = Criteria::ASC) Order by the a_traitement_id column
 *
 * @method     JTraitementSaisieEleveQuery groupByASaisieId() Group by the a_saisie_id column
 * @method     JTraitementSaisieEleveQuery groupByATraitementId() Group by the a_traitement_id column
 *
 * @method     JTraitementSaisieEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JTraitementSaisieEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JTraitementSaisieEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JTraitementSaisieEleveQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     JTraitementSaisieEleveQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     JTraitementSaisieEleveQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     JTraitementSaisieEleveQuery leftJoinAbsenceEleveTraitement($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     JTraitementSaisieEleveQuery rightJoinAbsenceEleveTraitement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     JTraitementSaisieEleveQuery innerJoinAbsenceEleveTraitement($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     JTraitementSaisieEleve findOne(PropelPDO $con = null) Return the first JTraitementSaisieEleve matching the query
 * @method     JTraitementSaisieEleve findOneOrCreate(PropelPDO $con = null) Return the first JTraitementSaisieEleve matching the query, or a new JTraitementSaisieEleve object populated from the query conditions when no match is found
 *
 * @method     JTraitementSaisieEleve findOneByASaisieId(int $a_saisie_id) Return the first JTraitementSaisieEleve filtered by the a_saisie_id column
 * @method     JTraitementSaisieEleve findOneByATraitementId(int $a_traitement_id) Return the first JTraitementSaisieEleve filtered by the a_traitement_id column
 *
 * @method     array findByASaisieId(int $a_saisie_id) Return JTraitementSaisieEleve objects filtered by the a_saisie_id column
 * @method     array findByATraitementId(int $a_traitement_id) Return JTraitementSaisieEleve objects filtered by the a_traitement_id column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJTraitementSaisieEleveQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseJTraitementSaisieEleveQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JTraitementSaisieEleve', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JTraitementSaisieEleveQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JTraitementSaisieEleveQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JTraitementSaisieEleveQuery) {
			return $criteria;
		}
		$query = new JTraitementSaisieEleveQuery();
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
	 * @param     array[$a_saisie_id, $a_traitement_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JTraitementSaisieEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = JTraitementSaisieElevePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(JTraitementSaisieElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    JTraitementSaisieEleve A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT A_SAISIE_ID, A_TRAITEMENT_ID FROM j_traitements_saisies WHERE A_SAISIE_ID = :p0 AND A_TRAITEMENT_ID = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new JTraitementSaisieEleve();
			$obj->hydrate($row);
			JTraitementSaisieElevePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    JTraitementSaisieEleve|array|mixed the result, formatted by the current formatter
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
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JTraitementSaisieElevePeer::A_SAISIE_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JTraitementSaisieElevePeer::A_SAISIE_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the a_saisie_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByASaisieId(1234); // WHERE a_saisie_id = 1234
	 * $query->filterByASaisieId(array(12, 34)); // WHERE a_saisie_id IN (12, 34)
	 * $query->filterByASaisieId(array('min' => 12)); // WHERE a_saisie_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveSaisie()
	 *
	 * @param     mixed $aSaisieId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByASaisieId($aSaisieId = null, $comparison = null)
	{
		if (is_array($aSaisieId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JTraitementSaisieElevePeer::A_SAISIE_ID, $aSaisieId, $comparison);
	}

	/**
	 * Filter the query on the a_traitement_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByATraitementId(1234); // WHERE a_traitement_id = 1234
	 * $query->filterByATraitementId(array(12, 34)); // WHERE a_traitement_id IN (12, 34)
	 * $query->filterByATraitementId(array('min' => 12)); // WHERE a_traitement_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveTraitement()
	 *
	 * @param     mixed $aTraitementId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByATraitementId($aTraitementId = null, $comparison = null)
	{
		if (is_array($aTraitementId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $aTraitementId, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie|PropelCollection $absenceEleveSaisie The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		if ($absenceEleveSaisie instanceof AbsenceEleveSaisie) {
			return $this
				->addUsingAlias(JTraitementSaisieElevePeer::A_SAISIE_ID, $absenceEleveSaisie->getId(), $comparison);
		} elseif ($absenceEleveSaisie instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JTraitementSaisieElevePeer::A_SAISIE_ID, $absenceEleveSaisie->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveSaisie() only accepts arguments of type AbsenceEleveSaisie or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useAbsenceEleveSaisieQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement|PropelCollection $absenceEleveTraitement The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = null)
	{
		if ($absenceEleveTraitement instanceof AbsenceEleveTraitement) {
			return $this
				->addUsingAlias(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $absenceEleveTraitement->getId(), $comparison);
		} elseif ($absenceEleveTraitement instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $absenceEleveTraitement->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveTraitement() only accepts arguments of type AbsenceEleveTraitement or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTraitement');

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
			$this->addJoinObject($join, 'AbsenceEleveTraitement');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveTraitement relation AbsenceEleveTraitement object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTraitementQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JTraitementSaisieEleve $jTraitementSaisieEleve Object to remove from the list of results
	 *
	 * @return    JTraitementSaisieEleveQuery The current query, for fluid interface
	 */
	public function prune($jTraitementSaisieEleve = null)
	{
		if ($jTraitementSaisieEleve) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JTraitementSaisieElevePeer::A_SAISIE_ID), $jTraitementSaisieEleve->getASaisieId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JTraitementSaisieElevePeer::A_TRAITEMENT_ID), $jTraitementSaisieEleve->getATraitementId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseJTraitementSaisieEleveQuery