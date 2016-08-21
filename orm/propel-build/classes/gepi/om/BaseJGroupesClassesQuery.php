<?php


/**
 * Base class that represents a query for the 'j_groupes_classes' table.
 *
 * Table permettant la jointure entre groupe d'enseignement et une classe. Cette jointure permet de definir un enseignement, c'est à dire un groupe d'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe
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
 * @method     JGroupesClassesQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     JGroupesClassesQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     JGroupesClassesQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     JGroupesClassesQuery leftJoinClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     JGroupesClassesQuery rightJoinClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     JGroupesClassesQuery innerJoinClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     JGroupesClasses findOne(PropelPDO $con = null) Return the first JGroupesClasses matching the query
 * @method     JGroupesClasses findOneOrCreate(PropelPDO $con = null) Return the first JGroupesClasses matching the query, or a new JGroupesClasses object populated from the query conditions when no match is found
 *
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
	 * Find object by primary key.
	 * Propel uses the instance pool to skip the database if the object exists.
	 * Go fast if the query is untouched.
	 *
	 * <code>
	 * $obj = $c->findPk(array(12, 34), $con);
	 * </code>
	 *
	 * @param     array[$id_groupe, $id_classe] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    JGroupesClasses|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = JGroupesClassesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		if ($this->formatter || $this->modelAlias || $this->with || $this->select
		 || $this->selectColumns || $this->asColumns || $this->selectModifiers
		 || $this->map || $this->having || $this->joins) {
			return $this->findPkComplex($key, $con);
		} else {
			return $this->findPkSimple($key, $con);
		}
	}

	/**
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    JGroupesClasses A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID_GROUPE, ID_CLASSE, PRIORITE, COEF, CATEGORIE_ID, SAISIE_ECTS, VALEUR_ECTS FROM j_groupes_classes WHERE ID_GROUPE = :p0 AND ID_CLASSE = :p1';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new JGroupesClasses();
			$obj->hydrate($row);
			JGroupesClassesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
		}
		$stmt->closeCursor();

		return $obj;
	}

	/**
	 * Find object by primary key.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    JGroupesClasses|array|mixed the result, formatted by the current formatter
	 */
	protected function findPkComplex($key, $con)
	{
		// As the query uses a PK condition, no limit(1) is necessary.
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKey($key)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
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
		if ($con === null) {
			$con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKeys($keys)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->format($stmt);
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
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $idGroupe, $comparison);
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $idClasse, $comparison);
	}

	/**
	 * Filter the query on the priorite column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPriorite(1234); // WHERE priorite = 1234
	 * $query->filterByPriorite(array(12, 34)); // WHERE priorite IN (12, 34)
	 * $query->filterByPriorite(array('min' => 12)); // WHERE priorite > 12
	 * </code>
	 *
	 * @param     mixed $priorite The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByPriorite($priorite = null, $comparison = null)
	{
		if (is_array($priorite)) {
			$useMinMax = false;
			if (isset($priorite['min'])) {
				$this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($priorite['max'])) {
				$this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JGroupesClassesPeer::PRIORITE, $priorite, $comparison);
	}

	/**
	 * Filter the query on the coef column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCoef(1234); // WHERE coef = 1234
	 * $query->filterByCoef(array(12, 34)); // WHERE coef IN (12, 34)
	 * $query->filterByCoef(array('min' => 12)); // WHERE coef > 12
	 * </code>
	 *
	 * @param     mixed $coef The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByCoef($coef = null, $comparison = null)
	{
		if (is_array($coef)) {
			$useMinMax = false;
			if (isset($coef['min'])) {
				$this->addUsingAlias(JGroupesClassesPeer::COEF, $coef['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($coef['max'])) {
				$this->addUsingAlias(JGroupesClassesPeer::COEF, $coef['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JGroupesClassesPeer::COEF, $coef, $comparison);
	}

	/**
	 * Filter the query on the categorie_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCategorieId(1234); // WHERE categorie_id = 1234
	 * $query->filterByCategorieId(array(12, 34)); // WHERE categorie_id IN (12, 34)
	 * $query->filterByCategorieId(array('min' => 12)); // WHERE categorie_id > 12
	 * </code>
	 *
	 * @param     mixed $categorieId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByCategorieId($categorieId = null, $comparison = null)
	{
		if (is_array($categorieId)) {
			$useMinMax = false;
			if (isset($categorieId['min'])) {
				$this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($categorieId['max'])) {
				$this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JGroupesClassesPeer::CATEGORIE_ID, $categorieId, $comparison);
	}

	/**
	 * Filter the query on the saisie_ects column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterBySaisieEcts(true); // WHERE saisie_ects = true
	 * $query->filterBySaisieEcts('yes'); // WHERE saisie_ects = true
	 * </code>
	 *
	 * @param     boolean|string $saisieEcts The value to use as filter.
	 *              Non-boolean arguments are converted using the following rules:
	 *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterBySaisieEcts($saisieEcts = null, $comparison = null)
	{
		if (is_string($saisieEcts)) {
			$saisie_ects = in_array(strtolower($saisieEcts), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
		}
		return $this->addUsingAlias(JGroupesClassesPeer::SAISIE_ECTS, $saisieEcts, $comparison);
	}

	/**
	 * Filter the query on the valeur_ects column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByValeurEcts(1234); // WHERE valeur_ects = 1234
	 * $query->filterByValeurEcts(array(12, 34)); // WHERE valeur_ects IN (12, 34)
	 * $query->filterByValeurEcts(array('min' => 12)); // WHERE valeur_ects > 12
	 * </code>
	 *
	 * @param     mixed $valeurEcts The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByValeurEcts($valeurEcts = null, $comparison = null)
	{
		if (is_array($valeurEcts)) {
			$useMinMax = false;
			if (isset($valeurEcts['min'])) {
				$this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($valeurEcts['max'])) {
				$this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(JGroupesClassesPeer::VALEUR_ECTS, $valeurEcts, $comparison);
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe|PropelCollection $groupe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		if ($groupe instanceof Groupe) {
			return $this
				->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $groupe->getId(), $comparison);
		} elseif ($groupe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JGroupesClassesPeer::ID_GROUPE, $groupe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
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
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe|PropelCollection $classe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    JGroupesClassesQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		if ($classe instanceof Classe) {
			return $this
				->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $classe->getId(), $comparison);
		} elseif ($classe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(JGroupesClassesPeer::ID_CLASSE, $classe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    JGroupesClassesQuery The current query, for fluid interface
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

} // BaseJGroupesClassesQuery