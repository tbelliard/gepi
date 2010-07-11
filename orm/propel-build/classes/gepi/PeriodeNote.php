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
	 *
	 * Retourne la date de debut de periode
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 *
	 * @return DateTime $date ou null si non précisé
	 */
	public function getDateDebut($format = '%X') {
	    if ($this->getNumPeriode() == 1) {
		//on essaye de récupérer la date de début dans le calendrier des périodes
		$edt_periode = EdtCalendrierPeriodeQuery::create()->filterByNumeroPeriode($this->getNumPeriode())->orderByDebutCalendrierTs()->findOne();
		if ($edt_periode != null) {
		    return $edt_periode->getJourdebutCalendrier($format);
		} else {
		    //on va renvoyer par default le 31 aout
		    $dt = new DateTime('now');
		    $dt->setDate($dt->format('Y'), 8, 31);
		    $dt->setTime(0,0,0);
		    $now = new DateTime('now');
		    if ($dt->format('U') - $now->format('U') > 3600*24*30) {
			//si la date est trop postérieure à maintenant c'est qu'on s'est trompé d'année
			$dt->setDate($dt->format('Y') - 1, 8, 31);
		    }
		    
		    if ($format === null) {
			    // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			    return $dt;
		    } elseif (strpos($format, '%') !== false) {
			    return strftime($format, $dt->format('U'));
		    } else {
			    return $dt->format($format);
		    }
		}
	    } else {
		//on renvoi la date de fin de la periode precedente
		$periode_prec = PeriodeNoteQuery::create()->filterByIdClasse($this->getIdClasse())->filterByNumPeriode($this->getNumPeriode() - 1)->findOne();
		if ($periode_prec === null) {
		    return null;
		}else {
		    return $periode_prec->getDateFin($format);
		}
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

} // PeriodeNote
