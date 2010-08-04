<?php


/**
 * Base static class for performing query and update operations on the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisiePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'gepi';

	/** the table name for this class */
	const TABLE_NAME = 'a_saisies';

	/** the related Propel class for this table */
	const OM_CLASS = 'AbsenceEleveSaisie';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'gepi.AbsenceEleveSaisie';

	/** the related TableMap class for this table */
	const TM_CLASS = 'AbsenceEleveSaisieTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 15;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'a_saisies.ID';

	/** the column name for the UTILISATEUR_ID field */
	const UTILISATEUR_ID = 'a_saisies.UTILISATEUR_ID';

	/** the column name for the ELEVE_ID field */
	const ELEVE_ID = 'a_saisies.ELEVE_ID';

	/** the column name for the COMMENTAIRE field */
	const COMMENTAIRE = 'a_saisies.COMMENTAIRE';

	/** the column name for the DEBUT_ABS field */
	const DEBUT_ABS = 'a_saisies.DEBUT_ABS';

	/** the column name for the FIN_ABS field */
	const FIN_ABS = 'a_saisies.FIN_ABS';

	/** the column name for the ID_EDT_CRENEAU field */
	const ID_EDT_CRENEAU = 'a_saisies.ID_EDT_CRENEAU';

	/** the column name for the ID_EDT_EMPLACEMENT_COURS field */
	const ID_EDT_EMPLACEMENT_COURS = 'a_saisies.ID_EDT_EMPLACEMENT_COURS';

	/** the column name for the ID_GROUPE field */
	const ID_GROUPE = 'a_saisies.ID_GROUPE';

	/** the column name for the ID_CLASSE field */
	const ID_CLASSE = 'a_saisies.ID_CLASSE';

	/** the column name for the ID_AID field */
	const ID_AID = 'a_saisies.ID_AID';

	/** the column name for the ID_S_INCIDENTS field */
	const ID_S_INCIDENTS = 'a_saisies.ID_S_INCIDENTS';

	/** the column name for the MODIFIE_PAR_UTILISATEUR_ID field */
	const MODIFIE_PAR_UTILISATEUR_ID = 'a_saisies.MODIFIE_PAR_UTILISATEUR_ID';

	/** the column name for the CREATED_AT field */
	const CREATED_AT = 'a_saisies.CREATED_AT';

	/** the column name for the UPDATED_AT field */
	const UPDATED_AT = 'a_saisies.UPDATED_AT';

	/**
	 * An identiy map to hold any loaded instances of AbsenceEleveSaisie objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array AbsenceEleveSaisie[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'UtilisateurId', 'EleveId', 'Commentaire', 'DebutAbs', 'FinAbs', 'IdEdtCreneau', 'IdEdtEmplacementCours', 'IdGroupe', 'IdClasse', 'IdAid', 'IdSIncidents', 'ModifieParUtilisateurId', 'CreatedAt', 'UpdatedAt', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'utilisateurId', 'eleveId', 'commentaire', 'debutAbs', 'finAbs', 'idEdtCreneau', 'idEdtEmplacementCours', 'idGroupe', 'idClasse', 'idAid', 'idSIncidents', 'modifieParUtilisateurId', 'createdAt', 'updatedAt', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::UTILISATEUR_ID, self::ELEVE_ID, self::COMMENTAIRE, self::DEBUT_ABS, self::FIN_ABS, self::ID_EDT_CRENEAU, self::ID_EDT_EMPLACEMENT_COURS, self::ID_GROUPE, self::ID_CLASSE, self::ID_AID, self::ID_S_INCIDENTS, self::MODIFIE_PAR_UTILISATEUR_ID, self::CREATED_AT, self::UPDATED_AT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'UTILISATEUR_ID', 'ELEVE_ID', 'COMMENTAIRE', 'DEBUT_ABS', 'FIN_ABS', 'ID_EDT_CRENEAU', 'ID_EDT_EMPLACEMENT_COURS', 'ID_GROUPE', 'ID_CLASSE', 'ID_AID', 'ID_S_INCIDENTS', 'MODIFIE_PAR_UTILISATEUR_ID', 'CREATED_AT', 'UPDATED_AT', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'utilisateur_id', 'eleve_id', 'commentaire', 'debut_abs', 'fin_abs', 'id_edt_creneau', 'id_edt_emplacement_cours', 'id_groupe', 'id_classe', 'id_aid', 'id_s_incidents', 'modifie_par_utilisateur_id', 'created_at', 'updated_at', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'UtilisateurId' => 1, 'EleveId' => 2, 'Commentaire' => 3, 'DebutAbs' => 4, 'FinAbs' => 5, 'IdEdtCreneau' => 6, 'IdEdtEmplacementCours' => 7, 'IdGroupe' => 8, 'IdClasse' => 9, 'IdAid' => 10, 'IdSIncidents' => 11, 'ModifieParUtilisateurId' => 12, 'CreatedAt' => 13, 'UpdatedAt' => 14, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'utilisateurId' => 1, 'eleveId' => 2, 'commentaire' => 3, 'debutAbs' => 4, 'finAbs' => 5, 'idEdtCreneau' => 6, 'idEdtEmplacementCours' => 7, 'idGroupe' => 8, 'idClasse' => 9, 'idAid' => 10, 'idSIncidents' => 11, 'modifieParUtilisateurId' => 12, 'createdAt' => 13, 'updatedAt' => 14, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::UTILISATEUR_ID => 1, self::ELEVE_ID => 2, self::COMMENTAIRE => 3, self::DEBUT_ABS => 4, self::FIN_ABS => 5, self::ID_EDT_CRENEAU => 6, self::ID_EDT_EMPLACEMENT_COURS => 7, self::ID_GROUPE => 8, self::ID_CLASSE => 9, self::ID_AID => 10, self::ID_S_INCIDENTS => 11, self::MODIFIE_PAR_UTILISATEUR_ID => 12, self::CREATED_AT => 13, self::UPDATED_AT => 14, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'UTILISATEUR_ID' => 1, 'ELEVE_ID' => 2, 'COMMENTAIRE' => 3, 'DEBUT_ABS' => 4, 'FIN_ABS' => 5, 'ID_EDT_CRENEAU' => 6, 'ID_EDT_EMPLACEMENT_COURS' => 7, 'ID_GROUPE' => 8, 'ID_CLASSE' => 9, 'ID_AID' => 10, 'ID_S_INCIDENTS' => 11, 'MODIFIE_PAR_UTILISATEUR_ID' => 12, 'CREATED_AT' => 13, 'UPDATED_AT' => 14, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'utilisateur_id' => 1, 'eleve_id' => 2, 'commentaire' => 3, 'debut_abs' => 4, 'fin_abs' => 5, 'id_edt_creneau' => 6, 'id_edt_emplacement_cours' => 7, 'id_groupe' => 8, 'id_classe' => 9, 'id_aid' => 10, 'id_s_incidents' => 11, 'modifie_par_utilisateur_id' => 12, 'created_at' => 13, 'updated_at' => 14, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
	 * @param      string $column The column name for current table. (i.e. AbsenceEleveSaisiePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(AbsenceEleveSaisiePeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::UTILISATEUR_ID);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ELEVE_ID);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::COMMENTAIRE);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::DEBUT_ABS);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::FIN_ABS);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_GROUPE);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_CLASSE);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_AID);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::ID_S_INCIDENTS);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::CREATED_AT);
			$criteria->addSelectColumn(AbsenceEleveSaisiePeer::UPDATED_AT);
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     AbsenceEleveSaisie
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = AbsenceEleveSaisiePeer::doSelect($critcopy, $con);
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
		return AbsenceEleveSaisiePeer::populateObjects(AbsenceEleveSaisiePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
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
	 * @param      AbsenceEleveSaisie $value A AbsenceEleveSaisie object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(AbsenceEleveSaisie $obj, $key = null)
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
	 * @param      mixed $value A AbsenceEleveSaisie object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof AbsenceEleveSaisie) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AbsenceEleveSaisie object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     AbsenceEleveSaisie Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to a_saisies
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in JTraitementSaisieElevePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		JTraitementSaisieElevePeer::clearInstancePool();
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
		$cls = AbsenceEleveSaisiePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = AbsenceEleveSaisiePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				AbsenceEleveSaisiePeer::addInstanceToPool($obj, $key);
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
	 * @return     array (AbsenceEleveSaisie object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = AbsenceEleveSaisiePeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + AbsenceEleveSaisiePeer::NUM_COLUMNS;
		} else {
			$cls = AbsenceEleveSaisiePeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			AbsenceEleveSaisiePeer::addInstanceToPool($obj, $key);
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

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
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related EdtEmplacementCours table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinEdtEmplacementCours(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AidDetails table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAidDetails(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their Eleve objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		ElevePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (Eleve)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their EdtCreneau objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEdtCreneau(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		EdtCreneauPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = EdtCreneauPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = EdtCreneauPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					EdtCreneauPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (EdtCreneau)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their EdtEmplacementCours objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinEdtEmplacementCours(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		EdtEmplacementCoursPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = EdtEmplacementCoursPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					EdtEmplacementCoursPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (EdtEmplacementCours)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their Groupe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		GroupePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (Groupe)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their Classe objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		ClassePeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (Classe)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their AidDetails objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAidDetails(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		AidDetailsPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = AidDetailsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = AidDetailsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					AidDetailsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (AidDetails)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with their UtilisateurProfessionnel objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);
		UtilisateurProfessionnelPeer::addSelectColumns($criteria);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to $obj2 (UtilisateurProfessionnel)
				$obj2->addModifiedAbsenceEleveSaisie($obj1);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol10 = $startcol9 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined Eleve rows

			$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = ElevePeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined EdtCreneau rows

			$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = EdtCreneauPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtCreneau)
				$obj4->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined EdtEmplacementCours rows

			$key5 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = EdtEmplacementCoursPeer::getInstanceFromPool($key5);
				if (!$obj5) {

					$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtEmplacementCoursPeer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (EdtEmplacementCours)
				$obj5->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined Groupe rows

			$key6 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol6);
			if ($key6 !== null) {
				$obj6 = GroupePeer::getInstanceFromPool($key6);
				if (!$obj6) {

					$cls = GroupePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					GroupePeer::addInstanceToPool($obj6, $key6);
				} // if obj6 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Groupe)
				$obj6->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined Classe rows

			$key7 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol7);
			if ($key7 !== null) {
				$obj7 = ClassePeer::getInstanceFromPool($key7);
				if (!$obj7) {

					$cls = ClassePeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					ClassePeer::addInstanceToPool($obj7, $key7);
				} // if obj7 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (Classe)
				$obj7->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined AidDetails rows

			$key8 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol8);
			if ($key8 !== null) {
				$obj8 = AidDetailsPeer::getInstanceFromPool($key8);
				if (!$obj8) {

					$cls = AidDetailsPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					AidDetailsPeer::addInstanceToPool($obj8, $key8);
				} // if obj8 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (AidDetails)
				$obj8->addAbsenceEleveSaisie($obj1);
			} // if joined row not null

			// Add objects for joined UtilisateurProfessionnel rows

			$key9 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol9);
			if ($key9 !== null) {
				$obj9 = UtilisateurProfessionnelPeer::getInstanceFromPool($key9);
				if (!$obj9) {

					$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj9 = new $cls();
					$obj9->hydrate($row, $startcol9);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj9, $key9);
				} // if obj9 loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj9 (UtilisateurProfessionnel)
				$obj9->addModifiedAbsenceEleveSaisie($obj1);
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related Eleve table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEleve(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * @param      Criteria $criteria
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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related EdtEmplacementCours table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptEdtEmplacementCours(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related AidDetails table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptAidDetails(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

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
		$criteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

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
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except UtilisateurProfessionnel.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (Eleve)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key3 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = EdtCreneauPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					EdtCreneauPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (EdtCreneau)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key4 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtEmplacementCoursPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtEmplacementCoursPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtEmplacementCours)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key5 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = GroupePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					GroupePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (Groupe)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except Eleve.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEleve(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key3 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = EdtCreneauPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					EdtCreneauPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (EdtCreneau)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key4 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtEmplacementCoursPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtEmplacementCoursPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtEmplacementCours)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key5 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = GroupePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					GroupePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (Groupe)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except EdtCreneau.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEdtCreneau(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Eleve rows

				$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ElevePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key4 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtEmplacementCoursPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtEmplacementCoursPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtEmplacementCours)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key5 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = GroupePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					GroupePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (Groupe)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except EdtEmplacementCours.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptEdtEmplacementCours(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Eleve rows

				$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ElevePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtCreneau)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key5 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = GroupePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					GroupePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (Groupe)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except Groupe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Eleve rows

				$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ElevePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtCreneau)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key5 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtEmplacementCoursPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtEmplacementCoursPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (EdtEmplacementCours)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except Classe.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Eleve rows

				$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ElevePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtCreneau)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key5 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtEmplacementCoursPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtEmplacementCoursPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (EdtEmplacementCours)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key6 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = GroupePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					GroupePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Groupe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except AidDetails.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptAidDetails(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		UtilisateurProfessionnelPeer::addSelectColumns($criteria);
		$startcol9 = $startcol8 + (UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::MODIFIE_PAR_UTILISATEUR_ID, UtilisateurProfessionnelPeer::LOGIN, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (UtilisateurProfessionnel)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Eleve rows

				$key3 = ElevePeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = ElevePeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = ElevePeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					ElevePeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (Eleve)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key4 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtCreneauPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtCreneauPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtCreneau)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key5 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = EdtEmplacementCoursPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					EdtEmplacementCoursPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (EdtEmplacementCours)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key6 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = GroupePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					GroupePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Groupe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key7 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = ClassePeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					ClassePeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (Classe)
				$obj7->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined UtilisateurProfessionnel rows

				$key8 = UtilisateurProfessionnelPeer::getPrimaryKeyHashFromRow($row, $startcol8);
				if ($key8 !== null) {
					$obj8 = UtilisateurProfessionnelPeer::getInstanceFromPool($key8);
					if (!$obj8) {
	
						$cls = UtilisateurProfessionnelPeer::getOMClass(false);

					$obj8 = new $cls();
					$obj8->hydrate($row, $startcol8);
					UtilisateurProfessionnelPeer::addInstanceToPool($obj8, $key8);
				} // if $obj8 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj8 (UtilisateurProfessionnel)
				$obj8->addModifiedAbsenceEleveSaisie($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of AbsenceEleveSaisie objects pre-filled with all related objects except ModifieParUtilisateur.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of AbsenceEleveSaisie objects.
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

		AbsenceEleveSaisiePeer::addSelectColumns($criteria);
		$startcol2 = (AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS);

		ElevePeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS);

		EdtCreneauPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS);

		EdtEmplacementCoursPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (EdtEmplacementCoursPeer::NUM_COLUMNS - EdtEmplacementCoursPeer::NUM_LAZY_LOAD_COLUMNS);

		GroupePeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS);

		ClassePeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS);

		AidDetailsPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ELEVE_ID, ElevePeer::ID_ELEVE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, EdtCreneauPeer::ID_DEFINIE_PERIODE, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, EdtEmplacementCoursPeer::ID_COURS, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_GROUPE, GroupePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_CLASSE, ClassePeer::ID, $join_behavior);

		$criteria->addJoin(AbsenceEleveSaisiePeer::ID_AID, AidDetailsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = AbsenceEleveSaisiePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = AbsenceEleveSaisiePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = AbsenceEleveSaisiePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				AbsenceEleveSaisiePeer::addInstanceToPool($obj1, $key1);
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
				} // if $obj2 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj2 (Eleve)
				$obj2->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtCreneau rows

				$key3 = EdtCreneauPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = EdtCreneauPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = EdtCreneauPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					EdtCreneauPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj3 (EdtCreneau)
				$obj3->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined EdtEmplacementCours rows

				$key4 = EdtEmplacementCoursPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = EdtEmplacementCoursPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = EdtEmplacementCoursPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					EdtEmplacementCoursPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj4 (EdtEmplacementCours)
				$obj4->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Groupe rows

				$key5 = GroupePeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = GroupePeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = GroupePeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					GroupePeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj5 (Groupe)
				$obj5->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined Classe rows

				$key6 = ClassePeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = ClassePeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = ClassePeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					ClassePeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj6 (Classe)
				$obj6->addAbsenceEleveSaisie($obj1);

			} // if joined row is not null

				// Add objects for joined AidDetails rows

				$key7 = AidDetailsPeer::getPrimaryKeyHashFromRow($row, $startcol7);
				if ($key7 !== null) {
					$obj7 = AidDetailsPeer::getInstanceFromPool($key7);
					if (!$obj7) {
	
						$cls = AidDetailsPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					AidDetailsPeer::addInstanceToPool($obj7, $key7);
				} // if $obj7 already loaded

				// Add the $obj1 (AbsenceEleveSaisie) to the collection in $obj7 (AidDetails)
				$obj7->addAbsenceEleveSaisie($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseAbsenceEleveSaisiePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseAbsenceEleveSaisiePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new AbsenceEleveSaisieTableMap());
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
		return $withPrefix ? AbsenceEleveSaisiePeer::CLASS_DEFAULT : AbsenceEleveSaisiePeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a AbsenceEleveSaisie or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisie object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from AbsenceEleveSaisie object
		}

		if ($criteria->containsKey(AbsenceEleveSaisiePeer::ID) && $criteria->keyContainsValue(AbsenceEleveSaisiePeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveSaisiePeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a AbsenceEleveSaisie or Criteria object.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisie object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(AbsenceEleveSaisiePeer::ID);
			$value = $criteria->remove(AbsenceEleveSaisiePeer::ID);
			if ($value) {
				$selectCriteria->add(AbsenceEleveSaisiePeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(AbsenceEleveSaisiePeer::TABLE_NAME);
			}

		} else { // $values is AbsenceEleveSaisie object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the a_saisies table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += AbsenceEleveSaisiePeer::doOnDeleteCascade(new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME), $con);
			$affectedRows += BasePeer::doDeleteAll(AbsenceEleveSaisiePeer::TABLE_NAME, $con, AbsenceEleveSaisiePeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			AbsenceEleveSaisiePeer::clearInstancePool();
			AbsenceEleveSaisiePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a AbsenceEleveSaisie or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or AbsenceEleveSaisie object or primary key or array of primary keys
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof AbsenceEleveSaisie) { // it's a model object
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(AbsenceEleveSaisiePeer::ID, (array) $values, Criteria::IN);
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
			$affectedRows += AbsenceEleveSaisiePeer::doOnDeleteCascade($c, $con);
			
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			if ($values instanceof Criteria) {
				AbsenceEleveSaisiePeer::clearInstancePool();
			} elseif ($values instanceof AbsenceEleveSaisie) { // it's a model object
				AbsenceEleveSaisiePeer::removeInstanceFromPool($values);
			} else { // it's a primary key, or an array of pks
				foreach ((array) $values as $singleval) {
					AbsenceEleveSaisiePeer::removeInstanceFromPool($singleval);
				}
			}
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			AbsenceEleveSaisiePeer::clearRelatedInstancePool();
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
		$objects = AbsenceEleveSaisiePeer::doSelect($criteria, $con);
		foreach ($objects as $obj) {


			// delete related JTraitementSaisieEleve objects
			$criteria = new Criteria(JTraitementSaisieElevePeer::DATABASE_NAME);
			
			$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $obj->getId());
			$affectedRows += JTraitementSaisieElevePeer::doDelete($criteria, $con);
		}
		return $affectedRows;
	}

	/**
	 * Validates all modified columns of given AbsenceEleveSaisie object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      AbsenceEleveSaisie $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(AbsenceEleveSaisie $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(AbsenceEleveSaisiePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(AbsenceEleveSaisiePeer::TABLE_NAME);

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

		return BasePeer::doValidate(AbsenceEleveSaisiePeer::DATABASE_NAME, AbsenceEleveSaisiePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     AbsenceEleveSaisie
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = AbsenceEleveSaisiePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveSaisiePeer::ID, $pk);

		$v = AbsenceEleveSaisiePeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
			$criteria->add(AbsenceEleveSaisiePeer::ID, $pks, Criteria::IN);
			$objs = AbsenceEleveSaisiePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseAbsenceEleveSaisiePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAbsenceEleveSaisiePeer::buildTableMap();

