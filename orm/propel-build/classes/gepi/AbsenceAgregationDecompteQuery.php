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
     * 
     * @param  DateTime $date_debut, $date_fin Dates de début et de fin de l'extraction des demi journées
     * @return    AbsenceAgregationDecompteQuery The current query, for fluid interface
     */
    public function filterByDateIntervalle($date_debut=Null,  $date_fin=Null) {
        
        if (is_null($date_debut) || is_null($date_fin)) {
            require_once("helpers/EdtHelper.php");
            if (is_null($date_debut)) {
                $date_debut = EdtHelper::getPremierJourAnneeScolaire();
            }
            if (is_null($date_fin)) {
                $date_fin = EdtHelper::getDernierJourAnneeScolaire();
            }
        }
        $this->filterByDateDemiJounee($date_debut, Criteria::GREATER_EQUAL)
             ->filterByDateDemiJounee($date_fin, Criteria::LESS_EQUAL);
        return $this;
    }
    
    /**
     * Retourne le nombre de retards (somme des demi journées de la requête)     * 
     * 
     * @return    int $nbreRetards Nbre de retards 
     */
    public function countRetards() {
        
        $absAgregationCol=$this->find();
        $nbreRetards=0;
        if ($absAgregationCol->isEmpty()){
            return $nbreRetards;
        }else{
            foreach($absAgregationCol as $demiJournee){
                $nbreRetards=$nbreRetards+$demiJournee->getNbRetards();
            }
            return($nbreRetards);
        }        
    }
} // AbsenceAgregationDecompteQuery
