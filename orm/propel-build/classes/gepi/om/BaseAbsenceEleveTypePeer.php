<?php


/**
 * Base static class for performing query and update operations on the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTypePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_types';

	/** the related Propel class for this table */
	const OM_CLASS = 'AbsenceEleveType';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceEleveType';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AbsenceEleveTypeTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 12;

	/** the column name for the ID field */
	const ID = 'a_types.ID';

	/** the column name for the NOM field */
	const NOM = 'a_types.NOM';

	/** the column name for the JUSTIFICATION_EXIGIBLE field */
	const JUSTIFICATION_EXIGIBLE = 'a_types.JUSTIFICATION_EXIGIBLE';

	/** the column name for the SOUS_RESPONSABILITE_ETABLISSEMENT field */
	const SOUS_RESPONSABILITE_ETABLISSEMENT = 'a_types.SOUS_RESPONSABILITE_ETABLISSEMENT';

	/** the column name for the MANQUEMENT_OBLIGATION_PRESENCE field */
	const MANQUEMENT_OBLIGATION_PRESENCE = 'a_types.MANQUEMENT_OBLIGATION_PRESENCE';

	/** the column name for the RETARD_BULLETIN field */
	const RETARD_BULLETIN = 'a_types.RETARD_BULLETIN';

	/** the column name for the TYPE_SAISIE field */
	const TYPE_SAISIE = 'a_types.TYPE_SAISIE';

	/** the column name for the COMMENTAIRE field */
	const COMMENTAIRE = 'a_types.COMMENTAIRE';

	/** the column name for the ID_LIEU field */
	const ID_LIEU = 'a_types.ID_LIEU';

	/** the column name for the SORTABLE_RANK field */
	const SORTABLE_RANK = 'a_types.SORTABLE_RANK';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'a_types.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'a_types.UPDATED_AT';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';
	
	/**
	 * An identiy map to hold any loaded instances of AbsenceEleveType objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceEleveType[]
	 */
	public static $instances = array();


	// sortable behavior
	
	/**
	 * rank column
	 */
	const RANK_COL = 'a_types.SORTABLE_RANK';

	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Nom', 'JustificationExigible', 'SousResponsabiliteEtablissement', 'ManquementObligationPresence', 'RetardBulletin', 'TypeSaisie', 'Commentaire', 'IdLieu', 'SortableRank', 'CreatedAt', 'UpdatedAt', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'nom', 'justificationExigible', 'sousResponsabiliteEtablissement', 'manquementObligationPresence', 'retardBulletin', 'typeSaisie', 'commentaire', 'idLieu', 'sortableRank', 'createdAt', 'updatedAt', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::NOM, self::JUSTIFICATION_EXIGIBLE, self::SOUS_RESPONSABILITE_ETABLISSEMENT, self::MANQUEMENT_OBLIGATION_PRESENCE, self::RETARD_BULLETIN, self::TYPE_SAISIE, self::COMMENTAIRE, self::ID_LIEU, self::SORTABLE_RANK, self::CREATED_AT, self::UPDATED_AT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'NOM', 'JUSTIFICATION_EXIGIBLE', 'SOUS_RESPONSABILITE_ETABLISSEMENT', 'MANQUEMENT_OBLIGATION_PRESENCE', 'RETARD_BULLETIN', 'TYPE_SAISIE', 'COMMENTAIRE', 'ID_LIEU', 'SORTABLE_RANK', 'CREATED_AT', 'UPDATED_AT', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'nom', 'justification_exigible', 'sous_responsabilite_etablissement', 'manquement_obligation_presence', 'retard_bulletin', 'type_saisie', 'commentaire', 'id_lieu', 'sortable_rank', 'created_at', 'updated_at', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Nom' => 1, 'JustificationExigible' => 2, 'SousResponsabiliteEtablissement' => 3, 'ManquementObligationPresence' => 4, 'RetardBulletin' => 5, 'TypeSaisie' => 6, 'Commentaire' => 7, 'IdLieu' => 8, 'SortableRank' => 9, 'CreatedAt' => 10, 'UpdatedAt' => 11, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'nom' => 1, 'justificationExigible' => 2, 'sousResponsabiliteEtablissement' => 3, 'manquementObligationPresence' => 4, 'retardBulletin' => 5, 'typeSaisie' => 6, 'commentaire' => 7, 'idLieu' => 8, 'sortableRank' => 9, 'createdAt' => 10, 'updatedAt' => 11, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::NOM => 1, self::JUSTIFICATION_EXIGIBLE => 2, self::SOUS_RESPONSABILITE_ETABLISSEMENT => 3, self::MANQUEMENT_OBLIGATION_PRESENCE => 4, self::RETARD_BULLETIN => 5, self::TYPE_SAISIE => 6, self::COMMENTAIRE => 7, self::ID_LIEU => 8, self::SORTABLE_RANK => 9, self::CREATED_AT => 10, self::UPDATED_AT => 11, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'NOM' => 1, 'JUSTIFICATION_EXIGIBLE' => 2, 'SOUS_RESPONSABILITE_ETABLISSEMENT' => 3, 'MANQUEMENT_OBLIGATION_PRESENCE' => 4, 'RETARD_BULLETIN' => 5, 'TYPE_SAISIE' => 6, 'COMMENTAIRE' => 7, 'ID_LIEU' => 8, 'SORTABLE_RANK' => 9, 'CREATED_AT' => 10, 'UPDATED_AT' => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'nom' => 1, 'justification_exigible' => 2, 'sous_responsabilite_etablissement' => 3, 'manquement_obligation_presence' => 4, 'retard_bulletin' => 5, 'type_saisie' => 6, 'commentaire' => 7, 'id_lieu' => 8, 'sortable_rank' => 9, 'created_at' => 10, 'updated_at' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
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
	 * @param      string $column The column name for current table. (i.e. AbsenceEleveTypePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceEleveTypePeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AbsenceEleveTypePeer::ID);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::NOM);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::SOUS_RESPONSABILITE_ETABLISSEMENT);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::MANQUEMENT_OBLIGATION_PRESENCE);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::RETARD_BULLETIN);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::TYPE_SAISIE);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::COMMENTAIRE);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::ID_LIEU);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::SORTABLE_RANK);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::CREATED_AT);
			$criteria->addSelectColumn(AbsenceEleveTypePeer::UPDATED_AT);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.NOM');
			$criteria->addSelectColumn($alias . '.JUSTIFICATION_EXIGIBLE');
			$criteria->addSelectColumn($alias . '.SOUS_RESPONSABILITE_ETABLISSEMENT');
			$criteria->addSelectColumn($alias . '.MANQUEMENT_OBLIGATION_PRESENCE');
			$criteria->addSelectColumn($alias . '.RETARD_BULLETIN');
			$criteria->addSelectColumn($alias . '.TYPE_SAISIE');
			$criteria->addSelectColumn($alias . '.COMMENTAIRE');
			$criteria->addSelectColumn($alias . '.ID_LIEU');
			$criteria->addSelectColumn($alias . '.SORTABLE_RANK');
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
		$criteria->setPrimaryTableName(AbsenceEleveTypePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTypePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceEleveType
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceEleveTypePeer::doSelect($critcopy, $con);
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
		return AbsenceEleveTypePeer::populateObjects(AbsenceEleveTypePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceEleveTypePeer::addSelectColumns($criteria);
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
	 * @param      AbsenceEleveType $value A AbsenceEleveType object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
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
	 * @param      mixed $value A AbsenceEleveType object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceEleveType) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceEleveType object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceEleveType Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to a_types
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in AbsenceEleveTypeStatutAutorisePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceEleveTypeStatutAutorisePeer::clearInstancePool();
		// Invalidate objects in AbsenceEleveTraitementPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		AbsenceEleveTraitementPeer::clearInstancePool();
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
		$cls = AbsenceEleveTypePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceEleveTypePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceEleveTypePeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AbsenceEleveType object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AbsenceEleveTypePeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AbsenceEleveTypePeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = AbsenceEleveTypePeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AbsenceEleveTypePeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveLieu table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceEleveLieu(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveTypePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTypePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTypePeer::ID_LIEU, AbsenceEleveLieuPeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveType objects pre-filled with their AbsenceEleveLieu objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveType objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceEleveLieu(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol = AbsenceEleveTypePeer::NUM_HYDRATE_COLUMNS;
		AbsenceEleveLieuPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveTypePeer::ID_LIEU, AbsenceEleveLieuPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTypePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveTypePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTypePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceEleveLieuPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveLieuPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveLieuPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceEleveLieuPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveType) to $obj2 (AbsenceEleveLieu)
				$obj2->addAbsenceEleveType($obj1);

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
		$criteria->setPrimaryTableName(AbsenceEleveTypePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveTypePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveTypePeer::ID_LIEU, AbsenceEleveLieuPeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveType objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveType objects.
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

		AbsenceEleveTypePeer::addSelectColumns($criteria);
		$startcol2 = AbsenceEleveTypePeer::NUM_HYDRATE_COLUMNS;

		AbsenceEleveLieuPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + AbsenceEleveLieuPeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(AbsenceEleveTypePeer::ID_LIEU, AbsenceEleveLieuPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveTypePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveTypePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveTypePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveTypePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined AbsenceEleveLieu rows

			$key2 = AbsenceEleveLieuPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveLieuPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveLieuPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AbsenceEleveLieuPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (AbsenceEleveType) to the collection in $obj2 (AbsenceEleveLieu)
				$obj2->addAbsenceEleveType($obj1);
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
	  $dbMap = Propel::getDatabaseMap(BaseAbsenceEleveTypePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAbsenceEleveTypePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AbsenceEleveTypeTableMap());
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
		return $withPrefix ? AbsenceEleveTypePeer::CLASS_DEFAULT : AbsenceEleveTypePeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a AbsenceEleveType or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveType object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceEleveType object
		}

		if ($criteria->containsKey(AbsenceEleveTypePeer::ID) && $criteria->keyContainsValue(AbsenceEleveTypePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveTypePeer::ID.')');
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
	 * Performs an UPDATE on the database, given a AbsenceEleveType or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveType object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceEleveTypePeer::ID);
			$value = $criteria->remove(AbsenceEleveTypePeer::ID);
			if ($value) {
				$selectCriteria->add(AbsenceEleveTypePeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceEleveTypePeer::TABLE_NAME);
			}

		} else { // $values is AbsenceEleveType object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the a_types table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AbsenceEleveTypePeer::doOnDeleteCascade(new Criteria(AbsenceEleveTypePeer::DATABASE_NAME), $con);
			AbsenceEleveTypePeer::doOnDeleteSetNull(new Criteria(AbsenceEleveTypePeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AbsenceEleveTypePeer::TABLE_NAME, $con, AbsenceEleveTypePeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AbsenceEleveTypePeer::clearInstancePool();
			AbsenceEleveTypePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a AbsenceEleveType or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveType object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceEleveType) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AbsenceEleveTypePeer::ID, (array) $values, Criteria::IN);
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
			$affectedRows += AbsenceEleveTypePeer::doOnDeleteCascade($c, $con);
			
			// cloning the Criteria in case it's modified by doSelect() or doSelectStmt()
			$c = clone $criteria;
			AbsenceEleveTypePeer::doOnDeleteSetNull($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				AbsenceEleveTypePeer::clearInstancePool();
			} elseif ($values instanceof AbsenceEleveType) { // it's a model object
				AbsenceEleveTypePeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					AbsenceEleveTypePeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			AbsenceEleveTypePeer::clearRelatedInstancePool();
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
		$objects = AbsenceEleveTypePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related AbsenceEleveTypeStatutAutorise objects
			$criteria = new Criteria(AbsenceEleveTypeStatutAutorisePeer::DATABASE_NAME);
			
			$criteria->add(AbsenceEleveTypeStatutAutorisePeer::ID_A_TYPE, $obj->getId());
			$affectedRows += AbsenceEleveTypeStatutAutorisePeer::doDelete($criteria, $con);
		}
		return $affectedRows;
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
		$objects = AbsenceEleveTypePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {

			// set fkey col in related AbsenceEleveTraitement rows to NULL
			$selectCriteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
			$updateValues = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $obj->getId());
			$updateValues->add(AbsenceEleveTraitementPeer::A_TYPE_ID, null);

			BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

		}
	}

	/**
	 * Validates all modified columns of given AbsenceEleveType object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceEleveType $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceEleveTypePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceEleveTypePeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceEleveTypePeer::DATABASE_NAME, AbsenceEleveTypePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AbsenceEleveType
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AbsenceEleveTypePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveTypePeer::ID, $pk);

		$v = AbsenceEleveTypePeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
			$criteria->add(AbsenceEleveTypePeer::ID, $pks, Criteria::IN);
			$objs = AbsenceEleveTypePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

	// sortable behavior
	
	/**
	 * Get the highest rank
	 * 
	 * @param     PropelPDO optional connection
	 *
	 * @return    integer highest position
	 */
	public static function getMaxRank(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		// shift the objects with a position lower than the one of object
		$c = new Criteria();
		$c->addSelectColumn('MAX(' . AbsenceEleveTypePeer::RANK_COL . ')');
		$stmt = AbsenceEleveTypePeer::doSelectStmt($c, $con);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * Get an item from the list based on its rank
	 *
	 * @param     integer   $rank rank
	 * @param     PropelPDO $con optional connection
	 *
	 * @return AbsenceEleveType
	 */
	public static function retrieveByRank($rank, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
	
		$c = new Criteria;
		$c->add(AbsenceEleveTypePeer::RANK_COL, $rank);
		
		return AbsenceEleveTypePeer::doSelectOne($c, $con);
	}
	
	/**
	 * Reorder a set of sortable objects based on a list of id/position
	 * Beware that there is no check made on the positions passed
	 * So incoherent positions will result in an incoherent list
	 *
	 * @param     array     $order id => rank pairs
	 * @param     PropelPDO $con   optional connection
	 *
	 * @return    boolean true if the reordering took place, false if a database problem prevented it
	 */
	public static function reorder(array $order, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		
		$con->beginTransaction();
		try {
			$ids = array_keys($order);
			$objects = AbsenceEleveTypePeer::retrieveByPKs($ids);
			foreach ($objects as $object) {
				$pk = $object->getPrimaryKey();
				if ($object->getSortableRank() != $order[$pk]) {
					$object->setSortableRank($order[$pk]);
					$object->save($con);
				}
			}
			$con->commit();
	
			return true;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Return an array of sortable objects ordered by position
	 *
	 * @param     Criteria  $criteria  optional criteria object
	 * @param     string    $order     sorting order, to be chosen between Criteria::ASC (default) and Criteria::DESC
	 * @param     PropelPDO $con       optional connection
	 *
	 * @return    array list of sortable objects
	 */
	public static function doSelectOrderByRank(Criteria $criteria = null, $order = Criteria::ASC, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
	
		if ($criteria === null) {
			$criteria = new Criteria();
		} elseif ($criteria instanceof Criteria) {
			$criteria = clone $criteria;
		}
	
		$criteria->clearOrderByColumns();
	
		if ($order == Criteria::ASC) {
			$criteria->addAscendingOrderByColumn(AbsenceEleveTypePeer::RANK_COL);
		} else {
			$criteria->addDescendingOrderByColumn(AbsenceEleveTypePeer::RANK_COL);
		}
	
		return AbsenceEleveTypePeer::doSelect($criteria, $con);
	}
	
	/**
	 * Adds $delta to all Rank values that are >= $first and <= $last.
	 * '$delta' can also be negative.
	 *
	 * @param      int $delta Value to be shifted by, can be negative
	 * @param      int $first First node to be shifted
	 * @param      int $last  Last node to be shifted
	 * @param      PropelPDO $con Connection to use.
	 */
	public static function shiftRank($delta, $first, $last = null, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
	
		$whereCriteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		$criterion = $whereCriteria->getNewCriterion(AbsenceEleveTypePeer::RANK_COL, $first, Criteria::GREATER_EQUAL);
		if (null !== $last) {
			$criterion->addAnd($whereCriteria->getNewCriterion(AbsenceEleveTypePeer::RANK_COL, $last, Criteria::LESS_EQUAL));
		}
		$whereCriteria->add($criterion);
	
		$valuesCriteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		$valuesCriteria->add(AbsenceEleveTypePeer::RANK_COL, array('raw' => AbsenceEleveTypePeer::RANK_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);
	
		BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);
		AbsenceEleveTypePeer::clearInstancePool();
	}

} // BaseAbsenceEleveTypePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAbsenceEleveTypePeer::buildTableMap();

