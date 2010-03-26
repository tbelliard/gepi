<?php


/**
 * Base class that represents a query for the 'j_aid_utilisateurs' table.
 *
 * Table de liaison entre les AID et les utilisateurs professionnels
 *
 * @method     JAidUtilisateursProfessionnelsQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     JAidUtilisateursProfessionnelsQuery orderByIdUtilisateur($order = Criteria::ASC) Order by the id_utilisateur column
 * @method     JAidUtilisateursProfessionnelsQuery orderByIndiceAid($order = Criteria::ASC) Order by the indice_aid column
 *
 * @method     JAidUtilisateursProfessionnelsQuery groupByIdAid() Group by the id_aid column
 * @method     JAidUtilisateursProfessionnelsQuery groupByIdUtilisateur() Group by the id_utilisateur column
 * @method     JAidUtilisateursProfessionnelsQuery groupByIndiceAid() Group by the indice_aid column
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JAidUtilisateursProfessionnelsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JAidUtilisateursProfessionnelsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoinAidDetails($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     JAidUtilisateursProfessionnelsQuery rightJoinAidDetails($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     JAidUtilisateursProfessionnelsQuery innerJoinAidDetails($relationAlias = '') Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JAidUtilisateursProfessionnelsQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JAidUtilisateursProfessionnelsQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoinAidConfiguration($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidConfiguration relation
 * @method     JAidUtilisateursProfessionnelsQuery rightJoinAidConfiguration($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidConfiguration relation
 * @method     JAidUtilisateursProfessionnelsQuery innerJoinAidConfiguration($relationAlias = '') Adds a INNER JOIN clause to the query using the AidConfiguration relation
 *
 * @method     JAidUtilisateursProfessionnels findOne(PropelPDO $con = null) Return the first JAidUtilisateursProfessionnels matching the query
 * @method     JAidUtilisateursProfessionnels findOneByIdAid(string $id_aid) Return the first JAidUtilisateursProfessionnels filtered by the id_aid column
 * @method     JAidUtilisateursProfessionnels findOneByIdUtilisateur(string $id_utilisateur) Return the first JAidUtilisateursProfessionnels filtered by the id_utilisateur column
 * @method     JAidUtilisateursProfessionnels findOneByIndiceAid(int $indice_aid) Return the first JAidUtilisateursProfessionnels filtered by the indice_aid column
 *
 * @method     array findByIdAid(string $id_aid) Return JAidUtilisateursProfessionnels objects filtered by the id_aid column
 * @method     array findByIdUtilisateur(string $id_utilisateur) Return JAidUtilisateursProfessionnels objects filtered by the id_utilisateur column
 * @method     array findByIndiceAid(int $indice_aid) Return JAidUtilisateursProfessionnels objects filtered by the indice_aid column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJAidUtilisateursProfessionnelsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJAidUtilisateursProfessionnelsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JAidUtilisateursProfessionnels', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JAidUtilisateursProfessionnelsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JAidUtilisateursProfessionnelsQuery) {
			return $criteria;
		}
		$query = new JAidUtilisateursProfessionnelsQuery();
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
		if ((null !== ($obj = JAidUtilisateursProfessionnelsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JAidUtilisateursProfessionnelsPeer::ID_AID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $key[1], Criteria::EQUAL);
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
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByIdAid($idAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idAid)) {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $idAid, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idAid)) {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, str_replace('*', '%', $idAid), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $idAid, $comparison);
		}
	}

	/**
	 * Filter the query on the id_utilisateur column
	 * 
	 * @param     string $idUtilisateur The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByIdUtilisateur($idUtilisateur = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idUtilisateur)) {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $idUtilisateur, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idUtilisateur)) {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, str_replace('*', '%', $idUtilisateur), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $idUtilisateur, $comparison);
		}
	}

	/**
	 * Filter the query on the indice_aid column
	 * 
	 * @param     int|array $indiceAid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByIndiceAid($indiceAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($indiceAid)) {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $indiceAid, Criteria::IN);
		} else {
			return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $indiceAid, $comparison);
		}
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $aidDetails->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
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
	 * Filter the query by a related AidConfiguration object
	 *
	 * @param     AidConfiguration $aidConfiguration  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByAidConfiguration($aidConfiguration, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $aidConfiguration->getIndiceAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidConfiguration relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
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
	 * @param     JAidUtilisateursProfessionnels $jAidUtilisateursProfessionnels Object to remove from the list of results
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function prune($jAidUtilisateursProfessionnels = null)
	{
		if ($jAidUtilisateursProfessionnels) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JAidUtilisateursProfessionnelsPeer::ID_AID), $jAidUtilisateursProfessionnels->getIdAid(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JAidUtilisateursProfessionnelsPeer::INDICE_AID), $jAidUtilisateursProfessionnels->getIndiceAid(), Criteria::NOT_EQUAL);
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

} // BaseJAidUtilisateursProfessionnelsQuery
