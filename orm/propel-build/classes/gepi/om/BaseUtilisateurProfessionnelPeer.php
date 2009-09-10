<?php

/**
 * Base static class for performing query and update operations on the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * @package    gepi.om
 */
abstract class BaseUtilisateurProfessionnelPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'utilisateurs';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.UtilisateurProfessionnel';

	/** The total number of columns. */
	const NUM_COLUMNS = 18;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the LOGIN field */
	const LOGIN = 'utilisateurs.LOGIN';

	/** the column name for the NOM field */
	const NOM = 'utilisateurs.NOM';

	/** the column name for the PRENOM field */
	const PRENOM = 'utilisateurs.PRENOM';

	/** the column name for the CIVILITE field */
	const CIVILITE = 'utilisateurs.CIVILITE';

	/** the column name for the PASSWORD field */
	const PASSWORD = 'utilisateurs.PASSWORD';

	/** the column name for the EMAIL field */
	const EMAIL = 'utilisateurs.EMAIL';

	/** the column name for the SHOW_EMAIL field */
	const SHOW_EMAIL = 'utilisateurs.SHOW_EMAIL';

	/** the column name for the STATUT field */
	const STATUT = 'utilisateurs.STATUT';

	/** the column name for the ETAT field */
	const ETAT = 'utilisateurs.ETAT';

	/** the column name for the CHANGE_MDP field */
	const CHANGE_MDP = 'utilisateurs.CHANGE_MDP';

	/** the column name for the DATE_VERROUILLAGE field */
	const DATE_VERROUILLAGE = 'utilisateurs.DATE_VERROUILLAGE';

	/** the column name for the PASSWORD_TICKET field */
	const PASSWORD_TICKET = 'utilisateurs.PASSWORD_TICKET';

	/** the column name for the TICKET_EXPIRATION field */
	const TICKET_EXPIRATION = 'utilisateurs.TICKET_EXPIRATION';

	/** the column name for the NIVEAU_ALERTE field */
	const NIVEAU_ALERTE = 'utilisateurs.NIVEAU_ALERTE';

	/** the column name for the OBSERVATION_SECURITE field */
	const OBSERVATION_SECURITE = 'utilisateurs.OBSERVATION_SECURITE';

	/** the column name for the TEMP_DIR field */
	const TEMP_DIR = 'utilisateurs.TEMP_DIR';

	/** the column name for the NUMIND field */
	const NUMIND = 'utilisateurs.NUMIND';

	/** the column name for the AUTH_MODE field */
	const AUTH_MODE = 'utilisateurs.AUTH_MODE';

	/**
	 * An identiy map to hold any loaded instances of UtilisateurProfessionnel objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array UtilisateurProfessionnel[]
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
		BasePeer::TYPE_PHPNAME => array ('Login', 'Nom', 'Prenom', 'Civilite', 'Password', 'Email', 'ShowEmail', 'Statut', 'Etat', 'ChangeMdp', 'DateVerrouillage', 'PasswordTicket', 'TicketExpiration', 'NiveauAlerte', 'ObservationSecurite', 'TempDir', 'Numind', 'AuthMode', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('login', 'nom', 'prenom', 'civilite', 'password', 'email', 'showEmail', 'statut', 'etat', 'changeMdp', 'dateVerrouillage', 'passwordTicket', 'ticketExpiration', 'niveauAlerte', 'observationSecurite', 'tempDir', 'numind', 'authMode', ),
		BasePeer::TYPE_COLNAME => array (self::LOGIN, self::NOM, self::PRENOM, self::CIVILITE, self::PASSWORD, self::EMAIL, self::SHOW_EMAIL, self::STATUT, self::ETAT, self::CHANGE_MDP, self::DATE_VERROUILLAGE, self::PASSWORD_TICKET, self::TICKET_EXPIRATION, self::NIVEAU_ALERTE, self::OBSERVATION_SECURITE, self::TEMP_DIR, self::NUMIND, self::AUTH_MODE, ),
		BasePeer::TYPE_FIELDNAME => array ('login', 'nom', 'prenom', 'civilite', 'password', 'email', 'show_email', 'statut', 'etat', 'change_mdp', 'date_verrouillage', 'password_ticket', 'ticket_expiration', 'niveau_alerte', 'observation_securite', 'temp_dir', 'numind', 'auth_mode', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Login' => 0, 'Nom' => 1, 'Prenom' => 2, 'Civilite' => 3, 'Password' => 4, 'Email' => 5, 'ShowEmail' => 6, 'Statut' => 7, 'Etat' => 8, 'ChangeMdp' => 9, 'DateVerrouillage' => 10, 'PasswordTicket' => 11, 'TicketExpiration' => 12, 'NiveauAlerte' => 13, 'ObservationSecurite' => 14, 'TempDir' => 15, 'Numind' => 16, 'AuthMode' => 17, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('login' => 0, 'nom' => 1, 'prenom' => 2, 'civilite' => 3, 'password' => 4, 'email' => 5, 'showEmail' => 6, 'statut' => 7, 'etat' => 8, 'changeMdp' => 9, 'dateVerrouillage' => 10, 'passwordTicket' => 11, 'ticketExpiration' => 12, 'niveauAlerte' => 13, 'observationSecurite' => 14, 'tempDir' => 15, 'numind' => 16, 'authMode' => 17, ),
		BasePeer::TYPE_COLNAME => array (self::LOGIN => 0, self::NOM => 1, self::PRENOM => 2, self::CIVILITE => 3, self::PASSWORD => 4, self::EMAIL => 5, self::SHOW_EMAIL => 6, self::STATUT => 7, self::ETAT => 8, self::CHANGE_MDP => 9, self::DATE_VERROUILLAGE => 10, self::PASSWORD_TICKET => 11, self::TICKET_EXPIRATION => 12, self::NIVEAU_ALERTE => 13, self::OBSERVATION_SECURITE => 14, self::TEMP_DIR => 15, self::NUMIND => 16, self::AUTH_MODE => 17, ),
		BasePeer::TYPE_FIELDNAME => array ('login' => 0, 'nom' => 1, 'prenom' => 2, 'civilite' => 3, 'password' => 4, 'email' => 5, 'show_email' => 6, 'statut' => 7, 'etat' => 8, 'change_mdp' => 9, 'date_verrouillage' => 10, 'password_ticket' => 11, 'ticket_expiration' => 12, 'niveau_alerte' => 13, 'observation_securite' => 14, 'temp_dir' => 15, 'numind' => 16, 'auth_mode' => 17, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, )
	);

	/**
	 * Get a (singleton) instance of the MapBuilder for this peer class.
	 * @return     MapBuilder The map builder for this peer
	 */
	public static function getMapBuilder()
	{
		if (self::$mapBuilder === null) {
			self::$mapBuilder = new UtilisateurProfessionnelMapBuilder();
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
	 * @param      string $column The column name for current table. (i.e. UtilisateurProfessionnelPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(UtilisateurProfessionnelPeer::TABLE_NAME.'.', $alias.'.', $column);
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

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::LOGIN);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::NOM);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::PRENOM);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::CIVILITE);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::PASSWORD);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::EMAIL);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::SHOW_EMAIL);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::STATUT);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::ETAT);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::CHANGE_MDP);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::PASSWORD_TICKET);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::TICKET_EXPIRATION);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::NIVEAU_ALERTE);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::TEMP_DIR);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::NUMIND);

		$criteria->addSelectColumn(UtilisateurProfessionnelPeer::AUTH_MODE);

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
		$criteria->setPrimaryTableName(UtilisateurProfessionnelPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     UtilisateurProfessionnel
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = UtilisateurProfessionnelPeer::doSelect($critcopy, $con);
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
		return UtilisateurProfessionnelPeer::populateObjects(UtilisateurProfessionnelPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			UtilisateurProfessionnelPeer::addSelectColumns($criteria);
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
	 * @param      UtilisateurProfessionnel $value A UtilisateurProfessionnel object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(UtilisateurProfessionnel $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getLogin();
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
	 * @param      mixed $value A UtilisateurProfessionnel object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof UtilisateurProfessionnel) {
				$key = (string) $value->getLogin();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or UtilisateurProfessionnel object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     UtilisateurProfessionnel Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
		$cls = UtilisateurProfessionnelPeer::getOMClass();
		$cls = substr('.'.$cls, strrpos('.'.$cls, '.') + 1);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = UtilisateurProfessionnelPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://propel.phpdb.org/trac/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
		
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				UtilisateurProfessionnelPeer::addInstanceToPool($obj, $key);
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
		return UtilisateurProfessionnelPeer::CLASS_DEFAULT;
	}

	/**
	 * Method perform an INSERT on the database, given a UtilisateurProfessionnel or Criteria object.
	 *
	 * @param      mixed $values Criteria or UtilisateurProfessionnel object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from UtilisateurProfessionnel object
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
	 * Method perform an UPDATE on the database, given a UtilisateurProfessionnel or Criteria object.
	 *
	 * @param      mixed $values Criteria or UtilisateurProfessionnel object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(UtilisateurProfessionnelPeer::LOGIN);
			$selectCriteria->add(UtilisateurProfessionnelPeer::LOGIN, $criteria->remove(UtilisateurProfessionnelPeer::LOGIN), $comparison);

		} else { // $values is UtilisateurProfessionnel object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the utilisateurs table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += UtilisateurProfessionnelPeer::doOnDeleteCascade(new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME), $con);
			UtilisateurProfessionnelPeer::doOnDeleteSetNull(new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(UtilisateurProfessionnelPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a UtilisateurProfessionnel or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or UtilisateurProfessionnel object or primary key or array of primary keys
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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			UtilisateurProfessionnelPeer::clearInstancePool();

			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof UtilisateurProfessionnel) {
			// invalidate the cache for this single object
			UtilisateurProfessionnelPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else {
			// it must be the primary key



			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(UtilisateurProfessionnelPeer::LOGIN, (array) $values, Criteria::IN);

			foreach ((array) $values as $singleval) {
				// we can invalidate the cache for this single object
				UtilisateurProfessionnelPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += UtilisateurProfessionnelPeer::doOnDeleteCascade($criteria, $con);
			UtilisateurProfessionnelPeer::doOnDeleteSetNull($criteria, $con);
			
				// Because this db requires some delete cascade/set null emulation, we have to
				// clear the cached instance *after* the emulation has happened (since
				// instances get re-added by the select statement contained therein).
				if ($values instanceof Criteria) {
					UtilisateurProfessionnelPeer::clearInstancePool();
				} else { // it's a PK or object
					UtilisateurProfessionnelPeer::removeInstanceFromPool($values);
				}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);

			// invalidate objects in JGroupesProfesseursPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JGroupesProfesseursPeer::clearInstancePool();

			// invalidate objects in CahierTexteCompteRenduPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			CahierTexteCompteRenduPeer::clearInstancePool();

			// invalidate objects in CahierTexteTravailAFairePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			CahierTexteTravailAFairePeer::clearInstancePool();

			// invalidate objects in CahierTexteNoticePriveePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			CahierTexteNoticePriveePeer::clearInstancePool();

			// invalidate objects in JEleveCpePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JEleveCpePeer::clearInstancePool();

			// invalidate objects in JEleveProfesseurPrincipalPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JEleveProfesseurPrincipalPeer::clearInstancePool();

			// invalidate objects in JAidUtilisateursProfessionnelsPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			JAidUtilisateursProfessionnelsPeer::clearInstancePool();

			// invalidate objects in AbsenceSaisiePeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			AbsenceSaisiePeer::clearInstancePool();

			// invalidate objects in AbsenceTraitementPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			AbsenceTraitementPeer::clearInstancePool();

			// invalidate objects in AbsenceEnvoiPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			AbsenceEnvoiPeer::clearInstancePool();

			// invalidate objects in PreferenceUtilisateurProfessionnelPeer instance pool, since one or more of them may be deleted by ON DELETE CASCADE rule.
			PreferenceUtilisateurProfessionnelPeer::clearInstancePool();

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
		$objects = UtilisateurProfessionnelPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JGroupesProfesseurs objects
			$c = new Criteria(JGroupesProfesseursPeer::DATABASE_NAME);
			
			$c->add(JGroupesProfesseursPeer::LOGIN, $obj->getLogin());
			$affectedRows += JGroupesProfesseursPeer::doDelete($c, $con);

			// delete related JEleveCpe objects
			$c = new Criteria(JEleveCpePeer::DATABASE_NAME);
			
			$c->add(JEleveCpePeer::CPE_LOGIN, $obj->getLogin());
			$affectedRows += JEleveCpePeer::doDelete($c, $con);

			// delete related JEleveProfesseurPrincipal objects
			$c = new Criteria(JEleveProfesseurPrincipalPeer::DATABASE_NAME);
			
			$c->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $obj->getLogin());
			$affectedRows += JEleveProfesseurPrincipalPeer::doDelete($c, $con);

			// delete related JAidUtilisateursProfessionnels objects
			$c = new Criteria(JAidUtilisateursProfessionnelsPeer::DATABASE_NAME);
			
			$c->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $obj->getLogin());
			$affectedRows += JAidUtilisateursProfessionnelsPeer::doDelete($c, $con);
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
		$objects = UtilisateurProfessionnelPeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {

			// set fkey col in related CahierTexteCompteRendu rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $obj->getLogin());
			$updateValues->add(CahierTexteCompteRenduPeer::ID_LOGIN, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related CahierTexteTravailAFaire rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $obj->getLogin());
			$updateValues->add(CahierTexteTravailAFairePeer::ID_LOGIN, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related CahierTexteNoticePrivee rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $obj->getLogin());
			$updateValues->add(CahierTexteNoticePriveePeer::ID_LOGIN, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related AbsenceSaisie rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceSaisiePeer::UTILISATEUR_ID, $obj->getLogin());
			$updateValues->add(AbsenceSaisiePeer::UTILISATEUR_ID, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related AbsenceTraitement rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceTraitementPeer::UTILISATEUR_ID, $obj->getLogin());
			$updateValues->add(AbsenceTraitementPeer::UTILISATEUR_ID, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related AbsenceEnvoi rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(AbsenceEnvoiPeer::UTILISATEUR_ID, $obj->getLogin());
			$updateValues->add(AbsenceEnvoiPeer::UTILISATEUR_ID, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

			// set fkey col in related PreferenceUtilisateurProfessionnel rows to NULL
			$selectCriteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$updateValues = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$selectCriteria->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, $obj->getLogin());
			$updateValues->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, null);

					BasePeer::doUpdate($selectCriteria, $updateValues, $con); // use BasePeer because generated Peer doUpdate() methods only update using pkey

		}
	}

	/**
	 * Validates all modified columns of given UtilisateurProfessionnel object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      UtilisateurProfessionnel $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(UtilisateurProfessionnel $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(UtilisateurProfessionnelPeer::TABLE_NAME);

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

		return BasePeer::doValidate(UtilisateurProfessionnelPeer::DATABASE_NAME, UtilisateurProfessionnelPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     UtilisateurProfessionnel
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = UtilisateurProfessionnelPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		$criteria->add(UtilisateurProfessionnelPeer::LOGIN, $pk);

		$v = UtilisateurProfessionnelPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
			$criteria->add(UtilisateurProfessionnelPeer::LOGIN, $pks, Criteria::IN);
			$objs = UtilisateurProfessionnelPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseUtilisateurProfessionnelPeer

// This is the static code needed to register the MapBuilder for this table with the main Propel class.
//
// NOTE: This static code cannot call methods on the UtilisateurProfessionnelPeer class, because it is not defined yet.
// If you need to use overridden methods, you can add this code to the bottom of the UtilisateurProfessionnelPeer class:
//
// Propel::getDatabaseMap(UtilisateurProfessionnelPeer::DATABASE_NAME)->addTableBuilder(UtilisateurProfessionnelPeer::TABLE_NAME, UtilisateurProfessionnelPeer::getMapBuilder());
//
// Doing so will effectively overwrite the registration below.

Propel::getDatabaseMap(BaseUtilisateurProfessionnelPeer::DATABASE_NAME)->addTableBuilder(BaseUtilisateurProfessionnelPeer::TABLE_NAME, BaseUtilisateurProfessionnelPeer::getMapBuilder());

