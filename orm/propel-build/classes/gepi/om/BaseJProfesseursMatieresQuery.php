<?php


/**
 * Base class that represents a query for the 'j_professeurs_matieres' table.
 *
 * Liaison entre les profs et les matières
 *
 * @method     JProfesseursMatieresQuery orderByIdMatiere($order = Criteria::ASC) Order by the id_matiere column
 * @method     JProfesseursMatieresQuery orderByIdProfesseur($order = Criteria::ASC) Order by the id_professeur column
 * @method     JProfesseursMatieresQuery orderByOrdreMatieres($order = Criteria::ASC) Order by the ordre_matieres column
 *
 * @method     JProfesseursMatieresQuery groupByIdMatiere() Group by the id_matiere column
 * @method     JProfesseursMatieresQuery groupByIdProfesseur() Group by the id_professeur column
 * @method     JProfesseursMatieresQuery groupByOrdreMatieres() Group by the ordre_matieres column
 *
 * @method     JProfesseursMatieresQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     JProfesseursMatieresQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     JProfesseursMatieresQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     JProfesseursMatieresQuery leftJoinMatiere($relationAlias = null) Adds a LEFT JOIN clause to the query using the Matiere relation
 * @method     JProfesseursMatieresQuery rightJoinMatiere($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Matiere relation
 * @method     JProfesseursMatieresQuery innerJoinMatiere($relationAlias = null) Adds a INNER JOIN clause to the query using the Matiere relation
 *
 * @method     JProfesseursMatieresQuery leftJoinProfesseur($relationAlias = null) Adds a LEFT JOIN clause to the query using the Professeur relation
 * @method     JProfesseursMatieresQuery rightJoinProfesseur($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Professeur relation
 * @method     JProfesseursMatieresQuery innerJoinProfesseur($relationAlias = null) Adds a INNER JOIN clause to the query using the Professeur relation
 *
 * @method     JProfesseursMatieres findOne(PropelPDO $con = null) Return the first JProfesseursMatieres matching the query
 * @method     JProfesseursMatieres findOneOrCreate(PropelPDO $con = null) Return the first JProfesseursMatieres matching the query, or a new JProfesseursMatieres object populated from the query conditions when no match is found
 *
 * @method     JProfesseursMatieres findOneByIdMatiere(string $id_matiere) Return the first JProfesseursMatieres filtered by the id_matiere column
 * @method     JProfesseursMatieres findOneByIdProfesseur(string $id_professeur) Return the first JProfesseursMatieres filtered by the id_professeur column
 * @method     JProfesseursMatieres findOneByOrdreMatieres(int $ordre_matieres) Return the first JProfesseursMatieres filtered by the ordre_matieres column
 *
 * @method     array findByIdMatiere(string $id_matiere) Return JProfesseursMatieres objects filtered by the id_matiere column
 * @method     array findByIdProfesseur(string $id_professeur) Return JProfesseursMatieres objects filtered by the id_professeur column
 * @method     array findByOrdreMatieres(int $ordre_matieres) Return JProfesseursMatieres objects filtered by the ordre_matieres column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJProfesseursMatieresQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseJProfesseursMatieresQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'JProfesseursMatieres', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new JProfesseursMatieresQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    JProfesseursMatieresQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof JProfesseursMatieresQuery) {
			return $criteria;
		}
		$query = new JProfesseursMatieresQuery();
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
	 * @param     array[$id_matiere, $id_professeur] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JProfesseursMatieres|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = JProfesseursMatieresPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(JProfesseursMatieresPeer::ID_MATIERE, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(JProfesseursMatieresPeer::ID_PROFESSEUR, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(JProfesseursMatieresPeer::ID_MATIERE, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(JProfesseursMatieresPeer::ID_PROFESSEUR, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
	}

	/**
	 * Filter the query on the id_matiere column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdMatiere('fooValue');   // WHERE id_matiere = 'fooValue'
	 * $query->filterByIdMatiere('%fooValue%'); // WHERE id_matiere LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $idMatiere The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
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
		return $this->addUsingAlias(JProfesseursMatieresPeer::ID_MATIERE, $idMatiere, $comparison);
	}

	/**
	 * Filter the query on the id_professeur column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdProfesseur('fooValue');   // WHERE id_professeur = 'fooValue'
	 * $query->filterByIdProfesseur('%fooValue%'); // WHERE id_professeur LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $idProfesseur The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByIdProfesseur($idProfesseur = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idProfesseur)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idProfesseur)) {
				$idProfesseur = str_replace('*', '%', $idProfesseur);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(JProfesseursMatieresPeer::ID_PROFESSEUR, $idProfesseur, $comparison);
	}

	/**
	 * Filter the query on the ordre_matieres column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByOrdreMatieres(1234); // WHERE ordre_matieres = 1234
	 * $query->filterByOrdreMatieres(array(12, 34)); // WHERE ordre_matieres IN (12, 34)
	 * $query->filterByOrdreMatieres(array('min' => 12)); // WHERE ordre_matieres > 12
	 * </code>
	 *
	 * @param     mixed $ordreMatieres The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByOrdreMatieres($ordreMatieres = null, $comparison = null)
	{
		if (is_array($ordreMatieres)) {
			$useMinMax = false;
			if (isset($ordreMatieres['min'])) {
				$this->addUsingAlias(JProfesseursMatieresPeer::ORDRE_MATIERES, $ordreMatieres['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($ordreMatieres['max'])) {
				$this->addUsingAlias(JProfesseursMatieresPeer::ORDRE_MATIERES, $ordreMatieres['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JProfesseursMatieresPeer::ORDRE_MATIERES, $ordreMatieres, $comparison);
	}

	/**
	 * Filter the query by a related Matiere object
	 *
	 * @param     Matiere|PropelCollection $matiere The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByMatiere($matiere, $comparison = null)
	{
		if ($matiere instanceof Matiere) {
			return $this
				->addUsingAlias(JProfesseursMatieresPeer::ID_MATIERE, $matiere->getMatiere(), $comparison);
		} elseif ($matiere instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JProfesseursMatieresPeer::ID_MATIERE, $matiere->toKeyValue('PrimaryKey', 'Matiere'), $comparison);
		} else {
			throw new PropelException('filterByMatiere() only accepts arguments of type Matiere or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Matiere relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function filterByProfesseur($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(JProfesseursMatieresPeer::ID_PROFESSEUR, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JProfesseursMatieresPeer::ID_PROFESSEUR, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
		} else {
			throw new PropelException('filterByProfesseur() only accepts arguments of type UtilisateurProfessionnel or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Professeur relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function joinProfesseur($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Professeur');
		
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
			$this->addJoinObject($join, 'Professeur');
		}
		
		return $this;
	}

	/**
	 * Use the Professeur relation UtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function useProfesseurQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinProfesseur($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Professeur', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     JProfesseursMatieres $jProfesseursMatieres Object to remove from the list of results
	 *
	 * @return    JProfesseursMatieresQuery The current query, for fluid interface
	 */
	public function prune($jProfesseursMatieres = null)
	{
		if ($jProfesseursMatieres) {
			$this->addCond('pruneCond0', $this->getAliasedColName(JProfesseursMatieresPeer::ID_MATIERE), $jProfesseursMatieres->getIdMatiere(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(JProfesseursMatieresPeer::ID_PROFESSEUR), $jProfesseursMatieres->getIdProfesseur(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseJProfesseursMatieresQuery
