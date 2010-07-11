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
		foreach($this->getJGroupesClassessJoinGroupe() as $ref) {
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
	 * Si la periode de note est null, cela renvoi les eleves de la priode actuelle, ou tous les eleves si il n'y a aucune periode actuelle
	 *
	 * @return     PropelObjectCollection Eleves[]
	 *
	 */
	public function getEleves($num_periode_notes = null) {
		$eleves = new PropelObjectCollection();
		$criteria = new Criteria();
		if ($num_periode_notes == null) {
        $periode_note = $this->getPeriodeNoteOuverte();
		    if ($periode_note) {
          $num_periode_notes = $periode_note->getNumPeriode();
        }
		}
		if ($num_periode_notes != null) {
		    $criteria->add(JEleveClassePeer::PERIODE,$num_periode_notes);
        $criteria->addAscendingOrderByColumn(ElevePeer::NOM);
		}
		foreach($this->getJEleveClassesJoinEleve($criteria) as $ref) {
		    if ($ref->getEleve() != null) {
			$eleves->add($ref->getEleve());
		    }
		}
		return $eleves;
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
	}

 	/**
	 * Retourne la periode de note actuelle pour une classe donnee.
	 *
	 * @return     PeriodeNote $periode la periode actuellement ouverte
	 */
	public function getPeriodeNoteOuverte($v = 'now') {
		$count_verrouiller_n = 0;
		$count_verrouiller_p = 0;
		foreach ($this->getPeriodeNotes() as $periode) {
		    if ($periode->getVerouiller() == 'N') {
			$count_verrouiller_n = $count_verrouiller_n + 1;
			$periode_verrouiller_n = $periode;
		    }
		    if ($periode->getVerouiller() == 'P') {
			$count_verrouiller_p = $count_verrouiller_p + 1;
			$periode_verrouiller_p = $periode;
		    }
		}
		if ($count_verrouiller_n == 1) {
		    return $periode_verrouiller_n;
		} elseif ($count_verrouiller_n == 0 && $count_verrouiller_p == 1) {
		    return $periode_verrouiller_p;
		}

		$calendrier_periode = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle($v);
		if ($calendrier_periode != null && $calendrier_periode->getNumeroPeriode() != null && $calendrier_periode->getNumeroPeriode() != 0) {
		    $criteria = new Criteria();
		    $criteria->add(PeriodeNotePeer::NUM_PERIODE,$calendrier_periode->getNumeroPeriode());
		    $periodes = $this->getPeriodeNotes($criteria);
		    return $periodes->getFirst();
		}
		return null;
	}
} // Classe
