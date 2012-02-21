<?php


/**
 * Base class that represents a row from the 'j_groupes_classes' table.
 *
 * Table permettant la jointure entre groupe d'enseignement et une classe. Cette jointure permet de definir un enseignement, c'est à dire un groupe d'eleves dans une meme classe. Est rarement utilise directement dans le code. Cette jointure permet de definir un coefficient et une valeur ects pour un groupe sur une classe
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseJGroupesClasses extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'JGroupesClassesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        JGroupesClassesPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

	/**
	 * The value for the id_groupe field.
	 * @var        int
	 */
	protected $id_groupe;

	/**
	 * The value for the id_classe field.
	 * @var        int
	 */
	protected $id_classe;

	/**
	 * The value for the priorite field.
	 * @var        int
	 */
	protected $priorite;

	/**
	 * The value for the coef field.
	 * @var        string
	 */
	protected $coef;

	/**
	 * The value for the categorie_id field.
	 * @var        int
	 */
	protected $categorie_id;

	/**
	 * The value for the saisie_ects field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $saisie_ects;

	/**
	 * The value for the valeur_ects field.
	 * @var        string
	 */
	protected $valeur_ects;

	/**
	 * @var        Groupe
	 */
	protected $aGroupe;

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
		$this->saisie_ects = false;
	}

	/**
	 * Initializes internal state of BaseJGroupesClasses object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id_groupe] column value.
	 * Cle primaire du groupe
	 * @return     int
	 */
	public function getIdGroupe()
	{
		return $this->id_groupe;
	}

	/**
	 * Get the [id_classe] column value.
	 * Cle primaire de la classe
	 * @return     int
	 */
	public function getIdClasse()
	{
		return $this->id_classe;
	}

	/**
	 * Get the [priorite] column value.
	 * 
	 * @return     int
	 */
	public function getPriorite()
	{
		return $this->priorite;
	}

	/**
	 * Get the [coef] column value.
	 * 
	 * @return     string
	 */
	public function getCoef()
	{
		return $this->coef;
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
	 * Get the [saisie_ects] column value.
	 * Active ou non la saisie ECTS pour cet enseignement
	 * @return     boolean
	 */
	public function getSaisieEcts()
	{
		return $this->saisie_ects;
	}

	/**
	 * Get the [valeur_ects] column value.
	 * Valeur par défaut des ECTS pour cet enseignement
	 * @return     string
	 */
	public function getValeurEcts()
	{
		return $this->valeur_ects;
	}

	/**
	 * Set the value of [id_groupe] column.
	 * Cle primaire du groupe
	 * @param      int $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::ID_GROUPE;
		}

		if ($this->aGroupe !== null && $this->aGroupe->getId() !== $v) {
			$this->aGroupe = null;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [id_classe] column.
	 * Cle primaire de la classe
	 * @param      int $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setIdClasse($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_classe !== $v) {
			$this->id_classe = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::ID_CLASSE;
		}

		if ($this->aClasse !== null && $this->aClasse->getId() !== $v) {
			$this->aClasse = null;
		}

		return $this;
	} // setIdClasse()

	/**
	 * Set the value of [priorite] column.
	 * 
	 * @param      int $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setPriorite($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->priorite !== $v) {
			$this->priorite = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::PRIORITE;
		}

		return $this;
	} // setPriorite()

	/**
	 * Set the value of [coef] column.
	 * 
	 * @param      string $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setCoef($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->coef !== $v) {
			$this->coef = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::COEF;
		}

		return $this;
	} // setCoef()

	/**
	 * Set the value of [categorie_id] column.
	 * 
	 * @param      int $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setCategorieId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->categorie_id !== $v) {
			$this->categorie_id = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::CATEGORIE_ID;
		}

		return $this;
	} // setCategorieId()

	/**
	 * Sets the value of the [saisie_ects] column.
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * Active ou non la saisie ECTS pour cet enseignement
	 * @param      boolean|integer|string $v The new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setSaisieEcts($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->saisie_ects !== $v) {
			$this->saisie_ects = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::SAISIE_ECTS;
		}

		return $this;
	} // setSaisieEcts()

	/**
	 * Set the value of [valeur_ects] column.
	 * Valeur par défaut des ECTS pour cet enseignement
	 * @param      string $v new value
	 * @return     JGroupesClasses The current object (for fluent API support)
	 */
	public function setValeurEcts($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->valeur_ects !== $v) {
			$this->valeur_ects = $v;
			$this->modifiedColumns[] = JGroupesClassesPeer::VALEUR_ECTS;
		}

		return $this;
	} // setValeurEcts()

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
			if ($this->saisie_ects !== false) {
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

			$this->id_groupe = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->id_classe = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->priorite = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->coef = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->categorie_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->saisie_ects = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
			$this->valeur_ects = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 7; // 7 = JGroupesClassesPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating JGroupesClasses object", $e);
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
		if ($this->aClasse !== null && $this->id_classe !== $this->aClasse->getId()) {
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
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = JGroupesClassesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aGroupe = null;
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
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = JGroupesClassesQuery::create()
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
			$con = Propel::getConnection(JGroupesClassesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				JGroupesClassesPeer::addInstanceToPool($this);
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

			if ($this->aClasse !== null) {
				if ($this->aClasse->isModified() || $this->aClasse->isNew()) {
					$affectedRows += $this->aClasse->save($con);
				}
				$this->setClasse($this->aClasse);
			}

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
		if ($this->isColumnModified(JGroupesClassesPeer::ID_GROUPE)) {
			$modifiedColumns[':p' . $index++]  = 'ID_GROUPE';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::ID_CLASSE)) {
			$modifiedColumns[':p' . $index++]  = 'ID_CLASSE';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::PRIORITE)) {
			$modifiedColumns[':p' . $index++]  = 'PRIORITE';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::COEF)) {
			$modifiedColumns[':p' . $index++]  = 'COEF';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::CATEGORIE_ID)) {
			$modifiedColumns[':p' . $index++]  = 'CATEGORIE_ID';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::SAISIE_ECTS)) {
			$modifiedColumns[':p' . $index++]  = 'SAISIE_ECTS';
		}
		if ($this->isColumnModified(JGroupesClassesPeer::VALEUR_ECTS)) {
			$modifiedColumns[':p' . $index++]  = 'VALEUR_ECTS';
		}

		$sql = sprintf(
			'INSERT INTO j_groupes_classes (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ID_GROUPE':
						$stmt->bindValue($identifier, $this->id_groupe, PDO::PARAM_INT);
						break;
					case 'ID_CLASSE':
						$stmt->bindValue($identifier, $this->id_classe, PDO::PARAM_INT);
						break;
					case 'PRIORITE':
						$stmt->bindValue($identifier, $this->priorite, PDO::PARAM_INT);
						break;
					case 'COEF':
						$stmt->bindValue($identifier, $this->coef, PDO::PARAM_STR);
						break;
					case 'CATEGORIE_ID':
						$stmt->bindValue($identifier, $this->categorie_id, PDO::PARAM_INT);
						break;
					case 'SAISIE_ECTS':
						$stmt->bindValue($identifier, (int) $this->saisie_ects, PDO::PARAM_INT);
						break;
					case 'VALEUR_ECTS':
						$stmt->bindValue($identifier, $this->valeur_ects, PDO::PARAM_STR);
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aGroupe !== null) {
				if (!$this->aGroupe->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroupe->getValidationFailures());
				}
			}

			if ($this->aClasse !== null) {
				if (!$this->aClasse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aClasse->getValidationFailures());
				}
			}


			if (($retval = JGroupesClassesPeer::doValidate($this, $columns)) !== true) {
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
		$pos = JGroupesClassesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdGroupe();
				break;
			case 1:
				return $this->getIdClasse();
				break;
			case 2:
				return $this->getPriorite();
				break;
			case 3:
				return $this->getCoef();
				break;
			case 4:
				return $this->getCategorieId();
				break;
			case 5:
				return $this->getSaisieEcts();
				break;
			case 6:
				return $this->getValeurEcts();
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
		if (isset($alreadyDumpedObjects['JGroupesClasses'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['JGroupesClasses'][serialize($this->getPrimaryKey())] = true;
		$keys = JGroupesClassesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdGroupe(),
			$keys[1] => $this->getIdClasse(),
			$keys[2] => $this->getPriorite(),
			$keys[3] => $this->getCoef(),
			$keys[4] => $this->getCategorieId(),
			$keys[5] => $this->getSaisieEcts(),
			$keys[6] => $this->getValeurEcts(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aGroupe) {
				$result['Groupe'] = $this->aGroupe->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aClasse) {
				$result['Classe'] = $this->aClasse->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = JGroupesClassesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdGroupe($value);
				break;
			case 1:
				$this->setIdClasse($value);
				break;
			case 2:
				$this->setPriorite($value);
				break;
			case 3:
				$this->setCoef($value);
				break;
			case 4:
				$this->setCategorieId($value);
				break;
			case 5:
				$this->setSaisieEcts($value);
				break;
			case 6:
				$this->setValeurEcts($value);
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
		$keys = JGroupesClassesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdGroupe($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setIdClasse($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPriorite($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCoef($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setCategorieId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSaisieEcts($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setValeurEcts($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(JGroupesClassesPeer::DATABASE_NAME);

		if ($this->isColumnModified(JGroupesClassesPeer::ID_GROUPE)) $criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(JGroupesClassesPeer::ID_CLASSE)) $criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id_classe);
		if ($this->isColumnModified(JGroupesClassesPeer::PRIORITE)) $criteria->add(JGroupesClassesPeer::PRIORITE, $this->priorite);
		if ($this->isColumnModified(JGroupesClassesPeer::COEF)) $criteria->add(JGroupesClassesPeer::COEF, $this->coef);
		if ($this->isColumnModified(JGroupesClassesPeer::CATEGORIE_ID)) $criteria->add(JGroupesClassesPeer::CATEGORIE_ID, $this->categorie_id);
		if ($this->isColumnModified(JGroupesClassesPeer::SAISIE_ECTS)) $criteria->add(JGroupesClassesPeer::SAISIE_ECTS, $this->saisie_ects);
		if ($this->isColumnModified(JGroupesClassesPeer::VALEUR_ECTS)) $criteria->add(JGroupesClassesPeer::VALEUR_ECTS, $this->valeur_ects);

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
		$criteria = new Criteria(JGroupesClassesPeer::DATABASE_NAME);
		$criteria->add(JGroupesClassesPeer::ID_GROUPE, $this->id_groupe);
		$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id_classe);

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
		$pks[0] = $this->getIdGroupe();
		$pks[1] = $this->getIdClasse();

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
		$this->setIdGroupe($keys[0]);
		$this->setIdClasse($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getIdGroupe()) && (null === $this->getIdClasse());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of JGroupesClasses (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setIdGroupe($this->getIdGroupe());
		$copyObj->setIdClasse($this->getIdClasse());
		$copyObj->setPriorite($this->getPriorite());
		$copyObj->setCoef($this->getCoef());
		$copyObj->setCategorieId($this->getCategorieId());
		$copyObj->setSaisieEcts($this->getSaisieEcts());
		$copyObj->setValeurEcts($this->getValeurEcts());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
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
	 * @return     JGroupesClasses Clone of current object.
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
	 * @return     JGroupesClassesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new JGroupesClassesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     JGroupesClasses The current object (for fluent API support)
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
			$v->addJGroupesClasses($this);
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
				$this->aGroupe->addJGroupesClassess($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Declares an association between this object and a Classe object.
	 *
	 * @param      Classe $v
	 * @return     JGroupesClasses The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setClasse(Classe $v = null)
	{
		if ($v === null) {
			$this->setIdClasse(NULL);
		} else {
			$this->setIdClasse($v->getId());
		}

		$this->aClasse = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Classe object, it will not be re-added.
		if ($v !== null) {
			$v->addJGroupesClasses($this);
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
		if ($this->aClasse === null && ($this->id_classe !== null)) {
			$this->aClasse = ClasseQuery::create()->findPk($this->id_classe, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aClasse->addJGroupesClassess($this);
			 */
		}
		return $this->aClasse;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id_groupe = null;
		$this->id_classe = null;
		$this->priorite = null;
		$this->coef = null;
		$this->categorie_id = null;
		$this->saisie_ects = null;
		$this->valeur_ects = null;
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

		$this->aGroupe = null;
		$this->aClasse = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(JGroupesClassesPeer::DEFAULT_STRING_FORMAT);
	}

} // BaseJGroupesClasses
