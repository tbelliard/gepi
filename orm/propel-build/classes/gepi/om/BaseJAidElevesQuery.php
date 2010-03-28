<?php


/**
 * Base class that represents a query for the 'j_aid_eleves' table.
 *
 * Table de liaison entre les AID et les eleves qui en sont membres
 *
 * @method     JAidElevesQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     JAidElevesQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     JAidElevesQuery orderByIndiceAid($order = Criteria::ASC) Order by the indice_aid column
 *
 * @method     JAidElevesQuery groupByIdAid() Group by the id_aid column
 * @method     JAidElevesQuery groupByLogin() Group by the login column
 * @method     JAidElevesQuery groupByIndiceAid() Group by the indice_aid column
 *
 * @method     JAidElevesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JAidElevesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JAidElevesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JAidElevesQuery leftJoinAidDetails($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     JAidElevesQuery rightJoinAidDetails($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     JAidElevesQuery innerJoinAidDetails($relationAlias = '') Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     JAidElevesQuery leftJoinEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     JAidElevesQuery rightJoinEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     JAidElevesQuery innerJoinEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     JAidElevesQuery leftJoinAidConfiguration($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidConfiguration relation
 * @method     JAidElevesQuery rightJoinAidConfiguration($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidConfiguration relation
 * @method     JAidElevesQuery innerJoinAidConfiguration($relationAlias = '') Adds a INNER JOIN clause to the query using the AidConfiguration relation
 *
 * @method     JAidEleves findOne(PropelPDO $con = null) Return the first JAidEleves matching the query
 * @method     JAidEleves findOneByIdAid(string $id_aid) Return the first JAidEleves filtered by the id_aid column
 * @method     JAidEleves findOneByLogin(string $login) Return the first JAidEleves filtered by the login column
 * @method     JAidEleves findOneByIndiceAid(int $indice_aid) Return the first JAidEleves filtered by the indice_aid column
 *
 * @method     array findByIdAid(string $id_aid) Return JAidEleves objects filtered by the id_aid column
 * @method     array findByLogin(string $login) Return JAidEleves objects filtered by the login column
 * @method     array findByIndiceAid(int $indice_aid) Return JAidEleves objects filtered by the indice_aid column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJAidElevesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJAidElevesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JAidEleves', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JAidElevesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JAidElevesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JAidElevesQuery) {
			return $criteria;
		}
		$query = new JAidElevesQuery();
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
	 * @param     array[$login, $indice_aid] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JAidEleves|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JAidElevesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
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
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JAidElevesPeer::LOGIN, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JAidElevesPeer::INDICE_AID, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JAidElevesPeer::LOGIN, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JAidElevesPeer::INDICE_AID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the id_aid column
	 * 
	 * @param     string $idAid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByIdAid($idAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idAid)) {
			return $this->addUsingAlias(JAidElevesPeer::ID_AID, $idAid, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idAid)) {
			return $this->addUsingAlias(JAidElevesPeer::ID_AID, str_replace('*', '%', $idAid), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(JAidElevesPeer::ID_AID, $idAid, $comparison);
		}
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($login)) {
			return $this->addUsingAlias(JAidElevesPeer::LOGIN, $login, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $login)) {
			return $this->addUsingAlias(JAidElevesPeer::LOGIN, str_replace('*', '%', $login), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(JAidElevesPeer::LOGIN, $login, $comparison);
		}
	}

	/**
	 * Filter the query on the indice_aid column
	 * 
	 * @param     int|array $indiceAid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByIndiceAid($indiceAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($indiceAid)) {
			return $this->addUsingAlias(JAidElevesPeer::INDICE_AID, $indiceAid, Criteria::IN);
		} else {
			return $this->addUsingAlias(JAidElevesPeer::INDICE_AID, $indiceAid, $comparison);
		}
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidElevesPeer::ID_AID, $aidDetails->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function joinAidDetails($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidDetails');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AidDetails');
		}
		
		return $this;
	}

	/**
	 * Use the AidDetails relation AidDetails object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery A secondary query class using the current class as primary query
	 */
	public function useAidDetailsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAidDetails($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidDetails', 'AidDetailsQuery');
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidElevesPeer::LOGIN, $eleve->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
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
	 * Filter the query by a related AidConfiguration object
	 *
	 * @param     AidConfiguration $aidConfiguration  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function filterByAidConfiguration($aidConfiguration, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidElevesPeer::INDICE_AID, $aidConfiguration->getIndiceAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidConfiguration relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function joinAidConfiguration($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidConfiguration');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AidConfiguration');
		}
		
		return $this;
	}

	/**
	 * Use the AidConfiguration relation AidConfiguration object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidConfigurationQuery A secondary query class using the current class as primary query
	 */
	public function useAidConfigurationQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAidConfiguration($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidConfiguration', 'AidConfigurationQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JAidEleves $jAidEleves Object to remove from the list of results
	 *
	 * @return    JAidElevesQuery The current query, for fluid interface
	 */
	public function prune($jAidEleves = null)
	{
		if ($jAidEleves) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JAidElevesPeer::LOGIN), $jAidEleves->getLogin(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JAidElevesPeer::INDICE_AID), $jAidEleves->getIndiceAid(), Criteria::NOT_EQUAL);
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

} // BaseJAidElevesQuery
