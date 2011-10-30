<?php



/**
 * Skeleton subclass for performing query and update operations on the 'a_agregation_decompte' table.
 *
 * Table d'agregation des decomptes de demi journees d'absence et de retard
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class AbsenceAgregationDecompteQuery extends BaseAbsenceAgregationDecompteQuery {
    
    /**
     * Filtre la requete sur les dates de début et de fin. En cas de date nulle, 
     * le premier jour ou le dernier de l'année scolaire est utilisé
     * Pour la date de fin, la comparaison est stricte. Si la date de fin est 2010-10-04 00:00:00, on aura les décompte du 03 mais pas du 04 
     * Pour la demi journée, on se base sur 12h et sur les préférences de l'application gepi
     * 
     * @param  DateTime $date_debut, $date_fin Dates de début et de fin de l'extraction des demi journées
     * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
     */
    public function filterByDateIntervalle(DateTime $date_debut=Null, DateTime $date_fin=Null) {
        $this->filterByDateDemiJounee($date_debut, Criteria::GREATER_EQUAL)
             ->filterByDateDemiJounee($date_fin, Criteria::LESS_THAN);
        return $this;
    }
    
   /**
     * Compte le nombre de retard
     * Attention, la requete n'est pas réutilisable
     *
     * @return    int
     */
    public function countRetards() {
        $new_query = clone $this;
        $new_query->withColumn('SUM(AbsenceAgregationDecompte.Retards)', 'Retards')
                ->withColumn('1', 'dummy')
                ->groupBy('dummy');
        $retard = $new_query->find();
        if ($retard->isEmpty()) {
            return 0;
        } else {
            return $retard->getFirst()->getVirtualColumn('Retards');
        }
    }
    
    /**
     * Filtre la requete sur suivant que la marqueur de fin de calcul de mise a jours est présent
     * 
     * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
     */
    public function filterByMarqueurFinMiseAJour() {
    	return $this->filterByDateDemiJounee(null);
    }
} // AbsenceAgregationDecompteQuery
