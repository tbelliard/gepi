<?php


/**
 * Skeleton subclass for representing a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class Classe extends BaseClasse {

	/**
	 * Renvoi sous forme d'une collection la liste des groupes d'une classe.
	 *
	 * @return     PropelObjectCollection Classes[]
	 */
	public function getGroupes() {
		$groupes = new PropelObjectCollection();
		foreach($this->getJGroupesClassessJoinGroupe() as $ref) {
		    if ($ref->getGroupe() != null) {
			$groupes->append($ref->getGroupe());
		    }
		}
		return $groupes;
	}

	/**
	 *
	 * Renvoi sous forme d'une collection la liste des eleves d'une classe. 
	 * Si la periode de note est null, cela renvoi les eleves de la priode actuelle, ou tous les eleves si il n'y a aucune periode actuelle
	 *
	 * @return     PropelObjectCollection Eleves[]
	 *
	 */
	public function getEleves($num_periode_notes = null) {
		$eleves = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$num_periode_notes);
		foreach($this->getJEleveClassesJoinEleve($criteria) as $ref) {
		    if ($ref->getEleve() != null) {
			$eleves->append($ref->getEleve());
		    }
		}
		return $eleves;
	}

	public function getElevesByProfesseurPrincipal($login_prof) {
		$eleves = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR,$login_prof);
		foreach($this->getJEleveProfesseurPrincipalsJoinEleve($criteria) as $ref) {
		    if ($ref->getEleve() != null) {
			$eleves->append($ref->getEleve());
		    }
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a une classe. Si la periode de note est nulle, cela ajoute l'eleve la periode actuelle
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 */
	public function addEleve(Eleve $eleve, $num_periode_notes = null) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		$jEleveClasse = new JEleveClasse();
		$jEleveClasse->setEleve($eleve);
		$jEleveClasse->setPeriode($num_periode_notes);
		$this->addJEleveClasse($jEleveClasse);
		$jEleveClasse->save();
	}

 	/**
	 * Retourne la periode de note actuelle pour une classe donnée.
	 *
	 * @return     Periode $periode la periode actuellement totalement ouverte
	 */
	public static function getPeriodeNoteOuverteActuelle() {
		throw new PropelException("Pas encore implemente");
		return new Periode();
	}

 	/**
	 * Retourne la periode de note actuelle pour une classe donnée.
	 *
	 * @param  String $classe_id l'id de la classe
	 * @return PropelObjectCollection une collection d'emplacements de cours
	 */
	public static function getEdtEmplacementCoursJoinProfesseur($login) {
		throw new PropelException("Pas encore implemente");
		return new PropelObjectCollection();
	}

} // Classe
