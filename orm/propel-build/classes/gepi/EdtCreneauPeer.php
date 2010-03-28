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
   * @return PropelObjectCollection EdtCreneau
   */
    public static function getAllEdtCreneauxOrderByTime(){
	    $criteria = new Criteria();
	    $criteria->addAscendingOrderByColumn(EdtCreneauPeer::HEUREDEBUT_DEFINIE_PERIODE);
	    return self::doSelect($criteria);
    }

	/**
	 *
	 * Renvoi le creneau actuel
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getEdtCreneauActuel() {
		throw new PropelException("Pas encore implemente");
		return new EdtCreneau();
	}

	/**
	 *
	 * Renvoi le premier creneau de la semaine
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getFirstEdtCreneau() {
		throw new PropelException("Pas encore implemente");
		return new EdtCreneau();
	}

} // EdtCreneauPeer
