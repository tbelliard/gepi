<?php


/**
 * Base class that represents a query for the 'horaires_etablissement' table.
 *
 * Table contenant les heures d'ouverture et de fermeture de l'etablissement par journee
 *
 * @method     EdtHorairesEtablissementQuery orderByIdHoraireEtablissement($order = Criteria::ASC) Order by the id_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByDateHoraireEtablissement($order = Criteria::ASC) Order by the date_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByJourHoraireEtablissement($order = Criteria::ASC) Order by the jour_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByOuvertureHoraireEtablissement($order = Criteria::ASC) Order by the ouverture_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByFermetureHoraireEtablissement($order = Criteria::ASC) Order by the fermeture_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByPauseHoraireEtablissement($order = Criteria::ASC) Order by the pause_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery orderByOuvertHoraireEtablissement($order = Criteria::ASC) Order by the ouvert_horaire_etablissement column
 *
 * @method     EdtHorairesEtablissementQuery groupByIdHoraireEtablissement() Group by the id_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByDateHoraireEtablissement() Group by the date_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByJourHoraireEtablissement() Group by the jour_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByOuvertureHoraireEtablissement() Group by the ouverture_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByFermetureHoraireEtablissement() Group by the fermeture_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByPauseHoraireEtablissement() Group by the pause_horaire_etablissement column
 * @method     EdtHorairesEtablissementQuery groupByOuvertHoraireEtablissement() Group by the ouvert_horaire_etablissement column
 *
 * @method     EdtHorairesEtablissementQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtHorairesEtablissementQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtHorairesEtablissementQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtHorairesEtablissement findOne(PropelPDO $con = null) Return the first EdtHorairesEtablissement matching the query
 * @method     EdtHorairesEtablissement findOneOrCreate(PropelPDO $con = null) Return the first EdtHorairesEtablissement matching the query, or a new EdtHorairesEtablissement object populated from the query conditions when no match is found
 *
 * @method     EdtHorairesEtablissement findOneByIdHoraireEtablissement(int $id_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the id_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByDateHoraireEtablissement(string $date_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the date_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByJourHoraireEtablissement(string $jour_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the jour_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByOuvertureHoraireEtablissement(string $ouverture_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the ouverture_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByFermetureHoraireEtablissement(string $fermeture_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the fermeture_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByPauseHoraireEtablissement(string $pause_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the pause_horaire_etablissement column
 * @method     EdtHorairesEtablissement findOneByOuvertHoraireEtablissement(boolean $ouvert_horaire_etablissement) Return the first EdtHorairesEtablissement filtered by the ouvert_horaire_etablissement column
 *
 * @method     array findByIdHoraireEtablissement(int $id_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the id_horaire_etablissement column
 * @method     array findByDateHoraireEtablissement(string $date_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the date_horaire_etablissement column
 * @method     array findByJourHoraireEtablissement(string $jour_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the jour_horaire_etablissement column
 * @method     array findByOuvertureHoraireEtablissement(string $ouverture_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the ouverture_horaire_etablissement column
 * @method     array findByFermetureHoraireEtablissement(string $fermeture_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the fermeture_horaire_etablissement column
 * @method     array findByPauseHoraireEtablissement(string $pause_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the pause_horaire_etablissement column
 * @method     array findByOuvertHoraireEtablissement(boolean $ouvert_horaire_etablissement) Return EdtHorairesEtablissement objects filtered by the ouvert_horaire_etablissement column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtHorairesEtablissementQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseEdtHorairesEtablissementQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtHorairesEtablissement', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtHorairesEtablissementQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtHorairesEtablissementQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtHorairesEtablissementQuery) {
			return $criteria;
		}
		$query = new EdtHorairesEtablissementQuery();
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
	 * @return    EdtHorairesEtablissement|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = EdtHorairesEtablissementPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(EdtHorairesEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    EdtHorairesEtablissement A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID_HORAIRE_ETABLISSEMENT, DATE_HORAIRE_ETABLISSEMENT, JOUR_HORAIRE_ETABLISSEMENT, OUVERTURE_HORAIRE_ETABLISSEMENT, FERMETURE_HORAIRE_ETABLISSEMENT, PAUSE_HORAIRE_ETABLISSEMENT, OUVERT_HORAIRE_ETABLISSEMENT FROM horaires_etablissement WHERE ID_HORAIRE_ETABLISSEMENT = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new EdtHorairesEtablissement();
			$obj->hydrate($row);
			EdtHorairesEtablissementPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    EdtHorairesEtablissement|array|mixed the result, formatted by the current formatter
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
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdHoraireEtablissement(1234); // WHERE id_horaire_etablissement = 1234
	 * $query->filterByIdHoraireEtablissement(array(12, 34)); // WHERE id_horaire_etablissement IN (12, 34)
	 * $query->filterByIdHoraireEtablissement(array('min' => 12)); // WHERE id_horaire_etablissement > 12
	 * </code>
	 *
	 * @param     mixed $idHoraireEtablissement The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByIdHoraireEtablissement($idHoraireEtablissement = null, $comparison = null)
	{
		if (is_array($idHoraireEtablissement) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $idHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the date_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDateHoraireEtablissement('2011-03-14'); // WHERE date_horaire_etablissement = '2011-03-14'
	 * $query->filterByDateHoraireEtablissement('now'); // WHERE date_horaire_etablissement = '2011-03-14'
	 * $query->filterByDateHoraireEtablissement(array('max' => 'yesterday')); // WHERE date_horaire_etablissement > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $dateHoraireEtablissement The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByDateHoraireEtablissement($dateHoraireEtablissement = null, $comparison = null)
	{
		if (is_array($dateHoraireEtablissement)) {
			$useMinMax = false;
			if (isset($dateHoraireEtablissement['min'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateHoraireEtablissement['max'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::DATE_HORAIRE_ETABLISSEMENT, $dateHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the jour_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByJourHoraireEtablissement('fooValue');   // WHERE jour_horaire_etablissement = 'fooValue'
	 * $query->filterByJourHoraireEtablissement('%fooValue%'); // WHERE jour_horaire_etablissement LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $jourHoraireEtablissement The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByJourHoraireEtablissement($jourHoraireEtablissement = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($jourHoraireEtablissement)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $jourHoraireEtablissement)) {
				$jourHoraireEtablissement = str_replace('*', '%', $jourHoraireEtablissement);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::JOUR_HORAIRE_ETABLISSEMENT, $jourHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the ouverture_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOuvertureHoraireEtablissement('2011-03-14'); // WHERE ouverture_horaire_etablissement = '2011-03-14'
	 * $query->filterByOuvertureHoraireEtablissement('now'); // WHERE ouverture_horaire_etablissement = '2011-03-14'
	 * $query->filterByOuvertureHoraireEtablissement(array('max' => 'yesterday')); // WHERE ouverture_horaire_etablissement > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $ouvertureHoraireEtablissement The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByOuvertureHoraireEtablissement($ouvertureHoraireEtablissement = null, $comparison = null)
	{
		if (is_array($ouvertureHoraireEtablissement)) {
			$useMinMax = false;
			if (isset($ouvertureHoraireEtablissement['min'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ouvertureHoraireEtablissement['max'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::OUVERTURE_HORAIRE_ETABLISSEMENT, $ouvertureHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the fermeture_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFermetureHoraireEtablissement('2011-03-14'); // WHERE fermeture_horaire_etablissement = '2011-03-14'
	 * $query->filterByFermetureHoraireEtablissement('now'); // WHERE fermeture_horaire_etablissement = '2011-03-14'
	 * $query->filterByFermetureHoraireEtablissement(array('max' => 'yesterday')); // WHERE fermeture_horaire_etablissement > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $fermetureHoraireEtablissement The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByFermetureHoraireEtablissement($fermetureHoraireEtablissement = null, $comparison = null)
	{
		if (is_array($fermetureHoraireEtablissement)) {
			$useMinMax = false;
			if (isset($fermetureHoraireEtablissement['min'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($fermetureHoraireEtablissement['max'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::FERMETURE_HORAIRE_ETABLISSEMENT, $fermetureHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the pause_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPauseHoraireEtablissement('2011-03-14'); // WHERE pause_horaire_etablissement = '2011-03-14'
	 * $query->filterByPauseHoraireEtablissement('now'); // WHERE pause_horaire_etablissement = '2011-03-14'
	 * $query->filterByPauseHoraireEtablissement(array('max' => 'yesterday')); // WHERE pause_horaire_etablissement > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $pauseHoraireEtablissement The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByPauseHoraireEtablissement($pauseHoraireEtablissement = null, $comparison = null)
	{
		if (is_array($pauseHoraireEtablissement)) {
			$useMinMax = false;
			if (isset($pauseHoraireEtablissement['min'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($pauseHoraireEtablissement['max'])) {
				$this->addUsingAlias(EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::PAUSE_HORAIRE_ETABLISSEMENT, $pauseHoraireEtablissement, $comparison);
	}

	/**
	 * Filter the query on the ouvert_horaire_etablissement column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByOuvertHoraireEtablissement(true); // WHERE ouvert_horaire_etablissement = true
	 * $query->filterByOuvertHoraireEtablissement('yes'); // WHERE ouvert_horaire_etablissement = true
	 * </code>
	 *
	 * @param     boolean|string $ouvertHoraireEtablissement The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function filterByOuvertHoraireEtablissement($ouvertHoraireEtablissement = null, $comparison = null)
	{
		if (is_string($ouvertHoraireEtablissement)) {
			$ouvert_horaire_etablissement = in_array(strtolower($ouvertHoraireEtablissement), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(EdtHorairesEtablissementPeer::OUVERT_HORAIRE_ETABLISSEMENT, $ouvertHoraireEtablissement, $comparison);
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtHorairesEtablissement $edtHorairesEtablissement Object to remove from the list of results
	 *
	 * @return    EdtHorairesEtablissementQuery The current query, for fluid interface
	 */
	public function prune($edtHorairesEtablissement = null)
	{
		if ($edtHorairesEtablissement) {
			$this->addUsingAlias(EdtHorairesEtablissementPeer::ID_HORAIRE_ETABLISSEMENT, $edtHorairesEtablissement->getIdHoraireEtablissement(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseEdtHorairesEtablissementQuery