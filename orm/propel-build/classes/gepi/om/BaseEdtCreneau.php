<?php

/**
 * Base class that represents a row from the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * @package    gepi.om
 */
abstract class BaseEdtCreneau extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EdtCreneauPeer
	 */
	protected static $peer;

	/**
	 * The value for the id_definie_periode field.
	 * @var        int
	 */
	protected $id_definie_periode;

	/**
	 * The value for the nom_definie_periode field.
	 * @var        string
	 */
	protected $nom_definie_periode;

	/**
	 * The value for the heuredebut_definie_periode field.
	 * @var        string
	 */
	protected $heuredebut_definie_periode;

	/**
	 * The value for the heurefin_definie_periode field.
	 * @var        string
	 */
	protected $heurefin_definie_periode;

	/**
	 * The value for the suivi_definie_periode field.
	 * Note: this column has a database default value of: 9
	 * @var        int
	 */
	protected $suivi_definie_periode;

	/**
	 * The value for the type_creneau field.
	 * Note: this column has a database default value of: 'cours'
	 * @var        string
	 */
	protected $type_creneau;

	/**
	 * The value for the jour_creneau field.
	 * Note: this column has a database default value of: 'NULL'
	 * @var        string
	 */
	protected $jour_creneau;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveSaisies.
	 */
	private $lastAbsenceEleveSaisieCriteria = null;

	/**
	 * @var        array EdtEmplacementCours[] Collection to store aggregation of EdtEmplacementCours objects.
	 */
	protected $collEdtEmplacementCourss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collEdtEmplacementCourss.
	 */
	private $lastEdtEmplacementCoursCriteria = null;

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
	 * Initializes internal state of BaseEdtCreneau object.
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
		$this->suivi_definie_periode = 9;
		$this->type_creneau = 'cours';
		$this->jour_creneau = 'NULL';
	}

	/**
	 * Get the [id_definie_periode] column value.
	 * cle primaire auto-incremente
	 * @return     int
	 */
	public function getIdDefiniePeriode()
	{
		return $this->id_definie_periode;
	}

	/**
	 * Get the [nom_definie_periode] column value.
	 * Nom du creneau - typiquement, M1, M2, R (pour repas), P (pour récréation), S1, S2 etc
	 * @return     string
	 */
	public function getNomDefiniePeriode()
	{
		return $this->nom_definie_periode;
	}

	/**
	 * Get the [optionally formatted] temporal [heuredebut_definie_periode] column value.
	 * Heure de debut du creneau
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getHeuredebutDefiniePeriode($format = '%X')
	{
		if ($this->heuredebut_definie_periode === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->heuredebut_definie_periode);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->heuredebut_definie_periode, true), $x);
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
	 * Get the [optionally formatted] temporal [heurefin_definie_periode] column value.
	 * Heure de fin du creneau
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getHeurefinDefiniePeriode($format = '%X')
	{
		if ($this->heurefin_definie_periode === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->heurefin_definie_periode);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->heurefin_definie_periode, true), $x);
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
	 * Get the [suivi_definie_periode] column value.
	 * champ inutilise
	 * @return     int
	 */
	public function getSuiviDefiniePeriode()
	{
		return $this->suivi_definie_periode;
	}

	/**
	 * Get the [type_creneau] column value.
	 * types possibles : cours, pause, repas, vie_scolaire
	 * @return     string
	 */
	public function getTypeCreneau()
	{
		return $this->type_creneau;
	}

	/**
	 * Get the [jour_creneau] column value.
	 * Par defaut, aucun jour en particulier mais on peut imposer que des creneaux soient specifiques a un jour en particulier : 'lundi', 'mardi', 'mercredi'...
	 * @return     string
	 */
	public function getJourCreneau()
	{
		return $this->jour_creneau;
	}

	/**
	 * Set the value of [id_definie_periode] column.
	 * cle primaire auto-incremente
	 * @param      int $v new value
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setIdDefiniePeriode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_definie_periode !== $v) {
			$this->id_definie_periode = $v;
			$this->modifiedColumns[] = EdtCreneauPeer::ID_DEFINIE_PERIODE;
		}

		return $this;
	} // setIdDefiniePeriode()

	/**
	 * Set the value of [nom_definie_periode] column.
	 * Nom du creneau - typiquement, M1, M2, R (pour repas), P (pour récréation), S1, S2 etc
	 * @param      string $v new value
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setNomDefiniePeriode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_definie_periode !== $v) {
			$this->nom_definie_periode = $v;
			$this->modifiedColumns[] = EdtCreneauPeer::NOM_DEFINIE_PERIODE;
		}

		return $this;
	} // setNomDefiniePeriode()

	/**
	 * Sets the value of [heuredebut_definie_periode] column to a normalized version of the date/time value specified.
	 * Heure de debut du creneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setHeuredebutDefiniePeriode($v)
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

		if ( $this->heuredebut_definie_periode !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->heuredebut_definie_periode !== null && $tmpDt = new DateTime($this->heuredebut_definie_periode)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->heuredebut_definie_periode = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE;
			}
		} // if either are not null

		return $this;
	} // setHeuredebutDefiniePeriode()

	/**
	 * Sets the value of [heurefin_definie_periode] column to a normalized version of the date/time value specified.
	 * Heure de fin du creneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setHeurefinDefiniePeriode($v)
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

		if ( $this->heurefin_definie_periode !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->heurefin_definie_periode !== null && $tmpDt = new DateTime($this->heurefin_definie_periode)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->heurefin_definie_periode = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE;
			}
		} // if either are not null

		return $this;
	} // setHeurefinDefiniePeriode()

	/**
	 * Set the value of [suivi_definie_periode] column.
	 * champ inutilise
	 * @param      int $v new value
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setSuiviDefiniePeriode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->suivi_definie_periode !== $v || $v === 9) {
			$this->suivi_definie_periode = $v;
			$this->modifiedColumns[] = EdtCreneauPeer::SUIVI_DEFINIE_PERIODE;
		}

		return $this;
	} // setSuiviDefiniePeriode()

	/**
	 * Set the value of [type_creneau] column.
	 * types possibles : cours, pause, repas, vie_scolaire
	 * @param      string $v new value
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setTypeCreneau($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type_creneau !== $v || $v === 'cours') {
			$this->type_creneau = $v;
			$this->modifiedColumns[] = EdtCreneauPeer::TYPE_CRENEAU;
		}

		return $this;
	} // setTypeCreneau()

	/**
	 * Set the value of [jour_creneau] column.
	 * Par defaut, aucun jour en particulier mais on peut imposer que des creneaux soient specifiques a un jour en particulier : 'lundi', 'mardi', 'mercredi'...
	 * @param      string $v new value
	 * @return     EdtCreneau The current object (for fluent API support)
	 */
	public function setJourCreneau($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->jour_creneau !== $v || $v === 'NULL') {
			$this->jour_creneau = $v;
			$this->modifiedColumns[] = EdtCreneauPeer::JOUR_CRENEAU;
		}

		return $this;
	} // setJourCreneau()

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
			if (array_diff($this->modifiedColumns, array(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE,EdtCreneauPeer::TYPE_CRENEAU,EdtCreneauPeer::JOUR_CRENEAU))) {
				return false;
			}

			if ($this->suivi_definie_periode !== 9) {
				return false;
			}

			if ($this->type_creneau !== 'cours') {
				return false;
			}

			if ($this->jour_creneau !== 'NULL') {
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

			$this->id_definie_periode = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->nom_definie_periode = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->heuredebut_definie_periode = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->heurefin_definie_periode = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->suivi_definie_periode = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->type_creneau = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->jour_creneau = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = EdtCreneauPeer::NUM_COLUMNS - EdtCreneauPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EdtCreneau object", $e);
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
			$con = Propel::getConnection(EdtCreneauPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = EdtCreneauPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collAbsenceEleveSaisies = null;
			$this->lastAbsenceEleveSaisieCriteria = null;

			$this->collEdtEmplacementCourss = null;
			$this->lastEdtEmplacementCoursCriteria = null;

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
			$con = Propel::getConnection(EdtCreneauPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			EdtCreneauPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(EdtCreneauPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			EdtCreneauPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = EdtCreneauPeer::ID_DEFINIE_PERIODE;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = EdtCreneauPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setIdDefiniePeriode($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += EdtCreneauPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAbsenceEleveSaisies !== null) {
				foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collEdtEmplacementCourss !== null) {
				foreach ($this->collEdtEmplacementCourss as $referrerFK) {
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


			if (($retval = EdtCreneauPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAbsenceEleveSaisies !== null) {
					foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collEdtEmplacementCourss !== null) {
					foreach ($this->collEdtEmplacementCourss as $referrerFK) {
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
		$pos = EdtCreneauPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdDefiniePeriode();
				break;
			case 1:
				return $this->getNomDefiniePeriode();
				break;
			case 2:
				return $this->getHeuredebutDefiniePeriode();
				break;
			case 3:
				return $this->getHeurefinDefiniePeriode();
				break;
			case 4:
				return $this->getSuiviDefiniePeriode();
				break;
			case 5:
				return $this->getTypeCreneau();
				break;
			case 6:
				return $this->getJourCreneau();
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
		$keys = EdtCreneauPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdDefiniePeriode(),
			$keys[1] => $this->getNomDefiniePeriode(),
			$keys[2] => $this->getHeuredebutDefiniePeriode(),
			$keys[3] => $this->getHeurefinDefiniePeriode(),
			$keys[4] => $this->getSuiviDefiniePeriode(),
			$keys[5] => $this->getTypeCreneau(),
			$keys[6] => $this->getJourCreneau(),
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
		$pos = EdtCreneauPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdDefiniePeriode($value);
				break;
			case 1:
				$this->setNomDefiniePeriode($value);
				break;
			case 2:
				$this->setHeuredebutDefiniePeriode($value);
				break;
			case 3:
				$this->setHeurefinDefiniePeriode($value);
				break;
			case 4:
				$this->setSuiviDefiniePeriode($value);
				break;
			case 5:
				$this->setTypeCreneau($value);
				break;
			case 6:
				$this->setJourCreneau($value);
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
		$keys = EdtCreneauPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdDefiniePeriode($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNomDefiniePeriode($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setHeuredebutDefiniePeriode($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setHeurefinDefiniePeriode($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSuiviDefiniePeriode($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTypeCreneau($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setJourCreneau($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);

		if ($this->isColumnModified(EdtCreneauPeer::ID_DEFINIE_PERIODE)) $criteria->add(EdtCreneauPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);
		if ($this->isColumnModified(EdtCreneauPeer::NOM_DEFINIE_PERIODE)) $criteria->add(EdtCreneauPeer::NOM_DEFINIE_PERIODE, $this->nom_definie_periode);
		if ($this->isColumnModified(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE)) $criteria->add(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE, $this->heuredebut_definie_periode);
		if ($this->isColumnModified(EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE)) $criteria->add(EdtCreneauPeer::HEUREFIN_DEFINIE_PERIODE, $this->heurefin_definie_periode);
		if ($this->isColumnModified(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE)) $criteria->add(EdtCreneauPeer::SUIVI_DEFINIE_PERIODE, $this->suivi_definie_periode);
		if ($this->isColumnModified(EdtCreneauPeer::TYPE_CRENEAU)) $criteria->add(EdtCreneauPeer::TYPE_CRENEAU, $this->type_creneau);
		if ($this->isColumnModified(EdtCreneauPeer::JOUR_CRENEAU)) $criteria->add(EdtCreneauPeer::JOUR_CRENEAU, $this->jour_creneau);

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
		$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);

		$criteria->add(EdtCreneauPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdDefiniePeriode();
	}

	/**
	 * Generic method to set the primary key (id_definie_periode column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdDefiniePeriode($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of EdtCreneau (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setNomDefiniePeriode($this->nom_definie_periode);

		$copyObj->setHeuredebutDefiniePeriode($this->heuredebut_definie_periode);

		$copyObj->setHeurefinDefiniePeriode($this->heurefin_definie_periode);

		$copyObj->setSuiviDefiniePeriode($this->suivi_definie_periode);

		$copyObj->setTypeCreneau($this->type_creneau);

		$copyObj->setJourCreneau($this->jour_creneau);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisie($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getEdtEmplacementCourss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addEdtEmplacementCours($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setIdDefiniePeriode(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     EdtCreneau Clone of current object.
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
	 * @return     EdtCreneauPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EdtCreneauPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collAbsenceEleveSaisies collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveSaisies()
	 */
	public function clearAbsenceEleveSaisies()
	{
		$this->collAbsenceEleveSaisies = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveSaisies collection (array).
	 *
	 * By default this just sets the collAbsenceEleveSaisies collection to an empty array (like clearcollAbsenceEleveSaisies());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveSaisies()
	{
		$this->collAbsenceEleveSaisies = array();
	}

	/**
	 * Gets an array of AbsenceEleveSaisie objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this EdtCreneau has previously been saved, it will retrieve
	 * related AbsenceEleveSaisies from storage. If this EdtCreneau is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array AbsenceEleveSaisie[]
	 * @throws     PropelException
	 */
	public function getAbsenceEleveSaisies($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				AbsenceEleveSaisiePeer::addSelectColumns($criteria);
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				AbsenceEleveSaisiePeer::addSelectColumns($criteria);
				if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
					$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;
		return $this->collAbsenceEleveSaisies;
	}

	/**
	 * Returns the number of related AbsenceEleveSaisie objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveSaisie objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveSaisies(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				$count = AbsenceEleveSaisiePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
					$count = AbsenceEleveSaisiePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collAbsenceEleveSaisies);
				}
			} else {
				$count = count($this->collAbsenceEleveSaisies);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a AbsenceEleveSaisie object to this object
	 * through the AbsenceEleveSaisie foreign key attribute.
	 *
	 * @param      AbsenceEleveSaisie $l AbsenceEleveSaisie
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveSaisie(AbsenceEleveSaisie $l)
	{
		if ($this->collAbsenceEleveSaisies === null) {
			$this->initAbsenceEleveSaisies();
		}
		if (!in_array($l, $this->collAbsenceEleveSaisies, true)) { // only add it if the **same** object is not already associated
			array_push($this->collAbsenceEleveSaisies, $l);
			$l->setEdtCreneau($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getAbsenceEleveSaisiesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

			if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;

		return $this->collAbsenceEleveSaisies;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getAbsenceEleveSaisiesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

			if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;

		return $this->collAbsenceEleveSaisies;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getAbsenceEleveSaisiesJoinEdtEmplacementCours($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtEmplacementCours($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_definie_periode);

			if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtEmplacementCours($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;

		return $this->collAbsenceEleveSaisies;
	}

	/**
	 * Clears out the collEdtEmplacementCourss collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEdtEmplacementCourss()
	 */
	public function clearEdtEmplacementCourss()
	{
		$this->collEdtEmplacementCourss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collEdtEmplacementCourss collection (array).
	 *
	 * By default this just sets the collEdtEmplacementCourss collection to an empty array (like clearcollEdtEmplacementCourss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initEdtEmplacementCourss()
	{
		$this->collEdtEmplacementCourss = array();
	}

	/**
	 * Gets an array of EdtEmplacementCours objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this EdtCreneau has previously been saved, it will retrieve
	 * related EdtEmplacementCourss from storage. If this EdtCreneau is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array EdtEmplacementCours[]
	 * @throws     PropelException
	 */
	public function getEdtEmplacementCourss($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
			   $this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				EdtEmplacementCoursPeer::addSelectColumns($criteria);
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				EdtEmplacementCoursPeer::addSelectColumns($criteria);
				if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
					$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;
		return $this->collEdtEmplacementCourss;
	}

	/**
	 * Returns the number of related EdtEmplacementCours objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related EdtEmplacementCours objects.
	 * @throws     PropelException
	 */
	public function countEdtEmplacementCourss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$count = EdtEmplacementCoursPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
					$count = EdtEmplacementCoursPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collEdtEmplacementCourss);
				}
			} else {
				$count = count($this->collEdtEmplacementCourss);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a EdtEmplacementCours object to this object
	 * through the EdtEmplacementCours foreign key attribute.
	 *
	 * @param      EdtEmplacementCours $l EdtEmplacementCours
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEdtEmplacementCours(EdtEmplacementCours $l)
	{
		if ($this->collEdtEmplacementCourss === null) {
			$this->initEdtEmplacementCourss();
		}
		if (!in_array($l, $this->collEdtEmplacementCourss, true)) { // only add it if the **same** object is not already associated
			array_push($this->collEdtEmplacementCourss, $l);
			$l->setEdtCreneau($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getEdtEmplacementCourssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getEdtEmplacementCourssJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinAidDetails($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinAidDetails($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getEdtEmplacementCourssJoinEdtSalle($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtSalle($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtSalle($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getEdtEmplacementCourssJoinEdtCalendrierPeriode($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCalendrierPeriode($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCalendrierPeriode($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCreneau is new, it will return
	 * an empty collection; or if this EdtCreneau has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCreneau.
	 */
	public function getEdtEmplacementCourssJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(EdtCreneauPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::ID_DEFINIE_PERIODE, $this->id_definie_periode);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
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
			if ($this->collAbsenceEleveSaisies) {
				foreach ((array) $this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEdtEmplacementCourss) {
				foreach ((array) $this->collEdtEmplacementCourss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collAbsenceEleveSaisies = null;
		$this->collEdtEmplacementCourss = null;
	}

} // BaseEdtCreneau
