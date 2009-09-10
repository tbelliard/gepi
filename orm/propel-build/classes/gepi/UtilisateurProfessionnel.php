<?php

require 'gepi/om/BaseUtilisateurProfessionnel.php';


/**
 * Skeleton subclass for representing a row from the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class UtilisateurProfessionnel extends BaseUtilisateurProfessionnel {

	/**
	 * @var        array $collGroupes[] Collection to store aggregation of Groupe objects.
	 */
	protected $collGroupes;

	/**
	 * Initializes internal state of UtilisateurProfessionnel object.
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
	 * Renvoi sous forme d'un tableau la liste des groupes d'un utilisateur professeur. Le tableau est ordonné par le noms du groupes puis les classes du groupes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Groupes[]
	 */
	public function getGroupes($con = null) {
		if ($this->collGroupes != null) {
			return $this->collGroupes;
		} else {
			$groupes = array();
			foreach($this->getJGroupesProfesseurssJoinGroupe($con) as $ref) {
				$groupes[] = $ref->getGroupe();
			}
			require_once("helpers/GroupeHelper.php");
			$this->collGroupes = GroupeHelper::orderByGroupNameWithClasses($groupes);
			return $this->collGroupes;
		}
	}

	/**
	 * Clears out the collGroupes collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearGroupes()
	{
		$this->collGroupes = null; // important to set this to NULL since that means it is uninitialized
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
		parent::clearAllReferences($deep);
		$this->collGroupes = null;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eleves d'un utilisateur professeur principal.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function getEleveProfesseurPrincipals($con = null) {
		$eleves = array();
		foreach($this->getJEleveProfesseurPrincipalsJoinEleve() as $ref) {
			$eleves[] = $ref->getEleve();
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a un prof principal
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function addEleveProfesseurPrincipal(Eleve $eleve) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		$jEleveProfesseurPrincipal = new JEleveProfesseurPrincipal();
		$jEleveProfesseurPrincipal->setEleve($eleve);
		$this->addJEleveProfesseurPrincipal($jEleveProfesseurPrincipal);
		$jEleveProfesseurPrincipal->save();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eleves d'un utilisateur cpe
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function getEleveCpes($con = null) {
		$eleves = array();
		foreach($this->getJEleveCpes() as $ref) {
			$eleves[] = $ref->getEleve();
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a un prof principal
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function addEleveCpe(Eleve $eleve) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		$jEleveCpe = new JEleveCpe();
		$jEleveCpe->setEleve($eleve);
		$jEleveCpe->setUtilisateurProfessionnel($this);
		$this->addJEleveCpe($jEleveCpe);
		$jEleveCpe->save();
	}

	/**
	 *
	 * Renvoi une preference d'un utilisateur
	 * Ajout manuel
	 *
	 * @param      String $name le nom de la preference à obtenir
	 * @return     array Eleves[]
	 */
	public function getPreferenceValeur($name){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if (count($prefs) == 0) {
		return null;
	    } else {
		return $prefs[0]->getValue();
	    }
	}

	/**
	 *
	 * Enregistre une preference d'un utilisateur
	 * Ajout manuel
	 *
	 * @param      String $name le nom de la preference à obtenir
	 * @return     array Eleves[]
	 */
	public function setPreferenceValeur($name, $value){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if (count($prefs) == 0) {
		//Creation d'une nouvelle entree dans les preferences
		$nouvellePref = new PreferenceUtilisateurProfessionnel();
		$nouvellePref->setName($name);
		$nouvellePref->setValue($value);
		$nouvellePref->setLogin($this->getLogin());
		$nouvellePref->save();
		$this->addPreferenceUtilisateurProfessionnel($nouvellePref);
		$this->save();
	    } else if (count($prefs) == 1) {
		$prefs[0]->setValue($value);
		$prefs[0]->save();
	    } else {
		//there's an error
		throw new PropelException("Il existe deja plusieurs preferences avec ce nom !");
	    }
	}

}