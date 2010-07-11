<?php


/**
 * Base class that represents a row from the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseEdtCalendrierPeriode extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'EdtCalendrierPeriodePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        EdtCalendrierPeriodePeer
	 */
	protected static $peer;

	/**
	 * The value for the id_calendrier field.
	 * @var        int
	 */
	protected $id_calendrier;

	/**
	 * The value for the classe_concerne_calendrier field.
	 * @var        string
	 */
	protected $classe_concerne_calendrier;

	/**
	 * The value for the nom_calendrier field.
	 * @var        string
	 */
	protected $nom_calendrier;

	/**
	 * The value for the debut_calendrier_ts field.
	 * @var        string
	 */
	protected $debut_calendrier_ts;

	/**
	 * The value for the fin_calendrier_ts field.
	 * @var        string
	 */
	protected $fin_calendrier_ts;

	/**
	 * The value for the jourdebut_calendrier field.
	 * @var        string
	 */
	protected $jourdebut_calendrier;

	/**
	 * The value for the heuredebut_calendrier field.
	 * @var        string
	 */
	protected $heuredebut_calendrier;

	/**
	 * The value for the jourfin_calendrier field.
	 * @var        string
	 */
	protected $jourfin_calendrier;

	/**
	 * The value for the heurefin_calendrier field.
	 * @var        string
	 */
	protected $heurefin_calendrier;

	/**
	 * The value for the numero_periode field.
	 * @var        int
	 */
	protected $numero_periode;

	/**
	 * The value for the etabferme_calendrier field.
	 * @var        int
	 */
	protected $etabferme_calendrier;

	/**
	 * The value for the etabvacances_calendrier field.
	 * @var        int
	 */
	protected $etabvacances_calendrier;

	/**
	 * @var        array EdtEmplacementCours[] Collection to store aggregation of EdtEmplacementCours objects.
	 */
	protected $collEdtEmplacementCourss;

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
	 * Get the [id_calendrier] column value.
	 * cle primaire
	 * @return     int
	 */
	public function getIdCalendrier()
	{
		return $this->id_calendrier;
	}

	/**
	 * Get the [classe_concerne_calendrier] column value.
	 * id des classes (separes par des ;) concernees par cette periode
	 * @return     string
	 */
	public function getClasseConcerneCalendrier()
	{
		return $this->classe_concerne_calendrier;
	}

	/**
	 * Get the [nom_calendrier] column value.
	 * nom de la periode definie
	 * @return     string
	 */
	public function getNomCalendrier()
	{
		return $this->nom_calendrier;
	}

	/**
	 * Get the [debut_calendrier_ts] column value.
	 * timestamp du debut de la periode
	 * @return     string
	 */
	public function getDebutCalendrierTs()
	{
		return $this->debut_calendrier_ts;
	}

	/**
	 * Get the [fin_calendrier_ts] column value.
	 * timestamp de la fin de la periode
	 * @return     string
	 */
	public function getFinCalendrierTs()
	{
		return $this->fin_calendrier_ts;
	}

	/**
	 * Get the [optionally formatted] temporal [jourdebut_calendrier] column value.
	 * date du debut de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getJourdebutCalendrier($format = '%x')
	{
		if ($this->jourdebut_calendrier === null) {
			return null;
		}


		if ($this->jourdebut_calendrier === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->jourdebut_calendrier);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->jourdebut_calendrier, true), $x);
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
	 * Get the [optionally formatted] temporal [heuredebut_calendrier] column value.
	 * heure du debut de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getHeuredebutCalendrier($format = '%X')
	{
		if ($this->heuredebut_calendrier === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->heuredebut_calendrier);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->heuredebut_calendrier, true), $x);
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
	 * Get the [optionally formatted] temporal [jourfin_calendrier] column value.
	 * date de la fin de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getJourfinCalendrier($format = '%x')
	{
		if ($this->jourfin_calendrier === null) {
			return null;
		}


		if ($this->jourfin_calendrier === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->jourfin_calendrier);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->jourfin_calendrier, true), $x);
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
	 * Get the [optionally formatted] temporal [heurefin_calendrier] column value.
	 * heure de la fin de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getHeurefinCalendrier($format = '%X')
	{
		if ($this->heurefin_calendrier === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->heurefin_calendrier);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->heurefin_calendrier, true), $x);
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
	 * Get the [numero_periode] column value.
	 * id de la periode de notes associee
	 * @return     int
	 */
	public function getNumeroPeriode()
	{
		return $this->numero_periode;
	}

	/**
	 * Get the [etabferme_calendrier] column value.
	 * egal a 1 si etablissement ouvert sur cette periode - 0 sinon
	 * @return     int
	 */
	public function getEtabfermeCalendrier()
	{
		return $this->etabferme_calendrier;
	}

	/**
	 * Get the [etabvacances_calendrier] column value.
	 * egal a 1 si la periode est definie sur les vacances - 0 sinon
	 * @return     int
	 */
	public function getEtabvacancesCalendrier()
	{
		return $this->etabvacances_calendrier;
	}

	/**
	 * Set the value of [id_calendrier] column.
	 * cle primaire
	 * @param      int $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setIdCalendrier($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id_calendrier !== $v) {
			$this->id_calendrier = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::ID_CALENDRIER;
		}

		return $this;
	} // setIdCalendrier()

	/**
	 * Set the value of [classe_concerne_calendrier] column.
	 * id des classes (separes par des ;) concernees par cette periode
	 * @param      string $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setClasseConcerneCalendrier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->classe_concerne_calendrier !== $v) {
			$this->classe_concerne_calendrier = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::CLASSE_CONCERNE_CALENDRIER;
		}

		return $this;
	} // setClasseConcerneCalendrier()

	/**
	 * Set the value of [nom_calendrier] column.
	 * nom de la periode definie
	 * @param      string $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setNomCalendrier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_calendrier !== $v) {
			$this->nom_calendrier = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::NOM_CALENDRIER;
		}

		return $this;
	} // setNomCalendrier()

	/**
	 * Set the value of [debut_calendrier_ts] column.
	 * timestamp du debut de la periode
	 * @param      string $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setDebutCalendrierTs($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->debut_calendrier_ts !== $v) {
			$this->debut_calendrier_ts = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::DEBUT_CALENDRIER_TS;
		}

		return $this;
	} // setDebutCalendrierTs()

	/**
	 * Set the value of [fin_calendrier_ts] column.
	 * timestamp de la fin de la periode
	 * @param      string $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setFinCalendrierTs($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fin_calendrier_ts !== $v) {
			$this->fin_calendrier_ts = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::FIN_CALENDRIER_TS;
		}

		return $this;
	} // setFinCalendrierTs()

	/**
	 * Sets the value of [jourdebut_calendrier] column to a normalized version of the date/time value specified.
	 * date du debut de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setJourdebutCalendrier($v)
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

		if ( $this->jourdebut_calendrier !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->jourdebut_calendrier !== null && $tmpDt = new DateTime($this->jourdebut_calendrier)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->jourdebut_calendrier = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER;
			}
		} // if either are not null

		return $this;
	} // setJourdebutCalendrier()

	/**
	 * Sets the value of [heuredebut_calendrier] column to a normalized version of the date/time value specified.
	 * heure du debut de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setHeuredebutCalendrier($v)
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

		if ( $this->heuredebut_calendrier !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->heuredebut_calendrier !== null && $tmpDt = new DateTime($this->heuredebut_calendrier)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->heuredebut_calendrier = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER;
			}
		} // if either are not null

		return $this;
	} // setHeuredebutCalendrier()

	/**
	 * Sets the value of [jourfin_calendrier] column to a normalized version of the date/time value specified.
	 * date de la fin de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setJourfinCalendrier($v)
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

		if ( $this->jourfin_calendrier !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->jourfin_calendrier !== null && $tmpDt = new DateTime($this->jourfin_calendrier)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->jourfin_calendrier = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER;
			}
		} // if either are not null

		return $this;
	} // setJourfinCalendrier()

	/**
	 * Sets the value of [heurefin_calendrier] column to a normalized version of the date/time value specified.
	 * heure de la fin de la periode
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setHeurefinCalendrier($v)
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

		if ( $this->heurefin_calendrier !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->heurefin_calendrier !== null && $tmpDt = new DateTime($this->heurefin_calendrier)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->heurefin_calendrier = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER;
			}
		} // if either are not null

		return $this;
	} // setHeurefinCalendrier()

	/**
	 * Set the value of [numero_periode] column.
	 * id de la periode de notes associee
	 * @param      int $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setNumeroPeriode($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->numero_periode !== $v) {
			$this->numero_periode = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::NUMERO_PERIODE;
		}

		return $this;
	} // setNumeroPeriode()

	/**
	 * Set the value of [etabferme_calendrier] column.
	 * egal a 1 si etablissement ouvert sur cette periode - 0 sinon
	 * @param      int $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setEtabfermeCalendrier($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->etabferme_calendrier !== $v) {
			$this->etabferme_calendrier = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER;
		}

		return $this;
	} // setEtabfermeCalendrier()

	/**
	 * Set the value of [etabvacances_calendrier] column.
	 * egal a 1 si la periode est definie sur les vacances - 0 sinon
	 * @param      int $v new value
	 * @return     EdtCalendrierPeriode The current object (for fluent API support)
	 */
	public function setEtabvacancesCalendrier($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->etabvacances_calendrier !== $v) {
			$this->etabvacances_calendrier = $v;
			$this->modifiedColumns[] = EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER;
		}

		return $this;
	} // setEtabvacancesCalendrier()

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

			$this->id_calendrier = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->classe_concerne_calendrier = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom_calendrier = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->debut_calendrier_ts = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->fin_calendrier_ts = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->jourdebut_calendrier = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->heuredebut_calendrier = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->jourfin_calendrier = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->heurefin_calendrier = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->numero_periode = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->etabferme_calendrier = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->etabvacances_calendrier = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 12; // 12 = EdtCalendrierPeriodePeer::NUM_COLUMNS - EdtCalendrierPeriodePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating EdtCalendrierPeriode object", $e);
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
			$con = Propel::getConnection(EdtCalendrierPeriodePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = EdtCalendrierPeriodePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collEdtEmplacementCourss = null;

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
			$con = Propel::getConnection(EdtCalendrierPeriodePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				EdtCalendrierPeriodeQuery::create()
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
			$con = Propel::getConnection(EdtCalendrierPeriodePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				EdtCalendrierPeriodePeer::addInstanceToPool($this);
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
					$affectedRows = EdtCalendrierPeriodePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
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


			if (($retval = EdtCalendrierPeriodePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
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
		$pos = EdtCalendrierPeriodePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getIdCalendrier();
				break;
			case 1:
				return $this->getClasseConcerneCalendrier();
				break;
			case 2:
				return $this->getNomCalendrier();
				break;
			case 3:
				return $this->getDebutCalendrierTs();
				break;
			case 4:
				return $this->getFinCalendrierTs();
				break;
			case 5:
				return $this->getJourdebutCalendrier();
				break;
			case 6:
				return $this->getHeuredebutCalendrier();
				break;
			case 7:
				return $this->getJourfinCalendrier();
				break;
			case 8:
				return $this->getHeurefinCalendrier();
				break;
			case 9:
				return $this->getNumeroPeriode();
				break;
			case 10:
				return $this->getEtabfermeCalendrier();
				break;
			case 11:
				return $this->getEtabvacancesCalendrier();
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
		$keys = EdtCalendrierPeriodePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getIdCalendrier(),
			$keys[1] => $this->getClasseConcerneCalendrier(),
			$keys[2] => $this->getNomCalendrier(),
			$keys[3] => $this->getDebutCalendrierTs(),
			$keys[4] => $this->getFinCalendrierTs(),
			$keys[5] => $this->getJourdebutCalendrier(),
			$keys[6] => $this->getHeuredebutCalendrier(),
			$keys[7] => $this->getJourfinCalendrier(),
			$keys[8] => $this->getHeurefinCalendrier(),
			$keys[9] => $this->getNumeroPeriode(),
			$keys[10] => $this->getEtabfermeCalendrier(),
			$keys[11] => $this->getEtabvacancesCalendrier(),
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
		$pos = EdtCalendrierPeriodePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setIdCalendrier($value);
				break;
			case 1:
				$this->setClasseConcerneCalendrier($value);
				break;
			case 2:
				$this->setNomCalendrier($value);
				break;
			case 3:
				$this->setDebutCalendrierTs($value);
				break;
			case 4:
				$this->setFinCalendrierTs($value);
				break;
			case 5:
				$this->setJourdebutCalendrier($value);
				break;
			case 6:
				$this->setHeuredebutCalendrier($value);
				break;
			case 7:
				$this->setJourfinCalendrier($value);
				break;
			case 8:
				$this->setHeurefinCalendrier($value);
				break;
			case 9:
				$this->setNumeroPeriode($value);
				break;
			case 10:
				$this->setEtabfermeCalendrier($value);
				break;
			case 11:
				$this->setEtabvacancesCalendrier($value);
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
		$keys = EdtCalendrierPeriodePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setIdCalendrier($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setClasseConcerneCalendrier($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNomCalendrier($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDebutCalendrierTs($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFinCalendrierTs($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setJourdebutCalendrier($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setHeuredebutCalendrier($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setJourfinCalendrier($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setHeurefinCalendrier($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setNumeroPeriode($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setEtabfermeCalendrier($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setEtabvacancesCalendrier($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(EdtCalendrierPeriodePeer::DATABASE_NAME);

		if ($this->isColumnModified(EdtCalendrierPeriodePeer::ID_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::ID_CALENDRIER, $this->id_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::CLASSE_CONCERNE_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::CLASSE_CONCERNE_CALENDRIER, $this->classe_concerne_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::NOM_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::NOM_CALENDRIER, $this->nom_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::DEBUT_CALENDRIER_TS)) $criteria->add(EdtCalendrierPeriodePeer::DEBUT_CALENDRIER_TS, $this->debut_calendrier_ts);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::FIN_CALENDRIER_TS)) $criteria->add(EdtCalendrierPeriodePeer::FIN_CALENDRIER_TS, $this->fin_calendrier_ts);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER, $this->jourdebut_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::HEUREDEBUT_CALENDRIER, $this->heuredebut_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::JOURFIN_CALENDRIER, $this->jourfin_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::HEUREFIN_CALENDRIER, $this->heurefin_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::NUMERO_PERIODE)) $criteria->add(EdtCalendrierPeriodePeer::NUMERO_PERIODE, $this->numero_periode);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::ETABFERME_CALENDRIER, $this->etabferme_calendrier);
		if ($this->isColumnModified(EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER)) $criteria->add(EdtCalendrierPeriodePeer::ETABVACANCES_CALENDRIER, $this->etabvacances_calendrier);

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
		$criteria = new Criteria(EdtCalendrierPeriodePeer::DATABASE_NAME);
		$criteria->add(EdtCalendrierPeriodePeer::ID_CALENDRIER, $this->id_calendrier);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getIdCalendrier();
	}

	/**
	 * Generic method to set the primary key (id_calendrier column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setIdCalendrier($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getIdCalendrier();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of EdtCalendrierPeriode (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setIdCalendrier($this->id_calendrier);
		$copyObj->setClasseConcerneCalendrier($this->classe_concerne_calendrier);
		$copyObj->setNomCalendrier($this->nom_calendrier);
		$copyObj->setDebutCalendrierTs($this->debut_calendrier_ts);
		$copyObj->setFinCalendrierTs($this->fin_calendrier_ts);
		$copyObj->setJourdebutCalendrier($this->jourdebut_calendrier);
		$copyObj->setHeuredebutCalendrier($this->heuredebut_calendrier);
		$copyObj->setJourfinCalendrier($this->jourfin_calendrier);
		$copyObj->setHeurefinCalendrier($this->heurefin_calendrier);
		$copyObj->setNumeroPeriode($this->numero_periode);
		$copyObj->setEtabfermeCalendrier($this->etabferme_calendrier);
		$copyObj->setEtabvacancesCalendrier($this->etabvacances_calendrier);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

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
	 * @return     EdtCalendrierPeriode Clone of current object.
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
	 * @return     EdtCalendrierPeriodePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new EdtCalendrierPeriodePeer();
		}
		return self::$peer;
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
	 * If this EdtCalendrierPeriode is new, it will return
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
					->filterByEdtCalendrierPeriode($this)
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
					->filterByEdtCalendrierPeriode($this)
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
			$l->setEdtCalendrierPeriode($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this EdtCalendrierPeriode is new, it will return
	 * an empty collection; or if this EdtCalendrierPeriode has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCalendrierPeriode.
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
	 * Otherwise if this EdtCalendrierPeriode is new, it will return
	 * an empty collection; or if this EdtCalendrierPeriode has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCalendrierPeriode.
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
	 * Otherwise if this EdtCalendrierPeriode is new, it will return
	 * an empty collection; or if this EdtCalendrierPeriode has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCalendrierPeriode.
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
	 * Otherwise if this EdtCalendrierPeriode is new, it will return
	 * an empty collection; or if this EdtCalendrierPeriode has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCalendrierPeriode.
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
	 * Otherwise if this EdtCalendrierPeriode is new, it will return
	 * an empty collection; or if this EdtCalendrierPeriode has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in EdtCalendrierPeriode.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array EdtEmplacementCours[] List of EdtEmplacementCours objects
	 */
	public function getEdtEmplacementCourssJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = EdtEmplacementCoursQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getEdtEmplacementCourss($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id_calendrier = null;
		$this->classe_concerne_calendrier = null;
		$this->nom_calendrier = null;
		$this->debut_calendrier_ts = null;
		$this->fin_calendrier_ts = null;
		$this->jourdebut_calendrier = null;
		$this->heuredebut_calendrier = null;
		$this->jourfin_calendrier = null;
		$this->heurefin_calendrier = null;
		$this->numero_periode = null;
		$this->etabferme_calendrier = null;
		$this->etabvacances_calendrier = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
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
			if ($this->collEdtEmplacementCourss) {
				foreach ((array) $this->collEdtEmplacementCourss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

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

} // BaseEdtCalendrierPeriode
