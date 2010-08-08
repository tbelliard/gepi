<?php


/**
 * Base class that represents a row from the 'a_types' table.
 *
 * Liste des types d'absences possibles dans l'etablissement
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveType extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'AbsenceEleveTypePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveTypePeer
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
	 * The value for the justification_exigible field.
	 * @var        boolean
	 */
	protected $justification_exigible;

	/**
	 * The value for the sous_responsabilite_etablissement field.
	 * Note: this column has a database default value of: 'NON_PRECISE'
	 * @var        string
	 */
	protected $sous_responsabilite_etablissement;

	/**
	 * The value for the manquement_obligation_presence field.
	 * Note: this column has a database default value of: 'NON_PRECISE'
	 * @var        string
	 */
	protected $manquement_obligation_presence;

	/**
	 * The value for the retard_bulletin field.
	 * Note: this column has a database default value of: 'NON_PRECISE'
	 * @var        string
	 */
	protected $retard_bulletin;

	/**
	 * The value for the type_saisie field.
	 * Note: this column has a database default value of: 'NON_PRECISE'
	 * @var        string
	 */
	protected $type_saisie;

	/**
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the sortable_rank field.
	 * @var        int
	 */
	protected $sortable_rank;

	/**
	 * @var        array AbsenceEleveTypeStatutAutorise[] Collection to store aggregation of AbsenceEleveTypeStatutAutorise objects.
	 */
	protected $collAbsenceEleveTypeStatutAutorises;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collAbsenceEleveTraitements;

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

	// sortable behavior
	
	/**
	 * Queries to be executed in the save transaction
	 * @var        array
	 */
	protected $sortableQueries = array();

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->sous_responsabilite_etablissement = 'NON_PRECISE';
		$this->manquement_obligation_presence = 'NON_PRECISE';
		$this->retard_bulletin = 'NON_PRECISE';
		$this->type_saisie = 'NON_PRECISE';
	}

	/**
	 * Initializes internal state of BaseAbsenceEleveType object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * Cle primaire auto-incrementee
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [nom] column value.
	 * Nom du type d'absence
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [justification_exigible] column value.
	 * Ce type d'absence doit entrainer une justification de la part de la famille
	 * @return     boolean
	 */
	public function getJustificationExigible()
	{
		return $this->justification_exigible;
	}

	/**
	 * Get the [sous_responsabilite_etablissement] column value.
	 * L'eleve est sous la responsabilite de l'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l'eleve est encore sous la responsabilité de l'etablissement. Possibilite : 'vrai'/'faux'/'non_precise'
	 * @return     string
	 */
	public function getSousResponsabiliteEtablissement()
	{
		return $this->sous_responsabilite_etablissement;
	}

	/**
	 * Get the [manquement_obligation_presence] column value.
	 * L'eleve manque à ses obligations de presence (L'absence apparait sur le bulletin). Possibilite : 'vrai'/'faux'/'non_precise'
	 * @return     string
	 */
	public function getManquementObligationPresence()
	{
		return $this->manquement_obligation_presence;
	}

	/**
	 * Get the [retard_bulletin] column value.
	 * La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : 'vrai'/'faux'/'non_precise'
	 * @return     string
	 */
	public function getRetardBulletin()
	{
		return $this->retard_bulletin;
	}

	/**
	 * Get the [type_saisie] column value.
	 * Enumeration des possibilités de l'interface de saisie de l'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE
	 * @return     string
	 */
	public function getTypeSaisie()
	{
		return $this->type_saisie;
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
	 * Get the [sortable_rank] column value.
	 * 
	 * @return     int
	 */
	public function getSortableRank()
	{
		return $this->sortable_rank;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire auto-incrementee
	 * @param      int $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom] column.
	 * Nom du type d'absence
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [justification_exigible] column.
	 * Ce type d'absence doit entrainer une justification de la part de la famille
	 * @param      boolean $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setJustificationExigible($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->justification_exigible !== $v) {
			$this->justification_exigible = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE;
		}

		return $this;
	} // setJustificationExigible()

	/**
	 * Set the value of [sous_responsabilite_etablissement] column.
	 * L'eleve est sous la responsabilite de l'etablissement. Typiquement : absence infirmerie, mettre la propriété à vrai car l'eleve est encore sous la responsabilité de l'etablissement. Possibilite : 'vrai'/'faux'/'non_precise'
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setSousResponsabiliteEtablissement($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->sous_responsabilite_etablissement !== $v || $this->isNew()) {
			$this->sous_responsabilite_etablissement = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::SOUS_RESPONSABILITE_ETABLISSEMENT;
		}

		return $this;
	} // setSousResponsabiliteEtablissement()

	/**
	 * Set the value of [manquement_obligation_presence] column.
	 * L'eleve manque à ses obligations de presence (L'absence apparait sur le bulletin). Possibilite : 'vrai'/'faux'/'non_precise'
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setManquementObligationPresence($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->manquement_obligation_presence !== $v || $this->isNew()) {
			$this->manquement_obligation_presence = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::MANQUEMENT_OBLIGATION_PRESENCE;
		}

		return $this;
	} // setManquementObligationPresence()

	/**
	 * Set the value of [retard_bulletin] column.
	 * La saisie est comptabilisée dans le bulletin en tant que retard. Possibilite : 'vrai'/'faux'/'non_precise'
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setRetardBulletin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->retard_bulletin !== $v || $this->isNew()) {
			$this->retard_bulletin = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::RETARD_BULLETIN;
		}

		return $this;
	} // setRetardBulletin()

	/**
	 * Set the value of [type_saisie] column.
	 * Enumeration des possibilités de l'interface de saisie de l'absence pour ce type : DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE, DISCIPLINE
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setTypeSaisie($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type_saisie !== $v || $this->isNew()) {
			$this->type_saisie = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::TYPE_SAISIE;
		}

		return $this;
	} // setTypeSaisie()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire saisi par l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Set the value of [sortable_rank] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveType The current object (for fluent API support)
	 */
	public function setSortableRank($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->sortable_rank !== $v) {
			$this->sortable_rank = $v;
			$this->modifiedColumns[] = AbsenceEleveTypePeer::SORTABLE_RANK;
		}

		return $this;
	} // setSortableRank()

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
			if ($this->sous_responsabilite_etablissement !== 'NON_PRECISE') {
				return false;
			}

			if ($this->manquement_obligation_presence !== 'NON_PRECISE') {
				return false;
			}

			if ($this->retard_bulletin !== 'NON_PRECISE') {
				return false;
			}

			if ($this->type_saisie !== 'NON_PRECISE') {
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
			$this->justification_exigible = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
			$this->sous_responsabilite_etablissement = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->manquement_obligation_presence = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->retard_bulletin = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->type_saisie = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->commentaire = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->sortable_rank = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 9; // 9 = AbsenceEleveTypePeer::NUM_COLUMNS - AbsenceEleveTypePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveType object", $e);
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveTypePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collAbsenceEleveTypeStatutAutorises = null;

			$this->collAbsenceEleveTraitements = null;

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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			// sortable behavior
			
			AbsenceEleveTypePeer::shiftRank(-1, $this->getSortableRank() + 1, null, $con);
			AbsenceEleveTypePeer::clearInstancePool();

			if ($ret) {
				AbsenceEleveTypeQuery::create()
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
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// sortable behavior
			$this->processSortableQueries($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// sortable behavior
				if (!$this->isColumnModified(AbsenceEleveTypePeer::RANK_COL)) {
					$this->setSortableRank(AbsenceEleveTypeQuery::create()->getMaxRank($con) + 1);
				}

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
				AbsenceEleveTypePeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = AbsenceEleveTypePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(AbsenceEleveTypePeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveTypePeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = AbsenceEleveTypePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAbsenceEleveTypeStatutAutorises !== null) {
				foreach ($this->collAbsenceEleveTypeStatutAutorises as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveTraitements !== null) {
				foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
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


			if (($retval = AbsenceEleveTypePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAbsenceEleveTypeStatutAutorises !== null) {
					foreach ($this->collAbsenceEleveTypeStatutAutorises as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveTraitements !== null) {
					foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
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
		$pos = AbsenceEleveTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getJustificationExigible();
				break;
			case 3:
				return $this->getSousResponsabiliteEtablissement();
				break;
			case 4:
				return $this->getManquementObligationPresence();
				break;
			case 5:
				return $this->getRetardBulletin();
				break;
			case 6:
				return $this->getTypeSaisie();
				break;
			case 7:
				return $this->getCommentaire();
				break;
			case 8:
				return $this->getSortableRank();
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
		$keys = AbsenceEleveTypePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getJustificationExigible(),
			$keys[3] => $this->getSousResponsabiliteEtablissement(),
			$keys[4] => $this->getManquementObligationPresence(),
			$keys[5] => $this->getRetardBulletin(),
			$keys[6] => $this->getTypeSaisie(),
			$keys[7] => $this->getCommentaire(),
			$keys[8] => $this->getSortableRank(),
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
		$pos = AbsenceEleveTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setJustificationExigible($value);
				break;
			case 3:
				$this->setSousResponsabiliteEtablissement($value);
				break;
			case 4:
				$this->setManquementObligationPresence($value);
				break;
			case 5:
				$this->setRetardBulletin($value);
				break;
			case 6:
				$this->setTypeSaisie($value);
				break;
			case 7:
				$this->setCommentaire($value);
				break;
			case 8:
				$this->setSortableRank($value);
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
		$keys = AbsenceEleveTypePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setJustificationExigible($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSousResponsabiliteEtablissement($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setManquementObligationPresence($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setRetardBulletin($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setTypeSaisie($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommentaire($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setSortableRank($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveTypePeer::ID)) $criteria->add(AbsenceEleveTypePeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveTypePeer::NOM)) $criteria->add(AbsenceEleveTypePeer::NOM, $this->nom);
		if ($this->isColumnModified(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE)) $criteria->add(AbsenceEleveTypePeer::JUSTIFICATION_EXIGIBLE, $this->justification_exigible);
		if ($this->isColumnModified(AbsenceEleveTypePeer::SOUS_RESPONSABILITE_ETABLISSEMENT)) $criteria->add(AbsenceEleveTypePeer::SOUS_RESPONSABILITE_ETABLISSEMENT, $this->sous_responsabilite_etablissement);
		if ($this->isColumnModified(AbsenceEleveTypePeer::MANQUEMENT_OBLIGATION_PRESENCE)) $criteria->add(AbsenceEleveTypePeer::MANQUEMENT_OBLIGATION_PRESENCE, $this->manquement_obligation_presence);
		if ($this->isColumnModified(AbsenceEleveTypePeer::RETARD_BULLETIN)) $criteria->add(AbsenceEleveTypePeer::RETARD_BULLETIN, $this->retard_bulletin);
		if ($this->isColumnModified(AbsenceEleveTypePeer::TYPE_SAISIE)) $criteria->add(AbsenceEleveTypePeer::TYPE_SAISIE, $this->type_saisie);
		if ($this->isColumnModified(AbsenceEleveTypePeer::COMMENTAIRE)) $criteria->add(AbsenceEleveTypePeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveTypePeer::SORTABLE_RANK)) $criteria->add(AbsenceEleveTypePeer::SORTABLE_RANK, $this->sortable_rank);

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
		$criteria = new Criteria(AbsenceEleveTypePeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveTypePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveType (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setNom($this->nom);
		$copyObj->setJustificationExigible($this->justification_exigible);
		$copyObj->setSousResponsabiliteEtablissement($this->sous_responsabilite_etablissement);
		$copyObj->setManquementObligationPresence($this->manquement_obligation_presence);
		$copyObj->setRetardBulletin($this->retard_bulletin);
		$copyObj->setTypeSaisie($this->type_saisie);
		$copyObj->setCommentaire($this->commentaire);
		$copyObj->setSortableRank($this->sortable_rank);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getAbsenceEleveTypeStatutAutorises() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTypeStatutAutorise($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveTraitements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTraitement($relObj->copy($deepCopy));
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
	 * @return     AbsenceEleveType Clone of current object.
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
	 * @return     AbsenceEleveTypePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveTypePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collAbsenceEleveTypeStatutAutorises collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveTypeStatutAutorises()
	 */
	public function clearAbsenceEleveTypeStatutAutorises()
	{
		$this->collAbsenceEleveTypeStatutAutorises = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveTypeStatutAutorises collection.
	 *
	 * By default this just sets the collAbsenceEleveTypeStatutAutorises collection to an empty array (like clearcollAbsenceEleveTypeStatutAutorises());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTypeStatutAutorises()
	{
		$this->collAbsenceEleveTypeStatutAutorises = new PropelObjectCollection();
		$this->collAbsenceEleveTypeStatutAutorises->setModel('AbsenceEleveTypeStatutAutorise');
	}

	/**
	 * Gets an array of AbsenceEleveTypeStatutAutorise objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveType is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveTypeStatutAutorise[] List of AbsenceEleveTypeStatutAutorise objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTypeStatutAutorises($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTypeStatutAutorises || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTypeStatutAutorises) {
				// return empty collection
				$this->initAbsenceEleveTypeStatutAutorises();
			} else {
				$collAbsenceEleveTypeStatutAutorises = AbsenceEleveTypeStatutAutoriseQuery::create(null, $criteria)
					->filterByAbsenceEleveType($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveTypeStatutAutorises;
				}
				$this->collAbsenceEleveTypeStatutAutorises = $collAbsenceEleveTypeStatutAutorises;
			}
		}
		return $this->collAbsenceEleveTypeStatutAutorises;
	}

	/**
	 * Returns the number of related AbsenceEleveTypeStatutAutorise objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveTypeStatutAutorise objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveTypeStatutAutorises(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTypeStatutAutorises || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTypeStatutAutorises) {
				return 0;
			} else {
				$query = AbsenceEleveTypeStatutAutoriseQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveType($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveTypeStatutAutorises);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveTypeStatutAutorise object to this object
	 * through the AbsenceEleveTypeStatutAutorise foreign key attribute.
	 *
	 * @param      AbsenceEleveTypeStatutAutorise $l AbsenceEleveTypeStatutAutorise
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveTypeStatutAutorise(AbsenceEleveTypeStatutAutorise $l)
	{
		if ($this->collAbsenceEleveTypeStatutAutorises === null) {
			$this->initAbsenceEleveTypeStatutAutorises();
		}
		if (!$this->collAbsenceEleveTypeStatutAutorises->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveTypeStatutAutorises[]= $l;
			$l->setAbsenceEleveType($this);
		}
	}

	/**
	 * Clears out the collAbsenceEleveTraitements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveTraitements()
	 */
	public function clearAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveTraitements collection.
	 *
	 * By default this just sets the collAbsenceEleveTraitements collection to an empty array (like clearcollAbsenceEleveTraitements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = new PropelObjectCollection();
		$this->collAbsenceEleveTraitements->setModel('AbsenceEleveTraitement');
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveType is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				// return empty collection
				$this->initAbsenceEleveTraitements();
			} else {
				$collAbsenceEleveTraitements = AbsenceEleveTraitementQuery::create(null, $criteria)
					->filterByAbsenceEleveType($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveTraitements;
				}
				$this->collAbsenceEleveTraitements = $collAbsenceEleveTraitements;
			}
		}
		return $this->collAbsenceEleveTraitements;
	}

	/**
	 * Returns the number of related AbsenceEleveTraitement objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveTraitement objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveTraitements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				return 0;
			} else {
				$query = AbsenceEleveTraitementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveType($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveTraitements);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveTraitement object to this object
	 * through the AbsenceEleveTraitement foreign key attribute.
	 *
	 * @param      AbsenceEleveTraitement $l AbsenceEleveTraitement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collAbsenceEleveTraitements === null) {
			$this->initAbsenceEleveTraitements();
		}
		if (!$this->collAbsenceEleveTraitements->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveTraitements[]= $l;
			$l->setAbsenceEleveType($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveMotif($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveMotif', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveJustification($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveJustification', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveType is new, it will return
	 * an empty collection; or if this AbsenceEleveType has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveType.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinModifieParUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('ModifieParUtilisateur', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->nom = null;
		$this->justification_exigible = null;
		$this->sous_responsabilite_etablissement = null;
		$this->manquement_obligation_presence = null;
		$this->retard_bulletin = null;
		$this->type_saisie = null;
		$this->commentaire = null;
		$this->sortable_rank = null;
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
			if ($this->collAbsenceEleveTypeStatutAutorises) {
				foreach ((array) $this->collAbsenceEleveTypeStatutAutorises as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ((array) $this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collAbsenceEleveTypeStatutAutorises = null;
		$this->collAbsenceEleveTraitements = null;
	}

	// sortable behavior
	
	/**
	 * Wrap the getter for rank value
	 *
	 * @return    int
	 */
	public function getRank()
	{
		return $this->sortable_rank;
	}
	
	/**
	 * Wrap the setter for rank value
	 *
	 * @param     int
	 * @return    AbsenceEleveType
	 */
	public function setRank($v)
	{
		return $this->setSortableRank($v);
	}
	
	/**
	 * Check if the object is first in the list, i.e. if it has 1 for rank
	 *
	 * @return    boolean
	 */
	public function isFirst()
	{
		return $this->getSortableRank() == 1;
	}
	
	/**
	 * Check if the object is last in the list, i.e. if its rank is the highest rank
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    boolean
	 */
	public function isLast(PropelPDO $con = null)
	{
		return $this->getSortableRank() == AbsenceEleveTypeQuery::create()->getMaxRank($con);
	}
	
	/**
	 * Get the next item in the list, i.e. the one for which rank is immediately higher
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveType
	 */
	public function getNext(PropelPDO $con = null)
	{
		return AbsenceEleveTypeQuery::create()->findOneByRank($this->getSortableRank() + 1, $con);
	}
	
	/**
	 * Get the previous item in the list, i.e. the one for which rank is immediately lower
	 *
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveType
	 */
	public function getPrevious(PropelPDO $con = null)
	{
		return AbsenceEleveTypeQuery::create()->findOneByRank($this->getSortableRank() - 1, $con);
	}
	
	/**
	 * Insert at specified rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @param     integer    $rank rank value
	 * @param     PropelPDO  $con      optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 *
	 * @throws    PropelException
	 */
	public function insertAtRank($rank, PropelPDO $con = null)
	{
		$maxRank = AbsenceEleveTypeQuery::create()->getMaxRank($con);
		if ($rank < 1 || $rank > $maxRank + 1) {
			throw new PropelException('Invalid rank ' . $rank);
		}
		// move the object in the list, at the given rank
		$this->setSortableRank($rank);
		if ($rank != $maxRank + 1) {
			// Keep the list modification query for the save() transaction
			$this->sortableQueries []= array(
				'callable'  => array('AbsenceEleveTypePeer', 'shiftRank'),
				'arguments' => array(1, $rank, null, )
			);
		}
		
		return $this;
	}
	
	/**
	 * Insert in the last rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @param PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 *
	 * @throws    PropelException
	 */
	public function insertAtBottom(PropelPDO $con = null)
	{
		$this->setSortableRank(AbsenceEleveTypeQuery::create()->getMaxRank($con) + 1);
		
		return $this;
	}
	
	/**
	 * Insert in the first rank
	 * The modifications are not persisted until the object is saved.
	 *
	 * @return    AbsenceEleveType the current object
	 */
	public function insertAtTop()
	{
		return $this->insertAtRank(1);
	}
	
	/**
	 * Move the object to a new rank, and shifts the rank
	 * Of the objects inbetween the old and new rank accordingly
	 *
	 * @param     integer   $newRank rank value
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 *
	 * @throws    PropelException
	 */
	public function moveToRank($newRank, PropelPDO $con = null)
	{
		if ($this->isNew()) {
			throw new PropelException('New objects cannot be moved. Please use insertAtRank() instead');
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		if ($newRank < 1 || $newRank > AbsenceEleveTypeQuery::create()->getMaxRank($con)) {
			throw new PropelException('Invalid rank ' . $newRank);
		}
	
		$oldRank = $this->getSortableRank();
		if ($oldRank == $newRank) {
			return $this;
		}
		
		$con->beginTransaction();
		try {
			// shift the objects between the old and the new rank
			$delta = ($oldRank < $newRank) ? -1 : 1;
			AbsenceEleveTypePeer::shiftRank($delta, min($oldRank, $newRank), max($oldRank, $newRank), $con);
				
			// move the object to its new rank
			$this->setSortableRank($newRank);
			$this->save($con);
			
			$con->commit();
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Exchange the rank of the object with the one passed as argument, and saves both objects
	 *
	 * @param     AbsenceEleveType $object
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 *
	 * @throws Exception if the database cannot execute the two updates
	 */
	public function swapWith($object, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$oldRank = $this->getSortableRank();
			$newRank = $object->getSortableRank();
			$this->setSortableRank($newRank);
			$this->save($con);
			$object->setSortableRank($oldRank);
			$object->save($con);
			$con->commit();
			
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object higher in the list, i.e. exchanges its rank with the one of the previous object
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 */
	public function moveUp(PropelPDO $con = null)
	{
		if ($this->isFirst()) {
			return $this;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$prev = $this->getPrevious($con);
			$this->swapWith($prev, $con);
			$con->commit();
			
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object higher in the list, i.e. exchanges its rank with the one of the next object
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 */
	public function moveDown(PropelPDO $con = null)
	{
		if ($this->isLast($con)) {
			return $this;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$next = $this->getNext($con);
			$this->swapWith($next, $con);
			$con->commit();
			
			return $this;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Move the object to the top of the list
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return    AbsenceEleveType the current object
	 */
	public function moveToTop(PropelPDO $con = null)
	{
		if ($this->isFirst()) {
			return $this;
		}
		return $this->moveToRank(1, $con);
	}
	
	/**
	 * Move the object to the bottom of the list
	 *
	 * @param     PropelPDO $con optional connection
	 *
	 * @return integer the old object's rank
	 */
	public function moveToBottom(PropelPDO $con = null)
	{
		if ($this->isLast($con)) {
			return false;
		}
		if ($con === null) {
			$con = Propel::getConnection(AbsenceEleveTypePeer::DATABASE_NAME);
		}
		$con->beginTransaction();
		try {
			$bottom = AbsenceEleveTypeQuery::create()->getMaxRank($con);
			$res = $this->moveToRank($bottom, $con);
			$con->commit();
			
			return $res;
		} catch (Exception $e) {
			$con->rollback();
			throw $e;
		}
	}
	
	/**
	 * Removes the current object from the list.
	 * The modifications are not persisted until the object is saved.
	 *
	 * @return    AbsenceEleveType the current object
	 */
	public function removeFromList()
	{
		// Keep the list modification query for the save() transaction
		$this->sortableQueries []= array(
			'callable'  => array('AbsenceEleveTypePeer', 'shiftRank'),
			'arguments' => array(-1, $this->getSortableRank() + 1, null)
		);
		// remove the object from the list
		$this->setSortableRank(null);
		
		return $this;
	}
	
	/**
	 * Execute queries that were saved to be run inside the save transaction
	 */
	protected function processSortableQueries($con)
	{
		foreach ($this->sortableQueries as $query) {
			$query['arguments'][]= $con;
			call_user_func_array($query['callable'], $query['arguments']);
		}
		$this->sortableQueries = array();
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

} // BaseAbsenceEleveType
