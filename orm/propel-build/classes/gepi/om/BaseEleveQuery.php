<?php


/**
 * Base class that represents a query for the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * @method     EleveQuery orderByNoGep($order = Criteria::ASC) Order by the no_gep column
 * @method     EleveQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     EleveQuery orderByNom($order = Criteria::ASC) Order by the nom column
 * @method     EleveQuery orderByPrenom($order = Criteria::ASC) Order by the prenom column
 * @method     EleveQuery orderBySexe($order = Criteria::ASC) Order by the sexe column
 * @method     EleveQuery orderByNaissance($order = Criteria::ASC) Order by the naissance column
 * @method     EleveQuery orderByLieuNaissance($order = Criteria::ASC) Order by the lieu_naissance column
 * @method     EleveQuery orderByElenoet($order = Criteria::ASC) Order by the elenoet column
 * @method     EleveQuery orderByEreno($order = Criteria::ASC) Order by the ereno column
 * @method     EleveQuery orderByEleId($order = Criteria::ASC) Order by the ele_id column
 * @method     EleveQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     EleveQuery orderByIdEleve($order = Criteria::ASC) Order by the id_eleve column
 * @method     EleveQuery orderByDateSortie($order = Criteria::ASC) Order by the date_sortie column
 * @method     EleveQuery orderByIdMef($order = Criteria::ASC) Order by the id_mef column
 *
 * @method     EleveQuery groupByNoGep() Group by the no_gep column
 * @method     EleveQuery groupByLogin() Group by the login column
 * @method     EleveQuery groupByNom() Group by the nom column
 * @method     EleveQuery groupByPrenom() Group by the prenom column
 * @method     EleveQuery groupBySexe() Group by the sexe column
 * @method     EleveQuery groupByNaissance() Group by the naissance column
 * @method     EleveQuery groupByLieuNaissance() Group by the lieu_naissance column
 * @method     EleveQuery groupByElenoet() Group by the elenoet column
 * @method     EleveQuery groupByEreno() Group by the ereno column
 * @method     EleveQuery groupByEleId() Group by the ele_id column
 * @method     EleveQuery groupByEmail() Group by the email column
 * @method     EleveQuery groupByIdEleve() Group by the id_eleve column
 * @method     EleveQuery groupByDateSortie() Group by the date_sortie column
 * @method     EleveQuery groupByIdMef() Group by the id_mef column
 *
 * @method     EleveQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     EleveQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     EleveQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     EleveQuery leftJoinMef($relationAlias = null) Adds a LEFT JOIN clause to the query using the Mef relation
 * @method     EleveQuery rightJoinMef($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Mef relation
 * @method     EleveQuery innerJoinMef($relationAlias = null) Adds a INNER JOIN clause to the query using the Mef relation
 *
 * @method     EleveQuery leftJoinJEleveClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveClasse relation
 * @method     EleveQuery rightJoinJEleveClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveClasse relation
 * @method     EleveQuery innerJoinJEleveClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveClasse relation
 *
 * @method     EleveQuery leftJoinJEleveCpe($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveCpe relation
 * @method     EleveQuery rightJoinJEleveCpe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveCpe relation
 * @method     EleveQuery innerJoinJEleveCpe($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveCpe relation
 *
 * @method     EleveQuery leftJoinJEleveGroupe($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveGroupe relation
 * @method     EleveQuery rightJoinJEleveGroupe($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveGroupe relation
 * @method     EleveQuery innerJoinJEleveGroupe($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveGroupe relation
 *
 * @method     EleveQuery leftJoinJEleveProfesseurPrincipal($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveProfesseurPrincipal relation
 * @method     EleveQuery rightJoinJEleveProfesseurPrincipal($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveProfesseurPrincipal relation
 * @method     EleveQuery innerJoinJEleveProfesseurPrincipal($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveProfesseurPrincipal relation
 *
 * @method     EleveQuery leftJoinEleveRegimeDoublant($relationAlias = null) Adds a LEFT JOIN clause to the query using the EleveRegimeDoublant relation
 * @method     EleveQuery rightJoinEleveRegimeDoublant($relationAlias = null) Adds a RIGHT JOIN clause to the query using the EleveRegimeDoublant relation
 * @method     EleveQuery innerJoinEleveRegimeDoublant($relationAlias = null) Adds a INNER JOIN clause to the query using the EleveRegimeDoublant relation
 *
 * @method     EleveQuery leftJoinResponsableInformation($relationAlias = null) Adds a LEFT JOIN clause to the query using the ResponsableInformation relation
 * @method     EleveQuery rightJoinResponsableInformation($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ResponsableInformation relation
 * @method     EleveQuery innerJoinResponsableInformation($relationAlias = null) Adds a INNER JOIN clause to the query using the ResponsableInformation relation
 *
 * @method     EleveQuery leftJoinJEleveAncienEtablissement($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveAncienEtablissement relation
 * @method     EleveQuery rightJoinJEleveAncienEtablissement($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveAncienEtablissement relation
 * @method     EleveQuery innerJoinJEleveAncienEtablissement($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveAncienEtablissement relation
 *
 * @method     EleveQuery leftJoinJAidEleves($relationAlias = null) Adds a LEFT JOIN clause to the query using the JAidEleves relation
 * @method     EleveQuery rightJoinJAidEleves($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JAidEleves relation
 * @method     EleveQuery innerJoinJAidEleves($relationAlias = null) Adds a INNER JOIN clause to the query using the JAidEleves relation
 *
 * @method     EleveQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EleveQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     EleveQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     EleveQuery leftJoinCreditEcts($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditEcts relation
 * @method     EleveQuery rightJoinCreditEcts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditEcts relation
 * @method     EleveQuery innerJoinCreditEcts($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditEcts relation
 *
 * @method     EleveQuery leftJoinCreditEctsGlobal($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditEctsGlobal relation
 * @method     EleveQuery rightJoinCreditEctsGlobal($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditEctsGlobal relation
 * @method     EleveQuery innerJoinCreditEctsGlobal($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditEctsGlobal relation
 *
 * @method     EleveQuery leftJoinArchiveEcts($relationAlias = null) Adds a LEFT JOIN clause to the query using the ArchiveEcts relation
 * @method     EleveQuery rightJoinArchiveEcts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ArchiveEcts relation
 * @method     EleveQuery innerJoinArchiveEcts($relationAlias = null) Adds a INNER JOIN clause to the query using the ArchiveEcts relation
 *
 * @method     Eleve findOne(PropelPDO $con = null) Return the first Eleve matching the query
 * @method     Eleve findOneOrCreate(PropelPDO $con = null) Return the first Eleve matching the query, or a new Eleve object populated from the query conditions when no match is found
 *
 * @method     Eleve findOneByNoGep(string $no_gep) Return the first Eleve filtered by the no_gep column
 * @method     Eleve findOneByLogin(string $login) Return the first Eleve filtered by the login column
 * @method     Eleve findOneByNom(string $nom) Return the first Eleve filtered by the nom column
 * @method     Eleve findOneByPrenom(string $prenom) Return the first Eleve filtered by the prenom column
 * @method     Eleve findOneBySexe(string $sexe) Return the first Eleve filtered by the sexe column
 * @method     Eleve findOneByNaissance(string $naissance) Return the first Eleve filtered by the naissance column
 * @method     Eleve findOneByLieuNaissance(string $lieu_naissance) Return the first Eleve filtered by the lieu_naissance column
 * @method     Eleve findOneByElenoet(string $elenoet) Return the first Eleve filtered by the elenoet column
 * @method     Eleve findOneByEreno(string $ereno) Return the first Eleve filtered by the ereno column
 * @method     Eleve findOneByEleId(string $ele_id) Return the first Eleve filtered by the ele_id column
 * @method     Eleve findOneByEmail(string $email) Return the first Eleve filtered by the email column
 * @method     Eleve findOneByIdEleve(int $id_eleve) Return the first Eleve filtered by the id_eleve column
 * @method     Eleve findOneByDateSortie(string $date_sortie) Return the first Eleve filtered by the date_sortie column
 * @method     Eleve findOneByIdMef(int $id_mef) Return the first Eleve filtered by the id_mef column
 *
 * @method     array findByNoGep(string $no_gep) Return Eleve objects filtered by the no_gep column
 * @method     array findByLogin(string $login) Return Eleve objects filtered by the login column
 * @method     array findByNom(string $nom) Return Eleve objects filtered by the nom column
 * @method     array findByPrenom(string $prenom) Return Eleve objects filtered by the prenom column
 * @method     array findBySexe(string $sexe) Return Eleve objects filtered by the sexe column
 * @method     array findByNaissance(string $naissance) Return Eleve objects filtered by the naissance column
 * @method     array findByLieuNaissance(string $lieu_naissance) Return Eleve objects filtered by the lieu_naissance column
 * @method     array findByElenoet(string $elenoet) Return Eleve objects filtered by the elenoet column
 * @method     array findByEreno(string $ereno) Return Eleve objects filtered by the ereno column
 * @method     array findByEleId(string $ele_id) Return Eleve objects filtered by the ele_id column
 * @method     array findByEmail(string $email) Return Eleve objects filtered by the email column
 * @method     array findByIdEleve(int $id_eleve) Return Eleve objects filtered by the id_eleve column
 * @method     array findByDateSortie(string $date_sortie) Return Eleve objects filtered by the date_sortie column
 * @method     array findByIdMef(int $id_mef) Return Eleve objects filtered by the id_mef column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEleveQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseEleveQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Eleve', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new EleveQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    EleveQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof EleveQuery) {
			return $criteria;
		}
		$query = new EleveQuery();
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
	 * @return    Eleve|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = ElevePeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ElevePeer::ID_ELEVE, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ElevePeer::ID_ELEVE, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the no_gep column
	 * 
	 * @param     string $noGep The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByNoGep($noGep = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($noGep)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $noGep)) {
				$noGep = str_replace('*', '%', $noGep);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::NO_GEP, $noGep, $comparison);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $login The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByLogin($login = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($login)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $login)) {
				$login = str_replace('*', '%', $login);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::LOGIN, $login, $comparison);
	}

	/**
	 * Filter the query on the nom column
	 * 
	 * @param     string $nom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
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
		return $this->addUsingAlias(ElevePeer::NOM, $nom, $comparison);
	}

	/**
	 * Filter the query on the prenom column
	 * 
	 * @param     string $prenom The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByPrenom($prenom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($prenom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $prenom)) {
				$prenom = str_replace('*', '%', $prenom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::PRENOM, $prenom, $comparison);
	}

	/**
	 * Filter the query on the sexe column
	 * 
	 * @param     string $sexe The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterBySexe($sexe = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($sexe)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $sexe)) {
				$sexe = str_replace('*', '%', $sexe);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::SEXE, $sexe, $comparison);
	}

	/**
	 * Filter the query on the naissance column
	 * 
	 * @param     string|array $naissance The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByNaissance($naissance = null, $comparison = null)
	{
		if (is_array($naissance)) {
			$useMinMax = false;
			if (isset($naissance['min'])) {
				$this->addUsingAlias(ElevePeer::NAISSANCE, $naissance['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($naissance['max'])) {
				$this->addUsingAlias(ElevePeer::NAISSANCE, $naissance['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ElevePeer::NAISSANCE, $naissance, $comparison);
	}

	/**
	 * Filter the query on the lieu_naissance column
	 * 
	 * @param     string $lieuNaissance The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByLieuNaissance($lieuNaissance = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($lieuNaissance)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $lieuNaissance)) {
				$lieuNaissance = str_replace('*', '%', $lieuNaissance);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::LIEU_NAISSANCE, $lieuNaissance, $comparison);
	}

	/**
	 * Filter the query on the elenoet column
	 * 
	 * @param     string $elenoet The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByElenoet($elenoet = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($elenoet)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $elenoet)) {
				$elenoet = str_replace('*', '%', $elenoet);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::ELENOET, $elenoet, $comparison);
	}

	/**
	 * Filter the query on the ereno column
	 * 
	 * @param     string $ereno The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByEreno($ereno = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ereno)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ereno)) {
				$ereno = str_replace('*', '%', $ereno);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::ERENO, $ereno, $comparison);
	}

	/**
	 * Filter the query on the ele_id column
	 * 
	 * @param     string $eleId The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByEleId($eleId = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($eleId)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $eleId)) {
				$eleId = str_replace('*', '%', $eleId);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::ELE_ID, $eleId, $comparison);
	}

	/**
	 * Filter the query on the email column
	 * 
	 * @param     string $email The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByEmail($email = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($email)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $email)) {
				$email = str_replace('*', '%', $email);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ElevePeer::EMAIL, $email, $comparison);
	}

	/**
	 * Filter the query on the id_eleve column
	 * 
	 * @param     int|array $idEleve The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByIdEleve($idEleve = null, $comparison = null)
	{
		if (is_array($idEleve) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ElevePeer::ID_ELEVE, $idEleve, $comparison);
	}

	/**
	 * Filter the query on the date_sortie column
	 * 
	 * @param     string|array $dateSortie The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByDateSortie($dateSortie = null, $comparison = null)
	{
		if (is_array($dateSortie)) {
			$useMinMax = false;
			if (isset($dateSortie['min'])) {
				$this->addUsingAlias(ElevePeer::DATE_SORTIE, $dateSortie['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dateSortie['max'])) {
				$this->addUsingAlias(ElevePeer::DATE_SORTIE, $dateSortie['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ElevePeer::DATE_SORTIE, $dateSortie, $comparison);
	}

	/**
	 * Filter the query on the id_mef column
	 * 
	 * @param     int|array $idMef The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByIdMef($idMef = null, $comparison = null)
	{
		if (is_array($idMef)) {
			$useMinMax = false;
			if (isset($idMef['min'])) {
				$this->addUsingAlias(ElevePeer::ID_MEF, $idMef['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($idMef['max'])) {
				$this->addUsingAlias(ElevePeer::ID_MEF, $idMef['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ElevePeer::ID_MEF, $idMef, $comparison);
	}

	/**
	 * Filter the query by a related Mef object
	 *
	 * @param     Mef $mef  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByMef($mef, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ID_MEF, $mef->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the Mef relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinMef($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Mef');
		
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
			$this->addJoinObject($join, 'Mef');
		}
		
		return $this;
	}

	/**
	 * Use the Mef relation Mef object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MefQuery A secondary query class using the current class as primary query
	 */
	public function useMefQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinMef($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Mef', 'MefQuery');
	}

	/**
	 * Filter the query by a related JEleveClasse object
	 *
	 * @param     JEleveClasse $jEleveClasse  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJEleveClasse($jEleveClasse, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $jEleveClasse->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveClasse relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinJEleveClasse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveClasse');
		
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
			$this->addJoinObject($join, 'JEleveClasse');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveClasse relation JEleveClasse object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveClasseQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveClasseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveClasse($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveClasse', 'JEleveClasseQuery');
	}

	/**
	 * Filter the query by a related JEleveCpe object
	 *
	 * @param     JEleveCpe $jEleveCpe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJEleveCpe($jEleveCpe, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $jEleveCpe->getELogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveCpe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinJEleveCpe($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveCpe');
		
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
			$this->addJoinObject($join, 'JEleveCpe');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveCpe relation JEleveCpe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveCpeQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveCpeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveCpe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveCpe', 'JEleveCpeQuery');
	}

	/**
	 * Filter the query by a related JEleveGroupe object
	 *
	 * @param     JEleveGroupe $jEleveGroupe  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJEleveGroupe($jEleveGroupe, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $jEleveGroupe->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveGroupe relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinJEleveGroupe($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveGroupe');
		
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
			$this->addJoinObject($join, 'JEleveGroupe');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveGroupe relation JEleveGroupe object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveGroupeQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveGroupeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveGroupe($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveGroupe', 'JEleveGroupeQuery');
	}

	/**
	 * Filter the query by a related JEleveProfesseurPrincipal object
	 *
	 * @param     JEleveProfesseurPrincipal $jEleveProfesseurPrincipal  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJEleveProfesseurPrincipal($jEleveProfesseurPrincipal, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $jEleveProfesseurPrincipal->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveProfesseurPrincipal relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinJEleveProfesseurPrincipal($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveProfesseurPrincipal');
		
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
			$this->addJoinObject($join, 'JEleveProfesseurPrincipal');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveProfesseurPrincipal relation JEleveProfesseurPrincipal object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveProfesseurPrincipalQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveProfesseurPrincipalQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveProfesseurPrincipal($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveProfesseurPrincipal', 'JEleveProfesseurPrincipalQuery');
	}

	/**
	 * Filter the query by a related EleveRegimeDoublant object
	 *
	 * @param     EleveRegimeDoublant $eleveRegimeDoublant  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByEleveRegimeDoublant($eleveRegimeDoublant, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $eleveRegimeDoublant->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the EleveRegimeDoublant relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinEleveRegimeDoublant($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('EleveRegimeDoublant');
		
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
			$this->addJoinObject($join, 'EleveRegimeDoublant');
		}
		
		return $this;
	}

	/**
	 * Use the EleveRegimeDoublant relation EleveRegimeDoublant object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveRegimeDoublantQuery A secondary query class using the current class as primary query
	 */
	public function useEleveRegimeDoublantQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinEleveRegimeDoublant($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'EleveRegimeDoublant', 'EleveRegimeDoublantQuery');
	}

	/**
	 * Filter the query by a related ResponsableInformation object
	 *
	 * @param     ResponsableInformation $responsableInformation  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByResponsableInformation($responsableInformation, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ELE_ID, $responsableInformation->getEleId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ResponsableInformation relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinResponsableInformation($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ResponsableInformation');
		
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
			$this->addJoinObject($join, 'ResponsableInformation');
		}
		
		return $this;
	}

	/**
	 * Use the ResponsableInformation relation ResponsableInformation object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ResponsableInformationQuery A secondary query class using the current class as primary query
	 */
	public function useResponsableInformationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinResponsableInformation($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ResponsableInformation', 'ResponsableInformationQuery');
	}

	/**
	 * Filter the query by a related JEleveAncienEtablissement object
	 *
	 * @param     JEleveAncienEtablissement $jEleveAncienEtablissement  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJEleveAncienEtablissement($jEleveAncienEtablissement, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ID_ELEVE, $jEleveAncienEtablissement->getIdEleve(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveAncienEtablissement relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinJEleveAncienEtablissement($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JEleveAncienEtablissement');
		
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
			$this->addJoinObject($join, 'JEleveAncienEtablissement');
		}
		
		return $this;
	}

	/**
	 * Use the JEleveAncienEtablissement relation JEleveAncienEtablissement object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JEleveAncienEtablissementQuery A secondary query class using the current class as primary query
	 */
	public function useJEleveAncienEtablissementQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJEleveAncienEtablissement($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JEleveAncienEtablissement', 'JEleveAncienEtablissementQuery');
	}

	/**
	 * Filter the query by a related JAidEleves object
	 *
	 * @param     JAidEleves $jAidEleves  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByJAidEleves($jAidEleves, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::LOGIN, $jAidEleves->getLogin(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the JAidEleves relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
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
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ID_ELEVE, $absenceEleveSaisie->getEleveId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the AbsenceEleveSaisie relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
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
	 * Filter the query by a related CreditEcts object
	 *
	 * @param     CreditEcts $creditEcts  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByCreditEcts($creditEcts, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ID_ELEVE, $creditEcts->getIdEleve(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CreditEcts relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinCreditEcts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CreditEcts');
		
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
			$this->addJoinObject($join, 'CreditEcts');
		}
		
		return $this;
	}

	/**
	 * Use the CreditEcts relation CreditEcts object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsQuery A secondary query class using the current class as primary query
	 */
	public function useCreditEctsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCreditEcts($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CreditEcts', 'CreditEctsQuery');
	}

	/**
	 * Filter the query by a related CreditEctsGlobal object
	 *
	 * @param     CreditEctsGlobal $creditEctsGlobal  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByCreditEctsGlobal($creditEctsGlobal, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::ID_ELEVE, $creditEctsGlobal->getIdEleve(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CreditEctsGlobal relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinCreditEctsGlobal($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CreditEctsGlobal');
		
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
			$this->addJoinObject($join, 'CreditEctsGlobal');
		}
		
		return $this;
	}

	/**
	 * Use the CreditEctsGlobal relation CreditEctsGlobal object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CreditEctsGlobalQuery A secondary query class using the current class as primary query
	 */
	public function useCreditEctsGlobalQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCreditEctsGlobal($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CreditEctsGlobal', 'CreditEctsGlobalQuery');
	}

	/**
	 * Filter the query by a related ArchiveEcts object
	 *
	 * @param     ArchiveEcts $archiveEcts  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByArchiveEcts($archiveEcts, $comparison = null)
	{
		return $this
			->addUsingAlias(ElevePeer::NO_GEP, $archiveEcts->getIne(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the ArchiveEcts relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function joinArchiveEcts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('ArchiveEcts');
		
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
			$this->addJoinObject($join, 'ArchiveEcts');
		}
		
		return $this;
	}

	/**
	 * Use the ArchiveEcts relation ArchiveEcts object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ArchiveEctsQuery A secondary query class using the current class as primary query
	 */
	public function useArchiveEctsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinArchiveEcts($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'ArchiveEcts', 'ArchiveEctsQuery');
	}

	/**
	 * Filter the query by a related AncienEtablissement object
	 * using the j_eleves_etablissements table as cross reference
	 *
	 * @param     AncienEtablissement $ancienEtablissement the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByAncienEtablissement($ancienEtablissement, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJEleveAncienEtablissementQuery()
				->filterByAncienEtablissement($ancienEtablissement, $comparison)
			->endUse();
	}
	
	/**
	 * Filter the query by a related AidDetails object
	 * using the j_aid_eleves table as cross reference
	 *
	 * @param     AidDetails $aidDetails the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function filterByAidDetails($aidDetails, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJAidElevesQuery()
				->filterByAidDetails($aidDetails, $comparison)
			->endUse();
	}
	
	/**
	 * Exclude object from result
	 *
	 * @param     Eleve $eleve Object to remove from the list of results
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
	public function prune($eleve = null)
	{
		if ($eleve) {
			$this->addUsingAlias(ElevePeer::ID_ELEVE, $eleve->getIdEleve(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseEleveQuery
