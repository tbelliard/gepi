<?php


/**
 * Base class that represents a query for the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @method     AbsenceEleveTraitementQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveTraitementQuery orderByUtilisateurId($order = Criteria::ASC) Order by the utilisateur_id column
 * @method     AbsenceEleveTraitementQuery orderByATypeId($order = Criteria::ASC) Order by the a_type_id column
 * @method     AbsenceEleveTraitementQuery orderByAMotifId($order = Criteria::ASC) Order by the a_motif_id column
 * @method     AbsenceEleveTraitementQuery orderByAJustificationId($order = Criteria::ASC) Order by the a_justification_id column
 * @method     AbsenceEleveTraitementQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveTraitementQuery orderByModifieParUtilisateurId($order = Criteria::ASC) Order by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveTraitementQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveTraitementQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     AbsenceEleveTraitementQuery orderByDeletedAt($order = Criteria::ASC) Order by the deleted_at column
 *
 * @method     AbsenceEleveTraitementQuery groupById() Group by the id column
 * @method     AbsenceEleveTraitementQuery groupByUtilisateurId() Group by the utilisateur_id column
 * @method     AbsenceEleveTraitementQuery groupByATypeId() Group by the a_type_id column
 * @method     AbsenceEleveTraitementQuery groupByAMotifId() Group by the a_motif_id column
 * @method     AbsenceEleveTraitementQuery groupByAJustificationId() Group by the a_justification_id column
 * @method     AbsenceEleveTraitementQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveTraitementQuery groupByModifieParUtilisateurId() Group by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveTraitementQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveTraitementQuery groupByUpdatedAt() Group by the updated_at column
 * @method     AbsenceEleveTraitementQuery groupByDeletedAt() Group by the deleted_at column
 *
 * @method     AbsenceEleveTraitementQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveTraitementQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveTraitementQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveTraitementQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveTraitementQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveTraitementQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinAbsenceEleveType($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveTraitementQuery rightJoinAbsenceEleveType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveType relation
 * @method     AbsenceEleveTraitementQuery innerJoinAbsenceEleveType($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveType relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinAbsenceEleveMotif($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveMotif relation
 * @method     AbsenceEleveTraitementQuery rightJoinAbsenceEleveMotif($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveMotif relation
 * @method     AbsenceEleveTraitementQuery innerJoinAbsenceEleveMotif($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveMotif relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinAbsenceEleveJustification($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveJustification relation
 * @method     AbsenceEleveTraitementQuery rightJoinAbsenceEleveJustification($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveJustification relation
 * @method     AbsenceEleveTraitementQuery innerJoinAbsenceEleveJustification($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveJustification relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinModifieParUtilisateur($relationAlias = null) Adds a LEFT JOIN clause to the query using the ModifieParUtilisateur relation
 * @method     AbsenceEleveTraitementQuery rightJoinModifieParUtilisateur($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ModifieParUtilisateur relation
 * @method     AbsenceEleveTraitementQuery innerJoinModifieParUtilisateur($relationAlias = null) Adds a INNER JOIN clause to the query using the ModifieParUtilisateur relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinJTraitementSaisieEleve($relationAlias = null) Adds a LEFT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveTraitementQuery rightJoinJTraitementSaisieEleve($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JTraitementSaisieEleve relation
 * @method     AbsenceEleveTraitementQuery innerJoinJTraitementSaisieEleve($relationAlias = null) Adds a INNER JOIN clause to the query using the JTraitementSaisieEleve relation
 *
 * @method     AbsenceEleveTraitementQuery leftJoinAbsenceEleveNotification($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     AbsenceEleveTraitementQuery rightJoinAbsenceEleveNotification($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveNotification relation
 * @method     AbsenceEleveTraitementQuery innerJoinAbsenceEleveNotification($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveNotification relation
 *
 * @method     AbsenceEleveTraitement findOne(PropelPDO $con = null) Return the first AbsenceEleveTraitement matching the query
 * @method     AbsenceEleveTraitement findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveTraitement matching the query, or a new AbsenceEleveTraitement object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveTraitement findOneById(int $id) Return the first AbsenceEleveTraitement filtered by the id column
 * @method     AbsenceEleveTraitement findOneByUtilisateurId(string $utilisateur_id) Return the first AbsenceEleveTraitement filtered by the utilisateur_id column
 * @method     AbsenceEleveTraitement findOneByATypeId(int $a_type_id) Return the first AbsenceEleveTraitement filtered by the a_type_id column
 * @method     AbsenceEleveTraitement findOneByAMotifId(int $a_motif_id) Return the first AbsenceEleveTraitement filtered by the a_motif_id column
 * @method     AbsenceEleveTraitement findOneByAJustificationId(int $a_justification_id) Return the first AbsenceEleveTraitement filtered by the a_justification_id column
 * @method     AbsenceEleveTraitement findOneByCommentaire(string $commentaire) Return the first AbsenceEleveTraitement filtered by the commentaire column
 * @method     AbsenceEleveTraitement findOneByModifieParUtilisateurId(string $modifie_par_utilisateur_id) Return the first AbsenceEleveTraitement filtered by the modifie_par_utilisateur_id column
 * @method     AbsenceEleveTraitement findOneByCreatedAt(string $created_at) Return the first AbsenceEleveTraitement filtered by the created_at column
 * @method     AbsenceEleveTraitement findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveTraitement filtered by the updated_at column
 * @method     AbsenceEleveTraitement findOneByDeletedAt(string $deleted_at) Return the first AbsenceEleveTraitement filtered by the deleted_at column
 *
 * @method     array findById(int $id) Return AbsenceEleveTraitement objects filtered by the id column
 * @method     array findByUtilisateurId(string $utilisateur_id) Return AbsenceEleveTraitement objects filtered by the utilisateur_id column
 * @method     array findByATypeId(int $a_type_id) Return AbsenceEleveTraitement objects filtered by the a_type_id column
 * @method     array findByAMotifId(int $a_motif_id) Return AbsenceEleveTraitement objects filtered by the a_motif_id column
 * @method     array findByAJustificationId(int $a_justification_id) Return AbsenceEleveTraitement objects filtered by the a_justification_id column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveTraitement objects filtered by the commentaire column
 * @method     array findByModifieParUtilisateurId(string $modifie_par_utilisateur_id) Return AbsenceEleveTraitement objects filtered by the modifie_par_utilisateur_id column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveTraitement objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveTraitement objects filtered by the updated_at column
 * @method     array findByDeletedAt(string $deleted_at) Return AbsenceEleveTraitement objects filtered by the deleted_at column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTraitementQuery extends ModelCriteria
{

	// soft_delete behavior
	protected static $softDelete = true;
	protected $localSoftDelete = true;

	/**
	 * Initializes internal state of BaseAbsenceEleveTraitementQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveTraitement', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveTraitementQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveTraitementQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveTraitementQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveTraitementQuery();
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
	 * @return    AbsenceEleveTraitement|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveTraitementPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::ID, $keys, Criteria::IN);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::ID, $id, $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $utilisateurId, $comparison);
	}

	/**
	 * Filter the query on the a_type_id column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByATypeId(1234); // WHERE a_type_id = 1234
	 * $query->filterByATypeId(array(12, 34)); // WHERE a_type_id IN (12, 34)
	 * $query->filterByATypeId(array('min' => 12)); // WHERE a_type_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveType()
	 *
	 * @param     mixed $aTypeId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByATypeId($aTypeId = null, $comparison = null)
	{
		if (is_array($aTypeId)) {
			$useMinMax = false;
			if (isset($aTypeId['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_TYPE_ID, $aTypeId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($aTypeId['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_TYPE_ID, $aTypeId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::A_TYPE_ID, $aTypeId, $comparison);
	}

	/**
	 * Filter the query on the a_motif_id column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByAMotifId(1234); // WHERE a_motif_id = 1234
	 * $query->filterByAMotifId(array(12, 34)); // WHERE a_motif_id IN (12, 34)
	 * $query->filterByAMotifId(array('min' => 12)); // WHERE a_motif_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveMotif()
	 *
	 * @param     mixed $aMotifId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAMotifId($aMotifId = null, $comparison = null)
	{
		if (is_array($aMotifId)) {
			$useMinMax = false;
			if (isset($aMotifId['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_MOTIF_ID, $aMotifId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($aMotifId['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_MOTIF_ID, $aMotifId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::A_MOTIF_ID, $aMotifId, $comparison);
	}

	/**
	 * Filter the query on the a_justification_id column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByAJustificationId(1234); // WHERE a_justification_id = 1234
	 * $query->filterByAJustificationId(array(12, 34)); // WHERE a_justification_id IN (12, 34)
	 * $query->filterByAJustificationId(array('min' => 12)); // WHERE a_justification_id > 12
	 * </code>
	 *
	 * @see       filterByAbsenceEleveJustification()
	 *
	 * @param     mixed $aJustificationId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAJustificationId($aJustificationId = null, $comparison = null)
	{
		if (is_array($aJustificationId)) {
			$useMinMax = false;
			if (isset($aJustificationId['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $aJustificationId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($aJustificationId['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $aJustificationId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $aJustificationId, $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::COMMENTAIRE, $commentaire, $comparison);
	}

	/**
	 * Filter the query on the modifie_par_utilisateur_id column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByModifieParUtilisateurId('fooValue');   // WHERE modifie_par_utilisateur_id = 'fooValue'
	 * $query->filterByModifieParUtilisateurId('%fooValue%'); // WHERE modifie_par_utilisateur_id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $modifieParUtilisateurId The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, $modifieParUtilisateurId, $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = null)
	{
		if (is_array($createdAt)) {
			$useMinMax = false;
			if (isset($createdAt['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($createdAt['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::CREATED_AT, $createdAt, $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = null)
	{
		if (is_array($updatedAt)) {
			$useMinMax = false;
			if (isset($updatedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($updatedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::UPDATED_AT, $updatedAt, $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByDeletedAt($deletedAt = null, $comparison = null)
	{
		if (is_array($deletedAt)) {
			$useMinMax = false;
			if (isset($deletedAt['min'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::DELETED_AT, $deletedAt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($deletedAt['max'])) {
				$this->addUsingAlias(AbsenceEleveTraitementPeer::DELETED_AT, $deletedAt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::DELETED_AT, $deletedAt, $comparison);
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
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
	 * Filter the query by a related AbsenceEleveType object
	 *
	 * @param     AbsenceEleveType|PropelCollection $absenceEleveType The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveType($absenceEleveType, $comparison = null)
	{
		if ($absenceEleveType instanceof AbsenceEleveType) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_TYPE_ID, $absenceEleveType->getId(), $comparison);
		} elseif ($absenceEleveType instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_TYPE_ID, $absenceEleveType->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveType() only accepts arguments of type AbsenceEleveType or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveType relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveType($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveType');
		
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
			$this->addJoinObject($join, 'AbsenceEleveType');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveType relation AbsenceEleveType object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveType($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveType', 'AbsenceEleveTypeQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveMotif object
	 *
	 * @param     AbsenceEleveMotif|PropelCollection $absenceEleveMotif The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveMotif($absenceEleveMotif, $comparison = null)
	{
		if ($absenceEleveMotif instanceof AbsenceEleveMotif) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_MOTIF_ID, $absenceEleveMotif->getId(), $comparison);
		} elseif ($absenceEleveMotif instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_MOTIF_ID, $absenceEleveMotif->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveMotif() only accepts arguments of type AbsenceEleveMotif or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveMotif relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveMotif($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveMotif');
		
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
			$this->addJoinObject($join, 'AbsenceEleveMotif');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveMotif relation AbsenceEleveMotif object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveMotifQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveMotifQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveMotif($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveMotif', 'AbsenceEleveMotifQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveJustification object
	 *
	 * @param     AbsenceEleveJustification|PropelCollection $absenceEleveJustification The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveJustification($absenceEleveJustification, $comparison = null)
	{
		if ($absenceEleveJustification instanceof AbsenceEleveJustification) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $absenceEleveJustification->getId(), $comparison);
		} elseif ($absenceEleveJustification instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $absenceEleveJustification->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByAbsenceEleveJustification() only accepts arguments of type AbsenceEleveJustification or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveJustification relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveJustification($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveJustification');
		
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
			$this->addJoinObject($join, 'AbsenceEleveJustification');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveJustification relation AbsenceEleveJustification object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveJustificationQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveJustificationQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveJustification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveJustification', 'AbsenceEleveJustificationQuery');
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByModifieParUtilisateur($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
		} else {
			throw new PropelException('filterByModifieParUtilisateur() only accepts arguments of type UtilisateurProfessionnel or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the ModifieParUtilisateur relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function joinModifieParUtilisateur($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useModifieParUtilisateurQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByJTraitementSaisieEleve($jTraitementSaisieEleve, $comparison = null)
	{
		if ($jTraitementSaisieEleve instanceof JTraitementSaisieEleve) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::ID, $jTraitementSaisieEleve->getATraitementId(), $comparison);
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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
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
	 * Filter the query by a related AbsenceEleveNotification object
	 *
	 * @param     AbsenceEleveNotification $absenceEleveNotification  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveNotification($absenceEleveNotification, $comparison = null)
	{
		if ($absenceEleveNotification instanceof AbsenceEleveNotification) {
			return $this
				->addUsingAlias(AbsenceEleveTraitementPeer::ID, $absenceEleveNotification->getATraitementId(), $comparison);
		} elseif ($absenceEleveNotification instanceof PropelCollection) {
			return $this
				->useAbsenceEleveNotificationQuery()
					->filterByPrimaryKeys($absenceEleveNotification->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByAbsenceEleveNotification() only accepts arguments of type AbsenceEleveNotification or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveNotification relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveNotification($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveNotification');
		
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
			$this->addJoinObject($join, 'AbsenceEleveNotification');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveNotification relation AbsenceEleveNotification object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveNotificationQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveNotificationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveNotification($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveNotification', 'AbsenceEleveNotificationQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 * using the j_traitements_saisies table as cross reference
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJTraitementSaisieEleveQuery()
				->filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison)
			->endUse();
	}
	
	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement Object to remove from the list of results
	 *
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveTraitement = null)
	{
		if ($absenceEleveTraitement) {
			$this->addUsingAlias(AbsenceEleveTraitementPeer::ID, $absenceEleveTraitement->getId(), Criteria::NOT_EQUAL);
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
		if (AbsenceEleveTraitementQuery::isSoftDeleteEnabled() && $this->localSoftDelete) {
			$this->addUsingAlias(AbsenceEleveTraitementPeer::DELETED_AT, null, Criteria::ISNULL);
		} else {
			AbsenceEleveTraitementPeer::enableSoftDelete();
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
		if (AbsenceEleveTraitementQuery::isSoftDeleteEnabled() && $this->localSoftDelete) {
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
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveTraitementPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveTraitementPeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveTraitementPeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveTraitementPeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveTraitementPeer::CREATED_AT);
	}

	// soft_delete behavior
	
	/**
	 * Temporarily disable the filter on deleted rows
	 * Valid only for the current query
	 * 
	 * @see AbsenceEleveTraitementQuery::disableSoftDelete() to disable the filter for more than one query
	 *
	 * @return AbsenceEleveTraitementQuery The current query, for fluid interface
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
		return AbsenceEleveTraitementPeer::doForceDelete($this, $con);
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
		return AbsenceEleveTraitementPeer::doForceDeleteAll($con);}
	
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

} // BaseAbsenceEleveTraitementQuery
