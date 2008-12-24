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
include("../lib/initialisations.inc.php");


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

# Enregistrement de nouvelles absences
$action               = isset($_POST['action']) ? $_POST['action'] : NULL;
$_eleve               = isset ($_POST['_eleve']) ? $_POST['_eleve'] : NULL;
$_jourentier          = isset($_POST["_jourentier"]) ? $_POST["_jourentier"] : NULL;
$_deb                 = isset ($_POST['_deb']) ? $_POST['_deb'] : NULL;
$_fin                 = isset ($_POST['_fin']) ? $_POST['_fin'] : NULL;
$_justifications      = isset ($_POST['_justifications']) ? $_POST['_justifications'] : NULL;
$_motifs              = isset ($_POST['_motifs']) ? $_POST['_motifs'] : NULL;
$nombre              = isset ($_POST['nombre']) ? $_POST['nombre'] : NULL;
$enregistrer_absences = isset ($_POST['enregistrer_absences']) ? $_POST['enregistrer_absences'] : NULL;



// ============== Code métier ===============================
include("absences.class.php");
include("helpers/aff_listes_utilisateurs.inc.php");
include("lib/erreurs.php");
require_once("activeRecordGepi.class.php");
require_once("classes/abs_informations.class.php");

try{

  // Un edemande d'enregistrement des absences est lancée
  if ($action == '' AND $enregistrer_absences == 'ok'){
    $test = new Abs_information();

    $test->setChamp("utilisateurs_id", $_SESSION["login"]);
    $test->setChamp("eleve_id", "eleve_test");
    $test->setChamp("date_saisie", date("U"));
    $test->setChamp("debut_abs", date("U"));
    $test->setChamp("fin_abs", date("U"));

    $test->save();
  }


  // le tableau des élèves en vue de son affichage sous différentes formes
  $options = array('classes'=>'toutes', 'eleves'=>$_SESSION["type_aff_abs"]);
  $liste_eleves = ListeEleves($options);
  $reglages = array('classe'=>"debut", 'label'=>'Elève (nom)', 'event'=>'change', 'method_event'=>'gestionaffAbs', 'url'=>'saisir_ajax.php', 'multiple'=>'on', 'size'=>'10');


}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
//**************** EN-TETE *****************
$javascript_specifique = "mod_abs2/lib/absences_ajax";
$style_specifique = "mod_abs2/lib/abs_style";
$titre_page = "Saisir les absences";
require_once("../lib/header.inc");
$menu = 'saisir';
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************
debug_var();
?>
<p><a href="saisir_absences.php?type_aff_abs=alpha">Afficher tous les &eacute;l&egrave;ves</a> -
<a href="saisir_absences.php?type_aff_abs=classe">Afficher par classe</a></p>

<div id="saisie_abs" style="border: 2px solid silver; background-color: lightblue; padding: 10px 10px 10px 10px;">


  <form action="saisir_absences.php" method="post">

<fieldset id="espace_saisie2" style="position: absolute; border: 1px solid grey; padding: 5px 5px 5px 5px; width: 500px; margin-left: 520px;">
  <legend> - Afficher une liste pr&eacute;cise - </legend>
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

</fieldset>

<fieldset id="espace_saisie" style="border: 1px solid grey; padding: 5px 5px 5px 5px; width: 500px;">
  <legend> - Saisir un ou plusieurs &eacute;l&egrave;ves - </legend>

    <p>
      <?php echo affSelectEleves($liste_eleves, $reglages); ?>
    </p>
</fieldset>


  </form>

</div>

<div id="aff_result" style="display: none;">
  <!-- Affichage des données AJAX -->
</div>


<?php require("../lib/footer.inc.php"); //debug_var(); ?>