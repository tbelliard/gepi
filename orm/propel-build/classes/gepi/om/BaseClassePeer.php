<?php

/**
 * Base static class for performing query and update operations on the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * @package    gepi.om
 */
abstract class BaseClassePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'classes';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.Classe';

	/** The total number of columns. */
	const NUM_COLUMNS = 27;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'classes.ID';

	/** the column name for the CLASSE field */
	const CLASSE = 'classes.CLASSE';

	/** the column name for the NOM_COMPLET field */
	const NOM_COMPLET = 'classes.NOM_COMPLET';

	/** the column name for the SUIVI_PAR field */
	const SUIVI_PAR = 'classes.SUIVI_PAR';

	/** the column name for the FORMULE field */
	const FORMULE = 'classes.FORMULE';

	/** the column name for the FORMAT_NOM field */
	const FORMAT_NOM = 'classes.FORMAT_NOM';

	/** the column name for the DISPLAY_RANG field */
	const DISPLAY_RANG = 'classes.DISPLAY_RANG';

	/** the column name for the DISPLAY_ADDRESS field */
	const DISPLAY_ADDRESS = 'classes.DISPLAY_ADDRESS';

	/** the column name for the DISPLAY_COEF field */
	const DISPLAY_COEF = 'classes.DISPLAY_COEF';

	/** the column name for the DISPLAY_MAT_CAT field */
	const DISPLAY_MAT_CAT = 'classes.DISPLAY_MAT_CAT';

	/** the column name for the DISPLAY_NBDEV field */
	const DISPLAY_NBDEV = 'classes.DISPLAY_NBDEV';

	/** the column name for the DISPLAY_MOY_GEN field */
	const DISPLAY_MOY_GEN = 'classes.DISPLAY_MOY_GEN';

	/** the column name for the MODELE_BULLETIN_PDF field */
	const MODELE_BULLETIN_PDF = 'classes.MODELE_BULLETIN_PDF';

	/** the column name for the RN_NOMDEV field */
	const RN_NOMDEV = 'classes.RN_NOMDEV';

	/** the column name for the RN_TOUTCOEFDEV field */
	const RN_TOUTCOEFDEV = 'classes.RN_TOUTCOEFDEV';

	/** the column name for the RN_COEFDEV_SI_DIFF field */
	const RN_COEFDEV_SI_DIFF = 'classes.RN_COEFDEV_SI_DIFF';

	/** the column name for the RN_DATEDEV field */
	const RN_DATEDEV = 'classes.RN_DATEDEV';

	/** the column name for the RN_SIGN_CHEFETAB field */
	const RN_SIGN_CHEFETAB = 'classes.RN_SIGN_CHEFETAB';

	/** the column name for the RN_SIGN_PP field */
	const RN_SIGN_PP = 'classes.RN_SIGN_PP';

	/** the column name for the RN_SIGN_RESP field */
	const RN_SIGN_RESP = 'classes.RN_SIGN_RESP';

	/** the column name for the RN_SIGN_NBLIG field */
	const RN_SIGN_NBLIG = 'classes.RN_SIGN_NBLIG';

	/** the column name for the RN_FORMULE field */
	const RN_FORMULE = 'classes.RN_FORMULE';

	/** the column name for the ECTS_TYPE_FORMATION field */
	const ECTS_TYPE_FORMATION = 'classes.ECTS_TYPE_FORMATION';

	/** the column name for the ECTS_PARCOURS field */
	const ECTS_PARCOURS = 'classes.ECTS_PARCOURS';

	/** the column name for the ECTS_CODE_PARCOURS field */
	const ECTS_CODE_PARCOURS = 'classes.ECTS_CODE_PARCOURS';

	/** the column name for the ECTS_DOMAINES_ETUDE field */
	const ECTS_DOMAINES_ETUDE = 'classes.ECTS_DOMAINES_ETUDE';

	/** the column name for the ECTS_FONCTION_SIGNATAIRE_ATTESTATION field */
	const ECTS_FONCTION_SIGNATAIRE_ATTESTATION = 'classes.ECTS_FONCTION_SIGNATAIRE_ATTESTATION';

	/**
	 * An identiy map to hold any loaded instances of Classe objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array Classe[]
	 */
	public static $instances = array();

	/**
	 * The MapBuilder instance for this peer.
	 * @var        MapBuilder
	 */
	private static $mapBuilder = null;

	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Classe', 'NomComplet', 'SuiviPar', 'Formule', 'FormatNom', 'DisplayRang', 'DisplayAddress', 'DisplayCoef', 'DisplayMatCat', 'DisplayNbdev', 'DisplayMoyGen', 'ModeleBulletinPdf', 'RnNomdev', 'RnToutcoefdev', 'RnCoefdevSiDiff', 'RnDatedev', 'RnSignChefetab', 'RnSignPp', 'RnSignResp', 'RnSignNblig', 'RnFormule', 'EctsTypeFormation', 'EctsParcours', 'EctsCodeParcours', 'EctsDomainesEtude', 'EctsFonctionSignataireAttestation', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'classe', 'nomComplet', 'suiviPar', 'formule', 'formatNom', 'displayRang', 'displayAddress', 'displayCoef', 'displayMatCat', 'displayNbdev', 'displayMoyGen', 'modeleBulletinPdf', 'rnNomdev', 'rnToutcoefdev', 'rnCoefdevSiDiff', 'rnDatedev', 'rnSignChefetab', 'rnSignPp', 'rnSignResp', 'rnSignNblig', 'rnFormule', 'ectsTypeFormation', 'ectsParcours', 'ectsCodeParcours', 'ectsDomainesEtude', 'ectsFonctionSignataireAttestation', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::CLASSE, self::NOM_COMPLET, self::SUIVI_PAR, self::FORMULE, self::FORMAT_NOM, self::DISPLAY_RANG, self::DISPLAY_ADDRESS, self::DISPLAY_COEF, self::DISPLAY_MAT_CAT, self::DISPLAY_NBDEV, self::DISPLAY_MOY_GEN, self::MODELE_BULLETIN_PDF, self::RN_NOMDEV, self::RN_TOUTCOEFDEV, self::RN_COEFDEV_SI_DIFF, self::RN_DATEDEV, self::RN_SIGN_CHEFETAB, self::RN_SIGN_PP, self::RN_SIGN_RESP, self::RN_SIGN_NBLIG, self::RN_FORMULE, self::ECTS_TYPE_FORMATION, self::ECTS_PARCOURS, self::ECTS_CODE_PARCOURS, self::ECTS_DOMAINES_ETUDE, self::ECTS_FONCTION_SIGNATAIRE_ATTESTATION, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'classe', 'nom_complet', 'suivi_par', 'formule', 'format_nom', 'display_rang', 'display_address', 'display_coef', 'display_mat_cat', 'display_nbdev', 'display_moy_gen', 'modele_bulletin_pdf', 'rn_nomdev', 'rn_toutcoefdev', 'rn_coefdev_si_diff', 'rn_datedev', 'rn_sign_chefetab', 'rn_sign_pp', 'rn_sign_resp', 'rn_sign_nblig', 'rn_formule', 'ects_type_formation', 'ects_parcours', 'ects_code_parcours', 'ects_domaines_etude', 'ects_fonction_signataire_attestation', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Classe' => 1, 'NomComplet' => 2, 'SuiviPar' => 3, 'Formule' => 4, 'FormatNom' => 5, 'DisplayRang' => 6, 'DisplayAddress' => 7, 'DisplayCoef' => 8, 'DisplayMatCat' => 9, 'DisplayNbdev' => 10, 'DisplayMoyGen' => 11, 'ModeleBulletinPdf' => 12, 'RnNomdev' => 13, 'RnToutcoefdev' => 14, 'RnCoefdevSiDiff' => 15, 'RnDatedev' => 16, 'RnSignChefetab' => 17, 'RnSignPp' => 18, 'RnSignResp' => 19, 'RnSignNblig' => 20, 'RnFormule' => 21, 'EctsTypeFormation' => 22, 'EctsParcours' => 23, 'EctsCodeParcours' => 24, 'EctsDomainesEtude' => 25, 'EctsFonctionSignataireAttestation' => 26, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'classe' => 1, 'nomComplet' => 2, 'suiviPar' => 3, 'formule' => 4, 'formatNom' => 5, 'displayRang' => 6, 'displayAddress' => 7, 'displayCoef' => 8, 'displayMatCat' => 9, 'displayNbdev' => 10, 'displayMoyGen' => 11, 'modeleBulletinPdf' => 12, 'rnNomdev' => 13, 'rnToutcoefdev' => 14, 'rnCoefdevSiDiff' => 15, 'rnDatedev' => 16, 'rnSignChefetab' => 17, 'rnSignPp' => 18, 'rnSignResp' => 19, 'rnSignNblig' => 20, 'rnFormule' => 21, 'ectsTypeFormation' => 22, 'ectsParcours' => 23, 'ectsCodeParcours' => 24, 'ectsDomainesEtude' => 25, 'ectsFonctionSignataireAttestation' => 26, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::CLASSE => 1, self::NOM_COMPLET => 2, self::SUIVI_PAR => 3, self::FORMULE => 4, self::FORMAT_NOM => 5, self::DISPLAY_RANG => 6, self::DISPLAY_ADDRESS => 7, self::DISPLAY_COEF => 8, self::DISPLAY_MAT_CAT => 9, self::DISPLAY_NBDEV => 10, self::DISPLAY_MOY_GEN => 11, self::MODELE_BULLETIN_PDF => 12, self::RN_NOMDEV => 13, self::RN_TOUTCOEFDEV => 14, self::RN_COEFDEV_SI_DIFF => 15, self::RN_DATEDEV => 16, self::RN_SIGN_CHEFETAB => 17, self::RN_SIGN_PP => 18, self::RN_SIGN_RESP => 19, self::RN_SIGN_NBLIG => 20, self::RN_FORMULE => 21, self::ECTS_TYPE_FORMATION => 22, self::ECTS_PARCOURS => 23, self::ECTS_CODE_PARCOURS => 24, self::ECTS_DOMAINES_ETUDE => 25, self::ECTS_FONCTION_SIGNATAIRE_ATTESTATION => 26, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'classe' => 1, 'nom_complet' => 2, 'suivi_par' => 3, 'formule' => 4, 'format_nom' => 5, 'display_rang' => 6, 'display_address' => 7, 'display_coef' => 8, 'display_mat_cat' => 9, 'display_nbdev' => 10, 'display_moy_gen' => 11, 'modele_bulletin_pdf' => 12, 'rn_nomdev' => 13, 'rn_toutcoefdev' => 14, 'rn_coefdev_si_diff' => 15, 'rn_datedev' => 16, 'rn_sign_chefetab' => 17, 'rn_sign_pp' => 18, 'rn_sign_resp' => 19, 'rn_sign_nblig' => 20, 'rn_formule' => 21, 'ects_type_formation' => 22, 'ects_parcours' => 23, 'ects_code_parcours' => 24, 'ects_domaines_etude' => 25, 'ects_fonction_signataire_attestation' => 26, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new ClasseMapBuilder();
		}
		return self::$mapBuilder;
	}
	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. ClassePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ClassePeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      criteria object containing the columns to add.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria)
	{

		$criteria->addSelectColumn(ClassePeer::ID);

		$criteria->addSelectColumn(ClassePeer::CLASSE);

		$criteria->addSelectColumn(ClassePeer::NOM_COMPLET);

		$criteria->addSelectColumn(ClassePeer::SUIVI_PAR);

		$criteria->addSelectColumn(ClassePeer::FORMULE);

		$criteria->addSelectColumn(ClassePeer::FORMAT_NOM);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_RANG);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_ADDRESS);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_COEF);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_MAT_CAT);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_NBDEV);

		$criteria->addSelectColumn(ClassePeer::DISPLAY_MOY_GEN);

		$criteria->addSelectColumn(ClassePeer::MODELE_BULLETIN_PDF);

		$criteria->addSelectColumn(ClassePeer::RN_NOMDEV);

		$criteria->addSelectColumn(ClassePeer::RN_TOUTCOEFDEV);

		$criteria->addSelectColumn(ClassePeer::RN_COEFDEV_SI_DIFF);

		$criteria->addSelectColumn(ClassePeer::RN_DATEDEV);

		$criteria->addSelectColumn(ClassePeer::RN_SIGN_CHEFETAB);

		$criteria->addSelectColumn(ClassePeer::RN_SIGN_PP);

		$criteria->addSelectColumn(ClassePeer::RN_SIGN_RESP);

		$criteria->addSelectColumn(ClassePeer::RN_SIGN_NBLIG);

		$criteria->addSelectColumn(ClassePeer::RN_FORMULE);

		$criteria->addSelectColumn(ClassePeer::ECTS_TYPE_FORMATION);

		$criteria->addSelectColumn(ClassePeer::ECTS_PARCOURS);

		$criteria->addSelectColumn(ClassePeer::ECTS_CODE_PARCOURS);

		$criteria->addSelectColumn(ClassePeer::ECTS_DOMAINES_ETUDE);

		$criteria->addSelectColumn(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION);

	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(ClassePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ClassePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     Classe
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ClassePeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return ClassePeer::populateObjects(ClassePeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			ClassePeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      Classe $value A Classe object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(Classe $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A Classe object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof Classe) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Classe object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     Classe Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol + 0] === null) {
			return null;
		}
		return (string) $row[$startcol + 0];
	}

	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = ClassePeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = ClassePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = ClassePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				ClassePeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * This uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass()
	{
		return ClassePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a Classe or Criteria object.
	 *
	 * @param      mixed $values Criteria or Classe object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from Classe object
		}

		if ($criteria->containsKey(ClassePeer::ID) && $criteria->keyContainsValue(ClassePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.ClassePeer::ID.')');
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a Classe or Criteria object.
	 *
	 * @param      mixed $values Criteria or Classe object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(ClassePeer::ID);
			$selectCriteria->add(ClassePeer::ID, $criteria->remove(ClassePeer::ID), $comparison);

		} else { // $values is Classe object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the classes table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += ClassePeer::doOnDeleteCascade(new Criteria(ClassePeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(ClassePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a Classe or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Classe object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			ClassePeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof Classe) {
			// invalidate the cache for this single object
			ClassePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ClassePeer::ID, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				ClassePeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += ClassePeer::doOnDeleteCascade($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					ClassePeer::clearInstancePool();
				} else { // it's a PK or object
					ClassePeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in JGroupesClassesPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JGroupesClassesPeer::clearInstancePool();

			// invalidate objects in JEleveClassePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JEleveClassePeer::clearInstancePool();

			// invalidate objects in JEleveProfesseurPrincipalPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JEleveProfesseurPrincipalPeer::clearInstancePool();

			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * This is a method for emulating ON DELETE CASCADE for DBs that don't support this
	 * feature (like MySQL or SQLite).
	 *
	 * This method is not very speedy because it must perform a query first to get
	 * the implicated records and then perform the deletes by calling those Peer classes.
	 *
	 * This method should be used within a transaction if possible.
	 *
	 * @param      Criteria $criteria
	 * @param      PropelPDO $con
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	protected static function doOnDeleteCascade(Criteria $criteria, PropelPDO $con)
	{
		// initialize var to track total num of affected rows
		$affectedRows = 0;

		// first find the objects that are implicated by the $criteria
		$objects = ClassePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JGroupesClasses objects
			$c = new Criteria(JGroupesClassesPeer::DATABASE_NAME);
			
			$c->add(JGroupesClassesPeer::ID_CLASSE, $obj->getId());
			$affectedRows += JGroupesClassesPeer::doDelete($c, $con);

			// delete related JEleveClasse objects
			$c = new Criteria(JEleveClassePeer::DATABASE_NAME);
			
			$c->add(JEleveClassePeer::ID_CLASSE, $obj->getId());
			$affectedRows += JEleveClassePeer::doDelete($c, $con);

			// delete related JEleveProfesseurPrincipal objects
			$c = new Criteria(JEleveProfesseurPrincipalPeer::DATABASE_NAME);
			
			$c->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $obj->getId());
			$affectedRows += JEleveProfesseurPrincipalPeer::doDelete($c, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given Classe object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Classe $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(Classe $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ClassePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ClassePeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(ClassePeer::DATABASE_NAME, ClassePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     Classe
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = ClassePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		$criteria->add(ClassePeer::ID, $pk);

		$v = ClassePeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
			$criteria->add(ClassePeer::ID, $pks, Criteria::IN);
			$objs = ClassePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseClassePeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the ClassePeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the ClassePeer class:
//
// Propel::getDatabaseMap(ClassePeer::DATABASE_NAME)->addTableBuilder(ClassePeer::TABLE_NAME, ClassePeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseClassePeer::DATABASE_NAME)->addTableBuilder(BaseClassePeer::TABLE_NAME, BaseClassePeer::getMapBuilder());

