<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_traitements' table.
 *
 * Un traitement peut gerer plusieurs saisies et consiste Ã  definir les motifs/justifications... de ces absences saisies
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveTraitementQuery extends BaseAbsenceEleveTraitementQuery {

	/**
	 * Filtre la requete sur les traitements qui montrent un manquement à l'obligation de presence de la part de l'eleve
	 *
	 * @param     boolean $value
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
        public function filterByManquementObligationPresence($value = true) {
	    if ($value === true) {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y') {
		    $this->useAbsenceEleveTypeQuery('', Criteria::LEFT_JOIN)
			    ->filterByManquementObligationPresence(Array(null, AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI))
			    ->endUse();
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') {
		    $this->useAbsenceEleveTypeQuery('', Criteria::LEFT_JOIN)
			    ->filterByManquementObligationPresence(Array(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI))
			    ->endUse();
		}
	    } else {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y') {
		    $this->useAbsenceEleveTypeQuery('', Criteria::LEFT_JOIN)
			    ->filterByManquementObligationPresence(Array(null, AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI), Criteria::NOT_IN)
			    ->endUse();
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') {
		    $this->useAbsenceEleveTypeQuery('', Criteria::LEFT_JOIN)
			    ->filterByManquementObligationPresence(Array(AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI), Criteria::NOT_IN)
			    ->endUse();
		}
	    }
	    return $this;
	}
} // AbsenceEleveTraitementQuery
