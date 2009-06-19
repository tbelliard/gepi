<?php
/**
 * @version : $Id$
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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


// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite = "y";
$use_observeur = "";
//$niveau_arbo = "2";

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
  header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
  die();
} else if ($resultat_session == '0') {
  header("Location: ./logout.php?auto=1");
  die();
}

// Sécurité
if (!checkAccess()) {
    //header("Location: ./logout.php?auto=2");
    //die();
}
function aff_debug($tableau){
  echo '<pre>';
  print_r($tableau);
  echo '</pre>';
}


/**
 * Header
 */
$use_observeur = "ok";
$titre_page = "Cr&eacute;er des s&eacute;quences pour le cahier de textes";
$javascript_specifique = "cahier_texte_2/js/fonctionscdt2";
include '../lib/header.inc';
?>
<form action="#" method="post">
  <p>
    <label for="idSeq">Créer une séquence pour le cahier de textes (précisez le nombre de séances)</label>
    <select id="idSeq" name="nbre_sequences" onchange="">
      <option value="rien"> -- -- </option>
      <?php for($a = 1 ; $a < 7 ; $a++){
        echo '<option value="'.$a.'">'.$a.'</option>'."\n";
      }
      ?>
    </select>
  </p>

</form>

<div id="aff_result" style="display: none;"><!-- Affichage des données AJAX --></div>
<?php include '../lib/footer.inc.php'; ?>