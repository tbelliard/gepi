<?php

require 'gepi/om/BaseCreneau.php';


/**
 * Skeleton subclass for representing a row from the 'a_creneaux' table.
 *
 * Les creneaux sont la base du temps des eleves et des cours
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Creneau extends BaseCreneau {

	/**
	 * Initializes internal state of Creneau object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

  /**
   * Méthode qui renvoie un horaire de debut de creneau formatte hh:mm
   * 
   * @return string hh:mm
   */
	public function getDebutHeureFr() {
		return $this->heureFr(parent::getDebutCreneau());
	}

  /**
   * Méthode qui renvoie un horaire de debut de creneau formatte hh:mm
   *
   * @return string hh:mm
   */
	public function getFinHeureFr() {
		return $this->heureFr(parent::getFinCreneau());
	}

  /**
   * Méthode qui attribue un horaire de debut de creneau au format hh:mm
   *
   * @param string $var Nombre de secondes
   * @return string hh:mm
   */
	public function setDebutHeureFr($var) {
		return parent::setDebutCreneau($this->heureBdd($var));
	}

  /**
   * Méthode qui attribue un horaire de debut de creneau au format hh:mm
   *
   * @param string $var Nombre de secondes
   * @return string hh:mm
   */
	public function setFinHeureFr($var) {
		return parent::setFinCreneau($this->heureBdd($var));
	}

  /**
   * Méthode qui transforme les secondes de l'horaire en heure fançaise hh:mm
   *
   * @access private
   * @param string $var Nombre de secondes
   * @return string hh:mm
   */
	private static function heureFr($var){
		$heures = floor($var / 3600);
		$reste = $var % 3600;
		$minutes = floor($reste / 60);
		$minutes = $minutes < 10 ? '0' . $minutes : $minutes;

		return $heures . ':' . $minutes;
	}

  /**
   * Méthode qui renvoit un horaire de la forme 10:00 sous un nombre de seconde écoulées depuis 00:00
   *
   * @param string $var hh:mm
   * @return numeric
   */
	private static function heureBdd($var){
		if (! CreneauHelper::isHoraire($var)){
			throw new PropelException("Mauvais formattage de l'heure");
		}
		$test = explode(":", $var);
		return (($test[0] * 3600) + ($test[1]* 60));
	}
} // Creneau
