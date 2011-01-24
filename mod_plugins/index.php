<?php
/**
 * @version : $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Julien Jocal
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Ce fichier est destiné à organiser les plugins de Gepi.
 * Il permet d'ajouter ses propres plugins.
 * Une documentation est disponible : http://projects.sylogix.org/gepi/wiki/plugin
 */

// On initialise
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files et autres librairies
include("../lib/initialisations.inc.php");
include("../lib/initialisationsPropel.inc.php");
include 'traiterXml.class.php';
include 'traiterRequetes.class.php';

// Resume session et on vérifie les droits de l'utilisateur
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
  header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
  die();
} else if ($resultat_session == '0') {
  header("Location: ../logout.php?auto=1");
  die();
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
  die();
}


// =========================================== VARIABLES =================================================== //
$plugin_id    = isset($_GET["plugin_id"]) ? $_GET["plugin_id"] : NULL;
$nom_plugin   = isset($_GET["nom_plugin"]) ? $_GET["nom_plugin"] : NULL;
$action       = isset($_GET["action"]) ? $_GET["action"] : NULL;
$_erreur      = isset($_GET["_erreur"]) ? $_GET["_erreur"] : NULL;
$_msg         = NULL;


// ========================================== CODE METIER ================================================== //
function aff_debug($tab){
  echo '<pre>';
  print_r($tab);
  echo '</pre>';
}

# On traite d'un plugin qui n'est pas installé
if (isset($nom_plugin)) {
  check_token();
  if ($action == "installer"){
    // On ouvre le répertoire et on scanne le contenu, il faut y trouver un fichier xml et un fichier index.php au minimum
    $plugin = scandir($nom_plugin);
    // On vérifie que le plugin.xml est bien présent
    $testXML = false;
    foreach ($plugin as $fichier){
      if ($fichier == "plugin.xml"){
        $testXML = true;
      }
    }

    if ($testXML){

      // On lit le fichier xml
      $xml = simplexml_load_file($nom_plugin . "/plugin.xml");
      $testXML = new traiterXml($xml);

      if ($testXML->getReponse() === true){
        // alors on peut envoyer le xml pour installer le plugin
        $new_plugin = PlugInPeer::addPluginComplet($xml);
        /**
         * On traite les requêtes demandées lors de l'installation
         */
        $traitement_requetes = new traiterRequetes($xml->installation->requetes);
        if ($traitement_requetes->getReponse() === true){
          // C'est fait les requêtes ont été exécutées
        }else{
          $_msg = '<p class="red">ERREUR(r) : ' . $traitement_requetes->getErreur() . '</p>';
        }
      }else{

        $_msg = '<p class="red">ERREUR(x) : ' . $testXML->getErreur() . '</p>';

      }

    }else{

      header("index.php?_erreur=1");
      exit();

    }

  }
# On traite des plugin déjà installés
}elseif(isset($plugin_id)) {
  check_token();
  // On s'attache à faire les actions demandées sur ce plugin déjà installé
  $pluginAmodifier = PlugInPeer::retrieveByPK($plugin_id);
  switch ($action) {
    case "desinstaller":
      $xml = simplexml_load_file($pluginAmodifier->getNom() . "/plugin.xml");
      $pluginAmodifier->delete();
      $traitement_requetes = new traiterRequetes($xml->desinstallation->requetes);
      break;
    case "ouvrir":
      $pluginAmodifier->ouvrePlugin();
      break;
    case "fermer":
      $pluginAmodifier->fermePlugin();
      break;

  default:
    $_msg = "<p>L'action demand&eacute;e n'existe pas !</p>";
    break;
  }
}

# On liste les plugins
$liste_plugins  = array();
$open_dir       = scandir("./");

foreach ($open_dir as $dir) {

  // On vérifie la présence d'un point dans le nom retourné
  $test = explode(".", $dir);
  if (count($test) <= 1){

    $test2 = PlugInPeer::getPluginByNom($dir);

    if (is_object($test2)){
      $liste_plugins[] = $test2;
    }else{
      $liste_plugins[] = $dir;
    }

  }

}

# Gestion des erreurs
switch ($_erreur) {
  case "1":
    $_msg = "<p class=\"red\">Il manque le fichier plugin.xml &agrave; ce plugin, impossible de l'installer !</p>";
    break;
  case "2":
    $_msg = "<p class=\"red\">Le fichier plugin.xml ne respecte pas la struture demand&eacute;e ! voyez <a href=\"https://www.sylogix.org/wiki/gepi/plugin\">la documentation collaborative (wiki)</a></p>";
    break;

default:
  //$_msg = NULL;
  break;
}
// ================= HEADER ========================//
$titre_page = "Param&eacute;trer les plugins";
include '../lib/header.inc';
// ================ FIN HEADER =====================//
//print_r($liste_plugins);
//aff_debug($testXML);
echo "<p class='bold'><a href='../accueil_modules.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
?>


<h3 class="Gepi">Liste des plugins install&eacute;s</h3>
<p>Pour plus d'informations concernant les plugins de Gepi, voyez
  <a onclick="window.open(this.href, '_blank'); return false;" href="https://www.sylogix.org/projects/gepi/wiki/GuideAdministrateur#Syst%C3%A8me-de-plugins">la documentation collaborative (wiki)</a>
</p>
<?php echo $_msg; ?>
 <table class="table">
  <tr>
    <th>Plugin</th>
    <th>Description</th>
    <th>Auteur</th>
    <th>Version</th>
    <th>Install&eacute; ?</th>
    <th>Ouvert ?</th>
  </tr>
<?php
foreach($liste_plugins as $plugin){
  if (!is_object($plugin)){
    // le plugin n'est pas installé
    echo '
    <tr>
      <td>'.str_replace("_", " ", $plugin).'</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td><a href="index.php?nom_plugin='.$plugin.'&amp;action=installer'.add_token_in_url().'" title="Voulez-vous l\'installer ?">NON</a></td>
      <td>NON</td>
    </tr>';
  }else{
    // Le plugin est installé
    $xml = simplexml_load_file($plugin->getNom() . "/plugin.xml");
    // On teste s'il est ouvert
    if ($plugin->getOuvert() == 'y'){
      $aff_ouvert = '<a href="index.php?plugin_id='.$plugin->getId().'&amp;action=fermer'.add_token_in_url().'" title="Voulez-vous le fermer ?">OUI</a>';
    }else{
      $aff_ouvert = '<a href="index.php?plugin_id='.$plugin->getId().'&amp;action=ouvrir'.add_token_in_url().'" title="Voulez-vous l\'ouvrir ?">NON</a>';
    }
    echo '
    <tr>
      <td>'.str_replace("_", " ", $plugin->getNom()).'</td>
      <td>'.iconv("utf-8","iso-8859-1",$xml->description).'</td>
      <td>'.iconv("utf-8","iso-8859-1",$xml->auteur).'</td>
      <td>'.iconv("utf-8","iso-8859-1",$xml->version).'</td>
      <td><a href="index.php?plugin_id='.$plugin->getId().'&amp;action=desinstaller'.add_token_in_url().'" title="Voulez-vous le d&eacute;sinstaller ?" onclick="return confirm('."'La desinstallation d\'un plugin entraîne la suppression des tables éventuellement associées et des données qu\'elles contiennent. Etes-vous sûr de vouloir désinstaller ce plugin ?'".');">OUI</a></td>
      <td>'.$aff_ouvert.'</td>
    </tr>';
  }
}
?>

</table>

<?php include '../lib/footer.inc.php'; ?>