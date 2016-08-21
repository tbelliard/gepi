<?php


/**
 * Base class that represents a row from the 'ct_sequences' table.
 *
 * Sequence de plusieurs compte-rendus
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteSequence extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'CahierTexteSequencePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CahierTexteSequencePeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the titre field.
	 * @var        string
	 */
	protected $titre;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * @var        array CahierTexteCompteRendu[] Collection to store aggregation of CahierTexteCompteRendu objects.
	 */
	protected $collCahierTexteCompteRendus;

	/**
	 * @var        array CahierTexteTravailAFaire[] Collection to store aggregation of CahierTexteTravailAFaire objects.
	 */
	protected $collCahierTexteTravailAFaires;

	/**
	 * @var        array CahierTexteNoticePrivee[] Collection to store aggregation of CahierTexteNoticePrivee objects.
	 */
	protected $collCahierTexteNoticePrivees;

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
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteCompteRendusScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteTravailAFairesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteNoticePriveesScheduledForDeletion = null;

	/**
	 * Get the [id] column value.
	 * Cle primaire des sequences
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [titre] column value.
	 * Titre de la sequence
	 * @return     string
	 */
	public function getTitre()
	{
		return $this->titre;
	}

	/**
	 * Get the [description] column value.
	 * Description de la sequence
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire des sequences
	 * @param      int $v new value
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CahierTexteSequencePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [titre] column.
	 * Titre de la sequence
	 * @param      string $v new value
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function setTitre($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->titre !== $v) {
			$this->titre = $v;
			$this->modifiedColumns[] = CahierTexteSequencePeer::TITRE;
		}

		return $this;
	} // setTitre()

	/**
	 * Set the value of [description] column.
	 * Description de la sequence
	 * @param      string $v new value
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = CahierTexteSequencePeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

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

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->titre = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->description = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 3; // 3 = CahierTexteSequencePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating CahierTexteSequence object", $e);
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
			$con = Propel::getConnection(CahierTexteSequencePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CahierTexteSequencePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collCahierTexteCompteRendus = null;

			$this->collCahierTexteTravailAFaires = null;

			$this->collCahierTexteNoticePrivees = null;

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
			$con = Propel::getConnection(CahierTexteSequencePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = CahierTexteSequenceQuery::create()
				->filterByPrimaryKey($this->getPrimaryKey());
			$ret = $this->preDelete($con);
			if ($ret) {
				$deleteQuery->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
		} catch (Exception $e) {
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
			$con = Propel::getConnection(CahierTexteSequencePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CahierTexteSequencePeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (Exception $e) {
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

			if ($this->isNew() || $this->isModified()) {
				// persist changes
				if ($this->isNew()) {
					$this->doInsert($con);
				} else {
					$this->doUpdate($con);
				}
				$affectedRows += 1;
				$this->resetModified();
			}

			if ($this->cahierTexteCompteRendusScheduledForDeletion !== null) {
				if (!$this->cahierTexteCompteRendusScheduledForDeletion->isEmpty()) {
					CahierTexteCompteRenduQuery::create()
						->filterByPrimaryKeys($this->cahierTexteCompteRendusScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteCompteRendusScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteCompteRendus !== null) {
				foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->cahierTexteTravailAFairesScheduledForDeletion !== null) {
				if (!$this->cahierTexteTravailAFairesScheduledForDeletion->isEmpty()) {
					CahierTexteTravailAFaireQuery::create()
						->filterByPrimaryKeys($this->cahierTexteTravailAFairesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteTravailAFairesScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteTravailAFaires !== null) {
				foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->cahierTexteNoticePriveesScheduledForDeletion !== null) {
				if (!$this->cahierTexteNoticePriveesScheduledForDeletion->isEmpty()) {
					CahierTexteNoticePriveeQuery::create()
						->filterByPrimaryKeys($this->cahierTexteNoticePriveesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteNoticePriveesScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteNoticePrivees !== null) {
				foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
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
	 * Insert the row in the database.
	 *
	 * @param      PropelPDO $con
	 *
	 * @throws     PropelException
	 * @see        doSave()
	 */
	protected function doInsert(PropelPDO $con)
	{
		$modifiedColumns = array();
		$index = 0;

		$this->modifiedColumns[] = CahierTexteSequencePeer::ID;
		if (null !== $this->id) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . CahierTexteSequencePeer::ID . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(CahierTexteSequencePeer::ID)) {
			$modifiedColumns[':p' . $index++]  = 'ID';
		}
		if ($this->isColumnModified(CahierTexteSequencePeer::TITRE)) {
			$modifiedColumns[':p' . $index++]  = 'TITRE';
		}
		if ($this->isColumnModified(CahierTexteSequencePeer::DESCRIPTION)) {
			$modifiedColumns[':p' . $index++]  = 'DESCRIPTION';
		}

		$sql = sprintf(
			'INSERT INTO ct_sequences (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ID':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
						break;
					case 'TITRE':
						$stmt->bindValue($identifier, $this->titre, PDO::PARAM_STR);
						break;
					case 'DESCRIPTION':
						$stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

		try {
			$pk = $con->lastInsertId();
		} catch (Exception $e) {
			throw new PropelException('Unable to get autoincrement id.', $e);
		}
		$this->setId($pk);

		$this->setNew(false);
	}

	/**
	 * Update the row in the database.
	 *
	 * @param      PropelPDO $con
	 *
	 * @see        doSave()
	 */
	protected function doUpdate(PropelPDO $con)
	{
		$selectCriteria = $this->buildPkeyCriteria();
		$valuesCriteria = $this->buildCriteria();
		BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
	}

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


			if (($retval = CahierTexteSequencePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = CahierTexteSequencePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getTitre();
				break;
			case 2:
				return $this->getDescription();
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
		if (isset($alreadyDumpedObjects['CahierTexteSequence'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['CahierTexteSequence'][$this->getPrimaryKey()] = true;
		$keys = CahierTexteSequencePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getTitre(),
			$keys[2] => $this->getDescription(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collCahierTexteCompteRendus) {
				$result['CahierTexteCompteRendus'] = $this->collCahierTexteCompteRendus->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCahierTexteTravailAFaires) {
				$result['CahierTexteTravailAFaires'] = $this->collCahierTexteTravailAFaires->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCahierTexteNoticePrivees) {
				$result['CahierTexteNoticePrivees'] = $this->collCahierTexteNoticePrivees->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = CahierTexteSequencePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setTitre($value);
				break;
			case 2:
				$this->setDescription($value);
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
		$keys = CahierTexteSequencePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setTitre($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CahierTexteSequencePeer::DATABASE_NAME);

		if ($this->isColumnModified(CahierTexteSequencePeer::ID)) $criteria->add(CahierTexteSequencePeer::ID, $this->id);
		if ($this->isColumnModified(CahierTexteSequencePeer::TITRE)) $criteria->add(CahierTexteSequencePeer::TITRE, $this->titre);
		if ($this->isColumnModified(CahierTexteSequencePeer::DESCRIPTION)) $criteria->add(CahierTexteSequencePeer::DESCRIPTION, $this->description);

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
		$criteria = new Criteria(CahierTexteSequencePeer::DATABASE_NAME);
		$criteria->add(CahierTexteSequencePeer::ID, $this->id);

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
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CahierTexteSequence (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setTitre($this->getTitre());
		$copyObj->setDescription($this->getDescription());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

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

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setId(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     CahierTexteSequence Clone of current object.
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
	 * @return     CahierTexteSequencePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CahierTexteSequencePeer();
		}
		return self::$peer;
	}


	/**
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('CahierTexteCompteRendu' == $relationName) {
			return $this->initCahierTexteCompteRendus();
		}
		if ('CahierTexteTravailAFaire' == $relationName) {
			return $this->initCahierTexteTravailAFaires();
		}
		if ('CahierTexteNoticePrivee' == $relationName) {
			return $this->initCahierTexteNoticePrivees();
		}
	}

	/**
	 * Clears out the collCahierTexteCompteRendus collection
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
	 * Initializes the collCahierTexteCompteRendus collection.
	 *
	 * By default this just sets the collCahierTexteCompteRendus collection to an empty array (like clearcollCahierTexteCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteCompteRendus($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteCompteRendus && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteCompteRendus = new PropelObjectCollection();
		$this->collCahierTexteCompteRendus->setModel('CahierTexteCompteRendu');
	}

	/**
	 * Gets an array of CahierTexteCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CahierTexteSequence is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteCompteRendus || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteCompteRendus) {
				// return empty collection
				$this->initCahierTexteCompteRendus();
			} else {
				$collCahierTexteCompteRendus = CahierTexteCompteRenduQuery::create(null, $criteria)
					->filterByCahierTexteSequence($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteCompteRendus;
				}
				$this->collCahierTexteCompteRendus = $collCahierTexteCompteRendus;
			}
		}
		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Sets a collection of CahierTexteCompteRendu objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteCompteRendus A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteCompteRendus(PropelCollection $cahierTexteCompteRendus, PropelPDO $con = null)
	{
		$this->cahierTexteCompteRendusScheduledForDeletion = $this->getCahierTexteCompteRendus(new Criteria(), $con)->diff($cahierTexteCompteRendus);

		foreach ($cahierTexteCompteRendus as $cahierTexteCompteRendu) {
			// Fix issue with collection modified by reference
			if ($cahierTexteCompteRendu->isNew()) {
				$cahierTexteCompteRendu->setCahierTexteSequence($this);
			}
			$this->addCahierTexteCompteRendu($cahierTexteCompteRendu);
		}

		$this->collCahierTexteCompteRendus = $cahierTexteCompteRendus;
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
		if(null === $this->collCahierTexteCompteRendus || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteCompteRendus) {
				return 0;
			} else {
				$query = CahierTexteCompteRenduQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCahierTexteSequence($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteCompteRendus);
		}
	}

	/**
	 * Method called to associate a CahierTexteCompteRendu object to this object
	 * through the CahierTexteCompteRendu foreign key attribute.
	 *
	 * @param      CahierTexteCompteRendu $l CahierTexteCompteRendu
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function addCahierTexteCompteRendu(CahierTexteCompteRendu $l)
	{
		if ($this->collCahierTexteCompteRendus === null) {
			$this->initCahierTexteCompteRendus();
		}
		if (!$this->collCahierTexteCompteRendus->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteCompteRendu($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteCompteRendu $cahierTexteCompteRendu The cahierTexteCompteRendu object to add.
	 */
	protected function doAddCahierTexteCompteRendu($cahierTexteCompteRendu)
	{
		$this->collCahierTexteCompteRendus[]= $cahierTexteCompteRendu;
		$cahierTexteCompteRendu->setCahierTexteSequence($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 */
	public function getCahierTexteCompteRendusJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteCompteRenduQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteCompteRendus($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 */
	public function getCahierTexteCompteRendusJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteCompteRenduQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getCahierTexteCompteRendus($query, $con);
	}

	/**
	 * Clears out the collCahierTexteTravailAFaires collection
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
	 * Initializes the collCahierTexteTravailAFaires collection.
	 *
	 * By default this just sets the collCahierTexteTravailAFaires collection to an empty array (like clearcollCahierTexteTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteTravailAFaires($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteTravailAFaires && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteTravailAFaires = new PropelObjectCollection();
		$this->collCahierTexteTravailAFaires->setModel('CahierTexteTravailAFaire');
	}

	/**
	 * Gets an array of CahierTexteTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CahierTexteSequence is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 * @throws     PropelException
	 */
	public function getCahierTexteTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteTravailAFaires || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteTravailAFaires) {
				// return empty collection
				$this->initCahierTexteTravailAFaires();
			} else {
				$collCahierTexteTravailAFaires = CahierTexteTravailAFaireQuery::create(null, $criteria)
					->filterByCahierTexteSequence($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteTravailAFaires;
				}
				$this->collCahierTexteTravailAFaires = $collCahierTexteTravailAFaires;
			}
		}
		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Sets a collection of CahierTexteTravailAFaire objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteTravailAFaires A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteTravailAFaires(PropelCollection $cahierTexteTravailAFaires, PropelPDO $con = null)
	{
		$this->cahierTexteTravailAFairesScheduledForDeletion = $this->getCahierTexteTravailAFaires(new Criteria(), $con)->diff($cahierTexteTravailAFaires);

		foreach ($cahierTexteTravailAFaires as $cahierTexteTravailAFaire) {
			// Fix issue with collection modified by reference
			if ($cahierTexteTravailAFaire->isNew()) {
				$cahierTexteTravailAFaire->setCahierTexteSequence($this);
			}
			$this->addCahierTexteTravailAFaire($cahierTexteTravailAFaire);
		}

		$this->collCahierTexteTravailAFaires = $cahierTexteTravailAFaires;
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
		if(null === $this->collCahierTexteTravailAFaires || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteTravailAFaires) {
				return 0;
			} else {
				$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCahierTexteSequence($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteTravailAFaires);
		}
	}

	/**
	 * Method called to associate a CahierTexteTravailAFaire object to this object
	 * through the CahierTexteTravailAFaire foreign key attribute.
	 *
	 * @param      CahierTexteTravailAFaire $l CahierTexteTravailAFaire
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function addCahierTexteTravailAFaire(CahierTexteTravailAFaire $l)
	{
		if ($this->collCahierTexteTravailAFaires === null) {
			$this->initCahierTexteTravailAFaires();
		}
		if (!$this->collCahierTexteTravailAFaires->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteTravailAFaire($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteTravailAFaire $cahierTexteTravailAFaire The cahierTexteTravailAFaire object to add.
	 */
	protected function doAddCahierTexteTravailAFaire($cahierTexteTravailAFaire)
	{
		$this->collCahierTexteTravailAFaires[]= $cahierTexteTravailAFaire;
		$cahierTexteTravailAFaire->setCahierTexteSequence($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 */
	public function getCahierTexteTravailAFairesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteTravailAFaires($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 */
	public function getCahierTexteTravailAFairesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getCahierTexteTravailAFaires($query, $con);
	}

	/**
	 * Clears out the collCahierTexteNoticePrivees collection
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
	 * Initializes the collCahierTexteNoticePrivees collection.
	 *
	 * By default this just sets the collCahierTexteNoticePrivees collection to an empty array (like clearcollCahierTexteNoticePrivees());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteNoticePrivees($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteNoticePrivees && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteNoticePrivees = new PropelObjectCollection();
		$this->collCahierTexteNoticePrivees->setModel('CahierTexteNoticePrivee');
	}

	/**
	 * Gets an array of CahierTexteNoticePrivee objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CahierTexteSequence is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 * @throws     PropelException
	 */
	public function getCahierTexteNoticePrivees($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteNoticePrivees || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteNoticePrivees) {
				// return empty collection
				$this->initCahierTexteNoticePrivees();
			} else {
				$collCahierTexteNoticePrivees = CahierTexteNoticePriveeQuery::create(null, $criteria)
					->filterByCahierTexteSequence($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteNoticePrivees;
				}
				$this->collCahierTexteNoticePrivees = $collCahierTexteNoticePrivees;
			}
		}
		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Sets a collection of CahierTexteNoticePrivee objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteNoticePrivees A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteNoticePrivees(PropelCollection $cahierTexteNoticePrivees, PropelPDO $con = null)
	{
		$this->cahierTexteNoticePriveesScheduledForDeletion = $this->getCahierTexteNoticePrivees(new Criteria(), $con)->diff($cahierTexteNoticePrivees);

		foreach ($cahierTexteNoticePrivees as $cahierTexteNoticePrivee) {
			// Fix issue with collection modified by reference
			if ($cahierTexteNoticePrivee->isNew()) {
				$cahierTexteNoticePrivee->setCahierTexteSequence($this);
			}
			$this->addCahierTexteNoticePrivee($cahierTexteNoticePrivee);
		}

		$this->collCahierTexteNoticePrivees = $cahierTexteNoticePrivees;
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
		if(null === $this->collCahierTexteNoticePrivees || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteNoticePrivees) {
				return 0;
			} else {
				$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCahierTexteSequence($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteNoticePrivees);
		}
	}

	/**
	 * Method called to associate a CahierTexteNoticePrivee object to this object
	 * through the CahierTexteNoticePrivee foreign key attribute.
	 *
	 * @param      CahierTexteNoticePrivee $l CahierTexteNoticePrivee
	 * @return     CahierTexteSequence The current object (for fluent API support)
	 */
	public function addCahierTexteNoticePrivee(CahierTexteNoticePrivee $l)
	{
		if ($this->collCahierTexteNoticePrivees === null) {
			$this->initCahierTexteNoticePrivees();
		}
		if (!$this->collCahierTexteNoticePrivees->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteNoticePrivee($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteNoticePrivee $cahierTexteNoticePrivee The cahierTexteNoticePrivee object to add.
	 */
	protected function doAddCahierTexteNoticePrivee($cahierTexteNoticePrivee)
	{
		$this->collCahierTexteNoticePrivees[]= $cahierTexteNoticePrivee;
		$cahierTexteNoticePrivee->setCahierTexteSequence($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 */
	public function getCahierTexteNoticePriveesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteNoticePrivees($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CahierTexteSequence is new, it will return
	 * an empty collection; or if this CahierTexteSequence has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CahierTexteSequence.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 */
	public function getCahierTexteNoticePriveesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getCahierTexteNoticePrivees($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->titre = null;
		$this->description = null;
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
			if ($this->collCahierTexteCompteRendus) {
				foreach ($this->collCahierTexteCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteTravailAFaires) {
				foreach ($this->collCahierTexteTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteNoticePrivees) {
				foreach ($this->collCahierTexteNoticePrivees as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collCahierTexteCompteRendus instanceof PropelCollection) {
			$this->collCahierTexteCompteRendus->clearIterator();
		}
		$this->collCahierTexteCompteRendus = null;
		if ($this->collCahierTexteTravailAFaires instanceof PropelCollection) {
			$this->collCahierTexteTravailAFaires->clearIterator();
		}
		$this->collCahierTexteTravailAFaires = null;
		if ($this->collCahierTexteNoticePrivees instanceof PropelCollection) {
			$this->collCahierTexteNoticePrivees->clearIterator();
		}
		$this->collCahierTexteNoticePrivees = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CahierTexteSequencePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseCahierTexteSequence
