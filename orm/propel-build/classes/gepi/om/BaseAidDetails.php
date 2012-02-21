<?php


/**
 * Base class that represents a row from the 'aid' table.
 *
 * Liste des AID (Activites Inter-Disciplinaires)
 *
 * @package    propel.generator.gepi.om
 */
abstract class BaseAidDetails extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
	const PEER = 'AidDetailsPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AidDetailsPeer
	 */
	protected static $peer;

	/**
	 * The flag var to prevent infinit loop in deep copy
	 * @var       boolean
	 */
	protected $startCopy = false;

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
	 * @var        array JAidEleves[] Collection to store aggregation of JAidEleves objects.
	 */
	protected $collJAidElevess;

	/**
	 * @var        array AbsenceEleveSaisie[] Collection to store aggregation of AbsenceEleveSaisie objects.
	 */
	protected $collAbsenceEleveSaisies;

	/**
	 * @var        array EdtEmplacementCours[] Collection to store aggregation of EdtEmplacementCours objects.
	 */
	protected $collEdtEmplacementCourss;

	/**
	 * @var        array UtilisateurProfessionnel[] Collection to store aggregation of UtilisateurProfessionnel objects.
	 */
	protected $collUtilisateurProfessionnels;

	/**
	 * @var        array Eleve[] Collection to store aggregation of Eleve objects.
	 */
	protected $collEleves;

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
	protected $utilisateurProfessionnelsScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $elevesScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jAidUtilisateursProfessionnelssScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $jAidElevessScheduledForDeletion = null;

	/**
	 * An array of objects scheduled for deletion.
	 * @var		array
	 */
	protected $absenceEleveSaisiesScheduledForDeletion = null;

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
	 * Initializes internal state of BaseAidDetails object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
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

		if ($this->nom !== $v) {
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

		if ($this->numero !== $v) {
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

		if ($this->indice_aid !== $v) {
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

		if ($this->eleve_peut_modifier !== $v) {
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

		if ($this->prof_peut_modifier !== $v) {
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

		if ($this->cpe_peut_modifier !== $v) {
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

		if ($this->fiche_publique !== $v) {
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

		if ($this->affiche_adresse1 !== $v) {
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

		if ($this->en_construction !== $v) {
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

			return $startcol + 24; // 24 = AidDetailsPeer::NUM_HYDRATE_COLUMNS.

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

			$this->collJAidElevess = null;

			$this->collAbsenceEleveSaisies = null;

			$this->collEdtEmplacementCourss = null;

			$this->collUtilisateurProfessionnels = null;
			$this->collEleves = null;
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
			$deleteQuery = AidDetailsQuery::create()
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
			$con = Propel::getConnection(AidDetailsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				AidDetailsPeer::addInstanceToPool($this);
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

			if ($this->utilisateurProfessionnelsScheduledForDeletion !== null) {
				if (!$this->utilisateurProfessionnelsScheduledForDeletion->isEmpty()) {
					JAidUtilisateursProfessionnelsQuery::create()
						->filterByPrimaryKeys($this->utilisateurProfessionnelsScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->utilisateurProfessionnelsScheduledForDeletion = null;
				}

				foreach ($this->getUtilisateurProfessionnels() as $utilisateurProfessionnel) {
					if ($utilisateurProfessionnel->isModified()) {
						$utilisateurProfessionnel->save($con);
					}
				}
			}

			if ($this->elevesScheduledForDeletion !== null) {
				if (!$this->elevesScheduledForDeletion->isEmpty()) {
					JAidElevesQuery::create()
						->filterByPrimaryKeys($this->elevesScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->elevesScheduledForDeletion = null;
				}

				foreach ($this->getEleves() as $eleve) {
					if ($eleve->isModified()) {
						$eleve->save($con);
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

			if ($this->jAidElevessScheduledForDeletion !== null) {
				if (!$this->jAidElevessScheduledForDeletion->isEmpty()) {
					JAidElevesQuery::create()
						->filterByPrimaryKeys($this->jAidElevessScheduledForDeletion->getPrimaryKeys(false))
						->delete($con);
					$this->jAidElevessScheduledForDeletion = null;
				}
			}

			if ($this->collJAidElevess !== null) {
				foreach ($this->collJAidElevess as $referrerFK) {
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
		if ($this->isColumnModified(AidDetailsPeer::ID)) {
			$modifiedColumns[':p' . $index++]  = 'ID';
		}
		if ($this->isColumnModified(AidDetailsPeer::NOM)) {
			$modifiedColumns[':p' . $index++]  = 'NOM';
		}
		if ($this->isColumnModified(AidDetailsPeer::NUMERO)) {
			$modifiedColumns[':p' . $index++]  = 'NUMERO';
		}
		if ($this->isColumnModified(AidDetailsPeer::INDICE_AID)) {
			$modifiedColumns[':p' . $index++]  = 'INDICE_AID';
		}
		if ($this->isColumnModified(AidDetailsPeer::PERSO1)) {
			$modifiedColumns[':p' . $index++]  = 'PERSO1';
		}
		if ($this->isColumnModified(AidDetailsPeer::PERSO2)) {
			$modifiedColumns[':p' . $index++]  = 'PERSO2';
		}
		if ($this->isColumnModified(AidDetailsPeer::PERSO3)) {
			$modifiedColumns[':p' . $index++]  = 'PERSO3';
		}
		if ($this->isColumnModified(AidDetailsPeer::PRODUCTIONS)) {
			$modifiedColumns[':p' . $index++]  = 'PRODUCTIONS';
		}
		if ($this->isColumnModified(AidDetailsPeer::RESUME)) {
			$modifiedColumns[':p' . $index++]  = 'RESUME';
		}
		if ($this->isColumnModified(AidDetailsPeer::FAMILLE)) {
			$modifiedColumns[':p' . $index++]  = 'FAMILLE';
		}
		if ($this->isColumnModified(AidDetailsPeer::MOTS_CLES)) {
			$modifiedColumns[':p' . $index++]  = 'MOTS_CLES';
		}
		if ($this->isColumnModified(AidDetailsPeer::ADRESSE1)) {
			$modifiedColumns[':p' . $index++]  = 'ADRESSE1';
		}
		if ($this->isColumnModified(AidDetailsPeer::ADRESSE2)) {
			$modifiedColumns[':p' . $index++]  = 'ADRESSE2';
		}
		if ($this->isColumnModified(AidDetailsPeer::PUBLIC_DESTINATAIRE)) {
			$modifiedColumns[':p' . $index++]  = 'PUBLIC_DESTINATAIRE';
		}
		if ($this->isColumnModified(AidDetailsPeer::CONTACTS)) {
			$modifiedColumns[':p' . $index++]  = 'CONTACTS';
		}
		if ($this->isColumnModified(AidDetailsPeer::DIVERS)) {
			$modifiedColumns[':p' . $index++]  = 'DIVERS';
		}
		if ($this->isColumnModified(AidDetailsPeer::MATIERE1)) {
			$modifiedColumns[':p' . $index++]  = 'MATIERE1';
		}
		if ($this->isColumnModified(AidDetailsPeer::MATIERE2)) {
			$modifiedColumns[':p' . $index++]  = 'MATIERE2';
		}
		if ($this->isColumnModified(AidDetailsPeer::ELEVE_PEUT_MODIFIER)) {
			$modifiedColumns[':p' . $index++]  = 'ELEVE_PEUT_MODIFIER';
		}
		if ($this->isColumnModified(AidDetailsPeer::PROF_PEUT_MODIFIER)) {
			$modifiedColumns[':p' . $index++]  = 'PROF_PEUT_MODIFIER';
		}
		if ($this->isColumnModified(AidDetailsPeer::CPE_PEUT_MODIFIER)) {
			$modifiedColumns[':p' . $index++]  = 'CPE_PEUT_MODIFIER';
		}
		if ($this->isColumnModified(AidDetailsPeer::FICHE_PUBLIQUE)) {
			$modifiedColumns[':p' . $index++]  = 'FICHE_PUBLIQUE';
		}
		if ($this->isColumnModified(AidDetailsPeer::AFFICHE_ADRESSE1)) {
			$modifiedColumns[':p' . $index++]  = 'AFFICHE_ADRESSE1';
		}
		if ($this->isColumnModified(AidDetailsPeer::EN_CONSTRUCTION)) {
			$modifiedColumns[':p' . $index++]  = 'EN_CONSTRUCTION';
		}

		$sql = sprintf(
			'INSERT INTO aid (%s) VALUES (%s)',
			implode(', ', $modifiedColumns),
			implode(', ', array_keys($modifiedColumns))
		);

		try {
			$stmt = $con->prepare($sql);
			foreach ($modifiedColumns as $identifier => $columnName) {
				switch ($columnName) {
					case 'ID':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_STR);
						break;
					case 'NOM':
						$stmt->bindValue($identifier, $this->nom, PDO::PARAM_STR);
						break;
					case 'NUMERO':
						$stmt->bindValue($identifier, $this->numero, PDO::PARAM_STR);
						break;
					case 'INDICE_AID':
						$stmt->bindValue($identifier, $this->indice_aid, PDO::PARAM_INT);
						break;
					case 'PERSO1':
						$stmt->bindValue($identifier, $this->perso1, PDO::PARAM_STR);
						break;
					case 'PERSO2':
						$stmt->bindValue($identifier, $this->perso2, PDO::PARAM_STR);
						break;
					case 'PERSO3':
						$stmt->bindValue($identifier, $this->perso3, PDO::PARAM_STR);
						break;
					case 'PRODUCTIONS':
						$stmt->bindValue($identifier, $this->productions, PDO::PARAM_STR);
						break;
					case 'RESUME':
						$stmt->bindValue($identifier, $this->resume, PDO::PARAM_STR);
						break;
					case 'FAMILLE':
						$stmt->bindValue($identifier, $this->famille, PDO::PARAM_INT);
						break;
					case 'MOTS_CLES':
						$stmt->bindValue($identifier, $this->mots_cles, PDO::PARAM_STR);
						break;
					case 'ADRESSE1':
						$stmt->bindValue($identifier, $this->adresse1, PDO::PARAM_STR);
						break;
					case 'ADRESSE2':
						$stmt->bindValue($identifier, $this->adresse2, PDO::PARAM_STR);
						break;
					case 'PUBLIC_DESTINATAIRE':
						$stmt->bindValue($identifier, $this->public_destinataire, PDO::PARAM_STR);
						break;
					case 'CONTACTS':
						$stmt->bindValue($identifier, $this->contacts, PDO::PARAM_STR);
						break;
					case 'DIVERS':
						$stmt->bindValue($identifier, $this->divers, PDO::PARAM_STR);
						break;
					case 'MATIERE1':
						$stmt->bindValue($identifier, $this->matiere1, PDO::PARAM_STR);
						break;
					case 'MATIERE2':
						$stmt->bindValue($identifier, $this->matiere2, PDO::PARAM_STR);
						break;
					case 'ELEVE_PEUT_MODIFIER':
						$stmt->bindValue($identifier, $this->eleve_peut_modifier, PDO::PARAM_STR);
						break;
					case 'PROF_PEUT_MODIFIER':
						$stmt->bindValue($identifier, $this->prof_peut_modifier, PDO::PARAM_STR);
						break;
					case 'CPE_PEUT_MODIFIER':
						$stmt->bindValue($identifier, $this->cpe_peut_modifier, PDO::PARAM_STR);
						break;
					case 'FICHE_PUBLIQUE':
						$stmt->bindValue($identifier, $this->fiche_publique, PDO::PARAM_STR);
						break;
					case 'AFFICHE_ADRESSE1':
						$stmt->bindValue($identifier, $this->affiche_adresse1, PDO::PARAM_STR);
						break;
					case 'EN_CONSTRUCTION':
						$stmt->bindValue($identifier, $this->en_construction, PDO::PARAM_STR);
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

				if ($this->collAbsenceEleveSaisies !== null) {
					foreach ($this->collAbsenceEleveSaisies as $referrerFK) {
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
		if (isset($alreadyDumpedObjects['AidDetails'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['AidDetails'][$this->getPrimaryKey()] = true;
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
		if ($includeForeignObjects) {
			if (null !== $this->aAidConfiguration) {
				$result['AidConfiguration'] = $this->aAidConfiguration->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
			}
			if (null !== $this->collJAidUtilisateursProfessionnelss) {
				$result['JAidUtilisateursProfessionnelss'] = $this->collJAidUtilisateursProfessionnelss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collJAidElevess) {
				$result['JAidElevess'] = $this->collJAidElevess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
			}
			if (null !== $this->collAbsenceEleveSaisies) {
				$result['AbsenceEleveSaisies'] = $this->collAbsenceEleveSaisies->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
	 * @param      object $copyObj An object of AidDetails (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
	{
		$copyObj->setNom($this->getNom());
		$copyObj->setNumero($this->getNumero());
		$copyObj->setIndiceAid($this->getIndiceAid());
		$copyObj->setPerso1($this->getPerso1());
		$copyObj->setPerso2($this->getPerso2());
		$copyObj->setPerso3($this->getPerso3());
		$copyObj->setProductions($this->getProductions());
		$copyObj->setResume($this->getResume());
		$copyObj->setFamille($this->getFamille());
		$copyObj->setMotsCles($this->getMotsCles());
		$copyObj->setAdresse1($this->getAdresse1());
		$copyObj->setAdresse2($this->getAdresse2());
		$copyObj->setPublicDestinataire($this->getPublicDestinataire());
		$copyObj->setContacts($this->getContacts());
		$copyObj->setDivers($this->getDivers());
		$copyObj->setMatiere1($this->getMatiere1());
		$copyObj->setMatiere2($this->getMatiere2());
		$copyObj->setElevePeutModifier($this->getElevePeutModifier());
		$copyObj->setProfPeutModifier($this->getProfPeutModifier());
		$copyObj->setCpePeutModifier($this->getCpePeutModifier());
		$copyObj->setFichePublique($this->getFichePublique());
		$copyObj->setAfficheAdresse1($this->getAfficheAdresse1());
		$copyObj->setEnConstruction($this->getEnConstruction());

		if ($deepCopy && !$this->startCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);
			// store object hash to prevent cycle
			$this->startCopy = true;

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

			foreach ($this->getAbsenceEleveSaisies() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addAbsenceEleveSaisie($relObj->copy($deepCopy));
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
			$this->aAidConfiguration = AidConfigurationQuery::create()->findPk($this->indice_aid, $con);
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
	 * Initializes a collection based on the name of a relation.
	 * Avoids crafting an 'init[$relationName]s' method name
	 * that wouldn't work when StandardEnglishPluralizer is used.
	 *
	 * @param      string $relationName The name of the relation to initialize
	 * @return     void
	 */
	public function initRelation($relationName)
	{
		if ('JAidUtilisateursProfessionnels' == $relationName) {
			return $this->initJAidUtilisateursProfessionnelss();
		}
		if ('JAidEleves' == $relationName) {
			return $this->initJAidElevess();
		}
		if ('AbsenceEleveSaisie' == $relationName) {
			return $this->initAbsenceEleveSaisies();
		}
		if ('EdtEmplacementCours' == $relationName) {
			return $this->initEdtEmplacementCourss();
		}
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
	 * If this AidDetails is new, it will return
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
					->filterByAidDetails($this)
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
				$jAidUtilisateursProfessionnels->setAidDetails($this);
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
					->filterByAidDetails($this)
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
	 * @return     AidDetails The current object (for fluent API support)
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
		$jAidUtilisateursProfessionnels->setAidDetails($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JAidUtilisateursProfessionnels[] List of JAidUtilisateursProfessionnels objects
	 */
	public function getJAidUtilisateursProfessionnelssJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JAidUtilisateursProfessionnelsQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getJAidUtilisateursProfessionnelss($query, $con);
	}

	/**
	 * Clears out the collJAidElevess collection
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
	 * Initializes the collJAidElevess collection.
	 *
	 * By default this just sets the collJAidElevess collection to an empty array (like clearcollJAidElevess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @param      boolean $overrideExisting If set to true, the method call initializes
	 *                                        the collection even if it is not empty
	 *
	 * @return     void
	 */
	public function initJAidElevess($overrideExisting = true)
	{
		if (null !== $this->collJAidElevess && !$overrideExisting) {
			return;
		}
		$this->collJAidElevess = new PropelObjectCollection();
		$this->collJAidElevess->setModel('JAidEleves');
	}

	/**
	 * Gets an array of JAidEleves objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AidDetails is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array JAidEleves[] List of JAidEleves objects
	 * @throws     PropelException
	 */
	public function getJAidElevess($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collJAidElevess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidElevess) {
				// return empty collection
				$this->initJAidElevess();
			} else {
				$collJAidElevess = JAidElevesQuery::create(null, $criteria)
					->filterByAidDetails($this)
					->find($con);
				if (null !== $criteria) {
					return $collJAidElevess;
				}
				$this->collJAidElevess = $collJAidElevess;
			}
		}
		return $this->collJAidElevess;
	}

	/**
	 * Sets a collection of JAidEleves objects related by a one-to-many relationship
	 * to the current object.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $jAidElevess A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setJAidElevess(PropelCollection $jAidElevess, PropelPDO $con = null)
	{
		$this->jAidElevessScheduledForDeletion = $this->getJAidElevess(new Criteria(), $con)->diff($jAidElevess);

		foreach ($jAidElevess as $jAidEleves) {
			// Fix issue with collection modified by reference
			if ($jAidEleves->isNew()) {
				$jAidEleves->setAidDetails($this);
			}
			$this->addJAidEleves($jAidEleves);
		}

		$this->collJAidElevess = $jAidElevess;
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
		if(null === $this->collJAidElevess || null !== $criteria) {
			if ($this->isNew() && null === $this->collJAidElevess) {
				return 0;
			} else {
				$query = JAidElevesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAidDetails($this)
					->count($con);
			}
		} else {
			return count($this->collJAidElevess);
		}
	}

	/**
	 * Method called to associate a JAidEleves object to this object
	 * through the JAidEleves foreign key attribute.
	 *
	 * @param      JAidEleves $l JAidEleves
	 * @return     AidDetails The current object (for fluent API support)
	 */
	public function addJAidEleves(JAidEleves $l)
	{
		if ($this->collJAidElevess === null) {
			$this->initJAidElevess();
		}
		if (!$this->collJAidElevess->contains($l)) { // only add it if the **same** object is not already associated
			$this->doAddJAidEleves($l);
		}

		return $this;
	}

	/**
	 * @param	JAidEleves $jAidEleves The jAidEleves object to add.
	 */
	protected function doAddJAidEleves($jAidEleves)
	{
		$this->collJAidElevess[]= $jAidEleves;
		$jAidEleves->setAidDetails($this);
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
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array JAidEleves[] List of JAidEleves objects
	 */
	public function getJAidElevessJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = JAidElevesQuery::create(null, $criteria);
		$query->joinWith('Eleve', $join_behavior);

		return $this->getJAidElevess($query, $con);
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
	 * If this AidDetails is new, it will return
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
					->filterByAidDetails($this)
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
				$absenceEleveSaisie->setAidDetails($this);
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
					->filterByAidDetails($this)
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
	 * @return     AidDetails The current object (for fluent API support)
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
		$absenceEleveSaisie->setAidDetails($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array AbsenceEleveSaisie[] List of AbsenceEleveSaisie objects
	 */
	public function getAbsenceEleveSaisiesJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = AbsenceEleveSaisieQuery::create(null, $criteria);
		$query->joinWith('UtilisateurProfessionnel', $join_behavior);

		return $this->getAbsenceEleveSaisies($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related AbsenceEleveSaisies from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * If this AidDetails is new, it will return
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
					->filterByAidDetails($this)
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
				$edtEmplacementCours->setAidDetails($this);
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
					->filterByAidDetails($this)
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
	 * @return     AidDetails The current object (for fluent API support)
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
		$edtEmplacementCours->setAidDetails($this);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this AidDetails is new, it will return
	 * an empty collection; or if this AidDetails has previously
	 * been saved, it will retrieve related EdtEmplacementCourss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in AidDetails.
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
	 * Clears out the collUtilisateurProfessionnels collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addUtilisateurProfessionnels()
	 */
	public function clearUtilisateurProfessionnels()
	{
		$this->collUtilisateurProfessionnels = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collUtilisateurProfessionnels collection.
	 *
	 * By default this just sets the collUtilisateurProfessionnels collection to an empty collection (like clearUtilisateurProfessionnels());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initUtilisateurProfessionnels()
	{
		$this->collUtilisateurProfessionnels = new PropelObjectCollection();
		$this->collUtilisateurProfessionnels->setModel('UtilisateurProfessionnel');
	}

	/**
	 * Gets a collection of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AidDetails is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array UtilisateurProfessionnel[] List of UtilisateurProfessionnel objects
	 */
	public function getUtilisateurProfessionnels($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collUtilisateurProfessionnels || null !== $criteria) {
			if ($this->isNew() && null === $this->collUtilisateurProfessionnels) {
				// return empty collection
				$this->initUtilisateurProfessionnels();
			} else {
				$collUtilisateurProfessionnels = UtilisateurProfessionnelQuery::create(null, $criteria)
					->filterByAidDetails($this)
					->find($con);
				if (null !== $criteria) {
					return $collUtilisateurProfessionnels;
				}
				$this->collUtilisateurProfessionnels = $collUtilisateurProfessionnels;
			}
		}
		return $this->collUtilisateurProfessionnels;
	}

	/**
	 * Sets a collection of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $utilisateurProfessionnels A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setUtilisateurProfessionnels(PropelCollection $utilisateurProfessionnels, PropelPDO $con = null)
	{
		$jAidUtilisateursProfessionnelss = JAidUtilisateursProfessionnelsQuery::create()
			->filterByUtilisateurProfessionnel($utilisateurProfessionnels)
			->filterByAidDetails($this)
			->find($con);

		$this->utilisateurProfessionnelsScheduledForDeletion = $this->getJAidUtilisateursProfessionnelss()->diff($jAidUtilisateursProfessionnelss);
		$this->collJAidUtilisateursProfessionnelss = $jAidUtilisateursProfessionnelss;

		foreach ($utilisateurProfessionnels as $utilisateurProfessionnel) {
			// Fix issue with collection modified by reference
			if ($utilisateurProfessionnel->isNew()) {
				$this->doAddUtilisateurProfessionnel($utilisateurProfessionnel);
			} else {
				$this->addUtilisateurProfessionnel($utilisateurProfessionnel);
			}
		}

		$this->collUtilisateurProfessionnels = $utilisateurProfessionnels;
	}

	/**
	 * Gets the number of UtilisateurProfessionnel objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_utilisateurs cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related UtilisateurProfessionnel objects
	 */
	public function countUtilisateurProfessionnels($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collUtilisateurProfessionnels || null !== $criteria) {
			if ($this->isNew() && null === $this->collUtilisateurProfessionnels) {
				return 0;
			} else {
				$query = UtilisateurProfessionnelQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAidDetails($this)
					->count($con);
			}
		} else {
			return count($this->collUtilisateurProfessionnels);
		}
	}

	/**
	 * Associate a UtilisateurProfessionnel object to this object
	 * through the j_aid_utilisateurs cross reference table.
	 *
	 * @param      UtilisateurProfessionnel $utilisateurProfessionnel The JAidUtilisateursProfessionnels object to relate
	 * @return     void
	 */
	public function addUtilisateurProfessionnel(UtilisateurProfessionnel $utilisateurProfessionnel)
	{
		if ($this->collUtilisateurProfessionnels === null) {
			$this->initUtilisateurProfessionnels();
		}
		if (!$this->collUtilisateurProfessionnels->contains($utilisateurProfessionnel)) { // only add it if the **same** object is not already associated
			$this->doAddUtilisateurProfessionnel($utilisateurProfessionnel);

			$this->collUtilisateurProfessionnels[]= $utilisateurProfessionnel;
		}
	}

	/**
	 * @param	UtilisateurProfessionnel $utilisateurProfessionnel The utilisateurProfessionnel object to add.
	 */
	protected function doAddUtilisateurProfessionnel($utilisateurProfessionnel)
	{
		$jAidUtilisateursProfessionnels = new JAidUtilisateursProfessionnels();
		$jAidUtilisateursProfessionnels->setUtilisateurProfessionnel($utilisateurProfessionnel);
		$this->addJAidUtilisateursProfessionnels($jAidUtilisateursProfessionnels);
	}

	/**
	 * Clears out the collEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEleves()
	 */
	public function clearEleves()
	{
		$this->collEleves = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collEleves collection.
	 *
	 * By default this just sets the collEleves collection to an empty collection (like clearEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initEleves()
	{
		$this->collEleves = new PropelObjectCollection();
		$this->collEleves->setModel('Eleve');
	}

	/**
	 * Gets a collection of Eleve objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this AidDetails is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Eleve[] List of Eleve objects
	 */
	public function getEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collEleves) {
				// return empty collection
				$this->initEleves();
			} else {
				$collEleves = EleveQuery::create(null, $criteria)
					->filterByAidDetails($this)
					->find($con);
				if (null !== $criteria) {
					return $collEleves;
				}
				$this->collEleves = $collEleves;
			}
		}
		return $this->collEleves;
	}

	/**
	 * Sets a collection of Eleve objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
	 * and new objects from the given Propel collection.
	 *
	 * @param      PropelCollection $eleves A Propel collection.
	 * @param      PropelPDO $con Optional connection object
	 */
	public function setEleves(PropelCollection $eleves, PropelPDO $con = null)
	{
		$jAidElevess = JAidElevesQuery::create()
			->filterByEleve($eleves)
			->filterByAidDetails($this)
			->find($con);

		$this->elevesScheduledForDeletion = $this->getJAidElevess()->diff($jAidElevess);
		$this->collJAidElevess = $jAidElevess;

		foreach ($eleves as $eleve) {
			// Fix issue with collection modified by reference
			if ($eleve->isNew()) {
				$this->doAddEleve($eleve);
			} else {
				$this->addEleve($eleve);
			}
		}

		$this->collEleves = $eleves;
	}

	/**
	 * Gets the number of Eleve objects related by a many-to-many relationship
	 * to the current object by way of the j_aid_eleves cross-reference table.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      boolean $distinct Set to true to force count distinct
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     int the number of related Eleve objects
	 */
	public function countEleves($criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collEleves) {
				return 0;
			} else {
				$query = EleveQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByAidDetails($this)
					->count($con);
			}
		} else {
			return count($this->collEleves);
		}
	}

	/**
	 * Associate a Eleve object to this object
	 * through the j_aid_eleves cross reference table.
	 *
	 * @param      Eleve $eleve The JAidEleves object to relate
	 * @return     void
	 */
	public function addEleve(Eleve $eleve)
	{
		if ($this->collEleves === null) {
			$this->initEleves();
		}
		if (!$this->collEleves->contains($eleve)) { // only add it if the **same** object is not already associated
			$this->doAddEleve($eleve);

			$this->collEleves[]= $eleve;
		}
	}

	/**
	 * @param	Eleve $eleve The eleve object to add.
	 */
	protected function doAddEleve($eleve)
	{
		$jAidEleves = new JAidEleves();
		$jAidEleves->setEleve($eleve);
		$this->addJAidEleves($jAidEleves);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->nom = null;
		$this->numero = null;
		$this->indice_aid = null;
		$this->perso1 = null;
		$this->perso2 = null;
		$this->perso3 = null;
		$this->productions = null;
		$this->resume = null;
		$this->famille = null;
		$this->mots_cles = null;
		$this->adresse1 = null;
		$this->adresse2 = null;
		$this->public_destinataire = null;
		$this->contacts = null;
		$this->divers = null;
		$this->matiere1 = null;
		$this->matiere2 = null;
		$this->eleve_peut_modifier = null;
		$this->prof_peut_modifier = null;
		$this->cpe_peut_modifier = null;
		$this->fiche_publique = null;
		$this->affiche_adresse1 = null;
		$this->en_construction = null;
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
			if ($this->collJAidUtilisateursProfessionnelss) {
				foreach ($this->collJAidUtilisateursProfessionnelss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJAidElevess) {
				foreach ($this->collJAidElevess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collAbsenceEleveSaisies) {
				foreach ($this->collAbsenceEleveSaisies as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEdtEmplacementCourss) {
				foreach ($this->collEdtEmplacementCourss as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collUtilisateurProfessionnels) {
				foreach ($this->collUtilisateurProfessionnels as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collEleves) {
				foreach ($this->collEleves as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		if ($this->collJAidUtilisateursProfessionnelss instanceof PropelCollection) {
			$this->collJAidUtilisateursProfessionnelss->clearIterator();
		}
		$this->collJAidUtilisateursProfessionnelss = null;
		if ($this->collJAidElevess instanceof PropelCollection) {
			$this->collJAidElevess->clearIterator();
		}
		$this->collJAidElevess = null;
		if ($this->collAbsenceEleveSaisies instanceof PropelCollection) {
			$this->collAbsenceEleveSaisies->clearIterator();
		}
		$this->collAbsenceEleveSaisies = null;
		if ($this->collEdtEmplacementCourss instanceof PropelCollection) {
			$this->collEdtEmplacementCourss->clearIterator();
		}
		$this->collEdtEmplacementCourss = null;
		if ($this->collUtilisateurProfessionnels instanceof PropelCollection) {
			$this->collUtilisateurProfessionnels->clearIterator();
		}
		$this->collUtilisateurProfessionnels = null;
		if ($this->collEleves instanceof PropelCollection) {
			$this->collEleves->clearIterator();
		}
		$this->collEleves = null;
		$this->aAidConfiguration = null;
	}

	/**
	 * Return the string representation of this object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->exportTo(AidDetailsPeer::DEFAULT_STRING_FORMAT);
	}

} // BaseAidDetails
