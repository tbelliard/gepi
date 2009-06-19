<?php

/**
 * Base static class for performing query and update operations on the 'ct_devoirs_entry' table.
 *
 * Travail Ã  faire (devoir) cahier de texte
 *
 * @package    gepi.om
 */
abstract class BaseCahierTexteTravailAFairePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'ct_devoirs_entry';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.CahierTexteTravailAFaire';

	/** The total number of columns. */
	const NUM_COLUMNS = 7;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID_CT field */
	const ID_CT = 'ct_devoirs_entry.ID_CT';

	/** the column name for the DATE_CT field */
	const DATE_CT = 'ct_devoirs_entry.DATE_CT';

	/** the column name for the CONTENU field */
	const CONTENU = 'ct_devoirs_entry.CONTENU';

	/** the column name for the VISE field */
	const VISE = 'ct_devoirs_entry.VISE';

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'ct_devoirs_entry.ID_GROUPE';

	/** the column name for the ID_LOGIN field */
	const ID_LOGIN = 'ct_devoirs_entry.ID_LOGIN';

	/** the column name for the ID_SEQUENCE field */
	const ID_SEQUENCE = 'ct_devoirs_entry.ID_SEQUENCE';

	/**
	 * An identiy map to hold any loaded instances of CahierTexteTravailAFaire objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CahierTexteTravailAFaire[]
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
		BasePeer::TYPE_PHPNAME => array ('IdCt', 'DateCt', 'Contenu', 'Vise', 'IdGroupe', 'IdLogin', 'IdSequence', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCt', 'dateCt', 'contenu', 'vise', 'idGroupe', 'idLogin', 'idSequence', ),
		BasePeer::TYPE_COLNAME => array (self::ID_CT, self::DATE_CT, self::CONTENU, self::VISE, self::ID_GROUPE, self::ID_LOGIN, self::ID_SEQUENCE, ),
		BasePeer::TYPE_FIELDNAME => array ('id_ct', 'date_ct', 'contenu', 'vise', 'id_groupe', 'id_login', 'id_sequence', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('IdCt' => 0, 'DateCt' => 1, 'Contenu' => 2, 'Vise' => 3, 'IdGroupe' => 4, 'IdLogin' => 5, 'IdSequence' => 6, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCt' => 0, 'dateCt' => 1, 'contenu' => 2, 'vise' => 3, 'idGroupe' => 4, 'idLogin' => 5, 'idSequence' => 6, ),
		BasePeer::TYPE_COLNAME => array (self::ID_CT => 0, self::DATE_CT => 1, self::CONTENU => 2, self::VISE => 3, self::ID_GROUPE => 4, self::ID_LOGIN => 5, self::ID_SEQUENCE => 6, ),
		BasePeer::TYPE_FIELDNAME => array ('id_ct' => 0, 'date_ct' => 1, 'contenu' => 2, 'vise' => 3, 'id_groupe' => 4, 'id_login' => 5, 'id_sequence' => 6, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new CahierTexteTravailAFaireMapBuilder();
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
	 * @param      string $column The column name for current table. (i.e. CahierTexteTravailAFairePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CahierTexteTravailAFairePeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::ID_CT);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::DATE_CT);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::CONTENU);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::VISE);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::ID_GROUPE);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::ID_LOGIN);

		$criteria->addSelectColumn(CahierTexteTravailAFairePeer::ID_SEQUENCE);

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
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     CahierTexteTravailAFaire
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CahierTexteTravailAFairePeer::doSelect($critcopy, $con);
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
		return CahierTexteTravailAFairePeer::populateObjects(CahierTexteTravailAFairePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
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
	 * @param      CahierTexteTravailAFaire $value A CahierTexteTravailAFaire object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CahierTexteTravailAFaire $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIdCt();
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
	 * @param      mixed $value A CahierTexteTravailAFaire object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CahierTexteTravailAFaire) {
				$key = (string) $value->getIdCt();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CahierTexteTravailAFaire object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     CahierTexteTravailAFaire Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		$cls = CahierTexteTravailAFairePeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CahierTexteTravailAFairePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CahierTexteTravailAFairePeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinUtilisateurProfessionnel(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related CahierTexteSequence table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCahierTexteSequence(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
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
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with their Groupe objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);
		GroupePeer::addSelectColumns($c);

		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = GroupePeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to $obj2 (Groupe)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinUtilisateurProfessionnel(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($c);

		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = UtilisateurProfessionnelPeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to $obj2 (UtilisateurProfessionnel)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with their CahierTexteSequence objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCahierTexteSequence(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);
		CahierTexteSequencePeer::addSelectColumns($c);

		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CahierTexteSequencePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = CahierTexteSequencePeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CahierTexteSequencePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to $obj2 (CahierTexteSequence)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $c
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
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
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
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol2 = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		CahierTexteSequencePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (CahierTexteSequencePeer::NUM_COLUMNS - CahierTexteSequencePeer::NUM_LAZY_LOAD_COLUMNS);

		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$c->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Groupe rows

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteTravailAFaire($obj1);
			} // if joined row not null

			// Add objects for joined UtilisateurProfessionnel rows

			$key3 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = UtilisateurProfessionnelPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addCahierTexteTravailAFaire($obj1);
			} // if joined row not null

			// Add objects for joined CahierTexteSequence rows

			$key4 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = CahierTexteSequencePeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$omClass = CahierTexteSequencePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CahierTexteSequencePeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj4 (CahierTexteSequence)
				$obj4->addCahierTexteTravailAFaire($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptUtilisateurProfessionnel(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related CahierTexteSequence table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCahierTexteSequence(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CahierTexteTravailAFairePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CahierTexteTravailAFairePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with all related objects except Groupe.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol2 = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		CahierTexteSequencePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (CahierTexteSequencePeer::NUM_COLUMNS - CahierTexteSequencePeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined UtilisateurProfessionnel rows

				$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

				// Add objects for joined CahierTexteSequence rows

				$key3 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CahierTexteSequencePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = CahierTexteSequencePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CahierTexteSequencePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj3 (CahierTexteSequence)
				$obj3->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptUtilisateurProfessionnel(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol2 = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		CahierTexteSequencePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (CahierTexteSequencePeer::NUM_COLUMNS - CahierTexteSequencePeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_SEQUENCE,), array(CahierTexteSequencePeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

				// Add objects for joined CahierTexteSequence rows

				$key3 = CahierTexteSequencePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CahierTexteSequencePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = CahierTexteSequencePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CahierTexteSequencePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj3 (CahierTexteSequence)
				$obj3->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CahierTexteTravailAFaire objects pre-filled with all related objects except CahierTexteSequence.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CahierTexteTravailAFaire objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCahierTexteSequence(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		CahierTexteTravailAFairePeer::addSelectColumns($c);
		$startcol2 = (CahierTexteTravailAFairePeer::NUM_COLUMNS - CahierTexteTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(CahierTexteTravailAFairePeer::ID_LOGIN,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CahierTexteTravailAFairePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CahierTexteTravailAFairePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = CahierTexteTravailAFairePeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				CahierTexteTravailAFairePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj2 (Groupe)
				$obj2->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key3 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = UtilisateurProfessionnelPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CahierTexteTravailAFaire) to the collection in $obj3 (UtilisateurProfessionnel)
				$obj3->addCahierTexteTravailAFaire($obj1);

			} // if joined row is not null

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
		return CahierTexteTravailAFairePeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a CahierTexteTravailAFaire or Criteria object.
	 *
	 * @param      mixed $values Criteria or CahierTexteTravailAFaire object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CahierTexteTravailAFaire object
		}

		if ($criteria->containsKey(CahierTexteTravailAFairePeer::ID_CT) && $criteria->keyContainsValue(CahierTexteTravailAFairePeer::ID_CT) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CahierTexteTravailAFairePeer::ID_CT.')');
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
	 * Method perform an UPDATE on the database, given a CahierTexteTravailAFaire or Criteria object.
	 *
	 * @param      mixed $values Criteria or CahierTexteTravailAFaire object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CahierTexteTravailAFairePeer::ID_CT);
			$selectCriteria->add(CahierTexteTravailAFairePeer::ID_CT, $criteria->remove(CahierTexteTravailAFairePeer::ID_CT), $comparison);

		} else { // $values is CahierTexteTravailAFaire object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the ct_devoirs_entry table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += CahierTexteTravailAFairePeer::doOnDeleteCascade(new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(CahierTexteTravailAFairePeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CahierTexteTravailAFaire or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CahierTexteTravailAFaire object or primary key or array of primary keys
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
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CahierTexteTravailAFairePeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CahierTexteTravailAFaire) {
			// invalidate the cache for this single object
			CahierTexteTravailAFairePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CahierTexteTravailAFairePeer::ID_CT, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				CahierTexteTravailAFairePeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += CahierTexteTravailAFairePeer::doOnDeleteCascade($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					CahierTexteTravailAFairePeer::clearInstancePool();
				} else { // it's a PK or object
					CahierTexteTravailAFairePeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in CahierTexteTravailAFaireFichierJointPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			CahierTexteTravailAFaireFichierJointPeer::clearInstancePool();

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
		$objects = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related CahierTexteTravailAFaireFichierJoint objects
			$c = new Criteria(CahierTexteTravailAFaireFichierJointPeer::DATABASE_NAME);
			
			$c->add(CahierTexteTravailAFaireFichierJointPeer::ID_CT_DEVOIR, $obj->getIdCt());
			$affectedRows += CahierTexteTravailAFaireFichierJointPeer::doDelete($c, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given CahierTexteTravailAFaire object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CahierTexteTravailAFaire $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CahierTexteTravailAFaire $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CahierTexteTravailAFairePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CahierTexteTravailAFairePeer::TABLE_NAME);

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

		return BasePeer::doValidate(CahierTexteTravailAFairePeer::DATABASE_NAME, CahierTexteTravailAFairePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CahierTexteTravailAFaire
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CahierTexteTravailAFairePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME);
		$criteria->add(CahierTexteTravailAFairePeer::ID_CT, $pk);

		$v = CahierTexteTravailAFairePeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(CahierTexteTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CahierTexteTravailAFairePeer::DATABASE_NAME);
			$criteria->add(CahierTexteTravailAFairePeer::ID_CT, $pks, Criteria::IN);
			$objs = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCahierTexteTravailAFairePeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the CahierTexteTravailAFairePeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the CahierTexteTravailAFairePeer class:
//
// Propel::getDatabaseMap(CahierTexteTravailAFairePeer::DATABASE_NAME)->addTableBuilder(CahierTexteTravailAFairePeer::TABLE_NAME, CahierTexteTravailAFairePeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseCahierTexteTravailAFairePeer::DATABASE_NAME)->addTableBuilder(BaseCahierTexteTravailAFairePeer::TABLE_NAME, BaseCahierTexteTravailAFairePeer::getMapBuilder());

