<?php

/**
 * Base class that represents a row from the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * @package    gepi.om
 */
abstract class BaseUtilisateur extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        UtilisateurPeer
	 */
	protected static $peer;

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
	 * The value for the civilite field.
	 * @var        string
	 */
	protected $civilite;

	/**
	 * The value for the password field.
	 * @var        string
	 */
	protected $password;

	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the show_email field.
	 * Note: this column has a database default value of: 'no'
	 * @var        string
	 */
	protected $show_email;

	/**
	 * The value for the statut field.
	 * @var        string
	 */
	protected $statut;

	/**
	 * The value for the etat field.
	 * @var        string
	 */
	protected $etat;

	/**
	 * The value for the change_mdp field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $change_mdp;

	/**
	 * The value for the date_verrouillage field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $date_verrouillage;

	/**
	 * The value for the password_ticket field.
	 * @var        string
	 */
	protected $password_ticket;

	/**
	 * The value for the ticket_expiration field.
	 * @var        string
	 */
	protected $ticket_expiration;

	/**
	 * The value for the niveau_alerte field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $niveau_alerte;

	/**
	 * The value for the observation_securite field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $observation_securite;

	/**
	 * The value for the temp_dir field.
	 * @var        string
	 */
	protected $temp_dir;

	/**
	 * The value for the numind field.
	 * @var        string
	 */
	protected $numind;

	/**
	 * The value for the auth_mode field.
	 * Note: this column has a database default value of: 'gepi'
	 * @var        string
	 */
	protected $auth_mode;

	/**
	 * @var        array CtCompteRendu[] Collection to store aggregation of CtCompteRendu objects.
	 */
	protected $collCtCompteRendus;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtCompteRendus.
	 */
	private $lastCtCompteRenduCriteria = null;

	/**
	 * @var        array CtTravailAFaire[] Collection to store aggregation of CtTravailAFaire objects.
	 */
	protected $collCtTravailAFaires;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCtTravailAFaires.
	 */
	private $lastCtTravailAFaireCriteria = null;

	/**
	 * @var        array JGroupesProfesseurs[] Collection to store aggregation of JGroupesProfesseurs objects.
	 */
	protected $collJGroupesProfesseurss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJGroupesProfesseurss.
	 */
	private $lastJGroupesProfesseursCriteria = null;

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
	 * Initializes internal state of BaseUtilisateur object.
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
		$this->show_email = 'no';
		$this->change_mdp = 'n';
		$this->date_verrouillage = '00:00:00';
		$this->niveau_alerte = 0;
		$this->observation_securite = 0;
		$this->auth_mode = 'gepi';
	}

	/**
	 * Get the [login] column value.
	 * Login de l'utilisateur, et clÃ© primaire de la table utilisateur
	 * @return     string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * Get the [nom] column value.
	 * Nom de l'utilisateur
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [prenom] column value.
	 * Prenom de l'utilisateur
	 * @return     string
	 */
	public function getPrenom()
	{
		return $this->prenom;
	}

	/**
	 * Get the [civilite] column value.
	 * Civilite
	 * @return     string
	 */
	public function getCivilite()
	{
		return $this->civilite;
	}

	/**
	 * Get the [password] column value.
	 * Mot de passe
	 * @return     string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Get the [email] column value.
	 * Email de l'utilisateur
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [show_email] column value.
	 * L'email de l'utilisateur est-il public (yes/no)
	 * @return     string
	 */
	public function getShowEmail()
	{
		return $this->show_email;
	}

	/**
	 * Get the [statut] column value.
	 * Statut de l'utilisateur
	 * @return     string
	 */
	public function getStatut()
	{
		return $this->statut;
	}

	/**
	 * Get the [etat] column value.
	 * Etat de l'utilisateur (actif/inactif)
	 * @return     string
	 */
	public function getEtat()
	{
		return $this->etat;
	}

	/**
	 * Get the [change_mdp] column value.
	 * L'utilisateur doit-il changer son mot de passe (y/n) (Ã  la premiere connexion par exemple)
	 * @return     string
	 */
	public function getChangeMdp()
	{
		return $this->change_mdp;
	}

	/**
	 * Get the [optionally formatted] temporal [date_verrouillage] column value.
	 * Date de verrouillage de l'utilisateur
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateVerrouillage($format = '%X')
	{
		if ($this->date_verrouillage === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->date_verrouillage);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_verrouillage, true), $x);
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
	 * Get the [password_ticket] column value.
	 * password_ticket de l'utilisateur
	 * @return     string
	 */
	public function getPasswordTicket()
	{
		return $this->password_ticket;
	}

	/**
	 * Get the [optionally formatted] temporal [ticket_expiration] column value.
	 * ticket_expiration de l'utilisateur
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getTicketExpiration($format = '%X')
	{
		if ($this->ticket_expiration === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->ticket_expiration);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ticket_expiration, true), $x);
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
	 * Get the [niveau_alerte] column value.
	 * niveau_alerte de l'utilisateur
	 * @return     int
	 */
	public function getNiveauAlerte()
	{
		return $this->niveau_alerte;
	}

	/**
	 * Get the [observation_securite] column value.
	 * observation_securite de l'utilisateur
	 * @return     int
	 */
	public function getObservationSecurite()
	{
		return $this->observation_securite;
	}

	/**
	 * Get the [temp_dir] column value.
	 * RÃ©pertoire temporaire de l'utilisateur
	 * @return     string
	 */
	public function getTempDir()
	{
		return $this->temp_dir;
	}

	/**
	 * Get the [numind] column value.
	 * numind de l'utilisateur
	 * @return     string
	 */
	public function getNumind()
	{
		return $this->numind;
	}

	/**
	 * Get the [auth_mode] column value.
	 * auth_mode de l'utilisateur (gepi/cas/ldap)
	 * @return     string
	 */
	public function getAuthMode()
	{
		return $this->auth_mode;
	}

	/**
	 * Set the value of [login] column.
	 * Login de l'utilisateur, et clÃ© primaire de la table utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->login !== $v) {
			$this->login = $v;
			$this->modifiedColumns[] = UtilisateurPeer::LOGIN;
		}

		return $this;
	} // setLogin()

	/**
	 * Set the value of [nom] column.
	 * Nom de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = UtilisateurPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [prenom] column.
	 * Prenom de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setPrenom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prenom !== $v) {
			$this->prenom = $v;
			$this->modifiedColumns[] = UtilisateurPeer::PRENOM;
		}

		return $this;
	} // setPrenom()

	/**
	 * Set the value of [civilite] column.
	 * Civilite
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setCivilite($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->civilite !== $v) {
			$this->civilite = $v;
			$this->modifiedColumns[] = UtilisateurPeer::CIVILITE;
		}

		return $this;
	} // setCivilite()

	/**
	 * Set the value of [password] column.
	 * Mot de passe
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setPassword($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password !== $v) {
			$this->password = $v;
			$this->modifiedColumns[] = UtilisateurPeer::PASSWORD;
		}

		return $this;
	} // setPassword()

	/**
	 * Set the value of [email] column.
	 * Email de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = UtilisateurPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [show_email] column.
	 * L'email de l'utilisateur est-il public (yes/no)
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setShowEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->show_email !== $v || $v === 'no') {
			$this->show_email = $v;
			$this->modifiedColumns[] = UtilisateurPeer::SHOW_EMAIL;
		}

		return $this;
	} // setShowEmail()

	/**
	 * Set the value of [statut] column.
	 * Statut de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setStatut($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->statut !== $v) {
			$this->statut = $v;
			$this->modifiedColumns[] = UtilisateurPeer::STATUT;
		}

		return $this;
	} // setStatut()

	/**
	 * Set the value of [etat] column.
	 * Etat de l'utilisateur (actif/inactif)
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setEtat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->etat !== $v) {
			$this->etat = $v;
			$this->modifiedColumns[] = UtilisateurPeer::ETAT;
		}

		return $this;
	} // setEtat()

	/**
	 * Set the value of [change_mdp] column.
	 * L'utilisateur doit-il changer son mot de passe (y/n) (Ã  la premiere connexion par exemple)
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setChangeMdp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->change_mdp !== $v || $v === 'n') {
			$this->change_mdp = $v;
			$this->modifiedColumns[] = UtilisateurPeer::CHANGE_MDP;
		}

		return $this;
	} // setChangeMdp()

	/**
	 * Sets the value of [date_verrouillage] column to a normalized version of the date/time value specified.
	 * Date de verrouillage de l'utilisateur
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Utilisateur The current object (for fluent API support)
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

			$currNorm = ($this->date_verrouillage !== null && $tmpDt = new DateTime($this->date_verrouillage)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->date_verrouillage = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = UtilisateurPeer::DATE_VERROUILLAGE;
			}
		} // if either are not null

		return $this;
	} // setDateVerrouillage()

	/**
	 * Set the value of [password_ticket] column.
	 * password_ticket de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setPasswordTicket($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password_ticket !== $v) {
			$this->password_ticket = $v;
			$this->modifiedColumns[] = UtilisateurPeer::PASSWORD_TICKET;
		}

		return $this;
	} // setPasswordTicket()

	/**
	 * Sets the value of [ticket_expiration] column to a normalized version of the date/time value specified.
	 * ticket_expiration de l'utilisateur
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setTicketExpiration($v)
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

		if ( $this->ticket_expiration !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->ticket_expiration !== null && $tmpDt = new DateTime($this->ticket_expiration)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->ticket_expiration = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = UtilisateurPeer::TICKET_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setTicketExpiration()

	/**
	 * Set the value of [niveau_alerte] column.
	 * niveau_alerte de l'utilisateur
	 * @param      int $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setNiveauAlerte($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->niveau_alerte !== $v || $v === 0) {
			$this->niveau_alerte = $v;
			$this->modifiedColumns[] = UtilisateurPeer::NIVEAU_ALERTE;
		}

		return $this;
	} // setNiveauAlerte()

	/**
	 * Set the value of [observation_securite] column.
	 * observation_securite de l'utilisateur
	 * @param      int $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setObservationSecurite($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->observation_securite !== $v || $v === 0) {
			$this->observation_securite = $v;
			$this->modifiedColumns[] = UtilisateurPeer::OBSERVATION_SECURITE;
		}

		return $this;
	} // setObservationSecurite()

	/**
	 * Set the value of [temp_dir] column.
	 * RÃ©pertoire temporaire de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setTempDir($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->temp_dir !== $v) {
			$this->temp_dir = $v;
			$this->modifiedColumns[] = UtilisateurPeer::TEMP_DIR;
		}

		return $this;
	} // setTempDir()

	/**
	 * Set the value of [numind] column.
	 * numind de l'utilisateur
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setNumind($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->numind !== $v) {
			$this->numind = $v;
			$this->modifiedColumns[] = UtilisateurPeer::NUMIND;
		}

		return $this;
	} // setNumind()

	/**
	 * Set the value of [auth_mode] column.
	 * auth_mode de l'utilisateur (gepi/cas/ldap)
	 * @param      string $v new value
	 * @return     Utilisateur The current object (for fluent API support)
	 */
	public function setAuthMode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->auth_mode !== $v || $v === 'gepi') {
			$this->auth_mode = $v;
			$this->modifiedColumns[] = UtilisateurPeer::AUTH_MODE;
		}

		return $this;
	} // setAuthMode()

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
			if (array_diff($this->modifiedColumns, array(UtilisateurPeer::SHOW_EMAIL,UtilisateurPeer::CHANGE_MDP,UtilisateurPeer::DATE_VERROUILLAGE,UtilisateurPeer::NIVEAU_ALERTE,UtilisateurPeer::OBSERVATION_SECURITE,UtilisateurPeer::AUTH_MODE))) {
				return false;
			}

			if ($this->show_email !== 'no') {
				return false;
			}

			if ($this->change_mdp !== 'n') {
				return false;
			}

			if ($this->date_verrouillage !== '00:00:00') {
				return false;
			}

			if ($this->niveau_alerte !== 0) {
				return false;
			}

			if ($this->observation_securite !== 0) {
				return false;
			}

			if ($this->auth_mode !== 'gepi') {
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

			$this->login = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->nom = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->prenom = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->civilite = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->password = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->email = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->show_email = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->statut = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->etat = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->change_mdp = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->date_verrouillage = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->password_ticket = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->ticket_expiration = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->niveau_alerte = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->observation_securite = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->temp_dir = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->numind = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->auth_mode = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 18; // 18 = UtilisateurPeer::NUM_COLUMNS - UtilisateurPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Utilisateur object", $e);
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
			$con = Propel::getConnection(UtilisateurPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = UtilisateurPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collCtCompteRendus = null;
			$this->lastCtCompteRenduCriteria = null;

			$this->collCtTravailAFaires = null;
			$this->lastCtTravailAFaireCriteria = null;

			$this->collJGroupesProfesseurss = null;
			$this->lastJGroupesProfesseursCriteria = null;

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
			$con = Propel::getConnection(UtilisateurPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			UtilisateurPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(UtilisateurPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			UtilisateurPeer::addInstanceToPool($this);
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
					$pk = UtilisateurPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += UtilisateurPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCtCompteRendus !== null) {
				foreach ($this->collCtCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCtTravailAFaires !== null) {
				foreach ($this->collCtTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJGroupesProfesseurss !== null) {
				foreach ($this->collJGroupesProfesseurss as $referrerFK) {
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


			if (($retval = UtilisateurPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCtCompteRendus !== null) {
					foreach ($this->collCtCompteRendus as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCtTravailAFaires !== null) {
					foreach ($this->collCtTravailAFaires as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJGroupesProfesseurss !== null) {
					foreach ($this->collJGroupesProfesseurss as $referrerFK) {
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
		$pos = UtilisateurPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getLogin();
				break;
			case 1:
				return $this->getNom();
				break;
			case 2:
				return $this->getPrenom();
				break;
			case 3:
				return $this->getCivilite();
				break;
			case 4:
				return $this->getPassword();
				break;
			case 5:
				return $this->getEmail();
				break;
			case 6:
				return $this->getShowEmail();
				break;
			case 7:
				return $this->getStatut();
				break;
			case 8:
				return $this->getEtat();
				break;
			case 9:
				return $this->getChangeMdp();
				break;
			case 10:
				return $this->getDateVerrouillage();
				break;
			case 11:
				return $this->getPasswordTicket();
				break;
			case 12:
				return $this->getTicketExpiration();
				break;
			case 13:
				return $this->getNiveauAlerte();
				break;
			case 14:
				return $this->getObservationSecurite();
				break;
			case 15:
				return $this->getTempDir();
				break;
			case 16:
				return $this->getNumind();
				break;
			case 17:
				return $this->getAuthMode();
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
		$keys = UtilisateurPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getLogin(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getPrenom(),
			$keys[3] => $this->getCivilite(),
			$keys[4] => $this->getPassword(),
			$keys[5] => $this->getEmail(),
			$keys[6] => $this->getShowEmail(),
			$keys[7] => $this->getStatut(),
			$keys[8] => $this->getEtat(),
			$keys[9] => $this->getChangeMdp(),
			$keys[10] => $this->getDateVerrouillage(),
			$keys[11] => $this->getPasswordTicket(),
			$keys[12] => $this->getTicketExpiration(),
			$keys[13] => $this->getNiveauAlerte(),
			$keys[14] => $this->getObservationSecurite(),
			$keys[15] => $this->getTempDir(),
			$keys[16] => $this->getNumind(),
			$keys[17] => $this->getAuthMode(),
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
		$pos = UtilisateurPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setLogin($value);
				break;
			case 1:
				$this->setNom($value);
				break;
			case 2:
				$this->setPrenom($value);
				break;
			case 3:
				$this->setCivilite($value);
				break;
			case 4:
				$this->setPassword($value);
				break;
			case 5:
				$this->setEmail($value);
				break;
			case 6:
				$this->setShowEmail($value);
				break;
			case 7:
				$this->setStatut($value);
				break;
			case 8:
				$this->setEtat($value);
				break;
			case 9:
				$this->setChangeMdp($value);
				break;
			case 10:
				$this->setDateVerrouillage($value);
				break;
			case 11:
				$this->setPasswordTicket($value);
				break;
			case 12:
				$this->setTicketExpiration($value);
				break;
			case 13:
				$this->setNiveauAlerte($value);
				break;
			case 14:
				$this->setObservationSecurite($value);
				break;
			case 15:
				$this->setTempDir($value);
				break;
			case 16:
				$this->setNumind($value);
				break;
			case 17:
				$this->setAuthMode($value);
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
		$keys = UtilisateurPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setLogin($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setPrenom($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCivilite($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPassword($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setEmail($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setShowEmail($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStatut($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setEtat($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setChangeMdp($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDateVerrouillage($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setPasswordTicket($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setTicketExpiration($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setNiveauAlerte($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setObservationSecurite($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setTempDir($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setNumind($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setAuthMode($arr[$keys[17]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);

		if ($this->isColumnModified(UtilisateurPeer::LOGIN)) $criteria->add(UtilisateurPeer::LOGIN, $this->login);
		if ($this->isColumnModified(UtilisateurPeer::NOM)) $criteria->add(UtilisateurPeer::NOM, $this->nom);
		if ($this->isColumnModified(UtilisateurPeer::PRENOM)) $criteria->add(UtilisateurPeer::PRENOM, $this->prenom);
		if ($this->isColumnModified(UtilisateurPeer::CIVILITE)) $criteria->add(UtilisateurPeer::CIVILITE, $this->civilite);
		if ($this->isColumnModified(UtilisateurPeer::PASSWORD)) $criteria->add(UtilisateurPeer::PASSWORD, $this->password);
		if ($this->isColumnModified(UtilisateurPeer::EMAIL)) $criteria->add(UtilisateurPeer::EMAIL, $this->email);
		if ($this->isColumnModified(UtilisateurPeer::SHOW_EMAIL)) $criteria->add(UtilisateurPeer::SHOW_EMAIL, $this->show_email);
		if ($this->isColumnModified(UtilisateurPeer::STATUT)) $criteria->add(UtilisateurPeer::STATUT, $this->statut);
		if ($this->isColumnModified(UtilisateurPeer::ETAT)) $criteria->add(UtilisateurPeer::ETAT, $this->etat);
		if ($this->isColumnModified(UtilisateurPeer::CHANGE_MDP)) $criteria->add(UtilisateurPeer::CHANGE_MDP, $this->change_mdp);
		if ($this->isColumnModified(UtilisateurPeer::DATE_VERROUILLAGE)) $criteria->add(UtilisateurPeer::DATE_VERROUILLAGE, $this->date_verrouillage);
		if ($this->isColumnModified(UtilisateurPeer::PASSWORD_TICKET)) $criteria->add(UtilisateurPeer::PASSWORD_TICKET, $this->password_ticket);
		if ($this->isColumnModified(UtilisateurPeer::TICKET_EXPIRATION)) $criteria->add(UtilisateurPeer::TICKET_EXPIRATION, $this->ticket_expiration);
		if ($this->isColumnModified(UtilisateurPeer::NIVEAU_ALERTE)) $criteria->add(UtilisateurPeer::NIVEAU_ALERTE, $this->niveau_alerte);
		if ($this->isColumnModified(UtilisateurPeer::OBSERVATION_SECURITE)) $criteria->add(UtilisateurPeer::OBSERVATION_SECURITE, $this->observation_securite);
		if ($this->isColumnModified(UtilisateurPeer::TEMP_DIR)) $criteria->add(UtilisateurPeer::TEMP_DIR, $this->temp_dir);
		if ($this->isColumnModified(UtilisateurPeer::NUMIND)) $criteria->add(UtilisateurPeer::NUMIND, $this->numind);
		if ($this->isColumnModified(UtilisateurPeer::AUTH_MODE)) $criteria->add(UtilisateurPeer::AUTH_MODE, $this->auth_mode);

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
		$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);

		$criteria->add(UtilisateurPeer::LOGIN, $this->login);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getLogin();
	}

	/**
	 * Generic method to set the primary key (login column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setLogin($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Utilisateur (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setLogin($this->login);

		$copyObj->setNom($this->nom);

		$copyObj->setPrenom($this->prenom);

		$copyObj->setCivilite($this->civilite);

		$copyObj->setPassword($this->password);

		$copyObj->setEmail($this->email);

		$copyObj->setShowEmail($this->show_email);

		$copyObj->setStatut($this->statut);

		$copyObj->setEtat($this->etat);

		$copyObj->setChangeMdp($this->change_mdp);

		$copyObj->setDateVerrouillage($this->date_verrouillage);

		$copyObj->setPasswordTicket($this->password_ticket);

		$copyObj->setTicketExpiration($this->ticket_expiration);

		$copyObj->setNiveauAlerte($this->niveau_alerte);

		$copyObj->setObservationSecurite($this->observation_securite);

		$copyObj->setTempDir($this->temp_dir);

		$copyObj->setNumind($this->numind);

		$copyObj->setAuthMode($this->auth_mode);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCtCompteRendus() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCtCompteRendu($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCtTravailAFaires() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCtTravailAFaire($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJGroupesProfesseurss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesProfesseurs($relObj->copy($deepCopy));
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
	 * @return     Utilisateur Clone of current object.
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
	 * @return     UtilisateurPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new UtilisateurPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collCtCompteRendus collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtCompteRendus()
	 */
	public function clearCtCompteRendus()
	{
		$this->collCtCompteRendus = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCtCompteRendus collection (array).
	 *
	 * By default this just sets the collCtCompteRendus collection to an empty array (like clearcollCtCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCtCompteRendus()
	{
		$this->collCtCompteRendus = array();
	}

	/**
	 * Gets an array of CtCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Utilisateur has previously been saved, it will retrieve
	 * related CtCompteRendus from storage. If this Utilisateur is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtCompteRendu[]
	 * @throws     PropelException
	 */
	public function getCtCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
			   $this->collCtCompteRendus = array();
			} else {

				$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

				CtCompteRenduPeer::addSelectColumns($criteria);
				$this->collCtCompteRendus = CtCompteRenduPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

				CtCompteRenduPeer::addSelectColumns($criteria);
				if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
					$this->collCtCompteRendus = CtCompteRenduPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;
		return $this->collCtCompteRendus;
	}

	/**
	 * Returns the number of related CtCompteRendu objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CtCompteRendu objects.
	 * @throws     PropelException
	 */
	public function countCtCompteRendus(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

				$count = CtCompteRenduPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

				if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
					$count = CtCompteRenduPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCtCompteRendus);
				}
			} else {
				$count = count($this->collCtCompteRendus);
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CtCompteRendu object to this object
	 * through the CtCompteRendu foreign key attribute.
	 *
	 * @param      CtCompteRendu $l CtCompteRendu
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCtCompteRendu(CtCompteRendu $l)
	{
		if ($this->collCtCompteRendus === null) {
			$this->initCtCompteRendus();
		}
		if (!in_array($l, $this->collCtCompteRendus, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCtCompteRendus, $l);
			$l->setUtilisateur($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Utilisateur is new, it will return
	 * an empty collection; or if this Utilisateur has previously
	 * been saved, it will retrieve related CtCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Utilisateur.
	 */
	public function getCtCompteRendusJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCtCompteRendus = array();
			} else {

				$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

				$this->collCtCompteRendus = CtCompteRenduPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CtCompteRenduPeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCtCompteRenduCriteria) || !$this->lastCtCompteRenduCriteria->equals($criteria)) {
				$this->collCtCompteRendus = CtCompteRenduPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastCtCompteRenduCriteria = $criteria;

		return $this->collCtCompteRendus;
	}

	/**
	 * Clears out the collCtTravailAFaires collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCtTravailAFaires()
	 */
	public function clearCtTravailAFaires()
	{
		$this->collCtTravailAFaires = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCtTravailAFaires collection (array).
	 *
	 * By default this just sets the collCtTravailAFaires collection to an empty array (like clearcollCtTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCtTravailAFaires()
	{
		$this->collCtTravailAFaires = array();
	}

	/**
	 * Gets an array of CtTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Utilisateur has previously been saved, it will retrieve
	 * related CtTravailAFaires from storage. If this Utilisateur is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CtTravailAFaire[]
	 * @throws     PropelException
	 */
	public function getCtTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
			   $this->collCtTravailAFaires = array();
			} else {

				$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

				CtTravailAFairePeer::addSelectColumns($criteria);
				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

				CtTravailAFairePeer::addSelectColumns($criteria);
				if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
					$this->collCtTravailAFaires = CtTravailAFairePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;
		return $this->collCtTravailAFaires;
	}

	/**
	 * Returns the number of related CtTravailAFaire objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CtTravailAFaire objects.
	 * @throws     PropelException
	 */
	public function countCtTravailAFaires(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

				$count = CtTravailAFairePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

				if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
					$count = CtTravailAFairePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCtTravailAFaires);
				}
			} else {
				$count = count($this->collCtTravailAFaires);
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CtTravailAFaire object to this object
	 * through the CtTravailAFaire foreign key attribute.
	 *
	 * @param      CtTravailAFaire $l CtTravailAFaire
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCtTravailAFaire(CtTravailAFaire $l)
	{
		if ($this->collCtTravailAFaires === null) {
			$this->initCtTravailAFaires();
		}
		if (!in_array($l, $this->collCtTravailAFaires, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCtTravailAFaires, $l);
			$l->setUtilisateur($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Utilisateur is new, it will return
	 * an empty collection; or if this Utilisateur has previously
	 * been saved, it will retrieve related CtTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Utilisateur.
	 */
	public function getCtTravailAFairesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCtTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCtTravailAFaires = array();
			} else {

				$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CtTravailAFairePeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCtTravailAFaireCriteria) || !$this->lastCtTravailAFaireCriteria->equals($criteria)) {
				$this->collCtTravailAFaires = CtTravailAFairePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastCtTravailAFaireCriteria = $criteria;

		return $this->collCtTravailAFaires;
	}

	/**
	 * Clears out the collJGroupesProfesseurss collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesProfesseurss()
	 */
	public function clearJGroupesProfesseurss()
	{
		$this->collJGroupesProfesseurss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesProfesseurss collection (array).
	 *
	 * By default this just sets the collJGroupesProfesseurss collection to an empty array (like clearcollJGroupesProfesseurss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJGroupesProfesseurss()
	{
		$this->collJGroupesProfesseurss = array();
	}

	/**
	 * Gets an array of JGroupesProfesseurs objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Utilisateur has previously been saved, it will retrieve
	 * related JGroupesProfesseurss from storage. If this Utilisateur is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JGroupesProfesseurs[]
	 * @throws     PropelException
	 */
	public function getJGroupesProfesseurss($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
			   $this->collJGroupesProfesseurss = array();
			} else {

				$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

				JGroupesProfesseursPeer::addSelectColumns($criteria);
				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

				JGroupesProfesseursPeer::addSelectColumns($criteria);
				if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
					$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;
		return $this->collJGroupesProfesseurss;
	}

	/**
	 * Returns the number of related JGroupesProfesseurs objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesProfesseurs objects.
	 * @throws     PropelException
	 */
	public function countJGroupesProfesseurss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

				$count = JGroupesProfesseursPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

				if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
					$count = JGroupesProfesseursPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJGroupesProfesseurss);
				}
			} else {
				$count = count($this->collJGroupesProfesseurss);
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JGroupesProfesseurs object to this object
	 * through the JGroupesProfesseurs foreign key attribute.
	 *
	 * @param      JGroupesProfesseurs $l JGroupesProfesseurs
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesProfesseurs(JGroupesProfesseurs $l)
	{
		if ($this->collJGroupesProfesseurss === null) {
			$this->initJGroupesProfesseurss();
		}
		if (!in_array($l, $this->collJGroupesProfesseurss, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJGroupesProfesseurss, $l);
			$l->setUtilisateur($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Utilisateur is new, it will return
	 * an empty collection; or if this Utilisateur has previously
	 * been saved, it will retrieve related JGroupesProfesseurss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Utilisateur.
	 */
	public function getJGroupesProfesseurssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesProfesseurss === null) {
			if ($this->isNew()) {
				$this->collJGroupesProfesseurss = array();
			} else {

				$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesProfesseursPeer::LOGIN, $this->login);

			if (!isset($this->lastJGroupesProfesseursCriteria) || !$this->lastJGroupesProfesseursCriteria->equals($criteria)) {
				$this->collJGroupesProfesseurss = JGroupesProfesseursPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesProfesseursCriteria = $criteria;

		return $this->collJGroupesProfesseurss;
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
			if ($this->collCtCompteRendus) {
				foreach ((array) $this->collCtCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCtTravailAFaires) {
				foreach ((array) $this->collCtTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJGroupesProfesseurss) {
				foreach ((array) $this->collJGroupesProfesseurss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCtCompteRendus = null;
		$this->collCtTravailAFaires = null;
		$this->collJGroupesProfesseurss = null;
	}

} // BaseUtilisateur
