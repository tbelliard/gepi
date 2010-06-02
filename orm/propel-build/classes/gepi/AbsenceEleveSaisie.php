<?php



/**
 * Skeleton subclass for representing a row from the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveSaisie extends BaseAbsenceEleveSaisie {

	/**
	 *
	 * Renvoi true ou false en fonction de la coherence de la saisie
	 *
	 * @return     boolean
	 *
	 */
	public function getValidationFailures() {
	    $message = '';

	    //on exclus mutuellement un id_classe, et id_groupe et un id_aid
	    $id_relation = 0;
	    if ($this->getIdAid() != null && $this->getIdAid() != -1) {
		
		$id_relation = $id_relation + 1;
		if ($this->getAidDetails() == null) {
		    $message .= "L'id de l'aid est incorrect.<br/>";
		}

	    }
	    if ($this->getIdClasse() != null && $this->getIdClasse() != -1) {
		$id_relation = $id_relation + 1;
		if ($this->getClasse() == null) {
		    $message .= "L'id de la classe est incorrect.<br/>";
		}
	    }
	    if ($this->getIdGroupe() != null && $this->getIdGroupe() != -1) {
		$id_relation = $id_relation + 1;
		if ($this->getGroupe() == null) {
		    $message .= "L'id du groupe est incorrect.<br/>";
		}
	    }
	    if ($id_relation > 1) {
		$message .= "Il ne peut y avoir un groupe, une classe et une aid simultanéments pécisé.<br/>";
	    }

	    if ($this->getEleveId() != null && $this->getEleveId() != -1) {
		if ($this->getEleve() == null) {
		    $message .= "L'id de l'eleve est incorrect.<br/>";
		}
	    }

	    if ($this->getIdEdtEmplacementCours() != null && $this->getIdEdtEmplacementCours() != -1) {
		if ($this->getEdtEmplacementCours() == null) {
		    $message .= "L'id de l'emplacement cours est incorrect.<br/>";
		}

		//si on saisie un cours, alors le creneau doit etre vide ainsi le groupe, l'aid et la classe
		if ($this->getIdAid() != null || $this->getIdClasse() != null || $this->getIdGroupe() != null || $this->getIdEdtCreneau() != null) {
		    $message .= "Si un cours est precisé, l'aid, le groupe, la calsse et le creneau doivent etre nuls.<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($this->getAidDetails() != null && $this->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JAidElevesPeer::LOGIN, $this->getEleve()->getLogin());
		if ($this->getAidDetails()->countJAidElevess($criteria) == 0) {
		    $message .= "L'eleve n'appartient pas à l'aid selectionné : ".$this->getAidDetails()->getNom()."<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($this->getGroupe() != null && $this->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JEleveGroupePeer::LOGIN, $this->getEleve()->getLogin());
		if ($this->getGroupe()->countJEleveGroupes($criteria) == 0) {
		    $message .= "L'eleve n'appartient pas au groupe selectionné.<br/>";
		}
	    }

	    //si il y a un eleve, on verifie qu'il appartient bien au groupe, à la classe ou à l'aid précisé
	    if ($this->getClasse() != null && $this->getEleve() != null) {
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::LOGIN, $this->getEleve()->getLogin());
		if ($this->getClasse()->countJEleveClasses($criteria) == 0) {
		    $message .= "L'eleve n'appartient pas à la classe selectionnée.<br/>";
		}
	    }

	    if ($this->getUtilisateurId() == null) {
		$message .= "Il faut preciser l'utilisateur qui rentre la saisie.<br/>";
	    }

	    if ($this->getDebutAbs() == null) {
		$message .= "La date de debut d'absence ne doit pas etre nulle.<br/>";
	    }

	    if ($this->getFinAbs() == null) {
		 $message .= "La date de fin d'absence ne doit pas etre nulle.<br/>";
	    }

	    if ($this->getDebutAbs() >= $this->getFinAbs()) {
		 $message .= "La date de debut d'absence doit etre strictement anterieure à la date de fin.<br/>";
	    }

	    if ($this->getEdtEmplacementCours() != null) {
		//un emplacement de cours est saisie, il faut verifier que les heure de debut et de fin d'absences sont coherent avec l'emplacement de cours.
		if ($this->getDebutAbs("Hi") < $this->getEdtEmplacementCours()->getHeureDebut("Hi")) {
		    $message .= "L'heure de debut d'absence ne peut pas etre anterieure au cours.<br/>";
		} elseif ($this->getFinAbs("Hi") > $this->getEdtEmplacementCours()->getHeureFin("Hi")) {
		    $message .= "L'heure de fin d'absence ne peut pas etre posterieure au cours.<br/>";
		} elseif (($this->getFinAbs("U") - $this->getDebutAbs("U")) > ($this->getEdtEmplacementCours()->getHeureFin("U") - $this->getEdtEmplacementCours()->getHeureDebut("U"))) {
		    $message .= "La durée de l'absence ne peut pas etre superieure à la durée du cours (verifier les date).<br/>";
		}
	    }

	    return $message;
	}

	/**
	 *
	 * Renvoi true ou false en fonction de la coherence de la saisie
	 *
	 * @return     boolean
	 *
	 */
	public function isValid() {
	    if ($this->getValidationFailures() == '') {
		return true;
	    } else {
		return false;
	    }

	}

	public function preSave(PropelPDO $con = null) {
	    return $this->isValid();
	}

	/**
	 *
	 * Renvoi true ou false en fonction des types associé
	 *
	 * @return     boolean
	 *
	 */
	public function hasTypeSaisieDiscipline() {
	    $traitements = $this->getAbsenceEleveTraitements();
	    foreach ($traitements as $traitement) {
		if ($traitement->getAbsenceEleveType() != null &&
		    $traitement->getAbsenceEleveType()->getTypeSaisie() == 'DISCIPLINE') {
		    return true;
		}
	    }
	    return false;
	}
} // AbsenceEleveSaisie
