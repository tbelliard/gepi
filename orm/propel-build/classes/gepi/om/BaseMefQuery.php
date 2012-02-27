<?php


/**
 * Base class that represents a query for the 'mef' table.
 *
 * Module élémentaire de formation
 *
 * @method     MefQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     MefQuery orderByMefCode($order = Criteria::ASC) Order by the mef_code column
 * @method     MefQuery orderByLibelleCourt($order = Criteria::ASC) Order by the libelle_court column
 * @method     MefQuery orderByLibelleLong($order = Criteria::ASC) Order by the libelle_long column
 * @method     MefQuery orderByLibelleEdition($order = Criteria::ASC) Order by the libelle_edition column
 *
 * @method     MefQuery groupById() Group by the id column
 * @method     MefQuery groupByMefCode() Group by the mef_code column
 * @method     MefQuery groupByLibelleCourt() Group by the libelle_court column
 * @method     MefQuery groupByLibelleLong() Group by the libelle_long column
 * @method     MefQuery groupByLibelleEdition() Group by the libelle_edition column
 *
 * @method     MefQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     MefQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     MefQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     MefQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     MefQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     MefQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     Mef findOne(PropelPDO $con = null) Return the first Mef matching the query
 * @method     Mef findOneOrCreate(PropelPDO $con = null) Return the first Mef matching the query, or a new Mef object populated from the query conditions when no match is found
 *
 * @method     Mef findOneById(int $id) Return the first Mef filtered by the id column
 * @method     Mef findOneByMefCode(int $mef_code) Return the first Mef filtered by the mef_code column
 * @method     Mef findOneByLibelleCourt(string $libelle_court) Return the first Mef filtered by the libelle_court column
 * @method     Mef findOneByLibelleLong(string $libelle_long) Return the first Mef filtered by the libelle_long column
 * @method     Mef findOneByLibelleEdition(string $libelle_edition) Return the first Mef filtered by the libelle_edition column
 *
 * @method     array findById(int $id) Return Mef objects filtered by the id column
 * @method     array findByMefCode(int $mef_code) Return Mef objects filtered by the mef_code column
 * @method     array findByLibelleCourt(string $libelle_court) Return Mef objects filtered by the libelle_court column
 * @method     array findByLibelleLong(string $libelle_long) Return Mef objects filtered by the libelle_long column
 * @method     array findByLibelleEdition(string $libelle_edition) Return Mef objects filtered by the libelle_edition column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseMefQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseMefQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Mef', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new MefQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    MefQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof MefQuery) {
			return $criteria;
		}
		$query = new MefQuery();
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
	 * @return    Mef|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = MefPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(MefPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Mef A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, MEF_CODE, LIBELLE_COURT, LIBELLE_LONG, LIBELLE_EDITION FROM mef WHERE ID = :p0';
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
			$obj = new Mef();
			$obj->hydrate($row);
			MefPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    Mef|array|mixed the result, formatted by the current formatter
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
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(MefPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(MefPeer::ID, $keys, Criteria::IN);
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
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(MefPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the mef_code column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMefCode(1234); // WHERE mef_code = 1234
	 * $query->filterByMefCode(array(12, 34)); // WHERE mef_code IN (12, 34)
	 * $query->filterByMefCode(array('min' => 12)); // WHERE mef_code > 12
	 * </code>
	 *
	 * @param     mixed $mefCode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByMefCode($mefCode = null, $comparison = null)
	{
		if (is_array($mefCode)) {
			$useMinMax = false;
			if (isset($mefCode['min'])) {
				$this->addUsingAlias(MefPeer::MEF_CODE, $mefCode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mefCode['max'])) {
				$this->addUsingAlias(MefPeer::MEF_CODE, $mefCode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(MefPeer::MEF_CODE, $mefCode, $comparison);
	}

	/**
	 * Filter the query on the libelle_court column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLibelleCourt('fooValue');   // WHERE libelle_court = 'fooValue'
	 * $query->filterByLibelleCourt('%fooValue%'); // WHERE libelle_court LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $libelleCourt The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleCourt($libelleCourt = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleCourt)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleCourt)) {
				$libelleCourt = str_replace('*', '%', $libelleCourt);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_COURT, $libelleCourt, $comparison);
	}

	/**
	 * Filter the query on the libelle_long column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLibelleLong('fooValue');   // WHERE libelle_long = 'fooValue'
	 * $query->filterByLibelleLong('%fooValue%'); // WHERE libelle_long LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $libelleLong The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleLong($libelleLong = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleLong)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleLong)) {
				$libelleLong = str_replace('*', '%', $libelleLong);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_LONG, $libelleLong, $comparison);
	}

	/**
	 * Filter the query on the libelle_edition column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLibelleEdition('fooValue');   // WHERE libelle_edition = 'fooValue'
	 * $query->filterByLibelleEdition('%fooValue%'); // WHERE libelle_edition LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $libelleEdition The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleEdition($libelleEdition = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleEdition)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleEdition)) {
				$libelleEdition = str_replace('*', '%', $libelleEdition);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_EDITION, $libelleEdition, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(MefPeer::MEF_CODE, $eleve->getMefCode(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			return $this
				->useEleveQuery()
				->filterByPrimaryKeys($eleve->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByEleve() only accepts arguments of type Eleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Mef $mef Object to remove from the list of results
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function prune($mef = null)
	{
		if ($mef) {
			$this->addUsingAlias(MefPeer::ID, $mef->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseMefQuery