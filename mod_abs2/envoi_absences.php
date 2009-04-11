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
// ============== traitement des variables ==================
$action     = isset($_POST['action']) ? $_POST['action'] : NULL;
$_tri       = isset($_POST["tri"]) ? $_POST["tri"] : '0';
$memoriser  = isset($_POST["memoriser"]) ? $_POST["memoriser"] : NULL;
$aff_envois = NULL;

// ============== Code métier ===============================
include("classes/odtphp_0_3/odf.php");
include("lib/erreurs.php");
include("helpers/aff_listes_utilisateurs.inc.php");
include '../orm/helpers/AbsencesParametresHelper.php';


try{

  // Mémorisation de l'affichage ?
  if ($memoriser == 'ok'){
    saveSetting('a2_aff_envois', $_tri);
  }
  $_memoriser = (getSettingValue('a2_aff_envois') != '' AND getSettingValue('a2_aff_envois') != '0') ? getSettingValue('a2_aff_envois') : $_tri;

  /**
   * Début de l'affichage des envois
   */
  $criteria = new Criteria();
  $criteria->add(AbsenceTraitementPeer::A_ACTION_ID, "0", Criteria::NOT_EQUAL);
  if ($_memoriser != '0'){
    $criteria->add(AbsenceTraitementPeer::A_ACTION_ID, $_memoriser, Criteria::EQUAL);
  }
  $liste_envois = AbsenceTraitementPeer::doSelect($criteria);
  $increment = 0;
  foreach ($liste_envois as $envoi){
    $couleur_ligne = (is_integer($increment/2)) ? 'lig1' : 'lig2';
    $_id_traitement = $envoi->getId();

    // On affiche l'action demandée par le selected
    $options_action = array('id'=>'action'.$_id_traitement, 'name'=>'action['.$increment.']', 'selected'=>$envoi->getAActionId(), 'class'=>$couleur_ligne);
    $aff_action     = AbsencesParametresHelper::AfficherListeDeroulanteActions($options_action);

    // On récupère l'objet eleve
    $saisies = $envoi->getJTraitementSaisies();
    $eleve = $saisies[0]->getAbsenceSaisie()->getEleve();

    $aff_envois .= '
      <tr class="'.$couleur_ligne.'">
        <td>'.$eleve->getNom().' '.$eleve->getPrenom().'</td>
        <td>'.$aff_action.'</td>
        <td><form method="post" action="envoi_absences.php"><input type="hidden" name="traitement" value="'.$_id_traitement.'" /><input type="submit" name="valider" value="Envoyer" /></form></td>
        <td></td>
      </tr>';

    $increment++;

  }

  $test_courrier_html = '
  <div class="a_adresse">
    <p>{NOM_RESP} {PRENOM_RESP}</p>
    <p>{ADR1_RESP}<br />{ADR2_RESP}<br />{CP_RESP} {COMMUNE_RESP}</p>
  </div>

  <div class="a_entete">

  </div>

  <div class="a_corps">

  </div>

  <div class="a_pied">
    
  </div>
  ';


}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$titre_page       = "Les absences";
$style_specifique = "mod_abs2/lib/abs_style";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************

?>
<form method="post" action="envoi_absences.php">
  <p><label for="idTri">Faire un tri par : </label>
  <?php echo AbsencesParametresHelper::AfficherListeDeroulanteActions(array('id'=>'idTri', 'name'=>'tri', 'selected'=>$_memoriser)); ?>
  <label for="idMem" title="Pour bloquer l'affichage sur cette action. Si vous voulez d&eacute;bloquer, il faudra m&eacute;moriser une autre action.">Mémoriser ?</label><input id="idMem" type="checkbox" name="memoriser" value="ok" />
  <input type="submit" name="valider" value="Trier" /></p>
</form>

<table id="table_liste_absents">
  <tr>
    <th></th>
    <th></th>
    <th></th>
  </tr>
  <?php echo $aff_envois; ?>
</table>



<?php require_once("../lib/footer.inc.php"); ?>