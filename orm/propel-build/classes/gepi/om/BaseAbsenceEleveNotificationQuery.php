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
 * @method     AbsenceEleveNotificationQuery orderByAdrId($order = Criteria::ASC) Order by the adr_id column
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
 * @method     AbsenceEleveNotificationQuery groupByAdrId() Group by the adr_id column
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
 * @method     AbsenceEleveNotificationQuery leftJoinResponsableEleveAdresse($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableEleveAdresse relation
 * @method     AbsenceEleveNotificationQuery rightJoinResponsableEleveAdresse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableEleveAdresse relation
 * @method     AbsenceEleveNotificationQuery innerJoinResponsableEleveAdresse($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableEleveAdresse relation
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
 * @method     AbsenceEleveNotification findOneByAdrId(string $adr_id) Return the first AbsenceEleveNotification filtered by the adr_id column
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
 * @method     array findByAdrId(string $adr_id) Return AbsenceEleveNotification objects filtered by the adr_id column
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
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    AbsenceEleveNotification|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveNotificationPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $utilisateurId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     int|array $aTraitementId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $typeNotification The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByTypeNotification($typeNotification = null, $comparison = null)
	{
		if (is_array($typeNotification)) {
			$useMinMax = false;
			if (isset($typeNotification['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION, $typeNotification['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($typeNotification['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION, $typeNotification['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION, $typeNotification, $comparison);
	}

	/**
	 * Filter the query on the email column
	 * 
	 * @param     string $email The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $telephone The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string $adrId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::ADR_ID, $adrId, $comparison);
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     int|array $statutEnvoi The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByStatutEnvoi($statutEnvoi = null, $comparison = null)
	{
		if (is_array($statutEnvoi)) {
			$useMinMax = false;
			if (isset($statutEnvoi['min'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::STATUT_ENVOI, $statutEnvoi['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($statutEnvoi['max'])) {
				$this->addUsingAlias(AbsenceEleveNotificationPeer::STATUT_ENVOI, $statutEnvoi['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveNotificationPeer::STATUT_ENVOI, $statutEnvoi, $comparison);
	}

	/**
	 * Filter the query on the date_envoi column
	 * 
	 * @param     string|array $dateEnvoi The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $erreurMessageEnvoi The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string|array $createdAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string|array $updatedAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveNotificationPeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
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
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $absenceEleveTraitement->getId(), $comparison);
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
	 * Filter the query by a related ResponsableEleveAdresse object
	 *
	 * @param     ResponsableEleveAdresse $responsableEleveAdresse  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByResponsableEleveAdresse($responsableEleveAdresse, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveNotificationPeer::ADR_ID, $responsableEleveAdresse->getAdrId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableEleveAdresse relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
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
	 * Filter the query by a related JNotificationResponsableEleve object
	 *
	 * @param     JNotificationResponsableEleve $jNotificationResponsableEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveNotificationQuery The current query, for fluid interface
	 */
	public function filterByJNotificationResponsableEleve($jNotificationResponsableEleve, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveNotificationPeer::ID, $jNotificationResponsableEleve->getANotificationId(), $comparison);
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
