<?php


/**
 * Base class that represents a query for the 'plugins_menus' table.
 *
 * Items pour construire le menu de ce plug-in
 *
 * @method     PlugInMiseEnOeuvreMenuQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     PlugInMiseEnOeuvreMenuQuery orderByPluginId($order = Criteria::ASC) Order by the plugin_id column
 * @method     PlugInMiseEnOeuvreMenuQuery orderByUserStatut($order = Criteria::ASC) Order by the user_statut column
 * @method     PlugInMiseEnOeuvreMenuQuery orderByTitreItem($order = Criteria::ASC) Order by the titre_item column
 * @method     PlugInMiseEnOeuvreMenuQuery orderByLienItem($order = Criteria::ASC) Order by the lien_item column
 * @method     PlugInMiseEnOeuvreMenuQuery orderByDescriptionItem($order = Criteria::ASC) Order by the description_item column
 *
 * @method     PlugInMiseEnOeuvreMenuQuery groupById() Group by the id column
 * @method     PlugInMiseEnOeuvreMenuQuery groupByPluginId() Group by the plugin_id column
 * @method     PlugInMiseEnOeuvreMenuQuery groupByUserStatut() Group by the user_statut column
 * @method     PlugInMiseEnOeuvreMenuQuery groupByTitreItem() Group by the titre_item column
 * @method     PlugInMiseEnOeuvreMenuQuery groupByLienItem() Group by the lien_item column
 * @method     PlugInMiseEnOeuvreMenuQuery groupByDescriptionItem() Group by the description_item column
 *
 * @method     PlugInMiseEnOeuvreMenuQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PlugInMiseEnOeuvreMenuQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PlugInMiseEnOeuvreMenuQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PlugInMiseEnOeuvreMenuQuery leftJoinPlugIn($relationAlias = '') Adds a LEFT JOIN clause to the query using the PlugIn relation
 * @method     PlugInMiseEnOeuvreMenuQuery rightJoinPlugIn($relationAlias = '') Adds a RIGHT JOIN clause to the query using the PlugIn relation
 * @method     PlugInMiseEnOeuvreMenuQuery innerJoinPlugIn($relationAlias = '') Adds a INNER JOIN clause to the query using the PlugIn relation
 *
 * @method     PlugInMiseEnOeuvreMenu findOne(PropelPDO $con = null) Return the first PlugInMiseEnOeuvreMenu matching the query
 * @method     PlugInMiseEnOeuvreMenu findOneById(int $id) Return the first PlugInMiseEnOeuvreMenu filtered by the id column
 * @method     PlugInMiseEnOeuvreMenu findOneByPluginId(int $plugin_id) Return the first PlugInMiseEnOeuvreMenu filtered by the plugin_id column
 * @method     PlugInMiseEnOeuvreMenu findOneByUserStatut(string $user_statut) Return the first PlugInMiseEnOeuvreMenu filtered by the user_statut column
 * @method     PlugInMiseEnOeuvreMenu findOneByTitreItem(string $titre_item) Return the first PlugInMiseEnOeuvreMenu filtered by the titre_item column
 * @method     PlugInMiseEnOeuvreMenu findOneByLienItem(string $lien_item) Return the first PlugInMiseEnOeuvreMenu filtered by the lien_item column
 * @method     PlugInMiseEnOeuvreMenu findOneByDescriptionItem(string $description_item) Return the first PlugInMiseEnOeuvreMenu filtered by the description_item column
 *
 * @method     array findById(int $id) Return PlugInMiseEnOeuvreMenu objects filtered by the id column
 * @method     array findByPluginId(int $plugin_id) Return PlugInMiseEnOeuvreMenu objects filtered by the plugin_id column
 * @method     array findByUserStatut(string $user_statut) Return PlugInMiseEnOeuvreMenu objects filtered by the user_statut column
 * @method     array findByTitreItem(string $titre_item) Return PlugInMiseEnOeuvreMenu objects filtered by the titre_item column
 * @method     array findByLienItem(string $lien_item) Return PlugInMiseEnOeuvreMenu objects filtered by the lien_item column
 * @method     array findByDescriptionItem(string $description_item) Return PlugInMiseEnOeuvreMenu objects filtered by the description_item column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePlugInMiseEnOeuvreMenuQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BasePlugInMiseEnOeuvreMenuQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'PlugInMiseEnOeuvreMenu', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new PlugInMiseEnOeuvreMenuQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof PlugInMiseEnOeuvreMenuQuery) {
			return $criteria;
		}
		$query = new PlugInMiseEnOeuvreMenuQuery();
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
		if ((null !== ($obj = PlugInMiseEnOeuvreMenuPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the plugin_id column
	 * 
	 * @param     int|array $pluginId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByPluginId($pluginId = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($pluginId)) {
			if (array_values($pluginId) === $pluginId) {
				return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $pluginId, Criteria::IN);
			} else {
				if (isset($pluginId['min'])) {
					$this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $pluginId['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($pluginId['max'])) {
					$this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $pluginId['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $pluginId, $comparison);
		}
	}

	/**
	 * Filter the query on the user_statut column
	 * 
	 * @param     string $userStatut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByUserStatut($userStatut = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($userStatut)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::USER_STATUT, $userStatut, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $userStatut)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::USER_STATUT, str_replace('*', '%', $userStatut), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::USER_STATUT, $userStatut, $comparison);
		}
	}

	/**
	 * Filter the query on the titre_item column
	 * 
	 * @param     string $titreItem The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByTitreItem($titreItem = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($titreItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM, $titreItem, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $titreItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM, str_replace('*', '%', $titreItem), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM, $titreItem, $comparison);
		}
	}

	/**
	 * Filter the query on the lien_item column
	 * 
	 * @param     string $lienItem The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByLienItem($lienItem = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($lienItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM, $lienItem, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $lienItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM, str_replace('*', '%', $lienItem), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM, $lienItem, $comparison);
		}
	}

	/**
	 * Filter the query on the description_item column
	 * 
	 * @param     string $descriptionItem The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByDescriptionItem($descriptionItem = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($descriptionItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM, $descriptionItem, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $descriptionItem)) {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM, str_replace('*', '%', $descriptionItem), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM, $descriptionItem, $comparison);
		}
	}

	/**
	 * Filter the query by a related PlugIn object
	 *
	 * @param     PlugIn $plugIn  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function filterByPlugIn($plugIn, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $plugIn->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the PlugIn relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function joinPlugIn($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PlugIn');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	 * @param     PlugInMiseEnOeuvreMenu $plugInMiseEnOeuvreMenu Object to remove from the list of results
	 *
	 * @return    PlugInMiseEnOeuvreMenuQuery The current query, for fluid interface
	 */
	public function prune($plugInMiseEnOeuvreMenu = null)
	{
		if ($plugInMiseEnOeuvreMenu) {
			$this->addUsingAlias(PlugInMiseEnOeuvreMenuPeer::ID, $plugInMiseEnOeuvreMenu->getId(), Criteria::NOT_EQUAL);
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

} // BasePlugInMiseEnOeuvreMenuQuery
