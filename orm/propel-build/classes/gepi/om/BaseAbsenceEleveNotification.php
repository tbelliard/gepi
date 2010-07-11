<?php


/**
 * Base class that represents a row from the 'a_notifications' table.
 *
 * Notification (a la famille) des absences
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveNotification extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'AbsenceEleveNotificationPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveNotificationPeer
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
	 * The value for the a_traitement_id field.
	 * @var        int
	 */
	protected $a_traitement_id;

	/**
	 * The value for the type_notification field.
	 * @var        int
	 */
	protected $type_notification;

	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the telephone field.
	 * @var        string
	 */
	protected $telephone;

	/**
	 * The value for the adr_id field.
	 * @var        string
	 */
	protected $adr_id;

	/**
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the statut_envoi field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $statut_envoi;

	/**
	 * The value for the date_envoi field.
	 * @var        string
	 */
	protected $date_envoi;

	/**
	 * The value for the erreur_message_envoi field.
	 * @var        string
	 */
	protected $erreur_message_envoi;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        AbsenceEleveTraitement
	 */
	protected $aAbsenceEleveTraitement;

	/**
	 * @var        ResponsableEleveAdresse
	 */
	protected $aResponsableEleveAdresse;

	/**
	 * @var        array JNotificationResponsableEleve[] Collection to store aggregation of JNotificationResponsableEleve objects.
	 */
	protected $collJNotificationResponsableEleves;

	/**
	 * @var        array ResponsableEleve[] Collection to store aggregation of ResponsableEleve objects.
	 */
	protected $collResponsableEleves;

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
		$this->statut_envoi = 0;
	}

	/**
	 * Initializes internal state of BaseAbsenceEleveNotification object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [utilisateur_id] column value.
	 * Login de l'utilisateur professionnel qui envoi la notification
	 * @return     string
	 */
	public function getUtilisateurId()
	{
		return $this->utilisateur_id;
	}

	/**
	 * Get the [a_traitement_id] column value.
	 * cle etrangere du traitement qu'on notifie
	 * @return     int
	 */
	public function getATraitementId()
	{
		return $this->a_traitement_id;
	}

	/**
	 * Get the [type_notification] column value.
	 * type de notification (0 : email, 1 : courrier, 2 : sms)
	 * @return     int
	 */
	public function getTypeNotification()
	{
		return $this->type_notification;
	}

	/**
	 * Get the [email] column value.
	 * email de destination (pour le type email)
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [telephone] column value.
	 * numero du telephone de destination (pour le type sms)
	 * @return     string
	 */
	public function getTelephone()
	{
		return $this->telephone;
	}

	/**
	 * Get the [adr_id] column value.
	 * cle etrangere vers l'adresse de destination (pour le type courrier)
	 * @return     string
	 */
	public function getAdrId()
	{
		return $this->adr_id;
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
	 * Get the [statut_envoi] column value.
	 * Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)
	 * @return     int
	 */
	public function getStatutEnvoi()
	{
		return $this->statut_envoi;
	}

	/**
	 * Get the [optionally formatted] temporal [date_envoi] column value.
	 * Date envoi
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateEnvoi($format = 'Y-m-d H:i:s')
	{
		if ($this->date_envoi === null) {
			return null;
		}


		if ($this->date_envoi === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_envoi);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_envoi, true), $x);
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
	 * Get the [erreur_message_envoi] column value.
	 * Message d'erreur retourné par le service d'envoi
	 * @return     string
	 */
	public function getErreurMessageEnvoi()
	{
		return $this->erreur_message_envoi;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
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
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui envoi la notification
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::UTILISATEUR_ID;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [a_traitement_id] column.
	 * cle etrangere du traitement qu'on notifie
	 * @param      int $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setATraitementId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->a_traitement_id !== $v) {
			$this->a_traitement_id = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::A_TRAITEMENT_ID;
		}

		if ($this->aAbsenceEleveTraitement !== null && $this->aAbsenceEleveTraitement->getId() !== $v) {
			$this->aAbsenceEleveTraitement = null;
		}

		return $this;
	} // setATraitementId()

	/**
	 * Set the value of [type_notification] column.
	 * type de notification (0 : email, 1 : courrier, 2 : sms)
	 * @param      int $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setTypeNotification($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type_notification !== $v) {
			$this->type_notification = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::TYPE_NOTIFICATION;
		}

		return $this;
	} // setTypeNotification()

	/**
	 * Set the value of [email] column.
	 * email de destination (pour le type email)
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [telephone] column.
	 * numero du telephone de destination (pour le type sms)
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setTelephone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->telephone !== $v) {
			$this->telephone = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::TELEPHONE;
		}

		return $this;
	} // setTelephone()

	/**
	 * Set the value of [adr_id] column.
	 * cle etrangere vers l'adresse de destination (pour le type courrier)
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setAdrId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adr_id !== $v) {
			$this->adr_id = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::ADR_ID;
		}

		if ($this->aResponsableEleveAdresse !== null && $this->aResponsableEleveAdresse->getAdrId() !== $v) {
			$this->aResponsableEleveAdresse = null;
		}

		return $this;
	} // setAdrId()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire saisi par l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Set the value of [statut_envoi] column.
	 * Statut de cet envoi (0 : etat initial, 1 : en cours, 2 : echec, 3 : succes, 4 : succes avec accuse de reception)
	 * @param      int $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setStatutEnvoi($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->statut_envoi !== $v || $this->isNew()) {
			$this->statut_envoi = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::STATUT_ENVOI;
		}

		return $this;
	} // setStatutEnvoi()

	/**
	 * Sets the value of [date_envoi] column to a normalized version of the date/time value specified.
	 * Date envoi
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setDateEnvoi($v)
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

		if ( $this->date_envoi !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->date_envoi !== null && $tmpDt = new DateTime($this->date_envoi)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->date_envoi = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveNotificationPeer::DATE_ENVOI;
			}
		} // if either are not null

		return $this;
	} // setDateEnvoi()

	/**
	 * Set the value of [erreur_message_envoi] column.
	 * Message d'erreur retourné par le service d'envoi
	 * @param      string $v new value
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setErreurMessageEnvoi($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->erreur_message_envoi !== $v) {
			$this->erreur_message_envoi = $v;
			$this->modifiedColumns[] = AbsenceEleveNotificationPeer::ERREUR_MESSAGE_ENVOI;
		}

		return $this;
	} // setErreurMessageEnvoi()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
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

		if ( $this->created_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->created_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveNotificationPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
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

		if ( $this->updated_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->updated_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = AbsenceEleveNotificationPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

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
			if ($this->statut_envoi !== 0) {
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
			$this->a_traitement_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->type_notification = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->email = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->telephone = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->adr_id = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->commentaire = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->statut_envoi = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->date_envoi = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->erreur_message_envoi = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 13; // 13 = AbsenceEleveNotificationPeer::NUM_COLUMNS - AbsenceEleveNotificationPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveNotification object", $e);
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
		if ($this->aAbsenceEleveTraitement !== null && $this->a_traitement_id !== $this->aAbsenceEleveTraitement->getId()) {
			$this->aAbsenceEleveTraitement = null;
		}
		if ($this->aResponsableEleveAdresse !== null && $this->adr_id !== $this->aResponsableEleveAdresse->getAdrId()) {
			$this->aResponsableEleveAdresse = null;
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
			$con = Propel::getConnection(AbsenceEleveNotificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveNotificationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aUtilisateurProfessionnel = null;
			$this->aAbsenceEleveTraitement = null;
			$this->aResponsableEleveAdresse = null;
			$this->collJNotificationResponsableEleves = null;

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
			$con = Propel::getConnection(AbsenceEleveNotificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AbsenceEleveNotificationQuery::create()
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
			$con = Propel::getConnection(AbsenceEleveNotificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(AbsenceEleveNotificationPeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(AbsenceEleveNotificationPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(AbsenceEleveNotificationPeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				AbsenceEleveNotificationPeer::addInstanceToPool($this);
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

			if ($this->aAbsenceEleveTraitement !== null) {
				if ($this->aAbsenceEleveTraitement->isModified() || $this->aAbsenceEleveTraitement->isNew()) {
					$affectedRows += $this->aAbsenceEleveTraitement->save($con);
				}
				$this->setAbsenceEleveTraitement($this->aAbsenceEleveTraitement);
			}

			if ($this->aResponsableEleveAdresse !== null) {
				if ($this->aResponsableEleveAdresse->isModified() || $this->aResponsableEleveAdresse->isNew()) {
					$affectedRows += $this->aResponsableEleveAdresse->save($con);
				}
				$this->setResponsableEleveAdresse($this->aResponsableEleveAdresse);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceEleveNotificationPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(AbsenceEleveNotificationPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveNotificationPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveNotificationPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJNotificationResponsableEleves !== null) {
				foreach ($this->collJNotificationResponsableEleves as $referrerFK) {
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

			if ($this->aAbsenceEleveTraitement !== null) {
				if (!$this->aAbsenceEleveTraitement->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveTraitement->getValidationFailures());
				}
			}

			if ($this->aResponsableEleveAdresse !== null) {
				if (!$this->aResponsableEleveAdresse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aResponsableEleveAdresse->getValidationFailures());
				}
			}


			if (($retval = AbsenceEleveNotificationPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJNotificationResponsableEleves !== null) {
					foreach ($this->collJNotificationResponsableEleves as $referrerFK) {
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
		$pos = AbsenceEleveNotificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getATraitementId();
				break;
			case 3:
				return $this->getTypeNotification();
				break;
			case 4:
				return $this->getEmail();
				break;
			case 5:
				return $this->getTelephone();
				break;
			case 6:
				return $this->getAdrId();
				break;
			case 7:
				return $this->getCommentaire();
				break;
			case 8:
				return $this->getStatutEnvoi();
				break;
			case 9:
				return $this->getDateEnvoi();
				break;
			case 10:
				return $this->getErreurMessageEnvoi();
				break;
			case 11:
				return $this->getCreatedAt();
				break;
			case 12:
				return $this->getUpdatedAt();
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
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = AbsenceEleveNotificationPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getATraitementId(),
			$keys[3] => $this->getTypeNotification(),
			$keys[4] => $this->getEmail(),
			$keys[5] => $this->getTelephone(),
			$keys[6] => $this->getAdrId(),
			$keys[7] => $this->getCommentaire(),
			$keys[8] => $this->getStatutEnvoi(),
			$keys[9] => $this->getDateEnvoi(),
			$keys[10] => $this->getErreurMessageEnvoi(),
			$keys[11] => $this->getCreatedAt(),
			$keys[12] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aUtilisateurProfessionnel) {
				$result['UtilisateurProfessionnel'] = $this->aUtilisateurProfessionnel->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aAbsenceEleveTraitement) {
				$result['AbsenceEleveTraitement'] = $this->aAbsenceEleveTraitement->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aResponsableEleveAdresse) {
				$result['ResponsableEleveAdresse'] = $this->aResponsableEleveAdresse->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = AbsenceEleveNotificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setATraitementId($value);
				break;
			case 3:
				$this->setTypeNotification($value);
				break;
			case 4:
				$this->setEmail($value);
				break;
			case 5:
				$this->setTelephone($value);
				break;
			case 6:
				$this->setAdrId($value);
				break;
			case 7:
				$this->setCommentaire($value);
				break;
			case 8:
				$this->setStatutEnvoi($value);
				break;
			case 9:
				$this->setDateEnvoi($value);
				break;
			case 10:
				$this->setErreurMessageEnvoi($value);
				break;
			case 11:
				$this->setCreatedAt($value);
				break;
			case 12:
				$this->setUpdatedAt($value);
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
		$keys = AbsenceEleveNotificationPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setATraitementId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTypeNotification($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setEmail($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTelephone($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAdrId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCommentaire($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStatutEnvoi($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDateEnvoi($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setErreurMessageEnvoi($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveNotificationPeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveNotificationPeer::ID)) $criteria->add(AbsenceEleveNotificationPeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::UTILISATEUR_ID)) $criteria->add(AbsenceEleveNotificationPeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID)) $criteria->add(AbsenceEleveNotificationPeer::A_TRAITEMENT_ID, $this->a_traitement_id);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION)) $criteria->add(AbsenceEleveNotificationPeer::TYPE_NOTIFICATION, $this->type_notification);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::EMAIL)) $criteria->add(AbsenceEleveNotificationPeer::EMAIL, $this->email);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::TELEPHONE)) $criteria->add(AbsenceEleveNotificationPeer::TELEPHONE, $this->telephone);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::ADR_ID)) $criteria->add(AbsenceEleveNotificationPeer::ADR_ID, $this->adr_id);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::COMMENTAIRE)) $criteria->add(AbsenceEleveNotificationPeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::STATUT_ENVOI)) $criteria->add(AbsenceEleveNotificationPeer::STATUT_ENVOI, $this->statut_envoi);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::DATE_ENVOI)) $criteria->add(AbsenceEleveNotificationPeer::DATE_ENVOI, $this->date_envoi);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::ERREUR_MESSAGE_ENVOI)) $criteria->add(AbsenceEleveNotificationPeer::ERREUR_MESSAGE_ENVOI, $this->erreur_message_envoi);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::CREATED_AT)) $criteria->add(AbsenceEleveNotificationPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceEleveNotificationPeer::UPDATED_AT)) $criteria->add(AbsenceEleveNotificationPeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(AbsenceEleveNotificationPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveNotificationPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveNotification (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setUtilisateurId($this->utilisateur_id);
		$copyObj->setATraitementId($this->a_traitement_id);
		$copyObj->setTypeNotification($this->type_notification);
		$copyObj->setEmail($this->email);
		$copyObj->setTelephone($this->telephone);
		$copyObj->setAdrId($this->adr_id);
		$copyObj->setCommentaire($this->commentaire);
		$copyObj->setStatutEnvoi($this->statut_envoi);
		$copyObj->setDateEnvoi($this->date_envoi);
		$copyObj->setErreurMessageEnvoi($this->erreur_message_envoi);
		$copyObj->setCreatedAt($this->created_at);
		$copyObj->setUpdatedAt($this->updated_at);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJNotificationResponsableEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJNotificationResponsableEleve($relObj->copy($deepCopy));
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
	 * @return     AbsenceEleveNotification Clone of current object.
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
	 * @return     AbsenceEleveNotificationPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveNotificationPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
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
			$v->addAbsenceEleveNotification($this);
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
			$this->aUtilisateurProfessionnel = UtilisateurProfessionnelQuery::create()->findPk($this->utilisateur_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aUtilisateurProfessionnel->addAbsenceEleveNotifications($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveTraitement object.
	 *
	 * @param      AbsenceEleveTraitement $v
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveTraitement(AbsenceEleveTraitement $v = null)
	{
		if ($v === null) {
			$this->setATraitementId(NULL);
		} else {
			$this->setATraitementId($v->getId());
		}

		$this->aAbsenceEleveTraitement = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveTraitement object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveNotification($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveTraitement object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveTraitement The associated AbsenceEleveTraitement object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveTraitement(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveTraitement === null && ($this->a_traitement_id !== null)) {
			$this->aAbsenceEleveTraitement = AbsenceEleveTraitementQuery::create()->findPk($this->a_traitement_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAbsenceEleveTraitement->addAbsenceEleveNotifications($this);
			 */
		}
		return $this->aAbsenceEleveTraitement;
	}

	/**
	 * Declares an association between this object and a ResponsableEleveAdresse object.
	 *
	 * @param      ResponsableEleveAdresse $v
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setResponsableEleveAdresse(ResponsableEleveAdresse $v = null)
	{
		if ($v === null) {
			$this->setAdrId(NULL);
		} else {
			$this->setAdrId($v->getAdrId());
		}

		$this->aResponsableEleveAdresse = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the ResponsableEleveAdresse object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveNotification($this);
		}

		return $this;
	}


	/**
	 * Get the associated ResponsableEleveAdresse object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     ResponsableEleveAdresse The associated ResponsableEleveAdresse object.
	 * @throws     PropelException
	 */
	public function getResponsableEleveAdresse(PropelPDO $con = null)
	{
		if ($this->aResponsableEleveAdresse === null && (($this->adr_id !== "" && $this->adr_id !== null))) {
			$this->aResponsableEleveAdresse = ResponsableEleveAdresseQuery::create()->findPk($this->adr_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aResponsableEleveAdresse->addAbsenceEleveNotifications($this);
			 */
		}
		return $this->aResponsableEleveAdresse;
	}

	/**
	 * Clears out the collJNotificationResponsableEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJNotificationResponsableEleves()
	 */
	public function clearJNotificationResponsableEleves()
	{
		$this->collJNotificationResponsableEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJNotificationResponsableEleves collection.
	 *
	 * By default this just sets the collJNotificationResponsableEleves collection to an empty array (like clearcollJNotificationResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJNotificationResponsableEleves()
	{
		$this->collJNotificationResponsableEleves = new PropelObjectCollection();
		$this->collJNotificationResponsableEleves->setModel('JNotificationResponsableEleve');
	}

	/**
	 * Gets an array of JNotificationResponsableEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveNotification is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JNotificationResponsableEleve[] List of JNotificationResponsableEleve objects
	 * @throws     PropelException
	 */
	public function getJNotificationResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJNotificationResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJNotificationResponsableEleves) {
				// return empty collection
				$this->initJNotificationResponsableEleves();
			} else {
				$collJNotificationResponsableEleves = JNotificationResponsableEleveQuery::create(null, $criteria)
					->filterByAbsenceEleveNotification($this)
					->find($con);
				if (null !== $criteria) {
					return $collJNotificationResponsableEleves;
				}
				$this->collJNotificationResponsableEleves = $collJNotificationResponsableEleves;
			}
		}
		return $this->collJNotificationResponsableEleves;
	}

	/**
	 * Returns the number of related JNotificationResponsableEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JNotificationResponsableEleve objects.
	 * @throws     PropelException
	 */
	public function countJNotificationResponsableEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJNotificationResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJNotificationResponsableEleves) {
				return 0;
			} else {
				$query = JNotificationResponsableEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveNotification($this)
					->count($con);
			}
		} else {
			return count($this->collJNotificationResponsableEleves);
		}
	}

	/**
	 * Method called to associate a JNotificationResponsableEleve object to this object
	 * through the JNotificationResponsableEleve foreign key attribute.
	 *
	 * @param      JNotificationResponsableEleve $l JNotificationResponsableEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJNotificationResponsableEleve(JNotificationResponsableEleve $l)
	{
		if ($this->collJNotificationResponsableEleves === null) {
			$this->initJNotificationResponsableEleves();
		}
		if (!$this->collJNotificationResponsableEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJNotificationResponsableEleves[]= $l;
			$l->setAbsenceEleveNotification($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveNotification is new, it will return
	 * an empty collection; or if this AbsenceEleveNotification has previously
	 * been saved, it will retrieve related JNotificationResponsableEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveNotification.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JNotificationResponsableEleve[] List of JNotificationResponsableEleve objects
	 */
	public function getJNotificationResponsableElevesJoinResponsableEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JNotificationResponsableEleveQuery::create(null, $criteria);
		$query->joinWith('ResponsableEleve', $join_behavior);

		return $this->getJNotificationResponsableEleves($query, $con);
	}

	/**
	 * Clears out the collResponsableEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addResponsableEleves()
	 */
	public function clearResponsableEleves()
	{
		$this->collResponsableEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collResponsableEleves collection.
	 *
	 * By default this just sets the collResponsableEleves collection to an empty collection (like clearResponsableEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initResponsableEleves()
	{
		$this->collResponsableEleves = new PropelObjectCollection();
		$this->collResponsableEleves->setModel('ResponsableEleve');
	}

	/**
	 * Gets a collection of ResponsableEleve objects related by a many-to-many relationship
	 * to the current object by way of the j_notifications_resp_pers cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveNotification is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array ResponsableEleve[] List of ResponsableEleve objects
	 */
	public function getResponsableEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				// return empty collection
				$this->initResponsableEleves();
			} else {
				$collResponsableEleves = ResponsableEleveQuery::create(null, $criteria)
					->filterByAbsenceEleveNotification($this)
					->find($con);
				if (null !== $criteria) {
					return $collResponsableEleves;
				}
				$this->collResponsableEleves = $collResponsableEleves;
			}
		}
		return $this->collResponsableEleves;
	}

	/**
	 * Gets the number of ResponsableEleve objects related by a many-to-many relationship
	 * to the current object by way of the j_notifications_resp_pers cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related ResponsableEleve objects
	 */
	public function countResponsableEleves($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collResponsableEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collResponsableEleves) {
				return 0;
			} else {
				$query = ResponsableEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveNotification($this)
					->count($con);
			}
		} else {
			return count($this->collResponsableEleves);
		}
	}

	/**
	 * Associate a ResponsableEleve object to this object
	 * through the j_notifications_resp_pers cross reference table.
	 *
	 * @param      ResponsableEleve $responsableEleve The JNotificationResponsableEleve object to relate
	 * @return     void
	 */
	public function addResponsableEleve($responsableEleve)
	{
		if ($this->collResponsableEleves === null) {
			$this->initResponsableEleves();
		}
		if (!$this->collResponsableEleves->contains($responsableEleve)) { // only add it if the **same** object is not already associated
			$jNotificationResponsableEleve = new JNotificationResponsableEleve();
			$jNotificationResponsableEleve->setResponsableEleve($responsableEleve);
			$this->addJNotificationResponsableEleve($jNotificationResponsableEleve);
			
			$this->collResponsableEleves[]= $responsableEleve;
		}
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->utilisateur_id = null;
		$this->a_traitement_id = null;
		$this->type_notification = null;
		$this->email = null;
		$this->telephone = null;
		$this->adr_id = null;
		$this->commentaire = null;
		$this->statut_envoi = null;
		$this->date_envoi = null;
		$this->erreur_message_envoi = null;
		$this->created_at = null;
		$this->updated_at = null;
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
			if ($this->collJNotificationResponsableEleves) {
				foreach ((array) $this->collJNotificationResponsableEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJNotificationResponsableEleves = null;
		$this->aUtilisateurProfessionnel = null;
		$this->aAbsenceEleveTraitement = null;
		$this->aResponsableEleveAdresse = null;
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     AbsenceEleveNotification The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = AbsenceEleveNotificationPeer::UPDATED_AT;
		return $this;
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

} // BaseAbsenceEleveNotification
