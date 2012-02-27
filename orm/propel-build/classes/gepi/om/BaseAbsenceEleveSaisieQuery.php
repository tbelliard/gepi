<?php


/**
 * Base class that represents a query for the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * @method     AbsenceEleveSaisieQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveSaisieQuery orderByUtilisateurId($order = Criteria::ASC) Order by the utilisateur_id column
 * @method     AbsenceEleveSaisieQuery orderByEleveId($order = Criteria::ASC) Order by the eleve_id column
 * @method     AbsenceEleveSaisieQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveSaisieQuery orderByDebutAbs($order = Criteria::ASC) Order by the debut_abs column
 * @method     AbsenceEleveSaisieQuery orderByFinAbs($order = Criteria::ASC) Order by the fin_abs column
 * @method     AbsenceEleveSaisieQuery orderByIdEdtCreneau($order = Criteria::ASC) Order by the id_edt_creneau column
 * @method     AbsenceEleveSaisieQuery orderByIdEdtEmplacementCours($order = Criteria::ASC) Order by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisieQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     AbsenceEleveSaisieQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 * @method     AbsenceEleveSaisieQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     AbsenceEleveSaisieQuery orderByIdSIncidents($order = Criteria::ASC) Order by the id_s_incidents column
 * @method     AbsenceEleveSaisieQuery orderByIdLieu($order = Criteria::ASC) Order by the id_lieu column
 * @method     AbsenceEleveSaisieQuery orderByDeletedBy($order = Criteria::ASC) Order by the deleted_by column
 * @method     AbsenceEleveSaisieQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveSaisieQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     AbsenceEleveSaisieQuery orderByDeletedAt($order = Criteria::ASC) Order by the deleted_at column
 * @method     AbsenceEleveSaisieQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     AbsenceEleveSaisieQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     AbsenceEleveSaisieQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 *
 * @method     AbsenceEleveSaisieQuery groupById() Group by the id column
 * @method     AbsenceEleveSaisieQuery groupByUtilisateurId() Group by the utilisateur_id column
 * @method     AbsenceEleveSaisieQuery groupByEleveId() Group by the eleve_id column
 * @method     AbsenceEleveSaisieQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveSaisieQuery groupByDebutAbs() Group by the debut_abs column
 * @method     AbsenceEleveSaisieQuery groupByFinAbs() Group by the fin_abs column
 * @method     AbsenceEleveSaisieQuery groupByIdEdtCreneau() Group by the id_edt_creneau column
 * @method     AbsenceEleveSaisieQuery groupByIdEdtEmplacementCours() Group by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisieQuery groupByIdGroupe() Group by the id_groupe column
 * @method     AbsenceEleveSaisieQuery groupByIdClasse() Group by the id_classe column
 * @method     AbsenceEleveSaisieQuery groupByIdAid() Group by the id_aid column
 * @method     AbsenceEleveSaisieQuery groupByIdSIncidents() Group by the id_s_incidents column
 * @method     AbsenceEleveSaisieQuery groupByIdLieu() Group by the id_lieu column
 * @method     AbsenceEleveSaisieQuery groupByDeletedBy() Group by the deleted_by column
 * @method     AbsenceEleveSaisieQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveSaisieQuery groupByUpdatedAt() Group by the updated_at column
 * @method     AbsenceEleveSaisieQuery groupByDeletedAt() Group by the deleted_at column
 * @method     AbsenceEleveSaisieQuery groupByVersion() Group by the version column
 * @method     AbsenceEleveSaisieQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     AbsenceEleveSaisieQuery groupByVersionCreatedBy() Group by the version_created_by column
 *
 * @method     AbsenceEleveSaisieQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveSaisieQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveSaisieQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveSaisieQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveSaisieQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveSaisieQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     AbsenceEleveSaisieQuery rightJoinEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     AbsenceEleveSaisieQuery innerJoinEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEdtCreneau($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtCreneau relation
 * @method     AbsenceEleveSaisieQuery rightJoinEdtCreneau($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtCreneau relation
 * @method     AbsenceEleveSaisieQuery innerJoinEdtCreneau($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtCreneau relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEdtEmplacementCours($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AbsenceEleveSaisieQuery rightJoinEdtEmplacementCours($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AbsenceEleveSaisieQuery innerJoinEdtEmplacementCours($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     AbsenceEleveSaisieQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     AbsenceEleveSaisieQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     AbsenceEleveSaisieQuery rightJoinClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     AbsenceEleveSaisieQuery innerJoinClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinAidDetails($relationAlias = null) Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     AbsenceEleveSaisieQuery rightJoinAidDetails($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     AbsenceEleveSaisieQuery innerJoinAidDetails($relationAlias = null) Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinAbsenceEleveLieu($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveLieu relation
 * @method     AbsenceEleveSaisieQuery rightJoinAbsenceEleveLieu($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveLieu relation
 * @method     AbsenceEleveSaisieQuery innerJoinAbsenceEleveLieu($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveLieu relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinJTraitementSaisieEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveSaisieQuery rightJoinJTraitementSaisieEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveSaisieQuery innerJoinJTraitementSaisieEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the JTraitementSaisieEleve relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinAbsenceEleveSaisieVersion($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisieVersion relation
 * @method     AbsenceEleveSaisieQuery rightJoinAbsenceEleveSaisieVersion($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisieVersion relation
 * @method     AbsenceEleveSaisieQuery innerJoinAbsenceEleveSaisieVersion($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisieVersion relation
 *
 * @method     AbsenceEleveSaisie findOne(PropelPDO $con = null) Return the first AbsenceEleveSaisie matching the query
 * @method     AbsenceEleveSaisie findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveSaisie matching the query, or a new AbsenceEleveSaisie object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveSaisie findOneById(int $id) Return the first AbsenceEleveSaisie filtered by the id column
 * @method     AbsenceEleveSaisie findOneByUtilisateurId(string $utilisateur_id) Return the first AbsenceEleveSaisie filtered by the utilisateur_id column
 * @method     AbsenceEleveSaisie findOneByEleveId(int $eleve_id) Return the first AbsenceEleveSaisie filtered by the eleve_id column
 * @method     AbsenceEleveSaisie findOneByCommentaire(string $commentaire) Return the first AbsenceEleveSaisie filtered by the commentaire column
 * @method     AbsenceEleveSaisie findOneByDebutAbs(string $debut_abs) Return the first AbsenceEleveSaisie filtered by the debut_abs column
 * @method     AbsenceEleveSaisie findOneByFinAbs(string $fin_abs) Return the first AbsenceEleveSaisie filtered by the fin_abs column
 * @method     AbsenceEleveSaisie findOneByIdEdtCreneau(int $id_edt_creneau) Return the first AbsenceEleveSaisie filtered by the id_edt_creneau column
 * @method     AbsenceEleveSaisie findOneByIdEdtEmplacementCours(int $id_edt_emplacement_cours) Return the first AbsenceEleveSaisie filtered by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisie findOneByIdGroupe(int $id_groupe) Return the first AbsenceEleveSaisie filtered by the id_groupe column
 * @method     AbsenceEleveSaisie findOneByIdClasse(int $id_classe) Return the first AbsenceEleveSaisie filtered by the id_classe column
 * @method     AbsenceEleveSaisie findOneByIdAid(int $id_aid) Return the first AbsenceEleveSaisie filtered by the id_aid column
 * @method     AbsenceEleveSaisie findOneByIdSIncidents(int $id_s_incidents) Return the first AbsenceEleveSaisie filtered by the id_s_incidents column
 * @method     AbsenceEleveSaisie findOneByIdLieu(int $id_lieu) Return the first AbsenceEleveSaisie filtered by the id_lieu column
 * @method     AbsenceEleveSaisie findOneByDeletedBy(string $deleted_by) Return the first AbsenceEleveSaisie filtered by the deleted_by column
 * @method     AbsenceEleveSaisie findOneByCreatedAt(string $created_at) Return the first AbsenceEleveSaisie filtered by the created_at column
 * @method     AbsenceEleveSaisie findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveSaisie filtered by the updated_at column
 * @method     AbsenceEleveSaisie findOneByDeletedAt(string $deleted_at) Return the first AbsenceEleveSaisie filtered by the deleted_at column
 * @method     AbsenceEleveSaisie findOneByVersion(int $version) Return the first AbsenceEleveSaisie filtered by the version column
 * @method     AbsenceEleveSaisie findOneByVersionCreatedAt(string $version_created_at) Return the first AbsenceEleveSaisie filtered by the version_created_at column
 * @method     AbsenceEleveSaisie findOneByVersionCreatedBy(string $version_created_by) Return the first AbsenceEleveSaisie filtered by the version_created_by column
 *
 * @method     array findById(int $id) Return AbsenceEleveSaisie objects filtered by the id column
 * @method     array findByUtilisateurId(string $utilisateur_id) Return AbsenceEleveSaisie objects filtered by the utilisateur_id column
 * @method     array findByEleveId(int $eleve_id) Return AbsenceEleveSaisie objects filtered by the eleve_id column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveSaisie objects filtered by the commentaire column
 * @method     array findByDebutAbs(string $debut_abs) Return AbsenceEleveSaisie objects filtered by the debut_abs column
 * @method     array findByFinAbs(string $fin_abs) Return AbsenceEleveSaisie objects filtered by the fin_abs column
 * @method     array findByIdEdtCreneau(int $id_edt_creneau) Return AbsenceEleveSaisie objects filtered by the id_edt_creneau column
 * @method     array findByIdEdtEmplacementCours(int $id_edt_emplacement_cours) Return AbsenceEleveSaisie objects filtered by the id_edt_emplacement_cours column
 * @method     array findByIdGroupe(int $id_groupe) Return AbsenceEleveSaisie objects filtered by the id_groupe column
 * @method     array findByIdClasse(int $id_classe) Return AbsenceEleveSaisie objects filtered by the id_classe column
 * @method     array findByIdAid(int $id_aid) Return AbsenceEleveSaisie objects filtered by the id_aid column
 * @method     array findByIdSIncidents(int $id_s_incidents) Return AbsenceEleveSaisie objects filtered by the id_s_incidents column
 * @method     array findByIdLieu(int $id_lieu) Return AbsenceEleveSaisie objects filtered by the id_lieu column
 * @method     array findByDeletedBy(string $deleted_by) Return AbsenceEleveSaisie objects filtered by the deleted_by column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveSaisie objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveSaisie objects filtered by the updated_at column
 * @method     array findByDeletedAt(string $deleted_at) Return AbsenceEleveSaisie objects filtered by the deleted_at column
 * @method     array findByVersion(int $version) Return AbsenceEleveSaisie objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return AbsenceEleveSaisie objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return AbsenceEleveSaisie objects filtered by the version_created_by column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisieQuery extends ModelCriteria
{
	
	// soft_delete behavior
	protected static $softDelete = true;
	protected $localSoftDelete = true;

	/**
	 * Initializes internal state of BaseAbsenceEleveSaisieQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveSaisie', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveSaisieQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveSaisieQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveSaisieQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveSaisieQuery();
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
	 * @return    AbsenceEleveSaisie|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AbsenceEleveSaisiePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    AbsenceEleveSaisie A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, UTILISATEUR_ID, ELEVE_ID, COMMENTAIRE, DEBUT_ABS, FIN_ABS, ID_EDT_CRENEAU, ID_EDT_EMPLACEMENT_COURS, ID_GROUPE, ID_CLASSE, ID_AID, ID_S_INCIDENTS, ID_LIEU, DELETED_BY, CREATED_AT, UPDATED_AT, DELETED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY FROM a_saisies WHERE ID = :p0';
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
			$obj = new AbsenceEleveSaisie();
			$obj->hydrate($row);
			AbsenceEleveSaisiePeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    AbsenceEleveSaisie|array|mixed the result, formatted by the current formatter
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID, $keys, Criteria::IN);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID, $id, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $utilisateurId, $comparison);
	}

	/**
	 * Filter the query on the eleve_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEleveId(1234); // WHERE eleve_id = 1234
	 * $query->filterByEleveId(array(12, 34)); // WHERE eleve_id IN (12, 34)
	 * $query->filterByEleveId(array('min' => 12)); // WHERE eleve_id > 12
	 * </code>
	 *
	 * @see       filterByEleve()
	 *
	 * @param     mixed $eleveId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEleveId($eleveId = null, $comparison = null)
	{
		if (is_array($eleveId)) {
			$useMinMax = false;
			if (isset($eleveId['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleveId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($eleveId['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleveId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleveId, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::COMMENTAIRE, $commentaire, $comparison);
	}

	/**
	 * Filter the query on the debut_abs column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDebutAbs('2011-03-14'); // WHERE debut_abs = '2011-03-14'
	 * $query->filterByDebutAbs('now'); // WHERE debut_abs = '2011-03-14'
	 * $query->filterByDebutAbs(array('max' => 'yesterday')); // WHERE debut_abs > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $debutAbs The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByDebutAbs($debutAbs = null, $comparison = null)
	{
		if (is_array($debutAbs)) {
			$useMinMax = false;
			if (isset($debutAbs['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::DEBUT_ABS, $debutAbs['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($debutAbs['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::DEBUT_ABS, $debutAbs['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::DEBUT_ABS, $debutAbs, $comparison);
	}

	/**
	 * Filter the query on the fin_abs column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFinAbs('2011-03-14'); // WHERE fin_abs = '2011-03-14'
	 * $query->filterByFinAbs('now'); // WHERE fin_abs = '2011-03-14'
	 * $query->filterByFinAbs(array('max' => 'yesterday')); // WHERE fin_abs > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $finAbs The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByFinAbs($finAbs = null, $comparison = null)
	{
		if (is_array($finAbs)) {
			$useMinMax = false;
			if (isset($finAbs['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::FIN_ABS, $finAbs['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($finAbs['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::FIN_ABS, $finAbs['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::FIN_ABS, $finAbs, $comparison);
	}

	/**
	 * Filter the query on the id_edt_creneau column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdEdtCreneau(1234); // WHERE id_edt_creneau = 1234
	 * $query->filterByIdEdtCreneau(array(12, 34)); // WHERE id_edt_creneau IN (12, 34)
	 * $query->filterByIdEdtCreneau(array('min' => 12)); // WHERE id_edt_creneau > 12
	 * </code>
	 *
	 * @see       filterByEdtCreneau()
	 *
	 * @param     mixed $idEdtCreneau The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdEdtCreneau($idEdtCreneau = null, $comparison = null)
	{
		if (is_array($idEdtCreneau)) {
			$useMinMax = false;
			if (isset($idEdtCreneau['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $idEdtCreneau['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idEdtCreneau['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $idEdtCreneau['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $idEdtCreneau, $comparison);
	}

	/**
	 * Filter the query on the id_edt_emplacement_cours column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdEdtEmplacementCours(1234); // WHERE id_edt_emplacement_cours = 1234
	 * $query->filterByIdEdtEmplacementCours(array(12, 34)); // WHERE id_edt_emplacement_cours IN (12, 34)
	 * $query->filterByIdEdtEmplacementCours(array('min' => 12)); // WHERE id_edt_emplacement_cours > 12
	 * </code>
	 *
	 * @see       filterByEdtEmplacementCours()
	 *
	 * @param     mixed $idEdtEmplacementCours The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdEdtEmplacementCours($idEdtEmplacementCours = null, $comparison = null)
	{
		if (is_array($idEdtEmplacementCours)) {
			$useMinMax = false;
			if (isset($idEdtEmplacementCours['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idEdtEmplacementCours['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe)) {
			$useMinMax = false;
			if (isset($idGroupe['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $idGroupe['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idGroupe['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $idGroupe['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $idGroupe, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse)) {
			$useMinMax = false;
			if (isset($idClasse['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $idClasse['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idClasse['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $idClasse['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $idClasse, $comparison);
	}

	/**
	 * Filter the query on the id_aid column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdAid(1234); // WHERE id_aid = 1234
	 * $query->filterByIdAid(array(12, 34)); // WHERE id_aid IN (12, 34)
	 * $query->filterByIdAid(array('min' => 12)); // WHERE id_aid > 12
	 * </code>
	 *
	 * @see       filterByAidDetails()
	 *
	 * @param     mixed $idAid The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdAid($idAid = null, $comparison = null)
	{
		if (is_array($idAid)) {
			$useMinMax = false;
			if (isset($idAid['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $idAid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idAid['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $idAid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $idAid, $comparison);
	}

	/**
	 * Filter the query on the id_s_incidents column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdSIncidents(1234); // WHERE id_s_incidents = 1234
	 * $query->filterByIdSIncidents(array(12, 34)); // WHERE id_s_incidents IN (12, 34)
	 * $query->filterByIdSIncidents(array('min' => 12)); // WHERE id_s_incidents > 12
	 * </code>
	 *
	 * @param     mixed $idSIncidents The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdSIncidents($idSIncidents = null, $comparison = null)
	{
		if (is_array($idSIncidents)) {
			$useMinMax = false;
			if (isset($idSIncidents['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_S_INCIDENTS, $idSIncidents['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idSIncidents['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_S_INCIDENTS, $idSIncidents['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_S_INCIDENTS, $idSIncidents, $comparison);
	}

	/**
	 * Filter the query on the id_lieu column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIdLieu(1234); // WHERE id_lieu = 1234
	 * $query->filterByIdLieu(array(12, 34)); // WHERE id_lieu IN (12, 34)
	 * $query->filterByIdLieu(array('min' => 12)); // WHERE id_lieu > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveLieu()
	 *
	 * @param     mixed $idLieu The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByIdLieu($idLieu = null, $comparison = null)
	{
		if (is_array($idLieu)) {
			$useMinMax = false;
			if (isset($idLieu['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_LIEU, $idLieu['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idLieu['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::ID_LIEU, $idLieu['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::ID_LIEU, $idLieu, $comparison);
	}

	/**
	 * Filter the query on the deleted_by column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDeletedBy('fooValue');   // WHERE deleted_by = 'fooValue'
	 * $query->filterByDeletedBy('%fooValue%'); // WHERE deleted_by LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $deletedBy The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByDeletedBy($deletedBy = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($deletedBy)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $deletedBy)) {
				$deletedBy = str_replace('*', '%', $deletedBy);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::DELETED_BY, $deletedBy, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::CREATED_AT, $createdAt, $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::UPDATED_AT, $updatedAt, $comparison);
	}

	/**
	 * Filter the query on the deleted_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDeletedAt('2011-03-14'); // WHERE deleted_at = '2011-03-14'
	 * $query->filterByDeletedAt('now'); // WHERE deleted_at = '2011-03-14'
	 * $query->filterByDeletedAt(array('max' => 'yesterday')); // WHERE deleted_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $deletedAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByDeletedAt($deletedAt = null, $comparison = null)
	{
		if (is_array($deletedAt)) {
			$useMinMax = false;
			if (isset($deletedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::DELETED_AT, $deletedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($deletedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::DELETED_AT, $deletedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::DELETED_AT, $deletedAt, $comparison);
	}

	/**
	 * Filter the query on the version column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByVersion(1234); // WHERE version = 1234
	 * $query->filterByVersion(array(12, 34)); // WHERE version IN (12, 34)
	 * $query->filterByVersion(array('min' => 12)); // WHERE version > 12
	 * </code>
	 *
	 * @param     mixed $version The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByVersion($version = null, $comparison = null)
	{
		if (is_array($version)) {
			$useMinMax = false;
			if (isset($version['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION, $version['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($version['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION, $version['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION, $version, $comparison);
	}

	/**
	 * Filter the query on the version_created_at column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByVersionCreatedAt('2011-03-14'); // WHERE version_created_at = '2011-03-14'
	 * $query->filterByVersionCreatedAt('now'); // WHERE version_created_at = '2011-03-14'
	 * $query->filterByVersionCreatedAt(array('max' => 'yesterday')); // WHERE version_created_at > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $versionCreatedAt The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
	{
		if (is_array($versionCreatedAt)) {
			$useMinMax = false;
			if (isset($versionCreatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($versionCreatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
	}

	/**
	 * Filter the query on the version_created_by column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByVersionCreatedBy('fooValue');   // WHERE version_created_by = 'fooValue'
	 * $query->filterByVersionCreatedBy('%fooValue%'); // WHERE version_created_by LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $versionCreatedBy The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByVersionCreatedBy($versionCreatedBy = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($versionCreatedBy)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $versionCreatedBy)) {
				$versionCreatedBy = str_replace('*', '%', $versionCreatedBy);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
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
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve|PropelCollection $eleve The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		if ($eleve instanceof Eleve) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleve->getId(), $comparison);
		} elseif ($eleve instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleve->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useEleveQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Filter the query by a related EdtCreneau object
	 *
	 * @param     EdtCreneau|PropelCollection $edtCreneau The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEdtCreneau($edtCreneau, $comparison = null)
	{
		if ($edtCreneau instanceof EdtCreneau) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $edtCreneau->getIdDefiniePeriode(), $comparison);
		} elseif ($edtCreneau instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $edtCreneau->toKeyValue('PrimaryKey', 'IdDefiniePeriode'), $comparison);
		} else {
			throw new PropelException('filterByEdtCreneau() only accepts arguments of type EdtCreneau or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the EdtCreneau relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEdtCreneau($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtCreneau');

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
			$this->addJoinObject($join, 'EdtCreneau');
		}

		return $this;
	}

	/**
	 * Use the EdtCreneau relation EdtCreneau object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EdtCreneauQuery A secondary query class using the current class as primary query
	 */
	public function useEdtCreneauQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtCreneau($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtCreneau', 'EdtCreneauQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours|PropelCollection $edtEmplacementCours The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		if ($edtEmplacementCours instanceof EdtEmplacementCours) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $edtEmplacementCours->getIdCours(), $comparison);
		} elseif ($edtEmplacementCours instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $edtEmplacementCours->toKeyValue('PrimaryKey', 'IdCours'), $comparison);
		} else {
			throw new PropelException('filterByEdtEmplacementCours() only accepts arguments of type EdtEmplacementCours or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EdtEmplacementCours');

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
	public function useEdtEmplacementCoursQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtEmplacementCours($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtEmplacementCours', 'EdtEmplacementCoursQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe|PropelCollection $groupe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		if ($groupe instanceof Groupe) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $groupe->getId(), $comparison);
		} elseif ($groupe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $groupe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useGroupeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		if ($classe instanceof Classe) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $classe->getId(), $comparison);
		} elseif ($classe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $classe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useClasseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails|PropelCollection $aidDetails The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = null)
	{
		if ($aidDetails instanceof AidDetails) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $aidDetails->getId(), $comparison);
		} elseif ($aidDetails instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $aidDetails->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAidDetails() only accepts arguments of type AidDetails or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinAidDetails($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidDetails');

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
			$this->addJoinObject($join, 'AidDetails');
		}

		return $this;
	}

	/**
	 * Use the AidDetails relation AidDetails object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery A secondary query class using the current class as primary query
	 */
	public function useAidDetailsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAidDetails($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidDetails', 'AidDetailsQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveLieu object
	 *
	 * @param     AbsenceEleveLieu|PropelCollection $absenceEleveLieu The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveLieu($absenceEleveLieu, $comparison = null)
	{
		if ($absenceEleveLieu instanceof AbsenceEleveLieu) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_LIEU, $absenceEleveLieu->getId(), $comparison);
		} elseif ($absenceEleveLieu instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID_LIEU, $absenceEleveLieu->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveLieu() only accepts arguments of type AbsenceEleveLieu or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveLieu relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveLieu($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveLieu');

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
			$this->addJoinObject($join, 'AbsenceEleveLieu');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveLieu relation AbsenceEleveLieu object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveLieuQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveLieuQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveLieu($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveLieu', 'AbsenceEleveLieuQuery');
	}

	/**
	 * Filter the query by a related JTraitementSaisieEleve object
	 *
	 * @param     JTraitementSaisieEleve $jTraitementSaisieEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByJTraitementSaisieEleve($jTraitementSaisieEleve, $comparison = null)
	{
		if ($jTraitementSaisieEleve instanceof JTraitementSaisieEleve) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID, $jTraitementSaisieEleve->getASaisieId(), $comparison);
		} elseif ($jTraitementSaisieEleve instanceof PropelCollection) {
			return $this
				->useJTraitementSaisieEleveQuery()
				->filterByPrimaryKeys($jTraitementSaisieEleve->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJTraitementSaisieEleve() only accepts arguments of type JTraitementSaisieEleve or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JTraitementSaisieEleve relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinJTraitementSaisieEleve($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JTraitementSaisieEleve');

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
			$this->addJoinObject($join, 'JTraitementSaisieEleve');
		}

		return $this;
	}

	/**
	 * Use the JTraitementSaisieEleve relation JTraitementSaisieEleve object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementSaisieEleveQuery A secondary query class using the current class as primary query
	 */
	public function useJTraitementSaisieEleveQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJTraitementSaisieEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JTraitementSaisieEleve', 'JTraitementSaisieEleveQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisieVersion object
	 *
	 * @param     AbsenceEleveSaisieVersion $absenceEleveSaisieVersion  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisieVersion($absenceEleveSaisieVersion, $comparison = null)
	{
		if ($absenceEleveSaisieVersion instanceof AbsenceEleveSaisieVersion) {
			return $this
				->addUsingAlias(AbsenceEleveSaisiePeer::ID, $absenceEleveSaisieVersion->getId(), $comparison);
		} elseif ($absenceEleveSaisieVersion instanceof PropelCollection) {
			return $this
				->useAbsenceEleveSaisieVersionQuery()
				->filterByPrimaryKeys($absenceEleveSaisieVersion->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAbsenceEleveSaisieVersion() only accepts arguments of type AbsenceEleveSaisieVersion or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisieVersion relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisieVersion($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisieVersion');

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
			$this->addJoinObject($join, 'AbsenceEleveSaisieVersion');
		}

		return $this;
	}

	/**
	 * Use the AbsenceEleveSaisieVersion relation AbsenceEleveSaisieVersion object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieVersionQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveSaisieVersionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisieVersion($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisieVersion', 'AbsenceEleveSaisieVersionQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 * using the j_traitements_saisies table as cross reference
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJTraitementSaisieEleveQuery()
			->filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie Object to remove from the list of results
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveSaisie = null)
	{
		if ($absenceEleveSaisie) {
			$this->addUsingAlias(AbsenceEleveSaisiePeer::ID, $absenceEleveSaisie->getId(), Criteria::NOT_EQUAL);
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
		// soft_delete behavior
		if (AbsenceEleveSaisieQuery::isSoftDeleteEnabled() && $this->localSoftDelete) {
			$this->addUsingAlias(AbsenceEleveSaisiePeer::DELETED_AT, null, Criteria::ISNULL);
		} else {
			AbsenceEleveSaisiePeer::enableSoftDelete();
		}

		return $this->preSelect($con);
	}

	/**
	 * Code to execute before every DELETE statement
	 *
	 * @param     PropelPDO $con The connection object used by the query
	 */
	protected function basePreDelete(PropelPDO $con)
	{
		// soft_delete behavior
		if (AbsenceEleveSaisieQuery::isSoftDeleteEnabled() && $this->localSoftDelete) {
			return $this->softDelete($con);
		} else {
			return $this->hasWhereClause() ? $this->forceDelete($con) : $this->forceDeleteAll($con);
		}

		return $this->preDelete($con);
	}

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveSaisiePeer::CREATED_AT);
	}

	// soft_delete behavior
	
	/**
	 * Temporarily disable the filter on deleted rows
	 * Valid only for the current query
	 *
	 * @see AbsenceEleveSaisieQuery::disableSoftDelete() to disable the filter for more than one query
	 *
	 * @return AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function includeDeleted()
	{
		$this->localSoftDelete = false;
		return $this;
	}
	
	/**
	 * Soft delete the selected rows
	 *
	 * @param			PropelPDO $con an optional connection object
	 *
	 * @return		int Number of updated rows
	 */
	public function softDelete(PropelPDO $con = null)
	{
		return $this->update(array('DeletedAt' => time()), $con);
	}
	
	/**
	 * Bypass the soft_delete behavior and force a hard delete of the selected rows
	 *
	 * @param			PropelPDO $con an optional connection object
	 *
	 * @return		int Number of deleted rows
	 */
	public function forceDelete(PropelPDO $con = null)
	{
		return AbsenceEleveSaisiePeer::doForceDelete($this, $con);
	}
	
	/**
	 * Bypass the soft_delete behavior and force a hard delete of all the rows
	 *
	 * @param			PropelPDO $con an optional connection object
	 *
	 * @return		int Number of deleted rows
	 */
	public function forceDeleteAll(PropelPDO $con = null)
	{
		return AbsenceEleveSaisiePeer::doForceDeleteAll($con);}
	
	/**
	 * Undelete selected rows
	 *
	 * @param			PropelPDO $con an optional connection object
	 *
	 * @return		int The number of rows affected by this update and any referring fk objects' save() operations.
	 */
	public function unDelete(PropelPDO $con = null)
	{
		return $this->update(array('DeletedAt' => null), $con);
	}
	
	/**
	 * Enable the soft_delete behavior for this model
	 */
	public static function enableSoftDelete()
	{
		self::$softDelete = true;
	}
	
	/**
	 * Disable the soft_delete behavior for this model
	 */
	public static function disableSoftDelete()
	{
		self::$softDelete = false;
	}
	
	/**
	 * Check the soft_delete behavior for this model
	 *
	 * @return boolean true if the soft_delete behavior is enabled
	 */
	public static function isSoftDeleteEnabled()
	{
		return self::$softDelete;
	}

} // BaseAbsenceEleveSaisieQuery