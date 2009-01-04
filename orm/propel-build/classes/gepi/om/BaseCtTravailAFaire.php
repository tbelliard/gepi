<?php

/**
 * Base class that represents a row from the 'ct_devoirs_entry' table.
 *
 * Travail Ã  faire (devoir) cahier de texte
 *
 * @package    gepi.om
 */
abstract class BaseCtTravailAFaire extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CtTravailAFairePeer
	 */
	protected static $peer;

	/**
	 * The value for the id_ct field.
	 * @var        int
	 */
	protected $id_ct;

	/**
	 * The value for the date_ct field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $date_ct;

	/**
	 * The value for the contenu field.
	 * @var        string
	 */
	protected $contenu;

	/**
	 * The value for the vise field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $vise;

	/**
	 * The value for the id_groupe field.
	 * @var        int
	 */
	protected $id_groupe;

	/**
	 * The value for the id_login field.
	 * @var        string
	 */
	protected $id_login;

	/**
	 * @var        Groupe
	 */
	protected $aGroupe;

	/**
	 * @var        Utilisateur
	 */
	protected $aUtilisateur;

	/**
	 * @var        array CtDevoirDocument[] Collection to store aggregation of CtDevoirDocument objects.
	 */
	protected $collCtDevoirDocuments;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtDevoirDocuments.
	 */
	private $lastCtDevoirDocumentCriteria = null;

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
	 * Initializes internal state of BaseCtTravailAFaire object.
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
		$this->date_ct = 0;
		$this->vise = 'n';
	}

	/**
	 * Get the [id_ct] column value.
	 * Cle primaire du travail Ã  faire
	 * @return     int
	 */
	public function getIdCt()
	{
		return $this->id_ct;
	}

	/**
	 * Get the [date_ct] column value.
	 * date pour laquelle le travail est a faire
	 * @return     int
	 */
	public function getDateCt()
	{
		return $this->date_ct;
	}

	/**
	 * Get the [contenu] column value.
	 * contenu redactionnel du travail a faire
	 * @return     string
	 */
	public function getContenu()
	{
		return $this->contenu;
	}

	/**
	 * Get the [vise] column value.
	 * vise
	 * @return     string
	 */
	public function getVise()
	{
		return $this->vise;
	}

	/**
	 * Get the [id_groupe] column value.
	 * Cle etrangere du groupe auquel appartient ce travail a faire
	 * @return     int
	 */
	public function getIdGroupe()
	{
		return $this->id_groupe;
	}

	/**
	 * Get the [id_login] column value.
	 * Cle etrangere du l'utilisateur auquel appartient ce travail a faire
	 * @return     string
	 */
	public function getIdLogin()
	{
		return $this->id_login;
	}

	/**
	 * Set the value of [id_ct] column.
	 * Cle primaire du travail Ã  faire
	 * @param      int $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setIdCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_ct !== $v) {
			$this->id_ct = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::ID_CT;
		}

		return $this;
	} // setIdCt()

	/**
	 * Set the value of [date_ct] column.
	 * date pour laquelle le travail est a faire
	 * @param      int $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setDateCt($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->date_ct !== $v || $v === 0) {
			$this->date_ct = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::DATE_CT;
		}

		return $this;
	} // setDateCt()

	/**
	 * Set the value of [contenu] column.
	 * contenu redactionnel du travail a faire
	 * @param      string $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setContenu($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->contenu !== $v) {
			$this->contenu = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::CONTENU;
		}

		return $this;
	} // setContenu()

	/**
	 * Set the value of [vise] column.
	 * vise
	 * @param      string $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setVise($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->vise !== $v || $v === 'n') {
			$this->vise = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::VISE;
		}

		return $this;
	} // setVise()

	/**
	 * Set the value of [id_groupe] column.
	 * Cle etrangere du groupe auquel appartient ce travail a faire
	 * @param      int $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::ID_GROUPE;
		}

		if ($this->aGroupe !== null && $this->aGroupe->getId() !== $v) {
			$this->aGroupe = null;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [id_login] column.
	 * Cle etrangere du l'utilisateur auquel appartient ce travail a faire
	 * @param      string $v new value
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 */
	public function setIdLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id_login !== $v) {
			$this->id_login = $v;
			$this->modifiedColumns[] = CtTravailAFairePeer::ID_LOGIN;
		}

		if ($this->aUtilisateur !== null && $this->aUtilisateur->getLogin() !== $v) {
			$this->aUtilisateur = null;
		}

		return $this;
	} // setIdLogin()

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
			if (array_diff($this->modifiedColumns, array(CtTravailAFairePeer::DATE_CT,CtTravailAFairePeer::VISE))) {
				return false;
			}

			if ($this->date_ct !== 0) {
				return false;
			}

			if ($this->vise !== 'n') {
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

			$this->id_ct = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->date_ct = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->contenu = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->vise = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->id_groupe = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->id_login = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = CtTravailAFairePeer::NUM_COLUMNS - CtTravailAFairePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CtTravailAFaire object", $e);
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

		if ($this->aGroupe !== null && $this->id_groupe !== $this->aGroupe->getId()) {
			$this->aGroupe = null;
		}
		if ($this->aUtilisateur !== null && $this->id_login !== $this->aUtilisateur->getLogin()) {
			$this->aUtilisateur = null;
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
			$con = Propel::getConnection(CtTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CtTravailAFairePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aGroupe = null;
			$this->aUtilisateur = null;
			$this->collCtDevoirDocuments = null;
			$this->lastCtDevoirDocumentCriteria = null;

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
			$con = Propel::getConnection(CtTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			CtTravailAFairePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(CtTravailAFairePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			CtTravailAFairePeer::addInstanceToPool($this);
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

			if ($this->aGroupe !== null) {
				if ($this->aGroupe->isModified() || $this->aGroupe->isNew()) {
					$affectedRows += $this->aGroupe->save($con);
				}
				$this->setGroupe($this->aGroupe);
			}

			if ($this->aUtilisateur !== null) {
				if ($this->aUtilisateur->isModified() || $this->aUtilisateur->isNew()) {
					$affectedRows += $this->aUtilisateur->save($con);
				}
				$this->setUtilisateur($this->aUtilisateur);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CtTravailAFairePeer::ID_CT;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CtTravailAFairePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setIdCt($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CtTravailAFairePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCtDevoirDocuments !== null) {
				foreach ($this->collCtDevoirDocuments as $referrerFK) {
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aGroupe !== null) {
				if (!$this->aGroupe->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroupe->getValidationFailures());
				}
			}

			if ($this->aUtilisateur !== null) {
				if (!$this->aUtilisateur->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUtilisateur->getValidationFailures());
				}
			}


			if (($retval = CtTravailAFairePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCtDevoirDocuments !== null) {
					foreach ($this->collCtDevoirDocuments as $referrerFK) {
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
		$pos = CtTravailAFairePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdCt();
				break;
			case 1:
				return $this->getDateCt();
				break;
			case 2:
				return $this->getContenu();
				break;
			case 3:
				return $this->getVise();
				break;
			case 4:
				return $this->getIdGroupe();
				break;
			case 5:
				return $this->getIdLogin();
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
		$keys = CtTravailAFairePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdCt(),
			$keys[1] => $this->getDateCt(),
			$keys[2] => $this->getContenu(),
			$keys[3] => $this->getVise(),
			$keys[4] => $this->getIdGroupe(),
			$keys[5] => $this->getIdLogin(),
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
		$pos = CtTravailAFairePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdCt($value);
				break;
			case 1:
				$this->setDateCt($value);
				break;
			case 2:
				$this->setContenu($value);
				break;
			case 3:
				$this->setVise($value);
				break;
			case 4:
				$this->setIdGroupe($value);
				break;
			case 5:
				$this->setIdLogin($value);
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
		$keys = CtTravailAFairePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdCt($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDateCt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContenu($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setVise($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setIdGroupe($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setIdLogin($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);

		if ($this->isColumnModified(CtTravailAFairePeer::ID_CT)) $criteria->add(CtTravailAFairePeer::ID_CT, $this->id_ct);
		if ($this->isColumnModified(CtTravailAFairePeer::DATE_CT)) $criteria->add(CtTravailAFairePeer::DATE_CT, $this->date_ct);
		if ($this->isColumnModified(CtTravailAFairePeer::CONTENU)) $criteria->add(CtTravailAFairePeer::CONTENU, $this->contenu);
		if ($this->isColumnModified(CtTravailAFairePeer::VISE)) $criteria->add(CtTravailAFairePeer::VISE, $this->vise);
		if ($this->isColumnModified(CtTravailAFairePeer::ID_GROUPE)) $criteria->add(CtTravailAFairePeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(CtTravailAFairePeer::ID_LOGIN)) $criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->id_login);

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
		$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);

		$criteria->add(CtTravailAFairePeer::ID_CT, $this->id_ct);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdCt();
	}

	/**
	 * Generic method to set the primary key (id_ct column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdCt($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CtTravailAFaire (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setDateCt($this->date_ct);

		$copyObj->setContenu($this->contenu);

		$copyObj->setVise($this->vise);

		$copyObj->setIdGroupe($this->id_groupe);

		$copyObj->setIdLogin($this->id_login);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCtDevoirDocuments() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCtDevoirDocument($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setIdCt(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     CtTravailAFaire Clone of current object.
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
	 * @return     CtTravailAFairePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CtTravailAFairePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     CtTravailAFaire The current object (for fluent API support)
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
			$v->addCtTravailAFaire($this);
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
			$this->aGroupe = GroupePeer::retrieveByPK($this->id_groupe, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aGroupe->addCtTravailAFaires($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Declares an association between this object and a Utilisateur object.
	 *
	 * @param      Utilisateur $v
	 * @return     CtTravailAFaire The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setUtilisateur(Utilisateur $v = null)
	{
		if ($v === null) {
			$this->setIdLogin(NULL);
		} else {
			$this->setIdLogin($v->getLogin());
		}

		$this->aUtilisateur = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Utilisateur object, it will not be re-added.
		if ($v !== null) {
			$v->addCtTravailAFaire($this);
		}

		return $this;
	}


	/**
	 * Get the associated Utilisateur object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Utilisateur The associated Utilisateur object.
	 * @throws     PropelException
	 */
	public function getUtilisateur(PropelPDO $con = null)
	{
		if ($this->aUtilisateur === null && (($this->id_login !== "" && $this->id_login !== null))) {
			$this->aUtilisateur = UtilisateurPeer::retrieveByPK($this->id_login, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aUtilisateur->addCtTravailAFaires($this);
			 */
		}
		return $this->aUtilisateur;
	}

	/**
	 * Clears out the collCtDevoirDocuments collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtDevoirDocuments()
	 */
	public function clearCtDevoirDocuments()
	{
		$this->collCtDevoirDocuments = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCtDevoirDocuments collection (array).
	 *
	 * By default this just sets the collCtDevoirDocuments collection to an empty array (like clearcollCtDevoirDocuments());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCtDevoirDocuments()
	{
		$this->collCtDevoirDocuments = array();
	}

	/**
	 * Gets an array of CtDevoirDocument objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this CtTravailAFaire has previously been saved, it will retrieve
	 * related CtDevoirDocuments from storage. If this CtTravailAFaire is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtDevoirDocument[]
	 * @throws     PropelException
	 */
	public function getCtDevoirDocuments($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtDevoirDocuments === null) {
			if ($this->isNew()) {
			   $this->collCtDevoirDocuments = array();
			} else {

				$criteria->add(CtDevoirDocumentPeer::ID_CT_DEVOIR, $this->id_ct);

				CtDevoirDocumentPeer::addSelectColumns($criteria);
				$this->collCtDevoirDocuments = CtDevoirDocumentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CtDevoirDocumentPeer::ID_CT_DEVOIR, $this->id_ct);

				CtDevoirDocumentPeer::addSelectColumns($criteria);
				if (!isset($this->lastCtDevoirDocumentCriteria) || !$this->lastCtDevoirDocumentCriteria->equals($criteria)) {
					$this->collCtDevoirDocuments = CtDevoirDocumentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCtDevoirDocumentCriteria = $criteria;
		return $this->collCtDevoirDocuments;
	}

	/**
	 * Returns the number of related CtDevoirDocument objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CtDevoirDocument objects.
	 * @throws     PropelException
	 */
	public function countCtDevoirDocuments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(CtTravailAFairePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCtDevoirDocuments === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CtDevoirDocumentPeer::ID_CT_DEVOIR, $this->id_ct);

				$count = CtDevoirDocumentPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CtDevoirDocumentPeer::ID_CT_DEVOIR, $this->id_ct);

				if (!isset($this->lastCtDevoirDocumentCriteria) || !$this->lastCtDevoirDocumentCriteria->equals($criteria)) {
					$count = CtDevoirDocumentPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCtDevoirDocuments);
				}
			} else {
				$count = count($this->collCtDevoirDocuments);
			}
		}
		$this->lastCtDevoirDocumentCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CtDevoirDocument object to this object
	 * through the CtDevoirDocument foreign key attribute.
	 *
	 * @param      CtDevoirDocument $l CtDevoirDocument
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCtDevoirDocument(CtDevoirDocument $l)
	{
		if ($this->collCtDevoirDocuments === null) {
			$this->initCtDevoirDocuments();
		}
		if (!in_array($l, $this->collCtDevoirDocuments, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCtDevoirDocuments, $l);
			$l->setCtTravailAFaire($this);
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
			if ($this->collCtDevoirDocuments) {
				foreach ((array) $this->collCtDevoirDocuments as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCtDevoirDocuments = null;
			$this->aGroupe = null;
			$this->aUtilisateur = null;
	}

} // BaseCtTravailAFaire
