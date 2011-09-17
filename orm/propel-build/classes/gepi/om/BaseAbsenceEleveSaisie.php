<?php


/**
 * Base class that represents a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceEleveSaisie extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AbsenceEleveSaisiePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceEleveSaisiePeer
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
	 * @var        UtilisateurProfessionnel
	 */
	protected $aUtilisateurProfessionnel;

	/**
	 * @var        Eleve
	 */
	protected $aEleve;

	/**
	 * @var        EdtCreneau
	 */
	protected $aEdtCreneau;

	/**
	 * @var        EdtEmplacementCours
	 */
	protected $aEdtEmplacementCours;

	/**
	 * @var        Groupe
	 */
	protected $aGroupe;

	/**
	 * @var        Classe
	 */
	protected $aClasse;

	/**
	 * @var        AidDetails
	 */
	protected $aAidDetails;

	/**
	 * @var        AbsenceEleveLieu
	 */
	protected $aAbsenceEleveLieu;

	/**
	 * @var        array JTraitementSaisieEleve[] Collection to store aggregation of JTraitementSaisieEleve objects.
	 */
	protected $collJTraitementSaisieEleves;

	/**
	 * @var        array AbsenceEleveSaisieVersion[] Collection to store aggregation of AbsenceEleveSaisieVersion objects.
	 */
	protected $collAbsenceEleveSaisieVersions;

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
	 * Initializes internal state of BaseAbsenceEleveSaisie object.
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
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [utilisateur_id] column.
	 * Login de l'utilisateur professionnel qui a saisi l'absence
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setUtilisateurId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->utilisateur_id !== $v) {
			$this->utilisateur_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::UTILISATEUR_ID;
		}

		if ($this->aUtilisateurProfessionnel !== null && $this->aUtilisateurProfessionnel->getLogin() !== $v) {
			$this->aUtilisateurProfessionnel = null;
		}

		return $this;
	} // setUtilisateurId()

	/**
	 * Set the value of [eleve_id] column.
	 * id_eleve de l'eleve objet de la saisie, egal à null si aucun eleve n'est saisi
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setEleveId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eleve_id !== $v) {
			$this->eleve_id = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ELEVE_ID;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setEleveId()

	/**
	 * Set the value of [commentaire] column.
	 * commentaire de l'utilisateur
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setCommentaire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->commentaire !== $v) {
			$this->commentaire = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::COMMENTAIRE;
		}

		return $this;
	} // setCommentaire()

	/**
	 * Sets the value of [debut_abs] column to a normalized version of the date/time value specified.
	 * Debut de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setDebutAbs($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->debut_abs !== null || $dt !== null) {
			$currentDateAsString = ($this->debut_abs !== null && $tmpDt = new DateTime($this->debut_abs)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->debut_abs = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::DEBUT_ABS;
			}
		} // if either are not null

		return $this;
	} // setDebutAbs()

	/**
	 * Sets the value of [fin_abs] column to a normalized version of the date/time value specified.
	 * Fin de l'absence en timestamp UNIX
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setFinAbs($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->fin_abs !== null || $dt !== null) {
			$currentDateAsString = ($this->fin_abs !== null && $tmpDt = new DateTime($this->fin_abs)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->fin_abs = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::FIN_ABS;
			}
		} // if either are not null

		return $this;
	} // setFinAbs()

	/**
	 * Set the value of [id_edt_creneau] column.
	 * identifiant du creneaux de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdEdtCreneau($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_creneau !== $v) {
			$this->id_edt_creneau = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_EDT_CRENEAU;
		}

		if ($this->aEdtCreneau !== null && $this->aEdtCreneau->getIdDefiniePeriode() !== $v) {
			$this->aEdtCreneau = null;
		}

		return $this;
	} // setIdEdtCreneau()

	/**
	 * Set the value of [id_edt_emplacement_cours] column.
	 * identifiant du cours de l'emploi du temps
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdEdtEmplacementCours($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_edt_emplacement_cours !== $v) {
			$this->id_edt_emplacement_cours = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS;
		}

		if ($this->aEdtEmplacementCours !== null && $this->aEdtEmplacementCours->getIdCours() !== $v) {
			$this->aEdtEmplacementCours = null;
		}

		return $this;
	} // setIdEdtEmplacementCours()

	/**
	 * Set the value of [id_groupe] column.
	 * identifiant du groupe pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdGroupe($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_groupe !== $v) {
			$this->id_groupe = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_GROUPE;
		}

		if ($this->aGroupe !== null && $this->aGroupe->getId() !== $v) {
			$this->aGroupe = null;
		}

		return $this;
	} // setIdGroupe()

	/**
	 * Set the value of [id_classe] column.
	 * identifiant de la classe pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdClasse($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_classe !== $v) {
			$this->id_classe = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_CLASSE;
		}

		if ($this->aClasse !== null && $this->aClasse->getId() !== $v) {
			$this->aClasse = null;
		}

		return $this;
	} // setIdClasse()

	/**
	 * Set the value of [id_aid] column.
	 * identifiant de l'aid pour lequel la saisie a ete effectuee
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdAid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_aid !== $v) {
			$this->id_aid = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_AID;
		}

		if ($this->aAidDetails !== null && $this->aAidDetails->getId() !== $v) {
			$this->aAidDetails = null;
		}

		return $this;
	} // setIdAid()

	/**
	 * Set the value of [id_s_incidents] column.
	 * identifiant de la saisie d'incident discipline
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdSIncidents($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_s_incidents !== $v) {
			$this->id_s_incidents = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_S_INCIDENTS;
		}

		return $this;
	} // setIdSIncidents()

	/**
	 * Set the value of [id_lieu] column.
	 * cle etrangere du lieu ou se trouve l'eleve
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setIdLieu($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_lieu !== $v) {
			$this->id_lieu = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID_LIEU;
		}

		if ($this->aAbsenceEleveLieu !== null && $this->aAbsenceEleveLieu->getId() !== $v) {
			$this->aAbsenceEleveLieu = null;
		}

		return $this;
	} // setIdLieu()

	/**
	 * Set the value of [deleted_by] column.
	 * Login de l'utilisateur professionnel qui a supprimé la saisie
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setDeletedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->deleted_by !== $v) {
			$this->deleted_by = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::DELETED_BY;
		}

		return $this;
	} // setDeletedBy()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * Date de creation de la saisie
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * Date de modification de la saisie, y compris suppression, restauration et changement de version
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Sets the value of [deleted_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setDeletedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->deleted_at !== null || $dt !== null) {
			$currentDateAsString = ($this->deleted_at !== null && $tmpDt = new DateTime($this->deleted_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->deleted_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::DELETED_AT;
			}
		} // if either are not null

		return $this;
	} // setDeletedAt()

	/**
	 * Set the value of [version] column.
	 * 
	 * @param      int $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setVersion($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->version !== $v || $this->isNew()) {
			$this->version = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::VERSION;
		}

		return $this;
	} // setVersion()

	/**
	 * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setVersionCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->version_created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->version_created_at !== null && $tmpDt = new DateTime($this->version_created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->version_created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::VERSION_CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setVersionCreatedAt()

	/**
	 * Set the value of [version_created_by] column.
	 * 
	 * @param      string $v new value
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function setVersionCreatedBy($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->version_created_by !== $v) {
			$this->version_created_by = $v;
			$this->modifiedColumns[] = AbsenceEleveSaisiePeer::VERSION_CREATED_BY;
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

			return $startcol + 20; // 20 = AbsenceEleveSaisiePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceEleveSaisie object", $e);
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
		if ($this->aEleve !== null && $this->eleve_id !== $this->aEleve->getIdEleve()) {
			$this->aEleve = null;
		}
		if ($this->aEdtCreneau !== null && $this->id_edt_creneau !== $this->aEdtCreneau->getIdDefiniePeriode()) {
			$this->aEdtCreneau = null;
		}
		if ($this->aEdtEmplacementCours !== null && $this->id_edt_emplacement_cours !== $this->aEdtEmplacementCours->getIdCours()) {
			$this->aEdtEmplacementCours = null;
		}
		if ($this->aGroupe !== null && $this->id_groupe !== $this->aGroupe->getId()) {
			$this->aGroupe = null;
		}
		if ($this->aClasse !== null && $this->id_classe !== $this->aClasse->getId()) {
			$this->aClasse = null;
		}
		if ($this->aAidDetails !== null && $this->id_aid !== $this->aAidDetails->getId()) {
			$this->aAidDetails = null;
		}
		if ($this->aAbsenceEleveLieu !== null && $this->id_lieu !== $this->aAbsenceEleveLieu->getId()) {
			$this->aAbsenceEleveLieu = null;
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceEleveSaisiePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aUtilisateurProfessionnel = null;
			$this->aEleve = null;
			$this->aEdtCreneau = null;
			$this->aEdtEmplacementCours = null;
			$this->aGroupe = null;
			$this->aClasse = null;
			$this->aAidDetails = null;
			$this->aAbsenceEleveLieu = null;
			$this->collJTraitementSaisieEleves = null;

			$this->collAbsenceEleveSaisieVersions = null;

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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			// soft_delete behavior
			if (!empty($ret) && AbsenceEleveSaisieQuery::isSoftDeleteEnabled()) {
				$this->keepUpdateDateUnchanged();
				$this->setDeletedAt(time());
				$this->save($con);
				$con->commit();
				AbsenceEleveSaisiePeer::removeInstanceFromPool($this);
				return;
			}

			if ($ret) {
				AbsenceEleveSaisieQuery::create()
					->filterByPrimaryKey($this->getPrimaryKey())
					->delete($con);
				$this->postDelete($con);
				// versionable behavior
				// emulate delete cascade
				AbsenceEleveSaisieVersionQuery::create()
					->filterByAbsenceEleveSaisie($this)
					->delete($con);
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
			$con = Propel::getConnection(AbsenceEleveSaisiePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// versionable behavior
			if ($this->isVersioningNecessary()) {
				$this->setVersion($this->isNew() ? 1 : $this->getLastVersionNumber($con) + 1);
				if (!$this->isColumnModified(AbsenceEleveSaisiePeer::VERSION_CREATED_AT)) {
					$this->setVersionCreatedAt(time());
				}
				$createVersion = true; // for postSave hook
			}
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(AbsenceEleveSaisiePeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(AbsenceEleveSaisiePeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(AbsenceEleveSaisiePeer::UPDATED_AT)) {
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
				// versionable behavior
				if (isset($createVersion)) {
					$this->addVersion($con);
				}
				AbsenceEleveSaisiePeer::addInstanceToPool($this);
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

			if ($this->aEleve !== null) {
				if ($this->aEleve->isModified() || $this->aEleve->isNew()) {
					$affectedRows += $this->aEleve->save($con);
				}
				$this->setEleve($this->aEleve);
			}

			if ($this->aEdtCreneau !== null) {
				if ($this->aEdtCreneau->isModified() || $this->aEdtCreneau->isNew()) {
					$affectedRows += $this->aEdtCreneau->save($con);
				}
				$this->setEdtCreneau($this->aEdtCreneau);
			}

			if ($this->aEdtEmplacementCours !== null) {
				if ($this->aEdtEmplacementCours->isModified() || $this->aEdtEmplacementCours->isNew()) {
					$affectedRows += $this->aEdtEmplacementCours->save($con);
				}
				$this->setEdtEmplacementCours($this->aEdtEmplacementCours);
			}

			if ($this->aGroupe !== null) {
				if ($this->aGroupe->isModified() || $this->aGroupe->isNew()) {
					$affectedRows += $this->aGroupe->save($con);
				}
				$this->setGroupe($this->aGroupe);
			}

			if ($this->aClasse !== null) {
				if ($this->aClasse->isModified() || $this->aClasse->isNew()) {
					$affectedRows += $this->aClasse->save($con);
				}
				$this->setClasse($this->aClasse);
			}

			if ($this->aAidDetails !== null) {
				if ($this->aAidDetails->isModified() || $this->aAidDetails->isNew()) {
					$affectedRows += $this->aAidDetails->save($con);
				}
				$this->setAidDetails($this->aAidDetails);
			}

			if ($this->aAbsenceEleveLieu !== null) {
				if ($this->aAbsenceEleveLieu->isModified() || $this->aAbsenceEleveLieu->isNew()) {
					$affectedRows += $this->aAbsenceEleveLieu->save($con);
				}
				$this->setAbsenceEleveLieu($this->aAbsenceEleveLieu);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AbsenceEleveSaisiePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(AbsenceEleveSaisiePeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.AbsenceEleveSaisiePeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += AbsenceEleveSaisiePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJTraitementSaisieEleves !== null) {
				foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collAbsenceEleveSaisieVersions !== null) {
				foreach ($this->collAbsenceEleveSaisieVersions as $referrerFK) {
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

			if ($this->aEleve !== null) {
				if (!$this->aEleve->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEleve->getValidationFailures());
				}
			}

			if ($this->aEdtCreneau !== null) {
				if (!$this->aEdtCreneau->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEdtCreneau->getValidationFailures());
				}
			}

			if ($this->aEdtEmplacementCours !== null) {
				if (!$this->aEdtEmplacementCours->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEdtEmplacementCours->getValidationFailures());
				}
			}

			if ($this->aGroupe !== null) {
				if (!$this->aGroupe->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGroupe->getValidationFailures());
				}
			}

			if ($this->aClasse !== null) {
				if (!$this->aClasse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aClasse->getValidationFailures());
				}
			}

			if ($this->aAidDetails !== null) {
				if (!$this->aAidDetails->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAidDetails->getValidationFailures());
				}
			}

			if ($this->aAbsenceEleveLieu !== null) {
				if (!$this->aAbsenceEleveLieu->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAbsenceEleveLieu->getValidationFailures());
				}
			}


			if (($retval = AbsenceEleveSaisiePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJTraitementSaisieEleves !== null) {
					foreach ($this->collJTraitementSaisieEleves as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collAbsenceEleveSaisieVersions !== null) {
					foreach ($this->collAbsenceEleveSaisieVersions as $referrerFK) {
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
		$pos = AbsenceEleveSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		if (isset($alreadyDumpedObjects['AbsenceEleveSaisie'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AbsenceEleveSaisie'][$this->getPrimaryKey()] = true;
		$keys = AbsenceEleveSaisiePeer::getFieldNames($keyType);
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
			if (null !== $this->aUtilisateurProfessionnel) {
				$result['UtilisateurProfessionnel'] = $this->aUtilisateurProfessionnel->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aEleve) {
				$result['Eleve'] = $this->aEleve->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aEdtCreneau) {
				$result['EdtCreneau'] = $this->aEdtCreneau->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aEdtEmplacementCours) {
				$result['EdtEmplacementCours'] = $this->aEdtEmplacementCours->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aGroupe) {
				$result['Groupe'] = $this->aGroupe->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aClasse) {
				$result['Classe'] = $this->aClasse->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aAidDetails) {
				$result['AidDetails'] = $this->aAidDetails->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->aAbsenceEleveLieu) {
				$result['AbsenceEleveLieu'] = $this->aAbsenceEleveLieu->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collJTraitementSaisieEleves) {
				$result['JTraitementSaisieEleves'] = $this->collJTraitementSaisieEleves->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveSaisieVersions) {
				$result['AbsenceEleveSaisieVersions'] = $this->collAbsenceEleveSaisieVersions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
		$pos = AbsenceEleveSaisiePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
		$keys = AbsenceEleveSaisiePeer::getFieldNames($keyType);

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
		$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID)) $criteria->add(AbsenceEleveSaisiePeer::ID, $this->id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::UTILISATEUR_ID)) $criteria->add(AbsenceEleveSaisiePeer::UTILISATEUR_ID, $this->utilisateur_id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ELEVE_ID)) $criteria->add(AbsenceEleveSaisiePeer::ELEVE_ID, $this->eleve_id);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::COMMENTAIRE)) $criteria->add(AbsenceEleveSaisiePeer::COMMENTAIRE, $this->commentaire);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::DEBUT_ABS)) $criteria->add(AbsenceEleveSaisiePeer::DEBUT_ABS, $this->debut_abs);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::FIN_ABS)) $criteria->add(AbsenceEleveSaisiePeer::FIN_ABS, $this->fin_abs);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU)) $criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $this->id_edt_creneau);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS)) $criteria->add(AbsenceEleveSaisiePeer::ID_EDT_EMPLACEMENT_COURS, $this->id_edt_emplacement_cours);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_GROUPE)) $criteria->add(AbsenceEleveSaisiePeer::ID_GROUPE, $this->id_groupe);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_CLASSE)) $criteria->add(AbsenceEleveSaisiePeer::ID_CLASSE, $this->id_classe);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_AID)) $criteria->add(AbsenceEleveSaisiePeer::ID_AID, $this->id_aid);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_S_INCIDENTS)) $criteria->add(AbsenceEleveSaisiePeer::ID_S_INCIDENTS, $this->id_s_incidents);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::ID_LIEU)) $criteria->add(AbsenceEleveSaisiePeer::ID_LIEU, $this->id_lieu);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::DELETED_BY)) $criteria->add(AbsenceEleveSaisiePeer::DELETED_BY, $this->deleted_by);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::CREATED_AT)) $criteria->add(AbsenceEleveSaisiePeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::UPDATED_AT)) $criteria->add(AbsenceEleveSaisiePeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::DELETED_AT)) $criteria->add(AbsenceEleveSaisiePeer::DELETED_AT, $this->deleted_at);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::VERSION)) $criteria->add(AbsenceEleveSaisiePeer::VERSION, $this->version);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::VERSION_CREATED_AT)) $criteria->add(AbsenceEleveSaisiePeer::VERSION_CREATED_AT, $this->version_created_at);
		if ($this->isColumnModified(AbsenceEleveSaisiePeer::VERSION_CREATED_BY)) $criteria->add(AbsenceEleveSaisiePeer::VERSION_CREATED_BY, $this->version_created_by);

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
		$criteria = new Criteria(AbsenceEleveSaisiePeer::DATABASE_NAME);
		$criteria->add(AbsenceEleveSaisiePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of AbsenceEleveSaisie (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
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

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJTraitementSaisieEleves() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJTraitementSaisieEleve($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getAbsenceEleveSaisieVersions() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisieVersion($relObj->copy($deepCopy));
				}
			}

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
	 * @return     AbsenceEleveSaisie Clone of current object.
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
	 * @return     AbsenceEleveSaisiePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceEleveSaisiePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a UtilisateurProfessionnel object.
	 *
	 * @param      UtilisateurProfessionnel $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
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
			$v->addAbsenceEleveSaisie($this);
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
				$this->aUtilisateurProfessionnel->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aUtilisateurProfessionnel;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEleve(Eleve $v = null)
	{
		if ($v === null) {
			$this->setEleveId(NULL);
		} else {
			$this->setEleveId($v->getIdEleve());
		}

		$this->aEleve = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Eleve object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated Eleve object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Eleve The associated Eleve object.
	 * @throws     PropelException
	 */
	public function getEleve(PropelPDO $con = null)
	{
		if ($this->aEleve === null && ($this->eleve_id !== null)) {
			$this->aEleve = EleveQuery::create()->findPk($this->eleve_id, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aEleve->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Declares an association between this object and a EdtCreneau object.
	 *
	 * @param      EdtCreneau $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEdtCreneau(EdtCreneau $v = null)
	{
		if ($v === null) {
			$this->setIdEdtCreneau(NULL);
		} else {
			$this->setIdEdtCreneau($v->getIdDefiniePeriode());
		}

		$this->aEdtCreneau = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the EdtCreneau object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated EdtCreneau object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     EdtCreneau The associated EdtCreneau object.
	 * @throws     PropelException
	 */
	public function getEdtCreneau(PropelPDO $con = null)
	{
		if ($this->aEdtCreneau === null && ($this->id_edt_creneau !== null)) {
			$this->aEdtCreneau = EdtCreneauQuery::create()->findPk($this->id_edt_creneau, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aEdtCreneau->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEdtCreneau;
	}

	/**
	 * Declares an association between this object and a EdtEmplacementCours object.
	 *
	 * @param      EdtEmplacementCours $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setEdtEmplacementCours(EdtEmplacementCours $v = null)
	{
		if ($v === null) {
			$this->setIdEdtEmplacementCours(NULL);
		} else {
			$this->setIdEdtEmplacementCours($v->getIdCours());
		}

		$this->aEdtEmplacementCours = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the EdtEmplacementCours object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated EdtEmplacementCours object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     EdtEmplacementCours The associated EdtEmplacementCours object.
	 * @throws     PropelException
	 */
	public function getEdtEmplacementCours(PropelPDO $con = null)
	{
		if ($this->aEdtEmplacementCours === null && ($this->id_edt_emplacement_cours !== null)) {
			$this->aEdtEmplacementCours = EdtEmplacementCoursQuery::create()->findPk($this->id_edt_emplacement_cours, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aEdtEmplacementCours->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aEdtEmplacementCours;
	}

	/**
	 * Declares an association between this object and a Groupe object.
	 *
	 * @param      Groupe $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setGroupe(Groupe $v = null)
	{
		if ($v === null) {
			$this->setIdGroupe(NULL);
		} else {
			$this->setIdGroupe($v->getId());
		}

		$this->aGroupe = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Groupe object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated Groupe object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Groupe The associated Groupe object.
	 * @throws     PropelException
	 */
	public function getGroupe(PropelPDO $con = null)
	{
		if ($this->aGroupe === null && ($this->id_groupe !== null)) {
			$this->aGroupe = GroupeQuery::create()->findPk($this->id_groupe, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aGroupe->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aGroupe;
	}

	/**
	 * Declares an association between this object and a Classe object.
	 *
	 * @param      Classe $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setClasse(Classe $v = null)
	{
		if ($v === null) {
			$this->setIdClasse(NULL);
		} else {
			$this->setIdClasse($v->getId());
		}

		$this->aClasse = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Classe object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated Classe object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Classe The associated Classe object.
	 * @throws     PropelException
	 */
	public function getClasse(PropelPDO $con = null)
	{
		if ($this->aClasse === null && ($this->id_classe !== null)) {
			$this->aClasse = ClasseQuery::create()->findPk($this->id_classe, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aClasse->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aClasse;
	}

	/**
	 * Declares an association between this object and a AidDetails object.
	 *
	 * @param      AidDetails $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAidDetails(AidDetails $v = null)
	{
		if ($v === null) {
			$this->setIdAid(NULL);
		} else {
			$this->setIdAid($v->getId());
		}

		$this->aAidDetails = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AidDetails object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated AidDetails object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AidDetails The associated AidDetails object.
	 * @throws     PropelException
	 */
	public function getAidDetails(PropelPDO $con = null)
	{
		if ($this->aAidDetails === null && ($this->id_aid !== null)) {
			$this->aAidDetails = AidDetailsQuery::create()->findPk($this->id_aid, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aAidDetails->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aAidDetails;
	}

	/**
	 * Declares an association between this object and a AbsenceEleveLieu object.
	 *
	 * @param      AbsenceEleveLieu $v
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAbsenceEleveLieu(AbsenceEleveLieu $v = null)
	{
		if ($v === null) {
			$this->setIdLieu(NULL);
		} else {
			$this->setIdLieu($v->getId());
		}

		$this->aAbsenceEleveLieu = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AbsenceEleveLieu object, it will not be re-added.
		if ($v !== null) {
			$v->addAbsenceEleveSaisie($this);
		}

		return $this;
	}


	/**
	 * Get the associated AbsenceEleveLieu object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AbsenceEleveLieu The associated AbsenceEleveLieu object.
	 * @throws     PropelException
	 */
	public function getAbsenceEleveLieu(PropelPDO $con = null)
	{
		if ($this->aAbsenceEleveLieu === null && ($this->id_lieu !== null)) {
			$this->aAbsenceEleveLieu = AbsenceEleveLieuQuery::create()->findPk($this->id_lieu, $con);
			/* The following can be used additionally to
				guarantee the related object contains a reference
				to this object.  This level of coupling may, however, be
				undesirable since it could result in an only partially populated collection
				in the referenced object.
				$this->aAbsenceEleveLieu->addAbsenceEleveSaisies($this);
			 */
		}
		return $this->aAbsenceEleveLieu;
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
		if ('JTraitementSaisieEleve' == $relationName) {
			return $this->initJTraitementSaisieEleves();
		}
		if ('AbsenceEleveSaisieVersion' == $relationName) {
			return $this->initAbsenceEleveSaisieVersions();
		}
	}

	/**
	 * Clears out the collJTraitementSaisieEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJTraitementSaisieEleves()
	 */
	public function clearJTraitementSaisieEleves()
	{
		$this->collJTraitementSaisieEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJTraitementSaisieEleves collection.
	 *
	 * By default this just sets the collJTraitementSaisieEleves collection to an empty array (like clearcollJTraitementSaisieEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJTraitementSaisieEleves($overrideExisting = true)
	{
		if (null !== $this->collJTraitementSaisieEleves && !$overrideExisting) {
			return;
		}
		$this->collJTraitementSaisieEleves = new PropelObjectCollection();
		$this->collJTraitementSaisieEleves->setModel('JTraitementSaisieEleve');
	}

	/**
	 * Gets an array of JTraitementSaisieEleve objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveSaisie is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JTraitementSaisieEleve[] List of JTraitementSaisieEleve objects
	 * @throws     PropelException
	 */
	public function getJTraitementSaisieEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJTraitementSaisieEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJTraitementSaisieEleves) {
				// return empty collection
				$this->initJTraitementSaisieEleves();
			} else {
				$collJTraitementSaisieEleves = JTraitementSaisieEleveQuery::create(null, $criteria)
					->filterByAbsenceEleveSaisie($this)
					->find($con);
				if (null !== $criteria) {
					return $collJTraitementSaisieEleves;
				}
				$this->collJTraitementSaisieEleves = $collJTraitementSaisieEleves;
			}
		}
		return $this->collJTraitementSaisieEleves;
	}

	/**
	 * Returns the number of related JTraitementSaisieEleve objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JTraitementSaisieEleve objects.
	 * @throws     PropelException
	 */
	public function countJTraitementSaisieEleves(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collJTraitementSaisieEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collJTraitementSaisieEleves) {
				return 0;
			} else {
				$query = JTraitementSaisieEleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveSaisie($this)
					->count($con);
			}
		} else {
			return count($this->collJTraitementSaisieEleves);
		}
	}

	/**
	 * Method called to associate a JTraitementSaisieEleve object to this object
	 * through the JTraitementSaisieEleve foreign key attribute.
	 *
	 * @param      JTraitementSaisieEleve $l JTraitementSaisieEleve
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJTraitementSaisieEleve(JTraitementSaisieEleve $l)
	{
		if ($this->collJTraitementSaisieEleves === null) {
			$this->initJTraitementSaisieEleves();
		}
		if (!$this->collJTraitementSaisieEleves->contains($l)) { // only add it if the **same** object is not already associated
			$this->collJTraitementSaisieEleves[]= $l;
			$l->setAbsenceEleveSaisie($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AbsenceEleveSaisie is new, it will return
	 * an empty collection; or if this AbsenceEleveSaisie has previously
	 * been saved, it will retrieve related JTraitementSaisieEleves from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AbsenceEleveSaisie.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JTraitementSaisieEleve[] List of JTraitementSaisieEleve objects
	 */
	public function getJTraitementSaisieElevesJoinAbsenceEleveTraitement($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JTraitementSaisieEleveQuery::create(null, $criteria);
		$query->joinWith('AbsenceEleveTraitement', $join_behavior);

		return $this->getJTraitementSaisieEleves($query, $con);
	}

	/**
	 * Clears out the collAbsenceEleveSaisieVersions collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addAbsenceEleveSaisieVersions()
	 */
	public function clearAbsenceEleveSaisieVersions()
	{
		$this->collAbsenceEleveSaisieVersions = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collAbsenceEleveSaisieVersions collection.
	 *
	 * By default this just sets the collAbsenceEleveSaisieVersions collection to an empty array (like clearcollAbsenceEleveSaisieVersions());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initAbsenceEleveSaisieVersions($overrideExisting = true)
	{
		if (null !== $this->collAbsenceEleveSaisieVersions && !$overrideExisting) {
			return;
		}
		$this->collAbsenceEleveSaisieVersions = new PropelObjectCollection();
		$this->collAbsenceEleveSaisieVersions->setModel('AbsenceEleveSaisieVersion');
	}

	/**
	 * Gets an array of AbsenceEleveSaisieVersion objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveSaisie is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array AbsenceEleveSaisieVersion[] List of AbsenceEleveSaisieVersion objects
	 * @throws     PropelException
	 */
	public function getAbsenceEleveSaisieVersions($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisieVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisieVersions) {
				// return empty collection
				$this->initAbsenceEleveSaisieVersions();
			} else {
				$collAbsenceEleveSaisieVersions = AbsenceEleveSaisieVersionQuery::create(null, $criteria)
					->filterByAbsenceEleveSaisie($this)
					->find($con);
				if (null !== $criteria) {
					return $collAbsenceEleveSaisieVersions;
				}
				$this->collAbsenceEleveSaisieVersions = $collAbsenceEleveSaisieVersions;
			}
		}
		return $this->collAbsenceEleveSaisieVersions;
	}

	/**
	 * Returns the number of related AbsenceEleveSaisieVersion objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related AbsenceEleveSaisieVersion objects.
	 * @throws     PropelException
	 */
	public function countAbsenceEleveSaisieVersions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveSaisieVersions || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveSaisieVersions) {
				return 0;
			} else {
				$query = AbsenceEleveSaisieVersionQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAbsenceEleveSaisie($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveSaisieVersions);
		}
	}

	/**
	 * Method called to associate a AbsenceEleveSaisieVersion object to this object
	 * through the AbsenceEleveSaisieVersion foreign key attribute.
	 *
	 * @param      AbsenceEleveSaisieVersion $l AbsenceEleveSaisieVersion
	 * @return     void
	 * @throws     PropelException
	 */
	public function addAbsenceEleveSaisieVersion(AbsenceEleveSaisieVersion $l)
	{
		if ($this->collAbsenceEleveSaisieVersions === null) {
			$this->initAbsenceEleveSaisieVersions();
		}
		if (!$this->collAbsenceEleveSaisieVersions->contains($l)) { // only add it if the **same** object is not already associated
			$this->collAbsenceEleveSaisieVersions[]= $l;
			$l->setAbsenceEleveSaisie($this);
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
	 * By default this just sets the collAbsenceEleveTraitements collection to an empty collection (like clearAbsenceEleveTraitements());
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
	 * Gets a collection of AbsenceEleveTraitement objects related by a many-to-many relationship
	 * to the current object by way of the j_traitements_saisies cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AbsenceEleveSaisie is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array AbsenceEleveTraitement[] List of AbsenceEleveTraitement objects
	 */
	public function getAbsenceEleveTraitements($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collAbsenceEleveTraitements || null !== $criteria) {
			if ($this->isNew() && null === $this->collAbsenceEleveTraitements) {
				// return empty collection
				$this->initAbsenceEleveTraitements();
			} else {
				$collAbsenceEleveTraitements = AbsenceEleveTraitementQuery::create(null, $criteria)
					->filterByAbsenceEleveSaisie($this)
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
	 * Gets the number of AbsenceEleveTraitement objects related by a many-to-many relationship
	 * to the current object by way of the j_traitements_saisies cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related AbsenceEleveTraitement objects
	 */
	public function countAbsenceEleveTraitements($criteria = null, $distinct = false, PropelPDO $con = null)
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
					->filterByAbsenceEleveSaisie($this)
					->count($con);
			}
		} else {
			return count($this->collAbsenceEleveTraitements);
		}
	}

	/**
	 * Associate a AbsenceEleveTraitement object to this object
	 * through the j_traitements_saisies cross reference table.
	 *
	 * @param      AbsenceEleveTraitement $absenceEleveTraitement The JTraitementSaisieEleve object to relate
	 * @return     void
	 */
	public function addAbsenceEleveTraitement($absenceEleveTraitement)
	{
		if ($this->collAbsenceEleveTraitements === null) {
			$this->initAbsenceEleveTraitements();
		}
		if (!$this->collAbsenceEleveTraitements->contains($absenceEleveTraitement)) { // only add it if the **same** object is not already associated
			$jTraitementSaisieEleve = new JTraitementSaisieEleve();
			$jTraitementSaisieEleve->setAbsenceEleveTraitement($absenceEleveTraitement);
			$this->addJTraitementSaisieEleve($jTraitementSaisieEleve);

			$this->collAbsenceEleveTraitements[]= $absenceEleveTraitement;
		}
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
			if ($this->collJTraitementSaisieEleves) {
				foreach ($this->collJTraitementSaisieEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisieVersions) {
				foreach ($this->collAbsenceEleveSaisieVersions as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveTraitements) {
				foreach ($this->collAbsenceEleveTraitements as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJTraitementSaisieEleves instanceof PropelCollection) {
			$this->collJTraitementSaisieEleves->clearIterator();
		}
		$this->collJTraitementSaisieEleves = null;
		if ($this->collAbsenceEleveSaisieVersions instanceof PropelCollection) {
			$this->collAbsenceEleveSaisieVersions->clearIterator();
		}
		$this->collAbsenceEleveSaisieVersions = null;
		if ($this->collAbsenceEleveTraitements instanceof PropelCollection) {
			$this->collAbsenceEleveTraitements->clearIterator();
		}
		$this->collAbsenceEleveTraitements = null;
		$this->aUtilisateurProfessionnel = null;
		$this->aEleve = null;
		$this->aEdtCreneau = null;
		$this->aEdtEmplacementCours = null;
		$this->aGroupe = null;
		$this->aClasse = null;
		$this->aAidDetails = null;
		$this->aAbsenceEleveLieu = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AbsenceEleveSaisiePeer::DEFAULT_STRING_FORMAT);
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = AbsenceEleveSaisiePeer::UPDATED_AT;
		return $this;
	}

	// soft_delete behavior
	
	/**
	 * Bypass the soft_delete behavior and force a hard delete of the current object
	 */
	public function forceDelete(PropelPDO $con = null)
	{
		if($isSoftDeleteEnabled = AbsenceEleveSaisiePeer::isSoftDeleteEnabled()) {
			AbsenceEleveSaisiePeer::disableSoftDelete();
		}
		$this->delete($con);
		if ($isSoftDeleteEnabled) {
			AbsenceEleveSaisiePeer::enableSoftDelete();
		}
	}
	
	/**
	 * Undelete a row that was soft_deleted
	 *
	 * @return		 int The number of rows affected by this update and any referring fk objects' save() operations.
	 */
	public function unDelete(PropelPDO $con = null)
	{
		$this->setDeletedAt(null);
		return $this->save($con);
	}

	// versionable behavior
	
	/**
	 * Checks whether the current state must be recorded as a version
	 *
	 * @return  boolean
	 */
	public function isVersioningNecessary($con = null)
	{
		if ($this->alreadyInSave) {
			return false;
		}
		if (AbsenceEleveSaisiePeer::isVersioningEnabled() && ($this->isNew() || $this->isModified())) {
			return true;
		}
		return false;
	}
	
	/**
	 * Creates a version of the current object and saves it.
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  AbsenceEleveSaisieVersion A version object
	 */
	public function addVersion($con = null)
	{
		$version = new AbsenceEleveSaisieVersion();
		$version->setId($this->id);
		$version->setUtilisateurId($this->utilisateur_id);
		$version->setEleveId($this->eleve_id);
		$version->setCommentaire($this->commentaire);
		$version->setDebutAbs($this->debut_abs);
		$version->setFinAbs($this->fin_abs);
		$version->setIdEdtCreneau($this->id_edt_creneau);
		$version->setIdEdtEmplacementCours($this->id_edt_emplacement_cours);
		$version->setIdGroupe($this->id_groupe);
		$version->setIdClasse($this->id_classe);
		$version->setIdAid($this->id_aid);
		$version->setIdSIncidents($this->id_s_incidents);
		$version->setIdLieu($this->id_lieu);
		$version->setDeletedBy($this->deleted_by);
		$version->setCreatedAt($this->created_at);
		$version->setUpdatedAt($this->updated_at);
		$version->setDeletedAt($this->deleted_at);
		$version->setVersion($this->version);
		$version->setVersionCreatedAt($this->version_created_at);
		$version->setVersionCreatedBy($this->version_created_by);
		$version->setAbsenceEleveSaisie($this);
		$version->save($con);
		
		return $version;
	}
	
	/**
	 * Sets the properties of the curent object to the value they had at a specific version
	 *
	 * @param   integer $versionNumber The version number to read
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function toVersion($versionNumber, $con = null)
	{
		$version = $this->getOneVersion($versionNumber, $con);
		if (!$version) {
			throw new PropelException(sprintf('No AbsenceEleveSaisie object found with version %d', $version));
		}
		$this->populateFromVersion($version, $con);
		
		return $this;
	}
	
	/**
	 * Sets the properties of the curent object to the value they had at a specific version
	 *
	 * @param   AbsenceEleveSaisieVersion $version The version object to use
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  AbsenceEleveSaisie The current object (for fluent API support)
	 */
	public function populateFromVersion($version, $con = null)
	{
		$this->setId($version->getId());
		$this->setUtilisateurId($version->getUtilisateurId());
		$this->setEleveId($version->getEleveId());
		$this->setCommentaire($version->getCommentaire());
		$this->setDebutAbs($version->getDebutAbs());
		$this->setFinAbs($version->getFinAbs());
		$this->setIdEdtCreneau($version->getIdEdtCreneau());
		$this->setIdEdtEmplacementCours($version->getIdEdtEmplacementCours());
		$this->setIdGroupe($version->getIdGroupe());
		$this->setIdClasse($version->getIdClasse());
		$this->setIdAid($version->getIdAid());
		$this->setIdSIncidents($version->getIdSIncidents());
		$this->setIdLieu($version->getIdLieu());
		$this->setDeletedBy($version->getDeletedBy());
		$this->setCreatedAt($version->getCreatedAt());
		$this->setUpdatedAt($version->getUpdatedAt());
		$this->setDeletedAt($version->getDeletedAt());
		$this->setVersion($version->getVersion());
		$this->setVersionCreatedAt($version->getVersionCreatedAt());
		$this->setVersionCreatedBy($version->getVersionCreatedBy());
		return $this;
	}
	
	/**
	 * Gets the latest persisted version number for the current object
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  integer
	 */
	public function getLastVersionNumber($con = null)
	{
		$v = AbsenceEleveSaisieVersionQuery::create()
			->filterByAbsenceEleveSaisie($this)
			->orderByVersion('desc')
			->findOne($con);
		if (!$v) {
			return 0;
		}
		return $v->getVersion();
	}
	
	/**
	 * Checks whether the current object is the latest one
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  Boolean
	 */
	public function isLastVersion($con = null)
	{
		return $this->getLastVersionNumber($con) == $this->getVersion();
	}
	
	/**
	 * Retrieves a version object for this entity and a version number
	 *
	 * @param   integer $versionNumber The version number to read
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  AbsenceEleveSaisieVersion A version object
	 */
	public function getOneVersion($versionNumber, $con = null)
	{
		return AbsenceEleveSaisieVersionQuery::create()
			->filterByAbsenceEleveSaisie($this)
			->filterByVersion($versionNumber)
			->findOne($con);
	}
	
	/**
	 * Gets all the versions of this object, in incremental order
	 *
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  PropelObjectCollection A list of AbsenceEleveSaisieVersion objects
	 */
	public function getAllVersions($con = null)
	{
		$criteria = new Criteria();
		$criteria->addAscendingOrderByColumn(AbsenceEleveSaisieVersionPeer::VERSION);
		return $this->getAbsenceEleveSaisieVersions($criteria, $con);
	}
	
	/**
	 * Gets all the versions of this object, in incremental order.
	 * <code>
	 * print_r($book->compare(1, 2));
	 * => array(
	 *   '1' => array('Title' => 'Book title at version 1'),
	 *   '2' => array('Title' => 'Book title at version 2')
	 * );
	 * </code>
	 *
	 * @param   integer   $fromVersionNumber
	 * @param   integer   $toVersionNumber
	 * @param   string    $keys Main key used for the result diff (versions|columns)
	 * @param   PropelPDO $con the connection to use
	 *
	 * @return  array A list of differences
	 */
	public function compareVersions($fromVersionNumber, $toVersionNumber, $keys = 'columns', $con = null)
	{
		$fromVersion = $this->getOneVersion($fromVersionNumber, $con)->toArray();
		$toVersion = $this->getOneVersion($toVersionNumber, $con)->toArray();
		$ignoredColumns = array(
			'Version',
			'VersionCreatedAt',
			'VersionCreatedBy',
		);
		$diff = array();
		foreach ($fromVersion as $key => $value) {
			if (in_array($key, $ignoredColumns)) {
				continue;
			}
			if ($toVersion[$key] != $value) {
				switch ($keys) {
					case 'versions':
						$diff[$fromVersionNumber][$key] = $value;
						$diff[$toVersionNumber][$key] = $toVersion[$key];
						break;
					default:
						$diff[$key] = array(
							$fromVersionNumber => $value,
							$toVersionNumber => $toVersion[$key],
						);
						break;
				}
			}
		}
		return $diff;
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

} // BaseAbsenceEleveSaisie
