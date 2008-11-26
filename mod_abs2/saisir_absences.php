<?php
/**
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau, Julien Jocal
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

$utiliser_pdo = 'on';
//error_reporting(0);
// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// ============== traitement des variables ==================
$_SESSION["type_aff_abs"] = isset($_SESSION["type_aff_abs"]) ? $_SESSION["type_aff_abs"] : 'alpha';
$_SESSION["type_aff_abs"] = (isset($_GET["type_aff_abs"]) AND ($_GET["type_aff_abs"] == 'alpha' OR $_GET["type_aff_abs"] == 'classe'))
                            ? $_GET["type_aff_abs"] : $_SESSION["type_aff_abs"];


// ============== Code métier ===============================
include("absences.class.php");
include("helpers/aff_listes_utilisateurs.inc.php");
include("lib/erreurs.php");

try{

  // le tableau des élèves en vue de son affichage sous différentes formes
  $options = array('classes'=>'toutes', 'eleves'=>$_SESSION["type_aff_abs"]);
  $liste_eleves = ListeEleves($options);
  $reglages = array('classe'=>"debut", 'label'=>'Elève (nom)', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php');


}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$titre_page = "Saisir les absences";
require_once("../lib/header.inc");
$menu = 'saisir';
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************

?>
<p><a href="saisir_absences.php?type_aff_abs=alpha">Afficher tous les &eacute;l&egrave;ves</a> -
<a href="saisir_absences.php?type_aff_abs=classe">Afficher par classe</a></p>

<div id="saisie_abs" style="border: 2px solid silver; background-color: lightblue; padding: 10px 10px 10px 10px;">
<fieldset id="espace_saisie" style="border: 1px solid grey; padding: 5px 5px 5px 5px; width: 500px;">
  <legend>Saisir un ou plusieurs &eacute;l&egrave;ves</legend>

  <form action="saisir_absences.php" method="post">
    <p>
      <?php echo affSelectEleves($liste_eleves, $reglages); ?>
    </p>
    <p style="text-align: right;">
      <?php echo affSelectClasses(array('label'=>'Classes', 'width'=>'380px', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php')); ?>
    </p>
    <p style="text-align: right;">
      <?php echo affSelectEnseignements(array('label'=>'Enseignements', 'width'=>'380px', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php')); ?>
    </p>
    <p style="text-align: right;">
      <?php echo affSelectAid(array('label'=>'Par AID', 'width'=>'380px', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php')); ?>
    </p>

    <p style="position: relative; margin-left: 1%;">
      <input type="submit" name="valider" value="&nbsp;>>&nbsp;" />
    </p>

  </form>


</fieldset>
</div>

<div id="aff_result" style="display: none;">
  <!-- Affichage des données AJAX -->
</div>


<?php require("../lib/footer.inc.php"); debug_var(); ?>