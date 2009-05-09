<?php

require 'gepi/om/BaseClasse.php';


/**
 * Skeleton subclass for representing a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Classe extends BaseClasse {

	/**
	 * Initializes internal state of Classe object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	/**
	 * Renvoi sous forme d'un tableau la liste des groupes d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @return     array Classes[]
	 */
	public function getGroupes() {
		$groupes = array();
		foreach($this->getJGroupesClassessJoinGroupe() as $ref) {
			$groupes[] = $ref->getGroupe();
		}
		return $groupes;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eles d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @return     array Eleves[]
	 *
	 */
	public function getEleves($periode) {
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode);
		foreach($this->getJEleveClassesJoinEleve($criteria) as $ref) {
			$eleves[] = $ref->getEleve();
		}
		return $eleves;
	}

	public function getElevesByProfesseurPrincipal($login_prof) {
		$criteria = new Criteria();
		$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR,$login_prof);
		foreach($this->getJEleveProfesseurPrincipalsJoinEleve($criteria) as $ref) {
			$eleves[] = $ref->getEleve();
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a une classe
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
		$jEleveClasse = new JEleveClasse();
		$jEleveClasse->setEleve($eleve);
		$jEleveClasse->setPeriode($periode);
		$this->addJEleveClasse($jEleveClasse);
		$jEleveClasse->save();
	}

} // Classe
