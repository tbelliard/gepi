<?php

/**
 * Base class that represents a row from the 'groupes' table.
 *
 * Groupe d'eleves permettant d'y affecter des matieres et des professeurs
 *
 * @package    gepi.om
 */
abstract class BaseGroupe extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        GroupePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the recalcul_rang field.
	 * @var        string
	 */
	protected $recalcul_rang;

	/**
	 * @var        array CtCompteRendu[] Collection to store aggregation of CtCompteRendu objects.
	 */
	protected $collCtCompteRendus;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtCompteRendus.
	 */
	private $lastCtCompteRenduCriteria = null;

	/**
	 * @var        array CtTravailAFaire[] Collection to store aggregation of CtTravailAFaire objects.
	 */
	protected $collCtTravailAFaires;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtTravailAFaires.
	 */
	private $lastCtTravailAFaireCriteria = null;

	/**
	 * @var        array JGroupesProfesseurs[] Collection to store aggregation of JGroupesProfesseurs objects.
	 */
	protected $collJGroupesProfesseurss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJGroupesProfesseurss.
	 */
	private $lastJGroupesProfesseursCriteria = null;

	/**
	 * @var        array JGroupesClasses[] Collection to store aggregation of JGroupesClasses objects.
	 */
	protected $collJGroupesClassess;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJGroupesClassess.
	 */
	private $lastJGroupesClassesCriteria = null;

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
	 * Initializes internal state of BaseGroupe object.
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
	 * Cle primaire du groupe
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [name] column value.
	 * Nom du groupe
	 * @return     string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the [description] column value.
	 * Description du groupe
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Get the [recalcul_rang] column value.
	 * recalcul_rang
	 * @return     string
	 */
	public function getRecalculRang()
	{
		return $this->recalcul_rang;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire du groupe
	 * @param      int $v new value
	 * @return     Groupe The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = GroupePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [name] column.
	 * Nom du groupe
	 * @param      string $v new value
	 * @return     Groupe The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = GroupePeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [description] column.
	 * Description du groupe
	 * @param      string $v new value
	 * @return     Groupe The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = GroupePeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Set the value of [recalcul_rang] column.
	 * recalcul_rang
	 * @param      string $v new value
	 * @return     Groupe The current object (for fluent API support)
	 */
	public function setRecalculRang($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->recalcul_rang !== $v) {
			$this->recalcul_rang = $v;
			$this->modifiedColumns[] = GroupePeer::RECALCUL_RANG;
		}

		return $this;
	} // setRecalculRang()

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
			$this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->description = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->recalcul_rang = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 4; // 4 = GroupePeer::NUM_COLUMNS - GroupePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Groupe object", $e);
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
			$con = Propel::getConnection(GroupePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = GroupePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collCtCompteRendus = null;
			$this->lastCtCompteRenduCriteria = null;

			$this->collCtTravailAFaires = null;
			$this->lastCtTravailAFaireCriteria = null;

			$this->collJGroupesProfesseurss = null;
			$this->lastJGroupesProfesseursCriteria = null;

			$this->collJGroupesClassess = null;
			$this->lastJGroupesClassesCriteria = null;

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
			$con = Propel::getConnection(GroupePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			GroupePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(GroupePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			GroupePeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = GroupePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = GroupePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += GroupePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCtCompteRendus !== null) {
				foreach ($this->collCtCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCtTravailAFaires !== null) {
				foreach ($this->collCtTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJGroupesProfesseurss !== null) {
				foreach ($this->collJGroupesProfesseurss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJGroupesClassess !== null) {
				foreach ($this->collJGroupesClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
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


			if (($retval = GroupePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCtCompteRendus !== null) {
					foreach ($this->collCtCompteRendus as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCtTravailAFaires !== null) {
					foreach ($this->collCtTravailAFaires as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJGroupesProfesseurss !== null) {
					foreach ($this->collJGroupesProfesseurss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJGroupesClassess !== null) {
					foreach ($this->collJGroupesClassess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = GroupePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getName();
				break;
			case 2:
				return $this->getDescription();
				break;
			case 3:
				return $this->getRecalculRang();
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
		$keys = GroupePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getName(),
			$keys[2] => $this->getDescription(),
			$keys[3] => $this->getRecalculRang(),
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
		$pos = GroupePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setName($value);
				break;
			case 2:
				$this->setDescription($value);
				break;
			case 3:
				$this->setRecalculRang($value);
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
		$keys = GroupePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setRecalculRang($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(GroupePeer::DATABASE_NAME);

		if ($this->isColumnModified(GroupePeer::ID)) $criteria->add(GroupePeer::ID, $this->id);
		if ($this->isColumnModified(GroupePeer::NAME)) $criteria->add(GroupePeer::NAME, $this->name);
		if ($this->isColumnModified(GroupePeer::DESCRIPTION)) $criteria->add(GroupePeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(GroupePeer::RECALCUL_RANG)) $criteria->add(GroupePeer::RECALCUL_RANG, $this->recalcul_rang);

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
		$criteria = new Criteria(GroupePeer::DATABASE_NAME);

		$criteria->add(GroupePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of Groupe (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setName($this->name);

		$copyObj->setDescription($this->description);

		$copyObj->setRecalculRang($this->recalcul_rang);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCtCompteRendus() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCtCompteRendu($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCtTravailAFaires() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCtTravailAFaire($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJGroupesProfesseurss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesProfesseurs($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJGroupesClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesClasses($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


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
	 * @return     Groupe Clone of current object.
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
	 * @return     GroupePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new GroupePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collCtCompteRendus collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtCompteRendus()
	 */
	public function clearCtCompteRendus()
	{
		$this->collCtCompteRendus = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCtCompteRendus collection (array).
	 *
	 * By default this just sets the collCtCompteRendus collection to an empty array (like clearcollCtCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCtCompteRendus()
	{
		$this->collCtCompteRendus = array();
	}

	/**
	 * Gets an array of CtCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CtCompteRendus from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtCompteRendu[]
	 * @throws     PropelException
	 */
	public function getCtCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
			   $this->collCtCompteRendus = array();
			} else {

				$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

				CtCompteRenduPeer::addSelectColumns($criteria);
				$this->collCtCompteRendus = CtCompteRenduPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

				CtCompteRenduPeer::addSelectColumns($criteria);
				if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
					$this->collCtCompteRendus = CtCompteRenduPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;
		return $this->collCtCompteRendus;
	}

	/**
	 * Returns the number of related CtCompteRendu objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CtCompteRendu objects.
	 * @throws     PropelException
	 */
	public function countCtCompteRendus(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

				$count = CtCompteRenduPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
					$count = CtCompteRenduPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCtCompteRendus);
				}
			} else {
				$count = count($this->collCtCompteRendus);
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CtCompteRendu object to this object
	 * through the CtCompteRendu foreign key attribute.
	 *
	 * @param      CtCompteRendu $l CtCompteRendu
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCtCompteRendu(CtCompteRendu $l)
	{
		if ($this->collCtCompteRendus === null) {
			$this->initCtCompteRendus();
		}
		if (!in_array($l, $this->collCtCompteRendus, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCtCompteRendus, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CtCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCtCompteRendusJoinUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCtCompteRendus = array();
			} else {

				$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

				$this->collCtCompteRendus = CtCompteRenduPeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CtCompteRenduPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
				$this->collCtCompteRendus = CtCompteRenduPeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;

		return $this->collCtCompteRendus;
	}

	/**
	 * Clears out the collCtTravailAFaires collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtTravailAFaires()
	 */
	public function clearCtTravailAFaires()
	{
		$this->collCtTravailAFaires = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCtTravailAFaires collection (array).
	 *
	 * By default this just sets the collCtTravailAFaires collection to an empty array (like clearcollCtTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCtTravailAFaires()
	{
		$this->collCtTravailAFaires = array();
	}

	/**
	 * Gets an array of CtTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CtTravailAFaires from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtTravailAFaire[]
	 * @throws     PropelException
	 */
	public function getCtTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
			   $this->collCtTravailAFaires = array();
			} else {

				$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

				CtTravailAFairePeer::addSelectColumns($criteria);
				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

				CtTravailAFairePeer::addSelectColumns($criteria);
				if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
					$this->collCtTravailAFaires = CtTravailAFairePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;
		return $this->collCtTravailAFaires;
	}

	/**
	 * Returns the number of related CtTravailAFaire objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CtTravailAFaire objects.
	 * @throws     PropelException
	 */
	public function countCtTravailAFaires(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

				$count = CtTravailAFairePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
					$count = CtTravailAFairePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCtTravailAFaires);
				}
			} else {
				$count = count($this->collCtTravailAFaires);
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CtTravailAFaire object to this object
	 * through the CtTravailAFaire foreign key attribute.
	 *
	 * @param      CtTravailAFaire $l CtTravailAFaire
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCtTravailAFaire(CtTravailAFaire $l)
	{
		if ($this->collCtTravailAFaires === null) {
			$this->initCtTravailAFaires();
		}
		if (!in_array($l, $this->collCtTravailAFaires, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCtTravailAFaires, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CtTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCtTravailAFairesJoinUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCtTravailAFaires = array();
			} else {

				$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;

		return $this->collCtTravailAFaires;
	}

	/**
	 * Clears out the collJGroupesProfesseurss collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesProfesseurss()
	 */
	public function clearJGroupesProfesseurss()
	{
		$this->collJGroupesProfesseurss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesProfesseurss collection (array).
	 *
	 * By default this just sets the collJGroupesProfesseurss collection to an empty array (like clearcollJGroupesProfesseurss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJGroupesProfesseurss()
	{
		$this->collJGroupesProfesseurss = array();
	}

	/**
	 * Gets an array of JGroupesProfesseurs objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related JGroupesProfesseurss from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JGroupesProfesseurs[]
	 * @throws     PropelException
	 */
	public function getJGroupesProfesseurss($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
			   $this->collJGroupesProfesseurss = array();
			} else {

				$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

				JGroupesProfesseursPeer::addSelectColumns($criteria);
				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

				JGroupesProfesseursPeer::addSelectColumns($criteria);
				if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
					$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;
		return $this->collJGroupesProfesseurss;
	}

	/**
	 * Returns the number of related JGroupesProfesseurs objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesProfesseurs objects.
	 * @throws     PropelException
	 */
	public function countJGroupesProfesseurss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

				$count = JGroupesProfesseursPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

				if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
					$count = JGroupesProfesseursPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJGroupesProfesseurss);
				}
			} else {
				$count = count($this->collJGroupesProfesseurss);
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JGroupesProfesseurs object to this object
	 * through the JGroupesProfesseurs foreign key attribute.
	 *
	 * @param      JGroupesProfesseurs $l JGroupesProfesseurs
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesProfesseurs(JGroupesProfesseurs $l)
	{
		if ($this->collJGroupesProfesseurss === null) {
			$this->initJGroupesProfesseurss();
		}
		if (!in_array($l, $this->collJGroupesProfesseurss, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJGroupesProfesseurss, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related JGroupesProfesseurss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getJGroupesProfesseurssJoinUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
				$this->collJGroupesProfesseurss = array();
			} else {

				$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinUtilisateur($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;

		return $this->collJGroupesProfesseurss;
	}

	/**
	 * Clears out the collJGroupesClassess collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		$this->collJGroupesClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesClassess collection (array).
	 *
	 * By default this just sets the collJGroupesClassess collection to an empty array (like clearcollJGroupesClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJGroupesClassess()
	{
		$this->collJGroupesClassess = array();
	}

	/**
	 * Gets an array of JGroupesClasses objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related JGroupesClassess from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JGroupesClasses[]
	 * @throws     PropelException
	 */
	public function getJGroupesClassess($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
			   $this->collJGroupesClassess = array();
			} else {

				$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

				JGroupesClassesPeer::addSelectColumns($criteria);
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

				JGroupesClassesPeer::addSelectColumns($criteria);
				if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
					$this->collJGroupesClassess = JGroupesClassesPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;
		return $this->collJGroupesClassess;
	}

	/**
	 * Returns the number of related JGroupesClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesClasses objects.
	 * @throws     PropelException
	 */
	public function countJGroupesClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

				$count = JGroupesClassesPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

				if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
					$count = JGroupesClassesPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJGroupesClassess);
				}
			} else {
				$count = count($this->collJGroupesClassess);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JGroupesClasses object to this object
	 * through the JGroupesClasses foreign key attribute.
	 *
	 * @param      JGroupesClasses $l JGroupesClasses
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesClasses(JGroupesClasses $l)
	{
		if ($this->collJGroupesClassess === null) {
			$this->initJGroupesClassess();
		}
		if (!in_array($l, $this->collJGroupesClassess, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJGroupesClassess, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getJGroupesClassessJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
				$this->collJGroupesClassess = array();
			} else {

				$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinClasse($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinClasse($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;

		return $this->collJGroupesClassess;
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
			if ($this->collCtCompteRendus) {
				foreach ((array) $this->collCtCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCtTravailAFaires) {
				foreach ((array) $this->collCtTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJGroupesProfesseurss) {
				foreach ((array) $this->collJGroupesProfesseurss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJGroupesClassess) {
				foreach ((array) $this->collJGroupesClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCtCompteRendus = null;
		$this->collCtTravailAFaires = null;
		$this->collJGroupesProfesseurss = null;
		$this->collJGroupesClassess = null;
	}

} // BaseGroupe
