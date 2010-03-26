<?php


/**
 * Base class that represents a query for the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * @method     AbsenceEleveTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveTypeQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AbsenceEleveTypeQuery orderByJustificationExigible($order = Criteria::ASC) Order by the justification_exigible column
 * @method     AbsenceEleveTypeQuery orderByResponabiliteEtablissement($order = Criteria::ASC) Order by the responabilite_etablissement column
 * @method     AbsenceEleveTypeQuery orderByTypeSaisie($order = Criteria::ASC) Order by the type_saisie column
 * @method     AbsenceEleveTypeQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveTypeQuery orderBySortableRank($order = Criteria::ASC) Order by the sortable_rank column
 *
 * @method     AbsenceEleveTypeQuery groupById() Group by the id column
 * @method     AbsenceEleveTypeQuery groupByNom() Group by the nom column
 * @method     AbsenceEleveTypeQuery groupByJustificationExigible() Group by the justification_exigible column
 * @method     AbsenceEleveTypeQuery groupByResponabiliteEtablissement() Group by the responabilite_etablissement column
 * @method     AbsenceEleveTypeQuery groupByTypeSaisie() Group by the type_saisie column
 * @method     AbsenceEleveTypeQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveTypeQuery groupBySortableRank() Group by the sortable_rank column
 *
 * @method     AbsenceEleveTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveTypeQuery leftJoinAbsenceEleveTypeStatutAutorise($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 * @method     AbsenceEleveTypeQuery rightJoinAbsenceEleveTypeStatutAutorise($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 * @method     AbsenceEleveTypeQuery innerJoinAbsenceEleveTypeStatutAutorise($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 *
 * @method     AbsenceEleveTypeQuery leftJoinAbsenceEleveTraitement($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveTypeQuery rightJoinAbsenceEleveTraitement($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveTypeQuery innerJoinAbsenceEleveTraitement($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     AbsenceEleveType findOne(PropelPDO $con = null) Return the first AbsenceEleveType matching the query
 * @method     AbsenceEleveType findOneById(int $id) Return the first AbsenceEleveType filtered by the id column
 * @method     AbsenceEleveType findOneByNom(string $nom) Return the first AbsenceEleveType filtered by the nom column
 * @method     AbsenceEleveType findOneByJustificationExigible(boolean $justification_exigible) Return the first AbsenceEleveType filtered by the justification_exigible column
 * @method     AbsenceEleveType findOneByResponabiliteEtablissement(boolean $responabilite_etablissement) Return the first AbsenceEleveType filtered by the responabilite_etablissement column
 * @method     AbsenceEleveType findOneByTypeSaisie(string $type_saisie) Return the first AbsenceEleveType filtered by the type_saisie column
 * @method     AbsenceEleveType findOneByCommentaire(string $commentaire) Return the first AbsenceEleveType filtered by the commentaire column
 * @method     AbsenceEleveType findOneBySortableRank(int $sortable_rank) Return the first AbsenceEleveType filtered by the sortable_rank column
 *
 * @method     array findById(int $id) Return AbsenceEleveType objects filtered by the id column
 * @method     array findByNom(string $nom) Return AbsenceEleveType objects filtered by the nom column
 * @method     array findByJustificationExigible(boolean $justification_exigible) Return AbsenceEleveType objects filtered by the justification_exigible column
 * @method     array findByResponabiliteEtablissement(boolean $responabilite_etablissement) Return AbsenceEleveType objects filtered by the responabilite_etablissement column
 * @method     array findByTypeSaisie(string $type_saisie) Return AbsenceEleveType objects filtered by the type_saisie column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveType objects filtered by the commentaire column
 * @method     array findBySortableRank(int $sortable_rank) Return AbsenceEleveType objects filtered by the sortable_rank column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTypeQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveTypeQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveType', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveTypeQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveTypeQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveTypeQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveTypeQuery();
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
		if ((null !== ($obj = AbsenceEleveTypePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypePeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the justification_exigible column
	 * 
	 * @param     boolean|string $justificationExigible The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByJustificationExigible($justificationExigible = null, $comparison = Criteria::EQUAL)
	{
		if(is_string($justificationExigible)) {
			$justification_exigible = in_array(strtolower($justificationExigible), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE, $justificationExigible, $comparison);
	}

	/**
	 * Filter the query on the responabilite_etablissement column
	 * 
	 * @param     boolean|string $responabiliteEtablissement The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByResponabiliteEtablissement($responabiliteEtablissement = null, $comparison = Criteria::EQUAL)
	{
		if(is_string($responabiliteEtablissement)) {
			$responabilite_etablissement = in_array(strtolower($responabiliteEtablissement), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::RESPONABILITE_ETABLISSEMENT, $responabiliteEtablissement, $comparison);
	}

	/**
	 * Filter the query on the type_saisie column
	 * 
	 * @param     string $typeSaisie The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByTypeSaisie($typeSaisie = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($typeSaisie)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::TYPE_SAISIE, $typeSaisie, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $typeSaisie)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::TYPE_SAISIE, str_replace('*', '%', $typeSaisie), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypePeer::TYPE_SAISIE, $typeSaisie, $comparison);
		}
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByCommentaire($commentaire = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($commentaire)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::COMMENTAIRE, $commentaire, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $commentaire)) {
			return $this->addUsingAlias(AbsenceEleveTypePeer::COMMENTAIRE, str_replace('*', '%', $commentaire), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypePeer::COMMENTAIRE, $commentaire, $comparison);
		}
	}

	/**
	 * Filter the query on the sortable_rank column
	 * 
	 * @param     int|array $sortableRank The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterBySortableRank($sortableRank = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($sortableRank)) {
			if (array_values($sortableRank) === $sortableRank) {
				return $this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank, Criteria::IN);
			} else {
				if (isset($sortableRank['min'])) {
					$this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($sortableRank['max'])) {
					$this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank, $comparison);
		}
	}

	/**
	 * Filter the query by a related AbsenceEleveTypeStatutAutorise object
	 *
	 * @param     AbsenceEleveTypeStatutAutorise $absenceEleveTypeStatutAutorise  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTypeStatutAutorise($absenceEleveTypeStatutAutorise, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveTypeStatutAutorise->getIdAType(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTypeStatutAutorise($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTypeStatutAutorise');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveTypeStatutAutorise');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveTypeStatutAutorise relation AbsenceEleveTypeStatutAutorise object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeStatutAutoriseQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTypeStatutAutorise($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTypeStatutAutorise', 'AbsenceEleveTypeStatutAutoriseQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveTraitement->getATypeId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
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
	 * @param     AbsenceEleveType $absenceEleveType Object to remove from the list of results
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveType = null)
	{
		if ($absenceEleveType) {
			$this->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveType->getId(), Criteria::NOT_EQUAL);
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

	// sortable behavior
	
	/**
	 * Filter the query based on a rank in the list
	 *
	 * @param     integer   $rank rank
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByRank($rank)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::RANK_COL, $rank, Criteria::EQUAL);
	}
	
	/**
	 * Order the query based on the rank in the list.
	 * Using the default $order, returns the item with the lowest rank first
	 *
	 * @param     string $order either Criteria::ASC (default) or Criteria::DESC
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function orderByRank($order = Criteria::ASC)
	{
		$order = strtoupper($order);
		switch ($order) {
			case Criteria::ASC:
				return $this->addAscendingOrderByColumn($this->getAliasedColName(AbsenceEleveTypePeer::RANK_COL));
				break;
			case Criteria::DESC:
				return $this->addDescendingOrderByColumn($this->getAliasedColName(AbsenceEleveTypePeer::RANK_COL));
				break;
			default:
				throw new PropelException('AbsenceEleveTypeQuery::orderBy() only accepts "asc" or "desc" as argument');
		}
	}
	
	/**
	 * Get an item from the list based on its rank
	 *
	 * @param     integer   $rank rank
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType
	 */
	public function findOneByRank($rank, PropelPDO $con = null)
	{
		return $this
			->filterByRank($rank)
			->findOne($con);
	}
	
	/**
	 * Returns the list of objects
	 *
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     mixed the list of results, formatted by the current formatter
	 */
	public function findList($con = null)
	{
		return $this
			->orderByRank()
			->find($con);
	}
	
	/**
	 * Get the highest rank
	 * 
	 * @param     PropelPDO optional connection
	 *
	 * @return    integer highest position
	 */
	public function getMaxRank(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		// shift the objects with a position lower than the one of object
		$this->addSelectColumn('MAX(' . AbsenceEleveTypePeer::RANK_COL . ')');
		$stmt = $this->getSelectStatement($con);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * Reorder a set of sortable objects based on a list of id/position
	 * Beware that there is no check made on the positions passed
	 * So incoherent positions will result in an incoherent list
	 *
	 * @param     array     $order id => rank pairs
	 * @param     PropelPDO $con   optional connection
	 *
	 * @return    boolean true if the reordering took place, false if a database problem prevented it
	 */
	public function reorder(array $order, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		
		$con->beginTransaction();
		try {
			$ids = array_keys($order);
			$objects = $this->findPks($ids, $con);
			foreach ($objects as $object) {
				$pk = $object->getPrimaryKey();
				if ($object->getSortableRank() != $order[$pk]) {
					$object->setSortableRank($order[$pk]);
					$object->save($con);
				}
			}
			$con->commit();
	
			return true;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

} // BaseAbsenceEleveTypeQuery
