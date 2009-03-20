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
   * Renvoie le creneau précédent de celui passé en argument
   * Si l'id du creneau dépasse 3600 (ce qui parait peu probable tout de même), on teste sur l'heure de début
   *
   * @var $creneau id du creneau ou heure de début
   * @return object CreneauPeer precedent
   */
  public static function getCreneauPrecedent($creneau){
    $creneaux = self::getListeCreneaux();
    $nbre = count($creneaux);
    if (is_numeric($creneau) AND $creneau < 3600){
      // On peut rechercher par rapport à l'id
      for($a = 0 ; $a < $nbre ; $a++){
        if ($creneaux[$a]->getId() == $creneau){
          return($creneaux[$a - 1]);
        }
      }
    }elseif($creneau > 3600){
      // On peut rechercher par rapport à l'heure de debut
      for($a = 0 ; $a < $nbre ; $a++){
        if ($creneaux[$a]->getDebutCreneau() <= $creneau AND $creneaux[$a]->getFinCreneau() >= $creneau){
          return($creneaux[$a - 1]);
        }
      }
    }
  }

} // CreneauPeer