<?php


/**
 * Base static class for performing query and update operations on the 'j_groupes_classes' table.
 *
 * Table permettant la jointure entre groupe d'enseignement et une classe. Cette jointure permet de definir un enseignement, c'est à dire un groupe d'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJGroupesClassesPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'j_groupes_classes';

	/** the related Propel class for this table */
	const OM_CLASS = 'JGroupesClasses';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.JGroupesClasses';

	/** the related TableMap class for this table */
	const TM_CLASS = 'JGroupesClassesTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 7;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 7;

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'j_groupes_classes.ID_GROUPE';

	/** the column name for the ID_CLASSE field */
	const ID_CLASSE = 'j_groupes_classes.ID_CLASSE';

	/** the column name for the PRIORITE field */
	const PRIORITE = 'j_groupes_classes.PRIORITE';

	/** the column name for the COEF field */
	const COEF = 'j_groupes_classes.COEF';

	/** the column name for the CATEGORIE_ID field */
	const CATEGORIE_ID = 'j_groupes_classes.CATEGORIE_ID';

	/** the column name for the SAISIE_ECTS field */
	const SAISIE_ECTS = 'j_groupes_classes.SAISIE_ECTS';

	/** the column name for the VALEUR_ECTS field */
	const VALEUR_ECTS = 'j_groupes_classes.VALEUR_ECTS';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';
	
	/**
	 * An identiy map to hold any loaded instances of JGroupesClasses objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array JGroupesClasses[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('IdGroupe', 'IdClasse', 'Priorite', 'Coef', 'CategorieId', 'SaisieEcts', 'ValeurEcts', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idGroupe', 'idClasse', 'priorite', 'coef', 'categorieId', 'saisieEcts', 'valeurEcts', ),
		BasePeer::TYPE_COLNAME => array (self::ID_GROUPE, self::ID_CLASSE, self::PRIORITE, self::COEF, self::CATEGORIE_ID, self::SAISIE_ECTS, self::VALEUR_ECTS, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID_GROUPE', 'ID_CLASSE', 'PRIORITE', 'COEF', 'CATEGORIE_ID', 'SAISIE_ECTS', 'VALEUR_ECTS', ),
		BasePeer::TYPE_FIELDNAME => array ('id_groupe', 'id_classe', 'priorite', 'coef', 'categorie_id', 'saisie_ects', 'valeur_ects', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('IdGroupe' => 0, 'IdClasse' => 1, 'Priorite' => 2, 'Coef' => 3, 'CategorieId' => 4, 'SaisieEcts' => 5, 'ValeurEcts' => 6, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('idGroupe' => 0, 'idClasse' => 1, 'priorite' => 2, 'coef' => 3, 'categorieId' => 4, 'saisieEcts' => 5, 'valeurEcts' => 6, ),
		BasePeer::TYPE_COLNAME => array (self::ID_GROUPE => 0, self::ID_CLASSE => 1, self::PRIORITE => 2, self::COEF => 3, self::CATEGORIE_ID => 4, self::SAISIE_ECTS => 5, self::VALEUR_ECTS => 6, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID_GROUPE' => 0, 'ID_CLASSE' => 1, 'PRIORITE' => 2, 'COEF' => 3, 'CATEGORIE_ID' => 4, 'SAISIE_ECTS' => 5, 'VALEUR_ECTS' => 6, ),
		BasePeer::TYPE_FIELDNAME => array ('id_groupe' => 0, 'id_classe' => 1, 'priorite' => 2, 'coef' => 3, 'categorie_id' => 4, 'saisie_ects' => 5, 'valeur_ects' => 6, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
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
	 * @param      string $column The column name for current table. (i.e. JGroupesClassesPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(JGroupesClassesPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(JGroupesClassesPeer::ID_GROUPE);
			$criteria->addSelectColumn(JGroupesClassesPeer::ID_CLASSE);
			$criteria->addSelectColumn(JGroupesClassesPeer::PRIORITE);
			$criteria->addSelectColumn(JGroupesClassesPeer::COEF);
			$criteria->addSelectColumn(JGroupesClassesPeer::CATEGORIE_ID);
			$criteria->addSelectColumn(JGroupesClassesPeer::SAISIE_ECTS);
			$criteria->addSelectColumn(JGroupesClassesPeer::VALEUR_ECTS);
		} else {
			$criteria->addSelectColumn($alias . '.ID_GROUPE');
			$criteria->addSelectColumn($alias . '.ID_CLASSE');
			$criteria->addSelectColumn($alias . '.PRIORITE');
			$criteria->addSelectColumn($alias . '.COEF');
			$criteria->addSelectColumn($alias . '.CATEGORIE_ID');
			$criteria->addSelectColumn($alias . '.SAISIE_ECTS');
			$criteria->addSelectColumn($alias . '.VALEUR_ECTS');
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
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     JGroupesClasses
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = JGroupesClassesPeer::doSelect($critcopy, $con);
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
		return JGroupesClassesPeer::populateObjects(JGroupesClassesPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			JGroupesClassesPeer::addSelectColumns($criteria);
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
	 * @param      JGroupesClasses $value A JGroupesClasses object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getIdGroupe(), (string) $obj->getIdClasse()));
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
	 * @param      mixed $value A JGroupesClasses object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof JGroupesClasses) {
				$key = serialize(array((string) $value->getIdGroupe(), (string) $value->getIdClasse()));
			} elseif (is_array($value) && count($value) === 2) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or JGroupesClasses object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     JGroupesClasses Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to j_groupes_classes
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
		return array((int) $row[$startcol], (int) $row[$startcol + 1]);
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
		$cls = JGroupesClassesPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = JGroupesClassesPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				JGroupesClassesPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (JGroupesClasses object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = JGroupesClassesPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = JGroupesClassesPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			JGroupesClassesPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related Classe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinClasse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CategorieMatiere table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCategorieMatiere(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

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
	 * Selects a collection of JGroupesClasses objects pre-filled with their Groupe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinGroupe(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;
		GroupePeer::addSelectColumns($criteria);

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to $obj2 (Groupe)
				$obj2->addJGroupesClasses($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JGroupesClasses objects pre-filled with their Classe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinClasse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;
		ClassePeer::addSelectColumns($criteria);

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = ClassePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = ClassePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					ClassePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to $obj2 (Classe)
				$obj2->addJGroupesClasses($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JGroupesClasses objects pre-filled with their CategorieMatiere objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCategorieMatiere(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;
		CategorieMatierePeer::addSelectColumns($criteria);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CategorieMatierePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CategorieMatierePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CategorieMatierePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CategorieMatierePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to $obj2 (CategorieMatiere)
				$obj2->addJGroupesClasses($obj1);

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
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

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
	 * Selects a collection of JGroupesClasses objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
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

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol2 = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		ClassePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + ClassePeer::NUM_HYDRATE_COLUMNS;

		CategorieMatierePeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + CategorieMatierePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined Groupe rows

			$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = GroupePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj2 (Groupe)
				$obj2->addJGroupesClasses($obj1);
			} // if joined row not null

			// Add objects for joined Classe rows

			$key3 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = ClassePeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = ClassePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ClassePeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj3 (Classe)
				$obj3->addJGroupesClasses($obj1);
			} // if joined row not null

			// Add objects for joined CategorieMatiere rows

			$key4 = CategorieMatierePeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = CategorieMatierePeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = CategorieMatierePeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CategorieMatierePeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj4 (CategorieMatiere)
				$obj4->addJGroupesClasses($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related Groupe table
	 *
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related Classe table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptClasse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CategorieMatiere table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCategorieMatiere(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			JGroupesClassesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

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
	 * Selects a collection of JGroupesClasses objects pre-filled with all related objects except Groupe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptGroupe(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol2 = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;

		ClassePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + ClassePeer::NUM_HYDRATE_COLUMNS;

		CategorieMatierePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + CategorieMatierePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Classe rows

				$key2 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = ClassePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					ClassePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj2 (Classe)
				$obj2->addJGroupesClasses($obj1);

			} // if joined row is not null

				// Add objects for joined CategorieMatiere rows

				$key3 = CategorieMatierePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CategorieMatierePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CategorieMatierePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CategorieMatierePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj3 (CategorieMatiere)
				$obj3->addJGroupesClasses($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JGroupesClasses objects pre-filled with all related objects except Classe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptClasse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol2 = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		CategorieMatierePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + CategorieMatierePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::CATEGORIE_ID, CategorieMatierePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj2 (Groupe)
				$obj2->addJGroupesClasses($obj1);

			} // if joined row is not null

				// Add objects for joined CategorieMatiere rows

				$key3 = CategorieMatierePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CategorieMatierePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CategorieMatierePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CategorieMatierePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj3 (CategorieMatiere)
				$obj3->addJGroupesClasses($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of JGroupesClasses objects pre-filled with all related objects except CategorieMatiere.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of JGroupesClasses objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCategorieMatiere(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		JGroupesClassesPeer::addSelectColumns($criteria);
		$startcol2 = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS;

		GroupePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + GroupePeer::NUM_HYDRATE_COLUMNS;

		ClassePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + ClassePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(JGroupesClassesPeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(JGroupesClassesPeer::ID_CLASSE, ClassePeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = JGroupesClassesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = JGroupesClassesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = JGroupesClassesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				JGroupesClassesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined Groupe rows

				$key2 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = GroupePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					GroupePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj2 (Groupe)
				$obj2->addJGroupesClasses($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key3 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ClassePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ClassePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (JGroupesClasses) to the collection in $obj3 (Classe)
				$obj3->addJGroupesClasses($obj1);

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
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseJGroupesClassesPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseJGroupesClassesPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new JGroupesClassesTableMap());
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
		return $withPrefix ? JGroupesClassesPeer::CLASS_DEFAULT : JGroupesClassesPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a JGroupesClasses or Criteria object.
	 *
	 * @param      mixed $values Criteria or JGroupesClasses object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from JGroupesClasses object
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
	 * Performs an UPDATE on the database, given a JGroupesClasses or Criteria object.
	 *
	 * @param      mixed $values Criteria or JGroupesClasses object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(JGroupesClassesPeer::ID_GROUPE);
			$value = $criteria->remove(JGroupesClassesPeer::ID_GROUPE);
			if ($value) {
				$selectCriteria->add(JGroupesClassesPeer::ID_GROUPE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(JGroupesClassesPeer::ID_CLASSE);
			$value = $criteria->remove(JGroupesClassesPeer::ID_CLASSE);
			if ($value) {
				$selectCriteria->add(JGroupesClassesPeer::ID_CLASSE, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(JGroupesClassesPeer::TABLE_NAME);
			}

		} else { // $values is JGroupesClasses object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the j_groupes_classes table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(JGroupesClassesPeer::TABLE_NAME, $con, JGroupesClassesPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			JGroupesClassesPeer::clearInstancePool();
			JGroupesClassesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a JGroupesClasses or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or JGroupesClasses object or primary key or array of primary keys
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
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			JGroupesClassesPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof JGroupesClasses) { // it's a model object
			// invalidate the cache for this single object
			JGroupesClassesPeer::removeInstanceFromPool($values);
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
				$criterion = $criteria->getNewCriterion(JGroupesClassesPeer::ID_GROUPE, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(JGroupesClassesPeer::ID_CLASSE, $value[1]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				JGroupesClassesPeer::removeInstanceFromPool($value);
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
			JGroupesClassesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given JGroupesClasses object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      JGroupesClasses $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(JGroupesClassesPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(JGroupesClassesPeer::TABLE_NAME);

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

		return BasePeer::doValidate(JGroupesClassesPeer::DATABASE_NAME, JGroupesClassesPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $id_groupe
	 * @param      int $id_classe
	 * @param      PropelPDO $con
	 * @return     JGroupesClasses
	 */
	public static function retrieveByPK($id_groupe, $id_classe, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $id_groupe, (string) $id_classe));
 		if (null !== ($obj = JGroupesClassesPeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(JGroupesClassesPeer::DATABASE_NAME);
		$criteria->add(JGroupesClassesPeer::ID_GROUPE, $id_groupe);
		$criteria->add(JGroupesClassesPeer::ID_CLASSE, $id_classe);
		$v = JGroupesClassesPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseJGroupesClassesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseJGroupesClassesPeer::buildTableMap();

