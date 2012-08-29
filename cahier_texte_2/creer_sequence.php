<?php
/**
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("../ckeditor/ckeditor.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
  header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
  die();
} else if ($resultat_session == '0') {
  header("Location: ../logout.php?auto=1");
  die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
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
$nb_reg=0;

// =================================================== Le code métier =======================================/

//debug_var();

# Un appel à enregistrer est lancé
if ($enregistrer == "Enregistrer"){
	check_token();

  // On pense à vérifier quelques trucs comme les entités html
  $sequence = new CahierTexteSequence();
  $sequence->setTitre($titresequence);
  $sequence->setDescription($descsequence);
  $sequence->save();

  // Maintenant on peut relier les compte-rendus avec cette séquence
  $nbre = count ($cr);

  for($i = 0 ; $i < $nbre ; $i++){
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
        $nb_reg++;
      }else{
        $verif = 'no';
      }

    }
  }

    // Gestion des erreurs//@TODO pas encore utilisé dans la page de retour
    if ($verif == 'no'){
      $msg = "<p>Au moins un compte-rendu n'a pas pu être enregistré !</p>";
    }
    header("Location: ../cahier_texte_2/index.php?id_groupe=" . $enseignement);die;

}
elseif($nb_reg>0) {
	$msg="Enregistrement effectué.";
}
/**
 * Header en include
 */
$use_observeur = "ok";
$titre_page = "Créer des séquences pour le cahier de textes";
include '../lib/header.inc.php';
//debug_var();

$nb_max_seq=getSettingValue('cdt2_sequence_nb_max_notice');
if(($nb_max_seq=="")||(!preg_match("/^[0-9]*$/", $nb_max_seq))) {
	$nb_max_seq=6;
}
?>
<p><a href="index.php<?php

	if(isset($enseignement)) {
		echo "?id_groupe=$enseignement";
	}

?>"><img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a></p>
<form action="#" method="post">
  <p>
    <label for="idSeq">Cr&eacute;er une s&eacute;quence pour le cahier de textes (<i>pr&eacute;cisez le nombre de s&eacute;ances</i>)</label>
    <select id="idSeq" name="nbre_sequences" onchange="submit();">
      <option value="rien"> -- -- </option>
      <?php for($a = 1 ; $a <= $nb_max_seq ; $a++){
        echo '<option value="'.$a.'">'.$a.'</option>'."\n";
      }
      ?>
    </select>
  </p>
<?php 
if (isset($_POST['nbre_sequences'])) {
echo add_token_field();
  // On affiche un select des enseignements de ce professeur
    $aff_select_groups = '<select id="idGroupe" name="enseignement">'."\n";
  foreach ($utilisateur->getGroupes() as $group) {
    $aff_select_groups .= '<option value="'.$group->getId().'">'.$group->getDescriptionAvecClasses().'</option>'."\n";
  }$aff_select_groups .= '</select>';
  echo '
<div id="listeSequences" style="border:2px solid gray; background-color: #33CC66; padding: 5px 5px 5px 5px;">
  <form method="post" action="creer_sequence.php">
    <p>
      <label for="idSeq">Titre de la s&eacute;quence</label>
      <input type="text" id="idSeq" name="titresequence" value="" />
      <label for="idGroupe">Enseignement concern&eacute;</label>
      '.$aff_select_groups.'
    </p>';
$ts = date("U");
for ($a=0;$a<$_POST['nbre_sequences'];$a++) {
echo '
<div style="border:2px solid gray;padding: 5px 5px 5px 5px;background-color:'.$color_fond_notices["c"].';">
  <p>
    <label for="idCR'.$a.'" style="font-weight: bold;color: red;">Compte-rendu '.($a+1).'</label> -
    <label for="idDate'.$a.'">Date</label>
    <input type="text" id="idDate'.$a.'" name="dateseance['.$a.']" value="'.date("d/m/Y", $ts).'" />
  </p>

  <p>';

    $oCKeditor = new CKeditor('../ckeditor/');
    $oCKeditor->editor('cr['.$a.']','');
  echo '</p>
</div>
<br />';
$ts = $ts + 86400;
}
 echo' <p>
    <input type="submit" name="enregistrer" value="Enregistrer" />
  </p>';
}
?>
</form></div>