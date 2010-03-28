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
 * @method     ArchiveEctsQuery leftJoinEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     ArchiveEctsQuery rightJoinEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     ArchiveEctsQuery innerJoinEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     ArchiveEcts findOne(PropelPDO $con = null) Return the first ArchiveEcts matching the query
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
			$stmt = $this
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $this->getFormatter()->formatOne($stmt);
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
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(ArchiveEctsPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the annee column
	 * 
	 * @param     string $annee The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByAnnee($annee = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($annee)) {
			return $this->addUsingAlias(ArchiveEctsPeer::ANNEE, $annee, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $annee)) {
			return $this->addUsingAlias(ArchiveEctsPeer::ANNEE, str_replace('*', '%', $annee), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::ANNEE, $annee, $comparison);
		}
	}

	/**
	 * Filter the query on the ine column
	 * 
	 * @param     string $ine The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByIne($ine = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ine)) {
			return $this->addUsingAlias(ArchiveEctsPeer::INE, $ine, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $ine)) {
			return $this->addUsingAlias(ArchiveEctsPeer::INE, str_replace('*', '%', $ine), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::INE, $ine, $comparison);
		}
	}

	/**
	 * Filter the query on the classe column
	 * 
	 * @param     string $classe The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($classe)) {
			return $this->addUsingAlias(ArchiveEctsPeer::CLASSE, $classe, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $classe)) {
			return $this->addUsingAlias(ArchiveEctsPeer::CLASSE, str_replace('*', '%', $classe), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::CLASSE, $classe, $comparison);
		}
	}

	/**
	 * Filter the query on the num_periode column
	 * 
	 * @param     int|array $numPeriode The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByNumPeriode($numPeriode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numPeriode)) {
			return $this->addUsingAlias(ArchiveEctsPeer::NUM_PERIODE, $numPeriode, Criteria::IN);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::NUM_PERIODE, $numPeriode, $comparison);
		}
	}

	/**
	 * Filter the query on the nom_periode column
	 * 
	 * @param     string $nomPeriode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByNomPeriode($nomPeriode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nomPeriode)) {
			return $this->addUsingAlias(ArchiveEctsPeer::NOM_PERIODE, $nomPeriode, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nomPeriode)) {
			return $this->addUsingAlias(ArchiveEctsPeer::NOM_PERIODE, str_replace('*', '%', $nomPeriode), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::NOM_PERIODE, $nomPeriode, $comparison);
		}
	}

	/**
	 * Filter the query on the special column
	 * 
	 * @param     string $special The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterBySpecial($special = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($special)) {
			return $this->addUsingAlias(ArchiveEctsPeer::SPECIAL, $special, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $special)) {
			return $this->addUsingAlias(ArchiveEctsPeer::SPECIAL, str_replace('*', '%', $special), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::SPECIAL, $special, $comparison);
		}
	}

	/**
	 * Filter the query on the matiere column
	 * 
	 * @param     string $matiere The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($matiere)) {
			return $this->addUsingAlias(ArchiveEctsPeer::MATIERE, $matiere, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $matiere)) {
			return $this->addUsingAlias(ArchiveEctsPeer::MATIERE, str_replace('*', '%', $matiere), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::MATIERE, $matiere, $comparison);
		}
	}

	/**
	 * Filter the query on the profs column
	 * 
	 * @param     string $profs The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByProfs($profs = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($profs)) {
			return $this->addUsingAlias(ArchiveEctsPeer::PROFS, $profs, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $profs)) {
			return $this->addUsingAlias(ArchiveEctsPeer::PROFS, str_replace('*', '%', $profs), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::PROFS, $profs, $comparison);
		}
	}

	/**
	 * Filter the query on the valeur column
	 * 
	 * @param     string|array $valeur The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByValeur($valeur = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($valeur)) {
			if (array_values($valeur) === $valeur) {
				return $this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur, Criteria::IN);
			} else {
				if (isset($valeur['min'])) {
					$this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($valeur['max'])) {
					$this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::VALEUR, $valeur, $comparison);
		}
	}

	/**
	 * Filter the query on the mention column
	 * 
	 * @param     string $mention The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByMention($mention = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($mention)) {
			return $this->addUsingAlias(ArchiveEctsPeer::MENTION, $mention, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $mention)) {
			return $this->addUsingAlias(ArchiveEctsPeer::MENTION, str_replace('*', '%', $mention), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(ArchiveEctsPeer::MENTION, $mention, $comparison);
		}
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(ArchiveEctsPeer::INE, $eleve->getNoGep(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ArchiveEctsQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Eleve');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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

} // BaseArchiveEctsQuery
