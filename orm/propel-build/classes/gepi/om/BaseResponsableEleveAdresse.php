<?php

/**
 * Base class that represents a row from the 'resp_adr' table.
 *
 * Table de jointure entre les responsables legaux et leur adresse
 *
 * @package    gepi.om
 */
abstract class BaseResponsableEleveAdresse extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ResponsableEleveAdressePeer
	 */
	protected static $peer;

	/**
	 * The value for the adr_id field.
	 * @var        string
	 */
	protected $adr_id;

	/**
	 * The value for the adr1 field.
	 * @var        string
	 */
	protected $adr1;

	/**
	 * The value for the adr2 field.
	 * @var        string
	 */
	protected $adr2;

	/**
	 * The value for the adr3 field.
	 * @var        string
	 */
	protected $adr3;

	/**
	 * The value for the adr4 field.
	 * @var        string
	 */
	protected $adr4;

	/**
	 * The value for the cp field.
	 * @var        string
	 */
	protected $cp;

	/**
	 * The value for the pays field.
	 * @var        string
	 */
	protected $pays;

	/**
	 * The value for the commune field.
	 * @var        string
	 */
	protected $commune;

	/**
	 * @var        array ResponsableEleve[] Collection to store aggregation of ResponsableEleve objects.
	 */
	protected $collResponsableEleves;

	/**
	 * @var        Criteria The criteria used to select the current contents of collResponsableEleves.
	 */
	private $lastResponsableEleveCriteria = null;

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
	 * Initializes internal state of BaseResponsableEleveAdresse object.
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
	 * Get the [adr_id] column value.
	 * cle primaire, genere par sconet
	 * @return     string
	 */
	public function getAdrId()
	{
		return $this->adr_id;
	}

	/**
	 * Get the [adr1] column value.
	 * 1ere ligne adresse
	 * @return     string
	 */
	public function getAdr1()
	{
		return $this->adr1;
	}

	/**
	 * Get the [adr2] column value.
	 * 2eme ligne adresse
	 * @return     string
	 */
	public function getAdr2()
	{
		return $this->adr2;
	}

	/**
	 * Get the [adr3] column value.
	 * 3eme ligne adresse
	 * @return     string
	 */
	public function getAdr3()
	{
		return $this->adr3;
	}

	/**
	 * Get the [adr4] column value.
	 * 4eme ligne adresse
	 * @return     string
	 */
	public function getAdr4()
	{
		return $this->adr4;
	}

	/**
	 * Get the [cp] column value.
	 * Code postal
	 * @return     string
	 */
	public function getCp()
	{
		return $this->cp;
	}

	/**
	 * Get the [pays] column value.
	 * Pays (quand il est autre que France)
	 * @return     string
	 */
	public function getPays()
	{
		return $this->pays;
	}

	/**
	 * Get the [commune] column value.
	 * Commune de residence
	 * @return     string
	 */
	public function getCommune()
	{
		return $this->commune;
	}

	/**
	 * Set the value of [adr_id] column.
	 * cle primaire, genere par sconet
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdrId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr_id !== $v) {
			$this->adr_id = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR_ID;
		}

		return $this;
	} // setAdrId()

	/**
	 * Set the value of [adr1] column.
	 * 1ere ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr1 !== $v) {
			$this->adr1 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR1;
		}

		return $this;
	} // setAdr1()

	/**
	 * Set the value of [adr2] column.
	 * 2eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr2 !== $v) {
			$this->adr2 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR2;
		}

		return $this;
	} // setAdr2()

	/**
	 * Set the value of [adr3] column.
	 * 3eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr3($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr3 !== $v) {
			$this->adr3 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR3;
		}

		return $this;
	} // setAdr3()

	/**
	 * Set the value of [adr4] column.
	 * 4eme ligne adresse
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setAdr4($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr4 !== $v) {
			$this->adr4 = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::ADR4;
		}

		return $this;
	} // setAdr4()

	/**
	 * Set the value of [cp] column.
	 * Code postal
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setCp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cp !== $v) {
			$this->cp = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::CP;
		}

		return $this;
	} // setCp()

	/**
	 * Set the value of [pays] column.
	 * Pays (quand il est autre que France)
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setPays($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pays !== $v) {
			$this->pays = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::PAYS;
		}

		return $this;
	} // setPays()

	/**
	 * Set the value of [commune] column.
	 * Commune de residence
	 * @param      string $v new value
	 * @return     ResponsableEleveAdresse The current object (for fluent API support)
	 */
	public function setCommune($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commune !== $v) {
			$this->commune = $v;
			$this->modifiedColumns[] = ResponsableEleveAdressePeer::COMMUNE;
		}

		return $this;
	} // setCommune()

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

			$this->adr_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->adr1 = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->adr2 = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->adr3 = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->adr4 = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->cp = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->pays = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->commune = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = ResponsableEleveAdressePeer::NUM_COLUMNS - ResponsableEleveAdressePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating ResponsableEleveAdresse object", $e);
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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ResponsableEleveAdressePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collResponsableEleves = null;
			$this->lastResponsableEleveCriteria = null;

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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			ResponsableEleveAdressePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ResponsableEleveAdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			ResponsableEleveAdressePeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ResponsableEleveAdressePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += ResponsableEleveAdressePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collResponsableEleves !== null) {
				foreach ($this->collResponsableEleves as $referrerFK) {
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


			if (($retval = ResponsableEleveAdressePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collResponsableEleves !== null) {
					foreach ($this->collResponsableEleves as $referrerFK) {
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
		$pos = ResponsableEleveAdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAdrId();
				break;
			case 1:
				return $this->getAdr1();
				break;
			case 2:
				return $this->getAdr2();
				break;
			case 3:
				return $this->getAdr3();
				break;
			case 4:
				return $this->getAdr4();
				break;
			case 5:
				return $this->getCp();
				break;
			case 6:
				return $this->getPays();
				break;
			case 7:
				return $this->getCommune();
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
		$keys = ResponsableEleveAdressePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAdrId(),
			$keys[1] => $this->getAdr1(),
			$keys[2] => $this->getAdr2(),
			$keys[3] => $this->getAdr3(),
			$keys[4] => $this->getAdr4(),
			$keys[5] => $this->getCp(),
			$keys[6] => $this->getPays(),
			$keys[7] => $this->getCommune(),
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
		$pos = ResponsableEleveAdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAdrId($value);
				break;
			case 1:
				$this->setAdr1($value);
				break;
			case 2:
				$this->setAdr2($value);
				break;
			case 3:
				$this->setAdr3($value);
				break;
			case 4:
				$this->setAdr4($value);
				break;
			case 5:
				$this->setCp($value);
				break;
			case 6:
				$this->setPays($value);
				break;
			case 7:
				$this->setCommune($value);
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
		$keys = ResponsableEleveAdressePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAdrId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAdr1($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAdr2($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAdr3($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAdr4($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCp($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPays($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommune($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);

		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR_ID)) $criteria->add(ResponsableEleveAdressePeer::ADR_ID, $this->adr_id);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR1)) $criteria->add(ResponsableEleveAdressePeer::ADR1, $this->adr1);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR2)) $criteria->add(ResponsableEleveAdressePeer::ADR2, $this->adr2);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR3)) $criteria->add(ResponsableEleveAdressePeer::ADR3, $this->adr3);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::ADR4)) $criteria->add(ResponsableEleveAdressePeer::ADR4, $this->adr4);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::CP)) $criteria->add(ResponsableEleveAdressePeer::CP, $this->cp);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::PAYS)) $criteria->add(ResponsableEleveAdressePeer::PAYS, $this->pays);
		if ($this->isColumnModified(ResponsableEleveAdressePeer::COMMUNE)) $criteria->add(ResponsableEleveAdressePeer::COMMUNE, $this->commune);

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
		$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);

		$criteria->add(ResponsableEleveAdressePeer::ADR_ID, $this->adr_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getAdrId();
	}

	/**
	 * Generic method to set the primary key (adr_id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setAdrId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ResponsableEleveAdresse (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAdrId($this->adr_id);

		$copyObj->setAdr1($this->adr1);

		$copyObj->setAdr2($this->adr2);

		$copyObj->setAdr3($this->adr3);

		$copyObj->setAdr4($this->adr4);

		$copyObj->setCp($this->cp);

		$copyObj->setPays($this->pays);

		$copyObj->setCommune($this->commune);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getResponsableEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addResponsableEleve($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

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
	 * @return     ResponsableEleveAdresse Clone of current object.
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
	 * @return     ResponsableEleveAdressePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ResponsableEleveAdressePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collResponsableEleves collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addResponsableEleves()
	 */
	public function clearResponsableEleves()
	{
		$this->collResponsableEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collResponsableEleves collection (array).
	 *
	 * By default this just sets the collResponsableEleves collection to an empty array (like clearcollResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initResponsableEleves()
	{
		$this->collResponsableEleves = array();
	}

	/**
	 * Gets an array of ResponsableEleve objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this ResponsableEleveAdresse has previously been saved, it will retrieve
	 * related ResponsableEleves from storage. If this ResponsableEleveAdresse is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array ResponsableEleve[]
	 * @throws     PropelException
	 */
	public function getResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collResponsableEleves === null) {
			if ($this->isNew()) {
			   $this->collResponsableEleves = array();
			} else {

				$criteria->add(ResponsableElevePeer::ADR_ID, $this->adr_id);

				ResponsableElevePeer::addSelectColumns($criteria);
				$this->collResponsableEleves = ResponsableElevePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(ResponsableElevePeer::ADR_ID, $this->adr_id);

				ResponsableElevePeer::addSelectColumns($criteria);
				if (!isset($this->lastResponsableEleveCriteria) || !$this->lastResponsableEleveCriteria->equals($criteria)) {
					$this->collResponsableEleves = ResponsableElevePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastResponsableEleveCriteria = $criteria;
		return $this->collResponsableEleves;
	}

	/**
	 * Returns the number of related ResponsableEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ResponsableEleve objects.
	 * @throws     PropelException
	 */
	public function countResponsableEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ResponsableEleveAdressePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collResponsableEleves === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(ResponsableElevePeer::ADR_ID, $this->adr_id);

				$count = ResponsableElevePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(ResponsableElevePeer::ADR_ID, $this->adr_id);

				if (!isset($this->lastResponsableEleveCriteria) || !$this->lastResponsableEleveCriteria->equals($criteria)) {
					$count = ResponsableElevePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collResponsableEleves);
				}
			} else {
				$count = count($this->collResponsableEleves);
			}
		}
		$this->lastResponsableEleveCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a ResponsableEleve object to this object
	 * through the ResponsableEleve foreign key attribute.
	 *
	 * @param      ResponsableEleve $l ResponsableEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addResponsableEleve(ResponsableEleve $l)
	{
		if ($this->collResponsableEleves === null) {
			$this->initResponsableEleves();
		}
		if (!in_array($l, $this->collResponsableEleves, true)) { // only add it if the **same** object is not already associated
			array_push($this->collResponsableEleves, $l);
			$l->setResponsableEleveAdresse($this);
		}
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
			if ($this->collResponsableEleves) {
				foreach ((array) $this->collResponsableEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collResponsableEleves = null;
	}

} // BaseResponsableEleveAdresse
