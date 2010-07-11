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
 * @method     JScolClassesQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JScolClassesQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JScolClassesQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     JScolClassesQuery leftJoinClasse($relationAlias = '') Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JScolClassesQuery rightJoinClasse($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JScolClassesQuery innerJoinClasse($relationAlias = '') Adds a INNER JOIN clause to the query using the Classe relation
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
	 * Find object by primary key
	 * <code>
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 * @param     array[$login, $id_classe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JScolClasses|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JScolClassesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     int|array $idClasse The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		return $this
			->addUsingAlias(JScolClassesPeer::LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useUtilisateurProfessionnelQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe $classe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		return $this
			->addUsingAlias(JScolClassesPeer::ID_CLASSE, $classe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JScolClassesQuery The current query, for fluid interface
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
