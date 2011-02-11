<?php


/**
 * Base class that represents a query for the 'mef' table.
 *
 * Module élémentaire de formation
 *
 * @method     MefQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     MefQuery orderByExtId($order = Criteria::ASC) Order by the ext_id column
 * @method     MefQuery orderByLibelleCourt($order = Criteria::ASC) Order by the libelle_court column
 * @method     MefQuery orderByLibelleLong($order = Criteria::ASC) Order by the libelle_long column
 * @method     MefQuery orderByLibelleEdition($order = Criteria::ASC) Order by the libelle_edition column
 *
 * @method     MefQuery groupById() Group by the id column
 * @method     MefQuery groupByExtId() Group by the ext_id column
 * @method     MefQuery groupByLibelleCourt() Group by the libelle_court column
 * @method     MefQuery groupByLibelleLong() Group by the libelle_long column
 * @method     MefQuery groupByLibelleEdition() Group by the libelle_edition column
 *
 * @method     MefQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     MefQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     MefQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     MefQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     MefQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     MefQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     Mef findOne(PropelPDO $con = null) Return the first Mef matching the query
 * @method     Mef findOneOrCreate(PropelPDO $con = null) Return the first Mef matching the query, or a new Mef object populated from the query conditions when no match is found
 *
 * @method     Mef findOneById(int $id) Return the first Mef filtered by the id column
 * @method     Mef findOneByExtId(int $ext_id) Return the first Mef filtered by the ext_id column
 * @method     Mef findOneByLibelleCourt(string $libelle_court) Return the first Mef filtered by the libelle_court column
 * @method     Mef findOneByLibelleLong(string $libelle_long) Return the first Mef filtered by the libelle_long column
 * @method     Mef findOneByLibelleEdition(string $libelle_edition) Return the first Mef filtered by the libelle_edition column
 *
 * @method     array findById(int $id) Return Mef objects filtered by the id column
 * @method     array findByExtId(int $ext_id) Return Mef objects filtered by the ext_id column
 * @method     array findByLibelleCourt(string $libelle_court) Return Mef objects filtered by the libelle_court column
 * @method     array findByLibelleLong(string $libelle_long) Return Mef objects filtered by the libelle_long column
 * @method     array findByLibelleEdition(string $libelle_edition) Return Mef objects filtered by the libelle_edition column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseMefQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseMefQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Mef', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new MefQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    MefQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof MefQuery) {
			return $criteria;
		}
		$query = new MefQuery();
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
	 * @return    Mef|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = MefPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(MefPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(MefPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(MefPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the ext_id column
	 * 
	 * @param     int|array $extId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByExtId($extId = null, $comparison = null)
	{
		if (is_array($extId)) {
			$useMinMax = false;
			if (isset($extId['min'])) {
				$this->addUsingAlias(MefPeer::EXT_ID, $extId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($extId['max'])) {
				$this->addUsingAlias(MefPeer::EXT_ID, $extId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(MefPeer::EXT_ID, $extId, $comparison);
	}

	/**
	 * Filter the query on the libelle_court column
	 * 
	 * @param     string $libelleCourt The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleCourt($libelleCourt = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleCourt)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleCourt)) {
				$libelleCourt = str_replace('*', '%', $libelleCourt);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_COURT, $libelleCourt, $comparison);
	}

	/**
	 * Filter the query on the libelle_long column
	 * 
	 * @param     string $libelleLong The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleLong($libelleLong = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleLong)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleLong)) {
				$libelleLong = str_replace('*', '%', $libelleLong);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_LONG, $libelleLong, $comparison);
	}

	/**
	 * Filter the query on the libelle_edition column
	 * 
	 * @param     string $libelleEdition The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByLibelleEdition($libelleEdition = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($libelleEdition)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $libelleEdition)) {
				$libelleEdition = str_replace('*', '%', $libelleEdition);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MefPeer::LIBELLE_EDITION, $libelleEdition, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		return $this
			->addUsingAlias(MefPeer::ID, $eleve->getIdMef(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');
		
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
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Mef $mef Object to remove from the list of results
	 *
	 * @return    MefQuery The current query, for fluid interface
	 */
	public function prune($mef = null)
	{
		if ($mef) {
			$this->addUsingAlias(MefPeer::ID, $mef->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseMefQuery
