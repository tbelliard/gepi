<?php



/**
 * Skeleton subclass for representing a row from the 'utilisateurs' table.
 *
 * Utilisateur de gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class UtilisateurProfessionnel extends BaseUtilisateurProfessionnel {

	/**
	 * @var        array Classe[] Collection to store aggregation of Classe objects.
	 */
	protected $collClasses;

	/**
	 * Gets a collection of Groupe objects related by a many-to-many relationship
	 * to the current object by way of the j_groupes_professeurs cross-reference table.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this UtilisateurProfessionnel is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria Optional query object to filter the query
	 * @param      PropelPDO $con Optional connection object
	 *
	 * @return     PropelCollection|array Groupe[] List of Groupe objects
	 */
	public function getGroupes($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collGroupes || null !== $criteria) {
			if ($this->isNew() && null === $this->collGroupes) {
				// return empty collection
				$this->initGroupes();
			} else {
				$collGroupes = GroupeQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->find($con);
				if ($this->statut == "cpe") {
				    $temp_collection = GroupeQuery::create(null, $criteria)
					    ->distinct()->useJEleveGroupeQuery()
					    ->useEleveQuery()->useJEleveCpeQuery()
					    ->filterByUtilisateurProfessionnel($this)->endUse()
					    ->endUse()->endUse()
					    ->find();
				    $collGroupes->addCollection($temp_collection);
				}
				if (null !== $criteria) {
					return $collGroupes;
				}
				$this->collGroupes = $collGroupes;
			}
		}
		return $this->collGroupes;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un utilisateur. Le tableau est ordonné par les noms des classes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Classe[]
	 */
	public function getGroupesProfesseurPrincipal($con = null) {
		return GroupeHelper::orderByGroupNameWithClasses(
			GroupeQuery::create()->distinct()->useJEleveGroupeQuery()->useEleveQuery()->useJEleveProfesseurPrincipalQuery()->filterByUtilisateurProfessionnel($this)->endUse()->endUse()->endUse()->find()
			);
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		parent::clearAllReferences($deep);
		$this->collClasses = null;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eleves d'un utilisateur professeur principal.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Eleves[]
	 */
	public function getEleveProfesseurPrincipals($con = null) {
		$eleves = new PropelObjectCollection();
		foreach($this->getJEleveProfesseurPrincipalsJoinEleve() as $ref) {
		    if ($ref != null) {
			$eleves->append($ref->getEleve());
		    }
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a un prof principal
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 */
	public function addEleveProfesseurPrincipal(Eleve $eleve) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		$jEleveProfesseurPrincipal = new JEleveProfesseurPrincipal();
		$jEleveProfesseurPrincipal->setEleve($eleve);
		$this->addJEleveProfesseurPrincipal($jEleveProfesseurPrincipal);
		$jEleveProfesseurPrincipal->save();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eleves d'un utilisateur cpe
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Eleves[]
	 */
	public function getEleveCpes($con = null) {
		return $this->getEleves();
	}

	/**
	 *
	 * Ajoute un eleve a un cpe
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 */
	public function addEleveCpe(Eleve $eleve) {
		$this->addEleve($eleve);
	}

	/**
	 *
	 * Renvoi une preference d'un utilisateur
	 * Ajout manuel
	 *
	 * @param      String $name le nom de la preference à obtenir
	 * @return     String the associated value
	 */
	public function getPreferenceValeur($name){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if ($prefs->isEmpty()) {
		return NULL;
	    } else {
		return $prefs->getFirst()->getValue();
	    }
	}

	/**
	 *
	 * Enregistre une preference d'un utilisateur
	 * Ajout manuel
	 *
	 * @param      String $name le nom de la preference à obtenir
	 */
	public function setPreferenceValeur($name, $value){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if ($prefs->isEmpty()) {
		//Creation d'une nouvelle entree dans les preferences
		$nouvellePref = new PreferenceUtilisateurProfessionnel();
		$nouvellePref->setName($name);
		$nouvellePref->setValue($value);
		$nouvellePref->setLogin($this->getLogin());
		$nouvellePref->save();
		$this->addPreferenceUtilisateurProfessionnel($nouvellePref);
		$this->save();
	    } else if ($prefs->count() == 1) {
		$prefs->getFirst()->setValue($value);
		$prefs->getFirst()->save();
	    } else {
		//there's an error
		throw new PropelException("Il existe deja plusieurs preferences avec ce nom !");
	    }
	}

	/**
	 *
	 * Retourne l'emplacement de cours de l'heure temps reel. retourne null si pas pas de cours actuel
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return EdtEmplacementCours l'emplacement de cours actuel ou null si pas de cours actuellement
	 */
	public function getEdtEmplacementCours($v = 'now'){

	    $edtCoursCol = $this->getEdtEmplacementCourssPeriodeCalendrierActuelle($v);

	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    return EdtEmplacementCoursHelper::getEdtEmplacementCoursActuel($edtCoursCol, $v);
	}

	/**
	 *
	 * Retourne tous les emplacements de cours pour la periode précisée du calendrier.
	 * On recupere aussi les emplacements dont la periode n'est pas definie ou vaut 0.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  EdtCalendrierPeriode est aussi accepté
	 * 
	 * @return PropelObjectCollection EdtEmplacementCours une collection d'emplacement de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCourssPeriodeCalendrierActuelle($v = 'now'){
	    $query = EdtEmplacementCoursQuery::create()->filterByLoginProf($this->getLogin())
		    ->filterByIdCalendrier(0)
		    ->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, NULL);

	    if ($v instanceof EdtCalendrierPeriode) {
		$query->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, $v->getIdCalendrier());
	    } else {
		$periodeCalendrier = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle($v);
		if ($periodeCalendrier != null) {
		       $query->addOr(EdtEmplacementCoursPeer::ID_CALENDRIER, $periodeCalendrier->getIdCalendrier());
		}
	    }

	    $edtCoursCol = $query->find();
	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    EdtEmplacementCoursHelper::orderChronologically($edtCoursCol);

	    return $edtCoursCol;
	}

	/**
	 *
	 * Retourne la collection des absences saisies pour ce creneau. Si null, on prend le creneau actuel
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return PropelObjectCollection AbsenceEleveSaisie
	 */
	public function getEdtCreneauAbsenceSaisie($edtcreneau = null, $v = 'now'){
	    if ($edtcreneau == null) {
		$edtcreneau = EdtCreneauPeer::retrieveEdtCreneauActuel($v);
	    }

	    if (!($edtcreneau instanceof EdtCreneau)) {
		$edtcreneau = EdtCreneauQuery::create()->findPk($edtcreneau);
		if ($edtcreneau == null) {
		    return new PropelObjectCollection();
		}
	    }

	    // we treat '' and NULL as 'now' for temporal
	    if ($v === null || $v === '') {
		    $dt = new DateTime('now');
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

//	    $criteria = new Criteria();
//	    $criteria->add(AbsenceEleveSaisiePeer::ID_EDT_CRENEAU, $edtcreneau->getPrimaryKey());
	    $query = AbsenceEleveSaisieQuery::create();
	    $query->filterByUtilisateurProfessionnel($this);
	    $dt->setTime($edtcreneau->getHeuredebutDefiniePeriode('H'), $edtcreneau->getHeuredebutDefiniePeriode('i'));
	    $query->filterByFinAbs($dt, Criteria::GREATER_EQUAL);
//	    $criteria->add(AbsenceEleveSaisiePeer::DEBUT_ABS, $dt, Criteria::GREATER_EQUAL);
	    $dt_end = clone $dt;
	    $dt_end->setTime($edtcreneau->getHeurefinDefiniePeriode('H'), $edtcreneau->getHeurefinDefiniePeriode('i'));
	    $query->filterByDebutAbs($dt_end, Criteria::LESS_THAN);
//	    $criteria->add(AbsenceEleveSaisiePeer::FIN_ABS, $dt_end, Criteria::LESS_EQUAL);
	    $col = $query->find();
	    return $col;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un utilisateur. Le tableau est ordonné par les noms des classes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Groupes[]
	 */
	public function getClasses($criteria = null, $con = null) {
		if(null === $this->collClasses || null !== $criteria) {
			if ($this->isNew() && null === $this->collClasses) {
				// return empty collection
				$this->initClasses();
			} else {
				if ($this->statut == "professeur") {
				    $collClasses = ClasseQuery::create()->distinct()->orderByNomComplet()->useJGroupesClassesQuery()->useGroupeQuery()->filterByUtilisateurProfessionnel($this)->endUse()->endUse()->find();
				} else if ($this->statut == "cpe") {
				    $collClasses = ClasseQuery::create()->distinct()->orderByNomComplet()->useJEleveClasseQuery()->useEleveQuery()->useJEleveCpeQuery()->filterByUtilisateurProfessionnel($this)->endUse()->endUse()->endUse()->find();
				} else {
				    $collClasses = ClasseQuery::create()->distinct()->orderByNomComplet()->find();
				}
				if (null !== $criteria) {
					return $collClasses;
				}
				$this->collClasses = $collClasses;
			}
		}
		return $this->collClasses;
	}

	/**
	 * Initializes the collClasses collection.
	 *
	 * By default this just sets the collGroupes collection to an empty collection (like clearGroupes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initClasses()
	{
		$this->collClasses = new PropelObjectCollection();
		$this->collClasses->setModel('Classe');
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un utilisateur. Le tableau est ordonné par les noms des classes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Classe[]
	 */
	public function getClassesProfesseurPrincipal($con = null) {
		return ClasseQuery::create()->distinct()->orderByNomComplet()->useJGroupesClassesQuery()->useGroupeQuery()->filterByUtilisateurProfessionnel($this)->endUse()->endUse()->find();
	}

		/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un utilisateur professeur. Le tableau est ordonné par le noms du groupes puis les classes du groupes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Groupes[]
	 */
	public function getAidDetailss() {
	    $temp_collection = parent::getAidDetailss();
	    $pk_col = $temp_collection->getPrimaryKeys();

	    if ($this->statut == "cpe") {
		$aid_col = AidDetailsQuery::create()->distinct()
			->useJAidElevesQuery()
			->useEleveQuery()
			->useJEleveCpeQuery()
			->filterByUtilisateurProfessionnel($this)
			->endUse()->endUse()->endUse()
			->find();

		$temp_collection->addCollection($aid_col);
	    }
	    return $temp_collection;
	}


}