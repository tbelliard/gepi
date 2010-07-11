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
 * @method     CategorieMatiereQuery leftJoinJGroupesClasses($relationAlias = '') Adds a LEFT JOIN clause to the query using the JGroupesClasses relation
 * @method     CategorieMatiereQuery rightJoinJGroupesClasses($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JGroupesClasses relation
 * @method     CategorieMatiereQuery innerJoinJGroupesClasses($relationAlias = '') Adds a INNER JOIN clause to the query using the JGroupesClasses relation
 *
 * @method     CategorieMatiereQuery leftJoinMatiere($relationAlias = '') Adds a LEFT JOIN clause to the query using the Matiere relation
 * @method     CategorieMatiereQuery rightJoinMatiere($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Matiere relation
 * @method     CategorieMatiereQuery innerJoinMatiere($relationAlias = '') Adds a INNER JOIN clause to the query using the Matiere relation
 *
 * @method     CategorieMatiereQuery leftJoinJCategoriesMatieresClasses($relationAlias = '') Adds a LEFT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     CategorieMatiereQuery rightJoinJCategoriesMatieresClasses($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     CategorieMatiereQuery innerJoinJCategoriesMatieresClasses($relationAlias = '') Adds a INNER JOIN clause to the query using the JCategoriesMatieresClasses relation
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
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CategorieMatiere|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CategorieMatierePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(12, 56, 832), $con);
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
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $nomCourt The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $nomComplet The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     int|array $priority The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * Filter the query by a related JGroupesClasses object
	 *
	 * @param     JGroupesClasses $jGroupesClasses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function filterByJGroupesClasses($jGroupesClasses, $comparison = null)
	{
		return $this
			->addUsingAlias(CategorieMatierePeer::ID, $jGroupesClasses->getCategorieId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JGroupesClasses relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function joinJGroupesClasses($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JGroupesClasses');
		
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
			$this->addJoinObject($join, 'JGroupesClasses');
		}
		
		return $this;
	}

	/**
	 * Use the JGroupesClasses relation JGroupesClasses object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesClassesQuery A secondary query class using the current class as primary query
	 */
	public function useJGroupesClassesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJGroupesClasses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JGroupesClasses', 'JGroupesClassesQuery');
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
		return $this
			->addUsingAlias(CategorieMatierePeer::ID, $matiere->getCategorieId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Matiere relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function joinMatiere($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useMatiereQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
		return $this
			->addUsingAlias(CategorieMatierePeer::ID, $jCategoriesMatieresClasses->getCategorieId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JCategoriesMatieresClasses relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery The current query, for fluid interface
	 */
	public function joinJCategoriesMatieresClasses($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useJCategoriesMatieresClassesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
