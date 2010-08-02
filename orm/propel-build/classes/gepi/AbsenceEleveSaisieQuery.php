<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durée (plusieurs jours), défini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisé dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisé dans debut_abs.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceEleveSaisieQuery extends BaseAbsenceEleveSaisieQuery {

	/**
	 * Filtre la requete sur les saisies qui chevauchent une plage de temps
	 *
	 * @param     dateTime $dt_debut
	 * @param     dateTime $dt_fin
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
        public function filterByPlageTemps($dt_debut = null, $dt_fin = null)
        {
	    if ($dt_debut != null && $dt_fin != null && $dt_debut == $dt_fin) {
		//on a pas une plage de temps mais deux fois le meme moment
		//on va renvoyer aussi les saisies qui debutent a ce momement
		$this->filterByFinAbs($dt_debut, Criteria::GREATER_THAN);
		$this->filterByDebutAbs($dt_fin, Criteria::LESS_EQUAL);
		return $this;
	    } else {
		if ($dt_debut != null) {
		    $this->filterByFinAbs($dt_debut, Criteria::GREATER_THAN);
		}
		if ($dt_fin != null) {
		    $this->filterByDebutAbs($dt_fin, Criteria::LESS_THAN);
		}
		return $this;
	    }
	    return $this;
        }

	/**
	 * Filtre la requete sur les saisies qui montre un manquement � l'obligation de presence de la part de l'eleve
	 *
	 * @param     boolean $value
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
        public function filterManquementObligationPresence($value = true)
        {
	    $this->setComment('filterManquementObligationPresence');
	    $this->groupById()
		->useJTraitementSaisieEleveQuery('', Criteria::LEFT_JOIN)
		->useAbsenceEleveTraitementQuery('', Criteria::LEFT_JOIN)
		->useAbsenceEleveTypeQuery('', Criteria::LEFT_JOIN)
		->endUse()->endUse()->endUse()
		->withColumn('group_concat(manquement_obligation_presence)', 'types_concat');
	    if ($value === true) {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c1);
		    $c->addOr($c2);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c->addAnd($c1);
		    $this->addHaving($c);
		}else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c1);
		    $this->addHaving($c);
		}
	    } else {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNOTNULL);
		    $c->addAnd($c1);
		    $c->addAnd($c2);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c1);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c->addOr($c1);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c2);
		    $this->addHaving($c);
		}else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNOTNULL);
		    $c->addAnd($c1);
		    $this->addHaving($c);
		}
	    }
	    return $this;
        }
	
} // AbsenceEleveSaisieQuery
