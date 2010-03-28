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
 * @method     AidDetailsQuery leftJoinAidConfiguration($relationAlias = '') Adds a LEFT JOIN clause to the query using the AidConfiguration relation
 * @method     AidDetailsQuery rightJoinAidConfiguration($relationAlias = '') Adds a RIGHT JOIN clause to the query using the AidConfiguration relation
 * @method     AidDetailsQuery innerJoinAidConfiguration($relationAlias = '') Adds a INNER JOIN clause to the query using the AidConfiguration relation
 *
 * @method     AidDetailsQuery leftJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a LEFT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     AidDetailsQuery rightJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 * @method     AidDetailsQuery innerJoinJAidUtilisateursProfessionnels($relationAlias = '') Adds a INNER JOIN clause to the query using the JAidUtilisateursProfessionnels relation
 *
 * @method     AidDetailsQuery leftJoinJAidEleves($relationAlias = '') Adds a LEFT JOIN clause to the query using the JAidEleves relation
 * @method     AidDetailsQuery rightJoinJAidEleves($relationAlias = '') Adds a RIGHT JOIN clause to the query using the JAidEleves relation
 * @method     AidDetailsQuery innerJoinJAidEleves($relationAlias = '') Adds a INNER JOIN clause to the query using the JAidEleves relation
 *
 * @method     AidDetailsQuery leftJoinEdtEmplacementCours($relationAlias = '') Adds a LEFT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AidDetailsQuery rightJoinEdtEmplacementCours($relationAlias = '') Adds a RIGHT JOIN clause to the query using the EdtEmplacementCours relation
 * @method     AidDetailsQuery innerJoinEdtEmplacementCours($relationAlias = '') Adds a INNER JOIN clause to the query using the EdtEmplacementCours relation
 *
 * @method     AidDetails findOne(PropelPDO $con = null) Return the first AidDetails matching the query
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
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    AidDetails|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = AidDetailsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @param     string $id The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($id)) {
			return $this->addUsingAlias(AidDetailsPeer::ID, $id, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $id)) {
			return $this->addUsingAlias(AidDetailsPeer::ID, str_replace('*', '%', $id), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::ID, $id, $comparison);
		}
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByNom($nom = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($nom)) {
			return $this->addUsingAlias(AidDetailsPeer::NOM, $nom, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $nom)) {
			return $this->addUsingAlias(AidDetailsPeer::NOM, str_replace('*', '%', $nom), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::NOM, $nom, $comparison);
		}
	}

	/**
	 * Filter the query on the numero column
	 * 
	 * @param     string $numero The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByNumero($numero = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($numero)) {
			return $this->addUsingAlias(AidDetailsPeer::NUMERO, $numero, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $numero)) {
			return $this->addUsingAlias(AidDetailsPeer::NUMERO, str_replace('*', '%', $numero), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::NUMERO, $numero, $comparison);
		}
	}

	/**
	 * Filter the query on the indice_aid column
	 * 
	 * @param     int|array $indiceAid The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByIndiceAid($indiceAid = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($indiceAid)) {
			if (array_values($indiceAid) === $indiceAid) {
				return $this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid, Criteria::IN);
			} else {
				if (isset($indiceAid['min'])) {
					$this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($indiceAid['max'])) {
					$this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AidDetailsPeer::INDICE_AID, $indiceAid, $comparison);
		}
	}

	/**
	 * Filter the query on the perso1 column
	 * 
	 * @param     string $perso1 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso1($perso1 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($perso1)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO1, $perso1, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $perso1)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO1, str_replace('*', '%', $perso1), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PERSO1, $perso1, $comparison);
		}
	}

	/**
	 * Filter the query on the perso2 column
	 * 
	 * @param     string $perso2 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso2($perso2 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($perso2)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO2, $perso2, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $perso2)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO2, str_replace('*', '%', $perso2), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PERSO2, $perso2, $comparison);
		}
	}

	/**
	 * Filter the query on the perso3 column
	 * 
	 * @param     string $perso3 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPerso3($perso3 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($perso3)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO3, $perso3, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $perso3)) {
			return $this->addUsingAlias(AidDetailsPeer::PERSO3, str_replace('*', '%', $perso3), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PERSO3, $perso3, $comparison);
		}
	}

	/**
	 * Filter the query on the productions column
	 * 
	 * @param     string $productions The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByProductions($productions = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($productions)) {
			return $this->addUsingAlias(AidDetailsPeer::PRODUCTIONS, $productions, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $productions)) {
			return $this->addUsingAlias(AidDetailsPeer::PRODUCTIONS, str_replace('*', '%', $productions), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PRODUCTIONS, $productions, $comparison);
		}
	}

	/**
	 * Filter the query on the resume column
	 * 
	 * @param     string $resume The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByResume($resume = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($resume)) {
			return $this->addUsingAlias(AidDetailsPeer::RESUME, $resume, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $resume)) {
			return $this->addUsingAlias(AidDetailsPeer::RESUME, str_replace('*', '%', $resume), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::RESUME, $resume, $comparison);
		}
	}

	/**
	 * Filter the query on the famille column
	 * 
	 * @param     int|array $famille The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByFamille($famille = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($famille)) {
			if (array_values($famille) === $famille) {
				return $this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille, Criteria::IN);
			} else {
				if (isset($famille['min'])) {
					$this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille['min'], Criteria::GREATER_EQUAL);
				}
				if (isset($famille['max'])) {
					$this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille['max'], Criteria::LESS_EQUAL);
				}
				return $this;	
			}
		} else {
			return $this->addUsingAlias(AidDetailsPeer::FAMILLE, $famille, $comparison);
		}
	}

	/**
	 * Filter the query on the mots_cles column
	 * 
	 * @param     string $motsCles The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMotsCles($motsCles = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($motsCles)) {
			return $this->addUsingAlias(AidDetailsPeer::MOTS_CLES, $motsCles, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $motsCles)) {
			return $this->addUsingAlias(AidDetailsPeer::MOTS_CLES, str_replace('*', '%', $motsCles), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::MOTS_CLES, $motsCles, $comparison);
		}
	}

	/**
	 * Filter the query on the adresse1 column
	 * 
	 * @param     string $adresse1 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAdresse1($adresse1 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($adresse1)) {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE1, $adresse1, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $adresse1)) {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE1, str_replace('*', '%', $adresse1), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE1, $adresse1, $comparison);
		}
	}

	/**
	 * Filter the query on the adresse2 column
	 * 
	 * @param     string $adresse2 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAdresse2($adresse2 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($adresse2)) {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE2, $adresse2, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $adresse2)) {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE2, str_replace('*', '%', $adresse2), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::ADRESSE2, $adresse2, $comparison);
		}
	}

	/**
	 * Filter the query on the public_destinataire column
	 * 
	 * @param     string $publicDestinataire The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByPublicDestinataire($publicDestinataire = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($publicDestinataire)) {
			return $this->addUsingAlias(AidDetailsPeer::PUBLIC_DESTINATAIRE, $publicDestinataire, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $publicDestinataire)) {
			return $this->addUsingAlias(AidDetailsPeer::PUBLIC_DESTINATAIRE, str_replace('*', '%', $publicDestinataire), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PUBLIC_DESTINATAIRE, $publicDestinataire, $comparison);
		}
	}

	/**
	 * Filter the query on the contacts column
	 * 
	 * @param     string $contacts The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByContacts($contacts = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($contacts)) {
			return $this->addUsingAlias(AidDetailsPeer::CONTACTS, $contacts, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $contacts)) {
			return $this->addUsingAlias(AidDetailsPeer::CONTACTS, str_replace('*', '%', $contacts), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::CONTACTS, $contacts, $comparison);
		}
	}

	/**
	 * Filter the query on the divers column
	 * 
	 * @param     string $divers The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByDivers($divers = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($divers)) {
			return $this->addUsingAlias(AidDetailsPeer::DIVERS, $divers, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $divers)) {
			return $this->addUsingAlias(AidDetailsPeer::DIVERS, str_replace('*', '%', $divers), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::DIVERS, $divers, $comparison);
		}
	}

	/**
	 * Filter the query on the matiere1 column
	 * 
	 * @param     string $matiere1 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMatiere1($matiere1 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($matiere1)) {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE1, $matiere1, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $matiere1)) {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE1, str_replace('*', '%', $matiere1), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE1, $matiere1, $comparison);
		}
	}

	/**
	 * Filter the query on the matiere2 column
	 * 
	 * @param     string $matiere2 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByMatiere2($matiere2 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($matiere2)) {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE2, $matiere2, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $matiere2)) {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE2, str_replace('*', '%', $matiere2), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::MATIERE2, $matiere2, $comparison);
		}
	}

	/**
	 * Filter the query on the eleve_peut_modifier column
	 * 
	 * @param     string $elevePeutModifier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByElevePeutModifier($elevePeutModifier = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($elevePeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::ELEVE_PEUT_MODIFIER, $elevePeutModifier, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $elevePeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::ELEVE_PEUT_MODIFIER, str_replace('*', '%', $elevePeutModifier), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::ELEVE_PEUT_MODIFIER, $elevePeutModifier, $comparison);
		}
	}

	/**
	 * Filter the query on the prof_peut_modifier column
	 * 
	 * @param     string $profPeutModifier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByProfPeutModifier($profPeutModifier = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($profPeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::PROF_PEUT_MODIFIER, $profPeutModifier, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $profPeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::PROF_PEUT_MODIFIER, str_replace('*', '%', $profPeutModifier), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::PROF_PEUT_MODIFIER, $profPeutModifier, $comparison);
		}
	}

	/**
	 * Filter the query on the cpe_peut_modifier column
	 * 
	 * @param     string $cpePeutModifier The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByCpePeutModifier($cpePeutModifier = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($cpePeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::CPE_PEUT_MODIFIER, $cpePeutModifier, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $cpePeutModifier)) {
			return $this->addUsingAlias(AidDetailsPeer::CPE_PEUT_MODIFIER, str_replace('*', '%', $cpePeutModifier), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::CPE_PEUT_MODIFIER, $cpePeutModifier, $comparison);
		}
	}

	/**
	 * Filter the query on the fiche_publique column
	 * 
	 * @param     string $fichePublique The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByFichePublique($fichePublique = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($fichePublique)) {
			return $this->addUsingAlias(AidDetailsPeer::FICHE_PUBLIQUE, $fichePublique, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $fichePublique)) {
			return $this->addUsingAlias(AidDetailsPeer::FICHE_PUBLIQUE, str_replace('*', '%', $fichePublique), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::FICHE_PUBLIQUE, $fichePublique, $comparison);
		}
	}

	/**
	 * Filter the query on the affiche_adresse1 column
	 * 
	 * @param     string $afficheAdresse1 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAfficheAdresse1($afficheAdresse1 = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($afficheAdresse1)) {
			return $this->addUsingAlias(AidDetailsPeer::AFFICHE_ADRESSE1, $afficheAdresse1, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $afficheAdresse1)) {
			return $this->addUsingAlias(AidDetailsPeer::AFFICHE_ADRESSE1, str_replace('*', '%', $afficheAdresse1), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::AFFICHE_ADRESSE1, $afficheAdresse1, $comparison);
		}
	}

	/**
	 * Filter the query on the en_construction column
	 * 
	 * @param     string $enConstruction The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByEnConstruction($enConstruction = null, $comparison = Criteria::EQUAL)
	{
		if (is_array($enConstruction)) {
			return $this->addUsingAlias(AidDetailsPeer::EN_CONSTRUCTION, $enConstruction, Criteria::IN);
		} elseif(preg_match('/[\%\*]/', $enConstruction)) {
			return $this->addUsingAlias(AidDetailsPeer::EN_CONSTRUCTION, str_replace('*', '%', $enConstruction), Criteria::LIKE);
		} else {
			return $this->addUsingAlias(AidDetailsPeer::EN_CONSTRUCTION, $enConstruction, $comparison);
		}
	}

	/**
	 * Filter the query by a related AidConfiguration object
	 *
	 * @param     AidConfiguration $aidConfiguration  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByAidConfiguration($aidConfiguration, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AidDetailsPeer::INDICE_AID, $aidConfiguration->getIndiceAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AidConfiguration relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinAidConfiguration($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('AidConfiguration');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useAidConfigurationQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
	public function filterByJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AidDetailsPeer::ID, $jAidUtilisateursProfessionnels->getIdAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JAidUtilisateursProfessionnels relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
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
	 * Filter the query by a related JAidEleves object
	 *
	 * @param     JAidEleves $jAidEleves  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByJAidEleves($jAidEleves, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AidDetailsPeer::ID, $jAidEleves->getIdAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JAidEleves relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function joinJAidEleves($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JAidEleves');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		
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
	public function useJAidElevesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJAidEleves($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JAidEleves', 'JAidElevesQuery');
	}

	/**
	 * Filter the query by a related EdtEmplacementCours object
	 *
	 * @param     EdtEmplacementCours $edtEmplacementCours  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
	 */
	public function filterByEdtEmplacementCours($edtEmplacementCours, $comparison = Criteria::EQUAL)
	{
		return $this
			->addUsingAlias(AidDetailsPeer::ID, $edtEmplacementCours->getIdAid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EdtEmplacementCours relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    AidDetailsQuery The current query, for fluid interface
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

} // BaseAidDetailsQuery
