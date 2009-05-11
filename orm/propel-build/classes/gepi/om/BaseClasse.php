<?php

/**
 * Base class that represents a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * @package    gepi.om
 */
abstract class BaseClasse extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        ClassePeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the classe field.
	 * @var        string
	 */
	protected $classe;

	/**
	 * The value for the nom_complet field.
	 * @var        string
	 */
	protected $nom_complet;

	/**
	 * The value for the suivi_par field.
	 * @var        string
	 */
	protected $suivi_par;

	/**
	 * The value for the formule field.
	 * @var        string
	 */
	protected $formule;

	/**
	 * The value for the format_nom field.
	 * @var        string
	 */
	protected $format_nom;

	/**
	 * The value for the display_rang field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_rang;

	/**
	 * The value for the display_address field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_address;

	/**
	 * The value for the display_coef field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $display_coef;

	/**
	 * The value for the display_mat_cat field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_mat_cat;

	/**
	 * The value for the display_nbdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $display_nbdev;

	/**
	 * The value for the display_moy_gen field.
	 * Note: this column has a database default value of: 'y'
	 * @var        string
	 */
	protected $display_moy_gen;

	/**
	 * The value for the modele_bulletin_pdf field.
	 * @var        string
	 */
	protected $modele_bulletin_pdf;

	/**
	 * The value for the rn_nomdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_nomdev;

	/**
	 * The value for the rn_toutcoefdev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_toutcoefdev;

	/**
	 * The value for the rn_coefdev_si_diff field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_coefdev_si_diff;

	/**
	 * The value for the rn_datedev field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_datedev;

	/**
	 * The value for the rn_sign_chefetab field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_chefetab;

	/**
	 * The value for the rn_sign_pp field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_pp;

	/**
	 * The value for the rn_sign_resp field.
	 * Note: this column has a database default value of: 'n'
	 * @var        string
	 */
	protected $rn_sign_resp;

	/**
	 * The value for the rn_sign_nblig field.
	 * Note: this column has a database default value of: 3
	 * @var        int
	 */
	protected $rn_sign_nblig;

	/**
	 * The value for the rn_formule field.
	 * @var        string
	 */
	protected $rn_formule;

	/**
	 * The value for the ects_type_formation field.
	 * @var        string
	 */
	protected $ects_type_formation;

	/**
	 * The value for the ects_parcours field.
	 * @var        string
	 */
	protected $ects_parcours;

	/**
	 * The value for the ects_code_parcours field.
	 * @var        string
	 */
	protected $ects_code_parcours;

	/**
	 * The value for the ects_domaines_etude field.
	 * @var        string
	 */
	protected $ects_domaines_etude;

	/**
	 * The value for the ects_fonction_signataire_attestation field.
	 * @var        string
	 */
	protected $ects_fonction_signataire_attestation;

	/**
	 * @var        array JGroupesClasses[] Collection to store aggregation of JGroupesClasses objects.
	 */
	protected $collJGroupesClassess;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJGroupesClassess.
	 */
	private $lastJGroupesClassesCriteria = null;

	/**
	 * @var        array JEleveClasse[] Collection to store aggregation of JEleveClasse objects.
	 */
	protected $collJEleveClasses;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJEleveClasses.
	 */
	private $lastJEleveClasseCriteria = null;

	/**
	 * @var        array JEleveProfesseurPrincipal[] Collection to store aggregation of JEleveProfesseurPrincipal objects.
	 */
	protected $collJEleveProfesseurPrincipals;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJEleveProfesseurPrincipals.
	 */
	private $lastJEleveProfesseurPrincipalCriteria = null;

	/**
	 * @var        array JCategoriesMatieresClasses[] Collection to store aggregation of JCategoriesMatieresClasses objects.
	 */
	protected $collJCategoriesMatieresClassess;

	/**
	 * @var        Criteria The criteria used to select the current contents of collJCategoriesMatieresClassess.
	 */
	private $lastJCategoriesMatieresClassesCriteria = null;

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
	 * Initializes internal state of BaseClasse object.
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
		$this->display_rang = 'n';
		$this->display_address = 'n';
		$this->display_coef = 'y';
		$this->display_mat_cat = 'n';
		$this->display_nbdev = 'n';
		$this->display_moy_gen = 'y';
		$this->rn_nomdev = 'n';
		$this->rn_toutcoefdev = 'n';
		$this->rn_coefdev_si_diff = 'n';
		$this->rn_datedev = 'n';
		$this->rn_sign_chefetab = 'n';
		$this->rn_sign_pp = 'n';
		$this->rn_sign_resp = 'n';
		$this->rn_sign_nblig = 3;
	}

	/**
	 * Get the [id] column value.
	 * Cle primaire de la classe
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [classe] column value.
	 * nom de la classe
	 * @return     string
	 */
	public function getClasse()
	{
		return $this->classe;
	}

	/**
	 * Get the [nom_complet] column value.
	 * nom complet de la classe
	 * @return     string
	 */
	public function getNomComplet()
	{
		return $this->nom_complet;
	}

	/**
	 * Get the [suivi_par] column value.
	 * 
	 * @return     string
	 */
	public function getSuiviPar()
	{
		return $this->suivi_par;
	}

	/**
	 * Get the [formule] column value.
	 * 
	 * @return     string
	 */
	public function getFormule()
	{
		return $this->formule;
	}

	/**
	 * Get the [format_nom] column value.
	 * 
	 * @return     string
	 */
	public function getFormatNom()
	{
		return $this->format_nom;
	}

	/**
	 * Get the [display_rang] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayRang()
	{
		return $this->display_rang;
	}

	/**
	 * Get the [display_address] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayAddress()
	{
		return $this->display_address;
	}

	/**
	 * Get the [display_coef] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayCoef()
	{
		return $this->display_coef;
	}

	/**
	 * Get the [display_mat_cat] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayMatCat()
	{
		return $this->display_mat_cat;
	}

	/**
	 * Get the [display_nbdev] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayNbdev()
	{
		return $this->display_nbdev;
	}

	/**
	 * Get the [display_moy_gen] column value.
	 * 
	 * @return     string
	 */
	public function getDisplayMoyGen()
	{
		return $this->display_moy_gen;
	}

	/**
	 * Get the [modele_bulletin_pdf] column value.
	 * 
	 * @return     string
	 */
	public function getModeleBulletinPdf()
	{
		return $this->modele_bulletin_pdf;
	}

	/**
	 * Get the [rn_nomdev] column value.
	 * 
	 * @return     string
	 */
	public function getRnNomdev()
	{
		return $this->rn_nomdev;
	}

	/**
	 * Get the [rn_toutcoefdev] column value.
	 * 
	 * @return     string
	 */
	public function getRnToutcoefdev()
	{
		return $this->rn_toutcoefdev;
	}

	/**
	 * Get the [rn_coefdev_si_diff] column value.
	 * 
	 * @return     string
	 */
	public function getRnCoefdevSiDiff()
	{
		return $this->rn_coefdev_si_diff;
	}

	/**
	 * Get the [rn_datedev] column value.
	 * 
	 * @return     string
	 */
	public function getRnDatedev()
	{
		return $this->rn_datedev;
	}

	/**
	 * Get the [rn_sign_chefetab] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignChefetab()
	{
		return $this->rn_sign_chefetab;
	}

	/**
	 * Get the [rn_sign_pp] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignPp()
	{
		return $this->rn_sign_pp;
	}

	/**
	 * Get the [rn_sign_resp] column value.
	 * 
	 * @return     string
	 */
	public function getRnSignResp()
	{
		return $this->rn_sign_resp;
	}

	/**
	 * Get the [rn_sign_nblig] column value.
	 * 
	 * @return     int
	 */
	public function getRnSignNblig()
	{
		return $this->rn_sign_nblig;
	}

	/**
	 * Get the [rn_formule] column value.
	 * 
	 * @return     string
	 */
	public function getRnFormule()
	{
		return $this->rn_formule;
	}

	/**
	 * Get the [ects_type_formation] column value.
	 * 
	 * @return     string
	 */
	public function getEctsTypeFormation()
	{
		return $this->ects_type_formation;
	}

	/**
	 * Get the [ects_parcours] column value.
	 * 
	 * @return     string
	 */
	public function getEctsParcours()
	{
		return $this->ects_parcours;
	}

	/**
	 * Get the [ects_code_parcours] column value.
	 * 
	 * @return     string
	 */
	public function getEctsCodeParcours()
	{
		return $this->ects_code_parcours;
	}

	/**
	 * Get the [ects_domaines_etude] column value.
	 * 
	 * @return     string
	 */
	public function getEctsDomainesEtude()
	{
		return $this->ects_domaines_etude;
	}

	/**
	 * Get the [ects_fonction_signataire_attestation] column value.
	 * 
	 * @return     string
	 */
	public function getEctsFonctionSignataireAttestation()
	{
		return $this->ects_fonction_signataire_attestation;
	}

	/**
	 * Set the value of [id] column.
	 * Cle primaire de la classe
	 * @param      int $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = ClassePeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [classe] column.
	 * nom de la classe
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setClasse($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->classe !== $v) {
			$this->classe = $v;
			$this->modifiedColumns[] = ClassePeer::CLASSE;
		}

		return $this;
	} // setClasse()

	/**
	 * Set the value of [nom_complet] column.
	 * nom complet de la classe
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setNomComplet($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->nom_complet !== $v) {
			$this->nom_complet = $v;
			$this->modifiedColumns[] = ClassePeer::NOM_COMPLET;
		}

		return $this;
	} // setNomComplet()

	/**
	 * Set the value of [suivi_par] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setSuiviPar($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->suivi_par !== $v) {
			$this->suivi_par = $v;
			$this->modifiedColumns[] = ClassePeer::SUIVI_PAR;
		}

		return $this;
	} // setSuiviPar()

	/**
	 * Set the value of [formule] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setFormule($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->formule !== $v) {
			$this->formule = $v;
			$this->modifiedColumns[] = ClassePeer::FORMULE;
		}

		return $this;
	} // setFormule()

	/**
	 * Set the value of [format_nom] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setFormatNom($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->format_nom !== $v) {
			$this->format_nom = $v;
			$this->modifiedColumns[] = ClassePeer::FORMAT_NOM;
		}

		return $this;
	} // setFormatNom()

	/**
	 * Set the value of [display_rang] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayRang($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_rang !== $v || $v === 'n') {
			$this->display_rang = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_RANG;
		}

		return $this;
	} // setDisplayRang()

	/**
	 * Set the value of [display_address] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayAddress($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_address !== $v || $v === 'n') {
			$this->display_address = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_ADDRESS;
		}

		return $this;
	} // setDisplayAddress()

	/**
	 * Set the value of [display_coef] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayCoef($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_coef !== $v || $v === 'y') {
			$this->display_coef = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_COEF;
		}

		return $this;
	} // setDisplayCoef()

	/**
	 * Set the value of [display_mat_cat] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayMatCat($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_mat_cat !== $v || $v === 'n') {
			$this->display_mat_cat = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_MAT_CAT;
		}

		return $this;
	} // setDisplayMatCat()

	/**
	 * Set the value of [display_nbdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayNbdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_nbdev !== $v || $v === 'n') {
			$this->display_nbdev = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_NBDEV;
		}

		return $this;
	} // setDisplayNbdev()

	/**
	 * Set the value of [display_moy_gen] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setDisplayMoyGen($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->display_moy_gen !== $v || $v === 'y') {
			$this->display_moy_gen = $v;
			$this->modifiedColumns[] = ClassePeer::DISPLAY_MOY_GEN;
		}

		return $this;
	} // setDisplayMoyGen()

	/**
	 * Set the value of [modele_bulletin_pdf] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setModeleBulletinPdf($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->modele_bulletin_pdf !== $v) {
			$this->modele_bulletin_pdf = $v;
			$this->modifiedColumns[] = ClassePeer::MODELE_BULLETIN_PDF;
		}

		return $this;
	} // setModeleBulletinPdf()

	/**
	 * Set the value of [rn_nomdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnNomdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_nomdev !== $v || $v === 'n') {
			$this->rn_nomdev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_NOMDEV;
		}

		return $this;
	} // setRnNomdev()

	/**
	 * Set the value of [rn_toutcoefdev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnToutcoefdev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_toutcoefdev !== $v || $v === 'n') {
			$this->rn_toutcoefdev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_TOUTCOEFDEV;
		}

		return $this;
	} // setRnToutcoefdev()

	/**
	 * Set the value of [rn_coefdev_si_diff] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnCoefdevSiDiff($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_coefdev_si_diff !== $v || $v === 'n') {
			$this->rn_coefdev_si_diff = $v;
			$this->modifiedColumns[] = ClassePeer::RN_COEFDEV_SI_DIFF;
		}

		return $this;
	} // setRnCoefdevSiDiff()

	/**
	 * Set the value of [rn_datedev] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnDatedev($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_datedev !== $v || $v === 'n') {
			$this->rn_datedev = $v;
			$this->modifiedColumns[] = ClassePeer::RN_DATEDEV;
		}

		return $this;
	} // setRnDatedev()

	/**
	 * Set the value of [rn_sign_chefetab] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignChefetab($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_chefetab !== $v || $v === 'n') {
			$this->rn_sign_chefetab = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_CHEFETAB;
		}

		return $this;
	} // setRnSignChefetab()

	/**
	 * Set the value of [rn_sign_pp] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignPp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_pp !== $v || $v === 'n') {
			$this->rn_sign_pp = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_PP;
		}

		return $this;
	} // setRnSignPp()

	/**
	 * Set the value of [rn_sign_resp] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignResp($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_sign_resp !== $v || $v === 'n') {
			$this->rn_sign_resp = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_RESP;
		}

		return $this;
	} // setRnSignResp()

	/**
	 * Set the value of [rn_sign_nblig] column.
	 * 
	 * @param      int $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnSignNblig($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rn_sign_nblig !== $v || $v === 3) {
			$this->rn_sign_nblig = $v;
			$this->modifiedColumns[] = ClassePeer::RN_SIGN_NBLIG;
		}

		return $this;
	} // setRnSignNblig()

	/**
	 * Set the value of [rn_formule] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setRnFormule($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rn_formule !== $v) {
			$this->rn_formule = $v;
			$this->modifiedColumns[] = ClassePeer::RN_FORMULE;
		}

		return $this;
	} // setRnFormule()

	/**
	 * Set the value of [ects_type_formation] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsTypeFormation($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_type_formation !== $v) {
			$this->ects_type_formation = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_TYPE_FORMATION;
		}

		return $this;
	} // setEctsTypeFormation()

	/**
	 * Set the value of [ects_parcours] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsParcours($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_parcours !== $v) {
			$this->ects_parcours = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_PARCOURS;
		}

		return $this;
	} // setEctsParcours()

	/**
	 * Set the value of [ects_code_parcours] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsCodeParcours($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_code_parcours !== $v) {
			$this->ects_code_parcours = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_CODE_PARCOURS;
		}

		return $this;
	} // setEctsCodeParcours()

	/**
	 * Set the value of [ects_domaines_etude] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsDomainesEtude($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_domaines_etude !== $v) {
			$this->ects_domaines_etude = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_DOMAINES_ETUDE;
		}

		return $this;
	} // setEctsDomainesEtude()

	/**
	 * Set the value of [ects_fonction_signataire_attestation] column.
	 * 
	 * @param      string $v new value
	 * @return     Classe The current object (for fluent API support)
	 */
	public function setEctsFonctionSignataireAttestation($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ects_fonction_signataire_attestation !== $v) {
			$this->ects_fonction_signataire_attestation = $v;
			$this->modifiedColumns[] = ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION;
		}

		return $this;
	} // setEctsFonctionSignataireAttestation()

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
			if (array_diff($this->modifiedColumns, array(ClassePeer::DISPLAY_RANG,ClassePeer::DISPLAY_ADDRESS,ClassePeer::DISPLAY_COEF,ClassePeer::DISPLAY_MAT_CAT,ClassePeer::DISPLAY_NBDEV,ClassePeer::DISPLAY_MOY_GEN,ClassePeer::RN_NOMDEV,ClassePeer::RN_TOUTCOEFDEV,ClassePeer::RN_COEFDEV_SI_DIFF,ClassePeer::RN_DATEDEV,ClassePeer::RN_SIGN_CHEFETAB,ClassePeer::RN_SIGN_PP,ClassePeer::RN_SIGN_RESP,ClassePeer::RN_SIGN_NBLIG))) {
				return false;
			}

			if ($this->display_rang !== 'n') {
				return false;
			}

			if ($this->display_address !== 'n') {
				return false;
			}

			if ($this->display_coef !== 'y') {
				return false;
			}

			if ($this->display_mat_cat !== 'n') {
				return false;
			}

			if ($this->display_nbdev !== 'n') {
				return false;
			}

			if ($this->display_moy_gen !== 'y') {
				return false;
			}

			if ($this->rn_nomdev !== 'n') {
				return false;
			}

			if ($this->rn_toutcoefdev !== 'n') {
				return false;
			}

			if ($this->rn_coefdev_si_diff !== 'n') {
				return false;
			}

			if ($this->rn_datedev !== 'n') {
				return false;
			}

			if ($this->rn_sign_chefetab !== 'n') {
				return false;
			}

			if ($this->rn_sign_pp !== 'n') {
				return false;
			}

			if ($this->rn_sign_resp !== 'n') {
				return false;
			}

			if ($this->rn_sign_nblig !== 3) {
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
			$this->classe = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->nom_complet = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->suivi_par = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->formule = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->format_nom = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->display_rang = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->display_address = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->display_coef = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->display_mat_cat = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->display_nbdev = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->display_moy_gen = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->modele_bulletin_pdf = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->rn_nomdev = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->rn_toutcoefdev = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->rn_coefdev_si_diff = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->rn_datedev = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->rn_sign_chefetab = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->rn_sign_pp = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->rn_sign_resp = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->rn_sign_nblig = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->rn_formule = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->ects_type_formation = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->ects_parcours = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->ects_code_parcours = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->ects_domaines_etude = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->ects_fonction_signataire_attestation = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 27; // 27 = ClassePeer::NUM_COLUMNS - ClassePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Classe object", $e);
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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = ClassePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collJGroupesClassess = null;
			$this->lastJGroupesClassesCriteria = null;

			$this->collJEleveClasses = null;
			$this->lastJEleveClasseCriteria = null;

			$this->collJEleveProfesseurPrincipals = null;
			$this->lastJEleveProfesseurPrincipalCriteria = null;

			$this->collJCategoriesMatieresClassess = null;
			$this->lastJCategoriesMatieresClassesCriteria = null;

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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			ClassePeer::doDelete($this, $con);
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
			$con = Propel::getConnection(ClassePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$affectedRows = $this->doSave($con);
			$con->commit();
			ClassePeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = ClassePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = ClassePeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += ClassePeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collJGroupesClassess !== null) {
				foreach ($this->collJGroupesClassess as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collJEleveClasses !== null) {
				foreach ($this->collJEleveClasses as $referrerFK) {
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

			if ($this->collJCategoriesMatieresClassess !== null) {
				foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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


			if (($retval = ClassePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collJGroupesClassess !== null) {
					foreach ($this->collJGroupesClassess as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collJEleveClasses !== null) {
					foreach ($this->collJEleveClasses as $referrerFK) {
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

				if ($this->collJCategoriesMatieresClassess !== null) {
					foreach ($this->collJCategoriesMatieresClassess as $referrerFK) {
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
		$pos = ClassePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getClasse();
				break;
			case 2:
				return $this->getNomComplet();
				break;
			case 3:
				return $this->getSuiviPar();
				break;
			case 4:
				return $this->getFormule();
				break;
			case 5:
				return $this->getFormatNom();
				break;
			case 6:
				return $this->getDisplayRang();
				break;
			case 7:
				return $this->getDisplayAddress();
				break;
			case 8:
				return $this->getDisplayCoef();
				break;
			case 9:
				return $this->getDisplayMatCat();
				break;
			case 10:
				return $this->getDisplayNbdev();
				break;
			case 11:
				return $this->getDisplayMoyGen();
				break;
			case 12:
				return $this->getModeleBulletinPdf();
				break;
			case 13:
				return $this->getRnNomdev();
				break;
			case 14:
				return $this->getRnToutcoefdev();
				break;
			case 15:
				return $this->getRnCoefdevSiDiff();
				break;
			case 16:
				return $this->getRnDatedev();
				break;
			case 17:
				return $this->getRnSignChefetab();
				break;
			case 18:
				return $this->getRnSignPp();
				break;
			case 19:
				return $this->getRnSignResp();
				break;
			case 20:
				return $this->getRnSignNblig();
				break;
			case 21:
				return $this->getRnFormule();
				break;
			case 22:
				return $this->getEctsTypeFormation();
				break;
			case 23:
				return $this->getEctsParcours();
				break;
			case 24:
				return $this->getEctsCodeParcours();
				break;
			case 25:
				return $this->getEctsDomainesEtude();
				break;
			case 26:
				return $this->getEctsFonctionSignataireAttestation();
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
		$keys = ClassePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getClasse(),
			$keys[2] => $this->getNomComplet(),
			$keys[3] => $this->getSuiviPar(),
			$keys[4] => $this->getFormule(),
			$keys[5] => $this->getFormatNom(),
			$keys[6] => $this->getDisplayRang(),
			$keys[7] => $this->getDisplayAddress(),
			$keys[8] => $this->getDisplayCoef(),
			$keys[9] => $this->getDisplayMatCat(),
			$keys[10] => $this->getDisplayNbdev(),
			$keys[11] => $this->getDisplayMoyGen(),
			$keys[12] => $this->getModeleBulletinPdf(),
			$keys[13] => $this->getRnNomdev(),
			$keys[14] => $this->getRnToutcoefdev(),
			$keys[15] => $this->getRnCoefdevSiDiff(),
			$keys[16] => $this->getRnDatedev(),
			$keys[17] => $this->getRnSignChefetab(),
			$keys[18] => $this->getRnSignPp(),
			$keys[19] => $this->getRnSignResp(),
			$keys[20] => $this->getRnSignNblig(),
			$keys[21] => $this->getRnFormule(),
			$keys[22] => $this->getEctsTypeFormation(),
			$keys[23] => $this->getEctsParcours(),
			$keys[24] => $this->getEctsCodeParcours(),
			$keys[25] => $this->getEctsDomainesEtude(),
			$keys[26] => $this->getEctsFonctionSignataireAttestation(),
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
		$pos = ClassePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setClasse($value);
				break;
			case 2:
				$this->setNomComplet($value);
				break;
			case 3:
				$this->setSuiviPar($value);
				break;
			case 4:
				$this->setFormule($value);
				break;
			case 5:
				$this->setFormatNom($value);
				break;
			case 6:
				$this->setDisplayRang($value);
				break;
			case 7:
				$this->setDisplayAddress($value);
				break;
			case 8:
				$this->setDisplayCoef($value);
				break;
			case 9:
				$this->setDisplayMatCat($value);
				break;
			case 10:
				$this->setDisplayNbdev($value);
				break;
			case 11:
				$this->setDisplayMoyGen($value);
				break;
			case 12:
				$this->setModeleBulletinPdf($value);
				break;
			case 13:
				$this->setRnNomdev($value);
				break;
			case 14:
				$this->setRnToutcoefdev($value);
				break;
			case 15:
				$this->setRnCoefdevSiDiff($value);
				break;
			case 16:
				$this->setRnDatedev($value);
				break;
			case 17:
				$this->setRnSignChefetab($value);
				break;
			case 18:
				$this->setRnSignPp($value);
				break;
			case 19:
				$this->setRnSignResp($value);
				break;
			case 20:
				$this->setRnSignNblig($value);
				break;
			case 21:
				$this->setRnFormule($value);
				break;
			case 22:
				$this->setEctsTypeFormation($value);
				break;
			case 23:
				$this->setEctsParcours($value);
				break;
			case 24:
				$this->setEctsCodeParcours($value);
				break;
			case 25:
				$this->setEctsDomainesEtude($value);
				break;
			case 26:
				$this->setEctsFonctionSignataireAttestation($value);
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
		$keys = ClassePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setClasse($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setNomComplet($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setSuiviPar($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setFormule($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setFormatNom($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDisplayRang($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDisplayAddress($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDisplayCoef($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDisplayMatCat($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDisplayNbdev($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDisplayMoyGen($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setModeleBulletinPdf($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRnNomdev($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setRnToutcoefdev($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setRnCoefdevSiDiff($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setRnDatedev($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setRnSignChefetab($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setRnSignPp($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setRnSignResp($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setRnSignNblig($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setRnFormule($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setEctsTypeFormation($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setEctsParcours($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setEctsCodeParcours($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setEctsDomainesEtude($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setEctsFonctionSignataireAttestation($arr[$keys[26]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(ClassePeer::DATABASE_NAME);

		if ($this->isColumnModified(ClassePeer::ID)) $criteria->add(ClassePeer::ID, $this->id);
		if ($this->isColumnModified(ClassePeer::CLASSE)) $criteria->add(ClassePeer::CLASSE, $this->classe);
		if ($this->isColumnModified(ClassePeer::NOM_COMPLET)) $criteria->add(ClassePeer::NOM_COMPLET, $this->nom_complet);
		if ($this->isColumnModified(ClassePeer::SUIVI_PAR)) $criteria->add(ClassePeer::SUIVI_PAR, $this->suivi_par);
		if ($this->isColumnModified(ClassePeer::FORMULE)) $criteria->add(ClassePeer::FORMULE, $this->formule);
		if ($this->isColumnModified(ClassePeer::FORMAT_NOM)) $criteria->add(ClassePeer::FORMAT_NOM, $this->format_nom);
		if ($this->isColumnModified(ClassePeer::DISPLAY_RANG)) $criteria->add(ClassePeer::DISPLAY_RANG, $this->display_rang);
		if ($this->isColumnModified(ClassePeer::DISPLAY_ADDRESS)) $criteria->add(ClassePeer::DISPLAY_ADDRESS, $this->display_address);
		if ($this->isColumnModified(ClassePeer::DISPLAY_COEF)) $criteria->add(ClassePeer::DISPLAY_COEF, $this->display_coef);
		if ($this->isColumnModified(ClassePeer::DISPLAY_MAT_CAT)) $criteria->add(ClassePeer::DISPLAY_MAT_CAT, $this->display_mat_cat);
		if ($this->isColumnModified(ClassePeer::DISPLAY_NBDEV)) $criteria->add(ClassePeer::DISPLAY_NBDEV, $this->display_nbdev);
		if ($this->isColumnModified(ClassePeer::DISPLAY_MOY_GEN)) $criteria->add(ClassePeer::DISPLAY_MOY_GEN, $this->display_moy_gen);
		if ($this->isColumnModified(ClassePeer::MODELE_BULLETIN_PDF)) $criteria->add(ClassePeer::MODELE_BULLETIN_PDF, $this->modele_bulletin_pdf);
		if ($this->isColumnModified(ClassePeer::RN_NOMDEV)) $criteria->add(ClassePeer::RN_NOMDEV, $this->rn_nomdev);
		if ($this->isColumnModified(ClassePeer::RN_TOUTCOEFDEV)) $criteria->add(ClassePeer::RN_TOUTCOEFDEV, $this->rn_toutcoefdev);
		if ($this->isColumnModified(ClassePeer::RN_COEFDEV_SI_DIFF)) $criteria->add(ClassePeer::RN_COEFDEV_SI_DIFF, $this->rn_coefdev_si_diff);
		if ($this->isColumnModified(ClassePeer::RN_DATEDEV)) $criteria->add(ClassePeer::RN_DATEDEV, $this->rn_datedev);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_CHEFETAB)) $criteria->add(ClassePeer::RN_SIGN_CHEFETAB, $this->rn_sign_chefetab);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_PP)) $criteria->add(ClassePeer::RN_SIGN_PP, $this->rn_sign_pp);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_RESP)) $criteria->add(ClassePeer::RN_SIGN_RESP, $this->rn_sign_resp);
		if ($this->isColumnModified(ClassePeer::RN_SIGN_NBLIG)) $criteria->add(ClassePeer::RN_SIGN_NBLIG, $this->rn_sign_nblig);
		if ($this->isColumnModified(ClassePeer::RN_FORMULE)) $criteria->add(ClassePeer::RN_FORMULE, $this->rn_formule);
		if ($this->isColumnModified(ClassePeer::ECTS_TYPE_FORMATION)) $criteria->add(ClassePeer::ECTS_TYPE_FORMATION, $this->ects_type_formation);
		if ($this->isColumnModified(ClassePeer::ECTS_PARCOURS)) $criteria->add(ClassePeer::ECTS_PARCOURS, $this->ects_parcours);
		if ($this->isColumnModified(ClassePeer::ECTS_CODE_PARCOURS)) $criteria->add(ClassePeer::ECTS_CODE_PARCOURS, $this->ects_code_parcours);
		if ($this->isColumnModified(ClassePeer::ECTS_DOMAINES_ETUDE)) $criteria->add(ClassePeer::ECTS_DOMAINES_ETUDE, $this->ects_domaines_etude);
		if ($this->isColumnModified(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION)) $criteria->add(ClassePeer::ECTS_FONCTION_SIGNATAIRE_ATTESTATION, $this->ects_fonction_signataire_attestation);

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
		$criteria = new Criteria(ClassePeer::DATABASE_NAME);

		$criteria->add(ClassePeer::ID, $this->id);

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
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Classe (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setClasse($this->classe);

		$copyObj->setNomComplet($this->nom_complet);

		$copyObj->setSuiviPar($this->suivi_par);

		$copyObj->setFormule($this->formule);

		$copyObj->setFormatNom($this->format_nom);

		$copyObj->setDisplayRang($this->display_rang);

		$copyObj->setDisplayAddress($this->display_address);

		$copyObj->setDisplayCoef($this->display_coef);

		$copyObj->setDisplayMatCat($this->display_mat_cat);

		$copyObj->setDisplayNbdev($this->display_nbdev);

		$copyObj->setDisplayMoyGen($this->display_moy_gen);

		$copyObj->setModeleBulletinPdf($this->modele_bulletin_pdf);

		$copyObj->setRnNomdev($this->rn_nomdev);

		$copyObj->setRnToutcoefdev($this->rn_toutcoefdev);

		$copyObj->setRnCoefdevSiDiff($this->rn_coefdev_si_diff);

		$copyObj->setRnDatedev($this->rn_datedev);

		$copyObj->setRnSignChefetab($this->rn_sign_chefetab);

		$copyObj->setRnSignPp($this->rn_sign_pp);

		$copyObj->setRnSignResp($this->rn_sign_resp);

		$copyObj->setRnSignNblig($this->rn_sign_nblig);

		$copyObj->setRnFormule($this->rn_formule);

		$copyObj->setEctsTypeFormation($this->ects_type_formation);

		$copyObj->setEctsParcours($this->ects_parcours);

		$copyObj->setEctsCodeParcours($this->ects_code_parcours);

		$copyObj->setEctsDomainesEtude($this->ects_domaines_etude);

		$copyObj->setEctsFonctionSignataireAttestation($this->ects_fonction_signataire_attestation);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getJGroupesClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJGroupesClasses($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveClasses() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveClasse($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJEleveProfesseurPrincipals() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJEleveProfesseurPrincipal($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getJCategoriesMatieresClassess() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addJCategoriesMatieresClasses($relObj->copy($deepCopy));
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
	 * @return     Classe Clone of current object.
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
	 * @return     ClassePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new ClassePeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collJGroupesClassess collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		$this->collJGroupesClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJGroupesClassess collection (array).
	 *
	 * By default this just sets the collJGroupesClassess collection to an empty array (like clearcollJGroupesClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJGroupesClassess()
	{
		$this->collJGroupesClassess = array();
	}

	/**
	 * Gets an array of JGroupesClasses objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Classe has previously been saved, it will retrieve
	 * related JGroupesClassess from storage. If this Classe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JGroupesClasses[]
	 * @throws     PropelException
	 */
	public function getJGroupesClassess($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
			   $this->collJGroupesClassess = array();
			} else {

				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				JGroupesClassesPeer::addSelectColumns($criteria);
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				JGroupesClassesPeer::addSelectColumns($criteria);
				if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
					$this->collJGroupesClassess = JGroupesClassesPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;
		return $this->collJGroupesClassess;
	}

	/**
	 * Returns the number of related JGroupesClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JGroupesClasses objects.
	 * @throws     PropelException
	 */
	public function countJGroupesClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				$count = JGroupesClassesPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
					$count = JGroupesClassesPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJGroupesClassess);
				}
			} else {
				$count = count($this->collJGroupesClassess);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JGroupesClasses object to this object
	 * through the JGroupesClasses foreign key attribute.
	 *
	 * @param      JGroupesClasses $l JGroupesClasses
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJGroupesClasses(JGroupesClasses $l)
	{
		if ($this->collJGroupesClassess === null) {
			$this->initJGroupesClassess();
		}
		if (!in_array($l, $this->collJGroupesClassess, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJGroupesClassess, $l);
			$l->setClasse($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJGroupesClassessJoinGroupe($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
				$this->collJGroupesClassess = array();
			} else {

				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

			if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinGroupe($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;

		return $this->collJGroupesClassess;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JGroupesClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJGroupesClassessJoinCategorieMatiere($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJGroupesClassess === null) {
			if ($this->isNew()) {
				$this->collJGroupesClassess = array();
			} else {

				$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JGroupesClassesPeer::ID_CLASSE, $this->id);

			if (!isset($this->lastJGroupesClassesCriteria) || !$this->lastJGroupesClassesCriteria->equals($criteria)) {
				$this->collJGroupesClassess = JGroupesClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		}
		$this->lastJGroupesClassesCriteria = $criteria;

		return $this->collJGroupesClassess;
	}

	/**
	 * Clears out the collJEleveClasses collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJEleveClasses()
	 */
	public function clearJEleveClasses()
	{
		$this->collJEleveClasses = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJEleveClasses collection (array).
	 *
	 * By default this just sets the collJEleveClasses collection to an empty array (like clearcollJEleveClasses());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJEleveClasses()
	{
		$this->collJEleveClasses = array();
	}

	/**
	 * Gets an array of JEleveClasse objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Classe has previously been saved, it will retrieve
	 * related JEleveClasses from storage. If this Classe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JEleveClasse[]
	 * @throws     PropelException
	 */
	public function getJEleveClasses($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveClasses === null) {
			if ($this->isNew()) {
			   $this->collJEleveClasses = array();
			} else {

				$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

				JEleveClassePeer::addSelectColumns($criteria);
				$this->collJEleveClasses = JEleveClassePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

				JEleveClassePeer::addSelectColumns($criteria);
				if (!isset($this->lastJEleveClasseCriteria) || !$this->lastJEleveClasseCriteria->equals($criteria)) {
					$this->collJEleveClasses = JEleveClassePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJEleveClasseCriteria = $criteria;
		return $this->collJEleveClasses;
	}

	/**
	 * Returns the number of related JEleveClasse objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JEleveClasse objects.
	 * @throws     PropelException
	 */
	public function countJEleveClasses(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJEleveClasses === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

				$count = JEleveClassePeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

				if (!isset($this->lastJEleveClasseCriteria) || !$this->lastJEleveClasseCriteria->equals($criteria)) {
					$count = JEleveClassePeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJEleveClasses);
				}
			} else {
				$count = count($this->collJEleveClasses);
			}
		}
		$this->lastJEleveClasseCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JEleveClasse object to this object
	 * through the JEleveClasse foreign key attribute.
	 *
	 * @param      JEleveClasse $l JEleveClasse
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJEleveClasse(JEleveClasse $l)
	{
		if ($this->collJEleveClasses === null) {
			$this->initJEleveClasses();
		}
		if (!in_array($l, $this->collJEleveClasses, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJEleveClasses, $l);
			$l->setClasse($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JEleveClasses from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJEleveClassesJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveClasses === null) {
			if ($this->isNew()) {
				$this->collJEleveClasses = array();
			} else {

				$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

				$this->collJEleveClasses = JEleveClassePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveClassePeer::ID_CLASSE, $this->id);

			if (!isset($this->lastJEleveClasseCriteria) || !$this->lastJEleveClasseCriteria->equals($criteria)) {
				$this->collJEleveClasses = JEleveClassePeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveClasseCriteria = $criteria;

		return $this->collJEleveClasses;
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
	 * Otherwise if this Classe has previously been saved, it will retrieve
	 * related JEleveProfesseurPrincipals from storage. If this Classe is new, it will return
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
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
			   $this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

				JEleveProfesseurPrincipalPeer::addSelectColumns($criteria);
				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

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
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
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

				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

				$count = JEleveProfesseurPrincipalPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

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
			$l->setClasse($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJEleveProfesseurPrincipalsJoinEleve($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
				$this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinEleve($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

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
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JEleveProfesseurPrincipals from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJEleveProfesseurPrincipalsJoinUtilisateurProfessionnel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJEleveProfesseurPrincipals === null) {
			if ($this->isNew()) {
				$this->collJEleveProfesseurPrincipals = array();
			} else {

				$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JEleveProfesseurPrincipalPeer::ID_CLASSE, $this->id);

			if (!isset($this->lastJEleveProfesseurPrincipalCriteria) || !$this->lastJEleveProfesseurPrincipalCriteria->equals($criteria)) {
				$this->collJEleveProfesseurPrincipals = JEleveProfesseurPrincipalPeer::doSelectJoinUtilisateurProfessionnel($criteria, $con, $join_behavior);
			}
		}
		$this->lastJEleveProfesseurPrincipalCriteria = $criteria;

		return $this->collJEleveProfesseurPrincipals;
	}

	/**
	 * Clears out the collJCategoriesMatieresClassess collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJCategoriesMatieresClassess()
	 */
	public function clearJCategoriesMatieresClassess()
	{
		$this->collJCategoriesMatieresClassess = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collJCategoriesMatieresClassess collection (array).
	 *
	 * By default this just sets the collJCategoriesMatieresClassess collection to an empty array (like clearcollJCategoriesMatieresClassess());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initJCategoriesMatieresClassess()
	{
		$this->collJCategoriesMatieresClassess = array();
	}

	/**
	 * Gets an array of JCategoriesMatieresClasses objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this Classe has previously been saved, it will retrieve
	 * related JCategoriesMatieresClassess from storage. If this Classe is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array JCategoriesMatieresClasses[]
	 * @throws     PropelException
	 */
	public function getJCategoriesMatieresClassess($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJCategoriesMatieresClassess === null) {
			if ($this->isNew()) {
			   $this->collJCategoriesMatieresClassess = array();
			} else {

				$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

				JCategoriesMatieresClassesPeer::addSelectColumns($criteria);
				$this->collJCategoriesMatieresClassess = JCategoriesMatieresClassesPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

				JCategoriesMatieresClassesPeer::addSelectColumns($criteria);
				if (!isset($this->lastJCategoriesMatieresClassesCriteria) || !$this->lastJCategoriesMatieresClassesCriteria->equals($criteria)) {
					$this->collJCategoriesMatieresClassess = JCategoriesMatieresClassesPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastJCategoriesMatieresClassesCriteria = $criteria;
		return $this->collJCategoriesMatieresClassess;
	}

	/**
	 * Returns the number of related JCategoriesMatieresClasses objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related JCategoriesMatieresClasses objects.
	 * @throws     PropelException
	 */
	public function countJCategoriesMatieresClassess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collJCategoriesMatieresClassess === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

				$count = JCategoriesMatieresClassesPeer::doCount($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

				if (!isset($this->lastJCategoriesMatieresClassesCriteria) || !$this->lastJCategoriesMatieresClassesCriteria->equals($criteria)) {
					$count = JCategoriesMatieresClassesPeer::doCount($criteria, $con);
				} else {
					$count = count($this->collJCategoriesMatieresClassess);
				}
			} else {
				$count = count($this->collJCategoriesMatieresClassess);
			}
		}
		$this->lastJCategoriesMatieresClassesCriteria = $criteria;
		return $count;
	}

	/**
	 * Method called to associate a JCategoriesMatieresClasses object to this object
	 * through the JCategoriesMatieresClasses foreign key attribute.
	 *
	 * @param      JCategoriesMatieresClasses $l JCategoriesMatieresClasses
	 * @return     void
	 * @throws     PropelException
	 */
	public function addJCategoriesMatieresClasses(JCategoriesMatieresClasses $l)
	{
		if ($this->collJCategoriesMatieresClassess === null) {
			$this->initJCategoriesMatieresClassess();
		}
		if (!in_array($l, $this->collJCategoriesMatieresClassess, true)) { // only add it if the **same** object is not already associated
			array_push($this->collJCategoriesMatieresClassess, $l);
			$l->setClasse($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Classe is new, it will return
	 * an empty collection; or if this Classe has previously
	 * been saved, it will retrieve related JCategoriesMatieresClassess from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in Classe.
	 */
	public function getJCategoriesMatieresClassessJoinCategorieMatiere($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(ClassePeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collJCategoriesMatieresClassess === null) {
			if ($this->isNew()) {
				$this->collJCategoriesMatieresClassess = array();
			} else {

				$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

				$this->collJCategoriesMatieresClassess = JCategoriesMatieresClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(JCategoriesMatieresClassesPeer::CLASSE_ID, $this->id);

			if (!isset($this->lastJCategoriesMatieresClassesCriteria) || !$this->lastJCategoriesMatieresClassesCriteria->equals($criteria)) {
				$this->collJCategoriesMatieresClassess = JCategoriesMatieresClassesPeer::doSelectJoinCategorieMatiere($criteria, $con, $join_behavior);
			}
		}
		$this->lastJCategoriesMatieresClassesCriteria = $criteria;

		return $this->collJCategoriesMatieresClassess;
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
			if ($this->collJGroupesClassess) {
				foreach ((array) $this->collJGroupesClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveClasses) {
				foreach ((array) $this->collJEleveClasses as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJEleveProfesseurPrincipals) {
				foreach ((array) $this->collJEleveProfesseurPrincipals as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collJCategoriesMatieresClassess) {
				foreach ((array) $this->collJCategoriesMatieresClassess as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collJGroupesClassess = null;
		$this->collJEleveClasses = null;
		$this->collJEleveProfesseurPrincipals = null;
		$this->collJCategoriesMatieresClassess = null;
	}

} // BaseClasse
