<?php



/**
 * Skeleton subclass for representing a row from the 'classes' table.
 *
 * Classe regroupant des eleves
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class Classe extends BaseClasse {

	/**
	 * Renvoi sous forme d'une collection la liste des groupes d'une classe.
	 *
	 * @return     PropelObjectCollection Classes[]
	 */
	public function getGroupes() {
		$groupes = new PropelObjectCollection();
		if ($this->collJGroupesClassess !== null) {
		    $collJGroupesClasses = $this->collJGroupesClassess;
		} else {
		    $collJGroupesClasses = $this->getJGroupesClassessJoinClasse($con);
		}
		foreach($collJGroupesClasses as $ref) {
		    if ($ref->getGroupe() != null) {
			$groupes->append($ref->getGroupe());
		    }
		}
		return $groupes;
	}

  public function getEctsGroupesByCategories() {
      // On commence par récupérer tous les groupes
      $groupes = $this->getGroupes();
      // Ensuite, il nous faut les catégories.
      $categories = array();
      $c = new Criteria();
      $c->add(JCategoriesMatieresClassesPeer::CLASSE_ID,$this->getId());
      $c->addAscendingOrderByColumn(JCategoriesMatieresClassesPeer::PRIORITY);
      foreach(JCategoriesMatieresClassesPeer::doSelect($c) as $j) {
          $cat = $j->getCategorieMatiere();
          $categories[$cat->getId()] = array(0 => $cat, 1 => array());
      }
      // Maintenant, on mets tout ça ensemble
      foreach($groupes as $groupe) {
          if ($groupe->allowsEctsCredits($this->getId())) {
              $cat = $groupe->getCategorieMatiere($this->getId());
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
	 *
	 * Renvoi sous forme d'une collection la liste des eleves d'une classe. 
	 * Si la periode de note est null, cela renvoi les eleves de la periode actuelle, ou tous les eleves si il n'y a aucune periode actuelle
	 *
	 * @return     PropelObjectCollection Eleves[]
	 *
	 */
	public function getEleves($periode = NULL) {
		if ($periode == NULL) {
		    if ($this->getPeriodeNoteOuverte() != null) {
			$periode = $this->getPeriodeNoteOuverte()->getNumPeriode();
		    }
		}
		$query = EleveQuery::create();
		if ($periode != NULL) {
		    $query->useJEleveClasseQuery()->filterByPeriode($periode)->endUse();
		}
		$query->useJEleveClasseQuery()->filterByClasse($this)->endUse();
		$query->orderByNom()->distinct();
		return $query->find();
	}

	public function getElevesByProfesseurPrincipal($login_prof) {
		$eleves = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JEleveProfesseurPrincipalPeer::PROFESSEUR,$login_prof);
		foreach($this->getJEleveProfesseurPrincipalsJoinEleve($criteria) as $ref) {
		    if ($ref->getEleve() != null) {
			$eleves->add($ref->getEleve());
		    }
		}
		return $eleves;
	}

	/**
	 *
	 * Ajoute un eleve a une classe. Si la periode de note est nulle, cela ajoute l'eleve la periode actuelle
	 *
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
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
		$jEleveClasse = new JEleveClasse();
		$jEleveClasse->setEleve($eleve);
		$jEleveClasse->setPeriode($num_periode_notes);
		$this->addJEleveClasse($jEleveClasse);
		$jEleveClasse->save();
		$eleve->clearPeriodeNotes();
	}

 	/**
	 * Retourne la periode de note actuellement ouverte pour une classe donnee.
	 *
	 * @return     PeriodeNote $periode la periode actuellement ouverte
	 */
	public function getPeriodeNoteOuverte() {
		$count_verrouiller_n = 0;
		$count_verrouiller_p = 0;
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
		    return $periode_verrouiller_n;
		} elseif ($count_verrouiller_n == 0 && $count_verrouiller_p == 1) {
		    //si on a une seule periode partiellement ouverte et aucune ouverte alors c'est la periode actuelle
		    return $periode_verrouiller_p;
		}

		//on verifie si il y a une periode du calendrier avec une periode de note precisee
		$calendrier_periode = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle();
		if ($calendrier_periode != null && $calendrier_periode->getNumeroPeriode() != null && $calendrier_periode->getNumeroPeriode() != 0) {
		    $criteria = new Criteria();
		    $criteria->add(PeriodeNotePeer::NUM_PERIODE,$calendrier_periode->getNumeroPeriode());
		    $periodes = $this->getPeriodeNotes($criteria);
		    return $periodes->getFirst();
		}

		//on va prendre la periode de numero la plus petite non verrouillee
		if (isset($periode_verrouiller_n) && $periode_verrouiller_n != null) {
		    return $periode_verrouiller_n;
		} elseif (isset($periode_verrouiller_p) && $periode_verrouiller_p != null) {
		    return $periode_verrouiller_p;
		}
		return null;
	}
} // Classe
