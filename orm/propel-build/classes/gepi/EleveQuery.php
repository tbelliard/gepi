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
} // EleveQuery
