<?php

require 'gepi/om/BasePlugInPeer.php';


/**
 * Skeleton subclass for performing query and update operations on the 'plugins' table.
 *
 * Liste des plugins installes sur ce Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class PlugInPeer extends BasePlugInPeer {

  public static function getPluginByNom($nom){
    if (is_string($nom)){
      $c = new Criteria();
      $c->add(PlugInPeer::NOM, $nom, Criteria::EQUAL);
      $retour = PlugInPeer::doSelect($c);

      if (empty ($retour)){
        return NULL;
      }else{
        return $retour;
      }

    }else{
      return false;
    }
  }

} // PlugInPeer
