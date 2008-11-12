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
error_reporting(0);
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
};
//debug_var();
// ============== traitement des variables ==================
$variable_test = isset($_POST["var"]) ? $_POST["var"] : '123';

// ============== Code métier ===============================
include("absences.class.php");

include("helpers/aff_listes_utilisateurs.inc.php");
$liste_eleves = ListeEleves(array('eleves'=>'classe', 'classes'=>'toutes'));
$aff_liste_eleves = affSelectEleves($liste_eleves);

try{
  $test1 = new absences();
  $array_abs = array('eleves_id' => 12, 'groupes_id' => $variable_test);
  $test1->setChamps($array_abs);
  $array2_abs = array('debut_abs' => date("U"), 'fin_abs'=> (date("U") + 3600));
  $test1->setChamps($array2_abs);
  $test2 = $test1->getChamps();
  $test1->setAffErreur('aff');
  if ($variable_test == 124 OR $variable_test == 'josianne') {
    $test1->insertAbs();
  }
}catch(exception $e){
  $tab = $e->getMessage();
  $erreur = explode("||", $tab);
  $erreur2 = $e->getTrace();
  $file = explode("/", $erreur2[0]["file"]);
  $nbre = count($file); $aff = $nbre - 1; $aff2 = $nbre - 2;

  echo '
        <div style="width: 400px; height: 300px; border: 2px solid grey; background-color: silver;">
        <p style="color: red; font-weight: bold; text-align: center;">ERREUR</p>
        <p>Message : ' . $erreur[0] . '</p>
        <p>Requête : ' . $erreur[1] . '</p>
        <p>Classe : ' . $erreur2[0]["class"] . '&nbsp;&nbsp;&nbsp;Méthode : ' . $erreur2[0]["function"] . '</p>
        <p>Ligne : ' . $erreur2[0]["line"] . '</p>
        <p>fichier : ' . $file[$aff2] . '/' . $file[$aff] . '</p>
      </div>';
  exit();
}
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


?>
<p>Un utilisateur veut enregistrer une absence, il s'agit de <?php echo $test2["utilisateurs_id"]; ?>
&nbsp;&agrave; <?php echo date("H:i:s", $test2["date_saisie"]); ?> le <?php echo date("d/m/Y", $test2["date_saisie"]); ?></p>


<form method="post" action="aff_absences_eleves.php" >

  <p><?php echo $aff_liste_eleves; ?></p>
  <p>
    <select name="var">
      <option value="123">Aucun choix</option>
      <option value="124">Erreur dans la requ&ecirc;te</option>
      <option value="josianne">Erreur dans l'absence</option>
      <input type="submit" name="enregistrer" value="Envoyer" />
    </select>
  </p>

</form>


<pre><?php print_r($test2); ?></pre>



<?php require_once("../lib/footer.inc.php"); ?>