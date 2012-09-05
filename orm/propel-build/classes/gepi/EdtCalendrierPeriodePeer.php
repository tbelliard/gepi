<?php



/**
 * Skeleton subclass for performing query and update operations on the 'edt_calendrier' table.
 *
 * Liste des periodes datees de l'annee courante(pour definir par exemple les trimestres)
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtCalendrierPeriodePeer extends BaseEdtCalendrierPeriodePeer {

  private static $_all_periodes;

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return     EdtCreneau
     */
    public static function retrieveByPK($pk, PropelPDO $con = null) {
	foreach (EdtCalendrierPeriodePeer::retrieveAllEdtCalendrierPeriodesOrderByTime() as $edtPeriode) {
	    if ((string)$edtPeriode->getPrimaryKey() === (string)$pk) {
		return $edtPeriode;
	    }
	}
	return null;
    }

  /**
   * Renvoie la liste des creneaux de la journee
   *
   * @return PropelObjectCollection EdtCreneau
   */
    public static function retrieveAllEdtCalendrierPeriodesOrderByTime(){
	    if (self::$_all_periodes == null) {
		self::$_all_periodes = EdtCalendrierPeriodeQuery::create()->addAscendingOrderByColumn(EdtCalendrierPeriodePeer::JOURDEBUT_CALENDRIER)->find();
	    }
	    return clone self::$_all_periodes;
    }

 	/**
	 * Retrourne la periode actuelle, ou null si aucune periode n'est trouve pour le jours actuel
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtCalendrierPeriode la periode actuelle
	 */
	public static function retrieveEdtCalendrierPeriodeActuelle($v = 'now') {
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
        $edt_periode_actuelle=Null;
        $intervalle_periode=Null;
		foreach (EdtCalendrierPeriodePeer::retrieveAllEdtCalendrierPeriodesOrderByTime() as $edtPeriode) {
          if ($edtPeriode->getJourdebutCalendrier('Y-m-d') <= $dt->format('Y-m-d')
			    &&	$edtPeriode->getJourfinCalendrier('Y-m-d') >= $dt->format('Y-m-d')) {
			   if (is_null($edt_periode_actuelle)){ //c'est la première periode rencontrée qui correspond
                 $edt_periode_actuelle=$edtPeriode;
                 $intervalle_periode=$edtPeriode->getFinCalendrierTs()-$edtPeriode->getDebutCalendrierTs();  
               }else{
          //si une periode plus courte correspond on prend celle là
          if ($edtPeriode->getFinCalendrierTs()-$edtPeriode->getDebutCalendrierTs()<=$intervalle_periode) {
            $edt_periode_actuelle = $edtPeriode;
            $intervalle_periode=$edtPeriode->getFinCalendrierTs()-$edtPeriode->getDebutCalendrierTs();
          }
        }
      }
    }
    return $edt_periode_actuelle;
		//return null;
//		return EdtCalendrierPeriodeQuery::create()
//			->filterByJourdebutCalendrier($dt, Criteria::LESS_EQUAL)
//			//->filterByHeuredebutCalendrier($dt, Criteria::GREATER_EQUAL)
//			->filterByJourfinCalendrier($dt, Criteria::GREATER_EQUAL)
//			//->filterByHeurefinCalendrier($dt, Criteria::LESS_EQUAL)
//			->findOne();
	}


} // EdtCalendrierPeriodePeer
