<?php
/**
 * Description of EdtEmplacementCoursHelper
 *  Classe qui implemente des methodes statiques pour géré un groupe ou un tableau de groupe
 *
 * @author joss
 */
class EdtEmplacementCoursHelper {

 	/**
	 * Compare deux edtEmplacementCours par ordre chronologique
	 *
	 * @param      EdtEmplacementCours $groupeA Le premier EdtEmplacementCours a coparer
	 * @param      EdtEmplacementCours $groupeB Le deuxieme EdtEmplacementCours a comparer
	 * @return     int un entier, qui sera inférieur, égal ou supérieur à zéro suivant que le premier argument est considéré comme plus petit, égal ou plus grand que le second argument.
	 */
	public static function compareEdtEmplacementCours($a, $b) {
		if ($a ==null || $b == null){
		    throw new PropelException("Objet null pour la comparaison.");
		}
		
		// On traduit le nom du jour
		$semaine_declaration["dimanche"] = 1;
		$semaine_declaration["lundi"] = 2;
		$semaine_declaration["mardi"] = 3;
		$semaine_declaration["mercredi"] = 4;
		$semaine_declaration["jeudi"] = 5;
		$semaine_declaration["vendredi"] = 6;
		$semaine_declaration["samedi"] = 7;


		if ($a->getJourSemaine() != $b->getJourSemaine()
			&& isset($semaine_declaration[$a->getJourSemaine()])
			&& isset($semaine_declaration[$a->getJourSemaine()])
			&& isset($semaine_declaration[$a->getJourSemaine()]) && isset($semaine_declaration[$b->getJourSemaine()]) 
			&& $semaine_declaration[$a->getJourSemaine()] != $semaine_declaration[$b->getJourSemaine()]) {
		    $result = ($semaine_declaration[$a->getJourSemaine()] - $semaine_declaration[$b->getJourSemaine()]);
		} elseif ($a->getEdtCreneau() == null && $b->getEdtCreneau() != null)  {
			//si aucun creneau n'est precise on considere que le creneau null est plus petit
		    $result = -1;
		} elseif ($a->getEdtCreneau() == null && $b->getEdtCreneau() == null)  {
		    $result = 0;
		} elseif ($a->getEdtCreneau() != null && $b->getEdtCreneau() == null)  {
		    $result = 1;
		} elseif ($a->getEdtCreneau()->getIdDefiniePeriode() != $b->getEdtCreneau()->getIdDefiniePeriode())  {
		    $start = strtotime($a->getEdtCreneau()->getHeuredebutDefiniePeriode());
		    $end = strtotime($b->getEdtCreneau()->getHeuredebutDefiniePeriode());
		    $result = ($start-$end);
		} elseif ($a->getHeuredebDec() != $b->getHeuredebDec())  {
		    $result = ($a->getHeuredebDec() - $b->getHeuredebDec());
		} elseif ($a->getTypeSemaine() != $b->getTypeSemaine())  {
		    $result = strcmp($a->getTypeSemaine(), $b->getTypeSemaine());
		} else  {
		    $result = ($a->getDuree() - $b->getDuree());
		}
		return $result;
	}

 	/**
	 *
	 * Classe un tableau de groupe par ordre alphabétique de leur nom (avec les noms de classes d'eleves associée)
	 *
	 * @param      PropelObjectCollection $edtEmplacementCours La collection d'emplacement de cours
	 * @return     PropelObjectCollection $edtEmplacementCours Un collection ordonnés d'emplacement de cours
	 * @throws     PropelException - si les types d'entrées ne sont pas bon.
	 */
	public static function orderChronologically(PropelObjectCollection $edtEmplacementCours) {
		$edtEmplacementCours->uasort(array("EdtEmplacementCoursHelper", "compareEdtEmplacementCours"));
		return $edtEmplacementCours;
	}

