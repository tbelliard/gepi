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
	 * Il peut y avoir dans le modèle plusieurs classes associés à un élève, mais il faut l'éviter en pratique
	 *
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
	 * @return     PropelObjectCollection Classes[]
	 *
	 */
	public function getClasses($periode = null) {
		if ($periode != null && !is_numeric($periode) &&  !($periode instanceOf PeriodeNote) && !($periode instanceOf DateTime)) {
			throw new PropelException('$periode doit être de type numeric|DateTime|PeriodeNote');
		}
		//on va récupérer le numéro de période $periode_num
		if (is_numeric($periode)) {
			$periode_num = $periode;
		} else {
			if ($periode instanceOf PeriodeNote) {
				$periode_num = $periode->getNumPeriode();
			} else {
				//$periode devrait maintenant être un DateTime (ou null)
				$periode = $this->getPeriodeNote($periode);//on récupère un objet période qui englobe la date
				if ($periode != null) {
					$periode_num = $periode->getNumPeriode();
				} else {
					return new PropelObjectCollection();//si la période est nulle, c'est que aucune classe n'a été assignée pour le paramêtre passé
				}
			}
		}
		
		if(!isset($this->collClasses[$periode_num]) || null === $this->collClasses[$periode_num]) {
			if ($this->isNew() && null === $this->collClasses[$periode_num]) {
				// return empty collection
				$this->initClasses($periode_num);
			} else {
				//on optimise si les jointure sont déjà hydratées, sinon on fait une requete
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
				    if (!isset($this->collClasses[$periode_num]) || $this->collClasses[$periode_num] == null) {
						//rien n'a été trouvé pour cette période, on renvoi une collection vide
						$this->initClasses($periode_num);
				    }
				} else {
				    $query = ClasseQuery::create();
				    $query->useJEleveClasseQuery()->filterByEleve($this)->filterByPeriode($periode_num)->endUse();
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
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
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
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
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
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
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
	protected function initClasses($periode_num)
	{
		$this->collClasses[$periode_num] = new PropelObjectCollection();
		$this->collClasses[$periode_num]->setModel('Classe');
	}

	/**
	 * Initializes the collPeriodeNotes collection.
	 *
	 * @return     void
	 */
	protected function initPeriodeNotes()
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
	protected function initAbsenceEleveSaisiesParJour($date_string)
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
	    $this->collPeriodeNotes = null;
	    $this->collCachePeriodeNotesResult = null;
	    $this->collClasses = null;
	    $this->collGroupes = null;
	    $this->collAbsenceEleveSaisiesParJour = null;
	    $this->periodeNoteOuverte = null;
	    parent::reload($deep,$con);
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
	    $this->clearAbsenceEleveSaisiesParJour($deep);
	    
	    parent::clearAllReferences($deep);
	    if ($deep) {
			if ($this->collPeriodeNotes) {
				foreach ($this->collPeriodeNotes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCachePeriodeNotesResult) {
				foreach ($this->collCachePeriodeNotesResult as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collClasses) {
				foreach ($this->collClasses as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collGroupes) {
				foreach ($this->collGroupes as $o) {
					$o->clearAllReferences($deep);
				}
			}
	    }
	    $this->collPeriodeNotes = null;
	    $this->collCachePeriodeNotesResult = null;
	    $this->collClasses = null;
	    $this->collGroupes = null;
	    $this->collAbsenceEleveSaisiesParJour = null;
	    $this->periodeNoteOuverte = null;
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un eleve pour une période donnée.
	 *
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
	 * @return     PropelObjectCollection Groupes[]
	 *
	 */
	public function getGroupes($periode = null) {
		if ($periode != null && !is_numeric($periode) &&  !($periode instanceOf PeriodeNote) && !($periode instanceOf DateTime)) {
			throw new PropelException('$periode doit être de type numeric|DateTime|PeriodeNote');
		}
		//on va récupérer le numéro de période $periode_num
		if (is_numeric($periode)) {
			$periode_num = $periode;
		} else {
			if ($periode instanceOf PeriodeNote) {
				$periode_num = $periode->getNumPeriode();
			} else {
				//$periode devrait maintenant être un DateTime (ou null)
				$periode = $this->getPeriodeNote($periode);//on récupère un objet période qui englobe la date
				if ($periode != null) {
					$periode_num = $periode->getNumPeriode();
				} else {
					return new PropelObjectCollection();//si la période est nulle, c'est que aucune classe n'a été assignée pour cette élève pour le paramêtre passé
				}
			}
		}
		
		if(!isset($this->collGroupes[$periode_num]) || null === $this->collGroupes[$periode_num]) {
			if ($this->isNew() && null === $this->collGroupes[$periode_num]) {
				// return empty collection
				$this->initGroupes($periode_num);
			} else {
				$query = GroupeQuery::create();
				$query->useJEleveGroupeQuery()
					    ->filterByEleve($this)
					    ->filterByPeriode($periode_num)
					    ->endUse();
				$query->orderByName()->distinct();
				$this->collGroupes[$periode_num] = $query->find();
			}
		}
		return $this->collGroupes[$periode_num];
	}

	/**
	 * Initializes the collGroupes collection.
	 *
	 * @return     void
	 */
	protected function initGroupes($periode_num)
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
	public function clearAbsenceEleveSaisiesParJour($deep = false)
	{
	    $start_string = 'query_AbsenceEleveSaisieQuery_filterByEleve_'.$this->getId().'_filterByPlageTemps_deb_';
	    $start_len = strlen($start_string);
	    foreach($_REQUEST as $key => $value) {
	        if (mb_substr($key,0,$start_len) == $start_string) {
	            unset($_REQUEST[$key]);
	        }
	    }
	    if ($deep) {
			if ($this->collAbsenceEleveSaisiesParJour) {
				foreach ($this->collAbsenceEleveSaisiesParJour as $key => $o) {
				    foreach ($o as $saisie) {
					    $saisie->clearAllReferences($deep);
				    }
				    unset($this->collAbsenceEleveSaisiesParJour[$key]);
				}
			}
	    }
	    $this->collAbsenceEleveSaisiesParJour = null; // important to set this to NULL since that means it is uninitialized
	}

	
	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un eleve pour une période donnée, sous forme d'un table multi-dimensionnel, qui contient les catégories
	 * dans le bon ordre, et les groupes sous chaque catégorie..
	 *
	 * @param      mixed $periode numeric, DateTime ou PeriodeNote
	 * @return     Array
	 *
	 */
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

	    require_once(dirname(__FILE__)."/../../../helpers/EdtEmplacementCoursHelper.php");
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

	    require_once(dirname(__FILE__)."/../../../helpers/EdtEmplacementCoursHelper.php");
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
				//on va prendre la periode de numero la plus grande non verrouillee
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
	 * Retourne une liste de saisies type retard pour le creneau et le jour donné.
	 *
	 * @param      EdtCreneau $edtcreneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 *
         * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getRetardsDuCreneau($edtcreneau = null, $v = 'now') {
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

	    return $this->getRetards($dt, $dt_fin_creneau);
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
	
	
	/**
	 * Renvoie le nom de la photo de l'élève
	 * Renvoie NULL si :
	 * - le module trombinoscope n'est pas activé
	 * - ou bien la photo n'existe pas.
	 * 
	 * @param $arbo : niveau d'aborescence (1 ou 2).
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
		if(file_exists($chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg")) {
			$photo=$chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg";
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

		  if(file_exists($chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg")) {
				$photo=$chemin."../photos/".$repertoire2."eleves/".encode_nom_photo($_elenoet_ou_login).".jpg";
			}
			else {
				if(file_exists($chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",encode_nom_photo($_elenoet_ou_login)).".jpg")) {
					$photo=$chemin."../photos/".$repertoire2."eleves/".sprintf("%05d",encode_nom_photo($_elenoet_ou_login)).".jpg";
				} else {
					for($i=0;$i<5;$i++){
						if(mb_substr(encode_nom_photo($_elenoet_ou_login),$i,1)=="0"){
							$test_photo=mb_substr(encode_nom_photo($_elenoet_ou_login),$i+1);
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
	}


	/**
	 *
	 * Retourne l'objet periode correspondant a partir :
	 * d'un parametre numerique (numero de periode)
	 * ou d'un parametre qui est deja un objet PeriodeNote (on renvoi le parametre sans modification)
	 * ou d'une date DateTime , auquel cas on renvoi la periode de l'epoque ou null si pas de periode trouvee
	 * ou d'un parametre null, auquel cas on renvoi la periode courante (période en cours temporellement), ou la dernière période si aucune période en cours
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
					//break;
			    }
			}
	    } else if ($periode_param === null) {
			$result = $this->getPeriodeNote(new DateTime('now'));//on récupére la période en cours temporellement
		    if ($periode_param == null && $result == null) {//si on a rien précisé, et qu'on ne trouve pas de période en cours, on renvoit la dernière période
		    	return $this->getPeriodeNotes()->getLast();
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
			    $sql = "SELECT /* log pour sql manuel */ DISTINCT periodes.NOM_PERIODE, periodes.NUM_PERIODE, periodes.VEROUILLER, periodes.ID_CLASSE, periodes.DATE_VERROUILLAGE, periodes.DATE_FIN FROM `periodes` INNER JOIN classes ON (periodes.ID_CLASSE=classes.ID) INNER JOIN j_eleves_classes ON (classes.ID=j_eleves_classes.ID_CLASSE) WHERE j_eleves_classes.LOGIN='".$this->getLogin()."' AND j_eleves_classes.periode = periodes.num_periode ORDER by periodes.NUM_PERIODE";
			    $con = Propel::getConnection(null, Propel::CONNECTION_READ);
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
	 * Retourne une collection brute de saisies contenant les absences à prendre en compte dans les decomptes de demi-journées
	 * entre deux dates
	 *
	 * @param      DateTime $date_debut
	 * @param      DateTime $date_fin
	 *
	 * @return PropelCollection  AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesParDate($date_debut= null, $date_fin= null) {
	    $request_query_hash = 'query_AbsenceEleveSaisieQuery_filterByEleve_'.$this->getId().'_filterByPlageTemps_deb_';
	    if ($date_debut != null) { $request_query_hash .= $date_debut->format('U');}
	    else {$request_query_hash .= 'null';}
	    $request_query_hash .= '_fin_';
	    if ($date_fin != null) {$request_query_hash .= $date_fin->format('U');}
	    else {$request_query_hash .= 'null';}

	    if (isset($_REQUEST[$request_query_hash]) && $_REQUEST[$request_query_hash] != null) {
		$abs_saisie_col = $_REQUEST[$request_query_hash];
	    } else {
                if ($date_debut== null && $date_fin== null) {
                    $abs_saisie_col = parent::getAbsenceEleveSaisies();
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
                }
                $_REQUEST[$request_query_hash] = $abs_saisie_col;
	    }
	    return $abs_saisie_col;
	}


        /**
	 *
	 * Renvoi une collection filtrée de saisies qui montrent un manquement à l'obligation de présence pour le décompte des demi-journées.
         * Une saisie qui est contré par une saisie de présence n'est pas dans la liste de retour.
	 *
	 * @param      DateTime $dateDebut
	 * @param      DateTime $dateFin
	 * @param      Boolean $non_justifiee
         * Si $non_justifiee est à false on renvoi toutes les saisies, si c'est à true on ne renvoi que les saisies non justifiée
	 * @return     PropelObjectCollection
	 *
	 */
	public function  getAbsenceEleveSaisiesDecompteDemiJournees($dateDebut = null, $dateFin = null, $non_justifiee = false) {
 	    $abs_saisie_col = $this->getAbsenceEleveSaisiesParDate($dateDebut, $dateFin);
	    //on filtre les saisie qu'on veut comptabiliser
	    $abs_saisie_col_filtre = new PropelCollection();
	    foreach ($abs_saisie_col as $saisie) {
	        //on fait la liste de ce qu'on ne décompte pas
	        if ($saisie->getEleveId() != $this->getId()) continue;
	        if ($saisie->getRetard()) continue;
	        if (!$saisie->getManquementObligationPresence()) continue;
	        if (!$saisie->getManquementObligationPresenceEnglobante()) continue;
	        if ($saisie->getRetardEnglobante()) continue; //on ne compte pas les retard dans le décompte des demi-journées d'absence
	        if ($non_justifiee && $saisie->getJustifiee()) continue;//on demande un décompte des saisies non justifiées
	        if ($non_justifiee && $saisie->getJustifieeEnglobante()) continue;

	        //si on est là c'est qu'il faut décompter cette saisie
	        $abs_saisie_col_filtre->append($saisie);
	    }
        return $abs_saisie_col_filtre;
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
	public function getDemiJourneesAbsence($date_debut = null, $date_fin = null) {
	    $abs_saisie_col_filtrees = $this->getAbsenceEleveSaisiesDecompteDemiJournees($date_debut, $date_fin, $non_justifiee = false);
            if ($date_fin != null) {
                $date_fin_iteration = clone $date_fin;
            } else {
                $date_fin_iteration = null;
            }
            if ($this->getDateSortie() != null && ($date_fin_iteration == null || $this->getDateSortie('U') < $date_fin_iteration->format('U'))) {
                $date_fin_iteration = $this->getDateSortie(null);
                            $date_fin_iteration->modify('-1 minute');
            }

	    require_once(dirname(__FILE__)."/../../../helpers/AbsencesEleveSaisieHelper.php");
	    return AbsencesEleveSaisieHelper::compte_demi_journee($abs_saisie_col_filtrees, $date_debut, $date_fin_iteration);
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
	public function getDemiJourneesNonJustifieesAbsence($date_debut = null, $date_fin = null) {
	    $abs_saisie_col_filtrees = $this->getAbsenceEleveSaisiesDecompteDemiJournees($date_debut, $date_fin, true);

	    if ($date_fin != null) {
		$date_fin_iteration = clone $date_fin;
	    } else {
		$date_fin_iteration = new DateTime('now');
		$date_fin_iteration->setTime(23,59);
	    }
            if ($this->getDateSortie() != null && $this->getDateSortie('U') < $date_fin_iteration->format('U')) {
                $date_fin_iteration = $this->getDateSortie(null);
				$date_fin_iteration->modify('-1 minute');
            }

	    require_once(dirname(__FILE__)."/../../../helpers/AbsencesEleveSaisieHelper.php");
	    return AbsencesEleveSaisieHelper::compte_demi_journee($abs_saisie_col_filtrees, $date_debut, $date_fin_iteration);
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
	 * Retourne une collection contenant des saisies comptée comme retard pour le décompte officiel
	 *
	 * @param      mixed $periode numeric or PeriodeNote value.
	 *
	 * @return PropelCollection AbsenceEleveSaisie[]
	 */
	public function getRetards($date_debut=null, $date_fin = null) {

            if (($date_fin != null) && ($this->getDateSortie() != null && $this->getDateSortie('U') < $date_fin->format('U'))) {
                $date_fin = $this->getDateSortie(null);
            }

            $abs_saisie_col = $this->getAbsenceEleveSaisiesParDate($date_debut, $date_fin);
            if ($abs_saisie_col->isEmpty()) {
                return new PropelCollection();
            }

            $result = new PropelCollection();
            $abs_saisie_englobante = clone $abs_saisie_col;
            //on va faire le décompte officiel des retard
            foreach ($abs_saisie_col as $saisie) {
                if ($saisie->getEleveId() != $this->getId()) {
                    continue;
                }
                if (!$saisie->getRetard() ||
                    !$saisie->getRetardEnglobante() ||
                    !$saisie->getManquementObligationPresence() ||
                    !$saisie->getManquementObligationPresenceEnglobante())
                {
                    //on retire la saisie contrée de la liste de test des saisise possiblement englobante pour optimiser
                    $abs_saisie_englobante->remove($abs_saisie_englobante->search($saisie));
                    continue;
                }

                //on va regarder si il y a un retard plus global, pour n'en compter qu'un seul en non pas deux
                $contra = false;
                foreach ($abs_saisie_englobante as $saisie_contra) {
                    if ($saisie_contra->getEleveId() != $this->getId()) {
                        continue;
                    }
                    if ($saisie_contra->getId() != $saisie->getId()
                            && $saisie->getDebutAbs('U') >= $saisie_contra->getDebutAbs('U')
                            && $saisie->getFinAbs('U') <= $saisie_contra->getFinAbs('U')
                            && !$saisie_contra->getManquementObligationPresenceSpecifie_NON_PRECISE())
                    {
                        //on a une saisie plus large
                        $contra = true;
                        break;
                    }
                }
                if (!$contra) {
                    $result->append($saisie);
                } else {
                    //on retire la saisie contrée de la liste de test des saisise possiblement englobante pour optimiser
                    $abs_saisie_englobante->remove($abs_saisie_englobante->search($saisie));
                }
            }

            //on va enlever les retards qui sont sur des périodes non ouvertes de l'établissement
            require_once(dirname(__FILE__)."/../../../helpers/EdtHelper.php");
            $result_final = new PropelCollection();
            foreach ($result as $saisie) {
                if (EdtHelper::isJourneeOuverte($saisie->getDebutAbs(null))
                    && EdtHelper::isHoraireOuvert($saisie->getDebutAbs(null))) {
                    $result_final->append($saisie);
                }
            }
	    return $result_final;
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
	 * Retourne une liste d'absence qui montrent un manquement à l'obligation de présence pour le creneau et le jour donné.
	 *
	 * @param      EdtCreneau $edtcreneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 *
 	 * @return PropelColection AbsenceEleveSaisie[]
	 */
	public function getAbsenceEleveSaisiesDecompteDemiJourneesDuCreneau($edtcreneau = null, $v = 'now') {
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

	    return $this->getAbsenceEleveSaisiesDecompteDemiJournees($dt, $dt_fin_creneau);
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
	 * Mets à jour la table d'agrégation des absences pour cet élève
	 * @TODO		implement the method
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 * @return		Boolean
	 *
	 */
	public function checkSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		$dateDebutClone = null;
		$dateFinClone = null;
		
		//on initialise les date clone qui seront manipulés dans l'algoritme, c'est nécessaire pour ne pas modifier les date passée en paramêtre.
		if ($dateDebut != null) {
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->setTime(0,0);
		}
		if ($dateFin != null) {
			$dateFinClone = clone $dateFin;
			$dateFinClone->setTime(23,59);
		}
		
		//on vérifie en comparant des dates que aucune mise a jour de la table d'agrégation n'a été oubliée 
		//on va rechercher la date de dernière modification des saisies, traitements, etc...
		$date_saisies_selection = ' 1=1 ';
		$date_saisies_version_selection = ' 1=1 ';
		$date_agregation_selection = ' 1=1 ';
		if ($dateDebutClone != null) {
			$date_saisies_selection .= ' and a_saisies.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_saisies_version_selection .= ' and a_saisies_version.fin_abs >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE >= "'.$dateDebutClone->format('Y-m-d H:i:s').'" ';
		}
		if ($dateFinClone != null) {
			$date_saisies_selection .= ' and a_saisies.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_saisies_version_selection .= ' and a_saisies_version.debut_abs <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
			$date_agregation_selection .= ' and a_agregation_decompte.DATE_DEMI_JOUNEE <= "'.$dateFinClone->format('Y-m-d H:i:s').'" ';
		}
		
		/* on va récupéré trois informations en base de donnée :
		 * - est-ce qu'il y a bien le marqueur de fin de calcul (entrée avec a_agregation_decompte.DATE_DEMI_JOUNEE IS NULL)
		 * - est-ce que la date updated_at de mise à jour de la table est bien postérieure aux date de modification des saisies et autres entrées
		 * - on va compter le nombre de demi journée, elle doivent être toutes remplies
		 */
		//$query = 'select ELEVE_ID is not null, union_date <= as updated_at, count_demi_jounee
		$query = 'select ELEVE_ID is not null as marqueur_calcul, union_date, updated_at, count_demi_jounee, now() as now
		
		FROM
			(SELECT  a_agregation_decompte.ELEVE_ID from  a_agregation_decompte WHERE a_agregation_decompte.ELEVE_ID='.$this->getId().' AND a_agregation_decompte.DATE_DEMI_JOUNEE =\'0001-01-01 00:00:00\'
			) as a_agregation_decompte_null_select
			
		LEFT JOIN (
			(SELECT count(a_agregation_decompte.eleve_id) as count_demi_jounee, max(updated_at) as updated_at
			FROM a_agregation_decompte WHERE a_agregation_decompte.eleve_id='.$this->getId().' and '.$date_agregation_selection.'	
			group by eleve_id) as updated_at_select
		) ON 1=1
		
		LEFT JOIN (
			(SELECT union_date from 
				(	SELECT GREATEST(IFNULL(max(updated_at),CAST(0 as DATETIME)),IFNULL(max(deleted_at),CAST(0 as DATETIME))) as union_date FROM a_saisies WHERE eleve_id='.$this->getId().' and '.$date_saisies_selection.' group by eleve_id
				UNION ALL
					SELECT GREATEST(IFNULL(max(a_saisies_version.updated_at),CAST(0 as DATETIME)),IFNULL(max(a_saisies_version.deleted_at),CAST(0 as DATETIME))) as union_date FROM a_saisies_version WHERE eleve_id='.$this->getId().' and '.$date_saisies_version_selection.' group by eleve_id
				UNION ALL
					SELECT GREATEST(IFNULL(max(a_traitements.updated_at),CAST(0 as DATETIME)),IFNULL(max(a_traitements.deleted_at),CAST(0 as DATETIME))) as union_date  FROM a_traitements join j_traitements_saisies on a_traitements.id = j_traitements_saisies.a_traitement_id join a_saisies on a_saisies.id = j_traitements_saisies.a_saisie_id WHERE a_saisies.eleve_id='.$this->getId().' and '.$date_saisies_selection.' group by eleve_id

				ORDER BY union_date DESC LIMIT 1
				) AS union_date_union_all_select
			) AS union_date_select
		) ON 1=1;';
			
		$result_query = mysql_query($query);
		if ($result_query === false) {
			echo 'Erreur sur la requete : '.mysql_error().'<br/>';
			return false;
		}
		$row = mysql_fetch_array($result_query, MYSQL_ASSOC);
		mysql_free_result($result_query);
		if (!$row['marqueur_calcul']) {//si il n'y a pas le marqueur de calcul fini, on retourne faux
			return false;
		} else if ($row['updated_at'] && $row['updated_at']  > $row['now']) {
			return false;
		} else if ($row['union_date'] && $row['union_date']  > $row['now']) {
			return false;
		} else if ($row['union_date'] && (!$row['updated_at'] || $row['union_date'] > $row['updated_at'])){//si on a pas de updated_at dans la table d'agrégation, ou si la date de mise à jour des saisies est postérieure à updated_at ou 
			return false;
		} else if ($dateDebutClone == null || $dateFinClone == null){
			return true;//on ne vérifie pas le nombre d'entrée car les dates ne sont pas précisée
		} else {
			$nbre_demi_journees=(int)(($dateFinClone->format('U')+3600*6-$dateDebutClone->format('U'))/(3600*12)); // on compte les tranches de 12h
                        //on ajoute une heure à la date de fin pour dépasser 23:59:59 et bien dépasser la tranche de 00:00
                        //si on a un debut à 00:00 et une fin la même journée à 23:59, en ajoutant une heure à la fin on a largement deux tranches de 12h completes
                        //donc bien deux demi journées de décomptées
                        if ($row['count_demi_jounee'] == $nbre_demi_journees) {
                                            return true;
                        } else {
                            return false;
                        }
		}
	}
	
	/**
	 *
	 * Mets à jour la table d'agrégation des absences pour cet élève
	 * Pour éviter des calculs trop long par erreurs, l'algorithme est limité à 3 ans dans le passé et le futur
	 * de la date courante
	 *
	 * @param      DateTime $dateDebut date de début pour la prise en compte de la mise à jours
	 * @param      DateTime $dateFin date de fin pour la prise en compte de la mise à jours
	 *
	 */
	public function updateAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
                $now = new DateTime();
		$dateDebutClone = null;
		$dateFinClone = null;
        
		if ($dateDebut != null && $dateFin != null && $dateDebut->format('U') > $dateFin->format('U')) {
			throw new PropelException('Erreur: la date de debut ne peut être postérieure à la date de fin');
		}
		
		if ($dateDebut != null) {
		    if (abs($dateDebut->format('U') - $now->format('U')) > 3600*24*365*3) {
			    throw new PropelException('Erreur: la date de debut ne doit pas être éloignées de plus de 3 ans de la date courante');
		    }
		}
		if ($dateFin != null) {
		    if (abs($dateFin->format('U') - $now->format('U')) > 3600*24*365*3) {
			    throw new PropelException('Erreur: la date de fin ne doit pas être éloignées de plus de 3 ans de la date courante');
		    }
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
		AbsenceAgregationDecompteQuery::create()->filterByEleve($this)->filterByMarqueurFinMiseAJour()->delete();
		
                $DMabsenceNonJustifiesCol = $this->getDemiJourneesNonJustifieesAbsence($dateDebutClone,$dateFinClone);
		$DMabsencesCol			= $this->getDemiJourneesAbsence($dateDebutClone,$dateFinClone);
		$retards				= $this->getRetards($dateDebutClone,$dateFinClone);
		$saisiesCol				= clone $this->getAbsenceEleveSaisiesParDate($dateDebutClone, $dateFinClone);//cette collection de saisie va nous permettre de récupérer les notifications et les motifs
				
		// préférence admin pour la demi journée
	    $heure_demi_journee = 11;
	    $minute_demi_journee = 50;
	    if (getSettingValue("abs2_heure_demi_journee") != null) {
    	    try {
    			$dt_demi_journee = new DateTime(getSettingValue("abs2_heure_demi_journee"));
    			$heure_demi_journee = $dt_demi_journee->format('H');
    			$minute_demi_journee = $dt_demi_journee->format('i');
    	    } catch (Exception $x) {
    	    }
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
                        if ($dateDebutClone != null && abs($dateDebutClone->format('U') - $now->format('U')) > 3600*24*365*3) {
                                $dateDebutClone = new DateTime('@'.($now->format('U') - 3600*24*365*3));//on limite la mise à jour à 4 ans en arrière
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
			//dans la boucle while on utilise les tests isFirst pour vérifier qu'on a pas fini les collections et qu'on est pas retourné au début
			do {
				$newAgregation = new AbsenceAgregationDecompte();
				$newAgregation->setEleve($this);
				$newAgregation->setDateDemiJounee($dateDemiJourneeIteration);
				if (($DMabsencesCol->getCurrent() != null) && $dateDemiJourneeIteration->format('d/m/Y H') == $DMabsencesCol->getCurrent()->format('d/m/Y H')) {
					$DMabsencesCol_start_compute = true;
					$newAgregation->setManquementObligationPresence(true);
					$newAgregation->setNonJustifiee(false);
					$DMabsencesCol->getNext();
					//on regarde si l'absence est non justifiée
					if (($DMabsenceNonJustifiesCol->getCurrent() != null) && $dateDemiJourneeIteration->format('d/m/Y H') == $DMabsenceNonJustifiesCol->getCurrent()->format('d/m/Y H')) {
						$newAgregation->setNonJustifiee(true);
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
					$newAgregation->setRetards($newAgregation->getRetards() + 1);
					if (!$retards->getCurrent()->getJustifiee()) {
						$newAgregation->setRetardsNonJustifies($newAgregation->getRetardsNonJustifies() + 1);
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
				
			} while (       ($dateDemiJourneeIteration->format('U') - $now->format('U') < 3600*24*365*3) //on continue si on est pas trop éloigné dans le futur
					&& (($dateFinClone != null && $dateDemiJourneeIteration <= $dateFinClone)//on continue si on a pas dépassé la date de fin
					   || ($dateFinClone == null && ((!$DMabsencesCol->isFirst() || !$DMabsencesCol_start_compute) || (!$retards->isFirst() || !$retards_start_compute)) ))//ou continue si la date de fin n'est pas précisé et qu'on a pas encore épuisé toutes les absences et retards
				);
		}
		
		
		//on enregistre le marqueur qui certifie que le calcul pour cet eleve a été terminé correctement
		$newAgregation = new AbsenceAgregationDecompte();
		$newAgregation->setEleve($this);
		$newAgregation->setDateDemiJounee('0001-01-01 00:00:00');
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
		//on va vérifier que avant et après les dates précisées, la table est bien synchronisée sur l'année en cours
		require_once(dirname(__FILE__)."/../../../helpers/EdtHelper.php");
		assert('$dateDebut == null || $dateFin == null || $dateDebut <= $dateFin');
		
		//on va vérifier antérieurement à la date de début
		if ($dateDebut != null) {
			$dateDebutClone = clone $dateDebut;
			$dateDebutClone->modify("-1 day");
			$premier_jour_annee_scolaire_large = EdtHelper::getPremierJourAnneeScolaire($dateDebutClone);
			$premier_jour_annee_scolaire_large->modify("-1 month");//on enleve 1 mois pour etre large
			if ($premier_jour_annee_scolaire_large < $dateDebutClone) {//si l'année débute avant la date précisée, on va faire deux mise à jour, comme ça on est sur que à partir du début de l'année la table sera remplie
				$premier_jour_annee_scolaire_large->modify("-1 day");//on évite aux dates de se chevaucher sur une même journée
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $premier_jour_annee_scolaire_large);
				$premier_jour_annee_scolaire_large->modify("+1 day");
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($premier_jour_annee_scolaire_large, $dateDebutClone);
			} else {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $dateDebutClone);
			}
		} else {//si la date de début est nulle, on prend le début de l'année en cours
			$dateDebutClone = EdtHelper::getPremierJourAnneeScolaire($dateFin);
			$dateDebutClone->modify("-1 month");
			$dateDebutClone->modify("-1 day");
			$this->thinCheckAndUpdateSynchroAbsenceAgregationTable(null, $dateDebutClone);
		}
		
		//on va vérifier postérieurement à la date de fin
		if ($dateFin != null) {
			$dateFinClone = clone $dateFin;
			$dateFinClone->modify("+1 day");
			$dernier_jour_annee_scolaire_large = EdtHelper::getDernierJourAnneeScolaire($dateFinClone);
			$dernier_jour_annee_scolaire_large->modify("+1 month");
			if ($dernier_jour_annee_scolaire_large > $dateFinClone) {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, $dernier_jour_annee_scolaire_large);
				$dernier_jour_annee_scolaire_large->modify("+1 day");
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dernier_jour_annee_scolaire_large, null);
			} else {
				$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, null);
			}
		} else {//si la date de fin est nulle, on va prendre comme date de fin la fin de l'année
			$dateFinClone = EdtHelper::getDernierJourAnneeScolaire($dateDebut);
			$dateFinClone->modify("+1 month");
			$dateFinClone->modify("+1 day");
			$this->thinCheckAndUpdateSynchroAbsenceAgregationTable($dateFinClone, null);
		}
		
		//on regarde sur les dates de début et de fin choisies
		//les dates ont été décalé pour les vérification antérieures et postérieures, donc on rétabli les bonnes dates
		$dateDebutClone->modify("+1 day");
		$dateFinClone->modify("-1 day");
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
	public function thinCheckAndUpdateSynchroAbsenceAgregationTable(DateTime $dateDebut = null, DateTime $dateFin = null) {
		if (!$this->checkSynchroAbsenceAgregationTable($dateDebut, $dateFin)) {
			$this->updateAbsenceAgregationTable($dateDebut, $dateFin);
		}
	}
	
	
    /**
	 *
	 * Renvoi true/false selon que l'élève est sorti ou non de l'établissement (désinscription)
	 *
	 * @param DateTime $date_debut_test date de fin pour le test de sortie de l'élève. Si null, la date courrante est utilisée
	 * @return Boolean
	 *
	 */
    public function isEleveSorti($date_debut_test = null) {
        if ($date_debut_test == null) {
            $date_debut_test = new DateTime('now');
        }
        $date_sortie_eleve = $this->getDateSortie();            
        if (is_null($date_sortie_eleve) || $date_sortie_eleve == 0) {
            return false;
        } else {
            if ($date_debut_test > $date_sortie_eleve) {
                return(true);
            } else {
                return(false);
            }
        }
    }
	
    public function setDateSortie($v) {
		parent::setDateSortie($v);
                $this->updateAbsenceAgregationTable();
	}
    
	/**
	 * Gets a single EleveRegimeDoublant object, which is related to this object by a one-to-one relationship.
         * Override because of a bug.
	 *
	 * @param      PropelPDO $con optional connection object
	 * @return     EleveRegimeDoublant
	 * @throws     PropelException
	 */
	public function getEleveRegimeDoublant(PropelPDO $con = null)
	{

		if ($this->singleEleveRegimeDoublant === null && !$this->isNew()) {
			$this->singleEleveRegimeDoublant = EleveRegimeDoublantQuery::create()->findPk($this->getLogin(), $con);
		}

		return $this->singleEleveRegimeDoublant;
	}
	
} // Eleve
