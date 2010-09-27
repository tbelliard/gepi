<?php


/**
 * Base class that represents a query for the 'j_groupes_matieres' table.
 *
 * Table permettant le jointure entre un enseignement et une matière.
 *
 * @method     JGroupesMatieresQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     JGroupesMatieresQuery orderByIdMatiere($order = Criteria::ASC) Order by the id_matiere column
 *
 * @method     JGroupesMatieresQuery groupByIdGroupe() Group by the id_groupe column
 * @method     JGroupesMatieresQuery groupByIdMatiere() Group by the id_matiere column
 *
 * @method     JGroupesMatieresQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JGroupesMatieresQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JGroupesMatieresQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JGroupesMatieresQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     JGroupesMatieresQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     JGroupesMatieresQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     JGroupesMatieresQuery leftJoinMatiere($relationAlias = null) Adds a LEFT JOIN clause to the query using the Matiere relation
 * @method     JGroupesMatieresQuery rightJoinMatiere($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Matiere relation
 * @method     JGroupesMatieresQuery innerJoinMatiere($relationAlias = null) Adds a INNER JOIN clause to the query using the Matiere relation
 *
 * @method     JGroupesMatieres findOne(PropelPDO $con = null) Return the first JGroupesMatieres matching the query
 * @method     JGroupesMatieres findOneOrCreate(PropelPDO $con = null) Return the first JGroupesMatieres matching the query, or a new JGroupesMatieres object populated from the query conditions when no match is found
 *
 * @method     JGroupesMatieres findOneByIdGroupe(int $id_groupe) Return the first JGroupesMatieres filtered by the id_groupe column
 * @method     JGroupesMatieres findOneByIdMatiere(string $id_matiere) Return the first JGroupesMatieres filtered by the id_matiere column
 *
 * @method     array findByIdGroupe(int $id_groupe) Return JGroupesMatieres objects filtered by the id_groupe column
 * @method     array findByIdMatiere(string $id_matiere) Return JGroupesMatieres objects filtered by the id_matiere column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJGroupesMatieresQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJGroupesMatieresQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JGroupesMatieres', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JGroupesMatieresQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JGroupesMatieresQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JGroupesMatieresQuery) {
			return $criteria;
		}
		$query = new JGroupesMatieresQuery();
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
	 * <code>
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 * @param     array[$id_groupe, $id_matiere] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JGroupesMatieres|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JGroupesMatieresPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JGroupesMatieresPeer::ID_GROUPE, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JGroupesMatieresPeer::ID_MATIERE, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JGroupesMatieresPeer::ID_GROUPE, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JGroupesMatieresPeer::ID_MATIERE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the id_groupe column
	 * 
	 * @param     int|array $idGroupe The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JGroupesMatieresPeer::ID_GROUPE, $idGroupe, $comparison);
	}

	/**
	 * Filter the query on the id_matiere column
	 * 
	 * @param     string $idMatiere The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByIdMatiere($idMatiere = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idMatiere)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idMatiere)) {
				$idMatiere = str_replace('*', '%', $idMatiere);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JGroupesMatieresPeer::ID_MATIERE, $idMatiere, $comparison);
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		return $this
			->addUsingAlias(JGroupesMatieresPeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groupe');
		
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
			$this->addJoinObject($join, 'Groupe');
		}
		
		return $this;
	}

	/**
	 * Use the Groupe relation Groupe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    GroupeQuery A secondary query class using the current class as primary query
	 */
	public function useGroupeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related Matiere object
	 *
	 * @param     Matiere $matiere  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere, $comparison = null)
	{
		return $this
			->addUsingAlias(JGroupesMatieresPeer::ID_MATIERE, $matiere->getMatiere(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Matiere relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function joinMatiere($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Matiere');
		
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
			$this->addJoinObject($join, 'Matiere');
		}
		
		return $this;
	}

	/**
	 * Use the Matiere relation Matiere object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MatiereQuery A secondary query class using the current class as primary query
	 */
	public function useMatiereQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinMatiere($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Matiere', 'MatiereQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JGroupesMatieres $jGroupesMatieres Object to remove from the list of results
	 *
	 * @return    JGroupesMatieresQuery The current query, for fluid interface
	 */
	public function prune($jGroupesMatieres = null)
	{
		if ($jGroupesMatieres) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JGroupesMatieresPeer::ID_GROUPE), $jGroupesMatieres->getIdGroupe(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JGroupesMatieresPeer::ID_MATIERE), $jGroupesMatieres->getIdMatiere(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJGroupesMatieresQuery
