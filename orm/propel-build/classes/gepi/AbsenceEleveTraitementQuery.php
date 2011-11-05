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
	 * @return    AbsenceEleveTraitementQuery The current query, for fluid interface
	 */
    public function filterByManquementObligationPresence($value = true) {
	    if ($value) {
    		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') {
    		    $this->joinAbsenceEleveType()
    		         ->where('AbsenceEleveType.ManquementObligationPresence = ?',AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
    		} else {//les saisies par défaut sont considérées comme des manquements
    		    $this->leftJoinAbsenceEleveType()
    			     ->where('AbsenceEleveType.Id IS NULL')
    			     ->_or()
    			     ->where('AbsenceEleveType.ManquementObligationPresence = ?',AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
    		}
	    } else {//on cherche les traitements qui ne sont pas des manquement à l'obligation de présence
    		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') {
    		    $this->leftJoinAbsenceEleveType()
    			     ->where('AbsenceEleveType.Id IS NULL')
    			     ->_or()
    		         ->where('AbsenceEleveType.ManquementObligationPresence <> ?',AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
    		} else {//les saisies par défaut sont considérées comme des manquements
    		    $this->joinAbsenceEleveType()
		             ->where('AbsenceEleveType.Id IS NOT NULL')
    			     ->where('AbsenceEleveType.ManquementObligationPresence <> ?',AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI);
    		}
	    }
	    return $this;
	}
} // AbsenceEleveTraitementQuery
