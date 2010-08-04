<?php


/**
 * Base class that represents a row from the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEleve extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'ElevePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ElevePeer
	 */
	protected static $peer;

	/**
	 * The value for the no_gep field.
	 * @var        string
	 */
	protected $no_gep;

	/**
	 * The value for the login field.
	 * @var        string
	 */
	protected $login;

	/**
	 * The value for the nom field.
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the prenom field.
	 * @var        string
	 */
	protected $prenom;

	/**
	 * The value for the sexe field.
	 * @var        string
	 */
	protected $sexe;

	/**
	 * The value for the naissance field.
	 * @var        string
	 */
	protected $naissance;

	/**
	 * The value for the lieu_naissance field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $lieu_naissance;

	/**
	 * The value for the elenoet field.
	 * @var        string
	 */
	protected $elenoet;

	/**
	 * The value for the ereno field.
	 * @var        string
	 */
	protected $ereno;

	/**
	 * The value for the ele_id field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $ele_id;

	/**
	 * The value for the email field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the id_eleve field.
	 * @var        int
	 */
	protected $id_eleve;

	/**
	 * @var        array JEleveClasse[] Collection to store aggregation of JEleveClasse objects.
	 */
	protected $collJEleveClasses;

	/**
	 * @var        array JEleveCpe[] Collection to store aggregation of JEleveCpe objects.
	 */
	protected $collJEleveCpes;

	/**
	 * @var        array JEleveGroupe[] Collection to store aggregation of JEleveGroupe objects.
	 */
	protected $collJEleveGroupes;

	/**
	 * @var        array JEleveProfesseurPrincipal[] Collection to store aggregation of JEleveProfesseurPrincipal objects.
	 */
	protected $collJEleveProfesseurPrincipals;

	/**
	 * @var        EleveRegimeDoublant one-to-one related EleveRegimeDoublant object
	 */
	protected $singleEleveRegimeDoublant;

	/**
	 * @var        array ResponsableInformation[] Collection to store aggregation of ResponsableInformation objects.
	 */
	protected $collResponsableInformations;

	/**
	 * @var        array JEleveAncienEtablissement[] Collection to store aggregation of JEleveAncienEtablissement objects.
	 */
	protected $collJEleveAncienEtablissements;

	/**
	 * @var        array JAidEleves[] Collection to store aggregation of JAidEleves objects.
	 */
	protected $collJAidElevess;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        array CreditEcts[] Collection to store aggregation of CreditEcts objects.
	 */
	protected $collCreditEctss;

	/**
	 * @var        array CreditEctsGlobal[] Collection to store aggregation of CreditEctsGlobal objects.
	 */
	protected $collCreditEctsGlobals;

	/**
	 * @var        array ArchiveEcts[] Collection to store aggregation of ArchiveEcts objects.
	 */
	protected $collArchiveEctss;

	/**
	 * @var        array AncienEtablissement[] Collection to store aggregation of AncienEtablissement objects.
	 */
	protected $collAncienEtablissements;

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
		$this->lieu_naissance = '';
		$this->ele_id = '';
		$this->email = '';
	}

	/**
	 * Initializes internal state of BaseEleve object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [no_gep] column value.
	 * Ancien numero GEP, Numero national de l'eleve
	 * @return     string
	 */
	public function getNoGep()
	{
		return $this->no_gep;
	}

	/**
	 * Get the [login] column value.
	 * Login de l'eleve, est conserve pour le login utilisateur
	 * @return     string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * Get the [nom] column value.
	 * Nom eleve
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [prenom] column value.
	 * Prenom eleve
	 * @return     string
	 */
	public function getPrenom()
	{
		return $this->prenom;
	}

	/**
	 * Get the [sexe] column value.
	 * M ou F
	 * @return     string
	 */
	public function getSexe()
	{
		return $this->sexe;
	}

	/**
	 * Get the [optionally formatted] temporal [naissance] column value.
	 * Date de naissance AAAA-MM-JJ
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getNaissance($format = '%x')
	{
		if ($this->naissance === null) {
			return null;
		}


		if ($this->naissance === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->naissance);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->naissance, true), $x);
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
	 * Get the [lieu_naissance] column value.
	 * Code de Sconet
	 * @return     string
	 */
	public function getLieuNaissance()
	{
		return $this->lieu_naissance;
	}

	/**
	 * Get the [elenoet] column value.
	 * Numero interne de l'eleve dans l'etablissement
	 * @return     string
	 */
	public function getElenoet()
	{
		return $this->elenoet;
	}

	/**
	 * Get the [ereno] column value.
	 * Plus utilise
	 * @return     string
	 */
	public function getEreno()
	{
		return $this->ereno;
	}

	/**
	 * Get the [ele_id] column value.
	 * cle utilise par Sconet dans ses fichiers xml
	 * @return     string
	 */
	public function getEleId()
	{
		return $this->ele_id;
	}

	/**
	 * Get the [email] column value.
	 * Courriel de l'eleve
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [id_eleve] column value.
	 * cle primaire autoincremente
	 * @return     int
	 */
	public function getIdEleve()
	{
		return $this->id_eleve;
	}

	/**
	 * Set the value of [no_gep] column.
	 * Ancien numero GEP, Numero national de l'eleve
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setNoGep($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->no_gep !== $v) {
			$this->no_gep = $v;
			$this->modifiedColumns[] = ElevePeer::NO_GEP;
		}

		return $this;
	} // setNoGep()

	/**
	 * Set the value of [login] column.
	 * Login de l'eleve, est conserve pour le login utilisateur
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->login !== $v) {
			$this->login = $v;
			$this->modifiedColumns[] = ElevePeer::LOGIN;
		}

		return $this;
	} // setLogin()

	/**
	 * Set the value of [nom] column.
	 * Nom eleve
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = ElevePeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [prenom] column.
	 * Prenom eleve
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setPrenom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prenom !== $v) {
			$this->prenom = $v;
			$this->modifiedColumns[] = ElevePeer::PRENOM;
		}

		return $this;
	} // setPrenom()

	/**
	 * Set the value of [sexe] column.
	 * M ou F
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setSexe($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->sexe !== $v) {
			$this->sexe = $v;
			$this->modifiedColumns[] = ElevePeer::SEXE;
		}

		return $this;
	} // setSexe()

	/**
	 * Sets the value of [naissance] column to a normalized version of the date/time value specified.
	 * Date de naissance AAAA-MM-JJ
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setNaissance($v)
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

		if ( $this->naissance !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->naissance !== null && $tmpDt = new DateTime($this->naissance)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->naissance = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = ElevePeer::NAISSANCE;
			}
		} // if either are not null

		return $this;
	} // setNaissance()

	/**
	 * Set the value of [lieu_naissance] column.
	 * Code de Sconet
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setLieuNaissance($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->lieu_naissance !== $v || $this->isNew()) {
			$this->lieu_naissance = $v;
			$this->modifiedColumns[] = ElevePeer::LIEU_NAISSANCE;
		}

		return $this;
	} // setLieuNaissance()

	/**
	 * Set the value of [elenoet] column.
	 * Numero interne de l'eleve dans l'etablissement
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setElenoet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->elenoet !== $v) {
			$this->elenoet = $v;
			$this->modifiedColumns[] = ElevePeer::ELENOET;
		}

		return $this;
	} // setElenoet()

	/**
	 * Set the value of [ereno] column.
	 * Plus utilise
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setEreno($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ereno !== $v) {
			$this->ereno = $v;
			$this->modifiedColumns[] = ElevePeer::ERENO;
		}

		return $this;
	} // setEreno()

	/**
	 * Set the value of [ele_id] column.
	 * cle utilise par Sconet dans ses fichiers xml
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setEleId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ele_id !== $v || $this->isNew()) {
			$this->ele_id = $v;
			$this->modifiedColumns[] = ElevePeer::ELE_ID;
		}

		return $this;
	} // setEleId()

	/**
	 * Set the value of [email] column.
	 * Courriel de l'eleve
	 * @param      string $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v || $this->isNew()) {
			$this->email = $v;
			$this->modifiedColumns[] = ElevePeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [id_eleve] column.
	 * cle primaire autoincremente
	 * @param      int $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setIdEleve($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_eleve !== $v) {
			$this->id_eleve = $v;
			$this->modifiedColumns[] = ElevePeer::ID_ELEVE;
		}

		return $this;
	} // setIdEleve()

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
			if ($this->lieu_naissance !== '') {
				return false;
			}

			if ($this->ele_id !== '') {
				return false;
			}

			if ($this->email !== '') {
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

			$this->no_gep = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->login = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->prenom = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->sexe = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->naissance = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->lieu_naissance = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->elenoet = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->ereno = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->ele_id = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->email = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->id_eleve = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 12; // 12 = ElevePeer::NUM_COLUMNS - ElevePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Eleve object", $e);
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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ElevePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collJEleveClasses = null;

			$this->collJEleveCpes = null;

			$this->collJEleveGroupes = null;

			$this->collJEleveProfesseurPrincipals = null;

			$this->singleEleveRegimeDoublant = null;

			$this->collResponsableInformations = null;

			$this->collJEleveAncienEtablissements = null;

			$this->collJAidElevess = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collCreditEctss = null;

			$this->collCreditEctsGlobals = null;

			$this->collArchiveEctss = null;

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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				EleveQuery::create()
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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				ElevePeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = ElevePeer::ID_ELEVE;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(ElevePeer::ID_ELEVE) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.ElevePeer::ID_ELEVE.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setIdEleve($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = ElevePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJEleveClasses !== null) {
				foreach ($this->collJEleveClasses as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveCpes !== null) {
				foreach ($this->collJEleveCpes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveGroupes !== null) {
				foreach ($this->collJEleveGroupes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveProfesseurPrincipals !== null) {
				foreach ($this->collJEleveProfesseurPrincipals as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->singleEleveRegimeDoublant !== null) {
				if (!$this->singleEleveRegimeDoublant->isDeleted()) {
						$affectedRows += $this->singleEleveRegimeDoublant->save($con);
				}
			}

			if ($this->collResponsableInformations !== null) {
				foreach ($this->collResponsableInformations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveAncienEtablissements !== null) {
				foreach ($this->collJEleveAncienEtablissements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJAidElevess !== null) {
				foreach ($this->collJAidElevess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveSaisies !== null) {
				foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCreditEctss !== null) {
				foreach ($this->collCreditEctss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCreditEctsGlobals !== null) {
				foreach ($this->collCreditEctsGlobals as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collArchiveEctss !== null) {
				foreach ($this->collArchiveEctss as $referrerFK) {
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


			if (($retval = ElevePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJEleveClasses !== null) {
					foreach ($this->collJEleveClasses as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveCpes !== null) {
					foreach ($this->collJEleveCpes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveGroupes !== null) {
					foreach ($this->collJEleveGroupes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveProfesseurPrincipals !== null) {
					foreach ($this->collJEleveProfesseurPrincipals as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->singleEleveRegimeDoublant !== null) {
					if (!$this->singleEleveRegimeDoublant->validate($columns)) {
						$failureMap = array_merge($failureMap, $this->singleEleveRegimeDoublant->getValidationFailures());
					}
				}

				if ($this->collResponsableInformations !== null) {
					foreach ($this->collResponsableInformations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveAncienEtablissements !== null) {
					foreach ($this->collJEleveAncienEtablissements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJAidElevess !== null) {
					foreach ($this->collJAidElevess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveSaisies !== null) {
					foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCreditEctss !== null) {
					foreach ($this->collCreditEctss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCreditEctsGlobals !== null) {
					foreach ($this->collCreditEctsGlobals as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collArchiveEctss !== null) {
					foreach ($this->collArchiveEctss as $referrerFK) {
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
		$pos = ElevePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNoGep();
				break;
			case 1:
				return $this->getLogin();
				break;
			case 2:
				return $this->getNom();
				break;
			case 3:
				return $this->getPrenom();
				break;
			case 4:
				return $this->getSexe();
				break;
			case 5:
				return $this->getNaissance();
				break;
			case 6:
				return $this->getLieuNaissance();
				break;
			case 7:
				return $this->getElenoet();
				break;
			case 8:
				return $this->getEreno();
				break;
			case 9:
				return $this->getEleId();
				break;
			case 10:
				return $this->getEmail();
				break;
			case 11:
				return $this->getIdEleve();
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
		$keys = ElevePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getNoGep(),
			$keys[1] => $this->getLogin(),
			$keys[2] => $this->getNom(),
			$keys[3] => $this->getPrenom(),
			$keys[4] => $this->getSexe(),
			$keys[5] => $this->getNaissance(),
			$keys[6] => $this->getLieuNaissance(),
			$keys[7] => $this->getElenoet(),
			$keys[8] => $this->getEreno(),
			$keys[9] => $this->getEleId(),
			$keys[10] => $this->getEmail(),
			$keys[11] => $this->getIdEleve(),
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
		$pos = ElevePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNoGep($value);
				break;
			case 1:
				$this->setLogin($value);
				break;
			case 2:
				$this->setNom($value);
				break;
			case 3:
				$this->setPrenom($value);
				break;
			case 4:
				$this->setSexe($value);
				break;
			case 5:
				$this->setNaissance($value);
				break;
			case 6:
				$this->setLieuNaissance($value);
				break;
			case 7:
				$this->setElenoet($value);
				break;
			case 8:
				$this->setEreno($value);
				break;
			case 9:
				$this->setEleId($value);
				break;
			case 10:
				$this->setEmail($value);
				break;
			case 11:
				$this->setIdEleve($value);
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
		$keys = ElevePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setNoGep($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setLogin($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNom($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setPrenom($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSexe($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setNaissance($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setLieuNaissance($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setElenoet($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setEreno($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setEleId($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setEmail($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setIdEleve($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ElevePeer::DATABASE_NAME);

		if ($this->isColumnModified(ElevePeer::NO_GEP)) $criteria->add(ElevePeer::NO_GEP, $this->no_gep);
		if ($this->isColumnModified(ElevePeer::LOGIN)) $criteria->add(ElevePeer::LOGIN, $this->login);
		if ($this->isColumnModified(ElevePeer::NOM)) $criteria->add(ElevePeer::NOM, $this->nom);
		if ($this->isColumnModified(ElevePeer::PRENOM)) $criteria->add(ElevePeer::PRENOM, $this->prenom);
		if ($this->isColumnModified(ElevePeer::SEXE)) $criteria->add(ElevePeer::SEXE, $this->sexe);
		if ($this->isColumnModified(ElevePeer::NAISSANCE)) $criteria->add(ElevePeer::NAISSANCE, $this->naissance);
		if ($this->isColumnModified(ElevePeer::LIEU_NAISSANCE)) $criteria->add(ElevePeer::LIEU_NAISSANCE, $this->lieu_naissance);
		if ($this->isColumnModified(ElevePeer::ELENOET)) $criteria->add(ElevePeer::ELENOET, $this->elenoet);
		if ($this->isColumnModified(ElevePeer::ERENO)) $criteria->add(ElevePeer::ERENO, $this->ereno);
		if ($this->isColumnModified(ElevePeer::ELE_ID)) $criteria->add(ElevePeer::ELE_ID, $this->ele_id);
		if ($this->isColumnModified(ElevePeer::EMAIL)) $criteria->add(ElevePeer::EMAIL, $this->email);
		if ($this->isColumnModified(ElevePeer::ID_ELEVE)) $criteria->add(ElevePeer::ID_ELEVE, $this->id_eleve);

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
		$criteria = new Criteria(ElevePeer::DATABASE_NAME);
		$criteria->add(ElevePeer::ID_ELEVE, $this->id_eleve);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdEleve();
	}

	/**
	 * Generic method to set the primary key (id_eleve column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdEleve($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getIdEleve();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Eleve (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setNoGep($this->no_gep);
		$copyObj->setLogin($this->login);
		$copyObj->setNom($this->nom);
		$copyObj->setPrenom($this->prenom);
		$copyObj->setSexe($this->sexe);
		$copyObj->setNaissance($this->naissance);
		$copyObj->setLieuNaissance($this->lieu_naissance);
		$copyObj->setElenoet($this->elenoet);
		$copyObj->setEreno($this->ereno);
		$copyObj->setEleId($this->ele_id);
		$copyObj->setEmail($this->email);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJEleveClasses() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveClasse($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveCpes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveCpe($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveGroupes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveGroupe($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveProfesseurPrincipals() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveProfesseurPrincipal($relObj->copy($deepCopy));
				}
			}

			$relObj = $this->getEleveRegimeDoublant();
			if ($relObj) {
				$copyObj->setEleveRegimeDoublant($relObj->copy($deepCopy));
			}

			foreach ($this->getResponsableInformations() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addResponsableInformation($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveAncienEtablissements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveAncienEtablissement($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJAidElevess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJAidEleves($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisie($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCreditEctss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCreditEcts($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCreditEctsGlobals() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCreditEctsGlobal($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getArchiveEctss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addArchiveEcts($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);
		$copyObj->setIdEleve(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     Eleve Clone of current object.
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
	 * @return     ElevePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ElevePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collJEleveClasses collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveClasses()
	 */
	public function clearJEleveClasses()
	{
		$this->collJEleveClasses = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveClasses collection.
	 *
	 * By default this just sets the collJEleveClasses collection to an empty array (like clearcollJEleveClasses());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveClasses()
	{
		$this->collJEleveClasses = new PropelObjectCollection();
		$this->collJEleveClasses->setModel('JEleveClasse');
	}

	/**
	 * Gets an array of JEleveClasse objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveClasse[] List of JEleveClasse objects
	 * @throws     PropelException
	 */
	public function getJEleveClasses($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveClasses) {
				// return empty collection
				$this->initJEleveClasses();
			} else {
				$collJEleveClasses = JEleveClasseQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveClasses;
				}
				$this->collJEleveClasses = $collJEleveClasses;
			}
		}
		return $this->collJEleveClasses;
	}

	/**
	 * Returns the number of related JEleveClasse objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveClasse objects.
	 * @throws     PropelException
	 */
	public function countJEleveClasses(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveClasses) {
				return 0;
			} else {
				$query = JEleveClasseQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveClasses);
		}
	}

	/**
	 * Method called to associate a JEleveClasse object to this object
	 * through the JEleveClasse foreign key attribute.
	 *
	 * @param      JEleveClasse $l JEleveClasse
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveClasse(JEleveClasse $l)
	{
		if ($this->collJEleveClasses === null) {
			$this->initJEleveClasses();
		}
		if (!$this->collJEleveClasses->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveClasses[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveClasses from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveClasse[] List of JEleveClasse objects
	 */
	public function getJEleveClassesJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveClasseQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getJEleveClasses($query, $con);
	}

	/**
	 * Clears out the collJEleveCpes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveCpes()
	 */
	public function clearJEleveCpes()
	{
		$this->collJEleveCpes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveCpes collection.
	 *
	 * By default this just sets the collJEleveCpes collection to an empty array (like clearcollJEleveCpes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveCpes()
	{
		$this->collJEleveCpes = new PropelObjectCollection();
		$this->collJEleveCpes->setModel('JEleveCpe');
	}

	/**
	 * Gets an array of JEleveCpe objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveCpe[] List of JEleveCpe objects
	 * @throws     PropelException
	 */
	public function getJEleveCpes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveCpes || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveCpes) {
				// return empty collection
				$this->initJEleveCpes();
			} else {
				$collJEleveCpes = JEleveCpeQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveCpes;
				}
				$this->collJEleveCpes = $collJEleveCpes;
			}
		}
		return $this->collJEleveCpes;
	}

	/**
	 * Returns the number of related JEleveCpe objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveCpe objects.
	 * @throws     PropelException
	 */
	public function countJEleveCpes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveCpes || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveCpes) {
				return 0;
			} else {
				$query = JEleveCpeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveCpes);
		}
	}

	/**
	 * Method called to associate a JEleveCpe object to this object
	 * through the JEleveCpe foreign key attribute.
	 *
	 * @param      JEleveCpe $l JEleveCpe
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveCpe(JEleveCpe $l)
	{
		if ($this->collJEleveCpes === null) {
			$this->initJEleveCpes();
		}
		if (!$this->collJEleveCpes->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveCpes[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveCpes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveCpe[] List of JEleveCpe objects
	 */
	public function getJEleveCpesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveCpeQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getJEleveCpes($query, $con);
	}

	/**
	 * Clears out the collJEleveGroupes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveGroupes()
	 */
	public function clearJEleveGroupes()
	{
		$this->collJEleveGroupes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveGroupes collection.
	 *
	 * By default this just sets the collJEleveGroupes collection to an empty array (like clearcollJEleveGroupes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveGroupes()
	{
		$this->collJEleveGroupes = new PropelObjectCollection();
		$this->collJEleveGroupes->setModel('JEleveGroupe');
	}

	/**
	 * Gets an array of JEleveGroupe objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveGroupe[] List of JEleveGroupe objects
	 * @throws     PropelException
	 */
	public function getJEleveGroupes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveGroupes) {
				// return empty collection
				$this->initJEleveGroupes();
			} else {
				$collJEleveGroupes = JEleveGroupeQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveGroupes;
				}
				$this->collJEleveGroupes = $collJEleveGroupes;
			}
		}
		return $this->collJEleveGroupes;
	}

	/**
	 * Returns the number of related JEleveGroupe objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveGroupe objects.
	 * @throws     PropelException
	 */
	public function countJEleveGroupes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveGroupes) {
				return 0;
			} else {
				$query = JEleveGroupeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveGroupes);
		}
	}

	/**
	 * Method called to associate a JEleveGroupe object to this object
	 * through the JEleveGroupe foreign key attribute.
	 *
	 * @param      JEleveGroupe $l JEleveGroupe
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveGroupe(JEleveGroupe $l)
	{
		if ($this->collJEleveGroupes === null) {
			$this->initJEleveGroupes();
		}
		if (!$this->collJEleveGroupes->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveGroupes[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveGroupes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveGroupe[] List of JEleveGroupe objects
	 */
	public function getJEleveGroupesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveGroupeQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getJEleveGroupes($query, $con);
	}

	/**
	 * Clears out the collJEleveProfesseurPrincipals collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveProfesseurPrincipals()
	 */
	public function clearJEleveProfesseurPrincipals()
	{
		$this->collJEleveProfesseurPrincipals = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveProfesseurPrincipals collection.
	 *
	 * By default this just sets the collJEleveProfesseurPrincipals collection to an empty array (like clearcollJEleveProfesseurPrincipals());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveProfesseurPrincipals()
	{
		$this->collJEleveProfesseurPrincipals = new PropelObjectCollection();
		$this->collJEleveProfesseurPrincipals->setModel('JEleveProfesseurPrincipal');
	}

	/**
	 * Gets an array of JEleveProfesseurPrincipal objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveProfesseurPrincipal[] List of JEleveProfesseurPrincipal objects
	 * @throws     PropelException
	 */
	public function getJEleveProfesseurPrincipals($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveProfesseurPrincipals || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveProfesseurPrincipals) {
				// return empty collection
				$this->initJEleveProfesseurPrincipals();
			} else {
				$collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveProfesseurPrincipals;
				}
				$this->collJEleveProfesseurPrincipals = $collJEleveProfesseurPrincipals;
			}
		}
		return $this->collJEleveProfesseurPrincipals;
	}

	/**
	 * Returns the number of related JEleveProfesseurPrincipal objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveProfesseurPrincipal objects.
	 * @throws     PropelException
	 */
	public function countJEleveProfesseurPrincipals(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveProfesseurPrincipals || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveProfesseurPrincipals) {
				return 0;
			} else {
				$query = JEleveProfesseurPrincipalQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveProfesseurPrincipals);
		}
	}

	/**
	 * Method called to associate a JEleveProfesseurPrincipal object to this object
	 * through the JEleveProfesseurPrincipal foreign key attribute.
	 *
	 * @param      JEleveProfesseurPrincipal $l JEleveProfesseurPrincipal
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveProfesseurPrincipal(JEleveProfesseurPrincipal $l)
	{
		if ($this->collJEleveProfesseurPrincipals === null) {
			$this->initJEleveProfesseurPrincipals();
		}
		if (!$this->collJEleveProfesseurPrincipals->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveProfesseurPrincipals[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveProfesseurPrincipal[] List of JEleveProfesseurPrincipal objects
	 */
	public function getJEleveProfesseurPrincipalsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveProfesseurPrincipalQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getJEleveProfesseurPrincipals($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveProfesseurPrincipal[] List of JEleveProfesseurPrincipal objects
	 */
	public function getJEleveProfesseurPrincipalsJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveProfesseurPrincipalQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getJEleveProfesseurPrincipals($query, $con);
	}

	/**
	 * Gets a single EleveRegimeDoublant object, which is related to this object by a one-to-one relationship.
	 *
	 * @param      PropelPDO $con optional connection object
	 * @return     EleveRegimeDoublant
	 * @throws     PropelException
	 */
	public function getEleveRegimeDoublant(PropelPDO $con = null)
	{

		if ($this->singleEleveRegimeDoublant === null && !$this->isNew()) {
			$this->singleEleveRegimeDoublant = EleveRegimeDoublantQuery::create()->findPk($this->getPrimaryKey(), $con);
		}

		return $this->singleEleveRegimeDoublant;
	}

	/**
	 * Sets a single EleveRegimeDoublant object as related to this object by a one-to-one relationship.
	 *
	 * @param      EleveRegimeDoublant $v EleveRegimeDoublant
	 * @return     Eleve The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEleveRegimeDoublant(EleveRegimeDoublant $v = null)
	{
		$this->singleEleveRegimeDoublant = $v;

		// Make sure that that the passed-in EleveRegimeDoublant isn't already associated with this object
		if ($v !== null && $v->getEleve() === null) {
			$v->setEleve($this);
		}

		return $this;
	}

	/**
	 * Clears out the collResponsableInformations collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addResponsableInformations()
	 */
	public function clearResponsableInformations()
	{
		$this->collResponsableInformations = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collResponsableInformations collection.
	 *
	 * By default this just sets the collResponsableInformations collection to an empty array (like clearcollResponsableInformations());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initResponsableInformations()
	{
		$this->collResponsableInformations = new PropelObjectCollection();
		$this->collResponsableInformations->setModel('ResponsableInformation');
	}

	/**
	 * Gets an array of ResponsableInformation objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ResponsableInformation[] List of ResponsableInformation objects
	 * @throws     PropelException
	 */
	public function getResponsableInformations($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collResponsableInformations || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableInformations) {
				// return empty collection
				$this->initResponsableInformations();
			} else {
				$collResponsableInformations = ResponsableInformationQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collResponsableInformations;
				}
				$this->collResponsableInformations = $collResponsableInformations;
			}
		}
		return $this->collResponsableInformations;
	}

	/**
	 * Returns the number of related ResponsableInformation objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ResponsableInformation objects.
	 * @throws     PropelException
	 */
	public function countResponsableInformations(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collResponsableInformations || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableInformations) {
				return 0;
			} else {
				$query = ResponsableInformationQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collResponsableInformations);
		}
	}

	/**
	 * Method called to associate a ResponsableInformation object to this object
	 * through the ResponsableInformation foreign key attribute.
	 *
	 * @param      ResponsableInformation $l ResponsableInformation
	 * @return     void
	 * @throws     PropelException
	 */
	public function addResponsableInformation(ResponsableInformation $l)
	{
		if ($this->collResponsableInformations === null) {
			$this->initResponsableInformations();
		}
		if (!$this->collResponsableInformations->contains($l)) { // only add it if the **same** object is not already associated
			$this->collResponsableInformations[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related ResponsableInformations from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array ResponsableInformation[] List of ResponsableInformation objects
	 */
	public function getResponsableInformationsJoinResponsableEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = ResponsableInformationQuery::create(null, $criteria);
		$query->joinWith('ResponsableEleve', $join_behavior);

		return $this->getResponsableInformations($query, $con);
	}

	/**
	 * Clears out the collJEleveAncienEtablissements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveAncienEtablissements()
	 */
	public function clearJEleveAncienEtablissements()
	{
		$this->collJEleveAncienEtablissements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveAncienEtablissements collection.
	 *
	 * By default this just sets the collJEleveAncienEtablissements collection to an empty array (like clearcollJEleveAncienEtablissements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveAncienEtablissements()
	{
		$this->collJEleveAncienEtablissements = new PropelObjectCollection();
		$this->collJEleveAncienEtablissements->setModel('JEleveAncienEtablissement');
	}

	/**
	 * Gets an array of JEleveAncienEtablissement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JEleveAncienEtablissement[] List of JEleveAncienEtablissement objects
	 * @throws     PropelException
	 */
	public function getJEleveAncienEtablissements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJEleveAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveAncienEtablissements) {
				// return empty collection
				$this->initJEleveAncienEtablissements();
			} else {
				$collJEleveAncienEtablissements = JEleveAncienEtablissementQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJEleveAncienEtablissements;
				}
				$this->collJEleveAncienEtablissements = $collJEleveAncienEtablissements;
			}
		}
		return $this->collJEleveAncienEtablissements;
	}

	/**
	 * Returns the number of related JEleveAncienEtablissement objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveAncienEtablissement objects.
	 * @throws     PropelException
	 */
	public function countJEleveAncienEtablissements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJEleveAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collJEleveAncienEtablissements) {
				return 0;
			} else {
				$query = JEleveAncienEtablissementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJEleveAncienEtablissements);
		}
	}

	/**
	 * Method called to associate a JEleveAncienEtablissement object to this object
	 * through the JEleveAncienEtablissement foreign key attribute.
	 *
	 * @param      JEleveAncienEtablissement $l JEleveAncienEtablissement
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveAncienEtablissement(JEleveAncienEtablissement $l)
	{
		if ($this->collJEleveAncienEtablissements === null) {
			$this->initJEleveAncienEtablissements();
		}
		if (!$this->collJEleveAncienEtablissements->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJEleveAncienEtablissements[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JEleveAncienEtablissements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveAncienEtablissement[] List of JEleveAncienEtablissement objects
	 */
	public function getJEleveAncienEtablissementsJoinAncienEtablissement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveAncienEtablissementQuery::create(null, $criteria);
		$query->joinWith('AncienEtablissement', $join_behavior);

		return $this->getJEleveAncienEtablissements($query, $con);
	}

	/**
	 * Clears out the collJAidElevess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJAidElevess()
	 */
	public function clearJAidElevess()
	{
		$this->collJAidElevess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJAidElevess collection.
	 *
	 * By default this just sets the collJAidElevess collection to an empty array (like clearcollJAidElevess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJAidElevess()
	{
		$this->collJAidElevess = new PropelObjectCollection();
		$this->collJAidElevess->setModel('JAidEleves');
	}

	/**
	 * Gets an array of JAidEleves objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JAidEleves[] List of JAidEleves objects
	 * @throws     PropelException
	 */
	public function getJAidElevess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJAidElevess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidElevess) {
				// return empty collection
				$this->initJAidElevess();
			} else {
				$collJAidElevess = JAidElevesQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collJAidElevess;
				}
				$this->collJAidElevess = $collJAidElevess;
			}
		}
		return $this->collJAidElevess;
	}

	/**
	 * Returns the number of related JAidEleves objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JAidEleves objects.
	 * @throws     PropelException
	 */
	public function countJAidElevess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJAidElevess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidElevess) {
				return 0;
			} else {
				$query = JAidElevesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collJAidElevess);
		}
	}

	/**
	 * Method called to associate a JAidEleves object to this object
	 * through the JAidEleves foreign key attribute.
	 *
	 * @param      JAidEleves $l JAidEleves
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJAidEleves(JAidEleves $l)
	{
		if ($this->collJAidElevess === null) {
			$this->initJAidElevess();
		}
		if (!$this->collJAidElevess->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJAidElevess[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related JAidElevess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JAidEleves[] List of JAidEleves objects
	 */
	public function getJAidElevessJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JAidElevesQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getJAidElevess($query, $con);
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
	 * By default this just sets the collAbsenceEleveSaisies collection to an empty array (like clearcollAbsenceEleveSaisies());
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
	 * Gets an array of AbsenceEleveSaisie objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveSaisies($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				// return empty collection
				$this->initAbsenceEleveSaisies();
			} else {
				$collAbsenceEleveSaisies = AbsenceEleveSaisieQuery::create(null, $criteria)
					->filterByEleve($this)
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
		if(null === $this->collAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisies) {
				return 0;
			} else {
				$query = AbsenceEleveSaisieQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveSaisies);
		}
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
		if (!$this->collAbsenceEleveSaisies->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveSaisies[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtCreneau', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEdtEmplacementCours($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtEmplacementCours', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinModifieParUtilisateur($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('ModifieParUtilisateur', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}

	/**
	 * Clears out the collCreditEctss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCreditEctss()
	 */
	public function clearCreditEctss()
	{
		$this->collCreditEctss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCreditEctss collection.
	 *
	 * By default this just sets the collCreditEctss collection to an empty array (like clearcollCreditEctss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCreditEctss()
	{
		$this->collCreditEctss = new PropelObjectCollection();
		$this->collCreditEctss->setModel('CreditEcts');
	}

	/**
	 * Gets an array of CreditEcts objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CreditEcts[] List of CreditEcts objects
	 * @throws     PropelException
	 */
	public function getCreditEctss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCreditEctss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCreditEctss) {
				// return empty collection
				$this->initCreditEctss();
			} else {
				$collCreditEctss = CreditEctsQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collCreditEctss;
				}
				$this->collCreditEctss = $collCreditEctss;
			}
		}
		return $this->collCreditEctss;
	}

	/**
	 * Returns the number of related CreditEcts objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CreditEcts objects.
	 * @throws     PropelException
	 */
	public function countCreditEctss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCreditEctss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCreditEctss) {
				return 0;
			} else {
				$query = CreditEctsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collCreditEctss);
		}
	}

	/**
	 * Method called to associate a CreditEcts object to this object
	 * through the CreditEcts foreign key attribute.
	 *
	 * @param      CreditEcts $l CreditEcts
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCreditEcts(CreditEcts $l)
	{
		if ($this->collCreditEctss === null) {
			$this->initCreditEctss();
		}
		if (!$this->collCreditEctss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCreditEctss[]= $l;
			$l->setEleve($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Eleve is new, it will return
	 * an empty collection; or if this Eleve has previously
	 * been saved, it will retrieve related CreditEctss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Eleve.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CreditEcts[] List of CreditEcts objects
	 */
	public function getCreditEctssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CreditEctsQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCreditEctss($query, $con);
	}

	/**
	 * Clears out the collCreditEctsGlobals collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCreditEctsGlobals()
	 */
	public function clearCreditEctsGlobals()
	{
		$this->collCreditEctsGlobals = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCreditEctsGlobals collection.
	 *
	 * By default this just sets the collCreditEctsGlobals collection to an empty array (like clearcollCreditEctsGlobals());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCreditEctsGlobals()
	{
		$this->collCreditEctsGlobals = new PropelObjectCollection();
		$this->collCreditEctsGlobals->setModel('CreditEctsGlobal');
	}

	/**
	 * Gets an array of CreditEctsGlobal objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CreditEctsGlobal[] List of CreditEctsGlobal objects
	 * @throws     PropelException
	 */
	public function getCreditEctsGlobals($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCreditEctsGlobals || null !== $criteria) {
			if ($this->isNew() && null === $this->collCreditEctsGlobals) {
				// return empty collection
				$this->initCreditEctsGlobals();
			} else {
				$collCreditEctsGlobals = CreditEctsGlobalQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collCreditEctsGlobals;
				}
				$this->collCreditEctsGlobals = $collCreditEctsGlobals;
			}
		}
		return $this->collCreditEctsGlobals;
	}

	/**
	 * Returns the number of related CreditEctsGlobal objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CreditEctsGlobal objects.
	 * @throws     PropelException
	 */
	public function countCreditEctsGlobals(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCreditEctsGlobals || null !== $criteria) {
			if ($this->isNew() && null === $this->collCreditEctsGlobals) {
				return 0;
			} else {
				$query = CreditEctsGlobalQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collCreditEctsGlobals);
		}
	}

	/**
	 * Method called to associate a CreditEctsGlobal object to this object
	 * through the CreditEctsGlobal foreign key attribute.
	 *
	 * @param      CreditEctsGlobal $l CreditEctsGlobal
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCreditEctsGlobal(CreditEctsGlobal $l)
	{
		if ($this->collCreditEctsGlobals === null) {
			$this->initCreditEctsGlobals();
		}
		if (!$this->collCreditEctsGlobals->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCreditEctsGlobals[]= $l;
			$l->setEleve($this);
		}
	}

	/**
	 * Clears out the collArchiveEctss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addArchiveEctss()
	 */
	public function clearArchiveEctss()
	{
		$this->collArchiveEctss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collArchiveEctss collection.
	 *
	 * By default this just sets the collArchiveEctss collection to an empty array (like clearcollArchiveEctss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initArchiveEctss()
	{
		$this->collArchiveEctss = new PropelObjectCollection();
		$this->collArchiveEctss->setModel('ArchiveEcts');
	}

	/**
	 * Gets an array of ArchiveEcts objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array ArchiveEcts[] List of ArchiveEcts objects
	 * @throws     PropelException
	 */
	public function getArchiveEctss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collArchiveEctss || null !== $criteria) {
			if ($this->isNew() && null === $this->collArchiveEctss) {
				// return empty collection
				$this->initArchiveEctss();
			} else {
				$collArchiveEctss = ArchiveEctsQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collArchiveEctss;
				}
				$this->collArchiveEctss = $collArchiveEctss;
			}
		}
		return $this->collArchiveEctss;
	}

	/**
	 * Returns the number of related ArchiveEcts objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related ArchiveEcts objects.
	 * @throws     PropelException
	 */
	public function countArchiveEctss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collArchiveEctss || null !== $criteria) {
			if ($this->isNew() && null === $this->collArchiveEctss) {
				return 0;
			} else {
				$query = ArchiveEctsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collArchiveEctss);
		}
	}

	/**
	 * Method called to associate a ArchiveEcts object to this object
	 * through the ArchiveEcts foreign key attribute.
	 *
	 * @param      ArchiveEcts $l ArchiveEcts
	 * @return     void
	 * @throws     PropelException
	 */
	public function addArchiveEcts(ArchiveEcts $l)
	{
		if ($this->collArchiveEctss === null) {
			$this->initArchiveEctss();
		}
		if (!$this->collArchiveEctss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collArchiveEctss[]= $l;
			$l->setEleve($this);
		}
	}

	/**
	 * Clears out the collAncienEtablissements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAncienEtablissements()
	 */
	public function clearAncienEtablissements()
	{
		$this->collAncienEtablissements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAncienEtablissements collection.
	 *
	 * By default this just sets the collAncienEtablissements collection to an empty collection (like clearAncienEtablissements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAncienEtablissements()
	{
		$this->collAncienEtablissements = new PropelObjectCollection();
		$this->collAncienEtablissements->setModel('AncienEtablissement');
	}

	/**
	 * Gets a collection of AncienEtablissement objects related by a many-to-many relationship
	 * to the current object by way of the j_eleves_etablissements cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AncienEtablissement[] List of AncienEtablissement objects
	 */
	public function getAncienEtablissements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAncienEtablissements) {
				// return empty collection
				$this->initAncienEtablissements();
			} else {
				$collAncienEtablissements = AncienEtablissementQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collAncienEtablissements;
				}
				$this->collAncienEtablissements = $collAncienEtablissements;
			}
		}
		return $this->collAncienEtablissements;
	}

	/**
	 * Gets the number of AncienEtablissement objects related by a many-to-many relationship
	 * to the current object by way of the j_eleves_etablissements cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related AncienEtablissement objects
	 */
	public function countAncienEtablissements($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAncienEtablissements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAncienEtablissements) {
				return 0;
			} else {
				$query = AncienEtablissementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collAncienEtablissements);
		}
	}

	/**
	 * Associate a AncienEtablissement object to this object
	 * through the j_eleves_etablissements cross reference table.
	 *
	 * @param      AncienEtablissement $ancienEtablissement The JEleveAncienEtablissement object to relate
	 * @return     void
	 */
	public function addAncienEtablissement($ancienEtablissement)
	{
		if ($this->collAncienEtablissements === null) {
			$this->initAncienEtablissements();
		}
		if (!$this->collAncienEtablissements->contains($ancienEtablissement)) { // only add it if the **same** object is not already associated
			$jEleveAncienEtablissement = new JEleveAncienEtablissement();
			$jEleveAncienEtablissement->setAncienEtablissement($ancienEtablissement);
			$this->addJEleveAncienEtablissement($jEleveAncienEtablissement);
			
			$this->collAncienEtablissements[]= $ancienEtablissement;
		}
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
	 * By default this just sets the collAidDetailss collection to an empty collection (like clearAidDetailss());
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
	 * Gets a collection of AidDetails objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AidDetails[] List of AidDetails objects
	 */
	public function getAidDetailss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAidDetailss || null !== $criteria) {
			if ($this->isNew() && null === $this->collAidDetailss) {
				// return empty collection
				$this->initAidDetailss();
			} else {
				$collAidDetailss = AidDetailsQuery::create(null, $criteria)
					->filterByEleve($this)
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
	 * Gets the number of AidDetails objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related AidDetails objects
	 */
	public function countAidDetailss($criteria = null, $distinct = false, PropelPDO $con = null)
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
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collAidDetailss);
		}
	}

	/**
	 * Associate a AidDetails object to this object
	 * through the j_aid_eleves cross reference table.
	 *
	 * @param      AidDetails $aidDetails The JAidEleves object to relate
	 * @return     void
	 */
	public function addAidDetails($aidDetails)
	{
		if ($this->collAidDetailss === null) {
			$this->initAidDetailss();
		}
		if (!$this->collAidDetailss->contains($aidDetails)) { // only add it if the **same** object is not already associated
			$jAidEleves = new JAidEleves();
			$jAidEleves->setAidDetails($aidDetails);
			$this->addJAidEleves($jAidEleves);
			
			$this->collAidDetailss[]= $aidDetails;
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->no_gep = null;
		$this->login = null;
		$this->nom = null;
		$this->prenom = null;
		$this->sexe = null;
		$this->naissance = null;
		$this->lieu_naissance = null;
		$this->elenoet = null;
		$this->ereno = null;
		$this->ele_id = null;
		$this->email = null;
		$this->id_eleve = null;
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
			if ($this->collJEleveClasses) {
				foreach ((array) $this->collJEleveClasses as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveCpes) {
				foreach ((array) $this->collJEleveCpes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveGroupes) {
				foreach ((array) $this->collJEleveGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveProfesseurPrincipals) {
				foreach ((array) $this->collJEleveProfesseurPrincipals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->singleEleveRegimeDoublant) {
				$this->singleEleveRegimeDoublant->clearAllReferences($deep);
			}
			if ($this->collResponsableInformations) {
				foreach ((array) $this->collResponsableInformations as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveAncienEtablissements) {
				foreach ((array) $this->collJEleveAncienEtablissements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidElevess) {
				foreach ((array) $this->collJAidElevess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ((array) $this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCreditEctss) {
				foreach ((array) $this->collCreditEctss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCreditEctsGlobals) {
				foreach ((array) $this->collCreditEctsGlobals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collArchiveEctss) {
				foreach ((array) $this->collArchiveEctss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJEleveClasses = null;
		$this->collJEleveCpes = null;
		$this->collJEleveGroupes = null;
		$this->collJEleveProfesseurPrincipals = null;
		$this->singleEleveRegimeDoublant = null;
		$this->collResponsableInformations = null;
		$this->collJEleveAncienEtablissements = null;
		$this->collJAidElevess = null;
		$this->collAbsenceEleveSaisies = null;
		$this->collCreditEctss = null;
		$this->collCreditEctsGlobals = null;
		$this->collArchiveEctss = null;
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

} // BaseEleve
