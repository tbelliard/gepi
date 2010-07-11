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
	public function getHeureDebut($format = '%X') {
		if ($this->getEdtCreneau() == NULL) {
		    throw new PropelException("Il n'y a pas de creneau associé a ce cours.");
		}
	
		if ($this->getHeuredebDec() == "0.5") {		    
		    $dt = $this->getEdtCreneau()->getHeuredebutDefiniePeriode(NULL);
		    $start = $dt->format('U'); //le formattage avec U nous donne un timestamp en secondes
		    $addStart = $this->getEdtCreneau()->getHeurefinDefiniePeriode('U');
		    $addSecond = ($addStart - $start) / 2;
		    $dt->modify("+$addSecond second");
		    if ($format === null) {
			    return $dt;
		    } elseif (strpos($format, '%') !== false) {
			    return strftime($format, $dt->format('U'));
		    } else {
			    return $dt->format($format);
		    }
		} else {
		    return $this->getEdtCreneau()->getHeuredebutDefiniePeriode($format);
		}
	}

	/**
	 *
	 * Renvoi l'heure de fin du cours
	 *
	 * @return     DateTime
	 *
	 */
	public function getHeureFin($format = '%X') {
		if ($this->getEdtCreneau() == NULL) {
		    throw new PropelException("Il n'y a pas de creneau associé a ce cours.");
		}

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
		    return $lastCreneau->getHeurefinDefiniePeriode($format);
		}
		if (($this->getDuree() % 2) == 0) {
		    //il faut prendre la fin du creneau precedent
		    return $lastCreneau->getHeurefinDefiniePeriode($format);
		} else {
		    //on prend le milieu du creneau en cours
		    $dt = $creneau->getHeuredebutDefiniePeriode(NULL);
		    $start = $dt->format('U'); //le formattage avec U nous donne un timestamp en secondes
		    $addStart = $creneau->getHeurefinDefiniePeriode('U');
		    $addSecond = ($addStart - $start) / 2;
		    $dt->modify("+$addSecond second");
		    if ($format === null) {
			    return $dt;
		    } elseif (strpos($format, '%') !== false) {
			    return strftime($format, $dt->format('U'));
		    } else {
			    return $dt->format($format);
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
	    if ($this->getGroupe() != null) {
		$desc .= $this->getGroupe()->getNameAvecClasses() . " ";
	    }
	    if ($this->getAidDetails() != null) {
		$desc .= $this->getAidDetails()->getNom() . " ";
	    }
	    $desc .= $this->getJourSemaine() . " ";
	    $desc .= $this->getHeureDebut("H:i") . " - ";
	    $desc .= $this->getHeureFin("H:i") . " ";
	    if ($this->getTypeSemaine() != NULL && $this->getTypeSemaine() != '' && $this->getTypeSemaine() != '0') {
		$desc .= " sem. ".$this->getTypeSemaine(). " ";
	    }
//	    if ($this->getEdtSalle() != null) {
//		$desc .= "salle ". $this->getEdtSalle()->getNomSalle();
//		if ($this->getEdtSalle()->getNumeroSalle() != null) {
//		    $desc .= $this->getEdtSalle()->getNumeroSalle();
//		}
//	    }
	    return $desc;
	}

	/**
	 *
	 * Renvoi le jour de la semaine du cours sous forme d'un entier,
	 * le jour 0 etant le dimanche, le retour est -1 si il y a un mauvais enregistrement dans la base
	 *
	 * @return     integer
	 *
	 */
	public function getJourSemaineNumeric() {
	    if ($this->getJourSemaine() == "dimanche") {
		return 0;
	    } else if ($this->getJourSemaine() == "lundi") {
		return 1;
	    } else if ($this->getJourSemaine() == "mardi") {
		return 2;
	    } else if ($this->getJourSemaine() == "mercredi") {
		return 3;
	    } else if ($this->getJourSemaine() == "jeudi") {
		return 4;
	    } else if ($this->getJourSemaine() == "vendredi") {
		return 5;
	    } else if ($this->getJourSemaine() == "samedi") {
		return 6;
	    }
	    return -1;
	}



	/**
	 *
	 * Renvoi la date du cours d'apres la semaine precisee
	 * prise en compte de l'annee courante avec comme pivot la semaine 30
	 *
	 * @param     integer $id_semaine le numero de la semaine. Par defaut la semaine courante
	 *
	 * @return     DateTime
	 *
	 */
	public function getDate($id_semaine = null) {
	    //on va utiliser le numero de semaine precisée pour regler la date
	    if ($id_semaine == null || $id_semaine == -1) {
		$id_semaine = date('W');
	    }
	    $day_of_the_week = $this->getJourSemaineNumeric();
	    $week_of_the_year = $id_semaine;
	    $current_week_of_the_year = date('W');
	    $year = date('Y');
	    //if faut peut etre decaler l'année
	    if ($current_week_of_the_year > 30 && $week_of_the_year > 30) {
		//ne rien faire on garde la meme annee
	    } else if ($current_week_of_the_year < 30 && $week_of_the_year < 30) {
		//ne rien faire on garde la meme annee
	    } else if ($current_week_of_the_year > 30 && $week_of_the_year < 30) {
		//on augmente d'un an
		$year = $year + 1;
	    } else if ($current_week_of_the_year < 30 && $week_of_the_year > 30) {
		//on reduit  d'un an
		$year = $year - 1;
	    }
	    if (strlen($week_of_the_year) == 1) {
		$week_of_the_year = '0'.$week_of_the_year;
	    }
	    return new DateTime($year.'-W'.$week_of_the_year.'-'.$day_of_the_week);

	}
} // EdtEmplacementCours
