<?php

require 'gepi/om/BaseGroupe.php';


/**
 * Skeleton subclass for representing a row from the 'groupes' table.
 *
 * Groupe d'eleves permettant d'y affecter des matieres et des professeurs
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Groupe extends BaseGroupe {

	/**
	 * The value for DescriptionAvecClasses
	 * @var        string
	 */
	protected $descriptionAvecClasses;

	/**
	 * The value for NameAvecClasses
	 * @var        string
	 */
	protected $nameAvecClasses;

	/**
	 * Initializes internal state of Groupe object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un groupe.
	 * Manually added for N:M relationship
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Classes[]
	 *
	 */
	public function getClasses($con = null) {
		$classes = array();
		foreach($this->getJGroupesClassessJoinClasse($con) as $ref) {
			$classes[] = $ref->getClasse();
		}
		return $classes;
	}

	/**
	 * Renvoi la description du groupe avec la liste des classes associÃ©es
	 * @return     String
	 */
	public function getDescriptionAvecClasses() {
		if (isset($this->descriptionAvecClasses) && $this->descriptionAvecClasses != null) {
			return $this->descriptionAvecClasses;
		} else {
			$str = $this->getDescription();
			$str .= "&nbsp;(";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getClasse() . ",&nbsp;";
			}
			$str = substr($str, 0, -7);
			$str.= ")";
			$this->descriptionAvecClasses = $str;
			return $str;
		}
	}

	/**
	 * Renvoi le nom du groupe avec la liste des classes associÃ©es
	 * @return     string
	 */
	public function getNameAvecClasses() {
		if (isset($this->nameAvecClasses) && $this->nameAvecClasses != null) {
			return $this->nameAvecClasses;
		} else {
			$str = $this->getName();
			$str .= "&nbsp;-&nbsp;(";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getClasse() . ",&nbsp;";
			}
			$str = substr($str, 0, -7);
			$str.= ")";
			$this->nameAvecClasses = $str;
			return $str;
		}
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
		parent::clearJGroupesClassess();
		$descriptionAvecClasses = null;
		$nameAvecClasses = null;
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
	public function clearAllReferences($deep = false) {
		parent::clearAllReferences($deep);
		$this->clearJGroupesClassess();
	}

	/**
	 * Manually added
	 *
	 * La mÃ©thode renvoi true si le Groupe est affectÃ© Ã  l'utilisateur.
	 *
	 * @param      Utilisateur $utilisateur l'utilisateur Ã  qui appartient le groupe
	 * @return     boolean true si le groupe appartient Ã  l'utilisateur
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function belongsTo($utilisateur) {
		if (!isset($utilisateur) || $utilisateur == null) {
			return false;
		} elseif (!($utilisateur instanceof UtilisateurProfessionnel)) {
			throw new PropelException("L'objet passé n'est pas de la classe Utilisateur");
		} else {
			$group_appartient_utilisateur = false;
			foreach ($utilisateur->getGroupes() as $group_iter) {
				if ($this->getId() == $group_iter->getId()) {
					$group_appartient_utilisateur = true;
					break;
				}
			}
			return $group_appartient_utilisateur;
		}
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eles d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Eleves[]
	 *
	 */
	public function getEleves($periode) {
		$eleves = array();
		$criteria = new Criteria();
		$criteria->add(JEleveGroupePeer::PERIODE,$periode);
		foreach($this->getJEleveGroupesJoinEleve($criteria) as $ref) {
			$eleves[] = $ref->getEleve();
		}
		return $eleves;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des profs d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Eleves[]
	 *
	 */
	public function getProfesseurs() {
		$profs = array();
		$criteria = new Criteria();
		$criteria->add(JGroupesProfesseursPeer::ID_GROUPE,$this->getId());
		foreach($this->getJGroupesProfesseurssJoinUtilisateurProfessionnel($criteria) as $ref) {
			$profs[] = $ref->getUtilisateurProfessionnel();
		}
		return $profs;
	}

	/**
	 *
	 * Renvoi sous forme la valeur ECTS par défaut.
	 * Cette valeur se trouve à la jointure d'un groupe et d'une classe, elle n'est pas spécifique à un groupe
	 * En effet, il peut y avoir plusieurs eleves d'une meme classe qui sont regroupés dans un groupe, et pour ces eleves il est possible
	 * que la valeur par defaut soit différente.
	 * @periode integer numero de la periode
	 * @return     array Eleves[]
	 *
	 */
	public function getEctsDefaultValue($id_classe) {
		$criteria = new Criteria();
		$criteria->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($criteria);
        return !empty($g) > 0 ? $g[0]->getValeurEcts() : '';
	}

    public function allowsEctsCredits($id_classe) {
        $c = new Criteria();
        $c->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($c);
        return !empty($g) > 0 ? $g[0]->getSaisieEcts() : false;
    }

	public function getCategorieMatiere($id_classe) {
		$profs = array();
		$criteria = new Criteria();
		$criteria->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($criteria);
        return !empty($g) ? $g[0]->getCategorieMatiere() : false;
	}

	/**
	 *
	 * Ajoute un eleve a un groupe
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function addEleve(Eleve $eleve, $periode) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		$jEleveGroupe = new JEleveGroupe();
		$jEleveGroupe->setEleve($eleve);
		$jEleveGroupe->setPeriode($periode);
		$this->addJEleveGroupe($jEleveGroupe);
		$jEleveGroupe->save();
	}

} // Groupe
