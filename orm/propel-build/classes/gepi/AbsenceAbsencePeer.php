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
    if (is_array($new_abs)){
      // On recherche toutes les absences de cet élève sur la semaine passée

      $test_deb = $new_abs["debut_abs"] - (7*24*3600);
      $criteria = new Criteria();
      $criteria->add(AbsenceAbsencePeer::ELEVE_ID, $new_abs["eleve_id"], Criteria::EQUAL);
      $criteria->add(AbsenceAbsencePeer::DEBUT_ABS, $test_deb, Criteria::GREATER_EQUAL);
      $test = self::doSelect($criteria);

      // Pour chacune de ces absences, on teste si il y a continuité avec l'absences saisie $new_abs
      if (empty ($test)){
        // Il n'y a aucune réponse, donc la saisie est une nouvelle absence
        $retour = array('rien'=>'rien');
      }else{
        $retour = array();
        // On traite les réponses présentes dans la table
        foreach ($test as $absence){
          $retour[] = $absence->getEleveId();
        }
      }
    }

    //$retour = $test; // en debug, on renvoie la requête
    return $retour;
  }

} // AbsenceAbsencePeer
