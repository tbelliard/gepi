<?php

require 'gepi/om/BaseCreneauPeer.php';


/**
 * Skeleton subclass for performing query and update operations on the 'a_creneaux' table.
 *
 * Les creneaux sont la base du temps des eleves et des cours
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class CreneauPeer extends BaseCreneauPeer {

  /**
   * Mets en cache la liste des creneaux
   *
   * @var array creneaux
   */
	private static $_liste_creneaux = NULL;

  /**
   * Les types de creneaux possibles
   */
  private static $_type_creneaux = array("cours", "pause", "repas");

  /**
   * Renvoie la liste des creneaux de la journee
   *
   * @return array tableau d'objets creneau
   */
	public static function getAllCreneauxOrderByTime(){
		if (self::$_liste_creneaux == null) {
			$criteria = new Criteria();
			$criteria->addAscendingOrderByColumn(CreneauPeer::DEBUT_CRENEAU);
			self::$_liste_creneaux = self::doSelect($criteria);
		}
		return self::$_liste_creneaux;
	}

  /**
   * Renvoie le premier creneau de la journee
   *
   * @return array first creneau
   */
	public static function getFirstCreneau(){
		$creneaux = self::getListeCreneaux();
		if ($creneaux != null) {
			return $creneaux[0];
		} else {
			return null;
		}
	}

  /**
   * Renvoie le dernier creneau de la journee
   *
   * @return array last creneau
   */
	public static function getLastCreneau(){
		$creneaux = self::getListeCreneaux();
		$nbre = count($creneaux);
		return $creneaux[$nbre - 1];
	}

  /**
   * Purge la liste des creneaux mis en cache
   *
   * @return array last creneau
   */
	public static function clearListeCreneaux(){
		self::$_liste_creneaux = null;
	}

  /**
   * Renvoie la liste des créneaux sous la forme d'un tableau php
   *
   * @return array liste d'objet creneau
   */
  public static function getListeCreneaux(){
    if (self::$_liste_creneaux === NULL){
      self::$_liste_creneaux = self::getAllCreneauxOrderByTime();
    }
    return self::$_liste_creneaux;
  }

  /**
   * Methode qui renvoie un objet du dernier creneau du jour precedent
   *
   * @todo Donner la possibilité de définir le jour precedent
   * @return object Creneau
   */
  public static function getLastCreneauJourPrecendent(){
    return self::getLastCreneau();
  }

/**
 * Methode qui renvoie le timestamp UNIX de ce jour à minuit (00:00:00)
 *
 * @return integer Timestamp UNIX de ce jour à 00:00:00 
 */
  public static function timestampMinuit(){

    return mktime(0, 0, 0, date("m"), date("d"), date("Y"));

  }

  /**
   * Méthode qui renvoie le nombre de secondes écoulées depuis minuit (00:00:00)
   * La methode tient compte du jour du timestamp demande
   *
   * @param integer $timestamp un timestamp UNIX valide
   * @return string Nombre de secondes écoulées depuis minuit (00:00:00)
   */
  public static function nombreSecondesMinuit($timestamp){

    return $timestamp - mktime(0, 0, 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));

  }

  /**
   * Renvoie le creneau précédent de celui passé en argument
   * Si l'id du creneau dépasse 3600 (ce qui parait peu probable tout de même), on teste sur l'heure de début
   *
   * @var $creneau id du creneau ou heure de début
   * @return object CreneauPeer precedent
   */
  public static function getCreneauPrecedentCours($creneau){
    $creneau_precedent = false;
    $creneaux = self::getListeCreneaux();
    $nbre = count($creneaux);
    $i = -1;

    for($a = 0 ; $a < $nbre ; $a++){

      if (is_numeric($creneau) AND $creneau < 3600){
        // On peut rechercher par rapport à l'id
        if ($creneaux[$a]->getId() == $creneau){
          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
          $i = $a-1; // un marqueur
        }

      }elseif($creneau > 3600 AND $creneau < 172800){
        // On peut rechercher par rapport à l'heure de debut
        if ($creneaux[$a]->getDebutCreneau() <= $creneau AND $creneaux[$a]->getFinCreneau() >= $creneau){
          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
          $i = $a-1; // un marqueur
        }

      }elseif($creneau > 172800){
        // On est donc dans le cas d'un timestamp UNIX complet qu'il faut convertir avant de tester
        $test = $creneau - self::timestampMinuit();
        //echo '['.$a.'] - '.$test.' : de '.$creneaux[$a]->getDebutCreneau().' à '.$creneaux[$a]->getFinCreneau();
        if ($test >= $creneaux[$a]->getDebutCreneau() AND $test < $creneaux[$a]->getFinCreneau()){
          // Il faut vérifier que le creneau précédent existe vraiment et s'il s'agit d'un creneau de cours
          $creneau_precedent_tempo = ($a > 0) ? $creneaux[$a - 1] : NULL;
          //echo ' tempo : '.$creneau_precedent_tempo->getId().'<br />';
          $i = $a-1; // un marqueur
        }
      }
    } // boucle for

    // On vérifie le creneau precedent et on teste le premier qui correspond à un cours
    if ($creneau_precedent_tempo === NULL){
      $creneau_precedent = false;
    }else if($creneau_precedent_tempo->getTypeCreneau() == 'cours'){
      $creneau_precedent = $creneau_precedent_tempo;
    }else{
      // il faut rechercher le bon créneau précédent
      for($t = $i ; $t !== 0 ; $t--){
        if ($creneaux[$t]->getTypeCreneau() == 'cours'){
          $creneau_precedent = $creneaux[$t];
          break; // on arrête là la boucle
        }
      }
    }

    return $creneau_precedent;
  }

} // CreneauPeer