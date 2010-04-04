<?php


/**
 * Skeleton subclass for representing a row from the 'edt_cours' table.
 *
 * Liste de tous les creneaux de tous les emplois du temps
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtEmplacementCours extends BaseEdtEmplacementCours {

	/**
	 *
	 * Renvoi l'heure de debut du cours
	 *
	 * @return     DateTime
	 *
	 */
	public function getHeureDebut() {
		$start = strtotime($this->getEdtCreneau()->getHeuredebutDefiniePeriode());
		if ($this->getHeuredebDec() == "0.5") {
		    $addStart = strtotime($this->getEdtCreneau()->getHeurefinDefiniePeriode());
		    $start = ($start + $addStart) / 2;
		}
		return $datetime = date("H:i:s", $start);
	}

	/**
	 *
	 * Renvoi l'heure de fin du cours
	 *
	 * @return     DateTime
	 *
	 */
	public function getHeureFin() {
		$creneau = $this->getEdtCreneau();
		$lastCreneau = new EdtCreneau();
		$duree_modif = $this->getDuree();
		if ($this->getHeuredebDec() == "0.5") {
		    $duree_modif++;
		}
		for ($i = 1; $i <= ($duree_modif / 2); $i ++) {
		    if ($creneau != null) {
			$lastCreneau = $creneau;
			$creneau = $creneau->getNextEdtCreneau("cours");
		    }
		}
		if ($creneau == null) {
		    // on est arrivé au bout, on va renvoye l'heure de fin du dernier creneau
		    return $lastCreneau->getHeurefinDefiniePeriode();
		}
		if (($this->getDuree() % 2) == 0) {
		    //il faut prendre la fin du creneau precedent
		    return $lastCreneau->getHeurefinDefiniePeriode();
		} else {
		    //on prend le milieu du creneau en cours
		    $end = strtotime($creneau->getHeuredebutDefiniePeriode());
		    $addEnd = strtotime($creneau->getHeurefinDefiniePeriode());
		    $end = ($end + $addEnd) / 2;
		    return $datetime = date("H:i:s", $end);
		}
	}

} // EdtEmplacementCours
