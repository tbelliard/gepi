<?php


/**
 * Base class that represents a query for the 'plugins_autorisations' table.
 *
 * Liste des autorisations pour chaque statut
 *
 * @method     PlugInAutorisationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     PlugInAutorisationQuery orderByPluginId($order = Criteria::ASC) Order by the plugin_id column
 * @method     PlugInAutorisationQuery orderByFichier($order = Criteria::ASC) Order by the fichier column
 * @method     PlugInAutorisationQuery orderByUserStatut($order = Criteria::ASC) Order by the user_statut column
 * @method     PlugInAutorisationQuery orderByAuth($order = Criteria::ASC) Order by the auth column
 *
 * @method     PlugInAutorisationQuery groupById() Group by the id column
 * @method     PlugInAutorisationQuery groupByPluginId() Group by the plugin_id column
 * @method     PlugInAutorisationQuery groupByFichier() Group by the fichier column
 * @method     PlugInAutorisationQuery groupByUserStatut() Group by the user_statut column
 * @method     PlugInAutorisationQuery groupByAuth() Group by the auth column
 *
 * @method     PlugInAutorisationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PlugInAutorisationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PlugInAutorisationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PlugInAutorisationQuery leftJoinPlugIn($relationAlias = '') Adds a LEFT JOIN clause to the query using the PlugIn relation
 * @method     PlugInAutorisationQuery rightJoinPlugIn($relationAlias = '') Adds a RIGHT JOIN clause to the query using the PlugIn relation
 * @method     PlugInAutorisationQuery innerJoinPlugIn($relationAlias = '') Adds a INNER JOIN clause to the query using the PlugIn relation
 *
 * @method     PlugInAutorisation findOne(PropelPDO $con = null) Return the first PlugInAutorisation matching the query
 * @method     PlugInAutorisation findOneOrCreate(PropelPDO $con = null) Return the first PlugInAutorisation matching the query, or a new PlugInAutorisation object populated from the query conditions when no match is found
 *
 * @method     PlugInAutorisation findOneById(int $id) Return the first PlugInAutorisation filtered by the id column
 * @method     PlugInAutorisation findOneByPluginId(int $plugin_id) Return the first PlugInAutorisation filtered by the plugin_id column
 * @method     PlugInAutorisation findOneByFichier(string $fichier) Return the first PlugInAutorisation filtered by the fichier column
 * @method     PlugInAutorisation findOneByUserStatut(string $user_statut) Return the first PlugInAutorisation filtered by the user_statut column
 * @method     PlugInAutorisation findOneByAuth(string $auth) Return the first PlugInAutorisation filtered by the auth column
 *
 * @method     array findById(int $id) Return PlugInAutorisation objects filtered by the id column
 * @method     array findByPluginId(int $plugin_id) Return PlugInAutorisation objects filtered by the plugin_id column
 * @method     array findByFichier(string $fichier) Return PlugInAutorisation objects filtered by the fichier column
 * @method     array findByUserStatut(string $user_statut) Return PlugInAutorisation objects filtered by the user_statut column
 * @method     array findByAuth(string $auth) Return PlugInAutorisation objects filtered by the auth column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePlugInAutorisationQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BasePlugInAutorisationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'PlugInAutorisation', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PlugInAutorisationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PlugInAutorisationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PlugInAutorisationQuery) {
			return $criteria;
		}
		$query = new PlugInAutorisationQuery();
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
	 * @return    PlugInAutorisation|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = PlugInAutorisationPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the plugin_id column
	 * 
	 * @param     int|array $pluginId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPluginId($pluginId = null, $comparison = null)
	{
		if (is_array($pluginId)) {
			$useMinMax = false;
			if (isset($pluginId['min'])) {
				$this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($pluginId['max'])) {
				$this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $pluginId, $comparison);
	}

	/**
	 * Filter the query on the fichier column
	 * 
	 * @param     string $fichier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByFichier($fichier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($fichier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $fichier)) {
				$fichier = str_replace('*', '%', $fichier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::FICHIER, $fichier, $comparison);
	}

	/**
	 * Filter the query on the user_statut column
	 * 
	 * @param     string $userStatut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByUserStatut($userStatut = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($userStatut)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $userStatut)) {
				$userStatut = str_replace('*', '%', $userStatut);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::USER_STATUT, $userStatut, $comparison);
	}

	/**
	 * Filter the query on the auth column
	 * 
	 * @param     string $auth The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByAuth($auth = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($auth)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $auth)) {
				$auth = str_replace('*', '%', $auth);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(PlugInAutorisationPeer::AUTH, $auth, $comparison);
	}

	/**
	 * Filter the query by a related PlugIn object
	 *
	 * @param     PlugIn $plugIn  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function filterByPlugIn($plugIn, $comparison = null)
	{
		return $this
			->addUsingAlias(PlugInAutorisationPeer::PLUGIN_ID, $plugIn->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the PlugIn relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function joinPlugIn($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PlugIn');
		
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
			$this->addJoinObject($join, 'PlugIn');
		}
		
		return $this;
	}

	/**
	 * Use the PlugIn relation PlugIn object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInQuery A secondary query class using the current class as primary query
	 */
	public function usePlugInQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPlugIn($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PlugIn', 'PlugInQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     PlugInAutorisation $plugInAutorisation Object to remove from the list of results
	 *
	 * @return    PlugInAutorisationQuery The current query, for fluid interface
	 */
	public function prune($plugInAutorisation = null)
	{
		if ($plugInAutorisation) {
			$this->addUsingAlias(PlugInAutorisationPeer::ID, $plugInAutorisation->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BasePlugInAutorisationQuery
