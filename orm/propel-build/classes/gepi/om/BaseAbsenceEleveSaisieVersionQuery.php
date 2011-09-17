<?php


/**
 * Base class that represents a query for the 'a_saisies_version' table.
 *
 * 
 *
 * @method     AbsenceEleveSaisieVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveSaisieVersionQuery orderByUtilisateurId($order = Criteria::ASC) Order by the utilisateur_id column
 * @method     AbsenceEleveSaisieVersionQuery orderByEleveId($order = Criteria::ASC) Order by the eleve_id column
 * @method     AbsenceEleveSaisieVersionQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveSaisieVersionQuery orderByDebutAbs($order = Criteria::ASC) Order by the debut_abs column
 * @method     AbsenceEleveSaisieVersionQuery orderByFinAbs($order = Criteria::ASC) Order by the fin_abs column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdEdtCreneau($order = Criteria::ASC) Order by the id_edt_creneau column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdEdtEmplacementCours($order = Criteria::ASC) Order by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdClasse($order = Criteria::ASC) Order by the id_classe column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdAid($order = Criteria::ASC) Order by the id_aid column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdSIncidents($order = Criteria::ASC) Order by the id_s_incidents column
 * @method     AbsenceEleveSaisieVersionQuery orderByIdLieu($order = Criteria::ASC) Order by the id_lieu column
 * @method     AbsenceEleveSaisieVersionQuery orderByDeletedBy($order = Criteria::ASC) Order by the deleted_by column
 * @method     AbsenceEleveSaisieVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveSaisieVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     AbsenceEleveSaisieVersionQuery orderByDeletedAt($order = Criteria::ASC) Order by the deleted_at column
 * @method     AbsenceEleveSaisieVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     AbsenceEleveSaisieVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     AbsenceEleveSaisieVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 *
 * @method     AbsenceEleveSaisieVersionQuery groupById() Group by the id column
 * @method     AbsenceEleveSaisieVersionQuery groupByUtilisateurId() Group by the utilisateur_id column
 * @method     AbsenceEleveSaisieVersionQuery groupByEleveId() Group by the eleve_id column
 * @method     AbsenceEleveSaisieVersionQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveSaisieVersionQuery groupByDebutAbs() Group by the debut_abs column
 * @method     AbsenceEleveSaisieVersionQuery groupByFinAbs() Group by the fin_abs column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdEdtCreneau() Group by the id_edt_creneau column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdEdtEmplacementCours() Group by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdGroupe() Group by the id_groupe column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdClasse() Group by the id_classe column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdAid() Group by the id_aid column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdSIncidents() Group by the id_s_incidents column
 * @method     AbsenceEleveSaisieVersionQuery groupByIdLieu() Group by the id_lieu column
 * @method     AbsenceEleveSaisieVersionQuery groupByDeletedBy() Group by the deleted_by column
 * @method     AbsenceEleveSaisieVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveSaisieVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     AbsenceEleveSaisieVersionQuery groupByDeletedAt() Group by the deleted_at column
 * @method     AbsenceEleveSaisieVersionQuery groupByVersion() Group by the version column
 * @method     AbsenceEleveSaisieVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     AbsenceEleveSaisieVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 *
 * @method     AbsenceEleveSaisieVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveSaisieVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveSaisieVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveSaisieVersionQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AbsenceEleveSaisieVersionQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AbsenceEleveSaisieVersionQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     AbsenceEleveSaisieVersion findOne(PropelPDO $con = null) Return the first AbsenceEleveSaisieVersion matching the query
 * @method     AbsenceEleveSaisieVersion findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveSaisieVersion matching the query, or a new AbsenceEleveSaisieVersion object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveSaisieVersion findOneById(int $id) Return the first AbsenceEleveSaisieVersion filtered by the id column
 * @method     AbsenceEleveSaisieVersion findOneByUtilisateurId(string $utilisateur_id) Return the first AbsenceEleveSaisieVersion filtered by the utilisateur_id column
 * @method     AbsenceEleveSaisieVersion findOneByEleveId(int $eleve_id) Return the first AbsenceEleveSaisieVersion filtered by the eleve_id column
 * @method     AbsenceEleveSaisieVersion findOneByCommentaire(string $commentaire) Return the first AbsenceEleveSaisieVersion filtered by the commentaire column
 * @method     AbsenceEleveSaisieVersion findOneByDebutAbs(string $debut_abs) Return the first AbsenceEleveSaisieVersion filtered by the debut_abs column
 * @method     AbsenceEleveSaisieVersion findOneByFinAbs(string $fin_abs) Return the first AbsenceEleveSaisieVersion filtered by the fin_abs column
 * @method     AbsenceEleveSaisieVersion findOneByIdEdtCreneau(int $id_edt_creneau) Return the first AbsenceEleveSaisieVersion filtered by the id_edt_creneau column
 * @method     AbsenceEleveSaisieVersion findOneByIdEdtEmplacementCours(int $id_edt_emplacement_cours) Return the first AbsenceEleveSaisieVersion filtered by the id_edt_emplacement_cours column
 * @method     AbsenceEleveSaisieVersion findOneByIdGroupe(int $id_groupe) Return the first AbsenceEleveSaisieVersion filtered by the id_groupe column
 * @method     AbsenceEleveSaisieVersion findOneByIdClasse(int $id_classe) Return the first AbsenceEleveSaisieVersion filtered by the id_classe column
 * @method     AbsenceEleveSaisieVersion findOneByIdAid(int $id_aid) Return the first AbsenceEleveSaisieVersion filtered by the id_aid column
 * @method     AbsenceEleveSaisieVersion findOneByIdSIncidents(int $id_s_incidents) Return the first AbsenceEleveSaisieVersion filtered by the id_s_incidents column
 * @method     AbsenceEleveSaisieVersion findOneByIdLieu(int $id_lieu) Return the first AbsenceEleveSaisieVersion filtered by the id_lieu column
 * @method     AbsenceEleveSaisieVersion findOneByDeletedBy(string $deleted_by) Return the first AbsenceEleveSaisieVersion filtered by the deleted_by column
 * @method     AbsenceEleveSaisieVersion findOneByCreatedAt(string $created_at) Return the first AbsenceEleveSaisieVersion filtered by the created_at column
 * @method     AbsenceEleveSaisieVersion findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveSaisieVersion filtered by the updated_at column
 * @method     AbsenceEleveSaisieVersion findOneByDeletedAt(string $deleted_at) Return the first AbsenceEleveSaisieVersion filtered by the deleted_at column
 * @method     AbsenceEleveSaisieVersion findOneByVersion(int $version) Return the first AbsenceEleveSaisieVersion filtered by the version column
 * @method     AbsenceEleveSaisieVersion findOneByVersionCreatedAt(string $version_created_at) Return the first AbsenceEleveSaisieVersion filtered by the version_created_at column
 * @method     AbsenceEleveSaisieVersion findOneByVersionCreatedBy(string $version_created_by) Return the first AbsenceEleveSaisieVersion filtered by the version_created_by column
 *
 * @method     array findById(int $id) Return AbsenceEleveSaisieVersion objects filtered by the id column
 * @method     array findByUtilisateurId(string $utilisateur_id) Return AbsenceEleveSaisieVersion objects filtered by the utilisateur_id column
 * @method     array findByEleveId(int $eleve_id) Return AbsenceEleveSaisieVersion objects filtered by the eleve_id column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveSaisieVersion objects filtered by the commentaire column
 * @method     array findByDebutAbs(string $debut_abs) Return AbsenceEleveSaisieVersion objects filtered by the debut_abs column
 * @method     array findByFinAbs(string $fin_abs) Return AbsenceEleveSaisieVersion objects filtered by the fin_abs column
 * @method     array findByIdEdtCreneau(int $id_edt_creneau) Return AbsenceEleveSaisieVersion objects filtered by the id_edt_creneau column
 * @method     array findByIdEdtEmplacementCours(int $id_edt_emplacement_cours) Return AbsenceEleveSaisieVersion objects filtered by the id_edt_emplacement_cours column
 * @method     array findByIdGroupe(int $id_groupe) Return AbsenceEleveSaisieVersion objects filtered by the id_groupe column
 * @method     array findByIdClasse(int $id_classe) Return AbsenceEleveSaisieVersion objects filtered by the id_classe column
 * @method     array findByIdAid(int $id_aid) Return AbsenceEleveSaisieVersion objects filtered by the id_aid column
 * @method     array findByIdSIncidents(int $id_s_incidents) Return AbsenceEleveSaisieVersion objects filtered by the id_s_incidents column
 * @method     array findByIdLieu(int $id_lieu) Return AbsenceEleveSaisieVersion objects filtered by the id_lieu column
 * @method     array findByDeletedBy(string $deleted_by) Return AbsenceEleveSaisieVersion objects filtered by the deleted_by column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveSaisieVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveSaisieVersion objects filtered by the updated_at column
 * @method     array findByDeletedAt(string $deleted_at) Return AbsenceEleveSaisieVersion objects filtered by the deleted_at column
 * @method     array findByVersion(int $version) Return AbsenceEleveSaisieVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return AbsenceEleveSaisieVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return AbsenceEleveSaisieVersion objects filtered by the version_created_by column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisieVersionQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveSaisieVersionQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveSaisieVersion', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveSaisieVersionQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveSaisieVersionQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveSaisieVersionQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveSaisieVersionQuery();
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
	 * @param     array[$id, $version] $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    AbsenceEleveSaisieVersion|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveSaisieVersionPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID, $key[0], Criteria::EQUAL);
		$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION, $key[1], Criteria::EQUAL);
		
		return $this;
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		if (empty($keys)) {
			return $this->add(null, '1<>1', Criteria::CUSTOM);
		}
		foreach ($keys as $key) {
			$cton0 = $this->getNewCriterion(AbsenceEleveSaisieVersionPeer::ID, $key[0], Criteria::EQUAL);
			$cton1 = $this->getNewCriterion(AbsenceEleveSaisieVersionPeer::VERSION, $key[1], Criteria::EQUAL);
			$cton0->addAnd($cton1);
			$this->addOr($cton0);
		}
		
		return $this;
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
	 * @see       filterByAbsenceEleveSaisie()
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID, $id, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::UTILISATEUR_ID, $utilisateurId, $comparison);
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
	 * @param     mixed $eleveId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByEleveId($eleveId = null, $comparison = null)
	{
		if (is_array($eleveId)) {
			$useMinMax = false;
			if (isset($eleveId['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ELEVE_ID, $eleveId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($eleveId['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ELEVE_ID, $eleveId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ELEVE_ID, $eleveId, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::COMMENTAIRE, $commentaire, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByDebutAbs($debutAbs = null, $comparison = null)
	{
		if (is_array($debutAbs)) {
			$useMinMax = false;
			if (isset($debutAbs['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DEBUT_ABS, $debutAbs['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($debutAbs['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DEBUT_ABS, $debutAbs['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DEBUT_ABS, $debutAbs, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByFinAbs($finAbs = null, $comparison = null)
	{
		if (is_array($finAbs)) {
			$useMinMax = false;
			if (isset($finAbs['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::FIN_ABS, $finAbs['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($finAbs['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::FIN_ABS, $finAbs['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::FIN_ABS, $finAbs, $comparison);
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
	 * @param     mixed $idEdtCreneau The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdEdtCreneau($idEdtCreneau = null, $comparison = null)
	{
		if (is_array($idEdtCreneau)) {
			$useMinMax = false;
			if (isset($idEdtCreneau['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU, $idEdtCreneau['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idEdtCreneau['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU, $idEdtCreneau['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU, $idEdtCreneau, $comparison);
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
	 * @param     mixed $idEdtEmplacementCours The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdEdtEmplacementCours($idEdtEmplacementCours = null, $comparison = null)
	{
		if (is_array($idEdtEmplacementCours)) {
			$useMinMax = false;
			if (isset($idEdtEmplacementCours['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idEdtEmplacementCours['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS, $idEdtEmplacementCours, $comparison);
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
	 * @param     mixed $idGroupe The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe)) {
			$useMinMax = false;
			if (isset($idGroupe['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_GROUPE, $idGroupe['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idGroupe['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_GROUPE, $idGroupe['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_GROUPE, $idGroupe, $comparison);
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
	 * @param     mixed $idClasse The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdClasse($idClasse = null, $comparison = null)
	{
		if (is_array($idClasse)) {
			$useMinMax = false;
			if (isset($idClasse['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_CLASSE, $idClasse['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idClasse['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_CLASSE, $idClasse['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_CLASSE, $idClasse, $comparison);
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
	 * @param     mixed $idAid The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdAid($idAid = null, $comparison = null)
	{
		if (is_array($idAid)) {
			$useMinMax = false;
			if (isset($idAid['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_AID, $idAid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idAid['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_AID, $idAid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_AID, $idAid, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdSIncidents($idSIncidents = null, $comparison = null)
	{
		if (is_array($idSIncidents)) {
			$useMinMax = false;
			if (isset($idSIncidents['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS, $idSIncidents['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idSIncidents['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS, $idSIncidents['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS, $idSIncidents, $comparison);
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
	 * @param     mixed $idLieu The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByIdLieu($idLieu = null, $comparison = null)
	{
		if (is_array($idLieu)) {
			$useMinMax = false;
			if (isset($idLieu['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_LIEU, $idLieu['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idLieu['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_LIEU, $idLieu['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID_LIEU, $idLieu, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DELETED_BY, $deletedBy, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::CREATED_AT, $createdAt, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::UPDATED_AT, $updatedAt, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByDeletedAt($deletedAt = null, $comparison = null)
	{
		if (is_array($deletedAt)) {
			$useMinMax = false;
			if (isset($deletedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DELETED_AT, $deletedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($deletedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DELETED_AT, $deletedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::DELETED_AT, $deletedAt, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByVersion($version = null, $comparison = null)
	{
		if (is_array($version) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION, $version, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
	{
		if (is_array($versionCreatedAt)) {
			$useMinMax = false;
			if (isset($versionCreatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($versionCreatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
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
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie|PropelCollection $absenceEleveSaisie The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		if ($absenceEleveSaisie instanceof AbsenceEleveSaisie) {
			return $this
				->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID, $absenceEleveSaisie->getId(), $comparison);
		} elseif ($absenceEleveSaisie instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveSaisieVersionPeer::ID, $absenceEleveSaisie->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveSaisie() only accepts arguments of type AbsenceEleveSaisie or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveSaisie');
		
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
	public function useAbsenceEleveSaisieQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveSaisieVersion $absenceEleveSaisieVersion Object to remove from the list of results
	 *
	 * @return    AbsenceEleveSaisieVersionQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveSaisieVersion = null)
	{
		if ($absenceEleveSaisieVersion) {
			$this->addCond('pruneCond0', $this->getAliasedColName(AbsenceEleveSaisieVersionPeer::ID), $absenceEleveSaisieVersion->getId(), Criteria::NOT_EQUAL);
			$this->addCond('pruneCond1', $this->getAliasedColName(AbsenceEleveSaisieVersionPeer::VERSION), $absenceEleveSaisieVersion->getVersion(), Criteria::NOT_EQUAL);
			$this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
	  }
	  
		return $this;
	}

} // BaseAbsenceEleveSaisieVersionQuery
