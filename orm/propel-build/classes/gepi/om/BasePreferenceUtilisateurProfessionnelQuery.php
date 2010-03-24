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
 * @method     PreferenceUtilisateurProfessionnelQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     PreferenceUtilisateurProfessionnelQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     PreferenceUtilisateurProfessionnelQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     PreferenceUtilisateurProfessionnel findOne(PropelPDO $con = null) Return the first PreferenceUtilisateurProfessionnel matching the query
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
		if ((null !== ($obj = PreferenceUtilisateurProfessionnelPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			return $this
				->filterByPrimaryKey($key)
				->findOne($con);
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
	 * @param     string $name The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByName($name = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($name)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::NAME, $name, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $name)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::NAME, str_replace('*', '%', $name), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::NAME, $name, $comparison);
		}
	}

	/**
	 * Filter the query on the value column
	 * 
	 * @param     string $value The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByValue($value = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($value)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::VALUE, $value, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $value)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::VALUE, str_replace('*', '%', $value), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::VALUE, $value, $comparison);
		}
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($login)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $login, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $login)) {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, str_replace('*', '%', $login), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $login, $comparison);
		}
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(PreferenceUtilisateurProfessionnelPeer::LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('UtilisateurProfessionnel');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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

} // BasePreferenceUtilisateurProfessionnelQuery
