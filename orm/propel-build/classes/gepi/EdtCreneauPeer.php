<?php


/**
 * Skeleton subclass for performing query and update operations on the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCreneauPeer extends BaseEdtCreneauPeer {

  /**
   * Les types de creneaux possibles
   */
  public static $_type_creneaux = array("cours", "pause", "repas", "vie scolaire");

  /**
   * Renvoie la liste des creneaux de la journee
   *
   * @return array tableau d'objets creneau
   */
    public static function getAllEdtCreneauxOrderByTime(){
	    $criteria = new Criteria();
	    $criteria->addAscendingOrderByColumn(CreneauPeer::DEBUT_CRENEAU);
	    return self::doSelect($criteria);
    }

  /**
   * Renvoie le creneau précédent de celui passé en argument
   * Si l'id du creneau dépasse 3600 (ce qui parait peu probable tout de même), on teste sur l'heure de début
   *
   * @var $creneau id du creneau ou heure de début
   * @return object CreneauPeer precedent
   */
//  public static function getCreneauPrecedentCours($creneau){
//    $creneau_precedent = false;
//    $creneaux = self::getAllEdtCreneauxOrderByTime();
//    $nbre = count($creneaux);
//    $i = -1;
//
//    for($a = 0 ; $a < $nbre ; $a++){
//
//      if (is_numeric($creneau) AND $creneau < 3600){
//        // On peut rechercher par rapport à l'id
//        if ($creneaux[$a]->getId() == $creneau){
//          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
//          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
//          $i = $a-1; // un marqueur
//        }
//
//      }elseif($creneau > 3600 AND $creneau < 172800){
//        // On peut rechercher par rapport à l'heure de debut
//        if ($creneaux[$a]->getDebutCreneau() <= $creneau AND $creneaux[$a]->getFinCreneau() >= $creneau){
//          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
//          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
//          $i = $a-1; // un marqueur
//        }
//
//      }elseif($creneau > 172800){
//        // On est donc dans le cas d'un timestamp UNIX complet qu'il faut convertir avant de tester
//        $test = $creneau - self::timestampMinuit();
//        //echo '['.$a.'] - '.$test.' : de '.$creneaux[$a]->getDebutCreneau().' à '.$creneaux[$a]->getFinCreneau();
//        if ($test >= $creneaux[$a]->getDebutCreneau() AND $test < $creneaux[$a]->getFinCreneau()){
//          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
//          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
//          //echo ' tempo : '.$creneau_precedent_tempo->getId().'<br />';
//          $i = $a-1; // un marqueur
//        }
//      }
//    } // boucle for
//
//    // On vérifie le creneau precedent et on teste le premier qui correspond à un cours
//    if ($creneau_precedent_tempo === NULL){
//      $creneau_precedent = false;
//    }else if($creneau_precedent_tempo->getTypeCreneau() == 'cours'){
//      $creneau_precedent = $creneau_precedent_tempo;
//    }else{
//      // il faut rechercher le bon créneau précédent
//      for($t = $i ; $t !== 0 ; $t--){
//        if ($creneaux[$t]->getTypeCreneau() == 'cours'){
//          $creneau_precedent = $creneaux[$t];
//          break; // on arrête là la boucle
//        }
//      }
//    }
//
//    return $creneau_precedent;
//  }
} // EdtCreneauPeer
