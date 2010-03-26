<?php


/**
 * Base class that represents a query for the 'j_eleves_groupes' table.
 *
 * Table de jointure entre les eleves et leurs enseignements (groupes)
 *
 * @method     JEleveGroupeQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     JEleveGroupeQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     JEleveGroupeQuery orderByPeriode($order = Criteria::ASC) Order by the periode column
 *
 * @method     JEleveGroupeQuery groupByLogin() Group by the login column
 * @method     JEleveGroupeQuery groupByIdGroupe() Group by the id_groupe column
 * @method     JEleveGroupeQuery groupByPeriode() Group by the periode column
 *
 * @method     JEleveGroupeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JEleveGroupeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JEleveGroupeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JEleveGroupeQuery leftJoinEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     JEleveGroupeQuery rightJoinEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     JEleveGroupeQuery innerJoinEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     JEleveGroupeQuery leftJoinGroupe($relationAlias = '') Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     JEleveGroupeQuery rightJoinGroupe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     JEleveGroupeQuery innerJoinGroupe($relationAlias = '') Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     JEleveGroupe findOne(PropelPDO $con = null) Return the first JEleveGroupe matching the query
 * @method     JEleveGroupe findOneByLogin(string $login) Return the first JEleveGroupe filtered by the login column
 * @method     JEleveGroupe findOneByIdGroupe(int $id_groupe) Return the first JEleveGroupe filtered by the id_groupe column
 * @method     JEleveGroupe findOneByPeriode(int $periode) Return the first JEleveGroupe filtered by the periode column
 *
 * @method     array findByLogin(string $login) Return JEleveGroupe objects filtered by the login column
 * @method     array findByIdGroupe(int $id_groupe) Return JEleveGroupe objects filtered by the id_groupe column
 * @method     array findByPeriode(int $periode) Return JEleveGroupe objects filtered by the periode column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJEleveGroupeQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJEleveGroupeQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JEleveGroupe', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JEleveGroupeQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JEleveGroupeQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JEleveGroupeQuery) {
			return $criteria;
		}
		$query = new JEleveGroupeQuery();
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
	 * $obj = $c->findPk(array(34, 634), $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JEleveGroupePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
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
	 * @return    the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JEleveGroupePeer::LOGIN, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JEleveGroupePeer::ID_GROUPE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(JEleveGroupePeer::PERIODE, $key[2], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JEleveGroupePeer::LOGIN, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JEleveGroupePeer::ID_GROUPE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(JEleveGroupePeer::PERIODE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
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
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($login)) {
			return $this->addUsingAlias(JEleveGroupePeer::LOGIN, $login, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $login)) {
			return $this->addUsingAlias(JEleveGroupePeer::LOGIN, str_replace('*', '%', $login), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(JEleveGroupePeer::LOGIN, $login, $comparison);
		}
	}

	/**
	 * Filter the query on the id_groupe column
	 * 
	 * @param     int|array $idGroupe The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idGroupe)) {
			return $this->addUsingAlias(JEleveGroupePeer::ID_GROUPE, $idGroupe, Criteria::IN);
		} else {
			return $this->addUsingAlias(JEleveGroupePeer::ID_GROUPE, $idGroupe, $comparison);
		}
	}

	/**
	 * Filter the query on the periode column
	 * 
	 * @param     int|array $periode The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByPeriode($periode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($periode)) {
			return $this->addUsingAlias(JEleveGroupePeer::PERIODE, $periode, Criteria::IN);
		} else {
			return $this->addUsingAlias(JEleveGroupePeer::PERIODE, $periode, $comparison);
		}
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JEleveGroupePeer::LOGIN, $eleve->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JEleveGroupePeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groupe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Groupe');
		}
		
		return $this;
	}

	/**
	 * Use the Groupe relation Groupe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    GroupeQuery A secondary query class using the current class as primary query
	 */
	public function useGroupeQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JEleveGroupe $jEleveGroupe Object to remove from the list of results
	 *
	 * @return    JEleveGroupeQuery The current query, for fluid interface
	 */
	public function prune($jEleveGroupe = null)
	{
		if ($jEleveGroupe) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JEleveGroupePeer::LOGIN), $jEleveGroupe->getLogin(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JEleveGroupePeer::ID_GROUPE), $jEleveGroupe->getIdGroupe(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(JEleveGroupePeer::PERIODE), $jEleveGroupe->getPeriode(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every SELECT statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreSelect(PropelPDO $con)
	{
		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		return $this->preDelete($con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreUpdate(&$values, PropelPDO $con)
	{
		return $this->preUpdate($values, $con);
	}

} // BaseJEleveGroupeQuery
