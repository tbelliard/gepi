<?php


/**
 * Base class that represents a query for the 'a_envois' table.
 *
 * Chaque envoi est repertorie ici
 *
 * @method     AbsenceEleveEnvoiQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveEnvoiQuery orderByUtilisateurId($order = Criteria::ASC) Order by the utilisateur_id column
 * @method     AbsenceEleveEnvoiQuery orderByIdTypeEnvoi($order = Criteria::ASC) Order by the id_type_envoi column
 * @method     AbsenceEleveEnvoiQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveEnvoiQuery orderByStatutEnvoi($order = Criteria::ASC) Order by the statut_envoi column
 * @method     AbsenceEleveEnvoiQuery orderByDateEnvoi($order = Criteria::ASC) Order by the date_envoi column
 * @method     AbsenceEleveEnvoiQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     AbsenceEleveEnvoiQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     AbsenceEleveEnvoiQuery groupById() Group by the id column
 * @method     AbsenceEleveEnvoiQuery groupByUtilisateurId() Group by the utilisateur_id column
 * @method     AbsenceEleveEnvoiQuery groupByIdTypeEnvoi() Group by the id_type_envoi column
 * @method     AbsenceEleveEnvoiQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveEnvoiQuery groupByStatutEnvoi() Group by the statut_envoi column
 * @method     AbsenceEleveEnvoiQuery groupByDateEnvoi() Group by the date_envoi column
 * @method     AbsenceEleveEnvoiQuery groupByCreatedAt() Group by the created_at column
 * @method     AbsenceEleveEnvoiQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     AbsenceEleveEnvoiQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveEnvoiQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveEnvoiQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveEnvoiQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveEnvoiQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     AbsenceEleveEnvoiQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     AbsenceEleveEnvoiQuery leftJoinAbsenceEleveTypeEnvoi($relationAlias = '') Adds a LEFT JOIN clause to the query using the AbsenceEleveTypeEnvoi relation
 * @method     AbsenceEleveEnvoiQuery rightJoinAbsenceEleveTypeEnvoi($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AbsenceEleveTypeEnvoi relation
 * @method     AbsenceEleveEnvoiQuery innerJoinAbsenceEleveTypeEnvoi($relationAlias = '') Adds a INNER JOIN clause to the query using the AbsenceEleveTypeEnvoi relation
 *
 * @method     AbsenceEleveEnvoiQuery leftJoinJTraitementEnvoiEleve($relationAlias = '') Adds a LEFT JOIN clause to the query using the JTraitementEnvoiEleve relation
 * @method     AbsenceEleveEnvoiQuery rightJoinJTraitementEnvoiEleve($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JTraitementEnvoiEleve relation
 * @method     AbsenceEleveEnvoiQuery innerJoinJTraitementEnvoiEleve($relationAlias = '') Adds a INNER JOIN clause to the query using the JTraitementEnvoiEleve relation
 *
 * @method     AbsenceEleveEnvoi findOne(PropelPDO $con = null) Return the first AbsenceEleveEnvoi matching the query
 * @method     AbsenceEleveEnvoi findOneById(int $id) Return the first AbsenceEleveEnvoi filtered by the id column
 * @method     AbsenceEleveEnvoi findOneByUtilisateurId(string $utilisateur_id) Return the first AbsenceEleveEnvoi filtered by the utilisateur_id column
 * @method     AbsenceEleveEnvoi findOneByIdTypeEnvoi(int $id_type_envoi) Return the first AbsenceEleveEnvoi filtered by the id_type_envoi column
 * @method     AbsenceEleveEnvoi findOneByCommentaire(string $commentaire) Return the first AbsenceEleveEnvoi filtered by the commentaire column
 * @method     AbsenceEleveEnvoi findOneByStatutEnvoi(string $statut_envoi) Return the first AbsenceEleveEnvoi filtered by the statut_envoi column
 * @method     AbsenceEleveEnvoi findOneByDateEnvoi(string $date_envoi) Return the first AbsenceEleveEnvoi filtered by the date_envoi column
 * @method     AbsenceEleveEnvoi findOneByCreatedAt(string $created_at) Return the first AbsenceEleveEnvoi filtered by the created_at column
 * @method     AbsenceEleveEnvoi findOneByUpdatedAt(string $updated_at) Return the first AbsenceEleveEnvoi filtered by the updated_at column
 *
 * @method     array findById(int $id) Return AbsenceEleveEnvoi objects filtered by the id column
 * @method     array findByUtilisateurId(string $utilisateur_id) Return AbsenceEleveEnvoi objects filtered by the utilisateur_id column
 * @method     array findByIdTypeEnvoi(int $id_type_envoi) Return AbsenceEleveEnvoi objects filtered by the id_type_envoi column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveEnvoi objects filtered by the commentaire column
 * @method     array findByStatutEnvoi(string $statut_envoi) Return AbsenceEleveEnvoi objects filtered by the statut_envoi column
 * @method     array findByDateEnvoi(string $date_envoi) Return AbsenceEleveEnvoi objects filtered by the date_envoi column
 * @method     array findByCreatedAt(string $created_at) Return AbsenceEleveEnvoi objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return AbsenceEleveEnvoi objects filtered by the updated_at column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveEnvoiQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveEnvoiQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveEnvoi', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveEnvoiQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveEnvoiQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveEnvoiQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveEnvoiQuery();
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
	 * @return    AbsenceEleveEnvoi|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveEnvoiPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $id, Criteria::IN);
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the utilisateur_id column
	 * 
	 * @param     string $utilisateurId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurId($utilisateurId = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($utilisateurId)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $utilisateurId, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $utilisateurId)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, str_replace('*', '%', $utilisateurId), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $utilisateurId, $comparison);
		}
	}

	/**
	 * Filter the query on the id_type_envoi column
	 * 
	 * @param     int|array $idTypeEnvoi The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByIdTypeEnvoi($idTypeEnvoi = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idTypeEnvoi)) {
			if (array_values($idTypeEnvoi) === $idTypeEnvoi) {
				return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID_TYPE_ENVOI, $idTypeEnvoi, Criteria::IN);
			} else {
				if (isset($idTypeEnvoi['min'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::ID_TYPE_ENVOI, $idTypeEnvoi['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($idTypeEnvoi['max'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::ID_TYPE_ENVOI, $idTypeEnvoi['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::ID_TYPE_ENVOI, $idTypeEnvoi, $comparison);
		}
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByCommentaire($commentaire = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($commentaire)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::COMMENTAIRE, $commentaire, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $commentaire)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::COMMENTAIRE, str_replace('*', '%', $commentaire), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::COMMENTAIRE, $commentaire, $comparison);
		}
	}

	/**
	 * Filter the query on the statut_envoi column
	 * 
	 * @param     string $statutEnvoi The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByStatutEnvoi($statutEnvoi = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($statutEnvoi)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::STATUT_ENVOI, $statutEnvoi, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $statutEnvoi)) {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::STATUT_ENVOI, str_replace('*', '%', $statutEnvoi), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::STATUT_ENVOI, $statutEnvoi, $comparison);
		}
	}

	/**
	 * Filter the query on the date_envoi column
	 * 
	 * @param     string|array $dateEnvoi The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByDateEnvoi($dateEnvoi = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($dateEnvoi)) {
			if (array_values($dateEnvoi) === $dateEnvoi) {
				return $this->addUsingAlias(AbsenceEleveEnvoiPeer::DATE_ENVOI, $dateEnvoi, Criteria::IN);
			} else {
				if (isset($dateEnvoi['min'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::DATE_ENVOI, $dateEnvoi['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($dateEnvoi['max'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::DATE_ENVOI, $dateEnvoi['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::DATE_ENVOI, $dateEnvoi, $comparison);
		}
	}

	/**
	 * Filter the query on the created_at column
	 * 
	 * @param     string|array $createdAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByCreatedAt($createdAt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($createdAt)) {
			if (array_values($createdAt) === $createdAt) {
				return $this->addUsingAlias(AbsenceEleveEnvoiPeer::CREATED_AT, $createdAt, Criteria::IN);
			} else {
				if (isset($createdAt['min'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($createdAt['max'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::CREATED_AT, $createdAt, $comparison);
		}
	}

	/**
	 * Filter the query on the updated_at column
	 * 
	 * @param     string|array $updatedAt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByUpdatedAt($updatedAt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($updatedAt)) {
			if (array_values($updatedAt) === $updatedAt) {
				return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UPDATED_AT, $updatedAt, Criteria::IN);
			} else {
				if (isset($updatedAt['min'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($updatedAt['max'])) {
					$this->addUsingAlias(AbsenceEleveEnvoiPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UPDATED_AT, $updatedAt, $comparison);
		}
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function joinUtilisateurProfessionnel($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('UtilisateurProfessionnel');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	 * Filter the query by a related AbsenceEleveTypeEnvoi object
	 *
	 * @param     AbsenceEleveTypeEnvoi $absenceEleveTypeEnvoi  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTypeEnvoi($absenceEleveTypeEnvoi, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveEnvoiPeer::ID_TYPE_ENVOI, $absenceEleveTypeEnvoi->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTypeEnvoi relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTypeEnvoi($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTypeEnvoi');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'AbsenceEleveTypeEnvoi');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveTypeEnvoi relation AbsenceEleveTypeEnvoi object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeEnvoiQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeEnvoiQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTypeEnvoi($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTypeEnvoi', 'AbsenceEleveTypeEnvoiQuery');
	}

	/**
	 * Filter the query by a related JTraitementEnvoiEleve object
	 *
	 * @param     JTraitementEnvoiEleve $jTraitementEnvoiEleve  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByJTraitementEnvoiEleve($jTraitementEnvoiEleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $jTraitementEnvoiEleve->getAEnvoiId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JTraitementEnvoiEleve relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function joinJTraitementEnvoiEleve($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JTraitementEnvoiEleve');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'JTraitementEnvoiEleve');
		}
		
		return $this;
	}

	/**
	 * Use the JTraitementEnvoiEleve relation JTraitementEnvoiEleve object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JTraitementEnvoiEleveQuery A secondary query class using the current class as primary query
	 */
	public function useJTraitementEnvoiEleveQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJTraitementEnvoiEleve($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JTraitementEnvoiEleve', 'JTraitementEnvoiEleveQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 * using the j_traitements_envois table as cross reference
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJTraitementEnvoiEleveQuery()
				->filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison)
			->endUse();
	}
	
	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveEnvoi $absenceEleveEnvoi Object to remove from the list of results
	 *
	 * @return    AbsenceEleveEnvoiQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveEnvoi = null)
	{
		if ($absenceEleveEnvoi) {
			$this->addUsingAlias(AbsenceEleveEnvoiPeer::ID, $absenceEleveEnvoi->getId(), Criteria::NOT_EQUAL);
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

	// timestampable behavior
	
	/**
	 * Filter by the latest updated
	 *
	 * @param      int $nbDays Maximum age of the latest update in days
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function recentlyUpdated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveEnvoiPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Filter by the latest created
	 *
	 * @param      int $nbDays Maximum age of in days
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function recentlyCreated($nbDays = 7)
	{
		return $this->addUsingAlias(AbsenceEleveEnvoiPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order by update date desc
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function lastUpdatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveEnvoiPeer::UPDATED_AT);
	}
	
	/**
	 * Order by update date asc
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function firstUpdatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveEnvoiPeer::UPDATED_AT);
	}
	
	/**
	 * Order by create date desc
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function lastCreatedFirst()
	{
		return $this->addDescendingOrderByColumn(AbsenceEleveEnvoiPeer::CREATED_AT);
	}
	
	/**
	 * Order by create date asc
	 *
	 * @return     AbsenceEleveEnvoiQuery The current query, for fuid interface
	 */
	public function firstCreatedFirst()
	{
		return $this->addAscendingOrderByColumn(AbsenceEleveEnvoiPeer::CREATED_AT);
	}

} // BaseAbsenceEleveEnvoiQuery
