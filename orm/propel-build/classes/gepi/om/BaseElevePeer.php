<?php


/**
 * Base static class for performing query and update operations on the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseElevePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'eleves';

	/** the related Propel class for this table */
	const OM_CLASS = 'Eleve';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.Eleve';

	/** the related TableMap class for this table */
	const TM_CLASS = 'EleveTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 14;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 14;

	/** the column name for the NO_GEP field */
	const NO_GEP = 'eleves.NO_GEP';

	/** the column name for the LOGIN field */
	const LOGIN = 'eleves.LOGIN';

	/** the column name for the NOM field */
	const NOM = 'eleves.NOM';

	/** the column name for the PRENOM field */
	const PRENOM = 'eleves.PRENOM';

	/** the column name for the SEXE field */
	const SEXE = 'eleves.SEXE';

	/** the column name for the NAISSANCE field */
	const NAISSANCE = 'eleves.NAISSANCE';

	/** the column name for the LIEU_NAISSANCE field */
	const LIEU_NAISSANCE = 'eleves.LIEU_NAISSANCE';

	/** the column name for the ELENOET field */
	const ELENOET = 'eleves.ELENOET';

	/** the column name for the ERENO field */
	const ERENO = 'eleves.ERENO';

	/** the column name for the ELE_ID field */
	const ELE_ID = 'eleves.ELE_ID';

	/** the column name for the EMAIL field */
	const EMAIL = 'eleves.EMAIL';

	/** the column name for the ID_ELEVE field */
	const ID_ELEVE = 'eleves.ID_ELEVE';

	/** the column name for the DATE_SORTIE field */
	const DATE_SORTIE = 'eleves.DATE_SORTIE';

	/** the column name for the MEF_CODE field */
	const MEF_CODE = 'eleves.MEF_CODE';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';
	
	/**
	 * An identiy map to hold any loaded instances of Eleve objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array Eleve[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('NoGep', 'Login', 'Nom', 'Prenom', 'Sexe', 'Naissance', 'LieuNaissance', 'Elenoet', 'Ereno', 'EleId', 'Email', 'IdEleve', 'DateSortie', 'MefCode', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('noGep', 'login', 'nom', 'prenom', 'sexe', 'naissance', 'lieuNaissance', 'elenoet', 'ereno', 'eleId', 'email', 'idEleve', 'dateSortie', 'mefCode', ),
		BasePeer::TYPE_COLNAME => array (self::NO_GEP, self::LOGIN, self::NOM, self::PRENOM, self::SEXE, self::NAISSANCE, self::LIEU_NAISSANCE, self::ELENOET, self::ERENO, self::ELE_ID, self::EMAIL, self::ID_ELEVE, self::DATE_SORTIE, self::MEF_CODE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('NO_GEP', 'LOGIN', 'NOM', 'PRENOM', 'SEXE', 'NAISSANCE', 'LIEU_NAISSANCE', 'ELENOET', 'ERENO', 'ELE_ID', 'EMAIL', 'ID_ELEVE', 'DATE_SORTIE', 'MEF_CODE', ),
		BasePeer::TYPE_FIELDNAME => array ('no_gep', 'login', 'nom', 'prenom', 'sexe', 'naissance', 'lieu_naissance', 'elenoet', 'ereno', 'ele_id', 'email', 'id_eleve', 'date_sortie', 'mef_code', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('NoGep' => 0, 'Login' => 1, 'Nom' => 2, 'Prenom' => 3, 'Sexe' => 4, 'Naissance' => 5, 'LieuNaissance' => 6, 'Elenoet' => 7, 'Ereno' => 8, 'EleId' => 9, 'Email' => 10, 'IdEleve' => 11, 'DateSortie' => 12, 'MefCode' => 13, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('noGep' => 0, 'login' => 1, 'nom' => 2, 'prenom' => 3, 'sexe' => 4, 'naissance' => 5, 'lieuNaissance' => 6, 'elenoet' => 7, 'ereno' => 8, 'eleId' => 9, 'email' => 10, 'idEleve' => 11, 'dateSortie' => 12, 'mefCode' => 13, ),
		BasePeer::TYPE_COLNAME => array (self::NO_GEP => 0, self::LOGIN => 1, self::NOM => 2, self::PRENOM => 3, self::SEXE => 4, self::NAISSANCE => 5, self::LIEU_NAISSANCE => 6, self::ELENOET => 7, self::ERENO => 8, self::ELE_ID => 9, self::EMAIL => 10, self::ID_ELEVE => 11, self::DATE_SORTIE => 12, self::MEF_CODE => 13, ),
		BasePeer::TYPE_RAW_COLNAME => array ('NO_GEP' => 0, 'LOGIN' => 1, 'NOM' => 2, 'PRENOM' => 3, 'SEXE' => 4, 'NAISSANCE' => 5, 'LIEU_NAISSANCE' => 6, 'ELENOET' => 7, 'ERENO' => 8, 'ELE_ID' => 9, 'EMAIL' => 10, 'ID_ELEVE' => 11, 'DATE_SORTIE' => 12, 'MEF_CODE' => 13, ),
		BasePeer::TYPE_FIELDNAME => array ('no_gep' => 0, 'login' => 1, 'nom' => 2, 'prenom' => 3, 'sexe' => 4, 'naissance' => 5, 'lieu_naissance' => 6, 'elenoet' => 7, 'ereno' => 8, 'ele_id' => 9, 'email' => 10, 'id_eleve' => 11, 'date_sortie' => 12, 'mef_code' => 13, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

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
	 * @param      string $column The column name for current table. (i.e. ElevePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ElevePeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      Criteria $criteria object containing the columns to add.
	 * @param      string   $alias    optional table alias
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(ElevePeer::NO_GEP);
			$criteria->addSelectColumn(ElevePeer::LOGIN);
			$criteria->addSelectColumn(ElevePeer::NOM);
			$criteria->addSelectColumn(ElevePeer::PRENOM);
			$criteria->addSelectColumn(ElevePeer::SEXE);
			$criteria->addSelectColumn(ElevePeer::NAISSANCE);
			$criteria->addSelectColumn(ElevePeer::LIEU_NAISSANCE);
			$criteria->addSelectColumn(ElevePeer::ELENOET);
			$criteria->addSelectColumn(ElevePeer::ERENO);
			$criteria->addSelectColumn(ElevePeer::ELE_ID);
			$criteria->addSelectColumn(ElevePeer::EMAIL);
			$criteria->addSelectColumn(ElevePeer::ID_ELEVE);
			$criteria->addSelectColumn(ElevePeer::DATE_SORTIE);
			$criteria->addSelectColumn(ElevePeer::MEF_CODE);
		} else {
			$criteria->addSelectColumn($alias . '.NO_GEP');
			$criteria->addSelectColumn($alias . '.LOGIN');
			$criteria->addSelectColumn($alias . '.NOM');
			$criteria->addSelectColumn($alias . '.PRENOM');
			$criteria->addSelectColumn($alias . '.SEXE');
			$criteria->addSelectColumn($alias . '.NAISSANCE');
			$criteria->addSelectColumn($alias . '.LIEU_NAISSANCE');
			$criteria->addSelectColumn($alias . '.ELENOET');
			$criteria->addSelectColumn($alias . '.ERENO');
			$criteria->addSelectColumn($alias . '.ELE_ID');
			$criteria->addSelectColumn($alias . '.EMAIL');
			$criteria->addSelectColumn($alias . '.ID_ELEVE');
			$criteria->addSelectColumn($alias . '.DATE_SORTIE');
			$criteria->addSelectColumn($alias . '.MEF_CODE');
		}
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
		$criteria->setPrimaryTableName(ElevePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ElevePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * Selects one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     Eleve
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ElevePeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Selects several row from the DB.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return ElevePeer::populateObjects(ElevePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			ElevePeer::addSelectColumns($criteria);
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
	 * @param      Eleve $value A Eleve object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIdEleve();
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
	 * @param      mixed $value A Eleve object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof Eleve) {
				$key = (string) $value->getIdEleve();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Eleve object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     Eleve Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to eleves
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in JEleveClassePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JEleveClassePeer::clearInstancePool();
		// Invalidate objects in JEleveCpePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JEleveCpePeer::clearInstancePool();
		// Invalidate objects in JEleveGroupePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JEleveGroupePeer::clearInstancePool();
		// Invalidate objects in JEleveProfesseurPrincipalPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JEleveProfesseurPrincipalPeer::clearInstancePool();
		// Invalidate objects in EleveRegimeDoublantPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		EleveRegimeDoublantPeer::clearInstancePool();
		// Invalidate objects in ResponsableInformationPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		ResponsableInformationPeer::clearInstancePool();
		// Invalidate objects in JEleveAncienEtablissementPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JEleveAncienEtablissementPeer::clearInstancePool();
		// Invalidate objects in JAidElevesPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JAidElevesPeer::clearInstancePool();
		// Invalidate objects in AbsenceEleveSaisiePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceEleveSaisiePeer::clearInstancePool();
		// Invalidate objects in AbsenceAgregationDecomptePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceAgregationDecomptePeer::clearInstancePool();
		// Invalidate objects in CreditEctsPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CreditEctsPeer::clearInstancePool();
		// Invalidate objects in CreditEctsGlobalPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CreditEctsGlobalPeer::clearInstancePool();
		// Invalidate objects in ArchiveEctsPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		ArchiveEctsPeer::clearInstancePool();
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
		if ($row[$startcol + 11] === null) {
			return null;
		}
		return (string) $row[$startcol + 11];
	}

	/**
	 * Retrieves the primary key from the DB resultset row 
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, an array of the primary key columns will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return (int) $row[$startcol + 11];
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
		$cls = ElevePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = ElevePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = ElevePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				ElevePeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Populates an object of the default type or an object that inherit from the default.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (Eleve object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = ElevePeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + ElevePeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = ElevePeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			ElevePeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Mef table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinMef(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(ElevePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ElevePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(ElevePeer::MEF_CODE, MefPeer::MEF_CODE, $join_behavior);

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
	 * Selects a collection of Eleve objects pre-filled with their Mef objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Eleve objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinMef(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		ElevePeer::addSelectColumns($criteria);
		$startcol = ElevePeer::NUM_HYDRATE_COLUMNS;
		MefPeer::addSelectColumns($criteria);

		$criteria->addJoin(ElevePeer::MEF_CODE, MefPeer::MEF_CODE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = ElevePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = ElevePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = ElevePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				ElevePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = MefPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = MefPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = MefPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					MefPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (Eleve) to $obj2 (Mef)
				$obj2->addEleve($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(ElevePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ElevePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(ElevePeer::MEF_CODE, MefPeer::MEF_CODE, $join_behavior);

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
	 * Selects a collection of Eleve objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of Eleve objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		ElevePeer::addSelectColumns($criteria);
		$startcol2 = ElevePeer::NUM_HYDRATE_COLUMNS;

		MefPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + MefPeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(ElevePeer::MEF_CODE, MefPeer::MEF_CODE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = ElevePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = ElevePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = ElevePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				ElevePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Mef rows

			$key2 = MefPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = MefPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = MefPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					MefPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (Eleve) to the collection in $obj2 (Mef)
				$obj2->addEleve($obj1);
			} // if joined row not null

			$results[] = $obj1;
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
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseElevePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseElevePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new EleveTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? ElevePeer::CLASS_DEFAULT : ElevePeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a Eleve or Criteria object.
	 *
	 * @param      mixed $values Criteria or Eleve object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from Eleve object
		}

		if ($criteria->containsKey(ElevePeer::ID_ELEVE) && $criteria->keyContainsValue(ElevePeer::ID_ELEVE) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.ElevePeer::ID_ELEVE.')');
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
	 * Performs an UPDATE on the database, given a Eleve or Criteria object.
	 *
	 * @param      mixed $values Criteria or Eleve object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(ElevePeer::ID_ELEVE);
			$value = $criteria->remove(ElevePeer::ID_ELEVE);
			if ($value) {
				$selectCriteria->add(ElevePeer::ID_ELEVE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(ElevePeer::TABLE_NAME);
			}

		} else { // $values is Eleve object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the eleves table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += ElevePeer::doOnDeleteCascade(new Criteria(ElevePeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(ElevePeer::TABLE_NAME, $con, ElevePeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			ElevePeer::clearInstancePool();
			ElevePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a Eleve or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or Eleve object or primary key or array of primary keys
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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof Eleve) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(ElevePeer::ID_ELEVE, (array) $values, Criteria::IN);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			// cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
			$c = clone $criteria;
			$affectedRows += ElevePeer::doOnDeleteCascade($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				ElevePeer::clearInstancePool();
			} elseif ($values instanceof Eleve) { // it's a model object
				ElevePeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					ElevePeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			ElevePeer::clearRelatedInstancePool();
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
		$objects = ElevePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JEleveClasse objects
			$criteria = new Criteria(JEleveClassePeer::DATABASE_NAME);
			
			$criteria->add(JEleveClassePeer::LOGIN, $obj->getLogin());
			$affectedRows += JEleveClassePeer::doDelete($criteria, $con);

			// delete related JEleveCpe objects
			$criteria = new Criteria(JEleveCpePeer::DATABASE_NAME);
			
			$criteria->add(JEleveCpePeer::E_LOGIN, $obj->getLogin());
			$affectedRows += JEleveCpePeer::doDelete($criteria, $con);

			// delete related JEleveGroupe objects
			$criteria = new Criteria(JEleveGroupePeer::DATABASE_NAME);
			
			$criteria->add(JEleveGroupePeer::LOGIN, $obj->getLogin());
			$affectedRows += JEleveGroupePeer::doDelete($criteria, $con);

			// delete related JEleveProfesseurPrincipal objects
			$criteria = new Criteria(JEleveProfesseurPrincipalPeer::DATABASE_NAME);
			
			$criteria->add(JEleveProfesseurPrincipalPeer::LOGIN, $obj->getLogin());
			$affectedRows += JEleveProfesseurPrincipalPeer::doDelete($criteria, $con);

			// delete related EleveRegimeDoublant objects
			$criteria = new Criteria(EleveRegimeDoublantPeer::DATABASE_NAME);
			
			$criteria->add(EleveRegimeDoublantPeer::LOGIN, $obj->getLogin());
			$affectedRows += EleveRegimeDoublantPeer::doDelete($criteria, $con);

			// delete related ResponsableInformation objects
			$criteria = new Criteria(ResponsableInformationPeer::DATABASE_NAME);
			
			$criteria->add(ResponsableInformationPeer::ELE_ID, $obj->getEleId());
			$affectedRows += ResponsableInformationPeer::doDelete($criteria, $con);

			// delete related JEleveAncienEtablissement objects
			$criteria = new Criteria(JEleveAncienEtablissementPeer::DATABASE_NAME);
			
			$criteria->add(JEleveAncienEtablissementPeer::ID_ELEVE, $obj->getIdEleve());
			$affectedRows += JEleveAncienEtablissementPeer::doDelete($criteria, $con);

			// delete related JAidEleves objects
			$criteria = new Criteria(JAidElevesPeer::DATABASE_NAME);
			
			$criteria->add(JAidElevesPeer::LOGIN, $obj->getLogin());
			$affectedRows += JAidElevesPeer::doDelete($criteria, $con);

			// delete related AbsenceEleveSaisie objects
			$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
			
			$criteria->add(AbsenceEleveSaisiePeer::ELEVE_ID, $obj->getIdEleve());
			$affectedRows += AbsenceEleveSaisiePeer::doDelete($criteria, $con);

			// delete related AbsenceAgregationDecompte objects
			$criteria = new Criteria(AbsenceAgregationDecomptePeer::DATABASE_NAME);
			
			$criteria->add(AbsenceAgregationDecomptePeer::ELEVE_ID, $obj->getIdEleve());
			$affectedRows += AbsenceAgregationDecomptePeer::doDelete($criteria, $con);

			// delete related CreditEcts objects
			$criteria = new Criteria(CreditEctsPeer::DATABASE_NAME);
			
			$criteria->add(CreditEctsPeer::ID_ELEVE, $obj->getIdEleve());
			$affectedRows += CreditEctsPeer::doDelete($criteria, $con);

			// delete related CreditEctsGlobal objects
			$criteria = new Criteria(CreditEctsGlobalPeer::DATABASE_NAME);
			
			$criteria->add(CreditEctsGlobalPeer::ID_ELEVE, $obj->getIdEleve());
			$affectedRows += CreditEctsGlobalPeer::doDelete($criteria, $con);

			// delete related ArchiveEcts objects
			$criteria = new Criteria(ArchiveEctsPeer::DATABASE_NAME);
			
			$criteria->add(ArchiveEctsPeer::INE, $obj->getNoGep());
			$affectedRows += ArchiveEctsPeer::doDelete($criteria, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given Eleve object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      Eleve $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ElevePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ElevePeer::TABLE_NAME);

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

		return BasePeer::doValidate(ElevePeer::DATABASE_NAME, ElevePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     Eleve
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = ElevePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(ElevePeer::DATABASE_NAME);
		$criteria->add(ElevePeer::ID_ELEVE, $pk);

		$v = ElevePeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(ElevePeer::DATABASE_NAME);
			$criteria->add(ElevePeer::ID_ELEVE, $pks, Criteria::IN);
			$objs = ElevePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseElevePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseElevePeer::buildTableMap();

