<?php


/**
 * Base class that represents a query for the 'ects_credits' table.
 *
 * Objet qui précise le nombre d'ECTS obtenus par l'eleve pour un enseignement et une periode donnée
 *
 * @method     CreditEctsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     CreditEctsQuery orderByIdEleve($order = Criteria::ASC) Order by the id_eleve column
 * @method     CreditEctsQuery orderByNumPeriode($order = Criteria::ASC) Order by the num_periode column
 * @method     CreditEctsQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     CreditEctsQuery orderByValeur($order = Criteria::ASC) Order by the valeur column
 * @method     CreditEctsQuery orderByMention($order = Criteria::ASC) Order by the mention column
 * @method     CreditEctsQuery orderByMentionProf($order = Criteria::ASC) Order by the mention_prof column
 *
 * @method     CreditEctsQuery groupById() Group by the id column
 * @method     CreditEctsQuery groupByIdEleve() Group by the id_eleve column
 * @method     CreditEctsQuery groupByNumPeriode() Group by the num_periode column
 * @method     CreditEctsQuery groupByIdGroupe() Group by the id_groupe column
 * @method     CreditEctsQuery groupByValeur() Group by the valeur column
 * @method     CreditEctsQuery groupByMention() Group by the mention column
 * @method     CreditEctsQuery groupByMentionProf() Group by the mention_prof column
 *
 * @method     CreditEctsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CreditEctsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CreditEctsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CreditEctsQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     CreditEctsQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     CreditEctsQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     CreditEctsQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     CreditEctsQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     CreditEcts findOne(PropelPDO $con = null) Return the first CreditEcts matching the query
 * @method     CreditEcts findOneOrCreate(PropelPDO $con = null) Return the first CreditEcts matching the query, or a new CreditEcts object populated from the query conditions when no match is found
 *
 * @method     CreditEcts findOneById(int $id) Return the first CreditEcts filtered by the id column
 * @method     CreditEcts findOneByIdEleve(int $id_eleve) Return the first CreditEcts filtered by the id_eleve column
 * @method     CreditEcts findOneByNumPeriode(int $num_periode) Return the first CreditEcts filtered by the num_periode column
 * @method     CreditEcts findOneByIdGroupe(int $id_groupe) Return the first CreditEcts filtered by the id_groupe column
 * @method     CreditEcts findOneByValeur(string $valeur) Return the first CreditEcts filtered by the valeur column
 * @method     CreditEcts findOneByMention(string $mention) Return the first CreditEcts filtered by the mention column
 * @method     CreditEcts findOneByMentionProf(string $mention_prof) Return the first CreditEcts filtered by the mention_prof column
 *
 * @method     array findById(int $id) Return CreditEcts objects filtered by the id column
 * @method     array findByIdEleve(int $id_eleve) Return CreditEcts objects filtered by the id_eleve column
 * @method     array findByNumPeriode(int $num_periode) Return CreditEcts objects filtered by the num_periode column
 * @method     array findByIdGroupe(int $id_groupe) Return CreditEcts objects filtered by the id_groupe column
 * @method     array findByValeur(string $valeur) Return CreditEcts objects filtered by the valeur column
 * @method     array findByMention(string $mention) Return CreditEcts objects filtered by the mention column
 * @method     array findByMentionProf(string $mention_prof) Return CreditEcts objects filtered by the mention_prof column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCreditEctsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCreditEctsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CreditEcts', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CreditEctsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CreditEctsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CreditEctsQuery) {
			return $criteria;
		}
		$query = new CreditEctsQuery();
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
	 * $obj = $c->findPk(array(12, 34, 56, 78), $con);
	 * </code>
	 * @param     array[$id, $id_eleve, $num_periode, $id_groupe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CreditEcts|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CreditEctsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2], (string) $key[3]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(CreditEctsPeer::ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(CreditEctsPeer::ID_ELEVE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(CreditEctsPeer::NUM_PERIODE, $key[2], Criteria::EQUAL);
		$this->addUsingAlias(CreditEctsPeer::ID_GROUPE, $key[3], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(CreditEctsPeer::ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(CreditEctsPeer::ID_ELEVE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(CreditEctsPeer::NUM_PERIODE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$cton3 = $this->getNewCriterion(CreditEctsPeer::ID_GROUPE, $key[3], Criteria::EQUAL);
			$cton0->addAnd($cton3);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the id column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterById(1234); // WHERE id = 1234
	 * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the id_eleve column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdEleve(1234); // WHERE id_eleve = 1234
	 * $query->filterByIdEleve(array(12, 34)); // WHERE id_eleve IN (12, 34)
	 * $query->filterByIdEleve(array('min' => 12)); // WHERE id_eleve > 12
	 * </code>
	 *
	 * @see       filterByEleve()
	 *
	 * @param     mixed $idEleve The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByIdEleve($idEleve = null, $comparison = null)
	{
		if (is_array($idEleve) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsPeer::ID_ELEVE, $idEleve, $comparison);
	}

	/**
	 * Filter the query on the num_periode column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNumPeriode(1234); // WHERE num_periode = 1234
	 * $query->filterByNumPeriode(array(12, 34)); // WHERE num_periode IN (12, 34)
	 * $query->filterByNumPeriode(array('min' => 12)); // WHERE num_periode > 12
	 * </code>
	 *
	 * @param     mixed $numPeriode The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByNumPeriode($numPeriode = null, $comparison = null)
	{
		if (is_array($numPeriode) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsPeer::NUM_PERIODE, $numPeriode, $comparison);
	}

	/**
	 * Filter the query on the id_groupe column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdGroupe(1234); // WHERE id_groupe = 1234
	 * $query->filterByIdGroupe(array(12, 34)); // WHERE id_groupe IN (12, 34)
	 * $query->filterByIdGroupe(array('min' => 12)); // WHERE id_groupe > 12
	 * </code>
	 *
	 * @see       filterByGroupe()
	 *
	 * @param     mixed $idGroupe The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CreditEctsPeer::ID_GROUPE, $idGroupe, $comparison);
	}

	/**
	 * Filter the query on the valeur column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByValeur(1234); // WHERE valeur = 1234
	 * $query->filterByValeur(array(12, 34)); // WHERE valeur IN (12, 34)
	 * $query->filterByValeur(array('min' => 12)); // WHERE valeur > 12
	 * </code>
	 *
	 * @param     mixed $valeur The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByValeur($valeur = null, $comparison = null)
	{
		if (is_array($valeur)) {
			$useMinMax = false;
			if (isset($valeur['min'])) {
				$this->addUsingAlias(CreditEctsPeer::VALEUR, $valeur['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($valeur['max'])) {
				$this->addUsingAlias(CreditEctsPeer::VALEUR, $valeur['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CreditEctsPeer::VALEUR, $valeur, $comparison);
	}

	/**
	 * Filter the query on the mention column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByMention('fooValue');   // WHERE mention = 'fooValue'
	 * $query->filterByMention('%fooValue%'); // WHERE mention LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $mention The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByMention($mention = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mention)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mention)) {
				$mention = str_replace('*', '%', $mention);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CreditEctsPeer::MENTION, $mention, $comparison);
	}

	/**
	 * Filter the query on the mention_prof column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByMentionProf('fooValue');   // WHERE mention_prof = 'fooValue'
	 * $query->filterByMentionProf('%fooValue%'); // WHERE mention_prof LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $mentionProf The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByMentionProf($mentionProf = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mentionProf)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mentionProf)) {
				$mentionProf = str_replace('*', '%', $mentionProf);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CreditEctsPeer::MENTION_PROF, $mentionProf, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(CreditEctsPeer::ID_ELEVE, $eleve->getIdEleve(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CreditEctsPeer::ID_ELEVE, $eleve->toKeyValue('PrimaryKey', 'IdEleve'), $comparison);
		} else {
			throw new PropelException('filterByEleve() only accepts arguments of type Eleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');
		
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
			$this->addJoinObject($join, 'Eleve');
		}
		
		return $this;
	}

	/**
	 * Use the Eleve relation Eleve object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery A secondary query class using the current class as primary query
	 */
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe|PropelCollection $groupe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		if ($groupe instanceof Groupe) {
			return $this
				->addUsingAlias(CreditEctsPeer::ID_GROUPE, $groupe->getId(), $comparison);
		} elseif ($groupe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CreditEctsPeer::ID_GROUPE, $groupe->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByGroupe() only accepts arguments of type Groupe or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     CreditEcts $creditEcts Object to remove from the list of results
	 *
	 * @return    CreditEctsQuery The current query, for fluid interface
	 */
	public function prune($creditEcts = null)
	{
		if ($creditEcts) {
			$this->addCond('pruneCond0', $this->getAliasedColName(CreditEctsPeer::ID), $creditEcts->getId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(CreditEctsPeer::ID_ELEVE), $creditEcts->getIdEleve(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(CreditEctsPeer::NUM_PERIODE), $creditEcts->getNumPeriode(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond3', $this->getAliasedColName(CreditEctsPeer::ID_GROUPE), $creditEcts->getIdGroupe(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2', 'pruneCond3'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseCreditEctsQuery
