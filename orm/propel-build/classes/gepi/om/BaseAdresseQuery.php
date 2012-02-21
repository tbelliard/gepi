<?php


/**
 * Base class that represents a query for the 'resp_adr' table.
 *
 * Adresse
 *
 * @method     AdresseQuery orderById($order = Criteria::ASC) Order by the adr_id column
 * @method     AdresseQuery orderByAdr1($order = Criteria::ASC) Order by the adr1 column
 * @method     AdresseQuery orderByAdr2($order = Criteria::ASC) Order by the adr2 column
 * @method     AdresseQuery orderByAdr3($order = Criteria::ASC) Order by the adr3 column
 * @method     AdresseQuery orderByAdr4($order = Criteria::ASC) Order by the adr4 column
 * @method     AdresseQuery orderByCp($order = Criteria::ASC) Order by the cp column
 * @method     AdresseQuery orderByPays($order = Criteria::ASC) Order by the pays column
 * @method     AdresseQuery orderByCommune($order = Criteria::ASC) Order by the commune column
 *
 * @method     AdresseQuery groupById() Group by the adr_id column
 * @method     AdresseQuery groupByAdr1() Group by the adr1 column
 * @method     AdresseQuery groupByAdr2() Group by the adr2 column
 * @method     AdresseQuery groupByAdr3() Group by the adr3 column
 * @method     AdresseQuery groupByAdr4() Group by the adr4 column
 * @method     AdresseQuery groupByCp() Group by the cp column
 * @method     AdresseQuery groupByPays() Group by the pays column
 * @method     AdresseQuery groupByCommune() Group by the commune column
 *
 * @method     AdresseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AdresseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AdresseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AdresseQuery leftJoinResponsableEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableEleve relation
 * @method     AdresseQuery rightJoinResponsableEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableEleve relation
 * @method     AdresseQuery innerJoinResponsableEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableEleve relation
 *
 * @method     AdresseQuery leftJoinAbsenceEleveNotification($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     AdresseQuery rightJoinAbsenceEleveNotification($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     AdresseQuery innerJoinAbsenceEleveNotification($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveNotification relation
 *
 * @method     Adresse findOne(PropelPDO $con = null) Return the first Adresse matching the query
 * @method     Adresse findOneOrCreate(PropelPDO $con = null) Return the first Adresse matching the query, or a new Adresse object populated from the query conditions when no match is found
 *
 * @method     Adresse findOneById(string $adr_id) Return the first Adresse filtered by the adr_id column
 * @method     Adresse findOneByAdr1(string $adr1) Return the first Adresse filtered by the adr1 column
 * @method     Adresse findOneByAdr2(string $adr2) Return the first Adresse filtered by the adr2 column
 * @method     Adresse findOneByAdr3(string $adr3) Return the first Adresse filtered by the adr3 column
 * @method     Adresse findOneByAdr4(string $adr4) Return the first Adresse filtered by the adr4 column
 * @method     Adresse findOneByCp(string $cp) Return the first Adresse filtered by the cp column
 * @method     Adresse findOneByPays(string $pays) Return the first Adresse filtered by the pays column
 * @method     Adresse findOneByCommune(string $commune) Return the first Adresse filtered by the commune column
 *
 * @method     array findById(string $adr_id) Return Adresse objects filtered by the adr_id column
 * @method     array findByAdr1(string $adr1) Return Adresse objects filtered by the adr1 column
 * @method     array findByAdr2(string $adr2) Return Adresse objects filtered by the adr2 column
 * @method     array findByAdr3(string $adr3) Return Adresse objects filtered by the adr3 column
 * @method     array findByAdr4(string $adr4) Return Adresse objects filtered by the adr4 column
 * @method     array findByCp(string $cp) Return Adresse objects filtered by the cp column
 * @method     array findByPays(string $pays) Return Adresse objects filtered by the pays column
 * @method     array findByCommune(string $commune) Return Adresse objects filtered by the commune column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAdresseQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseAdresseQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Adresse', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AdresseQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AdresseQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AdresseQuery) {
			return $criteria;
		}
		$query = new AdresseQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key.
	 * Propel uses the instance pool to skip the database if the object exists.
	 * Go fast if the query is untouched.
	 *
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Adresse|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AdressePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AdressePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		if ($this->formatter || $this->modelAlias || $this->with || $this->select
		 || $this->selectColumns || $this->asColumns || $this->selectModifiers
		 || $this->map || $this->having || $this->joins) {
			return $this->findPkComplex($key, $con);
		} else {
			return $this->findPkSimple($key, $con);
		}
	}

	/**
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Adresse A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ADR_ID, ADR1, ADR2, ADR3, ADR4, CP, PAYS, COMMUNE FROM resp_adr WHERE ADR_ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new Adresse();
			$obj->hydrate($row);
			AdressePeer::addInstanceToPool($obj, (string) $key);
		}
		$stmt->closeCursor();

		return $obj;
	}

	/**
	 * Find object by primary key.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Adresse|array|mixed the result, formatted by the current formatter
	 */
	protected function findPkComplex($key, $con)
	{
		// As the query uses a PK condition, no limit(1) is necessary.
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKey($key)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
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
		if ($con === null) {
			$con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKeys($keys)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->format($stmt);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AdressePeer::ADR_ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AdressePeer::ADR_ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the adr_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById('fooValue');   // WHERE adr_id = 'fooValue'
	 * $query->filterById('%fooValue%'); // WHERE adr_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $id The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($id)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $id)) {
				$id = str_replace('*', '%', $id);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AdressePeer::ADR_ID, $id, $comparison);
	}

	/**
	 * Filter the query on the adr1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdr1('fooValue');   // WHERE adr1 = 'fooValue'
	 * $query->filterByAdr1('%fooValue%'); // WHERE adr1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adr1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::ADR1, $adr1, $comparison);
	}

	/**
	 * Filter the query on the adr2 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdr2('fooValue');   // WHERE adr2 = 'fooValue'
	 * $query->filterByAdr2('%fooValue%'); // WHERE adr2 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adr2 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::ADR2, $adr2, $comparison);
	}

	/**
	 * Filter the query on the adr3 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdr3('fooValue');   // WHERE adr3 = 'fooValue'
	 * $query->filterByAdr3('%fooValue%'); // WHERE adr3 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adr3 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::ADR3, $adr3, $comparison);
	}

	/**
	 * Filter the query on the adr4 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdr4('fooValue');   // WHERE adr4 = 'fooValue'
	 * $query->filterByAdr4('%fooValue%'); // WHERE adr4 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adr4 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::ADR4, $adr4, $comparison);
	}

	/**
	 * Filter the query on the cp column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCp('fooValue');   // WHERE cp = 'fooValue'
	 * $query->filterByCp('%fooValue%'); // WHERE cp LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $cp The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::CP, $cp, $comparison);
	}

	/**
	 * Filter the query on the pays column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPays('fooValue');   // WHERE pays = 'fooValue'
	 * $query->filterByPays('%fooValue%'); // WHERE pays LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $pays The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::PAYS, $pays, $comparison);
	}

	/**
	 * Filter the query on the commune column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCommune('fooValue');   // WHERE commune = 'fooValue'
	 * $query->filterByCommune('%fooValue%'); // WHERE commune LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $commune The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AdressePeer::COMMUNE, $commune, $comparison);
	}

	/**
	 * Filter the query by a related ResponsableEleve object
	 *
	 * @param     ResponsableEleve $responsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = null)
	{
		if ($responsableEleve instanceof ResponsableEleve) {
			return $this
				->addUsingAlias(AdressePeer::ADR_ID, $responsableEleve->getAdresseId(), $comparison);
		} elseif ($responsableEleve instanceof PropelCollection) {
			return $this
				->useResponsableEleveQuery()
				->filterByPrimaryKeys($responsableEleve->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByResponsableEleve() only accepts arguments of type ResponsableEleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function joinResponsableEleve($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useResponsableEleveQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = null)
	{
		if ($absenceEleveNotification instanceof AbsenceEleveNotification) {
			return $this
				->addUsingAlias(AdressePeer::ADR_ID, $absenceEleveNotification->getAdresseId(), $comparison);
		} elseif ($absenceEleveNotification instanceof PropelCollection) {
			return $this
				->useAbsenceEleveNotificationQuery()
				->filterByPrimaryKeys($absenceEleveNotification->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAbsenceEleveNotification() only accepts arguments of type AbsenceEleveNotification or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveNotification relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveNotification($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useAbsenceEleveNotificationQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveNotification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveNotification', 'AbsenceEleveNotificationQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Adresse $adresse Object to remove from the list of results
	 *
	 * @return    AdresseQuery The current query, for fluid interface
	 */
	public function prune($adresse = null)
	{
		if ($adresse) {
			$this->addUsingAlias(AdressePeer::ADR_ID, $adresse->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseAdresseQuery