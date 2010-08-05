<?php



/**
 * Skeleton subclass for representing a row from the 'periodes' table.
 *
 * Table regroupant les periodes de notes pour les classes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class PeriodeNote extends BasePeriodeNote {

	/**
	 * @var        array PeriodesNote[] Collection to store aggregation of PeriodesNote objects.
	 */
	protected $dateDebut;

  	/**
	 *
	 * Retourne la date de debut de periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 *
	 * @return DateTime $date ou null si non précisé
	 */
	public function getDateDebut($format = null) {
	    if(null === $this->dateDebut) {
		    if ($this->isNew()) {
			    //do nothing
		    } else {
			    $dateDebut = null;
			    if ($this->getNumPeriode() == 1) {
				//on essaye de récupérer la date de début dans le calendrier des périodes
				$edt_periode = EdtCalendrierPeriodeQuery::create()->filterByNumeroPeriode($this->getNumPeriode())->orderByDebutCalendrierTs()->findOne();
				if ($edt_periode != null) {
				    $dateDebut = $edt_periode->getJourdebutCalendrier(null);
				} else {
				    //c'est la premiere periode
				    //on va renvoyer par default le 31 aout
				    $dateDebut = new DateTime('now');
				    $dateDebut->setDate($dateDebut->format('Y'), 8, 31);
				    $dateDebut->setTime(0,0,0);
				    $now = new DateTime('now');
				    if ($dateDebut->format('U') - $now->format('U') > 3600*24*30) {
					//si la date est trop postérieure à maintenant c'est qu'on s'est trompé d'année
					$dateDebut->setDate($dateDebut->format('Y') - 1, 8, 31);
				    }
				}
			    } else {
				//on renvoi la date de fin de la periode precedente
				$periode_prec = PeriodeNoteQuery::create()->filterByIdClasse($this->getIdClasse())->filterByNumPeriode($this->getNumPeriode() - 1)->findOne();
				if ($periode_prec != null) {
				    $dateDebut = $periode_prec->getDateFin(null);
				}
				//on prend le lendemain
				if ($dateDebut !== null) {
				    $dateDebut->modify("+24 hours");
				}
			    }
			    if ($dateDebut !== null) {
				//on commence la periode a 00:00
				$dateDebut->setTime(0,0,0);
			    }
			    $this->dateDebut = $dateDebut;
		    }
	    }
	    if ($this->dateDebut === null) {
		    //on initialise a un timestamp de 0  pour ne pas faire de nouveau la recherche
		    $this->dateDebut == new DateTime('@0');
		    return null;
	    } else if ($this->dateDebut->format('U') == 0) {
		    return null;
	    }

	    if ($format === null) {
		    //we return a DateTime object.
		    return $this->dateDebut;
	    } elseif (strpos($format, '%') !== false) {
		    return strftime($format, $this->dateDebut->format('U'));
	    } else {
		    return $this->dateDebut->format($format);
	    }
	}




 	/**
	 * Compare deux periodeNote par leur numéros
	 *
	 * @param      PeriodeNote $groupeA Le premier PeriodeNote a comparer
	 * @param      PeriodeNote $groupeB Le deuxieme PeriodeNote a comparer
	 * @return     int un entier, qui sera inférieur, égal ou supérieur à zéro suivant que le premier argument est considéré comme plus petit, égal ou plus grand que le second argument.
	 */
	public static function comparePeriodeNote($a, $b) {
		if ($a ==null || $b == null){
		    throw new PropelException("Objet null pour la comparaison.");
		}

		return $a->getNumPeriode() - $b->getNumPeriode();
	}

	/**
	 * Get the [optionally formatted] temporal [date_fin] column value.
	 * date de verrouillage de la periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateFin($format = 'Y-m-d H:i:s') {
	    //on fini la periode a 23:59
	    $date_fin = parent::getDateFin(null);
	    if ($date_fin == null) {
		return null;
	    } else {
		$date_fin->setTime(23,59,59);
		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $date_fin;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $date_fin->format('U'));
		} else {
			return $date_fin->format($format);
		}
	    }
	}
} // PeriodeNote
