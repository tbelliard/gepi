<?php


/**
 * Base class that represents a query for the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * @method     EdtCreneauQuery orderByIdDefiniePeriode($order = Criteria::ASC) Order by the id_definie_periode column
 * @method     EdtCreneauQuery orderByNomDefiniePeriode($order = Criteria::ASC) Order by the nom_definie_periode column
 * @method     EdtCreneauQuery orderByHeuredebutDefiniePeriode($order = Criteria::ASC) Order by the heuredebut_definie_periode column
 * @method     EdtCreneauQuery orderByHeurefinDefiniePeriode($order = Criteria::ASC) Order by the heurefin_definie_periode column
 * @method     EdtCreneauQuery orderBySuiviDefiniePeriode($order = Criteria::ASC) Order by the suivi_definie_periode column
 * @method     EdtCreneauQuery orderByTypeCreneaux($order = Criteria::ASC) Order by the type_creneaux column
 * @method     EdtCreneauQuery orderByJourCreneau($order = Criteria::ASC) Order by the jour_creneau column
 *
 * @method     EdtCreneauQuery groupByIdDefiniePeriode() Group by the id_definie_periode column
 * @method     EdtCreneauQuery groupByNomDefiniePeriode() Group by the nom_definie_periode column
 * @method     EdtCreneauQuery groupByHeuredebutDefiniePeriode() Group by the heuredebut_definie_periode column
 * @method     EdtCreneauQuery groupByHeurefinDefiniePeriode() Group by the heurefin_definie_periode column
 * @method     EdtCreneauQuery groupBySuiviDefiniePeriode() Group by the suivi_definie_periode column
 * @method     EdtCreneauQuery groupByTypeCreneaux() Group by the type_creneaux column
 * @method     EdtCreneauQuery groupByJourCreneau() Group by the jour_creneau column
 *
 * @method     EdtCreneauQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtCreneauQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtCreneauQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtCreneauQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EdtCreneauQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EdtCreneauQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     EdtCreneauQuery leftJoinEdtEmplacementCours($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtCreneauQuery rightJoinEdtEmplacementCours($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtCreneauQuery innerJoinEdtEmplacementCours($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     EdtCreneau findOne(PropelPDO $con = null) Return the first EdtCreneau matching the query
 * @method     EdtCreneau findOneOrCreate(PropelPDO $con = null) Return the first EdtCreneau matching the query, or a new EdtCreneau object populated from the query conditions when no match is found
 *
 * @method     EdtCreneau findOneByIdDefiniePeriode(int $id_definie_periode) Return the first EdtCreneau filtered by the id_definie_periode column
 * @method     EdtCreneau findOneByNomDefiniePeriode(string $nom_definie_periode) Return the first EdtCreneau filtered by the nom_definie_periode column
 * @method     EdtCreneau findOneByHeuredebutDefiniePeriode(string $heuredebut_definie_periode) Return the first EdtCreneau filtered by the heuredebut_definie_periode column
 * @method     EdtCreneau findOneByHeurefinDefiniePeriode(string $heurefin_definie_periode) Return the first EdtCreneau filtered by the heurefin_definie_periode column
 * @method     EdtCreneau findOneBySuiviDefiniePeriode(int $suivi_definie_periode) Return the first EdtCreneau filtered by the suivi_definie_periode column
 * @method     EdtCreneau findOneByTypeCreneaux(string $type_creneaux) Return the first EdtCreneau filtered by the type_creneaux column
 * @method     EdtCreneau findOneByJourCreneau(string $jour_creneau) Return the first EdtCreneau filtered by the jour_creneau column
 *
 * @method     array findByIdDefiniePeriode(int $id_definie_periode) Return EdtCreneau objects filtered by the id_definie_periode column
 * @method     array findByNomDefiniePeriode(string $nom_definie_periode) Return EdtCreneau objects filtered by the nom_definie_periode column
 * @method     array findByHeuredebutDefiniePeriode(string $heuredebut_definie_periode) Return EdtCreneau objects filtered by the heuredebut_definie_periode column
 * @method     array findByHeurefinDefiniePeriode(string $heurefin_definie_periode) Return EdtCreneau objects filtered by the heurefin_definie_periode column
 * @method     array findBySuiviDefiniePeriode(int $suivi_definie_periode) Return EdtCreneau objects filtered by the suivi_definie_periode column
 * @method     array findByTypeCreneaux(string $type_creneaux) Return EdtCreneau objects filtered by the type_creneaux column
 * @method     array findByJourCreneau(string $jour_creneau) Return EdtCreneau objects filtered by the jour_creneau column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtCreneauQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseEdtCreneauQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtCreneau', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtCreneauQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtCreneauQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtCreneauQuery) {
			return $criteria;
		}
		$query = new EdtCreneauQuery();
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
	 * @return    EdtCreneau|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = EdtCreneauPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(EdtCreneauPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    EdtCreneau A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID_DEFINIE_PERIODE, NOM_DEFINIE_PERIODE, HEUREDEBUT_DEFINIE_PERIODE, HEUREFIN_DEFINIE_PERIODE, SUIVI_DEFINIE_PERIODE, TYPE_CRENEAUX, JOUR_CRENEAU FROM edt_creneaux WHERE ID_DEFINIE_PERIODE = :p0';
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
			$obj = new EdtCreneau();
			$obj->hydrate($row);
			EdtCreneauPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    EdtCreneau|array|mixed the result, formatted by the current formatter
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
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_definie_periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdDefiniePeriode(1234); // WHERE id_definie_periode = 1234
	 * $query->filterByIdDefiniePeriode(array(12, 34)); // WHERE id_definie_periode IN (12, 34)
	 * $query->filterByIdDefiniePeriode(array('min' => 12)); // WHERE id_definie_periode > 12
	 * </code>
	 *
	 * @param     mixed $idDefiniePeriode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByIdDefiniePeriode($idDefiniePeriode = null, $comparison = null)
	{
		if (is_array($idDefiniePeriode) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $idDefiniePeriode, $comparison);
	}

	/**
	 * Filter the query on the nom_definie_periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNomDefiniePeriode('fooValue');   // WHERE nom_definie_periode = 'fooValue'
	 * $query->filterByNomDefiniePeriode('%fooValue%'); // WHERE nom_definie_periode LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomDefiniePeriode The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByNomDefiniePeriode($nomDefiniePeriode = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomDefiniePeriode)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomDefiniePeriode)) {
				$nomDefiniePeriode = str_replace('*', '%', $nomDefiniePeriode);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::NOM_DEFINIE_PERIODE, $nomDefiniePeriode, $comparison);
	}

	/**
	 * Filter the query on the heuredebut_definie_periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByHeuredebutDefiniePeriode('2011-03-14'); // WHERE heuredebut_definie_periode = '2011-03-14'
	 * $query->filterByHeuredebutDefiniePeriode('now'); // WHERE heuredebut_definie_periode = '2011-03-14'
	 * $query->filterByHeuredebutDefiniePeriode(array('max' => 'yesterday')); // WHERE heuredebut_definie_periode > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $heuredebutDefiniePeriode The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByHeuredebutDefiniePeriode($heuredebutDefiniePeriode = null, $comparison = null)
	{
		if (is_array($heuredebutDefiniePeriode)) {
			$useMinMax = false;
			if (isset($heuredebutDefiniePeriode['min'])) {
				$this->addUsingAlias(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE, $heuredebutDefiniePeriode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($heuredebutDefiniePeriode['max'])) {
				$this->addUsingAlias(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE, $heuredebutDefiniePeriode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE, $heuredebutDefiniePeriode, $comparison);
	}

	/**
	 * Filter the query on the heurefin_definie_periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByHeurefinDefiniePeriode('2011-03-14'); // WHERE heurefin_definie_periode = '2011-03-14'
	 * $query->filterByHeurefinDefiniePeriode('now'); // WHERE heurefin_definie_periode = '2011-03-14'
	 * $query->filterByHeurefinDefiniePeriode(array('max' => 'yesterday')); // WHERE heurefin_definie_periode > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $heurefinDefiniePeriode The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByHeurefinDefiniePeriode($heurefinDefiniePeriode = null, $comparison = null)
	{
		if (is_array($heurefinDefiniePeriode)) {
			$useMinMax = false;
			if (isset($heurefinDefiniePeriode['min'])) {
				$this->addUsingAlias(EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE, $heurefinDefiniePeriode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($heurefinDefiniePeriode['max'])) {
				$this->addUsingAlias(EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE, $heurefinDefiniePeriode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE, $heurefinDefiniePeriode, $comparison);
	}

	/**
	 * Filter the query on the suivi_definie_periode column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterBySuiviDefiniePeriode(1234); // WHERE suivi_definie_periode = 1234
	 * $query->filterBySuiviDefiniePeriode(array(12, 34)); // WHERE suivi_definie_periode IN (12, 34)
	 * $query->filterBySuiviDefiniePeriode(array('min' => 12)); // WHERE suivi_definie_periode > 12
	 * </code>
	 *
	 * @param     mixed $suiviDefiniePeriode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterBySuiviDefiniePeriode($suiviDefiniePeriode = null, $comparison = null)
	{
		if (is_array($suiviDefiniePeriode)) {
			$useMinMax = false;
			if (isset($suiviDefiniePeriode['min'])) {
				$this->addUsingAlias(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE, $suiviDefiniePeriode['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($suiviDefiniePeriode['max'])) {
				$this->addUsingAlias(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE, $suiviDefiniePeriode['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE, $suiviDefiniePeriode, $comparison);
	}

	/**
	 * Filter the query on the type_creneaux column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTypeCreneaux('fooValue');   // WHERE type_creneaux = 'fooValue'
	 * $query->filterByTypeCreneaux('%fooValue%'); // WHERE type_creneaux LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $typeCreneaux The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByTypeCreneaux($typeCreneaux = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($typeCreneaux)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $typeCreneaux)) {
				$typeCreneaux = str_replace('*', '%', $typeCreneaux);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::TYPE_CRENEAUX, $typeCreneaux, $comparison);
	}

	/**
	 * Filter the query on the jour_creneau column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByJourCreneau('fooValue');   // WHERE jour_creneau = 'fooValue'
	 * $query->filterByJourCreneau('%fooValue%'); // WHERE jour_creneau LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $jourCreneau The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByJourCreneau($jourCreneau = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($jourCreneau)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $jourCreneau)) {
				$jourCreneau = str_replace('*', '%', $jourCreneau);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtCreneauPeer::JOUR_CRENEAU, $jourCreneau, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		if ($absenceEleveSaisie instanceof AbsenceEleveSaisie) {
			return $this
				->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $absenceEleveSaisie->getIdEdtCreneau(), $comparison);
		} elseif ($absenceEleveSaisie instanceof PropelCollection) {
			return $this
				->useAbsenceEleveSaisieQuery()
				->filterByPrimaryKeys($absenceEleveSaisie->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAbsenceEleveSaisie() only accepts arguments of type AbsenceEleveSaisie or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisie');

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
			$this->addJoinObject($join, 'AbsenceEleveSaisie');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveSaisie relation AbsenceEleveSaisie object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveSaisieQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		if ($edtEmplacementCours instanceof EdtEmplacementCours) {
			return $this
				->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $edtEmplacementCours->getIdDefiniePeriode(), $comparison);
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
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useEdtEmplacementCoursQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEdtEmplacementCours($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtEmplacementCours', 'EdtEmplacementCoursQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     EdtCreneau $edtCreneau Object to remove from the list of results
	 *
	 * @return    EdtCreneauQuery The current query, for fluid interface
	 */
	public function prune($edtCreneau = null)
	{
		if ($edtCreneau) {
			$this->addUsingAlias(EdtCreneauPeer::ID_DEFINIE_PERIODE, $edtCreneau->getIdDefiniePeriode(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseEdtCreneauQuery