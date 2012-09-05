<?php


/**
 * Base class that represents a query for the 'aid' table.
 *
 * Liste des AID (Activites Inter-Disciplinaires)
 *
 * @method     AidDetailsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AidDetailsQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     AidDetailsQuery orderByNumero($order = Criteria::ASC) Order by the numero column
 * @method     AidDetailsQuery orderByIndiceAid($order = Criteria::ASC) Order by the indice_aid column
 * @method     AidDetailsQuery orderByPerso1($order = Criteria::ASC) Order by the perso1 column
 * @method     AidDetailsQuery orderByPerso2($order = Criteria::ASC) Order by the perso2 column
 * @method     AidDetailsQuery orderByPerso3($order = Criteria::ASC) Order by the perso3 column
 * @method     AidDetailsQuery orderByProductions($order = Criteria::ASC) Order by the productions column
 * @method     AidDetailsQuery orderByResume($order = Criteria::ASC) Order by the resume column
 * @method     AidDetailsQuery orderByFamille($order = Criteria::ASC) Order by the famille column
 * @method     AidDetailsQuery orderByMotsCles($order = Criteria::ASC) Order by the mots_cles column
 * @method     AidDetailsQuery orderByAdresse1($order = Criteria::ASC) Order by the adresse1 column
 * @method     AidDetailsQuery orderByAdresse2($order = Criteria::ASC) Order by the adresse2 column
 * @method     AidDetailsQuery orderByPublicDestinataire($order = Criteria::ASC) Order by the public_destinataire column
 * @method     AidDetailsQuery orderByContacts($order = Criteria::ASC) Order by the contacts column
 * @method     AidDetailsQuery orderByDivers($order = Criteria::ASC) Order by the divers column
 * @method     AidDetailsQuery orderByMatiere1($order = Criteria::ASC) Order by the matiere1 column
 * @method     AidDetailsQuery orderByMatiere2($order = Criteria::ASC) Order by the matiere2 column
 * @method     AidDetailsQuery orderByElevePeutModifier($order = Criteria::ASC) Order by the eleve_peut_modifier column
 * @method     AidDetailsQuery orderByProfPeutModifier($order = Criteria::ASC) Order by the prof_peut_modifier column
 * @method     AidDetailsQuery orderByCpePeutModifier($order = Criteria::ASC) Order by the cpe_peut_modifier column
 * @method     AidDetailsQuery orderByFichePublique($order = Criteria::ASC) Order by the fiche_publique column
 * @method     AidDetailsQuery orderByAfficheAdresse1($order = Criteria::ASC) Order by the affiche_adresse1 column
 * @method     AidDetailsQuery orderByEnConstruction($order = Criteria::ASC) Order by the en_construction column
 *
 * @method     AidDetailsQuery groupById() Group by the id column
 * @method     AidDetailsQuery groupByNom() Group by the nom column
 * @method     AidDetailsQuery groupByNumero() Group by the numero column
 * @method     AidDetailsQuery groupByIndiceAid() Group by the indice_aid column
 * @method     AidDetailsQuery groupByPerso1() Group by the perso1 column
 * @method     AidDetailsQuery groupByPerso2() Group by the perso2 column
 * @method     AidDetailsQuery groupByPerso3() Group by the perso3 column
 * @method     AidDetailsQuery groupByProductions() Group by the productions column
 * @method     AidDetailsQuery groupByResume() Group by the resume column
 * @method     AidDetailsQuery groupByFamille() Group by the famille column
 * @method     AidDetailsQuery groupByMotsCles() Group by the mots_cles column
 * @method     AidDetailsQuery groupByAdresse1() Group by the adresse1 column
 * @method     AidDetailsQuery groupByAdresse2() Group by the adresse2 column
 * @method     AidDetailsQuery groupByPublicDestinataire() Group by the public_destinataire column
 * @method     AidDetailsQuery groupByContacts() Group by the contacts column
 * @method     AidDetailsQuery groupByDivers() Group by the divers column
 * @method     AidDetailsQuery groupByMatiere1() Group by the matiere1 column
 * @method     AidDetailsQuery groupByMatiere2() Group by the matiere2 column
 * @method     AidDetailsQuery groupByElevePeutModifier() Group by the eleve_peut_modifier column
 * @method     AidDetailsQuery groupByProfPeutModifier() Group by the prof_peut_modifier column
 * @method     AidDetailsQuery groupByCpePeutModifier() Group by the cpe_peut_modifier column
 * @method     AidDetailsQuery groupByFichePublique() Group by the fiche_publique column
 * @method     AidDetailsQuery groupByAfficheAdresse1() Group by the affiche_adresse1 column
 * @method     AidDetailsQuery groupByEnConstruction() Group by the en_construction column
 *
 * @method     AidDetailsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AidDetailsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AidDetailsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     AidDetailsQuery leftJoinAidConfiguration($relationAlias = null) Adds a LEFT JOIN clause to the query using the AidConfiguration relation
 * @method     AidDetailsQuery rightJoinAidConfiguration($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AidConfiguration relation
 * @method     AidDetailsQuery innerJoinAidConfiguration($relationAlias = null) Adds a INNER JOIN clause to the query using the AidConfiguration relation
 *
 * @method     AidDetailsQuery leftJoinJAidUtilisateursProfessionnels($relationAlias = null) Adds a LEFT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     AidDetailsQuery rightJoinJAidUtilisateursProfessionnels($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     AidDetailsQuery innerJoinJAidUtilisateursProfessionnels($relationAlias = null) Adds a INNER JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 *
 * @method     AidDetailsQuery leftJoinJAidEleves($relationAlias = null) Adds a LEFT JOIN clause to the query using the JAidEleves relation
 * @method     AidDetailsQuery rightJoinJAidEleves($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JAidEleves relation
 * @method     AidDetailsQuery innerJoinJAidEleves($relationAlias = null) Adds a INNER JOIN clause to the query using the JAidEleves relation
 *
 * @method     AidDetailsQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AidDetailsQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     AidDetailsQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     AidDetailsQuery leftJoinEdtEmplacementCours($relationAlias = null) Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AidDetailsQuery rightJoinEdtEmplacementCours($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AidDetailsQuery innerJoinEdtEmplacementCours($relationAlias = null) Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     AidDetails findOne(PropelPDO $con = null) Return the first AidDetails matching the query
 * @method     AidDetails findOneOrCreate(PropelPDO $con = null) Return the first AidDetails matching the query, or a new AidDetails object populated from the query conditions when no match is found
 *
 * @method     AidDetails findOneById(string $id) Return the first AidDetails filtered by the id column
 * @method     AidDetails findOneByNom(string $nom) Return the first AidDetails filtered by the nom column
 * @method     AidDetails findOneByNumero(string $numero) Return the first AidDetails filtered by the numero column
 * @method     AidDetails findOneByIndiceAid(int $indice_aid) Return the first AidDetails filtered by the indice_aid column
 * @method     AidDetails findOneByPerso1(string $perso1) Return the first AidDetails filtered by the perso1 column
 * @method     AidDetails findOneByPerso2(string $perso2) Return the first AidDetails filtered by the perso2 column
 * @method     AidDetails findOneByPerso3(string $perso3) Return the first AidDetails filtered by the perso3 column
 * @method     AidDetails findOneByProductions(string $productions) Return the first AidDetails filtered by the productions column
 * @method     AidDetails findOneByResume(string $resume) Return the first AidDetails filtered by the resume column
 * @method     AidDetails findOneByFamille(int $famille) Return the first AidDetails filtered by the famille column
 * @method     AidDetails findOneByMotsCles(string $mots_cles) Return the first AidDetails filtered by the mots_cles column
 * @method     AidDetails findOneByAdresse1(string $adresse1) Return the first AidDetails filtered by the adresse1 column
 * @method     AidDetails findOneByAdresse2(string $adresse2) Return the first AidDetails filtered by the adresse2 column
 * @method     AidDetails findOneByPublicDestinataire(string $public_destinataire) Return the first AidDetails filtered by the public_destinataire column
 * @method     AidDetails findOneByContacts(string $contacts) Return the first AidDetails filtered by the contacts column
 * @method     AidDetails findOneByDivers(string $divers) Return the first AidDetails filtered by the divers column
 * @method     AidDetails findOneByMatiere1(string $matiere1) Return the first AidDetails filtered by the matiere1 column
 * @method     AidDetails findOneByMatiere2(string $matiere2) Return the first AidDetails filtered by the matiere2 column
 * @method     AidDetails findOneByElevePeutModifier(string $eleve_peut_modifier) Return the first AidDetails filtered by the eleve_peut_modifier column
 * @method     AidDetails findOneByProfPeutModifier(string $prof_peut_modifier) Return the first AidDetails filtered by the prof_peut_modifier column
 * @method     AidDetails findOneByCpePeutModifier(string $cpe_peut_modifier) Return the first AidDetails filtered by the cpe_peut_modifier column
 * @method     AidDetails findOneByFichePublique(string $fiche_publique) Return the first AidDetails filtered by the fiche_publique column
 * @method     AidDetails findOneByAfficheAdresse1(string $affiche_adresse1) Return the first AidDetails filtered by the affiche_adresse1 column
 * @method     AidDetails findOneByEnConstruction(string $en_construction) Return the first AidDetails filtered by the en_construction column
 *
 * @method     array findById(string $id) Return AidDetails objects filtered by the id column
 * @method     array findByNom(string $nom) Return AidDetails objects filtered by the nom column
 * @method     array findByNumero(string $numero) Return AidDetails objects filtered by the numero column
 * @method     array findByIndiceAid(int $indice_aid) Return AidDetails objects filtered by the indice_aid column
 * @method     array findByPerso1(string $perso1) Return AidDetails objects filtered by the perso1 column
 * @method     array findByPerso2(string $perso2) Return AidDetails objects filtered by the perso2 column
 * @method     array findByPerso3(string $perso3) Return AidDetails objects filtered by the perso3 column
 * @method     array findByProductions(string $productions) Return AidDetails objects filtered by the productions column
 * @method     array findByResume(string $resume) Return AidDetails objects filtered by the resume column
 * @method     array findByFamille(int $famille) Return AidDetails objects filtered by the famille column
 * @method     array findByMotsCles(string $mots_cles) Return AidDetails objects filtered by the mots_cles column
 * @method     array findByAdresse1(string $adresse1) Return AidDetails objects filtered by the adresse1 column
 * @method     array findByAdresse2(string $adresse2) Return AidDetails objects filtered by the adresse2 column
 * @method     array findByPublicDestinataire(string $public_destinataire) Return AidDetails objects filtered by the public_destinataire column
 * @method     array findByContacts(string $contacts) Return AidDetails objects filtered by the contacts column
 * @method     array findByDivers(string $divers) Return AidDetails objects filtered by the divers column
 * @method     array findByMatiere1(string $matiere1) Return AidDetails objects filtered by the matiere1 column
 * @method     array findByMatiere2(string $matiere2) Return AidDetails objects filtered by the matiere2 column
 * @method     array findByElevePeutModifier(string $eleve_peut_modifier) Return AidDetails objects filtered by the eleve_peut_modifier column
 * @method     array findByProfPeutModifier(string $prof_peut_modifier) Return AidDetails objects filtered by the prof_peut_modifier column
 * @method     array findByCpePeutModifier(string $cpe_peut_modifier) Return AidDetails objects filtered by the cpe_peut_modifier column
 * @method     array findByFichePublique(string $fiche_publique) Return AidDetails objects filtered by the fiche_publique column
 * @method     array findByAfficheAdresse1(string $affiche_adresse1) Return AidDetails objects filtered by the affiche_adresse1 column
 * @method     array findByEnConstruction(string $en_construction) Return AidDetails objects filtered by the en_construction column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAidDetailsQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseAidDetailsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'AidDetails', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new AidDetailsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    AidDetailsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof AidDetailsQuery) {
			return $criteria;
		}
		$query = new AidDetailsQuery();
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
	 * @return    AidDetails|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = AidDetailsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    AidDetails A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, NOM, NUMERO, INDICE_AID, PERSO1, PERSO2, PERSO3, PRODUCTIONS, RESUME, FAMILLE, MOTS_CLES, ADRESSE1, ADRESSE2, PUBLIC_DESTINATAIRE, CONTACTS, DIVERS, MATIERE1, MATIERE2, ELEVE_PEUT_MODIFIER, PROF_PEUT_MODIFIER, CPE_PEUT_MODIFIER, FICHE_PUBLIQUE, AFFICHE_ADRESSE1, EN_CONSTRUCTION FROM aid WHERE ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new AidDetails();
			$obj->hydrate($row);
			AidDetailsPeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    AidDetails|array|mixed the result, formatted by the current formatter
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
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(AidDetailsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(AidDetailsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById('fooValue');   // WHERE id = 'fooValue'
	 * $query->filterById('%fooValue%'); // WHERE id LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $id The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($id)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $id)) {
				$id = str_replace('*', '%', $id);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNom('fooValue');   // WHERE nom = 'fooValue'
	 * $query->filterByNom('%fooValue%'); // WHERE nom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(AidDetailsPeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the numero column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNumero('fooValue');   // WHERE numero = 'fooValue'
	 * $query->filterByNumero('%fooValue%'); // WHERE numero LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $numero The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByNumero($numero = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($numero)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $numero)) {
				$numero = str_replace('*', '%', $numero);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::NUMERO, $numero, $comparison);
	}

	/**
	 * Filter the query on the indice_aid column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByIndiceAid(1234); // WHERE indice_aid = 1234
	 * $query->filterByIndiceAid(array(12, 34)); // WHERE indice_aid IN (12, 34)
	 * $query->filterByIndiceAid(array('min' => 12)); // WHERE indice_aid > 12
	 * </code>
	 *
	 * @see       filterByAidConfiguration()
	 *
	 * @param     mixed $indiceAid The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByIndiceAid($indiceAid = null, $comparison = null)
	{
		if (is_array($indiceAid)) {
			$useMinMax = false;
			if (isset($indiceAid['min'])) {
				$this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($indiceAid['max'])) {
				$this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid, $comparison);
	}

	/**
	 * Filter the query on the perso1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPerso1('fooValue');   // WHERE perso1 = 'fooValue'
	 * $query->filterByPerso1('%fooValue%'); // WHERE perso1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $perso1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso1($perso1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($perso1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $perso1)) {
				$perso1 = str_replace('*', '%', $perso1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PERSO1, $perso1, $comparison);
	}

	/**
	 * Filter the query on the perso2 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPerso2('fooValue');   // WHERE perso2 = 'fooValue'
	 * $query->filterByPerso2('%fooValue%'); // WHERE perso2 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $perso2 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso2($perso2 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($perso2)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $perso2)) {
				$perso2 = str_replace('*', '%', $perso2);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PERSO2, $perso2, $comparison);
	}

	/**
	 * Filter the query on the perso3 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPerso3('fooValue');   // WHERE perso3 = 'fooValue'
	 * $query->filterByPerso3('%fooValue%'); // WHERE perso3 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $perso3 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso3($perso3 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($perso3)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $perso3)) {
				$perso3 = str_replace('*', '%', $perso3);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PERSO3, $perso3, $comparison);
	}

	/**
	 * Filter the query on the productions column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByProductions('fooValue');   // WHERE productions = 'fooValue'
	 * $query->filterByProductions('%fooValue%'); // WHERE productions LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $productions The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByProductions($productions = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($productions)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $productions)) {
				$productions = str_replace('*', '%', $productions);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PRODUCTIONS, $productions, $comparison);
	}

	/**
	 * Filter the query on the resume column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByResume('fooValue');   // WHERE resume = 'fooValue'
	 * $query->filterByResume('%fooValue%'); // WHERE resume LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $resume The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByResume($resume = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($resume)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $resume)) {
				$resume = str_replace('*', '%', $resume);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::RESUME, $resume, $comparison);
	}

	/**
	 * Filter the query on the famille column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFamille(1234); // WHERE famille = 1234
	 * $query->filterByFamille(array(12, 34)); // WHERE famille IN (12, 34)
	 * $query->filterByFamille(array('min' => 12)); // WHERE famille > 12
	 * </code>
	 *
	 * @param     mixed $famille The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByFamille($famille = null, $comparison = null)
	{
		if (is_array($famille)) {
			$useMinMax = false;
			if (isset($famille['min'])) {
				$this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($famille['max'])) {
				$this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille, $comparison);
	}

	/**
	 * Filter the query on the mots_cles column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMotsCles('fooValue');   // WHERE mots_cles = 'fooValue'
	 * $query->filterByMotsCles('%fooValue%'); // WHERE mots_cles LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $motsCles The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMotsCles($motsCles = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($motsCles)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $motsCles)) {
				$motsCles = str_replace('*', '%', $motsCles);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::MOTS_CLES, $motsCles, $comparison);
	}

	/**
	 * Filter the query on the adresse1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdresse1('fooValue');   // WHERE adresse1 = 'fooValue'
	 * $query->filterByAdresse1('%fooValue%'); // WHERE adresse1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adresse1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAdresse1($adresse1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adresse1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adresse1)) {
				$adresse1 = str_replace('*', '%', $adresse1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::ADRESSE1, $adresse1, $comparison);
	}

	/**
	 * Filter the query on the adresse2 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAdresse2('fooValue');   // WHERE adresse2 = 'fooValue'
	 * $query->filterByAdresse2('%fooValue%'); // WHERE adresse2 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $adresse2 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAdresse2($adresse2 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($adresse2)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $adresse2)) {
				$adresse2 = str_replace('*', '%', $adresse2);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::ADRESSE2, $adresse2, $comparison);
	}

	/**
	 * Filter the query on the public_destinataire column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByPublicDestinataire('fooValue');   // WHERE public_destinataire = 'fooValue'
	 * $query->filterByPublicDestinataire('%fooValue%'); // WHERE public_destinataire LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $publicDestinataire The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPublicDestinataire($publicDestinataire = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($publicDestinataire)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $publicDestinataire)) {
				$publicDestinataire = str_replace('*', '%', $publicDestinataire);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PUBLIC_DESTINATAIRE, $publicDestinataire, $comparison);
	}

	/**
	 * Filter the query on the contacts column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByContacts('fooValue');   // WHERE contacts = 'fooValue'
	 * $query->filterByContacts('%fooValue%'); // WHERE contacts LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $contacts The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByContacts($contacts = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($contacts)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $contacts)) {
				$contacts = str_replace('*', '%', $contacts);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::CONTACTS, $contacts, $comparison);
	}

	/**
	 * Filter the query on the divers column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDivers('fooValue');   // WHERE divers = 'fooValue'
	 * $query->filterByDivers('%fooValue%'); // WHERE divers LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $divers The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByDivers($divers = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($divers)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $divers)) {
				$divers = str_replace('*', '%', $divers);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::DIVERS, $divers, $comparison);
	}

	/**
	 * Filter the query on the matiere1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMatiere1('fooValue');   // WHERE matiere1 = 'fooValue'
	 * $query->filterByMatiere1('%fooValue%'); // WHERE matiere1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiere1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMatiere1($matiere1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiere1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiere1)) {
				$matiere1 = str_replace('*', '%', $matiere1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::MATIERE1, $matiere1, $comparison);
	}

	/**
	 * Filter the query on the matiere2 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMatiere2('fooValue');   // WHERE matiere2 = 'fooValue'
	 * $query->filterByMatiere2('%fooValue%'); // WHERE matiere2 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $matiere2 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMatiere2($matiere2 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($matiere2)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $matiere2)) {
				$matiere2 = str_replace('*', '%', $matiere2);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::MATIERE2, $matiere2, $comparison);
	}

	/**
	 * Filter the query on the eleve_peut_modifier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByElevePeutModifier('fooValue');   // WHERE eleve_peut_modifier = 'fooValue'
	 * $query->filterByElevePeutModifier('%fooValue%'); // WHERE eleve_peut_modifier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $elevePeutModifier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByElevePeutModifier($elevePeutModifier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($elevePeutModifier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $elevePeutModifier)) {
				$elevePeutModifier = str_replace('*', '%', $elevePeutModifier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::ELEVE_PEUT_MODIFIER, $elevePeutModifier, $comparison);
	}

	/**
	 * Filter the query on the prof_peut_modifier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByProfPeutModifier('fooValue');   // WHERE prof_peut_modifier = 'fooValue'
	 * $query->filterByProfPeutModifier('%fooValue%'); // WHERE prof_peut_modifier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $profPeutModifier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByProfPeutModifier($profPeutModifier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($profPeutModifier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $profPeutModifier)) {
				$profPeutModifier = str_replace('*', '%', $profPeutModifier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::PROF_PEUT_MODIFIER, $profPeutModifier, $comparison);
	}

	/**
	 * Filter the query on the cpe_peut_modifier column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByCpePeutModifier('fooValue');   // WHERE cpe_peut_modifier = 'fooValue'
	 * $query->filterByCpePeutModifier('%fooValue%'); // WHERE cpe_peut_modifier LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $cpePeutModifier The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByCpePeutModifier($cpePeutModifier = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($cpePeutModifier)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $cpePeutModifier)) {
				$cpePeutModifier = str_replace('*', '%', $cpePeutModifier);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::CPE_PEUT_MODIFIER, $cpePeutModifier, $comparison);
	}

	/**
	 * Filter the query on the fiche_publique column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFichePublique('fooValue');   // WHERE fiche_publique = 'fooValue'
	 * $query->filterByFichePublique('%fooValue%'); // WHERE fiche_publique LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $fichePublique The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByFichePublique($fichePublique = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($fichePublique)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $fichePublique)) {
				$fichePublique = str_replace('*', '%', $fichePublique);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::FICHE_PUBLIQUE, $fichePublique, $comparison);
	}

	/**
	 * Filter the query on the affiche_adresse1 column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByAfficheAdresse1('fooValue');   // WHERE affiche_adresse1 = 'fooValue'
	 * $query->filterByAfficheAdresse1('%fooValue%'); // WHERE affiche_adresse1 LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $afficheAdresse1 The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAfficheAdresse1($afficheAdresse1 = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($afficheAdresse1)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $afficheAdresse1)) {
				$afficheAdresse1 = str_replace('*', '%', $afficheAdresse1);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::AFFICHE_ADRESSE1, $afficheAdresse1, $comparison);
	}

	/**
	 * Filter the query on the en_construction column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEnConstruction('fooValue');   // WHERE en_construction = 'fooValue'
	 * $query->filterByEnConstruction('%fooValue%'); // WHERE en_construction LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $enConstruction The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByEnConstruction($enConstruction = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($enConstruction)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $enConstruction)) {
				$enConstruction = str_replace('*', '%', $enConstruction);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(AidDetailsPeer::EN_CONSTRUCTION, $enConstruction, $comparison);
	}

	/**
	 * Filter the query by a related AidConfiguration object
	 *
	 * @param     AidConfiguration|PropelCollection $aidConfiguration The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAidConfiguration($aidConfiguration, $comparison = null)
	{
		if ($aidConfiguration instanceof AidConfiguration) {
			return $this
				->addUsingAlias(AidDetailsPeer::INDICE_AID, $aidConfiguration->getIndiceAid(), $comparison);
		} elseif ($aidConfiguration instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(AidDetailsPeer::INDICE_AID, $aidConfiguration->toKeyValue('PrimaryKey', 'IndiceAid'), $comparison);
		} else {
			throw new PropelException('filterByAidConfiguration() only accepts arguments of type AidConfiguration or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the AidConfiguration relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinAidConfiguration($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidConfiguration');

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
			$this->addJoinObject($join, 'AidConfiguration');
		}

		return $this;
	}

	/**
	 * Use the AidConfiguration relation AidConfiguration object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidConfigurationQuery A secondary query class using the current class as primary query
	 */
	public function useAidConfigurationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinAidConfiguration($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AidConfiguration', 'AidConfigurationQuery');
	}

	/**
	 * Filter the query by a related JAidUtilisateursProfessionnels object
	 *
	 * @param     JAidUtilisateursProfessionnels $jAidUtilisateursProfessionnels  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels, $comparison = null)
	{
		if ($jAidUtilisateursProfessionnels instanceof JAidUtilisateursProfessionnels) {
			return $this
				->addUsingAlias(AidDetailsPeer::ID, $jAidUtilisateursProfessionnels->getIdAid(), $comparison);
		} elseif ($jAidUtilisateursProfessionnels instanceof PropelCollection) {
			return $this
				->useJAidUtilisateursProfessionnelsQuery()
				->filterByPrimaryKeys($jAidUtilisateursProfessionnels->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJAidUtilisateursProfessionnels() only accepts arguments of type JAidUtilisateursProfessionnels or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JAidUtilisateursProfessionnels relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinJAidUtilisateursProfessionnels($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JAidUtilisateursProfessionnels');

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
	public function useJAidUtilisateursProfessionnelsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJAidUtilisateursProfessionnels($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JAidUtilisateursProfessionnels', 'JAidUtilisateursProfessionnelsQuery');
	}

	/**
	 * Filter the query by a related JAidEleves object
	 *
	 * @param     JAidEleves $jAidEleves  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByJAidEleves($jAidEleves, $comparison = null)
	{
		if ($jAidEleves instanceof JAidEleves) {
			return $this
				->addUsingAlias(AidDetailsPeer::ID, $jAidEleves->getIdAid(), $comparison);
		} elseif ($jAidEleves instanceof PropelCollection) {
			return $this
				->useJAidElevesQuery()
				->filterByPrimaryKeys($jAidEleves->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJAidEleves() only accepts arguments of type JAidEleves or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JAidEleves relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinJAidEleves($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JAidEleves');

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
			$this->addJoinObject($join, 'JAidEleves');
		}

		return $this;
	}

	/**
	 * Use the JAidEleves relation JAidEleves object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JAidElevesQuery A secondary query class using the current class as primary query
	 */
	public function useJAidElevesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJAidEleves($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JAidEleves', 'JAidElevesQuery');
	}

	/**
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		if ($absenceEleveSaisie instanceof AbsenceEleveSaisie) {
			return $this
				->addUsingAlias(AidDetailsPeer::ID, $absenceEleveSaisie->getIdAid(), $comparison);
		} elseif ($absenceEleveSaisie instanceof PropelCollection) {
			return $this
				->useAbsenceEleveSaisieQuery()
				->filterByPrimaryKeys($absenceEleveSaisie->getPrimaryKeys())
				->endUse();
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
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinAbsenceEleveSaisie($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
	public function useAbsenceEleveSaisieQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinAbsenceEleveSaisie($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'AbsenceEleveSaisie', 'AbsenceEleveSaisieQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = null)
	{
		if ($edtEmplacementCours instanceof EdtEmplacementCours) {
			return $this
				->addUsingAlias(AidDetailsPeer::ID, $edtEmplacementCours->getIdAid(), $comparison);
		} elseif ($edtEmplacementCours instanceof PropelCollection) {
			return $this
				->useEdtEmplacementCoursQuery()
				->filterByPrimaryKeys($edtEmplacementCours->getPrimaryKeys())
				->endUse();
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
	 * @return    AidDetailsQuery The current query, for fluid interface
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
	 * Filter the query by a related UtilisateurProfessionnel object
	 * using the j_aid_utilisateurs table as cross reference
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJAidUtilisateursProfessionnelsQuery()
			->filterByUtilisateurProfessionnel($utilisateurProfessionnel, $comparison)
			->endUse();
	}

	/**
	 * Filter the query by a related Eleve object
	 * using the j_aid_eleves table as cross reference
	 *
	 * @param     Eleve $eleve the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByEleve($eleve, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJAidElevesQuery()
			->filterByEleve($eleve, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     AidDetails $aidDetails Object to remove from the list of results
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function prune($aidDetails = null)
	{
		if ($aidDetails) {
			$this->addUsingAlias(AidDetailsPeer::ID, $aidDetails->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseAidDetailsQuery