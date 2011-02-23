<?php


/**
 * Base class that represents a row from the 'matieres_categories' table.
 *
 * Categories de matiere, utilisees pour regrouper des enseignements
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseCategorieMatiere extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'CategorieMatierePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CategorieMatierePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the nom_court field.
	 * @var        string
	 */
	protected $nom_court;

	/**
	 * The value for the nom_complet field.
	 * @var        string
	 */
	protected $nom_complet;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

	/**
	 * @var        array JGroupesClasses[] Collection to store aggregation of JGroupesClasses objects.
	 */
	protected $collJGroupesClassess;

	/**
	 * @var        array Matiere[] Collection to store aggregation of Matiere objects.
	 */
	protected $collMatieres;

	/**
	 * @var        array JCategoriesMatieresClasses[] Collection to store aggregation of JCategoriesMatieresClasses objects.
	 */
	protected $collJCategoriesMatieresClassess;

	/**
	 * @var        array Classe[] Collection to store aggregation of Classe objects.
	 */
	protected $collClasses;

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
	 * Get the [nom_court] column value.
	 * Nom court
	 * @return     string
	 */
	public function getNomCourt()
	{
		return $this->nom_court;
	}

	/**
	 * Get the [nom_complet] column value.
	 * Nom complet
	 * @return     string
	 */
	public function getNomComplet()
	{
		return $this->nom_complet;
	}

	/**
	 * Get the [priority] column value.
	 * Priorite d'affichage
	 * @return     int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CategorieMatiere The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CategorieMatierePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom_court] column.
	 * Nom court
	 * @param      string $v new value
	 * @return     CategorieMatiere The current object (for fluent API support)
	 */
	public function setNomCourt($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_court !== $v) {
			$this->nom_court = $v;
			$this->modifiedColumns[] = CategorieMatierePeer::NOM_COURT;
		}

		return $this;
	} // setNomCourt()

	/**
	 * Set the value of [nom_complet] column.
	 * Nom complet
	 * @param      string $v new value
	 * @return     CategorieMatiere The current object (for fluent API support)
	 */
	public function setNomComplet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_complet !== $v) {
			$this->nom_complet = $v;
			$this->modifiedColumns[] = CategorieMatierePeer::NOM_COMPLET;
		}

		return $this;
	} // setNomComplet()

	/**
	 * Set the value of [priority] column.
	 * Priorite d'affichage
	 * @param      int $v new value
	 * @return     CategorieMatiere The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = CategorieMatierePeer::PRIORITY;
		}

		return $this;
	} // setPriority()

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
			$this->nom_court = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom_complet = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->priority = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 4; // 4 = CategorieMatierePeer::NUM_COLUMNS - CategorieMatierePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CategorieMatiere object", $e);
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
			$con = Propel::getConnection(CategorieMatierePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CategorieMatierePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collJGroupesClassess = null;

			$this->collMatieres = null;

			$this->collJCategoriesMatieresClassess = null;

			$this->collClasses = null;
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
			$con = Propel::getConnection(CategorieMatierePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CategorieMatiereQuery::create()
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
			$con = Propel::getConnection(CategorieMatierePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CategorieMatierePeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = CategorieMatierePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CategorieMatierePeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CategorieMatierePeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = CategorieMatierePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJGroupesClassess !== null) {
				foreach ($this->collJGroupesClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMatieres !== null) {
				foreach ($this->collMatieres as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJCategoriesMatieresClassess !== null) {
				foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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


			if (($retval = CategorieMatierePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJGroupesClassess !== null) {
					foreach ($this->collJGroupesClassess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMatieres !== null) {
					foreach ($this->collMatieres as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJCategoriesMatieresClassess !== null) {
					foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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
		$pos = CategorieMatierePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNomCourt();
				break;
			case 2:
				return $this->getNomComplet();
				break;
			case 3:
				return $this->getPriority();
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
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = CategorieMatierePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNomCourt(),
			$keys[2] => $this->getNomComplet(),
			$keys[3] => $this->getPriority(),
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
		$pos = CategorieMatierePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNomCourt($value);
				break;
			case 2:
				$this->setNomComplet($value);
				break;
			case 3:
				$this->setPriority($value);
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
		$keys = CategorieMatierePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNomCourt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNomComplet($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPriority($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CategorieMatierePeer::DATABASE_NAME);

		if ($this->isColumnModified(CategorieMatierePeer::ID)) $criteria->add(CategorieMatierePeer::ID, $this->id);
		if ($this->isColumnModified(CategorieMatierePeer::NOM_COURT)) $criteria->add(CategorieMatierePeer::NOM_COURT, $this->nom_court);
		if ($this->isColumnModified(CategorieMatierePeer::NOM_COMPLET)) $criteria->add(CategorieMatierePeer::NOM_COMPLET, $this->nom_complet);
		if ($this->isColumnModified(CategorieMatierePeer::PRIORITY)) $criteria->add(CategorieMatierePeer::PRIORITY, $this->priority);

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
		$criteria = new Criteria(CategorieMatierePeer::DATABASE_NAME);
		$criteria->add(CategorieMatierePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CategorieMatiere (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setNomCourt($this->nom_court);
		$copyObj->setNomComplet($this->nom_complet);
		$copyObj->setPriority($this->priority);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJGroupesClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesClasses($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getMatieres() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addMatiere($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJCategoriesMatieresClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJCategoriesMatieresClasses($relObj->copy($deepCopy));
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
	 * @return     CategorieMatiere Clone of current object.
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
	 * @return     CategorieMatierePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CategorieMatierePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collJGroupesClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		$this->collJGroupesClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesClassess collection.
	 *
	 * By default this just sets the collJGroupesClassess collection to an empty array (like clearcollJGroupesClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJGroupesClassess()
	{
		$this->collJGroupesClassess = new PropelObjectCollection();
		$this->collJGroupesClassess->setModel('JGroupesClasses');
	}

	/**
	 * Gets an array of JGroupesClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CategorieMatiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JGroupesClasses[] List of JGroupesClasses objects
	 * @throws     PropelException
	 */
	public function getJGroupesClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesClassess) {
				// return empty collection
				$this->initJGroupesClassess();
			} else {
				$collJGroupesClassess = JGroupesClassesQuery::create(null, $criteria)
					->filterByCategorieMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collJGroupesClassess;
				}
				$this->collJGroupesClassess = $collJGroupesClassess;
			}
		}
		return $this->collJGroupesClassess;
	}

	/**
	 * Returns the number of related JGroupesClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesClasses objects.
	 * @throws     PropelException
	 */
	public function countJGroupesClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesClassess) {
				return 0;
			} else {
				$query = JGroupesClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategorieMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collJGroupesClassess);
		}
	}

	/**
	 * Method called to associate a JGroupesClasses object to this object
	 * through the JGroupesClasses foreign key attribute.
	 *
	 * @param      JGroupesClasses $l JGroupesClasses
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesClasses(JGroupesClasses $l)
	{
		if ($this->collJGroupesClassess === null) {
			$this->initJGroupesClassess();
		}
		if (!$this->collJGroupesClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJGroupesClassess[]= $l;
			$l->setCategorieMatiere($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CategorieMatiere is new, it will return
	 * an empty collection; or if this CategorieMatiere has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CategorieMatiere.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JGroupesClasses[] List of JGroupesClasses objects
	 */
	public function getJGroupesClassessJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JGroupesClassesQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getJGroupesClassess($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CategorieMatiere is new, it will return
	 * an empty collection; or if this CategorieMatiere has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CategorieMatiere.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JGroupesClasses[] List of JGroupesClasses objects
	 */
	public function getJGroupesClassessJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JGroupesClassesQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getJGroupesClassess($query, $con);
	}

	/**
	 * Clears out the collMatieres collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addMatieres()
	 */
	public function clearMatieres()
	{
		$this->collMatieres = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collMatieres collection.
	 *
	 * By default this just sets the collMatieres collection to an empty array (like clearcollMatieres());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initMatieres()
	{
		$this->collMatieres = new PropelObjectCollection();
		$this->collMatieres->setModel('Matiere');
	}

	/**
	 * Gets an array of Matiere objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CategorieMatiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array Matiere[] List of Matiere objects
	 * @throws     PropelException
	 */
	public function getMatieres($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collMatieres) {
				// return empty collection
				$this->initMatieres();
			} else {
				$collMatieres = MatiereQuery::create(null, $criteria)
					->filterByCategorieMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collMatieres;
				}
				$this->collMatieres = $collMatieres;
			}
		}
		return $this->collMatieres;
	}

	/**
	 * Returns the number of related Matiere objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Matiere objects.
	 * @throws     PropelException
	 */
	public function countMatieres(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collMatieres) {
				return 0;
			} else {
				$query = MatiereQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategorieMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collMatieres);
		}
	}

	/**
	 * Method called to associate a Matiere object to this object
	 * through the Matiere foreign key attribute.
	 *
	 * @param      Matiere $l Matiere
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMatiere(Matiere $l)
	{
		if ($this->collMatieres === null) {
			$this->initMatieres();
		}
		if (!$this->collMatieres->contains($l)) { // only add it if the **same** object is not already associated
			$this->collMatieres[]= $l;
			$l->setCategorieMatiere($this);
		}
	}

	/**
	 * Clears out the collJCategoriesMatieresClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJCategoriesMatieresClassess()
	 */
	public function clearJCategoriesMatieresClassess()
	{
		$this->collJCategoriesMatieresClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJCategoriesMatieresClassess collection.
	 *
	 * By default this just sets the collJCategoriesMatieresClassess collection to an empty array (like clearcollJCategoriesMatieresClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJCategoriesMatieresClassess()
	{
		$this->collJCategoriesMatieresClassess = new PropelObjectCollection();
		$this->collJCategoriesMatieresClassess->setModel('JCategoriesMatieresClasses');
	}

	/**
	 * Gets an array of JCategoriesMatieresClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CategorieMatiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JCategoriesMatieresClasses[] List of JCategoriesMatieresClasses objects
	 * @throws     PropelException
	 */
	public function getJCategoriesMatieresClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJCategoriesMatieresClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJCategoriesMatieresClassess) {
				// return empty collection
				$this->initJCategoriesMatieresClassess();
			} else {
				$collJCategoriesMatieresClassess = JCategoriesMatieresClassesQuery::create(null, $criteria)
					->filterByCategorieMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collJCategoriesMatieresClassess;
				}
				$this->collJCategoriesMatieresClassess = $collJCategoriesMatieresClassess;
			}
		}
		return $this->collJCategoriesMatieresClassess;
	}

	/**
	 * Returns the number of related JCategoriesMatieresClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JCategoriesMatieresClasses objects.
	 * @throws     PropelException
	 */
	public function countJCategoriesMatieresClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJCategoriesMatieresClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJCategoriesMatieresClassess) {
				return 0;
			} else {
				$query = JCategoriesMatieresClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategorieMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collJCategoriesMatieresClassess);
		}
	}

	/**
	 * Method called to associate a JCategoriesMatieresClasses object to this object
	 * through the JCategoriesMatieresClasses foreign key attribute.
	 *
	 * @param      JCategoriesMatieresClasses $l JCategoriesMatieresClasses
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJCategoriesMatieresClasses(JCategoriesMatieresClasses $l)
	{
		if ($this->collJCategoriesMatieresClassess === null) {
			$this->initJCategoriesMatieresClassess();
		}
		if (!$this->collJCategoriesMatieresClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJCategoriesMatieresClassess[]= $l;
			$l->setCategorieMatiere($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CategorieMatiere is new, it will return
	 * an empty collection; or if this CategorieMatiere has previously
	 * been saved, it will retrieve related JCategoriesMatieresClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CategorieMatiere.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JCategoriesMatieresClasses[] List of JCategoriesMatieresClasses objects
	 */
	public function getJCategoriesMatieresClassessJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JCategoriesMatieresClassesQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getJCategoriesMatieresClassess($query, $con);
	}

	/**
	 * Clears out the collClasses collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addClasses()
	 */
	public function clearClasses()
	{
		$this->collClasses = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collClasses collection.
	 *
	 * By default this just sets the collClasses collection to an empty collection (like clearClasses());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initClasses()
	{
		$this->collClasses = new PropelObjectCollection();
		$this->collClasses->setModel('Classe');
	}

	/**
	 * Gets a collection of Classe objects related by a many-to-many relationship
	 * to the current object by way of the j_matieres_categories_classes cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CategorieMatiere is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Classe[] List of Classe objects
	 */
	public function getClasses($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collClasses) {
				// return empty collection
				$this->initClasses();
			} else {
				$collClasses = ClasseQuery::create(null, $criteria)
					->filterByCategorieMatiere($this)
					->find($con);
				if (null !== $criteria) {
					return $collClasses;
				}
				$this->collClasses = $collClasses;
			}
		}
		return $this->collClasses;
	}

	/**
	 * Gets the number of Classe objects related by a many-to-many relationship
	 * to the current object by way of the j_matieres_categories_classes cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Classe objects
	 */
	public function countClasses($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collClasses) {
				return 0;
			} else {
				$query = ClasseQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCategorieMatiere($this)
					->count($con);
			}
		} else {
			return count($this->collClasses);
		}
	}

	/**
	 * Associate a Classe object to this object
	 * through the j_matieres_categories_classes cross reference table.
	 *
	 * @param      Classe $classe The JCategoriesMatieresClasses object to relate
	 * @return     void
	 */
	public function addClasse($classe)
	{
		if ($this->collClasses === null) {
			$this->initClasses();
		}
		if (!$this->collClasses->contains($classe)) { // only add it if the **same** object is not already associated
			$jCategoriesMatieresClasses = new JCategoriesMatieresClasses();
			$jCategoriesMatieresClasses->setClasse($classe);
			$this->addJCategoriesMatieresClasses($jCategoriesMatieresClasses);

			$this->collClasses[]= $classe;
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->nom_court = null;
		$this->nom_complet = null;
		$this->priority = null;
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
			if ($this->collJGroupesClassess) {
				foreach ((array) $this->collJGroupesClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collMatieres) {
				foreach ((array) $this->collMatieres as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJCategoriesMatieresClassess) {
				foreach ((array) $this->collJCategoriesMatieresClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJGroupesClassess = null;
		$this->collMatieres = null;
		$this->collJCategoriesMatieresClassess = null;
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

} // BaseCategorieMatiere
