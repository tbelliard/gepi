<?php


/**
 * Base class that represents a query for the 'periodes' table.
 *
 * Table regroupant les periodes de notes pour les classes
 *
 * @method     PeriodesQuery orderByNomPeriode($order = Criteria::ASC) Order by the nom_periode column
 * @method     PeriodesQuery orderByNumPeriode($order = Criteria::ASC) Order by the num_periode column
 * @method     PeriodesQuery orderByVerouiller($order = Criteria::ASC) Order by the verouiller column
 * @method     PeriodesQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 * @method     PeriodesQuery orderByDateVerrouillage($order = Criteria::ASC) Order by the date_verrouillage column
 *
 * @method     PeriodesQuery groupByNomPeriode() Group by the nom_periode column
 * @method     PeriodesQuery groupByNumPeriode() Group by the num_periode column
 * @method     PeriodesQuery groupByVerouiller() Group by the verouiller column
 * @method     PeriodesQuery groupByIdClasse() Group by the id_classe column
 * @method     PeriodesQuery groupByDateVerrouillage() Group by the date_verrouillage column
 *
 * @method     PeriodesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PeriodesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PeriodesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PeriodesQuery leftJoinClasse($relationAlias = '') Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     PeriodesQuery rightJoinClasse($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     PeriodesQuery innerJoinClasse($relationAlias = '') Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     Periodes findOne(PropelPDO $con = null) Return the first Periodes matching the query
 * @method     Periodes findOneByNomPeriode(string $nom_periode) Return the first Periodes filtered by the nom_periode column
 * @method     Periodes findOneByNumPeriode(int $num_periode) Return the first Periodes filtered by the num_periode column
 * @method     Periodes findOneByVerouiller(string $verouiller) Return the first Periodes filtered by the verouiller column
 * @method     Periodes findOneByIdClasse(int $id_classe) Return the first Periodes filtered by the id_classe column
 * @method     Periodes findOneByDateVerrouillage(string $date_verrouillage) Return the first Periodes filtered by the date_verrouillage column
 *
 * @method     array findByNomPeriode(string $nom_periode) Return Periodes objects filtered by the nom_periode column
 * @method     array findByNumPeriode(int $num_periode) Return Periodes objects filtered by the num_periode column
 * @method     array findByVerouiller(string $verouiller) Return Periodes objects filtered by the verouiller column
 * @method     array findByIdClasse(int $id_classe) Return Periodes objects filtered by the id_classe column
 * @method     array findByDateVerrouillage(string $date_verrouillage) Return Periodes objects filtered by the date_verrouillage column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePeriodesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BasePeriodesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Periodes', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PeriodesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PeriodesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PeriodesQuery) {
			return $criteria;
		}
		$query = new PeriodesQuery();
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
	 * $obj = $c->findPk(array(12, 34, 56), $con);
	 * </code>
	 * @param     array[$nom_periode, $num_periode, $id_classe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Periodes|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = PeriodesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
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
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(PeriodesPeer::NOM_PERIODE, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(PeriodesPeer::NUM_PERIODE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(PeriodesPeer::ID_CLASSE, $key[2], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(PeriodesPeer::NOM_PERIODE, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(PeriodesPeer::NUM_PERIODE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(PeriodesPeer::ID_CLASSE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the nom_periode column
	 * 
	 * @param     string $nomPeriode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByNomPeriode($nomPeriode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nomPeriode)) {
			return $this->addUsingAlias(PeriodesPeer::NOM_PERIODE, $nomPeriode, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nomPeriode)) {
			return $this->addUsingAlias(PeriodesPeer::NOM_PERIODE, str_replace('*', '%', $nomPeriode), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PeriodesPeer::NOM_PERIODE, $nomPeriode, $comparison);
		}
	}

	/**
	 * Filter the query on the num_periode column
	 * 
	 * @param     int|array $numPeriode The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByNumPeriode($numPeriode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numPeriode)) {
			return $this->addUsingAlias(PeriodesPeer::NUM_PERIODE, $numPeriode, Criteria::IN);
		} else {
			return $this->addUsingAlias(PeriodesPeer::NUM_PERIODE, $numPeriode, $comparison);
		}
	}

	/**
	 * Filter the query on the verouiller column
	 * 
	 * @param     string $verouiller The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByVerouiller($verouiller = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($verouiller)) {
			return $this->addUsingAlias(PeriodesPeer::VEROUILLER, $verouiller, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $verouiller)) {
			return $this->addUsingAlias(PeriodesPeer::VEROUILLER, str_replace('*', '%', $verouiller), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PeriodesPeer::VEROUILLER, $verouiller, $comparison);
		}
	}

	/**
	 * Filter the query on the id_classe column
	 * 
	 * @param     int|array $idClasse The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idClasse)) {
			return $this->addUsingAlias(PeriodesPeer::ID_CLASSE, $idClasse, Criteria::IN);
		} else {
			return $this->addUsingAlias(PeriodesPeer::ID_CLASSE, $idClasse, $comparison);
		}
	}

	/**
	 * Filter the query on the date_verrouillage column
	 * 
	 * @param     string|array $dateVerrouillage The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByDateVerrouillage($dateVerrouillage = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($dateVerrouillage)) {
			if (array_values($dateVerrouillage) === $dateVerrouillage) {
				return $this->addUsingAlias(PeriodesPeer::DATE_VERROUILLAGE, $dateVerrouillage, Criteria::IN);
			} else {
				if (isset($dateVerrouillage['min'])) {
					$this->addUsingAlias(PeriodesPeer::DATE_VERROUILLAGE, $dateVerrouillage['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($dateVerrouillage['max'])) {
					$this->addUsingAlias(PeriodesPeer::DATE_VERROUILLAGE, $dateVerrouillage['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(PeriodesPeer::DATE_VERROUILLAGE, $dateVerrouillage, $comparison);
		}
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe $classe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(PeriodesPeer::ID_CLASSE, $classe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Classe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Classe');
		}
		
		return $this;
	}

	/**
	 * Use the Classe relation Classe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery A secondary query class using the current class as primary query
	 */
	public function useClasseQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Periodes $periodes Object to remove from the list of results
	 *
	 * @return    PeriodesQuery The current query, for fluid interface
	 */
	public function prune($periodes = null)
	{
		if ($periodes) {
			$this->addCond('pruneCond0', $this->getAliasedColName(PeriodesPeer::NOM_PERIODE), $periodes->getNomPeriode(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(PeriodesPeer::NUM_PERIODE), $periodes->getNumPeriode(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(PeriodesPeer::ID_CLASSE), $periodes->getIdClasse(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

	/**
	 * Code to execute before every SELECT statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreSelect(PropelPDO $con)
	{
		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 * 
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		return $this->preDelete($con);
	}

	/**
	 * Code to execute before every UPDATE statement
	 * 
	 * @param     array $values The associatiove array of columns and values for the update
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreUpdate(&$values, PropelPDO $con)
	{
		return $this->preUpdate($values, $con);
	}

} // BasePeriodesQuery
