<?php


/**
 * Base class that represents a query for the 'etablissements' table.
 *
 * Liste des etablissements precedents des eleves
 *
 * @method     AncienEtablissementQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AncienEtablissementQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AncienEtablissementQuery orderByNiveau($order = Criteria::ASC) Order by the niveau column
 * @method     AncienEtablissementQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     AncienEtablissementQuery orderByCp($order = Criteria::ASC) Order by the cp column
 * @method     AncienEtablissementQuery orderByVille($order = Criteria::ASC) Order by the ville column
 *
 * @method     AncienEtablissementQuery groupById() Group by the id column
 * @method     AncienEtablissementQuery groupByNom() Group by the nom column
 * @method     AncienEtablissementQuery groupByNiveau() Group by the niveau column
 * @method     AncienEtablissementQuery groupByType() Group by the type column
 * @method     AncienEtablissementQuery groupByCp() Group by the cp column
 * @method     AncienEtablissementQuery groupByVille() Group by the ville column
 *
 * @method     AncienEtablissementQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AncienEtablissementQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AncienEtablissementQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AncienEtablissementQuery leftJoinJEleveAncienEtablissement($relationAlias = '') Adds a LEFT JOIN clause to the query using the JEleveAncienEtablissement relation
 * @method     AncienEtablissementQuery rightJoinJEleveAncienEtablissement($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JEleveAncienEtablissement relation
 * @method     AncienEtablissementQuery innerJoinJEleveAncienEtablissement($relationAlias = '') Adds a INNER JOIN clause to the query using the JEleveAncienEtablissement relation
 *
 * @method     AncienEtablissement findOne(PropelPDO $con = null) Return the first AncienEtablissement matching the query
 * @method     AncienEtablissement findOneById(int $id) Return the first AncienEtablissement filtered by the id column
 * @method     AncienEtablissement findOneByNom(string $nom) Return the first AncienEtablissement filtered by the nom column
 * @method     AncienEtablissement findOneByNiveau(string $niveau) Return the first AncienEtablissement filtered by the niveau column
 * @method     AncienEtablissement findOneByType(string $type) Return the first AncienEtablissement filtered by the type column
 * @method     AncienEtablissement findOneByCp(int $cp) Return the first AncienEtablissement filtered by the cp column
 * @method     AncienEtablissement findOneByVille(string $ville) Return the first AncienEtablissement filtered by the ville column
 *
 * @method     array findById(int $id) Return AncienEtablissement objects filtered by the id column
 * @method     array findByNom(string $nom) Return AncienEtablissement objects filtered by the nom column
 * @method     array findByNiveau(string $niveau) Return AncienEtablissement objects filtered by the niveau column
 * @method     array findByType(string $type) Return AncienEtablissement objects filtered by the type column
 * @method     array findByCp(int $cp) Return AncienEtablissement objects filtered by the cp column
 * @method     array findByVille(string $ville) Return AncienEtablissement objects filtered by the ville column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAncienEtablissementQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAncienEtablissementQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AncienEtablissement', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AncienEtablissementQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AncienEtablissementQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AncienEtablissementQuery) {
			return $criteria;
		}
		$query = new AncienEtablissementQuery();
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
	 * @return    AncienEtablissement|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AncienEtablissementPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AncienEtablissementPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AncienEtablissementPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AncienEtablissementPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(AncienEtablissementPeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(AncienEtablissementPeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the niveau column
	 * 
	 * @param     string $niveau The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByNiveau($niveau = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($niveau)) {
			return $this->addUsingAlias(AncienEtablissementPeer::NIVEAU, $niveau, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $niveau)) {
			return $this->addUsingAlias(AncienEtablissementPeer::NIVEAU, str_replace('*', '%', $niveau), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::NIVEAU, $niveau, $comparison);
		}
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $type The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByType($type = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($type)) {
			return $this->addUsingAlias(AncienEtablissementPeer::TYPE, $type, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $type)) {
			return $this->addUsingAlias(AncienEtablissementPeer::TYPE, str_replace('*', '%', $type), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::TYPE, $type, $comparison);
		}
	}

	/**
	 * Filter the query on the cp column
	 * 
	 * @param     int|array $cp The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByCp($cp = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($cp)) {
			if (array_values($cp) === $cp) {
				return $this->addUsingAlias(AncienEtablissementPeer::CP, $cp, Criteria::IN);
			} else {
				if (isset($cp['min'])) {
					$this->addUsingAlias(AncienEtablissementPeer::CP, $cp['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($cp['max'])) {
					$this->addUsingAlias(AncienEtablissementPeer::CP, $cp['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::CP, $cp, $comparison);
		}
	}

	/**
	 * Filter the query on the ville column
	 * 
	 * @param     string $ville The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByVille($ville = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ville)) {
			return $this->addUsingAlias(AncienEtablissementPeer::VILLE, $ville, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $ville)) {
			return $this->addUsingAlias(AncienEtablissementPeer::VILLE, str_replace('*', '%', $ville), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AncienEtablissementPeer::VILLE, $ville, $comparison);
		}
	}

	/**
	 * Filter the query by a related JEleveAncienEtablissement object
	 *
	 * @param     JEleveAncienEtablissement $jEleveAncienEtablissement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByJEleveAncienEtablissement($jEleveAncienEtablissement, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AncienEtablissementPeer::ID, $jEleveAncienEtablissement->getIdEtablissement(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveAncienEtablissement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function joinJEleveAncienEtablissement($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveAncienEtablissement');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JEleveAncienEtablissement');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveAncienEtablissement relation JEleveAncienEtablissement object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveAncienEtablissementQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveAncienEtablissementQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveAncienEtablissement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveAncienEtablissement', 'JEleveAncienEtablissementQuery');
	}

	/**
	 * Filter the query by a related Eleve object
	 * using the j_eleves_etablissements table as cross reference
	 *
	 * @param     Eleve $eleve the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJEleveAncienEtablissementQuery()
				->filterByEleve($eleve, $comparison)
			->endUse();
	}
	
	/**
	 * Exclude object from result
	 *
	 * @param     AncienEtablissement $ancienEtablissement Object to remove from the list of results
	 *
	 * @return    AncienEtablissementQuery The current query, for fluid interface
	 */
	public function prune($ancienEtablissement = null)
	{
		if ($ancienEtablissement) {
			$this->addUsingAlias(AncienEtablissementPeer::ID, $ancienEtablissement->getId(), Criteria::NOT_EQUAL);
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

} // BaseAncienEtablissementQuery
