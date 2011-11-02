<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_saisies' table.
 *
 * Chaque saisie d'absence doit faire l'objet d'une ligne dans la table a_saisies. Une saisie peut etre : une plage horaire longue durÃ©e (plusieurs jours), dÃ©fini avec les champs debut_abs et fin_abs. Un creneau horaire, le jour etant precisÃ© dans debut_abs. Un cours de l'emploi du temps, le jours du cours etant precisÃ© dans debut_abs.
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
	    if ($dt_debut != null && $dt_fin != null && $dt_debut->format('U') == $dt_fin->format('U')) {
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
	 * Filtre la requete sur les saisies qui montre un manquement à l'obligation de presence de la part de l'eleve
	 * Ce filtre peut provoquer des bug sur les requetes complexes. Il est alors possible d'utiliser le code suivant :
	 * $saisie_col = AbsenceEleveSaisieQuery::create()->filtreXXX()->filterByManquementObligationPresence()->setFormatter(ModelCriteria::FORMAT_ARRAY)->find();
	 * $eleve_col = $query
	 *	    ->useAbsenceEleveSaisieQuery()
	 *	    ->filterById($saisie_col->toKeyValue('Id', 'Id'))
	 *
	 * @param     boolean $value
	 *
	 * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
	 */
        public function filterByManquementObligationPresence($value = true)
        {
	    $this->setComment('filterByManquementObligationPresence');

	    $this
		->join('AbsenceEleveSaisie.JTraitementSaisieEleve', Criteria::LEFT_JOIN)
		->join('JTraitementSaisieEleve.AbsenceEleveTraitement', Criteria::LEFT_JOIN)
		->join('AbsenceEleveTraitement.AbsenceEleveType', Criteria::LEFT_JOIN)
		->withColumn('group_concat(manquement_obligation_presence)', 'types_concat')
		->groupById();
	    if ($value === true) {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    //$c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    //$c->addOr($c1);
		    $c->addOr($c2);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c->addAnd($c1);
		    $this->addHaving($c);
		}else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c1);
		    $this->addHaving($c);
		}
	    } else {
		if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    //$c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNOTNULL);
		    //$c->addAnd($c1);
		    $c->addAnd($c2);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")!='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c1);
		    $this->addHaving($c);
		} else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_VRAI.'%', Criteria::NOT_LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c->addOr($c1);
		    $c2 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNULL);
		    $c->addOr($c2);
		    $this->addHaving($c);
		}else if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")!='y' && getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		    $criteria = new Criteria();
		    $c = $criteria->getNewCriterion('types_concat', '%'.AbsenceEleveType::MANQU_OBLIG_PRESE_FAUX.'%', Criteria::LIKE);
		    $c1 = $criteria->getNewCriterion('types_concat', null, Criteria::ISNOTNULL);
		    $c->addAnd($c1);
		    $this->addHaving($c);
		}
	    }
	    return $this;
        }

        /**
     * Filtre la requete sur les saisies en fonction du lieu de l'élève
     *
     * @param     integer $idLieu L'id du lieu
     *
     * @return    AbsenceEleveSaisieQuery The current query, for fluid interface
     */
    public function filterByIdLieu($idLieu= null, $comparison=null) {

        $this
                ->join('AbsenceEleveSaisie.JTraitementSaisieEleve', Criteria::LEFT_JOIN)
                ->join('JTraitementSaisieEleve.AbsenceEleveTraitement', Criteria::LEFT_JOIN)
                ->join('AbsenceEleveTraitement.AbsenceEleveType', Criteria::LEFT_JOIN)
                ->withColumn('AbsenceEleveType.IdLieu', 'idlieu')
                ->groupById();
        $criteria = new Criteria();
        $c = $criteria->getNewCriterion('idlieu', $idLieu, Criteria::EQUAL);
        if ($idLieu == Null) {
            $c1 = $criteria->getNewCriterion('idlieu', null, Criteria::ISNULL);
            $c->addOr($c1);
        }
        $this->addHaving($c);

        return $this;
    }
} // AbsenceEleveSaisieQuery
