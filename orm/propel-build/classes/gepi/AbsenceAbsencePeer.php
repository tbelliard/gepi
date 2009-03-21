<?php

require 'gepi/om/BaseAbsenceAbsencePeer.php';


/**
 * Skeleton subclass for performing query and update operations on the 'a_absences' table.
 *
 * Une absence est la compilation des saisies pour un meme eleve, cette compilation est faite automatiquement par Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    gepi
 */
class AbsenceAbsencePeer extends BaseAbsenceAbsencePeer {

  /**
   * Methode statique qui recherche si la saisie continue une absence ou pas
   *
   * @param array $new_abs Tableau des informations sur la nouvelle absence
   * @return integer Niveau de réponse (-1 s'il n'y a pas déjà une absence sinon ce sera l'id de l'absence à updater).
   */
  public static function verifAvantEnregistrement($new_abs){
    return true;
  }

} // AbsenceAbsencePeer
