<?php

/**
 * Base class that represents a row from the 'groupes' table.
 *
 * Groupe d'eleves permettant d'y affecter une matiere et un professeurs
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
	 * @var        array CahierTexteCompteRendu[] Collection to store aggregation of CahierTexteCompteRendu objects.
	 */
	protected $collCahierTexteCompteRendus;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteCompteRendus.
	 */
	private $lastCahierTexteCompteRenduCriteria = null;

	/**
	 * @var        array CahierTexteTravailAFaire[] Collection to store aggregation of CahierTexteTravailAFaire objects.
	 */
	protected $collCahierTexteTravailAFaires;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteTravailAFaires.
	 */
	private $lastCahierTexteTravailAFaireCriteria = null;

	/**
	 * @var        array CahierTexteNoticePrivee[] Collection to store aggregation of CahierTexteNoticePrivee objects.
	 */
	protected $collCahierTexteNoticePrivees;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteNoticePrivees.
	 */
	private $lastCahierTexteNoticePriveeCriteria = null;

	/**
	 * @var        array JEleveGroupe[] Collection to store aggregation of JEleveGroupe objects.
	 */
	protected $collJEleveGroupes;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJEleveGroupes.
	 */
	private $lastJEleveGroupeCriteria = null;

	/**
	 * @var        array CreditEcts[] Collection to store aggregation of CreditEcts objects.
	 */
	protected $collCreditEctss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCreditEctss.
	 */
	private $lastCreditEctsCriteria = null;

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
	 * Clee primaire du groupe
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
	 * Clee primaire du groupe
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

			$this->collJGroupesProfesseurss = null;
			$this->lastJGroupesProfesseursCriteria = null;

			$this->collJGroupesClassess = null;
			$this->lastJGroupesClassesCriteria = null;

			$this->collCahierTexteCompteRendus = null;
			$this->lastCahierTexteCompteRenduCriteria = null;

			$this->collCahierTexteTravailAFaires = null;
			$this->lastCahierTexteTravailAFaireCriteria = null;

			$this->collCahierTexteNoticePrivees = null;
			$this->lastCahierTexteNoticePriveeCriteria = null;

			$this->collJEleveGroupes = null;
			$this->lastJEleveGroupeCriteria = null;

			$this->collCreditEctss = null;
			$this->lastCreditEctsCriteria = null;

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

			if ($this->collCahierTexteCompteRendus !== null) {
				foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCahierTexteTravailAFaires !== null) {
				foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCahierTexteNoticePrivees !== null) {
				foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveGroupes !== null) {
				foreach ($this->collJEleveGroupes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCreditEctss !== null) {
				foreach ($this->collCreditEctss as $referrerFK) {
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

				if ($this->collCahierTexteCompteRendus !== null) {
					foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCahierTexteTravailAFaires !== null) {
					foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCahierTexteNoticePrivees !== null) {
					foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveGroupes !== null) {
					foreach ($this->collJEleveGroupes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCreditEctss !== null) {
					foreach ($this->collCreditEctss as $referrerFK) {
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

			foreach ($this->getCahierTexteCompteRendus() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteCompteRendu($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCahierTexteTravailAFaires() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteTravailAFaire($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCahierTexteNoticePrivees() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteNoticePrivee($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveGroupes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveGroupe($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCreditEctss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCreditEcts($relObj->copy($deepCopy));
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
	public function getJGroupesProfesseurssJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
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

				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesProfesseursPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
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
	public function getJGroupesClassessJoinCategorieMatiere($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
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

				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;

		return $this->collJGroupesClassess;
	}

	/**
	 * Clears out the collCahierTexteCompteRendus collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteCompteRendus()
	 */
	public function clearCahierTexteCompteRendus()
	{
		$this->collCahierTexteCompteRendus = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteCompteRendus collection (array).
	 *
	 * By default this just sets the collCahierTexteCompteRendus collection to an empty array (like clearcollCahierTexteCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteCompteRendus()
	{
		$this->collCahierTexteCompteRendus = array();
	}

	/**
	 * Gets an array of CahierTexteCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CahierTexteCompteRendus from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteCompteRendu[]
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				CahierTexteCompteRenduPeer::addSelectColumns($criteria);
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				CahierTexteCompteRenduPeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
					$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;
		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Returns the number of related CahierTexteCompteRendu objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteCompteRendu objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteCompteRendus(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
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

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				$count = CahierTexteCompteRenduPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
					$count = CahierTexteCompteRenduPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteCompteRendus);
				}
			} else {
				$count = count($this->collCahierTexteCompteRendus);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteCompteRendu object to this object
	 * through the CahierTexteCompteRendu foreign key attribute.
	 *
	 * @param      CahierTexteCompteRendu $l CahierTexteCompteRendu
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteCompteRendu(CahierTexteCompteRendu $l)
	{
		if ($this->collCahierTexteCompteRendus === null) {
			$this->initCahierTexteCompteRendus();
		}
		if (!in_array($l, $this->collCahierTexteCompteRendus, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteCompteRendus, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteCompteRendusJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;

		return $this->collCahierTexteCompteRendus;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteCompteRendusJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteCompteRenduPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;

		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Clears out the collCahierTexteTravailAFaires collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteTravailAFaires()
	 */
	public function clearCahierTexteTravailAFaires()
	{
		$this->collCahierTexteTravailAFaires = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteTravailAFaires collection (array).
	 *
	 * By default this just sets the collCahierTexteTravailAFaires collection to an empty array (like clearcollCahierTexteTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteTravailAFaires()
	{
		$this->collCahierTexteTravailAFaires = array();
	}

	/**
	 * Gets an array of CahierTexteTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CahierTexteTravailAFaires from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteTravailAFaire[]
	 * @throws     PropelException
	 */
	public function getCahierTexteTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				CahierTexteTravailAFairePeer::addSelectColumns($criteria);
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				CahierTexteTravailAFairePeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
					$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;
		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Returns the number of related CahierTexteTravailAFaire objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteTravailAFaire objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteTravailAFaires(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
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

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				$count = CahierTexteTravailAFairePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
					$count = CahierTexteTravailAFairePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteTravailAFaires);
				}
			} else {
				$count = count($this->collCahierTexteTravailAFaires);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteTravailAFaire object to this object
	 * through the CahierTexteTravailAFaire foreign key attribute.
	 *
	 * @param      CahierTexteTravailAFaire $l CahierTexteTravailAFaire
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteTravailAFaire(CahierTexteTravailAFaire $l)
	{
		if ($this->collCahierTexteTravailAFaires === null) {
			$this->initCahierTexteTravailAFaires();
		}
		if (!in_array($l, $this->collCahierTexteTravailAFaires, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteTravailAFaires, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteTravailAFairesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;

		return $this->collCahierTexteTravailAFaires;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteTravailAFairesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteTravailAFairePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;

		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Clears out the collCahierTexteNoticePrivees collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteNoticePrivees()
	 */
	public function clearCahierTexteNoticePrivees()
	{
		$this->collCahierTexteNoticePrivees = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteNoticePrivees collection (array).
	 *
	 * By default this just sets the collCahierTexteNoticePrivees collection to an empty array (like clearcollCahierTexteNoticePrivees());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteNoticePrivees()
	{
		$this->collCahierTexteNoticePrivees = array();
	}

	/**
	 * Gets an array of CahierTexteNoticePrivee objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CahierTexteNoticePrivees from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteNoticePrivee[]
	 * @throws     PropelException
	 */
	public function getCahierTexteNoticePrivees($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				CahierTexteNoticePriveePeer::addSelectColumns($criteria);
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				CahierTexteNoticePriveePeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
					$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;
		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Returns the number of related CahierTexteNoticePrivee objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteNoticePrivee objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteNoticePrivees(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
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

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				$count = CahierTexteNoticePriveePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
					$count = CahierTexteNoticePriveePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteNoticePrivees);
				}
			} else {
				$count = count($this->collCahierTexteNoticePrivees);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteNoticePrivee object to this object
	 * through the CahierTexteNoticePrivee foreign key attribute.
	 *
	 * @param      CahierTexteNoticePrivee $l CahierTexteNoticePrivee
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteNoticePrivee(CahierTexteNoticePrivee $l)
	{
		if ($this->collCahierTexteNoticePrivees === null) {
			$this->initCahierTexteNoticePrivees();
		}
		if (!in_array($l, $this->collCahierTexteNoticePrivees, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteNoticePrivees, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteNoticePriveesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;

		return $this->collCahierTexteNoticePrivees;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCahierTexteNoticePriveesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteNoticePriveePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;

		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Clears out the collJEleveGroupes collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveGroupes()
	 */
	public function clearJEleveGroupes()
	{
		$this->collJEleveGroupes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveGroupes collection (array).
	 *
	 * By default this just sets the collJEleveGroupes collection to an empty array (like clearcollJEleveGroupes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveGroupes()
	{
		$this->collJEleveGroupes = array();
	}

	/**
	 * Gets an array of JEleveGroupe objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related JEleveGroupes from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JEleveGroupe[]
	 * @throws     PropelException
	 */
	public function getJEleveGroupes($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveGroupes === null) {
			if ($this->isNew()) {
			   $this->collJEleveGroupes = array();
			} else {

				$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

				JEleveGroupePeer::addSelectColumns($criteria);
				$this->collJEleveGroupes = JEleveGroupePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

				JEleveGroupePeer::addSelectColumns($criteria);
				if (!isset($this->lastJEleveGroupeCriteria) || !$this->lastJEleveGroupeCriteria->equals($criteria)) {
					$this->collJEleveGroupes = JEleveGroupePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJEleveGroupeCriteria = $criteria;
		return $this->collJEleveGroupes;
	}

	/**
	 * Returns the number of related JEleveGroupe objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveGroupe objects.
	 * @throws     PropelException
	 */
	public function countJEleveGroupes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
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

		if ($this->collJEleveGroupes === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

				$count = JEleveGroupePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

				if (!isset($this->lastJEleveGroupeCriteria) || !$this->lastJEleveGroupeCriteria->equals($criteria)) {
					$count = JEleveGroupePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJEleveGroupes);
				}
			} else {
				$count = count($this->collJEleveGroupes);
			}
		}
		$this->lastJEleveGroupeCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JEleveGroupe object to this object
	 * through the JEleveGroupe foreign key attribute.
	 *
	 * @param      JEleveGroupe $l JEleveGroupe
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveGroupe(JEleveGroupe $l)
	{
		if ($this->collJEleveGroupes === null) {
			$this->initJEleveGroupes();
		}
		if (!in_array($l, $this->collJEleveGroupes, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJEleveGroupes, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related JEleveGroupes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getJEleveGroupesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveGroupes === null) {
			if ($this->isNew()) {
				$this->collJEleveGroupes = array();
			} else {

				$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

				$this->collJEleveGroupes = JEleveGroupePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveGroupePeer::ID_GROUPE, $this->id);

			if (!isset($this->lastJEleveGroupeCriteria) || !$this->lastJEleveGroupeCriteria->equals($criteria)) {
				$this->collJEleveGroupes = JEleveGroupePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveGroupeCriteria = $criteria;

		return $this->collJEleveGroupes;
	}

	/**
	 * Clears out the collCreditEctss collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCreditEctss()
	 */
	public function clearCreditEctss()
	{
		$this->collCreditEctss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCreditEctss collection (array).
	 *
	 * By default this just sets the collCreditEctss collection to an empty array (like clearcollCreditEctss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCreditEctss()
	{
		$this->collCreditEctss = array();
	}

	/**
	 * Gets an array of CreditEcts objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Groupe has previously been saved, it will retrieve
	 * related CreditEctss from storage. If this Groupe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CreditEcts[]
	 * @throws     PropelException
	 */
	public function getCreditEctss($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCreditEctss === null) {
			if ($this->isNew()) {
			   $this->collCreditEctss = array();
			} else {

				$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

				CreditEctsPeer::addSelectColumns($criteria);
				$this->collCreditEctss = CreditEctsPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

				CreditEctsPeer::addSelectColumns($criteria);
				if (!isset($this->lastCreditEctsCriteria) || !$this->lastCreditEctsCriteria->equals($criteria)) {
					$this->collCreditEctss = CreditEctsPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCreditEctsCriteria = $criteria;
		return $this->collCreditEctss;
	}

	/**
	 * Returns the number of related CreditEcts objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CreditEcts objects.
	 * @throws     PropelException
	 */
	public function countCreditEctss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
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

		if ($this->collCreditEctss === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

				$count = CreditEctsPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

				if (!isset($this->lastCreditEctsCriteria) || !$this->lastCreditEctsCriteria->equals($criteria)) {
					$count = CreditEctsPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCreditEctss);
				}
			} else {
				$count = count($this->collCreditEctss);
			}
		}
		$this->lastCreditEctsCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CreditEcts object to this object
	 * through the CreditEcts foreign key attribute.
	 *
	 * @param      CreditEcts $l CreditEcts
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCreditEcts(CreditEcts $l)
	{
		if ($this->collCreditEctss === null) {
			$this->initCreditEctss();
		}
		if (!in_array($l, $this->collCreditEctss, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCreditEctss, $l);
			$l->setGroupe($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Groupe is new, it will return
	 * an empty collection; or if this Groupe has previously
	 * been saved, it will retrieve related CreditEctss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Groupe.
	 */
	public function getCreditEctssJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(GroupePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCreditEctss === null) {
			if ($this->isNew()) {
				$this->collCreditEctss = array();
			} else {

				$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

				$this->collCreditEctss = CreditEctsPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id);

			if (!isset($this->lastCreditEctsCriteria) || !$this->lastCreditEctsCriteria->equals($criteria)) {
				$this->collCreditEctss = CreditEctsPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastCreditEctsCriteria = $criteria;

		return $this->collCreditEctss;
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
			if ($this->collCahierTexteCompteRendus) {
				foreach ((array) $this->collCahierTexteCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteTravailAFaires) {
				foreach ((array) $this->collCahierTexteTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteNoticePrivees) {
				foreach ((array) $this->collCahierTexteNoticePrivees as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveGroupes) {
				foreach ((array) $this->collJEleveGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCreditEctss) {
				foreach ((array) $this->collCreditEctss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJGroupesProfesseurss = null;
		$this->collJGroupesClassess = null;
		$this->collCahierTexteCompteRendus = null;
		$this->collCahierTexteTravailAFaires = null;
		$this->collCahierTexteNoticePrivees = null;
		$this->collJEleveGroupes = null;
		$this->collCreditEctss = null;
	}

} // BaseGroupe
