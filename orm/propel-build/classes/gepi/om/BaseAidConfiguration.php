<?php


/**
 * Base class that represents a row from the 'aid_config' table.
 *
 * Liste des categories d'AID (Activites inter-Disciplinaires)
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAidConfiguration extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AidConfigurationPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AidConfigurationPeer
	 */
	protected static $peer;

	/**
	 * The value for the nom field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the nom_complet field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $nom_complet;

	/**
	 * The value for the note_max field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $note_max;

	/**
	 * The value for the order_display1 field.
	 * Note: this column has a database default value of: '0'
	 * @var        string
	 */
	protected $order_display1;

	/**
	 * The value for the order_display2 field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $order_display2;

	/**
	 * The value for the type_note field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $type_note;

	/**
	 * The value for the display_begin field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $display_begin;

	/**
	 * The value for the display_end field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $display_end;

	/**
	 * The value for the message field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $message;

	/**
	 * The value for the display_nom field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $display_nom;

	/**
	 * The value for the indice_aid field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $indice_aid;

	/**
	 * The value for the display_bulletin field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $display_bulletin;

	/**
	 * The value for the bull_simplifie field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $bull_simplifie;

	/**
	 * The value for the outils_complementaires field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $outils_complementaires;

	/**
	 * The value for the feuille_presence field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $feuille_presence;

	/**
	 * @var        array AidDetails[] Collection to store aggregation of AidDetails objects.
	 */
	protected $collAidDetailss;

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
		$this->nom = '';
		$this->nom_complet = '';
		$this->note_max = 0;
		$this->order_display1 = '0';
		$this->order_display2 = 0;
		$this->type_note = '';
		$this->display_begin = 0;
		$this->display_end = 0;
		$this->message = '';
		$this->display_nom = '';
		$this->indice_aid = 0;
		$this->display_bulletin = 'y';
		$this->bull_simplifie = 'y';
		$this->outils_complementaires = 'n';
		$this->feuille_presence = 'n';
	}

	/**
	 * Initializes internal state of BaseAidConfiguration object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [nom] column value.
	 * Nom de la categorie d'AID
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [nom_complet] column value.
	 * Nom complet de la categorie d'AID
	 * @return     string
	 */
	public function getNomComplet()
	{
		return $this->nom_complet;
	}

	/**
	 * Get the [note_max] column value.
	 * Note maximum qu'on peut mettre pour cette categorie d'AID
	 * @return     int
	 */
	public function getNoteMax()
	{
		return $this->note_max;
	}

	/**
	 * Get the [order_display1] column value.
	 * 
	 * @return     string
	 */
	public function getOrderDisplay1()
	{
		return $this->order_display1;
	}

	/**
	 * Get the [order_display2] column value.
	 * 
	 * @return     int
	 */
	public function getOrderDisplay2()
	{
		return $this->order_display2;
	}

	/**
	 * Get the [type_note] column value.
	 * A no si cette AID n'est pas notee
	 * @return     string
	 */
	public function getTypeNote()
	{
		return $this->type_note;
	}

	/**
	 * Get the [display_begin] column value.
	 * Numero de la periode de debut de cette categorie d'AID
	 * @return     int
	 */
	public function getDisplayBegin()
	{
		return $this->display_begin;
	}

	/**
	 * Get the [display_end] column value.
	 * Numero de la periode de fin de cette categorie d'AID
	 * @return     int
	 */
	public function getDisplayEnd()
	{
		return $this->display_end;
	}

	/**
	 * Get the [message] column value.
	 * 
	 * @return     string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Get the [display_nom] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayNom()
	{
		return $this->display_nom;
	}

	/**
	 * Get the [indice_aid] column value.
	 * cle primaire de chaque categorie d'AID
	 * @return     int
	 */
	public function getIndiceAid()
	{
		return $this->indice_aid;
	}

	/**
	 * Get the [display_bulletin] column value.
	 * Pour savoir si cette categorie d'AID est presente sur le bulletin classique
	 * @return     string
	 */
	public function getDisplayBulletin()
	{
		return $this->display_bulletin;
	}

	/**
	 * Get the [bull_simplifie] column value.
	 * Pour savoir si cette categorie d'AID est presente sur le bulletin simplifie
	 * @return     string
	 */
	public function getBullSimplifie()
	{
		return $this->bull_simplifie;
	}

	/**
	 * Get the [outils_complementaires] column value.
	 * 
	 * @return     string
	 */
	public function getOutilsComplementaires()
	{
		return $this->outils_complementaires;
	}

	/**
	 * Get the [feuille_presence] column value.
	 * 
	 * @return     string
	 */
	public function getFeuillePresence()
	{
		return $this->feuille_presence;
	}

	/**
	 * Set the value of [nom] column.
	 * Nom de la categorie d'AID
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v || $this->isNew()) {
			$this->nom = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [nom_complet] column.
	 * Nom complet de la categorie d'AID
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setNomComplet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_complet !== $v || $this->isNew()) {
			$this->nom_complet = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::NOM_COMPLET;
		}

		return $this;
	} // setNomComplet()

	/**
	 * Set the value of [note_max] column.
	 * Note maximum qu'on peut mettre pour cette categorie d'AID
	 * @param      int $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setNoteMax($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->note_max !== $v || $this->isNew()) {
			$this->note_max = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::NOTE_MAX;
		}

		return $this;
	} // setNoteMax()

	/**
	 * Set the value of [order_display1] column.
	 * 
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setOrderDisplay1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->order_display1 !== $v || $this->isNew()) {
			$this->order_display1 = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::ORDER_DISPLAY1;
		}

		return $this;
	} // setOrderDisplay1()

	/**
	 * Set the value of [order_display2] column.
	 * 
	 * @param      int $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setOrderDisplay2($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->order_display2 !== $v || $this->isNew()) {
			$this->order_display2 = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::ORDER_DISPLAY2;
		}

		return $this;
	} // setOrderDisplay2()

	/**
	 * Set the value of [type_note] column.
	 * A no si cette AID n'est pas notee
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setTypeNote($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type_note !== $v || $this->isNew()) {
			$this->type_note = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::TYPE_NOTE;
		}

		return $this;
	} // setTypeNote()

	/**
	 * Set the value of [display_begin] column.
	 * Numero de la periode de debut de cette categorie d'AID
	 * @param      int $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setDisplayBegin($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_begin !== $v || $this->isNew()) {
			$this->display_begin = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::DISPLAY_BEGIN;
		}

		return $this;
	} // setDisplayBegin()

	/**
	 * Set the value of [display_end] column.
	 * Numero de la periode de fin de cette categorie d'AID
	 * @param      int $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setDisplayEnd($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_end !== $v || $this->isNew()) {
			$this->display_end = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::DISPLAY_END;
		}

		return $this;
	} // setDisplayEnd()

	/**
	 * Set the value of [message] column.
	 * 
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setMessage($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->message !== $v || $this->isNew()) {
			$this->message = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::MESSAGE;
		}

		return $this;
	} // setMessage()

	/**
	 * Set the value of [display_nom] column.
	 * 
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setDisplayNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_nom !== $v || $this->isNew()) {
			$this->display_nom = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::DISPLAY_NOM;
		}

		return $this;
	} // setDisplayNom()

	/**
	 * Set the value of [indice_aid] column.
	 * cle primaire de chaque categorie d'AID
	 * @param      int $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setIndiceAid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->indice_aid !== $v || $this->isNew()) {
			$this->indice_aid = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::INDICE_AID;
		}

		return $this;
	} // setIndiceAid()

	/**
	 * Set the value of [display_bulletin] column.
	 * Pour savoir si cette categorie d'AID est presente sur le bulletin classique
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setDisplayBulletin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_bulletin !== $v || $this->isNew()) {
			$this->display_bulletin = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::DISPLAY_BULLETIN;
		}

		return $this;
	} // setDisplayBulletin()

	/**
	 * Set the value of [bull_simplifie] column.
	 * Pour savoir si cette categorie d'AID est presente sur le bulletin simplifie
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setBullSimplifie($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->bull_simplifie !== $v || $this->isNew()) {
			$this->bull_simplifie = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::BULL_SIMPLIFIE;
		}

		return $this;
	} // setBullSimplifie()

	/**
	 * Set the value of [outils_complementaires] column.
	 * 
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setOutilsComplementaires($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->outils_complementaires !== $v || $this->isNew()) {
			$this->outils_complementaires = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::OUTILS_COMPLEMENTAIRES;
		}

		return $this;
	} // setOutilsComplementaires()

	/**
	 * Set the value of [feuille_presence] column.
	 * 
	 * @param      string $v new value
	 * @return     AidConfiguration The current object (for fluent API support)
	 */
	public function setFeuillePresence($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->feuille_presence !== $v || $this->isNew()) {
			$this->feuille_presence = $v;
			$this->modifiedColumns[] = AidConfigurationPeer::FEUILLE_PRESENCE;
		}

		return $this;
	} // setFeuillePresence()

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
			if ($this->nom !== '') {
				return false;
			}

			if ($this->nom_complet !== '') {
				return false;
			}

			if ($this->note_max !== 0) {
				return false;
			}

			if ($this->order_display1 !== '0') {
				return false;
			}

			if ($this->order_display2 !== 0) {
				return false;
			}

			if ($this->type_note !== '') {
				return false;
			}

			if ($this->display_begin !== 0) {
				return false;
			}

			if ($this->display_end !== 0) {
				return false;
			}

			if ($this->message !== '') {
				return false;
			}

			if ($this->display_nom !== '') {
				return false;
			}

			if ($this->indice_aid !== 0) {
				return false;
			}

			if ($this->display_bulletin !== 'y') {
				return false;
			}

			if ($this->bull_simplifie !== 'y') {
				return false;
			}

			if ($this->outils_complementaires !== 'n') {
				return false;
			}

			if ($this->feuille_presence !== 'n') {
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

			$this->nom = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->nom_complet = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->note_max = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->order_display1 = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->order_display2 = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->type_note = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->display_begin = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->display_end = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->message = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->display_nom = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->indice_aid = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->display_bulletin = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->bull_simplifie = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->outils_complementaires = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->feuille_presence = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 15; // 15 = AidConfigurationPeer::NUM_COLUMNS - AidConfigurationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AidConfiguration object", $e);
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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AidConfigurationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?
			$this->collAidDetailss = null;
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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AidConfigurationQuery::create()
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
			$con = Propel::getConnection(AidConfigurationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AidConfigurationPeer::addInstanceToPool($this);
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


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setNew(false);
				} else {
					$affectedRows = AidConfigurationPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collAidDetailss !== null) {
				foreach ($this->collAidDetailss as $referrerFK) {
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


			if (($retval = AidConfigurationPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collAidDetailss !== null) {
					foreach ($this->collAidDetailss as $referrerFK) {
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
		$pos = AidConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNom();
				break;
			case 1:
				return $this->getNomComplet();
				break;
			case 2:
				return $this->getNoteMax();
				break;
			case 3:
				return $this->getOrderDisplay1();
				break;
			case 4:
				return $this->getOrderDisplay2();
				break;
			case 5:
				return $this->getTypeNote();
				break;
			case 6:
				return $this->getDisplayBegin();
				break;
			case 7:
				return $this->getDisplayEnd();
				break;
			case 8:
				return $this->getMessage();
				break;
			case 9:
				return $this->getDisplayNom();
				break;
			case 10:
				return $this->getIndiceAid();
				break;
			case 11:
				return $this->getDisplayBulletin();
				break;
			case 12:
				return $this->getBullSimplifie();
				break;
			case 13:
				return $this->getOutilsComplementaires();
				break;
			case 14:
				return $this->getFeuillePresence();
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
		$keys = AidConfigurationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getNom(),
			$keys[1] => $this->getNomComplet(),
			$keys[2] => $this->getNoteMax(),
			$keys[3] => $this->getOrderDisplay1(),
			$keys[4] => $this->getOrderDisplay2(),
			$keys[5] => $this->getTypeNote(),
			$keys[6] => $this->getDisplayBegin(),
			$keys[7] => $this->getDisplayEnd(),
			$keys[8] => $this->getMessage(),
			$keys[9] => $this->getDisplayNom(),
			$keys[10] => $this->getIndiceAid(),
			$keys[11] => $this->getDisplayBulletin(),
			$keys[12] => $this->getBullSimplifie(),
			$keys[13] => $this->getOutilsComplementaires(),
			$keys[14] => $this->getFeuillePresence(),
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
		$pos = AidConfigurationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNom($value);
				break;
			case 1:
				$this->setNomComplet($value);
				break;
			case 2:
				$this->setNoteMax($value);
				break;
			case 3:
				$this->setOrderDisplay1($value);
				break;
			case 4:
				$this->setOrderDisplay2($value);
				break;
			case 5:
				$this->setTypeNote($value);
				break;
			case 6:
				$this->setDisplayBegin($value);
				break;
			case 7:
				$this->setDisplayEnd($value);
				break;
			case 8:
				$this->setMessage($value);
				break;
			case 9:
				$this->setDisplayNom($value);
				break;
			case 10:
				$this->setIndiceAid($value);
				break;
			case 11:
				$this->setDisplayBulletin($value);
				break;
			case 12:
				$this->setBullSimplifie($value);
				break;
			case 13:
				$this->setOutilsComplementaires($value);
				break;
			case 14:
				$this->setFeuillePresence($value);
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
		$keys = AidConfigurationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setNom($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNomComplet($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNoteMax($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setOrderDisplay1($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setOrderDisplay2($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTypeNote($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDisplayBegin($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDisplayEnd($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMessage($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDisplayNom($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setIndiceAid($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDisplayBulletin($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setBullSimplifie($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setOutilsComplementaires($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setFeuillePresence($arr[$keys[14]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AidConfigurationPeer::DATABASE_NAME);

		if ($this->isColumnModified(AidConfigurationPeer::NOM)) $criteria->add(AidConfigurationPeer::NOM, $this->nom);
		if ($this->isColumnModified(AidConfigurationPeer::NOM_COMPLET)) $criteria->add(AidConfigurationPeer::NOM_COMPLET, $this->nom_complet);
		if ($this->isColumnModified(AidConfigurationPeer::NOTE_MAX)) $criteria->add(AidConfigurationPeer::NOTE_MAX, $this->note_max);
		if ($this->isColumnModified(AidConfigurationPeer::ORDER_DISPLAY1)) $criteria->add(AidConfigurationPeer::ORDER_DISPLAY1, $this->order_display1);
		if ($this->isColumnModified(AidConfigurationPeer::ORDER_DISPLAY2)) $criteria->add(AidConfigurationPeer::ORDER_DISPLAY2, $this->order_display2);
		if ($this->isColumnModified(AidConfigurationPeer::TYPE_NOTE)) $criteria->add(AidConfigurationPeer::TYPE_NOTE, $this->type_note);
		if ($this->isColumnModified(AidConfigurationPeer::DISPLAY_BEGIN)) $criteria->add(AidConfigurationPeer::DISPLAY_BEGIN, $this->display_begin);
		if ($this->isColumnModified(AidConfigurationPeer::DISPLAY_END)) $criteria->add(AidConfigurationPeer::DISPLAY_END, $this->display_end);
		if ($this->isColumnModified(AidConfigurationPeer::MESSAGE)) $criteria->add(AidConfigurationPeer::MESSAGE, $this->message);
		if ($this->isColumnModified(AidConfigurationPeer::DISPLAY_NOM)) $criteria->add(AidConfigurationPeer::DISPLAY_NOM, $this->display_nom);
		if ($this->isColumnModified(AidConfigurationPeer::INDICE_AID)) $criteria->add(AidConfigurationPeer::INDICE_AID, $this->indice_aid);
		if ($this->isColumnModified(AidConfigurationPeer::DISPLAY_BULLETIN)) $criteria->add(AidConfigurationPeer::DISPLAY_BULLETIN, $this->display_bulletin);
		if ($this->isColumnModified(AidConfigurationPeer::BULL_SIMPLIFIE)) $criteria->add(AidConfigurationPeer::BULL_SIMPLIFIE, $this->bull_simplifie);
		if ($this->isColumnModified(AidConfigurationPeer::OUTILS_COMPLEMENTAIRES)) $criteria->add(AidConfigurationPeer::OUTILS_COMPLEMENTAIRES, $this->outils_complementaires);
		if ($this->isColumnModified(AidConfigurationPeer::FEUILLE_PRESENCE)) $criteria->add(AidConfigurationPeer::FEUILLE_PRESENCE, $this->feuille_presence);

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
		$criteria = new Criteria(AidConfigurationPeer::DATABASE_NAME);
		$criteria->add(AidConfigurationPeer::INDICE_AID, $this->indice_aid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIndiceAid();
	}

	/**
	 * Generic method to set the primary key (indice_aid column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIndiceAid($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getIndiceAid();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AidConfiguration (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setNom($this->nom);
		$copyObj->setNomComplet($this->nom_complet);
		$copyObj->setNoteMax($this->note_max);
		$copyObj->setOrderDisplay1($this->order_display1);
		$copyObj->setOrderDisplay2($this->order_display2);
		$copyObj->setTypeNote($this->type_note);
		$copyObj->setDisplayBegin($this->display_begin);
		$copyObj->setDisplayEnd($this->display_end);
		$copyObj->setMessage($this->message);
		$copyObj->setDisplayNom($this->display_nom);
		$copyObj->setIndiceAid($this->indice_aid);
		$copyObj->setDisplayBulletin($this->display_bulletin);
		$copyObj->setBullSimplifie($this->bull_simplifie);
		$copyObj->setOutilsComplementaires($this->outils_complementaires);
		$copyObj->setFeuillePresence($this->feuille_presence);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getAidDetailss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAidDetails($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


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
	 * @return     AidConfiguration Clone of current object.
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
	 * @return     AidConfigurationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AidConfigurationPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collAidDetailss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAidDetailss()
	 */
	public function clearAidDetailss()
	{
		$this->collAidDetailss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAidDetailss collection.
	 *
	 * By default this just sets the collAidDetailss collection to an empty array (like clearcollAidDetailss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAidDetailss()
	{
		$this->collAidDetailss = new PropelObjectCollection();
		$this->collAidDetailss->setModel('AidDetails');
	}

	/**
	 * Gets an array of AidDetails objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AidConfiguration is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AidDetails[] List of AidDetails objects
	 * @throws     PropelException
	 */
	public function getAidDetailss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAidDetailss || null !== $criteria) {
			if ($this->isNew() && null === $this->collAidDetailss) {
				// return empty collection
				$this->initAidDetailss();
			} else {
				$collAidDetailss = AidDetailsQuery::create(null, $criteria)
					->filterByAidConfiguration($this)
					->find($con);
				if (null !== $criteria) {
					return $collAidDetailss;
				}
				$this->collAidDetailss = $collAidDetailss;
			}
		}
		return $this->collAidDetailss;
	}

	/**
	 * Returns the number of related AidDetails objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AidDetails objects.
	 * @throws     PropelException
	 */
	public function countAidDetailss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAidDetailss || null !== $criteria) {
			if ($this->isNew() && null === $this->collAidDetailss) {
				return 0;
			} else {
				$query = AidDetailsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAidConfiguration($this)
					->count($con);
			}
		} else {
			return count($this->collAidDetailss);
		}
	}

	/**
	 * Method called to associate a AidDetails object to this object
	 * through the AidDetails foreign key attribute.
	 *
	 * @param      AidDetails $l AidDetails
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAidDetails(AidDetails $l)
	{
		if ($this->collAidDetailss === null) {
			$this->initAidDetailss();
		}
		if (!$this->collAidDetailss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAidDetailss[]= $l;
			$l->setAidConfiguration($this);
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->nom = null;
		$this->nom_complet = null;
		$this->note_max = null;
		$this->order_display1 = null;
		$this->order_display2 = null;
		$this->type_note = null;
		$this->display_begin = null;
		$this->display_end = null;
		$this->message = null;
		$this->display_nom = null;
		$this->indice_aid = null;
		$this->display_bulletin = null;
		$this->bull_simplifie = null;
		$this->outils_complementaires = null;
		$this->feuille_presence = null;
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
			if ($this->collAidDetailss) {
				foreach ((array) $this->collAidDetailss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collAidDetailss = null;
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

} // BaseAidConfiguration
