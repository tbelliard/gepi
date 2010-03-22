<?php

/**
 * Base class that represents a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceEleveSaisie extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveSaisiePeer
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
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the debut_abs field.
	 * @var        string
	 */
	protected $debut_abs;

	/**
	 * The value for the fin_abs field.
	 * @var        string
	 */
	protected $fin_abs;

	/**
	 * The value for the id_edt_creneau field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $id_edt_creneau;

	/**
	 * The value for the id_edt_emplacement_cours field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $id_edt_emplacement_cours;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        EdtCreneau
	 */
	protected $aEdtCreneau;

	/**
	 * @var        EdtEmplacementCours
	 */
	protected $aEdtEmplacementCours;

	/**
	 * @var        array JTraitementSaisieEleve[] Collection to store aggregation of JTraitementSaisieEleve objects.
	 */
	protected $collJTraitementSaisieEleves;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementSaisieEleves.
	 */
	private $lastJTraitementSaisieEleveCriteria = null;

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
	 * Initializes internal state of BaseAbsenceEleveSaisie object.
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
		$this->id_edt_creneau = 0;
		$this->id_edt_emplacement_cours = 0;
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
	 * Get the [commentaire] column value.
	 * commentaire de l'utilisateur
	 * @return     string
	 */
	public function getCommentaire()
	{
		return $this->commentaire;
	}

	/**
	 * Get the [optionally formatted] temporal [debut_abs] column value.
	 * Debut de l'absence en timestamp UNIX
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDebutAbs($format = '%X')
	{
		if ($this->debut_abs === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->debut_abs);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->debut_abs, true), $x);
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
	 * Get the [optionally formatted] temporal [fin_abs] column value.
	 * Fin de l'absence en timestamp UNIX
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFinAbs($format = '%X')
	{
		if ($this->fin_abs === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fin_abs);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fin_abs, true), $x);
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
	 * Get the [id_edt_creneau] column value.
	 * identifiant du creneaux de l'emploi du temps
	 * @return     int
	 */
	public function getIdEdtCreneau()
	{
		return $this->id_edt_creneau;
	}

	/**
	 * Get the [id_edt_emplacement_cours] column value.
	 * identifiant du cours de l'emploi du temps
	 * @return     int
	 */
	public function getIdEdtEmplacementCours()
	{
		return $this->id_edt_emplacement_cours;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::UTILISATEUR_ID;
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
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setEleveId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eleve_id !== $v) {
			$this->eleve_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ELEVE_ID;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setEleveId()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire de l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Sets the value of [debut_abs] column to a normalized version of the date/time value specified.
	 * Debut de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setDebutAbs($v)
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

		if ( $this->debut_abs !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->debut_abs !== null && $tmpDt = new DateTime($this->debut_abs)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->debut_abs = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::DEBUT_ABS;
			}
		} // if either are not null

		return $this;
	} // setDebutAbs()

	/**
	 * Sets the value of [fin_abs] column to a normalized version of the date/time value specified.
	 * Fin de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setFinAbs($v)
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

		if ( $this->fin_abs !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fin_abs !== null && $tmpDt = new DateTime($this->fin_abs)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->fin_abs = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::FIN_ABS;
			}
		} // if either are not null

		return $this;
	} // setFinAbs()

	/**
	 * Set the value of [id_edt_creneau] column.
	 * identifiant du creneaux de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdEdtCreneau($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_creneau !== $v || $v === 0) {
			$this->id_edt_creneau = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_EDT_CRENEAU;
		}

		if ($this->aEdtCreneau !== null && $this->aEdtCreneau->getIdDefiniePeriode() !== $v) {
			$this->aEdtCreneau = null;
		}

		return $this;
	} // setIdEdtCreneau()

	/**
	 * Set the value of [id_edt_emplacement_cours] column.
	 * identifiant du cours de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdEdtEmplacementCours($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_emplacement_cours !== $v || $v === 0) {
			$this->id_edt_emplacement_cours = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS;
		}

		if ($this->aEdtEmplacementCours !== null && $this->aEdtEmplacementCours->getIdCours() !== $v) {
			$this->aEdtEmplacementCours = null;
		}

		return $this;
	} // setIdEdtEmplacementCours()

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
			if (array_diff($this->modifiedColumns, array(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU,AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS))) {
				return false;
			}

			if ($this->id_edt_creneau !== 0) {
				return false;
			}

			if ($this->id_edt_emplacement_cours !== 0) {
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
			$this->commentaire = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->debut_abs = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->fin_abs = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->id_edt_creneau = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->id_edt_emplacement_cours = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = AbsenceEleveSaisiePeer::NUM_COLUMNS - AbsenceEleveSaisiePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveSaisie object", $e);
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
		if ($this->aEdtCreneau !== null && $this->id_edt_creneau !== $this->aEdtCreneau->getIdDefiniePeriode()) {
			$this->aEdtCreneau = null;
		}
		if ($this->aEdtEmplacementCours !== null && $this->id_edt_emplacement_cours !== $this->aEdtEmplacementCours->getIdCours()) {
			$this->aEdtEmplacementCours = null;
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveSaisiePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aUtilisateurProfessionnel = null;
			$this->aEleve = null;
			$this->aEdtCreneau = null;
			$this->aEdtEmplacementCours = null;
			$this->collJTraitementSaisieEleves = null;
			$this->lastJTraitementSaisieEleveCriteria = null;

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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			AbsenceEleveSaisiePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AbsenceEleveSaisiePeer::addInstanceToPool($this);
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

			if ($this->aEdtCreneau !== null) {
				if ($this->aEdtCreneau->isModified() || $this->aEdtCreneau->isNew()) {
					$affectedRows += $this->aEdtCreneau->save($con);
				}
				$this->setEdtCreneau($this->aEdtCreneau);
			}

			if ($this->aEdtEmplacementCours !== null) {
				if ($this->aEdtEmplacementCours->isModified() || $this->aEdtEmplacementCours->isNew()) {
					$affectedRows += $this->aEdtEmplacementCours->save($con);
				}
				$this->setEdtEmplacementCours($this->aEdtEmplacementCours);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AbsenceEleveSaisiePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveSaisiePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJTraitementSaisieEleves !== null) {
				foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
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

			if ($this->aEdtCreneau !== null) {
				if (!$this->aEdtCreneau->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEdtCreneau->getValidationFailures());
				}
			}

			if ($this->aEdtEmplacementCours !== null) {
				if (!$this->aEdtEmplacementCours->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEdtEmplacementCours->getValidationFailures());
				}
			}


			if (($retval = AbsenceEleveSaisiePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisieEleves !== null) {
					foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
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
		$pos = AbsenceEleveSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCommentaire();
				break;
			case 4:
				return $this->getDebutAbs();
				break;
			case 5:
				return $this->getFinAbs();
				break;
			case 6:
				return $this->getIdEdtCreneau();
				break;
			case 7:
				return $this->getIdEdtEmplacementCours();
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
		$keys = AbsenceEleveSaisiePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getEleveId(),
			$keys[3] => $this->getCommentaire(),
			$keys[4] => $this->getDebutAbs(),
			$keys[5] => $this->getFinAbs(),
			$keys[6] => $this->getIdEdtCreneau(),
			$keys[7] => $this->getIdEdtEmplacementCours(),
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
		$pos = AbsenceEleveSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCommentaire($value);
				break;
			case 4:
				$this->setDebutAbs($value);
				break;
			case 5:
				$this->setFinAbs($value);
				break;
			case 6:
				$this->setIdEdtCreneau($value);
				break;
			case 7:
				$this->setIdEdtEmplacementCours($value);
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
		$keys = AbsenceEleveSaisiePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEleveId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCommentaire($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDebutAbs($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFinAbs($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setIdEdtCreneau($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setIdEdtEmplacementCours($arr[$keys[7]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID)) $criteria->add(AbsenceEleveSaisiePeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::UTILISATEUR_ID)) $criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ELEVE_ID)) $criteria->add(AbsenceEleveSaisiePeer::ELEVE_ID, $this->eleve_id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::COMMENTAIRE)) $criteria->add(AbsenceEleveSaisiePeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::DEBUT_ABS)) $criteria->add(AbsenceEleveSaisiePeer::DEBUT_ABS, $this->debut_abs);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::FIN_ABS)) $criteria->add(AbsenceEleveSaisiePeer::FIN_ABS, $this->fin_abs);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU)) $criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_edt_creneau);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS)) $criteria->add(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $this->id_edt_emplacement_cours);

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
		$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);

		$criteria->add(AbsenceEleveSaisiePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveSaisie (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setUtilisateurId($this->utilisateur_id);

		$copyObj->setEleveId($this->eleve_id);

		$copyObj->setCommentaire($this->commentaire);

		$copyObj->setDebutAbs($this->debut_abs);

		$copyObj->setFinAbs($this->fin_abs);

		$copyObj->setIdEdtCreneau($this->id_edt_creneau);

		$copyObj->setIdEdtEmplacementCours($this->id_edt_emplacement_cours);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisieEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisieEleve($relObj->copy($deepCopy));
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
	 * @return     AbsenceEleveSaisie Clone of current object.
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
	 * @return     AbsenceEleveSaisiePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveSaisiePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
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
			$v->addAbsenceEleveSaisie($this);
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
			   $this->aUtilisateurProfessionnel->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
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
			$v->addAbsenceEleveSaisie($this);
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
			   $this->aEleve->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Declares an association between this object and a EdtCreneau object.
	 *
	 * @param      EdtCreneau $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEdtCreneau(EdtCreneau $v = null)
	{
		if ($v === null) {
			$this->setIdEdtCreneau(0);
		} else {
			$this->setIdEdtCreneau($v->getIdDefiniePeriode());
		}

		$this->aEdtCreneau = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the EdtCreneau object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated EdtCreneau object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     EdtCreneau The associated EdtCreneau object.
	 * @throws     PropelException
	 */
	public function getEdtCreneau(PropelPDO $con = null)
	{
		if ($this->aEdtCreneau === null && ($this->id_edt_creneau !== null)) {
			$this->aEdtCreneau = EdtCreneauPeer::retrieveByPK($this->id_edt_creneau, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aEdtCreneau->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEdtCreneau;
	}

	/**
	 * Declares an association between this object and a EdtEmplacementCours object.
	 *
	 * @param      EdtEmplacementCours $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEdtEmplacementCours(EdtEmplacementCours $v = null)
	{
		if ($v === null) {
			$this->setIdEdtEmplacementCours(0);
		} else {
			$this->setIdEdtEmplacementCours($v->getIdCours());
		}

		$this->aEdtEmplacementCours = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the EdtEmplacementCours object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated EdtEmplacementCours object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     EdtEmplacementCours The associated EdtEmplacementCours object.
	 * @throws     PropelException
	 */
	public function getEdtEmplacementCours(PropelPDO $con = null)
	{
		if ($this->aEdtEmplacementCours === null && ($this->id_edt_emplacement_cours !== null)) {
			$this->aEdtEmplacementCours = EdtEmplacementCoursPeer::retrieveByPK($this->id_edt_emplacement_cours, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aEdtEmplacementCours->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEdtEmplacementCours;
	}

	/**
	 * Clears out the collJTraitementSaisieEleves collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJTraitementSaisieEleves()
	 */
	public function clearJTraitementSaisieEleves()
	{
		$this->collJTraitementSaisieEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJTraitementSaisieEleves collection (array).
	 *
	 * By default this just sets the collJTraitementSaisieEleves collection to an empty array (like clearcollJTraitementSaisieEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJTraitementSaisieEleves()
	{
		$this->collJTraitementSaisieEleves = array();
	}

	/**
	 * Gets an array of JTraitementSaisieEleve objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveSaisie has previously been saved, it will retrieve
	 * related JTraitementSaisieEleves from storage. If this AbsenceEleveSaisie is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JTraitementSaisieEleve[]
	 * @throws     PropelException
	 */
	public function getJTraitementSaisieEleves($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisieEleves === null) {
			if ($this->isNew()) {
			   $this->collJTraitementSaisieEleves = array();
			} else {

				$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

				JTraitementSaisieElevePeer::addSelectColumns($criteria);
				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

				JTraitementSaisieElevePeer::addSelectColumns($criteria);
				if (!isset($this->lastJTraitementSaisieEleveCriteria) || !$this->lastJTraitementSaisieEleveCriteria->equals($criteria)) {
					$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJTraitementSaisieEleveCriteria = $criteria;
		return $this->collJTraitementSaisieEleves;
	}

	/**
	 * Returns the number of related JTraitementSaisieEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JTraitementSaisieEleve objects.
	 * @throws     PropelException
	 */
	public function countJTraitementSaisieEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJTraitementSaisieEleves === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

				$count = JTraitementSaisieElevePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

				if (!isset($this->lastJTraitementSaisieEleveCriteria) || !$this->lastJTraitementSaisieEleveCriteria->equals($criteria)) {
					$count = JTraitementSaisieElevePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJTraitementSaisieEleves);
				}
			} else {
				$count = count($this->collJTraitementSaisieEleves);
			}
		}
		$this->lastJTraitementSaisieEleveCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JTraitementSaisieEleve object to this object
	 * through the JTraitementSaisieEleve foreign key attribute.
	 *
	 * @param      JTraitementSaisieEleve $l JTraitementSaisieEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJTraitementSaisieEleve(JTraitementSaisieEleve $l)
	{
		if ($this->collJTraitementSaisieEleves === null) {
			$this->initJTraitementSaisieEleves();
		}
		if (!in_array($l, $this->collJTraitementSaisieEleves, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJTraitementSaisieEleves, $l);
			$l->setAbsenceEleveSaisie($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveSaisie is new, it will return
	 * an empty collection; or if this AbsenceEleveSaisie has previously
	 * been saved, it will retrieve related JTraitementSaisieEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveSaisie.
	 */
	public function getJTraitementSaisieElevesJoinAbsenceEleveTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisieEleves === null) {
			if ($this->isNew()) {
				$this->collJTraitementSaisieEleves = array();
			} else {

				$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelectJoinAbsenceEleveTraitement($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementSaisieElevePeer::A_SAISIE_ID, $this->id);

			if (!isset($this->lastJTraitementSaisieEleveCriteria) || !$this->lastJTraitementSaisieEleveCriteria->equals($criteria)) {
				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelectJoinAbsenceEleveTraitement($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementSaisieEleveCriteria = $criteria;

		return $this->collJTraitementSaisieEleves;
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
			if ($this->collJTraitementSaisieEleves) {
				foreach ((array) $this->collJTraitementSaisieEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJTraitementSaisieEleves = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aEleve = null;
			$this->aEdtCreneau = null;
			$this->aEdtEmplacementCours = null;
	}

} // BaseAbsenceEleveSaisie
