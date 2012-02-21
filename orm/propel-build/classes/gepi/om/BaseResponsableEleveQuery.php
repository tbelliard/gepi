<?php


/**
 * Base class that represents a query for the 'resp_pers' table.
 *
 * Liste des responsables legaux des eleves
 *
 * @method     ResponsableEleveQuery orderByResponsableEleveId($order = Criteria::ASC) Order by the pers_id column
 * @method     ResponsableEleveQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     ResponsableEleveQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     ResponsableEleveQuery orderByPrenom($order = Criteria::ASC) Order by the prenom column
 * @method     ResponsableEleveQuery orderByCivilite($order = Criteria::ASC) Order by the civilite column
 * @method     ResponsableEleveQuery orderByTelPers($order = Criteria::ASC) Order by the tel_pers column
 * @method     ResponsableEleveQuery orderByTelPort($order = Criteria::ASC) Order by the tel_port column
 * @method     ResponsableEleveQuery orderByTelProf($order = Criteria::ASC) Order by the tel_prof column
 * @method     ResponsableEleveQuery orderByMel($order = Criteria::ASC) Order by the mel column
 * @method     ResponsableEleveQuery orderByAdresseId($order = Criteria::ASC) Order by the adr_id column
 *
 * @method     ResponsableEleveQuery groupByResponsableEleveId() Group by the pers_id column
 * @method     ResponsableEleveQuery groupByLogin() Group by the login column
 * @method     ResponsableEleveQuery groupByNom() Group by the nom column
 * @method     ResponsableEleveQuery groupByPrenom() Group by the prenom column
 * @method     ResponsableEleveQuery groupByCivilite() Group by the civilite column
 * @method     ResponsableEleveQuery groupByTelPers() Group by the tel_pers column
 * @method     ResponsableEleveQuery groupByTelPort() Group by the tel_port column
 * @method     ResponsableEleveQuery groupByTelProf() Group by the tel_prof column
 * @method     ResponsableEleveQuery groupByMel() Group by the mel column
 * @method     ResponsableEleveQuery groupByAdresseId() Group by the adr_id column
 *
 * @method     ResponsableEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ResponsableEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ResponsableEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ResponsableEleveQuery leftJoinAdresse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Adresse relation
 * @method     ResponsableEleveQuery rightJoinAdresse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Adresse relation
 * @method     ResponsableEleveQuery innerJoinAdresse($relationAlias = null) Adds a INNER JOIN clause to the query using the Adresse relation
 *
 * @method     ResponsableEleveQuery leftJoinResponsableInformation($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableInformation relation
 * @method     ResponsableEleveQuery rightJoinResponsableInformation($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableInformation relation
 * @method     ResponsableEleveQuery innerJoinResponsableInformation($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableInformation relation
 *
 * @method     ResponsableEleveQuery leftJoinJNotificationResponsableEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the JNotificationResponsableEleve relation
 * @method     ResponsableEleveQuery rightJoinJNotificationResponsableEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JNotificationResponsableEleve relation
 * @method     ResponsableEleveQuery innerJoinJNotificationResponsableEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the JNotificationResponsableEleve relation
 *
 * @method     ResponsableEleve findOne(PropelPDO $con = null) Return the first ResponsableEleve matching the query
 * @method     ResponsableEleve findOneOrCreate(PropelPDO $con = null) Return the first ResponsableEleve matching the query, or a new ResponsableEleve object populated from the query conditions when no match is found
 *
 * @method     ResponsableEleve findOneByResponsableEleveId(string $pers_id) Return the first ResponsableEleve filtered by the pers_id column
 * @method     ResponsableEleve findOneByLogin(string $login) Return the first ResponsableEleve filtered by the login column
 * @method     ResponsableEleve findOneByNom(string $nom) Return the first ResponsableEleve filtered by the nom column
 * @method     ResponsableEleve findOneByPrenom(string $prenom) Return the first ResponsableEleve filtered by the prenom column
 * @method     ResponsableEleve findOneByCivilite(string $civilite) Return the first ResponsableEleve filtered by the civilite column
 * @method     ResponsableEleve findOneByTelPers(string $tel_pers) Return the first ResponsableEleve filtered by the tel_pers column
 * @method     ResponsableEleve findOneByTelPort(string $tel_port) Return the first ResponsableEleve filtered by the tel_port column
 * @method     ResponsableEleve findOneByTelProf(string $tel_prof) Return the first ResponsableEleve filtered by the tel_prof column
 * @method     ResponsableEleve findOneByMel(string $mel) Return the first ResponsableEleve filtered by the mel column
 * @method     ResponsableEleve findOneByAdresseId(string $adr_id) Return the first ResponsableEleve filtered by the adr_id column
 *
 * @method     array findByResponsableEleveId(string $pers_id) Return ResponsableEleve objects filtered by the pers_id column
 * @method     array findByLogin(string $login) Return ResponsableEleve objects filtered by the login column
 * @method     array findByNom(string $nom) Return ResponsableEleve objects filtered by the nom column
 * @method     array findByPrenom(string $prenom) Return ResponsableEleve objects filtered by the prenom column
 * @method     array findByCivilite(string $civilite) Return ResponsableEleve objects filtered by the civilite column
 * @method     array findByTelPers(string $tel_pers) Return ResponsableEleve objects filtered by the tel_pers column
 * @method     array findByTelPort(string $tel_port) Return ResponsableEleve objects filtered by the tel_port column
 * @method     array findByTelProf(string $tel_prof) Return ResponsableEleve objects filtered by the tel_prof column
 * @method     array findByMel(string $mel) Return ResponsableEleve objects filtered by the mel column
 * @method     array findByAdresseId(string $adr_id) Return ResponsableEleve objects filtered by the adr_id column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableEleveQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseResponsableEleveQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'ResponsableEleve', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ResponsableEleveQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ResponsableEleveQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ResponsableEleveQuery) {
			return $criteria;
		}
		$query = new ResponsableEleveQuery();
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
	 * @return    ResponsableEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = ResponsableElevePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(ResponsableElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    ResponsableEleve A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT PERS_ID, LOGIN, NOM, PRENOM, CIVILITE, TEL_PERS, TEL_PORT, TEL_PROF, MEL, ADR_ID FROM resp_pers WHERE PERS_ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new ResponsableEleve();
			$obj->hydrate($row);
			ResponsableElevePeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    ResponsableEleve|array|mixed the result, formatted by the current formatter
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
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ResponsableElevePeer::PERS_ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ResponsableElevePeer::PERS_ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the pers_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByResponsableEleveId('fooValue');   // WHERE pers_id = 'fooValue'
	 * $query->filterByResponsableEleveId('%fooValue%'); // WHERE pers_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $responsableEleveId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleveId($responsableEleveId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($responsableEleveId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $responsableEleveId)) {
				$responsableEleveId = str_replace('*', '%', $responsableEleveId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::PERS_ID, $responsableEleveId, $comparison);
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
	 * @return    ResponsableEleveQuery The current query, for fluid interface
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
		return $this->addUsingAlias(ResponsableElevePeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNom('fooValue');   // WHERE nom = 'fooValue'
	 * $query->filterByNom('%fooValue%'); // WHERE nom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nom)) {
				$nom = str_replace('*', '%', $nom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the prenom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPrenom('fooValue');   // WHERE prenom = 'fooValue'
	 * $query->filterByPrenom('%fooValue%'); // WHERE prenom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $prenom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPrenom($prenom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($prenom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $prenom)) {
				$prenom = str_replace('*', '%', $prenom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::PRENOM, $prenom, $comparison);
	}

	/**
	 * Filter the query on the civilite column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCivilite('fooValue');   // WHERE civilite = 'fooValue'
	 * $query->filterByCivilite('%fooValue%'); // WHERE civilite LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $civilite The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByCivilite($civilite = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($civilite)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $civilite)) {
				$civilite = str_replace('*', '%', $civilite);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::CIVILITE, $civilite, $comparison);
	}

	/**
	 * Filter the query on the tel_pers column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTelPers('fooValue');   // WHERE tel_pers = 'fooValue'
	 * $query->filterByTelPers('%fooValue%'); // WHERE tel_pers LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $telPers The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByTelPers($telPers = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($telPers)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $telPers)) {
				$telPers = str_replace('*', '%', $telPers);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::TEL_PERS, $telPers, $comparison);
	}

	/**
	 * Filter the query on the tel_port column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTelPort('fooValue');   // WHERE tel_port = 'fooValue'
	 * $query->filterByTelPort('%fooValue%'); // WHERE tel_port LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $telPort The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByTelPort($telPort = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($telPort)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $telPort)) {
				$telPort = str_replace('*', '%', $telPort);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::TEL_PORT, $telPort, $comparison);
	}

	/**
	 * Filter the query on the tel_prof column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTelProf('fooValue');   // WHERE tel_prof = 'fooValue'
	 * $query->filterByTelProf('%fooValue%'); // WHERE tel_prof LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $telProf The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByTelProf($telProf = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($telProf)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $telProf)) {
				$telProf = str_replace('*', '%', $telProf);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::TEL_PROF, $telProf, $comparison);
	}

	/**
	 * Filter the query on the mel column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMel('fooValue');   // WHERE mel = 'fooValue'
	 * $query->filterByMel('%fooValue%'); // WHERE mel LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $mel The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByMel($mel = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($mel)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $mel)) {
				$mel = str_replace('*', '%', $mel);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::MEL, $mel, $comparison);
	}

	/**
	 * Filter the query on the adr_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdresseId('fooValue');   // WHERE adr_id = 'fooValue'
	 * $query->filterByAdresseId('%fooValue%'); // WHERE adr_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adresseId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAdresseId($adresseId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adresseId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adresseId)) {
				$adresseId = str_replace('*', '%', $adresseId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::ADR_ID, $adresseId, $comparison);
	}

	/**
	 * Filter the query by a related Adresse object
	 *
	 * @param     Adresse|PropelCollection $adresse The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAdresse($adresse, $comparison = null)
	{
		if ($adresse instanceof Adresse) {
			return $this
				->addUsingAlias(ResponsableElevePeer::ADR_ID, $adresse->getId(), $comparison);
		} elseif ($adresse instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(ResponsableElevePeer::ADR_ID, $adresse->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAdresse() only accepts arguments of type Adresse or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Adresse relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinAdresse($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Adresse');

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
			$this->addJoinObject($join, 'Adresse');
		}

		return $this;
	}

	/**
	 * Use the Adresse relation Adresse object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AdresseQuery A secondary query class using the current class as primary query
	 */
	public function useAdresseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAdresse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Adresse', 'AdresseQuery');
	}

	/**
	 * Filter the query by a related ResponsableInformation object
	 *
	 * @param     ResponsableInformation $responsableInformation  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableInformation($responsableInformation, $comparison = null)
	{
		if ($responsableInformation instanceof ResponsableInformation) {
			return $this
				->addUsingAlias(ResponsableElevePeer::PERS_ID, $responsableInformation->getResponsableEleveId(), $comparison);
		} elseif ($responsableInformation instanceof PropelCollection) {
			return $this
				->useResponsableInformationQuery()
				->filterByPrimaryKeys($responsableInformation->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByResponsableInformation() only accepts arguments of type ResponsableInformation or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableInformation relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinResponsableInformation($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ResponsableInformation');

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
			$this->addJoinObject($join, 'ResponsableInformation');
		}

		return $this;
	}

	/**
	 * Use the ResponsableInformation relation ResponsableInformation object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableInformationQuery A secondary query class using the current class as primary query
	 */
	public function useResponsableInformationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinResponsableInformation($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableInformation', 'ResponsableInformationQuery');
	}

	/**
	 * Filter the query by a related JNotificationResponsableEleve object
	 *
	 * @param     JNotificationResponsableEleve $jNotificationResponsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByJNotificationResponsableEleve($jNotificationResponsableEleve, $comparison = null)
	{
		if ($jNotificationResponsableEleve instanceof JNotificationResponsableEleve) {
			return $this
				->addUsingAlias(ResponsableElevePeer::PERS_ID, $jNotificationResponsableEleve->getResponsableEleveId(), $comparison);
		} elseif ($jNotificationResponsableEleve instanceof PropelCollection) {
			return $this
				->useJNotificationResponsableEleveQuery()
				->filterByPrimaryKeys($jNotificationResponsableEleve->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJNotificationResponsableEleve() only accepts arguments of type JNotificationResponsableEleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JNotificationResponsableEleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinJNotificationResponsableEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JNotificationResponsableEleve');

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
			$this->addJoinObject($join, 'JNotificationResponsableEleve');
		}

		return $this;
	}

	/**
	 * Use the JNotificationResponsableEleve relation JNotificationResponsableEleve object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JNotificationResponsableEleveQuery A secondary query class using the current class as primary query
	 */
	public function useJNotificationResponsableEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJNotificationResponsableEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JNotificationResponsableEleve', 'JNotificationResponsableEleveQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveNotification object
	 * using the j_notifications_resp_pers table as cross reference
	 *
	 * @param     AbsenceEleveNotification $absenceEleveNotification the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJNotificationResponsableEleveQuery()
			->filterByAbsenceEleveNotification($absenceEleveNotification, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     ResponsableEleve $responsableEleve Object to remove from the list of results
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function prune($responsableEleve = null)
	{
		if ($responsableEleve) {
			$this->addUsingAlias(ResponsableElevePeer::PERS_ID, $responsableEleve->getResponsableEleveId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseResponsableEleveQuery