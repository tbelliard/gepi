<?php


/**
 * Base class that represents a row from the 'responsables2' table.
 *
 * Table de jointure entre les eleves et leurs responsables legaux avec mention du niveau de ces responsables
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseResponsableInformation extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'ResponsableInformationPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ResponsableInformationPeer
	 */
	protected static $peer;

	/**
	 * The value for the ele_id field.
	 * @var        string
	 */
	protected $ele_id;

	/**
	 * The value for the pers_id field.
	 * @var        string
	 */
	protected $pers_id;

	/**
	 * The value for the resp_legal field.
	 * @var        string
	 */
	protected $resp_legal;

	/**
	 * The value for the pers_contact field.
	 * @var        string
	 */
	protected $pers_contact;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        ResponsableEleve
	 */
	protected $aResponsableEleve;

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
	 * Get the [ele_id] column value.
	 * cle etrangere, ele_id de l'eleve
	 * @return     string
	 */
	public function getEleId()
	{
		return $this->ele_id;
	}

	/**
	 * Get the [pers_id] column value.
	 * cle etrangere vers le responsable
	 * @return     string
	 */
	public function getPersId()
	{
		return $this->pers_id;
	}

	/**
	 * Get the [resp_legal] column value.
	 * Niveau de responsabilite du responsable legal
	 * @return     string
	 */
	public function getRespLegal()
	{
		return $this->resp_legal;
	}

	/**
	 * Get the [pers_contact] column value.
	 * 
	 * @return     string
	 */
	public function getPersContact()
	{
		return $this->pers_contact;
	}

	/**
	 * Set the value of [ele_id] column.
	 * cle etrangere, ele_id de l'eleve
	 * @param      string $v new value
	 * @return     ResponsableInformation The current object (for fluent API support)
	 */
	public function setEleId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ele_id !== $v) {
			$this->ele_id = $v;
			$this->modifiedColumns[] = ResponsableInformationPeer::ELE_ID;
		}

		if ($this->aEleve !== null && $this->aEleve->getEleId() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setEleId()

	/**
	 * Set the value of [pers_id] column.
	 * cle etrangere vers le responsable
	 * @param      string $v new value
	 * @return     ResponsableInformation The current object (for fluent API support)
	 */
	public function setPersId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pers_id !== $v) {
			$this->pers_id = $v;
			$this->modifiedColumns[] = ResponsableInformationPeer::PERS_ID;
		}

		if ($this->aResponsableEleve !== null && $this->aResponsableEleve->getPersId() !== $v) {
			$this->aResponsableEleve = null;
		}

		return $this;
	} // setPersId()

	/**
	 * Set the value of [resp_legal] column.
	 * Niveau de responsabilite du responsable legal
	 * @param      string $v new value
	 * @return     ResponsableInformation The current object (for fluent API support)
	 */
	public function setRespLegal($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->resp_legal !== $v) {
			$this->resp_legal = $v;
			$this->modifiedColumns[] = ResponsableInformationPeer::RESP_LEGAL;
		}

		return $this;
	} // setRespLegal()

	/**
	 * Set the value of [pers_contact] column.
	 * 
	 * @param      string $v new value
	 * @return     ResponsableInformation The current object (for fluent API support)
	 */
	public function setPersContact($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pers_contact !== $v) {
			$this->pers_contact = $v;
			$this->modifiedColumns[] = ResponsableInformationPeer::PERS_CONTACT;
		}

		return $this;
	} // setPersContact()

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

			$this->ele_id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->pers_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->resp_legal = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->pers_contact = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 4; // 4 = ResponsableInformationPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating ResponsableInformation object", $e);
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

		if ($this->aEleve !== null && $this->ele_id !== $this->aEleve->getEleId()) {
			$this->aEleve = null;
		}
		if ($this->aResponsableEleve !== null && $this->pers_id !== $this->aResponsableEleve->getPersId()) {
			$this->aResponsableEleve = null;
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
			$con = Propel::getConnection(ResponsableInformationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ResponsableInformationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aEleve = null;
			$this->aResponsableEleve = null;
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
			$con = Propel::getConnection(ResponsableInformationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				ResponsableInformationQuery::create()
					->filterByPrimaryKey($this->getPrimaryKey())
					->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
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
			$con = Propel::getConnection(ResponsableInformationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				ResponsableInformationPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
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

			if ($this->aEleve !== null) {
				if ($this->aEleve->isModified() || $this->aEleve->isNew()) {
					$affectedRows += $this->aEleve->save($con);
				}
				$this->setEleve($this->aEleve);
			}

			if ($this->aResponsableEleve !== null) {
				if ($this->aResponsableEleve->isModified() || $this->aResponsableEleve->isNew()) {
					$affectedRows += $this->aResponsableEleve->save($con);
				}
				$this->setResponsableEleve($this->aResponsableEleve);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setNew(false);
				} else {
					$affectedRows += ResponsableInformationPeer::doUpdate($this, $con);
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

			if ($this->aEleve !== null) {
				if (!$this->aEleve->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEleve->getValidationFailures());
				}
			}

			if ($this->aResponsableEleve !== null) {
				if (!$this->aResponsableEleve->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aResponsableEleve->getValidationFailures());
				}
			}


			if (($retval = ResponsableInformationPeer::doValidate($this, $columns)) !== true) {
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
		$pos = ResponsableInformationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEleId();
				break;
			case 1:
				return $this->getPersId();
				break;
			case 2:
				return $this->getRespLegal();
				break;
			case 3:
				return $this->getPersContact();
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
	 * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 *                    Defaults to BasePeer::TYPE_PHPNAME.
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['ResponsableInformation'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['ResponsableInformation'][serialize($this->getPrimaryKey())] = true;
		$keys = ResponsableInformationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getEleId(),
			$keys[1] => $this->getPersId(),
			$keys[2] => $this->getRespLegal(),
			$keys[3] => $this->getPersContact(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aEleve) {
				$result['Eleve'] = $this->aEleve->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aResponsableEleve) {
				$result['ResponsableEleve'] = $this->aResponsableEleve->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
		}
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
		$pos = ResponsableInformationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEleId($value);
				break;
			case 1:
				$this->setPersId($value);
				break;
			case 2:
				$this->setRespLegal($value);
				break;
			case 3:
				$this->setPersContact($value);
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
		$keys = ResponsableInformationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setEleId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setPersId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setRespLegal($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPersContact($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ResponsableInformationPeer::DATABASE_NAME);

		if ($this->isColumnModified(ResponsableInformationPeer::ELE_ID)) $criteria->add(ResponsableInformationPeer::ELE_ID, $this->ele_id);
		if ($this->isColumnModified(ResponsableInformationPeer::PERS_ID)) $criteria->add(ResponsableInformationPeer::PERS_ID, $this->pers_id);
		if ($this->isColumnModified(ResponsableInformationPeer::RESP_LEGAL)) $criteria->add(ResponsableInformationPeer::RESP_LEGAL, $this->resp_legal);
		if ($this->isColumnModified(ResponsableInformationPeer::PERS_CONTACT)) $criteria->add(ResponsableInformationPeer::PERS_CONTACT, $this->pers_contact);

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
		$criteria = new Criteria(ResponsableInformationPeer::DATABASE_NAME);
		$criteria->add(ResponsableInformationPeer::ELE_ID, $this->ele_id);
		$criteria->add(ResponsableInformationPeer::RESP_LEGAL, $this->resp_legal);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();
		$pks[0] = $this->getEleId();
		$pks[1] = $this->getRespLegal();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{
		$this->setEleId($keys[0]);
		$this->setRespLegal($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getEleId()) && (null === $this->getRespLegal());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of ResponsableInformation (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setEleId($this->getEleId());
		$copyObj->setPersId($this->getPersId());
		$copyObj->setRespLegal($this->getRespLegal());
		$copyObj->setPersContact($this->getPersContact());
		if ($makeNew) {
			$copyObj->setNew(true);
		}
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
	 * @return     ResponsableInformation Clone of current object.
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
	 * @return     ResponsableInformationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ResponsableInformationPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     ResponsableInformation The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEleve(Eleve $v = null)
	{
		if ($v === null) {
			$this->setEleId(NULL);
		} else {
			$this->setEleId($v->getEleId());
		}

		$this->aEleve = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Eleve object, it will not be re-added.
		if ($v !== null) {
			$v->addResponsableInformation($this);
		}

		return $this;
	}


	/**
	 * Get the associated Eleve object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Eleve The associated Eleve object.
	 * @throws     PropelException
	 */
	public function getEleve(PropelPDO $con = null)
	{
		if ($this->aEleve === null && (($this->ele_id !== "" && $this->ele_id !== null))) {
			$this->aEleve = EleveQuery::create()
				->filterByResponsableInformation($this) // here
				->findOne($con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aEleve->addResponsableInformations($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Declares an association between this object and a ResponsableEleve object.
	 *
	 * @param      ResponsableEleve $v
	 * @return     ResponsableInformation The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setResponsableEleve(ResponsableEleve $v = null)
	{
		if ($v === null) {
			$this->setPersId(NULL);
		} else {
			$this->setPersId($v->getPersId());
		}

		$this->aResponsableEleve = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the ResponsableEleve object, it will not be re-added.
		if ($v !== null) {
			$v->addResponsableInformation($this);
		}

		return $this;
	}


	/**
	 * Get the associated ResponsableEleve object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     ResponsableEleve The associated ResponsableEleve object.
	 * @throws     PropelException
	 */
	public function getResponsableEleve(PropelPDO $con = null)
	{
		if ($this->aResponsableEleve === null && (($this->pers_id !== "" && $this->pers_id !== null))) {
			$this->aResponsableEleve = ResponsableEleveQuery::create()->findPk($this->pers_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aResponsableEleve->addResponsableInformations($this);
			 */
		}
		return $this->aResponsableEleve;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->ele_id = null;
		$this->pers_id = null;
		$this->resp_legal = null;
		$this->pers_contact = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
	}

	/**
	 * Resets all references to other model objects or collections of model objects.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect
	 * objects with circular references (even in PHP 5.3). This is currently necessary
	 * when using Propel in certain daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all referrer objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

		$this->aEleve = null;
		$this->aResponsableEleve = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(ResponsableInformationPeer::DEFAULT_STRING_FORMAT);
	}

	/**
	 * Catches calls to virtual methods
	 */
	public function __call($name, $params)
	{
		if (preg_match('/get(\w+)/', $name, $matches)) {
			$virtualColumn = $matches[1];
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
			// no lcfirst in php<5.3...
			$virtualColumn[0] = strtolower($virtualColumn[0]);
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
		}
		return parent::__call($name, $params);
	}

} // BaseResponsableInformation
