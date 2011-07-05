<?php


/**
 * Base static class for performing query and update operations on the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceAgregationDecomptePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_agregation_decompte';

	/** the related Propel class for this table */
	const OM_CLASS = 'AbsenceAgregationDecompte';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceAgregationDecompte';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AbsenceAgregationDecompteTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 11;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 11;

	/** the column name for the ELEVE_ID field */
	const ELEVE_ID = 'a_agregation_decompte.ELEVE_ID';

	/** the column name for the DATE_DEMI_JOUNEE field */
	const DATE_DEMI_JOUNEE = 'a_agregation_decompte.DATE_DEMI_JOUNEE';

	/** the column name for the MANQUEMENT_OBLIGATION_PRESENCE field */
	const MANQUEMENT_OBLIGATION_PRESENCE = 'a_agregation_decompte.MANQUEMENT_OBLIGATION_PRESENCE';

	/** the column name for the JUSTIFIEE field */
	const JUSTIFIEE = 'a_agregation_decompte.JUSTIFIEE';

	/** the column name for the NOTIFIEE field */
	const NOTIFIEE = 'a_agregation_decompte.NOTIFIEE';

	/** the column name for the NB_RETARDS field */
	const NB_RETARDS = 'a_agregation_decompte.NB_RETARDS';

	/** the column name for the NB_RETARDS_JUSTIFIES field */
	const NB_RETARDS_JUSTIFIES = 'a_agregation_decompte.NB_RETARDS_JUSTIFIES';

	/** the column name for the MOTIFS_ABSENCES field */
	const MOTIFS_ABSENCES = 'a_agregation_decompte.MOTIFS_ABSENCES';

	/** the column name for the MOTIFS_RETARDS field */
	const MOTIFS_RETARDS = 'a_agregation_decompte.MOTIFS_RETARDS';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'a_agregation_decompte.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'a_agregation_decompte.UPDATED_AT';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';
	
	/**
	 * An identiy map to hold any loaded instances of AbsenceAgregationDecompte objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceAgregationDecompte[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('EleveId', 'DateDemiJounee', 'ManquementObligationPresence', 'Justifiee', 'Notifiee', 'NbRetards', 'NbRetardsJustifies', 'MotifsAbsences', 'MotifsRetards', 'CreatedAt', 'UpdatedAt', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('eleveId', 'dateDemiJounee', 'manquementObligationPresence', 'justifiee', 'notifiee', 'nbRetards', 'nbRetardsJustifies', 'motifsAbsences', 'motifsRetards', 'createdAt', 'updatedAt', ),
		BasePeer::TYPE_COLNAME => array (self::ELEVE_ID, self::DATE_DEMI_JOUNEE, self::MANQUEMENT_OBLIGATION_PRESENCE, self::JUSTIFIEE, self::NOTIFIEE, self::NB_RETARDS, self::NB_RETARDS_JUSTIFIES, self::MOTIFS_ABSENCES, self::MOTIFS_RETARDS, self::CREATED_AT, self::UPDATED_AT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ELEVE_ID', 'DATE_DEMI_JOUNEE', 'MANQUEMENT_OBLIGATION_PRESENCE', 'JUSTIFIEE', 'NOTIFIEE', 'NB_RETARDS', 'NB_RETARDS_JUSTIFIES', 'MOTIFS_ABSENCES', 'MOTIFS_RETARDS', 'CREATED_AT', 'UPDATED_AT', ),
		BasePeer::TYPE_FIELDNAME => array ('eleve_id', 'date_demi_jounee', 'manquement_obligation_presence', 'justifiee', 'notifiee', 'nb_retards', 'nb_retards_justifies', 'motifs_absences', 'motifs_retards', 'created_at', 'updated_at', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('EleveId' => 0, 'DateDemiJounee' => 1, 'ManquementObligationPresence' => 2, 'Justifiee' => 3, 'Notifiee' => 4, 'NbRetards' => 5, 'NbRetardsJustifies' => 6, 'MotifsAbsences' => 7, 'MotifsRetards' => 8, 'CreatedAt' => 9, 'UpdatedAt' => 10, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('eleveId' => 0, 'dateDemiJounee' => 1, 'manquementObligationPresence' => 2, 'justifiee' => 3, 'notifiee' => 4, 'nbRetards' => 5, 'nbRetardsJustifies' => 6, 'motifsAbsences' => 7, 'motifsRetards' => 8, 'createdAt' => 9, 'updatedAt' => 10, ),
		BasePeer::TYPE_COLNAME => array (self::ELEVE_ID => 0, self::DATE_DEMI_JOUNEE => 1, self::MANQUEMENT_OBLIGATION_PRESENCE => 2, self::JUSTIFIEE => 3, self::NOTIFIEE => 4, self::NB_RETARDS => 5, self::NB_RETARDS_JUSTIFIES => 6, self::MOTIFS_ABSENCES => 7, self::MOTIFS_RETARDS => 8, self::CREATED_AT => 9, self::UPDATED_AT => 10, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ELEVE_ID' => 0, 'DATE_DEMI_JOUNEE' => 1, 'MANQUEMENT_OBLIGATION_PRESENCE' => 2, 'JUSTIFIEE' => 3, 'NOTIFIEE' => 4, 'NB_RETARDS' => 5, 'NB_RETARDS_JUSTIFIES' => 6, 'MOTIFS_ABSENCES' => 7, 'MOTIFS_RETARDS' => 8, 'CREATED_AT' => 9, 'UPDATED_AT' => 10, ),
		BasePeer::TYPE_FIELDNAME => array ('eleve_id' => 0, 'date_demi_jounee' => 1, 'manquement_obligation_presence' => 2, 'justifiee' => 3, 'notifiee' => 4, 'nb_retards' => 5, 'nb_retards_justifies' => 6, 'motifs_absences' => 7, 'motifs_retards' => 8, 'created_at' => 9, 'updated_at' => 10, ),
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
	 * @param      string $column The column name for current table. (i.e. AbsenceAgregationDecomptePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceAgregationDecomptePeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::ELEVE_ID);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::MANQUEMENT_OBLIGATION_PRESENCE);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::JUSTIFIEE);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::NOTIFIEE);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::NB_RETARDS);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::NB_RETARDS_JUSTIFIES);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::CREATED_AT);
			$criteria->addSelectColumn(AbsenceAgregationDecomptePeer::UPDATED_AT);
		} else {
			$criteria->addSelectColumn($alias . '.ELEVE_ID');
			$criteria->addSelectColumn($alias . '.DATE_DEMI_JOUNEE');
			$criteria->addSelectColumn($alias . '.MANQUEMENT_OBLIGATION_PRESENCE');
			$criteria->addSelectColumn($alias . '.JUSTIFIEE');
			$criteria->addSelectColumn($alias . '.NOTIFIEE');
			$criteria->addSelectColumn($alias . '.NB_RETARDS');
			$criteria->addSelectColumn($alias . '.NB_RETARDS_JUSTIFIES');
			$criteria->addSelectColumn($alias . '.MOTIFS_ABSENCES');
			$criteria->addSelectColumn($alias . '.MOTIFS_RETARDS');
			$criteria->addSelectColumn($alias . '.CREATED_AT');
			$criteria->addSelectColumn($alias . '.UPDATED_AT');
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
		$criteria->setPrimaryTableName(AbsenceAgregationDecomptePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceAgregationDecompte
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceAgregationDecomptePeer::doSelect($critcopy, $con);
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
		return AbsenceAgregationDecomptePeer::populateObjects(AbsenceAgregationDecomptePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
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
	 * @param      AbsenceAgregationDecompte $value A AbsenceAgregationDecompte object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getEleveId(), (string) $obj->getDateDemiJounee()));
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
	 * @param      mixed $value A AbsenceAgregationDecompte object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceAgregationDecompte) {
				$key = serialize(array((string) $value->getEleveId(), (string) $value->getDateDemiJounee()));
			} elseif (is_array($value) && count($value) === 2) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceAgregationDecompte object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceAgregationDecompte Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to a_agregation_decompte
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
		if ($row[$startcol] === null && $row[$startcol + 1] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1]));
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
		return array((int) $row[$startcol], (string) $row[$startcol + 1]);
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
		$cls = AbsenceAgregationDecomptePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceAgregationDecomptePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceAgregationDecomptePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceAgregationDecomptePeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AbsenceAgregationDecompte object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AbsenceAgregationDecomptePeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AbsenceAgregationDecomptePeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AbsenceAgregationDecomptePeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = AbsenceAgregationDecomptePeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AbsenceAgregationDecomptePeer::addInstanceToPool($obj, $key);
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
		$criteria->setPrimaryTableName(AbsenceAgregationDecomptePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceAgregationDecomptePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

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
	 * Selects a collection of AbsenceAgregationDecompte objects pre-filled with their Eleve objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceAgregationDecompte objects.
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

		AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
		$startcol = AbsenceAgregationDecomptePeer::NUM_HYDRATE_COLUMNS;
		ElevePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceAgregationDecomptePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceAgregationDecomptePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceAgregationDecomptePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceAgregationDecomptePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceAgregationDecomptePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceAgregationDecompte) to $obj2 (Eleve)
				$obj2->addAbsenceAgregationDecompte($obj1);

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
		$criteria->setPrimaryTableName(AbsenceAgregationDecomptePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceAgregationDecomptePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

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
	 * Selects a collection of AbsenceAgregationDecompte objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceAgregationDecompte objects.
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

		AbsenceAgregationDecomptePeer::addSelectColumns($criteria);
		$startcol2 = AbsenceAgregationDecomptePeer::NUM_HYDRATE_COLUMNS;

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + ElevePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(AbsenceAgregationDecomptePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceAgregationDecomptePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceAgregationDecomptePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceAgregationDecomptePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceAgregationDecomptePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceAgregationDecompte) to the collection in $obj2 (Eleve)
				$obj2->addAbsenceAgregationDecompte($obj1);
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
	  $dbMap = Propel::getDatabaseMap(BaseAbsenceAgregationDecomptePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAbsenceAgregationDecomptePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AbsenceAgregationDecompteTableMap());
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
		return $withPrefix ? AbsenceAgregationDecomptePeer::CLASS_DEFAULT : AbsenceAgregationDecomptePeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a AbsenceAgregationDecompte or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceAgregationDecompte object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceAgregationDecompte object
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
	 * Performs an UPDATE on the database, given a AbsenceAgregationDecompte or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceAgregationDecompte object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceAgregationDecomptePeer::ELEVE_ID);
			$value = $criteria->remove(AbsenceAgregationDecomptePeer::ELEVE_ID);
			if ($value) {
				$selectCriteria->add(AbsenceAgregationDecomptePeer::ELEVE_ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceAgregationDecomptePeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE);
			$value = $criteria->remove(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE);
			if ($value) {
				$selectCriteria->add(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceAgregationDecomptePeer::TABLE_NAME);
			}

		} else { // $values is AbsenceAgregationDecompte object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the a_agregation_decompte table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(AbsenceAgregationDecomptePeer::TABLE_NAME, $con, AbsenceAgregationDecomptePeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AbsenceAgregationDecomptePeer::clearInstancePool();
			AbsenceAgregationDecomptePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a AbsenceAgregationDecompte or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceAgregationDecompte object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			AbsenceAgregationDecomptePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceAgregationDecompte) { // it's a model object
			// invalidate the cache for this single object
			AbsenceAgregationDecomptePeer::removeInstanceFromPool($values);
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
				$criterion = $criteria->getNewCriterion(AbsenceAgregationDecomptePeer::ELEVE_ID, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $value[1]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				AbsenceAgregationDecomptePeer::removeInstanceFromPool($value);
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
			AbsenceAgregationDecomptePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given AbsenceAgregationDecompte object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceAgregationDecompte $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceAgregationDecomptePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceAgregationDecomptePeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceAgregationDecomptePeer::DATABASE_NAME, AbsenceAgregationDecomptePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $eleve_id
	 * @param      string $date_demi_jounee
	 * @param      PropelPDO $con
	 * @return     AbsenceAgregationDecompte
	 */
	public static function retrieveByPK($eleve_id, $date_demi_jounee, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $eleve_id, (string) $date_demi_jounee));
 		if (null !== ($obj = AbsenceAgregationDecomptePeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(AbsenceAgregationDecomptePeer::DATABASE_NAME);
		$criteria->add(AbsenceAgregationDecomptePeer::ELEVE_ID, $eleve_id);
		$criteria->add(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $date_demi_jounee);
		$v = AbsenceAgregationDecomptePeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseAbsenceAgregationDecomptePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAbsenceAgregationDecomptePeer::buildTableMap();

