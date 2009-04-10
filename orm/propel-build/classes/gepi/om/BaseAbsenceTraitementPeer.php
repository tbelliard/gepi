<?php

/**
 * Base static class for performing query and update operations on the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceTraitementPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_traitements';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceTraitement';

	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'a_traitements.ID';

	/** the column name for the UTILISATEUR_ID field */
	const UTILISATEUR_ID = 'a_traitements.UTILISATEUR_ID';

	/** the column name for the CREATED_ON field */
	const CREATED_ON = 'a_traitements.CREATED_ON';

	/** the column name for the UPDATED_ON field */
	const UPDATED_ON = 'a_traitements.UPDATED_ON';

	/** the column name for the A_TYPE_ID field */
	const A_TYPE_ID = 'a_traitements.A_TYPE_ID';

	/** the column name for the A_MOTIF_ID field */
	const A_MOTIF_ID = 'a_traitements.A_MOTIF_ID';

	/** the column name for the A_JUSTIFICATION_ID field */
	const A_JUSTIFICATION_ID = 'a_traitements.A_JUSTIFICATION_ID';

	/** the column name for the TEXTE_JUSTIFICATION field */
	const TEXTE_JUSTIFICATION = 'a_traitements.TEXTE_JUSTIFICATION';

	/** the column name for the A_ACTION_ID field */
	const A_ACTION_ID = 'a_traitements.A_ACTION_ID';

	/**
	 * An identiy map to hold any loaded instances of AbsenceTraitement objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceTraitement[]
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
		BasePeer::TYPE_PHPNAME => array ('Id', 'UtilisateurId', 'CreatedOn', 'UpdatedOn', 'ATypeId', 'AMotifId', 'AJustificationId', 'TexteJustification', 'AActionId', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'utilisateurId', 'createdOn', 'updatedOn', 'aTypeId', 'aMotifId', 'aJustificationId', 'texteJustification', 'aActionId', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::UTILISATEUR_ID, self::CREATED_ON, self::UPDATED_ON, self::A_TYPE_ID, self::A_MOTIF_ID, self::A_JUSTIFICATION_ID, self::TEXTE_JUSTIFICATION, self::A_ACTION_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'utilisateur_id', 'created_on', 'updated_on', 'a_type_id', 'a_motif_id', 'a_justification_id', 'texte_justification', 'a_action_id', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'UtilisateurId' => 1, 'CreatedOn' => 2, 'UpdatedOn' => 3, 'ATypeId' => 4, 'AMotifId' => 5, 'AJustificationId' => 6, 'TexteJustification' => 7, 'AActionId' => 8, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'utilisateurId' => 1, 'createdOn' => 2, 'updatedOn' => 3, 'aTypeId' => 4, 'aMotifId' => 5, 'aJustificationId' => 6, 'texteJustification' => 7, 'aActionId' => 8, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::UTILISATEUR_ID => 1, self::CREATED_ON => 2, self::UPDATED_ON => 3, self::A_TYPE_ID => 4, self::A_MOTIF_ID => 5, self::A_JUSTIFICATION_ID => 6, self::TEXTE_JUSTIFICATION => 7, self::A_ACTION_ID => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'utilisateur_id' => 1, 'created_on' => 2, 'updated_on' => 3, 'a_type_id' => 4, 'a_motif_id' => 5, 'a_justification_id' => 6, 'texte_justification' => 7, 'a_action_id' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new AbsenceTraitementMapBuilder();
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
	 * @param      string $column The column name for current table. (i.e. AbsenceTraitementPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceTraitementPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(AbsenceTraitementPeer::ID);

		$criteria->addSelectColumn(AbsenceTraitementPeer::UTILISATEUR_ID);

		$criteria->addSelectColumn(AbsenceTraitementPeer::CREATED_ON);

		$criteria->addSelectColumn(AbsenceTraitementPeer::UPDATED_ON);

		$criteria->addSelectColumn(AbsenceTraitementPeer::A_TYPE_ID);

		$criteria->addSelectColumn(AbsenceTraitementPeer::A_MOTIF_ID);

		$criteria->addSelectColumn(AbsenceTraitementPeer::A_JUSTIFICATION_ID);

		$criteria->addSelectColumn(AbsenceTraitementPeer::TEXTE_JUSTIFICATION);

		$criteria->addSelectColumn(AbsenceTraitementPeer::A_ACTION_ID);

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
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceTraitement
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceTraitementPeer::doSelect($critcopy, $con);
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
		return AbsenceTraitementPeer::populateObjects(AbsenceTraitementPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceTraitementPeer::addSelectColumns($criteria);
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
	 * @param      AbsenceTraitement $value A AbsenceTraitement object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(AbsenceTraitement $obj, $key = null)
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
	 * @param      mixed $value A AbsenceTraitement object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceTraitement) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceTraitement object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceTraitement Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		$cls = AbsenceTraitementPeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceTraitementPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceTraitementPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceMotif table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceMotif(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceJustification table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceJustification(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceAction table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceAction(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Selects a collection of AbsenceTraitement objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
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

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($c);

		$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceTraitement) to $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with their AbsenceType objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceType(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceTypePeer::addSelectColumns($c);

		$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceTypePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = AbsenceTypePeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceTypePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceTraitement) to $obj2 (AbsenceType)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with their AbsenceMotif objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceMotif(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceMotifPeer::addSelectColumns($c);

		$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceMotifPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = AbsenceMotifPeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceMotifPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceTraitement) to $obj2 (AbsenceMotif)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with their AbsenceJustification objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceJustification(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceJustificationPeer::addSelectColumns($c);

		$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceJustificationPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = AbsenceJustificationPeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceJustificationPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceTraitement) to $obj2 (AbsenceJustification)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with their AbsenceAction objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceAction(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceActionPeer::addSelectColumns($c);

		$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceActionPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = AbsenceActionPeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceActionPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceTraitement) to $obj2 (AbsenceAction)
				$obj2->addAbsenceTraitement($obj1);

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
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
		$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
		$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
		$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
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

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceTypePeer::NUM_COLUMNS - AbsenceTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceMotifPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceMotifPeer::NUM_COLUMNS - AbsenceMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceJustificationPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceJustificationPeer::NUM_COLUMNS - AbsenceJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceActionPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (AbsenceActionPeer::NUM_COLUMNS - AbsenceActionPeer::NUM_LAZY_LOAD_COLUMNS);

		$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
		$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
		$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
		$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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
				} // if obj2 loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceType rows

			$key3 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = AbsenceTypePeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = AbsenceTypePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceTypePeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceType)
				$obj3->addAbsenceTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceMotif rows

			$key4 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = AbsenceMotifPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$omClass = AbsenceMotifPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceMotifPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceMotif)
				$obj4->addAbsenceTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceJustification rows

			$key5 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = AbsenceJustificationPeer::getInstanceFromPool($key5);
				if (!$obj5) {

					$omClass = AbsenceJustificationPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceJustificationPeer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceJustification)
				$obj5->addAbsenceTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceAction rows

			$key6 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol6);
			if ($key6 !== null) {
				$obj6 = AbsenceActionPeer::getInstanceFromPool($key6);
				if (!$obj6) {

					$omClass = AbsenceActionPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					AbsenceActionPeer::addInstanceToPool($obj6, $key6);
				} // if obj6 loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj6 (AbsenceAction)
				$obj6->addAbsenceTraitement($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
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
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceType table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceMotif table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceMotif(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceJustification table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceJustification(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related AbsenceAction table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceAction(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$criteria->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
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
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
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

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceTypePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (AbsenceTypePeer::NUM_COLUMNS - AbsenceTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceMotifPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceMotifPeer::NUM_COLUMNS - AbsenceMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceJustificationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceJustificationPeer::NUM_COLUMNS - AbsenceJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceActionPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceActionPeer::NUM_COLUMNS - AbsenceActionPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined AbsenceType rows

				$key2 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = AbsenceTypePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = AbsenceTypePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AbsenceTypePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (AbsenceType)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceMotif rows

				$key3 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceMotifPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = AbsenceMotifPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceMotifPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceMotif)
				$obj3->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceJustification rows

				$key4 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = AbsenceJustificationPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceJustification)
				$obj4->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceAction rows

				$key5 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = AbsenceActionPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = AbsenceActionPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceActionPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceAction)
				$obj5->addAbsenceTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects except AbsenceType.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceType(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceMotifPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceMotifPeer::NUM_COLUMNS - AbsenceMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceJustificationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceJustificationPeer::NUM_COLUMNS - AbsenceJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceActionPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceActionPeer::NUM_COLUMNS - AbsenceActionPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceMotif rows

				$key3 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceMotifPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = AbsenceMotifPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceMotifPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceMotif)
				$obj3->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceJustification rows

				$key4 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = AbsenceJustificationPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceJustification)
				$obj4->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceAction rows

				$key5 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = AbsenceActionPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = AbsenceActionPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceActionPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceAction)
				$obj5->addAbsenceTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects except AbsenceMotif.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceMotif(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceTypePeer::NUM_COLUMNS - AbsenceTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceJustificationPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceJustificationPeer::NUM_COLUMNS - AbsenceJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceActionPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceActionPeer::NUM_COLUMNS - AbsenceActionPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceType rows

				$key3 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceTypePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = AbsenceTypePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceTypePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceType)
				$obj3->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceJustification rows

				$key4 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = AbsenceJustificationPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceJustification)
				$obj4->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceAction rows

				$key5 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = AbsenceActionPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = AbsenceActionPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceActionPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceAction)
				$obj5->addAbsenceTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects except AbsenceJustification.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceJustification(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceTypePeer::NUM_COLUMNS - AbsenceTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceMotifPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceMotifPeer::NUM_COLUMNS - AbsenceMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceActionPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceActionPeer::NUM_COLUMNS - AbsenceActionPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_ACTION_ID,), array(AbsenceActionPeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceType rows

				$key3 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceTypePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = AbsenceTypePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceTypePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceType)
				$obj3->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceMotif rows

				$key4 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceMotifPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = AbsenceMotifPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceMotifPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceMotif)
				$obj4->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceAction rows

				$key5 = AbsenceActionPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = AbsenceActionPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = AbsenceActionPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceActionPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceAction)
				$obj5->addAbsenceTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceTraitement objects pre-filled with all related objects except AbsenceAction.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceAction(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		AbsenceTraitementPeer::addSelectColumns($c);
		$startcol2 = (AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceTypePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (AbsenceTypePeer::NUM_COLUMNS - AbsenceTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceMotifPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (AbsenceMotifPeer::NUM_COLUMNS - AbsenceMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceJustificationPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (AbsenceJustificationPeer::NUM_COLUMNS - AbsenceJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(AbsenceTraitementPeer::UTILISATEUR_ID,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_TYPE_ID,), array(AbsenceTypePeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_MOTIF_ID,), array(AbsenceMotifPeer::ID,), $join_behavior);
				$c->addJoin(array(AbsenceTraitementPeer::A_JUSTIFICATION_ID,), array(AbsenceJustificationPeer::ID,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = AbsenceTraitementPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceTraitementPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceType rows

				$key3 = AbsenceTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceTypePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = AbsenceTypePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceTypePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj3 (AbsenceType)
				$obj3->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceMotif rows

				$key4 = AbsenceMotifPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceMotifPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = AbsenceMotifPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceMotifPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj4 (AbsenceMotif)
				$obj4->addAbsenceTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceJustification rows

				$key5 = AbsenceJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = AbsenceJustificationPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = AbsenceJustificationPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceJustificationPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceTraitement) to the collection in $obj5 (AbsenceJustification)
				$obj5->addAbsenceTraitement($obj1);

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
		return AbsenceTraitementPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a AbsenceTraitement or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceTraitement object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceTraitement object
		}

		if ($criteria->containsKey(AbsenceTraitementPeer::ID) && $criteria->keyContainsValue(AbsenceTraitementPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceTraitementPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a AbsenceTraitement or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceTraitement object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceTraitementPeer::ID);
			$selectCriteria->add(AbsenceTraitementPeer::ID, $criteria->remove(AbsenceTraitementPeer::ID), $comparison);

		} else { // $values is AbsenceTraitement object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the a_traitements table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			AbsenceTraitementPeer::doOnDeleteSetNull(new Criteria(AbsenceTraitementPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AbsenceTraitementPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a AbsenceTraitement or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceTraitement object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			AbsenceTraitementPeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceTraitement) {
			// invalidate the cache for this single object
			AbsenceTraitementPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AbsenceTraitementPeer::ID, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				AbsenceTraitementPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			AbsenceTraitementPeer::doOnDeleteSetNull($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					AbsenceTraitementPeer::clearInstancePool();
				} else { // it's a PK or object
					AbsenceTraitementPeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in JTraitementSaisiePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JTraitementSaisiePeer::clearInstancePool();

			// invalidate objects in JTraitementEnvoiPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JTraitementEnvoiPeer::clearInstancePool();

			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * This is a method for emulating ON DELETE SET NULL DBs that don't support this
	 * feature (like MySQL or SQLite).
	 *
	 * This method is not very speedy because it must perform a query first to get
	 * the implicated records and then perform the deletes by calling those Peer classes.
	 *
	 * This method should be used within a transaction if possible.
	 *
	 * @param      Criteria $criteria
	 * @param      PropelPDO $con
	 * @return     void
	 */
	protected static function doOnDeleteSetNull(Criteria $criteria, PropelPDO $con)
	{

		// first find the objects that are implicated by the $criteria
		$objects = AbsenceTraitementPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {

			// set fkey col in related JTraitementSaisie rows to NULL
			$selectCriteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
			$updateValues = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
			$selectCriteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $obj->getId());
			$updateValues->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related JTraitementEnvoi rows to NULL
			$selectCriteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
			$updateValues = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
			$selectCriteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $obj->getId());
			$updateValues->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

		}
	}

	/**
	 * Validates all modified columns of given AbsenceTraitement object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceTraitement $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(AbsenceTraitement $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceTraitementPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceTraitementPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceTraitementPeer::DATABASE_NAME, AbsenceTraitementPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AbsenceTraitement
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AbsenceTraitementPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		$criteria->add(AbsenceTraitementPeer::ID, $pk);

		$v = AbsenceTraitementPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
			$criteria->add(AbsenceTraitementPeer::ID, $pks, Criteria::IN);
			$objs = AbsenceTraitementPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAbsenceTraitementPeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the AbsenceTraitementPeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the AbsenceTraitementPeer class:
//
// Propel::getDatabaseMap(AbsenceTraitementPeer::DATABASE_NAME)->addTableBuilder(AbsenceTraitementPeer::TABLE_NAME, AbsenceTraitementPeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseAbsenceTraitementPeer::DATABASE_NAME)->addTableBuilder(BaseAbsenceTraitementPeer::TABLE_NAME, BaseAbsenceTraitementPeer::getMapBuilder());

