<?php


/**
 * Base class that represents a query for the 'preferences' table.
 *
 * Preference (cle - valeur) associes à un utilisateur professionnel
 *
 * @method     PreferenceUtilisateurProfessionnelQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     PreferenceUtilisateurProfessionnelQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method     PreferenceUtilisateurProfessionnelQuery orderByLogin($order = Criteria::ASC) Order by the login column
 *
 * @method     PreferenceUtilisateurProfessionnelQuery groupByName() Group by the name column
 * @method     PreferenceUtilisateurProfessionnelQuery groupByValue() Group by the value column
 * @method     PreferenceUtilisateurProfessionnelQuery groupByLogin() Group by the login column
 *
 * @method     PreferenceUtilisateurProfessionnelQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PreferenceUtilisateurProfessionnelQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PreferenceUtilisateurProfessionnelQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PreferenceUtilisateurProfessionnelQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     PreferenceUtilisateurProfessionnelQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     PreferenceUtilisateurProfessionnelQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     PreferenceUtilisateurProfessionnel findOne(PropelPDO $con = null) Return the first PreferenceUtilisateurProfessionnel matching the query
 * @method     PreferenceUtilisateurProfessionnel findOneOrCreate(PropelPDO $con = null) Return the first PreferenceUtilisateurProfessionnel matching the query, or a new PreferenceUtilisateurProfessionnel object populated from the query conditions when no match is found
 *
 * @method     PreferenceUtilisateurProfessionnel findOneByName(string $name) Return the first PreferenceUtilisateurProfessionnel filtered by the name column
 * @method     PreferenceUtilisateurProfessionnel findOneByValue(string $value) Return the first PreferenceUtilisateurProfessionnel filtered by the value column
 * @method     PreferenceUtilisateurProfessionnel findOneByLogin(string $login) Return the first PreferenceUtilisateurProfessionnel filtered by the login column
 *
 * @method     array findByName(string $name) Return PreferenceUtilisateurProfessionnel objects filtered by the name column
 * @method     array findByValue(string $value) Return PreferenceUtilisateurProfessionnel objects filtered by the value column
 * @method     array findByLogin(string $login) Return PreferenceUtilisateurProfessionnel objects filtered by the login column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePreferenceUtilisateurProfessionnelQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BasePreferenceUtilisateurProfessionnelQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'PreferenceUtilisateurProfessionnel', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PreferenceUtilisateurProfessionnelQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PreferenceUtilisateurProfessionnelQuery) {
			return $criteria;
		}
		$query = new PreferenceUtilisateurProfessionnelQuery();
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
	 * @param     array[$name, $login] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PreferenceUtilisateurProfessionnel|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = PreferenceUtilisateurProfessionnelPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(PreferenceUtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    PreferenceUtilisateurProfessionnel A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT NAME, VALUE, LOGIN FROM preferences WHERE NAME = :p0 AND LOGIN = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new PreferenceUtilisateurProfessionnel();
			$obj->hydrate($row);
			PreferenceUtilisateurProfessionnelPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    PreferenceUtilisateurProfessionnel|array|mixed the result, formatted by the current formatter
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
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::NAME, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(PreferenceUtilisateurProfessionnelPeer::NAME, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(PreferenceUtilisateurProfessionnelPeer::LOGIN, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the name column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
	 * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $name The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByName($name = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($name)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $name)) {
				$name = str_replace('*', '%', $name);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::NAME, $name, $comparison);
	}

	/**
	 * Filter the query on the value column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
	 * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $value The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByValue($value = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($value)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $value)) {
				$value = str_replace('*', '%', $value);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::VALUE, $value, $comparison);
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
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
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
		return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
		} else {
			throw new PropelException('filterByUtilisateurProfessionnel() only accepts arguments of type UtilisateurProfessionnel or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('UtilisateurProfessionnel');

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
			$this->addJoinObject($join, 'UtilisateurProfessionnel');
		}

		return $this;
	}

	/**
	 * Use the UtilisateurProfessionnel relation UtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function useUtilisateurProfessionnelQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     PreferenceUtilisateurProfessionnel $preferenceUtilisateurProfessionnel Object to remove from the list of results
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function prune($preferenceUtilisateurProfessionnel = null)
	{
		if ($preferenceUtilisateurProfessionnel) {
			$this->addCond('pruneCond0', $this->getAliasedColName(PreferenceUtilisateurProfessionnelPeer::NAME), $preferenceUtilisateurProfessionnel->getName(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(PreferenceUtilisateurProfessionnelPeer::LOGIN), $preferenceUtilisateurProfessionnel->getLogin(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BasePreferenceUtilisateurProfessionnelQuery