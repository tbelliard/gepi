<?php


/**
 * Base class that represents a query for the 'archivage_ects' table.
 *
 * Enregistrement d'archive pour les credits ECTS, dont le rapport n'est edite qu'au depart de l'eleve
 *
 * @method     ArchiveEctsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ArchiveEctsQuery orderByAnnee($order = Criteria::ASC) Order by the annee column
 * @method     ArchiveEctsQuery orderByIne($order = Criteria::ASC) Order by the ine column
 * @method     ArchiveEctsQuery orderByClasse($order = Criteria::ASC) Order by the classe column
 * @method     ArchiveEctsQuery orderByNumPeriode($order = Criteria::ASC) Order by the num_periode column
 * @method     ArchiveEctsQuery orderByNomPeriode($order = Criteria::ASC) Order by the nom_periode column
 * @method     ArchiveEctsQuery orderBySpecial($order = Criteria::ASC) Order by the special column
 * @method     ArchiveEctsQuery orderByMatiere($order = Criteria::ASC) Order by the matiere column
 * @method     ArchiveEctsQuery orderByProfs($order = Criteria::ASC) Order by the profs column
 * @method     ArchiveEctsQuery orderByValeur($order = Criteria::ASC) Order by the valeur column
 * @method     ArchiveEctsQuery orderByMention($order = Criteria::ASC) Order by the mention column
 *
 * @method     ArchiveEctsQuery groupById() Group by the id column
 * @method     ArchiveEctsQuery groupByAnnee() Group by the annee column
 * @method     ArchiveEctsQuery groupByIne() Group by the ine column
 * @method     ArchiveEctsQuery groupByClasse() Group by the classe column
 * @method     ArchiveEctsQuery groupByNumPeriode() Group by the num_periode column
 * @method     ArchiveEctsQuery groupByNomPeriode() Group by the nom_periode column
 * @method     ArchiveEctsQuery groupBySpecial() Group by the special column
 * @method     ArchiveEctsQuery groupByMatiere() Group by the matiere column
 * @method     ArchiveEctsQuery groupByProfs() Group by the profs column
 * @method     ArchiveEctsQuery groupByValeur() Group by the valeur column
 * @method     ArchiveEctsQuery groupByMention() Group by the mention column
 *
 * @method     ArchiveEctsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ArchiveEctsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ArchiveEctsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ArchiveEctsQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     ArchiveEctsQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     ArchiveEctsQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     ArchiveEcts findOne(PropelPDO $con = null) Return the first ArchiveEcts matching the query
 * @method     ArchiveEcts findOneOrCreate(PropelPDO $con = null) Return the first ArchiveEcts matching the query, or a new ArchiveEcts object populated from the query conditions when no match is found
 *
 * @method     ArchiveEcts findOneById(int $id) Return the first ArchiveEcts filtered by the id column
 * @method     ArchiveEcts findOneByAnnee(string $annee) Return the first ArchiveEcts filtered by the annee column
 * @method     ArchiveEcts findOneByIne(string $ine) Return the first ArchiveEcts filtered by the ine column
 * @method     ArchiveEcts findOneByClasse(string $classe) Return the first ArchiveEcts filtered by the classe column
 * @method     ArchiveEcts findOneByNumPeriode(int $num_periode) Return the first ArchiveEcts filtered by the num_periode column
 * @method     ArchiveEcts findOneByNomPeriode(string $nom_periode) Return the first ArchiveEcts filtered by the nom_periode column
 * @method     ArchiveEcts findOneBySpecial(string $special) Return the first ArchiveEcts filtered by the special column
 * @method     ArchiveEcts findOneByMatiere(string $matiere) Return the first ArchiveEcts filtered by the matiere column
 * @method     ArchiveEcts findOneByProfs(string $profs) Return the first ArchiveEcts filtered by the profs column
 * @method     ArchiveEcts findOneByValeur(string $valeur) Return the first ArchiveEcts filtered by the valeur column
 * @method     ArchiveEcts findOneByMention(string $mention) Return the first ArchiveEcts filtered by the mention column
 *
 * @method     array findById(int $id) Return ArchiveEcts objects filtered by the id column
 * @method     array findByAnnee(string $annee) Return ArchiveEcts objects filtered by the annee column
 * @method     array findByIne(string $ine) Return ArchiveEcts objects filtered by the ine column
 * @method     array findByClasse(string $classe) Return ArchiveEcts objects filtered by the classe column
 * @method     array findByNumPeriode(int $num_periode) Return ArchiveEcts objects filtered by the num_periode column
 * @method     array findByNomPeriode(string $nom_periode) Return ArchiveEcts objects filtered by the nom_periode column
 * @method     array findBySpecial(string $special) Return ArchiveEcts objects filtered by the special column
 * @method     array findByMatiere(string $matiere) Return ArchiveEcts objects filtered by the matiere column
 * @method     array findByProfs(string $profs) Return ArchiveEcts objects filtered by the profs column
 * @method     array findByValeur(string $valeur) Return ArchiveEcts objects filtered by the valeur column
 * @method     array findByMention(string $mention) Return ArchiveEcts objects filtered by the mention column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseArchiveEctsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseArchiveEctsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'ArchiveEcts', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ArchiveEctsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ArchiveEctsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ArchiveEctsQuery) {
			return $criteria;
		}
		$query = new ArchiveEctsQuery();
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
	 * @param     array[$id, $ine, $num_periode, $special] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    ArchiveEcts|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = ArchiveEctsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2], (string) $key[3]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(ArchiveEctsPeer::ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(ArchiveEctsPeer::INE, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(ArchiveEctsPeer::NUM_PERIODE, $key[2], Criteria::EQUAL);
		$this->addUsingAlias(ArchiveEctsPeer::SPECIAL, $key[3], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(ArchiveEctsPeer::ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(ArchiveEctsPeer::INE, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(ArchiveEctsPeer::NUM_PERIODE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$cton3 = $this->getNewCriterion(ArchiveEctsPeer::SPECIAL, $key[3], Criteria::EQUAL);
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ArchiveEctsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the annee column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByAnnee('fooValue');   // WHERE annee = 'fooValue'
	 * $query->filterByAnnee('%fooValue%'); // WHERE annee LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $annee The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByAnnee($annee = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($annee)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $annee)) {
				$annee = str_replace('*', '%', $annee);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::ANNEE, $annee, $comparison);
	}

	/**
	 * Filter the query on the ine column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIne('fooValue');   // WHERE ine = 'fooValue'
	 * $query->filterByIne('%fooValue%'); // WHERE ine LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ine The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByIne($ine = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ine)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ine)) {
				$ine = str_replace('*', '%', $ine);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::INE, $ine, $comparison);
	}

	/**
	 * Filter the query on the classe column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByClasse('fooValue');   // WHERE classe = 'fooValue'
	 * $query->filterByClasse('%fooValue%'); // WHERE classe LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $classe The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($classe)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $classe)) {
				$classe = str_replace('*', '%', $classe);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::CLASSE, $classe, $comparison);
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByNumPeriode($numPeriode = null, $comparison = null)
	{
		if (is_array($numPeriode) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ArchiveEctsPeer::NUM_PERIODE, $numPeriode, $comparison);
	}

	/**
	 * Filter the query on the nom_periode column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByNomPeriode('fooValue');   // WHERE nom_periode = 'fooValue'
	 * $query->filterByNomPeriode('%fooValue%'); // WHERE nom_periode LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomPeriode The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByNomPeriode($nomPeriode = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomPeriode)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomPeriode)) {
				$nomPeriode = str_replace('*', '%', $nomPeriode);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::NOM_PERIODE, $nomPeriode, $comparison);
	}

	/**
	 * Filter the query on the special column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterBySpecial('fooValue');   // WHERE special = 'fooValue'
	 * $query->filterBySpecial('%fooValue%'); // WHERE special LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $special The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterBySpecial($special = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($special)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $special)) {
				$special = str_replace('*', '%', $special);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::SPECIAL, $special, $comparison);
	}

	/**
	 * Filter the query on the matiere column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByMatiere('fooValue');   // WHERE matiere = 'fooValue'
	 * $query->filterByMatiere('%fooValue%'); // WHERE matiere LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiere The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiere)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiere)) {
				$matiere = str_replace('*', '%', $matiere);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::MATIERE, $matiere, $comparison);
	}

	/**
	 * Filter the query on the profs column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByProfs('fooValue');   // WHERE profs = 'fooValue'
	 * $query->filterByProfs('%fooValue%'); // WHERE profs LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $profs The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByProfs($profs = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($profs)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $profs)) {
				$profs = str_replace('*', '%', $profs);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::PROFS, $profs, $comparison);
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByValeur($valeur = null, $comparison = null)
	{
		if (is_array($valeur)) {
			$useMinMax = false;
			if (isset($valeur['min'])) {
				$this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($valeur['max'])) {
				$this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur, $comparison);
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(ArchiveEctsPeer::MENTION, $mention, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(ArchiveEctsPeer::INE, $eleve->getNoGep(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(ArchiveEctsPeer::INE, $eleve->toKeyValue('PrimaryKey', 'NoGep'), $comparison);
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
	 * @return    ArchiveEctsQuery The current query, for fluid interface
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
	 * Exclude object from result
	 *
	 * @param     ArchiveEcts $archiveEcts Object to remove from the list of results
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function prune($archiveEcts = null)
	{
		if ($archiveEcts) {
			$this->addCond('pruneCond0', $this->getAliasedColName(ArchiveEctsPeer::ID), $archiveEcts->getId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(ArchiveEctsPeer::INE), $archiveEcts->getIne(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(ArchiveEctsPeer::NUM_PERIODE), $archiveEcts->getNumPeriode(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond3', $this->getAliasedColName(ArchiveEctsPeer::SPECIAL), $archiveEcts->getSpecial(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2', 'pruneCond3'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseArchiveEctsQuery
