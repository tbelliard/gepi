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


// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

// ========================== VARIABLES ================================ //
$fusionner    = isset ($_POST["fusionner"]) ? $_POST["fusionner"] : NULL;
$_fusion      = isset ($_POST["fusion"]) ? $_POST["fusion"] : NULL;
$_fusionHier  = isset ($_POST["fusionHier"]) ? $_POST["fusionHier"] : NULL;
$_ordre       = isset ($_POST["ordre"]) ? $_POST["ordre"] : NULL;
$aff_fusion   = NULL;
$retour_aff   = NULL;

// ========================== Code métier ============================== //
// ============== Code métier ===============================
include("lib/erreurs.php");


try{

  // Une demande de fusion est lancée
  if ($fusionner == 'ok' AND $_fusion !== NULL){
    $aff_fusion = 'Une demande de fusion est lancée.';

    $saisies = AbsenceSaisiePeer::retrieveByPKs($_fusion);

  }



  $test = $saisies[0]->getEleveId(); // On prend le premier élève et on vérifiera les autres dans la boucle foreach
  $_idTraitement = NULL; // marqueur sur l'id du traitement
  if ($_fusionHier[0] == 'ok'){
    // On fusionne tout le groupe avec le traitement existant le plus récent
    $c = new Criteria();
    $c->add(AbsenceSaisiePeer::ELEVE_ID, $test, Criteria::EQUAL);
    $c->addDescendingOrderByColumn(AbsenceSaisiePeer::FIN_ABS);
    $traite_test = JTraitementSaisiePeer::doSelectJoinAbsenceSaisie($c);

    foreach($traite_test as $verif){
      if ((isset($verif)) AND is_object($verif)){
        $_id_test = $verif->getATraitementId();
        $verif2 = AbsenceTraitementPeer::retrieveByPK($_id_test);
        if (isset($verif2) AND is_object($verif2)){
          $_idTraitement = $verif2->getId();
        }
      }
    }
  }
  if ($_idTraitement === NULL){
    // sinon on crée un nouveau traitement
    $traite_test = new AbsenceTraitement();
    $traite_test->setUtilisateurId($_SESSION["login"]);
    $traite_test->setCreatedOn(date("U"));
    $traite_test->save();
    $_idTraitement = $traite_test->getId();
  }

  foreach($saisies as $saisie){

    if ($saisie->getEleveId() == $test){

      $join = new JTraitementSaisie();
      $join->setATraitementId($_idTraitement);
      $join->setASaisieId($saisie->getId());

      $retour_aff .= 'La saisie de ' . $saisie->getEleve()->getNom() . ' du ' . date("d/m/Y H:i", $saisie->getDebutAbs()) . ' &agrave; ' . date("d/m/Y H:i", $saisie->getFinAbs()) . ' est fusionn&eacute;e<br />';

    }else{

      $retour_aff .= 'Cette saisie ' . $saisie->getId(). ' ne peut &ecirc;tre fusionn&eacute;e car ce n\'est pas le m&ecirc;me él&egrave;ve <br />';

    }

    if ($join->save()){
      $_SESSION["msg_fusions"] = $retour_aff;
      header("Location:suivi_absences.php?ordre=".$_ordre);
    }else{

      //**************** EN-TETE *********************
      $javascript_specifique = "mod_abs2/lib/absences_ajax";
      $style_specifique = "mod_abs2/lib/abs_style";
      $utilisation_win = 'oui';
      $titre_page = "Fusionner des absences";
      require_once("../lib/header.inc");
      require("lib/abs_menu.php");
      //**************** FIN EN-TETE *****************
      echo '<p>IMPOSSIBLE de fusionner les saisies</p>' . $retour_aff;
      require_once("../lib/footer.inc.php");

    }
  }
}catch(exception $e){
  affExceptions($e);
}
?>