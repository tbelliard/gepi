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
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     PropelObjectCollection Classes[]
	 *
	 */
    // ERREUR ?? Il ne peut y avoir qu'une seule classe pour un élève pour une période !!
	public function getClasses($periode) {
		$classes = new PropelObjectCollection();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode);
		foreach($this->getJEleveClassesJoinClasse($criteria) as $ref) {
		    if ($ref->getClasse() != NULL) {
			$classes->append($ref->getClasse());
		    }
		}
		return $classes;
	}

    // La méthode ci-dessous, au singulier, corrige le problème ci-dessus.
	public function getClasse($periode) {
		$c = new Criteria();
		$c->add(JEleveClassePeer::PERIODE,$periode);
		$jec = $this->getJEleveClasses($c);
		if ($jec->isEmpty()) {
		    return null;
		} else {
		    return $jec->getFirst()->getClasse();
		}
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
	public function getGroupes($periode) {
		$groupes = new PropelObjectCollection();
		$c = new Criteria();
		$c->add(JEleveGroupePeer::PERIODE,$periode);
		foreach($this->getJEleveGroupesJoinGroupe($c) as $ref) {
			if ($ref->getGroupe() != NULL) {
			    $groupes->append($ref->getGroupe());
			}
		}
		return $groupes;
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
	public function setEctsCredit($periode,$id_groupe,$valeur_ects,$mention_ects) {
        $credit = $this->getEctsCredit($periode,$id_groupe);
        if ($credit == null) {
            $credit = new CreditEcts();
            $credit->setEleve($this);
            $credit->setIdGroupe($id_groupe);
            $credit->setNumPeriode($periode);
        }
        $credit->setValeur($valeur_ects);
        $credit->setMention($mention_ects);
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
	 * Renvoi la liste des cours (sur une semaine) d'un eleve pour
	 * la periode corespondant à la date donnée.
	 * Les cours sont retournés pour tous les types de semaine
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PropelObjectCollection EdtEmplacementCours[]
	 *
	 */
	public function getEdtEmplacementCourssToutTypeDeSemaine($v = 'now') {
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

		$edtEmplacementCourss = new PropelObjectCollection();
		throw new PropelException("Pas encore implemente");
		return $edtEmplacementCourss;
	}

	/**
	 *
	 * Renvoi la liste des cours (sur une semaine) d'un eleve pour
	 * la periode corespondant à la date donnée.
	 * Les cours sont retourné pour tous le type de semaine correspondant à la date donnée
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     PropelObjectCollection EdtEmplacementCours[]
	 *
	 */
	public function getEdtEmplacementCourssSemaineCourante($v = 'now') {
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

		$edtEmplacementCourss = new PropelObjectCollection();
		throw new PropelException("Pas encore implemente");
		return $edtEmplacementCourss;
	}

	/**
	 *
	 * Renvoi la liste du cours se deroulant pendant l'heure specifié, ou null si pas de cours trouvé.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtEmplacementCours
	 *
	 */
	public function getEdtEmplacementCoursActuel($v = 'now') {
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

		$edtEmplacementCours = new EdtEmplacementCours();
		throw new PropelException("Pas encore implemente");
		return $edtEmplacementCours;
	}

	/**
	 *
	 * Renvoi la liste du cours correspondant à un creneau donné, ou null si pas de cours
	 * pendant ce creneau pour la periode de la date precisée
	 *
	 * @param      integer $id_definie_periode La cle primaire de l'objet EdtCreneau
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     EdtEmplacementCours
	 *
	 */
	public function getEdtEmplacementCoursDapresCreneau($id_definie_periode, $v = 'now') {
		$edtEmplacementCours = new EdtEmplacementCours();
		throw new PropelException("Pas encore implemente");
		return $edtEmplacementCours;
	}

} // Eleve
