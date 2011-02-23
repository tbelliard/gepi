<?php


/**
 * Base class that represents a row from the 'j_eleves_etablissements' table.
 *
 * Table de jointure pour connaitre l'etablissement precedent de l'eleve
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJEleveAncienEtablissement extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'JEleveAncienEtablissementPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        JEleveAncienEtablissementPeer
	 */
	protected static $peer;

	/**
	 * The value for the id_eleve field.
	 * @var        string
	 */
	protected $id_eleve;

	/**
	 * The value for the id_etablissement field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $id_etablissement;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        AncienEtablissement
	 */
	protected $aAncienEtablissement;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->id_etablissement = '';
	}

	/**
	 * Initializes internal state of BaseJEleveAncienEtablissement object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id_eleve] column value.
	 * cle etrangere, id_eleve de l'eleve
	 * @return     string
	 */
	public function getIdEleve()
	{
		return $this->id_eleve;
	}

	/**
	 * Get the [id_etablissement] column value.
	 * cle etrangere, id de l'etablissement
	 * @return     string
	 */
	public function getIdEtablissement()
	{
		return $this->id_etablissement;
	}

	/**
	 * Set the value of [id_eleve] column.
	 * cle etrangere, id_eleve de l'eleve
	 * @param      string $v new value
	 * @return     JEleveAncienEtablissement The current object (for fluent API support)
	 */
	public function setIdEleve($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id_eleve !== $v) {
			$this->id_eleve = $v;
			$this->modifiedColumns[] = JEleveAncienEtablissementPeer::ID_ELEVE;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setIdEleve()

	/**
	 * Set the value of [id_etablissement] column.
	 * cle etrangere, id de l'etablissement
	 * @param      string $v new value
	 * @return     JEleveAncienEtablissement The current object (for fluent API support)
	 */
	public function setIdEtablissement($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id_etablissement !== $v || $this->isNew()) {
			$this->id_etablissement = $v;
			$this->modifiedColumns[] = JEleveAncienEtablissementPeer::ID_ETABLISSEMENT;
		}

		if ($this->aAncienEtablissement !== null && $this->aAncienEtablissement->getId() !== $v) {
			$this->aAncienEtablissement = null;
		}

		return $this;
	} // setIdEtablissement()

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
			if ($this->id_etablissement !== '') {
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

			$this->id_eleve = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->id_etablissement = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 2; // 2 = JEleveAncienEtablissementPeer::NUM_COLUMNS - JEleveAncienEtablissementPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating JEleveAncienEtablissement object", $e);
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
		if ($this->aAncienEtablissement !== null && $this->id_etablissement !== $this->aAncienEtablissement->getId()) {
			$this->aAncienEtablissement = null;
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
			$con = Propel::getConnection(JEleveAncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = JEleveAncienEtablissementPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aEleve = null;
			$this->aAncienEtablissement = null;
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
			$con = Propel::getConnection(JEleveAncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				JEleveAncienEtablissementQuery::create()
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
			$con = Propel::getConnection(JEleveAncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				JEleveAncienEtablissementPeer::addInstanceToPool($this);
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

			if ($this->aAncienEtablissement !== null) {
				if ($this->aAncienEtablissement->isModified() || $this->aAncienEtablissement->isNew()) {
					$affectedRows += $this->aAncienEtablissement->save($con);
				}
				$this->setAncienEtablissement($this->aAncienEtablissement);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setNew(false);
				} else {
					$affectedRows += JEleveAncienEtablissementPeer::doUpdate($this, $con);
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

			if ($this->aAncienEtablissement !== null) {
				if (!$this->aAncienEtablissement->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAncienEtablissement->getValidationFailures());
				}
			}


			if (($retval = JEleveAncienEtablissementPeer::doValidate($this, $columns)) !== true) {
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
		$pos = JEleveAncienEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdEleve();
				break;
			case 1:
				return $this->getIdEtablissement();
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
		$keys = JEleveAncienEtablissementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdEleve(),
			$keys[1] => $this->getIdEtablissement(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aEleve) {
				$result['Eleve'] = $this->aEleve->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aAncienEtablissement) {
				$result['AncienEtablissement'] = $this->aAncienEtablissement->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = JEleveAncienEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdEleve($value);
				break;
			case 1:
				$this->setIdEtablissement($value);
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
		$keys = JEleveAncienEtablissementPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdEleve($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIdEtablissement($arr[$keys[1]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(JEleveAncienEtablissementPeer::DATABASE_NAME);

		if ($this->isColumnModified(JEleveAncienEtablissementPeer::ID_ELEVE)) $criteria->add(JEleveAncienEtablissementPeer::ID_ELEVE, $this->id_eleve);
		if ($this->isColumnModified(JEleveAncienEtablissementPeer::ID_ETABLISSEMENT)) $criteria->add(JEleveAncienEtablissementPeer::ID_ETABLISSEMENT, $this->id_etablissement);

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
		$criteria = new Criteria(JEleveAncienEtablissementPeer::DATABASE_NAME);
		$criteria->add(JEleveAncienEtablissementPeer::ID_ELEVE, $this->id_eleve);
		$criteria->add(JEleveAncienEtablissementPeer::ID_ETABLISSEMENT, $this->id_etablissement);

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
		$pks[0] = $this->getIdEleve();
		$pks[1] = $this->getIdEtablissement();

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
		$this->setIdEleve($keys[0]);
		$this->setIdEtablissement($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getIdEleve()) && (null === $this->getIdEtablissement());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of JEleveAncienEtablissement (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setIdEleve($this->id_eleve);
		$copyObj->setIdEtablissement($this->id_etablissement);

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
	 * @return     JEleveAncienEtablissement Clone of current object.
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
	 * @return     JEleveAncienEtablissementPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new JEleveAncienEtablissementPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     JEleveAncienEtablissement The current object (for fluent API support)
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
			$v->addJEleveAncienEtablissement($this);
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
		if ($this->aEleve === null && (($this->id_eleve !== "" && $this->id_eleve !== null))) {
			$this->aEleve = EleveQuery::create()->findPk($this->id_eleve, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aEleve->addJEleveAncienEtablissements($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Declares an association between this object and a AncienEtablissement object.
	 *
	 * @param      AncienEtablissement $v
	 * @return     JEleveAncienEtablissement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAncienEtablissement(AncienEtablissement $v = null)
	{
		if ($v === null) {
			$this->setIdEtablissement('');
		} else {
			$this->setIdEtablissement($v->getId());
		}

		$this->aAncienEtablissement = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AncienEtablissement object, it will not be re-added.
		if ($v !== null) {
			$v->addJEleveAncienEtablissement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AncienEtablissement object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AncienEtablissement The associated AncienEtablissement object.
	 * @throws     PropelException
	 */
	public function getAncienEtablissement(PropelPDO $con = null)
	{
		if ($this->aAncienEtablissement === null && (($this->id_etablissement !== "" && $this->id_etablissement !== null))) {
			$this->aAncienEtablissement = AncienEtablissementQuery::create()->findPk($this->id_etablissement, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aAncienEtablissement->addJEleveAncienEtablissements($this);
			 */
		}
		return $this->aAncienEtablissement;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id_eleve = null;
		$this->id_etablissement = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->applyDefaultValues();
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
		$this->aAncienEtablissement = null;
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

} // BaseJEleveAncienEtablissement
