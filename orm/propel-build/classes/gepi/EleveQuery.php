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
	 * Filtre la requete sur le nom ou le prenom en recherchant une sous chaine
	 *
	 * @param     string $string sous chaine a rechercher
	 *
	 * @return    EleveQuery The current query, for fluid interface
	 */
        public function filterByNomOrPrenomLike($string = '')
        {
	    if ($string != '') {
                $this
		->condition('cond1_filterByNomOrPrenomLike', 'Eleve.Nom LIKE ?', '%'.$string.'%')
		->condition('cond2_filterByNomOrPrenomLik', 'Eleve.Prenom LIKE ?', '%'.$string.'%')
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
        public function filterByUtilisateurProfessionnel($utilisateurProfessionnel)
        {
	    if ($utilisateurProfessionnel == null ||
		    ($utilisateurProfessionnel->getStatut() != "cpe"
		    && $utilisateurProfessionnel->getStatut() != "professeur"
		    && $utilisateurProfessionnel->getStatut() != "scolarite")) {
		//on filtre tout
		return $this->where('1 <> 1');
	    } elseif ($utilisateurProfessionnel->getStatut() == "cpe") {
		$this->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
		return $this;
	    } else if ($utilisateurProfessionnel->getStatut() == "professeur") {
		$this->useJEleveProfesseurPrincipalQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
		return $this;
	    } else if ($utilisateurProfessionnel->getStatut() == "scolarite") {
//		$this->join('JEleveClasse jel')
//			->joinJEleveClasse()
//			->join('j_scol_classes')
//			->where('JEleveClasse.IdClasse = JScolClasses.IdClasse')
//			->where('JScolClasses.login = ?', $utilisateurProfessionnel->getLogin());

		$this->useJEleveClasseQuery()
			->useClasseQuery()
			->useJScolClassesQuery()
			->filterByUtilisateurProfessionnel($utilisateurProfessionnel)
			->endUse()->endUse()->endUse();
		return $this;
	    }
        }
} // EleveQuery
