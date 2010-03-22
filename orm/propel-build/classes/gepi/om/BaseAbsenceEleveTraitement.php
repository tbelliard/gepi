<?php

/**
 * Base class that represents a row from the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste à definir les motifs/justifications... de ces absences saisies
 *
 * @package    gepi.om
 */
abstract class BaseAbsenceEleveTraitement extends BaseObject  implements Persistent {


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
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

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
	 * @var        AbsenceEleveAction
	 */
	protected $aAbsenceEleveAction;

	/**
	 * @var        array JTraitementSaisieEleve[] Collection to store aggregation of JTraitementSaisieEleve objects.
	 */
	protected $collJTraitementSaisieEleves;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementSaisieEleves.
	 */
	private $lastJTraitementSaisieEleveCriteria = null;

	/**
	 * @var        array JTraitementEnvoiEleve[] Collection to store aggregation of JTraitementEnvoiEleve objects.
	 */
	protected $collJTraitementEnvoiEleves;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJTraitementEnvoiEleves.
	 */
	private $lastJTraitementEnvoiEleveCriteria = null;

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
	 * Initializes internal state of BaseAbsenceEleveTraitement object.
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
	 * Get the [commentaire] column value.
	 * commentaire saisi par l'utilisateur
	 * @return     string
	 */
	public function getCommentaire()
	{
		return $this->commentaire;
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
	 * Set the value of [texte_justification] column.
	 * Texte additionnel à ce traitement
	 * @param      string $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setTexteJustification($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->texte_justification !== $v) {
			$this->texte_justification = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::TEXTE_JUSTIFICATION;
		}

		return $this;
	} // setTexteJustification()

