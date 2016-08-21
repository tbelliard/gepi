<?php


/**
 * Base class that represents a query for the 'responsables2' table.
 *
 * Table de jointure entre les eleves et leurs responsables legaux avec mention du niveau de ces responsables
 *
 * @method     ResponsableInformationQuery orderByEleId($order = Criteria::ASC) Order by the ele_id column
 * @method     ResponsableInformationQuery orderByResponsableEleveId($order = Criteria::ASC) Order by the pers_id column
 * @method     ResponsableInformationQuery orderByNiveauResponsabilite($order = Criteria::ASC) Order by the resp_legal column
 * @method     ResponsableInformationQuery orderByPersContact($order = Criteria::ASC) Order by the pers_contact column
 *
 * @method     ResponsableInformationQuery groupByEleId() Group by the ele_id column
 * @method     ResponsableInformationQuery groupByResponsableEleveId() Group by the pers_id column
 * @method     ResponsableInformationQuery groupByNiveauResponsabilite() Group by the resp_legal column
 * @method     ResponsableInformationQuery groupByPersContact() Group by the pers_contact column
 *
 * @method     ResponsableInformationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ResponsableInformationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ResponsableInformationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ResponsableInformationQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     ResponsableInformationQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     ResponsableInformationQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     ResponsableInformationQuery leftJoinResponsableEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableEleve relation
 * @method     ResponsableInformationQuery rightJoinResponsableEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableEleve relation
 * @method     ResponsableInformationQuery innerJoinResponsableEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableEleve relation
 *
 * @method     ResponsableInformation findOne(PropelPDO $con = null) Return the first ResponsableInformation matching the query
 * @method     ResponsableInformation findOneOrCreate(PropelPDO $con = null) Return the first ResponsableInformation matching the query, or a new ResponsableInformation object populated from the query conditions when no match is found
 *
 * @method     ResponsableInformation findOneByEleId(string $ele_id) Return the first ResponsableInformation filtered by the ele_id column
 * @method     ResponsableInformation findOneByResponsableEleveId(string $pers_id) Return the first ResponsableInformation filtered by the pers_id column
 * @method     ResponsableInformation findOneByNiveauResponsabilite(string $resp_legal) Return the first ResponsableInformation filtered by the resp_legal column
 * @method     ResponsableInformation findOneByPersContact(string $pers_contact) Return the first ResponsableInformation filtered by the pers_contact column
 *
 * @method     array findByEleId(string $ele_id) Return ResponsableInformation objects filtered by the ele_id column
 * @method     array findByResponsableEleveId(string $pers_id) Return ResponsableInformation objects filtered by the pers_id column
 * @method     array findByNiveauResponsabilite(string $resp_legal) Return ResponsableInformation objects filtered by the resp_legal column
 * @method     array findByPersContact(string $pers_contact) Return ResponsableInformation objects filtered by the pers_contact column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableInformationQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseResponsableInformationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'ResponsableInformation', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ResponsableInformationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ResponsableInformationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ResponsableInformationQuery) {
			return $criteria;
		}
		$query = new ResponsableInformationQuery();
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
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 *
	 * @param     array[$ele_id, $resp_legal] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    ResponsableInformation|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = ResponsableInformationPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(ResponsableInformationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    ResponsableInformation A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ELE_ID, PERS_ID, RESP_LEGAL, PERS_CONTACT FROM responsables2 WHERE ELE_ID = :p0 AND RESP_LEGAL = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new ResponsableInformation();
			$obj->hydrate($row);
			ResponsableInformationPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    ResponsableInformation|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(ResponsableInformationPeer::ELE_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(ResponsableInformationPeer::RESP_LEGAL, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(ResponsableInformationPeer::ELE_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(ResponsableInformationPeer::RESP_LEGAL, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the ele_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEleId('fooValue');   // WHERE ele_id = 'fooValue'
	 * $query->filterByEleId('%fooValue%'); // WHERE ele_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $eleId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByEleId($eleId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($eleId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $eleId)) {
				$eleId = str_replace('*', '%', $eleId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableInformationPeer::ELE_ID, $eleId, $comparison);
	}

	/**
	 * Filter the query on the pers_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByResponsableEleveId('fooValue');   // WHERE pers_id = 'fooValue'
	 * $query->filterByResponsableEleveId('%fooValue%'); // WHERE pers_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $responsableEleveId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleveId($responsableEleveId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($responsableEleveId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $responsableEleveId)) {
				$responsableEleveId = str_replace('*', '%', $responsableEleveId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableInformationPeer::PERS_ID, $responsableEleveId, $comparison);
	}

	/**
	 * Filter the query on the resp_legal column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNiveauResponsabilite('fooValue');   // WHERE resp_legal = 'fooValue'
	 * $query->filterByNiveauResponsabilite('%fooValue%'); // WHERE resp_legal LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $niveauResponsabilite The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByNiveauResponsabilite($niveauResponsabilite = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($niveauResponsabilite)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $niveauResponsabilite)) {
				$niveauResponsabilite = str_replace('*', '%', $niveauResponsabilite);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableInformationPeer::RESP_LEGAL, $niveauResponsabilite, $comparison);
	}

	/**
	 * Filter the query on the pers_contact column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPersContact('fooValue');   // WHERE pers_contact = 'fooValue'
	 * $query->filterByPersContact('%fooValue%'); // WHERE pers_contact LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $persContact The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByPersContact($persContact = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($persContact)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $persContact)) {
				$persContact = str_replace('*', '%', $persContact);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableInformationPeer::PERS_CONTACT, $persContact, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(ResponsableInformationPeer::ELE_ID, $eleve->getEleId(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(ResponsableInformationPeer::ELE_ID, $eleve->toKeyValue('PrimaryKey', 'EleId'), $comparison);
		} else {
			throw new PropelException('filterByEleve() only accepts arguments of type Eleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Filter the query by a related ResponsableEleve object
	 *
	 * @param     ResponsableEleve|PropelCollection $responsableEleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = null)
	{
		if ($responsableEleve instanceof ResponsableEleve) {
			return $this
				->addUsingAlias(ResponsableInformationPeer::PERS_ID, $responsableEleve->getResponsableEleveId(), $comparison);
		} elseif ($responsableEleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(ResponsableInformationPeer::PERS_ID, $responsableEleve->toKeyValue('PrimaryKey', 'ResponsableEleveId'), $comparison);
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
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function joinResponsableEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useResponsableEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinResponsableEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableEleve', 'ResponsableEleveQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     ResponsableInformation $responsableInformation Object to remove from the list of results
	 *
	 * @return    ResponsableInformationQuery The current query, for fluid interface
	 */
	public function prune($responsableInformation = null)
	{
		if ($responsableInformation) {
			$this->addCond('pruneCond0', $this->getAliasedColName(ResponsableInformationPeer::ELE_ID), $responsableInformation->getEleId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(ResponsableInformationPeer::RESP_LEGAL), $responsableInformation->getNiveauResponsabilite(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

} // BaseResponsableInformationQuery