<?php


/**
 * Base class that represents a query for the 'matieres_categories' table.
 *
 * Categories de matiere, utilisees pour regrouper des enseignements
 *
 * @method     CategorieMatiereQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CategorieMatiereQuery orderByNomCourt($order = Criteria::ASC) Order by the nom_court column
 * @method     CategorieMatiereQuery orderByNomComplet($order = Criteria::ASC) Order by the nom_complet column
 * @method     CategorieMatiereQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 *
 * @method     CategorieMatiereQuery groupById() Group by the id column
 * @method     CategorieMatiereQuery groupByNomCourt() Group by the nom_court column
 * @method     CategorieMatiereQuery groupByNomComplet() Group by the nom_complet column
 * @method     CategorieMatiereQuery groupByPriority() Group by the priority column
 *
 * @method     CategorieMatiereQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CategorieMatiereQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CategorieMatiereQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CategorieMatiereQuery leftJoinMatiere($relationAlias = null) Adds a LEFT JOIN clause to the query using the Matiere relation
 * @method     CategorieMatiereQuery rightJoinMatiere($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Matiere relation
 * @method     CategorieMatiereQuery innerJoinMatiere($relationAlias = null) Adds a INNER JOIN clause to the query using the Matiere relation
 *
 * @method     CategorieMatiereQuery leftJoinJCategoriesMatieresClasses($relationAlias = null) Adds a LEFT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     CategorieMatiereQuery rightJoinJCategoriesMatieresClasses($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     CategorieMatiereQuery innerJoinJCategoriesMatieresClasses($relationAlias = null) Adds a INNER JOIN clause to the query using the JCategoriesMatieresClasses relation
 *
 * @method     CategorieMatiere findOne(PropelPDO $con = null) Return the first CategorieMatiere matching the query
 * @method     CategorieMatiere findOneOrCreate(PropelPDO $con = null) Return the first CategorieMatiere matching the query, or a new CategorieMatiere object populated from the query conditions when no match is found
 *
 * @method     CategorieMatiere findOneById(int $id) Return the first CategorieMatiere filtered by the id column
 * @method     CategorieMatiere findOneByNomCourt(string $nom_court) Return the first CategorieMatiere filtered by the nom_court column
 * @method     CategorieMatiere findOneByNomComplet(string $nom_complet) Return the first CategorieMatiere filtered by the nom_complet column
 * @method     CategorieMatiere findOneByPriority(int $priority) Return the first CategorieMatiere filtered by the priority column
 *
 * @method     array findById(int $id) Return CategorieMatiere objects filtered by the id column
 * @method     array findByNomCourt(string $nom_court) Return CategorieMatiere objects filtered by the nom_court column
 * @method     array findByNomComplet(string $nom_complet) Return CategorieMatiere objects filtered by the nom_complet column
 * @method     array findByPriority(int $priority) Return CategorieMatiere objects filtered by the priority column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCategorieMatiereQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCategorieMatiereQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CategorieMatiere', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CategorieMatiereQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CategorieMatiereQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CategorieMatiereQuery) {
			return $criteria;
		}
		$query = new CategorieMatiereQuery();
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
	 * @return    CategorieMatiere|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CategorieMatierePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CategorieMatierePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    CategorieMatiere A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, NOM_COURT, NOM_COMPLET, PRIORITY FROM matieres_categories WHERE ID = :p0';
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
			$obj = new CategorieMatiere();
			$obj->hydrate($row);
			CategorieMatierePeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    CategorieMatiere|array|mixed the result, formatted by the current formatter
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
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CategorieMatierePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CategorieMatierePeer::ID, $keys, Criteria::IN);
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
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CategorieMatierePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the nom_court column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNomCourt('fooValue');   // WHERE nom_court = 'fooValue'
	 * $query->filterByNomCourt('%fooValue%'); // WHERE nom_court LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomCourt The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByNomCourt($nomCourt = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomCourt)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomCourt)) {
				$nomCourt = str_replace('*', '%', $nomCourt);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CategorieMatierePeer::NOM_COURT, $nomCourt, $comparison);
	}

	/**
	 * Filter the query on the nom_complet column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNomComplet('fooValue');   // WHERE nom_complet = 'fooValue'
	 * $query->filterByNomComplet('%fooValue%'); // WHERE nom_complet LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomComplet The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByNomComplet($nomComplet = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomComplet)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomComplet)) {
				$nomComplet = str_replace('*', '%', $nomComplet);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CategorieMatierePeer::NOM_COMPLET, $nomComplet, $comparison);
	}

	/**
	 * Filter the query on the priority column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPriority(1234); // WHERE priority = 1234
	 * $query->filterByPriority(array(12, 34)); // WHERE priority IN (12, 34)
	 * $query->filterByPriority(array('min' => 12)); // WHERE priority > 12
	 * </code>
	 *
	 * @param     mixed $priority The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByPriority($priority = null, $comparison = null)
	{
		if (is_array($priority)) {
			$useMinMax = false;
			if (isset($priority['min'])) {
				$this->addUsingAlias(CategorieMatierePeer::PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($priority['max'])) {
				$this->addUsingAlias(CategorieMatierePeer::PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CategorieMatierePeer::PRIORITY, $priority, $comparison);
	}

	/**
	 * Filter the query by a related Matiere object
	 *
	 * @param     Matiere $matiere  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere, $comparison = null)
	{
		if ($matiere instanceof Matiere) {
			return $this
				->addUsingAlias(CategorieMatierePeer::ID, $matiere->getCategorieId(), $comparison);
		} elseif ($matiere instanceof PropelCollection) {
			return $this
				->useMatiereQuery()
				->filterByPrimaryKeys($matiere->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByMatiere() only accepts arguments of type Matiere or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Matiere relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function joinMatiere($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Matiere');

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
			$this->addJoinObject($join, 'Matiere');
		}

		return $this;
	}

	/**
	 * Use the Matiere relation Matiere object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MatiereQuery A secondary query class using the current class as primary query
	 */
	public function useMatiereQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinMatiere($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Matiere', 'MatiereQuery');
	}

	/**
	 * Filter the query by a related JCategoriesMatieresClasses object
	 *
	 * @param     JCategoriesMatieresClasses $jCategoriesMatieresClasses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByJCategoriesMatieresClasses($jCategoriesMatieresClasses, $comparison = null)
	{
		if ($jCategoriesMatieresClasses instanceof JCategoriesMatieresClasses) {
			return $this
				->addUsingAlias(CategorieMatierePeer::ID, $jCategoriesMatieresClasses->getCategorieId(), $comparison);
		} elseif ($jCategoriesMatieresClasses instanceof PropelCollection) {
			return $this
				->useJCategoriesMatieresClassesQuery()
				->filterByPrimaryKeys($jCategoriesMatieresClasses->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJCategoriesMatieresClasses() only accepts arguments of type JCategoriesMatieresClasses or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JCategoriesMatieresClasses relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function joinJCategoriesMatieresClasses($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JCategoriesMatieresClasses');

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
			$this->addJoinObject($join, 'JCategoriesMatieresClasses');
		}

		return $this;
	}

	/**
	 * Use the JCategoriesMatieresClasses relation JCategoriesMatieresClasses object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JCategoriesMatieresClassesQuery A secondary query class using the current class as primary query
	 */
	public function useJCategoriesMatieresClassesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJCategoriesMatieresClasses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JCategoriesMatieresClasses', 'JCategoriesMatieresClassesQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 * using the j_matieres_categories_classes table as cross reference
	 *
	 * @param     Classe $classe the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJCategoriesMatieresClassesQuery()
			->filterByClasse($classe, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CategorieMatiere $categorieMatiere Object to remove from the list of results
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function prune($categorieMatiere = null)
	{
		if ($categorieMatiere) {
			$this->addUsingAlias(CategorieMatierePeer::ID, $categorieMatiere->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseCategorieMatiereQuery