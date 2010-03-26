<?php


/**
 * Base class that represents a query for the 'ct_entry' table.
 *
 * Compte rendu du cahier de texte
 *
 * @method     CahierTexteCompteRenduQuery orderByIdCt($order = Criteria::ASC) Order by the id_ct column
 * @method     CahierTexteCompteRenduQuery orderByHeureEntry($order = Criteria::ASC) Order by the heure_entry column
 * @method     CahierTexteCompteRenduQuery orderByDateCt($order = Criteria::ASC) Order by the date_ct column
 * @method     CahierTexteCompteRenduQuery orderByContenu($order = Criteria::ASC) Order by the contenu column
 * @method     CahierTexteCompteRenduQuery orderByVise($order = Criteria::ASC) Order by the vise column
 * @method     CahierTexteCompteRenduQuery orderByVisa($order = Criteria::ASC) Order by the visa column
 * @method     CahierTexteCompteRenduQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     CahierTexteCompteRenduQuery orderByIdLogin($order = Criteria::ASC) Order by the id_login column
 * @method     CahierTexteCompteRenduQuery orderByIdSequence($order = Criteria::ASC) Order by the id_sequence column
 *
 * @method     CahierTexteCompteRenduQuery groupByIdCt() Group by the id_ct column
 * @method     CahierTexteCompteRenduQuery groupByHeureEntry() Group by the heure_entry column
 * @method     CahierTexteCompteRenduQuery groupByDateCt() Group by the date_ct column
 * @method     CahierTexteCompteRenduQuery groupByContenu() Group by the contenu column
 * @method     CahierTexteCompteRenduQuery groupByVise() Group by the vise column
 * @method     CahierTexteCompteRenduQuery groupByVisa() Group by the visa column
 * @method     CahierTexteCompteRenduQuery groupByIdGroupe() Group by the id_groupe column
 * @method     CahierTexteCompteRenduQuery groupByIdLogin() Group by the id_login column
 * @method     CahierTexteCompteRenduQuery groupByIdSequence() Group by the id_sequence column
 *
 * @method     CahierTexteCompteRenduQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteCompteRenduQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteCompteRenduQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteCompteRenduQuery leftJoinGroupe($relationAlias = '') Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteCompteRenduQuery rightJoinGroupe($relationAlias = '') Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteCompteRenduQuery innerJoinGroupe($relationAlias = '') Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     CahierTexteCompteRenduQuery leftJoinUtilisateurProfessionnel($relationAlias = '') Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteCompteRenduQuery rightJoinUtilisateurProfessionnel($relationAlias = '') Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteCompteRenduQuery innerJoinUtilisateurProfessionnel($relationAlias = '') Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     CahierTexteCompteRenduQuery leftJoinCahierTexteSequence($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteCompteRenduQuery rightJoinCahierTexteSequence($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteCompteRenduQuery innerJoinCahierTexteSequence($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteSequence relation
 *
 * @method     CahierTexteCompteRenduQuery leftJoinCahierTexteCompteRenduFichierJoint($relationAlias = '') Adds a LEFT JOIN clause to the query using the CahierTexteCompteRenduFichierJoint relation
 * @method     CahierTexteCompteRenduQuery rightJoinCahierTexteCompteRenduFichierJoint($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CahierTexteCompteRenduFichierJoint relation
 * @method     CahierTexteCompteRenduQuery innerJoinCahierTexteCompteRenduFichierJoint($relationAlias = '') Adds a INNER JOIN clause to the query using the CahierTexteCompteRenduFichierJoint relation
 *
 * @method     CahierTexteCompteRendu findOne(PropelPDO $con = null) Return the first CahierTexteCompteRendu matching the query
 * @method     CahierTexteCompteRendu findOneByIdCt(int $id_ct) Return the first CahierTexteCompteRendu filtered by the id_ct column
 * @method     CahierTexteCompteRendu findOneByHeureEntry(string $heure_entry) Return the first CahierTexteCompteRendu filtered by the heure_entry column
 * @method     CahierTexteCompteRendu findOneByDateCt(int $date_ct) Return the first CahierTexteCompteRendu filtered by the date_ct column
 * @method     CahierTexteCompteRendu findOneByContenu(string $contenu) Return the first CahierTexteCompteRendu filtered by the contenu column
 * @method     CahierTexteCompteRendu findOneByVise(string $vise) Return the first CahierTexteCompteRendu filtered by the vise column
 * @method     CahierTexteCompteRendu findOneByVisa(string $visa) Return the first CahierTexteCompteRendu filtered by the visa column
 * @method     CahierTexteCompteRendu findOneByIdGroupe(int $id_groupe) Return the first CahierTexteCompteRendu filtered by the id_groupe column
 * @method     CahierTexteCompteRendu findOneByIdLogin(string $id_login) Return the first CahierTexteCompteRendu filtered by the id_login column
 * @method     CahierTexteCompteRendu findOneByIdSequence(int $id_sequence) Return the first CahierTexteCompteRendu filtered by the id_sequence column
 *
 * @method     array findByIdCt(int $id_ct) Return CahierTexteCompteRendu objects filtered by the id_ct column
 * @method     array findByHeureEntry(string $heure_entry) Return CahierTexteCompteRendu objects filtered by the heure_entry column
 * @method     array findByDateCt(int $date_ct) Return CahierTexteCompteRendu objects filtered by the date_ct column
 * @method     array findByContenu(string $contenu) Return CahierTexteCompteRendu objects filtered by the contenu column
 * @method     array findByVise(string $vise) Return CahierTexteCompteRendu objects filtered by the vise column
 * @method     array findByVisa(string $visa) Return CahierTexteCompteRendu objects filtered by the visa column
 * @method     array findByIdGroupe(int $id_groupe) Return CahierTexteCompteRendu objects filtered by the id_groupe column
 * @method     array findByIdLogin(string $id_login) Return CahierTexteCompteRendu objects filtered by the id_login column
 * @method     array findByIdSequence(int $id_sequence) Return CahierTexteCompteRendu objects filtered by the id_sequence column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteCompteRenduQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCahierTexteCompteRenduQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteCompteRendu', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteCompteRenduQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteCompteRenduQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteCompteRenduQuery) {
			return $criteria;
		}
		$query = new CahierTexteCompteRenduQuery();
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
	 * @return    mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CahierTexteCompteRenduPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    the list of results, formatted by the current formatter
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
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_ct column
	 * 
	 * @param     int|array $idCt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByIdCt($idCt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idCt)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $idCt, Criteria::IN);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $idCt, $comparison);
		}
	}

	/**
	 * Filter the query on the heure_entry column
	 * 
	 * @param     string|array $heureEntry The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByHeureEntry($heureEntry = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($heureEntry)) {
			if (array_values($heureEntry) === $heureEntry) {
				return $this->addUsingAlias(CahierTexteCompteRenduPeer::HEURE_ENTRY, $heureEntry, Criteria::IN);
			} else {
				if (isset($heureEntry['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::HEURE_ENTRY, $heureEntry['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($heureEntry['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::HEURE_ENTRY, $heureEntry['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::HEURE_ENTRY, $heureEntry, $comparison);
		}
	}

	/**
	 * Filter the query on the date_ct column
	 * 
	 * @param     int|array $dateCt The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByDateCt($dateCt = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($dateCt)) {
			if (array_values($dateCt) === $dateCt) {
				return $this->addUsingAlias(CahierTexteCompteRenduPeer::DATE_CT, $dateCt, Criteria::IN);
			} else {
				if (isset($dateCt['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::DATE_CT, $dateCt['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($dateCt['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::DATE_CT, $dateCt['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::DATE_CT, $dateCt, $comparison);
		}
	}

	/**
	 * Filter the query on the contenu column
	 * 
	 * @param     string $contenu The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByContenu($contenu = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($contenu)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::CONTENU, $contenu, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $contenu)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::CONTENU, str_replace('*', '%', $contenu), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::CONTENU, $contenu, $comparison);
		}
	}

	/**
	 * Filter the query on the vise column
	 * 
	 * @param     string $vise The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByVise($vise = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($vise)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISE, $vise, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $vise)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISE, str_replace('*', '%', $vise), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISE, $vise, $comparison);
		}
	}

	/**
	 * Filter the query on the visa column
	 * 
	 * @param     string $visa The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByVisa($visa = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($visa)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISA, $visa, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $visa)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISA, str_replace('*', '%', $visa), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::VISA, $visa, $comparison);
		}
	}

	/**
	 * Filter the query on the id_groupe column
	 * 
	 * @param     int|array $idGroupe The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idGroupe)) {
			if (array_values($idGroupe) === $idGroupe) {
				return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_GROUPE, $idGroupe, Criteria::IN);
			} else {
				if (isset($idGroupe['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::ID_GROUPE, $idGroupe['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($idGroupe['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::ID_GROUPE, $idGroupe['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_GROUPE, $idGroupe, $comparison);
		}
	}

	/**
	 * Filter the query on the id_login column
	 * 
	 * @param     string $idLogin The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByIdLogin($idLogin = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idLogin)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_LOGIN, $idLogin, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $idLogin)) {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_LOGIN, str_replace('*', '%', $idLogin), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_LOGIN, $idLogin, $comparison);
		}
	}

	/**
	 * Filter the query on the id_sequence column
	 * 
	 * @param     int|array $idSequence The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByIdSequence($idSequence = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($idSequence)) {
			if (array_values($idSequence) === $idSequence) {
				return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_SEQUENCE, $idSequence, Criteria::IN);
			} else {
				if (isset($idSequence['min'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::ID_SEQUENCE, $idSequence['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($idSequence['max'])) {
					$this->addUsingAlias(CahierTexteCompteRenduPeer::ID_SEQUENCE, $idSequence['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(CahierTexteCompteRenduPeer::ID_SEQUENCE, $idSequence, $comparison);
		}
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe $groupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteCompteRenduPeer::ID_GROUPE, $groupe->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Groupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Groupe');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useGroupeQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteCompteRenduPeer::ID_LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the UtilisateurProfessionnel relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
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
	 * Filter the query by a related CahierTexteSequence object
	 *
	 * @param     CahierTexteSequence $cahierTexteSequence  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteSequence($cahierTexteSequence, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteCompteRenduPeer::ID_SEQUENCE, $cahierTexteSequence->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteSequence relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function joinCahierTexteSequence($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteSequence');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteSequence');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteSequence relation CahierTexteSequence object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteSequenceQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteSequenceQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteSequence($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteSequence', 'CahierTexteSequenceQuery');
	}

	/**
	 * Filter the query by a related CahierTexteCompteRenduFichierJoint object
	 *
	 * @param     CahierTexteCompteRenduFichierJoint $cahierTexteCompteRenduFichierJoint  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteCompteRenduFichierJoint($cahierTexteCompteRenduFichierJoint, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $cahierTexteCompteRenduFichierJoint->getIdCt(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteCompteRenduFichierJoint relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function joinCahierTexteCompteRenduFichierJoint($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteCompteRenduFichierJoint');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CahierTexteCompteRenduFichierJoint');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteCompteRenduFichierJoint relation CahierTexteCompteRenduFichierJoint object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteCompteRenduFichierJointQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteCompteRenduFichierJointQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCahierTexteCompteRenduFichierJoint($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteCompteRenduFichierJoint', 'CahierTexteCompteRenduFichierJointQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteCompteRendu $cahierTexteCompteRendu Object to remove from the list of results
	 *
	 * @return    CahierTexteCompteRenduQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteCompteRendu = null)
	{
		if ($cahierTexteCompteRendu) {
			$this->addUsingAlias(CahierTexteCompteRenduPeer::ID_CT, $cahierTexteCompteRendu->getIdCt(), Criteria::NOT_EQUAL);
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

} // BaseCahierTexteCompteRenduQuery
