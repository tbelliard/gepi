<?php


/**
 * Base class that represents a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveTraitement extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AbsenceEleveTraitementPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveTraitementPeer
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
	 * The value for the a_type_id field.
	 * @var        int
	 */
	protected $a_type_id;

	/**
	 * The value for the a_motif_id field.
	 * @var        int
	 */
	protected $a_motif_id;

	/**
	 * The value for the a_justification_id field.
	 * @var        int
	 */
	protected $a_justification_id;

	/**
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the modifie_par_utilisateur_id field.
	 * @var        string
	 */
	protected $modifie_par_utilisateur_id;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        AbsenceEleveType
	 */
	protected $aAbsenceEleveType;

	/**
	 * @var        AbsenceEleveMotif
	 */
	protected $aAbsenceEleveMotif;

	/**
	 * @var        AbsenceEleveJustification
	 */
	protected $aAbsenceEleveJustification;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aModifieParUtilisateur;

	/**
	 * @var        array JTraitementSaisieEleve[] Collection to store aggregation of JTraitementSaisieEleve objects.
	 */
	protected $collJTraitementSaisieEleves;

	/**
	 * @var        array AbsenceEleveNotification[] Collection to store aggregation of AbsenceEleveNotification objects.
	 */
	protected $collAbsenceEleveNotifications;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

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
	 * cle primaire auto-incremente
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [utilisateur_id] column value.
	 * Login de l'utilisateur professionnel qui a fait le traitement
	 * @return     string
	 */
	public function getUtilisateurId()
	{
		return $this->utilisateur_id;
	}

	/**
	 * Get the [a_type_id] column value.
	 * cle etrangere du type d'absence
	 * @return     int
	 */
	public function getATypeId()
	{
		return $this->a_type_id;
	}

	/**
	 * Get the [a_motif_id] column value.
	 * cle etrangere du motif d'absence
	 * @return     int
	 */
	public function getAMotifId()
	{
		return $this->a_motif_id;
	}

	/**
	 * Get the [a_justification_id] column value.
	 * cle etrangere de la justification de l'absence
	 * @return     int
	 */
	public function getAJustificationId()
	{
		return $this->a_justification_id;
	}

	/**
	 * Get the [commentaire] column value.
	 * commentaire saisi par l'utilisateur
	 * @return     string
	 */
	public function getCommentaire()
	{
		return $this->commentaire;
	}

	/**
	 * Get the [modifie_par_utilisateur_id] column value.
	 * Login de l'utilisateur professionnel qui a modifie en dernier le traitement
	 * @return     string
	 */
	public function getModifieParUtilisateurId()
	{
		return $this->modifie_par_utilisateur_id;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
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
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
	 * Set the value of [id] column.
	 * cle primaire auto-incremente
	 * @param      int $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a fait le traitement
	 * @param      string $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::UTILISATEUR_ID;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [a_type_id] column.
	 * cle etrangere du type d'absence
	 * @param      int $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setATypeId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_type_id !== $v) {
			$this->a_type_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::A_TYPE_ID;
		}

		if ($this->aAbsenceEleveType !== null && $this->aAbsenceEleveType->getId() !== $v) {
			$this->aAbsenceEleveType = null;
		}

		return $this;
	} // setATypeId()

	/**
	 * Set the value of [a_motif_id] column.
	 * cle etrangere du motif d'absence
	 * @param      int $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setAMotifId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_motif_id !== $v) {
			$this->a_motif_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::A_MOTIF_ID;
		}

		if ($this->aAbsenceEleveMotif !== null && $this->aAbsenceEleveMotif->getId() !== $v) {
			$this->aAbsenceEleveMotif = null;
		}

		return $this;
	} // setAMotifId()

	/**
	 * Set the value of [a_justification_id] column.
	 * cle etrangere de la justification de l'absence
	 * @param      int $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setAJustificationId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_justification_id !== $v) {
			$this->a_justification_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID;
		}

		if ($this->aAbsenceEleveJustification !== null && $this->aAbsenceEleveJustification->getId() !== $v) {
			$this->aAbsenceEleveJustification = null;
		}

		return $this;
	} // setAJustificationId()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire saisi par l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Set the value of [modifie_par_utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a modifie en dernier le traitement
	 * @param      string $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setModifieParUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->modifie_par_utilisateur_id !== $v) {
			$this->modifie_par_utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID;
		}

		if ($this->aModifieParUtilisateur !== null && $this->aModifieParUtilisateur->getLogin() !== $v) {
			$this->aModifieParUtilisateur = null;
		}

		return $this;
	} // setModifieParUtilisateurId()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
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

		if ( $this->created_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->created_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveTraitementPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
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

		if ( $this->updated_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->updated_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveTraitementPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

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
			$this->utilisateur_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->a_type_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->a_motif_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->a_justification_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->commentaire = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->modifie_par_utilisateur_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->created_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->updated_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 9; // 9 = AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveTraitement object", $e);
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
		if ($this->aAbsenceEleveType !== null && $this->a_type_id !== $this->aAbsenceEleveType->getId()) {
			$this->aAbsenceEleveType = null;
		}
		if ($this->aAbsenceEleveMotif !== null && $this->a_motif_id !== $this->aAbsenceEleveMotif->getId()) {
			$this->aAbsenceEleveMotif = null;
		}
		if ($this->aAbsenceEleveJustification !== null && $this->a_justification_id !== $this->aAbsenceEleveJustification->getId()) {
			$this->aAbsenceEleveJustification = null;
		}
		if ($this->aModifieParUtilisateur !== null && $this->modifie_par_utilisateur_id !== $this->aModifieParUtilisateur->getLogin()) {
			$this->aModifieParUtilisateur = null;
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveTraitementPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?
			$this->aUtilisateurProfessionnel = null;
			$this->aAbsenceEleveType = null;
			$this->aAbsenceEleveMotif = null;
			$this->aAbsenceEleveJustification = null;
			$this->aModifieParUtilisateur = null;
			$this->collJTraitementSaisieEleves = null;
			$this->collAbsenceEleveNotifications = null;
			$this->collAbsenceEleveSaisies = null;
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AbsenceEleveTraitementQuery::create()
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(AbsenceEleveTraitementPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(AbsenceEleveTraitementPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(AbsenceEleveTraitementPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				AbsenceEleveTraitementPeer::addInstanceToPool($this);
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

			if ($this->aUtilisateurProfessionnel !== null) {
				if ($this->aUtilisateurProfessionnel->isModified() || $this->aUtilisateurProfessionnel->isNew()) {
					$affectedRows += $this->aUtilisateurProfessionnel->save($con);
				}
				$this->setUtilisateurProfessionnel($this->aUtilisateurProfessionnel);
			}

			if ($this->aAbsenceEleveType !== null) {
				if ($this->aAbsenceEleveType->isModified() || $this->aAbsenceEleveType->isNew()) {
					$affectedRows += $this->aAbsenceEleveType->save($con);
				}
				$this->setAbsenceEleveType($this->aAbsenceEleveType);
			}

			if ($this->aAbsenceEleveMotif !== null) {
				if ($this->aAbsenceEleveMotif->isModified() || $this->aAbsenceEleveMotif->isNew()) {
					$affectedRows += $this->aAbsenceEleveMotif->save($con);
				}
				$this->setAbsenceEleveMotif($this->aAbsenceEleveMotif);
			}

			if ($this->aAbsenceEleveJustification !== null) {
				if ($this->aAbsenceEleveJustification->isModified() || $this->aAbsenceEleveJustification->isNew()) {
					$affectedRows += $this->aAbsenceEleveJustification->save($con);
				}
				$this->setAbsenceEleveJustification($this->aAbsenceEleveJustification);
			}

			if ($this->aModifieParUtilisateur !== null) {
				if ($this->aModifieParUtilisateur->isModified() || $this->aModifieParUtilisateur->isNew()) {
					$affectedRows += $this->aModifieParUtilisateur->save($con);
				}
				$this->setModifieParUtilisateur($this->aModifieParUtilisateur);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceEleveTraitementPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(AbsenceEleveTraitementPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveTraitementPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveTraitementPeer::doUpdate($this, $con);
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

			if ($this->collAbsenceEleveNotifications !== null) {
				foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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

			if ($this->aAbsenceEleveType !== null) {
				if (!$this->aAbsenceEleveType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveType->getValidationFailures());
				}
			}

			if ($this->aAbsenceEleveMotif !== null) {
				if (!$this->aAbsenceEleveMotif->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveMotif->getValidationFailures());
				}
			}

			if ($this->aAbsenceEleveJustification !== null) {
				if (!$this->aAbsenceEleveJustification->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveJustification->getValidationFailures());
				}
			}

			if ($this->aModifieParUtilisateur !== null) {
				if (!$this->aModifieParUtilisateur->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aModifieParUtilisateur->getValidationFailures());
				}
			}


			if (($retval = AbsenceEleveTraitementPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisieEleves !== null) {
					foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveNotifications !== null) {
					foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
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
		$pos = AbsenceEleveTraitementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getATypeId();
				break;
			case 3:
				return $this->getAMotifId();
				break;
			case 4:
				return $this->getAJustificationId();
				break;
			case 5:
				return $this->getCommentaire();
				break;
			case 6:
				return $this->getModifieParUtilisateurId();
				break;
			case 7:
				return $this->getCreatedAt();
				break;
			case 8:
				return $this->getUpdatedAt();
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
		$keys = AbsenceEleveTraitementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getATypeId(),
			$keys[3] => $this->getAMotifId(),
			$keys[4] => $this->getAJustificationId(),
			$keys[5] => $this->getCommentaire(),
			$keys[6] => $this->getModifieParUtilisateurId(),
			$keys[7] => $this->getCreatedAt(),
			$keys[8] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aUtilisateurProfessionnel) {
				$result['UtilisateurProfessionnel'] = $this->aUtilisateurProfessionnel->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aAbsenceEleveType) {
				$result['AbsenceEleveType'] = $this->aAbsenceEleveType->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aAbsenceEleveMotif) {
				$result['AbsenceEleveMotif'] = $this->aAbsenceEleveMotif->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aAbsenceEleveJustification) {
				$result['AbsenceEleveJustification'] = $this->aAbsenceEleveJustification->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aModifieParUtilisateur) {
				$result['ModifieParUtilisateur'] = $this->aModifieParUtilisateur->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = AbsenceEleveTraitementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setATypeId($value);
				break;
			case 3:
				$this->setAMotifId($value);
				break;
			case 4:
				$this->setAJustificationId($value);
				break;
			case 5:
				$this->setCommentaire($value);
				break;
			case 6:
				$this->setModifieParUtilisateurId($value);
				break;
			case 7:
				$this->setCreatedAt($value);
				break;
			case 8:
				$this->setUpdatedAt($value);
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
		$keys = AbsenceEleveTraitementPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setATypeId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAMotifId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAJustificationId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setCommentaire($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setModifieParUtilisateurId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCreatedAt($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setUpdatedAt($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveTraitementPeer::ID)) $criteria->add(AbsenceEleveTraitementPeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::UTILISATEUR_ID)) $criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::A_TYPE_ID)) $criteria->add(AbsenceEleveTraitementPeer::A_TYPE_ID, $this->a_type_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::A_MOTIF_ID)) $criteria->add(AbsenceEleveTraitementPeer::A_MOTIF_ID, $this->a_motif_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID)) $criteria->add(AbsenceEleveTraitementPeer::A_JUSTIFICATION_ID, $this->a_justification_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::COMMENTAIRE)) $criteria->add(AbsenceEleveTraitementPeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID)) $criteria->add(AbsenceEleveTraitementPeer::MODIFIE_PAR_UTILISATEUR_ID, $this->modifie_par_utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::CREATED_AT)) $criteria->add(AbsenceEleveTraitementPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::UPDATED_AT)) $criteria->add(AbsenceEleveTraitementPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveTraitementPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveTraitement (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setUtilisateurId($this->utilisateur_id);
		$copyObj->setATypeId($this->a_type_id);
		$copyObj->setAMotifId($this->a_motif_id);
		$copyObj->setAJustificationId($this->a_justification_id);
		$copyObj->setCommentaire($this->commentaire);
		$copyObj->setModifieParUtilisateurId($this->modifie_par_utilisateur_id);
		$copyObj->setCreatedAt($this->created_at);
		$copyObj->setUpdatedAt($this->updated_at);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisieEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisieEleve($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveNotifications() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveNotification($relObj->copy($deepCopy));
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
	 * @return     AbsenceEleveTraitement Clone of current object.
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
	 * @return     AbsenceEleveTraitementPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveTraitementPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
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
			$v->addAbsenceEleveTraitement($this);
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
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelQuery::create()->findPk($this->utilisateur_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aUtilisateurProfessionnel->addAbsenceEleveTraitements($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveType object.
	 *
	 * @param      AbsenceEleveType $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveType(AbsenceEleveType $v = null)
	{
		if ($v === null) {
			$this->setATypeId(NULL);
		} else {
			$this->setATypeId($v->getId());
		}

		$this->aAbsenceEleveType = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveType object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveType object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveType The associated AbsenceEleveType object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveType(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveType === null && ($this->a_type_id !== null)) {
			$this->aAbsenceEleveType = AbsenceEleveTypeQuery::create()->findPk($this->a_type_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aAbsenceEleveType->addAbsenceEleveTraitements($this);
			 */
		}
		return $this->aAbsenceEleveType;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveMotif object.
	 *
	 * @param      AbsenceEleveMotif $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveMotif(AbsenceEleveMotif $v = null)
	{
		if ($v === null) {
			$this->setAMotifId(NULL);
		} else {
			$this->setAMotifId($v->getId());
		}

		$this->aAbsenceEleveMotif = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveMotif object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveMotif object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveMotif The associated AbsenceEleveMotif object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveMotif(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveMotif === null && ($this->a_motif_id !== null)) {
			$this->aAbsenceEleveMotif = AbsenceEleveMotifQuery::create()->findPk($this->a_motif_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aAbsenceEleveMotif->addAbsenceEleveTraitements($this);
			 */
		}
		return $this->aAbsenceEleveMotif;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveJustification object.
	 *
	 * @param      AbsenceEleveJustification $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveJustification(AbsenceEleveJustification $v = null)
	{
		if ($v === null) {
			$this->setAJustificationId(NULL);
		} else {
			$this->setAJustificationId($v->getId());
		}

		$this->aAbsenceEleveJustification = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveJustification object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveJustification object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveJustification The associated AbsenceEleveJustification object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveJustification(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveJustification === null && ($this->a_justification_id !== null)) {
			$this->aAbsenceEleveJustification = AbsenceEleveJustificationQuery::create()->findPk($this->a_justification_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aAbsenceEleveJustification->addAbsenceEleveTraitements($this);
			 */
		}
		return $this->aAbsenceEleveJustification;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setModifieParUtilisateur(UtilisateurProfessionnel $v = null)
	{
		if ($v === null) {
			$this->setModifieParUtilisateurId(NULL);
		} else {
			$this->setModifieParUtilisateurId($v->getLogin());
		}

		$this->aModifieParUtilisateur = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the UtilisateurProfessionnel object, it will not be re-added.
		if ($v !== null) {
			$v->addModifiedAbsenceEleveTraitement($this);
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
	public function getModifieParUtilisateur(PropelPDO $con = null)
	{
		if ($this->aModifieParUtilisateur === null && (($this->modifie_par_utilisateur_id !== "" && $this->modifie_par_utilisateur_id !== null))) {
			$this->aModifieParUtilisateur = UtilisateurProfessionnelQuery::create()->findPk($this->modifie_par_utilisateur_id, $con);
			/* The following can be used additionally to
				 guarantee the related object contains a reference
				 to this object.  This level of coupling may, however, be
				 undesirable since it could result in an only partially populated collection
				 in the referenced object.
				 $this->aModifieParUtilisateur->addModifiedAbsenceEleveTraitements($this);
			 */
		}
		return $this->aModifieParUtilisateur;
	}

	/**
	 * Clears out the collJTraitementSaisieEleves collection
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
	 * Initializes the collJTraitementSaisieEleves collection.
	 *
	 * By default this just sets the collJTraitementSaisieEleves collection to an empty array (like clearcollJTraitementSaisieEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJTraitementSaisieEleves()
	{
		$this->collJTraitementSaisieEleves = new PropelObjectCollection();
		$this->collJTraitementSaisieEleves->setModel('JTraitementSaisieEleve');
	}

	/**
	 * Gets an array of JTraitementSaisieEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveTraitement is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JTraitementSaisieEleve[] List of JTraitementSaisieEleve objects
	 * @throws     PropelException
	 */
	public function getJTraitementSaisieEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJTraitementSaisieEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJTraitementSaisieEleves) {
				// return empty collection
				$this->initJTraitementSaisieEleves();
			} else {
				$collJTraitementSaisieEleves = JTraitementSaisieEleveQuery::create(null, $criteria)
					->filterByAbsenceEleveTraitement($this)
					->find($con);
				if (null !== $criteria) {
					return $collJTraitementSaisieEleves;
				}
				$this->collJTraitementSaisieEleves = $collJTraitementSaisieEleves;
			}
		}
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
		if(null === $this->collJTraitementSaisieEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJTraitementSaisieEleves) {
				return 0;
			} else {
				$query = JTraitementSaisieEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveTraitement($this)
					->count($con);
			}
		} else {
			return count($this->collJTraitementSaisieEleves);
		}
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
		if (!$this->collJTraitementSaisieEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJTraitementSaisieEleves[]= $l;
			$l->setAbsenceEleveTraitement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveTraitement is new, it will return
	 * an empty collection; or if this AbsenceEleveTraitement has previously
	 * been saved, it will retrieve related JTraitementSaisieEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveTraitement.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JTraitementSaisieEleve[] List of JTraitementSaisieEleve objects
	 */
	public function getJTraitementSaisieElevesJoinAbsenceEleveSaisie($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JTraitementSaisieEleveQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveSaisie', $join_behavior);

		return $this->getJTraitementSaisieEleves($query, $con);
	}

	/**
	 * Clears out the collAbsenceEleveNotifications collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveNotifications()
	 */
	public function clearAbsenceEleveNotifications()
	{
		$this->collAbsenceEleveNotifications = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveNotifications collection.
	 *
	 * By default this just sets the collAbsenceEleveNotifications collection to an empty array (like clearcollAbsenceEleveNotifications());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveNotifications()
	{
		$this->collAbsenceEleveNotifications = new PropelObjectCollection();
		$this->collAbsenceEleveNotifications->setModel('AbsenceEleveNotification');
	}

	/**
	 * Gets an array of AbsenceEleveNotification objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveTraitement is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveNotifications($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				// return empty collection
				$this->initAbsenceEleveNotifications();
			} else {
				$collAbsenceEleveNotifications = AbsenceEleveNotificationQuery::create(null, $criteria)
					->filterByAbsenceEleveTraitement($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveNotifications;
				}
				$this->collAbsenceEleveNotifications = $collAbsenceEleveNotifications;
			}
		}
		return $this->collAbsenceEleveNotifications;
	}

	/**
	 * Returns the number of related AbsenceEleveNotification objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveNotification objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveNotifications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveNotifications || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveNotifications) {
				return 0;
			} else {
				$query = AbsenceEleveNotificationQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveTraitement($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveNotifications);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveNotification object to this object
	 * through the AbsenceEleveNotification foreign key attribute.
	 *
	 * @param      AbsenceEleveNotification $l AbsenceEleveNotification
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveNotification(AbsenceEleveNotification $l)
	{
		if ($this->collAbsenceEleveNotifications === null) {
			$this->initAbsenceEleveNotifications();
		}
		if (!$this->collAbsenceEleveNotifications->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveNotifications[]= $l;
			$l->setAbsenceEleveTraitement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveTraitement is new, it will return
	 * an empty collection; or if this AbsenceEleveTraitement has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveTraitement.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveTraitement is new, it will return
	 * an empty collection; or if this AbsenceEleveTraitement has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveTraitement.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinResponsableEleveAdresse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('ResponsableEleveAdresse', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}

	/**
	 * Clears out the collAbsenceEleveSaisies collection
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
	 * Initializes the collAbsenceEleveSaisies collection.
	 *
	 * By default this just sets the collAbsenceEleveSaisies collection to an empty collection (like clearAbsenceEleveSaisies());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveSaisies()
	{
		$this->collAbsenceEleveSaisies = new PropelObjectCollection();
		$this->collAbsenceEleveSaisies->setModel('AbsenceEleveSaisie');
	}

	/**
	 * Gets a collection of AbsenceEleveSaisie objects related by a many-to-many relationship
	 * to the current object by way of the j_traitements_saisies cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveTraitement is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisies($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				// return empty collection
				$this->initAbsenceEleveSaisies();
			} else {
				$collAbsenceEleveSaisies = AbsenceEleveSaisieQuery::create(null, $criteria)
					->filterByAbsenceEleveTraitement($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveSaisies;
				}
				$this->collAbsenceEleveSaisies = $collAbsenceEleveSaisies;
			}
		}
		return $this->collAbsenceEleveSaisies;
	}

	/**
	 * Gets the number of AbsenceEleveSaisie objects related by a many-to-many relationship
	 * to the current object by way of the j_traitements_saisies cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related AbsenceEleveSaisie objects
	 */
	public function countAbsenceEleveSaisies($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				return 0;
			} else {
				$query = AbsenceEleveSaisieQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveTraitement($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveSaisies);
		}
	}

	/**
	 * Associate a AbsenceEleveSaisie object to this object
	 * through the j_traitements_saisies cross reference table.
	 *
	 * @param      AbsenceEleveSaisie $absenceEleveSaisie The JTraitementSaisieEleve object to relate
	 * @return     void
	 */
	public function addAbsenceEleveSaisie($absenceEleveSaisie)
	{
		if ($this->collAbsenceEleveSaisies === null) {
			$this->initAbsenceEleveSaisies();
		}
		if (!$this->collAbsenceEleveSaisies->contains($absenceEleveSaisie)) { // only add it if the **same** object is not already associated
			$jTraitementSaisieEleve = new JTraitementSaisieEleve();
			$jTraitementSaisieEleve->setAbsenceEleveSaisie($absenceEleveSaisie);
			$this->addJTraitementSaisieEleve($jTraitementSaisieEleve);

			$this->collAbsenceEleveSaisies[]= $absenceEleveSaisie;
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->utilisateur_id = null;
		$this->a_type_id = null;
		$this->a_motif_id = null;
		$this->a_justification_id = null;
		$this->commentaire = null;
		$this->modifie_par_utilisateur_id = null;
		$this->created_at = null;
		$this->updated_at = null;
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
			if ($this->collJTraitementSaisieEleves) {
				foreach ((array) $this->collJTraitementSaisieEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ((array) $this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJTraitementSaisieEleves = null;
		$this->collAbsenceEleveNotifications = null;
		$this->aUtilisateurProfessionnel = null;
		$this->aAbsenceEleveType = null;
		$this->aAbsenceEleveMotif = null;
		$this->aAbsenceEleveJustification = null;
		$this->aModifieParUtilisateur = null;
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = AbsenceEleveTraitementPeer::UPDATED_AT;
		return $this;
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

} // BaseAbsenceEleveTraitement
