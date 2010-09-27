<?php


/**
 * Base class that represents a query for the 'a_types_statut' table.
 *
 * Liste des statuts autorises à saisir des types d'absences
 *
 * @method     AbsenceEleveTypeStatutAutoriseQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveTypeStatutAutoriseQuery orderByIdAType($order = Criteria::ASC) Order by the id_a_type column
 * @method     AbsenceEleveTypeStatutAutoriseQuery orderByStatut($order = Criteria::ASC) Order by the statut column
 *
 * @method     AbsenceEleveTypeStatutAutoriseQuery groupById() Group by the id column
 * @method     AbsenceEleveTypeStatutAutoriseQuery groupByIdAType() Group by the id_a_type column
 * @method     AbsenceEleveTypeStatutAutoriseQuery groupByStatut() Group by the statut column
 *
 * @method     AbsenceEleveTypeStatutAutoriseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveTypeStatutAutoriseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveTypeStatutAutoriseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveTypeStatutAutoriseQuery leftJoinAbsenceEleveType($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveTypeStatutAutoriseQuery rightJoinAbsenceEleveType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveTypeStatutAutoriseQuery innerJoinAbsenceEleveType($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveType relation
 *
 * @method     AbsenceEleveTypeStatutAutorise findOne(PropelPDO $con = null) Return the first AbsenceEleveTypeStatutAutorise matching the query
 * @method     AbsenceEleveTypeStatutAutorise findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveTypeStatutAutorise matching the query, or a new AbsenceEleveTypeStatutAutorise object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveTypeStatutAutorise findOneById(int $id) Return the first AbsenceEleveTypeStatutAutorise filtered by the id column
 * @method     AbsenceEleveTypeStatutAutorise findOneByIdAType(int $id_a_type) Return the first AbsenceEleveTypeStatutAutorise filtered by the id_a_type column
 * @method     AbsenceEleveTypeStatutAutorise findOneByStatut(string $statut) Return the first AbsenceEleveTypeStatutAutorise filtered by the statut column
 *
 * @method     array findById(int $id) Return AbsenceEleveTypeStatutAutorise objects filtered by the id column
 * @method     array findByIdAType(int $id_a_type) Return AbsenceEleveTypeStatutAutorise objects filtered by the id_a_type column
 * @method     array findByStatut(string $statut) Return AbsenceEleveTypeStatutAutorise objects filtered by the statut column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTypeStatutAutoriseQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveTypeStatutAutoriseQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveTypeStatutAutorise', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveTypeStatutAutoriseQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveTypeStatutAutoriseQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveTypeStatutAutoriseQuery();
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
	 * @return    AbsenceEleveTypeStatutAutorise|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveTypeStatutAutorisePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the id_a_type column
	 * 
	 * @param     int|array $idAType The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterByIdAType($idAType = null, $comparison = null)
	{
		if (is_array($idAType)) {
			$useMinMax = false;
			if (isset($idAType['min'])) {
				$this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID_A_TYPE, $idAType['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idAType['max'])) {
				$this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID_A_TYPE, $idAType['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID_A_TYPE, $idAType, $comparison);
	}

	/**
	 * Filter the query on the statut column
	 * 
	 * @param     string $statut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterByStatut($statut = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($statut)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $statut)) {
				$statut = str_replace('*', '%', $statut);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::STATUT, $statut, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveType object
	 *
	 * @param     AbsenceEleveType $absenceEleveType  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveType($absenceEleveType, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID_A_TYPE, $absenceEleveType->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveType relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveType');
		
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
			$this->addJoinObject($join, 'AbsenceEleveType');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveType relation AbsenceEleveType object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveType($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveType', 'AbsenceEleveTypeQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveTypeStatutAutorise $absenceEleveTypeStatutAutorise Object to remove from the list of results
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveTypeStatutAutorise = null)
	{
		if ($absenceEleveTypeStatutAutorise) {
			$this->addUsingAlias(AbsenceEleveTypeStatutAutorisePeer::ID, $absenceEleveTypeStatutAutorise->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseAbsenceEleveTypeStatutAutoriseQuery
