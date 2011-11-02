<?php



/**
 * Skeleton subclass for performing query and update operations on the 'eleves' table.
 *
 * Liste des eleves de l'etablissement
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class ElevePeer extends BaseElevePeer {

	/**
	 * Récupère un élève à partir de son login.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     Eleve
	 */
	public static function retrieveByLOGIN($login, PropelPDO $con = null)
	{

		if (null !== ($obj = ElevePeer::getInstanceFromPool((string) $login))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(ElevePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(ElevePeer::DATABASE_NAME);
		$criteria->add(ElevePeer::LOGIN, $login);

		$v = ElevePeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}
  /**
   * Liste de tous les eleves de l'etablissement
   *
   * @var array Tableau d'objets
   */
  private static $_liste_eleves_all_order_by_nom_prenom = NULL;

  /**
   * Appelle la liste de tous les eleves de l'etablissement
   *
   * @access private
   * @return array Tableau d'objets de tous les eleves
   */
  public static function FindAllElevesOrderByNomPrenom($options = NULL){

    if (self::$_liste_eleves_all_order_by_nom_prenom === NULL){

      $critere = new Criteria();

      // On ajoute deux clauses d'ordre
      $critere->addAscendingOrderByColumn(ElevePeer::NOM);
      $critere->addAscendingOrderByColumn(ElevePeer::PRENOM);
      // et on demande à ElevePeer de renvoyer ce dont on a besoin
      self::$_liste_eleves_all_order_by_nom_prenom = ElevePeer::doSelect($critere);
    }

    return self::$_liste_eleves_all_order_by_nom_prenom;
  }

  /**
   * Appelle la liste de tous les eleves de l'etablissement sous la forme d'un objet étendu (classe, responsable, ...)
   *
   * @access public
   * @return array Tableau d'objets de tous les eleves étendus
   */
  public static function FindAllElevesAvecCLasse($periode = 1){

    $c = new Criteria();
    $c->add(JEleveClassePeer::PERIODE, $periode, Criteria::EQUAL);
    $e_avec_classe = JEleveClassePeer::doSelectJoinEleve($c);

    return $e_avec_classe;
  }

} // ElevePeer
