<?php


/**
 * Base class that represents a query for the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * @method     AbsenceAgregationDecompteQuery orderByEleveId($order = Criteria::ASC) Order by the eleve_id column
 * @method     AbsenceAgregationDecompteQuery orderByDateDemiJounee($order = Criteria::ASC) Order by the date_demi_jounee column
 * @method     AbsenceAgregationDecompteQuery orderByManquementObligationPresence($order = Criteria::ASC) Order by the manquement_obligation_presence column
 * @method     AbsenceAgregationDecompteQuery orderByNonJustifiee($order = Criteria::ASC) Order by the non_justifiee column
 * @method     AbsenceAgregationDecompteQuery orderByNotifiee($order = Criteria::ASC) Order by the notifiee column
 * @method     AbsenceAgregationDecompteQuery orderByRetards($order = Criteria::ASC) Order by the retards column
 * @method     AbsenceAgregationDecompteQuery orderByRetardsNonJustifies($order = Criteria::ASC) Order by the retards_non_justifies column
 * @method     AbsenceAgregationDecompteQuery orderByMotifsAbsences($order = Criteria::ASC) Order by the motifs_absences column
 * @method     AbsenceAgregationDecompteQuery orderByMotifsRetards($order = Criteria::ASC) Order by the motifs_retards column
 * @method     AbsenceAgregationDecompteQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceAgregationDecompteQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     AbsenceAgregationDecompteQuery groupByEleveId() Group by the eleve_id column
 * @method     AbsenceAgregationDecompteQuery groupByDateDemiJounee() Group by the date_demi_jounee column
 * @method     AbsenceAgregationDecompteQuery groupByManquementObligationPresence() Group by the manquement_obligation_presence column
 * @method     AbsenceAgregationDecompteQuery groupByNonJustifiee() Group by the non_justifiee column
 * @method     AbsenceAgregationDecompteQuery groupByNotifiee() Group by the notifiee column
 * @method     AbsenceAgregationDecompteQuery groupByRetards() Group by the retards column
 * @method     AbsenceAgregationDecompteQuery groupByRetardsNonJustifies() Group by the retards_non_justifies column
 * @method     AbsenceAgregationDecompteQuery groupByMotifsAbsences() Group by the motifs_absences column
 * @method     AbsenceAgregationDecompteQuery groupByMotifsRetards() Group by the motifs_retards column
 * @method     AbsenceAgregationDecompteQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceAgregationDecompteQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     AbsenceAgregationDecompteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceAgregationDecompteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceAgregationDecompteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceAgregationDecompteQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     AbsenceAgregationDecompteQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     AbsenceAgregationDecompteQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     AbsenceAgregationDecompte findOne(PropelPDO $con = null) Return the first AbsenceAgregationDecompte matching the query
 * @method     AbsenceAgregationDecompte findOneOrCreate(PropelPDO $con = null) Return the first AbsenceAgregationDecompte matching the query, or a new AbsenceAgregationDecompte object populated from the query conditions when no match is found
 *
 * @method     AbsenceAgregationDecompte findOneByEleveId(int $eleve_id) Return the first AbsenceAgregationDecompte filtered by the eleve_id column
 * @method     AbsenceAgregationDecompte findOneByDateDemiJounee(string $date_demi_jounee) Return the first AbsenceAgregationDecompte filtered by the date_demi_jounee column
 * @method     AbsenceAgregationDecompte findOneByManquementObligationPresence(boolean $manquement_obligation_presence) Return the first AbsenceAgregationDecompte filtered by the manquement_obligation_presence column
 * @method     AbsenceAgregationDecompte findOneByNonJustifiee(boolean $non_justifiee) Return the first AbsenceAgregationDecompte filtered by the non_justifiee column
 * @method     AbsenceAgregationDecompte findOneByNotifiee(boolean $notifiee) Return the first AbsenceAgregationDecompte filtered by the notifiee column
 * @method     AbsenceAgregationDecompte findOneByRetards(int $retards) Return the first AbsenceAgregationDecompte filtered by the retards column
 * @method     AbsenceAgregationDecompte findOneByRetardsNonJustifies(int $retards_non_justifies) Return the first AbsenceAgregationDecompte filtered by the retards_non_justifies column
 * @method     AbsenceAgregationDecompte findOneByMotifsAbsences(array $motifs_absences) Return the first AbsenceAgregationDecompte filtered by the motifs_absences column
 * @method     AbsenceAgregationDecompte findOneByMotifsRetards(array $motifs_retards) Return the first AbsenceAgregationDecompte filtered by the motifs_retards column
 * @method     AbsenceAgregationDecompte findOneByCreatedAt(string $created_at) Return the first AbsenceAgregationDecompte filtered by the created_at column
 * @method     AbsenceAgregationDecompte findOneByUpdatedAt(string $updated_at) Return the first AbsenceAgregationDecompte filtered by the updated_at column
 *
 * @method     array findByEleveId(int $eleve_id) Return AbsenceAgregationDecompte objects filtered by the eleve_id column
 * @method     array findByDateDemiJounee(string $date_demi_jounee) Return AbsenceAgregationDecompte objects filtered by the date_demi_jounee column
 * @method     array findByManquementObligationPresence(boolean $manquement_obligation_presence) Return AbsenceAgregationDecompte objects filtered by the manquement_obligation_presence column
 * @method     array findByNonJustifiee(boolean $non_justifiee) Return AbsenceAgregationDecompte objects filtered by the non_justifiee column
 * @method     array findByNotifiee(boolean $notifiee) Return AbsenceAgregationDecompte objects filtered by the notifiee column
 * @method     array findByRetards(int $retards) Return AbsenceAgregationDecompte objects filtered by the retards column
 * @method     array findByRetardsNonJustifies(int $retards_non_justifies) Return AbsenceAgregationDecompte objects filtered by the retards_non_justifies column
 * @method     array findByMotifsAbsences(array $motifs_absences) Return AbsenceAgregationDecompte objects filtered by the motifs_absences column
 * @method     array findByMotifsRetards(array $motifs_retards) Return AbsenceAgregationDecompte objects filtered by the motifs_retards column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceAgregationDecompte objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceAgregationDecompte objects filtered by the updated_at column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceAgregationDecompteQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseAbsenceAgregationDecompteQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceAgregationDecompte', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceAgregationDecompteQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceAgregationDecompteQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceAgregationDecompteQuery) {
			return $criteria;
		}
		$query = new AbsenceAgregationDecompteQuery();
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
	 * @param     array[$eleve_id, $date_demi_jounee] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    AbsenceAgregationDecompte|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AbsenceAgregationDecomptePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    AbsenceAgregationDecompte A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ELEVE_ID, DATE_DEMI_JOUNEE, MANQUEMENT_OBLIGATION_PRESENCE, NON_JUSTIFIEE, NOTIFIEE, RETARDS, RETARDS_NON_JUSTIFIES, MOTIFS_ABSENCES, MOTIFS_RETARDS, CREATED_AT, UPDATED_AT FROM a_agregation_decompte WHERE ELEVE_ID = :p0 AND DATE_DEMI_JOUNEE = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new AbsenceAgregationDecompte();
			$obj->hydrate($row);
			AbsenceAgregationDecomptePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
	 * @return    AbsenceAgregationDecompte|array|mixed the result, formatted by the current formatter
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
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(AbsenceAgregationDecomptePeer::ELEVE_ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $key[1], Criteria::EQUAL);

		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(AbsenceAgregationDecomptePeer::ELEVE_ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}

		return $this;
	}

	/**
	 * Filter the query on the eleve_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEleveId(1234); // WHERE eleve_id = 1234
	 * $query->filterByEleveId(array(12, 34)); // WHERE eleve_id IN (12, 34)
	 * $query->filterByEleveId(array('min' => 12)); // WHERE eleve_id > 12
	 * </code>
	 *
	 * @see       filterByEleve()
	 *
	 * @param     mixed $eleveId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByEleveId($eleveId = null, $comparison = null)
	{
		if (is_array($eleveId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::ELEVE_ID, $eleveId, $comparison);
	}

	/**
	 * Filter the query on the date_demi_jounee column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDateDemiJounee('2011-03-14'); // WHERE date_demi_jounee = '2011-03-14'
	 * $query->filterByDateDemiJounee('now'); // WHERE date_demi_jounee = '2011-03-14'
	 * $query->filterByDateDemiJounee(array('max' => 'yesterday')); // WHERE date_demi_jounee > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $dateDemiJounee The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByDateDemiJounee($dateDemiJounee = null, $comparison = null)
	{
		if (is_array($dateDemiJounee)) {
			$useMinMax = false;
			if (isset($dateDemiJounee['min'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $dateDemiJounee['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateDemiJounee['max'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $dateDemiJounee['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $dateDemiJounee, $comparison);
	}

	/**
	 * Filter the query on the manquement_obligation_presence column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByManquementObligationPresence(true); // WHERE manquement_obligation_presence = true
	 * $query->filterByManquementObligationPresence('yes'); // WHERE manquement_obligation_presence = true
	 * </code>
	 *
	 * @param     boolean|string $manquementObligationPresence The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByManquementObligationPresence($manquementObligationPresence = null, $comparison = null)
	{
		if (is_string($manquementObligationPresence)) {
			$manquement_obligation_presence = in_array(strtolower($manquementObligationPresence), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::MANQUEMENT_OBLIGATION_PRESENCE, $manquementObligationPresence, $comparison);
	}

	/**
	 * Filter the query on the non_justifiee column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNonJustifiee(true); // WHERE non_justifiee = true
	 * $query->filterByNonJustifiee('yes'); // WHERE non_justifiee = true
	 * </code>
	 *
	 * @param     boolean|string $nonJustifiee The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByNonJustifiee($nonJustifiee = null, $comparison = null)
	{
		if (is_string($nonJustifiee)) {
			$non_justifiee = in_array(strtolower($nonJustifiee), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::NON_JUSTIFIEE, $nonJustifiee, $comparison);
	}

	/**
	 * Filter the query on the notifiee column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNotifiee(true); // WHERE notifiee = true
	 * $query->filterByNotifiee('yes'); // WHERE notifiee = true
	 * </code>
	 *
	 * @param     boolean|string $notifiee The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByNotifiee($notifiee = null, $comparison = null)
	{
		if (is_string($notifiee)) {
			$notifiee = in_array(strtolower($notifiee), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::NOTIFIEE, $notifiee, $comparison);
	}

	/**
	 * Filter the query on the retards column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRetards(1234); // WHERE retards = 1234
	 * $query->filterByRetards(array(12, 34)); // WHERE retards IN (12, 34)
	 * $query->filterByRetards(array('min' => 12)); // WHERE retards > 12
	 * </code>
	 *
	 * @param     mixed $retards The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByRetards($retards = null, $comparison = null)
	{
		if (is_array($retards)) {
			$useMinMax = false;
			if (isset($retards['min'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS, $retards['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($retards['max'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS, $retards['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS, $retards, $comparison);
	}

	/**
	 * Filter the query on the retards_non_justifies column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRetardsNonJustifies(1234); // WHERE retards_non_justifies = 1234
	 * $query->filterByRetardsNonJustifies(array(12, 34)); // WHERE retards_non_justifies IN (12, 34)
	 * $query->filterByRetardsNonJustifies(array('min' => 12)); // WHERE retards_non_justifies > 12
	 * </code>
	 *
	 * @param     mixed $retardsNonJustifies The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByRetardsNonJustifies($retardsNonJustifies = null, $comparison = null)
	{
		if (is_array($retardsNonJustifies)) {
			$useMinMax = false;
			if (isset($retardsNonJustifies['min'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS_NON_JUSTIFIES, $retardsNonJustifies['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($retardsNonJustifies['max'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS_NON_JUSTIFIES, $retardsNonJustifies['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::RETARDS_NON_JUSTIFIES, $retardsNonJustifies, $comparison);
	}

	/**
	 * Filter the query on the motifs_absences column
	 *
	 * @param     array $motifsAbsences The values to use as filter.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByMotifsAbsences($motifsAbsences = null, $comparison = null)
	{
		$key = $this->getAliasedColName(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES);
		if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
			foreach ($motifsAbsences as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addAnd($key, $value, Criteria::LIKE);
				} else {
					$this->add($key, $value, Criteria::LIKE);
				}
			}
			return $this;
		} elseif ($comparison == Criteria::CONTAINS_SOME) {
			foreach ($motifsAbsences as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addOr($key, $value, Criteria::LIKE);
				} else {
					$this->add($key, $value, Criteria::LIKE);
				}
			}
			return $this;
		} elseif ($comparison == Criteria::CONTAINS_NONE) {
			foreach ($motifsAbsences as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addAnd($key, $value, Criteria::NOT_LIKE);
				} else {
					$this->add($key, $value, Criteria::NOT_LIKE);
				}
			}
			$this->addOr($key, null, Criteria::ISNULL);
			return $this;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES, $motifsAbsences, $comparison);
	}

	/**
	 * Filter the query on the motifs_absences column
	 * @param     mixed $motifsAbsences The value to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByMotifsAbsence($motifsAbsences = null, $comparison = null)
	{
		if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
			if (is_scalar($motifsAbsences)) {
				$motifsAbsences = '%| ' . $motifsAbsences . ' |%';
				$comparison = Criteria::LIKE;
			}
		} elseif ($comparison == Criteria::CONTAINS_NONE) {
			$motifsAbsences = '%| ' . $motifsAbsences . ' |%';
			$comparison = Criteria::NOT_LIKE;
			$key = $this->getAliasedColName(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES);
			if($this->containsKey($key)) {
				$this->addAnd($key, $motifsAbsences, $comparison);
			} else {
				$this->addAnd($key, $motifsAbsences, $comparison);
			}
			$this->addOr($key, null, Criteria::ISNULL);
			return $this;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES, $motifsAbsences, $comparison);
	}

	/**
	 * Filter the query on the motifs_retards column
	 *
	 * @param     array $motifsRetards The values to use as filter.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByMotifsRetards($motifsRetards = null, $comparison = null)
	{
		$key = $this->getAliasedColName(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS);
		if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
			foreach ($motifsRetards as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addAnd($key, $value, Criteria::LIKE);
				} else {
					$this->add($key, $value, Criteria::LIKE);
				}
			}
			return $this;
		} elseif ($comparison == Criteria::CONTAINS_SOME) {
			foreach ($motifsRetards as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addOr($key, $value, Criteria::LIKE);
				} else {
					$this->add($key, $value, Criteria::LIKE);
				}
			}
			return $this;
		} elseif ($comparison == Criteria::CONTAINS_NONE) {
			foreach ($motifsRetards as $value) {
				$value = '%| ' . $value . ' |%';
				if($this->containsKey($key)) {
					$this->addAnd($key, $value, Criteria::NOT_LIKE);
				} else {
					$this->add($key, $value, Criteria::NOT_LIKE);
				}
			}
			$this->addOr($key, null, Criteria::ISNULL);
			return $this;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS, $motifsRetards, $comparison);
	}

	/**
	 * Filter the query on the motifs_retards column
	 * @param     mixed $motifsRetards The value to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByMotifsRetard($motifsRetards = null, $comparison = null)
	{
		if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
			if (is_scalar($motifsRetards)) {
				$motifsRetards = '%| ' . $motifsRetards . ' |%';
				$comparison = Criteria::LIKE;
			}
		} elseif ($comparison == Criteria::CONTAINS_NONE) {
			$motifsRetards = '%| ' . $motifsRetards . ' |%';
			$comparison = Criteria::NOT_LIKE;
			$key = $this->getAliasedColName(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS);
			if($this->containsKey($key)) {
				$this->addAnd($key, $motifsRetards, $comparison);
			} else {
				$this->addAnd($key, $motifsRetards, $comparison);
			}
			$this->addOr($key, null, Criteria::ISNULL);
			return $this;
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS, $motifsRetards, $comparison);
	}

	/**
	 * Filter the query on the created_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $createdAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::CREATED_AT, $createdAt, $comparison);
	}

	/**
	 * Filter the query on the updated_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $updatedAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(AbsenceAgregationDecomptePeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::UPDATED_AT, $updatedAt, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(AbsenceAgregationDecomptePeer::ELEVE_ID, $eleve->getId(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceAgregationDecomptePeer::ELEVE_ID, $eleve->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     AbsenceAgregationDecompte $absenceAgregationDecompte Object to remove from the list of results
	 *
	 * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function prune($absenceAgregationDecompte = null)
	{
		if ($absenceAgregationDecompte) {
			$this->addCond('pruneCond0', $this->getAliasedColName(AbsenceAgregationDecomptePeer::ELEVE_ID), $absenceAgregationDecompte->getEleveId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE), $absenceAgregationDecompte->getDateDemiJounee(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
		}

		return $this;
	}

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceAgregationDecomptePeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceAgregationDecomptePeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceAgregationDecomptePeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceAgregationDecomptePeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceAgregationDecompteQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceAgregationDecomptePeer::CREATED_AT);
	}

} // BaseAbsenceAgregationDecompteQuery