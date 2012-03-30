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
        
        if (is_null($date_debut) || is_null($date_fin)) {
            require_once("helpers/EdtHelper.php");
            if (is_null($date_debut)) {
                $date_debut = EdtHelper::getPremierJourAnneeScolaire();
            }
            if (is_null($date_fin)) {
                $date_fin = EdtHelper::getDernierJourAnneeScolaire();
            }
        }

        $heure_demi_journee = 11;
        $minute_demi_journee = 50;
        if (getSettingValue("abs2_heure_demi_journee") != null) {
            try {
                $dt_demi_journee = new DateTime(getSettingValue("abs2_heure_demi_journee"));
                $heure_demi_journee = $dt_demi_journee->format('H');
                $minute_demi_journee = $dt_demi_journee->format('i');
            } catch (Exception $x) {
                
            }
        }
        if (!isset($dt_demi_journee) || $dt_demi_journee == null) {
            $dt_demi_journee = new DateTime($heure_demi_journee.':'.$minute_demi_journee);
        }
        $date_debut_clone=clone $date_debut;
        if ($date_debut_clone->format('Hi') < $heure_demi_journee . $minute_demi_journee) {
            $date_debut_clone->setTime(0, 0, 0);
        } else {
            $date_debut_clone->setTime(12, 0, 0);
        }
        $dt_demi_journee->modify("+1 hour");
        $dt_demi_journee->modify("+35 minutes");
        $heure_demi_journee = $dt_demi_journee->format('H');
        $minute_demi_journee = $dt_demi_journee->format('i'); 
        $date_fin_clone=clone $date_fin;
        if ($date_fin_clone->format('Hi') < $heure_demi_journee . $minute_demi_journee && $date_fin_clone->format('H:i')!="00:00" && $date_debut_clone->format('H:i')=="00:00" ) {
            $date_fin_clone->setTime(11, 59, 0);
        }         
        $this->filterByDateDemiJounee($date_debut_clone, Criteria::GREATER_EQUAL)
                ->filterByDateDemiJounee($date_fin_clone, Criteria::LESS_THAN);
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
        return $this->filterByDateDemiJounee('0001-01-01 00:00:00');
    }
} // AbsenceAgregationDecompteQuery
