<?php

require 'gepi/om/BaseEleve.php';


/**
 * Skeleton subclass for representing a row from the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class Eleve extends BaseEleve {

	/**
	 * Initializes internal state of Eleve object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des classes d'un eleves.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Classes[]
	 *
	 */
    // ERREUR ?? Il ne peut y avoir qu'une seule classe pour un élève pour une période !!
	public function getClasses($periode) {
		$classes = array();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode);
		foreach($this->getJEleveClassesJoinClasse($criteria) as $ref) {
			$classes[] = $ref->getClasse();
		}
		return $classes;
	}

    // La méthode ci-dessous, au singulier, corrige le problème ci-dessus.
	public function getClasse($periode) {
		$c = new Criteria();
		$c->add(JEleveClassePeer::PERIODE,$periode);
		$jec = $this->getJEleveClasses($c);
        return $jec[0]->getClasse();
	}

	/**
	 *
	 * Renvoi sous forme d'un tableau la liste des groupes d'un eleve pour une période donnée.
	 * Manually added for N:M relationship
	 *
	 * @periode integer numero de la periode
	 * @return     array Groupes[]
	 *
	 */
	public function getGroupes($periode) {
		$groupes = array();
		$c = new Criteria();
		$c->add(JEleveGroupePeer::PERIODE,$periode);
		foreach($this->getJEleveGroupesJoinGroupe($c) as $ref) {
			$groupes[] = $ref->getGroupe();
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
	 * @return     array Groupes[]
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
        return !empty($v) > 0 ? $v[0] : null;
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
        return !empty($v) ? $v[0] : null;
	}

	public function getCreditEctsGlobal() {
        $v = $this->getCreditEctsGlobals();
        return !empty($v) > 0 ? $v[0] : null;
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
} // Eleve
