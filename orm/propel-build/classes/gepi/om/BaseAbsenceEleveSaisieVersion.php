<?php


/**
 * Base class that represents a row from the 'a_saisies_version' table.
 *
 * 
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisieVersion extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AbsenceEleveSaisieVersionPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveSaisieVersionPeer
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
	 * The value for the eleve_id field.
	 * @var        int
	 */
	protected $eleve_id;

	/**
	 * The value for the commentaire field.
	 * @var        string
	 */
	protected $commentaire;

	/**
	 * The value for the debut_abs field.
	 * @var        string
	 */
	protected $debut_abs;

	/**
	 * The value for the fin_abs field.
	 * @var        string
	 */
	protected $fin_abs;

	/**
	 * The value for the id_edt_creneau field.
	 * @var        int
	 */
	protected $id_edt_creneau;

	/**
	 * The value for the id_edt_emplacement_cours field.
	 * @var        int
	 */
	protected $id_edt_emplacement_cours;

	/**
	 * The value for the id_groupe field.
	 * @var        int
	 */
	protected $id_groupe;

	/**
	 * The value for the id_classe field.
	 * @var        int
	 */
	protected $id_classe;

	/**
	 * The value for the id_aid field.
	 * @var        int
	 */
	protected $id_aid;

	/**
	 * The value for the id_s_incidents field.
	 * @var        int
	 */
	protected $id_s_incidents;

	/**
	 * The value for the id_lieu field.
	 * @var        int
	 */
	protected $id_lieu;

	/**
	 * The value for the deleted_by field.
	 * @var        string
	 */
	protected $deleted_by;

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
	 * The value for the deleted_at field.
	 * @var        string
	 */
	protected $deleted_at;

	/**
	 * The value for the version field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $version;

	/**
	 * The value for the version_created_at field.
	 * @var        string
	 */
	protected $version_created_at;

	/**
	 * The value for the version_created_by field.
	 * @var        string
	 */
	protected $version_created_by;

	/**
	 * @var        AbsenceEleveSaisie
	 */
	protected $aAbsenceEleveSaisie;

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
		$this->version = 0;
	}

	/**
	 * Initializes internal state of BaseAbsenceEleveSaisieVersion object.
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
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @return     string
	 */
	public function getUtilisateurId()
	{
		return $this->utilisateur_id;
	}

	/**
	 * Get the [eleve_id] column value.
	 * id_eleve de l'eleve objet de la saisie, egal à null si aucun eleve n'est saisi
	 * @return     int
	 */
	public function getEleveId()
	{
		return $this->eleve_id;
	}

	/**
	 * Get the [commentaire] column value.
	 * commentaire de l'utilisateur
	 * @return     string
	 */
	public function getCommentaire()
	{
		return $this->commentaire;
	}

	/**
	 * Get the [optionally formatted] temporal [debut_abs] column value.
	 * Debut de l'absence en timestamp UNIX
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDebutAbs($format = 'Y-m-d H:i:s')
	{
		if ($this->debut_abs === null) {
			return null;
		}


		if ($this->debut_abs === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->debut_abs);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->debut_abs, true), $x);
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
	 * Get the [optionally formatted] temporal [fin_abs] column value.
	 * Fin de l'absence en timestamp UNIX
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getFinAbs($format = 'Y-m-d H:i:s')
	{
		if ($this->fin_abs === null) {
			return null;
		}


		if ($this->fin_abs === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->fin_abs);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fin_abs, true), $x);
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
	 * Get the [id_edt_creneau] column value.
	 * identifiant du creneaux de l'emploi du temps
	 * @return     int
	 */
	public function getIdEdtCreneau()
	{
		return $this->id_edt_creneau;
	}

	/**
	 * Get the [id_edt_emplacement_cours] column value.
	 * identifiant du cours de l'emploi du temps
	 * @return     int
	 */
	public function getIdEdtEmplacementCours()
	{
		return $this->id_edt_emplacement_cours;
	}

	/**
	 * Get the [id_groupe] column value.
	 * identifiant du groupe pour lequel la saisie a ete effectuee
	 * @return     int
	 */
	public function getIdGroupe()
	{
		return $this->id_groupe;
	}

	/**
	 * Get the [id_classe] column value.
	 * identifiant de la classe pour lequel la saisie a ete effectuee
	 * @return     int
	 */
	public function getIdClasse()
	{
		return $this->id_classe;
	}

	/**
	 * Get the [id_aid] column value.
	 * identifiant de l'aid pour lequel la saisie a ete effectuee
	 * @return     int
	 */
	public function getIdAid()
	{
		return $this->id_aid;
	}

	/**
	 * Get the [id_s_incidents] column value.
	 * identifiant de la saisie d'incident discipline
	 * @return     int
	 */
	public function getIdSIncidents()
	{
		return $this->id_s_incidents;
	}

	/**
	 * Get the [id_lieu] column value.
	 * cle etrangere du lieu ou se trouve l'eleve
	 * @return     int
	 */
	public function getIdLieu()
	{
		return $this->id_lieu;
	}

	/**
	 * Get the [deleted_by] column value.
	 * Login de l'utilisateur professionnel qui a supprimé la saisie
	 * @return     string
	 */
	public function getDeletedBy()
	{
		return $this->deleted_by;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * Date de creation de la saisie
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
	 * Date de modification de la saisie, y compris suppression, restauration et changement de version
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
	 * Get the [optionally formatted] temporal [deleted_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->deleted_at === null) {
			return null;
		}


		if ($this->deleted_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->deleted_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->deleted_at, true), $x);
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
	 * Get the [version] column value.
	 * 
	 * @return     int
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Get the [optionally formatted] temporal [version_created_at] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getVersionCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->version_created_at === null) {
			return null;
		}


		if ($this->version_created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->version_created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->version_created_at, true), $x);
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
	 * Get the [version_created_by] column value.
	 * 
	 * @return     string
	 */
	public function getVersionCreatedBy()
	{
		return $this->version_created_by;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID;
		}

		if ($this->aAbsenceEleveSaisie !== null && $this->aAbsenceEleveSaisie->getId() !== $v) {
			$this->aAbsenceEleveSaisie = null;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::UTILISATEUR_ID;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [eleve_id] column.
	 * id_eleve de l'eleve objet de la saisie, egal à null si aucun eleve n'est saisi
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setEleveId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eleve_id !== $v) {
			$this->eleve_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ELEVE_ID;
		}

		return $this;
	} // setEleveId()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire de l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Sets the value of [debut_abs] column to a normalized version of the date/time value specified.
	 * Debut de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setDebutAbs($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->debut_abs !== null || $dt !== null) {
			$currentDateAsString = ($this->debut_abs !== null && $tmpDt = new DateTime($this->debut_abs)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->debut_abs = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::DEBUT_ABS;
			}
		} // if either are not null

		return $this;
	} // setDebutAbs()

	/**
	 * Sets the value of [fin_abs] column to a normalized version of the date/time value specified.
	 * Fin de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setFinAbs($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->fin_abs !== null || $dt !== null) {
			$currentDateAsString = ($this->fin_abs !== null && $tmpDt = new DateTime($this->fin_abs)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->fin_abs = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::FIN_ABS;
			}
		} // if either are not null

		return $this;
	} // setFinAbs()

	/**
	 * Set the value of [id_edt_creneau] column.
	 * identifiant du creneaux de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdEdtCreneau($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_creneau !== $v) {
			$this->id_edt_creneau = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU;
		}

		return $this;
	} // setIdEdtCreneau()

	/**
	 * Set the value of [id_edt_emplacement_cours] column.
	 * identifiant du cours de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdEdtEmplacementCours($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_emplacement_cours !== $v) {
			$this->id_edt_emplacement_cours = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS;
		}

		return $this;
	} // setIdEdtEmplacementCours()

	/**
	 * Set the value of [id_groupe] column.
	 * identifiant du groupe pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_GROUPE;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [id_classe] column.
	 * identifiant de la classe pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdClasse($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_classe !== $v) {
			$this->id_classe = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_CLASSE;
		}

		return $this;
	} // setIdClasse()

	/**
	 * Set the value of [id_aid] column.
	 * identifiant de l'aid pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdAid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_aid !== $v) {
			$this->id_aid = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_AID;
		}

		return $this;
	} // setIdAid()

	/**
	 * Set the value of [id_s_incidents] column.
	 * identifiant de la saisie d'incident discipline
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdSIncidents($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_s_incidents !== $v) {
			$this->id_s_incidents = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS;
		}

		return $this;
	} // setIdSIncidents()

	/**
	 * Set the value of [id_lieu] column.
	 * cle etrangere du lieu ou se trouve l'eleve
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setIdLieu($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_lieu !== $v) {
			$this->id_lieu = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::ID_LIEU;
		}

		return $this;
	} // setIdLieu()

	/**
	 * Set the value of [deleted_by] column.
	 * Login de l'utilisateur professionnel qui a supprimé la saisie
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setDeletedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->deleted_by !== $v) {
			$this->deleted_by = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::DELETED_BY;
		}

		return $this;
	} // setDeletedBy()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * Date de creation de la saisie
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * Date de modification de la saisie, y compris suppression, restauration et changement de version
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->deleted_at !== null || $dt !== null) {
			$currentDateAsString = ($this->deleted_at !== null && $tmpDt = new DateTime($this->deleted_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->deleted_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v || $this->isNew()) {
			$this->version = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setVersionCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->version_created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->version_created_at !== null && $tmpDt = new DateTime($this->version_created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->version_created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setVersionCreatedAt()

	/**
	 * Set the value of [version_created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 */
	public function setVersionCreatedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->version_created_by !== $v) {
			$this->version_created_by = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisieVersionPeer::VERSION_CREATED_BY;
		}

		return $this;
	} // setVersionCreatedBy()

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
			if ($this->version !== 0) {
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
			$this->eleve_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->commentaire = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->debut_abs = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->fin_abs = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->id_edt_creneau = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->id_edt_emplacement_cours = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->id_groupe = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->id_classe = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->id_aid = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->id_s_incidents = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->id_lieu = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->deleted_by = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->created_at = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->updated_at = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->deleted_at = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->version = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->version_created_at = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->version_created_by = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 20; // 20 = AbsenceEleveSaisieVersionPeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveSaisieVersion object", $e);
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

		if ($this->aAbsenceEleveSaisie !== null && $this->id !== $this->aAbsenceEleveSaisie->getId()) {
			$this->aAbsenceEleveSaisie = null;
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
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveSaisieVersionPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aAbsenceEleveSaisie = null;
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
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AbsenceEleveSaisieVersionQuery::create()
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
			$con = Propel::getConnection(AbsenceEleveSaisieVersionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AbsenceEleveSaisieVersionPeer::addInstanceToPool($this);
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

			if ($this->aAbsenceEleveSaisie !== null) {
				if ($this->aAbsenceEleveSaisie->isModified() || $this->aAbsenceEleveSaisie->isNew()) {
					$affectedRows += $this->aAbsenceEleveSaisie->save($con);
				}
				$this->setAbsenceEleveSaisie($this->aAbsenceEleveSaisie);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveSaisieVersionPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
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

			if ($this->aAbsenceEleveSaisie !== null) {
				if (!$this->aAbsenceEleveSaisie->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveSaisie->getValidationFailures());
				}
			}


			if (($retval = AbsenceEleveSaisieVersionPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = AbsenceEleveSaisieVersionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEleveId();
				break;
			case 3:
				return $this->getCommentaire();
				break;
			case 4:
				return $this->getDebutAbs();
				break;
			case 5:
				return $this->getFinAbs();
				break;
			case 6:
				return $this->getIdEdtCreneau();
				break;
			case 7:
				return $this->getIdEdtEmplacementCours();
				break;
			case 8:
				return $this->getIdGroupe();
				break;
			case 9:
				return $this->getIdClasse();
				break;
			case 10:
				return $this->getIdAid();
				break;
			case 11:
				return $this->getIdSIncidents();
				break;
			case 12:
				return $this->getIdLieu();
				break;
			case 13:
				return $this->getDeletedBy();
				break;
			case 14:
				return $this->getCreatedAt();
				break;
			case 15:
				return $this->getUpdatedAt();
				break;
			case 16:
				return $this->getDeletedAt();
				break;
			case 17:
				return $this->getVersion();
				break;
			case 18:
				return $this->getVersionCreatedAt();
				break;
			case 19:
				return $this->getVersionCreatedBy();
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
		if (isset($alreadyDumpedObjects['AbsenceEleveSaisieVersion'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AbsenceEleveSaisieVersion'][serialize($this->getPrimaryKey())] = true;
		$keys = AbsenceEleveSaisieVersionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getUtilisateurId(),
			$keys[2] => $this->getEleveId(),
			$keys[3] => $this->getCommentaire(),
			$keys[4] => $this->getDebutAbs(),
			$keys[5] => $this->getFinAbs(),
			$keys[6] => $this->getIdEdtCreneau(),
			$keys[7] => $this->getIdEdtEmplacementCours(),
			$keys[8] => $this->getIdGroupe(),
			$keys[9] => $this->getIdClasse(),
			$keys[10] => $this->getIdAid(),
			$keys[11] => $this->getIdSIncidents(),
			$keys[12] => $this->getIdLieu(),
			$keys[13] => $this->getDeletedBy(),
			$keys[14] => $this->getCreatedAt(),
			$keys[15] => $this->getUpdatedAt(),
			$keys[16] => $this->getDeletedAt(),
			$keys[17] => $this->getVersion(),
			$keys[18] => $this->getVersionCreatedAt(),
			$keys[19] => $this->getVersionCreatedBy(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aAbsenceEleveSaisie) {
				$result['AbsenceEleveSaisie'] = $this->aAbsenceEleveSaisie->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = AbsenceEleveSaisieVersionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEleveId($value);
				break;
			case 3:
				$this->setCommentaire($value);
				break;
			case 4:
				$this->setDebutAbs($value);
				break;
			case 5:
				$this->setFinAbs($value);
				break;
			case 6:
				$this->setIdEdtCreneau($value);
				break;
			case 7:
				$this->setIdEdtEmplacementCours($value);
				break;
			case 8:
				$this->setIdGroupe($value);
				break;
			case 9:
				$this->setIdClasse($value);
				break;
			case 10:
				$this->setIdAid($value);
				break;
			case 11:
				$this->setIdSIncidents($value);
				break;
			case 12:
				$this->setIdLieu($value);
				break;
			case 13:
				$this->setDeletedBy($value);
				break;
			case 14:
				$this->setCreatedAt($value);
				break;
			case 15:
				$this->setUpdatedAt($value);
				break;
			case 16:
				$this->setDeletedAt($value);
				break;
			case 17:
				$this->setVersion($value);
				break;
			case 18:
				$this->setVersionCreatedAt($value);
				break;
			case 19:
				$this->setVersionCreatedBy($value);
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
		$keys = AbsenceEleveSaisieVersionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setUtilisateurId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setEleveId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setCommentaire($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDebutAbs($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFinAbs($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setIdEdtCreneau($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setIdEdtEmplacementCours($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setIdGroupe($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setIdClasse($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setIdAid($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setIdSIncidents($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setIdLieu($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDeletedBy($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setCreatedAt($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setUpdatedAt($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setDeletedAt($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setVersion($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setVersionCreatedAt($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setVersionCreatedBy($arr[$keys[19]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceEleveSaisieVersionPeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::UTILISATEUR_ID)) $criteria->add(AbsenceEleveSaisieVersionPeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ELEVE_ID)) $criteria->add(AbsenceEleveSaisieVersionPeer::ELEVE_ID, $this->eleve_id);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::COMMENTAIRE)) $criteria->add(AbsenceEleveSaisieVersionPeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::DEBUT_ABS)) $criteria->add(AbsenceEleveSaisieVersionPeer::DEBUT_ABS, $this->debut_abs);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::FIN_ABS)) $criteria->add(AbsenceEleveSaisieVersionPeer::FIN_ABS, $this->fin_abs);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_EDT_CRENEAU, $this->id_edt_creneau);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_EDT_EMPLACEMENT_COURS, $this->id_edt_emplacement_cours);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_GROUPE)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_CLASSE)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_CLASSE, $this->id_classe);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_AID)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_AID, $this->id_aid);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_S_INCIDENTS, $this->id_s_incidents);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::ID_LIEU)) $criteria->add(AbsenceEleveSaisieVersionPeer::ID_LIEU, $this->id_lieu);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::DELETED_BY)) $criteria->add(AbsenceEleveSaisieVersionPeer::DELETED_BY, $this->deleted_by);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::CREATED_AT)) $criteria->add(AbsenceEleveSaisieVersionPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::UPDATED_AT)) $criteria->add(AbsenceEleveSaisieVersionPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::DELETED_AT)) $criteria->add(AbsenceEleveSaisieVersionPeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::VERSION)) $criteria->add(AbsenceEleveSaisieVersionPeer::VERSION, $this->version);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT)) $criteria->add(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_AT, $this->version_created_at);
		if ($this->isColumnModified(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_BY)) $criteria->add(AbsenceEleveSaisieVersionPeer::VERSION_CREATED_BY, $this->version_created_by);

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
		$criteria = new Criteria(AbsenceEleveSaisieVersionPeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveSaisieVersionPeer::ID, $this->id);
		$criteria->add(AbsenceEleveSaisieVersionPeer::VERSION, $this->version);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();
		$pks[0] = $this->getId();
		$pks[1] = $this->getVersion();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{
		$this->setId($keys[0]);
		$this->setVersion($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getId()) && (null === $this->getVersion());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AbsenceEleveSaisieVersion (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setId($this->getId());
		$copyObj->setUtilisateurId($this->getUtilisateurId());
		$copyObj->setEleveId($this->getEleveId());
		$copyObj->setCommentaire($this->getCommentaire());
		$copyObj->setDebutAbs($this->getDebutAbs());
		$copyObj->setFinAbs($this->getFinAbs());
		$copyObj->setIdEdtCreneau($this->getIdEdtCreneau());
		$copyObj->setIdEdtEmplacementCours($this->getIdEdtEmplacementCours());
		$copyObj->setIdGroupe($this->getIdGroupe());
		$copyObj->setIdClasse($this->getIdClasse());
		$copyObj->setIdAid($this->getIdAid());
		$copyObj->setIdSIncidents($this->getIdSIncidents());
		$copyObj->setIdLieu($this->getIdLieu());
		$copyObj->setDeletedBy($this->getDeletedBy());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());
		$copyObj->setDeletedAt($this->getDeletedAt());
		$copyObj->setVersion($this->getVersion());
		$copyObj->setVersionCreatedAt($this->getVersionCreatedAt());
		$copyObj->setVersionCreatedBy($this->getVersionCreatedBy());
		if ($makeNew) {
			$copyObj->setNew(true);
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
	 * @return     AbsenceEleveSaisieVersion Clone of current object.
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
	 * @return     AbsenceEleveSaisieVersionPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveSaisieVersionPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveSaisie object.
	 *
	 * @param      AbsenceEleveSaisie $v
	 * @return     AbsenceEleveSaisieVersion The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveSaisie(AbsenceEleveSaisie $v = null)
	{
		if ($v === null) {
			$this->setId(NULL);
		} else {
			$this->setId($v->getId());
		}

		$this->aAbsenceEleveSaisie = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveSaisie object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisieVersion($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveSaisie object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveSaisie The associated AbsenceEleveSaisie object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveSaisie(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveSaisie === null && ($this->id !== null)) {
			$this->aAbsenceEleveSaisie = AbsenceEleveSaisieQuery::create()->findPk($this->id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aAbsenceEleveSaisie->addAbsenceEleveSaisieVersions($this);
			 */
		}
		return $this->aAbsenceEleveSaisie;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->utilisateur_id = null;
		$this->eleve_id = null;
		$this->commentaire = null;
		$this->debut_abs = null;
		$this->fin_abs = null;
		$this->id_edt_creneau = null;
		$this->id_edt_emplacement_cours = null;
		$this->id_groupe = null;
		$this->id_classe = null;
		$this->id_aid = null;
		$this->id_s_incidents = null;
		$this->id_lieu = null;
		$this->deleted_by = null;
		$this->created_at = null;
		$this->updated_at = null;
		$this->deleted_at = null;
		$this->version = null;
		$this->version_created_at = null;
		$this->version_created_by = null;
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
		} // if ($deep)

		$this->aAbsenceEleveSaisie = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AbsenceEleveSaisieVersionPeer::DEFAULT_STRING_FORMAT);
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

} // BaseAbsenceEleveSaisieVersion
