<?php



/**
 * Skeleton subclass for representing a row from the 'groupes' table.
 *
 * Groupe d'eleves permettant d'y affecter une matiere et un professeurs
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class Groupe extends BaseGroupe {

	/**
	 * The value for DescriptionAvecClasses
	 * @var        string
	 */
	protected $descriptionAvecClasses;

	/**
	 * The value for NameAvecClasses
	 * @var        string
	 */
	protected $nameAvecClasses;


	/**
	 * @var        array Classe[] Collection to store aggregation of Classes objects.
	 */
	protected $collClasses;

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un groupe.
	 * Manually added for N:M relationship
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     PropelObjectCollection Classes[]
	 *
	 */
	public function getClasses($con = null) {
		if(null === $this->collClasses) {
			if ($this->isNew() && null === $this->collClasses) {
				// return empty collection
				$this->initClasses();
			} else {
				$collClasses = new PropelObjectCollection();
				$collClasses->setModel('Classe');
				if ($this->collJGroupesClassess !== null) {
				    $collJGroupesClasses = $this->collJGroupesClassess;
				} else {
				    $collJGroupesClasses = $this->getJGroupesClassessJoinClasse($con);
				}
				foreach($collJGroupesClasses as $ref) {
				    if ($ref != null) {
					$collClasses->append($ref->getClasse());
				    }
				}
				$this->collClasses = $collClasses;
			}
		}
		return $this->collClasses;
	}

	/**
	 * Initializes the collClasses collection.
	 *
	 * @param      integer $periode numero de la periode ou objet periodeNote
	 * @return     void
	 */
	public function initClasses()
	{
		$this->collClasses = new PropelObjectCollection();
		$this->collClasses->setModel('Classe');
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
	    if ($deep) {  // also de-associate any related objects?
		$this->collClasses = null;
		$this->clearJGroupesClassess();	
	    }
	}

	/**
	 * Renvoi la description du groupe avec la liste des classes associÃ©es
	 * @return     String
	 */
	public function getDescriptionAvecClasses() {
		if (isset($this->descriptionAvecClasses) && $this->descriptionAvecClasses != null) {
			return $this->descriptionAvecClasses;
		} else {
			$str = $this->getDescription();
			$str .= "&nbsp;(";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getNom() . ",&nbsp;";
			}
			$str = substr($str, 0, -7);
			$str.= ")";
			$this->descriptionAvecClasses = $str;
			return $str;
		}
	}

	/**
	 * Renvoi le nom du groupe avec la liste des classes associÃ©es
	 * @return     string
	 */
	public function getNameAvecClasses() {
		if (isset($this->nameAvecClasses) && $this->nameAvecClasses != null) {
			return $this->nameAvecClasses;
		} else {
			$str = $this->getName();
			$str .= " - (";
			foreach ($this->getClasses() as $classe) {
				$str .= $classe->getNom();
                                if ($this->getClasses()->isLast()) {
                                    $str .= ")";
                                } else {
                                    $str .= ", ";
                                }
			}
			$this->nameAvecClasses = $str;
			return $str;
		}
	}

	/**
	 * Clears out the collJGroupesClassess collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addJGroupesClassess()
	 */
	public function clearJGroupesClassess()
	{
		parent::clearJGroupesClassess();
		$descriptionAvecClasses = null;
		$nameAvecClasses = null;
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
		$this->clearJGroupesClassess();
		$this->collClasses = null;
	}

	/**
	 * Manually added
	 *
	 * La mÃ©thode renvoi true si le Groupe est affectÃ© Ã  l'utilisateur.
	 *
	 * @param      Utilisateur $utilisateur l'utilisateur Ã  qui appartient le groupe
	 * @return     boolean true si le groupe appartient Ã  l'utilisateur
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function belongsTo($utilisateur) {
		if (!isset($utilisateur) || $utilisateur == null) {
			return false;
		} elseif (!($utilisateur instanceof UtilisateurProfessionnel)) {
			throw new PropelException("L'objet passé n'est pas de la classe Utilisateur");
		} else {
			$group_appartient_utilisateur = false;
			foreach ($utilisateur->getGroupes() as $group_iter) {
				if ($this->getId() == $group_iter->getId()) {
					$group_appartient_utilisateur = true;
					break;
				}
			}
			return $group_appartient_utilisateur;
		}
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des eles d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Eleves[]
	 *
	 */
	public function getEleves($periode = null) {
		if ($periode === null) {
		    if ($this->getPeriodeNoteOuverte() != null) {
			$periode = $this->getPeriodeNoteOuverte()->getNumPeriode();
		    }
		}
		$query = EleveQuery::create();
		if ($periode !== null) {
		    $query->useJEleveGroupeQuery()->filterByGroupe($this)->filterByPeriode($periode)->endUse();
		} else {
		    $query->useJEleveGroupeQuery()->filterByGroupe($this)->endUse();
		}
		$query->orderByNom()->orderByPrenom()->distinct();
		return $query->find();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des profs d'une classe.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection UtilisateurProfessionel[]
	 *
	 */
	public function getProfesseurs() {
		$profs = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JGroupesProfesseursPeer::ID_GROUPE,$this->getId());
		foreach($this->getJGroupesProfesseurssJoinUtilisateurProfessionnel($criteria) as $ref) {
		    if ($ref != null && $ref->getUtilisateurProfessionnel() != null) {
			$profs->append($ref->getUtilisateurProfessionnel());
		    }
		}
		return $profs;
	}

	/**
	 *
	 * Renvoi sous forme la valeur ECTS par défaut.
	 * Cette valeur se trouve à la jointure d'un groupe et d'une classe, elle n'est pas spécifique à un groupe
	 * En effet, il peut y avoir plusieurs eleves d'une meme classe qui sont regroupés dans un groupe, et pour ces eleves il est possible
	 * que la valeur par defaut soit différente.
	 * @periode integer numero de la periode
	 * @return     array Eleves[]
	 *
	 */
	public function getEctsDefaultValue($id_classe) {
		$criteria = new Criteria();
		$criteria->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($criteria);
		if ($g->isEmpty()) {
		    return null;
		} else {
		    return $g->getFirst()->getValeurEcts();
		}
	}

    public function allowsEctsCredits($id_classe) {
		$c = new Criteria();
		$c->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($c);
		if ($g->isEmpty()) {
		    return false;
		} else {
		    return $g->getFirst()->getSaisieEcts();
		}
    }

	public function getCategorieMatiere($id_classe) {
		$profs = array();
		$criteria = new Criteria();
		$criteria->add(JGroupesClassesPeer::ID_CLASSE,$id_classe);
		$g = $this->getJGroupesClassess($criteria);
		if ($g->isEmpty()) {
		    return false;
		} else {
		    return $g->getFirst()->getCategorieMatiere();
		}
	}

	/**
	 *
	 * Ajoute un eleve a un groupe
	 * Manually added for N:M relationship
	 * It seems that the groupes are passed by values and not by references.
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     array Eleves[]
	 */
	public function addEleve(Eleve $eleve, $num_periode_notes = null) {
		if ($eleve->getIdEleve() == null) {
			throw new PropelException("Eleve id ne doit pas etre null");
		}
		if ($num_periode_notes == null) {
		    $periode = $this->getPeriodeNoteOuverte();
		    if ($periode != null) {
			$num_periode_notes = $periode->getNumPeriode();
		    }
		}
		$jEleveGroupe = new JEleveGroupe();
		$jEleveGroupe->setEleve($eleve);
		$jEleveGroupe->setPeriode($num_periode_notes);
		$this->addJEleveGroupe($jEleveGroupe);
		$jEleveGroupe->save();
		$eleve->clearPeriodeNotes();
	}

	/**
	 *
	 * Retourne tous les emplacements de cours pour la periode précisée du calendrier.
	 * On recupere aussi les emplacements dont la periode n'est pas definie ou vaut 0.
	 *
	 * @return PropelObjectCollection EdtEmplacementCours une collection d'emplacement de cours ordonnée chronologiquement
	 */
	public function getEdtEmplacementCourssPeriodeCalendrierActuelle($v = 'now'){
	    $query = EdtEmplacementCoursQuery::create()->filterByGroupe($this)
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
	 * Retourne la periode de note actuelle pour une classe donnee.
	 *
	 * @return     PeriodeNote $periode la periode actuellement ouverte
	 */
	public function getPeriodeNoteOuverte() {
	    $classes = $this->getClasses();
	    if ($classes->isEmpty()) {
		return null;
	    } else {
		return $classes->getFirst()->getPeriodeNoteOuverte();
	    }
	}


	/**
	 *
	 * Renvoi une collection des mefs des eleves de ce groupe. Un seul mef de chaque type sera retourné.
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Eleves[]
	 *
	 */
	public function getMefs($periode = null) {
            $mef_collection = new PropelObjectCollection();
            foreach($this->getEleves($periode) as $eleve) {
                $mef_collection->add($eleve->getMef());
            }
            return $mef_collection;
        }
} // Groupe
