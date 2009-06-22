<?php
/**
 * @version : $Id$
 *
 * Copyright 2001, 2009 Thomas Belliard, Julien Jocal
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


// ========================================== CODE METIER ================================================== //

# On traite d'un plugin déjà installé
if (isset($plugin_id)){

}

# On traite d'un plugin qui n'est pas installé
if (isset($nom_plugin)){
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

    echo $testXML . 'testons<br />';

  }
}

# On liste les plugins
$liste_plugins  = array();
$open_dir       = scandir("./");

foreach ($open_dir as $dir){

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

// ================= HEADER ========================//
$titre_page = "Param&eacute;trer les plugins";
include '../lib/header.inc';
// ================ FIN HEADER =====================//
//print_r($liste_plugins);
?>
<h3 class="Gepi">Liste des plugins install&eacute;s</h3>
<p>Pour plus d'informations concernant les plugins de Gepi, voyez
  <a onclick="window.open(this.href, '_blank'); return false;" href="http://projects.sylogix.org/gepi/wiki/plugin">la page sur TRAC</a>
</p>

 <table class="table">
  <tr>
    <th>Plugin</th>
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
      <td><a href="index.php?nom_plugin='.$plugin.'&amp;action=installer" title="Voulez-vous l\'installer ?">NON</a></td>
      <td>NON</td>
    </tr>';
  }else{
    // Le plugin est installé
    // On teste s'il est ouvert
    if ($plugin->getOuvert() == 'y'){
      $aff_ouvert = '<a href="index.php?plugin_id='.$plugin->getId().'&amp;action=fermer" title="Voulez-vous le fermer ?">OUI</a>';
    }else{
      $aff_ouvert = '<a href="index.php?plugin_id='.$plugin->getId().'&amp;action=ouvrir" title="Voulez-vous l\'ouvrir ?">NON</a>';
    }
    echo '
    <tr>
      <td>'.str_replace("_", " ", $plugin).'</td>
      <td><a href="index.php?plugin_id='.$plugin->getId().'&amp;action=desinstaller" title="Voulez-vous le d&eacute;sinstaller ?">OUI</a></td>
      <td>'.$aff_ouvert.'</td>
    </tr>';
  }
}
?>

</table>

<?php include '../lib/footer.inc.php'; ?>