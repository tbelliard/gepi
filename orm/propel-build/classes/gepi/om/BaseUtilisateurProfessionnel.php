<?php

/**
 * Base class that represents a row from the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * @package    gepi.om
 */
abstract class BaseUtilisateurProfessionnel extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        UtilisateurProfessionnelPeer
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
	 * @var        array JGroupesProfesseurs[] Collection to store aggregation of JGroupesProfesseurs objects.
	 */
	protected $collJGroupesProfesseurss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJGroupesProfesseurss.
	 */
	private $lastJGroupesProfesseursCriteria = null;

	/**
	 * @var        array CahierTexteCompteRendu[] Collection to store aggregation of CahierTexteCompteRendu objects.
	 */
	protected $collCahierTexteCompteRendus;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteCompteRendus.
	 */
	private $lastCahierTexteCompteRenduCriteria = null;

	/**
	 * @var        array CahierTexteTravailAFaire[] Collection to store aggregation of CahierTexteTravailAFaire objects.
	 */
	protected $collCahierTexteTravailAFaires;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteTravailAFaires.
	 */
	private $lastCahierTexteTravailAFaireCriteria = null;

	/**
	 * @var        array CahierTexteNoticePrivee[] Collection to store aggregation of CahierTexteNoticePrivee objects.
	 */
	protected $collCahierTexteNoticePrivees;

	/**
	 * @var        Criteria The criteria used to select the current contents of collCahierTexteNoticePrivees.
	 */
	private $lastCahierTexteNoticePriveeCriteria = null;

	/**
	 * @var        array JEleveCpe[] Collection to store aggregation of JEleveCpe objects.
	 */
	protected $collJEleveCpes;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJEleveCpes.
	 */
	private $lastJEleveCpeCriteria = null;

	/**
	 * @var        array JEleveProfesseurPrincipal[] Collection to store aggregation of JEleveProfesseurPrincipal objects.
	 */
	protected $collJEleveProfesseurPrincipals;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJEleveProfesseurPrincipals.
	 */
	private $lastJEleveProfesseurPrincipalCriteria = null;

	/**
	 * @var        array JAidUtilisateursProfessionnels[] Collection to store aggregation of JAidUtilisateursProfessionnels objects.
	 */
	protected $collJAidUtilisateursProfessionnelss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJAidUtilisateursProfessionnelss.
	 */
	private $lastJAidUtilisateursProfessionnelsCriteria = null;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveSaisies.
	 */
	private $lastAbsenceEleveSaisieCriteria = null;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collAbsenceEleveTraitements;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveTraitements.
	 */
	private $lastAbsenceEleveTraitementCriteria = null;

	/**
	 * @var        array AbsenceEleveEnvoi[] Collection to store aggregation of AbsenceEleveEnvoi objects.
	 */
	protected $collAbsenceEleveEnvois;

	/**
	 * @var        Criteria The criteria used to select the current contents of collAbsenceEleveEnvois.
	 */
	private $lastAbsenceEleveEnvoiCriteria = null;

	/**
	 * @var        array PreferenceUtilisateurProfessionnel[] Collection to store aggregation of PreferenceUtilisateurProfessionnel objects.
	 */
	protected $collPreferenceUtilisateurProfessionnels;

	/**
	 * @var        Criteria The criteria used to select the current contents of collPreferenceUtilisateurProfessionnels.
	 */
	private $lastPreferenceUtilisateurProfessionnelCriteria = null;

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
	 * Initializes internal state of BaseUtilisateurProfessionnel object.
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
	 * L'utilisateur doit-il changer son mot de passe (y/n) (a la premiere connexion par exemple)
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
	 * Repertoire temporaire de l'utilisateur
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setLogin($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->login !== $v) {
			$this->login = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::LOGIN;
		}

		return $this;
	} // setLogin()

	/**
	 * Set the value of [nom] column.
	 * Nom de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v) {
			$this->nom = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [prenom] column.
	 * Prenom de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setPrenom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prenom !== $v) {
			$this->prenom = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::PRENOM;
		}

		return $this;
	} // setPrenom()

	/**
	 * Set the value of [civilite] column.
	 * Civilite
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setCivilite($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->civilite !== $v) {
			$this->civilite = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::CIVILITE;
		}

		return $this;
	} // setCivilite()

	/**
	 * Set the value of [password] column.
	 * Mot de passe
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setPassword($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password !== $v) {
			$this->password = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::PASSWORD;
		}

		return $this;
	} // setPassword()

	/**
	 * Set the value of [email] column.
	 * Email de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [show_email] column.
	 * L'email de l'utilisateur est-il public (yes/no)
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setShowEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->show_email !== $v || $v === 'no') {
			$this->show_email = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::SHOW_EMAIL;
		}

		return $this;
	} // setShowEmail()

	/**
	 * Set the value of [statut] column.
	 * Statut de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setStatut($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->statut !== $v) {
			$this->statut = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::STATUT;
		}

		return $this;
	} // setStatut()

	/**
	 * Set the value of [etat] column.
	 * Etat de l'utilisateur (actif/inactif)
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setEtat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->etat !== $v) {
			$this->etat = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::ETAT;
		}

		return $this;
	} // setEtat()

	/**
	 * Set the value of [change_mdp] column.
	 * L'utilisateur doit-il changer son mot de passe (y/n) (a la premiere connexion par exemple)
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setChangeMdp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->change_mdp !== $v || $v === 'n') {
			$this->change_mdp = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::CHANGE_MDP;
		}

		return $this;
	} // setChangeMdp()

	/**
	 * Sets the value of [date_verrouillage] column to a normalized version of the date/time value specified.
	 * Date de verrouillage de l'utilisateur
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
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
				$this->modifiedColumns[] = UtilisateurProfessionnelPeer::DATE_VERROUILLAGE;
			}
		} // if either are not null

		return $this;
	} // setDateVerrouillage()

	/**
	 * Set the value of [password_ticket] column.
	 * password_ticket de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setPasswordTicket($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->password_ticket !== $v) {
			$this->password_ticket = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::PASSWORD_TICKET;
		}

		return $this;
	} // setPasswordTicket()

	/**
	 * Sets the value of [ticket_expiration] column to a normalized version of the date/time value specified.
	 * ticket_expiration de l'utilisateur
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
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
				$this->modifiedColumns[] = UtilisateurProfessionnelPeer::TICKET_EXPIRATION;
			}
		} // if either are not null

		return $this;
	} // setTicketExpiration()

	/**
	 * Set the value of [niveau_alerte] column.
	 * niveau_alerte de l'utilisateur
	 * @param      int $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setNiveauAlerte($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->niveau_alerte !== $v || $v === 0) {
			$this->niveau_alerte = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::NIVEAU_ALERTE;
		}

		return $this;
	} // setNiveauAlerte()

	/**
	 * Set the value of [observation_securite] column.
	 * observation_securite de l'utilisateur
	 * @param      int $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setObservationSecurite($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->observation_securite !== $v || $v === 0) {
			$this->observation_securite = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::OBSERVATION_SECURITE;
		}

		return $this;
	} // setObservationSecurite()

	/**
	 * Set the value of [temp_dir] column.
	 * Repertoire temporaire de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setTempDir($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->temp_dir !== $v) {
			$this->temp_dir = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::TEMP_DIR;
		}

		return $this;
	} // setTempDir()

	/**
	 * Set the value of [numind] column.
	 * numind de l'utilisateur
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setNumind($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->numind !== $v) {
			$this->numind = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::NUMIND;
		}

		return $this;
	} // setNumind()

	/**
	 * Set the value of [auth_mode] column.
	 * auth_mode de l'utilisateur (gepi/cas/ldap)
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setAuthMode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->auth_mode !== $v || $v === 'gepi') {
			$this->auth_mode = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::AUTH_MODE;
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
			if (array_diff($this->modifiedColumns, array(UtilisateurProfessionnelPeer::SHOW_EMAIL,UtilisateurProfessionnelPeer::CHANGE_MDP,UtilisateurProfessionnelPeer::DATE_VERROUILLAGE,UtilisateurProfessionnelPeer::NIVEAU_ALERTE,UtilisateurProfessionnelPeer::OBSERVATION_SECURITE,UtilisateurProfessionnelPeer::AUTH_MODE))) {
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
			return $startcol + 18; // 18 = UtilisateurProfessionnelPeer::NUM_COLUMNS - UtilisateurProfessionnelPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating UtilisateurProfessionnel object", $e);
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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = UtilisateurProfessionnelPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collJGroupesProfesseurss = null;
			$this->lastJGroupesProfesseursCriteria = null;

			$this->collCahierTexteCompteRendus = null;
			$this->lastCahierTexteCompteRenduCriteria = null;

			$this->collCahierTexteTravailAFaires = null;
			$this->lastCahierTexteTravailAFaireCriteria = null;

			$this->collCahierTexteNoticePrivees = null;
			$this->lastCahierTexteNoticePriveeCriteria = null;

			$this->collJEleveCpes = null;
			$this->lastJEleveCpeCriteria = null;

			$this->collJEleveProfesseurPrincipals = null;
			$this->lastJEleveProfesseurPrincipalCriteria = null;

			$this->collJAidUtilisateursProfessionnelss = null;
			$this->lastJAidUtilisateursProfessionnelsCriteria = null;

			$this->collAbsenceEleveSaisies = null;
			$this->lastAbsenceEleveSaisieCriteria = null;

			$this->collAbsenceEleveTraitements = null;
			$this->lastAbsenceEleveTraitementCriteria = null;

			$this->collAbsenceEleveEnvois = null;
			$this->lastAbsenceEleveEnvoiCriteria = null;

			$this->collPreferenceUtilisateurProfessionnels = null;
			$this->lastPreferenceUtilisateurProfessionnelCriteria = null;

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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			UtilisateurProfessionnelPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			UtilisateurProfessionnelPeer::addInstanceToPool($this);
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
					$pk = UtilisateurProfessionnelPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += UtilisateurProfessionnelPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJGroupesProfesseurss !== null) {
				foreach ($this->collJGroupesProfesseurss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCahierTexteCompteRendus !== null) {
				foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCahierTexteTravailAFaires !== null) {
				foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCahierTexteNoticePrivees !== null) {
				foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
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

			if ($this->collJEleveProfesseurPrincipals !== null) {
				foreach ($this->collJEleveProfesseurPrincipals as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJAidUtilisateursProfessionnelss !== null) {
				foreach ($this->collJAidUtilisateursProfessionnelss as $referrerFK) {
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

			if ($this->collAbsenceEleveTraitements !== null) {
				foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveEnvois !== null) {
				foreach ($this->collAbsenceEleveEnvois as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPreferenceUtilisateurProfessionnels !== null) {
				foreach ($this->collPreferenceUtilisateurProfessionnels as $referrerFK) {
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


			if (($retval = UtilisateurProfessionnelPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJGroupesProfesseurss !== null) {
					foreach ($this->collJGroupesProfesseurss as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCahierTexteCompteRendus !== null) {
					foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCahierTexteTravailAFaires !== null) {
					foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCahierTexteNoticePrivees !== null) {
					foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
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

				if ($this->collJEleveProfesseurPrincipals !== null) {
					foreach ($this->collJEleveProfesseurPrincipals as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJAidUtilisateursProfessionnelss !== null) {
					foreach ($this->collJAidUtilisateursProfessionnelss as $referrerFK) {
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

				if ($this->collAbsenceEleveTraitements !== null) {
					foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveEnvois !== null) {
					foreach ($this->collAbsenceEleveEnvois as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPreferenceUtilisateurProfessionnels !== null) {
					foreach ($this->collPreferenceUtilisateurProfessionnels as $referrerFK) {
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
		$pos = UtilisateurProfessionnelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = UtilisateurProfessionnelPeer::getFieldNames($keyType);
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
		$pos = UtilisateurProfessionnelPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = UtilisateurProfessionnelPeer::getFieldNames($keyType);

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
		$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);

		if ($this->isColumnModified(UtilisateurProfessionnelPeer::LOGIN)) $criteria->add(UtilisateurProfessionnelPeer::LOGIN, $this->login);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NOM)) $criteria->add(UtilisateurProfessionnelPeer::NOM, $this->nom);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PRENOM)) $criteria->add(UtilisateurProfessionnelPeer::PRENOM, $this->prenom);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::CIVILITE)) $criteria->add(UtilisateurProfessionnelPeer::CIVILITE, $this->civilite);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PASSWORD)) $criteria->add(UtilisateurProfessionnelPeer::PASSWORD, $this->password);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::EMAIL)) $criteria->add(UtilisateurProfessionnelPeer::EMAIL, $this->email);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::SHOW_EMAIL)) $criteria->add(UtilisateurProfessionnelPeer::SHOW_EMAIL, $this->show_email);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::STATUT)) $criteria->add(UtilisateurProfessionnelPeer::STATUT, $this->statut);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::ETAT)) $criteria->add(UtilisateurProfessionnelPeer::ETAT, $this->etat);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::CHANGE_MDP)) $criteria->add(UtilisateurProfessionnelPeer::CHANGE_MDP, $this->change_mdp);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE)) $criteria->add(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE, $this->date_verrouillage);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PASSWORD_TICKET)) $criteria->add(UtilisateurProfessionnelPeer::PASSWORD_TICKET, $this->password_ticket);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::TICKET_EXPIRATION)) $criteria->add(UtilisateurProfessionnelPeer::TICKET_EXPIRATION, $this->ticket_expiration);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NIVEAU_ALERTE)) $criteria->add(UtilisateurProfessionnelPeer::NIVEAU_ALERTE, $this->niveau_alerte);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE)) $criteria->add(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE, $this->observation_securite);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::TEMP_DIR)) $criteria->add(UtilisateurProfessionnelPeer::TEMP_DIR, $this->temp_dir);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NUMIND)) $criteria->add(UtilisateurProfessionnelPeer::NUMIND, $this->numind);
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::AUTH_MODE)) $criteria->add(UtilisateurProfessionnelPeer::AUTH_MODE, $this->auth_mode);

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
		$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);

		$criteria->add(UtilisateurProfessionnelPeer::LOGIN, $this->login);

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
	 * @param      object $copyObj An object of UtilisateurProfessionnel (or compatible) type.
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

			foreach ($this->getJGroupesProfesseurss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesProfesseurs($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCahierTexteCompteRendus() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteCompteRendu($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCahierTexteTravailAFaires() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteTravailAFaire($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCahierTexteNoticePrivees() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCahierTexteNoticePrivee($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveCpes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveCpe($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveProfesseurPrincipals() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveProfesseurPrincipal($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJAidUtilisateursProfessionnelss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJAidUtilisateursProfessionnels($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisie($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveTraitements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveTraitement($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveEnvois() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveEnvoi($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getPreferenceUtilisateurProfessionnels() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addPreferenceUtilisateurProfessionnel($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getEdtEmplacementCourss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addEdtEmplacementCours($relObj->copy($deepCopy));
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
	 * @return     UtilisateurProfessionnel Clone of current object.
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
	 * @return     UtilisateurProfessionnelPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new UtilisateurProfessionnelPeer();
		}
		return self::$peer;
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
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related JGroupesProfesseurss from storage. If this UtilisateurProfessionnel is new, it will return
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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
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
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JGroupesProfesseurss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJGroupesProfesseurssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
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
	 * Clears out the collCahierTexteCompteRendus collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteCompteRendus()
	 */
	public function clearCahierTexteCompteRendus()
	{
		$this->collCahierTexteCompteRendus = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteCompteRendus collection (array).
	 *
	 * By default this just sets the collCahierTexteCompteRendus collection to an empty array (like clearcollCahierTexteCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteCompteRendus()
	{
		$this->collCahierTexteCompteRendus = array();
	}

	/**
	 * Gets an array of CahierTexteCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related CahierTexteCompteRendus from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteCompteRendu[]
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				CahierTexteCompteRenduPeer::addSelectColumns($criteria);
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				CahierTexteCompteRenduPeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
					$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;
		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Returns the number of related CahierTexteCompteRendu objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteCompteRendu objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteCompteRendus(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				$count = CahierTexteCompteRenduPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
					$count = CahierTexteCompteRenduPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteCompteRendus);
				}
			} else {
				$count = count($this->collCahierTexteCompteRendus);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteCompteRendu object to this object
	 * through the CahierTexteCompteRendu foreign key attribute.
	 *
	 * @param      CahierTexteCompteRendu $l CahierTexteCompteRendu
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteCompteRendu(CahierTexteCompteRendu $l)
	{
		if ($this->collCahierTexteCompteRendus === null) {
			$this->initCahierTexteCompteRendus();
		}
		if (!in_array($l, $this->collCahierTexteCompteRendus, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteCompteRendus, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteCompteRendusJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;

		return $this->collCahierTexteCompteRendus;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteCompteRendus from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteCompteRendusJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteCompteRendus === null) {
			if ($this->isNew()) {
				$this->collCahierTexteCompteRendus = array();
			} else {

				$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteCompteRenduCriteria) || !$this->lastCahierTexteCompteRenduCriteria->equals($criteria)) {
				$this->collCahierTexteCompteRendus = CahierTexteCompteRenduPeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteCompteRenduCriteria = $criteria;

		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Clears out the collCahierTexteTravailAFaires collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteTravailAFaires()
	 */
	public function clearCahierTexteTravailAFaires()
	{
		$this->collCahierTexteTravailAFaires = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteTravailAFaires collection (array).
	 *
	 * By default this just sets the collCahierTexteTravailAFaires collection to an empty array (like clearcollCahierTexteTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteTravailAFaires()
	{
		$this->collCahierTexteTravailAFaires = array();
	}

	/**
	 * Gets an array of CahierTexteTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related CahierTexteTravailAFaires from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteTravailAFaire[]
	 * @throws     PropelException
	 */
	public function getCahierTexteTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				CahierTexteTravailAFairePeer::addSelectColumns($criteria);
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				CahierTexteTravailAFairePeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
					$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;
		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Returns the number of related CahierTexteTravailAFaire objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteTravailAFaire objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteTravailAFaires(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				$count = CahierTexteTravailAFairePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
					$count = CahierTexteTravailAFairePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteTravailAFaires);
				}
			} else {
				$count = count($this->collCahierTexteTravailAFaires);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteTravailAFaire object to this object
	 * through the CahierTexteTravailAFaire foreign key attribute.
	 *
	 * @param      CahierTexteTravailAFaire $l CahierTexteTravailAFaire
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteTravailAFaire(CahierTexteTravailAFaire $l)
	{
		if ($this->collCahierTexteTravailAFaires === null) {
			$this->initCahierTexteTravailAFaires();
		}
		if (!in_array($l, $this->collCahierTexteTravailAFaires, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteTravailAFaires, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteTravailAFairesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;

		return $this->collCahierTexteTravailAFaires;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteTravailAFaires from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteTravailAFairesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteTravailAFaires === null) {
			if ($this->isNew()) {
				$this->collCahierTexteTravailAFaires = array();
			} else {

				$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteTravailAFairePeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteTravailAFaireCriteria) || !$this->lastCahierTexteTravailAFaireCriteria->equals($criteria)) {
				$this->collCahierTexteTravailAFaires = CahierTexteTravailAFairePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteTravailAFaireCriteria = $criteria;

		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Clears out the collCahierTexteNoticePrivees collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCahierTexteNoticePrivees()
	 */
	public function clearCahierTexteNoticePrivees()
	{
		$this->collCahierTexteNoticePrivees = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCahierTexteNoticePrivees collection (array).
	 *
	 * By default this just sets the collCahierTexteNoticePrivees collection to an empty array (like clearcollCahierTexteNoticePrivees());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCahierTexteNoticePrivees()
	{
		$this->collCahierTexteNoticePrivees = array();
	}

	/**
	 * Gets an array of CahierTexteNoticePrivee objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related CahierTexteNoticePrivees from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array CahierTexteNoticePrivee[]
	 * @throws     PropelException
	 */
	public function getCahierTexteNoticePrivees($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
			   $this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				CahierTexteNoticePriveePeer::addSelectColumns($criteria);
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				CahierTexteNoticePriveePeer::addSelectColumns($criteria);
				if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
					$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;
		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Returns the number of related CahierTexteNoticePrivee objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CahierTexteNoticePrivee objects.
	 * @throws     PropelException
	 */
	public function countCahierTexteNoticePrivees(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				$count = CahierTexteNoticePriveePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
					$count = CahierTexteNoticePriveePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collCahierTexteNoticePrivees);
				}
			} else {
				$count = count($this->collCahierTexteNoticePrivees);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a CahierTexteNoticePrivee object to this object
	 * through the CahierTexteNoticePrivee foreign key attribute.
	 *
	 * @param      CahierTexteNoticePrivee $l CahierTexteNoticePrivee
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteNoticePrivee(CahierTexteNoticePrivee $l)
	{
		if ($this->collCahierTexteNoticePrivees === null) {
			$this->initCahierTexteNoticePrivees();
		}
		if (!in_array($l, $this->collCahierTexteNoticePrivees, true)) { // only add it if the **same** object is not already associated
			array_push($this->collCahierTexteNoticePrivees, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteNoticePriveesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;

		return $this->collCahierTexteNoticePrivees;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related CahierTexteNoticePrivees from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getCahierTexteNoticePriveesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collCahierTexteNoticePrivees === null) {
			if ($this->isNew()) {
				$this->collCahierTexteNoticePrivees = array();
			} else {

				$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(CahierTexteNoticePriveePeer::ID_LOGIN, $this->login);

			if (!isset($this->lastCahierTexteNoticePriveeCriteria) || !$this->lastCahierTexteNoticePriveeCriteria->equals($criteria)) {
				$this->collCahierTexteNoticePrivees = CahierTexteNoticePriveePeer::doSelectJoinCahierTexteSequence($criteria, $con, $join_behavior);
			}
		}
		$this->lastCahierTexteNoticePriveeCriteria = $criteria;

		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Clears out the collJEleveCpes collection (array).
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
	 * Initializes the collJEleveCpes collection (array).
	 *
	 * By default this just sets the collJEleveCpes collection to an empty array (like clearcollJEleveCpes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveCpes()
	{
		$this->collJEleveCpes = array();
	}

	/**
	 * Gets an array of JEleveCpe objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related JEleveCpes from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JEleveCpe[]
	 * @throws     PropelException
	 */
	public function getJEleveCpes($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveCpes === null) {
			if ($this->isNew()) {
			   $this->collJEleveCpes = array();
			} else {

				$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

				JEleveCpePeer::addSelectColumns($criteria);
				$this->collJEleveCpes = JEleveCpePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

				JEleveCpePeer::addSelectColumns($criteria);
				if (!isset($this->lastJEleveCpeCriteria) || !$this->lastJEleveCpeCriteria->equals($criteria)) {
					$this->collJEleveCpes = JEleveCpePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJEleveCpeCriteria = $criteria;
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
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJEleveCpes === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

				$count = JEleveCpePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

				if (!isset($this->lastJEleveCpeCriteria) || !$this->lastJEleveCpeCriteria->equals($criteria)) {
					$count = JEleveCpePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJEleveCpes);
				}
			} else {
				$count = count($this->collJEleveCpes);
			}
		}
		$this->lastJEleveCpeCriteria = $criteria;
		return $count;
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
		if (!in_array($l, $this->collJEleveCpes, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJEleveCpes, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JEleveCpes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJEleveCpesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveCpes === null) {
			if ($this->isNew()) {
				$this->collJEleveCpes = array();
			} else {

				$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

				$this->collJEleveCpes = JEleveCpePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveCpePeer::CPE_LOGIN, $this->login);

			if (!isset($this->lastJEleveCpeCriteria) || !$this->lastJEleveCpeCriteria->equals($criteria)) {
				$this->collJEleveCpes = JEleveCpePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveCpeCriteria = $criteria;

		return $this->collJEleveCpes;
	}

	/**
	 * Clears out the collJEleveProfesseurPrincipals collection (array).
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
	 * Initializes the collJEleveProfesseurPrincipals collection (array).
	 *
	 * By default this just sets the collJEleveProfesseurPrincipals collection to an empty array (like clearcollJEleveProfesseurPrincipals());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveProfesseurPrincipals()
	{
		$this->collJEleveProfesseurPrincipals = array();
	}

	/**
	 * Gets an array of JEleveProfesseurPrincipal objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related JEleveProfesseurPrincipals from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JEleveProfesseurPrincipal[]
	 * @throws     PropelException
	 */
	public function getJEleveProfesseurPrincipals($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
			   $this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
				if (!isset($this->lastJEleveProfesseurPrincipalCriteria) || !$this->lastJEleveProfesseurPrincipalCriteria->equals($criteria)) {
					$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJEleveProfesseurPrincipalCriteria = $criteria;
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
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				$count = JEleveProfesseurPrincipalPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				if (!isset($this->lastJEleveProfesseurPrincipalCriteria) || !$this->lastJEleveProfesseurPrincipalCriteria->equals($criteria)) {
					$count = JEleveProfesseurPrincipalPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJEleveProfesseurPrincipals);
				}
			} else {
				$count = count($this->collJEleveProfesseurPrincipals);
			}
		}
		$this->lastJEleveProfesseurPrincipalCriteria = $criteria;
		return $count;
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
		if (!in_array($l, $this->collJEleveProfesseurPrincipals, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJEleveProfesseurPrincipals, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJEleveProfesseurPrincipalsJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
				$this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

			if (!isset($this->lastJEleveProfesseurPrincipalCriteria) || !$this->lastJEleveProfesseurPrincipalCriteria->equals($criteria)) {
				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveProfesseurPrincipalCriteria = $criteria;

		return $this->collJEleveProfesseurPrincipals;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJEleveProfesseurPrincipalsJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
				$this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinClasse($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR, $this->login);

			if (!isset($this->lastJEleveProfesseurPrincipalCriteria) || !$this->lastJEleveProfesseurPrincipalCriteria->equals($criteria)) {
				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinClasse($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveProfesseurPrincipalCriteria = $criteria;

		return $this->collJEleveProfesseurPrincipals;
	}

	/**
	 * Clears out the collJAidUtilisateursProfessionnelss collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJAidUtilisateursProfessionnelss()
	 */
	public function clearJAidUtilisateursProfessionnelss()
	{
		$this->collJAidUtilisateursProfessionnelss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJAidUtilisateursProfessionnelss collection (array).
	 *
	 * By default this just sets the collJAidUtilisateursProfessionnelss collection to an empty array (like clearcollJAidUtilisateursProfessionnelss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJAidUtilisateursProfessionnelss()
	{
		$this->collJAidUtilisateursProfessionnelss = array();
	}

	/**
	 * Gets an array of JAidUtilisateursProfessionnels objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related JAidUtilisateursProfessionnelss from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JAidUtilisateursProfessionnels[]
	 * @throws     PropelException
	 */
	public function getJAidUtilisateursProfessionnelss($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
			   $this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				JAidUtilisateursProfessionnelsPeer::addSelectColumns($criteria);
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				JAidUtilisateursProfessionnelsPeer::addSelectColumns($criteria);
				if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
					$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;
		return $this->collJAidUtilisateursProfessionnelss;
	}

	/**
	 * Returns the number of related JAidUtilisateursProfessionnels objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JAidUtilisateursProfessionnels objects.
	 * @throws     PropelException
	 */
	public function countJAidUtilisateursProfessionnelss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				$count = JAidUtilisateursProfessionnelsPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
					$count = JAidUtilisateursProfessionnelsPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJAidUtilisateursProfessionnelss);
				}
			} else {
				$count = count($this->collJAidUtilisateursProfessionnelss);
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JAidUtilisateursProfessionnels object to this object
	 * through the JAidUtilisateursProfessionnels foreign key attribute.
	 *
	 * @param      JAidUtilisateursProfessionnels $l JAidUtilisateursProfessionnels
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJAidUtilisateursProfessionnels(JAidUtilisateursProfessionnels $l)
	{
		if ($this->collJAidUtilisateursProfessionnelss === null) {
			$this->initJAidUtilisateursProfessionnelss();
		}
		if (!in_array($l, $this->collJAidUtilisateursProfessionnelss, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJAidUtilisateursProfessionnelss, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JAidUtilisateursProfessionnelss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJAidUtilisateursProfessionnelssJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
				$this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidDetails($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

			if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidDetails($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;

		return $this->collJAidUtilisateursProfessionnelss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JAidUtilisateursProfessionnelss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getJAidUtilisateursProfessionnelssJoinAidConfiguration($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
				$this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_UTILISATEUR, $this->login);

			if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;

		return $this->collJAidUtilisateursProfessionnelss;
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
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related AbsenceEleveSaisies from storage. If this UtilisateurProfessionnel is new, it will return
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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

				AbsenceEleveSaisiePeer::addSelectColumns($criteria);
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
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

				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

				$count = AbsenceEleveSaisiePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

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
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveSaisiesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveSaisiesJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtCreneau($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtCreneau($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;

		return $this->collAbsenceEleveSaisies;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveSaisiesJoinEdtEmplacementCours($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveSaisies === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveSaisies = array();
			} else {

				$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtEmplacementCours($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveSaisieCriteria) || !$this->lastAbsenceEleveSaisieCriteria->equals($criteria)) {
				$this->collAbsenceEleveSaisies = AbsenceEleveSaisiePeer::doSelectJoinEdtEmplacementCours($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveSaisieCriteria = $criteria;

		return $this->collAbsenceEleveSaisies;
	}

	/**
	 * Clears out the collAbsenceEleveTraitements collection (array).
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
	 * Initializes the collAbsenceEleveTraitements collection (array).
	 *
	 * By default this just sets the collAbsenceEleveTraitements collection to an empty array (like clearcollAbsenceEleveTraitements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTraitements()
	{
		$this->collAbsenceEleveTraitements = array();
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related AbsenceEleveTraitements from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array AbsenceEleveTraitement[]
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				AbsenceEleveTraitementPeer::addSelectColumns($criteria);
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				AbsenceEleveTraitementPeer::addSelectColumns($criteria);
				if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
					$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;
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
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				$count = AbsenceEleveTraitementPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
					$count = AbsenceEleveTraitementPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collAbsenceEleveTraitements);
				}
			} else {
				$count = count($this->collAbsenceEleveTraitements);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;
		return $count;
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
		if (!in_array($l, $this->collAbsenceEleveTraitements, true)) { // only add it if the **same** object is not already associated
			array_push($this->collAbsenceEleveTraitements, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveType($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveType($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveType($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveMotif($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveMotif($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveMotif($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveJustification($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveJustification($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveJustification($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveAction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveTraitements === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveTraitements = array();
			} else {

				$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveAction($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveTraitementPeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveTraitementCriteria) || !$this->lastAbsenceEleveTraitementCriteria->equals($criteria)) {
				$this->collAbsenceEleveTraitements = AbsenceEleveTraitementPeer::doSelectJoinAbsenceEleveAction($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveTraitementCriteria = $criteria;

		return $this->collAbsenceEleveTraitements;
	}

	/**
	 * Clears out the collAbsenceEleveEnvois collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveEnvois()
	 */
	public function clearAbsenceEleveEnvois()
	{
		$this->collAbsenceEleveEnvois = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveEnvois collection (array).
	 *
	 * By default this just sets the collAbsenceEleveEnvois collection to an empty array (like clearcollAbsenceEleveEnvois());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initAbsenceEleveEnvois()
	{
		$this->collAbsenceEleveEnvois = array();
	}

	/**
	 * Gets an array of AbsenceEleveEnvoi objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related AbsenceEleveEnvois from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array AbsenceEleveEnvoi[]
	 * @throws     PropelException
	 */
	public function getAbsenceEleveEnvois($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveEnvois === null) {
			if ($this->isNew()) {
			   $this->collAbsenceEleveEnvois = array();
			} else {

				$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

				AbsenceEleveEnvoiPeer::addSelectColumns($criteria);
				$this->collAbsenceEleveEnvois = AbsenceEleveEnvoiPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

				AbsenceEleveEnvoiPeer::addSelectColumns($criteria);
				if (!isset($this->lastAbsenceEleveEnvoiCriteria) || !$this->lastAbsenceEleveEnvoiCriteria->equals($criteria)) {
					$this->collAbsenceEleveEnvois = AbsenceEleveEnvoiPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastAbsenceEleveEnvoiCriteria = $criteria;
		return $this->collAbsenceEleveEnvois;
	}

	/**
	 * Returns the number of related AbsenceEleveEnvoi objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveEnvoi objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveEnvois(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collAbsenceEleveEnvois === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

				$count = AbsenceEleveEnvoiPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

				if (!isset($this->lastAbsenceEleveEnvoiCriteria) || !$this->lastAbsenceEleveEnvoiCriteria->equals($criteria)) {
					$count = AbsenceEleveEnvoiPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collAbsenceEleveEnvois);
				}
			} else {
				$count = count($this->collAbsenceEleveEnvois);
			}
		}
		$this->lastAbsenceEleveEnvoiCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a AbsenceEleveEnvoi object to this object
	 * through the AbsenceEleveEnvoi foreign key attribute.
	 *
	 * @param      AbsenceEleveEnvoi $l AbsenceEleveEnvoi
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveEnvoi(AbsenceEleveEnvoi $l)
	{
		if ($this->collAbsenceEleveEnvois === null) {
			$this->initAbsenceEleveEnvois();
		}
		if (!in_array($l, $this->collAbsenceEleveEnvois, true)) { // only add it if the **same** object is not already associated
			array_push($this->collAbsenceEleveEnvois, $l);
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveEnvois from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getAbsenceEleveEnvoisJoinAbsenceEleveTypeEnvoi($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collAbsenceEleveEnvois === null) {
			if ($this->isNew()) {
				$this->collAbsenceEleveEnvois = array();
			} else {

				$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

				$this->collAbsenceEleveEnvois = AbsenceEleveEnvoiPeer::doSelectJoinAbsenceEleveTypeEnvoi($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(AbsenceEleveEnvoiPeer::UTILISATEUR_ID, $this->login);

			if (!isset($this->lastAbsenceEleveEnvoiCriteria) || !$this->lastAbsenceEleveEnvoiCriteria->equals($criteria)) {
				$this->collAbsenceEleveEnvois = AbsenceEleveEnvoiPeer::doSelectJoinAbsenceEleveTypeEnvoi($criteria, $con, $join_behavior);
			}
		}
		$this->lastAbsenceEleveEnvoiCriteria = $criteria;

		return $this->collAbsenceEleveEnvois;
	}

	/**
	 * Clears out the collPreferenceUtilisateurProfessionnels collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addPreferenceUtilisateurProfessionnels()
	 */
	public function clearPreferenceUtilisateurProfessionnels()
	{
		$this->collPreferenceUtilisateurProfessionnels = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collPreferenceUtilisateurProfessionnels collection (array).
	 *
	 * By default this just sets the collPreferenceUtilisateurProfessionnels collection to an empty array (like clearcollPreferenceUtilisateurProfessionnels());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initPreferenceUtilisateurProfessionnels()
	{
		$this->collPreferenceUtilisateurProfessionnels = array();
	}

	/**
	 * Gets an array of PreferenceUtilisateurProfessionnel objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related PreferenceUtilisateurProfessionnels from storage. If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array PreferenceUtilisateurProfessionnel[]
	 * @throws     PropelException
	 */
	public function getPreferenceUtilisateurProfessionnels($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPreferenceUtilisateurProfessionnels === null) {
			if ($this->isNew()) {
			   $this->collPreferenceUtilisateurProfessionnels = array();
			} else {

				$criteria->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, $this->login);

				PreferenceUtilisateurProfessionnelPeer::addSelectColumns($criteria);
				$this->collPreferenceUtilisateurProfessionnels = PreferenceUtilisateurProfessionnelPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, $this->login);

				PreferenceUtilisateurProfessionnelPeer::addSelectColumns($criteria);
				if (!isset($this->lastPreferenceUtilisateurProfessionnelCriteria) || !$this->lastPreferenceUtilisateurProfessionnelCriteria->equals($criteria)) {
					$this->collPreferenceUtilisateurProfessionnels = PreferenceUtilisateurProfessionnelPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPreferenceUtilisateurProfessionnelCriteria = $criteria;
		return $this->collPreferenceUtilisateurProfessionnels;
	}

	/**
	 * Returns the number of related PreferenceUtilisateurProfessionnel objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related PreferenceUtilisateurProfessionnel objects.
	 * @throws     PropelException
	 */
	public function countPreferenceUtilisateurProfessionnels(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collPreferenceUtilisateurProfessionnels === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, $this->login);

				$count = PreferenceUtilisateurProfessionnelPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(PreferenceUtilisateurProfessionnelPeer::LOGIN, $this->login);

				if (!isset($this->lastPreferenceUtilisateurProfessionnelCriteria) || !$this->lastPreferenceUtilisateurProfessionnelCriteria->equals($criteria)) {
					$count = PreferenceUtilisateurProfessionnelPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collPreferenceUtilisateurProfessionnels);
				}
			} else {
				$count = count($this->collPreferenceUtilisateurProfessionnels);
			}
		}
		$this->lastPreferenceUtilisateurProfessionnelCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a PreferenceUtilisateurProfessionnel object to this object
	 * through the PreferenceUtilisateurProfessionnel foreign key attribute.
	 *
	 * @param      PreferenceUtilisateurProfessionnel $l PreferenceUtilisateurProfessionnel
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPreferenceUtilisateurProfessionnel(PreferenceUtilisateurProfessionnel $l)
	{
		if ($this->collPreferenceUtilisateurProfessionnels === null) {
			$this->initPreferenceUtilisateurProfessionnels();
		}
		if (!in_array($l, $this->collPreferenceUtilisateurProfessionnels, true)) { // only add it if the **same** object is not already associated
			array_push($this->collPreferenceUtilisateurProfessionnels, $l);
			$l->setUtilisateurProfessionnel($this);
		}
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
	 * Otherwise if this UtilisateurProfessionnel has previously been saved, it will retrieve
	 * related EdtEmplacementCourss from storage. If this UtilisateurProfessionnel is new, it will return
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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
			   $this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				EdtEmplacementCoursPeer::addSelectColumns($criteria);
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

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
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
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

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$count = EdtEmplacementCoursPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

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
			$l->setUtilisateurProfessionnel($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getEdtEmplacementCourssJoinGroupeRelatedByIdGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupeRelatedByIdGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupeRelatedByIdGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getEdtEmplacementCourssJoinGroupeRelatedByIdGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupeRelatedByIdGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinGroupeRelatedByIdGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getEdtEmplacementCourssJoinEdtSalle($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtSalle($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getEdtEmplacementCourssJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCreneau($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCreneau($criteria, $con, $join_behavior);
			}
		}
		$this->lastEdtEmplacementCoursCriteria = $criteria;

		return $this->collEdtEmplacementCourss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 */
	public function getEdtEmplacementCourssJoinEdtCalendrierPeriode($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(UtilisateurProfessionnelPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collEdtEmplacementCourss === null) {
			if ($this->isNew()) {
				$this->collEdtEmplacementCourss = array();
			} else {

				$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCalendrierPeriode($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(EdtEmplacementCoursPeer::LOGIN_PROF, $this->login);

			if (!isset($this->lastEdtEmplacementCoursCriteria) || !$this->lastEdtEmplacementCoursCriteria->equals($criteria)) {
				$this->collEdtEmplacementCourss = EdtEmplacementCoursPeer::doSelectJoinEdtCalendrierPeriode($criteria, $con, $join_behavior);
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
			if ($this->collJGroupesProfesseurss) {
				foreach ((array) $this->collJGroupesProfesseurss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteCompteRendus) {
				foreach ((array) $this->collCahierTexteCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteTravailAFaires) {
				foreach ((array) $this->collCahierTexteTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteNoticePrivees) {
				foreach ((array) $this->collCahierTexteNoticePrivees as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveCpes) {
				foreach ((array) $this->collJEleveCpes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveProfesseurPrincipals) {
				foreach ((array) $this->collJEleveProfesseurPrincipals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidUtilisateursProfessionnelss) {
				foreach ((array) $this->collJAidUtilisateursProfessionnelss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ((array) $this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ((array) $this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveEnvois) {
				foreach ((array) $this->collAbsenceEleveEnvois as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collPreferenceUtilisateurProfessionnels) {
				foreach ((array) $this->collPreferenceUtilisateurProfessionnels as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEdtEmplacementCourss) {
				foreach ((array) $this->collEdtEmplacementCourss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJGroupesProfesseurss = null;
		$this->collCahierTexteCompteRendus = null;
		$this->collCahierTexteTravailAFaires = null;
		$this->collCahierTexteNoticePrivees = null;
		$this->collJEleveCpes = null;
		$this->collJEleveProfesseurPrincipals = null;
		$this->collJAidUtilisateursProfessionnelss = null;
		$this->collAbsenceEleveSaisies = null;
		$this->collAbsenceEleveTraitements = null;
		$this->collAbsenceEleveEnvois = null;
		$this->collPreferenceUtilisateurProfessionnels = null;
		$this->collEdtEmplacementCourss = null;
	}

} // BaseUtilisateurProfessionnel
