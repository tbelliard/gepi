<?php


/**
 * Base class that represents a row from the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseUtilisateurProfessionnel extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'UtilisateurProfessionnelPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        UtilisateurProfessionnelPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

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
	 * The value for the salt field.
	 * @var        string
	 */
	protected $salt;

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
	 * Note: this column has a database default value of: '2006-01-01'
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
	 * @var        array JScolClasses[] Collection to store aggregation of JScolClasses objects.
	 */
	protected $collJScolClassess;

	/**
	 * @var        array CahierTexteCompteRendu[] Collection to store aggregation of CahierTexteCompteRendu objects.
	 */
	protected $collCahierTexteCompteRendus;

	/**
	 * @var        array CahierTexteTravailAFaire[] Collection to store aggregation of CahierTexteTravailAFaire objects.
	 */
	protected $collCahierTexteTravailAFaires;

	/**
	 * @var        array CahierTexteNoticePrivee[] Collection to store aggregation of CahierTexteNoticePrivee objects.
	 */
	protected $collCahierTexteNoticePrivees;

	/**
	 * @var        array JEleveCpe[] Collection to store aggregation of JEleveCpe objects.
	 */
	protected $collJEleveCpes;

	/**
	 * @var        array JEleveProfesseurPrincipal[] Collection to store aggregation of JEleveProfesseurPrincipal objects.
	 */
	protected $collJEleveProfesseurPrincipals;

	/**
	 * @var        array JAidUtilisateursProfessionnels[] Collection to store aggregation of JAidUtilisateursProfessionnels objects.
	 */
	protected $collJAidUtilisateursProfessionnelss;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collAbsenceEleveTraitements;

	/**
	 * @var        array AbsenceEleveTraitement[] Collection to store aggregation of AbsenceEleveTraitement objects.
	 */
	protected $collModifiedAbsenceEleveTraitements;

	/**
	 * @var        array AbsenceEleveNotification[] Collection to store aggregation of AbsenceEleveNotification objects.
	 */
	protected $collAbsenceEleveNotifications;

	/**
	 * @var        array JProfesseursMatieres[] Collection to store aggregation of JProfesseursMatieres objects.
	 */
	protected $collJProfesseursMatieress;

	/**
	 * @var        array PreferenceUtilisateurProfessionnel[] Collection to store aggregation of PreferenceUtilisateurProfessionnel objects.
	 */
	protected $collPreferenceUtilisateurProfessionnels;

	/**
	 * @var        array EdtEmplacementCours[] Collection to store aggregation of EdtEmplacementCours objects.
	 */
	protected $collEdtEmplacementCourss;

	/**
	 * @var        array Groupe[] Collection to store aggregation of Groupe objects.
	 */
	protected $collGroupes;

	/**
	 * @var        array AidDetails[] Collection to store aggregation of AidDetails objects.
	 */
	protected $collAidDetailss;

	/**
	 * @var        array Matiere[] Collection to store aggregation of Matiere objects.
	 */
	protected $collMatieres;

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
	protected $groupesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $aidDetailssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $matieresScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jGroupesProfesseurssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jScolClassessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteCompteRendusScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteTravailAFairesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $cahierTexteNoticePriveesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveCpesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jEleveProfesseurPrincipalsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jAidUtilisateursProfessionnelssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveSaisiesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveTraitementsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $modifiedAbsenceEleveTraitementsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveNotificationsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jProfesseursMatieressScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $preferenceUtilisateurProfessionnelsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $edtEmplacementCourssScheduledForDeletion = null;

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
		$this->date_verrouillage = '2006-01-01';
		$this->niveau_alerte = 0;
		$this->observation_securite = 0;
		$this->auth_mode = 'gepi';
	}

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
	 * Get the [salt] column value.
	 * sel pour le hmac du mot de passe
	 * @return     string
	 */
	public function getSalt()
	{
		return $this->salt;
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
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateVerrouillage($format = '%x')
	{
		if ($this->date_verrouillage === null) {
			return null;
		}


		if ($this->date_verrouillage === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_verrouillage);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_verrouillage, true), $x);
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
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getTicketExpiration($format = '%x')
	{
		if ($this->ticket_expiration === null) {
			return null;
		}


		if ($this->ticket_expiration === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->ticket_expiration);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ticket_expiration, true), $x);
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
	 * Set the value of [salt] column.
	 * sel pour le hmac du mot de passe
	 * @param      string $v new value
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setSalt($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->salt !== $v) {
			$this->salt = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::SALT;
		}

		return $this;
	} // setSalt()

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

		if ($this->show_email !== $v) {
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

		if ($this->change_mdp !== $v) {
			$this->change_mdp = $v;
			$this->modifiedColumns[] = UtilisateurProfessionnelPeer::CHANGE_MDP;
		}

		return $this;
	} // setChangeMdp()

	/**
	 * Sets the value of [date_verrouillage] column to a normalized version of the date/time value specified.
	 * Date de verrouillage de l'utilisateur
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setDateVerrouillage($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->date_verrouillage !== null || $dt !== null) {
			$currentDateAsString = ($this->date_verrouillage !== null && $tmpDt = new DateTime($this->date_verrouillage)) ? $tmpDt->format('Y-m-d') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d') : null;
			if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match
				|| ($dt->format('Y-m-d') === '2006-01-01') // or the entered value matches the default
				 ) {
				$this->date_verrouillage = $newDateAsString;
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
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function setTicketExpiration($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->ticket_expiration !== null || $dt !== null) {
			$currentDateAsString = ($this->ticket_expiration !== null && $tmpDt = new DateTime($this->ticket_expiration)) ? $tmpDt->format('Y-m-d') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->ticket_expiration = $newDateAsString;
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

		if ($this->niveau_alerte !== $v) {
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

		if ($this->observation_securite !== $v) {
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

		if ($this->auth_mode !== $v) {
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
			if ($this->show_email !== 'no') {
				return false;
			}

			if ($this->change_mdp !== 'n') {
				return false;
			}

			if ($this->date_verrouillage !== '2006-01-01') {
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
			$this->salt = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->email = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->show_email = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->statut = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->etat = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->change_mdp = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->date_verrouillage = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->password_ticket = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->ticket_expiration = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->niveau_alerte = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->observation_securite = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->temp_dir = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->numind = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->auth_mode = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 19; // 19 = UtilisateurProfessionnelPeer::NUM_HYDRATE_COLUMNS.

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

			$this->collJScolClassess = null;

			$this->collCahierTexteCompteRendus = null;

			$this->collCahierTexteTravailAFaires = null;

			$this->collCahierTexteNoticePrivees = null;

			$this->collJEleveCpes = null;

			$this->collJEleveProfesseurPrincipals = null;

			$this->collJAidUtilisateursProfessionnelss = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collAbsenceEleveTraitements = null;

			$this->collModifiedAbsenceEleveTraitements = null;

			$this->collAbsenceEleveNotifications = null;

			$this->collJProfesseursMatieress = null;

			$this->collPreferenceUtilisateurProfessionnels = null;

			$this->collEdtEmplacementCourss = null;

			$this->collGroupes = null;
			$this->collAidDetailss = null;
			$this->collMatieres = null;
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
			$deleteQuery = UtilisateurProfessionnelQuery::create()
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
			$con = Propel::getConnection(UtilisateurProfessionnelPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				UtilisateurProfessionnelPeer::addInstanceToPool($this);
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

			if ($this->groupesScheduledForDeletion !== null) {
				if (!$this->groupesScheduledForDeletion->isEmpty()) {
					JGroupesProfesseursQuery::create()
						->filterByPrimaryKeys($this->groupesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->groupesScheduledForDeletion = null;
				}

				foreach ($this->getGroupes() as $groupe) {
					if ($groupe->isModified()) {
						$groupe->save($con);
					}
				}
			}

			if ($this->aidDetailssScheduledForDeletion !== null) {
				if (!$this->aidDetailssScheduledForDeletion->isEmpty()) {
					JAidUtilisateursProfessionnelsQuery::create()
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

			if ($this->matieresScheduledForDeletion !== null) {
				if (!$this->matieresScheduledForDeletion->isEmpty()) {
					JProfesseursMatieresQuery::create()
						->filterByPrimaryKeys($this->matieresScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->matieresScheduledForDeletion = null;
				}

				foreach ($this->getMatieres() as $matiere) {
					if ($matiere->isModified()) {
						$matiere->save($con);
					}
				}
			}

			if ($this->jGroupesProfesseurssScheduledForDeletion !== null) {
				if (!$this->jGroupesProfesseurssScheduledForDeletion->isEmpty()) {
					JGroupesProfesseursQuery::create()
						->filterByPrimaryKeys($this->jGroupesProfesseurssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jGroupesProfesseurssScheduledForDeletion = null;
				}
			}

			if ($this->collJGroupesProfesseurss !== null) {
				foreach ($this->collJGroupesProfesseurss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jScolClassessScheduledForDeletion !== null) {
				if (!$this->jScolClassessScheduledForDeletion->isEmpty()) {
					JScolClassesQuery::create()
						->filterByPrimaryKeys($this->jScolClassessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jScolClassessScheduledForDeletion = null;
				}
			}

			if ($this->collJScolClassess !== null) {
				foreach ($this->collJScolClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->cahierTexteCompteRendusScheduledForDeletion !== null) {
				if (!$this->cahierTexteCompteRendusScheduledForDeletion->isEmpty()) {
					CahierTexteCompteRenduQuery::create()
						->filterByPrimaryKeys($this->cahierTexteCompteRendusScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteCompteRendusScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteCompteRendus !== null) {
				foreach ($this->collCahierTexteCompteRendus as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->cahierTexteTravailAFairesScheduledForDeletion !== null) {
				if (!$this->cahierTexteTravailAFairesScheduledForDeletion->isEmpty()) {
					CahierTexteTravailAFaireQuery::create()
						->filterByPrimaryKeys($this->cahierTexteTravailAFairesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteTravailAFairesScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteTravailAFaires !== null) {
				foreach ($this->collCahierTexteTravailAFaires as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->cahierTexteNoticePriveesScheduledForDeletion !== null) {
				if (!$this->cahierTexteNoticePriveesScheduledForDeletion->isEmpty()) {
					CahierTexteNoticePriveeQuery::create()
						->filterByPrimaryKeys($this->cahierTexteNoticePriveesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->cahierTexteNoticePriveesScheduledForDeletion = null;
				}
			}

			if ($this->collCahierTexteNoticePrivees !== null) {
				foreach ($this->collCahierTexteNoticePrivees as $referrerFK) {
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

			if ($this->jAidUtilisateursProfessionnelssScheduledForDeletion !== null) {
				if (!$this->jAidUtilisateursProfessionnelssScheduledForDeletion->isEmpty()) {
					JAidUtilisateursProfessionnelsQuery::create()
						->filterByPrimaryKeys($this->jAidUtilisateursProfessionnelssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jAidUtilisateursProfessionnelssScheduledForDeletion = null;
				}
			}

			if ($this->collJAidUtilisateursProfessionnelss !== null) {
				foreach ($this->collJAidUtilisateursProfessionnelss as $referrerFK) {
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

			if ($this->absenceEleveTraitementsScheduledForDeletion !== null) {
				if (!$this->absenceEleveTraitementsScheduledForDeletion->isEmpty()) {
					AbsenceEleveTraitementQuery::create()
						->filterByPrimaryKeys($this->absenceEleveTraitementsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveTraitementsScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveTraitements !== null) {
				foreach ($this->collAbsenceEleveTraitements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->modifiedAbsenceEleveTraitementsScheduledForDeletion !== null) {
				if (!$this->modifiedAbsenceEleveTraitementsScheduledForDeletion->isEmpty()) {
					AbsenceEleveTraitementQuery::create()
						->filterByPrimaryKeys($this->modifiedAbsenceEleveTraitementsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->modifiedAbsenceEleveTraitementsScheduledForDeletion = null;
				}
			}

			if ($this->collModifiedAbsenceEleveTraitements !== null) {
				foreach ($this->collModifiedAbsenceEleveTraitements as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->absenceEleveNotificationsScheduledForDeletion !== null) {
				if (!$this->absenceEleveNotificationsScheduledForDeletion->isEmpty()) {
					AbsenceEleveNotificationQuery::create()
						->filterByPrimaryKeys($this->absenceEleveNotificationsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->absenceEleveNotificationsScheduledForDeletion = null;
				}
			}

			if ($this->collAbsenceEleveNotifications !== null) {
				foreach ($this->collAbsenceEleveNotifications as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->jProfesseursMatieressScheduledForDeletion !== null) {
				if (!$this->jProfesseursMatieressScheduledForDeletion->isEmpty()) {
					JProfesseursMatieresQuery::create()
						->filterByPrimaryKeys($this->jProfesseursMatieressScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jProfesseursMatieressScheduledForDeletion = null;
				}
			}

			if ($this->collJProfesseursMatieress !== null) {
				foreach ($this->collJProfesseursMatieress as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->preferenceUtilisateurProfessionnelsScheduledForDeletion !== null) {
				if (!$this->preferenceUtilisateurProfessionnelsScheduledForDeletion->isEmpty()) {
					PreferenceUtilisateurProfessionnelQuery::create()
						->filterByPrimaryKeys($this->preferenceUtilisateurProfessionnelsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->preferenceUtilisateurProfessionnelsScheduledForDeletion = null;
				}
			}

			if ($this->collPreferenceUtilisateurProfessionnels !== null) {
				foreach ($this->collPreferenceUtilisateurProfessionnels as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->edtEmplacementCourssScheduledForDeletion !== null) {
				if (!$this->edtEmplacementCourssScheduledForDeletion->isEmpty()) {
					EdtEmplacementCoursQuery::create()
						->filterByPrimaryKeys($this->edtEmplacementCourssScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->edtEmplacementCourssScheduledForDeletion = null;
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


		 // check the columns in natural order for more readable SQL queries
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::LOGIN)) {
			$modifiedColumns[':p' . $index++]  = 'LOGIN';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NOM)) {
			$modifiedColumns[':p' . $index++]  = 'NOM';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PRENOM)) {
			$modifiedColumns[':p' . $index++]  = 'PRENOM';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::CIVILITE)) {
			$modifiedColumns[':p' . $index++]  = 'CIVILITE';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PASSWORD)) {
			$modifiedColumns[':p' . $index++]  = 'PASSWORD';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::SALT)) {
			$modifiedColumns[':p' . $index++]  = 'SALT';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::EMAIL)) {
			$modifiedColumns[':p' . $index++]  = 'EMAIL';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::SHOW_EMAIL)) {
			$modifiedColumns[':p' . $index++]  = 'SHOW_EMAIL';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::STATUT)) {
			$modifiedColumns[':p' . $index++]  = 'STATUT';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::ETAT)) {
			$modifiedColumns[':p' . $index++]  = 'ETAT';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::CHANGE_MDP)) {
			$modifiedColumns[':p' . $index++]  = 'CHANGE_MDP';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::DATE_VERROUILLAGE)) {
			$modifiedColumns[':p' . $index++]  = 'DATE_VERROUILLAGE';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::PASSWORD_TICKET)) {
			$modifiedColumns[':p' . $index++]  = 'PASSWORD_TICKET';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::TICKET_EXPIRATION)) {
			$modifiedColumns[':p' . $index++]  = 'TICKET_EXPIRATION';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NIVEAU_ALERTE)) {
			$modifiedColumns[':p' . $index++]  = 'NIVEAU_ALERTE';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::OBSERVATION_SECURITE)) {
			$modifiedColumns[':p' . $index++]  = 'OBSERVATION_SECURITE';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::TEMP_DIR)) {
			$modifiedColumns[':p' . $index++]  = 'TEMP_DIR';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::NUMIND)) {
			$modifiedColumns[':p' . $index++]  = 'NUMIND';
		}
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::AUTH_MODE)) {
			$modifiedColumns[':p' . $index++]  = 'AUTH_MODE';
		}

		$sql = sprintf(
			'INSERT INTO utilisateurs (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'LOGIN':
						$stmt->bindValue($identifier, $this->login, PDO::PARAM_STR);
						break;
					case 'NOM':
						$stmt->bindValue($identifier, $this->nom, PDO::PARAM_STR);
						break;
					case 'PRENOM':
						$stmt->bindValue($identifier, $this->prenom, PDO::PARAM_STR);
						break;
					case 'CIVILITE':
						$stmt->bindValue($identifier, $this->civilite, PDO::PARAM_STR);
						break;
					case 'PASSWORD':
						$stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
						break;
					case 'SALT':
						$stmt->bindValue($identifier, $this->salt, PDO::PARAM_STR);
						break;
					case 'EMAIL':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
						break;
					case 'SHOW_EMAIL':
						$stmt->bindValue($identifier, $this->show_email, PDO::PARAM_STR);
						break;
					case 'STATUT':
						$stmt->bindValue($identifier, $this->statut, PDO::PARAM_STR);
						break;
					case 'ETAT':
						$stmt->bindValue($identifier, $this->etat, PDO::PARAM_STR);
						break;
					case 'CHANGE_MDP':
						$stmt->bindValue($identifier, $this->change_mdp, PDO::PARAM_STR);
						break;
					case 'DATE_VERROUILLAGE':
						$stmt->bindValue($identifier, $this->date_verrouillage, PDO::PARAM_STR);
						break;
					case 'PASSWORD_TICKET':
						$stmt->bindValue($identifier, $this->password_ticket, PDO::PARAM_STR);
						break;
					case 'TICKET_EXPIRATION':
						$stmt->bindValue($identifier, $this->ticket_expiration, PDO::PARAM_STR);
						break;
					case 'NIVEAU_ALERTE':
						$stmt->bindValue($identifier, $this->niveau_alerte, PDO::PARAM_INT);
						break;
					case 'OBSERVATION_SECURITE':
						$stmt->bindValue($identifier, $this->observation_securite, PDO::PARAM_INT);
						break;
					case 'TEMP_DIR':
						$stmt->bindValue($identifier, $this->temp_dir, PDO::PARAM_STR);
						break;
					case 'NUMIND':
						$stmt->bindValue($identifier, $this->numind, PDO::PARAM_STR);
						break;
					case 'AUTH_MODE':
						$stmt->bindValue($identifier, $this->auth_mode, PDO::PARAM_STR);
						break;
				}
			}
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
		}

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

				if ($this->collJScolClassess !== null) {
					foreach ($this->collJScolClassess as $referrerFK) {
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

				if ($this->collModifiedAbsenceEleveTraitements !== null) {
					foreach ($this->collModifiedAbsenceEleveTraitements as $referrerFK) {
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

				if ($this->collJProfesseursMatieress !== null) {
					foreach ($this->collJProfesseursMatieress as $referrerFK) {
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
				return $this->getSalt();
				break;
			case 6:
				return $this->getEmail();
				break;
			case 7:
				return $this->getShowEmail();
				break;
			case 8:
				return $this->getStatut();
				break;
			case 9:
				return $this->getEtat();
				break;
			case 10:
				return $this->getChangeMdp();
				break;
			case 11:
				return $this->getDateVerrouillage();
				break;
			case 12:
				return $this->getPasswordTicket();
				break;
			case 13:
				return $this->getTicketExpiration();
				break;
			case 14:
				return $this->getNiveauAlerte();
				break;
			case 15:
				return $this->getObservationSecurite();
				break;
			case 16:
				return $this->getTempDir();
				break;
			case 17:
				return $this->getNumind();
				break;
			case 18:
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
		if (isset($alreadyDumpedObjects['UtilisateurProfessionnel'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['UtilisateurProfessionnel'][$this->getPrimaryKey()] = true;
		$keys = UtilisateurProfessionnelPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getLogin(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getPrenom(),
			$keys[3] => $this->getCivilite(),
			$keys[4] => $this->getPassword(),
			$keys[5] => $this->getSalt(),
			$keys[6] => $this->getEmail(),
			$keys[7] => $this->getShowEmail(),
			$keys[8] => $this->getStatut(),
			$keys[9] => $this->getEtat(),
			$keys[10] => $this->getChangeMdp(),
			$keys[11] => $this->getDateVerrouillage(),
			$keys[12] => $this->getPasswordTicket(),
			$keys[13] => $this->getTicketExpiration(),
			$keys[14] => $this->getNiveauAlerte(),
			$keys[15] => $this->getObservationSecurite(),
			$keys[16] => $this->getTempDir(),
			$keys[17] => $this->getNumind(),
			$keys[18] => $this->getAuthMode(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->collJGroupesProfesseurss) {
				$result['JGroupesProfesseurss'] = $this->collJGroupesProfesseurss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJScolClassess) {
				$result['JScolClassess'] = $this->collJScolClassess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCahierTexteCompteRendus) {
				$result['CahierTexteCompteRendus'] = $this->collCahierTexteCompteRendus->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCahierTexteTravailAFaires) {
				$result['CahierTexteTravailAFaires'] = $this->collCahierTexteTravailAFaires->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collCahierTexteNoticePrivees) {
				$result['CahierTexteNoticePrivees'] = $this->collCahierTexteNoticePrivees->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveCpes) {
				$result['JEleveCpes'] = $this->collJEleveCpes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJEleveProfesseurPrincipals) {
				$result['JEleveProfesseurPrincipals'] = $this->collJEleveProfesseurPrincipals->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJAidUtilisateursProfessionnelss) {
				$result['JAidUtilisateursProfessionnelss'] = $this->collJAidUtilisateursProfessionnelss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveSaisies) {
				$result['AbsenceEleveSaisies'] = $this->collAbsenceEleveSaisies->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveTraitements) {
				$result['AbsenceEleveTraitements'] = $this->collAbsenceEleveTraitements->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collModifiedAbsenceEleveTraitements) {
				$result['ModifiedAbsenceEleveTraitements'] = $this->collModifiedAbsenceEleveTraitements->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveNotifications) {
				$result['AbsenceEleveNotifications'] = $this->collAbsenceEleveNotifications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJProfesseursMatieress) {
				$result['JProfesseursMatieress'] = $this->collJProfesseursMatieress->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collPreferenceUtilisateurProfessionnels) {
				$result['PreferenceUtilisateurProfessionnels'] = $this->collPreferenceUtilisateurProfessionnels->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collEdtEmplacementCourss) {
				$result['EdtEmplacementCourss'] = $this->collEdtEmplacementCourss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
				$this->setSalt($value);
				break;
			case 6:
				$this->setEmail($value);
				break;
			case 7:
				$this->setShowEmail($value);
				break;
			case 8:
				$this->setStatut($value);
				break;
			case 9:
				$this->setEtat($value);
				break;
			case 10:
				$this->setChangeMdp($value);
				break;
			case 11:
				$this->setDateVerrouillage($value);
				break;
			case 12:
				$this->setPasswordTicket($value);
				break;
			case 13:
				$this->setTicketExpiration($value);
				break;
			case 14:
				$this->setNiveauAlerte($value);
				break;
			case 15:
				$this->setObservationSecurite($value);
				break;
			case 16:
				$this->setTempDir($value);
				break;
			case 17:
				$this->setNumind($value);
				break;
			case 18:
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
		if (array_key_exists($keys[5], $arr)) $this->setSalt($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setEmail($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setShowEmail($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStatut($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setEtat($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setChangeMdp($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDateVerrouillage($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPasswordTicket($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setTicketExpiration($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setNiveauAlerte($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setObservationSecurite($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setTempDir($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setNumind($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setAuthMode($arr[$keys[18]]);
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
		if ($this->isColumnModified(UtilisateurProfessionnelPeer::SALT)) $criteria->add(UtilisateurProfessionnelPeer::SALT, $this->salt);
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
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getLogin();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of UtilisateurProfessionnel (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNom($this->getNom());
		$copyObj->setPrenom($this->getPrenom());
		$copyObj->setCivilite($this->getCivilite());
		$copyObj->setPassword($this->getPassword());
		$copyObj->setSalt($this->getSalt());
		$copyObj->setEmail($this->getEmail());
		$copyObj->setShowEmail($this->getShowEmail());
		$copyObj->setStatut($this->getStatut());
		$copyObj->setEtat($this->getEtat());
		$copyObj->setChangeMdp($this->getChangeMdp());
		$copyObj->setDateVerrouillage($this->getDateVerrouillage());
		$copyObj->setPasswordTicket($this->getPasswordTicket());
		$copyObj->setTicketExpiration($this->getTicketExpiration());
		$copyObj->setNiveauAlerte($this->getNiveauAlerte());
		$copyObj->setObservationSecurite($this->getObservationSecurite());
		$copyObj->setTempDir($this->getTempDir());
		$copyObj->setNumind($this->getNumind());
		$copyObj->setAuthMode($this->getAuthMode());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

			foreach ($this->getJGroupesProfesseurss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesProfesseurs($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJScolClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJScolClasses($relObj->copy($deepCopy));
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

			foreach ($this->getModifiedAbsenceEleveTraitements() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addModifiedAbsenceEleveTraitement($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveNotifications() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveNotification($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJProfesseursMatieress() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJProfesseursMatieres($relObj->copy($deepCopy));
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

			//unflag object copy
			$this->startCopy = false;
		} // if ($deepCopy)

		if ($makeNew) {
			$copyObj->setNew(true);
			$copyObj->setLogin(NULL); // this is a auto-increment column, so set to default value
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
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('JGroupesProfesseurs' == $relationName) {
			return $this->initJGroupesProfesseurss();
		}
		if ('JScolClasses' == $relationName) {
			return $this->initJScolClassess();
		}
		if ('CahierTexteCompteRendu' == $relationName) {
			return $this->initCahierTexteCompteRendus();
		}
		if ('CahierTexteTravailAFaire' == $relationName) {
			return $this->initCahierTexteTravailAFaires();
		}
		if ('CahierTexteNoticePrivee' == $relationName) {
			return $this->initCahierTexteNoticePrivees();
		}
		if ('JEleveCpe' == $relationName) {
			return $this->initJEleveCpes();
		}
		if ('JEleveProfesseurPrincipal' == $relationName) {
			return $this->initJEleveProfesseurPrincipals();
		}
		if ('JAidUtilisateursProfessionnels' == $relationName) {
			return $this->initJAidUtilisateursProfessionnelss();
		}
		if ('AbsenceEleveSaisie' == $relationName) {
			return $this->initAbsenceEleveSaisies();
		}
		if ('AbsenceEleveTraitement' == $relationName) {
			return $this->initAbsenceEleveTraitements();
		}
		if ('ModifiedAbsenceEleveTraitement' == $relationName) {
			return $this->initModifiedAbsenceEleveTraitements();
		}
		if ('AbsenceEleveNotification' == $relationName) {
			return $this->initAbsenceEleveNotifications();
		}
		if ('JProfesseursMatieres' == $relationName) {
			return $this->initJProfesseursMatieress();
		}
		if ('PreferenceUtilisateurProfessionnel' == $relationName) {
			return $this->initPreferenceUtilisateurProfessionnels();
		}
		if ('EdtEmplacementCours' == $relationName) {
			return $this->initEdtEmplacementCourss();
		}
	}

	/**
	 * Clears out the collJGroupesProfesseurss collection
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
	 * Initializes the collJGroupesProfesseurss collection.
	 *
	 * By default this just sets the collJGroupesProfesseurss collection to an empty array (like clearcollJGroupesProfesseurss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJGroupesProfesseurss($overrideExisting = true)
	{
		if (null !== $this->collJGroupesProfesseurss && !$overrideExisting) {
			return;
		}
		$this->collJGroupesProfesseurss = new PropelObjectCollection();
		$this->collJGroupesProfesseurss->setModel('JGroupesProfesseurs');
	}

	/**
	 * Gets an array of JGroupesProfesseurs objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JGroupesProfesseurs[] List of JGroupesProfesseurs objects
	 * @throws     PropelException
	 */
	public function getJGroupesProfesseurss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJGroupesProfesseurss || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesProfesseurss) {
				// return empty collection
				$this->initJGroupesProfesseurss();
			} else {
				$collJGroupesProfesseurss = JGroupesProfesseursQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collJGroupesProfesseurss;
				}
				$this->collJGroupesProfesseurss = $collJGroupesProfesseurss;
			}
		}
		return $this->collJGroupesProfesseurss;
	}

	/**
	 * Sets a collection of JGroupesProfesseurs objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jGroupesProfesseurss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJGroupesProfesseurss(PropelCollection $jGroupesProfesseurss, PropelPDO $con = null)
	{
		$this->jGroupesProfesseurssScheduledForDeletion = $this->getJGroupesProfesseurss(new Criteria(), $con)->diff($jGroupesProfesseurss);

		foreach ($jGroupesProfesseurss as $jGroupesProfesseurs) {
			// Fix issue with collection modified by reference
			if ($jGroupesProfesseurs->isNew()) {
				$jGroupesProfesseurs->setUtilisateurProfessionnel($this);
			}
			$this->addJGroupesProfesseurs($jGroupesProfesseurs);
		}

		$this->collJGroupesProfesseurss = $jGroupesProfesseurss;
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
		if(null === $this->collJGroupesProfesseurss || null !== $criteria) {
			if ($this->isNew() && null === $this->collJGroupesProfesseurss) {
				return 0;
			} else {
				$query = JGroupesProfesseursQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collJGroupesProfesseurss);
		}
	}

	/**
	 * Method called to associate a JGroupesProfesseurs object to this object
	 * through the JGroupesProfesseurs foreign key attribute.
	 *
	 * @param      JGroupesProfesseurs $l JGroupesProfesseurs
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addJGroupesProfesseurs(JGroupesProfesseurs $l)
	{
		if ($this->collJGroupesProfesseurss === null) {
			$this->initJGroupesProfesseurss();
		}
		if (!$this->collJGroupesProfesseurss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJGroupesProfesseurs($l);
		}

		return $this;
	}

	/**
	 * @param	JGroupesProfesseurs $jGroupesProfesseurs The jGroupesProfesseurs object to add.
	 */
	protected function doAddJGroupesProfesseurs($jGroupesProfesseurs)
	{
		$this->collJGroupesProfesseurss[]= $jGroupesProfesseurs;
		$jGroupesProfesseurs->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JGroupesProfesseurs[] List of JGroupesProfesseurs objects
	 */
	public function getJGroupesProfesseurssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JGroupesProfesseursQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getJGroupesProfesseurss($query, $con);
	}

	/**
	 * Clears out the collJScolClassess collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJScolClassess()
	 */
	public function clearJScolClassess()
	{
		$this->collJScolClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJScolClassess collection.
	 *
	 * By default this just sets the collJScolClassess collection to an empty array (like clearcollJScolClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJScolClassess($overrideExisting = true)
	{
		if (null !== $this->collJScolClassess && !$overrideExisting) {
			return;
		}
		$this->collJScolClassess = new PropelObjectCollection();
		$this->collJScolClassess->setModel('JScolClasses');
	}

	/**
	 * Gets an array of JScolClasses objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JScolClasses[] List of JScolClasses objects
	 * @throws     PropelException
	 */
	public function getJScolClassess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJScolClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJScolClassess) {
				// return empty collection
				$this->initJScolClassess();
			} else {
				$collJScolClassess = JScolClassesQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collJScolClassess;
				}
				$this->collJScolClassess = $collJScolClassess;
			}
		}
		return $this->collJScolClassess;
	}

	/**
	 * Sets a collection of JScolClasses objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jScolClassess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJScolClassess(PropelCollection $jScolClassess, PropelPDO $con = null)
	{
		$this->jScolClassessScheduledForDeletion = $this->getJScolClassess(new Criteria(), $con)->diff($jScolClassess);

		foreach ($jScolClassess as $jScolClasses) {
			// Fix issue with collection modified by reference
			if ($jScolClasses->isNew()) {
				$jScolClasses->setUtilisateurProfessionnel($this);
			}
			$this->addJScolClasses($jScolClasses);
		}

		$this->collJScolClassess = $jScolClassess;
	}

	/**
	 * Returns the number of related JScolClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JScolClasses objects.
	 * @throws     PropelException
	 */
	public function countJScolClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJScolClassess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJScolClassess) {
				return 0;
			} else {
				$query = JScolClassesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collJScolClassess);
		}
	}

	/**
	 * Method called to associate a JScolClasses object to this object
	 * through the JScolClasses foreign key attribute.
	 *
	 * @param      JScolClasses $l JScolClasses
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addJScolClasses(JScolClasses $l)
	{
		if ($this->collJScolClassess === null) {
			$this->initJScolClassess();
		}
		if (!$this->collJScolClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJScolClasses($l);
		}

		return $this;
	}

	/**
	 * @param	JScolClasses $jScolClasses The jScolClasses object to add.
	 */
	protected function doAddJScolClasses($jScolClasses)
	{
		$this->collJScolClassess[]= $jScolClasses;
		$jScolClasses->setUtilisateurProfessionnel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JScolClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JScolClasses[] List of JScolClasses objects
	 */
	public function getJScolClassessJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JScolClassesQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getJScolClassess($query, $con);
	}

	/**
	 * Clears out the collCahierTexteCompteRendus collection
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
	 * Initializes the collCahierTexteCompteRendus collection.
	 *
	 * By default this just sets the collCahierTexteCompteRendus collection to an empty array (like clearcollCahierTexteCompteRendus());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteCompteRendus($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteCompteRendus && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteCompteRendus = new PropelObjectCollection();
		$this->collCahierTexteCompteRendus->setModel('CahierTexteCompteRendu');
	}

	/**
	 * Gets an array of CahierTexteCompteRendu objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 * @throws     PropelException
	 */
	public function getCahierTexteCompteRendus($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteCompteRendus || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteCompteRendus) {
				// return empty collection
				$this->initCahierTexteCompteRendus();
			} else {
				$collCahierTexteCompteRendus = CahierTexteCompteRenduQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteCompteRendus;
				}
				$this->collCahierTexteCompteRendus = $collCahierTexteCompteRendus;
			}
		}
		return $this->collCahierTexteCompteRendus;
	}

	/**
	 * Sets a collection of CahierTexteCompteRendu objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteCompteRendus A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteCompteRendus(PropelCollection $cahierTexteCompteRendus, PropelPDO $con = null)
	{
		$this->cahierTexteCompteRendusScheduledForDeletion = $this->getCahierTexteCompteRendus(new Criteria(), $con)->diff($cahierTexteCompteRendus);

		foreach ($cahierTexteCompteRendus as $cahierTexteCompteRendu) {
			// Fix issue with collection modified by reference
			if ($cahierTexteCompteRendu->isNew()) {
				$cahierTexteCompteRendu->setUtilisateurProfessionnel($this);
			}
			$this->addCahierTexteCompteRendu($cahierTexteCompteRendu);
		}

		$this->collCahierTexteCompteRendus = $cahierTexteCompteRendus;
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
		if(null === $this->collCahierTexteCompteRendus || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteCompteRendus) {
				return 0;
			} else {
				$query = CahierTexteCompteRenduQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteCompteRendus);
		}
	}

	/**
	 * Method called to associate a CahierTexteCompteRendu object to this object
	 * through the CahierTexteCompteRendu foreign key attribute.
	 *
	 * @param      CahierTexteCompteRendu $l CahierTexteCompteRendu
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addCahierTexteCompteRendu(CahierTexteCompteRendu $l)
	{
		if ($this->collCahierTexteCompteRendus === null) {
			$this->initCahierTexteCompteRendus();
		}
		if (!$this->collCahierTexteCompteRendus->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteCompteRendu($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteCompteRendu $cahierTexteCompteRendu The cahierTexteCompteRendu object to add.
	 */
	protected function doAddCahierTexteCompteRendu($cahierTexteCompteRendu)
	{
		$this->collCahierTexteCompteRendus[]= $cahierTexteCompteRendu;
		$cahierTexteCompteRendu->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 */
	public function getCahierTexteCompteRendusJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteCompteRenduQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteCompteRendus($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteCompteRendu[] List of CahierTexteCompteRendu objects
	 */
	public function getCahierTexteCompteRendusJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteCompteRenduQuery::create(null, $criteria);
		$query->joinWith('CahierTexteSequence', $join_behavior);

		return $this->getCahierTexteCompteRendus($query, $con);
	}

	/**
	 * Clears out the collCahierTexteTravailAFaires collection
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
	 * Initializes the collCahierTexteTravailAFaires collection.
	 *
	 * By default this just sets the collCahierTexteTravailAFaires collection to an empty array (like clearcollCahierTexteTravailAFaires());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteTravailAFaires($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteTravailAFaires && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteTravailAFaires = new PropelObjectCollection();
		$this->collCahierTexteTravailAFaires->setModel('CahierTexteTravailAFaire');
	}

	/**
	 * Gets an array of CahierTexteTravailAFaire objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 * @throws     PropelException
	 */
	public function getCahierTexteTravailAFaires($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteTravailAFaires || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteTravailAFaires) {
				// return empty collection
				$this->initCahierTexteTravailAFaires();
			} else {
				$collCahierTexteTravailAFaires = CahierTexteTravailAFaireQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteTravailAFaires;
				}
				$this->collCahierTexteTravailAFaires = $collCahierTexteTravailAFaires;
			}
		}
		return $this->collCahierTexteTravailAFaires;
	}

	/**
	 * Sets a collection of CahierTexteTravailAFaire objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteTravailAFaires A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteTravailAFaires(PropelCollection $cahierTexteTravailAFaires, PropelPDO $con = null)
	{
		$this->cahierTexteTravailAFairesScheduledForDeletion = $this->getCahierTexteTravailAFaires(new Criteria(), $con)->diff($cahierTexteTravailAFaires);

		foreach ($cahierTexteTravailAFaires as $cahierTexteTravailAFaire) {
			// Fix issue with collection modified by reference
			if ($cahierTexteTravailAFaire->isNew()) {
				$cahierTexteTravailAFaire->setUtilisateurProfessionnel($this);
			}
			$this->addCahierTexteTravailAFaire($cahierTexteTravailAFaire);
		}

		$this->collCahierTexteTravailAFaires = $cahierTexteTravailAFaires;
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
		if(null === $this->collCahierTexteTravailAFaires || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteTravailAFaires) {
				return 0;
			} else {
				$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteTravailAFaires);
		}
	}

	/**
	 * Method called to associate a CahierTexteTravailAFaire object to this object
	 * through the CahierTexteTravailAFaire foreign key attribute.
	 *
	 * @param      CahierTexteTravailAFaire $l CahierTexteTravailAFaire
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addCahierTexteTravailAFaire(CahierTexteTravailAFaire $l)
	{
		if ($this->collCahierTexteTravailAFaires === null) {
			$this->initCahierTexteTravailAFaires();
		}
		if (!$this->collCahierTexteTravailAFaires->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteTravailAFaire($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteTravailAFaire $cahierTexteTravailAFaire The cahierTexteTravailAFaire object to add.
	 */
	protected function doAddCahierTexteTravailAFaire($cahierTexteTravailAFaire)
	{
		$this->collCahierTexteTravailAFaires[]= $cahierTexteTravailAFaire;
		$cahierTexteTravailAFaire->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 */
	public function getCahierTexteTravailAFairesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteTravailAFaires($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteTravailAFaire[] List of CahierTexteTravailAFaire objects
	 */
	public function getCahierTexteTravailAFairesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteTravailAFaireQuery::create(null, $criteria);
		$query->joinWith('CahierTexteSequence', $join_behavior);

		return $this->getCahierTexteTravailAFaires($query, $con);
	}

	/**
	 * Clears out the collCahierTexteNoticePrivees collection
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
	 * Initializes the collCahierTexteNoticePrivees collection.
	 *
	 * By default this just sets the collCahierTexteNoticePrivees collection to an empty array (like clearcollCahierTexteNoticePrivees());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initCahierTexteNoticePrivees($overrideExisting = true)
	{
		if (null !== $this->collCahierTexteNoticePrivees && !$overrideExisting) {
			return;
		}
		$this->collCahierTexteNoticePrivees = new PropelObjectCollection();
		$this->collCahierTexteNoticePrivees->setModel('CahierTexteNoticePrivee');
	}

	/**
	 * Gets an array of CahierTexteNoticePrivee objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 * @throws     PropelException
	 */
	public function getCahierTexteNoticePrivees($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCahierTexteNoticePrivees || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteNoticePrivees) {
				// return empty collection
				$this->initCahierTexteNoticePrivees();
			} else {
				$collCahierTexteNoticePrivees = CahierTexteNoticePriveeQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collCahierTexteNoticePrivees;
				}
				$this->collCahierTexteNoticePrivees = $collCahierTexteNoticePrivees;
			}
		}
		return $this->collCahierTexteNoticePrivees;
	}

	/**
	 * Sets a collection of CahierTexteNoticePrivee objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $cahierTexteNoticePrivees A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setCahierTexteNoticePrivees(PropelCollection $cahierTexteNoticePrivees, PropelPDO $con = null)
	{
		$this->cahierTexteNoticePriveesScheduledForDeletion = $this->getCahierTexteNoticePrivees(new Criteria(), $con)->diff($cahierTexteNoticePrivees);

		foreach ($cahierTexteNoticePrivees as $cahierTexteNoticePrivee) {
			// Fix issue with collection modified by reference
			if ($cahierTexteNoticePrivee->isNew()) {
				$cahierTexteNoticePrivee->setUtilisateurProfessionnel($this);
			}
			$this->addCahierTexteNoticePrivee($cahierTexteNoticePrivee);
		}

		$this->collCahierTexteNoticePrivees = $cahierTexteNoticePrivees;
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
		if(null === $this->collCahierTexteNoticePrivees || null !== $criteria) {
			if ($this->isNew() && null === $this->collCahierTexteNoticePrivees) {
				return 0;
			} else {
				$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collCahierTexteNoticePrivees);
		}
	}

	/**
	 * Method called to associate a CahierTexteNoticePrivee object to this object
	 * through the CahierTexteNoticePrivee foreign key attribute.
	 *
	 * @param      CahierTexteNoticePrivee $l CahierTexteNoticePrivee
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addCahierTexteNoticePrivee(CahierTexteNoticePrivee $l)
	{
		if ($this->collCahierTexteNoticePrivees === null) {
			$this->initCahierTexteNoticePrivees();
		}
		if (!$this->collCahierTexteNoticePrivees->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddCahierTexteNoticePrivee($l);
		}

		return $this;
	}

	/**
	 * @param	CahierTexteNoticePrivee $cahierTexteNoticePrivee The cahierTexteNoticePrivee object to add.
	 */
	protected function doAddCahierTexteNoticePrivee($cahierTexteNoticePrivee)
	{
		$this->collCahierTexteNoticePrivees[]= $cahierTexteNoticePrivee;
		$cahierTexteNoticePrivee->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 */
	public function getCahierTexteNoticePriveesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getCahierTexteNoticePrivees($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CahierTexteNoticePrivee[] List of CahierTexteNoticePrivee objects
	 */
	public function getCahierTexteNoticePriveesJoinCahierTexteSequence($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CahierTexteNoticePriveeQuery::create(null, $criteria);
		$query->joinWith('CahierTexteSequence', $join_behavior);

		return $this->getCahierTexteNoticePrivees($query, $con);
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
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
				$jEleveCpe->setUtilisateurProfessionnel($this);
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
					->filterByUtilisateurProfessionnel($this)
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
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
		$jEleveCpe->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveCpe[] List of JEleveCpe objects
	 */
	public function getJEleveCpesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveCpeQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getJEleveCpes($query, $con);
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
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
				$jEleveProfesseurPrincipal->setUtilisateurProfessionnel($this);
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
					->filterByUtilisateurProfessionnel($this)
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
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
		$jEleveProfesseurPrincipal->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JEleveProfesseurPrincipal[] List of JEleveProfesseurPrincipal objects
	 */
	public function getJEleveProfesseurPrincipalsJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveProfesseurPrincipalQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getJEleveProfesseurPrincipals($query, $con);
	}

	/**
	 * Clears out the collJAidUtilisateursProfessionnelss collection
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
	 * Initializes the collJAidUtilisateursProfessionnelss collection.
	 *
	 * By default this just sets the collJAidUtilisateursProfessionnelss collection to an empty array (like clearcollJAidUtilisateursProfessionnelss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJAidUtilisateursProfessionnelss($overrideExisting = true)
	{
		if (null !== $this->collJAidUtilisateursProfessionnelss && !$overrideExisting) {
			return;
		}
		$this->collJAidUtilisateursProfessionnelss = new PropelObjectCollection();
		$this->collJAidUtilisateursProfessionnelss->setModel('JAidUtilisateursProfessionnels');
	}

	/**
	 * Gets an array of JAidUtilisateursProfessionnels objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JAidUtilisateursProfessionnels[] List of JAidUtilisateursProfessionnels objects
	 * @throws     PropelException
	 */
	public function getJAidUtilisateursProfessionnelss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJAidUtilisateursProfessionnelss || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidUtilisateursProfessionnelss) {
				// return empty collection
				$this->initJAidUtilisateursProfessionnelss();
			} else {
				$collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collJAidUtilisateursProfessionnelss;
				}
				$this->collJAidUtilisateursProfessionnelss = $collJAidUtilisateursProfessionnelss;
			}
		}
		return $this->collJAidUtilisateursProfessionnelss;
	}

	/**
	 * Sets a collection of JAidUtilisateursProfessionnels objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jAidUtilisateursProfessionnelss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJAidUtilisateursProfessionnelss(PropelCollection $jAidUtilisateursProfessionnelss, PropelPDO $con = null)
	{
		$this->jAidUtilisateursProfessionnelssScheduledForDeletion = $this->getJAidUtilisateursProfessionnelss(new Criteria(), $con)->diff($jAidUtilisateursProfessionnelss);

		foreach ($jAidUtilisateursProfessionnelss as $jAidUtilisateursProfessionnels) {
			// Fix issue with collection modified by reference
			if ($jAidUtilisateursProfessionnels->isNew()) {
				$jAidUtilisateursProfessionnels->setUtilisateurProfessionnel($this);
			}
			$this->addJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels);
		}

		$this->collJAidUtilisateursProfessionnelss = $jAidUtilisateursProfessionnelss;
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
		if(null === $this->collJAidUtilisateursProfessionnelss || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidUtilisateursProfessionnelss) {
				return 0;
			} else {
				$query = JAidUtilisateursProfessionnelsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collJAidUtilisateursProfessionnelss);
		}
	}

	/**
	 * Method called to associate a JAidUtilisateursProfessionnels object to this object
	 * through the JAidUtilisateursProfessionnels foreign key attribute.
	 *
	 * @param      JAidUtilisateursProfessionnels $l JAidUtilisateursProfessionnels
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addJAidUtilisateursProfessionnels(JAidUtilisateursProfessionnels $l)
	{
		if ($this->collJAidUtilisateursProfessionnelss === null) {
			$this->initJAidUtilisateursProfessionnelss();
		}
		if (!$this->collJAidUtilisateursProfessionnelss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJAidUtilisateursProfessionnels($l);
		}

		return $this;
	}

	/**
	 * @param	JAidUtilisateursProfessionnels $jAidUtilisateursProfessionnels The jAidUtilisateursProfessionnels object to add.
	 */
	protected function doAddJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels)
	{
		$this->collJAidUtilisateursProfessionnelss[]= $jAidUtilisateursProfessionnels;
		$jAidUtilisateursProfessionnels->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JAidUtilisateursProfessionnels[] List of JAidUtilisateursProfessionnels objects
	 */
	public function getJAidUtilisateursProfessionnelssJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JAidUtilisateursProfessionnelsQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getJAidUtilisateursProfessionnelss($query, $con);
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
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
				$absenceEleveSaisie->setUtilisateurProfessionnel($this);
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
					->filterByUtilisateurProfessionnel($this)
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
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
		$absenceEleveSaisie->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveTraitements($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveTraitements && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveTraitements = new PropelObjectCollection();
		$this->collAbsenceEleveTraitements->setModel('AbsenceEleveTraitement');
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
	 * Sets a collection of AbsenceEleveTraitement objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveTraitements A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveTraitements(PropelCollection $absenceEleveTraitements, PropelPDO $con = null)
	{
		$this->absenceEleveTraitementsScheduledForDeletion = $this->getAbsenceEleveTraitements(new Criteria(), $con)->diff($absenceEleveTraitements);

		foreach ($absenceEleveTraitements as $absenceEleveTraitement) {
			// Fix issue with collection modified by reference
			if ($absenceEleveTraitement->isNew()) {
				$absenceEleveTraitement->setUtilisateurProfessionnel($this);
			}
			$this->addAbsenceEleveTraitement($absenceEleveTraitement);
		}

		$this->collAbsenceEleveTraitements = $absenceEleveTraitements;
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
					->filterByUtilisateurProfessionnel($this)
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collAbsenceEleveTraitements === null) {
			$this->initAbsenceEleveTraitements();
		}
		if (!$this->collAbsenceEleveTraitements->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveTraitement($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveTraitement $absenceEleveTraitement The absenceEleveTraitement object to add.
	 */
	protected function doAddAbsenceEleveTraitement($absenceEleveTraitement)
	{
		$this->collAbsenceEleveTraitements[]= $absenceEleveTraitement;
		$absenceEleveTraitement->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitementsJoinAbsenceEleveType($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveType', $join_behavior);

		return $this->getAbsenceEleveTraitements($query, $con);
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
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
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
	 * Clears out the collModifiedAbsenceEleveTraitements collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addModifiedAbsenceEleveTraitements()
	 */
	public function clearModifiedAbsenceEleveTraitements()
	{
		$this->collModifiedAbsenceEleveTraitements = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collModifiedAbsenceEleveTraitements collection.
	 *
	 * By default this just sets the collModifiedAbsenceEleveTraitements collection to an empty array (like clearcollModifiedAbsenceEleveTraitements());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initModifiedAbsenceEleveTraitements($overrideExisting = true)
	{
		if (null !== $this->collModifiedAbsenceEleveTraitements && !$overrideExisting) {
			return;
		}
		$this->collModifiedAbsenceEleveTraitements = new PropelObjectCollection();
		$this->collModifiedAbsenceEleveTraitements->setModel('AbsenceEleveTraitement');
	}

	/**
	 * Gets an array of AbsenceEleveTraitement objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 * @throws     PropelException
	 */
	public function getModifiedAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collModifiedAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collModifiedAbsenceEleveTraitements) {
				// return empty collection
				$this->initModifiedAbsenceEleveTraitements();
			} else {
				$collModifiedAbsenceEleveTraitements = AbsenceEleveTraitementQuery::create(null, $criteria)
					->filterByModifieParUtilisateur($this)
					->find($con);
				if (null !== $criteria) {
					return $collModifiedAbsenceEleveTraitements;
				}
				$this->collModifiedAbsenceEleveTraitements = $collModifiedAbsenceEleveTraitements;
			}
		}
		return $this->collModifiedAbsenceEleveTraitements;
	}

	/**
	 * Sets a collection of ModifiedAbsenceEleveTraitement objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $modifiedAbsenceEleveTraitements A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setModifiedAbsenceEleveTraitements(PropelCollection $modifiedAbsenceEleveTraitements, PropelPDO $con = null)
	{
		$this->modifiedAbsenceEleveTraitementsScheduledForDeletion = $this->getModifiedAbsenceEleveTraitements(new Criteria(), $con)->diff($modifiedAbsenceEleveTraitements);

		foreach ($modifiedAbsenceEleveTraitements as $modifiedAbsenceEleveTraitement) {
			// Fix issue with collection modified by reference
			if ($modifiedAbsenceEleveTraitement->isNew()) {
				$modifiedAbsenceEleveTraitement->setModifieParUtilisateur($this);
			}
			$this->addModifiedAbsenceEleveTraitement($modifiedAbsenceEleveTraitement);
		}

		$this->collModifiedAbsenceEleveTraitements = $modifiedAbsenceEleveTraitements;
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
	public function countModifiedAbsenceEleveTraitements(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collModifiedAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collModifiedAbsenceEleveTraitements) {
				return 0;
			} else {
				$query = AbsenceEleveTraitementQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByModifieParUtilisateur($this)
					->count($con);
			}
		} else {
			return count($this->collModifiedAbsenceEleveTraitements);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveTraitement object to this object
	 * through the AbsenceEleveTraitement foreign key attribute.
	 *
	 * @param      AbsenceEleveTraitement $l AbsenceEleveTraitement
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addModifiedAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collModifiedAbsenceEleveTraitements === null) {
			$this->initModifiedAbsenceEleveTraitements();
		}
		if (!$this->collModifiedAbsenceEleveTraitements->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddModifiedAbsenceEleveTraitement($l);
		}

		return $this;
	}

	/**
	 * @param	ModifiedAbsenceEleveTraitement $modifiedAbsenceEleveTraitement The modifiedAbsenceEleveTraitement object to add.
	 */
	protected function doAddModifiedAbsenceEleveTraitement($modifiedAbsenceEleveTraitement)
	{
		$this->collModifiedAbsenceEleveTraitements[]= $modifiedAbsenceEleveTraitement;
		$modifiedAbsenceEleveTraitement->setModifieParUtilisateur($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getModifiedAbsenceEleveTraitementsJoinAbsenceEleveType($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveType', $join_behavior);

		return $this->getModifiedAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getModifiedAbsenceEleveTraitementsJoinAbsenceEleveMotif($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveMotif', $join_behavior);

		return $this->getModifiedAbsenceEleveTraitements($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveTraitements from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getModifiedAbsenceEleveTraitementsJoinAbsenceEleveJustification($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveTraitementQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveJustification', $join_behavior);

		return $this->getModifiedAbsenceEleveTraitements($query, $con);
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
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveNotifications($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveNotifications && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveNotifications = new PropelObjectCollection();
		$this->collAbsenceEleveNotifications->setModel('AbsenceEleveNotification');
	}

	/**
	 * Gets an array of AbsenceEleveNotification objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
	 * Sets a collection of AbsenceEleveNotification objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $absenceEleveNotifications A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAbsenceEleveNotifications(PropelCollection $absenceEleveNotifications, PropelPDO $con = null)
	{
		$this->absenceEleveNotificationsScheduledForDeletion = $this->getAbsenceEleveNotifications(new Criteria(), $con)->diff($absenceEleveNotifications);

		foreach ($absenceEleveNotifications as $absenceEleveNotification) {
			// Fix issue with collection modified by reference
			if ($absenceEleveNotification->isNew()) {
				$absenceEleveNotification->setUtilisateurProfessionnel($this);
			}
			$this->addAbsenceEleveNotification($absenceEleveNotification);
		}

		$this->collAbsenceEleveNotifications = $absenceEleveNotifications;
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
					->filterByUtilisateurProfessionnel($this)
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
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addAbsenceEleveNotification(AbsenceEleveNotification $l)
	{
		if ($this->collAbsenceEleveNotifications === null) {
			$this->initAbsenceEleveNotifications();
		}
		if (!$this->collAbsenceEleveNotifications->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddAbsenceEleveNotification($l);
		}

		return $this;
	}

	/**
	 * @param	AbsenceEleveNotification $absenceEleveNotification The absenceEleveNotification object to add.
	 */
	protected function doAddAbsenceEleveNotification($absenceEleveNotification)
	{
		$this->collAbsenceEleveNotifications[]= $absenceEleveNotification;
		$absenceEleveNotification->setUtilisateurProfessionnel($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinAbsenceEleveTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveTraitement', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related AbsenceEleveNotifications from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveNotification[] List of AbsenceEleveNotification objects
	 */
	public function getAbsenceEleveNotificationsJoinAdresse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('Adresse', $join_behavior);

		return $this->getAbsenceEleveNotifications($query, $con);
	}

	/**
	 * Clears out the collJProfesseursMatieress collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJProfesseursMatieress()
	 */
	public function clearJProfesseursMatieress()
	{
		$this->collJProfesseursMatieress = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJProfesseursMatieress collection.
	 *
	 * By default this just sets the collJProfesseursMatieress collection to an empty array (like clearcollJProfesseursMatieress());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJProfesseursMatieress($overrideExisting = true)
	{
		if (null !== $this->collJProfesseursMatieress && !$overrideExisting) {
			return;
		}
		$this->collJProfesseursMatieress = new PropelObjectCollection();
		$this->collJProfesseursMatieress->setModel('JProfesseursMatieres');
	}

	/**
	 * Gets an array of JProfesseursMatieres objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JProfesseursMatieres[] List of JProfesseursMatieres objects
	 * @throws     PropelException
	 */
	public function getJProfesseursMatieress($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJProfesseursMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJProfesseursMatieress) {
				// return empty collection
				$this->initJProfesseursMatieress();
			} else {
				$collJProfesseursMatieress = JProfesseursMatieresQuery::create(null, $criteria)
					->filterByProfesseur($this)
					->find($con);
				if (null !== $criteria) {
					return $collJProfesseursMatieress;
				}
				$this->collJProfesseursMatieress = $collJProfesseursMatieress;
			}
		}
		return $this->collJProfesseursMatieress;
	}

	/**
	 * Sets a collection of JProfesseursMatieres objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jProfesseursMatieress A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJProfesseursMatieress(PropelCollection $jProfesseursMatieress, PropelPDO $con = null)
	{
		$this->jProfesseursMatieressScheduledForDeletion = $this->getJProfesseursMatieress(new Criteria(), $con)->diff($jProfesseursMatieress);

		foreach ($jProfesseursMatieress as $jProfesseursMatieres) {
			// Fix issue with collection modified by reference
			if ($jProfesseursMatieres->isNew()) {
				$jProfesseursMatieres->setProfesseur($this);
			}
			$this->addJProfesseursMatieres($jProfesseursMatieres);
		}

		$this->collJProfesseursMatieress = $jProfesseursMatieress;
	}

	/**
	 * Returns the number of related JProfesseursMatieres objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JProfesseursMatieres objects.
	 * @throws     PropelException
	 */
	public function countJProfesseursMatieress(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJProfesseursMatieress || null !== $criteria) {
			if ($this->isNew() && null === $this->collJProfesseursMatieress) {
				return 0;
			} else {
				$query = JProfesseursMatieresQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByProfesseur($this)
					->count($con);
			}
		} else {
			return count($this->collJProfesseursMatieress);
		}
	}

	/**
	 * Method called to associate a JProfesseursMatieres object to this object
	 * through the JProfesseursMatieres foreign key attribute.
	 *
	 * @param      JProfesseursMatieres $l JProfesseursMatieres
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addJProfesseursMatieres(JProfesseursMatieres $l)
	{
		if ($this->collJProfesseursMatieress === null) {
			$this->initJProfesseursMatieress();
		}
		if (!$this->collJProfesseursMatieress->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJProfesseursMatieres($l);
		}

		return $this;
	}

	/**
	 * @param	JProfesseursMatieres $jProfesseursMatieres The jProfesseursMatieres object to add.
	 */
	protected function doAddJProfesseursMatieres($jProfesseursMatieres)
	{
		$this->collJProfesseursMatieress[]= $jProfesseursMatieres;
		$jProfesseursMatieres->setProfesseur($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related JProfesseursMatieress from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in UtilisateurProfessionnel.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JProfesseursMatieres[] List of JProfesseursMatieres objects
	 */
	public function getJProfesseursMatieressJoinMatiere($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JProfesseursMatieresQuery::create(null, $criteria);
		$query->joinWith('Matiere', $join_behavior);

		return $this->getJProfesseursMatieress($query, $con);
	}

	/**
	 * Clears out the collPreferenceUtilisateurProfessionnels collection
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
	 * Initializes the collPreferenceUtilisateurProfessionnels collection.
	 *
	 * By default this just sets the collPreferenceUtilisateurProfessionnels collection to an empty array (like clearcollPreferenceUtilisateurProfessionnels());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initPreferenceUtilisateurProfessionnels($overrideExisting = true)
	{
		if (null !== $this->collPreferenceUtilisateurProfessionnels && !$overrideExisting) {
			return;
		}
		$this->collPreferenceUtilisateurProfessionnels = new PropelObjectCollection();
		$this->collPreferenceUtilisateurProfessionnels->setModel('PreferenceUtilisateurProfessionnel');
	}

	/**
	 * Gets an array of PreferenceUtilisateurProfessionnel objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array PreferenceUtilisateurProfessionnel[] List of PreferenceUtilisateurProfessionnel objects
	 * @throws     PropelException
	 */
	public function getPreferenceUtilisateurProfessionnels($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collPreferenceUtilisateurProfessionnels || null !== $criteria) {
			if ($this->isNew() && null === $this->collPreferenceUtilisateurProfessionnels) {
				// return empty collection
				$this->initPreferenceUtilisateurProfessionnels();
			} else {
				$collPreferenceUtilisateurProfessionnels = PreferenceUtilisateurProfessionnelQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collPreferenceUtilisateurProfessionnels;
				}
				$this->collPreferenceUtilisateurProfessionnels = $collPreferenceUtilisateurProfessionnels;
			}
		}
		return $this->collPreferenceUtilisateurProfessionnels;
	}

	/**
	 * Sets a collection of PreferenceUtilisateurProfessionnel objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $preferenceUtilisateurProfessionnels A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setPreferenceUtilisateurProfessionnels(PropelCollection $preferenceUtilisateurProfessionnels, PropelPDO $con = null)
	{
		$this->preferenceUtilisateurProfessionnelsScheduledForDeletion = $this->getPreferenceUtilisateurProfessionnels(new Criteria(), $con)->diff($preferenceUtilisateurProfessionnels);

		foreach ($preferenceUtilisateurProfessionnels as $preferenceUtilisateurProfessionnel) {
			// Fix issue with collection modified by reference
			if ($preferenceUtilisateurProfessionnel->isNew()) {
				$preferenceUtilisateurProfessionnel->setUtilisateurProfessionnel($this);
			}
			$this->addPreferenceUtilisateurProfessionnel($preferenceUtilisateurProfessionnel);
		}

		$this->collPreferenceUtilisateurProfessionnels = $preferenceUtilisateurProfessionnels;
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
		if(null === $this->collPreferenceUtilisateurProfessionnels || null !== $criteria) {
			if ($this->isNew() && null === $this->collPreferenceUtilisateurProfessionnels) {
				return 0;
			} else {
				$query = PreferenceUtilisateurProfessionnelQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collPreferenceUtilisateurProfessionnels);
		}
	}

	/**
	 * Method called to associate a PreferenceUtilisateurProfessionnel object to this object
	 * through the PreferenceUtilisateurProfessionnel foreign key attribute.
	 *
	 * @param      PreferenceUtilisateurProfessionnel $l PreferenceUtilisateurProfessionnel
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addPreferenceUtilisateurProfessionnel(PreferenceUtilisateurProfessionnel $l)
	{
		if ($this->collPreferenceUtilisateurProfessionnels === null) {
			$this->initPreferenceUtilisateurProfessionnels();
		}
		if (!$this->collPreferenceUtilisateurProfessionnels->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddPreferenceUtilisateurProfessionnel($l);
		}

		return $this;
	}

	/**
	 * @param	PreferenceUtilisateurProfessionnel $preferenceUtilisateurProfessionnel The preferenceUtilisateurProfessionnel object to add.
	 */
	protected function doAddPreferenceUtilisateurProfessionnel($preferenceUtilisateurProfessionnel)
	{
		$this->collPreferenceUtilisateurProfessionnels[]= $preferenceUtilisateurProfessionnel;
		$preferenceUtilisateurProfessionnel->setUtilisateurProfessionnel($this);
	}

	/**
	 * Clears out the collEdtEmplacementCourss collection
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
	 * Initializes the collEdtEmplacementCourss collection.
	 *
	 * By default this just sets the collEdtEmplacementCourss collection to an empty array (like clearcollEdtEmplacementCourss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initEdtEmplacementCourss($overrideExisting = true)
	{
		if (null !== $this->collEdtEmplacementCourss && !$overrideExisting) {
			return;
		}
		$this->collEdtEmplacementCourss = new PropelObjectCollection();
		$this->collEdtEmplacementCourss->setModel('EdtEmplacementCours');
	}

	/**
	 * Gets an array of EdtEmplacementCours objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 * @throws     PropelException
	 */
	public function getEdtEmplacementCourss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEdtEmplacementCourss || null !== $criteria) {
			if ($this->isNew() && null === $this->collEdtEmplacementCourss) {
				// return empty collection
				$this->initEdtEmplacementCourss();
			} else {
				$collEdtEmplacementCourss = EdtEmplacementCoursQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collEdtEmplacementCourss;
				}
				$this->collEdtEmplacementCourss = $collEdtEmplacementCourss;
			}
		}
		return $this->collEdtEmplacementCourss;
	}

	/**
	 * Sets a collection of EdtEmplacementCours objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $edtEmplacementCourss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setEdtEmplacementCourss(PropelCollection $edtEmplacementCourss, PropelPDO $con = null)
	{
		$this->edtEmplacementCourssScheduledForDeletion = $this->getEdtEmplacementCourss(new Criteria(), $con)->diff($edtEmplacementCourss);

		foreach ($edtEmplacementCourss as $edtEmplacementCours) {
			// Fix issue with collection modified by reference
			if ($edtEmplacementCours->isNew()) {
				$edtEmplacementCours->setUtilisateurProfessionnel($this);
			}
			$this->addEdtEmplacementCours($edtEmplacementCours);
		}

		$this->collEdtEmplacementCourss = $edtEmplacementCourss;
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
		if(null === $this->collEdtEmplacementCourss || null !== $criteria) {
			if ($this->isNew() && null === $this->collEdtEmplacementCourss) {
				return 0;
			} else {
				$query = EdtEmplacementCoursQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collEdtEmplacementCourss);
		}
	}

	/**
	 * Method called to associate a EdtEmplacementCours object to this object
	 * through the EdtEmplacementCours foreign key attribute.
	 *
	 * @param      EdtEmplacementCours $l EdtEmplacementCours
	 * @return     UtilisateurProfessionnel The current object (for fluent API support)
	 */
	public function addEdtEmplacementCours(EdtEmplacementCours $l)
	{
		if ($this->collEdtEmplacementCourss === null) {
			$this->initEdtEmplacementCourss();
		}
		if (!$this->collEdtEmplacementCourss->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddEdtEmplacementCours($l);
		}

		return $this;
	}

	/**
	 * @param	EdtEmplacementCours $edtEmplacementCours The edtEmplacementCours object to add.
	 */
	protected function doAddEdtEmplacementCours($edtEmplacementCours)
	{
		$this->collEdtEmplacementCourss[]= $edtEmplacementCours;
		$edtEmplacementCours->setUtilisateurProfessionnel($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinEdtSalle($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('EdtSalle', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('EdtCreneau', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinEdtCalendrierPeriode($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('EdtCalendrierPeriode', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
	}

	/**
	 * Clears out the collGroupes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addGroupes()
	 */
	public function clearGroupes()
	{
		$this->collGroupes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collGroupes collection.
	 *
	 * By default this just sets the collGroupes collection to an empty collection (like clearGroupes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initGroupes()
	{
		$this->collGroupes = new PropelObjectCollection();
		$this->collGroupes->setModel('Groupe');
	}

	/**
	 * Gets a collection of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_professeurs cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Groupe[] List of Groupe objects
	 */
	public function getGroupes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collGroupes) {
				// return empty collection
				$this->initGroupes();
			} else {
				$collGroupes = GroupeQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if (null !== $criteria) {
					return $collGroupes;
				}
				$this->collGroupes = $collGroupes;
			}
		}
		return $this->collGroupes;
	}

	/**
	 * Sets a collection of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_professeurs cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $groupes A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setGroupes(PropelCollection $groupes, PropelPDO $con = null)
	{
		$jGroupesProfesseurss = JGroupesProfesseursQuery::create()
			->filterByGroupe($groupes)
			->filterByUtilisateurProfessionnel($this)
			->find($con);

		$this->groupesScheduledForDeletion = $this->getJGroupesProfesseurss()->diff($jGroupesProfesseurss);
		$this->collJGroupesProfesseurss = $jGroupesProfesseurss;

		foreach ($groupes as $groupe) {
			// Fix issue with collection modified by reference
			if ($groupe->isNew()) {
				$this->doAddGroupe($groupe);
			} else {
				$this->addGroupe($groupe);
			}
		}

		$this->collGroupes = $groupes;
	}

	/**
	 * Gets the number of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_professeurs cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Groupe objects
	 */
	public function countGroupes($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collGroupes) {
				return 0;
			} else {
				$query = GroupeQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collGroupes);
		}
	}

	/**
	 * Associate a Groupe object to this object
	 * through the j_groupes_professeurs cross reference table.
	 *
	 * @param      Groupe $groupe The JGroupesProfesseurs object to relate
	 * @return     void
	 */
	public function addGroupe(Groupe $groupe)
	{
		if ($this->collGroupes === null) {
			$this->initGroupes();
		}
		if (!$this->collGroupes->contains($groupe)) { // only add it if the **same** object is not already associated
			$this->doAddGroupe($groupe);

			$this->collGroupes[]= $groupe;
		}
	}

	/**
	 * @param	Groupe $groupe The groupe object to add.
	 */
	protected function doAddGroupe($groupe)
	{
		$jGroupesProfesseurs = new JGroupesProfesseurs();
		$jGroupesProfesseurs->setGroupe($groupe);
		$this->addJGroupesProfesseurs($jGroupesProfesseurs);
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
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
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
					->filterByUtilisateurProfessionnel($this)
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
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $aidDetailss A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setAidDetailss(PropelCollection $aidDetailss, PropelPDO $con = null)
	{
		$jAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsQuery::create()
			->filterByAidDetails($aidDetailss)
			->filterByUtilisateurProfessionnel($this)
			->find($con);

		$this->aidDetailssScheduledForDeletion = $this->getJAidUtilisateursProfessionnelss()->diff($jAidUtilisateursProfessionnelss);
		$this->collJAidUtilisateursProfessionnelss = $jAidUtilisateursProfessionnelss;

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
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
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
					->filterByUtilisateurProfessionnel($this)
					->count($con);
			}
		} else {
			return count($this->collAidDetailss);
		}
	}

	/**
	 * Associate a AidDetails object to this object
	 * through the j_aid_utilisateurs cross reference table.
	 *
	 * @param      AidDetails $aidDetails The JAidUtilisateursProfessionnels object to relate
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
		$jAidUtilisateursProfessionnels = new JAidUtilisateursProfessionnels();
		$jAidUtilisateursProfessionnels->setAidDetails($aidDetails);
		$this->addJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels);
	}

	/**
	 * Clears out the collMatieres collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addMatieres()
	 */
	public function clearMatieres()
	{
		$this->collMatieres = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collMatieres collection.
	 *
	 * By default this just sets the collMatieres collection to an empty collection (like clearMatieres());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initMatieres()
	{
		$this->collMatieres = new PropelObjectCollection();
		$this->collMatieres->setModel('Matiere');
	}

	/**
	 * Gets a collection of Matiere objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Matiere[] List of Matiere objects
	 */
	public function getMatieres($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collMatieres) {
				// return empty collection
				$this->initMatieres();
			} else {
				$collMatieres = MatiereQuery::create(null, $criteria)
					->filterByProfesseur($this)
					->find($con);
				if (null !== $criteria) {
					return $collMatieres;
				}
				$this->collMatieres = $collMatieres;
			}
		}
		return $this->collMatieres;
	}

	/**
	 * Sets a collection of Matiere objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $matieres A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setMatieres(PropelCollection $matieres, PropelPDO $con = null)
	{
		$jProfesseursMatieress = JProfesseursMatieresQuery::create()
			->filterByMatiere($matieres)
			->filterByProfesseur($this)
			->find($con);

		$this->matieresScheduledForDeletion = $this->getJProfesseursMatieress()->diff($jProfesseursMatieress);
		$this->collJProfesseursMatieress = $jProfesseursMatieress;

		foreach ($matieres as $matiere) {
			// Fix issue with collection modified by reference
			if ($matiere->isNew()) {
				$this->doAddMatiere($matiere);
			} else {
				$this->addMatiere($matiere);
			}
		}

		$this->collMatieres = $matieres;
	}

	/**
	 * Gets the number of Matiere objects related by a many-to-many relationship
	 * to the current object by way of the j_professeurs_matieres cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Matiere objects
	 */
	public function countMatieres($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collMatieres || null !== $criteria) {
			if ($this->isNew() && null === $this->collMatieres) {
				return 0;
			} else {
				$query = MatiereQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByProfesseur($this)
					->count($con);
			}
		} else {
			return count($this->collMatieres);
		}
	}

	/**
	 * Associate a Matiere object to this object
	 * through the j_professeurs_matieres cross reference table.
	 *
	 * @param      Matiere $matiere The JProfesseursMatieres object to relate
	 * @return     void
	 */
	public function addMatiere(Matiere $matiere)
	{
		if ($this->collMatieres === null) {
			$this->initMatieres();
		}
		if (!$this->collMatieres->contains($matiere)) { // only add it if the **same** object is not already associated
			$this->doAddMatiere($matiere);

			$this->collMatieres[]= $matiere;
		}
	}

	/**
	 * @param	Matiere $matiere The matiere object to add.
	 */
	protected function doAddMatiere($matiere)
	{
		$jProfesseursMatieres = new JProfesseursMatieres();
		$jProfesseursMatieres->setMatiere($matiere);
		$this->addJProfesseursMatieres($jProfesseursMatieres);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->login = null;
		$this->nom = null;
		$this->prenom = null;
		$this->civilite = null;
		$this->password = null;
		$this->salt = null;
		$this->email = null;
		$this->show_email = null;
		$this->statut = null;
		$this->etat = null;
		$this->change_mdp = null;
		$this->date_verrouillage = null;
		$this->password_ticket = null;
		$this->ticket_expiration = null;
		$this->niveau_alerte = null;
		$this->observation_securite = null;
		$this->temp_dir = null;
		$this->numind = null;
		$this->auth_mode = null;
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
			if ($this->collJGroupesProfesseurss) {
				foreach ($this->collJGroupesProfesseurss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJScolClassess) {
				foreach ($this->collJScolClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteCompteRendus) {
				foreach ($this->collCahierTexteCompteRendus as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteTravailAFaires) {
				foreach ($this->collCahierTexteTravailAFaires as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCahierTexteNoticePrivees) {
				foreach ($this->collCahierTexteNoticePrivees as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveCpes) {
				foreach ($this->collJEleveCpes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveProfesseurPrincipals) {
				foreach ($this->collJEleveProfesseurPrincipals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidUtilisateursProfessionnelss) {
				foreach ($this->collJAidUtilisateursProfessionnelss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ($this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ($this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collModifiedAbsenceEleveTraitements) {
				foreach ($this->collModifiedAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ($this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJProfesseursMatieress) {
				foreach ($this->collJProfesseursMatieress as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collPreferenceUtilisateurProfessionnels) {
				foreach ($this->collPreferenceUtilisateurProfessionnels as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEdtEmplacementCourss) {
				foreach ($this->collEdtEmplacementCourss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collGroupes) {
				foreach ($this->collGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAidDetailss) {
				foreach ($this->collAidDetailss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collMatieres) {
				foreach ($this->collMatieres as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJGroupesProfesseurss instanceof PropelCollection) {
			$this->collJGroupesProfesseurss->clearIterator();
		}
		$this->collJGroupesProfesseurss = null;
		if ($this->collJScolClassess instanceof PropelCollection) {
			$this->collJScolClassess->clearIterator();
		}
		$this->collJScolClassess = null;
		if ($this->collCahierTexteCompteRendus instanceof PropelCollection) {
			$this->collCahierTexteCompteRendus->clearIterator();
		}
		$this->collCahierTexteCompteRendus = null;
		if ($this->collCahierTexteTravailAFaires instanceof PropelCollection) {
			$this->collCahierTexteTravailAFaires->clearIterator();
		}
		$this->collCahierTexteTravailAFaires = null;
		if ($this->collCahierTexteNoticePrivees instanceof PropelCollection) {
			$this->collCahierTexteNoticePrivees->clearIterator();
		}
		$this->collCahierTexteNoticePrivees = null;
		if ($this->collJEleveCpes instanceof PropelCollection) {
			$this->collJEleveCpes->clearIterator();
		}
		$this->collJEleveCpes = null;
		if ($this->collJEleveProfesseurPrincipals instanceof PropelCollection) {
			$this->collJEleveProfesseurPrincipals->clearIterator();
		}
		$this->collJEleveProfesseurPrincipals = null;
		if ($this->collJAidUtilisateursProfessionnelss instanceof PropelCollection) {
			$this->collJAidUtilisateursProfessionnelss->clearIterator();
		}
		$this->collJAidUtilisateursProfessionnelss = null;
		if ($this->collAbsenceEleveSaisies instanceof PropelCollection) {
			$this->collAbsenceEleveSaisies->clearIterator();
		}
		$this->collAbsenceEleveSaisies = null;
		if ($this->collAbsenceEleveTraitements instanceof PropelCollection) {
			$this->collAbsenceEleveTraitements->clearIterator();
		}
		$this->collAbsenceEleveTraitements = null;
		if ($this->collModifiedAbsenceEleveTraitements instanceof PropelCollection) {
			$this->collModifiedAbsenceEleveTraitements->clearIterator();
		}
		$this->collModifiedAbsenceEleveTraitements = null;
		if ($this->collAbsenceEleveNotifications instanceof PropelCollection) {
			$this->collAbsenceEleveNotifications->clearIterator();
		}
		$this->collAbsenceEleveNotifications = null;
		if ($this->collJProfesseursMatieress instanceof PropelCollection) {
			$this->collJProfesseursMatieress->clearIterator();
		}
		$this->collJProfesseursMatieress = null;
		if ($this->collPreferenceUtilisateurProfessionnels instanceof PropelCollection) {
			$this->collPreferenceUtilisateurProfessionnels->clearIterator();
		}
		$this->collPreferenceUtilisateurProfessionnels = null;
		if ($this->collEdtEmplacementCourss instanceof PropelCollection) {
			$this->collEdtEmplacementCourss->clearIterator();
		}
		$this->collEdtEmplacementCourss = null;
		if ($this->collGroupes instanceof PropelCollection) {
			$this->collGroupes->clearIterator();
		}
		$this->collGroupes = null;
		if ($this->collAidDetailss instanceof PropelCollection) {
			$this->collAidDetailss->clearIterator();
		}
		$this->collAidDetailss = null;
		if ($this->collMatieres instanceof PropelCollection) {
			$this->collMatieres->clearIterator();
		}
		$this->collMatieres = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(UtilisateurProfessionnelPeer::DEFAULT_STRING_FORMAT);
	}

} // BaseUtilisateurProfessionnel
