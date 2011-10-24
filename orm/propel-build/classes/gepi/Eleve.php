<?php



/**
 * Skeleton subclass for representing a row from the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class Eleve extends BaseEleve {

	/**
	 * @var        array PeriodesNote[] Collection to store aggregation of PeriodesNote objects.
	 */
	protected $collPeriodeNotes;

	/**
	 * @var        array PeriodesNote[] Collection to store aggregation of PeriodesNote objects.
	 */
	protected $collCachePeriodeNotesResult;

	/**
	 * @var        array Classe[][] Collection to store aggregation of Classes objects. There is a collection for each periode
	 */
	protected $collClasses;

	/**
	 * @var        array Groupe[][] Collection to store aggregation of Groupes objects. There is a collection for each periode
	 */
	protected $collGroupes;

	/**
	 * @var        array AbsenceEleveSaisie[][] Collection to store aggregation of AbsenceEleveSaisie objects.
	 * There is a collection for each day
	 */
	protected $collAbsenceEleveSaisiesParJour;

	/**
	 * @var        PeriodeNote object.
	 */
	protected $periodeNoteOuverte;

    // ERREUR ?? Il ne peut y avoir qu'une seule classe pour un élève pour une période !!
	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode ou objet periodeNote
	 * @return     PropelObjectCollection Classes[]
	 *
	 */
	public function getClasses($periode) {
		$periode = $this->getPeriodeNote($periode);
		require_once("helpers/PeriodeNoteHelper.php");
		$periode_num = PeriodeNoteHelper::getNumPeriode($periode);
		if(!isset($this->collClasses[$periode_num]) || null === $this->collClasses[$periode_num]) {
			if ($this->isNew() && null === $this->collClasses[$periode_num]) {
				// return empty collection
				$this->initClasses($periode_num);
			} else {
				$classe_hydrated = false;
				if (null !== $this->collJEleveClasses) {
				    //on teste si la collection de collJEleveClasses est hydratee avec les classes
				    if ($this->collJEleveClasses->getFirst() != null) {
					if ($this->collJEleveClasses->getFirst()->isClasseHydrated()) {
					    $classe_hydrated = true;
					}
				    }
				}

				if ($classe_hydrated) {
				    foreach ($this->getJEleveClasses() as $JEleveClasse) {
					if ($JEleveClasse->getClasse() != null) {
					    if(!isset($this->collClasses[$JEleveClasse->getPeriode()]) || null === $this->collClasses[$JEleveClasse->getPeriode()]) {
						$this->initClasses($JEleveClasse->getPeriode());
					    }
					    $this->collClasses[$JEleveClasse->getPeriode()]->add($JEleveClasse->getClasse());
					}
				    }
				    if ($this->collClasses[$periode_num] == null) {
					//rien n'a Ã©tÃ© trouvÃ© pour cette pÃ©riode, on renvoi une collection vide
					$this->initClasses($periode_num);
				    }
				} else {
				    $query = ClasseQuery::create();
				    if ($periode != null) {
					    $query->useJEleveClasseQuery()->filterByEleve($this)->filterByPeriode($periode_num)->endUse();
				    } else {
					    $query->useJEleveClasseQuery()->filterByEleve($this)->endUse();
				    }
				    $query->orderByNomComplet()->distinct();
				    $this->collClasses[$periode_num] = $query->find();
				}
			}
		}
		return $this->collClasses[$periode_num];
	}

 	/**
	 *
	 * Renvoi la classe d'un eleve. Si un eleve est affecté dans plusieurs classes, seule une classe est renvoyée
	 *
	 * @param      integer $periode numero de la periode ou objet periodeNote
	 * @return     Classe
	 *
	 */
	public function getClasse($periode = null) {
		return $this->getClasses($periode)->getFirst();
	}

 	/**
	 *
	 * Renvoi le nom de la classe d'un eleve. Si un eleve est affecté dans plusieurs classes, seule une% nom est renvoyée
	 *
	 * @param      integer $periode numero de la periode ou objet periodeNote
	 * @return     Classe
	 *
	 */
	public function getClasseNom($periode = null) {
		$classe = $this->getClasse($periode);
		if ($classe == null) {
		    return '';
		} else {
		    return $classe->getNom();
		}
	}

	/**
	 *
	 * Renvoi le nom de la classe d'un eleve. Si un eleve est affecté dans plusieurs classes, seul un nom est renvoyée
	 * Si pas de classe trouvée, renvoi null
	 *
	 * @param      integer $periode numero de la periode ou objet periodeNote
	 * @return     string
	 *
	 */
	public function getClasseNomComplet($periode = null) {
		$classe = $this->getClasse($periode);
		if ($classe == null) {
		    return null;
		}else {
		    return $classe->getNomComplet();
		}
	}

	/**
	 * Initializes the collClasses collection.
	 *
	 * @param      integer $periode numero de la periode ou objet periodeNote
	 * @return     void
	 */
	public function initClasses($periode_num)
	{
		$this->collClasses[$periode_num] = new PropelObjectCollection();
		$this->collClasses[$periode_num]->setModel('Classe');
	}

	/**
	 * Initializes the collPeriodeNotes collection.
	 *
	 * @return     void
	 */
	public function initPeriodeNotes()
	{
		$this->collPeriodeNotes = new PropelObjectCollection();
		$this->collPeriodeNotes->setModel('Classe');
	}

	/**
	 * Initializes the collAbsenceEleveSaisiesParJour collection.
	 *
	 * @param      strind $date_string clé date du jour format('d/m/Y')
	 * @return     void
	 */
	public function initAbsenceEleveSaisiesParJour($date_string)
	{
		$this->collAbsenceEleveSaisiesParJour[$date_string] = new PropelObjectCollection();
		$this->collAbsenceEleveSaisiesParJour[$date_string]->setModel('AbsenceEleveSaisie');
	}

	/**
	 * Clears out the collClasses collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearClasses()
	{
		$this->collClasses = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Clears out the collPeriodeNotes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearPeriodeNotes()
	{
		$this->collPeriodeNotes = null; // important to set this to NULL since that means it is uninitialized
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
	    parent::reload($deep,$con);
	    $this->collPeriodeNotes = null;
	    $this->collClasses = null;
	    $this->collGroupes = null;
	    $this->collAbsenceEleveSaisiesParJour = null;
	    $this->periodeNoteOuverte = null;
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
	public function clearAllReferences($deep = false) {
	    parent::clearAllReferences($deep);
	    $this->collPeriodeNotes = null;
	    $this->collClasses = null;
	    $this->collGroupes = null;
	    $this->collAbsenceEleveSaisiesParJour = null;
	    $this->periodeNoteOuverte = null;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un eleve pour une période donnée.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Groupes[]
	 *
	 */
	public function getGroupes($periode = null) {
		//$periode = $this->getPeriodeNote($periode); on ne vérifie pas si l'objet période existe vraiment
		require_once("helpers/PeriodeNoteHelper.php");
		$periode_key = PeriodeNoteHelper::getNumPeriode($periode);
                if ($periode_key === null) {
                    $periode_key = 'null'; // utile pour le clés du vecteur $this->collGroupes
                }
		if(!isset($this->collGroupes[$periode_key]) || null === $this->collGroupes[$periode_key]) {
			if ($this->isNew() && null === $this->collGroupes[$periode_key]) {
				// return empty collection
				$this->initGroupes($periode_key);
			} else {
				$query = GroupeQuery::create();
				if ($periode_key != 'null') {
					$query->useJEleveGroupeQuery()
					    ->filterByEleve($this)
					    ->filterByPeriode($periode_key)
					    ->endUse();
				} else {
					$query->useJEleveGroupeQuery()
					    ->filterByEleve($this)
					    ->endUse();
				}
				$query->orderByName()->distinct();
				$this->collGroupes[$periode_key] = $query->find();
			}
		}
		return $this->collGroupes[$periode_key];
	}

	/**
	 * Initializes the collGroupes collection.
	 *
	 * @return     void
	 */
	public function initGroupes($periode_num)
	{
		$this->collGroupes[$periode_num] = new PropelObjectCollection();
		$this->collGroupes[$periode_num]->setModel('Groupe');
	}

	/**
	 * Clears out the collGroupes collection
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
	 * Clears out the collGroupes collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearAbsenceEleveSaisiesParJour()
	{
		$this->collAbsenceEleveSaisiesParJour = null; // important to set this to NULL since that means it is uninitialized
	}

	
    public function getGroupesByCategories($periode) {
        // On commence par récupérer tous les groupes
        $groupes = $this->getGroupes($periode);
        // Ensuite, il nous faut les catégories. Pour ça, on passe par les classes.
        $classe = $this->getClasse($periode);
        $categories = array();
        $c = new Criteria();
        $c->add(JCategoriesMatieresClassesPeer::CLASSE_ID,$classe->getId());
        $c->addAscendingOrderByColumn(JCategoriesMatieresClassesPeer::PRIORITY);
        foreach(JCategoriesMatieresClassesPeer::doSelect($c) as $j) {
            $cat = $j->getCategorieMatiere();
            $categories[$cat->getId()] = array(0 => $cat, 1 => array());
        }
        // Maintenant, on mets tout ça ensemble
        foreach($groupes as $groupe) {
            $cat = $groupe->getCategorieMatiere($classe);
            $categories[$cat->getId()][1][] = $groupe;
        }
        // On renvoie un table multi-dimensionnel, qui contient les catégories
        // dans le bon ordre, et les groupes sous chaque catégorie.
        return $categories;
    }

    	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un élève pour une période donnée
     * en limitant aux groupes pour lesquels une saisie ECTS est prévue.

	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Groupes[]
	 *
	 */
	public function getEctsGroupes($periode) {
		$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);

        $sql = "SELECT groupes.* FROM groupes, j_eleves_classes jec, j_groupes_classes jgc, j_eleves_groupes jeg
                    WHERE (groupes.id = jgc.id_groupe AND jgc.id_groupe = jeg.id_groupe AND jgc.id_classe = jec.id_classe AND jgc.saisie_ects = TRUE AND jec.login = jeg.login AND jec.periode = jeg.periode AND jeg.periode = '".$periode."' AND jeg.login = '".$this->getLogin()."') ORDER BY jgc.priorite";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $groupes = GroupePeer::populateObjects($stmt);
		return $groupes;
	}

    public function getEctsGroupesByCategories($periode) {
        // On commence par récupérer tous les groupes
        $groupes = $this->getGroupes($periode);
        // Ensuite, il nous faut les catégories. Pour ça, on passe par les classes.
        $classe = $this->getClasse($periode);
        $categories = array();
        $c = new Criteria();
        $c->add(JCategoriesMatieresClassesPeer::CLASSE_ID,$classe->getId());
        $c->addAscendingOrderByColumn(JCategoriesMatieresClassesPeer::PRIORITY);
        foreach(JCategoriesMatieresClassesPeer::doSelect($c) as $j) {
            $cat = $j->getCategorieMatiere();
            $categories[$cat->getId()] = array(0 => $cat, 1 => array());
        }
        // Maintenant, on mets tout ça ensemble
        foreach($groupes as $groupe) {
            if ($groupe->allowsEctsCredits($classe->getId())) {
                $cat = $groupe->getCategorieMatiere($classe->getId());
                $categories[$cat->getId()][1][$groupe->getId()] = $groupe;
            }
        }

        foreach($categories as $cat) {
            if (count($cat[1]) == 0) {
                $id = $cat[0]->getId();
                unset($categories[$id]);
            }
        }

        // On renvoie un table multi-dimensionnel, qui contient les catégories
        // dans le bon ordre, et les groupes sous chaque catégorie.
        return $categories;
    }


	/**
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     EctsCredit
	 *
	 */
	public function getEctsCredit($periode,$id_groupe) {
		$criteria = new Criteria();
		$criteria->add(CreditEctsPeer::NUM_PERIODE,$periode);
		$criteria->add(CreditEctsPeer::ID_GROUPE,$id_groupe);
		$v = $this->getCreditEctss($criteria);
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

	public function deleteEctsCredit($periode,$id_groupe) {
        $credit = $this->getEctsCredit($periode, $id_groupe);
        return $credit == null ? true : $credit->delete();
	}

  // On remet à zéro le crédit ECTS concerné
  // Cela signifie simplement que l'on efface la valeur et la mention 'officielle'
  // pour ainsi ne conserver que l'éventuelle pré-saisie du prof
	public function resetEctsCredit($periode,$id_groupe) {
        $credit = $this->getEctsCredit($periode, $id_groupe);
        if ($credit) {
          $credit->setValeur(null);
          $credit->setMention(null);
        }
        return $credit == null ? true : $credit->save();
	}

	public function getArchivedEctsCredits($annee,$periode) {
		$criteria = new Criteria();
		$criteria->add(ArchiveEctsPeer::NUM_PERIODE,$periode);
        $criteria->add(ArchiveEctsPeer::ANNEE,$annee);
        $v = $this->getArchiveEctss($criteria);
        $result = array();
        if (!empty($v)) {
            foreach($v as $credit){
                $result[$credit->getId()] = $credit;
            }
        }
        return $result;
	}

	public function getArchivedEctsCredit($annee,$periode,$matiere) {
		$criteria = new Criteria();
		$criteria->add(ArchiveEctsPeer::NUM_PERIODE,$periode);
        $criteria->add(ArchiveEctsPeer::ANNEE,$annee);
        $criteria->add(ArchiveEctsPeer::MATIERE,$matiere);
        $v = $this->getArchiveEctss($criteria);
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

	public function getCreditEctsGlobal() {
        $v = $this->getCreditEctsGlobals();
		if ($v->isEmpty()) {
		    return null;
		} else {
		    return $v->getFirst();
		}
	}

    public function getEctsAnneesPrecedentes() {
        $c = new Criteria();
        $c->addAscendingOrderByColumn(ArchiveEctsPeer::ANNEE);
        $c->addAscendingOrderByColumn(ArchiveEctsPeer::NUM_PERIODE);
        $archives = $this->getArchiveEctss($c);
        $annees = array();
        foreach ($archives as $a) {
            if (array_key_exists($a->getAnnee(), $annees)) {
                // Le tableau avec l'année existe déjà.
                // On regarde si c'est le cas pour la période.
                if (!array_key_exists($a->getNumPeriode(), $annees[$a->getAnnee()]['periodes'])) {
                    $annees[$a->getAnnee()]['periodes'][$a->getNumPeriode()] = $a->getNomPeriode();
                }
            } else {
                $annees[$a->getAnnee()] = array('annee' => $a->getAnnee(), 'periodes' => array($a->getNumPeriode() => $a->getNomPeriode()));
            }
        }
        return $annees;
    }
    /**
	 * Enregistre les crédits ECTS pour une période et un groupe
	 */
	public function setEctsCredit($periode,$id_groupe,$valeur_ects,$mention_ects,$mention_prof = null) {
        $credit = $this->getEctsCredit($periode,$id_groupe);
        if ($credit == null) {
            $credit = new CreditEcts();
            $credit->setEleve($this);
            $credit->setIdGroupe($id_groupe);
            $credit->setNumPeriode($periode);
        }
        // Si on enregistre une pré-saisie, alors on n'enregistre que ça, sans toucher au reste.
        if ($mention_prof) {
          $credit->setMentionProf($mention_prof);
        } else {
          $credit->setValeur($valeur_ects);
          $credit->setMention($mention_ects);
        }
        return $credit->save();
	}

	public function setCreditEctsGlobal($mention_ects) {
        $credit = $this->getCreditEctsGlobal();
        if ($credit == null) {
            $credit = new CreditEctsGlobal();
            $credit->setEleve($this);
        }
        $credit->setMention($mention_ects);
        return $credit->save();
	}

	/**
	 *
	 * Retourne l'emplacement de cours pour la periode de note donnée.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return EdtEmplacementCours l'emplacement de cours actuel ou null si pas de cours actuellement
	 */
	public function getEdtEmplacementCours($v){

	    $edtCoursCol = $this->getEdtEmplacementCourssPeriodeCalendrierActuelle($v);

	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    return EdtEmplacementCoursHelper::getEdtEmplacementCoursActuel($edtCoursCol, $v);
	}

	/**
	 *
	 * Retourne tous les emplacements de cours pour la periode précisée du calendrier.
	 * On recupere aussi les emplacements dont la periode n'est pas definie ou vaut 0.
	 *
	 * @return PropelObjectCollection EdtEmplacementCours une collection d'emplacement de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCourssPeriodeCalendrierActuelle($v = 'now'){
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

	    //si il n'y a aucune periode ouverte actuellement, on renvoi tous les groupe et donc tous les emplacements de cours
	    $colGroupeId = $this->getGroupes($this->getPeriodeNote($dt))->getPrimaryKeys();

	    $query = EdtEmplacementCoursQuery::create()->filterByIdGroupe($colGroupeId)
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

 	    //si il n'y a aucune periode ouverte actuellement, on renvoi tous les groupe et donc tous les emplacements de cours
	    $colAidId = $id_array = $this->getAidDetailss()->getPrimaryKeys();

	    $query = EdtEmplacementCoursQuery::create()->filterByIdAid($colAidId)
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
	    $edtCoursCol->addCollection($query->find());

	    require_once("helpers/EdtEmplacementCoursHelper.php");
	    EdtEmplacementCoursHelper::orderChronologically($edtCoursCol);

	    return $edtCoursCol;
	}

  	/**
	 *
	 * Retourne une periode de note pour laquelle l'eleve est affectée à une classe dont la periode est ouverte
	 *
	 * @return PeriodeNote objet periode ou null si pas de periode ouverte
	 */
	public function getPeriodeNoteOuverte() {
		if(null === $this->periodeNoteOuverte) {
			if ($this->isNew() && null === $this->periodeNoteOuverte) {
				return null;
			} else {
			    $periode_result = null;
			    $count_verrouiller_n = 0;
			    $count_verrouiller_p = 0;
			    $periode_verrouiller_n = null;
			    $periode_verrouiller_p = null;
			    foreach ($this->getPeriodeNotes() as $periode) {
				if ($periode->getVerouiller() == 'N') {
				    $count_verrouiller_n = $count_verrouiller_n + 1;
				    if (!isset($periode_verrouiller_n)
					    || $periode_verrouiller_n == null
					    || $periode_verrouiller_n->getNumPeriode() > $periode->getNumPeriode())
				    $periode_verrouiller_n = $periode;
				}
				if ($periode->getVerouiller() == 'P') {
				    $count_verrouiller_p = $count_verrouiller_p + 1;
				    if (!isset($periode_verrouiller_p)
					    ||$periode_verrouiller_p == null
					    || $periode_verrouiller_p->getNumPeriode() > $periode->getNumPeriode())
				    $periode_verrouiller_p = $periode;
				}
			    }

			    if ($count_verrouiller_n == 1) {
				//si on a une seule periode ouverte alors c'est la periode actuelle
				$periode_result = $periode_verrouiller_n;
			    } elseif ($count_verrouiller_n == 0 && $count_verrouiller_p == 1) {
				//si on a une seule periode partiellement ouverte et aucune ouverte alors c'est la periode actuelle
				$periode_result = $periode_verrouiller_p;
			    } else {
				//on va prendre la periode de numero la plus petite non verrouillee
				if ($periode_verrouiller_n != null) {
				    $periode_result = $periode_verrouiller_n;
				} elseif ($periode_verrouiller_p != null) {
				    $periode_result = $periode_verrouiller_p;
				} else {
				    $periode_result = null;
				}
			    }
			    $this->periodeNoteOuverte = $periode_result;
			}
		}
		return $this->periodeNoteOuverte;
	}

  	/**
	 *
	 * Retourne une liste d'absence du jour
	 *
	 * @return PropelCollection AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesDuJour($v = 'now') {
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
	    $dt->setTime(0,0,0);

	    
	    if(!isset($this->collAbsenceEleveSaisiesParJour[$dt->format('d/m/Y')]) || null === $this->collAbsenceEleveSaisiesParJour[$dt->format('d/m/Y')]) {
		    if ($this->isNew() && null === $this->collAbsenceEleveSaisiesParJour[$dt->format('d/m/Y')]) {
			    // return empty collection
			    $this->initAbsenceEleveSaisiesParJour($dt->format('d/m/Y'));
		    } else {
			    $dt_fin = clone $dt;
			    $dt_fin->setTime(23,59,59);

			    if ($this->collAbsenceEleveSaisies !== null && $this->collAbsenceEleveSaisies->count() > 100) {
				//il y a trop de saisie, on passe l'optimisation et on fait une requete db
				$query = AbsenceEleveSaisieQuery::create()->filterByEleve($this);
				$query->filterByPlageTemps($dt, $dt_fin);
				$collAbsenceEleveSaisiesParJour = $query->distinct()->find();
			    } else {
				//on passe optimise en travaillant sur les saisies sans faire de requete db
				$saisie_col = $this->getAbsenceEleveSaisies();
				$collAbsenceEleveSaisiesParJour = new PropelObjectCollection();
				$collAbsenceEleveSaisiesParJour->setModel('AbsenceEleveSaisie');
				foreach ($saisie_col as $saisie) {
				    if ($dt->format('U') <  $saisie->getFinAbs('U')
					    && $dt_fin->format('U') >  $saisie->getDebutAbs('U')) {
					$collAbsenceEleveSaisiesParJour->append($saisie);
				    }
				}
			    }
			    $this->collAbsenceEleveSaisiesParJour[$dt->format('d/m/Y')] = $collAbsenceEleveSaisiesParJour;
		    }
	    }
	    return $this->collAbsenceEleveSaisiesParJour[$dt->format('d/m/Y')];

	}

    /**
	 *
	 * Retourne une liste d'absence pour le creneau et le jour donné.
	 *
	 * @param      EdtCreneau $edtcreneau
     * @param      Id lieu $id_lieu
     * @param      mixed $v string, integer (timestamp), or DateTime value.  
	 *
 	 * @return PropelColection AbsenceEleveSaisie[]
	 */
    public function getAbsenceEleveSaisiesDuCreneauByLieu($edtcreneau = null, $id_lieu = null, $v = 'now') {
        $result = new PropelObjectCollection();
        $result->setModel('AbsenceEleveSaisie');
        $saisie_col = $this->getAbsenceEleveSaisiesDuCreneau($edtcreneau, $v);
        foreach ($saisie_col as $saisie) {
            if ($saisie->hasLieuSaisie($id_lieu) && !$saisie->hasTypeLikeErreurSaisie()) {
                $result->append($saisie);
            }
        }
        return($result);
    }
  	/**
	 *
	 * Retourne une liste d'absence pour le creneau et le jour donné.
	 *
	 * @param      EdtCreneau $edtcreneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 *
 	 * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesDuCreneau($edtcreneau = null, $v = 'now') {
	    if ($edtcreneau == null) {
		$edtcreneau = EdtCreneauPeer::retrieveEdtCreneauActuel($v);
	    }
	    
	    if (!($edtcreneau instanceof EdtCreneau)) {
		throw new PropelException('Le premier argument doit etre de la classe EdtCreneau');
	    }
	    
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
	    
	    $dt->setTime($edtcreneau->getHeuredebutDefiniePeriode('H'), $edtcreneau->getHeuredebutDefiniePeriode('i'), 0);
	    $dt_fin_creneau = clone $dt;
	    $dt_fin_creneau->setTime($edtcreneau->getHeurefinDefiniePeriode('H'), $edtcreneau->getHeurefinDefiniePeriode('i'), 0);

	    return $this->getAbsenceEleveSaisiesFilterByDate($dt, $dt_fin_creneau);
	}

  	/**
	 *
	 * Retourne une liste de saisie dont la periode de temps coincide avec les dates passees en paremetre (methode optimisee)
	 *
	 * @param      $dt_debut DateTime
	 * @param      $dt_fin DateTime
	 *
 	 * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesFilterByDate($dt_debut, $dt_fin) {
	    $result = new PropelObjectCollection();
	    $result->setModel('AbsenceEleveSaisie');
	    if ($dt_debut != null && $dt_debut->format('d/m/Y') == $dt_fin->format('d/m/Y')) {
		//on a une date de debut et de fin le meme jour, on va optimiser un peu
		$saisie_col = $this->getAbsenceEleveSaisiesDuJour($dt_debut);
	    } else {
		if ($this->countAbsenceEleveSaisies() > 100) {
		    //il y a trop de saisie, on passe l'optimisation et on fait une requete db
		    $query = AbsenceEleveSaisieQuery::create()->filterByEleve($this);
		    $query->filterByPlageTemps($dt_debut, $dt_fin)
			->leftJoinWith('AbsenceEleveSaisie.JTraitementSaisieEleve')
			->leftJoinWith('JTraitementSaisieEleve.AbsenceEleveTraitement')
			->leftJoinWith('AbsenceEleveTraitement.AbsenceEleveType');
		    return $query->distinct()->find();
		} else {
		    $saisie_col = $this->getAbsenceEleveSaisies();
		}
	    }
	    foreach ($saisie_col as $saisie) {
		if ($dt_debut != null && $dt_fin!= null && $dt_debut->format('U') == $dt_fin->format('U')) {
		    //si on a un seul dateTime pour la plage de recherche, on renvoi les saisie qui chevauchent cette date
		    //ainsi que les saisies qui commence juste à cette date
		    if ($dt_debut->format('U') >=  $saisie->getFinAbs('U')) {
			continue;
		    }
		    if ($dt_fin != null && ($dt_fin->format('U') <  $saisie->getDebutAbs('U'))) {
			continue;
		    }
		    $result->append($saisie);
		} else {
		    if ($dt_debut != null && ($dt_debut->format('U') >=  $saisie->getFinAbs('U'))) {
			continue;
		    }
		    if ($dt_fin != null && ($dt_fin->format('U') <=  $saisie->getDebutAbs('U'))) {
			continue;
		    }
		    $result->append($saisie);
		}
	    }

	    return $result;
	}
	
	/*
	Renvoie le nom de la photo de l'élève
	Renvoie NULL si :
	- le module trombinoscope n'est pas activé
	- ou bien la photo n'existe pas.

	$_elenoet_ou_loginc : selon les cas, soir l'elenoet de l'élève ou bien lelogin du professeur
	$repertoire : "eleves"
	$arbo : niveau d'aborescence (1 ou 2).
	*/
	public function getNomPhoto($arbo=1) {
		if ($arbo==2) {$chemin = "../";} else {$chemin = "";}
		$repertoire = "eleves";
		if (getSettingValue("active_module_trombinoscopes")!='y') {
			return NULL;
			die();
		}

		$_elenoet_ou_login = $this->getElenoet();
	  	if($_elenoet_ou_login!='') {

		// En multisite, on ajoute le répertoire RNE
		if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			  // On récupère le RNE de l'établissement
		  $repertoire2=$_COOKIE['RNE']."/";
		}else{
		  $repertoire2="";
		}
		
		$photo = null;
		// on vérifie si la photo existe
		if(file_exists($chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg")) {
			$photo=$chemin."../photos/".$repertoire2."eleves/".$_elenoet_ou_login.".jpg";
		}
		else if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y')
		{
		  // En multisite, on recherche aussi avec les logins
		  if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
			// On récupère le login de l'élève
			$sql = 'SELECT login FROM eleves WHERE elenoet = "'.$_elenoet_ou_login.'"';
			$query = mysql_query($sql);
			$_elenoet_ou_login = mysql_result($query, 0,'login');
		  }

		  if(file_exists($chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg")) {
				$photo=$chemin."../photos/".$repertoire2."eleves/$_elenoet_ou_login.jpg";
			}
			else {
				if(file_exists($chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg")) {
					$photo=$chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",$_elenoet_ou_login).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(substr($_elenoet_ou_login,$i,1)=="0"){
							$test_photo=substr($_elenoet_ou_login,$i+1);
							//if(file_exists($chemin."../photos/eleves/".$test_photo.".jpg")){
							if(($test_photo!='')&&(file_exists($chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg"))) {
								$photo=$chemin."../photos/".$repertoire2."eleves/".$test_photo.".jpg";
								break;
							}
						}
					}
				}
			}

		}else{
		  $photo=NULL;
		}
		return $photo;
	  }else{
		return NULL;

	  }





	}

	/**
	 * renvoi la civilite 
	 * M. ou Mlle
	 * @return     string
	 */
	public function getCivilite()
	{
		if($this->getSexe()=="M") {
			return "M.";
		} elseif ($this->getSexe()=="F") {
			return "Mlle";
		}
		return "";
	}


	/**
	 *
	 * Retourne l'objet periode correspondant a partir
	 * d'un parametre numerique (numero de periode)
	 * ou d'un parametre qui est deja un objet PeriodeNote (on renvoi le parametre)
	 * ou d'une date DateTime , auquel cas on renvoi la periode de l'epoque ou null si pas de periode trouvee
	 * ou d'un parametre null, auquel cas on renvoi la periode courante
	 *
	 * @param      mixed $periode numeric or PeriodeNote value or DateTime
	 *
	 * @return	PeriodeNote
	 */
	public function getPeriodeNote($periode_param = null) {
	    $result = null;
	    if ($periode_param instanceof DateTime) {
		foreach ($this->getPeriodeNotes() as $periode_temp) {
		    if ($periode_temp->getDateDebut('U') <= $periode_param->format('U')
			    && ($periode_temp->getDateFin(null) === null || $periode_temp->getDateFin('U') > $periode_param->format('U')))
		    {
			$result = $periode_temp;
			break;
		    }
		}
	    } else if ($periode_param === null) {
		$periode = $this->getPeriodeNoteOuverte();
		if ($periode == null) {
			$now = new DateTime('now');
			$result = $this->getPeriodeNote($now);
		}
	    } else if (is_numeric($periode_param)) {
		foreach ($this->getPeriodeNotes() as $periode_temp) {
		    if ($periode_temp->getNumPeriode() == $periode_param) {
			$result = $periode_temp;
			break;
		    }
		}
	    } else if ($periode_param instanceof PeriodeNote) {
		$result = $periode_param;
	    } else {
		    throw new PropelException('Argument $periode doit etre de type numerique ou une instance de PeriodeNote ou un DateTime.');
	    }
	    return $result;
	}


	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      DateTime $date_debut
	 * @param      DateTime $date_fin
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesAbsence($date_debut=null, $date_fin = null) {
	    $abs_saisie_col = $this->getAbsColDecompteDemiJournee($date_debut, $date_fin);
            return ($this->getDemiJourneesAbsenceParCollection($abs_saisie_col,$date_debut, $date_fin));
        }

	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
         * Il faut en entré une collection de saisies ordonnée par date de debut
	 *
	 * @param      PropelObjectCollection $abs_saisie_col collection de saisies d'absence ordonne par date de début
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesAbsenceParCollection($abs_saisie_col,$date_debut=null, $date_fin=null) {
	    if ($abs_saisie_col->isEmpty()) {
		return new PropelCollection();
	    }
	    
	    //on filtre les saisie qu'on ne veut pas compter
	    $abs_saisie_col_filtre = new PropelCollection();
	    $abs_saisie_col_2 = clone $abs_saisie_col;
	    foreach ($abs_saisie_col as $saisie) {
		if (!$saisie->getRetard() && $saisie->getManquementObligationPresence()) {
		    $contra = false;
		    if (getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
			//on va vérifier si il n'y a pas une saisie contradictoire simultanée
			foreach ($abs_saisie_col_2 as $saisie_contra) {
			    if ($saisie_contra->getId() != $saisie->getId()
				    && $saisie->getDebutAbs('U') >= $saisie_contra->getDebutAbs('U')
				    && $saisie->getFinAbs('U') <= $saisie_contra->getFinAbs('U')
				    && !$saisie_contra->getManquementObligationPresenceSpecifie_NON_PRECISE()
				    //si c'est une saisie specifiquement a non precise c'est du type erreur de saisie on ne la prend pas en compte
				    && ($saisie_contra->getRetard() || !$saisie_contra->getManquementObligationPresence())) {
				$contra = true;
				break;
			    }
			}
		    }
		    if (!$contra) {
			$abs_saisie_col_filtre->append($saisie);
		    }
		}
	    }

	    if ($date_fin != null) {
		$date_fin_iteration = clone $date_fin;
	    } else {
		$date_fin_iteration = new DateTime('now');
		$date_fin_iteration->setTime(23,59);
	    }
            if ($this->getDateSortie() != null && $this->getDateSortie('U') < $date_fin_iteration->format('U')) {
                $date_fin_iteration = $this->getDateSortie(null);
            }

	    require_once("helpers/AbsencesEleveSaisieHelper.php");
	    return AbsencesEleveSaisieHelper::compte_demi_journee($abs_saisie_col_filtre, $date_debut, $date_fin_iteration);
	}

        /**
	 *
	 * Retourne une collection contenant les saisies des absences à prendre en compte dans les decomptes de demi-journées
	 * entre deux dates
	 *
	 * @param      DateTime $date_debut
	 * @param      DateTime $date_fin
	 *
	 * @return PropelCollection  AbsenceEleveSaisie[]
	 */
	private function getAbsColDecompteDemiJournee($date_debut= null, $date_fin= null) {
	    $request_query_hash = 'query_AbsenceEleveSaisieQuery_filterByEleve_'.$this->getIdEleve().'_filterByPlageTemps_deb_';
	    if ($date_debut != null) { $request_query_hash .= $date_debut->format('U');}
	    else {$request_query_hash .= 'null';}
	    $request_query_hash .= '_fin_';
	    if ($date_fin != null) {$request_query_hash .= $date_fin->format('U');}
	    else {$request_query_hash .= 'null';}

	    if (isset($_REQUEST[$request_query_hash]) && $_REQUEST[$request_query_hash] != null) {
		$abs_saisie_col = $_REQUEST[$request_query_hash];
	    } else {
		$abs_saisie_col =  AbsenceEleveSaisieQuery::create()
		    ->filterByEleve($this)
		    ->filterByPlageTemps($date_debut, $date_fin)
		    ->orderByDebutAbs(Criteria::ASC)
		    ->leftJoinWith('AbsenceEleveSaisie.JTraitementSaisieEleve')
		    ->leftJoinWith('JTraitementSaisieEleve.AbsenceEleveTraitement')
		    ->leftJoinWith('AbsenceEleveTraitement.AbsenceEleveType')
		    ->distinct()
		    ->find();
		$_REQUEST[$request_query_hash] = $abs_saisie_col;
	    }
	    return $abs_saisie_col;
	}

	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesAbsenceParPeriode($periode = null) {
	    $periode_obj = $this->getPeriodeNote($periode);
	    if ($periode_obj == null) {
		return new PropelObjectCollection();
	    }
	    $date_debut = $periode_obj->getDateDebut(null);
	    if ($date_debut  == null)  {
		return new PropelObjectCollection();
	    }

	    return $this->getDemiJourneesAbsence($periode_obj->getDateDebut(null), $periode_obj->getDateFin(null));
	}


  	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence non justifiees
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      DateTime $date_debut
	 * @param      DateTime $date_fin
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesNonJustifieesAbsence($date_debut=null, $date_fin = null) {
	    $abs_saisie_col = $this->getAbsColDecompteDemiJournee($date_debut, $date_fin);
	    return ($this->getDemiJourneesNonJustifieesAbsenceParCollection($abs_saisie_col,$date_debut, $date_fin));
	}
/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence non justifiees
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      PropelObjectCollection $abs_saisie_col collection de saisies d'absence ordonne par date de début
	 * @param      DateTime $date_debut
	 * @param      DateTime $date_fin
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesNonJustifieesAbsenceParCollection($abs_saisie_col,$date_debut=null,$date_fin=null) {
	    if ($abs_saisie_col->isEmpty()) {
		return new PropelCollection();
	    }

	    //on filtre les saisie qu'on ne veut pas compter
	    $abs_saisie_col_filtre = new PropelCollection();
	    $abs_saisie_col_2 = clone $abs_saisie_col;
	    foreach ($abs_saisie_col as $saisie) {
		if (!$saisie->getRetard() && $saisie->getManquementObligationPresence() && !$saisie->getJustifiee()) {
		    $contra = false;
		    if (getSettingValue("abs2_saisie_multi_type_non_justifiee")!='y') {
			//on va vérifier si il n'y a pas une saisie contradictoire simultanée
			foreach ($abs_saisie_col_2 as $saisie_contra) {
			    if ($saisie_contra->getId() != $saisie->getId()
				    && $saisie->getDebutAbs('U') >= $saisie_contra->getDebutAbs('U')
				    && $saisie->getFinAbs('U') <= $saisie_contra->getFinAbs('U')
				    && !$saisie_contra->getManquementObligationPresenceSpecifie_NON_PRECISE()
				    //si c'est une saisie specifiquement a non precise c'est du type erreur de saisie on ne la prend pas en compte
				    && ($saisie_contra->getRetard() || !$saisie_contra->getManquementObligationPresence() || $saisie_contra->getJustifiee())) {
				$contra = true;
				break;
			    }
			}
		    }
		    if (!$contra) {
			$abs_saisie_col_filtre->append($saisie);
		    }
		}
	    }

	    if ($date_fin != null) {
		$date_fin_iteration = clone $date_fin;
	    } else {
		$date_fin_iteration = new DateTime('now');
		$date_fin_iteration->setTime(23,59);
	    }
            if ($this->getDateSortie() != null && $this->getDateSortie('U') < $date_fin_iteration->format('U')) {
                $date_fin_iteration = $this->getDateSortie(null);
            }

	    require_once("helpers/AbsencesEleveSaisieHelper.php");
	    return AbsencesEleveSaisieHelper::compte_demi_journee($abs_saisie_col_filtre, $date_debut, $date_fin_iteration);
	}

 	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les demi journees d'absence non justifiees
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getDemiJourneesNonJustifieesAbsenceParPeriode($periode = null) {
	    $periode_obj = $this->getPeriodeNote($periode);
	    if ($periode_obj == null) {
		return new PropelObjectCollection();
	    }
	    $date_debut = $periode_obj->getDateDebut(null);
	    if ($date_debut  == null)  {
		return new PropelObjectCollection();
	    }
	    return $this->getDemiJourneesNonJustifieesAbsence($periode_obj->getDateDebut(null), $periode_obj->getDateFin(null));
	}


  	/**
	 *
	 * Retourne une collection contenant des saisies comptée comme absence pour le décompte officiel
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelCollection AbsenceEleveSaisie[]
	 */
	public function getRetards($date_debut=null, $date_fin = null) {
	    $abs_saisie_col = $this->getAbsColDecompteDemiJournee($date_debut, $date_fin);
	    if ($abs_saisie_col->isEmpty()) {
			return new PropelCollection();
	    }

	    $result = new PropelCollection();
	    $abs_saisie_col_2 = clone $abs_saisie_col;
	    //on va faire le décompte officiel des retard
	    foreach ($abs_saisie_col as $saisie) {
			if ($saisie->getRetard() && $saisie->getManquementObligationPresence()) {
			    $contra = false;
	    		//on va vérifier si il n'y a pas une saisie contradictoire simultanée
				foreach ($abs_saisie_col_2 as $saisie_contra) {
				    if ($saisie_contra->getId() != $saisie->getId()
					    && $saisie->getDebutAbs('U') >= $saisie_contra->getDebutAbs('U')
					    && $saisie->getFinAbs('U') <= $saisie_contra->getFinAbs('U')
					    && !$saisie_contra->getManquementObligationPresenceSpecifie_NON_PRECISE()) {
					    	if ($saisie_contra->getManquementObligationPresence()) {
					    		//on a une saisie plus large qui est aussi un manquement à l'obligation de présence, donc on ne compte pas celle qui est englobée
								$contra = true;
								break;
					    	} else if (getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
					    		//on a une saisie plus large qui est comptée comme présente, donc on ne compte pas celle la qui est englobée
								$contra = true;
								break;
					    	}
					}
			    }
			    if (!$contra) {
					$result->append($saisie);
			    }
			}
	    }
	    return $result;
	}

 	/**
	 *
	 * Retourne une collection contenant sous forme de DateTime les retards (saisies d'absences inferieures a 30min ou autre suivant reglage de l'admin)
	 * Un DateTime le 23/05/2010 à 00:00 signifie que l'eleve a ete saisie absent le 23/05/2010 au matin
	 * Pour l'apres midi la date est 23/05/2010 à 12:30
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelCollection DateTime[]
	 */
	public function getRetardsParPeriode($periode = null) {
	    $periode_obj = $this->getPeriodeNote($periode);
	    if ($periode_obj == null) {
		return new PropelObjectCollection();
	    }
	    $date_debut = $periode_obj->getDateDebut(null);
	    if ($date_debut  == null)  {
		return new PropelObjectCollection();
	    }
	    return $this->getRetards($periode_obj->getDateDebut(null), $periode_obj->getDateFin(null));
	}

	/**
	 *
	 * Retourne la liste de toutes les période de notes pour lesquelles l'eleve a ete affecte
	 *
	 *
	 * @return PropelObjectCollection PeriodeNote[]
	 */
	public function getPeriodeNotes() {
	    if(null === $this->collPeriodeNotes) {
		    if ($this->isNew() && null === $this->collPeriodeNotes) {
			    // return empty collection
			    $this->initPeriodeNotes();
		    } else {
			    $sql = "SELECT /* log pour sql manuel */ DISTINCT periodes.NOM_PERIODE, periodes.NUM_PERIODE, periodes.VEROUILLER, periodes.ID_CLASSE, periodes.DATE_VERROUILLAGE, periodes.DATE_FIN FROM `periodes` INNER JOIN classes ON (periodes.ID_CLASSE=classes.ID) INNER JOIN j_eleves_classes ON (classes.ID=j_eleves_classes.ID_CLASSE) WHERE j_eleves_classes.LOGIN='".$this->getLogin()."' AND j_eleves_classes.periode = periodes.num_periode";
			    $con = Propel::getConnection(PeriodeNotePeer::DATABASE_NAME, Propel::CONNECTION_READ);
			    $stmt = $con->prepare($sql);
			    $stmt->execute();

			    $formatter = new PropelObjectFormatter();
			    $formatter->setDbName(PeriodeNotePeer::DATABASE_NAME);
			    $formatter->setClass('PeriodeNote');
			    $formatter->setPeer('PeriodeNotePeer');
			    $formatter->setAsColumns(array());
			    $formatter->setHasLimit(false);
			    $this->collPeriodeNotes = $formatter->format($stmt);
			    
//			    $collPeriodeNotes = PeriodeNoteQuery::create()->useClasseQuery()->useJEleveClasseQuery()->filterByEleve($this)->endUse()->endUse()
//				    ->where('j_eleves_classes.periode = periodes.num_periode')
//				    ->setComment('log pour sql manuel')
//				    ->distinct()->find();
//			    $this->collPeriodeNotes = $collPeriodeNotes;
		    }
	    }
	    return $this->collPeriodeNotes;
	}


	/**
	 *
	 * Renvoi une collection de saisies qui montrent un manquement à l'obligation de présence.
         * Une saisie qui est contré par une saisie de présence n'est pas retournée.
	 *
	 * @param      DateTime $dateDebut
	 * @param      DateTime $dateFin
	 * @return     PropelObjectCollection
	 *
	 */
	public function  getAbsenceEleveSaisiesManquementObligationPresence($dateDebut = null, $dateFin = null) {
 	    $abs_saisie_col = $this->getAbsenceEleveSaisiesFilterByDate($dateDebut, $dateFin);
	    //on filtre les saisie qu'on ne veut pas compter
	    $abs_saisie_col_filtre = new PropelCollection();
	    $abs_saisie_col_2 = clone $abs_saisie_col;
	    foreach ($abs_saisie_col as $saisie) {
		if ($saisie->getManquementObligationPresence()) {
		    $contra = false;
		    if (getSettingValue("abs2_saisie_multi_type_non_justifiee")!='y') {
			//on va vérifier si il n'y a pas une saisie contradictoire simultanée
			foreach ($abs_saisie_col_2 as $saisie_contra) {
			    if ($saisie_contra->getId() != $saisie->getId()
				    && $saisie->getDebutAbs('U') >= $saisie_contra->getDebutAbs('U')
				    && $saisie->getFinAbs('U') <= $saisie_contra->getFinAbs('U')
				    && !$saisie_contra->getManquementObligationPresenceSpecifie_NON_PRECISE()
				    //si c'est une saisie specifiquement a non precise c'est du type erreur de saisie on ne la prend pas en compte
				    && (!$saisie_contra->getManquementObligationPresence())) {
				$contra = true;
				break;
			    }
			}
		    }
		    if (!$contra) {
                        //on a une saisie qui est en manquement et qui n'est pas contrée
			$abs_saisie_col_filtre->append($saisie);
		    }
		}
	    }
            return $abs_saisie_col_filtre;
        }

   	/**
	 *
	 * Retourne une liste d'absence qui montrent un manquement à l'obligation de présence pour le creneau et le jour donné.
	 *
	 * @param      EdtCreneau $edtcreneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 *
 	 * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesManquementObligationPresenceDuCreneau($edtcreneau = null, $v = 'now') {
	    if ($edtcreneau == null) {
		$edtcreneau = EdtCreneauPeer::retrieveEdtCreneauActuel($v);
	    }

	    if (!($edtcreneau instanceof EdtCreneau)) {
		throw new PropelException('Le premier argument doit etre de la classe EdtCreneau');
	    }

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

	    $dt->setTime($edtcreneau->getHeuredebutDefiniePeriode('H'), $edtcreneau->getHeuredebutDefiniePeriode('i'), 0);
	    $dt_fin_creneau = clone $dt;
	    $dt_fin_creneau->setTime($edtcreneau->getHeurefinDefiniePeriode('H'), $edtcreneau->getHeurefinDefiniePeriode('i'), 0);

	    return $this->getAbsenceEleveSaisiesManquementObligationPresence($dt, $dt_fin_creneau);
	}

        /**
	 *
	 * Renvoi true / false selon que l'eleve est present a l'heure donnee.
	 * On ne peut certifier la presence a 100% vu que seule les absences sont saisies (et non les presences)
	 * La fonction va rechercher les saisies de la classe de l'eleve et verifier que l'eleve n'est pas dedans.
	 * Les absences prisent en compte sont celles pour lesquelles l'eleve n'est pas sous la responsabilité de l'établissement et ne respecte pas son obligetion de presence
	 * Il est possible que l'eleve n'ai pas cours a l'heure precisee, auquel cas la fonction renvoi faux (eleve non present)
	 * Des plusieurs saisies sont contradictoire, on considere l'eleve present
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Boolean
	 *
	 */
	public function getSousResponsabiliteEtablissement($v = 'now') {
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
	    

	    //premierement on verifie que l'eleve n'a pas ete saisie absent a cette date
	    $resp_etab = true;
	    foreach ($this->getAbsenceEleveSaisiesFilterByDate($dt,$dt) as $saisie) {
		if ($saisie->getSousResponsabiliteEtablissement()) {
		    return true;
		} else {
		    $resp_etab = false;
		}
	    }
	    if (!$resp_etab) {
		//l'eleve est saisie mais absent
		return false;
	    }

	    //on recupere toute les saisies a cette heure
	    //optimisation : utiliser la requete pour stocker ca
	    if (isset($_REQUEST['query_AbsenceEleveSaisieQuery_getSousResponsabiliteEtablissement_'.$dt->format('U')])
		    && $_REQUEST['query_AbsenceEleveSaisieQuery_getSousResponsabiliteEtablissement_'.$dt->format('U')] != null) {
		$saisie_col = $_REQUEST['query_AbsenceEleveSaisieQuery_getSousResponsabiliteEtablissement_'.$dt->format('U')];
	    } else {
		$saisie_col = AbsenceEleveSaisieQuery::create()
		    ->filterByPlageTemps($dt, $dt)
		    ->find();
		$_REQUEST['query_AbsenceEleveSaisieQuery_getSousResponsabiliteEtablissement_'.$dt->format('U')] = $saisie_col;
	    }

	    if ($saisie_col->isEmpty()) {
		//rien n'a ete saisie (aucun cours a cette heure), en renvoi non present par defaut
		return false;
	    }

	    $periode = $this->getPeriodeNote($dt);

	    //on va verifier les saisie sur l'heure precisee pour les groupes de l'eleve
	    $id_array = $this->getGroupes($periode)->getPrimaryKeys();
	    foreach ($saisie_col as $saisie) {
		if (in_array($saisie->getIdGroupe(), $id_array)) {
		    //il y a une saisie pour la classe mais pas pour l'eleve, il est donc present
		    return true;
		}
	    }

	    //on va verifier les saisie sur l'heure precisee pour les aid de l'eleve
	    $id_array = $this->getAidDetailss()->toKeyValue('Id','Id');
	    if (count($id_array) > 0) {
		foreach ($saisie_col as $saisie) {
		    if (in_array($saisie->getIdAid(), $id_array)) {
			//il y a une saisie pour l'aid mais pas pour l'eleve, il est donc present
			return true;
		    }
		}
	    }

	    //on va verifier les saisie sur l'heure precisee pour la classe de l'eleve
	    foreach ($saisie_col as $saisie) {
		if ($this->getClasse($periode) != null && $saisie->getIdClasse() == $this->getClasse($periode)->getId()) {
		    //il y a une saisie pour la classe mais pas pour l'eleve, il est donc present
		    return true;
		}
	    }

	    //rien n'a ete saisie (aucun cours a cette heure), en renvoi non present par defaut
	    return false;
	}

	/**
	 *
	 * Hydrate la collection des pÃ©riodes de notes (il faut une requete adÃ©quate : EleveQuery->joinWithPeriodeNotes()
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Boolean
	 *
	 */
	public function hydratePeriodeNotes() {
	    $this->initPeriodeNotes();
	    foreach ($this->getJEleveClasses() as $JEleveClasses) {
		if ($JEleveClasses->getClasse() != null && $JEleveClasses->getClasse()->getProtectedCollPeriodeNote() != null) {
		    foreach ($JEleveClasses->getClasse()->getProtectedCollPeriodeNote() as $periode_note) {
			if ($periode_note->getNumPeriode() == $JEleveClasses->getPeriode()) {
			    $this->collPeriodeNotes->add($periode_note);
			    break;
			}
		    }
		}
	    }
	}

	/**
	 *
	 * Mets à jour la table d'agrégation des absences pour cet élève
	 * @TODO		implement the method
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 * @return		Boolean
	 *
	 */
	public function checkSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		throw new Exception('Not fully tested');
		$debug = false;
		
		if ($debug) {
			print_r('<br/>Vérification pour l eleve '.$this->getIdEleve().'<br/>');
		}
		$dateDebutClone = null;
		$dateFinClone = null;
		
		//on initialise les date clone qui seront manipulés dans l'algoritme, c'est nécessaire pour ne pas modifier les date passée en paramêtre.
		if ($dateDebut != null) {
			if ($debug) {
				print_r('Date début '.$dateDebut->format('Y-m-d').'<br/>');
			}
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->setTime(0,0);
		}
		if ($dateFin != null) {
			if ($debug) {
				print_r('Date fin '.$dateFin->format('Y-m-d').'<br/>');
			}
			$dateFinClone = clone $dateFin;
			$dateFinClone->setTime(23,59);
		}
		
		//on vérifie en comparant des dates que aucune mise a jour de la table d'agrégation n'a été oubliée 
		//on va rechercher la date de dernière modification des saisies, traitements, etc...
		$date_saisies_selection = ' 1=1 ';
		$date_agregation_selection = ' 1=1 ';
		if ($dateDebutClone != null) {
			$date_saisies_selection .= ' and a_saisies.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
		}
		if ($dateFinClone != null) {
			$date_saisies_selection .= ' and a_saisies.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
		}
		
		/* on va récupéré trois informations en base de donnée :
		 * - est-ce qu'il y a bien le marqueur de fin de calcul (entrée avec a_agregation_decompte.DATE_DEMI_JOUNEE IS NULL)
		 * - est-ce que la date updated_at de mise à jour de la table est bien postérieure aux date de modification des saisies et autres entrées
		 * - on va compter le nombre de demi journée, elle doivent être toutes remplies
		 */
		//$query = 'select ELEVE_ID is not null, union_date <= as updated_at, count_demi_jounee
		$query = 'select ELEVE_ID is not null as marqueur_calcul, union_date, updated_at, count_demi_jounee, count_manquement
		
		FROM
			(SELECT  a_agregation_decompte.ELEVE_ID from  a_agregation_decompte WHERE a_agregation_decompte.ELEVE_ID='.$this->getIdEleve().' AND a_agregation_decompte.DATE_DEMI_JOUNEE IS NULL
			) as a_agregation_decompte_null_select
			
		LEFT JOIN (
			(SELECT updated_at 
			FROM a_agregation_decompte WHERE a_agregation_decompte.eleve_id='.$this->getIdEleve().' and '.$date_agregation_selection.'	
			ORDER BY updated_at DESC LIMIT 1) as updated_at_select
		) ON 1=1

		LEFT JOIN (';
		if ($dateDebutClone != null && $dateFinClone != null) {
			$query .= '
			(SELECT count(*) as count_demi_jounee from  a_agregation_decompte WHERE a_agregation_decompte.ELEVE_ID='.$this->getIdEleve().' and '.$date_agregation_selection;
		} else {
			$query .= '
			(SELECT -1 as count_demi_jounee from  a_agregation_decompte limit 1';
		}
			$query .= '
			) as count_select
		) ON 1=1
		
		LEFT JOIN (
			(SELECT count(*) as count_manquement from  a_agregation_decompte 
			WHERE a_agregation_decompte.ELEVE_ID='.$this->getIdEleve().' and '.$date_agregation_selection.'
			AND manquement_obligation_presence=1
			) as count_select_manquement
		) ON 1=1
		
		LEFT JOIN (
			(SELECT union_date from 
				(SELECT updated_at as union_date FROM a_saisies WHERE a_saisies.deleted_at is null and eleve_id='.$this->getIdEleve().' and '.$date_saisies_selection.'
				UNION ALL
					SELECT deleted_at as union_date  FROM a_saisies WHERE a_saisies.deleted_at is not null and eleve_id='.$this->getIdEleve().' and '.$date_saisies_selection.'
				UNION ALL
					SELECT a_traitements.updated_at as union_date  FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE  a_traitements.deleted_at is null and a_saisies.deleted_at is null and a_saisies.eleve_id='.$this->getIdEleve().' and '.$date_saisies_selection.'
				UNION ALL
					SELECT a_traitements.deleted_at as union_date  FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE a_traitements.deleted_at is not null and a_saisies.deleted_at is null and a_saisies.eleve_id='.$this->getIdEleve().' and '.$date_saisies_selection.'
				
				ORDER BY union_date DESC LIMIT 1
				) AS union_date_union_all_select
			) AS union_date_select
		) ON 1=1;';
			
		$result_query = mysql_query($query);
		if ($result_query === false) {
			echo 'Erreur sur la requete : '.$query.'<br/>'.mysql_error().'<br/>';
			return false;
		}
		$row = mysql_fetch_array($result_query, MYSQL_ASSOC);
		if ($debug) {
			print_r($row);
			print_r('<br/>');
		}
		mysql_free_result($result_query);
		if (!$row['marqueur_calcul']) {//si il n'y a pas le marqueur de calcul fini, on retourne faux
			if ($debug) {
				print_r('faux : Pas de marqueur de fin de calcul<br/>');
			}
			return false;
		} else if ($row['union_date'] && (!$row['updated_at'] || $row['union_date'] > $row['updated_at'])){//si on a pas de updated_at dans la table d'agrégation, ou si la date de mise à jour des saisies est postérieure à updated_at ou 
			if ($debug) {
				print_r('faux : Date de mise a jour antérieur aux dates de saisies<br/>');
			}
			return false;
		} else if ($row['count_demi_jounee']==-1){
			return true;//on ne vérifie pas le nombre d'entrée car les dates ne sont pas précisée
		} else {
			$nbre_demi_journees=(int)(($dateFinClone->format('U')+3600*6-$dateDebutClone->format('U'))/(3600*12)); // on compte les tranches de 12h
            //on ajoute une heure à la date de fin pour dépasser 23:59:59 et bien dépasser la tranche de 00:00
            //si on a un debut à 00:00 et une fin la même journée à 23:59, en ajoutant une heure à la fin on a largement deux tranches de 12h completes
            //donc bien deux demi journées de décomptées
            if ($row['count_demi_jounee'] == $nbre_demi_journees) {
				return true;
            } else {
            	if ($debug) {
	            	print_r('faux : $nbre_demi_journees dans la table : '.$row['count_demi_jounee'].'<br/>');
	            	print_r('$nbre_demi_journees calculé : '.$nbre_demi_journees.'<br/>');
            	}
            	return false;
            }
		}
	}
	
	/**
	 *
	 * Mets à jour la table d'agrégation des absences pour cet élève
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 *
	 */
	public function updateAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		throw new Exception('Not fully tested');
		
		$dateDebutClone = null;
		$dateFinClone = null;
		
		if ($dateDebut != null && $dateFin != null && $dateDebut->format('U') > $dateFin->format('U')) {
			throw new PropelException('Erreur: la date de debut ne peut être postérieure à la date de fin');
		}
		
		//on initialise les date clone qui seront manipulés dans l'algoritme, c'est nécessaire pour ne pas modifier les date passée en paramêtre.
		if ($dateDebut != null) {
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->setTime(0,0);
		}
		if ($dateFin != null) {
			$dateFinClone = clone $dateFin;
			$dateFinClone->setTime(23,59);
		}
		
		
		//on commence par supprimer les anciennes entrée
		$queryDelete = AbsenceAgregationDecompteQuery::create()->filterByEleve($this);
		if ($dateDebutClone != null) {
			$queryDelete->filterByDateDemiJounee($dateDebutClone, Criteria::GREATER_EQUAL);
		}
		if ($dateFinClone != null) {
			$queryDelete->filterByDateDemiJounee($dateFinClone, Criteria::LESS_EQUAL);
		}
		$queryDelete->delete();
		
		//on supprime le marqueur qui certifie que le calcul pour cet eleve a été terminé correctement
		AbsenceAgregationDecompteQuery::create()->filterByEleve($this)->filterByDateDemiJounee(null)->_or()->filterByDateDemiJounee('0000-00-00 00:00:00')->delete();
		
		$DMabsenceNonJustifiesCol = $this->getDemiJourneesNonJustifieesAbsence($dateDebutClone,$dateFinClone);
		$DMabsencesCol			= $this->getDemiJourneesAbsence($dateDebutClone,$dateFinClone);
		$retards				= $this->getRetards($dateDebutClone,$dateFinClone);
		$saisiesCol				= clone $this->getAbsColDecompteDemiJournee($dateDebutClone, $dateFinClone);//cette collection de saisie va nous permettre de récupérer les notifications et les motifs
				
		// préférence admin pour la demi journée
	    $heure_demi_journee = 11;
	    $minute_demi_journee = 50;
	    try {
			$dt_demi_journee = new DateTime(getSettingValue("abs2_heure_demi_journee"));
			$heure_demi_journee = $dt_demi_journee->format('H');
			$minute_demi_journee = $dt_demi_journee->format('i');
	    } catch (Exception $x) {
	    }
	    
	    //on initialise le début de l'itération pour creer les entrées si aucune date n'est précisée
		if ($dateDebutClone == null) {
			if (!$DMabsencesCol->isEmpty()) {
				$dateDebutClone= clone $DMabsencesCol->getFirst(null);
				$dateDebutClone->setTime(0,0);
			}
			if (!$retards->isEmpty()) {
				if ($dateDebutClone == null || $dateDebutClone->format('U') > $retards->getFirst()->getDebutAbs('U')) {
					$dateDebutClone= clone $retards->getFirst()->getDebutAbs(null);
					$dateDebutClone->setTime(0,0);
				}
			}
		}
		if ($dateDebutClone == null) {
			//rien à remplir
			//on va quand même mettre une entrée pour dire qu'on est passé par la pour une vérification ultérieures
			$newAgregation = new AbsenceAgregationDecompte();
			$newAgregation->setEleve($this);
			if ($dateFinClone != null) {
				$dateFinClone->setTime(12,0);
			} else {
				//on a aucune date ni aucune saisie, on va mettre la date du jour
				$dateFinClone = new DateTime('now');
				$dateFinClone->setTime(0,0);
			}
			$newAgregation->setDateDemiJounee($dateFinClone);
			$newAgregation->save();
		} else {
			$dateDemiJourneeIteration = clone $dateDebutClone;
			$DMabsencesCol_start_compute = false;//obligatoire pour tester la fin de la collection car le pointeur retourne au début
			$retards_start_compute = false;
			//on va creer une collections d'entrées dans la table d'agrégation
			//dans la boucle while on utilise les tests isFirst pour vérifier qu'on a pas fini les collections et qu'on est pas retourné au débxyut
			do {
				$newAgregation = new AbsenceAgregationDecompte();
				$newAgregation->setEleve($this);
				$newAgregation->setDateDemiJounee($dateDemiJourneeIteration);
				if (($DMabsencesCol->getCurrent() != null) && $dateDemiJourneeIteration->format('d/m/Y H') == $DMabsencesCol->getCurrent()->format('d/m/Y H')) {
					$DMabsencesCol_start_compute = true;
					$newAgregation->setManquementObligationPresence(true);
					$newAgregation->setJustifiee(true);
					$DMabsencesCol->getNext();
					//on regarde si l'absence est non justifiée
					if (($DMabsenceNonJustifiesCol->getCurrent() != null) && $dateDemiJourneeIteration->format('d/m/Y H') == $DMabsenceNonJustifiesCol->getCurrent()->format('d/m/Y H')) {
						$newAgregation->setJustifiee(false);
						$DMabsenceNonJustifiesCol->getNext();
					}
					
					//on va voir si il y a eu des motifs et des notifications
					$date_debut_cherche_motif = clone $dateDemiJourneeIteration;
					$date_fin_cherche_motif = clone $dateDemiJourneeIteration;
					if ($dateDemiJourneeIteration->format('H') == 0) {
						$date_debut_cherche_motif->setTime(0,0);
						$date_fin_cherche_motif->setTime($heure_demi_journee,$minute_demi_journee);
					} else {
						$date_debut_cherche_motif->setTime($heure_demi_journee,$minute_demi_journee);
						$date_fin_cherche_motif->setTime(23,59);
					}
					foreach ($saisiesCol as $saisie) {
						if ($saisie->getDebutAbs('U') <= $date_fin_cherche_motif->format('U')
						    && $saisie->getFinAbs('U') >= $date_debut_cherche_motif->format('U')
						    && $saisie->getManquementObligationPresence()) {
						    	
					    	if (!$newAgregation->getNotifiee() && $saisie->getNotifiee()) {
					    		$newAgregation->setNotifiee(true);
					    	}
					    	if ($saisie->getMotif() != null) {
					            foreach ($saisie->getAbsenceEleveTraitements() as $traitement) {
					                if ($traitement->getAbsenceEleveMotif() != null) {
					                	$newAgregation->addMotifsAbsence($traitement->getAMotifId());
					                }
					            }
					    	}
					    }
					}
				}
				
				
				//on regarde si il y a des retards pendant cette demijournée
				$date_fin_decompte_retard = clone $dateDemiJourneeIteration;
				if ($date_fin_decompte_retard->format('H') == 0) {
					$date_fin_decompte_retard->setTime($heure_demi_journee,$minute_demi_journee);
				} else {
					$date_fin_decompte_retard->setTime(23,59);
				}
				while ($retards->getCurrent() != null && $retards->getCurrent()->getDebutAbs('U')<$date_fin_decompte_retard->format('U')) {
					$retards_start_compute = true;
					$newAgregation->setNbRetards($newAgregation->getNbRetards() + 1);
					if ($retards->getCurrent()->getJustifiee()) {
						$newAgregation->setNbRetardsJustifies($newAgregation->getNbRetardsJustifies() + 1);
					}
			    	if ($retards->getCurrent()->getMotif() != null) {
			    		foreach ($retards->getCurrent()->getAbsenceEleveTraitements() as $traitement) {
			                if ($traitement->getAbsenceEleveMotif() != null) {
			                	$newAgregation->addMotifsRetard($traitement->getAMotifId());
			                }
			            }
			    	}
					$retards->getNext();
				}
				$newAgregation->save();
				
				$dateDemiJourneeIteration->modify("+12 hours");
			} while (//on s'arrete si on a dépassé la date de fin
					($dateFinClone != null && $dateDemiJourneeIteration->format('U') <= $dateFinClone->format('U'))
					//on s'arrete si la date de fin n'est pas précisé et qu'on a épuisé toutes les absences
					|| ($dateFinClone == null && (!$DMabsencesCol->isFirst() || !$DMabsencesCol_start_compute) && (!$retards->isFirst() || !$retards_start_compute) )
					);
			
		}
		
		
		//on enregistre le marqueur qui certifie que le calcul pour cet eleve a été terminé correctement
		$newAgregation = new AbsenceAgregationDecompte();
		$newAgregation->setEleve($this);
		$newAgregation->setDateDemiJounee(null);
		$newAgregation->save();
	}
	
	/**
	 *
	 * Vérifie et mets à jour l'ensemble de la table d'agrégation des absences pour cet élève, sur l'ensemble des années scolaires incluant $dateDebut et $dateFin,
	 * et aussi avant et après les années scolaires si des saisies sont présentes.
	 * Cela permet de remplir la table obligatoirement pour l'année en cours (avec un mois de débordement sur les autres années), et de la remplir avant et après l'année en cours si des saisies le nécessite
	 * 
	 * @TODO		implement the method
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 * @return		Boolean
	 *
	 */
	public function checkAndUpdateSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		throw new Exception('Not fully tested');
		//on va vérifier que avant et après les dates précisées, la table est bien synchronisée sur l'année en cours
		require_once(dirname(__FILE__)."/../../../helpers/EdtHelper.php");
		assert('$dateDebut == null || $dateFin == null || $dateDebut <= $dateFin');
		
		//on va vérifier antérieurement à la date de début
		if ($dateDebut != null) {
			$dateDebutClone = clone $dateDebut;
			$premier_jour_annee_scolaire_large = EdtHelper::getPremierJourAnneeScolaire($dateDebutClone);
			$premier_jour_annee_scolaire_large->modify("-1 month");
			if ($premier_jour_annee_scolaire_large < $dateDebutClone) {//si l'année débute avant la date précisée, on va faire deux mise à jour, comme ça on est sur que à partir du début de l'année la table sera remplie
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $premier_jour_annee_scolaire_large);
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($premier_jour_annee_scolaire_large, $dateDebutClone);
			} else {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $dateDebutClone);
			}
		} else {//si la date de début est nulle, on prend le début de l'année en cours
			$dateDebutClone = EdtHelper::getPremierJourAnneeScolaire($dateFin);
			$dateDebutClone->modify("-1 month");
			$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $dateDebutClone);
		}
		
		//on va vérifier postérieurement à la date de fin
		if ($dateFin != null) {
			$dateFinClone = clone $dateFin;
			$dernier_jour_annee_scolaire_large = EdtHelper::getDernierJourAnneeScolaire($dateFinClone);
			$dernier_jour_annee_scolaire_large->modify("+1 month");
			if ($dernier_jour_annee_scolaire_large > $dateFinClone) {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, $dernier_jour_annee_scolaire_large);
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dernier_jour_annee_scolaire_large, null);
			} else {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, null);
			}
		} else {//si la date de fin est nulle, on va prendre comme date de fin la fin de l'année
			$dateFinClone = EdtHelper::getDernierJourAnneeScolaire($dateDebut);
			$dateFinClone->modify("+1 month");
			$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, null);
		}
		
		//on regarde sur les dates de début et de fin choisies
		$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateDebutClone, $dateFinClone);
	}
	
	/**
	 *
	 * Mets à jour la table d'agrégation des absences pour cet élève, uniquement entre les dates précisées
	 * Si une des deux date est nulle, la table n'est remplie que si il y a des saisies présente.
	 * Si les deux date ne sont pas nulles, la table est remplie obligatoirement entre les dates précisées, avec des valeurs 0 si nécessaire.
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 *
	 */
	private function thinCheckAndUpdateSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		if (!$this->checkSynchroAbsenceAgregationTable($dateDebut, $dateFin)) {
			$this->updateAbsenceAgregationTable($dateDebut, $dateFin);
		}
	}
	
} // Eleve
