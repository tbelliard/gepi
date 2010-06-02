<?php



/**
 * Base class that represents a query for the 'j_traitements_envois' table.
 *
 * Table de jointure entre le traitement des absences et leur envoi
 *
 * @method     JTraitementEnvoiEleveQuery orderByAEnvoiId($order = Criteria::ASC) Order by the a_envoi_id column
 * @method     JTraitementEnvoiEleveQuery orderByATraitementId($order = Criteria::ASC) Order by the a_traitement_id column
 *
 * @method     JTraitementEnvoiEleveQuery groupByAEnvoiId() Group by the a_envoi_id column
 * @method     JTraitementEnvoiEleveQuery groupByATraitementId() Group by the a_traitement_id column
 *
 * @method     JTraitementEnvoiEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JTraitementEnvoiEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JTraitementEnvoiEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JTraitementEnvoiEleveQuery leftJoinAbsenceEleveEnvoi($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     JTraitementEnvoiEleveQuery rightJoinAbsenceEleveEnvoi($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     JTraitementEnvoiEleveQuery innerJoinAbsenceEleveEnvoi($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveEnvoi relation
 *
 * @method     JTraitementEnvoiEleveQuery leftJoinAbsenceEleveTraitement($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     JTraitementEnvoiEleveQuery rightJoinAbsenceEleveTraitement($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     JTraitementEnvoiEleveQuery innerJoinAbsenceEleveTraitement($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     JTraitementEnvoiEleve findOne(PropelPDO $con = null) Return the first JTraitementEnvoiEleve matching the query
 * @method     JTraitementEnvoiEleve findOneByAEnvoiId(int $a_envoi_id) Return the first JTraitementEnvoiEleve filtered by the a_envoi_id column
 * @method     JTraitementEnvoiEleve findOneByATraitementId(int $a_traitement_id) Return the first JTraitementEnvoiEleve filtered by the a_traitement_id column
 *
 * @method     array findByAEnvoiId(int $a_envoi_id) Return JTraitementEnvoiEleve objects filtered by the a_envoi_id column
 * @method     array findByATraitementId(int $a_traitement_id) Return JTraitementEnvoiEleve objects filtered by the a_traitement_id column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJTraitementEnvoiEleveQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJTraitementEnvoiEleveQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JTraitementEnvoiEleve', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JTraitementEnvoiEleveQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JTraitementEnvoiEleveQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JTraitementEnvoiEleveQuery) {
			return $criteria;
		}
		$query = new JTraitementEnvoiEleveQuery();
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
	 * @param     array[$a_envoi_id, $a_traitement_id] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JTraitementEnvoiEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JTraitementEnvoiElevePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JTraitementEnvoiElevePeer::A_ENVOI_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JTraitementEnvoiElevePeer::A_ENVOI_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the a_envoi_id column
	 * 
	 * @param     int|array $aEnvoiId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByAEnvoiId($aEnvoiId = null, $comparison = null)
	{
		if (is_array($aEnvoiId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JTraitementEnvoiElevePeer::A_ENVOI_ID, $aEnvoiId, $comparison);
	}

	/**
	 * Filter the query on the a_traitement_id column
	 * 
	 * @param     int|array $aTraitementId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByATraitementId($aTraitementId = null, $comparison = null)
	{
		if (is_array($aTraitementId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $aTraitementId, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveEnvoi object
	 *
	 * @param     AbsenceEleveEnvoi $absenceEleveEnvoi  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveEnvoi($absenceEleveEnvoi, $comparison = null)
	{
		return $this
			->addUsingAlias(JTraitementEnvoiElevePeer::A_ENVOI_ID, $absenceEleveEnvoi->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveEnvoi relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveEnvoi($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveEnvoi');
		
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
			$this->addJoinObject($join, 'AbsenceEleveEnvoi');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveEnvoi relation AbsenceEleveEnvoi object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveEnvoiQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveEnvoi($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveEnvoi', 'AbsenceEleveEnvoiQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = null)
	{
		return $this
			->addUsingAlias(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $absenceEleveTraitement->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTraitement');
		
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
			$this->addJoinObject($join, 'AbsenceEleveTraitement');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveTraitement relation AbsenceEleveTraitement object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTraitementQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JTraitementEnvoiEleve $jTraitementEnvoiEleve Object to remove from the list of results
	 *
	 * @return    JTraitementEnvoiEleveQuery The current query, for fluid interface
	 */
	public function prune($jTraitementEnvoiEleve = null)
	{
		if ($jTraitementEnvoiEleve) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JTraitementEnvoiElevePeer::A_ENVOI_ID), $jTraitementEnvoiEleve->getAEnvoiId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID), $jTraitementEnvoiEleve->getATraitementId(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJTraitementEnvoiEleveQuery
