<?php


/**
 * Base class that represents a query for the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * @method     ClasseQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ClasseQuery orderByNom($order = Criteria::ASC) Order by the classe column
 * @method     ClasseQuery orderByNomComplet($order = Criteria::ASC) Order by the nom_complet column
 * @method     ClasseQuery orderBySuiviPar($order = Criteria::ASC) Order by the suivi_par column
 * @method     ClasseQuery orderByFormule($order = Criteria::ASC) Order by the formule column
 * @method     ClasseQuery orderByFormatNom($order = Criteria::ASC) Order by the format_nom column
 * @method     ClasseQuery orderByDisplayRang($order = Criteria::ASC) Order by the display_rang column
 * @method     ClasseQuery orderByDisplayAddress($order = Criteria::ASC) Order by the display_address column
 * @method     ClasseQuery orderByDisplayCoef($order = Criteria::ASC) Order by the display_coef column
 * @method     ClasseQuery orderByDisplayMatCat($order = Criteria::ASC) Order by the display_mat_cat column
 * @method     ClasseQuery orderByDisplayNbdev($order = Criteria::ASC) Order by the display_nbdev column
 * @method     ClasseQuery orderByDisplayMoyGen($order = Criteria::ASC) Order by the display_moy_gen column
 * @method     ClasseQuery orderByModeleBulletinPdf($order = Criteria::ASC) Order by the modele_bulletin_pdf column
 * @method     ClasseQuery orderByRnNomdev($order = Criteria::ASC) Order by the rn_nomdev column
 * @method     ClasseQuery orderByRnToutcoefdev($order = Criteria::ASC) Order by the rn_toutcoefdev column
 * @method     ClasseQuery orderByRnCoefdevSiDiff($order = Criteria::ASC) Order by the rn_coefdev_si_diff column
 * @method     ClasseQuery orderByRnDatedev($order = Criteria::ASC) Order by the rn_datedev column
 * @method     ClasseQuery orderByRnSignChefetab($order = Criteria::ASC) Order by the rn_sign_chefetab column
 * @method     ClasseQuery orderByRnSignPp($order = Criteria::ASC) Order by the rn_sign_pp column
 * @method     ClasseQuery orderByRnSignResp($order = Criteria::ASC) Order by the rn_sign_resp column
 * @method     ClasseQuery orderByRnSignNblig($order = Criteria::ASC) Order by the rn_sign_nblig column
 * @method     ClasseQuery orderByRnFormule($order = Criteria::ASC) Order by the rn_formule column
 * @method     ClasseQuery orderByEctsTypeFormation($order = Criteria::ASC) Order by the ects_type_formation column
 * @method     ClasseQuery orderByEctsParcours($order = Criteria::ASC) Order by the ects_parcours column
 * @method     ClasseQuery orderByEctsCodeParcours($order = Criteria::ASC) Order by the ects_code_parcours column
 * @method     ClasseQuery orderByEctsDomainesEtude($order = Criteria::ASC) Order by the ects_domaines_etude column
 * @method     ClasseQuery orderByEctsFonctionSignataireAttestation($order = Criteria::ASC) Order by the ects_fonction_signataire_attestation column
 *
 * @method     ClasseQuery groupById() Group by the id column
 * @method     ClasseQuery groupByNom() Group by the classe column
 * @method     ClasseQuery groupByNomComplet() Group by the nom_complet column
 * @method     ClasseQuery groupBySuiviPar() Group by the suivi_par column
 * @method     ClasseQuery groupByFormule() Group by the formule column
 * @method     ClasseQuery groupByFormatNom() Group by the format_nom column
 * @method     ClasseQuery groupByDisplayRang() Group by the display_rang column
 * @method     ClasseQuery groupByDisplayAddress() Group by the display_address column
 * @method     ClasseQuery groupByDisplayCoef() Group by the display_coef column
 * @method     ClasseQuery groupByDisplayMatCat() Group by the display_mat_cat column
 * @method     ClasseQuery groupByDisplayNbdev() Group by the display_nbdev column
 * @method     ClasseQuery groupByDisplayMoyGen() Group by the display_moy_gen column
 * @method     ClasseQuery groupByModeleBulletinPdf() Group by the modele_bulletin_pdf column
 * @method     ClasseQuery groupByRnNomdev() Group by the rn_nomdev column
 * @method     ClasseQuery groupByRnToutcoefdev() Group by the rn_toutcoefdev column
 * @method     ClasseQuery groupByRnCoefdevSiDiff() Group by the rn_coefdev_si_diff column
 * @method     ClasseQuery groupByRnDatedev() Group by the rn_datedev column
 * @method     ClasseQuery groupByRnSignChefetab() Group by the rn_sign_chefetab column
 * @method     ClasseQuery groupByRnSignPp() Group by the rn_sign_pp column
 * @method     ClasseQuery groupByRnSignResp() Group by the rn_sign_resp column
 * @method     ClasseQuery groupByRnSignNblig() Group by the rn_sign_nblig column
 * @method     ClasseQuery groupByRnFormule() Group by the rn_formule column
 * @method     ClasseQuery groupByEctsTypeFormation() Group by the ects_type_formation column
 * @method     ClasseQuery groupByEctsParcours() Group by the ects_parcours column
 * @method     ClasseQuery groupByEctsCodeParcours() Group by the ects_code_parcours column
 * @method     ClasseQuery groupByEctsDomainesEtude() Group by the ects_domaines_etude column
 * @method     ClasseQuery groupByEctsFonctionSignataireAttestation() Group by the ects_fonction_signataire_attestation column
 *
 * @method     ClasseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ClasseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ClasseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ClasseQuery leftJoinPeriodeNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the PeriodeNote relation
 * @method     ClasseQuery rightJoinPeriodeNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PeriodeNote relation
 * @method     ClasseQuery innerJoinPeriodeNote($relationAlias = null) Adds a INNER JOIN clause to the query using the PeriodeNote relation
 *
 * @method     ClasseQuery leftJoinJScolClasses($relationAlias = null) Adds a LEFT JOIN clause to the query using the JScolClasses relation
 * @method     ClasseQuery rightJoinJScolClasses($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JScolClasses relation
 * @method     ClasseQuery innerJoinJScolClasses($relationAlias = null) Adds a INNER JOIN clause to the query using the JScolClasses relation
 *
 * @method     ClasseQuery leftJoinJGroupesClasses($relationAlias = null) Adds a LEFT JOIN clause to the query using the JGroupesClasses relation
 * @method     ClasseQuery rightJoinJGroupesClasses($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JGroupesClasses relation
 * @method     ClasseQuery innerJoinJGroupesClasses($relationAlias = null) Adds a INNER JOIN clause to the query using the JGroupesClasses relation
 *
 * @method     ClasseQuery leftJoinJEleveClasse($relationAlias = null) Adds a LEFT JOIN clause to the query using the JEleveClasse relation
 * @method     ClasseQuery rightJoinJEleveClasse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JEleveClasse relation
 * @method     ClasseQuery innerJoinJEleveClasse($relationAlias = null) Adds a INNER JOIN clause to the query using the JEleveClasse relation
 *
 * @method     ClasseQuery leftJoinAbsenceEleveSaisie($relationAlias = null) Adds a LEFT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     ClasseQuery rightJoinAbsenceEleveSaisie($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AbsenceEleveSaisie relation
 * @method     ClasseQuery innerJoinAbsenceEleveSaisie($relationAlias = null) Adds a INNER JOIN clause to the query using the AbsenceEleveSaisie relation
 *
 * @method     ClasseQuery leftJoinJCategoriesMatieresClasses($relationAlias = null) Adds a LEFT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     ClasseQuery rightJoinJCategoriesMatieresClasses($relationAlias = null) Adds a RIGHT JOIN clause to the query using the JCategoriesMatieresClasses relation
 * @method     ClasseQuery innerJoinJCategoriesMatieresClasses($relationAlias = null) Adds a INNER JOIN clause to the query using the JCategoriesMatieresClasses relation
 *
 * @method     Classe findOne(PropelPDO $con = null) Return the first Classe matching the query
 * @method     Classe findOneOrCreate(PropelPDO $con = null) Return the first Classe matching the query, or a new Classe object populated from the query conditions when no match is found
 *
 * @method     Classe findOneById(int $id) Return the first Classe filtered by the id column
 * @method     Classe findOneByNom(string $classe) Return the first Classe filtered by the classe column
 * @method     Classe findOneByNomComplet(string $nom_complet) Return the first Classe filtered by the nom_complet column
 * @method     Classe findOneBySuiviPar(string $suivi_par) Return the first Classe filtered by the suivi_par column
 * @method     Classe findOneByFormule(string $formule) Return the first Classe filtered by the formule column
 * @method     Classe findOneByFormatNom(string $format_nom) Return the first Classe filtered by the format_nom column
 * @method     Classe findOneByDisplayRang(string $display_rang) Return the first Classe filtered by the display_rang column
 * @method     Classe findOneByDisplayAddress(string $display_address) Return the first Classe filtered by the display_address column
 * @method     Classe findOneByDisplayCoef(string $display_coef) Return the first Classe filtered by the display_coef column
 * @method     Classe findOneByDisplayMatCat(string $display_mat_cat) Return the first Classe filtered by the display_mat_cat column
 * @method     Classe findOneByDisplayNbdev(string $display_nbdev) Return the first Classe filtered by the display_nbdev column
 * @method     Classe findOneByDisplayMoyGen(string $display_moy_gen) Return the first Classe filtered by the display_moy_gen column
 * @method     Classe findOneByModeleBulletinPdf(string $modele_bulletin_pdf) Return the first Classe filtered by the modele_bulletin_pdf column
 * @method     Classe findOneByRnNomdev(string $rn_nomdev) Return the first Classe filtered by the rn_nomdev column
 * @method     Classe findOneByRnToutcoefdev(string $rn_toutcoefdev) Return the first Classe filtered by the rn_toutcoefdev column
 * @method     Classe findOneByRnCoefdevSiDiff(string $rn_coefdev_si_diff) Return the first Classe filtered by the rn_coefdev_si_diff column
 * @method     Classe findOneByRnDatedev(string $rn_datedev) Return the first Classe filtered by the rn_datedev column
 * @method     Classe findOneByRnSignChefetab(string $rn_sign_chefetab) Return the first Classe filtered by the rn_sign_chefetab column
 * @method     Classe findOneByRnSignPp(string $rn_sign_pp) Return the first Classe filtered by the rn_sign_pp column
 * @method     Classe findOneByRnSignResp(string $rn_sign_resp) Return the first Classe filtered by the rn_sign_resp column
 * @method     Classe findOneByRnSignNblig(int $rn_sign_nblig) Return the first Classe filtered by the rn_sign_nblig column
 * @method     Classe findOneByRnFormule(string $rn_formule) Return the first Classe filtered by the rn_formule column
 * @method     Classe findOneByEctsTypeFormation(string $ects_type_formation) Return the first Classe filtered by the ects_type_formation column
 * @method     Classe findOneByEctsParcours(string $ects_parcours) Return the first Classe filtered by the ects_parcours column
 * @method     Classe findOneByEctsCodeParcours(string $ects_code_parcours) Return the first Classe filtered by the ects_code_parcours column
 * @method     Classe findOneByEctsDomainesEtude(string $ects_domaines_etude) Return the first Classe filtered by the ects_domaines_etude column
 * @method     Classe findOneByEctsFonctionSignataireAttestation(string $ects_fonction_signataire_attestation) Return the first Classe filtered by the ects_fonction_signataire_attestation column
 *
 * @method     array findById(int $id) Return Classe objects filtered by the id column
 * @method     array findByNom(string $classe) Return Classe objects filtered by the classe column
 * @method     array findByNomComplet(string $nom_complet) Return Classe objects filtered by the nom_complet column
 * @method     array findBySuiviPar(string $suivi_par) Return Classe objects filtered by the suivi_par column
 * @method     array findByFormule(string $formule) Return Classe objects filtered by the formule column
 * @method     array findByFormatNom(string $format_nom) Return Classe objects filtered by the format_nom column
 * @method     array findByDisplayRang(string $display_rang) Return Classe objects filtered by the display_rang column
 * @method     array findByDisplayAddress(string $display_address) Return Classe objects filtered by the display_address column
 * @method     array findByDisplayCoef(string $display_coef) Return Classe objects filtered by the display_coef column
 * @method     array findByDisplayMatCat(string $display_mat_cat) Return Classe objects filtered by the display_mat_cat column
 * @method     array findByDisplayNbdev(string $display_nbdev) Return Classe objects filtered by the display_nbdev column
 * @method     array findByDisplayMoyGen(string $display_moy_gen) Return Classe objects filtered by the display_moy_gen column
 * @method     array findByModeleBulletinPdf(string $modele_bulletin_pdf) Return Classe objects filtered by the modele_bulletin_pdf column
 * @method     array findByRnNomdev(string $rn_nomdev) Return Classe objects filtered by the rn_nomdev column
 * @method     array findByRnToutcoefdev(string $rn_toutcoefdev) Return Classe objects filtered by the rn_toutcoefdev column
 * @method     array findByRnCoefdevSiDiff(string $rn_coefdev_si_diff) Return Classe objects filtered by the rn_coefdev_si_diff column
 * @method     array findByRnDatedev(string $rn_datedev) Return Classe objects filtered by the rn_datedev column
 * @method     array findByRnSignChefetab(string $rn_sign_chefetab) Return Classe objects filtered by the rn_sign_chefetab column
 * @method     array findByRnSignPp(string $rn_sign_pp) Return Classe objects filtered by the rn_sign_pp column
 * @method     array findByRnSignResp(string $rn_sign_resp) Return Classe objects filtered by the rn_sign_resp column
 * @method     array findByRnSignNblig(int $rn_sign_nblig) Return Classe objects filtered by the rn_sign_nblig column
 * @method     array findByRnFormule(string $rn_formule) Return Classe objects filtered by the rn_formule column
 * @method     array findByEctsTypeFormation(string $ects_type_formation) Return Classe objects filtered by the ects_type_formation column
 * @method     array findByEctsParcours(string $ects_parcours) Return Classe objects filtered by the ects_parcours column
 * @method     array findByEctsCodeParcours(string $ects_code_parcours) Return Classe objects filtered by the ects_code_parcours column
 * @method     array findByEctsDomainesEtude(string $ects_domaines_etude) Return Classe objects filtered by the ects_domaines_etude column
 * @method     array findByEctsFonctionSignataireAttestation(string $ects_fonction_signataire_attestation) Return Classe objects filtered by the ects_fonction_signataire_attestation column
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseClasseQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseClasseQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'gepi', $modelName = 'Classe', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new ClasseQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    ClasseQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof ClasseQuery) {
			return $criteria;
		}
		$query = new ClasseQuery();
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
	 * @return    Classe|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = ClassePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Classe A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT ID, CLASSE, NOM_COMPLET, SUIVI_PAR, FORMULE, FORMAT_NOM, DISPLAY_RANG, DISPLAY_ADDRESS, DISPLAY_COEF, DISPLAY_MAT_CAT, DISPLAY_NBDEV, DISPLAY_MOY_GEN, MODELE_BULLETIN_PDF, RN_NOMDEV, RN_TOUTCOEFDEV, RN_COEFDEV_SI_DIFF, RN_DATEDEV, RN_SIGN_CHEFETAB, RN_SIGN_PP, RN_SIGN_RESP, RN_SIGN_NBLIG, RN_FORMULE, ECTS_TYPE_FORMATION, ECTS_PARCOURS, ECTS_CODE_PARCOURS, ECTS_DOMAINES_ETUDE, ECTS_FONCTION_SIGNATAIRE_ATTESTATION FROM classes WHERE ID = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new Classe();
			$obj->hydrate($row);
			ClassePeer::addInstanceToPool($obj, (string) $key);
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
	 * @return    Classe|array|mixed the result, formatted by the current formatter
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
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(ClassePeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(ClassePeer::ID, $keys, Criteria::IN);
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
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(ClassePeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query on the classe column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNom('fooValue');   // WHERE classe = 'fooValue'
	 * $query->filterByNom('%fooValue%'); // WHERE classe LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
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
		return $this->addUsingAlias(ClassePeer::CLASSE, $nom, $comparison);
	}

	/**
	 * Filter the query on the nom_complet column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByNomComplet('fooValue');   // WHERE nom_complet = 'fooValue'
	 * $query->filterByNomComplet('%fooValue%'); // WHERE nom_complet LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $nomComplet The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByNomComplet($nomComplet = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($nomComplet)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $nomComplet)) {
				$nomComplet = str_replace('*', '%', $nomComplet);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::NOM_COMPLET, $nomComplet, $comparison);
	}

	/**
	 * Filter the query on the suivi_par column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterBySuiviPar('fooValue');   // WHERE suivi_par = 'fooValue'
	 * $query->filterBySuiviPar('%fooValue%'); // WHERE suivi_par LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $suiviPar The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterBySuiviPar($suiviPar = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($suiviPar)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $suiviPar)) {
				$suiviPar = str_replace('*', '%', $suiviPar);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::SUIVI_PAR, $suiviPar, $comparison);
	}

	/**
	 * Filter the query on the formule column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFormule('fooValue');   // WHERE formule = 'fooValue'
	 * $query->filterByFormule('%fooValue%'); // WHERE formule LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $formule The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByFormule($formule = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($formule)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $formule)) {
				$formule = str_replace('*', '%', $formule);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::FORMULE, $formule, $comparison);
	}

	/**
	 * Filter the query on the format_nom column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByFormatNom('fooValue');   // WHERE format_nom = 'fooValue'
	 * $query->filterByFormatNom('%fooValue%'); // WHERE format_nom LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $formatNom The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByFormatNom($formatNom = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($formatNom)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $formatNom)) {
				$formatNom = str_replace('*', '%', $formatNom);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::FORMAT_NOM, $formatNom, $comparison);
	}

	/**
	 * Filter the query on the display_rang column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayRang('fooValue');   // WHERE display_rang = 'fooValue'
	 * $query->filterByDisplayRang('%fooValue%'); // WHERE display_rang LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayRang The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayRang($displayRang = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayRang)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayRang)) {
				$displayRang = str_replace('*', '%', $displayRang);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_RANG, $displayRang, $comparison);
	}

	/**
	 * Filter the query on the display_address column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayAddress('fooValue');   // WHERE display_address = 'fooValue'
	 * $query->filterByDisplayAddress('%fooValue%'); // WHERE display_address LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayAddress The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayAddress($displayAddress = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayAddress)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayAddress)) {
				$displayAddress = str_replace('*', '%', $displayAddress);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_ADDRESS, $displayAddress, $comparison);
	}

	/**
	 * Filter the query on the display_coef column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayCoef('fooValue');   // WHERE display_coef = 'fooValue'
	 * $query->filterByDisplayCoef('%fooValue%'); // WHERE display_coef LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayCoef The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayCoef($displayCoef = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayCoef)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayCoef)) {
				$displayCoef = str_replace('*', '%', $displayCoef);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_COEF, $displayCoef, $comparison);
	}

	/**
	 * Filter the query on the display_mat_cat column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayMatCat('fooValue');   // WHERE display_mat_cat = 'fooValue'
	 * $query->filterByDisplayMatCat('%fooValue%'); // WHERE display_mat_cat LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayMatCat The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayMatCat($displayMatCat = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayMatCat)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayMatCat)) {
				$displayMatCat = str_replace('*', '%', $displayMatCat);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_MAT_CAT, $displayMatCat, $comparison);
	}

	/**
	 * Filter the query on the display_nbdev column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayNbdev('fooValue');   // WHERE display_nbdev = 'fooValue'
	 * $query->filterByDisplayNbdev('%fooValue%'); // WHERE display_nbdev LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayNbdev The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayNbdev($displayNbdev = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayNbdev)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayNbdev)) {
				$displayNbdev = str_replace('*', '%', $displayNbdev);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_NBDEV, $displayNbdev, $comparison);
	}

	/**
	 * Filter the query on the display_moy_gen column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDisplayMoyGen('fooValue');   // WHERE display_moy_gen = 'fooValue'
	 * $query->filterByDisplayMoyGen('%fooValue%'); // WHERE display_moy_gen LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $displayMoyGen The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByDisplayMoyGen($displayMoyGen = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($displayMoyGen)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $displayMoyGen)) {
				$displayMoyGen = str_replace('*', '%', $displayMoyGen);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::DISPLAY_MOY_GEN, $displayMoyGen, $comparison);
	}

	/**
	 * Filter the query on the modele_bulletin_pdf column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByModeleBulletinPdf('fooValue');   // WHERE modele_bulletin_pdf = 'fooValue'
	 * $query->filterByModeleBulletinPdf('%fooValue%'); // WHERE modele_bulletin_pdf LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $modeleBulletinPdf The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByModeleBulletinPdf($modeleBulletinPdf = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($modeleBulletinPdf)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $modeleBulletinPdf)) {
				$modeleBulletinPdf = str_replace('*', '%', $modeleBulletinPdf);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::MODELE_BULLETIN_PDF, $modeleBulletinPdf, $comparison);
	}

	/**
	 * Filter the query on the rn_nomdev column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnNomdev('fooValue');   // WHERE rn_nomdev = 'fooValue'
	 * $query->filterByRnNomdev('%fooValue%'); // WHERE rn_nomdev LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnNomdev The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnNomdev($rnNomdev = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnNomdev)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnNomdev)) {
				$rnNomdev = str_replace('*', '%', $rnNomdev);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_NOMDEV, $rnNomdev, $comparison);
	}

	/**
	 * Filter the query on the rn_toutcoefdev column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnToutcoefdev('fooValue');   // WHERE rn_toutcoefdev = 'fooValue'
	 * $query->filterByRnToutcoefdev('%fooValue%'); // WHERE rn_toutcoefdev LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnToutcoefdev The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnToutcoefdev($rnToutcoefdev = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnToutcoefdev)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnToutcoefdev)) {
				$rnToutcoefdev = str_replace('*', '%', $rnToutcoefdev);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_TOUTCOEFDEV, $rnToutcoefdev, $comparison);
	}

	/**
	 * Filter the query on the rn_coefdev_si_diff column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnCoefdevSiDiff('fooValue');   // WHERE rn_coefdev_si_diff = 'fooValue'
	 * $query->filterByRnCoefdevSiDiff('%fooValue%'); // WHERE rn_coefdev_si_diff LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnCoefdevSiDiff The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnCoefdevSiDiff($rnCoefdevSiDiff = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnCoefdevSiDiff)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnCoefdevSiDiff)) {
				$rnCoefdevSiDiff = str_replace('*', '%', $rnCoefdevSiDiff);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_COEFDEV_SI_DIFF, $rnCoefdevSiDiff, $comparison);
	}

	/**
	 * Filter the query on the rn_datedev column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnDatedev('fooValue');   // WHERE rn_datedev = 'fooValue'
	 * $query->filterByRnDatedev('%fooValue%'); // WHERE rn_datedev LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnDatedev The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnDatedev($rnDatedev = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnDatedev)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnDatedev)) {
				$rnDatedev = str_replace('*', '%', $rnDatedev);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_DATEDEV, $rnDatedev, $comparison);
	}

	/**
	 * Filter the query on the rn_sign_chefetab column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnSignChefetab('fooValue');   // WHERE rn_sign_chefetab = 'fooValue'
	 * $query->filterByRnSignChefetab('%fooValue%'); // WHERE rn_sign_chefetab LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnSignChefetab The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnSignChefetab($rnSignChefetab = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnSignChefetab)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnSignChefetab)) {
				$rnSignChefetab = str_replace('*', '%', $rnSignChefetab);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_SIGN_CHEFETAB, $rnSignChefetab, $comparison);
	}

	/**
	 * Filter the query on the rn_sign_pp column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnSignPp('fooValue');   // WHERE rn_sign_pp = 'fooValue'
	 * $query->filterByRnSignPp('%fooValue%'); // WHERE rn_sign_pp LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnSignPp The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnSignPp($rnSignPp = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnSignPp)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnSignPp)) {
				$rnSignPp = str_replace('*', '%', $rnSignPp);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_SIGN_PP, $rnSignPp, $comparison);
	}

	/**
	 * Filter the query on the rn_sign_resp column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnSignResp('fooValue');   // WHERE rn_sign_resp = 'fooValue'
	 * $query->filterByRnSignResp('%fooValue%'); // WHERE rn_sign_resp LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnSignResp The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnSignResp($rnSignResp = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnSignResp)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnSignResp)) {
				$rnSignResp = str_replace('*', '%', $rnSignResp);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_SIGN_RESP, $rnSignResp, $comparison);
	}

	/**
	 * Filter the query on the rn_sign_nblig column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnSignNblig(1234); // WHERE rn_sign_nblig = 1234
	 * $query->filterByRnSignNblig(array(12, 34)); // WHERE rn_sign_nblig IN (12, 34)
	 * $query->filterByRnSignNblig(array('min' => 12)); // WHERE rn_sign_nblig > 12
	 * </code>
	 *
	 * @param     mixed $rnSignNblig The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnSignNblig($rnSignNblig = null, $comparison = null)
	{
		if (is_array($rnSignNblig)) {
			$useMinMax = false;
			if (isset($rnSignNblig['min'])) {
				$this->addUsingAlias(ClassePeer::RN_SIGN_NBLIG, $rnSignNblig['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($rnSignNblig['max'])) {
				$this->addUsingAlias(ClassePeer::RN_SIGN_NBLIG, $rnSignNblig['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_SIGN_NBLIG, $rnSignNblig, $comparison);
	}

	/**
	 * Filter the query on the rn_formule column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByRnFormule('fooValue');   // WHERE rn_formule = 'fooValue'
	 * $query->filterByRnFormule('%fooValue%'); // WHERE rn_formule LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $rnFormule The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByRnFormule($rnFormule = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rnFormule)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rnFormule)) {
				$rnFormule = str_replace('*', '%', $rnFormule);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::RN_FORMULE, $rnFormule, $comparison);
	}

	/**
	 * Filter the query on the ects_type_formation column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEctsTypeFormation('fooValue');   // WHERE ects_type_formation = 'fooValue'
	 * $query->filterByEctsTypeFormation('%fooValue%'); // WHERE ects_type_formation LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ectsTypeFormation The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByEctsTypeFormation($ectsTypeFormation = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ectsTypeFormation)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ectsTypeFormation)) {
				$ectsTypeFormation = str_replace('*', '%', $ectsTypeFormation);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::ECTS_TYPE_FORMATION, $ectsTypeFormation, $comparison);
	}

	/**
	 * Filter the query on the ects_parcours column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEctsParcours('fooValue');   // WHERE ects_parcours = 'fooValue'
	 * $query->filterByEctsParcours('%fooValue%'); // WHERE ects_parcours LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ectsParcours The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByEctsParcours($ectsParcours = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ectsParcours)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ectsParcours)) {
				$ectsParcours = str_replace('*', '%', $ectsParcours);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::ECTS_PARCOURS, $ectsParcours, $comparison);
	}

	/**
	 * Filter the query on the ects_code_parcours column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEctsCodeParcours('fooValue');   // WHERE ects_code_parcours = 'fooValue'
	 * $query->filterByEctsCodeParcours('%fooValue%'); // WHERE ects_code_parcours LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ectsCodeParcours The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByEctsCodeParcours($ectsCodeParcours = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ectsCodeParcours)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ectsCodeParcours)) {
				$ectsCodeParcours = str_replace('*', '%', $ectsCodeParcours);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::ECTS_CODE_PARCOURS, $ectsCodeParcours, $comparison);
	}

	/**
	 * Filter the query on the ects_domaines_etude column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEctsDomainesEtude('fooValue');   // WHERE ects_domaines_etude = 'fooValue'
	 * $query->filterByEctsDomainesEtude('%fooValue%'); // WHERE ects_domaines_etude LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ectsDomainesEtude The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByEctsDomainesEtude($ectsDomainesEtude = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ectsDomainesEtude)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ectsDomainesEtude)) {
				$ectsDomainesEtude = str_replace('*', '%', $ectsDomainesEtude);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::ECTS_DOMAINES_ETUDE, $ectsDomainesEtude, $comparison);
	}

	/**
	 * Filter the query on the ects_fonction_signataire_attestation column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEctsFonctionSignataireAttestation('fooValue');   // WHERE ects_fonction_signataire_attestation = 'fooValue'
	 * $query->filterByEctsFonctionSignataireAttestation('%fooValue%'); // WHERE ects_fonction_signataire_attestation LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $ectsFonctionSignataireAttestation The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByEctsFonctionSignataireAttestation($ectsFonctionSignataireAttestation = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ectsFonctionSignataireAttestation)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ectsFonctionSignataireAttestation)) {
				$ectsFonctionSignataireAttestation = str_replace('*', '%', $ectsFonctionSignataireAttestation);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION, $ectsFonctionSignataireAttestation, $comparison);
	}

	/**
	 * Filter the query by a related PeriodeNote object
	 *
	 * @param     PeriodeNote $periodeNote  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByPeriodeNote($periodeNote, $comparison = null)
	{
		if ($periodeNote instanceof PeriodeNote) {
			return $this
				->addUsingAlias(ClassePeer::ID, $periodeNote->getIdClasse(), $comparison);
		} elseif ($periodeNote instanceof PropelCollection) {
			return $this
				->usePeriodeNoteQuery()
				->filterByPrimaryKeys($periodeNote->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByPeriodeNote() only accepts arguments of type PeriodeNote or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the PeriodeNote relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function joinPeriodeNote($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('PeriodeNote');

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
			$this->addJoinObject($join, 'PeriodeNote');
		}

		return $this;
	}

	/**
	 * Use the PeriodeNote relation PeriodeNote object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    PeriodeNoteQuery A secondary query class using the current class as primary query
	 */
	public function usePeriodeNoteQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinPeriodeNote($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'PeriodeNote', 'PeriodeNoteQuery');
	}

	/**
	 * Filter the query by a related JScolClasses object
	 *
	 * @param     JScolClasses $jScolClasses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByJScolClasses($jScolClasses, $comparison = null)
	{
		if ($jScolClasses instanceof JScolClasses) {
			return $this
				->addUsingAlias(ClassePeer::ID, $jScolClasses->getIdClasse(), $comparison);
		} elseif ($jScolClasses instanceof PropelCollection) {
			return $this
				->useJScolClassesQuery()
				->filterByPrimaryKeys($jScolClasses->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJScolClasses() only accepts arguments of type JScolClasses or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JScolClasses relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function joinJScolClasses($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JScolClasses');

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
			$this->addJoinObject($join, 'JScolClasses');
		}

		return $this;
	}

	/**
	 * Use the JScolClasses relation JScolClasses object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JScolClassesQuery A secondary query class using the current class as primary query
	 */
	public function useJScolClassesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJScolClasses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JScolClasses', 'JScolClassesQuery');
	}

	/**
	 * Filter the query by a related JGroupesClasses object
	 *
	 * @param     JGroupesClasses $jGroupesClasses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByJGroupesClasses($jGroupesClasses, $comparison = null)
	{
		if ($jGroupesClasses instanceof JGroupesClasses) {
			return $this
				->addUsingAlias(ClassePeer::ID, $jGroupesClasses->getIdClasse(), $comparison);
		} elseif ($jGroupesClasses instanceof PropelCollection) {
			return $this
				->useJGroupesClassesQuery()
				->filterByPrimaryKeys($jGroupesClasses->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJGroupesClasses() only accepts arguments of type JGroupesClasses or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JGroupesClasses relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function joinJGroupesClasses($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JGroupesClasses');

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
			$this->addJoinObject($join, 'JGroupesClasses');
		}

		return $this;
	}

	/**
	 * Use the JGroupesClasses relation JGroupesClasses object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JGroupesClassesQuery A secondary query class using the current class as primary query
	 */
	public function useJGroupesClassesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJGroupesClasses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JGroupesClasses', 'JGroupesClassesQuery');
	}

	/**
	 * Filter the query by a related JEleveClasse object
	 *
	 * @param     JEleveClasse $jEleveClasse  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByJEleveClasse($jEleveClasse, $comparison = null)
	{
		if ($jEleveClasse instanceof JEleveClasse) {
			return $this
				->addUsingAlias(ClassePeer::ID, $jEleveClasse->getIdClasse(), $comparison);
		} elseif ($jEleveClasse instanceof PropelCollection) {
			return $this
				->useJEleveClasseQuery()
				->filterByPrimaryKeys($jEleveClasse->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJEleveClasse() only accepts arguments of type JEleveClasse or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JEleveClasse relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery The current query, for fluid interface
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
	 * Filter the query by a related AbsenceEleveSaisie object
	 *
	 * @param     AbsenceEleveSaisie $absenceEleveSaisie  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByAbsenceEleveSaisie($absenceEleveSaisie, $comparison = null)
	{
		if ($absenceEleveSaisie instanceof AbsenceEleveSaisie) {
			return $this
				->addUsingAlias(ClassePeer::ID, $absenceEleveSaisie->getIdClasse(), $comparison);
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
	 * @return    ClasseQuery The current query, for fluid interface
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
	 * Filter the query by a related JCategoriesMatieresClasses object
	 *
	 * @param     JCategoriesMatieresClasses $jCategoriesMatieresClasses  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByJCategoriesMatieresClasses($jCategoriesMatieresClasses, $comparison = null)
	{
		if ($jCategoriesMatieresClasses instanceof JCategoriesMatieresClasses) {
			return $this
				->addUsingAlias(ClassePeer::ID, $jCategoriesMatieresClasses->getClasseId(), $comparison);
		} elseif ($jCategoriesMatieresClasses instanceof PropelCollection) {
			return $this
				->useJCategoriesMatieresClassesQuery()
				->filterByPrimaryKeys($jCategoriesMatieresClasses->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterByJCategoriesMatieresClasses() only accepts arguments of type JCategoriesMatieresClasses or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the JCategoriesMatieresClasses relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function joinJCategoriesMatieresClasses($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('JCategoriesMatieresClasses');

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
			$this->addJoinObject($join, 'JCategoriesMatieresClasses');
		}

		return $this;
	}

	/**
	 * Use the JCategoriesMatieresClasses relation JCategoriesMatieresClasses object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    JCategoriesMatieresClassesQuery A secondary query class using the current class as primary query
	 */
	public function useJCategoriesMatieresClassesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinJCategoriesMatieresClasses($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'JCategoriesMatieresClasses', 'JCategoriesMatieresClassesQuery');
	}

	/**
	 * Filter the query by a related Groupe object
	 * using the j_groupes_classes table as cross reference
	 *
	 * @param     Groupe $groupe the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByGroupe($groupe, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJGroupesClassesQuery()
			->filterByGroupe($groupe, $comparison)
			->endUse();
	}

	/**
	 * Filter the query by a related CategorieMatiere object
	 * using the j_matieres_categories_classes table as cross reference
	 *
	 * @param     CategorieMatiere $categorieMatiere the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function filterByCategorieMatiere($categorieMatiere, $comparison = Criteria::EQUAL)
	{
		return $this
			->useJCategoriesMatieresClassesQuery()
			->filterByCategorieMatiere($categorieMatiere, $comparison)
			->endUse();
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Classe $classe Object to remove from the list of results
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
	public function prune($classe = null)
	{
		if ($classe) {
			$this->addUsingAlias(ClassePeer::ID, $classe->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseClasseQuery