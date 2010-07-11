<?php


/**
 * Base class that represents a query for the 'resp_adr' table.
 *
 * Table de jointure entre les responsables legaux et leur adresse
 *
 * @method     ResponsableEleveAdresseQuery orderByAdrId($order = Criteria::ASC) Order by the adr_id column
 * @method     ResponsableEleveAdresseQuery orderByAdr1($order = Criteria::ASC) Order by the adr1 column
 * @method     ResponsableEleveAdresseQuery orderByAdr2($order = Criteria::ASC) Order by the adr2 column
 * @method     ResponsableEleveAdresseQuery orderByAdr3($order = Criteria::ASC) Order by the adr3 column
 * @method     ResponsableEleveAdresseQuery orderByAdr4($order = Criteria::ASC) Order by the adr4 column
 * @method     ResponsableEleveAdresseQuery orderByCp($order = Criteria::ASC) Order by the cp column
 * @method     ResponsableEleveAdresseQuery orderByPays($order = Criteria::ASC) Order by the pays column
 * @method     ResponsableEleveAdresseQuery orderByCommune($order = Criteria::ASC) Order by the commune column
 *
 * @method     ResponsableEleveAdresseQuery groupByAdrId() Group by the adr_id column
 * @method     ResponsableEleveAdresseQuery groupByAdr1() Group by the adr1 column
 * @method     ResponsableEleveAdresseQuery groupByAdr2() Group by the adr2 column
 * @method     ResponsableEleveAdresseQuery groupByAdr3() Group by the adr3 column
 * @method     ResponsableEleveAdresseQuery groupByAdr4() Group by the adr4 column
 * @method     ResponsableEleveAdresseQuery groupByCp() Group by the cp column
 * @method     ResponsableEleveAdresseQuery groupByPays() Group by the pays column
 * @method     ResponsableEleveAdresseQuery groupByCommune() Group by the commune column
 *
 * @method     ResponsableEleveAdresseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ResponsableEleveAdresseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ResponsableEleveAdresseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ResponsableEleveAdresseQuery leftJoinResponsableEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the ResponsableEleve relation
 * @method     ResponsableEleveAdresseQuery rightJoinResponsableEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the ResponsableEleve relation
 * @method     ResponsableEleveAdresseQuery innerJoinResponsableEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the ResponsableEleve relation
 *
 * @method     ResponsableEleveAdresseQuery leftJoinAbsenceEleveNotification($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     ResponsableEleveAdresseQuery rightJoinAbsenceEleveNotification($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     ResponsableEleveAdresseQuery innerJoinAbsenceEleveNotification($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveNotification relation
 *
 * @method     ResponsableEleveAdresse findOne(PropelPDO $con = null) Return the first ResponsableEleveAdresse matching the query
 * @method     ResponsableEleveAdresse findOneOrCreate(PropelPDO $con = null) Return the first ResponsableEleveAdresse matching the query, or a new ResponsableEleveAdresse object populated from the query conditions when no match is found
 *
 * @method     ResponsableEleveAdresse findOneByAdrId(string $adr_id) Return the first ResponsableEleveAdresse filtered by the adr_id column
 * @method     ResponsableEleveAdresse findOneByAdr1(string $adr1) Return the first ResponsableEleveAdresse filtered by the adr1 column
 * @method     ResponsableEleveAdresse findOneByAdr2(string $adr2) Return the first ResponsableEleveAdresse filtered by the adr2 column
 * @method     ResponsableEleveAdresse findOneByAdr3(string $adr3) Return the first ResponsableEleveAdresse filtered by the adr3 column
 * @method     ResponsableEleveAdresse findOneByAdr4(string $adr4) Return the first ResponsableEleveAdresse filtered by the adr4 column
 * @method     ResponsableEleveAdresse findOneByCp(string $cp) Return the first ResponsableEleveAdresse filtered by the cp column
 * @method     ResponsableEleveAdresse findOneByPays(string $pays) Return the first ResponsableEleveAdresse filtered by the pays column
 * @method     ResponsableEleveAdresse findOneByCommune(string $commune) Return the first ResponsableEleveAdresse filtered by the commune column
 *
 * @method     array findByAdrId(string $adr_id) Return ResponsableEleveAdresse objects filtered by the adr_id column
 * @method     array findByAdr1(string $adr1) Return ResponsableEleveAdresse objects filtered by the adr1 column
 * @method     array findByAdr2(string $adr2) Return ResponsableEleveAdresse objects filtered by the adr2 column
 * @method     array findByAdr3(string $adr3) Return ResponsableEleveAdresse objects filtered by the adr3 column
 * @method     array findByAdr4(string $adr4) Return ResponsableEleveAdresse objects filtered by the adr4 column
 * @method     array findByCp(string $cp) Return ResponsableEleveAdresse objects filtered by the cp column
 * @method     array findByPays(string $pays) Return ResponsableEleveAdresse objects filtered by the pays column
 * @method     array findByCommune(string $commune) Return ResponsableEleveAdresse objects filtered by the commune column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableEleveAdresseQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseResponsableEleveAdresseQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'ResponsableEleveAdresse', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ResponsableEleveAdresseQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ResponsableEleveAdresseQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ResponsableEleveAdresseQuery) {
			return $criteria;
		}
		$query = new ResponsableEleveAdresseQuery();
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
	 * @return    ResponsableEleveAdresse|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = ResponsableEleveAdressePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the adr_id column
	 * 
	 * @param     string $adrId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAdrId($adrId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adrId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adrId)) {
				$adrId = str_replace('*', '%', $adrId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $adrId, $comparison);
	}

	/**
	 * Filter the query on the adr1 column
	 * 
	 * @param     string $adr1 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAdr1($adr1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adr1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adr1)) {
				$adr1 = str_replace('*', '%', $adr1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR1, $adr1, $comparison);
	}

	/**
	 * Filter the query on the adr2 column
	 * 
	 * @param     string $adr2 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAdr2($adr2 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adr2)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adr2)) {
				$adr2 = str_replace('*', '%', $adr2);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR2, $adr2, $comparison);
	}

	/**
	 * Filter the query on the adr3 column
	 * 
	 * @param     string $adr3 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAdr3($adr3 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adr3)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adr3)) {
				$adr3 = str_replace('*', '%', $adr3);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR3, $adr3, $comparison);
	}

	/**
	 * Filter the query on the adr4 column
	 * 
	 * @param     string $adr4 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAdr4($adr4 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adr4)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adr4)) {
				$adr4 = str_replace('*', '%', $adr4);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::ADR4, $adr4, $comparison);
	}

	/**
	 * Filter the query on the cp column
	 * 
	 * @param     string $cp The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByCp($cp = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($cp)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $cp)) {
				$cp = str_replace('*', '%', $cp);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::CP, $cp, $comparison);
	}

	/**
	 * Filter the query on the pays column
	 * 
	 * @param     string $pays The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByPays($pays = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($pays)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $pays)) {
				$pays = str_replace('*', '%', $pays);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::PAYS, $pays, $comparison);
	}

	/**
	 * Filter the query on the commune column
	 * 
	 * @param     string $commune The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByCommune($commune = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($commune)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $commune)) {
				$commune = str_replace('*', '%', $commune);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableEleveAdressePeer::COMMUNE, $commune, $comparison);
	}

	/**
	 * Filter the query by a related ResponsableEleve object
	 *
	 * @param     ResponsableEleve $responsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = null)
	{
		return $this
			->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $responsableEleve->getAdrId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function joinResponsableEleve($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ResponsableEleve');
		
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
			$this->addJoinObject($join, 'ResponsableEleve');
		}
		
		return $this;
	}

	/**
	 * Use the ResponsableEleve relation ResponsableEleve object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery A secondary query class using the current class as primary query
	 */
	public function useResponsableEleveQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinResponsableEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableEleve', 'ResponsableEleveQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveNotification object
	 *
	 * @param     AbsenceEleveNotification $absenceEleveNotification  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = null)
	{
		return $this
			->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $absenceEleveNotification->getAdrId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveNotification relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveNotification($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveNotification');
		
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
			$this->addJoinObject($join, 'AbsenceEleveNotification');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveNotification relation AbsenceEleveNotification object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveNotificationQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveNotificationQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveNotification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveNotification', 'AbsenceEleveNotificationQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     ResponsableEleveAdresse $responsableEleveAdresse Object to remove from the list of results
	 *
	 * @return    ResponsableEleveAdresseQuery The current query, for fluid interface
	 */
	public function prune($responsableEleveAdresse = null)
	{
		if ($responsableEleveAdresse) {
			$this->addUsingAlias(ResponsableEleveAdressePeer::ADR_ID, $responsableEleveAdresse->getAdrId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseResponsableEleveAdresseQuery
