<?php


/**
 * Base class that represents a query for the 'ct_sequences' table.
 *
 * Sequence de plusieurs compte-rendus
 *
 * @method     CahierTexteSequenceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CahierTexteSequenceQuery orderByTitre($order = Criteria::ASC) Order by the titre column
 * @method     CahierTexteSequenceQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     CahierTexteSequenceQuery groupById() Group by the id column
 * @method     CahierTexteSequenceQuery groupByTitre() Group by the titre column
 * @method     CahierTexteSequenceQuery groupByDescription() Group by the description column
 *
 * @method     CahierTexteSequenceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteSequenceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteSequenceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteCompteRendu($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteCompteRendu($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteCompteRendu($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteCompteRendu relation
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteTravailAFaire($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteTravailAFaire($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteTravailAFaire($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteTravailAFaire relation
 *
 * @method     CahierTexteSequenceQuery leftJoinCahierTexteNoticePrivee($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     CahierTexteSequenceQuery rightJoinCahierTexteNoticePrivee($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     CahierTexteSequenceQuery innerJoinCahierTexteNoticePrivee($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteNoticePrivee relation
 *
 * @method     CahierTexteSequence findOne(PropelPDO $con = null) Return the first CahierTexteSequence matching the query
 * @method     CahierTexteSequence findOneById(int $id) Return the first CahierTexteSequence filtered by the id column
 * @method     CahierTexteSequence findOneByTitre(string $titre) Return the first CahierTexteSequence filtered by the titre column
 * @method     CahierTexteSequence findOneByDescription(string $description) Return the first CahierTexteSequence filtered by the description column
 *
 * @method     array findById(int $id) Return CahierTexteSequence objects filtered by the id column
 * @method     array findByTitre(string $titre) Return CahierTexteSequence objects filtered by the titre column
 * @method     array findByDescription(string $description) Return CahierTexteSequence objects filtered by the description column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteSequenceQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCahierTexteSequenceQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteSequence', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteSequenceQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteSequenceQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteSequenceQuery) {
			return $criteria;
		}
		$query = new CahierTexteSequenceQuery();
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
	 * @return    CahierTexteSequence|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CahierTexteSequencePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteSequencePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteSequencePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(CahierTexteSequencePeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(CahierTexteSequencePeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the titre column
	 * 
	 * @param     string $titre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByTitre($titre = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($titre)) {
			return $this->addUsingAlias(CahierTexteSequencePeer::TITRE, $titre, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $titre)) {
			return $this->addUsingAlias(CahierTexteSequencePeer::TITRE, str_replace('*', '%', $titre), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteSequencePeer::TITRE, $titre, $comparison);
		}
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $description The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByDescription($description = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($description)) {
			return $this->addUsingAlias(CahierTexteSequencePeer::DESCRIPTION, $description, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $description)) {
			return $this->addUsingAlias(CahierTexteSequencePeer::DESCRIPTION, str_replace('*', '%', $description), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteSequencePeer::DESCRIPTION, $description, $comparison);
		}
	}

	/**
	 * Filter the query by a related CahierTexteCompteRendu object
	 *
	 * @param     CahierTexteCompteRendu $cahierTexteCompteRendu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteCompteRendu($cahierTexteCompteRendu, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteCompteRendu->getIdSequence(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteCompteRendu relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteCompteRendu($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteCompteRendu');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteCompteRendu');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteCompteRendu relation CahierTexteCompteRendu object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteCompteRenduQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteCompteRendu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteCompteRendu', 'CahierTexteCompteRenduQuery');
	}

	/**
	 * Filter the query by a related CahierTexteTravailAFaire object
	 *
	 * @param     CahierTexteTravailAFaire $cahierTexteTravailAFaire  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteTravailAFaire($cahierTexteTravailAFaire, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteTravailAFaire->getIdSequence(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteTravailAFaire relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteTravailAFaire($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteTravailAFaire');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteTravailAFaire');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteTravailAFaire relation CahierTexteTravailAFaire object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteTravailAFaireQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteTravailAFaire($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteTravailAFaire', 'CahierTexteTravailAFaireQuery');
	}

	/**
	 * Filter the query by a related CahierTexteNoticePrivee object
	 *
	 * @param     CahierTexteNoticePrivee $cahierTexteNoticePrivee  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteNoticePrivee($cahierTexteNoticePrivee, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteNoticePrivee->getIdSequence(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteNoticePrivee relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function joinCahierTexteNoticePrivee($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteNoticePrivee');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteNoticePrivee');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteNoticePrivee relation CahierTexteNoticePrivee object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteNoticePriveeQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteNoticePriveeQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteNoticePrivee($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteNoticePrivee', 'CahierTexteNoticePriveeQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteSequence $cahierTexteSequence Object to remove from the list of results
	 *
	 * @return    CahierTexteSequenceQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteSequence = null)
	{
		if ($cahierTexteSequence) {
			$this->addUsingAlias(CahierTexteSequencePeer::ID, $cahierTexteSequence->getId(), Criteria::NOT_EQUAL);
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

} // BaseCahierTexteSequenceQuery
