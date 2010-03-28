<?php


/**
 * Base class that represents a query for the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * @method     UtilisateurProfessionnelQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     UtilisateurProfessionnelQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     UtilisateurProfessionnelQuery orderByPrenom($order = Criteria::ASC) Order by the prenom column
 * @method     UtilisateurProfessionnelQuery orderByCivilite($order = Criteria::ASC) Order by the civilite column
 * @method     UtilisateurProfessionnelQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     UtilisateurProfessionnelQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     UtilisateurProfessionnelQuery orderByShowEmail($order = Criteria::ASC) Order by the show_email column
 * @method     UtilisateurProfessionnelQuery orderByStatut($order = Criteria::ASC) Order by the statut column
 * @method     UtilisateurProfessionnelQuery orderByEtat($order = Criteria::ASC) Order by the etat column
 * @method     UtilisateurProfessionnelQuery orderByChangeMdp($order = Criteria::ASC) Order by the change_mdp column
 * @method     UtilisateurProfessionnelQuery orderByDateVerrouillage($order = Criteria::ASC) Order by the date_verrouillage column
 * @method     UtilisateurProfessionnelQuery orderByPasswordTicket($order = Criteria::ASC) Order by the password_ticket column
 * @method     UtilisateurProfessionnelQuery orderByTicketExpiration($order = Criteria::ASC) Order by the ticket_expiration column
 * @method     UtilisateurProfessionnelQuery orderByNiveauAlerte($order = Criteria::ASC) Order by the niveau_alerte column
 * @method     UtilisateurProfessionnelQuery orderByObservationSecurite($order = Criteria::ASC) Order by the observation_securite column
 * @method     UtilisateurProfessionnelQuery orderByTempDir($order = Criteria::ASC) Order by the temp_dir column
 * @method     UtilisateurProfessionnelQuery orderByNumind($order = Criteria::ASC) Order by the numind column
 * @method     UtilisateurProfessionnelQuery orderByAuthMode($order = Criteria::ASC) Order by the auth_mode column
 *
 * @method     UtilisateurProfessionnelQuery groupByLogin() Group by the login column
 * @method     UtilisateurProfessionnelQuery groupByNom() Group by the nom column
 * @method     UtilisateurProfessionnelQuery groupByPrenom() Group by the prenom column
 * @method     UtilisateurProfessionnelQuery groupByCivilite() Group by the civilite column
 * @method     UtilisateurProfessionnelQuery groupByPassword() Group by the password column
 * @method     UtilisateurProfessionnelQuery groupByEmail() Group by the email column
 * @method     UtilisateurProfessionnelQuery groupByShowEmail() Group by the show_email column
 * @method     UtilisateurProfessionnelQuery groupByStatut() Group by the statut column
 * @method     UtilisateurProfessionnelQuery groupByEtat() Group by the etat column
 * @method     UtilisateurProfessionnelQuery groupByChangeMdp() Group by the change_mdp column
 * @method     UtilisateurProfessionnelQuery groupByDateVerrouillage() Group by the date_verrouillage column
 * @method     UtilisateurProfessionnelQuery groupByPasswordTicket() Group by the password_ticket column
 * @method     UtilisateurProfessionnelQuery groupByTicketExpiration() Group by the ticket_expiration column
 * @method     UtilisateurProfessionnelQuery groupByNiveauAlerte() Group by the niveau_alerte column
 * @method     UtilisateurProfessionnelQuery groupByObservationSecurite() Group by the observation_securite column
 * @method     UtilisateurProfessionnelQuery groupByTempDir() Group by the temp_dir column
 * @method     UtilisateurProfessionnelQuery groupByNumind() Group by the numind column
 * @method     UtilisateurProfessionnelQuery groupByAuthMode() Group by the auth_mode column
 *
 * @method     UtilisateurProfessionnelQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     UtilisateurProfessionnelQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     UtilisateurProfessionnelQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     UtilisateurProfessionnelQuery leftJoinJGroupesProfesseurs($relationAlias = '') Adds a LEFT JOIN clause to the query using the JGroupesProfesseurs relation
 * @method     UtilisateurProfessionnelQuery rightJoinJGroupesProfesseurs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JGroupesProfesseurs relation
 * @method     UtilisateurProfessionnelQuery innerJoinJGroupesProfesseurs($relationAlias = '') Adds a INNER JOIN clause to the query using the JGroupesProfesseurs relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinCahierTexteCompteRendu($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     UtilisateurProfessionnelQuery rightJoinCahierTexteCompteRendu($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteCompteRendu relation
 * @method     UtilisateurProfessionnelQuery innerJoinCahierTexteCompteRendu($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteCompteRendu relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinCahierTexteTravailAFaire($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     UtilisateurProfessionnelQuery rightJoinCahierTexteTravailAFaire($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteTravailAFaire relation
 * @method     UtilisateurProfessionnelQuery innerJoinCahierTexteTravailAFaire($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteTravailAFaire relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinCahierTexteNoticePrivee($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     UtilisateurProfessionnelQuery rightJoinCahierTexteNoticePrivee($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteNoticePrivee relation
 * @method     UtilisateurProfessionnelQuery innerJoinCahierTexteNoticePrivee($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteNoticePrivee relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinJEleveCpe($relationAlias = '') Adds a LEFT JOIN clause to the query using the JEleveCpe relation
 * @method     UtilisateurProfessionnelQuery rightJoinJEleveCpe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JEleveCpe relation
 * @method     UtilisateurProfessionnelQuery innerJoinJEleveCpe($relationAlias = '') Adds a INNER JOIN clause to the query using the JEleveCpe relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinJEleveProfesseurPrincipal($relationAlias = '') Adds a LEFT JOIN clause to the query using the JEleveProfesseurPrincipal relation
 * @method     UtilisateurProfessionnelQuery rightJoinJEleveProfesseurPrincipal($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JEleveProfesseurPrincipal relation
 * @method     UtilisateurProfessionnelQuery innerJoinJEleveProfesseurPrincipal($relationAlias = '') Adds a INNER JOIN clause to the query using the JEleveProfesseurPrincipal relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a LEFT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     UtilisateurProfessionnelQuery rightJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     UtilisateurProfessionnelQuery innerJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a INNER JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinAbsenceEleveSaisie($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     UtilisateurProfessionnelQuery rightJoinAbsenceEleveSaisie($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     UtilisateurProfessionnelQuery innerJoinAbsenceEleveSaisie($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinAbsenceEleveTraitement($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     UtilisateurProfessionnelQuery rightJoinAbsenceEleveTraitement($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     UtilisateurProfessionnelQuery innerJoinAbsenceEleveTraitement($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinAbsenceEleveEnvoi($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     UtilisateurProfessionnelQuery rightJoinAbsenceEleveEnvoi($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveEnvoi relation
 * @method     UtilisateurProfessionnelQuery innerJoinAbsenceEleveEnvoi($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveEnvoi relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinPreferenceUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the PreferenceUtilisateurProfessionnel relation
 * @method     UtilisateurProfessionnelQuery rightJoinPreferenceUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the PreferenceUtilisateurProfessionnel relation
 * @method     UtilisateurProfessionnelQuery innerJoinPreferenceUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the PreferenceUtilisateurProfessionnel relation
 *
 * @method     UtilisateurProfessionnelQuery leftJoinEdtEmplacementCours($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     UtilisateurProfessionnelQuery rightJoinEdtEmplacementCours($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     UtilisateurProfessionnelQuery innerJoinEdtEmplacementCours($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     UtilisateurProfessionnel findOne(PropelPDO $con = null) Return the first UtilisateurProfessionnel matching the query
 * @method     UtilisateurProfessionnel findOneByLogin(string $login) Return the first UtilisateurProfessionnel filtered by the login column
 * @method     UtilisateurProfessionnel findOneByNom(string $nom) Return the first UtilisateurProfessionnel filtered by the nom column
 * @method     UtilisateurProfessionnel findOneByPrenom(string $prenom) Return the first UtilisateurProfessionnel filtered by the prenom column
 * @method     UtilisateurProfessionnel findOneByCivilite(string $civilite) Return the first UtilisateurProfessionnel filtered by the civilite column
 * @method     UtilisateurProfessionnel findOneByPassword(string $password) Return the first UtilisateurProfessionnel filtered by the password column
 * @method     UtilisateurProfessionnel findOneByEmail(string $email) Return the first UtilisateurProfessionnel filtered by the email column
 * @method     UtilisateurProfessionnel findOneByShowEmail(string $show_email) Return the first UtilisateurProfessionnel filtered by the show_email column
 * @method     UtilisateurProfessionnel findOneByStatut(string $statut) Return the first UtilisateurProfessionnel filtered by the statut column
 * @method     UtilisateurProfessionnel findOneByEtat(string $etat) Return the first UtilisateurProfessionnel filtered by the etat column
 * @method     UtilisateurProfessionnel findOneByChangeMdp(string $change_mdp) Return the first UtilisateurProfessionnel filtered by the change_mdp column
 * @method     UtilisateurProfessionnel findOneByDateVerrouillage(string $date_verrouillage) Return the first UtilisateurProfessionnel filtered by the date_verrouillage column
 * @method     UtilisateurProfessionnel findOneByPasswordTicket(string $password_ticket) Return the first UtilisateurProfessionnel filtered by the password_ticket column
 * @method     UtilisateurProfessionnel findOneByTicketExpiration(string $ticket_expiration) Return the first UtilisateurProfessionnel filtered by the ticket_expiration column
 * @method     UtilisateurProfessionnel findOneByNiveauAlerte(int $niveau_alerte) Return the first UtilisateurProfessionnel filtered by the niveau_alerte column
 * @method     UtilisateurProfessionnel findOneByObservationSecurite(int $observation_securite) Return the first UtilisateurProfessionnel filtered by the observation_securite column
 * @method     UtilisateurProfessionnel findOneByTempDir(string $temp_dir) Return the first UtilisateurProfessionnel filtered by the temp_dir column
 * @method     UtilisateurProfessionnel findOneByNumind(string $numind) Return the first UtilisateurProfessionnel filtered by the numind column
 * @method     UtilisateurProfessionnel findOneByAuthMode(string $auth_mode) Return the first UtilisateurProfessionnel filtered by the auth_mode column
 *
 * @method     array findByLogin(string $login) Return UtilisateurProfessionnel objects filtered by the login column
 * @method     array findByNom(string $nom) Return UtilisateurProfessionnel objects filtered by the nom column
 * @method     array findByPrenom(string $prenom) Return UtilisateurProfessionnel objects filtered by the prenom column
 * @method     array findByCivilite(string $civilite) Return UtilisateurProfessionnel objects filtered by the civilite column
 * @method     array findByPassword(string $password) Return UtilisateurProfessionnel objects filtered by the password column
 * @method     array findByEmail(string $email) Return UtilisateurProfessionnel objects filtered by the email column
 * @method     array findByShowEmail(string $show_email) Return UtilisateurProfessionnel objects filtered by the show_email column
 * @method     array findByStatut(string $statut) Return UtilisateurProfessionnel objects filtered by the statut column
 * @method     array findByEtat(string $etat) Return UtilisateurProfessionnel objects filtered by the etat column
 * @method     array findByChangeMdp(string $change_mdp) Return UtilisateurProfessionnel objects filtered by the change_mdp column
 * @method     array findByDateVerrouillage(string $date_verrouillage) Return UtilisateurProfessionnel objects filtered by the date_verrouillage column
 * @method     array findByPasswordTicket(string $password_ticket) Return UtilisateurProfessionnel objects filtered by the password_ticket column
 * @method     array findByTicketExpiration(string $ticket_expiration) Return UtilisateurProfessionnel objects filtered by the ticket_expiration column
 * @method     array findByNiveauAlerte(int $niveau_alerte) Return UtilisateurProfessionnel objects filtered by the niveau_alerte column
 * @method     array findByObservationSecurite(int $observation_securite) Return UtilisateurProfessionnel objects filtered by the observation_securite column
 * @method     array findByTempDir(string $temp_dir) Return UtilisateurProfessionnel objects filtered by the temp_dir column
 * @method     array findByNumind(string $numind) Return UtilisateurProfessionnel objects filtered by the numind column
 * @method     array findByAuthMode(string $auth_mode) Return UtilisateurProfessionnel objects filtered by the auth_mode column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseUtilisateurProfessionnelQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseUtilisateurProfessionnelQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'UtilisateurProfessionnel', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new UtilisateurProfessionnelQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    UtilisateurProfessionnelQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof UtilisateurProfessionnelQuery) {
			return $criteria;
		}
		$query = new UtilisateurProfessionnelQuery();
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
	 * @return    UtilisateurProfessionnel|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = UtilisateurProfessionnelPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * $objs = $c->findPks(array(12, 56, 832), $con);
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
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($login)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $login, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $login)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, str_replace('*', '%', $login), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $login, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the prenom column
	 * 
	 * @param     string $prenom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPrenom($prenom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($prenom)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PRENOM, $prenom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $prenom)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PRENOM, str_replace('*', '%', $prenom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PRENOM, $prenom, $comparison);
		}
	}

	/**
	 * Filter the query on the civilite column
	 * 
	 * @param     string $civilite The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByCivilite($civilite = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($civilite)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CIVILITE, $civilite, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $civilite)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CIVILITE, str_replace('*', '%', $civilite), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CIVILITE, $civilite, $comparison);
		}
	}

	/**
	 * Filter the query on the password column
	 * 
	 * @param     string $password The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPassword($password = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($password)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD, $password, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $password)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD, str_replace('*', '%', $password), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD, $password, $comparison);
		}
	}

	/**
	 * Filter the query on the email column
	 * 
	 * @param     string $email The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByEmail($email = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($email)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::EMAIL, $email, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $email)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::EMAIL, str_replace('*', '%', $email), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::EMAIL, $email, $comparison);
		}
	}

	/**
	 * Filter the query on the show_email column
	 * 
	 * @param     string $showEmail The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByShowEmail($showEmail = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($showEmail)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::SHOW_EMAIL, $showEmail, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $showEmail)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::SHOW_EMAIL, str_replace('*', '%', $showEmail), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::SHOW_EMAIL, $showEmail, $comparison);
		}
	}

	/**
	 * Filter the query on the statut column
	 * 
	 * @param     string $statut The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByStatut($statut = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($statut)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::STATUT, $statut, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $statut)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::STATUT, str_replace('*', '%', $statut), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::STATUT, $statut, $comparison);
		}
	}

	/**
	 * Filter the query on the etat column
	 * 
	 * @param     string $etat The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByEtat($etat = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($etat)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::ETAT, $etat, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $etat)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::ETAT, str_replace('*', '%', $etat), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::ETAT, $etat, $comparison);
		}
	}

	/**
	 * Filter the query on the change_mdp column
	 * 
	 * @param     string $changeMdp The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByChangeMdp($changeMdp = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($changeMdp)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CHANGE_MDP, $changeMdp, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $changeMdp)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CHANGE_MDP, str_replace('*', '%', $changeMdp), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::CHANGE_MDP, $changeMdp, $comparison);
		}
	}

	/**
	 * Filter the query on the date_verrouillage column
	 * 
	 * @param     string|array $dateVerrouillage The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByDateVerrouillage($dateVerrouillage = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($dateVerrouillage)) {
			if (array_values($dateVerrouillage) === $dateVerrouillage) {
				return $this->addUsingAlias(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE, $dateVerrouillage, Criteria::IN);
			} else {
				if (isset($dateVerrouillage['min'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE, $dateVerrouillage['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($dateVerrouillage['max'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE, $dateVerrouillage['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE, $dateVerrouillage, $comparison);
		}
	}

	/**
	 * Filter the query on the password_ticket column
	 * 
	 * @param     string $passwordTicket The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPasswordTicket($passwordTicket = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($passwordTicket)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD_TICKET, $passwordTicket, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $passwordTicket)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD_TICKET, str_replace('*', '%', $passwordTicket), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::PASSWORD_TICKET, $passwordTicket, $comparison);
		}
	}

	/**
	 * Filter the query on the ticket_expiration column
	 * 
	 * @param     string|array $ticketExpiration The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByTicketExpiration($ticketExpiration = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($ticketExpiration)) {
			if (array_values($ticketExpiration) === $ticketExpiration) {
				return $this->addUsingAlias(UtilisateurProfessionnelPeer::TICKET_EXPIRATION, $ticketExpiration, Criteria::IN);
			} else {
				if (isset($ticketExpiration['min'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::TICKET_EXPIRATION, $ticketExpiration['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($ticketExpiration['max'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::TICKET_EXPIRATION, $ticketExpiration['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::TICKET_EXPIRATION, $ticketExpiration, $comparison);
		}
	}

	/**
	 * Filter the query on the niveau_alerte column
	 * 
	 * @param     int|array $niveauAlerte The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByNiveauAlerte($niveauAlerte = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($niveauAlerte)) {
			if (array_values($niveauAlerte) === $niveauAlerte) {
				return $this->addUsingAlias(UtilisateurProfessionnelPeer::NIVEAU_ALERTE, $niveauAlerte, Criteria::IN);
			} else {
				if (isset($niveauAlerte['min'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::NIVEAU_ALERTE, $niveauAlerte['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($niveauAlerte['max'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::NIVEAU_ALERTE, $niveauAlerte['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NIVEAU_ALERTE, $niveauAlerte, $comparison);
		}
	}

	/**
	 * Filter the query on the observation_securite column
	 * 
	 * @param     int|array $observationSecurite The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByObservationSecurite($observationSecurite = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($observationSecurite)) {
			if (array_values($observationSecurite) === $observationSecurite) {
				return $this->addUsingAlias(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE, $observationSecurite, Criteria::IN);
			} else {
				if (isset($observationSecurite['min'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE, $observationSecurite['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($observationSecurite['max'])) {
					$this->addUsingAlias(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE, $observationSecurite['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE, $observationSecurite, $comparison);
		}
	}

	/**
	 * Filter the query on the temp_dir column
	 * 
	 * @param     string $tempDir The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByTempDir($tempDir = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($tempDir)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::TEMP_DIR, $tempDir, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $tempDir)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::TEMP_DIR, str_replace('*', '%', $tempDir), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::TEMP_DIR, $tempDir, $comparison);
		}
	}

	/**
	 * Filter the query on the numind column
	 * 
	 * @param     string $numind The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByNumind($numind = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numind)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NUMIND, $numind, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $numind)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NUMIND, str_replace('*', '%', $numind), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::NUMIND, $numind, $comparison);
		}
	}

	/**
	 * Filter the query on the auth_mode column
	 * 
	 * @param     string $authMode The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByAuthMode($authMode = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($authMode)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::AUTH_MODE, $authMode, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $authMode)) {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::AUTH_MODE, str_replace('*', '%', $authMode), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(UtilisateurProfessionnelPeer::AUTH_MODE, $authMode, $comparison);
		}
	}

	/**
	 * Filter the query by a related JGroupesProfesseurs object
	 *
	 * @param     JGroupesProfesseurs $jGroupesProfesseurs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByJGroupesProfesseurs($jGroupesProfesseurs, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $jGroupesProfesseurs->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JGroupesProfesseurs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinJGroupesProfesseurs($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JGroupesProfesseurs');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JGroupesProfesseurs');
		}
		
		return $this;
	}

	/**
	 * Use the JGroupesProfesseurs relation JGroupesProfesseurs object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesProfesseursQuery A secondary query class using the current class as primary query
	 */
	public function useJGroupesProfesseursQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJGroupesProfesseurs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JGroupesProfesseurs', 'JGroupesProfesseursQuery');
	}

	/**
	 * Filter the query by a related CahierTexteCompteRendu object
	 *
	 * @param     CahierTexteCompteRendu $cahierTexteCompteRendu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteCompteRendu($cahierTexteCompteRendu, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $cahierTexteCompteRendu->getIdLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteCompteRendu relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinCahierTexteCompteRendu($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteCompteRendu');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteCompteRendu');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteCompteRendu relation CahierTexteCompteRendu object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteCompteRenduQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteCompteRendu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteCompteRendu', 'CahierTexteCompteRenduQuery');
	}

	/**
	 * Filter the query by a related CahierTexteTravailAFaire object
	 *
	 * @param     CahierTexteTravailAFaire $cahierTexteTravailAFaire  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteTravailAFaire($cahierTexteTravailAFaire, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $cahierTexteTravailAFaire->getIdLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteTravailAFaire relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinCahierTexteTravailAFaire($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteTravailAFaire');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteTravailAFaire');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteTravailAFaire relation CahierTexteTravailAFaire object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteTravailAFaireQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteTravailAFaire($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteTravailAFaire', 'CahierTexteTravailAFaireQuery');
	}

	/**
	 * Filter the query by a related CahierTexteNoticePrivee object
	 *
	 * @param     CahierTexteNoticePrivee $cahierTexteNoticePrivee  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteNoticePrivee($cahierTexteNoticePrivee, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $cahierTexteNoticePrivee->getIdLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteNoticePrivee relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinCahierTexteNoticePrivee($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteNoticePrivee');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteNoticePrivee');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteNoticePrivee relation CahierTexteNoticePrivee object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteNoticePriveeQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteNoticePriveeQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteNoticePrivee($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteNoticePrivee', 'CahierTexteNoticePriveeQuery');
	}

	/**
	 * Filter the query by a related JEleveCpe object
	 *
	 * @param     JEleveCpe $jEleveCpe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByJEleveCpe($jEleveCpe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $jEleveCpe->getCpeLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveCpe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinJEleveCpe($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveCpe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JEleveCpe');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveCpe relation JEleveCpe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveCpeQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveCpeQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveCpe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveCpe', 'JEleveCpeQuery');
	}

	/**
	 * Filter the query by a related JEleveProfesseurPrincipal object
	 *
	 * @param     JEleveProfesseurPrincipal $jEleveProfesseurPrincipal  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByJEleveProfesseurPrincipal($jEleveProfesseurPrincipal, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $jEleveProfesseurPrincipal->getProfesseur(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveProfesseurPrincipal relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinJEleveProfesseurPrincipal($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveProfesseurPrincipal');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JEleveProfesseurPrincipal');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveProfesseurPrincipal relation JEleveProfesseurPrincipal object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveProfesseurPrincipalQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveProfesseurPrincipalQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveProfesseurPrincipal($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveProfesseurPrincipal', 'JEleveProfesseurPrincipalQuery');
	}

	/**
	 * Filter the query by a related JAidUtilisateursProfessionnels object
	 *
	 * @param     JAidUtilisateursProfessionnels $jAidUtilisateursProfessionnels  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $jAidUtilisateursProfessionnels->getIdUtilisateur(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JAidUtilisateursProfessionnels relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinJAidUtilisateursProfessionnels($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JAidUtilisateursProfessionnels');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JAidUtilisateursProfessionnels');
		}
		
		return $this;
	}

	/**
	 * Use the JAidUtilisateursProfessionnels relation JAidUtilisateursProfessionnels object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidUtilisateursProfessionnelsQuery A secondary query class using the current class as primary query
	 */
	public function useJAidUtilisateursProfessionnelsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJAidUtilisateursProfessionnels($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JAidUtilisateursProfessionnels', 'JAidUtilisateursProfessionnelsQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $absenceEleveSaisie->getUtilisateurId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisie');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveSaisie');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveSaisie relation AbsenceEleveSaisie object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveSaisieQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $absenceEleveTraitement->getUtilisateurId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTraitement');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useAbsenceEleveTraitementQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveEnvoi object
	 *
	 * @param     AbsenceEleveEnvoi $absenceEleveEnvoi  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveEnvoi($absenceEleveEnvoi, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $absenceEleveEnvoi->getUtilisateurId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveEnvoi relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveEnvoi($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveEnvoi');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveEnvoi');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveEnvoi relation AbsenceEleveEnvoi object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveEnvoiQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveEnvoi($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveEnvoi', 'AbsenceEleveEnvoiQuery');
	}

	/**
	 * Filter the query by a related PreferenceUtilisateurProfessionnel object
	 *
	 * @param     PreferenceUtilisateurProfessionnel $preferenceUtilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByPreferenceUtilisateurProfessionnel($preferenceUtilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $preferenceUtilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the PreferenceUtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinPreferenceUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PreferenceUtilisateurProfessionnel');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'PreferenceUtilisateurProfessionnel');
		}
		
		return $this;
	}

	/**
	 * Use the PreferenceUtilisateurProfessionnel relation PreferenceUtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PreferenceUtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function usePreferenceUtilisateurProfessionnelQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPreferenceUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PreferenceUtilisateurProfessionnel', 'PreferenceUtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $edtEmplacementCours->getLoginProf(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtEmplacementCours');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'EdtEmplacementCours');
		}
		
		return $this;
	}

	/**
	 * Use the EdtEmplacementCours relation EdtEmplacementCours object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtEmplacementCoursQuery A secondary query class using the current class as primary query
	 */
	public function useEdtEmplacementCoursQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtEmplacementCours($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtEmplacementCours', 'EdtEmplacementCoursQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 * using the j_groupes_professeurs table as cross reference
	 *
	 * @param     Groupe $groupe the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJGroupesProfesseursQuery()
				->filterByGroupe($groupe, $comparison)
			->endUse();
	}
	
	/**
	 * Filter the query by a related Eleve object
	 * using the j_eleves_cpe table as cross reference
	 *
	 * @param     Eleve $eleve the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJEleveCpeQuery()
				->filterByEleve($eleve, $comparison)
			->endUse();
	}
	
	/**
	 * Exclude object from result
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel Object to remove from the list of results
	 *
	 * @return    UtilisateurProfessionnelQuery The current query, for fluid interface
	 */
	public function prune($utilisateurProfessionnel = null)
	{
		if ($utilisateurProfessionnel) {
			$this->addUsingAlias(UtilisateurProfessionnelPeer::LOGIN, $utilisateurProfessionnel->getLogin(), Criteria::NOT_EQUAL);
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

} // BaseUtilisateurProfessionnelQuery
