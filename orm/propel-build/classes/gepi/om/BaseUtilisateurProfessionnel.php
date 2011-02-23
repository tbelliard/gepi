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
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collModifiedAbsenceEleveSaisies;

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

		if ($this->show_email !== $v || $this->isNew()) {
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

		if ($this->change_mdp !== $v || $this->isNew()) {
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

			$currNorm = ($this->date_verrouillage !== null && $tmpDt = new DateTime($this->date_verrouillage)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('Y-m-d') === '2006-01-01') // or the entered value matches the default
					)
			{
				$this->date_verrouillage = ($dt ? $dt->format('Y-m-d') : null);
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

			$currNorm = ($this->ticket_expiration !== null && $tmpDt = new DateTime($this->ticket_expiration)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->ticket_expiration = ($dt ? $dt->format('Y-m-d') : null);
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

		if ($this->niveau_alerte !== $v || $this->isNew()) {
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

		if ($this->observation_securite !== $v || $this->isNew()) {
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

		if ($this->auth_mode !== $v || $this->isNew()) {
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

			$this->collJScolClassess = null;

			$this->collCahierTexteCompteRendus = null;

			$this->collCahierTexteTravailAFaires = null;

			$this->collCahierTexteNoticePrivees = null;

			$this->collJEleveCpes = null;

			$this->collJEleveProfesseurPrincipals = null;

			$this->collJAidUtilisateursProfessionnelss = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collModifiedAbsenceEleveSaisies = null;

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
			$ret = $this->preDelete($con);
			if ($ret) {
				UtilisateurProfessionnelQuery::create()
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
					$affectedRows = UtilisateurProfessionnelPeer::doUpdate($this, $con);
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

			if ($this->collJScolClassess !== null) {
				foreach ($this->collJScolClassess as $referrerFK) {
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

			if ($this->collModifiedAbsenceEleveSaisies !== null) {
				foreach ($this->collModifiedAbsenceEleveSaisies as $referrerFK) {
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

			if ($this->collModifiedAbsenceEleveTraitements !== null) {
				foreach ($this->collModifiedAbsenceEleveTraitements as $referrerFK) {
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

			if ($this->collJProfesseursMatieress !== null) {
				foreach ($this->collJProfesseursMatieress as $referrerFK) {
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

				if ($this->collModifiedAbsenceEleveSaisies !== null) {
					foreach ($this->collModifiedAbsenceEleveSaisies as $referrerFK) {
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
	 * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 *                    Defaults to BasePeer::TYPE_PHPNAME.
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
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

			foreach ($this->getModifiedAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addModifiedAbsenceEleveSaisie($relObj->copy($deepCopy));
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
	 * @return     void
	 */
	public function initJGroupesProfesseurss()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesProfesseurs(JGroupesProfesseurs $l)
	{
		if ($this->collJGroupesProfesseurss === null) {
			$this->initJGroupesProfesseurss();
		}
		if (!$this->collJGroupesProfesseurss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJGroupesProfesseurss[]= $l;
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
	 * @return     void
	 */
	public function initJScolClassess()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJScolClasses(JScolClasses $l)
	{
		if ($this->collJScolClassess === null) {
			$this->initJScolClassess();
		}
		if (!$this->collJScolClassess->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJScolClassess[]= $l;
			$l->setUtilisateurProfessionnel($this);
		}
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
	 * @return     void
	 */
	public function initCahierTexteCompteRendus()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteCompteRendu(CahierTexteCompteRendu $l)
	{
		if ($this->collCahierTexteCompteRendus === null) {
			$this->initCahierTexteCompteRendus();
		}
		if (!$this->collCahierTexteCompteRendus->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCahierTexteCompteRendus[]= $l;
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
	 * @return     void
	 */
	public function initCahierTexteTravailAFaires()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteTravailAFaire(CahierTexteTravailAFaire $l)
	{
		if ($this->collCahierTexteTravailAFaires === null) {
			$this->initCahierTexteTravailAFaires();
		}
		if (!$this->collCahierTexteTravailAFaires->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCahierTexteTravailAFaires[]= $l;
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
	 * @return     void
	 */
	public function initCahierTexteNoticePrivees()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCahierTexteNoticePrivee(CahierTexteNoticePrivee $l)
	{
		if ($this->collCahierTexteNoticePrivees === null) {
			$this->initCahierTexteNoticePrivees();
		}
		if (!$this->collCahierTexteNoticePrivees->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCahierTexteNoticePrivees[]= $l;
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
	public function getJEleveProfesseurPrincipalsJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JEleveProfesseurPrincipalQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

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
	 * @return     void
	 */
	public function initJAidUtilisateursProfessionnelss()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJAidUtilisateursProfessionnels(JAidUtilisateursProfessionnels $l)
	{
		if ($this->collJAidUtilisateursProfessionnelss === null) {
			$this->initJAidUtilisateursProfessionnelss();
		}
		if (!$this->collJAidUtilisateursProfessionnelss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJAidUtilisateursProfessionnelss[]= $l;
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
	 * Clears out the collModifiedAbsenceEleveSaisies collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addModifiedAbsenceEleveSaisies()
	 */
	public function clearModifiedAbsenceEleveSaisies()
	{
		$this->collModifiedAbsenceEleveSaisies = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collModifiedAbsenceEleveSaisies collection.
	 *
	 * By default this just sets the collModifiedAbsenceEleveSaisies collection to an empty array (like clearcollModifiedAbsenceEleveSaisies());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initModifiedAbsenceEleveSaisies()
	{
		$this->collModifiedAbsenceEleveSaisies = new PropelObjectCollection();
		$this->collModifiedAbsenceEleveSaisies->setModel('AbsenceEleveSaisie');
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
	public function getModifiedAbsenceEleveSaisies($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collModifiedAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collModifiedAbsenceEleveSaisies) {
				// return empty collection
				$this->initModifiedAbsenceEleveSaisies();
			} else {
				$collModifiedAbsenceEleveSaisies = AbsenceEleveSaisieQuery::create(null, $criteria)
					->filterByModifieParUtilisateur($this)
					->find($con);
				if (null !== $criteria) {
					return $collModifiedAbsenceEleveSaisies;
				}
				$this->collModifiedAbsenceEleveSaisies = $collModifiedAbsenceEleveSaisies;
			}
		}
		return $this->collModifiedAbsenceEleveSaisies;
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
	public function countModifiedAbsenceEleveSaisies(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collModifiedAbsenceEleveSaisies || null !== $criteria) {
			if ($this->isNew() && null === $this->collModifiedAbsenceEleveSaisies) {
				return 0;
			} else {
				$query = AbsenceEleveSaisieQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByModifieParUtilisateur($this)
					->count($con);
			}
		} else {
			return count($this->collModifiedAbsenceEleveSaisies);
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
	public function addModifiedAbsenceEleveSaisie(AbsenceEleveSaisie $l)
	{
		if ($this->collModifiedAbsenceEleveSaisies === null) {
			$this->initModifiedAbsenceEleveSaisies();
		}
		if (!$this->collModifiedAbsenceEleveSaisies->contains($l)) { // only add it if the **same** object is not already associated
			$this->collModifiedAbsenceEleveSaisies[]= $l;
			$l->setModifieParUtilisateur($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinEdtCreneau($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtCreneau', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinEdtEmplacementCours($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('EdtEmplacementCours', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Groupe', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinClasse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('Classe', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinAidDetails($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AidDetails', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this UtilisateurProfessionnel is new, it will return
	 * an empty collection; or if this UtilisateurProfessionnel has previously
	 * been saved, it will retrieve related ModifiedAbsenceEleveSaisies from storage.
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
	public function getModifiedAbsenceEleveSaisiesJoinAbsenceEleveLieu($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveLieu', $join_behavior);

		return $this->getModifiedAbsenceEleveSaisies($query, $con);
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
	 * @return     void
	 */
	public function initModifiedAbsenceEleveTraitements()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addModifiedAbsenceEleveTraitement(AbsenceEleveTraitement $l)
	{
		if ($this->collModifiedAbsenceEleveTraitements === null) {
			$this->initModifiedAbsenceEleveTraitements();
		}
		if (!$this->collModifiedAbsenceEleveTraitements->contains($l)) { // only add it if the **same** object is not already associated
			$this->collModifiedAbsenceEleveTraitements[]= $l;
			$l->setModifieParUtilisateur($this);
		}
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
			$l->setUtilisateurProfessionnel($this);
		}
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
	public function getAbsenceEleveNotificationsJoinResponsableEleveAdresse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveNotificationQuery::create(null, $criteria);
		$query->joinWith('ResponsableEleveAdresse', $join_behavior);

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
	 * @return     void
	 */
	public function initJProfesseursMatieress()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJProfesseursMatieres(JProfesseursMatieres $l)
	{
		if ($this->collJProfesseursMatieress === null) {
			$this->initJProfesseursMatieress();
		}
		if (!$this->collJProfesseursMatieress->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJProfesseursMatieress[]= $l;
			$l->setProfesseur($this);
		}
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
	 * @return     void
	 */
	public function initPreferenceUtilisateurProfessionnels()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPreferenceUtilisateurProfessionnel(PreferenceUtilisateurProfessionnel $l)
	{
		if ($this->collPreferenceUtilisateurProfessionnels === null) {
			$this->initPreferenceUtilisateurProfessionnels();
		}
		if (!$this->collPreferenceUtilisateurProfessionnels->contains($l)) { // only add it if the **same** object is not already associated
			$this->collPreferenceUtilisateurProfessionnels[]= $l;
			$l->setUtilisateurProfessionnel($this);
		}
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
	 * @return     void
	 */
	public function initEdtEmplacementCourss()
	{
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
	 * @return     void
	 * @throws     PropelException
	 */
	public function addEdtEmplacementCours(EdtEmplacementCours $l)
	{
		if ($this->collEdtEmplacementCourss === null) {
			$this->initEdtEmplacementCourss();
		}
		if (!$this->collEdtEmplacementCourss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collEdtEmplacementCourss[]= $l;
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
	public function addGroupe($groupe)
	{
		if ($this->collGroupes === null) {
			$this->initGroupes();
		}
		if (!$this->collGroupes->contains($groupe)) { // only add it if the **same** object is not already associated
			$jGroupesProfesseurs = new JGroupesProfesseurs();
			$jGroupesProfesseurs->setGroupe($groupe);
			$this->addJGroupesProfesseurs($jGroupesProfesseurs);

			$this->collGroupes[]= $groupe;
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
	public function addAidDetails($aidDetails)
	{
		if ($this->collAidDetailss === null) {
			$this->initAidDetailss();
		}
		if (!$this->collAidDetailss->contains($aidDetails)) { // only add it if the **same** object is not already associated
			$jAidUtilisateursProfessionnels = new JAidUtilisateursProfessionnels();
			$jAidUtilisateursProfessionnels->setAidDetails($aidDetails);
			$this->addJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels);

			$this->collAidDetailss[]= $aidDetails;
		}
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
	public function addMatiere($matiere)
	{
		if ($this->collMatieres === null) {
			$this->initMatieres();
		}
		if (!$this->collMatieres->contains($matiere)) { // only add it if the **same** object is not already associated
			$jProfesseursMatieres = new JProfesseursMatieres();
			$jProfesseursMatieres->setMatiere($matiere);
			$this->addJProfesseursMatieres($jProfesseursMatieres);

			$this->collMatieres[]= $matiere;
		}
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
			if ($this->collJScolClassess) {
				foreach ((array) $this->collJScolClassess as $o) {
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
			if ($this->collModifiedAbsenceEleveSaisies) {
				foreach ((array) $this->collModifiedAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ((array) $this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collModifiedAbsenceEleveTraitements) {
				foreach ((array) $this->collModifiedAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveNotifications) {
				foreach ((array) $this->collAbsenceEleveNotifications as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJProfesseursMatieress) {
				foreach ((array) $this->collJProfesseursMatieress as $o) {
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
		$this->collJScolClassess = null;
		$this->collCahierTexteCompteRendus = null;
		$this->collCahierTexteTravailAFaires = null;
		$this->collCahierTexteNoticePrivees = null;
		$this->collJEleveCpes = null;
		$this->collJEleveProfesseurPrincipals = null;
		$this->collJAidUtilisateursProfessionnelss = null;
		$this->collAbsenceEleveSaisies = null;
		$this->collModifiedAbsenceEleveSaisies = null;
		$this->collAbsenceEleveTraitements = null;
		$this->collModifiedAbsenceEleveTraitements = null;
		$this->collAbsenceEleveNotifications = null;
		$this->collJProfesseursMatieress = null;
		$this->collPreferenceUtilisateurProfessionnels = null;
		$this->collEdtEmplacementCourss = null;
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

} // BaseUtilisateurProfessionnel
