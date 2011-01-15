<?php


/**
 * Base class that represents a query for the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * @method     AbsenceEleveTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AbsenceEleveTypeQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AbsenceEleveTypeQuery orderByJustificationExigible($order = Criteria::ASC) Order by the justification_exigible column
 * @method     AbsenceEleveTypeQuery orderBySousResponsabiliteEtablissement($order = Criteria::ASC) Order by the sous_responsabilite_etablissement column
 * @method     AbsenceEleveTypeQuery orderByManquementObligationPresence($order = Criteria::ASC) Order by the manquement_obligation_presence column
 * @method     AbsenceEleveTypeQuery orderByRetardBulletin($order = Criteria::ASC) Order by the retard_bulletin column
 * @method     AbsenceEleveTypeQuery orderByTypeSaisie($order = Criteria::ASC) Order by the type_saisie column
 * @method     AbsenceEleveTypeQuery orderByCommentaire($order = Criteria::ASC) Order by the commentaire column
 * @method     AbsenceEleveTypeQuery orderByIdLieu($order = Criteria::ASC) Order by the id_lieu column
 * @method     AbsenceEleveTypeQuery orderBySortableRank($order = Criteria::ASC) Order by the sortable_rank column
 *
 * @method     AbsenceEleveTypeQuery groupById() Group by the id column
 * @method     AbsenceEleveTypeQuery groupByNom() Group by the nom column
 * @method     AbsenceEleveTypeQuery groupByJustificationExigible() Group by the justification_exigible column
 * @method     AbsenceEleveTypeQuery groupBySousResponsabiliteEtablissement() Group by the sous_responsabilite_etablissement column
 * @method     AbsenceEleveTypeQuery groupByManquementObligationPresence() Group by the manquement_obligation_presence column
 * @method     AbsenceEleveTypeQuery groupByRetardBulletin() Group by the retard_bulletin column
 * @method     AbsenceEleveTypeQuery groupByTypeSaisie() Group by the type_saisie column
 * @method     AbsenceEleveTypeQuery groupByCommentaire() Group by the commentaire column
 * @method     AbsenceEleveTypeQuery groupByIdLieu() Group by the id_lieu column
 * @method     AbsenceEleveTypeQuery groupBySortableRank() Group by the sortable_rank column
 *
 * @method     AbsenceEleveTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AbsenceEleveTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AbsenceEleveTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AbsenceEleveTypeQuery leftJoinAbsenceEleveLieu($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveLieu relation
 * @method     AbsenceEleveTypeQuery rightJoinAbsenceEleveLieu($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveLieu relation
 * @method     AbsenceEleveTypeQuery innerJoinAbsenceEleveLieu($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveLieu relation
 *
 * @method     AbsenceEleveTypeQuery leftJoinAbsenceEleveTypeStatutAutorise($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 * @method     AbsenceEleveTypeQuery rightJoinAbsenceEleveTypeStatutAutorise($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 * @method     AbsenceEleveTypeQuery innerJoinAbsenceEleveTypeStatutAutorise($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
 *
 * @method     AbsenceEleveTypeQuery leftJoinAbsenceEleveTraitement($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveTypeQuery rightJoinAbsenceEleveTraitement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveTraitement relation
 * @method     AbsenceEleveTypeQuery innerJoinAbsenceEleveTraitement($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveTraitement relation
 *
 * @method     AbsenceEleveType findOne(PropelPDO $con = null) Return the first AbsenceEleveType matching the query
 * @method     AbsenceEleveType findOneOrCreate(PropelPDO $con = null) Return the first AbsenceEleveType matching the query, or a new AbsenceEleveType object populated from the query conditions when no match is found
 *
 * @method     AbsenceEleveType findOneById(int $id) Return the first AbsenceEleveType filtered by the id column
 * @method     AbsenceEleveType findOneByNom(string $nom) Return the first AbsenceEleveType filtered by the nom column
 * @method     AbsenceEleveType findOneByJustificationExigible(boolean $justification_exigible) Return the first AbsenceEleveType filtered by the justification_exigible column
 * @method     AbsenceEleveType findOneBySousResponsabiliteEtablissement(string $sous_responsabilite_etablissement) Return the first AbsenceEleveType filtered by the sous_responsabilite_etablissement column
 * @method     AbsenceEleveType findOneByManquementObligationPresence(string $manquement_obligation_presence) Return the first AbsenceEleveType filtered by the manquement_obligation_presence column
 * @method     AbsenceEleveType findOneByRetardBulletin(string $retard_bulletin) Return the first AbsenceEleveType filtered by the retard_bulletin column
 * @method     AbsenceEleveType findOneByTypeSaisie(string $type_saisie) Return the first AbsenceEleveType filtered by the type_saisie column
 * @method     AbsenceEleveType findOneByCommentaire(string $commentaire) Return the first AbsenceEleveType filtered by the commentaire column
 * @method     AbsenceEleveType findOneByIdLieu(int $id_lieu) Return the first AbsenceEleveType filtered by the id_lieu column
 * @method     AbsenceEleveType findOneBySortableRank(int $sortable_rank) Return the first AbsenceEleveType filtered by the sortable_rank column
 *
 * @method     array findById(int $id) Return AbsenceEleveType objects filtered by the id column
 * @method     array findByNom(string $nom) Return AbsenceEleveType objects filtered by the nom column
 * @method     array findByJustificationExigible(boolean $justification_exigible) Return AbsenceEleveType objects filtered by the justification_exigible column
 * @method     array findBySousResponsabiliteEtablissement(string $sous_responsabilite_etablissement) Return AbsenceEleveType objects filtered by the sous_responsabilite_etablissement column
 * @method     array findByManquementObligationPresence(string $manquement_obligation_presence) Return AbsenceEleveType objects filtered by the manquement_obligation_presence column
 * @method     array findByRetardBulletin(string $retard_bulletin) Return AbsenceEleveType objects filtered by the retard_bulletin column
 * @method     array findByTypeSaisie(string $type_saisie) Return AbsenceEleveType objects filtered by the type_saisie column
 * @method     array findByCommentaire(string $commentaire) Return AbsenceEleveType objects filtered by the commentaire column
 * @method     array findByIdLieu(int $id_lieu) Return AbsenceEleveType objects filtered by the id_lieu column
 * @method     array findBySortableRank(int $sortable_rank) Return AbsenceEleveType objects filtered by the sortable_rank column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTypeQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseAbsenceEleveTypeQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AbsenceEleveType', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AbsenceEleveTypeQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AbsenceEleveTypeQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AbsenceEleveTypeQuery) {
			return $criteria;
		}
		$query = new AbsenceEleveTypeQuery();
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
	 * @return    AbsenceEleveType|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AbsenceEleveTypePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $id The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveTypePeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the justification_exigible column
	 * 
	 * @param     boolean|string $justificationExigible The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByJustificationExigible($justificationExigible = null, $comparison = null)
	{
		if (is_string($justificationExigible)) {
			$justification_exigible = in_array(strtolower($justificationExigible), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE, $justificationExigible, $comparison);
	}

	/**
	 * Filter the query on the sous_responsabilite_etablissement column
	 * 
	 * @param     string $sousResponsabiliteEtablissement The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterBySousResponsabiliteEtablissement($sousResponsabiliteEtablissement = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($sousResponsabiliteEtablissement)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $sousResponsabiliteEtablissement)) {
				$sousResponsabiliteEtablissement = str_replace('*', '%', $sousResponsabiliteEtablissement);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::SOUS_RESPONSABILITE_ETABLISSEMENT, $sousResponsabiliteEtablissement, $comparison);
	}

	/**
	 * Filter the query on the manquement_obligation_presence column
	 * 
	 * @param     string $manquementObligationPresence The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByManquementObligationPresence($manquementObligationPresence = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($manquementObligationPresence)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $manquementObligationPresence)) {
				$manquementObligationPresence = str_replace('*', '%', $manquementObligationPresence);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::MANQUEMENT_OBLIGATION_PRESENCE, $manquementObligationPresence, $comparison);
	}

	/**
	 * Filter the query on the retard_bulletin column
	 * 
	 * @param     string $retardBulletin The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByRetardBulletin($retardBulletin = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($retardBulletin)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $retardBulletin)) {
				$retardBulletin = str_replace('*', '%', $retardBulletin);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::RETARD_BULLETIN, $retardBulletin, $comparison);
	}

	/**
	 * Filter the query on the type_saisie column
	 * 
	 * @param     string $typeSaisie The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByTypeSaisie($typeSaisie = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($typeSaisie)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $typeSaisie)) {
				$typeSaisie = str_replace('*', '%', $typeSaisie);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::TYPE_SAISIE, $typeSaisie, $comparison);
	}

	/**
	 * Filter the query on the commentaire column
	 * 
	 * @param     string $commentaire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AbsenceEleveTypePeer::COMMENTAIRE, $commentaire, $comparison);
	}

	/**
	 * Filter the query on the id_lieu column
	 * 
	 * @param     int|array $idLieu The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByIdLieu($idLieu = null, $comparison = null)
	{
		if (is_array($idLieu)) {
			$useMinMax = false;
			if (isset($idLieu['min'])) {
				$this->addUsingAlias(AbsenceEleveTypePeer::ID_LIEU, $idLieu['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idLieu['max'])) {
				$this->addUsingAlias(AbsenceEleveTypePeer::ID_LIEU, $idLieu['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::ID_LIEU, $idLieu, $comparison);
	}

	/**
	 * Filter the query on the sortable_rank column
	 * 
	 * @param     int|array $sortableRank The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterBySortableRank($sortableRank = null, $comparison = null)
	{
		if (is_array($sortableRank)) {
			$useMinMax = false;
			if (isset($sortableRank['min'])) {
				$this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($sortableRank['max'])) {
				$this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AbsenceEleveTypePeer::SORTABLE_RANK, $sortableRank, $comparison);
	}

	/**
	 * Filter the query by a related AbsenceEleveLieu object
	 *
	 * @param     AbsenceEleveLieu $absenceEleveLieu  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveLieu($absenceEleveLieu, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::ID_LIEU, $absenceEleveLieu->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveLieu relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
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
	 * Filter the query by a related AbsenceEleveTypeStatutAutorise object
	 *
	 * @param     AbsenceEleveTypeStatutAutorise $absenceEleveTypeStatutAutorise  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTypeStatutAutorise($absenceEleveTypeStatutAutorise, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveTypeStatutAutorise->getIdAType(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTypeStatutAutorise relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTypeStatutAutorise($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AbsenceEleveTypeStatutAutorise');
		
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
			$this->addJoinObject($join, 'AbsenceEleveTypeStatutAutorise');
		}
		
		return $this;
	}

	/**
	 * Use the AbsenceEleveTypeStatutAutorise relation AbsenceEleveTypeStatutAutorise object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeStatutAutoriseQuery A secondary query class using the current class as primary query
	 */
	public function useAbsenceEleveTypeStatutAutoriseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAbsenceEleveTypeStatutAutorise($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTypeStatutAutorise', 'AbsenceEleveTypeStatutAutoriseQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveTraitement object
	 *
	 * @param     AbsenceEleveTraitement $absenceEleveTraitement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveTraitement($absenceEleveTraitement, $comparison = null)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveTraitement->getATypeId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveTraitement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveTraitement($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useAbsenceEleveTraitementQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveTraitement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveTraitement', 'AbsenceEleveTraitementQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AbsenceEleveType $absenceEleveType Object to remove from the list of results
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function prune($absenceEleveType = null)
	{
		if ($absenceEleveType) {
			$this->addUsingAlias(AbsenceEleveTypePeer::ID, $absenceEleveType->getId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

	// sortable behavior
	
	/**
	 * Filter the query based on a rank in the list
	 *
	 * @param     integer   $rank rank
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function filterByRank($rank)
	{
		return $this
			->addUsingAlias(AbsenceEleveTypePeer::RANK_COL, $rank, Criteria::EQUAL);
	}
	
	/**
	 * Order the query based on the rank in the list.
	 * Using the default $order, returns the item with the lowest rank first
	 *
	 * @param     string $order either Criteria::ASC (default) or Criteria::DESC
	 *
	 * @return    AbsenceEleveTypeQuery The current query, for fluid interface
	 */
	public function orderByRank($order = Criteria::ASC)
	{
		$order = strtoupper($order);
		switch ($order) {
			case Criteria::ASC:
				return $this->addAscendingOrderByColumn($this->getAliasedColName(AbsenceEleveTypePeer::RANK_COL));
				break;
			case Criteria::DESC:
				return $this->addDescendingOrderByColumn($this->getAliasedColName(AbsenceEleveTypePeer::RANK_COL));
				break;
			default:
				throw new PropelException('AbsenceEleveTypeQuery::orderBy() only accepts "asc" or "desc" as argument');
		}
	}
	
	/**
	 * Get an item from the list based on its rank
	 *
	 * @param     integer   $rank rank
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType
	 */
	public function findOneByRank($rank, PropelPDO $con = null)
	{
		return $this
			->filterByRank($rank)
			->findOne($con);
	}
	
	/**
	 * Returns the list of objects
	 *
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     mixed the list of results, formatted by the current formatter
	 */
	public function findList($con = null)
	{
		return $this
			->orderByRank()
			->find($con);
	}
	
	/**
	 * Get the highest rank
	 * 
	 * @param     PropelPDO optional connection
	 *
	 * @return    integer highest position
	 */
	public function getMaxRank(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		// shift the objects with a position lower than the one of object
		$this->addSelectColumn('MAX(' . AbsenceEleveTypePeer::RANK_COL . ')');
		$stmt = $this->getSelectStatement($con);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * Reorder a set of sortable objects based on a list of id/position
	 * Beware that there is no check made on the positions passed
	 * So incoherent positions will result in an incoherent list
	 *
	 * @param     array     $order id => rank pairs
	 * @param     PropelPDO $con   optional connection
	 *
	 * @return    boolean true if the reordering took place, false if a database problem prevented it
	 */
	public function reorder(array $order, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		
		$con->beginTransaction();
		try {
			$ids = array_keys($order);
			$objects = $this->findPks($ids, $con);
			foreach ($objects as $object) {
				$pk = $object->getPrimaryKey();
				if ($object->getSortableRank() != $order[$pk]) {
					$object->setSortableRank($order[$pk]);
					$object->save($con);
				}
			}
			$con->commit();
	
			return true;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

} // BaseAbsenceEleveTypeQuery
