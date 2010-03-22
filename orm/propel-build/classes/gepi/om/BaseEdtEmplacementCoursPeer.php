<?php

/**
 * Base static class for performing query and update operations on the 'edt_cours' table.
 *
 * Liste de tous les creneaux de tous les emplois du temps
 *
 * @package    gepi.om
 */
abstract class BaseEdtEmplacementCoursPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'edt_cours';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.EdtEmplacementCours';

	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID_COURS field */
	const ID_COURS = 'edt_cours.ID_COURS';

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'edt_cours.ID_GROUPE';

	/** the column name for the ID_AID field */
	const ID_AID = 'edt_cours.ID_AID';

	/** the column name for the ID_SALLE field */
	const ID_SALLE = 'edt_cours.ID_SALLE';

	/** the column name for the JOUR_SEMAINE field */
	const JOUR_SEMAINE = 'edt_cours.JOUR_SEMAINE';

	/** the column name for the ID_DEFINIE_PERIODE field */
	const ID_DEFINIE_PERIODE = 'edt_cours.ID_DEFINIE_PERIODE';

	/** the column name for the DUREE field */
	const DUREE = 'edt_cours.DUREE';

	/** the column name for the HEUREDEB_DEC field */
	const HEUREDEB_DEC = 'edt_cours.HEUREDEB_DEC';

	/** the column name for the ID_SEMAINE field */
	const ID_SEMAINE = 'edt_cours.ID_SEMAINE';

	/** the column name for the ID_CALENDRIER field */
	const ID_CALENDRIER = 'edt_cours.ID_CALENDRIER';

	/** the column name for the MODIF_EDT field */
	const MODIF_EDT = 'edt_cours.MODIF_EDT';

	/** the column name for the LOGIN_PROF field */
	const LOGIN_PROF = 'edt_cours.LOGIN_PROF';

	/**
	 * An identiy map to hold any loaded instances of EdtEmplacementCours objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array EdtEmplacementCours[]
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
		BasePeer::TYPE_PHPNAME => array ('IdCours', 'IdGroupe', 'IdAid', 'IdSalle', 'JourSemaine', 'IdDefiniePeriode', 'Duree', 'HeuredebDec', 'IdSemaine', 'IdCalendrier', 'ModifEdt', 'LoginProf', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCours', 'idGroupe', 'idAid', 'idSalle', 'jourSemaine', 'idDefiniePeriode', 'duree', 'heuredebDec', 'idSemaine', 'idCalendrier', 'modifEdt', 'loginProf', ),
		BasePeer::TYPE_COLNAME => array (self::ID_COURS, self::ID_GROUPE, self::ID_AID, self::ID_SALLE, self::JOUR_SEMAINE, self::ID_DEFINIE_PERIODE, self::DUREE, self::HEUREDEB_DEC, self::ID_SEMAINE, self::ID_CALENDRIER, self::MODIF_EDT, self::LOGIN_PROF, ),
		BasePeer::TYPE_FIELDNAME => array ('id_cours', 'id_groupe', 'id_aid', 'id_salle', 'jour_semaine', 'id_definie_periode', 'duree', 'heuredeb_dec', 'id_semaine', 'id_calendrier', 'modif_edt', 'login_prof', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('IdCours' => 0, 'IdGroupe' => 1, 'IdAid' => 2, 'IdSalle' => 3, 'JourSemaine' => 4, 'IdDefiniePeriode' => 5, 'Duree' => 6, 'HeuredebDec' => 7, 'IdSemaine' => 8, 'IdCalendrier' => 9, 'ModifEdt' => 10, 'LoginProf' => 11, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idCours' => 0, 'idGroupe' => 1, 'idAid' => 2, 'idSalle' => 3, 'jourSemaine' => 4, 'idDefiniePeriode' => 5, 'duree' => 6, 'heuredebDec' => 7, 'idSemaine' => 8, 'idCalendrier' => 9, 'modifEdt' => 10, 'loginProf' => 11, ),
		BasePeer::TYPE_COLNAME => array (self::ID_COURS => 0, self::ID_GROUPE => 1, self::ID_AID => 2, self::ID_SALLE => 3, self::JOUR_SEMAINE => 4, self::ID_DEFINIE_PERIODE => 5, self::DUREE => 6, self::HEUREDEB_DEC => 7, self::ID_SEMAINE => 8, self::ID_CALENDRIER => 9, self::MODIF_EDT => 10, self::LOGIN_PROF => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('id_cours' => 0, 'id_groupe' => 1, 'id_aid' => 2, 'id_salle' => 3, 'jour_semaine' => 4, 'id_definie_periode' => 5, 'duree' => 6, 'heuredeb_dec' => 7, 'id_semaine' => 8, 'id_calendrier' => 9, 'modif_edt' => 10, 'login_prof' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new EdtEmplacementCoursMapBuilder();
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
	 * @param      string $column The column name for current table. (i.e. EdtEmplacementCoursPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(EdtEmplacementCoursPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_COURS);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_GROUPE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_AID);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_SALLE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::JOUR_SEMAINE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::DUREE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::HEUREDEB_DEC);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_SEMAINE);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::ID_CALENDRIER);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::MODIF_EDT);

		$criteria->addSelectColumn(EdtEmplacementCoursPeer::LOGIN_PROF);

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
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     EdtEmplacementCours
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = EdtEmplacementCoursPeer::doSelect($critcopy, $con);
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
		return EdtEmplacementCoursPeer::populateObjects(EdtEmplacementCoursPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
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
	 * @param      EdtEmplacementCours $value A EdtEmplacementCours object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(EdtEmplacementCours $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getIdCours();
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
	 * @param      mixed $value A EdtEmplacementCours object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof EdtEmplacementCours) {
				$key = (string) $value->getIdCours();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or EdtEmplacementCours object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     EdtEmplacementCours Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		$cls = EdtEmplacementCoursPeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = EdtEmplacementCoursPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				EdtEmplacementCoursPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the number of rows matching criteria, joining the related GroupeRelatedByIdGroupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinGroupeRelatedByIdGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related GroupeRelatedByIdGroupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinGroupeRelatedByIdGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtSalle table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEdtSalle(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtCreneau table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEdtCreneau(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtCalendrierPeriode table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEdtCalendrierPeriode(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
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
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their Groupe objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinGroupeRelatedByIdGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		GroupePeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (Groupe)
				$obj2->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their Groupe objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinGroupeRelatedByIdGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		GroupePeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (Groupe)
				$obj2->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their EdtSalle objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEdtSalle(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		EdtSallePeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = EdtSallePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = EdtSallePeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					EdtSallePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (EdtSalle)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their EdtCreneau objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEdtCreneau(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		EdtCreneauPeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = EdtCreneauPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = EdtCreneauPeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					EdtCreneauPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (EdtCreneau)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their EdtCalendrierPeriode objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEdtCalendrierPeriode(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		EdtCalendrierPeriodePeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = EdtCalendrierPeriodePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$omClass = EdtCalendrierPeriodePeer::getOMClass();

					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (EdtCalendrierPeriode)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
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

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($c);

		$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to $obj2 (UtilisateurProfessionnel)
				$obj2->addEdtEmplacementCours($obj1);

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
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
		$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
		$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
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

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol8 = $startcol7 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
		$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
		$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
		$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
		$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (Groupe)
				$obj2->addEdtEmplacementCoursRelatedByIdGroupe($obj1);
			} // if joined row not null

			// Add objects for joined Groupe rows

			$key3 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = GroupePeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					GroupePeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (Groupe)
				$obj3->addEdtEmplacementCoursRelatedByIdGroupe($obj1);
			} // if joined row not null

			// Add objects for joined EdtSalle rows

			$key4 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = EdtSallePeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtSallePeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtSalle)
				$obj4->addEdtEmplacementCours($obj1);
			} // if joined row not null

			// Add objects for joined EdtCreneau rows

			$key5 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = EdtCreneauPeer::getInstanceFromPool($key5);
				if (!$obj5) {

					$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtCreneauPeer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (EdtCreneau)
				$obj5->addEdtEmplacementCours($obj1);
			} // if joined row not null

			// Add objects for joined EdtCalendrierPeriode rows

			$key6 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol6);
			if ($key6 !== null) {
				$obj6 = EdtCalendrierPeriodePeer::getInstanceFromPool($key6);
				if (!$obj6) {

					$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj6, $key6);
				} // if obj6 loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj6 (EdtCalendrierPeriode)
				$obj6->addEdtEmplacementCours($obj1);
			} // if joined row not null

			// Add objects for joined UtilisateurProfessionnel rows

			$key7 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol7);
			if ($key7 !== null) {
				$obj7 = UtilisateurProfessionnelPeer::getInstanceFromPool($key7);
				if (!$obj7) {

					$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj7, $key7);
				} // if obj7 loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj7 (UtilisateurProfessionnel)
				$obj7->addEdtEmplacementCours($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related GroupeRelatedByIdGroupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptGroupeRelatedByIdGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related GroupeRelatedByIdGroupe table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptGroupeRelatedByIdGroupe(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtSalle table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEdtSalle(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtCreneau table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEdtCreneau(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
	 * Returns the number of rows matching criteria, joining the related EdtCalendrierPeriode table
	 *
	 * @param      Criteria $c
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEdtCalendrierPeriode(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);
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
		$criteria->setPrimaryTableName(EdtEmplacementCoursPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			EdtEmplacementCoursPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$criteria->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
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
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except GroupeRelatedByIdGroupe.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptGroupeRelatedByIdGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined EdtSalle rows

				$key2 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = EdtSallePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					EdtSallePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (EdtSalle)
				$obj2->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key3 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = EdtCreneauPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					EdtCreneauPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (EdtCreneau)
				$obj3->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCalendrierPeriode rows

				$key4 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCalendrierPeriodePeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtCalendrierPeriode)
				$obj4->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key5 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = UtilisateurProfessionnelPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (UtilisateurProfessionnel)
				$obj5->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except GroupeRelatedByIdGroupe.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptGroupeRelatedByIdGroupe(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined EdtSalle rows

				$key2 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = EdtSallePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					EdtSallePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (EdtSalle)
				$obj2->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key3 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = EdtCreneauPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					EdtCreneauPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (EdtCreneau)
				$obj3->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCalendrierPeriode rows

				$key4 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCalendrierPeriodePeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtCalendrierPeriode)
				$obj4->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key5 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = UtilisateurProfessionnelPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (UtilisateurProfessionnel)
				$obj5->addEdtEmplacementCoursRelatedByIdGroupe($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except EdtSalle.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEdtSalle(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (Groupe)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key3 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = GroupePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					GroupePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (Groupe)
				$obj3->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtCreneau)
				$obj4->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCalendrierPeriode rows

				$key5 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtCalendrierPeriodePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (EdtCalendrierPeriode)
				$obj5->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key6 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = UtilisateurProfessionnelPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj6 (UtilisateurProfessionnel)
				$obj6->addEdtEmplacementCours($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except EdtCreneau.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEdtCreneau(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (Groupe)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key3 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = GroupePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					GroupePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (Groupe)
				$obj3->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtSalle rows

				$key4 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtSallePeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtSallePeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtSalle)
				$obj4->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCalendrierPeriode rows

				$key5 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtCalendrierPeriodePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (EdtCalendrierPeriode)
				$obj5->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key6 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = UtilisateurProfessionnelPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj6 (UtilisateurProfessionnel)
				$obj6->addEdtEmplacementCours($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except EdtCalendrierPeriode.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEdtCalendrierPeriode(Criteria $c, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $c;

		// Set the correct dbName if it has not been overridden
		// $c->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::LOGIN_PROF,), array(UtilisateurProfessionnelPeer::LOGIN,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (Groupe)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key3 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = GroupePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					GroupePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (Groupe)
				$obj3->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtSalle rows

				$key4 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtSallePeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtSallePeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtSalle)
				$obj4->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key5 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtCreneauPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtCreneauPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (EdtCreneau)
				$obj5->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key6 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = UtilisateurProfessionnelPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$omClass = UtilisateurProfessionnelPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj6 (UtilisateurProfessionnel)
				$obj6->addEdtEmplacementCours($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of EdtEmplacementCours objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $c
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of EdtEmplacementCours objects.
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

		EdtEmplacementCoursPeer::addSelectColumns($c);
		$startcol2 = (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($c);
		$startcol4 = $startcol3 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtSallePeer::addSelectColumns($c);
		$startcol5 = $startcol4 + (EdtSallePeer::NUM_COLUMNS - EdtSallePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($c);
		$startcol6 = $startcol5 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCalendrierPeriodePeer::addSelectColumns($c);
		$startcol7 = $startcol6 + (EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS);

				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_GROUPE,), array(GroupePeer::ID,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_SALLE,), array(EdtSallePeer::ID_SALLE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE,), array(EdtCreneauPeer::ID_DEFINIE_PERIODE,), $join_behavior);
				$c->addJoin(array(EdtEmplacementCoursPeer::ID_CALENDRIER,), array(EdtCalendrierPeriodePeer::ID_CALENDRIER,), $join_behavior);

		$stmt = BasePeer::doSelect($c, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = EdtEmplacementCoursPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$omClass = EdtEmplacementCoursPeer::getOMClass();

				$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
				$obj1 = new $cls();
				$obj1->hydrate($row);
				EdtEmplacementCoursPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj2 (Groupe)
				$obj2->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key3 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = GroupePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$omClass = GroupePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					GroupePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj3 (Groupe)
				$obj3->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtSalle rows

				$key4 = EdtSallePeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtSallePeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$omClass = EdtSallePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtSallePeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj4 (EdtSalle)
				$obj4->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key5 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtCreneauPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$omClass = EdtCreneauPeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtCreneauPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj5 (EdtCreneau)
				$obj5->addEdtEmplacementCours($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCalendrierPeriode rows

				$key6 = EdtCalendrierPeriodePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = EdtCalendrierPeriodePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$omClass = EdtCalendrierPeriodePeer::getOMClass();


					$cls = substr('.'.$omClass, strrpos('.'.$omClass, '.') + 1);
					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					EdtCalendrierPeriodePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (EdtEmplacementCours) to the collection in $obj6 (EdtCalendrierPeriode)
				$obj6->addEdtEmplacementCours($obj1);

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
		return EdtEmplacementCoursPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a EdtEmplacementCours or Criteria object.
	 *
	 * @param      mixed $values Criteria or EdtEmplacementCours object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from EdtEmplacementCours object
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
	 * Method perform an UPDATE on the database, given a EdtEmplacementCours or Criteria object.
	 *
	 * @param      mixed $values Criteria or EdtEmplacementCours object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(EdtEmplacementCoursPeer::ID_COURS);
			$selectCriteria->add(EdtEmplacementCoursPeer::ID_COURS, $criteria->remove(EdtEmplacementCoursPeer::ID_COURS), $comparison);

		} else { // $values is EdtEmplacementCours object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the edt_cours table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			EdtEmplacementCoursPeer::doOnDeleteSetNull(new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(EdtEmplacementCoursPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a EdtEmplacementCours or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or EdtEmplacementCours object or primary key or array of primary keys
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
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			EdtEmplacementCoursPeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof EdtEmplacementCours) {
			// invalidate the cache for this single object
			EdtEmplacementCoursPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(EdtEmplacementCoursPeer::ID_COURS, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				EdtEmplacementCoursPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			EdtEmplacementCoursPeer::doOnDeleteSetNull($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					EdtEmplacementCoursPeer::clearInstancePool();
				} else { // it's a PK or object
					EdtEmplacementCoursPeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in AbsenceEleveSaisiePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			AbsenceEleveSaisiePeer::clearInstancePool();

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
		$objects = EdtEmplacementCoursPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {

			// set fkey col in related AbsenceEleveSaisie rows to NULL
			$selectCriteria = new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME);
			$updateValues = new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $obj->getIdCours());
			$updateValues->add(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

		}
	}

	/**
	 * Validates all modified columns of given EdtEmplacementCours object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      EdtEmplacementCours $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(EdtEmplacementCours $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(EdtEmplacementCoursPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(EdtEmplacementCoursPeer::TABLE_NAME);

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

		return BasePeer::doValidate(EdtEmplacementCoursPeer::DATABASE_NAME, EdtEmplacementCoursPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     EdtEmplacementCours
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = EdtEmplacementCoursPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME);
		$criteria->add(EdtEmplacementCoursPeer::ID_COURS, $pk);

		$v = EdtEmplacementCoursPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(EdtEmplacementCoursPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(EdtEmplacementCoursPeer::DATABASE_NAME);
			$criteria->add(EdtEmplacementCoursPeer::ID_COURS, $pks, Criteria::IN);
			$objs = EdtEmplacementCoursPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseEdtEmplacementCoursPeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the EdtEmplacementCoursPeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the EdtEmplacementCoursPeer class:
//
// Propel::getDatabaseMap(EdtEmplacementCoursPeer::DATABASE_NAME)->addTableBuilder(EdtEmplacementCoursPeer::TABLE_NAME, EdtEmplacementCoursPeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseEdtEmplacementCoursPeer::DATABASE_NAME)->addTableBuilder(BaseEdtEmplacementCoursPeer::TABLE_NAME, BaseEdtEmplacementCoursPeer::getMapBuilder());

