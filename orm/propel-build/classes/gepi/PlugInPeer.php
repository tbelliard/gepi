<?php



/**
 * Skeleton subclass for performing query and update operations on the 'plugins' table.
 *
 * Liste des plugins installes sur ce Gepi
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gepi
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
        return $retour[0];
       }

    }else{
      return false;
    }
  }

  /**
   * Méthode qui enregistre le plugin en entier dans la base (avec autorisations et droits)
   *
   * @param object $xml
   */
  public static function addPluginComplet(SimpleXMLElement $xml){
    // On considère que le xml est vérifié et bon
    $new = new PlugIn();
    $new->setNom($xml->nom);
    $new->setRepertoire($xml->nom);
    $new->setOuvert('n');
    $new->setDescription(utf8_decode($xml->description));
    $new->save();

    /**
     * @todo : il faudra améliorer ce dispositif et mettre en place quelque chose de plus dynamique pour l'insertion des droits
     */
    $liste_statuts = array('administrateur', 'professeur', 'cpe', 'scolarite', 'secours', 'eleve', 'responsable', 'autre');
    $liste_abrevia = array('A', 'P', 'C', 'S', 'sec', 'E', 'R', 'autre');

    # les autorisations
    foreach ($xml->administration->fichier->nomfichier as $fichier){

      $attributes = $fichier->attributes();
      $droits = explode ("-", $attributes["autorisation"]);

      foreach ($droits as $droit){
        if (in_array($droit, $liste_abrevia)){
          // on sait que cette abréviation est conforme mais pas quel est son rang dans le tableau
          $marqueur = 9;
          for($a = 0 ; $a < 8 ; $a++){
            if ($droit == $liste_abrevia[$a]){
              $marqueur = $a;
            }
          }

          // On peut maintenant enregistrer ce droit dans la base
          $autorisation = new PlugInAutorisation();
          $autorisation->setUserStatut($liste_statuts[$marqueur]);
          $autorisation->setAuth('V');
          $autorisation->setFichier('mod_plugins/' . $new->getNom() . '/' .$fichier);
          $autorisation->setPluginId($new->getId());
          $autorisation->save();

        }else{
          // Ce statut n'est pas autorisé dans Gepi
          return false;
        }
      }
    }

    # Les menus
    foreach ($xml->administration->menu->item as $item) {
      $attributes = $item->attributes();
      $statuts = explode("-", $attributes["autorisation"]);
      foreach ($statuts as $statut) {
        if (in_array($statut, $liste_abrevia)){
          // on sait que cette abréviation est conforme mais pas quel est son rang dans le tableau
          $marqueur = 9;
          for($a = 0 ; $a < 8 ; $a++){
            if ($statut == $liste_abrevia[$a]){
              $marqueur = $a;
            }
          }

          // On peut maintenant enregistrer cet item du menu dans la base
          $item_menu = new PlugInMiseEnOeuvreMenu();
          $item_menu->setPluginId($new->getId());
          $item_menu->setUserStatut($liste_statuts[$marqueur]);
          $item_menu->setLienItem('mod_plugins/' . $new->getNom() . '/' .$item);
          $item_menu->setDescriptionItem(utf8_decode($attributes["description"]));
          $item_menu->setTitreItem(utf8_decode($attributes["titre"]));
          $item_menu->save();

        }else{
          // Ce statut n'est pas autorisé dans Gepi
          return false;
        }
      }
    }
  }

  /**
   * Méthode qui désinstalle proprement le plugin
   *
   * @param object PlugIn $_plugin
   */
  public static function deletePluginComplet(PlugIn $_plugin){
    $_id = $_plugin->getId();
    # On détruit les droits
    $c = new Criteria();
    $c->add(PlugInAutorisationPeer::PLUGIN_ID, $_id, Criteria::EQUAL);
    $autorisation = PlugInAutorisationPeer::doDelete($c);
    # On détruit les menus
    $c = new Criteria();
    $c->add(PlugInMiseEnOeuvreMenuPeer::PLUGIN_ID, $_id, Criteria::EQUAL);
    $autorisation = PlugInMiseEnOeuvreMenuPeer::doDelete($c);
    # On détruit le plugin
    $plugin = PlugInPeer::doDelete($_id);

    return true;
  }

} // PlugInPeer
