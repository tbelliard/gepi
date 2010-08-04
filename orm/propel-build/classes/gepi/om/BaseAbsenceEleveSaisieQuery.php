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
 * @method     AbsenceEleveSaisieQuery orderByModifieParUtilisateurId($order = Criteria::ASC) Order by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveSaisieQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveSaisieQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
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
 * @method     AbsenceEleveSaisieQuery groupByModifieParUtilisateurId() Group by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveSaisieQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveSaisieQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     AbsenceEleveSaisieQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveSaisieQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveSaisieQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveSaisieQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveSaisieQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveSaisieQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the Eleve relation
 * @method     AbsenceEleveSaisieQuery rightJoinEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Eleve relation
 * @method     AbsenceEleveSaisieQuery innerJoinEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the Eleve relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEdtCreneau($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtCreneau relation
 * @method     AbsenceEleveSaisieQuery rightJoinEdtCreneau($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtCreneau relation
 * @method     AbsenceEleveSaisieQuery innerJoinEdtCreneau($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtCreneau relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinEdtEmplacementCours($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AbsenceEleveSaisieQuery rightJoinEdtEmplacementCours($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AbsenceEleveSaisieQuery innerJoinEdtEmplacementCours($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinGroupe($relationAlias = '') Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     AbsenceEleveSaisieQuery rightJoinGroupe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     AbsenceEleveSaisieQuery innerJoinGroupe($relationAlias = '') Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinClasse($relationAlias = '') Adds a LEFT JOIN clause to the query using the Classe relation
 * @method     AbsenceEleveSaisieQuery rightJoinClasse($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Classe relation
 * @method     AbsenceEleveSaisieQuery innerJoinClasse($relationAlias = '') Adds a INNER JOIN clause to the query using the Classe relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinAidDetails($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidDetails relation
 * @method     AbsenceEleveSaisieQuery rightJoinAidDetails($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidDetails relation
 * @method     AbsenceEleveSaisieQuery innerJoinAidDetails($relationAlias = '') Adds a INNER JOIN clause to the query using the AidDetails relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinModifieParUtilisateur($relationAlias = '') Adds a LEFT JOIN clause to the query using the ModifieParUtilisateur relation
 * @method     AbsenceEleveSaisieQuery rightJoinModifieParUtilisateur($relationAlias = '') Adds a RIGHT JOIN clause to the query using the ModifieParUtilisateur relation
 * @method     AbsenceEleveSaisieQuery innerJoinModifieParUtilisateur($relationAlias = '') Adds a INNER JOIN clause to the query using the ModifieParUtilisateur relation
 *
 * @method     AbsenceEleveSaisieQuery leftJoinJTraitementSaisieEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveSaisieQuery rightJoinJTraitementSaisieEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveSaisieQuery innerJoinJTraitementSaisieEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the JTraitementSaisieEleve relation
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
 * @method     AbsenceEleveSaisie findOneByModifieParUtilisateurId(string $modifie_par_utilisateur_id) Return the first AbsenceEleveSaisie filtered by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveSaisie findOneByCreatedAt(string $created_at) Return the first AbsenceEleveSaisie filtered by the created_at column
 * @method     AbsenceEleveSaisie findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveSaisie filtered by the updated_at column
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
 * @method     array findByModifieParUtilisateurId(string $modifie_par_utilisateur_id) Return AbsenceEleveSaisie objects filtered by the modifie_par_utilisateur_id column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveSaisie objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveSaisie objects filtered by the updated_at column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisieQuery extends ModelCriteria
{

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
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    AbsenceEleveSaisie|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveSaisiePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $utilisateurId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     int|array $eleveId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
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
	 * @param     string|array $debutAbs The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string|array $finAbs The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idEdtCreneau The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idEdtEmplacementCours The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idGroupe The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idClasse The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idAid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     int|array $idSIncidents The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * Filter the query on the modifie_par_utilisateur_id column
	 * 
	 * @param     string $modifieParUtilisateurId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByModifieParUtilisateurId($modifieParUtilisateurId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($modifieParUtilisateurId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $modifieParUtilisateurId)) {
				$modifieParUtilisateurId = str_replace('*', '%', $modifieParUtilisateurId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, $modifieParUtilisateurId, $comparison);
	}

	/**
	 * Filter the query on the created_at column
	 * 
	 * @param     string|array $createdAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * @param     string|array $updatedAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useUtilisateurProfessionnelQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinUtilisateurProfessionnel($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'UtilisateurProfessionnel', 'UtilisateurProfessionnelQuery');
	}

	/**
	 * Filter the query by a related Eleve object
	 *
	 * @param     Eleve $eleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ELEVE_ID, $eleve->getIdEleve(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Eleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEleve($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useEleveQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Eleve', 'EleveQuery');
	}

	/**
	 * Filter the query by a related EdtCreneau object
	 *
	 * @param     EdtCreneau $edtCreneau  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEdtCreneau($edtCreneau, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $edtCreneau->getIdDefiniePeriode(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtCreneau relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEdtCreneau($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useEdtCreneauQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtCreneau($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtCreneau', 'EdtCreneauQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $edtEmplacementCours->getIdCours(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinEdtEmplacementCours($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useEdtEmplacementCoursQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinEdtEmplacementCours($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EdtEmplacementCours', 'EdtEmplacementCoursQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useGroupeQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related Classe object
	 *
	 * @param     Classe $classe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByClasse($classe, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID_CLASSE, $classe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Classe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinClasse($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useClasseQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Classe', 'ClasseQuery');
	}

	/**
	 * Filter the query by a related AidDetails object
	 *
	 * @param     AidDetails $aidDetails  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID_AID, $aidDetails->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidDetails relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinAidDetails($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
	public function useAidDetailsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAidDetails($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidDetails', 'AidDetailsQuery');
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function filterByModifieParUtilisateur($utilisateurProfessionnel, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ModifieParUtilisateur relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinModifieParUtilisateur($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ModifieParUtilisateur');
		
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
			$this->addJoinObject($join, 'ModifieParUtilisateur');
		}
		
		return $this;
	}

	/**
	 * Use the ModifieParUtilisateur relation UtilisateurProfessionnel object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    UtilisateurProfessionnelQuery A secondary query class using the current class as primary query
	 */
	public function useModifieParUtilisateurQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinModifieParUtilisateur($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ModifieParUtilisateur', 'UtilisateurProfessionnelQuery');
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
		return $this
			->addUsingAlias(AbsenceEleveSaisiePeer::ID, $jTraitementSaisieEleve->getASaisieId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JTraitementSaisieEleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
	public function joinJTraitementSaisieEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function useJTraitementSaisieEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJTraitementSaisieEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JTraitementSaisieEleve', 'JTraitementSaisieEleveQuery');
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

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
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
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveSaisiePeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveSaisiePeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceEleveSaisieQuery The current query, for fuid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveSaisiePeer::CREATED_AT);
	}

} // BaseAbsenceEleveSaisieQuery
