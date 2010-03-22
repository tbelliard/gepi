<?php

/**
 * Base class that represents a row from the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceEleveType extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveTypePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the nom field.
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the justification_exigible field.
	 * @var        boolean
	 */
	protected $justification_exigible;

	/**
	 * The value for the responabilite_etablissement field.
	 * @var        boolean
	 */
	protected $responabilite_etablissement;

	/**
	 * The value for the type_saisie field.
	 * @var        string
	 */
	protected $type_saisie;

	/**
	 * The value for the ordre field.
	 * @var        int
	 */
	protected $ordre;

	/**
	 * @var        array AbsenceEleveTypeStatut[] Collection to store aggregation of AbsenceEleveTypeStatut objects.
	 */
	protected $collAbsenceEleveTypeStatuts;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveTypeStatuts.
	 */
	private $lastAbsenceEleveTypeStatutCriteria = null;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collAbsenceEleveTraitements;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveTraitements.
	 */
	private $lastAbsenceEleveTraitementCriteria = null;

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
	 * Initializes internal state of BaseAbsenceEleveType object.
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
	 * Cle primaire auto-incrementee
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [nom] column value.
	 * Nom du type d'absence
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [justification_exigible] column value.
	 * Ce type d'absence doit entrainer une justification de la part de la famille
	 * @return     boolean
	 */
	public function getJustificationExigible()
	{
		return $this->justification_exigible;
	}

	/**
	 * Get the [responabilite_etablissement] column value.
	 * L'eleve est encore sous la responsabilite de l'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l'eleve est encore sous la responsabilité de l'etablissement
	 * @return     boolean
	 */
	public function getResponabiliteEtablissement()
	{
		return $this->responabilite_etablissement;
	}

	/**
	 * Get the [type_saisie] column value.
	 * Enumeration des possibilités de l'interface de saisie de l'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE
	 * @return     string
	 */
	public function getTypeSaisie()
	{
		return $this->type_saisie;
	}

	/**
	 * Get the [ordre] column value.
	 * Ordre d'affichage du type dans la liste déroulante
	 * @return     int
	 */
	public function getOrdre()
	{
		return $this->ordre;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire auto-incrementee
	 * @param      int $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom] column.
	 * Nom du type d'absence
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [justification_exigible] column.
	 * Ce type d'absence doit entrainer une justification de la part de la famille
	 * @param      boolean $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setJustificationExigible($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->justification_exigible !== $v) {
			$this->justification_exigible = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE;
		}

		return $this;
	} // setJustificationExigible()

	/**
	 * Set the value of [responabilite_etablissement] column.
	 * L'eleve est encore sous la responsabilite de l'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l'eleve est encore sous la responsabilité de l'etablissement
	 * @param      boolean $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setResponabiliteEtablissement($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->responabilite_etablissement !== $v) {
			$this->responabilite_etablissement = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::RESPONABILITE_ETABLISSEMENT;
		}

		return $this;
	} // setResponabiliteEtablissement()

	/**
	 * Set the value of [type_saisie] column.
	 * Enumeration des possibilités de l'interface de saisie de l'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setTypeSaisie($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type_saisie !== $v) {
			$this->type_saisie = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::TYPE_SAISIE;
		}

		return $this;
	} // setTypeSaisie()

	/**
	 * Set the value of [ordre] column.
	 * Ordre d'affichage du type dans la liste déroulante
	 * @param      int $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setOrdre($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->ordre !== $v) {
			$this->ordre = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::ORDRE;
		}

		return $this;
	} // setOrdre()

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
			$this->nom = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->justification_exigible = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
			$this->responabilite_etablissement = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
			$this->type_saisie = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->ordre = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 6; // 6 = AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveType object", $e);
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveTypePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collAbsenceEleveTypeStatuts = null;
			$this->lastAbsenceEleveTypeStatutCriteria = null;

			$this->collAbsenceEleveTraitements = null;
			$this->lastAbsenceEleveTraitementCriteria = null;

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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			AbsenceEleveTypePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AbsenceEleveTypePeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = AbsenceEleveTypePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AbsenceEleveTypePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveTypePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAbsenceEleveTypeStatuts !== null) {
				foreach ($this->collAbsenceEleveTypeStatuts as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveTraitements !== null) {
				foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
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


			if (($retval = AbsenceEleveTypePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAbsenceEleveTypeStatuts !== null) {
					foreach ($this->collAbsenceEleveTypeStatuts as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveTraitements !== null) {
					foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
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
		$pos = AbsenceEleveTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNom();
				break;
			case 2:
				return $this->getJustificationExigible();
				break;
			case 3:
				return $this->getResponabiliteEtablissement();
				break;
			case 4:
				return $this->getTypeSaisie();
				break;
			case 5:
				return $this->getOrdre();
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
		$keys = AbsenceEleveTypePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getJustificationExigible(),
			$keys[3] => $this->getResponabiliteEtablissement(),
			$keys[4] => $this->getTypeSaisie(),
			$keys[5] => $this->getOrdre(),
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
		$pos = AbsenceEleveTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNom($value);
				break;
			case 2:
				$this->setJustificationExigible($value);
				break;
			case 3:
				$this->setResponabiliteEtablissement($value);
				break;
			case 4:
				$this->setTypeSaisie($value);
				break;
			case 5:
				$this->setOrdre($value);
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
		$keys = AbsenceEleveTypePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJustificationExigible($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setResponabiliteEtablissement($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setTypeSaisie($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setOrdre($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveTypePeer::ID)) $criteria->add(AbsenceEleveTypePeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveTypePeer::NOM)) $criteria->add(AbsenceEleveTypePeer::NOM, $this->nom);
		if ($this->isColumnModified(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE)) $criteria->add(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE, $this->justification_exigible);
		if ($this->isColumnModified(AbsenceEleveTypePeer::RESPONABILITE_ETABLISSEMENT)) $criteria->add(AbsenceEleveTypePeer::RESPONABILITE_ETABLISSEMENT, $this->responabilite_etablissement);
		if ($this->isColumnModified(AbsenceEleveTypePeer::TYPE_SAISIE)) $criteria->add(AbsenceEleveTypePeer::TYPE_SAISIE, $this->type_saisie);
		if ($this->isColumnModified(AbsenceEleveTypePeer::ORDRE)) $criteria->add(AbsenceEleveTypePeer::ORDRE, $this->ordre);

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
		$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);

		$criteria->add(AbsenceEleveTypePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveType (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setNom($this->nom);

		$copyObj->setJustificationExigible($this->justification_exigible);

		$copyObj->setResponabiliteEtablissement($this->responabilite_etablissement);

		$copyObj->setTypeSaisie($this->type_saisie);

		$copyObj->setOrdre($this->ordre);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getAbsenceEleveTypeStatuts() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTypeStatut($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveTraitements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTraitement($relObj->copy($deepCopy));
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
	 * @return     AbsenceEleveType Clone of current object.
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
	 * @return     AbsenceEleveTypePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveTypePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collAbsenceEleveTypeStatuts collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveTypeStatuts()
	 */
	public function clearAbsenceEleveTypeStatuts()
	{
		$this->collAbsenceEleveTypeStatuts = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveTypeStatuts collection (array).
	 *
	 * By default this just sets the collAbsenceEleveTypeStatuts collection to an empty array (like clearcollAbsenceEleveTypeStatuts());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTypeStatuts()
	{
		$this->collAbsenceEleveTypeStatuts = array();
	}

	/**
	 * Gets an array of AbsenceEleveTypeStatut objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType has previously been saved, it will retrieve
	 * related AbsenceEleveTypeStatuts from storage. If this AbsenceEleveType is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array AbsenceEleveTypeStatut[]
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTypeStatuts($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTypeStatuts === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveTypeStatuts = array();
			} else {

				$criteria->add(AbsenceEleveTypeStatutPeer::ID_A_TYPE, $this->id);

				AbsenceEleveTypeStatutPeer::addSelectColumns($criteria);
				$this->collAbsenceEleveTypeStatuts = AbsenceEleveTypeStatutPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveTypeStatutPeer::ID_A_TYPE, $this->id);

				AbsenceEleveTypeStatutPeer::addSelectColumns($criteria);
				if (!isset($this->lastAbsenceEleveTypeStatutCriteria) || !$this->lastAbsenceEleveTypeStatutCriteria->equals($criteria)) {
					$this->collAbsenceEleveTypeStatuts = AbsenceEleveTypeStatutPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAbsenceEleveTypeStatutCriteria = $criteria;
		return $this->collAbsenceEleveTypeStatuts;
	}

	/**
	 * Returns the number of related AbsenceEleveTypeStatut objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveTypeStatut objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveTypeStatuts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collAbsenceEleveTypeStatuts === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(AbsenceEleveTypeStatutPeer::ID_A_TYPE, $this->id);

				$count = AbsenceEleveTypeStatutPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveTypeStatutPeer::ID_A_TYPE, $this->id);

				if (!isset($this->lastAbsenceEleveTypeStatutCriteria) || !$this->lastAbsenceEleveTypeStatutCriteria->equals($criteria)) {
					$count = AbsenceEleveTypeStatutPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collAbsenceEleveTypeStatuts);
				}
			} else {
				$count = count($this->collAbsenceEleveTypeStatuts);
			}
		}
		$this->lastAbsenceEleveTypeStatutCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a AbsenceEleveTypeStatut object to this object
	 * through the AbsenceEleveTypeStatut foreign key attribute.
	 *
	 * @param      AbsenceEleveTypeStatut $l AbsenceEleveTypeStatut
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveTypeStatut(AbsenceEleveTypeStatut $l)
	{
		if ($this->collAbsenceEleveTypeStatuts === null) {
			$this->initAbsenceEleveTypeStatuts();
		}
		if (!in_array($l, $this->collAbsenceEleveTypeStatuts, true)) { // only add it if the **same** object is not already associated
			array_push($this->collAbsenceEleveTypeStatuts, $l);
			$l->setAbsenceEleveType($this);
		}
	}

	/**
	 * Clears out the collAbsenceEleveTraitements collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveTraitements()
	 */
	public function clearAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveTraitements collection (array).
	 *
	 * By default this just sets the collAbsenceEleveTraitements collection to an empty array (like clearcollAbsenceEleveTraitements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = array();
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType has previously been saved, it will retrieve
	 * related AbsenceEleveTraitements from storage. If this AbsenceEleveType is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array AbsenceEleveTraitement[]
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				AbsenceEleveTraitementPeer::addSelectColumns($criteria);
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				AbsenceEleveTraitementPeer::addSelectColumns($criteria);
				if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
					$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;
		return $this->collAbsenceEleveTraitements;
	}

	/**
	 * Returns the number of related AbsenceEleveTraitement objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveTraitement objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveTraitements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				$count = AbsenceEleveTraitementPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
					$count = AbsenceEleveTraitementPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collAbsenceEleveTraitements);
				}
			} else {
				$count = count($this->collAbsenceEleveTraitements);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a AbsenceEleveTraitement object to this object
	 * through the AbsenceEleveTraitement foreign key attribute.
	 *
	 * @param      AbsenceEleveTraitement $l AbsenceEleveTraitement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collAbsenceEleveTraitements === null) {
			$this->initAbsenceEleveTraitements();
		}
		if (!in_array($l, $this->collAbsenceEleveTraitements, true)) { // only add it if the **same** object is not already associated
			array_push($this->collAbsenceEleveTraitements, $l);
			$l->setAbsenceEleveType($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 */
	public function getAbsenceEleveTraitementsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveMotif($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveMotif($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveMotif($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveJustification($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveJustification($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveJustification($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveAction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveAction($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->id);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveAction($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
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
			if ($this->collAbsenceEleveTypeStatuts) {
				foreach ((array) $this->collAbsenceEleveTypeStatuts as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ((array) $this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collAbsenceEleveTypeStatuts = null;
		$this->collAbsenceEleveTraitements = null;
	}

} // BaseAbsenceEleveType
