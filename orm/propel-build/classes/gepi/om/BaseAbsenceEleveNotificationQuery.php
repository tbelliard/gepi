<?php


/**
 * Base class that represents a query for the 'a_notifications' table.
 *
 * Notification (a la famille) des absences
 *
 * @method     AbsenceEleveNotificationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveNotificationQuery orderByUtilisateurId($order = Criteria::ASC) Order by the utilisateur_id column
 * @method     AbsenceEleveNotificationQuery orderByATraitementId($order = Criteria::ASC) Order by the a_traitement_id column
 * @method     AbsenceEleveNotificationQuery orderByTypeNotification($order = Criteria::ASC) Order by the type_notification column
 * @method     AbsenceEleveNotificationQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     AbsenceEleveNotificationQuery orderByTelephone($order = Criteria::ASC) Order by the telephone column
 * @method     AbsenceEleveNotificationQuery orderByAdresseId($order = Criteria::ASC) Order by the adr_id column
 * @method     AbsenceEleveNotificationQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveNotificationQuery orderByStatutEnvoi($order = Criteria::ASC) Order by the statut_envoi column
 * @method     AbsenceEleveNotificationQuery orderByDateEnvoi($order = Criteria::ASC) Order by the date_envoi column
 * @method     AbsenceEleveNotificationQuery orderByErreurMessageEnvoi($order = Criteria::ASC) Order by the erreur_message_envoi column
 * @method     AbsenceEleveNotificationQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveNotificationQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     AbsenceEleveNotificationQuery groupById() Group by the id column
 * @method     AbsenceEleveNotificationQuery groupByUtilisateurId() Group by the utilisateur_id column
 * @method     AbsenceEleveNotificationQuery groupByATraitementId() Group by the a_traitement_id column
 * @method     AbsenceEleveNotificationQuery groupByTypeNotification() Group by the type_notification column
 * @method     AbsenceEleveNotificationQuery groupByEmail() Group by the email column
 * @method     AbsenceEleveNotificationQuery groupByTelephone() Group by the telephone column
 * @method     AbsenceEleveNotificationQuery groupByAdresseId() Group by the adr_id column
 * @method     AbsenceEleveNotificationQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveNotificationQuery groupByStatutEnvoi() Group by the statut_envoi column
 * @method     AbsenceEleveNotificationQuery groupByDateEnvoi() Group by the date_envoi column
 * @method     AbsenceEleveNotificationQuery groupByErreurMessageEnvoi() Group by the erreur_message_envoi column
 * @method     AbsenceEleveNotificationQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveNotificationQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     AbsenceEleveNotificationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveNotificationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveNotificationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveNotificationQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveNotificationQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveNotificationQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     AbsenceEleveNotificationQuery leftJoinAbsenceEleveTraitement($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveNotificationQuery rightJoinAbsenceEleveTraitement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveNotificationQuery innerJoinAbsenceEleveTraitement($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     AbsenceEleveNotificationQuery leftJoinAdresse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Adresse relation
 * @method     AbsenceEleveNotificationQuery rightJoinAdresse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Adresse relation
 * @method     AbsenceEleveNotificationQuery innerJoinAdresse($relationAlias = null) Adds a INNER JOIN clause to the query using the Adresse relation
 *
 * @method     AbsenceEleveNotificationQuery leftJoinJNotificationResponsableEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the JNotificationResponsableEleve relation
 * @method     AbsenceEleveNotificationQuery rightJoinJNotificationResponsableEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JNotificationResponsableEleve relation
 * @method     AbsenceEleveNotificationQuery innerJoinJNotificationResponsableEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the JNotificationResponsableEleve relation
 *
 * @method     AbsenceEleveNotification findOne(PropelPDO $con = null) Return the first AbsenceEleveNotification matching the query
 * @method     AbsenceEleveNotification findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveNotification matching the query, or a new AbsenceEleveNotification object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveNotification findOneById(int $id) Return the first AbsenceEleveNotification filtered by the id column
 * @method     AbsenceEleveNotification findOneByUtilisateurId(string $utilisateur_id) Return the first AbsenceEleveNotification filtered by the utilisateur_id column
 * @method     AbsenceEleveNotification findOneByATraitementId(int $a_traitement_id) Return the first AbsenceEleveNotification filtered by the a_traitement_id column
 * @method     AbsenceEleveNotification findOneByTypeNotification(int $type_notification) Return the first AbsenceEleveNotification filtered by the type_notification column
 * @method     AbsenceEleveNotification findOneByEmail(string $email) Return the first AbsenceEleveNotification filtered by the email column
 * @method     AbsenceEleveNotification findOneByTelephone(string $telephone) Return the first AbsenceEleveNotification filtered by the telephone column
 * @method     AbsenceEleveNotification findOneByAdresseId(string $adr_id) Return the first AbsenceEleveNotification filtered by the adr_id column
 * @method     AbsenceEleveNotification findOneByCommentaire(string $commentaire) Return the first AbsenceEleveNotification filtered by the commentaire column
 * @method     AbsenceEleveNotification findOneByStatutEnvoi(int $statut_envoi) Return the first AbsenceEleveNotification filtered by the statut_envoi column
 * @method     AbsenceEleveNotification findOneByDateEnvoi(string $date_envoi) Return the first AbsenceEleveNotification filtered by the date_envoi column
 * @method     AbsenceEleveNotification findOneByErreurMessageEnvoi(string $erreur_message_envoi) Return the first AbsenceEleveNotification filtered by the erreur_message_envoi column
 * @method     AbsenceEleveNotification findOneByCreatedAt(string $created_at) Return the first AbsenceEleveNotification filtered by the created_at column
 * @method     AbsenceEleveNotification findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveNotification filtered by the updated_at column
 *
 * @method     array findById(int $id) Return AbsenceEleveNotification objects filtered by the id column
 * @method     array findByUtilisateurId(string $utilisateur_id) Return AbsenceEleveNotification objects filtered by the utilisateur_id column
 * @method     array findByATraitementId(int $a_traitement_id) Return AbsenceEleveNotification objects filtered by the a_traitement_id column
 * @method     array findByTypeNotification(int $type_notification) Return AbsenceEleveNotification objects filtered by the type_notification column
 * @method     array findByEmail(string $email) Return AbsenceEleveNotification objects filtered by the email column
 * @method     array findByTelephone(string $telephone) Return AbsenceEleveNotification objects filtered by the telephone column
 * @method     array findByAdresseId(string $adr_id) Return AbsenceEleveNotification objects filtered by the adr_id column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveNotification objects filtered by the commentaire column
 * @method     array findByStatutEnvoi(int $statut_envoi) Return AbsenceEleveNotification objects filtered by the statut_envoi column
 * @method     array findByDateEnvoi(string $date_envoi) Return AbsenceEleveNotification objects filtered by the date_envoi column
 * @method     array findByErreurMessageEnvoi(string $erreur_message_envoi) Return AbsenceEleveNotification objects filtered by the erreur_message_envoi column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveNotification objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveNotification objects filtered by the updated_at column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveNotificationQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseAbsenceEleveNotificationQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveNotification', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveNotificationQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveNotificationQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveNotificationQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveNotificationQuery();
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
	 * @return    AbsenceEleveNotification|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AbsenceEleveNotificationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveNotificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    AbsenceEleveNotification A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, UTILISATEUR_ID, A_TRAITEMENT_ID, TYPE_NOTIFICATION, EMAIL, TELEPHONE, ADR_ID, COMMENTAIRE, STATUT_ENVOI, DATE_ENVOI, ERREUR_MESSAGE_ENVOI, CREATED_AT, UPDATED_AT FROM a_notifications WHERE ID = :p0';
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
			$obj = new AbsenceEleveNotification();
			$obj->hydrate($row);
			AbsenceEleveNotificationPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    AbsenceEleveNotification|array|mixed the result, formatted by the current formatter
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ID, $keys, Criteria::IN);
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the utilisateur_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUtilisateurId('fooValue');   // WHERE utilisateur_id = 'fooValue'
	 * $query->filterByUtilisateurId('%fooValue%'); // WHERE utilisateur_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $utilisateurId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurId($utilisateurId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($utilisateurId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $utilisateurId)) {
				$utilisateurId = str_replace('*', '%', $utilisateurId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::UTILISATEUR_ID, $utilisateurId, $comparison);
	}

	/**
	 * Filter the query on the a_traitement_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByATraitementId(1234); // WHERE a_traitement_id = 1234
	 * $query->filterByATraitementId(array(12, 34)); // WHERE a_traitement_id IN (12, 34)
	 * $query->filterByATraitementId(array('min' => 12)); // WHERE a_traitement_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveTraitement()
	 *
	 * @param     mixed $aTraitementId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByATraitementId($aTraitementId = null, $comparison = null)
	{
		if (is_array($aTraitementId)) {
			$useMinMax = false;
			if (isset($aTraitementId['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $aTraitementId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($aTraitementId['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $aTraitementId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $aTraitementId, $comparison);
	}

	/**
	 * Filter the query on the type_notification column
	 *
	 * @param     mixed $typeNotification The value to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByTypeNotification($typeNotification = null, $comparison = null)
	{
		$valueSet = AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION);
		if (is_scalar($typeNotification)) {
			if (!in_array($typeNotification, $valueSet)) {
				throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $typeNotification));
			}
			$typeNotification = array_search($typeNotification, $valueSet);
		} elseif (is_array($typeNotification)) {
			$convertedValues = array();
			foreach ($typeNotification as $value) {
				if (!in_array($value, $valueSet)) {
					throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
				}
				$convertedValues []= array_search($value, $valueSet);
			}
			$typeNotification = $convertedValues;
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION, $typeNotification, $comparison);
	}

	/**
	 * Filter the query on the email column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
	 * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $email The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByEmail($email = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($email)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $email)) {
				$email = str_replace('*', '%', $email);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::EMAIL, $email, $comparison);
	}

	/**
	 * Filter the query on the telephone column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTelephone('fooValue');   // WHERE telephone = 'fooValue'
	 * $query->filterByTelephone('%fooValue%'); // WHERE telephone LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $telephone The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByTelephone($telephone = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($telephone)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $telephone)) {
				$telephone = str_replace('*', '%', $telephone);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::TELEPHONE, $telephone, $comparison);
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ADR_ID, $adresseId, $comparison);
	}

	/**
	 * Filter the query on the commentaire column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCommentaire('fooValue');   // WHERE commentaire = 'fooValue'
	 * $query->filterByCommentaire('%fooValue%'); // WHERE commentaire LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $commentaire The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByCommentaire($commentaire = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($commentaire)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $commentaire)) {
				$commentaire = str_replace('*', '%', $commentaire);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::COMMENTAIRE, $commentaire, $comparison);
	}

	/**
	 * Filter the query on the statut_envoi column
	 *
	 * @param     mixed $statutEnvoi The value to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByStatutEnvoi($statutEnvoi = null, $comparison = null)
	{
		$valueSet = AbsenceEleveNotificationPeer::getValueSet(AbsenceEleveNotificationPeer::STATUT_ENVOI);
		if (is_scalar($statutEnvoi)) {
			if (!in_array($statutEnvoi, $valueSet)) {
				throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $statutEnvoi));
			}
			$statutEnvoi = array_search($statutEnvoi, $valueSet);
		} elseif (is_array($statutEnvoi)) {
			$convertedValues = array();
			foreach ($statutEnvoi as $value) {
				if (!in_array($value, $valueSet)) {
					throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
				}
				$convertedValues []= array_search($value, $valueSet);
			}
			$statutEnvoi = $convertedValues;
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::STATUT_ENVOI, $statutEnvoi, $comparison);
	}

	/**
	 * Filter the query on the date_envoi column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDateEnvoi('2011-03-14'); // WHERE date_envoi = '2011-03-14'
	 * $query->filterByDateEnvoi('now'); // WHERE date_envoi = '2011-03-14'
	 * $query->filterByDateEnvoi(array('max' => 'yesterday')); // WHERE date_envoi > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $dateEnvoi The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByDateEnvoi($dateEnvoi = null, $comparison = null)
	{
		if (is_array($dateEnvoi)) {
			$useMinMax = false;
			if (isset($dateEnvoi['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::DATE_ENVOI, $dateEnvoi['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateEnvoi['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::DATE_ENVOI, $dateEnvoi['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::DATE_ENVOI, $dateEnvoi, $comparison);
	}

	/**
	 * Filter the query on the erreur_message_envoi column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByErreurMessageEnvoi('fooValue');   // WHERE erreur_message_envoi = 'fooValue'
	 * $query->filterByErreurMessageEnvoi('%fooValue%'); // WHERE erreur_message_envoi LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $erreurMessageEnvoi The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByErreurMessageEnvoi($erreurMessageEnvoi = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($erreurMessageEnvoi)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $erreurMessageEnvoi)) {
				$erreurMessageEnvoi = str_replace('*', '%', $erreurMessageEnvoi);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ERREUR_MESSAGE_ENVOI, $erreurMessageEnvoi, $comparison);
	}

	/**
	 * Filter the query on the created_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
	 * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $createdAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::CREATED_AT, $createdAt, $comparison);
	}

	/**
	 * Filter the query on the updated_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
	 * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $updatedAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::UPDATED_AT, $updatedAt, $comparison);
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::UTILISATEUR_ID, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement|PropelCollection $absenceEleveTraitement The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = null)
	{
		if ($absenceEleveTraitement instanceof AbsenceEleveTraitement) {
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $absenceEleveTraitement->getId(), $comparison);
		} elseif ($absenceEleveTraitement instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $absenceEleveTraitement->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveTraitement() only accepts arguments of type AbsenceEleveTraitement or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTraitement');

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
			$this->addJoinObject($join, 'AbsenceEleveTraitement');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveTraitement relation AbsenceEleveTraitement object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTraitementQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Filter the query by a related Adresse object
	 *
	 * @param     Adresse|PropelCollection $adresse The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByAdresse($adresse, $comparison = null)
	{
		if ($adresse instanceof Adresse) {
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::ADR_ID, $adresse->getId(), $comparison);
		} elseif ($adresse instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::ADR_ID, $adresse->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
	 * Filter the query by a related JNotificationResponsableEleve object
	 *
	 * @param     JNotificationResponsableEleve $jNotificationResponsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByJNotificationResponsableEleve($jNotificationResponsableEleve, $comparison = null)
	{
		if ($jNotificationResponsableEleve instanceof JNotificationResponsableEleve) {
			return $this
				->addUsingAlias(AbsenceEleveNotificationPeer::ID, $jNotificationResponsableEleve->getANotificationId(), $comparison);
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
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
	 * Filter the query by a related ResponsableEleve object
	 * using the j_notifications_resp_pers table as cross reference
	 *
	 * @param     ResponsableEleve $responsableEleve the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleve($responsableEleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJNotificationResponsableEleveQuery()
			->filterByResponsableEleve($responsableEleve, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveNotification $absenceEleveNotification Object to remove from the list of results
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveNotification = null)
	{
		if ($absenceEleveNotification) {
			$this->addUsingAlias(AbsenceEleveNotificationPeer::ID, $absenceEleveNotification->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveNotificationPeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveNotificationPeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveNotificationPeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveNotificationPeer::CREATED_AT);
	}

} // BaseAbsenceEleveNotificationQuery