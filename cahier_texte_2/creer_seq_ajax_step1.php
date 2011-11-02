<?php
/**
 * @version : $Id: creer_seq_ajax_step1.php 7938 2011-08-24 07:57:41Z jjocal $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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


// Initialisations files et inclusion des librairies utiles
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
include("../fckeditor/fckeditor.php");
//include_once('./lib/tinyButStrong.class.php');

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


// ==================================================== Les variables =======================================/
$select   = isset($_POST["select"]) ? $_POST["select"] : NULL;
$_id      = isset($_POST["_id"]) ? $_POST["_id"] : NULL;

// =================================================== Le code métier =======================================/

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

if ($select == "nbre_sequences"){

  // On affiche un select des enseignements de ce professeur
    $aff_select_groups = '<select id="idGroupe" name="enseignement">'."\n";
  foreach ($utilisateur->getGroupes() as $group) {
    $aff_select_groups .= '<option value="'.$group->getId().'">'.$group->getDescriptionAvecClasses().'</option>'."\n";
  }
    $aff_select_groups .= '</select>'."\n";

  echo '
<div id="listeSequences" style="border:2px solid gray; background-color: #33CC66; padding: 5px 5px 5px 5px;">
  <form method="post" action="creer_sequence.php">
    <p>
      <label for="idSeq">Titre de la s&eacute;quence</label>
      <input type="text" id="idSeq" name="titresequence" value="" />
      <label for="idGroupe">Enseignement concern&eacute;</label>
      '.$aff_select_groups.'
    <p>
    </p>
      <label for="idDescSeq">Description</label>
      <input type="text" name="descsequence" value="" style="width: 40%;" />
    </p>';
  $ts = date("U");
  for($a = 1 ; $a <= $_id ; $a++){

    echo '
<div style="border:2px solid gray;padding: 5px 5px 5px 5px;background-color:'.$color_fond_notices["c"].';">
  <p>
    <label for="idCR'.$a.'" style="font-weight: bold;color: red;">Compte-rendu '.$a.'</label> -
    <label for="idDate'.$a.'">Date</label>
    <input type="text" id="idDate'.$a.'" name="dateseance['.$a.']" value="'.date("d/m/Y", $ts).'" />
  </p>

  <p>';

    $oFCKeditor                             = new FCKeditor('cr['.$a.']');
    $oFCKeditor->BasePath                   = '../fckeditor/';
    $oFCKeditor->Config['DefaultLanguage']  = 'fr';
    $oFCKeditor->ToolbarSet                 = 'Basic';
    $oFCKeditor->Value                      = '';
    $oFCKeditor->Create();
  echo '</p>
</div>
<br />
    ';
    $ts = $ts + 86400;
  }

	echo add_token_field();

  echo '
  <p>
    <input type="submit" name="enregistrer" value="Enregistrer" />
  </p>
  </form>
</div>';
}
?>