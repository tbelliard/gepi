<?php


/**
 * Base class that represents a query for the 'a_justifications' table.
 *
 * Liste des justifications possibles pour une absence
 *
 * @method     AbsenceEleveJustificationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveJustificationQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AbsenceEleveJustificationQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveJustificationQuery orderByOrdre($order = Criteria::ASC) Order by the ordre column
 *
 * @method     AbsenceEleveJustificationQuery groupById() Group by the id column
 * @method     AbsenceEleveJustificationQuery groupByNom() Group by the nom column
 * @method     AbsenceEleveJustificationQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveJustificationQuery groupByOrdre() Group by the ordre column
 *
 * @method     AbsenceEleveJustificationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveJustificationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveJustificationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveJustificationQuery leftJoinAbsenceEleveTraitement($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveJustificationQuery rightJoinAbsenceEleveTraitement($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveJustificationQuery innerJoinAbsenceEleveTraitement($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     AbsenceEleveJustification findOne(PropelPDO $con = null) Return the first AbsenceEleveJustification matching the query
 * @method     AbsenceEleveJustification findOneById(int $id) Return the first AbsenceEleveJustification filtered by the id column
 * @method     AbsenceEleveJustification findOneByNom(string $nom) Return the first AbsenceEleveJustification filtered by the nom column
 * @method     AbsenceEleveJustification findOneByCommentaire(string $commentaire) Return the first AbsenceEleveJustification filtered by the commentaire column
 * @method     AbsenceEleveJustification findOneByOrdre(int $ordre) Return the first AbsenceEleveJustification filtered by the ordre column
 *
 * @method     array findById(int $id) Return AbsenceEleveJustification objects filtered by the id column
 * @method     array findByNom(string $nom) Return AbsenceEleveJustification objects filtered by the nom column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveJustification objects filtered by the commentaire column
 * @method     array findByOrdre(int $ordre) Return AbsenceEleveJustification objects filtered by the ordre column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveJustificationQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveJustificationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveJustification', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveJustificationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveJustificationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveJustificationQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveJustificationQuery();
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
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveJustificationPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			return $this
				->filterByPrimaryKey($key)
				->findOne($con);
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
	 * @return    the list of results, formatted by the current formatter
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
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveJustificationPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveJustificationPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByCommentaire($commentaire = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($commentaire)) {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::COMMENTAIRE, $commentaire, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $commentaire)) {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::COMMENTAIRE, str_replace('*', '%', $commentaire), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::COMMENTAIRE, $commentaire, $comparison);
		}
	}

	/**
	 * Filter the query on the ordre column
	 * 
	 * @param     int|array $ordre The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByOrdre($ordre = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ordre)) {
			if (array_values($ordre) === $ordre) {
				return $this->addUsingAlias(AbsenceEleveJustificationPeer::ORDRE, $ordre, Criteria::IN);
			} else {
				if (isset($ordre['min'])) {
					$this->addUsingAlias(AbsenceEleveJustificationPeer::ORDRE, $ordre['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($ordre['max'])) {
					$this->addUsingAlias(AbsenceEleveJustificationPeer::ORDRE, $ordre['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveJustificationPeer::ORDRE, $ordre, $comparison);
		}
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveJustificationPeer::ID, $absenceEleveTraitement->getAJustificationId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTraitement');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveTraitement');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveTraitement relation AbsenceEleveTraitement object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTraitementQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveJustification $absenceEleveJustification Object to remove from the list of results
	 *
	 * @return    AbsenceEleveJustificationQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveJustification = null)
	{
		if ($absenceEleveJustification) {
			$this->addUsingAlias(AbsenceEleveJustificationPeer::ID, $absenceEleveJustification->getId(), Criteria::NOT_EQUAL);
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

} // BaseAbsenceEleveJustificationQuery
