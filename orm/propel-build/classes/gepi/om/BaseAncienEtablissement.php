<?php


/**
 * Base class that represents a row from the 'etablissements' table.
 *
 * Liste des etablissements precedents des eleves
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAncienEtablissement extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AncienEtablissementPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AncienEtablissementPeer
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
	 * The value for the niveau field.
	 * @var        string
	 */
	protected $niveau;

	/**
	 * The value for the type field.
	 * @var        string
	 */
	protected $type;

	/**
	 * The value for the cp field.
	 * @var        int
	 */
	protected $cp;

	/**
	 * The value for the ville field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $ville;

	/**
	 * @var        array JEleveAncienEtablissement[] Collection to store aggregation of JEleveAncienEtablissement objects.
	 */
	protected $collJEleveAncienEtablissements;

	/**
	 * @var        array Eleve[] Collection to store aggregation of Eleve objects.
	 */
	protected $collEleves;

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
		$this->ville = '';
	}

	/**
	 * Initializes internal state of BaseAncienEtablissement object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * cle primaire auto-incrementee
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [nom] column value.
	 * Nom de l'etablissement
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [niveau] column value.
	 * niveau
	 * @return     string
	 */
	public function getNiveau()
	{
		return $this->niveau;
	}

	/**
	 * Get the [type] column value.
	 * type d'etablissement
	 * @return     string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [cp] column value.
	 * code postal de l'etablissement
	 * @return     int
	 */
	public function getCp()
	{
		return $this->cp;
	}

	/**
	 * Get the [ville] column value.
	 * Ville de l'etablissement
	 * @return     string
	 */
	public function getVille()
	{
		return $this->ville;
	}

	/**
	 * Set the value of [id] column.
	 * cle primaire auto-incrementee
	 * @param      int $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom] column.
	 * Nom de l'etablissement
	 * @param      string $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [niveau] column.
	 * niveau
	 * @param      string $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setNiveau($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->niveau !== $v) {
			$this->niveau = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::NIVEAU;
		}

		return $this;
	} // setNiveau()

	/**
	 * Set the value of [type] column.
	 * type d'etablissement
	 * @param      string $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [cp] column.
	 * code postal de l'etablissement
	 * @param      int $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setCp($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->cp !== $v) {
			$this->cp = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::CP;
		}

		return $this;
	} // setCp()

	/**
	 * Set the value of [ville] column.
	 * Ville de l'etablissement
	 * @param      string $v new value
	 * @return     AncienEtablissement The current object (for fluent API support)
	 */
	public function setVille($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ville !== $v || $this->isNew()) {
			$this->ville = $v;
			$this->modifiedColumns[] = AncienEtablissementPeer::VILLE;
		}

		return $this;
	} // setVille()

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
			if ($this->ville !== '') {
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
			$this->niveau = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->cp = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->ville = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = AncienEtablissementPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating AncienEtablissement object", $e);
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
			$con = Propel::getConnection(AncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AncienEtablissementPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collJEleveAncienEtablissements = null;

			$this->collEleves = null;
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
			$con = Propel::getConnection(AncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AncienEtablissementQuery::create()
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
			$con = Propel::getConnection(AncienEtablissementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AncienEtablissementPeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AncienEtablissementPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(AncienEtablissementPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.AncienEtablissementPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = AncienEtablissementPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJEleveAncienEtablissements !== null) {
				foreach ($this->collJEleveAncienEtablissements as $referrerFK) {
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


			if (($retval = AncienEtablissementPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJEleveAncienEtablissements !== null) {
					foreach ($this->collJEleveAncienEtablissements as $referrerFK) {
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
		$pos = AncienEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNiveau();
				break;
			case 3:
				return $this->getType();
				break;
			case 4:
				return $this->getCp();
				break;
			case 5:
				return $this->getVille();
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
		if (isset($alreadyDumpedObjects['AncienEtablissement'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AncienEtablissement'][$this->getPrimaryKey()] = true;
		$keys = AncienEtablissementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getNiveau(),
			$keys[3] => $this->getType(),
			$keys[4] => $this->getCp(),
			$keys[5] => $this->getVille(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collJEleveAncienEtablissements) {
				$result['JEleveAncienEtablissements'] = $this->collJEleveAncienEtablissements->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = AncienEtablissementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNiveau($value);
				break;
			case 3:
				$this->setType($value);
				break;
			case 4:
				$this->setCp($value);
				break;
			case 5:
				$this->setVille($value);
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
		$keys = AncienEtablissementPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNiveau($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCp($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setVille($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AncienEtablissementPeer::DATABASE_NAME);

		if ($this->isColumnModified(AncienEtablissementPeer::ID)) $criteria->add(AncienEtablissementPeer::ID, $this->id);
		if ($this->isColumnModified(AncienEtablissementPeer::NOM)) $criteria->add(AncienEtablissementPeer::NOM, $this->nom);
		if ($this->isColumnModified(AncienEtablissementPeer::NIVEAU)) $criteria->add(AncienEtablissementPeer::NIVEAU, $this->niveau);
		if ($this->isColumnModified(AncienEtablissementPeer::TYPE)) $criteria->add(AncienEtablissementPeer::TYPE, $this->type);
		if ($this->isColumnModified(AncienEtablissementPeer::CP)) $criteria->add(AncienEtablissementPeer::CP, $this->cp);
		if ($this->isColumnModified(AncienEtablissementPeer::VILLE)) $criteria->add(AncienEtablissementPeer::VILLE, $this->ville);

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
		$criteria = new Criteria(AncienEtablissementPeer::DATABASE_NAME);
		$criteria->add(AncienEtablissementPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AncienEtablissement (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNom($this->getNom());
		$copyObj->setNiveau($this->getNiveau());
		$copyObj->setType($this->getType());
		$copyObj->setCp($this->getCp());
		$copyObj->setVille($this->getVille());

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJEleveAncienEtablissements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveAncienEtablissement($relObj->copy($deepCopy));
				}
			}

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
	 * @return     AncienEtablissement Clone of current object.
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
	 * @return     AncienEtablissementPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AncienEtablissementPeer();
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
		if ('JEleveAncienEtablissement' == $relationName) {
			return $this->initJEleveAncienEtablissements();
		}
	}

	/**
	 * Clears out the collJEleveAncienEtablissements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveAncienEtablissements()
	 */
	public function clearJEleveAncienEtablissements()
	{
		$this->collJEleveAncienEtablissements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveAncienEtablissements collection.
	 *
	 * By default this just sets the collJEleveAncienEtablissements collection to an empty array (like clearcollJEleveAncienEtablissements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveAncienEtablissements($overrideExisting = true)
	{
		if (null !== $this->collJEleveAncienEtablissements && !$overrideExisting) {
			return;
		}
		$this->collJEleveAncienEtablissements = new PropelObjectCollection();
		$this->collJEleveAncienEtablissements->setModel('JEleveAncienEtablissement');
	}

	/**
	 * Gets an array of JEleveAncienEtablissement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AncienEtablissement is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveAncienEtablissement[] List of JEleveAncienEtablissement objects
	 * @throws     PropelException
	 */
	public function getJEleveAncienEtablissements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveAncienEtablissements) {
				// return empty collection
				$this->initJEleveAncienEtablissements();
			} else {
				$collJEleveAncienEtablissements = JEleveAncienEtablissementQuery::create(null, $criteria)
					->filterByAncienEtablissement($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveAncienEtablissements;
				}
				$this->collJEleveAncienEtablissements = $collJEleveAncienEtablissements;
			}
		}
		return $this->collJEleveAncienEtablissements;
	}

	/**
	 * Returns the number of related JEleveAncienEtablissement objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveAncienEtablissement objects.
	 * @throws     PropelException
	 */
	public function countJEleveAncienEtablissements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveAncienEtablissements) {
				return 0;
			} else {
				$query = JEleveAncienEtablissementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAncienEtablissement($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveAncienEtablissements);
		}
	}

	/**
	 * Method called to associate a JEleveAncienEtablissement object to this object
	 * through the JEleveAncienEtablissement foreign key attribute.
	 *
	 * @param      JEleveAncienEtablissement $l JEleveAncienEtablissement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveAncienEtablissement(JEleveAncienEtablissement $l)
	{
		if ($this->collJEleveAncienEtablissements === null) {
			$this->initJEleveAncienEtablissements();
		}
		if (!$this->collJEleveAncienEtablissements->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveAncienEtablissements[]= $l;
			$l->setAncienEtablissement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AncienEtablissement is new, it will return
	 * an empty collection; or if this AncienEtablissement has previously
	 * been saved, it will retrieve related JEleveAncienEtablissements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AncienEtablissement.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveAncienEtablissement[] List of JEleveAncienEtablissement objects
	 */
	public function getJEleveAncienEtablissementsJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveAncienEtablissementQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getJEleveAncienEtablissements($query, $con);
	}

	/**
	 * Clears out the collEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEleves()
	 */
	public function clearEleves()
	{
		$this->collEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collEleves collection.
	 *
	 * By default this just sets the collEleves collection to an empty collection (like clearEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initEleves()
	{
		$this->collEleves = new PropelObjectCollection();
		$this->collEleves->setModel('Eleve');
	}

	/**
	 * Gets a collection of Eleve objects related by a many-to-many relationship
	 * to the current object by way of the j_eleves_etablissements cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AncienEtablissement is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Eleve[] List of Eleve objects
	 */
	public function getEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collEleves) {
				// return empty collection
				$this->initEleves();
			} else {
				$collEleves = EleveQuery::create(null, $criteria)
					->filterByAncienEtablissement($this)
					->find($con);
				if (null !== $criteria) {
					return $collEleves;
				}
				$this->collEleves = $collEleves;
			}
		}
		return $this->collEleves;
	}

	/**
	 * Gets the number of Eleve objects related by a many-to-many relationship
	 * to the current object by way of the j_eleves_etablissements cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Eleve objects
	 */
	public function countEleves($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collEleves) {
				return 0;
			} else {
				$query = EleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAncienEtablissement($this)
					->count($con);
			}
		} else {
			return count($this->collEleves);
		}
	}

	/**
	 * Associate a Eleve object to this object
	 * through the j_eleves_etablissements cross reference table.
	 *
	 * @param      Eleve $eleve The JEleveAncienEtablissement object to relate
	 * @return     void
	 */
	public function addEleve($eleve)
	{
		if ($this->collEleves === null) {
			$this->initEleves();
		}
		if (!$this->collEleves->contains($eleve)) { // only add it if the **same** object is not already associated
			$jEleveAncienEtablissement = new JEleveAncienEtablissement();
			$jEleveAncienEtablissement->setEleve($eleve);
			$this->addJEleveAncienEtablissement($jEleveAncienEtablissement);

			$this->collEleves[]= $eleve;
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->nom = null;
		$this->niveau = null;
		$this->type = null;
		$this->cp = null;
		$this->ville = null;
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
			if ($this->collJEleveAncienEtablissements) {
				foreach ($this->collJEleveAncienEtablissements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEleves) {
				foreach ($this->collEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJEleveAncienEtablissements instanceof PropelCollection) {
			$this->collJEleveAncienEtablissements->clearIterator();
		}
		$this->collJEleveAncienEtablissements = null;
		if ($this->collEleves instanceof PropelCollection) {
			$this->collEleves->clearIterator();
		}
		$this->collEleves = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AncienEtablissementPeer::DEFAULT_STRING_FORMAT);
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

} // BaseAncienEtablissement
