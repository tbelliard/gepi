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
$traite_anti_inject = 'no';

// Initialisations files et inclusion des librairies utiles
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
include_once('../mod_ooo/lib/tinyButStrong.class.php');
include("../fckeditor/fckeditor.php");

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
    header("Location: ./logout.php?auto=2");
    die();
}
function aff_debug($tableau){
  echo '<pre>';
  print_r($tableau);
  echo '</pre>';
}

// ==================================================== Les variables =======================================/
$enregistrer        = isset($_POST["enregistrer"]) ? $_POST["enregistrer"] : NULL;
$titresequence      = isset($_POST["titresequence"]) ? $_POST["titresequence"] : NULL;
$descsequence       = isset($_POST["descsequence"]) ? $_POST["descsequence"] : NULL;
$enseignement       = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
$dateseance         = isset($_POST["dateseance"]) ? $_POST["dateseance"] : NULL;
$cr                 = isset($_POST["cr"]) ? $_POST["cr"] : NULL;

$msg = NULL;
$verif = 'ok'; // indicateur pour le suivi des erreurs

// =================================================== Le code métier =======================================/

# Un appel à enregistrer est lancé
if ($enregistrer == "Enregistrer"){
  // On pense à vérifier quelques trucs comme les entités html
  $sequence = new CahierTexteSequence();
  $sequence->setTitre(htmlentities($titresequence));
  $sequence->setDescription($descsequence);
  $sequence->save();

  // Maintenant on peut relier les compte-rendus avec cette séquence
  $nbre = count ($cr);

  for($i = 1 ; $i <= $nbre ; $i++){
    $contenu = $seance = NULL;
    // On vérifie si le CR est bien renseigné ainsi que sa date
    if ($dateseance[$i] == ''){
      // On ne fait rien, on n'enregistre pas un CR sans date
    }else{
      $contenu = ($cr[$i] == '') ? '...' : $cr[$i]; // pour rester cohérent avec le module cdt
      // On formate la date demandée
      $test_date = explode("/", $dateseance[$i]);
      $ts_seance = mktime(0, 0, 0, $test_date[1], $test_date[0], $test_date[2]);
      // on enregistre alors ce CR
      $seance = new CahierTexteCompteRendu();
      $seance->setIdSequence($sequence->getId());
      $seance->setHeureEntry(date("H:i:s", date("U")));
      $seance->setIdGroupe($enseignement);
      $seance->setDateCt($ts_seance);
      $seance->setIdLogin($_SESSION["login"]);
      $seance->setContenu($contenu);

      if ($seance->save()){
        //$verif = 'yes';
      }else{
        $verif = 'no';
      }

    }
  }

}

// Gestion des erreurs
if ($verif == 'no'){
  $msg = "<p>Au moins un compte-rendu n'a pas pu &ecirc;tre enregistr&eacute; !</p>";
}

/**
 * Header en include
 */
$use_observeur = "ok";
$titre_page = "Cr&eacute;er des s&eacute;quences pour le cahier de textes";
$javascript_specifique = "cahier_texte_2/js/fonctionscdt2";
include '../lib/header.inc';
//debug_var();
?>
<p><a href="index.php"><img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a></p>
<form action="#" method="post">
  <p>
    <label for="idSeq">Cr&eacute;er une s&eacute;quence pour le cahier de textes (pr&eacute;cisez le nombre de s&eacute;ances)</label>
    <select id="idSeq" name="nbre_sequences">
      <option value="rien"> -- -- </option>
      <?php for($a = 1 ; $a < 7 ; $a++){
        echo '<option value="'.$a.'">'.$a.'</option>'."\n";
      }
      ?>
    </select>
  </p>

</form>
  <?php echo $msg; ?>
<div id="aff_result" style="display: none;"><!-- Affichage des données AJAX --></div>
<?php include '../lib/footer.inc.php'; ?>