	/**
	 * Set the value of [a_action_id] column.
	 * cle etrangere de l'action sur ce traitement
	 * @param      int $v new value
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 */
	public function setAActionId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_action_id !== $v) {
			$this->a_action_id = $v;
			$this->modifiedColumns[] = AbsenceEleveTraitementPeer::A_ACTION_ID;
		}

		if ($this->aAbsenceEleveAction !== null && $this->aAbsenceEleveAction->getId() !== $v) {
			$this->aAbsenceEleveAction = null;
		}

		return $this;
	} // setAActionId()

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
			$this->a_type_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->a_motif_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->a_justification_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->texte_justification = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->a_action_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->commentaire = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 8; // 8 = AbsenceEleveTraitementPeer::NUM_COLUMNS - AbsenceEleveTraitementPeer::NUM_LAZY_LOAD_COLUMNS).

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
		if ($this->aAbsenceEleveAction !== null && $this->a_action_id !== $this->aAbsenceEleveAction->getId()) {
			$this->aAbsenceEleveAction = null;
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
			$this->aAbsenceEleveAction = null;
			$this->collJTraitementSaisieEleves = null;
			$this->lastJTraitementSaisieEleveCriteria = null;

			$this->collJTraitementEnvoiEleves = null;
			$this->lastJTraitementEnvoiEleveCriteria = null;

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
			AbsenceEleveTraitementPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AbsenceEleveTraitementPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AbsenceEleveTraitementPeer::addInstanceToPool($this);
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

			if ($this->aAbsenceEleveAction !== null) {
				if ($this->aAbsenceEleveAction->isModified() || $this->aAbsenceEleveAction->isNew()) {
					$affectedRows += $this->aAbsenceEleveAction->save($con);
				}
				$this->setAbsenceEleveAction($this->aAbsenceEleveAction);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceEleveTraitementPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AbsenceEleveTraitementPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

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

			if ($this->collJTraitementEnvoiEleves !== null) {
				foreach ($this->collJTraitementEnvoiEleves as $referrerFK) {
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

			if ($this->aAbsenceEleveAction !== null) {
				if (!$this->aAbsenceEleveAction->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveAction->getValidationFailures());
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

				if ($this->collJTraitementEnvoiEleves !== null) {
					foreach ($this->collJTraitementEnvoiEleves as $referrerFK) {
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
				return $this->getTexteJustification();
				break;
			case 6:
				return $this->getAActionId();
				break;
			case 7:
				return $this->getCommentaire();
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
		$keys = AbsenceEleveTraitementPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getATypeId(),
			$keys[3] => $this->getAMotifId(),
			$keys[4] => $this->getAJustificationId(),
			$keys[5] => $this->getTexteJustification(),
			$keys[6] => $this->getAActionId(),
			$keys[7] => $this->getCommentaire(),
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
				$this->setTexteJustification($value);
				break;
			case 6:
				$this->setAActionId($value);
				break;
			case 7:
				$this->setCommentaire($value);
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
		if (array_key_exists($keys[5], $arr)) $this->setTexteJustification($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAActionId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommentaire($arr[$keys[7]]);
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
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::TEXTE_JUSTIFICATION)) $criteria->add(AbsenceEleveTraitementPeer::TEXTE_JUSTIFICATION, $this->texte_justification);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::A_ACTION_ID)) $criteria->add(AbsenceEleveTraitementPeer::A_ACTION_ID, $this->a_action_id);
		if ($this->isColumnModified(AbsenceEleveTraitementPeer::COMMENTAIRE)) $criteria->add(AbsenceEleveTraitementPeer::COMMENTAIRE, $this->commentaire);

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

		$copyObj->setTexteJustification($this->texte_justification);

		$copyObj->setAActionId($this->a_action_id);

		$copyObj->setCommentaire($this->commentaire);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisieEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisieEleve($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJTraitementEnvoiEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementEnvoiEleve($relObj->copy($deepCopy));
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
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelPeer::retrieveByPK($this->utilisateur_id, $con);
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
			$this->aAbsenceEleveType = AbsenceEleveTypePeer::retrieveByPK($this->a_type_id, $con);
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
			$this->aAbsenceEleveMotif = AbsenceEleveMotifPeer::retrieveByPK($this->a_motif_id, $con);
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
			$this->aAbsenceEleveJustification = AbsenceEleveJustificationPeer::retrieveByPK($this->a_justification_id, $con);
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
	 * Declares an association between this object and a AbsenceEleveAction object.
	 *
	 * @param      AbsenceEleveAction $v
	 * @return     AbsenceEleveTraitement The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveAction(AbsenceEleveAction $v = null)
	{
		if ($v === null) {
			$this->setAActionId(NULL);
		} else {
			$this->setAActionId($v->getId());
		}

		$this->aAbsenceEleveAction = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveAction object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveTraitement($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveAction object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveAction The associated AbsenceEleveAction object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveAction(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveAction === null && ($this->a_action_id !== null)) {
			$this->aAbsenceEleveAction = AbsenceEleveActionPeer::retrieveByPK($this->a_action_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceEleveAction->addAbsenceEleveTraitements($this);
			 */
		}
		return $this->aAbsenceEleveAction;
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
	 * Otherwise if this AbsenceEleveTraitement has previously been saved, it will retrieve
	 * related JTraitementSaisieEleves from storage. If this AbsenceEleveTraitement is new, it will return
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
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisieEleves === null) {
			if ($this->isNew()) {
			   $this->collJTraitementSaisieEleves = array();
			} else {

				$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

				JTraitementSaisieElevePeer::addSelectColumns($criteria);
				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

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
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
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

				$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

				$count = JTraitementSaisieElevePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

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
	 */
	public function getJTraitementSaisieElevesJoinAbsenceEleveSaisie($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementSaisieEleves === null) {
			if ($this->isNew()) {
				$this->collJTraitementSaisieEleves = array();
			} else {

				$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelectJoinAbsenceEleveSaisie($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementSaisieElevePeer::A_TRAITEMENT_ID, $this->id);

			if (!isset($this->lastJTraitementSaisieEleveCriteria) || !$this->lastJTraitementSaisieEleveCriteria->equals($criteria)) {
				$this->collJTraitementSaisieEleves = JTraitementSaisieElevePeer::doSelectJoinAbsenceEleveSaisie($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementSaisieEleveCriteria = $criteria;

		return $this->collJTraitementSaisieEleves;
	}

	/**
	 * Clears out the collJTraitementEnvoiEleves collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJTraitementEnvoiEleves()
	 */
	public function clearJTraitementEnvoiEleves()
	{
		$this->collJTraitementEnvoiEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJTraitementEnvoiEleves collection (array).
	 *
	 * By default this just sets the collJTraitementEnvoiEleves collection to an empty array (like clearcollJTraitementEnvoiEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJTraitementEnvoiEleves()
	{
		$this->collJTraitementEnvoiEleves = array();
	}

	/**
	 * Gets an array of JTraitementEnvoiEleve objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveTraitement has previously been saved, it will retrieve
	 * related JTraitementEnvoiEleves from storage. If this AbsenceEleveTraitement is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JTraitementEnvoiEleve[]
	 * @throws     PropelException
	 */
	public function getJTraitementEnvoiEleves($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementEnvoiEleves === null) {
			if ($this->isNew()) {
			   $this->collJTraitementEnvoiEleves = array();
			} else {

				$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

				JTraitementEnvoiElevePeer::addSelectColumns($criteria);
				$this->collJTraitementEnvoiEleves = JTraitementEnvoiElevePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

				JTraitementEnvoiElevePeer::addSelectColumns($criteria);
				if (!isset($this->lastJTraitementEnvoiEleveCriteria) || !$this->lastJTraitementEnvoiEleveCriteria->equals($criteria)) {
					$this->collJTraitementEnvoiEleves = JTraitementEnvoiElevePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJTraitementEnvoiEleveCriteria = $criteria;
		return $this->collJTraitementEnvoiEleves;
	}

	/**
	 * Returns the number of related JTraitementEnvoiEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JTraitementEnvoiEleve objects.
	 * @throws     PropelException
	 */
	public function countJTraitementEnvoiEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJTraitementEnvoiEleves === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

				$count = JTraitementEnvoiElevePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

				if (!isset($this->lastJTraitementEnvoiEleveCriteria) || !$this->lastJTraitementEnvoiEleveCriteria->equals($criteria)) {
					$count = JTraitementEnvoiElevePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJTraitementEnvoiEleves);
				}
			} else {
				$count = count($this->collJTraitementEnvoiEleves);
			}
		}
		$this->lastJTraitementEnvoiEleveCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JTraitementEnvoiEleve object to this object
	 * through the JTraitementEnvoiEleve foreign key attribute.
	 *
	 * @param      JTraitementEnvoiEleve $l JTraitementEnvoiEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJTraitementEnvoiEleve(JTraitementEnvoiEleve $l)
	{
		if ($this->collJTraitementEnvoiEleves === null) {
			$this->initJTraitementEnvoiEleves();
		}
		if (!in_array($l, $this->collJTraitementEnvoiEleves, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJTraitementEnvoiEleves, $l);
			$l->setAbsenceEleveTraitement($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveTraitement is new, it will return
	 * an empty collection; or if this AbsenceEleveTraitement has previously
	 * been saved, it will retrieve related JTraitementEnvoiEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveTraitement.
	 */
	public function getJTraitementEnvoiElevesJoinAbsenceEleveEnvoi($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AbsenceEleveTraitementPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJTraitementEnvoiEleves === null) {
			if ($this->isNew()) {
				$this->collJTraitementEnvoiEleves = array();
			} else {

				$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

				$this->collJTraitementEnvoiEleves = JTraitementEnvoiElevePeer::doSelectJoinAbsenceEleveEnvoi($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JTraitementEnvoiElevePeer::A_TRAITEMENT_ID, $this->id);

			if (!isset($this->lastJTraitementEnvoiEleveCriteria) || !$this->lastJTraitementEnvoiEleveCriteria->equals($criteria)) {
				$this->collJTraitementEnvoiEleves = JTraitementEnvoiElevePeer::doSelectJoinAbsenceEleveEnvoi($criteria, $con, $join_behavior);
			}
		}
		$this->lastJTraitementEnvoiEleveCriteria = $criteria;

		return $this->collJTraitementEnvoiEleves;
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
			if ($this->collJTraitementEnvoiEleves) {
				foreach ((array) $this->collJTraitementEnvoiEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJTraitementSaisieEleves = null;
		$this->collJTraitementEnvoiEleves = null;
			$this->aUtilisateurProfessionnel = null;
			$this->aAbsenceEleveType = null;
			$this->aAbsenceEleveMotif = null;
			$this->aAbsenceEleveJustification = null;
			$this->aAbsenceEleveAction = null;
	}

} // BaseAbsenceEleveTraitement
