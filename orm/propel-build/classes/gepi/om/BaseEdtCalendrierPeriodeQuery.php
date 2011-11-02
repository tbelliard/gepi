<?php


/**
 * Base class that represents a query for the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * @method     EdtCalendrierPeriodeQuery orderByIdCalendrier($order = Criteria::ASC) Order by the id_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByClasseConcerneCalendrier($order = Criteria::ASC) Order by the classe_concerne_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByNomCalendrier($order = Criteria::ASC) Order by the nom_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByDebutCalendrierTs($order = Criteria::ASC) Order by the debut_calendrier_ts column
 * @method     EdtCalendrierPeriodeQuery orderByFinCalendrierTs($order = Criteria::ASC) Order by the fin_calendrier_ts column
 * @method     EdtCalendrierPeriodeQuery orderByJourdebutCalendrier($order = Criteria::ASC) Order by the jourdebut_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByHeuredebutCalendrier($order = Criteria::ASC) Order by the heuredebut_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByJourfinCalendrier($order = Criteria::ASC) Order by the jourfin_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByHeurefinCalendrier($order = Criteria::ASC) Order by the heurefin_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByNumeroPeriode($order = Criteria::ASC) Order by the numero_periode column
 * @method     EdtCalendrierPeriodeQuery orderByEtabfermeCalendrier($order = Criteria::ASC) Order by the etabferme_calendrier column
 * @method     EdtCalendrierPeriodeQuery orderByEtabvacancesCalendrier($order = Criteria::ASC) Order by the etabvacances_calendrier column
 *
 * @method     EdtCalendrierPeriodeQuery groupByIdCalendrier() Group by the id_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByClasseConcerneCalendrier() Group by the classe_concerne_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByNomCalendrier() Group by the nom_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByDebutCalendrierTs() Group by the debut_calendrier_ts column
 * @method     EdtCalendrierPeriodeQuery groupByFinCalendrierTs() Group by the fin_calendrier_ts column
 * @method     EdtCalendrierPeriodeQuery groupByJourdebutCalendrier() Group by the jourdebut_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByHeuredebutCalendrier() Group by the heuredebut_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByJourfinCalendrier() Group by the jourfin_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByHeurefinCalendrier() Group by the heurefin_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByNumeroPeriode() Group by the numero_periode column
 * @method     EdtCalendrierPeriodeQuery groupByEtabfermeCalendrier() Group by the etabferme_calendrier column
 * @method     EdtCalendrierPeriodeQuery groupByEtabvacancesCalendrier() Group by the etabvacances_calendrier column
 *
 * @method     EdtCalendrierPeriodeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtCalendrierPeriodeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtCalendrierPeriodeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtCalendrierPeriodeQuery leftJoinEdtEmplacementCours($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtCalendrierPeriodeQuery rightJoinEdtEmplacementCours($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtCalendrierPeriodeQuery innerJoinEdtEmplacementCours($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     EdtCalendrierPeriode findOne(PropelPDO $con = null) Return the first EdtCalendrierPeriode matching the query
 * @method     EdtCalendrierPeriode findOneOrCreate(PropelPDO $con = null) Return the first EdtCalendrierPeriode matching the query, or a new EdtCalendrierPeriode object populated from the query conditions when no match is found
 *
 * @method     EdtCalendrierPeriode findOneByIdCalendrier(int $id_calendrier) Return the first EdtCalendrierPeriode filtered by the id_calendrier column
 * @method     EdtCalendrierPeriode findOneByClasseConcerneCalendrier(string $classe_concerne_calendrier) Return the first EdtCalendrierPeriode filtered by the classe_concerne_calendrier column
 * @method     EdtCalendrierPeriode findOneByNomCalendrier(string $nom_calendrier) Return the first EdtCalendrierPeriode filtered by the nom_calendrier column
 * @method     EdtCalendrierPeriode findOneByDebutCalendrierTs(string $debut_calendrier_ts) Return the first EdtCalendrierPeriode filtered by the debut_calendrier_ts column
 * @method     EdtCalendrierPeriode findOneByFinCalendrierTs(string $fin_calendrier_ts) Return the first EdtCalendrierPeriode filtered by the fin_calendrier_ts column
 * @method     EdtCalendrierPeriode findOneByJourdebutCalendrier(string $jourdebut_calendrier) Return the first EdtCalendrierPeriode filtered by the jourdebut_calendrier column
 * @method     EdtCalendrierPeriode findOneByHeuredebutCalendrier(string $heuredebut_calendrier) Return the first EdtCalendrierPeriode filtered by the heuredebut_calendrier column
 * @method     EdtCalendrierPeriode findOneByJourfinCalendrier(string $jourfin_calendrier) Return the first EdtCalendrierPeriode filtered by the jourfin_calendrier column
 * @method     EdtCalendrierPeriode findOneByHeurefinCalendrier(string $heurefin_calendrier) Return the first EdtCalendrierPeriode filtered by the heurefin_calendrier column
 * @method     EdtCalendrierPeriode findOneByNumeroPeriode(int $numero_periode) Return the first EdtCalendrierPeriode filtered by the numero_periode column
 * @method     EdtCalendrierPeriode findOneByEtabfermeCalendrier(int $etabferme_calendrier) Return the first EdtCalendrierPeriode filtered by the etabferme_calendrier column
 * @method     EdtCalendrierPeriode findOneByEtabvacancesCalendrier(int $etabvacances_calendrier) Return the first EdtCalendrierPeriode filtered by the etabvacances_calendrier column
 *
 * @method     array findByIdCalendrier(int $id_calendrier) Return EdtCalendrierPeriode objects filtered by the id_calendrier column
 * @method     array findByClasseConcerneCalendrier(string $classe_concerne_calendrier) Return EdtCalendrierPeriode objects filtered by the classe_concerne_calendrier column
 * @method     array findByNomCalendrier(string $nom_calendrier) Return EdtCalendrierPeriode objects filtered by the nom_calendrier column
 * @method     array findByDebutCalendrierTs(string $debut_calendrier_ts) Return EdtCalendrierPeriode objects filtered by the debut_calendrier_ts column
 * @method     array findByFinCalendrierTs(string $fin_calendrier_ts) Return EdtCalendrierPeriode objects filtered by the fin_calendrier_ts column
 * @method     array findByJourdebutCalendrier(string $jourdebut_calendrier) Return EdtCalendrierPeriode objects filtered by the jourdebut_calendrier column
 * @method     array findByHeuredebutCalendrier(string $heuredebut_calendrier) Return EdtCalendrierPeriode objects filtered by the heuredebut_calendrier column
 * @method     array findByJourfinCalendrier(string $jourfin_calendrier) Return EdtCalendrierPeriode objects filtered by the jourfin_calendrier column
 * @method     array findByHeurefinCalendrier(string $heurefin_calendrier) Return EdtCalendrierPeriode objects filtered by the heurefin_calendrier column
 * @method     array findByNumeroPeriode(int $numero_periode) Return EdtCalendrierPeriode objects filtered by the numero_periode column
 * @method     array findByEtabfermeCalendrier(int $etabferme_calendrier) Return EdtCalendrierPeriode objects filtered by the etabferme_calendrier column
 * @method     array findByEtabvacancesCalendrier(int $etabvacances_calendrier) Return EdtCalendrierPeriode objects filtered by the etabvacances_calendrier column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtCalendrierPeriodeQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtCalendrierPeriodeQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtCalendrierPeriode', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtCalendrierPeriodeQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtCalendrierPeriodeQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtCalendrierPeriodeQuery) {
			return $criteria;
		}
		$query = new EdtCalendrierPeriodeQuery();
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
	 * @return    EdtCalendrierPeriode|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtCalendrierPeriodePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::ID_CALENDRIER, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::ID_CALENDRIER, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdCalendrier(1234); // WHERE id_calendrier = 1234
	 * $query->filterByIdCalendrier(array(12, 34)); // WHERE id_calendrier IN (12, 34)
	 * $query->filterByIdCalendrier(array('min' => 12)); // WHERE id_calendrier > 12
	 * </code>
	 *
	 * @param     mixed $idCalendrier The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByIdCalendrier($idCalendrier = null, $comparison = null)
	{
		if (is_array($idCalendrier) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::ID_CALENDRIER, $idCalendrier, $comparison);
	}

	/**
	 * Filter the query on the classe_concerne_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByClasseConcerneCalendrier('fooValue');   // WHERE classe_concerne_calendrier = 'fooValue'
	 * $query->filterByClasseConcerneCalendrier('%fooValue%'); // WHERE classe_concerne_calendrier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $classeConcerneCalendrier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByClasseConcerneCalendrier($classeConcerneCalendrier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($classeConcerneCalendrier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $classeConcerneCalendrier)) {
				$classeConcerneCalendrier = str_replace('*', '%', $classeConcerneCalendrier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::CLASSE_CONCERNE_CALENDRIER, $classeConcerneCalendrier, $comparison);
	}

	/**
	 * Filter the query on the nom_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNomCalendrier('fooValue');   // WHERE nom_calendrier = 'fooValue'
	 * $query->filterByNomCalendrier('%fooValue%'); // WHERE nom_calendrier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomCalendrier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByNomCalendrier($nomCalendrier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomCalendrier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomCalendrier)) {
				$nomCalendrier = str_replace('*', '%', $nomCalendrier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::NOM_CALENDRIER, $nomCalendrier, $comparison);
	}

	/**
	 * Filter the query on the debut_calendrier_ts column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByDebutCalendrierTs('fooValue');   // WHERE debut_calendrier_ts = 'fooValue'
	 * $query->filterByDebutCalendrierTs('%fooValue%'); // WHERE debut_calendrier_ts LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $debutCalendrierTs The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByDebutCalendrierTs($debutCalendrierTs = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($debutCalendrierTs)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $debutCalendrierTs)) {
				$debutCalendrierTs = str_replace('*', '%', $debutCalendrierTs);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::DEBUT_CALENDRIER_TS, $debutCalendrierTs, $comparison);
	}

	/**
	 * Filter the query on the fin_calendrier_ts column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByFinCalendrierTs('fooValue');   // WHERE fin_calendrier_ts = 'fooValue'
	 * $query->filterByFinCalendrierTs('%fooValue%'); // WHERE fin_calendrier_ts LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $finCalendrierTs The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByFinCalendrierTs($finCalendrierTs = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($finCalendrierTs)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $finCalendrierTs)) {
				$finCalendrierTs = str_replace('*', '%', $finCalendrierTs);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::FIN_CALENDRIER_TS, $finCalendrierTs, $comparison);
	}

	/**
	 * Filter the query on the jourdebut_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByJourdebutCalendrier('2011-03-14'); // WHERE jourdebut_calendrier = '2011-03-14'
	 * $query->filterByJourdebutCalendrier('now'); // WHERE jourdebut_calendrier = '2011-03-14'
	 * $query->filterByJourdebutCalendrier(array('max' => 'yesterday')); // WHERE jourdebut_calendrier > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $jourdebutCalendrier The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByJourdebutCalendrier($jourdebutCalendrier = null, $comparison = null)
	{
		if (is_array($jourdebutCalendrier)) {
			$useMinMax = false;
			if (isset($jourdebutCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER, $jourdebutCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($jourdebutCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER, $jourdebutCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER, $jourdebutCalendrier, $comparison);
	}

	/**
	 * Filter the query on the heuredebut_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByHeuredebutCalendrier('2011-03-14'); // WHERE heuredebut_calendrier = '2011-03-14'
	 * $query->filterByHeuredebutCalendrier('now'); // WHERE heuredebut_calendrier = '2011-03-14'
	 * $query->filterByHeuredebutCalendrier(array('max' => 'yesterday')); // WHERE heuredebut_calendrier > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $heuredebutCalendrier The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByHeuredebutCalendrier($heuredebutCalendrier = null, $comparison = null)
	{
		if (is_array($heuredebutCalendrier)) {
			$useMinMax = false;
			if (isset($heuredebutCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER, $heuredebutCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($heuredebutCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER, $heuredebutCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER, $heuredebutCalendrier, $comparison);
	}

	/**
	 * Filter the query on the jourfin_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByJourfinCalendrier('2011-03-14'); // WHERE jourfin_calendrier = '2011-03-14'
	 * $query->filterByJourfinCalendrier('now'); // WHERE jourfin_calendrier = '2011-03-14'
	 * $query->filterByJourfinCalendrier(array('max' => 'yesterday')); // WHERE jourfin_calendrier > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $jourfinCalendrier The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByJourfinCalendrier($jourfinCalendrier = null, $comparison = null)
	{
		if (is_array($jourfinCalendrier)) {
			$useMinMax = false;
			if (isset($jourfinCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER, $jourfinCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($jourfinCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER, $jourfinCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER, $jourfinCalendrier, $comparison);
	}

	/**
	 * Filter the query on the heurefin_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByHeurefinCalendrier('2011-03-14'); // WHERE heurefin_calendrier = '2011-03-14'
	 * $query->filterByHeurefinCalendrier('now'); // WHERE heurefin_calendrier = '2011-03-14'
	 * $query->filterByHeurefinCalendrier(array('max' => 'yesterday')); // WHERE heurefin_calendrier > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $heurefinCalendrier The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByHeurefinCalendrier($heurefinCalendrier = null, $comparison = null)
	{
		if (is_array($heurefinCalendrier)) {
			$useMinMax = false;
			if (isset($heurefinCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER, $heurefinCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($heurefinCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER, $heurefinCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER, $heurefinCalendrier, $comparison);
	}

	/**
	 * Filter the query on the numero_periode column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNumeroPeriode(1234); // WHERE numero_periode = 1234
	 * $query->filterByNumeroPeriode(array(12, 34)); // WHERE numero_periode IN (12, 34)
	 * $query->filterByNumeroPeriode(array('min' => 12)); // WHERE numero_periode > 12
	 * </code>
	 *
	 * @param     mixed $numeroPeriode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByNumeroPeriode($numeroPeriode = null, $comparison = null)
	{
		if (is_array($numeroPeriode)) {
			$useMinMax = false;
			if (isset($numeroPeriode['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::NUMERO_PERIODE, $numeroPeriode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($numeroPeriode['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::NUMERO_PERIODE, $numeroPeriode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::NUMERO_PERIODE, $numeroPeriode, $comparison);
	}

	/**
	 * Filter the query on the etabferme_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByEtabfermeCalendrier(1234); // WHERE etabferme_calendrier = 1234
	 * $query->filterByEtabfermeCalendrier(array(12, 34)); // WHERE etabferme_calendrier IN (12, 34)
	 * $query->filterByEtabfermeCalendrier(array('min' => 12)); // WHERE etabferme_calendrier > 12
	 * </code>
	 *
	 * @param     mixed $etabfermeCalendrier The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByEtabfermeCalendrier($etabfermeCalendrier = null, $comparison = null)
	{
		if (is_array($etabfermeCalendrier)) {
			$useMinMax = false;
			if (isset($etabfermeCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER, $etabfermeCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($etabfermeCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER, $etabfermeCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER, $etabfermeCalendrier, $comparison);
	}

	/**
	 * Filter the query on the etabvacances_calendrier column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByEtabvacancesCalendrier(1234); // WHERE etabvacances_calendrier = 1234
	 * $query->filterByEtabvacancesCalendrier(array(12, 34)); // WHERE etabvacances_calendrier IN (12, 34)
	 * $query->filterByEtabvacancesCalendrier(array('min' => 12)); // WHERE etabvacances_calendrier > 12
	 * </code>
	 *
	 * @param     mixed $etabvacancesCalendrier The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByEtabvacancesCalendrier($etabvacancesCalendrier = null, $comparison = null)
	{
		if (is_array($etabvacancesCalendrier)) {
			$useMinMax = false;
			if (isset($etabvacancesCalendrier['min'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER, $etabvacancesCalendrier['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($etabvacancesCalendrier['max'])) {
				$this->addUsingAlias(EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER, $etabvacancesCalendrier['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER, $etabvacancesCalendrier, $comparison);
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		if ($edtEmplacementCours instanceof EdtEmplacementCours) {
			return $this
				->addUsingAlias(EdtCalendrierPeriodePeer::ID_CALENDRIER, $edtEmplacementCours->getIdCalendrier(), $comparison);
		} elseif ($edtEmplacementCours instanceof PropelCollection) {
			return $this
				->useEdtEmplacementCoursQuery()
					->filterByPrimaryKeys($edtEmplacementCours->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByEdtEmplacementCours() only accepts arguments of type EdtEmplacementCours or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtEmplacementCours');
		
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
			$this->addJoinObject($join, 'EdtEmplacementCours');
		}
		
		return $this;
	}

	/**
	 * Use the EdtEmplacementCours relation EdtEmplacementCours object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery A secondary query class using the current class as primary query
	 */
	public function useEdtEmplacementCoursQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtEmplacementCours($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtEmplacementCours', 'EdtEmplacementCoursQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtCalendrierPeriode $edtCalendrierPeriode Object to remove from the list of results
	 *
	 * @return    EdtCalendrierPeriodeQuery The current query, for fluid interface
	 */
	public function prune($edtCalendrierPeriode = null)
	{
		if ($edtCalendrierPeriode) {
			$this->addUsingAlias(EdtCalendrierPeriodePeer::ID_CALENDRIER, $edtCalendrierPeriode->getIdCalendrier(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseEdtCalendrierPeriodeQuery
