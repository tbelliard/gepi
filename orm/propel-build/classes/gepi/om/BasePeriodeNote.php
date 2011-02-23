<?php


/**
 * Base class that represents a row from the 'periodes' table.
 *
 * Table regroupant les periodes de notes pour les classes
 *
 * @package    propel.generator.gepi.om
 */
abstract class BasePeriodeNote extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'PeriodeNotePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        PeriodeNotePeer
	 */
	protected static $peer;

	/**
	 * The value for the nom_periode field.
	 * @var        string
	 */
	protected $nom_periode;

	/**
	 * The value for the num_periode field.
	 * @var        int
	 */
	protected $num_periode;

	/**
	 * The value for the verouiller field.
	 * Note: this column has a database default value of: 'O'
	 * @var        string
	 */
	protected $verouiller;

	/**
	 * The value for the id_classe field.
	 * @var        int
	 */
	protected $id_classe;

	/**
	 * The value for the date_verrouillage field.
	 * @var        string
	 */
	protected $date_verrouillage;

	/**
	 * The value for the date_fin field.
	 * @var        string
	 */
	protected $date_fin;

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
		$this->verouiller = 'O';
	}

	/**
	 * Initializes internal state of BasePeriodeNote object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [nom_periode] column value.
	 * Nom de la periode de note
	 * @return     string
	 */
	public function getNomPeriode()
	{
		return $this->nom_periode;
	}

	/**
	 * Get the [num_periode] column value.
	 * identifiant numerique de la periode (1, 2 ou3)
	 * @return     int
	 */
	public function getNumPeriode()
	{
		return $this->num_periode;
	}

	/**
	 * Get the [verouiller] column value.
	 * Verrouillage de la periode : O pour verouillee, N pour non verrouillee, P pour partiel (pied de bulletin)
	 * @return     string
	 */
	public function getVerouiller()
	{
		return $this->verouiller;
	}

	/**
	 * Get the [id_classe] column value.
	 * identifiant numerique de la classe.
	 * @return     int
	 */
	public function getIdClasse()
	{
		return $this->id_classe;
	}

	/**
	 * Get the [optionally formatted] temporal [date_verrouillage] column value.
	 * date de verrouillage de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateVerrouillage($format = 'Y-m-d H:i:s')
	{
		if ($this->date_verrouillage === null) {
			return null;
		}


		if ($this->date_verrouillage === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_verrouillage);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_verrouillage, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [date_fin] column value.
	 * date de verrouillage de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateFin($format = 'Y-m-d H:i:s')
	{
		if ($this->date_fin === null) {
			return null;
		}


		if ($this->date_fin === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_fin);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_fin, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Set the value of [nom_periode] column.
	 * Nom de la periode de note
	 * @param      string $v new value
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setNomPeriode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_periode !== $v) {
			$this->nom_periode = $v;
			$this->modifiedColumns[] = PeriodeNotePeer::NOM_PERIODE;
		}

		return $this;
	} // setNomPeriode()

	/**
	 * Set the value of [num_periode] column.
	 * identifiant numerique de la periode (1, 2 ou3)
	 * @param      int $v new value
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setNumPeriode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->num_periode !== $v) {
			$this->num_periode = $v;
			$this->modifiedColumns[] = PeriodeNotePeer::NUM_PERIODE;
		}

		return $this;
	} // setNumPeriode()

	/**
	 * Set the value of [verouiller] column.
	 * Verrouillage de la periode : O pour verouillee, N pour non verrouillee, P pour partiel (pied de bulletin)
	 * @param      string $v new value
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setVerouiller($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->verouiller !== $v || $this->isNew()) {
			$this->verouiller = $v;
			$this->modifiedColumns[] = PeriodeNotePeer::VEROUILLER;
		}

		return $this;
	} // setVerouiller()

	/**
	 * Set the value of [id_classe] column.
	 * identifiant numerique de la classe.
	 * @param      int $v new value
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setIdClasse($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_classe !== $v) {
			$this->id_classe = $v;
			$this->modifiedColumns[] = PeriodeNotePeer::ID_CLASSE;
		}

		if ($this->aClasse !== null && $this->aClasse->getId() !== $v) {
			$this->aClasse = null;
		}

		return $this;
	} // setIdClasse()

	/**
	 * Sets the value of [date_verrouillage] column to a normalized version of the date/time value specified.
	 * date de verrouillage de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setDateVerrouillage($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->date_verrouillage !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->date_verrouillage !== null && $tmpDt = new DateTime($this->date_verrouillage)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->date_verrouillage = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = PeriodeNotePeer::DATE_VERROUILLAGE;
			}
		} // if either are not null

		return $this;
	} // setDateVerrouillage()

	/**
	 * Sets the value of [date_fin] column to a normalized version of the date/time value specified.
	 * date de verrouillage de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PeriodeNote The current object (for fluent API support)
	 */
	public function setDateFin($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->date_fin !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->date_fin !== null && $tmpDt = new DateTime($this->date_fin)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->date_fin = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = PeriodeNotePeer::DATE_FIN;
			}
		} // if either are not null

		return $this;
	} // setDateFin()

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
			if ($this->verouiller !== 'O') {
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

			$this->nom_periode = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->num_periode = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->verouiller = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->id_classe = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->date_verrouillage = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->date_fin = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = PeriodeNotePeer::NUM_COLUMNS - PeriodeNotePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating PeriodeNote object", $e);
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
			$con = Propel::getConnection(PeriodeNotePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = PeriodeNotePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

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
			$con = Propel::getConnection(PeriodeNotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				PeriodeNoteQuery::create()
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
			$con = Propel::getConnection(PeriodeNotePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				PeriodeNotePeer::addInstanceToPool($this);
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
					$affectedRows += PeriodeNotePeer::doUpdate($this, $con);
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

			if ($this->aClasse !== null) {
				if (!$this->aClasse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aClasse->getValidationFailures());
				}
			}


			if (($retval = PeriodeNotePeer::doValidate($this, $columns)) !== true) {
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
		$pos = PeriodeNotePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNomPeriode();
				break;
			case 1:
				return $this->getNumPeriode();
				break;
			case 2:
				return $this->getVerouiller();
				break;
			case 3:
				return $this->getIdClasse();
				break;
			case 4:
				return $this->getDateVerrouillage();
				break;
			case 5:
				return $this->getDateFin();
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
		$keys = PeriodeNotePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getNomPeriode(),
			$keys[1] => $this->getNumPeriode(),
			$keys[2] => $this->getVerouiller(),
			$keys[3] => $this->getIdClasse(),
			$keys[4] => $this->getDateVerrouillage(),
			$keys[5] => $this->getDateFin(),
		);
		if ($includeForeignObjects) {
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
		$pos = PeriodeNotePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNomPeriode($value);
				break;
			case 1:
				$this->setNumPeriode($value);
				break;
			case 2:
				$this->setVerouiller($value);
				break;
			case 3:
				$this->setIdClasse($value);
				break;
			case 4:
				$this->setDateVerrouillage($value);
				break;
			case 5:
				$this->setDateFin($value);
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
		$keys = PeriodeNotePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setNomPeriode($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNumPeriode($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setVerouiller($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setIdClasse($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDateVerrouillage($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDateFin($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(PeriodeNotePeer::DATABASE_NAME);

		if ($this->isColumnModified(PeriodeNotePeer::NOM_PERIODE)) $criteria->add(PeriodeNotePeer::NOM_PERIODE, $this->nom_periode);
		if ($this->isColumnModified(PeriodeNotePeer::NUM_PERIODE)) $criteria->add(PeriodeNotePeer::NUM_PERIODE, $this->num_periode);
		if ($this->isColumnModified(PeriodeNotePeer::VEROUILLER)) $criteria->add(PeriodeNotePeer::VEROUILLER, $this->verouiller);
		if ($this->isColumnModified(PeriodeNotePeer::ID_CLASSE)) $criteria->add(PeriodeNotePeer::ID_CLASSE, $this->id_classe);
		if ($this->isColumnModified(PeriodeNotePeer::DATE_VERROUILLAGE)) $criteria->add(PeriodeNotePeer::DATE_VERROUILLAGE, $this->date_verrouillage);
		if ($this->isColumnModified(PeriodeNotePeer::DATE_FIN)) $criteria->add(PeriodeNotePeer::DATE_FIN, $this->date_fin);

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
		$criteria = new Criteria(PeriodeNotePeer::DATABASE_NAME);
		$criteria->add(PeriodeNotePeer::NUM_PERIODE, $this->num_periode);
		$criteria->add(PeriodeNotePeer::ID_CLASSE, $this->id_classe);

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
		$pks[0] = $this->getNumPeriode();
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
		$this->setNumPeriode($keys[0]);
		$this->setIdClasse($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getNumPeriode()) && (null === $this->getIdClasse());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of PeriodeNote (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setNomPeriode($this->nom_periode);
		$copyObj->setNumPeriode($this->num_periode);
		$copyObj->setVerouiller($this->verouiller);
		$copyObj->setIdClasse($this->id_classe);
		$copyObj->setDateVerrouillage($this->date_verrouillage);
		$copyObj->setDateFin($this->date_fin);

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
	 * @return     PeriodeNote Clone of current object.
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
	 * @return     PeriodeNotePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PeriodeNotePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Classe object.
	 *
	 * @param      Classe $v
	 * @return     PeriodeNote The current object (for fluent API support)
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
			$v->addPeriodeNote($this);
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
				 $this->aClasse->addPeriodeNotes($this);
			 */
		}
		return $this->aClasse;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->nom_periode = null;
		$this->num_periode = null;
		$this->verouiller = null;
		$this->id_classe = null;
		$this->date_verrouillage = null;
		$this->date_fin = null;
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

} // BasePeriodeNote
