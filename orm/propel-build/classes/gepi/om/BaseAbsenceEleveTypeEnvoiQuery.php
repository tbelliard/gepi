<?php


/**
 * Base class that represents a query for the 'a_type_envois' table.
 *
 * Chaque envoi dispose d'un type qui est stocke ici
 *
 * @method     AbsenceEleveTypeEnvoiQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveTypeEnvoiQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AbsenceEleveTypeEnvoiQuery orderByOrdreAffichage($order = Criteria::ASC) Order by the ordre_affichage column
 * @method     AbsenceEleveTypeEnvoiQuery orderByContenu($order = Criteria::ASC) Order by the contenu column
 *
 * @method     AbsenceEleveTypeEnvoiQuery groupById() Group by the id column
 * @method     AbsenceEleveTypeEnvoiQuery groupByNom() Group by the nom column
 * @method     AbsenceEleveTypeEnvoiQuery groupByOrdreAffichage() Group by the ordre_affichage column
 * @method     AbsenceEleveTypeEnvoiQuery groupByContenu() Group by the contenu column
 *
 * @method     AbsenceEleveTypeEnvoiQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveTypeEnvoiQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveTypeEnvoiQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveTypeEnvoiQuery leftJoinAbsenceEleveEnvoi($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     AbsenceEleveTypeEnvoiQuery rightJoinAbsenceEleveEnvoi($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     AbsenceEleveTypeEnvoiQuery innerJoinAbsenceEleveEnvoi($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveEnvoi relation
 *
 * @method     AbsenceEleveTypeEnvoi findOne(PropelPDO $con = null) Return the first AbsenceEleveTypeEnvoi matching the query
 * @method     AbsenceEleveTypeEnvoi findOneById(int $id) Return the first AbsenceEleveTypeEnvoi filtered by the id column
 * @method     AbsenceEleveTypeEnvoi findOneByNom(string $nom) Return the first AbsenceEleveTypeEnvoi filtered by the nom column
 * @method     AbsenceEleveTypeEnvoi findOneByOrdreAffichage(int $ordre_affichage) Return the first AbsenceEleveTypeEnvoi filtered by the ordre_affichage column
 * @method     AbsenceEleveTypeEnvoi findOneByContenu(string $contenu) Return the first AbsenceEleveTypeEnvoi filtered by the contenu column
 *
 * @method     array findById(int $id) Return AbsenceEleveTypeEnvoi objects filtered by the id column
 * @method     array findByNom(string $nom) Return AbsenceEleveTypeEnvoi objects filtered by the nom column
 * @method     array findByOrdreAffichage(int $ordre_affichage) Return AbsenceEleveTypeEnvoi objects filtered by the ordre_affichage column
 * @method     array findByContenu(string $contenu) Return AbsenceEleveTypeEnvoi objects filtered by the contenu column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTypeEnvoiQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveTypeEnvoiQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveTypeEnvoi', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveTypeEnvoiQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveTypeEnvoiQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveTypeEnvoiQuery();
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
		if ((null !== ($obj = AbsenceEleveTypeEnvoiPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the ordre_affichage column
	 * 
	 * @param     int|array $ordreAffichage The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByOrdreAffichage($ordreAffichage = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ordreAffichage)) {
			if (array_values($ordreAffichage) === $ordreAffichage) {
				return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ORDRE_AFFICHAGE, $ordreAffichage, Criteria::IN);
			} else {
				if (isset($ordreAffichage['min'])) {
					$this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ORDRE_AFFICHAGE, $ordreAffichage['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($ordreAffichage['max'])) {
					$this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ORDRE_AFFICHAGE, $ordreAffichage['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ORDRE_AFFICHAGE, $ordreAffichage, $comparison);
		}
	}

	/**
	 * Filter the query on the contenu column
	 * 
	 * @param     string $contenu The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByContenu($contenu = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($contenu)) {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::CONTENU, $contenu, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $contenu)) {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::CONTENU, str_replace('*', '%', $contenu), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::CONTENU, $contenu, $comparison);
		}
	}

	/**
	 * Filter the query by a related AbsenceEleveEnvoi object
	 *
	 * @param     AbsenceEleveEnvoi $absenceEleveEnvoi  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveEnvoi($absenceEleveEnvoi, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $absenceEleveEnvoi->getIdTypeEnvoi(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveEnvoi relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveEnvoi($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveEnvoi');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveEnvoi');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveEnvoi relation AbsenceEleveEnvoi object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveEnvoiQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveEnvoi($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveEnvoi', 'AbsenceEleveEnvoiQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveTypeEnvoi $absenceEleveTypeEnvoi Object to remove from the list of results
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveTypeEnvoi = null)
	{
		if ($absenceEleveTypeEnvoi) {
			$this->addUsingAlias(AbsenceEleveTypeEnvoiPeer::ID, $absenceEleveTypeEnvoi->getId(), Criteria::NOT_EQUAL);
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

} // BaseAbsenceEleveTypeEnvoiQuery
