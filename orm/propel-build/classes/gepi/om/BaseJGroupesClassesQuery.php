<?php


/**
 * Base class that represents a query for the 'j_groupes_classes' table.
 *
 * Table permettant la jointure entre groupe d'eleves et une classe. Cette jointure permet de definir un enseignement, c'est à dire un groupe d'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe
 *
 * @method     JGroupesClassesQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     JGroupesClassesQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 * @method     JGroupesClassesQuery orderByPriorite($order = Criteria::ASC) Order by the priorite column
 * @method     JGroupesClassesQuery orderByCoef($order = Criteria::ASC) Order by the coef column
 * @method     JGroupesClassesQuery orderByCategorieId($order = Criteria::ASC) Order by the categorie_id column
 * @method     JGroupesClassesQuery orderBySaisieEcts($order = Criteria::ASC) Order by the saisie_ects column
 * @method     JGroupesClassesQuery orderByValeurEcts($order = Criteria::ASC) Order by the valeur_ects column
 *
 * @method     JGroupesClassesQuery groupByIdGroupe() Group by the id_groupe column
 * @method     JGroupesClassesQuery groupByIdClasse() Group by the id_classe column
 * @method     JGroupesClassesQuery groupByPriorite() Group by the priorite column
 * @method     JGroupesClassesQuery groupByCoef() Group by the coef column
 * @method     JGroupesClassesQuery groupByCategorieId() Group by the categorie_id column
 * @method     JGroupesClassesQuery groupBySaisieEcts() Group by the saisie_ects column
 * @method     JGroupesClassesQuery groupByValeurEcts() Group by the valeur_ects column
 *
 * @method     JGroupesClassesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JGroupesClassesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JGroupesClassesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JGroupesClassesQuery leftJoinGroupe($relationAlias = '') Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     JGroupesClassesQuery rightJoinGroupe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     JGroupesClassesQuery innerJoinGroupe($relationAlias = '') Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     JGroupesClassesQuery leftJoinClasse($relationAlias = '') Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JGroupesClassesQuery rightJoinClasse($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JGroupesClassesQuery innerJoinClasse($relationAlias = '') Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JGroupesClassesQuery leftJoinCategorieMatiere($relationAlias = '') Adds a LEFT JOIN clause to the query using the CategorieMatiere relation
 * @method     JGroupesClassesQuery rightJoinCategorieMatiere($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CategorieMatiere relation
 * @method     JGroupesClassesQuery innerJoinCategorieMatiere($relationAlias = '') Adds a INNER JOIN clause to the query using the CategorieMatiere relation
 *
 * @method     JGroupesClasses findOne(PropelPDO $con = null) Return the first JGroupesClasses matching the query
 * @method     JGroupesClasses findOneByIdGroupe(int $id_groupe) Return the first JGroupesClasses filtered by the id_groupe column
 * @method     JGroupesClasses findOneByIdClasse(int $id_classe) Return the first JGroupesClasses filtered by the id_classe column
 * @method     JGroupesClasses findOneByPriorite(int $priorite) Return the first JGroupesClasses filtered by the priorite column
 * @method     JGroupesClasses findOneByCoef(string $coef) Return the first JGroupesClasses filtered by the coef column
 * @method     JGroupesClasses findOneByCategorieId(int $categorie_id) Return the first JGroupesClasses filtered by the categorie_id column
 * @method     JGroupesClasses findOneBySaisieEcts(boolean $saisie_ects) Return the first JGroupesClasses filtered by the saisie_ects column
 * @method     JGroupesClasses findOneByValeurEcts(string $valeur_ects) Return the first JGroupesClasses filtered by the valeur_ects column
 *
 * @method     array findByIdGroupe(int $id_groupe) Return JGroupesClasses objects filtered by the id_groupe column
 * @method     array findByIdClasse(int $id_classe) Return JGroupesClasses objects filtered by the id_classe column
 * @method     array findByPriorite(int $priorite) Return JGroupesClasses objects filtered by the priorite column
 * @method     array findByCoef(string $coef) Return JGroupesClasses objects filtered by the coef column
 * @method     array findByCategorieId(int $categorie_id) Return JGroupesClasses objects filtered by the categorie_id column
 * @method     array findBySaisieEcts(boolean $saisie_ects) Return JGroupesClasses objects filtered by the saisie_ects column
 * @method     array findByValeurEcts(string $valeur_ects) Return JGroupesClasses objects filtered by the valeur_ects column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJGroupesClassesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJGroupesClassesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JGroupesClasses', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JGroupesClassesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JGroupesClassesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JGroupesClassesQuery) {
			return $criteria;
		}
		$query = new JGroupesClassesQuery();
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
	 * $obj = $c->findPk(array(34, 634), $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JGroupesClassesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JGroupesClassesPeer::ID_GROUPE, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JGroupesClassesPeer::ID_CLASSE, $key[1], Criteria::EQUAL);
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idGroupe)) {
			return $this->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $idGroupe, Criteria::IN);
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $idGroupe, $comparison);
		}
	}

	/**
	 * Filter the query on the id_classe column
	 * 
	 * @param     int|array $idClasse The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idClasse)) {
			return $this->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $idClasse, Criteria::IN);
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $idClasse, $comparison);
		}
	}

	/**
	 * Filter the query on the priorite column
	 * 
	 * @param     int|array $priorite The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByPriorite($priorite = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($priorite)) {
			if (array_values($priorite) === $priorite) {
				return $this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite, Criteria::IN);
			} else {
				if (isset($priorite['min'])) {
					$this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($priorite['max'])) {
					$this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite, $comparison);
		}
	}

	/**
	 * Filter the query on the coef column
	 * 
	 * @param     string|array $coef The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByCoef($coef = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($coef)) {
			if (array_values($coef) === $coef) {
				return $this->addUsingAlias(JGroupesClassesPeer::COEF, $coef, Criteria::IN);
			} else {
				if (isset($coef['min'])) {
					$this->addUsingAlias(JGroupesClassesPeer::COEF, $coef['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($coef['max'])) {
					$this->addUsingAlias(JGroupesClassesPeer::COEF, $coef['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::COEF, $coef, $comparison);
		}
	}

	/**
	 * Filter the query on the categorie_id column
	 * 
	 * @param     int|array $categorieId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByCategorieId($categorieId = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($categorieId)) {
			if (array_values($categorieId) === $categorieId) {
				return $this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId, Criteria::IN);
			} else {
				if (isset($categorieId['min'])) {
					$this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($categorieId['max'])) {
					$this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId, $comparison);
		}
	}

	/**
	 * Filter the query on the saisie_ects column
	 * 
	 * @param     boolean|string $saisieEcts The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterBySaisieEcts($saisieEcts = null, $comparison = Criteria::EQUAL)
	{
		if(is_string($saisieEcts)) {
			$saisie_ects = in_array(strtolower($saisieEcts), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(JGroupesClassesPeer::SAISIE_ECTS, $saisieEcts, $comparison);
	}

	/**
	 * Filter the query on the valeur_ects column
	 * 
	 * @param     string|array $valeurEcts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByValeurEcts($valeurEcts = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($valeurEcts)) {
			if (array_values($valeurEcts) === $valeurEcts) {
				return $this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts, Criteria::IN);
			} else {
				if (isset($valeurEcts['min'])) {
					$this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($valeurEcts['max'])) {
					$this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts, $comparison);
		}
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groupe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useGroupeQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe $classe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $classe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Classe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Classe');
		}
		
		return $this;
	}

	/**
	 * Use the Classe relation Classe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery A secondary query class using the current class as primary query
	 */
	public function useClasseQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Filter the query by a related CategorieMatiere object
	 *
	 * @param     CategorieMatiere $categorieMatiere  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByCategorieMatiere($categorieMatiere, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieMatiere->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CategorieMatiere relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function joinCategorieMatiere($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CategorieMatiere');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CategorieMatiere');
		}
		
		return $this;
	}

	/**
	 * Use the CategorieMatiere relation CategorieMatiere object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CategorieMatiereQuery A secondary query class using the current class as primary query
	 */
	public function useCategorieMatiereQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCategorieMatiere($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CategorieMatiere', 'CategorieMatiereQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JGroupesClasses $jGroupesClasses Object to remove from the list of results
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function prune($jGroupesClasses = null)
	{
		if ($jGroupesClasses) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JGroupesClassesPeer::ID_GROUPE), $jGroupesClasses->getIdGroupe(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JGroupesClassesPeer::ID_CLASSE), $jGroupesClasses->getIdClasse(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
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

} // BaseJGroupesClassesQuery
