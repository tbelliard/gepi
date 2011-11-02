<?php


/**
 * Base class that represents a query for the 'ct_devoirs_entry' table.
 *
 * Travail Ã  faire (devoir) cahier de texte
 *
 * @method     CahierTexteTravailAFaireQuery orderByIdCt($order = Criteria::ASC) Order by the id_ct column
 * @method     CahierTexteTravailAFaireQuery orderByDateCt($order = Criteria::ASC) Order by the date_ct column
 * @method     CahierTexteTravailAFaireQuery orderByContenu($order = Criteria::ASC) Order by the contenu column
 * @method     CahierTexteTravailAFaireQuery orderByVise($order = Criteria::ASC) Order by the vise column
 * @method     CahierTexteTravailAFaireQuery orderByIdGroupe($order = Criteria::ASC) Order by the id_groupe column
 * @method     CahierTexteTravailAFaireQuery orderByIdLogin($order = Criteria::ASC) Order by the id_login column
 * @method     CahierTexteTravailAFaireQuery orderByIdSequence($order = Criteria::ASC) Order by the id_sequence column
 * @method     CahierTexteTravailAFaireQuery orderByDateVisibiliteEleve($order = Criteria::ASC) Order by the date_visibilite_eleve column
 *
 * @method     CahierTexteTravailAFaireQuery groupByIdCt() Group by the id_ct column
 * @method     CahierTexteTravailAFaireQuery groupByDateCt() Group by the date_ct column
 * @method     CahierTexteTravailAFaireQuery groupByContenu() Group by the contenu column
 * @method     CahierTexteTravailAFaireQuery groupByVise() Group by the vise column
 * @method     CahierTexteTravailAFaireQuery groupByIdGroupe() Group by the id_groupe column
 * @method     CahierTexteTravailAFaireQuery groupByIdLogin() Group by the id_login column
 * @method     CahierTexteTravailAFaireQuery groupByIdSequence() Group by the id_sequence column
 * @method     CahierTexteTravailAFaireQuery groupByDateVisibiliteEleve() Group by the date_visibilite_eleve column
 *
 * @method     CahierTexteTravailAFaireQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CahierTexteTravailAFaireQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CahierTexteTravailAFaireQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CahierTexteTravailAFaireQuery leftJoinGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteTravailAFaireQuery rightJoinGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Groupe relation
 * @method     CahierTexteTravailAFaireQuery innerJoinGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the Groupe relation
 *
 * @method     CahierTexteTravailAFaireQuery leftJoinUtilisateurProfessionnel($relationAlias = null) Adds a LEFT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteTravailAFaireQuery rightJoinUtilisateurProfessionnel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UtilisateurProfessionnel relation
 * @method     CahierTexteTravailAFaireQuery innerJoinUtilisateurProfessionnel($relationAlias = null) Adds a INNER JOIN clause to the query using the UtilisateurProfessionnel relation
 *
 * @method     CahierTexteTravailAFaireQuery leftJoinCahierTexteSequence($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteTravailAFaireQuery rightJoinCahierTexteSequence($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteSequence relation
 * @method     CahierTexteTravailAFaireQuery innerJoinCahierTexteSequence($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteSequence relation
 *
 * @method     CahierTexteTravailAFaireQuery leftJoinCahierTexteTravailAFaireFichierJoint($relationAlias = null) Adds a LEFT JOIN clause to the query using the CahierTexteTravailAFaireFichierJoint relation
 * @method     CahierTexteTravailAFaireQuery rightJoinCahierTexteTravailAFaireFichierJoint($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CahierTexteTravailAFaireFichierJoint relation
 * @method     CahierTexteTravailAFaireQuery innerJoinCahierTexteTravailAFaireFichierJoint($relationAlias = null) Adds a INNER JOIN clause to the query using the CahierTexteTravailAFaireFichierJoint relation
 *
 * @method     CahierTexteTravailAFaire findOne(PropelPDO $con = null) Return the first CahierTexteTravailAFaire matching the query
 * @method     CahierTexteTravailAFaire findOneOrCreate(PropelPDO $con = null) Return the first CahierTexteTravailAFaire matching the query, or a new CahierTexteTravailAFaire object populated from the query conditions when no match is found
 *
 * @method     CahierTexteTravailAFaire findOneByIdCt(int $id_ct) Return the first CahierTexteTravailAFaire filtered by the id_ct column
 * @method     CahierTexteTravailAFaire findOneByDateCt(int $date_ct) Return the first CahierTexteTravailAFaire filtered by the date_ct column
 * @method     CahierTexteTravailAFaire findOneByContenu(string $contenu) Return the first CahierTexteTravailAFaire filtered by the contenu column
 * @method     CahierTexteTravailAFaire findOneByVise(string $vise) Return the first CahierTexteTravailAFaire filtered by the vise column
 * @method     CahierTexteTravailAFaire findOneByIdGroupe(int $id_groupe) Return the first CahierTexteTravailAFaire filtered by the id_groupe column
 * @method     CahierTexteTravailAFaire findOneByIdLogin(string $id_login) Return the first CahierTexteTravailAFaire filtered by the id_login column
 * @method     CahierTexteTravailAFaire findOneByIdSequence(int $id_sequence) Return the first CahierTexteTravailAFaire filtered by the id_sequence column
 * @method     CahierTexteTravailAFaire findOneByDateVisibiliteEleve(string $date_visibilite_eleve) Return the first CahierTexteTravailAFaire filtered by the date_visibilite_eleve column
 *
 * @method     array findByIdCt(int $id_ct) Return CahierTexteTravailAFaire objects filtered by the id_ct column
 * @method     array findByDateCt(int $date_ct) Return CahierTexteTravailAFaire objects filtered by the date_ct column
 * @method     array findByContenu(string $contenu) Return CahierTexteTravailAFaire objects filtered by the contenu column
 * @method     array findByVise(string $vise) Return CahierTexteTravailAFaire objects filtered by the vise column
 * @method     array findByIdGroupe(int $id_groupe) Return CahierTexteTravailAFaire objects filtered by the id_groupe column
 * @method     array findByIdLogin(string $id_login) Return CahierTexteTravailAFaire objects filtered by the id_login column
 * @method     array findByIdSequence(int $id_sequence) Return CahierTexteTravailAFaire objects filtered by the id_sequence column
 * @method     array findByDateVisibiliteEleve(string $date_visibilite_eleve) Return CahierTexteTravailAFaire objects filtered by the date_visibilite_eleve column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteTravailAFaireQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCahierTexteTravailAFaireQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'CahierTexteTravailAFaire', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CahierTexteTravailAFaireQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CahierTexteTravailAFaireQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CahierTexteTravailAFaireQuery) {
			return $criteria;
		}
		$query = new CahierTexteTravailAFaireQuery();
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
	 * @return    CahierTexteTravailAFaire|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CahierTexteTravailAFairePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_CT, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_CT, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id_ct column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdCt(1234); // WHERE id_ct = 1234
	 * $query->filterByIdCt(array(12, 34)); // WHERE id_ct IN (12, 34)
	 * $query->filterByIdCt(array('min' => 12)); // WHERE id_ct > 12
	 * </code>
	 *
	 * @param     mixed $idCt The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByIdCt($idCt = null, $comparison = null)
	{
		if (is_array($idCt) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_CT, $idCt, $comparison);
	}

	/**
	 * Filter the query on the date_ct column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByDateCt(1234); // WHERE date_ct = 1234
	 * $query->filterByDateCt(array(12, 34)); // WHERE date_ct IN (12, 34)
	 * $query->filterByDateCt(array('min' => 12)); // WHERE date_ct > 12
	 * </code>
	 *
	 * @param     mixed $dateCt The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByDateCt($dateCt = null, $comparison = null)
	{
		if (is_array($dateCt)) {
			$useMinMax = false;
			if (isset($dateCt['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_CT, $dateCt['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateCt['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_CT, $dateCt['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_CT, $dateCt, $comparison);
	}

	/**
	 * Filter the query on the contenu column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByContenu('fooValue');   // WHERE contenu = 'fooValue'
	 * $query->filterByContenu('%fooValue%'); // WHERE contenu LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $contenu The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByContenu($contenu = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($contenu)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $contenu)) {
				$contenu = str_replace('*', '%', $contenu);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::CONTENU, $contenu, $comparison);
	}

	/**
	 * Filter the query on the vise column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByVise('fooValue');   // WHERE vise = 'fooValue'
	 * $query->filterByVise('%fooValue%'); // WHERE vise LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $vise The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByVise($vise = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($vise)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $vise)) {
				$vise = str_replace('*', '%', $vise);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::VISE, $vise, $comparison);
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
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByIdGroupe($idGroupe = null, $comparison = null)
	{
		if (is_array($idGroupe)) {
			$useMinMax = false;
			if (isset($idGroupe['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::ID_GROUPE, $idGroupe['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idGroupe['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::ID_GROUPE, $idGroupe['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_GROUPE, $idGroupe, $comparison);
	}

	/**
	 * Filter the query on the id_login column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdLogin('fooValue');   // WHERE id_login = 'fooValue'
	 * $query->filterByIdLogin('%fooValue%'); // WHERE id_login LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $idLogin The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByIdLogin($idLogin = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($idLogin)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $idLogin)) {
				$idLogin = str_replace('*', '%', $idLogin);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_LOGIN, $idLogin, $comparison);
	}

	/**
	 * Filter the query on the id_sequence column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByIdSequence(1234); // WHERE id_sequence = 1234
	 * $query->filterByIdSequence(array(12, 34)); // WHERE id_sequence IN (12, 34)
	 * $query->filterByIdSequence(array('min' => 12)); // WHERE id_sequence > 12
	 * </code>
	 *
	 * @see       filterByCahierTexteSequence()
	 *
	 * @param     mixed $idSequence The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByIdSequence($idSequence = null, $comparison = null)
	{
		if (is_array($idSequence)) {
			$useMinMax = false;
			if (isset($idSequence['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::ID_SEQUENCE, $idSequence['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idSequence['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::ID_SEQUENCE, $idSequence['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::ID_SEQUENCE, $idSequence, $comparison);
	}

	/**
	 * Filter the query on the date_visibilite_eleve column
	 * 
	 * Example usage:
	 * <code>
	 * $query->filterByDateVisibiliteEleve('2011-03-14'); // WHERE date_visibilite_eleve = '2011-03-14'
	 * $query->filterByDateVisibiliteEleve('now'); // WHERE date_visibilite_eleve = '2011-03-14'
	 * $query->filterByDateVisibiliteEleve(array('max' => 'yesterday')); // WHERE date_visibilite_eleve > '2011-03-13'
	 * </code>
	 *
	 * @param     mixed $dateVisibiliteEleve The value to use as filter.
	 *              Values can be integers (unix timestamps), DateTime objects, or strings.
	 *              Empty strings are treated as NULL.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByDateVisibiliteEleve($dateVisibiliteEleve = null, $comparison = null)
	{
		if (is_array($dateVisibiliteEleve)) {
			$useMinMax = false;
			if (isset($dateVisibiliteEleve['min'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_VISIBILITE_ELEVE, $dateVisibiliteEleve['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateVisibiliteEleve['max'])) {
				$this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_VISIBILITE_ELEVE, $dateVisibiliteEleve['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CahierTexteTravailAFairePeer::DATE_VISIBILITE_ELEVE, $dateVisibiliteEleve, $comparison);
	}

	/**
	 * Filter the query by a related Groupe object
	 *
	 * @param     Groupe|PropelCollection $groupe The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = null)
	{
		if ($groupe instanceof Groupe) {
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_GROUPE, $groupe->getId(), $comparison);
		} elseif ($groupe instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_GROUPE, $groupe->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function joinGroupe($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
	public function useGroupeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Groupe', 'GroupeQuery');
	}

	/**
	 * Filter the query by a related UtilisateurProfessionnel object
	 *
	 * @param     UtilisateurProfessionnel|PropelCollection $utilisateurProfessionnel The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = null)
	{
		if ($utilisateurProfessionnel instanceof UtilisateurProfessionnel) {
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_LOGIN, $utilisateurProfessionnel->getLogin(), $comparison);
		} elseif ($utilisateurProfessionnel instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_LOGIN, $utilisateurProfessionnel->toKeyValue('PrimaryKey', 'Login'), $comparison);
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
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
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
	 * Filter the query by a related CahierTexteSequence object
	 *
	 * @param     CahierTexteSequence|PropelCollection $cahierTexteSequence The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteSequence($cahierTexteSequence, $comparison = null)
	{
		if ($cahierTexteSequence instanceof CahierTexteSequence) {
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_SEQUENCE, $cahierTexteSequence->getId(), $comparison);
		} elseif ($cahierTexteSequence instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_SEQUENCE, $cahierTexteSequence->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByCahierTexteSequence() only accepts arguments of type CahierTexteSequence or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteSequence relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function joinCahierTexteSequence($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteSequence');
		
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
	public function useCahierTexteSequenceQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCahierTexteSequence($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteSequence', 'CahierTexteSequenceQuery');
	}

	/**
	 * Filter the query by a related CahierTexteTravailAFaireFichierJoint object
	 *
	 * @param     CahierTexteTravailAFaireFichierJoint $cahierTexteTravailAFaireFichierJoint  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function filterByCahierTexteTravailAFaireFichierJoint($cahierTexteTravailAFaireFichierJoint, $comparison = null)
	{
		if ($cahierTexteTravailAFaireFichierJoint instanceof CahierTexteTravailAFaireFichierJoint) {
			return $this
				->addUsingAlias(CahierTexteTravailAFairePeer::ID_CT, $cahierTexteTravailAFaireFichierJoint->getIdCtDevoir(), $comparison);
		} elseif ($cahierTexteTravailAFaireFichierJoint instanceof PropelCollection) {
			return $this
				->useCahierTexteTravailAFaireFichierJointQuery()
					->filterByPrimaryKeys($cahierTexteTravailAFaireFichierJoint->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByCahierTexteTravailAFaireFichierJoint() only accepts arguments of type CahierTexteTravailAFaireFichierJoint or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the CahierTexteTravailAFaireFichierJoint relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function joinCahierTexteTravailAFaireFichierJoint($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CahierTexteTravailAFaireFichierJoint');
		
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
			$this->addJoinObject($join, 'CahierTexteTravailAFaireFichierJoint');
		}
		
		return $this;
	}

	/**
	 * Use the CahierTexteTravailAFaireFichierJoint relation CahierTexteTravailAFaireFichierJoint object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CahierTexteTravailAFaireFichierJointQuery A secondary query class using the current class as primary query
	 */
	public function useCahierTexteTravailAFaireFichierJointQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCahierTexteTravailAFaireFichierJoint($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CahierTexteTravailAFaireFichierJoint', 'CahierTexteTravailAFaireFichierJointQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CahierTexteTravailAFaire $cahierTexteTravailAFaire Object to remove from the list of results
	 *
	 * @return    CahierTexteTravailAFaireQuery The current query, for fluid interface
	 */
	public function prune($cahierTexteTravailAFaire = null)
	{
		if ($cahierTexteTravailAFaire) {
			$this->addUsingAlias(CahierTexteTravailAFairePeer::ID_CT, $cahierTexteTravailAFaire->getIdCt(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCahierTexteTravailAFaireQuery
