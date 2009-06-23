<?php

require 'gepi/om/BasePlugIn.php';


/**
 * Skeleton subclass for representing a row from the 'plugins' table.
 *
 * Liste des plugins installes sur ce Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class PlugIn extends BasePlugIn {

	/**
	 * Initializes internal state of PlugIn object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

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
