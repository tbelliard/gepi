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
	 * Renvoi une description intelligible du traitement
	 *
	 * @return     String description
	 *
	 */
	public function getDescription() {
	    $desc = '';
	    if ($this->getEleve() != null) {
		$desc .= $this->getEleve()->getCivilite().' '.$this->getEleve()->getNom().' '.$this->getEleve()->getPrenom();
	    }
	    $desc .= ' '.$this->getDateDescription();
	    $desc .= ' ';
	    if ($this->getClasse() != null) {
		$desc .= "; classe : ".$this->getClasse()->getNomComplet();
	    }
	    if ($this->getGroupe() != null) {
		$desc .= "; groupe : ".$this->getGroupe()->getName();
	    }
	    if ($this->getAidDetails() != null) {
		$desc .= "; aid : ".$this->getAidDetails()->getNom();
	    }
	    if ($this->getCommentaire() != null && $this->getCommentaire() != '') {
		$desc .= "; ".$this->getCommentaire();
	    }
	    return $desc;
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

	/**
	 *
	 * Renvoi une chaine de caractere compréhensible concernant les dates de debut et de fin
	 *
	 * @return     string
	 *
	 */
	public function getDateDescription() {
	    $message = '';
	    if ($this->getDebutAbs('d/m/Y') == $this->getFinAbs('d/m/Y')) {
		$message .= 'Le ';
		$message .= (strftime("%a %d %b", $this->getDebutAbs('U')));
		$message .= ' de ';
		$message .= $this->getDebutAbs('H:i');
		$message .= ' à ';
		$message .= $this->getFinAbs('H:i');

	    } else {
		$message .= 'De ';
		$message .= (strftime("%a %d %b %H:%M", $this->getDebutAbs('U')));
		$message .= ' à ';
		$message .= (strftime("%a %d %b %H:%M", $this->getFinAbs('U')));
	    }
	    return $message;
	}

	/**
	 *
	 * Renvoi true ou false en fonction de la saisie
	 *
	 * @return     boolean
	 *
	 */
	public function getRetard() {
	    //est considéré retard toute absence inferieure a 30 min
	    //todo rendre ceci configurable
	    return (($this->getFinAbs('U') - $this->getDebutAbs('U')) < 60*30);
	}

	/**
	 *
	 * Renvoi true ou false si l'eleve etait sous la responsabilite de l'etablissement (infirmerie ou autre)
	 * si aucun traitement on renvoi faux par defaut
	 *
	 * @return     boolean
	 *
	 */
	public function getResponsabiliteEtablissement() {
	    $traitements = $this->getAbsenceEleveTraitements();
	    foreach ($traitements as $traitement) {
		if ($traitement->getAbsenceEleveType() != null &&
		    $traitement->getAbsenceEleveType()->getResponsabiliteEtablissement()) {
		    return true;
		}
	    }
	    return false;
	}

} // AbsenceEleveSaisie
