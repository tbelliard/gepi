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
	 * @var        array Eleve[] Collection to store aggregation of Eleve objects.
	 */
	protected $collEleves;

	/**
	 * @var        array Eleve[] Collection to store the list of autorized access to fiche eleve.
	 */
	protected $collAccesFicheEleves;

	/**
	 * Clears out the collEleves collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addEleves()
	 */
	public function clearEleves()
	{
		$this->collEleves = null; // important to set this to NULL since that means it is uninitialized
		$this->collAccesFicheEleves = null;
	}

	/**
	 * Initializes the collEleves collection.
	 *
	 * By default this just sets the collEleves collection to an empty collection (like clearEleves());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initEleves()
	{
		$this->collEleves = new PropelObjectCollection();
		$this->collEleves->setModel('Eleve');
		$this->collAccesFicheEleves = Array();
	}

	/**
	 * Retourne les eleves dont l'utilisateur a la responsabilite
	 * en tant que cpe, professeur principal ou compte scolarite
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
	 * @return     PropelCollection|array Eleve[] List of Eleve objects
	 */
	public function getEleves($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collEleves || null !== $criteria) {
			if ($this->isNew() && null === $this->collEleves) {
				// return empty collection
				$this->initEleves();
			} else {
				$collEleves = EleveQuery::create(null, $criteria)
					    ->filterByUtilisateurProfessionnel($this)
					    ->find($con);
				if (null !== $criteria) {
					return $collEleves;
				}
				$this->collEleves = $collEleves;
			}
		}
		return $this->collEleves;
	}

	/**
	 * Ajoute un eleve au cpe ou au professeur principal
	 * le statut doit etre cpe ou professeur
	 *
	 * @param      Eleve $eleve The JEleveCpe object to relate
	 * @return     void
	 */
	public function addEleve($eleve)
	{
		if ($this->statut != "cpe" && $this->statut != "professeur") {
		    throw new PropelException("le statut de l'utilisateur doit etre cpe ou professeur");
		}
		if ($this->collEleves === null) {
		    $this->initEleves();
		}

		if (!$this->collEleves->contains($eleve)) { // only add it if the **same** object is not already associated
		    if ($this->statut == "cpe") {
			$jEleveCpe = new JEleveCpe();
			$jEleveCpe->setEleve($eleve);
			$this->addJEleveCpe($jEleveCpe);

			$this->collEleves[]= $eleve;
		    } else if ($this->statut == "professeur") {
			$jEleveProfesseurPrincipal = new JEleveProfesseurPrincipal();
			$jEleveProfesseurPrincipal->setEleve($eleve);
			$this->addJEleveProfesseurPrincipal($jEleveProfesseurPrincipal);

			$this->collEleves[]= $eleve;

		    }
		}
	}

	/**
	 * Retourne true ou false selon que l'utilisateur a acces a la fiche de cette eleve
	 *
	 * @param      Eleve $eleve
	 *
	 * @return     Boolean
	 */
	public function getAccesFicheEleve(Eleve $eleve) {
	    if ($eleve === null) return false;
	    if ($this->getStatut() == "admin") {
		return true;
	    } else if ($this->getStatut() == "secours") {
		return true;
	    } else if ($this->getStatut() == "scolarite") {
		if (getSettingValue("GepiAccesTouteFicheEleveScolarite")=='yes') {
		    return true;
		} else {
		    if (!isset($this->collAccesFicheEleves[$eleve->getPrimaryKey()])) {
			$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = $this->getEleves()->contains($eleve);
		    }
		    return $this->collAccesFicheEleves[$eleve->getPrimaryKey()];
		}
	    } else if ($this->getStatut() == "cpe") {
		if (getSettingValue("GepiAccesTouteFicheEleveCpe")=='yes') {
		    return true;
		} else {
		    if (!isset($this->collAccesFicheEleves[$eleve->getPrimaryKey()])) {
			$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = $this->getEleves()->contains($eleve);
		    }
		    return $this->collAccesFicheEleves[$eleve->getPrimaryKey()];
		}
	    } else if ($this->getStatut() == "professeur") {
		if (isset($this->collAccesFicheEleves[$eleve->getPrimaryKey()])) {
		    return $this->collAccesFicheEleves[$eleve->getPrimaryKey()];
		}
		if (getSettingValue("GepiAccesGestElevesProfP")=='yes') {
		    if ($this->getEleves()->contains($eleve)) {
			$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = true;
			return true;
		    }
		}
		if (getSettingValue("GepiAccesGestElevesProf")=='yes') {
		    //on cherche dans les groupes du professeur
		    $query = EleveQuery::create()->filterByIdEleve($eleve->getIdEleve())
			    ->useJEleveGroupeQuery()->useGroupeQuery()->useJGroupesProfesseursQuery()
			    ->filterByUtilisateurProfessionnel($this)
			    ->endUse()->endUse()->endUse();
		    if ($query->findOne() != null) {
			$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = true;
			return true;
		    }
		    //on cherche dans les aid du professeur
		    $query = EleveQuery::create()->filterByIdEleve($eleve->getIdEleve())
			    ->useJAidElevesQuery()->useAidDetailsQuery()->useJAidUtilisateursProfessionnelsQuery()
			    ->filterByUtilisateurProfessionnel($this)
			    ->endUse()->endUse()->endUse();
		    if ($query->findOne() != null) {
			$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = true;
			return true;
		    }
		}
		$this->collAccesFicheEleves[$eleve->getPrimaryKey()] = false;
		return false;
	    } else if ($this->getStatut() == "autre") {
		    if (isset($this->collAccesFicheEleves['statut_autre'])) {
			return $this->collAccesFicheEleves['statut_autre'];
		    }

		    // On récupère les droits de ce statuts pour savoir ce qu'on peut afficher
		    $sql_d = "SELECT * FROM droits_speciaux WHERE id_statut = '" . $_SESSION['statut_special_id'] . "'";
		    $query_d = mysql_query($sql_d);

		    while($rep_d = mysql_fetch_array($query_d)){
			    //print_r($rep_d);
			    if (($rep_d['nom_fichier'] == '/voir_resp' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/voir_ens' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/voir_notes' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/voir_bulle' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/voir_abs' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/voir_anna' AND $rep_d['autorisation'] == 'V')
				|| ($rep_d['nom_fichier'] == '/mod_discipline/saisie_incident.php' AND $rep_d['autorisation'] == 'V')
				) {
				$this->collAccesFicheEleves['statut_autre'] = true;
				return true;
			    }
		    }
		    $this->collAccesFicheEleves['statut_autre'] = false;
		    return false;
	    }
	    return false;
	}
	
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
				if ($this->statut == "professeur") {
				    $collGroupes = GroupeQuery::create(null, $criteria)
					->filterByUtilisateurProfessionnel($this)
					->leftJoin('Groupe.JGroupesClasses')->with('JGroupesClasses')
					->leftJoin('JGroupesClasses.Classe')->with('Classe')
					->find($con);
				} elseif ($this->statut == "cpe") {
				    //on ajoute les groupes contenant des eleves sous la responsabilite du cpe
				    $collGroupes = GroupeQuery::create(null, $criteria)
					    ->distinct()->useJEleveGroupeQuery()
					    ->useEleveQuery()->useJEleveCpeQuery()
					    ->filterByUtilisateurProfessionnel($this)->endUse()
					    ->endUse()->endUse()
					    ->leftJoin('Groupe.JGroupesClasses')->with('JGroupesClasses')
					    ->leftJoin('JGroupesClasses.Classe')->with('Classe')
					    ->find();
				} else if ($this->statut == "scolarite") {
				    //on ajoute les groupes des classes sous la responsabilite du compte scolalite
				    $collGroupes = GroupeQuery::create(null, $criteria)
					    ->useJGroupesClassesQuery()->useClasseQuery()->useJScolClassesQuery()
					    ->filterByUtilisateurProfessionnel($this)
					    ->endUse()->endUse()->endUse()
					    ->leftJoin('Groupe.JGroupesClasses')->with('JGroupesClasses')
					    ->leftJoin('JGroupesClasses.Classe')->with('Classe')
					    ->find();
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
		$this->collEleves = null;
		$this->collAccesFicheEleves = null;
	}

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		parent::reload($deep, $con);
		if ($deep) {  // also de-associate any related objects?
		    $this->collClasses = null;
		    $this->collEleves = null;
		    $this->collAccesFicheEleves = null;
		}
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
				$collClasses = ClasseQuery::create(null, $criteria)->distinct()->orderByNomComplet()->filterByUtilisateurProfessionnel($this)->find($con);
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
	 * Renvoi sous forme d'un tableau la liste des groupes d'un utilisateur professeur. Le tableau est ordonné par le noms du groupes puis les classes du groupes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Groupes[]
	 */
	public function getAidDetailss($criteria = null, PropelPDO $con = null) {
		if(null === $this->collAidDetailss || null !== $criteria) {
			if ($this->isNew() && null === $this->collAidDetailss) {
				// return empty collection
				$this->initAidDetailss();
			} else {
				$collAidDetailss = parent::getAidDetailss($criteria, $con);

				if ($this->statut == "cpe") {
				    $aid_col = AidDetailsQuery::create(null, $criteria)
					    ->useJAidElevesQuery()
					    ->useEleveQuery()
					    ->useJEleveCpeQuery()
					    ->filterByUtilisateurProfessionnel($this)
					    ->endUse()->endUse()->endUse()
					    ->distinct()
					    ->find($con);
				    $collAidDetailss->addCollection($aid_col);
				} else if ($this->statut == "scolarite") {
				    $aid_col = AidDetailsQuery::create(null, $criteria)
					    ->useJAidElevesQuery()->useEleveQuery()->useJEleveClasseQuery()->useClasseQuery()->useJScolClassesQuery()
					    ->filterByUtilisateurProfessionnel($this)
					    ->endUse()->endUse()->endUse()->endUse()->endUse()
					    ->find($con);
				    $collAidDetailss->addCollection($aid_col);
				}
				if (null !== $criteria) {
					return $collAidDetailss;
				}
				$this->collAidDetailss = $collAidDetailss;
			}
		}
		return $this->collAidDetailss;
	}
}
