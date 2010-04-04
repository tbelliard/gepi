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
	 * @var        array $collGroupes[] Collection to store aggregation of Groupe objects.
	 */
	protected $collGroupes;

	/**
	 * 
	 * Renvoi sous forme d'un tableau la liste des groupes d'un utilisateur professeur. Le tableau est ordonné par le noms du groupes puis les classes du groupes.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Groupes[]
	 */
	public function getGroupes($con = null) {
		if ($this->collGroupes != null) {
			return $this->collGroupes;
		} else {
		    $groupes = new PropelObjectCollection();
		    foreach($this->getJGroupesProfesseurssJoinGroupe($con) as $ref) {
			if ($ref != NULL) {
			    $groupes->append($ref->getGroupe());
			}
		    }
		    require_once("helpers/GroupeHelper.php");
		    $this->collGroupes = GroupeHelper::orderByGroupNameWithClasses($groupes);
		    return $this->collGroupes;
		}
	}

	/**
	 * Clears out the collGroupes collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearGroupes()
	{
		$this->collGroupes = null; // important to set this to NULL since that means it is uninitialized
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
		$this->collGroupes = null;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eleves d'un utilisateur professeur principal.
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
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
	 * @return     array Eleves[]
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
	 * @return     array Eleves[]
	 */
	public function getEleveCpes($con = null) {
		return $this->getEleves();
	}

	/**
	 *
	 * Ajoute un eleve a un cpe
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
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
	 * @return     array Eleves[]
	 */
	public function getPreferenceValeur($name){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if ($prefs->isEmpty()) {
		return null;
	    } else {
		return $prefs->getFirst();
	    }
	}

	/**
	 *
	 * Enregistre une preference d'un utilisateur
	 * Ajout manuel
	 *
	 * @param      String $name le nom de la preference à obtenir
	 * @return     array Eleves[]
	 */
	public function setPreferenceValeur($name, $value){
	    $criteria = new Criteria();
	    $criteria->add(PreferenceUtilisateurProfessionnelPeer::NAME, $name);
	    $prefs = $this->getPreferenceUtilisateurProfessionnels($criteria);
	    if ($pref->isEmty()) {
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
	public function getEdtEmplacementCoursActuel($v = 'now'){
	    // we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
	    // -- which is unexpected, to say the least.
	    //$dt = new DateTime();
	    if ($v === null || $v === '') {
		    $dt = null;
	    } elseif ($v instanceof DateTime) {
		    $dt = $v;
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
	    $edtTypeSemaine = EdtTypeSemaineQuery::create()->filterByNumEdtSemaine($num_semaine)->findOne();
	    if ($edtTypeSemaine == null) {
		$type_semaine = '';
	    } else {
		$type_semaine = $edtTypeSemaine->getTypeEdtSemaine();
	    }

	    // On traduit le nom du jour
	    $semaine_declaration = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	    $jour_semaine = $semaine_declaration[$dt->format("w")];

	    $edtCoursCol = $this->getEdtEmplacementCoursPeriodeCalendrierActuelle($dt);

	    $timeStampNow = strtotime($dt->format('H:i:s'));
	    $edtCours = new EdtEmplacementCours();
	    foreach ($edtCoursCol as $edtCours) {
		if ($jour_semaine == $edtCours->getJourSemaine() &&
		    ($type_semaine == $edtCours->getTypeSemaine() || $edtCours->getTypeSemaine() == '')) {
		    if (strtotime($edtCours->getHeureDebut()) <= $timeStampNow &&
			$timeStampNow < strtotime($edtCours->getHeureFin())) {
			return $edtCours;
		    }
		}
	    }

	    return null;
	}

	/**
	 *
	 * Retourne tous les emplacements de cours pour la periode précisée du calendrier.
	 * On recupere aussi les emplacements dont la periode n'est pas definie ou vaut 0.
	 *
	 * @return PropelObjectCollection EdtEmplacementCours une collection d'emplacement de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCoursPeriodeCalendrierActuelle($v = 'now'){
	    $periodeCalendrier = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle();
	    if ($periodeCalendrier != NULL) {
		$edtCoursCol = EdtEmplacementCoursQuery::create()->filterByLoginProf($this->getLogin())
		    ->filterByEdtCalendrierPeriode($periodeCalendrier)->find();
	    } else {
		$edtCoursCol = new PropelObjectCollection();
	    }

	    //on recupere les cours pour la periode 0 et la periode null car ils ont lieur toute l'année.
	    $edtCoursCol2 = EdtEmplacementCoursQuery::create()->filterByLoginProf($this->getLogin())
		->filterByIdCalendrier(null)->find();
	    foreach ($edtCoursCol2 as $edtCours) {
		$edtCoursCol->append($edtCours);
	    }
	    $edtCoursCol2 = EdtEmplacementCoursQuery::create()->filterByLoginProf($this->getLogin())
		->filterByIdCalendrier(0)->find();
	    foreach ($edtCoursCol2 as $edtCours) {
		$edtCoursCol->append($edtCours);
	    }

	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    EdtEmplacementCoursHelper::orderChronologically($edtCoursCol);

	    return $edtCoursCol;
	}


	/**
	 *
	 * Retourne les emplacements de cours pour un groupe donnee pour la periode du calendrier actuelle
	 *
	 * @param  String $groupe_id l'id de la classe
	 * @return PropelObjectCollection EdtEmplacementCours les emplacements de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCoursPeriodeCalendrierActuelleJoinGroupe($groupe_id){
	    throw new PropelException("Pas encore implemente");
	    return new PropelObjectCollection();
	}

	/**
	 *
	 * Retourne les emplacements de cours pour un groupe donnee
	 *
	 * @param  String $groupe_id l'id de la classe
	 * @return PropelObjectCollection EdtEmplacementCours les emplacements de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCoursJoinGroupe($groupe_id){
	    throw new PropelException("Pas encore implemente");
	    return new PropelObjectCollection();
	}
}
