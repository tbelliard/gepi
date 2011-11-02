<?php



/**
 * Skeleton subclass for representing a row from the 'edt_creneaux' table.
 *
 * Table contenant les creneaux de chaque journee (M1, M2...S1, S2...)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCreneau extends BaseEdtCreneau {

	/**
	 * Les types de creneaux possibles
	 */
	const TYPE_COURS = 'cours';
	const TYPE_PAUSE = 'pause';
	const TYPE_REPAS = 'repas';
	const TYPE_VIE_SCOLAIRE = 'vie scolaire';

	public static $LISTE_LABEL_TYPE = array(
            EdtCreneau::TYPE_COURS => 'cours',
            EdtCreneau::TYPE_PAUSE => 'pause',
            EdtCreneau::TYPE_REPAS => 'repas',
            EdtCreneau::TYPE_VIE_SCOLAIRE => 'vie scolaire');

	/**
	 * Renvoi le creneau suivant du type donné
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getNextEdtCreneau($type_creneau = null) {
	    $found = false;
	    //on recupere la liste complete des creneaux
	    $all_creneau = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

	    //on parcourt la liste
	    $all_creneau->getFirst();
	    while (!$found && ($all_creneau->getCurrent() != null)) {
		if ($all_creneau->getCurrent()->getIdDefiniePeriode() == $this->getIdDefiniePeriode()) {
		    $found = true;
		} else {
		    $all_creneau->getNext();
		}
	    }

	    if (!$found) {
		return null;
	    }

	    if ($type_creneau == null) {
		return $all_creneau->getNext();
	    } else {
		$all_creneau->getNext();
		while ($all_creneau->getCurrent() != null) {
		    if ($all_creneau->getCurrent()->getTypeCreneaux() == $type_creneau) {
			return $all_creneau->getCurrent();
		    }
		    $all_creneau->getNext();
		}
	    }
	}

	/**
	 *
	 * Renvoi le creneau precedent du type donné
	 *
	 * @return     EdtCreneau EdtCreneau
	 *
	 */
	public function getPrevEdtCreneau($type_creneau = null) {
	    $found = false;
	    //on recupere la liste complete des creneaux
	    $all_creneau = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

	    //on parcourt la liste
	    $all_creneau->getFirst();
	    while (!$found && ($all_creneau->getCurrent() != null)) {
		if ($all_creneau->getCurrent()->getIdDefiniePeriode() == $this->getIdDefiniePeriode()) {
		    $found = true;
		} else {
		    $all_creneau->getNext();
		}
	    }

	    if (!$found) {
		return null;
	    }

	    if ($type_creneau == null) {
		return $all_creneau->getPrevious();
	    } else {
		$all_creneau->getNext();
		while ($all_creneau->getCurrent() != null) {
		    if ($all_creneau->getCurrent()->getTypeCreneaux() == $type_creneau) {
			return $all_creneau->getCurrent();
		    }
		    $all_creneau->getPrevious();
		}
	    }
	}

	/**
	 *
	 * Renvoi une description intelligible du creneau
	 *
	 * @return     String description
	 *
	 */
	public function getDescription() {
	    $desc = '';
	    $desc .= $this->getNomDefiniePeriode() . " ";
	    $desc .= $this->getHeuredebutDefiniePeriode("H:i") . " - ";
	    $desc .= $this->getHeurefinDefiniePeriode("H:i");
	    return $desc;
	}
} // EdtCreneau
