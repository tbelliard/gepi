<?php


/**
 * Base class that represents a query for the 'ct_private_entry' table.
 *
 * Notice privee du cahier de texte
 *
 * @method     CahierTexteNoticePriveeQuery orderByIdCt($order = Criteria::ASC) Order by the id_ct column
 * @method     CahierTexteNoticePriveeQuery orderByHeureEntry($order = Criteria::ASC) Order by the heure_entry column
 * @method     CahierTexteNoticePriveeQuery orderByDateCt($order = Criteria::ASC) Order by the date_ct column
 * @method     CahierTexteNoticePriveeQuery orderByContenu($order = Criteria::ASC) Order by the contenu column
 * @method     CahierTexteNoticePriveeQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     CahierTexteNoticePriveeQuery orderByIdLogin($order = Criteria::ASC) Order by the id_login column
 * @method     CahierTexteNoticePriveeQuery orderByIdSequence($order = Criteria::ASC) Order by the id_sequence column
 *
 * @method     CahierTexteNoticePriveeQuery groupByIdCt() Group by the id_ct column
 * @method     CahierTexteNoticePriveeQuery groupByHeureEntry() Group by the heure_entry column
 * @method     CahierTexteNoticePriveeQuery groupByDateCt() Group by the date_ct column
 * @method     CahierTexteNoticePriveeQuery groupByContenu() Group by the contenu column
 * @method     CahierTexteNoticePriveeQuery groupByIdGroupe() Group by the id_groupe column
 * @method     CahierTexteNoticePriveeQuery groupByIdLogin() Group by the id_login column
 * @method     CahierTexteNoticePriveeQuery groupByIdSequence() Group by the id_sequence column
 *
 * @method     CahierTexteNoticePriveeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteNoticePriveeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteNoticePriveeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteNoticePriveeQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteNoticePriveeQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteNoticePriveeQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     CahierTexteNoticePriveeQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteNoticePriveeQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteNoticePriveeQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     CahierTexteNoticePriveeQuery leftJoinCahierTexteSequence($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteNoticePriveeQuery rightJoinCahierTexteSequence($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteNoticePriveeQuery innerJoinCahierTexteSequence($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteSequence relation
 *
 * @method     CahierTexteNoticePrivee findOne(PropelPDO $con = null) Return the first CahierTexteNoticePrivee matching the query
 * @method     CahierTexteNoticePrivee findOneOrCreate(PropelPDO $con = null) Return the first CahierTexteNoticePrivee matching the query, or a new CahierTexteNoticePrivee object populated from the query conditions when no match is found
 *
 * @method     CahierTexteNoticePrivee findOneByIdCt(int $id_ct) Return the first CahierTexteNoticePrivee filtered by the id_ct column
 * @method     CahierTexteNoticePrivee findOneByHeureEntry(string $heure_entry) Return the first CahierTexteNoticePrivee filtered by the heure_entry column
 * @method     CahierTexteNoticePrivee findOneByDateCt(int $date_ct) Return the first CahierTexteNoticePrivee filtered by the date_ct column
 * @method     CahierTexteNoticePrivee findOneByContenu(string $contenu) Return the first CahierTexteNoticePrivee filtered by the contenu column
 * @method     CahierTexteNoticePrivee findOneByIdGroupe(int $id_groupe) Return the first CahierTexteNoticePrivee filtered by the id_groupe column
 * @method     CahierTexteNoticePrivee findOneByIdLogin(string $id_login) Return the first CahierTexteNoticePrivee filtered by the id_login column
 * @method     CahierTexteNoticePrivee findOneByIdSequence(int $id_sequence) Return the first CahierTexteNoticePrivee filtered by the id_sequence column
 *
 * @method     array findByIdCt(int $id_ct) Return CahierTexteNoticePrivee objects filtered by the id_ct column
 * @method     array findByHeureEntry(string $heure_entry) Return CahierTexteNoticePrivee objects filtered by the heure_entry column
 * @method     array findByDateCt(int $date_ct) Return CahierTexteNoticePrivee objects filtered by the date_ct column
 * @method     array findByContenu(string $contenu) Return CahierTexteNoticePrivee objects filtered by the contenu column
 * @method     array findByIdGroupe(int $id_groupe) Return CahierTexteNoticePrivee objects filtered by the id_groupe column
 * @method     array findByIdLogin(string $id_login) Return CahierTexteNoticePrivee objects filtered by the id_login column
 * @method     array findByIdSequence(int $id_sequence) Return CahierTexteNoticePrivee objects filtered by the id_sequence column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteNoticePriveeQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseCahierTexteNoticePriveeQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteNoticePrivee', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteNoticePriveeQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteNoticePriveeQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteNoticePriveeQuery) {
			return $criteria;
		}
		$query = new CahierTexteNoticePriveeQuery();
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
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CahierTexteNoticePrivee|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = CahierTexteNoticePriveePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteNoticePriveePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    CahierTexteNoticePrivee A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID_CT, HEURE_ENTRY, DATE_CT, CONTENU, ID_GROUPE, ID_LOGIN, ID_SEQUENCE FROM ct_private_entry WHERE ID_CT = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new CahierTexteNoticePrivee();
			$obj->hydrate($row);
			CahierTexteNoticePriveePeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    CahierTexteNoticePrivee|array|mixed the result, formatted by the current formatter
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
	 * $objs = $c->findPks(array(12, 56, 832), $con);
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
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_CT, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_CT, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_ct column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdCt(1234); // WHERE id_ct = 1234
	 * $query->filterByIdCt(array(12, 34)); // WHERE id_ct IN (12, 34)
	 * $query->filterByIdCt(array('min' => 12)); // WHERE id_ct > 12
	 * </code>
	 *
	 * @param     mixed $idCt The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByIdCt($idCt = null, $comparison = null)
	{
		if (is_array($idCt) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_CT, $idCt, $comparison);
	}

	/**
	 * Filter the query on the heure_entry column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByHeureEntry('2011-03-14'); // WHERE heure_entry = '2011-03-14'
	 * $query->filterByHeureEntry('now'); // WHERE heure_entry = '2011-03-14'
	 * $query->filterByHeureEntry(array('max' => 'yesterday')); // WHERE heure_entry > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $heureEntry The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByHeureEntry($heureEntry = null, $comparison = null)
	{
		if (is_array($heureEntry)) {
			$useMinMax = false;
			if (isset($heureEntry['min'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::HEURE_ENTRY, $heureEntry['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($heureEntry['max'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::HEURE_ENTRY, $heureEntry['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::HEURE_ENTRY, $heureEntry, $comparison);
	}

	/**
	 * Filter the query on the date_ct column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDateCt(1234); // WHERE date_ct = 1234
	 * $query->filterByDateCt(array(12, 34)); // WHERE date_ct IN (12, 34)
	 * $query->filterByDateCt(array('min' => 12)); // WHERE date_ct > 12
	 * </code>
	 *
	 * @param     mixed $dateCt The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByDateCt($dateCt = null, $comparison = null)
	{
		if (is_array($dateCt)) {
			$useMinMax = false;
			if (isset($dateCt['min'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::DATE_CT, $dateCt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateCt['max'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::DATE_CT, $dateCt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::DATE_CT, $dateCt, $comparison);
	}

	/**
	 * Filter the query on the contenu column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByContenu('fooValue');   // WHERE contenu = 'fooValue'
	 * $query->filterByContenu('%fooValue%'); // WHERE contenu LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $contenu The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByContenu($contenu = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($contenu)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $contenu)) {
				$contenu = str_replace('*', '%', $contenu);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::CONTENU, $contenu, $comparison);
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
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe)) {
			$useMinMax = false;
			if (isset($idGroupe['min'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::ID_GROUPE, $idGroupe['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idGroupe['max'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::ID_GROUPE, $idGroupe['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_GROUPE, $idGroupe, $comparison);
	}

	/**
	 * Filter the query on the id_login column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdLogin('fooValue');   // WHERE id_login = 'fooValue'
	 * $query->filterByIdLogin('%fooValue%'); // WHERE id_login LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $idLogin The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByIdLogin($idLogin = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idLogin)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idLogin)) {
				$idLogin = str_replace('*', '%', $idLogin);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_LOGIN, $idLogin, $comparison);
	}

	/**
	 * Filter the query on the id_sequence column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdSequence(1234); // WHERE id_sequence = 1234
	 * $query->filterByIdSequence(array(12, 34)); // WHERE id_sequence IN (12, 34)
	 * $query->filterByIdSequence(array('min' => 12)); // WHERE id_sequence > 12
	 * </code>
	 *
	 * @see       filterByCahierTexteSequence()
	 *
	 * @param     mixed $idSequence The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByIdSequence($idSequence = null, $comparison = null)
	{
		if (is_array($idSequence)) {
			$useMinMax = false;
			if (isset($idSequence['min'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::ID_SEQUENCE, $idSequence['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idSequence['max'])) {
				$this->addUsingAlias(CahierTexteNoticePriveePeer::ID_SEQUENCE, $idSequence['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteNoticePriveePeer::ID_SEQUENCE, $idSequence, $comparison);
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe|PropelCollection $groupe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		if ($groupe instanceof Groupe) {
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_GROUPE, $groupe->getId(), $comparison);
		} elseif ($groupe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_GROUPE, $groupe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_LOGIN, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useUtilisateurProfessionnelQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related CahierTexteSequence object
	 *
	 * @param     CahierTexteSequence|PropelCollection $cahierTexteSequence The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteSequence($cahierTexteSequence, $comparison = null)
	{
		if ($cahierTexteSequence instanceof CahierTexteSequence) {
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_SEQUENCE, $cahierTexteSequence->getId(), $comparison);
		} elseif ($cahierTexteSequence instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteNoticePriveePeer::ID_SEQUENCE, $cahierTexteSequence->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByCahierTexteSequence() only accepts arguments of type CahierTexteSequence or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteSequence relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function joinCahierTexteSequence($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteSequence');

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
			$this->addJoinObject($join, 'CahierTexteSequence');
		}

		return $this;
	}

	/**
	 * Use the CahierTexteSequence relation CahierTexteSequence object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteSequenceQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteSequence($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteSequence', 'CahierTexteSequenceQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteNoticePrivee $cahierTexteNoticePrivee Object to remove from the list of results
	 *
	 * @return    CahierTexteNoticePriveeQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteNoticePrivee = null)
	{
		if ($cahierTexteNoticePrivee) {
			$this->addUsingAlias(CahierTexteNoticePriveePeer::ID_CT, $cahierTexteNoticePrivee->getIdCt(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseCahierTexteNoticePriveeQuery