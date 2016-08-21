<?php


/**
 * Base class that represents a row from the 'resp_adr' table.
 *
 * Adresse
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAdresse extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AdressePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AdressePeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

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
	 * @var        array AbsenceEleveNotification[] Collection to store aggregation of AbsenceEleveNotification objects.
	 */
	protected $collAbsenceEleveNotifications;

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
	protected $responsableElevesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveNotificationsScheduledForDeletion = null;

	/**
	 * Get the [adr_id] column value.
	 * cle primaire, genere par sconet
	 * @return     string
	 */
	public function getId()
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
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr_id !== $v) {
			$this->adr_id = $v;
			$this->modifiedColumns[] = AdressePeer::ADR_ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [adr1] column.
	 * 1ere ligne adresse
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setAdr1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr1 !== $v) {
			$this->adr1 = $v;
			$this->modifiedColumns[] = AdressePeer::ADR1;
		}

		return $this;
	} // setAdr1()

	/**
	 * Set the value of [adr2] column.
	 * 2eme ligne adresse
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setAdr2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr2 !== $v) {
			$this->adr2 = $v;
			$this->modifiedColumns[] = AdressePeer::ADR2;
		}

		return $this;
	} // setAdr2()

	/**
	 * Set the value of [adr3] column.
	 * 3eme ligne adresse
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setAdr3($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr3 !== $v) {
			$this->adr3 = $v;
			$this->modifiedColumns[] = AdressePeer::ADR3;
		}

		return $this;
	} // setAdr3()

	/**
	 * Set the value of [adr4] column.
	 * 4eme ligne adresse
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setAdr4($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr4 !== $v) {
			$this->adr4 = $v;
			$this->modifiedColumns[] = AdressePeer::ADR4;
		}

		return $this;
	} // setAdr4()

	/**
	 * Set the value of [cp] column.
	 * Code postal
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setCp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cp !== $v) {
			$this->cp = $v;
			$this->modifiedColumns[] = AdressePeer::CP;
		}

		return $this;
	} // setCp()

	/**
	 * Set the value of [pays] column.
	 * Pays (quand il est autre que France)
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setPays($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pays !== $v) {
			$this->pays = $v;
			$this->modifiedColumns[] = AdressePeer::PAYS;
		}

		return $this;
	} // setPays()

	/**
	 * Set the value of [commune] column.
	 * Commune de residence
	 * @param      string $v new value
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function setCommune($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commune !== $v) {
			$this->commune = $v;
			$this->modifiedColumns[] = AdressePeer::COMMUNE;
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

			return $startcol + 8; // 8 = AdressePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating Adresse object", $e);
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
			$con = Propel::getConnection(AdressePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AdressePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collResponsableEleves = null;

			$this->collAbsenceEleveNotifications = null;

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
			$con = Propel::getConnection(AdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = AdresseQuery::create()
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
			$con = Propel::getConnection(AdressePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AdressePeer::addInstanceToPool($this);
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

			if ($this->responsableElevesScheduledForDeletion !== null) {
				if (!$this->responsableElevesScheduledForDeletion->isEmpty()) {
					ResponsableEleveQuery::create()
						->filterByPrimaryKeys($this->responsableElevesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->responsableElevesScheduledForDeletion = null;
				}
			}

			if ($this->collResponsableEleves !== null) {
				foreach ($this->collResponsableEleves as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->absenceEleveNotificationsScheduledForDeletion !== null) {
				if (!$this->absenceEleveNotificationsScheduledForDeletion->isEmpty()) {
					AbsenceEleveNotificationQuery::create()
						->filterByPrimaryKeys($this->absenceEleveNotificationsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveNotificationsScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveNotifications !== null) {
				foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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


		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(AdressePeer::ADR_ID)) {
			$modifiedColumns[':p' . $index++]  = 'ADR_ID';
		}
		if ($this->isColumnModified(AdressePeer::ADR1)) {
			$modifiedColumns[':p' . $index++]  = 'ADR1';
		}
		if ($this->isColumnModified(AdressePeer::ADR2)) {
			$modifiedColumns[':p' . $index++]  = 'ADR2';
		}
		if ($this->isColumnModified(AdressePeer::ADR3)) {
			$modifiedColumns[':p' . $index++]  = 'ADR3';
		}
		if ($this->isColumnModified(AdressePeer::ADR4)) {
			$modifiedColumns[':p' . $index++]  = 'ADR4';
		}
		if ($this->isColumnModified(AdressePeer::CP)) {
			$modifiedColumns[':p' . $index++]  = 'CP';
		}
		if ($this->isColumnModified(AdressePeer::PAYS)) {
			$modifiedColumns[':p' . $index++]  = 'PAYS';
		}
		if ($this->isColumnModified(AdressePeer::COMMUNE)) {
			$modifiedColumns[':p' . $index++]  = 'COMMUNE';
		}

		$sql = sprintf(
			'INSERT INTO resp_adr (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ADR_ID':
						$stmt->bindValue($identifier, $this->adr_id, PDO::PARAM_STR);
						break;
					case 'ADR1':
						$stmt->bindValue($identifier, $this->adr1, PDO::PARAM_STR);
						break;
					case 'ADR2':
						$stmt->bindValue($identifier, $this->adr2, PDO::PARAM_STR);
						break;
					case 'ADR3':
						$stmt->bindValue($identifier, $this->adr3, PDO::PARAM_STR);
						break;
					case 'ADR4':
						$stmt->bindValue($identifier, $this->adr4, PDO::PARAM_STR);
						break;
					case 'CP':
						$stmt->bindValue($identifier, $this->cp, PDO::PARAM_STR);
						break;
					case 'PAYS':
						$stmt->bindValue($identifier, $this->pays, PDO::PARAM_STR);
						break;
					case 'COMMUNE':
						$stmt->bindValue($identifier, $this->commune, PDO::PARAM_STR);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

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


			if (($retval = AdressePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collResponsableEleves !== null) {
					foreach ($this->collResponsableEleves as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveNotifications !== null) {
					foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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
		$pos = AdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		if (isset($alreadyDumpedObjects['Adresse'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Adresse'][$this->getPrimaryKey()] = true;
		$keys = AdressePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getAdr1(),
			$keys[2] => $this->getAdr2(),
			$keys[3] => $this->getAdr3(),
			$keys[4] => $this->getAdr4(),
			$keys[5] => $this->getCp(),
			$keys[6] => $this->getPays(),
			$keys[7] => $this->getCommune(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collResponsableEleves) {
				$result['ResponsableEleves'] = $this->collResponsableEleves->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveNotifications) {
				$result['AbsenceEleveNotifications'] = $this->collAbsenceEleveNotifications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = AdressePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = AdressePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
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
		$criteria = new Criteria(AdressePeer::DATABASE_NAME);

		if ($this->isColumnModified(AdressePeer::ADR_ID)) $criteria->add(AdressePeer::ADR_ID, $this->adr_id);
		if ($this->isColumnModified(AdressePeer::ADR1)) $criteria->add(AdressePeer::ADR1, $this->adr1);
		if ($this->isColumnModified(AdressePeer::ADR2)) $criteria->add(AdressePeer::ADR2, $this->adr2);
		if ($this->isColumnModified(AdressePeer::ADR3)) $criteria->add(AdressePeer::ADR3, $this->adr3);
		if ($this->isColumnModified(AdressePeer::ADR4)) $criteria->add(AdressePeer::ADR4, $this->adr4);
		if ($this->isColumnModified(AdressePeer::CP)) $criteria->add(AdressePeer::CP, $this->cp);
		if ($this->isColumnModified(AdressePeer::PAYS)) $criteria->add(AdressePeer::PAYS, $this->pays);
		if ($this->isColumnModified(AdressePeer::COMMUNE)) $criteria->add(AdressePeer::COMMUNE, $this->commune);

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
		$criteria = new Criteria(AdressePeer::DATABASE_NAME);
		$criteria->add(AdressePeer::ADR_ID, $this->adr_id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (adr_id column).
	 *
	 * @param      string $key Primary key.
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
	 * @param      object $copyObj An object of Adresse (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setAdr1($this->getAdr1());
		$copyObj->setAdr2($this->getAdr2());
		$copyObj->setAdr3($this->getAdr3());
		$copyObj->setAdr4($this->getAdr4());
		$copyObj->setCp($this->getCp());
		$copyObj->setPays($this->getPays());
		$copyObj->setCommune($this->getCommune());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getResponsableEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addResponsableEleve($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveNotifications() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveNotification($relObj->copy($deepCopy));
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
	 * @return     Adresse Clone of current object.
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
	 * @return     AdressePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AdressePeer();
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
		if ('ResponsableEleve' == $relationName) {
			return $this->initResponsableEleves();
		}
		if ('AbsenceEleveNotification' == $relationName) {
			return $this->initAbsenceEleveNotifications();
		}
	}

	/**
	 * Clears out the collResponsableEleves collection
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
	 * Initializes the collResponsableEleves collection.
	 *
	 * By default this just sets the collResponsableEleves collection to an empty array (like clearcollResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initResponsableEleves($overrideExisting = true)
	{
		if (null !== $this->collResponsableEleves && !$overrideExisting) {
			return;
		}
		$this->collResponsableEleves = new PropelObjectCollection();
		$this->collResponsableEleves->setModel('ResponsableEleve');
	}

	/**
	 * Gets an array of ResponsableEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Adresse is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ResponsableEleve[] List of ResponsableEleve objects
	 * @throws     PropelException
	 */
	public function getResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				// return empty collection
				$this->initResponsableEleves();
			} else {
				$collResponsableEleves = ResponsableEleveQuery::create(null, $criteria)
					->filterByAdresse($this)
					->find($con);
				if (null !== $criteria) {
					return $collResponsableEleves;
				}
				$this->collResponsableEleves = $collResponsableEleves;
			}
		}
		return $this->collResponsableEleves;
	}

	/**
	 * Sets a collection of ResponsableEleve objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $responsableEleves A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setResponsableEleves(PropelCollection $responsableEleves, PropelPDO $con = null)
	{
		$this->responsableElevesScheduledForDeletion = $this->getResponsableEleves(new Criteria(), $con)->diff($responsableEleves);

		foreach ($responsableEleves as $responsableEleve) {
			// Fix issue with collection modified by reference
			if ($responsableEleve->isNew()) {
				$responsableEleve->setAdresse($this);
			}
			$this->addResponsableEleve($responsableEleve);
		}

		$this->collResponsableEleves = $responsableEleves;
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
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				return 0;
			} else {
				$query = ResponsableEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAdresse($this)
					->count($con);
			}
		} else {
			return count($this->collResponsableEleves);
		}
	}

	/**
	 * Method called to associate a ResponsableEleve object to this object
	 * through the ResponsableEleve foreign key attribute.
	 *
	 * @param      ResponsableEleve $l ResponsableEleve
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function addResponsableEleve(ResponsableEleve $l)
	{
		if ($this->collResponsableEleves === null) {
			$this->initResponsableEleves();
		}
		if (!$this->collResponsableEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddResponsableEleve($l);
		}

		return $this;
	}

	/**
	 * @param	ResponsableEleve $responsableEleve The responsableEleve object to add.
	 */
	protected function doAddResponsableEleve($responsableEleve)
	{
		$this->collResponsableEleves[]= $responsableEleve;
		$responsableEleve->setAdresse($this);
	}

	/**
	 * Clears out the collAbsenceEleveNotifications collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveNotifications()
	 */
	public function clearAbsenceEleveNotifications()
	{
		$this->collAbsenceEleveNotifications = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveNotifications collection.
	 *
	 * By default this just sets the collAbsenceEleveNotifications collection to an empty array (like clearcollAbsenceEleveNotifications());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveNotifications($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveNotifications && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveNotifications = new PropelObjectCollection();
		$this->collAbsenceEleveNotifications->setModel('AbsenceEleveNotification');
	}

	/**
	 * Gets an array of AbsenceEleveNotification objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Adresse is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveNotifications($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				// return empty collection
				$this->initAbsenceEleveNotifications();
			} else {
				$collAbsenceEleveNotifications = AbsenceEleveNotificationQuery::create(null, $criteria)
					->filterByAdresse($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveNotifications;
				}
				$this->collAbsenceEleveNotifications = $collAbsenceEleveNotifications;
			}
		}
		return $this->collAbsenceEleveNotifications;
	}

	/**
	 * Sets a collection of AbsenceEleveNotification objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveNotifications A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveNotifications(PropelCollection $absenceEleveNotifications, PropelPDO $con = null)
	{
		$this->absenceEleveNotificationsScheduledForDeletion = $this->getAbsenceEleveNotifications(new Criteria(), $con)->diff($absenceEleveNotifications);

		foreach ($absenceEleveNotifications as $absenceEleveNotification) {
			// Fix issue with collection modified by reference
			if ($absenceEleveNotification->isNew()) {
				$absenceEleveNotification->setAdresse($this);
			}
			$this->addAbsenceEleveNotification($absenceEleveNotification);
		}

		$this->collAbsenceEleveNotifications = $absenceEleveNotifications;
	}

	/**
	 * Returns the number of related AbsenceEleveNotification objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveNotification objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveNotifications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				return 0;
			} else {
				$query = AbsenceEleveNotificationQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAdresse($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveNotifications);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveNotification object to this object
	 * through the AbsenceEleveNotification foreign key attribute.
	 *
	 * @param      AbsenceEleveNotification $l AbsenceEleveNotification
	 * @return     Adresse The current object (for fluent API support)
	 */
	public function addAbsenceEleveNotification(AbsenceEleveNotification $l)
	{
		if ($this->collAbsenceEleveNotifications === null) {
			$this->initAbsenceEleveNotifications();
		}
		if (!$this->collAbsenceEleveNotifications->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveNotification($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveNotification $absenceEleveNotification The absenceEleveNotification object to add.
	 */
	protected function doAddAbsenceEleveNotification($absenceEleveNotification)
	{
		$this->collAbsenceEleveNotifications[]= $absenceEleveNotification;
		$absenceEleveNotification->setAdresse($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Adresse is new, it will return
	 * an empty collection; or if this Adresse has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Adresse.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Adresse is new, it will return
	 * an empty collection; or if this Adresse has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Adresse.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinAbsenceEleveTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveTraitement', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->adr_id = null;
		$this->adr1 = null;
		$this->adr2 = null;
		$this->adr3 = null;
		$this->adr4 = null;
		$this->cp = null;
		$this->pays = null;
		$this->commune = null;
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
			if ($this->collResponsableEleves) {
				foreach ($this->collResponsableEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ($this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collResponsableEleves instanceof PropelCollection) {
			$this->collResponsableEleves->clearIterator();
		}
		$this->collResponsableEleves = null;
		if ($this->collAbsenceEleveNotifications instanceof PropelCollection) {
			$this->collAbsenceEleveNotifications->clearIterator();
		}
		$this->collAbsenceEleveNotifications = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AdressePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseAdresse
