<?php

/**
 * Base static class for performing query and update operations on the 'aid_config' table.
 *
 * Liste des categories d'AID (Activites inter-Disciplinaires)
 *
 * @package    gepi.om
 */
abstract class BaseAidConfigurationPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'aid_config';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AidConfiguration';

	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the NOM field */
	const NOM = 'aid_config.NOM';

	/** the column name for the NOM_COMPLET field */
	const NOM_COMPLET = 'aid_config.NOM_COMPLET';

	/** the column name for the NOTE_MAX field */
	const NOTE_MAX = 'aid_config.NOTE_MAX';

	/** the column name for the ORDER_DISPLAY1 field */
	const ORDER_DISPLAY1 = 'aid_config.ORDER_DISPLAY1';

	/** the column name for the ORDER_DISPLAY2 field */
	const ORDER_DISPLAY2 = 'aid_config.ORDER_DISPLAY2';

	/** the column name for the TYPE_NOTE field */
	const TYPE_NOTE = 'aid_config.TYPE_NOTE';

	/** the column name for the DISPLAY_BEGIN field */
	const DISPLAY_BEGIN = 'aid_config.DISPLAY_BEGIN';

	/** the column name for the DISPLAY_END field */
	const DISPLAY_END = 'aid_config.DISPLAY_END';

	/** the column name for the MESSAGE field */
	const MESSAGE = 'aid_config.MESSAGE';

	/** the column name for the DISPLAY_NOM field */
	const DISPLAY_NOM = 'aid_config.DISPLAY_NOM';

	/** the column name for the INDICE_AID field */
	const INDICE_AID = 'aid_config.INDICE_AID';

	/** the column name for the DISPLAY_BULLETIN field */
	const DISPLAY_BULLETIN = 'aid_config.DISPLAY_BULLETIN';

	/** the column name for the BULL_SIMPLIFIE field */
	const BULL_SIMPLIFIE = 'aid_config.BULL_SIMPLIFIE';

	/** the column name for the OUTILS_COMPLEMENTAIRES field */
	const OUTILS_COMPLEMENTAIRES = 'aid_config.OUTILS_COMPLEMENTAIRES';

	/** the column name for the FEUILLE_PRESENCE field */
	const FEUILLE_PRESENCE = 'aid_config.FEUILLE_PRESENCE';

	/**
	 * An identiy map to hold any loaded instances of AidConfiguration objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AidConfiguration[]
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
		BasePeer::TYPE_PHPNAME => array ('Nom', 'NomComplet', 'NoteMax', 'OrderDisplay1', 'OrderDisplay2', 'TypeNote', 'DisplayBegin', 'DisplayEnd', 'Message', 'DisplayNom', 'IndiceAid', 'DisplayBulletin', 'BullSimplifie', 'OutilsComplementaires', 'FeuillePresence', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('nom', 'nomComplet', 'noteMax', 'orderDisplay1', 'orderDisplay2', 'typeNote', 'displayBegin', 'displayEnd', 'message', 'displayNom', 'indiceAid', 'displayBulletin', 'bullSimplifie', 'outilsComplementaires', 'feuillePresence', ),
		BasePeer::TYPE_COLNAME => array (self::NOM, self::NOM_COMPLET, self::NOTE_MAX, self::ORDER_DISPLAY1, self::ORDER_DISPLAY2, self::TYPE_NOTE, self::DISPLAY_BEGIN, self::DISPLAY_END, self::MESSAGE, self::DISPLAY_NOM, self::INDICE_AID, self::DISPLAY_BULLETIN, self::BULL_SIMPLIFIE, self::OUTILS_COMPLEMENTAIRES, self::FEUILLE_PRESENCE, ),
		BasePeer::TYPE_FIELDNAME => array ('nom', 'nom_complet', 'note_max', 'order_display1', 'order_display2', 'type_note', 'display_begin', 'display_end', 'message', 'display_nom', 'indice_aid', 'display_bulletin', 'bull_simplifie', 'outils_complementaires', 'feuille_presence', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Nom' => 0, 'NomComplet' => 1, 'NoteMax' => 2, 'OrderDisplay1' => 3, 'OrderDisplay2' => 4, 'TypeNote' => 5, 'DisplayBegin' => 6, 'DisplayEnd' => 7, 'Message' => 8, 'DisplayNom' => 9, 'IndiceAid' => 10, 'DisplayBulletin' => 11, 'BullSimplifie' => 12, 'OutilsComplementaires' => 13, 'FeuillePresence' => 14, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('nom' => 0, 'nomComplet' => 1, 'noteMax' => 2, 'orderDisplay1' => 3, 'orderDisplay2' => 4, 'typeNote' => 5, 'displayBegin' => 6, 'displayEnd' => 7, 'message' => 8, 'displayNom' => 9, 'indiceAid' => 10, 'displayBulletin' => 11, 'bullSimplifie' => 12, 'outilsComplementaires' => 13, 'feuillePresence' => 14, ),
		BasePeer::TYPE_COLNAME => array (self::NOM => 0, self::NOM_COMPLET => 1, self::NOTE_MAX => 2, self::ORDER_DISPLAY1 => 3, self::ORDER_DISPLAY2 => 4, self::TYPE_NOTE => 5, self::DISPLAY_BEGIN => 6, self::DISPLAY_END => 7, self::MESSAGE => 8, self::DISPLAY_NOM => 9, self::INDICE_AID => 10, self::DISPLAY_BULLETIN => 11, self::BULL_SIMPLIFIE => 12, self::OUTILS_COMPLEMENTAIRES => 13, self::FEUILLE_PRESENCE => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('nom' => 0, 'nom_complet' => 1, 'note_max' => 2, 'order_display1' => 3, 'order_display2' => 4, 'type_note' => 5, 'display_begin' => 6, 'display_end' => 7, 'message' => 8, 'display_nom' => 9, 'indice_aid' => 10, 'display_bulletin' => 11, 'bull_simplifie' => 12, 'outils_complementaires' => 13, 'feuille_presence' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new AidConfigurationMapBuilder();
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
	 * @param      string $column The column name for current table. (i.e. AidConfigurationPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AidConfigurationPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(AidConfigurationPeer::NOM);

		$criteria->addSelectColumn(AidConfigurationPeer::NOM_COMPLET);

		$criteria->addSelectColumn(AidConfigurationPeer::NOTE_MAX);

		$criteria->addSelectColumn(AidConfigurationPeer::ORDER_DISPLAY1);

		$criteria->addSelectColumn(AidConfigurationPeer::ORDER_DISPLAY2);

		$criteria->addSelectColumn(AidConfigurationPeer::TYPE_NOTE);

		$criteria->addSelectColumn(AidConfigurationPeer::DISPLAY_BEGIN);

		$criteria->addSelectColumn(AidConfigurationPeer::DISPLAY_END);

		$criteria->addSelectColumn(AidConfigurationPeer::MESSAGE);

		$criteria->addSelectColumn(AidConfigurationPeer::DISPLAY_NOM);

		$criteria->addSelectColumn(AidConfigurationPeer::INDICE_AID);

		$criteria->addSelectColumn(AidConfigurationPeer::DISPLAY_BULLETIN);

		$criteria->addSelectColumn(AidConfigurationPeer::BULL_SIMPLIFIE);

		$criteria->addSelectColumn(AidConfigurationPeer::OUTILS_COMPLEMENTAIRES);

		$criteria->addSelectColumn(AidConfigurationPeer::FEUILLE_PRESENCE);

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
		$criteria->setPrimaryTableName(AidConfigurationPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AidConfigurationPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AidConfiguration
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AidConfigurationPeer::doSelect($critcopy, $con);
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
		return AidConfigurationPeer::populateObjects(AidConfigurationPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AidConfigurationPeer::addSelectColumns($criteria);
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
	 * @param      AidConfiguration $value A AidConfiguration object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(AidConfiguration $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIndiceAid();
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
	 * @param      mixed $value A AidConfiguration object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AidConfiguration) {
				$key = (string) $value->getIndiceAid();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AidConfiguration object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AidConfiguration Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		if ($row[$startcol + 10] === null) {
			return null;
		}
		return (string) $row[$startcol + 10];
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
		$cls = AidConfigurationPeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AidConfigurationPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AidConfigurationPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AidConfigurationPeer::addInstanceToPool($obj, $key);
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
		return AidConfigurationPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a AidConfiguration or Criteria object.
	 *
	 * @param      mixed $values Criteria or AidConfiguration object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AidConfiguration object
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
	 * Method perform an UPDATE on the database, given a AidConfiguration or Criteria object.
	 *
	 * @param      mixed $values Criteria or AidConfiguration object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AidConfigurationPeer::INDICE_AID);
			$selectCriteria->add(AidConfigurationPeer::INDICE_AID, $criteria->remove(AidConfigurationPeer::INDICE_AID), $comparison);

		} else { // $values is AidConfiguration object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the aid_config table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AidConfigurationPeer::doOnDeleteCascade(new Criteria(AidConfigurationPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AidConfigurationPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a AidConfiguration or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AidConfiguration object or primary key or array of primary keys
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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			AidConfigurationPeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AidConfiguration) {
			// invalidate the cache for this single object
			AidConfigurationPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AidConfigurationPeer::INDICE_AID, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				AidConfigurationPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AidConfigurationPeer::doOnDeleteCascade($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					AidConfigurationPeer::clearInstancePool();
				} else { // it's a PK or object
					AidConfigurationPeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in AidDetailsPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			AidDetailsPeer::clearInstancePool();

			// invalidate objects in JAidUtilisateursProfessionnelsPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JAidUtilisateursProfessionnelsPeer::clearInstancePool();

			// invalidate objects in JAidElevesPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JAidElevesPeer::clearInstancePool();

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
		$objects = AidConfigurationPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related AidDetails objects
			$c = new Criteria(AidDetailsPeer::DATABASE_NAME);
			
			$c->add(AidDetailsPeer::INDICE_AID, $obj->getIndiceAid());
			$affectedRows += AidDetailsPeer::doDelete($c, $con);

			// delete related JAidUtilisateursProfessionnels objects
			$c = new Criteria(JAidUtilisateursProfessionnelsPeer::DATABASE_NAME);
			
			$c->add(JAidUtilisateursProfessionnelsPeer::INDICE_AID, $obj->getIndiceAid());
			$affectedRows += JAidUtilisateursProfessionnelsPeer::doDelete($c, $con);

			// delete related JAidEleves objects
			$c = new Criteria(JAidElevesPeer::DATABASE_NAME);
			
			$c->add(JAidElevesPeer::INDICE_AID, $obj->getIndiceAid());
			$affectedRows += JAidElevesPeer::doDelete($c, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given AidConfiguration object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AidConfiguration $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(AidConfiguration $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AidConfigurationPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AidConfigurationPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AidConfigurationPeer::DATABASE_NAME, AidConfigurationPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AidConfiguration
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AidConfigurationPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AidConfigurationPeer::DATABASE_NAME);
		$criteria->add(AidConfigurationPeer::INDICE_AID, $pk);

		$v = AidConfigurationPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AidConfigurationPeer::DATABASE_NAME);
			$criteria->add(AidConfigurationPeer::INDICE_AID, $pks, Criteria::IN);
			$objs = AidConfigurationPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAidConfigurationPeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the AidConfigurationPeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the AidConfigurationPeer class:
//
// Propel::getDatabaseMap(AidConfigurationPeer::DATABASE_NAME)->addTableBuilder(AidConfigurationPeer::TABLE_NAME, AidConfigurationPeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseAidConfigurationPeer::DATABASE_NAME)->addTableBuilder(BaseAidConfigurationPeer::TABLE_NAME, BaseAidConfigurationPeer::getMapBuilder());

