<?php


/**
 * Base class that represents a query for the 'plugins' table.
 *
 * Liste des plugins installes sur ce Gepi
 *
 * @method     PlugInQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     PlugInQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     PlugInQuery orderByRepertoire($order = Criteria::ASC) Order by the repertoire column
 * @method     PlugInQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     PlugInQuery orderByOuvert($order = Criteria::ASC) Order by the ouvert column
 *
 * @method     PlugInQuery groupById() Group by the id column
 * @method     PlugInQuery groupByNom() Group by the nom column
 * @method     PlugInQuery groupByRepertoire() Group by the repertoire column
 * @method     PlugInQuery groupByDescription() Group by the description column
 * @method     PlugInQuery groupByOuvert() Group by the ouvert column
 *
 * @method     PlugInQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PlugInQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PlugInQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PlugInQuery leftJoinPlugInAutorisation($relationAlias = '') Adds a LEFT JOIN clause to the query using the PlugInAutorisation relation
 * @method     PlugInQuery rightJoinPlugInAutorisation($relationAlias = '') Adds a RIGHT JOIN clause to the query using the PlugInAutorisation relation
 * @method     PlugInQuery innerJoinPlugInAutorisation($relationAlias = '') Adds a INNER JOIN clause to the query using the PlugInAutorisation relation
 *
 * @method     PlugInQuery leftJoinPlugInMiseEnOeuvreMenu($relationAlias = '') Adds a LEFT JOIN clause to the query using the PlugInMiseEnOeuvreMenu relation
 * @method     PlugInQuery rightJoinPlugInMiseEnOeuvreMenu($relationAlias = '') Adds a RIGHT JOIN clause to the query using the PlugInMiseEnOeuvreMenu relation
 * @method     PlugInQuery innerJoinPlugInMiseEnOeuvreMenu($relationAlias = '') Adds a INNER JOIN clause to the query using the PlugInMiseEnOeuvreMenu relation
 *
 * @method     PlugIn findOne(PropelPDO $con = null) Return the first PlugIn matching the query
 * @method     PlugIn findOneOrCreate(PropelPDO $con = null) Return the first PlugIn matching the query, or a new PlugIn object populated from the query conditions when no match is found
 *
 * @method     PlugIn findOneById(int $id) Return the first PlugIn filtered by the id column
 * @method     PlugIn findOneByNom(string $nom) Return the first PlugIn filtered by the nom column
 * @method     PlugIn findOneByRepertoire(string $repertoire) Return the first PlugIn filtered by the repertoire column
 * @method     PlugIn findOneByDescription(string $description) Return the first PlugIn filtered by the description column
 * @method     PlugIn findOneByOuvert(string $ouvert) Return the first PlugIn filtered by the ouvert column
 *
 * @method     array findById(int $id) Return PlugIn objects filtered by the id column
 * @method     array findByNom(string $nom) Return PlugIn objects filtered by the nom column
 * @method     array findByRepertoire(string $repertoire) Return PlugIn objects filtered by the repertoire column
 * @method     array findByDescription(string $description) Return PlugIn objects filtered by the description column
 * @method     array findByOuvert(string $ouvert) Return PlugIn objects filtered by the ouvert column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePlugInQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BasePlugInQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'PlugIn', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PlugInQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PlugInQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PlugInQuery) {
			return $criteria;
		}
		$query = new PlugInQuery();
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
	 * @return    PlugIn|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = PlugInPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(PlugInPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(PlugInPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(PlugInPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nom)) {
				$nom = str_replace('*', '%', $nom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInPeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the repertoire column
	 * 
	 * @param     string $repertoire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByRepertoire($repertoire = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($repertoire)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $repertoire)) {
				$repertoire = str_replace('*', '%', $repertoire);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInPeer::REPERTOIRE, $repertoire, $comparison);
	}

	/**
	 * Filter the query on the description column
	 * 
	 * @param     string $description The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByDescription($description = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($description)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $description)) {
				$description = str_replace('*', '%', $description);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInPeer::DESCRIPTION, $description, $comparison);
	}

	/**
	 * Filter the query on the ouvert column
	 * 
	 * @param     string $ouvert The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByOuvert($ouvert = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ouvert)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ouvert)) {
				$ouvert = str_replace('*', '%', $ouvert);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInPeer::OUVERT, $ouvert, $comparison);
	}

	/**
	 * Filter the query by a related PlugInAutorisation object
	 *
	 * @param     PlugInAutorisation $plugInAutorisation  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByPlugInAutorisation($plugInAutorisation, $comparison = null)
	{
		return $this
			->addUsingAlias(PlugInPeer::ID, $plugInAutorisation->getPluginId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the PlugInAutorisation relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function joinPlugInAutorisation($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PlugInAutorisation');
		
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
			$this->addJoinObject($join, 'PlugInAutorisation');
		}
		
		return $this;
	}

	/**
	 * Use the PlugInAutorisation relation PlugInAutorisation object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInAutorisationQuery A secondary query class using the current class as primary query
	 */
	public function usePlugInAutorisationQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPlugInAutorisation($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PlugInAutorisation', 'PlugInAutorisationQuery');
	}

	/**
	 * Filter the query by a related PlugInMiseEnOeuvreMenu object
	 *
	 * @param     PlugInMiseEnOeuvreMenu $plugInMiseEnOeuvreMenu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function filterByPlugInMiseEnOeuvreMenu($plugInMiseEnOeuvreMenu, $comparison = null)
	{
		return $this
			->addUsingAlias(PlugInPeer::ID, $plugInMiseEnOeuvreMenu->getPluginId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the PlugInMiseEnOeuvreMenu relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function joinPlugInMiseEnOeuvreMenu($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PlugInMiseEnOeuvreMenu');
		
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
			$this->addJoinObject($join, 'PlugInMiseEnOeuvreMenu');
		}
		
		return $this;
	}

	/**
	 * Use the PlugInMiseEnOeuvreMenu relation PlugInMiseEnOeuvreMenu object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery A secondary query class using the current class as primary query
	 */
	public function usePlugInMiseEnOeuvreMenuQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPlugInMiseEnOeuvreMenu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PlugInMiseEnOeuvreMenu', 'PlugInMiseEnOeuvreMenuQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     PlugIn $plugIn Object to remove from the list of results
	 *
	 * @return    PlugInQuery The current query, for fluid interface
	 */
	public function prune($plugIn = null)
	{
		if ($plugIn) {
			$this->addUsingAlias(PlugInPeer::ID, $plugIn->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BasePlugInQuery
