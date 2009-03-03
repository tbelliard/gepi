<?php

/**
 * Base class that represents a row from the 'aid' table.
 *
 * Liste des AID (Activites Inter-Disciplinaires)
 *
 * @package    gepi.om
 */
abstract class BaseAidDetails extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AidDetailsPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        string
	 */
	protected $id;

	/**
	 * The value for the nom field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $nom;

	/**
	 * The value for the numero field.
	 * Note: this column has a database default value of: '0'
	 * @var        string
	 */
	protected $numero;

	/**
	 * The value for the indice_aid field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $indice_aid;

	/**
	 * The value for the perso1 field.
	 * @var        string
	 */
	protected $perso1;

	/**
	 * The value for the perso2 field.
	 * @var        string
	 */
	protected $perso2;

	/**
	 * The value for the perso3 field.
	 * @var        string
	 */
	protected $perso3;

	/**
	 * The value for the productions field.
	 * @var        string
	 */
	protected $productions;

	/**
	 * The value for the resume field.
	 * @var        string
	 */
	protected $resume;

	/**
	 * The value for the famille field.
	 * @var        int
	 */
	protected $famille;

	/**
	 * The value for the mots_cles field.
	 * @var        string
	 */
	protected $mots_cles;

	/**
	 * The value for the adresse1 field.
	 * @var        string
	 */
	protected $adresse1;

	/**
	 * The value for the adresse2 field.
	 * @var        string
	 */
	protected $adresse2;

	/**
	 * The value for the public_destinataire field.
	 * @var        string
	 */
	protected $public_destinataire;

	/**
	 * The value for the contacts field.
	 * @var        string
	 */
	protected $contacts;

	/**
	 * The value for the divers field.
	 * @var        string
	 */
	protected $divers;

	/**
	 * The value for the matiere1 field.
	 * @var        string
	 */
	protected $matiere1;

	/**
	 * The value for the matiere2 field.
	 * @var        string
	 */
	protected $matiere2;

	/**
	 * The value for the eleve_peut_modifier field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $eleve_peut_modifier;

	/**
	 * The value for the prof_peut_modifier field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $prof_peut_modifier;

	/**
	 * The value for the cpe_peut_modifier field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $cpe_peut_modifier;

	/**
	 * The value for the fiche_publique field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $fiche_publique;

	/**
	 * The value for the affiche_adresse1 field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $affiche_adresse1;

	/**
	 * The value for the en_construction field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $en_construction;

	/**
	 * @var        AidConfiguration
	 */
	protected $aAidConfiguration;

	/**
	 * @var        array JAidUtilisateursProfessionnels[] Collection to store aggregation of JAidUtilisateursProfessionnels objects.
	 */
	protected $collJAidUtilisateursProfessionnelss;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJAidUtilisateursProfessionnelss.
	 */
	private $lastJAidUtilisateursProfessionnelsCriteria = null;

	/**
	 * @var        array JAidEleves[] Collection to store aggregation of JAidEleves objects.
	 */
	protected $collJAidElevess;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJAidElevess.
	 */
	private $lastJAidElevesCriteria = null;

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
	 * Initializes internal state of BaseAidDetails object.
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
		$this->nom = '';
		$this->numero = '0';
		$this->indice_aid = 0;
		$this->eleve_peut_modifier = 'n';
		$this->prof_peut_modifier = 'n';
		$this->cpe_peut_modifier = 'n';
		$this->fiche_publique = 'n';
		$this->affiche_adresse1 = 'n';
		$this->en_construction = 'n';
	}

	/**
	 * Get the [id] column value.
	 * cle primaire auto-incremente
	 * @return     string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [nom] column value.
	 * Nom de l'AID
	 * @return     string
	 */
	public function getNom()
	{
		return $this->nom;
	}

	/**
	 * Get the [numero] column value.
	 * Numero d'ordre d'affichage
	 * @return     string
	 */
	public function getNumero()
	{
		return $this->numero;
	}

	/**
	 * Get the [indice_aid] column value.
	 * Cle etrangere, vers la liste des categories d'AID (aid_config)
	 * @return     int
	 */
	public function getIndiceAid()
	{
		return $this->indice_aid;
	}

	/**
	 * Get the [perso1] column value.
	 * 
	 * @return     string
	 */
	public function getPerso1()
	{
		return $this->perso1;
	}

	/**
	 * Get the [perso2] column value.
	 * 
	 * @return     string
	 */
	public function getPerso2()
	{
		return $this->perso2;
	}

	/**
	 * Get the [perso3] column value.
	 * 
	 * @return     string
	 */
	public function getPerso3()
	{
		return $this->perso3;
	}

	/**
	 * Get the [productions] column value.
	 * 
	 * @return     string
	 */
	public function getProductions()
	{
		return $this->productions;
	}

	/**
	 * Get the [resume] column value.
	 * 
	 * @return     string
	 */
	public function getResume()
	{
		return $this->resume;
	}

	/**
	 * Get the [famille] column value.
	 * 
	 * @return     int
	 */
	public function getFamille()
	{
		return $this->famille;
	}

	/**
	 * Get the [mots_cles] column value.
	 * 
	 * @return     string
	 */
	public function getMotsCles()
	{
		return $this->mots_cles;
	}

	/**
	 * Get the [adresse1] column value.
	 * 
	 * @return     string
	 */
	public function getAdresse1()
	{
		return $this->adresse1;
	}

	/**
	 * Get the [adresse2] column value.
	 * 
	 * @return     string
	 */
	public function getAdresse2()
	{
		return $this->adresse2;
	}

	/**
	 * Get the [public_destinataire] column value.
	 * 
	 * @return     string
	 */
	public function getPublicDestinataire()
	{
		return $this->public_destinataire;
	}

	/**
	 * Get the [contacts] column value.
	 * 
	 * @return     string
	 */
	public function getContacts()
	{
		return $this->contacts;
	}

	/**
	 * Get the [divers] column value.
	 * 
	 * @return     string
	 */
	public function getDivers()
	{
		return $this->divers;
	}

	/**
	 * Get the [matiere1] column value.
	 * 
	 * @return     string
	 */
	public function getMatiere1()
	{
		return $this->matiere1;
	}

	/**
	 * Get the [matiere2] column value.
	 * 
	 * @return     string
	 */
	public function getMatiere2()
	{
		return $this->matiere2;
	}

	/**
	 * Get the [eleve_peut_modifier] column value.
	 * 
	 * @return     string
	 */
	public function getElevePeutModifier()
	{
		return $this->eleve_peut_modifier;
	}

	/**
	 * Get the [prof_peut_modifier] column value.
	 * 
	 * @return     string
	 */
	public function getProfPeutModifier()
	{
		return $this->prof_peut_modifier;
	}

	/**
	 * Get the [cpe_peut_modifier] column value.
	 * 
	 * @return     string
	 */
	public function getCpePeutModifier()
	{
		return $this->cpe_peut_modifier;
	}

	/**
	 * Get the [fiche_publique] column value.
	 * 
	 * @return     string
	 */
	public function getFichePublique()
	{
		return $this->fiche_publique;
	}

	/**
	 * Get the [affiche_adresse1] column value.
	 * 
	 * @return     string
	 */
	public function getAfficheAdresse1()
	{
		return $this->affiche_adresse1;
	}

	/**
	 * Get the [en_construction] column value.
	 * 
	 * @return     string
	 */
	public function getEnConstruction()
	{
		return $this->en_construction;
	}

	/**
	 * Set the value of [id] column.
	 * cle primaire auto-incremente
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = AidDetailsPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [nom] column.
	 * Nom de l'AID
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom !== $v || $v === '') {
			$this->nom = $v;
			$this->modifiedColumns[] = AidDetailsPeer::NOM;
		}

		return $this;
	} // setNom()

	/**
	 * Set the value of [numero] column.
	 * Numero d'ordre d'affichage
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setNumero($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->numero !== $v || $v === '0') {
			$this->numero = $v;
			$this->modifiedColumns[] = AidDetailsPeer::NUMERO;
		}

		return $this;
	} // setNumero()

	/**
	 * Set the value of [indice_aid] column.
	 * Cle etrangere, vers la liste des categories d'AID (aid_config)
	 * @param      int $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setIndiceAid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->indice_aid !== $v || $v === 0) {
			$this->indice_aid = $v;
			$this->modifiedColumns[] = AidDetailsPeer::INDICE_AID;
		}

		if ($this->aAidConfiguration !== null && $this->aAidConfiguration->getIndiceAid() !== $v) {
			$this->aAidConfiguration = null;
		}

		return $this;
	} // setIndiceAid()

	/**
	 * Set the value of [perso1] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setPerso1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->perso1 !== $v) {
			$this->perso1 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PERSO1;
		}

		return $this;
	} // setPerso1()

	/**
	 * Set the value of [perso2] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setPerso2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->perso2 !== $v) {
			$this->perso2 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PERSO2;
		}

		return $this;
	} // setPerso2()

	/**
	 * Set the value of [perso3] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setPerso3($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->perso3 !== $v) {
			$this->perso3 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PERSO3;
		}

		return $this;
	} // setPerso3()

	/**
	 * Set the value of [productions] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setProductions($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->productions !== $v) {
			$this->productions = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PRODUCTIONS;
		}

		return $this;
	} // setProductions()

	/**
	 * Set the value of [resume] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setResume($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->resume !== $v) {
			$this->resume = $v;
			$this->modifiedColumns[] = AidDetailsPeer::RESUME;
		}

		return $this;
	} // setResume()

	/**
	 * Set the value of [famille] column.
	 * 
	 * @param      int $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setFamille($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->famille !== $v) {
			$this->famille = $v;
			$this->modifiedColumns[] = AidDetailsPeer::FAMILLE;
		}

		return $this;
	} // setFamille()

	/**
	 * Set the value of [mots_cles] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setMotsCles($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mots_cles !== $v) {
			$this->mots_cles = $v;
			$this->modifiedColumns[] = AidDetailsPeer::MOTS_CLES;
		}

		return $this;
	} // setMotsCles()

	/**
	 * Set the value of [adresse1] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setAdresse1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adresse1 !== $v) {
			$this->adresse1 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::ADRESSE1;
		}

		return $this;
	} // setAdresse1()

	/**
	 * Set the value of [adresse2] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setAdresse2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->adresse2 !== $v) {
			$this->adresse2 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::ADRESSE2;
		}

		return $this;
	} // setAdresse2()

	/**
	 * Set the value of [public_destinataire] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setPublicDestinataire($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->public_destinataire !== $v) {
			$this->public_destinataire = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PUBLIC_DESTINATAIRE;
		}

		return $this;
	} // setPublicDestinataire()

	/**
	 * Set the value of [contacts] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setContacts($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->contacts !== $v) {
			$this->contacts = $v;
			$this->modifiedColumns[] = AidDetailsPeer::CONTACTS;
		}

		return $this;
	} // setContacts()

	/**
	 * Set the value of [divers] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setDivers($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->divers !== $v) {
			$this->divers = $v;
			$this->modifiedColumns[] = AidDetailsPeer::DIVERS;
		}

		return $this;
	} // setDivers()

	/**
	 * Set the value of [matiere1] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setMatiere1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->matiere1 !== $v) {
			$this->matiere1 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::MATIERE1;
		}

		return $this;
	} // setMatiere1()

	/**
	 * Set the value of [matiere2] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setMatiere2($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->matiere2 !== $v) {
			$this->matiere2 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::MATIERE2;
		}

		return $this;
	} // setMatiere2()

	/**
	 * Set the value of [eleve_peut_modifier] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setElevePeutModifier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->eleve_peut_modifier !== $v || $v === 'n') {
			$this->eleve_peut_modifier = $v;
			$this->modifiedColumns[] = AidDetailsPeer::ELEVE_PEUT_MODIFIER;
		}

		return $this;
	} // setElevePeutModifier()

	/**
	 * Set the value of [prof_peut_modifier] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setProfPeutModifier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->prof_peut_modifier !== $v || $v === 'n') {
			$this->prof_peut_modifier = $v;
			$this->modifiedColumns[] = AidDetailsPeer::PROF_PEUT_MODIFIER;
		}

		return $this;
	} // setProfPeutModifier()

	/**
	 * Set the value of [cpe_peut_modifier] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setCpePeutModifier($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cpe_peut_modifier !== $v || $v === 'n') {
			$this->cpe_peut_modifier = $v;
			$this->modifiedColumns[] = AidDetailsPeer::CPE_PEUT_MODIFIER;
		}

		return $this;
	} // setCpePeutModifier()

	/**
	 * Set the value of [fiche_publique] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setFichePublique($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fiche_publique !== $v || $v === 'n') {
			$this->fiche_publique = $v;
			$this->modifiedColumns[] = AidDetailsPeer::FICHE_PUBLIQUE;
		}

		return $this;
	} // setFichePublique()

	/**
	 * Set the value of [affiche_adresse1] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setAfficheAdresse1($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->affiche_adresse1 !== $v || $v === 'n') {
			$this->affiche_adresse1 = $v;
			$this->modifiedColumns[] = AidDetailsPeer::AFFICHE_ADRESSE1;
		}

		return $this;
	} // setAfficheAdresse1()

	/**
	 * Set the value of [en_construction] column.
	 * 
	 * @param      string $v new value
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function setEnConstruction($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->en_construction !== $v || $v === 'n') {
			$this->en_construction = $v;
			$this->modifiedColumns[] = AidDetailsPeer::EN_CONSTRUCTION;
		}

		return $this;
	} // setEnConstruction()

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
			if (array_diff($this->modifiedColumns, array(AidDetailsPeer::NOM,AidDetailsPeer::NUMERO,AidDetailsPeer::INDICE_AID,AidDetailsPeer::ELEVE_PEUT_MODIFIER,AidDetailsPeer::PROF_PEUT_MODIFIER,AidDetailsPeer::CPE_PEUT_MODIFIER,AidDetailsPeer::FICHE_PUBLIQUE,AidDetailsPeer::AFFICHE_ADRESSE1,AidDetailsPeer::EN_CONSTRUCTION))) {
				return false;
			}

			if ($this->nom !== '') {
				return false;
			}

			if ($this->numero !== '0') {
				return false;
			}

			if ($this->indice_aid !== 0) {
				return false;
			}

			if ($this->eleve_peut_modifier !== 'n') {
				return false;
			}

			if ($this->prof_peut_modifier !== 'n') {
				return false;
			}

			if ($this->cpe_peut_modifier !== 'n') {
				return false;
			}

			if ($this->fiche_publique !== 'n') {
				return false;
			}

			if ($this->affiche_adresse1 !== 'n') {
				return false;
			}

			if ($this->en_construction !== 'n') {
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

			$this->id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->nom = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->numero = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->indice_aid = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->perso1 = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->perso2 = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->perso3 = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->productions = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->resume = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->famille = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->mots_cles = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->adresse1 = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->adresse2 = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->public_destinataire = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->contacts = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->divers = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->matiere1 = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->matiere2 = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->eleve_peut_modifier = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->prof_peut_modifier = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->cpe_peut_modifier = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->fiche_publique = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->affiche_adresse1 = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->en_construction = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 24; // 24 = AidDetailsPeer::NUM_COLUMNS - AidDetailsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AidDetails object", $e);
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

		if ($this->aAidConfiguration !== null && $this->indice_aid !== $this->aAidConfiguration->getIndiceAid()) {
			$this->aAidConfiguration = null;
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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = AidDetailsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aAidConfiguration = null;
			$this->collJAidUtilisateursProfessionnelss = null;
			$this->lastJAidUtilisateursProfessionnelsCriteria = null;

			$this->collJAidElevess = null;
			$this->lastJAidElevesCriteria = null;

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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			AidDetailsPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			AidDetailsPeer::addInstanceToPool($this);
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

			if ($this->aAidConfiguration !== null) {
				if ($this->aAidConfiguration->isModified() || $this->aAidConfiguration->isNew()) {
					$affectedRows += $this->aAidConfiguration->save($con);
				}
				$this->setAidConfiguration($this->aAidConfiguration);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = AidDetailsPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = AidDetailsPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += AidDetailsPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJAidUtilisateursProfessionnelss !== null) {
				foreach ($this->collJAidUtilisateursProfessionnelss as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJAidElevess !== null) {
				foreach ($this->collJAidElevess as $referrerFK) {
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

			if ($this->aAidConfiguration !== null) {
				if (!$this->aAidConfiguration->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aAidConfiguration->getValidationFailures());
				}
			}


			if (($retval = AidDetailsPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJAidUtilisateursProfessionnelss !== null) {
					foreach ($this->collJAidUtilisateursProfessionnelss as $referrerFK) {
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
		$pos = AidDetailsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getNom();
				break;
			case 2:
				return $this->getNumero();
				break;
			case 3:
				return $this->getIndiceAid();
				break;
			case 4:
				return $this->getPerso1();
				break;
			case 5:
				return $this->getPerso2();
				break;
			case 6:
				return $this->getPerso3();
				break;
			case 7:
				return $this->getProductions();
				break;
			case 8:
				return $this->getResume();
				break;
			case 9:
				return $this->getFamille();
				break;
			case 10:
				return $this->getMotsCles();
				break;
			case 11:
				return $this->getAdresse1();
				break;
			case 12:
				return $this->getAdresse2();
				break;
			case 13:
				return $this->getPublicDestinataire();
				break;
			case 14:
				return $this->getContacts();
				break;
			case 15:
				return $this->getDivers();
				break;
			case 16:
				return $this->getMatiere1();
				break;
			case 17:
				return $this->getMatiere2();
				break;
			case 18:
				return $this->getElevePeutModifier();
				break;
			case 19:
				return $this->getProfPeutModifier();
				break;
			case 20:
				return $this->getCpePeutModifier();
				break;
			case 21:
				return $this->getFichePublique();
				break;
			case 22:
				return $this->getAfficheAdresse1();
				break;
			case 23:
				return $this->getEnConstruction();
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
		$keys = AidDetailsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getNom(),
			$keys[2] => $this->getNumero(),
			$keys[3] => $this->getIndiceAid(),
			$keys[4] => $this->getPerso1(),
			$keys[5] => $this->getPerso2(),
			$keys[6] => $this->getPerso3(),
			$keys[7] => $this->getProductions(),
			$keys[8] => $this->getResume(),
			$keys[9] => $this->getFamille(),
			$keys[10] => $this->getMotsCles(),
			$keys[11] => $this->getAdresse1(),
			$keys[12] => $this->getAdresse2(),
			$keys[13] => $this->getPublicDestinataire(),
			$keys[14] => $this->getContacts(),
			$keys[15] => $this->getDivers(),
			$keys[16] => $this->getMatiere1(),
			$keys[17] => $this->getMatiere2(),
			$keys[18] => $this->getElevePeutModifier(),
			$keys[19] => $this->getProfPeutModifier(),
			$keys[20] => $this->getCpePeutModifier(),
			$keys[21] => $this->getFichePublique(),
			$keys[22] => $this->getAfficheAdresse1(),
			$keys[23] => $this->getEnConstruction(),
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
		$pos = AidDetailsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setNom($value);
				break;
			case 2:
				$this->setNumero($value);
				break;
			case 3:
				$this->setIndiceAid($value);
				break;
			case 4:
				$this->setPerso1($value);
				break;
			case 5:
				$this->setPerso2($value);
				break;
			case 6:
				$this->setPerso3($value);
				break;
			case 7:
				$this->setProductions($value);
				break;
			case 8:
				$this->setResume($value);
				break;
			case 9:
				$this->setFamille($value);
				break;
			case 10:
				$this->setMotsCles($value);
				break;
			case 11:
				$this->setAdresse1($value);
				break;
			case 12:
				$this->setAdresse2($value);
				break;
			case 13:
				$this->setPublicDestinataire($value);
				break;
			case 14:
				$this->setContacts($value);
				break;
			case 15:
				$this->setDivers($value);
				break;
			case 16:
				$this->setMatiere1($value);
				break;
			case 17:
				$this->setMatiere2($value);
				break;
			case 18:
				$this->setElevePeutModifier($value);
				break;
			case 19:
				$this->setProfPeutModifier($value);
				break;
			case 20:
				$this->setCpePeutModifier($value);
				break;
			case 21:
				$this->setFichePublique($value);
				break;
			case 22:
				$this->setAfficheAdresse1($value);
				break;
			case 23:
				$this->setEnConstruction($value);
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
		$keys = AidDetailsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setNom($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNumero($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setIndiceAid($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setPerso1($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setPerso2($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setPerso3($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setProductions($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setResume($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setFamille($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setMotsCles($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setAdresse1($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setAdresse2($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setPublicDestinataire($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setContacts($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setDivers($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setMatiere1($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setMatiere2($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setElevePeutModifier($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setProfPeutModifier($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setCpePeutModifier($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setFichePublique($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setAfficheAdresse1($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setEnConstruction($arr[$keys[23]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);

		if ($this->isColumnModified(AidDetailsPeer::ID)) $criteria->add(AidDetailsPeer::ID, $this->id);
		if ($this->isColumnModified(AidDetailsPeer::NOM)) $criteria->add(AidDetailsPeer::NOM, $this->nom);
		if ($this->isColumnModified(AidDetailsPeer::NUMERO)) $criteria->add(AidDetailsPeer::NUMERO, $this->numero);
		if ($this->isColumnModified(AidDetailsPeer::INDICE_AID)) $criteria->add(AidDetailsPeer::INDICE_AID, $this->indice_aid);
		if ($this->isColumnModified(AidDetailsPeer::PERSO1)) $criteria->add(AidDetailsPeer::PERSO1, $this->perso1);
		if ($this->isColumnModified(AidDetailsPeer::PERSO2)) $criteria->add(AidDetailsPeer::PERSO2, $this->perso2);
		if ($this->isColumnModified(AidDetailsPeer::PERSO3)) $criteria->add(AidDetailsPeer::PERSO3, $this->perso3);
		if ($this->isColumnModified(AidDetailsPeer::PRODUCTIONS)) $criteria->add(AidDetailsPeer::PRODUCTIONS, $this->productions);
		if ($this->isColumnModified(AidDetailsPeer::RESUME)) $criteria->add(AidDetailsPeer::RESUME, $this->resume);
		if ($this->isColumnModified(AidDetailsPeer::FAMILLE)) $criteria->add(AidDetailsPeer::FAMILLE, $this->famille);
		if ($this->isColumnModified(AidDetailsPeer::MOTS_CLES)) $criteria->add(AidDetailsPeer::MOTS_CLES, $this->mots_cles);
		if ($this->isColumnModified(AidDetailsPeer::ADRESSE1)) $criteria->add(AidDetailsPeer::ADRESSE1, $this->adresse1);
		if ($this->isColumnModified(AidDetailsPeer::ADRESSE2)) $criteria->add(AidDetailsPeer::ADRESSE2, $this->adresse2);
		if ($this->isColumnModified(AidDetailsPeer::PUBLIC_DESTINATAIRE)) $criteria->add(AidDetailsPeer::PUBLIC_DESTINATAIRE, $this->public_destinataire);
		if ($this->isColumnModified(AidDetailsPeer::CONTACTS)) $criteria->add(AidDetailsPeer::CONTACTS, $this->contacts);
		if ($this->isColumnModified(AidDetailsPeer::DIVERS)) $criteria->add(AidDetailsPeer::DIVERS, $this->divers);
		if ($this->isColumnModified(AidDetailsPeer::MATIERE1)) $criteria->add(AidDetailsPeer::MATIERE1, $this->matiere1);
		if ($this->isColumnModified(AidDetailsPeer::MATIERE2)) $criteria->add(AidDetailsPeer::MATIERE2, $this->matiere2);
		if ($this->isColumnModified(AidDetailsPeer::ELEVE_PEUT_MODIFIER)) $criteria->add(AidDetailsPeer::ELEVE_PEUT_MODIFIER, $this->eleve_peut_modifier);
		if ($this->isColumnModified(AidDetailsPeer::PROF_PEUT_MODIFIER)) $criteria->add(AidDetailsPeer::PROF_PEUT_MODIFIER, $this->prof_peut_modifier);
		if ($this->isColumnModified(AidDetailsPeer::CPE_PEUT_MODIFIER)) $criteria->add(AidDetailsPeer::CPE_PEUT_MODIFIER, $this->cpe_peut_modifier);
		if ($this->isColumnModified(AidDetailsPeer::FICHE_PUBLIQUE)) $criteria->add(AidDetailsPeer::FICHE_PUBLIQUE, $this->fiche_publique);
		if ($this->isColumnModified(AidDetailsPeer::AFFICHE_ADRESSE1)) $criteria->add(AidDetailsPeer::AFFICHE_ADRESSE1, $this->affiche_adresse1);
		if ($this->isColumnModified(AidDetailsPeer::EN_CONSTRUCTION)) $criteria->add(AidDetailsPeer::EN_CONSTRUCTION, $this->en_construction);

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
		$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);

		$criteria->add(AidDetailsPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AidDetails (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setNom($this->nom);

		$copyObj->setNumero($this->numero);

		$copyObj->setIndiceAid($this->indice_aid);

		$copyObj->setPerso1($this->perso1);

		$copyObj->setPerso2($this->perso2);

		$copyObj->setPerso3($this->perso3);

		$copyObj->setProductions($this->productions);

		$copyObj->setResume($this->resume);

		$copyObj->setFamille($this->famille);

		$copyObj->setMotsCles($this->mots_cles);

		$copyObj->setAdresse1($this->adresse1);

		$copyObj->setAdresse2($this->adresse2);

		$copyObj->setPublicDestinataire($this->public_destinataire);

		$copyObj->setContacts($this->contacts);

		$copyObj->setDivers($this->divers);

		$copyObj->setMatiere1($this->matiere1);

		$copyObj->setMatiere2($this->matiere2);

		$copyObj->setElevePeutModifier($this->eleve_peut_modifier);

		$copyObj->setProfPeutModifier($this->prof_peut_modifier);

		$copyObj->setCpePeutModifier($this->cpe_peut_modifier);

		$copyObj->setFichePublique($this->fiche_publique);

		$copyObj->setAfficheAdresse1($this->affiche_adresse1);

		$copyObj->setEnConstruction($this->en_construction);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJAidUtilisateursProfessionnelss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJAidUtilisateursProfessionnels($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJAidElevess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJAidEleves($relObj->copy($deepCopy));
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
	 * @return     AidDetails Clone of current object.
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
	 * @return     AidDetailsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AidDetailsPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a AidConfiguration object.
	 *
	 * @param      AidConfiguration $v
	 * @return     AidDetails The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setAidConfiguration(AidConfiguration $v = null)
	{
		if ($v === null) {
			$this->setIndiceAid(0);
		} else {
			$this->setIndiceAid($v->getIndiceAid());
		}

		$this->aAidConfiguration = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the AidConfiguration object, it will not be re-added.
		if ($v !== null) {
			$v->addAidDetails($this);
		}

		return $this;
	}


	/**
	 * Get the associated AidConfiguration object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     AidConfiguration The associated AidConfiguration object.
	 * @throws     PropelException
	 */
	public function getAidConfiguration(PropelPDO $con = null)
	{
		if ($this->aAidConfiguration === null && ($this->indice_aid !== null)) {
			$this->aAidConfiguration = AidConfigurationPeer::retrieveByPK($this->indice_aid, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aAidConfiguration->addAidDetailss($this);
			 */
		}
		return $this->aAidConfiguration;
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
	 * Otherwise if this AidDetails has previously been saved, it will retrieve
	 * related JAidUtilisateursProfessionnelss from storage. If this AidDetails is new, it will return
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
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
			   $this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

				JAidUtilisateursProfessionnelsPeer::addSelectColumns($criteria);
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

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
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
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

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

				$count = JAidUtilisateursProfessionnelsPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

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
			$l->setAidDetails($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related JAidUtilisateursProfessionnelss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
	 */
	public function getJAidUtilisateursProfessionnelssJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
				$this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

			if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;

		return $this->collJAidUtilisateursProfessionnelss;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related JAidUtilisateursProfessionnelss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
	 */
	public function getJAidUtilisateursProfessionnelssJoinAidConfiguration($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidUtilisateursProfessionnelss === null) {
			if ($this->isNew()) {
				$this->collJAidUtilisateursProfessionnelss = array();
			} else {

				$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidUtilisateursProfessionnelsPeer::ID_AID, $this->id);

			if (!isset($this->lastJAidUtilisateursProfessionnelsCriteria) || !$this->lastJAidUtilisateursProfessionnelsCriteria->equals($criteria)) {
				$this->collJAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidUtilisateursProfessionnelsCriteria = $criteria;

		return $this->collJAidUtilisateursProfessionnelss;
	}

	/**
	 * Clears out the collJAidElevess collection (array).
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
	 * Initializes the collJAidElevess collection (array).
	 *
	 * By default this just sets the collJAidElevess collection to an empty array (like clearcollJAidElevess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJAidElevess()
	{
		$this->collJAidElevess = array();
	}

	/**
	 * Gets an array of JAidEleves objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this AidDetails has previously been saved, it will retrieve
	 * related JAidElevess from storage. If this AidDetails is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JAidEleves[]
	 * @throws     PropelException
	 */
	public function getJAidElevess($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidElevess === null) {
			if ($this->isNew()) {
			   $this->collJAidElevess = array();
			} else {

				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				JAidElevesPeer::addSelectColumns($criteria);
				$this->collJAidElevess = JAidElevesPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				JAidElevesPeer::addSelectColumns($criteria);
				if (!isset($this->lastJAidElevesCriteria) || !$this->lastJAidElevesCriteria->equals($criteria)) {
					$this->collJAidElevess = JAidElevesPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJAidElevesCriteria = $criteria;
		return $this->collJAidElevess;
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
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJAidElevess === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				$count = JAidElevesPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				if (!isset($this->lastJAidElevesCriteria) || !$this->lastJAidElevesCriteria->equals($criteria)) {
					$count = JAidElevesPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJAidElevess);
				}
			} else {
				$count = count($this->collJAidElevess);
			}
		}
		$this->lastJAidElevesCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JAidEleves object to this object
	 * through the JAidEleves foreign key attribute.
	 *
	 * @param      JAidEleves $l JAidEleves
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJAidEleves(JAidEleves $l)
	{
		if ($this->collJAidElevess === null) {
			$this->initJAidElevess();
		}
		if (!in_array($l, $this->collJAidElevess, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJAidElevess, $l);
			$l->setAidDetails($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related JAidElevess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
	 */
	public function getJAidElevessJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidElevess === null) {
			if ($this->isNew()) {
				$this->collJAidElevess = array();
			} else {

				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				$this->collJAidElevess = JAidElevesPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidElevesPeer::ID_AID, $this->id);

			if (!isset($this->lastJAidElevesCriteria) || !$this->lastJAidElevesCriteria->equals($criteria)) {
				$this->collJAidElevess = JAidElevesPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidElevesCriteria = $criteria;

		return $this->collJAidElevess;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related JAidElevess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
	 */
	public function getJAidElevessJoinAidConfiguration($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(AidDetailsPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJAidElevess === null) {
			if ($this->isNew()) {
				$this->collJAidElevess = array();
			} else {

				$criteria->add(JAidElevesPeer::ID_AID, $this->id);

				$this->collJAidElevess = JAidElevesPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JAidElevesPeer::ID_AID, $this->id);

			if (!isset($this->lastJAidElevesCriteria) || !$this->lastJAidElevesCriteria->equals($criteria)) {
				$this->collJAidElevess = JAidElevesPeer::doSelectJoinAidConfiguration($criteria, $con, $join_behavior);
			}
		}
		$this->lastJAidElevesCriteria = $criteria;

		return $this->collJAidElevess;
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
			if ($this->collJAidUtilisateursProfessionnelss) {
				foreach ((array) $this->collJAidUtilisateursProfessionnelss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidElevess) {
				foreach ((array) $this->collJAidElevess as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJAidUtilisateursProfessionnelss = null;
		$this->collJAidElevess = null;
			$this->aAidConfiguration = null;
	}

} // BaseAidDetails
