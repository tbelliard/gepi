<?php


/**
 * Base class that represents a query for the 'j_eleves_professeurs' table.
 *
 * Table de jointure entre les professeurs principaux et les eleves
 *
 * @method     JEleveProfesseurPrincipalQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     JEleveProfesseurPrincipalQuery orderByProfesseur($order = Criteria::ASC) Order by the professeur column
 * @method     JEleveProfesseurPrincipalQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 *
 * @method     JEleveProfesseurPrincipalQuery groupByLogin() Group by the login column
 * @method     JEleveProfesseurPrincipalQuery groupByProfesseur() Group by the professeur column
 * @method     JEleveProfesseurPrincipalQuery groupByIdClasse() Group by the id_classe column
 *
 * @method     JEleveProfesseurPrincipalQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JEleveProfesseurPrincipalQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JEleveProfesseurPrincipalQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JEleveProfesseurPrincipalQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     JEleveProfesseurPrincipalQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     JEleveProfesseurPrincipalQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     JEleveProfesseurPrincipalQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JEleveProfesseurPrincipalQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     JEleveProfesseurPrincipalQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     JEleveProfesseurPrincipalQuery leftJoinClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JEleveProfesseurPrincipalQuery rightJoinClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JEleveProfesseurPrincipalQuery innerJoinClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JEleveProfesseurPrincipal findOne(PropelPDO $con = null) Return the first JEleveProfesseurPrincipal matching the query
 * @method     JEleveProfesseurPrincipal findOneOrCreate(PropelPDO $con = null) Return the first JEleveProfesseurPrincipal matching the query, or a new JEleveProfesseurPrincipal object populated from the query conditions when no match is found
 *
 * @method     JEleveProfesseurPrincipal findOneByLogin(string $login) Return the first JEleveProfesseurPrincipal filtered by the login column
 * @method     JEleveProfesseurPrincipal findOneByProfesseur(string $professeur) Return the first JEleveProfesseurPrincipal filtered by the professeur column
 * @method     JEleveProfesseurPrincipal findOneByIdClasse(int $id_classe) Return the first JEleveProfesseurPrincipal filtered by the id_classe column
 *
 * @method     array findByLogin(string $login) Return JEleveProfesseurPrincipal objects filtered by the login column
 * @method     array findByProfesseur(string $professeur) Return JEleveProfesseurPrincipal objects filtered by the professeur column
 * @method     array findByIdClasse(int $id_classe) Return JEleveProfesseurPrincipal objects filtered by the id_classe column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJEleveProfesseurPrincipalQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJEleveProfesseurPrincipalQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JEleveProfesseurPrincipal', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JEleveProfesseurPrincipalQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JEleveProfesseurPrincipalQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JEleveProfesseurPrincipalQuery) {
			return $criteria;
		}
		$query = new JEleveProfesseurPrincipalQuery();
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
	 * $obj = $c->findPk(array(12, 34, 56), $con);
	 * </code>
	 * @param     array[$login, $professeur, $id_classe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JEleveProfesseurPrincipal|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JEleveProfesseurPrincipalPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JEleveProfesseurPrincipalPeer::LOGIN, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JEleveProfesseurPrincipalPeer::PROFESSEUR, $key[1], Criteria::EQUAL);
		$this->addUsingAlias(JEleveProfesseurPrincipalPeer::ID_CLASSE, $key[2], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JEleveProfesseurPrincipalPeer::LOGIN, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JEleveProfesseurPrincipalPeer::PROFESSEUR, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$cton2 = $this->getNewCriterion(JEleveProfesseurPrincipalPeer::ID_CLASSE, $key[2], Criteria::EQUAL);
			$cton0->addAnd($cton2);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the login column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByLogin('fooValue');   // WHERE login = 'fooValue'
	 * $query->filterByLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $login The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($login)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $login)) {
				$login = str_replace('*', '%', $login);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JEleveProfesseurPrincipalPeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the professeur column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByProfesseur('fooValue');   // WHERE professeur = 'fooValue'
	 * $query->filterByProfesseur('%fooValue%'); // WHERE professeur LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $professeur The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByProfesseur($professeur = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($professeur)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $professeur)) {
				$professeur = str_replace('*', '%', $professeur);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JEleveProfesseurPrincipalPeer::PROFESSEUR, $professeur, $comparison);
	}

	/**
	 * Filter the query on the id_classe column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdClasse(1234); // WHERE id_classe = 1234
	 * $query->filterByIdClasse(array(12, 34)); // WHERE id_classe IN (12, 34)
	 * $query->filterByIdClasse(array('min' => 12)); // WHERE id_classe > 12
	 * </code>
	 *
	 * @see       filterByClasse()
	 *
	 * @param     mixed $idClasse The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JEleveProfesseurPrincipalPeer::ID_CLASSE, $idClasse, $comparison);
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::LOGIN, $eleve->getLogin(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::LOGIN, $eleve->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::PROFESSEUR, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::PROFESSEUR, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
		} else {
			throw new PropelException('filterByUtilisateurProfessionnel() only accepts arguments of type UtilisateurProfessionnel or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('UtilisateurProfessionnel');
		
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
			$this->addJoinObject($join, 'UtilisateurProfessionnel');
		}
		
		return $this;
	}

	/**
	 * Use the UtilisateurProfessionnel relation UtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function useUtilisateurProfessionnelQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe|PropelCollection $classe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		if ($classe instanceof Classe) {
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::ID_CLASSE, $classe->getId(), $comparison);
		} elseif ($classe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JEleveProfesseurPrincipalPeer::ID_CLASSE, $classe->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByClasse() only accepts arguments of type Classe or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Classe');
		
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
	public function useClasseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JEleveProfesseurPrincipal $jEleveProfesseurPrincipal Object to remove from the list of results
	 *
	 * @return    JEleveProfesseurPrincipalQuery The current query, for fluid interface
	 */
	public function prune($jEleveProfesseurPrincipal = null)
	{
		if ($jEleveProfesseurPrincipal) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JEleveProfesseurPrincipalPeer::LOGIN), $jEleveProfesseurPrincipal->getLogin(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JEleveProfesseurPrincipalPeer::PROFESSEUR), $jEleveProfesseurPrincipal->getProfesseur(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond2', $this->getAliasedColName(JEleveProfesseurPrincipalPeer::ID_CLASSE), $jEleveProfesseurPrincipal->getIdClasse(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJEleveProfesseurPrincipalQuery
