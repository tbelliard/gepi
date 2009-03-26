<?php

/**
 * Base class that represents a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceSaisie extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceSaisiePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the utilisateur_id field.
	 * @var        string
	 */
	protected $utilisateur_id;

	/**
	 * The value for the eleve_id field.
	 * @var        int
	 */
	protected $eleve_id;

	/**
	 * The value for the created_on field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $created_on;

	/**
	 * The value for the updated_on field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $updated_on;

	/**
	 * The value for the debut_abs field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $debut_abs;

	/**
	 * The value for the fin_abs field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $fin_abs;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        array JTraitementSaisie[] Collection to store aggregation of JTraitementSaisie objects.
	 */
	protected $collJTraitementSaisies;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementSaisies.
	 */
	private $lastJTraitementSaisieCriteria = null;

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
	 * Initializes internal state of BaseAbsenceSaisie object.
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
		$this->created_on = 0;
		$this->updated_on = 0;
		$this->debut_abs = 0;
		$this->fin_abs = 0;
	}

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
	 * Get the [utilisateur_id] column value.
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @return     string
	 */
	public function getUtilisateurId()
	{
		return $this->utilisateur_id;
	}

	/**
	 * Get the [eleve_id] column value.
	 * id_eleve de l'eleve objet de la saisie, egal à 'appel' si aucun eleve n'est saisi
	 * @return     int
	 */
	public function getEleveId()
	{
		return $this->eleve_id;
	}

	/**
	 * Get the [created_on] column value.
	 * Date de la saisie de l'absence en timestamp UNIX
	 * @return     int
	 */
	public function getCreatedOn()
	{
		return $this->created_on;
	}

	/**
	 * Get the [updated_on] column value.
	 * Date de la modification de la saisie en timestamp UNIX
	 * @return     int
	 */
	public function getUpdatedOn()
	{
		return $this->updated_on;
	}

	/**
	 * Get the [debut_abs] column value.
	 * Debut de l'absence en timestamp UNIX
	 * @return     int
	 */
	public function getDebutAbs()
	{
		return $this->debut_abs;
	}

	/**
	 * Get the [fin_abs] column value.
	 * Fin de l'absence en timestamp UNIX
	 * @return     int
	 */
	public function getFinAbs()
	{
		return $this->fin_abs;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @param      string $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::UTILISATEUR_ID;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [eleve_id] column.
	 * id_eleve de l'eleve objet de la saisie, egal à 'appel' si aucun eleve n'est saisi
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setEleveId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eleve_id !== $v) {
			$this->eleve_id = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::ELEVE_ID;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setEleveId()

	/**
	 * Set the value of [created_on] column.
	 * Date de la saisie de l'absence en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setCreatedOn($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->created_on !== $v || $v === 0) {
			$this->created_on = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::CREATED_ON;
		}

		return $this;
	} // setCreatedOn()

	/**
	 * Set the value of [updated_on] column.
	 * Date de la modification de la saisie en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setUpdatedOn($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->updated_on !== $v || $v === 0) {
			$this->updated_on = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::UPDATED_ON;
		}

		return $this;
	} // setUpdatedOn()

	/**
	 * Set the value of [debut_abs] column.
	 * Debut de l'absence en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setDebutAbs($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->debut_abs !== $v || $v === 0) {
			$this->debut_abs = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::DEBUT_ABS;
		}

		return $this;
	} // setDebutAbs()

	/**
	 * Set the value of [fin_abs] column.
	 * Fin de l'absence en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 */
	public function setFinAbs($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->fin_abs !== $v || $v === 0) {
			$this->fin_abs = $v;
			$this->modifiedColumns[] = AbsenceSaisiePeer::FIN_ABS;
		}

		return $this;
	} // setFinAbs()

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
			if (array_diff($this->modifiedColumns, array(AbsenceSaisiePeer::CREATED_ON,AbsenceSaisiePeer::UPDATED_ON,AbsenceSaisiePeer::DEBUT_ABS,AbsenceSaisiePeer::FIN_ABS))) {
				return false;
			}

			if ($this->created_on !== 0) {
				return false;
			}

			if ($this->updated_on !== 0) {
				return false;
			}

			if ($this->debut_abs !== 0) {
				return false;
			}

			if ($this->fin_abs !== 0) {
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
			$this->utilisateur_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->eleve_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->created_on = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->updated_on = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->debut_abs = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->fin_abs = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = AbsenceSaisiePeer::NUM_COLUMNS - AbsenceSaisiePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceSaisie object", $e);
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

		if ($this->aUtilisateurProfessionnel !== null && $this->utilisateur_id !== $this->aUtilisateurProfessionnel->getLogin()) {
			$this->aUtilisateurProfessionnel = null;
		}
		if ($this->aEleve !== null && $this->eleve_id !== $this->aEleve->getIdEleve()) {
			$this->aEleve = null;
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
			$con = Propel::getConnection(AbsenceSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceSaisiePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aUtilisateurProfessionnel = null;
			$this->aEleve = null;
			$this->collJTraitementSaisies = null;
			$this->lastJTraitementSaisieCriteria = null;

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
			$con = Propel::getConnection(AbsenceSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			AbsenceSaisiePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AbsenceSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AbsenceSaisiePeer::addInstanceToPool($this);
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

			if ($this->aUtilisateurProfessionnel !== null) {
				if ($this->aUtilisateurProfessionnel->isModified() || $this->aUtilisateurProfessionnel->isNew()) {
					$affectedRows += $this->aUtilisateurProfessionnel->save($con);
				}
				$this->setUtilisateurProfessionnel($this->aUtilisateurProfessionnel);
			}

			if ($this->aEleve !== null) {
				if ($this->aEleve->isModified() || $this->aEleve->isNew()) {
					$affectedRows += $this->aEleve->save($con);
				}
				$this->setEleve($this->aEleve);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceSaisiePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AbsenceSaisiePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AbsenceSaisiePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJTraitementSaisies !== null) {
				foreach ($this->collJTraitementSaisies as $referrerFK) {
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

			if ($this->aUtilisateurProfessionnel !== null) {
				if (!$this->aUtilisateurProfessionnel->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aUtilisateurProfessionnel->getValidationFailures());
				}
			}

			if ($this->aEleve !== null) {
				if (!$this->aEleve->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEleve->getValidationFailures());
				}
			}


			if (($retval = AbsenceSaisiePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisies !== null) {
					foreach ($this->collJTraitementSaisies as $referrerFK) {
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
		$pos = AbsenceSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getUtilisateurId();
				break;
			case 2:
				return $this->getEleveId();
				break;
			case 3:
				return $this->getCreatedOn();
				break;
			case 4:
				return $this->getUpdatedOn();
				break;
			case 5:
				return $this->getDebutAbs();
				break;
			case 6:
				return $this->getFinAbs();
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
		$keys = AbsenceSaisiePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getEleveId(),
			$keys[3] => $this->getCreatedOn(),
			$keys[4] => $this->getUpdatedOn(),
			$keys[5] => $this->getDebutAbs(),
			$keys[6] => $this->getFinAbs(),
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
		$pos = AbsenceSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setUtilisateurId($value);
				break;
			case 2:
				$this->setEleveId($value);
				break;
			case 3:
				$this->setCreatedOn($value);
				break;
			case 4:
				$this->setUpdatedOn($value);
				break;
			case 5:
				$this->setDebutAbs($value);
				break;
			case 6:
				$this->setFinAbs($value);
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
		$keys = AbsenceSaisiePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEleveId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCreatedOn($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setUpdatedOn($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDebutAbs($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setFinAbs($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceSaisiePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceSaisiePeer::ID)) $criteria->add(AbsenceSaisiePeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceSaisiePeer::UTILISATEUR_ID)) $criteria->add(AbsenceSaisiePeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceSaisiePeer::ELEVE_ID)) $criteria->add(AbsenceSaisiePeer::ELEVE_ID, $this->eleve_id);
		if ($this->isColumnModified(AbsenceSaisiePeer::CREATED_ON)) $criteria->add(AbsenceSaisiePeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(AbsenceSaisiePeer::UPDATED_ON)) $criteria->add(AbsenceSaisiePeer::UPDATED_ON, $this->updated_on);
		if ($this->isColumnModified(AbsenceSaisiePeer::DEBUT_ABS)) $criteria->add(AbsenceSaisiePeer::DEBUT_ABS, $this->debut_abs);
		if ($this->isColumnModified(AbsenceSaisiePeer::FIN_ABS)) $criteria->add(AbsenceSaisiePeer::FIN_ABS, $this->fin_abs);

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
		$criteria = new Criteria(AbsenceSaisiePeer::DATABASE_NAME);

		$criteria->add(AbsenceSaisiePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceSaisie (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setUtilisateurId($this->utilisateur_id);

		$copyObj->setEleveId($this->eleve_id);

		$copyObj->setCreatedOn($this->created_on);

		$copyObj->setUpdatedOn($this->updated_on);

		$copyObj->setDebutAbs($this->debut_abs);

		$copyObj->setFinAbs($this->fin_abs);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisie($relObj->copy($deepCopy));
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
	 * @return     AbsenceSaisie Clone of current object.
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
	 * @return     AbsenceSaisiePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceSaisiePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setUtilisateurProfessionnel(UtilisateurProfessionnel $v = null)
	{
		if ($v === null) {
			$this->setUtilisateurId(NULL);
		} else {
			$this->setUtilisateurId($v->getLogin());
		}

		$this->aUtilisateurProfessionnel = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the UtilisateurProfessionnel object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated UtilisateurProfessionnel object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     UtilisateurProfessionnel The associated UtilisateurProfessionnel object.
	 * @throws     PropelException
	 */
	public function getUtilisateurProfessionnel(PropelPDO $con = null)
	{
		if ($this->aUtilisateurProfessionnel === null && (($this->utilisateur_id !== "" && $this->utilisateur_id !== null))) {
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK($this->utilisateur_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aUtilisateurProfessionnel->addAbsenceSaisies($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     AbsenceSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEleve(Eleve $v = null)
	{
		if ($v === null) {
			$this->setEleveId(NULL);
		} else {
			$this->setEleveId($v->getIdEleve());
		}

		$this->aEleve = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Eleve object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceSaisie($this);
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
		if ($this->aEleve === null && ($this->eleve_id !== null)) {
			$this->aEleve = ElevePeer::retrieveByPK($this->eleve_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aEleve->addAbsenceSaisies($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Clears out the collJTraitementSaisies collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJTraitementSaisies()
	 */
	public function clearJTraitementSaisies()
	{
		$this->collJTraitementSaisies = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJTraitementSaisies collection (array).
	 *
	 * By default this just sets the collJTraitementSaisies collection to an empty array (like clearcollJTraitementSaisies());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJTraitementSaisies()
	{
		$this->collJTraitementSaisies = array();
	}

	/**
	 * Gets an array of JTraitementSaisie objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceSaisie has previously been saved, it will retrieve
	 * related JTraitementSaisies from storage. If this AbsenceSaisie is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JTraitementSaisie[]
	 * @throws     PropelException
	 */
	public function getJTraitementSaisies($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceSaisiePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisies === null) {
			if ($this->isNew()) {
			   $this->collJTraitementSaisies = array();
			} else {

				$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

				JTraitementSaisiePeer::addSelectColumns($criteria);
				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

				JTraitementSaisiePeer::addSelectColumns($criteria);
				if (!isset($this->lastJTraitementSaisieCriteria) || !$this->lastJTraitementSaisieCriteria->equals($criteria)) {
					$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJTraitementSaisieCriteria = $criteria;
		return $this->collJTraitementSaisies;
	}

	/**
	 * Returns the number of related JTraitementSaisie objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JTraitementSaisie objects.
	 * @throws     PropelException
	 */
	public function countJTraitementSaisies(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceSaisiePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJTraitementSaisies === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

				$count = JTraitementSaisiePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

				if (!isset($this->lastJTraitementSaisieCriteria) || !$this->lastJTraitementSaisieCriteria->equals($criteria)) {
					$count = JTraitementSaisiePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJTraitementSaisies);
				}
			} else {
				$count = count($this->collJTraitementSaisies);
			}
		}
		$this->lastJTraitementSaisieCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JTraitementSaisie object to this object
	 * through the JTraitementSaisie foreign key attribute.
	 *
	 * @param      JTraitementSaisie $l JTraitementSaisie
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJTraitementSaisie(JTraitementSaisie $l)
	{
		if ($this->collJTraitementSaisies === null) {
			$this->initJTraitementSaisies();
		}
		if (!in_array($l, $this->collJTraitementSaisies, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJTraitementSaisies, $l);
			$l->setAbsenceSaisie($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceSaisie is new, it will return
	 * an empty collection; or if this AbsenceSaisie has previously
	 * been saved, it will retrieve related JTraitementSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceSaisie.
	 */
	public function getJTraitementSaisiesJoinAbsenceTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceSaisiePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisies === null) {
			if ($this->isNew()) {
				$this->collJTraitementSaisies = array();
			} else {

				$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelectJoinAbsenceTraitement($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementSaisiePeer::A_SAISIE_ID, $this->id);

			if (!isset($this->lastJTraitementSaisieCriteria) || !$this->lastJTraitementSaisieCriteria->equals($criteria)) {
				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelectJoinAbsenceTraitement($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementSaisieCriteria = $criteria;

		return $this->collJTraitementSaisies;
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
			if ($this->collJTraitementSaisies) {
				foreach ((array) $this->collJTraitementSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJTraitementSaisies = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aEleve = null;
	}

} // BaseAbsenceSaisie
