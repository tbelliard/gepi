<?php


/**
 * Base class that represents a query for the 'j_aid_utilisateurs' table.
 *
 * Table de liaison entre les AID et les utilisateurs professionnels
 *
 * @method     JAidUtilisateursProfessionnelsQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     JAidUtilisateursProfessionnelsQuery orderByIdUtilisateur($order = Criteria::ASC) Order by the id_utilisateur column
 *
 * @method     JAidUtilisateursProfessionnelsQuery groupByIdAid() Group by the id_aid column
 * @method     JAidUtilisateursProfessionnelsQuery groupByIdUtilisateur() Group by the id_utilisateur column
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JAidUtilisateursProfessionnelsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JAidUtilisateursProfessionnelsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoinAidDetails($relationAlias = null) Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     JAidUtilisateursProfessionnelsQuery rightJoinAidDetails($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     JAidUtilisateursProfessionnelsQuery innerJoinAidDetails($relationAlias = null) Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     JAidUtilisateursProfessionnelsQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JAidUtilisateursProfessionnelsQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JAidUtilisateursProfessionnelsQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     JAidUtilisateursProfessionnels findOne(PropelPDO $con = null) Return the first JAidUtilisateursProfessionnels matching the query
 * @method     JAidUtilisateursProfessionnels findOneOrCreate(PropelPDO $con = null) Return the first JAidUtilisateursProfessionnels matching the query, or a new JAidUtilisateursProfessionnels object populated from the query conditions when no match is found
 *
 * @method     JAidUtilisateursProfessionnels findOneByIdAid(string $id_aid) Return the first JAidUtilisateursProfessionnels filtered by the id_aid column
 * @method     JAidUtilisateursProfessionnels findOneByIdUtilisateur(string $id_utilisateur) Return the first JAidUtilisateursProfessionnels filtered by the id_utilisateur column
 *
 * @method     array findByIdAid(string $id_aid) Return JAidUtilisateursProfessionnels objects filtered by the id_aid column
 * @method     array findByIdUtilisateur(string $id_utilisateur) Return JAidUtilisateursProfessionnels objects filtered by the id_utilisateur column
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
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 * @param     array[$id_aid, $id_utilisateur] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JAidUtilisateursProfessionnels|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JAidUtilisateursProfessionnelsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $key[1], Criteria::EQUAL);
		
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
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JAidUtilisateursProfessionnelsPeer::ID_AID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $key[1], Criteria::EQUAL);
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
	public function filterByIdAid($idAid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idAid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idAid)) {
				$idAid = str_replace('*', '%', $idAid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_AID, $idAid, $comparison);
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
	public function filterByIdUtilisateur($idUtilisateur = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idUtilisateur)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idUtilisateur)) {
				$idUtilisateur = str_replace('*', '%', $idUtilisateur);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $idUtilisateur, $comparison);
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = null)
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
	public function joinAidDetails($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidDetails');
		
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
	public function useAidDetailsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
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
	 * @param     JAidUtilisateursProfessionnels $jAidUtilisateursProfessionnels Object to remove from the list of results
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery The current query, for fluid interface
	 */
	public function prune($jAidUtilisateursProfessionnels = null)
	{
		if ($jAidUtilisateursProfessionnels) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JAidUtilisateursProfessionnelsPeer::ID_AID), $jAidUtilisateursProfessionnels->getIdAid(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR), $jAidUtilisateursProfessionnels->getIdUtilisateur(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJAidUtilisateursProfessionnelsQuery
