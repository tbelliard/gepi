<?php


/**
 * Base static class for performing query and update operations on the 'archivage_ects' table.
 *
 * Enregistrement d'archive pour les credits ECTS, dont le rapport n'est edite qu'au depart de l'eleve
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseArchiveEctsPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'archivage_ects';

	/** the related Propel class for this table */
	const OM_CLASS = 'ArchiveEcts';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.ArchiveEcts';

	/** the related TableMap class for this table */
	const TM_CLASS = 'ArchiveEctsTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 11;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 11;

	/** the column name for the ID field */
	const ID = 'archivage_ects.ID';

	/** the column name for the ANNEE field */
	const ANNEE = 'archivage_ects.ANNEE';

	/** the column name for the INE field */
	const INE = 'archivage_ects.INE';

	/** the column name for the CLASSE field */
	const CLASSE = 'archivage_ects.CLASSE';

	/** the column name for the NUM_PERIODE field */
	const NUM_PERIODE = 'archivage_ects.NUM_PERIODE';

	/** the column name for the NOM_PERIODE field */
	const NOM_PERIODE = 'archivage_ects.NOM_PERIODE';

	/** the column name for the SPECIAL field */
	const SPECIAL = 'archivage_ects.SPECIAL';

	/** the column name for the MATIERE field */
	const MATIERE = 'archivage_ects.MATIERE';

	/** the column name for the PROFS field */
	const PROFS = 'archivage_ects.PROFS';

	/** the column name for the VALEUR field */
	const VALEUR = 'archivage_ects.VALEUR';

	/** the column name for the MENTION field */
	const MENTION = 'archivage_ects.MENTION';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of ArchiveEcts objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array ArchiveEcts[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Annee', 'Ine', 'Classe', 'NumPeriode', 'NomPeriode', 'Special', 'Matiere', 'Profs', 'Valeur', 'Mention', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'annee', 'ine', 'classe', 'numPeriode', 'nomPeriode', 'special', 'matiere', 'profs', 'valeur', 'mention', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::ANNEE, self::INE, self::CLASSE, self::NUM_PERIODE, self::NOM_PERIODE, self::SPECIAL, self::MATIERE, self::PROFS, self::VALEUR, self::MENTION, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'ANNEE', 'INE', 'CLASSE', 'NUM_PERIODE', 'NOM_PERIODE', 'SPECIAL', 'MATIERE', 'PROFS', 'VALEUR', 'MENTION', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'annee', 'ine', 'classe', 'num_periode', 'nom_periode', 'special', 'matiere', 'profs', 'valeur', 'mention', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Annee' => 1, 'Ine' => 2, 'Classe' => 3, 'NumPeriode' => 4, 'NomPeriode' => 5, 'Special' => 6, 'Matiere' => 7, 'Profs' => 8, 'Valeur' => 9, 'Mention' => 10, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'annee' => 1, 'ine' => 2, 'classe' => 3, 'numPeriode' => 4, 'nomPeriode' => 5, 'special' => 6, 'matiere' => 7, 'profs' => 8, 'valeur' => 9, 'mention' => 10, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::ANNEE => 1, self::INE => 2, self::CLASSE => 3, self::NUM_PERIODE => 4, self::NOM_PERIODE => 5, self::SPECIAL => 6, self::MATIERE => 7, self::PROFS => 8, self::VALEUR => 9, self::MENTION => 10, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'ANNEE' => 1, 'INE' => 2, 'CLASSE' => 3, 'NUM_PERIODE' => 4, 'NOM_PERIODE' => 5, 'SPECIAL' => 6, 'MATIERE' => 7, 'PROFS' => 8, 'VALEUR' => 9, 'MENTION' => 10, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'annee' => 1, 'ine' => 2, 'classe' => 3, 'num_periode' => 4, 'nom_periode' => 5, 'special' => 6, 'matiere' => 7, 'profs' => 8, 'valeur' => 9, 'mention' => 10, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
	 * @param      string $column The column name for current table. (i.e. ArchiveEctsPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(ArchiveEctsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(ArchiveEctsPeer::ID);
			$criteria->addSelectColumn(ArchiveEctsPeer::ANNEE);
			$criteria->addSelectColumn(ArchiveEctsPeer::INE);
			$criteria->addSelectColumn(ArchiveEctsPeer::CLASSE);
			$criteria->addSelectColumn(ArchiveEctsPeer::NUM_PERIODE);
			$criteria->addSelectColumn(ArchiveEctsPeer::NOM_PERIODE);
			$criteria->addSelectColumn(ArchiveEctsPeer::SPECIAL);
			$criteria->addSelectColumn(ArchiveEctsPeer::MATIERE);
			$criteria->addSelectColumn(ArchiveEctsPeer::PROFS);
			$criteria->addSelectColumn(ArchiveEctsPeer::VALEUR);
			$criteria->addSelectColumn(ArchiveEctsPeer::MENTION);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.ANNEE');
			$criteria->addSelectColumn($alias . '.INE');
			$criteria->addSelectColumn($alias . '.CLASSE');
			$criteria->addSelectColumn($alias . '.NUM_PERIODE');
			$criteria->addSelectColumn($alias . '.NOM_PERIODE');
			$criteria->addSelectColumn($alias . '.SPECIAL');
			$criteria->addSelectColumn($alias . '.MATIERE');
			$criteria->addSelectColumn($alias . '.PROFS');
			$criteria->addSelectColumn($alias . '.VALEUR');
			$criteria->addSelectColumn($alias . '.MENTION');
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
		$criteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ArchiveEctsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     ArchiveEcts
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = ArchiveEctsPeer::doSelect($critcopy, $con);
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
		return ArchiveEctsPeer::populateObjects(ArchiveEctsPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			ArchiveEctsPeer::addSelectColumns($criteria);
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
	 * @param      ArchiveEcts $value A ArchiveEcts object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getId(), (string) $obj->getIne(), (string) $obj->getNumPeriode(), (string) $obj->getSpecial()));
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
	 * @param      mixed $value A ArchiveEcts object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof ArchiveEcts) {
				$key = serialize(array((string) $value->getId(), (string) $value->getIne(), (string) $value->getNumPeriode(), (string) $value->getSpecial()));
			} elseif (is_array($value) && count($value) === 4) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1], (string) $value[2], (string) $value[3]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or ArchiveEcts object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     ArchiveEcts Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to archivage_ects
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
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
		if ($row[$startcol] === null && $row[$startcol + 2] === null && $row[$startcol + 4] === null && $row[$startcol + 6] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 2], (string) $row[$startcol + 4], (string) $row[$startcol + 6]));
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
		return array((int) $row[$startcol], (string) $row[$startcol + 2], (int) $row[$startcol + 4], (string) $row[$startcol + 6]);
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
		$cls = ArchiveEctsPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = ArchiveEctsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = ArchiveEctsPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				ArchiveEctsPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (ArchiveEcts object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = ArchiveEctsPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = ArchiveEctsPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + ArchiveEctsPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = ArchiveEctsPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			ArchiveEctsPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Eleve table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEleve(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ArchiveEctsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(ArchiveEctsPeer::INE, ElevePeer::NO_GEP, $join_behavior);

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
	 * Selects a collection of ArchiveEcts objects pre-filled with their Eleve objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of ArchiveEcts objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEleve(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		ArchiveEctsPeer::addSelectColumns($criteria);
		$startcol = ArchiveEctsPeer::NUM_HYDRATE_COLUMNS;
		ElevePeer::addSelectColumns($criteria);

		$criteria->addJoin(ArchiveEctsPeer::INE, ElevePeer::NO_GEP, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = ArchiveEctsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = ArchiveEctsPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = ArchiveEctsPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				ArchiveEctsPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = ElevePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (ArchiveEcts) to $obj2 (Eleve)
				$obj2->addArchiveEcts($obj1);

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
		$criteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			ArchiveEctsPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(ArchiveEctsPeer::INE, ElevePeer::NO_GEP, $join_behavior);

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
	 * Selects a collection of ArchiveEcts objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of ArchiveEcts objects.
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

		ArchiveEctsPeer::addSelectColumns($criteria);
		$startcol2 = ArchiveEctsPeer::NUM_HYDRATE_COLUMNS;

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + ElevePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(ArchiveEctsPeer::INE, ElevePeer::NO_GEP, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = ArchiveEctsPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = ArchiveEctsPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = ArchiveEctsPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				ArchiveEctsPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Eleve rows

			$key2 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = ElevePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ElevePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					ElevePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (ArchiveEcts) to the collection in $obj2 (Eleve)
				$obj2->addArchiveEcts($obj1);
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
	  $dbMap = Propel::getDatabaseMap(BaseArchiveEctsPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseArchiveEctsPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new ArchiveEctsTableMap());
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
		return $withPrefix ? ArchiveEctsPeer::CLASS_DEFAULT : ArchiveEctsPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a ArchiveEcts or Criteria object.
	 *
	 * @param      mixed $values Criteria or ArchiveEcts object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from ArchiveEcts object
		}

		if ($criteria->containsKey(ArchiveEctsPeer::ID) && $criteria->keyContainsValue(ArchiveEctsPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.ArchiveEctsPeer::ID.')');
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
	 * Performs an UPDATE on the database, given a ArchiveEcts or Criteria object.
	 *
	 * @param      mixed $values Criteria or ArchiveEcts object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(ArchiveEctsPeer::ID);
			$value = $criteria->remove(ArchiveEctsPeer::ID);
			if ($value) {
				$selectCriteria->add(ArchiveEctsPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(ArchiveEctsPeer::INE);
			$value = $criteria->remove(ArchiveEctsPeer::INE);
			if ($value) {
				$selectCriteria->add(ArchiveEctsPeer::INE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(ArchiveEctsPeer::NUM_PERIODE);
			$value = $criteria->remove(ArchiveEctsPeer::NUM_PERIODE);
			if ($value) {
				$selectCriteria->add(ArchiveEctsPeer::NUM_PERIODE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(ArchiveEctsPeer::SPECIAL);
			$value = $criteria->remove(ArchiveEctsPeer::SPECIAL);
			if ($value) {
				$selectCriteria->add(ArchiveEctsPeer::SPECIAL, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(ArchiveEctsPeer::TABLE_NAME);
			}

		} else { // $values is ArchiveEcts object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the archivage_ects table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(ArchiveEctsPeer::TABLE_NAME, $con, ArchiveEctsPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			ArchiveEctsPeer::clearInstancePool();
			ArchiveEctsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a ArchiveEcts or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or ArchiveEcts object or primary key or array of primary keys
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
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			ArchiveEctsPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof ArchiveEcts) { // it's a model object
			// invalidate the cache for this single object
			ArchiveEctsPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			// primary key is composite; we therefore, expect
			// the primary key passed to be an array of pkey values
			if (count($values) == count($values, COUNT_RECURSIVE)) {
				// array is not multi-dimensional
				$values = array($values);
			}
			foreach ($values as $value) {
				$criterion = $criteria->getNewCriterion(ArchiveEctsPeer::ID, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(ArchiveEctsPeer::INE, $value[1]));
				$criterion->addAnd($criteria->getNewCriterion(ArchiveEctsPeer::NUM_PERIODE, $value[2]));
				$criterion->addAnd($criteria->getNewCriterion(ArchiveEctsPeer::SPECIAL, $value[3]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				ArchiveEctsPeer::removeInstanceFromPool($value);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			ArchiveEctsPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given ArchiveEcts object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      ArchiveEcts $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(ArchiveEctsPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(ArchiveEctsPeer::TABLE_NAME);

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

		return BasePeer::doValidate(ArchiveEctsPeer::DATABASE_NAME, ArchiveEctsPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $id
	 * @param      string $ine
	 * @param      int $num_periode
	 * @param      string $special
	 * @param      PropelPDO $con
	 * @return     ArchiveEcts
	 */
	public static function retrieveByPK($id, $ine, $num_periode, $special, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $id, (string) $ine, (string) $num_periode, (string) $special));
 		if (null !== ($obj = ArchiveEctsPeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(ArchiveEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(ArchiveEctsPeer::DATABASE_NAME);
		$criteria->add(ArchiveEctsPeer::ID, $id);
		$criteria->add(ArchiveEctsPeer::INE, $ine);
		$criteria->add(ArchiveEctsPeer::NUM_PERIODE, $num_periode);
		$criteria->add(ArchiveEctsPeer::SPECIAL, $special);
		$v = ArchiveEctsPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseArchiveEctsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseArchiveEctsPeer::buildTableMap();

