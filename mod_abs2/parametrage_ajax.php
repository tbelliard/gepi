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
}

// ==================== VARIABLES ===============================
$type_req = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_table = $_champ = NULL;
$_id = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$_classe = $tout = NULL;
$ajouter = $modifier = $effacer = NULL;
$del = false;
$action = 'ajouter';

// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");


try{

  // On vérifie l'état de la variable $type_req
  if (substr($type_req, 0, 7) == "effacer") {
    // Alors on explose $_id en deux et on requalifie $type_req pour la suite du script
    $testons_id = explode("|||", $_id);

    if (count($testons_id) == 2 AND is_numeric($testons_id[1])) {

      $type_req = $testons_id[0];
      $del_id   = $testons_id[1];
      $action = "effacer";
      //echo "<br /> _id = " . $_id . "<br />del_id = " . $del_id . "<br />type_req = " . $type_req; exit();

    }else{

      // On ne fait rien mais une exception sera levée car $type_req ne passera pas le switch
      throw new Exception("Il manque des informations pour aller au bout de la demande : impossible de supprimer cette entr&eacute;e.||" . $testons_id[1] . "+" . $testons_id[0]);

    }
  }

  switch($type_req){
    case 'types':
    $_classe = 'AbsenceType';
    $tout = AbsenceTypePeer::doSelect(new Criteria);

      break;
    case 'motifs':
    $_classe = 'AbsenceMotif';
    $tout = AbsenceMotifPeer::doSelect(new Criteria);

      break;
    case 'actions':
    $_classe = 'AbsenceAction';
    $tout = AbsenceActionPeer::doSelect(new Criteria);

      break;
    case 'justifications':
    $_classe = 'AbsenceJustification';
    $tout = AbsenceJustificationPeer::doSelect(new Criteria);

      break;
    default:
    $_table = $_champ = $_classe = NULL;
  } // switch


  /******************* AJOUTER ******************************/
  if ($type_req != $_id AND $action == 'ajouter') {

    // On instancie la bonne classe si elle existe
    $_objet = ($_classe !== NULL) ? new $_classe : NULL;

    // On est dans le cas d'une demande d'ajout dans la base
    $_objet->setNom($_id);
    $_objet->setOrdre('0');

    if ($_objet->save()) {
      $ajouter = 'ok';
    }else{
      $ajouter = 'no';
    }

  /******************* EFFACER ******************************/
  }elseif($type_req != $_id AND $action == 'effacer'){

    switch($type_req){
      case 'types':
        $del = AbsenceTypePeer::doDelete($del_id);
        break;
      case 'motifs':
        $del = AbsenceMotifPeer::doDelete($del_id);
        break;
      case 'actions':
        $del = AbsenceActionPeer::doDelete($del_id);
        break;
      case 'justifications':
        $del = AbsenceJustificationPeer::doDelete($del_id);
        break;
      default:
        $del = FALSE;
    } // switch

    if ($del) {
      $effacer = 'ok';
    }else{
      $effacer = 'no';
    }

  }

  /******************* LISTER ******************************/
      $c = new Criteria();
      $c->addDescendingOrderByColumn('ordre');
  switch($type_req){
    case 'types':
    $tout = AbsenceTypePeer::doSelect($c);
      break;
    case 'motifs':
    $tout = AbsenceMotifPeer::doSelect($c);
      break;
    case 'actions':
    $tout = AbsenceActionPeer::doSelect($c);
      break;
    case 'justifications':
    $tout = AbsenceJustificationPeer::doSelect($c);
      break;
    default:
    $tout = NULL;
  } // switch

}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
// On précise l'entête HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:utf-8');

?>
<table id="presentations">

  <tr>
    <th><?php echo $type_req; ?></th>
    <th>Effacer</th>
  </tr>

  <?php foreach($tout as $aff): ?>
    <?php $effacer_id = 'effacer' . $aff->getId() ; ?>
    <tr>
      <td><?php echo '(' . $aff->getOrdre() . ') ' . $aff->getNom(); ?></td>
      <td>
        <input type="hidden" name="effacer" id="<?php echo $effacer_id; ?>" value="<?php echo $type_req.'|||'.$aff->getId() ; ?>" />
        <img src="../images/icons/delete.png" alt="effacer" title="Effacer" onclick="gestionaffAbs('aff_result', '<?php echo $effacer_id; ?>', 'parametrage_ajax.php');" /></td>
    </tr>

  <?php endforeach; ?>

</table>

<?php
  // Cas où l'utilisateur vient de choisir ce qu'il veut afficher : types, actions, motifs et justifications

  echo '

  <p onclick="afficherDiv(\'ajouterEntree\');" class="lienWeb">Ajouter des ' .$type_req . '</p>

<div id="ajouterEntree" style="display: none;">

  <p>Ajouter une entr&eacute;e dans la base</p>


    <p><label for="idOrder" title="Pr&eacute;cisez un nombre de 0 &agrave; ...">Ordre d\'affichage</label>
    <input type="text" id="idOrder" name="ordre" value="" /></p>
    <p><label for="' . $type_req . '">Entrez un ' . substr($type_req, 0, (strlen($type_req) - 1)) . ' :</label>
    <input onkeydown="func_KeyDown(event, \'1\', \'' . $type_req . '\');" type="text" id="' . $type_req . '" name="nom" value="" /></p>
    <p onclick="gestionaffAbs(\'aff_result\', \'' . $type_req . '\', \'parametrage_ajax.php\');" class="lienWeb">Enregistrer</p>

' . $effacer . '

</div>
  ';

if ($ajouter == 'ok') {
  // Cas où l'utilisateur veut ajouter une entrée dans la base

  echo '<p class="ok">' . $type_req . ' en plus : ' . utf8_encode($_id) . '<p>';

}elseif($ajouter == 'no'){

  echo '<p class="no">Impossible d\'enregistrer "' . utf8_encode($_id) . '"<p>';

}

?>








