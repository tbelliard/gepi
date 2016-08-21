<?php


/**
 * Base static class for performing query and update operations on the 'a_saisies_version' table.
 *
 * 
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisieVersionPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_saisies_version';

	/** the related Propel class for this table */
	const OM_CLASS = 'AbsenceEleveSaisieVersion';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceEleveSaisieVersion';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AbsenceEleveSaisieVersionTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 20;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
	const NUM_HYDRATE_COLUMNS = 20;

	/** the column name for the ID field */
	const ID = 'a_saisies_version.ID';

	/** the column name for the UTILISATEUR_ID field */
	const UTILISATEUR_ID = 'a_saisies_version.UTILISATEUR_ID';

	/** the column name for the ELEVE_ID field */
	const ELEVE_ID = 'a_saisies_version.ELEVE_ID';

	/** the column name for the COMMENTAIRE field */
	const COMMENTAIRE = 'a_saisies_version.COMMENTAIRE';

	/** the column name for the DEBUT_ABS field */
	const DEBUT_ABS = 'a_saisies_version.DEBUT_ABS';

	/** the column name for the FIN_ABS field */
	const FIN_ABS = 'a_saisies_version.FIN_ABS';

	/** the column name for the ID_EDT_CRENEAU field */
	const ID_EDT_CRENEAU = 'a_saisies_version.ID_EDT_CRENEAU';

	/** the column name for the ID_EDT_EMPLACEMENT_COURS field */
	const ID_EDT_EMPLACEMENT_COURS = 'a_saisies_version.ID_EDT_EMPLACEMENT_COURS';

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'a_saisies_version.ID_GROUPE';

	/** the column name for the ID_CLASSE field */
	const ID_CLASSE = 'a_saisies_version.ID_CLASSE';

	/** the column name for the ID_AID field */
	const ID_AID = 'a_saisies_version.ID_AID';

	/** the column name for the ID_S_INCIDENTS field */
	const ID_S_INCIDENTS = 'a_saisies_version.ID_S_INCIDENTS';

	/** the column name for the ID_LIEU field */
	const ID_LIEU = 'a_saisies_version.ID_LIEU';

	/** the column name for the DELETED_BY field */
	const DELETED_BY = 'a_saisies_version.DELETED_BY';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'a_saisies_version.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'a_saisies_version.UPDATED_AT';

	/** the column name for the DELETED_AT field */
	const DELETED_AT = 'a_saisies_version.DELETED_AT';

	/** the column name for the VERSION field */
	const VERSION = 'a_saisies_version.VERSION';

	/** the column name for the VERSION_CREATED_AT field */
	const VERSION_CREATED_AT = 'a_saisies_version.VERSION_CREATED_AT';

	/** the column name for the VERSION_CREATED_BY field */
	const VERSION_CREATED_BY = 'a_saisies_version.VERSION_CREATED_BY';

	/** The default string format for model objects of the related table **/
	const DEFAULT_STRING_FORMAT = 'YAML';

	/**
	 * An identiy map to hold any loaded instances of AbsenceEleveSaisieVersion objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceEleveSaisieVersion[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	protected static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'UtilisateurId', 'EleveId', 'Commentaire', 'DebutAbs', 'FinAbs', 'IdEdtCreneau', 'IdEdtEmplacementCours', 'IdGroupe', 'IdClasse', 'IdAid', 'IdSIncidents', 'IdLieu', 'DeletedBy', 'CreatedAt', 'UpdatedAt', 'DeletedAt', 'Version', 'VersionCreatedAt', 'VersionCreatedBy', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'utilisateurId', 'eleveId', 'commentaire', 'debutAbs', 'finAbs', 'idEdtCreneau', 'idEdtEmplacementCours', 'idGroupe', 'idClasse', 'idAid', 'idSIncidents', 'idLieu', 'deletedBy', 'createdAt', 'updatedAt', 'deletedAt', 'version', 'versionCreatedAt', 'versionCreatedBy', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::UTILISATEUR_ID, self::ELEVE_ID, self::COMMENTAIRE, self::DEBUT_ABS, self::FIN_ABS, self::ID_EDT_CRENEAU, self::ID_EDT_EMPLACEMENT_COURS, self::ID_GROUPE, self::ID_CLASSE, self::ID_AID, self::ID_S_INCIDENTS, self::ID_LIEU, self::DELETED_BY, self::CREATED_AT, self::UPDATED_AT, self::DELETED_AT, self::VERSION, self::VERSION_CREATED_AT, self::VERSION_CREATED_BY, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'UTILISATEUR_ID', 'ELEVE_ID', 'COMMENTAIRE', 'DEBUT_ABS', 'FIN_ABS', 'ID_EDT_CRENEAU', 'ID_EDT_EMPLACEMENT_COURS', 'ID_GROUPE', 'ID_CLASSE', 'ID_AID', 'ID_S_INCIDENTS', 'ID_LIEU', 'DELETED_BY', 'CREATED_AT', 'UPDATED_AT', 'DELETED_AT', 'VERSION', 'VERSION_CREATED_AT', 'VERSION_CREATED_BY', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'utilisateur_id', 'eleve_id', 'commentaire', 'debut_abs', 'fin_abs', 'id_edt_creneau', 'id_edt_emplacement_cours', 'id_groupe', 'id_classe', 'id_aid', 'id_s_incidents', 'id_lieu', 'deleted_by', 'created_at', 'updated_at', 'deleted_at', 'version', 'version_created_at', 'version_created_by', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	protected static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'UtilisateurId' => 1, 'EleveId' => 2, 'Commentaire' => 3, 'DebutAbs' => 4, 'FinAbs' => 5, 'IdEdtCreneau' => 6, 'IdEdtEmplacementCours' => 7, 'IdGroupe' => 8, 'IdClasse' => 9, 'IdAid' => 10, 'IdSIncidents' => 11, 'IdLieu' => 12, 'DeletedBy' => 13, 'CreatedAt' => 14, 'UpdatedAt' => 15, 'DeletedAt' => 16, 'Version' => 17, 'VersionCreatedAt' => 18, 'VersionCreatedBy' => 19, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'utilisateurId' => 1, 'eleveId' => 2, 'commentaire' => 3, 'debutAbs' => 4, 'finAbs' => 5, 'idEdtCreneau' => 6, 'idEdtEmplacementCours' => 7, 'idGroupe' => 8, 'idClasse' => 9, 'idAid' => 10, 'idSIncidents' => 11, 'idLieu' => 12, 'deletedBy' => 13, 'createdAt' => 14, 'updatedAt' => 15, 'deletedAt' => 16, 'version' => 17, 'versionCreatedAt' => 18, 'versionCreatedBy' => 19, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::UTILISATEUR_ID => 1, self::ELEVE_ID => 2, self::COMMENTAIRE => 3, self::DEBUT_ABS => 4, self::FIN_ABS => 5, self::ID_EDT_CRENEAU => 6, self::ID_EDT_EMPLACEMENT_COURS => 7, self::ID_GROUPE => 8, self::ID_CLASSE => 9, self::ID_AID => 10, self::ID_S_INCIDENTS => 11, self::ID_LIEU => 12, self::DELETED_BY => 13, self::CREATED_AT => 14, self::UPDATED_AT => 15, self::DELETED_AT => 16, self::VERSION => 17, self::VERSION_CREATED_AT => 18, self::VERSION_CREATED_BY => 19, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'UTILISATEUR_ID' => 1, 'ELEVE_ID' => 2, 'COMMENTAIRE' => 3, 'DEBUT_ABS' => 4, 'FIN_ABS' => 5, 'ID_EDT_CRENEAU' => 6, 'ID_EDT_EMPLACEMENT_COURS' => 7, 'ID_GROUPE' => 8, 'ID_CLASSE' => 9, 'ID_AID' => 10, 'ID_S_INCIDENTS' => 11, 'ID_LIEU' => 12, 'DELETED_BY' => 13, 'CREATED_AT' => 14, 'UPDATED_AT' => 15, 'DELETED_AT' => 16, 'VERSION' => 17, 'VERSION_CREATED_AT' => 18, 'VERSION_CREATED_BY' => 19, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'utilisateur_id' => 1, 'eleve_id' => 2, 'commentaire' => 3, 'debut_abs' => 4, 'fin_abs' => 5, 'id_edt_creneau' => 6, 'id_edt_emplacement_cours' => 7, 'id_groupe' => 8, 'id_classe' => 9, 'id_aid' => 10, 'id_s_incidents' => 11, 'id_lieu' => 12, 'deleted_by' => 13, 'created_at' => 14, 'updated_at' => 15, 'deleted_at' => 16, 'version' => 17, 'version_created_at' => 18, 'version_created_by' => 19, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, )
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
	 * @param      string $column The column name for current table. (i.e. AbsenceEleveSaisieVersionPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceEleveSaisieVersionPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::UTILISATEUR_ID);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ELEVE_ID);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::COMMENTAIRE);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::DEBUT_ABS);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::FIN_ABS);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_GROUPE);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_CLASSE);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_AID);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::ID_LIEU);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::DELETED_BY);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::CREATED_AT);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::UPDATED_AT);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::DELETED_AT);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::VERSION);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT);
			$criteria->addSelectColumn(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_BY);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.UTILISATEUR_ID');
			$criteria->addSelectColumn($alias . '.ELEVE_ID');
			$criteria->addSelectColumn($alias . '.COMMENTAIRE');
			$criteria->addSelectColumn($alias . '.DEBUT_ABS');
			$criteria->addSelectColumn($alias . '.FIN_ABS');
			$criteria->addSelectColumn($alias . '.ID_EDT_CRENEAU');
			$criteria->addSelectColumn($alias . '.ID_EDT_EMPLACEMENT_COURS');
			$criteria->addSelectColumn($alias . '.ID_GROUPE');
			$criteria->addSelectColumn($alias . '.ID_CLASSE');
			$criteria->addSelectColumn($alias . '.ID_AID');
			$criteria->addSelectColumn($alias . '.ID_S_INCIDENTS');
			$criteria->addSelectColumn($alias . '.ID_LIEU');
			$criteria->addSelectColumn($alias . '.DELETED_BY');
			$criteria->addSelectColumn($alias . '.CREATED_AT');
			$criteria->addSelectColumn($alias . '.UPDATED_AT');
			$criteria->addSelectColumn($alias . '.DELETED_AT');
			$criteria->addSelectColumn($alias . '.VERSION');
			$criteria->addSelectColumn($alias . '.VERSION_CREATED_AT');
			$criteria->addSelectColumn($alias . '.VERSION_CREATED_BY');
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisieVersionPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceEleveSaisieVersion
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceEleveSaisieVersionPeer::doSelect($critcopy, $con);
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
		return AbsenceEleveSaisieVersionPeer::populateObjects(AbsenceEleveSaisieVersionPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
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
	 * @param      AbsenceEleveSaisieVersion $value A AbsenceEleveSaisieVersion object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool($obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = serialize(array((string) $obj->getId(), (string) $obj->getVersion()));
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
	 * @param      mixed $value A AbsenceEleveSaisieVersion object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceEleveSaisieVersion) {
				$key = serialize(array((string) $value->getId(), (string) $value->getVersion()));
			} elseif (is_array($value) && count($value) === 2) {
				// assume we've been passed a primary key
				$key = serialize(array((string) $value[0], (string) $value[1]));
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceEleveSaisieVersion object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceEleveSaisieVersion Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to a_saisies_version
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
		if ($row[$startcol] === null && $row[$startcol + 17] === null) {
			return null;
		}
		return serialize(array((string) $row[$startcol], (string) $row[$startcol + 17]));
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
		return array((int) $row[$startcol], (int) $row[$startcol + 17]);
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
		$cls = AbsenceEleveSaisieVersionPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceEleveSaisieVersionPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceEleveSaisieVersionPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceEleveSaisieVersionPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AbsenceEleveSaisieVersion object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AbsenceEleveSaisieVersionPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AbsenceEleveSaisieVersionPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AbsenceEleveSaisieVersionPeer::NUM_HYDRATE_COLUMNS;
		} else {
			$cls = AbsenceEleveSaisieVersionPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AbsenceEleveSaisieVersionPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}


	/**
	 * Returns the number of rows matching criteria, joining the related AbsenceEleveSaisie table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAbsenceEleveSaisie(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisieVersionPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisieVersionPeer::ID, AbsenceEleveSaisiePeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveSaisieVersion objects pre-filled with their AbsenceEleveSaisie objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisieVersion objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAbsenceEleveSaisie(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
		$startcol = AbsenceEleveSaisieVersionPeer::NUM_HYDRATE_COLUMNS;
		AbsenceEleveSaisiePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisieVersionPeer::ID, AbsenceEleveSaisiePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisieVersionPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisieVersionPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisieVersionPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisieVersionPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveSaisiePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveSaisiePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AbsenceEleveSaisiePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisieVersion) to $obj2 (AbsenceEleveSaisie)
				$obj2->addAbsenceEleveSaisieVersion($obj1);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisieVersionPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisieVersionPeer::ID, AbsenceEleveSaisiePeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveSaisieVersion objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisieVersion objects.
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

		AbsenceEleveSaisieVersionPeer::addSelectColumns($criteria);
		$startcol2 = AbsenceEleveSaisieVersionPeer::NUM_HYDRATE_COLUMNS;

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + AbsenceEleveSaisiePeer::NUM_HYDRATE_COLUMNS;

		$criteria->addJoin(AbsenceEleveSaisieVersionPeer::ID, AbsenceEleveSaisiePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisieVersionPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisieVersionPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisieVersionPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisieVersionPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined AbsenceEleveSaisie rows

			$key2 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = AbsenceEleveSaisiePeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AbsenceEleveSaisiePeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					AbsenceEleveSaisiePeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (AbsenceEleveSaisieVersion) to the collection in $obj2 (AbsenceEleveSaisie)
				$obj2->addAbsenceEleveSaisieVersion($obj1);
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
	  $dbMap = Propel::getDatabaseMap(BaseAbsenceEleveSaisieVersionPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAbsenceEleveSaisieVersionPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AbsenceEleveSaisieVersionTableMap());
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
		return $withPrefix ? AbsenceEleveSaisieVersionPeer::CLASS_DEFAULT : AbsenceEleveSaisieVersionPeer::OM_CLASS;
	}

	/**
	 * Performs an INSERT on the database, given a AbsenceEleveSaisieVersion or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisieVersion object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceEleveSaisieVersion object
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
	 * Performs an UPDATE on the database, given a AbsenceEleveSaisieVersion or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisieVersion object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceEleveSaisieVersionPeer::ID);
			$value = $criteria->remove(AbsenceEleveSaisieVersionPeer::ID);
			if ($value) {
				$selectCriteria->add(AbsenceEleveSaisieVersionPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceEleveSaisieVersionPeer::TABLE_NAME);
			}

			$comparison = $criteria->getComparison(AbsenceEleveSaisieVersionPeer::VERSION);
			$value = $criteria->remove(AbsenceEleveSaisieVersionPeer::VERSION);
			if ($value) {
				$selectCriteria->add(AbsenceEleveSaisieVersionPeer::VERSION, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceEleveSaisieVersionPeer::TABLE_NAME);
			}

		} else { // $values is AbsenceEleveSaisieVersion object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Deletes all rows from the a_saisies_version table.
	 *
	 * @param      PropelPDO $con the connection to use
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll(PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(AbsenceEleveSaisieVersionPeer::TABLE_NAME, $con, AbsenceEleveSaisieVersionPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AbsenceEleveSaisieVersionPeer::clearInstancePool();
			AbsenceEleveSaisieVersionPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs a DELETE on the database, given a AbsenceEleveSaisieVersion or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisieVersion object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			AbsenceEleveSaisieVersionPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceEleveSaisieVersion) { // it's a model object
			// invalidate the cache for this single object
			AbsenceEleveSaisieVersionPeer::removeInstanceFromPool($values);
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
				$criterion = $criteria->getNewCriterion(AbsenceEleveSaisieVersionPeer::ID, $value[0]);
				$criterion->addAnd($criteria->getNewCriterion(AbsenceEleveSaisieVersionPeer::VERSION, $value[1]));
				$criteria->addOr($criterion);
				// we can invalidate the cache for this single PK
				AbsenceEleveSaisieVersionPeer::removeInstanceFromPool($value);
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
			AbsenceEleveSaisieVersionPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given AbsenceEleveSaisieVersion object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceEleveSaisieVersion $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate($obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceEleveSaisieVersionPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceEleveSaisieVersionPeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, AbsenceEleveSaisieVersionPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve object using using composite pkey values.
	 * @param      int $id
	 * @param      int $version
	 * @param      PropelPDO $con
	 * @return     AbsenceEleveSaisieVersion
	 */
	public static function retrieveByPK($id, $version, PropelPDO $con = null) {
		$_instancePoolKey = serialize(array((string) $id, (string) $version));
 		if (null !== ($obj = AbsenceEleveSaisieVersionPeer::getInstanceFromPool($_instancePoolKey))) {
 			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$criteria = new Criteria(AbsenceEleveSaisieVersionPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveSaisieVersionPeer::ID, $id);
		$criteria->add(AbsenceEleveSaisieVersionPeer::VERSION, $version);
		$v = AbsenceEleveSaisieVersionPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} // BaseAbsenceEleveSaisieVersionPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAbsenceEleveSaisieVersionPeer::buildTableMap();

