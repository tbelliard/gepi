<?php

require 'gepi/om/BaseEleve.php';


/**
 * Skeleton subclass for representing a row from the 'eleves' table.
 *
 * 
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
	public function getClasses($periode) {
		$classes = array();
		$criteria = new Criteria();
		$criteria->add(JEleveClassePeer::PERIODE,$periode);
		foreach($this->getJEleveClassesJoinClasse($criteria) as $ref) {
			$classes[] = $ref->getClasse();
		}
		return $classes;
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
		$criteria = new Criteria();
		$criteria->add(JEleveGroupePeer::PERIODE,$periode);
		foreach($this->getJEleveGroupesJoinGroupe($criteria) as $ref) {
			$groupes[] = $ref->getGroupe();
		}
		return $groupes;
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
                    WHERE (groupes.id = jgc.id_groupe AND jgc.id_groupe = jeg.id_groupe AND jgc.id_classe = jec.id_classe AND jgc.saisie_ects = TRUE AND jec.login = jeg.login AND jec.periode = jeg.periode AND jeg.periode = '".$periode."' AND jeg.login = '".$this->getLogin()."')";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $groupes = GroupePeer::populateObjects($stmt);
		return $groupes;
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

} // Eleve
