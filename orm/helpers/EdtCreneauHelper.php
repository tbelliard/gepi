<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreneauHelper
 *
 * @author joss
 */
class EdtCreneauHelper {

 /**
   * Affiche l'heure d'un creneau sous la forme hh:mm
   *
   * @param creneau $creneau
   * @return array heure francaise avec les cles "debut" et "fin", Rencoi null si aucun creneau trouvé
   */
	public static function AfficherHeureFrancaiseCreneau($creneau){
		$heure_deb_ts = $creneau->getDebutCreneau();
		$heure_fin_ts = $creneau->getFinCreneau();
		$retour = array();
		$retour["debut"]  = date("H:i", $heure_deb_ts);
		$retour["fin"]    = date("H:i", $heure_fin_ts);
		return $retour;
	}


  /**
   * Méthode qui permet de vérifier si les horaires saisis par l'utilisateur sont corrects ou pas
   *
   * @param string $info
   * @return boolean false/true
   */
	public static function isHoraire($info){
		$test = explode(":", $info);
		if (count($test) == 2){
			// C'est bon, on continue les tests
			if (is_numeric($test[0]) AND is_numeric($test[1])){
				// C'est encore bon, on termine les tests
				if ($test[0] < 25 AND $test[1] < 61){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

  public static function afficherPetitTableauDesCreneaux($options = NULL){
    $_color = isset($options["couleur"]) ? $options["couleur"] : 'red';
    $_background = isset($options["background"]) ? $options["background"] : 'silver';

    $criteria = new Criteria();
    $tab_creneaux = EdtCreneauPeer::getAllCreneauxOrderByTime();

    $aff_creneaux = '<table><tr>';
    foreach ($tab_creneaux as $creneaux){
      // S'il s'agit d'un créneau de cours, on l'affiche
      if ($creneaux->getTypeCreneau() == 'cours'){
        $aff_creneaux .= '
                    <td style="color: '.$_color.'; font-weight: bold; background-color: '.$_background.';">' . $creneaux->getNomCreneau() . '</td>';
      }
    }
    $aff_creneaux .= '</tr></table>';
    return $aff_creneaux;
  }

}
?>