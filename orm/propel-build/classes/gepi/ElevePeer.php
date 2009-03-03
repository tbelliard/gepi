<?php

require 'gepi/om/BaseElevePeer.php';


/**
 * Skeleton subclass for performing query and update operations on the 'eleves' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class ElevePeer extends BaseElevePeer {

  /**
   * Liste de tous les eleves de l'etablissement
   *
   * @var array Tableau d'objets
   */
  protected static $_liste_eleves_all = NULL;

  /**
   * Appelle la liste de tous les eleves de l'etablissement
   *
   * @access private
   * @return array Tableau d'objets de tous les eleves
   */
  public static function FindAllEleves($options = NULL){

    if (self::$_liste_eleves_all === NULL){

      $critere = new Criteria();

      // On ajoute deux clauses d'ordre
      $critere->addAscendingOrderByColumn(ElevePeer::NOM);
      $critere->addAscendingOrderByColumn(ElevePeer::PRENOM);
      // et on demande à ElevePeer de renvoyer ce dont on a besoin
      self::$_liste_eleves_all = ElevePeer::doSelect($critere);
    }

    return self::$_liste_eleves_all;
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
