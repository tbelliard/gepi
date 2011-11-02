<?php


/**
 * Base class that represents a row from the 'ct_documents' table.
 *
 * Document (fichier joint) appartenant a un compte rendu du cahier de texte
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCahierTexteCompteRenduFichierJoint extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'CahierTexteCompteRenduFichierJointPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CahierTexteCompteRenduFichierJointPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the id_ct field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $id_ct;

	/**
	 * The value for the titre field.
	 * @var        string
	 */
	protected $titre;

	/**
	 * The value for the taille field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $taille;

	/**
	 * The value for the emplacement field.
	 * @var        string
	 */
	protected $emplacement;

	/**
	 * The value for the visible_eleve_parent field.
	 * Note: this column has a database default value of: true
	 * @var        boolean
	 */
	protected $visible_eleve_parent;

	/**
	 * @var        CahierTexteCompteRendu
	 */
	protected $aCahierTexteCompteRendu;

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
		$this->id_ct = 0;
		$this->taille = 0;
		$this->visible_eleve_parent = true;
	}

	/**
	 * Initializes internal state of BaseCahierTexteCompteRenduFichierJoint object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * Cle primaire du document
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [id_ct] column value.
	 * Cle etrangere du compte rendu auquel appartient ce document
	 * @return     int
	 */
	public function getIdCt()
	{
		return $this->id_ct;
	}

	/**
	 * Get the [titre] column value.
	 * Titre du document (fichier joint)
	 * @return     string
	 */
	public function getTitre()
	{
		return $this->titre;
	}

	/**
	 * Get the [taille] column value.
	 * Taille du document (fichier joint)
	 * @return     int
	 */
	public function getTaille()
	{
		return $this->taille;
	}

	/**
	 * Get the [emplacement] column value.
	 * Chemin du systeme de fichier vers le document (fichier joint)
	 * @return     string
	 */
	public function getEmplacement()
	{
		return $this->emplacement;
	}

	/**
	 * Get the [visible_eleve_parent] column value.
	 * Visibilité élève/parent du document joint
	 * @return     boolean
	 */
	public function getVisibleEleveParent()
	{
		return $this->visible_eleve_parent;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire du document
	 * @param      int $v new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [id_ct] column.
	 * Cle etrangere du compte rendu auquel appartient ce document
	 * @param      int $v new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setIdCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_ct !== $v || $this->isNew()) {
			$this->id_ct = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::ID_CT;
		}

		if ($this->aCahierTexteCompteRendu !== null && $this->aCahierTexteCompteRendu->getIdCt() !== $v) {
			$this->aCahierTexteCompteRendu = null;
		}

		return $this;
	} // setIdCt()

	/**
	 * Set the value of [titre] column.
	 * Titre du document (fichier joint)
	 * @param      string $v new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setTitre($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->titre !== $v) {
			$this->titre = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::TITRE;
		}

		return $this;
	} // setTitre()

	/**
	 * Set the value of [taille] column.
	 * Taille du document (fichier joint)
	 * @param      int $v new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setTaille($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->taille !== $v || $this->isNew()) {
			$this->taille = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::TAILLE;
		}

		return $this;
	} // setTaille()

	/**
	 * Set the value of [emplacement] column.
	 * Chemin du systeme de fichier vers le document (fichier joint)
	 * @param      string $v new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setEmplacement($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->emplacement !== $v) {
			$this->emplacement = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT;
		}

		return $this;
	} // setEmplacement()

	/**
	 * Sets the value of the [visible_eleve_parent] column. 
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * Visibilité élève/parent du document joint
	 * @param      boolean|integer|string $v The new value
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 */
	public function setVisibleEleveParent($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->visible_eleve_parent !== $v || $this->isNew()) {
			$this->visible_eleve_parent = $v;
			$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::VISIBLE_ELEVE_PARENT;
		}

		return $this;
	} // setVisibleEleveParent()

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
			if ($this->id_ct !== 0) {
				return false;
			}

			if ($this->taille !== 0) {
				return false;
			}

			if ($this->visible_eleve_parent !== true) {
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
			$this->id_ct = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->titre = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->taille = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->emplacement = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->visible_eleve_parent = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = CahierTexteCompteRenduFichierJointPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating CahierTexteCompteRenduFichierJoint object", $e);
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

		if ($this->aCahierTexteCompteRendu !== null && $this->id_ct !== $this->aCahierTexteCompteRendu->getIdCt()) {
			$this->aCahierTexteCompteRendu = null;
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
			$con = Propel::getConnection(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CahierTexteCompteRenduFichierJointPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCahierTexteCompteRendu = null;
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
			$con = Propel::getConnection(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CahierTexteCompteRenduFichierJointQuery::create()
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
			$con = Propel::getConnection(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CahierTexteCompteRenduFichierJointPeer::addInstanceToPool($this);
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

			if ($this->aCahierTexteCompteRendu !== null) {
				if ($this->aCahierTexteCompteRendu->isModified() || $this->aCahierTexteCompteRendu->isNew()) {
					$affectedRows += $this->aCahierTexteCompteRendu->save($con);
				}
				$this->setCahierTexteCompteRendu($this->aCahierTexteCompteRendu);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CahierTexteCompteRenduFichierJointPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CahierTexteCompteRenduFichierJointPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CahierTexteCompteRenduFichierJointPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CahierTexteCompteRenduFichierJointPeer::doUpdate($this, $con);
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

			if ($this->aCahierTexteCompteRendu !== null) {
				if (!$this->aCahierTexteCompteRendu->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCahierTexteCompteRendu->getValidationFailures());
				}
			}


			if (($retval = CahierTexteCompteRenduFichierJointPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CahierTexteCompteRenduFichierJointPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdCt();
				break;
			case 2:
				return $this->getTitre();
				break;
			case 3:
				return $this->getTaille();
				break;
			case 4:
				return $this->getEmplacement();
				break;
			case 5:
				return $this->getVisibleEleveParent();
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
		if (isset($alreadyDumpedObjects['CahierTexteCompteRenduFichierJoint'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['CahierTexteCompteRenduFichierJoint'][$this->getPrimaryKey()] = true;
		$keys = CahierTexteCompteRenduFichierJointPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getIdCt(),
			$keys[2] => $this->getTitre(),
			$keys[3] => $this->getTaille(),
			$keys[4] => $this->getEmplacement(),
			$keys[5] => $this->getVisibleEleveParent(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCahierTexteCompteRendu) {
				$result['CahierTexteCompteRendu'] = $this->aCahierTexteCompteRendu->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = CahierTexteCompteRenduFichierJointPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdCt($value);
				break;
			case 2:
				$this->setTitre($value);
				break;
			case 3:
				$this->setTaille($value);
				break;
			case 4:
				$this->setEmplacement($value);
				break;
			case 5:
				$this->setVisibleEleveParent($value);
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
		$keys = CahierTexteCompteRenduFichierJointPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIdCt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setTitre($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTaille($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEmplacement($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setVisibleEleveParent($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME);

		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::ID)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::ID, $this->id);
		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::ID_CT)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::ID_CT, $this->id_ct);
		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::TITRE)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::TITRE, $this->titre);
		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::TAILLE)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::TAILLE, $this->taille);
		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::EMPLACEMENT, $this->emplacement);
		if ($this->isColumnModified(CahierTexteCompteRenduFichierJointPeer::VISIBLE_ELEVE_PARENT)) $criteria->add(CahierTexteCompteRenduFichierJointPeer::VISIBLE_ELEVE_PARENT, $this->visible_eleve_parent);

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
		$criteria = new Criteria(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME);
		$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CahierTexteCompteRenduFichierJoint (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setIdCt($this->getIdCt());
		$copyObj->setTitre($this->getTitre());
		$copyObj->setTaille($this->getTaille());
		$copyObj->setEmplacement($this->getEmplacement());
		$copyObj->setVisibleEleveParent($this->getVisibleEleveParent());
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
	 * @return     CahierTexteCompteRenduFichierJoint Clone of current object.
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
	 * @return     CahierTexteCompteRenduFichierJointPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CahierTexteCompteRenduFichierJointPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CahierTexteCompteRendu object.
	 *
	 * @param      CahierTexteCompteRendu $v
	 * @return     CahierTexteCompteRenduFichierJoint The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCahierTexteCompteRendu(CahierTexteCompteRendu $v = null)
	{
		if ($v === null) {
			$this->setIdCt(0);
		} else {
			$this->setIdCt($v->getIdCt());
		}

		$this->aCahierTexteCompteRendu = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CahierTexteCompteRendu object, it will not be re-added.
		if ($v !== null) {
			$v->addCahierTexteCompteRenduFichierJoint($this);
		}

		return $this;
	}


	/**
	 * Get the associated CahierTexteCompteRendu object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CahierTexteCompteRendu The associated CahierTexteCompteRendu object.
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRendu(PropelPDO $con = null)
	{
		if ($this->aCahierTexteCompteRendu === null && ($this->id_ct !== null)) {
			$this->aCahierTexteCompteRendu = CahierTexteCompteRenduQuery::create()->findPk($this->id_ct, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aCahierTexteCompteRendu->addCahierTexteCompteRenduFichierJoints($this);
			 */
		}
		return $this->aCahierTexteCompteRendu;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->id_ct = null;
		$this->titre = null;
		$this->taille = null;
		$this->emplacement = null;
		$this->visible_eleve_parent = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->applyDefaultValues();
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

		$this->aCahierTexteCompteRendu = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(CahierTexteCompteRenduFichierJointPeer::DEFAULT_STRING_FORMAT);
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

} // BaseCahierTexteCompteRenduFichierJoint
