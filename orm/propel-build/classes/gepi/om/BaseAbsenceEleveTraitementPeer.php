<?php


/**
 * Base static class for performing query and update operations on the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTraitementPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_traitements';

	/** the related Propel class for this table */
	const OM_CLASS = 'AbsenceEleveTraitement';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceEleveTraitement';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AbsenceEleveTraitementTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 9;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'a_traitements.ID';

	/** the column name for the UTILISATEUR_ID field */
	const UTILISATEUR_ID = 'a_traitements.UTILISATEUR_ID';

	/** the column name for the A_TYPE_ID field */
	const A_TYPE_ID = 'a_traitements.A_TYPE_ID';

	/** the column name for the A_MOTIF_ID field */
	const A_MOTIF_ID = 'a_traitements.A_MOTIF_ID';

	/** the column name for the A_JUSTIFICATION_ID field */
	const A_JUSTIFICATION_ID = 'a_traitements.A_JUSTIFICATION_ID';

	/** the column name for the COMMENTAIRE field */
	const COMMENTAIRE = 'a_traitements.COMMENTAIRE';

	/** the column name for the MODIFIE_PAR_UTILISATEUR_ID field */
	const MODIFIE_PAR_UTILISATEUR_ID = 'a_traitements.MODIFIE_PAR_UTILISATEUR_ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'a_traitements.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'a_traitements.UPDATED_AT';

	/**
	 * An identiy map to hold any loaded instances of AbsenceEleveTraitement objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceEleveTraitement[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'UtilisateurId', 'ATypeId', 'AMotifId', 'AJustificationId', 'Commentaire', 'ModifieParUtilisateurId', 'CreatedAt', 'UpdatedAt', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'utilisateurId', 'aTypeId', 'aMotifId', 'aJustificationId', 'commentaire', 'modifieParUtilisateurId', 'createdAt', 'updatedAt', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::UTILISATEUR_ID, self::A_TYPE_ID, self::A_MOTIF_ID, self::A_JUSTIFICATION_ID, self::COMMENTAIRE, self::MODIFIE_PAR_UTILISATEUR_ID, self::CREATED_AT, self::UPDATED_AT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'UTILISATEUR_ID', 'A_TYPE_ID', 'A_MOTIF_ID', 'A_JUSTIFICATION_ID', 'COMMENTAIRE', 'MODIFIE_PAR_UTILISATEUR_ID', 'CREATED_AT', 'UPDATED_AT', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'utilisateur_id', 'a_type_id', 'a_motif_id', 'a_justification_id', 'commentaire', 'modifie_par_utilisateur_id', 'created_at', 'updated_at', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'UtilisateurId' => 1, 'ATypeId' => 2, 'AMotifId' => 3, 'AJustificationId' => 4, 'Commentaire' => 5, 'ModifieParUtilisateurId' => 6, 'CreatedAt' => 7, 'UpdatedAt' => 8, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'utilisateurId' => 1, 'aTypeId' => 2, 'aMotifId' => 3, 'aJustificationId' => 4, 'commentaire' => 5, 'modifieParUtilisateurId' => 6, 'createdAt' => 7, 'updatedAt' => 8, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::UTILISATEUR_ID => 1, self::A_TYPE_ID => 2, self::A_MOTIF_ID => 3, self::A_JUSTIFICATION_ID => 4, self::COMMENTAIRE => 5, self::MODIFIE_PAR_UTILISATEUR_ID => 6, self::CREATED_AT => 7, self::UPDATED_AT => 8, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'UTILISATEUR_ID' => 1, 'A_TYPE_ID' => 2, 'A_MOTIF_ID' => 3, 'A_JUSTIFICATION_ID' => 4, 'COMMENTAIRE' => 5, 'MODIFIE_PAR_UTILISATEUR_ID' => 6, 'CREATED_AT' => 7, 'UPDATED_AT' => 8, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'utilisateur_id' => 1, 'a_type_id' => 2, 'a_motif_id' => 3, 'a_justification_id' => 4, 'commentaire' => 5, 'modifie_par_utilisateur_id' => 6, 'created_at' => 7, 'updated_at' => 8, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, )
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
	 * @param      string $column The column name for current table. (i.e. AbsenceEleveTraitementPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceEleveTraitementPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::UTILISATEUR_ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::A_TYPE_ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::A_MOTIF_ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::COMMENTAIRE);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::CREATED_AT);
			$criteria->addSelectColumn(AbsenceEleveTraitementPeer::UPDATED_AT);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.UTILISATEUR_ID');
			$criteria->addSelectColumn($alias . '.A_TYPE_ID');
			$criteria->addSelectColumn($alias . '.A_MOTIF_ID');
			$criteria->addSelectColumn($alias . '.A_JUSTIFICATION_ID');
			$criteria->addSelectColumn($alias . '.COMMENTAIRE');
			$criteria->addSelectColumn($alias . '.MODIFIE_PAR_UTILISATEUR_ID');
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
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceEleveTraitement
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceEleveTraitementPeer::doSelect($critcopy, $con);
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
		return AbsenceEleveTraitementPeer::populateObjects(AbsenceEleveTraitementPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
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
	 * @param      AbsenceEleveTraitement $value A AbsenceEleveTraitement object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(AbsenceEleveTraitement $obj, $key = null)
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
	 * @param      mixed $value A AbsenceEleveTraitement object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceEleveTraitement) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceEleveTraitement object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceEleveTraitement Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to a_traitements
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in JTraitementSaisieElevePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JTraitementSaisieElevePeer::clearInstancePool();
		// Invalidate objects in AbsenceEleveNotificationPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceEleveNotificationPeer::clearInstancePool();
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
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
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
		return (int) $row[$startcol];
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
		$cls = AbsenceEleveTraitementPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceEleveTraitementPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceEleveTraitementPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AbsenceEleveTraitement object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AbsenceEleveTraitementPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AbsenceEleveTraitementPeer::NUM_COLUMNS;
		} else {
			$cls = AbsenceEleveTraitementPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AbsenceEleveTraitementPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveType table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceEleveType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveMotif table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceEleveMotif(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveJustification table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceEleveJustification(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related ModifieParUtilisateur table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinModifieParUtilisateur(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinUtilisateurProfessionnel(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with their AbsenceEleveType objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceEleveType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceEleveTypePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveTypePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceEleveTypePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to $obj2 (AbsenceEleveType)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with their AbsenceEleveMotif objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceEleveMotif(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceEleveMotifPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveMotifPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceEleveMotifPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to $obj2 (AbsenceEleveMotif)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with their AbsenceEleveJustification objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceEleveJustification(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		AbsenceEleveJustificationPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveJustificationPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to $obj2 (AbsenceEleveJustification)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinModifieParUtilisateur(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to $obj2 (UtilisateurProfessionnel)
				$obj2->addModifiedAbsenceEleveTraitement($obj1);

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
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
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

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveMotifPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveMotifPeer::NUM_COLUMNS - AbsenceEleveMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveJustificationPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (AbsenceEleveJustificationPeer::NUM_COLUMNS - AbsenceEleveJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined UtilisateurProfessionnel rows

			$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceEleveType rows

			$key3 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = AbsenceEleveTypePeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveTypePeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveType)
				$obj3->addAbsenceEleveTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceEleveMotif rows

			$key4 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = AbsenceEleveMotifPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveMotifPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveMotif)
				$obj4->addAbsenceEleveTraitement($obj1);
			} // if joined row not null

			// Add objects for joined AbsenceEleveJustification rows

			$key5 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = AbsenceEleveJustificationPeer::getInstanceFromPool($key5);
				if (!$obj5) {

					$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj5 (AbsenceEleveJustification)
				$obj5->addAbsenceEleveTraitement($obj1);
			} // if joined row not null

			// Add objects for joined UtilisateurProfessionnel rows

			$key6 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol6);
			if ($key6 !== null) {
				$obj6 = UtilisateurProfessionnelPeer::getInstanceFromPool($key6);
				if (!$obj6) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj6, $key6);
				} // if obj6 loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj6 (UtilisateurProfessionnel)
				$obj6->addModifiedAbsenceEleveTraitement($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related UtilisateurProfessionnel table
	 *
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveType table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceEleveType(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveMotif table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceEleveMotif(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveJustification table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAbsenceEleveJustification(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related ModifieParUtilisateur table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptModifieParUtilisateur(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptUtilisateurProfessionnel(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveMotifPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveMotifPeer::NUM_COLUMNS - AbsenceEleveMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveJustificationPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveJustificationPeer::NUM_COLUMNS - AbsenceEleveJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined AbsenceEleveType rows

				$key2 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = AbsenceEleveTypePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AbsenceEleveTypePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (AbsenceEleveType)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveMotif rows

				$key3 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceEleveMotifPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveMotifPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveMotif)
				$obj3->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveJustification rows

				$key4 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceEleveJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveJustification)
				$obj4->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects except AbsenceEleveType.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceEleveType(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveMotifPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveMotifPeer::NUM_COLUMNS - AbsenceEleveMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveJustificationPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveJustificationPeer::NUM_COLUMNS - AbsenceEleveJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined UtilisateurProfessionnel rows

				$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveMotif rows

				$key3 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceEleveMotifPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveMotifPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveMotif)
				$obj3->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveJustification rows

				$key4 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceEleveJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveJustification)
				$obj4->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key5 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = UtilisateurProfessionnelPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj5 (UtilisateurProfessionnel)
				$obj5->addModifiedAbsenceEleveTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects except AbsenceEleveMotif.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceEleveMotif(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveJustificationPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveJustificationPeer::NUM_COLUMNS - AbsenceEleveJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined UtilisateurProfessionnel rows

				$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveType rows

				$key3 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceEleveTypePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveTypePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveType)
				$obj3->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveJustification rows

				$key4 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceEleveJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveJustification)
				$obj4->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key5 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = UtilisateurProfessionnelPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj5 (UtilisateurProfessionnel)
				$obj5->addModifiedAbsenceEleveTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects except AbsenceEleveJustification.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAbsenceEleveJustification(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveMotifPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveMotifPeer::NUM_COLUMNS - AbsenceEleveMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined UtilisateurProfessionnel rows

				$key2 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = UtilisateurProfessionnelPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveType rows

				$key3 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceEleveTypePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveTypePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveType)
				$obj3->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveMotif rows

				$key4 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceEleveMotifPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveMotifPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveMotif)
				$obj4->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key5 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = UtilisateurProfessionnelPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj5 (UtilisateurProfessionnel)
				$obj5->addModifiedAbsenceEleveTraitement($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveTraitement objects pre-filled with all related objects except ModifieParUtilisateur.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveTraitement objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptModifieParUtilisateur(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTraitementPeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveMotifPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (AbsenceEleveMotifPeer::NUM_COLUMNS - AbsenceEleveMotifPeer::NUM_LAZY_LOAD_COLUMNS);

		AbsenceEleveJustificationPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (AbsenceEleveJustificationPeer::NUM_COLUMNS - AbsenceEleveJustificationPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_TYPE_ID, AbsenceEleveTypePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_MOTIF_ID, AbsenceEleveMotifPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, AbsenceEleveJustificationPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTraitementPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTraitementPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTraitementPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTraitementPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined AbsenceEleveType rows

				$key2 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = AbsenceEleveTypePeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = AbsenceEleveTypePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AbsenceEleveTypePeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj2 (AbsenceEleveType)
				$obj2->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveMotif rows

				$key3 = AbsenceEleveMotifPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = AbsenceEleveMotifPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = AbsenceEleveMotifPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					AbsenceEleveMotifPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj3 (AbsenceEleveMotif)
				$obj3->addAbsenceEleveTraitement($obj1);

			} // if joined row is not null

				// Add objects for joined AbsenceEleveJustification rows

				$key4 = AbsenceEleveJustificationPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = AbsenceEleveJustificationPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = AbsenceEleveJustificationPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					AbsenceEleveJustificationPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveTraitement) to the collection in $obj4 (AbsenceEleveJustification)
				$obj4->addAbsenceEleveTraitement($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseAbsenceEleveTraitementPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAbsenceEleveTraitementPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AbsenceEleveTraitementTableMap());
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
		return $withPrefix ? AbsenceEleveTraitementPeer::CLASS_DEFAULT : AbsenceEleveTraitementPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a AbsenceEleveTraitement or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveTraitement object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceEleveTraitement object
		}

		if ($criteria->containsKey(AbsenceEleveTraitementPeer::ID) && $criteria->keyContainsValue(AbsenceEleveTraitementPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveTraitementPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a AbsenceEleveTraitement or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveTraitement object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceEleveTraitementPeer::ID);
			$value = $criteria->remove(AbsenceEleveTraitementPeer::ID);
			if ($value) {
				$selectCriteria->add(AbsenceEleveTraitementPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceEleveTraitementPeer::TABLE_NAME);
			}

		} else { // $values is AbsenceEleveTraitement object
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AbsenceEleveTraitementPeer::doOnDeleteCascade(new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AbsenceEleveTraitementPeer::TABLE_NAME, $con, AbsenceEleveTraitementPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AbsenceEleveTraitementPeer::clearInstancePool();
			AbsenceEleveTraitementPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a AbsenceEleveTraitement or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveTraitement object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceEleveTraitement) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AbsenceEleveTraitementPeer::ID, (array) $values, Criteria::IN);
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
			$affectedRows += AbsenceEleveTraitementPeer::doOnDeleteCascade($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				AbsenceEleveTraitementPeer::clearInstancePool();
			} elseif ($values instanceof AbsenceEleveTraitement) { // it's a model object
				AbsenceEleveTraitementPeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					AbsenceEleveTraitementPeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			AbsenceEleveTraitementPeer::clearRelatedInstancePool();
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
		$objects = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JTraitementSaisieEleve objects
			$criteria = new Criteria(JTraitementSaisieElevePeer::DATABASE_NAME);
			
			$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $obj->getId());
			$affectedRows += JTraitementSaisieElevePeer::doDelete($criteria, $con);

			// delete related AbsenceEleveNotification objects
			$criteria = new Criteria(AbsenceEleveNotificationPeer::DATABASE_NAME);
			
			$criteria->add(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $obj->getId());
			$affectedRows += AbsenceEleveNotificationPeer::doDelete($criteria, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given AbsenceEleveTraitement object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceEleveTraitement $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(AbsenceEleveTraitement $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceEleveTraitementPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceEleveTraitementPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceEleveTraitementPeer::DATABASE_NAME, AbsenceEleveTraitementPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AbsenceEleveTraitement
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AbsenceEleveTraitementPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveTraitementPeer::ID, $pk);

		$v = AbsenceEleveTraitementPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
			$criteria->add(AbsenceEleveTraitementPeer::ID, $pks, Criteria::IN);
			$objs = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAbsenceEleveTraitementPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAbsenceEleveTraitementPeer::buildTableMap();

