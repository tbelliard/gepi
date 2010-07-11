<?php


/**
 * Base class that represents a query for the 'j_matieres_categories_classes' table.
 *
 * Liaison entre categories de matiere et classes
 *
 * @method     JCategoriesMatieresClassesQuery orderByCategorieId($order = Criteria::ASC) Order by the categorie_id column
 * @method     JCategoriesMatieresClassesQuery orderByClasseId($order = Criteria::ASC) Order by the classe_id column
 * @method     JCategoriesMatieresClassesQuery orderByAfficheMoyenne($order = Criteria::ASC) Order by the affiche_moyenne column
 * @method     JCategoriesMatieresClassesQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 *
 * @method     JCategoriesMatieresClassesQuery groupByCategorieId() Group by the categorie_id column
 * @method     JCategoriesMatieresClassesQuery groupByClasseId() Group by the classe_id column
 * @method     JCategoriesMatieresClassesQuery groupByAfficheMoyenne() Group by the affiche_moyenne column
 * @method     JCategoriesMatieresClassesQuery groupByPriority() Group by the priority column
 *
 * @method     JCategoriesMatieresClassesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JCategoriesMatieresClassesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JCategoriesMatieresClassesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JCategoriesMatieresClassesQuery leftJoinCategorieMatiere($relationAlias = '') Adds a LEFT JOIN clause to the query using the CategorieMatiere relation
 * @method     JCategoriesMatieresClassesQuery rightJoinCategorieMatiere($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CategorieMatiere relation
 * @method     JCategoriesMatieresClassesQuery innerJoinCategorieMatiere($relationAlias = '') Adds a INNER JOIN clause to the query using the CategorieMatiere relation
 *
 * @method     JCategoriesMatieresClassesQuery leftJoinClasse($relationAlias = '') Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JCategoriesMatieresClassesQuery rightJoinClasse($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JCategoriesMatieresClassesQuery innerJoinClasse($relationAlias = '') Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JCategoriesMatieresClasses findOne(PropelPDO $con = null) Return the first JCategoriesMatieresClasses matching the query
 * @method     JCategoriesMatieresClasses findOneOrCreate(PropelPDO $con = null) Return the first JCategoriesMatieresClasses matching the query, or a new JCategoriesMatieresClasses object populated from the query conditions when no match is found
 *
 * @method     JCategoriesMatieresClasses findOneByCategorieId(int $categorie_id) Return the first JCategoriesMatieresClasses filtered by the categorie_id column
 * @method     JCategoriesMatieresClasses findOneByClasseId(int $classe_id) Return the first JCategoriesMatieresClasses filtered by the classe_id column
 * @method     JCategoriesMatieresClasses findOneByAfficheMoyenne(boolean $affiche_moyenne) Return the first JCategoriesMatieresClasses filtered by the affiche_moyenne column
 * @method     JCategoriesMatieresClasses findOneByPriority(int $priority) Return the first JCategoriesMatieresClasses filtered by the priority column
 *
 * @method     array findByCategorieId(int $categorie_id) Return JCategoriesMatieresClasses objects filtered by the categorie_id column
 * @method     array findByClasseId(int $classe_id) Return JCategoriesMatieresClasses objects filtered by the classe_id column
 * @method     array findByAfficheMoyenne(boolean $affiche_moyenne) Return JCategoriesMatieresClasses objects filtered by the affiche_moyenne column
 * @method     array findByPriority(int $priority) Return JCategoriesMatieresClasses objects filtered by the priority column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJCategoriesMatieresClassesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJCategoriesMatieresClassesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JCategoriesMatieresClasses', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JCategoriesMatieresClassesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JCategoriesMatieresClassesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JCategoriesMatieresClassesQuery) {
			return $criteria;
		}
		$query = new JCategoriesMatieresClassesQuery();
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
	 * @param     array[$categorie_id, $classe_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JCategoriesMatieresClasses|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JCategoriesMatieresClassesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JCategoriesMatieresClassesPeer::CLASSE_ID, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JCategoriesMatieresClassesPeer::CLASSE_ID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the categorie_id column
	 * 
	 * @param     int|array $categorieId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByCategorieId($categorieId = null, $comparison = null)
	{
		if (is_array($categorieId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $categorieId, $comparison);
	}

	/**
	 * Filter the query on the classe_id column
	 * 
	 * @param     int|array $classeId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByClasseId($classeId = null, $comparison = null)
	{
		if (is_array($classeId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JCategoriesMatieresClassesPeer::CLASSE_ID, $classeId, $comparison);
	}

	/**
	 * Filter the query on the affiche_moyenne column
	 * 
	 * @param     boolean|string $afficheMoyenne The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByAfficheMoyenne($afficheMoyenne = null, $comparison = null)
	{
		if (is_string($afficheMoyenne)) {
			$affiche_moyenne = in_array(strtolower($afficheMoyenne), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(JCategoriesMatieresClassesPeer::AFFICHE_MOYENNE, $afficheMoyenne, $comparison);
	}

	/**
	 * Filter the query on the priority column
	 * 
	 * @param     int|array $priority The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByPriority($priority = null, $comparison = null)
	{
		if (is_array($priority)) {
			$useMinMax = false;
			if (isset($priority['min'])) {
				$this->addUsingAlias(JCategoriesMatieresClassesPeer::PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($priority['max'])) {
				$this->addUsingAlias(JCategoriesMatieresClassesPeer::PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JCategoriesMatieresClassesPeer::PRIORITY, $priority, $comparison);
	}

	/**
	 * Filter the query by a related CategorieMatiere object
	 *
	 * @param     CategorieMatiere $categorieMatiere  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByCategorieMatiere($categorieMatiere, $comparison = null)
	{
		return $this
			->addUsingAlias(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $categorieMatiere->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CategorieMatiere relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function joinCategorieMatiere($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CategorieMatiere');
		
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
			$this->addJoinObject($join, 'CategorieMatiere');
		}
		
		return $this;
	}

	/**
	 * Use the CategorieMatiere relation CategorieMatiere object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery A secondary query class using the current class as primary query
	 */
	public function useCategorieMatiereQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCategorieMatiere($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CategorieMatiere', 'CategorieMatiereQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe $classe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		return $this
			->addUsingAlias(JCategoriesMatieresClassesPeer::CLASSE_ID, $classe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useClasseQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JCategoriesMatieresClasses $jCategoriesMatieresClasses Object to remove from the list of results
	 *
	 * @return    JCategoriesMatieresClassesQuery The current query, for fluid interface
	 */
	public function prune($jCategoriesMatieresClasses = null)
	{
		if ($jCategoriesMatieresClasses) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JCategoriesMatieresClassesPeer::CATEGORIE_ID), $jCategoriesMatieresClasses->getCategorieId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JCategoriesMatieresClassesPeer::CLASSE_ID), $jCategoriesMatieresClasses->getClasseId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJCategoriesMatieresClassesQuery
