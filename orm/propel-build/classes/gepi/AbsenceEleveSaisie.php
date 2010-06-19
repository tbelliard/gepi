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

    
	public function preSave(PropelPDO $con = null) {
	    return $this->validate();
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
