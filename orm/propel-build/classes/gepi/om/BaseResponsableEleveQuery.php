<?php


/**
 * Base class that represents a query for the 'resp_pers' table.
 *
 * Liste des responsables legaux des eleves
 *
 * @method     ResponsableEleveQuery orderByPersId($order = Criteria::ASC) Order by the pers_id column
 * @method     ResponsableEleveQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     ResponsableEleveQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     ResponsableEleveQuery orderByPrenom($order = Criteria::ASC) Order by the prenom column
 * @method     ResponsableEleveQuery orderByCivilite($order = Criteria::ASC) Order by the civilite column
 * @method     ResponsableEleveQuery orderByTelPers($order = Criteria::ASC) Order by the tel_pers column
 * @method     ResponsableEleveQuery orderByTelPort($order = Criteria::ASC) Order by the tel_port column
 * @method     ResponsableEleveQuery orderByTelProf($order = Criteria::ASC) Order by the tel_prof column
 * @method     ResponsableEleveQuery orderByMel($order = Criteria::ASC) Order by the mel column
 * @method     ResponsableEleveQuery orderByAdrId($order = Criteria::ASC) Order by the adr_id column
 *
 * @method     ResponsableEleveQuery groupByPersId() Group by the pers_id column
 * @method     ResponsableEleveQuery groupByLogin() Group by the login column
 * @method     ResponsableEleveQuery groupByNom() Group by the nom column
 * @method     ResponsableEleveQuery groupByPrenom() Group by the prenom column
 * @method     ResponsableEleveQuery groupByCivilite() Group by the civilite column
 * @method     ResponsableEleveQuery groupByTelPers() Group by the tel_pers column
 * @method     ResponsableEleveQuery groupByTelPort() Group by the tel_port column
 * @method     ResponsableEleveQuery groupByTelProf() Group by the tel_prof column
 * @method     ResponsableEleveQuery groupByMel() Group by the mel column
 * @method     ResponsableEleveQuery groupByAdrId() Group by the adr_id column
 *
 * @method     ResponsableEleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ResponsableEleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ResponsableEleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ResponsableEleveQuery leftJoinResponsableEleveAdresse($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableEleveAdresse relation
 * @method     ResponsableEleveQuery rightJoinResponsableEleveAdresse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableEleveAdresse relation
 * @method     ResponsableEleveQuery innerJoinResponsableEleveAdresse($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableEleveAdresse relation
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
 * @method     ResponsableEleve findOneByPersId(string $pers_id) Return the first ResponsableEleve filtered by the pers_id column
 * @method     ResponsableEleve findOneByLogin(string $login) Return the first ResponsableEleve filtered by the login column
 * @method     ResponsableEleve findOneByNom(string $nom) Return the first ResponsableEleve filtered by the nom column
 * @method     ResponsableEleve findOneByPrenom(string $prenom) Return the first ResponsableEleve filtered by the prenom column
 * @method     ResponsableEleve findOneByCivilite(string $civilite) Return the first ResponsableEleve filtered by the civilite column
 * @method     ResponsableEleve findOneByTelPers(string $tel_pers) Return the first ResponsableEleve filtered by the tel_pers column
 * @method     ResponsableEleve findOneByTelPort(string $tel_port) Return the first ResponsableEleve filtered by the tel_port column
 * @method     ResponsableEleve findOneByTelProf(string $tel_prof) Return the first ResponsableEleve filtered by the tel_prof column
 * @method     ResponsableEleve findOneByMel(string $mel) Return the first ResponsableEleve filtered by the mel column
 * @method     ResponsableEleve findOneByAdrId(string $adr_id) Return the first ResponsableEleve filtered by the adr_id column
 *
 * @method     array findByPersId(string $pers_id) Return ResponsableEleve objects filtered by the pers_id column
 * @method     array findByLogin(string $login) Return ResponsableEleve objects filtered by the login column
 * @method     array findByNom(string $nom) Return ResponsableEleve objects filtered by the nom column
 * @method     array findByPrenom(string $prenom) Return ResponsableEleve objects filtered by the prenom column
 * @method     array findByCivilite(string $civilite) Return ResponsableEleve objects filtered by the civilite column
 * @method     array findByTelPers(string $tel_pers) Return ResponsableEleve objects filtered by the tel_pers column
 * @method     array findByTelPort(string $tel_port) Return ResponsableEleve objects filtered by the tel_port column
 * @method     array findByTelProf(string $tel_prof) Return ResponsableEleve objects filtered by the tel_prof column
 * @method     array findByMel(string $mel) Return ResponsableEleve objects filtered by the mel column
 * @method     array findByAdrId(string $adr_id) Return ResponsableEleve objects filtered by the adr_id column
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
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    ResponsableEleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = ResponsableElevePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(12, 56, 832), $con);
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
	 * @param     string $persId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByPersId($persId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($persId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $persId)) {
				$persId = str_replace('*', '%', $persId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::PERS_ID, $persId, $comparison);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $prenom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $civilite The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $telPers The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $telPort The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $telProf The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $mel The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $adrId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByAdrId($adrId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adrId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adrId)) {
				$adrId = str_replace('*', '%', $adrId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ResponsableElevePeer::ADR_ID, $adrId, $comparison);
	}

	/**
	 * Filter the query by a related ResponsableEleveAdresse object
	 *
	 * @param     ResponsableEleveAdresse $responsableEleveAdresse  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleveAdresse($responsableEleveAdresse, $comparison = null)
	{
		return $this
			->addUsingAlias(ResponsableElevePeer::ADR_ID, $responsableEleveAdresse->getAdrId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleveAdresse relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveQuery The current query, for fluid interface
	 */
	public function joinResponsableEleveAdresse($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ResponsableEleveAdresse');
		
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
			$this->addJoinObject($join, 'ResponsableEleveAdresse');
		}
		
		return $this;
	}

	/**
	 * Use the ResponsableEleveAdresse relation ResponsableEleveAdresse object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableEleveAdresseQuery A secondary query class using the current class as primary query
	 */
	public function useResponsableEleveAdresseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinResponsableEleveAdresse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableEleveAdresse', 'ResponsableEleveAdresseQuery');
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
		return $this
			->addUsingAlias(ResponsableElevePeer::PERS_ID, $responsableInformation->getPersId(), $comparison);
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
		return $this
			->addUsingAlias(ResponsableElevePeer::PERS_ID, $jNotificationResponsableEleve->getPersId(), $comparison);
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
			$this->addUsingAlias(ResponsableElevePeer::PERS_ID, $responsableEleve->getPersId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseResponsableEleveQuery
