<?php

/**
 * Base class that represents a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceTraitement extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceTraitementPeer
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
	 * The value for the created_on field.
	 * @var        int
	 */
	protected $created_on;

	/**
	 * The value for the updated_on field.
	 * @var        int
	 */
	protected $updated_on;

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
	 * The value for the texte_justification field.
	 * @var        string
	 */
	protected $texte_justification;

	/**
	 * The value for the a_action_id field.
	 * @var        int
	 */
	protected $a_action_id;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        AbsenceType
	 */
	protected $aAbsenceType;

	/**
	 * @var        AbsenceMotif
	 */
	protected $aAbsenceMotif;

	/**
	 * @var        AbsenceJustification
	 */
	protected $aAbsenceJustification;

	/**
	 * @var        AbsenceAction
	 */
	protected $aAbsenceAction;

	/**
	 * @var        array JTraitementSaisie[] Collection to store aggregation of JTraitementSaisie objects.
	 */
	protected $collJTraitementSaisies;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementSaisies.
	 */
	private $lastJTraitementSaisieCriteria = null;

	/**
	 * @var        array JTraitementEnvoi[] Collection to store aggregation of JTraitementEnvoi objects.
	 */
	protected $collJTraitementEnvois;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementEnvois.
	 */
	private $lastJTraitementEnvoiCriteria = null;

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
	 * Initializes internal state of BaseAbsenceTraitement object.
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
	 * Get the [created_on] column value.
	 * Date du traitement de ou des absences en timestamp UNIX
	 * @return     int
	 */
	public function getCreatedOn()
	{
		return $this->created_on;
	}

	/**
	 * Get the [updated_on] column value.
	 * Date de la modification du traitement de ou des absences en timestamp UNIX
	 * @return     int
	 */
	public function getUpdatedOn()
	{
		return $this->updated_on;
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
	 * Get the [texte_justification] column value.
	 * Texte additionnel à ce traitement
	 * @return     string
	 */
	public function getTexteJustification()
	{
		return $this->texte_justification;
	}

	/**
	 * Get the [a_action_id] column value.
	 * cle etrangere de l'action sur ce traitement
	 * @return     int
	 */
	public function getAActionId()
	{
		return $this->a_action_id;
	}

	/**
	 * Set the value of [id] column.
	 * cle primaire auto-incremente
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a fait le traitement
	 * @param      string $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::UTILISATEUR_ID;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [created_on] column.
	 * Date du traitement de ou des absences en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setCreatedOn($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->created_on !== $v) {
			$this->created_on = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::CREATED_ON;
		}

		return $this;
	} // setCreatedOn()

	/**
	 * Set the value of [updated_on] column.
	 * Date de la modification du traitement de ou des absences en timestamp UNIX
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setUpdatedOn($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->updated_on !== $v) {
			$this->updated_on = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::UPDATED_ON;
		}

		return $this;
	} // setUpdatedOn()

	/**
	 * Set the value of [a_type_id] column.
	 * cle etrangere du type d'absence
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setATypeId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_type_id !== $v) {
			$this->a_type_id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::A_TYPE_ID;
		}

		if ($this->aAbsenceType !== null && $this->aAbsenceType->getId() !== $v) {
			$this->aAbsenceType = null;
		}

		return $this;
	} // setATypeId()

	/**
	 * Set the value of [a_motif_id] column.
	 * cle etrangere du motif d'absence
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setAMotifId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_motif_id !== $v) {
			$this->a_motif_id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::A_MOTIF_ID;
		}

		if ($this->aAbsenceMotif !== null && $this->aAbsenceMotif->getId() !== $v) {
			$this->aAbsenceMotif = null;
		}

		return $this;
	} // setAMotifId()

	/**
	 * Set the value of [a_justification_id] column.
	 * cle etrangere de la justification de l'absence
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setAJustificationId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_justification_id !== $v) {
			$this->a_justification_id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::A_JUSTIFICATION_ID;
		}

		if ($this->aAbsenceJustification !== null && $this->aAbsenceJustification->getId() !== $v) {
			$this->aAbsenceJustification = null;
		}

		return $this;
	} // setAJustificationId()

	/**
	 * Set the value of [texte_justification] column.
	 * Texte additionnel à ce traitement
	 * @param      string $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setTexteJustification($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->texte_justification !== $v) {
			$this->texte_justification = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::TEXTE_JUSTIFICATION;
		}

		return $this;
	} // setTexteJustification()

	/**
	 * Set the value of [a_action_id] column.
	 * cle etrangere de l'action sur ce traitement
	 * @param      int $v new value
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 */
	public function setAActionId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_action_id !== $v) {
			$this->a_action_id = $v;
			$this->modifiedColumns[] = AbsenceTraitementPeer::A_ACTION_ID;
		}

		if ($this->aAbsenceAction !== null && $this->aAbsenceAction->getId() !== $v) {
			$this->aAbsenceAction = null;
		}

		return $this;
	} // setAActionId()

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
			$this->utilisateur_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->created_on = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->updated_on = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->a_type_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->a_motif_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->a_justification_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->texte_justification = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->a_action_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 9; // 9 = AbsenceTraitementPeer::NUM_COLUMNS - AbsenceTraitementPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceTraitement object", $e);
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
		if ($this->aAbsenceType !== null && $this->a_type_id !== $this->aAbsenceType->getId()) {
			$this->aAbsenceType = null;
		}
		if ($this->aAbsenceMotif !== null && $this->a_motif_id !== $this->aAbsenceMotif->getId()) {
			$this->aAbsenceMotif = null;
		}
		if ($this->aAbsenceJustification !== null && $this->a_justification_id !== $this->aAbsenceJustification->getId()) {
			$this->aAbsenceJustification = null;
		}
		if ($this->aAbsenceAction !== null && $this->a_action_id !== $this->aAbsenceAction->getId()) {
			$this->aAbsenceAction = null;
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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceTraitementPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aUtilisateurProfessionnel = null;
			$this->aAbsenceType = null;
			$this->aAbsenceMotif = null;
			$this->aAbsenceJustification = null;
			$this->aAbsenceAction = null;
			$this->collJTraitementSaisies = null;
			$this->lastJTraitementSaisieCriteria = null;

			$this->collJTraitementEnvois = null;
			$this->lastJTraitementEnvoiCriteria = null;

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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			AbsenceTraitementPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AbsenceTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AbsenceTraitementPeer::addInstanceToPool($this);
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

			if ($this->aAbsenceType !== null) {
				if ($this->aAbsenceType->isModified() || $this->aAbsenceType->isNew()) {
					$affectedRows += $this->aAbsenceType->save($con);
				}
				$this->setAbsenceType($this->aAbsenceType);
			}

			if ($this->aAbsenceMotif !== null) {
				if ($this->aAbsenceMotif->isModified() || $this->aAbsenceMotif->isNew()) {
					$affectedRows += $this->aAbsenceMotif->save($con);
				}
				$this->setAbsenceMotif($this->aAbsenceMotif);
			}

			if ($this->aAbsenceJustification !== null) {
				if ($this->aAbsenceJustification->isModified() || $this->aAbsenceJustification->isNew()) {
					$affectedRows += $this->aAbsenceJustification->save($con);
				}
				$this->setAbsenceJustification($this->aAbsenceJustification);
			}

			if ($this->aAbsenceAction !== null) {
				if ($this->aAbsenceAction->isModified() || $this->aAbsenceAction->isNew()) {
					$affectedRows += $this->aAbsenceAction->save($con);
				}
				$this->setAbsenceAction($this->aAbsenceAction);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceTraitementPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AbsenceTraitementPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AbsenceTraitementPeer::doUpdate($this, $con);
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

			if ($this->collJTraitementEnvois !== null) {
				foreach ($this->collJTraitementEnvois as $referrerFK) {
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

			if ($this->aAbsenceType !== null) {
				if (!$this->aAbsenceType->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceType->getValidationFailures());
				}
			}

			if ($this->aAbsenceMotif !== null) {
				if (!$this->aAbsenceMotif->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceMotif->getValidationFailures());
				}
			}

			if ($this->aAbsenceJustification !== null) {
				if (!$this->aAbsenceJustification->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceJustification->getValidationFailures());
				}
			}

			if ($this->aAbsenceAction !== null) {
				if (!$this->aAbsenceAction->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceAction->getValidationFailures());
				}
			}


			if (($retval = AbsenceTraitementPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisies !== null) {
					foreach ($this->collJTraitementSaisies as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJTraitementEnvois !== null) {
					foreach ($this->collJTraitementEnvois as $referrerFK) {
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
		$pos = AbsenceTraitementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getCreatedOn();
				break;
			case 3:
				return $this->getUpdatedOn();
				break;
			case 4:
				return $this->getATypeId();
				break;
			case 5:
				return $this->getAMotifId();
				break;
			case 6:
				return $this->getAJustificationId();
				break;
			case 7:
				return $this->getTexteJustification();
				break;
			case 8:
				return $this->getAActionId();
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
		$keys = AbsenceTraitementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getCreatedOn(),
			$keys[3] => $this->getUpdatedOn(),
			$keys[4] => $this->getATypeId(),
			$keys[5] => $this->getAMotifId(),
			$keys[6] => $this->getAJustificationId(),
			$keys[7] => $this->getTexteJustification(),
			$keys[8] => $this->getAActionId(),
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
		$pos = AbsenceTraitementPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setCreatedOn($value);
				break;
			case 3:
				$this->setUpdatedOn($value);
				break;
			case 4:
				$this->setATypeId($value);
				break;
			case 5:
				$this->setAMotifId($value);
				break;
			case 6:
				$this->setAJustificationId($value);
				break;
			case 7:
				$this->setTexteJustification($value);
				break;
			case 8:
				$this->setAActionId($value);
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
		$keys = AbsenceTraitementPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setCreatedOn($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setUpdatedOn($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setATypeId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setAMotifId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAJustificationId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTexteJustification($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAActionId($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceTraitementPeer::ID)) $criteria->add(AbsenceTraitementPeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceTraitementPeer::UTILISATEUR_ID)) $criteria->add(AbsenceTraitementPeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceTraitementPeer::CREATED_ON)) $criteria->add(AbsenceTraitementPeer::CREATED_ON, $this->created_on);
		if ($this->isColumnModified(AbsenceTraitementPeer::UPDATED_ON)) $criteria->add(AbsenceTraitementPeer::UPDATED_ON, $this->updated_on);
		if ($this->isColumnModified(AbsenceTraitementPeer::A_TYPE_ID)) $criteria->add(AbsenceTraitementPeer::A_TYPE_ID, $this->a_type_id);
		if ($this->isColumnModified(AbsenceTraitementPeer::A_MOTIF_ID)) $criteria->add(AbsenceTraitementPeer::A_MOTIF_ID, $this->a_motif_id);
		if ($this->isColumnModified(AbsenceTraitementPeer::A_JUSTIFICATION_ID)) $criteria->add(AbsenceTraitementPeer::A_JUSTIFICATION_ID, $this->a_justification_id);
		if ($this->isColumnModified(AbsenceTraitementPeer::TEXTE_JUSTIFICATION)) $criteria->add(AbsenceTraitementPeer::TEXTE_JUSTIFICATION, $this->texte_justification);
		if ($this->isColumnModified(AbsenceTraitementPeer::A_ACTION_ID)) $criteria->add(AbsenceTraitementPeer::A_ACTION_ID, $this->a_action_id);

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
		$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);

		$criteria->add(AbsenceTraitementPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceTraitement (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setUtilisateurId($this->utilisateur_id);

		$copyObj->setCreatedOn($this->created_on);

		$copyObj->setUpdatedOn($this->updated_on);

		$copyObj->setATypeId($this->a_type_id);

		$copyObj->setAMotifId($this->a_motif_id);

		$copyObj->setAJustificationId($this->a_justification_id);

		$copyObj->setTexteJustification($this->texte_justification);

		$copyObj->setAActionId($this->a_action_id);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisie($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJTraitementEnvois() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementEnvoi($relObj->copy($deepCopy));
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
	 * @return     AbsenceTraitement Clone of current object.
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
	 * @return     AbsenceTraitementPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceTraitementPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceTraitement The current object (for fluent API support)
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
			$v->addAbsenceTraitement($this);
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
			   $this->aUtilisateurProfessionnel->addAbsenceTraitements($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a AbsenceType object.
	 *
	 * @param      AbsenceType $v
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceType(AbsenceType $v = null)
	{
		if ($v === null) {
			$this->setATypeId(NULL);
		} else {
			$this->setATypeId($v->getId());
		}

		$this->aAbsenceType = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceType object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceType object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceType The associated AbsenceType object.
	 * @throws     PropelException
	 */
	public function getAbsenceType(PropelPDO $con = null)
	{
		if ($this->aAbsenceType === null && ($this->a_type_id !== null)) {
			$this->aAbsenceType = AbsenceTypePeer::retrieveByPK($this->a_type_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceType->addAbsenceTraitements($this);
			 */
		}
		return $this->aAbsenceType;
	}

	/**
	 * Declares an association between this object and a AbsenceMotif object.
	 *
	 * @param      AbsenceMotif $v
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceMotif(AbsenceMotif $v = null)
	{
		if ($v === null) {
			$this->setAMotifId(NULL);
		} else {
			$this->setAMotifId($v->getId());
		}

		$this->aAbsenceMotif = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceMotif object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceMotif object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceMotif The associated AbsenceMotif object.
	 * @throws     PropelException
	 */
	public function getAbsenceMotif(PropelPDO $con = null)
	{
		if ($this->aAbsenceMotif === null && ($this->a_motif_id !== null)) {
			$this->aAbsenceMotif = AbsenceMotifPeer::retrieveByPK($this->a_motif_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceMotif->addAbsenceTraitements($this);
			 */
		}
		return $this->aAbsenceMotif;
	}

	/**
	 * Declares an association between this object and a AbsenceJustification object.
	 *
	 * @param      AbsenceJustification $v
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceJustification(AbsenceJustification $v = null)
	{
		if ($v === null) {
			$this->setAJustificationId(NULL);
		} else {
			$this->setAJustificationId($v->getId());
		}

		$this->aAbsenceJustification = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceJustification object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceJustification object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceJustification The associated AbsenceJustification object.
	 * @throws     PropelException
	 */
	public function getAbsenceJustification(PropelPDO $con = null)
	{
		if ($this->aAbsenceJustification === null && ($this->a_justification_id !== null)) {
			$this->aAbsenceJustification = AbsenceJustificationPeer::retrieveByPK($this->a_justification_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceJustification->addAbsenceTraitements($this);
			 */
		}
		return $this->aAbsenceJustification;
	}

	/**
	 * Declares an association between this object and a AbsenceAction object.
	 *
	 * @param      AbsenceAction $v
	 * @return     AbsenceTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceAction(AbsenceAction $v = null)
	{
		if ($v === null) {
			$this->setAActionId(NULL);
		} else {
			$this->setAActionId($v->getId());
		}

		$this->aAbsenceAction = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceAction object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceAction object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceAction The associated AbsenceAction object.
	 * @throws     PropelException
	 */
	public function getAbsenceAction(PropelPDO $con = null)
	{
		if ($this->aAbsenceAction === null && ($this->a_action_id !== null)) {
			$this->aAbsenceAction = AbsenceActionPeer::retrieveByPK($this->a_action_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceAction->addAbsenceTraitements($this);
			 */
		}
		return $this->aAbsenceAction;
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
	 * Otherwise if this AbsenceTraitement has previously been saved, it will retrieve
	 * related JTraitementSaisies from storage. If this AbsenceTraitement is new, it will return
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
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisies === null) {
			if ($this->isNew()) {
			   $this->collJTraitementSaisies = array();
			} else {

				$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

				JTraitementSaisiePeer::addSelectColumns($criteria);
				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

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
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
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

				$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

				$count = JTraitementSaisiePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

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
			$l->setAbsenceTraitement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceTraitement is new, it will return
	 * an empty collection; or if this AbsenceTraitement has previously
	 * been saved, it will retrieve related JTraitementSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceTraitement.
	 */
	public function getJTraitementSaisiesJoinAbsenceSaisie($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisies === null) {
			if ($this->isNew()) {
				$this->collJTraitementSaisies = array();
			} else {

				$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelectJoinAbsenceSaisie($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementSaisiePeer::A_TRAITEMENT_ID, $this->id);

			if (!isset($this->lastJTraitementSaisieCriteria) || !$this->lastJTraitementSaisieCriteria->equals($criteria)) {
				$this->collJTraitementSaisies = JTraitementSaisiePeer::doSelectJoinAbsenceSaisie($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementSaisieCriteria = $criteria;

		return $this->collJTraitementSaisies;
	}

	/**
	 * Clears out the collJTraitementEnvois collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJTraitementEnvois()
	 */
	public function clearJTraitementEnvois()
	{
		$this->collJTraitementEnvois = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJTraitementEnvois collection (array).
	 *
	 * By default this just sets the collJTraitementEnvois collection to an empty array (like clearcollJTraitementEnvois());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJTraitementEnvois()
	{
		$this->collJTraitementEnvois = array();
	}

	/**
	 * Gets an array of JTraitementEnvoi objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceTraitement has previously been saved, it will retrieve
	 * related JTraitementEnvois from storage. If this AbsenceTraitement is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JTraitementEnvoi[]
	 * @throws     PropelException
	 */
	public function getJTraitementEnvois($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementEnvois === null) {
			if ($this->isNew()) {
			   $this->collJTraitementEnvois = array();
			} else {

				$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

				JTraitementEnvoiPeer::addSelectColumns($criteria);
				$this->collJTraitementEnvois = JTraitementEnvoiPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

				JTraitementEnvoiPeer::addSelectColumns($criteria);
				if (!isset($this->lastJTraitementEnvoiCriteria) || !$this->lastJTraitementEnvoiCriteria->equals($criteria)) {
					$this->collJTraitementEnvois = JTraitementEnvoiPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJTraitementEnvoiCriteria = $criteria;
		return $this->collJTraitementEnvois;
	}

	/**
	 * Returns the number of related JTraitementEnvoi objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JTraitementEnvoi objects.
	 * @throws     PropelException
	 */
	public function countJTraitementEnvois(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJTraitementEnvois === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

				$count = JTraitementEnvoiPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

				if (!isset($this->lastJTraitementEnvoiCriteria) || !$this->lastJTraitementEnvoiCriteria->equals($criteria)) {
					$count = JTraitementEnvoiPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJTraitementEnvois);
				}
			} else {
				$count = count($this->collJTraitementEnvois);
			}
		}
		$this->lastJTraitementEnvoiCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JTraitementEnvoi object to this object
	 * through the JTraitementEnvoi foreign key attribute.
	 *
	 * @param      JTraitementEnvoi $l JTraitementEnvoi
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJTraitementEnvoi(JTraitementEnvoi $l)
	{
		if ($this->collJTraitementEnvois === null) {
			$this->initJTraitementEnvois();
		}
		if (!in_array($l, $this->collJTraitementEnvois, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJTraitementEnvois, $l);
			$l->setAbsenceTraitement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceTraitement is new, it will return
	 * an empty collection; or if this AbsenceTraitement has previously
	 * been saved, it will retrieve related JTraitementEnvois from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceTraitement.
	 */
	public function getJTraitementEnvoisJoinAbsenceEnvoi($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementEnvois === null) {
			if ($this->isNew()) {
				$this->collJTraitementEnvois = array();
			} else {

				$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

				$this->collJTraitementEnvois = JTraitementEnvoiPeer::doSelectJoinAbsenceEnvoi($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementEnvoiPeer::A_TRAITEMENT_ID, $this->id);

			if (!isset($this->lastJTraitementEnvoiCriteria) || !$this->lastJTraitementEnvoiCriteria->equals($criteria)) {
				$this->collJTraitementEnvois = JTraitementEnvoiPeer::doSelectJoinAbsenceEnvoi($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementEnvoiCriteria = $criteria;

		return $this->collJTraitementEnvois;
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
			if ($this->collJTraitementEnvois) {
				foreach ((array) $this->collJTraitementEnvois as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJTraitementSaisies = null;
		$this->collJTraitementEnvois = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aAbsenceType = null;
			$this->aAbsenceMotif = null;
			$this->aAbsenceJustification = null;
			$this->aAbsenceAction = null;
	}

} // BaseAbsenceTraitement
