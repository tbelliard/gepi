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
 * @method     EdtSalleQuery leftJoinEdtEmplacementCours($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtSalleQuery rightJoinEdtEmplacementCours($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     EdtSalleQuery innerJoinEdtEmplacementCours($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     EdtSalle findOne(PropelPDO $con = null) Return the first EdtSalle matching the query
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
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
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
	 * @param     int|array $idSalle The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByIdSalle($idSalle = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idSalle)) {
			return $this->addUsingAlias(EdtSallePeer::ID_SALLE, $idSalle, Criteria::IN);
		} else {
			return $this->addUsingAlias(EdtSallePeer::ID_SALLE, $idSalle, $comparison);
		}
	}

	/**
	 * Filter the query on the numero_salle column
	 * 
	 * @param     string $numeroSalle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByNumeroSalle($numeroSalle = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numeroSalle)) {
			return $this->addUsingAlias(EdtSallePeer::NUMERO_SALLE, $numeroSalle, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $numeroSalle)) {
			return $this->addUsingAlias(EdtSallePeer::NUMERO_SALLE, str_replace('*', '%', $numeroSalle), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtSallePeer::NUMERO_SALLE, $numeroSalle, $comparison);
		}
	}

	/**
	 * Filter the query on the nom_salle column
	 * 
	 * @param     string $nomSalle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByNomSalle($nomSalle = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nomSalle)) {
			return $this->addUsingAlias(EdtSallePeer::NOM_SALLE, $nomSalle, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nomSalle)) {
			return $this->addUsingAlias(EdtSallePeer::NOM_SALLE, str_replace('*', '%', $nomSalle), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(EdtSallePeer::NOM_SALLE, $nomSalle, $comparison);
		}
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(EdtSallePeer::ID_SALLE, $edtEmplacementCours->getIdSalle(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtSalleQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtEmplacementCours');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useEdtEmplacementCoursQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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

} // BaseEdtSalleQuery
