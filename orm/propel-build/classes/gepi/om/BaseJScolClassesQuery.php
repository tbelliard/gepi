<?php


/**
 * Base class that represents a query for the 'j_scol_classes' table.
 *
 * Table permettant le jointure entre un utilisateur scolarite et une classe.
 *
 * @method     JScolClassesQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     JScolClassesQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 *
 * @method     JScolClassesQuery groupByLogin() Group by the login column
 * @method     JScolClassesQuery groupByIdClasse() Group by the id_classe column
 *
 * @method     JScolClassesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JScolClassesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JScolClassesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JScolClassesQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JScolClassesQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JScolClassesQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     JScolClassesQuery leftJoinClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JScolClassesQuery rightJoinClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JScolClassesQuery innerJoinClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JScolClasses findOne(PropelPDO $con = null) Return the first JScolClasses matching the query
 * @method     JScolClasses findOneOrCreate(PropelPDO $con = null) Return the first JScolClasses matching the query, or a new JScolClasses object populated from the query conditions when no match is found
 *
 * @method     JScolClasses findOneByLogin(string $login) Return the first JScolClasses filtered by the login column
 * @method     JScolClasses findOneByIdClasse(int $id_classe) Return the first JScolClasses filtered by the id_classe column
 *
 * @method     array findByLogin(string $login) Return JScolClasses objects filtered by the login column
 * @method     array findByIdClasse(int $id_classe) Return JScolClasses objects filtered by the id_classe column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJScolClassesQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseJScolClassesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JScolClasses', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JScolClassesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JScolClassesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JScolClassesQuery) {
			return $criteria;
		}
		$query = new JScolClassesQuery();
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
	 * @param     array[$login, $id_classe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JScolClasses|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = JScolClassesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(JScolClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    JScolClasses A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT LOGIN, ID_CLASSE FROM j_scol_classes WHERE LOGIN = :p0 AND ID_CLASSE = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new JScolClasses();
			$obj->hydrate($row);
			JScolClassesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    JScolClasses|array|mixed the result, formatted by the current formatter
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
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JScolClassesPeer::LOGIN, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JScolClassesPeer::ID_CLASSE, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JScolClassesPeer::LOGIN, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JScolClassesPeer::ID_CLASSE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
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
	 * @return    JScolClassesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(JScolClassesPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the id_classe column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdClasse(1234); // WHERE id_classe = 1234
	 * $query->filterByIdClasse(array(12, 34)); // WHERE id_classe IN (12, 34)
	 * $query->filterByIdClasse(array('min' => 12)); // WHERE id_classe > 12
	 * </code>
	 *
	 * @see       filterByClasse()
	 *
	 * @param     mixed $idClasse The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JScolClassesPeer::ID_CLASSE, $idClasse, $comparison);
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(JScolClassesPeer::LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JScolClassesPeer::LOGIN, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    JScolClassesQuery The current query, for fluid interface
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
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe|PropelCollection $classe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		if ($classe instanceof Classe) {
			return $this
				->addUsingAlias(JScolClassesPeer::ID_CLASSE, $classe->getId(), $comparison);
		} elseif ($classe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JScolClassesPeer::ID_CLASSE, $classe->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByClasse() only accepts arguments of type Classe or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Classe');

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
			$this->addJoinObject($join, 'Classe');
		}

		return $this;
	}

	/**
	 * Use the Classe relation Classe object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery A secondary query class using the current class as primary query
	 */
	public function useClasseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JScolClasses $jScolClasses Object to remove from the list of results
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function prune($jScolClasses = null)
	{
		if ($jScolClasses) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JScolClassesPeer::LOGIN), $jScolClasses->getLogin(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JScolClassesPeer::ID_CLASSE), $jScolClasses->getIdClasse(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseJScolClassesQuery