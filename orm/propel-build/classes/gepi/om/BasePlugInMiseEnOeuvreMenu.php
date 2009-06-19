<?php

/**
 * Base class that represents a row from the 'plugins_menus' table.
 *
 * Items pour construire le menu de ce plug-in
 *
 * @package    gepi.om
 */
abstract class BasePlugInMiseEnOeuvreMenu extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PlugInMiseEnOeuvreMenuPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the plugin_id field.
	 * @var        int
	 */
	protected $plugin_id;

	/**
	 * The value for the user_statut field.
	 * @var        string
	 */
	protected $user_statut;

	/**
	 * The value for the titre_item field.
	 * @var        string
	 */
	protected $titre_item;

	/**
	 * The value for the lien_item field.
	 * @var        string
	 */
	protected $lien_item;

	/**
	 * The value for the description_item field.
	 * @var        string
	 */
	protected $description_item;

	/**
	 * @var        PlugIn
	 */
	protected $aPlugIn;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Initializes internal state of BasePlugInMiseEnOeuvreMenu object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
	}

	/**
	 * Get the [id] column value.
	 * cle primaire
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [plugin_id] column value.
	 * cle etrangere vers la table plugins
	 * @return     int
	 */
	public function getPluginId()
	{
		return $this->plugin_id;
	}

	/**
	 * Get the [user_statut] column value.
	 * Statut de l'utilisateur
	 * @return     string
	 */
	public function getUserStatut()
	{
		return $this->user_statut;
	}

	/**
	 * Get the [titre_item] column value.
	 * Titre du lien qui amène vers le bon fichier
	 * @return     string
	 */
	public function getTitreItem()
	{
		return $this->titre_item;
	}

	/**
	 * Get the [lien_item] column value.
	 * url relative
	 * @return     string
	 */
	public function getLienItem()
	{
		return $this->lien_item;
	}

	/**
	 * Get the [description_item] column value.
	 * Description du lien
	 * @return     string
	 */
	public function getDescriptionItem()
	{
		return $this->description_item;
	}

	/**
	 * Set the value of [id] column.
	 * cle primaire
	 * @param      int $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [plugin_id] column.
	 * cle etrangere vers la table plugins
	 * @param      int $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setPluginId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plugin_id !== $v) {
			$this->plugin_id = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID;
		}

		if ($this->aPlugIn !== null && $this->aPlugIn->getId() !== $v) {
			$this->aPlugIn = null;
		}

		return $this;
	} // setPluginId()

	/**
	 * Set the value of [user_statut] column.
	 * Statut de l'utilisateur
	 * @param      string $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setUserStatut($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->user_statut !== $v) {
			$this->user_statut = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::USER_STATUT;
		}

		return $this;
	} // setUserStatut()

	/**
	 * Set the value of [titre_item] column.
	 * Titre du lien qui amène vers le bon fichier
	 * @param      string $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setTitreItem($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->titre_item !== $v) {
			$this->titre_item = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM;
		}

		return $this;
	} // setTitreItem()

	/**
	 * Set the value of [lien_item] column.
	 * url relative
	 * @param      string $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setLienItem($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->lien_item !== $v) {
			$this->lien_item = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM;
		}

		return $this;
	} // setLienItem()

	/**
	 * Set the value of [description_item] column.
	 * Description du lien
	 * @param      string $v new value
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 */
	public function setDescriptionItem($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description_item !== $v) {
			$this->description_item = $v;
			$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM;
		}

		return $this;
	} // setDescriptionItem()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			// First, ensure that we don't have any columns that have been modified which aren't default columns.
			if (array_diff($this->modifiedColumns, array())) {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->plugin_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->user_statut = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->titre_item = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->lien_item = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->description_item = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = PlugInMiseEnOeuvreMenuPeer::NUM_COLUMNS - PlugInMiseEnOeuvreMenuPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PlugInMiseEnOeuvreMenu object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aPlugIn !== null && $this->plugin_id !== $this->aPlugIn->getId()) {
			$this->aPlugIn = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PlugInMiseEnOeuvreMenuPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = PlugInMiseEnOeuvreMenuPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aPlugIn = null;
		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PlugInMiseEnOeuvreMenuPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			PlugInMiseEnOeuvreMenuPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PlugInMiseEnOeuvreMenuPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			PlugInMiseEnOeuvreMenuPeer::addInstanceToPool($this);
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aPlugIn !== null) {
				if ($this->aPlugIn->isModified() || $this->aPlugIn->isNew()) {
					$affectedRows += $this->aPlugIn->save($con);
				}
				$this->setPlugIn($this->aPlugIn);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = PlugInMiseEnOeuvreMenuPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PlugInMiseEnOeuvreMenuPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += PlugInMiseEnOeuvreMenuPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aPlugIn !== null) {
				if (!$this->aPlugIn->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPlugIn->getValidationFailures());
				}
			}


			if (($retval = PlugInMiseEnOeuvreMenuPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PlugInMiseEnOeuvreMenuPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getPluginId();
				break;
			case 2:
				return $this->getUserStatut();
				break;
			case 3:
				return $this->getTitreItem();
				break;
			case 4:
				return $this->getLienItem();
				break;
			case 5:
				return $this->getDescriptionItem();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                        BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
	 * @param      boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns.  Defaults to TRUE.
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = PlugInMiseEnOeuvreMenuPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getPluginId(),
			$keys[2] => $this->getUserStatut(),
			$keys[3] => $this->getTitreItem(),
			$keys[4] => $this->getLienItem(),
			$keys[5] => $this->getDescriptionItem(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PlugInMiseEnOeuvreMenuPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setPluginId($value);
				break;
			case 2:
				$this->setUserStatut($value);
				break;
			case 3:
				$this->setTitreItem($value);
				break;
			case 4:
				$this->setLienItem($value);
				break;
			case 5:
				$this->setDescriptionItem($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PlugInMiseEnOeuvreMenuPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPluginId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setUserStatut($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTitreItem($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setLienItem($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDescriptionItem($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PlugInMiseEnOeuvreMenuPeer::DATABASE_NAME);

		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::ID)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::ID, $this->id);
		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $this->plugin_id);
		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::USER_STATUT)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::USER_STATUT, $this->user_statut);
		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::TITRE_ITEM, $this->titre_item);
		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::LIEN_ITEM, $this->lien_item);
		if ($this->isColumnModified(PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM)) $criteria->add(PlugInMiseEnOeuvreMenuPeer::DESCRIPTION_ITEM, $this->description_item);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(PlugInMiseEnOeuvreMenuPeer::DATABASE_NAME);

		$criteria->add(PlugInMiseEnOeuvreMenuPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of PlugInMiseEnOeuvreMenu (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPluginId($this->plugin_id);

		$copyObj->setUserStatut($this->user_statut);

		$copyObj->setTitreItem($this->titre_item);

		$copyObj->setLienItem($this->lien_item);

		$copyObj->setDescriptionItem($this->description_item);


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     PlugInMiseEnOeuvreMenu Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     PlugInMiseEnOeuvreMenuPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PlugInMiseEnOeuvreMenuPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a PlugIn object.
	 *
	 * @param      PlugIn $v
	 * @return     PlugInMiseEnOeuvreMenu The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setPlugIn(PlugIn $v = null)
	{
		if ($v === null) {
			$this->setPluginId(NULL);
		} else {
			$this->setPluginId($v->getId());
		}

		$this->aPlugIn = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the PlugIn object, it will not be re-added.
		if ($v !== null) {
			$v->addPlugInMiseEnOeuvreMenu($this);
		}

		return $this;
	}


	/**
	 * Get the associated PlugIn object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     PlugIn The associated PlugIn object.
	 * @throws     PropelException
	 */
	public function getPlugIn(PropelPDO $con = null)
	{
		if ($this->aPlugIn === null && ($this->plugin_id !== null)) {
			$this->aPlugIn = PlugInPeer::retrieveByPK($this->plugin_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aPlugIn->addPlugInMiseEnOeuvreMenus($this);
			 */
		}
		return $this->aPlugIn;
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

			$this->aPlugIn = null;
	}

} // BasePlugInMiseEnOeuvreMenu
