<?php


/**
 * Base class that represents a row from the 'j_matieres_categories_classes' table.
 *
 * Liaison entre categories de matiere et classes
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJCategoriesMatieresClasses extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'JCategoriesMatieresClassesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        JCategoriesMatieresClassesPeer
	 */
	protected static $peer;

	/**
	 * The value for the categorie_id field.
	 * @var        int
	 */
	protected $categorie_id;

	/**
	 * The value for the classe_id field.
	 * @var        int
	 */
	protected $classe_id;

	/**
	 * The value for the affiche_moyenne field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $affiche_moyenne;

	/**
	 * The value for the priority field.
	 * @var        int
	 */
	protected $priority;

	/**
	 * @var        CategorieMatiere
	 */
	protected $aCategorieMatiere;

	/**
	 * @var        Classe
	 */
	protected $aClasse;

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
		$this->affiche_moyenne = false;
	}

	/**
	 * Initializes internal state of BaseJCategoriesMatieresClasses object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [categorie_id] column value.
	 * 
	 * @return     int
	 */
	public function getCategorieId()
	{
		return $this->categorie_id;
	}

	/**
	 * Get the [classe_id] column value.
	 * 
	 * @return     int
	 */
	public function getClasseId()
	{
		return $this->classe_id;
	}

	/**
	 * Get the [affiche_moyenne] column value.
	 * Nom complet
	 * @return     boolean
	 */
	public function getAfficheMoyenne()
	{
		return $this->affiche_moyenne;
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
	 * Set the value of [categorie_id] column.
	 * 
	 * @param      int $v new value
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 */
	public function setCategorieId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->categorie_id !== $v) {
			$this->categorie_id = $v;
			$this->modifiedColumns[] = JCategoriesMatieresClassesPeer::CATEGORIE_ID;
		}

		if ($this->aCategorieMatiere !== null && $this->aCategorieMatiere->getId() !== $v) {
			$this->aCategorieMatiere = null;
		}

		return $this;
	} // setCategorieId()

	/**
	 * Set the value of [classe_id] column.
	 * 
	 * @param      int $v new value
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 */
	public function setClasseId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->classe_id !== $v) {
			$this->classe_id = $v;
			$this->modifiedColumns[] = JCategoriesMatieresClassesPeer::CLASSE_ID;
		}

		if ($this->aClasse !== null && $this->aClasse->getId() !== $v) {
			$this->aClasse = null;
		}

		return $this;
	} // setClasseId()

	/**
	 * Set the value of [affiche_moyenne] column.
	 * Nom complet
	 * @param      boolean $v new value
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 */
	public function setAfficheMoyenne($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->affiche_moyenne !== $v || $this->isNew()) {
			$this->affiche_moyenne = $v;
			$this->modifiedColumns[] = JCategoriesMatieresClassesPeer::AFFICHE_MOYENNE;
		}

		return $this;
	} // setAfficheMoyenne()

	/**
	 * Set the value of [priority] column.
	 * Priorite d'affichage
	 * @param      int $v new value
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 */
	public function setPriority($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priority !== $v) {
			$this->priority = $v;
			$this->modifiedColumns[] = JCategoriesMatieresClassesPeer::PRIORITY;
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
			if ($this->affiche_moyenne !== false) {
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

			$this->categorie_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->classe_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->affiche_moyenne = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
			$this->priority = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 4; // 4 = JCategoriesMatieresClassesPeer::NUM_COLUMNS - JCategoriesMatieresClassesPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating JCategoriesMatieresClasses object", $e);
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

		if ($this->aCategorieMatiere !== null && $this->categorie_id !== $this->aCategorieMatiere->getId()) {
			$this->aCategorieMatiere = null;
		}
		if ($this->aClasse !== null && $this->classe_id !== $this->aClasse->getId()) {
			$this->aClasse = null;
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
			$con = Propel::getConnection(JCategoriesMatieresClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = JCategoriesMatieresClassesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCategorieMatiere = null;
			$this->aClasse = null;
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
			$con = Propel::getConnection(JCategoriesMatieresClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				JCategoriesMatieresClassesQuery::create()
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
			$con = Propel::getConnection(JCategoriesMatieresClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				JCategoriesMatieresClassesPeer::addInstanceToPool($this);
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

			if ($this->aCategorieMatiere !== null) {
				if ($this->aCategorieMatiere->isModified() || $this->aCategorieMatiere->isNew()) {
					$affectedRows += $this->aCategorieMatiere->save($con);
				}
				$this->setCategorieMatiere($this->aCategorieMatiere);
			}

			if ($this->aClasse !== null) {
				if ($this->aClasse->isModified() || $this->aClasse->isNew()) {
					$affectedRows += $this->aClasse->save($con);
				}
				$this->setClasse($this->aClasse);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setNew(false);
				} else {
					$affectedRows += JCategoriesMatieresClassesPeer::doUpdate($this, $con);
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

			if ($this->aCategorieMatiere !== null) {
				if (!$this->aCategorieMatiere->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCategorieMatiere->getValidationFailures());
				}
			}

			if ($this->aClasse !== null) {
				if (!$this->aClasse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aClasse->getValidationFailures());
				}
			}


			if (($retval = JCategoriesMatieresClassesPeer::doValidate($this, $columns)) !== true) {
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
		$pos = JCategoriesMatieresClassesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCategorieId();
				break;
			case 1:
				return $this->getClasseId();
				break;
			case 2:
				return $this->getAfficheMoyenne();
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
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = JCategoriesMatieresClassesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getCategorieId(),
			$keys[1] => $this->getClasseId(),
			$keys[2] => $this->getAfficheMoyenne(),
			$keys[3] => $this->getPriority(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCategorieMatiere) {
				$result['CategorieMatiere'] = $this->aCategorieMatiere->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aClasse) {
				$result['Classe'] = $this->aClasse->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = JCategoriesMatieresClassesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCategorieId($value);
				break;
			case 1:
				$this->setClasseId($value);
				break;
			case 2:
				$this->setAfficheMoyenne($value);
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
		$keys = JCategoriesMatieresClassesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setCategorieId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setClasseId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAfficheMoyenne($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPriority($arr[$keys[3]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(JCategoriesMatieresClassesPeer::DATABASE_NAME);

		if ($this->isColumnModified(JCategoriesMatieresClassesPeer::CATEGORIE_ID)) $criteria->add(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $this->categorie_id);
		if ($this->isColumnModified(JCategoriesMatieresClassesPeer::CLASSE_ID)) $criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->classe_id);
		if ($this->isColumnModified(JCategoriesMatieresClassesPeer::AFFICHE_MOYENNE)) $criteria->add(JCategoriesMatieresClassesPeer::AFFICHE_MOYENNE, $this->affiche_moyenne);
		if ($this->isColumnModified(JCategoriesMatieresClassesPeer::PRIORITY)) $criteria->add(JCategoriesMatieresClassesPeer::PRIORITY, $this->priority);

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
		$criteria = new Criteria(JCategoriesMatieresClassesPeer::DATABASE_NAME);
		$criteria->add(JCategoriesMatieresClassesPeer::CATEGORIE_ID, $this->categorie_id);
		$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->classe_id);

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
		$pks[0] = $this->getCategorieId();
		$pks[1] = $this->getClasseId();

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
		$this->setCategorieId($keys[0]);
		$this->setClasseId($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getCategorieId()) && (null === $this->getClasseId());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of JCategoriesMatieresClasses (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setCategorieId($this->categorie_id);
		$copyObj->setClasseId($this->classe_id);
		$copyObj->setAfficheMoyenne($this->affiche_moyenne);
		$copyObj->setPriority($this->priority);

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
	 * @return     JCategoriesMatieresClasses Clone of current object.
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
	 * @return     JCategoriesMatieresClassesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new JCategoriesMatieresClassesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CategorieMatiere object.
	 *
	 * @param      CategorieMatiere $v
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCategorieMatiere(CategorieMatiere $v = null)
	{
		if ($v === null) {
			$this->setCategorieId(NULL);
		} else {
			$this->setCategorieId($v->getId());
		}

		$this->aCategorieMatiere = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CategorieMatiere object, it will not be re-added.
		if ($v !== null) {
			$v->addJCategoriesMatieresClasses($this);
		}

		return $this;
	}


	/**
	 * Get the associated CategorieMatiere object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CategorieMatiere The associated CategorieMatiere object.
	 * @throws     PropelException
	 */
	public function getCategorieMatiere(PropelPDO $con = null)
	{
		if ($this->aCategorieMatiere === null && ($this->categorie_id !== null)) {
			$this->aCategorieMatiere = CategorieMatiereQuery::create()->findPk($this->categorie_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aCategorieMatiere->addJCategoriesMatieresClassess($this);
			 */
		}
		return $this->aCategorieMatiere;
	}

	/**
	 * Declares an association between this object and a Classe object.
	 *
	 * @param      Classe $v
	 * @return     JCategoriesMatieresClasses The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setClasse(Classe $v = null)
	{
		if ($v === null) {
			$this->setClasseId(NULL);
		} else {
			$this->setClasseId($v->getId());
		}

		$this->aClasse = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Classe object, it will not be re-added.
		if ($v !== null) {
			$v->addJCategoriesMatieresClasses($this);
		}

		return $this;
	}


	/**
	 * Get the associated Classe object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Classe The associated Classe object.
	 * @throws     PropelException
	 */
	public function getClasse(PropelPDO $con = null)
	{
		if ($this->aClasse === null && ($this->classe_id !== null)) {
			$this->aClasse = ClasseQuery::create()->findPk($this->classe_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aClasse->addJCategoriesMatieresClassess($this);
			 */
		}
		return $this->aClasse;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->categorie_id = null;
		$this->classe_id = null;
		$this->affiche_moyenne = null;
		$this->priority = null;
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

		$this->aCategorieMatiere = null;
		$this->aClasse = null;
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

} // BaseJCategoriesMatieresClasses
