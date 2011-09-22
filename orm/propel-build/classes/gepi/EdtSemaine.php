<?php



/**
 * Skeleton subclass for representing a row from the 'edt_semaines' table.
 *
 * Liste des semaines de l'annee scolaire courante - 53 enregistrements obligatoires (pas 52!), pour lesquel on assigne un type (A ou B par exemple)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtSemaine extends BaseEdtSemaine {

 	/**
	 *
	 * Renvoi la date du lundi de la semaine
	 * on se base sur l'annee de la date courante en counsiderant que la trentieme semaine separe une annee scolaire de la suivante
	 *
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL)
	 */
	public function getLundi($format = 'Y-m-d H:i:s') {
	    $now = new DateTime('now');
	    $year = $now->format('Y');
	    $num_semaine_now = $now->format('W');
	    if ($this->getNumEdtSemaine() === null || !is_numeric($this->getNumEdtSemaine())) {
		throw new PropelException('Numero de semaine non valide');
	    }
	    if ($num_semaine_now > 30 && $this->getNumEdtSemaine() > 30) {
		//on est dans la meme annee
	    } else if ($num_semaine_now < 30 && $this->getNumEdtSemaine() < 30) {
		//on est dans la meme annee
	    } else if ($num_semaine_now > 30 && $this->getNumEdtSemaine() < 30) {
		//on est dans l'anne suivante
		$year = $year+1;
	    } else if ($num_semaine_now < 30 && $this->getNumEdtSemaine() > 30) {
		//on est dans l'anne precedente
		$year = $year-1;
	    }
	    $dt = new DateTime();
	    $dt->setISODate($year, $this->getNumEdtSemaine(), 1);
	    if ($format === null) {
		    // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
		    return $dt;
	    } elseif (strpos($format, '%') !== false) {
		    return strftime($format, $dt->format('U'));
	    } else {
		    return $dt->format($format);
	    }
	}

 	/**
	 *
	 * Renvoi la date du vendredi de la semaine
	 *
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL)
	 */
	public function getSamedi($format = 'Y-m-d H:i:s') {
	    $now = new DateTime('now');
	    $year = $now->format('Y');
	    $num_semaine_now = $now->format('W');
	    if ($this->getNumEdtSemaine() === null || !is_numeric($this->getNumEdtSemaine())) {
		throw new PropelException('Numero de semaine non valide');
	    }
	    if ($num_semaine_now > 30 && $this->getNumEdtSemaine() > 30) {
		//on est dans la meme annee
	    } else if ($num_semaine_now < 30 && $this->getNumEdtSemaine() < 30) {
		//on est dans la meme annee
	    } else if ($num_semaine_now > 30 && $this->getNumEdtSemaine() < 30) {
		//on est dans l'anne suivante
		$year = $year+1;
	    } else if ($num_semaine_now < 30 && $this->getNumEdtSemaine() > 30) {
		//on est dans l'anne precedente
		$year = $year-1;
	    }
	    $dt = new DateTime();
	    $dt->setISODate($year, $this->getNumEdtSemaine(), 6);
	    if ($format === null) {
		    // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
		    return $dt;
	    } elseif (strpos($format, '%') !== false) {
		    return strftime($format, $dt->format('U'));
	    } else {
		    return $dt->format($format);
	    }
	}

} // EdtSemaine
