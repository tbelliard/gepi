<?php


/**
 * Base class that represents a query for the 'salle_cours' table.
 *
 * Liste des salles de classe
 *
 * @method     EdtSalleQuery orderByIdSalle($order = Criteria::ASC) Order by the id_salle column
 * @method     EdtSalleQuery orderByNumeroSalle($order = Criteria::ASC) Order by the numero_salle column
 * @method     EdtSalleQuery orderByNomSalle($order = Criteria::ASC) Order by the nom_salle column
 *
 * @method     EdtSalleQuery groupByIdSalle() Group by the id_salle column
 * @method     EdtSalleQuery groupByNumeroSalle() Group by the numero_salle column
 * @method     EdtSalleQuery groupByNomSalle() Group by the nom_salle column
 *
 * @method     EdtSalleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EdtSalleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EdtSalleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EdtSalleQuery leftJoinEdtEmplacementCours($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtSalleQuery rightJoinEdtEmplacementCours($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtSalleQuery innerJoinEdtEmplacementCours($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     EdtSalle findOne(PropelPDO $con = null) Return the first EdtSalle matching the query
 * @method     EdtSalle findOneOrCreate(PropelPDO $con = null) Return the first EdtSalle matching the query, or a new EdtSalle object populated from the query conditions when no match is found
 *
 * @method     EdtSalle findOneByIdSalle(int $id_salle) Return the first EdtSalle filtered by the id_salle column
 * @method     EdtSalle findOneByNumeroSalle(string $numero_salle) Return the first EdtSalle filtered by the numero_salle column
 * @method     EdtSalle findOneByNomSalle(string $nom_salle) Return the first EdtSalle filtered by the nom_salle column
 *
 * @method     array findByIdSalle(int $id_salle) Return EdtSalle objects filtered by the id_salle column
 * @method     array findByNumeroSalle(string $numero_salle) Return EdtSalle objects filtered by the numero_salle column
 * @method     array findByNomSalle(string $nom_salle) Return EdtSalle objects filtered by the nom_salle column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtSalleQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEdtSalleQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'EdtSalle', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EdtSalleQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EdtSalleQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EdtSalleQuery) {
			return $criteria;
		}
		$query = new EdtSalleQuery();
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
	 * @return    EdtSalle|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = EdtSallePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(EdtSallePeer::ID_SALLE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(EdtSallePeer::ID_SALLE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_salle column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdSalle(1234); // WHERE id_salle = 1234
	 * $query->filterByIdSalle(array(12, 34)); // WHERE id_salle IN (12, 34)
	 * $query->filterByIdSalle(array('min' => 12)); // WHERE id_salle > 12
	 * </code>
	 *
	 * @param     mixed $idSalle The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByIdSalle($idSalle = null, $comparison = null)
	{
		if (is_array($idSalle) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(EdtSallePeer::ID_SALLE, $idSalle, $comparison);
	}

	/**
	 * Filter the query on the numero_salle column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNumeroSalle('fooValue');   // WHERE numero_salle = 'fooValue'
	 * $query->filterByNumeroSalle('%fooValue%'); // WHERE numero_salle LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $numeroSalle The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByNumeroSalle($numeroSalle = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($numeroSalle)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $numeroSalle)) {
				$numeroSalle = str_replace('*', '%', $numeroSalle);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtSallePeer::NUMERO_SALLE, $numeroSalle, $comparison);
	}

	/**
	 * Filter the query on the nom_salle column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNomSalle('fooValue');   // WHERE nom_salle = 'fooValue'
	 * $query->filterByNomSalle('%fooValue%'); // WHERE nom_salle LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomSalle The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByNomSalle($nomSalle = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomSalle)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomSalle)) {
				$nomSalle = str_replace('*', '%', $nomSalle);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(EdtSallePeer::NOM_SALLE, $nomSalle, $comparison);
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		if ($edtEmplacementCours instanceof EdtEmplacementCours) {
			return $this
				->addUsingAlias(EdtSallePeer::ID_SALLE, $edtEmplacementCours->getIdSalle(), $comparison);
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
	 * @return    EdtSalleQuery The current query, for fluid interface
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
	 * @param     EdtSalle $edtSalle Object to remove from the list of results
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function prune($edtSalle = null)
	{
		if ($edtSalle) {
			$this->addUsingAlias(EdtSallePeer::ID_SALLE, $edtSalle->getIdSalle(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseEdtSalleQuery
