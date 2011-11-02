<?php



/**
 * Skeleton subclass for performing query and update operations on the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class ClasseQuery extends BaseClasseQuery {
	/**
	 * Filtre la requete pour les classes qui sont sous la responsabilite de l'utilisateur
	 * en tant que prof principal, cpe ou scolarite
	 *
	 * @param     UtilisateurProfessionnel $utilisateurProfessionnel the related object to use as filter
	 *
	 * @return    ClasseQuery The current query, for fluid interface
	 */
        public function filterByUtilisateurProfessionnel($utilisateurProfessionnel)
        {
	    if ($utilisateurProfessionnel === null) {
		//on filtre tout
		return $this->where('1 <> 1');
	    } else if ($utilisateurProfessionnel->getStatut() == "professeur") {
		return $this->useJEleveProfesseurPrincipalQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
	    } else if ($utilisateurProfessionnel->getStatut() == "cpe") {
		return $this->useJEleveClasseQuery()->useEleveQuery()->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse()->endUse()->endUse();
	    } else if ($utilisateurProfessionnel->getStatut() == "scolarite") {
		return $this->useJScolClassesQuery()->filterByUtilisateurProfessionnel($utilisateurProfessionnel)->endUse();
	    } else {
		//on filtre tout
		return $this->where('1 <> 1');
	    }
	}

} // ClasseQuery
