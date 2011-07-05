<?php


/**
 * Base class that represents a row from the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAbsenceAgregationDecompte extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AbsenceAgregationDecomptePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AbsenceAgregationDecomptePeer
	 */
	protected static $peer;

	/**
	 * The value for the eleve_id field.
	 * @var        int
	 */
	protected $eleve_id;

	/**
	 * The value for the date_demi_jounee field.
	 * Note: this column has a database default value of: NULL
	 * @var        string
	 */
	protected $date_demi_jounee;

	/**
	 * The value for the manquement_obligation_presence field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $manquement_obligation_presence;

	/**
	 * The value for the justifiee field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $justifiee;

	/**
	 * The value for the notifiee field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $notifiee;

	/**
	 * The value for the nb_retards field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $nb_retards;

	/**
	 * The value for the nb_retards_justifies field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $nb_retards_justifies;

	/**
	 * The value for the motifs_absences field.
	 * @var        array
	 */
	protected $motifs_absences;

	/**
	 * The unserialized $motifs_absences value - i.e. the persisted object.
	 * This is necessary to avoid repeated calls to unserialize() at runtime.
	 * @var        object
	 */
	protected $motifs_absences_unserialized;

	/**
	 * The value for the motifs_retards field.
	 * @var        array
	 */
	protected $motifs_retards;

	/**
	 * The unserialized $motifs_retards value - i.e. the persisted object.
	 * This is necessary to avoid repeated calls to unserialize() at runtime.
	 * @var        object
	 */
	protected $motifs_retards_unserialized;

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
	 * @var        Eleve
	 */
	protected $aEleve;

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
		$this->date_demi_jounee = NULL;
		$this->manquement_obligation_presence = false;
		$this->justifiee = false;
		$this->notifiee = false;
		$this->nb_retards = 0;
		$this->nb_retards_justifies = 0;
	}

	/**
	 * Initializes internal state of BaseAbsenceAgregationDecompte object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [eleve_id] column value.
	 * id de l'eleve
	 * @return     int
	 */
	public function getEleveId()
	{
		return $this->eleve_id;
	}

	/**
	 * Get the [optionally formatted] temporal [date_demi_jounee] column value.
	 * Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateDemiJounee($format = 'Y-m-d H:i:s')
	{
		if ($this->date_demi_jounee === null) {
			return null;
		}


		if ($this->date_demi_jounee === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_demi_jounee);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_demi_jounee, true), $x);
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
	 * Get the [manquement_obligation_presence] column value.
	 * Cette demi journée est comptée comme absence
	 * @return     boolean
	 */
	public function getManquementObligationPresence()
	{
		return $this->manquement_obligation_presence;
	}

	/**
	 * Get the [justifiee] column value.
	 * Si cette demi journée est compté comme absence, y a-t-il une justification
	 * @return     boolean
	 */
	public function getJustifiee()
	{
		return $this->justifiee;
	}

	/**
	 * Get the [notifiee] column value.
	 * Si cette demi journée est compté comme absence, y a-t-il une notification à la famille
	 * @return     boolean
	 */
	public function getNotifiee()
	{
		return $this->notifiee;
	}

	/**
	 * Get the [nb_retards] column value.
	 * Nombre de retards décomptés dans la demi journée
	 * @return     int
	 */
	public function getNbRetards()
	{
		return $this->nb_retards;
	}

	/**
	 * Get the [nb_retards_justifies] column value.
	 * Nombre de retards justifiés décomptés dans la demi journée
	 * @return     int
	 */
	public function getNbRetardsJustifies()
	{
		return $this->nb_retards_justifies;
	}

	/**
	 * Get the [motifs_absences] column value.
	 * Liste des motifs (table a_motifs) associés à cette demi-journée d'absence
	 * @return     array
	 */
	public function getMotifsAbsences()
	{
		if (null === $this->motifs_absences_unserialized) {
			$this->motifs_absences_unserialized = array();
		}
		if (!$this->motifs_absences_unserialized && null !== $this->motifs_absences) {
			$motifs_absences_unserialized = substr($this->motifs_absences, 2, -2);
			$this->motifs_absences_unserialized = $motifs_absences_unserialized ? explode(' | ', $motifs_absences_unserialized) : array();
		}
		return $this->motifs_absences_unserialized;
	}

	/**
	 * Test the presence of a value in the [motifs_absences] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés à cette demi-journée d'absence
	 * @return     Boolean
	 */
	public function hasMotifsAbsence($value)
	{
		return in_array($value, $this->getMotifsAbsences());
	} // hasMotifsAbsence()

	/**
	 * Get the [motifs_retards] column value.
	 * Liste des motifs (table a_motifs) associés aux retard de cette demi-journée
	 * @return     array
	 */
	public function getMotifsRetards()
	{
		if (null === $this->motifs_retards_unserialized) {
			$this->motifs_retards_unserialized = array();
		}
		if (!$this->motifs_retards_unserialized && null !== $this->motifs_retards) {
			$motifs_retards_unserialized = substr($this->motifs_retards, 2, -2);
			$this->motifs_retards_unserialized = $motifs_retards_unserialized ? explode(' | ', $motifs_retards_unserialized) : array();
		}
		return $this->motifs_retards_unserialized;
	}

	/**
	 * Test the presence of a value in the [motifs_retards] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés aux retard de cette demi-journée
	 * @return     Boolean
	 */
	public function hasMotifsRetard($value)
	{
		return in_array($value, $this->getMotifsRetards());
	} // hasMotifsRetard()

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
	 * Set the value of [eleve_id] column.
	 * id de l'eleve
	 * @param      int $v new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setEleveId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->eleve_id !== $v) {
			$this->eleve_id = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::ELEVE_ID;
		}

		if ($this->aEleve !== null && $this->aEleve->getIdEleve() !== $v) {
			$this->aEleve = null;
		}

		return $this;
	} // setEleveId()

	/**
	 * Sets the value of [date_demi_jounee] column to a normalized version of the date/time value specified.
	 * Date de la demi journée agrégée : 00:00 pour une matinée, 12:00 pour une après midi
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setDateDemiJounee($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->date_demi_jounee !== null || $dt !== null) {
			$currentDateAsString = ($this->date_demi_jounee !== null && $tmpDt = new DateTime($this->date_demi_jounee)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match 
				|| ($dt->format('Y-m-d H:i:s') === NULL) // or the entered value matches the default
				 ) {
				$this->date_demi_jounee = $newDateAsString;
				$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE;
			}
		} // if either are not null

		return $this;
	} // setDateDemiJounee()

	/**
	 * Sets the value of the [manquement_obligation_presence] column. 
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * Cette demi journée est comptée comme absence
	 * @param      boolean|integer|string $v The new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setManquementObligationPresence($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->manquement_obligation_presence !== $v || $this->isNew()) {
			$this->manquement_obligation_presence = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::MANQUEMENT_OBLIGATION_PRESENCE;
		}

		return $this;
	} // setManquementObligationPresence()

	/**
	 * Sets the value of the [justifiee] column. 
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * Si cette demi journée est compté comme absence, y a-t-il une justification
	 * @param      boolean|integer|string $v The new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setJustifiee($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->justifiee !== $v || $this->isNew()) {
			$this->justifiee = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::JUSTIFIEE;
		}

		return $this;
	} // setJustifiee()

	/**
	 * Sets the value of the [notifiee] column. 
	 * Non-boolean arguments are converted using the following rules:
	 *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
	 *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
	 * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
	 * Si cette demi journée est compté comme absence, y a-t-il une notification à la famille
	 * @param      boolean|integer|string $v The new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setNotifiee($v)
	{
		if ($v !== null) {
			if (is_string($v)) {
				$v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
			} else {
				$v = (boolean) $v;
			}
		}

		if ($this->notifiee !== $v || $this->isNew()) {
			$this->notifiee = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::NOTIFIEE;
		}

		return $this;
	} // setNotifiee()

	/**
	 * Set the value of [nb_retards] column.
	 * Nombre de retards décomptés dans la demi journée
	 * @param      int $v new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setNbRetards($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->nb_retards !== $v || $this->isNew()) {
			$this->nb_retards = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::NB_RETARDS;
		}

		return $this;
	} // setNbRetards()

	/**
	 * Set the value of [nb_retards_justifies] column.
	 * Nombre de retards justifiés décomptés dans la demi journée
	 * @param      int $v new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setNbRetardsJustifies($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->nb_retards_justifies !== $v || $this->isNew()) {
			$this->nb_retards_justifies = $v;
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::NB_RETARDS_JUSTIFIES;
		}

		return $this;
	} // setNbRetardsJustifies()

	/**
	 * Set the value of [motifs_absences] column.
	 * Liste des motifs (table a_motifs) associés à cette demi-journée d'absence
	 * @param      array $v new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setMotifsAbsences($v)
	{
		if ($this->motifs_absences_unserialized !== $v) {
			$this->motifs_absences_unserialized = $v;
			$this->motifs_absences = '| ' . implode(' | ', $v) . ' |';
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES;
		}

		return $this;
	} // setMotifsAbsences()

	/**
	 * Adds a value to the [motifs_absences] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés à cette demi-journée d'absence
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function addMotifsAbsence($value)
	{
		$currentArray = $this->getMotifsAbsences();
		$currentArray []= $value;
		$this->setMotifsAbsences($currentArray);
		
		return $this;
	} // addMotifsAbsence()

	/**
	 * Removes a value from the [motifs_absences] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés à cette demi-journée d'absence
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function removeMotifsAbsence($value)
	{
		$targetArray = array();
		foreach ($this->getMotifsAbsences() as $element) {
			if ($element != $value) {
				$targetArray []= $element;
			}
		}
		$this->setMotifsAbsences($targetArray);

		return $this;
	} // removeMotifsAbsence()

	/**
	 * Set the value of [motifs_retards] column.
	 * Liste des motifs (table a_motifs) associés aux retard de cette demi-journée
	 * @param      array $v new value
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setMotifsRetards($v)
	{
		if ($this->motifs_retards_unserialized !== $v) {
			$this->motifs_retards_unserialized = $v;
			$this->motifs_retards = '| ' . implode(' | ', $v) . ' |';
			$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::MOTIFS_RETARDS;
		}

		return $this;
	} // setMotifsRetards()

	/**
	 * Adds a value to the [motifs_retards] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés aux retard de cette demi-journée
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function addMotifsRetard($value)
	{
		$currentArray = $this->getMotifsRetards();
		$currentArray []= $value;
		$this->setMotifsRetards($currentArray);
		
		return $this;
	} // addMotifsRetard()

	/**
	 * Removes a value from the [motifs_retards] array column value.
	 * @param      mixed $value
	 * Liste des motifs (table a_motifs) associés aux retard de cette demi-journée
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function removeMotifsRetard($value)
	{
		$targetArray = array();
		foreach ($this->getMotifsRetards() as $element) {
			if ($element != $value) {
				$targetArray []= $element;
			}
		}
		$this->setMotifsRetards($targetArray);

		return $this;
	} // removeMotifsRetard()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->created_at !== null || $dt !== null) {
			$currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->created_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.
	 *               Empty strings are treated as NULL.
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		$dt = PropelDateTime::newInstance($v, null, 'DateTime');
		if ($this->updated_at !== null || $dt !== null) {
			$currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
			if ($currentDateAsString !== $newDateAsString) {
				$this->updated_at = $newDateAsString;
				$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::UPDATED_AT;
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
			if ($this->date_demi_jounee !== NULL) {
				return false;
			}

			if ($this->manquement_obligation_presence !== false) {
				return false;
			}

			if ($this->justifiee !== false) {
				return false;
			}

			if ($this->notifiee !== false) {
				return false;
			}

			if ($this->nb_retards !== 0) {
				return false;
			}

			if ($this->nb_retards_justifies !== 0) {
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

			$this->eleve_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->date_demi_jounee = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->manquement_obligation_presence = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
			$this->justifiee = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
			$this->notifiee = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
			$this->nb_retards = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->nb_retards_justifies = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->motifs_absences = $row[$startcol + 7];
			$this->motifs_retards = $row[$startcol + 8];
			$this->created_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->updated_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 11; // 11 = AbsenceAgregationDecomptePeer::NUM_HYDRATE_COLUMNS.

		} catch (Exception $e) {
			throw new PropelException("Error populating AbsenceAgregationDecompte object", $e);
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

		if ($this->aEleve !== null && $this->eleve_id !== $this->aEleve->getIdEleve()) {
			$this->aEleve = null;
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
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AbsenceAgregationDecomptePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aEleve = null;
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
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				AbsenceAgregationDecompteQuery::create()
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
			$con = Propel::getConnection(AbsenceAgregationDecomptePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
				// timestampable behavior
				if (!$this->isColumnModified(AbsenceAgregationDecomptePeer::CREATED_AT)) {
					$this->setCreatedAt(time());
				}
				if (!$this->isColumnModified(AbsenceAgregationDecomptePeer::UPDATED_AT)) {
					$this->setUpdatedAt(time());
				}
			} else {
				$ret = $ret && $this->preUpdate($con);
				// timestampable behavior
				if ($this->isModified() && !$this->isColumnModified(AbsenceAgregationDecomptePeer::UPDATED_AT)) {
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
				AbsenceAgregationDecomptePeer::addInstanceToPool($this);
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

			if ($this->aEleve !== null) {
				if ($this->aEleve->isModified() || $this->aEleve->isNew()) {
					$affectedRows += $this->aEleve->save($con);
				}
				$this->setEleve($this->aEleve);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setNew(false);
				} else {
					$affectedRows += AbsenceAgregationDecomptePeer::doUpdate($this, $con);
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

			if ($this->aEleve !== null) {
				if (!$this->aEleve->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aEleve->getValidationFailures());
				}
			}


			if (($retval = AbsenceAgregationDecomptePeer::doValidate($this, $columns)) !== true) {
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
		$pos = AbsenceAgregationDecomptePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getEleveId();
				break;
			case 1:
				return $this->getDateDemiJounee();
				break;
			case 2:
				return $this->getManquementObligationPresence();
				break;
			case 3:
				return $this->getJustifiee();
				break;
			case 4:
				return $this->getNotifiee();
				break;
			case 5:
				return $this->getNbRetards();
				break;
			case 6:
				return $this->getNbRetardsJustifies();
				break;
			case 7:
				return $this->getMotifsAbsences();
				break;
			case 8:
				return $this->getMotifsRetards();
				break;
			case 9:
				return $this->getCreatedAt();
				break;
			case 10:
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
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		if (isset($alreadyDumpedObjects['AbsenceAgregationDecompte'][serialize($this->getPrimaryKey())])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AbsenceAgregationDecompte'][serialize($this->getPrimaryKey())] = true;
		$keys = AbsenceAgregationDecomptePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getEleveId(),
			$keys[1] => $this->getDateDemiJounee(),
			$keys[2] => $this->getManquementObligationPresence(),
			$keys[3] => $this->getJustifiee(),
			$keys[4] => $this->getNotifiee(),
			$keys[5] => $this->getNbRetards(),
			$keys[6] => $this->getNbRetardsJustifies(),
			$keys[7] => $this->getMotifsAbsences(),
			$keys[8] => $this->getMotifsRetards(),
			$keys[9] => $this->getCreatedAt(),
			$keys[10] => $this->getUpdatedAt(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aEleve) {
				$result['Eleve'] = $this->aEleve->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
		$pos = AbsenceAgregationDecomptePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setEleveId($value);
				break;
			case 1:
				$this->setDateDemiJounee($value);
				break;
			case 2:
				$this->setManquementObligationPresence($value);
				break;
			case 3:
				$this->setJustifiee($value);
				break;
			case 4:
				$this->setNotifiee($value);
				break;
			case 5:
				$this->setNbRetards($value);
				break;
			case 6:
				$this->setNbRetardsJustifies($value);
				break;
			case 7:
				$this->setMotifsAbsences($value);
				break;
			case 8:
				$this->setMotifsRetards($value);
				break;
			case 9:
				$this->setCreatedAt($value);
				break;
			case 10:
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
		$keys = AbsenceAgregationDecomptePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setEleveId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDateDemiJounee($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setManquementObligationPresence($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setJustifiee($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setNotifiee($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setNbRetards($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setNbRetardsJustifies($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setMotifsAbsences($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMotifsRetards($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AbsenceAgregationDecomptePeer::DATABASE_NAME);

		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::ELEVE_ID)) $criteria->add(AbsenceAgregationDecomptePeer::ELEVE_ID, $this->eleve_id);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE)) $criteria->add(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $this->date_demi_jounee);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::MANQUEMENT_OBLIGATION_PRESENCE)) $criteria->add(AbsenceAgregationDecomptePeer::MANQUEMENT_OBLIGATION_PRESENCE, $this->manquement_obligation_presence);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::JUSTIFIEE)) $criteria->add(AbsenceAgregationDecomptePeer::JUSTIFIEE, $this->justifiee);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::NOTIFIEE)) $criteria->add(AbsenceAgregationDecomptePeer::NOTIFIEE, $this->notifiee);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::NB_RETARDS)) $criteria->add(AbsenceAgregationDecomptePeer::NB_RETARDS, $this->nb_retards);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::NB_RETARDS_JUSTIFIES)) $criteria->add(AbsenceAgregationDecomptePeer::NB_RETARDS_JUSTIFIES, $this->nb_retards_justifies);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES)) $criteria->add(AbsenceAgregationDecomptePeer::MOTIFS_ABSENCES, $this->motifs_absences);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS)) $criteria->add(AbsenceAgregationDecomptePeer::MOTIFS_RETARDS, $this->motifs_retards);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::CREATED_AT)) $criteria->add(AbsenceAgregationDecomptePeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(AbsenceAgregationDecomptePeer::UPDATED_AT)) $criteria->add(AbsenceAgregationDecomptePeer::UPDATED_AT, $this->updated_at);

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
		$criteria = new Criteria(AbsenceAgregationDecomptePeer::DATABASE_NAME);
		$criteria->add(AbsenceAgregationDecomptePeer::ELEVE_ID, $this->eleve_id);
		$criteria->add(AbsenceAgregationDecomptePeer::DATE_DEMI_JOUNEE, $this->date_demi_jounee);

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
		$pks[0] = $this->getEleveId();
		$pks[1] = $this->getDateDemiJounee();

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
		$this->setEleveId($keys[0]);
		$this->setDateDemiJounee($keys[1]);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return (null === $this->getEleveId()) && (null === $this->getDateDemiJounee());
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AbsenceAgregationDecompte (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setEleveId($this->getEleveId());
		$copyObj->setDateDemiJounee($this->getDateDemiJounee());
		$copyObj->setManquementObligationPresence($this->getManquementObligationPresence());
		$copyObj->setJustifiee($this->getJustifiee());
		$copyObj->setNotifiee($this->getNotifiee());
		$copyObj->setNbRetards($this->getNbRetards());
		$copyObj->setNbRetardsJustifies($this->getNbRetardsJustifies());
		$copyObj->setMotifsAbsences($this->getMotifsAbsences());
		$copyObj->setMotifsRetards($this->getMotifsRetards());
		$copyObj->setCreatedAt($this->getCreatedAt());
		$copyObj->setUpdatedAt($this->getUpdatedAt());
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
	 * @return     AbsenceAgregationDecompte Clone of current object.
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
	 * @return     AbsenceAgregationDecomptePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AbsenceAgregationDecomptePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Eleve object.
	 *
	 * @param      Eleve $v
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
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
			$v->addAbsenceAgregationDecompte($this);
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
				$this->aEleve->addAbsenceAgregationDecomptes($this);
			 */
		}
		return $this->aEleve;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->eleve_id = null;
		$this->date_demi_jounee = null;
		$this->manquement_obligation_presence = null;
		$this->justifiee = null;
		$this->notifiee = null;
		$this->nb_retards = null;
		$this->nb_retards_justifies = null;
		$this->motifs_absences = null;
		$this->motifs_retards = null;
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

		$this->aEleve = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AbsenceAgregationDecomptePeer::DEFAULT_STRING_FORMAT);
	}

	// timestampable behavior
	
	/**
	 * Mark the current object so that the update date doesn't get updated during next save
	 *
	 * @return     AbsenceAgregationDecompte The current object (for fluent API support)
	 */
	public function keepUpdateDateUnchanged()
	{
		$this->modifiedColumns[] = AbsenceAgregationDecomptePeer::UPDATED_AT;
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

} // BaseAbsenceAgregationDecompte
