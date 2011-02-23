<?php


/**
 * Base class that represents a row from the 'ects_credits' table.
 *
 * Objet qui précise le nombre d'ECTS obtenus par l'eleve pour un enseignement et une periode donnée
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCreditEcts extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'CreditEctsPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CreditEctsPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the id_eleve field.
	 * @var        int
	 */
	protected $id_eleve;

	/**
	 * The value for the num_periode field.
	 * @var        int
	 */
	protected $num_periode;

	/**
	 * The value for the id_groupe field.
	 * @var        int
	 */
	protected $id_groupe;

	/**
	 * The value for the valeur field.
	 * @var        string
	 */
	protected $valeur;

	/**
	 * The value for the mention field.
	 * @var        string
	 */
	protected $mention;

	/**
	 * The value for the mention_prof field.
	 * @var        string
	 */
	protected $mention_prof;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        Groupe
	 */
	protected $aGroupe;

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
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [id_eleve] column value.
	 * Identifiant de l'eleve
	 * @return     int
	 */
	public function getIdEleve()
	{
		return $this->id_eleve;
	}

	/**
	 * Get the [num_periode] column value.
	 * Identifiant de la periode
	 * @return     int
	 */
	public function getNumPeriode()
	{
		return $this->num_periode;
	}

	/**
	 * Get the [id_groupe] column value.
	 * Identifiant du groupe
	 * @return     int
	 */
	public function getIdGroupe()
	{
		return $this->id_groupe;
	}

	/**
	 * Get the [valeur] column value.
	 * Nombre de credits obtenus par l'eleve
	 * @return     string
	 */
	public function getValeur()
	{
		return $this->valeur;
	}

	/**
	 * Get the [mention] column value.
	 * Mention obtenue
	 * @return     string
	 */
	public function getMention()
	{
		return $this->mention;
	}

	/**
	 * Get the [mention_prof] column value.
	 * Mention presaisie par le prof
	 * @return     string
	 */
	public function getMentionProf()
	{
		return $this->mention_prof;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CreditEctsPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [id_eleve] column.
	 * Identifiant de l'eleve
	 * @param      int $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setIdEleve($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_eleve !== $v) {
			$this->id_eleve = $v;
			$this->modifiedColumns[] = CreditEctsPeer::ID_ELEVE;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setIdEleve()

	/**
	 * Set the value of [num_periode] column.
	 * Identifiant de la periode
	 * @param      int $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setNumPeriode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->num_periode !== $v) {
			$this->num_periode = $v;
			$this->modifiedColumns[] = CreditEctsPeer::NUM_PERIODE;
		}

		return $this;
	} // setNumPeriode()

	/**
	 * Set the value of [id_groupe] column.
	 * Identifiant du groupe
	 * @param      int $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = CreditEctsPeer::ID_GROUPE;
		}

		if ($this->aGroupe !== null && $this->aGroupe->getId() !== $v) {
			$this->aGroupe = null;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [valeur] column.
	 * Nombre de credits obtenus par l'eleve
	 * @param      string $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setValeur($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->valeur !== $v) {
			$this->valeur = $v;
			$this->modifiedColumns[] = CreditEctsPeer::VALEUR;
		}

		return $this;
	} // setValeur()

	/**
	 * Set the value of [mention] column.
	 * Mention obtenue
	 * @param      string $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setMention($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mention !== $v) {
			$this->mention = $v;
			$this->modifiedColumns[] = CreditEctsPeer::MENTION;
		}

		return $this;
	} // setMention()

	/**
	 * Set the value of [mention_prof] column.
	 * Mention presaisie par le prof
	 * @param      string $v new value
	 * @return     CreditEcts The current object (for fluent API support)
	 */
	public function setMentionProf($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mention_prof !== $v) {
			$this->mention_prof = $v;
			$this->modifiedColumns[] = CreditEctsPeer::MENTION_PROF;
		}

		return $this;
	} // setMentionProf()

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
			$this->id_eleve = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->num_periode = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->id_groupe = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->valeur = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->mention = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->mention_prof = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 7; // 7 = CreditEctsPeer::NUM_COLUMNS - CreditEctsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CreditEcts object", $e);
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

		if ($this->aEleve !== null && $this->id_eleve !== $this->aEleve->getIdEleve()) {
			$this->aEleve = null;
		}
		if ($this->aGroupe !== null && $this->id_groupe !== $this->aGroupe->getId()) {
			$this->aGroupe = null;
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
			$con = Propel::getConnection(CreditEctsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CreditEctsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aEleve = null;
			$this->aGroupe = null;
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
			$con = Propel::getConnection(CreditEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CreditEctsQuery::create()
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
			$con = Propel::getConnection(CreditEctsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CreditEctsPeer::addInstanceToPool($this);
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

			if ($this->aGroupe !== null) {
				if ($this->aGroupe->isModified() || $this->aGroupe->isNew()) {
					$affectedRows += $this->aGroupe->save($con);
				}
				$this->setGroupe($this->aGroupe);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CreditEctsPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CreditEctsPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditEctsPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CreditEctsPeer::doUpdate($this, $con);
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

			if ($this->aGroupe !== null) {
				if (!$this->aGroupe->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroupe->getValidationFailures());
				}
			}


			if (($retval = CreditEctsPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CreditEctsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdEleve();
				break;
			case 2:
				return $this->getNumPeriode();
				break;
			case 3:
				return $this->getIdGroupe();
				break;
			case 4:
				return $this->getValeur();
				break;
			case 5:
				return $this->getMention();
				break;
			case 6:
				return $this->getMentionProf();
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
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = CreditEctsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getIdEleve(),
			$keys[2] => $this->getNumPeriode(),
			$keys[3] => $this->getIdGroupe(),
			$keys[4] => $this->getValeur(),
			$keys[5] => $this->getMention(),
			$keys[6] => $this->getMentionProf(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aEleve) {
				$result['Eleve'] = $this->aEleve->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aGroupe) {
				$result['Groupe'] = $this->aGroupe->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = CreditEctsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdEleve($value);
				break;
			case 2:
				$this->setNumPeriode($value);
				break;
			case 3:
				$this->setIdGroupe($value);
				break;
			case 4:
				$this->setValeur($value);
				break;
			case 5:
				$this->setMention($value);
				break;
			case 6:
				$this->setMentionProf($value);
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
		$keys = CreditEctsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIdEleve($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNumPeriode($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setIdGroupe($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setValeur($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMention($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setMentionProf($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CreditEctsPeer::DATABASE_NAME);

		if ($this->isColumnModified(CreditEctsPeer::ID)) $criteria->add(CreditEctsPeer::ID, $this->id);
		if ($this->isColumnModified(CreditEctsPeer::ID_ELEVE)) $criteria->add(CreditEctsPeer::ID_ELEVE, $this->id_eleve);
		if ($this->isColumnModified(CreditEctsPeer::NUM_PERIODE)) $criteria->add(CreditEctsPeer::NUM_PERIODE, $this->num_periode);
		if ($this->isColumnModified(CreditEctsPeer::ID_GROUPE)) $criteria->add(CreditEctsPeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(CreditEctsPeer::VALEUR)) $criteria->add(CreditEctsPeer::VALEUR, $this->valeur);
		if ($this->isColumnModified(CreditEctsPeer::MENTION)) $criteria->add(CreditEctsPeer::MENTION, $this->mention);
		if ($this->isColumnModified(CreditEctsPeer::MENTION_PROF)) $criteria->add(CreditEctsPeer::MENTION_PROF, $this->mention_prof);

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
		$criteria = new Criteria(CreditEctsPeer::DATABASE_NAME);
		$criteria->add(CreditEctsPeer::ID, $this->id);
		$criteria->add(CreditEctsPeer::ID_ELEVE, $this->id_eleve);
		$criteria->add(CreditEctsPeer::NUM_PERIODE, $this->num_periode);
		$criteria->add(CreditEctsPeer::ID_GROUPE, $this->id_groupe);

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
		$pks[0] = $this->getId();
		$pks[1] = $this->getIdEleve();
		$pks[2] = $this->getNumPeriode();
		$pks[3] = $this->getIdGroupe();

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
		$this->setId($keys[0]);
		$this->setIdEleve($keys[1]);
		$this->setNumPeriode($keys[2]);
		$this->setIdGroupe($keys[3]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getId()) && (null === $this->getIdEleve()) && (null === $this->getNumPeriode()) && (null === $this->getIdGroupe());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CreditEcts (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setIdEleve($this->id_eleve);
		$copyObj->setNumPeriode($this->num_periode);
		$copyObj->setIdGroupe($this->id_groupe);
		$copyObj->setValeur($this->valeur);
		$copyObj->setMention($this->mention);
		$copyObj->setMentionProf($this->mention_prof);

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
	 * @return     CreditEcts Clone of current object.
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
	 * @return     CreditEctsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CreditEctsPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     CreditEcts The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEleve(Eleve $v = null)
	{
		if ($v === null) {
			$this->setIdEleve(NULL);
		} else {
			$this->setIdEleve($v->getIdEleve());
		}

		$this->aEleve = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Eleve object, it will not be re-added.
		if ($v !== null) {
			$v->addCreditEcts($this);
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
		if ($this->aEleve === null && ($this->id_eleve !== null)) {
			$this->aEleve = EleveQuery::create()->findPk($this->id_eleve, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aEleve->addCreditEctss($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     CreditEcts The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setGroupe(Groupe $v = null)
	{
		if ($v === null) {
			$this->setIdGroupe(NULL);
		} else {
			$this->setIdGroupe($v->getId());
		}

		$this->aGroupe = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Groupe object, it will not be re-added.
		if ($v !== null) {
			$v->addCreditEcts($this);
		}

		return $this;
	}


	/**
	 * Get the associated Groupe object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Groupe The associated Groupe object.
	 * @throws     PropelException
	 */
	public function getGroupe(PropelPDO $con = null)
	{
		if ($this->aGroupe === null && ($this->id_groupe !== null)) {
			$this->aGroupe = GroupeQuery::create()->findPk($this->id_groupe, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aGroupe->addCreditEctss($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->id_eleve = null;
		$this->num_periode = null;
		$this->id_groupe = null;
		$this->valeur = null;
		$this->mention = null;
		$this->mention_prof = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
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

		$this->aEleve = null;
		$this->aGroupe = null;
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

} // BaseCreditEcts
