<?php



/**
 * Skeleton subclass for performing query and update operations on the 'horaires_etablissement' table.
 *
 * Table contenant les heures d'ouverture et de fermeture de l'etablissement par journee
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class EdtHorairesEtablissementPeer extends BaseEdtHorairesEtablissementPeer {

  private static $_all_horaires;

  private static $_all_horaires_array_copy;

  /**
   * Renvoie la liste des creneaux de la journee
   *
   * @return PropelObjectCollection EdtCreneau
   */
    public static function retrieveAllEdtHorairesEtablissement(){
	    if (self::$_all_horaires == null) {
		self::$_all_horaires = EdtHorairesEtablissementQuery::create()->find();
	    }
	    return clone self::$_all_horaires;
    }

  /**
   * Renvoie la liste des creneaux de la journee
   *
   * @return array EdtCreneau
   */
    public static function retrieveAllEdtHorairesEtablissementArrayCopy(){
	    if (self::$_all_horaires_array_copy == null) {
		self::$_all_horaires_array_copy = EdtHorairesEtablissementPeer::retrieveAllEdtHorairesEtablissement()->getArrayCopy('JourHoraireEtablissement');
	    }
	    return self::$_all_horaires_array_copy;
    }

} // EdtHorairesEtablissementPeer
