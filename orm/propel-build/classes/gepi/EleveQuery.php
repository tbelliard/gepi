<?php

/**
 * Skeleton subclass for performing query and update operations on the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EleveQuery extends BaseEleveQuery {

    /**
     * Ajoute une jointure avec les classes pour minimiser le nombre de requetes.
     *
     * @return    EleveQuery
     */
    public function joinWithClasses()
    {
	    $this
		->joinWith('Eleve.JEleveClasse', Criteria::LEFT_JOIN)
		->joinWith('JEleveClasse.Classe', Criteria::LEFT_JOIN);
	    return $this;
    }

    /**
     * Ajoute une jointure avec les periode pour minimiser le nombre de requetes.
     *
     * @return    EleveQuery
     */
    public function joinWithPeriodeNotes()
    {
	    $this
		->joinWith('Eleve.JEleveClasse', Criteria::LEFT_JOIN)
		->joinWith('JEleveClasse.Classe', Criteria::LEFT_JOIN)
		->joinWith('Classe.PeriodeNote', Criteria::LEFT_JOIN)
		->where('j_eleves_classes.periode = periodes.num_periode');
	    return $this;
    }


    /**
     * Filtre la requete sur le nom ou le prenom en recherchant une sous chaine
     *
     * @param     string $string sous chaine a rechercher
     *
     * @return    EleveQuery The current query, for fluid interface
     */
    public function filterByNomOrPrenomLike($string = '') {
        if ($string != '') {
            $this
                    ->condition('cond1_filterByNomOrPrenomLike', 'Eleve.Nom LIKE ?', '%' . $string . '%')
                    ->condition('cond2_filterByNomOrPrenomLik', 'Eleve.Prenom LIKE ?', '%' . $string . '%')
                    ->where(array('cond1_filterByNomOrPrenomLike', 'cond2_filterByNomOrPrenomLik'), 'or');
            return $this;
        } else {
            return $this;
        }
    }

    /**
     * Filtre la requete pour les eleves qui sont sous la responsabilite de l'utilisateur
     * en tant que prof principal, cpe ou scolarite
     *
     * @param     UtilisateurProfessionnel $utilisateurProfessionnel the related object to use as filter
     *
     * @return    EleveQuery The current query, for fluid interface
     */
    public function filterByUtilisateurProfessionnel($utilisateurProfessionnel) {
        if ($utilisateurProfessionnel == null ||
                ($utilisateurProfessionnel->getStatut() != "cpe"
                && $utilisateurProfessionnel->getStatut() != "professeur"
                && $utilisateurProfessionnel->getStatut() != "scolarite"
                && $utilisateurProfessionnel->getStatut() != "responsable"
                && $utilisateurProfessionnel->getStatut() != "autre")) {
            //on filtre tout
            return $this->where('1 <> 1');
        } elseif ($utilisateurProfessionnel->getStatut() == "cpe") {
            $this->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
            return $this;
        } elseif ($utilisateurProfessionnel->getStatut() == "autre") {
            //pas de filtrage, tout les eleves sont acceptes
            return $this;
        } else if ($utilisateurProfessionnel->getStatut() == "professeur") {
            $this->useJEleveProfesseurPrincipalQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
            return $this;
        } else if ($utilisateurProfessionnel->getStatut() == "scolarite") {
            $this->useJEleveClasseQuery()->addJoin(JEleveClassePeer::ID_CLASSE, JScolClassesPeer::ID_CLASSE, Criteria::INNER_JOIN)
                    ->add(JScolClassesPeer::LOGIN, $utilisateurProfessionnel->getLogin())
                    ->endUse()->distinct();
            return $this;
        } else if ($utilisateurProfessionnel->getStatut() == "responsable") {
            $this->useResponsableInformationQuery()->useResponsableEleveQuery()->filterByLogin($utilisateurProfessionnel->getLogin())->endUse()->endUse();
            return $this;
        }
    }

    /**
     * Filtre la requete pour les eleves en fonction de leur regime
     *
     *
     * @param     string $regime regime de l'eleve
     *
     * @return    EleveQuery The current query, for fluid interface
     */
    public function filterByRegime($regime) {
        return $this->useEleveRegimeDoublantQuery()
                ->filterByRegime($regime)
                ->endUse();
    }

    /**
     * Retourne la liste des eleves de la requete, en hydratant si necessaire les periodes
     * Issue a SELECT query based on the current ModelCriteria
     *
     *
     *
     * @param     PropelPDO $con an optional connection object
     *
     * @return     PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function find($con = null)
    {
	$result = parent::find($con);
	if (array_key_exists('PeriodeNote', $this->getWith())) {
	    //on va hydrater les periodes de note des eleves
	    foreach ($result as $eleve) {
		$eleve->hydratePeriodeNotes();
	    }
	}

	return $result;
    }



}

// EleveQuery
