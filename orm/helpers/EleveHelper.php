<?php

/**
 * Classe de méthodes statiques qui permettent d'eviter de repeter du code en liaison avec l'affichage des listes d'eleves.
 *
 * @author jjocal
 */
class EleveHelper {

  /**
   * Affiche une liste d'élèves en menu déroulant (balises select et option en xhtml)
   *
   * @param   array $options Pour parametrer l'affichage : @todo à terminer
   * @param   array $liste_leves tableau d'objet Eleve
   * @return  string Liste déroulante Xhtml
   */
  public static function afficheHtmlSelectListeEleves($options, $liste_eleves){

    $aff_classe         = isset($options["classe"]) ? $options["classe"] : 'fin';
    $_id                = isset($options["id"]) ? ' id="' . $options["id"] . '"' : 'listeIdEleves';
    $aff_label          = isset($options["label"]) ? '<label for="' . $_id . '">'.$options["label"].'</label>' : '';
    $_url               = isset($options["url"]) ? $options["url"] : NULL;
    $method_event       = isset($options["method_event"]) ? $options["method_event"]."('aff_result', '".$_id."', '".$_url."')" : '';
    $aff_event          = isset($options["event"]) ? ' on'.$options["event"].'="'.$method_event.'"' : '';
    $aff_multiple       = (isset($options["multiple"]) AND $options["multiple"] == 'on') ? ' multiple="multiple"' : NULL;
    $aff_multiple_name  = (isset($options["multiple"]) AND $options["multiple"] == 'on') ? '[]' : NULL;
    $aff_size           = isset($options["size"]) ? ' size="'.$options["size"].'"' : NULL;
    $_selected          = isset($options["selected"]) ? $options["selected"] : NULL;


    $retour =
    $aff_label . '
    <select name="choix_eleve'.$aff_multiple_name.'" id="' . $_id . '"' . $aff_event . $aff_multiple . $aff_size . '>
      <option value="r">-- -- -- --</option>';

    $nbre = count($liste_eleves);
    if ($nbre === 0) {
      $retour .= '
      <option value="r">Pas d\'élève dans la base</option>';
    }else{

      for($a = 0 ; $a < $nbre ; $a++){
        //echo$a . ($liste_eleves[$a]->getClasse()->getNomComplet());exit();
        if (!is_a($liste_eleves[0], 'Eleve')){
          $classe_fin     = ($aff_classe == 'fin') ? '  '.$liste_eleves[$a]->getClasse()->getNomComplet() : '';
          $classe_debut   = ($aff_classe == 'debut') ? $liste_eleves[$a]->getClasse()->getNomComplet().'&nbsp;&nbsp;' : '';
          $_id_eleve      = $liste_eleves[$a]->getEleve()->getIdEleve();
          $_nom_eleve     = $liste_eleves[$a]->getEleve()->getNom();
          $_prenom_eleve  = $liste_eleves[$a]->getEleve()->getPrenom();

        }else{
          $classe_debut = $classe_fin = NULL;
          $_id_eleve      = $liste_eleves[$a]->getIdEleve();
          $_nom_eleve     = $liste_eleves[$a]->getNom();
          $_prenom_eleve  = $liste_eleves[$a]->getPrenom();
        }

        // On gère également le selected
        $aff_selected = ($_id_eleve == $_selected) ? ' selected="selected"' : NULL;

        $retour .= '
        <option value="' . $_id_eleve . '"'.$aff_selected.'>' . $classe_debut . $_nom_eleve . ' ' . $_prenom_eleve . $classe_fin . '</option>
        ';
      }

    }

    $retour .= '
    </select>';

    return $retour;
  }
}
?>