 	/**
	 *
	 * Renvoi le premiers cours de la collection qui correspond à l'heure donnée
	 *
	 * @param      PropelObjectCollection $edtEmplacementCours La collection d'emplacement de cours
	 * @return     PropelObjectCollection $edtEmplacementCours Un collection ordonnés d'emplacement de cours
	 * @throws     PropelException - si les types d'entrées ne sont pas bon.
	 */
	public static function getEdtEmplacementCoursActuel(PropelObjectCollection $edtEmplacementCoursCol, $v = 'now') {
	    // we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
	    // -- which is unexpected, to say the least.
	    //$dt = new DateTime();
	    if ($v === null || $v === '') {
		    $dt = null;
	    } elseif ($v instanceof DateTime) {
		    $dt = clone $v;
	    } else {
		    // some string/numeric value passed; we normalize that so that we can
		    // validate it.
		    try {
			    if (is_numeric($v)) { // if it's a unix timestamp
				    $dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
				    // We have to explicitly specify and then change the time zone because of a
				    // DateTime bug: http://bugs.php.net/bug.php?id=43003
				    $dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			    } else {
				    $dt = new DateTime($v);
			    }
		    } catch (Exception $x) {
			    throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
		    }
	    }
	    $num_semaine = $dt->format('W');
	    $edtSemaine = EdtSemaineQuery::create()->filterByNumEdtSemaine($num_semaine)->findOne();
	    if ($edtSemaine == null) {
		$type_semaine = '';
	    } else {
		$type_semaine = $edtSemaine->getTypeEdtSemaine();
	    }

	    // On traduit le nom du jour
	    $semaine_declaration = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	    $jour_semaine = $semaine_declaration[$dt->format("w")];

	    $timeStampNow = strtotime($dt->format('H:i:s'));
	    foreach ($edtEmplacementCoursCol as $edtCours) {
		if ($jour_semaine == $edtCours->getJourSemaine() &&
		    ($type_semaine == $edtCours->getTypeSemaine()
			|| $edtCours->getTypeSemaine() == ''
			|| $edtCours->getTypeSemaine() == '0'
			|| $edtCours->getTypeSemaine() === 0
			)) {
		    if ($edtCours->getEdtCreneau() != null && strtotime($edtCours->getHeureDebut()) <= $timeStampNow &&
			$timeStampNow < strtotime($edtCours->getHeureFin())) {
			return $edtCours;
		    }
		}
	    }

	    return null;
	}

	 	/**
	 *
	 * Renvoi la collection de cours qui correspond à l'heure donnée
	 *
	 * @param      PropelObjectCollection $edtEmplacementCours La collection d'emplacement de cours
	 * @return     PropelObjectCollection $edtEmplacementCours Un collection ordonnés d'emplacement de cours
	 * @throws     PropelException - si les types d'entrées ne sont pas bon.
	 */
	public static function getColEdtEmplacementCoursActuel(PropelObjectCollection $edtEmplacementCoursCol, $v = 'now') {
	    // we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
	    // -- which is unexpected, to say the least.
	    //$dt = new DateTime();
	    if ($v === null || $v === '') {
		    $dt = null;
	    } elseif ($v instanceof DateTime) {
		    $dt = clone $v;
	    } else {
		    // some string/numeric value passed; we normalize that so that we can
		    // validate it.
		    try {
			    if (is_numeric($v)) { // if it's a unix timestamp
				    $dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
				    // We have to explicitly specify and then change the time zone because of a
				    // DateTime bug: http://bugs.php.net/bug.php?id=43003
				    $dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			    } else {
				    $dt = new DateTime($v);
			    }
		    } catch (Exception $x) {
			    throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
		    }
	    }
	    
	    $result = new PropelObjectCollection();
	    
	    $num_semaine = $dt->format('W');
	    $edtSemaine = EdtSemaineQuery::create()->filterByNumEdtSemaine($num_semaine)->findOne();
	    if ($edtSemaine == null) {
		$type_semaine = '';
	    } else {
		$type_semaine = $edtSemaine->getTypeEdtSemaine();
	    }

	    // On traduit le nom du jour
	    $semaine_declaration = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	    $jour_semaine = $semaine_declaration[$dt->format("w")];

	    $timeStampNow = strtotime($dt->format('H:i:s'));
	    foreach ($edtEmplacementCoursCol as $edtCours) {
		if ($jour_semaine == $edtCours->getJourSemaine() &&
		    ($type_semaine == $edtCours->getTypeSemaine()
			|| $edtCours->getTypeSemaine() == ''
			|| $edtCours->getTypeSemaine() == '0'
			|| $edtCours->getTypeSemaine() === 0
			)) {
		    if ($edtCours->getEdtCreneau() != null && strtotime($edtCours->getHeureDebut()) <= $timeStampNow &&
			$timeStampNow < strtotime($edtCours->getHeureFin())) {
			$result->add($edtCours);
		    }
		}
	    }

	    return $result;
	}
	
}?>