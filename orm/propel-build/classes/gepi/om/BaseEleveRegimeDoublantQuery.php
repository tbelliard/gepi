<?php


/**
 * Base class that represents a query for the 'j_eleves_regime' table.
 *
 * Mention du redoublement eventuel de l'eleve ainsi que son regime de presence (externe, demi-pensionnaire, ...)
 *
 * @method     EleveRegimeDoublantQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     EleveRegimeDoublantQuery orderByDoublant($order = Criteria::ASC) Order by the doublant column
 * @method     EleveRegimeDoublantQuery orderByRegime($order = Criteria::ASC) Order by the regime column
 *
 * @method     EleveRegimeDoublantQuery groupByLogin() Group by the login column
 * @method     EleveRegimeDoublantQuery groupByDoublant() Group by the doublant column
 * @method     EleveRegimeDoublantQuery groupByRegime() Group by the regime column
 *
 * @method     EleveRegimeDoublantQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EleveRegimeDoublantQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EleveRegimeDoublantQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EleveRegimeDoublantQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     EleveRegimeDoublantQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     EleveRegimeDoublantQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     EleveRegimeDoublant findOne(PropelPDO $con = null) Return the first EleveRegimeDoublant matching the query
 * @method     EleveRegimeDoublant findOneOrCreate(PropelPDO $con = null) Return the first EleveRegimeDoublant matching the query, or a new EleveRegimeDoublant object populated from the query conditions when no match is found
 *
 * @method     EleveRegimeDoublant findOneByLogin(string $login) Return the first EleveRegimeDoublant filtered by the login column
 * @method     EleveRegimeDoublant findOneByDoublant(string $doublant) Return the first EleveRegimeDoublant filtered by the doublant column
 * @method     EleveRegimeDoublant findOneByRegime(string $regime) Return the first EleveRegimeDoublant filtered by the regime column
 *
 * @method     array findByLogin(string $login) Return EleveRegimeDoublant objects filtered by the login column
 * @method     array findByDoublant(string $doublant) Return EleveRegimeDoublant objects filtered by the doublant column
 * @method     array findByRegime(string $regime) Return EleveRegimeDoublant objects filtered by the regime column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEleveRegimeDoublantQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseEleveRegimeDoublantQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EleveRegimeDoublant', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EleveRegimeDoublantQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EleveRegimeDoublantQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EleveRegimeDoublantQuery) {
			return $criteria;
		}
		$query = new EleveRegimeDoublantQuery();
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
	 * @return    EleveRegimeDoublant|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = EleveRegimeDoublantPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(EleveRegimeDoublantPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    EleveRegimeDoublant A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT LOGIN, DOUBLANT, REGIME FROM j_eleves_regime WHERE LOGIN = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new EleveRegimeDoublant();
			$obj->hydrate($row);
			EleveRegimeDoublantPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    EleveRegimeDoublant|array|mixed the result, formatted by the current formatter
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
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the login column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByLogin('fooValue');   // WHERE login = 'fooValue'
	 * $query->filterByLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $login The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($login)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $login)) {
				$login = str_replace('*', '%', $login);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the doublant column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDoublant('fooValue');   // WHERE doublant = 'fooValue'
	 * $query->filterByDoublant('%fooValue%'); // WHERE doublant LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $doublant The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByDoublant($doublant = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($doublant)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $doublant)) {
				$doublant = str_replace('*', '%', $doublant);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EleveRegimeDoublantPeer::DOUBLANT, $doublant, $comparison);
	}

	/**
	 * Filter the query on the regime column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRegime('fooValue');   // WHERE regime = 'fooValue'
	 * $query->filterByRegime('%fooValue%'); // WHERE regime LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $regime The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByRegime($regime = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($regime)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $regime)) {
				$regime = str_replace('*', '%', $regime);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EleveRegimeDoublantPeer::REGIME, $regime, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $eleve->getLogin(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $eleve->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EleveRegimeDoublant $eleveRegimeDoublant Object to remove from the list of results
	 *
	 * @return    EleveRegimeDoublantQuery The current query, for fluid interface
	 */
	public function prune($eleveRegimeDoublant = null)
	{
		if ($eleveRegimeDoublant) {
			$this->addUsingAlias(EleveRegimeDoublantPeer::LOGIN, $eleveRegimeDoublant->getLogin(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseEleveRegimeDoublantQuery