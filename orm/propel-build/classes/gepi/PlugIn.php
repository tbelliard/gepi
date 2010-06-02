<?php



/**
 * Skeleton subclass for representing a row from the 'plugins' table.
 *
 * Liste des plugins installes sur ce Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
 */
class PlugIn extends BasePlugIn {

  /**
   * Ouvre le plugin aux utilisateurs autorisés
   */
  public function ouvrePlugin(){
    $this->setOuvert("y");
    $this->save();
  }

  /**
   * Ferme le plugin à tous les utilisateurs
   */
  public function fermePlugin(){
    $this->setOuvert("n");
    $this->save();
  }

} // PlugIn
