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
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

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
	 * The value for the date_sortie field.
	 * @var        string
	 */
	protected $date_sortie;

	/**
	 * The value for the mef_code field.
	 * @var        int
	 */
	protected $mef_code;

	/**
	 * @var        Mef
	 */
	protected $aMef;

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
	 * @var        array AbsenceAgregationDecompte[] Collection to store aggregation of AbsenceAgregationDecompte objects.
	 */
	protected $collAbsenceAgregationDecomptes;

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
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $ancienEtablissementsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $aidDetailssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveClassesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveCpesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveGroupesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveProfesseurPrincipalsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $eleveRegimeDoublantsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $responsableInformationsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveAncienEtablissementsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jAidElevessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveSaisiesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceAgregationDecomptesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $creditEctssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $creditEctsGlobalsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $archiveEctssScheduledForDeletion = null;

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
	public function getId()
	{
		return $this->id_eleve;
	}

	/**
	 * Get the [optionally formatted] temporal [date_sortie] column value.
	 * Timestamp de sortie de l'élève de l'établissement (fin d'inscription)
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateSortie($format = 'Y-m-d H:i:s')
	{
		if ($this->date_sortie === null) {
			return null;
		}


		if ($this->date_sortie === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_sortie);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_sortie, true), $x);
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
	 * Get the [mef_code] column value.
	 * code mef de la formation de l'eleve
	 * @return     int
	 */
	public function getMefCode()
	{
		return $this->mef_code;
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
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setNaissance($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->naissance !== null || $dt !== null) {
			$currentDateAsString = ($this->naissance !== null && $tmpDt = new DateTime($this->naissance)) ? $tmpDt->format('Y-m-d') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->naissance = $newDateAsString;
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

		if ($this->lieu_naissance !== $v) {
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

		if ($this->ele_id !== $v) {
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

		if ($this->email !== $v) {
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
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_eleve !== $v) {
			$this->id_eleve = $v;
			$this->modifiedColumns[] = ElevePeer::ID_ELEVE;
		}

		return $this;
	} // setId()

	/**
	 * Sets the value of [date_sortie] column to a normalized version of the date/time value specified.
	 * Timestamp de sortie de l'élève de l'établissement (fin d'inscription)
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setDateSortie($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->date_sortie !== null || $dt !== null) {
			$currentDateAsString = ($this->date_sortie !== null && $tmpDt = new DateTime($this->date_sortie)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->date_sortie = $newDateAsString;
				$this->modifiedColumns[] = ElevePeer::DATE_SORTIE;
			}
		} // if either are not null

		return $this;
	} // setDateSortie()

	/**
	 * Set the value of [mef_code] column.
	 * code mef de la formation de l'eleve
	 * @param      int $v new value
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function setMefCode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->mef_code !== $v) {
			$this->mef_code = $v;
			$this->modifiedColumns[] = ElevePeer::MEF_CODE;
		}

		if ($this->aMef !== null && $this->aMef->getMefCode() !== $v) {
			$this->aMef = null;
		}

		return $this;
	} // setMefCode()

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
			$this->date_sortie = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->mef_code = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 14; // 14 = ElevePeer::NUM_HYDRATE_COLUMNS.

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

		if ($this->aMef !== null && $this->mef_code !== $this->aMef->getMefCode()) {
			$this->aMef = null;
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

			$this->aMef = null;
			$this->collJEleveClasses = null;

			$this->collJEleveCpes = null;

			$this->collJEleveGroupes = null;

			$this->collJEleveProfesseurPrincipals = null;

			$this->singleEleveRegimeDoublant = null;

			$this->collResponsableInformations = null;

			$this->collJEleveAncienEtablissements = null;

			$this->collJAidElevess = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collAbsenceAgregationDecomptes = null;

			$this->collCreditEctss = null;

			$this->collCreditEctsGlobals = null;

			$this->collArchiveEctss = null;

			$this->collAncienEtablissements = null;
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
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$deleteQuery = EleveQuery::create()
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

			if ($this->aMef !== null) {
				if ($this->aMef->isModified() || $this->aMef->isNew()) {
					$affectedRows += $this->aMef->save($con);
				}
				$this->setMef($this->aMef);
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

			if ($this->ancienEtablissementsScheduledForDeletion !== null) {
				if (!$this->ancienEtablissementsScheduledForDeletion->isEmpty()) {
					JEleveAncienEtablissementQuery::create()
						->filterByPrimaryKeys($this->ancienEtablissementsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->ancienEtablissementsScheduledForDeletion = null;
				}

				foreach ($this->getAncienEtablissements() as $ancienEtablissement) {
					if ($ancienEtablissement->isModified()) {
						$ancienEtablissement->save($con);
					}
				}
			}

			if ($this->aidDetailssScheduledForDeletion !== null) {
				if (!$this->aidDetailssScheduledForDeletion->isEmpty()) {
					JAidElevesQuery::create()
						->filterByPrimaryKeys($this->aidDetailssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->aidDetailssScheduledForDeletion = null;
				}

				foreach ($this->getAidDetailss() as $aidDetails) {
					if ($aidDetails->isModified()) {
						$aidDetails->save($con);
					}
				}
			}

			if ($this->jEleveClassesScheduledForDeletion !== null) {
				if (!$this->jEleveClassesScheduledForDeletion->isEmpty()) {
					JEleveClasseQuery::create()
						->filterByPrimaryKeys($this->jEleveClassesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveClassesScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveClasses !== null) {
				foreach ($this->collJEleveClasses as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jEleveCpesScheduledForDeletion !== null) {
				if (!$this->jEleveCpesScheduledForDeletion->isEmpty()) {
					JEleveCpeQuery::create()
						->filterByPrimaryKeys($this->jEleveCpesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveCpesScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveCpes !== null) {
				foreach ($this->collJEleveCpes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jEleveGroupesScheduledForDeletion !== null) {
				if (!$this->jEleveGroupesScheduledForDeletion->isEmpty()) {
					JEleveGroupeQuery::create()
						->filterByPrimaryKeys($this->jEleveGroupesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveGroupesScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveGroupes !== null) {
				foreach ($this->collJEleveGroupes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jEleveProfesseurPrincipalsScheduledForDeletion !== null) {
				if (!$this->jEleveProfesseurPrincipalsScheduledForDeletion->isEmpty()) {
					JEleveProfesseurPrincipalQuery::create()
						->filterByPrimaryKeys($this->jEleveProfesseurPrincipalsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveProfesseurPrincipalsScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveProfesseurPrincipals !== null) {
				foreach ($this->collJEleveProfesseurPrincipals as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->eleveRegimeDoublantsScheduledForDeletion !== null) {
				if (!$this->eleveRegimeDoublantsScheduledForDeletion->isEmpty()) {
					EleveRegimeDoublantQuery::create()
						->filterByPrimaryKeys($this->eleveRegimeDoublantsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->eleveRegimeDoublantsScheduledForDeletion = null;
				}
			}

			if ($this->singleEleveRegimeDoublant !== null) {
				if (!$this->singleEleveRegimeDoublant->isDeleted()) {
						$affectedRows += $this->singleEleveRegimeDoublant->save($con);
				}
			}

			if ($this->responsableInformationsScheduledForDeletion !== null) {
				if (!$this->responsableInformationsScheduledForDeletion->isEmpty()) {
					ResponsableInformationQuery::create()
						->filterByPrimaryKeys($this->responsableInformationsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->responsableInformationsScheduledForDeletion = null;
				}
			}

			if ($this->collResponsableInformations !== null) {
				foreach ($this->collResponsableInformations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jEleveAncienEtablissementsScheduledForDeletion !== null) {
				if (!$this->jEleveAncienEtablissementsScheduledForDeletion->isEmpty()) {
					JEleveAncienEtablissementQuery::create()
						->filterByPrimaryKeys($this->jEleveAncienEtablissementsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jEleveAncienEtablissementsScheduledForDeletion = null;
				}
			}

			if ($this->collJEleveAncienEtablissements !== null) {
				foreach ($this->collJEleveAncienEtablissements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jAidElevessScheduledForDeletion !== null) {
				if (!$this->jAidElevessScheduledForDeletion->isEmpty()) {
					JAidElevesQuery::create()
						->filterByPrimaryKeys($this->jAidElevessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jAidElevessScheduledForDeletion = null;
				}
			}

			if ($this->collJAidElevess !== null) {
				foreach ($this->collJAidElevess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->absenceEleveSaisiesScheduledForDeletion !== null) {
				if (!$this->absenceEleveSaisiesScheduledForDeletion->isEmpty()) {
					AbsenceEleveSaisieQuery::create()
						->filterByPrimaryKeys($this->absenceEleveSaisiesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveSaisiesScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveSaisies !== null) {
				foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->absenceAgregationDecomptesScheduledForDeletion !== null) {
				if (!$this->absenceAgregationDecomptesScheduledForDeletion->isEmpty()) {
					AbsenceAgregationDecompteQuery::create()
						->filterByPrimaryKeys($this->absenceAgregationDecomptesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceAgregationDecomptesScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceAgregationDecomptes !== null) {
				foreach ($this->collAbsenceAgregationDecomptes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->creditEctssScheduledForDeletion !== null) {
				if (!$this->creditEctssScheduledForDeletion->isEmpty()) {
					CreditEctsQuery::create()
						->filterByPrimaryKeys($this->creditEctssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->creditEctssScheduledForDeletion = null;
				}
			}

			if ($this->collCreditEctss !== null) {
				foreach ($this->collCreditEctss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->creditEctsGlobalsScheduledForDeletion !== null) {
				if (!$this->creditEctsGlobalsScheduledForDeletion->isEmpty()) {
					CreditEctsGlobalQuery::create()
						->filterByPrimaryKeys($this->creditEctsGlobalsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->creditEctsGlobalsScheduledForDeletion = null;
				}
			}

			if ($this->collCreditEctsGlobals !== null) {
				foreach ($this->collCreditEctsGlobals as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->archiveEctssScheduledForDeletion !== null) {
				if (!$this->archiveEctssScheduledForDeletion->isEmpty()) {
					ArchiveEctsQuery::create()
						->filterByPrimaryKeys($this->archiveEctssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->archiveEctssScheduledForDeletion = null;
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

		$this->modifiedColumns[] = ElevePeer::ID_ELEVE;
		if (null !== $this->id_eleve) {
			throw new PropelException('Cannot insert a value for auto-increment primary key (' . ElevePeer::ID_ELEVE . ')');
		}

		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(ElevePeer::NO_GEP)) {
			$modifiedColumns[':p' . $index++]  = 'NO_GEP';
		}
		if ($this->isColumnModified(ElevePeer::LOGIN)) {
			$modifiedColumns[':p' . $index++]  = 'LOGIN';
		}
		if ($this->isColumnModified(ElevePeer::NOM)) {
			$modifiedColumns[':p' . $index++]  = 'NOM';
		}
		if ($this->isColumnModified(ElevePeer::PRENOM)) {
			$modifiedColumns[':p' . $index++]  = 'PRENOM';
		}
		if ($this->isColumnModified(ElevePeer::SEXE)) {
			$modifiedColumns[':p' . $index++]  = 'SEXE';
		}
		if ($this->isColumnModified(ElevePeer::NAISSANCE)) {
			$modifiedColumns[':p' . $index++]  = 'NAISSANCE';
		}
		if ($this->isColumnModified(ElevePeer::LIEU_NAISSANCE)) {
			$modifiedColumns[':p' . $index++]  = 'LIEU_NAISSANCE';
		}
		if ($this->isColumnModified(ElevePeer::ELENOET)) {
			$modifiedColumns[':p' . $index++]  = 'ELENOET';
		}
		if ($this->isColumnModified(ElevePeer::ERENO)) {
			$modifiedColumns[':p' . $index++]  = 'ERENO';
		}
		if ($this->isColumnModified(ElevePeer::ELE_ID)) {
			$modifiedColumns[':p' . $index++]  = 'ELE_ID';
		}
		if ($this->isColumnModified(ElevePeer::EMAIL)) {
			$modifiedColumns[':p' . $index++]  = 'EMAIL';
		}
		if ($this->isColumnModified(ElevePeer::ID_ELEVE)) {
			$modifiedColumns[':p' . $index++]  = 'ID_ELEVE';
		}
		if ($this->isColumnModified(ElevePeer::DATE_SORTIE)) {
			$modifiedColumns[':p' . $index++]  = 'DATE_SORTIE';
		}
		if ($this->isColumnModified(ElevePeer::MEF_CODE)) {
			$modifiedColumns[':p' . $index++]  = 'MEF_CODE';
		}

		$sql = sprintf(
			'INSERT INTO eleves (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'NO_GEP':
						$stmt->bindValue($identifier, $this->no_gep, PDO::PARAM_STR);
						break;
					case 'LOGIN':
						$stmt->bindValue($identifier, $this->login, PDO::PARAM_STR);
						break;
					case 'NOM':
						$stmt->bindValue($identifier, $this->nom, PDO::PARAM_STR);
						break;
					case 'PRENOM':
						$stmt->bindValue($identifier, $this->prenom, PDO::PARAM_STR);
						break;
					case 'SEXE':
						$stmt->bindValue($identifier, $this->sexe, PDO::PARAM_STR);
						break;
					case 'NAISSANCE':
						$stmt->bindValue($identifier, $this->naissance, PDO::PARAM_STR);
						break;
					case 'LIEU_NAISSANCE':
						$stmt->bindValue($identifier, $this->lieu_naissance, PDO::PARAM_STR);
						break;
					case 'ELENOET':
						$stmt->bindValue($identifier, $this->elenoet, PDO::PARAM_STR);
						break;
					case 'ERENO':
						$stmt->bindValue($identifier, $this->ereno, PDO::PARAM_STR);
						break;
					case 'ELE_ID':
						$stmt->bindValue($identifier, $this->ele_id, PDO::PARAM_STR);
						break;
					case 'EMAIL':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
						break;
					case 'ID_ELEVE':
						$stmt->bindValue($identifier, $this->id_eleve, PDO::PARAM_INT);
						break;
					case 'DATE_SORTIE':
						$stmt->bindValue($identifier, $this->date_sortie, PDO::PARAM_STR);
						break;
					case 'MEF_CODE':
						$stmt->bindValue($identifier, $this->mef_code, PDO::PARAM_INT);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

		try {
			$pk = $con->lastInsertId();
		} catch (Exception $e) {
			throw new PropelException('Unable to get autoincrement id.', $e);
		}
		$this->setId($pk);

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

			if ($this->aMef !== null) {
				if (!$this->aMef->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aMef->getValidationFailures());
				}
			}


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

				if ($this->collAbsenceAgregationDecomptes !== null) {
					foreach ($this->collAbsenceAgregationDecomptes as $referrerFK) {
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
				return $this->getId();
				break;
			case 12:
				return $this->getDateSortie();
				break;
			case 13:
				return $this->getMefCode();
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
		if (isset($alreadyDumpedObjects['Eleve'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['Eleve'][$this->getPrimaryKey()] = true;
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
			$keys[11] => $this->getId(),
			$keys[12] => $this->getDateSortie(),
			$keys[13] => $this->getMefCode(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aMef) {
				$result['Mef'] = $this->aMef->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collJEleveClasses) {
				$result['JEleveClasses'] = $this->collJEleveClasses->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveCpes) {
				$result['JEleveCpes'] = $this->collJEleveCpes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveGroupes) {
				$result['JEleveGroupes'] = $this->collJEleveGroupes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveProfesseurPrincipals) {
				$result['JEleveProfesseurPrincipals'] = $this->collJEleveProfesseurPrincipals->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->singleEleveRegimeDoublant) {
				$result['EleveRegimeDoublant'] = $this->singleEleveRegimeDoublant->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
			}
			if (null !== $this->collResponsableInformations) {
				$result['ResponsableInformations'] = $this->collResponsableInformations->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveAncienEtablissements) {
				$result['JEleveAncienEtablissements'] = $this->collJEleveAncienEtablissements->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJAidElevess) {
				$result['JAidElevess'] = $this->collJAidElevess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveSaisies) {
				$result['AbsenceEleveSaisies'] = $this->collAbsenceEleveSaisies->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceAgregationDecomptes) {
				$result['AbsenceAgregationDecomptes'] = $this->collAbsenceAgregationDecomptes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCreditEctss) {
				$result['CreditEctss'] = $this->collCreditEctss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCreditEctsGlobals) {
				$result['CreditEctsGlobals'] = $this->collCreditEctsGlobals->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collArchiveEctss) {
				$result['ArchiveEctss'] = $this->collArchiveEctss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
				$this->setId($value);
				break;
			case 12:
				$this->setDateSortie($value);
				break;
			case 13:
				$this->setMefCode($value);
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
		if (array_key_exists($keys[11], $arr)) $this->setId($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDateSortie($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setMefCode($arr[$keys[13]]);
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
		if ($this->isColumnModified(ElevePeer::DATE_SORTIE)) $criteria->add(ElevePeer::DATE_SORTIE, $this->date_sortie);
		if ($this->isColumnModified(ElevePeer::MEF_CODE)) $criteria->add(ElevePeer::MEF_CODE, $this->mef_code);

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
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id_eleve column).
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
	 * @param      object $copyObj An object of Eleve (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNoGep($this->getNoGep());
		$copyObj->setLogin($this->getLogin());
		$copyObj->setNom($this->getNom());
		$copyObj->setPrenom($this->getPrenom());
		$copyObj->setSexe($this->getSexe());
		$copyObj->setNaissance($this->getNaissance());
		$copyObj->setLieuNaissance($this->getLieuNaissance());
		$copyObj->setElenoet($this->getElenoet());
		$copyObj->setEreno($this->getEreno());
		$copyObj->setEleId($this->getEleId());
		$copyObj->setEmail($this->getEmail());
		$copyObj->setDateSortie($this->getDateSortie());
		$copyObj->setMefCode($this->getMefCode());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

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

			foreach ($this->getAbsenceAgregationDecomptes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceAgregationDecompte($relObj->copy($deepCopy));
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

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setId(NULL); // this is a auto-increment column, so set to default value
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
	 * Declares an association between this object and a Mef object.
	 *
	 * @param      Mef $v
	 * @return     Eleve The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setMef(Mef $v = null)
	{
		if ($v === null) {
			$this->setMefCode(NULL);
		} else {
			$this->setMefCode($v->getMefCode());
		}

		$this->aMef = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Mef object, it will not be re-added.
		if ($v !== null) {
			$v->addEleve($this);
		}

		return $this;
	}


	/**
	 * Get the associated Mef object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Mef The associated Mef object.
	 * @throws     PropelException
	 */
	public function getMef(PropelPDO $con = null)
	{
		if ($this->aMef === null && ($this->mef_code !== null)) {
			$this->aMef = MefQuery::create()
				->filterByEleve($this) // here
				->findOne($con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aMef->addEleves($this);
			 */
		}
		return $this->aMef;
	}


	/**
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('JEleveClasse' == $relationName) {
			return $this->initJEleveClasses();
		}
		if ('JEleveCpe' == $relationName) {
			return $this->initJEleveCpes();
		}
		if ('JEleveGroupe' == $relationName) {
			return $this->initJEleveGroupes();
		}
		if ('JEleveProfesseurPrincipal' == $relationName) {
			return $this->initJEleveProfesseurPrincipals();
		}
		if ('ResponsableInformation' == $relationName) {
			return $this->initResponsableInformations();
		}
		if ('JEleveAncienEtablissement' == $relationName) {
			return $this->initJEleveAncienEtablissements();
		}
		if ('JAidEleves' == $relationName) {
			return $this->initJAidElevess();
		}
		if ('AbsenceEleveSaisie' == $relationName) {
			return $this->initAbsenceEleveSaisies();
		}
		if ('AbsenceAgregationDecompte' == $relationName) {
			return $this->initAbsenceAgregationDecomptes();
		}
		if ('CreditEcts' == $relationName) {
			return $this->initCreditEctss();
		}
		if ('CreditEctsGlobal' == $relationName) {
			return $this->initCreditEctsGlobals();
		}
		if ('ArchiveEcts' == $relationName) {
			return $this->initArchiveEctss();
		}
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveClasses($overrideExisting = true)
	{
		if (null !== $this->collJEleveClasses && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JEleveClasse objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveClasses A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveClasses(PropelCollection $jEleveClasses, PropelPDO $con = null)
	{
		$this->jEleveClassesScheduledForDeletion = $this->getJEleveClasses(new Criteria(), $con)->diff($jEleveClasses);

		foreach ($jEleveClasses as $jEleveClasse) {
			// Fix issue with collection modified by reference
			if ($jEleveClasse->isNew()) {
				$jEleveClasse->setEleve($this);
			}
			$this->addJEleveClasse($jEleveClasse);
		}

		$this->collJEleveClasses = $jEleveClasses;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJEleveClasse(JEleveClasse $l)
	{
		if ($this->collJEleveClasses === null) {
			$this->initJEleveClasses();
		}
		if (!$this->collJEleveClasses->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveClasse($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveClasse $jEleveClasse The jEleveClasse object to add.
	 */
	protected function doAddJEleveClasse($jEleveClasse)
	{
		$this->collJEleveClasses[]= $jEleveClasse;
		$jEleveClasse->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveCpes($overrideExisting = true)
	{
		if (null !== $this->collJEleveCpes && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JEleveCpe objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveCpes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveCpes(PropelCollection $jEleveCpes, PropelPDO $con = null)
	{
		$this->jEleveCpesScheduledForDeletion = $this->getJEleveCpes(new Criteria(), $con)->diff($jEleveCpes);

		foreach ($jEleveCpes as $jEleveCpe) {
			// Fix issue with collection modified by reference
			if ($jEleveCpe->isNew()) {
				$jEleveCpe->setEleve($this);
			}
			$this->addJEleveCpe($jEleveCpe);
		}

		$this->collJEleveCpes = $jEleveCpes;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJEleveCpe(JEleveCpe $l)
	{
		if ($this->collJEleveCpes === null) {
			$this->initJEleveCpes();
		}
		if (!$this->collJEleveCpes->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveCpe($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveCpe $jEleveCpe The jEleveCpe object to add.
	 */
	protected function doAddJEleveCpe($jEleveCpe)
	{
		$this->collJEleveCpes[]= $jEleveCpe;
		$jEleveCpe->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveGroupes($overrideExisting = true)
	{
		if (null !== $this->collJEleveGroupes && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JEleveGroupe objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveGroupes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveGroupes(PropelCollection $jEleveGroupes, PropelPDO $con = null)
	{
		$this->jEleveGroupesScheduledForDeletion = $this->getJEleveGroupes(new Criteria(), $con)->diff($jEleveGroupes);

		foreach ($jEleveGroupes as $jEleveGroupe) {
			// Fix issue with collection modified by reference
			if ($jEleveGroupe->isNew()) {
				$jEleveGroupe->setEleve($this);
			}
			$this->addJEleveGroupe($jEleveGroupe);
		}

		$this->collJEleveGroupes = $jEleveGroupes;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJEleveGroupe(JEleveGroupe $l)
	{
		if ($this->collJEleveGroupes === null) {
			$this->initJEleveGroupes();
		}
		if (!$this->collJEleveGroupes->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveGroupe($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveGroupe $jEleveGroupe The jEleveGroupe object to add.
	 */
	protected function doAddJEleveGroupe($jEleveGroupe)
	{
		$this->collJEleveGroupes[]= $jEleveGroupe;
		$jEleveGroupe->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveProfesseurPrincipals($overrideExisting = true)
	{
		if (null !== $this->collJEleveProfesseurPrincipals && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JEleveProfesseurPrincipal objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveProfesseurPrincipals A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveProfesseurPrincipals(PropelCollection $jEleveProfesseurPrincipals, PropelPDO $con = null)
	{
		$this->jEleveProfesseurPrincipalsScheduledForDeletion = $this->getJEleveProfesseurPrincipals(new Criteria(), $con)->diff($jEleveProfesseurPrincipals);

		foreach ($jEleveProfesseurPrincipals as $jEleveProfesseurPrincipal) {
			// Fix issue with collection modified by reference
			if ($jEleveProfesseurPrincipal->isNew()) {
				$jEleveProfesseurPrincipal->setEleve($this);
			}
			$this->addJEleveProfesseurPrincipal($jEleveProfesseurPrincipal);
		}

		$this->collJEleveProfesseurPrincipals = $jEleveProfesseurPrincipals;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJEleveProfesseurPrincipal(JEleveProfesseurPrincipal $l)
	{
		if ($this->collJEleveProfesseurPrincipals === null) {
			$this->initJEleveProfesseurPrincipals();
		}
		if (!$this->collJEleveProfesseurPrincipals->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveProfesseurPrincipal($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveProfesseurPrincipal $jEleveProfesseurPrincipal The jEleveProfesseurPrincipal object to add.
	 */
	protected function doAddJEleveProfesseurPrincipal($jEleveProfesseurPrincipal)
	{
		$this->collJEleveProfesseurPrincipals[]= $jEleveProfesseurPrincipal;
		$jEleveProfesseurPrincipal->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initResponsableInformations($overrideExisting = true)
	{
		if (null !== $this->collResponsableInformations && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of ResponsableInformation objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $responsableInformations A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setResponsableInformations(PropelCollection $responsableInformations, PropelPDO $con = null)
	{
		$this->responsableInformationsScheduledForDeletion = $this->getResponsableInformations(new Criteria(), $con)->diff($responsableInformations);

		foreach ($responsableInformations as $responsableInformation) {
			// Fix issue with collection modified by reference
			if ($responsableInformation->isNew()) {
				$responsableInformation->setEleve($this);
			}
			$this->addResponsableInformation($responsableInformation);
		}

		$this->collResponsableInformations = $responsableInformations;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addResponsableInformation(ResponsableInformation $l)
	{
		if ($this->collResponsableInformations === null) {
			$this->initResponsableInformations();
		}
		if (!$this->collResponsableInformations->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddResponsableInformation($l);
		}

		return $this;
	}

	/**
	 * @param	ResponsableInformation $responsableInformation The responsableInformation object to add.
	 */
	protected function doAddResponsableInformation($responsableInformation)
	{
		$this->collResponsableInformations[]= $responsableInformation;
		$responsableInformation->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJEleveAncienEtablissements($overrideExisting = true)
	{
		if (null !== $this->collJEleveAncienEtablissements && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JEleveAncienEtablissement objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jEleveAncienEtablissements A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJEleveAncienEtablissements(PropelCollection $jEleveAncienEtablissements, PropelPDO $con = null)
	{
		$this->jEleveAncienEtablissementsScheduledForDeletion = $this->getJEleveAncienEtablissements(new Criteria(), $con)->diff($jEleveAncienEtablissements);

		foreach ($jEleveAncienEtablissements as $jEleveAncienEtablissement) {
			// Fix issue with collection modified by reference
			if ($jEleveAncienEtablissement->isNew()) {
				$jEleveAncienEtablissement->setEleve($this);
			}
			$this->addJEleveAncienEtablissement($jEleveAncienEtablissement);
		}

		$this->collJEleveAncienEtablissements = $jEleveAncienEtablissements;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJEleveAncienEtablissement(JEleveAncienEtablissement $l)
	{
		if ($this->collJEleveAncienEtablissements === null) {
			$this->initJEleveAncienEtablissements();
		}
		if (!$this->collJEleveAncienEtablissements->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJEleveAncienEtablissement($l);
		}

		return $this;
	}

	/**
	 * @param	JEleveAncienEtablissement $jEleveAncienEtablissement The jEleveAncienEtablissement object to add.
	 */
	protected function doAddJEleveAncienEtablissement($jEleveAncienEtablissement)
	{
		$this->collJEleveAncienEtablissements[]= $jEleveAncienEtablissement;
		$jEleveAncienEtablissement->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJAidElevess($overrideExisting = true)
	{
		if (null !== $this->collJAidElevess && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of JAidEleves objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jAidElevess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJAidElevess(PropelCollection $jAidElevess, PropelPDO $con = null)
	{
		$this->jAidElevessScheduledForDeletion = $this->getJAidElevess(new Criteria(), $con)->diff($jAidElevess);

		foreach ($jAidElevess as $jAidEleves) {
			// Fix issue with collection modified by reference
			if ($jAidEleves->isNew()) {
				$jAidEleves->setEleve($this);
			}
			$this->addJAidEleves($jAidEleves);
		}

		$this->collJAidElevess = $jAidElevess;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addJAidEleves(JAidEleves $l)
	{
		if ($this->collJAidElevess === null) {
			$this->initJAidElevess();
		}
		if (!$this->collJAidElevess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJAidEleves($l);
		}

		return $this;
	}

	/**
	 * @param	JAidEleves $jAidEleves The jAidEleves object to add.
	 */
	protected function doAddJAidEleves($jAidEleves)
	{
		$this->collJAidElevess[]= $jAidEleves;
		$jAidEleves->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveSaisies($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveSaisies && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of AbsenceEleveSaisie objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveSaisies A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveSaisies(PropelCollection $absenceEleveSaisies, PropelPDO $con = null)
	{
		$this->absenceEleveSaisiesScheduledForDeletion = $this->getAbsenceEleveSaisies(new Criteria(), $con)->diff($absenceEleveSaisies);

		foreach ($absenceEleveSaisies as $absenceEleveSaisie) {
			// Fix issue with collection modified by reference
			if ($absenceEleveSaisie->isNew()) {
				$absenceEleveSaisie->setEleve($this);
			}
			$this->addAbsenceEleveSaisie($absenceEleveSaisie);
		}

		$this->collAbsenceEleveSaisies = $absenceEleveSaisies;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addAbsenceEleveSaisie(AbsenceEleveSaisie $l)
	{
		if ($this->collAbsenceEleveSaisies === null) {
			$this->initAbsenceEleveSaisies();
		}
		if (!$this->collAbsenceEleveSaisies->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveSaisie($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveSaisie $absenceEleveSaisie The absenceEleveSaisie object to add.
	 */
	protected function doAddAbsenceEleveSaisie($absenceEleveSaisie)
	{
		$this->collAbsenceEleveSaisies[]= $absenceEleveSaisie;
		$absenceEleveSaisie->setEleve($this);
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
	public function getAbsenceEleveSaisiesJoinAbsenceEleveLieu($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveLieu', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}

	/**
	 * Clears out the collAbsenceAgregationDecomptes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceAgregationDecomptes()
	 */
	public function clearAbsenceAgregationDecomptes()
	{
		$this->collAbsenceAgregationDecomptes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceAgregationDecomptes collection.
	 *
	 * By default this just sets the collAbsenceAgregationDecomptes collection to an empty array (like clearcollAbsenceAgregationDecomptes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceAgregationDecomptes($overrideExisting = true)
	{
		if (null !== $this->collAbsenceAgregationDecomptes && !$overrideExisting) {
			return;
		}
		$this->collAbsenceAgregationDecomptes = new PropelObjectCollection();
		$this->collAbsenceAgregationDecomptes->setModel('AbsenceAgregationDecompte');
	}

	/**
	 * Gets an array of AbsenceAgregationDecompte objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this Eleve is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceAgregationDecompte[] List of AbsenceAgregationDecompte objects
	 * @throws     PropelException
	 */
	public function getAbsenceAgregationDecomptes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceAgregationDecomptes || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceAgregationDecomptes) {
				// return empty collection
				$this->initAbsenceAgregationDecomptes();
			} else {
				$collAbsenceAgregationDecomptes = AbsenceAgregationDecompteQuery::create(null, $criteria)
					->filterByEleve($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceAgregationDecomptes;
				}
				$this->collAbsenceAgregationDecomptes = $collAbsenceAgregationDecomptes;
			}
		}
		return $this->collAbsenceAgregationDecomptes;
	}

	/**
	 * Sets a collection of AbsenceAgregationDecompte objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceAgregationDecomptes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceAgregationDecomptes(PropelCollection $absenceAgregationDecomptes, PropelPDO $con = null)
	{
		$this->absenceAgregationDecomptesScheduledForDeletion = $this->getAbsenceAgregationDecomptes(new Criteria(), $con)->diff($absenceAgregationDecomptes);

		foreach ($absenceAgregationDecomptes as $absenceAgregationDecompte) {
			// Fix issue with collection modified by reference
			if ($absenceAgregationDecompte->isNew()) {
				$absenceAgregationDecompte->setEleve($this);
			}
			$this->addAbsenceAgregationDecompte($absenceAgregationDecompte);
		}

		$this->collAbsenceAgregationDecomptes = $absenceAgregationDecomptes;
	}

	/**
	 * Returns the number of related AbsenceAgregationDecompte objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceAgregationDecompte objects.
	 * @throws     PropelException
	 */
	public function countAbsenceAgregationDecomptes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceAgregationDecomptes || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceAgregationDecomptes) {
				return 0;
			} else {
				$query = AbsenceAgregationDecompteQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByEleve($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceAgregationDecomptes);
		}
	}

	/**
	 * Method called to associate a AbsenceAgregationDecompte object to this object
	 * through the AbsenceAgregationDecompte foreign key attribute.
	 *
	 * @param      AbsenceAgregationDecompte $l AbsenceAgregationDecompte
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addAbsenceAgregationDecompte(AbsenceAgregationDecompte $l)
	{
		if ($this->collAbsenceAgregationDecomptes === null) {
			$this->initAbsenceAgregationDecomptes();
		}
		if (!$this->collAbsenceAgregationDecomptes->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceAgregationDecompte($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceAgregationDecompte $absenceAgregationDecompte The absenceAgregationDecompte object to add.
	 */
	protected function doAddAbsenceAgregationDecompte($absenceAgregationDecompte)
	{
		$this->collAbsenceAgregationDecomptes[]= $absenceAgregationDecompte;
		$absenceAgregationDecompte->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCreditEctss($overrideExisting = true)
	{
		if (null !== $this->collCreditEctss && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of CreditEcts objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $creditEctss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCreditEctss(PropelCollection $creditEctss, PropelPDO $con = null)
	{
		$this->creditEctssScheduledForDeletion = $this->getCreditEctss(new Criteria(), $con)->diff($creditEctss);

		foreach ($creditEctss as $creditEcts) {
			// Fix issue with collection modified by reference
			if ($creditEcts->isNew()) {
				$creditEcts->setEleve($this);
			}
			$this->addCreditEcts($creditEcts);
		}

		$this->collCreditEctss = $creditEctss;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addCreditEcts(CreditEcts $l)
	{
		if ($this->collCreditEctss === null) {
			$this->initCreditEctss();
		}
		if (!$this->collCreditEctss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCreditEcts($l);
		}

		return $this;
	}

	/**
	 * @param	CreditEcts $creditEcts The creditEcts object to add.
	 */
	protected function doAddCreditEcts($creditEcts)
	{
		$this->collCreditEctss[]= $creditEcts;
		$creditEcts->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCreditEctsGlobals($overrideExisting = true)
	{
		if (null !== $this->collCreditEctsGlobals && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of CreditEctsGlobal objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $creditEctsGlobals A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCreditEctsGlobals(PropelCollection $creditEctsGlobals, PropelPDO $con = null)
	{
		$this->creditEctsGlobalsScheduledForDeletion = $this->getCreditEctsGlobals(new Criteria(), $con)->diff($creditEctsGlobals);

		foreach ($creditEctsGlobals as $creditEctsGlobal) {
			// Fix issue with collection modified by reference
			if ($creditEctsGlobal->isNew()) {
				$creditEctsGlobal->setEleve($this);
			}
			$this->addCreditEctsGlobal($creditEctsGlobal);
		}

		$this->collCreditEctsGlobals = $creditEctsGlobals;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addCreditEctsGlobal(CreditEctsGlobal $l)
	{
		if ($this->collCreditEctsGlobals === null) {
			$this->initCreditEctsGlobals();
		}
		if (!$this->collCreditEctsGlobals->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCreditEctsGlobal($l);
		}

		return $this;
	}

	/**
	 * @param	CreditEctsGlobal $creditEctsGlobal The creditEctsGlobal object to add.
	 */
	protected function doAddCreditEctsGlobal($creditEctsGlobal)
	{
		$this->collCreditEctsGlobals[]= $creditEctsGlobal;
		$creditEctsGlobal->setEleve($this);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initArchiveEctss($overrideExisting = true)
	{
		if (null !== $this->collArchiveEctss && !$overrideExisting) {
			return;
		}
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
	 * Sets a collection of ArchiveEcts objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $archiveEctss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setArchiveEctss(PropelCollection $archiveEctss, PropelPDO $con = null)
	{
		$this->archiveEctssScheduledForDeletion = $this->getArchiveEctss(new Criteria(), $con)->diff($archiveEctss);

		foreach ($archiveEctss as $archiveEcts) {
			// Fix issue with collection modified by reference
			if ($archiveEcts->isNew()) {
				$archiveEcts->setEleve($this);
			}
			$this->addArchiveEcts($archiveEcts);
		}

		$this->collArchiveEctss = $archiveEctss;
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
	 * @return     Eleve The current object (for fluent API support)
	 */
	public function addArchiveEcts(ArchiveEcts $l)
	{
		if ($this->collArchiveEctss === null) {
			$this->initArchiveEctss();
		}
		if (!$this->collArchiveEctss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddArchiveEcts($l);
		}

		return $this;
	}

	/**
	 * @param	ArchiveEcts $archiveEcts The archiveEcts object to add.
	 */
	protected function doAddArchiveEcts($archiveEcts)
	{
		$this->collArchiveEctss[]= $archiveEcts;
		$archiveEcts->setEleve($this);
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
	 * Sets a collection of AncienEtablissement objects related by a many-to-many relationship
	 * to the current object by way of the j_eleves_etablissements cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $ancienEtablissements A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAncienEtablissements(PropelCollection $ancienEtablissements, PropelPDO $con = null)
	{
		$jEleveAncienEtablissements = JEleveAncienEtablissementQuery::create()
			->filterByAncienEtablissement($ancienEtablissements)
			->filterByEleve($this)
			->find($con);

		$this->ancienEtablissementsScheduledForDeletion = $this->getJEleveAncienEtablissements()->diff($jEleveAncienEtablissements);
		$this->collJEleveAncienEtablissements = $jEleveAncienEtablissements;

		foreach ($ancienEtablissements as $ancienEtablissement) {
			// Fix issue with collection modified by reference
			if ($ancienEtablissement->isNew()) {
				$this->doAddAncienEtablissement($ancienEtablissement);
			} else {
				$this->addAncienEtablissement($ancienEtablissement);
			}
		}

		$this->collAncienEtablissements = $ancienEtablissements;
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
	public function addAncienEtablissement(AncienEtablissement $ancienEtablissement)
	{
		if ($this->collAncienEtablissements === null) {
			$this->initAncienEtablissements();
		}
		if (!$this->collAncienEtablissements->contains($ancienEtablissement)) { // only add it if the **same** object is not already associated
			$this->doAddAncienEtablissement($ancienEtablissement);

			$this->collAncienEtablissements[]= $ancienEtablissement;
		}
	}

	/**
	 * @param	AncienEtablissement $ancienEtablissement The ancienEtablissement object to add.
	 */
	protected function doAddAncienEtablissement($ancienEtablissement)
	{
		$jEleveAncienEtablissement = new JEleveAncienEtablissement();
		$jEleveAncienEtablissement->setAncienEtablissement($ancienEtablissement);
		$this->addJEleveAncienEtablissement($jEleveAncienEtablissement);
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
	 * Sets a collection of AidDetails objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $aidDetailss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAidDetailss(PropelCollection $aidDetailss, PropelPDO $con = null)
	{
		$jAidElevess = JAidElevesQuery::create()
			->filterByAidDetails($aidDetailss)
			->filterByEleve($this)
			->find($con);

		$this->aidDetailssScheduledForDeletion = $this->getJAidElevess()->diff($jAidElevess);
		$this->collJAidElevess = $jAidElevess;

		foreach ($aidDetailss as $aidDetails) {
			// Fix issue with collection modified by reference
			if ($aidDetails->isNew()) {
				$this->doAddAidDetails($aidDetails);
			} else {
				$this->addAidDetails($aidDetails);
			}
		}

		$this->collAidDetailss = $aidDetailss;
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
	public function addAidDetails(AidDetails $aidDetails)
	{
		if ($this->collAidDetailss === null) {
			$this->initAidDetailss();
		}
		if (!$this->collAidDetailss->contains($aidDetails)) { // only add it if the **same** object is not already associated
			$this->doAddAidDetails($aidDetails);

			$this->collAidDetailss[]= $aidDetails;
		}
	}

	/**
	 * @param	AidDetails $aidDetails The aidDetails object to add.
	 */
	protected function doAddAidDetails($aidDetails)
	{
		$jAidEleves = new JAidEleves();
		$jAidEleves->setAidDetails($aidDetails);
		$this->addJAidEleves($jAidEleves);
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
		$this->date_sortie = null;
		$this->mef_code = null;
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
			if ($this->collJEleveClasses) {
				foreach ($this->collJEleveClasses as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveCpes) {
				foreach ($this->collJEleveCpes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveGroupes) {
				foreach ($this->collJEleveGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveProfesseurPrincipals) {
				foreach ($this->collJEleveProfesseurPrincipals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->singleEleveRegimeDoublant) {
				$this->singleEleveRegimeDoublant->clearAllReferences($deep);
			}
			if ($this->collResponsableInformations) {
				foreach ($this->collResponsableInformations as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveAncienEtablissements) {
				foreach ($this->collJEleveAncienEtablissements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidElevess) {
				foreach ($this->collJAidElevess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ($this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceAgregationDecomptes) {
				foreach ($this->collAbsenceAgregationDecomptes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCreditEctss) {
				foreach ($this->collCreditEctss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCreditEctsGlobals) {
				foreach ($this->collCreditEctsGlobals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collArchiveEctss) {
				foreach ($this->collArchiveEctss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAncienEtablissements) {
				foreach ($this->collAncienEtablissements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAidDetailss) {
				foreach ($this->collAidDetailss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJEleveClasses instanceof PropelCollection) {
			$this->collJEleveClasses->clearIterator();
		}
		$this->collJEleveClasses = null;
		if ($this->collJEleveCpes instanceof PropelCollection) {
			$this->collJEleveCpes->clearIterator();
		}
		$this->collJEleveCpes = null;
		if ($this->collJEleveGroupes instanceof PropelCollection) {
			$this->collJEleveGroupes->clearIterator();
		}
		$this->collJEleveGroupes = null;
		if ($this->collJEleveProfesseurPrincipals instanceof PropelCollection) {
			$this->collJEleveProfesseurPrincipals->clearIterator();
		}
		$this->collJEleveProfesseurPrincipals = null;
		if ($this->singleEleveRegimeDoublant instanceof PropelCollection) {
			$this->singleEleveRegimeDoublant->clearIterator();
		}
		$this->singleEleveRegimeDoublant = null;
		if ($this->collResponsableInformations instanceof PropelCollection) {
			$this->collResponsableInformations->clearIterator();
		}
		$this->collResponsableInformations = null;
		if ($this->collJEleveAncienEtablissements instanceof PropelCollection) {
			$this->collJEleveAncienEtablissements->clearIterator();
		}
		$this->collJEleveAncienEtablissements = null;
		if ($this->collJAidElevess instanceof PropelCollection) {
			$this->collJAidElevess->clearIterator();
		}
		$this->collJAidElevess = null;
		if ($this->collAbsenceEleveSaisies instanceof PropelCollection) {
			$this->collAbsenceEleveSaisies->clearIterator();
		}
		$this->collAbsenceEleveSaisies = null;
		if ($this->collAbsenceAgregationDecomptes instanceof PropelCollection) {
			$this->collAbsenceAgregationDecomptes->clearIterator();
		}
		$this->collAbsenceAgregationDecomptes = null;
		if ($this->collCreditEctss instanceof PropelCollection) {
			$this->collCreditEctss->clearIterator();
		}
		$this->collCreditEctss = null;
		if ($this->collCreditEctsGlobals instanceof PropelCollection) {
			$this->collCreditEctsGlobals->clearIterator();
		}
		$this->collCreditEctsGlobals = null;
		if ($this->collArchiveEctss instanceof PropelCollection) {
			$this->collArchiveEctss->clearIterator();
		}
		$this->collArchiveEctss = null;
		if ($this->collAncienEtablissements instanceof PropelCollection) {
			$this->collAncienEtablissements->clearIterator();
		}
		$this->collAncienEtablissements = null;
		if ($this->collAidDetailss instanceof PropelCollection) {
			$this->collAidDetailss->clearIterator();
		}
		$this->collAidDetailss = null;
		$this->aMef = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(ElevePeer::DEFAULT_STRING_FORMAT);
	}

} // BaseEleve
